<?
include("../class/layout.class");

$db = new Database;
$cdb = new Database;

if($cdb->dbms_type == "oracle"){
	$cdb->query("SELECT * from shop_level where 1 order by level_ix asc limit 0,1");
}else{
	$cdb->query("SELECT * from shop_level where 1 order by level_ix asc limit 0,1");
}

$level_setup = $cdb->fetch();
$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		<col width='15%' />
		<col width='30%' />
		<col width='*' />
	  <tr>
		<td align='left' colspan=3> ".GetTitleNavigation("기초부서관리", "기초관리 > 기초부서관리 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=3 style='padding-bottom:12px;'>
			<div class='tab'>
				<table class='s_org_tab'>
				<tr>
					<td class='tab'>
						<div class='tab'>
							<table class='s_org_tab'>
							<col width='550px'>
							<col width='*'>
							<tr>
								<td class='tab'>
									<table id='tab_01' ".(($info_type == "group" ) ? "class='on' ":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02'  ><a href='./group_detail.php?info_type=group'>&nbsp;&nbsp;회원 그룹 설정&nbsp;&nbsp; </a></td>
										<th class='box_03'></th>
									</tr>
									</table>
							
									<table id='tab_03' ".($info_type == "level" || $info_type == "" ? "class='on' ":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' >";
										$Contents01 .= "<a href='./level.php?info_type=level'>&nbsp;&nbsp;회원 레벨 설정&nbsp;&nbsp; </a>";
					
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
				<td style='width:600px;text-align:right;vertical-align:bottom;padding:0 0 10px 0'>

				</td>
			</tr>
			</table>
		</div>
	    </td>
	</tr>
	</table>";

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=2 style='padding-bottom:0px;'> ".GetTitleNavigation("회원그룹/레벨설정", "회원관리 > 회원그룹/레벨설정 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=2 style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>그룹등급 / 회원 레벨 설정</b></div>")."</td>
	  </tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='search_table_box'>
	<col width='18%'>
	<col width='*'>
	   <tr bgcolor=#ffffff height='100'>
	   <td class='search_box_title'> <b>회원 레벨 관리 <img src='".$required3_path."'></b></td>
		<td  class='search_box_item' colspan='2'> 
			<table width='100%' cellpadding=0 cellspacing=10 border='0' align='center'>
			<tr>
				<td class='search_box_item'>
					<input type=radio name='lv_disp' id='lv_disp_1' value='1' ".($level_setup[lv_disp]=="1" || $level_setup[lv_disp]=="" ? 'checked' : '' )."><label for='lv_disp_1'>사용</label>
					<input type=radio name='lv_disp' id='lv_disp_0' value='0' ".($level_setup[lv_disp]=="0" ? 'checked' : '' )."><label for='lv_disp_0'>미사용</label>
				</td>
			</tr>
			<tr>
				<td class='search_box_item'>
					<span style=''>회원 패널티 관리 : 회원의 주문에서 결제를 바탕으로 고객을 더욱 효율적으로 관리할수 있습니다.</span>
				</td>
			</tr>
			<tr>
				<td class='search_box_item' style='padding-bottom:5px;'>
					<table width='98%' cellpadding=0 cellspacing=0 border='0' align='center' class='search_table_box'>
						<col width=20%>
						<col width=20%>
						<col width=20%>
						<col width=20%>
						<col width=20%>
						<tr>
							<td class='search_box_title'>주문결제</td>
							<td class='search_box_title'>고객취소(입금확인)</td>
							<td class='search_box_title'>교환(고객귀책)</td>
							<td class='search_box_title'>반품(고객귀책)</td>
							<td class='search_box_title'>구매확정</td>
						</tr>
						<tr>
							<td class='search_box_item'>상품 1 * <input type='text' name='order_point' id='order_point' value='".$level_setup[order_point]."' style='width:70px;'></td>
							<td class='search_box_item'>상품 1 * <input type='text' name='member_cancel' id='member_cancel' value='".$level_setup[member_cancel]."' style='width:70px;'></td>
							<td class='search_box_item'>상품 1 * <input type='text' name='member_exchange' id='member_exchange' value='".$level_setup[member_exchange]."' style='width:70px;'></td>
							<td class='search_box_item'>상품 1 * <input type='text' name='member_return' id='member_return' value='".$level_setup[member_return]."' style='width:70px;'></td>
							<td class='search_box_item'>상품 1 * <input type='text' name='order_decide' id='order_decide' value='".$level_setup[order_decide]."' style='width:70px;'></td>
						</tr>
					</table>
				</td>
			</tr>
			</table>
	  </tr>
	  </table>";


