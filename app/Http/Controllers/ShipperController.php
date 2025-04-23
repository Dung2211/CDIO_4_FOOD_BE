<?php

namespace App\Http\Controllers;

use App\Http\Requests\activeShipperRequest;
use App\Http\Requests\CapNhatShipperRequest;
use App\Http\Requests\changeShipperRequest;
use App\Http\Requests\ShipperCreateRequest;
use App\Http\Requests\ShipperDangKyRequest;
use App\Http\Requests\ShipperDeleteRequest;
use App\Http\Requests\ShipperDoiMatKhauRequest;
use App\Http\Requests\ShipperLoginRequest;
use App\Http\Requests\ShipperUpdateRequest;
use App\Http\Requests\ThemMoiShipperRequest;
use App\Http\Requests\updatePasswordShipperRequest;
use App\Http\Requests\updateProFileShipperRequest;
use App\Http\Requests\XoaShipperRequest;
use App\Models\DiaChi;
use App\Models\DonHang;
use App\Models\PhanQuyen;
use App\Models\Shipper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShipperController extends Controller
{
    public function search(Request $request){
        $noi_dung_tim = '%'. $request->noi_dung_tim . '%';
        $data   =  Shipper::where('ho_va_ten', 'like', $noi_dung_tim)
                            ->get();
        return response()->json([
            'data'  => $data
        ]);
    }
    public function DangXuat()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            DB::table('personal_access_tokens')
                ->where('id', $user->currentAccessToken()->id)
                ->delete();
            return response()->json([
                'status'  => 1,
                'message' => "Đăng xuất thành công",
            ]);
        } else {
            return response()->json([
                'status'  => 0,
                'message' => "Có lỗi xảy ra",
            ]);
        }
    }

    public function DangXuatAll()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $ds_token = $user->tokens;
            foreach ($ds_token as $key => $value) {
                $value->delete();
            }
            return response()->json([
                'status'  => 1,
                'message' => "Đăng xuất thành công",
            ]);
        } else {
            return response()->json([
                'status'  => 0,
                'message' => "Có lỗi xảy ra",
            ]);
        }
    }
    public function checkTokenShipper()
    {
        $user_login = Auth::guard('sanctum')->user();
        if ($user_login) {
            return response()->json([
                'status'    => 1,
                'ho_ten'    => $user_login->ho_va_ten,
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'Bạn cần đăng nhập hệ thống!'
            ]);
        }
    }
    public function Login(ShipperLoginRequest $request)
    {
        $check = Shipper::where('email', $request->email)
            ->where('password', $request->password)
            ->first();
        if ($check) {
            return response()->json([
                'status' => 1,
                'message' => "Đăng nhập thành công!",
                'token'     => $check->createToken('token_shipper')->plainTextToken,
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => "Tài khoản hoặc mật khẩu không đúng.",
            ]);
        }
    }
    public function getData()
    {
        $id_chuc_nang = 6;
        $login = Auth::guard('sanctum')->user();
        $id_chuc_vu = $login->id_chuc_vu;
        $check_quyen = PhanQuyen::where('id_chuc_vu', $id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$check_quyen) {
            return response()->json([
                'data' => false,
                'message' => "bạn không có quyền thực hiện chức năng này!"
            ]);
        }
        $shipper = Shipper::get();
        return response()->json([
            'data' => $shipper
        ]);
    }
    public function store(ThemMoiShipperRequest $request)
    {
        $id_chuc_nang = 7;
        $login = Auth::guard('sanctum')->user();
        $id_chuc_vu = $login->id_chuc_vu;
        $check_quyen = PhanQuyen::where('id_chuc_vu', $id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$check_quyen) {
            return response()->json([
                'data' => false,
                'message' => "bạn không có quyền thực hiện chức năng này!"
            ]);
        }
        Shipper::create([
            'ho_va_ten'     => $request->ho_va_ten,
            'so_dien_thoai' => $request->so_dien_thoai,
            'email'         => $request->email,
            'password'      => $request->password,
            'cccd'          => $request->cccd,
            'is_active'     => $request->is_active,
            'is_open '      => $request->is_open,
        ]);
        return response()->json([
            'status'  => 1,
            'message' => 'Đã thêm mới shipper  ' . $request->ho_va_ten . ' thành công.'
        ]);
    }

    public function destroy(XoaShipperRequest $request)
    {
        $id_chuc_nang = 9;
        $login = Auth::guard('sanctum')->user();
        $id_chuc_vu = $login->id_chuc_vu;
        $check_quyen = PhanQuyen::where('id_chuc_vu', $id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$check_quyen) {
            return response()->json([
                'data' => false,
                'message' => "bạn không có quyền thực hiện chức năng này!"
            ]);
        }
        Shipper::where('id', $request->id)->delete();
        return response()->json([
            'status'    =>  1,
            'message'   =>  'Đã xóa shipper ' . $request->ho_va_ten . ' thành công'
        ]);
    }

    public function update(CapNhatShipperRequest $request)
    {
        $id_chuc_nang = 8;
        $login = Auth::guard('sanctum')->user();
        $id_chuc_vu = $login->id_chuc_vu;
        $check_quyen = PhanQuyen::where('id_chuc_vu', $id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$check_quyen) {
            return response()->json([
                'data' => false,
                'message' => "bạn không có quyền thực hiện chức năng này!"
            ]);
        }
        Shipper::where('id', $request->id)->update([
            'ho_va_ten'     => $request->ho_va_ten,
            'email'         => $request->email,
            'cccd'          => $request->cccd,
            'so_dien_thoai' => $request->so_dien_thoai,
            'is_active'     => $request->is_active,
            'is_open'      => $request->is_open,
        ]);
        return response()->json([
            'status'  => 1,
            'message' => 'Đã cập nhật shipper  ' . $request->ho_va_ten . ' thành công.'
        ]);
    }

    public function changeStatus(changeShipperRequest  $request)
    {
        $id_chuc_nang = 10;
        $login = Auth::guard('sanctum')->user();
        $id_chuc_vu = $login->id_chuc_vu;
        $check_quyen = PhanQuyen::where('id_chuc_vu', $id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$check_quyen) {
            return response()->json([
                'data' => false,
                'message' => "bạn không có quyền thực hiện chức năng này!"
            ]);
        }
        $shipper = Shipper::where('id', $request->id)->first();

        if ($shipper->is_open == 1) {
            $shipper->is_open = 0;
            $shipper->save();
        } else {
            $shipper->is_open = 1;
            $shipper->save();
        }
        return response()->json([
            'status'    =>  true,
            'message'   =>  'Bạn đã cập nhật trạng thái ' . $request->ho_va_ten . ' thành công'
        ]);
    }

    public function active(activeShipperRequest $request)
    {
        $id_chuc_nang = 11;
        $login = Auth::guard('sanctum')->user();
        $id_chuc_vu = $login->id_chuc_vu;
        $check_quyen = PhanQuyen::where('id_chuc_vu', $id_chuc_vu)
            ->where('id_chuc_nang', $id_chuc_nang)
            ->first();
        if (!$check_quyen) {
            return response()->json([
                'data' => false,
                'message' => "bạn không có quyền thực hiện chức năng này!"
            ]);
        }
        $shipper = Shipper::where('id', $request->id)->first();
        if ($shipper) {
            if ($shipper->is_active) {
                return response()->json([
                    'status'  => 2,
                    'message' => "Tài khoản đã được kích hoạt trước đó ",
                ]);
            } else {
                $shipper->is_active = 1;
                $shipper->save();
                return response()->json([
                    'status'  => 1,
                    'message' => "Kích hoạt tài khoản thành công ",
                ]);
            }
        } else {
            return response()->json([
                'status'  => 0,
                'message' => "Tài khoản không tồn tại ",
            ]);
        }
    }
    public function dataSP()
    {
        $user_login = Auth::guard('sanctum')->user();
        if ($user_login) {
            $shipper = Shipper::where('id', $user_login->id)->first();
            return response()->json([
                'status'    => 1,
                'data'      => $shipper
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'Bạn cần đăng nhập hệ thống!'
            ]);
        }
    }
    public function updateSP(updateProFileShipperRequest $request)
    {
        $user_login = Auth::guard('sanctum')->user();
        if ($user_login) {
            Shipper::where('id', $user_login->id)->update([
                'ho_va_ten'     => $request->ho_va_ten,
                'password'      => $request->password,
                'cccd'          => $request->cccd,
                'so_dien_thoai' => $request->so_dien_thoai,
            ]);
            return response()->json([
                'status'    => 1,
                'message'   => 'Cập nhật thông tin thành công!'
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'Bạn cần đăng nhập hệ thống!'
            ]);
        }
    }
    public function updatePassword(updatePasswordShipperRequest $request)
    {
        $user_login = Auth::guard('sanctum')->user();
        if ($user_login) {
            if ($request->old_password == $user_login->password) {
                Shipper::where('id', $user_login->id)->update([
                    'password'  => $request->password,
                ]);
                return response()->json([
                    'status'    => 1,
                    'message'   => 'Cập nhật mật khẩu thành công!'
                ]);
            }
            return response()->json([
                'status'    => 0,
                'message'   => 'Mật khẩu cũ không đúng!'
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'Bạn cần đăng nhập hệ thống!'
            ]);
        }
    }
    public function dataDonHangNhan()
    {
        $user = Auth::guard('sanctum')->user();
        $data = DonHang::join('khach_hangs', 'don_hangs.id_khach_hang', '=', 'khach_hangs.id')
            ->join('quan_ans', 'quan_ans.id', '=', 'don_hangs.id_quan_an')
            ->leftJoin('shippers', 'shippers.id', '=', 'don_hangs.id_shipper')
            ->where('don_hangs.id_shipper', $user->id)
            ->where('don_hangs.tinh_trang', '=', 1)
            ->orderBy('don_hangs.created_at')
            ->select('don_hangs.*', 'khach_hangs.ho_va_ten', 'quan_ans.ten_quan_an', 'shippers.ho_va_ten')
            ->get();

        return response()->json([
            'data'      => $data
        ]);
    }
    public function dataDaGiao()
    {
        $user = Auth::guard('sanctum')->user();

        if ($user) {
            $data = DonHang::where('don_hangs.id_shipper', $user->id)
                ->where('don_hangs.tinh_trang', 2)
                ->join('quan_ans', 'quan_ans.id', 'don_hangs.id_quan_an')
                ->join('khach_hangs', 'khach_hangs.id', 'don_hangs.id_khach_hang')
                ->leftjoin('shippers', 'shippers.id', 'don_hangs.id_shipper')
                ->select('don_hangs.*', 'khach_hangs.ho_va_ten', 'quan_ans.ten_quan_an', 'quan_ans.hinh_anh', 'shippers.ho_va_ten as ten_shipper')
                ->orderByDESC('don_hangs.created_at')
                ->get();

            return response()->json([
                'user'  => $user->ho_va_ten,
                'data'  => $data,
            ]);
        }
    }
    public function nhanDonHang(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $donHang = DonHang::find($request->id);
            if ($donHang) {
                if ($donHang->id_shipper == $user->id) {
                    return response()->json([
                        'status'    => 0,
                        'message'   => 'Đơn hàng đã được bạn nhận!'
                    ]);
                } else if ($donHang->id_shipper != null) {
                    return response()->json([
                        'status'    => 0,
                        'message'   => 'Đơn hàng đã được shipper khác nhận!'
                    ]);
                }
                $donHang->id_shipper = $user->id;
                $donHang->save();
                return response()->json([
                    'status'    => 1,
                    'message'   => 'Nhận đơn hàng thành công!'
                ]);
            } else {
                return response()->json([
                    'status'    => 0,
                    'message'   => 'Đơn hàng không tồn tại!'
                ]);
            }
        }
    }
    public function hoanThanhDonHang(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $donHang = DonHang::find($request->id);
            if ($donHang) {
                if ($donHang->id_shipper !== $user->id) {
                    return response()->json([
                        'status'    => 0,
                        'message'   => 'Bạn không thể hoàn thành đơn hàng này!'
                    ]);
                } else if ($donHang->tinh_trang == 2) {
                    return response()->json([
                        'status'    => 0,
                        'message'   => 'Đơn hàng này đã được giao trước đó!'
                    ]);
                } else if ($donHang->id_shipper == null) {
                    return response()->json([
                        'status'    => 0,
                        'message'   => 'Đơn hàng chưa được nhận!'
                    ]);
                }
                $donHang->tinh_trang    = 2;
                $donHang->is_thanh_toan = 1;
                $donHang->save();
                $shipper = Shipper::find($user->id);
                $shipper->tong_tien = $shipper->tong_tien + $donHang->phi_ship * 0.8;
                $shipper->save();

                return response()->json([
                    'status'    => 1,
                    'message'   => 'Hoàn thành đơn hàng thành công!'
                ]);
            } else {
                return response()->json([
                    'status'    => 0,
                    'message'   => 'Đơn hàng không tồn tại!'
                ]);
            }
        }
    }

    public function Register(Request $request)
    {
        Shipper::create([
            'ho_va_ten'     => $request->ho_va_ten,
            'email'         => $request->email,
            'password'      => $request->password,
            'cccd'          => $request->cccd,
            'so_dien_thoai' => $request->so_dien_thoai,
        ]);
        return response()->json([
            'status'  => 1,
            'message' => 'Đã thêm mới shipper  ' . $request->ho_va_ten . ' thành công.'
        ]);
    }
}
