<?
include("../class/layout.class");
include("../webedit/webedit.lib.php");
include_once("origin.lib.php");

$db = new Database;
$db2 = new Database;

$Contents = "
	<table cellpadding=0 cellspacing=0 border=0 width=100%>
	<tr>
		<td colspan=2>
			".GetTitleNavigation("원산지목록", "상품관리 > 원산지목록")."
		</td>
	</tr>
	<tr>
		<td align='left' colspan=4 style='padding-bottom:15px;'>
			".origin_tab("list")."
		</td>
	</tr>
	<tr>
		<td colspan=2>
			<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' style='display:inline;'>
			<input type='hidden' name='mode' value='search'>
			<input type='hidden' name='cid2' value='$cid2'>
			<input type='hidden' name='depth' value='$depth'>
			<input type='hidden' name='bsmode' value='$bsmode'>
			<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='input_table_box'>
				<col width='20%' />
				<col width='*' />
				<tr>
					<td class='input_box_title'>  검색어  </td>
					<td class='input_box_item' colspan='3' >
						<table cellpadding=0 cellspacing=0 width=100%>
							<col width='90px'>
							<col width='*'>
							<tr>
								<td><select name='search_type'  style=\"font-size:12px;height:20px;\">
									<option value='og.origin_name'>원산지명</option>
									</select>
								</td>
								<td style='padding-left:0px;'>
								<INPUT id=search_texts  class='textbox' value='' style=' FONT-SIZE: 12px; WIDTH: 20%; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
								</td>
								<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'></span></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class='input_box_title' style='font-size:12px;'>
						<b>원산지 분류</b> <input type='button' name='search_origin_category' id='search_origin_category' value='검색' onclick=\"PoPWindow('./search_origin_category.php?group_code=',600,600,'add_origin_category')\" style='cursor:pointer;'>
					</td>
					<td class='input_box_item' colspan=3>
						<div id='selected_category_5' style='padding:10px 0px 10px 0px;'>
						<table width='100%' cellpadding='0' cellspacing='0' id='objDepartment'>
							<colgroup>
							<col width='*'>
							<col width='150'>
							</colgroup>
							<tbody>";
								if(count($od_ix) > 0){
									$sql = "select * from common_origin_div where od_ix = '".$od_ix[0]."'";
									$db2->query($sql);
									$data_array = $db2->fetchall();
								
									for($i=0;$i<count($data_array);$i++){
										if($data_array[$i][depth] == '2'){
											$sql = "select * from common_origin_div where od_ix = '".$data_array[$i][parent_od_ix]."'";
											$db2->query($sql);
											$db2->fetch();
											$div1_name = $db2->dt[div_name];
									
											$Contents .= "<tr style='height:26px;' id='od_row_".$data_array[$i][od_ix]."'><td><input type='hidden' name='od_ix' id='od_ix_".$data_array[$i][od_ix]."' value='".$data_array[$i][od_ix]."'>".$div1_name." > ".$data_array[$i][div_name]."</td><td><a href='javascript:void(0)' onclick=\"od_del('".$data_array[$i][od_ix]."')\"><img src='../images/korea/btc_del.gif' border='0'></a></td></tr>";
										}else{
											$Contents .= "<tr style='height:26px;' id='od_row_".$data_array[$i][od_ix]."'><td><input type='hidden' name='od_ix' id='od_ix_".$data_array[$i][od_ix]."' value='".$data_array[$i][od_ix]."'>".$data_array[$i][div_name]."</td><td><a href='javascript:void(0)' onclick=\"od_del('".$data_array[$i][od_ix]."')\"><img src='../images/korea/btc_del.gif' border='0'></a></td></tr>";
										}
									}
								}
				$Contents .= "
							</tbody>
						</table>
						</div>
					</td>
				</tr>

				<tr height=30>
					<td class='input_box_title' ><label for='regdate'>원산지 등록일</label><!--<input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.search_seller);' ".CompareReturnValue("1",$regdate,"checked").">--></td>
					<td class='input_box_item' colspan='3'>
						".search_date('sdate','edate',$sdate,$edate)."
					</td>
				</tr>";
