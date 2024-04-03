<?
include("../class/layout.class");

//print_r($admininfo);
$db = new Database;


$db->query("SELECT * FROM ".TBL_SHOP_EVENT." where event_ix= '$event_ix'");

//$up_dir = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/upfile/";
//echo $up_dir;

if($db->total){
	$db->fetch();
	$event_ix = $db->dt[event_ix];
	$mall_ix = $db->dt[mall_ix];
	$er_ix = $db->dt[er_ix];
	$kind = $db->dt[kind];
	$event_title = $db->dt[event_title];
	$event_text = $db->dt[event_text];
	$event_width = $db->dt[event_width];
	$event_height = $db->dt[event_height];
	$event_top = $db->dt[event_top];
	$event_left = $db->dt[event_left];
	$event_use_sdate = $db->dt[event_use_sdate];
	$event_use_edate = $db->dt[event_use_edate];
	$cid = $db->dt[cid];
	$company_id = $db->dt[company_id];

	$full = $db->dt[full];
	$disp = $db->dt[disp];
	$act = "update";
 
	$sDate = date("Y/m/d", mktime(0, 0, 0, substr($db->dt[event_use_sdate],4,2)  , substr($db->dt[event_use_sdate],6,2), substr($db->dt[event_use_sdate],0,4)));
	$eDate = date("Y/m/d",mktime(0, 0, 0, substr($db->dt[event_use_edate],4,2)  , substr($db->dt[event_use_edate],6,2), substr($db->dt[event_use_edate],0,4)));

	$startDate = $event_use_sdate;
	$endDate = $event_use_edate;

	$db->query("SELECT * FROM shop_event_config where event_ix= '$event_ix'");
	$db->fetch();
	$event_type = $db->dt[event_type];
	if($event_type == "1"){
		$event_type_text = "댓글 이벤트 ";
	}else if($event_type == "2"){
		$event_type_text = "체험단 이벤트";
	}else if($event_type == "3"){
		$event_type_text = "당첨(즈석) 이벤트";
	}else if($event_type == "4"){
		$event_type_text = "응모이벤트";
	}else if($event_type == "5"){
		$event_type_text = "포인트 상품 구매이벤트";
	}else if($event_type == "6"){
		$event_type_text = "숨은그림 찾기 이벤트";
	} 


	$lottery_type = $db->dt[lottery_type];
	$use_comment = $db->dt[use_comment];
	$probability = $db->dt[probability];
	$lottery_probability = $db->dt[lottery_probability];
	
	$lottery_amount = $db->dt[lottery_amount];
	$lottery_method = $db->dt[lottery_method];
	$lottery_date = $db->dt[lottery_date];

	$participation_able_times = $db->dt[participation_able_times];
	$participation_method = $db->dt[participation_method];
	$participation_use_point = $db->dt[participation_use_point];
	$participation_saving_point = $db->dt[participation_saving_point];
	$exposure_rate = $db->dt[exposure_rate];

	if($lottery_type == "1"){
		$lottery_type_text = "전체";
	}else if($lottery_type == "2"){
		$lottery_type_text = "추첨(랜덤) - 당첨확률 : ".$lottery_probability." % ";
	}else if($lottery_type == "3"){
		$lottery_type_text = "추첨(관리자 수동선택) ";
	}else if($lottery_type == "5"){
		$lottery_type_text = "선착순 : ".$lottery_amount." 명 ";
	}
	
	if($lottery_method == 1){
		$lottery_method_text = "즉시당첨";
	}else{
		$lottery_method_text = "추첨일 당첨 : ".$lottery_date." ";
	}
	
	if($participation_able_times == "1"){
		$participation_able_times_text = "1일/1회(ID)";
	}else  if($participation_able_times == "2"){
		$participation_able_times_text = "이벤트 기간내 1회";
	}

	if($participation_method == 1){
		$participation_method_text = "참여신청";
	}else{
		$participation_method_text = "회원상품선택";
	} 
}

$Script ="
<script language='javascript'>


function change_winner(event_ix, ea_ix, is_winner){
	if(is_winner == '1'){
		var confirm_text = '해당회원을 당첨처리 하시겠습니까?';
	}else{
		var confirm_text = '해당회원을 당첨 취소 처리 하시겠습니까?';
	}
	if(confirm(confirm_text)){
		window.frames['act'].location.href='./event.act.php?act=change_winner&event_ix='+event_ix+'&ea_ix='+ea_ix+'&is_winner='+is_winner;
	}
}



function clearAll(frm){
		for(i=0;i < frm.ec_ix.length;i++){
				frm.ec_ix[i].checked = false;
		}
}

