@extends('layouts.gurunewapp')

@section('title', 'Guru Inbox')

@section('content')

<style>
    .page-title { font-size: 30px; font-weight: 600; margin-bottom: 20px; }
    .letter-item { cursor: pointer; transition: 0.2s; }
    .letter-item:hover { background: #f7f7f7; }
    .preview-box { min-height: 600px; }
</style>

<div class="container-fluid">
    <div class="page-title">Letter Inbox</div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Inbox</h5>
                </div>
                <div class="card-body p-0">
                    @forelse($documents as $doc)
                    <div class="letter-item border-bottom p-3" onclick="loadLetter('{{ $doc->documentID }}')">
                        <div class="d-flex">
                            <div class="me-3"><div class="bg-success rounded-circle" style="width:12px;height:12px;"></div></div>
                            <div>
                                <h5>Offer Letter</h5>
                                <div>{{ Str::limit(strip_tags($doc->documentDescription), 80) }}</div>
                                <small class="text-muted">Letter: {{ $doc->dateIssued }}</small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-4 text-center">No letter found.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header"><h5>Display Letter</h5></div>
                <div class="card-body preview-box" id="previewArea">
                    <div class="text-center text-muted mt-5">
                        <h1>📩</h1>
                        <p>Click to display letter.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function loadLetter(id) {
        fetch('/guru-letter/' + id)
        .then(response => response.json())
        .then(data => {
            let pdfButton = data.pdfFile ? 
                `<a href="/offer_letters/${data.pdfFile}" target="_blank" class="btn btn-success mt-3">View PDF</a>` : '';

            let actionButtons = (data.status === 'Pending' || !data.status) ? `
                <button class="btn btn-success me-2" onclick="respondOffer('${data.documentID}', 'Accepted')">Terima Tawaran</button>
                <button class="btn btn-danger" onclick="respondOffer('${data.documentID}', 'Rejected')">Tolak Tawaran</button>
            ` : `<span class="badge bg-primary p-2">${data.status}</span>`;

            document.getElementById('previewArea').innerHTML = `
                <h4>${data.documentType}</h4>
                <hr>
                <p><strong>Date:</strong> ${data.dateIssued}</p>
                <p><strong>Signed By:</strong> ${data.signedby ?? 'HR Manager'}</p>
                <hr>
                <div>${data.documentDescription}</div>
                ${pdfButton}
                <hr>
                <p><strong>Status:</strong> ${data.status ?? 'Pending'}</p>
                <div class="mt-3">${actionButtons}</div>
            `;
        })
        .catch(err => console.error('Error loading letter:', err));
    }

    function respondOffer(id, status) {
        if (!confirm('Adakah anda pasti untuk menukar status surat ini kepada ' + status + '?')) return;

        fetch('/guru-letter/respond/' + id, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            alert('Jawapan berjaya dihantar.');
            loadLetter(id);
        })
        .catch(error => {
            console.error(error);
            alert('Ralat berlaku semasa menghantar jawapan.');
        });
    }
</script>

@endsection