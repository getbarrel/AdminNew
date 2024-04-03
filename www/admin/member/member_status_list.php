<?
include("../class/layout.class");

$db = new MySQL;
$mdb = new MySQL;$mdb = new MySQL;

$max = 20; //페이지당 갯수

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$Script = "	
	<script language='javascript'>
	function display_zone(id){
	if(document.getElementById(id).style.display == ''){
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
		yes = confirm('회원 승인 하시겠습니까?');
		if(yes){
		window.frames['iframe_act'].location.href = 'company_member.act.php?act=change&mode=top&code='+code;
		}else{
			return;
			}
		}
	</script>";

if($authorized != ""){
	$where .= " and cu.authorized = '$authorized' ";
}

if($search_text != ""){
	if($search_type=="cmd.name"){
		$where.=" and AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
	}else{
		$where .= " and $search_type LIKE '%$search_text%' ";
	}
}

if($mall_ix){
    $where .= " and cu.mall_ix = '".$mall_ix."' ";
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

$sql = "SELECT 
			count(cu.code) as total
		FROM 
			".TBL_COMMON_USER." as cu
			inner join ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code 
		where 
			cu.mem_type = 'M'
			and cu.request_info = 'M'
			$where"; 
$db->query($sql);
$db->fetch();
$total = $db->dt[total];

$sql = "SELECT 
			cu.date,
			cu.authorized,
			cu.id,
			cu.mall_ix,
			cu.mem_type,
			cmd.*,
			AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
			AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
			AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') as tel,
			AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs
		from
			".TBL_COMMON_USER." as  cu 
			inner join ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code 
		where
			cu.mem_type = 'M'
			and cu.request_info = 'M'
			$where order by cu.date desc limit $start , $max";
$db->query($sql);

$Contents = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	<tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "본사관리 > $menu_name")."</td>
	</tr>
	<tr>
	    <td align='left' colspan=4 style='padding-bottom:16px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<col width='550px'>
			<col width='*'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".(($info_type == "member" || $info_type == "") ? "class='on' ":"").">
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
					<table id='tab_03' ".($info_type == "add" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
							$Contents .= "<a href='seller_member_list.php?info_type=add'>셀러회원 권한승인</a>";
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
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	<tr>
	    <td align='left'  > ".GetTitleNavigation("리오더신청", "고객센타 > 리오더신청")."</td>
	</tr>
	<tr>
		<td>
		<form name='searchmember'>
		<input type='hidden' name='mode' value='search'>
		<table border='0' cellpadding='0' cellspacing='0' width='100%'>
			<tr>
			<td style='width:100%;' valign=top colspan=3>
						<table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
							<col width='18%'>
							<col width='*'>";

if($_SESSION["admin_config"][front_multiview] == "Y"){
    $Contents .= "
                            <tr>
                                <td class='search_box_title' > 글로벌 회원 구분</td>
                                <td class='search_box_item' >".GetDisplayDivision($mall_ix, "select")." </td>
                            </tr>";
}
$Contents .= "
							<tr height=27>
								<th class='search_box_title' bgcolor='#efefef' width='15%' align='center'>신청처리상태 </th>
								<td class='search_box_item' width='*' align=left style='padding-left:5px;'>
									<input type='radio' id='status_a' name='authorized' value='' checked ".CompareReturnValue("",$authorized,"checked")."> <label for='status_a'>전체</label>
									<input type='radio' id='approve_y' name='authorized' value='Y' ".CompareReturnValue("Y",$authorized,"checked")."> <label for='approve_y'>승인</label>
									<input type='radio' id='approve_n' name='authorized' value='N' ".CompareReturnValue("N",$authorized,"checked")."> <label for='approve_n'>미승인</label>
									<input type='radio' id='approve_x' name='authorized' value='X' ".CompareReturnValue("X",$authorized,"checked")."> <label for='approve_x'>승인거부</label></td>
							</tr>
							<tr height='27'>
								<td class='search_box_title' bgcolor='#efefef' align=center><label for='regdate'><b>신청일</b></label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".CompareReturnValue("1",$regdate,"checked")."></td>
								<td class='search_box_item' align=left   style='padding-left:5px;'>
									".search_date('sdate','edate',$sdate,$edate)."
								</td>
							</tr>
							<tr height=27>
								<th class='search_box_title' bgcolor='#efefef' width='15%' align='center'>조건검색</th>
								<td class='search_box_item' width='*' align=left style='padding-left:5px;'>
									<select name=search_type>
										<option value='cmd.name' ".CompareReturnValue("cmd.name",$search_type,"selected").">신청회원명</option>
										<option value='cu.id' ".CompareReturnValue("cu.id",$search_type,"selected").">신청회원ID</option>
										<option value='ccd.com_name' ".CompareReturnValue("ccd.com_name",$search_type,"selected").">회사명</option>
										<option value='represent_name' ".CompareReturnValue("represent_name",$search_type,"selected").">담당자명</option>
									</select>
										<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:200px;' >
								</td>
							</tr>
							</table>
											 
									 
						 ";
	$Contents .= "
			</td>
		</tr>
		<tr height='70'>
			<td colspan=3 align=center style='padding-top:10px;'>
				<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
			</td>
		</tr>
		</table>
		</form>
		</td>
	</tr>
</table>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
$Contents .= "<!--table width='100%' border='0' cellpadding='0' cellspacing='0' align='center'>
	<tr>
		<td align='right' style='padding-bottom:5px;'><a href='re_order.excel.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a></td>
	</tr>
</table-->";
}