$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');

$Contents02 =colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>회원레벨 설정</b></div>");
$Contents02 .= "
<form name='group_frm' action='level.act.php' method='post' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)'>
<input type='hidden' name='act' value='update'>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box' style='margin-top:3px;'>
	  <tr bgcolor=#efefef style='font-weight:bold' align='center' height=30>
	    <td class='s_td' width='60' height=25>코드</td>
		<td class='m_td' width='300' colspan='2'>레벨</td>
	    <td class='m_td' width='400'>레벨점수 설정</td>
	    <td class='e_td' width='240'>사용여부</td>
	  </tr>
	  ";

//오라클은 group by 할때 컬럼을 명시해줘야함
//$db->query("SELECT gi.*,COUNT(md.gp_ix) AS cnt FROM ".TBL_SHOP_GROUPINFO." gi LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." md ON gi.gp_ix=md.gp_ix GROUP BY gi.gp_ix order by gi.gp_level asc ");
if($db->dbms_type == "oracle"){
	$db->query("SELECT * from shop_level where 1 order by level_ix asc");
}else{
	$db->query("SELECT * from shop_level where 1 order by level_ix asc");
}

$level_array = $db->fetchall();

	$Contents02 .= "
		<tr bgcolor=#ffffff align='center' height=30>
			<td class='list_box_td'>".$level_array[0][level_ix]."</td>
			<td rowspan='3' class='list_box_td'  width='150'>일반회원</td>
			<td class='list_box_td'  width='150'>".$level_array[0][lv_name]."</td>
			<td class='list_box_td'><input type='text' name='data[".$level_array[0][level_ix]."][st_point]' id='st_point' value ='".$level_array[0][st_point]."' style='width:100px;'> ~ 
			<input type='text' name='data[".$level_array[0][level_ix]."][ed_point]' id='ed_point' value ='".$level_array[0][ed_point]."' style='width:100px;'></td>
			<td class='list_box_td'>
				<select name='data[".$level_array[0][level_ix]."][disp]' style='width:100px;'>
				<option value='' >--선택--</option>
				<option value='1' ".($level_array[0][disp] == '1' ? "selected":"").">사용</option>
				<option value='0' ".($level_array[0][disp] =='0' ? "selected":"").">미사용</option>
				</select>
			</td>
		</tr>
		<tr bgcolor=#ffffff align='center' height=30>
			<td class='list_box_td'>".$level_array[1][level_ix]."</td>
			<td class='list_box_td'  width='150'>".$level_array[1][lv_name]."</td>
			<td class='list_box_td'><input type='text' name='data[".$level_array[1][level_ix]."][st_point]' id='st_point'  value ='".$level_array[1][st_point]."' style='width:100px;'> ~ 
			<input type='text' name='data[".$level_array[1][level_ix]."][ed_point]' id='ed_point'  value ='".$level_array[1][ed_point]."' style='width:100px;'></td>
			<td class='list_box_td'>
				<select name='data[".$level_array[1][level_ix]."][disp]' style='width:100px;'>
				<option value='' >--선택--</option>
				<option value='1' ".($level_array[1][disp] == '1' ? "selected":"").">사용</option>
				<option value='0' ".($level_array[1][disp] =='0' ? "selected":"").">미사용</option>
				</select>
			</td>
		</tr>
		<tr bgcolor=#ffffff align='center' height=30>
			<td class='list_box_td'>".$level_array[2][level_ix]."</td>
			<td class='list_box_td'  width='150'>".$level_array[2][lv_name]."</td>
			<td class='list_box_td'><input type='text' name='data[".$level_array[2][level_ix]."][st_point]' id='st_point'  value ='".$level_array[2][st_point]."' style='width:100px;'> ~
			<input type='text' name='data[".$level_array[2][level_ix]."][ed_point]' id='ed_point' value ='".$level_array[2][ed_point]."' style='width:100px;'></td>
			<td class='list_box_td'>
				<select name='data[".$level_array[2][level_ix]."][disp]' style='width:100px;'>
				<option value='' >--선택--</option>
				<option value='1' ".($level_array[2][disp] == '1' ? "selected":"").">사용</option>
				<option value='0' ".($level_array[2][disp] =='0' ? "selected":"").">미사용</option>
				</select>
			</td>
		</tr>
		
		<tr bgcolor=#ffffff align='center' height=30>
			<td class='list_box_td'>".$level_array[3][level_ix]."</td>
			<td rowspan='3' class='list_box_td'  width='150'>VIP회원</td>
			<td class='list_box_td'  width='150'>".$level_array[3][lv_name]."</td>
			<td class='list_box_td'><input type='text' name='data[".$level_array[3][level_ix]."][st_point]' id='st_point'  value ='".$level_array[3][st_point]."' style='width:100px;'> ~
			<input type='text' name='data[".$level_array[3][level_ix]."][ed_point]' id='ed_point'  value ='".$level_array[3][ed_point]."' style='width:100px;'></td>
			<td class='list_box_td'>
				<select name='data[".$level_array[3][level_ix]."][disp]' style='width:100px;'>
				<option value='' >--선택--</option>
				<option value='1' ".($level_array[3][disp] == '1' ? "selected":"").">사용</option>
				<option value='0' ".($level_array[3][disp] =='0' ? "selected":"").">미사용</option>
				</select>
			</td>
		</tr>
		<tr bgcolor=#ffffff align='center' height=30>
			<td class='list_box_td'>".$level_array[4][level_ix]."</td>
			<td class='list_box_td'  width='150'>".$level_array[4][lv_name]."</td>
			<td class='list_box_td'><input type='text' name='data[".$level_array[4][level_ix]."][st_point]' id='st_point'  value ='".$level_array[4][st_point]."' style='width:100px;'> ~ 
			<input type='text' name='data[".$level_array[4][level_ix]."][ed_point]' id='ed_point'  value ='".$level_array[4][st_point]."' style='width:100px;'></td>
			<td class='list_box_td'>
				<select name='data[".$level_array[4][level_ix]."][disp]' style='width:100px;'>
				<option value='' >--선택--</option>
				<option value='1' ".($level_array[4][disp] == '1' ? "selected":"").">사용</option>
				<option value='0' ".($level_array[4][disp] =='0' ? "selected":"").">미사용</option>
				</select>
			</td>
		</tr>
		<tr bgcolor=#ffffff align='center' height=30>
			<td class='list_box_td'>".$level_array[5][level_ix]."</td>
			<td class='list_box_td'  width='150'>".$level_array[5][lv_name]."</td>
			<td class='list_box_td'><input type='text' name='data[".$level_array[5][level_ix]."][st_point]' id='st_point'  value ='".$level_array[5][st_point]."' style='width:100px;'> ~ 
			<input type='text' name='data[".$level_array[5][level_ix]."][ed_point]' id='ed_point' value ='".$level_array[5][st_point]."' style='width:100px;'></td>
			<td class='list_box_td'>
				<select name='data[".$level_array[5][level_ix]."][disp]' style='width:100px;'>
				<option value='' >--선택--</option>
				<option value='1' ".($level_array[5][disp] == '1' ? "selected":"").">사용</option>
				<option value='0' ".($level_array[5][disp] =='0' ? "selected":"").">미사용</option>
				</select>
			</td>
		</tr>
		
		<tr bgcolor=#ffffff align='center' height=30>
			<td class='list_box_td'>".$level_array[6][level_ix]."</td>
			<td rowspan='3' class='list_box_td'  width='150'>블랙회원</td>
			<td class='list_box_td'  width='150'>".$level_array[6][lv_name]."</td>
			<td class='list_box_td'><input type='text' name='data[".$level_array[6][level_ix]."][st_point]' id='st_point' value ='".$level_array[6][st_point]."' style='width:100px;'> ~ 
			<input type='text' name='data[".$level_array[6][level_ix]."][ed_point]' id='ed_point' value ='".$level_array[6][st_point]."' style='width:100px;'></td>
			<td class='list_box_td'>
				<select name='data[".$level_array[6][level_ix]."][disp]' style='width:100px;'>
				<option value='' >--선택--</option>
				<option value='1' ".($level_array[6][disp] == '1' ? "selected":"").">사용</option>
				<option value='0' ".($level_array[6][disp] =='0' ? "selected":"").">미사용</option>
				</select>
			</td>
		</tr>
		<tr bgcolor=#ffffff align='center' height=30>
			<td class='list_box_td'>".$level_array[7][level_ix]."</td>
			<td class='list_box_td'  width='150'>".$level_array[7][lv_name]."</td>
			<td class='list_box_td'><input type='text' name='data[".$level_array[7][level_ix]."][st_point]' id='st_point' value ='".$level_array[7][st_point]."' style='width:100px;'> ~
			<input type='text' name='data[".$level_array[7][level_ix]."][ed_point]' id='ed_point' value ='".$level_array[7][st_point]."' style='width:100px;'></td>
			<td class='list_box_td'>
				<select name='data[".$level_array[7][level_ix]."][disp]' style='width:100px;'>
				<option value='' >--선택--</option>
				<option value='1' ".($level_array[7][disp] == '1' ? "selected":"").">사용</option>
				<option value='0' ".($level_array[7][disp] =='0' ? "selected":"").">미사용</option>
				</select>
			</td>
		</tr>
		<tr bgcolor=#ffffff align='center' height=30>
			<td class='list_box_td'>".$level_array[8][level_ix]."</td>
			<td class='list_box_td'  width='150'>".$level_array[8][lv_name]."</td>
			<td class='list_box_td'><input type='text' name='data[".$level_array[8][level_ix]."][st_point]' id='st_point' value ='".$level_array[8][st_point]."' style='width:100px;'> ~ 
			<input type='text' name='data[".$level_array[8][level_ix]."][ed_point]' id='ed_point' value ='".$level_array[8][st_point]."' style='width:100px;'></td>
			<td class='list_box_td'>
				<select name='data[".$level_array[8][level_ix]."][disp]' style='width:100px;'>
				<option value='' >--선택--</option>
				<option value='1' ".($level_array[8][disp] == '1' ? "selected":"").">사용</option>
				<option value='0' ".($level_array[8][disp] =='0' ? "selected":"").">미사용</option>
				</select>
			</td>
		</tr>
		";

