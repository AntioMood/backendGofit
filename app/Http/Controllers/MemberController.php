<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\member;
use App\Models\deposit_kelas;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;

class MemberController extends Controller
{
    public function index(){
        $member = collect([]);
        App::setLocale('id');
        $members = member::all();
        foreach($members as $data){
            $dataMember['id_member'] = $data->id_member;
            $dataMember['nama_member'] = $data->nama_member;
            $dataMember['tgl_lahir'] = Carbon::parse($data->tgl_lahir)->translatedFormat('d F Y');
            $dataMember['alamat'] = $data->alamat;
            $dataMember['email'] = $data->email;
            $dataMember['password'] = $data->password;
            $dataMember['no_telp'] = $data->no_telp;
            $dataMember['deposit_uang'] = $data->deposit_uang;
            $dataMember['deposit_kelas'] = $data->deposit_kelas;
            $dataMember['status'] = $data->status;
            $dataMember['tgl_pembuatan'] = Carbon::parse($data->tgl_pembuatan)->translatedFormat('d F Y');
            $dataMember['tgl_exp'] = Carbon::parse($data->tgl_exp)->translatedFormat('d F Y');
            $dataMember['jenis_kelamin'] = $data->jenis_kelamin;
            $member->add($dataMember);
        }

        if(count($member)>0){
            return response([
                'message' => 'Retrieve All Success',
                'data' =>$member
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' =>null
        ], 400);
    }

    public function show($id_member){
        $member = DB::select(
            'SET lc_time_names = "id_ID";'
        );
        $member = DB::selectOne(
            'SELECT *, DATE_FORMAT(tgl_exp, "%d %M %Y") as tgl_exp, 
            DATE_FORMAT(tgl_lahir, "%d %M %Y") as tgl_lahir FROM members 
            WHERE id_member = "'. $id_member.'";');

        if(!is_null($member)){
            return response()->json([
                'message' => 'Retrieve Customer Success',
                'data' => $member
            ], 200);
        }

        return response([
            'message' => 'Member not found',
            'data' => null
        ], 400);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'nama_member' => 'required',
            'tgl_lahir' => 'required|date_format:Y-m-d',
            'alamat' => 'required',            
            'email' => 'required|email',
            'no_telp' => 'required',
            // 'deposit_uang' => 'required',
            // 'status' => 'required',
            // 'tgl_pembuatan'=> 'required',
            // 'tgl_exp' => 'required|date_format:Y-m-d',
            'jenis_kelamin' => 'required'
        ]);

        if($validate->fails()){
            return response()->json($validate->errors(), 422);
        }

        $last = DB::table('members')->latest()->first();
        if($last == null){
            $increment = 1;
        }else{
            $increment = ((int)Str::substr($last->id_member, 6,3)) + 1;
        }

        if($increment < 10){
            $increment = '00'.$increment;
        }else if($increment < 100){
            $increment = '0'.$increment;
        }

        $curdate = Carbon::now();
        $month = Str::substr($curdate, 5, 2); // 2023-01-02
        $year = Str::substr($curdate, 2, 2);
        Str::substr($year, -2);

        $dateborn = $request->tgl_lahir;
        $tgl = Str::substr($dateborn, 8, 2);
        $bulan = Str::substr($dateborn, 5, 2); // 2023-01-02
        $tahun = Str::substr($dateborn, 2, 2);
        Str::substr($tahun, -2);


        $member = member::create([
            'id_member' => $year.'.'.$month.'.'.$increment,
            'nama_member' => $request->nama_member,
            'tgl_lahir' => $request->tgl_lahir,
            'alamat' => $request->alamat,
            'email' => $request->email,
            'password' => Hash::make($tgl.$bulan.$tahun),
            'no_telp' => $request->no_telp,
            'deposit_uang' => 0,
            'status' => 0,
            'tgl_pembuatan' => null,
            'tgl_exp' => null,
            'jenis_kelamin' => $request->jenis_kelamin,
        ]);

        $storeData = member::latest()->first();

        return response([
            'message' => 'Data Added',
            'data' => $storeData
        ], 200);
    }

