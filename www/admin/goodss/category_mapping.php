<?
include("../class/layout.class");
$install_path = "../../include/";
include("SOAP/Client.php");
include_once("goodss.lib.php");


$soapclient = new SOAP_Client("http://www.goodss.co.kr/admin/goodss/api/");
// server.php 의 namespace 와 일치해야함
$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

## 한글 인자의 경우 에러가 나므로 인코딩함.


$service_infos = (array)$soapclient->call("getUsableServiceInfo",$params = array("mall_ix"=> $admininfo[mall_ix]),	$options);
	//echo $co_goodsinfo;

$useable_service = (array)$service_infos[useable_service];
$userable_service_infos = (array)$service_infos[userable_service_infos];
//$goodss_companyinfos = (array)$service_infos[goodss_companyinfos];



//print_r($userable_service_infos);

for($i=0;$i < count($userable_service_infos);$i++){
	$userable_service_info = (array)$userable_service_infos[$i];
	$goodss_companyinfos[$userable_service_info[goodss_company_id]] = $userable_service_info[com_name];
}
//print_r($goodss_companyinfos);
/*
foreach($unserialize_search_value as $key => $value){
	$search_rules[$key]= $value;//urlencode($value);
}  
*/

$db = new Database;


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

$where = "where gcs_ix is not null ";
if($cid2 != ""){
	$where .= " and gcs.cid LIKE '".substr($cid2,0,($depth+1)*3)."%' ";
}

if($search_text != ""){
	$where .= "and ".$search_type." LIKE '%".trim($search_text)."%' ";
}

if($bs_site != ""){
	$where .= "and bs_site = '".trim($bs_site)."' ";
}

$sql = "select count(*) as total from goodss_category_setting gcs  $where ";
//echo $sql;
$db->query($sql);

$db->fetch();
$total = $db->dt[total];

$sql = "select gcs.* , ci.cid, ci.cname , ci.depth 
			from goodss_category_setting gcs
			left join shop_category_info ci on gcs.cid = ci.cid  
			$where
			order by regdate desc limit $start, $max";


$db->query("$sql "); 



$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
			<td align='left' colspan=4> ".GetTitleNavigation("상품자동연동설정", "기본정보 설정 > 상품자동연동설정 ")."</td>
	  </tr>
	  
	 
	  </table>
	  <form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' style='display:inline;'>
	<input type='hidden' name='mode' value='search'>
	<input type='hidden' name='cid2' value='$cid2'>
	<input type='hidden' name='depth' value='$depth'>
	<input type='hidden' name='goods_cid' value='$goods_cid'>
	<input type='hidden' name='goods_depth' value='$goods_depth'>
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
					<td class='search_box_title'><b>도매업체</b></td>
					<td class='search_box_item' colspan=3>";
					$Contents01 .=	"<select name='company_id'>";
					$Contents01 .=	"<option value=''>등록한 도매업체</option>";
					for($i=0;$i < count($userable_service_infos);$i++){
						$userable_service_info = (array)$userable_service_infos[$i];
						$Contents01 .=	"<option value='".$userable_service_info["service_code"]."'  ".($userable_service_info["goodss_company_id"] == $category_mappingInfo[goodss_company_id] ? "selected":"").">".$userable_service_info["com_name"]."</option>";
					}
					$Contents01 .=	"</select> 
					</td>
				</tr>
				<tr>
					<td class='input_box_title' width='150'><b>도매 아이템 카테고리</b></td>
					<td class='input_box_item' colspan=3>
						<table border=0 cellpadding=0 cellspacing=0>
							<tr>
								<td style='padding-right:5px;'>".getGoodssCategoryList("대분류", "cid0_2", "onChange=\"loadGoodsCategory(this,'cid1_2',2)\" validation=false title='대분류' ", 0, $cid2)."</td>
								<td style='padding-right:5px;'>".getGoodssCategoryList("중분류", "cid1_2", "onChange=\"loadGoodsCategory(this,'cid2_2',2)\" validation=false title='중분류'", 1, $cid2)."</td>
								<td style='padding-right:5px;'>".getGoodssCategoryList("소분류", "cid2_2", "onChange=\"loadGoodsCategory(this,'cid3_2',2)\" validation=false title='소분류'", 2, $cid2)."</td>
								<td>".getGoodssCategoryList("세분류", "cid3_2", "onChange=\"loadCategory(this,'goodss_cid',2)\" title='세분류'", 3, $goodss_cid)."</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'>쇼핑몰 카테고리선택</td>
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
	<div style='z-index:-1;position:absolute;width:100%;text-align:center;' id='select_update_parent_save_loading'>
		<div style='width:100%;height:200px;display:block;position:relative;z-index:10px;text-align:center;padding-top:60px;' id='select_update_save_loading'></div>
	</div>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'  class='list_table_box'><!--style='table-layout:fixed;'-->
		<col width=5%>
		<col width=30%>
		<col width=* >
		<col width=10%>
		<col width=9%>
		<col width=10%>
		<col width=11%>
		<col width=7% >
		<col width=9%>
	  <tr bgcolor=#efefef align=center height=25>
			<td class='s_td' rowspan=2 nowrap>번호 </td>
			<td class='m_td' >도매아이템 카테고리</td>			
			<td class='m_td' rowspan=2>도매업체</td>
			<td class='m_td' rowspan=2>마진설정</td>
			<td class='m_td' rowspan=2>가격반올림</td>
			<td class='m_td' rowspan=2>판매/노출 여부</td>
			<td class='m_td' rowspan=2 align='center' >등록일자</td>
			<td class='m_td' rowspan=2>사용여부 </td>
			<td class='e_td' rowspan=2>관리 </td>
		</tr>
		<tr height=25>
			<td class='m_td' >쇼핑몰 카테고리</td>
			
		</tr>";

