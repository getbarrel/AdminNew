<?php
include("../class/layout.class");

if ($FromYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$startDate = $cupon_publish_sdate;//$FromYY.$FromMM.$FromDD;
	$endDate = $cupon_publish_edate;//$ToYY.$ToMM.$ToDD;
}

if(empty($max)){
	$max = 50;
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$where = " where c.cupon_ix is not null ";

if($search_text != ""){
	if($search_type != ""){
		$where .= " and $search_type LIKE '%$search_text%' ";
	}else{
		$where .= " and (publish_name LIKE '%$search_text%' or cupon_no LIKE '%$search_text%' or cp.publish_ix LIKE '%$search_text%') ";
	}
}

if($publlish_date) {
	if($cupon_publish_sdate != "" && $cupon_publish_edate != ""){
		$where .= " and  cp.regdate between  '$cupon_publish_sdate 00:00:00' and '$cupon_publish_edate 23:59:59' ";
	}
}

$db = new Database();
$db1 = new Database();
$rproduct_db = new Database();

$use_product_type = $_GET["use_product_type"];
if($use_product_type){
	$where .= " AND use_product_type = '".$use_product_type."' ";
}

if($cupon_sale_type){
	$where .= " AND cupon_sale_type = '".$cupon_sale_type."' ";
}

$publish_type = $_GET["publish_type"];
if($publish_type){
	$where .= " AND publish_type = '".$publish_type."' ";
}

$cupon_div = $_GET["cupon_div"];
if($cupon_div){
	$where .= " AND cp.cupon_div = '".$cupon_div."' ";
}

$cupon_use_div = $_GET["cupon_use_div"];
if($cupon_use_div){
	$where .= " AND c.cupon_use_div = '".$cupon_use_div."' ";
}

$issue_type = $_GET["issue_type"];
if($issue_type){
	$where .= " AND cp.issue_type = '".$issue_type."' ";
}

if($_GET["is_cs"] != ""){
	$where .= " and cp.is_cs =  '".$_GET["is_cs"]."' ";
}

$is_use = $_GET["is_use"];
if($is_use != ""){
	$where .= " AND cp.is_use = '".$is_use."' ";
}

$disp = $_GET["disp"];
if($disp != ""){
	$where .= " AND cp.disp = '".$disp."' ";
}

if($_GET["mall_ix"] != ""){
	$where .= " and cp.mall_ix =  '".$_GET["mall_ix"]."' ";
}

if($cp_state){
	$now_time = time();
	switch($cp_state){
        case '1'://발급중
			$where .= " and cp.is_use not in  ('0','3') and if (cp.use_date_type in ('1','3') , '".$now_time."' between cp.cupon_use_sdate and cp.cupon_use_edate ,cp.use_date_type in ('2','9'))
			";
            break;
        case '2'://발급대기
			$where .= " and cp.is_use not in  ('0','3') and cp.use_date_type in ('1','3') and cp.cupon_use_sdate > '".$now_time."' ";
            break;
        case '3'://발급중지
			$where .= " and cp.is_use in ('0','3') ";
            break;
        case '4'://발급종료
            $where .= " and cp.is_use not in  ('0','3') and cp.use_date_type in ('1','3') and cp.cupon_use_edate < '".$now_time."'";
            break;
	}
}


$sql = "select 
			cp.*,
			c.cupon_kind
		from
			".TBL_SHOP_CUPON."  c
			inner join ".TBL_SHOP_CUPON_PUBLISH." cp on c.cupon_ix = cp.cupon_ix";
$db->query($sql);
$real_total = $db->total;

$sql = "select 
			cp.*,
			c.cupon_kind
		from
			".TBL_SHOP_CUPON."  c
			inner join ".TBL_SHOP_CUPON_PUBLISH." cp on c.cupon_ix = cp.cupon_ix
		$where
			order by cp.regdate desc";

//echo nl2br($sql);
$db->query($sql);
$total = $db->total;

$Script = "
<!--script language='javascript' src='../include/DateSelect.js'></script-->
<script language='javascript'>
function publish_modify(publish_ix){
	var str = '쿠폰의 내용을 수정하시겠습니까?\\n*수정된 정보는 현재 발급되어 있는 고객의 모든 쿠폰에도 적용됩니다.';
	if(confirm(str)){
		window.document.location.href='coupon_regist.php?publish_ix='+publish_ix;
	}
}

function publish_modify_all(publish_ix){
    var str = '쿠폰의 내용을 수정하시겠습니까?\\n*쿠폰이 회원에게 발급 된 경우 수정이 불가능 합니다.';
	if(confirm(str)){
		window.document.location.href='coupon_regist.php?act=copy&sub_mode=modify&publish_ix='+publish_ix;
	}
}

function publish_delete(publish_ix){
	var str = '쿠폰을 삭제하시겠습니까?\\n*쿠폰 삭제 시, 현재 발급되어 있는 고객의 모든 쿠폰이 삭제됩니다.\\n*단순 발급 중지를 원하시면, 쿠폰을 미사용으로 변경해주세요.';
	if(confirm(str)){
		window.frames['act'].location.href='cupon.act.php?act=delete&publish_ix='+publish_ix;
	}
}

function publish_copy(publish_ix){
	var str = '해당 쿠폰의 내용을 복사하시겠습니까?';
	if(confirm(str)){
//		window.frames['act'].location.href='cupon.act.php?act=copy&publish_ix='+publish_ix;
		window.location.href='coupon_regist.php?act=copy&publish_ix='+publish_ix;
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
 
function changeMax(obj){
	var max = $(obj).val();
	$('form[name=search_coupon]').find('input[name=max]').val(max);
	$('form[name=search_coupon]').submit();
}

function checkIx(){
	if($('#cupon_ix_all').is(':checked')){
		$('input[name^=cupon_ix]').prop('checked', true);
	}else{
		$('input[name^=cupon_ix]').prop('checked', false);
	}
}

function submitToChange(){
	var update_type = $('#update_type').val();
	var detail = $('input[name=detail]:checked').val();

	$('input[name=update_type]').val(update_type);
	$('input[name=act_detail]').val(detail);

	$('form[name=list_frm]').submit();
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
		<td align='left' colspan=6> ".GetTitleNavigation("쿠폰 리스트", "전시관리 > 쿠폰 리스트")."</td>
  </tr>";
  $Contents .= "
	<tr>
	<td colspan=7>
		<form name='search_coupon' target='_self'>
		<input type='hidden' name='mmode' value='$mmode'>
		<input type='hidden' name='mem_ix' value='$mem_ix'>
		<input type='hidden' name='max' value='".$max."'>
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
																<td class='search_box_item' >
																	<table>
																		<tr>
																			<td>
																				<select name=search_type>
																					<option value='' ".CompareReturnValue("",$search_type,"selected").">통합 검색</option>
																					<option value='publish_name' ".CompareReturnValue("publish_name",$search_type,"selected").">쿠폰 이름</option>
																					<option value='cupon_no' ".CompareReturnValue("cupon_no",$search_type,"selected").">쿠폰 번호</option>				
																					<option value='cp.publish_ix' ".CompareReturnValue("cp.publish_ix",$search_type,"selected").">쿠폰 발행키</option>
																				</select>
																			</td>
																			<td><input type=text class=textbox name='search_text' value='".$search_text."' style='width:200px;' ></td>
																		</tr>

																	</table>
																</td>
                                                                <th class='search_box_title' ><label for='cupon_div_' >쿠폰종류</label><!--<input type='checkbox' name='cupon_div_all' id='cupon_div_' value='1'   validation=true title='쿠폰종류' onclick=\"if($(this).attr('checked') == 'checked'){ $('.cupon_div').attr('checked','checked');}else{ $('.cupon_div').attr('checked',false);}\" ".($cupon_div_all ? "checked":"").">--></th>
                                                                <td class='search_box_item' >";
																$Contents .= "<input type='radio' name='cupon_div' id='cupon_div_' class='cupon_div' ".($cupon_div == "" ? "checked" : "")." value='' title='쿠폰종류'> <label for='cupon_div_' >전체</label> ";
															  foreach($_COUPON_KIND as $key => $value){
																$Contents .= "<input type='radio' name='cupon_div' id='cupon_div_".$key."' class='cupon_div' value='".$key."' ".CompareReturnValue($key ,$cupon_div,"checked")." validation=true title='쿠폰종류'> <label for='cupon_div_".$key."' >".$value."</label> ";
															  }
															  $Contents .= "
																		</td>
															</tr>
															<tr height=30>
																  <td class='input_box_title' >   <label for='cupon_use_div_' >사용 범위</label> <!--<input type='checkbox' name='cupon_use_div_all' id='cupon_use_div_' value='1'  validation=true title='쿠폰사용' onclick=\"if($(this).attr('checked') == 'checked'){ $('.cupon_use_div').attr('checked','checked');}else{ $('.cupon_use_div').attr('checked',false);}\" ".($cupon_use_div_all ? "checked":"")."> --></td>
																  <td class='input_box_item' >";
																$Contents .= "<input type='radio' name='cupon_use_div' id='cupon_use_div_' class='cupon_use_div' ".($cupon_use_div == "" ? "checked" : "")." value='' title='쿠폰사용'> <label for='cupon_use_div_' >전체</label> ";
																  foreach($_COUPON_USE_DIV as $key => $value){
																	$Contents .= "<input type='radio' name='cupon_use_div' id='cupon_use_div_".$key."' class='cupon_use_div' value='".$key."' ".CompareReturnValue($key ,$cupon_use_div,"checked")." validation=true title='쿠폰사용'> <label for='cupon_use_div_".$key."' >".$value."</label> ";
																  }
																  $Contents .= " 
																  </td>
																	<th class='search_box_title' ><label for='use_product_type_'>쿠폰 혜택</label></th>
																	<td class='search_box_item' >
																		<input type='radio' name='cupon_sale_type' id='cupon_sale_type_' ".($cupon_sale_type == "" ? "checked" : "")." value=''> <label for='cupon_sale_type_' >전체</label> 																		 
																		<input type='radio' name='cupon_sale_type' id='cupon_sale_type_1' ".($cupon_sale_type == "1" ? "checked" : "")." value='1'> <label for='cupon_sale_type_1' >정률 할인</label>
																		<input type='radio' name='cupon_sale_type' id='cupon_sale_type_2' ".($cupon_sale_type == "2" ? "checked" : "")." value='2'> <label for='cupon_sale_type_2' >정액 할인</label>
																	</td>
															</tr>
															<tr height=30>
																<td class='search_box_title' ><label for='issue_type_all' class='green'>발급 방식</label></td>
																<td class='search_box_item'>
																	<input type='radio' name='issue_type' id='issue_type_' class='issue_type' align='middle' value=''  ".($issue_type == "" ? "checked" : "")."><label for='issue_type_' class='green'>전체</label>";
																	  foreach($_ISSUE_TYPE as $key => $value){
																		if($key == 5){
																			continue;
																		}
																		$Contents .= "<input type='radio' name='issue_type' id='issue_type_".$key."' class='issue_type' value='".$key."' ".($issue_type == $key ? "checked":"")."><label for='issue_type_".$key."' >".$value[text]."</label>";
																	  }
															
															$Contents .= "
																</td>
																<th class='search_box_title' ><label for='publish_type_' class='green'>발급 대상</label></th>
																<td class='search_box_item' >";
																$Contents .= "<input type='radio' name='publish_type' id='publish_type_' class='publish_type' onFocus='this.blur();' align='middle' value=''  ".($publish_type == "" ? "checked" : "")." ><label for='publish_type_' class='green'>전체</label>";
																 foreach($_PUBLISH_TYPE as $key => $value){

																	$Contents .= "<input type='radio' name='publish_type' id='publish_type_".$key."' class='publish_type' onFocus='this.blur();' align='middle' value='".$key."'  ".($publish_type == $key ? "checked":"")." ><label for='publish_type_".$key."' class='green'>".$value["text"]."</label>";
																  }
																	 $Contents .= "

																</td>
															</tr>
															<tr >
															  <td class='input_box_title' >  <b>사용 여부</b></td>
															  <td class='input_box_item'>
																	<input type='radio' name='is_use' id='is_use_' class='publish_type' onFocus='this.blur();' align='middle' value=''  ".($is_use == "" ? "checked" : "")." ><label for='is_use_'>전체</label>
																	<input type='radio' name='is_use' id='is_use_1'  align='middle' value='1' ".CompareReturnValue("1" ,$is_use,"checked")."><label for='is_use_1' class='green'>사용</label> 
																	<input type='radio' name='is_use' id='is_use_0'  align='middle' value='0' ".CompareReturnValue("0",$is_use,"checked")."><label for='is_use_0' class='green'>미사용</label> 
																	<input type='radio' name='is_use' id='is_use_3'  align='middle' value='3' ".CompareReturnValue("3",$is_use,"checked")."><label for='is_use_3' class='green'>사용불가</label> 
															  </td>
															  <td class='search_box_title' >  <b>노출 여부</b></td>
															  <td class='search_box_item'  >
																	<input type='radio' name='disp' id='disp_' class='publish_type' onFocus='this.blur();' align='middle' value=''  ".($disp == "" ? "checked" : "")." ><label for='disp_'>전체</label>
																	<input type='radio' name='disp' id='disp_1'  align='middle' value='1' ".CompareReturnValue("1" ,$disp,"checked")."><label for='disp_1' class='green'>노출</label> 
																	<input type='radio' name='disp' id='disp_0'  align='middle' value='0' ".CompareReturnValue("0" ,$disp,"checked")."><label for='disp_0' class='green'>미노출</label> 
															  </td>
															</tr>
															<tr> 
																<td class='input_box_title' >  <b>발급현황</b></td>
																  <td class='input_box_item' colspan='3'>
																		<input type='radio' name='cp_state' id='cp_state' align='middle' value=''  ".($cp_state == "" ? "checked" : "")." ><label for='cp_state'>전체</label>
																		<input type='radio' name='cp_state' id='cp_state_1'  align='middle' value='1' ".CompareReturnValue("1" ,$cp_state,"checked")."><label for='cp_state_1'>발급중</label> 
																		<input type='radio' name='cp_state' id='cp_state_2'  align='middle' value='2' ".CompareReturnValue("2",$cp_state,"checked")."><label for='cp_state_2' >발급대기</label> 
																		<input type='radio' name='cp_state' id='cp_state_3'  align='middle' value='3' ".CompareReturnValue("3",$cp_state,"checked")."><label for='cp_state_3' >발급중지</label>
																		 <input type='radio' name='cp_state' id='cp_state_4'  align='middle' value='4' ".CompareReturnValue("4",$cp_state,"checked")."><label for='cp_state_4' >발급종료</label>
																  </td>
															</tr>
															";
														}

														$Contents .= "
															<tr height=27>
																<td class='search_box_title' ><label for='publlish_date'><b>등록일자</b></label><input type='checkbox' name='publlish_date' id='publlish_date' value='1' onclick='ChangeRegistDate(document.search_coupon);' ".(($publlish_date==1)?"checked":"")."></td>
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
    <td height='5'> 
		<table width='100%' cellpadding='3' cellspacing='0' border='0'>
					<colgroup>
					<col width='40%'>
					<col width='30%'>
					<col width='1%'>
					<col width='1%'>
					<col width='20%'>
					</colgroup><tbody><tr>
						<td>검색 결과 :&nbsp;<b>".$total."</b>&nbsp;건 / 전체 :&nbsp;<b>".$real_total."</b>&nbsp;건</td>
						<td align='left' height='30'>
						</td>
						<td align='right'>
						</td>
						<td align='right'>
						</td>
						<td align='right'>
							<select id='max' onchange='changeMax(this);'>
								<option value='10' ".($max == 10 ? "selected" : "").">10개씩 보기</option>
								<option value='50' ".($max == 50 ? "selected" : "").">50개씩 보기</option>
							</select>
						</td>
						
					</tr>
					</tbody>
		</table>
	</td>
  </tr>
  <tr>
    <td valign='top'>
	<form id='list_form' name='list_frm' method='POST' action='cupon.act.php' enctype='multipart/form-data'>
	<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
	<input type='hidden' name='act' value='change'>
	<input type='hidden' name='update_type' value=''>
	<input type='hidden' name='act_detail' value=''>
	  <table width='100%' border='0' cellpadding='0' cellspacing='0'>
        <tr>
          <td valign='top'>
            <table width='100%' border='0' cellpadding='10' cellspacing='1' class='list_table_box'>
			<col width='2%' >
			<col width='5%'>
			<col width='6%'>
			<col width='15%'>
			<col width='*' >
			<col width='5%'>
			<col width='10%'>
			<col width='10%'>
			<col width='5%'>
			<col width='5%'>
			<col width='4%'>
			<col width='5%'>
			<col width='8%'>
              <tr align=center height=30>
                <td class='s_td' nowrap><input type='checkbox' id='cupon_ix_all' onclick='checkIx();'></td>
                <td class='m_td'>등록일자</td>
                <td class='m_td'>쿠폰종류</td>
                <td class='m_td'>쿠폰명/쿠폰번호</td>
                <td class='m_td'>사용기간/발급기간</td>
                <td class='m_td'>사용범위</td>
				<td class='m_td'>발급방식/대상</td>
				<td class='m_td'>쿠폰혜택</td>
				<td class='m_td'>발급현황</td>
				<td class='m_td'>사용</br>여부</td>
				<td class='m_td'>노출</br>여부</td>
				<td class='m_td'>국내<br>해외</td>
				<td class='m_td'>관리</td>
              </tr>";

	if($db->total < 1){
		$Contents .= "<tr bgcolor=#ffffff height=50><td colspan=13 align=center> 등록된 쿠폰 정보가 없습니다. </td></tr>";
	}else{

	$sql = "select 
				c.*,
				(select count(*) from shop_cupon_regist cr where cr.publish_ix = c.publish_ix ) as regist_cnt
			from (
				select
					cp.*,
					c.cupon_kind, c.cupon_use_div, c.cupon_sale_type, c.cupon_acnt, c.cupon_sale_value
				from 
					".TBL_SHOP_CUPON." as c 
					join ".TBL_SHOP_CUPON_PUBLISH." as cp on (c.cupon_ix = cp.cupon_ix)
				$where
					order by cp.regdate desc 
					LIMIT $start, $max
			) c";

	$db->query($sql);

	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);

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
			$priod_str = $use_date_type."로부터</br> ".$date_differ." ".$date_type."간";

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
			$priod_str = $use_date_type."로부터</br> ".$date_differ." ".$date_type."간";
		}else if($db->dt[use_date_type] == 3){
			$use_date_type = '사용기간';
			$priod_str = "".substr($db->dt[use_sdate], 0, 10)." ~ ".substr($db->dt[use_edate], 0, 10)." ";
		}else if($db->dt[use_date_type] == 9){
			$use_date_type = '사용기간';
			$priod_str = '제한없음';
		}

		if($db->dt['disp'] == '0'){
            $priod_str .="<br>미발급";
		}else{
            $priod_str .="<br>".date('Y-m-d',$db->dt[cupon_use_sdate])." ~ ".date('Y-m-d',$db->dt[cupon_use_edate])."";
		}

		switch($db->dt[cupon_use_div]){
			case 'A' : $use_div_str = 'PC + Mobile';
				break;
			case 'G' : $use_div_str = 'PC 전용';
				break;
			case 'M' : $use_div_str = 'Mobile 전용';
				break;
			default : $use_div_str = '';
				break;
		}

		if($db->dt[cupon_sale_type] == 1){
			$sale_str = '정률할인(%)';
			$sale_unit = '%';
		}else if($db->dt[cupon_sale_type] == 2){
			if($db->dt['mall_ix'] == '20bd04dac38084b2bafdd6d78cd596b2'){
                $sale_str = '정액할인($)';
                $sale_unit = '$';
			}else{
                $sale_str = '정액할인(원)';
                $sale_unit = '원';
			}

		}else if($db->dt[cupon_sale_type] == 3){
            $sale_str = '전액할인';
		}

		if($db->dt[cupon_acnt] == 1){
			$sale_str .= '</br>본사</br>';
		}else if($db->dt[cupon_acnt] == 2){
			$sale_str .= '</br>본사 + 셀러</br>';
		}

		if($db->dt['cupon_sale_type'] != 3){
			$sale_str .= '('.number_format($db->dt[cupon_sale_value]).$sale_unit.')';
		}
		
		$cp_state = '1';
		if($db->dt[is_use] == '0' || $db->dt[is_use] == '3'){
			$cp_state_str = '발급중지';
			$cp_state = '0';
		}else if($db->dt[use_date_type] == 1 || $db->dt[use_date_type] == 3){
			if($db->dt[disp] != '0'){

                if($db->dt[cupon_use_sdate] > time()){
                    $cp_state_str = '발급대기';
                }else if($db->dt[cupon_use_edate] < time()){
                    $cp_state_str = '발급종료';
                    $cp_state = '0';
                }else{
                    $cp_state_str = '발급중';
                }
			}else{
                $cp_state_str = '발급중';
			}

		}else if($db->dt[use_date_type] == 9 || $db->dt[use_date_type] == 2){
			$cp_state_str = '발급중';
		}


		switch($db->dt[cupon_div]){
			case 'G':
				$coupon_div_name = "상품쿠폰";
				break;
            case 'C':
                $coupon_div_name = "장바구니쿠폰";
                break;
            case 'D':
                $coupon_div_name = "배송비쿠폰";
                break;
		}

		$Contents .= "
			<tr bgcolor='#ffffff'>
				<td class='list_box_td' nowrap><input type='checkbox' name='cupon_ix[]' value='".$db->dt[cupon_ix]."'></td>
				<td class='list_box_td' nowrap>
					<font class='gray16'>".$db->dt[regdate] ."</font><br>
				</td>
				<td class='list_box_td' nowrap>".$coupon_div_name."</td>
				<td class='list_box_td'  style='line-height:120%;padding:10px;' nowrap>
					<b><span style='display: inline-block;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;width: 150px;'>".$db->dt[publish_name] ."</span></b><br>(".$db->dt[cupon_no] .")</br><input type='button' value='쿠폰상세' style='margin: 10px;' onclick=\"javascript:PoPWindow3('coupon_detail.php?cupon_ix=".$db->dt[cupon_ix]."',900,800,'cupon_detail_pop')\">
				</td>
				<td class='list_box_td ' style='line-height:150%;padding:10px;text-align:center;'>".$priod_str."</td>
				<td class='list_box_td ' nowrap>".$use_div_str."</td>
				<td class='list_box_td ' nowrap>
					".($_ISSUE_TYPE[$db->dt[issue_type]]["text"])."</br>".($_PUBLISH_TYPE[$db->dt[publish_type]]["text"])."
				</td>
				<td class='list_box_td ' nowrap>".$sale_str."</td>
				<td class='list_box_td ' nowrap>".$cp_state_str."</br>(".($db->dt[regist_cnt] > 0 ? number_format($db->dt[regist_cnt]) : "-").")</br><input type='button' value='발급상세' style='margin: 10px;' onclick=\"javascript:PoPWindow3('cupon_register_user.php?publish_ix=".$db->dt[publish_ix]."&cp_state=".$cp_state."',900,800,'cupon_detail_pop')\"></td>
				<td class='list_box_td ' nowrap>".is_use_chk($db->dt[is_use])."</td>
				<td class='list_box_td ' nowrap>".($db->dt[disp] == "1" ? "노출":"미노출")."</td>
				<td class='list_box_td ' nowrap>".GetDisplayDivision($db->dt['mall_ix'], "text")."</td>
				<td class='list_box_td ' nowrap>
				<input type='button' value='쿠폰복사' style='margin: 3px;' onclick=\"publish_copy('".$db->dt[publish_ix]."');\"></br>
				<input type='button' value='수정' style='margin: 3px;' onclick=\"publish_modify('".$db->dt[publish_ix]."');\"><input type='button' value='삭제' style='margin: 3px;' onclick=\"publish_delete('".$db->dt[publish_ix]."');\"><br>
				<input type='button' value='전체수정' style='margin: 3px;' onclick=\"publish_modify_all('".$db->dt[publish_ix]."');\">
				</td>
			</tr>";

	}
}
$Contents .= "

              <!--- 목록 반복 끝 // ---------->

            </table>
			</form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td height='20' valign='top'></td>
  </tr>

