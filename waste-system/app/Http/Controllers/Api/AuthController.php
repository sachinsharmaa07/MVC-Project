<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Generate an API token for the user
     */
    public function createToken(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'device_name' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !\Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken($validated['device_name'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'user_id' => (string) $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ], 201);
    }

    /**
     * Revoke a token (delete it)
     */
    public function revokeToken(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Token revoked successfully']);
    }

    /**
     * List all tokens for the user
     */
    public function listTokens(Request $request)
    {
        $tokens = $request->user()
            ->tokens()
            ->select('id', 'name', 'created_at', 'last_used_at')
            ->get();

        return response()->json([
            'tokens' => $tokens,
        ]);
    }

    /**
     * Delete a specific token
     */
    public function deleteToken(Request $request, $tokenId)
    {
        $token = $request->user()->tokens()->find($tokenId);

        if (!$token) {
            return response()->json(['message' => 'Token not found'], 404);
        }

        $token->delete();

        return response()->json(['message' => 'Token deleted successfully']);
    }

    /**
     * Get current authenticated user
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id' => (string) $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'role' => $user->role,
            'created_at' => $user->created_at->toDateTimeString(),
        ]);
    }
}
