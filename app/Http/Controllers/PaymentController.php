<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helpers\General;
use DB;

class PaymentController extends Controller
{
    public function pay(Request $request) {

    	$rules = [
    		'complete' => 'required',
    		'userid' => 'required',
            'amount' => 'required',
            'plan' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {

        	$urlid = General::payment_urlid();

        	$paymentid = General::paymentid();

        	$payment = Payment::create([
        		'userid' => $request['userid'],
	            'amount' => $request['amount'],
	            'urlid' => $urlid,
	            'paymentid' => $paymentid,
	            'status' => 'Approved',
	            'plan' => $request['plan']
	        ]);

	        if ($request['complete'] == 'Yes') {
	        	$user = DB::table('users')->where('id',$request['userid'])->limit(1)->update([
	                'status' => 'Complete'
	            ]);
	        }

	        $response = [
	            'status' => 'success'
	        ];

	        return response($response, 201);
        }

        if ($validator->fails()) {
            return $validator->errors();
        }

        
    }

    public function get_user_payments($user_id) {

        $pay_no = DB::table('payments')->where('userid', $user_id)->count();

        $pays = DB::table('payments')->where('userid', $user_id)->orderBy('id', 'desc')->get();

        $response = [
            'pay_no' => $pay_no,
            'pays' => $pays,
            'status' => 'success'
        ];

        return response($response, 201);
    }

    public function pay_promoter(Request $request) {

    	$rules = [
    		'userid' => 'required',
            'amount' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {

        	$urlid = General::payment_urlid();

        	$paymentid = General::paymentid();

        	$payment = Payment::create([
        		'userid' => $request['userid'],
	            'amount' => $request['amount'],
	            'urlid' => $urlid,
	            'paymentid' => $paymentid,
	            'status' => 'Approved',
	            'plan' => 'Promoter'
	        ]);

	        	$user = DB::table('users')->where('id',$request['userid'])->limit(1)->update([
	                'referer_no' => 0
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
