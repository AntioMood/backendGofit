<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\kelas;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class KelasController extends Controller
{
    public function index(){
        $kelas = kelas::all();

        if(count($kelas)>0){
            return response([
                'message' => 'Retrieve All Success',
                'data' =>$kelas
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' =>null
        ], 400);
    }

    public function show($id_kelas){
        $kelas = kelas::find($id_kelas);

        if(!is_null($kelas)){
            return response([
                'message' => 'Retrieve Customer Success',
                'data' => $kelas
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
            'nama_kelas' => 'required',
            'kuantitas_kelas' => 'required',
            'harga' => 'required',
        ]);

        if($validate->fails()){
            return response(['message' => $validate->errors()], 400);
        }
        
        $last = DB::table('kelas')->latest()->first();
        if($last == null){
            $increment = 1;
        }else{
            $increment = ((int)Str::substr($last->id_kelas, 3,3)) + 1;
        }

        if($increment < 10){
            $increment = '00'.$increment;
        }else if($increment < 100){
            $increment = '0'.$increment;
        }

        $kelas = kelas::create([
            'id_kelas' => 'KL'.'-'.$increment,
            'nama_kelas' => $request->nama_kelas,
            'kuantitas_kelas' => $request->kuantitas_kelas,
            'harga' => $request->harga,
        ]);

        return response([
            'message' => 'Data Added',
            'data' => $kelas
        ], 200);
    }

    public function destroy($id_kelas){
        $kelas = kelas::find($id_kelas);

        if(is_null($kelas)){
            return response([
                'message' => 'Data Not Found',
                'data' => null
            ], 404);
        }

        if($kelas->delete()){
            return response([
                'message' => 'Data Deleted',
                'data' => $kelas
            ], 200);
        }

        return response([
            'message' => 'Delete Data Failed',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $id_kelas){
        $kelas = kelas::find($id_kelas);

        if(is_null($kelas)){
            return response([
                'message' => 'Data Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'nama_kelas' => 'required',
            'kuantitas_kelas' => 'required',
            'harga' => 'required'
        ]);

        if($validate->fails()){
            return response(['message' => $validate->errors()], 400);
        }

        $kelas->nama_kelas = $updateData['nama_kelas'];
        $kelas->kuantitas_kelas = $updateData['kuantitas_kelas'];
        $kelas->harga = $updateData['harga'];

        if($kelas->save()){
            return response([
                'message' => 'Update Success',
                'data' => $kelas
            ], 200);
        }

        return response([
            'message' => 'Update Failed',
            'data' => null
        ], 400);
    }
}
