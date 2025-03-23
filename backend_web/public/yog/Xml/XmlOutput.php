<?php

namespace Yog\Xml;

use PDO;
use Yog\Bootstrap\ConstantEnum;
use Yog\PDO\PdoMysql;

final class XmlOutput
{
    public static function getInstance(): self
    {
        return new self();
    }

    public function echoXmlOpen(): void
    {
        echo "<xml>";
    }

    public function echoXmlClose(): void
    {
        echo "</xml>";
    }

    public function echoXmlError(string $errorCode, string $error): void
    {
        echo $this->getXmlError($errorCode, $error);
    }

    public function getEscapedCharsForXml(string $string): string
    {
        $result = str_replace("&", "&amp;", $string);
        $result = str_replace("<", "&lt;", $result);
        $result = str_replace(">", "&gt;", $result);
        $result = str_replace("'", "&apos;", $result);
        $result = str_replace("\"", "&quot;", $result);
        return $result;
    }

    private function getXmlError(string $errorCode, string $error): string
    {
        $tunnelVersion = ConstantEnum::TUNNEL_VERSION_13_21;
        $escapedError = $this->getEscapedCharsForXml($error);
        $output = [
            "<result v=\"$tunnelVersion\">",
            "<e_i><e_n>$errorCode</e_n><e_d>$escapedError</e_d></e_i>",
            "</result>"
        ];
        return $this->getAsString($output);
    }

    public function getXmlNoResult(PdoMysql $pdoMysql): string
    {
        $tunnelVersion = ConstantEnum::TUNNEL_VERSION_13_21;
        $output = [
            "<result v=\"$tunnelVersion\">",
            "<e_i></e_i>",
            $this->getHandleExtraInfo($pdoMysql),
            "<<f_i c=\"0\"></f_i><r_i></r_i></result>"
        ];
        return $this->getAsString($output);
    }

    public function getHandleExtraInfo(PdoMysql $pdoMysql): string
    {
        $lastInsertId = $pdoMysql->getLastInsertId() ?? "";
        $output = [
            "<s_v>{$pdoMysql->getServerVersion()}</s_v>",
            "<m_i></m_i>",
            "<a_r>{$pdoMysql->getRowCount()}</a_r>",
            "<i_i>$lastInsertId</i_i>"
        ];
        return $this->getAsString($output);
    }

    private function getAsString(array $output): string
    {
        return implode("", $output);
    }

    public function CreateXMLFromResult(PdoMysql $pdoMysql): string
    {
        $nunRows = 0;
        $numFields = 0;

        if ($statement = $pdoMysql->getStatement()) {
            $nunRows = $statement->rowCount();
            $numFields = $statement->columnCount();
        }

        $isResultQuery = $pdoMysql->isResultQuery();
        if (!$isResultQuery || (!$nunRows && !$numFields)) {
            return $this->getXmlNoResult($pdoMysql);
        }

        $xml = [
            $this->getXmlDescribe($pdoMysql)
        ];


        $i = 0;
        echo "<f_i c=\"$numFields\">";
        while ($i < $numFields){
            $meta = (object) $statement->getColumnMeta($i);
            echo $this->getXmlField($meta);
        }
        echo "</f_i>";

        echo "<r_i c=\"$nunRows\">";
        do {

            $row = $statement->fetchAll(PDO::FETCH_ASSOC);
            $lengths = array_map("strlen", $row);

        }
        while ($this->$statement->nextRowset());
        echo "</r_i></result>";
        return "";
    }

    private function getXmlDescribe(PdoMysql $pdoMysql): string
    {
        $tunnelVersion = ConstantEnum::TUNNEL_VERSION_13_21;
        $numFields = count($pdoMysql->getFields());
        $output = [
            "<result v=\"$tunnelVersion\">",
            "<e_i></e_i>",
            $this->getHandleExtraInfo($pdoMysql),
            $numFields,
        ];
        return $this->getAsString($output);
    }

    private function getXmlField(object $field): string
    {
        $name = htmlentities($field->name);
        $table = htmlentities($field->table);
        $length = htmlentities($field->max_length);
        $type = htmlentities($field->native_type);
        $output = [
            "<f>",
            "<n>{$name}</n>",
            "<t>{$table}</t>",
            "<m>{$length}</m>",
            "<d></d>",
            "<ty>{$type}</ty>",
            "</f>",
        ];
        return $this->getAsString($output);
    }
}