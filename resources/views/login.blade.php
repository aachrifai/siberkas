@extends('layout')

@section('content')
<style>
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

    .input-group-text {
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        color: #002855;
    }
    
    .form-control:focus {
        border-color: #00509d;
        box-shadow: 0 0 0 0.25rem rgba(0, 80, 157, 0.15);
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-5 col-lg-4">
            
            <div class="card glass-card p-4 p-md-5 border-0">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/logo_imigrasi.png') }}" alt="Logo" height="70" class="mb-3">
                    <h4 class="fw-bold text-dark mb-0">LOGIN PETUGAS</h4>
                    <p class="text-muted small">Silakan masuk untuk mengelola berkas</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm small text-center mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login.proses') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">EMAIL</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="nama@email.com" required autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary small">PASSWORD</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="******" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-imi w-100 py-3 fw-bold shadow rounded-pill">
                        <i class="bi bi-box-arrow-in-right me-2"></i> MASUK
                    </button>
                </form>

                <div class="text-center mt-4 border-top pt-3">
                    <a href="{{ route('home') }}" class="text-decoration-none text-muted small">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Halaman Utama
                    </a>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <small class="text-white-50 opacity-75">&copy; {{ date('Y') }} Kantor Imigrasi Kelas II Non TPI Wonosobo</small>
            </div>

        </div>
    </div>
</div>
@endsection