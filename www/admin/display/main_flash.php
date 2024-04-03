<?
include("../class/layout.class");
include_once("../display/display.lib.php");

$db = new Database;



/**
 * 배너 검색 부분
 */
if ($use_sdate == "" || $use_edate == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

//	$sDate = date("Y/m/d");
//	$sDate = date("Y/m/d", $before10day);
//	$eDate = date("Y/m/d");
	if($search_date){
	$use_sdate = date("Ymd", $before10day);
	$use_edate = date("Ymd");
	}
}else{
	/*
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;
	*/
}
$vdate = date("Ymd", time());
$today = date("Y/m/d", time());
$vyesterday = date("Y/m/d", time()-84600);
$voneweekago = date("Y/m/d", time()-84600*7);
$vtwoweekago = date("Y/m/d", time()-84600*14);
$vfourweekago = date("Y/m/d", time()-84600*28);
$vyesterday = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
$voneweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
$v15ago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
$vfourweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
$vonemonthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));


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
$regdate = $_GET["regdate"]; //1 == 날짜검색 사용
$date_type = $_GET["date_type"]; // use == 노출기간, reg == 등록일자

if(!empty($banner_page)){
    $div_ix = $banner_page;
}

if(!empty($banner_position)){
    $add_where .= " and bi.banner_position = '".$banner_position."' ";
}

if(!empty($search_text)){
    $add_where .= " and bi.banner_name LIKE '%".$search_text."%' ";
}

if($regdate == "1" && !empty($date_type)){
    if($db->dbms_type == "oracle"){
        $search_sdate = $_GET["FromMM"]."-".$_GET["FromDD"]."-".$_GET["FromYY"]." 00:00:00";
        $search_edate = $_GET["ToMM"]."-".$_GET["ToDD"]."-".$_GET["ToYY"]." 23:59:59";
        
    }else{
        $search_sdate = date("Y-m-d H:i:s",strtotime($_GET["FromYY"]."-".$_GET["FromMM"]."-".$_GET["FromDD"]." 00:00:00"));
        $search_edate = date("Y-m-d H:i:s",strtotime($_GET["ToYY"]."-".$_GET["ToMM"]."-".$_GET["ToDD"]." 23:59:59"));
    }
    
    if($date_type == "use"){
        //이게 맞나?;;
        if($db->dbms_type == "oracle"){
            $add_where .= " and bi.use_edate > TO_DATE('".$search_sdate."','MM-DD-YYYY HH24:MI:SS') ";
            $add_where .= " and bi.use_sdate < TO_DATE('".$search_edate."','MM-DD-YYYY HH24:MI:SS') ";
        }else{
            $add_where .= " and bi.use_edate > '".$search_sdate."'";
            $add_where .= " and bi.use_edate < '".$search_edate."'";
        }
    }else{
        if($db->dbms_type == "oracle"){
            $add_where .= " and bi.regdate BETWEEN TO_DATE('".$search_sdate."','MM-DD-YYYY HH24:MI:SS') AND TO_DATE('".$search_edate."','MM-DD-YYYY HH24:MI:SS')";
        }else{
            $add_where .= " and bi.regdate BETWEEN '".$search_sdate."' AND '".$search_edate."' ";
            $swhere .= " and bi.regdate BETWEEN '".$search_sdate."' AND '".$search_edate."' ";
        }
    }
}

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	  <tr >
		<td align='left' > ".GetTitleNavigation("움직이는 배너목록", "프로모션/전시 > 움직이는 배너목록 ")."</td>
	  </tr>
	  <tr>
			<td>
    <form name='search_banner'>
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
							<table id='tab_2' ".$tabmenu_class2.">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"alert('배너 결과분석 리포트는 준비중입니다.');/*document.location.href='banner.report.php'*/\">배너 결과분석</td>
								<th class='box_03'></th>				
							</tr>
							</table>
						</td>							
						<td align='right'>
							<!--a href='#;' onclick='FnDisplayWrite()'><img src='../images/".$admininfo["language"]."/btn_disp_write.gif' align=absmiddle ></a>&nbsp;
							<a href='#;' onclick='location.href=\"display_div.php?display_div=".$display_div."\"'><img src='../images/".$admininfo["language"]."/btn_disp_div.gif' align=absmiddle ></a-->";

