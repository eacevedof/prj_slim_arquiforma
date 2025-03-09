<?php
/**
 * @var \App\Modules\Shared\Infrastructure\Components\TplReader $this
 */
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

    <div id="div-logo">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="-15 50 400 100">
            <text x="10" y="100" font-family="serif" font-size="50" font-weight="bold" fill="red" stroke="black" stroke-width="2">
                ARQUIFORMA
            </text>
            <text x="120" y="130" font-family="sans-serif" font-size="10" fill="black">CONSTRUCCIÓN Y OBRAS</text>
        </svg>
    </div>

    <p><?= $this->invisibleCharsToHtml($seo["info"] ?? "") ?></p>
    <br/>
    <p><?= date("Y-m-d H:i:s"); ?></p>
    <br/>
    <p id="remote-addr">
        <?= $_SERVER["REMOTE_ADDR"]; ?>
    </p>
</body>
</html>