<?
include("../class/layout.class");
include_once("../display/display.lib.php");
if(!$agent_type){
	$agent_type = "W";
}

$db = new Database();

if($db->dbms_type == "mysql"){
	if(!$db->mysql_table_exists("shop_bannerinfo")){
		$sql = "create table shop_bannerinfo (
		banner_ix int(4) unsigned not null auto_increment  ,
		banner_name varchar(20) null default null,
		banner_link varchar(255)  null default null,
		banner_target varchar(20) null default null,
		banner_desc varchar(255)  null default null,
		banner_img varchar(255)  null default null,
		regdate datetime not null,
		primary key(banner_ix)
		)TYPE=MyISAM COMMENT='배너정보' ;";

		$db->query($sql);
	}
}


/**
 * 배너 검색 부분
 */

if ($use_sdate == "" || $use_edate == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

//	$sDate = date("Y-m-d");
//	$sDate = date("Y-m-d", $before10day);
//	$eDate = date("Y-m-d");
	if($search_date){
	$use_sdate = date("Y-m-d", $before10day);
	$use_edate = date("Y-m-d");
	}
}else{
	/*
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;
	*/
}
$vdate = date("Y-m-d", time());
$today = date("Y-m-d", time());
$vyesterday = date("Y-m-d", time()-84600);
$voneweekago = date("Y-m-d", time()-84600*7);
$vtwoweekago = date("Y-m-d", time()-84600*14);
$vfourweekago = date("Y-m-d", time()-84600*28);
$vyesterday = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
$voneweekago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
$v15ago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
$vfourweekago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
$vonemonthago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

 
$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
        <tr>
			<td>
    <form name='search_banner' id='search_banner'><input type='hidden' name='cid2' value='".$cid2."'><input type='hidden' name='depth' value='".$depth."'>
        <table border='0' cellpadding='0' cellspacing='0' width='100%' align='left'>
            <tr>
				<td align='left' style='padding-bottom:5px;'>								
					<div class='tab' style='width:100%;height:30px;margin:0px;'>
					<table width='100%' class='s_org_tab'>				
					<tr>							
						<td class='tab' >
							<table id='tab_1' class=on>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='banner.php'\">배너 전시관리</td>
								<th class='box_03'></th>							
							</tr>
							</table>
							<!--table id='tab_2' ".$tabmenu_class2.">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"alert('배너 결과분석 리포트는 준비중입니다.');/*document.location.href='banner.report.php'*/\">배너 결과분석</td>
								<th class='box_03'></th>				
							</tr>
							</table-->
						</td>							
						<td align='right'>
							<!--a href='#;' onclick='FnDisplayWrite()'><img src='../images/".$admininfo["language"]."/btn_disp_write.gif' align=absmiddle ></a>&nbsp;
							<a href='#;' onclick='location.href=\"display_div.php?display_div=".$display_div."\"'><img src='../images/".$admininfo["language"]."/btn_disp_div.gif' align=absmiddle ></a-->";

