<?php

namespace Yog\Bootstrap;

final class VariablesEntity
{
    private static ?self $instance = null;

    private ?int $xml_state = null;
    private string $host = "";
    private string $port = "";
    private string $db = "";
    private string $username = "";
    private string $pwd = "";
    private string $charset = "";
    private int $batch = 0;
    private int $isBase64 = 0;
    private string $query = "";
    private string $libxml2_test_query ="";
    private int $libxml2_is_base64 = 0;

    private string $dbExtension = "";
    private int $debug = 0;

    private function __construct() {}

    public static function getSingleInstance(): self
    {
        if (!self::$instance) self::$instance = new self();
        return self::$instance;
    }

    private function __clone() {}
    private function __wakeup() {}


    public function getXmlTagNameId(): ?int
    {
        return $this->xml_state;
    }

    public function setXmlTagNameId(int $xmlTagNameId): void
    {
        $this->xml_state = $xmlTagNameId;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(?string $host): void
    {
        $this->host = $host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function setPort($port): void
    {
        $this->port = $port;
    }

    public function getDb(): string
    {
        return $this->db;
    }

    public function getDbWithOutQuotes(): string
    {
        return str_replace("`", "", $this->db);
    }

    public function setDb(string $db): void
    {
        $this->db = $db;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username): void
    {
        $this->username = $username;
    }

    public function getPwd(): string
    {
        return $this->pwd;
    }

    public function setPwd(string $pwd): void
    {
        $this->pwd = $pwd;
    }

    public function getCharset(): string
    {
        return $this->charset;
    }

    public function setCharset(string $charset): void
    {
        $this->charset = $charset;
    }

    public function isBatch(): int
    {
        return $this->batch;
    }

    public function setIsBatchQuery(int $isBatch): void
    {
        $this->batch = $isBatch;
    }

    public function isBase64(): int
    {
        return $this->isBase64;
    }

    public function setIsBase64(int $base): void
    {
        $this->isBase64 = $base;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function setQuery(string $query): void
    {
        $this->query = $query;
    }

    public function getLibxml2TestQuery(): string
    {
        return $this->libxml2_test_query;
    }

    public function setLibxml2TestQuery(string $libxml2TestQuery): void
    {
        $this->libxml2_test_query = $libxml2TestQuery;
    }

    public function getLibxml2IsBase64(): int
    {
        return $this->libxml2_is_base64;
    }

    public function setLibxml2IsBase64(int $libxml2IsBase64): void
    {
        $this->libxml2_is_base64 = $libxml2IsBase64;
    }

    public function getMysqlExtension(): string
    {
        return $this->dbExtension;
    }

    public function setMysqlExtension(string $dbExtension): void
    {
        $this->dbExtension = $dbExtension;
    }

    public function isDebug(): int
    {
        return $this->debug;
    }

    public function debugOn(int $debug = 1): void
    {
        $this->debug = $debug;
    }
}