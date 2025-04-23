<?php

namespace App\Http\Controllers;

use App\Http\Requests\CapNhatDanhMucRequest;
use App\Http\Requests\changeActiveQuanAnrequest;
use App\Http\Requests\ChangeStatusQuanAnrequest;
use App\Http\Requests\crateDanhMucQuanAnRequest;
use App\Http\Requests\createMonAnRequest;
use App\Http\Requests\DangKyQuanAnRequest;
use App\Http\Requests\deleteMonAnRequest;
use App\Http\Requests\QuanAnCapNhatRequest;
use App\Http\Requests\QuanAnDangKyRequest;
use App\Http\Requests\QuanAnDeleteRequest;
use App\Http\Requests\QuanAnDoiMatKhauRequest;
use App\Http\Requests\QuanAnLoginRequest;
use App\Http\Requests\QuanAnThemMoiRequest;
use App\Http\Requests\QuanAnUpdateProfileRequest;
use App\Http\Requests\QuanAnUpdateRequest;
use App\Http\Requests\QuanAnXoaRequest;
use App\Http\Requests\ThemMoiDanhMucRequest;
use App\Http\Requests\updateMonAnRequest;
use App\Http\Requests\XoaDanhMucRequest;
use App\Models\ChiTietDanhMucQuanAn;
use App\Models\DanhMuc;
use App\Models\DiaChi;
use App\Models\MonAn;
use App\Models\PhanQuyen;
use App\Models\QuanAn;
use App\Models\QuanHuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuanAnController extends Controller
{
    public function searchNguoiDung(Request $request){
        $noi_dung_tim = '%'. $request->noi_dung_tim . '%';
        $data = QuanAn::join('quan_huyens', 'quan_ans.id_quan_huyen', 'quan_huyens.id')
        ->join('tinh_thanhs', 'tinh_thanhs.id', 'quan_huyens.id_tinh_thanh')
        ->select('quan_ans.*', 'quan_huyens.ten_quan_huyen', 'tinh_thanhs.ten_tinh_thanh')
        ->where('ten_quan_an', 'like', $noi_dung_tim)
                            ->where('tinh_trang', 1)
                            ->get();
        return response()->json([
            'data'  => $data
        ]);
    }

    public function search(Request $request){
        $noi_dung_tim = '%'. $request->noi_dung_tim . '%';
        $data = QuanAn::join('quan_huyens', 'quan_ans.id_quan_huyen', 'quan_huyens.id')
        ->join('tinh_thanhs', 'tinh_thanhs.id', 'quan_huyens.id_tinh_thanh')
        ->select('quan_ans.*', 'quan_huyens.ten_quan_huyen', 'tinh_thanhs.ten_tinh_thanh')
        ->where('ten_quan_an', 'like', $noi_dung_tim)
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
    public function checkTokenQuanAn()
    {
        $user_login = Auth::guard('sanctum')->user();
        if ($user_login) {
            return response()->json([
                'status'    => 1,
                'ten_quan_an'    => $user_login->ten_quan_an,
                'hinh_anh'  => $user_login->hinh_anh,
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'Bạn cần đăng nhập hệ thống!'
            ]);
        }
    }
    public function Login(QuanAnLoginRequest $request)
    {
        $check = QuanAn::where('email', $request->email)
            ->where('password', $request->password)
            ->first();
        if ($check) {
            return response()->json([
                'status' => 1,
                'message' => "Đăng nhập thành công!",
                'token'     => $check->createToken('token_quan_an')->plainTextToken,
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
        $id_chuc_nang = 28;
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
        $data = QuanAn::join('quan_huyens', 'quan_ans.id_quan_huyen', 'quan_huyens.id')
                        ->join('tinh_thanhs', 'tinh_thanhs.id', 'quan_huyens.id_tinh_thanh')
                        ->select('quan_ans.*', 'quan_huyens.ten_quan_huyen', 'tinh_thanhs.ten_tinh_thanh')
                        ->get();
        return response()->json([
            'data' => $data
        ]);
    }


    public function getDataOpen()
    {
        $data = QuanAn::where('tinh_trang', 1)->get();
        return response()->json([
            'data' => $data
        ]);
    }

    public function store(QuanAnThemMoiRequest $request)
    {
        $id_chuc_nang = 29;
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
        QuanAn::create([
            'email'                 => $request->email,
            'password'              => $request->password,
            'ma_so_thue'            => $request->ma_so_thue,
            'ten_quan_an'           => $request->ten_quan_an,
            'gio_mo_cua'            => $request->gio_mo_cua,
            'gio_dong_cua'          => $request->gio_dong_cua,
            'so_dien_thoai'         => $request->so_dien_thoai,
            'is_active'             => $request->is_active,
            'tinh_trang'            => $request->tinh_trang,
            'dia_chi'               => $request->dia_chi,
            'id_quan_huyen'         => $request->id_quan_huyen,
        ]);
        return response()->json([
            'status'    => 1,
            'message'   => 'Thêm mới quán ăn thành công!',
        ]);
    }

    public function update(QuanAnUpdateRequest $request)
    {
        $id_chuc_nang = 30;
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
        $data = QuanAn::find($request->id);
        if ($data) {
            $data->update([
                'email'                 => $request->email,
                'ma_so_thue'            => $request->ma_so_thue,
                'ten_quan_an'           => $request->ten_quan_an,
                'gio_mo_cua'            => $request->gio_mo_cua,
                'gio_dong_cua'          => $request->gio_dong_cua,
                'so_dien_thoai'         => $request->so_dien_thoai,
                'is_active'             => $request->is_active,
                'tinh_trang'            => $request->tinh_trang,
                'dia_chi'               => $request->dia_chi,
                'id_quan_huyen'         => $request->id_quan_huyen,
            ]);
            return response()->json([
                'status'    => 1,
                'message'   => 'Cập nhật quán ăn thành công!',
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'Quán ăn không tồn tại!',
            ]);
        }
    }
    public function destroy(QuanAnDeleteRequest $request)
    {
        $id_chuc_nang = 31;
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
        $data = QuanAn::find($request->id);
        if ($data) {
            $data->delete();
            return response()->json([
                'status'    => 1,
                'message'   => 'Xóa quán ăn thành công!',
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'Quán ăn không tồn tại!',
            ]);
        }
    }
    public function changeStatus(ChangeStatusQuanAnrequest $request)
    {
        $id_chuc_nang = 32;
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
            $data = QuanAn::find($request->id);
            if ($data) {
                $data->update([
                    'tinh_trang'    => !$data->tinh_trang,
                ]);
                return response()->json([
                    'status'    => 1,
                    'message'   => 'Thay đổi trạng thái thành công!',
                    'data'      => $data
                ]);
            } else {
                return response()->json([
                    'status'    => 0,
                    'message'   => 'Quán ăn không tồn tại!',
                ]);
            }
        }
    }
    public function changeActive(changeActiveQuanAnrequest $request)
    {
        $id_chuc_nang = 27;
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
        $quan_an = QuanAn::find($request->id);

        if ($quan_an->is_active == 0) {
            $quan_an->is_active = 1;
            $quan_an->save();

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

    public function getDataDanhMuc()
    {
        $user = Auth::guard('sanctum')->user();
        $data = DanhMuc::join('chi_tiet_danh_muc_quan_ans', 'danh_mucs.id', 'chi_tiet_danh_muc_quan_ans.id_danh_muc')
                        ->where('chi_tiet_danh_muc_quan_ans.id_quan_an', $user->id)
                        ->join('quan_ans', 'chi_tiet_danh_muc_quan_ans.id_quan_an', 'quan_ans.id')
                        ->leftjoin('danh_mucs as B', 'B.id', 'danh_mucs.id_danh_muc_cha')
                        ->select('danh_mucs.*', 'chi_tiet_danh_muc_quan_ans.id_quan_an', 'quan_ans.ten_quan_an', 'B.ten_danh_muc as ten_danh_muc_cha')
                        ->get();

        return response()->json([
            'data'      => $data
        ]);
    }
    public function getDataDanhMucCha()
    {
        $user = Auth::guard('sanctum')->user();
        $data = DanhMuc::join('chi_tiet_danh_muc_quan_ans', 'danh_mucs.id', 'chi_tiet_danh_muc_quan_ans.id_danh_muc')
            ->join('quan_ans', 'chi_tiet_danh_muc_quan_ans.id_quan_an', 'quan_ans.id')
            ->select('danh_mucs.*', 'chi_tiet_danh_muc_quan_ans.id_quan_an', 'quan_ans.ten_quan_an', 'chi_tiet_danh_muc_quan_ans.id_danh_muc')
            // ->where('chi_tiet_danh_muc_quan_ans.id_quan_an', $user->id)
            ->whereNull('danh_mucs.id_danh_muc_cha')
            ->get();

        return response()->json([
            'data'      => $data
        ]);
    }
    public function createDanhMuc(ThemMoiDanhMucRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        $danhMuc = DanhMuc::create([
            'ten_danh_muc'      => $request->ten_danh_muc,
            'slug_danh_muc'     => $request->slug_danh_muc,
            'tinh_trang'        => $request->tinh_trang,
            'id_danh_muc_cha'   => $request->id_danh_muc_cha,
            'hinh_anh'          => $request->hinh_anh,
        ]);
        ChiTietDanhMucQuanAn::create([
            'id_danh_muc' => $danhMuc->id,
            'id_quan_an'  => $user->id
        ]);
        return response()->json([
            'status' => 1,
            'message' => 'Thêm danh mục món ăn thành công'
        ]);
    }

    public function updateDanhMuc(CapNhatDanhMucRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user || !$user->id) {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn cần đăng nhập để cập nhật danh mục!'
            ]);
        }
        $danhMuc = DanhMuc::find($request->id);
        if (!$danhMuc) {
            return response()->json([
                'status' => 0,
                'message' => 'Danh mục không tồn tại!'
            ]);
        }
        $check = ChiTietDanhMucQuanAn::where('id_danh_muc', $request->id)
            ->where('id_quan_an', $user->id)
            ->first();
        if (!$check) {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn không có quyền cập nhật danh mục này!'
            ]);
        }
        $danhMuc->update([
            'ten_danh_muc'      => $request->ten_danh_muc,
            'slug_danh_muc'     => $request->slug_danh_muc,
            'tinh_trang'        => $request->tinh_trang,
            'id_danh_muc_cha'   => $request->id_danh_muc_cha,
            'hinh_anh'          => $request->hinh_anh,
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Cập nhật danh mục món ăn thành công',
        ]);
    }
    public function deleteDanhMuc(XoaDanhMucRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user || !$user->id) {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn cần đăng nhập để xóa danh mục!'
            ]);
        }
        $danhMuc = DanhMuc::find($request->id);
        $check = ChiTietDanhMucQuanAn::where('id_danh_muc', $request->id)
            ->where('id_quan_an', $user->id)
            ->first();
        if ($check) {
            $danhMuc->delete();
            $check->delete();
            return response()->json([
                'status' => 1,
                'message' => 'Xóa danh mục món ăn thành công'
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn không có quyền xóa danh mục này!'
            ]);
        }
    }
    public function doiTrangThaiDanhMuc(XoaDanhMucRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user || !$user->id) {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn cần đăng nhập để xóa danh mục!'
            ]);
        }
        $data = DanhMuc::find($request->id);
        $check = ChiTietDanhMucQuanAn::where('id_danh_muc', $request->id)
            ->where('id_quan_an', $user->id)
            ->first();
        if ($check) {
            $data->tinh_trang = $data->tinh_trang == 1 ? 0 : 1;
            $data->save();
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn không có quyền thay đổi trạng thái danh mục này!'
            ]);
        }
        return response()->json([
            'status' => 1,
            'message' => 'Cập nhật tình trạng danh mục món ăn thành công'
        ]);
    }
    public function getDataMonAn()
    {
        $user = Auth::guard('sanctum')->user();
        $data = MonAn::join('quan_ans', 'mon_ans.id_quan_an', 'quan_ans.id')
            ->where('mon_ans.id_quan_an', $user->id)
            ->join('danh_mucs', 'mon_ans.id_danh_muc', 'danh_mucs.id')
            ->select('mon_ans.*', 'quan_ans.ten_quan_an', 'danh_mucs.ten_danh_muc')
            ->get();
        return response()->json([
            'data' => $data
        ]);
    }
    public function createMonAn(createMonAnRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        MonAn::create([
            'ten_mon_an'    => $request->ten_mon_an,
            'slug_mon_an'   => $request->slug_mon_an,
            'gia_ban'       => $request->gia_ban,
            'gia_khuyen_mai' => $request->gia_khuyen_mai,
            'mo_ta'      => $request->mo_ta,
            'ten_quan_an' => $request->ten_quan_an,
            'id_quan_an'    => $user->id,
            'tinh_trang'    => $request->tinh_trang,
            'hinh_anh'      => $request->hinh_anh,
            'is_combo'      => 0,
            'id_danh_muc'   => $request->id_danh_muc,
        ]);
        return response()->json([
            'status' => 1,
            'message' => 'Thêm món ăn thành công!',
        ]);
    }
    public function updateMonAn(updateMonAnRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user || !$user->id) {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn cần đăng nhập để cập nhật món ăn!'
            ]);
        }

        $monAn = MonAn::where('id', $request->id)
            ->where('id_quan_an', $user->id)
            ->first();

        if (!$monAn) {
            return response()->json([
                'status' => 0,
                'message' => 'Món ăn không tồn tại hoặc bạn không có quyền cập nhật!'
            ]);
        }

        $monAn->update([
            'ten_mon_an'        => $request->ten_mon_an,
            'slug_mon_an'       => $request->slug_mon_an,
            'gia_ban'           => $request->gia_ban,
            'gia_khuyen_mai'    => $request->gia_khuyen_mai,
            'id_quan_an'        => $user->id,
            'tinh_trang'        => $request->tinh_trang,
            'hinh_anh'          => $request->hinh_anh,
            'is_combo'          => $request->is_combo,
            'id_danh_muc'       => $request->id_danh_muc,
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Cập nhật món ăn thành công!',
        ]);
    }

    // XoaMonAnRequest
    public function deleteMonAn(deleteMonAnRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user || !$user->id) {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn cần đăng nhập để xóa món ăn!'
            ]);
        }
        $check = MonAn::where('id', $request->id)
            ->where('id_quan_an', $user->id)
            ->first();

        if (!$check) {
            return response()->json([
                'status' => 0,
                'message' => 'Món ăn không tồn tại hoặc bạn không có quyền xóa!'
            ]);
        }
        $check->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Xóa món ăn thành công!'
        ]);
    }
    // DoiTrangThaiMonAnRequest
    public function doiTrangThaiMonAn(deleteMonAnRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user || !$user->id) {
            return response()->json([
                'status' => 0,
                'message' => 'Bạn cần đăng nhập để thay đổi trạng thái món ăn!'
            ]);
        }
        $check = MonAn::where('id', $request->id)
            ->where('id_quan_an', $request->id_quan_an)
            ->first();
        if (!$check) {
            return response()->json([
                'status' => 0,
                'message' => 'Món ăn không tồn tại hoặc bạn không có quyền thay đổi trạng thái!'
            ]);
        }
        $data = MonAn::find($request->id);
        $data->tinh_trang = $data->tinh_trang == 1 ? 0 : 1;
        $data->save();
        return response()->json([
            'status' => 1,
            'message' => 'Cập nhật tình trạng món ăn thành công!',
        ]);
    }

    public function dangKy(DangKyQuanAnRequest $request)
    {
        QuanAn::create([
            'email'                 => $request->email,
            'password'              => $request->password,
            'ma_so_thue'            => $request->ma_so_thue,
            'ten_quan_an'           => $request->ten_quan_an,
            'gio_mo_cua'            => $request->gio_mo_cua,
            'gio_dong_cua'          => $request->gio_dong_cua,
            'so_dien_thoai'         => $request->so_dien_thoai,
            'dia_chi'               => $request->dia_chi,
            'id_quan_huyen'         => $request->id_quan_huyen,
        ]);
        return response()->json([
            'status'    => 1,
            'message'   => 'Đăng ký quán ăn thành công!',
        ]);
    }
    public function getDataQuanAn()
    {
        $user_login = Auth::guard('sanctum')->user();
        if ($user_login) {
            $quanan = QuanAn::where('quan_ans.id', $user_login->id)->first();
            return response()->json([
                'status'    => 1,
                'data'      => $quanan
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'Bạn cần đăng nhập hệ thống!'
            ]);
        }
    }
    public function updatePassword(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $data = QuanAn::where('id', $user->id)
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

    public function updateProfile(QuanAnUpdateProfileRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        $data = QuanAn::find($user->id);
        if ($data) {
            $data->update([
                'ten_quan_an'     => $request->ten_quan_an,
                'so_dien_thoai' => $request->so_dien_thoai,
                'email'         => $request->email,
                'dia_chi'       => $request->dia_chi
            ]);
            return response()->json([
                'status'    => 1,
                'message'   => 'Cập nhật thông tin thành công!',
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'Thông tin quán ăn không tồn tại!',
            ]);
        }
    }
}
