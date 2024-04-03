<?
include("../class/layout.class");
include("../basic/company.lib.php");
$db = new Database;
$mdb = new Database;


if($info_type == ""){
$info_type = 'basic';
}

$sql = "SELECT COUNT(*) as total
				FROM ".TBL_SHOP_COMPANY_GROUP."
				where  1";

$db->query($sql);
$db->fetch();
$vendor_total = $db->dt[total];
if(!$info_type){
	$act = "insert";
}

if($admininfo[admin_level] == 9){
	if($info_type == "basic" || $info_type == ""){

		if($group_ix){
			$sql = "SELECT *
				FROM ".TBL_SHOP_COMPANY_GROUP." 
				where
					group_ix = '".$group_ix."' order by group_ix DESC";

			$db->query($sql);
			$data_cnt = $db->fetch();
			if(count($data_cnt) > 0){
				$act = "update";
			}

			$group_name = $db->dt[group_name];
			$seq	= $db->dt[seq];
			$disp	= $db->dt[disp];
			
		}else if($dp_ix){

			$sql = "SELECT *
			FROM ".TBL_SHOP_COMPANY_DEPARTMENT." 
			where
				dp_ix = '".$dp_ix."' order by dp_ix DESC";


			$db->query($sql);
			$data_cnt = $db->fetch();
			if(count($data_cnt) > 0){
				$act = "update";
			}
		
			$group_name = $db->dt[dp_name];
			$seq	= $db->dt[seq];
			$disp	= $db->dt[disp];
	
		}else{
			$act = "insert";
		}

	}else if($info_type == "post_info"){
		
		if($ps_ix){
			$sql = "
					select
						*
					from
						".TBL_SHOP_COMPANY_POSITION."
					where
						ps_ix = '".$ps_ix."'
			";
			$db->query($sql);
			$ps_cnt = $db->fetch();
			
			if(count($ps_cnt) > 0){
				$act = "update";
			}else{
				$act = "insert";
			}
		}else{
			$act = "insert";
		}
			
	}else if($info_type == "position_info"){
		if($cu_ix){
			$sql = "
					select
						*
					from
						".TBL_SHOP_COMPANY_DUTY."
					where
						cu_ix = '".$cu_ix."'
			";
			$db->query($sql);
			$cu_cnt = $db->fetch();
			
			if(count($cu_cnt) > 0){
				$act = "update";
			}else{
				$act = "insert";
			}
		}else{
			$act = "insert";
		}
	}
	//$act = "update";
	
}else if($admininfo[admin_level] == 8){

		$company_id = $admininfo[company_id];

		if($info_type == "basic"){
			$sql = "SELECT * FROM common_user cu , common_company_detail ccd where  cu.company_id = ccd.company_id and ccd.company_id = '".$company_id."'";
		}else if($info_type == "seller_info"){
			$sql = "SELECT * FROM common_company_detail csd where csd.company_id = '".$company_id."'";
		}else if($info_type == "delivery_info"){
			$sql = "SELECT * FROM common_company_detail csd where csd.company_id = '".$company_id."'";
		}

		$db->query($sql);
		$db->fetch();
		$act = "update";

}

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		<col width='20%' />
		<col width='80%' />
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
									<table id='tab_01'>
									<tr>
										<th class='box_01'></th>
										<td class='box_02'  ><a href='admin_manage_list.php'>관리자목록</a></td>
										<th class='box_03'></th>
									</tr>
									</table>
									<table id='tab_02' >
									<tr>
										<th class='box_01'></th>
										<td class='box_02'  ><a href='admin_manage.php'>관리자등록</a></td>
										<th class='box_03'></th>
									</tr>
									</table>
									<table id='tab_03' ".(($info_type == "basic" ) ? "class='on' ":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02'  ><a href='./department.add.php?info_type=basic&company_id=".$company_id."&mmode=$mmode'>&nbsp;&nbsp;본부/부서&nbsp;&nbsp; </a></td>
										<th class='box_03'></th>
									</tr>
									</table>
							
									<table id='tab_04' ".($info_type == "post_info" ? "class='on' ":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' >";
										if($company_id == ""){
											$Contents01 .= "<a href='./department.add.php?info_type=post_info&company_id=".$company_id."&mmode=$mmode'>&nbsp;&nbsp;직위&nbsp;&nbsp; </a>";
										}else{
											$Contents01 .= "<a href='./department.add.php?info_type=post_info&company_id=".$company_id."&mmode=$mmode'>&nbsp;&nbsp;직위&nbsp;&nbsp; </a>";
										}
										$Contents01 .= "
										</td>
										<th class='box_03'></th>
									</tr>
									</table>
									<table id='tab_05' ".($info_type == "position_info" ? "class='on' ":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' >";
										if($company_id == ""){
											$Contents01 .= "<a href='./department.add.php?info_type=position_info&company_id=".$company_id."&mmode=$mmode'>&nbsp;&nbsp;직책&nbsp;&nbsp; </a>";
										}else{
											$Contents01 .= "<a href='./department.add.php?info_type=position_info&company_id=".$company_id."&mmode=$mmode'>&nbsp;&nbsp;직책&nbsp;&nbsp; </a>";
										}
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
	</tr>";

