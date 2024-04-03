<? 
include("../class/layout.class");

$max = 15; //페이지당 갯수

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

if ($ToYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
	$before1month = mktime(0, 0, 0, date("m")-1  , 21, date("Y"));
	$vdate = date("Ymd", time());
	$today = date("Ymd", time());
	$startday = 2;
	$lastday = date('t', strtotime($today));
	
	$sDate = date("Y/m/d", time()-84600*(date("d")));
	$eDate = date("Y/m/".$lastday);
	
	$startDate = date("Ymd", time()-84600*(date("d")));
	$endDate = date("Ymd".$lastday);
}else{
	$vdate = date("Ymd", time());
	$today = date("Ymd", time());
	
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	

	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;

}




$db = new Database;
$db2 = new Database;
$mdb = new Database;


$db2->query("SELECT * FROM ".TBL_MALLSTORY_SHOPINFO." WHERE mall_ix='".$admininfo[mall_ix]."' ");
$db2->fetch();

$account_priod = $db2->dt[account_priod];
$where = " where es_div is not null ";
if($estimate_view_type == ""){
	$where .= "and es_div = 'S' ";
}else if($estimate_view_type == "m"){
	$where .= "and es_div = 'M' ";
}


if($ToYY != "" && $regdate == "1"){
	$where .= "and date_format(lo.regdate,'%Y%m%d') between $startDate and $endDate ";
}

if($search_text != ""){
	$where .= " and $search_type LIKE '%$search_text%' ";
}

if($es_status != ""){
	$where .= " and es_status = '$es_status' ";
}

	/*
	$sql = "select date_format(lo.regdate,'%Y-%m-%d') as reg_date from mallstory_large_order lo, ".TBL_MALLSTORY_MEMBER." m, mallstory_comm_sc cs WHERE m.sc_code = cs.sc_code and m.code= lo.code
		$where 
	order by regdate desc ";
	//echo $sql;
	*/
	//* regdate 정렬시 최신글이 위로 노출되지 않아 정렬기준을 lo_ix 값으로 변경*/
	$sql = "select date_format(lo.regdate,'%Y-%m-%d') as regdate ,lo.lo_ix
			from mallstory_large_order lo left join ".TBL_MALLSTORY_MEMBER." m on m.code= lo.code 
			left join mallstory_comm_sc cs on m.sc_code = cs.sc_code 
			$where 
			order by lo_ix desc ";
	//echo $sql;
$db->query($sql);
$total = $db->total;

	$sql = "select lo.*, date_format(lo.regdate,'%Y-%m-%d') as regdate,lo.lo_ix, cs.sc_nm, m.name, m.id from mallstory_large_order lo, ".TBL_MALLSTORY_MEMBER." m, mallstory_comm_sc cs WHERE m.sc_code = cs.sc_code and m.code= lo.code
		$where 
	order by regdate desc LIMIT $start, $max";
	//echo $sql;
	$sql = "select lo.*, date_format(lo.regdate,'%Y-%m-%d') as regdate, cs.sc_nm, m.name, m.id 
			from mallstory_large_order lo left join ".TBL_MALLSTORY_MEMBER." m on m.code= lo.code
			left join mallstory_comm_sc cs  on m.sc_code = cs.sc_code
			 
			$where 
			order by lo_ix desc LIMIT $start, $max";
	//echo nl2br($sql);

