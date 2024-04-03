<?
include("../class/layout.class");
include("inventory.lib.php");
//include_once($_SERVER["DOCUMENT_ROOT"]."/include/sns.config.php");

$db = new Database;
if ($FromYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-10, date("Y"));

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
	$search_startDate = mktime(0,0,0,date("m"),date("d")-10,date("Y"));
	$search_endDate = mktime(23,59,59,date("m"),date("d"),date("Y"));
}else{
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;
	$search_startDate = mktime(0,0,0,$FromMM,$FromDD,$FromYY);
	$search_endDate = mktime(23,59,59,$ToMM,$ToDD,$ToYY);
	//echo $search_endDate;
}


if(!$ptype || $ptype=="") $ptype=1;

if($ptype==1) {
	$tab_on_txt1="class='on'";
	$tab_on_txt2="";
	$arr_product_type=$shop_product_type;
} else {
	$tab_on_txt1="";
	$tab_on_txt2="class='on'";
	$arr_product_type=$sns_product_type;
}
$product_type_txt=implode(",",$arr_product_type);

$Script = "	<script language='javascript'>

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
	onLoad('$sDate','$eDate');
/*	if(!frm.regdate.checked){
		frm.FromYY.disabled = true;
		frm.FromMM.disabled = true;
		frm.FromDD.disabled = true;
		frm.ToYY.disabled = true;
		frm.ToMM.disabled = true;
		frm.ToDD.disabled = true;
	}*/
	


}

function init2(){

	var frm = document.searchmember;
	onLoad('$sDate','$eDate');


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




function onLoad(FromDate, ToDate) {
	var frm = document.searchmember;

	LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
	LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);

	init_date(FromDate,ToDate);

}
		</script>";
