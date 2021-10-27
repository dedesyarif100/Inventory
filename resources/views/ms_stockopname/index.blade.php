@extends('main')

@section('css')

@endsection

@section('title')
Stockopname
@endsection

@section('content')
    <h1>Stockopname</h1>
    @if ($stockopname)
        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="demo-container">
                    <div id="gridContainer"></div>
                </div>
            </div>
        </div>
    @else
        <button class="btn btn-success btn-sm" id="create">Create</button>
        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="demo-container">
                    <div id="gridContainer"></div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('modal')
<div class="modal fade modalStockopname" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

        </div>
    </div>
</div>
@endsection

@section('js')
    @include('ms_stockopname.viewbarcode')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    <script>
        let submitted = false;
        let modalEdit = false;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(function () {
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
                        searchable: false
                    },
                    {
                        dataField: "date",
                        dataType: "datetime",
                        format: 'dd-MM-yyyy',
                        // sortOrder: "desc"
                    },
                    {
                        dataField: "code",
                        dataType: "string"
                    },
                    {
                        dataField: "note",
                        dataType: "string"
                    },
                    {
                        dataField: "status",
                        dataType: "string",
                        type: 'html',
                        // sortIndex: 0,
                        // // allowSorting: false,
                        allowFiltering: false
                    },
                    {
                        dataField: "CreatedBy",
                        dataType: "string"
                    },
                    {
                        dataField: "UpdatedBy",
                        dataType: "string"
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
                sendData.dataSource = `{{ route('getdata.stockopname') }}`;
                if (isResponsive) {
                    sendData.columnWidth = 200;
                }
                $("#gridContainer").dxDataGrid(sendData);
            }
            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Get All Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Call modal Create Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
            $(document).on('click', '#create', function () {
                $.get('{{ route("editor.stockopname") }}', function(data) {
                    $('.modalStockopname').find('.modal-content').html(data);
                    $('.modalStockopname').modal('show');
                });
                submitted = true;
            });
            $('.modalStockopname').on('shown.bs.modal', function (event) {
                $('input[name="date"]').focus();
            });
            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Call modal Create Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Call Show Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
            $(document).on('click', '#show', function () {
                let stockopname_id = $(this).data('id');
                let getqr = document.getElementById('qrcode');
                getqr.innerHTML = "";

                $('.showqr_stockopname').find('form');
                $('.showqr_stockopname').find('span.error-text').text('');
                $('.showqr_stockopname').modal('show');
                new QRious({
                    element: getqr,
                    size: 230,
                    value: stockopname_id
                });
                document.getElementById("code").style.display = "block";
                // $('.showassets').find('h2[id="name"]').html(assets_id);
                $('.showqr_stockopname').find('h1[id="code"]').html(stockopname_id);
            });
            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Call Show Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Edit Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
            $(document).on('click', '#edit', function () {
                let stockopname_id = $(this).data('id');
                $('.modalStockopname').find('span.error-text').text('');
                $.get('{{ route("editor.stockopname") }}', {stockopname_id: stockopname_id}, function (data) {
                    $('.modalStockopname').find('.modal-content').html(data);
                    $('.modalStockopname').modal('show');
                });
                submitted = false;
            });
            $('.modalStockopname').on('hidden.bs.modal', function (event) {
                if (modalEdit) {
                    prosesDesktop();
                    modalEdit = false;
                }
            });
            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Edit Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Delete Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
            $(document).on('click', '#delete', function () {
                var stockopname_id = $(this).data('id');
                var url = '{{ route("delete.stockopname") }}';
                swal.fire({
                    title: 'Are you sure?',
                    html: 'You want to <b>delete</b> this stockopname',
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
                        $.post(url, {stockopname_id: stockopname_id}, function(data) {
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

            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Generate Code >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
            // Proses Create >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
            // function generateCode() {
            //     let code = $('input[name="code"]').val();
            //     let codesplit = code.split('.');
            //     let date = new Date($('#date').val());
            //     let time = new Date().toLocaleTimeString([], {hour: '2-digit', minute: '2-digit', second: '2-digit'});
            //     codesplit[0] = 'SO'+date.getFullYear()+''+(date.getMonth() + 1 )+''+date.getDate()+''+time;
            //     console.log(codesplit);
            //     $('input[name="code"]').val(codesplit.join('.'));
            // }

            // $('#date').on('change', function() {
            //     console.log($('#date').val());
            //     generateCode();
            // })
            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Generate Code >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        });
    </script>
@endsection
