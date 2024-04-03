<?
$work_status = array("WR"=>"작업대기","AR"=>"선작업대기중","WI"=>"진행중","WC"=>"작업완료","IS"=>"이슈중","WH"=>"작업보류","WD"=>"업무취소");
$work_complet_rate = array("0"=>"0%","25"=>"25%","50"=>"50%","75"=>"75%","100"=>"100%");

function footMenu(){
	global $admininfo;
if($admininfo[master] == "Y"){
	$footmenu_str = "<a href=\"javascript:LayerShow('operation_config_box')\"><img src='../images/icon/config1.gif'> 운영설정</a> |";
}
$footmenu_str .= " <a href=\"javascript:LayerShow('input_box')\"><img src='../images/icon/config1.gif'> 환경설정</a> <span ".(is_array($admininfo["work_confs"]["config_bbs"]) ? (in_array( "notice", $admininfo["work_confs"]["config_bbs"]) ? "":"style='display:none'"):"")."> | <a href='bbs.php' ><img src='../images/icon/notice.gif'> 공지사항</a> </span> <span ".(is_array($admininfo["work_confs"]["config_bbs"]) ? (in_array( "data", $admininfo["work_confs"]["config_bbs"]) ? "":"style='display:none'"):"")."> | <a href='data.php'><img src='../images/icon/dataroom.gif'> 양식자료실</a></span> <span ".(is_array($admininfo["work_confs"]["config_bbs"]) ? (in_array( "story", $admininfo["work_confs"]["config_bbs"]) ? "":"style='display:none'"):"").">  | <a href='freebbs.php'><img src='../images/icon/ourstory.gif'> 우리들 이야기</a></span> ";

	return $footmenu_str;
}



function footAddContents(){
	global $admininfo;
$mstring .= "

<div id='kms_category_box' style='display:none;vertical-align:top;'>
<table class='layer_box' border=0 cellpadding=0 cellspacing=0 style='width:800px;height:0px;display:block;' >
	<col width='11px'>
	<col width='*'>
	<col width='11px'>
	<tr>
		<th class='box_01'></th>
		<td class='box_02' ></td>
		<th class='box_03'></th>
	</tr>
	
	<tr>
		<th class='box_04' style='vertical-align:top'></th>
		<td class='box_05' rowspan=2 valign=top style='padding:5px 15px 5px 15px;font-weight:bold;font-size:12px;line-height:150%;text-align:left;background-color:#ffffff' >	
			<h1 id=\"check_title\">".GetTitleNavigation("지식분류 관리", "지식 관리 > 지식분류 관리 ", false)."</h1>
			<form name='deepzoom_frm' method=post enctype='multipart/form-data' action='work_config.act.php' onsubmit='return CheckFormValue(this)' target='act' style='display:inline;'><!-- target='act' -->
			<input type='hidden' name='act' value='op_update'>
			<div class=\"g_box2 \" >				
				<iframe name='act' id='act' width=800 height=400 frameborder=0 src='category.php'></iframe>
			</div>
			
			
			<p class=\"btns \" style='text-align:center;padding:10px 0px '>
				<input type='image' src='../image/b_save.gif' border=0 style='cursor:hand;border:0px;' align=absmiddle>
				<a id=\"btnCheck_cancel\" href=\"javascript:LayerClose()\"><img src='../image/b_cancel.gif' border=0 align=absmiddle></a>
			</p>
			</form>
		</td>
		<th class='box_06'></th>
	</tr>
	<tr>
		<th class='box_04'></th>
		<th class='box_06'></th>
	</tr>
	<tr>
		<th class='box_07'></th>
		<td class='box_08'></td>
		<th class='box_09'></th>
	</tr>
</table>
</div>";

	return $mstring;
}


function getCampaignGroupInfoSelect($obj_id, $obj_txt, $parent_group_ix, $selected, $return_type="select", $depth=1, $property=""){

	$mdb = new Database;
	
	if($depth == 1){
		$sql = 	"SELECT abg.*
				FROM work_group abg 
				where group_depth = '$depth'
				group by group_ix ";
	}else if($depth == 2){
		$sql = 	"SELECT abg.*
				FROM work_group abg 
				where group_depth = '$depth' and parent_group_ix = '$parent_group_ix'
				group by group_ix ";
	}
	//echo $sql;
	$mdb->query($sql);
	
	if($return_type == "select"){
		$mstring = "<select name='$obj_id' id='$obj_id' $property>";
		$mstring .= "<option value=''>$obj_txt</option>";
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
	}else{
		$datas = $mdb->fetchall();
		return $datas;
	}
	
	
}

function workCompanyList($company_id){
	$mdb = new Database;
	$mdb->query("select company_id,com_name from ".TBL_COMMON_COMPANY_DETAIL." ccd   "); //where  use_work = '1'
	if ($mdb->total){
			$SelectString = "<Select name='company_id' id='company_id' onChange=\"loadUser(this,'charger_ix')\" >";
			$SelectString = $SelectString."<option value=''>업체 선택</option>";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			
			if($company_id == $mdb->dt[company_id]){
				$SelectString = $SelectString."<option value='".$mdb->dt[company_id]."' selected>".$mdb->dt[com_name]."</option>";
			}else{
				$SelectString = $SelectString."<option value='".$mdb->dt[company_id]."'>".$mdb->dt[com_name]."</option>";
			}
			
		}
		$SelectString = $SelectString."</Select>";
	}
	return $SelectString;
}

function workCompanyUserList($company_id, $department="", $charger_ix="",$property=""){
	$mdb = new Database;
	if($department){
		$mdb->query("select * from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd where  cu.code = cmd.code and authorized = 'Y' and company_id ='$company_id' and department = '".$department."' ");
	}else{
		$mdb->query("select * from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd where  cu.code = cmd.code and authorized = 'Y' and company_id ='$company_id'  ");
	}
	if ($mdb->total){
			$SelectString = "<Select name='charger_ix' id='charger_ix' $property>";
			$SelectString = $SelectString."<option value=''>담당자 선택</option>";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			
			if($charger_ix == $mdb->dt[charger_ix]){
				$SelectString = $SelectString."<option value='".$mdb->dt[charger_ix]."' selected>".$mdb->dt[charger]." (".$mdb->dt[charger_roll].")</option>";
			}else{
				$SelectString = $SelectString."<option value='".$mdb->dt[charger_ix]."'>".$mdb->dt[charger]." (".$mdb->dt[charger_roll].")</option>";
			}
			
		}
		$SelectString = $SelectString."</Select>";
	}
	return $SelectString;
}
?>