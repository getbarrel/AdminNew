<? if($_SERVER[SERVER_NAME] == "daiso.forbiz.co.kr" || $_SERVER[SERVER_NAME] == "daisodev.forbiz.co.kr" || $_SERVER[SERVER_NAME] == "daisomall.mallstory.com" || $_SERVER[SERVER_NAME] == "daisomall.co.kr" || $_SERVER[SERVER_NAME] == "www.daisomall.co.kr"){ ?>
<? include("./admin_header.php");?>
	<div class='daiso_admin_margin'>
		<h1 style="padding-bottom:16px; text-align:left;"><img src="v3/images/common/daiso_admin_Seller.png" alt="다이소몰 입점 협력사 오피스" title="" /></h1>
		<div class='daiso_login_box2'>
			<div class='daiso_left_top'>
				<div class='daiso_left_margin'>
					<!--다이소 로그인-->
					<div class="daiso_login_box">
						<div class='daiso_login_wrap'>
							<form name="login_frm" action="" onsubmit="return CheckFormValue(this);" method="POST"> <input type=hidden name="act" value="verify">
							<table cellspacing="0" cellpadding="0" border="0" width="357px">
								<col width='95' />
								<col width='160' />
								<col width='86' />
								<tr>
									<th>
										<img src="v3/images/common/daiso_admin_id.png" alt="id" />
									</th>
									<td>
										<input type="text" name="id" value="<?=$_COOKIE['ck_adminSaveID']?>" tabindex=1 class="font_bold size_16" style="width:152px;padding:4px 0px 3px 4px;" align="absmiddle" />
									</td>
									<td rowspan='3' style='padding-left:7px;'>
										<input type='image' src="v3/images/common/daiso_admin_login2.png" alt="로그인버튼" title="" align="absmiddle" />
									</td>
								</tr>
								<tr>
									<td colspan='2' height='7px'></td>
								</tr>
								<tr>
									<th>
										<img src="v3/images/common/daiso_admin_pw.png" alt="pw" />
									</th>
									<td>
										<input type='password' name="pw" value='' tabindex=2 class="vm font_bold size_16" style="width:152px;padding:4px 0px 3px 4px;"/>
									</td>
								</tr>
								<tr>
									<td></td>
									<td>
										<ul class='daiso_login_ul'>
											<li>
												<input type="checkbox" id="chk_saveID" name="chk_saveID" value="Y"<?=($_COOKIE['ck_adminSaveID'])	?	' checked':'';?> class="vm" /><label for="chk_saveID" style="letter-spacing:-1px;" class="vm size_11 color_7b">아이디 저장</label>
											</li>
										</ul>
									</td>
								</tr>
							</table>
							</form>
						</div>
						<div class='daiso_login_text'>
							<div>
								다이소몰과 함께 할 성장파트너를 찾습니다.<br /><span>신규 입점제휴</span> 하시고 <span style='color:#bf6c0c;'>힘찬 도약</span>을 함께 하세요!
							</div>
							<a href="/admin/member/admin_join.php"><img src="v3/images/common/daiso_join_buttom.gif" alt="신규 입점 제휴" /></a>
						</div>
					</div>
					<!--다이소로그인-->
				</div>
			</div>
			<div class='daiso_right_top'>
				<dl>
					<dt class='img_line_font_0'>
						<img src="v3/images/common/daiso_customer_title.gif" alt="고객만족센터" />
					</dt>
					<dd class='img_line_font_0 daiso_customer_dd' >
						<img src="v3/images/common/daiso_customer_number.gif" alt="02.405.0700" />
					</dd>
					<dd class='daiso_customer_dd2'>
						<span>
							<a href="mailto:sellerhelp@daisomall.co.kr">sellerhelp@daisomall.co.kr</a>
						</span>
					</dd>
					<dd class='daiso_customer_dd3'>
						평일(월~금) 9:30 ~ 17:00<br />점심시간 12:00 ~ 13:00<br />토/일/공휴일 휴무
					</dd>
					<dd class='img_line_font_0'>
						<a href=""><img src="v3/images/common/daiso_customer_buttom.gif" alt="1:1문의하기" /></a>
					</dd>
				</dl>
			</div>
			<div class='daiso_center_middle'></div>
			<div class='daiso_bottom_wrap'>
				<ul class='daiso_customer_order'>
					<li class='daiso_order_li01'>
						<img src="v3/images/common/customer_order_img.gif" alt="입점절차" />
					</li>
					<li class='daiso_order_li02'>
						<dl>
							<dt>
								(주)한웰이쇼핑<span style="font-weight:normal;">(다이소몰)</span>
							</dt>
							<dd class='daiso_order_dd'>
								공인인증서
							</dd>
							<dd class='img_line_font_0'>
								<a href="https://raadmin.crosscert.com/customer/daisomall/index.html" target="_blank">
									<img src="v3/images/common/daiso_customer_Download.gif" alt="신청하기" />
								</a>
							</dd>
						</dl>
					</li>
				</ul>
			</div>
		</div>
		<div class='daiso_admin_banner'>
			<div class='daiso_banner_01'>
				<?
				echo get_banner_4_admin(347);
				?>
			</div>
			<div class='daiso_banner_02'>
				<?
				echo get_banner_4_admin(348);
				?>
			</div>
		</div>
	</div>
<? include("./admin_copyright.php");?>
<? }else{ ?>
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
function focusIn()
{
    document.login_frm.id.focus();
}
window.onload=focusIn;
</script>
<style type="text/css">
	a img {
		border: none;
	} #largeImage {
		position: absolute;
		padding: .5em;
		background: #e3e3e3;
		border: 1px solid;
	}
