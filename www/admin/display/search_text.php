<?
include("../class/layout.class");

$db = new Database;

$db->query("SELECT * FROM shop_search_text where st_ix= '$st_ix'");

if($db->total){
	$db->fetch();
	$act = "update";
	$st_ix = $db->dt[st_ix];
	
	$st_text = $db->dt[st_text];
	$st_type = $db->dt[st_type];
	$st_title = $db->dt[st_title];
	$st_url = $db->dt[st_url];
	$disp = $db->dt[disp];

	$st_sdate = date("Y-m-d",$db->dt[st_sdate]);
	$st_sdate_stime = date("H",$db->dt[st_sdate]);
	$st_sdate_smin = date("i",$db->dt[st_sdate]);

	$st_edate = date("Y-m-d",$db->dt[st_edate]);
	$st_edate_etime = date("H",$db->dt[st_edate]);
	$st_edate_emin = date("i",$db->dt[st_edate]);

}else{

	$act = "insert";
	$st_sdate = "";
	$st_edate = "";
	$disp = "1";
	$st_type = "1";

}


$Script = "
<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n
<script type='text/javascript' src='/js/ui/jquery-ui-1.8.9.custom.js'></script>
<script type='text/javascript' src='/js/ui/jquery.ui.core.js'></script>
<script type='text/javascript' src='/js/ui/jquery.ui.widget.js'></script>
<script type='text/javascript' src='/js/ui/jquery.ui.mouse.js'></script>
<script type='text/javascript' src='/admin/js/ms_productSearch.js'></script>
<script type='text/javascript' src='relationAjaxForEvent.js'></script>
<Script Language='JavaScript'>

function SubmitX(frm){

	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}

	return true;
}

function change_st_type(){

	var st_type_val = $('input[name=st_type]:checked').val();

	if(st_type_val==1){ //텍스트
		$('#title_tr_text').show();
		$('#title_tr_img').hide();

		$('[name=st_title]').attr('validation','true');
		$('[name=st_img]').attr('validation','false');
		$('[name=st_url]').attr('validation','false');
	}else{ //이미지
		$('#title_tr_text').hide();
		$('#title_tr_img').show();
		
		if($('[name=act]').val()=='update'){
			$('[name=st_img]').attr('validation','false');
		}else{
			$('[name=st_img]').attr('validation','true');
		}

		$('[name=st_title]').attr('validation','false');
		
		$('[name=st_url]').attr('validation','true');
	}

}



$(document).ready(function () {

	change_st_type();

	$(\"#start_datepicker\").datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){
			if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
				$('#end_datepicker').val(dateText);
			}
		}
	});

	$(\"#end_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력'
	});

});


</Script>";


$Contents = "
<table width='100%' border='0' align='left'>
 <tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("검색어 등록", "검색어관리 < 검색어 등록")."</td>
