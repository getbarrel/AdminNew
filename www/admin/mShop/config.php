<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");
if($admininfo[admin_level] == ""){
    header("Location:/admin/");
}
if($admininfo[admin_level] < 9){
    header("Location:/admin/seller/");
}

$shmop = new Shared("mobile_config");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$config = $shmop->getObjectForKey("mobile_config");
$config = unserialize(urldecode($config));

$Contents01 = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
<col width='27%' />
<col width='*' />
	<tr>
		<td align='left' colspan='2''> ".GetTitleNavigation("모바일 환경설정", "모바일샵 > 모바일 환경설정")."</td>
	</tr>
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>APP 환경설정</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;margin-top:5px;' class='input_table_box'>
	<col width='25%' />
	<col width='*' />
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>안드로이드 APP 버전</b></td>
		<td class='input_box_item'><input type='text' name='android_app_version' value='".$config['android_app_version']."' size='8' /></td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>안드로이드 APP 필수 업데이트 여부</b></td>
		<td class='input_box_item'>
            <label><input type='radio' name='android_app_version_necessary_update' value='N' ".($config['android_app_version_necessary_update'] == 'N' ? "checked" : "")."/>미적용</label>
            <label><input type='radio' name='android_app_version_necessary_update' value='Y' ".($config['android_app_version_necessary_update'] == 'Y' ? "checked" : "")."/>적용</label>
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>IOS APP 버전</b></td>
		<td class='input_box_item'><input type='text' name='ios_app_version' value='".$config['ios_app_version']."' size='8' /></td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>IOS APP 필수 업데이트 여부</b></td>
		<td class='input_box_item'>
            <label><input type='radio' name='ios_app_version_necessary_update' value='N' ".($config['ios_app_version_necessary_update'] == 'N' ? "checked" : "")."/>미적용</label>
            <label><input type='radio' name='ios_app_version_necessary_update' value='Y' ".($config['ios_app_version_necessary_update'] == 'Y' ? "checked" : "")."/>적용</label>
		</td>
	</tr>
</table>";

$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff height=70><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";

$Contents = "<form name='edit_form' action='config.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' target='act'>";
$Contents = $Contents."<table width='100%'  border=0>";
$Contents = $Contents."<tr><td>".$Contents01."<br></td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</table >";
$Contents = $Contents."</form>";

/*
$help_text = "
<table cellpadding=2 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td ><img src='/admin/image/icon_list.gif' ></td><td class='small' >알림설정을 사용시 <input type='checkbox' checked onclick=\"$(this).attr('checked',true)\" /> 에 체크를 해주셔야 합니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >시간 계산은 1일은 24시간으로 상태변경시간 기준으로 체크합니다.</td></tr>
</table>
";
*/

$Contents .=  HelpBox("모바일 환경설정", $help_text);

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = mShop_menu();
$P->Navigation = "모바일샵 > 모바일 환경설정";
$P->title = "모바일 환경설정";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>