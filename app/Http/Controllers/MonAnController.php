<?php

namespace App\Http\Controllers;

use App\Http\Requests\changeStatusMonAnRequest;
use App\Http\Requests\createMonAnRequest;
use App\Http\Requests\deleteMonAnRequest;
use App\Http\Requests\updateMonAnRequest;
use App\Models\MonAn;
use App\Models\QuanAn;
use Illuminate\Http\Request;

class MonAnController extends Controller
{
    public function searchNguoiDung(Request $request){
        $noi_dung_tim = '%'. $request->noi_dung_tim . '%';
        $data_mon_an   =  MonAn::where('ten_mon_an', 'like', $noi_dung_tim)
                                ->join('quan_ans', 'quan_ans.id', 'mon_ans.id_quan_an')
                                ->select('mon_ans.*', 'quan_ans.ten_quan_an')
                                ->orderBy('mon_ans.gia_khuyen_mai')
                                ->get();
                            
        $data_quan_an = QuanAn::where('ten_quan_an', 'like', $noi_dung_tim)
                            ->select('quan_ans.id', 'quan_ans.ten_quan_an', 'quan_ans.hinh_anh', 'quan_ans.dia_chi')
                            ->get();
        return response()->json([
            'mon_an'  => $data_mon_an,
            'quan_an'  => $data_quan_an,
        ]);

    }
    public function getData()
    {
        $data = MonAn::get();
        return response()->json([
            'data' => $data
        ]);
    }

    public function store(createMonAnRequest $request)
    {
        $monAn = MonAn::create([
            'ten_mon_an'        => $request->ten_mon_an,
            'slug_mon_an'       => $request->slug_mon_an,
            'gia_ban'           => $request->gia_ban,
            'gia_khuyen_mai'    => $request->gia_khuyen_mai,
            'id_quan_an'        => $request->id_quan_an,
            'tinh_trang'        => $request->tinh_trang,
            'hinh_anh'          => $request->hinh_anh,
            'id_danh_muc'       => $request->id_danh_muc,
        ]);
        return response()->json([
            'message' => 'Thêm ' . $request->ten_mon_an . ' thành công'
        ]);
    }

    public function update(updateMonAnRequest $request)
    {
        $monAn = MonAn::where('id', $request->id)->update([
            'ten_mon_an'        => $request->ten_mon_an,
            'slug_mon_an'       => $request->slug_mon_an,
            'gia_ban'           => $request->gia_ban,
            'gia_khuyen_mai'    => $request->gia_khuyen_mai,
            'id_quan_an'        => $request->id_quan_an,
            'tinh_trang'        => $request->tinh_trang,
            'hinh_anh'          => $request->hinh_anh,
            'id_danh_muc'       => $request->id_danh_muc,
        ]);
        return response()->json([
            'message' => 'Cập nhật ' . $request->ten_mon_an . ' thành công'
        ]);
    }

    public function destroy(deleteMonAnRequest $request)
    {
        $monAn = MonAn::where('id', $request->id)->delete();
        return response()->json([
            'message' => 'Xóa ' . $request->ten_mon_an . ' thành công'
        ]);
    }

    public function changeStatus(changeStatusMonAnRequest $request)
    {
        $monAn = MonAn::where('id', $request->id)->first();
        if ($monAn->tinh_trang == 1) {
            $monAn->tinh_trang = 0;
            $monAn->save();
        } else {
            $monAn->tinh_trang = 1;
            $monAn->save();
        }
        return response()->json([
            'message' => 'Cập nhật trạng thái thành công'
        ]);
    }
}
