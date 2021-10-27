@extends('main')

@section('css')

@endsection

@section('title')
Users
@endsection

@section('content')
    <h1>Users</h1>
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
<div class="modal fade modalUser" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                        dataField: 'DT_RowIndex',
                        caption: 'No',
                        dataType: 'number',
                        orderable: false,
                        searchable: false
                    },
                    {
                        dataField: "email",
                        dataType: "string"
                    },
                    // {
                    //     dataField: "employee",
                    //     dataType: "string"
                    // },
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
                sendData.dataSource = `{{ route('getdata.users') }}`;
                if (isResponsive) {
                    sendData.columnWidth = 200;
                }
                $("#gridContainer").dxDataGrid(sendData);
            }
            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Get All Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Call modal Create Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
            $(document).on('click', '#create', function () {
                // $('.createuser').find('form')[0].reset();
                $.get('{{ route("editor.users") }}', function(data) {
                    $('.modalUser').find('.modal-content').html(data);
                    $('.modalUser').modal('show');
                });
            });
            $('.modalUser').on('shown.bs.modal', function (event) {
                $('input[name="email"]').focus();
            });
            $('.modalUser').on('hidden.bs.modal', function (event) {
                if (submitted) {
                    prosesDesktop();
                    submitted = false;
                }
            });
            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Call modal Create Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Edit Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
            $(document).on('click', '#edit', function () {
                let user_id = $(this).data('id');
                $('.modalUser').find('span.error-text').text('');
                $.get('{{ route("editor.users") }}', {user_id : user_id}, function (data) {
                    $('.modalUser').find('.modal-content').html(data);
                    $('.modalUser').modal('show');
                });
            });
            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Edit Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Delete Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
            $(document).on('click', '#delete', function() {
                var user_id = $(this).data('id');
                var url = '{{ route("delete.user") }}';
                swal.fire({
                    title: 'Are you sure?',
                    html: 'You want to <b>delete</b> this user',
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
                        $.post(url, {user_id: user_id}, function(data) {
                            if(data.code == 1) {
                                prosesDesktop();
                                toastr.success(data.msg);
                            } else {
                                toasts.error(data.msg);
                            }
                        },'json');
                    }
                });
            });
            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Delete Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        });
    </script>
@endsection
