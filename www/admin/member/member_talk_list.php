<?
/*
* 만든이 : JBG 2014-05-30 
* 오류나 버그수정시에는 주석을 남겨주세요 ~_~ 
*/

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
$mdb = new MySQL;
$odb = new MySQL;

	
	//처리상태값
	function talk_state($state){
		switch($state){
			case 'W' : $talk_state = "접수중";
						break;
			case 'I' : $talk_state = "처리중";
						break;
			case 'D' : $talk_state = "처리지연";
						break;
			case 'F' : $talk_state = "처리완료";
						break;
			case 'C' : $talk_state = "처리취소";
						break;
		}
		return $talk_state;
	}

	//1차그룹2차그룹 검색
	if($search_parent_group_ix != "" && $search_group_ix == ""){
		$where .= " and (abg.group_ix = '".$search_parent_group_ix."' or abg.parent_group_ix = '".$search_parent_group_ix."') ";
	}else if($search_parent_group_ix != "" && $search_group_ix != ""){
		$where .= " and abg.parent_group_ix = '".$search_parent_group_ix."' ";
	}

	//검색 1주일단위 디폴트
	if ($startDate == ""){
		$before7day = mktime(0, 0, 0, date("m")  , date("d")-7, date("Y"));

		$startDate = date("Y-m-d", $before7day);
		$endDate = date("Y-m-d");
	}

	//가입/등록일 검색 
	if($orderdate && $mode=='search'){
		$where .= "and smt.regdate between '$startDate 00:00:00' and '$endDate 23:59:59' ";
	}

	//회원그룹 검색 
	if($user_group){
		$where .= "AND smt.user_group = '$user_group'";
	}

	//회원레벨 검색 
	if($level_ix){
		$where .= "AND smt.user_level = '$level_ix'";
	}

	if($mall_ix){
        $where .=" and cu.mall_ix = '".$mall_ix."' ";
    }

	//회원구분 검색
	if(is_array($user_type)){
		for($i=0;$i < count($user_type);$i++){
			if($user_type[$i] != ""){
				if($user_type_str == ""){
					$user_type_str .= "'".$user_type[$i]."'";
				}else{
					$user_type_str .= ",'".$user_type[$i]."' ";
				}
			}
		}

		if($user_type_str != ""){
			$where .= " AND smt.user_type in ($user_type_str) ";
		}
	}else{
		if($user_type){
			$where .= " AND smt.user_type = '$user_type' ";
		}
	}

	//처리상태 검색
	if(is_array($qa_state)){
		for($i=0;$i < count($qa_state);$i++){
			if($qa_state[$i] != ""){
				if($qa_state_str == ""){
					$qa_state_str .= "'".$qa_state[$i]."'";
				}else{
					$qa_state_str .= ",'".$qa_state[$i]."' ";
				}
			}
		}

		if($qa_state_str != ""){
			$where .= " AND smt.qa_state in ($qa_state_str) ";
		}
	}else{
		if($qa_state){
			$where .= " AND smt.qa_state = '$qa_state' ";
		}
	}

	//응대유형 검색
	if(is_array($aw_type)){
		for($i=0;$i < count($aw_type);$i++){
			if($aw_type[$i] != ""){
				if($aw_type_str == ""){
					$aw_type_str .= "'".$aw_type[$i]."'";
				}else{
					$aw_type_str .= ",'".$aw_type[$i]."' ";
				}
			}
		}

		if($aw_type_str != ""){
			$where .= " AND smt.aw_type in ($aw_type_str) ";
		}
	}else{
		if($aw_type){
			$where .= " AND smt.aw_type = '$aw_type' ";
		}
	}

	//긴급문의 검색
	if(is_array($emergency_type)){
		for($i=0;$i < count($emergency_type);$i++){
			if($emergency_type[$i] != ""){
				if($emergency_type_str == ""){
					$emergency_type_str .= "'".$emergency_type[$i]."'";
				}else{
					$emergency_type_str .= ",'".$emergency_type[$i]."' ";
				}
			}
		}

		if($emergency_type_str != ""){
			$where .= " AND smt.emergency_type in ($emergency_type_str) ";
		}
	}else{
		if($emergency_type){
			$where .= " AND smt.emergency_type = '$emergency_type' ";
		}
	}

	if($search_text != ""){
		$where .= " and smt.".$search_type." LIKE '%$search_text%' ";
	}

	if($info_type == "vip"){
		$where .= " and smt.user_level in ('4','5','6')";
	}
	
	$sql = "SELECT 
				*
			FROM
				shop_member_talk_history smt 
			left join
			    common_user cu on smt.ucode = cu.code
			WHERE 
				1	
				$where ";

	$db->query($sql);
	$db->fetch();
	$total = $db->total;

	$sql = "SELECT 
				*
			FROM
				shop_member_talk_history smt 
			left join
			    common_user cu on smt.ucode = cu.code
			WHERE
				1	
				$where 
				ORDER BY smt.regdate DESC
				LIMIT $start , $max";

	$db->query($sql);

