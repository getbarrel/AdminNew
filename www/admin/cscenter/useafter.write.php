<?
include("../class/layout.class");
include("../webedit/webedit.lib.php");
include("../logstory/class/sharedmemory.class");
$shmop = new Shared("use_after");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$datas = $shmop->getObjectForKey("use_after");
$datas = unserialize(urldecode($datas));

$db = new Database;

$db->query("SELECT * FROM shop_product_after WHERE bbs_ix='$bbs_ix'");

if($db->total){
	$db->fetch();
	$act = "update";
	$bbs_name = $db->dt[bbs_name];
	$mem_ix = $db->dt[mem_ix];
	$bbs_id = $db->dt[bbs_id];
	$add_script = "
		$(document).ready(function(){
			$('form[name=writeform] :input').prop('disabled', true);
			$('input[type=hidden]').prop('disabled', false);
			$('input[type=button]').prop('disabled', false);
			$('input[name=is_best]').prop('disabled', false);
			$('input[name=bbs_hidden]').prop('disabled', false);
		});
	";
}else{
	$act = "insert";
	$bbs_name = $admininfo[charger];
	$mem_ix = $admininfo[charger_ix];
	$bbs_id = $admininfo[charger_id];
}

$Contents = "
	<table cellpadding=0 cellspacing=0 border=0 width=100%>
	<tr>
		<td >
			".GetTitleNavigation("상품 후기 작성", "고객센타 > 상품 후기 작성")."
		</td>
	</tr>
	<tr height=10>
		<td rowspan=6 valign=top>
			<form name='writeform' action='./useafter.act.php' method='post' onsubmit=\"return CheckFormValue(this);\" enctype='multipart/form-data'>
			<input type=hidden name=act value='".$act."'>
			<input name='bbs_ix' type='hidden' value='".$bbs_ix."'>
			<input name='mem_ix' type='hidden' value='".$mem_ix."'>
			<input name='mmode' type='hidden' value='".$mmode."'>
			<table border='0' cellspacing='0' cellpadding='0' width='100%' >
			<tr>
				<td bgcolor='#F8F9FA'>
					<table cellpadding=3 cellspacing=0 border=0 width='100%' class='input_table_box'>
						<col width=15%>
						<col width=35%>
						<col width=15%>
						<col width=35%>";

						if($act == "update"){
							$Contents .="
							<tr bgcolor=#ffffff height=30>
								<td class='input_box_title'> <b>작성자</b></td>
								<td class='input_box_item' colspan='3'>".$bbs_name."(".$bbs_id.")</td>
							</tr>";
						}else{
							$Contents .="
							<tr bgcolor=#ffffff height=30>
								<td class='input_box_title'> <b>작성자</b></td>
								<td class='input_box_item'><input type='text' class='textbox' name='bbs_name' value='".$bbs_name."'></td>
								<td class='input_box_title'> <b>작성자 아이디</b></td>
								<td class='input_box_item'><input type='text' idtype='true' class='textbox' name='bbs_id' value='".$bbs_id."' title='작성자 아이디'></td>
							</tr>";
						}

