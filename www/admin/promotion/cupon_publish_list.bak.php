<?
include("../class/layout.class");

//echo date("Y",strtotime("2014-05-12"));
if ($FromYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

//	$sDate = date("Y/m/d");
	//$sDate = date("Y/m/d", $before10day);
	//$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	//$sDate = $cupon_publish_sdate;//$FromYY."/".$FromMM."/".$FromDD;
	//$eDate = $cupon_publish_edate;//$ToYY."/".$ToMM."/".$ToDD;
	$startDate = $cupon_publish_sdate;//$FromYY.$FromMM.$FromDD;
	$endDate = $cupon_publish_edate;//$ToYY.$ToMM.$ToDD;
}

$where = " where c.cupon_ix is not null ";

if($search_type != "" && $search_text != ""){
	$where .= " and $search_type LIKE  '%$search_text%' ";
}

//$startDate = $FromYY.$FromMM.$FromDD;
//$endDate = $ToYY.$ToMM.$ToDD;
if($publlish_date) {
	if($cupon_publish_sdate != "" && $cupon_publish_edate != ""){
		$where .= " and  cp.regdate between  '$cupon_publish_sdate 00:00:00' and '$cupon_publish_edate 23:59:59' ";
	}
}

$db = new Database();
$db1 = new Database();
$rproduct_db = new Database();

if($mmode == "personalization"){
	$where .= " and cp.is_cs = '1' ";
}
$use_product_type = $_GET["use_product_type"];
if(is_array($use_product_type)){
	for($i=0;$i < count($use_product_type);$i++){
		if($use_product_type[$i] != ""){
			if($use_product_type_str == ""){
				$use_product_type_str .= "'".$use_product_type[$i]."'";
			}else{
				$use_product_type_str .= ",'".$use_product_type[$i]."' ";
			}
		}
	}

	if($use_product_type_str != ""){
		$where .= " AND use_product_type in (".$use_product_type_str.") ";
	}
}else{
	if($use_product_type){
		$where .= " AND use_product_type = '".$use_product_type."' ";
	}else{
		$use_product_type = array();
	}
}

$publish_type = $_GET["publish_type"];
if(is_array($publish_type)){
	for($i=0;$i < count($publish_type);$i++){
		if($publish_type[$i] != ""){
			if($publish_type_str == ""){
				$publish_type_str .= "'".$publish_type[$i]."'";
			}else{
				$publish_type_str .= ",'".$publish_type[$i]."' ";
			}
		}
	}

	if($publish_type_str != ""){
		$where .= " AND publish_type in (".$publish_type_str.") ";
	}
}else{
	if($publish_type){
		$where .= " AND publish_type = '".$publish_type."' ";
	}else{
		$publish_type = array();
	}
}


$cupon_div = $_GET["cupon_div"];
if(is_array($cupon_div)){
	for($i=0;$i < count($cupon_div);$i++){
		if($cupon_div[$i] != ""){
			if($cupon_div_str == ""){
				$cupon_div_str .= "'".$cupon_div[$i]."'";
			}else{
				$cupon_div_str .= ",'".$cupon_div[$i]."' ";
			}
		}
	}

	if($cupon_div_str != ""){
		$where .= " AND cp.cupon_div in (".$cupon_div_str.") ";
	}
}else{
	if($cupon_div){
		$where .= " AND cp.cupon_div = '".$cupon_div."' ";
	}else{
		$cupon_div = array();
	}
}


$cupon_use_div = $_GET["cupon_use_div"];
if(is_array($cupon_use_div)){
	for($i=0;$i < count($cupon_use_div);$i++){
		if($cupon_use_div[$i] != ""){
			if($cupon_use_div_str == ""){
				$cupon_use_div_str .= "'".$cupon_use_div[$i]."'";
			}else{
				$cupon_use_div_str .= ",'".$cupon_use_div[$i]."' ";
			}
		}
	}

	if($cupon_use_div_str != ""){
		$where .= " AND c.cupon_use_div in (".$cupon_use_div_str.") ";
	}
}else{
	if($cupon_use_div){
		$where .= " AND c.cupon_use_div = '".$cupon_use_div."' ";
	}else{
		$cupon_use_div = array();
	}
}

$issue_type = $_GET["issue_type"];
if(is_array($issue_type)){
	for($i=0;$i < count($issue_type);$i++){
		if($issue_type[$i] != ""){
			if($issue_type_str == ""){
				$issue_type_str .= "'".$issue_type[$i]."'";
			}else{
				$issue_type_str .= ",'".$issue_type[$i]."' ";
			}
		}
	}

	if($issue_type_str != ""){
		$where .= " AND cp.issue_type in (".$issue_type_str.") ";
	}
}else{
	if($issue_type){
		$where .= " AND cp.issue_type = '".$issue_type."' ";
	}else{
		$issue_type = array();
	}
}


/*
if($publish_type!=""){
	$where .= " and publish_type ='".$publish_type."' ";
}
*/

if($pre_type == "group_cupon"){
	if($gp_ix !=""){
		$where .= " and crg.gp_ix = '".$gp_ix."'";
	}
}
/*
if($$_GET["cupon_div"] != ""){
	$where .= " and cupon_div =  '".$_GET["cupon_div"]."' ";
}

if($_GET["cupon_use_div"] != ""){
	$where .= " and cupon_use_div =  '".$_GET["cupon_use_div"]."' ";
}
*/
if($_GET["is_cs"] != ""){
	$where .= " and cp.is_cs =  '".$_GET["is_cs"]."' ";
}

