<?
include("../class/layout.class");
$db = new Database;
$db2 = new Database;
if ($ToYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
	$before1month = mktime(0, 0, 0, date("m")-1  , 21, date("Y"));
	$vdate = date("Ymd", time());
	$today = date("Ymd", time());
	$startday = 2;
	$lastday = date('t', strtotime($today));
	
//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", time()-84600*(date("d")));
	$eDate = date("Y/m/".$lastday);
	
	$startDate = date("Ymd", time()-84600*(date("d")));
	$endDate = date("Ym".$lastday);
}else{
	$vdate = date("Ymd", time());
	$today = date("Ymd", time());
	
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	

	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;

}
session_unregister("ESTIMATE_INTRA");

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

$Script = "<script language='javascript' src='../include/DateSelect.js'></script>\n
<script language='javascript' src='./estimate.js'></script>\n
<script  id='dynamic'></script>\n
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


function init(){

	var frm = document.searchmember;		
	onLoad('$sDate','$eDate');";

if($regdate != "1"){
$Script .= "
	frm.FromYY.disabled = true;
	frm.FromMM.disabled = true;
	frm.FromDD.disabled = true;
	frm.ToYY.disabled = true;
	frm.ToMM.disabled = true;
	frm.ToDD.disabled = true;";
}

$Script .= "	
}
function onLoad(FromDate, ToDate) {
	var frm = document.searchmember;
	
	LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
	LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);";	
	
if($ac_date != "1"){
$Script .= "
	frm.FromYY.disabled = true;
	frm.FromMM.disabled = true;
	frm.FromDD.disabled = true;
	frm.ToYY.disabled = true;
	frm.ToMM.disabled = true;
	frm.ToDD.disabled = true;";
}

$Script .= "
	init_date(FromDate,ToDate, 1);
	
}
function init_date(FromDate,ToDate, dType) {
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
	}
	
}


</script>";

$Script .= "
<Script Language='JavaScript'>
function setCategory(cname,cid,depth,id){	
	//document.location.href='estimate.product.php?view=innerview&cid='+cid+'&depth='+depth;	
	document.frames['act'].location.href='estimate.product.php?view=innerview&cid='+cid+'&depth='+depth;
}

function deleteEstimate(act, est_ix){
	document.frames['act'].location.href='estimate.act.php?act='+act+'&est_ix='+est_ix;
}

function ChangeStatus(est_ix, est_status){
	if(confirm('정말로 상태변경을 하시겠습니까?')){
		document.location.href='estimate.act.php?act=status_update&est_ix='+est_ix+'&est_status='+est_status;
	}
}
</Script>";


$Contents ="
<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' valign=top>			
<tr height=50>
	<td align='left' colspan=6 > ".GetTitleNavigation("오프라인 견적리스트", "주문관리 > 오프라인 견적리스트 ")."</td>
</tr>
<!--tr height=20>
	<td colspan=3 align=right style='padding-bottom:10px;'>
	".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 견적현황</b></div>")."
	</td>
