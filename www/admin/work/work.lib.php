<?
$week_name = array("0"=>"일","1"=>"월","2"=>"화","3"=>"수","4"=>"목","5"=>"금","6"=>"토");
$work_status = array("WR"=>"작업대기","AR"=>"선작업대기중","WI"=>"진행중","WC"=>"작업완료","IS"=>"이슈중","WH"=>"작업보류","WD"=>"업무취소");
$work_complet_rate = array("0"=>"0%","25"=>"25%","50"=>"50%","75"=>"75%","100"=>"100%","-1"=>"-1");
$work_crud = array("C"=>"생성","R"=>"읽기","U"=>"변경","D"=>"삭제");

$work_tab = array("pj"=>"프로젝트","ww"=>"주간업무","be"=>"미처리업무","my"=>"내업무","my"=>"내업무","de"=>"내부서업무","to"=>"금일업무","se"=>"상세검색");
//print_r($work_tab);
/*
<input type='radio'   name='basic_tab' value='pj' id='basic_tab_pj' title='프로젝트' ".(( "pj" == $admininfo["work_confs"]["basic_tab"]) ? "checked":"")."><label for='basic_tab_pj'>프로젝트</label>
							<input type='radio'   name='basic_tab' value='ww' id='basic_tab_ww' title='주간업무' ".(( "ww" == $admininfo["work_confs"]["basic_tab"]) ? "checked":"")."><label for='basic_tab_ww'>주간업무</label>
							<input type='radio'   name='basic_tab' value='be' id='basic_tab_be' title='미처리업무' ".(( "be" ==  $admininfo["work_confs"]["basic_tab"]) ? "checked":"")."><label for='basic_tab_be'>미처리업무</label>
							<input type='radio'   name='basic_tab' value='my' id='basic_tab_my' title='내업무' ".(( "my" == $admininfo["work_confs"]["basic_tab"]) ? "checked":"")."><label for='basic_tab_my'>내업무</label>
							<input type='radio'   name='basic_tab' value='de' id='basic_tab_de' title='내부서업무' ".(( "de" == $admininfo["work_confs"]["basic_tab"]) ? "checked":"")."><label for='basic_tab_de'>내부서업무</label>
							<input type='radio'   name='basic_tab' value='to' id='basic_tab_to' title='금일업무' ".(( "to" == $admininfo["work_confs"]["basic_tab"]) ? "checked":"")."><label for='basic_tab_to'>금일업무</label>
							<input type='radio'   name='basic_tab' value='se' id='basic_tab_se' title='상세검색' ".(( "se" == $admininfo["work_confs"]["basic_tab"]) ? "checked":"")."><label for='basic_tab_se'>상세검색</label>
							*/
//$work_tab[$admininfo["work_confs"]["basic_tab"]]

function footMenu(){
	global $admininfo;
	$footmenu_str = "<ul class='footer_list'   align=right>";
							
	

if($admininfo[master] == "Y"){
	$footmenu_str .= "<li><a href=\"javascript:LayerShow('operation_config_box')\"><!--img src='../images/icon/config1.gif'--> 운영설정</a></li><li> ㅣ </li>";
}

$footmenu_str .= "<li><a href=\"javascript:LayerShow('input_box')\"><!--img src='../images/icon/config1.gif'--> 환경설정</a><span ".(is_array($admininfo["work_confs"]["config_bbs"]) ? (in_array( "notice", $admininfo["work_confs"]["config_bbs"]) ? "":"style='display:none'"):"")."></li><li> ㅣ </li>";
$footmenu_str .= "<li><a href='bbs.php' ><!--img src='../images/icon/notice.gif'--> 공지사항</a> </span> <span ".(is_array($admininfo["work_confs"]["config_bbs"]) ? (in_array( "data", $admininfo["work_confs"]["config_bbs"]) ? "":"style='display:none'"):"")."></li><li> ㅣ </li>";
$footmenu_str .= "<li><a href='data.php'><!--img src='../images/icon/dataroom.gif'--> 양식자료실</a></span> <span ".(is_array($admininfo["work_confs"]["config_bbs"]) ? (in_array( "story", $admininfo["work_confs"]["config_bbs"]) ? "":"style='display:none'"):"")."></li><li> ㅣ </li>";
$footmenu_str .= "<li><a href='freebbs.php'><!--img src='../images/icon/ourstory.gif'--> 우리들 이야기</a></span></li><li> ㅣ </li>";
$footmenu_str .= "<li><a href='http://unimind.kr/unimind/manual.php' target='_blank'><b>PDF 메뉴얼보기</b><!--img src='../images/orange/btn_view_manual.gif' border=0--></a></li><li> ㅣ </li>";
$footmenu_str .= "<li><a href=\"javascript:PopSWindow('work_manual.php?mmode=pop',1150,760,'work_info')\"><b>동영상 메뉴얼보기</b><!--img src='../images/orange/btn_view_manual.gif' border=0--></a></li>";

	$footmenu_str .= "</ul>";

	return $footmenu_str;
}

