<?php

namespace App\Http\Requests\MonAn;

use Illuminate\Foundation\Http\FormRequest;

class CreateMonAnRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ten_mon'           =>  'required|min:5|max:30',
            'slug_mon'          =>  'required|min:5|unique:mon_ans,slug_mon',
            'gia_ban'           =>  'required|numeric|min:0',
            'tinh_trang'        =>  'required|boolean',
            'id_danh_muc'       =>  'required|exists:danh_mucs,id',
            'hinh_anh'          =>  'required'
        ];
    }

    public function messages()
    {
        return [
            'ten_mon.required'  =>  'Yêu cầu phải nhập tên món',
            'ten_mon.min'       =>  'Tên món phải từ 5 ký tự',
            'ten_mon.max'       =>  'Tên món tối đa được 30 ký tự',
            'slug_mon.*'        =>  'Slug món đã tồn tại!',
            'gia_ban.*'         =>  'Giá bán ít nhất là 0đ',
            'tinh_trang.*'      =>  'Vui lòng chọn tình trạng theo yêu cầu!',
            'id_danh_muc.*'     =>  'Danh mục không tồn tại trong hệ thống!',
        ];
    }
}