if($info_type == "basic" || $info_type == ""){
	if($group_ix){
		$checked	 = "checked";
	}else if($dp_ix){
		$checked_1	 = "checked";
	}else if($info_type == "basic" || $group_ix == ""){
	$checked	 = "checked";
	}

//	echo "$info_type"."$checked";
$Contents01 .= "
		<input type='hidden' name= 'group_ix' value='".$group_ix."'>
		<input type='hidden' name= 'dp_ix' value='".$dp_ix."'>
		<input type='hidden' name= 'group_type' value=''>
	<tr>
	    <td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>부서그룹 설정</b></div>")."</td>
	</tr>
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	<tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>부서그룹관리 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'  colspan='3'>
		<input type='radio' id='com_group' name='company_group' value='C' ".$checked."> &nbsp;<label for='com_group'>본부</label>
		&nbsp;
		<input type='radio' id='company_department' name='company_group' value='D' ".$checked_1."> &nbsp;<label for='company_department'>부서</label>&nbsp;&nbsp;
				".getgroup($dp_ix)."
		</td>
	</tr>
	<!--
	<tr bgcolor=#ffffff >
	    <td class='input_box_title'> 근무사업장 </td>
	    <td class='input_box_item' colspan='3'>
			<table border=0 cellpadding=0 cellspacing=0 id='company_list' disabled>
				<tr>
					<td style='padding-right:5px;'>
					".getCompanyList("본사", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='본사' ", '5', $cid2)."</td>
					<td style='padding-right:5px;'>
					".getCompanyList("본사", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='본사'", '15', $cid2)."</td>
					<td style='padding-right:5px;'>
					".getCompanyList("본사", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='본사'", '25', $cid2)."</td>
					<td>".getCompanyList("본사", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='본사'", '35', $cid2)."</td>
				</tr>
			</table>
	    </td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>부서그룹코드 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='group_code' value='".$db->dt[group_code]."'  style='width:200px' validation='true' title='부서그룹코드' >
		</td>
		<td class='input_box_title'> <b>부서 그룹명</b></td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='group_name' value='".$db->dt[group_name]."'  style='width:200px'>
		</td>
	</tr>-->
	<tr>
		<td class='input_box_title'> <b>부서 그룹명  <img src='".$required3_path."'> </b></td>
		<td class='input_box_item' colspan='3'>
			<input type=text class='textbox' name='group_name' value='".$group_name."' validation='true'  style='width:200px'>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>노출순서  <img src='".$required3_path."'> </b> </td>
	    <td class='input_box_item' colspan='3'><input type=text class='textbox' name='seq' value='".$seq."' style='width:60px;' validation='true' title='노출순서'>
		노출순서에 의해서 셀렉트 박스의 및 리스트 노출 순서가 정해집니다.
	    </td>
	</tr>
	<tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>사용유무 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item' colspan='3'>
	    	<input type=radio name='disp' id='disp_1' value='1' ".($disp == "1" || $disp == "" ? "checked":"")."><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ".($disp == "0" ? "checked":"")."><label for='disp_0'>미사용</label>
	    </td>
	</tr>