</tr>
  <tr>
    <td>
        <form name='st_frm' method='post' onSubmit=\"return SubmitX(this)\" action='search_text.act.php' style='display:inline;' enctype='multipart/form-data' target='act'>
		<input type='hidden' name=act value='$act'>
		<input type='hidden' name=st_ix value='$st_ix'>
        <table border='0' width='100%' cellspacing='1' cellpadding='0'>
          <tr>
            <td bgcolor='#6783A8'>
              <table border='0' cellspacing='0' cellpadding='0' width='100%'>
                <tr>
                  <td >
                    <table border='0' cellpadding=3 cellspacing=0 width='100%' class='input_table_box'>
						<col width='20%'>
						<col width='30%'>
						<col width='20%'>
						<col width='30%'>
						<tr height=27>
                      	<td class='input_box_title'> <b>노출 타입<img src='".$required3_path."'></b></td>
                        <td class='input_box_item'>
							<input type='radio' name='st_type' id='st_type_1' value='1' ".CompareReturnValue("1",$st_type,"checked")." onclick=\"change_st_type()\"> <label for='st_type_1' >텍스트</label>
							<input type='radio' name='st_type' id='st_type_2' value='2' ".CompareReturnValue("2",$st_type,"checked")." onclick=\"change_st_type()\"><label for='st_type_2' >이미지</label>
						</td>
						<td class='input_box_title'> <b>사용유무 <img src='".$required3_path."'></b></td>
                        <td class='input_box_item' >
							<input type='hidden' name='pop' value='1'> <input type='radio' name='disp' id='disp_1' value='1' ".CompareReturnValue("1",$disp,"checked")."> <label for='disp_1' >사용</label>
							<input type='radio' name='disp' id='disp_0' value='0' ".CompareReturnValue("0",$disp,"checked")."><label for='disp_0' >미사용</label>
						</td>
						</tr>
                      <tr height=28>
                        <td class='input_box_title' ><b>텍스트 <img src='".$required3_path."'></b></td>
                        <td class='input_box_item' colspan=3>
							<input type='text' name='st_text' class='textbox' value='".$st_text."' maxlength='80' style='width:98%' validation='true' title='텍스트'>
						</td>
                      </tr>
                      <tr height=27 >
						  <td class='input_box_title'> <b>노출기간 <img src='".$required3_path."'></b></td>
						  <td class='input_box_item' colspan=3>
							<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff >
								<tr>
									<TD width=80px nowrap>
									<input type='text' name='st_sdate' class='textbox' value='".$st_sdate."' style='height:18px;width:70px;text-align:center;' id='start_datepicker'>
									</td>
									<td align=center width=20px  style='text-align:center;'> 일</td>
									<td nowrap>
									<SELECT name='st_sdate_stime'>";

									for($i=0;$i < 24;$i++){
				$Contents .= "<option value='".$i."' ".($st_sdate_stime == $i ? "selected":"").">".$i."</option>";
									}
				$Contents .= "
									</SELECT> 시
									<SELECT name='st_sdate_smin'>";
									for($i=0;$i < 60;$i++){
				$Contents .= "<option value='".$i."' ".($st_sdate_smin == $i ? "selected":"").">".$i."</option>";
									}
				$Contents .= "
									</SELECT> 분
									</TD>
									<TD width=30px  align=center> ~ </TD>
									<TD width=80px nowrap>
									<input type='text' name='st_edate' class='textbox' value='".$st_edate."' style='height:18px;width:70px;text-align:center;' id='end_datepicker'>
									</td>
									<td align=center width=20px > 일</td>
									<td nowrap>
									<SELECT name='st_edate_etime'>";

									for($i=0;$i < 24;$i++){
				$Contents .= "<option value='".$i."' ".($st_edate_etime == $i ? "selected":"").">".$i."</option>";
									}
				$Contents .= "
									</SELECT> 시
									<SELECT name='st_edate_emin'>";
									for($i=0;$i < 60;$i++){
				$Contents .= "<option value='".$i."' ".($st_edate_emin == $i ? "selected":"").">".$i."</option>";
									}
				$Contents .= "
									</SELECT> 분
									</TD>
								</tr>
							</table>
						  </td>
						</tr>

						<tr height=27 id='title_tr_text'>
							<td class='input_box_title'>타이틀</td>
							<td class='input_box_item' colspan=3 style='padding:5px;'>
								<input type='text' name='st_title' class='textbox' value='".$st_title."' maxlength='30' style='width:50%' validation='true' title='타이틀'>
							</td>
						</tr>

						<tr height=27 id='title_tr_img'>
							<td class='input_box_title'> 타이틀</td>
							<td class='input_box_item' colspan=3 style='padding:5px;'>
							<table cellpadding=0 cellspacing=0 width='100%'>
								<tr height=27>
									<td width='50px'>이미지</td>
									<td width='10px'> : </td>
									<td width='*'><input type='file' class='textbox' name='st_img' validation='true' title='이미지'></td>
									<td align='left'>";

										if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/search/$st_ix/".$st_ix.".gif")){
											$Contents .= "<img src='".$admin_config[mall_data_root]."/images/search/$st_ix/".$st_ix.".gif' align=absmiddle >";
										}

										$Contents .= "
									</td>
								</tr>
								<tr height=27>
									<td>링 크</td>
									<td > : </td>
									<td>
										<input type='text' name='st_url' class='textbox' value='".$st_url."' maxlength='30' style='width:70%' validation='true' title='링크'>
										<br/>  ex> http://urlname.com/index.php <- http:// 포함된 풀URL 입력
									</td>
									<td></td>
								</tr>
							</table>
							</td>
						</tr>
                    </table>
                  </td>
                </tr>
				<tr>
					<td colspan=3 align=right style='padding:10px;' class='input_box_item'>
						<table width=100%  border=0>
							<col width= '*' >
							<col width= '100' >
							<col width= '100' >
							<col width= '100' >
							<tr>
								<td></td>
								<td><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle></td>
								<td><a href='search_text_list.php'><img src='../images/".$admininfo["language"]."/b_cancel.gif' border=0 align=absmiddle></a></td>
							 </tr>
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

  ";

	$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$help_text = HelpBox("<table cellpadding='0' cellspacing='0' border='0'><tr><td><b>검색어 등록</b></td></tr></table>", $help_text,160);

$Contents .= "
  <tr>
    <td align='left' style='padding-bottom:200px;'>
		".$help_text."
    </td>
  </tr>";

$Contents .= "
	</table>";

$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "프로모션/전시 > 검색어 등록";
$P->title = "검색어 등록";
$P->OnloadFunction = "";
$P->strLeftMenu = display_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>