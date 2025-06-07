<form action="<?= BASE_URL ?>/includes/upload_handler.php" method="POST" enctype="multipart/form-data" class="mt-4">
    <?php if (!empty($_SESSION['upload_errors'])): ?>
        <div class="alert alert-danger">
            <?php foreach ($_SESSION['upload_errors'] as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
            <?php unset($_SESSION['upload_errors']); ?>
        </div>
    <?php endif; ?>
    
    <div class="mb-3">
        <label for="image" class="form-label">Выберите изображение (JPG, PNG, GIF)</label>
        <input class="form-control" type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif" required>
        <div class="form-text">Максимальный размер: 5MB</div>
    </div>
    
    <div class="mb-3">
        <label for="title" class="form-label">Название</label>
        <input type="text" class="form-control" id="title" name="title" maxlength="100">
    </div>
    
    <div class="mb-3">
        <label for="description" class="form-label">Описание</label>
        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
    </div>
    
    <button type="submit" class="btn btn-primary">Загрузить</button>
</form>

<div id="uploadResult"></div>

<script>
document.getElementById('uploadForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const response = await fetch('/includes/upload_handler.php', {
        method: 'POST',
        body: formData
    });
    
    const result = await response.json();
    const resultDiv = document.getElementById('uploadResult');
    
    if (result.status === 'success') {
        resultDiv.innerHTML = `
            <div class="alert alert-success">
                ${result.message}
            </div>
            <img src="/uploads/images/${result.filename}" class="img-thumbnail" style="max-height: 200px;">
        `;
    } else {
        resultDiv.innerHTML = `
            <div class="alert alert-danger">
                Ошибка: ${result.message}
            </div>
        `;
    }
});
</script>