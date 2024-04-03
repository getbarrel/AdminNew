<?
/*
* 만든이 : JBG 2014-06-02 
* 오류나 버그수정시에는 주석을 남겨주세요 ~_~ 
*/

include("../class/layout.class");

$menu_name = "회원상담문의";

$db = new Database;
$mdb = new Database;

//업데이트할떄
if($ta_ix){
	$sql = "SELECT
				*
			FROM 
				shop_member_talk_history
			WHERE 
				ta_ix = '$ta_ix';
			";
	$db->query($sql);
	$db->fetch();
	$aw_type = str_replace(",","",$db->dt['aw_type']);
	$user_name	=	$db->dt['user_name'];
	$bbs_div	=	$db->dt['user_qa_group'];
	$sub_div	=	$db->dt['user_sub_group'];
	$act = "update";

	$phone	=	explode('-',$db->dt['user_phone']);
	$tel	=	explode('-',$db->dt['user_tel']);
}else{
	//새로입력할떄
	$act = "insert";
}

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	  <tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "견적서 관리 > $menu_name")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b>회원정보</b>  </div>")."</td>
	  </tr>
	  </table>";

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
	<colgroup>
		<col width='15%' />
		<col width='35%' />
		<col width='15%' />
		<col width='35%' />
	</colgroup>
	<tr>
		<td class='input_box_title'><b>작성자</b></td>
		<td class='input_box_item'>
		<input type=text name='ta_counselor' value='".($db->dt[ta_counselor] ? $db->dt[ta_counselor] : $_SESSION['admininfo']['charger'])."' class='textbox' style='width:100px' />
		</td>
		<td class='input_box_title'>작성일/코드</td>
			<td class='input_box_item'>
				".($ta_ix ? $db->dt['regdate']."/".$db->dt['ta_code'] : '')."
			</td>
	</tr>
	<tr>
	    <td class='input_box_title'> <b>회원명</b></td>
		<td class='input_box_item'>
			".MBSelect('ucode','user_name',$user_name)." 
			<input type='hidden' name='ucode' id='user_code' />
		</td>
		<td class='search_box_title' >ID</td>
		<td class='search_box_item'>
			<input type='text' name='user_id' id='user_id' value='".$db->dt['user_id']."' class='textbox' />
		</td>
	</tr>
	<tr>
		<td class='input_box_title'>회원 그룹</td>
		<td class='input_box_item'>
			".makeGroupSelectBox($mdb,"user_group",$db->dt[user_group]," title='사용자 그룹'")."
		</td>
		<td class='input_box_title'>회원레벨</td>
		<td class='input_box_item'>
			".getMemberLevel($db->dt[user_level],'true')."
		</td>
	</tr>
		<tr>
	    <td class='input_box_title'> <b>연락처</b></td>
		<td class='input_box_item'>
			<input type=text name='tel_1' id='tel_1' value='".$tel[0]."' class='textbox'  style='width:50px' /> -
			<input type=text name='tel_2' id='tel_2' value='".$tel[1]."' class='textbox'  style='width:50px' /> -
			<input type=text name='tel_3' id='tel_3' value='".$tel[2]."' class='textbox'  style='width:50px' />
		</td>
		<td class='search_box_title' >핸드폰</td>
		<td class='search_box_item'>
			<input type=text name='phone_1' id='phone_1' value='".$phone[0]."' class='textbox'  style='width:50px' /> -
			<input type=text name='phone_2' id='phone_2' value='".$phone[1]."' class='textbox'  style='width:50px' /> -
			<input type=text name='phone_3' id='phone_3' value='".$phone[2]."' class='textbox'  style='width:50px' />
		</td>
	</tr>
	<tr>
	    <td class='input_box_title'> <b>문의분류</b></td>
		<td class='input_box_item'>";
			//주문분류는 게시판의 분류의 키bm_ix = '1'을 이용하여 구연함!
			$sql = "SELECT 
						*
					FROM 
						shop_member_talk_category
					where disp = 1
					";
					$db->query($sql);
					$bbs_divs = $db->fetchall();
		
			$Contents01 .= "
			<select name='bbs_div' align='absmiddle'>
					<option value=''>분류선택</option>";
			for($d=0;$d<count($bbs_divs);$d++){
				$Contents01 .= "<option value=".$bbs_divs[$d]['tc_code']." >".$bbs_divs[$d]['tc_name']."</option>";
			}
			$Contents01 .= "
			</select>
			<span id='sub_cate_table' style='display:none;'>
				<select name='sub_bbs_div'>
					<option value=''>서브분류선택</option>
				</select>
			</span>&nbsp;&nbsp;
		</td>
		<td class='search_box_title' >긴급문의체크</td>
		<td class='search_box_item'>
			<input type='checkbox' name='emergency_type' value='1' id='emergency' ".($db->dt['emergency_type'] == 1 ? 'checked' : '')." /><label for='emergency'>긴급문의</label>
		</td>
	</tr>
	<tr>
	    <td class='input_box_title'> <b>고객상태</b></td>
		<td class='input_box_item' colspan='3'>
			<input type=radio name='customer_state' id='user_mood_state_5' value='0' ".($db->dt['customer_state'] == 0 ? 'checked' : '').">
			<label class='helpcloud' help_width='45' help_height='15' help_html='기쁨' for='user_mood_state_5'> <img src='../images/icon/mood_state_5.png' align='absmiddle' /></label>
			<input type=radio name='customer_state' id='user_mood_state_4' value='1' ".($db->dt['customer_state'] == 1 ? 'checked' : '').">
			<label class='helpcloud' help_width='45' help_height='15' help_html='양호' for='user_mood_state_4'> <img src='../images/icon/mood_state_4.png' align='absmiddle' /></label>
			<input type=radio name='customer_state' id='user_mood_state_3' value='2' ".($db->dt['customer_state'] == 2 || $db->dt['customer_state'] == '' ? 'checked' : '').">
			<label class='helpcloud' help_width='45' help_height='15' help_html='보통' for='user_mood_state_3'> <img src='../images/icon/mood_state_3.png' align='absmiddle' /></label>
			<input type=radio name='customer_state' id='user_mood_state_2' value='3' ".($db->dt['customer_state'] == 3 ? 'checked' : '').">
			<label class='helpcloud' help_width='45' help_height='15' help_html='불만' for='user_mood_state_2'> <img src='../images/icon/mood_state_2.png' align='absmiddle' /></label>
			<input type=radio name='customer_state' id='user_mood_state_1' value='4' ".($db->dt['customer_state'] == 4 ? 'checked' : '').">
			<label class='helpcloud' help_width='70' help_height='15' help_html='매우불만' for='user_mood_state_1'> <img src='../images/icon/mood_state_1.png' align='absmiddle' /></label> 
		</td>
	</tr>
	<tr>
	    <td class='input_box_title'> <b>주문번호</b></td>
		<td class='input_box_item' colspan='3'>
		<input type=text name='oid' value='".$db->dt['oid']."' class='textbox'  style='width:100px' />상품번호
		<input type=text name='pid' value='".$db->dt['pid']."' class='textbox'  style='width:100px' />
		</td>
	  </tr>
	  <!--<tr>
	    <td class='input_box_title'> <b>첨부파일</b></td>
		<td class='input_box_item' colspan='3'>
			<input type='file' name='ta_file' />
		</td>
	  </tr>-->
	</table><br>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	  <tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "견적서 관리 > $menu_name")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' ><b>회원문의사항</b>  </div>")."</td>
	  </tr>
	  </table>
	<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
	<colgroup>
		<col width='15%' />
		<col width='35%' />
		<col width='15%' />
		<col width='35%' />
	</colgroup>
	<tr>
		<td class='input_box_title'><b>문의사항</b></td>
		<td class='input_box_item' colspan='3'>
			<textarea name='ta_memo' style='width:98%;height:150px'>".$db->dt['ta_memo']."</textarea>
		</td>
	</tr>
	</table>
	<br />
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	  <tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "견적서 관리 > $menu_name")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b>담당자 처리사항</b>  </div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
	<colgroup>
		<col width='15%' />
		<col width='35%' />
		<col width='15%' />
		<col width='35%' />
	</colgroup>
	<tr>
		<td class='input_box_title'><b>담당자 설정</b></td>
		<td class='input_box_item'>
			".CASelect('ta_charger',$db->dt['ta_charger'])." 
			<input type='hidden' name='ta_charger_ix' id='ta_charger_ix' />
		</td>
		<td class='input_box_title'>처리상태</td>
		<td class='input_box_item'>
			<select name='qa_state'>
				<option>선택하기</option>
				<option value='W' ".($db->dt['qa_state'] == "W"?"selected" : "").">접수중</option>
				<option value='I' ".($db->dt['qa_state'] == "I"?"selected" : "").">처리중</option>
				<option value='D' ".($db->dt['qa_state'] == "D"?"selected" : "").">처리지연</option>
				<option value='F' ".($db->dt['qa_state'] == "F"?"selected" : "").">처리완료</option>
				<option value='C' ".($db->dt['qa_state'] == "C"?"selected" : "").">처리취소</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'><b>응대유형</b></td>
		<td class='input_box_item'>
			<input type=checkbox name='aw_type' value='N' id='aw_type_n' ".($aw_type == "N"?"checked" : "")."><label for='aw_type_n'>미요청</label>
			<input type=checkbox name='aw_type' value='S' id='aw_type_s' ".($aw_type == "S"?"checked" : "")."><label for='aw_type_s'>SMS</label>
			<input type=checkbox name='aw_type' value='E' id='aw_type_e' ".($aw_type == "E"?"checked" : "")."><label for='aw_type_e'>이메일</label>
			<input type=checkbox name='aw_type' value='T' id='aw_type_t' ".($aw_type == "T"?"checked" : "")."><label for='aw_type_t'>전화</label>
		</td>
		<td class='input_box_title'>응대처리상태</td>
		<td class='input_box_item'>
			<select name='aw_state'>
				<option>선택하기</option>
				<option value='W' ".($db->dt['aw_state'] == "W"?"selected" : "").">접수중</option>
				<option value='I' ".($db->dt['aw_state'] == "I"?"selected" : "").">처리중</option>
				<option value='D' ".($db->dt['aw_state'] == "D"?"selected" : "").">처리지연</option>
				<option value='F' ".($db->dt['aw_state'] == "F"?"selected" : "").">처리완료</option>
				<option value='C' ".($db->dt['aw_state'] == "C"?"selected" : "").">처리취소</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'><b>처리답변</b></td>
		<td class='input_box_item' colspan='3'>
			<textarea name='aw_memo' style='width:98%;height:150px'>".$db->dt['aw_memo']."</textarea>
		</td>
	</tr>
	</table>
	  ";
