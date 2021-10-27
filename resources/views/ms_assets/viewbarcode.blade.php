@extends('main')

@section('css')

@endsection

@section('title')
Print QRCode
@endsection

@section('content')
    {{-- @dd($assets) --}}
    <h1>Print QRCode</h1>
    <div class="d-flex mb-3 showassets" id="printarea">
        <div class="text-center py-2 px-3">
            <div class="px-3 border text-center" style="max-width: 8rem;">
                <h5 id="name" class="mt-2 text-truncate">{{ $assets->name }}</h5>
                <div class="d-block mb-2">{!! QrCode::size(80)->generate($assets->code); !!}</div class="d-block mb-3">
                <h5 id="code">{{ $assets->code }}</h5>
            </div>
        </div>
    </div>
    <div class="d-flex px-3">
        <button class="btn btn-primary btn-sm" style="width: 8rem;" onclick="print()">
            <i class="fas fa-file-pdf"></i> Export PDF
        </button>
    </div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        function print() {
            let printarea = $('#printarea');
            let name = $('.showassets').find('h5[id="name"]').html();
            let code = $('.showassets').find('h5[id="code"]').html();

            let element = document.getElementById('printarea');
            // printarea.removeClass('justify-content-center');
            // printarea.addClass('justify-content-left');

            html2pdf(element, {
                margin: [15, 0, 15, 0],
                filename: code +'_'+ toSnakeCase(name) +'.pdf',
                html2canvas: { scale: 1, logging: true },
                jsPDF: { unit: 'pt', format: 'a4', orientation: 'p' },
            });

            // printarea.addClass('justify-content-center');
            // printarea.removeClass('justify-content-left');
        }
    </script>
@endsection