if($admininfo[admin_level]  > 8){
	$Contents01 .= "
					<a href='/admin/display/banner_category.php'><img src='../images/".$admininfo["language"]."/btn_banner_group.gif' align=absmiddle></a>";
}
$Contents01 .= "</td>
					</tr>
					</table>										
					</div>					
				</td>
			</tr>
			<tr>
                <td style='width:100%;' valign=top colspan=3>
                    <table width=100%  border=0>
                        <tr height=25>
                            <td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>배너 목록 검색하기</b></td>
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
															<col width='*'>
                                                            <tr>
                                                                <th class='search_box_title' >페이지</th>
                                                                <td class='search_box_left' colspan=3>
                                                                    <table>
                                                                        <tr>
                                                                            <td class='input_box_item' colspan=3>
                                                                                ".getBannerFirstDIV($banner_page)."
                                                                                ".bannerPosition($banner_page, $banner_position)."
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class='search_box_title' >배너명</th>
                                                                <td class='search_box_left' colspan=3>
                                                                    <table>
                                                                        <tr>
                                                                            <td class='input_box_item'>
                                                                                <input type=text class=textbox name='search_text' value='".$search_text."' style='width:200px;' >
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
															<tr>
																<td class='search_box_title' >  진행상태</td>
																<td class='search_box_item'>".makeRadioTag($arr_display_status, "srch_status")."</td>
																<td class='search_box_title' >  전시유무</td>
																<td class='search_box_item'>".makeRadioTag($arr_display_disp, "srch_disp")."</td>
															</tr>
															<tr>
																<td class='search_box_title' >
																	<label for='search_date'><b>날짜 검색</b></label><input type='checkbox' name='search_date' id='search_date' value='1' onclick='ChangeRegistDate(document.search_banner);' ".(($search_date==1)?"checked":"").">
																	<select name='date_type'>
																		<option value='use' ".(($date_type == 'use')?"selected":"").">노출기간</option>
																		<option value='reg' ".(($date_type == 'reg')?"selected":"").">등록일자</option>
																	</select>
																  </td>
																  <td class='search_box_item'  colspan=3>
																	<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
																		<col width=35>
																		<col width=35>
																		<col width=20>
																		<col width=35>
																		<col width=35>
																		<col width=*>
																		<tr>
																			<td  nowrap>
																			<input type='text' name='use_sdate' class='textbox' value='".$use_sdate."' style='height:18px;width:70px;text-align:center;'  id='start_datepicker'> 일
																			</td>
																			<td nowrap>
																			<SELECT name=FromHH>";
																							for($i=0;$i < 24;$i++){
																			$Contents01.= "<option value='".$i."' ".($FromHH == $i ? "selected":"").">".$i."</option>";
																							}
																			$Contents01.= "
																							</SELECT> 시
																							<SELECT name=FromMI>";
																							for($i=0;$i < 60;$i++){
																			$Contents01.= "<option value='".$i."' ".($FromMI == $i ? "selected":"").">".$i."</option>";
																							}
																			$Contents01.= "
																							</SELECT> 분
																			</td>
																			<td align=center> ~ </td>
																			<td nowrap>
																			<input type='text' name='use_edate' class='textbox' value='".$use_edate."' style='height:18px;width:70px;text-align:center;' id='end_datepicker'> 일
																			</td>
																			<td nowrap>
																			<SELECT name=ToHH>";
																							for($i=0;$i < 24;$i++){
																			$Contents01.= "<option value='".$i."' ".(($ToHH == $i || $i == 23) ? "selected":"").">".$i."</option>";
																							}
																			$Contents01.= "
																							</SELECT> 시
																							<SELECT name=ToMI>";
																							for($i=0;$i < 60;$i++){
																			$Contents01.= "<option value='".$i."' ".(($ToMI == $i || $i == 59) ? "selected":"").">".$i."</option>";
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
																	</table>
																  </td>
															</tr>
                                                            <!--tr height=27>
                                                                <td class='search_box_title' ><label for='regdate'><b>날짜 검색</b></label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.search_banner);' ".(($regdate==1)?"checked":"")."><select name='date_type'>
                                                                                    <option value='use' ".(($date_type == 'use')?"selected":"").">노출기간</option>
                                                                                    <option value='reg' ".(($date_type == 'reg')?"selected":"").">등록일자</option>
                                                                                </select></td>
                                                                <td class='search_box_item' colspan=3>
                                                                    <table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff >
                                                                        <tr>
                                                                            <td>
                                                                                
                                                                            </td>
                                                                            <TD  nowrap>
                                                                                <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY >
                                                                                </SELECT> 년 
                                                                                <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM>
                                                                                </SELECT> 월 
                                                                                <SELECT name=FromDD>
                                                                                </SELECT> 일 
                                                                            </TD>
                                                                            <TD  align=center> ~ </TD>
                                                                            <TD nowrap>
                                                                                <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY>
                                                                                </SELECT> 년 
                                                                                <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM>
                                                                                </SELECT> 월 
                                                                                <SELECT name=ToDD>
                                                                                </SELECT> 일
                                                                            </TD>
                                                                            <TD style='padding-left:10px; vertical-align:middle;'>
																				<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
																				<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
																				<a href=\"javascript:select_date('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
																				<a href=\"javascript:select_date('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
																				<a href=\"javascript:select_date('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
                                                                            </TD>
                                                                        </tr>
                                                                    </table>
                                                                </TD>
                                                            </TR-->

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
	  <tr>
	    <td align='left' colspan=6 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'>  <b>이미지목록</b> <span style='padding-left:2px' class='helpcloud' help_width='240' help_height='30' help_html='메인플레쉬 고객 화면에 메인큰이미지를 변경할 수 있습니다.'><img src='/admin/images/icon_q.gif' /></span></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
	    <col style='width:10%;'>
	    <col style='width:*;'>
		<col style='width:25%;'>
	    <col style='width:10%;'>
	    <col style='width:20%;'>
	    <col style='width:10%;'>
	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td'> 구분코드</td>
	    <td class='m_td'> 플래쉬명</td>
		<td class='m_td'> 치환코드</td>
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";
$db = new Database;


