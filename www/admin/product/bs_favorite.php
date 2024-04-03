<?
include("../class/layout.class");
include_once("buyingService.lib.php");

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


$max = 20; //페이지당 갯수

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}

$where = "where bsui.bsui_ix is not null ";
if($cid2 != ""){
	$where .= " and bsui.cid LIKE '".substr($cid2,0,($depth+1)*3)."%' ";
}

if($search_text != ""){
	$where .= "and ".$search_type." LIKE '%".trim($search_text)."%' ";
}

if($bs_site != ""){
	$where .= "and bsui.bs_site = '".trim($bs_site)."' ";
}

$sql = "select count(*) as total from shop_buyingservice_url_info bsui  $where ";
//echo $sql;
$db->query($sql);

$db->fetch();
$total = $db->dt[total];


$sql = "select bsui.bsui_ix, bsui.bs_site, bsui.bs_list_url , bsui.bs_list_url_md5, bsui.orgin_category_info, bsui.currency_ix, bsui.disp, bsui.regdate,
			ci.cid, ci.cname , ci.depth 
			from shop_buyingservice_url_info bsui 
			left join shop_category_info ci on bsui.cid = ci.cid  
			$where
			order by bsui.regdate desc limit $start, $max ";
//echo nl2br($sql);

//
$db->query($sql); //where uid = '$code'
$db->fetch(0);
//print_r($db->dt);


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
			<td align='left' colspan=4> ".GetTitleNavigation("즐겨찾기 목록", "상품관리 > 즐겨찾기 목록 ")."</td>
	  </tr>
	  
	 
	  </table>
	  <form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' style='display:inline;'>
	<input type='hidden' name='mode' value='search'>
	<input type='hidden' name='cid2' value='$cid2'>
	<input type='hidden' name='depth' value='$depth'>
	<input type='hidden' name='bsmode' value='$bsmode'>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	<tr>
		<td colspan=2>
			
			<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='input_table_box'>
				<col width='20%' />
				<col width='30%' />
				<col width='20%' />
				<col width='30%' />
				<tr>
					<td class='input_box_title'>  선택된 카테고리  </td>
					<td class='input_box_item' colspan=3 ><b id='select_category_path1'>".($search_text == "" ? getCategoryPathByAdmin($cid2, $depth)."(".$total."개)":"<b style='color:red'>'$search_text'</b> <!--로 검색된 결과 입니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'O')." ")."</b></div></td>
				</tr>
				<tr>
					<td class='input_box_title'>카테고리선택</td>
					<td class='input_box_item' colspan=3 >
						<table border=0 cellpadding=0 cellspacing=0>
							<tr>
								<td style='padding-right:5px;'>".getCategoryList3("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid2)."</td>
								<td style='padding-right:5px;'>".getCategoryList3("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid2)."</td>
								<td style='padding-right:5px;'>".getCategoryList3("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid2)."</td>
								<td>".getCategoryList3("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $cid2)."</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr bgcolor=#ffffff >
				<td class='input_box_title'> 구매대행 사이트  </td>
				<td class='input_box_item'>
					".getBuyingServiceSiteInfo($bs_site)."
					
					<span class=small></span>
					<div id='organization_img_area' ></div>
				</td>
				<td class='input_box_title'>목록갯수</td>
					<td class='input_box_item'>
						<select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle><!-- onchange=\"document.frames['act'].location.href='".$HTTP_URL."?cid=$cid&depth=$depth&view=innerview&max='+this.value\"-->
							<option value='5' ".CompareReturnValue(5,$max).">5</option>
							<option value='10' ".CompareReturnValue(10,$max).">10</option>
							<option value='20' ".CompareReturnValue(20,$max).">20</option>
							<option value='50' ".CompareReturnValue(50,$max).">50</option>
							<option value='100' ".CompareReturnValue(100,$max).">100</option>
						</select> <span class='small'><!--한페이지에 보여질 갯수를 선택해주세요.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."</span>
					</td>
			  </tr>
			  <tr>
					<td class='input_box_title'>  검색어  </td>
					<td class='input_box_item' colspan=3>
						<table cellpadding=0 cellspacing=0 width=100%>
							<col width='130px'>
							<col width='*'>
							<tr>
								<td><select name='search_type'  style=\"font-size:12px;height:20px;\">
									<option value='orgin_category_info'>Orgin 카테고리 정보</option>
									<option value='bs_list_url'>상품리스트 URL</option>
									</select>
								</td>
								<td style='padding-left:5px;'>
								<INPUT id=search_texts  class='textbox' value='' style=' FONT-SIZE: 12px; WIDTH: 90%; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
								
								</td>
								<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'></span></td>
							</tr>
						</table>
					</td>
					
							</tr>
				";
				
