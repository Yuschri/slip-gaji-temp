<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function authenticate(string $username, string $password)
    {
        $user = $this->userRepository->findByUsername($username);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found.',
            ];
        }

        if ($password !== $user->USER_PASSWORD) {
            return [
                'success' => false,
                'message' => 'Invalid credentials.',
            ];
        }

        $roles = $user->roles->pluck('nama_role')->toArray();
        $allowedRoles = ['Super Admin', 'Manager Finance'];

        $hasAllowedRole = false;
        foreach ($roles as $role) {
            if (in_array($role, $allowedRoles)) {
                $hasAllowedRole = true;
                break;
            }
        }

        if (!$hasAllowedRole) {
            return [
                'success' => false,
                'message' => 'Unauthorized role.',
            ];
        }

        Auth::login($user);

        return [
            'success' => true,
        ];
    }
}
