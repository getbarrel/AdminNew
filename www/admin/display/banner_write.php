<?
include("../class/layout.class");
include_once("../display/display.lib.php");

if(!$agent_type){
	$agent_type = "W";
}
$db = new Database();

if($banner_ix){
	/*
	$sql = "select bi.*,bd.div_name,ccd.com_name
				from shop_bannerinfo bi
				LEFT JOIN shop_banner_div bd ON bi.banner_page=bd.div_ix
				LEFT JOIN common_company_detail ccd USING (company_id)
				where banner_ix ='$banner_ix' ";
	*/

	$sql = "select bi.company_id,bi.mall_ix, bi.banner_loc, bi.banner_kind, bi.banner_text_reversal, bi.change_effect, bi.banner_page,bi.banner_position, bi.display_cid, bi.banner_link,bi.banner_target,bi.banner_desc,bi.banner_name,bi.shot_title,bi.banner_img,bi.banner_img_on,bi.banner_btn_position,
				bi.banner_width,bi.banner_height,bi.disp, bi.md_mem_ix, bi.goal_cnt,bi.banner_html, bi.banner_html_m,
       			bi.b_name, bi.i_name, bi.u_name, bi.c_name, bi.s_name,
				bi.b_title, bi.i_title, bi.u_title, bi.c_title, bi.s_title,
				bi.b_desc, bi.i_desc, bi.u_desc, bi.c_desc, bi.s_desc,
       			bi.banner_desc_m,bi.banner_name_m,bi.shot_title_m, 
       			bi.b_name_m, bi.i_name_m, bi.u_name_m, bi.c_name_m, bi.s_name_m,
				bi.b_title_m, bi.i_title_m, bi.u_title_m, bi.c_title_m, bi.s_title_m,
				bi.b_desc_m, bi.i_desc_m, bi.u_desc_m, bi.c_desc_m, bi.s_desc_m,   
				date_format(bi.use_sdate,'%Y%m%d%H%i%s') as use_sdate,
				date_format(bi.use_sdate, '%H') as use_stime, date_format(bi.use_sdate, '%i') as use_sminute,
				date_format(bi.use_edate,'%Y%m%d%H%i%s') as use_edate,
				date_format(bi.use_edate, '%H') as use_etime, date_format(bi.use_edate, '%i') as use_eminute,
				bi.regdate,bd.div_name,ccd.com_name
				from shop_bannerinfo bi
				LEFT JOIN shop_banner_div bd ON bi.banner_page=bd.div_ix
				LEFT JOIN common_company_detail ccd on bi.company_id=ccd.company_id
				where bi.banner_ix ='$banner_ix' ";
	$db->query($sql);

	//$db->query("SELECT * FROM shop_bannerinfo where banner_ix ='$banner_ix' ");
	$db->fetch();
	if($mode == "copy"){
		$act = "insert";
	}else{
		$act = "update";
	}
	
	$mall_ix = $db->dt[mall_ix];
	$banner_loc = $db->dt[banner_loc];
	$banner_kind = $db->dt[banner_kind];
    $banner_text_reversal = $db->dt[banner_text_reversal];
	$change_effect = $db->dt[change_effect];

	$com_name = $db->dt[com_name];
	$company_id = $db->dt[company_id];
	$banner_page = $db->dt[banner_page];
	$banner_position = $db->dt[banner_position];
	$display_cid = $db->dt[display_cid];
	
	$banner_name = $db->dt[banner_name];
	$b_name		= $db->dt[b_name];
	$i_name		= $db->dt[i_name];
	$u_name		= $db->dt[u_name];
	$c_name		= $db->dt[c_name];
	$s_name		= $db->dt[s_name];

    $shot_title = $db->dt[shot_title];
	$b_title	= $db->dt[b_title];
	$i_title	= $db->dt[i_title];
	$u_title	= $db->dt[u_title];
	$c_title	= $db->dt[c_title];
	$s_title	= $db->dt[s_title];

	$banner_name_m = $db->dt[banner_name_m];
	$b_name_m		= $db->dt[b_name_m];
	$i_name_m		= $db->dt[i_name_m];
	$u_name_m		= $db->dt[u_name_m];
	$c_name_m		= $db->dt[c_name_m];
	$s_name_m		= $db->dt[s_name_m];

	$shot_title_m = $db->dt[shot_title_m];
	$b_title_m	= $db->dt[b_title_m];
	$i_title_m	= $db->dt[i_title_m];
	$u_title_m	= $db->dt[u_title_m];
	$c_title_m	= $db->dt[c_title_m];
	$s_title_m	= $db->dt[s_title_m];

	$banner_link = $db->dt[banner_link];
	$banner_target = $db->dt[banner_target];

	$banner_desc = $db->dt[banner_desc];
	$b_desc		= $db->dt[b_desc];
	$i_desc		= $db->dt[i_desc];
	$u_desc		= $db->dt[u_desc];
	$c_desc		= $db->dt[c_desc];
	$s_desc		= $db->dt[s_desc];

	$banner_desc_m = $db->dt[banner_desc_m];
	$b_desc_m		= $db->dt[b_desc_m];
	$i_desc_m		= $db->dt[i_desc_m];
	$u_desc_m		= $db->dt[u_desc_m];
	$c_desc_m		= $db->dt[c_desc_m];
	$s_desc_m		= $db->dt[s_desc_m];


	$banner_img = $db->dt[banner_img];
	$banner_img_on = $db->dt[banner_img_on];	
	$banner_btn_position = $db->dt[banner_btn_position];	

	
	$banner_width = $db->dt[banner_width];
	$banner_height = $db->dt[banner_height];
	$banner_html = $db->dt[banner_html];
	$banner_html_m = $db->dt[banner_html_m];
	
	$md_mem_ix = $db->dt[md_mem_ix];
	$goal_cnt = $db->dt[goal_cnt];

	$disp = $db->dt[disp];
	//echo $db->dt[use_sdate];
	if($db->dt[use_sdate] && $db->dt[use_sdate] != "000000000000"){

		//$use_sdate = substr($db->dt[use_sdate],0,4)."-".substr($db->dt[use_sdate],4,2)."-".substr($db->dt[use_sdate],6,2);
        $use_sdate = date('Y-m-d H:i:s', strtotime($db->dt[use_sdate]));
		$sTime = substr($db->dt[use_sdate],8,2);
		$sMinute = substr($db->dt[use_sdate],10,2);//$db->dt[use_sminute];
	}else{
		$use_sdate = date("Y-m-d");
	}
	//echo $sDate;
	if($db->dt[use_edate]  && $db->dt[use_edate] != "000000000000"){
		//$eDate = substr($db->dt[use_edate],0,4)."/".substr($db->dt[use_edate],5,2)."/".substr($db->dt[use_edate],8,2);
		$use_edate = date('Y-m-d H:i:s', strtotime($db->dt[use_edate]));
		$eTime = substr($db->dt[use_edate],8,2);//$db->dt[use_etime];
		$eMinute = substr($db->dt[use_edate],10,2);//$db->dt[use_eminute];
	}else{
		$next10day = mktime(0, 0, 0, date("m")  , date("d")+10, date("Y"));
		$use_edate = date("Y-m-d",$next10day);
	}

	//$startDate = $db->dt[use_sdate];
	//$endDate = $db->dt[use_edate];
	/*
	if(!$startDate){
		$startDate = date("Ymd");
	}
	if(!$endDate){
		$endDate = date("Ymd", $next10day);
	}
	*/


}else{
	$act = "insert";
	/*
	$next10day = mktime(0, 0, 0, date("m")  , date("d")+10, date("Y"));

	$sDate = date("Y/m/d");
	$eDate = date("Y/m/d",$next10day);

	$startDate = date("Ymd");
	$endDate = date("Ymd",$next10day);
	*/
	$next10day = mktime(date("H"), date("i"), date("s"), date("m")  , date("d")+10, date("Y"));

//	$sDate = date("Y/m/d");
	$sDate = date("Ymd");
	$eDate = date("Ymd", $next10day);

	$startDate = date("Ymd");
	$endDate = date("Ymd", $next10day);

	/*
		if(!$db->mysql_table_exists("shop_bannerinfo")){
				$sql = "create table shop_bannerinfo (
				banner_ix int(4) unsigned not null auto_increment  ,
				banner_name varchar(20) null default null,
				banner_page int(2) null default null,
				banner_link varchar(255)  null default null,
				banner_target varchar(20) null default null,
				banner_desc varchar(255)  null default null,
				regdate datetime not null,
				primary key(banner_ix)
				)TYPE=MyISAM COMMENT='배너정보' ;";

				$db->query($sql);
		}
	*/
}



