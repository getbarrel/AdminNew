<?
include("../class/layout.class");


$db = new Database;
$mdb = new Database;

if($code){
	$mem_ix = $code;
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
	
	function DeleteReserve(id, uid){
		if(confirm(language_data['common']['G'][language])){
		//'적립금 정보를 정말로 삭제하시겠습니까?'
			//document.frames['iframe_act'].location.href='member.act.php?act=reserve_delete&id='+id+'&uid='+uid;
			window.frames['iframe_act'].location.href='member.act.php?act=reserve_delete&id='+id+'&uid='+uid;
		}
	}

	function UpdateReserve(id){
		var frm = document.forms['reserve_list'];
		var state = $('#state').find('option:selected').val();
		
		if(state == '0' || state == '1' || state == '9'){
			var use_state = $('#reserce_add').find('option:selected').val();
			
		}else{
			var use_state = $('#reserce_cancel').find('option:selected').val();
		}

		if(confirm('적립금 정보를 수정하시겠습니까?')){
		window.frames['iframe_act'].location.href='member.act.php?act=update_state&id='+id+'&state='+state+'&use_state='+use_state;
		}
	}

	
	function CheckDelete(frm){
		if(confirm(language_data['reserve.php']['G'][language])){
		//'선택하신 적립금을 정말로 삭제하시겠습니까? 삭제하신 적립금은 복원되지 않습니다'
			for(i=0;i < frm.reserve_id.length;i++){
				if(frm.reserve_id[i].checked){
					return true
				}
			}
			alert(language_data['reserve.php']['C'][language]);
			//'삭제하실 목록을 한개이상 선택하셔야 합니다.'
		}
		return false;

	}

	function SelectDelete(frm){
		frm.act.value = 'reserve_select_delete';
		if(CheckDelete(frm)){
			frm.submit();
		}
	}

</script>
";
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

						$Contents01 .= "<a href='?mmode=".$mmode."&info_type=add'>마일리지 적립</a>";

						$Contents01 .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_05' ".($info_type == "use"  ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

						$Contents01 .= "<a href='?mmode=".$mmode."&info_type=use'>마일리지 사용</a>";

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
if($info_type == "list" or $info_type == ""){
	
	$where = "where 1";
	if($mmode == "personalization"){ 
		$where = " and ri.uid ='".$mem_ix."' ";
	}

	$searchTotalBool = false;
	if($regdate){
	    if($sdate && $edate){
            $searchTotalBool = true;
            $startDate = $sdate;
            $endDate = $edate;
            $searchWhere = " and date_format(ri.regdate,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
        }
    }
	
	$sql = "select sum(reserve) as state_wait from ".TBL_SHOP_ORDER_DETAIL." where  status in ('IC','DR','DI')";
	$mdb->query($sql);
	$mdb->fetch();	
	$state_wait = $mdb->dt[state_wait];		//적립대기
	
	$sql = "select 
					sum(am_mileage) as state_complate 
				from 
					shop_add_mileage ri 
				left join 
					".TBL_COMMON_USER." as cu on (ri.uid = cu.code)
				left join
					".TBL_COMMON_MEMBER_DETAIL." cmd on (cu.code = cmd.code)
				$where";

	$sql2 = $sql.$searchWhere;			

	$mdb->query($sql);
	$mdb->fetch();

	
	$state_complate = $mdb->dt[state_complate];	//적립완료

    if($searchTotalBool){
        $mdb->query($sql2);
        $mdb->fetch();
        $state_complate_search = $mdb->dt[state_complate];	//적립완료
    }

	


	$sql = "select 
					sum(um_mileage) as state_use 
				from 
					shop_use_mileage ri 
				left join 
					".TBL_COMMON_USER." as cu on (ri.uid = cu.code)
				left join
					".TBL_COMMON_MEMBER_DETAIL." cmd on (cu.code = cmd.code)
				$where	and ri.use_type not in ('5','6')
			";
    $sql2 = $sql.$searchWhere;
	$mdb->query($sql);
	$mdb->fetch();
	
	$state_use = abs($mdb->dt[state_use]);		//적립사용

    if($searchTotalBool){
        $mdb->query($sql2);
        $mdb->fetch();
        $state_use_search = abs($mdb->dt[state_use]);		//적립사용
    }

	$sql = "select 
					sum(um_mileage) as auto_cancel 
				from 
					shop_use_mileage ri 
				left join 
					".TBL_COMMON_USER." as cu on (ri.uid = cu.code)
				left join
					".TBL_COMMON_MEMBER_DETAIL." cmd on (cu.code = cmd.code)
				$where	and ri.use_type in ('5','6')
			";
    $sql2 = $sql.$searchWhere;

	$mdb->query($sql);
	$mdb->fetch();
	
	$auto_cancel = abs($mdb->dt[auto_cancel]);		//적립사용
    if($searchTotalBool){
        $mdb->query($sql2);
        $mdb->fetch();
        $auto_cancel_search = abs($mdb->dt[auto_cancel]);		//적립사용
    }

	
	$total_reserve = $state_wait + $state_complate;	//합계

	$total_use = $state_use + $auto_cancel ;//합계

	$sum_reserve = $state_complate - abs($total_use); // 총누적마일리지 - 총사용마일리지
	

	if($total_reserve > 0){
		$vip_rate = $state_wait / $total_reserve * 100 ;
		$vvip_rate = $state_complate / $total_reserve * 100 ;

		$total_use_rate = $total_use / $total_reserve * 100;	//총 사용마일리지 비율
		$total_reserve_rate = $sum_reserve / $total_reserve * 100;	//현재보유 마일리지 비율
		$total_rate = $total_use_rate + $total_reserve_rate;	//총 누적 마일리지 비율
	
	}else{
		$vip_rate = '0' ;
		$vvip_rate = '0' ;
	}

	if($total_use > 0){
		$reserve_rate_use = $state_use / $total_use * 100 ;
		$reserve_rate_cancel = $auto_cancel / $total_use * 100 ;

	}else{
		$vip_rate = '0' ;
		$vvip_rate = '0' ;
	}
/*
$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<tr>
		<td colspan=8>
			<table border='0' cellpadding='0' cellspacing='0' width='100%'>
			<tr>
				<td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 마일리지 관리</b> ".($company_id != "" ? "[ ".$db->dt[com_name]." : ".$company_id." ]" : "")." </div>")."</td>
			  </tr>
			</table>
		</td>
	</tr>
</table>";
*/
//if($mmode != "personalization"){
$Contents01 .= "
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
	<tr height='28' bgcolor='#ffffff'>
		<td width='30%' align='center' class=s_td colspan=3>총 누적 마일리지</td>
		<td width='30%' align='center' class='m_td'  colspan=3> <font color='#000000'><b>총 사용 마일리지</b></font></td>
		<td width='40%' align='center' class='e_td' nowrap rowspan=2><font color='#000000'><b>현재 회원보유<br>마일리지 합계</b></font></td>
	</tr>
	<tr height='28' bgcolor='#ffffff'>
		<td width='14%' align='center' class=s_td><font color='#000000'><b>적립대기</b></font></td>
		<td width='14%' align='center' class='m_td'><font color='#000000'><b>적립완료</b></font></td>
		<td width='14%' align='center' class='m_td'><font color='#000000'><b>합계</b></font></td>
		<td width='14%' align='center' class='m_td'><font color='#000000'><b>사용</b></font></td>
		<td width='14%' align='center' class='m_td'><font color='#000000'><b>기간소멸</b></font></td>
		<td width='14%' align='center' class='e_td'><font color='#000000'><b>합계</b></font></td>
	
	</tr>
	<tr height='28'>
		<td class='list_box_td' >".number_format($state_wait)."(".round($vip_rate,2)."%)</td>
		<td class='list_box_td'>".number_format($state_complate)."(".round($vvip_rate,2)."%)</td>
		<td class='list_box_td'>".number_format($total_reserve)."(".round($total_rate,2)."%)</td>
		<td class='list_box_td'>".number_format($state_use)."(".round($reserve_rate_use,2)."%)</td>
		<td class='list_box_td'>".number_format($auto_cancel)."(".round($reserve_rate_cancel,2)."%)</td>
		<td class='list_box_td'>".number_format($total_use)."(".round($total_use_rate,2)."%)</td>
		<td class='list_box_td'>".number_format($sum_reserve)."(".round($total_reserve_rate,2)."%)</td>
	</tr>
</table>
<br><br>";
//}
}

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6> ".GetTitleNavigation("마일리지 관리", "회원관리 > 마일리지 관리 ")."</td>
	  </tr>
	  <tr>
			<td>
				<form name='searchmember' style='display:inline;'>
				<input type='hidden' name='mmode' value='$mmode'>
				<input type='hidden' name='mem_ix' value='$mem_ix'>
				<input type='hidden' name='info_type' value='$info_type'>
				 
						<table border='0' cellpadding='0' cellspacing='0' width='100%'> 
							<tr>
								<td align='left' colspan=2  width='100%' valign=top style='padding-top:0px;'>
									 

									<table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
										<col width='18%' />
										<col width='32%' />
										<col width='18%' />
										<col width='32%' />";
if($_SESSION["admin_config"][front_multiview] == "Y"){
    $Contents01 .= "
					<tr>
						<td class='search_box_title' > 글로벌 회원 구분</td>
						<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
					</tr>";
}

										if($mmode != "personalization"){
									$Contents01 .= "
										<tr height=27>
											<td class='search_box_title' >회원그룹 </td>
											<td class='search_box_item' colspan='3'>".makeGroupSelectBox($mdb,"gp_ix",$gp_ix)."</td>";
									$Contents01 .= "
										</tr>";
									$Contents01 .= "
										<tr height=27>
										<!--
											<td class='search_box_title' >회원구분 </td>
											<td class='search_box_item' >
											   
												<input type=radio name='nationality' value='' id='nationality_'  ".CompareReturnValue("",$nationality,"checked")."><label for='nationality_'>전체회원</label>
												<input type=radio name='nationality' value='I' id='nationality_I'  ".CompareReturnValue("I",$nationality,"checked")."><label for='nationality_I'>국내회원</label>
												<input type=radio name='nationality' value='O' id='nationality_O'  ".CompareReturnValue("O",$nationality,"checked")."><label for='nationality_O'>해외회원</label>
												<input type=radio name='nationality' value='D' id='nationality_D' ".CompareReturnValue("D",$nationality,"checked")."><label for='nationality_D'>기타회원</label>
											
											</td>
											-->
											<td class='search_box_title' >회원타입 </td>
											<td class='search_box_item' colspan='3' >
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
										
										if($info_type == "list" || $info_type == ""){
										$Contents01 .= "
											<input type=radio name='info_type' value='' id='state_100'  ".CompareReturnValue('',$info_type,"checked")." checked><label for='state_100'>&nbsp;전체</label>&nbsp;
											<input type=radio name='info_type' value='add' id='state_1' ".CompareReturnValue('add',$info_type,"checked")."><label for='state_1'>&nbsp;완료(+)</label>&nbsp;
											<input type=radio name='info_type' value='use' id='state_2' ".CompareReturnValue('use',$info_type,"checked")."><label for='state_2'>&nbsp;사용(-)</label>&nbsp;
											";
										}else if($info_type == "add"){
											$Contents01 .= "
											<input type=radio name='info_type' value='add' id='state_1' checked><label for='state_1'>&nbsp;완료(+)</label>&nbsp;
											";
										
										}else if($info_type == "use"){
											$Contents01 .= "
											<input type=radio name='info_type' value='use' id='state_2' checked><label for='state_2'>&nbsp;사용(-)</label>&nbsp;
											";
										}

						$Contents01 .= "
											</td>
										</tr>";
										if($info_type == "add"){
                                            $Contents01 .= "
                                            <tr height=27>
                                                <td class='search_box_title' bgcolor='#efefef' align=center><b>적립타입</b></td>
                                                <td class='search_box_item' align=left  colspan='3'>
                                                    ".getMileageSearchType('add',$mileage_info_type)."
                                                </td>
                                            </tr>
                                            ";
                                        }else if($info_type == "use"){
                                            $Contents01 .= "
                                            <tr height=27>
                                                <td class='search_box_title' bgcolor='#efefef' align=center><b>사용타입</b></td>
                                                <td class='search_box_item' align=left  colspan='3'>
                                                    ".getMileageSearchType('use',$mileage_info_type)."
                                                </td>
                                            </tr>
                                            ";
                                        }

$Contents01 .= "				
										<tr height=27>
											<td class='search_box_title' bgcolor='#efefef' align=center><label for='regdate'><b>처리일자</b></label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);'".CompareReturnValue("1",$regdate,"checked")."></td>
											<td class='search_box_item' align=left  colspan='3'>
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
														<option value='cu.id' ".CompareReturnValue("cu.id",$search_type,"selected").">ID</option>";
													}
													$Contents01 .= "
														<option value='ri.oid' ".CompareReturnValue("ri.oid",$search_type,"selected").">주문번호</option>
														<option value='message' ".CompareReturnValue("etc",$search_type,"selected").">적립내용</option>
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
							</tr>
							<tr >
								<td colspan=3 align=center style='padding:10px 0;'>
									<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
								</td>
							</tr>
						</table> 
				</form>
			</td>
		</tr>";

