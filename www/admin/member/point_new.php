<?
include("../class/layout.class");

if($info_type == ""){
	$info_type ="list";
}
if($code){
	$mem_ix = $code;
}
$db = new Database;
$mdb = new Database;

if ($FromYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;
}

$Script ="
<script language='JavaScript' >

function clearAll(frm){
		for(i=0;i < frm.reserve_id.length;i++){
				frm.reserve_id[i].checked = false;
		}
}
function checkAll(frm){
       	for(i=0;i < frm.reserve_id.length;i++){
				frm.reserve_id[i].checked = true;
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
</script>
";
if($mmode != "personalization"){
$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	  <tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "회원관리 > $menu_name")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<col width='700px'>
			<col width='*'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".(($info_type == "list" || $info_type == "" ) ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='?mmode=".$mmode."&info_type=list'>전체리스트</a> </td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_04' ".($info_type == "add"  ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

						$Contents01 .= "<a href='?mmode=".$mmode."&info_type=add'>적립완료 리스트</a>";

						$Contents01 .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_05' ".($info_type == "use"  ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

						$Contents01 .= "<a href='?mmode=".$mmode."&info_type=use'>적립사용 리스트</a>";

						$Contents01 .= "
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
}
if($info_type == "list" or $info_type == ""){
	$where = "where 1";
	if($mmode == "personalization"){ 
		$where = " and ri.uid ='".$mem_ix."' ";
	}
	
	$sql = "select 
					sum(am_mileage) as state_complate 
				from 
					shop_add_point ri 
				left join 
					".TBL_COMMON_USER." as cu on (ri.uid = cu.code)
				left join
					".TBL_COMMON_MEMBER_DETAIL." cmd on (cu.code = cmd.code)
				$where	
			";
	$mdb->query($sql);
	$mdb->fetch();

	
	$state_complate = $mdb->dt[state_complate];	//적립완료
	
	


	$sql = "select 
					sum(um_mileage) as state_use 
				from 
					shop_use_point ri 
				left join 
					".TBL_COMMON_USER." as cu on (ri.uid = cu.code)
				left join
					".TBL_COMMON_MEMBER_DETAIL." cmd on (cu.code = cmd.code)
				$where	and ri.use_type not in ('5','6')
			";
	$mdb->query($sql);
	$mdb->fetch();
	
	$state_use = abs($mdb->dt[state_use]);		//적립사용

	
	$sql = "select 
					sum(um_mileage) as auto_cancel 
				from 
					shop_use_point ri 
				left join 
					".TBL_COMMON_USER." as cu on (ri.uid = cu.code)
				left join
					".TBL_COMMON_MEMBER_DETAIL." cmd on (cu.code = cmd.code)
				$where	and ri.use_type in ('5','6')
			";
	$mdb->query($sql);
	$mdb->fetch();
	
	$auto_cancel = abs($mdb->dt[auto_cancel]);		//적립사용


	
	$total_reserve = $state_wait + $state_complate;	//합계

	$total_use = $state_use + $auto_cancel ;//합계

	$sum_reserve = $state_complate - abs($total_use); // 총누적마일리지 - 총사용마일리지
	

	if($total_reserve > 0){
		$vvip_rate = $state_complate / $total_reserve * 100 ;

		$total_use_rate = $total_use / $total_reserve * 100;	//총 사용마일리지 비율
		$total_reserve_rate = $sum_reserve / $total_reserve * 100;	//현재보유 마일리지 비율
		$total_rate = $total_use_rate + $total_reserve_rate;	//총 누적 마일리지 비율
	
	}else{
		$vvip_rate = '0' ;
	}

	if($total_use > 0){
		$reserve_rate_use = $state_use / $total_use * 100 ;
		$reserve_rate_cancel = $auto_cancel / $total_use * 100 ;

	}else{
		$vvip_rate = '0' ;
	}

if($mmode != "personalization"){
$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<tr>
		<td colspan=8>
			<table border='0' cellpadding='0' cellspacing='0' width='100%'>
			<tr>
				<td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 포인트 관리</b> ".($company_id != "" ? "[ ".$db->dt[com_name]." : ".$company_id." ]" : "")." </div>")."</td>
			  </tr>
			</table>
		</td>
	</tr>
</table>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
	<tr height='28' bgcolor='#ffffff'>
		<td width='30%' align='center' class=s_td colspan=2>총 누적 포인트</td>
		<td width='30%' align='center' class='m_td'  colspan=3> <font color='#000000'><b>총 사용 포인트</b></font></td>
		<td width='40%' align='center' class='e_td' nowrap><font color='#000000'><b>현재 회원보유</b></font></td>
	</tr>
	<tr height='28' bgcolor='#ffffff'>
		<td width='14%' align='center' class='m_td'><font color='#000000'><b>적립완료</b></font></td>
		<td width='14%' align='center' class='m_td'><font color='#000000'><b>합계</b></font></td>
		<td width='14%' align='center' class='m_td'><font color='#000000'><b>사용</b></font></td>
		<td width='14%' align='center' class='m_td'><font color='#000000'><b>기간소멸</b></font></td>
		<td width='14%' align='center' class='m_td'><font color='#000000'><b>합계</b></font></td>
		<td width='14%' align='center' class='e_td' nowrap><font color='#000000'><b>포인트 합계</b></font></td>
	</tr>
	<tr height='28'>
		<td class='list_box_td'>".number_format($state_complate)."(".round($vvip_rate,2)."%)</td>
		<td class='list_box_td'>".number_format($total_reserve)."(".round($total_rate,2)."%)</td>
		<td class='list_box_td'>".number_format($state_use)."(".round($reserve_rate_use,2)."%)</td>
		<td class='list_box_td'>".number_format($auto_cancel)."(".round($reserve_rate_cancel,2)."%)</td>
		<td class='list_box_td'>".number_format($total_use)."(".round($total_use_rate,2)."%)</td>
		<td class='list_box_td'>".number_format($sum_reserve)."(".round($total_reserve_rate,2)."%)</td>
	</tr>
</table>
<br><br>";
}
}

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6> ".GetTitleNavigation("포인트 지급내역", "회원관리 > 포인트 지급내역 ")."</td>
	  </tr>
	  <tr>
			<td>
				<form name='searchmember' style='display:inline;'>
				<input type='hidden' name='mmode' value='$mmode'>
				<input type='hidden' name='mem_ix' value='$mem_ix'>
				<input type='hidden' name='info_type' value='$info_type'>
				<table border='0' cellpadding='0' cellspacing='0' width='100%'>
					<tr>
					<td style='width:100%;' valign=top colspan=3>
						<table width=100%  border=0>
							<!--tr height=25><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>포인트 검색하기</b></td></tr-->
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
											<table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
											<col width='18%' />
											<col width='32%' />
											<col width='18%' />
											<col width='32%' />";
										if($mmode != "personalization"){
											$Contents01 .= "
											<tr height=27>
											<td class='search_box_title' >회원그룹 </td>
											<td class='search_box_item' colspan='3'>".makeGroupSelectBox($mdb,"gp_ix",$gp_ix)."</td>
											</tr>";
										}
										if($mmode != "personalization"){
											$Contents01 .= "
											<tr height=27>
												<td class='search_box_title' >회원구분 </td>
												<td class='search_box_item' >
													<input type=radio name='nationality' value='' id='nationality_'  ".CompareReturnValue("",$nationality,"checked")."><label for='nationality_'>전체회원</label>
													<input type=radio name='nationality' value='I' id='nationality_I'  ".CompareReturnValue("I",$nationality,"checked")."><label for='nationality_I'>국내회원</label>
													<input type=radio name='nationality' value='O' id='nationality_O'  ".CompareReturnValue("O",$nationality,"checked")."><label for='nationality_O'>해외회원</label>
													<input type=radio name='nationality' value='D' id='nationality_D' ".CompareReturnValue("D",$nationality,"checked")."><label for='nationality_D'>기타회원</label>
												</td>
												<td class='search_box_title' >회원타입 </td>
												<td class='search_box_item' >
													<input type=radio name='mem_type' value='' id='mem_type_'  ".CompareReturnValue("",$mem_type,"checked")." checked><label for='mem_type_'>전체</label>
													<input type=radio name='mem_type' value='M' id='mem_type_m'  ".CompareReturnValue("M",$mem_type,"checked")."><label for='mem_type_m'>일반회원</label>
													<input type=radio name='mem_type' value='C' id='mem_type_c' ".CompareReturnValue("C",$mem_type,"checked")."><label for='mem_type_c'>기업회원</label>
													<input type=radio name='mem_type' value='S' id='mem_type_S' ".CompareReturnValue("F",$mem_type,"checked")."><label for='mem_type_S'>셀러회원</label>
												</td>
											</tr>";
										}
										$Contents01 .= "
											<tr height=27>
												<td class='search_box_title' bgcolor='#efefef' align=center><b>적립상태</b></td>
												<td class='search_box_item' align=left   colspan='3'>";

											if($info_type == "list"){
											$Contents01 .= "
												<input type=radio name='state' value='' id='state_100'  ".CompareReturnValue('',$state,"checked")." checked><label for='state_100'>&nbsp;전체</label>&nbsp;
												<input type=radio name='state' value='add' id='state_1' ".CompareReturnValue('add',$state,"checked")."><label for='state_1'>&nbsp;완료(+)</label>&nbsp;
												<input type=radio name='state' value='use' id='state_2' ".CompareReturnValue('use',$state,"checked")."><label for='state_2'>&nbsp;사용(-)</label>&nbsp;
												";
											}else if($info_type == "add"){
												$Contents01 .= "
												<input type=radio name='state' value='add' id='state_1' checked><label for='state_1'>&nbsp;완료(+)</label>&nbsp;
												";
											
											}else if($info_type == "use"){
												$Contents01 .= "
												<input type=radio name='state' value='use' id='state_2' checked><label for='state_2'>&nbsp;사용(-)</label>&nbsp;
												";
											}

								$Contents01 .= "
												</td>
											</tr>
								
											<tr height=27>
												<td class='search_box_title' bgcolor='#efefef' align=center><label for='regdate'><b>처리일자</b></label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);'".CompareReturnValue("1",$regdate,"checked")."></td>
												<td class='search_box_item' align=left colspan='3'>
													".search_date('sdate','edate',$sdate,$edate)."
												</td>
											</tr>
											<tr height=27>
												<th class='search_box_title' bgcolor='#efefef' width='150' align=center>조건검색 </th>
												<td class='search_box_item' colspan='3'>
													<table border=0 cellpadding=0 cellspacing=0 width=100%>
														<col width=80>
														<col width=*>
														<tr>
															<td>
															<select name=search_type>";
															if($mmode != "personalization"){
																$Contents01 .= "
																<option value='cmd.name' ".CompareReturnValue("cmd.name",$search_type,"selected").">회원명</option>
																<option value='cu.id' ".CompareReturnValue("cu.id",$search_type,"selected").">회원ID</option>";
															}
															$Contents01 .= "
																<option value='ri.oid' ".CompareReturnValue("ri.oid",$search_type,"selected").">주문번호</option>
																<option value='etc' ".CompareReturnValue("etc",$search_type,"selected").">적립내용</option>
															</select>
															</td>
															<td>
															<input type=text name='search_text' class=textbox value='".$search_text."' style='width:200px' >
															</td>
														</tr>
													</table>
												</td>
											</tr>
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
							<tr >
								<td colspan=3 align=center style='padding:10px 0;'>
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
		</table>";


if($info_type == "list" or $info_type == ""){
	$mileage_table = "shop_point_log";
	$mileage_ix = "ml_ix";
	$mileage_data = "ml_mileage";
	$mileage_state = "ml_state";

}else if($info_type == "add"){
	$mileage_table = "shop_add_point";
	$mileage_ix = "am_ix";
	$mileage_data = "am_mileage";
	$mileage_state = "am_state";

}else if($info_type == "use"){
	$mileage_table = "shop_use_point";
	$mileage_ix = "um_ix";
	$mileage_data = "um_mileage";
	$mileage_state = "um_state";

}

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
unset($where);

$where = "where 1";

if($mmode == "personalization"){
	$where .= " and r.uid = '".$mem_ix."' ";
}


if($gp_ix != ""){

	$where .= " and cmd.gp_ix = '".$gp_ix."' ";
}

if($state != ""){
    if($info_type == "list" or $info_type == ""){
        $where .= " and ri.log_type = '$state' ";
    }
}

if($mem_type !=""){
	$where .= " and cu.mem_type = '".$mem_type."' ";
}

if($nationality !=""){
	$where .= " and cmd.nationality = '".$nationality."' ";
}

if($db->dbms_type == "oracle"){
	if($search_type != "" && $search_text != ""){
		if($search_type == "cmd.name"){
		   $where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
		}else{
		   $where .= " and $search_type LIKE '%$search_text%' ";
		}
	}
}else{
	if($search_type != "" && $search_text != ""){
		if($search_type == "cmd.name"){
		   $where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
		}else{
		   $where .= " and $search_type LIKE '%$search_text%' ";
		}
	}
}

$startDate = $sdate;
$endDate = $edate;

if($regdate == '1'){	//신청일
	if($startDate != "" && $endDate != ""){
		if($db->dbms_type == "oracle"){
			$where .= " and  to_char(ri.regdate , 'YYYY-MM-DD') between '".$startDate."' and '".$endDate."' ";
			$count_where .= " and  to_char(ri.regdate , 'YYYY-MM-DD') between '".$startDate."' and '".$endDate."' ";
		}else{
			$where .= " and date_format(ri.regdate,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
			$count_where .= " and date_format(ri.regdate,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
		}
	}
}

$sql = "select
	count(*) as total 
	from 
		$mileage_table as ri 
		left join ".TBL_COMMON_USER." as cu on (ri.uid = cu.code)
		left join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
	$where";

$db->query($sql);
$db->fetch();
$total = $db->dt[total];

if($db->dbms_type == "oracle"){
	$sql = "select 
				ri.id,
				ri.etc,
				r.state,
				r.reserve,
				r.oid,
				r.uid_,
				r.use_state,
				cmd.code,
				cmd.gp_ix,
				cmd.level_ix,
				cu.id as member_id,
				AES_DECRYPT(IFNULL(cmd.name,'-'),'".$db->ase_encrypt_key."') as name
			from 
				$mileage_table as ri 
				left join ".TBL_COMMON_USER." as cu on (ri.uid = cu.code)
				left join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)

			$where 
				order by ri.regdate desc LIMIT $start, $max";

	$db->query($sql);

}else{

	$sql = "select 
				ri.*,
				ri.message,
				ri.".$mileage_ix." as mileage_ix,
				ri.".$mileage_data." as mileage,
				ri.".$mileage_state." as state,
				cmd.code,
				cmd.gp_ix,
				cmd.level_ix,
				cu.id as member_id,
				AES_DECRYPT(UNHEX(IFNULL(cmd.name,'-')),'".$db->ase_encrypt_key."') as name 
			from 
				$mileage_table as ri 
				left join ".TBL_COMMON_USER." as cu on (ri.uid = cu.code)
				left join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)

			$where
				order by ri.".$mileage_ix." desc LIMIT $start, $max";
	$db->query($sql);

}
//echo nl2br($sql);
$Contents02 = "
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
	<tr height=30 >
		<td>
			<b>전체 : ".$total." 개</b> 
		</td>
		<td colspan=5 align=right>";
