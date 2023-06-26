<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\jadwal_harian;
use App\Models\jadwal_umum;
use App\Models\instruktur;
use App\Models\kelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class JadwalHController extends Controller
{
    public function index(){
        $now = Carbon::now();
        // $now = '2023-06-13';
        $startOfWeek = Carbon::parse($now)->startOfWeek();
        $endOfWeek = Carbon::parse($now)->endOfWeek();

        $jadwalH = jadwal_harian::with('kelas', 'instruktur')
                                ->whereBetween('tanggal', [$startOfWeek, $endOfWeek]) // Mengambil data hanya untuk satu minggu ini
                                ->latest()
                                ->get();

        $kelas = kelas::latest()->get();
        $instruktur = instruktur::latest()->get();
        $jadwalU = jadwal_umum::latest()->get();

        $jadwalH = $jadwalH->map(function($item) {
            $item->tanggal = date('d M Y', strtotime($item->tanggal)); // Mengubah format tanggal menjadi tanggal Indonesia
            $item->jam_mulai = substr($item->jam_mulai, 0, 5);
            return $item;
        });

        if(count($jadwalH)>0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $jadwalH
            ], 200);
        }
        
        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function show($id_jadwalH){
        $jadwalH= jadwal_harian::find($id_jadwalH);
        $kelas = kelas::all();
        $instruktur = instruktur::all();
        $jadwalU = jadwal_umum::all();

        if(!is_null($jadwalH)){
            return response([
                'message' => 'Retrieve Jadwal Umum Success',
                'data' => $jadwalH
            ], 200);
        }

        return response([
            'message' => 'Jadwal not found',
            'data' => null
        ], 400);
    }

    public function store(){

        $jadwal_harian = Jadwal_Harian::all();

        $now = Carbon::now();
        // $now = '2023-06-13';
        $startDate = Carbon::parse($now)->startOfWeek();
        $endDate = Carbon::parse($now)->endOfWeek();

        $existingJadwal = jadwal_harian::whereBetween('tanggal', [$startDate, $endDate])->exists();
        if($existingJadwal){
            return response([
                'message' => 'Jadwal untuk minggu ini sudah dibuat.',
            ], 400);
        }else{
            $jadwal_umum = Jadwal_Umum::all();

            foreach($jadwal_umum as $item){
                $storeData['id_jadwalU'] = $item->id_jadwalU;
                $storeData['id_kelas'] = $item->id_kelas;
                $storeData['id_instruktur'] = $item->id_instruktur;
                $storeData['hari'] = $item->hari;
                $storeData['jam_mulai'] = $item->jam_mulai;
                $storeData['jam_selesai'] = $item->jam_selesai;
                $storeData['status'] = "Ada kelas";

                $last = DB::table('jadwal_harians')->latest('id_jadwalH')->first();
                if ($last == null) {
                    $increment = 1;
                } else {
                    $lastId = (int)substr($last->id_jadwalH, 2);
                    $increment = $lastId + 1;
                }

                $id_jadwalH = 'JH' . str_pad($increment, 3, '0', STR_PAD_LEFT);
                $storeData['id_jadwalH'] = $id_jadwalH;
                
                $tanggalMingguan = $startDate->copy();

                if ($item->hari == 'Senin') {

                } elseif ($item->hari == 'Selasa') {
                    $tanggalMingguan->addDay();
                } elseif ($item->hari == 'Rabu') {
                    $tanggalMingguan->addDays(2);
                } elseif ($item->hari == 'Kamis') {
                    $tanggalMingguan->addDays(3);
                } elseif ($item->hari == 'Jumat') {
                    $tanggalMingguan->addDays(4);
                } elseif ($item->hari == 'Sabtu') {
                    $tanggalMingguan->addDays(5);
                } elseif ($item->hari == 'Minggu') {
                    $tanggalMingguan->addDays(6);
                }

                $storeData['tanggal'] = $tanggalMingguan;
                $jadwal_harian = Jadwal_Harian::create($storeData);         
            }
        }

        $jadwal_harian = Jadwal_Harian::all();

        return response([
            'message' => 'Generate Jadwal Harian Success',
            'data' => $jadwal_harian
        ], 200);
    }

    public function update(Request $request, $id_jadwalH){
        $jadwalH = jadwal_harian::find($id_jadwalH);

        if(is_null($jadwalH)){
            return response([
                'message' => 'Data Not Found',
                'data' => null
            ], 404);
        }
        if($jadwalH->status == "Libur"){
            $jadwalH->status = "Ada Kelas";
        }else if($jadwalH->status == "Ada Kelas"){
            $jadwalH->status = "Libur";
        }else{
            $jadwalH->status = "Ada Kelas";
        }
        

        if($jadwalH->save()){
            return response([
                'message' => 'Update Success',
                'data' => $jadwalH
            ], 200);
        }

        return response([
            'message' => 'Update Failed',
            'data' => null
        ], 400);
    }
}
