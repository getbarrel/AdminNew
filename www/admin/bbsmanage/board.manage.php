<?
include("../class/layout.class");

$db = new Database;

$Script = "	<script language='javascript'>


		function showObj(id)
		{
			obj = eval(id+'.style');
			obj.display = 'block';

			document.lyrstat.opend.value = id;
		}

		function hideObj(id)
		{
			obj = eval(id+'.style');
			obj.display = 'none';

			document.lyrstat.opend.value = '';
		}

		function swapObj(id)
		{

			obj = eval(id+'.style');
			stats = obj.display;

			if (stats == 'none')
			{
				if (document.lyrstat.opend.value)
					hideObj(document.lyrstat.opend.value);

				showObj(id);
			}
			else
			{
				hideObj(id);
			}
		}

		function thum_disable(obj){
			if(obj.value == 'Y'){
				bbs_manage_frm.thum_width.disabled = false;
				bbs_manage_frm.thum_width.style.background = '#ffffff';
				bbs_manage_frm.thum_height.disabled = false;
				bbs_manage_frm.thum_height.style.background = '#ffffff';
			}else{
				bbs_manage_frm.thum_width.disabled = true;
				bbs_manage_frm.thum_width.style.background = '#efefef';
				bbs_manage_frm.thum_height.disabled = true;
				bbs_manage_frm.thum_height.style.background = '#efefef';
			}

		}

		function board_point(view)
		{
			if(view == 1)
			{
				//document.getElementById('point_01').style.display = 'block';
			}
			else if(view == 1)
			{
				//document.getElementById('point_01').style.display = 'none';
			}
		}
		</script>";
/*
<tr>
	    <td align='left' colspan=4 > ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 사업자 정보</b></div>")."</td>
	  </tr>
*/
$db->query("SELECT * FROM bbs_manage_config where bm_ix= '$bm_ix'");
$db->fetch();

if($db->total){
	if($act == "copy"){
		$act = "insert";
		$bm_ix = "";
	}else{
		$act = "update";
		$bm_ix = $db->dt[bm_ix];
		$board_name = $db->dt[board_name];
		$board_ename = $db->dt[board_ename];
	}

	$board_max_cnt = $db->dt[board_max_cnt];
	$bt_ix = $db->dt[board_templete_code];
	$bbs_templet_dir = $db->dt[bbs_templet_dir];
	$board_style = $db->dt[board_style];
	$board_file_yn = $db->dt[board_file_yn];
	$board_hidden_yn = $db->dt[board_hidden_yn];
	$board_response_yn = $db->dt[board_response_yn];
	$board_comment_yn = $db->dt[board_comment_yn];
	$board_thumbnail_yn = $db->dt[board_thumbnail_yn];
	$thum_width = $db->dt[thum_width];
	$thum_height = $db->dt[thum_height];
	$board_user_write_auth_yn = $db->dt[board_user_write_auth_yn];
	$board_admin_write_auth_yn = $db->dt[board_admin_write_auth_yn];
	$board_seller_write_auth_yn = $db->dt[board_seller_write_auth_yn];	//셀러업체 글쓰기 권한
	$board_category_use_yn = $db->dt[board_category_use_yn];
	$recent_list_display = $db->dt[recent_list_display];
	$board_hitcheck_yn = $db->dt[board_hitcheck_yn];
	$board_qna_yn = $db->dt[board_qna_yn];
	$board_recom_yn = $db->dt[board_recom_yn];//추천기능 사용여부 추가 kbk 13/07/08

	$board_group = $db->dt[board_group];
	$board_titlemax_cnt = $db->dt[board_titlemax_cnt];

	$board_searchable = $db->dt[board_searchable];

	$design_width = $db->dt[design_width];
	$design_new_priod = $db->dt[design_new_priod];
	$design_hot_limit = $db->dt[design_hot_limit];

	$view_check_yn = $db->dt[view_check_yn];
	$view_no_yn = $db->dt[view_no_yn];
	$view_title_yn = $db->dt[view_title_yn];
	$view_name_yn = $db->dt[view_name_yn];
	$view_file_yn = $db->dt[view_file_yn];
	$view_date_yn = $db->dt[view_date_yn];
	$view_viewcnt_yn =  $db->dt[view_viewcnt_yn];
	$view_email_yn = $db->dt[view_email_yn];
	$view_sms_yn =  $db->dt[view_sms_yn];
	$view_recommend_yn =  $db->dt[view_recommend_yn];//추천수 추가 kbk 13/07/08
	$view_comment_yn =  $db->dt[view_comment_yn];//댓글수 추가 kbk 13/07/08
	$view_md_name_yn =  $db->dt[view_md_name_yn];//담당MD추가 2014-06-09 이학봉
	$view_read_yn =  $db->dt[view_read_yn];//본인확인 2014-06-09 이학봉

	$board_list_auth = $db->dt[board_list_auth];
	$board_read_auth = $db->dt[board_read_auth];
	$board_comment_auth = $db->dt[board_comment_auth];
	$board_write_auth = $db->dt[board_write_auth];

	$board_point_yn = $db->dt[board_point_yn];
	$board_point_time = $db->dt[board_point_time];
	$write_point = $db->dt[write_point];
	$response_point = $db->dt[response_point];
	$comment_point = $db->dt[comment_point];

	$basic_comment_name = $db->dt[basic_comment_name];


}else{
	$act = "insert";
	$board_style = "bbs";
	$board_hidden_yn = "Y";
	$board_qna_yn = "N";
	$board_category_use_yn = "N";
	$board_response_yn = "Y";
	$board_comment_yn = "Y";
	$board_thumbnail_yn = "N";
	$board_user_write_auth_yn = "Y";
	$board_admin_write_auth_yn = "Y";
	$board_file_yn = "N";
	$board_hitcheck_yn = "N";
	$recent_list_display = "Y";
	$board_recom_yn = "N";//추천기능 사용여부 추가 kbk 13/07/08

	$view_check_yn = 1;
	$view_no_yn = 1;
	$view_title_yn = 1;
	$view_name_yn = 1;
	$view_file_yn = 1;
	$view_date_yn = 1;
	$view_viewcnt_yn =  1;

	$board_point_yn = "N";
	$board_point_time = "A";
	$write_point = "0";
	$response_point = "0";
	$comment_point = "0";

}