</tr-->
<tr>
	<td>
		<table class='box_shadow' style='width:100%;height:100%;' >
			<tr>
				<th class='box_01'></th>
				<td class='box_02'></td>
				<th class='box_03'></th>
			</tr>
			<tr>
				<th class='box_04'></th>
				<td class='box_05'  valign=top style='padding:5 5 5 5'>	
				<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>
				<form name=searchmember method='get' style='display:inline;'>
							
					<tr  height=27>
					  <td bgcolor='#efefef' align=center>처리상태</td>
					  <td align=left  style='padding-left:5px;' colspan='3'>
					 <select name='est_status'>				
						<option value='' >전체</option>
						<option  value=0  ".CompareReturnValue('0',$est_status).">견적대기</option>
						<!--option value=1 ".CompareReturnValue(1,$est_status).">견적완료</option-->
						<option value=2 ".CompareReturnValue(2,$est_status).">주문완료</option>
						<option value=3 ".CompareReturnValue(3,$est_status).">취소</option>
					</select>
					  </td>
					  
					</tr>				    		
					<tr height=1><td colspan=4 class='dot-x'></td></tr>
					
					<tr  height=27>
					  <td width='15%' bgcolor='#efefef' align=center >검색조건</td>
					  <td align=left style='padding-left:5px;' colspan=3>
					  <select name='search_type' >
						<option value='e.est_company' ".CompareReturnValue('e.est_company',$search_type).">학교명</option>
						<option value='e.est_charger' ".CompareReturnValue('e.est_charger',$search_type).">주문자</option>
						<option value='e.est_id' ".CompareReturnValue('e.est_id',$search_type).">접수번호</option>
						<option value='cu.charger' ".CompareReturnValue('cu.charger',$search_type).">운영자</option>
					  </select>
					  <input type='text' name='search_text' size=30 value='$search_text'>
					  </td>	
					</tr>				    		
					<!--tr hegiht=1><td colspan=6 class='dot-x'></td></tr>
					<tr>
						<td width='15%' bgcolor='#efefef' align=center >  상품금액  </td>
						<td align=left style='padding-left:5px;' colspan=3>
							<table cellpadding=0 cellspacing=0>
							<tr>
								<td style='padding-right:5px;'>
								<INPUT class='textbox' value='$sprice'  autocomplete='off'  clickbool='false' style=' FONT-SIZE: 12px; WIDTH: 130px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=sprice validation=false  title='상품금액'>
								</td>
								<td>~</td>
								<td style='padding-left:5px;'>
								<INPUT class='textbox' value='$eprice'  autocomplete='off'  clickbool='false' style=' FONT-SIZE: 12px; WIDTH: 130px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=eprice validation=false  title='상품금액'>
								</td>
								
							</tr>
							</table>
						</td>
					</tr-->
					<tr height=1><td colspan=4 class='dot-x'></td></tr>
					<tr height=27>
					  <td bgcolor='#efefef' align=center><label for='regdate'>접수일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".CompareReturnValue("1",$regdate,"checked")."></td>
					  <td align=left colspan=5 style='padding-left:5px;'>
						<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>		
							<tr>					
								<TD width=200 nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월 <SELECT name=FromDD></SELECT> 일 </TD>
								<TD width=20 align=center> ~ </TD>
								<TD width=200 nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월 <SELECT name=ToDD></SELECT> 일</TD>
								<TD>
									<a href=\"javascript:select_date('$today','$today',1);\"><img src='../image/b_btn_s_today.gif'></a>
									<a href=\"javascript:select_date('$vyesterday','$vyesterday',1);\"><img src='../image/b_btn_s_yesterday.gif'></a>
									<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../image/b_btn_s_1week01.gif'></a>
									<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../image/b_btn_s_15day01.gif'></a>
									<a href=\"javascript:select_date('$vonemonthago','$today',1);\"><img src='../image/b_btn_s_1month01.gif'></a>
									<a href=\"javascript:select_date('$v2monthago','$today',1);\"><img src='../image/b_btn_s_2month01.gif'></a>
									<a href=\"javascript:select_date('$v3monthago','$today',1);\"><img src='../image/b_btn_s_3month01.gif'></a>
								</TD>
							</tr>		
						</table>	
					  </td>			
					</tr>		    
					<tr height=1><td colspan=4 class='dot-x'></td></tr>
					<tr height=27>
						<td width='15%' bgcolor='#efefef' align=center >  주문TYPE  </td>
						<td align=left style='padding-left:5px;' colspan=3>
							<table cellpadding=0 cellspacing=0>
							<tr>
								<td style='padding-right:5px;'>
									<input type='checkbox' name='mall_ix[]' id='iscream' value='d02b37324dd0b08f6bc0f3847673e7d5' ".CompareReturnValue("d02b37324dd0b08f6bc0f3847673e7d5",$mall_ix,"checked")."> 아이스크림 몰
									  <input type='checkbox' name='mall_ix[]' id='yooung' value='d02b37324dd0b08f6bc0f3847673e7d6' ".CompareReturnValue("d02b37324dd0b08f6bc0f3847673e7d6",$mall_ix,"checked")."> 누리놀이 몰
									  <input type='checkbox' name='mall_ix[]' id='middle' value='d02b37324dd0b08f6bc0f3847673e7d7' ".CompareReturnValue("d02b37324dd0b08f6bc0f3847673e7d7",$mall_ix,"checked")."> 클래스 몰
								</td>
								
							</tr>
							</table>
						</td>
					</tr>
					<tr hegiht=1><td colspan=6 class='dot-x'></td></tr>
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
	<td style='padding:10 20 0 20' colspan=4 align=center><input type=image src='../image/bt_search.gif' align=absmiddle  ></td>		    	
