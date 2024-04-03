<?
include("../class/layout.class");
include("./contract.lib.php");

$db = new Database();

$db->query("Select * from econtract_info where ei_ix ='$ei_ix'");

//$total = $db->total;
if($db->total){
	$db->fetch();
	$mall_ix = $db->dt[mall_ix];
	$ei_ix = $db->dt[ei_ix];

	$et_ix = $db->dt[et_ix];
	$contract_group = $db->dt[contract_group];
	$contract_title = $db->dt[contract_title];
	$contract_type = $db->dt[contract_type];
	$contract_detail = $db->dt[contract_detail];
	 
	$contract_sdate = $db->dt[contract_sdate];
	$contract_edate = $db->dt[contract_edate];
	$contract_date = $db->dt[contract_date]; 
	$company_id = $db->dt[company_id]; 
	$contractor_id = $db->dt[contractor_id]; 

	$com_ceo = $db->dt["com_ceo"];
	$com_zip = explode("-",$db->dt[com_zip]);
	$com_reg_no = explode("-",$db->dt[com_reg_no]);
	$com_addr1 = $db->dt["com_addr1"];
	$com_addr2 = $db->dt["com_addr2"];

	$contractor_ceo = $db->dt["contractor_ceo"];

	$contractor_zip = explode("-",$db->dt[contractor_zip]);
	$contractor_reg_no = explode("-",$db->dt[contractor_reg_no]);
	$contractor_addr1 = $db->dt["contractor_addr1"];
	$contractor_addr2 = $db->dt["contractor_addr2"];
	 
	$is_multiple = 0;//$db->dt[is_multiple]; 
	$is_use = $db->dt[is_use]; 
	$sign_type = $db->dt[sign_type]; 
	$priod_type = $db->dt[priod_type]; 
	$extension_year = $db->dt[extension_year]; 
	$use_relation_file = $db->dt[use_relation_file]; 
	
	
	$contract_priod_type = $db->dt[contract_priod_type]; 
	
	$charger_ix = $db->dt[charger_ix]; 
	
	
 
	$act = "updateContract";
}else{
	$ei_ix = "";
	$econtract_title = "";
	$econtract_code = "";
	$contract_sdate = date("Y-m-d");
	$contract_edate = date("Y-m-d",strtotime("1 year"));
	$contract_date = date("Y-m-d");
	$company_id = $_SESSION["admininfo"]["company_id"];
	$charger_ix = $_SESSION["admininfo"]["charger_ix"];
	$contractor_id = "";
	$use_relation_file = 0;

	$is_multiple = 0;
	$is_use = 1; 
	$disp = 1; 
 
	$act = "regContract";

	$db->query("Select * from common_company_detail where company_id ='".$_SESSION["admininfo"]["company_id"]."'");
	$db->fetch();
	$headoffice_info = $db->dt;

	$com_ceo = $headoffice_info["com_ceo"];
	$com_zip = explode("-",$headoffice_info[com_zip]);
	$com_reg_no = explode("-",$headoffice_info[com_number]);
	$com_addr1 = $headoffice_info["com_addr1"];
	$com_addr2 = $headoffice_info["com_addr2"];

	/*
	$db->query("Select * from common_company_detail where company_id ='226fa2801d69d8138e088ca0c6d66616'");
	$db->fetch();
	$contractor_id = $db->dt[company_id];
	$contractor_ceo = $db->dt[com_ceo];
	$contractor_zip = explode("-",$db->dt[com_zip]);
	$contractor_addr1 = $db->dt[com_addr1];
	$contractor_addr2 = $db->dt[com_addr2];
	$contractor_reg_no = explode("-",$db->dt[com_number]);
	*/
}