$mstring ="<form name=bbs_manage_frm action='board.manage.act.php' onsubmit='return CheckFormValue(this)'>
		<input type=hidden name=act value='$act'>
		<input type=hidden name=bm_ix value='$bm_ix'>
		<input type=hidden name=mmode value='$mmode'>
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed'>
		<col width='18%'>
		<col width='32%'>
		<col width='18%'>
		<col width='32%'>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("게시판 설정", "게시판관리 > 게시판 설정 ")."</td>
		</tr>
		<tr>
			<td align='left' colspan=4 style='padding:2px 0 2px 0;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle><b> 게시판 정보</b></div>")."</td>
		</tr>
		</table>
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed' class='input_table_box'>
		<col width='18%'>
		<col width='32%'>
		<col width='18%'>
		<col width='32%'>
		<tr >
			<td class='input_box_title'  > 게시판 그룹설정 </td>
			<td class='input_box_item'  colspan=3 style='padding:5px;'>
				<table cellpadding=0 cellsapcing=0>
					<tr>
						<td>".board_group()."</td>
					</tr>
					<tr height=25><td ><img src='../image/emo_3_15.gif' align=absmiddle> <span class='small' ><!--게시판 그룹을 선택하시면 그룹에 맞게 노출되며 일반 게시판의 경우는 자동 노출되지 않습니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C' )." </span></td></tr>
				</table>
			</td>
		</tr>

		<tr >
			<td class='input_box_title' > 게시판 타이틀 </td>
			<td class='input_box_item' colspan=3 width='100%'>
				<input type=text name='board_name' class='textbox' value='".$board_name."' style='width:200px' validation=true title='게시판 타이틀'>
			</td>
		</tr>
		<tr >
			<td class='input_box_title' > 게시판 영문 이름 </td>
			<td class='input_box_item' colspan=3>
				<input type=hidden name='bf_board_ename' value='".$board_ename."' >
				<input type=text name='board_ename' class='textbox' value='".$board_ename."' style='width:200px;ime-mode:disabled;' validation=true dbtable=true title='게시판 이름'> &nbsp;&nbsp;&nbsp;<img src='../image/emo_3_15.gif' align=absmiddle> <span class='small' ><!--영문으로 반드시 입력해주세요 테이블 생성시 사용됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D' )."</span>
				".($admininfo[charger_id]=='forbiz' ? '오라클디비일땐 최대영문14글자로 이름지어야함' : '')."
			</td>
		</tr>
		<tr >
			<td class='input_box_title'> 게시물 목록수 </td>
			<td class='input_box_item'>
			<input type=text name='board_max_cnt' class='textbox' value='".$board_max_cnt."' style='width:40px' validation=true title='게시물 목록수'> &nbsp;&nbsp;&nbsp;<img src='../image/emo_3_15.gif' align=absmiddle> <span class='small' ><!--한페이지에 보여지는 목록수-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'E' )."</span>
			</td>
			<td class='input_box_title'> 게시물제목 글자제한 </td>
			<td class='input_box_item' >
			<input type=text name='board_titlemax_cnt' class='textbox' value='".$board_titlemax_cnt."' style='width:40px'>  <span class='small' ><!--게시물 제목의 byte 수--></span>
			</td>
		</tr>
		<tr >
			<td class='input_box_title' > 템플릿 선택 </td>
			<td class='input_box_item' colspan=3>";
			if(in_array($bbs_templet_dir,$exclue_bbs_templet)){
			$mstring .= "<b>".$bbs_templet_dir ."</b><input type='hidden' name='bbs_templet_dir' value='".$bbs_templet_dir."'>";
			}else{
			//$mstring .= SelectDirList("bbs_templet_dir",$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/bbs_templet",$bbs_templet_dir);
			$mstring .= SelectDirList("bbs_templet_dir",$_SERVER["DOCUMENT_ROOT"]."/bbs_templet",$bbs_templet_dir);
			}