$Contents02 .= "
		<!--tr height=1><td colspan=8 ><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small>사용을 원하시는 PG 모듈을 선택해 주세요</span></td></tr-->
		</table>";
$Contents02 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr height=20><td></td></tr>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ><!--img src='../images/".$admininfo["language"]."/b_save.gif' border='0'  style='cursor:pointer;border:0px;' onClick='CheckFormValue(document.group_frm)' /--> </td></tr>
</table>
</form>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ><!--img src='../images/".$admininfo["language"]."/b_save.gif' border='0'  style='cursor:pointer;border:0px;' onClick='CheckFormValue(document.group_frm)' /--> </td></tr>
</table>
";
}else{
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff >
    <td colspan=4 align=center>
        <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>
    </td>
</tr>
</table>
";
}

$Contents = "<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents = $Contents."<form name='group_frm' action='level.act.php' method='post' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)'>
<input name='act' type='hidden' value='all_update'>
<input name='gp_ix' type='hidden' value=''>
<input name='basic' type='hidden' value=''>";
$Contents = $Contents."<tr><td width='100%'>";
$Contents = $Contents.$Contents01;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');


$Contents .= HelpBox("회원레벨설정", $help_text,'100');

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = member_menu();
$P->Navigation = "회원관리 > 회원그룹/레벨설정";
$P->title = "회원그룹관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>