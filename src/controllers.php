<?php
// src/controllers.php

function gallery_action() {
    // Veritabanından resimleri çek
    return findAll('photos');
}

function upload_action() {
    $targetDir = __DIR__ . '/../public/images/';
    $thumbDir = __DIR__ . '/../public/images/thumbnails/';
    
    // Dosya seçildi mi?
    if (!isset($_FILES['image']) || $_FILES['image']['error'] != 0) {
        echo "Dosya seçilmedi veya hata oluştu.";
        return;
    }

    $fileName = basename($_FILES['image']['name']);
    $targetFile = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // 1. Validasyon: Sadece JPG ve PNG [cite: 41]
    if ($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg") {
        die("Sadece JPG ve PNG dosyaları yüklenebilir.");
    }

    // 2. Validasyon: Boyut (1MB) [cite: 42]
    if ($_FILES['image']['size'] > 1048576) {
        die("Dosya boyutu 1MB'dan büyük olamaz.");
    }

    // Dosyayı kaydet
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        
        // 3. Thumbnail Oluşturma (GD Library) [cite: 47, 48]
        create_thumbnail($targetFile, $thumbDir . $fileName, $fileType);

        // 4. Veritabanına Kayıt [cite: 61]
        $document = [
            'filename' => $fileName,
            'title' => $_POST['title'] ?? 'Adsız',
            'author' => $_POST['author'] ?? 'Anonim',
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ];
        insertOne('photos', $document);

        header("Location: index.php?action=gallery");
        exit;
    } else {
        echo "Dosya yüklenirken bir hata oluştu.";
    }
}

function create_thumbnail($src, $dest, $type) {
    list($width, $height) = getimagesize($src);
    $newwidth = 200;
    $newheight = 125;

    $thumb = imagecreatetruecolor($newwidth, $newheight);

    if ($type == 'jpg' || $type == 'jpeg') {
        $source = imagecreatefromjpeg($src);
    } else {
        $source = imagecreatefrompng($src);
    }

    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    if ($type == 'jpg' || $type == 'jpeg') {
        imagejpeg($thumb, $dest);
    } else {
        imagepng($thumb, $dest);
    }
}
?>