$Contents = "
<form name='form_cupon' onsubmit='return CheckFormValue(this)' method='post' enctype='multipart/form-data'  action='contract.act.php'>
<input type=hidden name='act' value='".$act."'>
<input type=hidden name='ei_ix' value='".$ei_ix."'>
<table width='100%' border='0' cellpadding='0' cellspacing='0'>
  <!-- // 전자계약서 작성 -->
  <tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("전자계약서 작성", "전자계약 관리 > 전자계약서 작성 ")."</td>
  </tr> 
  <tr>
	<td align='left' colspan=6 style='padding:0px 0px 3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><b><img src='../image/title_head.gif' align=absmiddle> 계약서 주최(본사/갑)</b></div>")."</td>
  </tr>
  <tr>
    <td valign='top'>
	
      <table width='100%' border='0' cellpadding='0' cellspacing='0' class='input_table_box'>
		<col width='15%'>
		<col width='35%'>
		<col width='15%'>
		<col width='35%'> 
		<tr>
			<td class='input_box_title'> 계약자(갑) </td>
			<td class='input_box_item' >
			".companyAuthList($company_id , "validation=true title='계약자(갑)' ","company_id", "company_id","com_name", "company_info")."
			</td>
			<td class='input_box_title'> 대표자</td>
			<td class='input_box_item' >
			 <span id='com_ceo_text'>".($com_ceo ? $com_ceo:"")."</span>
			 <input type=hidden name='com_ceo' value='".$com_ceo."'>
			</td>
		</tr>		
		<tr height='90px;'>
			<td class='input_box_title'> <b>주소 <img src='".$required3_path."'>  </b></td>
			<td class='input_box_item' colspan=3>
			<div id='input_address_area' >
				<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
				<col width='120px'>
				<col width='*'>				
				<tr>
					<td height=26>
						<input type='text' class='textbox ' name='com_zip1' id='zipcode1' style='width:80px;' maxlength='10' value='".$headoffice_info[com_zip]."' readonly> 
					</td>
					<td style='padding:1px 0 0 5px;'>
						<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('9','input_address_area');\"  style='cursor:pointer;'>

					</td>
				</tr>
				<tr>
					<td colspan=2 height=26>
						<input type=text name='com_addr1'  id='addr1' value='".$com_addr1."' size=50 class='textbox point_color'  style='width:450px'>
					</td>
				</tr>
				<tr>
					<td colspan=2 height=26>
						<input type=text name='com_addr2'  id='addr2'  value='".$com_addr2."' size=70 class='textbox point_color'  style='width:450px'> (상세주소)
					</td>
				</tr>
				</table>
			</div>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'><b>사업자등록번호 <img src='".$required3_path."'>  </b></td>
			<td class='input_box_item' colspan=3>
				<input type='radio' name='use_com_reg_no' id='use_com_reg_no_1' value='1' checked><label for='use_com_reg_no_1'>사용</label>
				<div id='company_id_area' style='display:inline;'>
					<input type=text name='com_reg_no1' id='com_reg_no_1' value='".$com_reg_no[0]."' maxlength=3 class='textbox numeric point_color' style='width:35px;' com_numeric=true validation='true' title='사업자등록번호'> -
					<input type=text name='com_reg_no2' id='com_reg_no_2' value='".$com_reg_no[1]."' maxlength=2 class='textbox numeric point_color' style='width:35px;' com_numeric=true validation='true' title='사업자등록번호'> -
					<input type=text name='com_reg_no3' id='com_reg_no_3' value='".$com_reg_no[2]."' maxlength=5 class='textbox numeric point_color' style='width:35px;' com_numeric=true validation='true' title='사업자등록번호'>
					<div style='display:inline;padding:2px;' class=small>예) XXX-XX-XXXXX</div>
				</div>
				<input type='radio' name='use_com_reg_no' id='use_com_reg_no_0' value='0'><label for='use_com_reg_no_0'>미사용</label>
			</td> 
		</tr> 
      </table>
    </td>
  </tr>
  <tr>
	<td align='left' colspan=6 style='padding:20px 0px 3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><b><img src='../image/title_head.gif' align=absmiddle> 계약서 고객용(협력사/을)</b></div>")."</td>
  </tr>
  <tr>
    <td valign='top'>
	
      <table width='100%' border='0' cellpadding='0' cellspacing='0' class='input_table_box'>
		<col width='15%'>
		<col width='35%'>
		<col width='15%'>
		<col width='35%'>";
