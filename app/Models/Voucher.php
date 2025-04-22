<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = 'vouchers';
    protected $fillable = [
        'ma_code',
        'thoi_gian_bat_dau',
        'thoi_gian_ket_thuc',
        'loai_giam',
        'id_quan_an',
        'so_giam_gia',
        'so_tien_toi_da',
        'don_hang_toi_thieu',
        'tinh_trang',
        'hinh_anh',
        'ten_voucher',
    ];

    const GIAM_PHAN_TRAM = 1;
    const GIAM_TIEN_MAT = 0;
}