</table>";

if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

$Contents .= "
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
	  <tr height='40'>
	    <td colspan=5 align=left>

	    </td>
	    <td  colspan='6' align='right' >&nbsp;".page_bar($total, $page, $max,$query_string,'')."&nbsp;</td>
	  </tr>
	</table>
	";

$help_text = "
	<div id='batch_update_coupon'>
				<table cellpadding=3 cellspacing=0 width=100% style='border:0px; margin-top: 10px;'>
					<tr height=30>
						<td class='input_box_item'>
							<input type='radio' name='detail' value='use0' id='use0' checked><label for='use0'>미사용으로 변경하기</label>
							<input type='radio' name='detail' value='disp0' id='disp0'><label for='disp0'>미노출로 변경하기</label>
							<input type='radio' name='detail' value='use1' id='use1'><label for='use1'>사용으로 변경하기</label>
							<input type='radio' name='detail' value='disp1' id='disp1'><label for='disp1'>노출로 변경하기</label>
							<input type='radio' name='detail' value='delete' id='delete'><label for='delete'>삭제하기</label>
						</td></tr>
				</table>
			<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
				<tr height=50>
					<td colspan=4 align=center>
						<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' onclick='submitToChange();'>
					</td>
				</tr>
			</table>
	</div>
	";

$select = "
<nobr>
<select id='update_type'>
	<option value='1'>검색한 쿠폰 전체에게</option>
	<option value='2' selected>선택한 쿠폰 전체에게</option>
</select>";

$Contents .= HelpBox($select, $help_text, 700);

$P = new LayOut();
$P->addScript = $Script;
//if($regdate!=1) $P->OnloadFunction = "onLoad('$sDate','$eDate', document.search_coupon);";//MenuHidden(false);
//else $P->OnloadFunction = "onLoad2('$sDate','$eDate', document.search_coupon);";//MenuHidden(false);
//$P->OnloadFunction = "init();";
$P->strLeftMenu = promotion_menu();
$P->Navigation = "프로모션(마케팅)/전시 > 쿠폰관리 > 쿠폰 리스트";
$P->title = "쿠폰 리스트";
$P->strContents = $Contents;
echo $P->PrintLayOut();

function is_use_chk($is_use){
	$str = "미사용";
	if($is_use == 0){
        $str = "미사용";
	}elseif($is_use == 1){
        $str = "사용";
	}elseif($is_use == 3){
        $str = "사용불가";
	}
	return $str;
}
?>
