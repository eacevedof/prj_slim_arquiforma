<?php

namespace App\Modules\Shared\Infrastructure\Components\Mailer;

/**
 * $config['protocol'] = 'smtp';
 * $config['smtp_host'] = 'ssl://smtp.domain.es';
 * $config['smtp_port'] = 465;
 * $config['smtp_user'] = 'noreply@domain.es';
 * $config['smtp_pass'] = '1234556789';
 * $config['mailtype'] = 'html';
 * $config['newline'] = "\r\n";
 * $config['charset'] = 'utf-8';
 * $config['wordwrap'] = true;
 * $config['crlf'] = "\r\n";
 */
final class MailerSmtp
{
    private static $func_overload;

    /**
     * STMP Server host
     *
     * @var	string
     */
    public $smtpHost	= "";

    /**
     * SMTP Username
     *
     * @var	string
     */
    public $smtp_user	= "";

    /**
     * SMTP Password
     *
     * @var	string
     */
    public $smtp_pass	= "";

    /**
     * SMTP Server port
     *
     * @var	int
     */
    public $smtp_port	= 25;

    /**
     * SMTP connection timeout in seconds
     *
     * @var	int
     */
    public $smtp_timeout	= 5;

    /**
     * SMTP persistent connection
     *
     * @var	bool
     */
    public $smtp_keepalive	= false;

    /**
     * SMTP Encryption
     *
     * @var	string	empty, 'tls' or 'ssl'
     */
    public $smtpCrypto	= "";

    /**
     * Whether to apply word-wrapping to the message body.
     *
     * @var	bool
     */
    public $wordwrap	= true;

    /**
     * Number of characters to wrap at.
     *
     * @see	CI_Email::$wordwrap
     * @var	int
     */
    public $wrapchars	= 76;

    /**
     * Message format.
     *
     * @var	string	'text' or 'html'
     */
    public $mailtype	= 'html';

    /**
     * Character set (default: utf-8)
     *
     * @var	string
     */
    public $charset		= 'UTF-8';

    /**
     * Alternative message (for HTML messages only)
     *
     * @var	string
     */
    public $alt_message	= "";

    /**
     * Whether to validate e-mail addresses.
     *
     * @var	bool
     */
    public $validate	= false;

    /**
     * X-Priority header value.
     *
     * @var	int	1-5
     */
    public $priority	= 3;			// Default priority (1 - 5)

    /**
     * Newline character sequence.
     * Use "\r\n" to comply with RFC 822.
     *
     * @link	http://www.ietf.org/rfc/rfc822.txt
     * @var	string	"\r\n" or "\n"
     */
    public $newline		= "\n";			// Default newline. "\r\n" or "\n" (Use "\r\n" to comply with RFC 822)

    /**
     * CRLF character sequence
     *
     * RFC 2045 specifies that for 'quoted-printable' encoding,
     * "\r\n" must be used. However, it appears that some servers
     * (even on the receiving end) don't handle it properly and
     * switching to "\n", while improper, is the only solution
     * that seems to work for all environments.
     *
     * @link	http://www.ietf.org/rfc/rfc822.txt
     * @var	string
     */
    public $crlf		= "\n";

    /**
     * Whether to use Delivery Status Notification.
     *
     * @var	bool
     */
    public $dsn		= false;

    /**
     * Whether to send multipart alternatives.
     * Yahoo! doesn't seem to like these.
     *
     * @var	bool
     */
    public $send_multipart	= true;

    /**
     * Whether to send messages to BCC recipients in batches.
     *
     * @var	bool
     */
    public $bcc_batch_mode	= false;

    /**
     * BCC Batch max number size.
     *
     * @see	CI_Email::$bcc_batch_mode
     * @var	int
     */
    public $bcc_batch_size	= 200;

    // --------------------------------------------------------------------

    /**
     * Whether PHP is running in safe mode. Initialized by the class constructor.
     *
     * @var	bool
     */
    private $_safe_mode		= false;

