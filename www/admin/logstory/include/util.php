<?php
$use_path = "/logstory/";
//include($_SERVER["DOCUMENT_ROOT"]."/".$use_path."include/design.tmp.php");

function displayTimeFormat($seconds) {
    $seconds;

    $days = floor($seconds/60/60/24);
    $hours = $seconds/60/60%24;
    $mins = $seconds/60%60;
    $secs = $seconds%60;

    $duration='';
    if($days > 0) $duration .= $days."일";
    if($hours > 0) $duration .= " ".$hours."시간";
    if($mins > 0) $duration .= " ".$mins."분";
    if($secs > 0) $duration .= " ".$secs."초";

    $duration = trim($duration);
    if($duration==null) $duration = ' -';   //1 초이내

    return $duration;
}


function getRefererCategoryPath($cid, $depth='-1'){

    $mdb = new forbizDatabase;
    $mstring = "";
    if($depth == '0'){
        $sql = "select * from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." where  depth = 0 and cid LIKE '".substr($cid,0,3)."%' order by depth asc";
    }else if($depth == '1'){
        $sql = "select * from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." where  depth <= 1 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%'))  order by depth asc";
    }else if($depth == '2'){
        $sql = "select * from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." where  depth <= 2 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%'))  order by depth asc";
    }else if($depth == '3'){
        $sql = "select * from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." where  depth <= 3 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%'))  order by depth asc";
    }else if($depth == '4'){
        $sql = "select * from ".TBL_LOGSTORY_REFERER_CATEGORYINFO." where  depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";

    }else{
        return "";
    }

    $mdb->query($sql);

    for($i=0;$i < $mdb->total;$i++){
        $mdb->fetch($i);

        if($i == 0){
            $mstring .= $mdb->dt['cname'];
        }else{
            $mstring .= " > ".$mdb->dt['cname'];
        }
    }
    return $mstring;
}
if(!function_exists('getCategoryPath')) {

    function getCategoryPath($cid, $depth='-1'){

        $mdb = new forbizDatabase;
        if($depth == '0'){
            $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where  depth = 0 and cid LIKE '".substr($cid,0,3)."%' order by depth asc";
        }else if($depth == '1'){
            $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where  depth <= 1 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%'))  order by depth asc";
        }else if($depth == '2'){
            $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where  depth <= 2 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%'))  order by depth asc";
        }else if($depth == '3'){
            $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where  depth <= 3 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%'))  order by depth asc";
        }else if($depth == '4'){
            $sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where  depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";

        }else{
            return "";
        }

        $mdb->query($sql);

        for($i=0;$i < $mdb->total;$i++){
            $mdb->fetch($i);

            if($i == 0){
                $mstring .= $mdb->dt['cname'];
            }else{
                $mstring .= " > ".$mdb->dt['cname'];
            }
        }
        return $mstring;
    }
}

function getDepth($cid, $category="tree"){

    $mdb = new forbizDatabase;

    $sql = "select depth from ".TBL_SHOP_CATEGORY_INFO." where cid = '".$cid."'  ";
    $mdb->query($sql);

    $mdb->fetch();

    return $mdb->dt['depth'];

}


if(!function_exists('HelpBox')){
    function HelpBox($title, $text, $title_size="151"){

        $mstring = "<table width='100%' border=0>
					<tr height=2><td colspan=3><div style='z-index:0;position:relative;top:10px;left:10px;width:".$title_size."px;height:15px;font-weight:bold;padding:3px 5px 0 55px;background-color:#ffffff' class='help_title' nowrap> $title</div></td></tr>
					<tr height=2><td class='help_col' colspan=3></td></tr>
					<tr height=60>
						<td width=1 class='help_row1'></td>
						<td class='top p10 lh160'>
							<table width='100%' cellpadding=0 cellspacing=0>
							<!--tr><td align=left><img src='/images/icon/dot_orange_square.gif'><b> $title</b></td></tr-->
							<tr><td> $text</b></td></tr>
							</table>

						</td>
						<td width=1 class='help_row2'></td>
					</tr>
					<tr height=2><td class='help_col' colspan=3></td></tr>
				</table>";

        return $mstring;
    }
}

if(!function_exists('returnZeroValue')){
    function returnZeroValue($value){
        if(empty($value)){
            return 0;
        }else{
            return $value;
            //return number_format($value,1);
        }
    }
}

function CheckGraphValue($value){
    if(empty($value)){
        return 0;
    }else{
        return $value;
        //return number_format($value,2);
    }
}

function DisplpayArrowView($thisvalue,$val2){

}



