<?php
// Oturum kontrolü (diğer sayfalarda tekrar kontrol etmemek için)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kullanıcı giriş durumu
$isLoggedIn = isset($_SESSION['user']);
$userName = $isLoggedIn ? $_SESSION['user']['name'] : '';

// Kullanıcı coin miktarını veritabanından çekme
$userCoins = 0;
if ($isLoggedIn) {
    require_once 'login/config.php';
    
    $stmt = $conn->prepare("SELECT coin FROM users WHERE google_id = ?");
    $stmt->bind_param("s", $_SESSION['user']['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userCoins = $row['coin'];
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="images/logo.png" alt="Logo" class="logo">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Make CountryBalls</a>
                </li>
                
                <!-- Fun Menüsü -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="funDropdown" 
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Fun
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="funDropdown">
                        <li><a class="dropdown-item" href="cbcoin/daily.php"><i class="fas fa-calendar-day me-2"></i>Daily CBCoin</a></li>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" 
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Links
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="social-media/dc.php"><i class="fab fa-discord me-2"></i>Discord</a></li>
                        <li><a class="dropdown-item" href="social-media/yt.php"><i class="fab fa-youtube me-2"></i>YouTube</a></li>
                        <li><a class="dropdown-item" href="social-media/ig.php"><i class="fab fa-instagram me-2"></i>Instagram</a></li>
                        <li><a class="dropdown-item" href="social-media/tt.php"><i class="fab fa-tiktok me-2"></i>TikTok</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact</a>
                </li>
            </ul>
            
            <ul class="navbar-nav">
                <?php if($isLoggedIn): ?>
                    <!-- Profil Menüsü -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileDropdown" 
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-check profile-icon me-2"></i>
                            <span><?= htmlspecialchars($userName) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end dropdown-menu-custom" aria-labelledby="profileDropdown">
                            <!-- CBCoin Bilgisi -->
                            <li class="dropdown-coin-display">
                                <span class="coin-text">
                                    <i class="fas fa-coins me-2 text-warning"></i>
                                    <strong>CBCoin</strong>
                                </span>
                                <span class="coin-amount"><?= number_format($userCoins) ?></span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            
                            <!-- Diğer Menü Öğeleri -->
                            <li><a class="dropdown-item dropdown-item-custom" href="settings.php"><i class="fas fa-user-cog me-2"></i>Settings</a></li>
                            <li><a class="dropdown-item dropdown-item-custom" href="gallery.php"><i class="fas fa-images me-2"></i>Gallery</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item dropdown-item-custom text-danger" href="login/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="login/">
                            <i class="fas fa-user-circle profile-icon me-2"></i>
                            <span>Login</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>