<?php
include("../class/layout.class");

if($admininfo[admin_level] < 9){
    header("Location:../admin.php");
}

$db = new Database;

if($code == ""){
    $code = "use";
}

if($pi_code == ""){
    $pi_code = "use";
}

//수정할 인덱스 값이 있을떄
if($pi_ix){

        $sql="SELECT *, substr(startdate, 1, 10) as startdate_ymd FROM shop_policy_info WHERE pi_ix = '$pi_ix'";
        $db->query($sql);

        if($db->total) {

            $db->fetch();
            $mp_config[$db->dt[pi_code]]["pi_ix"]			= $db->dt[pi_ix];
            $mp_config[$db->dt[pi_code]]["pi_code"]			= $db->dt[pi_code];
            $mp_config[$db->dt[pi_code]]["pi_contents"]		= stripslashes($db->dt[pi_contents]);
            $mp_config[$db->dt[pi_code]]["contents_type"]	= $db->dt[contents_type];
            $mp_config[$db->dt[pi_code]]["startdate"]		= $db->dt[startdate_ymd];
            $mp_config[$db->dt[pi_code]]["disp"]			= $db->dt[disp];

        }

        $sql="SELECT 
					*, substr(startdate, 1, 10) as startdate_ymd
				FROM 
					shop_policy_info 
				WHERE pi_code = '$pi_code' AND startdate < now() 
				ORDER BY startdate DESC 
				LIMIT 1";

        $db->query($sql);

        if($db->total){

            $db->fetch();

            $default_pi_ix	=	$db->dt['pi_ix'];
            $default_text	=	$db->dt['pi_contents'];
            $default_sdate	=	$db->dt['startdate_ymd'];
            $default_disp	=	$db->dt['disp'];
            $default_link_text = $db->dt['link_text'];
        }

//기본값일떄
}else{

    $sql="	SELECT 
            * , substr(startdate, 1, 10) as startdate_ymd
        FROM 
            shop_policy_info 
        WHERE pi_code = '$pi_code' AND startdate < now() 
        ORDER BY startdate DESC LIMIT 1";

    $db->query($sql);

    if($db->total){

        $db->fetch();

        $default_pi_ix	=	$db->dt['pi_ix'];
        $default_text	=	$db->dt['pi_contents'];
        $default_sdate	=	$db->dt['startdate_ymd'];
        $default_disp	=	$db->dt['disp'];
        $default_link_text = $db->dt['link_text'];
    }
}

