<?
include("../class/layout.class");

$db = new Database;
/*
$db2 = new Database;

$sql = "select id from shop_product where product_type = 1 ";
$db->query ($sql);

for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$db2->query ("update shop_product_buyingservice_priceinfo set bs_use_yn = '1' where pid ='".$db->dt[id]."' limit 1");
}
echo "처리완료";
exit;
*/
//print_r($admin_config);

if($currency_ix == ""){
	$sql = "select * from shop_buyingservice_currencytype_info order by regdate desc  ";
}else{
	$sql = "select * from shop_buyingservice_currencytype_info where currency_ix = '".$currency_ix."' order by regdate desc limit 0,1 ";
}
//echo $sql;
$db->query ($sql);
$currency_kinds = $db->fetchall();
$currency_type_info = $currency_kinds[0];
//print_r($currency_kinds);

$sql = "select count(*) as total
				from shop_product p, shop_product_buyingservice_priceinfo pbp
				where p.id = pbp.pid and product_type = '1' and bs_use_yn = '1' and price_policy = 'N' and currency_ix = '".$currency_type_info[currency_ix]."'   "; //and p.id = '044045' limit 1 and state = '1'
//echo nl2br($sql);
$db->query ($sql);
$db->fetch();
$goods_total = $db->dt[total];


$sql = "select * from shop_buyingservice_info where exchange_type = '".$currency_type_info[currency_ix]."' order by regdate desc limit 0,1 ";

$db->query ($sql);

if($db->total){
	$db->fetch();

	$exchange_type = $db->dt[exchange_type];
	$exchange_rate = $db->dt[exchange_rate];
	$exchange_type = str_split("-",$db->dt[exchange_type]);
	//$exchange_type = split("-",$db->dt[exchange_type]);
	$bs_basic_air_shipping = $db->dt[bs_basic_air_shipping];
	$bs_add_air_shipping = $db->dt[bs_add_air_shipping];

	$bs_duty = $db->dt[bs_duty];
	$bs_supertax_rate = $db->dt[bs_supertax_rate];
	$clearance_fee = $db->dt[clearance_fee];
	$bs_fee_rate = $db->dt[bs_fee_rate];

	$usable_round = $db->dt[usable_round];
	$round_precision = $db->dt[round_precision];
	$round_type = $db->dt[round_type];
}



$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
			<td align='left' colspan=4> ".GetTitleNavigation("구매대행 환율/수수료 관리", "상품관리 > 구매대행 환율/수수료 관리 ")."</td>
	  </tr>
	  <tr>
			<td align='left' colspan=4 style='padding-bottom:11px;'>
				<div class='tab'>
						<table class='s_org_tab'>
						<tr>
							<td class='tab'>";
if($mmode == "pop"){
$Contents01 .= "
								<table id='tab_01'  >
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='buyingService.php?mmode=pop'\">구매대행 상품 등록</td>
									<th class='box_03'></th>
								</tr>
								</table>";
}else{
$Contents01 .= "
								<table id='tab_01'  >
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='product_bsgoods.php?bsmode=reg'\">구매대행 상품 등록</td>
									<th class='box_03'></th>
								</tr>
								</table>
								<table id='tab_02' >
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='product_bsgoods.php?bsmode=list'\">구매대행 상품 리스트</td>
									<th class='box_03'></th>
								</tr>
								</table>";
}
$Contents01 .= "
								<table id='tab_03' class='on'>
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='buyingServiceInfo.php?mmode=".$_GET["mmode"]."&currency_ix=".$_GET["currency_ix"]."'\">구매대행 환율/수수료 관리</td>
									<th class='box_03'></th>
								</tr>
								</table>";
