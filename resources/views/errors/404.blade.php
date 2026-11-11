<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Di Temukan</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            color: #1f2937;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .card {
            background: white;
            padding: 40px 50px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            text-align: center;
            max-width: 520px;
            width: 90%;
        }
        .code {
            font-size: 72px;
            font-weight: 700;
            color: #dc2626;
            margin: 0;
            line-height: 1;
        }
        h1 {
            margin: 18px 0 10px;
            font-size: 32px;
        }
        p {
            margin: 0 0 24px;
            font-size: 18px;
            color: #4b5563;
        }
        .btn {
            display: inline-block;
            text-decoration: none;
            background: #2563eb;
            color: white;
            padding: 12px 22px;
            border-radius: 10px;
            font-weight: 600;
        }
        .btn:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="code">404</div>
        <h1>Halaman Tidak Di Temukan</h1>
        <p>Halaman yang Anda cari mungkin sudah dipindahkan, dihapus, atau tidak tersedia.</p>
        <a href="{{ route('dashboard') }}" class="btn">Kembali Ke halaman Dashboard</a>
    </div>
</body>
</html>