$mstring .= "
			</td>
		</tr>
		<tr >
			<td class='input_box_title' > 게시판 스타일</td>
			<td class='input_box_item' colspan=3>".($board_style ? "<input type='hidden' name='board_style' value='$board_style'>":"")."
			<input type=radio name='board_style' value='bbs' id='board_style1'  ".CompareReturnValue("bbs",$board_style,"checked")." ".($bm_ix != "" ? "disabled":"")." ><label for='board_style1'>일반게시판</label><input type=radio name='board_style' value='faq' id='board_style2' ".CompareReturnValue("faq",$board_style,"checked")." ".($bm_ix != "" ? "disabled":"")." ><label for='board_style2'>faq 게시판</label>
			
			<!--input type=radio name='board_style' value='attend' id='board_style2' ".CompareReturnValue("attend",$board_style,"checked")." ".($bm_ix != "" ? "disabled":"")." ><label for='board_style2'>출석 게시판</label--> &nbsp;&nbsp;&nbsp;<img src='../image/emo_3_15.gif' align=absmiddle> <b><span class='small' ><!--게시판 생성시에만 선택가능합니다. 생성후 변경 불가-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'G' )."</span></b>
			</td>
		</tr>
		</table>
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed;margin-top:20px;'
		<tr>
			<td align='left' colspan=4 style='padding:2px 0 2px 0;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle><b> 게시판 기능/권한 설정</b></div>")."</td>
		</tr>
		</table>
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed' class='input_table_box'>
		<col width='18%'>
		<col width='32%'>
		<col width='18%'>
		<col width='32%'>
		<tr >
			<td class='input_box_title' > 게시판 분류 사용여부 </td>
			<td class='input_box_item'  colspan=3>
			<input type=radio name='board_category_use_yn' value='Y' id='oboard_category_use_yn1'  ".CompareReturnValue("Y",$board_category_use_yn,"checked")."><label for='oboard_category_use_yn1'>사용</label><input type=radio name='board_category_use_yn' value='N' id='oboard_category_use_yn2' ".CompareReturnValue("N",$board_category_use_yn,"checked")."><label for='oboard_category_use_yn2'>사용하지 않음</label> &nbsp;&nbsp;&nbsp;<img src='../image/emo_3_15.gif' align=absmiddle> <span class='small' ><!--분류를 사용하시려면 선택해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'H' )."</span>
			</td>
		</tr>
		<tr >
			<td class='input_box_title'> 비밀글 사용여부 </td>
			<td class='input_box_item'  colspan=3>
			<input type=radio name='board_hidden_yn' value='Y' id='oboard_hidden_yn1'  ".CompareReturnValue("Y",$board_hidden_yn,"checked")."><label for='oboard_hidden_yn1'>사용</label><input type=radio name='board_hidden_yn' value='N' id='oboard_hidden_yn2' ".CompareReturnValue("N",$board_hidden_yn,"checked")."><label for='oboard_hidden_yn2'>사용하지 않음</label> &nbsp;&nbsp;&nbsp;<img src='../image/emo_3_15.gif' align=absmiddle><span class='small' > <!--비밀글을 사용하시면 자신의 글만 읽을수 있습니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'I' )."</span>
			</td>
		</tr>
		<tr >
			<td class='input_box_title' > 문의게시판 사용여부 </td>
			<td class='input_box_item'  colspan=3>
			<input type=radio name='board_qna_yn' value='Y' id='oboard_qna_yn1'  ".CompareReturnValue("Y",$board_qna_yn,"checked")." onclick=\"$('input[name=board_response_yn]').attr('disabled',true);\"><label for='oboard_qna_yn1'>사용</label>
			<input type=radio name='board_qna_yn' value='N' id='oboard_qna_yn2' ".CompareReturnValue("N",$board_qna_yn,"checked")." onclick=\"$('input[name=board_response_yn]').attr('disabled',false);\"><label for='oboard_qna_yn2'>사용하지 않음</label> &nbsp;&nbsp;&nbsp;<img src='../image/emo_3_15.gif' align=absmiddle><span class='small' > <!--사용시 자신의글만 볼수 있으며 댓글내용이 답변으로 활용됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'J' )." </span>
			</td>
		</tr>
		<tr >
			<td class='input_box_title' > 파일업로드 사용여부 </td>
			<td class='input_box_item'  colspan=3>
			<input type=radio name='board_file_yn' value='Y' id='oboard_file_yn1'  ".CompareReturnValue("Y",$board_file_yn,"checked")."><label for='oboard_file_yn1'>사용</label><input type=radio name='board_file_yn' value='N' id='oboard_file_yn2' ".CompareReturnValue("N",$board_file_yn,"checked")."><label for='oboard_file_yn2'>사용하지 않음</label>
			</td>
		</tr>
		<tr >
			<td class='input_box_title' > 답변 사용여부 </td>
			<td class='input_box_item' colspan=3>
			<input type=radio name='board_response_yn' value='Y' id='oboard_response_yn1' ".($board_response_yn == "Y" ? "checked":"")." ".($board_qna_yn == "Y" ? "disabled":"")."><label for='oboard_response_yn1'>사용</label><input type=radio name='board_response_yn' value='N' id='oboard_response_yn2' ".($board_response_yn == "N" ? "checked":"")." ".($board_qna_yn == "Y" ? "disabled":"")."><label for='oboard_response_yn2'>사용하지 않음</label> &nbsp;&nbsp;&nbsp;<img src='../image/emo_3_15.gif' align=absmiddle> <span class='small' ><!--선택시 계층형 게시판으로 사용하실수 있습니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'K' )."</span>
			</td>
		</tr>
		<tr >
			<td class='input_box_title' > 댓글 사용여부 </td>
			<td class='input_box_item'  colspan=3>
			<input type=radio name='board_comment_yn' value='Y' id='oboard_comment_yn1' ".CompareReturnValue("Y",$board_comment_yn,"checked")."><label for='oboard_comment_yn1'>사용</label><input type=radio name='board_comment_yn' value='N' id='oboard_comment_yn2' ".CompareReturnValue("N",$board_comment_yn,"checked")."><label for='oboard_comment_yn2'>사용하지 않음</label>
			&nbsp;&nbsp;&nbsp;
			기본 답변처리자 <input type='text' name='basic_comment_name' class='textbox' size='10' value='$basic_comment_name'> <img src='../image/emo_3_15.gif' align=absmiddle><span class='small'> 기본 답변처리자 미입력시 로그인한 관리자 이름이 노출이됩니다. </span>
			</td>
		</tr>
		<tr >
			<td class='input_box_title' > 썸네일 사용여부 </td>
			<td class='input_box_item' colspan=3>
			<input type=radio name='board_thumbnail_yn' value='Y' id='oboard_thumbnail_yn1' ".CompareReturnValue("Y",$board_thumbnail_yn,"checked")." onclick='thum_disable(this)'><label for='oboard_thumbnail_yn1'>사용</label><input type=radio name='board_thumbnail_yn' value='N' id='oboard_thumbnail_yn2' ".CompareReturnValue("N",$board_thumbnail_yn,"checked")." onclick='thum_disable(this)'><label for='oboard_thumbnail_yn2'>사용하지 않음</label>
			&nbsp;&nbsp;&nbsp;
			가로 <input type='text' name='thum_width' class='textbox' size='5' value='$thum_width' ".CompareReturnValue("N",$board_thumbnail_yn,"disabled style='background:#efefef'")."> px ,
			높이 <input type='text' name='thum_height' class='textbox' size='5' value='$thum_height' ".CompareReturnValue("N",$board_thumbnail_yn,"disabled style='background:#efefef'")."> px
			</td>
		</tr>
		<tr >
			<td class='input_box_title' > 조회수 중복불가 </td>
			<td class='input_box_item' colspan=3>
			<input type=radio name='board_hitcheck_yn' value='Y' id='oboard_hitcheck_yn1' ".CompareReturnValue("Y",$board_hitcheck_yn,"checked")."><label for='oboard_hitcheck_yn1'>사용</label><input type=radio name='board_hitcheck_yn' value='N' id='oboard_hitcheck_yn2' ".CompareReturnValue("N",$board_hitcheck_yn,"checked")."><label for='oboard_hitcheck_yn2'>사용하지 않음</label>
			</td>
		</tr>
		<!--tr >
			<td class='input_box_title' > 추천기능 사용여부 </td>
			<td class='input_box_item' colspan=3>
			<input type=radio name='board_recom_yn' value='Y' id='oboard_recom_yn1' ".CompareReturnValue("Y",$board_recom_yn,"checked")."><label for='oboard_recom_yn1'>사용</label><input type=radio name='board_recom_yn' value='N' id='oboard_recom_yn2' ".CompareReturnValue("N",$board_recom_yn,"checked")."><label for='oboard_recom_yn2'>사용하지 않음</label>
			</td>
		</tr-->
		<tr >
			<td class='input_box_title'> 쓰기권한 (사용자) </td>
			<td class='input_box_item' colspan=3>
				<table cellpadding=0 cellsapcing=0>
					<tr height=40>
						<td>
							<input type=radio name='board_user_write_auth_yn' value='Y' id='oboard_userwrite_auth_yn1' ".CompareReturnValue("Y",$board_user_write_auth_yn,"checked")."><label for='oboard_user_write_auth_yn1'>사용</label><input type=radio name='board_user_write_auth_yn' value='N' id='oboard_user_write_auth_yn2' ".CompareReturnValue("N",$board_user_write_auth_yn,"checked")."><label for='oboard_user_write_auth_yn2'>사용하지 않음</label>
						</td>
					</tr>
					<tr height=25>
						<td >
							<table cellpadding=1 cellspacing=1 bgcolor=silver width=600 class='list_table_box'>
							<tr bgcolor='#efefef' height=28 align=center>
								<td class='s_td'>리스트 보기</td>
								<td class='m_td'>내용 보기</td>
								<td class='m_td'>댓글 쓰기</td>
								<td class='e_td'>글쓰기</td>
							</tr>
							<tr bgcolor='#fbfbfb' height=28 align=center>
								<td class='list_box_td '>".makeSelectBox($db,"board_list_auth",$board_list_auth)."</td>
								<td class='list_box_td '>".makeSelectBox($db,"board_read_auth",$board_read_auth)."</td>
								<td class='list_box_td '>".makeSelectBox($db,"board_comment_auth",$board_comment_auth)."</td>
								<td class='list_box_td '>".makeSelectBox($db,"board_write_auth",$board_write_auth)."</td>
							</tr>
							</table><br>
							<span class='small' style='line-height:120%;'><!--사용자 권한은 1 ~ 9 등급순으로 1등급이 가장 높은 등급니다. 선택한 등급보다 높은 등급은 글쓰기가 가능합니다 회원 이상 글쓰기를 원하시면 가장 낮은 등급(9등급)을 선택하시면 됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'L' )."</span>
						</td>
					</tr>
				</table>

			</td>
		</tr>

		<tr >
			<td class='input_box_title' > 쓰기권한 (관리자) </td>
			<td class='input_box_item' colspan=3>
			<input type=radio name='board_admin_write_auth_yn' value='Y' id='oboard_admin_write_auth_yn1' ".CompareReturnValue("Y",$board_admin_write_auth_yn,"checked")."><label for='oboard_admin_write_auth_yn1'>사용</label><input type=radio name='board_admin_write_auth_yn' value='N' id='oboard_admin_write_auth_yn2' ".CompareReturnValue("N",$board_admin_write_auth_yn,"checked")."><label for='oboard_admin_write_auth_yn2'>사용하지 않음</label>
			</td>
		</tr>
		<tr >
			<td class='input_box_title' > 쓰기권한 (셀러) </td>
			<td class='input_box_item' colspan=3>
			<input type=radio name='board_seller_write_auth_yn' value='Y' id='board_seller_write_auth_yn_y' ".CompareReturnValue("Y",$board_seller_write_auth_yn,"checked")."><label for='board_seller_write_auth_yn_y'>사용</label>
			<input type=radio name='board_seller_write_auth_yn' value='N' id='board_seller_write_auth_yn_n' ".CompareReturnValue("N",$board_seller_write_auth_yn,"checked")."><label for='board_seller_write_auth_yn_n'>사용하지 않음</label>
			</td>
		</tr>
		<tr >
			<td class='input_box_title'  class=small> 최근게시물관리 노출 </td>
			<td class='input_box_item' colspan=3>
			<input type=radio name='recent_list_display' value='Y' id='recent_list_display_1' ".CompareReturnValue("Y",$recent_list_display,"checked")."><label for='recent_list_display_1'>노출함</label>
			<input type=radio name='recent_list_display' value='N' id='recent_list_display_2' ".CompareReturnValue("N",$recent_list_display,"checked")."><label for='recent_list_display_2'>노출하지  않음</label>
			&nbsp;&nbsp;&nbsp;<img src='../image/emo_3_15.gif' align=absmiddle> <span class='small' > <!--최근 게시물관리에 노출여부를 설정합니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'M' )."
			</td>
		</tr>
		<!--tr >
			<td class='input_box_title' > 통합검색 노출여부 </td>
			<td class='input_box_item' colspan=3>
			<input type=radio name='board_searchable' value='1' id='board_searchable_1' ".CompareReturnValue("1",$board_searchable,"checked")."><label for='board_searchable_1'>노출</label>
			<input type=radio name='board_searchable' value='0' id='board_searchable_2' ".CompareReturnValue("0",$board_searchable,"checked")."><label for='board_searchable_2'>노출하지 않음</label>
			</td>
		</tr-->
		</table>
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed;padding-top:20px;' >
		<col width='18%'>
		<col width='32%'>
		<col width='18%'>
		<col width='32%'>
		<tr>
			<td align='left' colspan=4 style='padding:2px 0 2px 0;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle><b> 게시판 디자인 설정</b></div>")."</td>
		</tr>
		</table>
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed' class='input_table_box'>
		<col width='18%'>
		<col width='32%'>
		<col width='18%'>
		<col width='32%'>
		<tr >
			<td class='input_box_title' > 게시판 넓이 </td>
			<td class='input_box_item' colspan=3>
			<input type=text name='design_width' class='textbox' value='".$design_width."' style='width:100px' validation=true title='게시판 넓이'> &nbsp;&nbsp;&nbsp;<img src='../image/emo_3_15.gif' align=absmiddle> <span class='small' ><!--게시판의 넓이 px 또는 % 로 설정 예) 700px  또는 100% -->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'N' )."</span>
			</td>
		</tr>
		<tr >
			<td class='input_box_title'  class=small> NEW아이콘 시간설정 </td>
			<td class='input_box_item' colspan=3>
			<input type=text name='design_new_priod' class='textbox' value='".$design_new_priod."' style='width:70px' validation=true title='NEW아이콘 시간설정'>  시간
			</td>
			<!--td class='input_box_title' > HOT아이콘 조회수설정 </td>
			<td class='input_box_item' >
			<input type=text class='textbox' name='design_hot_limit' value='".$design_hot_limit."' style='width:70px'>
			</td-->
		</tr>
		<tr >
			<td class='input_box_title' > 리스트 노출항목 </td>
			<td class='input_box_item' colspan=3 style='padding:5px;'>
				<table cellpadding=0 cellsapcing=0>
					<tr height=30>
						<td>
							<input type=checkbox name='view_check_yn' id='view_check_yn' value='1' ".($view_check_yn == "1" ? "checked":"")."><label for='view_check_yn'>체크박스</label>
							<input type=checkbox name='view_no_yn' id='view_no_yn' value='1' ".($view_no_yn == "1" ? "checked":"")."><label for='view_no_yn'>넘버</label>
							<input type=checkbox name='view_title_yn' id='view_title_yn' value='1' ".($view_title_yn == "1" ? "checked":"")."><label for='view_title_yn'>제목</label>
							<input type=checkbox name='view_name_yn' id='view_name_yn' value='1' ".($view_name_yn == "1" ? "checked":"")."><label for='view_name_yn'>글쓴이</label>
							<input type=checkbox name='view_file_yn' id='view_file_yn' value='1' ".($view_file_yn == "1" ? "checked":"")."><label for='view_file_yn'>파일</label>
							<input type=checkbox name='view_date_yn' id='view_date_yn' value='1' ".($view_date_yn == "1" ? "checked":"")."><label for='view_date_yn'>날짜</label>
							<input type=checkbox name='view_viewcnt_yn' id='view_viewcnt_yn' value='1' ".($view_viewcnt_yn == "1" ? "checked":"")."><label for='view_viewcnt_yn'>조회수(처리상태)</label>
							<input type=checkbox name='view_email_yn' id='view_email_yn' value='1' ".($view_email_yn == "1" ? "checked":"")."><label for='view_email_yn'>이메일</label>
							<input type=checkbox name='view_sms_yn' id='view_sms_yn' value='1' ".($view_sms_yn == "1" ? "checked":"")."><label for='view_sms_yn'>SMS</label>
							<input type=checkbox name='view_recommend_yn' id='view_recommend_yn' value='1' ".($view_recommend_yn == "1" ? "checked":"")."><label for='view_recommend_yn'>추천수</label>
							<input type=checkbox name='view_comment_yn' id='view_comment_yn' value='1' ".($view_comment_yn == "1" ? "checked":"")."><label for='view_comment_yn'>댓글수</label>
							<input type=checkbox name='view_md_name_yn' id='view_md_name_yn' value='1' ".($view_md_name_yn == "1" ? "checked":"")."><label for='view_md_name_yn'>MD담당자</label>
							<input type=checkbox name='view_read_yn' id='view_read_yn' value='1' ".($view_read_yn == "1" ? "checked":"")."><label for='view_read_yn'>본인확인</label>
						</td>
					</tr>
					<tr height=25>
						<td >
							<img src='../image/emo_3_15.gif' align=absmiddle> <span class='small' style='line-height:120%;'><!--노출을 원하는 항목만 체크해주세요, 노출을 원하지 않으시면 체크박스를 해제해 주세요 <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;문의게시판 사용을 체크하셨으면 조회수는 처리상태로 노출됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'O' )."</span>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</table>";
		if($admininfo[mall_type] != "H"){
		$mstring .="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='display:none;table-layout:fixed;padding-top:20px;'>
		<tr>
			<td align='left' colspan=4 style='padding:2px 0 2px 0;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle><b> 게시판 포인트 설정</b></div>")."</td>
		</tr>
		</table>";
		}
		$mstring .="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed' class='input_table_box'>
		<col width='18%'>
		<col width='32%'>
		<col width='18%'>
		<col width='32%'>";
		if($admininfo[mall_type] != "H"){
		$mstring .="
		<tr>
			<td class='input_box_title' > 포인트 적립여부 </td>
			<td class='input_box_item' >
				<input type=radio name='board_point_yn' value='Y' id='board_point_yn1' ".CompareReturnValue("Y",$board_point_yn,"checked")." onclick='board_point(1)'>
				<label for='board_point_yn1'>사용</label>
				<input type=radio name='board_point_yn' value='N' id='board_point_yn2' ".CompareReturnValue("N",$board_point_yn,"checked")." onclick='board_point(0)'>
				<label for='board_point_yn2'>사용하지 않음</label>
			</td>
			<td class='input_box_title' > 포인트 적용시점 </td>
			<td class='input_box_item' >
				<input type=radio name='board_point_time' value='R' id='board_point_time_r' ".CompareReturnValue("R",$board_point_time,"checked")." >
				<label for='board_point_time_r'>즉시</label>
				<input type=radio name='board_point_time' value='A' id='board_point_time_a' ".CompareReturnValue("A",$board_point_time,"checked")." >
				<label for='board_point_time_a'>승인후</label>
			</td>
		</tr>
		<tr>
			<td class='input_box_title' > 적립 포인트 </td>
			<td class='input_box_item' colspan=3>
				<table>
					<tr><td>글쓰기시 </td><td><input type=text name='write_point' class='textbox' value='".$write_point."' style='width:60px' validation=false title='글쓰기시 포인트'> 적립 </td></tr>
					<tr><td>답변시 </td><td><input type=text name='response_point' class='textbox' value='".$response_point."' style='width:60px' validation=false title='답변시 포인트'> 적립 </td></tr>
					<tr><td>답글시 </td><td><input type=text name='comment_point' class='textbox' value='".$comment_point."' style='width:60px' validation=false title='답글시 포인트'> 적립 </td></tr>
				</table>
				<!--select name=''>
					<option value='1'>1</option>
					<option value='2'>2</option>
					<option value='3'>3</option>
					<option value='4'>4</option>
					<option value='5'>5</option>
					<option value='6'>6</option>
					<option value='7'>7</option>
					<option value='8'>8</option>
					<option value='9'>9</option>
					<option value='10'>10</option>
				</select-->
			</td>
		</tr>";
		}
