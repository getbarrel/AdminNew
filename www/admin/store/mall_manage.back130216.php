<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");

$shmop = new Shared("member_reg_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$member_reg_rule = $shmop->getObjectForKey("member_reg_rule");

$member_reg_rule = unserialize(urldecode($member_reg_rule));

//print_r($member_reg_rule);
$db = new Database;


$sql = "SELECT * FROM ".TBL_SHOP_SHOPINFO." where mall_ix = '".$admininfo[mall_ix]."' and mall_div = '".$admininfo[mall_div]."'  ";

//echo $sql;
$db->query($sql);

$db->fetch();

if($join_type == ""){
	$join_type = "B";
}

$Contents01 = "
<form name='login_form' action='mall_manage.act.php' method='post' onsubmit='return CheckFormValue(this)' target='act'>
<input type=hidden name='join_type' id='join_type' value='".$join_type."'>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' >
	  <col width=150>
	  <col width=250>
	  <col width=*>
	  <tr >
			<td align='left' colspan=3> ".GetTitleNavigation("회원가입설정", "상점관리 > 쇼핑몰 환경설정 > 회원가입설정 ")."</td>
	  </tr>
	  <tr>
		<td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b>회원전용 사용여부</b></div>")."</td>
	</tr>
   </table>
   <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	  <col width=20%>
	  <col width=30%>
	  <col width=20%>
	  <col width=30%>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>사용권한설정</b></td>
		<td class='input_box_item'>
			<input type=radio name='mall_open_yn' id='mall_open_y' value='Y' ".CompareReturnValue("Y",$member_reg_rule[mall_open_yn],"checked")."><label for='mall_open_y'>회원전용</label>
			<input type=radio name='mall_open_yn' id='mall_open_n' value='N' ".CompareReturnValue("N",$member_reg_rule[mall_open_yn],"checked")."><label for='mall_open_n'>전체</label>
		</td>
		<td class='input_box_title'> <b>승인타입</b></td>
		<td class='input_box_item'>
			<input type=radio name='auth_type' id='auth_type_a' value='A' ".($member_reg_rule[auth_type] == "A" || $member_reg_rule[auth_type] == "" ? "checked":"")."><label for='auth_type_a'>자동승인</label>
			<input type=radio name='auth_type' id='auth_type_m' value='M' ".CompareReturnValue("M",$member_reg_rule[auth_type],"checked")."><label for='auth_type_m'>관리자 승인</label>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>인증방식</b></td>
		<td class='input_box_item' colspan=3>
			<input type=radio name='auth_method' id='auth_method_a' value='J' ".($member_reg_rule[auth_method] == "J" || $member_reg_rule[auth_method] == "" ? "checked":"")."><label for='auth_method_a'>주민번호, IPIN</label>
			<input type=radio name='auth_method' id='auth_method_m' value='E' ".CompareReturnValue("E",$member_reg_rule[auth_method],"checked")."><label for='auth_method_m'>이메일인증</label>
		</td>
	</tr>
	<tr bgcolor=#ffffff id>
		<td class='input_box_title'> <b>실명인증 사용여부</b></td>
		<td class='input_box_item' colspan=3>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td style='padding:0px 10px 0px 0px'>
					<input type='checkbox' id='mall_use_identificationUse' name='mall_use_identificationUse' value='Y'".(($member_reg_rule[mall_use_identificationUse] == "Y")	?	' checked':'')." onclick=\"document.getElementById('mall_use_identification').style.display = (this.checked)	?	'':'none';\" style='vertical-align:middle;'' /> <label for='mall_use_identificationUse' style='vertical-align:middle;'>ID 입력</label>
					<input type='text' id='mall_use_identification' name='mall_use_identification' class='textbox' value='".$member_reg_rule[mall_use_identification]."' style='display:".(($member_reg_rule[mall_use_identification])	?	'':'none').";'  />
					<!--input type=radio name='mall_use_identification' id='mall_use_identification_y' value='Y' ".CompareReturnValue("Y",$member_reg_rule[mall_use_identification],"checked")."><label for='mall_use_identification_y'>사용함</label>
					<input type=radio name='mall_use_identification' id='mall_use_identification_n' value='N' ".CompareReturnValue("N",$member_reg_rule[mall_use_identification],"checked")."><label for='mall_use_identification_n'>사용안함</label-->
					</td>
					<td align=left><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span> </td>
				</tr>
			</table>
		</td>

	</tr>
	<tr bgcolor=#ffffff id>
		<td class='input_box_title'> <b>아이핀 사용여부</b></td>
		<td class='input_box_item' style='padding:7px 5px 7px 5px;' colspan=3>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td style='padding:0px 10px 0px 0px'>
					<input type='checkbox' id='mall_use_ipin' name='mall_use_ipin' value='Y'".(($member_reg_rule[mall_use_ipin] == "Y")	?	' checked':'')." onclick=\"document.getElementById('ipin_info').style.display = (this.checked)	?	'':'none';\" style='vertical-align:middle;'' /> <label for='mall_use_ipin' style='vertical-align:middle;'>ID 입력</label>
					</td>
                   	<td align=left><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span> </td>
				</tr>
                <tr>
                    <td colspan='3'>
                         <table id='ipin_info' style='display:".(($member_reg_rule[mall_use_ipin] == "Y")	?	'':'none').";'>
                            <tr>
                                <td>회원사아이디</td>
                                <td><input type='text' id='mall_ipin_code' name='mall_ipin_code' class='textbox' value='".$member_reg_rule[mall_ipin_code]."' /></td>
                            </tr>
                            <tr>
                                <td>사이트식별번호</td>
                                <td><input type='text' id='mall_ipin_pw' name='mall_ipin_pw' class='textbox' value='".$member_reg_rule[mall_ipin_pw]."' /></td>
                            </tr>
                        </table>
                    </td>
                </tr>
			</table>
		</td>

	</tr>
    <tr bgcolor=#ffffff id>
		<td class='input_box_title'> <b>안심체크 사용여부</b></td>
		<td class='input_box_item' colspan=3 style='padding:7px 5px 7px 5px;' >
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td style='padding:0px 10px 0px 0px'>
					<input type='checkbox' id='mall_use_niceid' name='mall_use_niceid' value='Y'".(($member_reg_rule[mall_use_niceid] == "Y")	?	' checked':'')." onclick=\"document.getElementById('niceid_info').style.display = (this.checked)	?	'':'none';\" style='vertical-align:middle;'' /> <label for='mall_use_niceid' style='vertical-align:middle;'>CODE 입력</label>
					</td>
                   	<td align=left><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span> </td>
				</tr>
                <tr>
                    <td colspan='3'>
                         <table id='niceid_info' style='display:".(($member_reg_rule[mall_use_niceid] == "Y")	?	'':'none').";'>
                            <tr>
                                <td>사이트코드</td>
                                <td><input type='text' id='mall_niceid_code' name='mall_niceid_code' class='textbox' value='".$member_reg_rule[mall_niceid_code]."' /></td>
                            </tr>
                            <tr>
                                <td>사이트비밀번호</td>
                                <td><input type='text' id='mall_niceid_pw' name='mall_niceid_pw' class='textbox' value='".$member_reg_rule[mall_niceid_pw]."' /></td>
                            </tr>
                        </table>
                    </td>
                </tr>
			</table>
		</td>

	</tr>


	<!--tr bgcolor=#ffffff >
		<td ><img src='../image/ico_dot2.gif' align=absmiddle> 인트로사용</td>
		<td>
			<input type=radio name='mall_intro_use' id='mall_intro_use_y' value='Y' ".CompareReturnValue("Y",$member_reg_rule[mall_intro_use],"checked")."><label for='mall_intro_use_y'>사용</label>
			<input type=radio name='mall_intro_use' id='mall_intro_use_n' value='N' ".CompareReturnValue("N",$member_reg_rule[mall_intro_use],"checked")."><label for='mall_intro_use_n'>사용 하지않음</label>
			<span class=small style='color:gray'>디자인 관리에서 인트로 화면에 대한 디자인 작업을 하셔야 합니다.</span>
		</td>
		<td align=left></td>
	</tr-->

	  <tr bgcolor=#ffffff height=110>
	    <td class='input_box_title'><b>가입불가ID</b></td>
	    <td class='input_box_item' colspan=3><textarea type=text class='textbox' name='mall_deny_id' style='width:98%;height:85px;padding:2px;'>".$member_reg_rule[mall_deny_id]."</textarea></td>
	  </tr>
	  </table>
<ul class='paging_area' >
	<li class='front'><img src='../image/emo_3_15.gif' align=absmiddle > <span  style='line-height:120%'> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</span></li>
	<li class='back'></li>
  </ul>";
/*
$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px; line-height:120%' class=small>
		  - 회원가입시 제한할 ID를 입력하세요. <b>아이디는 콤마(,)</b>로 구분합니다.<br>
		  - 기본 제한 ID는 <b>admin, administration, administrator, webmaster, root, master</b>로써 총 6개 입니다.<br>
	</td>
</tr>
</table>
";*/
$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents02 = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' >
	  <col width=20%>
	  <col width=20%>
	  <col width=20%>
	  <col width=20%>
	  <col width=20%>
	  <tr>
	    <td align='left' colspan=5 style='width:100%;padding-bottom:12px;' >
	    <div class='tab'>
			<table class='s_org_tab' >
			<tr>
				<td class='tab' >
					<table id='tab_01' ".(($join_type == "B" || $join_type == "") ? "class='on' ":"")." >
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='?join_type=B&company_id=".$company_id."'>기본 회원가입 항목설정</a></td>
						<th class='box_03'></th>
					</tr>
					</table>

					<table id='tab_03' ".($join_type == "F1" ? "class='on' ":"")." style='border:0px solid red;'>
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

							$Contents02 .= "<a href='?join_type=F1&company_id=".$company_id."'>외국인 회원가입 항목설정</a>";

						$Contents02 .= "

						</td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
				<td style='width:400px;text-align:right;vertical-align:bottom;padding:0 0 10px 0'>

				</td>
			</tr>
			</table>
		</div>
	    </td>
	  </tr>
	  <tr>
	    <td align='left' colspan=5> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>회원 가입 항목설정</b></div>")."</td>
	  </tr>

	  <tr height=25 align=left>
		<td colspan=5>
		<a href='javascript:moveTreeGroup(1);'><img src='../images/".$admininfo["language"]."/btn_sort_down.gif'></a> <a href='javascript:moveTreeGroup(-1);'><img src='../images/".$admininfo["language"]."/btn_sort_up.gif'></a>
		<a href='javascript:moveTreeGroup(5);'><img src='../images/".$admininfo["language"]."/btn_sort_5down.gif'></a> <a href='javascript:moveTreeGroup(-5);'><img src='../images/".$admininfo["language"]."/btn_sort_5up.gif'></a>
		<a href='javascript:moveTreeGroup(10);'><img src='../images/".$admininfo["language"]."/btn_sort_10down.gif'></a> <a href='javascript:moveTreeGroup(-10);'><img src='../images/".$admininfo["language"]."/btn_sort_10up.gif'></a>
		<a href='javascript:moveTreeGroup(-10000);'><img src='../images/".$admininfo["language"]."/btn_sort_end.gif'></a> <a href='javascript:moveTreeGroup(10000);'><img src='../images/".$admininfo["language"]."/btn_sort_top.gif'></a>
		</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
	  <col width=20%>
	  <col width=20%>
	  <col width=20%>
	  <col width=20%>
	  <col width=20%>
	 <tr height=25 align=center>
		<td class=s_td >필드명</td>
		<td class=m_td >필드값 타입</td>
		<td class=m_td >필드값 설정</td>
		<td class=m_td >사용유무</td>
		<td class=e_td >필수값체크유무</td>

	</tr>
	</table>";
	$Contents03 ="<table border=0 cellpadding=0 cellspacing=0 align='left' width='100%' id='list_table' class='list_table_box'>
					 <col width=20%>
	  <col width=20%>
	  <col width=20%>
	  <col width=20%>
	  <col width=20%>";
if($join_type == "B" || $join_type == "" ){
	$sql = "select * from shop_join_info where join_type='B' order by vieworder   ";
}else{
	$sql = "select * from shop_join_info where join_type='F1' order by vieworder  ";
}
	//$sql = "select * from shop_join_info order by vieworder";
	$db->query($sql);
	//echo $db->total;
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);
		$Contents03 .= "<tr height='30'  style='cursor:pointer;' onclick=\"spoit(this)\" idx='".$db->dt[idx]."'>

							";
							if($db->dt[modify_yn] == "N"){
								$Contents03 .= "<td class='list_box_td point'  style='padding-left:20px'>".$db->dt[field_name]."</td>
												<td class='list_box_td list_bg_gray'  align=center style='padding:0px 10px;'>".$db->dt[field_type]."</td>
												<td class='list_box_td ' ></td>";
							}else{
						$Contents03 .= "<td class='list_box_td point'  style='padding-left:10px'>
													<input type='text' class=textbox  name='field_name[".$db->dt[idx]."]' ".($db->dt[field_name] == "" ? "":"value='".$db->dt[field_name]."'")." style='width:80%' onkeydown=\"return true\" onkeyup=\"return true\">
												</td>
												<td class='list_box_td list_bg_gray'  align=center width='170px'>
													<select name='field_type[".$db->dt[idx]."]' class=textbox >
														<option value='' ".($db->dt[field_type] == "" ? "selected":"").">필드타입을 선택하세요</option>
														<option value='text' ".($db->dt[field_type] == "text" ? "selected":"").">text</option>
														<option value='password' ".($db->dt[field_type] == "password" ? "selected":"").">password</option>
														<option value='radio' ".($db->dt[field_type] == "radio" ? "selected":"").">radio</option>
														<option value='checkbox' ".($db->dt[field_type] == "checkbox" ? "selected":"").">checkbox</option>
														<option value='select' ".($db->dt[field_type] == "select" ? "selected":"").">select</option>
														<option value='textarea' ".($db->dt[field_type] == "textarea" ? "selected":"").">textarea</option>
													</select>
												</td>
												<td class='list_box_td'  width='170px' align=center><input type='text'  class=textbox name='field_value[".$db->dt[idx]."]' value='".$db->dt[field_value]."'></td>";
							}
							$Contents03 .= "
							<!--
							<td align=center class='list_box_td' >
								<input type='hidden' name='idx[]' value='".$db->dt[idx]."'>
								<input type=hidden name=sno[] value='".$db->dt[idx]."'>
								<input type=hidden name=sort[] value='".$db->dt[vieworder]."'>
								<input type='radio' name='disp[".$db->dt[idx]."]' value='Y' ".($db->dt[disp] == "Y" ? "checked":"").">사용함
								<input type='radio' name='disp[".$db->dt[idx]."]' value='N' ".($db->dt[disp] == "N" ? "checked":"").">사용안함

							</td>
							<td align=center class='list_box_td list_bg_gray' ><input type='radio' name='validation_yn[".$db->dt[idx]."]' value='Y' ".($db->dt[validation_yn] == "Y" ? "checked":"").">사용함 <input type='radio' name='validation_yn[".$db->dt[idx]."]' value='N' ".($db->dt[validation_yn] == "N" ? "checked":"").">사용안함</td>
							-->
							<!-- 필수값 조절 시작 -->
							<td align=center class='list_box_td list_bg_gray' >
								<input type='hidden' name='idx[]' value='".$db->dt[idx]."'>
								<input type=hidden name=sno[] value='".$db->dt[idx]."'>
								<input type=hidden name=sort[] value='".$db->dt[vieworder]."'>";
								if ($db->dt[idx] == "1" || $db->dt[idx] == "2" || $db->dt[idx] == "3" || $db->dt[idx] == "8" ){
										$Contents03 .= "
										<input type='radio' name='disp[".$db->dt[idx]."]' value='Y'".($db->dt[disp] == "Y" ? "checked":"")." >사용함
										<span> 필수 입니다.</span>
										";

								}else{
									$Contents03 .= "
									<input type='radio' name='disp[".$db->dt[idx]."]' value='Y' ".($db->dt[disp] == "Y" ? "checked":"")." id='disp_".$db->dt[idx]."_y'><label for='disp_".$db->dt[idx]."_y'>사용함</label>
									<input type='radio' name='disp[".$db->dt[idx]."]' value='N' ".($db->dt[disp] == "N" ? "checked":"")." id='disp_".$db->dt[idx]."_n'><label for='disp_".$db->dt[idx]."_n'>사용안함</label>
								";
								}
								$Contents03 .= "
							</td>

							<td align=center class='list_box_td' >
							";
							if ($db->dt[idx] == "1" || $db->dt[idx] == "2" || $db->dt[idx] == "3" || $db->dt[idx] == "8" ){
										$Contents03 .= "
										<input type='radio' name='validation_yn[".$db->dt[idx]."]' value='Y' ".($db->dt[validation_yn] == "Y" ? "checked":"").">사용함
										<span> 필수 입니다.</span>
										";

								}else{
									$Contents03 .= "
									<input type='radio' name='validation_yn[".$db->dt[idx]."]' value='Y' ".($db->dt[validation_yn] == "Y" ? "checked":"")." id='validation_".$db->dt[idx]."_y'><label for='validation_".$db->dt[idx]."_y'>사용함</label> <input type='radio' name='validation_yn[".$db->dt[idx]."]' value='N' ".($db->dt[validation_yn] == "N" ? "checked":"")." id='validation_".$db->dt[idx]."_n'><label for='validation_".$db->dt[idx]."_n'>사용안함</label></td>
								";
								}
							$Contents03 .= "
							<!-- 여기까지 -->
						</tr>
						";
	}
	$Contents03 .= "</table>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	$ButtonString .= "<table border=0 cellpadding=0 cellspacing=0 width='100%'><tr height=50 bgcolor=#ffffff><td colspan=8 align=center><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle></td></tr></table>";
}
/*
$ContentsDesc03 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px; line-height:120%' class=small>
		  - 회원 가입 항목을 추가 및 설정 할수 있는 메뉴입니다. 해당 필드를 클릭하시고 위치를 조정 하시면 됩니다.<br>
		  - 필드 추가시 필드타입에 따라 radio, checkbox, select 는 필드값을 설정 하셔야 합니다. 필드값 설정은 | 로 구분 합니다.<br>
	</td>
</tr>
</table>
";
*/
$ContentsDesc03 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'D');


$Contents = "<table width='100%' height='100%'  border=0>";
$Contents = $Contents."<tr><td>".$Contents01."<br></td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr><td>".$Contents03."<br></td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc03."</td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</table >";
$Contents = $Contents."</form>";

//$Contents = "<div style=height:1000px;'></div>";


$Script = "<script language='javascript' src='mall_manage.js'></script>";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->Navigation = "상점관리 > 쇼핑몰 환경설정 > 회원가입설정 ";
$P->title = "회원가입설정 ";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>