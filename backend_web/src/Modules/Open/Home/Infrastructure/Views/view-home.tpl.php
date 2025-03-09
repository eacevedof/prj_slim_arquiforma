<?php
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $seo["title"] ?? "" ?></title>
    <meta name="description" content="<?= $seo["meta_description"] ?? "" ?>">
    <meta name="keywords" content="<?= $seo["meta_keywords"] ?? "" ?>">
    <meta name="author" content="<?= $seo["meta_author"] ?? "" ?>">

    <link rel="stylesheet" href="/modules/open/home/home.css">
    <script src="/modules/open/home/home.js"></script>
</head>
<body>
    <p><?= $seo["info"] ?? "" ?></p>
    <p><?= date("Y-m-d H:i:s"); ?></p>
    <p><?= $_SERVER["REMOTE_ADDR"]; ?></p>
</body>
</html>