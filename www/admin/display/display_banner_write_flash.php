<?
/////////////////////////////////////////////
//  
//  제목 : 전시관리 > 배너관리 > 플래시 배너등록
//  작성자 : 이현우 (2013-05-20)
//
/////////////////////////////////////////////
include("../class/layout.class");

$db = new Database();
$db2 = new Database;
 

if($mf_ix){
	$act = "update";
	$db->query("SELECT * FROM ".TBL_SHOP_MANAGE_FLASH." bi INNER JOIN shop_display_banner b ON bi.mf_ix = b.banner_ix AND b.banner_div = '$banner_div'  LEFT JOIN shop_banner_div bd ON b.div_ix=bd.div_ix where bi.mf_ix = '".$mf_ix."' ");
	$db->fetch();
	$path = $admin_config[mall_data_root]."/images";
	$path = $path."/flash_data/";
	$depth = $db->dt[depth];
	$div_ix = $db->dt[div_ix];
	$cid2 = $db->dt[cid];
	$md_id = $db->dt[md_id];
	$goal_cnt = $db->dt[goal_cnt];
	$disp = $db->dt[disp];
	$display_sdate		= $db->dt[sdate];
	$display_edate		= $db->dt[edate];
	$mf_name = $db->dt[mf_name];
	$time_sec = $db->dt[time_sec];

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
<iframe name='iframe_act_thispage' id='iframe_act_thispage' width=600 height=300 frameborder=0  style='display:none'></iframe>
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
	   <col width='10%' />
	   <col width='20%' />
	   <col width='10%' />
		<col width='20%' />
		<col width='10%' />
		<col width='20%' />
	   <tr>
		<td class='search_box_title' >  배너분류</td>
		<td class='search_box_item' colspan=5>".makeBannerDivSelectBox($db,  'srch_div', $parent_div_ix, 0,  '1차분류', 'onChange="loadBannerDivix(this.form,\'div_ix\', this.value)"')."&nbsp;".makeBannerDivSelectBox($db, 'div_ix', $div_ix, 1,  $display_name = '2차분류', $onchange='')."</td>
	</tr>
	  <tr>
		<td class='search_box_title' >  카테고리선택</td>
		<td class='search_box_item' colspan=5>
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
	    <td class='input_box_item' colspan=5><input type=text class='textbox' name='mf_name' value='".$mf_name."' title='배너명' validation=true style='width:220px;'> <span class=small></span></td>
	  </tr>
	  
	   <tr>
			<td class='search_box_title'>배너 노출기간</td>
			  <td class='search_box_item' colspan=5 >
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
	    <td class='input_box_item' colspan=5>
	    	<input type=radio name='disp' id='disp_1' value='1' ".($disp == "1" || $disp == "" ? "checked":"")."><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ".($disp == "0" ? "checked":"")."><label for='disp_0'>사용하지않음</label>
	    </td>
		 
	  </tr>";
}else{
 $Contents01 .= "
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 사용유무 </td>
	    <td class='input_box_item' colspan=5>
	    	<input type=radio name='disp' id='disp_1' value='1' ".($disp == "1" || $disp == "" ? "checked":"")."><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ".($disp == "0" ? "checked":"")."><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>";
}		
 $Contents01 .= "	
	<tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>효과선택 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item' colspan=5>
			<select name='mf_effect' style='250px;'  title='효과선택'>
				<option value='' >선택하세요</option>
				<option value='S' ".CompareReturnValue(S,$db->dt['mf_effect']).">슬라이드</option>
				<option value='F' ".CompareReturnValue(F,$db->dt['mf_effect']).">패이드인</option>
				<option value='R' ".CompareReturnValue(R,$db->dt['mf_effect']).">랜덤</option>
				<option value='T' ".CompareReturnValue(T,$db->dt['mf_effect']).">지그재그</option>
			</select>
		</td>
	  </tr>	 
	  <tr bgcolor=#ffffff >
			<td class='search_box_title' >  담당 MD</td>
			<td class='search_box_item'> ".makeMDSelectBox($db,'md_id',$md_id,'')."	</td>				
			<td class='search_box_title'> 목표유입</td>
			<td class='input_box_item'><input type=text class='textbox number' name='goal_cnt' value='".$goal_cnt."' title='목표유입수' validation=true style='width:40px;'> 번</td>
			<td class='search_box_title'> 지속시간</td>
			<td class='input_box_item'><input type=text class='textbox number' name='time_sec' value='".$time_sec."' title='지속시간'  style='width:40px;'> 초</td>
	</tr>

	  <tr >
	    <td class='input_box_title' valign='middle'>
			<table cellpadding=0 cellspacing=0 height='78px;'>
				<tr>
					<td width='70px'><b style='color:#000000;'>상세내용 <img src='".$required3_path."'></b><td>
					<td><div style='margin-left:15px;margin-top:5px;'><img src='../images/".$admininfo["language"]."/btn_add.gif' alt='옵션추가' id='flash_addbtn'> <img src='../images/".$admininfo["language"]."/btn_del.gif' alt='옵션삭제' id='flash_delbtn'><div>
					</td>
				</tr>
			</table>
		</td>
		<td class='input_box_item' colspan=5>
		<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' id='flash_table'>
		";
	//$mfdArr = array();
	$db2->query("SELECT * FROM shop_manage_flash_detail  where mf_ix = '".$mf_ix."' order by mfd_ix ASC ");//order by 가 regdate 로 되어 있던 것을 고침 kbk 13/02/15
	if($db2->total){
		$mfdArr = $db2->fetchall();
	}