if($ei_ix == ""){
$Contents .= "	
		<tr >
		  <td class='input_box_title' >  <b>다중 계약자사용</b></td>
		  <td class='input_box_item' colspan=3>
				<input type='radio' name='is_multiple' id='is_multiple_1'  align='middle' value='1' ".($is_multiple == '1' || $is_multiple == '' ? "checked":"")." onclick=\"$('.input_single_contractor').hide();$('.input_multi_contractor').show();$('input[id^=contractor_reg_no').attr('validation','false');\"><label for='is_multiple_1' class='green'>사용</label> 
				<input type='radio' name='is_multiple' id='is_multiple_0'  align='middle' value='0' ".($is_multiple == '0' ? "checked":"")." onclick=\"$('.input_single_contractor').show();$('.input_multi_contractor').hide();$('input[id^=contractor_reg_no').attr('validation','true');\"><label for='is_multiple_0' class='green'>미사용</label> 
		  </td>
		</tr>";
}
$Contents .= "
		<tr class='input_single_contractor' ".($is_multiple == '0' ? "":"style='display:none'")." >
			<td class='input_box_title'> 계약자(을) </td>
			<td class='input_box_item' >
			".companyAuthList($contractor_id , "validation=true title='계약자(을)' ","contractor_id", "contractor_id","contractor_name", "contractor_info")."
			</td>
			<td class='input_box_title'> 대표자</td>
			<td class='input_box_item' >
			 <span id='contractor_ceo_text'>".($contractor_ceo ? $contractor_ceo:"")."</span>
			 <input type=hidden name='contractor_ceo' value='".$contractor_ceo."'>
			</td>
		</tr>		
		<tr height='90px;' class='input_single_contractor'  ".($is_multiple == '0' ? "":"style='display:none'").">
			<td class='input_box_title'> <b>주소 <img src='".$required3_path."'>  </b></td>
			<td class='input_box_item' colspan=3>
			<div id='contractor_address_area' >
				<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
				<col width='120px'>
				<col width='*'>				
				<tr>
					<td height=26>
						<input type='text' class='textbox ' name='contractor_zip1' id='zipcode1' style='width:80px;' maxlength='10' value='".$db->dt[contractor_zip]."' readonly> 
					</td>
					<td style='padding:1px 0 0 5px;'>
						<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('9','contractor_address_area');\" style='cursor:pointer;'>
					</td>
				</tr>
				<tr>
					<td colspan=2 height=26>
						<input type=text name='contractor_addr1'  id='addr1' value='".$contractor_addr1."' size=50 class='textbox point_color'  style='width:450px'>
					</td>
				</tr>
				<tr>
					<td colspan=2 height=26>
						<input type=text name='contractor_addr2'  id='addr2'  value='".$contractor_addr2."' size=70 class='textbox point_color'  style='width:450px'> (상세주소)
					</td>
				</tr>
				</table>
			</div>
			</td>
		</tr>
		<tr height='90px;' class='input_multi_contractor'  ".($is_multiple == '1' ? "":"style='display:none'").">
			<td class='input_box_title'> <b>협력사 선택 <img src='".$required3_path."'>  </b></td>
			<td class='input_box_item' colspan=3>
			<div class='goods_auto_area'  id='goods_display_sub_area_S' style='padding:10px 5px 10px 5px;".(($use_product_type == "5") ? "display:block;":"display:block;")."'>
						<table   border='0'  cellpadding=0 cellspacing=0 >								
							<tr>
								<td width='300'>
									<table  border='0' cellpadding=0 cellspacing=0 align='center'>
										<tr align='left'>
											<td width='100'>
												<input type=text class=textbox name='search_text'  id='search_text' style='width:150px;margin-bottom:2px;' value='' onkeyup=\"SearchInfo('S',$('DIV#goods_display_sub_area_S'), 'seller');\"> 
												<!--onclick=ShowModalWindow('./charger_search.php?company_id=3444fde7c7d641abc19d5a26f35a12cc&target=4&amp;code=',600,530,'charger_search')-->
											</td>
											<td align='center'>
												<img src='../images/btn_select_seller.gif' onclick=\"SearchInfo('S',$('DIV#goods_display_sub_area_S'), 'seller');\"  style='cursor:pointer;'> 
												<img src='../images/icon/pop_all.gif' alt='전체선택' title='전체선택' onclick=\"SelectedAll($('DIV#goods_display_sub_area_S #search_result_seller option'),'selected')\" style='cursor:pointer;'/>
											</td>
										</tr>
										<tr>
											<td colspan='2' >
												<!--div style=' width:320px;height:148px;font-size:12px;background:#fff;border:1px solid silver;' id='search_result'>
												</div-->
												<select name='search_result[seller]' style=' width:320px;height:148px;font-size:12px;background:#fff;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' class='search_result' id='search_result_seller'  multiple>											
												</select>
											</td>
										</tr>
									</table>
								</td>
								<td align='center' width=80>
									<div class='float01 email_btns01'>
										<ul>
											<li>
												<a href=\"javascript:MoveSelectBox($('DIV#goods_display_sub_area_S'), 'S','ADD','seller');\"><img src='../images/icon/pop_plus_btn.gif' alt='추가' title='추가' /></a>
											</li>
											<li>
												<a href=\"javascript:MoveSelectBox($('DIV#goods_display_sub_area_S'),'S','REMOVE','seller');\"><img src='../images/icon/pop_del_btn.gif' alt='삭제' title='삭제' /></a>
											</li>
										</ul>
									</div>
								</td>
								<td width='300' style='vertical-align:bottom;'>
									<table width='100%' border='0' align='center'>
										<tr>
											<td colspan='2' >
												<!--div style=' width:320px;height:148px;font-size:12px;background:#fff;border:1px solid silver;' id='selected_result'>
												</div-->
												<select name=\"seller[]\" style='width:300px;height:148px;font-size:12px;background:#fff;padding:5px;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' id='selected_result_seller' validation=false title='셀러' multiple>
												";
												//$vip_array = get_vip_member('4');
												if($use_product_type == 5){
													$sql = "SELECT ccd.company_id, ccd.com_name 
																FROM common_company_detail ccd, shop_cupon_relation_seller crs 
																where ccd.company_id = crs.company_id and  crs.publish_ix = '".$publish_ix."'  ";
													$db->query($sql);
													$selected_sellers = $db->fetchall();
													

													for($j = 0; $j < count($selected_sellers); $j++){
														$Contents .="<option value='".$selected_sellers[$j][company_id]."' ondblclick=\"$(this).remove();\" selected>".$selected_sellers[$j][com_name]."</option>";
													}
												}
												$Contents .="
												</select>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<!--a href=\"javascript:ShowModalWindow('../code_search.php?search_type=brand&group_code=".($i+1)."',600,600,'code_search')\" style='cursor:pointer;'><img src='/admin/images/btn_select_seller.gif' alt='셀러선택' title='셀러선택' align='absmiddle' /></a--> 
					</div>
			</td>
		</tr>
		<tr class='input_single_contractor' ".($is_multiple == '0' ? "":"style='display:none'")." >
			<td class='input_box_title'><b>사업자등록번호 <img src='".$required3_path."'>  </b></td>
			<td class='input_box_item' colspan=3>
				<input type='radio' name='use_contractor_reg_no' id='use_contractor_reg_no_1' value='1' onclick=\"$('input[id^=contractor_reg_no').attr('validation','true');\" checked><label for='use_contractor_reg_no_1'>사용</label>
				<div id='company_id_area' style='display:inline;'>
					<input type=text name='contractor_reg_no1' id='contractor_reg_no_1' value='".$contractor_reg_no[0]."' maxlength=3 class='textbox numeric point_color' style='width:35px;' com_numeric=true validation='true' title='사업자등록번호'> -
					<input type=text name='contractor_reg_no2' id='contractor_reg_no_2' value='".$contractor_reg_no[1]."' maxlength=2 class='textbox numeric point_color' style='width:35px;' com_numeric=true validation='true' title='사업자등록번호'> -
					<input type=text name='contractor_reg_no3' id='contractor_reg_no_3' value='".$contractor_reg_no[2]."' maxlength=5 class='textbox numeric point_color' style='width:35px;' com_numeric=true validation='true' title='사업자등록번호'>
					<div style='display:inline;padding:2px;' class=small>예) XXX-XX-XXXXX</div>
				</div>
				<input type='radio' name='use_contractor_reg_no' id='use_contractor_reg_no_0' value='0' onclick=\"$('input[id^=contractor_reg_no').attr('validation','false');\"><label for='use_contractor_reg_no_0'>미사용</label>
			</td> 
		</tr> 
      </table>
    </td>
  </tr>
   <tr>
	<td align='left' colspan=6 style='padding:20px 0px 3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><b><img src='../image/title_head.gif' align=absmiddle> 계약서 작성</b></div>")."</td>
  </tr>
  <tr>
    <td valign='top'>
	
      <table width='100%' border='0' cellpadding='0' cellspacing='0' class='input_table_box'>
		<col width='15%'>
		<col width='35%'>
		<col width='15%'>
		<col width='35%'> 
		<tr >
		  <td class='input_box_title' >  <b>계약서 작성여부</b></td>
		  <td class='input_box_item' colspan=3>
				<input type='radio' name='use_contract_type' id='use_contract_type_2'  align='middle' value='2' ".($use_contract_type == '2' ? "checked":"")." onclick='clearContract()'><label for='use_contract_type_2' class='green'>신규계약서 작성 
				</label> 
				<input type='radio' name='use_contract_type' id='use_contract_type_1'  align='middle' value='1' ".($use_contract_type == '1' || $use_contract_type == '' ? "checked":"")." onclick=\"$('#contract_group').show();	$('#et_ix').show();\"><label for='use_contract_type_1' class='green'>자주쓰는 계약서 사용</label> 
				".getContractGroup($contract_group, "onchange=\"loadContract($(this), 'et_ix')\"")."
				".getContract($contract_group, $et_ix," onchange='changeContract($(this))' ")."
				
		  </td>
		</tr>  
		<tr >
		  
		    
          <td class='input_box_title'  > 계약서명</td>
          <td class='input_box_item' style='padding-left:10px;'>
		  <input type='text' validation='true' title='계약서명' id='contract_title' name='contract_title' class='textbox' maxlength='50' style='height: 20px; width: 300px; filter: blendTrans(duration=0.5)' align='absmiddle' value='".$contract_title."'></td>
		  <td class='input_box_title' >  <b>계약서 종류</b></td>
		  <td class='input_box_item'>
				<input type='radio' name='contract_type' id='contract_type_1'  align='middle' value='1' ".($contract_type == '1' ? "checked":"")."><label for='contract_type_1' class='green'>일반계약서</label> 
				<input type='radio' name='contract_type' id='contract_type_2'  align='middle' value='2' ".($contract_type == '2' ? "checked":"")."><label for='contract_type_2' class='green'>첨부서류</label> 
		  </td>
        </tr>		   
		<tr >
		    <td class='input_box_title' ><b>계약일</b> <img src='".$required3_path."'></td>
			<td class='input_box_item' >
				<input type='text' class='textbox point_color' name='contract_date' class='textbox' value='".$contract_date."' style='height:20px;width:100px;text-align:center;' id='contract_date' validation='true' title='계약일'>
			</td>
			<td class='input_box_title'>담당자</td>
			<td class='input_box_item'>
			".CompayChargerSearch($_SESSION["admininfo"]["company_id"] ,$charger_ix,"","selectbox")."
			</td>
		</tr>
        <tr >
		  <td class='input_box_title' >  <b>계약기간</b></td>
		  <td class='input_box_item' colspan=3 style='padding:10px;'>
		  <div style='display:block;width:100%;'>".search_date('contract_sdate','contract_edate',$contract_sdate,$contract_edate,'N','')."</div> 
		  <div style='display:block;width:100%;'>
				<input type='radio' name='priod_type' id='priod_type_1'  align='middle' value='1' ".($priod_type == '1' || $priod_type == '' ? "checked":"")."><label for='priod_type_1' class='green'>1회성</label> 
				<input type='radio' name='priod_type' id='priod_type_2'  align='middle' value='2' ".($priod_type == '2' ? "checked":"")."><label for='priod_type_2' class='green'>계약만료일로부터 
				<select name='extension_year' id='extension_year'>
					<option value='1' ".($extension_year == "1" ? "selected":"").">1년</option>
					<option value='2' ".($extension_year == "2" ? "selected":"").">2년</option>
				</select>
				자동연장</label> 
		  </div>
		  </td>
		</tr>  
		<tr style='display:none;'>
		  <td class='input_box_title' >  <b>첨부서류</b></td>
		  <td class='input_box_item' colspan=3 id='relation_file_zone' style='padding:10px;'>
				<div style='padding-bottom:5px;'>
				<input type='radio' name='use_relation_file' id='use_relation_file_1'  align='middle' value='1' ".($use_relation_file == '1' ? "checked":"")." onclick=\"$('.relation_file_box').show();\"><label for='use_relation_file_1' class='green'>사용함</label> 
				<input type='radio' name='use_relation_file' id='use_relation_file_0'  align='middle' value='0' ".($use_relation_file == '0' ? "checked":"")." onclick=\"$('.relation_file_box').hide();\"><label for='use_relation_file_0' class='green'>미사용</label> 
				</div>
		  ";