$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		<col width='20%' />
		<col width='80%' />
	  <tr >
		<td align='left' colspan=2>";
		if (function_exists('GetTitleNavigation')) {
			$Contents01 .= "
				".GetTitleNavigation("배너관리", "전시관리 > 배너관리 ")."
				";
		}
		$Contents01 .= "
		</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>배너 등록/수정</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	   <col width='20%' />
		<col width='30%' />
		<col width='20%' />
		<col width='30%' />";
		if($_SESSION["admin_config"][front_multiview] == "Y"){
		$Contents01 .= "
		<tr height=28>
			<td class='search_box_title' > 프론트 전시 구분</td>
			<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." <span class=small>전시 구분을 선택하실경우 해당 사이트에 노출됩니다.</span></td>
		</tr>";
		}
		$Contents01 .= "
		<tr bgcolor=#ffffff >
			<td class='search_box_title' > 전시위치</td>
			<td class='search_box_item' colspan=3>
				<select name='banner_loc' style='height:22px;' align=absmiddle >
					<option value='A' ".($banner_loc == "A" || $banner_loc == "" ? "selected":"").">PC/Mobile 전체노출</option>
					<option value='P' ".($banner_loc == "P" ? "selected":"").">PC노출</option>
					<option value='M' ".($banner_loc == "M" ? "selected":"").">Mobile노출</option>
				</select>
			</td>
		</tr>
	   <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>페이지 / 배너위치<img src='".$required3_path."'></b> </td>
	    <td class='input_box_item' colspan=3>".getBannerFirstDIV($banner_page)."

		".bannerPosition($banner_page, $banner_position)." <div style='display:inline;padding-left:10px;'> 배너위치가 정해질경우 배너를 스케줄 형태로 관리 하실 수 있습니다.</div>

			<!--select name='banner_page' style='height:22px;' align=absmiddle validation=true title='페이지'>
	    	<option value=''>페이지를 선택하세요</option>
	    	<option value='1' ".($banner_page == "1" ? "selected":"").">메인</option>
	    	<option value='2' ".($banner_page == "2" ? "selected":"").">카테고리메인</option>
			<option value='3' ".($banner_page == "3" ? "selected":"").">카테고리서브</option>
			<option value='4' ".($banner_page == "4" ? "selected":"").">신상품</option>
			<option value='5' ".($banner_page == "5" ? "selected":"").">할인상품</option>
			<option value='6' ".($banner_page == "6" ? "selected":"").">베스트</option>
			<option value='7' ".($banner_page == "7" ? "selected":"").">이벤트/기획전</option>
			<option value='8' ".($banner_page == "8" ? "selected":"").">메인스크롤</option>
			<option value='9' ".($banner_page == "9" ? "selected":"").">할인스크롤</option>
			<option value='10' ".($banner_page == "10" ? "selected":"").">신상품스크롤</option>
			<option value='11' ".($banner_page == "11" ? "selected":"").">로그인</option>
			</select-->
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>간략제목 </b> </td>
	    <td class='input_box_item'>
	    	<input type=text class='textbox' name='shot_title' value=\"".$shot_title."\" title='간략제목' style='width:220px;'> <span class=small></span>
	    </td>
	    <td class='input_box_title'> <b>간략제목 설정</b> </td>
	    <td class='input_box_item'>
	    	<input type='radio' name='s_title' id='s_title_L' value='L' ".("L" == $s_title || "" == $s_title  ? "checked":"")."><label for='s_title_L'> 좌측정렬</label>
			<input type='radio' name='s_title' id='s_title_C' value='C' ".("C" == $s_title ? "checked":"")."><label for='s_title_C'> 가운데정렬</label>
			<input type='radio' name='s_title' id='s_title_R' value='R' ".("R" == $s_title ? "checked":"")."><label for='s_title_R'> 우측정렬</label><br><br>
			진하게<input type='checkbox' name='b_title' id='b_title' ".("Y" == $b_title ? "checked":"").">
			기울기<input type='checkbox' name='i_title' id='i_title' ".("Y" == $i_title ? "checked":"").">
			밑줄<input type='checkbox' name='u_title' id='u_title' ".("Y" == $u_title ? "checked":"").">
			글자색 <input type='text' name='c_title' id='c_title' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_title ? "#000000":$c_title)."'>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>배너명 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'><input type=text class='textbox' name='banner_name' value='".$banner_name."' title='배너명' validation=true style='width:220px;'> <span class=small></span></td>
	    <td class='input_box_title'> <b>배너명 설정</b> </td>
	    <td class='input_box_item'>
	    	<input type='radio' name='s_name' id='s_name_L' value='L' ".("L" == $s_name || "" == $s_name  ? "checked":"")."><label for='s_name_L'> 좌측정렬</label>
			<input type='radio' name='s_name' id='s_name_C' value='C' ".("C" == $s_name ? "checked":"")."><label for='s_name_C'> 가운데정렬</label>
			<input type='radio' name='s_name' id='s_name_R' value='R' ".("R" == $s_name ? "checked":"")."><label for='s_name_R'> 우측정렬</label><br><br>
			진하게<input type='checkbox' name='b_name' id='b_name' ".("Y" == $b_name ? "checked":"").">
			기울기<input type='checkbox' name='i_name' id='i_name' ".("Y" == $i_name ? "checked":"").">
			밑줄<input type='checkbox' name='u_name' id='u_name' ".("Y" == $u_name ? "checked":"").">
			글자색 <input type='text' name='c_name' id='c_name' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_name ? "#000000":$c_name)."'> 
	    <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>간략제목(모바일) </b> </td>
	    <td class='input_box_item'>
	    	<input type=text class='textbox' name='shot_title_m' value=\"".$shot_title_m."\" title='간략제목(모바일)' style='width:220px;'> <span class=small></span>
	    </td>
	    <td class='input_box_title'> <b>간략제목 설정(모바일)</b> </td>
	    <td class='input_box_item'>
	    	<input type='radio' name='s_title_m' id='s_title_m_L' value='L' ".("L" == $s_title_m || "" == $s_title_m  ? "checked":"")."><label for='s_title_m_L'> 좌측정렬</label>
			<input type='radio' name='s_title_m' id='s_title_m_C' value='C' ".("C" == $s_title_m ? "checked":"")."><label for='s_title_m_C'> 가운데정렬</label>
			<input type='radio' name='s_title_m' id='s_title_m_R' value='R' ".("R" == $s_title_m ? "checked":"")."><label for='s_title_m_R'> 우측정렬</label><br><br>
			진하게<input type='checkbox' name='b_title_m' id='b_title_m' ".("Y" == $b_title_m ? "checked":"").">
			기울기<input type='checkbox' name='i_title_m' id='i_title_m' ".("Y" == $i_title_m ? "checked":"").">
			밑줄<input type='checkbox' name='u_title_m' id='u_title_m' ".("Y" == $u_title_m ? "checked":"").">
			글자색 <input type='text' name='c_title_m' id='c_title_m' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_title_m ? "#000000":$c_title_m)."'>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>배너명(모바일) <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'><input type=text class='textbox' name='banner_name_m' value='".$banner_name_m."' title='배너명' validation=true style='width:220px;'> <span class=small></span></td>
	    <td class='input_box_title'> <b>배너명 설정(모바일)</b> </td>
	    <td class='input_box_item'>
	    	<input type='radio' name='s_name_m' id='s_name_m_L' value='L' ".("L" == $s_name_m || "" == $s_name_m  ? "checked":"")."><label for='s_name_m_L'> 좌측정렬</label>
			<input type='radio' name='s_name_m' id='s_name_m_C' value='C' ".("C" == $s_name_m ? "checked":"")."><label for='s_name_m_C'> 가운데정렬</label>
			<input type='radio' name='s_name_m' id='s_name_m_R' value='R' ".("R" == $s_name_m ? "checked":"")."><label for='s_name_m_R'> 우측정렬</label><br><br>
			진하게<input type='checkbox' name='b_name_m' id='b_name_m' ".("Y" == $b_name_m ? "checked":"").">
			기울기<input type='checkbox' name='i_name_m' id='i_name_m' ".("Y" == $i_name_m ? "checked":"").">
			밑줄<input type='checkbox' name='u_name_m' id='u_name_m' ".("Y" == $u_name_m ? "checked":"").">
			글자색 <input type='text' name='c_name_m' id='c_name_m' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_name_m ? "#000000":$c_name_m)."'> 
	    <span class=small></span></td>
	  </tr>
	 <tr height=27>
		<td class='input_box_title' > 노출 카테고리</td>
		<td class='input_box_item' colspan=3 > 
			<input type=hidden name='display_cid' id='display_cid' value='".$display_cid."'>
			<table border=0 cellpadding=0 cellspacing=0>
				<tr>
					<td style='padding-right:5px;'>".getCategoryList3("대분류", "cid0_1", "class='display_cid' onChange=\"loadCategory($(this),'cid1_1',2)\" title='대분류' ", 0, $display_cid)."</td>
					<td style='padding-right:5px;'>".getCategoryList3("중분류", "cid1_1", "class='display_cid' onChange=\"loadCategory($(this),'cid2_1',2)\" title='중분류'", 1, $display_cid)."</td>
					<td style='padding-right:5px;'>".getCategoryList3("소분류", "cid2_1", "class='display_cid' onChange=\"loadCategory($(this),'cid3_1',2)\" title='소분류'", 2, $display_cid)."</td>
					<td>".getCategoryList3("세분류", "cid3_1", "class='display_cid' onChange=\"loadCategory($(this),'display_cid',2)\" title='세분류'", 3, $display_cid)."</td>
				</tr>
			</table> "; 
		$Contents01 .= "
		</td>
	  </tr>
	  <tr>
			<td class='search_box_title'>배너 노출기간 <img src='".$required3_path."'> </td>
			  <td class='search_box_item'  colspan=3>
			   ".search_date('use_sdate','use_edate',$use_sdate,$use_edate,'Y',"",'autocomplete="off"')."";
