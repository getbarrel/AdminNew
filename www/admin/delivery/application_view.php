<? 
include("../class/layout.class");

$db = new Database;
$db2 = new Database;

$sql = 	"SELECT di.*, dg.group_depth , case when dg.group_depth = 1 then dg.group_ix else parent_group_ix end as parent_group_ix 
				FROM delivery_info di, delivery_group dg 
				where di_ix ='$di_ix' and di.group_ix = dg.group_ix ";

//echo $sql;
$db->query($sql);
$db->fetch();
$addressee_zip = split("-",$db->dt[addressee_zip]);

if($db->total){
	$act = "update";
	
	if($db->dt[name_type] == "N"){
			$shipper_name = $db->dt[shipper_first_name]." ".$db->dt[shipper_last_name];
		}else{
			$shipper_name = $db->dt[shipper_compnay_name];
		}
}else{
	$act = "insert";
}


$Contents01 = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6 style='padding-bottom:0px;'> ".GetTitleNavigation("택배접수내역", "택배관리 > 택배접수내역 ")."</td>
	  </tr>
	  <tr >
	    <td align='left' colspan=4>";


$Contents01 .= "		
		<div class='t_no' style='margin: 10px 5px 20px; padding:5px 0px;border-top: solid 3px #c6c6c6; '>
			<!-- my_movie start -->
			<div class='my_box' style='height:150px;'>
				
					<table width=100% cellpadding=5 cellspacing=0 border=0>
					<col width=150>
					<col width=300>
					<col width=160>
					<col width=*>
					
					<tr height=1><td colspan=4 style='border-bottom:2px solid #efefef'><IMG id=SM114641I src='../images/dot_org.gif' border=0 align=absmiddle>&nbsp;<b onclick='AutoFill(document.div_form)'>Shipper (보내는 사람)</b></td></tr>";
