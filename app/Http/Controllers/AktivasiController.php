<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\aktivasi_tahunan;
use App\Models\member;
use App\Models\pegawai;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AktivasiController extends Controller
{
    public function index(){
        $at = aktivasi_tahunan::with('member', 'pegawai')->latest()->get();
        $member = member::latest()->get();
        $pegawai = pegawai::latest()->get();
        $at = $at->map(function($item) {
            $item->tgl_transaksi = date('d M Y', strtotime($item->tgl_transaksi));
            $item->tgl_exp = date('d M Y', strtotime($item->tgl_exp)); // Mengubah format tanggal menjadi tanggal Indonesia
            return $item;
        });

        if(count($at)>0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $at
            ], 200);
        }
        
        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function show($id_aktivasi){
        $at= aktivasi_tahunan::find($id_aktivasi);
        $member = member::all();
        $pegawai = pegawai::all();

        if(!is_null($at)){
            return response([
                'message' => 'Retrieve Jadwal Umum Success',
                'data' => $at
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
            'id_pegawai' => 'required',
            'id_member' => 'required',          
        ]);

        if($validate->fails()){
            return response()->json($validate->errors(), 422);
        }

        $last = DB::table('aktivasi_tahunans')->latest()->first();
        if($last == null){
            $increment = 1;
        }else{
            $increment = ((int)Str::substr($last->no_strukA, 6,3)) + 1;
        }

        if($increment < 10){
            $increment = '00'.$increment;
        }else if($increment < 100){
            $increment = '0'.$increment;
        }

        $tgl = Carbon::now();
        $tglBeli = $tgl->toDateString();

        $curdate = $tglBeli;
        $month = Str::substr($curdate, 5, 2); // 2023-01-02
        $year = Str::substr($curdate, 2, 2);
        Str::substr($year, -2);

        $beli = Carbon::parse($tglBeli);
        $exp = $beli->addYear();

        $at = aktivasi_tahunan::create([
            'id_aktivasi' => 'A-'.$increment,
            'no_strukA' => $year.'.'.$month.'.'.$increment,
            'id_pegawai' => $request->id_pegawai,
            'id_member' => $request->id_member,
            'tgl_transaksi' => $tglBeli,
            'tgl_exp' => $exp,
        ]);
        
        $idMember = $request->id_member;
        $member = member::find($idMember);
        $member->tgl_exp = $at->tgl_exp;
        $member->status = 1;
        $member->tgl_pembuatan = $at->tgl_transaksi;
        $member->save();

        $storeData = aktivasi_tahunan::latest()->first();

        return response([
            'message' => 'Data Added',
            'data' => $storeData
        ], 200);
    }
}
