<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">
        {{ empty($assets) ? 'Create Assets' : 'Edit Assets' }}
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    @if ( empty($assets) )
        <form action="{{ route('sendtodatabase.assets') }}" method="post" id="editor-assets-form">
    @else
        <form action="{{ url('master/updatedataassets/'. $assets->id) }}" method="post" id="editor-assets-form">
        @method('PATCH')
    @endif
        @csrf
            @if ( !empty($assets) )
                <input type="hidden" name="category_update" value="{{ $assets->category_id }}">
            @endif
            <div class="form-group">
                <label for="">Category</label>
                @if ($logs > 1)
                    <select name="category_id" class="form-control" id="category_id" disabled>
                        {{-- <option value="">- PILIH -</option> --}}
                        @foreach ($categories as $item)
                            <option value="{{ $item->id }}" data-code="{{ $item->code }}" {{ old('category_id', $assets->category_id) == $item->id ? 'selected' : null }}>{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="category_id" value="{{ $assets->category_id }}">
                @else
                    <select name="category_id" class="form-control" id="category_id" @if (!empty($assets->category_id)) disabled @endif>
                        <option value="">- PILIH -</option>
                        @foreach ($categories as $item)
                            @if ( empty($assets) )
                                <option value="{{ $item->id }}" data-code="{{ $item->code }}" {{ old('category_id') == $item->id ? 'selected' : null }}>{{ $item->name }}</option>
                            @else
                                <option value="{{ $item->id }}" data-code="{{ $item->code }}" {{ old('category_id', $assets->category_id) == $item->id ? 'selected' : null }}>{{ $item->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    @if (!empty($assets->category_id))
                        <input type="hidden" name="category_id" value="{{ $assets->category_id }}">
                    @endif
                @endif
                <span class="text-danger error-text category_id_error"></span>
            </div>
            <div class="form-group d-none">
                <label for="">Code</label>
                <input type="text" class="form-control" name="code" @if ( !empty($assets) ) value="{{ old('code', $assets->code) }}" @endif readonly>
                <span class="text-danger error-text code_error"></span>
            </div>
            <div class="form-group">
                <label for="">Name</label>
                <input type="text" class="form-control" name="name" @if ( !empty($assets) ) value="{{ old('name', $assets->name) }}" @endif placeholder="Enter name">
                <span class="text-danger error-text name_error"></span>
            </div>
            <div class="form-group">
                <label for="">Vendor</label>
                <select name="vendor_id" class="form-control" id="vendor_id">
                    <option value="">- PILIH -</option>
                    @foreach ($vendors as $item)
                        @if ( empty($assets) )
                            <option value="{{ $item->id }}" {{ old('vendor_id') == $item->id ? 'selected' : null }}>{{ $item->name }}</option>
                        @else
                            <option value="{{ $item->id }}" {{ old('vendor_id', $assets->vendor_id) == $item->id ? 'selected' : null }}>{{ $item->name }}</option>
                        @endif
                    @endforeach
                </select>
                <span class="text-danger error-text vendor_id_error"></span>
            </div>
            <div class="form-group">
                <label for="">Quantity</label>
                <input type="number" class="form-control" name="quantity" @if (!empty($assets)) value="{{ $assets->quantity }}" @endif min="1" placeholder="Enter Quantity" @if ( ($logs > 1) ) readonly @endif>
                <span class="text-danger error-text quantity_error"></span>
            </div>
            <div class="form-group">
                <label for="">Buy At</label>
                <input type="date" class="form-control" name="buy_at" id="buy_at" @if ( !empty($assets) ) value="{{ old('buy_at', Carbon\Carbon::create($assets->buy_at)->toDateString() ) }}" @endif>
                <span class="text-danger error-text buy_at_error"></span>
            </div>
            <div class="form-group">
                <label for="">Notes</label>
                <textarea class="form-control" name="notes" placeholder="Enter note">@if ( !empty($assets) ){{ $assets->notes }}@endif</textarea>
                <span class="text-danger error-text notes_error"></span>
            </div>
            <div class="form-group float-left @if (!empty($assets)) d-none @endif">
                <input type="checkbox" class="form-check-input" name="same_qrcode" value="1" id="exampleCheck1" style="margin-left: 1px;">
                <label class="form-check-label" for="exampleCheck1" style="margin-left: 1px; cursor: pointer;"> Make same code </label>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-block btn-success">Save Changes</button>
            </div>
        </form>
</div>
<script>
    $(function() {
        // Proses Edit >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        if (@json( empty($assets) || !empty($assets) )) {
            function generateEditCode() {
                let code = $('input[name="code"]').val();
                let codesplit = code.split('.');
                if ($('#buy_at').val() != '') {
                    let date = new Date($('#buy_at').val());
                    codesplit[1] = date.getFullYear();
                }
                codesplit[0] = $('#category_id').children('option:selected').attr('data-code');
                $('input[name="code"]').val(codesplit.join('.'));
            }

            $('#category_id').on('change', function() {
                console.log($('#category_id').children('option:selected').attr('data-code'));
                generateEditCode();
            });

            $('#buy_at').on('change', function() {
                console.log($('#buy_at').val());
                generateEditCode();
            });
            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Generate Code >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        }

        $('#editor-assets-form').on('submit', function (e) {
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
                        $('.modalAssets').modal('hide');
                        $('.modalAssets').find('form')[0].reset();
                        toastr.success(data.msg);
                        submitted = true;
                    }
                }
            })
        });
    });
</script>
