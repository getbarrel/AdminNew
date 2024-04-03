<? 
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.work.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/work/work.lib.php");
//print_r($admininfo);
$db = new Database;
$mdb = new Database;

$db->query("SELECT * FROM work_group where group_ix ='$group_ix' ");
$db->fetch();
$item_total = $db->total;
$group_name = $db->dt[group_name];
$project_sdate = $db->dt[project_sdate];
$project_edate = $db->dt[project_edate];
$pm_charger_ix = $db->dt[pm_charger_ix];
//echo $item_total ;
$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' ".($mmode == "pop" ? "style='display:none;'":"").">
	  <tr >
		<td align='left' colspan=6 style='padding-bottom:0px;'> ".GetTitleNavigation("프로젝트 공정단계 관리", "업무관리 > 프로젝트 공정단계 관리 ")."</td>
	  </tr>
	<tr height=25>
		<td colspan=4 ><img src='/admin/images/dot_org.gif' align=absmiddle> <b>프로젝트 정보 수정</b></td>
	</tr>
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <col width='150px'>
	  <col width='*'>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 프로젝트명 : </td>
		<td class='input_box_item'><input type=text class='textbox' name='group_name' value='".$group_name."' style='width:230px;'> <span class=small></span></td>
	  </tr> 	
	  <tr bgcolor=#ffffff >
		<td class='input_box_title'><b> 시작날짜/종료일 : </b></td>
		<td class='input_box_item' >
				<table cellpadding=0 cellspacing=0>
				<tr>
					<td width=87 style='padding:0px 7px 0px 0px;'>
					<input type='text' name='sdate' class='textbox' value='".$project_sdate."' style='width:80px;text-align:center;' id='start_datepicker'>
					</td>
					<td> ~ </td>
					<td width=87 style='padding:0px 7px 0px 9px;'>
					<input type='text' name='dday' class='textbox' value='".$project_edate."' style='width:80px;text-align:center;' id='end_datepicker'>
					</td>
					<td class=small> 공정단계 설정시 프로젝트 기간이 자동으로 변경됩니다.</td>
				</tr>
				</table>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
		<td class='input_box_title'><b> 담당자 (PM) : </b></td>
		<td class='input_box_item'>
			<table>
			<tr>
				<td>
				".makeDepartmentSelectBox($mdb,"department",$department,"select","부서", "onchange=\"loadWorkUser(this,'charger_ix')\"")."
				".workCompanyUserList($admininfo["company_id"],"pm_charger_ix",$department, $pm_charger_ix," style='width:200px;'")."	
				</td>
				
			</tr>
			</table>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 사용유무 : </td>
	    <td class='input_box_item'>
	    	<input type=radio name='disp' value='1' id='disp_1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' value='0' id='disp_0' ><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr> 	 
	  </table>";


$innerview = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0'  >	  
	  <tr height=25>
		<td colspan=6 style='border-bottom:2px solid #efefef;padding:5px 0px;'><img src='/admin/images/dot_org.gif' align=absmiddle onclick='CheckValue(document.architecture_frm);'> 
		<b>".$group_name." </b> 공정단계 설정 <a onclick=\"javascript:fnAddRow('project_architecture');\"><img src='/admin/image/btn_work_add.gif' border=0 align=absmiddle></a> 
		<!--| <a href=\"javascript:CopyRow();\">테이블 보기</a--> 
		".($mmode != "pop" ? "| <a href=\"?group_ix=".$group_ix."&mmode=pop\" target=_blank><img src='/admin/image/btn_architecture_view.gif' border=0 align=absmiddle></a>":"")."
		<!--input type=text id='test_text'-->
		<input type='checkbox' name='monthly_view' id='monthly_view' onclick=\"ToggleMonthlyView()\" ".($_COOKIE[view_monthly] == 1 ? "checked":"")." ><label for='monthly_view'> 월간 간트차트로 보기</label></td>
		</td>
	  </tr>
	</table>
	<div style='width:100%;overflow-x:hidden;padding:0  ;' >
	
	<table width=100% cellpadding=2 cellspacing=0 border='0' onselectstart='return false;'  style='border-bottom:2px solid silver;margin-bottom:2px;'>
	    <col width='400px'>
	    <col width='135px;'>
		<col width='100px;'>
	    <col width='100px;'>
		<col width='90px;'>
	    <col width='90px;'>";