</table>";

}


if($info_type == "post_info"){
$Contents01 .= "
		<input type='hidden' name='ps_ix' value='".$ps_ix."'>
		<input type='hidden' name='group_type' value=''>
	<tr>
	    <td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>직위 설정</b></div>")."</td>
	</tr>
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
		<tr>
			<td class='input_box_title'> <b>직위명  <img src='".$required3_path."'> </b></td>
			<td class='input_box_item' colspan='3'>
				<input type=text class='textbox' name='ps_name' value='".$db->dt[ps_name]."' validation='true' style='width:200px'  title='직위명'>
			</td>
		</tr>
	<tr bgcolor=#ffffff >
			<td class='input_box_title'> <b>노출순서 <img src='".$required3_path."'> </b> </td>
			<td class='input_box_item' colspan='3'><input type=text class='textbox' name='seq' value='".$db->dt[seq]."' style='width:60px;' validation='true' title='노출순서'>
			&nbsp;&nbsp;&nbsp;&nbsp;노출순서에 의해서 셀렉트 박스의 및 리스트 노출 순서가 정해집니다.
			</td>
	</tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>사용유무  <img src='".$required3_path."'> </b> </td>
	    <td class='input_box_item' colspan='3'>
	    	<input type=radio name='disp' id='disp_1' value='1' ".($disp == "1" || $disp == "" ? "checked":"")."><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ".($disp == "0" ? "checked":"")."><label for='disp_0'>미사용</label>
	    </td>
	</tr>
</table>";
}

if($info_type == "position_info"){
$Contents01 .= "
		<input type='hidden' name='cu_ix' value='".$cu_ix."'>
		<input type='hidden' name='group_type' value=''>
	<tr>
	    <td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>직책 설정</b></div>")."</td>
	</tr>
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
		<tr>
			<td class='input_box_title'> <b>직책명 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' name='duty_name' value='".$db->dt[duty_name]."' validation='true' style='width:200px'  title='직책명'>
			</td>
		</tr>
	<tr bgcolor=#ffffff >
			<td class='input_box_title'> <b>노출순서 <img src='".$required3_path."'> </b> </td>
			<td class='input_box_item' colspan='3'><input type=text class='textbox' name='seq' value='".$db->dt[seq]."' style='width:60px;' validation='true' title='노출순서'>
			&nbsp;&nbsp;&nbsp;&nbsp;노출순서에 의해서 셀렉트 박스의 및 리스트 노출 순서가 정해집니다.
			</td>
	</tr>
	<tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>사용유무  <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item' colspan='3'>
	    	<input type=radio name='disp' id='disp_1' value='1' ".($disp == "1"  || $disp == "" ? "checked":"")."><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ".($disp == "0" ? "checked":"")."><label for='disp_0'>미사용</label>
	    </td>
	</tr>
</table>";
}

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
    $ButtonString = "
    <table width='100%' cellpadding=0 cellspacing=0 border='0'>
        <tr bgcolor=#ffffff ><td align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
    </table>
";
}

$Contents01 .= "
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
	<tr>
		<td height='30'></td></tr>
	</table>