/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >디자인 구성페이지에 아래 함수를 복사해넣어주면 게시판이 노출됩니다   </td></tr>
	<tr><td valign=top></td><td class='small' ><b>함수설명</b> : print_bbs(게시판코드, 게시판모드(list, write, modify, response ...), 게시판 액션(insert, update, delete ...), 페이지 코드) </td></tr>
	<tr height=10><td valign=top></td></tr>
	";

	$help_text .= " <tr height=20><td valign=top></td><td >고객센타 게시판 일경우 : {=print_bbs(_GET[\"board\"],_GET[\"mode\"], _GET[\"act\"],\"010001000000000\")} </td></tr>";

	$help_text .= " <tr height=20><td valign=top></td><td >커뮤니티 게시판 일경우 : {=print_bbs(_GET[\"board\"],_GET[\"mode\"], _GET[\"act\"],\"008000000000000\")}</td></tr>";

	$help_text .= " <tr height=20><td valign=top></td><td >일반 게시판 일경우 : {=print_bbs('$board_ename',_GET[\"mode\"], _GET[\"act\"],\"페이지코드\")} </td></tr>";

$help_text .= "</table>";*/

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

if($board_ename){
	$help_text = HelpBox("게시판 함수 ", $help_text);
$mstring .="</table>
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed' >
		<tr height=170><td colspan=4 >$help_text</td></tr>";
}