if($mmode == "pop"){
	  $innerview .= "<col width='*'>";
}
$innerview .= "
	  <!--tr>
		<td><img src='/admin/image/0.gif' width='400px' height=1></td>
		<td><img src='/admin/image/0.gif' width='135px' height=1></td>
		<td><img src='/admin/image/0.gif' width='100x' height=1></td>
		<td><img src='/admin/image/0.gif' width='100x' height=1></td>
		<td><img src='/admin/image/0.gif' width='90px' height=1></td>
		<td><img src='/admin/image/0.gif' width='90px' height=1></td>
		<td></td>
	  </tr-->
	  <tr ".($mmode == "pop" ? "height=48":"height=25")."  bgcolor=#efefef align=center style='font-weight:bold'>
		<td style='width:400px;'> <div >공정단계명</div></td>	    
	    <td style='width:135px;'> <div >담당자</div></td>
		<td style='width:100x;'> <div >시작일</div></td>
	    <td style='width:100px;'> <div >완료기한</div></td>
		<td style='width:90px;'> <div >진행율</div></td>
		<td style='width:90px;'> <div >완료일</div></td>";
if($mmode == "pop"){
	$innerview .= "
	<td style='width:auto;text-align:left;' >";

	
$sql = "select wl.* 
		from work_list wl where company_id ='".$admininfo["company_id"]."' 
		and wl.group_ix = '$group_ix' and depth != '0' order by architecture_code asc  ";

$db->query($sql);
$item_total = $db->total;
//echo $item_total;

	if($_COOKIE["view_monthly"] != 1){
			$project_sdate_num = date("z",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2),substr($project_sdate,0,4)));
			$project_edate_num = date("z",mktime(0,0,0,substr($project_edate,4,2),substr($project_edate,6,2),substr($project_edate,0,4)));
			$first_week_num = date("w",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2),substr($project_sdate,0,4)));
			$project_sdate = date("Ymd",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2)- $first_week_num,substr($project_sdate,0,4)));

			$project_sdate_num = $project_sdate_num - $first_week_num;
			$innerview .= "<table bgcolor=#efefef cellspacing=1 style='table-layout:fixed;width:100%'>";
			//echo $project_sdate_num;
			for($i=0;$i < $project_edate_num-$project_sdate_num;$i++){
				
				$col_str .= "<col width='30px'>";
				$week_num = date("w",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2)+$i,substr($project_sdate,0,4)));
				
				$day = date("d",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2)+$i,substr($project_sdate,0,4)));
				$vdate = date("Ymd",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2)+$i,substr($project_sdate,0,4)));

				$this_day = date("Y년 m월 d일",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2)+$i,substr($project_sdate,0,4)));
				if($vdate == date("Ymd")){
					$add_today_line = "<div style='position:absolute;'><div style='position:relative;z-index:100;color:red;background-color:red;width:2px;height:".($item_total*34+10)."px;'></div></div>";
				}else{
					unset($add_today_line);
				}
				$innerview1 .= "<td width='30px' height=20 align=center title='".$this_day."' class=small>".$week_name[$week_num]."".$day." ".$add_today_line."</td>";

				if($i == 0){
					$innerview2 .= "<td height=20 align=center colspan='".(7-$week_num)."' class=small>".$this_day."</td>";
				}else if($week_num == 0){
					$innerview2 .= "<td height=20 align=center colspan='7' class=small>".$this_day."</td>";
				}
			}
			if($project_edate_num-$project_sdate_num > 28){
				$end_date_num = $project_edate_num-$project_sdate_num + (7-(($project_edate_num-$project_sdate_num)%7));
			}else{
				$end_date_num = 28;
			}
			//echo $end_date_num;
			for($i=$i;$i < $end_date_num;$i++){
				$week_num = date("w",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2)+$i,substr($project_sdate,0,4)));
				$day = date("d",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2)+$i,substr($project_sdate,0,4)));
				$this_day = date("Y년 m월 d일",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2)+$i,substr($project_sdate,0,4)));
				$innerview1 .= "<td width='30' height=20 align=center title='".$this_day."' class=small>".$week_name[$week_num]."".$day."</td>";
				$col_str .= "<col width='30px'>";
				if($i == 0){
					$innerview2 .= "<td height=20 align=center colspan='".(7-$week_num)."' class=small>".$this_day."</td>";
				}else if($week_num == 0){
					$innerview2 .= "<td height=20 align=center colspan='7' class=small>".$this_day."</td>";
				}
			}
			//echo $i%7;
			$innerview .= $col_str;
			$innerview .= "<tr bgcolor=#ffffff>".$innerview2."</tr>";
			$innerview .= "<tr bgcolor=#ffffff>".$innerview1."</tr>";
			$innerview .= "</table>";
	}else{
			$project_sdate_num = date("n",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2),substr($project_sdate,0,4)));
			$project_edate_num = date("n",mktime(0,0,0,substr($project_edate,4,2),substr($project_edate,6,2),substr($project_edate,0,4)));
			$first_week_num = date("w",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2),substr($project_sdate,0,4)));
			$project_sdate = date("Ymd",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2),substr($project_sdate,0,4)));
			
			//$project_sdate_num = $project_sdate_num ;
			$innerview .= "<table bgcolor=#efefef cellspacing=1 border=0 style='table-layout:fixed;'>";
			
			//echo $project_edate;
			
				$end_date_num = $project_edate_num;//-$project_sdate_num + (7-(($project_edate_num-$project_sdate_num)%30));
			//echo $project_sdate_num;
			//echo $end_date_num;
			for($i=$i;$i < ($end_date_num-$project_sdate_num+1);$i++){
				//echo $project_sdate."<br>";
				//$week_num = date("w",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2)+$i,substr($project_sdate,0,4)));
				//$day = date("d",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2)+$i,substr($project_sdate,0,4)));
				$this_day = date("y.m월",mktime(0,0,0,substr($project_sdate,4,2)+$i,substr($project_sdate,6,2),substr($project_sdate,0,4)));
				//$innerview1 .= "<td width='30' height=20 align=center title='".$this_day."' class=small>".$week_name[$week_num]."".$day."</td>";
				$col_str .= "<col width='60px'>";
				if($i == 0){
					$innerview2 .= "<td height=20 align=center style='width:60px;' nowrap><b>".$this_day."</b></td>";
				}else if($week_num == 0){
					$innerview2 .= "<td height=20 align=center  style='width:60px;' nowrap><b>".$this_day."</b></td>";
				}
			}
			//echo $i%7;
			$innerview .= $col_str;
			$innerview .= "<tr bgcolor=#ffffff>".$innerview2."</tr>";
			$innerview .= "</table>";
	}

	$innerview .= "</td>";
}
$innerview .= "
	  </tr>
	 </table>
	 <table width=100% cellpadding=2 cellspacing=0 border='0' id='project_architecture' onselectstart='return false;' class='list_table_box' style='table-layout:fixed;margin-bottom:20px;width:100%;'>	  
		<col width='400px'>
	    <col width='135px;'>
		<col width='100px;'>
	    <col width='100px;'>
		<col width='90px;'>
	    <col width='100px;'>";
