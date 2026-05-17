<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteGioHangRequest;
use App\Http\Requests\ThemGioHangRequest;
use App\Http\Requests\TinhPhiShipRequest;
use App\Http\Requests\UpdateGioHangRequest;
use App\Models\ChiTietDonHang;
use App\Models\DiaChi;
use App\Models\DonHang;
use App\Models\MonAn;
use App\Models\QuanAn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;

class ChiTietDonHangController extends Controller
{
    public function getDonDatHang($id_quan_an)
    {
        $khachHang = Auth::guard('sanctum')->user();
        $quan_an     =   QuanAn::where('quan_ans.id', $id_quan_an) // quán đang lấy
                                ->where('quan_ans.tinh_trang', 1)  // Quán đang hoạt động
                                ->where('quan_ans.is_active', 1)   // Quán đã được kích hoạt
                                ->first();

        $mon_an     =   MonAn::where('mon_ans.id_quan_an', $id_quan_an)
                                ->where('mon_ans.tinh_trang', 1)  // Món ăn đang bán
                                ->get();

        $gio_hang     =   ChiTietDonHang::where('id_don_hang', 0)
                                        ->where('id_khach_hang', $khachHang->id)
                                        ->where('chi_tiet_don_hangs.id_quan_an', $id_quan_an)
                                        ->join('mon_ans', 'mon_ans.id', '=', 'chi_tiet_don_hangs.id_mon_an')
                                        ->select('chi_tiet_don_hangs.*', 'mon_ans.ten_mon_an')
                                        ->get();

        $dia_chi_khach = DiaChi::where('id_khach_hang', $khachHang->id)->get();

        if ($quan_an) {
            return response()->json([
                'quan_an'       =>  $quan_an,
                'mon_an'        =>  $mon_an,
                'gio_hang'      =>  $gio_hang,
                'dia_chi_khach' =>  $dia_chi_khach,
                'tong_tien'     =>  $gio_hang->sum('thanh_tien'),
                'status'        =>  true
            ]);
        } else {
            return response()->json([
                'status'    =>  false
            ]);
        }
    }

    public function tinhPhiShip(TinhPhiShipRequest $request)
    {
        try {
            $link_get = 'https://api.openrouteservice.org/geocode/search';
            $dia_chi_quan  = QuanAn::where('id', $request->id_quan_an)->first();
            
            // 🚀 SỬA Ở ĐÂY: Join bảng để lấy full địa chỉ của khách hàng
            $dia_chi_khach = DiaChi::where('dia_chis.id', $request->id_dia_chi_khach)
                                ->join('quan_huyens', 'dia_chis.id_quan_huyen', 'quan_huyens.id')
                                ->join('tinh_thanhs', 'quan_huyens.id_tinh_thanh', 'tinh_thanhs.id')
                                ->select('dia_chis.*', 'quan_huyens.ten_quan_huyen', 'tinh_thanhs.ten_tinh_thanh')
                                ->first();

            // Ghép thành chuỗi địa chỉ hoàn chỉnh
            $full_dia_chi_khach = $dia_chi_khach->dia_chi . ', ' . $dia_chi_khach->ten_quan_huyen . ', ' . $dia_chi_khach->ten_tinh_thanh;
            $full_dia_chi_quan  = $dia_chi_quan->dia_chi . ', Đà Nẵng'; // Đề phòng địa chỉ quán viết tắt

            $client = new Client();

            // 1. TÌM TỌA ĐỘ QUÁN
            $response_quan = $client->request('GET', $link_get, [
                'http_errors' => false, 
                'headers' => ['User-Agent' => 'MyApp/1.0', 'Accept' => 'application/json'],
                'query' => ['api_key' => '5b3ce3597851110001cf62484c960a399b1d44f4829554f302e513b8', 'text' => $full_dia_chi_quan, 'size' => 1]
            ]);
            $body_quan = json_decode($response_quan->getBody()->getContents(), true);
            $toa_do_quan = $body_quan['features'][0]['geometry']['coordinates'] ?? null;

            // 2. TÌM TỌA ĐỘ KHÁCH
            $response_khach = $client->request('GET', $link_get, [
                'http_errors' => false,
                'headers' => ['User-Agent' => 'MyApp/1.0', 'Accept' => 'application/json'],
                'query' => ['api_key' => '5b3ce3597851110001cf62484c960a399b1d44f4829554f302e513b8', 'text' => $full_dia_chi_khach, 'size' => 1]
            ]);
            $body_khach = json_decode($response_khach->getBody()->getContents(), true);
            $toa_do_khach = $body_khach['features'][0]['geometry']['coordinates'] ?? null;

            // 🔴 NẾU BẢN ĐỒ KHÔNG TÌM RA ĐỊA CHỈ THÌ ÁP DỤNG PHÍ SHIP MẶC ĐỊNH
            if(!$toa_do_quan || !$toa_do_khach) {
                return response()->json(['status' => true, 'phi_ship' => 15000]);
            }

            // 3. TÍNH KHOẢNG CÁCH
            $link_directions = 'https://api.openrouteservice.org/v2/directions/driving-car';
            $response_distance = $client->request('POST', $link_directions, [
                'http_errors' => false,
                'headers' => ['Authorization' => '5b3ce3597851110001cf62484c960a399b1d44f4829554f302e513b8', 'Content-Type' => 'application/json', 'Accept' => 'application/json'],
                'json' => ['coordinates' => [$toa_do_quan, $toa_do_khach]]
            ]);

            $body_dist = json_decode($response_distance->getBody()->getContents(), true);
            $khoang_cach_met = $body_dist['routes'][0]['summary']['distance'] ?? 0;

            if ($khoang_cach_met == 0) {
                return response()->json(['status' => true, 'phi_ship' => 15000]);
            }

            $khoang_cach_km  = $khoang_cach_met / 1000;

            // Chốt chặn 20km
            if ($khoang_cach_km > 20) {
                $phi_ship = 15000;
            } else {
                $phi_ship = round($khoang_cach_km * 25000, -3); // 25.000đ/1km
                if ($phi_ship < 15000) {
                    $phi_ship = 15000;
                }
            }

            return response()->json([
                'status'        => true,
                'phi_ship'      => $phi_ship
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'        => true,
                'phi_ship'      => 15000
            ]);
        }
    }