$mstring .="</table>
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed' >
		<col width='18%'>
		<col width='32%'>
		<col width='18%'>
		<col width='32%'>
		<tr bgcolor=#ffffff >
            <td colspan=4 align=right style='padding:10px 0px;'>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $mstring.="
                <input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' >";
            }else{
                $mstring.="
                <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif'  align=absmiddle border=0 style='cursor:hand;border:0px;' >";
            }
            $mstring.="
                <img src='../images/".$admininfo["language"]."/b_cancel.gif' border=0 style='cursor:hand;border:0px;' align=absmiddle onclick='history.back();'>
            </td>
        </tr>
		</form>";
$mstring .="</table>";
/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>게시판 타이틀</u>은 사용자가 게시판을 구분할수 있는 이름입니다 </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>게시판 이름</u>은 반드시 영문으로 입력하셔야 합니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>카테고리 사용여부</u>에서 '사용' 을 선택하시면 게시판 생성후 게시판 분류관리를 하실수 있습니다</td></tr>
</table>
";
*/

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');

$help_text = HelpBox("게시판 설정", $help_text);

$Contents = $mstring.$help_text."<br><br><br><br>";

//$Script = "<script language='javascript' src='basicinfo.js'></script>";
if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = bbsmanage_menu();
	$P->Navigation = "게시판관리 > 게시판설정";
	$P->title = "게시판설정";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = bbsmanage_menu();
	$P->Navigation = "게시판관리 > 게시판설정";
	$P->title = "게시판설정";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}

