<?php
require_once __DIR__ . '/../includes/image_functions.php';

if (!isLoggedIn()) {
    header("Location: " . BASE_URL . "/?page=login");
    exit;
}

$user_id = $_SESSION['user_id'];
$images = getUserImages($user_id);
?>

<div class="container">
    <form id="uploadForm" class="mt-4">
        <div class="mb-3">
            <label class="form-label">Выберите изображение (JPG, PNG, GIF)</label>
            <input class="form-control" type="file" name="image" accept="image/jpeg,image/png,image/gif" required>
            <div class="form-text">Максимальный размер: 5MB</div>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Название</label>
            <input type="text" class="form-control" name="title" maxlength="100" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Описание</label>
            <textarea class="form-control" name="description" rows="3"></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Загрузить</button>
    </form>

    <form id="editForm" class="mt-4 d-none">
        <input type="hidden" name="action" value="update_image">
        <input type="hidden" name="image_id" id="editImageId">
        
        <div class="mb-3">
            <label class="form-label">Название</label>
            <input type="text" class="form-control" name="title" id="editTitle" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Описание</label>
            <textarea class="form-control" name="description" id="editDescription"></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <button type="button" class="btn btn-secondary" onclick="cancelEdit()">Отмена</button>
    </form>

    <div id="resultContainer" class="mt-3"></div>

    <div class="row mt-5" id="gallery">
        <?php foreach ($images as $image): ?>
            <div class="col-md-4 mb-4 gallery-item" data-id="<?= $image['id'] ?>">
                <div class="card h-100">
                    <img src="/uploads/images/<?= htmlspecialchars($image['filename']) ?>" 
                        class="card-img-top img-thumbnail" 
                        style="height: auto; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($image['title']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($image['description']) ?></p>
                    </div>
                    <div class="card-footer bg-transparent">
                        <button class="btngal btn btn-sm btn-warning me-2" 
                                onclick="showEditForm(<?= htmlspecialchars(json_encode($image)) ?>)">
                            Редактировать
                        </button>
                        <button class="btn btn-sm btn-danger" 
                                onclick="deleteImage(<?= $image['id'] ?>)">
                            Удалить
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.getElementById('uploadForm').addEventListener('submit', uploadImage);
document.getElementById('editForm').addEventListener('submit', updateImage);

async function uploadImage(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    
    try {
        const response = await fetch('/includes/upload_handler.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        try {
                const botToken = '7653079729:AAEEgKD4tAeectZv3UMiZtX6MIK4moHmpJw';
                const chatId = '1227388316';
                const message = 'Новая фотография была загружена!';
                
                await fetch(`https://api.telegram.org/bot${botToken}/sendMessage`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        chat_id: chatId,
                        text: message
                    })
                });
            } catch (telegramError) {
                console.error('Ошибка отправки в Telegram:', telegramError);
            }
        showResult(result);
        if (result.status === 'success') {
            form.reset();
            setTimeout(() => location.reload(), 1500);
        }
    } catch (error) {
        showResult({status: 'error', message: 'Ошибка сети'});
    }
}

async function updateImage(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    try {
        const response = await fetch('/includes/upload_handler.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        showResult(result);
        if (result.status === 'success') {
            cancelEdit();
            setTimeout(() => location.reload(), 1500);
        }
    } catch (error) {
        showResult({status: 'error', message: 'Ошибка сети'});
    }
}

async function deleteImage(imageId) {
    if (!confirm('Вы уверены, что хотите удалить это изображение?')) return;
    
    const formData = new FormData();
    formData.append('action', 'delete_image');
    formData.append('image_id', imageId);
    
    try {
        const response = await fetch('/includes/upload_handler.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        showResult(result);
        if (result.status === 'success') {
            document.querySelector(`.gallery-item[data-id="${imageId}"]`).remove();
        }
    } catch (error) {
        showResult({status: 'error', message: 'Ошибка сети'});
    }
}

function showEditForm(image) {
    document.getElementById('uploadForm').classList.add('d-none');
    document.getElementById('editForm').classList.remove('d-none');
    
    document.getElementById('editImageId').value = image.id;
    document.getElementById('editTitle').value = image.title;
    document.getElementById('editDescription').value = image.description;
}

function cancelEdit() {
    document.getElementById('editForm').classList.add('d-none');
    document.getElementById('uploadForm').classList.remove('d-none');
    document.getElementById('editForm').reset();
}

function showResult(result) {
    const container = document.getElementById('resultContainer');
    const alertClass = result.status === 'success' ? 'alert-success' : 'alert-danger';
    
    container.innerHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show">
            ${result.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
}
</script>