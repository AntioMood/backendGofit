<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\member;
use App\Models\sesi_gym;
use App\Models\booking_gym;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingGymController extends Controller
{
    public function index(){
        $booking = booking_gym::with('member', 'sesi_gym')->latest()->get();
        $member = member::latest()->get();
        $sesi = sesi_gym::latest()->get();

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
        $booking= booking_gym::find($id_booking);
        $member = member::all();
        $sesi = sesi_gym::all();

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
            'id_sesi' => 'required',
            'tgl_booking' => 'required',
        ]);

        if($validate->fails()){
            return response()->json($validate->errors(), 422);
        }

        $last = DB::table('booking_gyms')->latest()->first();
        if($last == null){
            $increment = 1;
        }else{
            $increment = ((int)Str::substr($last->id_booking_gym, 3,3)) + 1;
        }

        if($increment < 10){
            $increment = '00'.$increment;
        }else if($increment < 100){
            $increment = '0'.$increment;
        }

        $tgl = Carbon::now();
        $tglBooking = $tgl->toDateString();

        $curdate = $tglBooking;
        $month = Str::substr($curdate, 5, 2); // 2023-01-02
        $year = Str::substr($curdate, 2, 2);
        Str::substr($year, -2);

        $member = member::find($request->id_member);
        $sesi = sesi_gym::find($request->id_sesi);


        if($member->status == 1){
            if($sesi->kapasitas >= 1){
                $booking = booking_gym::create([
                    'id_booking_gym' => 'BG'.'-'.$increment,
                    'noStrukBG' => $year.'.'.$month.'.'.$increment,
                    'id_member' => $request->id_member,
                    'id_sesi' => $request->id_sesi,
                    'tgl_booking' => $request->tgl_booking,
                ]);
                $sesi->kapasitas = $sesi->kapasitas - 1;
                $sesi->save();
            }else{
                return response([
                    'message' => 'Kapasitas sudah full',
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
        $booking = booking_gym::find($id_booking);

        $idMember = $booking->id_member;
        $member = member::find($idMember);

        $idSesi = $booking->id_sesi;
        $sesi = sesi_gym::find($idSesi);

        $hMin1 = Carbon::parse($booking->tgl_booking)->subDay();
        $today = Carbon::today();

        if($today >= $hMin1){
            return response([
                'message' => 'Sudah tidak bisa membatalkan booking',
                'data' => null
            ], 400);
        }else{
            $sesi->kapasitas = $sesi->kapasitas + 1;
            $sesi->save();

            if($booking->delete()){
                return response([
                    'message' => 'Cancel booking berhasil',
                    'data' => $booking
                ], 200);
            }
        }
    }

    public function konfirmasiPresensi($id_booking_gym){
        $booking = booking_gym::find($id_booking_gym);

        $tgl_presensi = Carbon::now()->toDateTimeString();

        $booking->tgl_presensi = $tgl_presensi;
        $booking->save();

        return response([
            'message' => 'Presensi booking berhasil',
            'data' => $booking
        ], 200);
    }

    public function showBelum(){
        $booking = booking_gym::with('member', 'sesi_gym')
                     ->whereNull('tgl_presensi')
                     ->latest()
                     ->get();

        return response([
            'message' => 'Yang belum dipresensi berhasil didapat',
            'data' => $booking
        ], 200);
    }

    public function showSudah(){
        $booking = booking_gym::with('member', 'sesi_gym')
                     ->whereNotNull('tgl_presensi')
                     ->latest()
                     ->get();

        return response([
            'message' => 'Yang sudah dipresensi berhasil didapat',
            'data' => $booking
        ], 200);
    }
}
