@extends('main')

@section('css')

@endsection

@section('title')
Detail Assets
@endsection

@section('content')
    {{-- <h1>Journal Assets</h1> --}}
    {{-- <input type="hidden" id="asset_id" name="asset_id" value="{{ $asset_items }}" /> --}}
    <input type="hidden" id="id" name="id" value="{{ $asset->id }}" />
    {{-- @dd($asset->toArray()) --}}
    <div class="content mt-3">
        <div class="animated fadeIn">
            <div class="demo-container">
                <div id="gridContainer"></div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
<div class="modal fade modalLog" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
        let getLog = @json($asset->id);
        let submitted = false;
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
                        dataField: 'date',
                        dataType: 'datetime',
                        format: 'dd-MM-yyyy',
                    },
                    {
                        dataField: 'code',
                        dataType: 'string'
                    },
                    {
                        dataField: 'asset',
                        dataType: 'string'
                    },
                    {
                        dataField: 'quantity',
                        dataType: 'string',
                        type: 'html'
                    },
                    {
                        dataField: 'type',
                        dataType: 'string',
                        type: 'html',
                    },
                    {
                        dataField: 'employee',
                        dataType: 'string'
                    },
                    {
                        dataField: 'notes',
                        dataType: 'string'
                    },
                ]
            };
            myFunction(x);
            x.addListener(myFunction);

            function myFunction(x) {
                prosesDesktop(x.matches);
            }

            function prosesDesktop(isResponsive = false) {
                let id = $('#id').val();
                console.log(id);
                console.log(getLog);
                // let url = "{{ URL('master/showasset_byid/'." getLog ") }}";
                // getLog = getLog.map(function (log) {
                //     return log.id;
                // });
                sendData.dataSource = "{{ url('master/show_detailassets/') }}"+ '/' + id;
                if (isResponsive) {
                    sendData.columnWidth = 200;
                }
                $("#gridContainer").dxDataGrid(sendData);
            }
            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Get All Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        });
    </script>
@endsection