if($admininfo[admin_level]  > 8){
	$Contents02 .= "
					<a href='./banner_category.php'><img src='../images/".$admininfo["language"]."/btn_banner_group.gif' align=absmiddle></a>";
}
$Contents02 .= "</td>
					</tr>
					</table>										
					</div>					
				</td>
			</tr>
			<tr>
                <td style='width:100%;' valign=top colspan=3>
                    <table width=100%  border=0>
                        <!--tr height=25>
                            <td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>배너 목록 검색하기</b></td>
                        </tr-->
						<tr>
							<td align='left' colspan=9 style='padding:0px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 20px;'>
							<table cellpadding=0 cellspacing=0><tr><td><img src='../image/title_head.gif' align=absmiddle> <b>배너목록 검색하기</b></td><td></td></tr></table></div>")."</td>
						  </tr>
                        <tr>
                            <td align='left' colspan=2 height=50 width='100%' valign=top style='padding-top:5px;'>
                                <table class='box_shadow' style='width:100%;' align=left cellpadding='0' cellspacing='0' border='0'>
                                    <tr>
                                        <th class='box_01'></th>
                                        <td class='box_02'></td>
                                        <th class='box_03'></th>
                                    </tr>
                                    <tr>
                                        <th class='box_04'></th>
                                        <td class='box_05' valign=top>
                                            <TABLE height=20 cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0>
                                                <TR>
                                                    <TD bgColor=#ffffff style='padding:0 0 0 0;height:30px;'>
                                                        <table cellpadding=2 cellspacing=1 width='100%' class='search_table_box'>
															<col width='15%'>
															<col width='35%'>
															<col width='15%'>
															<col width='35%'>";
															if($_SESSION["admin_config"][front_multiview] == "Y"){
															$Contents02 .= "
															<tr>
																<td class='search_box_title' > 프론트 전시 구분</td>
																<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
															</tr>";
															}
															$Contents02 .= "
															<tr>
                                                                <th class='search_box_title' >배너위치</th>
                                                                <td class='search_box_item' >
                                                                     ".getBannerFirstDIV($banner_page)."
                                                                     ".bannerPosition($banner_page, $banner_position)."
                                                                </td>
                                                                <th class='search_box_title' >배너명</th>
                                                                <td class='search_box_item'  >
                                                                    <input type=text class=textbox name='search_text' value='".$search_text."' style='width:200px;' >
                                                                </td>
                                                            </tr>
															<tr>
																<td class='search_box_title' >  카테고리선택</td>
																<td class='search_box_item' colspan=3>
																	<table border=0 cellpadding=0 cellspacing=0>
																		<tr>
																			<td style='padding-right:5px;'>".getCategoryList3("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid2)."</td>
																			<td style='padding-right:5px;'>".getCategoryList3("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid2)."</td>
																			<td style='padding-right:5px;'>".getCategoryList3("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid2)."</td>
																			<td>".getCategoryList3("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $cid2)."</td>
																		</tr>
																	</table>
																</td>
															</tr>
															<tr>
																<td class='search_box_title' >  진행상태</td>
																<td class='search_box_item'>".makeRadioTag($arr_display_status, "srch_status", $srch_status)."</td>
																<td class='search_box_title' >  전시유무</td>
																<td class='search_box_item'>".makeRadioTag($arr_display_disp, "srch_disp", $srch_disp)."</td>
															</tr>
															<tr>
																<td class='search_box_title' nowrap>
																<label for='search_date'><b>날짜 검색</b></label><input type='checkbox' name='search_date' id='search_date' value='1' onclick='ChangeRegistDate(document.search_banner);' ".(($search_date==1)?"checked":"").">
																<select name='date_type'>
																	<option value='use' ".(($date_type == 'use')?"selected":"").">만료기간</option>
																	<option value='reg' ".(($date_type == 'reg')?"selected":"").">등록일자</option>
																</select>
															  </td>
															  <td class='search_box_item'  colspan=3>
															   ".search_date('use_sdate','use_edate',$use_sdate,$use_edate,'N','D')."	";
																
																$Contents02 .= "
															  </td>
															</tr>
															<tr>
																<td class='search_box_title' > 담당 MD</td>
																<td class='search_box_item' colspan=3> ".MDSelect($md_code,"md_code","md_name","","")."</td>
															</tr>

                                                        </table>
                                                    </TD>
                                                </TR>
                                            </TABLE>
                                        </td>
                                        <th class='box_06'></th>
                                    </tr>
                                    <tr>
                                        <th class='box_07'></th>
                                        <td class='box_08'></td>
                                        <th class='box_09'></th>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan=3 align=center  style='padding:10px 0 0 0'>
                                <input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        </form>
		</td>
    </tr>
";

$Contents02 .= "        
       
	  <tr >
			<td align='left' colspan=9> ".GetTitleNavigation("배너관리", "전시관리 > 배너관리 ")."</td>
	  </tr>
		<tr>
			<td align='right' colspan=9 style='padding-bottom:10px;'></td>
		</tr>
	  <tr>
		<td align='left' colspan='7' style='padding-bottom:15px;'>
		<div class='tab'>
			<table class='s_org_tab' width=100%>
			<col width='*'>
			<col width='100'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".($banner_page == "" ? "class='on'":"")." >
					<tr>
						<th class='box_01'></th>
						<td class='box_02'><a href='banner.php?SubID=SM22464243Sub'>전체</a></td>
						<th class='box_03'></th>
					</tr>
					</table>";
$sql = 	"SELECT * FROM shop_banner_div where disp=1 and agent_type = '".$agent_type."' ORDER BY div_ix ASC ";

