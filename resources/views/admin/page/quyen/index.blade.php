@extends('admin.share.master')
@section('noi_dung')
<div id="app" class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Thêm Mới Quyền
            </div>
            <form id="formdata" v-on:submit.prevent="add()">
                <div class="card-body">
                    <div class="col-md-12 mb-2">
                        <label class="form-label">Tên Quyền</label>
                        <input type="text" name="ten_quyen" class="form-control">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label class="form-label">List Quyền</label>
                        <input type="text" name="list_id_quyen" class="form-control">
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button class="btn btn-primary" type="submit">Thêm Mới</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                Danh Sách Quyền
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="text-center">
                                <th>#</th>
                                <th>Tên Quyền</th>
                                <th>List Id Quyền</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(value , index) in list_quyen">
                                <th class="text-center align-middle">@{{ index + 1 }}</th>
                                <td class="align-middle">@{{ value.ten_quyen }}</td>
                                <td class="align-middle">@{{ value.list_id_quyen }}</td>
                                <td class="text-center align-middle">
                                    <button class="btn btn-primary" v-on:click="getDetail(value)" data-bs-toggle="modal" data-bs-target="#editModal"><i class="fa-solid fa-pen-to-square" style="margin-left: 4px"></i></button>
                                    <button class="btn btn-danger" v-on:click="getDetail(value)" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="fa-solid fa-trash" style="margin-left: 4px"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                {{-- Model Xoa --}}
                <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Xóa Quyền</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Bạn có chắc chắn muốn xóa quyền: <b>"@{{ detail_quyen.ten_quyen }}"</b> này không?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button v-on:click="delete_quyen()" type="button" class="btn btn-danger" data-bs-dismiss="modal">Xác Nhận</button>
                        </div>
                    </div>
                    </div>
                </div>

                {{-- Model Edit --}}
                <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Chỉnh Sửa Quyền</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Tên Quyền</label>
                                <input type="text" name="ten_quyen" v-model="detail_quyen.ten_quyen" class="form-control">
                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="form-label">List Quyền</label>
                                <input type="text" name="list_id_quyen" v-model="detail_quyen.list_id_quyen" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button v-on:click="update_quyen()" type="button" class="btn btn-primary">Xác Nhận</button>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    new Vue({
        el      :   '#app',
        data    :   {
            list_quyen      : [],
            detail_quyen    : {},
            list_action     : [],
            array_action    : [],
            cap_quyen       : {},
        },
        created()   {
            this.loadDataQuyen();
        },
        methods :   {
            add(id, name) {
                var paramObj = {};
                $.each($('#formdata').serializeArray(), function(_, kv) {
                    if (paramObj.hasOwnProperty(kv.name)) {
                        paramObj[kv.name] = $.makeArray(paramObj[kv.name]);
                        paramObj[kv.name].push(kv.value);
                    } else {
                        paramObj[kv.name] = kv.value;
                    }
                });
                axios
                    .post('/admin/quyen/create', paramObj)
                    .then((res) => {
                        if(res.data.status) {
                            toastr.success(res.data.message);
                            this.loadDataQuyen();
                            $('#formdata').trigger("reset");
                        } else {
                            toastr.error(res.data.message);
                        }
                    })
                    .catch((res) => {
                        $.each(res.response.data.errors, function(k, v) {
                            toastr.error(v[0]);
                        });
                    });
            },

            update_quyen() {
                axios
                    .post('/admin/quyen/update', this.detail_quyen)
                    .then((res) => {
                        if(res.data.status) {
                            toastr.success(res.data.message);
                            this.loadDataQuyen();
                            $('#editModal').modal('hide');
                        } else {
                            toastr.error(res.data.message);
                        }
                    })
                    .catch((res) => {
                        $.each(res.response.data.errors, function(k, v) {
                            toastr.error(v[0]);
                        });
                    });
            },

            loadDataQuyen() {
                axios
                .get('/admin/quyen/data')
                .then((res) => {
                    this.list_quyen = res.data.data;
                });
            },

            getDetail(value) {
                this.detail_quyen = Object.assign({}, value);
            },

            delete_quyen() {
                axios
                    .post('/admin/quyen/delete', this.detail_quyen)
                    .then((res) => {
                        toastr.success(res.data.message);
                        this.loadDataQuyen();
                    })
                    .catch((res) => {
                        $.each(res.response.data.errors, function(k, v) {
                            toastr.error(v[0]);
                        });
                    });
            },
        },
    });
</script>
@endsection
