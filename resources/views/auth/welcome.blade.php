<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Sistem Kerjasama DUDIKA</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            text-align: center;
        }

        .btn {
            background: #007bff;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background: #0056b3;
        }
    </style>

</head>

<body>

    <div class="container">

        <h1>Sistem Informasi Kerjasama DUDIKA</h1>

        <p>
            Sistem Informasi Kerjasama Kampus dengan Dunia Usaha dan Industri
        </p>

        <br>

        <a href="{{ route('login') }}" class="btn">Masuk ke Sistem</a>

    </div>

</body>

</html>