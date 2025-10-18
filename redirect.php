<?php

//================================================
//                    Redirect
//================================================

require 'config.php';

$request = trim($_SERVER['REQUEST_URI'], '/');

if ($request) {
    $stmt = $conn->prepare("SELECT long_url FROM urls WHERE short_code = ?");
    $stmt->bind_param("s", $request);
    $stmt->execute();
    $stmt->bind_result($long_url);
    if ($stmt->fetch()) {
        header("Location: " . $long_url);
        exit;
    } else {
        http_response_code(404);
        echo "<h1 style='color:white;background:#0d1117;text-align:center;padding:20px;'>404 - Shortlink tidak ditemukan</h1>";
    }
} else {
    header("Location: index.php");
    exit;
}
?>