$top_contents = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed; margin-bottom: 3%;'>
<col width='*'>
	<tr>
		<td align='left'> ".GetTitleNavigation("약관/개인정보처리방침", "상점관리 > 쇼핑몰 환경설정 > 약관/개인정보처리방침")."</td>
	</tr>
	<tr>
			<td align='left' colspan=4 style='padding-bottom:11px;'>
				 <div class='tab'>
				<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='parent_tab01' onclick=\"hideOtherContents(this);showPolicyCategory(1);\" >
							<tr>
								<th class='box_01'></th>
								<td class='box_02'  >쇼핑몰 이용약관</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='parent_tab02' onclick=\"hideOtherContents(this);showPolicyCategory(2);\">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' >개인정보처리방침(회원)</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='parent_tab05' onclick=\"hideOtherContents(this);showPolicyCategory(5);\">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' >개인정보처리방침(비회원)</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='parent_tab03' onclick=\"hideOtherContents(this);showPolicyCategory(3);\">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' >기타약관</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='parent_tab04' onclick=\"hideOtherContents(this);showPolicyCategory(4);\">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' >글로벌약관</td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
			</td>
		</tr>
		<tr>
			<td align='left' colspan='4' style='padding-bottom:11px;'>
				<table id='tab_01' cellpadding='0' cellspacing='1' width='100%' bgcolor='#c0c0c0' class='list_table_box' style='display:none;margin-top:10px;margin-bottom: -28px;'>
					<tr bgcolor='#ffffff' align='center' height='30'>
						<td class='".ReturnClassName($pi_code, 'use')."'><a href='?code=use&pi_code=use'>이용약관</br>(Footer, 회원가입, 비회원 구매 노출)</a></td>
						<td class='".ReturnClassName($pi_code, 'seller')."'><a href='?code=use&pi_code=seller'>판매회원 이용약관</br>(Footer, 셀러 가입 시)</a></td>
						<td class='".ReturnClassName($pi_code, 'protect')."'><a href='?code=use&pi_code=protect'>판매회원 개인정보 보호 준수사항</br>(Footer, 셀러 가입 시)</a></td>
						<td class='".ReturnClassName($pi_code, 'teen')."'><a href='?code=use&pi_code=teen'>청소년 보호대책</br>(Footer)</a></td>
						<td class='".ReturnClassName($pi_code, 'email')."'><a href='?code=use&pi_code=email'>이메일 무단 수집 거부</a></td>
						<td class='".ReturnClassName($pi_code, 'marketing')."'><a href='?code=use&pi_code=marketing'>마케팅 활용 동의</a></td>
						<td class='".ReturnClassName($pi_code, 'ig_use_collection')."'><a href='?code=use&pi_code=ig_use_collection'>개인정보 이용내역</a></td>
					</tr>
				</table>
				<table id='tab_02' cellpadding='0' cellspacing='1' width='100%' bgcolor='#c0c0c0' class='list_table_box' style='display:none;margin-top:10px;margin-bottom: -28px;'>
					<tr bgcolor='#ffffff' align='center' height='30'>
						<td class='".ReturnClassName($pi_code, 'person')."'><a href='?code=person&pi_code=person'>개인정보처리방침</br>(Footer, 회원가입)</a></td>
						<td class='".ReturnClassName($pi_code, 'collection')."'><a href='?code=person&pi_code=collection'>개인정보 수집 및 이용</br>(필수_회원가입)</a></td>
						<td class='".ReturnClassName($pi_code, 'collection_select')."'><a href='?code=person&pi_code=collection_select'>개인정보 수집 및 이용</br>(선택_회원가입)</a></td>
						<td class='".ReturnClassName($pi_code, 'consign')."'><a href='?code=person&pi_code=consign'>개인정보처리위탁</br>(회원가입)</a></td>
						<td class='".ReturnClassName($pi_code, 'third')."'><a href='?code=person&pi_code=third'>개인정보 제 3자 제공</br>(회원가입)</a></td>
					</tr>
				</table>
				<table id='tab_05' cellpadding='0' cellspacing='1' width='100%' bgcolor='#c0c0c0' class='list_table_box' style='display:none;margin-top:10px;margin-bottom: -28px;'>
					<tr bgcolor='#ffffff' align='center' height='30'>
						<td class='".ReturnClassName($pi_code, 'non_collection')."'><a href='?code=non_person&pi_code=non_collection'>개인정보 수집 및 이용</br>(비회원 구매 시, 비회원 게시판 글/댓글 작성 시)</a></td>
						<td class='".ReturnClassName($pi_code, 'non_consign')."'><a href='?code=non_person&pi_code=non_consign'>개인정보처리위탁</br>(비회원 구매 시)</a></td>
						<td class='".ReturnClassName($pi_code, 'non_third')."'><a href='?code=non_person&pi_code=non_third'>개인정보 제 3자 제공</br>(비회원 구매 시)</a></td>
					</tr>
				</table>
				<table id='tab_03' cellpadding='0' cellspacing='1' width='100%' bgcolor='#c0c0c0' class='list_table_box' style='display:none;margin-top:10px;margin-bottom: -28px;'>
					<tr bgcolor='#ffffff' align='center' height='30'>
						<td class='".ReturnClassName($pi_code, 'duty')."'><a href='?code=etc&pi_code=duty'>세금 납부 유의사항</a></td>
						<td class='".ReturnClassName($pi_code, 'caution')."'><a href='?code=etc&pi_code=caution'>상품 구매 주의사항</a></td>
						<td class='".ReturnClassName($pi_code, 'alliance')."'><a href='?code=etc&pi_code=alliance'>제휴 문의</a></td>
					</tr>
				</table>
				<table id='tab_04' cellpadding='0' cellspacing='1' width='100%' bgcolor='#c0c0c0' class='list_table_box' style='display:none;margin-top:10px;margin-bottom: -28px;'>
					<tr bgcolor='#ffffff' align='center' height='30'>
						<td class='".ReturnClassName($pi_code, 'use_global')."'><a href='?code=global&pi_code=use_global'>이용약관</a></td>
						<td class='".ReturnClassName($pi_code, 'collection_global')."'><a href='?code=global&pi_code=collection_global'>개인정보 수집 및 이용</a></td>
						<td class='".ReturnClassName($pi_code, 'person_global')."'><a href='?code=global&pi_code=person_global'>개인정보처리방침</a></td>
						<td class='".ReturnClassName($pi_code, 'marketing_global')."'><a href='?code=global&pi_code=marketing_global'>마케팅 활용 동의</a></td>
					</tr>
				</table>
			</td>
		</tr>
