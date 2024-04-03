<?
include("../class/layout.class");

$db = new Database;
$mdb = new Database;

if ($FromYY == ""){
    $before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

    //	$sDate = date("Y/m/d");
    $sDate = date("Y/m/d", $before10day);
    $eDate = date("Y/m/d");

    $startDate = date("Ymd", $before10day);
    $endDate = date("Ymd");
}else{
    $sDate = $FromYY."/".$FromMM."/".$FromDD;
    $eDate = $ToYY."/".$ToMM."/".$ToDD;
    $startDate = $FromYY.$FromMM.$FromDD;
    $endDate = $ToYY.$ToMM.$ToDD;
}

$Script = "
<script language='javascript'>

	function popupDelete(popup_ix){
		if(confirm('해당 팝업를 정말로 삭제하시겠습니까? 삭제하시면 관련된 모든 이미지가 삭제됩니다.'))
		{
			//document.frames('act').location.href= 'popup.act.php?act=delete&popup_ix='+popup_ix;//kbk
			document.getElementById('act').src= 'popup.act.php?act=delete&popup_ix='+popup_ix;
		}
	}
";

if($regdate){
    $Script .= "
	$( document ).ready(function() {
		$('#regdate').trigger('click');

		var frm = document.searchmember;
		ChangeRegistDate(frm);
	});";
}

