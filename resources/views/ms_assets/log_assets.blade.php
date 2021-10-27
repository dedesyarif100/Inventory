{{-- <link rel="stylesheet" type="text/css" href="https://cdn3.devexpress.com/jslib/21.1.5/css/dx.common.css" />
<link rel="stylesheet" type="text/css" href="https://cdn3.devexpress.com/jslib/21.1.5/css/dx.light.css" /> --}}
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Log Asset</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    {{-- <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
            </tr>
        </thead>

        @foreach ($log_asset as $key => $asset )
            <tr>
                <td class=""> {{ $asset->asset_id }} </td>
                <td> {{ $asset->qrcode }} </td>
            </tr>
        @endforeach
    </table> --}}

    {{-- <div class="content mt-3">
        <div class="animated fadeIn">
            <div class="demo-container">
                <div id="gridContainer"></div>
            </div>
        </div>
    </div> --}}
</div>
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn3.devexpress.com/jslib/21.1.5/js/dx.all.js"></script>
<script>
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
                    searchable: false,
                    width: 60,
                },
                {
                    dataField: 'asset_id',
                    dataType: 'number',
                    width: 80
                },
                {
                    dataField: 'type',
                    dataType: 'number',
                    width: 80
                },
                {
                    dataField: 'qty_in',
                    dataType: 'number',
                    width: 80
                },
                {
                    dataField: 'qty_out',
                    dataType: 'number',
                    width: 80
                },
                {
                    dataField: 'employee_id',
                    dataType: 'number',
                    width: 80
                },
                {
                    dataField: 'notes',
                    dataType: 'string'
                },
                // {
                //     dataField: 'action',
                //     type: 'html',
                //     allowFiltering: false
                // }
            ]
        };
        myFunction(x);
        x.addListener(myFunction);

        function myFunction(x) {
            prosesDesktop(x.matches);
        }

        function prosesDesktop(isResponsive = false) {
            sendData.dataSource = `{{ route('get.log_asset') }}`;
            if (isResponsive) {
                sendData.columnWidth = 200;
            }
            $("#gridContainer").dxDataGrid(sendData);
        }
    });
</script>
<script>
    // $(function() {
    //     $('#editor-assets-form').on('submit', function (e) {
    //         e.preventDefault();
    //         var form = this;
    //         $.ajax({
    //             url: $(form).attr('action'),
    //             method: $(form).attr('method'),
    //             data: new FormData(form),
    //             processData: false,
    //             dataType: 'json',
    //             contentType: false,
    //             beforeSend: function() {
    //                 $(form).find('span.error-text').text('');
    //             },
    //             success: function(data) {
    //                 if(data.code == 0) {
    //                     $.each(data.error, function(prefix, val) {
    //                         $(form).find('span.'+prefix+'_error').text(val[0]);
    //                     });
    //                 } else {
    //                     $('.modalAssets').modal('hide');
    //                     $('.modalAssets').find('form')[0].reset();
    //                     toastr.success(data.msg);
    //                     window.location.href = '{{ route("index.assets") }}';
    //                 }
    //             }
    //         })
    //     });
    // });
</script> --}}
