<?php
require_once './class/GoogleAuthenticator.php';

$ga = new PHPGangsta_GoogleAuthenticator();

$secret = $ga->createSecret(); // 시크릿키 생성

//$secret = 'OQB6ZZGYHCPSX4AK'; //테스트를 위한 고정 시크릿키
$qrCodeUrl = $ga->getQRCodeGoogleUrl('12345', $secret);
$oneCode = $ga->getCode($secret);
?>
2단계 OTP인증 모듈 작업을 위한 테스트 입니다.<br>
Google OTP 앱을 받으신 다음 아래 QR코드를 스캔하세요.<br>
만약, QR코드가 스캔되지 않으시면 제공키에 <?php echo $secret; ?>를 입력 하세요.
<hr>
<?php
echo "비교하실 OTP 코드: <b style='color:#ff0000'>".$oneCode."</b><hr>";
?>
<img src="qr.php?images=<?php echo urlencode($qrCodeUrl); ?>" alt="" />