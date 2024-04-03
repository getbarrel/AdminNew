<?
include("../class/layout.class");
include("./found_info.lib.php");

$db = new Database;
$mdb = new Database;

if($admininfo[admin_level] != '9'){
	$where = " and company_id = '".$admininfo[company_id]."'";
}
//
$db->query("select * from common_seller_support where fs_ix = '$fs_ix' $where");
$db->fetch();

$mobile = $db->dt[mobile];
$m_nums = explode('-',$mobile);
$tel = $db->dt[tel];
$tel_nums = explode('-',$tel);
$status = $db->dt[status];

if(empty($_GET['company_id'])){
	$company_id = $db->dt[company_id];
}else{
	$company_id = $_GET['company_id'];
}

if($admininfo[admin_level] != '9'){
//게시글 확인 부분 2014-07-03 flag 값을 넣어서 지정한 셀러 업체가 접속시 Y로 넣어서 게시글 확인으로 처리함

	if($db->total > 0){
		$sql = "update common_seller_support set check_yn = 'Y' where fs_ix = '$fs_ix' $where";
		$db->query($sql);
	}
}

$sql = "select
			sg.gp_name
		from
			common_member_detail as cmd
			left join shop_groupinfo as sg on (cmd.gp_ix = sg.gp_ix)
		where
			cmd.code = '".$db->dt[fs_code]."'";

$mdb->query($sql);
$mdb->fetch();
$gp_name = $mdb->dt[gp_name];


if($fs_ix != ""){
	$act = 'detail_update';
}else{
	$act = 'detail_insert';
}

if($type == 'C'){
	$cs_title = "입점업체 답변";
}else{
	$cs_title = "관리자 답변";
}

$addScript = "
<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n
<script>
function go_con_del(fs_ix){
	yes = confirm('정말로 삭제하시 겠습니까?');
	if(yes){
		window.frames['act'].location.href = './found_info.act.php?act=detail_delete&fs_ix='+fs_ix;
	}else{
		return;
	}
}

$(document).ready(function() {	//xq edit IE 11버전에서 문제 잇어서 ck로 교체 2014-08-07 이학봉

	CKEDITOR.replace('contents',{
		startupFocus : false,height:200
	});

	CKEDITOR.replace('reply',{
		startupFocus : false,height:200
	});
});

function common_response_templet(selectbox,id){
	//$('#'+id).val(selectbox.val())

	//alert(selectbox.val());

	var ckEditor = CKEDITOR.instances.reply;

	ckEditor.setData(selectbox.val());


}

</script>";