$Contents .= "<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center'>
	<tr>
		<td align='left' style='padding-bottom:5px;'><img src='../image/red_point.gif' border='0'> 표시된 회원은 승인요청 중인 회원입니다. '승인' 혹은 '승인거부' 처리를 하셔야합니다.</td>
	</tr>
</table>";


$Contents .= "
<form name='list_frm' action='company_member.act.php' method='post' onsubmit='return SelectUpdate(this)' enctype='multipart/form-data' target='iframe_act'>
<input type='hidden' name='search_searialize_value' value='".($_GET[mode] == "search" ? urlencode(serialize($_GET)):"")."'>
<input type='hidden' name='code[]' id='code'>
<input type='hidden' name='act' value='all_update'>
<input type='hidden' name='page_name' value='company'>

<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
	<col width='2%'>
	<col width='4%'>
	<col width='9%'>
	<col width='8%'>
	<col width='10%'>
	<col width='7%'>
	<col width='8%'>
	<col width='8%'>
	<col width='10%'>
	<col width='7%'>
	<col width='8%'>
	<col width='9%'>
	<tr bgcolor=#efefef align=center height=30 style='font-weight:600;'>
		<td align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
		<td class='s_td'>순번</td>
		<td class='m_td'>등록일</td>
		<td class='m_td'>회원구분</td>
		<td class='m_td'>신청인</td>
		<td class='m_td'>아이디</td>
		<td class='m_td'>전화번호</td>
		<td class='m_td'>핸드폰</td>
		<td class='m_td'>E-mail</td>
		<td class='m_td'>회원그룹</td>
		<td class='m_td'>처리상태</td>
		<td class='e_td'>관리</td>
	</tr>";

	if($db->total){
		for($i=0;$i<$db->total;$i++){
		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;
		if(file_exists("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/apply_company/".$db->dt["company_file"])){
			$file_name = '다운로드';
		}else $file_name = '-';

		if($db->dt[gp_ix]){
				$sql = "select gp_name from shop_groupinfo where gp_ix = '".$db->dt[gp_ix]."'";
				$mdb->query($sql);
				$mdb->fetch();

			$gp_name = $mdb->dt[gp_name];
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

        $nationality = GetDisplayDivision($db->dt['mall_ix'], "text");

		$Contents .="
			<tr height=30 align=center>
				<td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td>
				<td class='list_box_td' bgcolor='#efefef'>".$no."</td>
				<td class='list_box_td'>".$db->dt[date]."</td>
				<td class='list_box_td' bgcolor='#efefef' >(".$nationality.") ".$mem_type."</td>
				<td class='list_box_td' bgcolor='#efefef'>
					<table>
					<tr>
						<td width='15'>
							".($db->dt[authorized]=="X" || $db->dt[authorized]=="N" ? "<img src='../image/red_point.gif' border='0'> ":"")."
						</td>
						<td>
							<a href=\"javascript:PopSWindow('/admin/member/member_view.php?code=".$db->dt[code]."',985,600,'member_info')\">".$db->dt[name]."</a>
						</td>
					</tr>
					</table>
				</td>
				<td class='list_box_td' bgcolor='#efefef' >".$db->dt[id]."</td>
				<td class='list_box_td' bgcolor='#efefef' >".$db->dt[tel]."</td>
				<td class='list_box_td' bgcolor='#efefef' >".$db->dt[pcs]."</td>
				<td class='list_box_td'>".$db->dt['mail']."</td>
				<td class='list_box_td'>".$gp_name."</td>
				<td class='list_box_td' bgcolor='#efefef'>";

					if($db->dt[authorized]=="Y") {
						$Contents .= "승인(".substr($db->dt[date],0,10).")";
					}else if($db->dt[authorized]=="X") {
						$Contents .= "승인거부";
						if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
							$Contents .= "<img src='../images/".$admininfo["language"]."/btn_comform.gif' border=0 align=absmiddle onClick=\"approve_company('".$db->dt[code]."')\" title='승인'/>";
						} else {
							//Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle ></a> ";
						}
							} else {
							$Contents .= "미승인 ";
							if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
								$Contents .= "<img src='../images/".$admininfo["language"]."/btn_comform.gif' border=0 align=absmiddle onClick=\"approve_company('".$db->dt[code]."')\" title='승인'/>";
							} else {
								//Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle ></a> ";
							}
						}//member_info.php?mmode=pop&code=ebfe8c56a3ea18f3d41cf18ea5b906ce&mmode=pop
					$Contents .= "
						</td>
						<td class='list_box_td' bgcolor='#efefef'>
					<a href=\"javascript:PoPWindow('member_view.php?mmode=pop&code=".$db->dt[code]."',900,710,'member_view')\"><img src='../images/".$admininfo["language"]."//btn_crm.gif' align=absmiddle title='고객상담'></a>
					<a href=\"javascript:PoPWindow('member_info.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',900,710,'member_info')\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle alt='수정' title='수정'></a>
			
					</td>
				</tr>";
	}
		$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
	}else{
		$Contents .= "
		<tr height=50>
			<td class='list_box_td' colspan=13 align=center style='padding-top:10px;'>검색회원이 없습니다.</td>
		</tr>";
		}
		$Contents .="
		</table>";

