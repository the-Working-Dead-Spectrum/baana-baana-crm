<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Liste des utilisateurs
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $role = $request->get('role');

        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role && in_array($role, ['admin', 'creator', 'logistic', 'user'])) {
            $query->where('role', $role);
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Stats par rôle
        $roleStats = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        return view('admin.users.index', [
            'users' => $users,
            'search' => $search,
            'role' => $role,
            'roleStats' => $roleStats,
            'totalUsers' => User::count(),
            'availableRoles' => ['admin', 'creator', 'logistic', 'user'],
        ]);
    }

    /**
     * Formulaire de création d'utilisateur
     */
    public function create()
    {
        return view('admin.users.create', [
            'availableRoles' => ['admin', 'creator', 'logistic', 'user'],
        ]);
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,creator,logistic,user',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // Si c'est un créateur, créer automatiquement le profil creator
        if ($validated['role'] === 'creator') {
            \App\Models\Creator::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'status' => 'active',
                'brand_slug' => \Illuminate\Support\Str::slug($validated['name']),
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès');
    }

    /**
     * Afficher un utilisateur
     */
    public function show(User $user)
    {
        return view('admin.users.show', [
            'user' => $user,
        ]);
    }

    /**
     * Formulaire d'édition d'utilisateur
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user' => $user,
            'availableRoles' => ['admin', 'creator', 'logistic', 'user'],
        ]);
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => 'required|string|in:admin,creator,logistic,user',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Mettre à jour le profil creator si nécessaire
        if ($validated['role'] === 'creator') {
            $creator = \App\Models\Creator::where('user_id', $user->id)->first();
            if ($creator) {
                $creator->name = $validated['name'];
                $creator->email = $validated['email'];
                $creator->save();
            } else {
                \App\Models\Creator::create([
                    'user_id' => $user->id,
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'status' => 'active',
                    'brand_slug' => \Illuminate\Support\Str::slug($validated['name']),
                ]);
            }
        } else {
            // Supprimer le profil creator si le rôle n'est plus creator
            \App\Models\Creator::where('user_id', $user->id)->delete();
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès');
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy(User $user)
    {
        // Empêcher la suppression de son propre compte
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte');
        }

        // Supprimer le profil creator associé si existe
        \App\Models\Creator::where('user_id', $user->id)->delete();

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès');
    }

    /**
     * Activer/désactiver un utilisateur
     */
    public function toggleStatus(User $user)
    {
        // Empêcher de désactiver son propre compte
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas désactiver votre propre compte');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activé' : 'désactivé';

        return redirect()->route('admin.users.index')
            ->with('success', "Utilisateur {$status} avec succès");
    }
}