function checkAll(frm){
       	for(i=0;i < frm.ec_ix.length;i++){
			if(frm.ec_ix[i].disabled!=true){
				frm.ec_ix[i].checked = true;
			}
		}
}

function fixAll(frm){
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;

	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
}

function select_comment_disp(disp){
	var frm = document.listform;
	var check_bool = false;
	var cnt=0;

	for(i=0;i < frm.ec_ix.length;i++){
			if(frm.ec_ix[i].checked){
				cnt++;
				check_bool = true;
			}
	}

	if(check_bool){
	   frm.change_disp.value=disp;
		frm.act.value='select_comment_disp';
		frm.submit();
	}else{
		alert('체크박스를 하나 이상 선택 하셔야 합니다.');
		return false;
	}
}
 
</script>
";

$Contents = "
<script language='javascript' src='"."tglib_orders.php?page=$page&ctgr=$ctgr&qstr=$qstr"."'></script>
<table width='100%' cellpadding=0 cellspacing=0>
<tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("이벤트신청자리스트", "프로모션/전시 > 이벤트신청자리스트")."</td>
</tr>
<tr>
	<td align='left' colspan=4 style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:3px;'><img src='../image/title_head.gif' align=absmiddle><b> 이벤트정보</b><span class=small> &nbsp;&nbsp;&nbsp;</span></div>")."</td>
</tr>
<tr>
	<td align='left' colspan=6 >
	<table border='0' cellpadding=3 cellspacing=0 width='100%' class='input_table_box'>
		<col width='15%'>
		<col width='35%'>
		<col width='15%'>
		<col width='35%'>
		<tr>
			<td class='search_box_title' > 이벤트 제목</td>
			<td class='search_box_item' > ".$event_title." </td>
			<td class='search_box_title' > 이벤트 기간</td>
			<td class='search_box_item' > ".date("Y-m-d",$event_use_sdate)."~".date("Y-m-d",$event_use_edate)." </td>
		</tr>
	</table>
	</td>
</tr>
<tr>
	<td align='left' colspan=4 style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:3px;'><img src='../image/title_head.gif' align=absmiddle><b> 댓글목록</b><span class=small> &nbsp;&nbsp;&nbsp;</span></div>")."</td>
</tr>
</table>";


$sql = "SELECT * FROM shop_event_sub where idx='".$sub_idx."' ";
//$db->query($sql);
//$db->fetch();
$recruit_cnt=$db->dt[recruit_cnt];

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
    $Contents .="
	<a href='?mode=excel&".$QUERY_STRING."'><img src='../image/btn_excel_save.gif' border=0></a>";
}else{
    $Contents .="    
    <a href=\"".$auth_excel_msg."\"><img src='../image/btn_excel_save.gif' border=0></a>";
}
$Contents .= "   
<form name=listform method=post action='event.act.php' onsubmit='return AccountsSelectCheck(this)' target='act'>
<input type='hidden' name='act' value=''>
<input type='hidden' name='event_ix' value='".$event_ix."'>
<input type='hidden' name='change_disp' value=''>
<div style='width:100%;height:400px;'><!--overflow-y:scroll;overflow-x:scroll;-->
	<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
		<tr height='30' align='center'>
			
			<td width='5%' class='s_td'><input type=checkbox  name='all_fix' onclick='fixAll(document.listform)'></td>
			<td width='10%' align='center' class='m_td'><font color='#000000'><b>회원그룹</b></font></td>
			<td width='8%' align='center' class='m_td'><font color='#000000'><b>댓글분류</b></font></td>
			<td width='10%' align='center' class='m_td'><font color='#000000'><b>회원 아이디</b></font></td>
			<td width='10%' align='center' class='m_td'><font color='#000000'><b>".OrderByLink("신청자 이름", "name", $ordertype)."</b></font></td>
			<td width='10%' align='center' class='m_td'><font color='#000000'><b>이메일</b></font></td>
			<td width='10%' align='center' class='m_td'><font color='#000000'><b>전화번호</b></font></td>
			<td width='*' align='center' class='m_td'><font color='#000000'><b>내용</b></font></td>
			<td width='8%' align='center' class='m_td'><font color='#000000'><b>".OrderByLink("추천인", "recommend", $ordertype)." </b></font></td>
			<td width='8%' align='center' class='m_td'><font color='#000000'><b>노출여부</b></font></td>
			<td width='10%' align='center' class='m_td'><font color='#000000'><b>관리</b></font></td>
			
			
		</tr>";


if($orderby == "recommend"){
	$orderby_str = " order by recommend ".$ordertype."  ";
}else if($orderby == "name"){
	$orderby_str = " order by name ".$ordertype."  ";
}else{
	$orderby_str = " order by regdate desc  ";
}

