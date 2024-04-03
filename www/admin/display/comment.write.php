<?
/*
메인프로모션 분류 추가로 인해 이미지명이 중복되어 저장되므로 분류 코드를 이미지명 앞에 붙여서 중복 저장을 막음(기존에 쓰던 방식은 그대로 유지) kbk 13/05/09
*/
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");

include("../class/layout.class");
include("../display/display.lib.php");
include("../display/main_display.lib.php");
if($admininfo[mall_type] == "H"){
	header("Location:./popup.list.php");
}

$sql = "SELECT * FROM shop_comment WHERE cmt_ix ='".$cmt_ix."' ";

$slave_db->query($sql); //AND cid='$cid'
if($slave_db->total){
	$slave_db->fetch();
	$title 						= $slave_db->dt[title];
	$title_en 					= $slave_db->dt[title_en];
	$comment_use 				= $slave_db->dt[comment_use];
	$comment_state				= $slave_db->dt[comment_state];
	$information				= $slave_db->dt[information];

	$comment_notify				= $slave_db->dt[comment_notify];
	$comment_limit				= $slave_db->dt[comment_limit];
	$comment_secret_use			= $slave_db->dt[comment_secret_use];
	$comment_answer_use			= $slave_db->dt[comment_answer_use];
	$comment_img_use			= $slave_db->dt[comment_img_use];

	$comment_view_permission	= $slave_db->dt[comment_view_permission];
	$comment_sug_use			= $slave_db->dt[comment_sug_use];
	$comment_start				= date("Y-m-d H:i:s",$slave_db->dt[comment_start]);
	$comment_end				= date("Y-m-d H:i:s",$slave_db->dt[comment_end]);
}

$Script = "
<style type='text/css'>
    .slide-up-down-link { display:block;position:absolute;right:55px;top:13px; }
	.slide-up-down-link .plus { display:none; }
	.slide-up-down-link.closed .minus { display:none; }
	.slide-up-down-link.closed .plus { display:inline; }
	.slide-up-down-all { margin-bottom:10px; }
	.slide-up-down-all .slide-up-down-link { 
		position:static; 
		font-weight:bold;
	}
	.slide-up-down-all .slide-up-down-link:hover { text-decoration:none; }
	.slide-up-down-all .slide-up-down-link .label {
		display: inline-block;
		font-size:18px;
		margin-top:2px;
		vertical-align:top;
		*display:inline;
		*zoom:1;
	}
</style>
<script type='text/javascript' src='/admin/js/ui/jquery-ui-1.8.9.custom.js'></script>
<script type='text/javascript' src='/admin/js/ui/jquery.ui.core.js'></script>
<script type='text/javascript' src='/admin/js/ui/jquery.ui.widget.js'></script>
<script type='text/javascript' src='/admin/js/ui/jquery.ui.mouse.js'></script-->
<script type='text/javascript' src='/admin/js/jquery.easing-1.3.js'></script>
<script type='text/javascript' src='/admin/js/jquery.quicksand.js'></script>";

