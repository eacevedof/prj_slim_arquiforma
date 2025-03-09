<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data["message"] ?? "" ?></title>
    <link rel="stylesheet" href="/modules/open/home/home.css">
    <script src="/modules/open/home/home.js"></script>
</head>
<body>
    <p><?= $data["message"] ?? "" ?></p>
    <p><?= date("Y-m-d H:i:s"); ?></p>
    <p><?= $_SERVER["REMOTE_ADDR"]; ?></p>
</body>
</html>