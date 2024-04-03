<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include("../class/layout.class");

$db = new Database;
$db2 = new Database;



$Script = "
<script language='JavaScript' >

if(window.dialogArguments){

	var opener = window.dialogArguments;
}else{

	var opener = window.opener;
}

function CheckSMS(frm){

	if(frm.sms_contents.value.length < 1){
		alert('SMS 내용을 입력해주세요');
		return false;
	}

	if(frm.mobiles.value.length < 1){
		alert('SMS 보낼 셀러이 한명이상이어야 합니다.');
		return false;
	}

	return true;
}

function CheckSearch(frm){
	if(frm.search_text.value.length < 1){
		alert('검색어를 입력해주세요');
		return false;
	}
}

function SearchCharger(md_code,md_name){
//	alert($('#charger_ix',opener.document).parent().html());
	$('#md_code".($group_code == "" ? "":"_".$group_code)."',opener.document).val(md_code);
	$('#md_name".($group_code == "" ? "":"_".$group_code)."',opener.document).val(md_name);
	self.close();
}

function changeBgColor(obj){
	var objTop = obj.parentNode.parentNode;	
	for(j=0;j < objTop.rows.length;j++){
		$(objTop.rows[j]).find('td').each(function(){
			$(this).css('background-color','');	
		});
	}
	$(obj).find('td').css('background-color','#f9ded1');
}

</Script>";

$Contents = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<tr>
		<td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "입점업체 관리 > $menu_name")."</td>
	</tr>
	<tr>
		<td align='left' colspan=4 style='padding-bottom:20px;'>
		<div class='tab'>
			<table class='s_org_tab'>
			<col width='850px'>
			<col width='*'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".(($info_type == "basic" || $info_type == "") ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'><a href='?info_type=basic&mmode=$mmode&code=$code'>분류별 선택</a> </td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($info_type == "search" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'><a href='?info_type=search&mmode=$mmode&code=$code'>검색어 검색</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
				<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'>
				</td>
			</tr>
			</table>
		</div>
		</td>
	</tr>
	</table>";

if($info_type == "" || $info_type == "basic"){
$Contents .= "
<table cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<tr>
		<td align=center colspan=2>
			<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<col width='50%'>
<col width='50%'>
			<tr >
				<td class='p11 ls1' style='padding:0 0 5px 5px;' nowrap><b> 1차분류 </b></td>
				<td class='p11 ls1' style='padding:0 0 5px 5px;' nowrap><b> 2차분류 </b></td>
			</tr>
			<tr>
				<td>
					<select name='group_list' id='group_list' style='border:solid 1px #ddd;width:98%;height:148px;font-size:12px;background:#fff;' class='participation' multiple>";
					$db->query("select * from common_origin_div where  depth ='1'");
					$group_array = $db->fetchall();
					for($i=0;$i<count($group_array); $i++){
						$Contents .= "<option value='".$group_array[$i][od_ix]."'>".$group_array[$i][div_name]."</option>";
					}
	$Contents .= "
					</select>
				</td>
				<td>
					<select name='department_list' id='department_list' style='border:solid 1px #ddd;width:98%;height:148px;font-size:12px;background:#fff;' class='participation' multiple>

					</select>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr height=50 align='center'>
		<td colspan='2'><input type='button' value='리스트 담기' onclick=\"add_list();\"></td>
	</tr>
	<tr height=30>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap><b> > 추가 리스트 </b></td>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> </td>
	</tr>
	<tr>
		<td colspan=2 style='padding: 0 5px 0 5px' width=100% valign=top>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
		<tr>
			<td>
				<select name='add_list' id='add_list' style='border:solid 1px #ddd;width:100%;height:100px;font-size:12px;background:#fff;' class='participation' multiple>

				</select>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style='padding-top:10px;'><span class='small blu'> * '더블클릭' 하시면 '추가리스트'에서 삭제됩니다.</span></td>
	</tr>
	<tr height=50 align='center'>
		<td colspan='2'><input type='button' value='추가하기' onclick=\"add_department();\"></td>
	</tr>
</TABLE>
";

}