";
if($info_type == "basic" || $info_type == ""){

$ContentsDesc01 .= "
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
  <tr height='28' bgcolor='#ffffff'>
    <td width='3%' align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.listform)'></td>
    <td width='5%' align='center' class='m_td'><font color='#000000'><b>등록일</b></font></td>
    <td width='5%' align='center' class='m_td'><font color='#000000'><b>사업장</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>본부명</b></font></td>
    <td width='6%' align='center' class=m_td><font color='#000000'><b>노출순위</b></font></td>
    <td width='5%' align='center' class=m_td><font color='#000000'><b>사용유무</b></font></td>
    <td width='10%' align='center' class=e_td><font color='#000000'><b>관리</b></font></td>
  </tr>";

	$sql = "SELECT *
					FROM ".TBL_SHOP_COMPANY_GROUP."
					where  1 order by seq ASC";
	$db->query($sql);
	$data_array = $db->fetchall();
	//echo "<pre>";
	///print_r ($data_array);
	for ($i = 0; $i < count($data_array); $i++)
	{
		if($data_array[$i][disp] == "1"){
			$disp = "사용";
		}else{
			$disp = "사용 안함";
		}
		if($data_array[$i][company_group] == "C"){
			$company_group = "본부";
		}else if($data_array[$i][company_group] == "D"){
			$company_group = "부서";
			
		}
		$no = count($data_array) - ($page - 1) * $max - $i;

        $ContentsDesc01 = $ContentsDesc01."
          <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
            <td class='list_box_td'><input type=checkbox name=code[group][] id='code' value='".$data_array[$i][group_ix]."'></td>
            <td class='list_box_td' >".$data_array[$i][reg_date]."</td>
            <td class='list_box_td' style='padding:0px 5px;'><span title='".$data_array[$i][com_name]."'>".$company_group."</span></td>
      
			<td class='list_box_td' >
				<table cellpadding='0' cellspacing='0' border='0' width='100%'>
					<tr>
						
						<td width='*' align='left'>".$data_array[$i][group_name]."</td>
					</tr>
				</table>
			</td>	
			<td class='list_box_td' >".$data_array[$i][seq]."</td>
            <td class='list_box_td ctr point' >".$disp."</a></td>
            <td class='list_box_td ctr'  style='padding:5px;' nowrap>";

			 if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
		    	$ContentsDesc01 .= "
                <a href='?info_type=basic&group_ix=".$data_array[$i][group_ix]."&mmode=$mmode'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>";
            }else{
                $ContentsDesc01 .= "
                <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
                ";   
            }
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
                $ContentsDesc01 .= "
	    		<a href=\"javascript:deleteGroupInfo('delete','".$data_array[$i][group_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
                ";
            }
            $ContentsDesc01 .= "
    </td>
  </tr>";

			$sql = "select *
					from ".TBL_SHOP_COMPANY_DEPARTMENT."
					where
						group_ix = '".$data_array[$i][group_ix]."'
						order by seq ASC
					";
			$db->query($sql);
			$department_array = $db->fetchall();

			for($j = 0; $j<count($department_array); $j++){
	
				if($department_array[$j][company_group] == "C"){
					$company_group = "본부";
				}else if($department_array[$j][company_group] == "D"){
					$company_group = "부서";
					$space = "&nbsp;&nbsp;&nbsp;";
				}
				if($department_array[$j][disp] == "1"){
					$disp = "사용";
				}else{
					$disp = "사용 안함";
				}

										
				$ContentsDesc01 = $ContentsDesc01."
				  <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
					<td class='list_box_td'><input type=checkbox name=code[department][] id='code' value='".$department_array[$j][dp_ix]."'></td>
					<td class='list_box_td' >".$department_array[$j][regdate]."</td>
					<td class='list_box_td' style='padding:0px 5px;'><span title='".$department_array[$j][com_name]."'>".$company_group."</span></td>
					<td class='list_box_td' >
						<table cellpadding='0' cellspacing='0' border='0' width='100%'>
							<tr>
								
								<td width='*' align='left'>".$space." ".$department_array[$j][dp_name]."</td>
							</tr>
						</table>
					</td>	
					<td class='list_box_td' >".$department_array[$j][seq]."</td>
					<td class='list_box_td ctr point' >".$disp."</a></td>
					<td class='list_box_td ctr'  style='padding:5px;' nowrap>";

					 if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
						$ContentsDesc01 .= "
						<a href='?info_type=basic&dp_ix=".$department_array[$j][dp_ix]."&mmode=$mmode'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 id='depart_update'></a>";
					}else{
						$ContentsDesc01 .= "
						<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
						";   
					}
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
						$ContentsDesc01 .= "
						<a href=\"javascript:deleteDepartmentInfo('delete','".$department_array[$j][dp_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
						";
					}
					$ContentsDesc01 .= "
					</td>
				  </tr>";
			
			}
	}

