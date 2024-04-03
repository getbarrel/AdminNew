<?
include("../class/layout.class");
include("../webedit/webedit.lib.php");
include_once("brand.lib.php");

$db = new Database;
$db2 = new Database;

if($_COOKIE[mandatory_max_limit]){
	$max = $_COOKIE[mandatory_max_limit]; //페이지당 갯수
}else{
	$max = 20;
}

if($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

if($_GET[disp] !="" ){
	$where .="and mb.disp = '".$_GET[disp]."' ";
}

$sdate = str_replace("/","",$sdate);
$edate = str_replace("/","",$edate);

if($search_text != "" && $search_type){
	$where = " and mandatory_name like '%".$search_text."%'";

}

if($sdate != "" && $edate != ""){
	if($db->dbms_type == "oracle"){
		$where .= " and  to_char(regdate_ , 'YYYY-MM-DD') between '".$sdate."' and '".$edate."' ";
	}else{
		$where .= " and  date_format(regdate,'%Y-%m-%d') between '".$sdate."' and '".$edate."' ";
	}
}

if($mall_ix != ""){
    $where .= "and mall_ix = '".$mall_ix."'";
}

$sql = "SELECT
			count(mi_ix) as total  
		FROM 
			shop_mandatory_info
		where
			1
			$where
			";
$db->query($sql);
$db->fetch();
$total = $db->dt[total];

$sql = "SELECT 
			*
		FROM 
			shop_mandatory_info
		where
			1
			$where
			order by regdate desc  
			limit $start,$max";

$db->query($sql);

$pagestring = page_bar($total, $page, $max, "&cid2=$cid2&depth=$depth&orderby=$orderby&disp=$disp&search_type=$search_type&search_text=$search_text&bd_ix2=$bd_ix2","");

$Contents = "

	<table cellpadding=0 cellspacing=0 border=0 width=100%>
	<tr>
		<td colspan=2>
			".GetTitleNavigation("상품고시정보", "상품관리 > 상품고시정보")."
		</td>
	</tr>
	
	</table>
	<form name='search_form' method='get' action='product_mandatory_list.php' onsubmit='return CheckFormValue(this);' style='display:inline;'>
	<input type='hidden' name='mode' value='search'>
	<table cellpadding=0 cellspacing=0 border=0 width=100%>
	<tr>
		<td colspan=2>
			<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='input_table_box'>
				<col width='20%' />
				<col width='30%' />
				<col width='20%' />
				<col width='30%' />
				<tr>
                    <td class='search_box_title' > 프론트 전시 구분</td>
                    <td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
                </tr>
				<tr>
					<td class='input_box_title'> 검색어  </td>
					<td class='input_box_item' colspan='3'>
						<table cellpadding=0 cellspacing=0 width=30%>
							<col width='80px'>
							<col width='*'>
							<tr>
								<td>
									<select name='search_type'  style=\"font-size:12px;height:20px;\">
									<option value='company_name'>상품고시명</option>
									</select>
								</td>
								<td style='padding-left:5px;'>
									<INPUT id=search_texts  class='textbox' value='".$search_text."' style=' FONT-SIZE: 12px; WIDTH: 90%; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
								</td>
								<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'></span></td>
							</tr>
						</table>
					</td>
				</tr>
				<!--
				<tr>
					<td class='search_box_title'> 사용여부 </td>
					<td class='search_box_item' colspan='3'>
						<input type='radio' name='disp'  id='disp_' value='' ".ReturnStringAfterCompare($disp, "", " checked")."><label for='disp_'>전체</label>
						<input type='radio' name='disp'  id='disp_1' value='1' ".ReturnStringAfterCompare($disp, "1", " checked")."><label for='disp_1'>사용</label>
						<input type='radio' name='disp'  id='disp_0' value='0' ".ReturnStringAfterCompare($disp, "0", " checked")."><label for='disp_0'>사용하지않음</label>
					</td>
				</tr>
				-->
				<tr height=30>
					<td class='input_box_title' ><label for='regdate'>등록일</label><!--<input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.search_seller);' ".CompareReturnValue("1",$regdate,"checked").">--></td>
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
	
	<form name='list_frm' method='POST' onsubmit='return CompanyInput_list(this);' action='product_mandatory.act.php'  target='act'>
		<input type='hidden' name='code[]' id='code'>
		<input type='hidden' name=before_update_kind value='".$update_kind."'>
		<input type='hidden' name=update_kind value='".$update_kind."'>
		<input type='hidden' name='group_by' value='".$group_by."'>
		<input type='hidden' name='start' value='".$start."'>
		<input type='hidden' name='cid2' value='$cid2'>
		<input type='hidden' name='depth' value='$depth'>
		<input type='hidden' name='bd_ix2' value='$bd_ix2'>";