$sql = "select * from econtract_info_file where ei_ix = '".$ei_ix."' ";
$db->query($sql);

for($i=0;($i < $db->total || $i == 0);$i++){
	$db->fetch($i);
$Contents .= "
				 <div id='relation_file_box' class='relation_file_box' style='padding:3px 0px;".($use_relation_file == 0 ? "display:none;":"")."'>
				 <input type=text class='textbox'  name='relation_files[]' id='relation_file' style='vertical-align:middle;' value='".$db->dt[file_text]."'> 
				 <img src='../images/korea/btn_add.gif' border='0' align=absmiddle onclick=\"AddFiles();\"> 
				 <img src='../images/korea/btc_del.gif' border='0' align=absmiddle id='remove_btn' ".($i == 0 ? "style='display:none;'":"")." onclick=\"$(this).closest('div').remove();\"> 
				 </div>";
}
$Contents .= "
		  </td>
		</tr> 
		<tr >
		  <td class='input_box_title' >  <b>전자서명</b></td>
		  <td class='input_box_item' colspan=3 style='padding:10px;'>
		  <div style='display:block;width:100%;'>
				<input type='radio' name='sign_type' id='sign_type_1'  align='middle' value='1' ".($sign_type == '1' || $sign_type == '' ? "checked":"")."><label for='sign_type_1' class='green'>요청시 자동서명</label> 
				<input type='radio' name='sign_type' id='sign_type_2'  align='middle' value='2' ".($sign_type == '2' ? "checked":"")."><label for='sign_type_2' class='green'>클라이언트(협력사/발주처)등 서명후 별도 서명</label> 
		  </div>
		  </td>
		</tr> 
      </table>
    </td>
  </tr>
  <tr>
	<td align='left' colspan=6 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><b><img src='../image/title_head.gif' align=absmiddle> 전자계약서 작성</b></div>")."</td>
  </tr>
  <tr>
    <td height='20'><textarea name='contract_detail' id='contract_detail'>".$contract_detail."</textarea></td>
  </tr>
  <tr>
    <td align='center' style='padding-top:20px;'>";
