<?php

require_once __DIR__ . "/autoload.php";

use Yog\Bootstrap\ConstantEnum;
use Yog\Bootstrap\VariablesEntity;

use Yog\Http\HttpRequest;
use Yog\Checkers\PhpExtensions;
use Yog\Checkers\Php;
use Yog\PDO\PdoMysql;
use Yog\Xml\HtmlOutput;
use Yog\Xml\XmlInterpreter;
use Yog\Xml\XmlOutput;
use Yog\Xml\XmlSql;

$httpRequest = HttpRequest::getInstance();
//$httpRequest->logRequest();

$phpExtensions = PhpExtensions::getInstance();
$php = Php::getInstance();
/* uncomment this line to create a debug log */
$variablesEntity = VariablesEntity::getSingleInstance();
$variablesEntity->debugOn();

set_time_limit(0);
error_reporting(0);
ini_set("display_errors", 0); //siempre tiene q estar en 0 sino rompe el xml por los warnings

if ($variablesEntity->isDebug()) {
    error_reporting(E_ALL);
}

$variablesEntity->setMysqlExtension("-1");
/* Check for the PHP_MYSQL/PHP_MYSQLI extension loaded */
if (extension_loaded('mysqli')) {
    $variablesEntity->setMysqlExtension( "mysqli");
}
elseif (extension_loaded('mysql')) {
    $variablesEntity->setMysqlExtension( "mysql");
}
$variablesEntity->setXmlTagNameId( ConstantEnum::XML_NOSTATE);

function yog_mysql_connect($host, $port, $username, $password, $db_name = "")
{
    $username = utf8_decode($username);
    $ret = 0;
    switch (VariablesEntity::getSingleInstance()->getMysqlExtension()) {
        case "mysql":
            if ($port != 0) {
                //TCP-IP
                $ret = mysql_connect($host.':'.$port, $username, $password);
            } else {
                //UDS. Here 'host' is the socket path
                $ret = mysql_connect($host, $username, $password);
            }

            if (strlen($db_name) != 0) {
                mysql_select_db("$db_name");
            }
            break;
        case "mysqli":
            $port = (int)$port;
            if ($port != 0) {
                //TCP-IP
                $GLOBALS["___mysqli_ston"] = mysqli_connect($host, $username, $password, $db_name, $port);
            } else {
                //UDS. Here 'host' is the socket path
                $GLOBALS["___mysqli_ston"] = mysqli_connect(null, $username, $password, $db_name, 0, $host);
            }
            $ret = $GLOBALS["___mysqli_ston"];
            break;
    }
    return $ret;
}

function yog_mysql_field_type($result, $offset)
{
    //Get the type of the specified field in a result

    $ret = 0;
    switch (VariablesEntity::getSingleInstance()->getMysqlExtension()) {
        case "mysql":
            $ret = mysql_field_type($result, $offset);
            break;
        case "mysqli":
            $tmp = mysqli_fetch_field_direct($result, $offset);
            $ret = GetCorrectDataTypeMySQLI($tmp->type);
            break;
    }
    return $ret;
}
function yog_mysql_field_len($result, $offset)
{
    //Returns the length of the specified field

    $ret = 0;
    switch (VariablesEntity::getSingleInstance()->getMysqlExtension()) {
        case "mysql":
            $ret = mysql_field_len($result, $offset);
            break;
        case "mysqli":
            $tmp = mysqli_fetch_field_direct($result, $offset);
            $ret = $tmp->length;
            break;
    }
    return $ret;
}
function yog_mysql_field_flags($result, $offset)
{
    //Get the flags associated with the specified field in a result

    $ret = 0;
    switch (VariablesEntity::getSingleInstance()->getMysqlExtension()) {
        case "mysql":
            $ret = mysql_field_flags($result, $offset);
            break;
        case "mysqli":
            $___mysqli_obj = (mysqli_fetch_field_direct($result, $offset));
            $___mysqli_tmp = $___mysqli_obj->flags;
            $ret = ($___mysqli_tmp ? (string)(substr((($___mysqli_tmp & MYSQLI_NOT_NULL_FLAG) ? "not_null " : "") . (($___mysqli_tmp & MYSQLI_PRI_KEY_FLAG) ? "primary_key " : "") . (($___mysqli_tmp & MYSQLI_UNIQUE_KEY_FLAG) ? "unique_key " : "") . (($___mysqli_tmp & MYSQLI_MULTIPLE_KEY_FLAG) ? "unique_key " : "") . (($___mysqli_tmp & MYSQLI_BLOB_FLAG) ? "blob " : "") . (($___mysqli_tmp & MYSQLI_UNSIGNED_FLAG) ? "unsigned " : "") . (($___mysqli_tmp & MYSQLI_ZEROFILL_FLAG) ? "zerofill " : "") . (($___mysqli_tmp & 128) ? "binary " : "") . (($___mysqli_tmp & 256) ? "enum " : "") . (($___mysqli_tmp & MYSQLI_AUTO_INCREMENT_FLAG) ? "auto_increment " : "") . (($___mysqli_tmp & MYSQLI_TIMESTAMP_FLAG) ? "timestamp " : "") . (($___mysqli_tmp & MYSQLI_SET_FLAG) ? "set " : ""), 0, -1)) : false);
            break;
    }
    return $ret;
}
function yog_mysql_get_server_info($db_link)
{
    //Get MySQL server info

    $ret = 0;
    switch (VariablesEntity::getSingleInstance()->getMysqlExtension()) {
        case "mysql":
            $ret = mysql_get_server_info($db_link);
            break;
        case "mysqli":
            $ret = mysqli_get_server_info($db_link);
            break;
    }
    return $ret;
}
function yog_mysql_insert_id($db_link)
{
    //Get the ID generated from the previous INSERT operation

    $ret = 0;
    switch (VariablesEntity::getSingleInstance()->getMysqlExtension()) {
        case "mysql":
            $ret = mysql_insert_id($db_link);
            break;
        case "mysqli":
            $ret = mysqli_insert_id($db_link);
            break;
    }
    return $ret;
}

