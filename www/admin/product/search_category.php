<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include("../class/layout.class");

$db = new Database;

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
		<td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "상품관리 > $menu_name")."</td>
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
						<td class='box_02'><a href='?info_type=basic&mmode=$mmode&code=$code'>카테고리 선택</a> </td>
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
			<col width='19%'>
			<col width='19%'>
			<col width='19%'>
			<col width='19%'>
			<col width='19%'>

			<tr >
				<td class='p11 ls1' style='padding:0 0 5px 5px;' nowrap><b> 1뎁스 </b></td>
				<td class='p11 ls1' style='padding:0 0 5px 5px;' nowrap><b> 2뎁스 </b></td>
				<td class='p11 ls1' style='padding:0 0 5px 5px;' nowrap><b> 3뎁스 </b></td>
				<td class='p11 ls1' style='padding:0 0 5px 5px;' nowrap><b> 4뎁스 </b></td>
				<td class='p11 ls1' style='padding:0 0 5px 5px;' nowrap><b> 5뎁스 </b></td>
			</tr>
			<tr>
				<td>
					<select name='category_list1' id='category_list1' style='border:solid 1px #ddd;width:98%;height:148px;font-size:12px;background:#fff;' onclick=\"get_cagetory_info('category_list1','category_list2')\" class='participation' multiple>";
					$db->query("select * from shop_category_info where depth='0' order by cid ASC");
					$category_array = $db->fetchall();
					for($i=0;$i<count($category_array); $i++){
						$Contents .= "<option value='".$category_array[$i][cid]."'>".$category_array[$i][cname]."</option>";
					}
	$Contents .= "
					</select>
				</td>
				<td>
					<select name='category_list2' id='category_list2' style='border:solid 1px #ddd;width:98%;height:148px;font-size:12px;background:#fff;' onclick=\"get_cagetory_info('category_list2','category_list3')\"  class='participation' multiple>

					</select>
				</td>
				<td>
					<select name='category_list3' id='category_list3' style='border:solid 1px #ddd;width:98%;height:148px;font-size:12px;background:#fff;' onclick=\"get_cagetory_info('category_list3','category_list4')\"  class='participation' multiple>

					</select>
				</td>
				<td>
					<select name='category_list4' id='category_list4' style='border:solid 1px #ddd;width:98%;height:148px;font-size:12px;background:#fff;' onclick=\"get_cagetory_info('category_list4','category_list5')\"  class='participation' multiple>

					</select>
				</td>
				<td>
					<select name='category_list5' id='category_list5' style='border:solid 1px #ddd;width:98%;height:148px;font-size:12px;background:#fff;' class='participation' multiple>

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
		if(strpos($search_text,',') === false){
			$where = " and cname like '%".$search_text."%'";
		}else{
			$search_text_array = explode(",",$search_text);
			$where .=" and (";
			for($i=0;$i<count($search_text_array);$i++){
				if($i == count($search_text_array) -1){
					$where .= " cname like '%".$search_text_array[$i]."%' ";
				}else{
					$where .= " cname like '%".$search_text_array[$i]."%' or ";
				}
			}
			$where .= ")";
		}
	}

	$sql = "select
				depth,
				cid,
				cname
			from
				shop_category_info
			where
				1
				$where
				order by cid  ASC";
				//echo nl2br($sql);
	$db->query($sql);
	$data_array = $db->fetchall();

}

