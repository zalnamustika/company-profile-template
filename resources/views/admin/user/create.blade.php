@extends('admin.layouts.app')

@section('content')
    <div class="conatiner-fluid content-inner mt-n5 py-0" style="margin-top: 100px !important;">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="flex-wrap d-flex justify-content-between align-items-center">
                            <div>
                                <div class="header-title">
                                    <h2 class="card-title">User</h2>
                                    <p>Create data</p>
                                </div>
                            </div>
                            <div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="needs-validation" id="form-data" name="form-data" novalidate>
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label class="form-label" for="name">Level </label>
                                <select class="form-select" id="level_id" name="level_id" required>
                                    <option value="2">User</option>
                                    <option value="1">Super Admin</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="username">Username </label>
                                <input class="form-control" type="text" id="username" name="username" value="" placeholder="Enter Username" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="email">Email </label>
                                <input class="form-control" type="text" id="email" name="email" value="" placeholder="Enter Email" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="name">Name </label>
                                <input class="form-control" type="text" id="name" name="name" value="" placeholder="Enter Name" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="password">Password </label>
                                <input class="form-control" type="password" id="password" name="password" value="" placeholder="Enter Password" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="picture">Picture</label>
                                <input class="form-control" type="file" id="file" name="file" required>
                                <input class="form-control" type="hidden" id="picture" name="picture" value="noimage.jpg" placeholder="image">
                                <br>
                                <img src="{{ asset('/uploads/noimage.jpg') }}" id="image-preview" name="image-preview" width="300px" style="border-radius: 2%;">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="notes">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active">
                                    <label class="form-check-label" for="is_active">Active Status</label>
                                </div>
                            </div>
                            <br><br>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <a href="{{ URL::previous() }}" class="btn btn-danger">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>


        // handle upload image
        $('#file').change(function(){
            // preview
            $('#image-preview').attr('display', 'block');
            var oFReader = new FileReader();
            oFReader.readAsDataURL( $("#file")[0].files[0]);
            oFReader.onload = function(oFREvent) {
                $('#image-preview').attr('src', oFREvent.target.result);
            };

            // upload
            formdata = new FormData();
            if($(this).prop('files').length > 0) {
                file =$(this).prop('files')[0];
                formdata.append("image", file);
            }
            $.ajaxSetup({
                headers:{
                    'Authorization': "Bearer {{$session_token}}"
                }
            });
            $.ajax({
                url: '/api/upload',
                type: "POST",
                data: formdata,
                processData: false,
                contentType: false,
                success: function (result) {
                    if(result['success'] == true) {
                        $('#picture').val(result['data']['url'].replace('/xxx/', '/300/'));
                    }
                }
            });
        });

        // handle post
        $('#form-data').submit(false);
        $("#form-data").submit( function () {

            if($(this).valid()) {
                var form = $("#form-data").serializeArray();
                var formdata = {};
                $.map(form, function(n, i){
                    formdata[n['name']] = n['value'];
                });
                if ('is_active' in formdata) {
                    if (formdata['is_active'] == 'on') {
                        formdata['is_active'] = true;
                    } else {
                        formdata['is_active'] = false;
                    }
                } else {
                    formdata['is_active'] = false;
                }

                $.ajaxSetup({
                    headers:{
                        'Authorization': "Bearer {{$session_token}}"
                    }
                });
                $.ajax({
                    url: '/api/user',
                    type: "POST",
                    data: JSON.stringify(formdata),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    processData: false,
                    success: function (result) {
                        if(result['success'] == true) {
                            Swal.fire({
                                icon: "success",
                                title: "Success",
                                text: result['message'],
                                confirmButtonColor: '#3A57E8',
                            }).then((result) => {
                                window.location.replace("{{ url('/admin/user') }}");
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: result['message'],
                                confirmButtonColor: '#3A57E8',
                            });
                        }
                    }
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "You must complete the entire form.",
                    confirmButtonColor: '#3A57E8',
                });
            }
            return false;
        });
    </script>
@endsection