</style>
<script type="text/javascript" src="./js/jquery-1.4.js"></script>
<script type="text/javascript" src="./js/jquery.blockUI.js"></script>
<script type="text/javascript" src="./js/auto.validation.js"></script>

</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" width="100%" >
	<tr>
		<td class="top_menu_area" align="left" style="background:#000;">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" >
				<col width="*" />
				<col width="23%" />
				<tr>
					<td>
						<div class="left_menu01" >
							<img src="/admin/v3/images/common/<?=$mall_ename?>_admin_title.png" alt="엔터프라이즈 관리자" title="" style="position:relative;top:2px;" />
						</div>
					</td>
					<td align="right">
						<div class="top_menu">
							<ul>
								<li>
									<a href="/admin/admin.php" class="btn-top-login">LOG IN</a>
								</li>
								<li class="top_menu_list01">
									<a href="https://www.mallstory.com/customer/bbs.php?mode=list&amp;board=notice" target="_blank">Notice <img src="v3/images/btns/new_icon.gif" alt="" title="" /></a>
								</li>
							</ul>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td class="topmenu_bg01" style="border-bottom:4px solid #ed1c24;"></td></tr>
	<tr>
		<td align="center" style="background:url(v3/images/common/login_bg.gif) repeat-x;height:588px;">
			<div>
				<table cellpadding="0" cellspacing="0" border="0" width="737" class="table_fix">
					<col width="350" />
					<col width="*" />
					<tr>
						<td align="left">
							<div style="padding:30px 43px 30px 0;border-right:1px solid #dcdcdc;">
								<form name="login_frm" id="login_frm" action="" onsubmit="return CheckFormValue(this);" method="post"> <input type="hidden" name="act" value="verify" />
									<h2 style="font-family: NanumBarunGothicBold;"><img src="v3/images/common/<?=$mall_ename?>_admin_title01.png" alt="엔터프라이즈 관리자" title="" /></h2>
									<ul class="login_box">
										<li>
											<input type="text" name="id" id="admin-id" value="<?=$_COOKIE['ck_adminSaveID']?>" tabindex="1" class="font_bold size_16" style="padding:10px;width:284px;border:1px solid #bdbdbd;" />
										</li>
										<li>
											<input type="password" name="pw" id="admin-pw" value='' tabindex="2" class="font_bold size_16" style="padding:10px;width:284px;border:1px solid #bdbdbd;"  onkeyup="if(event.keyCode == 13) $('#login_frm').submit();;"/>
										</li>
										<li>
											<input type="checkbox" id="chk_saveID" name="chk_saveID" value="Y"<?=($_COOKIE['ck_adminSaveID'])	?	'checked':'';?>  align="absmiddle" style="margin:0 0 0 7px;" />
											<label for="chk_saveID" class="vm color_B f-arial">Remember ID</label>
										</li>
										<li>
											<div class="btn-login" onclick="$('#login_frm').submit();">LOG IN</div>
										</li>
										<!-- <li style="padding:9px 0 9px 6px;">
											<a href="http://www.mallstory.com/member/join_agreement.php"><strong class="color_B f-arial size_12">Register </strong></a> ㅣ
											<a href="https://www.mallstory.com/member/search_idpw.php" class="color_B f-arial size_12">Forgot ID or Password</a>
										</li> -->
									</ul>
								</form>
							</div>
						</td>
						<td class="vt align_left">
							<div style="padding-left:38px;">
								<div>
									<img src="v3/images/common/<?=$mall_ename?>_login_img01.gif" title="" alt="" />
								</div>
								
							</div>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	<tr>
		<td align="center" style="padding:10px 0;font-family:돋움">
			Copyright ⓒ <strong><?= $_SESSION["shopcfg"]["com_name"]?></strong>. All Rights Reserved.
		</td>
	</tr>