$db->query($sql);
for($i=0;($i < 7 && $i < $db->total);$i++){
	$db->fetch($i);
$Contents02 .= "<table id='tab_".($i+2)."' ".($banner_page == $db->dt[div_ix] ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?banner_page=".$db->dt[div_ix]."'\">".$db->dt[div_name]."</td>
								<th class='box_03'></th>
							</tr>
							</table>";
}

$Contents02 .= "<div style='padding:0px 0px 0px 20px'>";
if($db->total){
$Contents02 .= "<select name='div_ix' id='div_ix' style='margin-left:5px;border:1px solid silver;padding:1px;' onchange=\"document.location.href='?div_ix='+this.value\">";
		$Contents02 .= "<option value=''>배너분류</option>";
		
			for($i=$i;$i < $db->total;$i++){
				$db->fetch($i);
				if($db->dt[div_ix] == $div_ix){
					$Contents02 .= "<option value='".$db->dt[div_ix]."' selected>".$db->dt[div_name]."</option>";
				}else{
					$Contents02 .= "<option value='".$db->dt[div_ix]."'>".$db->dt[div_name]."</option>";
				}
			}

		
		$Contents02 .= "</select>";
}

$mall_ix_group = GetDisplayDivision($mall_ix, "array");
$mall_ix_select = "";
if(is_array($mall_ix_group)){
    foreach($mall_ix_group as $key=>$val){
        $mall_ix_select .="<option value='".$val['mall_ix']."' ".($val[mall_ix] == $mall_ix ? "selected":"").">".$val['mall_templete_type']."</option>";
    }
}
$Contents02 .= "

   				<select style='margin-left:5px;border:1px solid silver;padding:1px;' id='mall_ix_group'>
   					<option value='' ".($mall_ix == '' ? "selected":"").">전체</option>
   					".$mall_ix_select."
				</select>
		</div>
						</td>
				<td align=right><input type='checkbox' name='is_change_function' id='is_change_function' value='1' onclick='toggleChangeFunction();' ".($_COOKIE["is_change_function"] ? "checked":"")."><label for='is_change_function'> 치환코드 보기</label> ";

$Contents02 .= "</td>
			</tr>
			</table>
		</div>
		</td>
	</tr>
	".bannerPositionTab($banner_page, $banner_position,$cid2)."	
	<tr>
	    <td align='left' colspan=9 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 20px;'>
		<table cellpadding=0 cellspacing=0><tr><td><img src='../image/title_head.gif' align=absmiddle> <b>배너목록</b></td><td><!--input type='checkbox' name='banner_image_view' id='banner_image_view' value=1 onclick='BannerImageView();' ".($_COOKIE[banner_image_view] == 1 ? "checked":"")." ><label for='banner_image_view'>배너이미지 크게보기</label--></td></tr></table></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
	    <col style='width:5%;'>	
		".($_SESSION["admin_config"][front_multiview] == "Y" ? "<col style='width:6%;'>":"")."
		<col style='width:5%;'>
	    <col style='width:15%;'>
	    <col style='width:15%;'>
		<col style='width:10%;'>
		<col style='width:10%;'>
		<col style='width:12%;'>		
	    <col style='width:10%;'>
	    <col style='width:10%;'>
	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td'> 번호</td>
		".($_SESSION["admin_config"][front_multiview] == "Y" ? "<td class='m_td'> 프론트전시</td>":"")."
		<td class='m_td'> 페이지</td>
		<td class='m_td' style='line-height:140%;'> 배너위치 <br>스케줄 치환함수</td>
		<td class='m_td'>카테고리/ 배너명 </td>
		<td class='m_td'>배너종류</td> 
		<td class='m_td' nowrap> 이미지</td>
		<td class='m_td'> 담당 MD</td>
	    <td class='m_td'> 노출기간 / 등록일자</td>
		<!--td class='m_td'> 등록일자</td-->
	    <td class='e_td'> 관리 / 관리업체</td>
	  </tr>";
$db = new Database;
global $page;
$max = 20;

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}
$paging = "view";
$add_where = "";
if($admininfo[admin_level] < 9){
	if($admininfo[mall_type] == "B"  || $admininfo[mall_type] == "O"){// 입점형 , 오픈마켓형
		$add_where .=" and company_id = '".$admininfo[company_id]."' ";
	}
}

$banner_page = $_GET["banner_page"]; //배너분류
$banner_position = $_GET["banner_position"]; //배너위치
$search_text = $_GET["search_text"]; //배너명
$search_date = $_GET["search_date"]; //1 == 날짜검색 사용
$date_type = $_GET["date_type"]; // use == 노출기간, reg == 등록일자

if(!empty($banner_page)){
    $div_ix = $banner_page;
}

if(!empty($banner_position)){
    $add_where .= " and bi.banner_position = '".$banner_position."' ";
}

if(!empty($md_code)){
    $add_where .= " and bi.md_mem_ix = '".$md_code."' ";
}