/*
<tr>
	    <td align='left' colspan=4 > ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 사업자 정보</b></div>")."</td>
	  </tr>
*/

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
$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
			<tr>
				<td align='left' colspan=6 > ".GetTitleNavigation("집계요약", "재고관리 > 집계표 > 집계요약")."</td>
			</tr>
			<tr>
				<td align='left' colspan=8 style='padding-bottom:14px;'>
					<div class='tab'>
						<table class='s_org_tab'>
							<tr>
								<td class='tab'>
									<table id='tab_00'  ".($summary_type == "" ? "class='on'":"").">
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"document.location.href='?summary_type='\">발주집계</td>
											<th class='box_03'></th>
										</tr>
									</table>
									<table id='tab_01'  ".($summary_type == "stock" ? "class='on'":"").">
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"document.location.href='?summary_type=stock'\">구매집계</td>
											<th class='box_03'></th>
										</tr>
									</table>
								</td>
								<td align='right' style='text-align:right;vertical-align:bottom;padding:0 0 6px 4px;'>";
									$mstring .= "
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<td>
				<form name='searchmember'>
    <table border='0' cellpadding='0' cellspacing='0' width='100%'>
		<tr>
		<td style='width:100%;' valign=top colspan=3>
			<table width=100%  border=0 cellpadding='0' cellspacing='0'>
				<!--tr height=25><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>사용후기 검색하기</b></td></tr-->
				<tr>
					<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>
						<table class='box_shadow' style='width:100%;' align=left cellpadding='0' cellspacing='0' border='0'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02'></td>
								<th class='box_03'></th>
							</tr>
							<tr>
								<th class='box_04'></th>
								<td class='box_05' valign=top>
									<TABLE cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0>
									<TR>
										<TD bgColor=#ffffff style='padding:0 0 0 0;'>
										<table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
											<col width='15%'>
											<col width='35%'>
											<col width='15%'>
											<col width='35%'>
											";
											if ($summary_type == 'stock'){
											$mstring .= "
											<tr height='27'>
												<th class='search_box_title' bgcolor='#efefef' width='150' align=center>조건검색 : </th>
												<td class='search_box_item' colspan=3>
													<table cellpaddig='0' cellspacing='0' border='0' width='100%'>
														<tr>
															<td width='90' >
															<select name=search_type>
																<option value='order_charger' ".CompareReturnValue("order_charger",$search_type,"selected").">담당자</option>
															</select>
															</td>
															<td width='*'><input type=text class=textbox name='search_text' value='".$search_text."' style='width:200px;height:18px;' ></td>
															
														</tr>
													</table>
												</td>
											</tr>
											 <tr height=27>
												<td class='search_box_title' bgcolor='#efefef' align=center><label for='regdate'><b>입고일자</b></label></td>
											  <td class='search_box_item' align=left style='padding-left:5px;' colspan=3>
												<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff >
												<tr>
													<TD  nowrap>
														<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY style='width:65px;'></SELECT> 년 
														<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM style='width:45px;'></SELECT> 월 
														<SELECT name=FromDD style='width:45px;'></SELECT> 일 
													</TD>
													<TD style='padding:0 5px;' align=left> ~ </TD>
													<TD nowrap>
														<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY style='width:65px;'></SELECT> 년 
														<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM style='width:45px;'></SELECT> 월 
														<SELECT name=ToDD style='width:45px;'></SELECT> 일
													</TD>
													<TD style='padding-left:10px;'>
														<a href=\"javascript:select_date('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
															<a href=\"javascript:select_date('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
															<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
															<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
															<a href=\"javascript:select_date('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
															<a href=\"javascript:select_date('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
															<a href=\"javascript:select_date('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
													</TD>
												</tr>";
												}else{
												$mstring .= "
												<tr height='27'>
													<th class='search_box_title' bgcolor='#efefef' width='150' align=center>조건검색 : </th>
													<td class='search_box_item' colspan=3>
														<table cellpaddig='0' cellspacing='0' border='0' width='100%'>
															<tr>
																<td width='90' >
																<select name=search_type>
																	<option value='order_charger' ".CompareReturnValue("order_charger",$search_type,"selected").">담당자</option>
																</select>
																</td>
																<td width='*'><input type=text class=textbox name='search_text' value='".$search_text."' style='width:200px;height:18px;' ></td>
																
															</tr>
														</table>
													</td>
												</tr>
												<tr height=27>
												<td class='search_box_title' bgcolor='#efefef' align=center><label for='regdate'><b>발주일자</b></label></td>
											  <td class='search_box_item' align=left style='padding-left:5px;' colspan=3>
												<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff >
												<tr>
													<TD  nowrap>
														<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY style='width:65px;'></SELECT> 년 
														<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM style='width:45px;'></SELECT> 월 
														<SELECT name=FromDD style='width:45px;'></SELECT> 일 
													</TD>
													<TD style='padding:0 5px;' align=left> ~ </TD>
													<TD nowrap>
														<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY style='width:65px;'></SELECT> 년 
														<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM style='width:45px;'></SELECT> 월 
														<SELECT name=ToDD style='width:45px;'></SELECT> 일
													</TD>
													<TD style='padding-left:10px;'>
														<a href=\"javascript:select_date('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
															<a href=\"javascript:select_date('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
															<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
															<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
															<a href=\"javascript:select_date('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
															<a href=\"javascript:select_date('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
															<a href=\"javascript:select_date('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
													</TD>
												</tr>";
												}
												$mstring .= "
											</table>
											  </td>
											</tr>
										</table>
										</TD>
									</TR>

									</TABLE>
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
				<tr >
					<td colspan=3 align=center  style='padding:10px 0 20px 0'>
						<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	</table>
	</form>
	</td>
	</tr>
	
	<tr>
		<td style='clear:both;'>
		".($summary_type == "stock" ? "".PrintInputSummry()."" : "".PrintOrderSummry()."")."
		
		</td>
	</tr>
	</table>";

$Contents = $mstring;

//$Contents .= HelpBox("사용후기 관리", $help_text);