    /**
     * Subject header
     *
     * @var	string
     */
    private $_subject		= "";

    /**
     * Message body
     *
     * @var	string
     */
    private $_body		= "";

    /**
     * Final message body to be sent.
     *
     * @var	string
     */
    private $_finalbody		= "";

    /**
     * Final headers to send
     *
     * @var	string
     */
    private $_header_str		= "";

    /**
     * SMTP Connection socket placeholder
     *
     * @var	resource
     */
    private $smtpSocket	= "";


    /**
     * Whether to perform SMTP authentication
     *
     * @var	bool
     */
    private $_smtp_auth		= false;



    /**
     * Debug messages
     *
     * @see	CI_Email::print_debugger()
     * @var	string
     */
    private $_debug_msg		= array();

    /**
     * Recipients
     *
     * @var	string[]
     */
    private $_recipients		= array();

    /**
     * CC Recipients
     *
     * @var	string[]
     */
    private $_cc_array		= array();

    /**
     * BCC Recipients
     *
     * @var	string[]
     */
    private $_bcc_array		= array();

    /**
     * Message headers
     *
     * @var	string[]
     */
    private $_headers		= array();
    private $_bit_depths;

    public function send($auto_clear = TRUE)
    {
        if ( ! isset($this->_headers['From']))
        {
            $this->_set_error_message('lang:email_no_from');
            return FALSE;
        }

        if ($this->_replyto_flag === FALSE)
        {
            $this->reply_to($this->_headers['From']);
        }

        if ( ! isset($this->_recipients) && ! isset($this->_headers['To'])
            && ! isset($this->_bcc_array) && ! isset($this->_headers['Bcc'])
            && ! isset($this->_headers['Cc']))
        {
            $this->_set_error_message('lang:email_no_recipients');
            return FALSE;
        }

        $this->_build_headers();

        if ($this->bcc_batch_mode && count($this->_bcc_array) > $this->bcc_batch_size)
        {
            $result = $this->batch_bcc_send();

            if ($result && $auto_clear)
            {
                $this->clear();
            }

            return $result;
        }

        if ($this->_build_message() === FALSE)
        {
            return FALSE;
        }

        $result = $this->sendSmtp();

        if ($result && $auto_clear)
        {
            $this->clear();
        }

        return $result;
    }

    public function sendSmtp(): bool
    {
        if (!$this->smtpHost) {
            $this->addToDebug('lang:email_no_hostname');
            return false;
        }

        if (!$this->openSslSocketConnection()) {
            return false;
        }

        if (!$this->tryToAuthenticateIfIsSmtpAuth()) {
            return false;
        }

        if (!$this->sendSocketCommand("from", $this->getCleanedEmail($this->_headers['From']))) {
            $this->smtpResetOrQuit();
            return false;
        }

        foreach ($this->_recipients as $emailTo) {
            if (!$this->sendSocketCommand('to', $emailTo)) {
                $this->smtpResetOrQuit();
                return false;
            }
        }

        foreach ($this->_cc_array as $emailCc) {
            if ($emailCc !== "" && !$this->sendSocketCommand('to', $emailCc)) {
                $this->smtpResetOrQuit();
                return false;
            }
        }

        foreach ($this->_bcc_array as $emailBcc) {
            if ($emailBcc !== "" && ! $this->sendSocketCommand('to', $emailBcc)) {
                $this->smtpResetOrQuit();
                return false;
            }
        }

        if (!$this->sendSocketCommand('data')) {
            $this->smtpResetOrQuit();
            return false;
        }

        // perform dot transformation on any lines that begin with a dot
        $this->writeDataIntoSocket($this->_header_str.preg_replace("/^\./m", "..$1", $this->_finalbody));

        $this->writeDataIntoSocket('.');

        $serverResponse = $this->getSmtpServerResponse();
        $this->addToDebug($serverResponse);

        $this->smtpResetOrQuit();

        if (!str_starts_with($serverResponse, "250")) {
            $this->addToDebug('lang:email_smtp_error', $serverResponse);
            return false;
        }

        return true;
    }

