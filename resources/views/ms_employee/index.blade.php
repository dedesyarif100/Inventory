@extends('main')

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn3.devexpress.com/jslib/21.1.5/css/dx.common.css" />
<link rel="stylesheet" type="text/css" href="https://cdn3.devexpress.com/jslib/21.1.5/css/dx.light.css" />
@endsection

@section('content')
    <h1>Employee</h1>
    <div class="content mt-3">
        <div class="animated fadeIn">
            <div class="demo-container">
                <div id="gridContainer"></div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn3.devexpress.com/jslib/21.1.5/js/dx.all.js"></script>
    <script>
        $(function() {
            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Get All Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
            var x = window.matchMedia("(max-width: 700px)");
            let sendData = {
                columnsAutoWidth: true,
                filterRow: { visible: true },
                filterPanel: { visible: true },
                headerFilter: { visible: true },
                filterBuilderPopup: {
                    position: { of: window, at: "top", my: "top", offset: { y: 10 } },
                },
                scrolling: { mode: "infinite" },
                showBorders: true,
                columns: [
                    {
                        dataField: "DT_RowIndex",
                        dataType: "number",
                        orderable: false,
                        searchable: false
                    },
                    {
                        dataField: "Name",
                        dataType: "string"
                    },
                ]
            };
            myFunction(x);
            x.addListener(myFunction);

            function myFunction(x) {
                prosesDesktop(x.matches);
            }

            function prosesDesktop(isResponsive = false) {
                sendData.dataSource = `{{ route('getdata.employee') }}`;
                if (isResponsive) {
                    sendData.columnWidth = 200;
                }
                $("#gridContainer").dxDataGrid(sendData);
            }
            // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Get All Data >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        });
    </script>
@endsection
