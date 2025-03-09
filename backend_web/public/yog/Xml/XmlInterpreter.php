<?php

namespace Yog\Xml;


use Yog\Bootstrap\ConstantEnum;

final class XmlInterpreter
{
    public static function getInstance(): self
    {
        return new self();
    }

    /**
     * @info: prints xml error in case of failure
     */
    public function executeXmlHandlersFunctionsOrOutputError(string $xmlDocument): bool
    {
        $xmlParser = xml_parser_create();

        xml_set_element_handler($xmlParser, "xmlHandlerStartElement", "xmlHandlerEndElement");
        xml_set_character_data_handler($xmlParser, "xmlHandlerCharData");

        $isOk = xml_parse($xmlParser, $xmlDocument);
        if (!$isOk) {
            $errorCode = (string) xml_get_error_code($xmlParser);
            $error = xml_error_string($errorCode) ?? "unknown error";

            xml_parser_free($xmlParser);
            XmlOutput::getInstance()->echoXmlError($errorCode, $error);
            return false;
        }

        xml_parser_free($xmlParser);
        return true;
    }

}