//$Script = "<script language='javascript' src='basicinfo.js'></script>";
$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->strLeftMenu = inventory_menu();
$P->Navigation = "재고관리 > 집계표 > 집계요약";
$P->title = "집계요약";
$P->strContents = $Contents;
echo $P->PrintLayOut();

function PrintOrderSummry(){
	global $admininfo, $admin_config, $DOCUMENT_ROOT,$nset,$page,$uf_valuation,$search_type,$search_text,$_GET,$product_type_txt;
	global $inventory_order_status;
	global $startDate, $endDate,$search_startDate,$search_endDate; 

	$mdb = new Database;

	$where = " where 1=1";


	if($search_type != "" && $search_text != ""){
		$where .= " and io.".$search_type." LIKE '%$search_text%' ";
	}

	/*$startDate = $_GET["FromYY"].$_GET["FromMM"].$_GET["FromDD"];
	$endDate = $_GET["ToYY"].$_GET["ToMM"].$_GET["ToDD"];
*/
	if($startDate != "" && $endDate != ""){
		$where .= " and  unix_timestamp(io.regdate) between  $search_startDate and $search_endDate ";
	}

	

	if($admininfo[admin_level] == 9){
		$sql = "select COUNT(*) from inventory_order as io  
					left join inventory_order_detail as iod 
					on io.ioid = iod.ioid  $where ";
	}else{
		$sql = "select COUNT(*) from inventory_order as io  
					left join inventory_order_detail as iod 
					on io.ioid = iod.ioid  $where and company_id = '".$admininfo["company_id"]."' ";
	}
	//echo $sql."<br>";
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[0];
	$max = 30;
	//echo $total;
	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$mString ="<form name='form5' action='useafter.act.php' method='post' target='act'><input type='hidden' name='act' value='update'>";
	$mString .= "<table cellpadding=0 cellspacing=0 width=100% bgcolor=silver border='0' style='padding:0px;margin:0px;' class='list_table_box'>";
	$mString .= "<tr bgcolor=#efefef style='font-weight:600;'>
		<td class=s_td width='20%' height=27 align='center'>담당자</td>
		<td class=m_td width='20%' align='center'>수량</td>
		<td class='m_td' width='20%' align='center'>발주가</td>
		<td class='m_td' width='20%' align='center'>부가세</td>
		<td class=e_td width='20%' align='center'>합계</td>
		
		</tr>
		";
	if ($total == 0){
		if($admininfo[admin_level] == 9){
			$mString .= "<tr bgcolor=#ffffff height=50><td class='list_box_td' colspan=5 align=center>집계 내역이 존재 하지 않습니다.</td></tr>";
		}else{
			$mString .= "<tr bgcolor=#ffffff height=50><td class='list_box_td' colspan=5 align=center>집계 내역이 존재 하지 않습니다.</td></tr>";
		}
	}else{
		if($admininfo[admin_level] == 9){
			$sql = "select io.order_charger,io.charger_ix, sum(iod.order_cnt) as cnt, sum(io.total_price) as total_price , sum(io.total_add_price) as total_add_price , iod.detail_status
					from inventory_order as io  
					left join inventory_order_detail as iod 
					on io.ioid = iod.ioid 
					$where group by io.charger_ix  order by  io.regdate desc ";//limit $start , $max, having iod.detail_status='WC'
		}else{
			$sql = "select io.order_charger,io.charger_ix, sum(iod.order_cnt) as cnt, sum(io.total_price) as total_price , sum(io.total_add_price) as total_add_price, iod.detail_status   
					from inventory_order as io 
					left join inventory_order_detail as iod 
					on io.ioid = iod.ioid   
					$where group by io.charger_ix and io.company_id = '".$admininfo["company_id"]."' order by  io.regdate desc  ";//limit $start , $max
		}
		//echo $sql."<br>";
		$mdb->query($sql);

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);

			$no = $total - ($page - 1) * $max - $i;
			$sum_cnt += $mdb->dt[cnt];
			$sum_order += $mdb->dt[total_price];
			$sum_order_add += $mdb->dt[total_add_price];
			$sum_total_order += ($mdb->dt[total_price]+$mdb->dt[total_add_price]);
			

			$mString .= "<tr bgcolor=#ffffff align=center height='50'>
			<input type='hidden' name='ioid[]' value='".$mdb->dt[ioid]."'>
			<td class='list_box_td list_bg_gray' >".$mdb->dt[order_charger]."</td>
			<td class='list_box_td ' >".$mdb->dt[cnt]."</td>
			<td class='list_box_td list_bg_gray' >".number_format($mdb->dt[total_price])."</td>
			<td class='list_box_td ' >".number_format($mdb->dt[total_add_price])."</td>
			<td class='list_box_td point' > ".number_format($mdb->dt[total_price]+$mdb->dt[total_add_price])."</td>
			
			
			 ";
			$mString .= "
			</td>
			</tr>
			";
		}
			$mString .= "<tr height=30>
					<td class='list_box_td list_bg_gray' ><b>합계</b></td>					
					<td class='list_box_td ' >".$sum_cnt."</td>
					<td class='list_box_td list_bg_gray' >".number_format($sum_order)."</td>
					<td class='list_box_td ' >".number_format($sum_order_add)."</td>
					<td class='list_box_td point ' >".number_format($sum_total_order)."</td>
					
					</tr>
					";

	}