//function GetTitleNavigation($menu_title, $navigation){
function TitleBar($menu_title, $navigation="", $month_view = true, $excel_down_str=""){
    global $vdate;
    $mstring = '';



    if($_SESSION['admininfo']['mall_type'] == "F"){
        $mstring = "<table width='100%' border='0' cellspacing='0' cellpadding='0' class='list_table_box' style='margin-top:20px;'>
				<tr>
					<td class='list_box_td point' style='padding:20px 0px;' style='line-height:150%;text-align:center;'>
					소호형 서비스는 로그분석 및 이커머스 분석이 1개월 동안만 한시적으로 제공됩니다. <br> 사용을 원하시면 별도의 로그분석 서비스를 신청 하시거나 비즈형으로 전환 하셔야 합니다.
					</td>
				</tr>
			</table>";
    }

    $mstring .= "<table width='100%' border='0' cellspacing='0' cellpadding='0' >
				<tr>					
					<td width='90%' align='left' valign='middle' >
						<b>$navigation &nbsp;</b>
					</td>
					
				</tr>";
    if(!empty($month_view)){
        $mstring .= "
						<tr height=40>
							<td >";
        if(empty($vdate)){
            $vdate = date("Ym",time());

        }
        //echo $vdate;
        for($i=1;$i <= 12;$i++){
            $display_month = date("Ym",mktime(0,0,0,$i,1,substr($vdate,0,4)));
            $display_month2 = date("Y년 m월",mktime(0,0,0,$i,1,substr($vdate,0,4)));
            $mstring .= "
													<div style='cursor:pointer;float:left;padding:5px 5px;border:1px solid silver;margin:5px 5px 0px 0px ;".(($_GET["SelectReport"] == "3" && $display_month == substr($vdate,0,6)) ? "background-color:silver;":"")."'  onclick=\"document.location.href='?SelectReport=3&vdate=".$display_month."&view_status=".$_GET["view_status"]."&SubID=".$_GET["SubID"]."'\">".$display_month2."</div>
													";
        }

        $mstring .= " 
							</td>
							<td align=right nowrap>".$excel_down_str."</td>
						  </tr>";
    }
    $mstring .= "
				<tr height=10><td colspan=2></td></tr>
			</table>";


    if(substr_count($_SERVER["PHP_SELF"],"admin")){
        return $mstring;
        //return $msting ."<div style='padding:10px 0px;width:100%;text-align:left;font-weight:bold;'>$navigation </div>";
    }else{
        return $mstring."";
    }

}

function TitleBarYear($menu_title, $navigation="", $year_view = true, $excel_down_str=""){
    global $vdate;

    $mstring .= "<table width='100%' border='0' cellspacing='0' cellpadding='0' >
				<tr>					
					<td width='90%' align='left' valign='middle' >
						<b>$navigation &nbsp;</b>
					</td>
					
				</tr>";
    if(!empty($year_view)){
        $mstring .= "
						<tr height=40>
							<td >";

        $vdate = date("Y",time());


        for($i=1;$i <= 3;$i++){
            //$display_year = date("Y",mktime(0,0,0,0,0,substr($vdate,0,4)-2+$i));
            //$display_year2 = date("Y년",mktime(0,0,0,1+$i,substr($vdate,6,2),substr($vdate,0,4)));

            $mstring .= "
													<div style='cursor:pointer;float:left;padding:5px 5px;border:1px solid silver;margin:5px 5px 0px 0px ;".(($_GET["SelectReport"] == "3" && $display_year == substr($vdate,0,4)) ? "background-color:silver;":"")."'  onclick=\"document.location.href='?SelectReport=3&vdate=".($vdate-3+$i)."&SubID=".$_GET["SubID"]."'\">".($vdate-3+$i)." 년</div>
													";
        }

        $mstring .= " 
							</td>
							<td align=right>".$excel_down_str."</td>
						  </tr>";
    }
    $mstring .= "
				<tr height=10><td colspan=2></td></tr>
			</table>";


    if(substr_count($_SERVER["PHP_SELF"],"admin")){
        return $mstring;
        //return $msting ."<div style='padding:10px 0px;width:100%;text-align:left;font-weight:bold;'>$navigation </div>";
    }else{
        return $mstring."";
    }

}


