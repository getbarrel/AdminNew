<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");
if($admininfo[admin_level] == ""){
	header("Location:/admin/");
}
if($admininfo[admin_level] < 9){
	header("Location:/admin/seller/");
}

$shmop = new Shared("reserve_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$reserve_data = $shmop->getObjectForKey("reserve_rule");
$reserve_data = unserialize(urldecode($reserve_data));

//print_r($reserve_data);

//echo md5("wooho".$db->dt[mall_domain].$db->dt[mall_domain_id]);



/*
$Contents01 = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
<col width='27%' />
<col width='*' />
	<tr>
		<td align='left' colspan='2''> ".GetTitleNavigation("적립금 정책", "상점관리 > 적립금 정책 ")."</td>
	</tr>
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>적립금 지급 정책</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='25%' />
	<col width='*' />
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>적립금 지급 여부 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'><input type='radio' name='reserve_use_yn' value='Y' ".($reserve_data[reserve_use_yn] == "Y" ? "checked":"")."> 사용 <input type='radio' name='reserve_use_yn' value='N' ".($reserve_data[reserve_use_yn] =="N" ? "checked":"")."> 사용안함</td>
	</tr>
	<tr bgcolor='#ffffff' height='50'>
		<td class='input_box_title'> <b>상품적립금 기본 설정 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' style='line-height:150%'>
			<!--상품 금액의  <input type=text class='textbox' name='goods_reserve_rate' value='".$reserve_data[goods_reserve_rate]."' style='width:60px;' validation='true' title='상품적립금 기본설정'>  % 를 적립합니다.-->  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F', $reserve_data, "reserve_data")." <br><span class=blue><!--* 신규 상품 등록시 기본으로 적용되며, 상품별로 개별 설정시 본 설정은 적용되지 않습니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
		</td>
	</tr>
	<tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title'>
			<b>회원가입 적립금 설정 <img src='".$required3_path."'></b>
		</td>
		<td class='input_box_item'>
			<!--신규 회원 가입시  <input type=text class='textbox' name='join_reserve_rate' value='".$reserve_data[join_reserve_rate]."' style='width:60px;' validation='true' title='회원가입 적립금 설정'> 원을 적립합니다.</span--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'H' ,$reserve_data ,"reserve_data")."<br>
			<span class=blue><!--* 회원가입 적립금을 지급하시지 않을 경우 0원을 입력 하시면 됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
		</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;margin-top:30px;' >
	<col width='25%' />
	<col width='*' />
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>적립금 사용 정책</b></div>")."</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='25%' />
	<col width='*' />
	<tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title' rowspan='2'> <b>적립금 사용 제한 설정 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<!--상품 구매 합계액이  <input type=text class='textbox' name='total_order_price' value='".$reserve_data[total_order_price]."' style='width:60px;' validation='true' title='적립금 사용제한 설정'> 원 이상 상품 구매시 사용 가능(제한이 없을경우 0입력)--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'J',$reserve_data,"reserve_data")."
		</td>
	</tr>
	<tr bgcolor='#ffffff' height=;'0' style='line-height:150%'>
		<td class='input_box_item'>
			<!--보유적립금이<input type=text class='textbox' name='min_reserve_price' value='".$reserve_data[min_reserve_price]."' style='width:60px;' validation='true' title='적립금 사용제한 설정'> 원 이상일때 상품 구매시 사용 가능(제한이 없을경우 0입력)--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'L' ,$reserve_data,"reserve_data")."
		</td>
	</tr>
	<tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title' rowspan='3'>
			<b>적립금 1회 사용 한도  <img src='".$required3_path."'></b>
		</td>
		<td class='input_box_item'>
			<!--input type=radio name='reserve_one_use_type' value='1' ".($reserve_data[reserve_one_use_type] == "1" ? "checked":"")." onclick=\"document.getElementById('max_goods_sum_rate').validation=false;document.getElementById('use_reseve_max').validation=true\"> 최대  <input type=text class='textbox' name='use_reseve_max' value='".$reserve_data[use_reseve_max]."' style='width:60px;' id='use_reseve_max' ".($reserve_data[reserve_one_use_type] == "1" ? "validation='true'":"validation='false'")." title='적립금 1회 사용 한도'> 원 까지만 사용 가능--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'M',$reserve_data,"reserve_data")." <br>
			<span class='blue small'><!--* 적립금으로 전액 결제 가능하게 하시려면 0원을 입력하시면 됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')." </span>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=50 style='line-height:150%'>
		<td class='input_box_item'>
			<!--input type=radio name='reserve_one_use_type' value='2' ".($reserve_data[reserve_one_use_type] == "2" ? "checked":"")." onclick=\"document.getElementById('max_goods_sum_rate').validation=true;document.getElementById('use_reseve_max').validation=false\">  상품 구매 합계액의 <input type=text class='textbox' name='max_goods_sum_rate' value='".$reserve_data[max_goods_sum_rate]."' style='width:60px;' id='max_goods_sum_rate' ".($reserve_data[reserve_one_use_type] == "2" ? "validation='true'":"validation='false'")." title='적립금 1회 사용 한도'>%  까지만 사용가능(최대 100%)--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'O' ,$reserve_data,"reserve_data")." <br>
		</td>
	</tr>
</table>";
*/
$Contents01 = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;margin-top:30px;' >
	<col width='25%' />
	<col width='*' />
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>MGM(Members Get Members Marketing) 혜택 정책</b></div>")."</td>
	</tr>
	</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='25%' />
	<col width='*' />
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>MGM 사용 여부 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		<input type='radio' name='mgm_use_yn' id='mgm_use_y'  value='Y' ".($reserve_data[mgm_use_yn] == "Y" ? "checked":"")."><label for='mgm_use_y'>사용</label> 
		<input type='radio' name='mgm_use_yn' id='mgm_use_n' value='N' ".($reserve_data[mgm_use_yn] =="N" ? "checked":"")."><label for='mgm_use_n'>사용안함</label>
		</td>
	</tr>
	<tr bgcolor='#ffffff' height='50'>
		<td class='input_box_title'> <b>MGM 구매시 적립금<img src='".$required3_path."'></b></td>
		<td class='input_box_item' style='line-height:150%'>
			상품 금액의  <input type=text class='textbox' name='mgm_reserve_rate' value='".$reserve_data[mgm_reserve_rate]."' style='width:60px;' validation='true' title='MGM 상품적립금 기본설정'>  % 를 적립합니다<br><span class=blue>신규 상품 등록시 기본으로 적용되며, 상품별로 개별 설정시 본 설정은 적용되지 않습니다.</span>
		</td>
	</tr>
	<tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title'>
			<b>회원 추천시 적립금 <img src='".$required3_path."'></b>
		</td>
		<td class='input_box_item'>
			신규 회원 가입시 추천인으로 등록 적립금 <input type=text class='textbox' name='mgm_reserve' value='".$reserve_data[mgm_reserve]."' style='width:60px;' validation='true' title='추천인 적립금 설정'> 원을 적립합니다.</span><br>
			<span class=blue><!--* 회원가입 적립금을 지급하시지 않을 경우 0원을 입력 하시면 됩니다.-->  </span>
		</td>
	</tr>
	</table>
	<table width='100%' border='0'>
		<tr>
			<td align='left' style='line-height:120%;'>
				※ <span class='small'> MGM 마케팅 은 마이페이지에 친구에게 메일 보내기 , SNS (facebook, 트위터, 미투데이, 요즘 등), 배너교환 등 을 통해 정보를 공유하고 그를 통해 회원이 유입된 경우 회원에게 드리는 혜택 입니다. </span><br>
				※ <span class='small'> 혜택은 신규로 방문하는 고객에 한해서만 적용되며 한번 방문시 방문정보가 1주일간 유지 됩니다. </span>
			</td>
			
		</tr>
	</table>
	";
/*
$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small>
		  도메인, 도메인 아이디, 도메인 key 등은 몰스토리에서 발급해드리는 사항이므로 변경이 불가능합니다.<br>
		  <u>상업적인 목적으로 상점을 운영</u>하기 위해서는 정식 <b>도메인 key</b>를 발급 받아 사용하셔야만 상점을 정상적으로 운영하실수 있습니다.
	</td>
</tr>
</table>
";
*/
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff height=70><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
}

