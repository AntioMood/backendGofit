<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\jadwal_harian;
use App\Models\jadwal_umum;
use App\Models\member;
use App\Models\kelas;
use App\Models\booking_kelas;
use App\Models\presensi_instruktur;
use App\Models\deposit_kelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingKelasController extends Controller
{
    public function index(){
        $booking = booking_kelas::with('member', 'jadwal_harian')->latest()->get();
        $member = member::latest()->get();
        $jadwalH = jadwal_harian::latest()->get();

        if(count($booking)>0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $booking
            ], 200);
        }
        
        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function show($id_booking){
        $booking= booking_kelas::find($id_booking);
        $member = member::all();
        $jadwalH = jadwal_harian::all();

        if(!is_null($booking)){
            return response([
                'message' => 'Retrieve Jadwal Umum Success',
                'data' => $booking
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
            'id_member' => 'required',
            'id_jadwalH' => 'required',
        ]);

        if($validate->fails()){
            return response()->json($validate->errors(), 422);
        }

        $last = DB::table('booking_kelas')->latest()->first();
        if($last == null){
            $increment = 1;
        }else{
            $increment = ((int)Str::substr($last->id_booking, 3,3)) + 1;
        }

        if($increment < 10){
            $increment = '00'.$increment;
        }else if($increment < 100){
            $increment = '0'.$increment;
        }

        $tgl = Carbon::now();
        $tglBooking = $tgl->toDateString();

        $member = member::find($request->id_member);

        $kelas = DB::select(
            'SELECT k.kuantitas_kelas, k.id_kelas, k.harga, a.jam_selesai from jadwal_harians a
            join jadwal_umums b
            on a.id_jadwalU = b.id_jadwalU
            join kelas k
            on b.id_kelas = k.id_kelas
            where a.id_jadwalH = "' .$request->id_jadwalH. '";'
        );

        $deposit_kelas = deposit_kelas::where('id_member', '=', $member->id_member)
                                        ->where('id_kelas', '=', $kelas[0]->id_kelas)
                                        ->first();

        $tgl1 = Carbon::now();
        $tglBeli = $tgl1->toDateString();

        $curdate = $tglBeli;
        $month = Str::substr($curdate, 5, 2); // 2023-01-02
        $year = Str::substr($curdate, 2, 2);
        Str::substr($year, -2);


        if($member->status == 1){
            if(is_null($deposit_kelas) || $deposit_kelas->deposit_kelas < 1){
                if($member->deposit_uang >= 200000){
                    if($kelas[0]->kuantitas_kelas >= 1){
                        $booking = booking_kelas::create([
                            'id_booking' => 'BK'.'-'.$increment,
                            'noStrukBK' => $year.'.'.$month.'.'.$increment,
                            'id_member' => $request->id_member,
                            'id_jadwalH' => $request->id_jadwalH,
                            'tgl_booking' => $tglBooking,
                            'status' => 0,
                            'jenis_pembayaran' => 'Deposit Uang',
                        ]);
                        $hasil = (int)$kelas[0]->kuantitas_kelas - 1;
                        DB::table('kelas')->where('id_kelas', $kelas[0]->id_kelas)->update(['kuantitas_kelas' => $hasil]);
                        $member->deposit_uang = $member->deposit_uang - $kelas[0]->harga;
                        $member->save();
                    }else{
                        return response([
                            'message' => 'Kuantitas Kelas habis',
                            'data' => null
                        ], 400);
                    }
                }else{
                    return response([
                        'message' => 'Deposit uang tidak mencukupi dan deposit kelas tidak mencukupi',
                        'data' => null
                    ], 400);
                }
            }else if($deposit_kelas->deposit_kelas >= 1){
                if($kelas[0]->kuantitas_kelas >= 1){
                    $booking = booking_kelas::create([
                        'id_booking' => 'BK'.'-'.$increment,
                        'noStrukBK' => $year.'.'.$month.'.'.$increment,
                        'id_member' => $request->id_member,
                        'id_jadwalH' => $request->id_jadwalH,
                        'tgl_booking' => $tglBooking,
                        'status' => 0,
                        'jenis_pembayaran' => 'Deposit Kelas',
                    ]);
                    $hasil = (int)$kelas[0]->kuantitas_kelas - 1;
                    DB::table('kelas')->where('id_kelas', $kelas[0]->id_kelas)->update(['kuantitas_kelas' => $hasil]);
                    $hasilDepoK = $deposit_kelas->deposit_kelas - 1;
                    DB::table('deposit_kelas')->where('id_kelas', '=', $kelas[0]->id_kelas)
                                            ->where('id_member', '=', $member->id_member)
                                            ->update(['deposit_kelas' => $hasilDepoK]);
                }else{
                    return response([
                        'message' => 'Kuantitas Kelas habis',
                        'data' => null
                    ], 400);
                }
            }else{
                return response([
                    'message' => 'Deposit kelas tidak mencukupi',
                    'data' => null
                ], 400);
            }
        }else{
            return response([
                'message' => 'Member tidak aktif',
                'data' => null
            ], 400);
        }

        return response([
            'message' => 'Data Added',
            'data' => $booking
        ], 200);
    }

    public function cancelBooking($id_booking){
        $booking = booking_kelas::find($id_booking);

        $idMember = $booking->id_member;
        $member = member::find($idMember);

        $idJadwalH = $booking->id_jadwalH;
        $jadwalH = jadwal_harian::find($idJadwalH);

        $kelas = DB::select(
            'SELECT k.kuantitas_kelas, k.id_kelas, k.harga from jadwal_harians a
            join jadwal_umums b
            on a.id_jadwalU = b.id_jadwalU
            join kelas k
            on b.id_kelas = k.id_kelas
            where a.id_jadwalH = "' .$booking->id_jadwalH. '";'
        );

        $deposit_kelas = deposit_kelas::where('id_member', $member)
                                        ->where('id_kelas', $kelas[0]->id_kelas)
                                        ->first();

        if($booking->tgl_booking >= $jadwalH->tanggal){
            return response([
                'message' => 'Sudah tidak bisa membatalkan booking',
                'data' => null
            ], 400);
        }else{
            $hasil = (int)$kelas[0]->kuantitas_kelas + 1;
            DB::table('kelas')->where('id_kelas', $kelas[0]->id_kelas)->update(['kuantitas_kelas' => $hasil]);

            if($booking->jenis_pembayaran == "Deposit Uang"){
                $member->deposit_uang = $member->deposit_uang + $kelas[0]->harga;
            }else if($booking->jenis_pembayaran == "Deposit Kelas"){
                $deposit_kelas->deposit_kelas = $deposit_kelas->deposit_kelas + 1;
            }
            $member->save();
            $deposit_kelas->save();

            if($booking->delete()){
                return response([
                    'message' => 'Data Deleted',
                    'data' => $booking
                ], 200);
            }
        }

        return response([
            'message' => 'Cancel booking berhasil',
            'data' => $booking
        ], 200);
    }

    public function konfirmasiHadir($id_booking){
        $booking = booking_kelas::find($id_booking);

        $idJadwalH = $booking->id_jadwalH;

        // $hari_ini = Carbon::now();
        // $hari_ini = '2023-05-29';

        $query = "SELECT id_presensi_instruktur 
                FROM presensi_instrukturs 
                WHERE id_jadwalH = '$idJadwalH' 
                AND jam_mulai IS NOT NULL";
        $presensi_instruktur = DB::select($query);

        if(empty($presensi_instruktur)){
            return response([
                'message' => 'Instruktur belum di presensi',
                'data' => null
            ], 200);
        }else{
            $booking->status = 1;
            $booking->save();
        }
        
        return response([
            'message' => 'presensi kelas berhasil',
            'data' => $booking
        ], 200);
    }

    public function showBelum(){
        $booking = DB::select(
            'SET lc_time_names = "id_ID";'
        );
        $booking = DB::select(
            'SELECT DISTINCT m.nama_member, k.nama_kelas, b.hari, 
                    b.jam_mulai, b.jam_selesai, DATE_FORMAT(b.tanggal, "%d %M %Y") as tanggal,
                    DATE_FORMAT(a.tgl_booking, "%d %M %Y") as tgl_booking, a.jenis_pembayaran, i.nama_instruktur,
                    dk.deposit_kelas, m.id_member,
                    k.harga, m.deposit_uang, a.noStrukBK, a.id_booking, a.id_jadwalH from booking_kelas a 
            join jadwal_harians b
            on a.id_jadwalH = b.id_jadwalH
            join jadwal_umums c
            on b.id_jadwalU = c.id_jadwalU
            join kelas k
            on c.id_kelas = k.id_kelas
            join members m
            on a.id_member = m.id_member
            join instrukturs i
            on c.id_instruktur = i.id_instruktur
            LEFT JOIN deposit_kelas dk
            on dk.id_member = m.id_member
            where a.status = 0;'
        );
        
        if(empty($booking)){
            return response([
                'message' => 'data kosong',
                'data' => null
            ], 400);
        }else{
            return response([
                'message' => 'data berhasil didapat',
                'data' => $booking
            ], 200);
        }
    }

    public function showSudah(){
        $booking = DB::select(
            'SET lc_time_names = "id_ID";'
        );
        $booking = DB::select(
            'SELECT DISTINCT m.nama_member, k.nama_kelas, b.hari, 
                    b.jam_mulai, b.jam_selesai, DATE_FORMAT(b.tanggal, "%d %M %Y") as tanggal,
                    DATE_FORMAT(a.tgl_booking, "%d %M %Y") as tgl_booking, a.jenis_pembayaran, i.nama_instruktur,
                    dk.deposit_kelas, DATE_FORMAT(dk.tgl_exp, "%d %M %Y") as tgl_exp, m.id_member,
                    k.harga, m.deposit_uang, a.noStrukBK from booking_kelas a 
            join jadwal_harians b
            on a.id_jadwalH = b.id_jadwalH
            join jadwal_umums c
            on b.id_jadwalU = c.id_jadwalU
            join kelas k
            on c.id_kelas = k.id_kelas
            join instrukturs i
            on c.id_instruktur = i.id_instruktur
            join members m
            on a.id_member = m.id_member
            LEFT JOIN deposit_kelas dk
            on dk.id_member = m.id_member
            -- join transaksi_deposit_kelas dk
            -- on m.id_member = dk.id_member
            -- join transaksi_deposit_uangs du
            -- on m.id_member = du.id_member
            where a.status = 1;'
        );
        
        if(empty($booking)){
            return response([
                'message' => 'data kosong',
                'data' => null
            ], 400);
        }else{
            return response([
                'message' => 'data berhasil didapat',
                'data' => $booking
            ], 200);
        }
    }
}
