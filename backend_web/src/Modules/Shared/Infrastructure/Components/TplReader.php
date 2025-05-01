<?php

namespace App\Modules\Shared\Infrastructure\Components;

use App\Modules\Shared\Domain\Exceptions\ComponentException;
use App\Modules\Shared\Infrastructure\Traits\LogTrait;

final class TplReader
{
    use LogTrait;

    private string $pathShared;
    private string $pathViews;
    private string $pathCache;

    public function __construct(array $primitives = [])
    {
        $this->pathShared = realpath(trim((string) ($primitives["pathShared"] ?? PATH_ROOT."/src/Modules/Shared/Infrastructure/Views")));
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
            ComponentException::unexpectedErrorOnRequest("Views folder not found: src/$viewFolder");

        $this->pathViews = $pathViewsFolder;
    }

    public function getFileContent(string $pathTpl, array $vars): string
    {
        $DS = DIRECTORY_SEPARATOR;
        $paths = [
            "$this->pathViews{$DS}$pathTpl.tpl.php",
            "$this->pathViews{$DS}$pathTpl",
            "$pathTpl.tpl.php",

            "$this->pathShared{$DS}$pathTpl.tpl.php",
            "$this->pathShared{$DS}$pathTpl",
        ];
        //$this->logDebug($paths, "Paths to search for template");

        if (!$pathView = $this->getRealPath($paths))
            ComponentException::unexpectedErrorOnRequest("Template not found:".implode(", ", $paths));

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