$clon_no = 0;
if(is_array($mfdArr)){
	foreach($mfdArr as $_key=>$_value){

		if($_key == 0) {
		$Contents01 .= "<tbody>";
		} else if($_key == 1){
		$Contents01 .= "<tfoot>";
		}
		$Contents01 .= "
				  <tr bgcolor=#ffffff  class='clone_tr'>

					<td height='25' style='padding:10px 0; solid #d3d3d3;'>
					<input type=hidden name='mfd_ix[]' class='mfd_ix' value='".$mfdArr[$_key][mfd_ix]."' style='width:230px;' validation=false>
					 첨부파일 : <input type=file class='textbox' name='mf_file[]' style='width:255px;' validation=false title='파일'> <span class='file_text'>".$mfdArr[$_key][mf_file]."<input type='checkbox' name='nondelete[".$mfdArr[$_key][mfd_ix]."]' value='1' checked>업로드된 파일유지</span><br><br>
					 링&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;크 : <input type=text class='textbox mf_link' name='mf_link[]' value='".$mfdArr[$_key][mf_link]."' style='width:248px;' validation=true title='링크'>
					 타 이 틀 : <input type=text class='textbox mf_title' name='mf_title[]' value='".$mfdArr[$_key][mf_title]."' style='width:230px;' validation=true title='타이틀'>
					</td>
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
					<input type=hidden name='mfd_ix[]' value='' style='width:230px;' validation=false>
					 첨부파일 : <input type=file class='textbox' name='mf_file[]' style='width:255px;' validation=true title='파일'> <br><br>
					 링&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;크 : <input type=text class='textbox mf_link' name='mf_link[]' value='' style='width:248px;' validation=true title='링크'>
					 타 이 틀 : <input type=text class='textbox mf_title' name='mf_title[]' value='' style='width:230px;' validation=true title='타이틀'>
					</td>
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

		</td>
	  </tr>	   
	  ";
 $Contents01 .= "
	  </table>";



if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
}

$Contents = "<form name='banner_frm' action='display_banner_flash.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data'>
<input name='act' type='hidden' value='$act'>
<input name='mf_ix' type='hidden' value='".$mf_ix."'>
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
<Script Language='JavaScript' src='design.js'></Script>
 <Script Language='JavaScript'>
var eqIndex = $clon_no;
$(document).ready(function () {
	var copy_text;
	$('#flash_addbtn').click(function(){
		eqIndex++;
		copy_text = $('#flash_table tbody:first').html();
		$(copy_text).clone().appendTo('#flash_table tfoot');
		$('.file_text:eq('+eqIndex+')').text('');
		$('.mf_link:eq('+eqIndex+')').val('');
		$('.mf_title:eq('+eqIndex+')').val('');
		$('.mfd_ix:eq('+eqIndex+')').val('');
	});

	$('#flash_delbtn').click(function(){
		var len = $('#flash_table .clone_tr').length;
		if(len > 1){
			eqIndex--;
			$('#flash_table .clone_tr:last').remove();
		}else{
			return false;
		}
	});

});
</script>
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