function yog_mysql_query($query, $db_link): array
{
    $ret = [];
    switch (VariablesEntity::getSingleInstance()->getMysqlExtension()) {
        case "mysql":
            $result = mysql_query($query, $db_link);
            yogLog($result, "yog_mysql_query executed [$query]");
            if (yog_mysql_errno($db_link) != 0) {
                $temp_ar = array("result" => -1, "ar" => mysql_affected_rows($db_link));
                array_push($ret, $temp_ar);
            } elseif ($result === false) {

                $temp_ar = array("result" => 1, "ar" => mysql_affected_rows($db_link));
                array_push($ret, $temp_ar);
            } else {
                $temp_ar = array("result" => $result, "ar" => mysql_affected_rows($db_link));
                array_push($ret, $temp_ar);
            }

            /**********************/
            break;
        case "mysqli":
            $ret = get_array_from_query($query, $db_link);
            break;
    }
    return $ret;
}
function get_array_from_query($query, $db_link)
{
    $ret = array();
    $bool = mysqli_real_query($db_link, $query) or yog_mysql_error($db_link);

    if (yog_mysql_errno($db_link) != 0) {

        $temp_ar = array("result" => -1, "ar" => 0);
        array_push($ret, $temp_ar);

    } elseif ($bool) {
        do {
            /* store first result set */
            $result = mysqli_store_result($db_link);
            $num_ar = mysqli_affected_rows($db_link);

            if ($result === false && yog_mysql_errno($db_link) != 0) {
                $temp_ar = array("result" => -1, "ar" => $num_ar);
                array_push($ret, $temp_ar);
                break;
            } elseif ($result === false) {
                $temp_ar = array("result" => 1, "ar" => $num_ar);
                array_push($ret, $temp_ar);
            } else {
                $temp_ar = array("result" => $result, "ar" => $num_ar);
                array_push($ret, $temp_ar);
            }
        } while (mysqli_more_results($db_link) and mysqli_next_result($db_link));

        if (yog_mysql_errno($db_link) != 0) {
            $temp_ar = array("result" => -1, "ar" => $num_ar);
            array_push($ret, $temp_ar);
        }
    }
    return $ret;
}
function yog_mysql_errno($db_link)
{
    //Returns the numerical value of the error message from previous MySQL operation

    $ret = 0;
    switch (VariablesEntity::getSingleInstance()->getMysqlExtension()) {
        case "mysql":
            $ret = mysql_errno($db_link);
            break;
        case "mysqli":
            $ret = mysqli_errno($db_link);
            break;
    }
    return $ret;
}
function yog_mysql_error($db_link)
{
    //Returns the text of the error message from previous MySQL operation

    $ret = 0;
    switch (VariablesEntity::getSingleInstance()->getMysqlExtension()) {
        case "mysql":
            $ret = mysql_error($db_link);
            break;
        case "mysqli":
            $ret = mysqli_error($db_link);
            break;
    }
    return $ret;
}
function yog_mysql_num_rows($result)
{
    //Get number of rows in result
    $ret = 0;
    switch (VariablesEntity::getSingleInstance()->getMysqlExtension()) {
        case "mysql":
            $ret = mysql_num_rows($result);
            break;
        case "mysqli":
            $ret = mysqli_num_rows($result);
            break;
    }
    return $ret;
}
function yog_mysql_num_fields($result)
{
    //Get number of fields in result
    $ret = 0;
    switch (VariablesEntity::getSingleInstance()->getMysqlExtension()) {
        case "mysql":
            $ret = mysql_num_fields($result);
            break;
        case "mysqli":
            $ret = mysqli_num_fields($result);
            break;
    }
    return $ret;
}
function yog_mysql_fetch_field($result)
{
    //Get column information from a result and return as an object
    $ret = 0;
    switch (VariablesEntity::getSingleInstance()->getMysqlExtension()) {
        case "mysql":
            $ret = mysql_fetch_field($result);
            break;
        case "mysqli":
            $ret = mysqli_fetch_field($result);
            break;
    }
    return $ret;
}
function yog_mysql_fetch_array($result)
{
    //Fetch a result row as an associative array, a numeric array, or both
    $ret = 0;
    switch (VariablesEntity::getSingleInstance()->getMysqlExtension()) {
        case "mysql":
            $ret = mysql_fetch_array($result);
            break;
        case "mysqli":
            $ret = mysqli_fetch_array($result);
            break;
    }
    return $ret;
}
function yog_mysql_fetch_lengths($result)
{
    //Get the length of each output in a result
    $ret = array();
    switch (VariablesEntity::getSingleInstance()->getMysqlExtension()) {
        case "mysql":
            $ret = mysql_fetch_lengths($result);
            break;
        case "mysqli":
            $ret = mysqli_fetch_lengths($result);
            break;
    }
    return $ret;
}

