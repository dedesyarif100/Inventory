<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">
        {{ empty($vendor) ? 'Create Vendor' : 'Edit Vendor' }}
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    @if ( empty($vendor) )
        <form action="{{ route('sendtodatabase.vendor') }}" method="post" id="editor-vendor-form">
    @else
        <form action="{{ url('master/updatedatavendor/'.$vendor->id) }}" method="post" id="editor-vendor-form">
        @method('PATCH')
    @endif
        @csrf
            <div class="form-group">
                <label for="">Name</label>
                <input type="text" class="form-control" name="name" placeholder="Enter name" @if ( !empty($vendor) ) value="{{ $vendor->name }}" @endif>
                <span class="text-danger error-text name_error"></span>
            </div>
            <div class="form-group">
                <label for="">Address</label>
                <input type="text" class="form-control" name="address" placeholder="Enter address" @if ( !empty($vendor) ) value="{{ $vendor->address }}" @endif>
                <span class="text-danger error-text address_error"></span>
            </div>
            <div class="form-group">
                <label for="">Contact</label>
                <input type="number" class="form-control" name="contact" placeholder="Enter contact" @if ( !empty($vendor) ) value="{{ $vendor->contact }}" @endif>
                <span class="text-danger error-text contact_error"></span>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-block btn-success">Save Changes</button>
            </div>
        </form>
</div>
<script>
    $(function () {
        $('#editor-vendor-form').on('submit', function(e) {
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
                        $('.modalVendor').modal('hide');
                        $('.modalVendor').find('form')[0].reset();
                        toastr.success(data.msg);
                        submitted = true;
                    }
                }
            });
        });
    });
</script>
