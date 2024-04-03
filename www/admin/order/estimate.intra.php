<?
include("../class/layout.class");
session_start();

$db = new Database;
$db1 = new Database;
$db2 = new Database;

if ($act_c == "clear")
{
	if(is_array($ESTIMATE_INTRA)){
		session_unregister("ESTIMATE_INTRA");
		echo "<script>alert('기존세션이 있어서 초기화합니다.');location.href='estimate.intra.php';</script>";
	}
}



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

	//print_r($ESTIMATE_INTRA);
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

if($mode == "et_update"){
	$sql = "select e.*,m.id from 
	mallstory_estimates e left join ".TBL_MALLSTORY_MEMBER." m on e.ucode = m.code where est_ix = '".$est_ix."' ";
	$db1->query($sql);
	$db1->fetch();
	$est_zip = explode("-", $db1->dt[est_delivery_zip]);
	$est_tel = explode("-", $db1->dt[est_tel]);
	$est_mobile = explode("-", $db1->dt[est_mobile]);
	if($EstimateBool){
		$EstimateBool = false;
		session_register("EstimateBool");
		session_unregister("ESTIMATE_INTRA");
		$sql = "SELECT ed.*,p.coprice from mallstory_estimates_detail ed, ".TBL_MALLSTORY_PRODUCT." p, mallstory_companyinfo ci WHERE est_ix = '".$est_ix."' and ed.pid = p.id and p.admin = ci.company_id ORDER BY ci.company_name desc";
		$db->query($sql);
		$num = 1;
		$sum = 0;
		for($j = 0; $j < $db->total; $j++)
		{
			$db->fetch($j);
			$option = $db->dt[options];
			$options = "";
			if($option) $options = explode("|",$option);
			$id = $db->dt[pid];
			if(is_array($options)){
				$option_serial = md5($id.serialize($options));
			}else{
				$option_serial = md5($id);
			}

			$ESTIMATE_INTRA[$option_serial] = array("pname"=>$db->dt[pname], "pcount"=>$db->dt[pcount], "coprice"=>$db->dt[coprice] ,"sellprice"=>$db->dt[sellprice] ,"id"=>$id, "cid"=>$cid, "pcode"=>$db->dt[pcode], "options"=>$options, "option_serial"=>$option_serial,"totalprice"=>$db->dt[sellprice]*$db->dt[pcount]);
		}
	session_register("ESTIMATE_INTRA");
	}
}

if ($act == "add" )
{
	//print_r($id);
	$option_pric = 0;
	$id_cnt=count($id);
	$option_coprice="";
	$option_kind="";
	for($c=0;$c<$id_cnt;$c++) {
		if(is_array($options[$id[$c]])){
			$option_serial = md5($id[$c].serialize($options[$id[$c]]));
			for($o=0; $o<count($options[$id[$c]]); $o++){
				$sql = "select ops.option_kind,op.option_price,op.option_coprice,op.option_warehouse_pcode,op.option_barcode   from ".TBL_MALLSTORY_PRODUCT_OPTIONS." ops LEFT JOIN ".TBL_MALLSTORY_PRODUCT_OPTION." op ON ops.opn_ix=op.opn_ix where op.pid = '".$id[$c]."' and op.id ='".$options[$id[$c]][$o]."' ";
				$db->query($sql);
				$db->fetch();
				$option_price = $db->dt[option_price];
				$option_warehouse_pcode = $db->dt[option_warehouse_pcode];
				$option_barcode = $db->dt[option_barcode];
				if($db->dt[option_kind]=="b") {
					$option_coprice=$db->dt[option_coprice];
					$option_kind="b";
				}
			}
		}else{
			$option_serial = md5($id[$c]);
		}
		if($ESTIMATE_INTRA[$option_serial]) {
			$pcount_sum = $ESTIMATE_INTRA[$option_serial][pcount] + $pcount[$id[$c]];
			
			$db->query("SELECT pname, sellprice,  id,reserve,  coprice,  pcode, warehouse_pcode, barcode FROM ".TBL_MALLSTORY_PRODUCT." WHERE id='".$id[$c]."'");

			if ($db->total){			
				$db->fetch();
				if($option_price > 0) $sellprice = $option_price;
				else $sellprice = $db->dt[sellprice];
				
				if ($option_warehouse_pcode != "" ){
				   $warehouse_pcode = $option_warehouse_pcode;
				} else {
					$warehouse_pcode = $db->dt[warehouse_pcode];
				}
				if ($option_barcode != "" ){
				   $barcode = $option_barcode;
				} else {
					$barcode = $db->dt[barcode];
				}
				
				
				if($option_coprice=="") $option_coprice=$db->dt[coprice];
				
				$ESTIMATE_INTRA[$option_serial] = array("pname"=>$db->dt[pname], "pcount"=>$pcount_sum, "coprice"=>$option_coprice ,"ori_coprice"=>$db->dt[coprice], "sellprice"=>$sellprice ,"id"=>$id[$c], "cid"=>$cid, "pcode"=>$db->dt[pcode], "warehouse_pcode"=>$warehouse_pcode, "barcode"=>$barcode, "options"=>$options[$id[$c]], "option_kind"=>$option_kind, "option_serial"=>$option_serial,"totalprice"=>$sellprice*$pcount_sum);
			}
		} else {
			$db->query("SELECT pname, sellprice,   id,reserve,  coprice,  pcode, warehouse_pcode, barcode FROM ".TBL_MALLSTORY_PRODUCT." WHERE id='".$id[$c]."'");

			if ($db->total){			
				$db->fetch();
				if($option_price > 0) $sellprice = $option_price;
				else $sellprice = $db->dt[sellprice];
				
				if ($option_warehouse_pcode != "" ){
				   $warehouse_pcode = $option_warehouse_pcode;
				} else {
					$warehouse_pcode = $db->dt[warehouse_pcode];
				}
				if ($option_barcode != "" ){
				   $barcode = $option_barcode;
				} else {
					$barcode = $db->dt[barcode];
				}
				
				if($option_coprice=="") $option_coprice=$db->dt[coprice];
				
				$ESTIMATE_INTRA[$option_serial] = array("pname"=>$db->dt[pname], "pcount"=>$pcount[$id[$c]], "coprice"=>$option_coprice ,"ori_coprice"=>$db->dt[coprice], "sellprice"=>$sellprice ,"id"=>$id[$c], "cid"=>$cid, "pcode"=>$db->dt[pcode], "warehouse_pcode"=>$warehouse_pcode, "barcode"=>$barcode, "options"=>$options[$id[$c]], "option_kind"=>$option_kind, "option_serial"=>$option_serial,"totalprice"=>$sellprice*$pcount[$id[$c]]);
			}
		}
	}

	session_register("ESTIMATE_INTRA");
	
}