$ButtonString .= "<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer;'>";

$Contents = "<form name='talk_from' id='talk_from' action='member_talk_act.php' method='post' enctype='multipart/form-data' target=''><input type='hidden' name='act' value='".$act."' /><input type='hidden' name='ta_ix' value='$ta_ix' />";
$Contents = $Contents."<table width='100%' border=0>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents04."</td></tr>";
$Contents = $Contents."<tr><td align=center>".$ButtonString."</td></tr>";
$Contents = $Contents."</table></form><br><br>";


$Script = "<script type='text/javascript'>
$(function(){
	
	$('select[name=bbs_div]').val(".$bbs_div.");

	window.frames['iframe_act'].location.href='/bbs/category.load.php?form=' + talk_from + '&trigger=' + ".$bbs_div." + '&depth='+'1'+'&target=' + 'sub_bbs_div';

	$('#talk_from').submit(function(){

		if($('input[name=user_name]').val() == '' && $('input[name=user_type]:checked').val() == undefined){
			alert('회원명이나 비회원유무를 선택해주세요');
			$('input[name=user_name]').focus();
			return false;
		}else if($('input[name=tel_1]').val() == ''){
			alert('연락처를 입력해주세요');
			$('input[name=tel_1]').focus();
			return false;
		}else if($('input[name=phone_1]').val() == ''){
			alert('핸드폰을 입력해주세요');
			$('input[name=phone_1]').focus();
			return false;
		}else if($('select[name=bbs_div]').val().length == 0 ){
			alert('문의분류를 선택해주세요');
			$('select[name=bbs_div]').focus();
			return false;
		}else if($('textarea[name=ta_memo]').val() == ''){
			alert('회원문의사항을 입력해주세요');
			$('textarea[name=ta_memo]').focus();
			return false;
		}
		
	})

})
function bbsloadCategory(sel,target, depth) {
	
	var trigger = sel.options[sel.selectedIndex].value;	// 첫번째 selectbox의 선택된 텍스트
	var form = sel.form.name;
	window.frames['iframe_act'].location.href='/bbs/category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

}
</script>
";

if($mmode == "pop"){

	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = estimate_menu();
	$P->strContents = $Contents;
	$P->Navigation = "견적서 관리 > 견적서 관리 > $menu_name";
	$P->NaviTitle = " $menu_name";
	echo $P->PrintLayOut();
}else{

	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = estimate_menu();
	$P->strContents = $Contents;
	$P->Navigation = "견적서 관리 > 견적서 관리 > $menu_name";
	$P->title = "$menu_name";
	echo $P->PrintLayOut();
}

?>