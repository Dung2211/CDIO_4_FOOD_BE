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
}