if($di_ix){
$Contents01 .= "							
					<tr bgcolor=#ffffff height='50'>
				    <td><img src='../image/ico_dot2.gif' align=absmiddle><b>  접수번호 : </b></td>
				    <td colspan=3 >
				    	<b style='font-size:20px;'>".$db->dt[application_no]."</b>
							
				    </td>    
				  </tr>
				  <tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>	";
}
$Contents01 .= "					  
					<!--tr bgcolor=#ffffff >
				    <td ><img src='../image/ico_dot2.gif' align=absmiddle><b>  Group : </b></td>
				    <td colspan=3>
				    	".getGroupInfoSelect('parent_group_ix', '1 차그룹',$db->dt[parent_group_ix], $db->dt[parent_group_ix], 1, " onChange=\"loadCampaignGroup(this,'group_ix')\" ")."
				    	".getGroupInfoSelect('group_ix', '2 차그룹',$db->dt[parent_group_ix], $db->dt[group_ix], 2)."<br>
				    	
							<div class=small style='margin-top:4px;'>택배 관리 그룹 : 택배 성격에 맞게 분류해서 관리 하실수 있습니다. 선택하지 않을시 기본그룹으로 자동 저장됩니다.</div>
				    </td>    
				  </tr>
				  <tr height=1><td colspan=4 background='../image/dot.gif'></td></tr-->	
				  <tr bgcolor=#ffffff height=30>
				    <td><img src='../image/ico_dot2.gif' align=absmiddle><b> Name : </b></td><td>".$shipper_name." <span class=small></span></td>
				    <td><img src='../image/ico_dot2.gif' align=absmiddle><b> Tel : </b></td><td>".$db->dt[shipper_phone]." <span class=small></span></td>
				  </tr>
				  <tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
				  <tr bgcolor=#ffffff height=30>
				    <td><img src='../image/ico_dot2.gif' align=absmiddle><b> Mobile : </b></td><td>".$db->dt[shipper_mobile]." <span class=small></span></td>				  
				    <td><img src='../image/ico_dot2.gif' align=absmiddle><b> Email : </b></td><td>".$db->dt[shipper_email]." <span class=small></span></td>
				  </tr>
				  <tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>	 
				  <tr bgcolor=#ffffff height=30>
				    <td><img src='../image/ico_dot2.gif' align=absmiddle><b> FAX : </b></td><td>".$db->dt[shipper_fax]."<span class=small></span></td>
				    
				  </tr>
				  <tr height=1><td colspan=4 background='../image/dot.gif'></td></tr> 	
				  <tr bgcolor=#ffffff height=40>
				    <td><img src='../image/ico_dot2.gif' align=absmiddle><b> Address </b>: </td>
				    <td colspan=3>
				    	".$db->dt[shipper_address1]." 
				    	".$db->dt[shipper_address2]." <span class=small></span>
				    </td>
				  </tr>
				  <tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
				  <tr height=1><td colspan=4 style='border-bottom:2px solid #efefef;padding-top:50px;' onclick='AutoFillText(document.div_form)'><IMG id=SM114641I src='../images/dot_org.gif' border=0 align=absmiddle>&nbsp;<b >받는사람</b></td></tr>	
				  
				  <tr bgcolor=#ffffff height=30>
				    <td><img src='../image/ico_dot2.gif' align=absmiddle><b> 받는사람 성명 : </b></td>
				    <td>".$db->dt[addressee_name]."<span class=small></span></td>
				    <td><img src='../image/ico_dot2.gif' align=absmiddle><b> 받는사람 영문성명 : </b></td>
				    <td>".$db->dt[addressee_eng_name]." <span class=small></span></td>
				    
				  </tr>
				  <tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
				  <tr bgcolor=#ffffff height=30>
				    <td nowrap><img src='../image/ico_dot2.gif' align=absmiddle><b> 받는사람 전화번호 : </b></td>
				    <td>".$db->dt[addressee_phone]." <span class=small></span></td>
				    <td><img src='../image/ico_dot2.gif' align=absmiddle><b> 받는사람 핸드폰 : </b></td>
				    <td>".$db->dt[addressee_mobile]."<span class=small></span></td>				  
				    
				  </tr>
				  <tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>	 
				  <tr bgcolor=#ffffff height=30>
				  	<td><img src='../image/ico_dot2.gif' align=absmiddle><b> 받는사람 이메일 : </b></td>
				    <td>".$db->dt[addressee_email]."<span class=small></span></td>
				    <td><img src='../image/ico_dot2.gif' align=absmiddle><b> 주민/사업자번호 : </b></td>
				    <td>".$db->dt[certification_no]." <span class=small></span></td>
				    
				  </tr>
				  <tr height=1><td colspan=4 background='../image/dot.gif'></td></tr> 	
				  <tr bgcolor=#ffffff >
				    <td><img src='../image/ico_dot2.gif' align=absmiddle> <b>배송주소</b> : </td>
				    <td colspan=3 style='line-height:140%;'>
				    	".$addressee_zip[0]." - ".$addressee_zip[1]."<br>
				    	".$db->dt[addressee_address1]."
				    	".$db->dt[addressee_address2]."
				    </td>
				  </tr>
				  <tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
				  
				  <tr bgcolor=#ffffff height='100'>
				    <td><img src='../image/ico_dot2.gif' align=absmiddle> 배송 메모 : </td>
				    <td colspan=3 valign=top style='padding:10 0 0 0'>".$db->dt[memo]."</td>
				    
				  </tr>
				  <tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>	
					
				  </table>
					<form name='div_form' action='application.act.php' method='post' onsubmit='return CheckForm(this)' style='display:inline;' target='act' ><!---->
					<input name='act' type='hidden' value='status_update'>
					<input name='di_ix' type='hidden' value='$di_ix'>
					<table width='100%' border='0' cellpadding='0' cellspacing='0'>
						<tr height=1><td colspan=11 style='padding-top:50px;padding-bottom:5px;'><IMG id=SM114641I src='../images/dot_org.gif' border=0 align=absmiddle>&nbsp;<b >배송품목-영문</b></td></tr>	
						<tr height='25' bgcolor='#efefef' align=center>
							<td width='5%' class='s_td'><b>No</b></td>
							<td width='40%'  class='m_td' ><b>Item Name</b></td>
							<td width='10%' class='m_td'><b>Brand</b></td>
							<td width='15%' class='m_td'><b>단가</b></td>
							<td width='10%' class='e_td'><b>수량</b></td>
						</tr>";

	
		$sql = "SELECT * from delivery_detail_info WHERE di_ix = '".$di_ix."' order by ddi_ix asc ";
	
		
	$db2->query($sql);
	


	for($i = 0; $i < $db2->total; $i++){
			$db2->fetch($i);
			$Contents01 .= "
						<tr height='33' align='center'>
							<td align=center>".($i+1)."</td>
							<td>".$db2->dt[item_name]."</td>
							<td align=center>".$db2->dt[brand]."</td>
							<td align=center>
								".$db2->dt[price]."
								".$db2->dt[unit]."
							</td>
							<td align='center'>".$db2->dt[amount]."</td>
						</tr>
						<tr><td colspan=11 class=dot-x></td></tr>";
	}
	