/*
	if($_SERVER[QUERY_STRING] == "nset=$nset&page=$page"){
		$query_string = str_replace("nset=$nset&page=$page","",$_SERVER[QUERY_STRING]) ;
	}else{
		$query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER[QUERY_STRING]) ;
	}
*/
	$mString .= "
					</table>
					<table cellpadding=0 cellspacing=0 width=100%>
					
					<!--tr height=30 bgcolor=#ffffff>
						<td colspan=6 align=right><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer;'></td>
					</tr-->
					</table>
					</form>";

	return $mString;
}
function PrintInputSummry(){
	global $admininfo, $admin_config, $DOCUMENT_ROOT,$nset,$page,$uf_valuation,$search_type,$search_text,$_GET,$product_type_txt;
	global $inventory_order_status;
	global $startDate, $endDate,$search_startDate,$search_endDate; 

	$mdb = new Database;

	$where = " where 1=1";


	if($search_type != "" && $search_text != ""){
		$where .= " and io.".$search_type." LIKE '%$search_text%' ";
	}

	/*$startDate = $_GET["FromYY"].$_GET["FromMM"].$_GET["FromDD"];
	$endDate = $_GET["ToYY"].$_GET["ToMM"].$_GET["ToDD"];
*/
	if($startDate != "" && $endDate != ""){
		$where .= " and  unix_timestamp(io.regdate) between  $search_startDate and $search_endDate ";
	}

	

	if($admininfo[admin_level] == 9){
		$sql = "select COUNT(*) from inventory_order as io  
					left join inventory_order_detail as iod 
					on io.ioid = iod.ioid  $where ";
	}else{
		$sql = "select COUNT(*) from inventory_order as io  
					left join inventory_order_detail as iod 
					on io.ioid = iod.ioid  $where and company_id = '".$admininfo["company_id"]."' ";
	}
	//echo $sql."<br>";
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[0];
	$max = 30;
	//echo $total;
	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$mString ="<form name='form5' action='useafter.act.php' method='post' target='act'><input type='hidden' name='act' value='update'>";
	$mString .= "<table cellpadding=0 cellspacing=0 width=100% bgcolor=silver border='0' style='padding:0px;margin:0px;' class='list_table_box'>";
	$mString .= "<tr bgcolor=#efefef style='font-weight:600;'>
		<td class=s_td width='20%' height=27 align='center'>입고담당자</td>
		<td class=m_td width='20%' align='center'>수량</td>
		<td class='m_td' width='20%' align='center'>입고가</td>
		<td class='m_td' width='20%' align='center'>부가세</td>
		<td class=e_td width='20%' align='center'>합계</td>
		
		</tr>
		";
	if ($total == 0){
		if($admininfo[admin_level] == 9){
			$mString .= "<tr bgcolor=#ffffff height=50><td class='list_box_td' colspan=5 align=center>집계 내역이 존재 하지 않습니다.</td></tr>";
		}else{
			$mString .= "<tr bgcolor=#ffffff height=50><td class='list_box_td' colspan=5 align=center>집계 내역이 존재 하지 않습니다.</td></tr>";
		}
	}else{
		if($admininfo[admin_level] == 9){
			$sql = "select io.order_charger,iod.in_charger_ix, sum(iod.order_cnt) as cnt, sum(io.total_price) as total_price , sum(io.total_add_price) as total_add_price , iod.detail_status ,iod.in_charger_name 
					from inventory_order as io  
					left join inventory_order_detail as iod 
					on io.ioid = iod.ioid 
					$where group by iod.in_charger_ix  having iod.detail_status='WC' order by  io.regdate desc ";//limit $start , $max,
		}else{
			$sql = "select io.order_charger,iod.in_charger_ix, sum(iod.order_cnt) as cnt, sum(io.total_price) as total_price , sum(io.total_add_price) as total_add_price, iod.detail_status ,iod.in_charger_name   
					from inventory_order as io 
					left join inventory_order_detail as iod 
					on io.ioid = iod.ioid   
					$where group by iod.in_charger_ix and io.company_id = '".$admininfo["company_id"]."' order by  io.regdate desc  ";//limit $start , $max
		}
		//echo $sql."<br>";
		$mdb->query($sql);

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);

			$no = $total - ($page - 1) * $max - $i;
			$sum_cnt += $mdb->dt[cnt];
			$sum_order += $mdb->dt[total_price];
			$sum_order_add += $mdb->dt[total_add_price];
			$sum_total_order += ($mdb->dt[total_price]+$mdb->dt[total_add_price]);
			

			$mString .= "<tr bgcolor=#ffffff align=center height='50'>
			<input type='hidden' name='ioid[]' value='".$mdb->dt[ioid]."'>
			<td class='list_box_td list_bg_gray' >".$mdb->dt[in_charger_name]."</td>
			<td class='list_box_td ' >".$mdb->dt[cnt]."</td>
			<td class='list_box_td list_bg_gray' >".number_format($mdb->dt[total_price])."</td>
			<td class='list_box_td ' >".number_format($mdb->dt[total_add_price])."</td>
			<td class='list_box_td point' > ".number_format($mdb->dt[total_price]+$mdb->dt[total_add_price])."</td>
			
			
			 ";
			$mString .= "
			</td>
			</tr>
			";
		}
			$mString .= "<tr height=30>
					<td class='list_box_td list_bg_gray' ><b>합계</b></td>					
					<td class='list_box_td ' >".$sum_cnt."</td>
					<td class='list_box_td list_bg_gray' >".number_format($sum_order)."</td>
					<td class='list_box_td ' >".number_format($sum_order_add)."</td>
					<td class='list_box_td point ' >".number_format($sum_total_order)."</td>
					
					</tr>
					";

	}
/*
	if($_SERVER[QUERY_STRING] == "nset=$nset&page=$page"){
		$query_string = str_replace("nset=$nset&page=$page","",$_SERVER[QUERY_STRING]) ;
	}else{
		$query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER[QUERY_STRING]) ;
	}
*/
	$mString .= "
					</table>
					<table cellpadding=0 cellspacing=0 width=100%>
					
					<!--tr height=30 bgcolor=#ffffff>
						<td colspan=6 align=right><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer;'></td>
					</tr-->
					</table>
					</form>";

	return $mString;
}


?>
