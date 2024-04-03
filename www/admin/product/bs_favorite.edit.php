<?
include("../class/layout.class");
include_once("buyingService.lib.php");

$db = new Database;


$sql = "select bsui.* , ci.cid, ci.cname , ci.depth 
			from shop_buyingservice_url_info bsui 
			left join shop_category_info ci on bsui.cid = ci.cid  
			where bsui_ix = '".$bsui_ix."'
			";

$db->query("$sql "); //where uid = '$code'

if($db->total){
	$db->fetch();
	$bs_favoriteInfo = $db->dt;
	$act = "update";
}else{
	$act = "insert";
}



$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
			<td align='left' colspan=4> ".GetTitleNavigation("즐겨찾기 정보 수정", "상품관리 > 즐겨찾기 정보 수정 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>구매대행 즐겨찾기 정보수정</b></div>")."</td>
	  </tr>
	 
	  </table>
	  <form name='bs_favorite_form' method='post' action='bs_favorite.act.php' onsubmit='return CheckFormValue(this);' target='act' style='display:inline;'>
	<input type='hidden' name='act' value='".$act."'>
	<input type='hidden' name='cid2' value='".$bs_favoriteInfo[cid]."'>
	<input type='hidden' name='depth' value='".$bs_favoriteInfo[depth]."'>
	<input type='hidden' name='bsui_ix' value='$bsui_ix'>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	<tr>
		<td colspan=2>
			
			<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='input_table_box'>
				<col width='25%' />
				<col width='30%' />
				<col width='25%' />
				<col width='30%' />
				<tr>
					<td class='input_box_title'>  Orgin 카테고리 정보 </td>
					<td class='input_box_item' colspan=3 >
						<input type=text class=textbox name='orgin_category_info' style='width:97%;' value='".str_replace("'","&#39;",str_replace("\t"," ",trim($bs_favoriteInfo[orgin_category_info])))."' >
					</td>
				</tr>
				<tr bgcolor=#ffffff >
					<td class='input_box_title'> 환율타입  </td>
					<td class='input_box_item' colspan=3>
						".getBuyingServiceCurrencyInfo($bs_favoriteInfo[currency_ix])."			
					</td>					
				 </tr>
				<tr>
					<td class='input_box_title'>카테고리선택</td>
					<td class='input_box_item' colspan=3 >
						<table border=0 cellpadding=0 cellspacing=0>
							<tr>
								<td style='padding-right:5px;'>".getCategoryList3("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $bs_favoriteInfo[cid])."</td>
								<td style='padding-right:5px;'>".getCategoryList3("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $bs_favoriteInfo[cid])."</td>
								<td style='padding-right:5px;'>".getCategoryList3("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $bs_favoriteInfo[cid])."</td>
								<td>".getCategoryList3("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $bs_favoriteInfo[cid])."</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr bgcolor=#ffffff >
					<td class='input_box_title'> 구매대행 사이트  </td>
					<td class='input_box_item' colspan=3>
						".getBuyingServiceSiteInfo($bs_favoriteInfo[bs_site])."			
					</td>					
				</tr>
				<tr>
						<td class='input_box_title'>  상품리스트 URL  </td>
						<td class='input_box_item' colspan=3>
							<input type=text class=textbox name='bs_list_url' style='width:97%;' value='".trim($bs_favoriteInfo[bs_list_url])."' >
						</td>
				</tr>
				<tr>
						<td class='input_box_title'>사용여부</td>
						<td class='input_box_item' colspan=3>
							<input type=radio name='disp' id='disp_1' value='1' ".($bs_favoriteInfo[disp] == "1" ? "checked":"")."><label for='disp_1'>사용</label>
	    					<input type=radio name='disp' id='disp_0' value='0' ".($bs_favoriteInfo[disp] == "0" ? "checked":"")."><label for='disp_0'>사용하지않음</label>
						</td>
				</tr>
				";

$Contents01 .=	"				
				
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
	</tr>
	  </table>
	  </form>";

//$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');
/*
$Contents02 = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'  class='list_table_box'><!--style='table-layout:fixed;'-->
		<col width=5%>
		<col width=30%>
		<col width=* >
		<col width=9%>
		<col width=7%>
		<col width=7% >
		<col width=9%>
	  <tr bgcolor=#efefef align=center height=25>
			<td class='s_td' rowspan=2 nowrap>번호 </td>
			<td class='m_td' >카테고리</td>
			<td class='m_td' >Orgin 카테고리 정보</td>
			<td class='m_td' align='center' >환율타입</td>
			<td class='m_td' align='center' nowrap>사이트 구분</td>
			<td class='m_td' >사용여부 </td>
			<td class='e_td' >관리 </td>
		</tr>
		<tr height=25>
			<td class='m_td' colspan=4>상품리스트 URL </td>
			<td class='m_td' colspan=2>등록일자</td>
		</tr>";



if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;

		$Contents02 .= "<tr align=center height=30>
				<td class='list_box_td list_bg_gray' rowspan=2>".$no." </td>
				<td class='list_box_td point' style='text-align:left;'>".getCategoryPathByAdmin($db->dt[cid], 4)."</td>
				<td class='list_box_td point' style='padding:0px;' nowrap>
					<input type=text name='orgin_category_info' style='vertical-align:bottom;border:0px;width:98%;background-color:transparent;' value='".str_replace("'","&#39;",str_replace("\t"," ",trim($db->dt[orgin_category_info])))."' onfocus=\"$(this).parent().css('border','2px solid gray')\" onfocusout=\"$(this).parent().css('border','0px');UpdateOrginCategoryInfo('".$db->dt[bsui_ix]."','".$db->dt[orgin_category_info]."', $(this).val())\">
				</td>
				<td class='list_box_td list_bg_gray'>".getBuyingServiceCurrencyInfo($db->dt[currency_ix],"text")."</td>
				<td class='list_box_td list_bg_gray'>".$db->dt[bs_site]."</td>
				<td class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ? "사용함":"사용안함")."</td>
				<td bgcolor='#efefef'>
				<a href=\"javascript:PoPWindow3('../product/bs_favorite.edit.php?mmode=pop',760,700,'bs_favorite_edit')\"'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
				<a href=\"javascript:DeleteFavoriteInfo('".$db->dt[bsui_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a></td>
			</tr>
			<tr height=25>
				<td colspan=4 title='".$db->dt[bs_list_url_md5]."'><a href='".$db->dt[bs_list_url]."' target=_blank>".$db->dt[bs_list_url]."</a></td>
				<td colspan=2 align=center>".$db->dt[regdate]."</td>
			</tr>";
	}
	$Contents02 .= "";
}else{
		$Contents02 .= "
			<tr height=60><td colspan=6 align=center>구매대행 환율/수수료  정보가 없습니다.</td></tr>";

}


$Contents02 .= "</table>";
$Contents02 .= "<ul class='paging_area' >
						<li class='front'></li>
						<li class='back'>".page_bar($total, $page, $max,"&cid2=$cid2&depth=$depth&search_type=$search_type&search_text=$search_text&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD","")."</li>
					  </ul>";
*/


