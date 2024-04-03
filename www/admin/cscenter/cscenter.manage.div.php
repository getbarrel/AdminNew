<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");

$db = new Database;

if(empty($page_type)){
	$page_type = 1;
}

if($page_type == 1){
	$page_type_str = "divSetting_premium";
}else if($page_type == 2){
	$page_type_str = "divSetting_review";
}

$shmop = new Shared($page_type_str);
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$datas = $shmop->getObjectForKey($page_type_str);
$datas = unserialize(urldecode($datas));

$Script = "
<style>
.width_class {width:150px;}
input {border:1px solid #c6c6c6;padding:3px;}
.member_table td {text-align:left;}
</style>

<script>

</script>
";


if(empty($datas[use_limit])){
	$datas[use_limit] = "N";
}

if(empty($datas[use_image])){
	$datas[use_image] = "N";
}

if(empty($datas[use_div])){
	$datas[use_div] = "N";
}

$Contents = "
<form name=bbs_manage_frm action='cscenter.manage.act.php' method='post' onsubmit='return CheckFormValue(this)'>
<input type=hidden name=act value='div_setting'>
<input type=hidden name=page_type value='$page_type_str'>
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2 valign=top>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("분류 설정", "분류 설정", false)."</td>
			</tr>
			<tr>
				<td>
					<table class='s_org_tab' cellpadding='0' cellspacing='0' border='0'>
					<tr>
						<td class='tab'>
							<div class='tab' style='width:100%;height:38px;margin:0px;'>
								<table id='tab_01' ".($page_type == "1" ? "class='on'" : "").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' style='padding-left:20px;padding-right:20px;'>
										<a href='?page_type=1'>일반 후기</a>
									</td>
									<th class='box_03'></th>
								</tr>
								</table>
								<table id='tab_02' ".($page_type == "2" ? "class='on'" : "").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' style='padding-left:20px;padding-right:20px;'>
										<a href='?page_type=2'>프리미엄 후기</a>
									</td>
									<th class='box_03'></th>
								</tr>
								</table>
							</div>
						</td>
						<td class='btn'>
						</td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align=center> <!-- style='padding: 0 10px 0 10px;height:569px;vertical-align:top' -->
				<table border='0' cellspacing='1' cellpadding='5' width='100%'>
				<tr>
				  <td bgcolor='#F8F9FA'>
					<table border='0' width='100%' cellspacing='1' cellpadding='0'>
						<tr>
							<td >
							<table border='0' width='100%' cellspacing='0' cellpadding='0' class='member_table input_table_box' style='text-align:left;'>
									<col width=15%>
									<col width=35%>
									<col width=15%>
									<col width=35%>
								<tr>
									<td class='input_box_title' nowrap> 분류명 <img src='".$required3_path."'> </td>
									<td class='input_box_item' colspan='3'><input type=text class='textbox' style='width: 60%' name='div_name' value='".$datas[div_name]."'></td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 내용 입력 제한 <img src='".$required3_path."'> </td>
									<td class='input_box_item' colspan='3'>
										<input type=radio name='use_limit' value='Y' id='use_limit_y' ".CompareReturnValue("Y",$datas[use_limit],"checked")." >
										<label for='use_limit_y'>제한 있음</label>
										<input type=radio name='use_limit' value='N' id='use_limit_n' ".CompareReturnValue("N",$datas[use_limit],"checked")." >
										<label for='use_limit_n'>제한 없음</label>
									</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 내용 입력 조건 <img src='".$required3_path."'> </td>
									<td class='input_box_item' colspan='3'><input type=text class='textbox' style='width: 40px;' name='limit_cnt_s' value='".$datas[limit_cnt_s]."'> 자 이내, <input type=text class='textbox' style='width: 40px;' name='limit_cnt_e' value='".$datas[limit_cnt_e]."'> 자 이상</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 이미지 첨부 사용 여부 <img src='".$required3_path."'> </td>
									<td class='input_box_item'>
										<input type=radio name='use_image' value='Y' id='use_image_y' ".CompareReturnValue("Y",$datas[use_image],"checked")." >
										<label for='use_image_y'>사용</label>
										<input type=radio name='use_image' value='N' id='use_image_n' ".CompareReturnValue("N",$datas[use_image],"checked")." >
										<label for='use_image_n'>사용 안함</label>
									</td>
									<td class='input_box_title' > 이미지 용량 제한 </td>
									<td class='input_box_item'>
										한 파일당 <input type=text name='image_size' class='textbox' value='".$datas[image_size]."'> MB
									</td>
								</tr>
							</table>
						</td>
					</tr>
					</table>
				  </td>
				</tr>
				</tr>
				</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</TABLE>";

$Contents .="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed' >
		<col width='18%'>
		<col width='32%'>
		<col width='18%'>
		<col width='32%'>
		<tr bgcolor=#ffffff >
            <td colspan=4 align=right style='padding:10px 0px;'>
				<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' >
                <img src='../images/".$admininfo["language"]."/b_cancel.gif' border=0 style='cursor:hand;border:0px;' align=absmiddle onclick='history.back();'>
            </td>
        </tr>
		</form>";
$Contents .="</table>";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "분류 설정";
$P->NaviTitle = "분류 설정";
$P->title = "분류 설정";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>
