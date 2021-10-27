@extends('main')

@section('css')

@endsection

@section('title')
Assetss
@endsection

@section('content')
    <h1>Assets</h1>
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
    <div class="row">
        <div class="col-0.5" style="padding: 2px; margin-left: 20px;">
            <button class="btn btn-success btn-sm" id="create">Create</button>
        </div>
        <div class="col-0.5" style="padding: 2px">
            <a href="{{ route('pageprint.qrcode') }}" class="btn btn-info btn-sm" id="print">Print QRCode</a>
        </div>
        <input type="hidden" id="element-to-print">
        <div class="col-6" style="padding: 2px;">
            <a href="{{ route('export.template') }}" class="btn btn-primary btn-sm float-right">Export Template</a>
        </div>
        <div class="col-4" style="padding: 2px;">
            <form action="{{ route('import.assets') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-group float-right" style="padding: 0;">
                    <div class="input-group">
                        <input type="file" name="file" accept=".xlsx, .xls" id="files" style="visibility: hidden; height: 30px; padding: 0;" class="form-control input-file" aria-describedby="inputGroupFileAddon03" required>
                        <label class="file-label" for="files" style="border: 1px solid; width: 80%; background-color: white; opacity: 0.5; padding: 10px 13px;">Choose file</label>
                        <label class="input-group-btn">
                            <span class="btn btn-success">
                                <button type="submit" id="import" class="btn btn-success btn-sm" style="padding: 0px 0px; width: 40%;">Import</button>
                                {{-- <input type="hidden" name="file" id="showhidden" for="import"> --}}
                            </span>
                        </label>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="content mt-3">
        <div class="animated fadeIn">
            <div class="demo-container">
                <div id="gridContainer"></div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
<div class="modal fade modalAssets" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

        </div>
    </div>
</div>

<div class="modal fade modal_LogAssets" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

        </div>
    </div>
</div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>

    <script>
        let submitted = false;

        $('.input-file').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.file-label').addClass("selected").html(fileName);
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(function () {
            // $('#import').on('change', function() {
                // $('#import').click(function() {
                //     $('#import').prop('disabled', true);
                //     $('#showhidden').attr('type', 'submit')
                // });
            // });

            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Get All Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
            var x = window.matchMedia("(max-width: 700px)");
            let sendData = {
                columnsAutoWidth: true,
                filterRow: { visible: true },
                // filterPanel: { visible: true },
                // headerFilter: { visible: true },
                filterBuilderPopup: {
                    position: { of: window, at: "top", my: "top", offset: { y: 10 } },
                },
                hoverStateEnabled: true,
                scrolling: {
                    rowRenderingMode: 'virtual'
                },
                showBorders: true,
                paging: {
                    pageSize: 10
                },
                pager: {
                    visible: true,
                    showInfo: true,
                    showNavigationButtons: true
                },
                columns: [
                    {
                        dataField: "DT_RowIndex",
                        caption: 'No',
                        dataType: "number",
                        orderable: false,
                        searchable: false,
                        width: 60,
                    },
                    {
                        dataField: 'code',
                        dataType: 'string'
                    },
                    {
                        dataField: 'name',
                        dataType: 'string'
                    },
                    {
                        dataField: 'vendor',
                        dataType: 'string'
                    },
                    {
                        dataField: 'quantity',
                        dataType: 'number',
                        width: 80,
                        type: 'html'
                    },
                    {
                        dataField: 'buy_at',
                        dataType: 'datetime',
                        format: 'dd-MM-yyyy',
                    },
                    {
                        dataField: 'status',
                        dataType: 'string',
                        type: 'html',
                        allowFiltering: false
                    },
                    {
                        dataField: 'notes',
                        dataType: 'string'
                    },
                    {
                        dataField: 'action',
                        type: 'html',
                        allowFiltering: false
                    }
                ]
            };
            myFunction(x);
            x.addListener(myFunction);

            function myFunction(x) {
                prosesDesktop(x.matches);
            }

            function prosesDesktop(isResponsive = false) {
                sendData.dataSource = `{{ route('getdata.assets') }}`;
                if (isResponsive) {
                    sendData.columnWidth = 200;
                }
                $("#gridContainer").dxDataGrid(sendData);
            }
            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Get All Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Call modal Create Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
            $(document).on('click', '#create', function () {
                // $('.editorassets').find('form')[0].reset();
                $.get('{{ route("editor.assets") }}', function(data) {
                    $('.modalAssets').find('.modal-content').html(data);
                    $('.modalAssets').modal('show');
                });
            });
            $('.modalAssets').on('shown.bs.modal', function (event) {
                $('select[name="category_id"]').focus();
            })

            $('.modalAssets').on('hidden.bs.modal', function (event) {
                if (submitted) {
                    prosesDesktop();
                    submitted = false;
                }
            });
            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Call modal Create Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Edit Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
            $(document).on('click', '#edit', function () {
                let assets_id = $(this).data('id');
                $('.modalAssets').find('span.error-text').text('');
                $.get('{{ route("editor.assets") }}', {assets_id: assets_id}, function (data) {
                    $('.modalAssets').find('.modal-content').html(data);
                    $('.modalAssets').modal('show');
                });
            });
            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Edit Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Log Asset >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
            $(document).on('click', '#log', function () {
                let assets_id = $(this).data('id');
                $('.modal_LogAssets').find('span.error-text').text('');
                $.get('{{ route("log.asset") }}', {assets_id: assets_id}, function (data) {
                    $('.modal_LogAssets').find('.modal-content').html(data);
                    $('.modal_LogAssets').modal('show');
                });
            });
            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Log Asset >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Delete Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
            $(document).on('click', '#delete', function () {
                var assets_id = $(this).data('id');
                var url = '{{ route("delete.assets") }}';
                swal.fire({
                    title: 'Are you sure?',
                    html: 'You want to <b>delete</b> this assets',
                    showCancelButton: true,
                    showCloseButton: true,
                    cancelButtonText: 'Cancel',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonColor: '#d33',
                    confirmButtonColor: '#556ee6',
                    width: 300,
                    allowOutsideClick: false
                }).then(function(result) {
                    if(result.value) {
                        $.post(url, {assets_id: assets_id}, function(data) {
                            if(data.code == 1) {
                                prosesDesktop();
                                toastr.success(data.msg);
                            } else {
                                toastr.error(data.msg);
                            }
                        },'json');
                    }
                });
            });
            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Delete Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        });
    </script>
@endsection