if(!empty($srch_status)){
	if($srch_status == 1){
		$status_where = " and use_sdate > '".date("Y-m-d H:i:s")."' ";
	}else if($srch_status == 2){
		$status_where = " and '".date("Y-m-d H:i:s")."' between use_sdate and use_edate ";
	}else if($srch_status == 3){
		$status_where = " and use_edate < '".date("Y-m-d H:i:s")."' ";
	}
}

$add_where .= $status_where;

if($_GET[cid2] !="" ){
	//$where ="and cmd.cid = '".$_GET[cid2]."' ";
    $add_where ="and bi.display_cid = '".$_GET[cid2]."' ";
	//$add_where .= "and bi.display_cid LIKE '".substr($_GET[cid2], 0,($_GET["depth"]+1)*3)."%' ";
}

if(!empty($mall_ix)){
    $add_where .= " and bi.mall_ix = '".$mall_ix."' ";
}

if(!empty($search_text)){
    $add_where .= " and bi.banner_name LIKE '%".$search_text."%' ";
}

if($search_date == "1" && !empty($date_type)){
    /*
	if($db->dbms_type == "oracle"){
        $use_sdate = $_GET["use_sdate"]." ".$_GET["FromHH"].":".$_GET["FromMI"].":00";
        $use_edate = $_GET["use_edate"]." ".$_GET["ToHH"].":".$_GET["ToMI"].":00";
        
    }else{
        $use_sdate = date("Y-m-d H:i:s",strtotime($_GET["use_sdate"]." ".$_GET["FromHH"].":".$_GET["FromMI"].":00"));
        $use_edate = date("Y-m-d H:i:s",strtotime($_GET["use_edate"]." ".$_GET["ToHH"].":".$_GET["ToMI"].":59"));
    }
	*/
// echo $use_sdate;
    
    if($date_type == "use"){
        //이게 맞나?;;
        if($db->dbms_type == "oracle"){
            $add_where .= " and bi.use_edate > TO_DATE('".$use_sdate."','MM-DD-YYYY HH24:MI:SS') ";
            $add_where .= " and bi.use_sdate <= TO_DATE('".$use_edate."','MM-DD-YYYY HH24:MI:SS') ";
        }else{
//            $add_where .= " and bi.use_edate > '".$use_sdate."'";
//            $add_where .= " and bi.use_edate <= '".$use_edate."'";
			$add_where .= " and bi.use_edate BETWEEN '".$use_sdate." 00:00:00' AND '".$use_edate." 23:59:59' ";
        }
    }else{
        if($db->dbms_type == "oracle"){
            $add_where .= " and bi.regdate BETWEEN TO_DATE('".$use_sdate."','MM-DD-YYYY HH24:MI:SS') AND TO_DATE('".$use_edate."','MM-DD-YYYY HH24:MI:SS')";
        }else{
            $add_where .= " and date_format(bi.regdate,'%Y-%m-%d') BETWEEN '".$use_sdate."' AND '".$use_edate."' ";
            $swhere .= " and date_format(bi.regdate,'%Y-%m-%d') BETWEEN '".$use_sdate."' AND '".$use_edate."' ";
        }
    }
}

define_syslog_variables();
openlog("phplog", LOG_PID , LOG_LOCAL4);
//syslog(LOG_INFO, $HTTP_URL);
closelog();
if($_SERVER["QUERY_STRING"] == "nset=$nset&page=$page"){
    $query_string = str_replace("nset=$nset&page=$page","",$_SERVER["QUERY_STRING"]) ;
}else{
    $query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER["QUERY_STRING"]) ;
}