if (!$db->total){

$ContentsDesc01 = $ContentsDesc01."
  <tr height=50>
    <td colspan='7' align='center'>등록된 회원 데이타가 없습니다.</td>
  </tr>
  ";
}
$ContentsDesc01 = $ContentsDesc01."</table>";
}



if($info_type == "post_info"){

			$sql = "
					select
						*
					from
						".TBL_SHOP_COMPANY_POSITION."
					where
						1 order by ps_ix ASC
			";
			$db->query($sql);
			$position_array = $db->fetchall();
//$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');
		
$ContentsDesc01 .= "
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
  <tr height='28' bgcolor='#ffffff'>
    <td width='3%' align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.listform)'></td>
    <td width='8%' align='center' class='m_td'><font color='#000000'><b>등록일</b></font></td>
    <td width='6%' align='center' class=m_td><font color='#000000'><b>직위명</b></font></td>
    <td width='5%' align='center' class=m_td><font color='#000000'><b>노출순위</b></font></td>
    <td width='5%' align='center' class=m_td><font color='#000000'><b>사용유무</b></font></td>
    <td width='10%' align='center' class=e_td><font color='#000000'><b>관리</b></font></td>
  </tr>";

	for ($i = 0; $i < count($position_array); $i++)
	{
		if($position_array[$i][disp] == "1"){
			$disp = "사용";
		}else{
			$disp = "사용 안함";
		}

		
	/*
		if ($db->dt[mem_level] == "E")	{ $perm = "탈퇴회원"; }
		if ($db->dt[mem_level] == "M")	{ $perm = "일반회원"; }
		if ($db->dt[mem_level] == "B")	{ $perm = "입점업체"; }
		if ($db->dt[mem_level] == "C")	{ $perm = "특별회원"; }
	*/

        $ContentsDesc01 = $ContentsDesc01."
          <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
            <td class='list_box_td'><input type=checkbox name=code[position][] id='code' value='".$position_array[$i][ps_ix]."'></td>
            <td class='list_box_td' >".$position_array[$i][regdate]."</td>
			<td class='list_box_td' >".$position_array[$i][ps_name]."</td>
			<td class='list_box_td' >".$position_array[$i][seq]."</td>
            <td class='list_box_td ctr point' >".$disp."</a></td>
            <td class='list_box_td ctr'  style='padding:5px;' nowrap>";
			 if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$ContentsDesc01 .= "
					<a href='?info_type=post_info&ps_ix=".$position_array[$i][ps_ix]."&mmode=$mmode'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 id='depart_update'></a>";
				}else{
					$ContentsDesc01 .= "
					<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
					";
				}
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
					$ContentsDesc01 .= "
					<a href=\"javascript:deletePositionInfo('delete','".$position_array[$i][ps_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
					";
				}
            $ContentsDesc01 .= "
    </td>
  </tr>";

	}

if (!$db->total){

$ContentsDesc01 = $ContentsDesc01."
  <tr height=50>
    <td colspan='8' align='center'>등록된 회원 데이타가 없습니다.</td>
  </tr>
  ";
}
$ContentsDesc01 = $ContentsDesc01."</table>";

}