function footAddContents(){
	global $admininfo;

	//print_r($admininfo);
$mstring .= "
<div id='input_box' style='display:none;vertical-align:top;'>
<table class='layer_box' border=0 cellpadding=0 cellspacing=0 style='width:760px;height:0px;' >
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
		<td class='box_05' rowspan=2 valign=top style='padding:5px 15px 5px 15px;font-weight:bold;font-size:12px;line-height:150%;text-align:left;' >	
			<h1 id=\"check_title\">".GetTitleNavigation("업무 관리", "업무 관리 > 환경설정 ", false)."</h1>
			<form name='deepzoom_frm' method=post enctype='multipart/form-data' action='work_config.act.php' onsubmit='return CheckFormValue(this)' target='act' style='display:inline;'><!-- target='act' -->
			<input type='hidden' name='act' value='update'>
			<div class=\"g_box2 \" >				
				<table width='100%' border='0' align='center' bgcolor='#c0c0c0' cellpadding=4 cellspacing=1 class='input_table_box'>
					<tr bgcolor=#ffffff>
						<td class='input_box_title' >업무노출기준 : </td>
						<td class='input_box_item' style='padding:5px;'>
							<table>
							<tr>
								<td>
								<select name=config_view_order1 >						
									<option value='sdate' ".CompareReturnValue("sdate",$admininfo["work_confs"]["config_view_order1"],"selected").">업무시작일</option>
									<option value='dday' ".CompareReturnValue("edate",$admininfo["work_confs"]["config_view_order1"],"selected").">업무종료일</option>
									<option value='edit_date' ".CompareReturnValue("edit_date",$admininfo["work_confs"]["config_view_order1"],"selected").">업무수정일</option>
								</select>
								</td>
								<td>
								<select name=config_view_order_type1 >						
									<option value='desc' ".CompareReturnValue("desc",$admininfo["work_confs"]["config_view_order_type1"],"selected").">DESC</option>
									<option value='asc' ".CompareReturnValue("asc",$admininfo["work_confs"]["config_view_order_type1"],"selected").">ASC</option>
								</select>
								</td>
								<td> <div class='small' style='text-align:left;'>* 업무목록 첫번째 노출기준</div> </td>
							</tr>
							<tr>
								<td>
								<select name=config_view_order2>						
									<option value='sdate' ".CompareReturnValue("sdate",$admininfo["work_confs"]["config_view_order2"],"selected").">업무시작일</option>
									<option value='dday' ".CompareReturnValue("edate",$admininfo["work_confs"]["config_view_order2"],"selected").">업무종료일</option>
									<option value='edit_date' ".CompareReturnValue("edit_date",$admininfo["work_confs"]["config_view_order2"],"selected").">업무수정일</option>
								</select>
								</td>
								<td>
								<select name=config_view_order_type2 >						
									<option value='desc' ".CompareReturnValue("desc",$admininfo["work_confs"]["config_view_order_type2"],"selected").">DESC</option>
									<option value='asc' ".CompareReturnValue("asc",$admininfo["work_confs"]["config_view_order_type2"],"selected").">ASC</option>
								</select>
								</td>
								<td> <div class='small' style='text-align:left;'>* 업무목록 두번째 노출기준</div> </td>
							</tr>
							</table>
						</td>
					</tr>
					<!--tr bgcolor=#ffffff>
						<td style='text-align:left;padding-left:10px'>전사 목표 : </td>
						<td style='text-align:left;padding-left:10px'><input type='text' class='textbox'  name='company_goal' style='width:95%' validation='true' title='전사 목표'></td>
					</tr-->
					<tr bgcolor=#ffffff>
						<td class='input_box_title'>좌측메뉴 설정 : </td>
						<td class='input_box_item' style='padding:5px;'>
							<input type='checkbox'   name='config_leftmenu[]' value='department' id='config_leftmenu_department' title='부서/직원' ".(is_array($admininfo["work_confs"]["config_leftmenu"]) ? (is_array($admininfo["work_confs"]["config_leftmenu"]) ? (in_array( "department", $admininfo["work_confs"]["config_leftmenu"]) ? "checked":""):""):"")."><label for='config_leftmenu_department'>부서/직원</label>
							<input type='checkbox'   name='config_leftmenu[]' value='workgroup' id='config_leftmenu_workgroup' title='업무분류' ".(is_array($admininfo["work_confs"]["config_leftmenu"]) ? (is_array($admininfo["work_confs"]["config_leftmenu"]) ? (in_array("workgroup",$admininfo["work_confs"]["config_leftmenu"]) ? "checked":""):""):"")."><label for='config_leftmenu_workgroup'>업무분류</label>
							
						</td>
					</tr>
					<tr bgcolor=#ffffff>
						<td class='input_box_title'>노출게시판 설정 : </td>
						<td class='input_box_item' style='padding:5px;'>
							<input type='checkbox'   name='config_bbs[]' value='notice' id='config_bbs_notice' title='공지사항' ".(is_array($admininfo["work_confs"]["config_bbs"]) ? (in_array( "notice", $admininfo["work_confs"]["config_bbs"]) ? "checked":""):"")."><label for='config_bbs_notice'>공지사항</label>
							<input type='checkbox'   name='config_bbs[]' value='data' id='config_bbs_data' title='양식자료실' ".(is_array($admininfo["work_confs"]["config_bbs"]) ? (in_array("data",$admininfo["work_confs"]["config_bbs"]) ? "checked":""):"")."><label for='config_bbs_data'>양식자료실</label>
							<input type='checkbox'   name='config_bbs[]' value='story' id='config_bbs_story' title='우리들 이야기' ".(is_array($admininfo["work_confs"]["config_bbs"]) ? (in_array("story",$admininfo["work_confs"]["config_bbs"]) ? "checked":""):"")."><label for='config_bbs_story'>우리들 이야기</label>
						</td>
					</tr>
					<tr bgcolor=#ffffff>
						<td class='input_box_title'>노출탭 설정 : </td>
						<td class='input_box_item' style='padding:5px;'>
							<input type='checkbox'   name='config_tab[]' value='pj' id='config_tab_pj' title='프로젝트' ".(is_array($admininfo["work_confs"]["config_tab"]) ? (in_array( "pj", $admininfo["work_confs"]["config_tab"]) ? "checked":""):"")."><label for='config_tab_pj'>프로젝트</label>
							<input type='checkbox'   name='config_tab[]' value='ww' id='config_tab_ww' title='주간업무' ".(is_array($admininfo["work_confs"]["config_tab"]) ? (in_array( "ww", $admininfo["work_confs"]["config_tab"]) ? "checked":""):"")."><label for='config_tab_ww'>주간업무</label>
							<input type='checkbox'   name='config_tab[]' value='be' id='config_tab_be' title='미처리업무' ".(is_array($admininfo["work_confs"]["config_tab"]) ? (in_array( "be", $admininfo["work_confs"]["config_tab"]) ? "checked":""):"")."><label for='config_tab_be'>미처리업무</label>
							<input type='checkbox'   name='config_tab[]' value='my' id='config_tab_my' title='내업무' ".(is_array($admininfo["work_confs"]["config_tab"]) ? (in_array( "my", $admininfo["work_confs"]["config_tab"]) ? "checked":""):"")."><label for='config_tab_my'>내업무</label>
							<input type='checkbox'   name='config_tab[]' value='de' id='config_tab_de' title='내부서업무' ".(is_array($admininfo["work_confs"]["config_tab"]) ? (in_array( "de", $admininfo["work_confs"]["config_tab"]) ? "checked":""):"")."><label for='config_tab_de'>내부서업무</label>
							<input type='checkbox'   name='config_tab[]' value='to' id='config_tab_to' title='금일업무' ".(is_array($admininfo["work_confs"]["config_tab"]) ? (in_array( "to", $admininfo["work_confs"]["config_tab"]) ? "checked":""):"")."><label for='config_tab_to'>금일업무</label>
							<input type='checkbox'   name='config_tab[]' value='se' id='config_tab_se' title='상세검색' ".(is_array($admininfo["work_confs"]["config_tab"]) ? (in_array( "se", $admininfo["work_confs"]["config_tab"]) ? "checked":""):"")."><label for='config_tab_se'>상세검색</label>
						</td>
					</tr>
					<tr bgcolor=#ffffff>
						<td class='input_box_title'>기본탭탭 설정 : </td>
						<td class='input_box_item' style='padding:5px;'>
							<input type='radio'   name='basic_tab' value='pj' id='basic_tab_pj' title='프로젝트' ".(( "pj" == $admininfo["work_confs"]["basic_tab"]) ? "checked":"")."><label for='basic_tab_pj'>프로젝트</label>
							<input type='radio'   name='basic_tab' value='ww' id='basic_tab_ww' title='주간업무' ".(( "ww" == $admininfo["work_confs"]["basic_tab"]) ? "checked":"")."><label for='basic_tab_ww'>주간업무</label>
							<input type='radio'   name='basic_tab' value='be' id='basic_tab_be' title='미처리업무' ".(( "be" ==  $admininfo["work_confs"]["basic_tab"]) ? "checked":"")."><label for='basic_tab_be'>미처리업무</label>
							<input type='radio'   name='basic_tab' value='my' id='basic_tab_my' title='내업무' ".(( "my" == $admininfo["work_confs"]["basic_tab"]) ? "checked":"")."><label for='basic_tab_my'>내업무</label>
							<input type='radio'   name='basic_tab' value='de' id='basic_tab_de' title='내부서업무' ".(( "de" == $admininfo["work_confs"]["basic_tab"]) ? "checked":"")."><label for='basic_tab_de'>내부서업무</label>
							<input type='radio'   name='basic_tab' value='to' id='basic_tab_to' title='금일업무' ".(( "to" == $admininfo["work_confs"]["basic_tab"]) ? "checked":"")."><label for='basic_tab_to'>금일업무</label>
							<input type='radio'   name='basic_tab' value='se' id='basic_tab_se' title='상세검색' ".(( "se" == $admininfo["work_confs"]["basic_tab"]) ? "checked":"")."><label for='basic_tab_se'>상세검색</label>
						</td>
					</tr>
					<tr bgcolor=#ffffff>
						<td class='input_box_title'>노출요일설정 : </td>
						<td class='input_box_item' style='padding:5px;'>
							<input type='checkbox'   name='config_week_num[]' value='SUN' id='config_week_num_sun' title='일' ".(is_array($admininfo["work_confs"]["config_week_num"]) ? (in_array( "SUN", $admininfo["work_confs"]["config_week_num"]) ? "checked":""):"")."><label for='config_week_num_sun'>일</label>
							<input type='checkbox'   name='config_week_num[]' value='MON' id='config_week_num_mon' title='월' ".(is_array($admininfo["work_confs"]["config_week_num"]) ? (in_array( "MON", $admininfo["work_confs"]["config_week_num"]) ? "checked":""):"")."><label for='config_week_num_mon'>월</label>
							<input type='checkbox'   name='config_week_num[]' value='TUE' id='config_week_num_tue' title='화' ".(is_array($admininfo["work_confs"]["config_week_num"]) ? (in_array( "TUE", $admininfo["work_confs"]["config_week_num"]) ? "checked":""):"")."><label for='config_week_num_tue'>화</label>
							<input type='checkbox'   name='config_week_num[]' value='WED' id='config_week_num_wed' title='수' ".(is_array($admininfo["work_confs"]["config_week_num"]) ? (in_array( "WED", $admininfo["work_confs"]["config_week_num"]) ? "checked":""):"")."><label for='config_week_num_wed'>수</label>
							<input type='checkbox'   name='config_week_num[]' value='THU' id='config_week_num_thu' title='목' ".(is_array($admininfo["work_confs"]["config_week_num"]) ? (in_array( "THU", $admininfo["work_confs"]["config_week_num"]) ? "checked":""):"")."><label for='config_week_num_thu'>목</label>
							<input type='checkbox'   name='config_week_num[]' value='FRI' id='config_week_num_fri' title='금' ".(is_array($admininfo["work_confs"]["config_week_num"]) ? (in_array( "FRI", $admininfo["work_confs"]["config_week_num"]) ? "checked":""):"")."><label for='config_week_num_fri'>금</label>
							<input type='checkbox'   name='config_week_num[]' value='SAT' id='config_week_num_sat' title='토' ".(is_array($admininfo["work_confs"]["config_week_num"]) ? (in_array( "SAT", $admininfo["work_confs"]["config_week_num"]) ? "checked":""):"")."><label for='config_week_num_sat'>토</label>
						</td>
					</tr>
					<!--tr bgcolor=#ffffff>
						<td style='text-align:left;padding-left:10px'>이미지  : </td>
						<td style='text-align:left;padding-left:10px'>
							<table>
							<tr>
							<td><input type='file' class='textbox' name='deepzoom_file' validation='true' title='이미지파일' align=absmiddle></td>
							<td><input type='checkbox' name='chk_deepzoom' id='chk_deepzoom' validation='false' title='딥줌생성' value='1'></td>
							<td><label for='chk_deepzoom'>딥줌생성</label></td>
							</tr>
							</table>
							<div class='small' style='text-align:left;'>* 딥중 생성 체크박스를 클릭하시면 딥줌 이미지가 함께 생성됩니다.</div>
						</td>
					</tr>
					<tr bgcolor=#ffffff>
						<td style='text-align:left;padding-left:10px'>이미지 링크 : </td>
						<td style='text-align:left;padding-left:10px'>
							<input type='text' class='textbox'  name='image_link' style='width:95%' validation='true' title='이미지 링크'>
							<div class='small' style='padding:6px 0 0 0'>갤러리 생성시 이미지 클릭시 이동을 원하는 링크를 입력해주세요</div>
						</td>
					</tr-->
				</table>
			</div>
			
			
			<p class=\"btns \" style='text-align:center;padding:0px 0px '>
				<table align=center>
					<tr>
						<td><input type='image' src='../image/b_save.gif' border=0 style='cursor:hand;border:0px;' align=absmiddle></td>
						<td><a id=\"btnCheck_cancel\" href=\"javascript:LayerClose()\"><img src='../image/b_cancel.gif' border=0 align=absmiddle></a></td>
					</tr>
				</table>
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
</div>
<div id='operation_config_box' style='display:none;vertical-align:top;'>
<table class='layer_box' border=0 cellpadding=0 cellspacing=0 style='width:650px;height:0px;display:block;' >
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
			<h1 id=\"check_title\">".GetTitleNavigation("운영설정", "업무 관리 > 운영설정 ", false)."</h1>
			<form name='deepzoom_frm' method=post enctype='multipart/form-data' action='work_config.act.php' onsubmit='return CheckFormValue(this)' target='act' style='display:inline;'><!-- target='act' -->
			<input type='hidden' name='act' value='op_update'>
			<div class=\"g_box2 \" >				
				<table width='100%' border='0' align='center' bgcolor='#c0c0c0' cellpadding=4 cellspacing=1 class='input_table_box'>
					<col width='140px'>
					<col width='*'>
					
					<tr bgcolor=#ffffff>
						<td class='input_box_title'>기업메세지 타이틀 : </td>
						<td class='input_box_item' style='padding:5px;'>
						<input type='text' class='textbox'  name='company_goal_title' style='width:95%' validation='true' title='기업메세지 타이틀' value=\"".str_replace("\"","&quot;",htmlspecialchars($admininfo["work_op_confs"]["company_goal_title"]))."\">
						<div class='small' style='text-align:left;'>* 입력된 텍스트는 좌측 상단의 기업메세지 영역의 이름으로 노출됩니다..</div>
						</td>
					</tr>
					<tr bgcolor=#ffffff>
						<td class='input_box_title'>기업메세지 내용 : </td>
						<td class='input_box_item' style='padding:5px;'>
						<textarea class='textbox'  name='company_goal' style='width:95%;height:80px' validation='true' title='전사 목표' >".str_replace("\"","&quot;",htmlspecialchars($admininfo["work_op_confs"]["company_goal"]))."</textarea>
						<div class='small' style='text-align:left;'>* 입력된 텍스트는 좌측 상단의 기업메세지 타이틀을 클릭했을때 나타납니다.</div>
						</td>
					</tr>
				</table>
			</div>
			
			
			<p class=\"btns \" style='text-align:center;padding:10px 0px '>
				<table>
					<tr>
						<td><input type='image' src='../image/b_save.gif' border=0 style='cursor:hand;border:0px;' align=absmiddle></td>
						<td><a id=\"btnCheck_cancel\" href=\"javascript:LayerClose()\"><img src='../image/b_cancel.gif' border=0 align=absmiddle></a></td>
					</tr>
				</table>
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

