<?php
include("../class/layout.class");

$db = new Database;


$before10day = mktime(0, 0, 0, date("m")  , date("d")-10, date("Y"));

if ($use_sdate == "1"){
	if(!$event_start_sdate || !$event_start_edate){ 
		$event_start_sdate = date("Ymd", $before10day);
		$event_start_edate = date("Ymd");	
	}
}

$Script = "<script language='javascript'>
function imageView(url){
	window.open(url, 'Image view', 'height=500,width=400');
}
</script>";

if (empty( $_REQUEST['os'])) {
    $os_select_all = 'selected';
} elseif ($_REQUEST['os'] == 'a') {
    $os_select_android = 'selected';
} elseif ($_REQUEST['os'] == 'i') {
    $os_select_iphone = 'selected';
}


$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td>";
		$mstring .= "
		<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
			<tr>
				<th class='box_01'></th>
				<td class='box_02'></td>
				<th class='box_03'></th>
			</tr>
			<tr>
				<th class='box_04'></th>
				<td class='box_05'  valign=top style='padding:5px'>
				<form name=search_events method='get' ><!--SubmitX(this);'-->
				<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
					<col width='15%'>
					<col width='35%'>
					<col width='15%'>
					<col width='35%'>
					<tr height=30>
					  <td class='search_box_title'>운영체제</td>
					  <td class='search_box_item'  style='padding-left:5px;' colspan='3'>
                          <select name='os'>
                              <option value='' ".$os_select_all.">전체</option>
                              <option value='a' ".$os_select_android.">안드로이드</option>
                              <option value='i' ".$os_select_iphone.">아이폰</option>
                          </select> 
					  </td>
					</tr>
					<tr height=30>
					  <td class='search_box_title'>RegistrationID</td>
					  <td class='search_box_item'  style='padding-left:5px;' colspan='3'>
						  <input type=text name='registration_id' class='textbox' value='".$registration_id."' style='width:30% ; vertical-align:top;' >
					  </td>
					</tr>
					<tr height=30>
					  <td class='search_box_title'>회원코드</td>
					  <td class='search_box_item'  style='padding-left:5px;' colspan='3'>
						  <input type=text name='user_code' class='textbox' value='".$user_code."' style='width:30% ; vertical-align:top;' >
					  </td>
					</tr>
					";

		$vdate = date("Ymd", time());
		$today = date("Ymd", time());
		$vyesterday = date("Ymd", time()-84600);
		$voneweekago = date("Ymd", time()-84600*7);
		$vtwoweekago = date("Ymd", time()-84600*14);
		$vfourweekago = date("Ymd", time()-84600*28);
		$vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
		$voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
		$v15ago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
		$vfourweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
		$vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
		$v2monthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
		$v3monthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

		 $mstring .= "
					<tr height=30>
						<td class='search_box_title'>
						<label for='use_sdate'>단말등록일</label>
						</td>
						<td class='search_box_item' colspan='3'>
							".search_date('sdate','edate',$sdate,$edate,'N','D')."	";

							$mstring .= "
						</td>
					</tr>
					</table>
				</td>
				<th class='box_06'></th>
			</tr>
			<tr>
				<th class='box_07'></th>
				<td class='box_08'></td>
				<th class='box_09'></th>
			</tr>
		</table>";



		$mstring .= "
			</td>
		</tr>
		<tr >
			<td style='padding:10px 0px;' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
		</tr>
		</form>
			<tr>
				<td>
					<table cellpadding=0 cellspacing=0 border=0 width=100%  class='list_table_box'>
						<col width='5%'>
						<col width='17%'>
						<col width='13%'>
						<col width='13%'>
						<tr align=center bgcolor=#efefef height='30'>
							<td class=s_td>SEQ</td>
							<td class=m_td>운영체제</td>
							<td class=m_td>회원코드</td>
							<td class=m_td>앱구분</td>
							<td class=m_td>등록일</td>
						</tr>
						<tr align=center bgcolor=#efefef height='30'>
							<td class=m_td colspan=5>registration ID</td>
						</tr>";