if ($act == "del")
{
	unset($ESTIMATE_INTRA[$option_serial]);

	session_register("ESTIMATE_INTRA");
}


$QUERY_STRING = "mode=$mode&search_yn=$search_yn&est_ix=$est_ix&cid2=$cid2&depth=$depth&search_type=$search_type&search_text=$search_text&sprice=$sprice&eprice=$eprice&company_id=$company_id&company=$company&state2=$state2&cid0_1=$cid0_1&cid1_1=$cid1_1&cid2_1=$cid2_1&cid3_1=$cid3_1&x=69&y=10";


$Contents ="
<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' valign=top>			
<tr >
	<td align='left' colspan=6 style='padding-bottom:10px;'> ".GetTitleNavigation("오프라인 빠른견적 접수", "주문관리 > 오프라인 빠른견적 접수 ")."</td>
</tr>
				
			 <form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
			 <input type='hidden' name='mode' value='$mode'>
			 <input type='hidden' name='search_yn' value='Y'>
			 <input type='hidden' name='est_ix' value='$est_ix'>
			 <input type='hidden' name='cid2' value='$cid2'>
			 <input type='hidden' name='depth' value='$depth'>
			 <tr height=150>
				<td colspan=2 class='rgt' >
					<table class='box_shadow' style='width:100%;height:20px' ><!---mbox04-->
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05 align=center' style='padding:5'>
								<table cellpadding=4 cellspacing=1 border=0 width=100% align='center'>
									<col width=10%>
									<col width=21%>
									<col width=10%>
									<col width=20%>
									<col width=10%>
									<col width=20%>
									<tr>
										<td width='150' bgcolor='#efefef' align=center>  검색조건  </td>
										<td align=left valign='top' style='padding-right:5px;padding-top:1px;' colspan=5>
											<table cellpadding=0 cellspacing=0>
											<tr>
												<td><select name='search_type'  style=\"font-size:12px;height:20px;\"><option value='pname'>상품명</option><option value='pcode'>상품코드</option><option value='id'>상품코드(키)</option><option value='making'>제조사</option></select></td>
												<td style='padding-left:5px;'>
												<INPUT id=search_texts class='textbox' value=''  autocomplete='off'  clickbool='false' style=' FONT-SIZE: 12px; WIDTH: 230px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
												</td>
												
											</tr>
											</table>
										</td>
									</tr>
									<tr hegiht=1><td colspan=6 class='dot-x'></td></tr>
									<tr height=27>
									  <td bgcolor='#efefef' align=center><label for='regdate'>등록날짜</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.search_form);' ".CompareReturnValue("1",$regdate,"checked")."></td>
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
									<tr hegiht=1><td colspan=6 class='dot-x'></td></tr>
									<tr>
										<td width='150' bgcolor='#efefef' align=center>  상품금액  </td>
										<td align=left valign='top' style='padding-right:5px;padding-top:1px;' colspan=5>
											<table cellpadding=0 cellspacing=0>
											<tr>
												<td style='padding-right:5px;'>
												<INPUT class='textbox' value=''  autocomplete='off'  clickbool='false' style=' FONT-SIZE: 12px; WIDTH: 230px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=sprice validation=false  title='상품금액'>
												</td>
												<td>~</td>
												<td style='padding-left:5px;'>
												<INPUT class='textbox' value=''  autocomplete='off'  clickbool='false' style=' FONT-SIZE: 12px; WIDTH: 230px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=eprice validation=false  title='상품금액'>
												</td>
												
											</tr>
											</table>
										</td>
									</tr>
									<tr hegiht=1><td colspan=6 class='dot-x'></td></tr>
									<tr>
										<td bgcolor='#efefef' align=center>입점업체</td>
										<td>
											".CompanyList2($company_id,"")."
										</td>
										<td bgcolor='#efefef' align=center>매입처</td>
										<td >
											".MakerList($company,$cid)."
										</td>
										<td bgcolor='#efefef' align=center>판매및 상태값</td>
										<td>
											<select name='state2'>
												<option value=''>상태값선택</option>
												<option value='1'>판매중</option>
												<option value='0'>일시품절</option>
												<option value='6'>등록신청중</option>
												<option value='7'>수정신청중</option>
											</select>
										</td>
									</tr>
									<tr><td colspan=6 class='dot-x'></td></tr>
									<tr>
										<td width='150' bgcolor='#efefef' align=center>카테고리선택</td>
										<td colspan=5>
											<table border=0 cellpadding=0 cellspacing=0>
												<tr>
													<td style='padding-right:5px;'>".getCategoryList3("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid2)."</td>	
													<td style='padding-right:5px;'>".getCategoryList3("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid2)."</td>
													<td style='padding-right:5px;'>".getCategoryList3("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid2)."</td>
													<td>".getCategoryList3("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $cid2)."</td>
												</tr>
											</table>
										</td>			
									</tr>
									<tr><td colspan=6 class='dot-x'></td></tr>
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
			<tr height=50><td  colspan=2 align=center ><input type=image src='../image/bt_search.gif' border=0 align=absmiddle> <!--btn_inquiry.gif--></td></td>
			</form>
