<?php
session_start();
require_once 'login/config.php'; // Düzeltildi (login/ eklendi)

if (!isset($_SESSION['user'])) {
    header('Location: login/');
    exit;
}

$userId = $_SESSION['user']['id'];
$userDir = "users/{$userId}";

// Klasör yoksa oluştur
if (!file_exists($userDir)) {
    mkdir($userDir, 0777, true);
}

$images = glob("{$userDir}/*.png");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Gallery - CountryBalls</title>
    <!-- CSS Bağlantıları -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
     <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="css/main-style.css">
    <style>
        /* Özel Galeri Stilleri */
        .gallery-container {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .gallery-item {
            position: relative;
            margin-bottom: 20px;
            transition: transform 0.3s;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #dee2e6;
        }
        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .gallery-actions {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: opacity 0.3s;
            display: flex;
            gap: 10px;
        }
        .gallery-item:hover .gallery-actions {
            opacity: 1;
        }
        .gallery-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .empty-gallery {
            text-align: center;
            padding: 40px;
            background-color: white;
            border-radius: 10px;
        }
            /* Animasyonlar */

    .gallery-item {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* İsteğe bağlı: Fare üzerine gelince sil butonunu göster */
    .gallery-item:hover .delete-btn {
        opacity: 1 !important;
    }
    
    @keyframes fadeInOut {
        0% { opacity: 0; transform: translateY(-20px); }
        10% { opacity: 1; transform: translateY(0); }
        90% { opacity: 1; transform: translateY(0); }
        100% { opacity: 0; transform: translateY(-20px); }
    }
    </style>
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>
    
    <div class="container py-5">
        <div class="gallery-container">
            <h2 class="text-center mb-4"><i class="fas fa-images me-2"></i>My Gallery</h2>
            
            <?php if (empty($images)): ?>
                <div class="empty-gallery">
                    <i class="fas fa-image fa-4x text-muted mb-3"></i>
                    <h4>Your gallery is empty</h4>
                    <p class="text-muted">Create and save your CountryBalls from the editor!</p>
                    <a href="index.php" class="btn btn-danger mt-3">
                        <i class="fas fa-plus-circle me-2"></i>Create Now
                    </a>
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php foreach ($images as $image): ?>
                        <div class="col">
                            <div class="gallery-item">
                                <img src="<?= $image ?>" class="gallery-img">
                                <div class="gallery-actions">
                                    <a href="<?= $image ?>" download class="btn btn-success btn-sm">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm delete-btn" data-filename="<?= basename($image) ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scriptler -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
        const filename = this.dataset.filename;
        const galleryItem = this.closest('.gallery-item');
        
        // Anında animasyon başlat
        galleryItem.style.opacity = '0';
        galleryItem.style.transform = 'scale(0.8)';
        galleryItem.style.transition = 'all 0.3s ease-out';
        
        try {
            const response = await fetch('php/deleteImage.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `filename=${encodeURIComponent(filename)}`
            });
            
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.error || 'Delete failed');
            }
            
            // Animasyon tamamlandığında kaldır
            setTimeout(() => {
                galleryItem.remove();
                
                // Galeri boşsa mesaj göster
                if (document.querySelectorAll('.gallery-item').length === 0) {
                    document.querySelector('.gallery-container').innerHTML = `
                        <div class="empty-gallery text-center py-5">
                            <i class="fas fa-image fa-4x text-muted mb-3"></i>
                            <h4>Your gallery is empty</h4>
                        </div>
                    `;
                }
            }, 300);
            
        } catch (error) {
            // Hata durumunda animasyonu geri al
            galleryItem.style.opacity = '1';
            galleryItem.style.transform = 'scale(1)';
            console.error('Delete error:', error);
        }
    });
});
    </script>
</body>
</html>