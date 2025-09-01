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
                'don_hangs.trang_thai_quan',
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
        ->where('don_hangs.tinh_trang', '<>', DonHang::TINH_TRANG_DA_HUY)
        ->join('khach_hangs', 'khach_hangs.id', 'don_hangs.id_khach_hang')
        ->leftJoin('shippers', 'shippers.id', 'don_hangs.id_shipper')
        ->select(
            'don_hangs.id',
            'don_hangs.created_at',
            'don_hangs.ma_don_hang',
            'don_hangs.tien_hang',
            'don_hangs.tinh_trang',
            'don_hangs.trang_thai_quan',
            'don_hangs.ten_nguoi_nhan',
            'shippers.ho_va_ten as ho_va_ten_shipper',
        )
        ->orderBy('don_hangs.created_at', 'desc')
        ->get();

    return response()->json([
        'data' => $data,
    ]);
    }

    public function tinhTrangDonHang(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

    DonHang::where('id', $request->id)
        ->where('id_quan_an', $user->id)
        ->where('tinh_trang', DonHang::TINH_TRANG_DANG_LAM)
        ->update([
            'tinh_trang' => DonHang::TINH_TRANG_DA_XONG,
        ]);

    return response()->json([
        'status'  => 1,
        'message' => 'Đã hoàn thành món, chờ shipper giao!',
    ]);
    }
    public function xacNhanDonHang(Request $request)
    {
       //xác nhận đơn hàng từ quán ăn
      $donHang = DonHang::find($request->id);
      if ($donHang && $donHang->trang_thai_quan == 0) {
        $donHang->trang_thai_quan = 1; // Đã xác nhận
        $donHang->save();
        return response()->json(['status' => true, 'message' => 'Đã xác nhận đơn hàng']);
    }

    return response()->json(['status' => false, 'message' => 'Không thể xác nhận đơn hàng']);
    }
    public function huyDonHang(HuyDonHangRequest $request)
    {
       $donHang = DonHang::find($request->id);
    if (!$donHang) {
        return response()->json(['status' => false, 'message' => 'Không tìm thấy đơn hàng']);
    }

    // Chỉ cho hủy nếu chưa xác nhận
    if ($donHang->trang_thai_quan != 0) {
        return response()->json(['status' => false, 'message' => 'Không thể hủy đơn đã xác nhận']);
    }

    $donHang->trang_thai_quan = -1;
    $donHang->save();

    return response()->json(['status' => true, 'message' => 'Đã hủy đơn hàng thành công']);
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
        ->where('don_hangs.tinh_trang', DonHang::TINH_TRANG_CHO_XAC_NHAN) // dùng hằng số: 0
        ->where('don_hangs.trang_thai_quan', DonHang::TRANG_THAI_QUAN_DA_XAC_NHAN) // dùng hằng số: 1
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

    // Đơn shipper đang giao (ĐANG LÀM hoặc ĐÃ XONG)
    $list_don_hang_dang_giao = DonHang::where('don_hangs.id_shipper', $user->id)
        ->whereIn('don_hangs.tinh_trang', [
            DonHang::TINH_TRANG_DANG_LAM,
            DonHang::TINH_TRANG_DA_XONG,
        ])
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

    // Đơn đã giao (hoàn thành) hoặc bị hủy
    $list_don_hang_hoan_thanh = DonHang::where('don_hangs.id_shipper', $user->id)
        ->whereIn('don_hangs.tinh_trang', [
            DonHang::TINH_TRANG_DA_GIAO,
            DonHang::TINH_TRANG_DA_HUY,
        ])
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
        'data'                     => $list_don_hang_dang_giao,
        'list_don_hang_hoan_thanh' => $list_don_hang_hoan_thanh,
    ]);
    }

    public function hoanThanhDonHangShipper(Request $request)
    {
      $user = Auth::guard('sanctum')->user();

    $donHang = DonHang::where('id', $request->id)
                      ->where('id_shipper', $user->id)
                      ->where('tinh_trang', DonHang::TINH_TRANG_DA_XONG) // 2
                      ->first();

    if (!$donHang) {
        return response()->json([
            'status'  => 0,
            'message' => 'Không thể hoàn thành đơn hàng. Đơn chưa ở trạng thái ĐÃ XONG!',
        ]);
    }

    $donHang->update([
        'tinh_trang' => DonHang::TINH_TRANG_DA_GIAO, // 3
    ]);

    return response()->json([
        'status'  => 1,
        'message' => 'Shipper đã giao đơn hàng thành công!',
    ]);
    }

    public function nhanDonDonHangShipper(ShipperNhanDonHangRequest $request)
    {
        $user = Auth::guard('sanctum')->user();

    $donHang = DonHang::where('id', $request->id)
                      ->where('id_shipper', 0)
                      ->where('trang_thai_quan', DonHang::TRANG_THAI_QUAN_DA_XAC_NHAN) // dùng hằng số
                      ->where('tinh_trang', DonHang::TINH_TRANG_CHO_XAC_NHAN)         // chắc chắn đang chờ
                      ->first();

    if ($donHang) {
        $donHang->update([
            'id_shipper' => $user->id,
            'tinh_trang' => DonHang::TINH_TRANG_DANG_LAM,
        ]);

        return response()->json([
            'status'  => 1,
            'message' => "Bạn đã nhận đơn hàng thành công!!",
        ]);
    }

    return response()->json([
        'status'  => 0,
        'message' => "Đơn hàng đã có shipper hoặc chưa được quán xác nhận!",
    ]);
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

}