$Contents01 .=	"				
				
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
	</tr>
	  </table>
	  </form>";

//$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

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
					<input type=text name='orgin_category_info' style='vertical-align:bottom;border:0px;width:98%;background-color:transparent;' value='".str_replace("'","&#39;",str_replace("\t"," ",trim($db->dt[orgin_category_info])))."' onfocus=\"$(this).parent().css('border','2px solid gray')\" onfocusout=\"$(this).parent().css('border','0px');UpdateOrginCategoryInfo('".$db->dt[bsui_ix]."','".str_replace("'","&#39;",str_replace("\t"," ",$db->dt[orgin_category_info]))."', $(this).val());\"  ondblclick=\"AutoUpdate('".$db->dt[cid]."','".$db->dt[bs_site]."','".$db->dt[bs_list_url]."');\">
				</td>
				<td class='list_box_td list_bg_gray'>".getBuyingServiceCurrencyInfo($db->dt[currency_ix],"text")."</td>
				<td class='list_box_td list_bg_gray'>".$db->dt[bs_site]."</td>
				<td class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ? "사용함":"사용안함")."</td>
				<td bgcolor='#efefef'>
				<a href=\"javascript:PoPWindow3('../product/bs_favorite.edit.php?mmode=pop&bsui_ix=".$db->dt[bsui_ix]."',800,600,'bs_favorite_edit')\"'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
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
			<tr height=60><td colspan=7 align=center>구매대행 즐겨찾기  정보가 없습니다.</td></tr>";

}


$Contents02 .= "</table>";

/*
$Contents02 .= "<ul class='paging_area' >
						<li class='front'></li>
						<li class='back'>".page_bar($total, $page, $max,"&cid2=$cid2&depth=$depth&search_type=$search_type&search_text=$search_text&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&bs_site=$bs_site","")."</li>
					  </ul>";
*/

$Contents02 .= "<table width=100%>
	<tr>
		<td>".page_bar($total, $page, $max,"&cid2=$cid2&depth=$depth&search_type=$search_type&search_text=$search_text&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&bs_site=$bs_site","")."</td>
		<td style='text-align:right;padding-top:10px;'><a href=\"javascript:PoPWindow3('../product/bs_favorite.edit.php?mmode=pop',900,600,'bs_favorite_edit')\"'>추가하기</a></td>
	</tr>
	</table>";




$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >Orgin 카테고리 정보 변경을 원하시면 해당 필드를 클릭후 수정하시면 저장됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록된 즐겨찾기 목록은 상품 스크래핑 화면에서 자동으로 조회 될수 있습니다.</td></tr>
</table>
";
//$help_text =  getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');

$Contents .= HelpBox("즐겨찾기 목록", $help_text)."<br>";

 $Script = "
 <script language='javascript'>
 function AutoUpdate(cid, bs_site, list_url){
	//alert(bs_site+':::'+list_url);
	$.ajax({ 
			type: 'GET', 
			data: {'bs_act': 'favorite_update','bs_favorite': '1','cid2': cid,'bs_site': bs_site,'list_url': list_url},
			url: 'product_bsgoods.act.php',  
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
 	if(confirm('구매대행 즐겨찾기 목록을 정말로 삭제하시겠습니까?')){//'해당 구매대행 환율/수수료 정보를 정말로 삭제하시겠습니까?'
 	//	var frm = document.group_frm;
 	//	frm.act.value = act;
 	//	frm.gp_ix.value = gp_ix;
 	//	frm.submit();

	$.ajax({ 
		type: 'GET', 
		data: {'act': 'ajax_delete','bsui_ix': bsui_ix},
		url: 'bs_favorite.act.php',  
		dataType: 'html', 
		async: true, 
		beforeSend: function(){  
		},  
		success: function(data){ 
			alert(data);
			document.location.reload();
			//$.unblockUI();
			//alert('카테고리 정보가 정상적으로 처리 되었습니다.');
			//alert($('#'+obj_id).parent().html());
			//alert($('#'+obj_id).clone().wrapAll('<div/>').parent().html());
		} 
	}); 

	/*
 	f    = document.createElement('form');
    f.name = 'bsform';
    f.id = 'bsform';
    f.method    = 'post'; 
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
	*/
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
	$P->NaviTitle = "즐겨찾기 목록";
	$P->Navigation = "상품관리 > 구매대행 > 즐겨찾기 목록";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->title = "즐겨찾기 목록";
	$P->Navigation = "상품관리 > 구매대행 > 즐겨찾기 목록";
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