$is_use = $_GET["is_use"];
if(is_array($is_use)){
    for($i=0;$i < count($is_use);$i++){
        if($is_use[$i] != ""){
            if($is_use_str == ""){
                $is_use_str .= "'".$is_use[$i]."'";
            }else{
                $is_use_str .= ",'".$is_use[$i]."' ";
            }
        }
    }

    if($is_use_str != ""){
        $where .= " AND cp.is_use in (".$is_use_str.") ";
    }
}else{
    if($is_use){
        $where .= " AND cp.is_use = '".$is_use."' ";
    }else{
        $is_use = array();
    }
}

$disp = $_GET["disp"];
if(is_array($disp)){
    for($i=0;$i < count($disp);$i++){
        if($disp[$i] != ""){
            if($disp_str == ""){
                $disp_str .= "'".$disp[$i]."'";
            }else{
                $disp_str .= ",'".$disp[$i]."' ";
            }
        }
    }

    if($disp_str != ""){
        $where .= " AND cp.disp in (".$disp_str.") ";
    }
}else{
    if($disp){
        $where .= " AND cp.disp = '".$disp."' ";
    }else{
        $disp = array();
    }
}

if($_GET["mall_ix"] != ""){
	$where .= " and cp.mall_ix =  '".$_GET["mall_ix"]."' ";
}

if($pre_type == "group_cupon"){
	$sql = "select 
				distinct cp.publish_tmp_ix,
				cp.*,
				c.cupon_kind
			from
				".TBL_SHOP_CUPON."  c
				inner join shop_cupon_publish_tmp cp on c.cupon_ix = cp.cupon_ix
				left join shop_cupon_relation_group as crg on (cp.publish_tmp_ix = crg.publish_tmp_ix)
			$where
				order by cp.regdate desc";
}else{
	$sql = "select 
				cp.*,
				c.cupon_kind
			from
				".TBL_SHOP_CUPON."  c
				inner join ".TBL_SHOP_CUPON_PUBLISH." cp on c.cupon_ix = cp.cupon_ix
			$where
				order by cp.regdate desc";
}
//echo nl2br($sql);
$db->query($sql);
$total = $db->total;
$Script = "
<!--script language='javascript' src='../include/DateSelect.js'></script-->
<script language='javascript'>
/*
if(window.dialogArguments){
	var opener = window.dialogArguments;
}else{
	var opener = window.opener;
}
*/

function publish_modify(publish_ix){
	if(confirm('수정된 정보는 현재 발급되어 있는 고객의 모든 쿠폰에도 모두 적용됩니다. 수정하시겠습니까?')){//'수정시 현재 발급되어 있는 고객의 모든 쿠폰에도 모두 적용됩니다. 수정하시겠습니까?'
		window.document.location.href='cupon_publish.php?publish_ix='+publish_ix;
	}
}

function publish_delete(publish_ix){
	if(confirm(language_data['cupon_publish_list.php']['A'][language])){//'정말 쿠폰발행을 삭제 하시겠습니까?'
		window.frames['act'].location.href='cupon.act.php?act=publish_delete&publish_ix='+publish_ix;
	}
}

function publish_tmp_delete(publish_tmp_ix){
	if(confirm(language_data['cupon_publish_list.php']['A'][language])){//'정말 쿠폰발행을 삭제 하시겠습니까?'
		window.frames['act'].location.href='cupon.act.php?act=publish_tmp_delete&publish_tmp_ix='+publish_tmp_ix;
	}
} 
function ChangeRegistDate(frm){
	if(frm.publlish_date.checked){
		$('#cupon_publish_sdate').attr('disabled',false);
		$('#cupon_publish_edate').attr('disabled',false);
	 
	}else{
		$('#cupon_publish_sdate').attr('disabled','disabled');
		$('#cupon_publish_edate').attr('disabled','disabled');
	 
	}
}
 