/*
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents02 .= "<a href='point_excel2016.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}else{
			$Contents02 .= "<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}
*/
		$Contents02 .= "
		</td>
	</tr>
	</table>

	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
	<form name=reserve_list method=post action='member.act.php' onsubmit='return CheckDelete(this)' target='iframe_act'>
	<input type='hidden' name='act' value='reserve_select_delete'>
	<input type='hidden' name='id' value=''>
	<input type='hidden' name='etc' value=''>
	<input type='hidden' name='reserve' value=''>
	<tr bgcolor=#efefef style='font-weight:bold' align=center height=28>
		<td class='s_td' width=3%><input type=checkbox class=nonborder id='all_fix' onclick='fixAll(document.reserve_list)'></td>";
if($mmode != "personalization"){
	$Contents02 .= "
		<td class='m_td' width=8%>회원명</td>
		<td class='m_td' width=8%>ID</td>
		<td class='m_td' width=6%>회원그룹</td>";
}
	$Contents02 .= "
		<td class='m_td' width=20%>적립내용 </td>
		<td class='m_td' width=8% >포인트 </td>
		<td class='m_td' width=7% >적립상태 </td>
		<td class='m_td' width=10% >처리일자 </td>
		<td class='e_td' width=6% >관리 </td>
	</tr>";