if($div_ix != ""){

	$sql = "select bi.banner_ix
				from shop_bannerinfo bi
				LEFT JOIN shop_banner_div bd ON bi.banner_page=bd.div_ix
				LEFT JOIN common_company_detail ccd USING (company_id)
				where bi.banner_page = '$div_ix' and bi.agent_type = '".$agent_type."'  ".$add_where."
				order by bi.regdate DESC";

	$db->query($sql);
	$total = $db->total;
 
	$sql = "select bi.banner_ix,bi.mall_ix, bi.banner_name,bi.banner_img,bi.banner_width,bi.banner_height,bi.disp,bp.bp_name,bi.banner_kind, 
				date_format(bi.use_sdate, '%Y-%m-%d %H:%i') as use_sdate, 
				date_format(bi.use_edate, '%Y-%m-%d %H:%i') as use_edate, 
				date_format(bi.regdate, '%Y-%m-%d %H:%i') as regdate,
                bi.banner_page, bi.banner_position,bi.display_cid,  bd.div_name,ccd.com_name , md_mem_ix
				from shop_bannerinfo bi 
				LEFT JOIN shop_banner_div bd ON bi.banner_page=bd.div_ix 
				LEFT JOIN shop_banner_position bp ON bi.banner_position=bp.bp_ix 
				LEFT JOIN common_company_detail ccd USING (company_id)  
				where  bi.banner_page = '$div_ix' and bi.agent_type = '".$agent_type."' ".$add_where." 
				order by bi.banner_page, bi.banner_position, bi.display_cid desc,bi.view_order asc	, bi.regdate DESC 			
				limit $start, $max";
	//echo nl2br($sql);
	$db->query($sql);
	$str_page_bar = page_bar($total, $page,$max, $query_string,$paging); //TODO:쿼리추가할것
    


}else{
	$db->query("select bi.banner_ix from shop_bannerinfo bi LEFT JOIN shop_banner_div bd ON bi.banner_page=bd.div_ix LEFT JOIN common_company_detail ccd USING (company_id) where 1=1 and bi.agent_type = '".$agent_type."'  ".$add_where." order by bi.regdate DESC");
	$total = $db->total;

	$sql = "select bi.banner_ix,bi.mall_ix, bi.banner_name,bi.banner_img,bi.banner_width,bi.banner_height,bi.disp,bp.bp_name,bi.banner_kind, 
				date_format(bi.use_sdate, '%Y-%m-%d %H:%i') as use_sdate, 
				date_format(bi.use_edate, '%Y-%m-%d %H:%i') as use_edate, 
				date_format(bi.regdate, '%Y-%m-%d %H:%i') as regdate,
                bi.banner_page, bi.banner_position, bi.display_cid, bd.div_name,ccd.com_name , md_mem_ix
				from shop_bannerinfo bi 
				LEFT JOIN shop_banner_div bd ON bi.banner_page=bd.div_ix 
				LEFT JOIN shop_banner_position bp ON bi.banner_position=bp.bp_ix 
				LEFT JOIN common_company_detail ccd USING (company_id)  
				where 1=1 and bi.agent_type = '".$agent_type."'   ".$add_where." 
				order by bi.banner_page, bi.banner_position, bi.display_cid desc,bi.view_order asc	, bi.regdate DESC 			
				limit $start, $max";
	//echo nl2br($sql);
	//exit;

	$db->query($sql);
	$str_page_bar = page_bar($total, $page,$max, $query_string,$paging); //TODO:쿼리추가할것  //&view=innerview&max=$max&div_ix=$div_ix&banner_page=$banner_page
}

$banner_infos = $db->fetchall();

if(is_array($banner_infos)){
	$banner_position_rows = array();
	for($i=0;$i < count($banner_infos);$i++){
        $banner_position_rows[$banner_infos[$i][banner_position]][$banner_infos[$i][display_cid]]++;
	}
}


