@extends('layout')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<style>
    /* Styling khusus Admin */
    .stat-card {
        background: linear-gradient(135deg, #002855 0%, #00509d 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        position: relative;
        overflow: hidden;
    }
    .stat-card i {
        position: absolute;
        right: -10px;
        bottom: -20px;
        font-size: 5rem;
        opacity: 0.15;
        transform: rotate(-15deg);
    }
    
    .accordion-item {
        border: none;
        margin-bottom: 10px;
        border-radius: 10px !important;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .accordion-button:not(.collapsed) {
        background-color: #e8f4fd;
        color: #002855;
    }
    
    .sidebar-wrapper {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .logo-container img {
        display: block;
        margin-left: auto;
        margin-right: auto;
    }

    /* === [UPDATE] Layout Pagination Atas-Bawah === */
    .pagination-wrapper {
        width: 100%;
        padding-top: 15px;
        border-top: 1px solid rgba(0,0,0,0.1);
    }

    /* Memaksa elemen flex bawaan Bootstrap menjadi kolom (Vertikal) */
    .pagination-wrapper nav .d-none.flex-sm-fill {
        display: flex !important;
        flex-direction: column !important; /* Susun Atas-Bawah */
        align-items: center !important;    /* Rata Tengah */
        justify-content: center !important;
        gap: 10px;                         /* Jarak antara tulisan dan tombol */
    }

    /* Pastikan pembungkus tidak melebar paksa */
    .pagination-wrapper nav .d-none.flex-sm-fill > div {
        display: block !important;
        width: auto !important;
    }

    /* Styling tombol pagination */
    .page-item .page-link {
        color: #002855;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        margin: 0 2px;
        padding: 5px 10px;
        font-size: 0.9rem;
    }
    .page-item.active .page-link {
        background-color: #002855;
        border-color: #002855;
        color: white;
    }
    .page-item .page-link:hover {
        background-color: #e8f4fd;
    }

    /* Hapus margin pada teks 'Showing...' */
    .pagination-wrapper p.small {
        margin-bottom: 0 !important;
        font-size: 0.85rem;
        color: #6c757d;
    }
</style>

<div class="row">
    <div class="col-md-4 col-lg-3 mb-4">
        <div class="sidebar-wrapper">
            
            <div class="card glass-card border-0 p-4 text-center logo-container">
                <img src="{{ asset('images/logo_imigrasi.png') }}" alt="Logo" width="80" class="mb-3 mx-auto d-block">
                <h6 class="fw-bold text-dark mb-0">ADMINISTRATOR</h6>
                <small class="text-muted" style="font-size: 12px;">Imigrasi Wonosobo</small>
                
                <form action="{{ route('logout') }}" method="POST" class="mt-3">
                    @csrf
                    <button class="btn btn-danger w-100 btn-sm rounded-pill">
                        <i class="bi bi-box-arrow-right me-1"></i> Keluar
                    </button>
                </form>
            </div>

            <div class="stat-card shadow-sm">
                <i class="bi bi-file-earmark-text"></i>
                <small class="text-uppercase opacity-75 fw-bold" style="font-size: 10px;">Total Berkas Masuk</small>
                <h2 class="fw-bold mb-0">{{ $permohonanPage->total() }}</h2>
                
                @if(request('tanggal'))
                    <div class="mt-2 badge bg-white text-primary bg-opacity-90">
                        <i class="bi bi-filter me-1"></i> {{ \Carbon\Carbon::parse(request('tanggal'))->format('d M') }}
                    </div>
                @else
                    <small class="opacity-50" style="font-size: 11px;">Semua Data</small>
                @endif
            </div>

            <div class="card glass-card border-0 p-3">
                <small class="fw-bold text-secondary mb-2 d-block"><i class="bi bi-sliders me-1"></i> FILTER & OPSI</small>
                
                <div class="mb-3">
                    <form action="{{ route('admin.dashboard') }}" method="GET">
                        <div class="input-group input-group-sm">
                            <input type="date" name="tanggal" class="form-control" 
                                   value="{{ request('tanggal') }}" onchange="this.form.submit()">
                            @if(request('tanggal'))
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary"><i class="bi bi-x"></i></a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="mb-3">
                    <form action="{{ route('admin.hapus_bulan') }}" method="POST" class="delete-form">
                        @csrf @method('DELETE')
                        <div class="input-group input-group-sm">
                            <select name="bulan" class="form-select" required>
                                <option value="">Hapus Bulan...</option>
                                @foreach($months as $m)
                                    <option value="{{ $m }}">{{ \Carbon\Carbon::parse($m)->translatedFormat('M Y') }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i></button>
                        </div>
                    </form>
                </div>

                <hr class="my-2 opacity-25">

                <div class="d-flex flex-column gap-2">
                    <button class="btn btn-outline-dark btn-sm text-start" data-bs-toggle="modal" data-bs-target="#bgModal">
                        <i class="bi bi-image me-2"></i> Ganti Background
                    </button>
                    <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-primary btn-sm text-start">
                        <i class="bi bi-eye me-2"></i> Lihat Website User
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8 col-lg-9">
        <div class="card glass-card border-0 p-4 h-100 d-flex flex-column">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-folder2-open me-2 text-primary"></i> DATA PEMOHON</h5>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm rounded-circle shadow-sm" title="Refresh">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>

            <div class="accordion flex-grow-1 mb-3" id="accordionBerkas">
                @forelse($data as $no_permohonan => $items)
                    @php 
                        $user = $items->first(); 
                        $isChecked = $user->is_checked; 
                    @endphp
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#acc{{ $loop->index }}">
                                <div class="row w-100 align-items-center g-0">
                                    <div class="col-md-5 mb-1 mb-md-0">
                                        <div class="fw-bold text-dark text-uppercase d-flex align-items-center">
                                            @if($isChecked)
                                                <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                                            @endif
                                            {{ $user->nama_lengkap }}
                                        </div>
                                        <div class="text-muted small ms-1"><i class="bi bi-hash"></i> {{ $no_permohonan }}</div>
                                    </div>

                                    <div class="col-md-5 mb-1 mb-md-0">
                                        <span class="badge bg-light text-dark border me-1">{{ $user->lokasi_wawancara }}</span>
                                        <span class="badge bg-warning bg-opacity-25 text-dark border border-warning">
                                            <i class="bi bi-camera me-1"></i> {{ \Carbon\Carbon::parse($user->tanggal_foto)->format('d/m/Y') }}
                                        </span>
                                    </div>

                                    <div class="col-md-2 text-end">
                                        <span class="badge bg-primary rounded-pill">{{ $items->count() }} File</span>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        
                        <div id="acc{{ $loop->index }}" class="accordion-collapse collapse" data-bs-parent="#accordionBerkas">
                            <div class="accordion-body bg-light">
                                
                                <div class="d-flex gap-2 mb-3">
                                    <button class="btn btn-warning btn-sm text-white shadow-sm" title="Edit Data"
                                            onclick="editData('{{ $no_permohonan }}', '{{ $user->nama_lengkap }}', '{{ $user->no_wa }}', '{{ $user->lokasi_wawancara }}')">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>

                                    <a href="https://wa.me/{{ $user->no_wa }}" target="_blank" class="btn btn-success btn-sm shadow-sm font-monospace">
                                        <i class="fab fa-whatsapp me-1"></i> {{ $user->no_wa }}
                                    </a>

                                    <form action="{{ route('admin.toggle_check', $no_permohonan) }}" method="POST">
                                        @csrf @method('PATCH')
                                        @if($isChecked)
                                            <button type="submit" class="btn btn-secondary btn-sm shadow-sm" title="Batalkan status cek">
                                                <i class="bi bi-x-circle me-1"></i> Batal
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-outline-primary btn-sm shadow-sm" title="Tandai sudah dicek">
                                                <i class="bi bi-check2-square me-1"></i> Sudah Dicek
                                            </button>
                                        @endif
                                    </form>
                                </div>

                                <div class="list-group">
                                    @foreach($items as $file)
                                        @php 
                                            $ext = pathinfo($file->path_file, PATHINFO_EXTENSION); 
                                            $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']);
                                        @endphp

                                        <div class="list-group-item d-flex justify-content-between align-items-center p-2">
                                            <div class="d-flex align-items-center text-truncate" style="flex: 1;">
                                                <div class="me-3 fs-5">
                                                    @if($isImage) <i class="fas fa-file-image text-primary"></i>
                                                    @else <i class="fas fa-file-pdf text-danger"></i> @endif
                                                </div>
                                                <div class="text-truncate">
                                                    <span class="fw-bold text-dark d-block text-truncate" title="{{ $file->nama_file_asli }}">
                                                        {{ $file->nama_file_asli }}
                                                    </span>
                                                    <div class="small text-muted">{{ $file->created_at->format('H:i') }} WIB</div>
                                                </div>
                                            </div>

                                            <div class="btn-group btn-group-sm ms-2">
                                                <a href="{{ asset('storage/'.$file->path_file) }}" target="_blank" class="btn btn-outline-info" title="Lihat">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ asset('storage/'.$file->path_file) }}" download class="btn btn-outline-success" title="Unduh">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <form action="{{ route('admin.hapus', $file->id) }}" method="POST" class="delete-form d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Hapus" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 flex-grow-1">
                        <i class="bi bi-inbox fs-1 text-muted opacity-50"></i>
                        <p class="text-muted small mt-2">Belum ada data masuk.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-auto pagination-wrapper">
                {{ $permohonanPage->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bgModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.bg') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h6 class="modal-title fw-bold">Ganti Background</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body"><input type="file" name="bg_image" class="form-control" required></div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary btn-sm">Simpan</button></div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="formEdit" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-warning text-white"><h6 class="modal-title fw-bold">Edit Data</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-2"><label class="small fw-bold">Nama</label><input type="text" name="nama_lengkap" id="editNama" class="form-control" required></div>
                    <div class="mb-2"><label class="small fw-bold">Whatsapp</label><input type="number" name="no_wa" id="editWa" class="form-control" required></div>
                    <div class="mb-2"><label class="small fw-bold">Lokasi</label>
                        <select name="lokasi_wawancara" id="editLokasi" class="form-select" required>
                            <option value="Kantor Imigrasi">Kantor Imigrasi</option>
                            <option value="Unit Layanan Paspor">Unit Layanan Paspor</option>
                            <option value="Mall Pelayanan Publik">Mall Pelayanan Publik</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-warning text-white btn-sm fw-bold">Update</button></div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success')) Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', timer: 1500, showConfirmButton: false }); @endif
    
    // Konfirmasi Hapus
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({ 
                title: 'Hapus?', 
                text: 'Data yang dihapus tidak bisa dikembalikan!',
                icon: 'warning', 
                showCancelButton: true, 
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus', 
                cancelButtonText: 'Batal' 
            })
            .then((result) => { if (result.isConfirmed) { this.submit(); } });
        });
    });

    // Script Modal Edit
    function editData(no, nama, wa, lokasi) {
        document.getElementById('editNama').value = nama;
        document.getElementById('editWa').value = wa;
        document.getElementById('editLokasi').value = lokasi;
        let url = "{{ route('admin.update_data', ':id') }}";
        document.getElementById('formEdit').action = url.replace(':id', no);
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }
</script>
@endsection