</table>
";

$Contents01 = "
<form action='mall_policy_act.php' method='post' id='mall_policy' target='act__'>
<input type='hidden' name='mode' value='add'>
<input type='hidden' name='pi_ix' value='".($pi_ix ? $pi_ix : $default_pi_ix)."'>
<input type='hidden' name='pi_code' value='".$pi_code."'>
<input type='hidden' name='code' value='".$code."'>
<table width='100%' style='margin-top:50px'>
	<tr>
		<td align='left' style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>".pi_codeName($pi_code)."</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='20%'>
	<col width='40%'>
	<tr>
		<td class='input_box_title' style='padding:5px 20px;text-align:left;'>
			약관 사용 유무
		</td>
		<td class='input_box_item' style='padding:5px 5px;text-align:left;' colspan='3'>
			<input type='radio' name='disp' id='dis_use' value='Y' ".($default_disp == "Y"?"checked":"")." /><label for='dis_use'>사용</label>
			<input type='radio' name='disp' id='dis_unuse' value='N' ".($default_disp == "N"?"checked":"")." /><label for='dis_unuse'>미사용</label>
		</td>
	</tr>
	<tr>
		<td class='input_box_title' style='padding:5px 20px;text-align:left;'>
			약관 실행일 설정
		</td>
		<td class='input_box_item' style='padding:5px 5px;text-align:center;' colspan='3'>
			<table cellpadding=0>
				<tr>
					<td>
					".select_date('start_date',($pi_ix ? $mp_config[$pi_code]['startdate'] : $default_sdate ))."
					<input type='hidden' name='sdate' value=".($pi_ix ? $mp_config[$pi_code]['startdate'] : $default_sdate )." />
					</td>
				</tr>
				<tr>
					<td>* 실행일 변경 후 저장시 변경리스트에 추가됩니다</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr ".($code != "person" ? "style='display:none;'" : "").">
		<td class='input_box_title' style='padding:5px 20px;text-align:left;'>
			링크 텍스트
		</td>
		<td class='input_box_item' style='padding:5px 5px;text-align:left;' colspan='3'>
			<input type='text' name='link_text' id='link_text' value='".$default_link_text."'/>
			*셀러 리스트 페이지 치환코드는 [SELLERLIST] 입니다
		</td>
	</tr>
	<tr>
		<td class='input_box_title' style='padding:5px 20px;text-align:left;'>".($pi_ix ? "선택된 약관 내용" : "노출중인 약관 내용")."</td>
		<td class='input_box_item' style='padding:5px 0px;' colspan='3'>
			<div id='mall_policy_select' style='width:99%;height:150px;overflow:hidden;overflow-y:scroll;padding:10px 0 0 10px' readonly>".($pi_ix ? $mp_config[$pi_code]["pi_contents"] : $default_text )."
			</div>
			<div id='default_txt' style='display:none'>".$default_txt."</div>
		</td>
	</tr>
	<tr>
		<td class='input_box_title' style='padding:5px 20px;text-align:left;'>
			수정 약관내용<br /><input type='button' value='기존내용복사' id='memo_copy' />
		</td>
		<td class='input_box_item' style='padding:5px 0px;text-align:center;' colspan='3'>
			<textarea name='policy_text' id='policy_text'></textarea>
		</td>
	</tr>
	<!--tr>
		<td class='input_box_title' style='padding:5px 20px;text-align:left;'>
			기본형 사용
		</td>
		<td class='input_box_item' style='padding:5px 5px;text-align:left;' colspan='3' >
			<input type='radio' name='contents_type' id='unuse' value='B' /><label for='unuse'>기본형</label>
			<input type='radio' name='contents_type' id='use' value='U' /><label for='use'>직접입력</label>
		</td>
	</tr-->
</table>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr bgcolor=#ffffff >
	<td align=center style='padding:30px 0px;'>
		<img src='../images/".$admininfo["language"]."/b_save.gif' id='update' border=0 style='cursor:Pointer;border:0px;' >
		<!--img src='../images/".$admininfo["language"]."/add_btn.gif' id='add' border=0 style='cursor:Pointer;border:0px;' -->
	</td>
</tr>
</table>
</form>
";