if(count($banner_infos)){//$db->total
	for($i=0;$i < count($banner_infos);$i++){
	//$db->fetch($i);
	$no = $total - ($page - 1) * $max - $i;

	if($banner_infos[$i][banner_position]){
		if($banner_infos[$i][display_cid]){
			$bannerScheduleFunction = "{=getScheduleBannerInfo(".$banner_infos[$i][banner_position].", cid)}";
		}else{
			$bannerScheduleFunction = "{=getScheduleBannerInfo(".$banner_infos[$i][banner_position].")}";
		}
	}else{
		$bannerScheduleFunction = "";
	}

	if($banner_infos[$i][banner_kind] == 1){
		$banner_kind = '일반배너';
	}else if($banner_infos[$i][banner_kind] == 2){
		$banner_kind = '플래쉬형 배너';
	}else if($banner_infos[$i][banner_kind] == 3){
		$banner_kind = '슬라이드형 배너';
	}else if($banner_infos[$i][banner_kind] == 4){
		$banner_kind = '동영상 배너';
	}else if($banner_infos[$i][banner_kind] == 5){
		$banner_kind = '사용자지정 배너';
	}

	$sql = "select AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name from ".TBL_COMMON_MEMBER_DETAIL." cmd where code= '".$banner_infos[$i][md_mem_ix]."' ";
	//echo $sql."<br>";
	$db->query($sql);
	if($db->total){
		$db->fetch();
		$md_name = $db->dt[name];
	}else{
		$md_name = "-";
	}

	$Contents02 .= "
		  <tr bgcolor=#ffffff align=center height=30>
		    <td class='list_box_td'>".$no."</td>";
	if($_SESSION["admin_config"][front_multiview] == "Y"){
		$Contents02 .= "
				<td class='list_box_td list_bg_gray'>".GetDisplayDivision($banner_infos[$i][mall_ix], "text")."</td>";
	}
	$Contents02 .= "
			<td class='list_box_td' >".$banner_infos[$i][div_name]."</td>";

    if($banner_infos[$i][banner_position] != $b_banner_position || $banner_infos[$i][display_cid] != $b_display_cid){

        $position_cnt = $banner_position_rows[$banner_infos[$i][banner_position]][$banner_infos[$i][display_cid]];

        $Contents02 .= "<td class='list_box_td list_bg_gray' style='line-height:140%;' rowspan='".$position_cnt."' title='".$position_cnt."'>
        ".$banner_infos[$i][bp_name]."";
        if($_COOKIE["is_change_function"]){
            $Contents02 .= "<br>".$bannerScheduleFunction."";
        }
        //if(strpos($_SESSION['admininfo']['admin_id'] ,'forbiz') !== false){
        $Contents02 .= "
		<br><input type='button' value='배너위치변경' onclick=\"changeBannerViewOrder(".$banner_infos[$i][banner_position].",'".$banner_infos[$i][display_cid]."')\" />";
       // }
        $Contents02 .= "
		
        </td>";
    }

	$Contents02 .= "
		    <td class='list_box_td point' style='text-align:left;font-weight:normal;line-height:150%;'>
			<b>".getCategoryPathByAdmin($banner_infos[$i][display_cid],4)."</b><br> ".$banner_infos[$i][banner_name]."
			";
			if($_COOKIE["is_change_function"]){
				$Contents02 .= "<br>{=getBannerInfo(".$banner_infos[$i][banner_ix].")}";
			}
			$Contents02 .= "
			</td>
			<td class='list_box_td' >".$banner_kind."</td> ";

if($banner_infos[$i][banner_kind] == "2" || $banner_infos[$i][banner_kind] == "3" || $banner_infos[$i][banner_kind] == 5){
	$db->query("SELECT * FROM shop_bannerinfo_detail  where banner_ix = '".$banner_infos[$i][banner_ix]."' order by bd_ix ASC ");//order by 가 regdate 로 되어 있던 것을 고침 kbk 13/02/15
	if($db->total){
		$banner_details = $db->fetchall();
	}


	$Contents02 .= "<td class='list_box_td list_bg_gray' style='padding:5px;'>";
	for($j=0; $j < count($banner_details);$j++){ 
		if($banner_details[$j][bd_file] && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/".$banner_infos[$i][banner_ix]."/".$banner_details[$j][bd_file])){
			//	exit;
			$image_info = getimagesize ($DOCUMENT_ROOT.$admin_config[mall_data_root]."/images/banner/".$banner_infos[$i][banner_ix]."/".$banner_details[$j][bd_file]);
			$Contents02 .= "<img src='".$admin_config[mall_data_root]."/images/banner/".$banner_details[$j][banner_ix]."/".$banner_details[$j][bd_file]."' width='100px' style='vertical-align:middle;margin:3px;'  ".($_COOKIE[banner_image_view] == 1 ? "":"height=50")." class='helpcloud' help_width='".($image_info[0]+20)."' help_height='".($image_info[1])."' help_html=\"<img src='".$admin_config[mall_data_root]."/images/banner/".$banner_details[$j][banner_ix]."/".$banner_details[$j][bd_file]."' >\" style='cursor:pointer;'>";
		}
	}
	$Contents02 .= "</td>";

}else{
	if(substr_count($banner_infos[$i][banner_img],'.swf') > 0){
		$Contents02 .= "
				<td class='list_box_td list_bg_gray' style='padding:5px;'>
					<script language='javascript'>generate_flash('".$admin_config[mall_data_root]."/images/banner/".$banner_infos[$i][banner_ix]."/".$banner_infos[$i][banner_img]."', '".$banner_infos[$i][banner_width]."', '".$banner_infos[$i][banner_height]."');</script>
				</td>";
	}else{
		if($banner_infos[$i][banner_img] && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/".$banner_infos[$i][banner_ix]."/".$banner_infos[$i][banner_img])){
			$image_info = getimagesize ($DOCUMENT_ROOT.$admin_config[mall_data_root]."/images/banner/".$banner_infos[$i][banner_ix]."/".$banner_infos[$i][banner_img]);
			//print_r($image_info);
			if($image_info[0] > 300){
				$Contents02 .= "<td class='list_box_td list_bg_gray' style='padding:5px;'><img src='".$admin_config[mall_data_root]."/images/banner/".$banner_infos[$i][banner_ix]."/".$banner_infos[$i][banner_img]."'  style='vertical-align:middle' ".($_COOKIE[banner_image_view] == 1 ? "":"width=100")." class='helpcloud' help_width='".($image_info[0]+20)."' help_height='".($image_info[1])."' help_html=\"<img src='".$admin_config[mall_data_root]."/images/banner/".$banner_infos[$i][banner_ix]."/".$banner_infos[$i][banner_img]."' >\" style='cursor:pointer;'></td>";
			}else{
				$Contents02 .= "<td class='list_box_td list_bg_gray' style='padding:5px;'><img src='".$admin_config[mall_data_root]."/images/banner/".$banner_infos[$i][banner_ix]."/".$banner_infos[$i][banner_img]."' style='vertical-align:middle'  ".($_COOKIE[banner_image_view] == 1 ? "":"height=50")." class='helpcloud' help_width='".($image_info[0]+20)."' help_height='".($image_info[1])."' help_html=\"<img src='".$admin_config[mall_data_root]."/images/banner/".$banner_infos[$i][banner_ix]."/".$banner_infos[$i][banner_img]."' >\" style='cursor:pointer;'></td>";
			}
		}else{
			$Contents02 .= "<td class='list_box_td list_bg_gray' style='padding:5px;'></td>";
		}
	}
}
$Contents02 .= "
				<td class='list_box_td point'>".$md_name."</td>
				<td class='list_box_td' style='line-height:140%;padding:10px;' nowrap>
				".$banner_infos[$i][use_sdate]."  ~ <br>".$banner_infos[$i][use_edate]."<br> 
				등록일자 :  ".$banner_infos[$i][regdate]."<br>
				".($banner_infos[$i][disp] == "1" ? "<b>사용</b>":"사용하지않음")."
				</td>
				<!--td class='list_box_td'>".$banner_infos[$i][regdate]."</td-->
				<td class='list_box_td list_bg_gray' style='padding:7px;line-height:140%;'>
				".$banner_infos[$i][com_name]."<br>
				";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U") ){
		    	$Contents02 .= "<a href='banner_write.php?mode=copy&banner_ix=".$banner_infos[$i][banner_ix]."&SubID=SM22464243Sub'><img src='../images/".$admininfo["language"]."/btn_list_copy.gif' border=0 alt='복사'></a> ";
				
				$Contents02 .= "<a href=\"javascript:PoPWindow3('banner_write.php?mmode=pop&banner_ix=".$banner_infos[$i][banner_ix]."&SubID=SM22464243Sub',1100,800,'stock_report')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
				//$Contents02 .= "<a href='banner_write.php?banner_ix=".$banner_infos[$i][banner_ix]."&SubID=SM22464243Sub'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}else{
				$Contents02 .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D") ){
				$Contents02 .= "<a href=\"javascript:deleteBanner('delete','".$banner_infos[$i][banner_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}else{
				$Contents02 .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}
		    	//if($banner_infos[$i][basic] =="N"){
		    	//$Contents02 .= " <a href=\"javascript:deleteGroupInfo('delete','".$banner_infos[$i][gp_ix]."')\"><img src='../image/btc_del.gif' border=0></a>";
	    		//}
	    		$Contents02 .= "
		    </td>
		  </tr>
		 ";

		$b_banner_position = $banner_infos[$i][banner_position];
		$b_display_cid = $banner_infos[$i][display_cid];
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=10>등록된 배너가 없습니다. </td>
		  </tr>	  ";
}
$Contents02 .="</table>
				<table cellpadding=0 cellspacing=0 width='100%' style='padding-top:3px;'>";