<tr> 
	<td colspan=2 width='72%'  valign=top id='estimate_product_list'>".PrintProductList($cid, $depth)."</td>
</tr>
<tr height=30 >
	<td style='padding-left:0px;' >	
	<table width=100% border=0>
	<tr height=25><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>견적 제품정보</b></td>	
	</table>
	</td>
</tr>
<tr > 
	<td width='100%' valign=top style='padding-top:3px;'>
	<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
				<tr align=center> 
					<td width=10% height=28 class=s_td>입점업체</td>
					<td width=10% height=28 class=m_td>매입처</td>
					<td width=* colspan=2 class=m_td>제 품 명</td>
					<td width=10% class=m_td>옵션</td>
					<td width=8% class=m_td>수량</td>
					<td width=8% class=m_td>정가</td>
					<td width=11% class=m_td>판 매 가</td>
					<td width=6% class=m_td>공급가</td>
					<td width=8% class=m_td>합계</td>
					<td width=6% class=m_td>공급율</td>
					<td width=3% class=e_td>취소</td>
				</tr>";
if($ESTIMATE_INTRA){

	for ( reset($ESTIMATE_INTRA); $key = key($ESTIMATE_INTRA); next($ESTIMATE_INTRA) ){
			
			$value = pos($ESTIMATE_INTRA);
			$pid = $value[id];
			$pname = $value[pname];
			$pname_str .= $value[pname];
			$pcount    = $value[pcount];
			$options    = $value[options];
			$option_serial    = $value[option_serial];
			$option_kind= $value[option_kind];
			$coprice = $value[coprice];
			$ori_coprice = $value[ori_coprice];
			$sellprice = $value[sellprice];				
			$warehouse_pcode = $value[warehouse_pcode];				
			$barcode = $value[barcode];				
			$totalprice = $value[totalprice];	
			$estimate_totalprice = $estimate_totalprice + $totalprice;
			$coper = $coprice / $sellprice * 100;
			
			$db->query("SELECT listprice, sellprice,  admin,company, state FROM ".TBL_MALLSTORY_PRODUCT." WHERE id='$pid'");
			$db->fetch();
			
			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$pid.".gif")){
				$img_str = "".$admin_config[mall_data_root]."/images/product/s_".$pid.".gif";
			}else{
				$img_str = "../image/no_img.gif";
			}
					
$Contents .="				
				<tr height=55> 
					<form name='estimate_form_$option_serial' method='post' action='estimate.php?act=mod&id=$pid'>
					<td align=center style='padding:5px;'>".CompanyInfo($db->dt[admin])."</td>						
					<td align=center style='padding:5px;'>".$db->dt[company]."</td>						
					<td height='55' align='left'><a href='../product/goods_input.php?id=$pid'><img src='$img_str' border=0 width=50 align=left></a></td>
					<td height='55' style='padding:5px;'>$pname ".($db->dt[state] == "0" ? "<img src ='/data/sigong/templet/basic/sigong_img/btn_soldout.jpg' align='absmiddle'>" :"")."</td>					
					<td height='55' style='padding:5px;'>";
					for($o=0; $o<count($options); $o++){
					$Contents .= getOptionName($options[$o]);
					}