function TitleBarTerm($menu_title, $navigation="", $view = true, $excel_down_str=""){
    global $vdate,$sdate,$edate;

    $mstring .= "

	<link type='text/css' href='../js/themes/base/ui.all.css' rel='stylesheet' />
	<script type='text/javascript' src='../js/ui/ui.core.js'></script>
	<script type='text/javascript' src='../js/ui/ui.datepicker.js'></script>

	<script language='javascript'>
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
				if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
					$('#end_datepicker').val(dateText);
				}else{
					$('#end_datepicker').datepicker('setDate','+0d');
				}
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

			//$('#end_timepicker').timepicker();
		});



		function select_date(FromDate,ToDate,dType) {
			var frm = document.searchmember;

			$(\"#start_datepicker\").val(FromDate);
			$(\"#end_datepicker\").val(ToDate);
		}

		</script>

			<table width='100%' border='0' cellspacing='0' cellpadding='0' >
				<tr>					
					<td width='90%' align='left' valign='middle' >
						<b>$navigation &nbsp;</b>
					</td>
				</tr>";
    if(!empty($view)){


        if(empty($sdate)){
            $sdate = date("Ymd", time()-84600*7);
            $edate = date("Ymd", time());
        }

        $vdate = date("Ymd", time());
        $today = date("Ymd", time());
        $vyesterday = date("Ymd", time()-84600);
        $voneweekago = date("Ymd", time()-84600*7);
        $vtwoweekago = date("Ymd", time()-84600*14);
        $vfourweekago = date("Ymd", time()-84600*28);
        $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
        $v15ago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
        $vfourweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
        $vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
        $v2monthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
        $v3monthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));


        $mstring .= "<tr>
							<td colspan='2'>
								<form name='search' >
									<table border='0' cellpadding='0' cellspacing='0' width='100%'>
										<tr>
										<td style='width:100%;' valign=top colspan=3>
											<table width=100%  cellpadding='0' cellspacing='0'  border=0>
												<!--tr height=25><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>적립금 검색하기</b></td></tr-->
												<tr>
													<td align='left' colspan=2  width='100%' valign=top style='padding-top:0px;'>
														<table class='box_shadow' cellpadding=0 cellspacing=0 style='width:100%;' align=left>
															<tr>
																<th class='box_01'></th>
																<td class='box_02'></td>
																<th class='box_03'></th>
															</tr>
															<tr>
																<th class='box_04'></th>
																<td class='box_05' valign=top style='padding:0px;'>
																	<TABLE height=20 cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0>
																	<TR>
																		<TD bgColor=#ffffff style='padding:0 0 0 0;'>
																		<table cellpadding=3 cellspacing=1 width='100%' class='search_table_box'>
																			<tr height=27>
																			  <td class='search_box_title' bgcolor='#efefef' align=center><label for='regdate'><b>기간검색</b></label></td>
																			  <td class='search_box_item' align=left style='padding-left:5px;'>
																				<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
																					<col width=70>
																					<col width=20>
																					<col width=70>
																					<col width=*>
																					<tr>
																						<TD nowrap>
																						<input type='text' name='sdate' class='textbox' value='".$sdate."' style='height:20px;width:70px;text-align:center;' id='start_datepicker'>
																						<!--SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년
																						<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월
																						<SELECT name=FromDD></SELECT> 일 -->
																						</TD>
																						<TD align=center> ~ </TD>
																						<TD nowrap>
																						<input type='text' name='edate' class='textbox' value='".$edate."' style='height:20px;width:70px;text-align:center;' id='end_datepicker'>
																						<!--SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년
																						<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월
																						<SELECT name=ToDD></SELECT> 일 -->
																						</TD>
																						<TD style='padding:0px 10px'>
																							<a href=\"javascript:select_date('$today','$today',1);\"><img src='../images/".$_SESSION['admininfo']['language']."/btn_today.gif'></a>
																							<a href=\"javascript:select_date('$vyesterday','$vyesterday',1);\"><img src='../images/".$_SESSION['admininfo']['language']."/btn_yesterday.gif'></a>
																							<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../images/".$_SESSION['admininfo']['language']."/btn_1week.gif'></a>
																							<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../images/".$_SESSION['admininfo']['language']."/btn_15days.gif'></a>
																							<a href=\"javascript:select_date('$vonemonthago','$today',1);\"><img src='../images/".$_SESSION['admininfo']['language']."/btn_1month.gif'></a>
																							<a href=\"javascript:select_date('$v2monthago','$today',1);\"><img src='../images/".$_SESSION['admininfo']['language']."/btn_2months.gif'></a>
																							<a href=\"javascript:select_date('$v3monthago','$today',1);\"><img src='../images/".$_SESSION['admininfo']['language']."/btn_3months.gif'></a>
																						</TD>
																					</tr>
																				</table>
																			  </td>
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
												<tr >
													<td colspan=3 align=center style='padding:10px 0 20px 0'>
														<input type='image' src='../images/".$_SESSION['admininfo']["language"]."/bt_search.gif' border=0>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									</table>
								</form>
							</td>
						</tr>
						<tr height=40>
							<td >
							</td>
							<td align=right>".$excel_down_str."(단위:원) </td>
						  </tr>";
    }
    $mstring .= "
				<tr height=10><td colspan=2></td></tr>
			</table>";


    if(substr_count($_SERVER["PHP_SELF"],"admin")){
        return $mstring;
        //return $msting ."<div style='padding:10px 0px;width:100%;text-align:left;font-weight:bold;'>$navigation </div>";
    }else{
        return $mstring."";
    }

}

