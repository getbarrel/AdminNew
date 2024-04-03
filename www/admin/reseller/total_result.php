<?
include_once("../class/layout.class");
//print_r($admin_config);

if ($vFromYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $vFromYY."/".$vFromMM."/".$vFromDD;
	$eDate = $vToYY."/".$vToMM."/".$vToDD;
	$startDate = $vFromYY.$vFromMM.$vFromDD;
	$endDate = $vToYY.$vToMM.$vToDD;
}


$db = new Database;
$db1 = new Database;

$vdate = date("Ymd", time());
$today = date("Ymd", time());
$firstday = date("Ymd", time()-84600*date("w"));
$lastday = date("Ymd", time()+84600*(6-date("w")));


$Contents = "

<table width='100%'>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation("리셀러종합성과리스트", "리셀러관리 > 통계분석 > 종합성과리스트")."</td>
	</tr>
	<!--tr>
		<td colspan=3 align=right style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 리셀러종합성과리스트 </b></div>")."</td>
	</tr-->
</table>
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td colspan=2>
			".OrderSummary()."
		</td>
	</tr>
	<tr>";

$Contents .= "
		<td style='width:75%;' colspan=2 valign=top>
			<table width=100%  border=0><form name='search_frm' method='get' action=''>
				<tr height=25>
					<td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>리셀러 검색하기</b></td>
				</tr>
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
								<td class='box_05'>
									<TABLE cellSpacing=0 cellPadding=3 style='width:100%;' align=center border=0 class='search_table_box'>
											<col width=170>
											<col width=*>
											<tr height=30>
												<th class='search_box_title'>조건 : </th>
												<td class='search_box_item' colspan=3>
													<table cellpadding='3' cellspacing='0' border='0' width='100%'>
													<col width='100px'>
													<col width='*'>
													<tr>
														<td >
														<select name='search_type' style='font-size:12px;'>
															<option value='combi_name' ".CompareReturnValue('combi_name',$search_type,' selected').">리셀러ID+이름</option>
															<option value='id' ".CompareReturnValue('id',$search_type,' selected').">ID</option>
															<option value='name' ".CompareReturnValue('name',$search_type,' selected').">이름</option>
															<option value='mail' ".CompareReturnValue('mail',$search_type,' selected').">이메일</option>
															<option value='tel' ".CompareReturnValue('tel',$search_type,' selected').">전화번호</option>
															<option value='pcs' ".CompareReturnValue('pcs',$search_type,' selected').">휴대전화</option>
														</select>
														</td>
														<td ><input type='text' class=textbox name='search_text' size='30' value='$search_text' style=''></td>
														</tr>
														</table>
													</td>
											</tr>
											<tr height=33>
												<th class='search_box_title'>
												<label for='regdate'>리셀러 등록일</label>
												<input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeOrderDate(document.search_frm);' ".CompareReturnValue('1',$regdate,' checked')."></th>
												<td class='search_box_item' colspan=3>
													<table cellpadding=3  cellspacing=1 border=0 bgcolor=#ffffff>
														<tr>
															<TD  nowrap><SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromMM></SELECT> 월 <SELECT name=vFromDD></SELECT> 일 </TD>
															<TD style='padding:0 5px;' align=center> ~ </TD>
															<TD nowrap><SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToMM></SELECT> 월 <SELECT name=vToDD></SELECT> 일</TD>
															<td>";

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

							$Contents .= "
												<a href=\"javascript:init_date('$today','$today');\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
												<a href=\"javascript:init_date('$vyesterday','$vyesterday');\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
												<a href=\"javascript:init_date('$voneweekago','$today');\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
												<a href=\"javascript:init_date('$v15ago','$today');\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
												<a href=\"javascript:init_date('$vonemonthago','$today');\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
												<a href=\"javascript:init_date('$v2monthago','$today');\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
												<a href=\"javascript:init_date('$v3monthago','$today');\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>

															</td>
														</tr>
													</table>
												</td>												
											</tr>
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
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2 align=center style='padding:10px 0px;'>
			<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
		</td>
	</tr></form>
