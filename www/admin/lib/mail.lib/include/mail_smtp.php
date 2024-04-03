<?

// bool mail ( string to, string subject, string message [, string additional_headers])

function mail_smtp($recipients, $to, $from, $subject, $body, $body_type="TXT", $file=NULL, $file_name=NULL, $host=NULL, $ismime="Y")
{
	include_once("../include/pear/Mail.php");
	include_once("../include/pear/Mail/mime.php");

	$charset = "euc-kr";
	$headers["From"]    = $from;
	$headers["To"]      = $to;
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

		switch($body_type)
		{
			case "TXT":
				$mime->setTXTBody($body);
				break;
			default:		// HTML
				$mime->setHTMLBody($body);
				break;
		}	// switch()


		if($file)
		{
			if($file_name)
			{
				//$mime->addAttachment($file, "text/plain", $file_name, false);
				$mime->addAttachment($file, "text/plain", $file_name);
			}else
			{
				$mime->addAttachment($file, "text/plain");
			}	// if()
		}	// if()

		$body = $mime->get();
		$headers = $mime->headers($headers);

	} else {

		$body;
		$headers;

	}	// if()

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
	if(is_object($return))
	{
		echo "<hr><font color=red><B>Mail send failed</B><br>recipients : " . $recipients . "<br>error message : " . $return->message . "</font>";
		$return = false;
	}	// if()

	return $return;

}	// function()



/* ex)
	include("../include/mail_smtp.php");

	$recipients = "ibin@passkorea.net";		// An array or comma seperated string of recipients. 
	$to = "테스터<ibin@passkorea.net>";
	$from = "\"테스터\"<ibin@passkorea.net>";		// 한글사용시 큰따옴표로 묶어줘야함.  ex) "테스터"<test@test.com>
	$subject = "제목입니다. - " . date("Y-m-d H:i:s") . $from;
	$body = "내용입니다.<b>내용</b>";
	$body_type = "HTML";
	$file = "/home/ibin/public_html/smtp/mail_smtp.php";
	$file_name = NULL;
	$host = "localhost";

	echo "<HR>>>>" . mail_smtp($recipients, $to, $from, $subject, $body, $body_type, $file, $file_name, $host);
*/

?>