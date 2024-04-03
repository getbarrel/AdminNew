<?
include("../class/layout.class");
include("../display/main_display.lib.php");
//print_r($_SESSION["admin_config"][front_multiview] );

$db = new Database;
$mdb = new Database;

if(!$agent_type){
	$agent_type = "W";
}

$search_text 				= $_GET['search_text'];
$comment_secret_use 		= $_GET['comment_secret_use'];
$comment_answer_use 		= $_GET['comment_answer_use'];
$comment_img_use 			= $_GET['comment_img_use'];
$comment_sug_use 			= $_GET['comment_sug_use'];
$comment_view_permission 	= $_GET['comment_view_permission'];
$comment_state 				= $_GET['comment_state'];
$search_start_date 			= $_GET['search_start_date'];
$search_end_date 			= $_GET['search_end_date'];
$start_sdate 				= $_GET['start_sdate'];
$end_sdate 					= $_GET['end_sdate'];

//echo $agent_type;
$Script = "<script language='javascript'>


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

function commentDelete(cmtIx){
	var allData = { 'act': 'delete', 'cmt_ix': cmtIx};
    
    if(confirm('삭제 하시겠습니까?')) {
            $.ajax({
                url:'./comment.act.php',
                type:'POST',
                data: allData,
                success:function(data){
                    alert('삭제 되었습니다.');
                    location.reload();
                },error:function(jqXHR, textStatus, errorThrown){
                    alert('에러 발생~~' + textStatus + ' : ' + errorThrown);
                }
            });
        }
}


</script>";


$mstring ="<form name=serchform >
		<table cellpadding=5 cellspacing=0 width=100% border=0 align=center style=''>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("컨텐츠관리", "댓글게시판 관리 > 댓글게시판 관리")."</td>
		</tr>";
 

$mstring .= "
		<tr>
			<td>
				<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='search_table_box'>
					<col width=20%>
					<col width=30%>
					<col width=20%>
					<col width=30%>
					<tr>
						<td class='search_box_title' > 게시판타이틀</td>
						<td class='search_box_item' colspan=3>
							<input type='text' class=textbox style='width: 800px; ' name=search_text value='$search_text'> 
						</td>
					</tr>
					<tr>
						<td class='search_box_title' > 조건검색</td>
						<td class='search_box_item' colspan=3>
							<table cellpadding=0 cellspacing=0 border='0'  align='left'>
							<col width=150>
							<col width=150>
							<col width=150>
								<tr>
									<td>
										<input type='checkbox' name='comment_secret_use' id='comment_secret_use' value='Y' title='비밀글' ".ReturnStringAfterCompare($comment_secret_use, "Y", " checked")."/>
										<label for='comment_secret_use'> 비밀글 </label> 
									</td>
									<td>
										<input type='checkbox' name='comment_answer_use' id='comment_answer_use' value='Y' title='답변' ".ReturnStringAfterCompare($comment_answer_use, "Y", " checked")."/>
										<label for='comment_answer_use'> 답변 </label> 
									</td>
									<td>
										<input type='checkbox' name='comment_img_use' id='comment_img_use' value='Y' title='이미지첨부' ".ReturnStringAfterCompare($comment_img_use, "Y", " checked")."/>
										<label for='comment_img_use'> 이미지첨부 </label> 
									</td>
									<td>
										<input type='checkbox' name='comment_sug_use' id='comment_sug_use' value='Y' title='추천기능' ".ReturnStringAfterCompare($comment_sug_use, "Y", " checked")."/>
										<label for='comment_sug_use'> 추천기능</label>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td class='search_box_title' > 보기권한</td>
						<td class='search_box_item' colspan=3>
							<input type='radio' name='comment_view_permission'  id='comment_view_permission_' value='' ".ReturnStringAfterCompare($comment_view_permission, "", " checked")."><label for='comment_view_permission_'>전체</label>
							<input type='radio' name='comment_view_permission'  id='comment_view_permission_M' value='M' ".ReturnStringAfterCompare($comment_view_permission, "M", " checked")."><label for='comment_view_permission_M'>회원</label>
							<input type='radio' name='comment_view_permission'  id='comment_view_permission_O' value='O' ".ReturnStringAfterCompare($comment_view_permission, "O", " checked")."><label for='comment_view_permission_O'>자신이 작성한 댓글로</label>
						</td>
					</tr>
					<tr>
						<td class='search_box_title' > 게시판상태</td>
						<td class='search_box_item' colspan=3>
							<input type='radio' name='comment_state'  id='comment_state_' value='' ".ReturnStringAfterCompare($comment_state, "", " checked")."><label for='comment_state_'>전체</label>
							<input type='radio' name='comment_state'  id='comment_state_Y' value='Y' ".ReturnStringAfterCompare($comment_state, "Y", " checked")."><label for='comment_state_Y'>사용중</label>
							<input type='radio' name='comment_state'  id='comment_state_N' value='N' ".ReturnStringAfterCompare($comment_state, "N", " checked")."><label for='comment_state_N'>종료</label>
						</td>
					</tr>
					<tr>
						<td class='search_box_title' nowrap>
							<label for='search_start_date'><b>시작일자</b></label><input type='checkbox' name='search_start_date' id='search_start_date' value='1' onclick='ChangeRegistDate(document.search_banner);' ".(($search_start_date==1)?"checked":"").">
						  </td>
						  <td class='search_box_item'  colspan=3>
						  ".search_date('start_sdate','start_edate',$start_sdate,$start_edate,'N','D')."
						  </td>
					</tr>
					<tr>
						<td class='search_box_title' nowrap>
							<label for='search_end_date'><b>종료일자</b></label><input type='checkbox' name='search_end_date' id='search_end_date' value='1' onclick='ChangeRegistDate(document.search_banner);' ".(($search_end_date==1)?"checked":"").">
						  </td>
						  <td class='search_box_item'  colspan=3>
						  ".search_date('end_sdate','end_edate',$end_sdate,$end_edate,'N','D')."
						  </td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align ='center'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle></td>
		</tr>
		
		<tr>
			<td>
			".PrintPromotionGoods()."
			</td>
		</tr>
		</form>";