$Script .= "
	function ChangeRegistDate(frm){
		if(frm.regdate.checked){
			frm.FromYY.disabled = false;
			frm.FromMM.disabled = false;
			frm.FromDD.disabled = false;
			frm.ToYY.disabled = false;
			frm.ToMM.disabled = false;
			frm.ToDD.disabled = false;
		}else{
			frm.FromYY.disabled = true;
			frm.FromMM.disabled = true;
			frm.FromDD.disabled = true;
			frm.ToYY.disabled = true;
			frm.ToMM.disabled = true;
			frm.ToDD.disabled = true;
		}
	}

	function ChangeVisitDate(frm){
		if(frm.visitdate.checked){
			frm.vFromYY.disabled = false;
			frm.vFromMM.disabled = false;
			frm.vFromDD.disabled = false;
			frm.vToYY.disabled = false;
			frm.vToMM.disabled = false;
			frm.vToDD.disabled = false;
		}else{
			frm.vFromYY.disabled = true;
			frm.vFromMM.disabled = true;
			frm.vFromDD.disabled = true;
			frm.vToYY.disabled = true;
			frm.vToMM.disabled = true;
			frm.vToDD.disabled = true;
		}
	}

	function init(){

		var frm = document.searchmember;
		onLoad('$sDate','$eDate');

		frm.FromYY.disabled = true;
		frm.FromMM.disabled = true;
		frm.FromDD.disabled = true;
		frm.ToYY.disabled = true;
		frm.ToMM.disabled = true;
		frm.ToDD.disabled = true;

		frm.vFromYY.disabled = true;
		frm.vFromMM.disabled = true;
		frm.vFromDD.disabled = true;
		frm.vToYY.disabled = true;
		frm.vToMM.disabled = true;
		frm.vToDD.disabled = true;
	}

	function init_date(FromDate,ToDate) {
		var frm = document.searchmember;

		for(i=0; i<frm.FromYY.length; i++) {
			if(frm.FromYY.options[i].value == FromDate.substring(0,4))
				frm.FromYY.options[i].selected=true
		}
		for(i=0; i<frm.FromMM.length; i++) {
			if(frm.FromMM.options[i].value == FromDate.substring(5,7))
				frm.FromMM.options[i].selected=true
		}
		for(i=0; i<frm.FromDD.length; i++) {
			if(frm.FromDD.options[i].value == FromDate.substring(8,10))
				frm.FromDD.options[i].selected=true
		}

		for(i=0; i<frm.ToYY.length; i++) {
			if(frm.ToYY.options[i].value == ToDate.substring(0,4))
				frm.ToYY.options[i].selected=true
		}
		for(i=0; i<frm.ToMM.length; i++) {
			if(frm.ToMM.options[i].value == ToDate.substring(5,7))
				frm.ToMM.options[i].selected=true
		}
		for(i=0; i<frm.ToDD.length; i++) {
			if(frm.ToDD.options[i].value == ToDate.substring(8,10))
				frm.ToDD.options[i].selected=true
		}

		for(i=0; i<frm.vFromYY.length; i++) {
			if(frm.vFromYY.options[i].value == FromDate.substring(0,4))
				frm.vFromYY.options[i].selected=true
		}
		for(i=0; i<frm.vFromMM.length; i++) {
			if(frm.vFromMM.options[i].value == FromDate.substring(5,7))
				frm.vFromMM.options[i].selected=true
		}
		for(i=0; i<frm.vFromDD.length; i++) {
			if(frm.vFromDD.options[i].value == FromDate.substring(8,10))
				frm.vFromDD.options[i].selected=true
		}

		for(i=0; i<frm.vToYY.length; i++) {
			if(frm.vToYY.options[i].value == ToDate.substring(0,4))
				frm.vToYY.options[i].selected=true
		}
		for(i=0; i<frm.vToMM.length; i++) {
			if(frm.vToMM.options[i].value == ToDate.substring(5,7))
				frm.vToMM.options[i].selected=true
		}
		for(i=0; i<frm.vToDD.length; i++) {
			if(frm.vToDD.options[i].value == ToDate.substring(8,10))
				frm.vToDD.options[i].selected=true
		}
	}

	function select_date(FromDate,ToDate,dType) {
		var frm = document.searchmember;

		if(dType == 1){
			for(i=0; i<frm.FromYY.length; i++) {
				if(frm.FromYY.options[i].value == FromDate.substring(0,4))
					frm.FromYY.options[i].selected=true
			}
			for(i=0; i<frm.FromMM.length; i++) {
				if(frm.FromMM.options[i].value == FromDate.substring(5,7))
					frm.FromMM.options[i].selected=true
			}
			for(i=0; i<frm.FromDD.length; i++) {
				if(frm.FromDD.options[i].value == FromDate.substring(8,10))
					frm.FromDD.options[i].selected=true
			}

			for(i=0; i<frm.ToYY.length; i++) {
				if(frm.ToYY.options[i].value == ToDate.substring(0,4))
					frm.ToYY.options[i].selected=true
			}
			for(i=0; i<frm.ToMM.length; i++) {
				if(frm.ToMM.options[i].value == ToDate.substring(5,7))
					frm.ToMM.options[i].selected=true
			}
			for(i=0; i<frm.ToDD.length; i++) {
				if(frm.ToDD.options[i].value == ToDate.substring(8,10))
					frm.ToDD.options[i].selected=true
			}
		}else{
			for(i=0; i<frm.vFromYY.length; i++) {
				if(frm.vFromYY.options[i].value == FromDate.substring(0,4))
					frm.vFromYY.options[i].selected=true
			}
			for(i=0; i<frm.vFromMM.length; i++) {
				if(frm.vFromMM.options[i].value == FromDate.substring(5,7))
					frm.vFromMM.options[i].selected=true
			}
			for(i=0; i<frm.vFromDD.length; i++) {
				if(frm.vFromDD.options[i].value == FromDate.substring(8,10))
					frm.vFromDD.options[i].selected=true
			}

			for(i=0; i<frm.vToYY.length; i++) {
				if(frm.vToYY.options[i].value == ToDate.substring(0,4))
					frm.vToYY.options[i].selected=true
			}
			for(i=0; i<frm.vToMM.length; i++) {
				if(frm.vToMM.options[i].value == ToDate.substring(5,7))
					frm.vToMM.options[i].selected=true
			}
			for(i=0; i<frm.vToDD.length; i++) {
				if(frm.vToDD.options[i].value == ToDate.substring(8,10))
					frm.vToDD.options[i].selected=true
			}
		}
	}

	function onLoad(FromDate, ToDate) {
		var frm = document.searchmember;

		LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
		LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);
		LoadValues(frm.vFromYY, frm.vFromMM, frm.vFromDD, FromDate);
		LoadValues(frm.vToYY, frm.vToMM, frm.vToDD, ToDate);

		init_date(FromDate,ToDate);
	}

</script>";

