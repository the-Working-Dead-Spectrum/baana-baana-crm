<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class PapsService
{
    protected string $apiUrl;
    protected ?string $token = null;
    protected ?string $tokenExpiration = null;
    protected int $timeout;

    public function __construct()
    {
        $this->apiUrl = config('services.paps.api_url');
        $this->timeout = config('services.paps.timeout', 30);
    }

    /**
     * S'authentifier auprès de l'API PAPS
     * 
     * @return bool
     * @throws Exception
     */
    public function authenticate(): bool
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->apiUrl}/auth/login", [
                    'clientId' => config('services.paps.client_id'),
                    'clientSecret' => config('services.paps.client_secret'),
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['data']['token'])) {
                    $this->token = $data['data']['token'];
                    $this->tokenExpiration = $data['data']['expiration'] ?? null;
                    
                    Log::info('PAPS Authentication successful', [
                        'expiration' => $this->tokenExpiration,
                    ]);
                    
                    return true;
                }
            }

            Log::error('PAPS Authentication failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (Exception $e) {
            Log::error('PAPS Authentication exception', [
                'message' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Obtenir le token d'authentification (avec cache si déjà authentifié)
     * 
     * @return string|null
     * @throws Exception
     */
    public function getToken(): ?string
    {
        // Si le token est expiré ou inexistant, on se ré-authentifie
        if (!$this->token || $this->isTokenExpired()) {
            $this->authenticate();
        }

        return $this->token;
    }

    /**
     * Vérifier si le token est expiré
     * 
     * @return bool
     */
    protected function isTokenExpired(): bool
    {
        if (!$this->tokenExpiration) {
            return true;
        }

        $expiration = \Carbon\Carbon::parse($this->tokenExpiration);
        
        // On considère le token comme expiré 5 minutes avant l'heure réelle
        return $expiration->subMinutes(5)->isPast();
    }

    /**
     * Créer une tâche de livraison (PICKUP)
     * 
     * @param array $data
     * @return array|null
     * @throws Exception
     */
    public function createPickupTask(array $data): ?array
    {
        try {
            $token = $this->getToken();
            
            if (!$token) {
                throw new Exception('Unable to get PAPS authentication token');
            }

            $payload = [
                'type' => 'PICKUP',
                'datePickup' => $data['date_pickup'] ?? now()->toDateString(),
                'timePickup' => $data['time_pickup'] ?? '10:00',
                'vehicleType' => $data['vehicle_type'] ?? config('services.paps.default_vehicle_type', 'SCOOTER'),
                'address' => $data['address'],
                'contactName' => $data['contact_name'] ?? null,
                'contactPhone' => $data['contact_phone'] ?? null,
                'packages' => $data['packages'] ?? [],
                'metadata' => array_merge([
                    'order_id' => $data['order_id'] ?? null,
                    'creator_id' => $data['creator_id'] ?? null,
                    'brand_slug' => $data['brand_slug'] ?? null,
                ], $data['metadata'] ?? []),
            ];

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->timeout($this->timeout)
            ->post("{$this->apiUrl}/tasks/", $payload);

            if ($response->created() || $response->successful()) {
                $result = $response->json();
                
                Log::info('PAPS Pickup task created successfully', [
                    'job_id' => $result['data']['job']['_id'] ?? null,
                    'order_id' => $data['order_id'] ?? null,
                ]);

                return $result;
            }

            Log::error('PAPS Pickup task creation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'order_id' => $data['order_id'] ?? null,
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('PAPS Pickup task creation exception', [
                'message' => $e->getMessage(),
                'order_id' => $data['order_id'] ?? null,
            ]);

            throw $e;
        }
    }

    /**
     * Récupérer les détails d'une tâche
     * 
     * @param string $taskId
     * @return array|null
     * @throws Exception
     */
    public function fetchTask(string $taskId): ?array
    {
        try {
            $token = $this->getToken();
            
            if (!$token) {
                throw new Exception('Unable to get PAPS authentication token');
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/json',
            ])
            ->timeout($this->timeout)
            ->get("{$this->apiUrl}/tasks/fetch/{$taskId}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PAPS Task fetch failed', [
                'task_id' => $taskId,
                'status' => $response->status(),
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('PAPS Task fetch exception', [
                'task_id' => $taskId,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Récupérer le statut d'une commande
     * 
     * @param string $orderId
     * @return array|null
     * @throws Exception
     */
    public function getOrderStatus(string $orderId): ?array
    {
        try {
            $token = $this->getToken();
            
            if (!$token) {
                throw new Exception('Unable to get PAPS authentication token');
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/json',
            ])
            ->timeout($this->timeout)
            ->get("{$this->apiUrl}/tasks/fetch-order/{$orderId}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PAPS Order status fetch failed', [
                'order_id' => $orderId,
                'status' => $response->status(),
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('PAPS Order status fetch exception', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Récupérer l'historique complet d'une commande
     * 
     * @param string $orderId
     * @return array|null
     * @throws Exception
     */
    public function fetchOrderHistory(string $orderId): ?array
    {
        try {
            $token = $this->getToken();
            
            if (!$token) {
                throw new Exception('Unable to get PAPS authentication token');
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/json',
            ])
            ->timeout($this->timeout)
            ->get("{$this->apiUrl}/orders/client/fetch/{$orderId}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PAPS Order history fetch failed', [
                'order_id' => $orderId,
                'status' => $response->status(),
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('PAPS Order history fetch exception', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Calculer les frais de livraison
     * 
     * @param array $data
     * @return array|null
     * @throws Exception
     */
    public function calculateDeliveryFees(array $data): ?array
    {
        try {
            $token = $this->getToken();
            
            if (!$token) {
                throw new Exception('Unable to get PAPS authentication token');
            }

            $payload = [
                'origin' => $data['origin'],
                'destination' => $data['destination'],
                'deliveryType' => $data['delivery_type'] ?? config('services.paps.default_delivery_type', 'STANDARD'),
                'sizeDetails' => $data['size_details'] ?? [],
            ];

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->timeout($this->timeout)
            ->post("{$this->apiUrl}/marketplace/price-of-multiple-parcels", $payload);

            if ($response->successful()) {
                $result = $response->json();
                
                Log::info('PAPS Delivery fees calculated', [
                    'price' => $result['data']['price'] ?? null,
                    'origin' => $data['origin'],
                    'destination' => $data['destination'],
                ]);

                return $result;
            }

            Log::error('PAPS Delivery fees calculation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('PAPS Delivery fees calculation exception', [
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Enregistrer un webhook
     * 
     * @param string $name
     * @param string $event
     * @param string $url
     * @return array|null
     * @throws Exception
     */
    public function registerWebhook(string $name, string $event, string $url): ?array
    {
        try {
            $token = $this->getToken();
            
            if (!$token) {
                throw new Exception('Unable to get PAPS authentication token');
            }

            $payload = [
                'name' => $name,
                'event' => $event,
                'url' => $url,
            ];

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->timeout($this->timeout)
            ->post("{$this->apiUrl}/webhook", $payload);

            if ($response->successful()) {
                $result = $response->json();
                
                Log::info('PAPS Webhook registered successfully', [
                    'name' => $name,
                    'event' => $event,
                    'url' => $url,
                ]);

                return $result;
            }

            Log::error('PAPS Webhook registration failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('PAPS Webhook registration exception', [
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Créer une tâche DROP_OFF
     * 
     * @param array $data
     * @return array|null
     * @throws Exception
     */
    public function createDropOffTask(array $data): ?array
    {
        try {
            $token = $this->getToken();
            
            if (!$token) {
                throw new Exception('Unable to get PAPS authentication token');
            }

            $payload = [
                'type' => 'DROP_OFF',
                'datePickup' => $data['date_pickup'] ?? now()->toDateString(),
                'timePickup' => $data['time_pickup'] ?? '10:00',
                'vehicleType' => $data['vehicle_type'] ?? config('services.paps.default_vehicle_type', 'SCOOTER'),
                'address' => $data['address'],
                'contactName' => $data['contact_name'] ?? null,
                'contactPhone' => $data['contact_phone'] ?? null,
                'packages' => $data['packages'] ?? [],
                'metadata' => array_merge([
                    'order_id' => $data['order_id'] ?? null,
                    'creator_id' => $data['creator_id'] ?? null,
                ], $data['metadata'] ?? []),
            ];

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->timeout($this->timeout)
            ->post("{$this->apiUrl}/tasks/", $payload);

            if ($response->created() || $response->successful()) {
                $result = $response->json();
                
                Log::info('PAPS Drop-off task created successfully', [
                    'job_id' => $result['data']['job']['_id'] ?? null,
                ]);

                return $result;
            }

            Log::error('PAPS Drop-off task creation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('PAPS Drop-off task creation exception', [
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