$Contents .= "
		<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
		<col width='17%'>
		<col width='71'>
		<col width='12%'>
		<tr height=30 >
			<td><b>전체</b> : ".$total." 개
			</td>
			<td align=right>
			목록수 : <select name='max' id='max'>
						<option value='5' ".($_COOKIE[mandatory_max_limit] == '5'?'selected':'').">5</option>
						<option value='10' ".($_COOKIE[mandatory_max_limit] == '10'?'selected':'').">10</option>
						<option value='20' ".($_COOKIE[mandatory_max_limit] == '20'?'selected':'').">20</option>
						<option value='30' ".($_COOKIE[mandatory_max_limit] == '30'?'selected':'').">30</option>
						<option value='50' ".($_COOKIE[mandatory_max_limit] == '50'?'selected':'').">50</option>
						<option value='100' ".($_COOKIE[mandatory_max_limit] == '100'?'selected':'').">100</option>
					</select>
			</td>
		</tr>
		</table>

		<table cellpadding=0 cellspacing=0 width=100% class='list_table_box'>
			<col width='3%'>
			<col width='5%'>
			<col width='5%'>
			<col width='10%'>
			<col width='15%'>
			<col width='*'>
			<col width='10%'>
			<col width='10%'>
			<tr height=25 bgcolor=#efefef align=center>
				<td class='s_td' ><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.list_frm)'></td>
				<td class='m_td' >번호</td>
				<td class='m_td' >전시</td>
				<td class='m_td' >코드</td>
				<td class='m_td' >등록일</td>
				<td class='m_td' width='*'>상품고시명</td>
				<td class='m_td' >사용여부</td>
				<td class='e_td'>관리</td>
			</tr>";

		if ($db->total == 0)	{
			$Contents = $Contents."<tr height=100><td colspan=11 align=center>상품고시 정보가 없습니다.</td></tr>";
		}else{
			$mandatory_array = $db->fetchall();
			for($i=0 ; $i < count($mandatory_array) ; $i++)
			{
				$no = $total - ($page - 1) * $max - $i;

				if($mandatory_array[$i][is_use] == 1){
					$display_string = "사용";
				}else{
					$display_string = "사용안함";
				}

				$Contents = $Contents."<tr height=27 align=center>
					<td class='list_box_td'><input type=checkbox class=nonborder id='cpid' name=cpid[] value='".$mandatory_array[$i][mi_ix]."'></td>
					<td class='list_box_td'>".$no."</td>
					<td class='list_box_td'>".GetDisplayDivision($mandatory_array[$i]['mall_ix'], "text")."</td>
					<td class='list_box_td'>".$mandatory_array[$i][mi_code]."</td>
					<td class='list_box_td'>".$mandatory_array[$i][regdate]."</td>
					<td>".$mandatory_array[$i][mandatory_name]."</td>
					<td class='list_box_td'>".$display_string."</td>
					<td class='list_box_td' style='padding:2px;'>
						<a href=\"javascript:ShowModalWindow('../product/product_mandatory_add.php?type=update&mi_ix=".$mandatory_array[$i][mi_ix]."',950,680,'mandatory_update')\">
						<img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0>
						</a>
						<a href=\"javascript:deleteMandatory('delete','".$mandatory_array[$i][mi_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
					</td>
					</tr>";
			}
		}

$Contents .= "
		</table>";

$Contents .= "
		<table width='100%' border='0' cellpadding='0' cellspacing='0'>
		<tr height=30  align='right' >
			<td>".$pagestring."</td>
		</tr>
		</table>
		<br>
		<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
		<col width='17%'>
		<col width='71'>
		<col width='12%'>
		<tr height=30 >
			<td></td>
			<td align=right>
			<a href=\"javascript:ShowModalWindow('../product/product_mandatory_add.php?type=add',900,400,'mandatory_add')\">
			<img src='/admin/images/korean/btn_admin_add.gif' border=0>
			</a>
			</td>
		</tr>
		</table>";

$Contents .= HelpBox($help_text, '상품고시 정보 설명');
	
$Script = "
<SCRIPT type='text/javascript'>
<!--
	$(document).ready(function (){

		$('#max').change(function(){
			var value= $(this).val();
			$.cookie('mandatory_max_limit', value, {expires:1,domain:document.domain, path:'/', secure:0});
			document.location.reload();
			
		});

	});

	function deleteMandatory(mode, mi_ix){
		if(confirm('해당 고시 정보를 정말로 삭제하시겠습니까?')){
			window.frames['act'].location.href = './product_mandatory.act.php?act=delete&mi_ix='+mi_ix;
		}
	}
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

	function loadBrandInfo(sel,target) {//brand.php 에 있던 것을 brand_list.php 에서 같이 사용하기 위해 여기로 옮겨옴 kbk 13/07/01

		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;

		//var depth = sel.getAttribute('depth');
		//document.write('company.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2');
		window.frames['act'].location.href = './company.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

	}

	function loadBrandInfo2(sel) {

		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form;
		if(trigger!=''){
			form.bd_ix2.value=trigger;
		}else{
			form.bd_ix2.value=form.parent_bd_ix.value;
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
</SCRIPT>";

if($mmode == "pop"){
	$Script = "<script language='JavaScript' src='company.js'></script>".$Script;
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->OnloadFunction = "Init(document.brandform);";
	$P->Navigation = "상품관리 > 상품고시정보 > 고시정보 목록";
	$P->NaviTitle = "상품고시정보";
	$P->strLeftMenu = product_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$Script = "<script language='JavaScript' src='company.js'></script>".$Script;
	$P = new LayOut();
	$P->addScript = $Script;
	$P->OnloadFunction = ""; //showSubMenuLayer('storeleft');
	$P->Navigation = "상품관리 > 상품고시정보 > 제조사목록";
	$P->title = "상품고시정보";
	$P->strLeftMenu = product_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}


?>