$Contents .="
                        <!--
						<tr bgcolor=#ffffff height=30>
							<td class='input_box_title'> <b>제목</b></td>
							<td class='input_box_item' colspan='3'>
								<input type=text class='textbox' name='bbs_subject' validation='true' title='제목' style='height:18px; width:80%;' value='".$db->dt[bbs_subject]."'>
							</td>
						</tr>
						-->
						<input type=hidden class='textbox' name='bbs_subject' validation='true' title='제목' style='height:18px; width:80%;' value=''>
						<tr bgcolor=#ffffff height=30>
							<td class='input_box_title'> <b>분류</b></td>
							<td class='input_box_item'>
								<select name='bbs_div' validation='true' title='분류'>
									<option value=''>전체</option>
									<option value='1' ".($db->dt[bbs_div] == "1" ? "selected" : "").">프리미엄</option>
									<option value='2' ".($db->dt[bbs_div] == "2" ? "selected" : "").">일반</option>
								</select>
							</td>
							<td class='input_box_title'> <b>베스트 후기</b></td>
							<td class='input_box_item'>
								<input type='radio' name='is_best' value='Y' id='best_1' ".($db->dt[is_best] == "Y" ? "checked" : "")."><label for='best_1'>지정</label>
								<input type='radio' name='is_best' value='N' id='best_0' ".($db->dt[is_best] == "N" || $db->dt[is_best] == "" ? "checked" : "")."><label for='best_0'>미지정</label>
							</td>
						</tr>
						<tr bgcolor=#ffffff height=30>
							<td class='input_box_title'> <b>상품명</b></td>
							<td class='input_box_item' colspan='3'>
								<input type=text class='textbox' id='fake_name' style='height:18px; width:40%;' validation='true' title='상품명' value='".$db->dt[pname]."' readonly>
								<input type=hidden name='pname' value='".$db->dt[pname]."'>
								<input type=hidden name='pid' value='".$db->dt[pid]."'>
								<input type=hidden name='option_name' value='".$db->dt[option_name]."'>
								<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"javascript:PoPWindow3('useafter.prd.php',900,800,'useafter.prd')\" style='cursor:pointer;'>
							</td>
						</tr>
						<tr bgcolor=#ffffff height=30 ".($datas[use_valuation_goods] == "N" ? "style='display:none;'" : "").">
							<td class='input_box_title'> <b>상품평가</b></td>
							<td class='input_box_item' colspan='3'>
								<input type='radio' name='valuation_goods' value='0' id='goods_0' ".($db->dt[valuation_goods] == "0" || $db->dt[valuation_goods] == "" ? "checked" : "")."><label for='goods_0'>".str_repeat("<img src='/admin/images/icon_score_02.gif'>",5)."</label>

								<input type='radio' name='valuation_goods' value='1' id='goods_1' ".($db->dt[valuation_goods] == "1" ? "checked" : "")."><label for='goods_1'>".str_repeat("<img src='/admin/images/icon_score_01.gif'>",1)."".str_repeat("<img src='/admin/images/icon_score_02.gif'>",4)."</label>

								<input type='radio' name='valuation_goods' value='2' id='goods_2' ".($db->dt[valuation_goods] == "2" ? "checked" : "")."><label for='goods_2'>".str_repeat("<img src='/admin/images/icon_score_01.gif'>",2)."".str_repeat("<img src='/admin/images/icon_score_02.gif'>",3)."</label>

								<input type='radio' name='valuation_goods' value='3' id='goods_3' ".($db->dt[valuation_goods] == "3" ? "checked" : "")."><label for='goods_3'>".str_repeat("<img src='/admin/images/icon_score_01.gif'>",3)."".str_repeat("<img src='/admin/images/icon_score_02.gif'>",2)."</label>

								<input type='radio' name='valuation_goods' value='4' id='goods_4' ".($db->dt[valuation_goods] == "4" ? "checked" : "")."><label for='goods_4'>".str_repeat("<img src='/admin/images/icon_score_01.gif'>",4)."".str_repeat("<img src='/admin/images/icon_score_02.gif'>",1)."</label>

								<input type='radio' name='valuation_goods' value='5' id='goods_5' ".($db->dt[valuation_goods] == "5" ? "checked" : "")."><label for='goods_5'>".str_repeat("<img src='/admin/images/icon_score_01.gif'>",5)."</label>
							</td>
						</tr>
						<tr bgcolor=#ffffff height=30 ".($datas[use_valuation_delivery] == "N" ? "style='display:none;'" : "").">
							<td class='input_box_title'> <b>배송평가</b></td>
							<td class='input_box_item' colspan='3'>
								<input type='radio' name='valuation_delivery' value='0' id='delivery_0' ".($db->dt[valuation_delivery] == "0" || $db->dt[valuation_delivery] == "" ? "checked" : "")."><label for='delivery_0'>".str_repeat("<img src='/admin/images/icon_score_02.gif'>",5)."</label>

								<input type='radio' name='valuation_delivery' value='1' id='delivery_1' ".($db->dt[valuation_delivery] == "1" ? "checked" : "")."><label for='delivery_1'>".str_repeat("<img src='/admin/images/icon_score_01.gif'>",1)."".str_repeat("<img src='/admin/images/icon_score_02.gif'>",4)."</label>

								<input type='radio' name='valuation_delivery' value='2' id='delivery_2' ".($db->dt[valuation_delivery] == "2" ? "checked" : "")."><label for='delivery_2'>".str_repeat("<img src='/admin/images/icon_score_01.gif'>",2)."".str_repeat("<img src='/admin/images/icon_score_02.gif'>",3)."</label>

								<input type='radio' name='valuation_delivery' value='3' id='delivery_3' ".($db->dt[valuation_delivery] == "3" ? "checked" : "")."><label for='delivery_3'>".str_repeat("<img src='/admin/images/icon_score_01.gif'>",3)."".str_repeat("<img src='/admin/images/icon_score_02.gif'>",2)."</label>

								<input type='radio' name='valuation_delivery' value='4' id='delivery_4' ".($db->dt[valuation_delivery] == "4" ? "checked" : "")."><label for='delivery_4'>".str_repeat("<img src='/admin/images/icon_score_01.gif'>",4)."".str_repeat("<img src='/admin/images/icon_score_02.gif'>",1)."</label>

								<input type='radio' name='valuation_delivery' value='5' id='delivery_5' ".($db->dt[valuation_delivery] == "5" ? "checked" : "")."><label for='delivery_5'>".str_repeat("<img src='/admin/images/icon_score_01.gif'>",5)."</label>
							</td>
						</tr>
						<tr bgcolor=#ffffff height=30>
							<td class='input_box_title'> <b>공개설정</b></td>
							<td class='input_box_item' colspan='3'>
								<input type='radio' name='bbs_hidden' value='0' id='bbs_hidden0' ".($db->dt[bbs_hidden] == "0" || $db->dt[bbs_hidden] == "" ? "checked" : "")."><label for='bbs_hidden0'>공개</label>
								<input type='radio' name='bbs_hidden' value='1' id='bbs_hidden1' ".($db->dt[bbs_hidden] == "1" ? "checked" : "")."><label for='bbs_hidden1'>비공개</label>
							</td>
						</tr>
						<tr bgcolor=#ffffff height=30>
							<td class='input_box_title'> <b>첨부파일 <img src='../images/".$_SESSION["admininfo"]["language"]."/btn_add.gif' align='absmiddle' onclick='showImgAad();'></b></td>
							<td class='input_box_item' colspan='3'>";

						if($act == "update"){
							if(! empty($db->dt[bbs_file_1])){
								$Contents .= "<a href='./useafter.detail.php?act=down&bbs_ix=".$db->dt[bbs_ix]."&bbs_file=".$db->dt[bbs_file_1]."'>".$db->dt[bbs_file_1]."</a></br>";
							}
							if(! empty($db->dt[bbs_file_2])){
								$Contents .= "<a href='./useafter.detail.php?act=down&bbs_ix=".$db->dt[bbs_ix]."&bbs_file=".$db->dt[bbs_file_2]."'>".$db->dt[bbs_file_2]."</a></br>";
							}
							if(! empty($db->dt[bbs_file_3])){
								$Contents .= "<a href='./useafter.detail.php?act=down&bbs_ix=".$db->dt[bbs_ix]."&bbs_file=".$db->dt[bbs_file_3]."'>".$db->dt[bbs_file_3]."</a></br>";
							}
							if(! empty($db->dt[bbs_file_4])){
								$Contents .= "<a href='./useafter.detail.php?act=down&bbs_ix=".$db->dt[bbs_ix]."&bbs_file=".$db->dt[bbs_file_4]."'>".$db->dt[bbs_file_4]."</a></br>";
							}
							if(! empty($db->dt[bbs_file_5])){
								$Contents .= "<a href='./useafter.detail.php?act=down&bbs_ix=".$db->dt[bbs_ix]."&bbs_file=".$db->dt[bbs_file_5]."'>".$db->dt[bbs_file_5]."</a></br>";
							}
						}else{
							$Contents .= "
								<p class='addimg'><input type='file' id='bbs_file_1' name='bbs_file_1' style='padding-bottom: 4px;padding-top: 10px;' title='대표 파일'></p>
								<p style='display:none;' class='addimg'><input type='file' id='bbs_file_2' name='bbs_file_2' style='padding-bottom: 4px;'></p>
								<p style='display:none;' class='addimg'><input type='file' id='bbs_file_3' name='bbs_file_3' style='padding-bottom: 4px;'></p>
								<p style='display:none;' class='addimg'><input type='file' id='bbs_file_4' name='bbs_file_4' style='padding-bottom: 4px;'></p>
								<p style='display:none;' class='addimg'><input type='file' id='bbs_file_5' name='bbs_file_5' style='padding-bottom: 10px;'></p>";
						}