if($mmode == "pop"){
$innerview .= "
		<col style='width:auto;'>";
}
if($_COOKIE["view_all_group"] == "1"){
	//$where .= " and (wg.disp = '".$_COOKIE["view_all_group"]."' )  ";
}else{
	$where .= " and (wg.disp = '1' )  ";
}


if($db->total || true){
	$act = "project_update";

	$innerview .= "
		  <tr bgcolor=#ffffff height=30 align=center id='architecture_row_0' class='listTR' depth='1' architecture_row=1 rno='1_' onclick=\"spoit(this)\" ><!--onclick=\"if($(this).css('background-color') == '#ffffff'){ $(this).css('background-color','#efefef'); }else{ $(this).css('background-color','#ffffff'); }\"--><!--onmouseover=\"$(this).find('div[id^=button_area]').css('display','inline');\" onmouseout=\"$(this).find('div[id^=button_area]').css('display','none');\"-->
			<td class='list_box_td point' align='left' style='text-align:left;' id='tdWork_name' onselect='return true;'>
				<span id=rno style='padding-left:10px;width:0px;color:transparent;font-size:0px;'>1_</span>
				<input type='text' class='textbox' name='architecture[0][architecture_name]' id='architecture_name' style='width:170px;' value='' onselectstart='return true;' >
				<div style='display:inline;' id='button_area'>
				<a onclick=\"SubAddRow('project_architecture',$(this).parent().parent().parent())\" class=small id='btn_subrow_add'><img src='/admin/images/orange/ico_sub_add_gray.gif' onmouseover=\"this.src='/admin/images/orange/ico_sub_add.gif'\" onmouseout=\"this.src='/admin/images/orange/ico_sub_add_gray.gif'\" border=0 align=absmiddle title='하위업무추가' style='margin-right:5px;'></a><a onclick=\"$(this).parent().parent().parent().remove();\" class=small><img src='/admin/images/orange/ico_del_gray.gif' border=0 align=absmiddle title='공정업무 삭제' style='margin-right:5px;' onmouseover=\"this.src='/admin/images/orange/ico_del.gif'\" onmouseout=\"this.src='/admin/images/orange/ico_del_gray.gif'\"></a><img src='/admin/images/orange/calendar.gif' border=0 align=absmiddle title='스케줄' style='display:none;margin-right:0px;' id='architecture_is_schedule'><img src='/admin/images/orange/ico_magnifier.gif' border=0 align=absmiddle title='업무상세보기' style='margin-right:0px;cursor:pointer;' id='architecture_view_work'> 
				
				</div>
				<input type='hidden' name='architecture[0][architecture_wl_ix]' id='architecture_wl_ix' class='architecture_wl_ix' value='' style='width:40px;' >				
				<input type='hidden' name='architecture[0][architecture_code]' id='architecture_code' class='architecture_code' value='1.' style='width:70px;' >
				<input type='hidden' name='architecture[0][architecture_depth]' id='architecture_depth' class='architecture_depth' style='width:40px;' value='1' >
				<input type='hidden' name='architecture[0][sub_archietcture_cnt]' id='sub_archietcture_cnt' class='sub_archietcture_cnt' style='width:40px;' value='0' >
			</td>	
		    <td class='list_box_td list_bg_gray' >".projectUserList($admininfo["company_id"],"architecture[0][architecture_charger_ix]", 'architecture_charger_ix', $dp_ix, $charger_ix,"style='width:100px;'")."	<!--input type='hidden' name='architecture[0][architecture_charger_ix]' class='textbox architecture_charger_ix' id='architecture_charger_ix' value='' --></td>
			<td class='list_box_td'><input type='text' name='architecture[0][architecture_sdate]' class='textbox architecture_sdate' id='architecture_sdate' value='' style='width:75px;text-align:center;' ></td>
		    <td class='list_box_td list_bg_gray'><input type='text' name='architecture[0][architecture_edate]' class='textbox architecture_edate' id='architecture_edate' value='' style='width:75px;text-align:center;' ></td>
		    <td class='list_box_td'  id='architecture_status'>-</td>
			<td class='list_box_td list_bg_gray' id='architecture_complete_date'>-</td>";
if($mmode == "pop"){
	$innerview .= "
	<td style='width:auto;text-align:left;' >";
	//$project_sdate_num = date("z",mktime(0,0,0,substr($project_sdate,4,2),substr($project_sdate,6,2),substr($project_sdate,0,4)));
	//$project_edate_num = date("z",mktime(0,0,0,substr($project_edate,4,2),substr($project_edate,6,2),substr($project_edate,0,4)));
	//<!--div style='background:#ff7200;width:1px;height:6px;'></div-->
		$innerview .= "<div style='width:20px;border:1px solid gray;height:6px;cursor:pointer;' id='architecture_scedule_bar' >								
								<div style='background:#ff7200;width:1px;height:6px;'></div>
								<!--table width='100%' cellpadding=0 cellspacing=0 >
									<tr height=8><td bgcolor='#ff7200' id='bar01'></td><td id='bar02'></td></tr>
								</table-->
							</div>";
	$innerview .= "</td>";
}
$innerview .= "
		  </tr>";
/*
	for($i=0;$i < $db->total;$i++){
		
	$db->fetch($i);
	$innerview .= "
		  <tr bgcolor=#ffffff height=30 align=center id='architecture_row_".$i."' class='listTR' depth='".$db->dt[depth]."'>	    
		    <td id='tdSeq'>-</td>
			<td width='*' align='left' id='tdWork_name' style='padding-left:".(20*$db->dt[depth])."' onselect='return false;'>
				<span id=rno>".$db->dt[architecture_code]."</span> 
				<input type='text' name='architecture[".$i."][wl_ix]' id='architecture_wl_ix' class='architecture_wl_ix' value='".$db->dt[wl_ix]."'>
				<input type='hidden' name='architecture[".$i."][architecture_code]' id='architecture_code' class='architecture_code' style='width:40px;' value='".$db->dt[architecture_code]."'>
				<input type='hidden' name='architecture[".$i."][depth]' id='architecture_depth' class='architecture_depth' style='width:40px;' value='".$db->dt[depth]."'>
				<input type='text' class='textbox' name='architecture[".$i."][architecture_name]' id='architecture_name' style='width:220px;' value='".$db->dt[work_title]."'> 
				<a onclick=\"SubAddRow('project_architecture',$(this).parent().parent())\" class=small>하위작업추가</a>
			</td>	
		    <td><input type='text' name='architecture[".$i."][architecture_sdate]' class='textbox architecture_sdate' id='architecture_sdate".$db->dt[architecture_code]."' value='".changeDate($db->dt[sdate],"Y-m-d")."' style='width:80px;text-align:center;' ></td>
		    <td><input type='text' name='architecture[".$i."][architecture_edate]' class='textbox architecture_edate' id='architecture_edate".$db->dt[architecture_code]."' value='".changeDate($db->dt[dday],"Y-m-d")."' style='width:80px;text-align:center;' ></td>
		    <td>-</td>
		  </tr>";
	}
*/
}else{
	$act = "project_update";
	$innerview .= "
		  <tr bgcolor=#ffffff height=30 align=center onclick=\"spoit(this)\"  id='architecture_row_0' class='listTR' depth='1' architecture_row=1>	    
		    
			<td width='*' align='left' id='tdWork_name' onselect='return false;'>
				<span id=rno style='display:none;'>1.</span> 
				<input type='hidden' name='architecture[0][architecture_wl_ix]' id='architecture_wl_ix' class='architecture_wl_ix' style='width:40px;' value=''>
				<input type='hidden' name='architecture[0][architecture_code]' id='architecture_code' class='architecture_code' value='1.'>
				<input type='hidden' name='architecture[0][architecture_depth]' id='architecture_depth' class='architecture_depth' style='width:40px;' value='1'>
				<input type='hidden' name='architecture[0][sub_archietcture_cnt]' id='sub_archietcture_cnt' class='sub_archietcture_cnt' style='width:40px;' value='0' >
				<input type='text' class='textbox' name='architecture[0][architecture_name]' id='architecture_name' style='width:180px;height:20px;' value=''> 
				<a onclick=\"SubAddRow('project_architecture',$(this).parent().parent())\" class=small>추가</a> | <a onclick=\"$(this).parent().parent().remove();\" class=small>삭제</a>
			</td>	
		    <td><input type='text' name='architecture[0][architecture_sdate]' class='textbox architecture_sdate' id='architecture_sdate' value='' style='width:75px;text-align:center;' ></td>
		    <td><input type='text' name='architecture[0][architecture_edate]' class='textbox architecture_edate' id='architecture_edate' value='' style='width:75px;text-align:center;' ></td>
		    <td id='architecture_status'>-</td>
			<td id='architecture_complete_date'>-</td>
		  </tr>";
}