$vdate = date("Ymd", time());
$today = date("Y/m/d", time());
$vyesterday = date("Y/m/d", time()-84600);
$voneweekago = date("Y/m/d", time()-84600*7);
$vtwoweekago = date("Y/m/d", time()-84600*14);
$vfourweekago = date("Y/m/d", time()-84600*28);
$vyesterday = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
$voneweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
$v15ago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
$vfourweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
$vonemonthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

$mstring = "
<form name=searchmember method='get' >
	<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("관리자 로그", "사이트관리 > 관리자 로그 ")."</td>
		</tr>
		<tr>
			<td align='left' colspan=6 style='padding-bottom:15px;'>
				<div class='tab' style='background: url(../images/tab/tab_bg.gif) repeat-x top'>
					<table class='s_org_tab' style='width:100%'>
						<tr>
							<td class='tab'>
								<table id='tab_01' class='on'>
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='admin_log.php'\">관리자 로그</td>
										<th class='box_03'></th>
									</tr>
								</table>
								<table id='tab_02' >
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='con_log.php'\" >접속로그</td>
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
			<td>
				<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
					<tr>
						<th class='box_01'></th>
						<td class='box_02'></td>
						<th class='box_03'></th>
					</tr>
					<tr>
						<th class='box_04'></th>
						<td class='box_05'  valign=top >
							<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='input_table_box'>
								<col width='20%'>
								<col width='*'>
								<tr height=27>
									<td class='input_box_title' ><b>조건검색</b> </td>
									<td class='input_box_item' >
										<table cellpadding=0 cellspacing=0>
											<tr>
												<td>
													<select name=search_type>
														<option value='admin_id' ".CompareReturnValue("admin_id",$search_type,"selected").">접속 ID</option>
														<option value='ip' ".CompareReturnValue("ip",$search_type,"selected").">접속 IP</option>
														<!--option value='accept_com_name' ".CompareReturnValue("accept_com_name",$search_type,"selected").">대상업체명</option>
														<option value='accept_m_name' ".CompareReturnValue("accept_m_name",$search_type,"selected").">대상업체 담당자</option-->
													</select>
												</td>
												<td style='padding:0px 3px;'>
													<input type=text name='search_text' class='textbox'  value='".$search_text."' style='width:300px;font-size:12px;' >
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr height=27>
									<td class='input_box_title'>
										<label for='regdate' style='vertical-align:middle'><b>검색일자</b></label>
										<input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' style='vertical-align:middle'>
									</td>
									<td class='input_box_item'>
										<table width=100% cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
											<tr>
												<TD width=220 nowrap>
													<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년 
													<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월 
													<SELECT name=FromDD></SELECT> 일 
												</TD>
												<TD width=14 align=center> ~ </TD>
												<TD width=220 nowrap>
													<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년 
													<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월 
													<SELECT name=ToDD></SELECT> 일
												</TD>
												<TD>
													<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
													<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
													<a href=\"javascript:select_date('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
													<a href=\"javascript:select_date('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
													<a href=\"javascript:select_date('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
												</TD>
											</tr>
										</table>
									</td>
								</tr>
								<tr height=27 style='display:none'>
									<td class='input_box_title'>
										<label for='visitdate'>종료일자</label><input type='checkbox' name='visitdate' id='visitdate' value='1' onclick='ChangeVisitDate(document.searchmember);'>
									</td>
									<td class='input_box_item' colspan=3  >
										<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
											<tr>
												<TD width=200 nowrap>
													<SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromYY ></SELECT> 년 
													<SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromMM></SELECT> 월 
													<SELECT name=vFromDD></SELECT> 일 
												</TD>
												<TD width=20 align=center> ~ </TD>
												<TD width=200 nowrap>
													<SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToYY></SELECT> 년 
													<SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToMM></SELECT> 월 
													<SELECT name=vToDD></SELECT> 일
												</TD>
												<TD>
													<a href=\"javascript:select_date('$voneweekago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
													<a href=\"javascript:select_date('$v15ago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
													<a href=\"javascript:select_date('$vonemonthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
													<a href=\"javascript:select_date('$v2monthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
													<a href=\"javascript:select_date('$v3monthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
												</TD>
											</tr>
										</table>
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
				</table>
			</td>
		</tr>
		<tr height=60>
			<td style='padding:10px 0px;' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
		</tr>
		<tr>
			<td>
				".printLogListNew()."
			</td>
		</tr>
	</table>
