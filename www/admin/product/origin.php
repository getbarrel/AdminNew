<?
include("../class/layout.class");
include("../webedit/webedit.lib.php");
include_once("origin.lib.php");
$db = new Database;

$db->query("SELECT * FROM common_origin WHERE og_ix='$og_ix'");

if($db->total){
	$db->fetch();
	$act = "update";
	$origin_info = $db->dt;
	$check_origin_code = 1;

	$db->query("SELECT parent_od_ix FROM common_origin_div WHERE od_ix='".$origin_info[od_ix]."'");
	$db->fetch();

	$parent_od_ix = $db->dt[parent_od_ix];

}else{

	$act = "insert";
	$origin_info[disp]  = 1;
	$check_origin_code = 0;

	if($_SESSION["admininfo"]["admin_level"] == 9){
		$origin_info[apply_status] = 1;
	}else{
		$origin_info[apply_status] = 2;
	}

}

$Contents = "
	<table cellpadding=0 cellspacing=0 border=0 width=100%>
	<tr>
		<td >
			".GetTitleNavigation("원산지 등록", "상품관리 > 원산지 등록")."
		</td>
	</tr>
	<tr>
	    <td align='left' colspan=4 style='padding-bottom:15px;'>
	    	  ".origin_tab("reg")."
	    </td>
	  </tr>
	<tr height=10>
		<td rowspan=6 valign=top>
			<form name='originform' action='./origin.act.php' method='post'  onsubmit=\"return OriginInput(this,'".$act."');\" enctype='multipart/form-data'  target='iframe_act'>
			<input type=hidden name=mode value='".$act."'>
			<input type=hidden name=og_ix value='".$og_ix."'>
			<input type='hidden' name='top_design' value=''>
			<input type='hidden' name='origin_name_division' value=''>
			<input type='hidden' name='mmode' value='$mmode'>";
$Contents .="
			<table width='100%' cellpadding=0 cellspacing=0>
			<tr height=30>
				<td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'><b class=blk> 원산지 등록/수정 하기 </b><span class=small><!--하단에 카테고리를 선택하신후 카테고리 등록하기 버튼을 클릭하세요.(다중 카테고리 등록지원)-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')." </span>
				</td>
				<td align=right style='padding-right:20px;' class=small><!--a href='JavaScript:SampleProductInsert()'>샘플데이타 넣기</a--></td></tr></table>")."</td>
			</tr>
			</table>";

