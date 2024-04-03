<?
include("../class/layout.class");
include("buying.lib.php");

$db = new Database;

if($sc_ix){
	if($view_type == "detail"){
		$sql = "select 
			sc.*,
			cu.id,AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
			from buyingservice_shopping_center sc 
			left join common_user cu on (sc.sc_mg_charger_ix = cu.code)
			left join common_member_detail cmd on (cu.code = cmd.code)
			where sc.sc_ix='$sc_ix' ";
	}else{
		$sql = "select 
			sc.*,
			ca.ca_country,
			cu.id,AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
			ccd.com_ceo,ccd.com_number,ccd.com_phone,ccd.com_fax,ccd.com_zip,ccd.com_addr1,ccd.com_addr2,
			ccd.customer_name,ccd.customer_position,ccd.customer_phone,ccd.customer_mobile,customer_mail
			from buyingservice_shopping_center sc 
			left join common_user cu on (sc.sc_charger_ix = cu.code)
			left join common_member_detail cmd on (cu.code = cmd.code)
			left join common_company_detail ccd on (cu.company_id = ccd.company_id)
			left join buyingservice_commercial_area ca on(ca.ca_ix=sc.ca_ix) 
			where sc.sc_ix='$sc_ix' ";
	}
	$db->query($sql);
	$db->fetch();
	$sc=$db->dt;
	$_act="update";
}else{
	$_act="insert";
}

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <col width='20%'>
	  <col width='*'>
	  <tr >
		<td align='left' colspan=6> ".GetTitleNavigation("상가관리", "상가등록 ")."</td>
	  </tr>
	  	<tr>
	    <td align='left' colspan=8 style='padding-bottom:14px;'>
			<div class='tab'>
				<table class='s_org_tab'>
				<tr>
					<td class='tab'>
						<table id='tab_00'  ".($view_type == "" ? "class='on'":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='./shopping_center.add.php?sc_ix=$sc_ix'\">상가 기본정보</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_01'  ".($view_type == "detail" ? "class='on'":"").">
						<tr>
							<th class='box_01'></th>";
							if($_act=="insert"){
								$Contents01 .= "
								<td class='box_02' onclick=\"alert('기본정보 등록후에 상세정보를 입력하실수 있습니다.');\">상가 상세정보</td>";
							}else{
								$Contents01 .= "
								<td class='box_02' onclick=\"document.location.href='./shopping_center.add.php?sc_ix=$sc_ix&view_type=detail'\">상가 상세정보</td>";
							}
							$Contents01 .= "
							<th class='box_03'></th>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</div>
	    </td>
	</tr>
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk><u>$board_name</u> 상가 등록/수정</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <col width='15%'>
	  <col width='35%'>
	  <col width='15%'>
	  <col width='35%'>";
	if($view_type == "detail"){
		$Contents01 .= "
	   <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 상가 운영팀 <img src='".$required3_path."'></td>
	    <td class='input_box_item' colspan='3'>
	    	<table cellpadding=0 cellspacing=0>
				<tr>
					<td><input type=hidden name='sc_mg_charger_ix' id='mem_ix' value='".$sc[sc_mg_charger_ix]."' validation=true title='상가 운영팀' style='width:100px;'></td>
					<td><input type=text class='ca_change_code textbox' id='buying_mem_name' value='".$sc[name]."' style='width:100px;' onclick=\"PoPWindow('./member_search.php?mode=getcominfo',600,380,'member_search')\"  readonly></td>
					<td style='padding-left:5px;'><img src='../v3/images/".$admininfo["language"]."/btn_member_search.gif' align=absmiddle onclick=\"PoPWindow('./member_search.php?mode=getcominfo',600,380,'member_search')\" class='ca_change_code'  style='cursor:pointer;'></td>
				</tr>
			</table>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 상가 운영시간 <img src='".$required3_path."'></td>
	    <td class='input_box_item' colspan='3'>
			<table cellpadding=0 cellspacing=0>
				<tr height='30'>
					<td>
						<input type=radio name='sc_mg_time_type' id='sc_mg_time_type_a' value='A' ".($sc[sc_mg_time_type]=="A" || $sc[sc_mg_time_type]=="" ? "checked" :"")."><label for='sc_mg_time_type_a'>전층 동일</label>
	    				<input type=radio name='sc_mg_time_type' id='sc_mg_time_type_e' value='E' ".($sc[sc_mg_time_type]=="E" ? "checked" :"")."><label for='sc_mg_time_type_e'>폐점시간</label>
					</td>
				</tr>
				<tr id='sc_mg_time_tr' height='30' ".($sc[sc_mg_time_type]=="E" ? "style='display:none;'" : "" ).">
					<td>
						개점시간 <input type='text' name='sc_start_mg_time' class='textbox' value='".$sc[sc_start_mg_time]."' size='15' validation='".($sc[sc_mg_time_type]=="E" ? "false" : "true" )."' title='개점시간'> &nbsp;&nbsp; 
						폐점시간 <input type='text' name='sc_end_mg_time' class='textbox' value='".$sc[sc_end_mg_time]."' size='15' validation='".($sc[sc_mg_time_type]=="E" ? "false" : "true" )."' title='폐점시간'>
					</td>
				</tr>
			</table>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 층 선택 <img src='".$required3_path."'> <img src='../images/".$_SESSION["admininfo"]["language"]."/btn_add.gif' alt='추가' align='absmiddle' onclick=\"floor_copy();\" style='cursor:pointer;'></td>
		<td class='input_box_item' colspan=3 style='padding:5px 5px;'>
			<table width=100% border='0' id='sc_floor_time_table'>
			<col width='*'>
			<col width='25px'>";

		$sql = "select * from buyingservice_shopping_center_floor_info where sc_ix = '".$sc_ix."'   ";
		$db->query($sql);
		if($db->total){
			$floor_infos = $db->fetchall("object");

			foreach($floor_infos as $fi){
				$Contents01 .= "
				<tr class='sc_floor_time_tr'>
					<td>
						층 
						<select name='floor[]' class='sc_floor' validation='true' title='층' onchange=\"change_name($(this))\">
							<option value=''>층 선택</option>";
						foreach($_floor_info as $key => $value){
							$Contents01 .= "<option value='".$key."' ".($key==$fi["floor"] ? "selected" : "").">".$value."</option>";
						}
				$Contents01 .= "
						</select> 
						&nbsp;&nbsp; 
						간략설명 <input type='text' name='floor_memo[".$fi["floor"]."]' class='sc_floor_memo textbox' value='".$fi["floor_memo"]."' size='20'> 
						
						<span class='floor_time_div' style='".($sc[sc_mg_time_type]=="E" ? "" : "display:none;" )."'>
							&nbsp; 개점시간 <input type='text' name='floor_start_time[".$fi["floor"]."]' class='sc_floor_start_time textbox' value='".$fi["floor_start_time"]."' size='15' validation='".($fi["floor_start_time"]=="E" ? "true" : "false" )."' title='개점시간'>
							&nbsp; 폐점시간 <input type='text' name='floor_end_time[".$fi["floor"]."]' class='sc_floor_end_time textbox' value='".$fi["floor_end_time"]."' size='15' validation='".($fi["floor_end_time"]=="E" ? "true" : "false" )."' title='폐점시간'>
						</span>
						<img src='../images/".$_SESSION["admininfo"]["language"]."/btn_del.gif' alt='삭제' align='absmiddle' onclick=\"floor_delete($(this));\" style='cursor:pointer;'>
					</td>
				</tr>";
			}

		}else{
			$Contents01 .= "
			<tr class='sc_floor_time_tr'>
				<td>
					층 
					<select name='floor[]' class='sc_floor' validation='true' title='층' onchange=\"change_name($(this))\">
						<option value='' selected>층 선택</option>";
					foreach($_floor_info as $key => $value){
						$Contents01 .= "<option value='".$key."'>".$value."</option>";
					}
			$Contents01 .= "
					</select> 
					&nbsp;&nbsp; 
					간략설명 <input type='text' name='floor_memo[0]' class='sc_floor_memo textbox' value='' size='20'> 
					
					<span class='floor_time_div' style='".($sc[sc_mg_time_type]=="E" ? "" : "display:none;" )."'>
						&nbsp; 개점시간 <input type='text' name='floor_start_time[0]' class='sc_floor_start_time textbox' value='' size='15' validation='".($fi["floor_start_time"]=="E" ? "true" : "false" )."' title='개점시간'>
						&nbsp; 폐점시간 <input type='text' name='floor_end_time[0]' class='sc_floor_end_time textbox' value='' size='15' validation='".($fi["floor_end_time"]=="E" ? "true" : "false" )."' title='폐점시간'>
					</span>
					<img src='../images/".$_SESSION["admininfo"]["language"]."/btn_del.gif' alt='삭제' align='absmiddle' onclick=\"floor_delete($(this));\" style='cursor:pointer;'>
				</td>
			</tr>";
		}

		$Contents01 .= "
			</table>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 라인 선택 <img src='".$required3_path."'></td>
		<td class='input_box_item' colspan=3>
		<table width=100% border=0>
			<col width='80px'>
			<col width='*'>
			<tr>
				<td>
					<input type='checkbox' name='line_all'  id='line_english_all' value='all' onclick=\"CheckAll('line_english');\" ><label for='line_english_all' style='margin-right:10px;'>전체선택</label>
				</td>
				<td>";
		$sql = "select line from buyingservice_shopping_center_line_info where sc_ix = '".$sc_ix."'  ";
		$db->query($sql);
		if($db->total){
			//$db->fetch();
			//print_r($db->dt);
			$line_infos = $db->getrows();
			$line_info = $line_infos[0];
			//print_r($line_info);
		}else{
			$line_info = array();
		}

		foreach($_line_info_english as $key => $value){
			$Contents01 .= "<div style='width:60px;float:left;'><input type='checkbox' name='line[]'  id='line_english_".$key."' class='line_english' value='".$key."' ".(in_array($key,$line_info) ? " checked":"")."><label for='line_english_".$key."' style='margin-right:10px;'>". $value."</label></div>";
		}
		$Contents01 .= "
				</td>
			</tr>
			<tr>
				<td>
					<input type='checkbox' name='line_all'  id='line_korea_all' value='all' onclick=\"CheckAll('line_korea');\" ><label for='line_korea_all' style='margin-right:10px;'>전체선택</label>
				</td>
				<td>";
		foreach($_line_info_korea as $key => $value){
			$Contents01 .= "<div style='width:60px;float:left;' ><input type='checkbox' name='line[]'  id='line_korea_".$key."' class='line_korea' value='".$key."' ".(in_array($key,$line_info) ? " checked":"")."><label for='line_korea_".$key."' style='margin-right:10px;'>". $value."</label></div>";
		}
		$Contents01 .= "
				</td>
			</tr>
			</table>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff>
		<td class='input_box_title'> 호수 <img src='".$required3_path."'></td>
		<td class='input_box_item' colspan=3>
		<table>
			<tr>
				 <td><input type='text' name='start_no' class='textbox number' value='".$sc[start_no]."' size='4' validation='true' title='호수'></td>
				<td> ~ </td>
				<td><input type='text' name='end_no' class='textbox number' value='".$sc[end_no]."' size='4' validation='true' title='호수'></td>
				<td> * 200~300</td>
			</tr>
		</table>
		</td>
	  </tr>";

		$sql = "select id,file_div from file_binary_data where file_div_ix='$sc_ix' and (file_div='shopping_center_transport' or  file_div like 'shopping_center_shopimg%') ";
		$db->query($sql);
		$image_id_array=$db->fetchall();

	$Contents01 .= "
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 교통수단 </td>
		<td class='input_box_item' colspan='3' style='padding:5px;'>
			<input type='file' class='textbox' id='sc_transport' name='sc_transport' />";
			
			if(count($image_id_array)){
				foreach($image_id_array as $img_info){
					if($img_info["file_div"]=="shopping_center_transport"){
						$Contents01 .= "
						<br/><br/><img src='/binary_file_loading.php?id=".$img_info["id"]."' />";
					}
				}
			}
		$Contents01 .= "
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 상가이미지 <img src='../images/".$_SESSION["admininfo"]["language"]."/btn_add.gif' alt='추가' align='absmiddle' onclick=\"shopimg_copy();\" style='cursor:pointer;'></td>
		<td class='input_box_item' colspan='3' style='padding:5px;'>
			<table id='sc_shopimg_table'>";

			if(count($image_id_array)){
				foreach($image_id_array as $img_info){
					if(substr_count($img_info["file_div"],"shopping_center_shopimg")){
						$Contents01 .= "
						<tr class='sc_shopimg_tr' style='padding:5px;'>
							<td>
								<input type='hidden' name='sc_shopimg_id[]' class='sc_shopimg_id' value='".$img_info["id"]."' />
								<input type='file' class='textbox' class='sc_shopimg' name='sc_shopimg[]' /> 
								<img src='../images/".$_SESSION["admininfo"]["language"]."/btn_del.gif' alt='삭제' align='absmiddle' onclick=\"shopimg_delete($(this));\" style='cursor:pointer;'>
								<br/><img src='/binary_file_loading.php?id=".$img_info["id"]."' style='margin-top:10px;' class='binary_img'/>
							</td>
						</tr>";
						$sc_shopimg_bool=true;
					}
				}
			}

			if(!$sc_shopimg_bool){
				$Contents01 .= "<tr class='sc_shopimg_tr' style='padding:5px;'>
					<td>
						<input type='hidden' name='sc_shopimg_id[]' class='sc_shopimg_id' value='' />
						<input type='file' class='textbox'  name='sc_shopimg[]' class='sc_shopimg' />
					</td>
				</tr>";
			}

			$Contents01 .= "
			</table>
		</td>
	  </tr>";
	}else{
	$Contents01 .= "
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 상가 등록/수정일 </td>
	    <td class='input_box_item'>
	    	".($_act=="update" ? $sc[regdate]." / ".$sc[editdate] : "")."
	    </td>	 
	    <td class='input_box_title'> 상가국가/상권선택 <img src='".$required3_path."'></td>
		<td class='input_box_item'>
			".getCommercialCountry($sc[ca_country],"select","onchange=\"window.frames['act'].location.href='./commercial_area.load.php?trigger='+this.value+'&target=ca_ix&form=manufacturer_form'\"")." ".getCommercialAreaInfo($sc[ca_country],$sc[ca_ix])."
		</td>
	  </tr>
	   <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 사이트 URL <img src='".$required3_path."'></td>
	    <td class='input_box_item' colspan='3'>
	    	<input type=text class='textbox' name='sc_sub_domain' value='".$sc[sc_sub_domain]."' size='10' validation='true' title='서브도메인' > .ddm3.co.kr 가능한/불가능한 서브도메인입니다. 링크 URL 설정 
			<input type=text class='textbox' name='sc_url' value='".$sc[sc_url]."' size='30' validation='true' title='링크 URL' >
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 상가명(한/중/영) <img src='".$required3_path."'></td>
	    <td class='input_box_item' colspan='3'>
	    	한국어 <input type=text class='textbox' name='sc_name_korea' value='".$sc[sc_name_korea]."' size='20' validation='true' title='한국어' > &nbsp;&nbsp; 중국어 <input type=text class='textbox' name='sc_name_chinese' value='".$sc[sc_name_chinese]."' size='20' validation='true' title='중국어' > &nbsp;&nbsp; 영어
			<input type=text class='textbox' name='sc_name_english' value='".$sc[sc_name_english]."' size='20' validation='true' title='영어' >
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 상가코드 <img src='".$required3_path."'></td>
	    <td class='input_box_item'>
	    	<input type=text class='textbox' name='sc_code' value='".$sc[sc_code]."' validation='true' title='상가코드' size='3' ".( $_act=="update" ?"style='background-color:#F2F2F2;' readonly" : "")."> * 2자리
	    </td>
	    <td class='input_box_title'> 상가 권한자 <img src='".$required3_path."'></td>
		<td class='input_box_item'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td><input type=hidden name='sc_charger_ix' id='mem_ix' value='".$sc[ca_charger_ix]."' validation=true title='상가 권한자' style='width:100px;'></td>
					<td><input type=text class='ca_change_code textbox' id='buying_mem_name' value='".$sc[name]."' style='width:100px;' onclick=\"PoPWindow('./member_search.php?mode=getcominfo',600,380,'member_search')\"  readonly></td>
					<td style='padding-left:5px;'><img src='../v3/images/".$admininfo["language"]."/btn_member_search.gif' align=absmiddle onclick=\"PoPWindow('./member_search.php?mode=getcominfo',600,380,'member_search')\" class='ca_change_code'  style='cursor:pointer;'></td>
				</tr>
			</table>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 상가 권한 일 <img src='".$required3_path."'></td>
		<td class='input_box_item'>
			<input type='text' name='sc_start_date' class='textbox' value='".$sc[sc_start_date]."' style='height:20px;width:70px;text-align:center;' id='start_datepicker' validation='true' title='상가 권한 일'> ~ <input type='text' name='sc_end_date' class='textbox' value='".$sc[sc_end_date]."' style='height:20px;width:70px;text-align:center;' id='end_datepicker' validation='true' title='상가 권한 일'> ".($_act=="update" ? "&nbsp;&nbsp; 잔여일 ".round((mktime(0,0,0,substr($sc[sc_end_date],5,2),substr($sc[sc_end_date],8,2),substr($sc[sc_end_date],0,4))-time())/86400)."일":"")."
		</td>
	    <td class='input_box_title'> 인센티브 </td>
	    <td class='input_box_item'>
	    	<input type=text class='textbox number' name='sc_incentive' value='".$sc[sc_incentive]."' validation='false' title='인센티브' size='3'> % &nbsp;&nbsp; / 수익 * 인센티브
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 수수료지급일 </td>
		<td class='input_box_item'>
			매월 <input type=text class='textbox number' name='sc_give_day' value='".$sc[sc_give_day]."' validation='false' title='수수료지급일' size='3'> 일 &nbsp;&nbsp; * 전월 말일까지의 정산된 금액만 정산처리합니다.
		</td>
	    <td class='input_box_title'> 사용유무 <img src='".$required3_path."'></td>
	    <td class='input_box_item'>
	    	<input type=radio name='disp' id='disp_1' value='1' ".($sc[disp]=="1" || $sc[disp]=="" ? "checked" :"")."><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ".($sc[disp]=="0" ? "checked" :"")."><label for='disp_0'>미사용</label>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 비고(상세내역) </td>
		<td class='input_box_item' colspan='3'>
			<input type=text class='textbox' name='sc_msg' value='".$sc[sc_msg]."' validation='false' title='비고' style='width:90%' >
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 사업자관련 </td>
		<td class='input_box_item' colspan='3'>
			대표자명 <input type=text class='textbox' id='com_ceo' value='".$sc[com_ceo]."' size='10' style='background-color:#F2F2F2;' readonly> &nbsp;
			사업자번호 <input type=text class='textbox' id='com_number' value='".$sc[com_number]."' size='12' style='background-color:#F2F2F2;' readonly> &nbsp;
			대표전화번호 <input type=text class='textbox' id='com_phone' value='".$sc[com_phone]."' size='12' style='background-color:#F2F2F2;' readonly> &nbsp;
			팩스 <input type=text class='textbox' id='com_fax' value='".$sc[com_fax]."' size='12' style='background-color:#F2F2F2;' readonly>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 사업장주소 </td>
		<td class='input_box_item' colspan='3'>
			우편번호 <input type=text class='textbox' id='com_zip' value='".$sc[com_zip]."' size='10' style='background-color:#F2F2F2;' readonly> &nbsp;
			주소 <input type=text class='textbox' id='com_addr' value='".$sc[com_addr1]." ".$sc[com_addr2]."' size='50' style='background-color:#F2F2F2;' readonly>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 담당자 </td>
		<td class='input_box_item' colspan='3'>
			성명 <input type=text class='textbox' id='customer_name' value='".$sc[customer_name]."' size='6' style='background-color:#F2F2F2;' readonly> &nbsp;
			직책 <input type=text class='textbox' id='customer_position' value='".$sc[customer_position]."' size='7' style='background-color:#F2F2F2;' readonly> &nbsp;
			연락처 <input type=text class='textbox' id='customer_phone' value='".$sc[customer_phone]."' size='12' style='background-color:#F2F2F2;' readonly> &nbsp;
			핸드폰 <input type=text class='textbox' id='customer_mobile' value='".$sc[customer_mobile]."' size='12' style='background-color:#F2F2F2;' readonly> &nbsp;
			이메일 <input type=text class='textbox' id='customer_mail' value='".$sc[customer_mail]."' size='20' style='background-color:#F2F2F2;' readonly>
		</td>
	  </tr>";
	}
	 $Contents01 .= "
	  </table>";