</form>";

/*
$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >대상업체명/담당자, 접근한 관리자ID/이름, C/R/U/D, 접속IP, 날짜를 확인 할 수 있습니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >C : 생성 / R : 읽기 / U : 수정 / D : 삭제</td></tr>
	</table>
";*/

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');
$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<div style='position:relative;top:4px;'><table><tr><td valign=bottom><b>관리자 로그</b></td><td></td></tr></table></div>", $help_text,170)."</div>";

$Contents = $mstring.$help_text;

$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->Navigation = "상점관리 > 관리자설정 > 관리자 로그";
$P->title = "관리자 로그";
$P->strLeftMenu = store_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

function PrintLogList(){
    global $db, $mdb, $page, $search_type, $search_text, $disp_yn, $regdate;
    global $FromYY, $FromMM, $FromDD, $ToYY, $ToMM, $ToDD, $vFromYY, $vFromMM, $vFromDD, $vToYY, $vToDD, $vToMM;

    $max = 10;

    if ($page == ''){
        $start = 0;
        $page  = 1;
    }else{
        $start = ($page - 1) * $max;
    }

    //$where = " where log_ix > 0 ";
    $where = array();

    if($search_type != "" && $search_text != ""){
        $where[] = "$search_type LIKE  '%$search_text%'";
    }

    $startDate = $FromYY.$FromMM.$FromDD;
    $endDate = $ToYY.$ToMM.$ToDD;

    if($startDate != "" && $endDate != ""){
        $where[] = "regdate between  $startDate and $endDate";
    }

    $where = (count($where)) ? " WHERE ".implode(" AND ", $where) : ' ';

    $sql = "select COUNT(*) as total from admin_log $where ";
    //echo $sql;
    $mdb->query($sql);
    $mdb->fetch();
    $total = $mdb->dt[total];

    $mString = "
	<table cellpadding=4 cellspacing=0 border=0 width=100% bgcolor=silver class='list_table_box'>
		<col width='10%'>
		<col width='20%'>
		<col width='20%'>
		<col width='10%'>
		<col width='20%'>
		<col width='20%'>
		<tr align=center bgcolor=#efefef height=30>
			<td class=s_td >번호</td>
			<td class=m_td >대상업체명/담당자</td>
			<td class=m_td >관리자ID/이름</td>
			<td class=m_td >C.R.U.D</td>
			<td class=m_td >접속 IP</td>
			<td class=e_td >날짜</td>
		</tr>";
    if ($total == 0){
        $mString .= "
		<tr bgcolor=#ffffff height=70>
			<td colspan=6 align=center>로그 내역이 존재 하지 않습니다.</td>
		</tr>";
    }else{
        $db->query("select * from admin_log  $where order by  regdate desc limit $start, $max");

        for($i=0;$i < $db->total;$i++){
            $db->fetch($i);

            $no = $no + 1;

            switch($db->dt[crud_div]){
                case "C":
                    $crud_div = "생성";
                    break;
                case "R":
                    $crud_div = "읽기";
                    break;
                case "U":
                    $crud_div = "수정";
                    break;
                case "D":
                    $crud_div = "삭제";
                    break;
            }

            $mString .= "
			<tr bgcolor=#ffffff align=center height=30>
				<td class='list_box_td list_bg_gray' >$no</td>
				<td class='list_box_td point' align=left style='padding:5px 10px;'>".$db->dt[accept_com_name]."/".$db->dt[accept_m_name]."</td>
				<td class='list_box_td list_bg_gray'>".$db->dt[admin_id]."/".$db->dt[admin_name]."</td>
				<td class='list_box_td '>".$crud_div."</td>
				<td class='list_box_td list_bg_gray'>".$db->dt[ip]."</td>
				<td class='list_box_td '>".$db->dt[regdate]."</td>
			</tr>";
        }

        $mString .= "
			<tr height=50 bgcolor=#ffffff>
				<td colspan=6 align=center>
					".page_bar($total, $page, $max,  "&max=$max&search_type=$search_type&search_text=$search_text&regdate=$regdate&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD&disp_yn=$disp_yn","")."
				</td>
			</tr>";
    }

    $mString .= "
	</table>";

    return $mString;
}