$db->query($sql);
$large_order = $db->fetchall();

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
$Contents = "
<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'>
	<tr>
		<td align='left' colspan=4>".GetTitleNavigation("온라인 견적 리스트", "주문관리 > 온라인 견적(셀프&맞춤) 리스트")."</td>
	</tr>	
	<tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	        <div class='tab'>
	            <table class='s_org_tab'>
	                <tr>
	                    <td class='tab'>
	                        <table id='tab_01'  ".($estimate_view_type == "" ? "class='on'":"").">
	                            <tr>
	                                <th class='box_01'></th>
	                                <td class='box_02' onclick=\"document.location.href='?estimate_view_type='\">셀프견적리스트</td>
	                                <th class='box_03'></th>
	                            </tr>
	                        </table>
	                        <table id='tab_02' ".($estimate_view_type == "m" ? "class='on'":"").">
	                            <tr>
	                                <th class='box_01'></th>
	                                <td class='box_02' onclick=\"document.location.href='?estimate_view_type=m'\">맞춤견적리스트</td>
	                                <th class='box_03'></th>
	                            </tr>
	                        </table>
	                    </td>
	                    <td class='btn'>
	
	                    </td>
	                </tr>
	            </table>
	        </div>
	    </td>
	</tr>
	<tr>
		<td align='left' colspan=4>".colorCirCleBox("#efefef","100%","<div style='padding:5 5 5 15;'><img src='../image/title_head.gif' align=absmiddle><b> 정산검색</b><span class=small> &nbsp;&nbsp;&nbsp;</span></div>")."</td>
	</tr>
	<form name='search_frm' method='get' action='' style='display:inline;'>
	<input type=hidden name='estimate_view_type' value='$estimate_view_type'>
	<tr height=27>
	  <td  align=left><label for='regdate'><img src='../image/ico_dot.gif' align=absmiddle> 견적일</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.search_frm);' ".CompareReturnValue("1",$regdate,"checked")."></td>
	  <td align=left colspan=3 style='padding-left:5px;'>
		<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>		
		<tr>					
			<TD width=190 nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월 <SELECT name=FromDD></SELECT> 일 </TD>
			<TD width=20 align=center> ~ </TD>
			<TD width=190 nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월 <SELECT name=ToDD></SELECT> 일</TD>
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
	<tr hegiht=1><td colspan=4 class='dot-x'></td></tr>";
	
	
