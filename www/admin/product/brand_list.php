<?
include("../class/layout.class");
include("../webedit/webedit.lib.php");
include_once("brand.lib.php");

$db = new Database;
$db2 = new Database;


$Contents = "
	<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' style='display:inline;'>
	<input type='hidden' name='mode' value='search'>
	<input type='hidden' name='cid2' value='$cid2'>
	<input type='hidden' name='depth' value='$depth'>
	<input type='hidden' name='bsmode' value='$bsmode'>
	<input type='hidden' name='bd_ix2' value='$bd_ix2'>
	<table cellpadding=0 cellspacing=0 border=0 width=100%>
	<tr>
		<td colspan=2>
			".GetTitleNavigation("브랜드목록", "상품관리 > 브랜드목록")."
		</td>
	</tr>
	<tr>
		<td align='left' colspan=4 style='padding-bottom:15px;'>
			 ".brand_tab("list")."
		</td>
	</tr>
	<tr>
		<td colspan=2>
			<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='input_table_box'>
				<col width='20%' />
				<col width='30%' />
				<col width='20%' />
				<col width='30%' />
				<tr>
					<td class='input_box_title'>  검색어  </td>
					<td class='input_box_item' colspan='3'>
						<table cellpadding=0 cellspacing=0 width=30%>
							<col width='80px'>
							<col width='*'>
							<tr>
								<td><select name='search_type'  style=\"font-size:12px;height:20px;\">
									<option value='brand_name'>브랜드명</option>
									</select>
								</td>
								<td style='padding-left:5px;'>
								<INPUT id=search_texts  class='textbox' value='".$search_text."' style=' FONT-SIZE: 12px; WIDTH: 90%; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
								
								</td>
								<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'></span></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class='input_box_title'>카테고리선택  <input type='button' name='search_brand_category' id='search_brand_category' value='검색' onclick=\"PoPWindow('./search_category.php?group_code=',600,600,'add_brand_category')\" style='cursor:pointer;'></td>
					<td class='input_box_item' colspan=3 >
						<div id='selected_category_6'  style='padding:10px 0px 10px 0px;'>
						<table width='100%' cellpadding='0' cellspacing='0' id='objMd'>
						<colgroup>
							<col width='*'>
							<col width='150'>
						</colgroup>
						<tbody>";
							if(count($cid) > 0){
								for($k=0;$k<count($cid);$k++){

									$re_cid = $cid[$k];
									$sql = "select * from shop_category_info where cid = '".$re_cid."'";
									$db->query($sql);
									$db->fetch();
									$depth = $db->dt[depth];
								
									for($i=0;$i<=$depth;$i++){
										$this_cid = substr(substr($re_cid, 0,($i*3+3)).'000000000000',0,15);
										$sql = "select * from shop_category_info where cid = '".$this_cid."'";
										$db2->query($sql);
										$db2->fetch();
										$cname = $db2->dt[cname];
										$relation_cname[$k] .= $cname." > ";
									}
				
									$Contents .= "<tr style='height:26px;' id='row_".$re_cid."'><td><input type='hidden' name='cid[]' id='cid_".$re_cid."' value='".$re_cid."'>".$relation_cname[$k]."</td><td><a href='javascript:void(0)' onclick=\"cid_del('".$re_cid."')\"><img src='../images/korea/btc_del.gif' border='0'></a></td></tr>";
								}
							}
				$Contents .= "
						</tbody>
						</table>
						</div>
					</td>
				</tr>
				<tr>
					<td class='input_box_title' style='font-size:12px;'>
					<b>브랜드 분류 <input type='button' name='search_brand_category' id='search_brand_category' value='검색' onclick=\"PoPWindow('./search_brand_category.php?group_code=',600,600,'add_brand_category')\" style='cursor:pointer;'></b>
					</td>
					<td class='input_box_item'  colspan='3'>
						<div id='selected_category_5' style='padding:10px 0px 10px 0px;'>
						<table width='100%' cellpadding='0' cellspacing='0' id='objDepartment'>
							<colgroup>
							<col width='*'>
							<col width='150'>
							</colgroup>
							<tbody>";
								if(count($bd_ix) > 0){
									$sql = "select * from shop_brand_div where bd_ix = '".$bd_ix[0]."'";
									$db2->query($sql);
									$data_array = $db2->fetchall();
								
									for($i=0;$i<count($data_array);$i++){
										if($data_array[$i][depth] == '2'){
											$sql = "select * from shop_brand_div where bd_ix = '".$data_array[$i][parent_bd_ix]."'";
											$db2->query($sql);
											$db2->fetch();
											$div1_name = $db2->dt[div_name];
									
											$Contents .= "<tr style='height:26px;' id='bd_row_".$data_array[$i][bd_ix]."'><td><input type='hidden' name='bd_ix' id='bd_ix_".$data_array[$i][bd_ix]."' value='".$data_array[$i][bd_ix]."'>".$div1_name." > ".$data_array[$i][div_name]."</td><td><a href='javascript:void(0)' onclick=\"bd_del('".$data_array[$i][bd_ix]."')\"><img src='../images/korea/btc_del.gif' border='0'></a></td></tr>";
										}else{
											$Contents .= "<tr style='height:26px;' id='bd_row_".$data_array[$i][bd_ix]."'><td><input type='hidden' name='bd_ix' id='bd_ix_".$data_array[$i][bd_ix]."' value='".$data_array[$i][bd_ix]."'>".$data_array[$i][div_name]."</td><td><a href='javascript:void(0)' onclick=\"bd_del('".$data_array[$i][bd_ix]."')\"><img src='../images/korea/btc_del.gif' border='0'></a></td></tr>";
										}
									}
								}
						
				$Contents .= "
							</tbody>
						</table>
						</div>
					</td><!-- loadRegion 를 loadBrandInfo 로 변경 kbk 13/07/01 -->
					
				</tr>
				<tr>
					<td class='search_box_title'> 사용여부 </td>
					<td class='search_box_item' colspan='3'>
						<input type='radio' name='disp'  id='disp_' value='' ".ReturnStringAfterCompare($disp, "", " checked")."><label for='disp_'>전체</label>
						<input type='radio' name='disp'  id='disp_1' value='1' ".ReturnStringAfterCompare($disp, "1", " checked")."><label for='disp_1'>사용</label>
						<input type='radio' name='disp'  id='disp_0' value='0' ".ReturnStringAfterCompare($disp, "0", " checked")."><label for='disp_0'>사용하지않음</label>
					</td>
				</tr>
				<tr>
					<td class='search_box_title'> 신청처리상태 </td>
					<td class='search_box_item' colspan='3'>
						<input type='radio' name='apply_status'  id='apply_status_' value='' ".ReturnStringAfterCompare($apply_status, "", " checked")."><label for='apply_status_'>전체</label>
						<input type='radio' name='apply_status'  id='apply_status_0' value='0' ".ReturnStringAfterCompare($apply_status, "0", " checked")."><label for='apply_status_0'>신청중</label>
						<input type='radio' name='apply_status'  id='apply_status_1' value='1' ".ReturnStringAfterCompare($apply_status, "1", " checked")."><label for='apply_status_1'>승인</label>
						<input type='radio' name='apply_status'  id='apply_status_2' value='2' ".ReturnStringAfterCompare($apply_status, "2", " checked")."><label for='apply_status_2'>승인보류</label>
						<input type='radio' name='apply_status'  id='apply_status_3' value='3' ".ReturnStringAfterCompare($apply_status, "3", " checked")."><label for='apply_status_3'>승인거부</label>
					</td>
				</tr>
				<tr height=30>
					<td class='input_box_title' ><label for='regdate'>브랜드 등록일</label><!--<input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.search_seller);' ".CompareReturnValue("1",$regdate,"checked").">--></td>
					<td class='input_box_item' colspan='3'>
						".search_date('sdate','edate',$sdate,$edate)."
					</td>
				</tr>

				";
