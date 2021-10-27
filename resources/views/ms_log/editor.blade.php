
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">
        Create Log
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="alert alert-warning d-none" role="alert">Maaf, barang tidak cukup</div>
        {{-- @dd($assets) --}}
        <form action="{{ route('sendtodatabase.log') }}" method="post" id="editor-log-form">
        @csrf
            <div class="form-group">
                <label for="">Date</label>
                <input type="date" class="form-control" name="date" id="date">
                <span class="text-danger error-text date_error"></span>
            </div>
            <input type="hidden" name="qrcode" value="qrcode">
            <div class="form-group">
                <label for="">Asset</label>
                <select class="js-example-basic-single" name="asset_id" id="asset_id" style="width: 100%">
                    <option value="">- PILIH -</option>
                    @foreach ($assets as $item)
                        <option value="{{ $item->id }}" data-name="{{ $item->quantity }}" {{ old('assets_id') == $item->id ? 'selected' : null }}>{{ $item->name }}</option>
                    @endforeach
                </select>
                <span class="text-danger error-text asset_id_error"></span>
                {{-- {{ $cek = \App\Models\master\Asset::find($assets) }} --}}
            </div>
            <div class="form-group">
                <label for="">Type</label>
                <select class="js-example-basic-single" name="type" id="type" style="width: 100%">
                    <option value="">- PILIH -</option>
                    <option value="{{ \App\Helpers\FunctionHelper::DIKEMBALIKAN }}">DIKEMBALIKAN</option>
                    <option value="{{ \App\Helpers\FunctionHelper::DIPINJAMKAN }}">DIPINJAMKAN</option>
                    <option value="{{ \App\Helpers\FunctionHelper::SERVICE }}">SERVICE</option>
                    <option value="{{ \App\Helpers\FunctionHelper::RUSAK }}">RUSAK</option>
                    <option value="{{ \App\Helpers\FunctionHelper::HILANG }}">HILANG</option>
                    <option value="{{ \App\Helpers\FunctionHelper::KELUAR }}">KELUAR</option>
                    <option value="{{ \App\Helpers\FunctionHelper::HIBAH }}">HIBAH</option>
                    <option value="{{ \App\Helpers\FunctionHelper::BELI }}">BELI</option>
                    <option value="{{ \App\Helpers\FunctionHelper::JUAL }}">JUAL</option>
                    <option value="{{ \App\Helpers\FunctionHelper::STOCK_AWAL }}">STOCK AWAL</option>
                </select>
                <span class="text-danger error-text type_error"></span>
            </div>
            <div class="form-group employee d-none">
                <label for="">Employee</label>
                <select class="js-example-basic-single" name="employee_id" id="employee_id" style="width: 100%">
                    <option value="">- PILIH -</option>
                    @foreach ($employees as $item)
                        <option value="{{ $item->Id }}" data-name="{{ $item->Name }}" {{ old('employee_id') == $item->Id ? 'selected' : null }}>{{ $item->Name }}</option>
                    @endforeach
                </select>
                <span class="text-danger error-text employee_id _error"></span>
            </div>
            <div class="form-group">
                <label for="">Quantity</label>
                <input type="number" class="form-control" name="qty_in" id="qty" min="1" placeholder="Enter Quantity">
                <span class="text-danger error-text qty_in_error"></span>
            </div>
            <input type="hidden" class="form-control" name="qty_out" min="1" placeholder="Enter Quantity">
            <div class="form-group">
                <label for="">Notes</label>
                <textarea class="form-control" name="notes" placeholder="Enter note"></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-block btn-success">Save Changes</button>
            </div>
            {{-- @dd($employees) --}}
        </form>
</div>

<script>
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });

    // $('.alert').alert('hide')

    // $('#qty').on('change', function () {
    //     if ($('#qty').val() == 2 || $('#qty').val() == 4) {
    //         $('.alert').removeClass('d-none');
    //     } else {
    //         $('.alert').addClass('d-none');
    //     }
    // });

    $(function() {
        $('#asset_id').on('change', function() {
            console.log($('#asset_id').val());
        });

        $('#qty').on('change', function() {
            console.log($('#qty').val());
        });

        $('#type').on('change', function() {
            if ($('#type').val() == 1 || $('#type').val() == 2) {
                $('.employee').removeClass('d-none');
            } else {
                $('.employee').addClass('d-none');
            }
        });


        $('#editor-log-form').on('submit', function (e) {
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
                        toastr.error(data.msg);
                    } else {
                        $('.modalLog').modal('hide');
                        $('.modalLog').find('form')[0].reset();
                        toastr.success(data.msg);
                        submitted = true;
                    }
                }
            });
        });
    });
</script>