$innerview .= "</table>";
$innerview .= "</div>";


$ButtonString = "
<table cellpadding=5 cellspacing=0 border='0' align='center' >
";
if($db->dt[pm_charger_ix] == $admininfo[charger_ix] || $db->dt[pm_charger_ix] == "" || true){
	$ButtonString .= "<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='/admin/image/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td> <td colspan=4 align=center><img src='/admin/image/b_del.gif' border=0 onclick=\"DeleteProject('".$group_ix."')\" style='cursor:pointer;'></td></tr>";
	}
$ButtonString .= "
</table>
";
	  
$Contents .= "<form name='architecture_frm' action='work_project_architecture.act.php' method='post' onsubmit=\"return CheckArchitectureInfo(this,'project_architecture')\"  target='act'>
<input name='mmode' type='hidden' value='$mmode'>
<input name='act' type='hidden' value='".$act."'>
<input name='group_ix' type='hidden' value='$group_ix'>";	  
$Contents .= "<table width='100%' border=0>";


$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."";
$Contents = $Contents."</td></tr>";

$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td id='result_area'>".$innerview."</td></tr>";
$Contents = $Contents."<tr><td style='text-align:center;' align=center>".$ButtonString."</td></tr>";

//$Contents = $Contents."<tr><td style='padding-top:20px;'><textarea style='width:100%;height:100px;' id='debug_text'></textarea></td></tr>";
$Contents = $Contents."</table >";
$Contents = $Contents."</form>";

