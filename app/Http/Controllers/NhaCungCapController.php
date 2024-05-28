<?php

namespace App\Http\Controllers;

use App\Http\Requests\NhaCungCap\CreateNhaCungCapRequest;
use App\Http\Requests\NhaCungCap\DeleteNhaCungCapRequest;
use App\Http\Requests\NhaCungCap\UpdateNhaCungCapRequest;
use App\Models\NhaCungCap;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class NhaCungCapController extends Controller
{
    public function  index()
    {
        return view('admin.page.nha_cung_cap.index');
    }

    public function store(CreateNhaCungCapRequest $request)
    {
        $data = $request->all();

        NhaCungCap::create($data);

        return response()->json([
            'status'    => true,
            'message'   => 'Đã tạo mới nhà cung cấp thành công!',
        ]);
    }

    public function data()
    {
        $data = NhaCungCap::all();

        return response()->json([
            'data'  => $data,
        ]);
    }

    public function update(UpdateNhaCungCapRequest $request)
    {

        $data    = $request->all();
        $nhaCungCap = NhaCungCap::find($request->id);
        $nhaCungCap->update($data);

        return response()->json([
            'status'    => true,
            'message'   => 'Đã cập nhật thành công nhà cung cấp!',
        ]);
    }

    public function destroy(DeleteNhaCungCapRequest $request)
    {
        $nhaCungCap = NhaCungCap::where('id', $request->id)->first();
        $nhaCungCap->delete();

        return response()->json([
            'status'    => true,
            'message'   => 'Đã xóa thành công nhà cung cấp!',
        ]);
    }

    public function doiTrangThai(Request $request)
    {
        $nhaCungCap = NhaCungCap::find($request->id);

        if($nhaCungCap) {
            $nhaCungCap->tinh_trang = !$nhaCungCap->tinh_trang;
            $nhaCungCap->save();
            return response()->json([
                'status'    => true,
                'message'   => 'Đã đổi trạng thái thành công!'
            ]);
        } else {
            return response()->json([
                'status'    => false,
                'message'   => 'Nhà cung cấp không tồn tại!'
            ]);
        }
    }


}