function yog_mysql_free_result($result): void
{
    switch (VariablesEntity::getSingleInstance()->getMysqlExtension()) {
        case "mysql":
            $ret = mysql_free_result($result);
            break;
        case "mysqli":
            $ret = mysqli_free_result($result);
            break;
    }
}

function yog_mysql_select_db($db_name, $db_link)
{
    //Select a MySQL database
    $ret = 0;
    switch (VariablesEntity::getSingleInstance()->getMysqlExtension()) {
        case "mysql":
            $ret = mysql_select_db($db_name, $db_link);
            break;
        case "mysqli":
            $ret = mysqli_select_db($db_link, $db_name);
            break;
    }
    return $ret;
}
function yog_mysql_close($db_link)
{
    //Close MySQL connection
    $ret = 0;
    switch (VariablesEntity::getSingleInstance()->getMysqlExtension()) {
        case "mysql":
            $ret = mysql_close($db_link);
            break;
        case "mysqli":
            $ret = mysqli_close($db_link);
            break;
    }
    return $ret;
}

function GetCorrectDataTypeMySQLI($type)
{
    switch($type) {
        case MYSQLI_TYPE_TINY:
            $data = "tinyint";
            break;
        case MYSQLI_TYPE_SHORT:
            $data = "shortint";
            break;
        case MYSQLI_TYPE_LONG:
            $data = "int";
            break;
        case MYSQLI_TYPE_FLOAT:
            $data = "float";
            break;
        case MYSQLI_TYPE_DOUBLE:
            $data = "double";
            break;
        case MYSQLI_TYPE_NULL:
            $data = "default null";
            break;
        case MYSQLI_TYPE_TIMESTAMP:
            $data = "timestamp" ;
            break;
        case MYSQLI_TYPE_BIT:
            $data = "bit" ;
            break;
        case MYSQLI_TYPE_LONGLONG:
            $data = "bigint";
            break;
        case MYSQLI_TYPE_INT24:
            $data = "mediumint";
            break;
        case MYSQLI_TYPE_DATE:
            $data = "date";
            break;
        case MYSQLI_TYPE_TIME:
            $data = "time";
            break;
        case MYSQLI_TYPE_DATETIME:
            $data = "datetime";
            break;
        case MYSQLI_TYPE_YEAR:
            $data = "year";
            break;
        case MYSQLI_TYPE_NEWDATE:
            $data = "date";
            break;
        case MYSQLI_TYPE_ENUM:
            $data = "enum";
            break;
        case MYSQLI_TYPE_SET:
            $data = "set";
            break;
        case MYSQLI_TYPE_TINY_BLOB:
            $data = "tinyblob";
            break;
        case MYSQLI_TYPE_MEDIUM_BLOB:
            $data = "mediumblob";
            break;
        case MYSQLI_TYPE_LONG_BLOB:
            $data = "longblob";
            break;
        case MYSQLI_TYPE_BLOB:
            $data = "blob";
            break;
        case MYSQLI_TYPE_VAR_STRING:
            $data = "varchar";
            break;
        case MYSQLI_TYPE_STRING:
            $data = "char";
            break;
        case MYSQLI_TYPE_GEOMETRY:
            $data = "geometry";
            break;
        case MYSQLI_TYPE_NEWDECIMAL:
            $data = "newdecimal";
            break;
        case MYSQLI_TYPE_JSON:
            $data = "json";
            break;

    }
    return ($data);
}
/* function finds and returns the correct type understood by MySQL C API() */

