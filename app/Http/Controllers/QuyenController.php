<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\CapNhapActionRequest;
use App\Http\Requests\ThemMoiQuyenRequest;
use App\Http\Requests\UpdateQuyenRequest;
use App\Http\Requests\XoaQuyenRequest;
use App\Models\Action;
use App\Models\Quyen;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuyenController extends Controller
{
    public function index()
    {
        return view('admin.page.quyen.index');
    }

    public function store(ThemMoiQuyenRequest $request)
    {
        Quyen::create([
            'ten_quyen'             => $request->ten_quyen,
            'list_id_quyen'         => $request->list_id_quyen,
        ]);

        return response()->json([
            'status' => true,
            'message' => "Thêm mới quyền thành công!"
        ]);

    }

    public function getData()
    {
        $data = Quyen::all();

        return response()->json([
            'data' => $data
        ]);
    }

    public function destroy(XoaQuyenRequest $request)
    {
        $quyen = Quyen::find($request->id);
        $quyen->delete();

        return response()->json([
            'status' => true,
            'message' => "Đã xóa quyền thành công!",
        ]);
    }

    public function update(UpdateQuyenRequest $request)
    {
        $data = $request->all();

        $quyen = Quyen::find($request->id);
        $quyen->update($data);

        return response()->json([
            'status' => true,
            'message' => "Đã cập nhật thành công!"
        ]);
    }
}
