<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    
    function UserRegistration(Request $request){
        try{
            User::create([
                'firstName' => $request->input('firstName'),
                'lastName' => $request->input('lastName'),
                'email' => $request->input('email'),
                'mobile' => $request->input('mobile'),
                'password' => $request->input('password')
           ]);
   
           return response()->json([
               'status' => 'success',
               'massage' =>'User Registration Successful'
           ]);
        }
        catch (Exception $e) {
            return response()->json([
                'status' => 'failed' ,
                'massage' =>'User Registration failed'
                //'massage' => $e->getMessage()  //this line can used to display error message
            ]);
        }
         
    }

    
    function UserLogin(Request $request){
        $count = User::where('email', $request->input('email'))
            ->where('password', $request->input('password'))     
            ->count();
    
        if ($count == 1) {
            $token = JWTToken::CreateToken($request->input('email'));
            return response()->json([
                'status' => 'success',
                'message' => 'User Login Successful',
                'token' => $token,
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Unauthorized'
            ]);
        }
    }
    

}