$Contents = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation("댓글게시판 관리", "댓글게시판 관리 > 댓글게시판 등록")."</td>
	</tr>
	<tr>
    	<td>
			<form name='main_frm' method='POST' onSubmit=\"return CheckFormValue(this)\" action='../display/comment.act.php' style='display:inline;' target='calcufrm'><!--SubmitX-->
			<input type='hidden' name=act value='".($act == "" ? "insert":"update")."'>
			<input type='hidden' name=cmt_ix value='".$cmt_ix."'>

			<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05'  valign=top style='padding:0px'>
						<table border='0' cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
						<col width='15%'>
						<col width='35%'>
						<col width='15%'>
						<col width='35%'>
							<tr>
								<td class='search_box_title' style='text-align:center;' colspan=4><b>댓글게시판 정보 </b></td>
							</tr>
							<tr>
								<td class='search_box_title'><b>게시판 타이틀</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 width=100%>
									<col width=50px><col width=*>
										<tr height=28><td>국문</td><td><input class='textbox' type='text' name='title' id='title' value='".$title."' style='width:500px'></td></tr>
										<tr height=28><td>영문</td><td><input class='textbox' type='text' name='title_en' id='title_en' value='".$title_en."' style='width:500px'></td></tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>게시판 사용여부</b></td>
								<td class='search_box_item'>
									<input type='radio' class='textbox' name='comment_use' id='comment_use_y' size=50 value='Y' checked style='border:0px;' ".($comment_use == "Y" ? "checked":"")."><label for='comment_use_y'> 사용</label>
									<input type='radio' class='textbox' name='comment_use' id='comment_use_n' size=50 value='N' style='border:0px;' ".($comment_use == "N" ? "checked":"")."><label for='comment_use_n'> 미사용</label>
								</td>
								<td class='search_box_title'><b>게시판 종료여부</b></td>
								<td class='search_box_item'>
									<input type='radio' class='textbox' name='comment_state' id='comment_state_y' size=50 value='Y' checked style='border:0px;' ".($comment_state == "Y" ? "checked":"")."><label for='comment_state_y'> 사용</label>
									<input type='radio' class='textbox' name='comment_state' id='comment_state_n' size=50 value='N' style='border:0px;' ".($comment_state == "N" ? "checked":"")."><label for='comment_state_n'> 미사용</label>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>게시판 이용안내</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 width=100%>
									<col width=*>
										<tr height=28><td><textarea name='information' id='information' style='width:85%;height:15px' placeholder='개인정보 / 혐오•차별•비난 / 광고•홍보•상업적 / 사기•기만•현혹 / 부적절한 콘텐츠 / 불법적인 콘텐츠 / 기타 알맞지 않은 콘텐츠는 관리자에 의해 비공개 또는 삭제될 수 있습니다.'>".$information."</textarea></td></tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>댓글 목록수</b></td>
								<td class='search_box_item'>
									<input class='textbox' type='text' name='comment_notify' value='".$comment_notify."' style='width:100px'> ※ 0 입력 시 한 페이지에 모든 댓글 표출
								</td>
								<td class='search_box_title'><b>댓글 글자제한</b></td>
								<td class='search_box_item'>
									<input class='textbox' type='text' name='comment_limit' value='".$comment_limit."' style='width:100px'> ※ 댓글 작성 시 한번에 등록 최대 글자수는 500자 입니다.
								</td>
							</tr>
							
							<tr>
								<td class='search_box_title'> <b>사용기간</b></td>
								<td class='search_box_item' colspan=3>
									".search_date('comment_start','comment_end',$comment_start,$comment_end,'Y',' ' ,' validation=true title="사용기간" ')."
								</td>
							</tr>
							<tr>
								<td class='search_box_title' style='text-align:center;' colspan=4><b>댓글게시판 권한설정 </b></td>
							</tr>
							<tr>
								<td class='input_box_title'> <b>비밀글 사용여부</b></td>
								<td class='input_box_item' colspan=3>
									<input type='radio' class='textbox' name='comment_secret_use' id='comment_secret_use_y' size=50 value='Y' checked style='border:0px;' ".($comment_secret_use == "Y" ? "checked":"")."><label for='comment_secret_use_y'> 사용</label>
									<input type='radio' class='textbox' name='comment_secret_use' id='comment_secret_use_n' size=50 value='N' style='border:0px;' ".($comment_secret_use == "N" ? "checked":"")."><label for='comment_secret_use_n'> 미사용</label>
									※ 비밀글을 사용하시면 자기가 쓴 글만 읽을 수 있습니다.
								</td>
							</tr>
							<tr>
								<td class='input_box_title'> <b>답변 사용여부</b></td>
								<td class='input_box_item' colspan=3>
									<input type='radio' class='textbox' name='comment_answer_use' id='comment_answer_use_y' size=50 value='Y' checked style='border:0px;' ".($comment_answer_use == "Y" ? "checked":"")."><label for='comment_answer_use_y'> 사용</label>
									<input type='radio' class='textbox' name='comment_answer_use' id='comment_answer_use_n' size=50 value='N' style='border:0px;' ".($comment_answer_use == "N" ? "checked":"")."><label for='comment_answer_use_n'> 미사용</label>
									※ 회원이 댓글에 대한 답변 사용 시 계층형 댓글로 사용 가능
								</td>
							</tr>      
							<tr>
								<td class='input_box_title'> <b>이미지첨부 사용여부</b></td>
								<td class='input_box_item' colspan=3>
									<input type='radio' class='textbox' name='comment_img_use' id='comment_img_use_y' size=50 value='Y' checked style='border:0px;' ".($comment_img_use == "Y" ? "checked":"")."><label for='comment_img_use_y'> 사용</label>
									<input type='radio' class='textbox' name='comment_img_use' id='comment_img_use_n' size=50 value='N' style='border:0px;' ".($comment_img_use == "N" ? "checked":"")."><label for='comment_img_use_n'> 미사용</label>
									※ 최대 4장 첨부 가능 
								</td>
							</tr>      
							<tr>
								<td class='input_box_title'> <b>댓글 보기권한</b></td>
								<td class='input_box_item' colspan=3>
									<input type='radio' class='textbox' name='comment_view_permission' id='comment_view_permission_a' size=50 value='A' checked style='border:0px;' ".($comment_view_permission == "A" ? "checked":"")."><label for='comment_view_permission_a'> 전체</label>
									<input type='radio' class='textbox' name='comment_view_permission' id='comment_view_permission_m' size=50 value='M' style='border:0px;' ".($comment_view_permission == "M" ? "checked":"")."><label for='comment_view_permission_m'> 회원</label>
									<input type='radio' class='textbox' name='comment_view_permission' id='comment_view_permission_o' size=50 value='O' style='border:0px;' ".($comment_view_permission == "O" ? "checked":"")."><label for='comment_view_permission_o'> 자신이 작성한 댓글만</label>
									※ 전체 선택 시 비회원도 작성된 댓글 볼 수 있음
								</td>
							</tr>      
							<tr>
								<td class='input_box_title'> <b>추가기능 사용여부</b></td>
								<td class='input_box_item' colspan=3>
									<input type='radio' class='textbox' name='comment_sug_use' id='comment_sug_use_y' size=50 value='Y' checked style='border:0px;' ".($comment_sug_use == "Y" ? "checked":"")."><label for='comment_sug_use_y'> 사용</label>
									<input type='radio' class='textbox' name='comment_sug_use' id='comment_sug_use_n' size=50 value='N' style='border:0px;' ".($comment_sug_use == "N" ? "checked":"")."><label for='comment_sug_use_n'> 미사용</label>
								</td>
							</tr>      
							<tr>
								<td colspan=4 align=right style='padding:10px;'>
									<table>
										<tr>
											<td><input type='checkbox' name='delete_cache' id='delete_cache' value='Y'><label for='delete_cache'>캐쉬삭제하기</label></td>
											<td><input type=image src='../images/".$admininfo["language"]."/b_save.gif' align=absmiddle border=0></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
        


