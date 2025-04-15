<?php
// Oturum kontrolü
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kullanıcı giriş durumu
$isLoggedIn = isset($_SESSION['user']);
$userName = $isLoggedIn ? $_SESSION['user']['name'] : '';
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make CountryBalls</title>
    <meta property="og:description" content="Design your own Countryballs with just a few clicks! Choose flags, add eyes and effects, and build funny characters right in your browser. Free, fast, and fun! Brought to you by Bilish Studio for all Countryballs fans around the world.">
    <meta property="og:image" content="images/logo.png">
    <meta property="og:type" content="website">
    <meta name="author" content="CountryBalls.Fun">
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/main-style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/notifications.css">
<script src="js/notifications.js"></script>
</head>
<body class="d-flex flex-column min-vh-100">
<?php include 'navbar.php'; ?>
    <!-- Content -->
    <div class="flex-grow-1">
        <div id="container">
            <div id="canvas-controls">
                <button id="lock-selected" title="Seçili Öğeyi Kilitle/Aç">
                    <i class="fa fa-lock"></i>
                </button>
                <button id="download-png" title="Canvası PNG Olarak İndir">
                    <i class="fa fa-download"></i>
                </button>
                <button id="save-gallery" title="Save to Gallery" class="<?= $isLoggedIn ? '' : 'd-none' ?>">
                    <i class="fas fa-save"></i>
                </button>
                <button id="clear-canvas" title="Canvası Temizle">
                    <i class="fa fa-trash"></i>
                </button>
                <button id="remove-selected" title="Seçili Öğeyi Kaldır">
                    <i class="fa-solid fa-square-xmark"></i>
                </button>
            </div>
            
            <div id="canvas-container">
                <canvas id="main-canvas" width="600" height="600"></canvas>
            </div>
            
            <div id="canvas-controls">
                <button id="zoom-out" title="Öğeyi Küçült">
                    <i class="fas fa-search-minus"></i>
                </button>
                <button id="zoom-in" title="Öğeyi Büyült">
                    <i class="fas fa-search-plus"></i>
                </button>
                <button id="flip-horizontal" title="Yatay Çevir">
                    <i class="fas fa-arrows-alt-h"></i>
                </button>
                <button id="flip-vertical" title="Dikey Çevir">
                    <i class="fas fa-arrows-alt-v"></i>
                </button>
            </div>

            <div id="button-panel">
                <button data-category="countries">Countries</button>
                <button data-category="effects">Effects</button>
                <button data-category="eyes">Eyes</button>
                <button data-category="items">Items</button>
            </div>
            <div id="items-preview"></div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-danger text-white text-center py-4 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-6 d-flex justify-content-center">
                    <a href="social-media/ig.php" target="_blank" class="footer-icon mx-3">
                        <i class="fab fa-instagram fa-2x"></i>
                    </a>
                    <a href="social-media/tt.php" target="_blank" class="footer-icon mx-3">
                        <i class="fab fa-tiktok fa-2x"></i>
                    </a>
                    <a href="social-media/dc.php" target="_blank" class="footer-icon mx-3">
                        <i class="fab fa-discord fa-2x"></i>
                    </a>
                    <a href="social-media/yt.php" target="_blank" class="footer-icon mx-3">
                        <i class="fab fa-youtube fa-2x"></i>
                    </a>
                </div>
                <div class="col-md-6">
                    <p class="mb-0">Bilish Studio © 2025 Copyright | All Rights Reserved</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    <script src="js/appLogic.js"></script>
    <script src="js/canvasControls.js"></script>
    <script src="js/transformTool.js"></script>
    <script src="js/savehook.js"></script>
    
    <script>
        // Bootstrap tooltip'leri aktif et
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Dropdown menülerin otomatik kapanmasını ayarla
        document.querySelectorAll('.dropdown-menu').forEach(function(element){
            element.addEventListener('click', function(e){
                e.stopPropagation();
            });
        });

        // Galeri Kaydetme Fonksiyonu
        document.getElementById('save-gallery').addEventListener('click', async function() {
            const canvas = document.getElementById('main-canvas');
            const imageData = canvas.toDataURL('image/png');
            const statusElement = document.createElement('div');
            
            // Status mesajını hazırla
            statusElement.className = 'save-status';
            document.body.appendChild(statusElement);

            try {
                const response = await fetch('php/saveToGallery.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ image: imageData })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    statusElement.textContent = `Image saved to gallery (${result.count}/10)`;
                } else {
                    statusElement.textContent = result.message;
                    statusElement.classList.add('error');
                }
                
            } catch (error) {
                statusElement.textContent = 'Network error';
                statusElement.classList.add('error');
                console.error('Error:', error);
            }
            
            // 3 saniye sonra mesajı kaldır
            setTimeout(() => {
                statusElement.remove();
            }, 3000);
        });
    </script>
    <script src="js/notifications.js"></script>
</body>
</html>