$Contents = "
<form name='delivery_form' action='found_info.act.php' method='post' onsubmit='return CheckFormValue(this)' target=''>
<input type='hidden' name='act' value='".$act."'>
<input type='hidden' name='fs_ix' value='".$fs_ix."'>
<input type='hidden' name='type' value='".$type."'>

	<table cellSpacing=0 cellPadding=0 width='99%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("창업 지원 서비스 신청", "고객센타 > 창업 지원 서비스 신청", false)."</td>
			</tr>
			<tr>
				<td align=center style='padding: 10px 0px'>
					<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box'>
						<colgroup>
							<col width='20%' />
							<col width='30%' style='padding:0px 0px 0px 10px'/>
							<col width='20%' />
							<col width='30%' style='padding:0px 0px 0px 10px'/>
						</colgroup>
						<tr height='28' valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>제목 <img src='".$required3_path."'></b></td>
							<td class='input_box_item' colspan='3'>
								<input type='text' class='textbox' name='title' id='title' value='".$db->dt[title]."' style='width:200px;' validation=true title='제목'>
							</td>
						</tr>
						<tr height='28' valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>작성자</b></td>
							<td class='input_box_item' colspan='3'>
								<input type='text'  class='textbox' name='name' id='name' value='".($fs_ix != ""?$db->dt[name]:$_SESSION[admininfo][charger])."' style='width:100px;' readonly>
								<input type='hidden' name='code' value='".($fs_ix != ""?$db->dt[code]:$_SESSION[admininfo][charger_ix])."'>
								<input type='hidden' name='id' value='".($fs_ix != ""?$db->dt[id]:$_SESSION[admininfo][charger_id])."'>
							</td>
							
						</tr>
						<tr height='28' valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>핸드폰 <img src='".$required3_path."'></b></td>
							<td class='input_box_item'>
								<input type='text' name='mobile_01' value='".$m_nums[0]."' id='mobile_01' class='textbox' maxlength=3 com_numeric=true validation=true style='width:50px;text-align:center;' title='핸드폰번호' /> - 
								<input type='text' name='mobile_02' value='".$m_nums[1]."' id='mobile_02' class='textbox' maxlength=4 com_numeric=true validation=true style='width:50px;text-align:center; title='핸드폰번호' /> - 
								<input type='text' name='mobile_03' value='".$m_nums[2]."' id='mobile_03' class='textbox' maxlength=4 com_numeric=true validation=true style='width:50px;text-align:center;' title='핸드폰번호' />
							</td>
							<td class='input_box_title'> <b>일반전화 </b></td>
							<td class='input_box_item'>
								<input type='text' name='tel_01' value='".$tel_nums[0]."' id='tel_01' class='textbox' maxlength=3 com_numeric=true style='width:50px;text-align:center;' /> - 
								<input type='text' name='tel_02' value='".$tel_nums[1]."' id='tel_02' class='textbox' maxlength=4 com_numeric=true style='width:50px;text-align:center;' /> - 
								<input type='text' name='tel_03' value='".$tel_nums[2]."' id='tel_03' class='textbox' maxlength=4 com_numeric=true style='width:50px;text-align:center;' />
							</td>
						</tr>
						<tr height='28' valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>이메일 <img src='".$required3_path."'></b></td>
							<td class='input_box_item'>
								<input type='text' name='mail' value='".$db->dt[mail]."' id='mail' class='textbox' style='width:200px;padding-left:10px;' validation=true title='이메일' />
							</td>
							<td class='input_box_title'> <b>처리상태 </b></td>
							<td class='input_box_item'>";
				if($admininfo[admin_level] !=''){
				$Contents .= "<input type='hidden' name='status' value='".$db->dt[status]."' title='문의요청시 처리상태'>";
				}
							if($db->dt[status] == 'W'){
							$Contents .= "	문의요청";
							}else if($db->dt[status] == 'I'){
							$Contents .= "	처리중";
							}else if($db->dt[status] == 'C'){
							$Contents .= "	처리완료";
							}else{
							$Contents .= "	문의요청";
							}

$Contents .= "
							</td>
						</tr>";
if($type != 'C'){
$Contents .= "
						<tr height='28' valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>MD </b></td>
							<td class='input_box_item' colspan='3'>
								".MDSelect($db->dt[md_code], "md_code", "md_name",'','',"validation=false")."
							</td>
						</tr>";
}
			if($admininfo[admin_level] == '9'){
$Contents .= "
						<tr height='28' valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>셀러업체 <img src='".$required3_path."'></b></td>
							<td class='input_box_item' colspan='3'>
								".companyAuthList($company_id , "validation=true title='셀러업체' ")."
							</td>
						</tr>";
			
			}else{
$Contents .= "<input type='hidden' name='company_id' value='".$admininfo[company_id]."'>";
			}
$Contents .= "
						<tr valign='middle' bgcolor=#ffffff>
							<td colspan=4 style='padding:5px'>
								<textarea name='contents' id='contents' class='textbox' style='width:95%;height:85px;resize:none;' validation=true title='내용' >"
									.$db->dt[contents]."
								</textarea>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>";