$Contents01 .= "
							</td>
							<td class='btn' style='padding:10px 0px 0px 10px;'>
								<!--a href='./currencyType.add.php?mmode=pop&x=".rand()."' rel='facebox' >구매대행 환율 타입등록</a-->
							</td>
						</tr>
						</table>
					</div>
			</td>
		</tr>
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>구매대행 환율정보 관리 - ".$currency_type_info["currency_type_name"]."(".$currency_type_info[basic_currency]."-".$currency_type_info[price_currency].")</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <col width=15%>
	  <col width=35%>
	  <col width=15%>
	  <col width=35%>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title' > 화폐타입 : </td>
	    <td class='input_box_item'>
		   <b class=blk>".$currency_type_info["currency_type_name"]."(".$currency_type_info[basic_currency]."-".$currency_type_info[price_currency].")</b><input type=hidden name='exchange_type' value='".$currency_type_info[currency_ix]."' >
		   <!--select name='exchange_type' id='exchange_type'>
			   <option value=''>화폐타입</option>";
			   for($i=0; $i < count($currencys);$i++){
						$Contents01 .= "<option value='".$currencys[$i][currency_type]."' ".CompareReturnValue($currencys[$i][currency_type],$exchange_type[0],"selected").">".$currencys[$i][currency_type_name]."</option>";

			   }
			   $Contents01 .= "

		   </select>
		   - <b class=blk>".$admin_config["currency_unit"]."</b><input type=hidden name='currency_unit' value='".$admin_config["currency_unit"]."' -->

		</td>
	    <td class='input_box_title' > 환율 : </td>
	    <td class='input_box_item'>
			<input type=hidden name='b_exchange_rate' value='$exchange_rate' >
			<input type=text class='textbox' name='exchange_rate' value='$exchange_rate' style='width:100px;text-align:right;padding:2 3 0 0;'> 원 <span class=small></span>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 부가세 : </td>
	    <td class='input_box_item'>
			<input type=hidden name='b_bs_supertax_rate' value='$bs_supertax_rate' >
			<input type=text class='textbox' name='bs_supertax_rate' value='$bs_supertax_rate' style='width:100px;text-align:right;padding:2 3 0 0;'> %
			<span class=small></span>
		</td>
	    <td class='input_box_title'> 통관수수료 : </td>
	    <td class='input_box_item'><input type=hidden name='b_clearance_fee' value='$clearance_fee' ><input type=text class='textbox' name='clearance_fee' value='$clearance_fee' style='width:100px;text-align:right;padding:2 3 0 0;'> 원 <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 항공 운송료 : </td>
	    <td class='input_box_item' style='padding:5px;'>
		    <table>
			    <tr>
			    	<td>기본1파운드비용</td>
			    	<td>추가1파운드비용</td>
			    </tr>
			    <tr>
			    	<td><input type=hidden name='b_bs_basic_air_shipping' value='$bs_basic_air_shipping' ><input type=text class='textbox' name='bs_basic_air_shipping' value='$bs_basic_air_shipping' style='width:50px;text-align:right;padding:2px 3px 0 0;'> $ </td>
			    	<td><input type=hidden name='b_bs_add_air_shipping' value='$bs_add_air_shipping' ><input type=text class='textbox' name='bs_add_air_shipping' value='$bs_add_air_shipping' style='width:50px;text-align:right;padding:2px 3px 0 0;'> $ </td>
			    </tr>
		    </table>
	    </td>
		<td class='input_box_title'> 관세 : </td>
	    <td class='input_box_item'><input type=hidden name='b_bs_duty' value='$bs_duty' ><input type=text class='textbox' name='bs_duty' value='$bs_duty' style='width:100px;text-align:right;padding:2 3 0 0;'> % <span class=small></span></td>
	  </tr>
	  <tr>
		  <td class='input_box_title'> 구매대행수수료율</td>
		  <td class='input_box_item' colspan=3>
		  <input type=hidden name='b_bs_fee_rate' id='b_bs_fee_rate' value='".$bs_fee_rate."' >
		  <input type=text class='textbox' name='bs_fee_rate' value='".$bs_fee_rate."' style='width:100px;text-align:right;padding-right:3px;'> %
		  </td>
	  </tr>
	  <tr>
			<td class='input_box_title'><label for='usable_round'>가격반올림</label><input type='checkbox' name='usable_round' id='usable_round' value='Y' onclick='UsableRound(this)' ".($usable_round == "Y" ? "checked":"")."></td>
			<td class='input_box_item'><input type=hidden name='b_round_precision' id='b_round_precision'  value='".$round_precision."' >
				<select name='round_precision' id='round_precision' ".($usable_round == "N" ? "disabled":"").">
					<option value='2' ".($round_precision == "2" ? "selected":"").">100자리</option>
					<option value='3' ".($round_precision == "3" ? "selected":"").">1000자리</option>
					<option value='4' ".($round_precision == "4" ? "selected":"").">10000자리</option>
				</select>
				<input type=hidden name='b_round_type' id='b_round_type' value='".$round_type."' >
				<input type='radio' name='round_type' id='round_type_1' value='round' ".($usable_round == "N" ? "disabled":"")." ".(($round_type == "round" || $round_type == "") ? "checked":"")."><label for='round_type_1'>반올림</label>
				<input type='radio' name='round_type' id='round_type_2' value='floor' ".($usable_round == "N" ? "disabled":"")."  ".($round_type == "floor" ? "checked":"")."><label for='round_type_2'>버림</label>
			</td>
			<td class='input_box_title'> 적용상품수 : </td>
			<td class='input_box_item'><b>".$goods_total."개</b> 상품에 적용 되어 있음</td>
		</tr>
	  <!--tr bgcolor=#ffffff >
	    <td class='input_box_title'> 상품구매시 적립금 : </td><td>구매액의 <input type=text class='textbox' name='sale_rate' value='".$db->dt[sale_rate]."' style='width:30px;'> % 적립금으로 적립합니다 <span class=small></span></td>
	    <td class='input_box_item' colspan=3></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 사용유무 : </td>
	    <td class='input_box_item' colspan=3>
	    	<input type=radio name='disp' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' value='0' ><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr-->
	  </table>";