$Contents01 .= "
	  </table>";

if($info_type == "list" or $info_type == ""){
	$mileage_table = "shop_mileage_log";
	$mileage_ix = "ml_ix";
	$mileage_data = "ml_mileage";
	$mileage_state = "ml_state";
	$log_ix = "ml_ix";
	$log_where = "";
	$mileage_type = "";

}else if($info_type == "add"){
	$mileage_table = "shop_add_mileage";
	$mileage_ix = "am_ix";
	$mileage_data = "am_mileage";
	$mileage_state = "am_state";
    $log_ix = "type_ix";
    $log_where = " and log_type = 'add' ";
    $mileage_type = "add_type";

}else if($info_type == "use"){
	$mileage_table = "shop_use_mileage";
	$mileage_ix = "um_ix";
	$mileage_data = "um_mileage";
	$mileage_state = "um_state";
    $log_ix = "type_ix";
    $log_where = " and log_type = 'use' ";
    $mileage_type = "use_type";
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
if($db->dbms_type == "oracle"){
	$where = " where cmd.code = ri.uid_ and cmd.code = cu.code ";
}else{
	$where = " where  cmd.code = ri.uid and cu.code = cmd.code ";
}

if($mmode == "personalization"){
	$where .= " and ri.uid = '".$mem_ix."' ";
}

if($gp_ix != ""){
	$where .= " and cmd.gp_ix = '".$gp_ix."' ";
}

if($mem_type !=""){
	$where .= " and cu.mem_type = '".$mem_type."' ";
}

if($nationality !=""){
	$where .= " and cmd.nationality = '".$nationality."' ";
}

if(is_array($mileage_info_type)){
    for($i=0;$i < count($mileage_info_type);$i++){
        if($mileage_info_type[$i] != ""){
            if($mileage_info_type_str == ""){
                $mileage_info_type_str .= "'".$mileage_info_type[$i]."'";
            }else{
                $mileage_info_type_str .= ", '".$mileage_info_type[$i]."' ";
            }
        }
    }

    if($mileage_info_type_str != ""){
        $where .= "and ri.".$mileage_type." in ($mileage_info_type_str) ";
    }
}else{
    if($mileage_info_type){
        $where .= "and ri.".$mileage_type." = '$mileage_info_type' ";
    }
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

if($mall_ix){
    $where .=" and cu.mall_ix = '".$mall_ix."' ";
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
				cu.mall_ix,
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
				cu.mall_ix,
				AES_DECRYPT(UNHEX(IFNULL(cmd.name,'-')),'".$db->ase_encrypt_key."') as name 
			from 
				$mileage_table as ri 
				left join ".TBL_COMMON_USER." as cu on (ri.uid = cu.code)
				left join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)

			$where
				order by ri.".$mileage_ix." desc LIMIT $start, $max";
	$db->query($sql);

}
$Contents02 = "
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
  <tr height=30 >
	<td>
		<b>전체 : ".$total." 개</b> ";
        if($info_type == "list" || $info_type == "") {
            if ($regdate) {
            $Contents02 .= "
            <b>적립완료 : " . number_format($state_complate_search) . " </b> ,
            <b>사용 : " . number_format($state_use_search) . " </b> ,
            <b>소멸 : " . number_format($auto_cancel_search) . " </b>    	
            ";
            }
        }
$Contents02 .= "
	</td>
  	<td colspan=5 align=right>";
	if($mmode != "personalization"){ 
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents02 .= "<a href='mileage_excel2016.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}else{
			$Contents02 .= "<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}
	}
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
			<td class='s_td' width=3%><input type=checkbox class=nonborder id='all_fix' onclick='fixAll(document.reserve_list)'></td>
			<td class='m_td' width=11%>주문번호</td>";
if($mmode != "personalization"){ 
$Contents02 .= "
			<td class='m_td' width=7%>회원명</td>
			<td class='m_td' width=7%>ID</td>
			<td class='m_td' width=5%>국내<br>해외</td>
			<td class='m_td' width=9%>회원그룹</td>";
}
$Contents02 .= "
			<td class='m_td' width=20%>적립내용 </td>
			<td class='m_td' width=7% >마일리지 </td>
			<td class='m_td' width=6% >적립상태 </td>
			<td class='m_td' width=6% >잔여마일리지 </td>
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

		$sql = "select total_mileage from shop_mileage_log where ".$log_ix." = '".$db->dt['mileage_ix']."' $log_where ";
        $mdb->query($sql);
        $mdb->fetch();

        $total_mileage = $mdb->dt[total_mileage];

        $nationality = GetDisplayDivision($db->dt['mall_ix'], "text");
		$Contents02 .= "<tr height=28 align=center>
				<td class='list_box_td list_bg_gray' ><input type=checkbox class=nonborder id='reserve_id' name=rid[] value='".$db->dt[mileage_ix]."'></td>
				<td class='list_box_td' bgcolor='#ffffff'>".$db->dt[oid]."</td>";
if($mmode != "personalization"){ 
$Contents02 .= "
				<td class='list_box_td point' bgcolor='#efefef'><a href=\"javascript:PoPWindow('mileage.pop.php?code=".$db->dt[code]."',750,550,'mileage_pop')\">".$db->dt[name]."</a></td>
				<td class='list_box_td point' bgcolor='#efefef'><a href=\"javascript:PoPWindow('mileage.pop.php?code=".$db->dt[code]."',750,550,'mileage_pop')\">".$db->dt[member_id]."</a></td>
				<td class='list_box_td' bgcolor='#ffffff'>".$nationality."</td>
				<td class='list_box_td' bgcolor='#ffffff' style='padding:5px 0 5px 5px' align=left>".$gp_name."</td>";
}
$Contents02 .= "
				<td class='list_box_td' bgcolor='#ffffff' style='padding:5px 0 5px 5px' align=left>".$db->dt[message]."</td>
				<td class='list_box_td list_bg_gray'><font color='".$font_color."'>";
		if($db->dt[state]	== RESERVE_STATUS_ORDER_CANCEL){
			$Contents02 .= "<s>".number_format($db->dt[mileage])."</s>";
		}else{
			$Contents02 .= number_format($db->dt[mileage]);
		}
	$Contents02 .= "
                    <font></td>
					<td class='list_box_td' >".$mstate."</td>
					<td class='list_box_td' >".number_format($total_mileage)."</td>
				<td class='list_box_td list_bg_gray' >".$reserve_date."</td>
				
					<td class='list_box_td' >
					<a href=\"javascript:PoPWindow('mileage.pop.php?code=".$db->dt[code]."&reserve_id=".$db->dt[reserve_id]."',800,550,'mileage_pop')\"><img src='../images/".$admininfo["language"]."/btn_detail_view.gif' border=0></a>
				</td>
			</tr>";
	}
	$Contents02 .= "</form>";
}else{
		$Contents02 .= "
			<tr height=60><td class='list_box_td' ".($mmode == "personalization" ? "colspan=8":"colspan=12")." align=center>적립금 내용이 없습니다.</td></tr>";
}