function GetCorrectDataType($result, $j)
{
    $data   = null;

    yogFullLog("Enter GetCorrectDataType");

    switch(yog_mysql_field_type($result, $j)) {
        case "int":
            if (yog_mysql_field_len($result, $j) <= 4) {
                $data = "smallint";
            } elseif (yog_mysql_field_len($result, $j) <= 9) {
                $data = "mediumint";
            } else {
                $data = "int";
            }
            break;

        case "real":
            if (yog_mysql_field_len($result, $j) <= 10) {
                $data = "float";
            } else {
                $data = "double";
            }
            break;

        case "string":
            $data = "varchar";
            break;

        case "blob":
            $textblob = "TEXT";
            if (strpos(yog_mysql_field_flags($result, $j), "binary")) {
                $textblob = "BLOB";
            }
            if (yog_mysql_field_len($result, $j) <= 255) {
                if ($textblob == "TEXT") {
                    $data = "tinytext";
                } else {
                    $data = "tinyblob";
                }
            } elseif (yog_mysql_field_len($result, $j) <= 65535) {
                if ($textblob == "TEXT") {
                    $data = "mediumtext";
                } else {
                    $data = "mediumblob";
                }
            } else {
                if ($textblob == "TEXT") {
                    $data = "longtext";
                } else {
                    $data = "longblob";
                }
            }
            break;

        case "date":
            $data = "date";
            break;

        case "time":
            $data = "time";
            break;

        case "datetime":
            $data = "datetime";
            break;
    }

    yogFullLog("Exit GetCorrectDataType");

    return (convertxmlchars($data));
}

/* Output extra info used by SQLyog internally */
function HandleExtraInfo($mysql, $value)
{

    yogFullLog("Enter HandleExtraInfo");

    echo "<s_v>" . yog_mysql_get_server_info($mysql) . "</s_v>";
    echo "<m_i></m_i>";
    echo "<a_r>" . $value['ar'] . "</a_r>";
    echo "<i_i>" . yog_mysql_insert_id($mysql) . "</i_i>";

    yogFullLog("Exit HandleExtraInfo");

}

/* Process when only a single query is called. */
function ExecuteSingleQuery($mysql, string $query): void
{
    yogLog("query-to-be-run: [$query]", "ExecuteSingleQuery");
    $result = yog_mysql_query($query, $mysql);

    $xmlOutput = XmlOutput::getInstance();
    foreach ($result as  $value) {
        if ($value['result'] === -1) {
            $xmlOutput->echoXmlError(
                (string) yog_mysql_errno($mysql),
                (string) yog_mysql_error($mysql)
            );
            return;
        }
        /* free the result */
        CreateXMLFromResult($mysql, $value);

        if ($value['result'] !== 1) {
            yog_mysql_free_result($value['result']);
        }
    }
}

function CreateXMLFromResult($mysql, $value)
{
    // $value['result'], $value['ar']
    /* query execute was successful so we need to echo the correct xml */
    /* the query may or may not return any result */
    yogFullLog("yog_mysql_num_rows in ExecuteSingleQuery");

    // check if the query is not a result returning query
    $isNotResultQuery = 0;
    if (VariablesEntity::getSingleInstance()->getMysqlExtension() === "mysqli") {
        ($value['result'] === 1) ? $isNotResultQuery = 1 : $isNotResultQuery = 0;
    } else {
        ($value['result'] == 1) ? $isNotResultQuery = 1 : $isNotResultQuery = 0;
    }

    $numrows = 0;
    $numfields = 0;

    if (!is_int($value['result'])) {
        $numrows = yog_mysql_num_rows($value['result']);
        $numfields = yog_mysql_num_fields($value['result']);
    }

    if ($isNotResultQuery  || (!$numrows && !$numfields)) {//
        /* is a non-result query */
        echo "<result v=\"" . ConstantEnum::TUNNEL_VERSION_13_21 . "\">";
        echo "<e_i></e_i>";
        HandleExtraInfo($mysql, $value);
        echo "<f_i c=\"0\"></f_i><r_i></r_i></result>";
        return;
    }

    /* handle result query like SELECT,SHOW,EXPLAIN or DESCRIBE */
    echo '<result v="' . ConstantEnum::TUNNEL_VERSION_13_21 . '">';
    echo "<e_i></e_i>";

    /* add some extra info */
    HandleExtraInfo($mysql, $value);

    /* add the field count information */
    $fieldcount = yog_mysql_num_fields($value['result']);
    print($fieldcount);
    echo "<f_i c=\"$fieldcount\">";

    /* retrieve information about each fields */
    $i = 0;
    while ($i < $fieldcount) {
        $meta = yog_mysql_fetch_field($value['result']);

        echo "<f>";
        echo "<n>" . convertxmlchars($meta->name) . "</n>";
        echo "<t>" . convertxmlchars($meta->table) . "</t>";
        echo "<m>" . convertxmlchars($meta->max_length) . "</m>";
        echo "<d></d>";
        switch (VariablesEntity::getSingleInstance()->getMysqlExtension()) {
            case "mysql":
                echo "<ty>" . GetCorrectDataType($value['result'], $i) . "</ty>";
                break;
            case "mysqli":
                echo "<ty>" . yog_mysql_field_type($value['result'], $i) . "</ty>";
                break;
        }

        echo "</f>";

        $i++;
    }

    /* end field informations */
    echo "</f_i>";

    /* get information about number of rows in the resultset */

    echo "<r_i c=\"$numrows\">";
    /* add up each row information */
    while ($row = yog_mysql_fetch_array($value['result'])) {
        $lengths = yog_mysql_fetch_lengths($value['result']);

        /* start of a row */
        echo "<r>";

        for ($i = 0; $i < $fieldcount; $i++) {
            /* start of a col */
            echo "<c l=\"$lengths[$i]\">";
            if (!isset($row[$i]) /*== NULL*/) {
                echo "(NULL)";
            } else {
                if ($lengths[$i] == 0) {
                    echo "_";
                } else {
                    echo convertxmlchars(base64_encode($row[$i]));
                }
            }

            /* end of a col */
            echo "</c>";
        }

        /* end of a row */
        echo "</r>";
    }

    /* close the xml output */
    echo "</r_i></result>";
}