/*
$Contents01 .= "
				<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width='100%'>
				<col width=70>
				<col width=10>
				<col width=120>
				<col width=20>
				<col width=70>
				<col width=10>
				<col width=120>
				<col width=*>
					<tr>
						<td nowrap>
						<input type='text' name='use_sdate' class='textbox' value='".$use_sdate."' style='height:18px;width:70px;text-align:center;' id='start_datepicker'>
						</td>
						<td>일</td>
						<td nowrap>
							<SELECT name=FromHH>";
							for($i=0;$i < 24;$i++){
								$Contents01.= "<option value='".$i."' ".($sTime == $i ? "selected":"").">".$i."</option>";
												}
								$Contents01.= "
							</SELECT> 시
							<SELECT name=FromMI>";
							for($i=0;$i < 60;$i++){
								$Contents01.= "<option value='".$i."' ".($sMinute == $i ? "selected":"").">".$i."</option>";
												}
								$Contents01.= "
							</SELECT> 분
						</td>
						<td align=center> ~ </td>
						<td nowrap>
							<input type='text' name='use_edate' class='textbox' value='".$use_edate."' style='height:18px;width:70px;text-align:center;' id='end_datepicker'>
						</td>
						<td>일</td>
						<td nowrap>
							<SELECT name=ToHH>";
							for($i=0;$i < 24;$i++){
								$Contents01.= "<option value='".$i."' ".($eTime == $i ? "selected":"").">".$i."</option>";
												}
								$Contents01.= "
							</SELECT> 시
							<SELECT name=ToMI>";
							for($i=0;$i < 60;$i++){
								$Contents01.= "<option value='".$i."' ".($eMinute == $i ? "selected":"").">".$i."</option>";
												}
								$Contents01.= "
							</SELECT> 분
						</td>
						<td style='padding:0px 10px'>
							<a href=\"javascript:select_date('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
							<a href=\"javascript:select_date('$today','$voneweeklater',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
							<a href=\"javascript:select_date('$today','$v15later',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
							<a href=\"javascript:select_date('$today','$vonemonthlater',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
							<a href=\"javascript:select_date('$today','$v2monthlater',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
							<a href=\"javascript:select_date('$today','$v3monthlater',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
						</td>
					</tr>
				</table>";
*/
$Contents01 .= "
			  </td>
		</tr>
		<tr bgcolor=#ffffff >
			<td class='input_box_title'> 배너종류 </td>
			<td class='input_box_item' colspan=3>
				<input type=radio name='banner_kind' id='banner_kind_1' value='1' ".($banner_kind == "1" || $banner_kind == "" ? "checked":"")." onclick=\"ChangeBannerKind(1);\"><label for='banner_kind_1'>기본이미지 배너</label>
				<!--input type=radio name='banner_kind' id='banner_kind_2' value='2' ".($banner_kind == "2" ? "checked":"")." onclick=\"ChangeBannerKind(2);\"><label for='banner_kind_2'>플래쉬형 배너</label>
				<select name='change_effect' style='250px;' ".($banner_kind == "2" ? "validation=true":"validation=false")." title='효과선택' id='change_effect'>
					<option value='' >선택하세요</option>
					<option value='S' ".CompareReturnValue(S,$db->dt['change_effect']).">슬라이드</option>
					<option value='F' ".CompareReturnValue(F,$db->dt['change_effect']).">패이드인</option>
					<option value='R' ".CompareReturnValue(R,$db->dt['change_effect']).">랜덤</option>
					<option value='T' ".CompareReturnValue(T,$db->dt['change_effect']).">지그재그</option>
				</select>
				<input type=radio name='banner_kind' id='banner_kind_3' value='3' ".($banner_kind == "3" ? "checked":"")." onclick=\"ChangeBannerKind(3);\"><label for='banner_kind_3'>슬라이드 배너</label-->
				<input type=radio name='banner_kind' id='banner_kind_4' value='4' ".($banner_kind == "4" ? "checked":"")." onclick=\"ChangeBannerKind(4);\"><label for='banner_kind_4'>동영상 배너</label>
				<input type=radio name='banner_kind' id='banner_kind_5' value='5' ".($banner_kind == "5" ? "checked":"")." onclick=\"ChangeBannerKind(5);\"><label for='banner_kind_5'>사용자지정 배너
				</label> 
			</td>
		</tr>
		<tr>
			<td class='input_box_title'>배너 텍스트 색상반전 선택</td>
			<td class='input_box_item' colspan='3'>
				<input type='radio' name='banner_text_reversal' id='banner_text_reversal0' value='0' ".(($banner_text_reversal == "0" || $banner_text_reversal == "") ? "checked" : "")."><label for='banner_text_reversal0'> 화이트->블랙</label>
				<input type='radio' name='banner_text_reversal' id='banner_text_reversal1' value='1' ".($banner_text_reversal == "1" ? "checked" : "")."><label for='banner_text_reversal1'> 블랙->화이트</label>

				<input type='radio' name='banner_text_reversal' id='banner_text_reversal2' value='2' ".($banner_text_reversal == "2" ? "checked" : "")."><label for='banner_text_reversal2'> 화이트->화이트</label>

				<input type='radio' name='banner_text_reversal' id='banner_text_reversal3' value='3' ".($banner_text_reversal == "3" ? "checked" : "")."><label for='banner_text_reversal3'> 블랙->블랙</label>
			</td>
		</tr>
		<tr bgcolor=#ffffff >
			<td class='input_box_title' style='vertical-align:top;padding-top:20px;'> <b>배너이미지 <img src='".$required3_path."'></b> <img src='../images/".$admininfo["language"]."/btn_add.gif' alt='옵션추가' id='flash_addbtn' ".(($banner_kind == "2" || $banner_kind == "3" || $banner_kind == "5") ? "style='display:inline;'":"style='display:none;'")."   align=absmiddle> </td>
			<td class='input_box_item' colspan=3 style='padding:5px 15px;'>

			
		<table cellpadding=0 cellspacing=0 border='0' width='100%' id='move_banner_table' style='".(($banner_kind == '2' || $banner_kind == '3' || $banner_kind == '5') ? "display:block;":"display:none;")."'>
		";
	//$mfdArr = array();
	$db->query("SELECT * FROM shop_bannerinfo_detail  where banner_ix = '".$banner_ix."' order by bd_ix ASC ");//order by 가 regdate 로 되어 있던 것을 고침 kbk 13/02/15
	if($db->total){
		$banner_details = $db->fetchall();
	}
