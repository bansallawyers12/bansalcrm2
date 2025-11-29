<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Admin;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
  /////////////////////////////////
  // https://bansalcrm.com/api/login
  // POST
   /* 
   {
    "email": "bansalcrm@gmail.com",
    "password": "Arun@bansal13"
   }
   
   Content-Type: application/json
   */
  /////////////////////////////////
  public function login(Request $request)
    { //dd('login');
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = Admin::where('email', $request->email)->first(); //dd($user);
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user]);
    }

    
     /////////////////////////////////
    // https://bansalcrm.com/api/appointments/logout
    // POST
     /* 
     {
      "email": "bansalcrm@gmail.com",
      "password": "Arun@bansal13"
     }

      Authorization: Bearer 2|HZRFBNn5TGirb1MvogjFCqQfIUxP0yq74cOU41wkbe177c37
      Content-Type: application/json
     */
    /////////////////////////////////
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}