</table>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
 <tr height=30>";


	$max = 15; //페이지당 갯수

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	// 검색 조건 설정 부분

	$where = " where rp.rsl_code != '' and rp.rsl_ok='y' ";
	
	
	if($search_type && $search_text){
			if($search_type == "combi_name"){
				$where .= "and (id LIKE '%".trim($search_text)."%'  or name LIKE '%".trim($search_text)."%') ";
			}elseif($search_type == "name" || $search_type == "mail" || $search_type == "pcs" || $search_type == "tel" ){
				$where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
			}else{
				$where .= "and $search_type LIKE '%".trim($search_text)."%' ";
			}
		}


	$startDate = $vFromYY.$vFromMM.$vFromDD;
	$endDate = $vToYY.$vToMM.$vToDD;

	if($startDate != "" && $endDate != ""){
		$where .= " and  MID(replace(rp.regdate,'-',''),1,8) between  $startDate and $endDate ";
	}


	// 전체 갯수 불러오는 부분

	$db->query("SELECT count(*) as total from reseller_policy rp inner join ".TBL_COMMON_USER." cu on(rp.rsl_code=cu.code) inner join ".TBL_COMMON_MEMBER_DETAIL." cmd USING (code)	$where ");
	$db->fetch();
	$total = $db->dt[total];
	$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD","view");




	$sql = "select rp.rsl_code, date_format(rp.regdate,'%Y.%m.%d') as regdate, cu.id, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
			cu.visit, 	(SELECT count(*) FROM reseller_flowin_detail rfd2 WHERE rfd2.rsl_code=cu.code) as flowin_common_cunt,
			(SELECT count(*) FROM reseller_visit_count rvc WHERE rvc.rsl_code=cu.code) as total_visit_cunt,
			(SELECT sum(od.ptprice) FROM  reseller_incentive ri ,".TBL_SHOP_ORDER_DETAIL." od WHERE rp.rsl_code=ri.rsl_code and ri.ac_ix !='' and ri.incentive_type ='2' and od.od_ix=ri.od_ix ) as total_ptprice,
			(SELECT sum(ri.incentive) FROM  reseller_incentive ri WHERE ri.ac_ix !='' and ri.incentive_type ='1' and rp.rsl_code=ri.rsl_code ) as total_incentive_common,
			(SELECT sum(ri.incentive) FROM  reseller_incentive ri WHERE ri.ac_ix !='' and ri.incentive_type ='2' and rp.rsl_code=ri.rsl_code ) as total_incentive_order
			from reseller_policy rp inner join ".TBL_COMMON_USER." cu on(rp.rsl_code=cu.code) inner join ".TBL_COMMON_MEMBER_DETAIL." cmd USING (code)
			$where	ORDER BY rp.regdate DESC LIMIT $start, $max";
		

	$db->query($sql);

	$sql1= "select cu.id,	(SELECT sum(ri.incentive) FROM  reseller_incentive ri WHERE ri.ac_ix !='' and rp.rsl_code=ri.rsl_code ) as total_incentive
			from reseller_policy rp inner join ".TBL_COMMON_USER." cu on(rp.rsl_code=cu.code) inner join ".TBL_COMMON_MEMBER_DETAIL." cmd USING (code)
			where rp.rsl_code != '' and rp.rsl_ok='y' ORDER BY total_incentive DESC";
	
	$db1->query($sql1); //순위 정하기 위한 쿼리