$select = "
	<select name='update_type' onChange='view_member_num(this,\"$total\")'>
		<option value='1'>검색한 회원 전체에게</option>
		<option value='2' selected>선택한 회원 전체에게</option>
	</select>
		<input type='radio' name='update_kind' id='batch_update_pos' value='group' ".(($update_kind == "pos" || $update_kind == "") ? "checked":"")." onclick=\"ChangeUpdateForm('batch_update_pos');\"><label for='update_kind_group'>회원 승인 일괄변경</label>

				";

	$help_text .= "
	<div id='batch_update_pos' ".($update_kind == "pos" || $update_kind == "" ? "style='display:block'":"style='display:none'")." >
	<div style='padding:4px 0px 4px 0px'><img src='../images/dot_org.gif'> <b>회원 승인 여부</b> 
		<span class=small style='color:gray'>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'P')."</span></div>
		<table width='100%' border=0 cellpadding=3 cellspacing=0 class='input_table_box'>
			<col width='15%'>
			<col width='*'>
			<tr height=30>
				<td class='input_box_title'> <b>승인여부 </b></td>
				<td class='input_box_item'>
					<input type='radio' id='use_disp_1' name='use_disp' value='Y' checked><label for='use_disp_1'> 승인</label>
					<input type='radio' id='use_disp_0' name='use_disp' value='N' > <label for='use_disp_0'> 미승인</label> 
					<input type='radio' id='use_disp_2' name='use_disp' value='X' > <label for='use_disp_2'> 승인거부</label>
				</td>
			</tr>
		</table>
		<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50>
			<td colspan=4 align=center>";
			if( checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
			$help_text .= "
				<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
			} 
		$help_text .= "
			</td> 
		</tr>
		</table>
		</div>
	</form>";
	

$select_contents .= "".HelpBox($select, $help_text,750)."</form>";

$Contents .= "<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >";
$Contents .="<tr height=40><td  align=right>".page_bar($total, $page, $max,$query_string,"")."</td></tr>";
//$Contents .= "<tr><td style='padding-bottom:10px;' colspan=9>".HelpBox("사업자회원신청 관리", $help_text)."</td></tr>";
$Contents = $Contents."<tr><td>".$select_contents."<br></td></tr>";
$Contents .="</table>
<br>";

$P = new LayOut();
$P->addScript = "<script language='javascript' src='member.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n<script language='JavaScript' src='../webedit/webedit.js'></script>\n".$Script;
$P->OnloadFunction = "";	//init();
$P->strLeftMenu = member_menu();
$P->jquery_use = true;
$P->prototype_use = false;
$P->Navigation = "HOME > 회원관리 > 회원승인관리";
$P->title = "회원승인관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();



?>