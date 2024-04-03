<?
include("../class/layout.class");

if(!$title_str){
	$title_str = '견적 리스트';
}

if($nationality != ""){
	//$search_str .= " and sd.nationality = '".$nationality."' ";
}

if($sdate != "" && $edate != ""){

	$sdate = str_replace("/","",$sdate);
	$edate = str_replace("/","",$edate);
	//$search_str .= " and  MID(replace(regdate ,'-',''),1,8) between  $sdate and $edate ";
	$search_str = " and date_format(regdate,'%Y-%m-%d') between  '".$sdate."' and '".$edate."' ";
	
}

if ($search_type){
	
	if($search_text){

		$search_str .= " and ".$search_type." LIKE  '%$search_text%' ";

	}
}

if(count($estimate_type) > 0){
	$search_str .= " and estimate_type in (";
	for($i = 0; $i<count($estimate_type); $i++){
		if($i == count($estimate_type) -1){
		$search_str .= "'".$estimate_type[$i]."'";
		}else{
		$search_str .= "'".$estimate_type[$i]."',";
		}
	}
	$search_str .= " )";
}

if(count($estimate_div) > 0){
	$search_str .= " and estimate_div in (";
	for($i = 0; $i<count($estimate_div); $i++){
		if($i == count($estimate_div) -1){
		$search_str .= "'".$estimate_div[$i]."'";
		}else{
		$search_str .= "'".$estimate_div[$i]."',";
		}
	}
	$search_str .= " )";
}

if(count($estimate_status) > 0){
	$search_str .= " and status in (";
	for($i = 0; $i<count($estimate_status); $i++){
		if($i == count($estimate_status) -1){
		$search_str .= "'".$estimate_status[$i]."'";
		}else{
		$search_str .= "'".$estimate_status[$i]."',";
		}
	}
	$search_str .= " )";
}


$Script = "
<script language='JavaScript' src='/admin/js/dd.js'></Script>

<Script Language='JavaScript'>
function setCategory(cname,cid,depth,id){
	//document.location.href='estimate.product.php?view=innerview&cid='+cid+'&depth='+depth;
	window.frames['act'].location.href='estimate.product.php?view=innerview&cid='+cid+'&depth='+depth;
}

function deleteEstimate(act, est_ix,type){
	window.frames['act'].location.href='estimate.act.php?act='+act+'&est_ix='+est_ix+'&type='+type;
}

function ChangeStatus(est_ix, status){
	if(confirm('정말로 상태변경을 하시겠습니까?')){
		//document.location.href='estimate.act.php?act=status_update&est_ix='+est_ix+'&status='+status;
		window.frames['act'].location.href='estimate.act.php?act=status_update&est_ix='+est_ix+'&status='+status;
	}
}

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

$vdate = date("Ymd", time());
$today = date("Y-m-d", time());
$vyesterday = date("Y-m-d", time()+84600);
$voneweeklater = date("Y-m-d", time()-84600*7);
$vtwoweeklater = date("Y-m-d", time()-84600*14);
$vfourweeklater = date("Y-m-d", time()-84600*28);
$vyesterday = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24);
//$voneweeklater = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*7);
$v15later = date("Y-m-d", time()-84600*15);;//date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*15);
//$vfourweeklater = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*28);
$vonemonthlater = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)-1,substr($vdate,0,4)));
$v2monthlater = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)-1,substr($vdate,0,4)));
$v3monthlater = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)-1,substr($vdate,0,4)));

if(is_array($estimate_type)){
	if(in_array(1,$estimate_type)) $estimate_type_1 = 'checked';
	if(in_array(2,$estimate_type)) $estimate_type_2 = 'checked';
	if(in_array(3,$estimate_type)) $estimate_type_3 = 'checked';
	if(in_array(4,$estimate_type)) $estimate_type_4 = 'checked';
}

if(is_array($estimate_div)){
	if(in_array(1,$estimate_div)) $estimate_div_1 = 'checked';
	if(in_array(2,$estimate_div)) $estimate_div_2 = 'checked';
}

if(is_array($estimate_status)){
	if(in_array(1,$estimate_status)) $estimate_status_1 = 'checked';
	if(in_array(2,$estimate_status)) $estimate_status_2 = 'checked';
	if(in_array(3,$estimate_status)) $estimate_status_3 = 'checked';
	if(in_array(4,$estimate_status)) $estimate_status_4 = 'checked';
	if(in_array(5,$estimate_status)) $estimate_status_5 = 'checked';
	if(in_array(7,$estimate_status)) $estimate_status_7 = 'checked';
}