    private function openSslSocketConnection(): bool
    {
        if (is_resource($this->smtpSocket)) return true;

        $ssl = ($this->smtpCrypto === "ssl") ? "ssl://" : "";

        $this->smtpSocket = fsockopen($ssl.$this->smtpHost,
            $this->smtp_port,
            $errno,
            $errstr,
            $this->smtp_timeout
        );

        if (!is_resource($this->smtpSocket)) {
            $this->addToDebug('lang:email_smtp_error', $errno.' '.$errstr);
            return false;
        }

        stream_set_timeout($this->smtpSocket, $this->smtp_timeout);
        $this->addToDebug($this->getSmtpServerResponse());

        if ($this->smtpCrypto === "tls") {
            $this->sendSocketCommand("hello");
            $this->sendSocketCommand("starttls");

            $isCryptoEnabled = stream_socket_enable_crypto(
                $this->smtpSocket,
                true,
                STREAM_CRYPTO_METHOD_TLS_CLIENT
            );
            if (!$isCryptoEnabled) {
                $this->addToDebug('lang:email_smtp_error', $this->getSmtpServerResponse());
                return false;
            }
        }

        return $this->sendSocketCommand("hello");
    }

    private function tryToAuthenticateIfIsSmtpAuth(): bool
    {
        if (!$this->_smtp_auth) {
            return true;
        }

        if (!($this->smtp_user || $this->smtp_pass)) {
            $this->addToDebug('lang:email_no_smtp_unpw');
            return false;
        }

        $this->writeDataIntoSocket('AUTH LOGIN');

        $reply = $this->getSmtpServerResponse();

        if (strpos($reply, '503') === 0)	// Already authenticated
        {
            return true;
        }
        elseif (strpos($reply, '334') !== 0)
        {
            $this->addToDebug('lang:email_failed_smtp_login', $reply);
            return false;
        }

        $this->writeDataIntoSocket(base64_encode($this->smtp_user));

        $reply = $this->getSmtpServerResponse();

        if (strpos($reply, '334') !== 0)
        {
            $this->addToDebug('lang:email_smtp_auth_un', $reply);
            return false;
        }

        $this->writeDataIntoSocket(base64_encode($this->smtp_pass));

        $reply = $this->getSmtpServerResponse();

        if (strpos($reply, '235') !== 0)
        {
            $this->addToDebug('lang:email_smtp_auth_pw', $reply);
            return false;
        }

        if ($this->smtp_keepalive)
        {
            $this->_smtp_auth = false;
        }

        return true;
    }

    private function sendSocketCommand(string $command, string $email = ""): bool
    {
        $expectedCode = 0;
        switch ($command) {
            case 'from' :
                $this->writeDataIntoSocket("MAIL FROM:<$email>");
                $expectedCode = 250;
            break;

            case "hello" :
                if ($this->_smtp_auth || $this->getBitEncodingByCharset() === "8bit") {
                    $this->writeDataIntoSocket('EHLO '.$this->getThisServerName());
                }
                else {
                    $this->writeDataIntoSocket('HELO '.$this->getThisServerName());
                }
                $expectedCode = 250;
            break;

            case 'starttls'	:
                $this->writeDataIntoSocket('STARTTLS');
                $expectedCode = 220;
            break;

            case 'to' :
                //delivery status notification
                if ($this->dsn) {
                    $this->writeDataIntoSocket("RCPT TO:<{$email}> NOTIFY=SUCCESS,DELAY,FAILURE ORCPT=rfc822;{$email}");
                }
                else {
                    $this->writeDataIntoSocket("RCPT TO:<{$email}>");
                }
                $expectedCode = 250;
            break;

            case 'data'	:
                $this->writeDataIntoSocket('DATA');
                $expectedCode = 354;
            break;

            case 'reset':
                $this->writeDataIntoSocket('RSET');
                $expectedCode = 250;
            break;

            case 'quit'	:
                $this->writeDataIntoSocket('QUIT');
                $expectedCode = 221;
            break;
        }

        $reply = $this->getSmtpServerResponse();

        $this->_debug_msg[] = "<pre>{$command}: {$reply}</pre>";

        $serverResponse = (int) self::substr($reply, 0, 3);
        if ($serverResponse !== $expectedCode) {
            $this->addToDebug('lang:email_smtp_error', $reply);
            return false;
        }

        if ($command === 'quit') {
            fclose($this->smtpSocket);
        }
        return true;
    }

