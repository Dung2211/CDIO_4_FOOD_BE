<?php

namespace App\Http\Controllers;

use App\Http\Requests\CauHinhQuanAnRequest;
use App\Models\QuanAn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CauHinhQuanAnController extends Controller
{
    public function cauHinhQuanAn(CauHinhQuanAnRequest $request)
    {
        $user = Auth::guard('sanctum')->user();

        QuanAn::where('id', $user->id)->update([
            'gio_mo_cua'    => $request->gio_mo_cua,
            'gio_dong_cua'  => $request->gio_dong_cua,
            'dia_chi'       => $request->dia_chi,
            'toa_do_x'      => $request->toa_do_x,
            'toa_do_y'      => $request->toa_do_y,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật thành công',
        ]);
    }

    public function cauHinhQuanAnData()
    {
        $user = Auth::guard('sanctum')->user();

        $quan_an = QuanAn::where('id', $user->id)->first();

        return response()->json([
            'data'   => $quan_an,
        ]);
    }
}