if($info_type == "position_info"){
	
			$sql = "
					select
						*
					from
						".TBL_SHOP_COMPANY_DUTY."
					where
						1 order by cu_ix ASC
			";
			$db->query($sql);

			$duty_array = $db->fetchall();
	
$ContentsDesc01 .= "
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
  <tr height='28' bgcolor='#ffffff'>
    <td width='3%' align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.listform)'></td>
    <td width='8%' align='center' class='m_td'><font color='#000000'><b>등록일</b></font></td>
    <td width='6%' align='center' class=m_td><font color='#000000'><b>직책명</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>노출순위</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>사용유무</b></font></td>
    <td width='10%' align='center' class=e_td><font color='#000000'><b>관리</b></font></td>
  </tr>";

	for ($i = 0; $i < count($duty_array); $i++)
	{
	
		if($duty_array[$i][disp] == "1"){
			$disp = "사용";
		}else{
			$disp = "사용 안함";
		}
        $ContentsDesc01 = $ContentsDesc01."
          <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
            <td class='list_box_td'><input type=checkbox name='code[duty][]' id='code' value='".$duty_array[$i][cu_ix]."'></td>
            <td class='list_box_td' >".$duty_array[$i][reg_date]."</td>
			<td class='list_box_td' >".$duty_array[$i][duty_name]."</td>
			<td class='list_box_td' >".$duty_array[$i][seq]."</td>
            <td class='list_box_td ctr point' >".$disp."</a></td>
            <td class='list_box_td ctr'  style='padding:5px;' nowrap>";
			 if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$ContentsDesc01 .= "
					<a href='?info_type=position_info&cu_ix=".$duty_array[$i][cu_ix]."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 id='depart_update'></a>";
				}else{
					$ContentsDesc01 .= "
					<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
					";
				}
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
					$ContentsDesc01 .= "
					<a href=\"javascript:deleteDutyInfo('delete','".$duty_array[$i][cu_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
					";
				}
            $ContentsDesc01 .= "
    </td>
  </tr>";

	}

if (!count($duty_array) > 0){

$ContentsDesc01 = $ContentsDesc01."
  <tr height=50>
    <td colspan='6' align='center'>등록된 회원 데이타가 없습니다.</td>
  </tr>
  ";
}
$ContentsDesc01 = $ContentsDesc01."</table>";
}

$select = "
	<select name='update_type' >
		<option value='0'>방식 선택</option>
		<option value='2' selected>선택한 상품 전체에</option>
		<option value='1' >검색한 상품 전체에</option>
	</select>

	<input type='radio' name='update_kind' id='update_kind_pos' value='pos'>
	<label for='update_kind_pos'>사용 유무</label>
				";

$help_text .= "
<div id='batch_update_pos' ".($update_kind == "pos" || $update_kind == "" ? "style='display:block'":"style='display:none'")." >
<div style='padding:4px 0px 4px 0px'><img src='../images/dot_org.gif'> <b>본부/부서, 직위,직책 사용유무</b> <span class=small style='color:gray'>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'P')."</span></div>
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width=160>
	<col width='*'>
		<tr height=30>
		<td class='input_box_title'> <b>사용 유무 </b></td>
		<td class='input_box_item'>
		<input type='radio' name='use_disp' value='1' id='use_disp_1' checked><label for='use_disp_1'> 사용</label> <input type='radio' id='use_disp_0' name='use_disp' value='0' > <label for='use_disp_0'> 미 사용</label> 
		</td>
	</tr>
	</table>";

if( checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$help_text .= "
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50><td colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
	</table></div>";
}


$select_contents .= "".HelpBox($select, $help_text,600)."";

$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='department_frm' action='../basic/department.act.php?url=store' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data'>
<input name='act' type='hidden' value='$act'>
<input name='dp_ix' type='hidden' value=''>
<input type='hidden' name='cid2' value='$cid2'>
<input type='hidden' name='depth' value='$depth'>
<input name='info_type' type='hidden' value='$info_type'>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."</form>";

