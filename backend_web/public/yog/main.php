<?php

require_once __DIR__ . "/autoload.php";

use Yog\Bootstrap\ConstantEnum;
use Yog\Bootstrap\VariablesEntity;

use Yog\Enums\XmlTagEnum;
use Yog\Http\HttpRequest;
use Yog\Checkers\PhpExtensions;
use Yog\Checkers\Php;
use Yog\PDO\PdoMysql;
use Yog\Xml\HtmlOutput;
use Yog\Xml\XmlInterpreter;
use Yog\Xml\XmlOutput;
use Yog\Xml\XmlSql;
use Yog\Checkers\Mysql;
use Yog\Xml\XmlInput;
use Yog\Xml\XmlResponse;

$httpRequest = HttpRequest::getInstance();
$htmlOutput = HtmlOutput::getInstance();
$variablesEntity = VariablesEntity::getSingleInstance();
$variablesEntity->debugOn();

if (
    !($requestInput = $httpRequest->getPhpInput()) &&
    !$httpRequest->isGarbageTestFromApp()
) {
    $htmlOutput->echoHtmlForEmptyRequest();
    return;
}

if ($httpRequest->isGarbageTestFromApp()) {
    $htmlOutput->echoErrorAppTunnelVersion();
    return;
}

$xmlInput = XmlInput::getInstance($requestInput ?? "");
$phpExtensions = PhpExtensions::getInstance();
$chkMysql = Mysql::getInstance();

set_time_limit(0);
error_reporting(0);
ini_set("display_errors", 0); //siempre tiene q estar en 0 sino rompe el xml por los warnings

if ($variablesEntity->isDebug()) {
    error_reporting(E_ALL);
}

$variablesEntity->setXmlTagNameId( ConstantEnum::XML_NOSTATE);

$variablesEntity->setHost($xmlInput->getInnerText(XmlTagEnum::HOST));
$variablesEntity->setDb($xmlInput->getInnerText(XmlTagEnum::DB));
$variablesEntity->setCharset($xmlInput->getInnerText(XmlTagEnum::CHARSET));
$variablesEntity->setUsername($xmlInput->getInnerText(XmlTagEnum::USER));
$variablesEntity->setPwd($xmlInput->getInnerText(XmlTagEnum::PASSWORD));
$variablesEntity->setPort($xmlInput->getInnerText(XmlTagEnum::PORT));
//$variablesEntity->setQuery($xmlInput->getInnerText(XmlTagEnum::QUERY));


/* check whether global variables are registered or not */
if (!get_cfg_var("register_globals")) {
    extract($httpRequest->getRequest());
}

function yog_mysql_connect($host, $port, $username, $password, $db_name = "")
{
    $username = mb_convert_encoding($username, 'ISO-8859-1', 'UTF-8');
    $port = (int)$port;
    if ($port != 0) {
        //TCP-IP
        $GLOBALS["___mysqli_ston"] = mysqli_connect($host, $username, $password, $db_name, $port);
    } else {
        //UDS. Here 'host' is the socket path
        $GLOBALS["___mysqli_ston"] = mysqli_connect(null, $username, $password, $db_name, 0, $host);
    }
    $ret = $GLOBALS["___mysqli_ston"];
    return $ret;
}

function yog_mysql_field_type($result, $fieldPosition): string
{
    $tmp = mysqli_fetch_field_direct($result, $fieldPosition);
    $literalType = Mysql::getInstance()->getLiteralMysqlTypeByMysqlTypeId($tmp->type);
    return $literalType;
}

