<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\member;
use App\Models\pegawai;
use App\Models\instruktur;
use Validator;


class AuthController extends Controller
{
    public function login(Request $request){
        // $loginData = $request->all();
        $validate = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' =>'required',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        if($member = member::where('email',$request->email)->first()){
            $loginMember = member::where('email', '=',$request['email'])->first();

            if(Hash::check($request['password'], $loginMember['password'])){
                $member = member::where('email', '=',$request['email'])->first();
            }else{
                return response(['message' => 'Invalid Password or Email'], 404);
            }

            $token = bcrypt(uniqid());
            // $token = $member->createToken('Authentication Token')->accessToken;
            return response([
                'message' => 'Successfully Login as member',
                'id' => $member['id_member'], 
                'token'=> $token
            ], 200);
        }else if($pegawai = pegawai::where('email',$request->email)->first()){
            $loginPegawai = pegawai::where('email', '=',$request['email'])->first();

            if(Hash::check($request['password'], $loginPegawai['password'])){
                $pegawai = pegawai::where('email', '=',$request['email'])->first();
            }else{
                return response()->json(['success' => false, 'message' => 'Invalid Password or Email'], 400);
            }

            // $token = bcrypt(uniqid());
            // $token = $member->createToken('Authentication Token')->accessToken;
            return response([
                'message' => 'Successfully Login as pegawai',
                'success' => true,
                'id' => $pegawai['id_pegawai'], 
                'role' => $pegawai->id_role,
                // 'token'=> $token
            ], 200);
        }else if($instruktur = instruktur::where('email',$request->email)->first()){
            $loginInstruktur = instruktur::where('email', '=',$request['email'])->first();

            if(Hash::check($request['password'], $loginInstruktur['pass'])){
                $instruktur = instruktur::where('email', '=',$request['email'])->first();
            }else{
                return response(['message' => 'Invalid Password or Email'], 404);
            }

            $token = bcrypt(uniqid());
            // $token = $member->createToken('Authentication Token')->accessToken;
            return response([
                'message' => 'Successfully Login as instruktur',
                'id' => $instruktur['id_instruktur'], 
                'token'=> $token
            ], 200);
        }else{
            return response([
                'message' => 'Gagal login',
                'data' => null, 
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'success' => true,
            'message' => 'Success Logout',
        ], 200);
    }
}
