@extends('layout')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

<style>
    /* --- Stylesheet Khusus --- */
    .glass-card {
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }
    
    .btn-imi {
        background: linear-gradient(135deg, #002855 0%, #00509d 100%);
        color: white;
        border: none;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .btn-imi:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 40, 85, 0.3);
        color: white;
    }

    /* Styling Input Group agar menyatu */
    .input-group-text {
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        color: #002855;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #00509d;
        box-shadow: 0 0 0 0.25rem rgba(0, 80, 157, 0.15);
    }

    /* Tombol Login Petugas (Pojok Kanan Atas) */
    .btn-admin-login {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        color: #fff; 
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .btn-admin-login:hover {
        background: rgba(255, 255, 255, 0.9);
        color: #002855;
    }
</style>

<div class="container py-4">
    
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('login') }}" class="btn btn-sm btn-admin-login rounded-pill px-4 py-2 fw-bold text-decoration-none">
            <i class="bi bi-shield-lock-fill me-2"></i>Login Petugas
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6"> 
            
            <div class="card glass-card p-4 p-md-5"> 
                
                <div class="text-center mb-4">
                    <img src="{{ asset('images/logo_imigrasi.png') }}" alt="Logo" height="75" class="mb-3">
                    <h3 class="fw-bold text-dark mb-0" style="letter-spacing: 1px;">SIBERKAS</h3>
                    <p class="text-muted small mb-0">Layanan Upload Kekurangan Berkas Imigrasi</p>
                </div>

                <div class="alert alert-primary bg-opacity-10 border-0 shadow-sm text-center mb-4">
                    <div class="fs-4 text-primary mb-2"><i class="bi bi-info-circle-fill"></i></div>
                    <h6 class="fw-bold text-primary">INFORMASI PENTING</h6>
                    <p class="small mb-0 text-dark">
                        Pengambilan paspor <b>5 hari kerja</b> setelah berkas lengkap.<br>
                        Cek status via WhatsApp: 
                        <a href="https://wa.me/628112698859" class="fw-bold text-decoration-none" target="_blank">0811-2698-859</a>
                    </p>
                </div>

                <form action="{{ route('kirim') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary small">NO. PERMOHONAN</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                <input type="text" inputmode="numeric" name="no_permohonan" class="form-control" placeholder="Cek bukti bayar..." required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary small">NO. WHATSAPP</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-whatsapp text-success"></i></span>
                                <input type="text" inputmode="numeric" name="no_wa" class="form-control" placeholder="08xxxxxxxx" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">NAMA LENGKAP</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            <input type="text" name="nama_lengkap" class="form-control" placeholder="Sesuai KTP/Paspor" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary small">TANGGAL FOTO</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                <input type="date" name="tanggal_foto" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary small">LOKASI WAWANCARA</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt-fill text-danger"></i></span>
                                <select name="lokasi_wawancara" class="form-select" required>
                                    <option value="" selected disabled>Pilih Lokasi...</option>
                                    <option value="Kantor Imigrasi">Kantor Imigrasi</option>
                                    <option value="Unit Layanan Paspor">Unit Layanan Paspor</option>
                                    <option value="Mall Pelayanan Publik">Mall Pelayanan Publik</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary small">FILE BERKAS (PDF/FOTO)</label>
                        <div class="input-group">
                            <input type="file" name="file_berkas" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            <span class="input-group-text"><i class="bi bi-cloud-upload"></i></span>
                        </div>
                        <div class="text-danger mt-1 text-center" style="font-size: 0.75rem;">
                            *Format PDF/JPG, Pastikan terbaca jelas (Maks 5MB)
                        </div>
                    </div>

                    <button type="submit" class="btn btn-imi w-100 py-3 fw-bold shadow rounded-pill" id="btnSubmit">
                        <i class="bi bi-paperplane-fill me-2"></i> KIRIM BERKAS
                    </button>
                </form>
                
                <div class="text-center mt-4 pt-3 border-top">
                    <small class="text-muted opacity-75">&copy; {{ date('Y') }} Kantor Imigrasi Kelas II Non TPI Wonosobo</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // 1. Notifikasi Sukses
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil Terkirim!',
            html: `
                <div class="text-center">
                    <p>Berkas Anda telah kami terima.</p>
                    <div class="alert alert-light border mt-2 small">
                        Pengambilan paspor <b>5 hari kerja</b> setelah berkas lengkap.<br>
                        Info lanjut WA: <b>0811-2698-859</b>
                    </div>
                </div>
            `,
            confirmButtonText: 'OK, Selesai',
            confirmButtonColor: '#002855'
        });
    @endif

    // 2. Notifikasi Error
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Gagal Mengirim',
            html: `
                <ul class="text-start mb-0 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            `,
            confirmButtonColor: '#d33'
        });
    @endif

    // 3. Loading Button Script
    document.getElementById('uploadForm').addEventListener('submit', function() {
        let btn = document.getElementById('btnSubmit');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mengirim...';
        btn.classList.add('disabled');
    });
</script>
@endsection