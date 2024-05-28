<?php

namespace App\Http\Controllers;

use App\Http\Requests\NhapHang\KiemTraIdNhapHangRequest;
use App\Http\Requests\NhapHang\UpdateChiTietHoaDonNhapHangRequest;
use App\Models\ChiTietHoaDonNhapHang;
use App\Models\HoaDonNhapHang;
use App\Models\MonAn;
use App\Models\NhaCungCap;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PHPViet\NumberToWords\Transformer;
use Illuminate\Support\Str;

class HoaDonNhapHangController extends Controller
{

    public function index()
    {
        return view('admin.page.nhap_hang.index');
    }

    public function getData()
    {
        $data =  ChiTietHoaDonNhapHang::where('id_hoa_don_nhap_hang', 0)
                                      ->where('trang_thai', 0)
                                      ->select('chi_tiet_hoa_don_nhap_hangs.*')
                                      ->get();
        $tong_tien = 0;
        foreach ($data as $key => $value) {
            $tong_tien = $tong_tien + $value->thanh_tien;
        }
        $transformer = new Transformer();

        return response()->json([
            'data'      => $data,
            'tong_tien' => $tong_tien,
            'tien_chu'  => $transformer->toCurrency($tong_tien),
        ]);
    }


    public function addSanPhamNhapHang(Request $request)
    {
        $monAn  = MonAn::find($request->id);
        $hoaDonNhapHang = ChiTietHoaDonNhapHang::where('id_mon_an', $monAn->id)
                                               ->where('id_hoa_don_nhap_hang', 0)
                                               ->where('trang_thai', 0)
                                               ->first();
        if($hoaDonNhapHang) {
            $hoaDonNhapHang->so_luong_nhap  = $hoaDonNhapHang->so_luong_nhap + 1;
            $hoaDonNhapHang->thanh_tien   = $hoaDonNhapHang->so_luong_nhap * $hoaDonNhapHang->don_gia_nhap;
            $hoaDonNhapHang->save();
        } else {
            ChiTietHoaDonNhapHang::create([
                'id_mon_an'             => $monAn->id,
                'ten_mon_an'            => $monAn->ten_mon,
                'so_luong_nhap'         => 1,
                'don_gia_nhap'          => $monAn->gia_ban,
                'thanh_tien'            => $monAn->gia_ban * 1,
                'id_hoa_don_nhap_hang'  => 0,
            ]);
        }

        return response()->json([
            'status' => true,
            'message'=> "Thêm mới món ăn vào hóa đơn nhập hàng thành công!"
        ]);
    }

    public function deleteMonAnNhapHang(KiemTraIdNhapHangRequest $request)
    {
        $chiTietHoaDonNhap = ChiTietHoaDonNhapHang::find($request->id);

        $chiTietHoaDonNhap->delete();

        return response()->json([
            'status' => true,
            'message' => "Xóa món ăn khỏi hóa đơn nhập thành công!"
        ]);
    }

    public function updateChiTietHoaDonNhap(UpdateChiTietHoaDonNhapHangRequest $request)
    {
        $chiTietHoaDonNhap = ChiTietHoaDonNhapHang::find($request->id);
        if($chiTietHoaDonNhap->id_hoa_don_nhap_hang == 0 && $chiTietHoaDonNhap->trang_thai == 0) {
            $chiTietHoaDonNhap->update([
                'so_luong_nhap' => $request->so_luong_nhap,
                'don_gia_nhap'  => $request->don_gia_nhap,
                'thanh_tien'    => $request->don_gia_nhap * $request->so_luong_nhap,
                'ghi_chu'       => $request->ghi_chu
            ]);

            return response()->json([
                'status'    => 1,
                'message'   => 'Đã cập nhật món ăn thành công!',
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'Có lỗi không mong muốn xảy ra!',
            ]);
        }
    }

    public function nhapHangReal(Request $request)
    {
        $data = $request->all();
        $data['ma_hoa_don_nhap_hang']   = Str::uuid();
        $data['ngay_nhap_hang']         = Carbon::now();

        $chiTietNhapHang = ChiTietHoaDonNhapHang::where('id_hoa_don_nhap_hang', 0)
                                                    ->where('trang_thai', 0)
                                                    ->get();
        if(count($chiTietNhapHang) > 0){
            $nhapHang = HoaDonNhapHang::create($data);

            if($nhapHang){
                foreach($chiTietNhapHang as $key => $value){
                    $value->id_hoa_don_nhap_hang = $nhapHang->id;
                    $value->trang_thai = 1;
                    $value->save();
                }
                return response()->json([
                    'status'    => 1,
                    'message'   => 'Đã nhập hàng thành công!',
                ]);
            }else{
                return response()->json([
                    'status'    => 0,
                    'message'   => 'Đã có lỗi hệ thống!',
                ]);
            }
        }else{
            return response()->json([
                'status'    => 0,
                'message'   => 'Hóa đơn hàng này đã được người khác nhập!',
            ]);
        }
    }
}