function yog_mysql_query($query, $db_link): array
{
    $ret = [];
    $bool = mysqli_real_query($db_link, $query) or mysqli_error($db_link);

    if (mysqli_errno($db_link) != 0) {
        $temp_ar = array(
            "result" => -1,
            "ar" => 0
        );
        array_push($ret, $temp_ar);
    }
    elseif ($bool) {
        do {
            /* store first result set */
            $result = mysqli_store_result($db_link);
            $num_ar = mysqli_affected_rows($db_link);

            if ($result === false && mysqli_errno($db_link) != 0) {
                $temp_ar = array(
                    "result" => -1,
                    "ar" => $num_ar
                );
                array_push($ret, $temp_ar);
                break;
            }
            elseif ($result === false) {
                $temp_ar = array(
                    "result" => 1,
                    "ar" => $num_ar
                );
                array_push($ret, $temp_ar);
            }
            else {
                $temp_ar = array(
                    "result" => $result,
                    "ar" => $num_ar
                );
                array_push($ret, $temp_ar);
            }
        }
        while (
            mysqli_more_results($db_link) and mysqli_next_result($db_link)
        );

        if (mysqli_errno($db_link) != 0) {
            $temp_ar = array(
                "result" => -1,
                "ar" => $num_ar
            );
            array_push($ret, $temp_ar);
        }
    }

    return $ret;
}

/* Output extra info used by SQLyog internally */
function HandleExtraInfo($mysql, $value)
{
    echo "<s_v>" . mysqli_get_server_info($mysql) . "</s_v>";
    echo "<m_i></m_i>";
    echo "<a_r>" . $value['ar'] . "</a_r>";
    echo "<i_i>" .  mysqli_insert_id($mysql) . "</i_i>";
}

/* Process when only a single query is called. */
function ExecuteSingleQuery($mysql, string $query): void
{
    yogLog("query-to-be-run: [\n\n\t$query\n\n]", "ExecuteSingleQuery");

    $result = yog_mysql_query($query, $mysql);

    $xmlOutput = XmlOutput::getInstance();
    foreach ($result as  $value) {
        if ($value['result'] === -1) {
            $xmlOutput->echoXmlError(
                (string) mysqli_errno($mysql),
                mysqli_error($mysql)
            );
            return;
        }

        CreateXMLFromResult($mysql, $value);

        if ($value['result'] !== 1) {
            mysqli_free_result($value['result']);
        }
    }
}

function CreateXMLFromResult($mysql, $qResult): void
{
    $xmlOutput = XmlOutput::getInstance();

    $isNotResultQuery = ($qResult['result'] === 1) ? 1 : 0;
    $numrows = 0;
    $numfields = 0;

    if (!is_int($qResult['result'])) {
        $numrows = mysqli_num_rows($qResult['result']);
        $numfields = mysqli_num_fields($qResult['result']);
    }

    if ($isNotResultQuery  || (!$numrows && !$numfields)) {
        /* is a non-result query */
        echo "<result v=\"" . ConstantEnum::TUNNEL_VERSION_13_21 . "\">";
        echo "<e_i></e_i>";
        HandleExtraInfo($mysql, $qResult);
        echo "<f_i c=\"0\"></f_i><r_i></r_i></result>";
        return;
    }

    /* handle result query like SELECT,SHOW,EXPLAIN or DESCRIBE */
    echo '<result v="' . ConstantEnum::TUNNEL_VERSION_13_21 . '">';
    echo "<e_i></e_i>";

    /* add some extra info */
    HandleExtraInfo($mysql, $qResult);

    /* add the field count information */
    $fieldcount = mysqli_num_fields($qResult['result']);
    print($fieldcount);
    echo "<f_i c=\"$fieldcount\">";

    /* retrieve information about each fields */
    $i = 0;
    while ($i < $fieldcount) {
        $meta = mysqli_fetch_field($qResult['result']);

        echo "<f>";
        echo "<n>" . $xmlOutput->getEscapedCharsForXml($meta->name) . "</n>";
        echo "<t>" . $xmlOutput->getEscapedCharsForXml($meta->table) . "</t>";
        echo "<m>" . $xmlOutput->getEscapedCharsForXml($meta->max_length) . "</m>";
        echo "<d></d>";
        echo "<ty>" . yog_mysql_field_type($qResult['result'], $i) . "</ty>";
        echo "</f>";

        $i++;
    }

    /* end field informations */
    echo "</f_i>";

    /* get information about number of rows in the resultset */

    echo "<r_i c=\"$numrows\">";
    /* add up each row information */
    while ($row = mysqli_fetch_array($qResult['result'])) {
        $lengths = mysqli_fetch_lengths($qResult['result']);

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
                    echo $xmlOutput->getEscapedCharsForXml(
                        base64_encode($row[$i])
                    );
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

        foreach ($result as  $value) {
            if ($value['result'] === -1) {
                $xmlOutput->echoXmlError(
                    (string) mysqli_errno($mysql),
                    mysqli_error($mysql)
                );
                return;
            }

            /* free the result */
            if (!is_int($value['result'])) {
                mysqli_free_result($value['result']);
            }
        }
    }
}

