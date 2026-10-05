<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ExternalCustomer;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    /**
     * Register a new user
     * Supports both traditional registration (with password) and Google registration (with google_id)
     */
    public function register(Request $request): JsonResponse
    {
        $isGoogleRegistration = $request->has('google_id') && !empty($request->google_id);

        // Base validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'cpf' => 'required|string|unique:users,cpf',
            'phone' => 'nullable|string|max:20',
            'education_level' => 'nullable|in:fundamental,medio_incompleto,medio_completo,superior_incompleto,superior_completo,pos_graduacao',
        ];

        // Conditional validation based on registration type
        if ($isGoogleRegistration) {
            // Google registration: google_id required, password not required
            $rules['google_id'] = 'required|string|unique:users,google_id';
        } else {
            // Traditional registration: password required
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($rules);

        // Prepare user data
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'cpf' => $request->cpf,
            'type' => 'student', // Default type
            'phone' => $request->phone,
            'education_level' => $request->education_level,
        ];

        // Add password or google_id based on registration type
        if ($isGoogleRegistration) {
            $userData['google_id'] = $request->google_id;
            // Password is not set for Google users
        } else {
            $userData['password'] = Hash::make($request->password);
        }

        // Check if CPF exists in external customers table
        $userData['is_customer'] = ExternalCustomer::exists($request->cpf);

        $user = User::create($userData);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => new UserResource($user),
            'token' => $token,
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($user->isGoogleUser()) {
            throw ValidationException::withMessages([
                'email' => ['This account can only be accessed via Google login.'],
            ]);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'sometimes|nullable|string|max:20',
            'education_level' => 'sometimes|nullable|in:fundamental,medio_incompleto,medio_completo,superior_incompleto,superior_completo,pos_graduacao',
        ]);

        $user->update($request->only(['name', 'email', 'phone', 'education_level']));

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => new UserResource($user->fresh()),
        ]);
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        // Google users can set a password (if they don't have one) or change it
        if ($user->isGoogleUser() && !$user->password) {
            // Google user setting password for the first time
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'message' => 'Password set successfully',
            ]);
        }

        // Traditional password change (requires current password)
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Password changed successfully',
        ]);
    }

    /**
     * Delete user account
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $user = $request->user();

        // Google users without password can delete without password verification
        if ($user->isGoogleUser() && !$user->password) {
            $user->delete();

            return response()->json([
                'message' => 'Account deleted successfully',
            ]);
        }

        // Users with password must verify it before deletion
        $request->validate([
            'password' => 'required|string',
        ]);

        // Verify password before deletion
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['The password is incorrect.'],
            ]);
        }

        // Delete user (this will also delete related data due to foreign key constraints)
        $user->delete();

        return response()->json([
            'message' => 'Account deleted successfully',
        ]);
    }
}
