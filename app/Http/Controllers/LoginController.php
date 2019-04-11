<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name'=> 'required',
            'email'=> 'required|email|unique:users',
            'password'=> 'required',
            'c_password'=> 'required|same:password',
        ] );

        if ($validator->fails())
        {
            return $this->sendError('error validation', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->accessToken;
        $success['name'] = $user->name;


        return $this->sendResponse($success , 'User registered');

    }

    public function login(){

        if(Auth::attempt(['email' => request('email'), 'password' => request('password')]))
        {
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            return $this->sendResponse($success , 'User Login');
        }
        else{
            return $this->sendError('error','Unauthorised' ,401);
        }
    }

    public function sendResponse($result , $message){

        $response = [
            'success' => true ,
            'data' => $result,
            'message' => $message
        ];

        return response()->json($response , 200);
    }

    public function sendError($error , $errorMessages = [] , $code = 404){

        $response = [
            'success' => false ,
            'message' => $error
        ];

        if (!empty($errorMessages))
        {

            $response['date'] = $errorMessages;
        }

        return response()->json($response , $code);

    }
}