$clon_no = 0;
if(is_array($banner_details)){
	foreach($banner_details as $_key=>$_value){

		if($_key == 0) {
		$Contents01 .= "<tbody>";
		} else if($_key == 1){
		$Contents01 .= "<tfoot>";
		}
		
		$Contents01 .= "
				<tr bgcolor=#ffffff  class='clone_tr'>

					<td height='25' style='padding:20px 0;border-bottom:2px dashed #d3d3d3' >

					<input type=hidden name='bd_ix[]' class='bd_ix' value='".$banner_details[$_key][bd_ix]."' style='width:230px;' validation=false>
					 첨부파일 : <input type=file class='textbox' name='bd_file[]' id='bd_file' class='bd_file'  style='width:255px;'   ".(($banner_kind == "2" || $banner_kind == "3") ? "validation=false ":"validation=false")."  title='파일'> <span class='file_text helpcloud'  help_width='200' help_height='30' help_html=\"선택 해제후 저장하시면 해당이미지가 삭제되게 됩니다.\"><b>".$banner_details[$_key][bd_file]."</b><input type='checkbox' name='nondelete[".$banner_details[$_key][bd_ix]."]' id='non_delete_".$banner_details[$_key][bd_ix]."' value='1' checked><label for='non_delete_".$banner_details[$_key][bd_ix]."'>업로드된 파일유지</label></span>
					 <div class='check_goods'>
						<!--img src='../images/delete_check.png' alt='' class='delete_check' id='img_delete_0' style='display: none;'>
						<img src='../images/checkbox.png' style='vertical-align: middle; display: none;' alt='' class='checkbox_image_tick04' id='tick_img_basic_img_0'-->
						<input type='checkbox' name='basic_img' id='basic_img_0' value='0' class='select_check' checked='checked' style='display: none;'>
						<p>";
					if($banner_details[$_key][bd_file] && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/".$banner_details[$_key][banner_ix]."/".$banner_details[$_key][bd_file])){
					 
						$image_info = getimagesize ($DOCUMENT_ROOT.$admin_config[mall_data_root]."/images/banner/".$banner_details[$_key][banner_ix]."/".$banner_details[$_key][bd_file]);
						$Contents01 .= "
						<img src='".$admin_config[mall_data_root]."/images/banner/".$banner_details[$_key][banner_ix]."/".$banner_details[$_key][bd_file]."' style='vertical-align:middle;margin:3px;'  ".($_COOKIE[banner_image_view] == 1 ? "":"width=".($image_info[0]/2)."")." class='helpcloud' help_width='".($image_info[0]+20)."' help_height='".($image_info[1])."' help_html=\"<img src='".$admin_config[mall_data_root]."/images/banner/".$banner_details[$_key][banner_ix]."/".$banner_details[$_key][bd_file]."'  >\" style='cursor:pointer;' ><!--id='good_img_0' onclick=\"$('#img_file_0').trigger('click')\" -->";
					}else{
						//$Contents01 .= "	<img src='../images/goods_null.png' alt='' width='100%' id='good_img_0' onclick='$('#img_file_0').trigger('click')'  >";
					}

						$Contents01 .= "
							<input type='file' name='img_list[0]' id='img_file_0' style='width:0px;position:absolute;'>
						</p>
					</div>

					 <br><br>";
					
					$Contents01 .= "
					<div class='hidden_area' style='display:none;'>
					버튼이미지 : <input type=file class='textbox' name='bd_btn_out[]'  value='".$banner_details[$_key][bd_btn_out]."' style='width:230px;' validation=false>
					";
					 if($banner_details[$_key][bd_btn_out] && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/".$banner_details[$_key][banner_ix]."/".$banner_details[$_key][bd_btn_out])){
						$Contents01 .= "<b>".$banner_details[$_key][bd_btn_out]."</b><input type='checkbox' name='nondelete_out[".$banner_details[$_key][bd_ix]."]' id='non_delete_out_".$banner_details[$_key][bd_ix]."' value='1' checked><label for='non_delete_out_".$banner_details[$_key][bd_ix]."'>업로드된 파일유지</label></span>";

						$image_info = getimagesize ($DOCUMENT_ROOT.$admin_config[mall_data_root]."/images/banner/".$banner_details[$_key][banner_ix]."/".$banner_details[$_key][bd_btn_out]);
						$Contents01 .= "<br>
						<img src='".$admin_config[mall_data_root]."/images/banner/".$banner_details[$_key][banner_ix]."/".$banner_details[$_key][bd_btn_out]."' style='vertical-align:middle;margin:3px;'  ".($_COOKIE[banner_image_view] == 1 ? "":"width=".($image_info[0]/2)."")." class='helpcloud' help_width='".($image_info[0]+20)."' help_height='".($image_info[1])."' help_html=\"<img src='".$admin_config[mall_data_root]."/images/banner/".$banner_details[$_key][banner_ix]."/".$banner_details[$_key][bd_btn_out]."'  >\" style='cursor:pointer;' ><!--id='good_img_0' onclick=\"$('#img_file_0').trigger('click')\" -->";
					}
					$Contents01 .= "<br><br>";
					 

					$Contents01 .= "
					버튼 오버 이미지 : <input type=file class='textbox' name='bd_btn_over[]'  value='".$banner_details[$_key][bd_btn_over]."' style='width:230px;' validation=false>
					
					";
					if($banner_details[$_key][bd_btn_over] && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/".$banner_details[$_key][banner_ix]."/".$banner_details[$_key][bd_btn_over])){
						$Contents01 .= "<b>".$banner_details[$_key][bd_btn_over]."</b><input type='checkbox' name='nondelete_over[".$banner_details[$_key][bd_ix]."]' id='non_delete_over_".$banner_details[$_key][bd_ix]."' value='1' checked><label for='non_delete_over_".$banner_details[$_key][bd_ix]."'>업로드된 파일유지</label></span>";

						$image_info = getimagesize ($DOCUMENT_ROOT.$admin_config[mall_data_root]."/images/banner/".$banner_details[$_key][banner_ix]."/".$banner_details[$_key][bd_btn_over]);

						$Contents01 .= "<br>
						<img src='".$admin_config[mall_data_root]."/images/banner/".$banner_details[$_key][banner_ix]."/".$banner_details[$_key][bd_btn_over]."' style='vertical-align:middle;margin:3px;'  ".($_COOKIE[banner_image_view] == 1 ? "":"width=".($image_info[0]/2)."")." class='helpcloud' help_width='".($image_info[0]+20)."' help_height='".($image_info[1])."' help_html=\"<img src='".$admin_config[mall_data_root]."/images/banner/".$banner_details[$_key][banner_ix]."/".$banner_details[$_key][bd_btn_over]."'  >\" style='cursor:pointer;' ><!--id='good_img_0' onclick=\"$('#img_file_0').trigger('click')\" -->";
					}
					$Contents01 .= "<br><br>
					</div>
					 링&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;크 : <input type=text class='textbox bd_link' name='bd_link[]' id='bd_link' class='bd_link' value='".$banner_details[$_key][bd_link]."' style='width:248px;' ".(($banner_kind == "2" || $banner_kind == "3") ? "validation=true ":"validation=false")." title='링크'>
					 타 이 틀 : <input type=text class='textbox bd_title' name='bd_title[]' value='".$banner_details[$_key][bd_title]."' id='bd_title' class='bd_title' style='width:230px;' ".(($banner_kind == "2" || $banner_kind == "3") ? "validation=true ":"validation=false")." title='타이틀'>
					 노출순서 : <input type=text class='textbox bd_vieworder' name='bd_vieworder[]' value='".$banner_details[$_key][vieworder]."' id='bd_vieworder' class='bd_vieworder' style='width:50px;' validation=false title='노출순서'><br>
					 <div class='hidden_area' style='display:none;'>
					  <dl><dt style='float:left;'>브랜드(셀러) 선택 : </dt><dd style='float:left;'>".companyAuthList($banner_details[$_key][etc_code] , "validation=false title='입점업체' ", "company_id_".$_key,"company_id[]","com_name_".$_key)."</dd></dl>
					  </div>
					</td>
					";
					/*
					if($banner_details[$_key][bd_file] && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/".$banner_details[$_key][banner_ix]."/".$banner_details[$_key][bd_file])){
					//	exit;
					$image_info = getimagesize ($DOCUMENT_ROOT.$admin_config[mall_data_root]."/images/banner/".$banner_details[$_key][banner_ix]."/".$banner_details[$_key][bd_file]);
					$Contents01 .= "<td style='padding:5px;'><img src='".$admin_config[mall_data_root]."/images/banner/".$banner_details[$_key][banner_ix]."/".$banner_details[$_key][bd_file]."' style='vertical-align:middle;margin:3px;'  ".($_COOKIE[banner_image_view] == 1 ? "":"height=50")." class='helpcloud' help_width='".($image_info[0]+20)."' help_height='".($image_info[1])."' help_html=\"<img src='".$admin_config[mall_data_root]."/images/banner/".$banner_details[$_key][banner_ix]."/".$banner_details[$_key][bd_file]."' >\" style='cursor:pointer;'></td>";
					}
					*/

		$Contents01 .= "
					
					<td style='vertical-align:middle;padding:10px;'><img src='../images/".$admininfo["language"]."/btn_del.gif' alt='이미지삭제' id='delete_btn'  onclick=\"del_detail_img($(this).parent().parent())\" ".($_key == 0 ? "style='display:none;' ":"")."></td>
				  </tr>
				  
				  ";
		if($_key == 0) {
		$Contents01 .= "</tbody>";
		} else {
			$clon_no++;
		}
	}
} else {
		$Contents01 .= "
				 <tbody>
				  <tr bgcolor=#ffffff  class='clone_tr'>
					<td height='25' style='padding:10px 0; solid #d3d3d3;'>
					<input type=hidden name='bd_ix[]' value='' style='width:230px;' validation=false>
					 첨부파일 : <input type=file class='textbox' name='bd_file[]' style='width:255px;' ".(($banner_kind == "2" || $banner_kind == "3") ? "validation=false ":"validation=false")."  title='파일'> <br><br>
					 
					 <div class='hidden_area' style='display:none;'>
					 버튼이미지 : <input type=file class='textbox' name='bd_btn_out[]'  value='".$banner_details[$_key][bd_btn_out]."' style='width:230px;' validation=false><br><br>

					 버튼 오버 이미지 : <input type=file class='textbox' name='bd_btn_over[]'  value='".$banner_details[$_key][bd_btn_over]."' style='width:230px;' validation=false><br><br>
					 </div>
					 링&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;크 : <input type=text class='textbox bd_link' name='bd_link[]' value='' style='width:248px;' ".(($banner_kind == "2" || $banner_kind == "3") ? "validation=true ":"validation=false")." title='링크'>
					 타 이 틀 : <input type=text class='textbox bd_title' name='bd_title[]' value='' style='width:230px;' ".(($banner_kind == "2" || $banner_kind == "3") ? "validation=true ":"validation=false")." title='타이틀'>

					 노출순서 : <input type=text class='textbox bd_vieworder' name='bd_vieworder[]' value='' id='bd_vieworder' class='bd_vieworder' style='width:50px;' validation=false title='노출순서'>
					 <br><br>

					 <div class='hidden_area' style='display:none;'>
					 <dl><dt style='float:left;'>브랜드(셀러) 선택 : </dt><dd style='float:left;'>".companyAuthList($banner_details[$_key][company_id] , "validation=false title='입점업체' ", "company_id[]","company_id_0","com_name_0")."</dd></dl>
					 </div>
					</td>
					<td style='vertical-align:top;padding:10px;'><img src='../images/".$admininfo["language"]."/btn_del.gif' alt='이미지삭제' id='delete_btn'  style='display:none;' onclick=\"del_detail_img($(this).parent().parent())\"></td>
				  </tr>
				 </tbody>
				 ";
}
if($clon_no == 0){
$Contents01 .= "<tfoot>";
}
$Contents01 .= "
		</tfoot>
		</table>

		<div class='hidden_area' style='display:none;'>
			<table cellpadding=0 cellspacing=0 border='0'   id='changeButtonArea' ".(($banner_kind == "2" || $banner_kind == "3") ? "style='padding-top:30px;'":"style='display:none;padding-top:30px;' ").">
				<tr>
					<td style='padding:20px 0px;'>
					좌측 버튼파일(PNG) : <input type=file class='textbox' name='banner_btn_left' id='banner_btn_left' style='width:255px;'   title='파일'>  ";
					if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/banner_btn_left.png")){
						//$img_size = getimagesize($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img);
						//$file_size = filesize($DOCUMENT_ROOT.$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img);
						$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/banner_btn_left.png"."' style='vertical-align:middle;margin:5px 0px ;' >";
						$Contents01 .= "<input type='checkbox' name='use_banner_btn_left' id='use_banner_btn_left' value='1' checked><label for='use_banner_btn_left'>업로드된 파일유지</label>";
					}
					$Contents01 .= " <br><br>
					좌측 버튼파일 오버(PNG) : <input type=file class='textbox' name='banner_btn_left_on' id='banner_btn_left' style='width:255px;'   title='파일'>   ";
					if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/banner_btn_left_on.png")){
						//$img_size = getimagesize($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img);
						//$file_size = filesize($DOCUMENT_ROOT.$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img);
						$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/banner_btn_left_on.png"."' style='vertical-align:middle;margin:5px 0px ;' >";
						$Contents01 .= "<input type='checkbox' name='use_banner_btn_left_on' id='use_banner_btn_left_on' value='1' checked><label for='use_banner_btn_left_on'>업로드된 파일유지</label>";
					}
					$Contents01 .= "<br><br>
					우측 버튼파일(PNG) : <input type=file class='textbox' name='banner_btn_right' id='banner_btn_right' style='width:255px;'   title='파일'>  ";
					
					if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/banner_btn_right.png")){
						//$img_size = getimagesize($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img);
						//$file_size = filesize($DOCUMENT_ROOT.$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img);
						$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/banner_btn_right.png"."' style='vertical-align:middle;margin:5px 0px ;' >";
						$Contents01 .= "<input type='checkbox' name='use_banner_btn_right' id='use_banner_btn_right' value='1' checked><label for='use_banner_btn_right'>업로드된 파일유지</label>";
					}
					$Contents01 .= "<br><br>
					우측 버튼파일 오버(PNG) : <input type=file class='textbox' name='banner_btn_right_on' id='banner_btn_right_on' style='width:255px;'   title='파일'> ";
					
					if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/banner_btn_right_on.png")){
						//$img_size = getimagesize($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img);
						//$file_size = filesize($DOCUMENT_ROOT.$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img);
						$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/banner_btn_right_on.png"."' style='vertical-align:middle;margin:5px 0px ;' >";
						$Contents01 .= "<input type='checkbox' name='use_banner_btn_right_on' id='use_banner_btn_right_on' value='1' checked><label for='use_banner_btn_right_on'>업로드된 파일유지</label>";
					}
					$Contents01 .= "

					</td>
				  </tr>
				  <tr>
					<td style='padding:20px 0px;'>
					<input type=radio name='banner_btn_position' id='banner_btn_position_1' value='1' ".($banner_btn_position == "1" ? "checked":"")." ><label for='banner_btn_position_1'>배너 상단좌측</label>
					<input type=radio name='banner_btn_position' id='banner_btn_position_2' value='2' ".($banner_btn_position == "2" ? "checked":"")." ><label for='banner_btn_position_2'>배너 상단중앙</label>
					<input type=radio name='banner_btn_position' id='banner_btn_position_3' value='3' ".($banner_btn_position == "3" ? "checked":"")." ><label for='banner_btn_position_3'>배너 상단우측</label>

					<input type=radio name='banner_btn_position' id='banner_btn_position_4' value='4' ".(($banner_btn_position == "4" || $banner_btn_position == "") ? "checked":"")." ><label for='banner_btn_position_4'>배너 중단좌우측</label>

					<input type=radio name='banner_btn_position' id='banner_btn_position_5' value='5' ".($banner_btn_position == "5" ? "checked":"")." ><label for='banner_btn_position_5'>배너 하단좌측</label>
					<input type=radio name='banner_btn_position' id='banner_btn_position_6' value='6' ".($banner_btn_position == "6" ? "checked":"")." ><label for='banner_btn_position_6'>배너 하단중앙</label>
					<input type=radio name='banner_btn_position' id='banner_btn_position_7' value='7' ".($banner_btn_position == "7" ? "checked":"")." ><label for='banner_btn_position_7'>배너 하단우측</label>
					</td>
				  </tr>
			</table>
		</div>

			<table cellpadding=0 cellspacing=1 bgcolor='silver' width=100% id='image_banner' style='".(($banner_kind == '' || $banner_kind == 1) ? "":"display:none;")."'>
				<col width=20%>
				<col width=30%>
				<col width=20%>
				<col width=30%>
				<tr bgcolor=#ffffff >
					<td class='input_box_title' colspan=2> <b>기본이미지(PC) <img src='".$required3_path."'></b> </td>
					<td class='input_box_title' colspan=2> <b>기본이미지(MO) <img src='".$required3_path."'></b> </td>
				</tr>
				<tr bgcolor=#ffffff >
					<td class='input_box_item' style='padding:10px;vertical-align:top; max-width:300px;overflow-x: scroll;' colspan=2 > <input type=file class='textbox' name='banner_img' value='' ><br> ";
					if($banner_img != ""){
							if($banner_width == 0){
								$banner_width = $img_size[0];
							}
							if($banner_height == 0){
								$banner_height = $img_size[1];
							}
							//[Start] 특정 사이즈를 넘을 경우 사이즈 줄임 kbk 13/07/14
							if($banner_width>460) {
								$banner_w_rate=ceil(460*100/$banner_width);
								$banner_min_w=460;
								$banner_min_h=ceil($banner_height*$banner_w_rate/100);
							} else {
								$banner_min_w=$banner_width;
								$banner_min_h=$banner_height;
							}
							//[End] 특정 사이즈를 넘을 경우 사이즈 줄임 kbk 13/07/14
							if(substr_count($banner_img,'.swf') > 0){
								$Contents01 .= "<script language='javascript'>generate_flash('".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img."', '".$banner_min_w."', '".$banner_min_h."');</script>";
							}else{

								$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img."' style='vertical-align:middle;margin:5px 0px ;' width='".$banner_min_w."' height='".$banner_min_h."'>";
								//$DOCUMENT_ROOT.$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img
								if($banner_img && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img)){
									$img_size = getimagesize($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img);
									$file_size = filesize($DOCUMENT_ROOT.$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img);
								}
								//print_r($img_size);
								
								$Contents01 .= "<br><div style='padding:10px 10px 10px 0px;'> 가로 : ".$img_size[0]."px &nbsp;&nbsp;&nbsp;세로 : ".$img_size[1]."px &nbsp;&nbsp;&nbsp; 용량 : ".$file_size." Byte</div>";

							
							}
					}else{
						$Contents01 .= "";
					}
					
					$Contents01 .= " 
					</td>
					<td class='input_box_item' style='padding:10px;vertical-align:top;' colspan=2> <input type=file class='textbox' name='banner_img_on' value='' ><br> ";
					if($banner_img_on != ""){
							if(substr_count($banner_img_on,'.swf') > 0){
								$Contents01 .= "<script language='javascript'>generate_flash('".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img_on."', '".$banner_min_w."', '".$banner_min_h."');</script>";
							}else{

								$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img_on."' style='vertical-align:middle;margin:5px 0px ;' width='".$banner_min_w."' height='".$banner_min_h."'>";
							}
							
							if($banner_img_on && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/".$banner_details[$_key][banner_ix]."/".$banner_img_on)){
								$img_size = getimagesize($DOCUMENT_ROOT.$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img_on);
								//print_r($img_size);
								$file_size = filesize($DOCUMENT_ROOT.$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img_on);
								$Contents01 .= "<br><div style='padding:10px 10px 10px 0px;'>  가로 : ".$img_size[0]."px &nbsp;&nbsp;&nbsp;세로 : ".$img_size[1]."px &nbsp;&nbsp;&nbsp; 용량 : ".$file_size." Byte</div>";
							}

					}else{
						$Contents01 .= "";
					}					
					
				$Contents01 .= "
					</td>
				  </tr>
				<tr bgcolor=#ffffff >
					<td class='input_box_title'> 링크 URL </td>
					<td class='input_box_item' colspan=3 style='padding-top:10px;'>
						<table>
							<tr>
								<td><input type=text class='textbox' name='banner_link' value='".$banner_link."' title='배너링크'    style='width:360px;' ></td>
								<td>
									<select name='banner_target' style='height:22px;' align=absmiddle>
										<option value=''>타겟을 선택하세요</option>
										<option value='_SELF' ".($banner_target == "_SELF" ? "selected":"").">현재창</option>
										<option value='_BLANK' ".($banner_target == "_BLANK" ? "selected":"").">새창</option>
									</select>
								</td>
							</tr>
							<tr>
								<td colspan=2>
									<span class='small' style='line-height:200%' ><!--* 이미지일 경우에는 링크를 정확하게 입력하여 주시고 플래쉬의 경우는 '/' 만입력하시면 됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				
				 <!--tr>
					<td class='search_box_title' >  담당 MD</td>
					<td class='search_box_item' > ".makeMDSelectBox($db,'md_id',$md_id,'')."</td>
					<td class='search_box_title'> 목표유입</td>
					<td class='input_box_item'><input type=text class='textbox number' name='goal_cnt' value='".$goal_cnt."' title='목표유입수' validation=true style='width:40px;'> 번 <span class=small></span></td>
				</tr-->
			</table>
			
			<table cellpadding=0 cellspacing=1 bgcolor='silver' width=100% id='movie_banner' style='".($banner_kind == 4 ? "display:block;":"display:none;")."'>
				<col width=20%>
				<col width=30%>
				<col width=20%>
				<col width=30%>
				
				<tr bgcolor=#ffffff >
					<td class='input_box_title'> 동영상 HTML </td>
					<td class='input_box_item' colspan=3 style='padding:10px;'>
						<table width=100%>
							<tr>
								<td><textarea class='textbox' name='banner_html'  title='배너 html'    style='width:90%;height:70px;padding:2px;' >".$banner_html."</textarea></td>
								
							</tr>
							<tr>
								<td >
									<span class='small' style='line-height:200%' >동영상의 경우는 Link 방식만 지원합니다. HTML 형태롤 입력해주세요 (유투브, 네이버, vimeo 등등)</span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr bgcolor=#ffffff >
					<td height='5px;' colspan=4></td>
				</tr>
				<tr bgcolor=#ffffff >
					<td class='input_box_title'> 동영상 HTML(모바일) </td>
					<td class='input_box_item' colspan=3 style='padding:10px;'>
						<table width=100%>
							<tr>
								<td><textarea class='textbox' name='banner_html_m'  title='배너 html'    style='width:90%;height:70px;padding:2px;' >".$banner_html_m."</textarea></td>
								
							</tr>
							<tr>
								<td >
									<span class='small' style='line-height:200%' >동영상의 경우는 Link 방식만 지원합니다. HTML 형태롤 입력해주세요 (유투브, 네이버, vimeo 등등)</span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			";

	$Contents01 .= "
			</td>
		  </tr>";