$Contents .="</table>
		</td>
	</tr>
	<tr>
		<td colspan=2 align=center style='padding:20px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
	</tr>
	</table>
	</form>
	
	<form name='list_frm' method='POST' onsubmit='return BrandInput_lsit(this);' action='brand.act.php'  target='act'>
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

			".BrandList()."
		</td>
	</tr>
	</table>
	<iframe name='extand' id='extand' src='' width=0 height=0></iframe>";

	$help_text = "
	<div id='batch_update_category' ".($update_kind == "category" ? "style='display:block'":"style='display:'")." >
	<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>브랜드 카테고리 변경</b> <span class=small style='color:gray'><!--검색/선택된 회원에게 SMS 를 발송합니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F' )."</span></div>
	<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	<tr>
		<td class='input_box_title'>변경형태</td>
		<td class='input_box_item'>
			<input type='radio' name='update_category_type' id='update_category_type_add' value='add' checked> <label for='update_category_type_add'>카테고리 추가</label> &nbsp;&nbsp;
			<input type='radio' name='update_category_type' id='update_category_type_basic_add' value='basic_add'> <label for='update_category_type_basic_add'>기본카테고리 변경 또는 추가</label> &nbsp;&nbsp;
			<input type='radio' name='update_category_type' id='update_category_type_basic_del' value='basic_del'> <label for='update_category_type_basic_del'>기본카테고리 변경 (기본카테고리 삭제)</label> &nbsp;&nbsp;
		</td>
	</tr>
	<tr>
		<td class='input_box_title'>변경 카테고리</td>
		<td class='input_box_item'>
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

	<div id='batch_update_bd_category' ".($update_kind == "bd_category" ? "style='display:block'":"style='display:none'")." >
	<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>브랜드 분류 변경</b> <span class=small style='color:gray'><!--검색/선택된 회원에게 SMS 를 발송합니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F' )."</span></div>
	<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	<tr>
		<td class='input_box_title'>변경 브랜드 분류</td>
		<td class='input_box_item'>
			<table border=0 cellpadding=0 cellspacing=0>
				<tr>
					<td>
					".getBrandDivSelect('parent_bd_ix', '1차 브랜드 분류',$parent_bd_ix, $parent_bd_ix, 1, " onChange=\"loadBrandInfo(this,'bd_ix')\" validation='".$realestate_defailt_validation."' title='브랜드분류' class='property_info' ")."
						".getBrandDivSelect('bd_ix', '2차 브랜드 분류',$parent_bd_ix, $bd_ix, 2, "validation='".$realestate_defailt_validation."' title='브랜드 분류' class='property_info' onChange=\"loadBrandInfo2(this)\" ")."
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
	<!--<option value='1'>검색한 브랜드 전체</option>-->
		<option value='2'>선택한 브랜드 전체</option>
	</select>
	<input type='radio' name='update_kind' id='update_kind_group' value='category' ".(($update_kind == "category" || $update_kind == "") ? "checked":"")." onclick=\"ChangeUpdateForm('batch_update_category');\"><label for='update_kind_group'>카테고리 변경</label>
	<input type='radio' name='update_kind' id='update_kind_sms' value='bd_category'  ".(($update_kind == "bd_category" ) ? "checked":"")." onclick=\"ChangeUpdateForm('batch_update_bd_category');\"><label for='update_kind_sms'>브랜드 분류 변경</label>";
	$select .= "
	</nobr>";

	if($admininfo[mall_type] == "H"){
		$Contents .= "".HelpBox($select, $help_text, 520)."</form>";
	}else{
		$Contents .= "".HelpBox($select, $help_text, 750)."</form>";
	}

