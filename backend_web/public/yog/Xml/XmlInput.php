<?php

namespace Yog\Xml;

use \DOMDocument;
use Yog\Enums\XmlTagEnum;
use Yog\Enums\XmlAttributeEnum;

final class XmlInput
{
    private string $xmlString;
    private DOMDocument $domDocument;

    public function __construct(
        string $xmlString
    )
    {
        $this->xmlString = $xmlString;
        $this->domDocument = new DOMDocument();
        if ($xmlString) {
            $xmlString = $this->getCleanedXml($xmlString);
            $isLoaded = $this->domDocument->loadXML($xmlString);
            if (!$isLoaded) {
                throw new \Exception("Failed to load XML string.\n $xmlString");
            }
        }
    }

    private function getCleanedXml(string $xmlDoc): string
    {
        $xml = str_replace("<xml>", "", $xmlDoc);
        $xml = str_replace("</xml>", "", $xml);

        $xml = str_replace(" e=\\'0\\'", " e=\"0\"", $xml);
        $xml = str_replace(" e=\\'1\\'", " e=\"1\"", $xml);

        $xml = str_replace(" b=\\'0\\'", " b=\"0\"", $xml);
        $xml = str_replace(" b=\\'1\\'", " b=\"1\"", $xml);

        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$xml;
    }

    public static function getInstance(string $xmlString): self
    {
        return new self($xmlString);
    }

    public function getInnerText(XmlTagEnum $xmlTagEnum): string
    {
        $elements = $this->domDocument->getElementsByTagName($xmlTagEnum->value);
        yogLog($this->domDocument->saveXML(), "elements->length");
        if ($elements->length) return "";

        return $elements->item(0)->nodeValue ?? "";
    }

    public function getAttributeValue(
        XmlTagEnum $connectTag,
        XmlAttributeEnum $attributeName
    ): string
    {
        $elements = $this->domDocument->getElementsByTagName($connectTag->value);
        if ($elements->length) return "";

        $element = $elements->item(0);
        if ($element->hasAttribute($attributeName->value)) {
            return $element->getAttribute($attributeName->value);
        }
        return "";
    }

}