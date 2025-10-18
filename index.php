<?php

//==================================================
//                     Index
//==================================================

require 'config.php';

function generateCode($length = 6) {
    return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $length);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $long_url = trim($_POST['long_url']);
    if (!filter_var($long_url, FILTER_VALIDATE_URL)) {
        $error = "Invalid URL!";
    } else {
        $check = $conn->prepare("SELECT short_code FROM urls WHERE long_url = ?");
        $check->bind_param("s", $long_url);
        $check->execute();
        $check->bind_result($existing_code);
        if ($check->fetch()) {
            $short_code = $existing_code;
        } else {
            do {
                $short_code = generateCode();
                $stmt = $conn->prepare("SELECT id FROM urls WHERE short_code = ?");
                $stmt->bind_param("s", $short_code);
                $stmt->execute();
                $stmt->store_result();
            } while ($stmt->num_rows > 0);

            $expires_at = date('Y-m-d H:i:s', time() + 5);
            $stmt = $conn->prepare("INSERT INTO urls (long_url, short_code, expires_at) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $long_url, $short_code, $expires_at);
            $stmt->execute();
        }

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $short_url = $scheme . "://" . $_SERVER['HTTP_HOST'] . "/" . $short_code;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dark URL Shortener</title>
<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
body {
    background: #0d1117;
    color: #c9d1d9;
    font-family: "Poppins", sans-serif;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
    animation: fadeIn 1s ease-in-out;
}
.container {
    background: #161b22;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 0 20px rgba(56, 139, 253, 0.2);
    text-align: center;
    width: 90%;
    max-width: 500px;
    transition: transform 0.3s;
}
.container:hover {
    transform: scale(1.02);
}
input[type=url] {
    width: 80%;
    padding: 12px;
    border-radius: 10px;
    border: none;
    margin-bottom: 20px;
    background: #0d1117;
    color: #c9d1d9;
    font-size: 15px;
}
button {
    background: linear-gradient(90deg, #238636, #2ea043);
    color: white;
    border: none;
    border-radius: 10px;
    padding: 10px 20px;
    cursor: pointer;
    font-size: 16px;
    transition: 0.3s;
}
button:hover {
    background: linear-gradient(90deg, #2ea043, #3fb950);
    transform: translateY(-2px);
}
.result {
    margin-top: 20px;
    background: #0d1117;
    border: 1px solid #30363d;
    border-radius: 10px;
    padding: 10px;
    color: #58a6ff;
    word-wrap: break-word;
    animation: fadeIn 1s;
}
.error {
    color: #ff6b6b;
    margin-top: 10px;
}
h1 {
    color: #58a6ff;
}
footer {
    margin-top: 30px;
    font-size: 13px;
    color: #8b949e;
}
.countdown {
    color: #ff6b6b;
    font-size: 14px;
    margin-top: 10px;
}
</style>
</head>
<body>
<div class="container">
    <h1>🔗 URL Shortener</h1>
    <form method="POST">
        <input type="url" name="long_url" placeholder="Enter a long URL..." required>
        <br>
        <button type="submit">Shorten!</button>
    </form>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php elseif (!empty($short_url)): ?>
        <div class="result" id="resultBox">
            🎯 Short URL: <a href="<?= $short_url ?>" target="_blank" id="shortLink"><?= $short_url ?></a>
            <div class="countdown" id="countdown">Links will be removed within <span id="timer">5</span> Second...</div>
        </div>
    <?php endif; ?>
</div>
<footer>Made with ❤️ by (Your name)</footer> <!-- you can change it to your name or your github name or copyright-->

<script>
const timerElement = document.getElementById("timer");
const resultBox = document.getElementById("resultBox");

if (timerElement) {
    let seconds = 5;
    const countdown = setInterval(() => {
        seconds--;
        timerElement.textContent = seconds;
        if (seconds <= 0) {
            clearInterval(countdown);
            resultBox.innerHTML = "❌ Link has been removed.";
            resultBox.style.color = "#ff6b6b";

            setTimeout(() => {
                const msg = resultBox;
                msg.style.transition = "all 0.6s ease";
                msg.style.opacity = "0";
                msg.style.transform = "translateY(-10px)";
                setTimeout(() => msg.remove(), 600);
            }, 1000);
        }
    }, 1000);
}
</script>
</body>
</html>
