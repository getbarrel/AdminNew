<?
include("../class/layout.class");

if($admininfo[admin_level] < 9){
	header("Location:../admin.php");
}

$db = new Database;

if($mall_ix==''){
	if($_SESSION[admininfo][mall_ix]){
		$mall_ix=$_SESSION[admininfo][mall_ix];
	}else{
		$mall_ix=$_SESSION[layout_config][mall_ix];
	}
}

function pi_codeName($pi_code){
		switch ($pi_code) {
			case "use":
						$policy_text = "이용약관";
						break;
			case "person":
						$policy_text = "개인정보 취급방침";
						break;
			case "consign":
						$policy_text = "개인정보 취급위탁";
						break;
			case "third":
						$policy_text = "개인정보 제 3자 제공";
						break;
			case "person_all":
						$policy_text = "개인정보 취급방침 전문";
						break;
			case "finance":
						$policy_text = "전자금융거래 이용약관";
						break;
			case "seller":
						$policy_text = "판매회원 이용약관";
						break;
			case "protect":
						$policy_text = "판매회원 개인정보 보호 준수사항";
						break;
			case "duty":
						$policy_text = "세금 납부 유의사항";
						break;
			case "caution":
						$policy_text = "상품 구매 주의사항";
						break;
			case "alliance":
						$policy_text = "제휴 문의";
						break;
			case "teen":
						$policy_text = "청소년 보호대책";
						break;
			case "email":
						$policy_text = "이메일 무단 수집 거부";
						break;
			case "marketing":
						$policy_text = "마케팅 활용 동의";
						break;
			case "premium":
						$policy_text = "프리미엄 회원 약관";
						break;
			case "reseller":
						$policy_text = "리셀러 회원 약관";
						break;
			default:
				;
			break;
		}

		return $policy_text;
	}

$Contents01 = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
<col width='*'>
	<tr>
		<td align='left'>".GetTitleNavigation("약관/개인정보취급방침", "상점관리 > 쇼핑몰 환경설정 > 약관/개인정보취급방침")."</td>
	</tr>
	<tr>
		<td style='padding-bottom:11px;'>
			<select name='mall_ix' validation=false title='프론트 전시구분' onchange=\"location.href='/admin/store/mall_policy_manage.php?mall_ix='+this.value;\" >";
				$displayDivision = GetDisplayDivision($mall_ix);
				foreach($displayDivision as $dd){
					$Contents01 .= "<option value='".$dd[mall_ix]."' ".($dd[mall_ix] == $mall_ix ? "selected":"").">".$dd[mall_ename]." (".$dd[mall_domain].")</option>";
				}
			$Contents01 .= "
			</select>
		</td>
	</tr>
	<tr>
			<td align='left' colspan=4 style='padding-bottom:11px;'>
				 <div class='tab'>
				<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01' ".($code==""?"class='on'" :"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02'><a href='mall_policy_manage.php?mall_ix=".$mall_ix."'>전체</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' ".($code=="use"?"class='on'" :"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' ><a href='mall_policy_list.php?mall_ix=".$mall_ix."&code=use'>이용약관</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03' ".($code=="person"?"class='on'" :"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' ><a href='mall_policy_list.php?mall_ix=".$mall_ix."&code=person'>개인정보 취급방침</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_04' ".($code=="consign"?"class='on'" :"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02'><a href='mall_policy_list.php?mall_ix=".$mall_ix."&code=consign'>개인정보 취급위탁</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_05' ".($code=="third"?"class='on'" :"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02'><a href='mall_policy_list.php?mall_ix=".$mall_ix."&code=third'>개인정보 제 3자 제공</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_06' ".($code=="person_all"?"class='on'" :"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02'><a href='mall_policy_list.php?mall_ix=".$mall_ix."&code=person_all'>개인정보 취급방침 전문</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_07' ".($code=="seller"?"class='on'" :"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02'><a href='mall_policy_list.php?mall_ix=".$mall_ix."&code=seller'>판매회원 이용약과</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_08' ".($code=="protect"?"class='on'" :"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02'><a href='mall_policy_list.php?mall_ix=".$mall_ix."&code=protect'>판매회원 개인정보 보호 준수사항</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_09' ".($code=="duty"?"class='on'" :"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02'><a href='mall_policy_list.php?mall_ix=".$mall_ix."&code=duty'>세금 납부 유의사항</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_10' ".($code=="caution"?"class='on'" :"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02'><a href='mall_policy_list.php?mall_ix=".$mall_ix."&code=caution'>상품 구매 주의사항</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_11' ".($code=="alliance"?"class='on'" :"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02'><a href='mall_policy_list.php?mall_ix=".$mall_ix."&code=alliance'>제휴 문의</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_12' ".($code=="teen"?"class='on'" :"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02'><a href='mall_policy_list.php?mall_ix=".$mall_ix."&code=teen'>청소년 보호대책</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_13' ".($code=="email"?"class='on'" :"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02'><a href='mall_policy_list.php?mall_ix=".$mall_ix."&code=email'>이메일 무단 수집 거부</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_14' ".($code=="marketing"?"class='on'" :"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02'><a href='mall_policy_list.php?mall_ix=".$mall_ix."&code=marketing'>마케팅 활용 동의</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_15' ".($code=="premium"?"class='on'" :"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02'><a href='mall_policy_list.php?mall_ix=".$mall_ix."&code=premium'>프리미엄 회원 약관</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_15' ".($code=="reseller"?"class='on'" :"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02'><a href='mall_policy_list.php??mall_ix=".$mall_ix."&code=reseller'>리셀러 회원 약관</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
			</td>
		</tr>
