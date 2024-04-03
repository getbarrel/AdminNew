<?
include("../class/layout.class");
include_once("buyingService.lib.php");


$db = new Database;

$sql = "select * from shop_buyingservice_info order by regdate desc limit 0,1 ";

$db->query ($sql);

if($db->total){
	$db->fetch();

	$exchange_rate = $db->dt[exchange_rate];
	$bs_basic_air_shipping = $db->dt[bs_basic_air_shipping];
	$bs_add_air_shipping = $db->dt[bs_add_air_shipping];
	$bs_duty = $db->dt[bs_duty];
	$bs_supertax_rate = $db->dt[bs_supertax_rate];
	$clearance_fee = $db->dt[clearance_fee];
}




$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6 > ".GetTitleNavigation("구매대행 상품관리", "상품관리 > 구매대행 상품관리 ")."</td>
	  </tr>
	  <tr>
			    <td align='left' colspan=4 style='padding-bottom:15px;'>
			    	<div class='tab'>
							<table class='s_org_tab'>
							<tr>
								<td class='tab'>
									<table id='tab_01' class='on' >
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='buyingService.php?mmode=pop'\">구매대행 상품 등록</td>
										<th class='box_03'></th>
									</tr>
									</table>
									<table id='tab_02' >
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='buyingServiceInfo.php?mmode=pop'\">구매대행 환율/수수료 관리</td>
										<th class='box_03'></th>
									</tr>
									</table>

								</td>
								<td class='btn'>

								</td>
							</tr>
							</table>
						</div>
			    </td>
			</tr>
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>구매대행 상품등록</b></div>")."</td>
	  </tr>
	  </table>

	  <table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box'>
	  <col width=150 >
	  <col width=*>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 구매대행 상품 URL : </td>
	    <td class='input_box_item' colspan=3><input type=text class='textbox' name='bs_url' value='' style='width:430px;'> <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 구매대행 사이트 : </td>
	    <td class='input_box_item' colspan=3>".getBuyingServiceSiteInfo($bs_site)."
	    <!--select name='bs_site' style='font-size:12px;'>
	    	<option value='' >구매대행 사이트</option>
			<option value='izabel' >www.izabel.co.kr(메이크샵)</option>
	    	<option value='saksfifthavenue' >www.saksfifthavenue.com </option>
	    	<option value='bloomingdales' >www1.bloomingdales.com</option>
	    	<option value='barneys' >www.barneys.com</option>
	    	<option value='macys' >www1.macys.com</option>
	    	<option value='nordstrom' >shop.nordstrom.com</option>
	    	<option value='gymboree'>www.gymboree.com (짐보리)</option>
	    	<option value='polo' >www.ralphlauren.com (폴로)</option>
	    	<option value='onestepahead' >www.onestepahead.com (원스텝퍼헤드)</option>
	    	<option value='csnstores' >www.csnstores.com (씨에앤스토어)</option>
	    	http://www.saksfifthavenue.com
	    </select-->
	    <span class=small></span>
	    <div id='organization_img_area' ></div>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff  height=30>
	    <td class='input_box_title'> 통관타입 : </td>
	    <td class='input_box_item' colspan=3>
	    	<input type=radio name='clearance_type' id='clearance_type_1' value='1' ><label for='clearance_type_1'>목록통관</label>
	    	<input type=radio name='clearance_type' id='clearance_type_0' value='0' checked><label for='clearance_type_0'>일반통관</label>
			<input type=radio name='clearance_type' id='clearance_type_9' value='9'><label for='clearance_type_9'>국내배송</label>
	    	<!--table>
		    <tr align=center>
		    	<td width=120>Orgin원가</td>
		    	<td width=90>환율($exchange_rate 원)</td>
		    	<td width=90>관세($bs_duty %)</td>
		    	<td>통관수수료($clearance_fee 원)</td>
		    </tr>
		    <tr align=center>
		    	<td><input type=text class='textbox' name='bs_orgin_price' value='' style='width:100px;text-align:right;padding-right:3px;'> $ + </td>
		    	<td><input type=text class='textbox' name='exchange_rate' value='' style='width:80px;text-align:right;padding-right:3px;'> 원 + </td>
		    	<td><input type=text class='textbox' name='bs_duty' value='' style='width:80px;text-align:right;padding-right:3px;'> + </td>
		    	<td><input type=text class='textbox' name='clearance_fee' id='clearance_fee' value='' style='width:80px;text-align:right;padding-right:3px;'> 원 </td>
		    </tr>
		    </table-->
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 항공 운송료 : </td>
	    <td class='input_box_item' colspan=3>
	    	<table width=160>
		    <tr align=center>
		    	<td width=130>예상무게</td>
		    	<!--td>항공운송료</td-->
		    </tr>
		    <tr>
		    	<td><input type=text class='textbox' name='bs_air_wt' id='bs_air_wt'  value='1' style='width:100px;text-align:right;padding-right:3px;'> 파운드</td>
		    	<!--td><input type=text class='textbox' name='bs_air_shipping' id='bs_air_shipping' value='' style='width:100px;text-align:right;padding-right:3px;'> $ </td-->
		    </tr>
		    </table>
	    </td>
		<!--td class='input_box_title'> 환율타입</td>
		<td class='input_box_item' >
			".getBuyingServiceCurrencyInfo($currency_ix)."

			<span class=small></span>
			<div id='organization_img_area' ></div>
		</td-->
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 구매대행 수수료 : </td>
	    <td class='input_box_item'>
	    <table>
		    <tr align=center>
		    	<td width=130>수수료율</td>
		    	<!--td>구매대행 수수료</td-->
		    </tr>
		    <tr>
		    	<td><input type=text class='textbox' name='bs_fee_rate' value='10' style='width:100px;text-align:right;padding-right:3px;'> %</td>
		    	<!--td><input type=text class='textbox' name='bs_fee' id='bs_fee' value='' style='width:100px;text-align:right;padding-right:3px;'></td-->
		    </tr>
		    </table>
	     <span class=small></span></td>
			<td class='input_box_title'><label for='usable_round'>가격반올림</label><input type='checkbox' name='usable_round' id='usable_round' value='Y' onclick='UsableRound(this)'></td>
			<td class='input_box_item' >
			<select name='precision' id='precision' disabled>
				<!--option value=''>반올림단위</option-->
				<option value='2'>100자리</option>
				<option value='3'>1000자리</option>
				<option value='4'>10000자리</option>
			</select>
				<!--input type='radio' name='round_type' id='round_type_0' value='0' checked><label for='round_type_0'>반올림없음</label-->
				<input type='radio' name='round_type' id='round_type_1' value='round' disabled checked><label for='round_type_1'>반올림</label>
				<input type='radio' name='round_type' id='round_type_2' value='floor' disabled><label for='round_type_2'>버림</label>
			</td>
	  </tr>
	  <!--tr bgcolor=#ffffff >
	    <td> 상품구매시 적립금 : </td><td>구매액의 <input type=text class='textbox' name='sale_rate' value='".$db->dt[sale_rate]."' style='width:30px;'> % 적립금으로 적립합니다 <span class=small></span></td>
	    <td align=left colspan=2></td>
	  </tr>
	  <tr height=1><td colspan=4 class='dot-x'></td></tr>
	  <tr bgcolor=#ffffff >
	    <td> 사용유무 : </td>
	    <td>
	    	<input type=radio name='disp' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' value='0' ><label for='disp_0'>사용하지않음</label>
	    </td>
	    <td align=left colspan=2></td>
	  </tr>
	  <tr height=1><td colspan=4 class='dot-x'></td></tr-->
	  </table>";