if($_SERVER["QUERY_STRING"] == "nset=$nset&page=$page"){
    $query_string = str_replace("nset=$nset&page=$page","",$_SERVER["QUERY_STRING"]) ;
}else{
    $query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER["QUERY_STRING"]) ;
}
$str_page_bar = page_bar($total, $page, $max, $query_string,"");

$Contents02 .= "
</table>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
<col width='10%'>
<col width='*'>
<tr height=40>
	<td align=right>".$str_page_bar."</td>
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


$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("마일리지 관리", $help_text);


if($mmode == "personalization"){
	$P = new ManagePopLayOut();
	$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
	$P->OnloadFunction = "";	//init();
	$P->strLeftMenu = member_menu();
	$P->Navigation = "회원관리 > 마일리지 관리";
	$P->title = "마일리지 관리";
    $P->NaviTitle = "마일리지 관리"; 
	$P->strContents = $Contents;
	$P->layout_display = false;
	$P->view_type = "personalization";
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
	$P->OnloadFunction = "";	//init();
	$P->strLeftMenu = member_menu();
	$P->Navigation = "회원관리 > 마일리지 관리";
	$P->title = "마일리지 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

function getMileageSearchType($type,$value){
    $html = "";
    if($type == 'add'){
        //1 : 주문에 의한 적립, 2 : 회원가입에 의한 적립  3 : 수동적립, 4 : 취소적립, 5 : 배송비 적립 ,6 : 게시판 글 작성, 7 :기타
        $html .= "
        <input type='checkbox' name='mileage_info_type[]' id='add_type_1' value='1' style='vertical-align: middle;' ".CompareReturnValue('1',$value,' checked').">
        <label for='add_type_1'><spna>주문에 의한 적립</spna></label>
        <input type='checkbox' name='mileage_info_type[]' id='add_type_2' value='2' style='vertical-align: middle;' ".CompareReturnValue('2',$value,' checked').">
        <label for='add_type_2'><spna>회원가입에 의한 적립</spna></label>
        <input type='checkbox' name='mileage_info_type[]' id='add_type_3' value='3' style='vertical-align: middle;' ".CompareReturnValue('3',$value,' checked').">
        <label for='add_type_3'><spna>수동적립</spna></label>
        <input type='checkbox' name='mileage_info_type[]' id='add_type_4' value='4' style='vertical-align: middle;' ".CompareReturnValue('4',$value,' checked').">
        <label for='add_type_4'><spna>취소적립</spna></label>
        <input type='checkbox' name='mileage_info_type[]' id='add_type_5' value='5' style='vertical-align: middle;' ".CompareReturnValue('5',$value,' checked').">
        <label for='add_type_5'><spna>배송비 적립</spna></label>
        <input type='checkbox' name='mileage_info_type[]' id='add_type_6' value='6' style='vertical-align: middle;' ".CompareReturnValue('6',$value,' checked').">
        <label for='add_type_6'><spna>게시판 글 작성</spna></label>
        <input type='checkbox' name='mileage_info_type[]' id='add_type_7' value='7' style='vertical-align: middle;' ".CompareReturnValue('7',$value,' checked').">
        <label for='add_type_7'><spna>기타</spna></label>
        ";
    }else if ($type == 'use'){
        //1 : 주문에 의한 사용 ,2 : 수동사용 처리,  5 : 마일리지 소멸 , 6 : 탈퇴에 의한 소멸
        $html .= "
        <input type='checkbox' name='mileage_info_type[]' id='use_type_1' value='1' style='vertical-align: middle;' ".CompareReturnValue('1',$value,' checked').">
        <label for='use_type_1'><spna>주문에 의한 사용</spna></label>
        <input type='checkbox' name='mileage_info_type[]' id='use_type_2' value='2' style='vertical-align: middle;' ".CompareReturnValue('2',$value,' checked').">
        <label for='use_type_2'><spna>수동사용 처리</spna></label>
        <input type='checkbox' name='mileage_info_type[]' id='use_type_5' value='5' style='vertical-align: middle;' ".CompareReturnValue('5',$value,' checked').">
        <label for='use_type_5'><spna>마일리지 소멸</spna></label>
        <input type='checkbox' name='mileage_info_type[]' id='use_type_6' value='6' style='vertical-align: middle;' ".CompareReturnValue('6',$value,' checked').">
        <label for='use_type_6'><spna>탈퇴에 의한 소멸</spna></label>
        ";
    }
    return $html;
}
?>