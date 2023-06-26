<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\transaksi_deposit_uang;
use App\Models\member;
use App\Models\pegawai;
use App\Models\promo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransaksiDepoU extends Controller
{
    public function index(){
        $du = transaksi_deposit_uang::with('member', 'pegawai', 'promo')->latest()->get();
        $member = member::latest()->get();
        $pegawai = pegawai::latest()->get();
        $promo = promo::latest()->get();

        $du = $du->map(function($item) {
            $item->tgl_transaksi = date('d M Y', strtotime($item->tgl_transaksi)); // Mengubah format tanggal menjadi tanggal Indonesia
            return $item;
        });

        if(count($du)>0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $du
            ], 200);
        }
        
        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function show($id_TdepoU){
        $du= aktivasi_tahunan::find($id_TdepoU);
        $member = member::all();
        $pegawai = pegawai::all();
        $promo = promo::all();

        if(!is_null($du)){
            return response([
                'message' => 'Retrieve Jadwal Umum Success',
                'data' => $du
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
            'depoU' => 'required',           
        ]);

        if($validate->fails()){
            return response()->json($validate->errors(), 422);
        }

        $last = DB::table('transaksi_deposit_uangs')->latest()->first();
        if($last == null){
            $increment = 1;
        }else{
            $increment = ((int)Str::substr($last->no_strukU, 6,3)) + 1;
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
        
        $idMember = $request->id_member;
        $member = member::find($idMember);
        $promoU = promo::where('id_promo', '2')->first();

        $syaratPromo = $promoU->syarat;
        $bonusPromo = $promoU->bonus;

        if($request->depoU < 500000){
            return response([
                'message' => 'Minimal Deposit Sebesar Rp 500.000',
                'data' => null
            ], 400);
        }else{
            if($request->depoU < $syaratPromo){
                $bonus = 0;
                $total = $member->deposit_uang + $request->depoU + $bonus;
                $du = transaksi_deposit_uang::create([
                    'id_TdepoU' => 'DU-'.$increment,
                    'no_strukU' => $year.'.'.$month.'.'.$increment,
                    'id_pegawai' => $request->id_pegawai,
                    'id_member' => $request->id_member,
                    'id_promo' => 1,
                    'tgl_transaksi' => $tglBeli,
                    'depoU' => $request->depoU,
                    'totalDepoU' => $total,
                    'bonus' => $bonus,
                    'sisa' => $member->deposit_uang
                ]);
            }else if($request->depoU >= $syaratPromo ){
                $totalU = $member->deposit_uang + $bonusPromo + $request->depoU;
                
                $du = transaksi_deposit_uang::create([
                    'id_TdepoU' => 'DU-'.$increment,
                    'no_strukU' => $year.'.'.$month.'.'.$increment,
                    'id_pegawai' => $request->id_pegawai,
                    'id_member' => $request->id_member,
                    'id_promo' => 2,
                    'tgl_transaksi' => $tglBeli,
                    'depoU' => $request->depoU,
                    'totalDepoU' => $totalU,
                    'bonus'=> $bonusPromo,
                    'sisa' => $member->deposit_uang
                ]);
            }
        }
        
        $member->deposit_uang = $du->totalDepoU;
        $member->save();

        $storeData = transaksi_deposit_uang::latest()->first();

        return response([
            'message' => 'Data Added',
            'data' => $storeData
        ], 200);
    
    }
}