function makeSelectBox($mdb,$select_name,$gp_level){
	$mdb->query("SELECT * FROM ".TBL_SHOP_GROUPINFO." where disp=1 order by gp_level asc ");

	$mstring = "<select name='$select_name' class=small style='width:100px;'>";
	$mstring .= "<option value='0'>전체보기</option>";
	if($mdb->total){
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$mstring .= "<option value='".$mdb->dt[gp_level]."' ".($mdb->dt[gp_level] == $gp_level ? "selected":"").">".$mdb->dt[gp_name]."  (레벨 : ".$mdb->dt[gp_level].")</option>";
		}
	}else{
		$mstring .= "<option value=''>".$msg."</option>";
	}
	$mstring .= "</select>";

	return $mstring;
}

function FieldInsert($pollnumber, $fieldnumber, $disp){
$dbm = new Database;
$dbm->query("SELECT * FROM ".TBL_SHOP_POLL_FIELD." where number = '$pollnumber' order by fieldnumber");
if($dbm->total > 0){
	$actstring = "fieldupdate";
	$submitstring = "수정하기";
}else{
	$actstring = "fieldinsert";
	$submitstring = "저장하기";
}

	$mstring = "<div id='TG_VIEW_".$pollnumber."' style='position: relative; display: none;'>";
	$mstring .="<form name='field$pollnumber' action='poll.act.php'><input type=hidden name=pollnumber value=$pollnumber><input type=hidden name=act value=$actstring><input type=hidden name=fieldsize value='$fieldnumber'>";
	$mstring .= "<table cellapdding=0 cellspaicng=0>";
	for($i=0;$i<$fieldnumber;$i++){
		$dbm->fetch($i);
		if($i==0){
			$mstring .= "<tr><td>".($i+1)."</td><td><input type=text name=fielddesc".($i)." size=40 value='".$dbm->dt[fielddesc]."'></td><td  valign=top style='padding-left:10px;' rowspan=10>표시 : <input type='checkbox' name='disp' style='border:1px solid #ffffff' value=1 ".($disp==1 ? "checked":"")."> &nbsp;&nbsp;&nbsp;&nbsp;<input type=submit value='$submitstring'></td></tr>";
		}else{
			$mstring .= "<tr><td>".($i+1)."</td><td><input type=text name=fielddesc".($i)." size=40 value='".$dbm->dt[fielddesc]."'></td></tr>";
		}
	}
	$mstring .= "</table></form></div>";

	return $mstring;

}

