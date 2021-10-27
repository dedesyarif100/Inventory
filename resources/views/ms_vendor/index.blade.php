@extends('main')

@section('css')

@endsection

@section('title')
Vendors
@endsection

@section('content')
    <h1>Vendor</h1>
    <button class="btn btn-success btn-sm" id="create">Create</button>
    <div class="content mt-3">
        <div class="animated fadeIn">
            <div class="demo-container">
                <div id="gridContainer"></div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
<div class="modal fade modalVendor" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

        </div>
    </div>
</div>
@endsection

@section('js')
    <script>
        let submitted = false;
        $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            }
        });

        $(function() {
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
                        dataField: "name",
                        dataType: "string",
                        orderable: 'false',
                        searchable: 'false'
                    },
                    {
                        dataField: "address",
                        dataType: "string",
                        searchable: 'false'
                    },
                    {
                        dataField: "contact",
                        dataType: "string",
                        searchable: 'false'
                    },
                    {
                        dataField: "CreatedBy",
                        dataType: "string",
                        searchable: 'false'
                    },
                    {
                        dataField: "UpdatedBy",
                        dataType: "string",
                    },
                    {
                        dataField: 'action',
                        dataType: 'button',
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
                sendData.dataSource = `{{ route('getdata.vendor') }}`;
                if (isResponsive) {
                    sendData.columnWidth = 200;
                }
                $("#gridContainer").dxDataGrid(sendData);
            }
            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Get All Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Call modal Create Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
            $(document).on('click', '#create', function () {
                $.get('{{ route("editor.vendor") }}', function(data) {
                    $('.modalVendor').find('.modal-content').html(data);
                    $('.modalVendor').modal('show');
                });
            });
            $('.modalVendor').on('shown.bs.modal', function (event) {
                $('input[name="name"]').focus();
            });
            $('.modalVendor').on('hidden.bs.modal', function (event) {
                if (submitted) {
                    prosesDesktop();
                    submitted = false;
                }
            });
            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Call modal Create Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Edit Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
            $(document).on('click', '#edit', function () {
                let vendor_id = $(this).data('id');
                $('.modalVendor').find('span.error-text').text('');
                $.get('{{ route("editor.vendor") }}', {vendor_id: vendor_id}, function (data) {
                    $('.modalVendor').find('.modal-content').html(data);
                    $('.modalVendor').modal('show');
                });
            });
            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Edit Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Delete Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
            $(document).on('click', '#delete', function () {
                var vendor_id = $(this).data('id');
                var url = '{{ route("delete.vendor") }}';
                swal.fire({
                    title: 'Are you sure?',
                    html: 'You want to <b>delete</b> this vendor',
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
                        $.post(url, {vendor_id: vendor_id}, function(data) {
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
