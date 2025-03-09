<?php

namespace Yog\Checkers;

use Yog\Bootstrap\ConstantEnum;
use Yog\Http\HttpRequest;
use Yog\Xml\HtmlOutput;

final class PhpExtensions
{
    public static function getInstance(): self
    {
        return new self();
    }

    public function areExtensionsLoaded(): bool
    {
        $htmlOutput = HtmlOutput::getInstance();

        $tunnelVersion = ConstantEnum::TUNNEL_VERSION_13_21; //13.21
        $tunnelVersionString = ConstantEnum::TUNNEL_VERSION_STRING; //TunnelVersion:
        $phpModuleError = ConstantEnum::PHP_MODULE_ERROR; //PHP_MODULE_NOT_INSTALLED

        if (!extension_loaded("xml")) {
            $htmlOutput->echoHtmlErrorExtensions(
                "XML",
                "this extension",
                $tunnelVersion
            );
            return false;
        }

        if (
            extension_loaded("mysqli") ||
            extension_loaded("mysql")
        ) {
            yogFullLog("Exit aremodulesinstalled");
            return true;
        }

        $htmlOutput->echoHtmlErrorExtensions(
            "php_mysqli or php_mysql",
            "one of these these extensions",
            $tunnelVersion
        );

        $app = HttpRequest::getInstance()->getGET("app");
        if ($app) {
            // from SQLyog - just indicate to SQLyog that SOME modules are not installed
            echo "$tunnelVersionString $phpModuleError";
            yogFullLog("Exit aremodulesinstalled");
        }

        return false;
    }
}