if($info_type == "search"){

if($mode == 'search'){
	if($search_text != ""){

		if(strpos($search_text,',') == false){
			$where = " and div_name like '%".$search_text."%'";
		}else{
			$search_text_array = explode(",",$search_text);
			$where .=" and (";
			for($i=0;$i<count($search_text_array);$i++){
				if($i == count($search_text_array) -1){
					$where .= " div_name like '%".$search_text_array[$i]."%' ";
				}else{
					$where .= " div_name like '%".$search_text_array[$i]."%' or ";
				}
			}
			$where .= ")";
		}
	}

	$sql = "select
			*
			from
				common_origin_div
			where
				1
				$where
				order by od_ix  ASC";
	$db->query($sql);
	$data_array = $db->fetchall();

}
$Contents .= "
<table cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<tr>
		<td align=center >
		<form name='search_frm' action='./search_origin_category.php' method='get' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;' target=''>
		<input type='hidden' name='mode' value='search'>
		<input type='hidden' name='info_type' value='search'>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center' class='input_table_box'>
		<col width='20%'>
		<col width='*'>
		<tr>
			<td class='input_box_title' style='padding:0 0 5px 5px;' nowrap>
				검색어
			</td>
			<td class='input_box_item' nowrap>
				 <input type='text' class='textbox' name='search_text' value='".$search_text."'> <input type='image' src='../v3/images/korea/btn_search.gif' align='absmiddle' style='cursor:pointer;'>
			</td>
		</tr>
		</table><br>

		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
		<tr>
			<td>
				<select name='group_department' id='group_department' style='border:solid 1px #ddd;width:100%;height:148px;font-size:12px;background:#fff;' class='participation' id='department'  multiple>";
				for($i=0;$i<count($data_array); $i++){
					if($data_array[$i][depth] == '2'){
						$sql = "select * from common_origin_div where od_ix = '".$data_array[$i][parent_od_ix]."'";
						$db2->query($sql);
						$db2->fetch();
						$div1_name = $db2->dt[div_name];
						$Contents .= "<option value='".$data_array[$i][od_ix]."'>".$div1_name." > ".$data_array[$i][div_name]."</option>";
					}else{
						$Contents .= "<option value='".$data_array[$i][od_ix]."'>".$data_array[$i][div_name]."</option>";
					}
				}
$Contents .= "
				</select>
			</td>
		</tr> 
		</table>
		</from>
		</td>
	</tr>
	<tr height=30 style='padding:0 0 0 5px;'>
		<td ><span class='small blu'> * 원산지 분류 추가시 해당 분류에 '더블클릭' 하시면 '추가리스트'에 추가됩니다.</td>
	</tr>
	<tr style='padding:0 0 0 5px;'>
		<td ><span class='small blu'> * 한번에 여러 원산지 분류를 검색하시려면 검색어위에 ','를 분여 구분해주시면 됩니다. Ex:)신발,의류,기타 </td>
	</tr>
	<tr height=30>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap><b> > 추가 리스트 </b></td>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> </td>
	</tr>
	<tr>
		<td width=100% valign=top>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
		<tr>
			<td>
				<select name='add_list' id='add_list' style='border:solid 1px #ddd;width:100%;height:100px;font-size:12px;background:#fff;' class='participation' id='department'  multiple> 
				</select>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style='padding-top:10px;'><span class='small blu'> * '더블클릭' 하시면 '추가리스트'에서 삭제됩니다.</span></td>
	</tr>
	<tr height=50 align='center'>
		<td colspan='2'><input type='button' value='추가하기' onclick=\"add_department();\"></td>
	</tr>
</TABLE>
";
}


$Script = "
<SCRIPT type='text/javascript'>
<!--
	$(document).ready(function(){	//검색어 검색 관련
		$('#group_department').dblclick(function(){
			var value = $('#group_department').find('option:selected').val();
			var text = $('#group_department').find('option:selected').text();
			
			if(value){
				$('#add_list option').remove();
				$('#add_list').append('<option value='+value+' selected>'+text+'</option>');
			}
		});
	});
	

	$(document).ready(function(){	//부서 선택
		$('#group_list').dblclick(function(){
			var od_ix = $('#group_list').find('option:selected').val();
		
			if(od_ix){
				$.ajax({
				    url : './origin.act.php',
				    type : 'POST',
				    data : {od_ix:od_ix,
							mode:'select_depth2'
							},
				    dataType: 'json',
				    error: function(data,error){// 실패시 실행함수 
				        alert(error);},
				    success: function(args){
						if(args){
							$.each(args, function(index, entry){
								$('#department_list').append(\"<option value=\"+index+\">\"+entry+\"</option>\");
							});
						}else{
							$('#department_list').empty();
							alert('2차 분류가 없습니다.');
						}
		        	}
		    	});
			}else{
				alert('1차분류를 선택해 주세요.');
			}
		});

		$('#add_list').dblclick(function(){
			
			var selected_value = $(this).val();
			$('#add_list option').each(function(){
				if($(this).val() == selected_value){
					$(this).remove();
				}
			});
		});
	});

function add_list(){
	$('#add_list').empty();

	if($('#department_list option:selected').length > 0){
		$('#add_list').append('<option value='+$('#department_list option:selected').val()+' selected>'+$('#department_list option:selected').html()+'</option>');
	}else{
		$('#add_list').append('<option value='+$('#group_list option:selected').val()+' selected>'+$('#group_list option:selected').html()+'</option>');
	}
}

function add_department(){
	var tbody = $('#objDepartment tbody',opener.document);
	var tbody_tr = $('#objDepartment tbody tr',opener.document);
	var thisRow = tbody.find('tr[depth^=1]:last');

	tbody_tr.remove();
	$('#add_list option').each(function(){
		var dp_ix = $(this).val();
		var dp_text = $(this).html();
		var add_html = \"<tr style='height:26px;' id='od_row_\"+dp_ix+\"'><td><input type=hidden name=od_ix id='od_ix_\"+dp_ix+\"' value='\"+dp_ix+\"'>\"+dp_text+\"</td><td><a href='javascript:void(0)' onClick='od_del(\"+dp_ix+\")'><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0></a></td></tr>\";
		tbody.append(add_html);
	});

	self.close();
}


//-->
</SCRIPT>

";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "원산지 분류 선택";
$P->NaviTitle = "원산지 분류 선택";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>