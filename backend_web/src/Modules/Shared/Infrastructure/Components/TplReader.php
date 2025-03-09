<?php

namespace App\Modules\Shared\Infrastructure\Components;

use Exception;

final class TplReader
{
    private string $pathViews;
    private string $pathCache;

    public function __construct(array $primitives = [])
    {
        $this->pathViews = realpath(trim((string) ($primitives["pathViews"] ?? "")));
        $this->pathCache = realpath(trim((string) ($primitives["pathCache"] ?? "")));
    }

    public static function fromPrimitives(array $primitives): self
    {
        return new self($primitives);
    }

    public function setViewFolderByController(string $viewFolder): void
    {
        $viewFolder = str_replace("\\", "/", $viewFolder);

        $parts = explode("/", $viewFolder);
        array_shift($parts);
        array_pop($parts); array_pop($parts);

        $viewFolder = implode(DIRECTORY_SEPARATOR, $parts).DIRECTORY_SEPARATOR."Views";
        $thisDir = realpath(__DIR__."/../../../../");

        $pathViewsFolder = $thisDir.DIRECTORY_SEPARATOR.$viewFolder;
        $pathViewsFolder = realpath($pathViewsFolder);
        if (!is_dir($pathViewsFolder))
            $this->throwError("Views folder not found: $pathViewsFolder");

        $this->pathViews = $pathViewsFolder;
    }

    public function getFileContent(string $pathTpl, array $vars): string
    {
        $paths = [
            "$this->pathViews/$pathTpl.tpl.php",
            "$this->pathViews/$pathTpl",
            "$pathTpl.tpl.php",
            "$pathTpl.tpl.php"
        ];

        if (!$pathView = $this->getRealPath($paths))
            $this->throwError("Template not found: $pathTpl");

        ob_start();
        foreach ($vars as $name => $value)
            $$name = $value;

        include $pathView;
        return ob_get_clean();
    }

    private function getRealPath(array $paths): string
    {
        foreach ($paths as $path) {
            if (is_file($path)) {
                return realpath($path);
            }
        }
        return "";
    }

    private function throwError(string $message): void
    {
        throw new Exception($message, 500);
    }
}