$Contents .= "
<table cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<tr>
		<td align=center >
		<form name='search_frm' action='./search_category.php' method='get' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;' target=''>
		<input type='hidden' name='mode' value='search'>
		<input type='hidden' name='info_type' value='search'>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center' class='input_table_box'>
		<col width='20%'>
		<col width='*'>
		<tr>
			<td  class='input_box_title' style='padding:0 0 5px 5px;' nowrap>
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
					$Contents .= "<option value='".$data_array[$i][cid]."'>".GetParentCategory_2($data_array[$i][cid],$data_array[$i][depth])."</option>";
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
		<td ><span class='small blu'> * 카테고리 추가시 카테고리에 '더블클릭' 하시면 '추가리스트'에 추가됩니다.</td>
	</tr>
	<tr style='padding:0 0 0 5px;'>
		<td ><span class='small blu'> * 한번에 여러 카테고리를 검색하시려면 검색어위에 ','를 분여 구분해주시면 됩니다. Ex:)신발,의류,기타 </td>
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
				$('#add_list').append('<option value='+value+' selected>'+text+'</option>');
			}
		});
	});
	
	function get_cagetory_info(id,target_id){

		$('#'+id).dblclick(function(){
			var cid = $('#'+id).find('option:selected').val();
			if(cid){
				$.ajax({
				    url : './category.save.php',
				    type : 'POST',
				    data : {cid:cid,
							mode:'select_category_info'
							},
				    dataType: 'json',
				    error: function(data,error){// 실패시 실행함수 
				        alert(error);},
				    success: function(args){

						if(args){
							$('#'+target_id).empty();
							$.each(args, function(index, entry){
								$('#'+target_id).append(\"<option value=\"+index+\">\"+entry+\"</option>\");
							});
						}else{
							$('#'+target_id).empty();
							//alert('하위 분류가 없습니다.');
						}
		        	}
		    	});
			}else{
				alert('분류를 선택해 주세요.');
			}
		});
	}

	$(document).ready(function(){	//부서 선택

		$('#department_list').click(function(){
			var dp_ix = $('#department_list').find('option:selected').val();
		
			if(dp_ix){
				$.ajax({
				    url : './category.save.php',
				    type : 'POST',
				    data : {dp_ix:dp_ix,
							mode:'select_position_md'
							},
				    dataType: 'json',
				    error: function(data,error){// 실패시 실행함수 
				        alert(error);},
				    success: function(args){

						if(args){
							$('#person_list').empty();
							$.each(args, function(index, entry){
								$('#person_list').append(\"<option value=\"+index+\">\"+entry+\"</option>\");
							});
						}else{
							alert('해당 담당자가 없습니다.');
						}
		        	}
		    	});
			}else{
				alert('본사를 선택해 주세요.');
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
	
	var i = 5;
	for(i=5;i>=1;i--){
		var check_len = $('#category_list'+i+' option:selected').length;

		if(check_len > 0){
			$('#category_list'+i+' option:selected').each(function(){
				var cid = $(this).val();
				$.ajax({
				    url : './category.save.php',
				    type : 'POST',
				    data : {cid:cid,
							mode:'get_category_name'
							},
				    dataType: 'json',
				    error: function(data,error){// 실패시 실행함수 
				        alert(error);},
				    success: function(args){
						if(args){
							//$('#person_list').empty();
							$.each(args, function(index, entry){
								//alert(entry);
								$('#add_list').append('<option value='+cid+' selected>'+entry+'</option>');
							});
						}else{
							alert('해당 담당자가 없습니다.');
						}
		        	}
		    	});
			});
			break; 
		}
	}

}

function add_department(){
	var tbody = $('#objMd tbody',opener.document);
	var thisRow = tbody.find('tr[depth^=1]:last');
	$('#add_list option').each(function(){
		var dp_ix = $(this).val();
		var dp_text = $(this).html();
		//alert(dp_ix);
		var add_html = '<tr style=\"height:26px;\" id=\"row_'+dp_ix+'\"><td><input type=hidden name=cid[] id=\"cid_'+dp_ix+'\" value=\"'+dp_ix+'\">'+dp_text+'</td><td><a href=\"javascript:void(0)\" onClick=\"cid_del(\''+dp_ix+'\')\"><img src=\"/admin/images/".$_SESSION["admininfo"]["language"]."/btc_del.gif\" border=0></a></td></tr>';
		tbody.append(add_html);
	});
	self.close();
}


//-->
</SCRIPT>

";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "카테고리선택";
$P->NaviTitle = "카테고리선택";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>