<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoaiKhachHang\CreateLoaiKhachHangRequest;
use App\Http\Requests\LoaiKhachHang\DeleteLoaiKhachHangRequest;
use App\Http\Requests\LoaiKhachHang\UpdateLoaiKhachHangRequest;
use App\Models\LoaiKhachHang;
use App\Models\MonAn;
use Illuminate\Http\Request;

class LoaiKhachHangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.page.loai_khach_hang.index');
    }

    public function store(CreateLoaiKhachHangRequest $request)
    {
        $data = $request->all();
        $str  = "";
        foreach($request->list_mon as $key => $value) {
            if(isset($value['check'])) {
                $str = $str . $value['id'] . ',';
            }
        }
        $str  = rtrim($str, ",");
        $data['list_mon_tang']  = $str;

        LoaiKhachHang::create($data);

        return response()->json([
            'status'    => true,
            'message'   => 'Đã tạo mới thành công!',
        ]);
    }

    public function getData()
    {
        $list = LoaiKhachHang::get();

        foreach($list as $key => $value) {
            $value->list_mon_tang    = explode(",", $value->list_mon_tang);
            $danhSachMon             = MonAn::whereIn('id', $value->list_mon_tang)->select('ten_mon')->get();
            $ten_mon                 = "";
            foreach($danhSachMon as $k => $v) {
                $ten_mon    = $ten_mon . $v->ten_mon . ', ';
            }
            $ten_mon  = rtrim($ten_mon, ", ");
            $value->ten_mon = $ten_mon;
        }

        return response()->json([
            'list'  => $list
        ]);
    }

    public function update(UpdateLoaiKhachHangRequest $request)
    {
        $loaiKH = LoaiKhachHang::where('id', $request->id)->first();

        $data = $request->all();

        $loaiKH->update($data);

        return response()->json([
            'status'    => true,
            'message'   => 'Đã cập nhật được thông tin!',
        ]);
    }

    public function destroy(DeleteLoaiKhachHangRequest $request)
    {
        LoaiKhachHang::find($request->id)->delete();

        return response()->json([
            'status'    => true,
            'message'   => 'Đã xóa loại khách hàng thành công!'
        ]);
    }

    public function search(Request $request)
    {
        $list = LoaiKhachHang::select('loai_khach_hangs.*')
                     ->where('ten_loai_khach', 'like', '%' . $request->key_search . '%')
                     ->orWhere('list_mon_tang', 'like', '%' . $request->key_search . '%')
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
                $LoaiKH = LoaiKhachHang::where('id', $v);

                if($LoaiKH) {
                    $LoaiKH->delete();
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