$Contents .= "
			<table border='0' cellspacing='0' cellpadding='0' width='100%' >
			<tr>
				<td bgcolor='#F8F9FA'>
					<table cellpadding=3 cellspacing=0 border=0 width='100%' class='input_table_box'>
						<col width=15%>
						<col width=35%>
						<col width=15%>
						<col width=35%>
						<tr bgcolor=#ffffff height=30>
							<td class='input_box_title' onclick=\"OriginInput(document.originform,'".$act."');\"> <b>원산지명 <img src='".$required3_path."'></b></td>
							<td class='input_box_item'>
							<input type=text class='textbox' name='origin' validation='true' title='원산지명' style='height:18px; width:200px;' value=\"".str_replace("\"","\\\"",$origin_info[origin_name])."\">
							</td>
							<td class='input_box_title'> <b>원산지 코드 <img src='".$required3_path."'></b></td>
							<td class='input_box_item'>
							<input type='hidden' class='textbox' name='check_origin_code' id='check_origin_code' value='".$check_origin_code."' style='width:50px;'>
							<input type='text' class='textbox' name='origin_code' validation='true' title='원산지 코드' style='height:18px; width:80px;' onkeyup=\"checkOrginCode($(this),$('#check_desc'))\" value=\"".str_replace("\"","\\\"",$origin_info[origin_code])."\">
							<span id='check_desc' style='padding-left:10px;'>원산지 코드를 입력해주세요</span>
							</td>
						</tr>";

					if($admininfo[mall_type] == "B" || $admininfo[mall_type] == "O" || $admininfo[mall_type] == "E"){// 입점형
			$Contents .="<tr bgcolor=#ffffff height=30>											
							<td class='input_box_title'> <b>사용유무 <img src='".$required3_path."'></b></td>
							<td class='input_box_item' colspan='3'>";

		if($admininfo[admin_level] == 9){
		$Contents .= "		<input type=radio name=disp class=nonborder value=0 id='disp_0' validation=false title='사용유무' ".($origin_info[disp] == "0" ? "checked":"")."><label for='disp_0'>사용하지않음</label>
							<input type=radio name=disp class=nonborder value=1 id='disp_1' validation=false title='사용유무' ".($origin_info[disp] == "1" ? "checked":"")."><label for='disp_1'>사용</label>
							<!--input type=radio name=disp class=nonborder value=2 id='disp_2' validation=false title='사용유무' ".($origin_info[disp] == "2" ? "checked":"")."><label for='disp_2'>신청</label-->";
		}else if($admininfo[admin_level] == 8){
			if($origin_info[disp] == "0"){
				$Contents .= "사용하지 않음";
			}else if($origin_info[disp] == "1"){
				$Contents .= "사용";
			}else if($origin_info[disp] == "2"){
				$Contents .= "신청";
			}
				$Contents .= "<input type=hidden name=disp value='".$origin_info[disp]."'>";
		}
					}
			$Contents .= "
						<tr>
							<td class='input_box_title' style='font-size:12px;'>
							 <b>원산지 분류</b> <input type='button' name='search_origin_category' id='search_origin_category' value='검색' onclick=\"PoPWindow('./search_origin_category.php?group_code=',600,600,'add_origin_category')\" style='cursor:pointer;'>
							</td>
							<td class='input_box_item' colspan=3>
								<div id='selected_category_5' style='padding:10px 0px 10px 0px;'>
									<table width='100%' cellpadding='0' cellspacing='0' id='objDepartment'>
										<colgroup>
										<col width='*'>
										<col width='150'>
										</colgroup>
										<tbody>
										".$brand_category."
										</tbody>
									</table>
								</div>
							</td>
						</tr>
						<tr bgcolor=#ffffff height=60>
							<td class='input_box_title' nowrap> <b>원산지 간략설명 </b></td>
							<td class='input_box_item' colspan=3>
							<textarea type='text' name='shotinfo' class=textbox style='width:90%;height:40px;padding:3px;'  maxlength='80'>".$origin_info[shotinfo]."</textarea>
							</td>
						</tr>
					</table>
					<table width='100%' cellpadding=0 cellspacing=0 border=0>
					<tr >
						<td align=right nowrap style='padding:10px 0px'>
							<table cellpadding=0 cellspacing=0>
							<tr>
								<td style='padding-right:5px;'>";
								if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
									$Contents .= "<img src='../images/".$admininfo["language"]."/b_del.gif' border=0 id=delete style='cursor:pointer;display:none' onclick=\"origin_del(document.originform);\">
												<script>
												function origin_del(frm){
													var select = confirm(frm.origin.value + '을(를) 삭제하시겠습니까?');
													if(select){
														OriginSubmit(frm,'delete');
													}else{
														return false;
													}
												}
												</script>
												";
								}else{
									$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../image/b_del.gif' border=0 id=delete style='cursor:pointer;display:none' ></a>";
								}
								$Contents .= "
								</td>
								<td>";
								if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
									$Contents .= "<img src='../images/".$admininfo["language"]."/bt_modify.gif' border=0 id=modify style='cursor:pointer;display:none' onclick=\"OriginSubmit(document.originform,'update')\">";
								}else{
									$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bt_modify.gif' border=0 id=modify style='cursor:pointer;display:none' ></a>";
								}
								$Contents .= "
								</td>
							</tr>
							</table>
						</td>
						<td align=right style='padding:10px 0px'>";
						if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){//b_save.gif
							$Contents .= " <img src='../images/".$admininfo["language"]."/b_save.gif' id=ok border=0 align=absmiddle style='cursor:pointer' onclick=\"OriginSubmit(document.originform,'".$act."')\">";
						}else{
							$Contents .= "<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' id=ok border=0 align=absmiddle style='cursor:pointer' ></a>";
						}
		$Contents .= "</td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
			</form>
			<br>
		</td>
	</tr>
	</table>
	<iframe name='extand' id='extand' src='' width=0 height=0></iframe>";

	$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');
	$Contents .= HelpBox("원산지 등록", $help_text);

