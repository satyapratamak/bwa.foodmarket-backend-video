<?php

namespace App\Http\Controllers\API;


use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use PasswordValidationRules;


use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
//use App\Actions\Fortify\PasswordValidationRules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        // validasi input
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required',
            ]);

            $credentials = $request(['email', 'password']);

            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error(
                    ['message' => 'Unauthorized'],
                    'Authentication Failed',
                    500

                );
            }



            $user = User::where('email', $request->email)->first();
            // check hash password
            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Invalid Credentials');
            }

            //Jika Berhasil
            $tokenResult = $user->createToken('authToken')->plainTextToken();
            return ResponseFormatter::success([

                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 'Authenticated');
        } catch (Exception $error) {

            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Authentication Failed', 500);
        }
    }

    public function register(Request $request)
    {

        try {
            $request->validate(
                [
                    'name' => ['required', 'string', 'max:255'],
                    'email'
                    => ['required', 'string', 'email', 'max:255', 'unique:users'],
                    'password' => $this->passwordRules(),

                ]
            );

            User::create(
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'address' => $request->address,
                    'houseNumber' => $request->houseNumber,
                    'phoneNumber' => $request->phoneNumber,
                    'city' => $request->city,
                    'password' => Hash::make($request->password),
                ]
            );

            $user = User::where('email', $request->email)->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([

                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 'Registered');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Registration Failed', 500);
        }
    }
}