</script>";

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
<table width='100%' border='0' cellpadding='0' cellspacing='0'>
  <tr>
		<td align='left' colspan=6> ".GetTitleNavigation("쿠폰발행 목록", "전시관리 > 쿠폰발행 목록 ")."</td>
  </tr>";
  if($mmode == "personalization"){
$Contents .= "
  <tr>
    <td valign='top'>
      <table width='100%' border='0' cellpadding='2' cellspacing='0'>
        <tr>
				    <td align='left' colspan=4 style='padding-bottom:15px;'>
				    	<div class='tab'>
								<table class='s_org_tab' style='width:100%'>
								<col width=320>
								<col width=*>
								<tr>
									<td class='tab'>
										<table id='tab_03'   >
										<tr>
											<th class='box_01'></th>
											<td class='box_02' ><a href='../promotion/cupon_user_regist_list.php?mmode=personalization&mem_ix=".$mem_ix."'>보유쿠폰</a></td>
											<th class='box_03'></th>
										</tr>
										</table>
										<table id='tab_02' class='on'>
										<tr>
											<th class='box_01'></th>
											<td class='box_02' ><a href='../promotion/cupon_publish_list.php?mmode=personalization&mem_ix=".$mem_ix."'>발급 목록 보기</a></td>
											<th class='box_03'></th>
										</tr>
										</table>
										
									</td>
								</tr>
								</table>
							</div>
				    </td>
					</tr>
      </table>
    </td>
  </tr>";
  }
  $Contents .= "
	<tr>
	<td colspan=7>
		<form name='search_coupon' target='_self'>
		<input type='hidden' name='mmode' value='$mmode'>
		<input type='hidden' name='mem_ix' value='$mem_ix'>
		<table border='0' cellpadding='0' cellspacing='0' width='100%'>
			<tr>
				<td style='width:100%;' valign=top colspan=3>
					<table width=100%  border=0>
						<tr>
							<td align='left' colspan=2 height=50 width='100%' valign=top style='padding-top:5px;'>
								<table class='box_shadow' style='width:100%;' align=left cellpadding='0' cellspacing='0' border='0'>
									<tr>
										<th class='box_01'></th>
										<td class='box_02'></td>
										<th class='box_03'></th>
									</tr>
									<tr>
										<th class='box_04'></th>
										<td class='box_05' valign=top>
											<TABLE height=20 cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0>
												<TR>
													<TD bgColor=#ffffff style='padding:0 0 0 0;height:30px;'>
														<table cellpadding=2 cellspacing=1 width='100%' class='search_table_box'>
															<col width='15%'>
															<col width='35%'>
															<col width='15%'>
															<col width='35%'>";
															if($_SESSION["admin_config"][front_multiview] == "Y"){
															$Contents .= "
															<tr>
																<td class='search_box_title' > 프론트 전시 구분</td>
																<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
															</tr>";
															}
															
														if(!$pre_type){
															$Contents .= "
															<tr>
																<th class='search_box_title' >조건검색 : </th>
																<td class='search_box_item' colspan=3>
																	<table>
																		<tr>
																			<td>
																				<select name=search_type>
																				<option value='publish_name' ".CompareReturnValue("publish_name",$search_type,"selected").">쿠폰발행명</option>
																				<option value='cupon_no' ".CompareReturnValue("cupon_no",$search_type,"selected").">쿠폰번호</option>				
																				<option value='cp.publish_ix' ".CompareReturnValue("cp.publish_ix",$search_type,"selected").">쿠폰발행키</option>
																				</select>
																			</td>
																			<td><input type=text class=textbox name='search_text' value='".$search_text."' style='width:200px;' ></td>
																		</tr>

																	</table>
																</td>
															</tr>
															
															<tr >
															  <td class='input_box_title' >   <label for='cupon_use_div_' >쿠폰사용 구분</label> <!--<input type='checkbox' name='cupon_use_div_all' id='cupon_use_div_' value='1'  validation=true title='쿠폰사용' onclick=\"if($(this).attr('checked') == 'checked'){ $('.cupon_use_div').attr('checked','checked');}else{ $('.cupon_use_div').attr('checked',false);}\" ".($cupon_use_div_all ? "checked":"")."> --></td>
															  <td class='input_box_item' colspan=3>";
															  $Contents .= " ";
															  foreach($_COUPON_USE_DIV as $key => $value){
																$Contents .= "<input type='checkbox' name='cupon_use_div[]' id='cupon_use_div_".$key."' class='cupon_use_div' value='".$key."' ".(in_array($key,$cupon_use_div) ? "checked":"")." validation=true title='쿠폰사용'> <label for='cupon_use_div_".$key."' >".$value."</label> ";
															  }
															  $Contents .= " 
															  </td>
															</tr>
															<tr height=30>
                                                                <th class='search_box_title' ><label for='cupon_div_' >쿠폰종류</label><!--<input type='checkbox' name='cupon_div_all' id='cupon_div_' value='1'   validation=true title='쿠폰종류' onclick=\"if($(this).attr('checked') == 'checked'){ $('.cupon_div').attr('checked','checked');}else{ $('.cupon_div').attr('checked',false);}\" ".($cupon_div_all ? "checked":"").">--></th>
                                                                <td class='search_box_item' colspan=3>";
																$Contents .= "  ";
															  foreach($_COUPON_KIND as $key => $value){
																$Contents .= "<input type='checkbox' name='cupon_div[]' id='cupon_div_".$key."' class='cupon_div' value='".$key."' ".(in_array($key,$cupon_div) ? "checked":"")." validation=true title='쿠폰종류'> <label for='cupon_div_".$key."' >".$value."</label> ";
															  }
															  $Contents .= "
																		</td>
																	</tr>
															<tr height=30>
																<th class='search_box_title' ><label for='use_product_type_'>사용가능상품</label><!--<input type='checkbox' name='use_product_type_all' id='use_product_type_' onFocus='this.blur();' align='middle' value='1'  onclick=\"if($(this).attr('checked') == 'checked'){ $('.use_product_type').attr('checked','checked');}else{ $('.use_product_type').attr('checked',false);}\" ".($use_product_type_all ? "checked":"").">-->&nbsp;</th>
																<td class='search_box_item' colspan=3>
																	<input type='checkbox' name='use_product_type[]' id='use_product_type_1' class='use_product_type' onFocus='this.blur();' align='middle' value='1' ".(in_array(1,$use_product_type) ? "checked":"")."><label for='use_product_type_1'>전체상품</label>&nbsp;
																	<input type='checkbox' name='use_product_type[]' id='use_product_type_2' class='use_product_type' onFocus='this.blur();' align='middle' value=2 ".(in_array(2,$use_product_type) ? "checked":"")."><label for='use_product_type_2'>카테고리</label>&nbsp;
																	<input type='checkbox' name='use_product_type[]' id='use_product_type_3' class='use_product_type' onFocus='this.blur();' align='middle' value=3 ".(in_array(3,$use_product_type) ? "checked":"")."><label for='use_product_type_3'>상품별</label>&nbsp;
																	 <input type='checkbox' name='use_product_type[]' id='use_product_type_4' class='use_product_type' onFocus='this.blur();' align='middle' value=4 ".(in_array(4,$use_product_type) ? "checked":"")."><label for='use_product_type_4'>브랜드</label>&nbsp;
																	 <input type='checkbox' name='use_product_type[]' id='use_product_type_5' class='use_product_type' onFocus='this.blur();' align='middle' value=5 ".(in_array(5,$use_product_type) ? "checked":"")."><label for='use_product_type_5'>셀러</label>&nbsp;
																</td>
															</tr>
															<tr height=30>
																<th class='search_box_title' ><span style='padding-left:2px' class='helpcloud' help_width='240' help_height='10' help_html='선택시 전체 발행상태가 체크됩니다.'><label for='publish_type_' class='green'>발행대상</label></span> <!--<input type='checkbox' name='publish_type_all' id='publish_type_' onFocus='this.blur();' align='middle' value='1'   onclick=\"if($(this).attr('checked') == 'checked'){ $('.publish_type').attr('checked','checked');}else{ $('.publish_type').attr('checked',false);}\"  ".($publish_type_all ? "checked":"")." >--></th>
																<td class='search_box_item' colspan=3>";
																$Contents .= "";

																 foreach($_PUBLISH_TYPE as $key => $value){

																	$Contents .= "<input type='checkbox' name='publish_type[]' id='publish_type_".$key."' class='publish_type' onFocus='this.blur();' align='middle' value='".$key."'  ".($publish_type == $key ? "checked":"")."  ".(in_array($key,$publish_type) ? "checked":"")." ><span style='padding-left:2px' class='helpcloud' help_width='280' help_height='10' help_html='".$value["desc"]."'><label for='publish_type_".$key."' class='green'>".$value["text"]."</label></span>";
																  }
																	/*
																	$Contents .= "
																	<input type='radio' name='publish_type' id='publish_type_' onFocus='this.blur();' align='middle' value='' ".CompareReturnValue($publish_type,'',' checked')."><label for='publish_type_'>모두보기</label>&nbsp;
																	<input type='radio' name='publish_type' id='publish_type_2' onFocus='this.blur();' align='middle' value=2 ".CompareReturnValue($publish_type,'2',' checked')."><label for='publish_type_2'>전체회원(일반 발행)</label>&nbsp;

																	<input type='radio' name='publish_type' id='publish_type_1' onFocus='this.blur();' align='middle' value=1 ".CompareReturnValue($publish_type,'1',' checked')."><label for='publish_type_1'>고객지정 발행</label>&nbsp;

																	<input type='radio' name='publish_type' id='publish_type_3' onFocus='this.blur();' align='middle' value=3 ".CompareReturnValue($publish_type,'3',' checked')."><label for='publish_type_3'>회원그룹 발행</label>&nbsp;
																	
																	 <input type='radio' name='publish_type' id='publish_type_4' onFocus='this.blur();' align='middle' value=4 ".CompareReturnValue($publish_type,'4',' checked')."><label for='publish_type_4'>회원가입시 자동발급</label>&nbsp;

																	 <input type='radio' name='publish_type' id='publish_type_5' onFocus='this.blur();' align='middle' value=5 ".CompareReturnValue($publish_type,'5',' checked')."><label for='publish_type_5'>생일/결혼기념일 자동발급</label>&nbsp;";
																	*/
																	 $Contents .= "

																</td>
															</tr>
															<tr>
																<td class='search_box_title' ><span style='padding-left:2px' class='helpcloud' help_width='240' help_height='10' help_html='선택시 전체 발급구분이 체크됩니다.'><label for='issue_type_all' class='green'>발급구분</label></span> <!--<input type='checkbox' name='issue_type_all' id='issue_type_all' onFocus='this.blur();' align='middle' value='1'   onclick=\"if($(this).attr('checked') == 'checked'){ $('.issue_type').attr('checked','checked');}else{ $('.issue_type').attr('checked',false);}\"  ".($issue_type_all ? "checked":"")." >--></td>
																<td class='search_box_item' colspan='3'>
																	<input type='checkbox' name='issue_type[]' id='issue_type_1' class='issue_type' align='middle' value='1'  ".(in_array(1,$issue_type) ? "checked":"")."><label for='issue_type_1' class='green'>관리자 선택</label> 
																	<input type='checkbox' name='issue_type[]' id='issue_type_2' class='issue_type' align='middle' value='2'  ".(in_array(2,$issue_type) ? "checked":"")."><label for='issue_type_2' class='green'>고객다운로드</label> 
																</td>
																<!--td class='search_box_title' >  <b>C/S 쿠폰여부</b></td>
																<td class='search_box_item'>
																<input type='checkbox' name='is_cs' id='is_cs_1'  align='middle' value='1' ".($is_cs == '1' ? "checked":"")."><label for='is_cs_1' class='green'>CS쿠폰</label>
																</td-->
															</tr>
															<tr >
															  <td class='search_box_title' >  <b>노출여부</b></td>
															  <td class='search_box_item'  >
																	<input type='checkbox' name='disp[]' id='disp_1'  align='middle' value='1' ".(in_array("1",$disp) ? "checked":"")."><label for='disp_1' class='green'>노출함</label> 
																	<input type='checkbox' name='disp[]' id='disp_0'  align='middle' value='0' ".(in_array("0",$disp) ? "checked":"")."><label for='disp_0' class='green'>노출안함</label> 
															  </td>
															  <td class='input_box_title' >  <b>사용여부</b></td>
															  <td class='input_box_item'>
																	<input type='checkbox' name='is_use[]' id='is_use_1'  align='middle' value='1' ".(in_array("1",$is_use) ? "checked":"")."><label for='is_use_1' class='green'>사용함</label> 
																	<input type='checkbox' name='is_use[]' id='is_use_0'  align='middle' value='0' ".(in_array("0",$is_use) ? "checked":"")."><label for='is_use_0' class='green'>미사용</label> 
															  </td>
															</tr>
															";
														}

														if($pre_type == "group_cupon"){
														$Contents .= "
															<tr height=27>
																<th class='search_box_title' >그룹검색</th>
																<td class='search_box_item' colspan=3>
																	".makeGroupSelectBox($db,"gp_ix",$gp_ix)."
																</td>
															</tr>";
														}
														$Contents .= "
															<tr height=27>
																<td class='search_box_title' ><label for='publlish_date'><b>발행일자</b></label><input type='checkbox' name='publlish_date' id='publlish_date' value='1' onclick='ChangeRegistDate(document.search_coupon);' ".(($publlish_date==1)?"checked":"")."></td>
																<td class='search_box_item' colspan=3>
																".search_date('cupon_publish_sdate','cupon_publish_edate',$cupon_publish_sdate,$cupon_publish_edate,'N','D')."
																	 
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
							<td colspan=3 align=center  style='padding:10px 0 0 0'>
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
    <td height='5'> 쿠폰 총 발행 수 :&nbsp;<b>".$total."</b>&nbsp;건 </td>
  </tr>
  <tr>
    <td height='10'></td>
  </tr>
  <tr>
    <td valign='top'>
      <table width='100%' border='0' cellpadding='0' cellspacing='0'>
        <tr>
          <td valign='top'>
            <table width='100%' border='0' cellpadding='0' cellspacing='1' class='list_table_box'>
			<col width='7%' >
			".($_SESSION["admin_config"][front_multiview] == "Y" ? "<col width=10%>":"")."
			<col width='15%'>
			<col width='18%'>
			<col width='*' >
			<col width='10%'>
			<col width='10%'>
              <tr align=center height=30>
                <td class='s_td' rowspan=2 nowrap>번호</td>
				".($_SESSION["admin_config"][front_multiview] == "Y" ? "<td class='m_td' rowspan=2 > 프론트전시</td>":"")."
                <td class='m_td' rowspan=2>발행일자</td>
                <td class='m_td' rowspan=2>쿠폰명/쿠폰발행번호</td>
                <td class='m_td' rowspan=2>발행명/발행구분/발급구분</td>
                <td class='e_td' colspan=2>관리</td>
                <!--td class='m_td'>사용</td-->
              </tr>
			  <tr height=30>
				<td class='m_td' >노출여부</td>
				<td class='m_td' >사용여부</td>
			  </tr>";