<iframe name='calcufrm' id='calcufrm' src='' width=0 height=0></iframe>
<Script Language='JavaScript'>

$(function() {
	$(\"#start_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
			$('#end_datepicker').val(dateText);
		//}else{
			//$('#end_datepicker').datepicker('setDate','+0d');
		}
	}

	});

	$(\"#end_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	//$('#end_timepicker').timepicker();
});



function select_date(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	$(\"#start_datepicker\").val(FromDate);
	$(\"#end_datepicker\").val(ToDate);
}

</Script>";

$Script = "\n
$Script";

if($agent_type == "M"  || $agent_type == "mobile"){
	if($mmode == "pop"){
		$P = new ManagePopLayOut();
		$P->addScript = $Script;
		$P->OnloadFunction = "";
		$P->strLeftMenu = mshop_menu();
		$P->Navigation = $navigation;
		$P->title = $title;
		$P->NaviTitle = $title;
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}else{
		$P = new LayOut();
		$P->addScript = $Script;
		$P->Navigation = $navigation;
		$P->title = $title;
		$P->strLeftMenu = mshop_menu();
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}
}else{
	if($mmode == "pop"){

		$P = new ManagePopLayOut();
		$P->addScript = $Script;
		$P->OnloadFunction = "";
		$P->strLeftMenu = display_menu();
		$P->Navigation = "컨텐츠전시 > 댓글게시판 관리 > 댓글게시판 등록";
		$P->title = "댓글게시판 등록";
		$P->NaviTitle = "댓글게시판 등록";
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}else{
		$P = new LayOut();
		$P->addScript = $Script;
		$P->Navigation = "컨텐츠전시 > 댓글게시판 관리 > 댓글게시판 등록";
		$P->title = "댓글게시판 등록";
		$P->OnloadFunction = "";//showSubMenuLayer('storeleft');
		$P->strLeftMenu = display_menu();
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}
}


?>
