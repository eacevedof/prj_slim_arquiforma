<?php

namespace Yog\PDO;

use \PDO;
use \PDOException;
use \PDOStatement;
use \Throwable;

final class PdoMysql
{
    private string $errorCode = "";
    private string $error = "";

    private string $lastQuery = "";
    private bool|PDOStatement $statement;
    private array $result = [];
    private int $rowCount = 0;
    private int $affectedRows = 0;
    private ?int $lastInsertId = null;
    private string $serverVersion = "";
    private bool $isResultQuery = false;
    public ?PDO $pdo = null;

    public static function getInstance(): self
    {
        return new self();
    }

    public function loadPdoByDbInfo(
        string $host,
        string $port,
        string $username,
        string $password,
        string $dbName = ""
    ): void
    {
        $this->pdo = null;
        try {
            $dsn = "mysql:host=$host;port=$port;";
            if ($dbName) $dsn .= "dbname=$dbName;";

            $username = mb_convert_encoding($username, "ISO-8859-1", "UTF-8");
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("SET sql_mode=''");
            $this->serverVersion = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
            $this->pdo = $pdo;
        }
        catch (PDOException $e) {
            $this->setErrorByException($e);
        }
    }

    public function setNames(?string $charset = ""): void
    {
        if (!$charset) return;
        $this->pdo->exec("SET NAMES '$charset'");
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function query(string $query): array
    {
        $this->resetQueryData();

        $this->lastQuery = $query;
        try {
            $this->statement = $this->pdo->query($this->lastQuery);
            if (!$this->statement) {
                $this->setError("-1", "Error in query: $this->lastQuery");
                return [];
            }

            if (stripos($query, "INSERT") === 0) {
                $this->isResultQuery = false;
                $this->lastInsertId = (int) $this->pdo->lastInsertId();
                return [
                    "lastInsertId" => $this->lastInsertId
                ];
            }
            elseif (stripos($query, "UPDATE") === 0 || stripos($query, "DELETE") === 0) {
                $this->isResultQuery = false;
                $this->affectedRows = $this->statement->rowCount();
                return [
                    "affectedRows" => $this->affectedRows
                ];
            }

            $this->isResultQuery = true;
            if (!$this->rowCount = $this->statement->rowCount())
                return [];

            do {

                //$row = $this->statement->fetchAll(PDO::FETCH_ASSOC);
                //if ($row) $this->result[] = $row;

            }
            while ($this->statement->nextRowset());

        } catch (PDOException $e) {
            $this->setErrorByException($e);
        }
        return $this->result;
    }

    public function executeQuery(string $query): array
    {
        try {
            $statement = $this->pdo->query($query);
            if (!$statement)
                return ["result" => -1, "ar" => 0];

            return [
                "result" => $statement,
                "ar" => $statement->rowCount(),
                "i_i" => $this->pdo->lastInsertId()
            ];

        }
        catch (PDOException $e) {
            $result = [
                "result" => -1,
                "ar" => 0,
                "error" => $e->getMessage()
            ];
        }
        return $result;
    }

    private function resetQueryData(): void
    {
        $this->result = [];
        $this->rowCount = 0;
        $this->affectedRows = 0;
        $this->lastInsertId = null;
        $this->lastQuery = "";
        $this->isResultQuery = false;
    }

    private function setErrorByException(Throwable $ex): void
    {
        $this->errorCode = (string) $ex->getCode();
        $this->error = $ex->getMessage();
    }

    private function setError(string $errorCode, string $error): void
    {
        $this->errorCode = $errorCode;
        $this->error = $error;
    }

    public function getStatement(): bool|PDOStatement
    {
        return $this->statement;
    }

    public function getFields(): array
    {
        $fields = [];
        for ($i = 0; $i < $this->statement->columnCount(); $i++) {
            $fields[] = $this->statement->getColumnMeta($i);
        }
        return $fields;
    }

    public function getServerVersion(): string
    {
        return $this->serverVersion;
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    public function getLastInsertId(): ?int
    {
        return $this->lastInsertId;
    }

    public function isResultQuery(): bool
    {
        return $this->isResultQuery;
    }

}