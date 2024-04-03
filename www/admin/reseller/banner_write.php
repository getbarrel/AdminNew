<?
include("../class/layout.class");

$db = new Database();

$act="insert";

if($rsl_banner_ix){
	$db->query("SELECT * FROM reseller_banner where rsl_banner_ix ='$rsl_banner_ix' ");
	$db->fetch();
	$act = "update";
	$banner_page = $db->dt[banner_page];
	$banner_name = $db->dt[banner_name];
	$banner_link = $db->dt[banner_link];
	$banner_target = $db->dt[banner_target];
	$banner_desc = $db->dt[banner_desc];
	$banner_img = $db->dt[banner_img];
	$banner_width = $db->dt[banner_width];
	$banner_height = $db->dt[banner_height];
}


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		<col width='20%' />
		<col width='80%' />
	  <tr >
		<td align='left' colspan=2> ".GetTitleNavigation("배너관리", "리셀러관리 > 리셀러설정 > 배너관리 ")."</td>
	  </tr>
	   </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>배너명 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'><input type=text class='textbox' name='banner_name' value='".$banner_name."' title='배너명' validation=true style='width:220px;'> <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>배너이미지 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'> <input type='file' class='textbox' name='banner_img' value='' ><br> ";
	if($banner_img != ""){
			if(substr_count($banner_img,'.swf') > 0){
				$Contents01 .= "<script language='javascript'>generate_flash('".$admin_config[mall_data_root]."/images/banner/".$rsl_banner_ix."/".$banner_img."', '".$banner_width."', '".$banner_height."');</script>";
			}else{

				$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/banner/".$rsl_banner_ix."/".$banner_img."' style='vertical-align:middle'>";
			}
		//$Contents01 .= "<img src='".$admin_config[mall_data_root]."/images/banner/".$rsl_banner_ix."/".$banner_img."' style='vertical-align:middle'>";
	}else{
		$Contents01 .= "";
	}
	$img_size = getimagesize($DOCUMENT_ROOT.$admin_config[mall_data_root]."/images/banner/".$rsl_banner_ix."/".$banner_img);
	$file_size = filesize($DOCUMENT_ROOT.$admin_config[mall_data_root]."/images/banner/".$rsl_banner_ix."/".$banner_img);
$Contents01 .= "
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 배너링크 : </td>
	    <td class='input_box_item'>
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
	    <td class='input_box_item'><input type=text class='textbox' name='banner_width' value='".$banner_width."' title='배너가로' validation=true style='width:220px;'> <span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 세로 : </td>
	    <td class='input_box_item'><input type=text class='textbox' name='banner_height' value='".$banner_height."' title='배너세로' validation=true style='width:220px;'> <span class=small></span></td>
	  </tr>	  ";
	  if($banner_img != ""){
	$Contents01 .= "	<tr bgcolor=#ffffff >
	    <td class='input_box_title'> 이미지 정보  </td>
	    <td class='input_box_item'>가로 : ".$img_size[0]."px &nbsp;&nbsp;&nbsp;세로 : ".$img_size[1]."px &nbsp;&nbsp;&nbsp; 용량 : ".$file_size." Byte</td>
	  </tr>";
	  }
	 $Contents01 .= "<tr bgcolor=#ffffff >
	    <td class='input_box_title'> 배너설명  </td>
	    <td class='input_box_item'><input type=text class='textbox' name='banner_desc' value='".$banner_desc."' style='width:530px;'> <span class=small></span></td>
	  </tr>
	  <!--tr bgcolor=#ffffff >
	    <td class='input_box_title'> 사용유무건 </td>
	    <td class='input_box_item'>
	    	<input type=radio name='disp' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' value='0' ><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr-->
	  </table>";



//if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
//}

$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='group_frm' action='banner.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data'>
<input name='act' type='hidden' value='$act'>
<input name='rsl_banner_ix' type='hidden' value='$rsl_banner_ix'>
<input name='SubID' type='hidden' value='$SubID'>";

$Contents = $Contents."<tr><td>";
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

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >배너를 등록하신후 치환함수를 이용해 디자인에 적용하실 수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >페이지를 선택하시면 추후 배너관리시 편리합니다.</td></tr>
</table>
";
	//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');


$Contents .= HelpBox("배너관리", $help_text,70);



$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = reseller_menu();
$P->Navigation = "리셀러관리 > 리셀러설정 > 배너관리";
$P->title = "배너관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();
/*
function getFirstDIV($selected=""){
	$mdb = new Database;

	$sql = 	"SELECT *
			FROM shop_banner_div
			where disp=1 ORDER BY div_ix ASC ";

	$mdb->query($sql);

	$mstring = "<select name='banner_page' id='banner_page' >";
	$mstring .= "<option value=''>베너 분류</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[div_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[div_ix]."' selected>".$mdb->dt[div_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[div_ix]."'>".$mdb->dt[div_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}
*/


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