$mstring .="</table>";

$Contents = $mstring.$help_text;
$Contents .= "<div style='height:120px;'></div>";

if($agent_type == "M" || $agent_type == "mobile"){
	$P = new LayOut();
	$P->addScript = $Script;
	$P->Navigation = $navigation;
	$P->title = $title;
	$P->strLeftMenu = mshop_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->Navigation = "컨텐츠전시 > 댓글게시판 관리 > 댓글게시판 목록";
	$P->title = "댓글게시판 목록";
	$P->strLeftMenu = display_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}
function PrintPromotionGoods(){
	global $db, $mdb, $admin_config, $div_ix ,$admininfo;
	global $auth_write_msg, $auth_delete_msg, $auth_update_msg,$product_image_column_str;
	global $page, $max, $nset;
	global $agent_type ;
	global $search_text, $comment_secret_use, $comment_answer_use, $comment_img_use, $comment_sug_use, $comment_view_permission, $comment_state, $search_start_date, $search_end_date, $start_sdate, $end_sdate;

	$where = " WHERE 1=1 ";

	if($search_text != ""){
		$where .= " AND title LIKE  '%$search_text%' ";
	}

	if($comment_secret_use == "Y" || $comment_answer_use == "Y" || $comment_img_use == "Y" || $comment_sug_use == "Y"){
		$where .= " AND ( ";

		$conditionCnt = 0;

		if($comment_secret_use == "Y"){
			$where .= " comment_secret_use =  '$comment_secret_use' ";

			$conditionCnt++;
		}

		if($comment_answer_use == "Y"){
			if($conditionCnt > 0){
				$where .= " OR ";
			}
			$where .= " comment_answer_use =  '$comment_answer_use' ";

			$conditionCnt++;
		}

		if($comment_img_use == "Y"){
			if($conditionCnt > 0){
				$where .= " OR ";
			}
			$where .= " comment_img_use =  '$comment_img_use' ";

			$conditionCnt++;
		}

		if($comment_sug_use == "Y"){
			if($conditionCnt > 0){
				$where .= " OR ";
			}
			$where .= " comment_sug_use =  '$comment_sug_use' ";

			$conditionCnt++;
		}

		$where .= " ) ";
	}

	if($comment_view_permission != ""){
		$where .= " AND comment_view_permission =  '$comment_view_permission' ";
	}

	if($comment_state != ""){
		$where .= " AND comment_state =  '$comment_state' ";
	}

	if($search_start_date == "1"){
		$start_sdate_where = mktime(0,0,0,substr($start_sdate,5,2),substr($start_sdate,8,2),substr($start_sdate,0,4));
		$start_edate_where = mktime(0,0,0,substr($start_edate,5,2),substr($start_edate,8,2),substr($start_edate,0,4));
		$where .= " AND  comment_start BETWEEN  $start_sdate_where AND $start_edate_where ";
	}

	if($search_end_date == "1"){
		$end_sdate_where = mktime(0,0,0,substr($end_sdate,5,2),substr($end_sdate,8,2),substr($end_sdate,0,4));
		$end_edate_where = mktime(0,0,0,substr($end_edate,5,2),substr($end_edate,8,2),substr($end_edate,0,4));
		$where .= " AND  comment_end BETWEEN  $end_sdate_where AND $end_edate_where ";
	}

	$sql = "SELECT count(*) AS total 
			FROM shop_comment  
			$where 
	";

	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[total];

	$max = 20;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}


	$mString = "<table cellpadding=4 cellspacing=0 border=0 width=100% class='list_table_box' >
					<col width='3%'>
					<col width='*%'>
					<col width='5%'>
					<col width='15%'>
					<col width='5%'>						
					<col width='5%'>
					<col width='5%'>
					<col width='5%'>
					<col width='7%'>
					<col width='12%'>
					<col width='10%'>
						<tr height=30 align=center>
							<td align='center' class='m_td'><font color='#000000'><b>번호</b></font></td>
							<td class=s_td >게시판타이틀</td>
							<td class=s_td >댓글수</td>
							<td class=s_td >사용기간</td>
							<td class=m_td >비밀</td>
							<td class=m_td >답변</td>
							<td class=m_td >첨부</td>
							<td class=m_td >추천</td>
							<td class=m_td >권한</td>
							<td class=m_td >등록일</td>
							<td class=e_td >관리</td>
						</tr>";
	if ($total == 0){
		$mString .= "<tr bgcolor=#ffffff><td height=70 colspan=13 align=center>내역이 존재 하지 않습니다.</td></tr>";
	}else{
		$sql = "SELECT * FROM shop_comment
					$where 
				ORDER BY regdate desc 
				LIMIT $start, $max

		";
		$db->query($sql);

		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);
			$no = $total - ($page - 1) * $max - $i;

			$mString .= "<tr height=27 style='font-weight:bold;'>
				<td class='list_box_td'>".$no."</td>
				<td class='list_box_td' >".$db->dt[title]."</td>
				<td class='list_box_td' >0</td>
				<td class='list_box_td' >".date("Y.m.d", $db->dt[comment_start])." ~ ".date("Y.m.d",$db->dt[comment_end])."</td>
				<td class='list_box_td' >".$db->dt[comment_secret_use]."</td>
				<td class='list_box_td' >".$db->dt[comment_answer_use]."</td>
				<td class='list_box_td' >".$db->dt[comment_img_use]."</td>
				<td class='list_box_td' >".$db->dt[comment_sug_use]."</td>
				<td class='list_box_td' >".($db->dt[comment_view_permission] == "A" ? "전체": $db->dt[comment_view_permission] == "M" ? "회원" : "자신이 작성한 댓글만")."</td>
				<td class='list_box_td' >".$db->dt[regdate]."</td>
				<td class='list_box_td list_bg_gray' nowrap>
					<a href='comment.write.php?cmt_ix=".$db->dt[cmt_ix]."&act=update'><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle alt='수정' title='수정'></a>
					<a href=\"JavaScript:commentDelete('".$db->dt[cmt_ix]."')\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='margin:0px 5px;' alt='삭제' title='삭제'></a>
				</td>
			</tr>	
			";
		}
	}
	$mString .= "</table>";
	$mString .= "<table cellpadding=0 cellspacing=0 border=0 width=100% >
				<tr height=50 bgcolor=#ffffff>
					<td colspan=3 align=left>".page_bar($total, $page, $max,  "&max=$max","")."</td>
					<td colspan=3 align=right>";
					
					
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
						$mString .= "<a href='comment.write.php'><img src='../images/btm_reg.gif' border=0 ></a>";
					}else{
						$mString .= "<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_promotionadd.gif' border=0 ></a>";
					}
					$mString .= "
					</td>
				</tr>";
	$mString .= "</table>";

	return $mString;
}


?>