$Contents .=" </td>					
					<td height='55' nowrap> 
						<div align='center'><input type=text name=quantity value='$pcount' size=5 class=input2 style='text-align:right;padding:0 5 0 0' >  개</div>
					</td>
					<td height='55' align=center>".number_format($db->dt[listprice])."</td>
					<td height='55' align=center><input type=text name=sellprice value='$sellprice' size=5 class=input2 style='text-align:right;padding:0 5 0 0' > 
						<A href=\"javascript:num_apply(document.estimate_form_$option_serial,'$option_serial');\"><img src='../image/bt_modify.gif' align=absmiddle border=0></a>							        			
					</td>
					<td height='55' align=center>".number_format($coprice).($option_kind=="b"?"<br />(".number_format($ori_coprice).")":"")."</td>
					<td height='55' align=center>".number_format($totalprice)."</td>
					<td height='55' align=center>".number_format($coper)."%</td>
					<td height='55' align=center><a href='estimate.intra.php?act=del&option_serial=$option_serial&mode=$mode&est_ix=$est_ix&search_yn=$search_yn'><img src='../image/icon_x.gif' border='0'></a></td>
					</form>	
				</tr>
				<tr height=1><td colspan=11 class='dot-x'></td></tr>";
	}	
}else{	
$Contents .="				
				<tr height=50><td colspan=11 align=center>견적상품 내역이  존재 하지 않습니다.</td></tr>
				<tr> 
					<td colspan=11 bgcolor='#D8D8D8'></td>
				</tr>";	
	
}			

				
$Contents .="				
				<tr bgcolor=#ffffff height=25 > 
					<td align='center' class=s_td><b><font color='#333333'>총합계</font></b></td>
					<td colspan='8' class=m_td>-</td>
					<td align=center class=m_td><b> <font color='FF4E00'>".number_format($estimate_totalprice)." </font></b><font color='FF4E00'> 원</font></td>
					<td colspan='2' class=e_td>-</td>
				</tr>
				<tr> 
					<td colspan=8 bgcolor='#D8D8D8'></td>
				</tr>
			</table><br><br>
	</td>
	
</tr>
<tr height=30 >
	<td style='padding-left:0px;'>	
	<table width=100% border=0>
	<tr height=25><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>견적의뢰 고객정보</b></td>	
	</table>
	</td>
</tr>
<tr><form name='estimate_form' method='post' action='estimate.act.php' onsubmit='return CheckValue(this)'>
	<input type='hidden' name='mode' value='$mode'>
	<input type='hidden' name='est_ix' value='$est_ix'>
	<input type=hidden name='est_type' value='i'>
	<input type=hidden name='act' value='intra_insert'>		
	<td style='padding-left:20px;' >
		<table width='580' border='0' class='aa' cellpadding=5 cellspacing='0'>
			<tr>
				<td width='100' bgcolor='#ffffff' nowrap><font color='#000000'><img src='../image/title_head.gif' align='absmiddle'> <b>주문TYPE</b></font></td>
				<td  width='480' bgcolor='#ffffff'>
					<input type='radio' name='mall_ix' value='d02b37324dd0b08f6bc0f3847673e7d5' checked> 아이스크림몰
					<input type='radio' name='mall_ix' value='d02b37324dd0b08f6bc0f3847673e7d6' > 누리놀이몰
					<input type='radio' name='mall_ix' value='d02b37324dd0b08f6bc0f3847673e7d7' > 클래스몰
				</td>
			</tr>
			<tr> 
				<td width='100' bgcolor='#ffffff' nowrap><font color='#000000'><img src='../image/title_head.gif' align='absmiddle'> <b>학교명</b></font></td>
				<td  width='480' bgcolor='#ffffff'><input type='text' name='est_company' size='17' maxlength='20' class='input' value='".$db1->dt[est_company]."' title='학교명' validation='true'> <img src='../image/joinus04_search_school.jpg'align=absmiddle style='cursor:hand;' onClick=\"javascript:PoPWindow('/popup/school.php?school_type=8&school_form=estimate_form&company_yn=N&gubun=1','450','360','scpop')\" alt='학교 찾기'></td>
			</tr>
			<tr> 
				<td width='100' bgcolor='#ffffff' nowrap><font color='#000000'><img src='../image/title_head.gif' align='absmiddle'>  <b>주문자</b></font></td>
				<td  width='480' bgcolor='#ffffff'><input type='text' name='est_charger' size='17' maxlength='20' class='input' value='".$db1->dt[est_charger]."' title='주문자' validation='true'></td>
			</tr>	
			<tr>
				<td width='100' bgcolor='#ffffff' nowrap><font color='#000000'><img src='../image/title_head.gif' align='absmiddle'>  <b>주소(배송지)</b></font></td>
				<td  bgcolor='#ffffff'>
				<input type='text' name='est_zip1' size='10' maxlength='3' class='input' value='".$est_zip[0]."' title='주소(배송지)' validation='false'> - 
				<input type='text' name='est_zip2' size='10' maxlength='3' class='input' value='".$est_zip[1]."' title='주소(배송지)' validation='false'><input type='button' value='주소 찾기'  class='box' onClick=\"zipcode('4')\"  alt=\"주소 찾기\"><br>
				<input type='text' name='est_delivery_postion' size='50' maxlength='100' class='input' value='".$db1->dt[est_delivery_postion]."' title='주소(배송지)' validation='false'><br>
				<input type='text' name='est_delivery_postion2' size='50' maxlength='100' class='input' value='".$db1->dt[est_delivery_postion2]."' title='주소(배송지)' validation='false'>
				</td>
			</tr>
			<tr>
				<td bgcolor='#ffffff'>이메일</font></td>
				<td  bgcolor='#ffffff'><input type='text' name='est_email' size='50' maxlength='100' class='input' value='".$db1->dt[est_email]."' title='이메일' validation='false'></td>
			</tr>
			<tr>
				<td bgcolor='#ffffff'><font color='#000000'> 전화번호</font></td>
				<td  bgcolor='#ffffff'>
					<input type='text' name='est_tel1' size='3' maxlength='3' class='input' value='".$est_tel[0]."' title='전화번호' validation='false'> - 
					<input type='text' name='est_tel2' size='4' maxlength='4' class='input' value='".$est_tel[1]."' title='전화번호' validation='false'> - 
					<input type='text' name='est_tel3' size='4' maxlength='4' class='input' value='".$est_tel[2]."' title='전화번호' validation='false'></td>
			</tr>
			<tr>
				<td bgcolor='#ffffff'><font color='#000000'>휴대전화</font></td>
				<td  bgcolor='#ffffff'>
					<input type='text' name='est_mobile1' size='3' maxlength='3' class='input' value='".$est_mobile[0]."' title='휴대전화' validation='false'> - 
					<input type='text' name='est_mobile2' size='4' maxlength='4' class='input' value='".$est_mobile[1]."' title='휴대전화' validation='false'> - 
					<input type='text' name='est_mobile3' size='4' maxlength='4' class='input' value='".$est_mobile[2]."' title='휴대전화' validation='false'></td>
			</tr>
			<tr> 
				<td bgcolor='#ffffff'>    <font color='#000000'>관리자메모</font></td>
				<td  bgcolor='#ffffff'> <textarea name='est_etc' size='20' rows=5  cols=70 class='input' >".$db1->dt[est_etc]."</textarea></td>
			</tr>
		</table>
		<br>
		
	</td>
