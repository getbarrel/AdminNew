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


$db = new Database;


$sql = "select gcs.* , ci.cid, ci.cname , ci.depth 
			from goodss_category_setting gcs
			left join shop_category_info ci on gcs.cid = ci.cid  
			where gcs_ix = '".$gcs_ix."'
			";

$db->query("$sql "); //where uid = '$code'

if($db->total){
	$db->fetch();
	$category_mappingInfo = $db->dt;
	$act = "update";
}else{
	$act = "insert";
}



$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
			<td align='left' colspan=4> ".GetTitleNavigation("상품자동연동 설정", "기본정보설정 > 상품자동연동 설정")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>상품자동연동설정</b></div>")."</td>
	  </tr>
	 
	  </table>
	  <form name='category_mapping_form' method='get' action='category_mapping.act.php' onsubmit='return CheckFormValue(this);' target='iframe_act' style='display:inline;'>
	<input type='hidden' name='act' value='".$act."'>
	<input type='hidden' name='cid2' value='".$category_mappingInfo[cid]."'>
	<input type='hidden' name='depth' value=''>
	<input type='hidden' name='goodss_cid' value='".$category_mappingInfo[goodss_cid]."'>
	<input type='hidden' name='goodss_depth' value=''>
	<input type='hidden' name='gcs_ix' value='$gcs_ix'>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	<tr>
		<td colspan=2>
			
			<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='input_table_box'>
				<col width='25%' />
				<col width='30%' />
				<col width='25%' />
				<col width='30%' />
				<tr>
					<td class='search_box_title'><b>도매업체</b></td>
					<td class='search_box_item' colspan=3>";
					$Contents01 .=	"<select name='company_id' validation=true title='도매업체'>";
					$Contents01 .=	"<option value=''>등록한 도매업체</option>";
					for($i=0;$i < count($userable_service_infos);$i++){
						$userable_service_info = (array)$userable_service_infos[$i];
						$Contents01 .=	"<option value='".$userable_service_info["service_code"]."'  ".($userable_service_info["goodss_company_id"] == $category_mappingInfo[goodss_company_id] ? "selected":"").">".$userable_service_info["com_name"]."</option>";
					}
					$Contents01 .=	"</select> 
					</td>
				</tr>
				 <tr>
					<td class='input_box_title' width='150'><b>도매 사이트 카테고리</b></td>
					<td class='input_box_item' colspan=3>
						<table border=0 cellpadding=0 cellspacing=0>
							<tr>
								<td style='padding-right:5px;'>".getGoodssCategoryList("대분류", "cid0_2", "onChange=\"loadGoodsCategory(this,'cid1_2',2)\" validation=false title='대분류' ", 0, $category_mappingInfo[goodss_cid])."</td>
								<td style='padding-right:5px;'>".getGoodssCategoryList("중분류", "cid1_2", "onChange=\"loadGoodsCategory(this,'cid2_2',2)\" validation=false title='중분류'", 1, $category_mappingInfo[goodss_cid])."</td>
								<td style='padding-right:5px;'>".getGoodssCategoryList("소분류", "cid2_2", "onChange=\"loadGoodsCategory(this,'cid3_2',2)\" validation=false title='소분류'", 2, $category_mappingInfo[goodss_cid])."</td>
								<td>".getGoodssCategoryList("세분류", "cid3_2", "onChange=\"loadCategory(this,'goodss_cid',2)\" title='세분류'", 3, $category_mappingInfo[goodss_cid])."</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'>카테고리선택</td>
					<td class='input_box_item' colspan=3 >
						<table border=0 cellpadding=0 cellspacing=0>
							<tr>
								<td style='padding-right:5px;'>".getCategoryList3("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $category_mappingInfo[cid])."</td>
								<td style='padding-right:5px;'>".getCategoryList3("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $category_mappingInfo[cid])."</td>
								<td style='padding-right:5px;'>".getCategoryList3("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $category_mappingInfo[cid])."</td>
								<td>".getCategoryList3("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $category_mappingInfo[cid])."</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
				<td class='input_box_title'>마진설정</td>
				<td class='input_box_item' colspan=3 style='padding:10px 5px;'>
					<table cellpadding=0 cellspacing=0 >
						<col width='130px'>
						<col width='300px'>
						<tr>
							<td>
								<input type=radio name='margin_caculation_type' id='margin_caculation_type_9' value='9' onclick=\"$('#margin_caculation_type_1_zone').hide();$('#margin_caculation_type_2_zone').hide();$('#margin_caculation_type_9_zone').show();\" ".($category_mappingInfo[margin_caculation_type] == "9" || $category_mappingInfo[margin_caculation_type] == "" ? "checked":"")."><label for='margin_caculation_type_9'>권장판매가 사용</label><br>
								<input type=radio name='margin_caculation_type' id='margin_caculation_type_1' value='1' onclick=\"$('#margin_caculation_type_1_zone').show();$('#margin_caculation_type_2_zone').hide();$('#margin_caculation_type_9_zone').hide();\" ".($category_mappingInfo[margin_caculation_type] == "1"  ? "checked":"")."><label for='margin_caculation_type_1'>공급가 + 마진</label><br>
								<input type=radio name='margin_caculation_type' id='margin_caculation_type_2' value='2' onclick=\"$('#margin_caculation_type_1_zone').hide();$('#margin_caculation_type_2_zone').show();$('#margin_caculation_type_9_zone').hide();\" ".($category_mappingInfo[margin_caculation_type] == "2" ? "checked":"")."><label for='margin_caculation_type_2'>공급가 * 배수</label>
							</td>
							<td style='border:1px solid silver;padding:7px 0px;' class='input_box_td point'>
								<table cellpadding=3 cellspacing=0  width=400 height=60 border=0 class='point' id='margin_caculation_type_1_zone' style='display:none;'>
									<col width='100px'>
									<col width='20px'>
									<col width='100px'>
									<col width='20px'>
									<col width='100px'>
									<tr style='text-align:center;font-weight:bold;'>
										<td>공급원가 </td><td> + </td><td>마진</td><td>=</td><td>판매가</td>
									</tr>
									<tr style='text-align:center;'>
										<td>##,### 원</td><td>+</td><td><input type=text class=textbox name='margin_plus' style='width:60px;' value='".$category_mappingInfo[margin_plus]."' ></td><td>=</td><td>##,### 원</td>
									</tr>
								</table>
								<table cellpadding=3 cellspacing=0  width=400 height=60 border=0 class='point' id='margin_caculation_type_2_zone' style='display:none;'>
									<col width='100px'>
									<col width='20px'>
									<col width='100px'>
									<col width='20px'>
									<col width='100px'>
									<tr style='text-align:center;font-weight:bold;'>
										<td>공급원가 </td><td> * </td><td>배수</td><td>=</td><td>판매가</td>
									</tr>
									<tr style='text-align:center;'>
										<td>##,### 원</td><td>*</td><td><input type=text class=textbox name='margin_cross' style='width:60px;' value=''".$category_mappingInfo[margin_cross]."' ></td><td>=</td><td>##,### 원</td>
									</tr>
								</table>
								<table cellpadding=3 cellspacing=0  width=400 height=60 border=0 class='point' id='margin_caculation_type_9_zone' >
									<col width='*'>
									<tr style='text-align:center;font-weight:bold;'>
										<td>도매업체에서 제공하는 권장 판매가를 적용합니다. </td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class='input_box_title'> <b>품절상태 </b></td>
				<td class='input_box_item' >
				<input type='radio' name='gsc_state' id='gsc_state_1' value='1' ".($category_mappingInfo[gsc_state] == "1" || $category_mappingInfo[gsc_state] == "" ? "checked":"")."><label for='gsc_state_1'><b>판매중</b></label> 
				<input type='radio' name='gsc_state' id='gsc_state_0' value='0' ".($category_mappingInfo[gsc_state] == "0" ? "checked":"")."><label for='gsc_state_0'> <b>일시품절</b></label>				
				</td>
				<td class='input_box_title'> <b>진열상태 </b></td>
				<td class='input_box_item' >
				<input type='radio' name='gsc_disp' id='gsc_disp_1' value='1' ".($category_mappingInfo[gsc_disp] == "1" || $category_mappingInfo[gsc_disp] == "" ? "checked":"")."><label for='gsc_disp_1'><b>노출함</b> </label> 
				<input type='radio' name='gsc_disp' id='gsc_disp_0' value='0' ".($category_mappingInfo[gsc_disp] == "0" ? "checked":"")."><label for='gsc_disp_0'><b>노출안함</b></label>				
				</td>
			</tr>
			<tr>
				<td class='input_box_title'>중복상품처리</td>
				<td class='input_box_item'>
					<input type='radio' name='dupe_process' id='dupe_process_skip' value='skip' ".($category_mappingInfo[dupe_process] == "skip" || $category_mappingInfo[dupe_process] == "" ? "checked":"")."><label for='dupe_process_skip'>SKIP</label>
					<input type='radio' name='dupe_process' id='dupe_process_update' value='update' ".($category_mappingInfo[dupe_process] == "update" ? "checked":"")."><label for='dupe_process_update'>UPDATE</label>					
				</td>
				<td class='input_box_title'><label for='usable_round'>가격반올림</label><input type='checkbox' name='usable_round' id='usable_round' value='Y' onclick='UsableRound(this)' ".($category_mappingInfo[usable_round] == "Y" ? "checked":"")."></td>
				<td class='input_box_item'><input type=hidden name='b_round_precision' id='b_round_precision'  value='".$category_mappingInfo[round_precision]."' >
					<select name='round_precision' id='round_precision' ".($category_mappingInfo[usable_round] == "N" || $category_mappingInfo[usable_round] == "" ? "disabled":"").">
						<option value='2' ".($category_mappingInfo[round_precision] == "2" ? "selected":"").">100자리</option>
						<option value='3' ".($category_mappingInfo[round_precision] == "3" ? "selected":"").">1000자리</option>
						<option value='4' ".($category_mappingInfo[round_precision] == "4" ? "selected":"").">10000자리</option>
					</select>
					<input type=hidden name='b_round_type' id='b_round_type' value='".$category_mappingInfo[round_type]."' >
					<input type='radio' name='round_type' id='round_type_1' value='round' ".($category_mappingInfo[usable_round] == "N" || $category_mappingInfo[usable_round] == "" ? "disabled":"")." ".(($category_mappingInfo[round_type] == "round" || $category_mappingInfo[round_type] == "") ? "checked":"")."><label for='round_type_1'>반올림</label>
					<input type='radio' name='round_type' id='round_type_2' value='floor' ".($category_mappingInfo[usable_round] == "N" || $category_mappingInfo[usable_round] == "" ? "disabled":"")."  ".($category_mappingInfo[round_type] == "floor" ? "checked":"")."><label for='round_type_2'>버림</label>
				</td>
			</tr>
			<tr>
					<td class='input_box_title'>사용여부</td>
					<td class='input_box_item' colspan=3>
						<input type=radio name='disp' id='disp_1' value='1' ".($category_mappingInfo[disp] == "1" || $category_mappingInfo[disp] == "" ? "checked":"")."><label for='disp_1'>사용</label>
						<input type=radio name='disp' id='disp_0' value='0' ".($category_mappingInfo[disp] == "0" ? "checked":"")."><label for='disp_0'>사용하지않음</label>
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
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >상품을 가져오고자 하는 도매사이트 분류를 선택합니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록하기를 원하는 쇼핑몰의 카테고리를 설정합니다.</td></tr>
</table>
";
//$help_text =  getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');