$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >프로젝트 공정단계 설정은 <b>4단계</b>까지 가능합니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td ><u>프로젝트 공정단계명 수정</u>을 원하실 경우는 공정단계명을 클릭하시면  수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr>
		<t!--r><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >스케줄 바를 drag 하여 이동하시면 스케줄이 자동으로 변경됩니다. 변경된 스케줄은 저장버튼을 클릭해서 저장 할수 있습니다.</td></tr-->
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >저장된후 스케줄 바를 더블 클릭하시면 해당 업무를 확인하실 수 있습니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >공정단계의 일정은 하위 단계의 일정에 의해서 자동으로 셋팅되게 됩니다. </td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >원하시는 항목을 선택한후 마우스로 위치를 변경 하실수 있습니다.</td></tr>
	</table>
	";

	
$help_text = HelpBox("프로젝트 공정단계 관리", $help_text)."<br><br>";				
$Contents = $Contents.$help_text;	

 $Script = "<script type='text/javascript' src='./js/jquery-ui-1.8.6.custom.min.js'></script>
 <script type='text/javascript' src='./js/ui/ui.core.js'></script>
 <script language='javascript' src='./js/jquery.cookie.js'></script>
 <script type='text/javascript' src='./js/ui/jquery.ui.droppable.js'></script>
 <script language='javascript' src='work.project.js'></script>
 <style>
