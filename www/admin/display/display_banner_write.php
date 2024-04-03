<?
/////////////////////////////////////////
//  
//  제목 : 전시관리 > 배너관리 > 배너등록
//
/////////////////////////////////////////
include("../class/layout.class");
include_once("../display/display.lib.php");

$db = new Database();

if($banner_ix){
	/*
	$sql = "select bi.*,bd.div_name,ccd.com_name
				from shop_bannerinfo bi
				LEFT JOIN shop_banner_div bd ON bi.banner_page=bd.div_ix
				LEFT JOIN common_company_detail ccd USING (company_id)
				where banner_ix ='$banner_ix' ";
	*/

	$sql = "select bi.company_id,bi.banner_page,bi.banner_position, bi.banner_link,bi.banner_target,bi.banner_desc,bi.banner_name,bi.banner_img,
				bi.banner_width,bi.banner_height,bi.disp,
				date_format(bi.use_sdate,'%Y%m%d%H%i') as use_sdate,
				date_format(bi.use_sdate, '%H') as use_stime, date_format(bi.use_sdate, '%i') as use_sminute,
				date_format(bi.use_edate,'%Y%m%d%H%i') as use_edate,
				date_format(bi.use_edate, '%H') as use_etime, date_format(bi.use_edate, '%i') as use_eminute,
				bi.regdate,bd.div_name,ccd.com_name,
				bd.parent_div_ix, bd.depth, b.div_ix, b.cid, b.md_id, b.goal_cnt, b.sdate, b.edate, bi.banner_img_on, banner_on_use
				from shop_bannerinfo bi
				INNER JOIN shop_display_banner b ON bi.banner_ix = b.banner_ix AND b.banner_div = '$banner_div'
				LEFT JOIN shop_banner_div bd ON b.div_ix=bd.div_ix
				LEFT JOIN common_company_detail ccd on bi.company_id=ccd.company_id				
				where bi.banner_ix ='$banner_ix' ";
	$db->query($sql);

	//$db->query("SELECT * FROM shop_bannerinfo where banner_ix ='$banner_ix' ");
	$db->fetch();
	$act = "update";
	$com_name = $db->dt[com_name];
	$company_id = $db->dt[company_id];
	$banner_page = $db->dt[banner_page];
	$banner_position = $db->dt[banner_position];
	$banner_name = $db->dt[banner_name];
	$banner_link = $db->dt[banner_link];
	$banner_target = $db->dt[banner_target];
	$banner_desc = $db->dt[banner_desc];
	$banner_img = $db->dt[banner_img];
	$banner_width = $db->dt[banner_width];
	$banner_height = $db->dt[banner_height];
	$parent_div_ix = $db->dt[parent_div_ix];
	$depth = $db->dt[depth];
	$div_ix = $db->dt[div_ix];
	$cid2 = $db->dt[cid];
	$md_id = $db->dt[md_id];
	$goal_cnt = $db->dt[goal_cnt];
	$disp = $db->dt[disp];
	$display_sdate		= $db->dt[sdate];
	$display_edate		= $db->dt[edate];
	$banner_img_on = $db->dt[banner_img_on];
	$banner_on_use = $db->dt[banner_on_use];

	if ($depth==0){	// 분류코드가 1depth (0) 이라면
		$parent_div_ix = $div_ix;	// 이렇게 안하면 1depth 만 사용하고 있는 상태에서 선택한 1depth 가 자동 select 가 안됨
	}else{
		$parent_div_ix = $db->dt[parent_div_ix];
	}

	if ($display_sdate) {
		$sDate = substr($display_sdate,0,8);
		$FromHH = substr($display_sdate,8,2);
		$FromMI = substr($display_sdate,10,2);
	}
	if ($display_edate){
		$eDate = substr($display_edate,0,8);
		$ToHH = substr($display_edate,8,2);
		$ToMI = substr($display_edate,10,2);
	}

	//echo "div_ix : ".$parent_div_ix;

	$startDate = $sDate;
	$endDate = $eDate;

}else{
	$act = "insert";
	$next10day = mktime(date("H"), date("i"), date("s"), date("m")  , date("d")+10, date("Y"));

	$sDate = date("Ymd");
	$eDate = date("Ymd",$next10day);

	$startDate = date("Ymd");
	$endDate = date("Ymd",$next10day);
}
$Contents01 = "
<iframe name='iframe_act_thispage' id='iframe_act_thispage' width=600 height=300 frameborder=0 style='display:none'  ></iframe>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		<col width='20%' />
		<col width='80%' />
	  <tr >
		<td align='left' colspan=2> ".GetTitleNavigation("배너관리", "전시관리 > 배너관리 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>배너 등록/수정</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	   <col width='20%' />
		<col width='80%' />
	   <tr>
		<td class='search_box_title' >  배너분류</td>
		<td class='search_box_item' >".makeBannerDivSelectBox($db,  'srch_div', $parent_div_ix, 0,  '1차분류', 'onChange="loadBannerDivix(this.form,\'div_ix\', this.value)"')."&nbsp;".makeBannerDivSelectBox($db, 'div_ix', $div_ix, 1,  $display_name = '2차분류', $onchange='')."</td>
	</tr>
	  <tr>
		<td class='search_box_title' >  카테고리선택</td>
		<td class='search_box_item' >
			<table border=0 cellpadding=0 cellspacing=0>
				<tr>
					<td style='padding-right:5px;'>".getCategoryList3("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid2)."</td>
					<td style='padding-right:5px;'>".getCategoryList3("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid2)."</td>
					<td style='padding-right:5px;'>".getCategoryList3("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid2)."</td>
														<td>".getCategoryList3("상세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='상세분류'", 3, $cid2)."</td>
				</tr>
			</table>
		</td>
	</tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>배너명 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item' ><input type=text class='textbox' name='banner_name' value='".$banner_name."' title='배너명' validation=true style='width:220px;'> <span class=small></span></td>
	  </tr>
	  
	   <tr>
			<td class='search_box_title'>배너 노출기간</td>
			  <td class='search_box_item'  >
				<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width='100%'>
				<col width=70>
				<col width=120>
				<col width=20>
				<col width=70>
				<col width=120>
				<col width=*>
					<tr>
						<td nowrap>
						<input type='text' name='sdate' class='textbox' value='".$sDate."' style='height:18px;width:70px;text-align:center;' id='start_datepicker'>
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
							<input type='text' name='edate' class='textbox' value='".$eDate."' style='height:18px;width:70px;text-align:center;' id='end_datepicker'>
						</td>
						<td nowrap>
							<SELECT name=ToHH>";
							for($i=0;$i < 24;$i++){
								$Contents01.= "<option value='".$i."' ".($ToHH == $i ? "selected":"").">".$i."</option>";
												}
								$Contents01.= "
							</SELECT> 시
							<SELECT name=ToMI>";
							for($i=0;$i < 60;$i++){
								$Contents01.= "<option value='".$i."' ".($ToMI == $i ? "selected":"").">".$i."</option>";
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
		</tr>";
if($admininfo["admin_level"] == 9 && ($admininfo[mall_type] == "B"  || $admininfo[mall_type] == "O")){
 $Contents01 .= "
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 사용유무 </td>
	    <td class='input_box_item' >
	    	<input type=radio name='disp' id='disp_1' value='1' ".($disp == "1" || $disp == "" ? "checked":"")."><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ".($disp == "0" ? "checked":"")."><label for='disp_0'>사용하지않음</label>
	    </td>
		 
	  </tr>";
}else{
 $Contents01 .= "
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 사용유무 </td>
	    <td class='input_box_item' >
	    	<input type=radio name='disp' id='disp_1' value='1' ".($disp == "1" || $disp == "" ? "checked":"")."><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ".($disp == "0" ? "checked":"")."><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>";
}		
 $Contents01 .= "	
	<tr bgcolor=#ffffff >
	    <td class='input_box_title'> 마우스오버 사용유무 </td>
	    <td class='input_box_item' >
	    	<input type=radio name='banner_on_use' id='disp_1' value='Y' ".($banner_on_use == "Y" || $banner_on_use == "" ? "checked":"")."><label for='disp_1'>사용</label>
	    	<input type=radio name='banner_on_use' id='disp_0' value='N' ".($banner_on_use == "N" ? "checked":"")."><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>
	<tr bgcolor=#ffffff >
	    <td class='input_box_title'> 이미지 설정 </td>
	    <td class='input_box_item' >
			<table>
				<tr bgcolor=#ffffff >
					<td class='input_box_title'> <b>기본이미지 <img src='".$required3_path."'></b> </td>
					<td class='input_box_item' style='padding:5px;' colspan=3> <input type=file class='textbox' name='banner_img' value='' ><br> ";
					if($banner_img != ""){
							if(substr_count($banner_img,'.swf') > 0){
								$Contents01 .= "<script language='javascript'>generate_flash('".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img."', '".$banner_width."', '".$banner_height."');</script>";
							}else{

								$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img."' style='vertical-align:middle;margin:5px 0px ;' width='".$banner_width."' height='".$banner_height."'>";
							}
					}else{
						$Contents01 .= "";
					}
					$img_size = getimagesize($DOCUMENT_ROOT.$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img);
					$file_size = filesize($DOCUMENT_ROOT.$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img);
				$Contents01 .= "
					</td>
				  </tr>

				  <tr bgcolor=#ffffff >
					<td class='input_box_title'> <b>마우스오버 이미지 <img src='".$required3_path."'></b> </td>
					<td class='input_box_item' style='padding:5px;' colspan=3> <input type=file class='textbox' name='banner_img_on' value='' ><br> ";
					if($banner_img_on != ""){
							if(substr_count($banner_img_on,'.swf') > 0){
								$Contents01 .= "<script language='javascript'>generate_flash('".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img_on."', '".$banner_width."', '".$banner_height."');</script>";
							}else{

								$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/".$banner_img_on."' style='vertical-align:middle;margin:5px 0px ;' width='".$banner_width."' height='".$banner_height."'>";
							}
					}else{
						$Contents01 .= "";
					}					
				$Contents01 .= "
					</td>
				  </tr>
				<tr bgcolor=#ffffff >
					<td class='input_box_title'> 링크 URL </td>
					<td class='input_box_item' colspan=3>
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
				<tr bgcolor=#ffffff >
					<td class='input_box_title'> 가로</td>
					<td class='input_box_item'><input type=text class='textbox number' name='banner_width' value='".$banner_width."' title='배너가로' validation=true style='width:120px;'> px <span class=small></span></td>				
					<td class='input_box_title'> 세로</td>
					<td class='input_box_item'><input type=text class='textbox number' name='banner_height' value='".$banner_height."' title='배너세로' validation=true style='width:120px;'> px <span class=small></span></td>
				 </tr>
				 <tr>
					<td class='search_box_title' >  담당 MD</td>
					<td class='search_box_item' > ".makeMDSelectBox($db,'md_id',$md_id,'')."</td>
					<td class='search_box_title'> 목표유입</td>
					<td class='input_box_item'><input type=text class='textbox number' name='goal_cnt' value='".$goal_cnt."' title='목표유입수' validation=true style='width:40px;'> 번 <span class=small></span></td>
				</tr>
			</table>
	    </td>
	  </tr>	
	    ";
	  if($banner_img != ""){
	$Contents01 .= "	<tr bgcolor=#ffffff >
	    <td class='input_box_title'> 이미지 정보  </td>
	    <td class='input_box_item' >가로 : ".$img_size[0]."px &nbsp;&nbsp;&nbsp;세로 : ".$img_size[1]."px &nbsp;&nbsp;&nbsp; 용량 : ".$file_size." Byte</td>
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

$Contents = "<form name='banner_frm' action='display_banner.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data'>
<input name='act' type='hidden' value='$act'>
<input name='banner_ix' type='hidden' value='$banner_ix'>
<input name='SubID' type='hidden' value='$SubID'>
<input type='hidden' name='cid2' value='$cid2'>
<input type='hidden' name='depth' value=''>
<input type='hidden' name='banner_div' value='".$banner_div."'>
";
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
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');


$Contents .= HelpBox("배너관리", $help_text,70);


$Script = "
<script type='text/javascript' src='display.js'></script>
<script language='javascript' src='banner.write.js'></script>
<Script Language='JavaScript'>
function init(){
	var frm = document.banner_frm;
}

function loadCategory(sel,target) {
	if (target != 'cid2'){
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;

		var depth = $('select[name='+sel.name+']').attr('depth');
		window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	}

}


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
				//}else{
					//$('#end_datepicker').datepicker('setDate','+0d');
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
	var frm = document.serchform;

	$(\"#start_datepicker\").val(FromDate);
	$(\"#end_datepicker\").val(ToDate);
}

</script>
";

if ($div_ix){
	$Contents .="
	<SCRIPT LANGUAGE='JavaScript'>loadBannerDivix(document.banner_frm, 'div_ix', ".$div_ix.");</SCRIPT>";
}

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = display_menu();
$P->Navigation = "프로모션/전시 > 배너관리 > ".$__arr_display_div_banner[$banner_div]." 등록";
$P->OnloadFunction = "init();";
$P->title = $__arr_display_div_banner[$banner_div]." 등록";
$P->strContents = $Contents;
echo $P->PrintLayOut();




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