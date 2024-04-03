<?
include("../class/layout.class");
include_once("buyingService.lib.php");


$db = new Database;

$sql = "select * from shop_buyingservice_info order by regdate desc limit 1 ";

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


//print_r($currencys);

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	  <tr >
		<td align='left' colspan=6 > ".GetTitleNavigation("구매대행 화폐타입 관리", "상품관리 > 구매대행 화폐타입 관리 ")."</td>
	  </tr>
	  
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>구매대행 상품등록</b></div>")."</td>
	  </tr>
	  </table>
	  
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box'>
	  <col width=150 >
	  <col width=*>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 화폐타입 이름 : </td>
	    <td class='input_box_item' colspan=3><input type=text class='textbox' name='currency_type_name' validation=true title='화폐타입 이름' value='' style='width:230px;'> <span class=small></span></td>
	  </tr>
	   <tr bgcolor=#ffffff >
	    <td class='input_box_title' > 화폐타입 : </td>
	    <td class='input_box_item'>
		   <select name='basic_currency' id='basic_currency' validation=true title='기본통화' >
			   <option value=''>기본통화</option>";
			   for($i=0; $i < count($currencys);$i++){
				   //if($admin_config["currency_unit"] != $currencys[$i][currency_type]){
						$Contents01 .= "<option value='".$currencys[$i][currency_type]."' ".CompareReturnValue($currencys[$i][currency_type],$exchange_type[0],"selected").">".$currencys[$i][currency_type_name]."</option>";
				   //}
			   }
				$Contents01 .= "
			   
		   </select>
		   - <b class=blk>".$admin_config["currency_unit"]."</b><input type=hidden name='price_currency' value='".$admin_config["currency_unit"]."' >

		</td>
	    <td class='input_box_title'> 사용유무 : </td>
	    <td class='input_box_item' >
	    	<input type=radio name='disp' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' value='0' ><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>
	  </table>";

$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding-left:10px;' class=small>
		  <u>구매대행  상품에 적용할 화폐 타입의 이름과 화폐 타입을 관리합니다</u>
	</td>
</tr>
</table>
";




$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";


$Contents ="<form name='buyingSrvicefrm' action='buyingServiceInfo.act.php' method='post' onsubmit='return CheckFormValue(this);' target='act'>
<input name='act' type='hidden' value='currency_type_add'>
<input name='gp_ix' type='hidden' value=''>
<input name='is_basic' type='hidden' value='N'>";
$Contents = $Contents. "<table width='100%' border=0 width=500>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."</table >";
$Contents = $Contents."</form>";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사용하지 않으실 회원그룹정보는 수정버튼을 클릭하신후 사용하지 않음으로 선택하신후 저장 버튼을 누르시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사용유무가 사용으로 되어 있는 회원그룹만 사용하실수 있게 됩니다</td></tr>
</table>
";


$Contents .= HelpBox("구매대행 화폐타입 관리", $help_text);

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
 	if(frm.currency_type_name.value.length < 1){
		alert('화폐타입 이름을 입력해주세요 ');
		frm.bs_site.focus();
		
		return false;
	}


	return true;
}
 </script>
 ";

if($mmode == "pop" ){

	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->NaviTitle = "구매대행 화폐타입 관리";
	$P->Navigation = "HOME > 상품관리 > 구매대행 화폐타입 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->title = "구매대행 화폐타입 관리";
	$P->Navigation = "HOME > 상품관리 > 구매대행 화폐타입 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


/*

create table shop_buyingservice_currencytype_info (
	currency_ix int(4) unsigned not null auto_increment  ,
	cid varchar(15)  NOT NULL COMMENT '상품카테고리' ,
	currency_type_name varchar(100) NOT NULL COMMENT '화폐타입이름',
	basic_currency varchar(256) null default null COMMENT '기본통화' ,
	price_currency varchar(32) null default null COMMENT '가격통화' ,
	is_basic enum('Y','N') default 'N' COMMENT '기본통화타입' ,
	disp char(1) default '1' COMMENT '사용여부',
	regdate datetime not null,
primary key(currency_ix));



*/
?>