if($db->total < 1){
	$Contents .= "<tr bgcolor=#ffffff height=50><td colspan=7 align=center> 등록된 쿠폰 정보가 없습니다. </td></tr>";
}else{
$max = 10;

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

if($pre_type == "group_cupon"){

	$sql = "select 
				distinct cp.publish_tmp_ix,
				cp.*,
				c.cupon_kind
			from 
				".TBL_SHOP_CUPON." as c
				inner join shop_cupon_publish_tmp as cp on (c.cupon_ix = cp.cupon_ix)
				left join shop_cupon_relation_group as crg on (cp.publish_tmp_ix = crg.publish_tmp_ix)
			$where
				order by cp.regdate desc LIMIT $start, $max";
}else{

	$sql = "select 
				c.*,
				(select count(*) from shop_cupon_regist cr where cr.publish_ix = c.publish_ix ) as regist_cnt
			from (
				select
					cp.*,
					c.cupon_kind
				from 
					".TBL_SHOP_CUPON." as c 
					join ".TBL_SHOP_CUPON_PUBLISH." as cp on (c.cupon_ix = cp.cupon_ix)
				$where
					order by cp.regdate desc 
					LIMIT $start, $max
			) c";
}
//echo nl2br($sql)."<br><br><br><br><br><br><br>";

//echo $sql."<br>";
$db->query($sql);

for($i=0;$i<$db->total;$i++){
	$db->fetch($i);
	$no = $total - ($page - 1) * $max - $i;


	if($db->dt[use_date_type] == 1){
		if($db->dt[publish_date_type] == 1){
			$date_type = '년';
		}else if($db->dt[publish_date_type] == 2){
			$date_type = '개월';
		}else if($db->dt[publish_date_type] == 3){
			$date_type = '일';
		}
		$date_differ = $db->dt[publish_date_differ];
		$use_date_type = '발행일';
		$priod_str = $use_date_type."로부터 ".$date_differ." ".$date_type."간";

	}else if($db->dt[use_date_type] == 2){
		if($db->dt[regist_date_type] == 1){
			$date_type = '년';
		}else if($db->dt[regist_date_type] == 2){
			$date_type = '개월';
		}else if($db->dt[regist_date_type] == 3){
			$date_type = '일';
		}
		$date_differ = $db->dt[regist_date_differ];
		$use_date_type = '발급일';
		$priod_str = $use_date_type."로부터 ".$date_differ." ".$date_type."간";
	}else if($db->dt[use_date_type] == 3){
		$use_date_type = '사용기간';
		//$priod_str = $use_date_type." : ".ChangeDate($db->dt[use_sdate],"Y-m-d H:i:s")." ~ ".ChangeDate($db->dt[use_edate],"Y-m-d H:i:s")." ";
        $priod_str = "".$db->dt[use_sdate]." ~ ".$db->dt[use_edate]." ";
	}else if($db->dt[use_date_type] == 9){
		$use_date_type = '사용기간';
		$priod_str = "기간제한없음";
	}

	if($db->dt[use_product_type] == 3){
		
		if($pre_type == "group_cupon"){
			$sql = "Select crp.pid, p.pname ".$product_image_column_str." from shop_cupon_relation_product crp, shop_product p where p.id = crp.pid and publish_tmp_ix = '".$db->dt[publish_tmp_ix]."' order by crp.vieworder asc";
		}else{
			$sql = "Select crp.pid, p.pname ".$product_image_column_str." from shop_cupon_relation_product crp, shop_product p where p.id = crp.pid and publish_ix = '".$db->dt[publish_ix]."' order by crp.vieworder asc";
		}
		$rproduct_db->query($sql);

		for($j=0;$j < $rproduct_db->total;$j++){
			$rproduct_db->fetch($j);
			//$rproduct_str .= "<img src='".$admin_config[mall_data_root]."/images/product/c_".$rproduct_db->dt[pid].".gif' alt='".$rproduct_db->dt[pname]."' style='border:1px solid silver'> ";
			$rproduct_str .= "<img src='".PrintImage($admin_config[mall_data_root]."/images/product", $rproduct_db->dt[pid], "c" , $rproduct_db->dt)."' width=50 height=65 alt='".$rproduct_db->dt[pname]."' style='border:1px solid silver'> ";
		}
	}else if($db->dt[use_product_type] == 2){
		if($pre_type == "group_cupon"){
			$sql = "Select crc.cid from shop_cupon_relation_category crc, shop_category_info c
						where c.cid = crc.cid and publish_tmp_ix = '".$db->dt[publish_tmp_ix]."'
						order by crc.cpc_ix asc";
		}else{
			$sql = "Select crc.cid from shop_cupon_relation_category crc, shop_category_info c
						where c.cid = crc.cid and publish_ix = '".$db->dt[publish_ix]."'
						order by crc.cpc_ix asc";
		}
		$rproduct_db->query($sql);

		for($j=0;$j < $rproduct_db->total;$j++){
			$rproduct_db->fetch($j);
			$rproduct_str .= "<b>".getCategoryPathByAdmin($rproduct_db->dt[cid], 4)."</b> &nbsp;&nbsp;&nbsp;<span class=small>카테고리 등록상품</span> <br> ";
		}

		//$rproduct_str .= "<b>선택된 카테고리</b>";
	}else if($db->dt[use_product_type] == 4){

		if($pre_type == "group_cupon"){
			$sql = "Select brand_name from shop_cupon_relation_brand crb, shop_brand b
						where crb.b_ix = b.b_ix and crb.publish_tmp_ix = '".intval($db->dt[publish_tmp_ix])."'";
		}else{
			$sql = "Select brand_name from shop_cupon_relation_brand crb, shop_brand b
						where crb.b_ix = b.b_ix and crb.publish_ix = '".intval($db->dt[publish_ix])."'";
		}
		//echo $sql;
		$rproduct_db->query($sql);

		for($j=0;$j < $rproduct_db->total;$j++){
			$rproduct_db->fetch($j);
			if($j == 0){
				$rproduct_str .= "<b>".$rproduct_db->dt[brand_name]." </b>";
			}else{
				$rproduct_str .= ", <b>".$rproduct_db->dt[brand_name]." </b>";
			}
		}

		if($db->dt[is_include] == 1){
			$rproduct_str .="에 속한 브랜드 상품 ";
		}else{
			$rproduct_str .="를 제외한 브랜드 상품";
		}

	}else if($db->dt[use_product_type] == 5){
		$sql = "SELECT ccd.company_id, ccd.com_name 
					FROM common_company_detail ccd, shop_cupon_relation_seller crs 
					where ccd.company_id = crs.company_id and  crs.publish_ix = '".intval($db->dt[publish_ix])."'  ";
		$rproduct_db->query($sql);
		$selected_sellers = $rproduct_db->fetchall();
		
		 
		for($j = 0; $j < count($selected_sellers); $j++){
			if($j == 0){
				$rproduct_str .= "<b>".$selected_sellers[$j][com_name]."</b>";
			}else{
				$rproduct_str .= ", <b>".$selected_sellers[$j][com_name]."</b>";
			}
		}
		if($db->dt[is_include] == 1){
			$rproduct_str .="에 속한 셀러상품 ";
		}else{
			$rproduct_str .="를 제외한 셀러상품";
		}

	}else{
		$rproduct_str = "<b>전체 상품</b>";
	}

	if ($db->dt[publish_type] == 1){// 고객 지정발행일경우 

		$Contents .= "
			<!--- // 목록 반복 시작 ---------->
			<!--- / 지정발행일경우 - 쿠폰발행일자 - ()안은 유효기간, blue=사용날짜 ---------->
			<tr bgcolor='#ffffff'>
				<td class='list_box_td list_bg_gray' rowspan='2' nowrap>".$no."</td>";
if($_SESSION["admin_config"]["front_multiview"] == "Y"){
	$Contents .= "
		    <td class='list_box_td' rowspan=2 >".GetDisplayDivision($event_infos[$i][mall_ix], "text")."</td>";
}
	$Contents .= "
				<td class='list_box_td' nowrap>
					<font class='gray16'>".$db->dt[regdate] ."</font><br><!--font class='td16'>(2006-04-01)</font><br><font class='blue16'>2006-03-20</font-->
				</td>
				<td class='list_box_td list_bg_gray' style='line-height:120%;padding:3px 0px 3px 3px;' nowrap>
					<font class='yellow'>".$db->dt[cupon_kind] ."<br><a href='cupon_publish.php?publish_ix=".$db->dt[publish_ix] ."' class=blue>".$db->dt[cupon_no] ."</a></font>
				</td>
				<td class='list_box_td point' colspan='1'  style='line-height:150%;padding:10px 0px 10px 10px;text-align:left;'>
					".$db->dt[publish_name]."<br/>
					<font class='green'><b>고객지정 발행</b> <span class='small'><!--(관리자가 사용자를 직접 지정합니다)--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."</span></font><br>
					".($_ISSUE_TYPE[$db->dt[issue_type]]["text"])."(".$db->dt[regist_cnt].")
					
				</td>
				<td class='list_box_td list_bg_gray' nowrap>".($db->dt[disp] == "1" ? "노출":"미노출")."<!--img src='../image/icon_".($db->dt[disp] == "1" ? "o":"x").".gif' border='0'--></td>
				<td class='list_box_td list_bg_gray' nowrap>".($db->dt[is_use] == "1" ? "사용":"사용안함")."
					<!--img src='../image/icon_".($db->dt[is_use] == "1" ? "o":"x").".gif' border='0'-->
				</td>
			</tr>
			<tr bgcolor='#ffffff'>
				<td class='list_box_td' colspan='3'  style='padding:4px 4px;text-align:left; '>
					<table>";
					if($priod_str){
						$Contents .= "
						<tr><td>사용기간 : </td><td>".$priod_str."&nbsp;&nbsp;<!--(결제가격이 ".number_format($db->dt[publish_condition_price],0)." 원 이상인 상품에 사용가능)--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B',$db)."</td></tr>";
					}
					$Contents .= "
						<tr><td>사용가능상품 : </td><td style='padding:5px;line-height:120%'>".$rproduct_str." </td></tr>";
					if($db->dt[cupon_use_sdate] && $db->dt[cupon_use_edate]){
					$Contents .= "
						<tr><td>쿠폰노출기간 : </td><td style='padding:5px;line-height:120%'>".date("Y-m-d H:i:s",$db->dt[cupon_use_sdate])." ~ ".date("Y-m-d H:i:s",$db->dt[cupon_use_edate])." </td></tr>";
					}
						$Contents .= "
					</table>
				</td>
				<td class='list_box_td list_bg_gray' align='center' colspan=2 style='padding:10px;'>";
				if($mmode != "personalization"){
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
						if($db->dt[regist_cnt] > 0){
							$Contents .= "<a href=\"javascript:publish_modify('".$db->dt[publish_ix] ."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border='0' ></a> ";
						}else{
							$Contents .= "<a href='cupon_publish.php?publish_ix=".$db->dt[publish_ix] ."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border='0' ></a> ";
						}
					}else{
						$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border='0' ></a> ";
					}
				

					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
						$Contents .= "<a href='javascript:publish_delete(".intval($db->dt[publish_ix]).");'><img src='../images/".$admininfo["language"]."/btc_del.gif'></a>";
					}else{
						$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border='0' ></a> ";
					}
				}

				$Contents .= "<br><a href=\"javascript:PopSWindow('cupon_register_user.php?publish_ix=".$db->dt[publish_ix]."&mmode=".$mmode."&mem_ix=".$mem_ix."',900,800,'cupon_detail_pop');\" class=blue><img src='../images/".$admininfo["language"]."/btn_issue_cupon.gif' style='padding:5px 0;' align=absmiddle></a>
				</td>
			</tr>
			<!--- 지정발행일경우 / ---------->";
	}else{
		/*
		if($db->dt[publish_type]==3){
			$publish_type="회원가입 발행";
		}elseif($db->dt[publish_type]==4){
			$publish_type="<span class='red'>회원그룹 발행</span>";
		}else{
			$publish_type="일반 발행";
		}
		*/
		
		if($pre_type == "group_cupon"){
			$sql = "select
						sg.*
					from
						shop_cupon_relation_group as crg
						inner join shop_groupinfo as sg on (crg.gp_ix = sg.gp_ix)
					where
						crg.publish_tmp_ix = '".$db->dt[publish_tmp_ix]."'";
			$db1->query($sql);
			$data_array = $db1->fetchall();
			
			for($jj=0;$jj<count($data_array);$jj++){
				$gp_name .= " (".$data_array[$jj][gp_level].")".$data_array[$jj][gp_name]." ";
			}
		}

		$Contents .= "
			<!--- / 무작위발행일경우 - 쿠폰발행일자 - ()안은 유효기간 ---------->
			<tr bgcolor='#ffffff'>
				<td class='list_box_td list_bg_gray' rowspan='2'  nowrap>".$no."</td>";
if($_SESSION["admin_config"]["front_multiview"] == "Y"){
	$Contents .= "
		    <td class='list_box_td' rowspan=2 >".GetDisplayDivision($event_infos[$i][mall_ix], "text")."</td>";
}
	$Contents .= "
				<td class='list_box_td' nowrap align='center'><font class='gray16'>".$db->dt[regdate] ."</font><!--br><font class='td16'>(2006-04-01)</font--></td>
				<td class='list_box_td list_bg_gray' nowrap style='line-height:120%;padding:10px 0px 10px 10px;'>";
					if($pre_type!="group_cupon"){
						$Contents .= "<font class='yellow'>".$db->dt[cupon_kind] ."<br><a href='cupon_publish.php?publish_ix=".$db->dt[publish_ix] ."' class=blue>".$db->dt[cupon_no] ."</a></font>";
					}else{
						$Contents .= "<a href='group_cupon_publish.php?view_type=mem_group&publish_tmp_ix=".$db->dt[publish_tmp_ix] ."' class=blue>".$db->dt[cupon_kind] ."</a></font>";
					}
				$Contents .= "
				</td>
				<td class='list_box_td point' colspan='1' style='line-height:150%;padding:10px;text-align:left'>
					".$db->dt[publish_name]."<br/>
					<font class='orange'><b>".$_PUBLISH_TYPE[$db->dt[publish_type]]["text"].$gp_name."</b> <span class='small'><!--(사용자들이 쿠폰을 직접 등록할 수 있습니다.)-->".($db->dt[publish_type]==3 ||$db->dt[publish_type]==4 ? "" : getTransDiscription(md5($_SERVER["PHP_SELF"]),'D') )."</span></font><br>
					".($_ISSUE_TYPE[$db->dt[issue_type]]["text"])."(".$db->dt[regist_cnt].")";

					
				$Contents .= "
				</td>
				<td class='list_box_td list_bg_gray' nowrap>".($db->dt[disp] == "1" ? "노출":"미노출")."<!--img src='../image/icon_".($db->dt[disp] == "1" ? "o":"x").".gif' border='0'--></td>
				<td class='list_box_td list_bg_gray' nowrap>".($db->dt[is_use] == "1" ? "사용":"사용안함")."
					<!--img src='../image/icon_".($db->dt[is_use] == "1" ? "o":"x").".gif' border='0'-->
				</td>
				<!--td class='list_box_td list_bg_gray' nowrap>
					<a href='iframe_admin_random_send_cupon_list.php?publish_ix=".$db->dt[publish_ix] ."'>[목록보기]</a><br>
					<img src='../img/0.gif' width='1' height='5'><br><a href='iframe_admin_random_send_cupon.php?publish_ix=".$db->dt[publish_ix] ."'>[쿠폰보내기]</a>
				</td-->
			  </tr>
			  <tr bgcolor='#ffffff'>
				<td colspan='3' class='con_l' style='padding:4px 4px '>
					<table>";
					$Contents .= "
						<tr><td nowrap>사용기간 : </td><td>".$priod_str."&nbsp;&nbsp;<!--(결제가격이 ".number_format($db->dt[publish_condition_price],0)." 원 이상인 상품에 사용가능)--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A',$db)."</td></tr>";
					$Contents .= "
						<tr><td nowrap>사용가능상품 : </td><td style='padding:5px;'>".$rproduct_str." </td></tr>";
					if($db->dt[cupon_use_sdate] && $db->dt[cupon_use_edate]){
					$Contents .= "
						<tr><td>쿠폰노출기간 : </td><td style='padding:5px;line-height:120%'>".date("Y-m-d H:i:s",$db->dt[cupon_use_sdate])." ~ ".date("Y-m-d H:i:s",$db->dt[cupon_use_edate])." </td></tr>";
					}
						$Contents .= "
					</table>
				</td>
				<td align='center' colspan=2 style='padding:10px;'>";
				if($mmode != "personalization"){
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
						if($db->dt[regist_cnt] > 0){
							$Contents .= "<a href=\"javascript:publish_modify('".$db->dt[publish_ix] ."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border='0' ></a> ";
						}else{
							if($pre_type!="group_cupon"){
								$Contents .= "<a href='cupon_publish.php?publish_ix=".$db->dt[publish_ix] ."' class=blue><img src='../images/".$admininfo["language"]."/btc_modify.gif' border='0' ></a>";
							}else{
								$Contents .= "<a href='group_cupon_publish.php?view_type=mem_group&publish_tmp_ix=".$db->dt[publish_tmp_ix] ."' class=blue><img src='../images/".$admininfo["language"]."/btc_modify.gif' border='0' ></a>";
							}
							//$Contents .= "<a href='cupon_publish.php?publish_ix=".$db->dt[publish_ix] ."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border='0' ></a> ";
						}
					}else{
						$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border='0' ></a> ";
					}

					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
						if($pre_type!="group_cupon"){
							$Contents .= " <a href='javascript:publish_delete(".intval($db->dt[publish_ix]).");'><img src='../images/".$admininfo["language"]."/btc_del.gif'></a>";
						}else{
							$Contents .= " <a href='javascript:publish_tmp_delete(".intval($db->dt[publish_tmp_ix]).");'><img src='../images/".$admininfo["language"]."/btc_del.gif'></a>";
						}
					}else{
						$Contents .= " <a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border='0' ></a> ";
					}
				}

				if($pre_type!="group_cupon"){
						$Contents .= "<br><a href=\"javascript:PopSWindow('cupon_register_user.php?publish_ix=".$db->dt[publish_ix]."&mmode=".$mmode."&mem_ix=".$mem_ix."',900,800,'cupon_detail_pop');\" class=blue><img src='../images/".$admininfo["language"]."/btn_issue_cupon.gif' style='padding:5px 0;' align=absmiddle></a>";
					}
				$Contents .= "
				</td>
			  </tr>
			  <!--- 무작위발행일경우 / ---------->";
		unset($gp_name);
	}