$soapclient = new SOAP_Client("http://www.goodss.co.kr/admin/goodss/api/");
// server.php 의 namespace 와 일치해야함
$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

## 한글 인자의 경우 에러가 나므로 인코딩함.




if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;
		$category_text = $soapclient->call("getCategoryPathByAdmin",$params = array("cid"=> $db->dt[goodss_cid], "depth"=> 4),	$options);

		$Contents02 .= "<tr align=center height=35>
				<td class='list_box_td list_bg_gray' rowspan=2>".$no." </td>
				<td class='list_box_td point' style='text-align:left;'>".$category_text ."</td>
				<td class='list_box_td ' rowspan=2>".$goodss_companyinfos[$db->dt[goodss_company_id]]."</td>
				<td class='list_box_td list_bg_gray' style='padding:0px;' rowspan=2 nowrap>";
				if($db->dt[margin_caculation_type] == "9"){
					$Contents02 .= "권장판매가 사용";
				}else if($db->dt[margin_caculation_type] == "1"){
					$Contents02 .= "공급가 + 마진 (".number_format($db->dt[margin])." %)";
				}else if($db->dt[margin_caculation_type] == "2"){
					$Contents02 .= "공급가*배수 (".number_format($db->dt[margin])." 배)";
				}

		$Contents02 .= "			
				</td>
				<td class='list_box_td ' rowspan=2>";
				if($db->dt[usable_round] == "Y"){
					//$Contents02 .= "가격반올림 사용 : ";
					if($db->dt[round_precision] == "2"){
						$Contents02 .= "100 자리";
					}else if($db->dt[round_precision] == "3"){
						$Contents02 .= "1,000  자리";
					}else if($db->dt[round_precision] == "4"){
						$Contents02 .= "10,000 자리";
					}

					if($db->dt[round_type] == "floor"){
						$Contents02 .= "(버림)";
					}else if($db->dt[round_type] == "round"){
						$Contents02 .= "(반올림)";
					}
				}else{
					$Contents02 .= " 사용안함";
				}
				$Contents02 .= "</td>
				
				<td class='list_box_td list_bg_gray' style='line-height:140%;' rowspan=2>".($db->dt[gcs_state] == "1" ? "판매중":"일시품절")." <br> ".($db->dt[gcs_disp] == "1" ? "노출함":"노출안함")."</td>
				<td class='list_box_td ' rowspan=2>".$db->dt[regdate]."</td>
				<td class='list_box_td ' rowspan=2>".($db->dt[disp] == "1" ? "사용함":"사용안함")."</td>
				<td class='list_box_td list_bg_gray'  rowspan=2>
				<a href=\"javascript:PoPWindow3('../goodss/category_mapping.edit.php?mmode=pop&gcs_ix=".$db->dt[gcs_ix]."',800,600,'category_mapping_edit')\"'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
				<a href=\"javascript:DeleteCategoryMapping('".$db->dt[gcs_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a></td>
			</tr>
			<tr height=35>
				<td  ><b>".getCategoryPathByAdmin($db->dt[cid], 4)."</b></td>
			</tr>";
	}
	$Contents02 .= "";
}else{
		$Contents02 .= "<tr height=60><td colspan=9 align=center>상품자동연동 설정  정보가 없습니다.</td></tr>";

}


$Contents02 .= "</table>";
$Contents02 .= "<table width=100%>
						<tr>
							
							<td align=right>".page_bar($total, $page, $max,"&cid2=$cid2&depth=$depth&search_type=$search_type&search_text=$search_text&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&bs_site=$bs_site","")."</td>							
					  </tr>
					  <tr>
							<td style='text-align:center;'>
								<a href=\"javascript:PoPWindow3('../goodss/category_mapping.edit.php?mmode=pop',800,600,'category_mapping_edit')\"'><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0></a> 
								<img src='../images/".$admininfo["language"]."/btn_goodss_autoupdate.gif' border=0 \" id='onclick_' onclick=\"GoodssAuto('cron_goodss_reg');$('#onclick_').attr('onclick','');\" style='cursor:pointer;'>
							</td>
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
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >상품 자동연동 설정등록 버튼을 클릭하시면 설정팝업이 노출됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록된 설정정보를 바탕으로 상품정보를 주기적으로 연동하게 됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ></td></tr>
</table>
";
//$help_text =  getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');

