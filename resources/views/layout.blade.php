<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIBERKAS - Imigrasi Wonosobo</title>
    
    <link rel="shortcut icon" href="{{ asset('images/logo_imigrasi.png') }}" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --imi-blue: #002855; }
        
        body {
            font-family: 'Inter', sans-serif;
            
            /* Logika Background: Jika ada data bg dari controller pakai itu, jika tidak pakai default */
            @if(isset($bg) && $bg)
                background-image: url("{{ asset('storage/'.$bg) }}");
            @else
                background: linear-gradient(135deg, #002855, #00509d);
            @endif

            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            
            min-height: 100vh;
            width: 100vw;
            overflow-x: hidden;
        }

        /* Overlay gelap agar tulisan lebih terbaca di atas background */
        .overlay { 
            background: rgba(0, 40, 85, 0.5); 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            z-index: -1; 
        }
        
        /* Glass Card Global (Bisa dipakai di Admin/Login) */
        .glass-card { 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(15px); 
            border: 1px solid rgba(255, 255, 255, 0.5); 
            border-radius: 16px; 
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3); 
        }

        /* Utility Classes */
        .form-label { margin-bottom: 2px; font-size: 0.8rem; }
        .form-control, .form-select { font-size: 0.9rem; padding: 8px 12px; }
        .mb-3 { margin-bottom: 12px !important; }
    </style>
</head>
<body>
    
    <div class="overlay"></div>
    
    <div class="{{ Request::is('/') || Request::is('login') ? 'container' : 'container-fluid px-4 py-4' }}">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>