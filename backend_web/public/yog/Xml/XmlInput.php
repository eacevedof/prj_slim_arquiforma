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
        if ($xmlString)
            $this->domDocument->loadXML($xmlString);
    }

    public static function getInstance(string $xmlString): self
    {
        return new self($xmlString);
    }

    public function getInnerText(XmlTagEnum $xmlTagEnum): string
    {
        $elements = $this->domDocument->getElementsByTagName($xmlTagEnum->value);
        if ($elements->length) return "";

        return $elements->item(0)->nodeValue;
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