if(false){
$Contents01 .= "
	   <tr height=27 >
		  <td class='input_box_title'> <b>노출기간 <img src='".$required3_path."'></b></td>
		  <td class='input_box_item' colspan=3>
			<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff >
				<tr>
					<TD width=45% nowrap>
					<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY></SELECT> 년
					<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월
					<SELECT name=FromDD></SELECT> 일
					<SELECT name=FromHH>";
					for($i=0;$i < 24;$i++){
	$Contents01 .= "<option value='".$i."'  ".($sTime == $i ? "selected":"").">".$i."</option>";
					}
	$Contents01 .= "
					</SELECT> 시
					<SELECT name=FromMI>";
					for($i=0;$i < 60;$i++){
	$Contents01 .= "<option value='".$i."' ".($sMinute == $i ? "selected":"").">".$i."</option>";
					}
	$Contents01 .= "
					</SELECT> 분
					</TD>
					<TD width=10% align=center> ~ </TD>
					<TD width=45% nowrap>
					<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년
					<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월
					<SELECT name=ToDD></SELECT> 일
					<SELECT name=ToHH>";
					for($i=0;$i < 24;$i++){
	$Contents01 .= "<option value='".$i."'  ".($eTime == $i ? "selected":"").">".$i."</option>";
					}
	$Contents01 .= "
					</SELECT> 시
					<SELECT name=ToMI>";
					for($i=0;$i < 60;$i++){
	$Contents01 .= "<option value='".$i."'  ".($eMinute == $i ? "selected":"").">".$i."</option>";
					}
	$Contents01 .= "
					</SELECT> 분
					</TD>
				</tr>
			</table>
		  </td>
		</tr>";

$Contents01 .= "
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 배너링크 : </td>
	    <td class='input_box_item' colspan=3>
			<table>
				<tr>
					<td><input type=text class='textbox' name='banner_link' value='".$banner_link."' title='배너링크' validation=true  style='width:360px;' ></td>
					<td>
						<select name='banner_target' style='height:22px;' align=absmiddle>
							<option value=''>타겟을 선택하세요</option>
							<option value='_SELF' ".($banner_target == "_SELF" ? "selected":"").">현재창</option>
							<option value='_BLANK' ".($banner_target == "_BLANK" ? "selected":"").">새창</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan=2>
						<span class='small' style='line-height:200%' ><!--* 이미지일 경우에는 링크를 정확하게 입력하여 주시고 플래쉬의 경우는 '/' 만입력하시면 됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span>
					</td>
				</tr>
			</table>

	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 가로 : </td>
	    <td class='input_box_item'><input type=text class='textbox number' name='banner_width' value='".$banner_width."' title='배너가로' validation=true style='width:120px;'>  <span class=small></span></td>

	    <td class='input_box_title'> 세로 : </td>
	    <td class='input_box_item'><input type=text class='textbox number' name='banner_height' value='".$banner_height."' title='배너세로' validation=true style='width:120px;'>  <span class=small></span></td>
	  </tr>	  ";
}
	  if($banner_img != "" && false){
	$Contents01 .= "	<tr bgcolor=#ffffff >
	    <td class='input_box_title'> 이미지 정보  </td>
	    <td class='input_box_item' colspan=3>가로 : ".$img_size[0]."px &nbsp;&nbsp;&nbsp;세로 : ".$img_size[1]."px &nbsp;&nbsp;&nbsp; 용량 : ".$file_size." Byte</td>
	  </tr>";
	  }
	 $Contents01 .= "
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> 가로</td>
		<td class='input_box_item'><input type=text class='textbox number' name='banner_width' value='".$banner_width."' title='배너가로' validation=false style='width:120px;'> px  또는 % 를 함께 입력 <span class=small></span></td>				
		<td class='input_box_title'> 세로</td>
		<td class='input_box_item'><input type=text class='textbox number' name='banner_height' value='".$banner_height."' title='배너세로' validation=false style='width:120px;'> px  또는 % 를 함께 입력 <span class=small></span></td>
	 </tr>";

	if($agent_type == "M")
	{
		//모바일용 상태
		 $Contents01 .= "
		 <tr bgcolor=#ffffff >
			<td class='input_box_title'> 배너설명  </td>
			<td class='input_box_item'>
			<textarea name='banner_desc' class='textbox' style='width:60%; height:100px;'>{$banner_desc}</textarea>
			<span class=small valign=top style='padding-top:10px;position:absolute'>BRAND명|상품명|설명|기타설명|가격|할인가격|노출여부(O,X)</span></td>
			<td class='input_box_title'> 배너설명 설정 </td>
			<td class='input_box_item'>
				<input type='radio' name='s_desc' id='s_desc_L' value='L' ".("L" == $s_desc || "" == $s_desc  ? "checked":"")."><label for='s_desc_L'> 좌측정렬</label>
				<input type='radio' name='s_desc' id='s_desc_C' value='C' ".("C" == $s_desc ? "checked":"")."><label for='s_desc_C'> 가운데정렬</label>
				<input type='radio' name='s_desc' id='s_desc_R' value='R' ".("R" == $s_desc ? "checked":"")."><label for='s_desc_R'> 우측정렬</label><br><br>
				진하게<input type='checkbox' name='b_desc' id='b_desc' ".("Y" == $b_desc ? "checked":"").">
				기울기<input type='checkbox' name='i_desc' id='i_desc' ".("Y" == $i_desc ? "checked":"").">
				밑줄<input type='checkbox' name='u_desc' id='u_desc' ".("Y" == $u_desc ? "checked":"").">
				글자색 <input type='text' name='c_desc' id='c_desc' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_desc ? "#000000":$c_desc)."'> 
			</td>
		  </tr>";
	}
	else
	{
		//기본 상태
//		$Contents01 .= "
//		 <tr bgcolor=#ffffff >
//			<td class='input_box_title'> 배너설명  </td>
//			<td class='input_box_item' colspan=3><input type=text class='textbox' name='banner_desc' value='".$banner_desc."' style='width:530px;'> <span class=small></span></td>
//		  </tr>";
        $Contents01 .= "
		 <tr bgcolor=#ffffff >
			<td class='input_box_title'> 배너설명  </td>
			<td class='input_box_item'>
				<textarea name='banner_desc' class='textbox' style='width:60%; height:100px;'>{$banner_desc}</textarea>
			</td>
			<td class='input_box_title'> 배너설명 설정  </td>
			<td class='input_box_item'>
				<input type='radio' name='s_desc' id='s_desc_L' value='L' ".("L" == $s_desc || "" == $s_desc  ? "checked":"")."><label for='s_desc_L'> 좌측정렬</label>
				<input type='radio' name='s_desc' id='s_desc_C' value='C' ".("C" == $s_desc ? "checked":"")."><label for='s_desc_C'> 가운데정렬</label>
				<input type='radio' name='s_desc' id='s_desc_R' value='R' ".("R" == $s_desc ? "checked":"")."><label for='s_desc_R'> 우측정렬</label><br><br>
				진하게<input type='checkbox' name='b_desc' id='b_desc' ".("Y" == $b_desc ? "checked":"").">
				기울기<input type='checkbox' name='i_desc' id='i_desc' ".("Y" == $i_desc ? "checked":"").">
				밑줄<input type='checkbox' name='u_desc' id='u_desc' ".("Y" == $u_desc ? "checked":"").">
				글자색 <input type='text' name='c_desc' id='c_desc' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_desc ? "#000000":$c_desc)."'> 
			</td>
		  </tr>
		  <tr bgcolor=#ffffff >
			<td class='input_box_title'> 배너설명(모바일)  </td>
			<td class='input_box_item'>
				<textarea name='banner_desc_m' class='textbox' style='width:60%; height:100px;'>{$banner_desc_m}</textarea>
			</td>
			<td class='input_box_title'> 배너설명 설정(모바일)  </td>
			<td class='input_box_item'>
				<input type='radio' name='s_desc_m' id='s_desc_m_L' value='L' ".("L" == $s_desc_m || "" == $s_desc_m  ? "checked":"")."><label for='s_desc_m_L'> 좌측정렬</label>
				<input type='radio' name='s_desc_m' id='s_desc_m_C' value='C' ".("C" == $s_desc_m ? "checked":"")."><label for='s_desc_m_C'> 가운데정렬</label>
				<input type='radio' name='s_desc_m' id='s_desc_m_R' value='R' ".("R" == $s_desc_m ? "checked":"")."><label for='s_desc_m_R'> 우측정렬</label><br><br>
				진하게<input type='checkbox' name='b_desc_m' id='b_desc_m' ".("Y" == $b_desc_m ? "checked":"").">
				기울기<input type='checkbox' name='i_desc_m' id='i_desc_m' ".("Y" == $i_desc_m ? "checked":"").">
				밑줄<input type='checkbox' name='u_desc_m' id='u_desc_m' ".("Y" == $u_desc_m ? "checked":"").">
				글자색 <input type='text' name='c_desc_m' id='c_desc_m' style='width:50px' maxlength='7' data-jscolor='{required:false, format:hex}' value='".("" == $c_desc_m ? "#000000":$c_desc_m)."'> 
			</td>
		  </tr>";
	}
	$Contents01 .= "
	   <tr>
			<td class='search_box_title' >  담당 MD</td>
			<td class='search_box_item' colspan='3'> ".MDSelect($md_mem_ix)."</td>
			<!--
			<td class='search_box_title'> 목표유입</td>
			<td class='input_box_item'><input type=text class='textbox number' name='goal_cnt' value='".$goal_cnt."' title='목표유입수' validation=false style='width:40px;'> 번 <span class=small></span></td>
			-->
		</tr>";
