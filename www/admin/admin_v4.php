<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?=$_SESSION["admininfo"][company_name]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="{title_desc}" />
<meta name="keywords" content="{keyword_desc}" />
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<link rel="stylesheet" type="text/css" href="./v3/include/admin.css" />
<link rel="stylesheet" type="text/css" href="./v3/css/class.css" />
<link rel="stylesheet" type="text/css" href="./v3/css/common.css" />
<script type="text/javascript">
var language = "<?=$admininfo[language]?>";
/*function focusIn()
{
    document.auth_frm.otpNumber.focus();
}
window.onload=focusIn;*/
</script>

<style type="text/css">
	a img {border: none;}
	#largeImage {background: #e3e3e3; border: 1px solid; padding: .5em; position: absolute;}
	
	#main-container {height:550px !important; margin-top:-275px !important;}
	.login_box .txt_login_info {color:#fff; font-size:13px; line-height:22px;}
	.admin-login .admin-qrcode {padding:30px 0; text-align:center;}
</style>

<script type="text/javascript" src="./js/jquery-1.4.js"></script>
<script type="text/javascript" src="./js/jquery.blockUI.js"></script>
<script type="text/javascript" src="./js/auto.validation.js"></script>
<script>
	function aaclick() {
		var f = document.auth_frm;
		
		if(f.otpNumber.value == ""){
			alert("OTP Number을 입력하세요.");
			f.otpNumber.focus();
			return false;
		}

		f.submit();
	}
</script>
</head>
<body class="admin-login" style="overflow-y:hidden">

<div id="document">
	<div id="header" style="display: none;">
		<h1><img src="/admin/v3/images/common/<?=$logo_img_title?>_admin_title.png" alt="엔터프라이즈 관리자" /></h1>
		<span class="header-utill">
			<a href="/admin/admin.php" class="btn-top-login">LOG IN</a>
			<a href="https://www.mallstory.com/customer/bbs.php?mode=list&amp;board=notice" class="ex-link" target="_blank">
				Notice
				<img src="v3/images/btns/new_icon.gif" alt="NEW" />
			</a>
		</span>
	</div>
	<div id="main-container" style="<?if($secretChk == 1) {?>height:550px !important; margin-top:-275px !important;<?} else {?>height:550px !important; margin-top:-126px !important;<?}?>">
		<div id="contents">
			<div class="area">
				<form name="auth_frm" id="auth_frm" action="./admin.php" method="post">
					<input type="hidden" name="act" value="cert" />
					<input type="hidden" name="otpkey" value="<?=$secret?>" />
					<input type="hidden" name="id" value="<?=$id?>" />
					<input type="hidden" name="pw" value="<?=$pw?>" />
					<h2 style="font-family: NanumBarunGothicBold;">
<!--                        <img src="v3/images/common/--><?//=$logo_img_title?><!--_admin_title01.png" alt="엔터프라이즈 관리자" title="" />-->
                        <img src="v3/images/common/logo-admin.png" alt="엔터프라이즈 관리자" title="" />

                    </h2>
					<ul class="login_box">
<?
						if($secretChk == 1) {
?>
							<li class="admin-input__wrap admin-qrcode">
								<img src="./otp/qr.php?images=<?php echo urlencode($qrCodeUrl); ?>" alt="" />
							</li>
							<p class="txt_login_info">Google OTP 앱을 받으신 다음 아래 QR코드를 스캔하세요.<br>만약, QR코드가 스캔되지 않으시면 제공키에 <?php echo $secret; ?>를 입력 하세요.</p>
							<li class="admin-input__wrap">
								<input type="text" name="otpNumber" id="otpNumber" tabindex="1" class="font_bold size_16 admin-input" placeholder="OTP Number" />
							</li>
<?
						} else {
?>
							<li class="admin-input__wrap">
								<input type="text" name="otpNumber" id="otpNumber" tabindex="1" class="font_bold size_16 admin-input" placeholder="OTP Number" />
							</li>
<?
						}
?>
						<li>
							<div class="btn-login" onclick="$('#auth_frm').submit();">인증승인</div>
						</li>
						<!-- <li style="padding:9px 0 9px 6px;">
							<a href="http://www.mallstory.com/member/join_agreement.php"><strong class="color_B f-arial size_12">Register </strong></a> ㅣ
							<a href="https://www.mallstory.com/member/search_idpw.php" class="color_B f-arial size_12">Forgot ID or Password</a>
						</li> -->
					</ul>
				</form>
			</div>
<!--			<div class="area">-->
<!--				<img src="v3/images/common/--><?//=$logo_img_title?><!--_login_img01.jpg" title="" alt="" />-->
<!--			</div>-->
		</div>

		<?if($mall_service_type=='selling'){?>
			<div class="wrap-banner">
				<p class="main-desc"><img src="v3/images/common/<?=$logo_img_title?>_login_img02.gif" alt="마켓 통합관리 솔루션 Leading Company FORBIZ KOREA" /></p>
				<div class="main-banner"><img src="v3/images/common/<?=$logo_img_title?>_login_img03.gif" alt="국내/해외 제휴몰" /></div>
			</div>
		<?}?>

	</div>
    <div class="area__bg">
        <img src="v3/images/common/bg-admin.jpg" title="" alt="" />
    </div>
    <p id="main-footer">Copyright ⓒ <strong><?= $_SESSION["shopcfg"]["com_name"]?></strong>. All Rights Reserved.</p>
</div>

<div id='loading' style='display:none;border:0px solid red;width:100px;height:100px;padding-top:13px;text-align:center;'>
<table class='layer_box' border="0" cellpadding="0" cellspacing="0" style='width:270px;height:70px;' >
		<col width='11' />
		<col width='*' />
		<col width='11' />
		<tr>
			<th class='box_01'></th>
			<td class='box_02' ></td>
			<th class='box_03'></th>
		</tr>

		<tr>
			<th class='box_04' style='vertical-align:top'></th>
			<td class='box_05' rowspan="2" valign="top" style='padding:15px 15px 5px 25px;font-size:12px;text-align:left;' >
				<table>
					<tr>
						<td><img src='./images/indicator_.gif' border="0" alt=" " /></td>
						<td style='padding-left:20px;'> 사용자 정보 확인중입니다...</td>
					</tr>
				</table>
			</td>
			<th class='box_06'></th>
		</tr>
		<tr>
			<th class='box_04'></th>
			<th class='box_06'></th>
		</tr>
		<tr>
			<th class='box_07'></th>
			<td class='box_08'></td>
			<th class='box_09'></th>
		</tr>
	</table>
</div>
<div id='layerBg' style='border:0px solid gray;'></div>
<script type="text/javascript">
//	$.blockUI.defaults.css = {};
//	$.blockUI({ message: $('#loading'), css: { width: '100px' , height: '100px' ,padding:  '10px'} });
	$("#admin-id, #admin-pw").focus(function(){
		$(this).css("border-color","#5d5d5d");
	});
	$("#admin-id, #admin-pw").blur(function(){
		$(this).css("border-color","#bdbdbd");
	});
</script>
</body>
</html>
