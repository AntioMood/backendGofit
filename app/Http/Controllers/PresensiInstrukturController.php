<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\jadwal_harian;
use App\Models\jadwal_umum;
use App\Models\instruktur;
use App\Models\kelas;
use App\Models\presensi_instruktur;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PresensiInstrukturController extends Controller
{
    public function showSchadule(){
        $today = Carbon::today();

        // $today = '2023-05-23';

        $jadwal = DB::select(
            'SELECT a.* from jadwal_harians a
            join jadwal_umums b
            on a.id_jadwalU = b.id_jadwalU
            join kelas k
            on b.id_kelas = k.id_kelas
            where a.tanggal = "' .$today. '";'
        );

        return response([
            'message' => 'Retrieve Jadwal dan kelas Success',
            'data' => $jadwal
        ], 200);
    }

    public function store(Request $request, $id_jadwalH){
        $storeData = $request->all();

        $jadwal = DB::select(
            'SELECT a.*, i.*, i.jumlah_terlambat, a.jam_mulai from jadwal_harians a
            join jadwal_umums b
            on a.id_jadwalU = b.id_jadwalU
            join kelas k
            on b.id_kelas = k.id_kelas
            join instrukturs i
            on b.id_instruktur = i.id_instruktur
            where a.id_jadwalH = "' .$id_jadwalH. '";'
        );

        $last = DB::table('presensi_instrukturs')->latest()->first();
        if($last == null){
            $increment = 1;
        }else{
            $increment = ((int)Str::substr($last->id_presensi_instruktur, 3,3)) + 1;
        }

        if($increment < 10){
            $increment = '00'.$increment;
        }else if($increment < 100){
            $increment = '0'.$increment;
        }

        // $tgl = Carbon::now()->toDateString();
        $tgl = '2023-06-01';

        // $jamSekarang = Carbon::now();
        $jamSekarang = Carbon::parse('08:15:00');

        $jamJadwal = Carbon::parse($jadwal[0]->jam_mulai);

        if($jamSekarang > $jamJadwal){
            $presensi_instruktur = presensi_instruktur::create([
                'id_presensi_instruktur' => 'PI'.'-'.$increment,
                'id_jadwalH' => $request->id_jadwalH,
                'jam_mulai' => $jamSekarang,
                'jam_selesai' => null,
                'tgl_presensi' => $tgl,
            ]);

            $waktu_terlambat = $jamSekarang->diff(Carbon::parse($jamJadwal));
            $keterlambatanInstruktur = Carbon::parse($jadwal[0]->jumlah_terlambat);

            $hours = $waktu_terlambat->h;
            $minutes = $waktu_terlambat->i;
            $second = $waktu_terlambat->s;

            $totalKeterlambatan = $keterlambatanInstruktur->addHours($hours)->addMinutes($minutes)->addSeconds($second);
            $hasilKeterlambatan = $totalKeterlambatan->toTimeString();

            DB::table('instrukturs')->where('id_instruktur', $jadwal[0]->id_instruktur)->update(['jumlah_terlambat'=> $hasilKeterlambatan]);
            
        }else{
            $presensi_instruktur = presensi_instruktur::create([
                'id_presensi_instruktur' => 'PI'.'-'.$increment,
                'id_jadwalH' => $request->id_jadwalH,
                'jam_mulai' => $jamSekarang,
                'jam_selesai' => null,
                'tgl_presensi' => $tgl,
            ]);
        }

        return response([
            'message' => 'Data Added',
            'data' => $presensi_instruktur
        ], 200);
    }

    public function jamSelesai($id_presensi_instruktur){
        // $jam_selesai = Carbon::now()->toTimeString();
        $jam_selesai = '09:30:00';

        $presensi = presensi_instruktur::find($id_presensi_instruktur);
        $presensi->jam_selesai = $jam_selesai;
        $presensi->save();

        return response([
            'message' => 'Data Added',
            'data' => $presensi
        ], 200);
    }

}

