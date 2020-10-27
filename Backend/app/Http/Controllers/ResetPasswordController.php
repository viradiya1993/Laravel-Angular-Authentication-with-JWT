<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    public function sendEmail(Request $request) {
        
        if(!$this->validteEmail($request->email)) {
            return $this->failedResponse();
        }

        $this->send($request->email);
        return $this->successResponse();
    }

    public function send($email) {
        $token = $this->createToken($email);
        Mail::to($email)->send(new ResetPasswordMail($token));
    }

    public function createToken($email) {
       $oldToken = DB::table('password_resets')->where('email',$email)->first();
       if($oldToken) {
           return $oldToken->token;
       }
        $token = str_random(250);
        $this->saveToken($email,$token);
        return $token;
    }

    public function saveToken($email, $token) {
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
    }

    public function validteEmail($email) {
        return !!User::where('email',$email)->first();
    }

    public function failedResponse() {

        return response()->json([
             'error' => 'Email does\t found our database' 
        ], Response::HTTP_NOT_FOUND);
        
    }

    public function successResponse() {
        return response()->json([
            'data' => 'Reset Email is send successfully,please check your inbox.' 
       ], Response::HTTP_OK);
    }
}