function getWorkGroupInfoSelect($obj_id, $obj_txt, $parent_group_ix, $selected, $return_type="select", $depth=1, $property=""){
	global $admininfo;
	$mdb = new Database;
	
	if($depth == 1){
		$sql = 	"SELECT abg.*
				FROM work_group abg 
				where group_depth = '$depth' and company_id = '".$admininfo[company_id]."' and disp = '1'
				group by group_ix ";
	}else if($depth == 2){
		$sql = 	"SELECT abg.*
				FROM work_group abg 
				where group_depth = '$depth' and parent_group_ix = '$parent_group_ix' and company_id = '".$admininfo[company_id]."' and disp = '1'
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
	$sql = "select distinct ccd.company_id, com_name  from common_company_detail ccd
				where ccd.company_id ='$company_id'  ";

	$mdb->query($sql);
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

function workCompanyUserList($company_id, $object_id = "charger_ix", $department="", $code="",$property=""){
	
	$mdb = new Database;
	if($department){
		$sql = "select cmd.code , AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name from common_user cu , common_company_detail ccd, common_member_detail cmd 
				where cu.company_id = ccd.company_id 
				and cu.code = cmd.code and cu.authorized = 'Y' 
				and cu.company_id ='$company_id' 
				and cmd.department = '".$department."' ";
		//echo $sql;
		$mdb->query($sql);
	}else{
		$sql = "select cmd.code , AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name from common_user cu , common_company_detail ccd, common_member_detail cmd 
				where cu.company_id = ccd.company_id 
				and cu.code = cmd.code and cu.authorized = 'Y' 
				and cu.company_id ='$company_id'  ";

		$mdb->query($sql);
	}
	if ($mdb->total){
			$SelectString = "<Select name='".$object_id."' id='".str_replace("[]","",$object_id)."' $property>";
			$SelectString = $SelectString."<option value=''>담당자 선택</option>";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			
			if(is_array($code)){
				if(in_array($mdb->dt[code],$code)){
					$SelectString = $SelectString."<option value='".$mdb->dt[code]."' selected>".$mdb->dt[name]." </option>";
				}else{
					//$SelectString = $SelectString."<option value='".$mdb->dt[code]."'>".$mdb->dt[name]." </option>";
				}
			}else{
				if($code == $mdb->dt[code]){
					$SelectString = $SelectString."<option value='".$mdb->dt[code]."' selected>".$mdb->dt[name]." </option>";
				}else{
					$SelectString = $SelectString."<option value='".$mdb->dt[code]."'>".$mdb->dt[name]." </option>";
				}
			}
		}
		$SelectString = $SelectString."</Select>";
	}
	return $SelectString;
}



function projectUserList($company_id, $object_id = "charger_ix", $_id = "charger_ix", $department="", $code="",$property=""){
	$mdb = new Database;
	if($department){
		$sql = "select cmd.code , AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name from common_user cu , common_company_detail ccd, common_member_detail cmd 
				where cu.company_id = ccd.company_id 
				and cu.code = cmd.code and cu.authorized = 'Y' 
				and cu.company_id ='$company_id' 
				and cmd.department = '".$department."' ";
		//echo $sql;
		$mdb->query($sql);
	}else{
		$sql = "select cmd.code, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name from common_user cu , common_company_detail ccd, common_member_detail cmd 
				where cu.company_id = ccd.company_id 
				and cu.code = cmd.code and cu.authorized = 'Y' 
				and cu.company_id ='$company_id'  ";

		$mdb->query($sql);
	}
	if ($mdb->total){
			$SelectString = "<Select name='".$object_id."' id='".$_id."' $property>";
			$SelectString = $SelectString."<option value=''>담당자</option>";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			
			if(is_array($code)){
				if(in_array($mdb->dt[code],$code)){
					$SelectString = $SelectString."<option value='".$mdb->dt[code]."' selected>".$mdb->dt[name]." </option>";
				}else{
					//$SelectString = $SelectString."<option value='".$mdb->dt[code]."'>".$mdb->dt[charger]." </option>";
				}
			}else{
				if($code == $mdb->dt[code]){
					$SelectString = $SelectString."<option value='".$mdb->dt[code]."' selected>".$mdb->dt[name]." </option>";
				}else{
					$SelectString = $SelectString."<option value='".$mdb->dt[code]."'>".$mdb->dt[name]." </option>";
				}
			}
		}
		$SelectString = $SelectString."</Select>";
	}
	return $SelectString;
}