$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');


$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";



$Contents = "<form name='manufacturer_form' action='shopping_center.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' target='act'>
<input name='mmode' type='hidden' value='$mmode'>
<input name='act' type='hidden' value='$_act'>
<input name='sc_ix' type='hidden' value='$sc_ix'>
<input name='view_type' type='hidden' value='$view_type'>";
$Contents = $Contents."<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents = $Contents."<tr><td width='100%'>";
$Contents = $Contents.$Contents01;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."</table>";

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'D');

$help_text = HelpBox("<div style='padding-top:6px;'>상가등록</div>", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>

$(document).ready(function() {
	

	$('input[name=sc_mg_time_type]').click(function(){
		if($(this).val()=='E'){
			$('#sc_mg_time_tr').hide();
			$('.floor_time_div').show();
			$('.sc_floor_start_time').attr('validation','true');
			$('.sc_floor_end_time').attr('validation','true');
		}else{
			$('#sc_mg_time_tr').show();
			$('.floor_time_div').hide();
			$('.sc_floor_start_time').attr('validation','false');
			$('.sc_floor_end_time').attr('validation','false');
		}
	})

	$(\"#start_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
			$('#end_datepicker').val(dateText);
		}else{
			$('#end_datepicker').datepicker('setDate','+0d');
		}
	}

	});

	$(\"#end_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

});