/* implementation of my_strtok() in PHP */
/*
 * Description: string my_strtok(string $string, string $delimiter).
 *
 * Function my_strtok() splits a string ($string) into smaller
 * strings (tokens), with each token being delimited by the delimiter
 * string ($delimiter), considering string variables and comments
 * in the $string argument. Note that the comparision is case-insensitive.
 *
 * Returns FALSE if there are no tokens left.
 * Does not return empty tokens.
 * Does not return the "delimiter" command as a token.
 *
 * Usage:
 * The first call to my_strtok() uses the $string and $delimiter arguments.
 * Every subsequent call to my_strtok() needs no arguments at all, or only
 * the $delimiter argument to use, as it keeps track of where it is in the
 * current string. To start over, or to tokenize a new string you simply
 * call my_strtok() with the both arguments again to initialize it.
 * The delimiter can be changed by the command "delimiter new_delimiter" in
 * the $string argument (the command is case-insensitive).
 *
 * Example:
 *  $res = my_strtok($query, $delimiter);
 *  while ($res) {
 *      echo "token = $res<br>";
 *      $res = my_strtok();
 *  }
 *
 * Author: Andrey Adaikin, IVA Team, <IVATeam@gmail.com>
 * @version $Revision: 1.3 $, $Date: 2005/09/28 $
 */
function my_strtok($string = null, $delimiter = null)
{
    static $str;            // lower case $string (equals to strtolower($string))
    static $str_original;   // stores $string argument
    static $len;            // length of the $string
    static $curr_pos;       // current position in the $string
    static $match_pos;      // position where the $delimiter is a substring of the $string
    static $delim;          // lower case $delimiter (equals to strtolower($delimiter))

    yogFullLog("Enter my_strtok");

    if (null === $delimiter) {
        if (null !== $string) {
            $delim = strtolower($string);
            $match_pos = -1;
        }
    } else {
        if (!is_string($string) || !is_string($delimiter)) {
            return false;
        }
        $str_original = $string;
        $str = strtolower($str_original);
        $len = strlen($str);
        $curr_pos = 0;
        $match_pos = -1;
        $delim = strtolower($delimiter);
    }

    if ($curr_pos >= $len) {
        return false;
    }

    if ("" == $delim) {
        $delim = ";";
        $match_pos = -1;
    }

    $dlen = strlen($delim);
    $result = false;

    for ($i = $curr_pos; $i < $len; ++$i) {
        if ($match_pos < $i) {
            $match_pos = strpos($str, $delim, $i);
            if (false === $match_pos) {
                $match_pos = $len;
            }
        }

        if ($i == $match_pos) {
            if ($i != $curr_pos) {
                $result = trim(substr($str_original, $curr_pos, $i - $curr_pos));
                if (strncasecmp($result, 'delimiter', 9) == 0 && (strlen($result) == 9 || false !== strpos(" \t", $result[9]))) {
                    $delim = trim(strtolower(substr($result, 10)));
                    if ("" == $delim) {
                        $delim = ";";
                    }
                    $match_pos = -1;
                    $result = false;
                }
            }
            $i += $dlen;
            if ($match_pos < 0) {
                $dlen = strlen($delim);
            }
            $curr_pos = $i--;
            if ("" === $result) {
                $result = false;
            }
            if (false !== $result) {
                break;
            }
        } elseif ($str[$i] == "'") {
            for ($j = $i + 1; $j < $len; ++$j) {
                if ($str[$j] == "\\") {
                    ++$j;
                } elseif ($str[$j] == "'") {
                    break;
                }
            }
            $i = $j;
        } elseif ($str[$i] == "\"") {
            for ($j = $i + 1; $j < $len; ++$j) {
                if ($str[$j] == "\\") {
                    ++$j;
                } elseif ($str[$j] == "\"") {
                    break;
                }
            }
            $i = $j;
        } elseif ($i < $len - 1 && $str[$i] == "/" && $str[$i + 1] == "*") {
            $j = $i + 2;
            while ($j) {
                $j = strpos($str, "*/", $j);
                if (!$j || $str[$j - 1] != "\\") {
                    break;
                }
                ++$j;
            }
            if (!$j) {
                break;
            }
            $i = $j + 1;
        } elseif ($str[$i] == "#") {
            $j = strpos($str, "\n", $i + 1) or strpos($str, "\r", $i + 1);
            if (!$j) {
                break;
            }
            $i = $j;
        } elseif ($i < $len - 2 && $str[$i] == "-" && $str[$i + 1] == "-" && false !== strpos(" \t", $str[$i + 2])) {
            $j = strpos($str, "\n", $i + 3) or strpos($str, "\r", $i + 1);
            if (!$j) {
                break;
            }
            $i = $j;
        } elseif ($str[$i] == "\\") {
            ++$i;
        }
    }

    if (false === $result && $curr_pos < $len) {
        $result = trim(substr($str_original, $curr_pos));
        if (strncasecmp($result, 'delimiter', 9) == 0 && (strlen($result) == 9 || false !== strpos(" \t", $result[9]))) {
            $delim = trim(strtolower(substr($result, 10)));
            if ("" == $delim) {
                $delim = ";";
            }
            $match_pos = -1;
            $dlen = strlen($delim);
            $result = false;
        }
        $curr_pos = $len;
        if ("" === $result) {
            $result = false;
        }
    }
    return $result;
}