/* css for timepicker */
#ui-timepicker-div dl{ text-align: left; }
#ui-timepicker-div dl dt{ height: 25px; }
#ui-timepicker-div dl dd{ margin: -25px 0 10px 65px; }
.test
{
    background-color:#efefef;
}


</style>
<link rel='stylesheet' media='all' type='text/css' href='css/jquery-ui-1.8.custom.css' />
<link type='text/css' href='./js/themes/base/ui.all.css' rel='stylesheet' />
<link type='text/css' href='./js/themes/demos.css' rel='stylesheet' />
<script type='text/javascript' src='./js/ui/ui.datepicker.js'></script>
<script type='text/javascript' src='./js/jquery.tablednd_0_5.js'></script>

 <script language='javascript'>
var group_ix = '".$group_ix."';
$(function() {
	$(\"#start_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
    dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
    //showMonthAfterYear:true,
    dateFormat: 'yymmdd',
    buttonImageOnly: true,
    buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		$('#end_datepicker').datepicker('setDate','+0d');
	}

	});

	
	$(\"#end_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
    dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
    //showMonthAfterYear:true,
    dateFormat: 'yymmdd',
    buttonImageOnly: true,
    buttonText: '달력'

	});

	$(\"#architecture_sdate\").datepicker('destroy').datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){
			//alert(dateText);
			$(\"#architecture_edate\").datepicker('setDate','+1d');
		}

   });

   $(\"#architecture_edate\").datepicker('destroy').datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력'

   });

