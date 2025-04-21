<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Termwind\Components\Raw;

class ThongKeAdminController extends Controller
{
    public function thongKeTienKhachHang(Request $request)
    {
        $day_begin = $request->day_begin;
        $day_end = $request->day_end;
        if ($day_begin && $day_end) {
            $data = DonHang::join('khach_hangs', 'khach_hangs.id', '=', 'don_hangs.id_khach_hang')
                ->whereDate('don_hangs.created_at', '>=', $day_begin)
                ->whereDate('don_hangs.created_at', '<=', $day_end)
                ->select(
                    'khach_hangs.ho_va_ten',
                    DB::raw('SUM(tong_tien) as tong_tien_tieu'),
                    DB::raw('COUNT(don_hangs.id) as tong_don_hang'),
                    DB::raw('MAX(tong_tien) as don_hang_max'),
                )->groupBy('khach_hangs.ho_va_ten')->get();
        } else {
            $data = DonHang::join('khach_hangs', 'khach_hangs.id', '=', 'don_hangs.id_khach_hang')
                ->select(
                    'khach_hangs.ho_va_ten',
                    DB::raw('SUM(tong_tien) as tong_tien_tieu'),
                    DB::raw('COUNT(don_hangs.id) as tong_don_hang'),
                    DB::raw('MAX(tong_tien) as don_hang_max'),
                )->groupBy('khach_hangs.ho_va_ten')->get();
        }
        $list_ten = [];
        $list_tien = [];
        foreach ($data as $key => $value) {
            array_push($list_ten, $value->ho_va_ten);
            array_push($list_tien, $value->tong_tien_tieu);
        }
        return response()->json([
            'list_ten' => $list_ten,
            'list_tien' => $list_tien,
            'data'  => $data
        ]);
    }
    public function thongKeTienQuanAn(Request $request)
    {
        $day_begin = $request->day_begin;
        $day_end = $request->day_end;

        if ($day_begin && $day_end) {
            $data = DB::table('don_hangs')
                ->join('quan_ans', 'quan_ans.id', '=', 'don_hangs.id_quan_an')
                ->join('khach_hangs', 'khach_hangs.id', '=', 'don_hangs.id_khach_hang')
                ->whereDate('don_hangs.created_at', '>=', $day_begin)
                ->whereDate('don_hangs.created_at', '<=', $day_end)
                ->select(
                    'quan_ans.ten_quan_an',
                    DB::raw('COUNT(DISTINCT don_hangs.id) as tong_don_hang'),
                    DB::raw('COUNT(DISTINCT don_hangs.id_khach_hang) as so_luong_khach_hang'),
                    DB::raw('SUM(don_hangs.tong_tien) as tong_tien_ban')
                )
                ->groupBy('quan_ans.ten_quan_an')
                ->get();
        } else {
            $data = DB::table('don_hangs')
                ->join('quan_ans', 'quan_ans.id', '=', 'don_hangs.id_quan_an')
                ->join('khach_hangs', 'khach_hangs.id', '=', 'don_hangs.id_khach_hang')
                ->select(
                    'quan_ans.ten_quan_an',
                    DB::raw('COUNT(DISTINCT don_hangs.id) as tong_don_hang'),
                    DB::raw('COUNT(DISTINCT don_hangs.id_khach_hang) as so_luong_khach_hang'),
                    DB::raw('SUM(don_hangs.tong_tien) as tong_tien_ban')
                )
                ->groupBy('quan_ans.ten_quan_an')
                ->get();
        }
        $list_ten = [];
        $list_tien = [];
        foreach ($data as $key => $value) {
            array_push($list_ten, $value->ten_quan_an);
            array_push($list_tien, $value->tong_tien_ban);
        }
        return response()->json([
            'list_ten' => $list_ten,
            'list_tien' => $list_tien,
            'data'  => $data
        ]);
    }
}
