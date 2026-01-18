<?php
// Parse a positive integer safely (used for quantities/pagination).
function parse_positive_int($value, $default) {
    $n = filter_var($value, FILTER_VALIDATE_INT);
    if ($n === false || $n <= 0) {
        return $default;
    }
    return (int)$n;
}

// Compute thumbnail/full URLs (watermark/thumbnail fallback).
function photo_urls($filename) {
    $name = basename((string)$filename);
    if ($name === '') {
        return ['full' => '', 'thumb' => ''];
    }

    $wmPath = __DIR__ . '/../public/images/watermarked/' . $name;
    $thumbPath = __DIR__ . '/../public/images/thumbnails/' . $name;

    $full = is_file($wmPath) ? ('images/watermarked/' . $name) : ('images/' . $name);
    $thumb = is_file($thumbPath) ? ('images/thumbnails/' . $name) : ('images/' . $name);

    return ['full' => $full, 'thumb' => $thumb];
}

// Safe Mongo ObjectId parsing.
function oid_from_string($id) {
    try {
        return new MongoDB\BSON\ObjectId($id);
    } catch (Exception $e) {
        return null;
    }
}

// Small helper for image-type checks.
function is_jpeg_type($type) {
    $t = strtolower((string)$type);
    return ($t === 'jpg' || $t === 'jpeg');
}

function gallery_filter_for_user($userId) {
    // Guests see public; logged-in users also see their own private uploads.
    if ($userId === null) {
        return [
            '$or' => [
                ['visibility' => 'public'],
                ['visibility' => ['$exists' => false]],
                ['visibility' => null],
            ],
        ];
    }

    return [
        '$or' => [
            ['visibility' => 'public'],
            ['visibility' => ['$exists' => false]],
            ['visibility' => null],
            ['visibility' => 'private', 'owner_user_id' => $userId],
        ],
    ];
}

function create_thumbnail($src, $dest, $type) {
    // Create a 200x125 thumbnail
    list($width, $height) = getimagesize($src);
    $newwidth = 200;
    $newheight = 125;

    $thumb = imagecreatetruecolor($newwidth, $newheight);

    $isJpeg = is_jpeg_type($type);
    $source = $isJpeg ? imagecreatefromjpeg($src) : imagecreatefrompng($src);

    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    $isJpeg ? imagejpeg($thumb, $dest) : imagepng($thumb, $dest);

    imagedestroy($thumb);
    if (isset($source)) {
        $isGdImage = function_exists('class_exists') && class_exists('GdImage') && ($source instanceof GdImage);
        if ($isGdImage || is_resource($source)) {
            imagedestroy($source);
        }
    }
}

function create_watermarked($src, $dest, $type, $text) {
    // Add a simple text watermark using GD.
    list($width, $height) = getimagesize($src);

    $isJpeg = is_jpeg_type($type);
    if ($isJpeg) {
        $img = imagecreatefromjpeg($src);
    } else {
        $img = imagecreatefrompng($src);
        imagealphablending($img, true);
        imagesavealpha($img, true);
    }

    $margin = 10;
    $font = 5;
    $textWidth = imagefontwidth($font) * strlen($text);
    $textHeight = imagefontheight($font);
    $x = max($margin, $width - $textWidth - $margin);
    $y = max($margin, $height - $textHeight - $margin);

    $shadow = imagecolorallocatealpha($img, 0, 0, 0, 90);
    $color = imagecolorallocatealpha($img, 255, 255, 255, 70);
    imagestring($img, $font, $x + 1, $y + 1, $text, $shadow);
    imagestring($img, $font, $x, $y, $text, $color);

    $isJpeg ? imagejpeg($img, $dest) : imagepng($img, $dest);

    imagedestroy($img);
}