    public function themGioHang(ThemGioHangRequest $request)
    {
        $khachHang = Auth::guard('sanctum')->user();
        $monAn     = MonAn::where('id', $request->id)->first();
        $check     = ChiTietDonHang::where('id_khach_hang', $khachHang->id)
            ->where('id_mon_an', $request->id)
            ->where('id_don_hang', 0) 
            ->first();
        if ($check) {
            $check->so_luong += 1;
            $check->thanh_tien = $check->don_gia * $check->so_luong;
            $check->save();

            return response()->json([
                'status'    =>  true,
                'message'   =>  'Cập nhật số lượng món ăn thành công'
            ]);
        } else {
            ChiTietDonHang::create([
                'id_mon_an'     =>  $request->id,
                'id_quan_an'    =>  $monAn->id_quan_an,
                'don_gia'       =>  $monAn->gia_khuyen_mai,
                'so_luong'      =>  1,
                'thanh_tien'    =>  $monAn->gia_khuyen_mai,
                'id_khach_hang' =>  $khachHang->id,
            ]);

            return response()->json([
                'status'    =>  true,
                'message'   =>  'Thêm món ăn vào giỏ hàng thành công'
            ]);
        }
    }

    public function updateGioHang(UpdateGioHangRequest $request)
    {
        $khachHang  = Auth::guard('sanctum')->user();
        $mon_an     =   MonAn::where('id', $request->id_mon_an)
                                ->where('mon_ans.tinh_trang', 1)
                                ->first();
        if(!$mon_an) {
            return response()->json([
                'status'    => 0,
                'message'   => "Món ăn không tồn tại hoặc đã nhưng bán!!!!"
            ]);
        } else {
            ChiTietDonHang::where('id', $request->id)->update([
                'don_gia'       => $mon_an->gia_khuyen_mai > 0 ? $mon_an->gia_khuyen_mai : $mon_an->gia_ban,
                'so_luong'      => $request->so_luong,
                'thanh_tien'    => $request->so_luong * ($mon_an->gia_khuyen_mai > 0 ? $mon_an->gia_khuyen_mai : $mon_an->gia_ban),
                'ghi_chu'       => $request->ghi_chu,
            ]);

            return response()->json([
                'status'    => 1,
                'message'   => "Cập nhật giỏ hàng thành công!!"
            ]);
        }
    }

    public function deleteGioHang(DeleteGioHangRequest $request)
    {
        ChiTietDonHang::where('id', $request->all())->delete();
        return response()->json([
            'status'    => 1,
            'message'   => "Đã hủy món " . $request->ten_mon_an . " thành công!!",
        ]);
    }