</tr>
<tr height=50>
	<td align=center colspan=3>
	<input type=image src='../images/estimate.intra.gif' aligb=absmiddle border=0>
	<br><br><br>
	</td>
</tr></form>
<tr> 
	<td bgcolor='D0D0D0' height='1' colspan='4'></td>
</tr>
</table>
<form action='./estimate.product.act.php'>
<input type=hidden name='ecid' value=''>
<input type=hidden name='pid' value=''>
</form>
";

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

	var frm = document.search_form;		
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
	var frm = document.search_form;
	
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
	var frm = document.search_form;

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
	var frm = document.search_form;
	
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
	//document.location.href='estimate.intra.php?view=innerview&cid='+cid+'&depth='+depth;	
	document.frames['act'].location.href='estimate.intra.php?view=innerview&cid='+cid+'&depth='+depth;
}

function deleteEstimate(act, est_ix){
	document.frames['act'].location.href='estimate.act.php?act='+act+'&est_ix='+est_ix+'&mode=$mode';
}

function num_apply(frm, pid) {
	frm.quantity.value = parseInt(frm.quantity.value) ;
	frm.sellprice.value = parseInt(frm.sellprice.value) ;
	document.frames['act'].location.href='estimate_countadd.php?PID='+pid+'&act=mod&count='+frm.quantity.value+'&sellprice='+frm.sellprice.value+'&mode=$mode&est_ix=$est_ix';
}

function num_p(frm, pid) {
	frm.quantity.value = parseInt(frm.quantity.value) + 1;
	document.frames['act'].location.href='estimate_countadd.php?PID='+pid+'&act=mod&count='+frm.quantity.value;
}

function num_m(frm, pid) {

	if(frm.quantity.value > 1) {
		frm.quantity.value = parseInt(frm.quantity.value) -1;
		document.frames['act'].location.href='estimate_countadd.php?PID='+pid+'&act=mod&count='+frm.quantity.value;
	}else {
		frm.quantity.value = 1;
		alert('1개 이상 선택하셔야 합니다    ');
		return;
	}
}

function CheckValue(frm){
	
	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}
	
	return true;
	
}

function clearAll(frm){
	var pid=document.getElementsByName('id[]');
	for(i=0;i < pid.length;i++){
			pid[i].checked = false;
	}
}