/* Processes a set of queries. The queries are delimited with ;. Will return result for the last query only. */
/* If it encounters any error in between will return error values for that query */
function ExecuteBatchQuery($mysql, $query)
{
    $token = my_strtok($query, ";");
    $xmlOutput = XmlOutput::getInstance();

    while ($token) {
        $prev = $token;
        $token = my_strtok();
        if (!$token) {
            ExecuteSingleQuery($mysql, $prev);
            return;
        }

        $result = yog_mysql_query($prev, $mysql);

        foreach ($result as $key => $value) {
            //$value['result'], $value['ar']
            if ($value['result'] === -1) {

                $xmlOutput->echoXmlError(
                    (string) yog_mysql_errno($mysql),
                    (string) yog_mysql_error($mysql)
                );
                return;
            }

            /* free the result */
            if (!is_int($value['result'])) {
                yog_mysql_free_result($value['result']);
            }

        }
    }
}

/* Function sets the MySQL server to non-strict mode as SQLyog is designed to work in non-strict mode */
function SetNonStrictMode($mysql)
{

    yogFullLog("Enter SetNonStrictMode");

    /* like SQLyog app we dont check the MySQL version. We just execute the statement and ignore the error if any */
    $query = "set sql_mode=''";
    $result = yog_mysql_query($query, $mysql);

    yogFullLog("Exit SetNonStrictMode");

    return;
}

/* Starting from SQLyog v5.1, we dont take the charset info from the server, instead SQLyog send the info
   in the posted XML */
function SetName($cnxMysql)
{
    $variablesEntity = VariablesEntity::getSingleInstance();
    if (!$variablesEntity->getCharset()) {
        return;
    }

    $query = "SET NAMES " . $variablesEntity->getCharset();
    if ($variablesEntity->getCharset() !== "[default]") {
        yog_mysql_query($query, $cnxMysql);
    }
}