function WorkHistory($mdb, $wl_ix, $charger_ix, $crud, $crud_desc){
	global $admininfo;
	if($admininfo["charger_id"] != "sigi1074"){
		$sql = "insert into work_action_history(wah_ix,wl_ix,charger_ix, crud,crud_desc,regdate) values('','$wl_ix','$charger_ix','$crud','$crud_desc',NOW())";
		$mdb->query($sql);
	}
	
}


function WorkHistoryResult($mdb, $wl_ix="", $charger_ix="", $crud=""){
	$where = "";
	if($charger_ix != ""){
		$where .= " and wah.charger_ix = '".$charger_ix."' ";
	}
	if($wl_ix != ""){
		$where .= " and wah.wl_ix = '".$wl_ix."' ";
	}
	if($crud != ""){
		$where .= " and wah.crud = '".$crud."' ";
	}
	
	$sql = "select wah.* , AES_DECRYPT(UNHEX(cmd.name),'".$mdb->ase_encrypt_key."') as charger from work_action_history wah, common_member_detail cmd   
			where wah.charger_ix = cmd.code  ".$where." order by regdate desc";
	//echo $sql."<br>";
	$mdb->query($sql);
	return $mdb->fetchall('object');
}

function WorkConfigSetting($mdb){
	global $admininfo;
	
	if(!$admininfo["work_confs"]){
		$mdb->query("select conf_name, conf_val from work_config where charger_ix = '".$admininfo[charger_ix]."' ");
		if(!$mdb->total){
			$mdb->query("insert into work_config  select '".$admininfo[charger_ix]."' as charger_ix, conf_name, conf_val from work_config where charger_ix = '0' ");
		}

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$work_confs[$mdb->dt[conf_name]] = unserialize($mdb->dt[conf_val]);
			//echo "aaa".unserialize($mdb->dt[conf_val]);
		}
		//print_r($work_confs);
		//echo "aaa";
		$admininfo["work_confs"] = $work_confs;
		session_register("admininfo");
	}


	if(!$admininfo["work_op_confs"]){

		$mdb->query("select * from work_op_config where company_id = '".$admininfo[company_id]."' ");
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$work_op_confs[$mdb->dt[conf_name]] = $mdb->dt[conf_val];
		}
	
		$admininfo["work_op_confs"] = $work_op_confs;
	
		session_register("admininfo");
	}
	

}