$rproduct_str='';
}//for

if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

$Contents .= "
			  <tr bgcolor=#ffffff>
				<td height='35' align='center' colspan=7 >".page_bar($total, $page, $max,$query_string,'')."</td>
			  </tr>";
}
$Contents .= "

              <!--- 목록 반복 끝 // ---------->

            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td height='20' valign='top'></td>
  </tr>

</table>";


if($mmode == "personalization"){
	$P = new ManagePopLayOut();
	$P->addScript = "<script language='javascript' src='orders.js'></script>\n".$Script;
	$P->OnloadFunction = "";
	$P->strLeftMenu = order_menu();
	$P->Navigation = "HOME > 전시관리 > 쿠폰 사용자 발급 리스트";
	$P->title = "쿠폰 사용자 발급 리스트";
	$P->NaviTitle =  "쿠폰 사용자 발급 리스트";
	$P->strContents =  $Contents;
	$P->layout_display = false;
	$P->view_type = "personalization";
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	//if($regdate!=1) $P->OnloadFunction = "onLoad('$sDate','$eDate', document.search_coupon);";//MenuHidden(false);
	//else $P->OnloadFunction = "onLoad2('$sDate','$eDate', document.search_coupon);";//MenuHidden(false);
	//$P->OnloadFunction = "init();";
	$P->strLeftMenu = promotion_menu();
	$P->Navigation = "프로모션(마케팅)/전시 > 쿠폰관리 > 쿠폰발행목록";
	$P->title = "쿠폰발행목록";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}
?>