if($_GET["cupon_ix"] == ""){
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
		$Contents .= "<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0>";
	}else{
		$Contents .= "<a href=\"".$auth_write_msg."\"><img  src='../images/".$admininfo["language"]."/b_save.gif' border=0></a>";
	}
}else{
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
		$Contents .= "<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0>";
	}else{
		$Contents .= "<a href=\"".$auth_update_msg."\"><img  src='../images/".$admininfo["language"]."/b_save.gif' border=0></a>";
	}
}
$Contents .= "
	</td>
  </tr>
</table>
</form>";

 $Script = "
 <script language='javascript' src='../search.js'></script>
<script language='JavaScript' src='/include/ckeditor/ckeditor.js'></script>
 <script language='javascript'>

 $(document).ready(function() {

CKEDITOR.replace('contract_detail',{
		docType : '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">',
		font_defaultLabel : '굴림',
		font_names : '굴림/Gulim;돋움/Dotum;바탕/Batang;궁서/Gungsuh;Arial/Arial;Tahoma/Tahoma;Verdana/Verdana',
		fontSize_defaultLabel : '12px',
		fontSize_sizes : '8/8px;9/9px;10/10px;11/11px;12/12px;14/14px;16/16px;18/18px;20/20px;22/22px;24/24px;26/26px;28/28px;36/36px;48/48px;',
		language :'ko',
		resize_enabled : false,
		enterMode : CKEDITOR.ENTER_BR,
		shiftEnterMode : CKEDITOR.ENTER_P,
		startupFocus : false,
		uiColor : '#EEEEEE',
		toolbarCanCollapse : false,
		menu_subMenuDelay : 0,
		toolbar : [['Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','TextColor','BGColor','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Link','Unlink','-','Find','Replace','SelectAll','RemoveFormat','-','Image','Flash','Table','SpecialChar'],'/',['Source','-','ShowBlocks','-','Font','FontSize','Undo','Redo','-','About'],['Maximize']],
		filebrowserImageUploadUrl : '/include/ckeditor/upload.php',
		height:300});

});
 