if($admininfo["admin_level"] == 9 && ($admininfo[mall_type] == "B"  || $admininfo[mall_type] == "O")){
 $Contents01 .= "
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 사용유무 </td>
	    <td class='input_box_item' >
	    	<input type=radio name='disp' id='disp_1' value='1' ".($disp == "1" || $disp == "" ? "checked":"")."><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ".($disp == "0" ? "checked":"")."><label for='disp_0'>사용하지않음</label>
	    </td>
		<td class='input_box_title'> 등록(관리)업체 </td>
	    <td class='input_box_item' >".companyAuthList($company_id)."
		<!--table cellpadding=0 cellspacing=0>
			<tr>
				<td><input type=hidden class='textbox' name='company_id' id='company_id'  value='".$company_id."' ></td>
				<td><input type=text class='textbox' name='com_name' id='com_name' value='".$com_name."' style='width:130px;' readonly></td>
				<td style='padding-left:5px;'><img src='../v3/images/".$admininfo["language"]."/btn_seller_search.gif' align=absmiddle onclick=\"PoPWindow('../seller_search.php?code=".$db->dt[code]."',600,380,'sendsms')\"  style='cursor:pointer;'></td>
			</tr>
		</table-->
		</td>
	  </tr>";
}else{
 $Contents01 .= "
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 사용유무 </td>
	    <td class='input_box_item' colspan=3>
	    	<input type=radio name='disp' id='disp_1' value='1' ".($disp == "1" || $disp == "" ? "checked":"")."><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ".($disp == "0" ? "checked":"")."><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>";
}
 $Contents01 .= "
	  </table>";