$Script = "
<script language='javascript'>

function deleteImage(imagetype, og_ix){
	if(confirm('해당이미지를 정말로 삭제하시겠습니까?')){		
		window.frames['act'].location.href = './origin.act.php?mode=image_delete&imagetype='+imagetype+'&og_ix='+og_ix;
	}
}

function loadOriginInfo(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;

	//var depth = sel.getAttribute('depth');
	//document.write('origin.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2');
	window.frames['act'].location.href = './origin.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';
}

function cd_del(dp_ix){
	$('#od_row_'+dp_ix).remove();
}


</script>
";


if($mmode == "pop"){
	$Script = "<script language='JavaScript' src='origin.js'></script>\n<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n".$Script;
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->Navigation = "상품관리 > 상품분류관리 > 원산지 등록";
	$P->NaviTitle = "원산지 등록";
	$P->strLeftMenu = product_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$Script = "<script language='JavaScript' src='origin.js'></script>\n<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>".$Script;
	$P = new LayOut();
	$P->addScript = $Script;
	$P->OnloadFunction = "";//"Init(document.originform);MM_preloadImages('../webedit/image/wtool1_1.gif','../webedit/image/wtool2_1.gif','../webedit/image/wtool3_1.gif','../webedit/image/wtool4_1.gif','../webedit/image/wtool5_1.gif','../webedit/image/wtool6_1.gif','../webedit/image/wtool7_1.gif','../webedit/image/wtool8_1.gif','../webedit/image/wtool9_1.gif','../webedit/image/wtool11_1.gif','../webedit/image/wtool13_1.gif','../webedit/image/wtool10_1.gif','../webedit/image/wtool12_1.gif','../webedit/image/wtool14_1.gif','../webedit/image/bt_html_1.gif','../webedit/image/bt_source_1.gif')"; //showSubMenuLayer('storeleft');
	$P->Navigation = "상품관리 > 상품분류관리 > 원산지 등록";
	$P->title = "원산지 등록";
	$P->strLeftMenu = product_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}


function getCategoryListForOrigin($category_text ="기본카테고리 선택", $object_name="cid",$id="cid", $onchange_handler="", $depth=0, $cid="")
{
	$mdb = new Database;
	$tb = (defined('TBL_SNS_CATEGORY_INFO'))	?	TBL_SNS_CATEGORY_INFO:TBL_SHOP_CATEGORY_INFO;
	if($depth == 0 || $cid != ""){
		$sql = "SELECT * FROM $tb where depth ='$depth' and cid LIKE '".substr($cid,0,($depth)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";
		//echo $sql;
		$mdb->query($sql);
	}

	if ($mdb->total){
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler id='$id' validation=false style='width:165px;font-size:12px;' title='카테고리'>\n";
		$mstring = $mstring."<option value=''>$category_text</option>\n";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			//if(substr_count($cid,substr($mdb->dt[cid],0,$mdb->dt[depth]+1))){
			if(substr($cid,0,($depth+1)*3) == substr($mdb->dt[cid],0,($depth+1)*3)){

				$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}else{
				$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}
		}
	}else{
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler id='$id' validation=false  style='width:140px;'>\n";
		$mstring = $mstring."<option value=''> $category_text</option>\n";
	}

	$mstring = $mstring."</Select>\n";

	return $mstring;
}



/*
create table common_origin (
	og_ix int(3) unsigned zerofill not null auto_increment,
	origin_name varchar(100) null default null,
	disp char(1) null default '0',
	primary key(og_ix)
);
*/
?>