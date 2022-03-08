<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Helpers\General;
use DB;

class MessageController extends Controller
{
    public function sendmsg(Request $request) {

    	$rules = [
    		'email' => 'required',
    		'name' => 'required',
            'message' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {

        	$payment = Message::create([
        		'email' => $request['email'],
	            'name' => $request['name'],
	            'message' => $request['message']
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

    public function get_all_messages() {

    	$msg_no = DB::table('messages')->count();

    	$messages = DB::table('messages')->orderBy('id', 'desc')->get();

    	$response = [
            'msg_no' => $msg_no,
            'messages' => $messages,
            'status' => 'success'
        ];

        return response($response, 201);
    }
}
