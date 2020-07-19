<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiAuthController extends Controller
{
    public $successStatus = 200;
    protected $username;

    /**
     * Create a new user instance after a valid registration..
     *
     * @return string
     */
    public function register (Request $request) {
        try{
            DB::beginTransaction();
            $validator = Validator::make($request->all(),
                [
                    'display_name' => 'required|string|max:40',
                    'username' => 'string|max:40|unique:users',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:8|confirmed',
                ]);
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors(),'status'=>'error'], 401);
            }
            $input = $request->all();
            $input['password']=Hash::make($request['password']);
            $input['email_verified_at']=date(now());
            $user = User::create($input);
            DB::commit();
            $success['message'] = 'Usuario creado correctamente!';
            $success['token'] = $user->createToken('A&B - '.Carbon::now())->accessToken;
            return response()->json(['success' => $success, 'status'=>'success', 'objUser' => $user], $this->successStatus);
        }catch (\Exception $err){
            DB::rollBack();
            return response()->json(
                [
                    'status' => 'error',
                    'results' => $err->getMessage()
                ], 400);
        }
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function findUsername()
    {
        $login = request()->input('login');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        request()->merge([$fieldType => $login]);

        return $fieldType;
    }

    /**
     * Get a user instance after a valid registration..
     *
     * @return string
     */
    public function login (Request $request) {
        try{
            $validator = Validator::make($request->all(), [
                'login' => 'required|string|max:255',
                'password' => 'required|string|min:8',
            ]);
            if ($validator->fails())
            {
                return response(['errors'=>$validator->errors()->all()], 422);
            }
            $this->username = $this->findUsername();
            if($this->username === 'username'){
                if (Auth::attempt(['username' => $request->login, 'password' => $request->password])) {
                    $user = Auth::user();
                    if($user->email_verified_at !== NULL){
                        $success['token'] = $user->createToken('A&B - '.Carbon::now())->accessToken;
                        return response()->json(['success' => $success,'status'=>'success', 'objUser' => $user], $this->successStatus);
                    } else{
                        return response()->json(['error'=>'Por favor verifica tu cuenta'], 401);
                    }
                } else {
                    return response()->json(['error' => 'Tus credenciales no son válidas, por favor revisa e intenta de nuevo.','status'=>'error'], 401);
                }
            } else{
                if (Auth::attempt(['email' => request('login'), 'password' => request('password')])) {
                    $user = Auth::user();
                    if($user->email_verified_at !== NULL){
                        $success['token'] = $user->createToken('A&B - '.Carbon::now())->accessToken;
                        return response()->json(['success' => $success,'status'=>'success', 'objUser' => $user], $this->successStatus);
                    } else{
                        return response()->json(['error'=>'Por favor verifica tu cuenta'], 401);
                    }
                } else {
                    return response()->json(['error' => 'Tus credenciales no son válidas, por favor revisa e intenta de nuevo.','status'=>'error'], 401);
                }
            }

        }catch (\Exception $err){
            return response()->json(
                [
                    'status' => 'Error',
                    'results' => $err->getMessage()
                ], 400);
        }
    }

    /**
     * Revoke an access token to logout a user..
     *
     * @return string
     */
    public function logout (Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        return response()->json(['message' => 'Has cerrado la sesion correctamente! El token ha sido revocado', 'status'=>'success'], $this->successStatus);
    }
}