$footerContents = "
<div id='footer_html'>
<table width='100%' style='padding-top:20px'>
	<tr>
		<td align='left' style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>이용약관 변경 리스트</b></div>")."</td>
	</tr>
</table>
<table width='100%' border='0' cellpadding='0' cellspacing='0'  class='list_table_box'>
  <col width='5%'>
  <col width='20%'>
  <col width='20%'>
  <col width='20%'>
  <col width='20%'>
  <col width='20%'>
  <tr height='28' bgcolor='#ffffff'>
    <td align='center' class='m_td'><font color='#000000'><b>No</b></font></td>
	<td align='center' class='m_td'><font color='#000000'><b>등록일자/등록자</b></font></td>
    <td align='center' class='m_td'><font color='#000000'><b>약관 실행일자</b></font></td>
    <td align='center' class='m_td'><font color='#000000'><b>수정자</b></font></td>
    <td align='center' class=e_td><font color='#000000'><b>관리</b></font></td>
  </tr>";

$sql="SELECT 
    *,
    (substr(startdate,1,10)) as before_date
FROM 
    shop_policy_info
WHERE 
    pi_code= '$pi_code'
ORDER BY regdate DESC";
$db->query($sql);

if($db->total) {
    for($i=0;$i < $db->total;$i++){

        $db->fetch($i);

        $total	=	$db->total;
        $no = $total - ($page - 1) * $max - $i;

        $footerContents = $footerContents."
  <tr height='54' align=center onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
    <td class='list_box_td'>".$no."</td>
	<td class='list_box_td'>".$db->dt['regdate']."<br />".$db->dt['reg_name']."(".$db->dt['reg_id'].")</td>
	<td class='list_box_td'>".$db->dt['startdate']."</td>
	<td class='list_box_td'>".$db->dt['moddate']."<br />".($db->dt['mod_name'] ? $db->dt['mod_name']."(".$db->dt['mod_id'].")" : "")."</td>
    <td class='list_box_td'>";
        $footerContents.="<a href='mall_policy_list.php?pi_ix=".$db->dt['pi_ix']."&pi_code=".$pi_code."&code=".$code."&before_date=".$db->dt['before_date']."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
        $footerContents.="
	  </td>
  </tr> ";

    }
}else{
    $footerContents = $footerContents."
	  <tr height=50>
		<td colspan='5' align='center'>등록된 데이터가 없습니다.</td>
	  </tr>";
}
$footerContents .= "</table>
</div>
";

//노출 내용 구성
$Contents = "<table width='100%' height='100%'  border=0>";
$Contents = $Contents."<tr><td>".$top_contents.$Contents01.$footerContents."<br></td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</table >";