$Contents .="
			</table>
			
		</td>
	</tr>
	<tr>
		<td colspan=2 align=center style='padding:20px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
	</tr>
	</table>
	</form>

	<form name='list_frm' method='POST' onsubmit='return OriginInput_list(this);' action='origin.act.php'  target='act'>

	<input type='hidden' name='code[]' id='code'>
	<input type='hidden' name=before_update_kind value='".$update_kind."'>
	<input type='hidden' name=update_kind value='".$update_kind."'>
	<input type='hidden' name='group_by' value='".$group_by."'>
	<input type='hidden' name='start' value='".$start."'>
	<input type='hidden' name='cid2' value='$cid2'>
	<input type='hidden' name='depth' value='$depth'>
	<input type='hidden' name='bd_ix2' value='$bd_ix2'>
	<table cellpadding=0 cellspacing=0 border=0 width=100%>
	<tr height=10>
		<td align=rihgt style='padding-right:5px;' valign=top>
			".OriginList()."
		</td>
	</tr>
	</table>
	<iframe name='act' id='act' style='display:none;'></iframe>
	<iframe name='extand' id='extand' src='' width=0 height=0></iframe>";

$help_text = "
	<div id='batch_update_bd_category' ".($update_kind == "bd_category" ? "style='display:block'":"style='display:'")." >
	<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>원산지 분류 변경</b> <span class=small style='color:gray'><!--검색/선택된 회원에게 SMS 를 발송합니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F' )."</span></div>
	<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='input_table_box'>
	<col width='20%' />
	<col width='*' />

	<tr>
		<td class='input_box_title'>변경 원산지 분류</td>
		<td class='input_box_item'>
			<table border=0 cellpadding=0 cellspacing=0>
				<tr>
					<td>
						".getOriginDivSelect('parent_od_ix', '1차 원산지 분류',$parent_od_ix, $parent_od_ix, 1, " onChange=\"loadOriginInfo(this,'od_ix')\" validation='".$realestate_defailt_validation."' title='원산지분류' class='property_info' ")."
						".getOriginDivSelect('od_ix', '2차 원산지 분류',$parent_od_ix, $od_ix, 2, "validation='".$realestate_defailt_validation."' title='원산지 분류' class='property_info' ")."
					</td>
				</tr>
			</table>
		</td>
	</tr>
	</table>
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50>
			<td colspan=4 align=center>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$help_text .= "
				<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
			}else{
				$help_text .= "
				<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>";
			}
			$help_text .= "
			</td>
		</tr>
	</table>
	</div>
	";

	$select = "
	<nobr>
	<select name='update_type' onChange='view_member_num(this,\"$total\")'>
	<!--<option value='1'>검색한 원산지 전체</option>-->
		<option value='2'>선택한 원산지 전체</option>
	</select>
	<input type='radio' name='update_kind' id='update_kind_sms' value='bd_category'  ".(($update_kind == "bd_category" ) ? "checked":"checked")." onclick=\"ChangeUpdateForm('batch_update_bd_category');\"><label for='update_kind_sms'>원산지 분류 변경</label>";
	$select .= "
	</nobr>";

	if($admininfo[mall_type] == "H"){
		$Contents .= "".HelpBox($select, $help_text, 520)."</form>";
	}else{
		$Contents .= "".HelpBox($select, $help_text, 750)."</form>";
	}