$Script = "
<SCRIPT type='text/javascript'>
<!--
	$(document).ready(function (){

		$('#max').change(function(){
			var value= $(this).val();
			$.cookie('brand_max_limit', value, {expires:1,domain:document.domain, path:'/', secure:0});
			document.location.reload();
			
		});

	});

	function cid_del(code){
		$('#row_'+code).remove();
	}

	function bd_del(code){
		$('#bd_row_'+code).remove();
	}

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
		for(i=0;i <= frm.cpid.length;i++){
			frm.cpid[i].checked = false;
		}
	}

	function checkAll(frm){

		for(i=0;i <= frm.cpid.length;i++){
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
//-->
</SCRIPT>



";

if($mmode == "pop"){
	$Script = "<script language='JavaScript' src='brand.js'></script>";
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
//	$P->OnloadFunction = "Init(document.brandform);MM_preloadImages('../webedit/images/wtool1_1.gif','../webedit/images/wtool2_1.gif','../webedit/images/wtool3_1.gif','../webedit/images/wtool4_1.gif','../webedit/images/wtool5_1.gif','../webedit/images/wtool6_1.gif','../webedit/images/wtool7_1.gif','../webedit/images/wtool8_1.gif','../webedit/images/wtool9_1.gif','../webedit/images/wtool11_1.gif','../webedit/images/wtool13_1.gif','../webedit/images/wtool10_1.gif','../webedit/images/wtool12_1.gif','../webedit/images/wtool14_1.gif','../webedit/images/bt_html_1.gif','../webedit/images/bt_source_1.gif')"; //showSubMenuLayer('storeleft');
	$P->OnloadFunction = "Init(document.brandform);";
	$P->Navigation = "상품관리 > 상품분류관리 > 브랜드목록";
	$P->NaviTitle = "브랜드목록";
	$P->strLeftMenu = product_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$Script = "<script language='JavaScript' src='brand.js'></script>".$Script;
	$P = new LayOut();
	$P->addScript = $Script;
	$P->OnloadFunction = ""; //showSubMenuLayer('storeleft');
	$P->Navigation = "상품관리 > 브랜드분류관리 > 브랜드목록";
	$P->title = "브랜드목록";
	$P->strLeftMenu = product_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}




function getCategoryListForBrand($category_text ="기본카테고리 선택", $object_name="cid",$id="cid", $onchange_handler="", $depth=0, $cid="")
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




function BrandList(){

global $db, $admininfo,$nset,$page,$search_text,$search_type, $cid, $depth, $bd_ix,$mode,$sdate,$edate,$apply_status;//$cid2, $depth, $bd_ix2 추가 kbk 13/07/01
global $auth_update_msg,$_COOKIE;

	if($_COOKIE[brand_max_limit]){
		$max = $_COOKIE[brand_max_limit]; //페이지당 갯수
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
		//$add_table=" left join ".TBL_SHOP_CATEGORY_INFO." mc on mb.cid = mc.cid";
	}else if($admininfo[mall_type] == "F" || $admininfo[mall_type] == "R"){ // 무료형 , 임대형
		$add_select="";
		$add_table="";
	}
	if($admininfo[admin_level] == "9"){
		$where = " where mb.b_ix IS NOT NULL ";
	}else{
		$where = " where mb.company_id = '".$admininfo[company_id]."' ";
	}

	if($search_text != "" && $search_type != ""){
		$where .= " and ".$search_type." LIKE '%".$search_text."%'";
	}


	if(count($cid) > 0){	//카테고리 다중검색
		$where .=" and (";
		for($i=0;$i<count($cid);$i++){
			if($i == count($cid) - 1){
				$where .= " br.cid like '".SetLikeCategory($cid[$i])."%'";
			}else{
				$where .= " br.cid like '".SetLikeCategory($cid[$i])."%' or ";
			}
		}
		$where .= ")";
	}

	if($apply_status !=""){
		$where.=" AND mb.apply_status='".$apply_status."' ";
	}

	if($disp !=""){
		$where.=" AND mb.disp='".$disp."' ";
	}

	if($bd_ix!="") {
		$where.=" AND mb.bd_ix='".$bd_ix."' ";
	}

	if($_GET[disp] !="" ){
		$where .="and mb.disp = '".$_GET[disp]."' ";
	}

    if($sdate!="" && $edate!="") {
        $where.=" AND mb.regdate between '".$sdate." 00:00:00' and '".$edate." 23:59:59'";
    }

	$sql = "SELECT count(mb.b_ix) as total  
				FROM shop_brand mb 
				left join shop_brand_div bd on mb.bd_ix = bd.bd_ix LEFT JOIN shop_brand_relation br ON mb.b_ix=br.b_ix AND br.basic='1' AND br.disp='1'
				".$where." 
				";

	$db->query($sql);
	$db->fetch();
	$total = $db->dt[total];
		$sql = "SELECT 
					DISTINCT mb.* ,
					bd.div_name,
					bd.parent_bd_ix, bd.depth, 
					count(*) as goods_cnt,
					br.cid  
				FROM 
					shop_brand mb 
					left join shop_brand_div bd on mb.bd_ix = bd.bd_ix
					LEFT JOIN shop_brand_relation br ON mb.b_ix=br.b_ix AND br.basic='1' AND br.disp='1'
				".$where." 
					group by b_ix  
					order by b_ix desc  
					limit $start,$max";

	//echo nl2br($sql);
	$db->query($sql);

	if($mode == "excel"){

		$goods_infos = $db->fetchall();
		$info_type = "list";
		include("excel_out_columsinfo.php");
		$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='brand_".$info_type."' ";
		$db->query($sql);
		$db->fetch();
		$stock_report_excel = $db->dt[conf_val];

		$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='brand_check_".$info_type."' ";
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
		if(is_array($check_colums)){
			foreach($check_colums as $key => $value){
				$inventory_excel->getActiveSheet(0)->setCellValue($col . "1", $columsinfo[$value][title]);
				$col++;
			}
		}

		$before_pid = "";

		for ($i = 0; $i < count($goods_infos); $i++)
		{
			$j="A";
			if(is_array($check_colums)){
				foreach($check_colums as $key => $value){
					if($key == "bd_ix"){
						if($goods_infos[$i][depth] == 2){
							$db->query("SELECT div_name FROM shop_brand_div WHERE bd_ix  = '".$goods_infos[$i][parent_bd_ix]."' ");
							$db->fetch(0);
							$value_str = $db->dt[div_name]." > ".$goods_infos[$i][div_name];
						}else{
							$value_str = $goods_infos[$i][div_name];
						}
					}else if($key == "cid"){
						//[Start] 카테고리명을 따로 불러옴 kbk 13/07/01
						$sql="SELECT cname FROM ".TBL_SHOP_CATEGORY_INFO." WHERE cid='".$goods_infos[$i][cid]."' ";
						$db->query($sql);
						if($db->total) {
							$db->fetch();
							$value_str=$db->dt["cname"];
						}
						//[End] 카테고리명을 따로 불러옴 kbk 13/07/01
					}else if($key == "apply_status"){
						switch($goods_infos[$i][apply_status]){
							case "0":
							$value_str = "신청중";
							break;
							case "1":
							$value_str = "승인";
							break;
							case "2":
							$value_str = "승인보류";
							break;
							case "3":
							$value_str = "승인거부";
							break;
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
			}
			$z++;
		}
		// 첫번째 시트 선택
		$inventory_excel->setActiveSheetIndex(0);

		// 너비조정
		$col = 'A';
		if(is_array($check_colums)){
			foreach($check_colums as $key => $value){
				$inventory_excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
				$col++;
			}
		}

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="member_'.$info_type.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'Excel5');
		$objWriter->save('php://output');

		exit;
	}

	//echo $total;
	$pagestring = page_bar($total, $page, $max, "&cid2=$cid2&depth=$depth&orderby=$orderby&disp=$disp&search_type=$search_type&search_text=$search_text&bd_ix2=$bd_ix2","");
	$mstring = "
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
	<col width='100px'>
	<col width='*'>
	<col width='120px'>
	<tr height=30 >
		<td>	<b>전체</b> : ".$total." 개
		</td>
		<td align=right>
		";
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$mstring .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
			<a href='excel_config.php?".$QUERY_STRING."&info_type=list&excel_type=brand_list_excel' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
		}else{
			$mstring .= "
			<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
		}

		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$mstring .= " <a href='brand_list.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}else{
			$mstring .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}

	$mstring .= "
		</td>
		<td align=right>
		목록수 : <select name='max' id='max'>
					<option value='5' ".($_COOKIE[brand_max_limit] == '5'?'selected':'').">5</option>
					<option value='10' ".($_COOKIE[brand_max_limit] == '10'?'selected':'').">10</option>
					<option value='20' ".($_COOKIE[brand_max_limit] == '20'?'selected':'').">20</option>
					<option value='30' ".($_COOKIE[brand_max_limit] == '30'?'selected':'').">30</option>
					<option value='50' ".($_COOKIE[brand_max_limit] == '50'?'selected':'').">50</option>
					<option value='100' ".($_COOKIE[brand_max_limit] == '100'?'selected':'').">100</option>
				</select>
		</td>
	</tr>
	</table>
	<table cellpadding=0 cellspacing=0 width=100% class='list_table_box'>
		<col width='5%'>
		<col width='5%'>
		<col width='10%'>
		<col width='20%'>
		<col width='10%'>
		<col width='15%'>
		<col width='*'>
		<col width='5%'>
		<col width='8%'>
		<col width='10%'>
		<tr height=28 bgcolor=#efefef align=center>
			<td class='s_td' ><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.list_frm)'></td>
			<td class='m_td' >번호</td>
			<td class='m_td' >등록일자</td>
			<td class='m_td' >브랜드 분류</td>
			<td class='m_td' >브랜드 코드</td>
			<!--td class='m_td'>코드</td-->";
		//	echo ($admininfo[mall_type]);
	if($admininfo[mall_type] == "B" || $admininfo[mall_type] == "O"){// 입점형 , 오픈마켓형
		$mstring .= "<td class='m_td' width='100'>카테고리</td>";
	}
		$mstring .= "<td class='m_td' width='*'>브랜드명</td>
			<td class='m_td' width='60'>신청상태</td>
			<td class='m_td' width='60'>상품수</td>
			<td class='m_td' width='60'>사용여부</td>
			<td class='e_td'>관리</td>
		</tr>";

	if ($db->total == 0)	{
		$mstring = $mstring."<tr height=100><td colspan=10 align=center>브랜드 리스트가 존재 없습니다.</td></tr>";
	}else{
		$brand_infos = $db->fetchall();
		for($i=0 ; $i < count($brand_infos) ; $i++)
		{
			$no = $total - ($page - 1) * $max - $i;

			if($brand_infos[$i][disp] == 1){
				$display_string = "사용";
			}else{
				$display_string = "사용안함";
			}

			if($brand_infos[$i][apply_status] == 1){
				$apply_status_string = "승인";
			}else if($brand_infos[$i][apply_status] == 2){
				$apply_status_string = "신청중";
			}else if($brand_infos[$i][apply_status] == 3){
				$apply_status_string = "신청보류";
			}else{
				$apply_status_string = "승인거부";
			}

			if($brand_infos[$i][search_disp] == 1){
				$search_disp_string = "표시";
			}else{
				$search_disp_string = "표시하지않음";
			}

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				if($admininfo[admin_level] == 9){
					$brand_name = "<a href=\"brand.php?b_ix=".$brand_infos[$i][b_ix]."\">".$brand_infos[$i][brand_name]."</a>";
				}else if($admininfo[admin_level] == 8){
					if($admininfo[company_id] == $brand_infos[$i][company_id]){
						$brand_name = "<a href=\"brand.php?b_ix=".$brand_infos[$i][b_ix]."\"><u>".$brand_infos[$i][brand_name]."</u></a>";
					}else{
						$brand_name = $brand_infos[$i][brand_name];
					}
				}
			}else{
				$brand_name = "<a href=\"".$auth_update_msg."\"><u>".$brand_infos[$i][brand_name]."</u></a>";
			}

			if($brand_infos[$i][depth] == 2){
				$db->query("SELECT div_name FROM shop_brand_div WHERE bd_ix  = '".$brand_infos[$i][parent_bd_ix]."' ");
				$db->fetch(0);
				$div_name = $db->dt[div_name]." > ".$brand_infos[$i][div_name];
			}else{
				$div_name = $brand_infos[$i][div_name];
			}

			//[Start] 카테고리명을 따로 불러옴 kbk 13/07/01
			$sql="SELECT cname FROM ".TBL_SHOP_CATEGORY_INFO." WHERE cid='".$brand_infos[$i][cid]."' ";
			$db->query($sql);
			if($db->total) {
				$db->fetch();
				$cate_name=$db->dt["cname"];
			}
			//[End] 카테고리명을 따로 불러옴 kbk 13/07/01

			$mstring = $mstring."<tr height=33 align=center>
				<td class='list_box_td'><input type=checkbox class=nonborder id='cpid' name=cpid[] value='".$brand_infos[$i][b_ix]."'></td>
				<td class='list_box_td'>".$no."</td>
				<td class='list_box_td'>".substr($brand_infos[$i][regdate],0,10)."</td>
				<td>".$div_name."</td>
				<td class='list_box_td'>".$brand_infos[$i][b_ix]." ( ".$brand_infos[$i][brand_code]." )</td>
				<!--td class='list_box_td'><a href=\"JavaScript:ViewBrandImage('".$brand_infos[$i][b_ix]."')\">".$brand_infos[$i][b_ix]."</a></td-->";
			if($admininfo[mall_type] == "B" || $admininfo[mall_type] == "O"){// 입점형
				//$mstring .="<td class='list_box_td'>".($brand_infos[$i][cname] == "" ? "전체":$brand_infos[$i][cname])."</td>";
				$mstring .="<td class='list_box_td'>".($cate_name == "" ? "전체":$cate_name)."</td>";//카테고리명 수정 kbk 13/07/01
			}
				$mstring .="<td class='list_box_td point' align=center>$brand_name</td>
				<td>".$apply_status_string."</td>
				<td>".$brand_infos[$i][goods_cnt]."</td>
				<td>".$display_string."</td>
				<td>
					<a href=\"brand.php?b_ix=".$brand_infos[$i][b_ix]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
				<a href=\"javascript:deleteBrandInfo('delete','".$brand_infos[$i][b_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
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
?>