if(($admininfo[admin_level] == '9' && $fs_ix && $type != 'C') || ($admininfo[admin_level] == '8' && $type == 'C')){	//관리자 긴급사항시 셀러 답변 가능 기타 미노출

$Contents .= "
		<table width='100%' cellpadding=0 cellspacing=0 border='0'>
			<tr>
				<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
					<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>".$cs_title."</b>
				</td>
			</tr>
		</table>";

$Contents .= "
		<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box'>
			<colgroup>
				<col width='20%' />
				<col width='30%' style='padding:0px 0px 0px 10px'/>
				<col width='20%' />
				<col width='30%' style='padding:0px 0px 0px 10px'/>
			</colgroup>
			<tr>
				<td class='input_box_title'> <b>작성자 </b></td>
				<td class='input_box_item'>
					<input type='text'  class='textbox' name='fs_name' id='fs_name' value='".$_SESSION[admininfo][charger]."' style='width:100px;' readonly>
					<input type='hidden' name='charger_ix' value='".$_SESSION[admininfo][charger_ix]."'>
				</td>
				<td class='input_box_title'> <b>처리상태 </b></td>
				<td class='input_box_item'>
					<select id='status' name='status'>
						<option>선택</option>
						<option name='status' value='W' id='status_w' ".(($db->dt[status] == 'W')? "selected":"").">문의요청</option>
						<option name='status' value='I' id='status_i' ".(($db->dt[status] == 'I')? "selected":"").">처리중</option>
						<option name='status' value='C' id='status_c' ".(($db->dt[status] == 'C')? "selected":"").">처리완료</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class='input_box_title'> <b>답변 템플릿 선택 </b></td>
				<td class='input_box_item' colspan='3'>
					".common_response_templet_selectbox($type)."
				</td>
			
			</tr>
			<tr>
				<td class='input_box_item' colspan='4' style='padding:5px;'>
					<textarea name='reply' id='reply' class='textbox' style='width:95%;height:30px;resize:none;'>
						".$db->dt[reply]."
					</textarea>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	</table>";

}

if(($admininfo[admin_level] == '8' && $fs_ix && $type != 'C') || ($admininfo[admin_level] == '9' && $type == 'C')){	//관리자 긴급사항시 관리자 노출 기타 셀러만 노출

$Contents .= "
		<table width='100%' cellpadding=0 cellspacing=0 border='0'>
			<tr>
				<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
					<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>".$cs_title."</b>
				</td>
			</tr>
		</table>";

$Contents .= "
		<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box'>
			<colgroup>
				<col width='20%' />
				<col width='30%' style='padding:0px 0px 0px 10px'/>
				<col width='20%' />
				<col width='30%' style='padding:0px 0px 0px 10px'/>
			</colgroup>
			<tr>
				<td class='input_box_item' colspan='4' style='padding:5px;'>
					<textarea name='reply' id='reply' class='textbox' style='width:95%;height:30px;resize:none;'>
						".$db->dt[reply]."
					</textarea>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	</table>";
}


$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0>
	<tr>
		<td align=center style='padding:10px 0px' colspan=2>";
		
		if($admininfo[admin_level] != '8' || $db->dt[status] == 'W' || $db->dt[status] == ''){

			$Contents .= "<input type='image' src='../images/korea/bts_modify.gif' style='border:0 none;vertical-align:0px;' />";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents .= "
				<a href=\"javascript:go_con_del('".$fs_ix."');\"><img src='../images/".$admininfo[language]."/btn_del.gif' border=0></a>";
			}else{
				$Contents .= "
				<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo[language]."/btn_del.gif' border=0></a>";
			}
		}

$Contents .= "
		</td>
	</tr>
	</TABLE>";


$Contents .= "
</form>
<IFRAME id=act name=act src='' frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>	
";


if($mmode == 'pop'){
	$P = new ManagePopLayOut();
	$P->addScript = $addScript;
	$P->Navigation = "문의요청하기 > 문의요청";
	$P->NaviTitle = "문의요청";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else{

	$P = new LayOut();
	$P->strLeftMenu = seller_menu();
	$P->addScript = $Script;
	$P->Navigation = "문의요청하기 > ".$title;
	$P->title = $menu_name;
	$P->strContents = $Contents;
	$P->PrintLayOut();
}
?>





