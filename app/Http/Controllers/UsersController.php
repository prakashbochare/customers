<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Auth;
use App\Models\Passport\Token;
use Laravel\Passport\PassportServiceProvider;

class UsersController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
// User Registration Api
    public function register(Request $request) {
        $validator = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'role_id' => 'required',
        ]);
        $checkEmail = DB::table('users')->where('email', '=', $request->email)->first();
        if (empty($checkEmail) && $checkEmail == null) {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $token = Str::random(60);
            $user->remember_token = hash('sha256', $token);
            $user->status = $request->status;
            if (isset($request->role_id) && !empty($request->role_id)) {
                $user->role_id = $request->role_id;
            } else {
                $user->role_id = 1;
            }
            $user->status = 1;
            if ($user->save()) {
                $success['status'] = '1';
                $success['msg'] = 'Your account has been created successfully';
                $success['name'] = $user->name;
            }
        } else {
            $success['status'] = '0';
            $success['msg'] = 'Your email is already registered with us.';
        }
        return response()->json(['success' => $success]);
    }

}
