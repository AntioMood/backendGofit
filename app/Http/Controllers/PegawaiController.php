<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\pegawai;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class PegawaiController extends Controller
{
    public function index(){
        $pegawai = pegawai::with('role')->latest()->get();;

        if(count($pegawai)>0){
            return response([
                'message' => 'Retrieve All Success',
                'data' =>$pegawai
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' =>null
        ], 400);
    }

    public function show($id_pegawai){
        $pegawai = pegawai::find($id_pegawai);

        if(!is_null($pegawai)){
            return response([
                'message' => 'Retrieve Customer Success',
                'data' => $pegawai
            ], 200);
        }

        return response([
            'message' => 'Instruktur not found',
            'data' => null
        ], 400);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_role' => 'required',
            'nama_pegawai' => 'required',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'no_telp' => 'required'

        ]);

        if($validate->fails()){
            return response(['message' => $validate->errors()], 400);
        }
        
        $last = DB::table('pegawais')->latest()->first();
        if($last == null){
            $increment = 1;
        }else{
            $increment = ((int)Str::substr($last->id_pegawai, 3,3)) + 1;
        }

        if($increment < 10){
            $increment = '00'.$increment;
        }else if($increment < 100){
            $increment = '0'.$increment;
        }

        $pegawai = pegawai::create([
            'id_pegawai' => 'PG'.'-'.$increment,
            'id_role' => $request->id_role,
            'nama_pegawai' => $request->nama_pegawai,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'no_telp' => $request->no_telp
        ]);

        return response([
            'message' => 'Data Added',
            'data' => $pegawai
        ], 200);
    }

    public function destroy($id_pegawai){
        $pegawai = pegawai::find($id_pegawai);

        if(is_null($pegawai)){
            return response([
                'message' => 'Data Not Found',
                'data' => null
            ], 404);
        }

        if($pegawai->delete()){
            return response([
                'message' => 'Data Deleted',
                'data' => $pegawai
            ], 200);
        }

        return response([
            'message' => 'Delete Data Failed',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $id_pegawai){
        $pegawai = pegawai::find($id_pegawai);

        if(is_null($pegawai)){
            return response([
                'message' => 'Data Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'id_role' => 'required',
            'nama_pegawai' => 'required',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'no_telp' => 'required'
        ]);

        if($validate->fails()){
            return response(['message' => $validate->errors()], 400);
        }

        $pegawai->id_role = $updateData['id_role'];
        $pegawai->nama_pegawai = $updateData['nama_pegawai'];
        $pegawai->jenis_kelamin = $updateData['jenis_kelamin'];
        $pegawai->alamat = $updateData['alamat'];
        $pegawai->email = $updateData['email'];
        $pegawai->password = $updateData['password'];
        $pegawai->no_telp = $updateData['no_telp'];

        if($pegawai->save()){
            return response([
                'message' => 'Update Success',
                'data' => $pegawai
            ], 200);
        }

        return response([
            'message' => 'Update Failed',
            'data' => null
        ], 400);
    }
}