$Contents02 .= "
		<tr height=1>
			<td align=left colspan=6>".$str_page_bar."</td>
			<td colspan=1 style='padding-top:10px;' align=right>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") ){
			$Contents02 .= "<a href='banner_write.php?SubID=SM22464243Sub'><img src='../images/".$admininfo["language"]."/btn_reg.gif' align=absmiddle ></a>";
			}
			$Contents02 .= "
			</td>
		</tr>

		</table>";



$Contents = "<table width='100%' border=0>";

$Contents = $Contents."<tr><td>".$Contents02."</td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";
/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >배너를 등록하신후 치환함수를 이용해 디자인에 적용하실 수 있습니다</td></tr>

</table>
";*/
	$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("배너관리", $help_text);

 $Script = "
 <!--script language='javascript' src='../include/DateSelect.js'></script-->
 <script language='javascript'>

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
	
	$('#mall_ix_group').on('change',function(){
	   var mall_ix_val = $(this).val();	   
	   $('select[name=mall_ix]').val(mall_ix_val).prop('selected',true);
	   $('#search_banner').submit();
	});
});

 function deleteBanner(act, banner_ix){
	if(confirm('배너를 정말로 삭제하시겠습니까?')){//'배너를 정말로 삭제하시겠습니까?'
		//document.location.href = '/admin/display/banner.act.php?act='+act+'&banner_ix='+banner_ix+'&SubID=SM22464243Sub&agent_type=".$agent_type."';
		window.frames['act'].location.href = '/admin/display/banner.act.php?act='+act+'&banner_ix='+banner_ix+'&SubID=SM22464243Sub&agent_type=".$agent_type."';
	}
 }


