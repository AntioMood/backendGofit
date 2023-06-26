<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\jadwal_umum;
use App\Models\instruktur;
use App\Models\kelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class JadwalUController extends Controller
{
    public function index(){
        $jadwalU = jadwal_umum::with('kelas', 'instruktur')->latest()->get();
        $kelas = kelas::latest()->get();
        $instruktur = instruktur::latest()->get();

        if(count($jadwalU)>0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $jadwalU
            ], 200);
        }
        
        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function show($id_jadwalU){
        $jadwalU= jadwal_umum::find($id_jadwalU);
        $kelas = kelas::all();
        $instruktur = instruktur::all();

        if(!is_null($jadwalU)){
            return response([
                'message' => 'Retrieve Jadwal Umum Success',
                'data' => $jadwalU
            ], 200);
        }

        return response([
            'message' => 'Jadwal not found',
            'data' => null
        ], 400);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_instruktur' => 'required',
            'id_kelas' => 'required',
            'hari' => 'required',            
            'jam_mulai' => 'required',
        ]);

        if($validate->fails()){
            return response()->json($validate->errors(), 422);
        }
        
        $data = jadwal_umum::all();
        foreach($data as $datas){
            if( $datas->id_instruktur == $request->id_instruktur&&
                $datas->hari == $request->hari &&
                $datas->jam_mulai == $request->jam_mulai){
                    return response([
                       'message' => 'Jadwal terdaftar',
                        'data' => null
                    ], 400);
            }
        }     

        $last = DB::table('jadwal_umums')->latest()->first();
        if($last == null){
            $increment = 1;
        }else{
            $increment = ((int)Str::substr($last->id_jadwalU, 2,3)) + 1;
        }

        if($increment < 10){
            $increment = '00'.$increment;
        }else if($increment < 100){
            $increment = '0'.$increment;
        }

        $mulai = Carbon::parse($request->jam_mulai);
        $selesai = $mulai->addMinutes(90);

        $jadwalU = jadwal_umum::create([
            'id_jadwalU' => 'JU'.$increment,
            'id_instruktur' => $request->id_instruktur,
            'id_kelas' => $request->id_kelas,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $selesai
        ]);

        $storeData = jadwal_umum::latest()->first();

        return response([
            'message' => 'Data Added',
            'data' => $storeData
        ], 200);
    }

    public function destroy($id_jadwalU){
        $jadwalU = jadwal_umum::find($id_jadwalU);

        if(is_null($jadwalU)){
            return response([
                'message' => 'Data Not Found',
                'data' => null
            ], 404);
        }

        if($jadwalU->delete()){
            return response([
                'message' => 'Data Deleted',
                'data' => $jadwalU
            ], 200);
        }

        return response([
            'message' => 'Delete Data Failed',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $id_jadwalU){
        $jadwalU = jadwal_umum::find($id_jadwalU);

        if(is_null($jadwalU)){
            return response([
                'message' => 'Data Not Found',
                'data' => null
            ], 404);
        }

        $updateData  = $request->all();
        $validate = Validator::make($updateData, [
            'id_instruktur' => 'required',
            'id_kelas' => 'required',
            'hari' => 'required',            
            'jam_mulai' => 'required',
        ]);

        if($validate->fails()){
            return response()->json($validate->errors(), 422);
        }

        $data = jadwal_umum::all();
        foreach($data as $data){
            if( $data->id_instruktur == $request->id_instruktur&&
                $data->id_kelas == $request->id_kelas &&
                $data->hari == $request->hari &&
                $data->jam_mulai == $request->jam_mulai){
                    return response([
                       'message' => 'Jadwal terdaftar',
                        'data' => null
                    ], 400);
            }
        } 
        
        $mulai = Carbon::parse($request->jam_mulai);
        $selesai = $mulai->addMinutes(90);

        $jadwalU->id_instruktur = $updateData['id_instruktur'];
        $jadwalU->id_kelas = $updateData['id_kelas'];
        $jadwalU->hari = $updateData['hari'];
        $jadwalU->jam_mulai = $updateData['jam_mulai'];
        $jadwalU->jam_selesai = $selesai->toTimeString();

        if($jadwalU->save()){
            return response([
                'message' => 'Update Success',
                'data' => $jadwalU
            ], 200);
        }

        return response([
            'message' => 'Update Failed',
            'data' => null
        ], 400);
    }
}