$Script = "<script type='text/javascript' src='../ckeditor/ckeditor.js'></script>";
$Script .= "<script type='text/javascript' src='basicinfo.js'></script>
<script type='text/javascript'>
$(function(){";

if($code == ""){
    $Script .= "
		$('#parent_tab01').addClass('on');
		$('#parent_tab02').removeClass('on');
		$('#parent_tab03').removeClass('on');
		$('#parent_tab04').removeClass('on');
		$('#parent_tab05').removeClass('on');
		";
}else{
    if($code == "use"){
        $Script .= "
		$('#parent_tab01').addClass('on');
		$('#parent_tab02').removeClass('on');
		$('#parent_tab03').removeClass('on');
		$('#parent_tab04').removeClass('on');
		$('#parent_tab05').removeClass('on');
		";
    }else if($code == "person"){
        $Script .= "
		$('#parent_tab01').removeClass('on');
		$('#parent_tab02').addClass('on');
		$('#parent_tab03').removeClass('on');
		$('#parent_tab04').removeClass('on');
		$('#parent_tab05').removeClass('on');
		";
    }else if($code == "etc"){
        $Script .= "
		$('#parent_tab01').removeClass('on');
		$('#parent_tab02').removeClass('on');
		$('#parent_tab03').addClass('on');
		$('#parent_tab04').removeClass('on');
		$('#parent_tab05').removeClass('on');
		";
    }else if($code == "global"){
        $Script .= "
		$('#parent_tab01').removeClass('on');
		$('#parent_tab02').removeClass('on');
		$('#parent_tab03').removeClass('on');
		$('#parent_tab04').addClass('on');
		$('#parent_tab05').removeClass('on');
		";
    }else if($code == "non_person"){
        $Script .= "
		$('#parent_tab01').removeClass('on');
		$('#parent_tab02').removeClass('on');
		$('#parent_tab03').removeClass('on');
		$('#parent_tab04').removeClass('on');
		$('#parent_tab05').addClass('on');
		";
    }
}

if($pi_code == ""){
    $Script .= "
		$('#tab_01').show();
		$('#tab_02').hide();
		$('#tab_03').hide();
		";
}else{
    if($code == "use"){
        $Script .= "
			$('#tab_01').show();
			$('#tab_02').hide();
			$('#tab_03').hide();
			$('#tab_04').hide();
			$('#tab_05').hide();
			";
    }else if($code == "person"){
        $Script .= "
			$('#tab_01').hide();
			$('#tab_02').show();
			$('#tab_03').hide();
			$('#tab_04').hide();
			$('#tab_05').hide();
			";
    }else if($code == "etc"){
        $Script .= "
			$('#tab_01').hide();
			$('#tab_02').hide();
			$('#tab_03').show();
			$('#tab_04').hide();
			$('#tab_05').hide();
			";
    }else if($code == "global"){
        $Script .= "
			$('#tab_01').hide();
			$('#tab_02').hide();
			$('#tab_03').hide();
			$('#tab_04').show();
			$('#tab_05').hide();
			";
    }else if($code == "non_person"){
        $Script .= "
			$('#tab_01').hide();
			$('#tab_02').hide();
			$('#tab_03').hide();
			$('#tab_04').hide();
			$('#tab_05').show();
			";
    }
}

$Script .="

	CKEDITOR.replace('policy_text',{
		toolbar: [
			[ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ],
			[ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ],
			[ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ],
			[ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ],
			'/',
			[ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ],
			[ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ],
			[ 'Link', 'Unlink', 'Anchor' ],
			[ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ],
			'/',
			[ 'Styles', 'Format', 'Font', 'FontSize' ],
			[ 'TextColor', 'BGColor' ],
			[ 'Maximize', 'ShowBlocks' ],
			[ '-' ],
			[ 'About' ]
		]
	});

	$('#memo_copy').click(function(){
		var txt	=	$('#mall_policy_select').html();
		CKEDITOR.instances.policy_text.setData(txt);
	})

	$('#unuse').click(function(){
		var txt	=	$('#default_txt').html();
		CKEDITOR.instances.policy_text.setData(txt);
	})

	$('#use').click(function(){
		CKEDITOR.instances.policy_text.setData();
	})
	

	$('#update').click(function(){

		$('input[name=mode]').val('update');

		for ( instance in CKEDITOR.instances )
		{
			CKEDITOR.instances[instance].updateElement();
		}

		var startdate	=	$('input[name=start_date]').val();
		var sdate		=	$('input[name=sdate]').val();
		startdate		=	startdate.replace('-','');
		sdate			=	sdate.replace('-','');
		startdate		=	startdate.replace('-','');
		sdate			=	sdate.replace('-','');
		
		if($('input[name=start_date]').val() == ''){
			alert('시작일을 입력해주세요');
			$('input[name=startdate]').focus();
			return false;
		}

		var valuesToSubmit = $('#mall_policy').serialize();
		$('#mall_policy').submit();

		/* 왜 submit 하고 ajax로 또 처리했는지 모르겠음 170524
		$.ajax({
			url: $('#mall_policy').attr('action'), 
			data: valuesToSubmit,
			type: 'POST',
			dataType: 'html' ,
		}).success(function(data){
			//act on result.
			if(data == 'succues'){
				alert('등록되었습니다');
				location.reload();
			}else if(data == 'update'){
				alert('수정되었습니다');
				location.reload();
			}else if(data == 'fail'){
				alert('오류가 발생하였습니다 다시 시도해주십시요');
			}
		});
		*/
		return false;

	})
	
})