//$sql = "SELECT (select mem_ix from shop_event_winner ew where ea.event_code=(ew.event_code-1) and ea.mem_ix=ew.mem_ix and rownum=1) as checked,ea.* FROM shop_event_applicant ea where ea.event_code='".$event_code."' and ea.sub_idx='".$sub_idx."' ";
$sql = "SELECT cu.id as mem_id, mg.gp_name, 
			(select div_name from shop_event_comment as ec inner join shop_display_comment_div as dcd on (ec.div_ix = dcd.div_ix) where ec.mem_ix = cu.code and ea.event_ix = ec.event_ix) as div_name,
			AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as mem_name, 
			AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail, 
			AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') as phone,
			AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as mobile, 
			AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') as addr1,
			AES_DECRYPT(UNHEX(cmd.addr2),'".$db->ase_encrypt_key."') as addr2, 
			AES_DECRYPT(UNHEX(cmd.zip),'".$db->ase_encrypt_key."') as zip,
			ec.* 
			FROM shop_event_comment ec 
			right join common_user cu on ec.mem_ix = cu.code 
			right join common_member_detail cmd  on ec.mem_ix = cmd.code
			left join ".TBL_SHOP_GROUPINFO." mg on cmd.gp_ix = mg.gp_ix
			left join shop_event_applicant ea on ec.event_ix = ea.event_ix  and ec.mem_ix = ea.mem_ix
			where   ec.event_ix = '".$event_ix."' 
			".$orderby_str." ";//limit $start,$max
//echo nl2br($sql);

			$db->query($sql);			

//	$event_comments = $db->fetchall();

//$db->query($sql);

if($mode=="excel"){

	include '../include/phpexcel/Classes/PHPExcel.php';

	$winner_XL = new PHPExcel();
		
		// 속성 정의
		$winner_XL->getProperties()->setCreator("포비즈 코리아")
									 ->setLastModifiedBy("Mallstory.com")
									 ->setTitle("winners")
									 ->setSubject("winners")
									 ->setDescription("generated by forbiz korea")
									 ->setKeywords("mallstory")
									 ->setCategory("winners");
		
		

		//// 타이틀을 정한다.
		$column_array = array("고객이름","우편번호","고객주소","고객아이디"," E-mail주소","전화번호","핸드폰번호","댓글내용","노출여부");

		$col='A';
		foreach($column_array as $column){
			$winner_XL->getActiveSheet(0)->setCellValue($col . 1,  $column);
			$col++;
		}

		$HeaderRow = 2;
		for ($i = 0; $i < $db->total; $i++){
			$db->fetch($i);

			$col='A';
			$winner_XL->getActiveSheet(0)->setCellValue($col . ($i+$HeaderRow), $db->dt[mem_name]);
			$col++;
			$winner_XL->getActiveSheet(0)->setCellValue($col . ($i+$HeaderRow), $db->dt[zip]);
			$col++;
			$winner_XL->getActiveSheet(0)->setCellValue($col . ($i+$HeaderRow), $db->dt[addr1]." ".$db->dt[addr2]);
			$col++;
			$winner_XL->getActiveSheet(0)->setCellValue($col . ($i+$HeaderRow), $db->dt[mem_id]);
			$col++;
			$winner_XL->getActiveSheet(0)->setCellValue($col . ($i+$HeaderRow), $db->dt[mail]);
			$col++;
			$winner_XL->getActiveSheet(0)->setCellValue($col . ($i+$HeaderRow), $db->dt[phone]);
			$col++;
			$winner_XL->getActiveSheet(0)->setCellValue($col . ($i+$HeaderRow), $db->dt[mobile]);
			$col++;
			$winner_XL->getActiveSheet(0)->setCellValue($col . ($i+$HeaderRow), $db->dt[comment]);
			$col++;
            $winner_XL->getActiveSheet(0)->setCellValue($col . ($i+$HeaderRow), ($db->dt[disp]==1 ? "예" : "아니오"));
			//$winner_XL->getActiveSheet(0)->setCellValue($col . ($i+$HeaderRow), $db->dt[div_name]);
			//$col++;
		}


	// 첫번째 시트 선택
	$winner_XL->setActiveSheetIndex(0);
	$winner_XL->getActiveSheet()->setTitle('당첨자리스트');

	$col='A';
	foreach($column_array as $column){
		$winner_XL->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
		$col++;
	}
	
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="ecent_comment_list.xls"');
	header('Cache-Control: max-age=0');


	$objWriter = PHPExcel_IOFactory::createWriter($winner_XL, 'Excel5');
	$objWriter->save('php://output');

	exit;
}


