<?php
// Oturum kontrolü
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kullanıcı giriş durumu
$isLoggedIn = isset($_SESSION['user']);
$userName = $isLoggedIn ? $_SESSION['user']['name'] : '';
$userEmail = $isLoggedIn ? $_SESSION['user']['email'] : '';

// Webhook URL
$webhook_url = 'https://discord.com/api/webhooks/1360299236180365446/frgKa8qum23TDw0K4vqLkfXF6_PQldho4PWB1DjUWrSQVTjNxVzOaL0LnaVrDYKMlJfU';

// IP ve zaman kontrolü için dosya
$cache_file = 'ip_cache.json';
$cache_duration = 120; // 1 saat (60 dakika)

// IP adresini al
$ip_address = $_SERVER['REMOTE_ADDR'];

// JSON cache dosyasını oku
$cache_data = file_exists($cache_file) ? json_decode(file_get_contents($cache_file), true) : [];

// Mesaj durumu için değişkenler
$message_class = ''; // Mesaj kutucuğu sınıfı
$message_text = '';  // Mesaj metni

// Zaman kontrolü
$current_time = time();
if (isset($cache_data[$ip_address]) && ($current_time - $cache_data[$ip_address] < $cache_duration)) {
    $message_text = "A message has already been sent from this IP address. Please wait 1 hour.";
    $message_class = 'error-message';
}

// Form gönderimi kontrolü
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // En popüler e-posta servislerini kontrol et
    $allowed_email_domains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'aol.com'];
    $email_domain = substr(strrchr($email, "@"), 1);

    if (!in_array($email_domain, $allowed_email_domains)) {
        $message_text = "Geçersiz Mail Adresi.";
        $message_class = 'error-message';
    } else if (!empty($name) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($message)) {
        // Discord webhook gönderimi - Embed formatında
        $data = [
            'embeds' => [
                [
                    'title' => 'Yeni Mesaj Gönderildi',
                    'description' => $message,
                    'fields' => [
                        [
                            'name' => 'Ad',
                            'value' => $name,
                            'inline' => true
                        ],
                        [
                            'name' => 'E-posta',
                            'value' => $email,
                            'inline' => true
                        ]
                    ],
                    'color' => 5814783 // Embed rengi (Hex renk kodu)
                ]
            ]
        ];

        $options = [
            'http' => [
                'header'  => "Content-Type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($data),
            ],
        ];
        $context  = stream_context_create($options);
        $result = file_get_contents($webhook_url, false, $context);

        $cache_data[$ip_address] = $current_time;
        file_put_contents($cache_file, json_encode($cache_data));

        $message_text = "Message sent successfully!";
        $message_class = 'success-message';
    } else {
        $message_text = "Please fill out the form completely.";
        $message_class = 'error-message';
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <meta name="description" content="You can easily design your CountryBalls here and make your videos faster!">
    <meta name="author" content="CountryBalls.Fun">
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/main-style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        form {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
            margin: 100px auto;
        }
        label {
            display: block;
            font-size: 14px;
            margin-bottom: 6px;
            font-weight: bold;
            text-align: left;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        button {
            width: 100%;
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        button i {
            margin-right: 8px;
        }
        button:hover {
            background-color: #c82333;
        }
        h1 {
            font-size: 22px;
            margin-bottom: 20px;
            color: #dc3545;
        }
        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .logged-in-field {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
    <form method="POST">
        <h1><i class="fas fa-comment-dots"></i> Send Message</h1>
        
        <label for="name"><i class="fas fa-user"></i> Name:</label>
        <input type="text" id="name" name="name" 
               value="<?= $isLoggedIn ? htmlspecialchars($userName) : '' ?>" 
               placeholder="Please enter your name..." 
               <?= $isLoggedIn ? 'readonly class="logged-in-field"' : 'required' ?>>

        <label for="email"><i class="fas fa-envelope"></i> E-Mail:</label>
        <input type="email" id="email" name="email" 
               value="<?= $isLoggedIn ? htmlspecialchars($userEmail) : '' ?>" 
               placeholder="Enter your Email..." 
               <?= $isLoggedIn ? 'readonly class="logged-in-field"' : 'required' ?>>

        <label for="message"><i class="fas fa-edit"></i> Message:</label>
        <input type="text" id="message" name="message" placeholder="Write your message..." required>

        <button type="submit"><i class="fas fa-paper-plane"></i> Send</button>
        
        <?php if (!empty($message_text)) : ?>
            <div class="message <?= $message_class; ?>">
                <?= $message_text; ?>
            </div>
            <script>
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            </script>
        <?php endif; ?>
    </form>
    
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>