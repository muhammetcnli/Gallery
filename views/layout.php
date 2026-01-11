<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Foto Galeri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php?action=gallery">FotoGaleri</a>
            <div class="navbar-nav">
                <a class="nav-link" href="index.php?action=gallery">Galeri</a>
                <a class="nav-link" href="index.php?action=upload">Fotoğraf Yükle</a>
                <a class="nav-link" href="index.php?action=login">Giriş Yap</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php include $content; ?>
    </div>

</body>
</html>