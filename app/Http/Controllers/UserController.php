<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Mail\OTPMail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    function LoginPage(){
        return view('pages.auth.login-page');
    }

    function RegistrationPage(){
        return view('pages.auth.registration-page');
    }
    function SendOtpPage(){
        return view('pages.auth.send-otp-page');
    }
    function VerifyOTPPage(){
        return view('pages.auth.verify-otp-page');
    }

    function ResetPasswordPage(){
        return view('pages.auth.reset-pass-page');
    }

    function ProfilePage(){
        return view('pages.dashboard.profile-page');
    }







    //user registration
    function UserRegistration(Request $request){
        try {
            User::create([
                'firstName' => $request->input('firstName'),
                'lastName' => $request->input('lastName'),
                'email' => $request->input('email'),
                'mobile' => $request->input('mobile'),
                'password' => $request->input('password')
            ]);
    
            return response()->json([
                'status' => 'success',
                'message' => 'User Registration Successful'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'User Registration failed'
            ]);
        }
    }
    

    //user login
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
            ],200)->cookie('token',$token,60*24*30);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Unauthorized'
            ],200);
        }  
    }

     //user forgate password otp send 
    function SendOTPCode(Request $request){

        $email=$request->input('email');
        $otp=rand(1000,9999);
        $count=User::where('email','=',$email)->count();

        if($count==1){
            // OTP Email Address
            Mail::to($email)->send(new OTPMail($otp));
            // OTO Code Table Update
            User::where('email','=',$email)->update(['otp'=>$otp]);

            return response()->json([
                'status' => 'success',
                'message' => '4 Digit OTP Code has been send to your email !'
            ],200);
        }
        else{
            return response()->json([
                'status' => 'failed',
                'message' => 'unauthorized'
            ]);
        }
    }
    
     //user verify OTP code
    function VerifyOTP(Request $request){
        $email = $request->input('email');
        $otp = $request->input('otp');
        $count = User::where('email','=',$email)
        ->where('otp','=',$otp)->count();

        if($count == 1){
            //Database OTP Update
            User::where('email','=',$email)->update(['otp'=>'0']);

            //password reset token issue  
            $token = JWTToken::CreateToken($request->input('email'));
            return response()->json([
                'status' => 'success',
                'message' => 'OTP Verification Successful',
                'token' => $token,
            ]);
        }
        else{
            return response()->json([
                'status' => 'failed',
                'message' => 'unauthorized'
            ]);
        }
    }

     // user reset password after otp verification
     function ResetPassword(Request $request){
         try {
             $email = $request->header('email');
             $password = $request->input('password');
             User::where('email', '=', $email)->update(['password' => $password]);
      
             return response()->json([
                 'status' => 'success',
                 'message' => 'Password reset successfully'
             ]);
     
         } catch (Exception $e) {
             return response()->json([
                 'status' => 'failed',
                 'message' => 'Something went wrong: ' . $e->getMessage()
             ]);
         }
     }
     
}








