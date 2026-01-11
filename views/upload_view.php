<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Yeni Fotoğraf Yükle</div>
            <div class="card-body">
                <form action="index.php?action=upload" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Başlık (Title)</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Yazar (Author)</label>
                        <input type="text" name="author" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fotoğraf (Max 1MB, JPG/PNG)</label>
                        <input type="file" name="image" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">Yükle</button>
                </form>
            </div>
        </div>
    </div>
</div>