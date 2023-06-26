<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\transaksi_deposit_kelas;
use App\Models\member;
use App\Models\pegawai;
use App\Models\promoKelas;
use App\Models\kelas;
use App\Models\deposit_kelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransaksiDepoK extends Controller
{
    public function index(){
        $dk = transaksi_deposit_kelas::with('member', 'pegawai', 'promoK', 'kelas')->latest()->get();
        $member = member::latest()->get();
        $pegawai = pegawai::latest()->get();
        $promoK = promoKelas::latest()->get();
        $kelas = kelas::latest()->get();

        $dk = $dk->map(function($item) {
            $item->tgl_transaksi = date('d M Y', strtotime($item->tgl_transaksi));
            $item->tgl_exp = date('d M Y', strtotime($item->tgl_exp)); // Mengubah format tanggal menjadi tanggal Indonesia
            return $item;
        });

        if(count($dk)>0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $dk
            ], 200);
        }
        
        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function show($id_TdepoK){
        $dk= aktivasi_tahunan::find($id_TdepoK);
        $member = member::all();
        $pegawai = pegawai::all();
        $promoK = promoKelas::all();
        $kelas = kelas::all();

        if(!is_null($dk)){
            return response([
                'message' => 'Retrieve Jadwal Umum Success',
                'data' => $dk
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
            'id_kelas' => 'required',
            'depoK' => 'required',         
        ]);

        if($validate->fails()){
            return response()->json($validate->errors(), 422);
        }

        $last = DB::table('transaksi_deposit_kelas')->latest()->first();
        if($last == null){
            $increment = 1;
        }else{
            $increment = ((int)Str::substr($last->no_strukK, 6,3)) + 1;
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
        $idKelas = $request->id_kelas;
        $kelas = kelas::find($idKelas);
        $tdkPromo = promoKelas::where('id_promoK', '1')->first();
        $promo1 = promoKelas::where('id_promoK', '2')->first();
        $promo2 = promoKelas::where('id_promoK', '3')->first();

        $syaratPromo1 = $promo1->syarat;
        $bonusPromo1 = $promo1->bonus;

        $syaratPromo2 = $promo2->syarat;
        $bonusPromo2 = $promo2->bonus;

        $tgl = Carbon::now();
        $tglSekarang = Carbon::parse($tgl);

        $deposit_kelas = deposit_kelas::where('id_member', $request->id_member)
                                        ->where('id_kelas', $request->id_kelas)
                                        ->first();

        if(is_null($deposit_kelas)){
            if($request->depoK < 5){
                if($request->id_kelas == $kelas->id_kelas){
                    $bonus = $tdkPromo->bonus;
                    $totalDepoK = $request->depoK;
                    $totalBayar = $request->depoK * $kelas->harga;
                    $exp = $tgl->addMonth()->toDateString();
    
                $dk = transaksi_deposit_kelas::create([
                        'id_TdepoK' => 'DK-'.$increment,
                        'no_strukK' => $year.'.'.$month.'.'.$increment,
                        'id_pegawai' => $request->id_pegawai,
                        'id_member' => $request->id_member,
                        'id_kelas' => $request->id_kelas,
                        'id_promoK' => 1,
                        'tgl_transaksi' => $tglBeli,
                        'tgl_exp' => $exp,
                        'depoK' => $request->depoK,
                        'totalBayar' => $totalBayar,
                        'totalDepoK' => $totalDepoK,
                        'bonus' => $bonus
                    ]);
                }
            }else if($request->depoK >= 5 && $request->depoK < 10){
                if($request->id_kelas == $kelas->id_kelas){
                    $bonus1 = $promo1->bonus;
                    $totalDepoK = $request->depoK + $bonus1;
                    $totalBayar = $request->depoK * $kelas->harga;
                    $exp = $tgl->addMonth()->toDateString();
    
                $dk = transaksi_deposit_kelas::create([
                        'id_TdepoK' => 'DK-'.$increment,
                        'no_strukK' => $year.'.'.$month.'.'.$increment,
                        'id_pegawai' => $request->id_pegawai,
                        'id_member' => $request->id_member,
                        'id_kelas' => $request->id_kelas,
                        'id_promoK' => 2,
                        'tgl_transaksi' => $tglBeli,
                        'tgl_exp' => $exp,
                        'depoK' => $request->depoK,
                        'totalBayar' => $totalBayar,
                        'totalDepoK' => $totalDepoK,
                        'bonus' => $bonus1
                    ]);
                }
            }else if($request->depoK >= 10){
                if($request->id_kelas == $kelas->id_kelas){
                    $bonus2 = $promo2->bonus;
                    $totalDepoK = $request->depoK + $bonus2;
                    $totalBayar = $request->depoK * $kelas->harga;
                    $exp = $tgl->addMonth(2)->toDateString();
    
                $dk = transaksi_deposit_kelas::create([
                        'id_TdepoK' => 'DK-'.$increment,
                        'no_strukK' => $year.'.'.$month.'.'.$increment,
                        'id_pegawai' => $request->id_pegawai,
                        'id_member' => $request->id_member,
                        'id_kelas' => $request->id_kelas,
                        'id_promoK' => 3,
                        'tgl_transaksi' => $tglBeli,
                        'tgl_exp' => $exp,
                        'depoK' => $request->depoK,
                        'totalBayar' => $totalBayar,
                        'totalDepoK' => $totalDepoK,
                        'bonus' => $bonus2
                    ]);
                }
            }
        }else if($deposit_kelas->deposit_kelas < 1 || $deposit_kelas->tgl_exp < $tglSekarang){
            if($request->depoK < 5){
                if($request->id_kelas == $kelas->id_kelas){
                    $bonus = $tdkPromo->bonus;
                    $totalDepoK = $request->depoK;
                    $totalBayar = $request->depoK * $kelas->harga;
                    $exp = $tgl->addMonth()->toDateString();
    
                $dk = transaksi_deposit_kelas::create([
                        'id_TdepoK' => 'DK-'.$increment,
                        'no_strukK' => $year.'.'.$month.'.'.$increment,
                        'id_pegawai' => $request->id_pegawai,
                        'id_member' => $request->id_member,
                        'id_kelas' => $request->id_kelas,
                        'id_promoK' => 1,
                        'tgl_transaksi' => $tglBeli,
                        'tgl_exp' => $exp,
                        'depoK' => $request->depoK,
                        'totalBayar' => $totalBayar,
                        'totalDepoK' => $totalDepoK,
                        'bonus' => $bonus
                    ]);
                }
            }else if($request->depoK >= 5 && $request->depoK < 10){
                if($request->id_kelas == $kelas->id_kelas){
                    $bonus1 = $promo1->bonus;
                    $totalDepoK = $request->depoK + $bonus1;
                    $totalBayar = $request->depoK * $kelas->harga;
                    $exp = $tgl->addMonth()->toDateString();
    
                $dk = transaksi_deposit_kelas::create([
                        'id_TdepoK' => 'DK-'.$increment,
                        'no_strukK' => $year.'.'.$month.'.'.$increment,
                        'id_pegawai' => $request->id_pegawai,
                        'id_member' => $request->id_member,
                        'id_kelas' => $request->id_kelas,
                        'id_promoK' => 2,
                        'tgl_transaksi' => $tglBeli,
                        'tgl_exp' => $exp,
                        'depoK' => $request->depoK,
                        'totalBayar' => $totalBayar,
                        'totalDepoK' => $totalDepoK,
                        'bonus' => $bonus1
                    ]);
                }
            }else if($request->depoK >= 10){
                if($request->id_kelas == $kelas->id_kelas){
                    $bonus2 = $promo2->bonus;
                    $totalDepoK = $request->depoK + $bonus2;
                    $totalBayar = $request->depoK * $kelas->harga;
                    $exp = $tgl->addMonth(2)->toDateString();
    
                $dk = transaksi_deposit_kelas::create([
                        'id_TdepoK' => 'DK-'.$increment,
                        'no_strukK' => $year.'.'.$month.'.'.$increment,
                        'id_pegawai' => $request->id_pegawai,
                        'id_member' => $request->id_member,
                        'id_kelas' => $request->id_kelas,
                        'id_promoK' => 3,
                        'tgl_transaksi' => $tglBeli,
                        'tgl_exp' => $exp,
                        'depoK' => $request->depoK,
                        'totalBayar' => $totalBayar,
                        'totalDepoK' => $totalDepoK,
                        'bonus' => $bonus2
                    ]);
                }
            }
        }else{
            return response([
                'message' => 'deposit kelas yang anda pilih masih ada atau tanggal deposit kelas sudah expired ',
                'data' => null
            ], 400);
        }                                

        // $member->deposit_kelas = $dk->totalDepoK;
        $member->deposit_uang = $member->deposit_uang - $dk->totalBayar;
        $member->save();

        $data = [
            'id_member' => $dk->id_member,
            'id_kelas' => $dk->id_kelas,
            'deposit_kelas' => $dk->totalDepoK,
            'tgl_exp' => $dk->tgl_exp,
            'created_at' => $dk->created_at,
            'updated_at' => $dk->updated_at
        ];
        DB::table('deposit_kelas')->insert($data);
        
        $storeData = transaksi_deposit_kelas::latest()->first();

        return response([
            'message' => 'Data Added',
            'data' => $storeData
        ], 200);
    
    }

    public function showTglExp(){
        // // $depok = transaksi_deposit_kelas::where($dk->tgl_exp, '<=', Carbon::today())->get();
        // $dk = transaksi_deposit_kelas::with('member', 'pegawai', 'promoK', 'kelas')->latest()->get();
        // $member = member::latest()->get();
        // $pegawai = pegawai::latest()->get();
        // $promoK = promoKelas::latest()->get();
        // $kelas = kelas::latest()->get();

        $tgl = Carbon::today();

        $depok = DB::select('SELECT a.*, b.*, c.*, d.*, e.* FROM transaksi_deposit_kelas a
            join members b
            on a.id_member = b.id_member
            join pegawais c
            on a.id_pegawai = c.id_pegawai
            join promo_kelas d
            on a.id_promoK = d.id_promoK
            join kelas e
            on a.id_kelas = e.id_kelas
            where a.tgl_exp <= "'.$tgl.'";');

        if(!is_null($depok)){
            return response([
                'message' => 'Retrieve Member Success',
                'data' => $depok
            ], 200);
        }

        return response([
            'message' => 'Member not found',
            'data' => null
        ], 400);
    }

    public function resetDepoK($id_TdepoK){
        $tdepok = transaksi_deposit_kelas::find($id_TdepoK);
        // $members = member::where('tgl_exp', '<=', Carbon::now()->toDatestring())->get();
        $idMember = $tdepok->id_member;
        $member = member::find($idMember);

        if($tdepok->tgl_exp <= Carbon::now()->toDatestring()){
            $tdepok->totalDepoK = 0;
            $tdepok->tgl_exp = null;
            $tdepok->save();
            $member->deposit_kelas = $tdepok->totalDepoK;
            $member->save();
        }   

        return response([
            'message' => 'Retrieve Customer Success',
            'data' => $member
        ], 200);
    }

}
