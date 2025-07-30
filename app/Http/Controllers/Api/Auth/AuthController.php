<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 *
 * @tags Auth
 *
 */
class AuthController extends Controller
{
     /**
     * @unauthenticated
     *
     * Login.
     *
     * Login.
     *
     */
    public function login(LoginRequest $request)
    {
        try {
            $request->validated();

            DB::beginTransaction();

            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 401);
            }

            // find user
            $user = User::where('email', $request->email)->firstOrFail();

            // create token
            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Success',
                'data' => [
                    'token_type' => 'Bearer',
                    'access_token' => $token,
                    'user' => $user,
                ],
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     *
     * Logout.
     *
     * Logout the authenticated user.
     *
     */
    public function logout(Request $request)
    {
        try {
            if (!$request->user()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Unauthenticated.',
                ]);
            }

            // delete token
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Logged out successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => $th->getCode(),
                'status' => 'error',
                'message' => $th->getMessage(),
            ]);
        }
    }

    /**
     *
     * Update Password.
     *
     * Update Password the authenticated user.
     *
     */
    public function updatePassword(Request $request)
    {
        try {
            $request->validate([
                /**
                 * Current password
                 *
                 * @example string
                 */
                'current_password' => 'required|string',
                /**
                 * New password
                 *
                 * @example string
                 */
                'new_password' => 'required|string|confirmed',
            ]);

            $user = Auth::user();

            // verified the new password
            if (!Hash::check($request->input('current_password'), $user->password)) {
                return response()->json([
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Current password is incorrect',
                ], 400);
            }

            // update
            $user->password = Hash::make($request->input('new_password'));
            $user->save();

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Password updated successfully',
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'code' => $th->getCode() ?: 500,
                'status' => 'error',
                'message' => $th->getMessage(),
            ]);
        }
    }
}
