<?
include("../class/layout.class");
$install_path = "../../include/";
include("SOAP/Client.php");
//include_once("goodss.lib.php");
include("../logstory/class/sharedmemory.class");
include_once("sellertool.lib.php");

$db = new Database;

if($goods_act == ""){
	$goods_act = "cron_sellertool_update";
}

if($goods_act == "cron_sellertool_update"){
	$shmop = new Shared("cron_sellertool_update");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$cron_sellertools = $shmop->getObjectForKey("cron_sellertool_update");
}else{
	$shmop = new Shared("cron_sellertool_reg");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$cron_sellertools = $shmop->getObjectForKey("cron_sellertool_reg");
}
$cron_sellertools = unserialize(urldecode($cron_sellertools));
if($_GET["site_code"]){
$set_cron_sellertool = $cron_sellertools[$_GET["site_code"]];
}
//print_r($cron_sellertools);
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
			<td align='left' colspan=4> ".GetTitleNavigation("제휴사연동 스케줄링", "제휴사연동 > 제휴사연동 스케줄링 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=2 style='padding-bottom:15px;'>
	    <div class='tab'>
				<table class='s_org_tab'>
				<tr>
					<td class='tab'>";
			
			$Contents01 .=	"
						
						<table id='tab_02' ".($goods_act == "cron_sellertool_reg" ||  $goods_act == ""  ? "class='on'":"")." >
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='?goods_act=cron_sellertool_reg'\">신규상품등록</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_04' ".($goods_act == "cron_sellertool_update" ? "class='on'":"")." >
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='?goods_act=cron_sellertool_update'\">가격/재고 업데이트</td>
							<th class='box_03'></th>
						</tr>
						</table>";
			
			$Contents01 .=	"
					</td>
					<td style='vertical-align:bottom;padding:0px 0px 10px 4px;'>";

