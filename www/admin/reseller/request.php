<?
include("../class/layout.class");

$db = new Database;
$mdb = new Database;

$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

if ($FromYY == ""){
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY."-".$FromMM."-".$FromDD;
	$endDate = $ToYY."-".$ToMM."-".$ToDD;
}

if($regdate == 1){
	$cmd_sdate = $FromYY."-".$FromMM."-".$FromDD;
	$cmd_edate = $ToYY."-".$ToMM."-".$ToDD;
}

if($visitdate == 1){
	$slast = $FromYY2."-".$FromMM2."-".$FromDD2;
	$elast = $ToYY2."-".$ToMM2."-".$ToDD2;
}

$Script = "
<script language='javascript'>

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
		frm.FromYY2.disabled = false;
		frm.FromMM2.disabled = false;
		frm.FromDD2.disabled = false;
		frm.ToYY2.disabled = false;
		frm.ToMM2.disabled = false;
		frm.ToDD2.disabled = false;
	}else{
		frm.FromYY2.disabled = true;
		frm.FromMM2.disabled = true;
		frm.FromDD2.disabled = true;
		frm.ToYY2.disabled = true;
		frm.ToMM2.disabled = true;
		frm.ToDD2.disabled = true;
	}
}


function init(){

	var frm = document.searchmember;
	onLoad('$sDate','$eDate','$sDate2','$eDate2','$sDate3');
	onLoad2('$sDate','$eDate','$sDate2','$eDate2','$sDate3');";

	if($regdate != "1"){
	$Script .= "
		frm.FromYY.disabled = true;
		frm.FromMM.disabled = true;
		frm.FromDD.disabled = true;
		frm.ToYY.disabled = true;
		frm.ToMM.disabled = true;
		frm.ToDD.disabled = true;";
	}
	if($visitdate != "1"){
	$Script .= "
		frm.FromYY2.disabled = true;
		frm.FromMM2.disabled = true;
		frm.FromDD2.disabled = true;
		frm.ToYY2.disabled = true;
		frm.ToMM2.disabled = true;
		frm.ToDD2.disabled = true;";
	}

$Script .= "
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

include ($_SERVER["DOCUMENT_ROOT"]."/admin/reseller/reseller.lib.php");

$update_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U");
$delete_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D");
$create_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C");
$excel_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E");

if($_COOKIE[member_max_limit]){
	$max = $_COOKIE[member_max_limit]; //페이지당 갯수
}else{
	$max = 50;
}

$mode = "reseller";
include $_SERVER["DOCUMENT_ROOT"]."/admin/member/member_query.php";

if($mode == "excel"){
	//echo $sql;
	//exit;
	$goods_infos = $db->fetchall("object");

	$info_type = "member";
	include("excel_out_columsinfo.php");
	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='member_list_".$info_type."' ";
	$db->query($sql);
	$db->fetch();
	$stock_report_excel = $db->dt[conf_val];

	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='check_member_list_".$info_type."' ";
	$db->query($sql);
	$db->fetch();
	$stock_report_excel_checked = $db->dt[conf_val];

	$check_colums = unserialize(stripslashes($stock_report_excel_checked));
	
	if(empty($check_colums)){
		echo "<script>alert('다운받을 엑셀 항목이 설정되어 있지 않습니다. 엑셀 설정 후 다시 시도 바랍니다.')</script>";
		echo "<script>window.history.back()</script>";
		exit;
	}

	$columsinfo = $colums;

	include '../include/phpexcel/Classes/PHPExcel.php';
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

	date_default_timezone_set('Asia/Seoul');

	$inventory_excel = new PHPExcel();

	// 속성 정의
	$inventory_excel->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("accounts plan price List")
								 ->setSubject("accounts plan price List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("accounts plan price List");
	$col = 'A';
	foreach($check_colums as $key => $value){
		$inventory_excel->getActiveSheet(0)->setCellValue($col . "1", $columsinfo[$value][title]);
		$col++;
	}

	$before_pid = "";

	for ($i = 0; $i < count($goods_infos); $i++)
	{
		$j="A";
		foreach($check_colums as $key => $value){
			if($key == "authorized"){
				switch($goods_infos[$i][authorized]){
					case "Y":
						$value_str = "승인";
						break;
					case "N":
						$value_str = "승인대기";
						break;
					case "X":
						$value_str = "승인거부";
						break;
					default:
						$value_str = "알수없음";
						break;
				}
			}else if($key == "mem_type"){

				switch($goods_infos[$i][mem_type]){
					case "M":
						$value_str = "일반회원";
						break;
					case "C":
						$value_str = "기업".($db->dt[com_name] != "" ? "(".$db->dt[com_name].")":"");
						break;
					case "A":
						$value_str = "직원(관리자)";
						break;
				}

			}else if($key == "mem_div"){
				switch($goods_infos[$i][mem_div]){
					case "MD":
					$value_str = "MD담당자";
					break;
					case "S":
					$value_str = "셀러";
					break;
					case "D":
					$value_str = "기타";
					break;
				}
			}else if($key == "nationality"){
				switch($goods_infos[$i][nationality]){
					case "I":
					$value_str = "국내";
					break;
					case "O":
					$value_str = "해외";
					break;
				}
			}else{
				$value_str = $goods_infos[$i][$value];//$db1->dt[$value];
			}

			$inventory_excel->getActiveSheet()->setCellValue($j . ($z + 2), $value_str);
			$j++;

			unset($history_text);
		}
		$z++;
	}
	// 첫번째 시트 선택
	$inventory_excel->setActiveSheetIndex(0);

	// 너비조정
	$col = 'A';
	foreach($check_colums as $key => $value){
		$inventory_excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
		$col++;
	}

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="member_list_'.$info_type.'.xls"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'Excel5');
	$objWriter->save('php://output');

	exit;
}


$Script .= "
<script language='javascript'>

function view_go()
{
	var sort = document.view.sort[document.view.sort.selectedIndex].value;
	location.href = 'member.php?view='+sort;
}


function deleteMemberInfo(act, code){
	if(confirm('해당회원 정보를 정말로 삭제하시겠습니까? \\n 삭제시 관련된 모든 정보가 삭제됩니다.')){
		window.frames['iframe_act'].location.href= 'member.act.php?act='+act+'&code='+code;
	}
}

$(document).ready(function (){

	$('#max').change(function(){
		var value= $(this).val();
		$.cookie('member_max_limit', value, {expires:1,domain:document.domain, path:'/', secure:0});
		document.location.reload();
		
	});

});


</script>";

$Contents = "
<script language='javascript' src='/admin/reseller/reseller.js'></script>
<script language='javascript' src='inflow_detail.js?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."'></script>
<table width='100%' border='0' align='center'>
<tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("매니저 적용관리", "회원관리 > 매니저 적용관리 ")."</td>
</tr>
<tr>
	<td>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	<tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "본사관리 > $menu_name")."</td>
	</tr>
	<tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<col width='550px'>
			<col width='*'>
			<tr>
				<td class='tab'>
					<table id='tab_01' class='on'>
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  >전체회원</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($info_type == "reseller" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

							$Contents .= "<a href='reseller_list.php'>리셀러 회원</a>";

						$Contents .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
				<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'>

				</td>
			</tr>
			</table>
		</div>
		</td>
	</tr>
</table>";

$Contents .= "
	<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
		<tr>
			<th class='box_01'></th>
			<td class='box_02'></td>
			<th class='box_03'></th>
		</tr>
		<tr>
			<th class='box_04'></th>
			<td class='box_05'  valign=top style='padding:5px 0px 5px 0px'>
			<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
			<form name=searchmember method='get'><!--SubmitX(this);'-->
				<input type='hidden' name=mc_ix value='".$mc_ix."'>
				<input type='hidden' name=mode value='search'>
				<col width='18%'>
				<col width='32%'>
				<col width='18%'>
				<col width='32%'>
					<tr height=27>
						<td class='search_box_title' >조건검색 </td>
						<td class='search_box_item'>
							<table cellpadding=0 cellspacing=0 width=100%>
								<col width='80'>
								<col width='*'>
								<tr>
									<td>
									<select name=search_type>
											<option value='cmd.name' ".CompareReturnValue("cmd.name",$search_type,"selected").">고객명</option>
											<option value='cu.id' ".CompareReturnValue("cu.id",$search_type,"selected").">아이디</option>
											<option value='cmd.tel' ".CompareReturnValue("cmd.tel",$search_type,"selected").">전화번호</option>
											<option value='cmd.pcs' ".CompareReturnValue("cmd.pcs",$search_type,"selected").">휴대전화</option>
											<option value='ccd.com_name' ".CompareReturnValue("ccd.com_name",$search_type,"selected").">회사명</option>
											<option value='ccd.com_phone' ".CompareReturnValue("ccd.com_phone",$search_type,"selected").">회사전화</option>
											<option value='ccd.com_fax' ".CompareReturnValue("ccd.com_fax",$search_type,"selected").">회사팩스</option>
											<option value='cmd.mail' ".CompareReturnValue("cmd.mail",$search_type,"selected").">이메일</option>
											<option value='cmd.addr1' ".CompareReturnValue("cmd.addr1",$search_type,"selected").">주소</option>
									</select>
									</td>
									<td>
										<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:70%;font-size:12px;padding:1px;' >
									</td>
								</tr>
							</table>
						</td>
						<td class='search_box_title' >회원그룹 </td>
						<td class='search_box_item' >
						".makeGroupSelectBox($mdb,"gp_ix",$gp_ix)."
						</td>
					</tr>
					<tr height=27>
						<td class='search_box_title' >국내/해외 </td>
						<td class='search_box_item' >
						<input type=radio name='nationality' value=''  id='nationality_'  ".CompareReturnValue("",$nationality,"checked")." checked><label for='nationality_'>전체</label>
						<input type=radio name='nationality' value='I' id='nationality_I' ".CompareReturnValue("I",$nationality,"checked")."><label for='nationality_I'>국내회원</label>
						<input type=radio name='nationality' value='O' id='nationality_O' ".CompareReturnValue("O",$nationality,"checked")."><label for='nationality_O'>해외회원</label>
						<input type=radio name='nationality' value='D' id='nationality_D' ".CompareReturnValue("D",$nationality,"checked")."><label for='nationality_D'>기타회원</label>
						</td>
						<td class='search_box_title' >회원구분 </td>
						<td class='search_box_item' >
						<input type=radio name='mem_type' value=''  id='mem_type_'  ".CompareReturnValue("",$mem_type,"checked")." checked><label for='mem_type_'>전체</label>
						<input type=radio name='mem_type' value='M' id='mem_type_m' ".CompareReturnValue("M",$mem_type,"checked")."><label for='mem_type_m'>일반회원</label>
						<input type=radio name='mem_type' value='C' id='mem_type_c' ".CompareReturnValue("C",$mem_type,"checked")."><label for='mem_type_c'>사업자회원</label>
						<input type=radio name='mem_type' value='A' id='mem_type_a' ".CompareReturnValue("A",$mem_type,"checked")."><label for='mem_type_a'>직원(관리자)</label>
						</td>
					</tr>
					<tr  height=27>
						<td class='search_box_title'  >지역선택</td>
						<td class='search_box_item'>
							<select name='region' style='width:100px;font-size:12px;'>
								<option value=''>지역 선택</option>
								<option value='서울'  ".CompareReturnValue("서울",$region,"selected").">서울</option>
								<option value='충북'  ".CompareReturnValue("충북",$region,"selected").">충북</option>
								<option value='충남'  ".CompareReturnValue("충남",$region,"selected").">충남</option>
								<option value='전북'  ".CompareReturnValue("전북",$region,"selected").">전북</option>
								<option value='제주'  ".CompareReturnValue("제주",$region,"selected").">제주</option>
								<option value='전남'  ".CompareReturnValue("전남",$region,"selected").">전남</option>
								<option value='경북'  ".CompareReturnValue("경북",$region,"selected").">경북</option>
								<option value='경남'  ".CompareReturnValue("경남",$region,"selected").">경남</option>
								<option value='경기'  ".CompareReturnValue("경기",$region,"selected").">경기</option>
								<option value='부산'  ".CompareReturnValue("부산",$region,"selected").">부산</option>
								<option value='대구'  ".CompareReturnValue("대구",$region,"selected").">대구</option>
								<option value='인천'  ".CompareReturnValue("인천",$region,"selected").">인천</option>
								<option value='광주'  ".CompareReturnValue("광주",$region,"selected").">광주</option>
								<option value='대전'  ".CompareReturnValue("대전",$region,"selected").">대전</option>
								<option value='울산'  ".CompareReturnValue("울산",$region,"selected").">울산</option>
								<option value='강원'  ".CompareReturnValue("강원",$region,"selected").">강원</option>
								</select>
						</td>
						<td class='search_box_title' >회원타입 </td>
						<td class='search_box_item' >
							<input type=radio name='mem_div' value='' id='mem_div_'  ".CompareReturnValue("",$mem_div,"checked")." checked><label for='mem_div_'>전체</label>
							<input type=radio name='mem_div' value='S' id='mem_div_s'  ".CompareReturnValue("S",$mem_div,"checked")."><label for='mem_div_s'>셀러</label>
							<input type=radio name='mem_div' value='MD' id='mem_div_md' ".CompareReturnValue("MD",$mem_div,"checked")."><label for='mem_div_md'>MD담당자</label>
							<input type=radio name='mem_div' value='D' id='mem_div_d' ".CompareReturnValue("D",$mem_div,"checked")."><label for='mem_div_d'>기타</label>
						</td>
					</tr>
					<tr height=27>
						<td class='search_box_title' >이메일 발송여부 </td>
						<td class='search_box_item'  >
						<input type=radio name='mailsend_yn' value='A' id='mailsend_a'  ".CompareReturnValue("A",$mailsend_yn,"checked")." checked><label for='mailsend_a'>전체</label>
						<input type=radio name='mailsend_yn' value='1' id='mailsend_y'  ".CompareReturnValue("1",$mailsend_yn,"checked")."><label for='mailsend_y'>수신회원만</label><input type=radio name='mailsend_yn' value='0' id='mailsend_n' ".CompareReturnValue("0",$mailsend_yn,"checked")."><label for='mailsend_n'>수신거부회원</label>
						</td>
						<td class='search_box_title' >SMS 발송여부 </td>
						<td class='search_box_item'  >
							<input type=radio name='smssend_yn' value='A' id='smssend_a'  ".CompareReturnValue("A",$smssend_yn,"checked")." checked><label for='smssend_a'>전체</label>
							<input type=radio name='smssend_yn' value='1' id='smssend_y'  ".CompareReturnValue("1",$smssend_yn,"checked")."><label for='smssend_y'>수신회원만</label>
							<input type=radio name='smssend_yn' value='0' id='smssend_n' ".CompareReturnValue("0",$smssend_yn,"checked")."><label for='smssend_n'>수신거부회원</label>
						</td>
					</tr>";

	 $Contents .= "
					<tr height=27>
						<td class='search_box_title' ><label for='regdate'>가입일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".CompareReturnValue("1",$regdate,"checked")."></td>
						<td class='search_box_item'  colspan=3 >
							<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
								<tr>
									<TD nowrap>
										<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY  style='width:57px;'></SELECT> 년
										<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM style='width:43px;'></SELECT> 월
										<SELECT name=FromDD style='width:43px;'></SELECT> 일
									</TD>
									<TD style='padding:0 5px;' align=center>~</TD>
									<TD nowrap>
										<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY style='width:57px;'></SELECT> 년
										<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM style='width:43px;'></SELECT> 월
										<SELECT name=ToDD style='width:43px;'></SELECT> 일
									</TD>
									<TD style='padding-left:10px;' >
										<a href=\"javascript:select_date('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
										<a href=\"javascript:select_date('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
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
					<tr height=27>
						<td class='search_box_title' ><label for='visitdate'>최근방문일</label><input type='checkbox' name='visitdate' id='visitdate' value='1' onclick='ChangeVisitDate(document.searchmember);' ".CompareReturnValue("1",$visitdate,"checked")."></td>
						<td class='search_box_item'  colspan=3 >
							<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
								<tr>
									<TD nowrap>
										<SELECT onchange=javascript:onChangeDate(this.form.FromYY2,this.form.FromMM2,this.form.FromDD2) name=FromYY2  style='width:57px;'></SELECT> 년
										<SELECT onchange=javascript:onChangeDate(this.form.FromYY2,this.form.FromMM2,this.form.FromDD2) name=FromMM2 style='width:43px;'></SELECT> 월
										<SELECT name=FromDD2 style='width:43px;'></SELECT> 일
									</TD>
									<TD style='padding:0 5px;' align=center>~</TD>
									<TD nowrap>
										<SELECT onchange=javascript:onChangeDate(this.form.ToYY2,this.form.ToMM2,this.form.ToDD2) name=ToYY2 style='width:57px;'></SELECT> 년
										<SELECT onchange=javascript:onChangeDate(this.form.ToYY2,this.form.ToMM2,this.form.ToDD2) name=ToMM2 style='width:43px;'></SELECT> 월
										<SELECT name=ToDD2 style='width:43px;'></SELECT> 일
									</TD>
									<TD style='padding-left:10px;' >
										<a href=\"javascript:select_date2('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
										<a href=\"javascript:select_date2('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
										<a href=\"javascript:select_date2('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
										<a href=\"javascript:select_date2('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
										<a href=\"javascript:select_date2('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
										<a href=\"javascript:select_date2('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
										<a href=\"javascript:select_date2('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
									</TD>
								</tr>
							</table>
						</td>
					</tr>
					<tr height=27>
						<td class='search_box_title' >마일리지(M) 보유</td>
						<td class='search_box_item'  >
							<input type=radio name='mileage' value='' id='reserve_'  ".CompareReturnValue("",$mileage,"checked")." checked><label for='reserve_'>전체</label>
							<input type=radio name='mileage' value='1' id='reserve_y'  ".CompareReturnValue("1",$mileage,"checked")."><label for='reserve_y'>보유</label>
							<input type=radio name='mileage' value='2' id='reserve_n' ".CompareReturnValue("2",$mileage,"checked")."><label for='reserve_n'>미보유</label>
						</td>
						<td class='search_box_title' >포인트(P) 보유 </td>
						<td class='search_box_item'  >
							<input type=radio name='point' value='' id='point_'  ".CompareReturnValue("",$point,"checked")." checked><label for='point_'>전체</label>
							<input type=radio name='point' value='1' id='point_y'  ".CompareReturnValue("1",$point,"checked")."><label for='point_y'>보유</label>
							<input type=radio name='point' value='2' id='point_n' ".CompareReturnValue("2",$point,"checked")."><label for='point_n'>미보유</label>
						</td>
					</tr>
					<tr height=27>
						<td class='search_box_title' >가입경로</td>
						<td class='search_box_item'  colspan=3>
							<input type=radio name='agent_type' value='' id='agent_type_'  ".CompareReturnValue("",$mileage,"checked")." checked><label for='agent_type_'>전체</label>
							<input type=radio name='agent_type' value='W' id='agent_type_W'  ".CompareReturnValue("1",$mileage,"checked")."><label for='agent_type_W'>PC(web)</label>
							<input type=radio name='agent_type' value='M' id='agent_type_M' ".CompareReturnValue("2",$mileage,"checked")."><label for='agent_type_M'>모바일</label>
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
<tr height=50>
	<td style='padding:10px 20px 0 20px' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
</tr>
</table><br></form>

<form name='list_frm'>
<input type='hidden' name='code[]' id='code'>
<table width='100%' cellpadding=0 cellspacing=0 border=0>
<col width=10%>
<col width=52%>
<col width=18%>
<col width=9%>
<tr>
	<td>전체 회원수 : ".number_format($total)." 명</td>
	<td align=left height=30>
	</td>
	<td align=right>
	</td>
	<td align=right>
	목록수 : <select name='max' id='max'>
				<option value='5' ".($_COOKIE[member_max_limit] == '5'?'selected':'').">5</option>
				<option value='10' ".($_COOKIE[member_max_limit] == '10'?'selected':'').">10</option>
				<option value='20' ".($_COOKIE[member_max_limit] == '20'?'selected':'').">20</option>
				<option value='30' ".($_COOKIE[member_max_limit] == '30'?'selected':'').">30</option>
				<option value='50' ".($_COOKIE[member_max_limit] == '50'?'selected':'').">50</option>
				<option value='100' ".($_COOKIE[member_max_limit] == '100'?'selected':'').">100</option>
				<option value='500' ".($_COOKIE[member_max_limit] == '500'?'selected':'').">500</option>
			</select>
	</td>
</tr>
</table>

<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
<tr height='34' bgcolor='#ffffff'>
	<td width='3%' align='center' class='m_td'><font color='#000000'><b>순서</b></font></td>
	<td width='5%' align='center' class='m_td'><font color='#000000'><b>그룹</b></font></td>
	<td width='5%' align='center' class='m_td small' nowrap><font color='#000000'><b>국내<br>해외</b></font></td>
	<td width='6%' align='center' class='m_td' nowrap><font color='#000000'><b>회원구분</b></font></td>
	 <td width='6%' align='center' class='m_td' nowrap><font color='#000000'><b>회원타입</b></font></td>
	<td width='6%' align='center' class=m_td nowrap><font color='#000000'><b>승인여부</b></font></td>
	<td width='7%' align='center' class=m_td><font color='#000000'><b>이름</b></font></td>
	<td width='9%' align='center' class=m_td><font color='#000000'><b>아이디</b></font></td>
	<td width='17%' align='center' class=m_td><font color='#000000'><b>이메일</b></font></td>
	<td width='9%' align='center' class=m_td><font color='#000000'><b>최근방문일</b></font></td>
	<td width='6%' align='center' class=m_td><font color='#000000'><b>로긴수</b></font></td>";
	if($admininfo[mall_type] != "H"){
	$Contents .= "
	<td width='6%' align='center' class=m_td><font color='#000000'><b>마일리지</b></font></td>
	<td width='6%' align='center' class=m_td><font color='#000000'><b>포인트</b></font></td>";
	}
	$Contents .= "
	<td width='20%' align='center' class=e_td><font color='#000000'><b>관리</b></font></td>
</tr>";

	for ($i = 0; $i < $db->total; $i++){

		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;

		if($db->dt[is_id_auth] != "Y"){
			$is_id_auth = "미인증";
		}else{
			$is_id_auth = "";
		}

		switch($db->dt[authorized]){
			case "Y":
				$authorized = "승인";
				break;
			case "N":
				$authorized = "승인대기";
				break;
			case "X":
				$authorized = "승인거부";
				break;
			default:
				$authorized = "알수없음";
				break;
		}

		switch($db->dt[mem_type]){
			case "M":
				$mem_type = "일반회원";
				break;
			case "C":
				$mem_type = "기업<br>".($db->dt[com_name] != "" ? "(".$db->dt[com_name].")":"");
				break;
			case "A":
				$mem_type = "직원(관리자)";
				break;
		}

		switch($db->dt[mem_div]){
			case "MD":
			$mem_div = "MD담당자";
			break;
			case "S":
			$mem_div = "셀러";
			break;
			case "D":
			$mem_div = "기타";
			break;
		}
		switch($db->dt[nationality]){
			case "I":
			$nationality = "국내";
			break;
			case "O":
			$nationality = "해외";
			break;
			case "D":
			$nationality = "기타";
			break;
		}

if($_SESSION["admininfo"]["charger_id"] == "forbiz"){
		switch($db->dt[sex_div]){
			case "W":
			$sex_div = "<span style='padding-left:2px' class='helpcloud' help_width='45' help_height='20' help_html='여성'><img src='../images/".$admininfo["language"]."/Female.png' style='cursor:pointer;'></span>";
			break;
			case "M":
			$sex_div = "<span style='padding-left:2px' class='helpcloud' help_width='45' help_height='20' help_html='남성'><img src='../images/".$admininfo["language"]."/Male.png' style='cursor:pointer;'></span>";
			break;
			case "D":
			default:
			$sex_div = "";
			break;
		}
}

		$Contents = $Contents."
		<tr height='32' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
			<td class='list_box_td' nowrap>".$no."</td>
			<td class='list_box_td' style='padding:0px 5px;' nowrap><span title='".$db->dt[organization_name]."'>".$db->dt[gp_name]."</span></td>
			<td class='list_box_td' nowrap>".$nationality."(".($db->dt[agent_type] == "M" ? "모바일":"웹").")</td>
			<td class='list_box_td small' style='line-height:150%;' nowrap>".$mem_type."</td>
			<td class='list_box_td' nowrap>".$mem_div."</td>
			<td class='list_box_td' >".$authorized."</td>
			<td class=' point' nowrap align='left'>
				<table border='0' width='100%'>
				<tr>
					<td width='17'>".$sex_div."</td>
					<td align='left'>";
					if($update_auth){
						$Contents .= "<a href=\"javascript:PopSWindow2('/admin/member/member_cti.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',1280,800,'member_view')\" style='cursor:pointer' >";
					}else{
						$Contents .= "<a href=\"".$auth_update_msg."\">";
					}
					$Contents .="
					<!--<a href=\"javascript:PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')\" style='cursor:pointer' >-->".Black_list_check($db->dt[code],$db->dt[name])."
					</td>
				</tr>
				</table>
			</td>
			<td class='list_box_td' ><a href=\"javascript:PopSWindow2('/admin/member/member_cti.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',1280,800,'member_view')\" style='cursor:pointer' >".$db->dt[id]."</a></td>
			<td class='list_box_td' >".$db->dt[mail]."<font color=red> ".$is_id_auth."</font></td>
			<td class='list_box_td' >".$db->dt[last]."</td>
			<td class='list_box_td' >".$db->dt[visit]."</td>";
		if($admininfo[mall_type] != "H"){
			$Contents .= "
			<td class='list_box_td ctr point' ><a href=\"javascript:PoPWindow('/admin/member/reserve.pop.php?code=".$db->dt[code]."',700,700,'reserve_pop')\">".$db->dt[mileage]."</a></td>
			<td class='list_box_td ctr point' ><a href=\"javascript:PoPWindow('/admin/member/point.pop.php?code=".$db->dt[code]."',700,700,'reserve_pop')\">".$db->dt[point]."</a></td>";
		}
			$Contents .= "
			<td class='list_box_td ctr'  style='padding:5px;' nowrap>";

			if($db->dt[rsl_ix] == ""){
				if($update_auth){
					$Contents .= "<a href=\"javascript:PoPWindow('request.bank.php?code=".$db->dt[code]."',700,250,'reserve_pop')\"><img src='../images/".$admininfo["language"]."/bts_manager.gif' border=0 align=absmiddle style='cursor:pointer;' alt='매니저 적용' title='매니저 적용'/></a>";
				}else{
					$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_manager.gif' border=0 align=absmiddle alt='매니저 적용' title='매니저 적용' ></a> ";
				}
			}

			$Contents .= "
	</td>
</tr>";

}

if (!$db->total){

$Contents .= "
<tr height=50>
	<td colspan='15' align='center'>";
		if($mode == "search"){
			$Contents .= "검색결과에 맞는 회원 데이타가 없습니다.";
		}else{
			$Contents .= "조회하시고자 하는 검색조건을 선택후 검색해주세요";
		}
		$Contents .= "
	</td>
</tr>";
}

$Contents .= "
</table>
</form>

<table width=100%>
	<tr height='40'>
		<td align='left'>";

$Contents .= "
		</td>
		<td align='right'>".$str_page_bar."</td>
	</td>
</tr>
</table>";

/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원에게 SMS 또는 메일을 보내실려면 보내고자 하는 회원을 선택하신후 '선택회원 SMS 보내기' 버튼을 클릭하신후 메일을 보내실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원정보를 백업하기 위해서는 회원정보 검색후 엑셀로 저장 버튼을 클릭하시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >검색기능을 통해서 조건에 맞는 회원을 빠르게 검색하실수 있습니다</td></tr>
</table>
";*/

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("회원관리", $help_text,'70');


$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";


$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->strLeftMenu = reseller_menu();
$P->Navigation = "회원관리 > 매니저 적용";
$P->title = "매니저 적용";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>