/*
function SelectFieldNumber($selectfield)
{
	$divname = array ("1","2","3","4","5","6","7","8","9");

	$pos = 0;
	$strDiv = "<Select name='fieldnum'>\n";
	$strDiv = $strDiv."<option value=0>항목수</option>\n";
	while(hasMoreElements(&$divname))
	{
	       	if( $pos == $selectdiv )
	       	{
	        	$strDiv = $strDiv."<option value='".($pos+1)."' Selected>".$divname[$pos]."</option>\n";
	       	}else{
	       		$strDiv = $strDiv."<option value='".($pos+1)."'>".$divname[$pos]."</option>\n";
		}
	       	$pos++;
	}

	$strDiv = $strDiv."</Select>\n";

	return $strDiv;

}
*/
function board_group()
{
	global $board_group;

	$mdb = new Database;

	$sql = "select div_ix,div_name from bbs_group where disp = '1'";
	$mdb->query($sql);

	if($mdb->total)
	{
		for($i = 0;$i < $mdb->total;$i++)
		{
			$mdb->fetch($i);
		
			$mstring .= "<input type=radio name='board_group' id='group_".$mdb->dt["div_ix"]."' value='".$mdb->dt["div_ix"]."' ".($board_group == $mdb->dt["div_ix"] ? "checked":"")." validation='true' title='게시판 그룹' /><label for='group_".$mdb->dt["div_ix"]."'>".$mdb->dt["div_name"]."</label>";
			
		}
	}

	return $mstring;
}