$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding-left:10px;' class=small>
		  <u>구매대행 상품으로 등록하실 상품 URL 과 구매대행 사이트를 선택해 주세요</u>
	</td>
</tr>
</table>
";




$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../image/btn_buyservice_goods_get.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";


$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='buyingSrvicefrm' action='buyingService.act.php' method='post' onsubmit='return CheckBuyingService(this);' target='act'>
<input name='act' type='hidden' value='insert'>
<input name='gp_ix' type='hidden' value=''>
<input name='basic' type='hidden' value=''>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";

$Contents = $Contents."</table >";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원 그룹정보는 위와 같이 9단계의 등급으로 이루어져 있으며 '그룹등급' 은 수정이 불가능하며 각 등급에 맞는 명칭을 변경해서 사용하셔야 합니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사용하지 않으실 회원그룹정보는 수정버튼을 클릭하신후 사용하지 않음으로 선택하신후 저장 버튼을 누르시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사용유무가 사용으로 되어 있는 회원그룹만 사용하실수 있게 됩니다</td></tr>
</table>
";


$Contents .= HelpBox("구매대행 상품관리", $help_text);

 $Script = "
 <script language='javascript'>
function UsableRound(obj){
	//alert(obj.checked);
	if(obj.checked){
		$('#precision').attr('disabled',false);
		$('input[name=round_type]').attr('disabled',false);
	}else{
		$('#precision').attr('disabled',true);
		$('input[name=round_type]').attr('disabled',true);
	}
}

 function CheckBuyingService(frm){
 	if(frm.bs_site.value.length < 1){
		alert(language_data['buyingService.php']['A'][language]);
		//'구매대행 사이트를 지정해주세요'
		frm.bs_site.focus();
		//document.getElementById('save_loading').style.display = 'none';
		//obj.innerHTML = \"\";
		return false;
	}

	if(frm.bs_url.value.length < 1){
		alert(language_data['buyingService.php']['B'][language]);
		//'구매대행 상품 URL 을 입력해주세요 '
		frm.bs_url.focus();
		//document.getElementById('save_loading').style.display = 'none';
		//obj.innerHTML = \"\";
		return false;
	}

	if(frm.bs_url.value.indexOf(frm.bs_site.value) == -1){
		alert(language_data['buyingService.php']['C'][language]);
		//'구매대행 상품 URL 선택하신 구매대행 사이트와 맞는지 다시 한번 확인해주세요'
		frm.list_url.focus();
		//document.getElementById('save_loading').style.display = 'none';
		//obj.innerHTML = \"\";
		return false;
	}

	return true;
}
 </script>
 ";