$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";

$Contents = $Contents."</table >";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >Orgin 카테고리 정보 변경을 원하시면 해당 필드를 클릭후 수정하시면 저장됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록된 즐겨찾기 정보 수정은 상품 스크래핑 화면에서 자동으로 조회 될수 있습니다.</td></tr>
</table>
";
//$help_text =  getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');

$Contents .= HelpBox("즐겨찾기 정보 수정", $help_text)."<br>";

 $Script = "
 <script language='javascript'>
 
 function UpdateOrginCategoryInfo(bsui_ix, before_orgin_category_info, change_orgin_category_info){
//alert(($.trim(before_orgin_category_info) == $.trim(change_orgin_category_info)));
if(!($.trim(before_orgin_category_info) == $.trim(change_orgin_category_info))){
	$.ajax({ 
		type: 'GET', 
		data: {'act': 'orgin_category_info_update','bsui_ix': bsui_ix,'orgin_category_info': change_orgin_category_info},
		url: 'bs_favorite.act.php',  
		dataType: 'html', 
		async: true, 
		beforeSend: function(){ 
			$.blockUI.defaults.css = {}; 
			$.blockUI({ message: $('#loading'), css: { width: '100px' , height: '100px' ,padding:  '10px'} });  
		},  
		success: function(data){ 
			//alert(data);
			$.unblockUI();
			//alert('카테고리 정보가 정상적으로 처리 되었습니다.');
			//alert($('#'+obj_id).parent().html());
			//alert($('#'+obj_id).clone().wrapAll('<div/>').parent().html());


		} 
	}); 
}
}

 function CheckBsInfo(frm){

 	if(frm.exchange_rate.value == frm.b_exchange_rate.value && frm.bs_duty.value == frm.b_bs_duty.value && frm.bs_supertax_rate.value == frm.b_bs_supertax_rate.value && frm.bs_basic_air_shipping.value == frm.b_bs_basic_air_shipping.value && frm.bs_add_air_shipping.value == frm.b_bs_add_air_shipping.value && frm.clearance_fee.value == frm.b_clearance_fee.value){
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


 function DeleteFavoriteInfo(bsui_ix){
 	if(confirm('구매대행 즐겨찾기 정보 수정을 정말로 삭제하시겠습니까?')){//'해당 구매대행 환율/수수료 정보를 정말로 삭제하시겠습니까?'
 	//	var frm = document.group_frm;
 	//	frm.act.value = act;
 	//	frm.gp_ix.value = gp_ix;
 	//	frm.submit();

 		f    = document.createElement('form');
    f.name = 'bsform';
    f.id = 'bsform';
    f.method    = 'post';
    f.target = 'iframe_act';
    f.action    = 'bs_favorite.act.php';

    i0          = document.createElement('input');
    i0.type     = 'hidden';
    i0.name     = 'act';
    i0.id     = 'act';
    i0.value    = 'delete';
    f.insertBefore(i0);

    i1          = document.createElement('input');
    i1.type     = 'hidden';
    i1.name     = 'bsui_ix';
    i1.id     = 'bsui_ix';
    i1.value    = bsui_ix;
    f.insertBefore(i1);

		document.insertBefore(f);
		f.submit();

 	}
}
 </script>
 <script Language='JavaScript' type='text/javascript'>
	function loadCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.depth;//kbk
		var depth = sel.getAttribute('depth');
		//dynamic.src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		//document.getElementById('act').src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
	function loadChangeCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.depth;//kbk
		var depth = sel.getAttribute('depth');

		//dynamic.src = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;//kbk
		//document.getElementById('act').src = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		window.frames['act'].location.href = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
	</script>
 ";

if($mmode == "pop"){

	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->NaviTitle = "즐겨찾기 정보 수정";
	$P->Navigation = "상품관리 > 구매대행 > 즐겨찾기 정보 수정";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->title = "즐겨찾기 정보 수정";
	$P->Navigation = "상품관리 > 구매대행 > 즐겨찾기 정보 수정";
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

*/
?>