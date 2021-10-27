<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">
        {{ empty($users) ? 'Create User' : 'Edit User' }}
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    @if ( empty($users) )
        <form action="{{ route('sendtodatabase.user') }}" method="post" id="editor-user-form">
    @else
        <form action="{{ url('master/updatedatauser/'.$users->id) }}" method="post" id="editor-user-form">
        @method('PATCH')
    @endif
        @csrf
            <div class="form-group">
                <label for="">Email</label>
                <input type="text" class="form-control" name="email" placeholder="Enter email" @if (!empty($users)) value="{{ $users->email }}" @endif>
                <span class="text-danger error-text email_error"></span>
            </div>
            @if (empty($users))
                <div class="form-group">
                    <label for="">Password</label>
                    <div class="input-group-append">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Enter password">
                        <span class="input-group-text" onclick="password_show_hide();" style="cursor: pointer;">
                            <i class="fas fa-eye" id="show_eye"></i>
                            <i class="fas fa-eye-slash d-none" id="hide_eye"></i>
                        </span>
                    </div>
                    <span class="text-danger error-text password_error"></span>
                </div>
            @endif
            <div class="form-group">
                <button type="submit" class="btn btn-block btn-success">Save Changes</button>
            </div>
        </form>
</div>
<script>
    function password_show_hide() {
        var x = document.getElementById("password");
        var show_eye = document.getElementById("show_eye");
        var hide_eye = document.getElementById("hide_eye");
        hide_eye.classList.remove("d-none");
        if (x.type === "password") {
            x.type = "text";
            show_eye.style.display = "none";
            hide_eye.style.display = "block";
        } else {
            x.type = "password";
            show_eye.style.display = "block";
            hide_eye.style.display = "none";
        }
    }

    $(function() {
        $('#editor-user-form').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            $.ajax({
                url: $(form).attr('action'),
                method: $(form).attr('method'),
                data: new FormData(form),
                processData: false,
                dataType: 'json',
                contentType: false,
                beforeSend: function() {
                    $(form).find('span.error-text').text('');
                },
                success: function(data) {
                    if (data.code == 0) {
                        $.each(data.error, function(prefix, val) {
                            $(form).find('span.'+prefix+'_error').text(val[0]);
                        });
                    } else {
                        $('.modalUser').modal('hide');
                        $('.modalUser').find('form')[0].reset();
                        toastr.success(data.msg);
                        submitted = true;
                    }
                }
            })
        });
    });
</script>
