<?
include("../class/layout.class");

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	  <tr >
		<td  style='padding-bottom:0px;'> ".GetTitleNavigation("메일링/SMS 주소록 일괄등록", "메일링/SMS > 메일링/SMS 주소록 일괄등록 ")."</td>
	  </tr>
	  <tr >
	    <td >";
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
							<table id='tab_03'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='addressbook_add.php'\">주소록 개별등록</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_04' class='on'>
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
			</td>
		</tr>
		<tr >
			<td  >
		<div class='t_no' style='padding:15px 0px; '>
			<div class='my_box'>
				<form name='div_form' action='addressbook.act.php' method='post' onsubmit='return CheckForm(this)' enctype='multipart/form-data'  >
				<input name='act' type='hidden' value='excel_insert'>
				<input name='ab_ix' type='hidden' value='$ab_ix'>
				<table width=100% cellpadding=0 cellspacing=0 border=0 class='input_table_box'>
				<col width=10%>
				<col width=40%>
				<col width=10%>
				<col width=40%>
				<tr>
				    <td class='input_box_title'><b>  그룹</b></td>
				    <td class='input_box_item' colspan=3>
				    	".getCampaignGroupInfoSelect('parent_group_ix', '1 차그룹','', '', 1, " onChange=\"loadCampaignGroup(this,'group_ix')\" ")."
				    	".getCampaignGroupInfoSelect('group_ix', '2 차그룹','', '', 2)."

							<span class=small><!--메일링/SMS 주소록을 원활히 관리하기 위해서는 그룹을 선택하셔야 합니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</span>
				    </td>
				  </tr>
				  <tr>
				    <td class='input_box_title'><b> 엑셀파일</b></td>
				    <td class='input_box_item' style='padding:5px;' colspan=3><input type=file class='textbox' name='addressbook_excel' value='' style='width:230px;'>
				    <a href='./addressbook.act.php?act=sample_excel_down'><img src='../images/".$admininfo["language"]."/btn_sample_excel_save.gif' align=absmiddle></a><br>
				    <div class=small><!--샘플을 다운로드 하여 정보를 입력하시고 다른이름저장을 통해 Excel 97~2003 통합버전으로 저장하여 등록하시면 됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</div></td>
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
				    <td class='input_box_title'> 메모</td>
				    <td class='input_box_item' style='padding:5px;' colspan=3><textarea  name='memo'  style='width:90%;height:70px;'>".$db->dt[memo]."</textarea></td>
				  </tr>
				  </table>
				  <table width=100% cellpadding=0 cellspacing=0 border=0>
					<tr bgcolor=#ffffff >
                        <td align=center style='padding:10px 0px;'>";
                        if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
                            $Contents01 .= "
                            <input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' >";
                        }else{
                            $Contents01 .= "
                            <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></a>";
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
/*
$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >주소록 일괄등록은 샘플을 다운로드 하여 정보를 입력하시고 다른이름저장을 통해 Excel 97~2003 통합버전으로 저장하여 등록하시면 됩니다.</td></tr>

	</table>
	";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$help_text = HelpBox("주소록 일괄등록 관리", $help_text);
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
 	/*
	if(frm.user_name.value.length < 1){
		alert('이름을 입력해주세요');
		frm.user_name.focus();
		return false;
	}

	if(frm.mobile.value.length < 1){
		alert('해드폰을 입력해주세요');
		frm.mobile.focus();
		return false;
	}

	if(frm.mail.value.length < 1){
		alert('이메일을 입력해주세요');
		frm.mail.focus();
		return false;
	}
	*/
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
	var depth = sel.getAttribute('depth');
	//document.write('campaigngroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2');
	//dynamic.src = 'campaigngroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2';
	window.frames['act'].location.href='campaigngroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2';
	//alert('campaigngroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2');

}

 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = campaign_menu();
	$P->Navigation = "메일링/SMS > 주소록 일괄등록";
	$P->NaviTitle = "주소록 일괄등록";
	$P->strContents = $Contents;


	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = campaign_menu();
	$P->Navigation = "메일링/SMS > 주소록 일괄등록";
	$P->title = "주소록 일괄등록";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

function getCampaignGroupInfoSelect($obj_id, $obj_txt, $parent_group_ix, $selected, $depth=1, $property=""){
	global $admininfo;
	$mdb = new Database;
	if($depth == 1){
		$sql = 	"SELECT abg.*
				FROM shop_addressbook_group abg
				where group_depth = '$depth'  and abg.company_id = '".$admininfo[company_id]."'
				order by vieworder asc ";
				//group by group_ix
	}else if($depth == 2){
		$sql = 	"SELECT abg.*
				FROM shop_addressbook_group abg
				where group_depth = '$depth' and parent_group_ix = '$parent_group_ix'  and abg.company_id = '".$admininfo[company_id]."'
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