$Contents .= HelpBox("상품자동연동 설정", $help_text)."<br>";

 $Script = "
 <script language='javascript'>

 function GoodssAuto(act){
	SelectUpdateLoading();
	window.frames['act'].location.href = 'goodss.cron.php?goods_act='+ act;
 }

function SelectUpdateLoading(){
	document.getElementById('select_update_parent_save_loading').style.zIndex = '1';
	with (document.getElementById('select_update_save_loading').style){

		width = '100%';
		height = '173px';
		backgroundColor = '#ffffff';
		filter = 'Alpha(Opacity=70)';
		opacity = '0.8';
	}

	var obj = document.createElement('div');
	with (obj.style){
		position = 'relative';
		zIndex = 100;
	}
	obj.id = 'select_update_loadingbar';

	obj.innerHTML = \"<img src='/admin/images/indicator.gif' border=0 width=32 height=32 align=absmiddle> 상품연동 중입니다..\";

	document.getElementById('select_update_save_loading').appendChild(obj);

	document.getElementById('select_update_save_loading').style.display = 'block';
}

 function AutoUpdate(cid, bs_site, list_url){
	//alert(bs_site+':::'+list_url);
	$.ajax({ 
			type: 'GET', 
			data: {'bs_act': 'favorite_update','category_mapping': '1','cid2': cid,'bs_site': bs_site,'list_url': list_url},
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

 function UpdateOrginCategoryInfo(gcs_ix, before_orgin_category_info, change_orgin_category_info){
	//alert(($.trim(before_orgin_category_info) == $.trim(change_orgin_category_info)));
	if(!($.trim(before_orgin_category_info) == $.trim(change_orgin_category_info))){
		$.ajax({ 
			type: 'GET', 
			data: {'act': 'orgin_category_info_update','gcs_ix': gcs_ix,'orgin_category_info': change_orgin_category_info},
			url: 'category_mapping.act.php',  
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


 function DeleteCategoryMapping(gcs_ix){
 	if(confirm('도매아이템 상품자동연동 설정을 정말로 삭제하시겠습니까?')){//'해당 구매대행 환율/수수료 정보를 정말로 삭제하시겠습니까?'
 	//	var frm = document.group_frm;
 	//	frm.act.value = act;
 	//	frm.gp_ix.value = gp_ix;
 	//	frm.submit();

 		f    = document.createElement('form');
    f.name = 'bsform';
    f.id = 'bsform';
    f.method    = 'post';
    f.target = 'iframe_act';
    f.action    = 'category_mapping.act.php';

    i0          = document.createElement('input');
    i0.type     = 'hidden';
    i0.name     = 'act';
    i0.id     = 'act';
    i0.value    = 'delete';
    f.insertBefore(i0);

    i1          = document.createElement('input');
    i1.type     = 'hidden';
    i1.name     = 'gcs_ix';
    i1.id     = 'gcs_ix';
    i1.value    = gcs_ix;
    f.insertBefore(i1);

		document.insertBefore(f);
		f.submit();

 	}
}
 </script>
 <script Language='JavaScript' type='text/javascript'>
	function loadGoodsCategory(sel,target) {
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.depth;
		var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
		//document.write('category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
		window.frames['act'].location.href = 'category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
	function loadCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.depth;//kbk
		//var depth = sel.getAttribute('depth');
		var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
		//dynamic.src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		//document.getElementById('act').src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
	function loadChangeCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.depth;//kbk
		//var depth = sel.getAttribute('depth');
		var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
		//dynamic.src = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;//kbk
		//document.getElementById('act').src = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		window.frames['act'].location.href = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
	</script>
 ";

if($mmode == "pop"){

	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = goodss_menu();
	$P->NaviTitle = "상품자동연동설정";
	$P->Navigation = "도매아이템 > 기본정보설정 > 상품자동연동설정";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = goodss_menu();
	$P->title = "상품자동연동설정";
	$P->Navigation = "도매아이템 > 기본정보 설정 > 상품자동연동설정";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


/*
create table goodss_category_setting (
	gcs_ix int(4) unsigned not null auto_increment  ,
	cid varchar(15)  NOT NULL COMMENT '상품카테고리' ,
	goodss_cid varchar(15) NOT NULL COMMENT '도매사이트 상품카테고리',
	`margin_caculation_type` enum('9','1','2') NOT NULL default '9' COMMENT '마진설정타입',
	margin int(2) NOT NULL default '0' COMMENT '마진값(배수, 마진)',
	`usable_round` enum('Y','N') NOT NULL default 'Y' COMMENT '마진적용시 반올림값',
	`round_precision` int(2) NOT NULL default '0' COMMENT '마진적용시 반올림값',
	`round_type` varchar(10) NOT NULL default 'round' COMMENT '가격반올림타입',
	`dupe_process` enum('skip','update') NOT NULL default 'skip' COMMENT '중복상품처리',
	`gcs_state` enum('1','0') NOT NULL default '1' COMMENT '판매상태',
	`gcs_disp` enum('1','0') NOT NULL default '1' COMMENT '진열상태',
	disp char(1) default '1' ,
	regdate datetime not null,
primary key(gcs_ix));

*/
?>