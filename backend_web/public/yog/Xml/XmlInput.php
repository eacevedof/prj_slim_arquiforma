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
        $this->domDocument = new DOMDocument("1.0", "utf-8");
        $this->xmlString = trim($xmlString);
        if (!$xmlString) return;

        $xmlString = $this->getCleanedXml($xmlString);
        $isLoaded = $this->domDocument->loadXML($xmlString);

        if (!$isLoaded) {
            throw new \Exception("Failed to load XML string.\n $xmlString");
        }
    }

    private function getCleanedXml(string $xmlString): string
    {
        $xmlString = str_replace(" e='0'", " e=\"0\"", $xmlString);
        $xmlString = str_replace(" e='1'", " e=\"1\"", $xmlString);

        $xmlString = str_replace(" b='0'", " b=\"0\"", $xmlString);
        $xmlString = str_replace(" b='1'", " b=\"1\"", $xmlString);

        $xmlString = str_replace("<xml>", "<root>", $xmlString);
        $xmlString = str_replace("</xml>", "</root>", $xmlString);

        $xmlString = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>".$xmlString;

        return trim($xmlString);
    }

    public static function getInstance(string $xmlString): self
    {
        return new self($xmlString);
    }

    public function getInnerText(XmlTagEnum $xmlTagEnum): string
    {
        $elements = $this->domDocument->getElementsByTagName($xmlTagEnum->value);
        if (!$elements->length) return "";

        $element = $elements->item(0);
        return $element->nodeValue ?? "";
    }

    public function getAttributeValue(
        XmlTagEnum $connectTag,
        XmlAttributeEnum $attributeName
    ): string
    {
        $elements = $this->domDocument->getElementsByTagName($connectTag->value);
        if (!$elements->length) return "";

        $element = $elements->item(0);
        if ($element->hasAttribute($attributeName->value)) {
            return $element->getAttribute($attributeName->value);
        }
        return "";
    }

    public function isQueryBatch(): int
    {
        $attributeValue = $this->getAttributeValue(
            XmlTagEnum::QUERY,
            XmlAttributeEnum::BATCH
        );
        if (!$attributeValue) return 0;

        return (int) ($attributeValue === "1");
    }

    public function isValueInBase64(XmlTagEnum $tagEnum): int
    {
        $attributeValue = $this->getAttributeValue(
            $tagEnum,
            XmlAttributeEnum::BASE64
        );
        if (!$attributeValue) return false;

        return (int) ($attributeValue === "1");
    }

}