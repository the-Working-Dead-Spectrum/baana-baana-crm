<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class OrderSyncService
{
    protected WordPressService $wordPressService;

    public function __construct(WordPressService $wordPressService)
    {
        $this->wordPressService = $wordPressService;
    }

    /**
     * Vérifier si la synchronisation WordPress est activée et configurée
     */
    private function isSyncEnabled(): bool
    {
        return config('services.wordpress.sync_enabled', true)
            && !empty(config('services.wordpress.consumer_key'))
            && !empty(config('services.wordpress.consumer_secret'))
            && !empty(config('services.wordpress.url'));
    }

    /**
     * Obtenir les credentials WordPress
     */
    private function getCredentials(): ?array
    {
        $consumerKey = config('services.wordpress.consumer_key');
        $consumerSecret = config('services.wordpress.consumer_secret');
        $baseUrl = config('services.wordpress.url');

        if (!$consumerKey || !$consumerSecret || !$baseUrl) {
            Log::warning('WordPress credentials not properly configured', [
                'has_consumer_key' => !empty($consumerKey),
                'has_consumer_secret' => !empty($consumerSecret),
                'has_url' => !empty($baseUrl),
            ]);
            return null;
        }

        return [
            'consumer_key' => $consumerKey,
            'consumer_secret' => $consumerSecret,
            'url' => $baseUrl,
        ];
    }

    /**
     * Créer un client HTTP configuré pour WordPress
     */
    private function createHttpClient(): \Illuminate\Http\Client\PendingRequest
    {
        $credentials = $this->getCredentials();

        if (!$credentials) {
            throw new Exception('WordPress credentials not available');
        }

        $client = Http::withBasicAuth(
            $credentials['consumer_key'],
            $credentials['consumer_secret']
        )
            ->timeout(config('services.wordpress.timeout', 30))
            ->retry(
                config('services.wordpress.retry_attempts', 3),
                config('services.wordpress.retry_delay', 1000)
            );

        // Désactiver la vérification SSL en environnement local
        $verifySSL = config('services.wordpress.verify_ssl');

        if ($verifySSL === false || $verifySSL === 'false' || app()->environment('local')) {
            $client = $client->withOptions(['verify' => false]);
            Log::debug('SSL verification disabled for WordPress sync', [
                'verify_ssl_config' => $verifySSL,
                'environment' => app()->environment()
            ]);
        }

        return $client;
    }

    /**
     * Synchroniser une mise à jour de commande vers WordPress
     * 
     * @param Order $order
     * @param string $action Type de mise à jour (status_change, completion, etc.)
     * @return bool
     */
    public function syncOrderUpdateToWordPress(Order $order, string $syncType): bool
    {
        // Rediriger vers la version async pour éviter les timeouts
        return $this->syncOrderUpdateToWordPressAsync($order, $syncType);
    }

    /**
     * Préparer les données de commande pour WordPress
     * 
     * @param Order $order
     * @param string $action
     * @return array
     */
    protected function prepareOrderData(Order $order, string $action): array
    {
        $data = [];

        // Mapping des statuts Laravel → WooCommerce
        $statusMapping = [
            'pending' => 'pending',
            'processing' => 'processing',
            'in-production' => 'processing', // Statut personnalisé → processing
            'shipped' => 'completed', // Ou votre statut personnalisé 'wc-shipped'
            'completed' => 'completed',
            'cancelled' => 'cancelled',
            'refunded' => 'refunded',
            'failed' => 'failed',
        ];

        // Statut de la commande
        if (isset($statusMapping[$order->status])) {
            $data['status'] = $statusMapping[$order->status];
        }

        // Informations client (si modifiées)
        if ($action === 'update' || $action === 'customer_update') {
            $data['billing'] = [
                'first_name' => $order->customer_name ?? '',
                'email' => $order->customer_email ?? '',
                'phone' => $order->customer_phone ?? '',
            ];
        }

        // Notes de commande
        if ($order->notes) {
            $data['customer_note'] = $order->notes;
        }

        // Métadonnées personnalisées
        $metaData = [
            [
                'key' => '_crm_last_sync',
                'value' => now()->toIso8601String()
            ],
            [
                'key' => '_crm_order_id',
                'value' => $order->id
            ]
        ];

        // Ajouter des métadonnées spécifiques pour certaines actions
        if ($action === 'completion') {
            $metaData[] = [
                'key' => '_crm_completed_at',
                'value' => now()->toDateTimeString()
            ];
            $metaData[] = [
                'key' => '_crm_completed_by',
                'value' => 'creator_dashboard'
            ];
        }

        $data['meta_data'] = $metaData;

        return $data;
    }

    /**
     * Mettre à jour le statut d'une commande WordPress
     * 
     * @param int $wpOrderId
     * @param string $status
     * @return bool
     */
    public function updateOrderStatus(int $wpOrderId, string $status): bool
    {
        if (!$this->isSyncEnabled()) {
            Log::info('WordPress sync disabled, skipping status update');
            return true;
        }

        $credentials = $this->getCredentials();
        if (!$credentials) {
            return false;
        }

        try {
            $url = rtrim($credentials['url'], '/') . '/wp-json/wc/v3/orders/' . $wpOrderId;

            $httpClient = $this->createHttpClient();
            $response = $httpClient->put($url, [
                'status' => $status
            ]);

            if ($response->successful()) {
                Log::info("✅ WordPress order status updated", [
                    'wp_order_id' => $wpOrderId,
                    'new_status' => $status,
                ]);
                return true;
            }

            Log::error("❌ Failed to update WordPress order status", [
                'wp_order_id' => $wpOrderId,
                'status' => $status,
                'response' => $response->body()
            ]);

            return false;
        } catch (Exception $e) {
            Log::error("❌ Failed to update WordPress order status", [
                'wp_order_id' => $wpOrderId,
                'status' => $status,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Synchroniser avec WordPress de manière non-bloquante
     * 
     * @param Order $order
     * @param string $syncType
     * @return bool
     */
    public function syncOrderUpdateToWordPressAsync(Order $order, string $syncType): bool
    {
        if (!$order->wp_order_id) {
            Log::warning('Order has no wp_order_id', ['order_id' => $order->id]);
            return false;
        }

        try {
            // Préparer les données
            $data = $this->prepareOrderDataForWordPress($order, $syncType);

            // ✅ Appel HTTP non-bloquant (fire-and-forget)
            $this->sendAsyncRequest(
                $this->getWordPressApiUrl($order->wp_order_id),
                $data
            );

            Log::info('📤 WordPress sync request sent (non-blocking)', [
                'order_id' => $order->id,
                'wp_order_id' => $order->wp_order_id,
                'sync_type' => $syncType,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send async WordPress sync', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Ajouter une note WordPress de manière non-bloquante
     */
    public function addOrderNoteAsync(int $wpOrderId, string $noteText, bool $isCustomerNote = false): bool
    {
        try {
            $data = [
                'note' => $noteText,
                'customer_note' => $isCustomerNote,
            ];

            $this->sendAsyncRequest(
                $this->getWordPressNotesUrl($wpOrderId),
                $data
            );

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send async note to WordPress', [
                'wp_order_id' => $wpOrderId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Envoyer une requête HTTP asynchrone (non-bloquante)
     * 
     * @param string $url
     * @param array $data
     * @return void
     */
    private function sendAsyncRequest(string $url, array $data): void
    {
        // Option 1: Utiliser Guzzle en mode asynchrone
        if (class_exists(\GuzzleHttp\Client::class)) {
            $client = new \GuzzleHttp\Client([
                'timeout' => 0.5, // Timeout très court
                'connect_timeout' => 0.5,
            ]);

            $client->postAsync($url, [
                'json' => $data,
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode(
                        config('services.wordpress.api_key') . ':' .
                            config('services.wordpress.api_secret')
                    ),
                    'Content-Type' => 'application/json',
                ],
            ])->then(
                function ($response) use ($url) {
                    Log::debug('Async request succeeded', ['url' => $url]);
                },
                function ($exception) use ($url) {
                    Log::warning('Async request failed (expected)', [
                        'url' => $url,
                        'error' => $exception->getMessage(),
                    ]);
                }
            );

            return;
        }

        // Option 2: Utiliser cURL en mode non-bloquant
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode(
                    config('services.wordpress.api_key') . ':' .
                        config('services.wordpress.api_secret')
                ),
            ],
            CURLOPT_RETURNTRANSFER => false, // ✅ Ne pas attendre la réponse
            CURLOPT_TIMEOUT_MS => 500, // Timeout 500ms
            CURLOPT_NOSIGNAL => 1, // ✅ Important pour les timeouts courts
        ]);

        // Exécuter et fermer immédiatement
        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * Préparer les données de commande pour WordPress
     */
    private function prepareOrderDataForWordPress(Order $order, string $syncType): array
    {
        return [
            'status' => $this->mapStatusToWooCommerce($order->status),
            'meta_data' => [
                [
                    'key' => '_crm_sync_type',
                    'value' => $syncType,
                ],
                [
                    'key' => '_crm_synced_at',
                    'value' => now()->toIso8601String(),
                ],
            ],
        ];
    }

    /**
     * Mapper les statuts Laravel vers WooCommerce
     */
    private function mapStatusToWooCommerce(string $status): string
    {
        $map = [
            'pending' => 'pending',
            'processing' => 'processing',
            'logistics' => 'processing', // ou créer un statut custom dans WooCommerce
            'completed' => 'completed',
            'cancelled' => 'cancelled',
            'refunded' => 'refunded',
        ];

        return $map[$status] ?? 'processing';
    }

    /**
     * Obtenir l'URL de l'API WordPress pour une commande
     */
    private function getWordPressApiUrl(int $wpOrderId): string
    {
        $baseUrl = rtrim(config('services.wordpress.url'), '/');
        return "{$baseUrl}/wp-json/wc/v3/orders/{$wpOrderId}";
    }

    /**
     * Obtenir l'URL de l'API WordPress pour les notes
     */
    private function getWordPressNotesUrl(int $wpOrderId): string
    {
        $baseUrl = rtrim(config('services.wordpress.url'), '/');
        return "{$baseUrl}/wp-json/wc/v3/orders/{$wpOrderId}/notes";
    }

    /**
     * Ajouter une note à une commande WordPress
     * 
     * @param int $wpOrderId
     * @param string $note
     * @param bool $isCustomerNote
     * @return bool
     */
    public function addOrderNote(int $wpOrderId, string $noteText, bool $isCustomerNote = false): bool
    {
        // Rediriger vers la version async pour éviter les timeouts
        return $this->addOrderNoteAsync($wpOrderId, $noteText, $isCustomerNote);
    }

    /**
     * Tester la connexion à l'API WordPress
     * 
     * @return bool
     */
    public function testConnection(): bool
    {
        if (!$this->isSyncEnabled()) {
            return false;
        }

        $credentials = $this->getCredentials();
        if (!$credentials) {
            return false;
        }

        try {
            $url = rtrim($credentials['url'], '/') . '/wp-json/wc/v3/system_status';

            $httpClient = $this->createHttpClient();
            $response = $httpClient->get($url);

            if ($response->successful()) {
                Log::info('✅ WordPress API connection test successful');
                return true;
            }

            Log::warning('⚠️ WordPress API connection test failed', [
                'status_code' => $response->status(),
            ]);

            return false;
        } catch (Exception $e) {
            Log::error('❌ WordPress API connection test failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
