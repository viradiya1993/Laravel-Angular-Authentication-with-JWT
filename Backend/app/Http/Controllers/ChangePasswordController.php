<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\ChangepasswordRequest;
use Illuminate\Support\Facades\DB;
use App\User;

class ChangePasswordController extends Controller
{

    
    public function process(ChangepasswordRequest $request) {
        return $this->getPasswordResetTableRow($request)->count()> 0 ? $this->changePassword($request) : 
         $this->tokenNotFoundResponse();
    }

    public function getPasswordResetTableRow($request) {
        
        return DB::table('password_resets')->where([
            'email' => $request->email,
            'token' => $request->resetToken
        ]);
    }

    private function changePassword($request) {
        $user = User::whereEmail($request->email)->first();
        $user->update(['password' => $request->password]);
        $this->getPasswordResetTableRow($request)->delete();
        return response()->json(['data' => 'Password Succssfully Chnaged.'],
        Response::HTTP_OK);
       
    }

    private function tokenNotFoundResponse() {
        return response()->json(['error' => 'Token or Email is incorrect.'],
        Response::HTTP_NOT_FOUND);
    }
}