if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);

		$uid ="";
		if($db->dbms_type == "oracle"){
			$uid = $db->dt[uid_];
		}else{
			$uid = $db->dt[uid];
		}

		if($info_type == 'list' || $info_type == ''){
			if($db->dt[state] !='2'){	//적립상태,사용구분 선택후 수정가능 부분
				$add_display = '';
				$cancel_display = 'none';
				$font_color = '#0054FF';
				$mstate = '적립완료(+)';
			}else {
				$add_display = 'none';
				$cancel_display = '';
				$font_color = '#FF0000';
				$mstate = '사용(-)';
			}	
		}else if($info_type == 'add'){
			$add_display = '';
			$cancel_display = 'none';
			$font_color = '#0054FF';
			$mstate = '적립완료(+)';
		}else if($info_type == 'use'){
			$add_display = 'none';
			$cancel_display = '';
			$font_color = '#FF0000';
			$mstate = '사용(-)';
		}


		$reserve_date = $db->dt[regdate];

		if($db->dt[gp_ix]){
			$sql = "select gp_name from shop_groupinfo where gp_ix = '".$db->dt[gp_ix]."'";
			$mdb->query($sql);
			$mdb->fetch();

			$gp_name = $mdb->dt[gp_name];
		}


		$Contents02 .= "<tr height=28 align=center>
				<td class='list_box_td list_bg_gray' ><input type=checkbox class=nonborder id='reserve_id' name=rid[] value='".$db->dt[mileage_ix]."'></td>";
	if($mmode != "personalization"){
		$Contents02 .= "
				<td class='list_box_td point' bgcolor='#efefef'><a href=\"javascript:PoPWindow('point_new.pop.php?code=".$db->dt[code]."',700,550,'point_new_pop')\">".$db->dt[name]."</a></td>
				<td class='list_box_td point' bgcolor='#efefef'><a href=\"javascript:PoPWindow('point_new.pop.php?code=".$db->dt[code]."',700,550,'point_new_pop')\">".$db->dt[member_id]."</a></td>
				<td class='list_box_td' bgcolor='#ffffff' style='padding:5px 0 5px 5px' align=left>".$gp_name."</td>";
	}
		$Contents02 .= "
				<td class='list_box_td' bgcolor='#ffffff' style='padding:5px 0 5px 5px' align=left>".$db->dt[message]."</td>
				<td class='list_box_td list_bg_gray'><font color='".$font_color."'>";
	
			$Contents02 .= number_format($db->dt[mileage]);
	
	$Contents02 .= "<font></td>
					<td class='list_box_td' >".$mstate."</td>
			
				<td class='list_box_td list_bg_gray' >".$reserve_date."</td>
				<td>
					<a href=\"javascript:PoPWindow('point_new.pop.php?code=".$db->dt[code]."&reserve_id=".$db->dt[reserve_id]."',700,550,'point_pop')\"><img src='../images/".$admininfo["language"]."/btn_detail_view.gif' border=0></a>
				</td>
			</tr>";
	}
	$Contents02 .= "</form>";
}else{
		$Contents02 .= "
			<tr height=60><td class='list_box_td' ".($mmode == "personalization" ? "colspan=8":"colspan=11")." align=center>포인트 내용이 없습니다.</td></tr>";
}

