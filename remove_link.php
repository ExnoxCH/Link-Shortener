<?php

//================================================
//                 RemoveLink
//================================================

require 'config.php';

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $stmt = $conn->prepare("DELETE FROM urls WHERE short_code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
}
?>