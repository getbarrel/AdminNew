<?php
include("../class/layout.class");

$db = new Database;

$sql = "SELECT * FROM dewytree_video where dv_ix='" . $dv_ix . "'";
$db->query($sql);
if ($db->total) {
    $db->fetch();
    $dv_ix = $db->dt['dv_ix'];
    $title = $db->dt['title'];
    $video_url = $db->dt['video_url'];
    $disp = $db->dt['disp'];
    $act = "update";
} else {
    $act = "insert";
}

$Script = "
	<Script Language='JavaScript'>

	function SubmitX(frm){
		for(i=0;i < frm.elements.length;i++){
			if(!CheckForm(frm.elements[i])){
				return false;
			}
		}
	}

	</Script>";


$Contents = "
	<table width='100%' border='0' align='left' cellspacing='0' cellpadding='0'>
	 <tr>
		<td align='left' colspan=6 > " . GetTitleNavigation("카드별프로모션등록", "전시관리 > 카드별프로모션등록 ") . "</td>
	</tr>
	  <tr>
		<td>
			<form name='main_frm' method='post' onSubmit='return SubmitX(this)' action='dewytree_video.act.php' style='display:inline;' enctype='multipart/form-data' target='act'>
			<input type='hidden' name='act' value='" . $act . "'>
			<input type='hidden' name='dv_ix' value='" . $dv_ix . "'>
			<table border='0' width='100%' cellspacing='1' cellpadding='0'>
			  <tr>
				<td style='padding:0px 0px 20px 0px'>
				<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
					<tr>
						<th class='box_01'></th>
						<td class='box_02'></td>
						<th class='box_03'></th>
					</tr>
					<tr>
						<th class='box_04'></th>
						<td class='box_05'  valign=top style='padding:0px'>
						<table border='0' cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
						  <col width='20%'>
						  <col width='30%'>
						  <col width='20%'>
						  <col width='30%'>
						  <tr height=27>
								<td class='input_box_title'> <b>프로모션명</b> <img src='" . $required3_path . "'></td>
								<td class='input_box_item' style='padding:5px 10px;' colspan=3>
									<input type='text' name='title' value='" . $title . "' class='textbox' style='width:350px;' align='absmiddle' title='제목' validation=true>
								</td>
							</tr>
						<tr height=27>
							  <td class='search_box_title' > <b>유튜브 동영상 URL</b> <img src='" . $required3_path . "'></td>
							  <td class='search_box_item'  colspan=3>
								    <input type='text' name='video_url' value='" . $video_url . "' class='textbox' style='width:550px;' align='absmiddle' title='유튜브 동영상 URL' validation=true>
							  </td>
							</tr>
							<tr height=27>
								<td class='search_box_title'> <b>사용여부</b></td>
								<td class='search_box_item'  colspan=3>
									<input type='radio' name='disp' id='disp_1' value='1' " . CompareReturnValue("", $disp, "checked") . " " . CompareReturnValue("1", $disp, "checked") . " validation=true title='사용여부'> <label for='disp_1' >사용</label>
									<input type='radio' name='disp' id='disp_0' value='0' " . CompareReturnValue("0", $disp, "checked") . " validation=true title='사용여부'><label for='disp_0' >사용하지 않음</label>
								</td>
							</tr>
						</table>
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
				<td bgcolor='#ffffff'>
				  <table border='0' cellspacing='0' cellpadding='0' width='100%'>
					<tr>
					  <td >
						<table border='0' cellpadding=0 cellspacing=0 width='100%'>
						<tr><td colspan=3 align=right style='padding:10px;'>";
if (checkMenuAuth(md5($_SERVER["PHP_SELF"]), "U")) {
    $Contents .= "<table>
										<tr>
											<td><input type=image src='../images/" . $admininfo["language"] . "/b_save.gif' align=absmiddle border=0></td>
										</tr>
									</table>";
}
$Contents .= "
					</td></tr>
					  </table>

					  </td>
					</tr>
				  </table>
				</td>
			  </tr>
			</table>
			</form>
		</td>
	  </tr>
  </table>";

$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "프로모션/전시 > 프로모션 전시관리 > 듀이트리 영상 등록";
$P->title = "듀이트리 영상 등록";
$P->OnloadFunction = "";//showSubMenuLayer('storeleft');
$P->strLeftMenu = display_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();