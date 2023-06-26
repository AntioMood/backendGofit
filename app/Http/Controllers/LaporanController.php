<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\booking_gym;
use App\Models\booking_kelas;
use App\Models\presensi_instruktur;
use App\Models\kelas;
use App\Models\instruktur;
use App\Models\jadwal_harian;
use App\Models\aktivasi_tahunan;
use App\Models\transaksi_deposit_uang;
use App\Models\transaksi_deposit_kelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

class LaporanController extends Controller
{
    public function laporanGym(){
        $laporan = collect([]);
        App::setLocale('id');
        $sekarang = Carbon::now();
        $sekarangIndo = $sekarang->setTimezone('Asia/Jakarta');
        
        $sekarangFormated = $sekarangIndo->translatedFormat('Y-F-d');

        $tglCetak = $sekarang->translatedFormat('d');
        $bulan = $sekarang->translatedFormat('F');
        $tahun = $sekarang->translatedFormat('Y');

        $akhir = $sekarang->endOfMonth();

        $tanggal_cetak = $tglCetak.' '.$bulan.' '.$tahun;

        $tlgAkhir = substr($akhir, 8, 2);
        $temp = (int)$tlgAkhir;

        $total = 0;

        for($i = 0; $i < $temp; $i++){
            $tgl = Carbon::now();
            $tgl->startOfMonth();
            $tgl->addDays($i)->format('Y-F-d');
            $bookingGym = booking_gym::where('tgl_booking','=', $tgl)
                                       ->whereNotNull('tgl_presensi')
                                       ->get();
            // $tgl->addDays(1);
            $count = count($bookingGym);
            $storeData['jumlah'] = $count;
            $total = $total + $storeData['jumlah'];
            $storeData['tanggal'] = $tgl->translatedFormat('d F Y');
            $laporan->add($storeData);
        }

        if(count($laporan) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $laporan,
                'tahun' => $tahun,
                'bulan' => $bulan,
                'tgl_cetak' => $tanggal_cetak,
                'total' => $total,
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => $temp
        ], 400);
    }

    // public function laporanPaketKelas(){
    //     $now = Carbon::now();
    //     $bulan = substr($now, 5,2);

    //     $laporan = collect([]);

    //     $kelas = kelas::all();
    //     $instruktur = instruktur::all();

    //     foreach($kelas as $itemKelas){
    //         foreach($instruktur as $itemInstruktur){
    //             $idKelas = $itemKelas->id_kelas;
    //             $idInstruktur = $itemInstruktur->id_instruktur;
    //             // return response([
    //             //     'data' => $idKelas,
    //             //     'instruktur' => $idInstruktur
    //             // ], 200);
    //             $jadwalHarian = DB::select(
    //                 "SELECT * FROM jadwal_harians 
    //                 WHERE id_kelas = $itemKelas->id_kelas 
    //                 AND id_instruktur = $itemInstruktur->id_instruktur 
    //                 AND MONTH(tanggal) = $bulan"
    //             );
                
    //             if(count($jadwalHarian) > 0){
    //                 $storeData['Kelas'] = $itemKelas->nama_kelas;
    //                 $storeData['Instruktur'] = $itemInstruktur->nama_instruktur;

    //                 $libur = DB::select(
    //                     "SELECT a.* FROM jadwal_harians a
    //                     join jadwal_umums b 
    //                     on a.id_jadwalU = b.id_jadwalU
    //                     join kelas d
    //                     on b.id_kelas = d.id_kelas
    //                     join instrukturs c
    //                     on a.id_instruktur = c.id_instruktur
    //                     WHERE id_kelas = $itemKelas->id_kelas 
    //                 AND id_instruktur = $itemInstruktur->id_instruktur
    //                     AND MONTH(a.tanggal) = $bulan
    //                     AND a.status = 'Libur'"
    //                 );
    //                 $storeData['Jumlah Libur'] = count($libur); 

    //                 $peserta = DB::select(
    //                     "SELECT a.* FROM jadwal_harians a
    //                     join jadwal_umums b 
    //                     on a.id_jadwalU = b.id_jadwalU
    //                     join kelas d
    //                     on b.id_kelas = d.id_kelas
    //                     join instrukturs c
    //                     on a.id_instruktur = c.id_instruktur  
    //                     WHERE id_kelas = $itemKelas->id_kelas 
    //                 AND id_instruktur = $itemInstruktur->id_instruktur
    //                     AND MONTH(a.tanggal) = $bulan
    //                     AND a.status = 'Ada kelas'"
    //                 ); 
    //                 $storeData['Jumlah Peserta'] = 0;

    //                 foreach($peserta as $item){
    //                     $jumlahPeserta = DB::select(
    //                         "SELECT * FROM booking_kelas a
    //                         join jadwal_harians b
    //                         on  a.id_jadwalH = b.id_jadwalH
    //                         WHERE a.id_jadwalH = $item->id_jadwalH 
    //                         AND b.status = 'Ada kelas'"
    //                     );
    //                     $storeData['Jumlah Peserta'] = $storeData['Jumlah Peserta'] + count($jumlahPeserta);
    //                 }
    //                 $laporan->add($storeData);                   
    //             }
                
    //         }
    //     }

    //     if(!is_null($laporan)){
    //         return response([
    //             'message' => 'Retrieve All Success',
    //             'data' => $laporan
    //         ], 200);
    //     }
    //     return response([
    //         'message' => 'Empty',
    //         'data' => null
    //     ], 400);

    // }

    public function laporanPaketKelas(){
        $now = Carbon::now();
        $bulan = substr($now, 5,2);

        $sekarang = Carbon::now();
        $sekarangIndo = $sekarang->setTimezone('Asia/Jakarta');
        
        $sekarangFormated = $sekarangIndo->translatedFormat('Y-F-d');

        $tgl = $sekarang->translatedFormat('d');
        $bulanindo = $sekarang->translatedFormat('F');
        $tahun = $sekarang->translatedFormat('Y');

        $tanggal_cetak = $tgl.' '.$bulanindo.' '.$tahun;

        $laporan = collect([]);

        $kelas = kelas::orderBy('nama_kelas', 'asc')->get();
        $instruktur = instruktur::orderBy('nama_instruktur', 'asc')->get();

        foreach($kelas as $item1){
            foreach($instruktur as $item2){
                $id1 = $item1->id_kelas;
                $id2 = $item2->id_instruktur;

                $jadwalHarian = jadwal_harian::whereMonth('tanggal', $bulan)
                                            ->where('id_kelas','=',$id1)
                                            ->where('id_instruktur','=',$id2)
                                            ->get();
                if(count($jadwalHarian)>0){
                    $storeData['kelas'] = $item1->nama_kelas;
                    $storeData['instruktur'] = $item2->nama_instruktur;

                    $jumlahLibur = jadwal_harian::whereMonth('tanggal', $bulan)
                                                ->where('id_kelas','=', $item1->id_kelas)
                                                ->where('id_instruktur','=',$item2->id_instruktur)
                                                ->where('status','=','Libur')
                                                ->get(); 
                    $storeData['jumlah_libur'] = count($jumlahLibur); 

                    // $jumlahP = jadwal_harian::whereMonth('tanggal', $bulan)
                    //                         ->where('id_kelas','=', $item1->id_kelas)
                    //                         ->where('id_instruktur','=',$item2->id_instruktur)
                    //                         ->where('status','=','Ada Kelas')
                    //                         ->get(); 

                    $storeData['jumlah_peserta'] = 0;
                    foreach($jadwalHarian as $item3){
                        $jumlahPeserta = booking_kelas::where('id_jadwalH','=',$item3->id_jadwalH)
                                                    ->where('status','=','1')
                                                    ->get();
                        $storeData['jumlah_peserta'] = $storeData['jumlah_peserta'] + count($jumlahPeserta);
                    }
                    $laporan->add($storeData);                   
                }
                
            }
        }

        if(!is_null($laporan)){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $laporan,
                'bulan' => $bulanindo,
                'tahun' => $tahun,
                'tgl_cetak' => $tanggal_cetak
            ], 200);
        }
        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);

    }

    public function laporanPendapatan(){
        $total_all = 0;
        App::setLocale('id');

        $dataLaporanAll = [];
        for ($i = 1; $i < 13; $i++) {
            $laporan = [];
            $laporan['total'] = null;
            $laporan['Deposit_Uang'] = 0;
            $laporan['Deposit_Kelas'] = 0;
            $laporan['Bulan'] = Carbon::create(null, $i, 1)->translatedFormat('F');

            $depoU = transaksi_deposit_uang::whereMonth('tgl_transaksi', '=', $i)->get();
            $depoK = transaksi_deposit_kelas::whereMonth('tgl_transaksi', '=', $i)->get();
            $aktivasi = aktivasi_tahunan::whereMonth('tgl_transaksi', '=', $i)->get();

            foreach ($depoU as $data1) {
                $laporan['Deposit_Uang'] = (float) $laporan['Deposit_Uang'] + $data1['depoU'];
            }

            foreach ($depoK as $data2) {
                $laporan['Deposit_Kelas'] = (float) $laporan['Deposit_Kelas'] + $data2['totalBayar'];
            }

            $laporan['Aktivasi'] = count($aktivasi) * 3000000;
            $laporan['depositall'] = $laporan['Deposit_Uang'] + $laporan['Deposit_Kelas'];
            $laporan['total'] = $laporan['Aktivasi'] + $laporan['Deposit_Uang'] + $laporan['Deposit_Kelas'];
            $total_all = $total_all + $laporan['total'];

            $dataLaporanAll[] = $laporan;
        }
        $periode = Carbon::now()->format('Y');

        $sekarang = Carbon::now();
        $sekarangFormated = $sekarang->translatedFormat('Y-F-d');

        $tgl = $sekarang->format('d');
        $bulanAngka = $sekarang->translatedFormat('F');
        $tahun = $sekarang->format('Y');
        $tanggal_cetak = $tgl . ' ' . $bulanAngka . ' ' . $tahun;

        if (!is_null($dataLaporanAll)) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $dataLaporanAll,
                'periode' => $periode,
                'Tanggal_Cetak' => $tanggal_cetak,
                'Total' => $total_all,
            ], 200);
        }
        return response([
            'message' => 'Empty',
            'data' => null,
        ], 400);
    }


    public function laporanInstruktur(){
        $sekarang = Carbon::now();
        $sekarangIndo = $sekarang->setTimezone('Asia/Jakarta');
        
        $sekarangFormated = $sekarangIndo->translatedFormat('Y-F-d');

        $tgl = $sekarang->translatedFormat('d');
        $bulanindo = $sekarang->translatedFormat('F');
        $tahun = $sekarang->translatedFormat('Y');

        $tanggal_cetak = $tgl.' '.$bulanindo.' '.$tahun;
        $bulan = Carbon::now()->format('m');

        $laporan = collect([]);
        $instruktur = Instruktur::orderBy('nama_instruktur', 'asc')->get();
        $data['Jumlah_Hadir'] = 0;
        $jadwalHarian[] = null;

        foreach($instruktur as $dataInstruktur){
            $data['Nama_Instruktur'] = $dataInstruktur->nama_instruktur;
            $jadwalHarian = DB::select(
                'SELECT * from jadwal_harians
                where id_instruktur = "'.$dataInstruktur['id_instruktur'].'"
                AND MONTH(tanggal) = "'.$bulan.'";'
            );

            // return response([
            //     'message' => 'Retrieve All Success',
            //     'data' => $jadwalHarian
            // ], 200);

            if(count($jadwalHarian) > 1){
                foreach($jadwalHarian as $dataJadwalHarian){
                    $jumlahHadir = DB::select(
                        'SELECT * from presensi_instrukturs
                        where id_jadwalH = "'.$dataJadwalHarian->id_jadwalH.'"
                        AND MONTH(tgl_presensi) = "'.$bulan.'";'
                    );
                    $data['Jumlah_Hadir'] = $data['Jumlah_Hadir'] + count($jumlahHadir);
                }
            }else{
                $jumlahHadir = DB::select(
                    'SELECT * from presensi_instrukturs
                    where id_jadwalH = "'.$jadwalHarian[0]->id_jadwalH.'"
                    AND MONTH(tgl_presensi) = "'.$bulan.'";'
                );
                $data['Jumlah_Hadir'] = count($jumlahHadir);
            }

            $jumlahLibur = DB::select(
                'SELECT * from jadwal_harians
                where id_instruktur = "'.$dataInstruktur['id_instruktur'].'"
                AND status = "Libur"
                AND MONTH(tanggal) = "'.$bulan.'";'
            );

            $data['Jumlah_Libur'] = count($jumlahLibur);

            $keterlambatan = $dataInstruktur['jumlah_terlambat'];
            $detik = strtotime($keterlambatan) - strtotime('00:00:00');
            $data['Waktu_Terlambat'] = $detik;
            
            $laporan->add($data);
        }

        if (!is_null($laporan)) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $laporan,
                'bulan' => $bulanindo,
                'tahun' => $tahun,
                'tgl_cetak' => $tanggal_cetak
            ], 200);
        }
        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);

    }
}
