<?php

namespace App\Http\Controllers;

use App\Http\Requests\HuyDonHangRequest;
use App\Http\Requests\ShipperNhanDonHangRequest;
use App\Models\ChiTietDonHang;
use App\Models\DonHang;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonHangController extends Controller
{
    public function getDonHangKhachHang()
    {
        $user = Auth::guard('sanctum')->user();

        $data = DonHang::where('don_hangs.id_khach_hang', $user->id)
            ->join('quan_ans', 'quan_ans.id', 'don_hangs.id_quan_an')
            ->leftjoin('shippers', 'shippers.id', 'don_hangs.id_shipper')
            ->join('dia_chis', 'dia_chis.id', 'don_hangs.id_dia_chi_nhan')
            ->select(
                'don_hangs.id',
                'don_hangs.ma_don_hang',
                'don_hangs.created_at',
                'don_hangs.tien_hang',
                'don_hangs.phi_ship',
                'don_hangs.tong_tien',
                'don_hangs.is_thanh_toan',
                'don_hangs.tinh_trang',
                'quan_ans.ten_quan_an',
                'shippers.ho_va_ten as ho_va_ten_shipper',
                'dia_chis.dia_chi',
                'dia_chis.ten_nguoi_nhan',
                'dia_chis.so_dien_thoai',
            )
            ->get();
        return response()->json([
            'data'      => $data
        ]);
    }

    public function getChiTietDonHangKhachHang(Request $request)
    {
        $data = ChiTietDonHang::where('chi_tiet_don_hangs.id_don_hang', $request->id)
            ->join('mon_ans', 'mon_ans.id', 'chi_tiet_don_hangs.id_mon_an')
            ->select(
                'mon_ans.ten_mon_an',
                'chi_tiet_don_hangs.so_luong',
                'chi_tiet_don_hangs.don_gia',
                'chi_tiet_don_hangs.thanh_tien',
                'chi_tiet_don_hangs.ghi_chu',
            )
            ->get();
        return response()->json([
            'data'  => $data
        ]);
    }

    public function getDonHangQuanAn()
    {
        $user = Auth::guard('sanctum')->user();

        $data = DonHang::where('don_hangs.id_quan_an', $user->id)
            ->where('tinh_trang', '>=', 1)
            ->join('khach_hangs', 'khach_hangs.id', 'don_hangs.id_khach_hang')
            ->join('shippers', 'shippers.id', 'don_hangs.id_shipper')
            ->select(
                'don_hangs.id',
                'don_hangs.created_at',
                'don_hangs.ma_don_hang',
                'don_hangs.tien_hang',
                'don_hangs.tinh_trang',
                'don_hangs.ten_nguoi_nhan',
                'shippers.ho_va_ten as ho_va_ten_shipper',
            )
            ->get();
        return response()->json([
            'data' => $data,
        ]);
    }

    public function daXongDonHang(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        DonHang::where('id', $request->id)
            ->where('id_quan_an', $user->id)
            ->where('tinh_trang', 1)
            ->update([
                'tinh_trang' => 2,
            ]);
        return response()->json([
            'status'    =>  1,
            'message'   =>  'Đã hoàn thành đơn hàng!!',
        ]);
    }

    public function chiTietDonHangQuanAn(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        $data = ChiTietDonHang::join('don_hangs', 'don_hangs.id', 'chi_tiet_don_hangs.id_don_hang')
            ->join('mon_ans', 'mon_ans.id', 'chi_tiet_don_hangs.id_mon_an')
            ->where('chi_tiet_don_hangs.id_don_hang', $request->id)
            ->where('don_hangs.id_quan_an', $user->id)
            ->select(
                'mon_ans.ten_mon_an',
                'chi_tiet_don_hangs.so_luong',
                'chi_tiet_don_hangs.don_gia',
                'chi_tiet_don_hangs.thanh_tien',
                'chi_tiet_don_hangs.ghi_chu',
            )
            ->get();
        return response()->json([
            'status'    =>  1,
            'data'      =>  $data,
        ]);
    }

    public function getDonHangShipper()
    {
        $list_don_hang_co_the_nhan = DonHang::where('don_hangs.id_shipper', 0)
                                            ->where('don_hangs.tinh_trang', 0)
                                            ->join('quan_ans', 'quan_ans.id', 'don_hangs.id_quan_an')
                                            ->join('khach_hangs', 'khach_hangs.id', 'don_hangs.id_khach_hang')
                                            ->join('dia_chis', 'dia_chis.id', 'don_hangs.id_dia_chi_nhan')
                                            ->select(
                                                'don_hangs.id',
                                                'don_hangs.ma_don_hang',
                                                'quan_ans.ten_quan_an',
                                                'quan_ans.hinh_anh',
                                                'quan_ans.dia_chi as dia_chi_quan',
                                                'don_hangs.ten_nguoi_nhan',
                                                'khach_hangs.avatar',
                                                'dia_chis.dia_chi as dia_chi_khach',
                                                'don_hangs.tong_tien',
                                                'don_hangs.phi_ship',
                                            )
                                            ->get();
        return response()->json([
            'list_don_hang_co_the_nhan' => $list_don_hang_co_the_nhan,
        ]);
    }

    public function getDonHangShipperDangGiao()
    {
        $user = Auth::guard('sanctum')->user();
        $list_don_hang_co_the_nhan = DonHang::where('don_hangs.id_shipper', $user->id)
                                            ->whereIn('don_hangs.tinh_trang', [1, 2])
                                            ->join('quan_ans', 'quan_ans.id', 'don_hangs.id_quan_an')
                                            ->join('khach_hangs', 'khach_hangs.id', 'don_hangs.id_khach_hang')
                                            ->join('dia_chis', 'dia_chis.id', 'don_hangs.id_dia_chi_nhan')
                                            ->select(
                                                'don_hangs.id',
                                                'don_hangs.ma_don_hang',
                                                'quan_ans.ten_quan_an',
                                                'quan_ans.hinh_anh',
                                                'quan_ans.dia_chi as dia_chi_quan',
                                                'don_hangs.ten_nguoi_nhan',
                                                'khach_hangs.avatar',
                                                'dia_chis.dia_chi as dia_chi_khach',
                                                'don_hangs.tong_tien',
                                                'don_hangs.phi_ship',
                                                'don_hangs.tinh_trang',
                                            )
                                            ->get();
        $list_don_hang_hoan_thanh = DonHang::where('don_hangs.id_shipper', $user->id)
                                            ->whereIn('don_hangs.tinh_trang', [3, 4])
                                            ->join('quan_ans', 'quan_ans.id', 'don_hangs.id_quan_an')
                                            ->join('khach_hangs', 'khach_hangs.id', 'don_hangs.id_khach_hang')
                                            ->join('dia_chis', 'dia_chis.id', 'don_hangs.id_dia_chi_nhan')
                                            ->select(
                                                'don_hangs.id',
                                                'don_hangs.ma_don_hang',
                                                'quan_ans.ten_quan_an',
                                                'quan_ans.hinh_anh',
                                                'quan_ans.dia_chi as dia_chi_quan',
                                                'don_hangs.ten_nguoi_nhan',
                                                'khach_hangs.avatar',
                                                'dia_chis.dia_chi as dia_chi_khach',
                                                'don_hangs.tong_tien',
                                                'don_hangs.phi_ship',
                                                'don_hangs.tinh_trang',
                                            )
                                            ->get();

        return response()->json([
            'data'                      => $list_don_hang_co_the_nhan,
            'list_don_hang_hoan_thanh'  => $list_don_hang_hoan_thanh,
        ]);
    }

    public function hoanThanhDonHangShipper(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        DonHang::where('id', $request->id)
            ->where('id_shipper', $user->id)
            ->where('tinh_trang', 2)
            ->update([
                'tinh_trang' => 3,
            ]);
        return response()->json([
            'status'    =>  1,
            'message'   =>  'Đã hoàn thành đơn hàng!!',
        ]);
    }

    public function nhanDonDonHangShipper(ShipperNhanDonHangRequest $request)
    {
        $user = Auth::guard('sanctum')->user();

        $check = DonHang::where('id', $request->id)
                        ->where('id_shipper', 0)
                        ->first();
        if ($check) {
            $check->update([
                'id_shipper' => $user->id,
                'tinh_trang' => 1,
            ]);
            return response()->json([
                'status'    => 1,
                'message'   => "Bạn đã nhận đơn hàng thành công!!",
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => "Đơn hàng này đã có người nhận!!",
            ]);
        }
    }

    public function getDonHangAdmin()
    {
        $data = DonHang::join('quan_ans', 'quan_ans.id', 'don_hangs.id_quan_an')
            ->join('khach_hangs', 'khach_hangs.id', 'don_hangs.id_khach_hang')
            ->leftjoin('shippers', 'shippers.id', 'don_hangs.id_shipper')
            ->select(
                'don_hangs.*',
                'quan_ans.ten_quan_an',
                'khach_hangs.ho_va_ten as ho_va_ten_khach_hang',
                'shippers.ho_va_ten as ho_va_ten_shipper',
            )
            ->get();

        return response()->json([
            'data'   => $data,
        ]);
    }

    public function getChiTietDonHangAdmin(Request $request)
    {
        $data = ChiTietDonHang::where('chi_tiet_don_hangs.id_don_hang', $request->id)
            ->join('mon_ans', 'mon_ans.id', 'chi_tiet_don_hangs.id_mon_an')
            ->select(
                'mon_ans.ten_mon_an',
                'chi_tiet_don_hangs.so_luong',
                'chi_tiet_don_hangs.don_gia',
                'chi_tiet_don_hangs.thanh_tien',
                'chi_tiet_don_hangs.ghi_chu',
            )
            ->get();
        return response()->json([
            'data'  => $data
        ]);
    }

    public function huyDonHangAdmin(HuyDonHangRequest $request)
    {
        DonHang::where('id', $request->id)->update([
            'tinh_trang'    => 4
        ]);

        return response()->json([
            'status'    => 1,
            'message'   => "Đã hủy đơn hàng thành công!!",
        ]);
    }
}
