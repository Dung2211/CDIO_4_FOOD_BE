<?php

namespace App\Http\Controllers;

use App\Http\Requests\CapNhatVoucherRequest;
use App\Http\Requests\createQuanAnVoucherRequest;
use App\Http\Requests\deleteQuanAnVoucherRequest;
use App\Http\Requests\DoiTrangThaiVoucherRequest;
use App\Http\Requests\ThemMoiVoucherRequest;
use App\Http\Requests\updateQuanAnVoucherRequest;
use App\Http\Requests\XoaVoucherRequest;
use App\Models\PhanQuyen;
use App\Models\QuanAn;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    public function getData()
    {
        $id_chuc_nang = 17;
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
            $data = Voucher::all();
            return response()->json([
                'data'      => $data
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'Bạn cần đăng nhập hệ thống!'
            ]);
        }
    }

    public function getDataQuanAnVoucher()
    {
        $user = Auth::guard('sanctum')->user();
        $data = Voucher::where('id_quan_an', $user->id)->get();
        return response()->json([
            'data'      => $data
        ]);
    }

    public function createQuanAnVoucher(createQuanAnVoucherRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'status'    => 0,
                'message'   => 'Bạn cần đăng nhập hệ thống!'
            ]);
        } else {
            Voucher::create([
                'ma_code'               => $request->ma_code,
                'ten_voucher'           => $request->ten_voucher,
                'hinh_anh'              => $request->hinh_anh,
                'thoi_gian_bat_dau'     => $request->thoi_gian_bat_dau,
                'thoi_gian_ket_thuc'    => $request->thoi_gian_ket_thuc,
                'loai_giam'             => $request->loai_giam,
                'so_giam_gia'           => $request->so_giam_gia,
                'id_quan_an'            => $user->id,
                'so_tien_toi_da'        => $request->so_tien_toi_da,
                'don_hang_toi_thieu'    => $request->don_hang_toi_thieu,
            ]);

            return response()->json([
                'status'    => 1,
                'message'   => 'Thêm voucher thành công!',
            ]);
        }
    }

    public function updateQuanAnVoucher(updateQuanAnVoucherRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'status'    => 0,
                'message'   => 'Bạn cần đăng nhập hệ thống!'
            ]);
        } else {
            Voucher::find($request->id)->update([
                'ma_code'               => $request->ma_code,
                'ten_voucher'           => $request->ten_voucher,
                'hinh_anh'              => $request->hinh_anh,
                'thoi_gian_bat_dau'     => $request->thoi_gian_bat_dau,
                'thoi_gian_ket_thuc'    => $request->thoi_gian_ket_thuc,
                'loai_giam'             => $request->loai_giam,
                'so_giam_gia'           => $request->so_giam_gia,
                'id_quan_an'            => $user->id,
                'so_tien_toi_da'        => $request->so_tien_toi_da,
                'don_hang_toi_thieu'    => $request->don_hang_toi_thieu,
            ]);

            return response()->json([
                'status'    => 1,
                'message'   => 'Cập nhật voucher thành công!',
            ]);
        }
    }

    public function deleteQuanAnVoucher(deleteQuanAnVoucherRequest $request)
    {
        $check = Auth::guard('sanctum')->user();
        if (!$check) {
            return response()->json([
                'status'    => 0,
                'message'   => 'Bạn cần đăng nhập hệ thống!'
            ]);
        } else {
            $data = Voucher::find($request->id);
            if ($data) {
                $data->delete();
                return response()->json([
                    'status'    => 1,
                    'message'   => 'Xóa Voucher thành công!',
                ]);
            } else {
                return response()->json([
                    'status'    => 0,
                    'message'   => 'Voucher không tồn tại!',
                ]);
            }
        }
    }

    public function doiTrangThaiQuanAnVoucher(deleteQuanAnVoucherRequest $request)
    {
        $check = Auth::guard('sanctum')->user();
        if (!$check) {
            return response()->json([
                'status'    => 0,
                'message'   => 'Bạn cần đăng nhập hệ thống!'
            ]);
        } else {
            $data = Voucher::find($request->id);
            if ($data) {
                $data->update([
                    'tinh_trang'    => !$data->tinh_trang,
                ]);
                return response()->json([
                    'status'    => 1,
                    'message'   => 'Thay đổi trạng thái thành công!',
                ]);
            } else {
                return response()->json([
                    'status'    => 0,
                    'message'   => 'Voucher không tồn tại!',
                ]);
            }
        }
    }

    public function store(ThemMoiVoucherRequest $request)
    {
        $id_chuc_nang = 18;
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
            $data = Voucher::create([
                'ma_code'           => $request->ma_code,
                'ten_voucher'           => $request->ten_voucher,
                'hinh_anh'              => $request->hinh_anh,
                'id_quan_an'        => $check->id,
                'thoi_gian_bat_dau' => $request->thoi_gian_bat_dau,
                'thoi_gian_ket_thuc' => $request->thoi_gian_ket_thuc,
                'loai_giam'         => $request->loai_giam,
                'so_giam_gia'       => $request->so_giam_gia,
                'so_tien_toi_da'    => $request->so_tien_toi_da,
                'don_hang_toi_thieu' => $request->don_hang_toi_thieu,
            ]);

            return response()->json([
                'status'    => 1,
                'message'   => 'Thêm voucher thành công!',
            ]);
        }
    }
    public function destroy(XoaVoucherRequest $request)
    {
        $id_chuc_nang = 20;
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
            $data = Voucher::find($request->id);
            if ($data) {
                $data->delete();
                return response()->json([
                    'status'    => 1,
                    'message'   => 'Xóa Voucher thành công!',
                ]);
            } else {
                return response()->json([
                    'status'    => 0,
                    'message'   => 'Voucher không tồn tại!',
                ]);
            }
        }
    }
    public function update(CapNhatVoucherRequest $request)
    {
        $id_chuc_nang = 19;
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
            $data = Voucher::find($request->id);
            if ($data) {
                $data->update([
                    'ma_code'               => $request->ma_code,
                    'ten_voucher'           => $request->ten_voucher,
                    'hinh_anh'              => $request->hinh_anh,
                    'thoi_gian_bat_dau'     => $request->thoi_gian_bat_dau,
                    'thoi_gian_ket_thuc'    => $request->thoi_gian_ket_thuc,
                    'id_quan_an'            => $check->id,
                    'loai_giam'             => $request->loai_giam,
                    'so_giam_gia'           => $request->so_giam_gia,
                    'so_tien_toi_da'        => $request->so_tien_toi_da,
                    'don_hang_toi_thieu'    => $request->don_hang_toi_thieu,
                    'tinh_trang'            => $request->tinh_trang,
                ]);
                return response()->json([
                    'status'    => 1,
                    'message'   => 'Cập nhật Voucher thành công!',
                ]);
            } else {
                return response()->json([
                    'status'    => 0,
                    'message'   => 'Voucher không tồn tại!',
                ]);
            }
        }
    }
    public function changeStatus(DoiTrangThaiVoucherRequest $request)
    {
        $id_chuc_nang = 21;
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
            $data = Voucher::find($request->id);
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
                    'message'   => 'Voucher không tồn tại!',
                ]);
            }
        }
    }
}
