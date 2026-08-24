<!DOCTYPE html>
<html>
<head>
    <title>Akses Login Mitra - Sistem Informasi Kerja Sama</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Halo, {{ $user->name }}</h2>
    <p>Akun Anda untuk mengakses Sistem Informasi Kerja Sama (WD4) telah berhasil dibuat.</p>
    
    <p>Berikut adalah informasi kredensial login Anda:</p>
    <ul style="background: #f4f4f4; padding: 15px; border-radius: 5px; list-style-type: none;">
        <li><strong>Email:</strong> {{ $user->email }}</li>
        <li><strong>Password:</strong> {{ $password }}</li>
    </ul>

    <p>Silakan gunakan informasi di atas untuk masuk ke dalam sistem. Demi keamanan, kami menyarankan Anda untuk segera mengubah password setelah berhasil login pertama kali.</p>
    
    <p>Terima kasih,<br>Tim Administrator WD4</p>
</body>
</html>
