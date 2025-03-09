<?php

namespace App\Modules\Shared\Infrastructure\Components;

use App\Modules\Shared\Domain\Exceptions\ComponentException;

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

        $pathViewsFolder = PATH_ROOT.DIRECTORY_SEPARATOR."src/".$viewFolder;
        $pathViewsFolder = realpath($pathViewsFolder);
        if (!is_dir($pathViewsFolder))
            ComponentException::unexpectedErrorOnRequest("Views folder not found: $pathViewsFolder");

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
            ComponentException::unexpectedErrorOnRequest("Template not found: $pathTpl");

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

    public function invisibleCharsToHtml(string $text): string
    {
        $text = str_replace("\n", "<br>", $text);
        $text = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $text);
        return $text;
    }

}