<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vouchers')->delete();

        DB::table('vouchers')->truncate();

        DB::table('vouchers')->insert([
            [
                'id' => 1,
                'ma_code' => 'SAVE10',
                'thoi_gian_bat_dau' => '2025-05-01',
                'thoi_gian_ket_thuc' => '2025-06-01',
                'loai_giam' => 1,
                'so_giam_gia' => 10,
                'so_tien_toi_da' => 30000,
                'don_hang_toi_thieu' => 100000,
                'tinh_trang' => 1,
                'id_quan_an' => 5,
                'hinh_anh' => 'https://thuvienmuasam.com/uploads/default/original/2X/e/eb5195361a1c0e308b3acf2426984e87fb5f8cb7.jpeg',
                'ten_voucher' => 'Giảm 10% cho đơn từ 100k'
            ],
            [
                'id' => 2,
                'ma_code' => 'FREESHIP50',
                'thoi_gian_bat_dau' => '2025-05-01',
                'thoi_gian_ket_thuc' => '2025-06-30',
                'loai_giam' => 0,
                'so_giam_gia' => 20000,
                'so_tien_toi_da' => 20000,
                'don_hang_toi_thieu' => 50000,
                'tinh_trang' => 1,
                'id_quan_an' => 11,
                'hinh_anh' => 'https://thuvienmuasam.com/uploads/default/original/2X/e/eb5195361a1c0e308b3acf2426984e87fb5f8cb7.jpeg',
                'ten_voucher' => 'Miễn phí vận chuyển đơn từ 50k'
            ],
            [
                'id' => 3,
                'ma_code' => 'WELCOME20',
                'thoi_gian_bat_dau' => '2025-05-05',
                'thoi_gian_ket_thuc' => '2025-07-05',
                'loai_giam' => 1,
                'so_giam_gia' => 20,
                'so_tien_toi_da' => 40000,
                'don_hang_toi_thieu' => 80000,
                'tinh_trang' => 1,
                'id_quan_an' => 2,
                'hinh_anh' => 'https://thuvienmuasam.com/uploads/default/original/2X/e/eb5195361a1c0e308b3acf2426984e87fb5f8cb7.jpeg',
                'ten_voucher' => 'Chào mừng khách mới - giảm 20%'
            ],
            [
                'id' => 4,
                'ma_code' => 'FLASHSALE30',
                'thoi_gian_bat_dau' => '2025-05-10',
                'thoi_gian_ket_thuc' => '2025-05-15',
                'loai_giam' => 1,
                'so_giam_gia' => 30,
                'so_tien_toi_da' => 50000,
                'don_hang_toi_thieu' => 70000,
                'tinh_trang' => 1,
                'id_quan_an' => 17,
                'hinh_anh' => 'https://thuvienmuasam.com/uploads/default/original/2X/e/eb5195361a1c0e308b3acf2426984e87fb5f8cb7.jpeg',
                'ten_voucher' => 'Flash Sale 30% - Chỉ 5 ngày'
            ],
            [
                'id' => 5,
                'ma_code' => 'SUMMER15',
                'thoi_gian_bat_dau' => '2025-06-01',
                'thoi_gian_ket_thuc' => '2025-07-15',
                'loai_giam' => 1,
                'so_giam_gia' => 15,
                'so_tien_toi_da' => 35000,
                'don_hang_toi_thieu' => 60000,
                'tinh_trang' => 1,
                'id_quan_an' => 8,
                'hinh_anh' => 'https://thuvienmuasam.com/uploads/default/original/2X/e/eb5195361a1c0e308b3acf2426984e87fb5f8cb7.jpeg',
                'ten_voucher' => 'Ưu đãi mùa hè 15%'
            ],
            [
                'id' => 6,
                'ma_code' => 'VIPCUSTOMER25',
                'thoi_gian_bat_dau' => '2025-05-20',
                'thoi_gian_ket_thuc' => '2025-07-20',
                'loai_giam' => 1,
                'so_giam_gia' => 25,
                'so_tien_toi_da' => 70000,
                'don_hang_toi_thieu' => 120000,
                'tinh_trang' => 1,
                'id_quan_an' => 19,
                'hinh_anh' => 'https://thuvienmuasam.com/uploads/default/original/2X/e/eb5195361a1c0e308b3acf2426984e87fb5f8cb7.jpeg',
                'ten_voucher' => 'Khách VIP giảm 25%'
            ],
            [
                'id' => 7,
                'ma_code' => 'BUYMORE5',
                'thoi_gian_bat_dau' => '2025-05-10',
                'thoi_gian_ket_thuc' => '2025-08-10',
                'loai_giam' => 1,
                'so_giam_gia' => 5,
                'so_tien_toi_da' => 15000,
                'don_hang_toi_thieu' => 50000,
                'tinh_trang' => 1,
                'id_quan_an' => 6,
                'hinh_anh' => 'https://thuvienmuasam.com/uploads/default/original/2X/e/eb5195361a1c0e308b3acf2426984e87fb5f8cb7.jpeg',
                'ten_voucher' => 'Mua nhiều giảm thêm 5%'
            ],
            [
                'id' => 8,
                'ma_code' => 'LUCKYDAY40',
                'thoi_gian_bat_dau' => '2025-07-01',
                'thoi_gian_ket_thuc' => '2025-07-31',
                'loai_giam' => 1,
                'so_giam_gia' => 40,
                'so_tien_toi_da' => 80000,
                'don_hang_toi_thieu' => 90000,
                'tinh_trang' => 1,
                'id_quan_an' => 4,
                'hinh_anh' => 'https://thuvienmuasam.com/uploads/default/original/2X/e/eb5195361a1c0e308b3acf2426984e87fb5f8cb7.jpeg',
                'ten_voucher' => 'Ngày may mắn giảm 40%'
            ],
            [
                'id' => 9,
                'ma_code' => 'TETSALE2025',
                'thoi_gian_bat_dau' => '2025-01-15',
                'thoi_gian_ket_thuc' => '2025-02-15',
                'loai_giam' => 1,
                'so_giam_gia' => 20,
                'so_tien_toi_da' => 60000,
                'don_hang_toi_thieu' => 100000,
                'tinh_trang' => 1,
                'id_quan_an' => 9,
                'hinh_anh' => 'https://thuvienmuasam.com/uploads/default/original/2X/e/eb5195361a1c0e308b3acf2426984e87fb5f8cb7.jpeg',
                'ten_voucher' => 'Giảm giá Tết 2025'
            ],
            [
                'id' => 10,
                'ma_code' => 'BLACKFRIDAY50',
                'thoi_gian_bat_dau' => '2025-11-20',
                'thoi_gian_ket_thuc' => '2025-11-30',
                'loai_giam' => 1,
                'so_giam_gia' => 50,
                'so_tien_toi_da' => 100000,
                'don_hang_toi_thieu' => 150000,
                'tinh_trang' => 1,
                'id_quan_an' => 14,
                'hinh_anh' => 'https://thuvienmuasam.com/uploads/default/original/2X/e/eb5195361a1c0e308b3acf2426984e87fb5f8cb7.jpeg',
                'ten_voucher' => 'Black Friday giảm 50%'
            ],
            [
                'id' => 11,
                'ma_code' => 'BACK2SCHOOL10',
                'thoi_gian_bat_dau' => '2025-08-01',
                'thoi_gian_ket_thuc' => '2025-09-15',
                'loai_giam' => 1,
                'so_giam_gia' => 10,
                'so_tien_toi_da' => 25000,
                'don_hang_toi_thieu' => 60000,
                'tinh_trang' => 1,
                'id_quan_an' => 13,
                'hinh_anh' => 'https://thuvienmuasam.com/uploads/default/original/2X/e/eb5195361a1c0e308b3acf2426984e87fb5f8cb7.jpeg',
                'ten_voucher' => 'Mùa tựu trường - giảm 10%'
            ],
            [
                'id' => 12,
                'ma_code' => 'BIRTHDAYGIFT20',
                'thoi_gian_bat_dau' => '2025-01-01',
                'thoi_gian_ket_thuc' => '2025-12-31',
                'loai_giam' => 0,
                'so_giam_gia' => 50000,
                'so_tien_toi_da' => 50000,
                'don_hang_toi_thieu' => 80000,
                'tinh_trang' => 1,
                'id_quan_an' => 7,
                'hinh_anh' => 'https://thuvienmuasam.com/uploads/default/original/2X/e/eb5195361a1c0e308b3acf2426984e87fb5f8cb7.jpeg',
                'ten_voucher' => 'Quà sinh nhật 50k'
            ],
            [
                'id' => 13,
                'ma_code' => 'LOVEYOU15',
                'thoi_gian_bat_dau' => '2025-02-01',
                'thoi_gian_ket_thuc' => '2025-02-14',
                'loai_giam' => 1,
                'so_giam_gia' => 15,
                'so_tien_toi_da' => 30000,
                'don_hang_toi_thieu' => 70000,
                'tinh_trang' => 1,
                'id_quan_an' => 18,
                'hinh_anh' => 'https://thuvienmuasam.com/uploads/default/original/2X/e/eb5195361a1c0e308b3acf2426984e87fb5f8cb7.jpeg',
                'ten_voucher' => 'Valentine ngọt ngào giảm 15%'
            ],
            [
                'id' => 14,
                'ma_code' => 'NEWYEAR2025',
                'thoi_gian_bat_dau' => '2024-12-25',
                'thoi_gian_ket_thuc' => '2025-01-10',
                'loai_giam' => 0,
                'so_giam_gia' => 70000,
                'so_tien_toi_da' => 70000,
                'don_hang_toi_thieu' => 120000,
                'tinh_trang' => 1,
                'id_quan_an' => 1,
                'hinh_anh' => 'https://thuvienmuasam.com/uploads/default/original/2X/e/eb5195361a1c0e308b3acf2426984e87fb5f8cb7.jpeg',
                'ten_voucher' => 'Mừng năm mới 70k'
            ],
            [
                'id' => 15,
                'ma_code' => 'FAMILYDAY30',
                'thoi_gian_bat_dau' => '2025-06-20',
                'thoi_gian_ket_thuc' => '2025-06-28',
                'loai_giam' => 1,
                'so_giam_gia' => 30,
                'so_tien_toi_da' => 60000,
                'don_hang_toi_thieu' => 90000,
                'tinh_trang' => 1,
                'id_quan_an' => 10,
                'hinh_anh' => 'https://thuvienmuasam.com/uploads/default/original/2X/e/eb5195361a1c0e308b3acf2426984e87fb5f8cb7.jpeg',
                'ten_voucher' => 'Ưu đãi ngày gia đình 30%'
            ]
        ]);
    }
}
