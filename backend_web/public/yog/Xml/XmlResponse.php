<?php

namespace Yog\Xml;

use Yog\Bootstrap\ConstantEnum;
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

    private function getTaggedValue(string $tag, string $value): string
    {
        $value = htmlentities($value, ENT_QUOTES, "UTF-8");
        return "<$tag>$value</$tag>";
    }

    private function addOutput(string $tagged): self
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