function TitleBarX($vtitle,$vdatestring="", $tablewidth=745){

    $mstring = $mstring."<table cellpadding=0 cellspacing=0 width=$tablewidth border=0>
	<tr><td align=center height=31 valign=middle style='' background='../img/title_bar_head.gif' rowspan=2 width=212 >
		<div align=center id='revolution' style='position:relative;width:190px;top:0px;left:0px;font-size:9pt;color:white;line-height:1;filter:glow(color=black,strength=0)'>
	<!--&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--> <b>".$vtitle."</b>
		</div>
	</td>
	</tr>
	<tr>
	<td width='".($tablewidth-212-123)."' background='../img/title_bar_bg.gif' style='background-position: 0% 100%;background-repeat:repeat-x;padding-left:10px;padding-bottom:6px;' width=212 align=left> $vdatestring </td>
	<td width='121' background='../img/title_bar_bg.gif' style='background-position: 0% 100%;background-repeat:repeat-x;border-right:' width=212 valign=top align=right><!--img src='img/upload.gif' align=absmiddle--> <a href='JavaScript:document.location.reload();'><img src='../img/reload.gif' border=0 align=absmiddle></a></td>
	</tr>";
    $mstring = $mstring."</table>";

    return $mstring;
}

function getNameOfWeekday($WeekNum, $vdate,$type="datename"){
    $WeekName = array("일요일","월요일","화요일","수요일","목요일","금요일","토요일");

    if($type == "datename"){
        return date("Y-m-d",strtotime($vdate))." (".$WeekName[($WeekNum % 7)].")";
    }else if($type == "dayname"){
        return date("Y-m-d",mktime(0,0,0,(int)substr($vdate,4,2),(int)substr($vdate,6,2),(int)substr($vdate,0,4))+60*60*24*$WeekNum)." (".$WeekName[(date('w',strtotime($vdate)) % 7)].")";//." (".$WeekName[0].")";
    }else if($type == "monthname"){
        return substr(date("Y-m-d",mktime(0,0,0,(int)substr($vdate,4,2),1,(int)substr($vdate,0,4))),0,7)." 월";
    }else if($type == "priodname"){
        return date("Y-m-d",mktime(0,0,0,(int)substr($vdate,4,2),(int)substr($vdate,6,2),(int)substr($vdate,0,4)))." (".$WeekName[$WeekNum].")";
    }else if($type == "monthDay"){
        $return_date = date("Y-m-d", strtotime("+".$WeekNum." day", strtotime($vdate)));
        $return_day_num = date('w',strtotime($return_date));
        return $return_date." (".$WeekName[$return_day_num].")";
    }else{
        return $WeekName[$WeekNum];
    }
}

function getTimeString($time){
    if($time == 0){
        return "24:00:00 - ".($time+1).":00:00" ;
    }else{
        return ($time).":00:00 - ".($time+1).":00:00" ;
    }
}

function SetZefoFill($nchar,$fillstr,$nValue){
    if (strlen($nValue) == $nchar){
        return $nValue;
    }else{
        return str_repeat($fillstr,$nchar-strlen($nValue)).$nValue;
    }
}

function CheckDivision($number, $division_number){
    if($division_number == 0){
        return 0;
    }else{
        return $number/$division_number;
    }
}

function ReturnDateFormat($vdateString){
    return date("Y-m-d",mktime(0,0,0,substr($vdateString,4,2),substr($vdateString,6,2),substr($vdateString,0,4)));
}



function BarChartDraw($sValue){

    If ($sValue >= 100){
        $mstring2 ="
		<table border=0 cellpadding=0  cellspacing=1 bgcolor=#000000 height=20 class='list_table_box' style='background:#000000;'>
		<tr><td width=15 height='100%' style='background-color:red;' bgcolor=red></td></tr>
		</table>";
    }else{
        $mstring2 ="
		<table border=0 cellpadding=0  cellspacing=1 bgcolor=#000000 height=20 class='list_table_box' style='background:#000000;'>
		<tr><td width=15 bgcolor=#ffffff></td></tr><tr><td height='".number_format($sValue,0)."%' style='background-color:red;' bgcolor=red></td></tr>
		</table>";
    }
    return $mstring2;
}

