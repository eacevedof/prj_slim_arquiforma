<?php

use Yog\Bootstrap\VariablesEntity;

function dd(...$vars)
{
    foreach ($vars as $var) {
        var_dump($var);
    }
    die(1);
}

function yogFullLog(mixed $logVar, string $title = ""): void
{
    if (!VariablesEntity::getSingleInstance()->isDebug()) return;

    $fileContext = fopen("yog-full.log", "a");
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