$Contents = $Contents."<form name='listform' action='department.act.php' method='post' onsubmit='return SelectUpdate2(this)' enctype='multipart/form-data' target='iframe_act'>
	<input type='hidden' name='search_searialize_value' value='".($_GET[mode] == "search" ? urlencode(serialize($_GET)):"")."'>
	<input name='act' type='hidden' value='select_update'>
	<input name='info_type' type='hidden' value='$info_type'>
";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr><td>".$select_contents."<br></td></tr>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</form>";

$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."</table >";

/*target='iframe_act'
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원 부서정보는 위와 같이 9단계의 등급으로 이루어져 있으며 '부서등급' 은 수정이 불가능하며 각 등급에 맞는 명칭을 변경해서 사용하셔야 합니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사용하지 않으실 부서정보는 수정버튼을 클릭하신후 사용하지 않음으로 선택하신후 저장 버튼을 누르시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사용유무가 사용으로 되어 있는 부서만 사용하실수 있게 됩니다</td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

//$Contents .= HelpBox("부서관리", $help_text, 60);

 $Script = "
 <script language='javascript'>
function clearAll(frm){
			for(i=0;i < frm.code.length;i++){
					frm.code[i].checked = false;
			}  
	}
function checkAll(frm){

	for(i=0;i < frm.code.length ;i++){
				frm.code[i].checked = true;
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

function deleteGroupInfo(act, group_ix){
 	if(confirm('해당 정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.department_frm;
 		frm.act.value = act;
 		frm.group_ix.value = group_ix;
		frm.group_type.value = 'C';
 		frm.submit();
 	}
}

function deleteDepartmentInfo(act, dp_ix){
 	if(confirm('해당 정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.department_frm;
 		frm.act.value = act;
 		frm.group_ix.value = dp_ix;
		frm.group_type.value = 'D';
 		frm.submit();
 	}
}

function deletePositionInfo(act, ps_ix){
 	if(confirm('해당 정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.department_frm;
 		frm.act.value = act;
 		frm.ps_ix.value = ps_ix;
		frm.group_type.value = 'P';
 		frm.submit();
 	}
}

function deleteDutyInfo(act, cu_ix){
 	if(confirm('해당 정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.department_frm;
 		frm.act.value = act;
 		frm.cu_ix.value = cu_ix;
		frm.group_type.value = 'T';
 		frm.submit();
 	}
}

function ChangeUpdateForm(selected_id){
	var area = new Array('batch_update_display','batch_update_category','batch_update_reserve','batch_update_pos','batch_update_whole'); //,'batch_update_sms','batch_update_coupon'

	for(var i=0; i<area.length; ++i){
		if(area[i]==selected_id){
			document.getElementById(selected_id).style.display = 'block';
			$.cookie('goodsinfo_update_kind', selected_id.replace('batch_update_',''), {expires:1,domain:document.domain, path:'/', secure:0});
		}else{
			document.getElementById(area[i]).style.display = 'none';
		}
	}
}

function SelectUpdate2(frm){

	var pid_checked_bool = false;
	var pid_obj=document.getElementsById('code[]');//kbk

	for(i=0;i < pid_obj.length;i++){
		if(pid_obj[i].checked){
			pid_checked_bool = true;
		}
	}
	if(!pid_checked_bool){
		alert(language_data['product_list.js']['F'][language]);//'수정하실 정보를 한개이상 선택하셔야 합니다.'
		return false;
	}
	frm.submit();
}

$(document).ready(function(){

$('#depart_update').click(function(){
	$('#company_department').attr('checked',true);
	$('#department_group').attr('disabled',false);
});

$('#com_group').click(function(){
	$('#department_group').attr('disabled',true);
});

$('#company_department').click(function(){
	$('#department_group').attr('disabled',false);
});


});
 </script>
 <script language='javascript' src='company.add.js'></script>
 ";

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->Navigation = "상점관리 > 기초관리 > 기초부서관리";
$P->title = "상점관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>