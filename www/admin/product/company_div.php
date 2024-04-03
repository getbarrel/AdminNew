<?
include("../class/layout.class");
include_once("brand.lib.php");

$db = new Database;


$sql = "select cr.* , case when depth = 1 then cd_ix  else parent_cd_ix end as group_order from shop_company_div cr where cd_ix = '".$cd_ix."'   ";
$db->query($sql);
if($db->total){
	$db->fetch();
	$act = "update";
}else{
	$act = "insert";
}

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<col width='15%'>
	<col width='35%'>
	<col width='15%'>
	<col width='35%'>
	<tr>
		<td align='left' colspan=6 style='padding-bottom:10px;'> ".GetTitleNavigation("제조사 분류관리", "상품분류관리 > 제조사 분류관리 ")."</td>
	</tr>
	<tr>
		<td align='left' colspan=4 style='padding-bottom:15px;'>
			<div class='tab'>
				<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01'  ".($info_type == "list" || $info_type == "" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02'  ><a href='company_list.php?mmode=".$mmode."&info_type=list'>제조사 리스트</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' ".($info_type == "add" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' ><a href='company.php?mmode=".$mmode."&info_type=add'>제조사 등록</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_04' ".($info_type == "category" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' ><a href='company_div.php?mmode=".$mmode."&info_type=category'>제조사 분류관리</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							
							<table id='tab_05' ".($info_type == "batch" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' ><a href='company_batch.php?mmode=".$mmode."&info_type=batch'>제조사 일괄등록</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	<tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u> 제조사 분류 추가/수정</b></div>")."</td>
	</tr>
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	<col width='20%'>
	<col width='*'>
	<tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b> 제조사 분류 : </b></td>
	    <td class='input_box_item'>
	    	<input type=radio name='depth' value='1' id='depth_1' onclick=\"document.getElementById('parent_cd_ix').disabled=true;\" checked><label for='depth_1'>1차 제조사 분류</label>
	    	<input type=radio name='depth' value='2' id='depth_2' onclick=\"document.getElementById('parent_cd_ix').disabled=false;\" ><label for='depth_2'>2차 제조사 분류</label>
	    	".getcompanyFirstDIV($db->dt[cd_ix])." <span class='small'><!--2차 제조사 분류 등록하기 위해서는 반드시 1차브랜드 분류를 선택하셔야 합니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
	    </td>	  
	    <td class='input_box_title'> <b>제조사 분류명 :</b> </td>
		<td class='input_box_item'><input type=text class='textbox' name='div_name' value='".$db->dt[div_name]."' style='width:230px;'> <span class=small></span></td>
	</tr>
	<tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>노출순서 : </b></td>
		<td class='input_box_item'><input type=text class='textbox' name='vieworder' value='".$db->dt[vieworder]."' style='width:130px;'> <span class=small><!--노출순서에 의해서 셀렉트 박스 및 리스트 노출순서가 정해집니다--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span> </td>
	 
	    <td class='input_box_title'> <b>사용유무 : </b></td>
	    <td class='input_box_item'>
	    	<input type=radio name='disp' id='disp_1' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ><label for='disp_0'>사용하지않음</label>
	    </td>
	</tr>
	<tr bgcolor=#ffffff height=100>
			<td class='input_box_title'> <b>분류이미지 : </b></td>
			<td  id='brandimgarea' colspan=3 style='padding:5px;vertical-align:top;'>
			<input type=file class='textbox' title='제조사 이미지' name='brand_div_img' size=15 >
			";