$Contents .= "
	<tr>
		<td width='20%'><img src='../image/ico_dot.gif' align=absmiddle> 업체검색  </td>
		<td width='80%' colspan=3>".CompanyList3($company_name,"")."</td>
	</tr>
	<tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
	<tr>
		<td width='20%'><img src='../image/ico_dot.gif' align=absmiddle> 처리상태  </td>
		<td width='80%' colspan=3>
		<select name='es_status'>
			<option value='' >전체</option>
			<option value='N' ".CompareReturnValue("N",$es_status,"selected").">견적대기</option>
			".($estimate_view_type == "m" ? "
			<option value='R' ".CompareReturnValue("R",$es_status,"selected").">견적진행</option>
			<option value='A' ".CompareReturnValue("A",$es_status,"selected").">견적완료</option>":"")."
			<option value='Y' ".CompareReturnValue("Y",$es_status,"selected").">주문완료</option>
		</select>
		</td>
	</tr>
	<tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
	<tr>
		<td width='20%'><img src='../image/ico_dot.gif' align=absmiddle> 조건검색  </td>
		<td width='80%' colspan=3>
		<select name='search_type'>
		".($estimate_view_type == "" ? "<option value='cs.sc_nm' ".CompareReturnValue("cs.sc_nm",$search_type).">학교명</option>" :"
			<option value='es_sc_nm' ".CompareReturnValue("es_sc_nm",$search_type).">학교명</option>")."
		".($estimate_view_type == "" ? "<option value='m.name' ".CompareReturnValue("m.name",$search_type).">이름</option>" :"
			<option value='es_damdang' ".CompareReturnValue("es_damdang",$search_type).">이름</option>")."	
			<option value='m.id' ".CompareReturnValue("m.id",$search_type).">아이디</option>
		</select>  <input type='text' name='search_text' size=20 value='$search_text'>
		</td>
	</tr>
	<tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
	<tr bgcolor=#ffffff>
		<td colspan=4 align=right><input type='image' src='../image/bt_search.gif' border=0 style='cursor:hand;border:0px;' ></td>
	</tr>
	</form>
	<tr height=50><td colspan=4></td></tr>
		
		<form name=listform method=post action='estimate.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'>
			<input type='hidden' name='act' value='es_status_update'>
			".($estimate_view_type == "" ? "" :"
				<td colspan='4' bgcolor=#ffffff align=right>	<input type=image src='../image/btc_modify.gif' align=absmiddle></td>")."
	<tr>
		<td align='left' colspan=4 > ".colorCirCleBox("#efefef","100%","<div style='padding:5 5 5 15;'><img src='../image/title_head.gif' align=absmiddle><b>온라인 ".($estimate_view_type == "" ? "셀프":"맞춤")."견적리스트</b><span class=small> &nbsp;&nbsp;&nbsp;</span></div>")."</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td colspan=4 align=right>
						
			<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center'>
				<tr>
					<td width='2%' class='s_td'><input type=checkbox  name='all_fix' onclick='fixAll(document.listform)'></td>
					<td width='5%' align='center' class='m_td small'><font color='#000000'><b>NO</b></font></td>
					<td width='10%' align='center' class='m_td small'><font color='#000000'><b>견적일자</b></font></td>					
					<td width='10%' align='center' class='m_td small'><font color='#000000'><b>학교명</b></font></td>
					<td width='10%' align='center' class='m_td small' nowrap><font color='#000000'><b>담당자</b></font></td>
					<td width='12%' align='center' class='m_td small' nowrap><font color='#000000'><b>견적종류</b></font></td>
					<!--td width='12%' align='center' class='m_td small' nowrap><font color='#000000'><b>상품분류</b></font></td-->
					<td width='8%' align='center' class='m_td small' nowrap><font color='#000000'><b>견적금액</b></font></td>
					<td width='8%' align='center' class='m_td small' nowrap><font color='#000000'><b>예산금액</b></font></td>
					<td width='10%' align='center' class='m_td small' nowrap><font color='#000000'><b>처리상태</b></font></td>
					<td width='10%' align='center' class='e_td small' nowrap><font color='#000000'><b>견적보기</b></font></td>
					
				</tr>";
	
	
	
	if(count($large_order)){
		for ($i = 0; $i < count($large_order); $i++){
			$db->fetch($i);
			
			if($db->dt[es_div] == "M") $es_div = "맞춤견적";
			else $es_div = "셀프견적";
			
			if($db->dt[es_status] == "Y") $es_status = "주문완료";
			else if($db->dt[es_status] == "R") $es_status = "견적진행";
			else if($db->dt[es_status] == "A") $es_status = "견적완료";
			else $es_status = "견적대기";
			
			if($estimate_view_type==""){
				$db->dt[es_sc_nm] = $db->dt[sc_nm];
				$db->dt[es_damdang] = $db->dt[name];
			}
			if($db->dt[id]==""){
				$db->dt[id] = "비회원";
			}
			
			$Contents .= "
				<tr onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\" height=30>
					<td bgcolor='#EAEAEA' nowrap><input type=checkbox name='lo_ix[]' id='lo_ix' value='".$db->dt[lo_ix]."'></td> 
					<td align='center' nowrap>".($i+1)."</td>
					<td bgcolor='#EAEAEA' align='center' nowrap>".$db->dt[regdate]."</td>		
					<td align='center' nowrap>".$db->dt[es_sc_nm]."</td>
					".($db->dt[code] == "" ? "<td bgcolor='#EAEAEA' align='center' nowrap>".$db->dt[es_damdang]."(".$db->dt[id].") </td>" :"
					<td bgcolor='#EAEAEA' align='center' nowrap  onClick=\"PopSWindow('../member/member_view.php?code=".$db->dt[code]."',950,500,'member_info')\" style='cursor:pointer;'>".$db->dt[es_damdang]."(".$db->dt[id].") </td>")."
					<td bgcolor='#ffffff' align='center' nowrap>".$es_div." </td>
					<!--td bgcolor='#EAEAEA' align='center' nowrap>".$db->dt[es_damdang]." </td-->
					<td bgcolor='#EAEAEA' align='center' nowrap>".number_format($db->dt[es_amount])." 원</td>
					<td bgcolor='#ffffff' align='center' nowrap>".number_format($db->dt[es_price])." 원</td>
					".($estimate_view_type!="m"? "<td bgcolor='#EAEAEA' align='center' nowrap>".$es_status." </td>":"							
					<td bgcolor='#ffffff' align='center' nowrap><select name='es_status[".$db->dt[lo_ix]."]'>
						<option style='background-color:#f59db3;' value='N' ".CompareReturnValue("N",$db->dt[es_status],"selected").">견적대기</option>
						<option value='R' ".CompareReturnValue("R",$db->dt[es_status],"selected").">견적진행</option>
						<option value='A' ".CompareReturnValue("A",$db->dt[es_status],"selected").">견적완료</option>
						<option value='Y' ".CompareReturnValue("Y",$db->dt[es_status],"selected").">주문완료</option>
					</select></td>")."				
					".($estimate_view_type=="m"? "<td bgcolor='#ffffff' align='center' nowrap><input type='button' value='보기' onClick=\"PopSWindow('estimate_view.php?lo_ix=".$db->dt[lo_ix]."',950,600,'member_info')\">
					</td>" : "<td bgcolor='#EAEAEA' align='center' nowrap><input type='button' value='보기' onClick=\"PopSWindow('/popup/es.php?est_ix=".$db->dt[est_ix]."&order_type=estimate&lo_ix=".$db->dt[lo_ix]."',950,500,'member_info')\"></td>")."
				
				</tr>
				<tr height=1><td colspan=10 background='../image/dot.gif'></td></tr>";
			$total_delivery_price = $total_delivery_price + $db->dt[es_price]; 
				
		}
	}else{
		$Contents .= "
				<tr height=50><td colspan=10 align=center>견적 내용이 없습니다</td></tr>
				<tr height=1><td colspan=10 background='../image/dot.gif'></td></tr>";
	}
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >정산완료후의 정산일자를 선택한 후 검색 합니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >검색하신 해당 정산일에 대한 배송비 내역이 노출됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >업체명을 클릭하시면 배송비 정산내역에 대한 상세 내역을 확인하실수 있습니다</td></tr>
	<!--tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >배송비 정산내역이 확인 되었으면 정산확인 버튼을 클릭합니다. 정산이 완료된 금액은 나의 통장으로 입금 되게 됩니다</td></tr-->
