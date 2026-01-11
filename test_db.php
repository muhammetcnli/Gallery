<?php
// Sanal makinede genelde library kuruludur, require 'vendor/autoload.php' gerekmeyebilir.
// Eğer hata alırsan hocanın verdiği notlarda kütüphanenin nerede olduğu yazar.

try {
    $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
    echo "MongoDB Bağlantısı Başarılı!";
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}
?>