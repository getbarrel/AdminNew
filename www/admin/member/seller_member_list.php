<?
include("../class/layout.class");

$max = 20; //페이지당 갯수

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}

$db = new MySQL;
$db2 = new MySQL;
$mdb = new MySQL;

$Script = "	<script language='javascript'>
function display_zone(id)
{
	if(document.getElementById(id).style.display == '')
	{
		document.getElementById(id).style.display = 'none';
	}else{
		document.getElementById(id).style.display = '';
	}
}

function ProductQnaDelete(cm_ix){
	if(confirm('사업자 신청 정보를 삭제하시겠습니까?')){
		window.frames['act'].location.href='apply_company_member.act.php?act=delete&cm_ix='+cm_ix;
	}
}

function ChangeRegistDate(frm){
	if(frm.regdate.checked){
		$('input[name=sdate]').attr('disabled',false);
		$('input[name=edate]').attr('disabled',false);

	}else{
		$('input[name=sdate]').attr('disabled',true);
		$('input[name=edate]').attr('disabled',true);
	}
}

function init(){

	var frm = document.searchmember;
	onLoad('$sDate','$eDate');
";

$Script .= "
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

function go_con_del(cm_ix){
	yes = confirm('정말로 삭제하시겠습니까?');
	if(yes){
		window.frames['act'].location.href = 'apply_company_member.act.php?act=delete&mode=top&cm_ix='+cm_ix;
	}else{
		return;
	}
}

function approve_company(code) {
	yes = confirm('셀러회원 승인 하시겠습니까?');
	if(yes){
		window.frames['iframe_act'].location.href = 'company_member.act.php?act=seller_change&mode=top&code='+code;
	}else{
		return;
	}
}

function approve_company_cancel(code) {
	yes = confirm('셀러회원 승인거부 하시겠습니까?');
	if(yes){
		window.frames['iframe_act'].location.href = 'company_member.act.php?act=seller_change_cancel&mode=top&code='+code;
	}else{
		return;
	}
}

function download_img(file_name){
	
window.frames['iframe_act'].location.href = 'download.php?file_name='+file_name;

}

</script>";

if($authorized != ""){
	$where .= " and cu.authorized = '$authorized' ";
}

if($auth != ""){
	$where .= " and cu.auth = '$auth' ";
}

if($search_text != ""){
	if($search_type=="cmd.name"){
		$where.=" and AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
	}else{
		$where .= " and $search_type LIKE '%$search_text%' ";
	}
}

$startDate = $sdate;
$endDate = $edate;

if($regdate == '1'){	//신청일
	if($startDate != "" && $endDate != ""){
		if($db->dbms_type == "oracle"){
			$where .= " and  to_char(cu.request_date_ , 'YYYY-MM-DD') between '".$startDate."' and '".$endDate."' ";
			$count_where .= " and  to_char(cu.request_date_ , 'YYYY-MM-DD') between '".$startDate."' and '".$endDate."' ";
		}else{
			$where .= " and date_format(cu.request_date,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
			$count_where .= " and date_format(cu.request_date,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
		}
	}
}

//and u.authorized = 'N'
$sql = "SELECT 
			count(cu.code) as total
		FROM 
			".TBL_COMMON_USER." as  cu 
			inner join ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code 
			inner join common_company_detail as ccd on (cu.company_id = ccd.company_id)
		where 
			cu.request_info = 'S'
			$where
"; 

$db->query($sql);
$db->fetch();
$total = $db->dt[total];


$sql = "SELECT 
		cu.date,
		cu.authorized,
		cu.id,
		cu.auth,
		cu.request_info,
		cu.request_yn,
		cu.mem_type,
		cu.mem_div,
		cmd.*,
		ccd.com_name,
		ccd.seller_auth,
		ccd.company_id,
		csd.seller_cid,
		csd.seller_msg,
		ci.cname,
		AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
		AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
		AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') as tel,
		AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs
	from 
		".TBL_COMMON_USER." as cu 
		inner join ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code 
		inner join common_company_detail as ccd on (cu.company_id = ccd.company_id)
		inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
		left join shop_category_info as ci on (ci.cid = csd.seller_cid)
	where 
		cu.request_info = 'S'
		$where order by cu.date desc limit $start , $max";
$db->query($sql);

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$mstring = "
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
					<table id='tab_01' ".(($info_type == "member") ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='member_status_list.php?info_type=member'>일반회원승인</a> </td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".(($info_type == "list") ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='company_member_list.php?info_type=list'>사업자회원승인</a> </td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_03' ".($info_type == "add"  || $info_type == "" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
							$mstring .= "<a href='seller_member_list.php?info_type=add'>셀러회원 권한승인</a>";
						$mstring .= "
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

$mstring .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	<tr>
		<td align='left' style='padding-bottom:10px;'> ".GetTitleNavigation("리오더신청", "고객센타 > 리오더신청")."</td>
	</tr>
	<tr>
		<td>
		<form name='searchmember'>
		<input type='hidden' name='mode' value='search'>
		<table border='0' cellpadding='0' cellspacing='0' width='100%'>
			<tr>
			<td style='width:100%;' valign=top colspan=3>
				<table width=100%  border=0 cellpadding='0' cellspacing='0'>
					<tr>
						<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>
							<table class='box_shadow' style='width:100%;' align=left border=0 cellpadding='0' cellspacing='0'>
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
												<col width='18%'>
												<col width='*'>
												<tr height='27'>
													<th class='search_box_title' bgcolor='#efefef' align='center'>처리상태 : </th>
													<td class='search_box_item'>
														<input type='radio' id='status_a' name='auth' value='' checked ".CompareReturnValue("",$auth,"checked")."> <label for='status_a'>전체</label>
														<input type='radio' id='approve_y' name='auth' value='4' ".CompareReturnValue("4",$auth,"checked")."> <label for='approve_y'>승인</label>
														<input type='radio' id='approve_n' name='auth' value='0' ".CompareReturnValue("0",$auth,"checked")."> <label for='approve_n'>미승인</label>
														<!--<input type='radio' id='approve_X' name='auth' value='X' ".CompareReturnValue("X",$auth,"checked")."> <label for='approve_X'>승인거부</label>--></td>
												</tr>
												<tr height='27'>
													<td class='search_box_title' bgcolor='#efefef' align=center><label for='regdate'><b>신청일</b></label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".(($regdate==1)?"checked":"")."></td>
													<td class='search_box_item' align=left style='padding-left:5px;'>
														".search_date('sdate','edate',$sdate,$edate)."
													</td>
												</tr>
												<tr height='27'>
													<th class='search_box_title' bgcolor='#efefef' width='150' align='center'>조건검색 : </th>
													<td class='search_box_item'>
													<select name=search_type>
														<option value='cmd.name' ".CompareReturnValue("cmd.name",$search_type,"selected").">신청회원명</option>
														<option value='cu.id' ".CompareReturnValue("cu.id",$search_type,"selected").">신청자ID</option>
														<option value='ccd.com_name' ".CompareReturnValue("ccd.com_name",$search_type,"selected").">회사명</option>
														<option value='represent_name' ".CompareReturnValue("represent_name",$search_type,"selected").">담당자명</option>
													</select>
													<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:200px;' >
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
				</table>
			</td>
		</tr>
		<tr >
			<td colspan=3 align=center style='padding:10px 0 20px 0'>
				<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
			</td>
		</tr>
		</table>
		</form>
		</td>
	</tr>
</table>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
$mstring .= "<!--table width='100%' border='0' cellpadding='0' cellspacing='0' align='center'>
	<tr>
		<td align='right' style='padding-bottom:5px;'><a href='re_order.excel.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a></td>
	</tr>
</table-->";
}//iframe_act

$mstring .= "<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center'>
	<tr>
		<td align='left' style='padding-bottom:5px;'><img src='../image/red_point.gif' border='0'> 표시된 회원은 승인요청 중인 회원입니다. '승인' 혹은 '승인거부' 처리를 하셔야합니다.</td>
	</tr>
</table>";

$mstring .= "
<form name='list_frm' action='company_member.act.php' method='post' onsubmit='return SelectUpdate(this)' enctype='multipart/form-data' target='iframe_act'>
<input type='hidden' name='search_searialize_value' value='".($_GET[mode] == "search" ? urlencode(serialize($_GET)):"")."'>
<input type='hidden' name='code[]' id='code'>
<input type='hidden' name='act' value='all_seller_update'>
<input type='hidden' name='page_name' value='seller'>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
	<col width='1%'>
	<col width='3%'>
	<!--<col width='10%'>
	<col width='5%'>-->
	<col width='9%'>
	<col width='7%'>
	<col width='7%'>
	<col width='*'>
	<col width='8%'>
	<col width='8%'>
	<col width='12%'>

	<col width='7%'>
	<col width='8%'>
	<col width='12%'>
	<col width='10%'>
	<tr bgcolor=#efefef align=center height=30 style='font-weight:600;'>
		<td align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
		<td class='s_td'>번호</td>
		<td class='m_td'>등록일</td>
		<!--<td class='m_td' style='padding:1px;'>요청시<br>회원타입</td>
		<td class='m_td' style='padding:1px;'>현재<br>회원타입</td>-->
		<td class='m_td'>신청인</td>
		<td class='m_td'>아이디</td>
		<td class='m_td'>상호명</td>
		<td class='m_td'>전화번호</td>
		<td class='m_td'>핸드폰</td>
		<td class='m_td'>E-mail</td>
		<td class='m_td'>주요상품군</td>
		<td class='m_td'>사업자첨부파일</td>
		<td class='m_td'>처리상태</td>
		<td class='e_td'>관리</td>
	</tr>";

if($db->total){
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;
		if(file_exists("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/apply_company/".$db->dt["company_file"])){
			$file_name = '다운로드';
		}else {
			$file_name = '-';
		}

		if($db->dt[mem_div] == "S"){
			$mem_div_text = "셀러";
		}else if($db->dt[mem_div] == "D"){
			$mem_div_text = "기타";
		}else if($db->dt[mem_div] == "MD"){
			$mem_div_text = "MD관리자";
		}
		
		if($db->dt[company_id]){
			$sql = "select * from common_company_file where company_id = '".$db->dt[company_id]."' and sheet_name = 'business_file'";
			$db2->query($sql);
			$db2->fetch();
			$business_file = $db2->dt[sheet_value];

			$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$db->dt[company_id];
		}

		$mstring .="<tr height=30 align=center>
					<td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td>
					<td class='list_box_td' bgcolor='#efefef'>".$no."</td>
					<td class='list_box_td'>".$db->dt[date]."</td>
					<!--<td class='list_box_td'>기타</td>
					<td class='list_box_td'>".$mem_div_text."</td>-->
					<td class='list_box_td' bgcolor='#efefef' >
						<table>
						<tr>
							<td width='15'>
								".($db->dt[auth]!="4" ? "<img src='../image/red_point.gif' border='0'> ":"")."
							</td>
							<td>
								<a href=\"javascript:PopSWindow('/admin/member/member_view.php?code=".$db->dt[code]."',985,600,'member_info')\">".$db->dt[name]."</a>
							</td>
						</tr>
						</table>
					</td>
					<td class='list_box_td'>".$db->dt[id]."</td>
					<td class='list_box_td'>".$db->dt[com_name]."</td>
					<td class='list_box_td' bgcolor='#efefef' >".$db->dt[tel]."</td>
					<td class='list_box_td' bgcolor='#efefef' >".$db->dt[pcs]."</td>
					<td class='list_box_td'>".$db->dt['mail']."</td>
					<td class='list_box_td'><span style='padding-left:2px;cursor:pointer;' class='helpcloud' help_width='100' help_height='35' help_html='".$db->dt['seller_msg']."'>".$db->dt['cname']."</span>
					</td>
					<td class='list_box_td' bgcolor='#efefef'>";
			if(is_file($path."/".$business_file)){
		$mstring .="<input type='button' name='file_down' value='다운로드 받기' onclick=\"download_img('".$path."/".$business_file."')\" class='textbox' style='cursor: pointer;'>";
			}else{
		$mstring .="-";
			}
		$mstring .="
					</td>
					<td class='list_box_td' bgcolor='#efefef'>";

					if($db->dt[auth]=="4") {
						$mstring .= "승인<br>(".substr($db->dt[date],0,10).")";
					}else {
						//$mstring .= "미승인 ";
						if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
							$mstring .= "<img src='../images/".$admininfo["language"]."/seller_change_cancel.gif' border=0 align=absmiddle onClick=\"approve_company_cancel('".$db->dt[code]."')\" title='승인' style='cursor:pointer;'/>&nbsp;";
							$mstring .= "<img src='../images/".$admininfo["language"]."/btn_comform.gif' border=0 align=absmiddle onClick=\"approve_company('".$db->dt[code]."')\" title='승인' style='cursor:pointer;'/>";
						} else {
							//$mstring .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle ></a> ";
						}
					}
					$mstring .= "</td>
					<td class='list_box_td' bgcolor='#efefef'>
					<a href=\"javascript:PoPWindow('member_view.php?mmode=pop&code=".$db->dt[code]."',900,710,'member_view')\"><img src='../images/".$admininfo["language"]."//btn_crm.gif' align=absmiddle title='고객상담'></a>
					<a href=\"javascript:PoPWindow('../seller/company.add.php?company_id=".$db->dt[company_id]."&code=".$db->dt[code]."&mmode=pop',900,710,'member_info')\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle alt='수정' title='수정'></a>
					</td>
				</tr>";
	}//<a href='company.add.php?company_id=".$db->dt[company_id]."&code=".$db->dt[code]."&mmode=pop'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align='absmiddle'></a>
				
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}else{
	$mstring .= "<tr height=50><td class='list_box_td' colspan=15 align=center style='padding-top:10px;'>검색회원이 없습니다.</td></tr>";
}
$mstring .="</table>";