</table>
";



$p_query_str=str_replace("&nset=$nset&page=$page","",$QUERY_STRING);
$p_query_str=str_replace("nset=$nset&page=$page&","",$p_query_str);
//echo $p_query_str;
$Contents .= "	</table>
	  	</td>
	  </tr>
	  <tr height=40>
		<td colspan='4' align='center' >&nbsp;".page_bar($total, $page, $max,"&".$p_query_str,"")."&nbsp;</td>
	  </tr>
	  <tr><td colspan=4>".HelpBox("정산정책관리", $help_text)."</td></tr>
	  </table></form><br>
	  ";
	  



/*				
$Contents .= "	</table>
	  	</td>
	  </tr>
	  <tr height=40>
		<td colspan='4' align='center' >&nbsp;".page_bar($total, $page, $max,"&".$QUERY_STRING,"")."&nbsp;</td>
	  </tr>
	  <tr><td colspan=4>".HelpBox("정산정책관리", $help_text)."</td></tr>
	  </table><br>
	  ";
	 */ 

$Script = "<script lanaguage='javascript'>
function AccountDelivery(oid,company_id, edate){
	var frm = document.account_delivery_frm;
	
	if(confirm('배송비 정산을 하시겠습니까?')){
		frm.company_id.value = company_id
		frm.oid.value = oid;
		frm.submit();
	}
}
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


function clearAll(frm){
		for(i=0;i < frm.lo_ix.length;i++){
				frm.lo_ix[i].checked = false;
		}
}

function checkAll(frm){
       	for(i=0;i < frm.lo_ix.length;i++){
				frm.lo_ix[i].checked = true;
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

function init_date(FromDate,ToDate, dType) {
	var frm = document.search_frm;

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


function select_date(FromDate,ToDate,dType) {
	var frm = document.search_frm;
	
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
	var frm = document.search_frm;
	
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

</script>";
	


$P = new LayOut();
$P->addScript = "<script language='javascript' src='account.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "onLoad('$sDate','$eDate');ChangeRegistDate(document.search_frm);";
$P->strLeftMenu = order_menu();
$P->Navigation = "HOME > 주문관리 > 온라인 견적";
$P->strContents = $Contents;
echo $P->PrintLayOut();



?>