<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Masuk') · Warna Tanjung Jaya</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: #eef2f9;
            min-height: 100vh;
        }

        /* Latar kanan yang lembut */
        .auth-pane {
            background:
                radial-gradient(circle at 10% 15%, rgba(37, 99, 235, 0.10), transparent 40%),
                radial-gradient(circle at 90% 85%, rgba(79, 70, 229, 0.10), transparent 40%),
                linear-gradient(135deg, #f8fafc 0%, #e8eef8 100%);
        }

        /* Panel branding kiri */
        .brand-pane {
            background:
                radial-gradient(circle at 20% 25%, rgba(96, 165, 250, 0.28), transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(129, 140, 248, 0.30), transparent 50%),
                linear-gradient(150deg, #1e3a8a 0%, #3730a3 55%, #4c1d95 100%);
        }

        /* Pola titik halus di panel branding */
        .brand-pane::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.14) 1px, transparent 1px);
            background-size: 22px 22px;
            mask-image: radial-gradient(circle at 50% 40%, black, transparent 75%);
            -webkit-mask-image: radial-gradient(circle at 50% 40%, black, transparent 75%);
            pointer-events: none;
        }

        @keyframes floaty {
            0%, 100% { transform: translateY(0) }
            50%      { transform: translateY(-12px) }
        }
        .floaty { animation: floaty 6s ease-in-out infinite; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px) }
            to   { opacity: 1; transform: translateY(0) }
        }
        .fade-up { animation: fadeUp 0.6s cubic-bezier(0.22, 1, 0.36, 1) both; }
        .fade-up-1 { animation-delay: 0.05s }
        .fade-up-2 { animation-delay: 0.12s }
        .fade-up-3 { animation-delay: 0.19s }
        .fade-up-4 { animation-delay: 0.26s }

        /* Tombol peran terpilih */
        .role-btn.is-active {
            border-color: #2563eb;
            background: linear-gradient(135deg, rgba(239, 246, 255, 0.95), rgba(224, 231, 255, 0.75));
            box-shadow: 0 10px 25px -8px rgba(37, 99, 235, 0.45);
        }
    </style>
</head>
<body class="text-slate-800">
    <div class="min-h-screen flex">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
