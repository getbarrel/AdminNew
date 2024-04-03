<?

	include("../include/mail_smtp.php");


	$recipients = "johan@forbiz.co.kr";		// 받는사람(An array or comma seperated string of recipients.)
	//$recipients = "test_to@passkorea.net, test_to2@passkorea.net, test_to3@passkorea.net";		// An array or comma seperated string of recipients.
	$to = "테스터<johan@forbiz.co.kr>";
	$from = "테스터<mytesoro@mytesoro.com>";
	$subject = "제목입니다. - " . date("Y-m-d H:i:s") . $from;
	$body = "내용입니다.<b>내용</b>";
	$body_type = "HTML";		// HTML, TXT
	$file = NULL;		// 첨부파일 경로및 파일명
	$file_name = NULL;		// 첨부파일명
	$host = "localhost";		// SMTP Address

	if (mail_smtp($recipients, $to, $from, $subject, $body, $body_type, $file, $file_name, $host))
	{
		echo "메일발송 성공";
	} else
	{
		echo "메일발송 실패";
	}	// if()

?>