function WorkTab($total){
	global $list_type, $admininfo;
if($list_type == "project"){
	$mstring = "<div style='padding:0px 0px 4px 4px;'>총 &nbsp;<b>".$total." 건</b> 의 프로젝트가 등록되었습니다. </div>";
}else if($list_type == "weekly"){
	$mstring = "";
}else{
	$mstring = "<div style='padding:0px 0px 4px 4px;'>총 &nbsp;<b>".$total." 건</b> 의 업무가 등록되었습니다. </div>";
}
$mstring .= "
<div class='tab' style='height:43px;'>

					<table class='s_org_tab' border=0 style='width:100%;padding:0px;min-width:900px;'>
					<col width='*'>
					<col width=100px'>
					<tr>
						<td class='tab' >
							<table id='tab_01'  ".($list_type == "project" ? "class='on'":"")." ".(is_array($admininfo["work_confs"]["config_tab"]) ? (in_array( "pj", $admininfo["work_confs"]["config_tab"]) ? "style='display:inline'":"style='display:none'"):"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='work_project_list.php?mmode=$mmode&list_view_type=".$list_view_type."&list_type=project'\">
								프로젝트 ";
								
				$mstring .= "	</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_08'  ".($list_type == "weekly" ? "class='on'":"")." ".(is_array($admininfo["work_confs"]["config_tab"]) ? (in_array( "pj", $admininfo["work_confs"]["config_tab"]) ? "style='display:inline'":"style='display:none'"):"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='work_list.php?mmode=$mmode&list_view_type=weekly&list_type=weekly'\">
								주간계획 ";
								
				$mstring .= "	</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02'  ".($status == "" && $list_type == "" ? "class='on'":"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='work_list.php?mmode=$mmode&list_view_type=".$list_view_type."'\">
								전체업무 ";
								/*
				foreach($work_status  as $key => $value){
					$Contents .= "<input type='checkbox' name='work_status' id='work_status_".($key)."' value='".($key)."'><label for='work_status_".($key)."'>".$value."</label>";
				}
*/
				$mstring .= "	</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03' ".($list_type == "before" ? "class='on'":"")." ".(is_array($admininfo["work_confs"]["config_tab"]) ? (in_array( "be", $admininfo["work_confs"]["config_tab"]) ? "style='display:inline'":"style='display:none'"):"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='work_list.php?mmode=$mmode&list_view_type=".$list_view_type."&list_type=before'\" >미처리업무</td>
								<th class='box_03'></th>
							</tr>
							</table>
							
							<table id='tab_04' ".($list_type == "myjob" ? "class='on'":"")." ".(is_array($admininfo["work_confs"]["config_tab"]) ? (in_array( "my", $admininfo["work_confs"]["config_tab"]) ? "style='display:inline'":"style='display:none'"):"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='work_list.php?mmode=$mmode&list_view_type=".$list_view_type."&list_type=myjob'\">내업무</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_05' ".($list_type == "mydepartment" ? "class='on'":"")." ".(is_array($admininfo["work_confs"]["config_tab"]) ? (in_array( "de", $admininfo["work_confs"]["config_tab"]) ? "style='display:inline'":"style='display:none'"):"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='work_list.php?mmode=$mmode&list_view_type=".$list_view_type."&list_type=mydepartment'\">
									내부서업무
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_06' ".($list_type == "today" ? "class='on'":"")." ".(is_array($admininfo["work_confs"]["config_tab"]) ? (in_array( "to", $admininfo["work_confs"]["config_tab"]) ? "style='display:inline'":"style='display:none'"):"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='work_list.php?mmode=$mmode&list_view_type=".$list_view_type."&list_type=today'\">금일업무</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_07' ".($list_type == "search" ? "class='on'":"")." ".(is_array($admininfo["work_confs"]["config_tab"]) ? (in_array( "se", $admininfo["work_confs"]["config_tab"]) ? "style='display:inline'":"style='display:none'"):"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='work_list.php?mmode=$mmode&list_view_type=".$list_view_type."&list_type=search'\" >업무상세검색</td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
						<td align=left style='vertical-align:bottom;padding:0px 0px 8px 0px;'>";
if(substr_count ($_SERVER["PHP_SELF"],"work_list.php") && $list_type != "search"){
			$mstring .= "
						<table border=1 width=100%  style='text-align:right;' class='rgt'>
							<col width='100px'>
							<col width='2%'>
							<col width='1%'>
							<col width='2%'>
							<col width='1%'>
							<tr>
								<td></td>
								<td><input type='checkbox' name='view_complete_job' id='view_complete_job' onclick=\"ToggleJob('complete')\" ".($_COOKIE[view_complete_job] == 1 ? "checked":"")." ></td>
								<td  align=left style='vertical-align:middle;padding:0px 5px 0px 3px;' nowrap><label for='view_complete_job'> <b>완료/취소업무포함</b></label></td>
								<td><input type='checkbox' name='view_project_job' id='view_project_job' onclick=\"ToggleJob('project')\" ".($_COOKIE[view_project_job] == 1 ? "checked":"")." ></td>
								<td align=left style='vertical-align:middle;padding:0px 5px 0px 3px;' nowrap><label for='view_project_job'> <b>프로젝트 업무포함</b></label></td>
							</tr>
						</table>";
}
			$mstring .= "
						</td>
						
					</tr>
					
					</table>	
				</div>";

	return $mstring;

}

function work_fetch_bbs($table="bbs_notice", $size=5)
{
	global $admininfo;
    //global $db;
	$mdb = new Database;
	if($mdb->dbms_type == "oracle"){
		$sql = "SELECT ".$table.".*, TO_DATE(regdate,'YYYY.MM.DD') AS day, case when regdate > sysdate - 3 then 1 else 0 end as new 
					FROM $table 
					where bbs_etc5 = '".$admininfo[company_id]."' and rownum < 5 ORDER BY regdate DESC ";
	}else{
		$sql = "SELECT ".$table.".*, DATE_FORMAT(regdate,'%Y.%m.%d') AS day, case when regdate > DATE_SUB(now(), interval 3 day) then 1 else 0 end as new 
					FROM $table where bbs_etc5 = '".$admininfo[company_id]."' ORDER BY regdate DESC limit 0,$size";
	}
	$mdb->query($sql);

	return $mdb->fetchall();
	exit;
}



function LiveIssue($type="list", $view = "all"){
	global $admininfo, $start, $max , $page;
	$mdb = new Database;
	
	//echo "view_close_issue:".$_COOKIE[view_close_issue]."<br>";
	if($_COOKIE[view_close_issue] != "1"){
		$where = " and wi.status = 'O' ";
	}

	if($group_ix != ""){
		$where .= " and (wg.group_ix in(".$group_ix.") or wg.parent_group_ix in (".$group_ix.") )  ";
	}else if($_COOKIE["dynatree-work_group-select"]){
		$where .= " and (wg.group_ix in ('".str_replace(",","','",$_COOKIE["dynatree-work_group-select"])."') )  ";
	}

	if($_COOKIE["dynatree-user-select"]){
		$where .= " and (wl.charger_ix in ('".str_replace(",","','",$_COOKIE["dynatree-user-select"])."'))  ";
		//$union_where .= " and (cr.charger_ix in ('".str_replace(",","','",$_COOKIE["dynatree-user-select"])."'))";
	}else{
		$where .= " and (wl.charger_ix = '".$admininfo[charger_ix]."' or  (wl.charger_ix !=  '".$admininfo[charger_ix]."' and is_hidden = '0'))";
		//$union_where .= " and (cr.charger_ix = '".$admininfo[charger_ix]."')";

	}

	if($type == "print"){
		$max = 200;
	}else{
		$max = 20; //페이지당 갯수

		if ($page == ''){
			$start = 0;
			$page  = 1;
		}else{
			$start = ($page - 1) * $max;
		}
		
		$sql = "SELECT count(*) as total
				FROM work_list wl , work_group wg, common_member_detail cmd, work_issue wi
				where wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl.wl_ix != '' 
				and wl.group_ix = wg.group_ix and wi.charger_ix = cmd.code and wl.wl_ix = wi.wl_ix 				
				and wl.status != 'WC' 
				".$where." ";	
				//and (wl.charger_ix =  '".$admininfo[charger_ix]."' or  (wl.charger_ix !=  '".$admininfo[charger_ix]."' and is_hidden = '0'))  
		//echo nl2br($sql);
		$mdb->query($sql);
		$mdb->fetch();
		$total = $mdb->dt[total];
	}

	$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text","view");
	$sql = "SELECT wl.*, wi.issue, wi.status, wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
						FROM work_list wl , work_group wg, common_member_detail cmd, work_issue wi
						where wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl.wl_ix != '' 
						and wl.group_ix = wg.group_ix and wi.charger_ix = cmd.code and wl.wl_ix = wi.wl_ix 						
						and wl.status != 'WC' ".$where."
						group by wl.wl_ix
						order by wl.regdate desc limit $start , $max
						";	
						//and (wl.charger_ix =  '".$admininfo[charger_ix]."' or  (wl.charger_ix !=  '".$admininfo[charger_ix]."' and is_hidden = '0'))  
	
	//echo nl2br($sql);
	$mdb->query($sql);
	$works = $mdb->fetchall();
	//echo count($works);
$mstring = " 
		<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>
			<tr height=25>
				<td colspan=2 style='border-bottom:2px solid #efefef' nowrap><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>이슈목록</b></td>
				<td clospan=2 style='border-bottom:2px solid #efefef;text-align:right;'>
				<table border=0 width=100%  style='text-align:right;' class='rgt'>
							<col width='100px'>
							<col width='2%'>
							<col width='1%'>
							<col width='2%'>
							<col width='1%'>
							<tr>
								<td></td>
								<td><input type='checkbox' name='view_close_issue' id='view_close_issue' onclick=\"ToggleIssue('close_issue')\" ".($_COOKIE[view_close_issue] == 1 ? "checked":"")." ></td>
								<td  align=left style='vertical-align:middle;padding:0px 5px 0px 3px;' nowrap><label for='view_close_issue'> <b>Close 항목포함</b></label></td>
								<td><a href='work_issue_list.php'>more</a></td>
							</tr>
						</table>
				
				</td>
			</tr>";
if($mdb->total == 0){
$mstring .= "<tr ><td colspan=4  style='height:75px;text-align:center;'>등록된 issue 가 없습니다.</td></tr>";
//$mstring .= "<tr><td colspan=4 class='dot-x' style='height:1px;'></td></tr>";
}else{
	$mstring .= "<tr><td colspan=4 style='padding:5px 5px;'>
		<table cellpadding=0 cellspacing=0 width=100% class='list_table_box'>";

		for ($i = 0; $i < count($works); $i++)
		{
			//$mdb->fetch($i);
			$mstring .= "<tr>
								<td>
								<table width=100% cellpadding=0 cellspacing=0 border=0>";
					$mstring .= "<tr>
										<td colspan=3>
											<table cellpadding=0 cellspacing=0 width=100% border=0>
												<col width='*'>
												<col width='5%'>
												<col width='20%'>
												<col width='5%'>
												<tr height='25px'>							
												<td style='padding-left:10px;'>";
												if($_GET["mmode"] != "print"){
													$mstring .= "
													".($works[$i][is_hidden] == '1' ? "<img src='../images/key.gif' border=0 align=bottom title='비밀글'> ":"")."
													".($works[$i][is_schedule] == '1' ? "<img src='../images/orange/calendar.gif' border=0 align=absmiddle title='스케줄'> ":"")."
													".($works[$i][co_charger_yn] == 'Y' ? "<img src='../images/orange/cowork.gif' border=0 align=absmiddle title='협업업무'> ":"")."";
												}
												$mstring .= "
													<a href=\"work_view.php?view_type=issue&mmode=&wl_ix=".$works[$i][wl_ix]."\" align=absmiddle><b style='color:#000000;'>".$works[$i][group_name]." > ".$works[$i][work_title]." </b>(".$works[$i][comment_cnt].")</a></td>
												<td></td>
												<td>
													<table width=90% cellpadding=0 cellspacing=0 style='border:1px solid #db6201;margin-bottom:5px;'>
														<col width='".($works[$i][complete_rate] == 0 ? 1:$works[$i][complete_rate])."%'>
														<col width='".((100-$works[$i][complete_rate]) == 100 ? 99:(100-$works[$i][complete_rate]))."%'>
														<tr height=8><td bgcolor='#ff7200' id='graph_".$works[$i][wl_ix]."'></td><td></td></tr>
													</table>
												</td>
												<td>".$works[$i][complete_rate]." % </td>
											</tr>
											</table>
										</td>
									</tr>";
								//$mstring .= "<tr height=1><td colspan=4 class='dot-x'></td></tr>";
					
			

			$sql = "SELECT wl.*, wi.wi_ix, wi.issue, wi.status, wi.regdate, wg.group_name, wg.group_depth, wg.parent_group_ix, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
						FROM work_list wl , work_group wg, common_member_detail cmd, work_issue wi
						where wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl.wl_ix != '' 
						and wl.group_ix = wg.group_ix and wi.charger_ix = cmd.code and wl.wl_ix = wi.wl_ix and wl.wl_ix = '".$works[$i][wl_ix]."'						
						and wl.status != 'WC' ".$where."
						order by wi.regdate desc
						";	
						//and (wl.charger_ix =  '".$admininfo[charger_ix]."' or  (wl.charger_ix !=  '".$admininfo[charger_ix]."' and is_hidden = '0'))  
			//echo nl2br($sql);
			$mdb->query($sql);
			$comments = $mdb->fetchall();
			//echo count($comments)."<br>";
			for ($j = 0; $j < count($comments); $j++)
			{
			$mstring .= "<tr height='25px'>
								<td style='padding:5px 10px;line-height:140%;'>
								<b style='color:#000000;'>".$comments[$j][name]."</b> - <b>".($comments[$j][status] == "O" ? "<a href='work.act.php?act=issue_close&wi_ix=".$comments[$j][wi_ix]."' style='color:red;' target='iframe_act'><span class='helpcloud'  help_html='이슈가 Close 가 된 상태라면 해당 Open을 클릭하시면 Close 상태로 변경되게 됩니다. '>[Open]</span></a>":"[Close]")."</b> <a href=\"work_view.php?view_type=issue&mmode=&wl_ix=".$comments[$j][wl_ix]."\" align=absmiddle class=small>".nl2br($comments[$j][issue])."</a>
								</td>
								<td style='padding-right:10px;' align=right>".$comments[$j][regdate]."</td>
							</tr>
							<tr>
								<td align='left' colspan=3>
								<a href='download.php?wi_ix=".$comments[$j][wi_ix]."&data_type=issue&data_file=".$comments[$j][issue_file]."'>".$comments[$j][issue_file]."</a>
								</td>
							</tr>
							
							";
			}
				$mstring .= "</table>
							</td>
						</tr>";
		}
		$mstring .= "</table>
						</td>
					</tr>";
}

$mstring .= "<tr height=50><td colspan=4 align=center>".$str_page_bar."</td></tr>";
$mstring .= "</table>
		";

return $mstring;
}


function LiveComment(){
	global $admininfo, $start, $max , $page;
	$mdb = new Database;

	$max = 20; //페이지당 갯수

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}


	$sql = "SELECT count(*) as total
			FROM work_list wl , work_group wg, common_member_detail cmd, work_issue wi
			where wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl.wl_ix != '' 
			and wl.group_ix = wg.group_ix and wi.charger_ix = cmd.code and wl.wl_ix = wi.wl_ix 
			and (wl.charger_ix =  '".$admininfo[charger_ix]."' or  (wl.charger_ix !=  '".$admininfo[charger_ix]."' and is_hidden = '0'))  
			and wl.status != 'WC' ";	
	//echo nl2br($sql);
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[total];

	//echo $total;

	$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text","view");
	$sql = "SELECT wl.*, wi.issue, wg.group_name, wg.group_depth, wg.parent_group_ix,AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
			FROM work_list wl , work_group wg, common_member_detail cmd, work_issue wi
			where wl.company_id = '".$_SESSION['admininfo']['company_id']."' and wl.wl_ix != '' 
			and wl.group_ix = wg.group_ix and wi.charger_ix = cmd.code and wl.wl_ix = wi.wl_ix 
			and (wl.charger_ix =  '".$admininfo[charger_ix]."' or  (wl.charger_ix !=  '".$admininfo[charger_ix]."' and is_hidden = '0'))  
			and wl.status != 'WC'
			order by regdate desc
			limit $start , $max ";	
	//echo nl2br($sql);
	$mdb->query($sql);

$mstring = "<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>
			";
if($mdb->total == 0){
$mstring .= "<tr ><td colspan=4 class='dot-x' style='height:75px;text-align:center;'>등록된 이슈가 없습니다.</td></tr>";
$mstring .= "<tr><td colspan=4 class='dot-x' style='height:1px;'></td></tr>";
}else{
		for ($i = 0; $i < $mdb->total; $i++)
		{
			$mdb->fetch($i);

			if($b_wl_ix != $mdb->dt[wl_ix]){
			$mstring .= "<tr height='25px' bgcolor=#efefef>
							<td colspan=2 style='padding:0px 0px 0px 10px'><img src='../image/icon_list.gif' border=0 align=bottom >
							".($mdb->dt[is_hidden] == '1' ? "<img src='../images/key.gif' border=0 align=bottom title='비밀글'> ":"")."
							".($mdb->dt[is_schedule] == '1' ? "<img src='../images/orange/calendar.gif' border=0 align=absmiddle title='스케줄'> ":"")."
							".($mdb->dt[co_charger_yn] == 'Y' ? "<img src='../images/orange/cowork.gif' border=0 align=absmiddle title='협업업무'> ":"")."
							<a href=\"work_view.php?mmode=&wl_ix=".$mdb->dt[wl_ix]."\" align=absmiddle><b>".$mdb->dt[group_name]." > ".$mdb->dt[work_title]."</b></a></td>
							<td></td>
							<td>
								<table width=90% cellpadding=0 cellspacing=0 style='border:1px solid #db6201;margin-bottom:5px;'>
									<col width='".($mdb->dt[complete_rate] == 0 ? 1:$mdb->dt[complete_rate])."%'>
									<col width='".((100-$mdb->dt[complete_rate]) == 100 ? 99:(100-$mdb->dt[complete_rate]))."%'>
									<tr height=8><td bgcolor='#ff7200' id='graph_".$mdb->dt[wl_ix]."'></td><td></td></tr>
								</table>
							</td>
						</tr>";
			//$mstring .= "<tr height=1><td colspan=4 class='dot-x'></td></tr>";
			}
		
	$mstring .= "<tr height='25px'>
					<td colspan=2 style='padding:10px 20px;line-height:130%;'>
					<!--[comment]<br>-->
					<a href=\"work_view.php?mmode=&wl_ix=".$mdb->dt[wl_ix]."\" align=absmiddle >".nl2br($mdb->dt[issue])."</a></td>
					<td>".$mdb->dt[charger]."</td>
					
				</tr>";
	$mstring .= "<tr height=1><td colspan=4 class='dot-x'></td></tr>";
		$b_wl_ix = $mdb->dt[wl_ix];
		}
}
	

$mstring .= "<tr height=50><td colspan=4 align=center>".$str_page_bar."</td></tr>";
$mstring .= "</table>";

return $mstring;
}



?>