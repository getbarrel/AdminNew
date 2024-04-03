<?php

/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-04-02
 * Time: 오전 10:43
 */
class email
{
    /**
     * 메일 보내기
     * @param $recipients
     * @param $to
     * @param $from
     * @param $subject
     * @param $body
     * @param null $file
     * @return bool
     */
    public function send($recipients, $to, $from, $subject, $body, $file = null)
    {
        //gmail 을 별도로
        $subject = iconv('UTF-8', 'EUC-KR//TRANSLIT', $subject);
        $body = iconv('UTF-8', 'EUC-KR//TRANSLIT', $body);

        if (substr_count($to, "@gmail")) {
            $headers = "From: " . $from . ">\r\n" .
                "MIME-Version: 1.0" . "\r\n" .
                "Content-type: text/html; " . "\r\n";
            return mail($to, $subject, $body, $headers);
        } else {
            //mail_smtp libery 이용하여 처리
            return $this->mail_smtp($recipients, $to, $from, $subject, $body, 'HTML', $file);
        }
    }

    /**
     * msil_smtp 함수
     * @param $recipients
     * @param $to
     * @param $from
     * @param $subject
     * @param $body
     * @param string $body_type
     * @param null $file
     * @param null $file_name
     * @param null $host
     * @param string $ismime
     * @return bool
     */
    private function mail_smtp($recipients, $to, $from, $subject, $body, $body_type = "TXT", $file = NULL, $file_name = NULL, $host = NULL, $ismime = "Y")
    {
        global $install_path;
        $install_path = $_SERVER["DOCUMENT_ROOT"] . "/include";

        include_once($install_path . "/pear/Mail.php");
        include_once($install_path . "/pear/Mail/mime.php");

        //$charset = "utf-8";
        $charset = "euc-kr";
        $headers["From"] = $from;
        $headers["To"] = $to;
        $headers["Subject"] = $subject;

        if ($ismime == "Y") {

            $crlf = "\r\n";
            $mime = new Mail_mime($crlf);
            $mime->_build_params["html_charset"] = $charset;
            $mime->_build_params["text_charset"] = $charset;

            // [2003-07-29] 보낸이나 본문이 아웃룩 익스프레스나 일부 웹메일에서 깨지는 문제을 해결하기 위해 인코딩 및 케릭터셋 변경.
            $mime->_build_params["head_charset"] = $charset;
            $mime->_build_params["text_encoding"] = "8bit";
            $mime->_build_params["html_encoding"] = "base64";

            switch ($body_type) {
                case "TXT":
                    $mime->setTXTBody($body);
                    break;
                default:        // HTML
                    $mime->setHTMLBody($body);
                    break;
            }


            if ($file) {
                if ($file_name) {
                    //$mime->addAttachment($file, "text/plain", $file_name, false);
                    $mime->addAttachment($file, "text/plain", $file_name);
                } else {
                    $mime->addAttachment($file, "text/plain");
                }
            }

            $body = $mime->get();
            $headers = $mime->headers($headers);

        } else {

            $body;
            $headers;

        }

        $params["host"] = ($host) ? $host : "localhost";

        // Create the mail object using the Mail::factory method
        $mail_object =& Mail::factory("smtp", $params);

        $return = $mail_object->send($recipients, $headers, $body);

        /* PEAR_Error
        while( list($_key_, $_val_) = each($return) )
        {
            echo $_key_ . " - " . $_val_ . "<br>";
        }
        */

        // error
        if (is_object($return)) {
            echo "<hr><font color=red><B>Mail send failed</B><br>recipients : " . $recipients . "<br>error message : " . $return->message . "</font>";
            $return = false;
        }

        return $return;

    }
}