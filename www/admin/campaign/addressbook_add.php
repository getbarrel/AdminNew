<?
include("../class/layout.class");

$db = new Database;


$sql = 	"SELECT ab.*, abg.group_depth , case when abg.group_depth = 1 then abg.group_ix else parent_group_ix end as parent_group_ix
				FROM shop_addressbook ab, shop_addressbook_group abg
				where ab_ix ='$ab_ix' and ab.group_ix = abg.group_ix ";

//echo $sql;
$db->query($sql);
$db->fetch();

if($db->total){
	$act = "update";
}else{
	$act = "insert";
}


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6 style='padding-left:20px;padding-bottom:0px;'> ".GetTitleNavigation("메일링/SMS 주소록 등록관리", "메일링/SMS > 메일링/SMS 주소록 등록관리 ")."</td>
	  </tr>
	  <tr >
	    <td align='left' colspan=4 >";
if($mmode == ""){
$Contents01 .= "
	    <div class='tab'>
			<table class='s_org_tab'>
			<tr>
				<td class='tab'>
							<table id='tab_02'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='addressbook_list.php'\">주소록 리스트</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03' class='on'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='addressbook_add.php'\">주소록 개별등록</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_04'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='addressbook_add_excel.php'\">주소록 대량등록</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_01'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='addressbook_group.php'\" >주소록 그룹관리</td>
								<th class='box_03'></th>
							</tr>
							</table>
				<td class='btn'>

				</td>
			</tr>
			</table>
		</div>";
}
$Contents01 .= "
		<div class='t_no' style=' padding:5px 0px; '>
			<!-- my_movie start -->
			<div class='my_box'>
				<form name='div_form' action='addressbook.act.php' method='post' onsubmit='return CheckForm(this)' target='action_frame'>
				<input name='act' type='hidden' value='$act'>
				<input name='ab_ix' type='hidden' value='$ab_ix'>
					<table width=100% cellpadding=5 cellspacing=0 border=0 style='margin-top:10px;' class='input_table_box'>
					<col width=10%>
					<col width=40%>
					<col width=10%>
					<col width=40%>
					<tr>
				    <td class='input_box_title'><b>그룹설정</b><img src='".$required3_path."'></td>
				    <td class='input_box_item'>
				    	".getCampaignGroupInfoSelect('parent_group_ix', '1 차그룹',$db->dt[parent_group_ix], $db->dt[parent_group_ix], 1, " onChange=\"loadCampaignGroup(this,'group_ix')\" ")."
				    	".getCampaignGroupInfoSelect('group_ix', '2 차그룹',$db->dt[parent_group_ix], $db->dt[group_ix], 2)."
				    </td>
					<td class='input_box_title'><b> 회원구분</b><img src='".$required3_path."'></td>
					<td class='input_box_item'>
						<input type=radio name='mem_type' id='mem_type_user' value='M' ".(($db->dt[mem_type] == "M" || $db->dt[mem_type] == "") ? "checked":"")."><label for='mem_type_user'>일반회원</label>
				    	<input type=radio name='mem_type' id='mem_type_biz' value='C' ".($db->dt[mem_type] == "C" ? "checked":"")." ><label for='mem_type_biz'>사업자회원</label>
						<input type=radio name='mem_type' id='mem_type_staff' value='A' ".($db->dt[mem_type] == "A" ? "checked":"")."><label for='mem_type_staff'>직원</label>
					</td>
				  </tr>
				  <tr >
				    <td class='input_box_title'><b> 성명</b><img src='".$required3_path."'></td>
					<td class='input_box_item'>
						<input type=text class='textbox' name='user_name' value='".$db->dt[user_name]."' style='width:230px;'>
					</td>
					<td class='input_box_title'><b> 성별</b><img src='".$required3_path."'></td>
					<td class='input_box_item'>
						<input type=radio name='sex' id='sex_male' value='0' ".(($db->dt[sex] == "0" || $db->dt[mail_yn] == "") ? "checked":"")."><label for='sex_male'>남성</label>
				    	<input type=radio name='sex' id='sex_female' value='1' ".($db->dt[sex] == "1" ? "checked":"")." ><label for='sex_female'>여성</label>
						<input type=radio name='sex' id='sex_unknown' value='2' ".($db->dt[sex] == "2" ? "checked":"")." ><label for='sex_unknown'>기타</label>
					</td>
				  </tr>
				  <tr>
					<td class='input_box_title'><b> 이메일</b></td>
					<td class='input_box_item'>
						<input type=text class='textbox' name='email' style='width:230px;'value='".$db->dt[email]."'>
					</td>
					<td class='input_box_title'><b> 생년월일</b></td>
					<td class='input_box_item'>
						".select_date('birth')."
					</td>
				  </tr>
				  <tr>
					<td class='input_box_title'><b>국적</b></td>
					<td class='input_box_item' colspan='3'>
						<select>
							<option>국가선택</option>
						</select>
					</td>
				  </tr>
				  <tr>
				    <td class='input_box_title'><b> 전화번호</b></td>
					<td class='input_box_item'>
						<input type=text class='textbox' name='tel'style='width:230px;'value='".$db->dt[tel]."'>
					</td>
				    <td class='input_box_title'><b> 핸드폰번호</b></td>
					<td class='input_box_item'>
						<input type=text class='textbox' name='mobile' style='width:230px;' value='".$db->dt[mobile]."'>
					</td>
				  </tr>
				  <tr>
				    <td class='input_box_title'><b> 메일수신여부</b></td>
				    <td class='input_box_item'>
				    	<input type=radio name='mail_yn' id='mail_yn_1' value='1' ".(($db->dt[mail_yn] == "1" || $db->dt[mail_yn] == "") ? "checked":"")."><label for='mail_yn_1'>수신</label>
				    	<input type=radio name='mail_yn' id='mail_yn_0' value='0' ".($db->dt[mail_yn] == "0" ? "checked":"")."><label for='mail_yn_0'>수신거부</label>
				    </td>
				    <td class='input_box_title'><b> SMS 수신여부</b></td>
				    <td class='input_box_item'>
				    	<input type=radio name='sms_yn' id='sms_yn_1' value='1' ".(($db->dt[sms_yn] == "1" || $db->dt[sms_yn] == "") ? "checked":"")."><label for='sms_yn_1'>수신</label>
				    	<input type=radio name='sms_yn' id='sms_yn_0' value='0' ".($db->dt[sms_yn] == "0" ? "checked":"")."><label for='sms_yn_0'>수신거부</label>
				    </td>
				  </tr>
				  <tr>
				    <td class='input_box_title'> 회사명</td><td class='input_box_item' colspan=3><input type=text class='textbox' name='com_name' value='".$db->dt[com_name]." ' style='width:230px;'> <span class=small></span></td>
				  </tr>
				  <tr>
				    <td class='input_box_title'> 일반전화</td><td class='input_box_item'><input type=text class='textbox' name='phone' value='".$db->dt[phone]."' style='width:230px;'> <span class=small></span></td>
				    <td class='input_box_title'> 팩스</td><td class='input_box_item'><input type=text class='textbox' name='fax' value='".$db->dt[fax]."' style='width:230px;'> <span class=small></span></td>
				  </tr>

				  <tr>
				    <td class='input_box_title'> 홈페이지</td><td class='input_box_item' colspan=3><input type=text class='textbox' name='homepage' value='".$db->dt[homepage]."' style='width:230px;'> <span class=small></span></td>
				  </tr>
				  <tr >
				    <td class='input_box_title'> 회사주소</td><td class='input_box_item' colspan=3><input type=text class='textbox' name='com_address' value='".$db->dt[com_address]."' style='width:430px;'> <span class=small></span></td>

				  </tr>

				  <tr>
				    <td class='input_box_title'> 메모</td>
				    <td class='input_box_item' style='padding:5px;' colspan=3><textarea  name='memo'  style='width:90%;height:70px;'>".$db->dt[memo]."</textarea></td>
				  </tr>
				  </table>

				  <table width=100% cellpadding=5 cellspacing=0 border=0>
					<tr bgcolor=#ffffff >
                        <td colspan=4 align=center style='padding:10px 0px;'>";
                        if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
                            $Contents01 .= "
                            <input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
                        }else{
                            $Contents01 .= "
                            <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>";
                        }
                        $Contents01 .= "
                        </td>
                    </tr>
				  </table>
				  </form>
				</div>

		</div>
	    </td>
	  </tr>

