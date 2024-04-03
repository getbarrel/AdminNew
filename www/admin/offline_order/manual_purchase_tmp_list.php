<?
include("../class/layout.class");

$db = new Database;
$mdb = new Database;

$Script = "
<link type='text/css' href='../js/themes/base/ui.all.css' rel='stylesheet' />
<script type='text/javascript' src='../js/ui/ui.core.js'></script>
<script type='text/javascript' src='../js/ui/ui.datepicker.js'></script>

<script language='javascript'>
	$(function() {
		$(\"#start_datepicker\").datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yymmdd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){
			//alert(dateText);
			if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
				$('#end_datepicker').val(dateText);
			}else{
				$('#end_datepicker').datepicker('setDate','+0d');
			}
		}

		});

		$(\"#end_datepicker\").datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yymmdd',
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

	function clearAll(frm){
		for(i=0;i < frm.mpt_ix.length;i++){
				frm.mpt_ix[i].checked = false;
		}
	}

	function checkAll(frm){
		for(i=0;i < frm.mpt_ix.length;i++){
				frm.mpt_ix[i].checked = true;
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

	function CheckMptix(frm){
		var checked_bool = false;
		
		for(i=0;i < frm.mpt_ix.length;i++){
			if(frm.mpt_ix[i].checked){
				checked_bool = true;
			}
		}

		if(!checked_bool){
			alert('상태변경하실 임시수주서를 한개이상 선택하셔야 합니다.');
			return false;
		}else{
			if(confirm('선택하신 상태로 처리 하시겠습니까?')){
				return true;
			}else{
				return false;
			}
		}
	}

</script>";

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


$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("임시수주서리스트", "수주관리 > 임시수주서리스트 ")."</td>
		</tr>
		<tr>
			<td>
				<form name='search' >
				<table border='0' cellpadding='0' cellspacing='0' width='100%'>
					<tr>
					<td style='width:100%;' valign=top colspan=3>
						<table width=100%  cellpadding='0' cellspacing='0'  border=0>
							<!--tr height=25><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>적립금 검색하기</b></td></tr-->
							<tr>
								<td align='left' colspan=2  width='100%' valign=top style='padding-top:0px;'>
									<table class='box_shadow' cellpadding=0 cellspacing=0 style='width:100%;' align=left>
										<tr>
											<th class='box_01'></th>
											<td class='box_02'></td>
											<th class='box_03'></th>
										</tr>
										<tr>
											<th class='box_04'></th>
											<td class='box_05' valign=top style='padding:0px;'>
												<TABLE height=20 cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0>
												<TR>
													<TD bgColor=#ffffff style='padding:0 0 0 0;'>
													<table cellpadding=3 cellspacing=1 width='100%' class='search_table_box'>
														 <tr height=27>
															<th class='search_box_title' bgcolor='#efefef' width='150' align=center>조건검색 </th>
															<td class='search_box_item'>
															<table cellpadding=0 cellspacing=0 width=100%>
																<col width=110>
																<col width=*>
																<tr>
																	<td>
																	<select name=search_type style='width:100px;'>
																		<option value='ci_name' ".CompareReturnValue("ci_name",$search_type,"selected").">매출처</option>
																		<option value='com_name' ".CompareReturnValue("com_name",$search_type,"selected").">납품처명</option>
																	</select>
																	</td>
																	<td>
																	<input type=text name='search_text' class=textbox value='".$search_text."' style='width:50%' >
																	</td>
																</tr>
															</table>
															</td>
														</tr>
														<tr height=27>
														  <td class='search_box_title' bgcolor='#efefef' align=center><label for='regdate'><b>임시저장일자</b></label></td>
														  <td class='search_box_item' align=left style='padding-left:5px;'>
															<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
																<col width=70>
																<col width=20>
																<col width=70>
																<col width=*>
																<tr>
																	<TD nowrap>
																	<input type='text' name='sdate' class='textbox' value='".$sdate."' style='height:20px;width:70px;text-align:center;' id='start_datepicker'>
																	<!--SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년
																	<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월
																	<SELECT name=FromDD></SELECT> 일 -->
																	</TD>
																	<TD align=center> ~ </TD>
																	<TD nowrap>
																	<input type='text' name='edate' class='textbox' value='".$edate."' style='height:20px;width:70px;text-align:center;' id='end_datepicker'>
																	<!--SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년
																	<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월
																	<SELECT name=ToDD></SELECT> 일 -->
																	</TD>
																	<TD style='padding:0px 10px'>
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
								<td colspan=3 align=center style='padding:10px 0 20px 0'>
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
			<td>";

	$max = 20;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	if($sdate && $edate){
		$where .= " and DATE_FORMAT(regdate,'%Y%m%d') between ".$sdate." and ".$edate." ";
	}

	if($search_text && $search_type){
		$where .= " and $search_type LIKE '%".$search_text."%' ";
	}


	$sql="select * from shop_manual_purchase_tmp where code='".$admininfo[charger_ix]."' ";

	$mdb->query($sql);
	$total = $mdb->total;
	

	$mstring .= "
					<form  name='listform' method='post' onsubmit=\"return CheckMptix(this)\" action='./manual_purchase.act.php' target='act' >
					<input type='hidden' id='mpt_ix' />
					<input type='hidden' id='frm_act' name='act' value='' />
						<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver class='list_table_box'>
						<tr align=center bgcolor=#efefef height=30 style='font-weight:600;'>
							<td class=s_td width='5%'><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
							<td class=s_td width='*'>임시저장일</td>
							<td class=m_td width='15%'>매출처</td>
							<td class=m_td width='10%'>결제상태</td>
							<td class=m_td width='10%'>발송예정일</td>
							<td class=m_td width='10%'>납품처명</td>
							<td class=m_td width='10%'>상품수량</td>
							<td class=m_td width='10%'>잔여여신</td>
							<td class=m_td width='10%'>총주문금액</td>
							<td class=e_td width='10%'>관리</td>
						</tr>";

	if ($total == 0){
		$mstring .= "<tr bgcolor=#ffffff height=50><td class='list_box_td' colspan=10 align=center>등록된 임시수주서가 없습니다.</td></tr>";
	}else{
		
		$sql="select * from shop_manual_purchase_tmp where code='".$admininfo[charger_ix]."' order by regdate desc limit $start , $max";
		$mdb->query($sql);

		for($j=0;$j < $mdb->total;$j++){
			$mdb->fetch($j);

			$mstring .= "<tr height=30 bgcolor=#ffffff align=center>
				<td class='list_box_td list_bg_gray' ><input type=checkbox class=nonborder id='mpt_ix' name=mpt_ix[] value='".$mdb->dt[mpt_ix]."'></td>
				<td class='list_box_td'>".$mdb->dt[regdate]."</td>
				<td class='list_box_td point' >".$mdb->dt[ci_name]."</td>
				<td class='list_box_td'>".getOrderStatus($mdb->dt[status])."</td>
				<td class='list_box_td list_bg_gray' >".($mdb->dt[due_date] ==0 ||$mdb->dt[due_date] =='' ? "-" :$mdb->dt[due_date])."</td>
				<td class='list_box_td' >".$mdb->dt[com_name]."</td>
				<td class='list_box_td list_bg_gray' >".$mdb->dt[pcount]."</td>
				<td class='list_box_td' >-</td>
				<td class='list_box_td list_bg_gray' >".number_format($mdb->dt[total_price])." 원</td>
				<td class='list_box_td'><a href='./manual_purchase_list.php?mpt_ix=".$mdb->dt[mpt_ix]."'><img  src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a></td>
				</tr>";
		}
	}

$mstring .= "
					</table>
				</form>
				<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver >
					<tr height=50 bgcolor=#ffffff>
						<td>
							<input type='button' value='선택 일괄삭제' onclick=\"$('#frm_act').val('select_tmp_delete');$('form[name=listform]').submit();\" /> <input type='button' value='선택 일괄주문하기' onclick=\"$('#frm_act').val('select_tmp_insert');$('form[name=listform]').submit();\" />
						</td>
						<td align=right>
							".page_bar($total, $page, $max,"&max=$max&search_type=$search_type&search_text=$search_text&sdate=$sdate&edate=$edate","")."
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>";



$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$mstring .= HelpBox("임시수주서리스트", $help_text);

$Contents = $mstring;

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = offline_order_menu();
$P->Navigation = "수주관리 > 임시수주서리스트";
$P->title = "임시수주서리스트";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>
