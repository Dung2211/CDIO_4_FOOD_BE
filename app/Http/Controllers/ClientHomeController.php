<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use App\Models\DonHang;
use App\Models\MonAn;
use App\Models\QuanAn;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientHomeController extends Controller
{
    public function getDataHome()
    {
        $mon_an = MonAn::where('mon_ans.tinh_trang', 1)
            ->where('mon_ans.gia_khuyen_mai', '>', 0)
            ->join('quan_ans', 'quan_ans.id', 'mon_ans.id_quan_an')
            ->select('mon_ans.*', 'quan_ans.ten_quan_an')
            ->orderBy('mon_ans.gia_khuyen_mai')
            ->take(12)->get();

        $quan_an_yeu_thich = QuanAn::leftjoin('don_hangs', 'don_hangs.id_quan_an', 'quan_ans.id')
            ->select('quan_ans.id', 'quan_ans.ten_quan_an', 'quan_ans.hinh_anh', 'quan_ans.dia_chi')
            ->take(9)
            ->get();

        $voucher = Voucher::where('vouchers.tinh_trang', 1)
            ->where('vouchers.thoi_gian_ket_thuc', '>=', now())
            ->join('quan_ans', 'quan_ans.id', 'vouchers.id_quan_an')
            ->select('vouchers.*', 'quan_ans.ten_quan_an')
            ->take(6)
            ->get();

        $phan_loai = DanhMuc::where('tinh_trang', 1)
            ->get();

        return response()->json([
            'mon_an'                => $mon_an,
            'voucher'               => $voucher,
            'quan_an_yeu_thich'     => $quan_an_yeu_thich,
            'phan_loai'             => $phan_loai
        ]);
    }

    public function getDataQuanAn()
    {
        $quan_an_yeu_thich = QuanAn::leftJoin('don_hangs', 'don_hangs.id_quan_an', 'quan_ans.id')
                                    ->leftjoin('mon_ans', 'mon_ans.id_quan_an', 'quan_ans.id')
                                    ->select(
                                        'quan_ans.id',
                                        'quan_ans.ten_quan_an',
                                        'quan_ans.hinh_anh',
                                        'quan_ans.dia_chi',
                                        DB::raw('MIN(CASE WHEN mon_ans.gia_khuyen_mai > 0 THEN mon_ans.gia_khuyen_mai ELSE mon_ans.gia_ban END) as gia_min'),
                                        DB::raw('MAX(CASE WHEN mon_ans.gia_khuyen_mai > 0 THEN mon_ans.gia_khuyen_mai ELSE mon_ans.gia_ban END) as gia_max')
                                    )
                                    ->groupBy('quan_ans.id', 'quan_ans.ten_quan_an', 'quan_ans.hinh_anh', 'quan_ans.dia_chi') // Cần groupBy khi dùng aggregate
                                    ->take(9)
                                    ->get();
        return response()->json([
            'data'     => $quan_an_yeu_thich,
        ]);
    }
}