if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
}

$Contents = "<form name='banner_frm' action='../display/banner.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' target='iframe_act'>
<input name='act' type='hidden' value='$act'>
<input name='mode' type='hidden' value='$mode'>
<input name='mmode' type='hidden' value='$mmode'>
<input name='banner_ix' type='hidden' value='$banner_ix'>
<input name='b_banner_ix' type='hidden' value='$banner_ix'>
<input name='agent_type' type='hidden' value='$agent_type'>
<input name='SubID' type='hidden' value='$SubID'>";
$Contents = $Contents."<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";

$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."</table >";
$Contents = $Contents."</form>";
//TODO: IFRAME내용 퍼블리싱하기~
//$Contents .= "<iframe src='".$_SERVER["HTTP"]."/admin/display/banner_image_map.php' width='100%' height='600px' style='border:0px;'></iframe>";
/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >배너를 등록하신후 치환함수를 이용해 디자인에 적용하실 수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >페이지를 선택하시면 추후 배너관리시 편리합니다.</td></tr>
</table>
";*/
//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');


//$Contents .= HelpBox("배너관리", $help_text,70);

if($banner_kind == 5){
    $banner_kind_str = "ChangeBannerKind(5);";
}

$Script = "<script language='javascript' src='../include/DateSelect.js'></script>
<script language='javascript' src='banner.write.js'></script>
<script language='javascript' src='./color/jscolor.js'></script>
<Script Language='JavaScript'>
var eqIndex = '$clon_no';
$(document).ready(function () {
    jscolor.presets.default = {
		width: 141,               // make the picker a little narrower
		position: 'right',        // position it to the right of the target
		previewPosition: 'right', // display color preview on the right
		previewSize: 40,          // make the color preview bigger
		palette: [
			'#000000', '#7d7d7d', '#870014', '#ec1c23', '#ff7e26',
			'#fef100', '#22b14b', '#00a1e7', '#3f47cc', '#a349a4',
			'#ffffff', '#c3c3c3', '#b87957', '#feaec9', '#ffc80d',
			'#eee3af', '#b5e61d', '#99d9ea', '#7092be', '#c8bfe7',
		],
	};
		
	var copy_text;
	$('#flash_addbtn').click(function(){
		var key = $('input[id^=company_id_]').length;
		var newRow = $('#move_banner_table tbody tr.clone_tr:last').clone(true).appendTo('#move_banner_table tfoot');  
		newRow.find('.file_text').text('');
		newRow.find('.bd_link').val('');
		newRow.find('.bd_title').val('');
		newRow.find('.bd_vieworder').val('');
		newRow.find('.bd_ix').val('');
		newRow.find('#delete_btn').show();

		newRow.find('p > img').remove();
		
		newRow.find('input[id^=company_id_]').val('').attr('id','company_id_'+key);
		newRow.find('input[id^=com_name_]').val('').attr('id','com_name_'+key).attr('onclick',\"ShowModalWindow('../seller_search.php?code=&select_id=company_id[]&input_id=company_id_\"+key+\"&input_name=com_name_\"+key+\"&type=list');\");	//attr('name','com_name_'+key).
		newRow.find('img.com_search_btn').val('').attr('onclick',\"ShowModalWindow('../seller_search.php?code=&select_id=company_id[]&input_id=company_id_\"+key+\"&input_name=com_name_\"+key+\"&type=list');\");
		 
	});

	$('#flash_delbtn').click(function(){
		var len = $('#move_banner_table .clone_tr').length;
		if(len > 1){
			eqIndex--;
			$('#move_banner_table .clone_tr:last').remove();
		}else{
			return false;
		}
	});

    ".$banner_kind_str."

});
$(function() {
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
		//}else{
			//$('#end_datepicker').datepicker('setDate','+0d');
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

	//$('#end_timepicker').timepicker();
});

function select_date(FromDate,ToDate,dType) {
	var frm = document.serchform;

	$(\"#start_datepicker\").val(FromDate);
	$(\"#end_datepicker\").val(ToDate);
}

function ChangeBannerKind(banner_kind){
	if(banner_kind == 1){
		$('#move_banner_table').hide();
		$('#image_banner').show();
		$('#movie_banner').hide();

		$('#flash_addbtn').hide();
		$('input[name^=banner_width]').attr('validation','false');
		$('input[name^=banner_height]').attr('validation','false');
		
		$('input[class^=bd_file]').attr('validation','false');
		$('input[class^=bd_title]').attr('validation','false');
		$('input[class^=bd_link]').attr('validation','false');

		$('select[name=change_effect]').attr('validation','false');
		$('#changeButtonArea').hide();
		   

	}else if(banner_kind == 2 || banner_kind == 3 || banner_kind == 5){
		$('#move_banner_table').show();
		$('#image_banner').hide();
		$('#flash_addbtn').show();
		$('#movie_banner').hide();

		$('input[name^=banner_width]').attr('validation','false');
		$('input[name^=banner_height]').attr('validation','false');

		$('input[class^=bd_file]').attr('validation','false');
		$('input[class^=bd_title]').attr('validation','true');
		$('input[class^=bd_link]').attr('validation','true');
		
		if(banner_kind == 2){
			$('select[name=change_effect]').attr('validation','true');
		}else{
			$('select[name=change_effect]').attr('validation','false');
		}
		//if(banner_kind == 2 || banner_kind == 3){
		if(banner_kind == 2 || banner_kind == 3 || banner_kind == 5){
			$('#changeButtonArea').show();
		}

	}else if(banner_kind == 4){
		$('#move_banner_table').hide();
		$('#image_banner').hide();
		$('#movie_banner').show();
		$('#flash_addbtn').hide();

		$('input[name^=banner_width]').attr('validation','false');
		$('input[name^=banner_height]').attr('validation','false');

		$('input[class^=bd_file]').attr('validation','false');
		$('input[class^=bd_title]').attr('validation','false');
		$('input[class^=bd_link]').attr('validation','false');

		$('select[name=change_effect]').attr('validation','false');

		$('#changeButtonArea').hide();

	}

}

function loadBannerPosition(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;

	var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
	//document.write('banner_position.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
	window.frames['act'].location.href = '../display/banner_position.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;


}


function loadCategory(obj,target) {
	
	var trigger = obj.find('option:selected').val();
	var form = obj.closest('form').attr('name');
	var depth = obj.attr('depth');//sel.getAttribute('depth');
	//alert(trigger+':::'+depth);
	if(trigger == ''){
		if(depth == 0){
			$('#display_cid').val('');
		}else{
			$('#display_cid').val($('.display_cid[depth='+(depth-1)+']').val());
		}
	}else{
		$.ajax({ 
			type: 'GET', 
			data: {'return_type': 'json', 'form':form, 'trigger':trigger, 'depth':depth, 'target':target},
			url: '../product/category.load.php',  
			dataType: 'json', 
			async: true, 
			error: function(){ 
				//alert('error');
			},  
			beforeSend: function(){ 
				
			},  
			success: function(datas){
				$('select[class=display_cid]').each(function(){
					if(parseInt($(this).attr('depth')) > parseInt(depth)){
						$(this).find('option').not(':first').remove();
					}
				});
				 
				if(datas != null){
					$.each(datas, function(i, data){ 
							$('select[name='+target+']').append(\"<option value='\"+data.cid+\"'>\"+data.cname+\"</option>\");
					});  
				}
				$('#display_cid').val(trigger);
			} 
		}); 
	}
 
}

function del_detail_img(tg) {//이미지 정보 삭제 kbk 13/07/11
	var bd_ix=$(tg).find('.bd_ix').eq(0).val();
	var banner_ix='".$banner_ix."';
	if(bd_ix!='') {
		$.ajax({
			type: 'GET',
			data:
				{'act': 'del_detail_img','bd_ix':bd_ix,'banner_ix':banner_ix},
			url: '/admin/display/banner.act.php',
			dataType: 'html',
			async: false,
			beforeSend: function(){
			},
			success: function(datas){
				if(datas == null){
					alert('정보를 찾을 수 없습니다.');
				}else{
					if(datas=='Y') {
						$(tg).remove();
						alert('해당 이미지 정보를 삭제하였습니다.');
					} else {
						alert('요청한 동작을 실행하지 못하였습니다.');
					}
				}
			}

		});
	} else { 
		$(tg).remove();
	}
}
</script>
";

if($agent_type == "M"  || $agent_type == "mobile"){
	if($mmode == "pop"){

		$P = new ManagePopLayOut();
		$P->addScript = $Script;
		$P->OnloadFunction = "";
		$P->strLeftMenu = mshop_menu();
		$P->Navigation = $navigation;
		$P->title = $title;
		$P->NaviTitle = $title;
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}else{
		$P = new LayOut();
		$P->addScript = $Script;
		$P->Navigation = $navigation;
		$P->title = $title;
		$P->strLeftMenu = mshop_menu();
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}
}else{

	if($mmode == "pop"){

		$P = new ManagePopLayOut();
		$P->addScript = $Script;
		$P->OnloadFunction = "";
		$P->strLeftMenu = display_menu();
		$P->Navigation = "프로모션/전시 > 배너관리 > 통합배너관리 등록/수정";
		$P->title = "통합배너관리 등록/수정";
		$P->NaviTitle = "통합배너관리 등록/수정";
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}else{
		$P = new LayOut();
		$P->addScript = $Script;
		$P->strLeftMenu = display_menu();
		$P->Navigation = "프로모션/전시 > 배너관리 > 통합배너관리 등록/수정";
		$P->OnloadFunction = "";
		$P->title = "통합배너관리 등록/수정";
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}
}

function displayCategorySelect($cid, $default_text="카테고리 선택하기"){
	$mdb = new Database;

	$sql = "select cname,cid from shop_category_info where depth = '0' and category_use = 1 order by vlevel1";
	$mdb->query($sql);
	$mstring ="<select name='display_cid' class='select_box' style='font-size:12px;font-family;돋움' >\n";
	$mstring .="<option value=''>".$default_text."</option>\n";
	for($i=0;$i<$mdb->total;$i++){
		$mdb->fetch($i);
		if(substr($mdb->dt[cid],0,3) == $cid){
			$mstring .="<option value='".substr($mdb->dt[cid],0,3)."' selected>".$mdb->dt[cname]."</option>\n";
		}else{
			$mstring .="<option value='".substr($mdb->dt[cid],0,3)."'>".$mdb->dt[cname]."</option>\n";
		}
	}
	$mstring .="</select>\n";

	return $mstring;
}



/*

create table shop_bannerinfo (
banner_ix int(4) unsigned not null auto_increment  ,
banner_name varchar(20) null default null,
banner_link varchar(255)  null default null,
banner_target varchar(20) null default null,
banner_desc varchar(255)  null default null,
regdate datetime not null,
primary key(banner_ix));
*/
?>