   public function xacNhanDatHang($id_quan_an, $id_dia_chi_khach)
    {
        $gio_hang = ChiTietDonHang::where('id_don_hang', 0)
                                    ->where('id_khach_hang', Auth::guard('sanctum')->user()->id)
                                    ->where('chi_tiet_don_hangs.id_quan_an', $id_quan_an)
                                    ->join('mon_ans', 'mon_ans.id', '=', 'chi_tiet_don_hangs.id_mon_an')
                                    ->select('chi_tiet_don_hangs.*', 'mon_ans.ten_mon_an')
                                    ->get();

        try {
            $link_get = 'https://api.openrouteservice.org/geocode/search';
            $dia_chi_quan  = QuanAn::where('id', $id_quan_an)->first();
            
            // 🚀 SỬA Ở ĐÂY CHO ĐỒNG BỘ: Join bảng để lấy full địa chỉ lúc xác nhận đơn hàng
            $dia_chi_khach = DiaChi::where('dia_chis.id', $id_dia_chi_khach)
                                ->join('quan_huyens', 'dia_chis.id_quan_huyen', 'quan_huyens.id')
                                ->join('tinh_thanhs', 'quan_huyens.id_tinh_thanh', 'tinh_thanhs.id')
                                ->select('dia_chis.*', 'quan_huyens.ten_quan_huyen', 'tinh_thanhs.ten_tinh_thanh')
                                ->first();

            $full_dia_chi_khach = $dia_chi_khach->dia_chi . ', ' . $dia_chi_khach->ten_quan_huyen . ', ' . $dia_chi_khach->ten_tinh_thanh;
            $full_dia_chi_quan  = $dia_chi_quan->dia_chi . ', Đà Nẵng';

            $client = new Client();

            $response_quan = $client->request('GET', $link_get, ['http_errors' => false, 'headers' => ['User-Agent' => 'MyApp/1.0', 'Accept' => 'application/json'], 'query' => ['api_key' => '5b3ce3597851110001cf62484c960a399b1d44f4829554f302e513b8', 'text' => $full_dia_chi_quan, 'size' => 1]]);
            $toa_do_quan = json_decode($response_quan->getBody()->getContents(), true)['features'][0]['geometry']['coordinates'] ?? null;

            $response_khach = $client->request('GET', $link_get, ['http_errors' => false, 'headers' => ['User-Agent' => 'MyApp/1.0', 'Accept' => 'application/json'], 'query' => ['api_key' => '5b3ce3597851110001cf62484c960a399b1d44f4829554f302e513b8', 'text' => $full_dia_chi_khach, 'size' => 1]]);
            $toa_do_khach = json_decode($response_khach->getBody()->getContents(), true)['features'][0]['geometry']['coordinates'] ?? null;

            if(!$toa_do_quan || !$toa_do_khach) {
                $phi_ship = 15000;
            } else {
                $link_directions = 'https://api.openrouteservice.org/v2/directions/driving-car';
                $response_distance = $client->request('POST', $link_directions, ['http_errors' => false, 'headers' => ['Authorization' => '5b3ce3597851110001cf62484c960a399b1d44f4829554f302e513b8', 'Content-Type' => 'application/json', 'Accept' => 'application/json'], 'json' => ['coordinates' => [$toa_do_quan, $toa_do_khach]]]);

                $khoang_cach_met = json_decode($response_distance->getBody()->getContents(), true)['routes'][0]['summary']['distance'] ?? 0;
                
                if ($khoang_cach_met == 0) {
                    $phi_ship = 15000;
                } else {
                    $khoang_cach_km = $khoang_cach_met / 1000;
                    if ($khoang_cach_km > 20) {
                        $phi_ship = 15000;
                    } else {
                        $phi_ship = round($khoang_cach_km * 25000, -3);
                        if ($phi_ship < 15000) $phi_ship = 15000;
                    }
                }
            }

        } catch (\Exception $e) {
            $phi_ship  = 15000;
        }

        $loai_thanh_toan = request('thanh_toan'); 
        $trang_thai_thanh_toan = ($loai_thanh_toan == 'momo') ? 1 : 0; // Nếu là momo thì gán bằng 1 (Đã thanh toán)

        $donHang = DonHang::create([
            'ma_don_hang'       =>  'DH' . time(),
            'id_khach_hang'     =>  Auth::guard('sanctum')->user()->id,
            'id_voucher'        =>  0,
            'id_shipper'        =>  0,
            'id_quan_an'        =>  $id_quan_an,
            'id_dia_chi_nhan'   =>  $id_dia_chi_khach,
            'ten_nguoi_nhan'    =>  $dia_chi_khach->ten_nguoi_nhan,
            'so_dien_thoai'     =>  $dia_chi_khach->so_dien_thoai,
            'tien_hang'         =>  $gio_hang->sum('thanh_tien'),
            'phi_ship'          =>  $phi_ship,
            'tong_tien'         =>  $gio_hang->sum('thanh_tien') + $phi_ship,
            'is_thanh_toan'     =>  $trang_thai_thanh_toan,
            'tinh_trang'        =>  0,   
            'trang_thai_quan'   =>  0,   
        ]);

        ChiTietDonHang::where('id_don_hang', 0)
                    ->where('id_khach_hang', Auth::guard('sanctum')->user()->id)
                    ->where('chi_tiet_don_hangs.id_quan_an', $id_quan_an)
                    ->update([
                        'id_don_hang' => $donHang->id,
                    ]);

        return response()->json([
            'status'    =>  1,
            'message'   =>  'Đã xác nhận đơn hàng thành công!'
        ]);
    }
}