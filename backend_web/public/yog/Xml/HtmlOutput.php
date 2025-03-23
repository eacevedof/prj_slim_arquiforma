<?php

namespace Yog\Xml;

use Yog\Bootstrap\ConstantEnum;
use Yog\Checkers\Php;

final class HtmlOutput
{
    public static function getInstance(): self
    {
        return new self();
    }

    public function echoHtmlForEmptyRequest(
        string $extraMessage = ""
    ): void
    {
        $tunnelVersion = ConstantEnum::TUNNEL_VERSION_13_21;
        $html = "
<html>
<head>
<title>SQLyog HTTP Tunneling</title>
</head>
<body leftmargin=\"0\" topmargin=\"0\">
    <p><img src=\"http://www.webyog.com/images/header.jpg\" alt=\"Webyog\"></p>
    <table align=\"center\" width=\"60%\" cellpadding=\"3\" border=\"0\">
        <tr>
            <td>
                <div align='justify'>
                    <p><b>Tunnel version: $tunnelVersion</b>.
                    <p>
                        This PHP page exposes the MySQL API as a set of webservices.<br><br>
                        This page allows SQLyog to manage a MySQL server even if the MySQL port is blocked or remote access to MySQL is not allowed.<br><br>
                        Visit <a href =\"http://www.webyog.com\">Webyog</a> to get more details about SQLyog.
                    </p>
                </div>
            </td>
        </tr>
    </table>
";
        if (!Php::getInstance()->isPhpVersionOver4_3()) {
            $html .= "<table width=\"100%\" cellpadding=\"3\" border=\"0\">
            <tr>
            <td><font face=\"Verdana\" size=\"2\"><p><b>Error: </b>SQLyog HTTP Tunnel feature requires PHP version > 4.3.0</td>
            </tr>
            </table>";
        }

        if ($extraMessage) {
            $html .= "<table width=\"100%\" cellpadding=\"3\" border=\"0\">
            <tr>
            <td><font face=\"Verdana\" size=\"2\"><p><b>Error!: </b>$extraMessage</td>
            </tr>
            </table>";
        }

        $html .= "</body></html>";
        echo $html;
    }

    public function echoErrorAppPhpVersion(): void
    {
        //TunnelVersion: PHP_VERSION_ERROR
        echo ConstantEnum::TUNNEL_VERSION_STRING;
        echo ConstantEnum::PHP_VERSION_ERROR;
    }

    /**
     * @info debe enviarse así con dos echos sino falla
     */
    public function echoErrorAppTunnelVersion(): void
    {
        echo ConstantEnum::TUNNEL_VERSION_STRING;
        echo ConstantEnum::TUNNEL_VERSION_13_21;
    }

    public function echoHtmlErrorExtensions(
        string $moduleNotFound,
        string $thisExtension,
        string $tunnelVersion
    ): void
    {
        echo "
<html>
<head>
<title>SQLyog HTTP Tunneling</title>
</head>
<body leftmargin=\"0\" topmargin=\"0\">
<div align=center>
    <img src=\"http://www.webyog.com/images/header.jpg\" alt=\"Webyog\">
    <table align=\"center\" width=\"60%\" cellpadding=\"3\" border=\"0\">
        <tr>
            <td>
                <p>
                    <font face=\"Verdana\" size=\"2\">
                        <font color=\"#FF0000\"><b>Error:</b></font>Extension 
                        <b>{$moduleNotFound}</b> was not found compiled and loaded in the PHP interpreter. 
                        SQLyog requires {$thisExtension} to work properly.
                    </font>
                    <b>Tunnel version: $tunnelVersion</b>.
                </p>
                <p>
                    This PHP page exposes the MySQL API as a set of webservices.<br><br>
                    This page allows SQLyog to manage a MySQL server even if the MySQL port is blocked or remote access to MySQL is not allowed.<br><br>
                    Visit <a href =\"http://www.webyog.com\">Webyog</a> to get more details about SQLyog.
                </p>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
";
    }

}