if($mmode == "pop"){

	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->NaviTitle = "구매대행 상품관리";
	$P->Navigation = "HOME > 상품관리 > 구매대행 상품관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->Navigation = "HOME > 상품관리 > 구매대행 상품관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


/*
create table shop_buyingservice_url_info (
	bsui_ix int(4) unsigned not null auto_increment  ,
	cid varchar(15)  NOT NULL COMMENT '상품카테고리' ,
	bs_site varchar(100) NOT NULL COMMENT '구매대행사이트코드',
	bs_list_url varchar(256) null default null COMMENT '구매대행 사이트 리스트 URL ' ,
	bs_list_url_md5 varchar(32) null default null COMMENT '구매대행 사이트 리스트 URL 키값' ,
	orgin_category_info varchar(255)  NULL COMMENT '구매대행사이트 카테고리 정보',
	disp char(1) default '1' ,
	regdate datetime not null,
primary key(bsui_ix));


create table shop_buyingservice_info (
bsi_ix int(4) unsigned not null auto_increment  ,
exchange_type enum('USD','KRW') null default 'USD',
exchange_rate int(2)  default '9' ,
bs_tax int(8) null default null,
bs_supertax_rate float null default null,
bs_orgin_shipping int(8) null default 0,
bs_air_shipping int(8) null default 0,
bs_packing_fee int(8) null default 0,
bs_duty int(8) null default 0,
disp char(1) default '1' ,
regdate datetime not null,
primary key(bsi_ix));

create table shop_buyingservice_info (
bsi_ix int(4) unsigned not null auto_increment  ,
exchange_type enum('USD','KRW') null default 'USD',
exchange_rate int(2)  default '9' ,
bs_duty float null default 0,
bs_supertax_rate float null default null,
bs_fee int(8) null default null,
disp char(1) default '1' ,
regdate datetime not null,
primary key(bsi_ix));

*/
?>