<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helpers\General;
use DB;

class AuthController extends Controller
{
    public function register(Request $request) {

    	$rules = [
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'plan' => 'required|string',
            'bank' => 'required|string',
            'acnt_name' => 'required|string',
            'acnt_no' => 'required|string',
            'password' => 'required|string|confirmed',
            'payment_slip' => 'required|mimes:jpg,png,jpeg|max:5048'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {

        	if ($request['referer'] && $request['referer'] != '') {
	        	$referer_no = DB::table('users')->where('urlid', '=', $request['referer'])->where('promoter', '=', 'Yes')->count();

	        	if ($referer_no > 0) {
	        		$ref_no = User::where('urlid','=', $request['referer'])->first();

	        		$v = $ref_no->referer_no + 1;

		        	$ref = DB::table('users')->where('urlid',$request['referer'])->limit(1)->update([
		                'referer_no' => $v
		            ]);
	        	}
	        }else{
	        	$request['referer'] = '';
	        }

        	$urlid = General::user_urlid();

        	$new_image_name = time().'-'.$request['name'].'.'.$request['payment_slip']->extension();

	        $request['payment_slip']->move(public_path('payment_slips'), $new_image_name);

            $user = User::create([
	            'name' => $request['name'],
	            'email' => $request['email'],
	            'plan' => $request['plan'],
	            'password' => bcrypt($request['password']),
	            'payment_slip' => $new_image_name,
	            'urlid' => $urlid,
	            'status' => 'Pending',
	            'promoter' => 'No',
	            'promoter_slip' => '',
	            'referer_no' => 0,
	            'referer' => $request['referer'],
	            'acnt_no' => $request['acnt_no'],
	            'acnt_name' => $request['acnt_name'],
	            'bank' => $request['bank'],
	        ]);

	        

	        $token = $user->createToken('myapptoken')->plainTextToken;

	        $response = [
	            'user' => $user,
	            'token' => $token,
	            'status' => 'success'
	        ];

	        return response($response, 201);
        }

        if ($validator->fails()) {
            return $validator->errors();
        }
    }

    public function login(Request $request) {

    	$rules = [
            'email' => 'required|string',
            'password' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
        	// Check email
	        $user = User::where('email', $request['email'])->first();

	        // Check password
	        if(!$user || !Hash::check($request['password'], $user->password)) {
	            return response([
	                'email' => 'Username or password is incorrect'
	            ]);
	        }

	        $token = $user->createToken('myapptoken')->plainTextToken;
	        Auth::guard('web')->login($user);

	        $response = [
	            'user' => $user,
	            'token' => $token,
	            'status' => 'success'
	        ];

	        return response($response, 201);
        }

        if ($validator->fails()) {
            return $validator->errors();
        }

        
    }

    public function logout(Request $request) {
        auth()->user()->tokens()->delete();

        return [
        	'status' => 'success',
            'message' => 'Logged out'
        ];
    }

    public function is_user_loggedin() {

    	return auth()->guard('user')->id();

    	// return auth()->guard('user')->check();

    	// if(auth()->guard('admin')->check()){
    	// 	return [
	    //     	'status' => 'success',
	    //         'message' => 'Logged out'
	    //     ];
    	// }else{
    	// 	return [
     //    		'status' => 'failed',
     //        	'message' => 'Logged out'
     //    	];
    	// }
    }

    public function get_all_users() {

    	$users_no = DB::table('users')->count();

    	$users = DB::table('users')->where('status', '=', 'Pending')->orderBy('id', 'desc')->limit(5)->get();

    	$response = [
            'user_no' => $users_no,
            'users' => $users,
            'status' => 'success'
        ];

        return response($response, 201);
    }

    public function get_new_promoters() {

    	$users_no = DB::table('users')->count();

    	$users = DB::table('users')->where('promoter', '=', 'Pending')->limit(5)->get();

    	$response = [
            'user_no' => $users_no,
            'users' => $users,
            'status' => 'success'
        ];

        return response($response, 201);
    }

    public function get_promoters() {

    	$p_no = DB::table('users')->where('promoter', '=', 'Yes')->count();

    	$np = DB::table('users')->where('promoter', '=', 'Pending')->get();
    	$dp = DB::table('users')->where('promoter', '=', 'Declined')->get();
    	$p = DB::table('users')->where('promoter', '=', 'Yes')->get();
    	$ap = DB::table('users')->where('referer_no', '>', 0)->get();

    	$response = [
            'p_no' => $p_no,
            'np' => $np,
            'dp' => $dp,
            'p' => $p,
            'ap' => $ap,
            'status' => 'success'
        ];

        return response($response, 201);
    }


    public function get_users() {

    	$nu = DB::table('users')->where('status', '=', 'Pending')->orderBy('id', 'desc')->get();
    	$du = DB::table('users')->where('status', '=', 'Declined')->get();
    	$au = DB::table('users')->where('status', '=', 'Active')->get();
    	$u = DB::table('users')->get();

    	$response = [
            'nu' => $nu,
            'du' => $du,
            'u' => $u,
            'au' => $au,
            'status' => 'success'
        ];

        return response($response, 201);
    }



    public function get_user_detail($user_id) {



    	$user = User::where('id', $user_id)->first();
    	$ref_no = DB::table('users')->where('referer', '=', $user->urlid)->count();

    	$result = json_decode($user, true);

		$response = [
            'ref_no' => $ref_no,
            'user' => $result,
            'status' => 'success'
        ];

        return response($response, 201);
    }

    public function update_user_status(Request $request) {

    	$rules = [
    		'userid' => 'required',
            'status' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {

        	$user = DB::table('users')->where('id',$request['userid'])->limit(1)->update([
                'status' => $request['status'],
                'promoter' => $request['pstatus']
            ]);

	        $response = [
	            'status' => 'success'
	        ];

	        return response($response, 201);
        }

        if ($validator->fails()) {
            return $validator->errors();
        }

        
    }

    public function update_profile(Request $request) {

    	$rules = [
    		'userid' => 'required',
            'name' => 'required',
            'bank' => 'required',
            'acnt_name' => 'required',
            'acnt_no' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {

        	$user = DB::table('users')->where('id',$request['userid'])->limit(1)->update([
                'name' => $request['name'],
                'bank' => $request['bank'],
                'acnt_name' => $request['acnt_name'],
                'acnt_no' => $request['acnt_no']
            ]);

	        $response = [
	            'status' => 'success'
	        ];

	        return response($response, 201);
        }

        if ($validator->fails()) {
            return $validator->errors();
        }

        
    }

    public function sub(Request $request) {

    	$rules = [
    		'userid' => 'required',
    		'plan' => 'required|string',
            'payment_slip' => 'required|mimes:jpg,png,jpeg|max:5048'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {

        	$new_image_name = time().'-'.$request['userid'].'.'.$request['payment_slip']->extension();

	        $request['payment_slip']->move(public_path('payment_slips'), $new_image_name);

        	$user = DB::table('users')->where('id',$request['userid'])->limit(1)->update([
                'plan' => $request['plan'],
                'payment_slip' => $new_image_name,
                'status' => 'Pending'
            ]);

	        $response = [
	            'status' => 'success'
	        ];

	        return response($response, 201);
        }

        if ($validator->fails()) {
            return $validator->errors();
        }

        
    }

    public function promote(Request $request) {

    	$rules = [
    		'userid' => 'required',
            'promoter_slip' => 'required|mimes:jpg,png,jpeg|max:5048'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {

        	$new_image_name = time().'-'.$request['userid'].'.'.$request['promoter_slip']->extension();

	        $request['promoter_slip']->move(public_path('promoter_slips'), $new_image_name);

        	$user = DB::table('users')->where('id',$request['userid'])->limit(1)->update([
                'promoter_slip' => $new_image_name,
                'promoter' => 'Pending'
            ]);

	        $response = [
	            'status' => 'success'
	        ];

	        return response($response, 201);
        }

        if ($validator->fails()) {
            return $validator->errors();
        }

        
    }
}