$Script = "	<script type='text/javascript'>
$(function(){
	$('#list_frm').submit(function(){
       var checked = $('input:checkbox[name=\"ta_ix[]\"]:checked').length;
       if(checked == 0){
           alert('삭제할 문의사항을 선택해주세요');
           return false;
       }else{
           if(confirm('삭제 하시겠습니까?')){
               return true;
           }else{
               return false;
           }
       }
	})
})
function bbsloadCategory(sel,target, depth) {

	var trigger = sel.options[sel.selectedIndex].value;	// 첫번째 selectbox의 선택된 텍스트
	var form = sel.form.name;
	window.frames['iframe_act'].location.href='/bbs/category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

}
</script>";

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
					<table id='tab_01' ".(($info_type == "list"  || $info_type == "" ) ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='?info_type=list'>전체리스트</a> </td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($info_type == "vip" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

							$mstring .= "<a href='?info_type=vip'>VIP리스트</a>";

						$mstring .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($info_type == "category" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

							$mstring .= "<a href='member_talk_category.php?info_type=category'>문의분류관리</a>";

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
	</table>
";

$mstring .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	<tr>
		<td>
		<form name='searchmember' metod='post'>
		<input type='hidden' name='mode' value='search'>
		<input type='hidden' name='info_type' value='".$info_type."'>
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
													<col width='18%' />
													<col width='32%' />
													<col width='18%' />
													<col width='32%' />";

if($_SESSION["admin_config"][front_multiview] == "Y"){
    $mstring .= "
					<tr>
						<td class='search_box_title' > 글로벌 회원 구분</td>
						<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
					</tr>";
}
$mstring .= "
												<tr height='27'>
													<td class='search_box_title'>회원그룹</td>
													<td class='search_box_item'>
														".makeGroupSelectBox($mdb,"user_group",$user_group,"validation=false title='사용자 그룹'")."
													</td>
													<td class='search_box_title'>회원레벨</td>
													<td class='search_box_item'>
														".getMemberLevel($level_ix,'false')."
													</td>
												</tr>
												<tr height='27'>
													<td class='search_box_title'>문의분류</td>
													<td class='input_box_item'>";
														//주문분류는 게시판의 분류의 키bm_ix = '1'을 이용하여 구연함!
														$sql = "SELECT div_ix,bm_ix,parent_div_ix,div_name,div_depth,view_order,disp,regdate
														FROM ".TBL_BBS_MANAGE_DIV."
														where bm_ix = '1' and div_depth = 1
														group by div_ix,bm_ix,parent_div_ix,div_name,div_depth,view_order,disp,regdate
														order by view_order asc, div_depth asc,div_ix asc ";
														$odb->query($sql);
														$bbs_divs = $db->fetchall();

														$mstring .= "
														<select name='bbs_div' onChange=\"bbsloadCategory(this,'sub_bbs_div',1)\" align='absmiddle'>
																<option value=''>분류선택</option>";
														for($d=0;$d<count($bbs_divs);$d++){
															$mstring .= "<option value=".$bbs_divs[$d][div_ix].">".$bbs_divs[$d][div_name]."</option>";
														}
														$mstring .= "
														</select>
														<span id='sub_cate_table' style='display:none;'>
															<select name='sub_bbs_div'>
																<option value=''>서브분류선택</option>
															</select>
														</span>&nbsp;&nbsp;
													</td>
													<td class='search_box_title'>회원구분</td>
													<td class='search_box_item'>
														<input type=checkbox name='user_type[]' value='0' id='join_o' ".CompareReturnValue("0",$user_type,"checked")."><label for='join_o'>회원</label>
														<input type=checkbox name='user_type[]' value='1' id='join_x'  ".CompareReturnValue("1",$user_type,"checked")."><label for='join_x'>비회원</label>
													</td>
												</tr>
												<tr height='27'>
													<td class='search_box_title'>처리상태</td>
													<td class='search_box_item' colspan='3'>
														<input type=checkbox name='qa_state[]' value='W' id='qa_state_w' ".CompareReturnValue("W",$qa_state,"checked")."><label for='qa_state_w'>접수중</label>
														<input type=checkbox name='qa_state[]' value='I' id='qa_state_i' ".CompareReturnValue("I",$qa_state,"checked")."><label for='qa_state_i'>처리중</label>
														<input type=checkbox name='qa_state[]' value='D' id='qa_state_d' ".CompareReturnValue("D",$qa_state,"checked")."><label for='qa_state_d'>처리지연</label>
														<input type=checkbox name='qa_state[]' value='F' id='qa_state_f' ".CompareReturnValue("F",$qa_state,"checked")."><label for='qa_state_f'>처리완료</label>
														<input type=checkbox name='qa_state[]' value='C' id='qa_state_c' ".CompareReturnValue("C",$qa_state,"checked")."><label for='qa_state_c'>처리취소</label>
													</td>
												</tr>
												<tr height='27'>
													<td class='search_box_title'>긴급문의</td>
													<td class='search_box_item'>
														<input type=checkbox name='emergency_type[]' value='1' id='emergency_x' ".CompareReturnValue("1",$emergency_type,"checked")."><label for='emergency_x'>긴급문의</label>
														<input type=checkbox name='emergency_type[]' value='0' id='emergency_o'  ".CompareReturnValue("0",$emergency_type,"checked")."><label for='emergency_o'>비 긴급문의</label>
													</td>
													<td class='search_box_title'>응대유형</td>
													<td class='search_box_item'>
														<input type=checkbox name='aw_type[]' value='N' id='aw_type_n' ".CompareReturnValue("N",$aw_type,"checked")."><label for='aw_type_n'>미요청</label>
														<input type=checkbox name='aw_type[]' value='S' id='aw_type_s' ".CompareReturnValue("S",$aw_type,"checked")."><label for='aw_type_s'>SMS</label>
														<input type=checkbox name='aw_type[]' value='E' id='aw_type_e'  ".CompareReturnValue("E",$aw_type,"checked")."><label for='aw_type_e'>이메일</label>
														<input type=checkbox name='aw_type[]' value='T' id='aw_type_t'  ".CompareReturnValue("T",$aw_type,"checked")."><label for='aw_type_t'>전화</label>
													</td>
												</tr>
												<tr height='27'>
													<td class='search_box_title'>등록일 <input type='checkbox' name='orderdate' value='1' ".CompareReturnValue("1",$orderdate,"checked")." /></td>
													<td class='search_box_item' colspan='3'>
														".search_date('startDate','endDate',$startDate,$endDate)."
													</td>
												</tr>
												<tr height='27'>
													<td class='search_box_title' bgcolor='#efefef' width='150' align='center'>검색어</td>
													<td class='search_box_item' colspan='3'>
													<select name=search_type >
														<option value='user_name' ".CompareReturnValue("user_name",$search_type,"selected").">성명</option>
														<option value='user_id' ".CompareReturnValue("user_id",$search_type,"selected").">ID</option>
														<option value='ta_counselor' ".CompareReturnValue("ta_counselor",$search_type,"selected").">작성자</option>
														<option value='ta_charger' ".CompareReturnValue("ta_charger",$search_type,"selected").">업무담당자</option>
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
			<td height='10'>
			</td>
		</tr>
		</table><br>
		
		<table cellpadding=0 cellspacing=0 width='100%' align='center'>
		<tr>
			<td align='center'>
				<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
			</td>
		</tr>
		</table>
		</form>
		</td>
	</tr>
</table><br><br>";
/*
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
$mstring .= "<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center'>
	<tr>
		<td align='right' style='padding-bottom:5px;'><a href='re_order.excel.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a></td>
	</tr>
</table>";
}
*/
//iframe_act
$mstring .= "
<table cellpadding=0 cellspacing=0 width='100%'>
<tr >
	<td align=right>
		<a href=\"javascript:PoPWindow('member_talk_add.php?mmode=pop',900,550,'member_info')\"><input type='button' value='작성하기' /></a>
	</td>
</tr>
</table>
<script type='text/javascript' src='member.js?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."'></script>
<form name='list_frm' id='list_frm' action='member_talk_act.php' method='post' target=''>
<input type='hidden' name='code[]' id='code'>
<input type='hidden' name='act' value='delete'>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
	<col width='50'>
	<col width='50'>
	<col width='50'>
	<col width='50'>
	<col width='150'>
	<col width='300'>
	<col width='80'>
	<col width='80'>
	<col width='100'>
	<col width='90'>
	<col width='100'>
	<col width='90'>
	<tr bgcolor=#efefef align=center height=30 style='font-weight:600;'>
		<td width='3%' align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
		<td class='s_td'>알림</td>
		<td class='m_td'>순번</td>
		<td class='m_td'>국내<br>해외</td>
		<td class='m_td'>등록일/<br />처리완료일/<br />처리시간</td>
		<td class='m_td'>문의분류<br />문의사항</td>
		<td class='m_td'>회원그룹<br />회원레벨</td>
		<td class='m_td'>성명/ID</td>
		<td class='m_td'>작성자/<br />업무담당자</td>
		<td class='m_td'>문의 처리 상태</td>
		<td class='m_td'>응대 처리 상태</td>
		<td class='e_td'>관리</td>
	</tr>";

if($db->total){
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;

		if($db->dt[user_group]){
			$sql = "select gp_name from shop_groupinfo where gp_ix = '".$db->dt[user_group]."'";
			$mdb->query($sql);
			$mdb->fetch();

			$gp_name = $mdb->dt[gp_name];
		}
		
		if($db->dt[user_level]){
			$sql = "select lv_name from shop_level where level_ix = '".$db->dt[user_level]."'";
			$mdb->query($sql);
			$mdb->fetch();
				
			$lv_name = $mdb->dt[lv_name];
		}else{
			$lv_name = '-';
		}

		$mstring .="<tr height=30 align=center>
					<td class='list_box_td'><input type=checkbox name=ta_ix[] id='code' value='".$db->dt[ta_ix]."'></td>
					<td class='list_box_td' bgcolor='#efefef'></td>
					<td class='list_box_td'>".$no."</td>
					<td class='list_box_td'>".GetDisplayDivision($db->dt['mall_ix'], "text")."</td>
					<td class='list_box_td' bgcolor='#efefef' >".$db->dt[regdate]."<br />".($db->dt['aw_state'] == "F" ? $db->dt[moddate]."<br />".($db->dt['regdate'] - $db->dt['moddate']) : '')."</td>
					<td class='list_box_td'>".$db->dt[ta_memo]."</td>
					<td class='list_box_td' bgcolor='#efefef' >".$gp_name."<br />".$lv_name."</td>
					<td class='list_box_td' bgcolor='#efefef' >".($db->dt['user_type'] == 0 ? $db->dt[user_name]."/".$db->dt['user_id'] : $db->dt['user_name']."/비회원" )."</td>
					<td class='list_box_td'>".$db->dt['ta_counselor']."/".$db->dt['ta_charger']."</td>
					<td class='list_box_td'>".talk_state($db->dt['qa_state'])."</td>
					<td class='list_box_td' bgcolor='#efefef'>".talk_state($db->dt['aw_state'])."</td>
					<td class='list_box_td' bgcolor='#efefef'>
						<a href=\"javascript:PoPWindow('member_talk_add.php?mmode=pop&ta_ix=".$db->dt[ta_ix]."',900,550,'member_info')\"><img src='../images/".$admininfo["language"]."/btn_detail_view.gif' align=absmiddle></a>
					</td>
				</tr>";
	}
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
	

}else{
	$mstring .= "<tr height=50><td class='list_box_td' colspan=12 align=center style='padding-top:10px;'>회원상담내역이 없습니다.</td></tr>
				";
}

$mstring .= "<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >";
$mstring .="<tr height=40>
				<td  align=left>
					<input type='submit' value='선택한 문의 삭제' />
				</form>
				</td>
				<td algin=center>
					".page_bar($total, $page, $max,$query_string,"")."
				</td>
			</tr>";
$mstring .="</table>
<br>";

$Contents = $mstring;

$P = new LayOut;
$P->addScript = $Script;
$P->strLeftMenu = member_menu();
$P->OnloadFunction = "";	//init();
$P->Navigation = "회원관리 > 회원상담내역";
$P->title = "회원상담내역";
$P->strContents = $Contents;
$P->PrintLayOut();




?>