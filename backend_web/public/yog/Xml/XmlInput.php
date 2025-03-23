<?php

namespace Yog\Xml;

use \DOMDocument;

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

}