function BarchartView($value1, $value2){

    if($value1 == 0 || $value2 == 0){
        $sValue = 0;
    }else if(empty($value1) || empty($value2)){
        $sValue = 0;
    }else{
        $sValue = $value1/$value2*100;
    }

    if ($sValue == "-"){
        if (substr($sValue,2,strpos($sValue,".")) >= 10){
            $mstring = "<table border=0 cellpadding=0 cellspacing=0 align=center><tr><td width=50 style='padding-right:5px;'>".number_format($value2,0)."</td><td align=left width=15>". BarChartDraw($sValue) ."</td><td align=center width=60>". number_format($sValue,1) ." %</td></tr></table>";
        }else{
            $mstring = "<table border=0 cellpadding=0  cellspacing=0 align=center><tr><td width=50 style='padding-right:5px;'>".number_format($value2,0)."</td><td align=left width=15>". BarChartDraw($sValue) ."</td><td align=center width=60>". number_format($sValue,1) ." %</td></tr></table>";
        }

    }else if (substr($sValue,0,1) != "-" && substr($sValue,0,3) == 0.0){
        $mstring = "<table border=0 cellpadding=0  cellspacing=0 align=center><tr><td width=50 style='padding-right:5px;'>".number_format($value2,2)."</td><td align=left width=15><b>-</b></td><td align=center width=60>". number_format($sValue,0) ." %</td></tr></table>";

    }else{

        //if (substr($sValue,strpos($sValue,".")) >= 10){
        if($value2 > 1){
            $mstring = "<table border=0 cellpadding=0  cellspacing=0 align=center><tr><td width=50 style='padding-right:5px;'>".number_format($value2,0)."</td><td align=left width=15>". BarChartDraw($sValue) ."</td><td align=center width=60>". number_format($sValue,1) ." %</td></tr></table>";
        }else{
            $mstring = "<table border=0 cellpadding=0  cellspacing=0 align=center><tr><td width=50 style='padding-right:5px;'>".number_format($value2,2)."</td><td align=left width=15>". BarChartDraw($sValue) ."</td><td align=center width=60>". number_format($sValue,1) ." %</td></tr></table>";
        }

    }
    return $mstring;
}

if(!function_exists('page_bar')){

    function page_bar($total, $page, $max,$add_query="",$paging_type="inner"){
        //$page_string;
        global $cid,$depth,$category_load, $company_id;
        global $nset, $orderby;
        global $HTTP_URL;
        //echo $HTTP_URL;
        //echo $add_query;
        if ($total % $max > 0){
            $total_page = floor($total / $max) + 1;
        }else{
            $total_page = floor($total / $max);
        }

        if (empty($nset)){
            $nset = 1;
        }

        $next = (($nset)*10+1);
        $prev = (($nset-2)*10+1);

        if($paging_type == "inner"){
            $paging_type_param = "view=innerview&";
            $paging_type_target = " target=act";
        }else{
            $paging_type_param = "";
            $paging_type_target = "";
        }


        //echo $total_page.":::".$next."::::".$prev."<br>";
        if ($total){
            $prev_mark = ($prev > 0) ? "<a href='".$HTTP_URL."?".$paging_type_param."nset=".($nset-1)."&page=".(($nset-2)*10+1)."&cid=$cid&depth=$depth&category_load=$category_load&company_id=$company_id&orderby=$orderby$add_query' ".$paging_type_target."><img src='/image/pre10_a.gif' border=0 align=absmiddle></a> " : "<img src='/image/pre10_b.gif' border=0 align=absmiddle> ";
            $next_mark = ($next <= $total_page) ? "<a href='".$HTTP_URL."?".$paging_type_param."nset=".($nset+1)."&page=".($nset*10+1)."&cid=$cid&depth=$depth&category_load=$category_load&company_id=$company_id&orderby=$orderby$add_query' ".$paging_type_target."><img src='/image/next10_a.gif' border=0 align=absmiddle></a>" :  " <img src='/image/next10_b.gif' border=0 align=absmiddle>";
        }

        $page_string = $prev_mark;

//	for ($i = $page - 10; $i <= $page + 10; $i++)

        for ($i = ($nset-1)*10+1 ; $i <= (($nset-1)*10 + 10); $i++)
        {
            if ($i > 0)
            {
                if ($i <= $total_page)
                {
                    if ($i != $page){
                        if($i != (($nset-1)*10+1)){
                            $page_string = $page_string.("<font color='silver'>|</font> <a href='".$HTTP_URL."?".$paging_type_param."nset=$nset&page=$i&cid=$cid&depth=$depth&category_load=$category_load&company_id=$company_id&orderby=$orderby$add_query' style='font-weight:bold;color:gray' ".$paging_type_target.">$i</a> ");
                        }else{
                            $page_string = $page_string.(" <a href='".$HTTP_URL."?".$paging_type_param."nset=$nset&page=$i&cid=$cid&depth=$depth&category_load=$category_load&company_id=$company_id&orderby=$orderby$add_query' style='font-weight:bold;color:gray' ".$paging_type_target.">$i</a> ");
                        }

                    }else{
                        if($i != (($nset-1)*10+1)){
                            $page_string = $page_string.("<font color='silver'>|</font> <font color=#FF0000 style='font-weight:bold'>$i</font> ");
                        }else{
                            $page_string = $page_string.("<font color=#FF0000 style='font-weight:bold'>$i</font> ");
                        }
                    }


                }
            }
        }
        if($nset < (floor($total_page/10)+1)){
            $last_page_string = "<b style='color:gray'>...</b> <a href='".$HTTP_URL."?".$paging_type_param."nset=".(floor($total_page/10)+1)."&page=$total_page&cid=$cid&depth=$depth&category_load=$category_load&company_id=$company_id&orderby=$orderby$add_query' style='font-weight:bold;color:gray' ".$paging_type_target.">$total_page</a> ";
        }
        $page_string = $page_string.$last_page_string.$next_mark;

        return $page_string;
    }
}