function CheckAll(class_name){
	if($('#'+class_name+'_all').attr('checked')){
		$('.'+class_name).each(function(){
			$(this).attr('checked',true);
		});
	}else{
		$('.'+class_name).each(function(){
			$(this).attr('checked',false);
		});
	}
}

function shopimg_copy(){
	
	var newRow = $('.sc_shopimg_tr:first').clone(true).appendTo('#sc_shopimg_table');  
	newRow.find('.sc_shopimg_id').val('');
	newRow.find('.sc_shopimg').val('');
	newRow.find('.binary_img').remove();
}

function shopimg_delete(obj){
	
	if($('.sc_shopimg_tr').length > 1){
		obj.parent().parent().remove();
	}else{
		$('.sc_shopimg_tr').find('.sc_shopimg_id').val('');
		$('.sc_shopimg_tr').find('.sc_shopimg').val('');
		$('.sc_shopimg_tr').find('.binary_img').remove();
	}
}

function change_name(obj){
	var tmp_name;
	obj.parent().parent().find('input').each(function(){
		tmp_name = $(this).attr('name').replace(/\[(.*?)\]/gi,'\['+obj.val()+'\]');
		$(this).attr('name',tmp_name);
	});
}

function floor_copy(){
	var newRow = $('.sc_floor_time_tr:first').clone().appendTo('#sc_floor_time_table');  
	newRow.find('.sc_floor').val('');
	newRow.find('.sc_floor_memo').val('');
	newRow.find('.sc_floor_start_time').val('');
	newRow.find('.sc_floor_end_time').val('');
}