$Script = "<script language='JavaScript'>

	$(document).ready(function (){

		$('#max').change(function(){
			var value= $(this).val();
			$.cookie('origin_max_limit', value, {expires:1,domain:document.domain, path:'/', secure:0});
			document.location.reload();
			
		});

	});

	function ChangeUpdateForm(selected_id){
		var area = new Array('batch_update_category','batch_update_bd_category');

		for(var i=0; i<area.length; ++i){
			if(area[i]==selected_id){
				document.getElementById(selected_id).style.display = 'block';
				$.cookie('brand_update_kind', selected_id.replace('batch_update_',''), {expires:1,domain:document.domain, path:'/', secure:0});
			}else{
				document.getElementById(area[i]).style.display = 'none';
			}
		}
	}

	function clearAll(frm){
		for(i=0;i < frm.cpid.length;i++){
			frm.cpid[i].checked = false;
		}
	}

	function checkAll(frm){
		for(i=0;i < frm.cpid.length;i++){
			frm.cpid[i].checked = true;
		}
	}

	function fixAll(frm){
		
		if (!frm.all_fix.checked){
			clearAll(frm);
			frm.all_fix.checked = false;
				
		}else{
			checkAll(frm);
			frm.all_fix.checked = true;
		}
		//input_check_num();
	}

	function loadOriginInfo(sel,target) {

		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;

		//var depth = sel.getAttribute('depth');
		//document.write('origin.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2');
		window.frames['act'].location.href = './origin.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

	}

	function od_del(code){
		$('#od_row_'+code).remove();
	}

	function ChangeRegistDate(frm){
		if(frm.regdate.checked){
			frm.sdate.disabled = false;
			frm.edate.disabled = false;
		}else{
			frm.sdate.disabled = true;
			frm.edate.disabled = true;
		}
	}

	function init(){
	//alert(1);
		var frm = document.search_seller;
	//	onLoad('$sDate','$eDate');";

	if($regdate != "1"){ 
		$Script .= "

		frm.sdate.disabled = true;
		frm.edate.disabled = true;";
	}

	$Script .= "
	}
</script>";