$Contents01 .= "
					</td>
				</tr>
				</table>
			</div>
	    </td>
	</tr>
	 
	  </table>
	  <form name='search_form' method='get' action='interface_scheduler.act.php' onsubmit='return CheckFormValue(this);' style='display:inline;'  target='iframe_act'>
	<input type='hidden' name='act' value='update'>
	<input type='hidden' name='goods_act' value='".$goods_act."'>
	
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	<tr>
		<td colspan=2>
			
			<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='input_table_box'>
				<col width='20%' />
				<col width='30%' />
				<col width='20%' />
				<col width='30%' />
				<tr>
        					<td class='input_box_title'>제휴사 선택</td>
        					<td class='input_box_item' colspan=3>
        						<table border=0 cellpadding=0 cellspacing=0>
        							<tr>
        								<td style='padding-right:5px;'>
                                            ".getSellerToolSiteInfo($site_code)."
                                        </td>
        							</tr>
        						</table>
        					</td>
        				</tr>
				<tr>
					<td class='input_box_title'>  스케줄 시간설정  </td>
					<td class='input_box_item' colspan=3 style='padding:5px;' >
					<table cellpadding=3>
						<tr>
							<td>
							<!--input type='radio' name='cron_minutes_set' id='cron_minutes_set_1' value='*' onclick=\"$('#cron_minutes').attr('disabled',true);\" ><label for='cron_minutes_set_1'>매분</label><br-->
							<input type='radio' name='cron_minutes_set' id='cron_minutes_set_2' value='' onclick=\"$('#cron_minutes').attr('disabled',false);\" checked><label for='cron_minutes_set_2'>분지정</label><br>
							</td>
							<td>
							<!--input type='radio' name='cron_hours_set' id='cron_hours_set_1' value='*' onclick=\"$('#cron_hours').attr('disabled',true);\" ><label for='cron_hours_set_1'>매시간</label><br-->
							<input type='radio' name='cron_hours_set' id='cron_hours_set_2' value='' onclick=\"$('#cron_hours').attr('disabled',false);\" checked><label for='cron_hours_set_2'>시간지정</label><br>
							</td>
							<td>
							<input type='radio' name='cron_days_set' id='cron_days_set_1' value='*' onclick=\"$('#cron_days').attr('disabled',true);\" checked><label for='cron_days_set_1'>매일</label> 
							<!--input type='radio' name='cron_days_set' id='cron_days_set_2' value='' onclick=\"$('#cron_days').attr('disabled',false);\" ><label for='cron_days_set_2'>지정</label><br-->
							</td>
							<td>
							<input type='radio' name='cron_months_set' id='cron_months_set_1' value='*' onclick=\"$('#cron_months').attr('disabled',true);\" checked><label for='cron_months_set_1'>매월</label><br>
							<!--input type='radio' name='cron_months_set' id='cron_months_set_2' value='' onclick=\"$('#cron_months').attr('disabled',false);\" ><label for='cron_months_set_2'>지정</label><br-->
							</td>
							<td>
							<input type='radio' name='cron_weekdays_set' id='cron_weekdays_set_1' value='*' onclick=\"$('#cron_weekdays').attr('disabled',true);\"  checked><label for='cron_weekdays_set_1'>매요일</label><br>
							<!--input type='radio' name='cron_weekdays_set' id='cron_weekdays_set_2' value='' onclick=\"$('#cron_weekdays').attr('disabled',false);\" ><label for='cron_weekdays_set_2'>지정</label><br-->
							</td>
						</tr>
						<tr>
							<td>
							<select name='cron_minutes'  id='cron_minutes'  style=\"font-size:12px;width:90px;\" >
								<option value='00'>00</option>";
								for($i=1; $i < 6;$i++){
								$Contents01 .= "<option value='".($i*10)."' ".( $set_cron_sellertool[cron_minutes] == $i*10 ? 'selected' : '' ).">".($i*10)."</option>";
								}
								$Contents01 .= "
							</select>
							</td>
							<td>
							<select name='cron_hours' id='cron_hours' style=\"font-size:12px;width:90px;\" >
								<!--option value='00'>00</option-->";
								for($i=0; $i < 24;$i++){
								$Contents01 .= "<option value='".$i."' ".( $set_cron_sellertool[cron_hours] == $i ? 'selected' : '' ).">".$i."</option>";
								}
								$Contents01 .= "
							</select>
							</td>
							<td>
							<select name='cron_days' id='cron_days' style=\"font-size:12px;width:90px;\" disabled >
								<option value='*'>매일</option>";
								for($i=0; $i < 31;$i++){
								$Contents01 .= "<option value='".$i."' ".( $set_cron_sellertool[cron_days] == $i ? 'selected' : '' ).">".$i."</option>";
								}
								$Contents01 .= "
							</select>
							</td>
							<td>
							<select name='cron_months' id='cron_months' style=\"font-size:12px;width:90px;\" disabled >
								<option value='*'>매월</option>";
								for($i=0; $i < 31;$i++){
								$Contents01 .= "<option value='".$i."' ".( $set_cron_sellertool[cron_months] == $i ? 'selected' : '' ).">".$i." 월</option>";
								}
								$Contents01 .= "
							</select>
							</td>
							<td>
							<select name='cron_weekdays' id='cron_weekdays' style=\"font-size:12px;width:90px;\" disabled >
								<option value='*'>매요일</option>";
								for($i=0; $i < 31;$i++){
								$Contents01 .= "<option value='".$i."' ".( $set_cron_sellertool[cron_weekdays] == $i ? 'selected' : '' ).">".$i." 주</option>";
								}
								$Contents01 .= "
							</select>
							</td>
						</tr>
					</table>
					

					

					

					

					

					</td>
				</tr>
				<!--tr>
					<td class='search_box_title'><b>도매업체</b></td>
					<td class='search_box_item' colspan=3>";
					$Contents01 .=	"<select name='company_id'>";
					$Contents01 .=	"<option value=''>등록한 도매업체</option>";
					for($i=0;$i < count($userable_service_infos);$i++){
						$userable_service_info = (array)$userable_service_infos[$i];
						$Contents01 .=	"<option value='".$userable_service_info["service_code"]."'  ".($userable_service_info["service_code"] == $company_id ? "selected":"").">".$userable_service_info["com_name"]."</option>";
					}
					$Contents01 .=	"</select>
					</td>
					<td class='input_box_title'>작업종류</td>
					<td class='input_box_item'>
						<input type='radio' name='work_type' id='work_type_reg' value='reg' ".($category_mappingInfo[work_type] == "reg" || $category_mappingInfo[work_type] == "" ? "checked":"")."><label for='work_type_reg'>신규상품등록</label>
						<input type='radio' name='work_type' id='work_type_update' value='update' ".($category_mappingInfo[work_type] == "update" ? "checked":"")."><label for='work_type_update'>가격/재고 업데이트</label>					
					</td>
				</tr-->
				
				</table>
		</td>
	</tr>
	<tr>
		<td colspan=2 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
	</tr>
	  </table>
	  </form>";