function floor_delete(obj){
	
	if($('.sc_floor_time_tr').length > 1){
		obj.parent().parent().remove();
	}else{
		$('.sc_floor_time_tr').find('.sc_floor').val('');
		$('.sc_floor_time_tr').find('.sc_floor_memo').val('');
		$('.sc_floor_time_tr').find('.sc_floor_start_time').val('');
		$('.sc_floor_time_tr').find('.sc_floor_end_time').val('');
	}
}

function get_com_info (company_id){
	
	$.ajax({ 
		type: 'POST', 
		data: {'act': 'com_json', 'company_id':company_id},
		url: './commercial_area.act.php',  
		dataType: 'json', 
		async: false, 
		beforeSend: function(){ 

		},  
		success: function(com_data){ 

			$('#com_ceo').val(com_data[0].com_ceo);
			$('#com_number').val(com_data[0].com_number);
			$('#com_phone').val(com_data[0].com_phone);
			$('#com_fax').val(com_data[0].com_fax);
			$('#com_zip').val(com_data[0].com_zip);
			$('#com_addr').val(com_data[0].com_addr1 + ' ' + com_data[0].com_addr2);
			$('#customer_name').val(com_data[0].customer_name);
			$('#customer_position').val(com_data[0].customer_position);
			$('#customer_phone').val(com_data[0].customer_phone);
			$('#customer_mobile').val(com_data[0].customer_mobile);
			$('#customer_mail').val(com_data[0].customer_mail);

		}
	}); 

}

 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = buyingservice_menu();
	$P->Navigation = "상가관리 > 상가등록";
	$P->title = "상가등록";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = buyingservice_menu();
	$P->Navigation = "상가관리 > 상가등록";
	$P->title = "상가등록";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


/*

CREATE TABLE `shop_address_group` (
  `sc_ix` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `parent_sc_ix` int(4) unsigned DEFAULT NULL,
  `sc_name` varchar(20) DEFAULT NULL,
  `depth` int(2) unsigned DEFAULT '1',
  `disp` char(1) DEFAULT '1',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`sc_ix`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
*/
?>