/*
alter table bbs_manage_config add board_titlemax_cnt int(3) default 20 after board_max_cnt; -- 제목글자수 제한
alter table bbs_manage_config add design_width varchar(10) default '100%' after board_titlemax_cnt; -- 게시판 넓이
alter table bbs_manage_config add design_new_priod int(3) default 24 after design_width; -- NEW 아이콘 효력 시간
alter table bbs_manage_config add design_hot_limit int(3) default 50 after design_new_priod; -- HOT 아이콘 제한
alter table bbs_manage_config add board_searchable enum('0','1') default '1' after design_hot_limit; -- 통합검색 노출여부
alter table bbs_manage_config add board_ip_viewable enum('0','1') default '1' after board_searchable; -- IP 노출여부
alter table bbs_manage_config add board_ip_encoding enum('0','1') default '1' after board_ip_viewable; -- IP 암호화 여부
alter table bbs_manage_config add board_group enum('H','C','G') default 'H' after board_ip_encoding; -- 게시판 그룹 H (help):  고객센타 , C(community) : 커뮤니티, G(general) : 일반게시판
alter table bbs_manage_config add board_list_auth int(2) default '1' after board_group; -- 리스트 보기 사용자 권한
alter table bbs_manage_config add board_read_auth int(2) default '1' after board_list_auth ; -- 읽기 사용자 권한
alter table bbs_manage_config add board_comment_auth int(2) default '1' after board_read_auth  ; -- 콤멘트 쓰기 사용자 권한
alter table bbs_manage_config add board_write_auth int(2) default '1' after board_comment_auth ; -- 쓰기 사용자권한

alter table bbs_manage_config add view_check_yn enum('0','1') default '1' after board_write_auth ;  -- 리스트에  체크박스 노출여부
alter table bbs_manage_config add view_no_yn enum('0','1') default '1' after view_check_yn ;  -- 리스트에 넘버 노출여부
alter table bbs_manage_config add view_title_yn enum('0','1') default '1' after view_no_yn ;   -- 리스트에 제목 노출여부
alter table bbs_manage_config add view_name_yn enum('0','1') default '1' after view_title_yn  ;  -- 리스트에 이름 노출여부
alter table bbs_manage_config add view_file_yn enum('0','1') default '1' after view_name_yn ;   -- 리스트에 파일 노출여부
alter table bbs_manage_config add view_date_yn enum('0','1') default '1' after view_file_yn  ;   -- 리스트에 날짜 노출여부
alter table bbs_manage_config add view_viewcnt_yn enum('0','1') default '1' after view_date_yn;   -- 리스트에 조회수 노출여부

alter table bbs_manage_config add image_click enum('V','P','LP') default 'V' after view_viewcnt_yn;  -- 이미지 클릭시 액션 여부 V : 읽기 페이지로 이동, P :  팝업 , LP : 레이어 팝업
alter table bbs_manage_config add break_autowrite enum('0','1') default '0' after image_click;   -- 자동글쓰기 방지 기능
alter table bbs_manage_config add break_autocomment enum('0','1') default '0' after break_autowrite;  -- 자동 컴멘트 달기 방지 기능

alter table bbs_manage_config add break_autocomment enum('0','1') default '0' after board_hitcheck_yn;

alter table bbs_manage_config add board_point_yn enum('Y','N') default 'N' after board_hitcheck_yn;
alter table bbs_manage_config add board_point_time enum('R','A') default 'A' after board_point_yn;
alter table bbs_manage_config add write_point int(8) default 0 after board_point_time;
alter table bbs_manage_config add response_point int(8) default 0 after write_point;
alter table bbs_manage_config add comment_point int(8) default 0 after response_point;

*/

/*

create table companyinfo (
company_id varchar(32) not null ,
company_name varchar(50) null default null,
business_number varchar(40) null default null,
business_kind varchar(40) null default null,
ceo varchar(20) null default null,
business_item varchar(50) null default null,
company_address varchar(200) null default null,
bank_owner varchar(20) null default null,
bank_name varchar(20) null default null,
bank_number varchar(30) null default null,
business_day datetime null default null,
admin_id varchar(20) null default null,
admin_pass varchar(32) null default null,
phone varchar(20) null default null,
fax varchar(20) null default null,
charger varchar(20) null default null,
charger_email varchar(20) null default null,
homepage varchar(50) null default null,
shipping_company varchar(30) null default null,
primary key(company_id));
*/
?>