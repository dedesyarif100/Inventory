<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">
        {{ empty($category) ? 'Create Category' : 'Edit Category' }}
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    @if ( empty($category) )
        <form action="{{ route('sendtodatabase.category') }}" method="post" id="editor-category-form">
    @else
        <form action="{{ url('master/updatedatacategory/'.$category->id) }}" method="post" id="editor-category-form">
        @method('PATCH')
    @endif
        @csrf
            <div class="form-group">
                <label for="">Name</label>
                <input type="text" class="form-control" name="name" placeholder="Enter name" @if ( !empty($category) ) value="{{ $category->name }}" @endif>
                <span class="text-danger error-text name_error"></span>
            </div>
            <div class="form-group">
                <label for="">Code</label>
                <input type="text" class="form-control" name="code" placeholder="Enter code" @if ( !empty($category) ) value="{{ $category->code }}" @endif maxlength="4" onkeypress="abjadOnly(event)" style="text-transform:uppercase" required @if ( !empty($asset) ) readonly @endif>
                <span class="text-danger error-text code_error"></span>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-block btn-success">Save Changes</button>
            </div>
        </form>
</div>
<script>
    function abjadOnly(e) {
        var regex = new RegExp("^[a-zA-Z]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        // var len = {max:4};
        if (regex.test(str)) {
            return true;
        }
        e.preventDefault();
        return false;
    }

    $(function() {
        $('#editor-category-form').on('submit', function (e) {
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
                    if(data.code == 0) {
                        $.each(data.error, function(prefix, val) {
                            $(form).find('span.'+prefix+'_error').text(val[0]);
                        });
                    } else {
                        $('.modalCategory').modal('hide');
                        $('.modalCategory').find('form')[0].reset();
                        toastr.success(data.msg);
                        submitted = true;
                    }
                }
            })
        });
    });
</script>