$Contents .= "
							</td>
						</tr>
						<tr bgcolor=#ffffff height=30>
							<td class='input_box_title'> <b>내용</b></td>
							<td class='input_box_item' colspan='3'>
								<textarea name='bbs_contents' title='내용' validation='true' style='width: 80%; height: 300px; margin: 10px; margin-left: 1px;'>".$db->dt[bbs_contents]."</textarea>
							</td>
						</tr>
					</table>
					<table width='100%' cellpadding=0 cellspacing=0 border=0>
					<tr >
						<td align=right nowrap style='padding:10px 0px'>
							<table cellpadding=0 cellspacing=0>
							<tr>
								<td style='padding-right:5px;'>
									<img src='../images/".$admininfo["language"]."/b_del.gif' border=0 id=delete style='cursor:pointer;display:none' onclick=\"origin_del(document.originform);\">
								</td>
								<td>
									<img src='../images/".$admininfo["language"]."/bt_modify.gif' border=0 id=modify style='cursor:pointer;display:none' onclick=\"OriginSubmit(document.originform,'update')\">
								</td>
							</tr>
							</table>
						</td>
						<td align=right style='padding:10px 0px'>
							<input type='button' value='저장' onclick='submitForm();'>
							<input type='button' value='취소' onclick='cancelForm();'>
						</td>
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

$Script = "
<style type='text/css'></style>

<script language='javascript'>

".$add_script."

function submitForm(){
	$('form[name=writeform]').submit();
}

function cancelForm(){
	if(confirm('현재 입력중인 상품후기가 저장되지 않았습니다. 그래도 취소하시겠습니까?')){
		top.document.location.href='./useafter.list.php';
	}
}

function showImgAad(){
	var seq = $('.addimg:visible').length;
	$('.addimg').eq(seq).show();
	
	if(seq == 5){
		alert('업로드 가능한 이미지는 최대 5개입니다');
	}
}

</script>
";

$Script = "<script language='JavaScript' src='origin.js'></script>\n<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>".$Script;

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->Navigation = "고객센타 > 상품 후기 작성";
	$P->NaviTitle = "상품 후기 작성";
	$P->title = "상품 후기 작성";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->OnloadFunction = "";
	$P->Navigation = "고객센타 > 상품 후기 작성";
	$P->title = "상품 후기 작성";
	$P->strLeftMenu = cscenter_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
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