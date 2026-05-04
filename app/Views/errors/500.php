<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Kesalahan Server | Gampong Munye Pirak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #1B3A6B;
            --accent-color: #C9870C;
            --error-color: #be123c;
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
            width: 320px;
            height: 320px;
            margin: 0 auto;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 900;
            color: var(--error-color);
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
            background-image: radial-gradient(var(--error-color) 0.5px, transparent 0.5px);
            background-size: 32px 32px;
            opacity: 0.03;
            z-index: -1;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="bg-motif"></div>
    
    <div class="error-container text-center">
        <h1 class="error-code">500</h1>
        <h2 class="error-title">Terjadi Kesalahan Server</h2>
        <p class="error-desc">
            Sistem kami sedang mengalami gangguan teknis sejenak. Tim IT Gampong sedang berupaya memperbaikinya.
        </p>
        
        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
            <a href="<?= base_url() ?>" class="btn btn-home">
                <i class="bi bi-arrow-clockwise me-2"></i> Coba Muat Ulang
            </a>
            <a href="https://wa.me/<?= preg_replace('/\D/', '', $whatsapp_number ?? '') ?>" class="btn btn-outline-success rounded-pill px-4">
                <i class="bi bi-whatsapp me-2"></i> Lapor Admin
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