/* Start element handler for the parser */
function xmlHandlerStartElement($parser, $xmlTagName, $attrs)
{
    yogLog($xmlTagName, "xmlHandlerStartElement");
    //Done for bug in PHP 5.2.6 and libXML 2.7,2.
    // Special HTML characters were being dropped. So, now we provide to send always as base 64 encoded data.
    $variablesEntity = VariablesEntity::getSingleInstance();
    $variablesEntity->setIsBase64(0);
    if (isset($attrs["E"])) {
        $variablesEntity->setIsBase64(($attrs["E"] === "1" ? 1 : 0));
    }

    switch (strtolower($xmlTagName)) {
        case "host":
            $variablesEntity->setXmlTagNameId( ConstantEnum::XML_HOST);
            break;
        case "db":
            $variablesEntity->setXmlTagNameId( ConstantEnum::XML_DB);
            break;
        case "charset":
            $variablesEntity->setXmlTagNameId( ConstantEnum::XML_CHARSET);
            break;
        case "user":
            $variablesEntity->setXmlTagNameId( ConstantEnum::XML_USER);
            break;
        case "password":
            $variablesEntity->setXmlTagNameId( ConstantEnum::XML_PWD);
            break;
        case "port":
            $variablesEntity->setXmlTagNameId( ConstantEnum::XML_PORT);
            //This is an assumption that, if port is sent as base64 encoded
            //the "Always Use Base64 Encoding For Data Stream is checked."
            if ($variablesEntity->isBase64()) {
                $variablesEntity->setLibxml2IsBase64( 1);
            }
            break;
        case "query":
            $variablesEntity->setXmlTagNameId( ConstantEnum::XML_QUERY);
            /* track whether the query(s) has to be processed in batch mode */
            $variablesEntity->setIsBatchQuery($attrs["B"] === "1" ? 1 : 0);
            break;

        case "libxml2_test_query":
            $variablesEntity->setXmlTagNameId( ConstantEnum::XML_LIBXML2_TEST_QUERY);
            break;
    }
}

/* End element handler for the XML parser */
function xmlHandlerEndElement($parser, $name)
{
    yogFullLog("Enter endElement");

    $variablesEntity = VariablesEntity::getSingleInstance();
    $variablesEntity->setXmlTagNameId(ConstantEnum::XML_NOSTATE);

    yogFullLog("Exit  endElement");
}

/* Character data handler for the parser */
function xmlHandlerCharData($parser, $tagInnerText): void
{
    yogLog($tagInnerText, "xmlHandlerCharData tagInnerText");
    //Done for bug in PHP 5.2.6 and libXML 2.7,2.
    // Special HTML characters were being dropped. So, now we provide to send always as base 64 encoded data.
    $variablesEntity = VariablesEntity::getSingleInstance();

    $xmlTagId = $variablesEntity->getXmlTagNameId();
    yogLog($tagInnerText, "charHandler.base64 prev xml-state: ($xmlTagId)");

    if ($variablesEntity->isBase64()) {
        $tagInnerText = base64_decode($tagInnerText);
        yogLog($tagInnerText, "charHandler.base64 xml-state: ($xmlTagId)");
    }

    if ($xmlTagId === ConstantEnum::XML_HOST) {
        $variablesEntity->setHost($variablesEntity->getHost().$tagInnerText);
        return;
    }

    if ($xmlTagId === ConstantEnum::XML_DB) {
        $variablesEntity->setDb($variablesEntity->getDb(). $tagInnerText);
        return;
    }

    if ($xmlTagId === ConstantEnum::XML_CHARSET) {
        $variablesEntity->setCharset($variablesEntity->getCharset().$tagInnerText);
        return;
    }

    if ($xmlTagId === ConstantEnum::XML_USER) {
        $variablesEntity->setUsername($variablesEntity->getUsername().$tagInnerText);
        return;
    }

    if ($xmlTagId === ConstantEnum::XML_PWD) {
        $variablesEntity->setPwd($variablesEntity->getPwd().$tagInnerText);
        return;
    }

    if ($xmlTagId === ConstantEnum::XML_PORT) {
        $variablesEntity->setPort($variablesEntity->getPort().$tagInnerText);
        return;
    }

    if ($xmlTagId === ConstantEnum::XML_QUERY) {
        $variablesEntity->setQuery(
            $variablesEntity->getQuery().$tagInnerText
        );
        return;
    }

    if ($xmlTagId === ConstantEnum::XML_LIBXML2_TEST_QUERY) {
        $variablesEntity->setLibxml2TestQuery(
            $variablesEntity->getLibxml2TestQuery().$tagInnerText
        );
    }
}

/* Convert special characters such as <,>,&, /,\, to equivalent xmlor html characters*/
function convertxmlchars($string, $called_by = "")
{
    yogFullLog("Enter convertxmlchars, called by".$called_by);
    yogFullLog("Input: " . $string);

    $result = $string;

    $result = str_replace("&", "&amp;", $result);
    $result = str_replace("<", "&lt;", $result);
    $result = str_replace(">", "&gt;", $result);
    $result = str_replace("'", "&apos;", $result);
    $result = str_replace("\"", "&quot;", $result);

    yogFullLog("Output: " . $result);
    yogFullLog("Exit convertxmlchars");

    return $result;
}

/**
 * checks if the XML stream is base 64 encoded
 *
 * @return bool If stream is encoded, return true. Else false;
 *
 */
function LibXml2IsBase64Encoded(): bool
{
    $variablesEntity = VariablesEntity::getSingleInstance();
    return (bool) $variablesEntity->getLibxml2IsBase64();
}

/** Detect if the user's PHP/LibXML is affected by the following bug: -
 *  http://bugs.php.net/bug.php?id=45996
 */