/*
	 $Contents .= "<td colspan=3 align=left></td>
						<td colspan=9 align=right>
		<!--a href=\"javascript:mybox.service('/admin/order/excel_out.php?".$QUERY_STRING."','10','500','900', 4, [], Prototype.emptyFunction, [], 'HOME > 주문관리 > 주문내역저장하기');\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a-->
		<a href='excel_out.php?".$QUERY_STRING."' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";

	$Contents .= " <a href='orders_excel2003.php?".$type_param."&".$add_query_string."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
*/
	$Contents .= "
	  </td>
	  </tr>
	  </table>
	  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
		<tr height='25' >
			<td width='5%' align='center' class='m_td'><b>순서</b></td>
			<td width='10%' align='center'  class='m_td' nowrap><b>가입일</b></td>
			<td width='*' align='center' class='m_td' nowrap><b>ID</b></td>
			<td width='7%' align='center' class='m_td' nowrap><b>이름</b></td>
			<td width='7%' align='center' class='m_td' nowrap><b>로그인수</b></td>
			<td width='5%' align='center' class='m_td' nowrap><b>총방문자</b></td>
			<td width='7%' align='center' class='m_td' nowrap><b>가입자</b></td>
			<td width='7%' align='center' class='m_td' nowrap><b>가입율</b></td>
			<td width='5%' align='center' class='m_td' nowrap><b>매출액</b></td>
			<td width='7%' align='center' class='m_td' nowrap><b>객단가</b></td>
			<td width='7%' align='center' class='e_td' nowrap><b>가입</br>인센티브</b></td>
			<td width='7%' align='center' class='e_td' nowrap><b>매출</br>인센티브</b></td>
			<td width='7%' align='center' class='e_td' nowrap><b>합계</b></td>
			<td width='7%' align='center' class='e_td' nowrap><b>순위</b></td>
		</tr>
	  ";

	if ($db->total){

		for ($i = 0; $i < $db->total; $i++){
			
			$db->fetch($i);

			

			for($j = 0; $j < $db1->total; $j++){
				
				$db1->fetch($j);

				if($db1->dt[id] == $db->dt[id] ){
					$ranking = $j+1;
				}
			}

			$no = $total - ($page - 1) * $max - $i;
		
		$sum_incentive=$db->dt[total_incentive_common]+$db->dt[total_incentive_order];
		
		$Contents = $Contents."
		  <tr height='28' >
			<td class='list_box_td'>".$no."</td>
			<td class='list_box_td point' nowrap>".$db->dt[regdate]."</td>
			<td class='list_box_td' >".$db->dt[id]."</td>
			<td class='list_box_td' >".$db->dt[name]."</td>
			<td class='list_box_td' >".number_format($db->dt[visit])."</td>
			<td class='list_box_td' >".number_format($db->dt[total_visit_cunt])."</td>
			<td class='list_box_td' >".number_format($db->dt[flowin_common_cunt])."</td>
			<td class='list_box_td' >".(!$db->dt[flowin_common_cunt] ? 0 : number_format($db->dt[flowin_common_cunt] / $db->dt[total_visit_cunt] * 100))." %</td>
			<td class='list_box_td' >".number_format($db->dt[total_ptprice])."</td>
			<td class='list_box_td' >".(!$db->dt[flowin_common_cunt] ? 0 : number_format($db->dt[total_ptprice] / $db->dt[flowin_common_cunt] ))." 원</td>
			<td class='list_box_td' >".number_format($db->dt[total_incentive_common])."</td>
			<td class='list_box_td' >".number_format($db->dt[total_incentive_order])."</td>
			<td class='list_box_td' >".number_format($sum_incentive)."</td>
			<td class='list_box_td ctr point' >".$ranking."</td>
		  </tr>";
				
		 }

	}else {

	$Contents = $Contents."
	  <tr height=50>
		<td colspan='14' align='center'>조회된 결과가 없습니다.</td>
	  </tr>";
	}

	$Contents .= "
	  </table>
	  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
	  <tr height=40>
		<td colspan=12 align=left valign=middle style='font-weight:bold' nowrap>
		</td>
	  </tr>
	  <tr height=40>
		<td colspan='12' align='center'>&nbsp;".$str_page_bar."&nbsp;</td>
	  </tr>
	</table>
	<form name='lyrstat'>
		<input type='hidden' name='opend' value=''>
	</form>";