/*
$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small>
		  <u>구매대행 상품 가격에 공통으로 반영되는 가격 정보입니다.</u>
	</td>
</tr>
</table>
";*/
$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents02 = "
	<table table width='100%' cellpadding=0 cellspacing=0 border='0'>
	<tr>
			<td align='left' colspan=4 style='padding-bottom:15px;'>
				<div class='tab'>
						<table class='s_org_tab'>
						<tr>
							<td class='tab'>";

$sql = "select * from shop_buyingservice_currencytype_info where disp = '1' order by regdate desc  ";

//echo $sql;
$db->query ($sql);
$currency_kinds = $db->fetchall();

if(count($currency_kinds) < 3){
	for($i=0; $i < count($currency_kinds);$i++){

	$Contents02 .= "
									<table id='tab_03' ".(($currency_kinds[$i][currency_ix] == $_GET["currency_ix"] || $currency_kinds[$i][currency_ix] == $currency_type_info["currency_ix"]) ? "class='on'":"")." >
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='buyingServiceInfo.php?mmode=".$_GET["mmode"]."&currency_ix=".$currency_kinds[$i][currency_ix]."'\">".$currency_kinds[$i][currency_type_name]."(".$currency_kinds[$i][basic_currency]."-".$currency_kinds[$i][price_currency].")</td>
										<th class='box_03'></th>
									</tr>
									</table>";
	}
}else{


	$Contents02 .= "
									<table id='tab_03' class='on' >
									<tr>
										<th class='box_01'></th>
										<td class='box_02' style='padding:5px 0px 0px 0px;'>";
					$Contents02 .= "<select name='currency_ix' onchange=\"document.location.href='buyingServiceInfo.php?mmode=".$_GET["mmode"]."&currency_ix='+this.value\" style='border:1px solid silver;'>";
					for($i=0; $i < count($currency_kinds);$i++){
					$Contents02 .= "<option value='".$currency_kinds[$i][currency_ix]."' ".(($currency_kinds[$i][currency_ix] == $_GET["currency_ix"] || $currency_kinds[$i][currency_ix] == $currency_type_info["currency_ix"]) ? "selected":"").">".$currency_kinds[$i][currency_type_name]."(".$currency_kinds[$i][basic_currency]."-".$currency_kinds[$i][price_currency].")</option>";
					}
					$Contents02 .= "</select>
										</td>
										<th class='box_03'></th>
									</tr>
									</table>";

}
$Contents02 .= "
							</td>
							<td class='btn' style='padding:10px 0px 0px 10px;'>
								<a href=\"javascript:void(0);PoPWindow('./currencyType.add.php?mmode=pop&x=".rand()."',600,380,'currencyType')\">구매대행 환율 타입등록</a>
							</td>
						</tr>
						</table>
					</div>
			</td>
		</tr>

	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0'  class='list_table_box'>
		<col width=16%>
		<col width=*>
		<col width=7% >
		<col width=7%>
		<col width=7% >
		<col width=7%>
		<col width=11% >
		<col width=15%>
		<col width=15%>
		<col width=7%>
	  <tr bgcolor=#efefef align=center height=25>
			<td class='s_td' rowspan=2>날짜 </td>
			<td class='m_td' rowspan=2>환율</td>
			<td colspan=2 class='m_td' align='center' >항공수수료</td>
			<td class='m_td' rowspan=2>관세 </td>
			<td class='m_td' rowspan=2>부가세 </td>
			<td class='m_td' rowspan=2>통관수수료</td>
			<td class='m_td' rowspan=2>구매대행 수수료율</td>
			<td class='m_td' rowspan=2>가격반올림</td>
			<td class='e_td' rowspan=2>관리 </td>
		</tr>
		<tr height=25 align=center><td  class='m_td'>기본</td><td  class='m_td'>추가</td></tr>";