//paging variable
$max = 5;
if(empty($_GET['page'])){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

// 검색조건 : registration id
if(! empty($_REQUEST['registration_id'])){
	if(! empty($where)) $where .= " AND ";
	$where .= " key_value = '".$_REQUEST['registration_id']."'";
}

// 검색조건 : 회원코드
if(! empty($_REQUEST['user_code'])){
	if(! empty($where)) $where .= " AND ";
	$where .= " user_code = '".$_REQUEST['user_code']."'";
}

// 검색조건 : 운영체제
if(! empty($_REQUEST['os'])){
	if(! empty($where)) $where .= " AND ";
	$where .= " os = '".$_REQUEST['os']."'";
}

// 검색조건 : 날짜
if(! empty($_REQUEST['sdate'])){
	$sdate = $_REQUEST['sdate'] . ' 00:00:00';
	$edate = $_REQUEST['edate'] . ' 23:59:59';
	if(! empty($where)) $where .= " AND ";
	$where .= " regdate BETWEEN '".$sdate."' AND '".$edate."'";
}
if(! empty($where)) $where = ' WHERE '.$where;

// 컨텐츠 총 갯수 조회
$sql = "SELECT count(*) as total
		FROM mobile_push_service 
		" . $where;



$db->query($sql);
$db->fetch();
$total = $db->dt['total'];

$content_qry = $sql;

if($total > 0){
	$sql = "SELECT * 
		FROM mobile_push_service 
		" . $where . " 
		ORDER BY sequence DESC 
		LIMIT ".$start.", ".$max;
	$content_qry = $sql;
	$db->query($sql);
	$result = $db->fetchall();

	foreach($result as $rt):
		$result = str_replace('success', '성공', $rt['result']);
		$result = str_replace('fail', '실패', $result);
		if($rt['contents_type'] == 'img'){
			$send_data = "<a href='#' onclick='imageView(\"".$rt['contents']."\");'>발송한 이미지 보기</a>";
		}else{
			$send_data = $rt['contents'];
		}
		
		if ($rt['os'] == 'a') {
		    $rt['os'] = '안드로이드';
		} else {
		    $rt['os'] = '아이폰';
		}
		
		if (empty($rt['user_code'])) {
		    $rt['user_code'] = "회원코드 없음";
		}

		$mstring .= "<tr height='30'>
						<td align=center>".$rt['sequence']."</td>
						<td align=center style='text-align:center;padding:5px;line-height:150%;'>".$rt['os']."</td>
						<td align=center style='text-align:center;padding:5px;line-height:150%;'>".$rt['user_code']."</td>
						<td align=center style='text-align:center;padding:5px;line-height:150%;'>".$rt['app_div']."</td>
						<td align=center>".$rt['regdate']."</td>
					</tr>";
		$mstring .= "<tr height='30'><td colspan=5 style='text-align:center;padding:5px;line-height:150%;'>". $rt['key_value'] ."</td></tr>";
		
	endforeach;

}else{
	$mstring .= "<tr height='30'><td colspan=7 align=center>검색 결과가 없습니다.</td></tr>";
}

$mstring .="		</table>
				</td>
			</tr>
		</table>";

						
	// 검색결과 총 개수
	$mstring .= "<br><table cellpadding=0 cellspacing=0 border=0 width=100%>
			<col width='10%'>
	        <tr height='30' bgcolor=#efefef>
	        <td style='text-align:center;padding:5px;line-height:150%;'>총 검색 결과 : ". $total ."건</td></tr></table>";
					


$query_string = "&search_text=".$_REQUEST['search_text']."&sdate=".$_REQUEST['sdate']."&edate=".$$_REQUEST['edate'];

$mstring .= "<table cellpadding=0 cellspacing=0 border=0 width=100%  >
				<tr bgcolor=#ffffff style='height:50px;'>
					<td colspan=3 align=center>
					".page_bar($total, $page, $max, $query_string," ")."
					</td>
				</tr>
			</table>
			";

$help_text = '발송한 푸시메시지의 목록을 확인합니다.<br>';
$help_text .= $content_qry;
$help_text = HelpBox("<table cellpadding='0' cellspacing='0' border='0'><tr><td valign=top><b>푸시메시지 발송목록</b></td></tr></table>", $help_text,220);

$Contents = $mstring.$help_text;

$P = new LayOut();
$P->addScript = "".$Script;
$P->OnloadFunction = "init();";
$P->Navigation = "모바일샵관리 > 푸시메시지 > 단말기 목록";
$P->title = "단말기 목록";
$P->strLeftMenu = mshop_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();