function checkAll(frm){
	var pid=document.getElementsByName('id[]');
	for(i=0;i < pid.length;i++){
			pid[i].checked = true;
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
function check_intra_product(pid) {
	var op_box=document.getElementById(pid+'_options');
	var op_objs=op_box.getElementsByTagName('select');
	if(op_objs.length>0) {
		for(var i=0;i<op_objs.length;i++) {
			if(op_objs[i].getAttribute('validation')=='true') {
				if(op_objs[i].value=='' || op_objs[i].value=='0') {
					alert(op_objs[i].getAttribute('title')+'을 선택해 주세요.');
					op_objs[i].focus();
					return false;
				}
			}
		}
	}
	var pc_box=document.getElementById(pid+'_pcount');
	var pc_objs=pc_box.getElementsByTagName('input');
	if(pc_objs[0].value=='' || pc_objs[0].value=='0') {
		alert(pc_objs[0].getAttribute('title')+'을 입력해 주세요.');
		pc_objs[i].focus();
		return false;
	}
	return true;
}
function check_intra_row(pid) {
	var fm=document.add_intra_form;
	fm.all_fix.checked=false;
	var ch_box_all=document.getElementsByName('id[]');
	for(var i=0;i<ch_box_all.length;i++) {
		ch_box_all[i].checked=false;
	}
	var ch_box=document.getElementById(pid+'_check');
	var ch_objs=ch_box.getElementsByTagName('input');
	ch_objs[0].checked=true;
	if(!check_intra_product(pid)) {
		return;
	} else {
		fm.submit();
	}
}
function check_intra_all_row() {
	var fm=document.add_intra_form;
	var ch_box_all=document.getElementsByName('id[]');
	var ch_num=0;
	for(var i=0;i<ch_box_all.length;i++) {
		if(ch_box_all[i].checked) {
			ch_num+=1;
			if(!check_intra_product(ch_box_all[i].value)) {
				return;
			}
		}
	}
	if(ch_num<1) {
		alert('추가할 상품을 선택해 주세요.');
	} else {
		fm.submit();
	}
}
</Script>";

//if(false){
if($view == "innerview"){
	
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>".PrintProductList($cid,$depth)."</body></html>";	
	
	echo "
	<Script>
	parent.document.getElementById('estimate_product_list').innerHTML = document.body.innerHTML;
	</Script>";
}else{	
	$P = new LayOut;
	$P->OnloadFunction = "onLoad('$sDate','$eDate');";//"ChangeOrderDate(document.search_frm);";
	$P->addScript = $Script;
	$P->prototype_use = false;
	$P->jquery_use = true;
	$P->strLeftMenu = order_menu();
	$P->Navigation = "HOME > 주문관리 > 오프라인 빠른견적 접수";
	$P->strContents = $Contents;
	$P->PrintLayOut();
}

function PrintProductList($cid, $depth){
	global $start,$page, $orderby, $admin_config, $DOCUMENT_ROOT, $search_text, $search_type, $sprice, $eprice, $brand2, $brand_name, $company_id, $state2, $startDate, $endDate, $cid2, $QUERY_STRING, $mode, $est_ix, $search_yn, $company;
	
	$max = 10;
	
	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}
		
	if($search_text != ""){
		$search_text = str_replace("...","",$search_text);
		$search_text = str_replace(" ","",$search_text);
		$search_text = trim($search_text);
		$where = "and (REPLACE(p.".$search_type.", ' ', '') LIKE '%".trim($search_text)."%' or p.".$search_type." LIKE '%".trim($search_text)."%') ";
	}
	
	if($sprice && $eprice){
		$where = "and sellprice between $sprice and $eprice ";
	}
	
	if($brand2 != ""){
		$where .= " and brand = ".$brand2."";
	}
	
	if($brand_name != ""){
		$where .= " and brand_name LIKE '%".trim($brand_name)."%' ";
	}
	
	if($company != ""){
		$where .= " and p.company = '".$company."'";		
	}
	
	if($company_id != ""){
		$where .= " and p.admin = '".$company_id."'";		
	}
	
	if($state2 != ""){
		$where .= " and state = ".$state2."";
	}
	
	if($startDate != "" && $endDate != "" && $regdate == 1){	
		$where .= " and  date_format(p.regdate,'%Y%m%d') between  $startDate and $endDate ";
	}
	if($cid2 != ""){
		$where .= " and r.cid LIKE '".substr($cid2,0,($depth+1)*3)."%'";
	}else{
		$where .= "";
	}

	if($mode == "" && $search_yn == "") $where = " and 1=2";

	$db = new Database;
	
	$db->query("SELECT distinct p.id,p.pname, p.admin, p.company, p.sellprice, p.reserve, p.state FROM ".TBL_MALLSTORY_PRODUCT." p, ".TBL_MALLSTORY_PRODUCT_RELATION." r where p.id = r.pid and  p.product_type not in (5,2) and p.state not in (2) and p.disp in (1,3) $where  ");
	$total = $db->total;
	
	$sql = "SELECT distinct p.id, p.pcode, p.admin, p.company, p.shotinfo, p.pname, p.sellprice, p.listprice, p.coprice, p.reserve, p.state  FROM ".TBL_MALLSTORY_PRODUCT." p, ".TBL_MALLSTORY_PRODUCT_RELATION." r where p.id = r.pid  and p.product_type not in (5,2) and p.state not in (2) and p.disp in (1,3) $where order by vieworder limit $start,$max";
	$db->query($sql);
	//echo $sql;
	$mString = "
		<form name='add_intra_form' method='post' onsubmit='/*return CheckFormValue(this);*/'>
		<input type='hidden' name='mode' value='$mode'>
		<input type='hidden' name='search_yn' value='$search_yn'>
		<input type='hidden' name='est_ix' value='$est_ix'>
		<input type='hidden' name='act' value='add'>
		<!--input type='hidden' name='id' value='".$db->dt[id]."'-->
		<input type='hidden' name='page' value='$page'>
		<input type='hidden' name='cid2' value='$cid2'>
		<input type='hidden' name='search_type' value='$search_type'>
		<input type='hidden' name='search_text' value='$search_text'>
		<input type='hidden' name='start_date' value='$start_date'>
		<input type='hidden' name='end_date' value='$end_date'>
		<input type='hidden' name='sprice' value='$sprice'>
		<input type='hidden' name='eprice' value='$eprice'>
		<input type='hidden' name='depth' value='$depth'>
		<input type='hidden' name='company_id' value='$company_id'>
		<input type='hidden' name='company' value='$company'>
		<input type='hidden' name='state2' value='$state2'>
	";
	
	
	$mString .= "<table cellpadding=0 cellspacing=0 width=100%  style='font-size:10px;'>";	
	$mString .= "<tr align=center bgcolor=#efefef height=25>
			<td class=s_td width=3%><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.add_intra_form)'></td>
			<td class=s_td width=8%>입점업체</td>
			<td class=m_td width=8% >매입처</td>
			<td class=m_td width=8% >이미지</td>
			<td class=m_td width=* >상품명</td>
			<td class=m_td width=8%>옵션</td>
			<td class=m_td width=7%>수량</td>
			<td class=m_td width=7% nowrap>정가</td>
			<td class=m_td width=7%>판매가</td>
			<td class=m_td width=7%>공급가</td>
			<td class=m_td width=7%>공급율</td>
			<td width=7% class=e_td>추가</td>
			</tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=11 align=center>등록된 상품 정보가 없습니다.</td></tr>";
	}else{
		
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);		
			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif")){
				$img_str = $admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif";
			}else{
				$img_str = "../image/no_img.gif";
			}	
			$coper = $db->dt[coprice] / $db->dt[sellprice] * 100;

			
			$mString .= "<tr height=30 bgcolor=#ffffff>
						<td class=table_td_white align=center id='".$db->dt[id]."_check'>".($db->dt[state] > 0 ? "<input type=checkbox class=nonborder name=id[] value='".$db->dt[id]."'>":"")."</td>
						<td class=table_td_white align=center>".CompanyInfo($db->dt[admin])."</td>						
						<td class=table_td_white align=center>".$db->dt[company]."</td>						
						<td class=table_td_white><img src='$img_str' align=absmiddle></td>
						<td class=table_td_white>".cut_str($db->dt[pname],60)."</td>
						<td align=right id='".$db->dt[id]."_options'>";
					$options = getOptions($db->dt[id]);
					//$options2 = getOptionName($db->dt[option_id]);
					for($s=0; $s<count($options); $s++){
						$mString .= getMakeOptionAdmin($options[$s][option_name],$db->dt[id],$options[$s][opn_ix],$options[$s][option_kind]);
					}
			$mString .= "</td>
						<td align=right id='".$db->dt[id]."_pcount'><input type='text' title='수량' name='pcount[".$db->dt[id]."]' value='1' style='width:50px;'></td>
						<td align=right >".number_format($db->dt[listprice])."</td>
						<td align=right >".number_format($db->dt[sellprice])."</td>
						<td align=right >".number_format($db->dt[coprice])."</td>
						<td align=right >".number_format($coper)."%</td>
						<td class=table_td_white align=center>".($db->dt[state] == "0" ? "일시<br>품절" : "<input type='button' value='추가' style='cursor:pointer;' onClick='check_intra_row(\"".$db->dt[id]."\")'>")."</td>
						</tr>";
			$mString .= "<tr height=1><td colspan=12 class='dot-x'></td></tr>";
		}
	}
	
	$str_page_bar = page_bar($total, $page,$max, "&".$QUERY_STRING);
	
	$mString .= "<tr align=center  height=50>
		<td colspan=10 align=left><b>* 해당되는 상품의 추가 버튼을 클릭하시면 견적서에 추가 됩니다.</b></td>
		<td colspan='2' align='right'><input type='button' value='선택상품추가' style='cursor:pointer;width:80px;' onClick='check_intra_all_row()' /></td>
	</tr>";
	$mString .= "<tr align=right bgcolor=#ffffff height=30><td colspan=12 align=left >$str_page_bar</td></tr>";
	$mString = $mString."</table>";
	$mString .= "</form>";
	
	return $mString;
	
}
function CompanyInfo($admin){
$mdb = new Database;
	$sql = "select company_name from ".TBL_MALLSTORY_COMPANYINFO." where  company_id  = '".$admin."' ";
	$mdb->query($sql);
	$mdb->fetch();
	
	return $mdb->dt[company_name];
}
function getOptionName($ix){
	$mdb = new Database;

	$mdb->query("SELECT option_div FROM ".TBL_MALLSTORY_PRODUCT_OPTION." where id = '$ix' ");	
	$mdb->fetch();
	return $mdb->dt[option_div];
}
function getOptions($pid){
	$mdb = new Database;

	$mdb->query("SELECT option_name , opn_ix, option_kind  FROM ".TBL_MALLSTORY_PRODUCT_OPTIONS." where pid = '$pid' and option_use ='1' order by opn_ix desc");	
	return $mdb->fetchall();
}
function getMakeOptionAdmin($option_name, $pid, $opn_ix="", $option_kind="b", $return_type="select",$select_option_id=""){
	global $user;
	$mdb = new Database;
	
	if($return_type=="select"){
		$sql = "select id, option_div,option_price, option_m_price,option_d_price,option_a_price,option_coprice, option_stock, option_etc1  from ".TBL_MALLSTORY_PRODUCT_OPTION." a where pid = '$pid' and opn_ix ='$opn_ix' order by id asc";
	}else{
		$sql = "select a.id, a.option_div, b.option_name  from ".TBL_MALLSTORY_PRODUCT_OPTION." a , ".TBL_MALLSTORY_PRODUCT_OPTIONS." b where a.pid = '$pid' and a.opn_ix = b.opn_ix ";
	}
	//echo $sql;
	$mdb->query($sql);
	
	
	if ($mdb->total == 0){
		//return "<input type=hidden name='options[]' value=''>";
		return "";
	}else{
		if($return_type=="select"){
			if($option_kind == "b"){			
				$mString = "<Select name=options[$pid][] id='_goods_options'  before_price=0  title='$option_name' validation='true'>";	
				$mString .= "<option value='0' stock='0' n_price='0' m_price='0' d_price='0' a_price='0' soldout='0' etc1='' >".$option_name."을 선택해주세요</option>";
			
				$i=0;
				for($i=0;$i < $mdb->total; $i++){			
					$mdb->fetch($i);
					
					if($select_option_id == $mdb->dt[id]){
						$mString .= "<option value='".$mdb->dt[id]."' stock='".$mdb->dt[option_stock]."' n_price='".$mdb->dt[option_price]."' m_price='".$mdb->dt[option_m_price]."' d_price='".$mdb->dt[option_d_price]."' a_price='".$mdb->dt[option_a_price]."' etc1='".$mdb->dt[option_etc1]."' soldout='".$mdb->dt[option_soldout]."' selected>".$mdb->dt[option_div]."".($mdb->dt[option_soldout] == 1 ? "[일시품절]":"")."</option>\n";
					}else{
						$mString .= "<option value='".$mdb->dt[id]."' stock='".$mdb->dt[option_stock]."' n_price='".$mdb->dt[option_price]."' m_price='".$mdb->dt[option_m_price]."' d_price='".$mdb->dt[option_d_price]."' a_price='".$mdb->dt[option_a_price]."' etc1='".$mdb->dt[option_etc1]."' soldout='".$mdb->dt[option_soldout]."'>".$mdb->dt[option_div]."".($mdb->dt[option_soldout] == 1 ? "[일시품절]":"")."</option>\n";
					}
								
				}
				$mString .= "</select>";
			}else if($option_kind == "p"){
				$mString = "<Select name=options[$pid][] id='_goods_options' onchange=\"/*ChangeAddPriceOption('".$user[mem_level]."',this, this.selectedIndex);*/\"  befor_price=0 title='$option_name' validation='false'>";	
				$mString .= "<option value='0' stock='0' n_price='0' m_price='0' d_price='0' a_price='0' soldout='0' etc1='' >".$option_name."</option>";
			
				$i=0;
				for($i=0;$i < $mdb->total; $i++){			
					$mdb->fetch($i);
					
					if($select_option_id == $mdb->dt[id]){
						$mString .= "<option value='".$mdb->dt[id]."' stock='".$mdb->dt[option_stock]."' n_price='".$mdb->dt[option_price]."' m_price='".$mdb->dt[option_m_price]."' d_price='".$mdb->dt[option_d_price]."' a_price='".$mdb->dt[option_a_price]."' etc1='".$mdb->dt[option_etc1]."' soldout='".$mdb->dt[option_soldout]."' selected>".$mdb->dt[option_div]."".($mdb->dt[option_soldout] == 1 ? "[일시품절]":"")."</option>\n";
					}else{
						$mString .= "<option value='".$mdb->dt[id]."' stock='".$mdb->dt[option_stock]."' n_price='".$mdb->dt[option_price]."' m_price='".$mdb->dt[option_m_price]."' d_price='".$mdb->dt[option_d_price]."' a_price='".$mdb->dt[option_a_price]."' etc1='".$mdb->dt[option_etc1]."' soldout='".$mdb->dt[option_soldout]."'>".$mdb->dt[option_div]."".($mdb->dt[option_soldout] == 1 ? "[일시품절]":"")."</option>\n";
					}
								
				}
				$mString .= "</select>";
			}else if($option_kind == "s"){
				$mString = "<Select name=options[$pid][] id='_goods_options'  title='$option_name' onchange='/*ChangeSelectOption(this)*/' validation='true'>";	
				$mString .= "<option value='0' stock='0' n_price='0' m_price='0' d_price='0' a_price='0' soldout='0' etc1='' >".$option_name."</option>";
			
				$i=0;
				for($i=0;$i < $mdb->total; $i++){			
					$mdb->fetch($i);
					
					if($select_option_id == $mdb->dt[id]){
						$mString .= "<option value='".$mdb->dt[id]."' stock='".$mdb->dt[option_stock]."' n_price='".$mdb->dt[option_price]."' m_price='".$mdb->dt[option_m_price]."' d_price='".$mdb->dt[option_d_price]."' a_price='".$mdb->dt[option_a_price]."' etc1='".$mdb->dt[option_etc1]."' soldout='".$mdb->dt[option_soldout]."' selected>".$mdb->dt[option_div]."".($mdb->dt[option_soldout] == 1 ? "[일시품절]":"")."</option>\n";
					}else{
						$mString .= "<option value='".$mdb->dt[id]."' stock='".$mdb->dt[option_stock]."' n_price='".$mdb->dt[option_price]."' m_price='".$mdb->dt[option_m_price]."' d_price='".$mdb->dt[option_d_price]."' a_price='".$mdb->dt[option_a_price]."' etc1='".$mdb->dt[option_etc1]."' soldout='".$mdb->dt[option_soldout]."'>".$mdb->dt[option_div]."".($mdb->dt[option_soldout] == 1 ? "[일시품절]":"")."</option>\n";
					}
								
				}
				$mString .= "</select>";
				//echo $mString;
			}
		}else{
			for($i=0;$i < $mdb->total; $i++){			
				$mdb->fetch($i);
				
				if($select_option_id == $mdb->dt[id]){
					return $mdb->dt[option_name] ." : ".$mdb->dt[option_div];
				}							
			}
		}
	}
	
	
	return $mString;
}
?>