$Contents01 = $Contents01."
						<tr height=1><td colspan=11 style='border-bottom:2px solid #efefef;padding:40px 10px 10px 0px'><IMG id=SM114641I src='../images/dot_org.gif' border=0 align=absmiddle>&nbsp;<b >배송비 정산</b></td></tr>	
						<tr bgcolor=#ffffff >
							<td colspan=11 align=center style='padding-top:30px;'>
							<table cellpadding=5 cellspacing=0 border=0>
				    	<tr>
				    	
					    	<td colspan=4 style='padding:0 0 0 5px'> 
					    	<div style='display:inline;position:relative;left:-20px;'><input type='checkbox' name='weight' value='".$db->dt[weight]."' class='textbox' id='add_delivery_fee1' style='font-size:16px;height:35px;width:100px;border:0px;color:red;' align=absmiddle ><label for='add_delivery_fee1'><div style='display:inline;position:relative;left:-30px;font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;'>도서상간비용</div></label></div>
					    						    	</td>
							
				    	
							</tr>
							<tr>
				    	
					    	<td colspan=4 style='padding:0 0 0 5px'> 
					    	<div style='display:inline;position:relative;left:-20px;'><input type='checkbox' name='weight' value='".$db->dt[weight]."' class='textbox' id='add_delivery_fee2' style='font-size:16px;height:35px;width:100px;border:0px;' align=absmiddle ><label for='add_delivery_fee2'><div style='display:inline;position:relative;left:-30px;font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;'>15만원 또는 음식물 추가비용</div></label></div>
					    	</td>
							
				    	
							</tr>
							<tr>
				    		<td style='font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding:0 20 0 20'> 총무게 </td>
					    	<td style='padding:0 0 0 5px'> <input type='text' name='weight' value='".$db->dt[weight]."' class='textbox' style='font-size:16px;height:30px;width:150px;text-align:right;padding:4 10 0 0;' align=absmiddle ></td>
							
				    		<td style='font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding:0 20 0 20'> 배송금액 </td>
					    	<td style='padding:0 0 0 5px'> <input type='text' name='delivery_fee'  value='".$db->dt[delivery_fee]."' class='textbox' style='font-size:16px;height:30px;width:150px;text-align:right;padding:4 10 0 0;' align=absmiddle ></td>
							</tr>
							</table>
							</td>
						</tr>
						<tr bgcolor=#ffffff ><td colspan=11 align=center style='padding-top:30px;'><input type='image' src='../image/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
						</table>
							
				  </form>
				</div>
				
		</div>
	    </td>
	  </tr>
	  
	  </table>";
	  



$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";

$Contents = $Contents."</table >";

$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >택배 접수를 원활히 관리하기 위해서는 택배 관리그룹을 선택하셔야 합니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>핸드폰 번호</u>와 <u>이메일주소</u>는  메일링이나 SMS 발송시 사용되므로 정확히 입력하여 주시기 바랍니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >부가적으로 회사정보등을 입력해서 관리 하실 수 있습니다.</td></tr>
	</table>
	";


$help_text = HelpBox("택배접수내역", $help_text);			
	
$Contents = $Contents.$help_text;	
 $Script = "
 <script  id='dynamic'></script>
 <script language='javascript'>
function AutoFill(frm){
//alert(frm);
	for(i=0;i < frm.elements.length;i++){
		//alert(frm.elements.name);
			if(frm.elements[i].type == 'text'){
				frm.elements[i].value = frm.elements[i].name;
			}
		/*
		if(!CheckForm(frm.elements[i])){
			return false;
		}
		*/
	}
	
}

