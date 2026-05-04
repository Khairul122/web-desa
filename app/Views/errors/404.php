<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | Gampong Munye Pirak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #1B3A6B;
            --accent-color: #C9870C;
            --body-bg: #f8fafc;
        }
        body {
            background-color: var(--body-bg);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #334155;
            overflow: hidden;
        }
        .error-container {
            max-width: 600px;
            padding: 2rem;
        }
        .lottie-container {
            width: 300px;
            height: 300px;
            margin: 0 auto;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 900;
            color: var(--primary-color);
            line-height: 1;
            margin-bottom: 0.5rem;
            letter-spacing: -2px;
        }
        .error-title {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1rem;
        }
        .error-desc {
            color: #64748b;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        .btn-home {
            background-color: var(--primary-color);
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(27, 58, 107, 0.2);
        }
        .btn-home:hover {
            background-color: var(--accent-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(201, 135, 12, 0.3);
        }
        .bg-motif {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(var(--primary-color) 0.5px, transparent 0.5px);
            background-size: 24px 24px;
            opacity: 0.05;
            z-index: -1;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="bg-motif"></div>
    
    <div class="error-container text-center">
        <h1 class="error-code">404</h1>
        <h2 class="error-title">Halaman Tidak Ditemukan</h2>
        <p class="error-desc">
            Sepertinya halaman yang Anda cari sedang "berkeliling" Gampong atau memang belum tersedia.
        </p>
        
        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
            <a href="<?= base_url() ?>" class="btn btn-home">
                <i class="bi bi-house-door me-2"></i> Kembali ke Beranda
            </a>
            <a href="<?= base_url('/kontak') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-chat-dots me-2"></i> Hubungi Kami
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