function SetNonStrictMode($mysql)
{

    yogFullLog("Enter SetNonStrictMode");

    /* like SQLyog app we dont check the MySQL version. We just execute the statement and ignore the error if any */
    $query = "set sql_mode=''";
    $result = yog_mysql_query($query, $mysql);

    yogFullLog("Exit SetNonStrictMode");

    return;
}

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

function xmlHandlerStartElement($parser, $xmlTagName, $attrs)
{
    yogLog($xmlTagName, "xmlHandlerStartElement");
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

function xmlHandlerEndElement($parser, $name)
{
    $variablesEntity = VariablesEntity::getSingleInstance();
    $variablesEntity->setXmlTagNameId(ConstantEnum::XML_NOSTATE);
}

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

        $htmlOutput->echoHtmlForEmptyRequest();
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
        $htmlOutput->echoHtmlForEmptyRequest();
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
    /*
    if (!$variablesEntity->getLibxml2IsBase64() && LibXml2IsBuggy()) {
        $errorLibXml =
            'Your PHP/libxml version is affected by a bug. ' .
            'Please check "Always Use Base64 Encoding For Data Stream" in "Advanced" section of HTTP tab.'
        ;
        $xmlOutput->echoXmlError("4", $errorLibXml);
        $xmlOutput->echoXmlClose();
        return;
    }
    */
    mysqli_report(MYSQLI_REPORT_OFF);

    $cnxMysql = yog_mysql_connect(
        $variablesEntity->getHost(), $variablesEntity->getPort(),
        $variablesEntity->getUsername(), $variablesEntity->getPwd()
    );

    $pdoHelper = PdoMysql::getInstance();
    $pdoHelper->loadPdoByDbInfo(
        $variablesEntity->getHost(), $variablesEntity->getPort(),
        $variablesEntity->getUsername(), $variablesEntity->getPwd()
    );
    $variablesEntity->setPdoMysql($pdoHelper);

    if (!$cnxMysql) {
        $xmlOutput->echoXmlError(
            $pdoHelper->getErrorCode(),
            $pdoHelper->getError()
        );
        yogFullLog($pdoHelper->getError(), "linea: 668");
        $xmlOutput->echoXmlClose();
        return;
    }

    /* Function will execute setnames in the server as it does in SQLyog client */
    SetName($cnxMysql);
    $pdoHelper->setNames($variablesEntity->getCharset());

    if ($variablesEntity->getDb()) {
        mysqli_select_db($cnxMysql, $variablesEntity->getDbWithOutQuotes());
    }

    /* set sql_mode to zero */
    SetNonStrictMode($cnxMysql);

    if ($variablesEntity->isBatch()) {
        ExecuteBatchQuery($cnxMysql, $variablesEntity->getQuery());
    }
    else {
        ExecuteSingleQuery($cnxMysql, $variablesEntity->getQuery());
    }

    mysqli_close($cnxMysql);
    $xmlOutput->echoXmlClose();
}

if ($phpExtensions->areExtensionsLoaded())
    ProcessQuery();




