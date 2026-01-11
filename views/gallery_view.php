<h2 class="mb-4">Fotoğraf Galerisi</h2>
<div class="row">
    <?php 
    $count = 0;
    foreach ($photos as $photo): 
        $count++;
    ?>
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <a href="images/<?php echo $photo->filename; ?>" target="_blank">
                    <img src="images/thumbnails/<?php echo $photo->filename; ?>" class="card-img-top" alt="...">
                </a>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($photo->title); ?></h5>
                    <p class="card-text"><small class="text-muted">Yazar: <?php echo htmlspecialchars($photo->author); ?></small></p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if ($count == 0): ?>
        <div class="alert alert-warning">Henüz hiç fotoğraf yüklenmemiş.</div>
    <?php endif; ?>
</div>