$Contents = "<table width='100%'  border=0>";
$Contents = $Contents."<form name='edit_form' action='reserve_rule.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data'><!-- target='act'-->";
$Contents = $Contents."<tr><td>".$Contents01."<br></td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";

$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."</table >";

/*
$help_text = "
<table cellpadding=2 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td ><img src='/admin/image/icon_list.gif' ></td><td class='small' >사이트 적립금 정책을 관리합니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >적립금 정책 수정 즉시 반영되게 됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원이 사용 가능한 보유적립금의 1회 사용 한도를 금액 또는 %로 설정 하실 수 있습니다. </td></tr>
	<tr><td valign=top></td><td class='small' style='line-height:120%' >예) 사용가능 누적 적립금 5,000원 이상과 1회 한도 10,000원으로 설정했을 경우, 회원의 보유적립금이 13,000원인 경우, 구매시 10,000원 까지는 사용 가능하고, 나머지 3,000원은 다음 구매시 보유적립금 5,000원 이상일때 사용 가능합니다. </td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'D');

$Contents .=  HelpBox("MGM 정책 관리", $help_text);

//$Contents = "<div style=height:1000px;'></div>";


$Script = "<script language='javascript' src='reserve_rule.js'></script>";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->Navigation = "상점관리 > 결제관련 > MGM 정책";
$P->title = "MGM 정책";
$P->strContents = $Contents;
echo $P->PrintLayOut();



?>