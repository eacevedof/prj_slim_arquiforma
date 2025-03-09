<?php

namespace Yog\Xml;

final class XmlSql
{
    public static function getInstance(): self
    {
        return new self();
    }

    public function getXmlTestSql(): string
    {
        return "<xml><libxml2_test_query>select &apos;a&apos;</libxml2_test_query></xml>";
    }

    public function getXmlFromResult(): string
    {
        return "<xml><libxml2_test_query>select &apos;a&apos;</libxml2_test_query></xml>";
    }
}