    public function destroy($id_member){
        $member = member::find($id_member);

        if(is_null($member)){
            return response([
                'message' => 'Data Not Found',
                'data' => null
            ], 404);
        }

        if($member->delete()){
            return response([
                'message' => 'Data Deleted',
                'data' => $member
            ], 200);
        }

        return response([
            'message' => 'Delete Data Failed',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $id_member){
        $member = member::find($id_member);

        if(is_null($member)){
            return response([
                'message' => 'Data Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'nama_member' => 'required',
            'tgl_lahir' => 'required|date_format:Y-m-d',
            'alamat' => 'required',            
            'email' => 'required|email',
            'password' => 'required',
            'no_telp' => 'required',
            // 'deposit_uang' => 'required',
            // 'status' => 'required',
            // 'tgl_pembuatan' => 'required',
            // 'tgl_exp' => 'required',
            'jenis_kelamin' => 'required'
        ]);

        if($validate->fails()){
            return response()->json($validate->errors(), 422);
        }

        $member->nama_member = $updateData['nama_member'];
        $member->tgl_lahir = $updateData['tgl_lahir'];
        $member->alamat = $updateData['alamat'];
        $member->email = $updateData['email'];
        $member->password = $updateData['password'];
        $member->no_telp = $updateData['no_telp'];
        // $member->deposit_uang = $updateData['deposit_uang'];
        // $member->status = $updateData['status'];
        // $member->tgl_pembuatan = $updateData['tgl_pembuatan'];
        // $member->tgl_exp = $updateData['tgl_exp'];
        $member->jenis_kelamin = $updateData['jenis_kelamin'];

        if($member->save()){
            return response([
                'message' => 'Update Success',
                'data' => $member
            ], 200);
        }

        return response([
            'message' => 'Update Failed',
            'data' => null
        ], 400);
    }

    public function resetPassword(Request $request, $id_member){
        $member = member::find($id_member);

        if(is_null($member)){
            return response([
                'message' => 'Data Not Found',
                'data' => null
            ], 404);
        }

        // $updateData = $request->all();

        $dateborn = $request->tgl_lahir;
        $tgl = Str::substr($dateborn, 8, 2);
        $bulan = Str::substr($dateborn, 5, 2); // 2023-01-02
        $tahun = Str::substr($dateborn, 2, 2);
        Str::substr($tahun, -2);

        $member->password = Hash::make($tgl.$bulan.$tahun);

        if($member->save()){
            return response([
                'message' => 'Update Success',
                'data' => $member->password
            ], 200);
        }

        return response([
            'message' => 'Update Failed',
            'data' => null
        ], 400);
    }

    public function deaktivasi($id_member){
        $members = member::find($id_member);
        // $members = member::where('tgl_exp', '<=', Carbon::now()->toDatestring())->get();

        if($members->tgl_exp <= Carbon::today()){
            $members->status = 0;
            $members->save();
        }else{
            return response([
                'message' => 'Masa Aktif masih berjalan',
                'data' => null
            ], 400);
        } 

        return response([
            'message' => 'Retrieve member Success',
            'data' => $members
        ], 200);
    }

    public function showTglExp(){
        $member = member::where('tgl_exp', '<=', Carbon::today())->get();

        if(!is_null($member)){
            return response([
                'message' => 'Retrieve Member Success',
                'data' => $member
            ], 200);
        }

        return response([
            'message' => 'Member not found',
            'data' => null
        ], 400);
    }

    public function showDepoK($id_member){
        $member = member::find($id_member);

        $deposit_kelas = DB::select(
            'SET lc_time_names = "id_ID";'
        );

        $deposit_kelas = DB::select(
            'SELECT a.*, b.nama_member, c.nama_kelas, DATE_FORMAT(a.tgl_exp, "%d %M %Y") as tgl_exp FROM deposit_kelas a
            join members b
            on a.id_member = b.id_member
            join kelas c
            on a.id_kelas = c.id_kelas 
            WHERE a.id_member = "'.$member->id_member.'";');

        if(!is_null($deposit_kelas)){
            return response([
                'message' => 'Retrieve Member Success',
                'data' => $deposit_kelas
            ], 200);
        }
    }
}
