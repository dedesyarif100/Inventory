@extends('main')

@section('css')

@endsection

@section('title')
Print QRCode
@endsection

@section('content')
    <h1>Print QRCode</h1>
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-primary btn-sm float-right" onclick="print()">
            <i class="fas fa-file-pdf"></i> Export PDF
        </button>
    </div>
    <div class="card">
        <div class="card-body px-5 py-5" id="printarea">
            <div class="row">
                @foreach ($assets as $asset)
                    <div class="col-auto border text-center">
                        <div style="max-width: 8rem;">
                            <h5 class="mt-2 text-truncate">{{ $asset->name }}</h5>
                            <div class="d-block mb-2">{!! QrCode::size(80)->generate($asset->code); !!}</div class="d-block mb-3">
                            <h5>{{ $asset->code }}</h5>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        function print() {
            var element = document.getElementById('printarea');
            html2pdf(element, {
                margin: 5,
                filename: "{{ now()->format('Ymd_His') .'_qrcode_assets' }}",
                jsPDF: { unit: 'pt', format: 'a4', orientation: 'p' },
            });
        }
    </script>
@endsection
