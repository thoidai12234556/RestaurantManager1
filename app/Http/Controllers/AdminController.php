<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\ChangePassWordAdminRequest;
use App\Http\Requests\Admin\CreateAdminRequest;
use App\Http\Requests\Admin\DeleteAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Mail\QuenMatKhau;
use App\Models\Admin;
use App\Models\Quyen;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use function PHPUnit\Framework\returnSelf;

class AdminController extends Controller
{
    public function viewUpdatePass($hash_reset)
    {
        $taiKhoan = Admin::where('hash_reset', $hash_reset)->first();
        if($taiKhoan) {
            return view('admin.page.update_pass', compact('hash_reset'));
        } else {
            toastr()->error('Dữ liệu không tồn tại!');
            return redirect('/admin/login');
        }
    }

    public function actionUpdatePass(Request $request)
    {
        if($request->password != $request->re_password) {
            toastr()->error('Mật khẩu không trùng nhau!');
            return redirect()->back();
        }
        $taiKhoan = Admin::where('hash_reset', $request->hash_reset)->first();
        if(!$taiKhoan) {
            toastr()->error('Dữ liệu không tồn tại!');
            return redirect()->back();
        } else {
            $taiKhoan->password   = bcrypt($request->password);
            $taiKhoan->hash_reset = NULL;
            $taiKhoan->save();
            toastr()->success('Đã đổi mật khẩu thành công!');
            return redirect('/admin/login');
        }
    }

    public function viewLostPass()
    {
        return view('admin.page.lost_password');
    }

    public function actionLostPass(Request $request)
    {
        $taiKhoan   = Admin::where('email', $request->email)->first();
        if($taiKhoan) {
            $now    = Carbon::now();
            $time   = $now->diffInMinutes($taiKhoan->updated_at);
            if(!$taiKhoan->hash_reset || $time > 0) {
                $taiKhoan->hash_reset = Str::uuid();
                $taiKhoan->save();

                $link    = env('APP_URL') . '/admin/update-password/' . $taiKhoan->hash_reset;

                Mail::to($taiKhoan->email)->send(new QuenMatKhau($link));
            }
            toastr()->success("Vui lòng kiểm tra email!");
            return redirect('/admin/login');

        } else {
            toastr()->error("Tài khoản không tồn tại!");
            return redirect('/admin/lost-password');
        }
    }

    public function actionLogout()
    {
        Auth::guard('aloxinh')->logout();
        toastr()->error("Tài khoản đã đăng xuất!");
        return redirect('/admin/login');
    }

    public function viewLogin()
    {
        $check = Auth::guard('aloxinh')->check();
        if($check) {
            return redirect('/');
        } else {
            return view('admin.page.login');
        }
    }

    public function actionLogin(Request $request)
    {
        // $request->email, $request->password
        $check =  Auth::guard('aloxinh')->attempt([
                                        'email'     => $request->email,
                                        'password'  => $request->password
                                    ]);
        if($check) {
            toastr()->success("Đã đăng nhập thành công!");
            return redirect('/');
        } else {
            toastr()->error("Tài khoản hoặc mật khẩu không đúng!");
            return redirect('/admin/login');
        }
    }

    public function index()
    {
        $x     =$this->checkRule(1);
        if($x){

           return response()->json([
            'status'    => 0,
            'message'   =>'Bạn không đủ quyền truy cập!'

           ]);
        }
        $quyen = Quyen::get();
        return view('admin.page.tai_khoan.index', compact('quyen'));
    }

    public function store(CreateAdminRequest $request)
    {
        $data = $request->all();
        $data['password'] =  bcrypt($request->password);
        Admin::create($data);

        return response()->json([
            'status'    => true,
            'message'   => 'Đã tạo tài khoản thành công!'
        ]);
    }

    public function getData()
    {
        $x          =$this->checkRule(2);
        if($x){
            toastr()-> error("Bạn không đủ quyền truy cập!");
            return redirect('/');
        }
        $list = Admin::join('quyens', 'admins.id_quyen', 'quyens.id')
                     ->select('admins.*', 'quyens.ten_quyen')
                     ->get();
        return response()->json([
            'list'  => $list
        ]);
    }

    public function destroy(DeleteAdminRequest $request)
    {
        $admin = Admin::where('id', $request->id)->first();
        $admin->delete();
        return response()->json([
            'status'    => true,
            'message'   => 'Đã xóa thành công!',
        ]);
    }

    public function update(UpdateAdminRequest $request)
    {
        $data    = $request->all();
        $admin = Admin::find($request->id);
        $admin->update($data);

        return response()->json([
            'status'    => true,
            'message'   => 'Đã cập nhật thành công!',
        ]);
    }

    public function changePassword(ChangePassWordAdminRequest $request)
    {
        $data = $request->all();
        if(isset($request->password)){
            $admin = Admin::find($request->id);
            $data['password'] = bcrypt($data['password_new']);
            $admin->password  = $data['password'];
            $admin->save();
        }
        return response()->json([
            'status'    => 1,
            'message'   => 'Đã cập nhật mật khẩu thành công!',
        ]);
    }
}
