<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Get the authenticated user's profile.
     */
    public function show(Request $request)
    {
        return response()->json([
            'user' => $request->user()->append('avatar_url'),
        ]);
    }

    /**
     * Update the authenticated user's profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'bio' => ['nullable', 'string', 'max:1000'],
            'preferences' => ['nullable', 'array'],
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user->fresh()->append('avatar_url'),
        ]);
    }

    /**
     * Update the user's avatar.
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'], // Max 2MB
        ]);

        $user = $request->user();

        if ($request->file('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar_path' => $path]);
        }

        return response()->json([
            'message' => 'Avatar updated successfully.',
            'avatar_url' => $user->avatar_url,
        ]);
    }
}
