<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function login(Request $request) {

    	$rules = [
            'username' => 'required|string',
            'password' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
        	// Check email
	        $admin = Admin::where('username', $request['username'])->first();

	        // Check password
	        if(!$admin || !Hash::check($request['password'], $admin->password)) {
	            return response([
	                'message' => 'Bad creds'
	            ], 401);
	        }

	        $token = $admin->createToken('myapptoken')->plainTextToken;

	        // Auth::guard('admin')->login($admin);

	        $response = [
	            'admin' => $admin,
	            'token' => $token,
	            'status' => 'success'
	        ];

	        return response($response, 201);
        }

        if ($validator->fails()) {
            return $validator->errors();
        }

        
    }

    public function is_user_loggedin() {

    	if(auth()->guard('admin')->check()){
    		return [
	        	'status' => 'success',
	            'message' => 'Logged out'
	        ];
    	}else{
    		return [
        		'status' => 'success',
            	'message' => 'Logged out'
        	];
    	}
    }

    public function admin_pass(Request $request) {

        $rules = [
            'oldp' => 'required|string',
            'newp' => 'required|string',
            'cnewp' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            // Check email
            $admin = Admin::where('username', $request['username'])->first();

            // Check password
            if(!$admin || !Hash::check($request['password'], $admin->password)) {
                return response([
                    'message' => 'Bad creds'
                ], 401);
            }

            $response = [
                'admin' => $admin,
                'token' => $token,
                'status' => 'success'
            ];

            return response($response, 201);
        }

        if ($validator->fails()) {
         

    }
}