function LibXml2IsBuggy(): bool
{
    $variablesEntity = VariablesEntity::getSingleInstance();
    $variablesEntity->setLibxml2TestQuery("");

    yogLog($testSqlInXml = XmlSql::getInstance()->getXmlTestSql(), "testSqlInXml to be run");
    XmlInterpreter::getInstance()->executeXmlHandlersFunctionsOrOutputError($testSqlInXml);

    if (strcasecmp($variablesEntity->getLibxml2TestQuery(), "select a") === 0) {
        //This PHP/LibXML is buggy!
        return true;
    }
    //Not buggy!
    return false;
}

/* Process the  query*/
function ProcessQuery()
{
    $php = Php::getInstance();
    $httpRequest = HttpRequest::getInstance();
    $htmlOutput = HtmlOutput::getInstance();

    if (!$php->isPhpVersionOver4_3()) {
        if ($httpRequest->isGarbageTestFromApp()) {
            $htmlOutput->echoErrorAppPhpVersion();
            return;
        }

        $htmlOutput->echoHtmlErrorAccess();
        return;
    }

    /* in special case, sqlyog just sends garbage data with query string to check for tunnel version. we need to process that now */
    if ($httpRequest->isGarbageTestFromApp()) {
        $htmlOutput->echoErrorAppTunnelVersion();
        return;
    }

    /* Starting from 5.1 BETA 4, we dont get the data as URL encoded POST data, we just get it as raw data */
    $xmlDbAndQuery = $httpRequest->getPhpInput();
    yogLog($xmlDbAndQuery, "xmlDbAndQuery");
    if (!$xmlDbAndQuery) {
        yogLog("empty query showing yog version xml");
        $htmlOutput->echoHtmlErrorAccess();
        return;
    }

    $isValidXml = XmlInterpreter::getInstance()->executeXmlHandlersFunctionsOrOutputError(
        $xmlDbAndQuery
    );
    if (!$isValidXml) {
        yogLog($xmlDbAndQuery,"Invalid XML 1110 just aborting");
        return;
    }

    $xmlOutput = XmlOutput::getInstance();
    $xmlOutput->echoXmlOpen();

    $variablesEntity = VariablesEntity::getSingleInstance();
    //If the stream is not base-64 encoded and the PHP has the libxml2 bug display extra error.
    if (LibXml2IsBase64Encoded() == false && LibXml2IsBuggy() == true) {
        $errorLibXml =
            'Your PHP/libxml version is affected by a bug. ' .
            'Please check "Always Use Base64 Encoding For Data Stream" in "Advanced" section of HTTP tab.'
        ;
        $xmlOutput->echoXmlError("4", $errorLibXml);
        $xmlOutput->echoXmlClose();
        return;
    }

    if ($variablesEntity->getMysqlExtension() === "mysqli") {
        mysqli_report(MYSQLI_REPORT_OFF);
    }

    $cnxMysql = yog_mysql_connect(
        $variablesEntity->getHost(), $variablesEntity->getPort(),
        $variablesEntity->getUsername(), $variablesEntity->getPwd()
    );

    $pdoHelper = PdoMysql::getInstance();
    $pdoRaw = $pdoHelper->getPdoConnection(
        $variablesEntity->getHost(), $variablesEntity->getPort(),
        $variablesEntity->getUsername(), $variablesEntity->getPwd()
    );

    if (!$cnxMysql) {
        $xmlOutput->echoXmlError(
            $pdoHelper->getErrorCode(),
            $pdoHelper->getError()
        );
        yogFullLog($pdoHelper->getError(), "linea: 1135");
        $xmlOutput->echoXmlClose();
        return;
    }

    /* Function will execute setnames in the server as it does in SQLyog client */
    SetName($cnxMysql);
    $pdoHelper->setNames($pdoRaw, $variablesEntity->getCharset());

    if ($variablesEntity->getDb()) {
        yog_mysql_select_db(str_replace("`", "", $variablesEntity->getDb()), $cnxMysql);
    }

    /* set sql_mode to zero */
    SetNonStrictMode($cnxMysql);

    if ($variablesEntity->isBatch()) {
        ExecuteBatchQuery($cnxMysql, $variablesEntity->getQuery());
    } else {
        ExecuteSingleQuery($cnxMysql, $variablesEntity->getQuery());
    }

    yog_mysql_close($cnxMysql);
    $xmlOutput->echoXmlClose();
}

if (!defined("MYSQLI_TYPE_BIT")) {
    define("MYSQLI_TYPE_BIT", 16);
}

/* check whether global variables are registered or not */
if (!get_cfg_var("register_globals")) {
    extract($httpRequest->getRequest());
}

/* we check if all the external libraries support i.e. expat and mysql in our case is built in or not */
if ($phpExtensions->areExtensionsLoaded()) {
    ProcessQuery();
}