</table>";

$sql="	SELECT 
			* 
		FROM
		( SELECT *
			FROM shop_policy_info
			WHERE startdate < now()
			AND mall_ix='".$mall_ix."'
			ORDER BY startdate DESC
		) AS h
		GROUP BY pi_code 
		ORDER BY pi_code DESC";

$db->query($sql);

if($db->total){
	for($i=0;$i < $db->total;$i++){
		$db->fetch($i);
$Contents01 .="
<table width='100%' style='margin-top:100px'>
	<tr>
		<td align='left' style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'>
			<img src='../image/title_head.gif' align=absmiddle> 
			<b class='blk'>".pi_codeName($db->dt['pi_code'])."</b>
			<span style='float:right'>".($db->dt['disp'] == "Y" ? $db->dt['startdate']."~" : "<b style='color:red'>미사용</b>" )."
			<a href='mall_policy_list.php?code=".$db->dt['pi_code']."'><input type='button' value='수정' style='display:none' id='modify_btn".$i."' /></a>
			<input type='button' value='보기' onclick='view_policy(".$i.",this)' /></span></div>")."
		</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;display:none' class='input_table_box' id='view_btn".$i."' >
	<col width='*'>
	<tr>
		<td class='input_box_item' style='padding:5px 0px;text-align:left;' colspan='2'>
			<div id='mall_policy_select' style='width:99%;height:150px;overflow:hidden;overflow-y:scroll;padding:10px 0 0 10px;' readonly>".$db->dt['pi_contents']."
			</div>
		</td>
	</tr>
</table>";

		}
	}

$Contents = "<table width='100%' height='100%'  border=0>";
$Contents = $Contents."<tr><td>".$Contents01."<br></td></tr>";
$Contents = $Contents."<tr><td>";
//$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</table >";


$Script = "<script type='text/javascript' src='basicinfo.js'></script>
<script type='text/javascript'>
	function view_policy(id,menu){
		$('#modify_btn'+id).toggle();
		$('#view_btn'+id).toggle();

		if ($('#view_btn'+id).is(':visible')) {
				menu.value ='닫기';
		} else {
				menu.value ='보기';
		}
	}
</script>
";

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->Navigation = "상점관리 > 쇼핑몰 환경설정 > 약관/개인정보취급방침";
$P->title = "약관/개인정보취급방침";
$P->strContents = $Contents;
echo $P->PrintLayOut();




/*
create table admin_menus (
menu_code varchar(32) not null ,
menu_name varchar(255) null default null,
menu_path varchar(255) null default null,
auth_read enum('Y','N') null default 'Y',
auth_write enum('Y','N') null default 'Y',
shipping_company varchar(30) null default null,
primary key(menu_code));


CREATE TABLE IF NOT EXISTS `shop_mall_config` (
  `mall_ix` varchar(32) NOT NULL COMMENT '쇼핑몰키',
  `config_name` varchar(100) NOT NULL DEFAULT '' COMMENT 'PG 환경설정 변수이름',
  `config_value` varchar(255) DEFAULT NULL COMMENT 'PG 환경설정 변수값',
  PRIMARY KEY (`mall_ix`,`config_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='쇼핑몰 환경설정 정보';


*/
?>