//날짜검색 공용함수 이학봉 추가
/*
$sdate_name : 시작날짜 input name	이 값을 기준으로 시,분,초 name 과 id 생성
$edate_name : 마감날짜 input name
$basic_sdate : 시작날짜 기본값
$basic_edate : 마감날짜 기본값
$use_time	: 시간사용시 Y 값을 넘김
$search_type : D: 종료일전 부터 1주일 값 , A:시작일로부터 1주일
*/
function Log_search_date($sdate_name,$edate_name,$basic_sdate='',$basic_edate='',$use_time='',$search_type='D', $property=""){

    
    //echo "search_type:".$search_type;


    $vdate = date("Y-m-d", time());
    $today = date("Y-m-d", time());

    if($search_type == 'D'){

        $vyesterday = date("Y-m-d", time()-86400);
        $voneweekago = date("Y-m-d", time()-86400*7);
        $v15ago = date("Y-m-d", time()-86400*15);
        $vonemonthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-1,substr($vdate,8,2)+1,substr($vdate,0,4)));
        $v2monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-2,substr($vdate,8,2)+1,substr($vdate,0,4)));
        $v3monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-3,substr($vdate,8,2)+1,substr($vdate,0,4)));
    }else if($search_type == 'tax'){

        $vyesterday = date("Y-m-d", time()-86400);
        $voneweekago = date("Y-m-d", time()-86400*7);
        $v15ago = date("Y-m-d", time()-86400*15);
        $vonemonthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-1,substr($vdate,8,2)+1,substr($vdate,0,4)));
        $v2monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-2,substr($vdate,8,2)+1,substr($vdate,0,4)));
        $v3monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)-3,substr($vdate,8,2)+1,substr($vdate,0,4)));

        $first_quarter_1 = date("Y-01-01", time());
        $first_quarter_2 = date("Y-03-31", time());
        $second_quarter_1 = date("Y-04-01", time());
        $second_quarter_2 = date("Y-06-30", time());
        $third_quarter_1 = date("Y-07-01", time());
        $third_quarter_2 = date("Y-09-30", time());
        $fourth_quarter_1 = date("Y-10-01", time());
        $fourth_quarter_2 = date("Y-12-31", time());

    }else{

        $vyesterday = date("Y-m-d", time()+86400);
        $voneweekago = date("Y-m-d", time()+86400*7);
        $v15ago = date("Y-m-d", time()+86400*15);
        $vonemonthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)+1,substr($vdate,8,2)+1,substr($vdate,0,4)));
        $v2monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)+2,substr($vdate,8,2)+1,substr($vdate,0,4)));
        $v3monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,5,2)+3,substr($vdate,8,2)+1,substr($vdate,0,4)));
    }

    $basic_sdate_array = explode(" ",$basic_sdate);	//넘어온값에 시간이 붙어잇을경우 짤라서 시간을 따로 변수에 담아둠
    $basic_sdate_ymd = $basic_sdate_array[0];

    $basic_edate_array = explode(" ",$basic_edate);	//넘어온값에 시간이 붙어잇을경우 짤라서 시간을 따로 변수에 담아둠
    $basic_edate_ymd = $basic_edate_array[0];
    //print_r($basic_edate_array[0]);

    if($use_time == 'Y'){	//시 까지 사용할경우 처리
        if(!empty($basic_sdate_array[1])){
            $start_time_h = strftime('%H',strtotime($basic_sdate));
            $start_time_i = strftime('%M',strtotime($basic_sdate));
            $start_time_s = strftime('%S',strtotime($basic_sdate));
        }

        if(!empty($basic_edate_array[1])){

            $end_time_h = strftime('%H',strtotime($basic_edate));
            $end_time_i = strftime('%M',strtotime($basic_edate));
            $end_time_s = strftime('%S',strtotime($basic_edate));

        }

        $start_time_select = "<select name='".$sdate_name."_h' id='".$sdate_name."_h' >";
        for($i=0;$i<24;$i++){
            $start_time_select .="<option value='".$i."' ".($start_time_h == $i?'selected':'').">".$i."</option>";
        }
        $start_time_select .= "</select> 시";

        $start_time_select .= "<select name='".$sdate_name."_i' id='".$sdate_name."_i'>";
        for($i=0;$i<60;$i++){
            $start_time_select .="<option value='".$i."' ".($start_time_i == $i?'selected':'').">".$i."</option>";
        }
        $start_time_select .= "</select> 분";

        $start_time_select .= "<select name='".$sdate_name."_s' id='".$sdate_name."_s'>";
        for($i=0;$i<60;$i++){
            $start_time_select .="<option value='".$i."' ".($start_time_s == $i?'selected':'').">".$i."</option>";
        }
        $start_time_select .= "</select> 초";


        $end_time_select = "<select name='".$edate_name."_h' id='".$edate_name."_h'>";
        for($i=0;$i<24;$i++){
            $end_time_select .="<option value='".$i."' ".($end_time_h == $i?'selected':'').">".$i."</option>";
        }
        $end_time_select .= "</select> 시";

        $end_time_select .= "<select name='".$edate_name."_i' id='".$edate_name."_i'>";
        for($i=0;$i<60;$i++){
            $end_time_select .="<option value='".$i."' ".($end_time_i == $i?'selected':'').">".$i."</option>";
        }
        $end_time_select .= "</select> 분";

        $end_time_select .= "<select name='".$edate_name."_s' id='".$edate_name."_s'>";
        for($i=0;$i<60;$i++){
            $end_time_select .="<option value='".$i."' ".($end_time_s == $i?'selected':'').">".$i."</option>";
        }
        $end_time_select .= "</select> 초";
    }

    $Contents = "";
    $Contents .= "
	<table cellpadding=3  cellspacing=1 border=0 bgcolor=#ffffff style='float:left;'>
		<tr>
			<td>
				<img src='../../images/".$_SESSION['admininfo']["language"]."/calendar_icon.gif'>
			</td>
			<TD nowrap>
				<input type='text' name='".$sdate_name."' class='textbox point_color' value='".$basic_sdate_ymd."' style='height:20px;width:70px;text-align:center;' id='".$sdate_name."' ".$property."> 일
				".$start_time_select."
			</TD>
			<TD style='padding:0 5px;' align=center> ~ </TD>
			<td>
				<img src='../../images/".$_SESSION['admininfo']["language"]."/calendar_icon.gif'>
			</td>
			<TD nowrap>
				<input type='text' name='".$edate_name."' class='textbox point_color' value='".$basic_edate_ymd."' style='height:20px;width:70px;text-align:center;' id='".$edate_name."' ".$property."> 일
				".$end_time_select."
			</TD>
		</tr>
	</table>";

    if($search_type == 'D'){
        $Contents .= "
	<div style='float:left;padding:7px 10px;'>
		<a href=\"javascript:".$sdate_name."('$today','$today',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_today.gif'></a>
		<a href=\"javascript:".$sdate_name."('$vyesterday','$vyesterday',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_yesterday.gif'></a>
		<a href=\"javascript:".$sdate_name."('$voneweekago','$today',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_1week.gif'></a>
		<a href=\"javascript:".$sdate_name."('$v15ago','$today',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_15days.gif'></a>
		<a href=\"javascript:".$sdate_name."('$vonemonthago','$today',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_1month.gif'></a>
		<a href=\"javascript:".$sdate_name."('$v2monthago','$today',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_2months.gif'></a>
		<a href=\"javascript:".$sdate_name."('$v3monthago','$today',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_3months.gif'></a>
	</div>";
    }else if(empty($search_type)){

    }else if($search_type == 'tax'){
        $Contents .= "
	<div style='float:left;padding:7px 10px;'>
		<a href=\"javascript:".$sdate_name."('$today','$today',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_today.gif'></a>
		<a href=\"javascript:".$sdate_name."('$vyesterday','$vyesterday',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_yesterday.gif'></a>
		<a href=\"javascript:".$sdate_name."('$voneweekago','$today',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_1week.gif'></a>
		<a href=\"javascript:".$sdate_name."('$v15ago','$today',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_15days.gif'></a>
		<a href=\"javascript:".$sdate_name."('$vonemonthago','$today',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_1month.gif'></a>
		<a href=\"javascript:".$sdate_name."('$v2monthago','$today',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_2months.gif'></a>
		<a href=\"javascript:".$sdate_name."('$v3monthago','$today',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_3months.gif'></a>

		<a href=\"javascript:".$sdate_name."('$first_quarter_1','$first_quarter_2',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_first_quarter.gif'></a>
		<a href=\"javascript:".$sdate_name."('$second_quarter_1','$second_quarter_2',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_second_quarter.gif'></a>
		<a href=\"javascript:".$sdate_name."('$third_quarter_1','$third_quarter_2',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_third_quarter.gif'></a>
		<a href=\"javascript:".$sdate_name."('$fourth_quarter_1','$fourth_quarter_2',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_fourth_quarter.gif'></a>
	</div>";
    }elseif($search_type == 'Z'){

    }else{

        $Contents .= "
	<div style='float:left;padding:7px 10px;'>
		<a href=\"javascript:".$sdate_name."('$today','$today',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_today.gif'></a>
		<a href=\"javascript:".$sdate_name."('$today','$voneweekago',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_1week.gif'></a>
		<a href=\"javascript:".$sdate_name."('$today','$v15ago',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_15days.gif'></a>
		<a href=\"javascript:".$sdate_name."('$today','$vonemonthago',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_1month.gif'></a>
		<a href=\"javascript:".$sdate_name."('$today','$v2monthago',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_2months.gif'></a>
		<a href=\"javascript:".$sdate_name."('$today','$v3monthago',1);\"><img src='../../images/".$_SESSION['admininfo']['language']."/btn_3months.gif'></a>
	</div>";
    }

    $Contents .= "
	<script type='text/javascript'>
	<!--
	function ".$sdate_name."(FromDate,ToDate,dType) {
//alert($('#".$sdate_name."').attr('disabled'));
		if($('#".$sdate_name."').attr('disabled') == 'disabled'){
			alert('비활성화 상태에서는 날짜 선택이 불가합니다.');
		}else{
			var frm = document.search_frm;
			$('#".$sdate_name."').val(FromDate);
			$('#".$edate_name."').val(ToDate);
		}
	}

	$(document).ready(function (){
		$('#".$sdate_name."').datepicker({
			//changeMonth: true,
			//changeYear: true,
			monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
			dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
			showMonthAfterYear:true,
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			buttonText: '달력',";

    //생년월일 검색값 년도 셀렉트 박스 추가함 p
    //Z는 년도 셀렉트박스 옵션 추가를 위해 제일 마지막 알파벳으로 넣음.
    if($search_type =='Z'){
        $Contents .="changeYear: true,
				yearRange: '-100:+0',";
    }

    $Contents .= "onSelect: function(dateText, inst){
				if($('#".$edate_name."').val() != '' && $('#".$edate_name."').val() <= dateText){
					$('#".$edate_name."').val(dateText);
				}else{
					$('#".$edate_name."').datepicker('setDate','+0d');
				}
			}
		});

		$('#".$edate_name."').datepicker({
			//changeMonth: true,
			//changeYear: true,
			monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
			dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
			showMonthAfterYear:true,
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			buttonText: '달력',";

    //생년월일 검색값 년도 셀렉트 박스 추가함 p
    if($search_type =='Z'){
        $Contents .="changeYear: true,
				yearRange: '-100:+0',";
    }

    $Contents .= "onSelect: function(dateText, inst){";
    if($search_type == 'D'){
        $Contents .= "
				/*
				var s_d = s_date.getDate();
				var s_m = s_date.getMonth();
				var s_y = s_date.getFullYear();

				//ralert(s_h);
				var sdate = new Date(s_y,s_m,s_d,s_h, s_i);

				alert(sdate);
				*/
				if($('#".$sdate_name."').val() != '' && $('#".$sdate_name."').val() > dateText){
					$('#".$sdate_name."').val(dateText);
				}";
    }
    $Contents .= "
			}

		});
	});
	//-->
	</script>
";

    return $Contents;
}