</tr>";

if($mode == "excel"){
	$sql = "select e.est_ix, e.est_type, e.est_title, e.est_charger, e.regdate,e.est_id, e.est_company, e.est_charger, e.est_status, cu.charger 
				from ".TBL_MALLSTORY_ESTIMATES." e LEFT OUTER JOIN ".TBL_MALLSTORY_COMPANY_USERINFO." cu on e.ucode =  cu.charger_id $where order by e.regdate desc ";



	$db->query($sql);
	$goods_infos = $db->fetchall();	
}
if($mode == "excel"){
	include '../include/phpexcel/Classes/PHPExcel.php';
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);
	
	date_default_timezone_set('Asia/Seoul');
	
	$accounts_plan_priceXL = new PHPExcel();
	
	// 속성 정의
	$accounts_plan_priceXL->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("accounts plan price List")
								 ->setSubject("accounts plan price List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("accounts plan price List");
	
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('A' . 1, "번호");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('B' . 1, "접수일자");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('C' . 1, "접수번호");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('D' . 1, "학교명");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('E' . 1, "주문자");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('F' . 1, "견적금액");	
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('G' . 1, "처리상태");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('H' . 1, "운영자");


	$before_pid = "";
	
	
	
	for ($i = 0; $i < count($goods_infos); $i++)
	{	
	
		$db2->query("SELECT sum(totalprice) as totalprice FROM mallstory_estimates_detail WHERE est_ix = '".$goods_infos[$i][est_ix]."' ");
		$db2->fetch();
		
		
		if($goods_infos[$i][est_status] == "0"){
			$est_status_str = "견적대기";
		}else if($goods_infos[$i][est_status] == "1"){
			$est_status_str = "견적완료";
		}else if($goods_infos[$i][est_status] == "2"){
			$est_status_str = "주문완료";
		}else if($goods_infos[$i][est_status] == "3"){
			$est_status_str = "취소";
		}  
	
		$profit = ($goods_infos[$i][delivery_price]-$goods_infos[$i][cast_price]);
		$profit_price = ($profit*$goods_infos[$i][delivery_cnt]);
		if ($profit_price > 0){
			$profit_margin = round(($profit_price/$total_delivery_price)*100, 1);
		}
		
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('A' . ($i + 2), ($i + 1));
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('B' . ($i + 2), $goods_infos[$i][regdate]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('C' . ($i + 2), $goods_infos[$i][est_id]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('D' . ($i + 2), $goods_infos[$i][est_company]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('E' . ($i + 2), $goods_infos[$i][est_charger]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('F' . ($i + 2), number_format($db2->dt[totalprice]));
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('G' . ($i + 2), $est_status_str);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('H' . ($i + 2), $goods_infos[$i][charger]) ;
		

		
	
	}

	// 첫번째 시트 선택
	$accounts_plan_priceXL->setActiveSheetIndex(0);
	
	// 너비조정
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);

	
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="estimate.list.xls"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($accounts_plan_priceXL, 'Excel5');
	$objWriter->save('php://output');

	exit;
}


$search_query = "&max=$max&search_type=$search_type&search_text=$search_text&est_status=$est_status&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD";
$Contents  .= "
<tr>
	<td colapns=4 align=right>
	
			<a href='estimate.list.php?mode=excel".$search_query."'><img src='../image/btn_excel_save.gif' border=0></a>
	
	</td>
</tr>

<tr> 
	<td colspan=3 width='80%'  valign=top id='estimate_product_list' style='padding-top:20px'>".EstimateApplyList($cid, $depth)."</td>
</tr>
<tr> 
	<td width='100%' colspan='2' valign=top>
				
				
	</td>
	
</tr>
<tr> 
	<td bgcolor='D0D0D0' height='1' colspan='4'></td>
</tr>
</table>
<form action='./estimate.product.act.php'>
<input type=hidden name='ecid' value=''>
<input type=hidden name='pid' value=''>
</form>
";

if(!$EstimateBool){
	$EstimateBool = true;
	session_register("EstimateBool");
}

//if(false){
if($view == "innerview"){
	
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>".EstimateApplyList($cid,$depth)."</body></html>";	
	
	echo "
	<Script>
	parent.document.getElementById('estimate_product_list').innerHTML = document.body.innerHTML;
	</Script>";
}else{	
	$P = new LayOut;
	$P->addScript = $Script;
	$P->OnloadFunction = "init();ChangeRegistDate(document.searchmember);";//"ChangeOrderDate(document.search_frm);";
	$P->strLeftMenu = order_menu();
	$P->strContents = $Contents;
	$P->Navigation = "HOME > 주문관리 > 오프라인 견적리스트";
	$P->PrintLayOut();
}


function EstimateApplyList($ecid, $depth){
	global $page, $est_status, $est_type, $regdate,$startDate,$endDate,$search_type,$search_text, $admininfo,$FromYY,$FromMM,$FromDD,$ToYY,$ToMM,$ToDD;
	$db = new Database;
	$db2 = new Database;

	
	$where = " where est_ix is not null ";
	if($search_text != ""){
		$where .= "and $search_type LIKE '%".trim($search_text)."%' ";
	}

	if($est_status !=""){	
		$where .= " and e.est_status =  '".$est_status."' ";
	}

	if($est_type !=""){	
		$where .= " and e.est_type =  '".$est_type."' ";
	}
	if($startDate != "" && $endDate != "" && $regdate == 1){	
		$where .= " and  date_format(e.regdate,'%Y%m%d') between  $startDate and $endDate ";
	}
	$mall_ix = $_GET["mall_ix"];
	if(is_array($mall_ix)){
		for($i=0;$i < count($mall_ix);$i++){


			if($mall_ix[$i]){
				if($type_str == ""){
					$type_str .= "'".$mall_ix[$i]."'";
				}else{
					$type_str .= ", '".$mall_ix[$i]."' ";
				}
			}
		}

		if($type_str != ""){
			$where .= "and e.mall_ix in ($type_str) ";
		}
	}else{
		if($mall_ix){
			$where .= " and e.mall_ix = '".$mall_ix."' ";
		}

	}
	$sql = "select e.est_ix from ".TBL_MALLSTORY_ESTIMATES." e LEFT OUTER JOIN ".TBL_MALLSTORY_COMPANY_USERINFO." cu on e.ucode =  cu.charger_id $where ";

	echo "$sql";exit;
	$db->query($sql);
	$total = $db->total;
	
	$max = 10;
	
	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}
	
	$sql = "select e.est_ix, e.mall_ix, e.est_type, e.est_title, e.est_charger, e.regdate,e.est_id, e.est_company, e.est_charger, e.est_status, cu.charger 
			from ".TBL_MALLSTORY_ESTIMATES." e LEFT OUTER JOIN ".TBL_MALLSTORY_COMPANY_USERINFO." cu on e.ucode =  cu.charger_id $where order by e.regdate desc limit $start , $max";
	//echo $sql;
	$db->query($sql);
	
	$mString = "<table cellpadding=0 cellspacing=0 width=100% bgcolor=silver style='font-size:10px;'>";	
	$mString .= "<tr align=center bgcolor=#efefef height=25>
			<td width=5% class=s_td>번호</td>
			<td width=12% class=m_td>접수일자/접수번호</td>
			<td width=8% class=m_td>주문TYPE</td>
			<td width=15% class=m_td>학교명</td>
			<td width=10% class=m_td >주문자</td>
			<td width=10% class=m_td>견적금액</td>
			<td width=10% class=m_td>처리상태</td>
			<td width=10% class=m_td>운영자</td>
			<td width=* class=e_td>관리</td>
			".($admininfo[charger_id] == "forbiz" ? "<td width=5% class=e_td>삭제</td>" : "")."
			</tr>";

	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=10 align=center>등록된 견적 정보가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			
			$db2->query("SELECT sum(totalprice) as totalprice FROM mallstory_estimates_detail WHERE est_ix = '".$db->dt[est_ix]."' ");
			$db2->fetch();
			
			if($db->dt[est_type] == "c"){
				$est_type_str = "맞춤견적";
			}else if($db->dt[est_type] == "q"){
				$est_type_str = "빠른견적";
			}else if($db->dt[est_type] == "s"){
				$est_type_str = "시스템견적";
			}else if($db->dt[est_type] == "i"){
				$est_type_str = "오프라인";
			}   
			
			if($db->dt[est_status] == "0"){
				$est_status_str = "<span style='color:#f59db3;'>견적대기</span>";
			}else if($db->dt[est_status] == "1"){
				$est_status_str = "견적완료";
			}else if($db->dt[est_status] == "2"){
				$est_status_str = "주문완료";
			}else if($db->dt[est_status] == "3"){
				$est_status_str = "취소";
			}   
			
			if($db->dt["mall_ix"] == "d02b37324dd0b08f6bc0f3847673e7d5"){
				$mall_ix = "아이스크림몰";
			}else if($db->dt["mall_ix"] == "d02b37324dd0b08f6bc0f3847673e7d6"){
				$mall_ix = "누리놀이몰";
			}else if($db->dt["mall_ix"] == "d02b37324dd0b08f6bc0f3847673e7d7"){
				$mall_ix = "클래스몰";
			}else{
				$mall_ix = "기타";
			}
			$db->dt[regdate] = substr($db->dt[regdate], 0, 10);
			
			$no = $total - ($page - 1) * $max - $i;
			
			$mString .= "<tr height=25 bgcolor=#ffffff>
				<td class=table_td_white align=center style='font-size:11px;'>".$no."</td>
				<td class=table_td_white align=center style='font-size:11px;' style='padding:5px 0;'>".$db->dt[regdate]."<br><a href='estimate.intra.php?est_ix=".$db->dt[est_ix]."&mode=et_update' style='color:#007DB7;font-weight:bold;'>".$db->dt[est_id]."</a></td>
				<td class=table_td_white align=center style='font-size:11px;'>".$mall_ix."</td>
				<td class=table_td_white align=center style='font-size:11px;'><!--a href='estimate.detail.php?est_ix=".$db->dt[est_ix]."'-->".$db->dt[est_company]."</a></td>
				<td class=table_td_white align=center style='font-size:11px;'><a href='estimate.intra.php?est_ix=".$db->dt[est_ix]."&mode=et_update'>".$db->dt[est_charger]."</a></td>
				<td class=table_td_white align=center style='font-size:11px;'>".number_format($db2->dt[totalprice])."원</td>
				<td class=table_td_white align=center style='font-size:11px;'>".$est_status_str."</td>
				<td class=table_td_white align=center style='font-size:11px;'>".$db->dt[charger]."</td>
				<td class=table_td_white align=center style='font-size:11px;'>
				<input type=image src='../images/estimate.print.gif' alt='견적서출력' onClick=\"PopSWindow('/popup/es.php?est_ix=".$db->dt[est_ix]."&order_type=estimate_admin',950,500,'member_info')\">
				<img src='../images/estimate.order.gif'style='cursor:pointer;' alt='주문하기' onclick=\"document.location.href='./estimate.cart.php?est_ix=".$db->dt[est_ix]."&mall_ix=".$db->dt['mall_ix']."'\">
				<input type='button' value='취소' name='cancel' id='cancel' style='vertical-align:top;' onclick=\"ChangeStatus('".$db->dt[est_ix]."','3');\">
				</td>
				".($admininfo[charger_id] == "forbiz" ? "<td class=table_td_white align=center style='font-size:11px;'>
				<a href=\"JavaScript:deleteEstimate('delete','".$db->dt[est_ix]."')\"><img src='../image/btc_del.gif' border=0></a>
				</td>" : "")."</tr>";
			$mString .= "<tr height=1><td colspan=10 class='dot-x'></td></tr>";
		}
	}
	
	
	$search_query = "&max=$max&search_type=$search_type&search_text=$search_text&est_status=$est_status&regdate=$regdate&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD";
	
	$mString .= "<tr height=50 bgcolor=#ffffff><td colspan=10 align=left>".page_bar($total, $page, $max, $search_query ,"")."</td></tr>";
	$mString .= "</table>";
	
	return $mString;
}
?>