</table>
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

<!-- -------------------------------------------------------------------------다이소------------------------------------------------------------------------------------ -->
<? } ?>
</body>
</html>
<?
function get_banner_4_admin($banner_ix = false, $banner_width=false, $banner_height=false, $div_ix = false, $cid = false, $class = false){
	$db = new Database;
	$sql = "SELECT * FROM shop_shopinfo WHERE mall_domain = '" . str_replace('www.','',$_SERVER['HTTP_HOST']) . "'";
	$db->query($sql);

	$shop_info = $db->fetch();

	$sql = "select b.banner_ix , b.banner_kind
				from shop_bannerinfo b
				where banner_ix = '$banner_ix' and disp ='1'
				and NOW() between use_sdate and use_edate
				order by use_sdate asc , use_edate asc limit 1 ";
	$db->query($sql);
	$db->fetch();
	$banner_kind  = $db->dt[banner_kind];

	$today_srch = date("YmdHi");

	$where = "WHERE bi.disp='1' AND b.sdate <= '".$today_srch."' AND b.edate >= '".$today_srch."' ";

	if ($banner_kind == 1){	// 일반배너


		if ($banner_ix) $where.= " AND bi.banner_ix = ".$banner_ix;
		if ($div_ix) $where.= " AND bd.div_ix = ".$div_ix;
		if ($cid)	$where.= " AND b.cid = '".$cid."'";
		/*
		$sql = "SELECT * FROM shop_bannerinfo bi
					INNER JOIN shop_display_banner b ON bi.banner_ix = b.banner_ix AND b.banner_div = '$banner_kind'
					LEFT OUTER JOIN shop_banner_div bd ON b.div_ix=bd.div_ix
					".$where;
		*/
		$sql = "select b.banner_ix, b.banner_kind, change_effect, banner_img,banner_img_on, banner_link,banner_target,banner_width,banner_height,disp,
			IFNULL(sum(bc.ncnt),0) as ncnt
			from shop_bannerinfo b left join logstory_banner_click bc
			on b.banner_ix = bc.banner_ix and b.banner_ix = '".$banner_ix."' ".$vdate_str."
			where b.banner_ix = '".$banner_ix."' and disp ='1'
			group by b.banner_ix, banner_img,banner_link,banner_target,banner_width,banner_height  ";

		//echo nl2br($sql);
		$db->query($sql);
		if ($db->total){

			for ($i=0; $i<$db->total; $i++){
				$db->fetch($i);
				$banner_ix			= $db->dt[banner_ix];
				$banner_img		= $db->dt[banner_img];
				$banner_width	= $db->dt[banner_width];
				$banner_height	= $db->dt[banner_height];
				$banner_img_on	= $db->dt[banner_img_on];
				$banner_on_use	= $db->dt[banner_on_use];
				$banner_target	= $db->dt[banner_target];

				$imgPath = $shop_info[mall_data_root]."/images/banner/".$banner_ix."/";
				if ($i>0) $mString."<BR>";

				if(substr_count($banner_img,'.swf') > 0){
					$mString .= "<script language='javascript'>generate_flash('".$layout_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img."', '".$banner_width."', '".$banner_height."');</script>";
				}else if ($banner_on_use=="Y" || $banner_img_on){	// 마우스오버시 바로 이미지가 바뀌는 오버기능 사용시
					$mString .= "<a href='/banner.link.php?banner_ix=".$banner_ix."' target='".$banner_target."'><img src='".$shop_info[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img."' width='".$banner_width."' height='".$banner_height."' onmouseover='this.src=\"".$imgPath.$banner_img_on."\"' onmouseout='this.src=\"".$imgPath.$banner_img."\"'></a>";//<li ".$class."></li>
				}else if ($banner_img_on){	// 롤오버 이미지가 있으면
					//$mString .= "<li>";
					$mString .= "<a href='/banner.link.php?banner_ix=".$banner_ix."' target='".$banner_target."'><img src='".$shop_info[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img."' width='".$banner_width."' height='".$banner_height."'></a>";
					//$mString .= "</li>";
					//$mString .= "<li>";
					$mString .= "<a href='/banner.link.php?banner_ix=".$banner_ix."' target='".$banner_target."'><img src='".$shop_info[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img_on."'  width='".$banner_width."' height='".$banner_height."'></a>";
					//$mString .= "</li>";
				}else{

					$mString .= "<a href='/banner.link.php?banner_ix=".$banner_ix."' target='".$banner_target."'><img src='".$shop_info[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img."'  width='".$banner_width."' height='".$banner_height."'></a>";//<li ".$class."></li>
				}
			}
		}

	}else if ($banner_kind == 2 || $banner_kind == 3){	// 플래시,슬라이드배너
		if ($banner_ix) $where.= " AND bi.bd_ix = ".$banner_ix;
		if ($div_ix) $where.= " AND bd.div_ix = ".$div_ix;
		/*
		$sql = "SELECT * FROM ".TBL_SHOP_MANAGE_FLASH." bi
					INNER JOIN shop_display_banner b ON bi.bd_ix = b.banner_ix AND b.banner_div = '$banner_kind'
					LEFT OUTER JOIN shop_banner_div bd ON b.div_ix=bd.div_ix
					LEFT OUTER JOIN shop_manage_flash_detail mfd on bi.bd_ix = mfd.bd_ix
					".$where;
		*/
		$sql = "select b.banner_ix, b.banner_kind, change_effect, banner_img,banner_link,banner_target,banner_width,banner_height,disp, bd.*,
			IFNULL(sum(bc.ncnt),0) as ncnt
			from shop_bannerinfo b left join shop_bannerinfo_detail bd on b.banner_ix = bd.banner_ix
			left join logstory_banner_click bc
			on b.banner_ix = bc.banner_ix and b.banner_ix = '".$banner_ix."' ".$vdate_str."
			where b.banner_ix = '".$banner_ix."' and disp ='1'
			group by b.banner_ix, bd.bd_ix, banner_img,banner_link,banner_target,banner_width,banner_height  ";

		//echo nl2br($sql);
		//exit;
		$db->query($sql);
		if ($banner_kind == 2){	// 플래시배너

			$i_no = 0;
			$btn_no = "";
			$printflash = $db->fetchall();
			$banner_ix	= $printflash[0][banner_ix];
			$banner_width	= $printflash[0][banner_width];
			$banner_height	= $printflash[0][banner_height];
			$change_effect = $printflash[0][change_effect];
			//echo "change_effect:".$change_effect;


			if (is_array($printflash)){
				foreach($printflash as $_key => $_val){
					if($printflash[$_key][bd_file] != ""){ //이미지값과 네비게이션 숫자 데이터를 담아둔다
						$i_no++;
						$imgPath = $shop_info[mall_data_root]."/images/banner/".$banner_ix."/"; //$printflash[$_key][bd_ix]

						$imgString .= "<a href='/banner.link.php?banner_ix=".$banner_ix."&bd_ix=".$printflash[$_key][bd_ix]."' target='".$target."'><!--a href='".$printflash[$_key][bd_link]."'--><img src='".$imgPath.$printflash[$_key][bd_file]."' title='".$printflash[$_key][bd_title]."' width='".$banner_width."' height='".$banner_height."'></a>";
					}
				}
			}
			//echo $imgString;
			//exit;
			$bd_ix = $printflash[0][bd_ix];


			$time_sec = $printflash[0][time_sec] * 1000;
			if (!$time_sec) $time_sec = 4000;


				$mString .= "

			<STYLE>
			#slider-wrapper {
				background:url(/images/slider.png) no-repeat;
				width:".$banner_width."px;
				height:".$banner_height."px;
				margin:0 auto;
				padding-top:74px;
				margin-top:50px;
			}

			#slider {
				position:relative;
				width:".$banner_width."px;
				height:".$banner_height."px;
				margin-left:0px;
				margin-bottom:10px;
				background:url(/images/loading.gif) no-repeat 50% 50%;
			}
			</STYLE>
				";

				$mString .= "<div id='slider_".$banner_ix."' class='nivoSlider' style='height:".$banner_height."px'>";
				$mString .= "{$imgString}";
				$mString .= "</div>";
				if($i_no > 1){
						$mString .= "
						<script type='text/javascript'>
						$(window).load(function() {";
					if($change_effect == "S"){
						$mString .= "
							$('#slider_".$banner_ix."').nivoSlider({
								effect:'fade',
								pauseTime:".$time_sec.",
								pauseOnHover:true
							});
						";
					} else if($change_effect == "F"){
						$mString .= "
							$('#slider_".$banner_ix."').nivoSlider({
								effect:'fade',
								pauseTime:".$time_sec.",
								pauseOnHover:true
							});";
					} else if($change_effect == "T"){
						$mString .= "
							$('#slider_".$banner_ix."').nivoSlider({
								effect:'fold',
								pauseTime:".$time_sec.",
								pauseOnHover:true
							});";
					} else if($change_effect == "R"){
						$mString .= "
							$('#slider_".$banner_ix."').nivoSlider({
								effect:'random',
								animSpeed:1500,
								pauseTime:".$time_sec.",
								startSlide:2,
								directionNav:false,
								controlNav:true,
								keyboardNav:false,
								pauseOnHover:false
							});";
					}
					$mString .= "
					});
						</script>
						<script type='text/javascript' language=javascript src='/js/banner_slide.js'></script>
						";
				}
		}else if ($banner_kind == 3){	// 슬라이드 배너

			$printflash = $db->fetchall();
			$banner_ix	= $printflash[0][banner_ix];
			$banner_width	= $printflash[0][banner_width];
			$banner_height	= $printflash[0][banner_height];
			$change_effect = $printflash[0][change_effect];
			//echo "change_effect:".$change_effect;

			//print_r($printflash);
			if (is_array($printflash)){
				$html = '<div style="position:relative;float:left;width:'.$banner_width.';overflow:hidden;height:'.$banner_height.'px; " id="main_scroll_width1">
								<div id="slide_banner_'.$banner_ix.'" class="goods" style="float:left;width:2500px;white-space:nowrap;margin:0; height:'.$banner_height.'px; overflow:hidden; z-index:-10;">';
				foreach($printflash as $_key => $_val){
					if($printflash[$_key][bd_file] != ""){ //이미지값과 네비게이션 숫자 데이터를 담아둔다
						$i_no++;
						$imgPath = $shop_info[mall_data_root]."/images/banner/".$banner_ix."/".$printflash[$_key][bd_file]; //$printflash[$_key][bd_ix]

						$_html .= "<ul class='banners' style='float:left;z-index:-5;'>\n";
						$_html .= "	<li><a href='".$printflash[$_key][bd_link]."'><img src='".$imgPath."' title='".$printflash[$_key][bd_title]."' ></a></li>\n";
						$_html .= "</ul>\n";
					}
				}
				$img_size = getimagesize($_SERVER["DOCUMENT_ROOT"].$imgPath);
				$width = $img_size[0];
				$height = $img_size[1];
				//$_html .= $_html.$_html;
				$html .= $_html."</div>
						<div class='s_l_b' style='position:absolute; z-index:10; top:50%;margin-top:-13px; right:20px;'>
							<a href=\"javascript:clickbannerScroll('slide_banner_".$banner_ix."',".$width.");\"><img src='".$shop_info[mall_templet_webpath]."/images/common/right.png' on_src='".$shop_info[mall_templet_webpath]."/images/common/right_on.png' out_src='".$shop_info[mall_templet_webpath]."/images/common/right.png' onmouseover=\"$(this).attr('src',$(this).attr('on_src'))\" onmouseout=\"$(this).attr('src',$(this).attr('out_src'))\"  alt='' title='' /></a>
						</div>
						<div class='s_l_b' style='position:absolute; z-index:10; top:50%; margin-top:-13px; left:20px;'>
							<a href=\"javascript:clickbannerScroll('slide_banner_".$banner_ix."',-".$width.");\"><img src='".$shop_info[mall_templet_webpath]."/images/common/left.png' on_src='".$shop_info[mall_templet_webpath]."/images/common/left_on.png' out_src='".$shop_info[mall_templet_webpath]."/images/common/left.png' onmouseover=\"$(this).attr('src',$(this).attr('on_src'))\" onmouseout=\"$(this).attr('src',$(this).attr('out_src'))\"  alt='' title=''/></a>
						</div>
						</div>";
			}


			$html .= "<script language='javascript'>
							<!--
								var slideBanner = setInterval(\"bannerScroll('slide_banner_".$banner_ix."',-".$width.")\", 3000);
								$('div#slide_banner_".$banner_ix."').hover(function()	{
									clearInterval(slideBanner);
								}, function()
								{
									slideBanner = setInterval(\"bannerScroll('slide_banner_".$banner_ix."',-".$width.")\", 3000);
								});
							//-->
							</script>";
			$html.="<script type='text/javascript' language=javascript src='/js/banner_slide.js'></script>";
			$mString = $html;
		}
	}else if ($banner_kind == 4){	// 동영상 배너
		$sql = "select b.banner_ix, b.banner_kind, change_effect, banner_html,banner_img_on, banner_link,banner_target,banner_width,banner_height,disp,
			IFNULL(sum(bc.ncnt),0) as ncnt
			from shop_bannerinfo b left join logstory_banner_click bc
			on b.banner_ix = bc.banner_ix and b.banner_ix = '".$banner_ix."' ".$vdate_str."
			where b.banner_ix = '".$banner_ix."' and disp ='1'
			group by b.banner_ix, banner_img,banner_link,banner_target,banner_width,banner_height  ";

		//echo nl2br($sql);
		$db->query($sql);
		$db->fetch();
		$mString = $db->dt[banner_html];
	}

	// 배너유입수 Log Insert

	return $mString;
}
?>
<!--
<html>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<title></title>
<style>
TD{font-size:12px;font-family:돋움}
.bg_line {background: url(./image/bg_line.gif) no-repeat left top; }
.bg_color2 {background: url(./image/bg_color.gif) repeat-x left top; }
.bg_main {background: url(./image/bg.gif) no-repeat left top; }
.bg_login {background:url(./image/bg_login.gif) no-repeat center top; }
</style>
<Script Language='JavaScript'>
var language = "<?=$admininfo[language]?>";
function focusIn()
{
    document.login_frm.id.focus();
}
window.onload=focusIn;
</Script>
<script language='JavaScript' src='./js/jquery-1.4.js'></Script>
<script language='javascript' src='./js/jquery.blockUI.js'></script>
<script language='JavaScript' src='./js/auto.validation.js'></Script>
<body topmargin=0 leftmargin=0 class="bg_color2">
<table cellpadding=0 border=0 cellspacing=0 width="100%" height="100%" class="bg_login">
	<tr height=290><td></td></tr>
	<tr>
		<td valign=top align=center style="padding-left:150px;">
			<div style="position:relative;width:300px;hegiht:200px;"  align=center >
			<form name="login_frm" action="" onsubmit="return CheckFormValue(this);" method="POST"> <input type=hidden name="act" value="verify">
			<table cellpadding=0 border=0 cellspacing=0 width="280" height="50" >
				<tr height=24>
					<td width=60>아이디 </td>
					<td width=100><input type=text name="id" value="<?=$_COOKIE['ck_adminSaveID']?>" style='width:120px;border:1px solid silver;ime-mode:disabled ;' onfocus="this.style.border='2px solid orange'" onfocusout="this.style.border='1px solid silver'" validation='true' title='아이디' tabindex=1> </td>
					<td width=78 rowspan=2><input type=image src="./image/btn_login.gif" size=14 tabindex=3> </td>
				</tr>
				<tr height=24>
					<td>비밀번호 </td>
					<td><input type=password name="pw" style='width:120px;border:1px solid silver' onfocus="this.style.border='2px solid orange'" onfocusout="this.style.border='1px solid silver'"  validation='true' title='비밀번호' tabindex=2> </td>
				</tr>
				<tr height="24">
					<td colspan="2"><input type="checkbox" id="chk_saveID" name="chk_saveID" value="Y"<?=($_COOKIE['ck_adminSaveID'])	?	' checked':'';?> /> <label for="chk_saveID">아이디 저장</label></td>
				</tr>
			</table>
			</form>
			</div>
		</td>
	</tr>
	<tr height=200><td></td></tr>
</table>
<div id='loading'></div>
</body>
</html>
-->