//$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents02 = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'  class='list_table_box'><!--style='table-layout:fixed;'-->
		<col width=5%>
		<col width=30%>
		<col width=* >
		<col width=15%>
		<col width=10%>
		<col width=7% >
		<col width=9%>
	  <tr bgcolor=#efefef align=center height=25>
			<td class='s_td'  nowrap>번호 </td>
			<td class='m_td' >제휴사 업체</td>
			<td class='m_td' >분지정</td>
			<td class='m_td' >시간지정</td>
			<td class='m_td' >일지정</td>
			<td class='m_td' >월지정</td>
			<td class='e_td' >관리 </td>
		</tr>";
//print_r($cron_sellertools);
if(count($cron_sellertools) > 0){
	$i = 1;
	if(is_array($cron_sellertools)){
	foreach($cron_sellertools as $key => $value){
		//for($i=0;$i  < count($cron_sellertools); $i++){
			$cron_sellertool = $cron_sellertools[$key];
			//print_r($cron_sellertool);
			$Contents02 .= "<tr align=center height=30>
					<td class='list_box_td list_bg_gray' >".$i." </td>
					<td class='list_box_td point' style='text-align:left;padding-left:10px;'>".$cron_sellertool[site_code]."</td>
					<td class='list_box_td list_bg_gray' >".$cron_sellertool[cron_minutes]." 분</td>
					<td class='list_box_td ' >".$cron_sellertool[cron_hours]." 시</td>
					<td class='list_box_td list_bg_gray' >".($cron_sellertool[cron_days_set] == "*" ? "매일":$cron_sellertool[cron_days_set])."</td>
					<td class='list_box_td ' >".($cron_sellertool[cron_weekdays_set] == "*" ? "매월":$cron_sellertool[cron_days_set])."</td>
					<td class='list_box_td list_bg_gray' >
					<a href=\"?site_code=".$cron_sellertool[site_code]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
					<a href=\"javascript:DeleteScheduleInfo('".$cron_sellertool[site_code]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a></td>
				</tr>";
		//}
		$i++;
	}
	}
	$Contents02 .= "";
}else{
		$Contents02 .= "<tr height=60><td colspan=7 align=center>제휴사연동 설정  정보가 없습니다.</td></tr>";

}


$Contents02 .= "</table>";



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
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >상품 업데이트를 원하시는 시간대를 설정 하신후 저장 버튼을 클릭하시면 설정이 완료되게 됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록된 설정정보를 바탕으로 상품정보를 주기적으로 연동하게 됩니다</td></tr>
</table>
";
//$help_text =  getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');

$Contents .= HelpBox("제휴사연동 스케줄링", $help_text)."<br>";

 $Script = "
 <script language='javascript'>
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


 function DeleteScheduleInfo(bsui_ix){
 	if(confirm('구매대행 카테고리 매핑을 정말로 삭제하시겠습니까?')){//'해당 구매대행 환율/수수료 정보를 정말로 삭제하시겠습니까?'
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
	$P->strLeftMenu = sellertool_menu();
	$P->NaviTitle = "제휴사연동 스케줄링";
	$P->Navigation = "제휴사연동 > 제휴사연동 > 제휴사연동 스케줄링";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu();
	$P->title = "제휴사연동 스케줄링";
	$P->Navigation = "제휴사연동 > 제휴사연동 > 제휴사연동 스케줄링";
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