if($bd_ix && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand_div/".$bd_ix."/brand_div_".$bd_ix.".gif")){
	$image_info = getimagesize ($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand_div/".$bd_ix."/brand_div_".$bd_ix.".gif");
	$width = $image_info[0];
	$Contents01 .= "<div style='padding:5px;'><a href=\"javascript:deleteImage('brand_div','".$bd_ix."');\">[삭제]</div>";

	if($width > 100){
		$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/brand_div/".$bd_ix."/brand_div_".$bd_ix.".gif' width=100>";
	}else{
		$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/brand_div/".$bd_ix."/brand_div_".$bd_ix.".gif'>";
	}
}

$Contents01 .= "
			</td>
		</tr>
	</table>";
/*
$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small>
		  <u>$board_name </u> 에 이용할 브랜드 분류명을  입력해주세요
	</td>
</tr>
</table>
";*/
$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');
/*$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		<tr>
			<td align='left'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  브랜드 분류 목록</b></div>")."</td>
		</tr>
	</table>";*/
$Contents02 = "
	<div style='width:100%;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  제조사 분류 목록</b></div>")."</div>";

$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box' style='margin-top:3px;'>
	<col width='*'>
	<col width=100>
	<col width=100>
	<col width=150>
	<col width=150>
	<tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='m_td'> 제조사 분류명</td>
	    <td class='m_td'> 이미지</td>
		<td class='m_td'> 노출순서</td>
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='m_td'> 관리</td>
	</tr>";

$sql = "select cr.* , case when depth = 1 then cd_ix  else parent_cd_ix end as group_order from shop_company_div cr  order by  group_order asc ,depth asc , vieworder asc ";
$db->query($sql);
/*

$sql = 	"SELECT bdiv.*, sum(case when bbs_div is NULL then 0 else 1 end) as  group_bbs_cnt , case when depth = 1 then bd_ix  else parent_bd_ix end as group_order
		FROM ".TBL_BBS_MANAGE_DIV." bdiv left join bbs_".$board_ename." bbs on bdiv.bd_ix = bbs.bbs_div where bm_ix = '$bm_ix'
		group by bd_ix
		order by group_order asc, depth asc";



//echo $sql;
$db->query($sql);
*/

if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		<tr bgcolor=#ffffff height=30 align=center>
			<td class='list_box_td point' style='padding-left:20px;'>
				<table cellpadding='0' cellspacing='0' border='0' width='100%'>
					<tr>
						<td width=".(20*$db->dt[depth])."></td>
						<td width='*' align='left'>".$db->dt[div_name]."</td>
					</tr>
				</table>
			</td>
		    <td class='list_box_td list_bg_gray'>";
		if($db->dt[bd_ix] && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand_div/".$db->dt[bd_ix]."/brand_div_".$db->dt[bd_ix].".gif")){
			$image_info = getimagesize ($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand_div/".$db->dt[bd_ix]."/brand_div_".$db->dt[bd_ix].".gif");
			$width = $image_info[0]; 

			if($width > 100){
				$Contents02 .= "<img src='".$admin_config[mall_data_root]."/images/brand_div/".$db->dt[bd_ix]."/brand_div_".$db->dt[bd_ix].".gif' width=100>";
			}else{
				$Contents02 .= "<img src='".$admin_config[mall_data_root]."/images/brand_div/".$db->dt[bd_ix]."/brand_div_".$db->dt[bd_ix].".gif'>";
			}
		}

$Contents02 .= "
			</td>
			<td class='list_box_td list_bg_gray'>".$db->dt[vieworder]."</td>
		    <td class='list_box_td '>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		    <td class='list_box_td '>
		    	<!--a href=\"javascript:updateGroupInfo('".$db->dt[cd_ix]."','".$db->dt[div_name]."','".$db->dt[depth]."','".$db->dt[vieworder]."','".$db->dt[group_order]."','".$db->dt[disp]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a-->
				<a href=\"?cd_ix=".$db->dt[cd_ix]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
	    		<a href=\"javascript:deleteGroupInfo('delete','".$db->dt[cd_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
		    </td>
		</tr> ";
	}
}else{
	$Contents02 .= "
		<tr bgcolor=#ffffff height=50>
		    <td align=center colspan=6>등록된 제조사 분류이 없습니다. </td>
		</tr>";
}
$Contents02 .= "

	  </table>";



$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";


$Contents = "<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents = $Contents."<form name='brand_form' action='company_div.act.php' method='post' onsubmit='return CheckValue(this)' enctype='multipart/form-data' target='act'>
<input name='mmode' type='hidden' value='$mmode'>
<input name='act' type='hidden' value='".$act."'>
<input name='cd_ix' type='hidden' value='".$cd_ix."'>";
$Contents = $Contents."<tr><td width='100%'>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."</table >";
/*
$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >상품분류관리 브랜드 분류 설정은 <b>2단계</b>까지 가능합니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>브랜드 분류명 수정</u>을 원하실 경우는 수정버튼을 클릭하시면 상단에 해당정보가 표시되고 수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr>
	</table>
	";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'D');

	$help_text = HelpBox("제조사 분류관리", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>

 
function deleteImage(imagetype, bd_ix){
	if(confirm('해당이미지를 정말로 삭제하시겠습니까?')){		
		window.frames['act'].location.href = './brand_div.act.php?mode=image_delete&imagetype='+imagetype+'&bd_ix='+bd_ix;
	}
}



 function CheckValue(frm){
 	if(frm.depth[1].checked){
 		if(frm.parent_cd_ix.value == ''){
	 		alert(language_data['region.php']['A'][language]);
			//'2차 제조사 분류을 등록하기 위해서는 1차브랜드 분류을 반드시 선택하셔야 합니다.'
	 		return false;
 		}
 	}

 	if(frm.div_name.value.length < 1){
 		alert(language_data['region.php']['B'][language]);
		//'등록하시고자 하는 상품분류관리 제조사 분류명을 입력해주세요'
 		frm.div_name.focus();
 		return false;
 	}

}

function updateGroupInfo(cd_ix,div_name,depth, vieworder,group_order,disp){
	var frm = document.brand_form;

	frm.act.value = 'update';
	frm.cd_ix.value = cd_ix;
	frm.div_name.value = div_name;
	frm.vieworder.value = vieworder;
	if(disp=='1') {
		frm.disp[0].checked = true;
	} else {
		frm.disp[1].checked = true;
	}
alert('123');
	if(depth == '1'){
		frm.depth[0].checked = true;
		document.getElementById('parent_cd_ix').disabled=true;
		document.getElementById('parent_cd_ix').options[0].selected='selected';
	}else{
		frm.depth[1].checked = true;
		document.getElementById('parent_cd_ix').disabled=false;
		document.getElementById('parent_cd_ix').value=group_order;
	}

}

 function deleteGroupInfo(act, cd_ix){
 	if(confirm(language_data['region.php']['C'][language])){//'해당브랜드 분류 정보를 정말로 삭제하시겠습니까?'
 		var frm = document.brand_form;
 		frm.act.value = act;
 		frm.cd_ix.value = cd_ix;
 		frm.submit();
 	}
}
 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->Navigation = "상품관리 > 제조사분류관리 > 제조사 분류관리";
	$P->NaviTitle = "제조사 분류관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->Navigation = "상품관리 > 제조사분류관리 > 제조사 분류관리";
	$P->title = "제조사 분류관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


?>