function showPolicyCategory(type){
	if(type == '1'){
		$('#parent_tab01').addClass('on');
		$('#parent_tab02').removeClass('on');
		$('#parent_tab03').removeClass('on');
		$('#parent_tab04').removeClass('on');
		$('#parent_tab05').removeClass('on');

		$('#tab_01').show();
		$('#tab_02').hide();
		$('#tab_03').hide();
		$('#tab_04').hide();
		$('#tab_05').hide();
	}else if(type == '2'){
		$('#parent_tab01').removeClass('on');
		$('#parent_tab02').addClass('on');
		$('#parent_tab03').removeClass('on');
		$('#parent_tab04').removeClass('on');
		$('#parent_tab05').removeClass('on');

		$('#tab_01').hide();
		$('#tab_02').show();
		$('#tab_03').hide();
		$('#tab_04').hide();
		$('#tab_05').hide();
	}else if(type == '3'){
		$('#parent_tab01').removeClass('on');
		$('#parent_tab02').removeClass('on');
		$('#parent_tab03').addClass('on');
		$('#parent_tab04').removeClass('on');
		$('#parent_tab05').removeClass('on');

		$('#tab_01').hide();
		$('#tab_02').hide();
		$('#tab_03').show();
		$('#tab_04').hide();
		$('#tab_05').hide();
	}else if(type == '4'){
		$('#parent_tab01').removeClass('on');
		$('#parent_tab02').removeClass('on');
		$('#parent_tab03').removeClass('on');
		$('#parent_tab04').addClass('on');
		$('#parent_tab05').removeClass('on');

		$('#tab_01').hide();
		$('#tab_02').hide();
		$('#tab_03').hide();
		$('#tab_04').show();
		$('#tab_05').hide();
	}else if(type == '5'){
		$('#parent_tab01').removeClass('on');
		$('#parent_tab02').removeClass('on');
		$('#parent_tab03').removeClass('on');
		$('#parent_tab04').removeClass('on');
		$('#parent_tab05').addClass('on');

		$('#tab_01').hide();
		$('#tab_02').hide();
		$('#tab_03').hide();
		$('#tab_04').hide();
		$('#tab_05').show();
	}

	return;
}

function hideOtherContents(obj){
	if($(obj).attr('class') != 'on'){
		$('#footer_html').hide();
		$('#mall_policy').hide();

		if($(obj).attr('id') == 'parent_tab01'){
			if($('#tab_01').find('.m_td').length == 1){
				$('#footer_html').show();
				$('#mall_policy').show();
			}
		}else if($(obj).attr('id') == 'parent_tab02'){
			if($('#tab_02').find('.m_td').length == 1){
				$('#footer_html').show();
				$('#mall_policy').show();
			}
		}else if($(obj).attr('id') == 'parent_tab03'){
			if($('#tab_03').find('.m_td').length == 1){
				$('#footer_html').show();
				$('#mall_policy').show();
			}
		}
	}

	return;
}
</script>
";

$P = new LayOut();

$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->Navigation = "상점관리 > 쇼핑몰 환경설정 > 약관/개인정보취급방침";
$P->title = "약관/개인정보취급방침";
$P->strContents = $Contents;
echo $P->PrintLayOut();

function pi_codeName($pi_code,$pi_type = ""){
    switch ($pi_code) {
        case "use":
            $policy_text = "이용약관";
            break;
        case "person":
            $policy_text = "개인정보 처리방침";
            break;
        case "consign":
            $policy_text = "개인정보 취급위탁 안내";
            break;
        case "non_consign":
            $policy_text = "개인정보 취급위탁 안내";
            break;
        case "third":
            $policy_text = "제 3자 정보제공 동의";
            break;
        case "non_third":
            $policy_text = "제 3자 정보제공 동의";
            break;
        case "collection":
            $policy_text = "개인정보 수집 및 이용";
            break;
		case "collection_select":
            $policy_text = "개인정보 수집 및 이용";
            break;
        case "non_collection":
            $policy_text = "개인정보 수집 및 이용";
            break;
        case "finance":
            $policy_text = "전자금융거래 이용약관";
            break;
        case "seller":
            $policy_text = "판매회원 이용약관";
            break;
        case "protect":
            $policy_text = "판매회원 개인정보 보호 준수사항";
            break;
        case "duty":
            $policy_text = "세금 납부 유의사항";
            break;
        case "caution":
            $policy_text = "상품 구매 주의사항";
            break;
        case "alliance":
            $policy_text = "제휴 문의";
            break;
        case "teen":
            $policy_text = "청소년 보호대책";
            break;
        case "email":
            $policy_text = "이메일 무단 수집 거부";
            break;
        case "marketing":
            $policy_text = "마케팅 활용 동의";
            break;
        case "ig_use_collection":
            $policy_text = "개인정보 이용내역";
            break;
        default:
            ;
            break;
    }

    return $policy_text;
}

function ReturnClassName($pi_code, $real_pi_code){
    if($pi_code == $real_pi_code){
        return "m_td";
    }else{
        return "list_box_td list_bg_gray";
    }
}

?>