<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    /**
     * Create a new user with proper type assignment
     * Note: is_customer is set in AuthController based on ExternalCustomer table
     */
    public function createUser(array $userData): User
    {
        // Set user type (default to student)
        $userData['type'] = $userData['type'] ?? 'student';
        
        return User::create($userData);
    }
}
