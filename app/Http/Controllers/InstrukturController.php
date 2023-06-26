<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\instruktur;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

class InstrukturController extends Controller
{
    public function index(){
        $instruktur = instruktur::all();

        if(count($instruktur)>0){
            return response([
                'message' => 'Retrieve All Success',
                'data' =>$instruktur
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' =>null
        ], 400);
    }

        public function show($id_instruktur){
            $instruktur = DB::select(
                'SET lc_time_names = "id_ID";'
            );
            $instruktur = DB::selectOne(
                'SELECT *, DATE_FORMAT(tgl_lahir, "%d %M %Y") as tgl_lahir FROM instrukturs 
                WHERE id_instruktur = "'. $id_instruktur.'";');

            if(!is_null($instruktur)){
                $timeParts = explode(':', $instruktur->jumlah_terlambat);
                $hours = intval($timeParts[0]);
                $minutes = intval($timeParts[1]);
                $seconds = intval($timeParts[2]);

                $totalSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;
                $instruktur->total_terlambat = $totalSeconds;
                return response([
                    'message' => 'Retrieve Customer Success',
                    'data' => $instruktur
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
            'nama_instruktur' => 'required',
            'jenis_kelamin' => 'required',
            'tgl_lahir' => 'required|date_format:Y-m-d',
            'no_telp' => 'required',
            'email' => 'required|email',
            'pass' => 'required',

        ]);

        if($validate->fails()){
            return response()->json($validate->errors(), 422);
        }
        
        
        // $count = DB::table('instrukturs')->count() +1;
        // $id_generate = sprintf("%03d", $count);
        $last = DB::table('instrukturs')->latest()->first();
        if($last == null){
            $increment = 1;
        }else{
            $increment = ((int)Str::substr($last->id_instruktur, 4,3)) + 1;
        }

        if($increment < 10){
            $increment = '00'.$increment;
        }else if($increment < 100){
            $increment = '0'.$increment;
        }

        $instruktur = Instruktur::create([
            'id_instruktur' => 'INS'.'-'.$increment,
            'nama_instruktur' => $request->nama_instruktur,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tgl_lahir' => $request->tgl_lahir,
            'no_telp' => $request->no_telp,
            'email' => $request->email,
            'pass' => Hash::make($request->pass)
        ]);

        return response([
            'message' => 'Data Added',
            'data' => $instruktur
        ], 200);
    }

    public function destroy($id_instruktur){
        $instruktur = instruktur::find($id_instruktur);

        if(is_null($instruktur)){
            return response([
                'message' => 'Data Not Found',
                'data' => null
            ], 404);
        }

        if($instruktur->delete()){
            return response([
                'message' => 'Data Deleted',
                'data' => $instruktur
            ], 200);
        }

        return response([
            'message' => 'Delete Data Failed',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $id_instruktur){
        $instruktur = instruktur::find($id_instruktur);

        if(is_null($instruktur)){
            return response([
                'message' => 'Data Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'nama_instruktur' => 'required',
            'jenis_kelamin' => 'required',
            'tgl_lahir' => 'required|date_format:Y-m-d',
            'no_telp' => 'required',
            'email' => 'required|email',
            // 'pass' => 'required',
        ]);

        if($validate->fails()){
            return response()->json($validate->errors(), 422);
        }

        $instruktur->nama_instruktur = $updateData['nama_instruktur'];
        $instruktur->jenis_kelamin = $updateData['jenis_kelamin'];
        $instruktur->tgl_lahir = $updateData['tgl_lahir'];
        $instruktur->no_telp = $updateData['no_telp'];
        $instruktur->email = $updateData['email'];
        $instruktur->pass = Hash::make($updateData['pass']);

        if($instruktur->save()){
            return response([
                'message' => 'Update Success',
                'data' => $instruktur
            ], 200);
        }

        return response([
            'message' => 'Update Failed',
            'data' => null
        ], 400);
    }

    public function reset(){
        $instruktur = instruktur::all(); 
        
        foreach ($instruktur as $presensi) {
            // $today = Carbon::now()->toDateString();
            $today = '2023-06-01';
            if ($today == Carbon::now()->startOfMonth()->toDateString()) {
                $presensi->jumlah_terlambat = '00:00:00';
                $presensi->save();
            }else{}
        }
        return response([
            'message' => 'set total terlambat instruktur 0 detik',
            'data' => $instruktur
        ], 200);
    }
}