$db->query("SELECT * FROM ".TBL_SHOP_MANAGE_FLASH."   ".$add_where." order by regdate desc ");


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents01 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
		    <td class='list_box_td list_bg_gray'>".$db->dt[mf_type]."</td>
		    <td class='list_box_td point'>".$db->dt[mf_name]."</td>
			<td class='list_box_td list_bg_gray'>{=GetPrintFlash01('".$db->dt[mf_type]."', 630, 318)}</td>
		    <td class='list_box_td'>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		    <td class='list_box_td'>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents01 .= "<a href=\"./main_flash_write.php?mf_ix=".$db->dt[mf_ix]."&act=update\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}else{
				$Contents01 .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents01 .= "<a href=\"javascript:deleteflashInfo('delete','".$db->dt[mf_ix]."','".$db->dt[mf_type]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}else{
				$Contents01 .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}

$Contents01 .= "
		    </td>
		  </tr>
			";
	}
}else{
	$Contents01 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=6>등록된 이미지가  없습니다. </td>
		  </tr>  ";
}
$Contents01 .= "</table>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
	$ButtonString = "<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >";
	$ButtonString .= " <tr><td colspan=6 align='center' style='padding:10px 0px;' ><a href='./main_flash_write.php'><img src='../images/".$admininfo["language"]."/btn_reg.gif' align=absmiddle ></a></td></tr>";
	$ButtonString .= "</table>";
}





$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='mf_form' action='main_flash.act.php' method='post' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)'><input name='act' type='hidden' value='insert'><input name='mf_ix' type='hidden' value=''>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."</table >";
/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=*>
	<tr><td valign=top ><img src='/admin/image/icon_list.gif' ></td><td class='small' style='line-height:120%' >프로모션 배너를 등록하신후 치환함수를 이용해 디자인에 적용하실 수 있습니다 <br />
		디자인 관리페이지에 적용하고자 하는 페이지로 이동한 후 해당 치환코드를 원하시는 위치에 입력하여 주시면 화면에 출력 됩니다.</td></tr>

</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("프로모션 배너관리", $help_text);

 $Script = "
 <Script Language='JavaScript' src='design.js'></Script>
 <script language='javascript'>
 function updateflashInfo(mf_ix,mf_name,mf_link,disp){
 	var frm = document.mf_form;

 	frm.act.value = 'update';
 	frm.mf_ix.value = mf_ix;
 	frm.mf_name.value = mf_name;
 	frm.mf_link.value = mf_link;
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}

}
 function deleteflashInfo(act, mf_ix,mf_type){
 	if(confirm('해당이미지정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.mf_form;
 		frm.act.value = act;
 		frm.mf_ix.value = mf_ix;
 		frm.submit();
 	}
}

 </script>
 ";


$P = new LayOut();
$P->prototype_use = false;
$P->jquery_use = true;
$P->addScript = $Script;
$P->strLeftMenu = display_menu("/admin",$category_str);
$P->Navigation = "프로모션/전시 > 배너관리 > 움직이는 배너목록";
$P->title = "움직이는 배너목록";
$P->strContents = $Contents;
echo $P->PrintLayOut();


/*

create table ".TBL_OAASYS_MANAGE_FLASH." (
mf_ix int(4) unsigned not null auto_increment  ,
mf_type varchar(2) not null,
mf_name varchar(20) null default null,
mf_link varchar(255) null default null,
mf_file varchar(20) null default null,
disp char(1) default '1' ,
regdate datetime not null,
primary key(mf_ix));
*/
?>