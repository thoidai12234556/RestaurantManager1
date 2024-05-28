<?php

namespace App\Http\Controllers;

use App\Http\Requests\MonAn\CreateMonAnRequest;
use App\Http\Requests\MonAn\UpdateMonAnRequest;
use App\Models\Ban;
use App\Models\DanhMuc;
use App\Models\KhuVuc;
use App\Models\MonAn;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use function GuzzleHttp\Promise\all;

class MonAnController extends Controller
{
    public function index()
    {
        $danhMuc = DanhMuc::get();
        return view('admin.page.mon_an.index', compact('danhMuc'));
    }

    public function index_vue()
    {
        $danhMuc = DanhMuc::get();

        return view('admin.page.mon_an.index_vue', compact('danhMuc'));
    }

    public function getData()
    {
        $list = MonAn::join('danh_mucs', 'danh_mucs.id', 'mon_ans.id_danh_muc')
                     ->select('mon_ans.*', 'danh_mucs.ten_danh_muc')
                     ->get();

        return response()->json([
            'list'  => $list
        ]);
    }

    public function doiTrangThai(Request $request)
    {
        $monAn = MonAn::find($request->id);

        if($monAn) {
            $monAn->tinh_trang = !$monAn->tinh_trang;
            $monAn->save();
            return response()->json([
                'status'    => true,
                'message'   => 'Đã đổi trạng thái thành công!'
            ]);
        } else {
            return response()->json([
                'status'    => false,
                'message'   => 'Món ăn không tồn tại!'
            ]);
        }
    }

    public function edit(Request $request)
    {
        $monAn = MonAn::find($request->id);

        if($monAn) {
            return response()->json([
                'status'    => true,
                'message'   => 'Đã lấy được dữ liệu!',
                'monAn'    => $monAn,
            ]);
        } else {
            return response()->json([
                'status'    => false,
                'message'   => 'Món ăn không tồn tại!'
            ]);
        }
    }

    public function destroy(Request $request)
    {
        $monAn = MonAn::find($request->id);

        if($monAn) {
            $monAn->delete();
            return response()->json([
                'status'    => true,
                'message'   => 'Đã xóa món ăn thành công!'
            ]);
        } else {
            return response()->json([
                'status'    => false,
                'message'   => 'Món ăn không tồn tại!'
            ]);
        }
    }

    public function store(CreateMonAnRequest $request)
    {
        $data = $request->all();
        $file = $request->file('hinh_anh');

        $ten_hinh_anh       =   Str::uuid() . '-' . $request->slug_mon . '.' . $file->getClientOriginalExtension();
        $data['hinh_anh']   = $ten_hinh_anh;
        $file->move('hinh-mon-an', $ten_hinh_anh);
        MonAn::create($data);

        return response()->json([
            'status'    => true,
            'message'   => 'Đã tạo mới thành công!',
        ]);
    }

    public function checkSlug(Request $request)
    {
        if(isset($request->id)) {
            $check = MonAn::where('slug_mon', $request->slug_mon)
                        ->where('id', '<>', $request->id)
                        ->first();
        } else {
            $check = MonAn::where('slug_mon', $request->slug_mon)->first();
        }

        if($check) {
            return response()->json([
                'status'    => false,
                'message'   => 'Món ăn đã tồn tại!',
            ]);
        } else {
            return response()->json([
                'status'    => true,
                'message'   => 'Món ăn có thể sử dụng!',
            ]);
        }
    }

    public function update(UpdateMonAnRequest $request)
    {
        $monAn = MonAn::where('id', $request->id)->first();

        $data = $request->all();

        $monAn->update($data);

        return response()->json([
            'status'    => true,
            'message'   => 'Đã cập nhật được thông tin!',
        ]);
    }

    public function search(Request $request)
    {
        $list = MonAn::join('danh_mucs', 'danh_mucs.id', 'mon_ans.id_danh_muc')
                     ->select('mon_ans.*', 'danh_mucs.ten_danh_muc')
                     ->where('ten_mon', 'like', '%' . $request->key_search . '%')
                     ->orWhere('danh_mucs.ten_danh_muc', 'like', '%' . $request->key_search . '%')
                     ->get();

        return response()->json([
            'list'  => $list
        ]);
    }
    public function deleteCheckbox(Request $request)
    {
        $data = $request->all();

        $str = "";

        foreach ($data as $key => $value) {
            if(isset($value['check'])) {
                $str .= $value['id'] . ",";
            }

            $data_id = explode("," , rtrim($str, ","));

            foreach ($data_id as $k => $v) {
                $MonAn = MonAn::where('id', $v);

                if($MonAn) {
                    $MonAn->delete();
                } else {
                    return response()->json([
                        'status'    => false,
                        'message'   => 'Đã có lỗi sự cố!',
                    ]);
                }
            }
        }

        return response()->json([
            'status'    => true,
            'message'   => 'Đã xóa thành công!',
        ]);
    }
}
