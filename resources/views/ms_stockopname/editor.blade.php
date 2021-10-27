<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">
        {{ empty($stockopname) ? 'Create Stockopname' : 'Edit Stockopname' }}
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    @if (empty($stockopname))
        <form action="{{ route('sendtodatabase.stockopname') }}" method="post" id="editor-stockopname-form">
    @else
        <form action="{{ url('master/updatedatastockopname/'.$stockopname->id) }}" method="post" id="editor-stockopname-form">
        @method('PATCH')
    @endif
        @csrf
        @if ( empty($stockopname) )
            <div class="form-group">
                <label for="">Date</label>
                <input type="date" class="form-control" name="date" id="date">
                <span class="text-danger error-text date_error"></span>
            </div>
        @endif
        <div class="form-group d-none">
            <label for="">Code</label>
            <input type="text" class="form-control" name="code" readonly>
            <span class="text-danger error-text code_error"></span>
        </div>
        <div class="form-group">
            <label for="">Note</label>
            <textarea class="form-control" name="note" placeholder="Enter note" autofocus>@if ( !empty($stockopname) ){{ $stockopname->note }}@endif</textarea>
            <span class="text-danger error-text note_error"></span>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-block btn-success">Save Changes</button>
        </div>
    </form>
</div>
<script>
    // var x = document.getElementById("date").autofocus;
    $(function() {
        $('#editor-stockopname-form').on('submit', function (e) {
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
                        $('.modalStockopname').modal('hide');
                        $('.modalStockopname').find('form')[0].reset();
                        toastr.success(data.msg);
                        if (submitted) {
                            window.location.href = '{{ route("index.stockopname") }}';
                        }
                        modalEdit = true;
                    }
                }
            });
        });
    });
</script>