$(function() {
	$(\"#contract_date\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
 
	}

	});
 
});

function loadContract(obj,target) {
	
	var contract_group = obj.find('option:selected').val();
	var form = obj.closest('form').attr('name'); 
	//alert(contract_group);
	$.ajax({ 
		type: 'GET', 
		data: {'act':'getContractList','return_type': 'json',  'contract_group':contract_group},
		url: './contract.act.php',  
		dataType: 'json', 
		async: true, 
		beforeSend: function(){  
		},  
		error: function(request,status,error){ 
			alert('code:'+request.status+':: message:'+request.responseText+':: error:'+error);
		},  
		success: function(datas){
			$('select#'+target).find('option').not(':first').remove();
			if(datas != null){
				$.each(datas, function(i, data){ 
						$('select[name='+target+']').append(\"<option value='\"+data.et_ix+\"'>\"+data.contract_title+\"</option>\");
				});  
			}
		} 
	});  
}

function clearContract(){
	$('#contract_title').val('');

	$('input[name=contract_type]:eq(0)').attr('checked','checked');		
	$('input[name=priod_type]:eq(0)').attr('checked','checked');	
	
	$('select#extension_year option:eq(0)').attr('selected','selected');
	

	$('textarea#contract_detail').val('');
	CKEDITOR.instances['contract_detail'].setData('');

	$('#contract_group').hide();
	$('#et_ix').hide();
}


function changeContract(obj) {
	var et_ix = obj.find('option:selected').val();
	var company_id = $('#company_id').val();
	var contractor_id = $('#contractor_id').val();
	var is_multiple = $('input[name=is_multiple]:checked').val();
	
	if($('input[name=is_multiple]:checked').val() == 0){
		if(company_id == '' || contractor_id == ''){
			alert('계약서는 업체정보를 입력후 선택해주세요. 입력하신 업체의 정보가 계약서에 자동으로 추가됩니다.');
			return false;
		}
	}else{
		//alert($('select#selected_result_seller option').length);
		if($('select#selected_result_seller option').length == 0){
			obj.find('option:first').attr('selected','selected');
			alert('계약서는 업체정보를 입력후 선택해주세요. 입력하신 업체의 정보가 계약서에 자동으로 추가됩니다.');
			return false;
		}
	}
	$.ajax({ 
		type: 'POST', 
		data: {'act':'getContract','return_type': 'json', 'et_ix':et_ix,'is_multiple':is_multiple, 'contractor_id':contractor_id, 'company_id':company_id},
		url: './contract.act.php',  
		dataType: 'json', 
		async: true, 
		beforeSend: function(){  
		},  
		error: function(){ 
			alert('error');
		},  
		success: function(contract){
			//alert(contract.contract_detail);
			if(contract != null){
				 
				$('#contract_title').val(contract.contract_title);
				 
				$('input[name=contract_type][value='+contract.contract_type+']').attr('checked','checked');		
				$('input[name=priod_type][value='+contract.priod_type+']').attr('checked','checked');	
				$('input[name=use_relation_file][value='+contract.use_relation_file+']').attr('checked','checked');	
				 
				if(contract.use_relation_file == 1){
					$('#relation_file_box').show(); 
						$.each(contract.relation_files, function(i, relation_file){ 
							if(i == 0){
								var newRow = $('#relation_file_box:first');  
								newRow.find('img[id=remove_btn]').hide();
							}else{
								var newRow = $('#relation_file_box:last').clone(true).appendTo($('#relation_file_zone'));  
								newRow.find('img[id=remove_btn]').show();
							}
							newRow.find('input[id=relation_file]').val(relation_file.file_text);						
						});  
				}
				if(contract.priod_type == 2){
					$('select#extension_year').val(contract.extension_year);
				}

				//alert(contract.contract_detail);
				$('textarea#contract_detail').val(contract.contract_detail);
				CKEDITOR.instances['contract_detail'].setData(contract.contract_detail);

			}
		} 
	});  

}

function zipcode(type, input_area_id) {
	var zip = window.open('../member/zipcode.php?zip_type='+type+'&obj_id='+input_area_id,'','width=440,height=300,scrollbars=yes,status=no');
}

function AddFiles(){
	
	var newRow = $('#relation_file_box:last').clone(true).appendTo($('#relation_file_zone'));  

	newRow.find('input[id=relation_file]').val('');
	newRow.find('img[id=remove_btn]').show();
}

</script>
 ";


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = econtract_menu();
$P->strContents = $Contents;
$P->Navigation = "전자계약 > 전자계약 관리 > 전자계약서 작성";
$P->title = "전자계약서 작성";
echo $P->PrintLayOut();

/*
CREATE TABLE IF NOT EXISTS `econtract_info` (
  `ei_ix` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `company_id` varchar(32) DEFAULT NULL COMMENT '계약자(갑) 회사코드',
  `com_zip` varchar(7) DEFAULT NULL COMMENT '우편번호',
  `com_addr1` varchar(255) DEFAULT NULL COMMENT '주소',
  `com_addr2` varchar(255) DEFAULT NULL COMMENT '나머지주소',
  `com_reg_no` varchar(20) DEFAULT NULL COMMENT '사업자등록번호',
  `use_com_reg_no` int(1) default '1' COMMENT '1:사용, 2:미사용',
  
  `contractor_id` varchar(32) DEFAULT NULL COMMENT '계약자(을) 회사코드',
  `contractor_zip` varchar(7) DEFAULT NULL COMMENT '우편번호',
  `contractor_addr1` varchar(255) DEFAULT NULL COMMENT '주소',
  `contractor_addr2` varchar(255) DEFAULT NULL COMMENT '나머지주소',
  `contractor_reg_no` varchar(20) DEFAULT NULL COMMENT '계약자 사업자등록번호',
  `use_contractor_reg_no` int(1) default '1' COMMENT '1:사용, 2:미사용',

  `et_ix` int(10) unsigned NOT NULL COMMENT '계약서 코드',  
  `contract_type` int(1) default '1' COMMENT '1:일반계약서, 2:첨부서류',
  `contract_title` varchar(255) DEFAULT NULL COMMENT '계약서명',
  `contract_group` int(5) DEFAULT NULL COMMENT '계약서 분류',
  `contract_date` varchar(10) DEFAULT NULL COMMENT '계약서일자',
  `contract_sdate` varchar(10) DEFAULT NULL COMMENT '계약서 시작일자',
  `contract_edate` varchar(10) DEFAULT NULL COMMENT '계약서 종료일자',
  `contract_detail` mediumtext DEFAULT NULL COMMENT '계약서 내용',  
  `priod_type` int(1) DEFAULT NULL COMMENT '계약기간 타입 1:1회성, 자동연장 ',
  `extension_year` int(2) DEFAULT NULL COMMENT '연장기간',
  `sign_type` int(1) DEFAULT NULL COMMENT '전자서명 타입 1: 요청시 자동서명, 2: 클라이언트(협력사/발주처)등 서명후 별도 서명',
  `charger_ix` varchar(32) DEFAULT NULL COMMENT '담당자',
  `is_use` enum(1,0) DEFAULT 1 COMMENT '사용여부',
  `editdate` datetime DEFAULT NULL COMMENT '수정일',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`ei_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='계약서 정보'  AUTO_INCREMENT='100000';

*/

?>