/*
	 $(\".architecture_sdate\").each(function(){
	   //alert($(this).parent().html());
		$(this).datepicker('destroy').datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력'

		});
   });

  
   $(\".architecture_edate\").each(function(){
	   //alert($(this).parent().html());
		$(this).datepicker('destroy').datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력'

		});
   });
*/	

});
</script>";

function getFirstDIV($selected=""){
	global $admininfo;
	$mdb = new Database;
	
	$sql = 	"SELECT bdiv.*
			FROM work_group bdiv 
			where group_depth = 1 and company_id ='".$admininfo["company_id"]."'
			group by group_ix ";
	
	$mdb->query($sql);
	
	$mstring = "<select name='parent_group_ix' id='parent_group_ix' disabled>";
	$mstring .= "<option value=''>1차프로젝트</option>";
	if($mdb->total){
		
		
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[group_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[group_ix]."' selected>".$mdb->dt[group_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[group_ix]."'>".$mdb->dt[group_name]."</option>";
			}
		}
		
	}	
	$mstring .= "</select>";
	
	return $mstring;
}



function work_project_menu($default_path='/admin'){
	global $admininfo, $delivery_type;
	//echo $admininfo[permit];
/*
	$mstring = "
	<table cellpadding=0  cellspacing=0 width=156 border=0>
			<tr><td align=center style='padding-bottom:5px;'><img src='$default_path/images/leftmenu/left_title_delivery.gif'></td></tr>
	</table>";
*/

	$mstring .= "<table cellpadding=0 bgcolor='#c0c0c0' cellspacing=1 width=156 border=0>";
		$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu'><!--IMG id=SM114641I src='".$default_path."/images/dot_org.gif' border=0>&nbsp;--><b >프로젝트 관리</b></td></tr>";


		//$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu' style='padding-left:17px;'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange.gif' border=0>&nbsp;<a href='$default_path/delivery/application.php' class='menu_style1_a'>택배접수</a></td></tr>";
		$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu' style='padding-left:17px;'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange.gif' border=0>&nbsp;<a href='$default_path/delivery/application_list.php?status=AC&delivery_type=$delivery_type' class='menu_style1_a'>프로젝트 공정단계</a></td></tr>";
		$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu' style='padding-left:17px;'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange.gif' border=0>&nbsp;<a href='$default_path/delivery/application_list.php?status=TC&delivery_type=$delivery_type' class='menu_style1_a'>스케줄</a></td></tr>";
		$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu' style='padding-left:17px;'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange.gif' border=0>&nbsp;<a href='$default_path/delivery/application_list.php?status=IC&delivery_type=$delivery_type' class='menu_style1_a'>이슈사항</a></td></tr>";
		$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu' style='padding-left:17px;'><IMG id=SM114641I src='".$default_path."/images/icon/dot_orange.gif' border=0>&nbsp;<a href='$default_path/delivery/group.php' class='menu_style1_a small'>연락처</a></td></tr>";
		$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu'><!--IMG id=SM114641I src='".$default_path."/images/dot_org.gif' border=0>&nbsp;--><a href='$default_path/delivery/application.php' class='menu_style1_a'><b >디버그 게시판</b></a></td></tr>";
		$mstring .= "<tr height=20 bgcolor='#E1F0F6'><td align=left class='leftmenu'><!--IMG id=SM114641I src='".$default_path."/images/dot_org.gif' border=0>&nbsp;--><a href='$default_path/delivery/application.php' class='menu_style1_a'><b >관련 자료</b></a></td></tr>";
		$mstring .= "</table><br>";



	return $mstring;

}

/*



CREATE TABLE `shop_address_group` (
  `group_ix` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `parent_group_ix` int(4) unsigned DEFAULT NULL,
  `group_name` varchar(20) DEFAULT NULL,
  `group_depth` int(2) unsigned DEFAULT '1',
  `disp` char(1) DEFAULT '1',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`group_ix`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8


*/
?>