$select = "
	<select name='update_type' >
		<option value='2' selected>선택한 회원 전체에</option>
		<option value='1' >검색한 회원 전체에</option>
	</select>";

$help_text .= "
<div id='batch_update_pos' ".($update_kind == "pos" || $update_kind == "" ? "style='display:block'":"style='display:none'")." >
<div style='padding:4px 0px 4px 0px'><img src='../images/dot_org.gif'> <b>셀러 회원 승인, 미승인</b> <span class=small style='color:gray'>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'P')."</span></div>
<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
<col width=160>
<col width='*'>
	<tr height=30>
	<td class='input_box_title'> <b>승인여부 </b></td>
	<td class='input_box_item'>
	<input type='radio' name='use_disp' value='4' id='use_disp_1' checked><label for='use_disp_1'> 승인</label>
	<input type='radio' id='use_disp_0' name='use_disp' value='0' > <label for='use_disp_0'> 미승인</label>
	<!--<input type='radio' id='use_disp_2' name='use_disp' value='0' > <label for='use_disp_2'> 승인거부</label>-->
	</td>
</tr>
</table>";

if( checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$help_text .= "
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50><td colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
	</table></div>
	</form>";
}

$select_contents .= "".HelpBox($select, $help_text,600)."";

$mstring .= "<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >";
$mstring .="<tr height=40><td  align=right>".page_bar($total, $page, $max,$query_string,"")."</td></tr>";
//$mstring .= "<tr><td style='padding-bottom:10px;' colspan=9>".HelpBox("사업자회원신청 관리", $help_text)."</td></tr>";
$mstring = $mstring."<tr><td>".$select_contents."<br></td></tr>";
$mstring .="</table>
<br>";

$Contents = $mstring;

$P = new LayOut;
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->strLeftMenu = member_menu();
$P->OnloadFunction = "";	//init();
$P->Navigation = "회원관리 > 셀러회원 권한승인";
$P->title = "셀러회원 권한승인";
$P->strContents = $Contents;
$P->PrintLayOut();
?>