if($mmode == "pop"){

	$Script = "<script language='JavaScript' src='origin.js'></script>".$Script;
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
//	$P->OnloadFunction = "Init(document.originform);MM_preloadImages('../webedit/images/wtool1_1.gif','../webedit/images/wtool2_1.gif','../webedit/images/wtool3_1.gif','../webedit/images/wtool4_1.gif','../webedit/images/wtool5_1.gif','../webedit/images/wtool6_1.gif','../webedit/images/wtool7_1.gif','../webedit/images/wtool8_1.gif','../webedit/images/wtool9_1.gif','../webedit/images/wtool11_1.gif','../webedit/images/wtool13_1.gif','../webedit/images/wtool10_1.gif','../webedit/images/wtool12_1.gif','../webedit/images/wtool14_1.gif','../webedit/images/bt_html_1.gif','../webedit/images/bt_source_1.gif')"; //showSubMenuLayer('storeleft');
	//$P->OnloadFunction = "Init(document.originform);";
	$P->Navigation = "상품관리 > 상품분류관리 > 원산지목록";
	$P->NaviTitle = "원산지목록";
	$P->strLeftMenu = product_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else{
	$Script = "<script language='JavaScript' src='origin.js'></script>".$Script;
	$P = new LayOut();
	$P->addScript = $Script;
	$P->OnloadFunction = ""; //showSubMenuLayer('storeleft');
	$P->Navigation = "상품관리 > 상품분류관리 > 원산지목록";
	$P->title = "원산지목록";
	$P->strLeftMenu = product_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}

function getCategoryListForOrigin($category_text ="기본카테고리 선택", $object_name="cid",$id="cid", $onchange_handler="", $depth=0, $cid="")
{
	$mdb = new Database;
	$tb = (defined('TBL_SNS_CATEGORY_INFO'))	?	TBL_SNS_CATEGORY_INFO:TBL_SHOP_CATEGORY_INFO;
	if($depth == 0 || $cid != ""){
		$sql = "SELECT * FROM $tb where depth ='$depth' and cid LIKE '".substr($cid,0,($depth)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";
		//echo $sql;
		$mdb->query($sql);
	}

	if ($mdb->total){
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler id='$id' validation=false style='width:165px;font-size:12px;' title='카테고리'>\n";
		$mstring = $mstring."<option value=''>$category_text</option>\n";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			//if(substr_count($cid,substr($mdb->dt[cid],0,$mdb->dt[depth]+1))){
			if(substr($cid,0,($depth+1)*3) == substr($mdb->dt[cid],0,($depth+1)*3)){

				$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}else{
				$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}
		}
	}else{
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler id='$id' validation=false  style='width:140px;'>\n";
		$mstring = $mstring."<option value=''> $category_text</option>\n";
	}

	$mstring = $mstring."</Select>\n";

	return $mstring;
}




function OriginList(){

global $db, $admininfo,$nset,$page,$search_text,$search_type,$od_ix,$mode,$sdate,$edate;
global $auth_update_msg;

	if($_COOKIE[origin_max_limit]){
		$max = $_COOKIE[origin_max_limit]; //페이지당 갯수
	}else{
		$max = 20;
	}
	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	//$admininfo[mall_type]="F";
	if($admininfo[mall_type] == "B"  || $admininfo[mall_type] == "O"){// 입점형 , 오픈마켓형
		//$add_select=", mc.cname";
		//$add_table=" left join ".TBL_SHOP_CATEGORY_INFO." mc on og.cid = mc.cid";
	}else if($admininfo[mall_type] == "F" || $admininfo[mall_type] == "R"){ // 무료형 , 임대형
		$add_select="";
		$add_table="";
	}

	if($admininfo[admin_level] == "9"){
		$where = " where 1 ";
	}else{
		$where = " where og.company_id = '".$admininfo[company_id]."' ";
	}

	if($search_text != "" && $search_type != ""){
		$where .= " and ".$search_type." LIKE '%".$search_text."%'";
	}

	if($od_ix != ""){
		$where .= " and og.od_ix = '".$od_ix."'";
	}

	$sdate = str_replace("/","",$sdate);
	$edate = str_replace("/","",$edate);

	if($sdate != "" && $edate != ""){
		if($db->dbms_type == "oracle"){
			$where .= " and  to_char(og.regdate_ , 'YYYY-MM-DD') between '".$sdate."' and '".$edate."' ";
		}else{
			$where .= " and  MID(og.regdate,1,10) between  '".$sdate."' and '".$edate."' ";
		}
	}
	
	$sql = "SELECT 
				count(*) as total  
			FROM 
				common_origin og 
				left join common_origin_div od on og.od_ix = od.od_ix 
			".$where."
				";
	//echo $sql;
	$db->query($sql);
	$db->fetch();
	$total = $db->dt[total];
	//echo $total;
	$sql = "SELECT og.*, od.div_name, od.parent_od_ix, od.depth, count(*) as goods_cnt  
				FROM common_origin og 
				left join common_origin_div od on og.od_ix = od.od_ix 
				".$where." 
				group by og_ix  
				order by og.regdate desc  
				limit $start,$max";
	$db->query($sql);

	if($mode == "excel"){

		$goods_infos = $db->fetchall();
		$info_type = "origin_list";
		include("excel_out_columsinfo.php");
		$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='origin_".$info_type."' ";
		$db->query($sql);
		$db->fetch();
		$stock_report_excel = $db->dt[conf_val];

		$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='origin_check_".$info_type."' ";
		$db->query($sql);
		$db->fetch();
		$stock_report_excel_checked = $db->dt[conf_val];

		$check_colums = unserialize(stripslashes($stock_report_excel_checked));

		$columsinfo = $colums;

		include '../include/phpexcel/Classes/PHPExcel.php';
		PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

		date_default_timezone_set('Asia/Seoul');

		$inventory_excel = new PHPExcel();

		// 속성 정의
		$inventory_excel->getProperties()->setCreator("포비즈 코리아")
									 ->setLastModifiedBy("Mallstory.com")
									 ->setTitle("accounts plan price List")
									 ->setSubject("accounts plan price List")
									 ->setDescription("generated by forbiz korea")
									 ->setKeywords("mallstory")
									 ->setCategory("accounts plan price List");
		$col = 'A';
		foreach($check_colums as $key => $value){
			$inventory_excel->getActiveSheet(0)->setCellValue($col . "1", $columsinfo[$value][title]);
			$col++;
		}

		$before_pid = "";
	/*
	$colums[regdate] = array(value=>'regdate',title=>'등록일', checked=>'checked');
	$colums[bd_ix] = array(value=>'bd_ix',title=>'브랜드분류', checked=>'checked');
	$colums[brand_code] = array(value=>'brand_code',title=>'브랜드코드', checked=>'checked');
	$colums[cid] = array(value=>'cid',title=>'카테고리', checked=>'checked');
	$colums[brand_name] = array(value=>'brand_name',title=>'브랜드명', checked=>'checked');
	$colums[apply_status] = array(value=>'apply_status',title=>'신청상태', checked=>'checked');
	$colums[pcount] = array(value=>'pcount',title=>'상품수', checked=>'checked');
	$colums[disp] = array(value=>'disp',title=>'사용여부', checked=>'checked');
	*/

		for ($i = 0; $i < count($goods_infos); $i++)
		{
			$j="A";
			foreach($check_colums as $key => $value){
				if($key == "od_ix"){
					if($goods_infos[$i][depth] == 2){
						$db->query("SELECT div_name FROM common_origin_div WHERE od_ix  = '".$goods_infos[$i][parent_od_ix]."' ");
						$db->fetch(0);
						$value_str = $db->dt[div_name]." > ".$goods_infos[$i][div_name];
					}else{
						$value_str = $goods_infos[$i][div_name];
					}
				}else if($key == "disp"){
					switch($goods_infos[$i][disp]){
						case "0":
						$value_str = "사용안함";
						break;
						case "1":
						$value_str = "사용";
						break;
					}

				}else if($key == "pcount"){
					$value_str = $goods_infos[$i][goods_cnt];
				}else{
					$value_str = $goods_infos[$i][$value];//$db1->dt[$value];
				}

				$inventory_excel->getActiveSheet()->setCellValue($j . ($z + 2), $value_str);
				$j++;

				unset($history_text);
			}
			$z++;
		}
		// 첫번째 시트 선택
		$inventory_excel->setActiveSheetIndex(0);

		// 너비조정
		$col = 'A';
		foreach($check_colums as $key => $value){
			$inventory_excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
			$col++;
		}

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="member_'.$info_type.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'Excel5');
		$objWriter->save('php://output');

		exit;
	}

	//echo $total;
	$pagestring = page_bar($total, $page, $max, "&cid=$cid&depth=$depth&orderby=$orderby","");
	$mstring = "
		<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
			<col width='100px'>
			<col width='*'>
			<col width='120px'>
			<tr height=30 >
				<td><b>전체</b> : ".$total." 개</td>
				<td align=right>
				";
			
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
					$mstring .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
					<a href='excel_config.php?".$QUERY_STRING."&info_type=origin_list&excel_type=origin_list_excel' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
				}else{
					$mstring .= "
					<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
				}

				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
					$mstring .= " <a href='origin_list.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
				}else{
					$mstring .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
				}

			$mstring .= "
				</td>
				<td align=right nowrap>
				목록수 : <select name='max' id='max'>
							<option value='5' ".($_COOKIE[origin_max_limit] == '5'?'selected':'').">5</option>
							<option value='10' ".($_COOKIE[origin_max_limit] == '10'?'selected':'').">10</option>
							<option value='20' ".($_COOKIE[origin_max_limit] == '20'?'selected':'').">20</option>
							<option value='30' ".($_COOKIE[origin_max_limit] == '30'?'selected':'').">30</option>
							<option value='50' ".($_COOKIE[origin_max_limit] == '50'?'selected':'').">50</option>
							<option value='100' ".($_COOKIE[origin_max_limit] == '100'?'selected':'').">100</option>
						</select>
				</td>
			</tr>
		</table>

	<table cellpadding=0 cellspacing=0 width=100% class='list_table_box'>
		<col width='5%'>
		<col width='15%'>
		<col width='20%'>
		<col width='10%'>
		<col width='*'>
		<col width='8%'>
		<col width='8%'>
		<col width='15%'>
		<tr height=25 bgcolor=#efefef align=center>
			<td class='s_td' ><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.list_frm)'></td>
			<td class='m_td' >등록일자</td>
			<td class='m_td' >원산지 분류</td>
			<td class='m_td' >원산지 코드</td>";
		$mstring .= "<td class='m_td' width='*'>원산지명</td>
			<td class='m_td' >상품수</td>
			<td class='m_td' >사용여부</td>
			<td class='e_td'>관리</td>
		</tr>";

	if ($db->total == 0)	{
		$mstring = $mstring."<tr height=100><td colspan=10 align=center>원산지 리스트가 존재 없습니다.</td></tr>";
	}else{
		$origin_infos = $db->fetchall();
		for($i=0 ; $i < count($origin_infos) ; $i++)
		{
			
			if($origin_infos[$i][disp] == 1){
				$display_string = "사용";
			}else{
				$display_string = "사용안함";
			}
 

			if($origin_infos[$i][search_disp] == 1){
				$search_disp_string = "표시";
			}else{
				$search_disp_string = "표시하지않음";
			}

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				if($admininfo[admin_level] == 9){
					$origin_name = "<a href=\"origin.php?og_ix=".$origin_infos[$i][og_ix]."\">".$origin_infos[$i][origin_name]."</a>";
				}else if($admininfo[admin_level] == 8){
					if($admininfo[company_id] == $origin_infos[$i][company_id]){
						$origin_name = "<a href=\"origin.php?og_ix=".$origin_infos[$i][og_ix]."\"><u>".$origin_infos[$i][origin_name]."</u></a>";
					}else{
						$origin_name = $origin_infos[$i][origin_name];
					}
				}
			}else{
				$origin_name = "<a href=\"".$auth_update_msg."\"><u>".$origin_infos[$i][origin_name]."</u></a>";
			}

			if($origin_infos[$i][depth] == 2){
				$db->query("SELECT div_name FROM common_origin_div WHERE od_ix  = '".$origin_infos[$i][parent_od_ix]."' ");
				$db->fetch(0);
				$div_name = $db->dt[div_name]." > ".$origin_infos[$i][div_name];
			}else{
				$div_name = $origin_infos[$i][div_name];
			}

			$mstring = $mstring."<tr height=32 align=center>
				<td class='list_box_td'><input type=checkbox class=nonborder id='cpid' name=cpid[] value='".$origin_infos[$i][og_ix]."'></td>
				<td class='list_box_td'>".$origin_infos[$i][regdate]."</td>
				<td>".$div_name ."</td>
				<td class='list_box_td'>".$origin_infos[$i][og_ix]." ( ".$origin_infos[$i][origin_code]." )</td>
				<!--td class='list_box_td'><a href=\"JavaScript:ViewOriginImage('".$origin_infos[$i][og_ix]."')\">".$origin_infos[$i][og_ix]."</a></td-->";
				$mstring .="<td class='list_box_td point' align=center>$origin_name</td>
				<td>".$origin_infos[$i][goods_cnt]."</td>
				<td>".$display_string."</td>
				<td>
					<a href=\"origin.php?og_ix=".$origin_infos[$i][og_ix]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
	    			<a href=\"javascript:deleteOriginInfo('delete','".$origin_infos[$i][og_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
				</td>
				</tr>";

		}
	}

	$mstring .= "</table>";
	$mstring .= "<ul class='paging_area' >
						<li class='front'>".$pagestring."</li>
						<li class='back'></li>
					  </ul>";
		return $mstring;
} 


/*
CREATE TABLE IF NOT EXISTS `common_origin` (
  `og_ix` int(4) NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `cid` varchar(15) DEFAULT NULL COMMENT '상품카테고리값',
  `od_ix` int(10) unsigned NOT NULL COMMENT '원산지 분류',
  `origin_name` varchar(100) DEFAULT NULL COMMENT '원산지명',
  `origin_code` varchar(20) NOT NULL COMMENT '원산지코드',
  `origin_name_division` varchar(255) DEFAULT '' COMMENT '원산지명 자모음 분리',
  `disp` char(1) DEFAULT '0' COMMENT '사용여부',
  `search_disp` char(1) DEFAULT NULL COMMENT '검색노출여부',
  `shotinfo` varchar(255) DEFAULT NULL COMMENT '간략설명',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`or_ix`),
  KEY `IDX_MB_CID` (`cid`),
  KEY `IDX_MB_DISP` (`disp`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='원산지정보'
*/
?>