</table>";




$Contents = "<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";

$Contents = $Contents."</table >";
$Contents.="<iframe name='action_frame' id='action_frame' border=0 height=0 width=0></iframe>";
/*
$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >메일링/SMS 주소록을 원활히 관리하기 위해서는 그룹을 선택하셔야 합니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>핸드폰 번호</u>와 <u>이메일주소</u>는  메일링이나 SMS 발송시 사용되므로 정확히 입력하여 주시기 바랍니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >부가적으로 회사정보등을 입력해서 관리 하실 수 있습니다.</td></tr>
	</table>
	";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');

$help_text = HelpBox("주소록 등록 관리", $help_text);

$Contents = $Contents.$help_text;
 $Script = "
 <script  id='dynamic'></script>
 <script language='javascript'>
function showTabContents(vid, tab_id){
	var area = new Array('mailling_insert_form','mailling_search_form');
	var tab = new Array('tab_01','tab_02');

	for(var i=0; i<area.length; ++i){
		if(area[i]==vid){
			document.getElementById(vid).style.display = 'block';
			document.getElementById(tab_id).className = 'on';
		}else{
			document.getElementById(area[i]).style.display = 'none';
			document.getElementById(tab[i]).className = '';
		}
	}

}

 function updateBankInfo(div_ix,div_name,disp){
 	var frm = document.div_form;

 	frm.act.value = 'update';
 	frm.div_ix.value = div_ix;
 	frm.div_name.value = div_name;
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}

}

function CheckForm(frm){
	if(frm.parent_group_ix.value == ''){
		alert('1차 그룹은 반드시 선택하셔야 합니다.');
		return false;
 	}
	
	if(frm.user_name.value ==''){
		alert('이름을 입력해주세요');
		frm.user_name.focus();
		return false;
	}
	
	return true;
}

function deleteMaillingInfo(act, ci_ix){
 	if(confirm('해당메일링 리스트를  정말로 삭제하시겠습니까?')){
 		var frm = document.div_form;
 		frm.act.value = act;
 		frm.ci_ix.value = ci_ix;
 		frm.submit();
 	}
}

function show_mailling_info(obj){
	if(obj.style.display == 'block'){
		obj.style.display = 'none';
	}else{
		obj.style.display = 'block';
	}
}

function loadCampaignGroup(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	//var depth = sel.depth;
	var depth = sel.getAttribute('depth');
	//document.write('campaigngroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2');
	//dynamic.src = 'campaigngroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2';
	window.frames['act'].location.href = 'campaigngroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2';

}

 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = campaign_menu();
	$P->Navigation = "메일링/SMS > 주소록 등록관리";
	$P->title = "주소록 등록관리";
	$P->strContents = $Contents;


	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = campaign_menu();
	$P->Navigation = "메일링/SMS > 주소록 등록관리";
	$P->title = "주소록 등록관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

function getCampaignGroupInfoSelect($obj_id, $obj_txt, $parent_group_ix, $selected, $depth=1, $property=""){
	global $admininfo;
	$mdb = new Database;
	if($depth == 1){
		$sql = 	"SELECT abg.*
				FROM shop_addressbook_group abg
				where group_depth = '$depth' and abg.company_id = '".$admininfo[company_id]."'
				order by vieworder asc ";
				//group by group_ix
	}else if($depth == 2){
		$sql = 	"SELECT abg.*
				FROM shop_addressbook_group abg
				where group_depth = '$depth' and parent_group_ix = '$parent_group_ix' and abg.company_id = '".$admininfo[company_id]."'
				order by vieworder asc ";
				//group by group_ix
	}
	//echo $sql;
	$mdb->query($sql);

	$mstring = "<select name='$obj_id' id='$obj_id' $property>";
	$mstring .= "<option value=''>$obj_txt</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[group_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[group_ix]."' selected>".$mdb->dt[group_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[group_ix]."'>".$mdb->dt[group_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}
?>