/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >주문번호를 클릭하시면 주문에 대한 상세 정보를 보실수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >주문상태를 변경하시려면 수정버튼을 누르세요</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >주문상태를 빠르게 변경하시려면 변경하시고자 하는 주문 선택후 아래 변경하고자 하는 상태를 선택하신후 수정버튼을 클릭하시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>주문총액</b>은 <u>배송비 미포함 금액</u>입니다.</td></tr>
</table>
";*/
//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

//$Contents .= HelpBox("$title_str", $help_text);
$P = new LayOut();
$P->strLeftMenu = reseller_menu();
$P->OnloadFunction = "onLoad('$sDate','$eDate');ChangeOrderDate(document.search_frm);";//MenuHidden(false);
$P->addScript = "<script language='javascript' src='total_result.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n";
$P->Navigation = "리셀러관리 > 통계분석 > 종합성과리스트";
$P->title = "통계분석 >종합성과리스트";
$P->strContents = $Contents;
echo $P->PrintLayOut();


function OrderSummary(){
	global $currency_display, $admin_config;
	$mdb = new Database;

	$vdate = date("Ymd", time());
	$today = date("Ymd", time());
	$firstday = date("Ymd", time()-84600*date("w"));
	$lastday = date("Ymd", time()+84600*(6-date("w")));

	$sql = "select count(rp.rsl_code) as total_rsl,
			(SELECT count(rp.rsl_code) FROM reseller_policy rp WHERE rp.rsl_ok='y' ) as now_rsl,
			(SELECT count(visit_ix) FROM reseller_visit_count WHERE rsl_code !='' ) as total_visit,
			(SELECT count(flowin_code) FROM reseller_flowin_detail) as total_common,
			(SELECT count(ri.incentive) FROM  reseller_incentive ri WHERE ri.ac_ix !='' and ri.incentive_type ='2' ) as total_order,
			(SELECT sum(od.ptprice) FROM  reseller_incentive ri ,".TBL_SHOP_ORDER_DETAIL." od WHERE ri.ac_ix !='' and ri.incentive_type ='2' and od.od_ix=ri.od_ix ) as total_ptprice,
			(SELECT sum(ri.incentive) FROM  reseller_incentive ri WHERE ri.ac_ix !='' and ri.incentive_type ='1' ) as total_incentive_common,
			(SELECT sum(ri.incentive) FROM  reseller_incentive ri WHERE ri.ac_ix !='' and ri.incentive_type ='2' ) as total_incentive_order
			from reseller_policy rp inner join ".TBL_COMMON_USER." cu on(rp.rsl_code=cu.code) inner join ".TBL_COMMON_MEMBER_DETAIL." cmd USING (code)
			WHERE  rp.rsl_code!=''
			";

	$mdb->query($sql);
	$mdb->fetch();

	$total_incentive_order=$mdb->dt[total_incentive_order];
	$total_incentive_common=$mdb->dt[total_incentive_common];
	$total_sum=$total_incentive_order+$total_incentive_common;

	$mstring = "<table width=100%  border=0><form name='search_frm' method='get' action=''>
				<tr height=25>
					<td style='border-bottom:2px solid #efefef'>
					<img src='../images/dot_org.gif' align=absmiddle> <b>종합홍보지수</b>
					</td>
				</tr>
				<tr>
					<td align='left' colspan=2 height=100 width='100%' valign=top style='padding-top:5px;'>
					<table cellpadding=3 cellspacing=1 width='100%' border='0' bgcolor='silver'>
						<col width='11%'>
						<col width='11%'>
						<col width='11%'>
						<col width='11%'>
						<col width='11%'>
						<col width='11%'>
						<col width='11%'>
						<col width='11%'>
						<col width='11%'>
							<tr height=30 bgcolor=#efefef  align='center' >
								<th colspan='3'>리셀러활동수</th><th colspan='3'>가입</th><th colspan='3'>객단가</th>
							</tr>
							<tr height=30 bgcolor=#efefef align='center'>
								<td>총회원</td>	<td>홍보단수</td><td>활동율</td><td>총방문자</td><td>가입자</td><td>가입율</td><td>총구매건수</td><td>총매출액</td><td>객단가</td>
							</tr>
							<tr height=30 align='right' bgcolor=white>
								<td style='padding-right:15px;'> ".number_format($mdb->dt[total_rsl])." 명</td>
								<td style='padding-right:15px;'> ".number_format($mdb->dt[now_rsl])." 명</td>
								<td style='padding-right:15px;'> ".(!$mdb->dt[now_rsl] ? 0 : number_format($mdb->dt[now_rsl]/$mdb->dt[total_rsl]*100))." %</td>
								<td style='padding-right:15px;'> ".number_format($mdb->dt[total_visit])." 명</td>
								<td style='padding-right:15px;'> ".number_format($mdb->dt[total_common])." 명</td>
								<td style='padding-right:15px;'> ".(!$mdb->dt[total_common] ? 0 : number_format($mdb->dt[total_common]/$mdb->dt[total_visit]*100))." %</td>
								<td style='padding-right:15px;'> ".number_format($mdb->dt[total_order])." 건</td>
								<td style='padding-right:15px;'> ".number_format($mdb->dt[total_ptprice])." 원</td>
								<td style='padding-right:15px;'> ".(!$mdb->dt[total_order] ? 0 : number_format($mdb->dt[total_ptprice]/$mdb->dt[total_order]))." 원</td>
							</tr>
						</table>
						<table cellpadding=3 cellspacing=1 width='100%' border='0' bgcolor='silver'>
						<col width='11%'>
						<col width='11%'>
						<col width='11%'>
						<col width='11%'>
						<col width='11%'>
						<col width='11%'>
						<col width='11%'>
						<col width='11%'>
						<col width='11%'>		
							<tr height=30 bgcolor=#efefef  align='center' >
								<th colspan='3'>가입인센티브</th><th colspan='3'>매출액</th><th colspan='3'>총누적 평균 수익</th>
							</tr>
							<tr height=30 bgcolor=#efefef align='center'>
								<td>홍보단수</td><td>가입인센티브</td><td>1인평균</td><td>홍보단수</td><td>매출액</td><td>1인평균</td><td>홍보단수</td><td>누적수익</td><td>1인평균</td>
							</tr>
							<tr height=30 align='right' bgcolor=white>
								<td style='padding-right:15px;'> ".number_format($mdb->dt[now_rsl])." 명</td>
								<td style='padding-right:15px;'> ".number_format($mdb->dt[total_incentive_common])." 원</td>
								<td style='padding-right:15px;'> ".(!$mdb->dt[now_rsl] ? 0 : number_format($mdb->dt[total_incentive_common]/$mdb->dt[now_rsl]))." 원</td>
								<td style='padding-right:15px;'> ".number_format($mdb->dt[now_rsl])."</td>
								<td style='padding-right:15px;'> ".number_format($mdb->dt[total_ptprice])." 명</td>
								<td style='padding-right:15px;'> ".(!$mdb->dt[now_rsl] ? 0 : number_format($mdb->dt[total_ptprice]/$mdb->dt[now_rsl]))." 원</td>
								<td style='padding-right:15px;'> ".number_format($mdb->dt[now_rsl])."</td>
								<td style='padding-right:15px;'> ".number_format($total_sum)."</td>
								<td style='padding-right:15px;'> ".(!$mdb->dt[now_rsl] ? 0 : number_format($total_sum/$mdb->dt[now_rsl]))." 원</td>
							</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td style='padding:5px 0px;text-align:right;'>* 위 통계는 정산이 이루어진 데이터로 작성 됩니다.</td>
				</tr>
			</table>";

	return $mstring;
}
?>