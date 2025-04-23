<?php

namespace App\Http\Controllers;

use App\Http\Requests\CapNhatKhachHangRequest;
use App\Http\Requests\changeActiveRequest;
use App\Http\Requests\changKhachHangRequest;
use App\Http\Requests\createDiaChiKhachHangRequest;
use App\Http\Requests\deleteDiaChiKhachHangRequest;
use App\Http\Requests\doiMatKhauKhachHangRequest;
use App\Http\Requests\KhachHangLoginRequest;
use App\Http\Requests\ThemMoiKhachHangRequest;
use App\Http\Requests\updateDiaChiKhachHangRequest;
use App\Http\Requests\updatePasswordKhachHangRequest;
use App\Http\Requests\updateProfileKhachHangRequest;
use App\Http\Requests\XoaKhachHangRequest;
use App\Models\ChiTietDiaChi;
use App\Models\DiaChi;
use App\Models\KhachHang;
use App\Models\MonAn;
use App\Models\PhanQuyen;
use App\Models\QuanHuyen;
use App\Models\TinhThanh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class KhachHangController extends Controller
{
    public function search(Request $request){
        $noi_dung_tim = '%'. $request->noi_dung_tim . '%';
        $data   =  KhachHang::where('ho_va_ten', 'like', $noi_dung_tim)
                            ->get();
        return response()->json([
            'data'  => $data
        ]);
    }
    public function checkToken()
    {
        $user_login = Auth::guard('sanctum')->user();
        if ($user_login) {
            return response()->json([
                'status'    => 1,
                'ho_ten'    => $user_login->ho_va_ten,
                'avatar'    => $user_login->avatar,
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'Bạn cần đăng nhập hệ thống!'
            ]);
        }
    }
    public function Login(KhachHangLoginRequest $request)
    {
        $res = Http::get("https://www.google.com/recaptcha/api/siteverify", [
            'secret'    => "6LftzRorAAAAAE0IHevZsTvnvUxU16FIK4dcYLY5",
            'response'  => $request->code,
        ]);

        if ($res->json()['success'] == false) {
            return response()->json([
                'status'    => 0,
                'message'   => 'Captcha không hợp lệ!'
            ]);
        }

        $check = KhachHang::where('email', $request->email)
            ->where('password', $request->password)
            ->first();
        if ($check) {
            return response()->json([
                'status' => 1,
                'message' => "Đăng nhập thành công!",
                'token'     => $check->createToken('token_khach_hang')->plainTextToken,
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
        $id_chuc_nang = 12;
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

        $check = Auth::guard('sanctum')->user();
        if ($check) {
            $data = KhachHang::all();
            return response()->json([
                'status'    => 1,
                'data'      => $data
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'Bạn cần đăng nhập hệ thống!'
            ]);
        }
    }
    public function store(ThemMoiKhachHangRequest $request)
    {
        $id_chuc_nang = 13;
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
        $check = Auth::guard('sanctum')->user();
        if (!$check) {
            return response()->json([
                'status'    => 0,
                'message'   => 'Bạn cần đăng nhập hệ thống!'
            ]);
        } else {
            $data = KhachHang::create([
                'ho_va_ten'     => $request->ho_va_ten,
                'so_dien_thoai' => $request->so_dien_thoai,
                'email'         => $request->email,
                'password'      => $request->password,
                'ngay_sinh'     => $request->ngay_sinh,
            ]);
            return response()->json([
                'status'    => 1,
                'message'   => 'Thêm Mới khách hàng thành công!',
                'data'      => $data
            ]);
        }
    }
    public function destroy(XoaKhachHangRequest $request)
    {
        $id_chuc_nang = 15;
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
        $data = KhachHang::find($request->id);
        $data->delete();
        return response()->json([
            'status' => 1,
            'message' => 'Xóa khách hàng thành công'
        ]);
    }
    public function update(CapNhatKhachHangRequest $request)
    {
        $id_chuc_nang = 14;
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
        $check = Auth::guard('sanctum')->user();
        if (!$check) {
            return response()->json([
                'status'    => 0,
                'message'   => 'Bạn cần đăng nhập hệ thống!'
            ]);
        } else {
            $data = KhachHang::find($request->id);
            if ($data) {
                $data->update([
                    'ho_va_ten'     => $request->ho_va_ten,
                    'so_dien_thoai' => $request->so_dien_thoai,
                    'email'         => $request->email,
                    'ngay_sinh'     => $request->ngay_sinh,
                    'is_active'     => $request->is_active,
                    'is_block'      => $request->is_block,
                ]);
                return response()->json([
                    'status'    => 1,
                    'message'   => 'Cập nhật khách hàng thành công!',
                    'data'      => $data
                ]);
            } else {
                return response()->json([
                    'status'    => 0,
                    'message'   => 'Khách hàng không tồn tại!',
                ]);
            }
        }
    }
    public function changeStatus(changKhachHangRequest $request)
    {
        $id_chuc_nang = 16;
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
        $data = KhachHang::find($request->id);
        if ($data->is_block == 1) {
            $data->is_block = 0;
        } else {
            $data->is_block = 1;
        }
        $data->save();
        return response()->json([
            'status' => 1,
            'message' => 'Cập nhật trạng thái khách hàng thành công'
        ]);
    }

    public function changeActive(changeActiveRequest $request)
    {
        $khachhang = KhachHang::find($request->id);

        if ($khachhang->is_active == 0) {
            $khachhang->is_active = 1;
            $khachhang->save();

            return response()->json([
                'status' => true,
                'message' => 'Đã kích hoạt quán ăn thành công!'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Quán ăn này đã được kích hoạt trước đó!'
            ]);
        }
    }

    public function getDataKhachHang()
    {
        $user = Auth::guard('sanctum')->user();
        $data = KhachHang::find($user->id);
        return response()->json([
            'status' => 1,
            'data' => $data
        ]);
    }

    public function updateProfile(updateProfileKhachHangRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        $data = KhachHang::find($user->id);
        if ($data) {
            $data->update([
                'ho_va_ten'     => $request->ho_va_ten,
                'so_dien_thoai' => $request->so_dien_thoai,
                'email'         => $request->email,
                'ngay_sinh'     => $request->ngay_sinh
            ]);
            return response()->json([
                'status'    => 1,
                'message'   => 'Cập nhật thông tin thành công!',
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'Thông tin khách hàng không tồn tại!',
            ]);
        }
    }

    public function updatePassword(updatePasswordKhachHangRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        $data = KhachHang::where('id', $user->id)
            ->where('password', $request->old_password)
            ->first();
        if ($data) {
            $data->update([
                'password' => $request->password,
            ]);
            return response()->json([
                'status'    => 1,
                'message'   => 'Cập nhật mật khẩu thành công!',
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'Mật khẩu cũ không đúng!',
            ]);
        }
    }

    public function getDataDiaChi()
    {
        $user = Auth::guard('sanctum')->user();
        $data = DiaChi::where('id_khach_hang', $user->id)
            ->join('khach_hangs', 'dia_chis.id_khach_hang', 'khach_hangs.id')
            ->join('quan_huyens', 'dia_chis.id_quan_huyen', 'quan_huyens.id')
            ->join('tinh_thanhs', 'quan_huyens.id_tinh_thanh', 'tinh_thanhs.id')
            ->select('dia_chis.*', 'quan_huyens.ten_quan_huyen', 'tinh_thanhs.ten_tinh_thanh')
            ->get();
        return response()->json([
            'status' => 1,
            'data' => $data
        ]);
    }

    public function storeDiaChi(createDiaChiKhachHangRequest $request)
    {
        $user = Auth::guard('sanctum')->user();

        DiaChi::create([
            'id_khach_hang'     => $user->id,
            'id_quan_huyen'     => $request->id_quan_huyen,
            'dia_chi'           => $request->dia_chi,
            'ten_nguoi_nhan'    => $request->ten_nguoi_nhan,
            'so_dien_thoai'     => $request->so_dien_thoai,
        ]);

        return response()->json([
            'status'    => 1,
            'message'   => 'Thêm mới địa chỉ thành công!'
        ]);
    }

    public function updateDiaChi(updateDiaChiKhachHangRequest $request)
    {
        $user = Auth::guard('sanctum')->user();

        DiaChi::where('id', $request->id)->update([
            'ten_nguoi_nhan' => $request->ten_nguoi_nhan,
            'so_dien_thoai'  => $request->so_dien_thoai,
            'dia_chi'        => $request->dia_chi,
            'id_quan_huyen'  => $request->id_quan_huyen,
        ]);

        return response()->json([
            'status'    => 1,
            'message'   => 'Cập nhật địa chỉ thành công!',
        ]);
    }

    public function destroyDiaChi(deleteDiaChiKhachHangRequest $request)
    {
        $dia_chi = DiaChi::find($request->id)->delete();
        return response()->json([
            'status'    => 1,
            'message'   => 'Xóa Địa Chỉ thành công!',
        ]);
    }

    public function getMonAn()
    {
        $data = MonAn::join('quan_ans', 'mon_ans.id_quan_an', '=', 'quan_ans.id')
            ->select('mon_ans.*', 'quan_ans.ten_quan_an')
            ->get();
        return response()->json([
            'status'    => 1,
            'data'      => $data
        ]);
    }
    public function doiMatKhau(doiMatKhauKhachHangRequest $request)
    {
        $user_login = Auth::guard('sanctum')->user();
        $kh = KhachHang::where('id', $user_login->id)->first();
        if ($kh) {
            if ($request->password == $kh->password) {
                $kh->update([
                    'password' => $request->new_password
                ]);
                return response()->json([
                    'status'    => 1,
                    'message'   => 'Đổi mật khẩu thành công!'
                ]);
            } else {
                return response()->json([
                    'status'    => 0,
                    'message'   => 'Mật khẩu cũ không đúng!'
                ]);
            }
        }
    }

    public function getDataTinhThanh()
    {
        $data = TinhThanh::where('tinh_trang', 1)->get();
        return response()->json([
            'data'  => $data
        ]);
    }

    public function getDataQuanHuyen(Request $request)
    {
        $data = QuanHuyen::where('tinh_trang', 1)
            ->where('id_tinh_thanh', $request->id_tinh_thanh)
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
}
