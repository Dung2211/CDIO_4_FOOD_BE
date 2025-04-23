<?php

namespace App\Http\Controllers;

use App\Models\ChiTietDonHang;
use App\Models\DonHang;
use App\Models\MonAn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ThongkeController extends Controller
{
    public function thongkeDoanhThu(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        $data = DonHang::where('don_hangs.id_quan_an', $user->id)
                        ->where('is_thanh_toan', DonHang::DA_THANH_TOAN)
                        ->whereDate('created_at', '>=', $request->day_begin)
                        ->whereDate('created_at', '<=', $request->day_end)
                        ->select(
                            DB::raw("SUM(tien_hang) as tong_tien_hang"),
                            DB::raw("COUNT(id) as so_don_hang"),
                            DB::raw("DATE_FORMAT(created_at, '%d/%m/%Y') as ngay_tao"),
                        )
                        ->groupBy('ngay_tao')
                        ->get();

        $list_ngay = [];
        $list_tong_tien_hang = [];

        foreach ($data as $item) {
            array_push($list_ngay, $item->ngay_tao);
            array_push($list_tong_tien_hang, $item->tong_tien_hang);
        }

        return response()->json([
            'data'                  => $data,
            'list_ngay'             => $list_ngay,
            'list_tong_tien_hang'   => $list_tong_tien_hang,
        ]);
    }

    public function thongkeMonAn(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        $data = ChiTietDonHang::join('mon_ans', 'mon_ans.id', 'chi_tiet_don_hangs.id_mon_an')
                            ->join('don_hangs', 'don_hangs.id', 'chi_tiet_don_hangs.id_don_hang')
                            ->where('mon_ans.id_quan_an', $user->id)
                            ->where('don_hangs.is_thanh_toan', DonHang::DA_THANH_TOAN)
                            ->whereDate('don_hangs.created_at', '>=', $request->day_begin)
                            ->whereDate('don_hangs.created_at', '<=', $request->day_end)
                            ->select(
                                DB::raw("SUM(chi_tiet_don_hangs.so_luong) as so_luong_ban"),
                                DB::raw("SUM(chi_tiet_don_hangs.thanh_tien) as tong_tien"),
                                'mon_ans.ten_mon_an',
                            )
                            ->groupBy('mon_ans.ten_mon_an')
                            ->get();
        $list_mon_an = [];
        $list_so_luong = [];
        foreach ($data as $key => $value) {
            array_push($list_mon_an, $value->ten_mon_an);
            array_push($list_so_luong, $value->so_luong);
        }
        return response()->json([
            'data'          => $data,
            'list_mon_an'   => $list_mon_an,
            'list_so_luong' => $list_so_luong,
        ]);
    }

    public function dataThongKeShipper(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        $data = DonHang::where('id_shipper', $user->id)
                       ->where(function ($query) use ($request) {
                            if($request->day_begin && $request->day_end) {
                                $query->whereDate('don_hangs.created_at', '>=', $request->day_begin)
                                ->whereDate('don_hangs.created_at', '<=', $request->day_end);
                            }
                        })
                       ->join('dia_chis', 'dia_chis.id', 'don_hangs.id_dia_chi_nhan')
                       ->select(
                            'don_hangs.*',
                            'dia_chis.dia_chi',
                            DB::raw('DATE_FORMAT(don_hangs.created_at, "%d/%m/%Y") as ngay_giao'),
                        )
                        ->orderByDESC('don_hangs.created_at')
                        ->get();

        return response()->json([
            'data' => $data,
        ]);
    }
}
