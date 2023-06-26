<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\perizinan;
use App\Models\jadwal_harian;
use App\Models\instruktur;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PerizinanController extends Controller
{
    public function index(){
        $perizinan = DB::select(
            'SET lc_time_names = "id_ID";'
        );
        $perizinan = DB::select(
            'SELECT a.*, b.nama_instruktur as nama, i.nama_instruktur as nama_pengganti, 
                    b.*, c.jam_mulai, c.jam_selesai, c.hari, d.nama_kelas, d.*, DATE_FORMAT(c.tanggal, "%d %M %Y") as tanggal, DATE_FORMAT(a.tgl_izin, "%d %M %Y") as tgl_izin FROM perizinans a
            join instrukturs b
            on a.id_instruktur = b.id_instruktur
            join instrukturs i
            on a.id_instruktur_pengganti = i.id_instruktur
            join jadwal_harians c
            on a.id_jadwalH = c.id_jadwalH
            join kelas d
            on c.id_kelas = d.id_kelas');

        if(count($perizinan)>0){
            return response([
                'message' => 'Retrieve All Success',
                'data' =>$perizinan
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' =>null
        ], 400);
    }

    public function show($id_perizinan){
        $perizinan = perizinan::find($id_perizinan);
        $jadwalH = jadwal_harian::all();
        $instruktur = instruktur::all();

        if(!is_null($perizinan)){
            return response([
                'message' => 'Retrieve perizinan Success',
                'data' => $perizinan
            ], 200);
        }

        return response([
            'message' => 'perizinan not found',
            'data' => null
        ], 400);
    }

    public function show_izin(){
        $perizinan = DB::select(
            'SELECT a.*, b.nama_instruktur as nama, i.nama_instruktur as nama_pengganti, 
                    b.*, c.jam_mulai, c.jam_selesai, c.hari, d.nama_kelas, d.*, c.tanggal FROM perizinans a
            join instrukturs b
            on a.id_instruktur = b.id_instruktur
            join instrukturs i
            on a.id_instruktur_pengganti = i.id_instruktur
            join jadwal_harians c
            on a.id_jadwalH = c.id_jadwalH
            join kelas d
            on c.id_kelas = d.id_kelas
            where a.status = "Belum dikonfirmasi";');

        if(!is_null($perizinan)){
            return response([
                'message' => 'Retrieve perizinan Success',
                'data' => $perizinan
            ], 200);
        }
            
        return response([
            'message' => 'Tidak ada yang izin',
            'data' => null
        ], 400);
    }

    public function konfirmasi($id_perizinan){
        $perizinan = perizinan::find($id_perizinan);

        $instruktur = instruktur::find($perizinan->id_instruktur);
        $nama_instruktur = $instruktur->nama_instruktur;


        $idJadwalH = $perizinan->id_jadwalH;
        $jadwalH = jadwal_harian::find($idJadwalH);

        if($perizinan->status == "Belum dikonfirmasi"){
            $perizinan->status = "Telah Dikonfirmasi";
            $perizinan->save();
            $jadwalH->id_instruktur = $perizinan->id_instruktur_pengganti;
            $jadwalH->save();
            $jadwalH->status = "Menggantikan ". $nama_instruktur;
            $jadwalH->save();
        }

        return response([
            'message' => 'Retrieve perizinan Success',
            'data' => $perizinan
        ], 200);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_jadwalH' => 'required',
            'id_instruktur' => 'required',
            'id_instruktur_pengganti' => 'required',
            'keterangan' => 'required',
        ]);

        if($validate->fails()){
            return response(['message' => $validate->errors()], 400);
        }

        $last = DB::table('perizinans')->latest()->first();
        if($last == null){
            $increment = 1;
        }else{
            $increment = ((int)Str::substr($last->id_perizinan, 4,3)) + 1;
        }

        if($increment < 10){
            $increment = '00'.$increment;
        }else if($increment < 100){
            $increment = '0'.$increment;
        }

        $tgl = Carbon::now();
        $tglIzin = $tgl->toDateString();

        $instruktur = instruktur::find($request->id_instruktur_pengganti);
        $nama_instruktur = $instruktur->nama_instruktur;

        $perizinan = perizinan::create([
            'id_perizinan' => 'IZN'.'-'.$increment,
            'id_jadwalH' => $request->id_jadwalH,
            'id_instruktur' => $request->id_instruktur,
            'id_instruktur_pengganti' => $request->id_instruktur_pengganti,
            'status' => "Belum dikonfirmasi",
            'keterangan' => $request->keterangan,
            'tgl_izin' => $tglIzin,
        ]);

        $storeData = perizinan::latest()->first();

        return response([
            'message' => 'Data Added',
            'data' => $storeData
        ], 200);
    }

    public function showInstruktur($id_instruktur){
        $izin_instruktur = DB::select(
            'SELECT a.*, a.status as status_perizinan, b.nama_instruktur as nama, i.nama_instruktur as nama_pengganti, 
            b.*, c.*, d.* FROM perizinans a
            join instrukturs b
            on a.id_instruktur = b.id_instruktur
            join instrukturs i
            on a.id_instruktur_pengganti = i.id_instruktur
            join jadwal_harians c
            on a.id_jadwalH = c.id_jadwalH
            join kelas d
            on c.id_kelas = d.id_kelas
            where a.id_instruktur = "'.$id_instruktur.'";');

        if(!is_null($izin_instruktur)){
            return response([
                'message' => 'Retrieve perizinan Success',
                'data' => $izin_instruktur
            ], 200);
        }

        return response([
            'message' => 'perizinan not found',
            'data' => null
        ], 400);
    }
}
