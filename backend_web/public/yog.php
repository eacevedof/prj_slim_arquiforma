<?php
//yog.php

function yogLog(mixed $logVar, string $title = ""): void
{
    $fileContext = fopen("yog.log", "a");
    if (!$fileContext) die("Cannot open log file");

    $content = "";
    if ($title) {
        $content = "[$title] ";
    }
    $content .= is_string($logVar) ? $logVar : var_export($logVar, true);
    $now = date("Y-m-d H:i:s");

    $content = "[$now] $content \r\n";
    fwrite($fileContext, $content);
    fclose($fileContext);
}

try {
    ob_start();

    yogLog([
        "input" => file_get_contents("php://input"),
        "get" => $_GET,
        "post" => $_POST,
        "request" => $_REQUEST,
    ], "request");

    include_once __DIR__ . "/yog/main.php";
    $toEcho = ob_get_clean();
    yogLog($toEcho);

    echo $toEcho;
}
catch (Throwable $e) {
    echo $e->getMessage();
    $e = [
        "file" => $e->getFile(). " [line: ". $e->getLine(). "]",
        "message" => $e->getMessage(),
        "trace" => $e->getTraceAsString(),
    ];
    file_put_contents("./yog.log", var_export($e, 1), FILE_APPEND);
}