$Contents ="
<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' valign=top>
<tr>
	<td align='left' colspan=6 > ".GetTitleNavigation($title_str, "견적센터 > 견적현황 > $title_str ")."</td>
</tr>
<tr>
				<td align='left' colspan=8 style='padding-bottom:14px;'>
					<div class='tab'>
						<table class='s_org_tab'>
							<tr>
								<td class='tab'>
									<table id='tab_00'  ".($subul_type == "" ? "class='on'":"").">
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"document.location.href='?subul_type='\">".$title_str."</td>
											<th class='box_03'></th>
										</tr>
									</table>
									<!--<table id='tab_01'  ".($subul_type == "date" ? "class='on'":"").">
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"document.location.href='?subul_type=date'\">상품별 리스트</td>
											<th class='box_03'></th>
										</tr>
									</table>-->
								</td>
								<td align='right' style='text-align:right;vertical-align:bottom;padding:0 0 6px 4px;'>";
									$Contents .= "
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<tr>			 
				<td colspan=2>
				<form name='search_frm' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' ><!--target='act'><input type='hidden' name='view' value='innerview'-->
				 <input type='hidden' name='mode' value='search'>
				 <input type='hidden' name='subul_type' value='$subul_type'>
					<table class='box_shadow' style='width:100%;height:20px' cellpadding='0' cellspacing='0'><!---mbox04-->
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05 align=center' style='padding:10'>
								<table cellpadding=0 cellspacing=0 border=0 width=100% class='input_table_box' >";

$Contents .=	"				<col width=20%>
									<col width=30%>
									<col width=20%>
									<col width=30%>
									<!--tr>
										<td class='input_box_title'> <label for='regdate'>출고일자 </label> </td>
										<td class='input_box_item' colspan=3>
											<table border=0 cellpadding=0 cellspacing=0>
												<TD nowrap>
												<SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromYY></SELECT> 년
												<SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromMM></SELECT> 월 <SELECT name=vFromDD></SELECT> 일 </TD>
												<TD width=10 align=center> ~ </TD>
												<TD nowrap>
												<SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToYY></SELECT> 년
												<SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToMM></SELECT> 월
												<SELECT name=vToDD></SELECT> 일</TD>
											</table>
										</td>
									</tr-->
									<tr height=27>
									  <td class='search_box_title'><b>등록일자</b></td>
									  <td class='search_box_item' colspan=3 >
										".search_date('sdate','edate',$sDate,$eDate)."
									  </td>
									</tr>
									<tr height='30' id='move_status_tr'>
										<td class='input_box_title'>견적서 분류 </td>
										<td class='input_box_item' colspan=3>
											<!--<input type=checkbox name='estimate_type[]' id='estimate_type_1' value='1' ".$estimate_type_1."><label for='estimate_type_1'>맞춤 견적서</label>
											<input type=checkbox name='estimate_type[]' id='estimate_type_2' value='2' ".$estimate_type_2."><label for='estimate_type_2'>선택 견적서</label>-->
											<input type=checkbox name='estimate_type[]' id='estimate_type_3' value='3' ".$estimate_type_3."><label for='estimate_type_3'>전문가 견적서</label>
											<input type=checkbox name='estimate_type[]' id='estimate_type_4' value='4' ".$estimate_type_4."><label for='estimate_type_4'>자유 견적서</label>
										</td>
									</tr>
									<tr height='30' id='move_status_tr'>
										<td class='input_box_title'>견적서 유형 </td>
										<td class='input_box_item' colspan=3>
											<!--<input type=checkbox name='estimate_div[]' id='estimate_div_1' value='1' ".$estimate_div_1."><label for='estimate_div_1'>창업문의</label>-->
											<input type=checkbox name='estimate_div[]' id='estimate_div_2' value='2' ".$estimate_div_2." checked><label for='estimate_div_2'>대량구매문의</label>
										</td>
									</tr>
									<tr height='30' id='move_status_tr'>
										<td class='input_box_title'>처리상태</td>
										<td class='input_box_item' colspan=3>
											<!--<input type=checkbox name='estimate_status[]' id='estimate_status_1' value='1' ".$estimate_status_1."><label for='estimate_status_1'>전체</label>-->
											<input type=checkbox name='estimate_status[]' id='estimate_status_2' value='2' ".$estimate_status_2."><label for='estimate_status_2'>견적대기</label>
											<input type=checkbox name='estimate_status[]' id='estimate_status_3' value='3' ".$estimate_status_3."><label for='estimate_status_3'>견적취소</label>
											<input type=checkbox name='estimate_status[]' id='estimate_status_4' value='4' ".$estimate_status_4."><label for='estimate_status_4'>견적진행중</label>
											<input type=checkbox name='estimate_status[]' id='estimate_status_5' value='5' ".$estimate_status_5."><label for='estimate_status_5'>견적기간만료</label>
											<input type=checkbox name='estimate_status[]' id='estimate_status_7' value='7' ".$estimate_status_7."><label for='estimate_status_7'>견적확정</label>
										</td>
									</tr>
									</tr>
										<td class='input_box_title'> 검색어  </td>
										<td class='input_box_item'  align=left  style='padding-right:5px;padding-top:2px;'>
											<table cellpadding=0 cellspacing=0>
											<tr>
												<td>
													<select name='search_type' id='search_type'  style=\"font-size:12px;height:22px;\">
													<option value='estimate_title'>견적제목</option>
													<option value='estimate_code'>견적번호</option>
													</select>
												</td>
												<td style='padding-left:3px;'>
												<INPUT id=search_texts class='textbox' value=''  style=' FONT-SIZE: 12px; WIDTH: 260px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text autocomplete='off' validation=false  title='검색어'><!--onFocusOut='clearNames()'--><br>

												</td>
											</tr>
											</table>
										</td>
										<td class='input_box_title'><b>목록갯수</b></td>
										<td class='input_box_item' >
											<select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle>
												<option value='5' ".CompareReturnValue(5,$max).">5</option>
												<option value='10' ".CompareReturnValue(10,$max).">10</option>
												<option value='20' ".CompareReturnValue(20,$max).">20</option>
												<option value='50' ".CompareReturnValue(50,$max).">50</option>
												<option value='100' ".CompareReturnValue(100,$max).">100</option>
											</select> <span class='small'>한페이지에 보여질 갯수를 선택해주세요</span>
										</td>
									</tr>
								</table>
							</td>
							<th class='box_06'></th>
						</tr>
						<tr ><td  colspan=2 align=center style='padding:20px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle> <!--btn_inquiry.gif--></td></tr>
						<tr>
							<th class='box_07'></th>
							<td class='box_08'></td>
							<th class='box_09'></th>
						</tr>
					</table>
					</form>

				</td>
			</tr>
			<tr>
				<td colspan=3 width='80%'  valign=top id='estimate_product_list'>".EstimateApplyList($cid, $depth,$search_str)."</td>
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