$Contents .= HelpBox("자동설정하기", $help_text)."<br>";

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


function UpdateOrginCategoryInfo(bsui_ix, before_orgin_category_info, change_orgin_category_info){
//alert(($.trim(before_orgin_category_info) == $.trim(change_orgin_category_info)));
if(!($.trim(before_orgin_category_info) == $.trim(change_orgin_category_info))){
	$.ajax({ 
		type: 'GET', 
		data: {'act': 'orgin_category_info_update','bsui_ix': bsui_ix,'orgin_category_info': change_orgin_category_info},
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


 function DeleteFavoriteInfo(bsui_ix){
 	if(confirm('구매대행 자동설정하기을 정말로 삭제하시겠습니까?')){//'해당 구매대행 환율/수수료 정보를 정말로 삭제하시겠습니까?'
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
	function loadGoodsCategory(sel,target) {
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		var depth = sel.depth;

		//document.write('category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
		window.frames['act'].location.href = 'category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
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
	$P->NaviTitle = "자동설정하기";
	$P->Navigation = "설정 > 자동설정하기";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->title = "자동설정하기";
	$P->Navigation = "설정 > 자동설정하기";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


/*
create table goodss_category_setting (
	bsui_ix int(4) unsigned not null auto_increment  ,
	cid varchar(15)  NOT NULL COMMENT '상품카테고리' ,
	goodss_cid varchar(15) NOT NULL COMMENT '도매사이트 상품카테고리',
	price_type varchar(256) null default null COMMENT '구매대행 사이트 리스트 URL ' ,
	bs_list_url_md5 varchar(32) null default null COMMENT '구매대행 사이트 리스트 URL 키값' ,
	orgin_category_info varchar(255)  NULL COMMENT '구매대행사이트 카테고리 정보',
	disp char(1) default '1' ,
	regdate datetime not null,
primary key(bsui_ix));

*/
?>