$Contents02 .= "
</table>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
<col width='10%'>
<col width='*'>
<tr height=40>
	<td align=right>".page_bar($total, $page, $max,"&code=$code&state=$state&ust_status_add=$ust_status_add&mem_type=$mem_type&nationality=$nationality&gp_ix=$gp_ix&info_type=$info_type&search_type=$search_type&search_text=$search_text&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD","")."</td>
</tr>
</table>";


$ContentsDesc02 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td align=left style='padding:10px;'>
		<img src='../image/emo_3_15.gif' align=absmiddle>  거래은행 및 그룹 정보는 결제시 이용됩니다. 정확하게 입력해주세요
	</td>
</tr>
</table>
";


$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";

$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<tr><td>$Contents01<br></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."</table >";
/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >고객들에게 지급됐거나 고객들이 사용한 포인트 내역입니다 </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원아이디를 클릭하시면 해당 회원에 대한 포인트을 확인하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >삭제를 원하시는 포인트 내역을 선택하신후 일괄정보 삭제를 클릭하시면 포인트이 삭제됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >포인트을 직접 지급 하고자 하실 경우 회원 이름을 클릭하여 입력하시면 됩니다.</td></tr>
</table>
";
*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("포인트 지급내역", $help_text);



if($mmode == "personalization"){
	$P = new ManagePopLayOut();
	$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
	$P->OnloadFunction = "";	//init();
	$P->strLeftMenu = member_menu();
	$P->Navigation = "회원관리 > 포인트 지급내역";
	$P->title = "포인트 지급내역";
    $P->NaviTitle = "포인트 지급내역"; 
	$P->strContents = $Contents;
	$P->layout_display = false;
	$P->view_type = "personalization";

	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
	$P->OnloadFunction = "";	//init();
	$P->strLeftMenu = member_menu();
	$P->Navigation = "회원관리 > 포인트 지급내역";
	$P->title = "포인트 지급내역";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

?>