$max = 15; //페이지당 갯수

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}


$sql = "select count(*) as total from shop_buyingservice_info  where exchange_type = '".$currency_type_info[currency_ix]."'  ";

$db->query($sql);

$db->fetch();
$total = $db->dt[total];


$sql = "select * from shop_buyingservice_info where exchange_type = '".$currency_type_info[currency_ix]."'  order by regdate desc limit $start, $max ";
$db->query("$sql "); //where uid = '$code'


if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);

		if($db->dt[round_type] == "round"){
			$round_type = "반올림";
		}else if($db->dt[round_type] == "floor"){
			$round_type = "버림";
		}

		if($db->dt[round_precision] > 0){
			$pre_precision_text = "(1".str_repeat("0",$db->dt[round_precision])."자리 ".$round_type.")";
		}else{
			$pre_precision_text = "";
		}

		$Contents02 .= "<tr align=center height=30 ".($i == 0 ? "style='font-weight:bold;'":"").">
				<td class='list_box_td list_bg_gray".($i == 0 ? " blk":"")."'>".$db->dt[regdate]." </td>
				<td class='list_box_td point".($i == 0 ? " blk":"")."'>".number_format($db->dt[exchange_rate],2)." 원</td>
				<td class='list_box_td list_bg_gray".($i == 0 ? " blk":"")."'>".number_format($db->dt[bs_basic_air_shipping])." $</td>
				<td class='list_box_td list_bg_gray".($i == 0 ? " blk":"")."'>".number_format($db->dt[bs_add_air_shipping])." $</td>
				<td class='list_box_td".($i == 0 ? " blk":"")."'>".$db->dt[bs_duty]." %</td>
				<td class='list_box_td list_bg_gray".($i == 0 ? " blk":"")."'>";

			$Contents02 .= number_format($db->dt[bs_supertax_rate]);

	$Contents02 .= " % </td>
				<td class='list_box_td".($i == 0 ? " blk":"")."'>".number_format($db->dt[clearance_fee])." 원</td>
				<td class='list_box_td".($i == 0 ? " blk":"")."'>".number_format($db->dt[bs_fee_rate])." %</td>
				<td class='list_box_td'>".($db->dt[usable_round] == "Y" ? "사용".$pre_precision_text."":"사용안함")." </td>
				<td class='list_box_td list_bg_gray".($i == 0 ? " blk":"")."'>".($i != 0 ? "<a href=\"javascript:DeleteBSInfo('".$db->dt[bsi_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>":"")."</td>
			</tr>";

			unset($pre_precision_text);
			unset($round_type);
	}
	$Contents02 .= "";
}else{
		$Contents02 .= "
			<tr height=60><td colspan=10 align=center>구매대행 환율/수수료  정보가 없습니다.</td></tr>";

}


$Contents02 .= "</table>";
$Contents02 .= "<ul class='paging_area' >
						<li class='front'></li>
						<li class='back'>".page_bar($total, $page, $max,"&code=$code&state=$state&search_type=$search_type&search_text=$search_text&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD","")."</li>
					  </ul>";



$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";