function onLoad(FromDate, ToDate) {
		var frm = document.search_banner;


	//LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
	//LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);
    if(frm.search_date.checked){
       frm.date_type.disabled = false;
		frm.use_sdate.disabled = false;
		frm.use_edate.disabled = false;
    }else{
        frm.date_type.disabled = true;
		frm.use_sdate.disabled = true;
		frm.use_edate.disabled = true;
    }
	//init_date(FromDate,ToDate);

}

function onLoad2(FromDate, ToDate) {
		var frm = document.search_banner;


	LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
	LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);

	//init_date(FromDate,ToDate);

}

function ChangeRegistDate(frm){
	if(frm.search_date.checked){
        frm.date_type.disabled = false;
		frm.use_sdate.disabled = false;
		frm.use_edate.disabled = false;
		/*
		frm.FromDD.disabled = false;
		frm.ToYY.disabled = false;
		frm.ToMM.disabled = false;
		frm.ToDD.disabled = false;
		*/
	}else{
	    frm.date_type.disabled = true;
		frm.use_sdate.disabled = true;
		frm.use_edate.disabled = true;
		/*
		frm.FromDD.disabled = true;
		frm.ToYY.disabled = true;
		frm.ToMM.disabled = true;
		frm.ToDD.disabled = true;
		*/
	}
}



function select_date(FromDate,ToDate,dType) {
	var frm = document.search_banner;

	$(\"#start_datepicker\").val(FromDate);
	$(\"#end_datepicker\").val(ToDate);
	frm.search_date.checked = true;
}



function loadBannerPosition(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;	
	var form = sel.form.name;
	
	var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
	//document.write('banner_position.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
	window.frames['act'].location.href = '/admin/display/banner_position.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
}

 function loadCategory(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	var depth = $('select[name='+sel.name+']').attr('depth');
	if(trigger == ''){
	    depth = depth-1;
	    trigger = $('select[name=cid'+depth+'_1] :selected').val();
	}
//alert(trigger)
//alert(depth)
	window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

}

function BannerImageView(){
		if($('#banner_image_view').attr('checked') == true || $('#banner_image_view').attr('checked') == 'checked'){		
			$.cookie('banner_image_view', '1', {expires:1,domain:document.domain, path:'/', secure:0});
		}else{		
			$.cookie('banner_image_view', '0', {expires:1,domain:document.domain, path:'/', secure:0});
		}
		document.location.reload();
}

function changeBannerViewOrder(banner_position,display_cid){
    PoPWindow3('bannerViewOrderChange.php?mmode=pop&banner_position='+banner_position+'&display_cid='+display_cid+'',1100,800,'bannerViewOrderChange');
    
}

 </script>
 ";

if($mmode == "pop"){
	if($agent_type == "M"  || $agent_type == "mobile"){
		$P = new LayOut();
		$P->addScript = $Script;
		$P->Navigation = $navigation;
		$P->title = $title;
		$P->strLeftMenu = mshop_menu();
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}else{
		$P = new ManagePopLayOut();
		$P->addScript = $Script;
		$P->OnloadFunction = "";
		$P->strLeftMenu = display_menu();
		$P->Navigation = "프로모션/전시 > 배너관리 > 통합배너관리 목록";
		$P->title = "통합배너관리 목록";
		$P->NaviTitle = "통합배너관리 목록";
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}
}else{
	if($agent_type == "M"  || $agent_type == "mobile"){
		$P = new LayOut();
		$P->addScript = $Script;
		$P->Navigation = $navigation;
		$P->title = $title;
		$P->strLeftMenu = mshop_menu();
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}else{
		$P = new LayOut();
		$P->addScript = $Script;
		//$P->strLeftMenu = design_menu();
		$P->OnloadFunction = "";//onLoad('$sDate','$eDate', document.search_banner);
		$P->strLeftMenu = display_menu("/admin",$category_str);
		$P->Navigation = "프로모션/전시 > 배너관리 > 통합배너관리 목록";
		$P->title = "통합배너관리 목록";
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}
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