if($db->total){
	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

	$Contents .= "
	  <tr height='28' align='center'>
		<td class='list_box_td '><input type=checkbox name='ec_ix[]' id='ec_ix' value='".$db->dt[ec_ix]."' ".($db->dt[checked]!="" ?"disabled":"")."></td>
		<td class='list_box_td ' >".$db->dt[gp_name]."</td>
		<td class='list_box_td ' >".$db->dt[div_name]."</td>
		<td class='list_box_td list_bg_gray'>".$db->dt[mem_id]."</td>
		<td class='list_box_td '>".$db->dt[mem_name]."</td>
		<td class='list_box_td list_bg_gray' >".$db->dt[mail]."</td>
		<td class='list_box_td list_bg_gray' >".$db->dt[mobile]."</td>
		<td class='list_box_td lft' style='padding:5px;'>".nl2br($db->dt[comment])."</td>
		<td class='list_box_td list_bg_gray' >".$db->dt[recommend]."</td>
		<td class='list_box_td' >".($db->dt[disp] == 1 ? "예" : "아니오")."</td>
		<td class='list_box_td '>".($db->dt[is_winner] == "1" ? "<a href=\"javascript:change_winner('".$db->dt[event_ix]."', '".$db->dt[ea_ix]."', '".($db->dt[is_winner] == "1" ? "0":"1")."');\">당첨취소</a>":"<a href=\"javascript:change_winner('".$db->dt[event_ix]."', '".$db->dt[ea_ix]."', '".($db->dt[is_winner] == "1" ? "0":"1")."');\">수동당첨</a>")."</td>
		</tr>";
	}
}else{
	$Contents .= "<tr height=50><td colspan=11 align=center>조회된 결과가 없습니다.</td></tr>";
}
$Contents .= "
</table>";


$Contents .= "
</div>
<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
  <tr>
	<td>
	  <input type='button' onclick=\"select_comment_disp(1)\" value='일괄 노출 처리'>
	  <input type='button' onclick=\"select_comment_disp(0)\" value='일괄 비노출 처리'>
	</td>
  </tr>
</table>
</form>";


if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;

	if ($gid != ""){
		if ($admininfo[admin_level] == 9){
			$P->OnloadFunction = "";
		}else{
			$P->OnloadFunction = "";
		}
	}else{
		$P->OnloadFunction = "";
	}

	$P->strLeftMenu = product_menu();
	$P->strContents = $Contents;
	$P->Navigation = "프로모션/전시 > 이벤트신청자리스트";
	$P->NaviTitle = "이벤트 신청자리스트";
	echo $P->PrintLayOut();
}else{

	$P = new LayOut();
	$P->strLeftMenu = display_menu();
	$P->OnloadFunction = "";
	$P->addScript = "$Script";
	$P->Navigation = "프로모션/전시 > 이벤트신청자리스트";
	$P->title = "이벤트 신청자리스트";
	$P->strContents = $Contents;


	echo $P->PrintLayOut();
}

/*
<tr height='28' align='center'>
		<td class='list_box_td '><input type=checkbox name='idx[]' id='idx' value='".$db->dt[ec_ix]."' ".($db->dt[checked]!="" ?"disabled":"")."></td>
		<td class='list_box_td list_bg_gray'>".$db->dt[mem_ix]."</td>
		<td class='list_box_td '>".$db->dt[mem_id]."</td>
		<td class='list_box_td list_bg_gray' >".$db->dt[mem_name]."</td>
		<td class='list_box_td ' >".$db->dt[title]."</td>
		<td class='list_box_td list_bg_gray' >".$db->dt[description]."</td>
		<td class='list_box_td ' >".$db->dt[regdate]."</td>
		</tr>

CREATE TABLE IF NOT EXISTS shop_event_applicant (
  `ea_ix` int(6) NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `event_ix` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '이벤트코드',
  `mem_id` varchar(15) DEFAULT NULL COMMENT '회원 아이디',
  `mem_name` varchar(50) DEFAULT NULL COMMENT '회원 이름',
  `phone` varchar(50) DEFAULT NULL COMMENT '전화번호',
  `mobile` varchar(50) DEFAULT NULL COMMENT '핸드폰',
  `email` varchar(50) DEFAULT NULL COMMENT '이메일',
  `mem_ix` varchar(32) NOT NULL COMMENT '신청자키값',
  `is_winner` char(1) DEFAULT NULL COMMENT '사용여부',
  `disp` char(1) DEFAULT NULL COMMENT '사용여부',
  `ranking` varchar(32) NOT NULL COMMENT '신청자키값',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`db_ix`),
  KEY `mem_ix` (`mem_ix`),
  KEY `regdate_ix` (`regdate`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='이벤트 신청자 정보' 

`title` varchar(255) DEFAULT NULL COMMENT '제목',
  `description` mediumtext DEFAULT NULL COMMENT '내용',

*/

?>