//if(false){
if($view == "innerview"){

	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>".EstimateApplyList($cid,$depth,$search_str)."</body></html>";

	echo "
	<Script>
	parent.document.getElementById('estimate_product_list').innerHTML = document.body.innerHTML;
	</Script>";
}else{
	$P = new LayOut;
	$P->addScript = $Script;
	$P->strLeftMenu = estimate_menu();
	$P->strContents = $Contents;
	$P->Navigation = "견적센타 > 견적현황 > $title_str ";
	$P->title = $title_str;
	$P->PrintLayOut();
}


function EstimateApplyList($ecid, $depth,$search_str=""){
	global $page, $admininfo;

	$db = new Database;
	$mdb = new Database;
	$cdb = new Database;

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	if($depth == 'order'){

		$sql = "select 
						e.est_ix 
				from 
					".TBL_SHOP_ESTIMATES." as e
					inner join ".TBL_SHOP_ORDER." as o on (e.est_ix = o.est_ix)
				where 
					1 
					$where
					$search_str";
					
		$db->query($sql);
		$total = $db->total;

		$sql = "select *,cu.id as buser_id
				from 
					".TBL_SHOP_ESTIMATES." as e
					inner join ".TBL_SHOP_ORDER." as o on (e.est_ix = o.est_ix)
					inner join ".TBL_COMMON_USER." as cu on (o.user_code = cu.code)
				where 1
					$where
					$search_str
					order by e.regdate desc limit $start , $max";
		$db->query($sql);

	}else{

		if($depth){
			$where = " and status = '".$depth."'";
		}

		$sql = "select est_ix from ".TBL_SHOP_ESTIMATES." where 1 $where $search_str";
		$db->query($sql);
		$total = $db->total;

		$sql = "select *
				from 
					".TBL_SHOP_ESTIMATES." 
				where 1
					$where
					$search_str
					and estimate_type != '1'
					order by regdate desc limit $start , $max";

		$db->query($sql);
	}
	
	$mString .= "
		<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box' style='border-bottem:0px;'>
		<colgroup>
		<col width='15px'>
		<col width='8%'>
		<col width='15%'>
		<col width='11%'>
		<col width='11%'>
		<col width='11%'>
		<col width='11%'>
		<col width='10%'>
		<col width='15%'>
		<col width='6%'>
		</colgroup><tbody><tr height='25'>
		<td align='center' class='s_td' style='background-color:#fff7da;'><input type='checkbox' name='all_fix' onclick='fixAll(document.listform)'></td>
		<td align='center' class='m_td' style='background-color:#fff7da;'><font color='#000000' class='small'><b>판매처</b></font></td>
		<td align='center' class='m_td' style='background-color:#fff7da;' nowrap=''><font color='#000000' class='small'><b>견적의뢰일</b></font></td>
		<td align='center' class='m_td' style='background-color:#fff7da;' nowrap=''><font color='#000000' class='small'><b>견적번호</b></font></td>
		<td align='center' class='m_td' style='background-color:#fff7da;' nowrap=''><font color='#000000' class='small'><b>회원/ID</b></font></td>
		<td align='center' class='m_td' style='background-color:#fff7da;' nowrap=''><font color='#000000' class='small'><b>견적서분류</b></font></td>
		<td align='center' class='m_td' style='background-color:#fff7da;' nowrap=''><font color='#000000' class='small'><b>견적서유형</b></font></td>
		<td align='center' class='m_td' style='background-color:#fff7da;' nowrap=''><font color='#000000' class='small'><b>처리상태</b></font></td>
		<td align='center' class='m_td' style='background-color:#fff7da;' nowrap=''><font color='#000000' class='small'><b>의뢰견적가/할인견적가</b></font></td>
		<td align='center' class='e_td' style='background-color:#fff7da;' nowrap=''><font color='#000000' class='small'><b>관리</b></font></td>
		</tr>
		</tbody></table>";

						
	$mString .= "<table cellpadding=0 cellspacing=0 width=100% style='font-size:10px; ' class='list_table_box'>
				<tr align=center bgcolor=#efefef height=25>
				<td width=18% class=s_td rowspan='2'>상품명</td>
				<td width=12% class=m_td rowspan='2'>옵션</td>
				<td width=7% class=m_td rowspan='2'>매입가</td>
				<td width=7% class=m_td rowspan='2'>정가</td>
				<td width=7% class=m_td rowspan='2'>판매가(할인가)</td>
				<td width=5% class=m_td rowspan='2'>수량</td>
				<td width=7% class=m_td rowspan='2'>상품가격</td>
				<td width=7% class=m_td rowspan='2'>에누리액</td>
				<td width=7% class=m_td colspan='3'>에누리견적가</td>
				<td width=7% class=m_td rowspan='2'>할인률</td>

				</tr>";

	$mString .= "<tr align=center bgcolor=#efefef height=25>
				<td width=7% class=s_td>단가</td>
				<td width=7% class=m_td>세액</td>
				<td width=7% class=m_td>공급가</td>
				</tr>";

	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=12 align=center>등록된 견적 정보가 없습니다.</td></tr>";
	}else{

		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
	
			if($db->dt[estimate_type] == "c"){
				$estimate_type_str = "맞춤견적";
			}else if($db->dt[estimate_type] == "q"){
				$estimate_type_str = "빠른견적";
			}else if($db->dt[estimate_type] == "s"){
				$estimate_type_str = "시스템견적";
			}else if($db->dt[estimate_type] == "i"){
				$estimate_type_str = "내부견적";
			}

			switch($db->dt[estimate_type]){
				case '1':
					$estimate_type = '가이드 견적서';
				break;
				case '2':
					$estimate_type = '셀프 견적서';
				break;
				case '3':
					$estimate_type = '전문가 견적서';
				break;
				case '4':
					$estimate_type = '프리 견적서';
				break;

			}

			switch($db->dt[estimate_div]){
			
				case '1':
					$estimate_div = '창업문의';
				break;
				case '2':
					$estimate_div = '대량구매문의';
				break;
			}

			switch($db->dt[status]){
			
				case '2':
					$status = '견적대기';
				break;
				case '3':
					$status = '견적취소';
				break;
				case '4':
					$status = '견적진행중';
				break;
				case '5':
					$status = '견적기간만료';
				break;
				case '7':
					$status = '구매확정';
				break;

			}

			if($db->dt[ucode]){
				$sql = "select
						mem_type
						from
							".TBL_COMMON_USER."
						where
							code = '".$db->dt[ucode]."'";
				$mdb->query($sql);
				$mdb->fetch();
				$mem_type = $mdb->dt[mem_type];
			}
			
			if($mem_type == "C"){	//사업자 회원일경우 도매가

				/*
				$sql = "select
							sum(wholesale_price) as total_wholesale_price,
							sum(wholesale_sellprice * pcount) as total_wholesale_sellprice,
							sum(wholesale_totalprice) as total_wholesale_totalprice,
							sum(wholesale_discountprice * pcount) as total_wholesale_discountprice,
							wholesale_discountprice as discountprice
						from
							shop_estimates_detail
						where
							est_ix = '".$db->dt[est_ix]."'
				";
		
				$mdb->query($sql);
				$mdb->fetch();
				$total_price = $mdb->dt[wholesale_totalprice];	//최종견적가(의뢰견적가)
				$discount_price = $mdb->dt[total_wholesale_discountprice];	//에누리가격 합계
				$discountprice	= $mdb->dt[discountprice];
				$total_sellprice	= $mdb->dt[total_wholesale_sellprice];
				*/
				$sql = "select
							sum(sellprice * pcount) as total_sellprice,
							sum(totalprice) as total_totalprice,
							sum(discountprice * pcount) as total_discountprice,
							discountprice as discountprice
							
						from
							shop_estimates_detail
						where
							est_ix = '".$db->dt[est_ix]."'
				";
			
				$mdb->query($sql);
				$mdb->fetch();
				$total_price = $mdb->dt[total_totalprice];	//최종견적가(의뢰견적가) 합계
				$discount_price = $mdb->dt[total_discountprice];	//에누리가격 합계
				$discountprice	= $mdb->dt[discountprice];
				$total_sellprice	= $mdb->dt[total_sellprice];

			}else{	//일반회원일경우 소매가
				$sql = "select
							sum(sellprice * pcount) as total_sellprice,
							sum(totalprice) as total_totalprice,
							sum(discountprice * pcount) as total_discountprice,
							discountprice as discountprice
							
						from
							shop_estimates_detail
						where
							est_ix = '".$db->dt[est_ix]."'
				";
			
				$mdb->query($sql);
				$mdb->fetch();
				$total_price = $mdb->dt[total_totalprice];	//최종견적가(의뢰견적가) 합계
				$discount_price = $mdb->dt[total_discountprice];	//에누리가격 합계
				$discountprice	= $mdb->dt[discountprice];
				$total_sellprice	= $mdb->dt[total_sellprice];
			}

			$no = $total - ($page - 1) * $max - $i;
			$mString .= "<tr>
						<td class='' style='background-color:#fff7da;height:30px;font-weight:bold;' colspan='12'>
							<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center'>
								<colgroup>
								<col width='15px'>
								<col width='8%'>
								<col width='15%'>
								<col width='11%'>
								<col width='11%'>
								<col width='11%'>
								<col width='11%'>
								<col width='10%'>
								<col width='15%'>
								<col width='6%'>
								</colgroup><tbody><tr>
									<td align='center' style='background-color:#fff7da;'><input type='checkbox' name='est_id[]' id='est_id' value='".$db->dt[est_id]."'>
									<td align='center' style='background-color:#fff7da;'><font color='#000000' class='small'><b>자체쇼핑몰</b></font></td>
									<td align='center' style='background-color:#fff7da;' nowrap=''><font color='orange' class='small'><b>".$db->dt[open_date]."</b></font></td>
									<td align='center' style='background-color:#fff7da;' nowrap=''><font color='blue' class='small'><b>";

									if($depth == 'order'){
							$mString .= "
									<a href='/admin/order/orders.read.php?oid=".$db->dt[oid]."&amp;pid=0910000018' style='color:#007DB7;font-weight:bold;' class='small'>".$db->dt[estimate_code]."</a>";
									}else{
							$mString .= "
									<a href='/admin/estimate/estimate.php?est_ix=".$db->dt[est_ix]."&amp;pid=0910000018' style='color:#007DB7;font-weight:bold;' class='small'>".$db->dt[estimate_code]."</a>";

									}
									$mString .= "
									</b></font></td>
									<td align='center' style='background-color:#fff7da;' nowrap=''><font color='#000000' class='small'><b>".$db->dt[bname]."<br>".$db->dt[buser_id]."</b></font></td>
									<td align='center' style='background-color:#fff7da;' nowrap=''><font color='#000000' class='small'><b>".$estimate_type."</b></font></td>
									<td align='center' style='background-color:#fff7da;' nowrap=''><font color='#000000' class='small'><b>".$estimate_div."</b></font></td>
									<td align='center' style='background-color:#fff7da;' nowrap=''><font color='#000000' class='small'><b>".$status."</b></font></td>
									<td align='center' style='background-color:#fff7da;' nowrap=''><font color='#000000' class='small'><b>".number_format($total_sellprice)." 원 / ".number_format($total_price)." 원</b></font></td>
									<td align='center' style='background-color:#fff7da;' class='small' colspan='2'>
									<a href='./estimate.php?est_ix=".$db->dt[est_ix]."&oid=".$db->dt[oid]."'><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0></a>&nbsp;
									<a href=\"JavaScript:deleteEstimate('delete','".$db->dt[est_ix]."','basic')\"><img src='../images/".$admininfo["language"]."/btn_del.gif' border=0></a>
									</td>
								</tr>
							</tbody></table>
						</td>
					</tr>";
		
		$sql = "select 
					ed.*,
					ccd.com_name,
					po.option_div,
					p.pname,
					ed.listprice,
					ed.sellprice,
					p.coprice
				from 
					shop_estimates_detail as ed
					inner join shop_product as p on (ed.pid = p.id)
					inner join common_company_detail as ccd on (p.admin = ccd.company_id)
					left join shop_product_options_detail as po on (ed.opn_ix = po.id)
				where 
					est_ix = '".$db->dt[est_ix]."'
					order by ed.estd_ix ASC";
		$cdb->query($sql);
		$esti_detail = $cdb->fetchall();

			for($j=0;$j<count($esti_detail); $j++){

			$td_dc_unit_price = round(($esti_detail[$j][sellprice] - $esti_detail[$j][discountprice]) /11*10*$esti_detail[$j][pcount]);
			$dc_tax = round(($esti_detail[$j][sellprice] - $esti_detail[$j][discountprice]) /11*$esti_detail[$j][pcount]);
			
			$mString .= "<tr>
						<td width='150'>
							<table>
							<tbody>
								<tr>
								<td class='small' style='line-height:140%'><a href='javascript:PopSWindow('../seller/company.add.php?company_id='".$esti_detail[$j][company_id]."'&mmode=pop',960,600,'brand')'><b>".$esti_detail[$j][com_name]."</b></a><br>".$esti_detail[$j][pname]."
								</td>
							</tr>
							</tbody>
							</table>
						</td>
						<td align='center'><label class='helpcloud' help_width='140' help_height='15' help_html='가격+재고관리 옵션'>".($esti_detail[$j][option_div]?$esti_detail[$j][option_div]:'옵션 없음')."</label> </td>
						<td class='' align='center'>".number_format($esti_detail[$j][coprice])." 원</td>
						<td class='' align='center'>".number_format($esti_detail[$j][listprice])." 원</td>
						<td class='' align='center'>".number_format($esti_detail[$j][sellprice])." 원</td>
						<td class='' align='center'>".$esti_detail[$j][pcount]." 개</td>
						<td class='' align='center'>".number_format($esti_detail[$j][sellprice] * $esti_detail[$j][pcount])." 원</td>
						<td class='' align='center' style='line-height:140%;' >".number_format($esti_detail[$j][discountprice])." 원</td>
						<td class='' align='center'>".number_format($td_dc_unit_price)." 원</td>
						<td class='' align='center'>".number_format($dc_tax)." 원</td>
						<td class='' align='center'>".number_format($esti_detail[$j][totalprice])." 원</td>
						<td class='' point'='' align='center'>".$esti_detail[$j][rate]." % <input type='hidden' id='od_status_2013071215106196' value='DP'><br><b></b></td>
					</tr>";

			}
		}
	}


	$mString .= "</table>";
	$mString .= "<table cellpadding=0 cellspacing=0 width=100%>
					<tr height=50 bgcolor=#ffffff><td colspan=8 align=center>".page_bar($total, $page, $max,  "&max=$max")."</td></tr>
				 </table>
				";
	return $mString;
}

?>
