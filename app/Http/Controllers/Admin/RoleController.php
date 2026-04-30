<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Liste des rôles
     */
    public function index()
    {
        // Si vous utilisez spatie/laravel-permission
        // $roles = Role::withCount('users')->get();
        
        // Pour une solution simple sans package
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Administrateur complet',
                'users_count' => \App\Models\User::where('role', 'admin')->count(),
                'permissions' => ['all']
            ],
            [
                'name' => 'creator',
                'description' => 'Créateur / Vendeur',
                'users_count' => \App\Models\User::where('role', 'creator')->count(),
                'permissions' => ['view_orders', 'manage_products', 'view_stats']
            ],
            [
                'name' => 'logistic',
                'description' => 'Service logistique',
                'users_count' => \App\Models\User::where('role', 'logistic')->count(),
                'permissions' => ['view_orders', 'update_order_status', 'track_shipments']
            ],
            [
                'name' => 'user',
                'description' => 'Utilisateur standard',
                'users_count' => \App\Models\User::where('role', 'user')->count(),
                'permissions' => ['view_profile', 'view_orders']
            ]
        ];

        $permissions = [
            'all' => 'Toutes les permissions',
            'view_dashboard' => 'Voir le tableau de bord',
            'view_orders' => 'Voir les commandes',
            'manage_orders' => 'Gérer les commandes',
            'update_order_status' => 'Mettre à jour le statut des commandes',
            'manage_products' => 'Gérer les produits',
            'manage_users' => 'Gérer les utilisateurs',
            'manage_creators' => 'Gérer les créateurs',
            'view_stats' => 'Voir les statistiques',
            'manage_settings' => 'Gérer les paramètres',
            'sync_orders' => 'Synchroniser les commandes',
            'track_shipments' => 'Suivre les expéditions',
            'view_profile' => 'Voir le profil',
        ];

        return view('admin.roles.index', [
            'roles' => $roles,
            'permissions' => $permissions,
            'totalUsers' => \App\Models\User::count(),
        ]);
    }

    /**
     * Mettre à jour les permissions d'un rôle
     */
    public function updatePermissions(Request $request)
    {
        $request->validate([
            'role' => 'required|in:admin,creator,logistic,user',
            'permissions' => 'array',
            'permissions.*' => 'string',
        ]);

        // Ici vous pouvez sauvegarder les permissions dans la base de données
        // Pour une solution simple, on peut utiliser un cache ou un fichier de config
        
        $role = $request->input('role');
        $permissions = $request->input('permissions', []);

        // Stocker temporairement dans la session pour la démo
        session()->put("role_permissions.{$role}", $permissions);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Permissions mises à jour avec succès');
    }

    /**
     * Afficher les utilisateurs d'un rôle spécifique
     */
    public function showUsers($role)
    {
        if (!in_array($role, ['admin', 'creator', 'logistic', 'user'])) {
            abort(404);
        }

        $users = \App\Models\User::where('role', $role)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $roleNames = [
            'admin' => 'Administrateur',
            'creator' => 'Créateur',
            'logistic' => 'Logistique',
            'user' => 'Utilisateur',
        ];

        return view('admin.roles.users', [
            'users' => $users,
            'role' => $role,
            'roleName' => $roleNames[$role] ?? $role,
        ]);
    }
}