function printLogListNew(){
    global $db, $mdb, $page, $search_type,$search_text,$disp_yn;
    global $regdate, $FromYY,$FromMM,$FromDD,$ToYY,$ToMM,$ToDD,$vFromYY,$vFromMM,$vFromDD,$vToYY,$vToDD,$vToMM;

    $max = 15;

    if ($page == ''){
        $start = 0;
        $page  = 1;
    }else{
        $start = ($page - 1) * $max;
    }

    $where = array();

    if($search_type != "" && $search_text != ""){
        $where[] = "$search_type LIKE  '%$search_text%'";
    }

    if($regdate){
        $startDate = $FromYY."-".$FromMM."-".$FromDD." 00:00:00";
        $endDate = $ToYY."-".$ToMM."-".$ToDD." 23:59:59";

        if($startDate != "" && $endDate != ""){
            $where[] = "regdate BETWEEN '$startDate' AND '$endDate'";
        }
    }

    $where = (count($where)) ? " WHERE ".implode(" AND ", $where):'';

    $sql = "SELECT COUNT(*) AS total FROM admin_log_new $where ";
    //echo $sql;
    $mdb->query($sql);
    $mdb->fetch();
    $total = $mdb->dt[total];

    $mString = "
	<table cellpadding=4 cellspacing=0 border=0 width=100% bgcolor=silver class='list_table_box'>
		<col width='5%'>
		<col width='10%'>
		<col width='20%'>
		<col width='10%'>
		<col width='*%'>
		<col width='5%'>
		<col width='10%'>
		<tr align=center bgcolor=#efefef height=30>
			<td class=s_td >번호</td>
			<td class=m_td >관리자 ID</td>
			<td class=m_td >작업 URL</td>
			<td class=m_td >접속 IP</td>
			<td class=m_td >작업 Request</td>
			<td class=m_td >C.R.U.D</td>
			<td class=e_td >날짜</td>
		</tr>";
    if ($total == 0){
        $mString .= "<tr bgcolor=#ffffff height=70><td colspan=7 align=center>로그 내역이 존재 하지 않습니다.</td></tr>";
    }else{

        $db->query("SELECT * FROM admin_log_new $where ORDER BY regdate DESC LIMIT $start, $max");

        for($i=0;$i < $db->total;$i++){
            $db->fetch($i);

            $no = $total - ($page - 1) * $max - $i;

            switch($db->dt[crud_div]){
                case "C":
                    $crud_div = "생성";
                    break;
                case "R":
                    $crud_div = "읽기";
                    break;
                case "U":
                    $crud_div = "변경";
                    break;
                case "D":
                    $crud_div = "삭제";
                    break;
            }

            $send_data = '';
            if($db->dt[request_method] == 'POST'){
                //unserialize(urldecode($myservice_info));
                if($db->dt[send_data]){
                    $send_data = 'POST 정보';
                }

            }else if($db->dt[request_method] == 'GET'){
                $send_data = $db->dt[send_data];

                $string_len = strlen($send_data);
            }

            $mString .= "
			<tr bgcolor=#ffffff align=center height=30>
				<td class='list_box_td' >$no</td>
				<td class='list_box_td list_bg_gray'>".$db->dt[admin_id]."</td>
				<td class='list_box_td '>".$db->dt[http_host]."</td>
				<td class='list_box_td list_bg_gray'>".$db->dt[ip]."</td>
				<td class='list_box_td' ><span style='text-overflow: ellipsis; overflow: hidden; white-space: nowrap; width: 545px; display: inline-block;'>".$send_data."</span></td>
				<td class='list_box_td '>".$crud_div."</td>
				<td class='list_box_td '>".$db->dt[regdate]."</td>
			</tr>
			";
        }

        $mString .= "<tr height=50 bgcolor=#ffffff>
					<td colspan=7 align=center>".page_bar($total, $page, $max,  "&max=$max&regdate=$regdate&search_type=$search_type&search_text=$search_text&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD","")."</td>
				</tr>";
    }

    $mString .= "</table>";

    return $mString;
}

?>