    private function getSmtpServerResponse(): string
    {
        $data = "";
        while ($str = fgets($this->smtpSocket, 512)) {
            $data .= $str;
            if ($str[3] === ' ') {
                break;
            }
        }
        return $data;
    }

    private function addToDebug(string $msg, string $val = ""): void
    {
        $this->_debug_msg[] = str_replace('%s', $val, $msg).'<br />';
    }

    private function writeDataIntoSocket(string $data): bool
    {
        $data .= $this->newline;
        for (
            $written = $timestamp = 0, $length = self::strlen($data);
            $written < $length;
            $written += $result
        ) {
            if (($result = fwrite($this->smtpSocket, self::substr($data, $written))) === false) {
                break;
            }

            // See https://bugs.php.net/bug.php?id=39598 and http://php.net/manual/en/function.fwrite.php#96951
            elseif ($result === 0) {
                if ($timestamp === 0) {
                    $timestamp = time();
                }
                elseif ($timestamp < (time() - $this->smtp_timeout)) {
                    $result = false;
                    break;
                }
                usleep(250000);
                continue;
            }

            $timestamp = 0;
        }

        if ($result === false) {
            $this->addToDebug('lang:email_smtp_data_failure', $data);
            return false;
        }
        return true;
    }

    private function getCleanedEmail($email): string|array
    {
        if (!is_array($email)) {
            return preg_match("/\<(.*)\>/", $email, $match) ? $match[1] : $email;
        }

        $clean_email = array();
        foreach ($email as $addy) {
            $clean_email[] = preg_match("/\<(.*)\>/", $addy, $match) ? $match[1] : $addy;
        }
        return $clean_email;
    }

    private function smtpResetOrQuit(): void
    {
        ($this->smtp_keepalive)
            ? $this->sendSocketCommand('reset')
            : $this->sendSocketCommand('quit');
    }

    private static function substr($str, $start, $length = NULL)
    {
        if (self::$func_overload)
        {
            // mb_substr($str, $start, null, '8bit') returns an empty
            // string on PHP 5.3
            isset($length) OR $length = ($start >= 0 ? self::strlen($str) - $start : -$start);
            return mb_substr($str, $start, $length, '8bit');
        }

        return isset($length)
            ? substr($str, $start, $length)
            : substr($str, $start);
    }

    /**
     * Byte-safe strlen()
     *
     * @param	string	$str
     * @return	int
     */
    private static function strlen($str)
    {
        return (self::$func_overload)
            ? mb_strlen($str, '8bit')
            : strlen($str);
    }

    protected $_encoding		= '8bit';
    protected $_base_charsets	= array('us-ascii', 'iso-2022-');

    protected function getBitEncodingByCharset(): string
    {
        in_array($this->_encoding, $this->_bit_depths) OR $this->_encoding = "8bit";
        foreach ($this->_base_charsets as $charset) {
            if (str_starts_with($this->charset, $charset)) {
                $this->_encoding = "7bit";
            }
        }
        return $this->_encoding;
    }

    protected function getThisServerName(): string
    {
        return $_SERVER['SERVER_NAME'] ?? ($_SERVER['SERVER_ADDR'] ?? '[127.0.0.1]');
    }
}