<?php

namespace Yog\Xml;

use Yog\Bootstrap\ConstantEnum;
use Yog\Enums\XmlAttributeEnum;
use Yog\Enums\XmlTagEnum;

final class XmlResponse
{
    private array $output = [];
    public static function getInstance(): self
    {
        return new self();
    }

    public function getTagValue(XmlTagEnum $tag, string $value): string
    {
        return $this->getTaggedValue($tag->value, $value);
    }

    public function getTagWithAttribute(
        XmlTagEnum $tag, string $value,
        XmlAttributeEnum $attribute, string $attributeValue
    ): string
    {
        return $this->getTaggedWithAttribute(
            $tag->value, $value,
            $attribute->value, $attributeValue
        );
    }

    private function getTaggedValue(string $tag, string $innerText): string
    {
        //$innerText = htmlentities($innerText, ENT_QUOTES, "UTF-8");
        $innerText = base64_encode($innerText);
        return "<$tag>$innerText</$tag>";
    }

    private function getTaggedWithAttribute(string $tag, string $innerText, string $attribute, string $attrValue): string
    {
        //$innerText = htmlentities($innerText, ENT_QUOTES, "UTF-8");
        $innerText = base64_encode($innerText);
        $attrValue = htmlentities($attrValue, ENT_QUOTES, "UTF-8");

        return "<$tag $attribute=\"$attrValue\">$innerText</$tag>";
    }

    public function addOutput(string $tagged): self
    {
        $this->output[] = $tagged;
        return $this;
    }

    public function reset(): self
    {
        $this->output = [];
        return $this;
    }

    public function getOutput(): string
    {
        return implode("", $this->output);
    }

    public function echoOutput(): void
    {
        echo $this->getOutput();
    }
}