$Contents = "<form name='buyingSrvicefrm' action='buyingServiceInfo.act.php' onsubmit=\"return CheckBsInfo(this);\" method='post' target='act'><!--target='act'-->
<input name='act' type='hidden' value='insert'>
<!--input name='exchange_type' type='hidden' value='KRW'-->
<input name='basic' type='hidden' value=''>";
$Contents = $Contents."<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</table >";
$Contents = $Contents."</form>";
$Contents = $Contents."<table width='100%' border=0>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";
/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >구매대행 수수료 변경시 모든 구매대행 상품의 공급가가 자동으로 변경됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >구매대행 수수료는 최근입력한 수수료 기준으로 관리되게 됩니다.</td></tr>
</table>
";*/
$help_text =  getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');

$Contents .= HelpBox("구매대행 환율/수수료 관리", $help_text)."<br>";

 $Script = "
 <script language='javascript'>
function UsableRound(obj){
	//alert(obj.checked);
	if(obj.checked){
		$('#round_precision').attr('disabled',false);
		$('input[name=round_type]').attr('disabled',false);
	}else{
		$('#round_precision').attr('disabled',true);
		$('input[name=round_type]').attr('disabled',true);
	}
}

 function CheckBsInfo(frm){
	//alert($('#b_round_type').val()+':::'+$('input[name^=round_type]:checked').val());
 	if(frm.exchange_rate.value == frm.b_exchange_rate.value && frm.bs_duty.value == frm.b_bs_duty.value && frm.bs_supertax_rate.value == frm.b_bs_supertax_rate.value && frm.bs_basic_air_shipping.value == frm.b_bs_basic_air_shipping.value && frm.bs_add_air_shipping.value == frm.b_bs_add_air_shipping.value && frm.clearance_fee.value == frm.b_clearance_fee.value && $('#b_round_precision').val() == $('#round_precision').val() && $('#b_round_type').val() == $('input[name^=round_type]:checked').val() && $('#b_bs_fee_rate').val() == $('input[name^=bs_fee_rate]:checked').val()){
 		alert(language_data['buyingServiceInfo.php']['C'][language]);
		//'변경된 환율/수수료 정보가 없습니다. 변경된 정보가 없으면 저장이 되지 않습니다.'
 		return false;
 	}

 	if(confirm(language_data['buyingServiceInfo.php']['A'][language])){//'환율/수수료 정보가 변경되면 구매대행 상품 전체 가격이 재 산정되게됩니다. 환율/수수료 정보를 정말로 변경하시겠습니까? '
 		return true;
 	}else{
 		return false;
 	}
 }


 function DeleteBSInfo(bsi_ix){
 	if(confirm(language_data['buyingServiceInfo.php']['B'][language])){//'해당 구매대행 환율/수수료 정보를 정말로 삭제하시겠습니까?'
 	//	var frm = document.group_frm;
 	//	frm.act.value = act;
 	//	frm.gp_ix.value = gp_ix;
 	//	frm.submit();

 		f    = document.createElement('form');
    f.name = 'bsform';
    f.id = 'bsform';
    f.method    = 'post';
    f.target = 'act';
    f.action    = 'buyingServiceInfo.act.php';

    i0          = document.createElement('input');
    i0.type     = 'hidden';
    i0.name     = 'act';
    i0.id     = 'act';
    i0.value    = 'delete';
    f.insertBefore(i0);

    i1          = document.createElement('input');
    i1.type     = 'hidden';
    i1.name     = 'bsi_ix';
    i1.id     = 'bsi_ix';
    i1.value    = bsi_ix;
    f.insertBefore(i1);

		document.insertBefore(f);
		f.submit();

 	}
}
 </script>
 ";

if($mmode == "pop"){

	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->title = "환율/수수료 관리";
	$P->NaviTitle = "환율/수수료 관리";
	$P->Navigation = "상품관리 > 구매대행 > 환율/수수료 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->title = "환율/수수료 관리";
	$P->Navigation = "상품관리 > 구매대행 > 환율/수수료 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


/*

create table shop_buyingservice_info (
bsi_ix int(4) unsigned not null auto_increment  ,
exchange_type enum('USD','KRW') null default 'USD',
exchange_rate int(2)  default '9' ,

bs_tax int(8) null default null,
bs_orgin_shipping int(8) null default 0,
bs_air_shipping int(8) null default 0,
bs_packing_fee int(8) null default 0,
bs_tariff int(8) null default 0,
disp char(1) default '1' ,
regdate datetime not null,
primary key(gp_ix));
*/
?>