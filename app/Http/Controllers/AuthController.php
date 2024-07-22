<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\ResetCodePassword;
use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'email_verified_at' => now(),
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'code' => 200,
            'message' => 'Account created successfully! kindly login.',
            'data' => $user
        ], 200);
    }

    public function login(LoginRequest $request)
    {
        $user = User::query()->where('email', $request->email)->first();

        if ($user && !Hash::check($request->password, $user->password)) {
            return response()->json([
                'code' => 401,
                'message' => 'Incorrect Password!',
            ], 401);
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'code' => 401,
                'message' => "Email or Username doesn't exist",
            ], 401);
        }

        if($user->status == "Pending") {
            return response()->json([
                'code' => 401,
                'message' => "Please proceed to confirm your email address.",
            ], 401);
        }

         // Determine if the login details are an email or a username
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (auth()->attempt($credentials)) {
            $token = $user->createToken("API TOKEN")->plainTextToken;
            // $user->createToken('API TOKEN', ['*'], now()->addMinutes(60))->plainTextToken;

            return response()->json([
                'code' => 200,
                'message' => "Login successfully.",
                'token' => $token,
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'code' => 401,
                'message' => 'User authentication failed.',
            ], 401);
        }
    }

    public function forget_password(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email|exists:users',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Please see errors parameter for all errors.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        // Delete all old code that user send before.
        ResetCodePassword::where('email', $request->email)->delete();

        // Generate random code
        $code = mt_rand(100000, 999999);

        // Create a new code
        $codeData = ResetCodePassword::create([
            'email' => $request->email,
            'code' => $code
        ]);

        // Send the notification
        $user->notify(new PasswordResetNotification($codeData));


        return response()->json([
            'code' => 200,
            'message' => "We have emailed your password reset code.",
        ], 200);
    }

    public function reset_password(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'code' => 'required|string|exists:reset_code_passwords',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Please see errors parameter for all errors.',
                'errors' => $validator->errors()
            ], 422);
        }

        if (ResetCodePassword::where('code', '=', $request->code)->exists()) {
            // find the code
            $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

            // check if it does not expired: the time is one hour
            if ($passwordReset->created_at > now()->addHour()) {
                $passwordReset->delete();

                return response()->json([
                    'code' => 401,
                    'message' => 'Password reset code expired'
                ], 401);
            }

            // find user's email
            $user = User::where('email', $passwordReset->email)->first();

            // update user password
            $user->update([
                'password' => Hash::make($request->password),
                'current_password' => $request->password
            ]);

            // delete current code
            $passwordReset->delete();

            return response()->json([
                'code' => 200,
                'message' => 'Password has been successfully reset, Please login',
            ], 200);
        } else {
            return response()->json([
                'code' => 401,
                'message' => "Code doesn't exist in our database."
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Ensure the user is authenticated
            if (!Auth::check()) {
                return response()->json([
                    'code' => 401,
                    'message' => "User is not authenticated."
                ], 401);
            }

            // Get the authenticated user
            $user = Auth::user();

            // Get the token from the request header
            $token = $request->bearerToken();

            // Revoke the token
            if ($token) {
                $personalAccessToken = PersonalAccessToken::findToken($token);
                if ($personalAccessToken && $personalAccessToken->tokenable_id === $user->id) {
                    $personalAccessToken->delete();
                }
            } else {
                // Revoke all tokens if no specific token is provided
                $user->tokens()->delete();
            }

            // Return success response
            return response()->json([
                'code' => 200,
                'message' => "Successfully logged out."
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => "Logout failed."
            ], 500);
        }
    }
}