function AutoFillText(frm){
//alert(frm);
	frm.shipper_name.value = 'hun shick shin';
	frm.shipper_phone.value = '02-2058-2214';
	frm.shipper_mobile.value = '010-5484-5455';
	frm.shipper_fax.value = '02-2058-2215';
	frm.shipper_email.value = 'tech@forbiz.co.kr';
	frm.shipper_address1.value = '서울시 서초구 양재동 16-3 번지 ';
	frm.shipper_address2.value = '윤화빌딩 6층 ';
	
	frm.addressee_name.value = '신훈식';
	frm.addressee_eng_name.value = '신훈식';
	frm.addressee_phone.value = '02-2058-2214';
	frm.addressee_mobile.value = '010-5484-5455';
	frm.certification_no.value = '750511-1351417';
	frm.addressee_email.value = 'tech@forbiz.co.kr';
	frm.addressee_address1.value = '서울시 서초구 양재동 16-3 번지 ';
	frm.addressee_address2.value = '윤화빌딩 6층 ';
	frm.addressee_zip1.value = '123';
	frm.addressee_zip2.value = '456';
}

 function updateBankInfo(div_ix,div_name,disp){
 	var frm = document.div_form;
 	
 	frm.act.value = 'update';
 	frm.div_ix.value = div_ix;
 	frm.div_name.value = div_name;
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}
 
}

function CheckForm(frm){

	if(frm.parent_group_ix.value == ''){
	 		alert('1차 그룹은 반드시 선택하셔야 합니다.');
	 		return false;
 	}
		
	if(frm.shipper_name.value.length < 1){
		alert('이름을 입력해주세요');
		frm.shipper_name.focus();
		return false;
	}
	
	if(frm.shipper_mobile.value.length < 1){
		alert('핸드폰을 입력해주세요');
		frm.shipper_mobile.focus();
		return false;
	}
	
	if(frm.email.value.length < 1){
		alert('이메일을 입력해주세요');
		frm.email.focus();
		return false;
	}else{
		var PT_email = /[a-z0-9_]{2,}@[a-z0-9-]{2,}\.[a-z0-9]{2,}/i;  // 이메일
		if (!PT_email.test(frm.email.value)){
			alert('이메일 형식이 아닙니다. 확인후 다시 시도해주세요');
			frm.email.focus();
			return false;
		}
	}
	
	return true;
}
 
function deleteMaillingInfo(act, ci_ix){
 	if(confirm('해당메일링 리스트를  정말로 삭제하시겠습니까?')){
 		var frm = document.div_form; 	
 		frm.act.value = act;
 		frm.ci_ix.value = ci_ix;
 		frm.submit();
 	}	
}

function show_mailling_info(obj){
	if(obj.style.display == 'block'){
		obj.style.display = 'none';
	}else{
		obj.style.display = 'block';
	}
}

function loadCampaignGroup(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;	
	var form = sel.form.name;
	var depth = sel.depth;
	//document.write('campaigngroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2');
	dynamic.src = 'campaigngroup.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target+'&depth=2';
	
}

function zipcode(id)
{
	var zip = window.open('./zipcode.php?type='+id,'','width=440,height=350,scrollbars=yes,status=no');
}

 </script>
 ";
	
if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = delivery_menu();
	$P->Navigation = "HOME > 택배관리 > 택배접수내역";
	$P->strContents = $Contents;
	$P->NaviTitle = "택배접수내역";
	
	echo $P->PrintLayOut();	
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = delivery_menu();
	$P->Navigation = "HOME > 택배관리 > 택배접수내역";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

function getGroupInfoSelect($obj_id, $obj_txt, $parent_group_ix, $selected, $depth=1, $property=""){
	$mdb = new Database;
	if($depth == 1){
		$sql = 	"SELECT abg.*
				FROM delivery_group abg 
				where group_depth = '$depth'
				group by group_ix ";
	}else if($depth == 2){
		$sql = 	"SELECT abg.*
				FROM delivery_group abg 
				where group_depth = '$depth' and parent_group_ix = '$parent_group_ix'
				group by group_ix ";
	}
	//echo $sql;
	$mdb->query($sql);
	
	$mstring = "<select name='$obj_id' id='$obj_id' $property>";
	$mstring .= "<option value=''>$obj_txt</option>";
	if($mdb->total){
		
		
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[group_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[group_ix]."' selected>".$mdb->dt[group_name]."</option>";
			}else{
				if($selected == "" && $i == 0){
					$mstring .= "<option value='".$mdb->dt[group_ix]."' selected>".$mdb->dt[group_name]."</option>";
				}else{
					$mstring .= "<option value='".$mdb->dt[group_ix]."'>".$mdb->dt[group_name]."</option>";
				}
			}
		}
		
	}	
	$mstring .= "</select>";
	
	return $mstring;
}
?>