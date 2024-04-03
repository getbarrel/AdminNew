<?
include("../class/layout.class");

//echo md5($_SERVER["PHP_SELF"]).":::".md5(str_replace("/promotion/","/display/",$_SERVER["PHP_SELF"]));


$max = 20; //페이지당 갯수

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$db = new Database;
$db_korea = new Database;
$db_english = new Database;
$db_indonesian = new Database;
//if($dic_ix){
	$sql = "SELECT * FROM admin_dic where dic_ix = '$dic_ix' ";

//}else{
//	$sql = "SELECT * FROM admin_dic where dic_type = '$dic_type' and menu_div = '$menu_div' and menu_code = '$menu_code' and dic_code = '$dic_code' and language_type = 'english' ";
//}
//echo $sql;
$db->query($sql);
$db->fetch();




if($db->total){
	if($trans_mode == "desc_trans"){
		$act = "insert";
		$dic_ix = "";
	}else{
		$act = "update";
		$dic_ix = $db->dt[dic_ix];
		$dic_type = $db->dt[dic_type];
		$dic_code = $db->dt[dic_code];
		$menu_div = $db->dt[menu_div];
		$menu_code = $db->dt[menu_code];
		$language_type = $db->dt[language_type];
		//echo $language_type;


	}
	if($dic_type != "WORD"){
		$dic_type_str = " and dic_code = '$dic_code' ";
	}else{
		$dic_type_str = " and dic_ix = '".$dic_ix."' ";
	}

	$sql = "SELECT * FROM admin_dic where dic_type = '$dic_type' and menu_div = '$menu_div' and menu_code = '$menu_code' and language_type = 'english' $dic_type_str ";

	//echo $sql."<br>";
	$db_english->query($sql);
	$db_english->fetch();

	/*
	$dic_ix = $db->dt[dic_ix];
	$dic_type = $db->dt[dic_type];
	$dic_code = $db->dt[dic_code];
	$menu_div = $db->dt[menu_div];
	$menu_code = $db->dt[menu_code];
	$language_type = $db->dt[language_type];
	*/
	/*
	if($language_type == "korea"){
		$language_type_str = " and language_type = 'english' ";
	}else{
		$language_type_str = " and language_type = 'korea' ";
	}
	*/
	$sql = "SELECT * FROM admin_dic where dic_type = '$dic_type' and menu_div = '$menu_div' and menu_code = '$menu_code' and language_type = 'korea' $dic_type_str ";
	//echo $sql;
	$db_korea->query($sql);
	$db_korea->fetch();

	$sql = "SELECT * FROM admin_dic where dic_type = '$dic_type' and menu_div = '$menu_div' and menu_code = '$menu_code' and language_type = 'indonesian' $dic_type_str ";
	//echo $sql;

	$db_indonesian->query($sql);
	$db_indonesian->fetch();

}else{
	$act = "insert";
}

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
	<col width='25%' />
	<col width='*' />
	  <tr >
		<td align='left' colspan=2> ".GetTitleNavigation("번역사전관리", "상점관리 > 번역사전관리 ")."</td>
	  </tr>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U") || true){
$Contents01 .= "
	  <tr>
	    <td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>추가하기</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
		<col width='20%' />
		<col width='*' />
	  <tr bgcolor=#ffffff height='34'>
	    <td class='input_box_title'> <b>사전 키 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item'>
		".getMenuGroup($menu_div)."
		".getMenus($menu_div, $menu_code)."
	    ".getLanguage($language_type , " onchange=\"document.location.href='?menu_div=$menu_div&dic_type=$dic_type&dic_code=$dic_code&menu_code=$menu_code&language_type='+this.value\" ")."
		</td>
	  </tr>
	  <tr bgcolor=#ffffff height='34'>
	    <td class='input_box_title'> <b>사전 구분 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item'>
		<input type='radio' name='dic_type' id='dic_type_word' value='WORD' ".(($dic_type == "WORD" || $dic_type == "") ? "checked":"")." onclick=\"document.location='?menu_div=$menu_div&dic_code=$dic_code&dic_type=WORD&menu_code=$menu_code&language_type=$language_type'\"><label for='dic_type_word'>단어타입</label>
		<input type='radio' name='dic_type' id='dic_type_desc' value='DESC' ".($dic_type == "DESC" ? "checked":"")." onclick=\"document.location='?menu_div=$menu_div&dic_code=$dic_code&dic_type=DESC&menu_code=$menu_code&language_type=$language_type'\" ><label for='dic_type_desc'>설명타입</label>
		<div id='dic_code_area' ".($dic_type == "DESC" ? "style='display:inline;'":"style='display:none;'")." class='small'><input type='text' class='textbox' name='dic_code' id='dic_code' value='".$db->dt[dic_code]."' validation='false' title='사전코드' style='width:130px;'> 설명타입은 코드를 지정후 해당 코드값을 이용하여 페이지에 직접 지정하셔야 합니다. </div>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff height='34'>
			<td class='input_box_title'> <b>한글문구 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item'>
			<textarea type=text class='textbox' name='text_korea' style='padding:4px;width:98%;height:60px;margin:4px 0px' ".(($dic_type == "WORD" || $dic_type == "") ? "validation=true":"validation=false")." title='한글번역'>".$db->dt[text_korea]."</textarea>
			<span class=small></span>
			</td>
		</tr>
		<tr bgcolor=#ffffff height='34'>
			<td class='input_box_title'> <b>번역문구 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item'>
			<textarea type=text class='textbox' name='text_trans' style='padding:4px;width:98%;height:60px;margin:4px 0px' ".(($dic_type == "WORD" || $dic_type == "") ? "validation=true":"validation=false")." title='번역문구'>".$db->dt[text_trans]."</textarea>
			<span class=small></span></td>
		</tr>";
 if( $dic_type == "DESC"){//$db_korea->total && $act == "update" &&
		$Contents01 .= "
		<tr bgcolor=#ffffff height='34'>
			<td class='input_box_title'> <b>한글 번역문구 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item'>
			<textarea type=text class='textbox' name='desc_trans_korea' style='padding:4px;width:98%;height:140px;margin:4px 0px' wrap='off'  ".(($dic_type == "DESC") ? "validation=true":"validation=false")." title='번역문구'>".$db_korea->dt[desc_trans]."</textarea>
			<span class=small></span></td>
		</tr>";
}


if($dic_type == "DESC"){//$act == "update" &&
$Contents01 .= "
		<tr bgcolor=#ffffff height='34'>
			<td class='input_box_title'> <b>영문 번역문구 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item'>
			<textarea type=text class='textbox' name='desc_trans_english' style='padding:4px;width:98%;height:140px;margin:4px 0px' wrap='off'  ".(($dic_type == "DESC") ? "validation=true":"validation=false")." title='번역문구'>".$db_english->dt[desc_trans]."</textarea>
			<span class=small></span></td>
		</tr>";
}
if($dic_type == "DESC"){//$db_indonesian->total && $act == "update" &&
$Contents01 .= "
		<tr bgcolor=#ffffff height='34'>
			<td class='input_box_title'> <b>인도네시아 번역문구 <img src='".$required3_path."'></b> </td>
			<td class='input_box_item'>
			<textarea type=text class='textbox' name='desc_trans_indonesian' style='padding:4px;width:98%;height:140px;margin:4px 0px' wrap='off'  ".(($dic_type == "DESC") ? "validation=true":"validation=false")." title='번역문구'>".$db_indonesian->dt[desc_trans]."</textarea>
			<span class=small></span></td>
		</tr>";
}
$Contents01 .= "
		</td>
	  </tr>
	  <tr bgcolor=#ffffff height='34'>
	    <td class='input_box_title'> <b>사용유무 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'>
	    	<input type=radio name='disp' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' value='0' ><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>";
}
$Contents01 .= "
	  </table>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center style='padding:10px 0px'><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' > </td></tr>
</table>
";
}


$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr>
	    <td align='left' colspan=8 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>사전 목록</b></div>")."</td>
	  </tr>
	  </table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
		<col style='width:70px;'>
		<col style='width:70px;'>
		<col style='width:80px;'>
		<col style='width:160px;'>
		<col style='width:*;'>
		<col style='width:100px;'>
		<col style='width:130px;'>
		<col style='width:100px;'>
	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td'> 메뉴구분</td>
		<td class='m_td'> 사전구분</td>
	    <td class='m_td'> 언어구분</td>
	    <td class='m_td'> 한글문구</td>
		<td class='m_td'> 번역문구</td>
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";


$where = "where dic_ix is not null " ;
if($menu_div){
	$where .= " and menu_div = '$menu_div' ";
}
if($menu_code){
	$where .= " and menu_code = '$menu_code' ";
}

if($language_type){
	$where .= " and language_type = '$language_type' ";
}

if($dic_type){
	$where .= " and dic_type = '$dic_type' ";
}


$sql = "SELECT count(*) as total FROM admin_dic $where  ";
//echo $sql;
$db->query($sql);
$db->fetch();
$total = $db->dt[total];

$sql = "SELECT * FROM admin_dic $where order by dic_code asc, regdate desc limit $start, $max  ";
//echo $sql;
$db->query($sql);


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
		    <td class='list_box_td list_bg_gray'>".$db->dt[menu_div]."</td>
			<td class='list_box_td '>".$db->dt[dic_type]."</td>
			<td class='list_box_td list_bg_gray'>".$db->dt[language_type]."</td>
		    <td class='list_box_td point' style='padding:5px;'>".($db->dt[dic_type] == "WORD" ? $db->dt[text_korea]:"")."</td>
		    <td class='list_box_td list_bg_gray' style='padding:5px;'>".($db->dt[dic_type] == "WORD" ? $db->dt[text_trans]:"")."</td>

		    <td class='list_box_td '>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td list_bg_gray'>".$db->dt[regdate]."</td>
		    <td class='list_box_td '>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				//$Contents02 .= "<a href=\"javascript:updateLanguageInfo('".$db->dt[dic_ix]."','".$db->dt[text_div]."','".$db->dt[text_korea]."','".$db->dt[text_english]."','".$db->dt[disp]."')\"><img src='../image/btc_modify.gif' border=0></a>";
				$Contents02 .= "<a href=\"?dic_ix=".$db->dt[dic_ix]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}else{
				$Contents02 .= "<a href=\"javascript:alert('수정권한이 없습니다.');\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents02 .= "<a href=\"javascript:deleteDicInfo('delete','".$db->dt[dic_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}else{
				$Contents02 .= "<a href=\"javascript:alert('삭제권한이 없습니다.')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}
			$Contents02 .= "
		    </td>
		  </tr>";
		if($db->dt[dic_type] == "DESC"){
$Contents02 .= "
			<tr height=1>
				<td colspan=8 style='background-color:#efefef;padding:20px'><b>".$db->dt[dic_code]."</b><br>
				".$db->dt[desc_trans]."<br>";
				if($db->dt[language_type] != "english"){
				$Contents02 .= "<a href='?trans_mode=desc_trans&dic_ix=".$db->dt[dic_ix]."&language_type=english'>영문번역</a>";
				}
				$Contents02 .= "</td>
			</tr>	  ";
		}

	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=8>등록된 사전 목록이 없습니다. </td>
		  </tr>
		  <tr height=1><td colspan=8 class='dot-x'></td></tr>	  ";
}

$Contents02 .= "</table>";
$Contents02 .= "<ul class='paging_area' >
						<li class='front'></li>
						<li class='back'>".page_bar($total, $page, $max,$query_string."&menu_div=$menu_div&menu_code=$menu_code&language_type=$language_type&dic_type=$dic_type","")."</li>
					  </ul>";

$ContentsDesc02 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td align=left style='padding:10px;'>
		<img src='../image/emo_3_15.gif' align=absmiddle>  거래은행 및 계좌 정보는 결제시 이용됩니다. 정확하게 입력해주세요
	</td>
</tr>
</table>
";



$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='dictionary_frm' action='dic.act.php' method='post' onsubmit='return CheckFormValue(this)' style='display:inline;'><input name='act' type='hidden' value='".$act."'><input name='dic_ix' type='hidden' value='".$dic_ix."'>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";

 $Script = "
 <script language='javascript'>
function ChangeDicType(obj){
	if(obj.value == 'WORD'){
		$('#dic_code').hide();
		$('#desc_trans_area').hide();
		$('#dic_code_area').hide();

		$('#text_trans_area').show();
		$('#text_korea').attr('validation','true');
		$('#text_trans').attr('validation','true');

	}else{
		$('#dic_code').show();
		$('#desc_trans_area').show();
		$('#text_trans_area').hide();
		$('#dic_code_area').css('display','inline');
		$('#text_korea').attr('validation','false');
		$('#text_trans').attr('validation','false');
	}
}
 function updateLanguageInfo(dic_ix,text_div,text_korea, text_english ,disp){
 	var frm = document.dictionary_frm;

 	frm.act.value = 'update';
 	frm.dic_ix.value = dic_ix;
 	//frm.text_name.value = text_name;
 	frm.text_div.value = text_div;
 	frm.text_korea.value = text_korea;
	frm.text_english.value = text_english;
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}
	//frm.bank_name.focus();

}

 function deleteDicInfo(act, dic_ix){
 	if(confirm('해당랭귀지 목록을 정말로 삭제하시겠습니까?')){
 		var frm = document.dictionary_frm;
 		frm.act.value = act;
 		frm.dic_ix.value = dic_ix;
 		frm.submit();
 	}
}
function etcBank(etc){
	if(etc == 'etc'){
		document.getElementById('etc').disabled = false;
	}else{
		document.getElementById('etc').disabled = true;
	}
}
 </script>
 ";


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->Navigation = "상점관리 > 다국어지원 > 번역사전관리";
$P->title = "번역사전관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();


function getMenuGroup($selected=""){
	global $menu_code, $language_type;
	$mdb = new Database;

	$sql = 	"SELECT *
			FROM admin_menu_div
			where disp=1 order by vieworder asc ";

	$mdb->query($sql);

	$mstring = "<select name='menu_div' id='menu_div' onchange=\"document.location.href='?menu_div='+this.value+'&menu_code=".$menu_code."&language_type=$language_type'\">";
	$mstring .= "<option value=''>1차분류</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[div_name] == $selected){
				$mstring .= "<option value='".$mdb->dt[div_name]."' selected>".$mdb->dt[div_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[div_name]."'>".$mdb->dt[div_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}

function getMenus($menu_div, $selected=""){
	global $language_type;

	$mdb = new Database;

	$sql = 	"SELECT *
			FROM admin_menus
			where menu_div = '".$menu_div."'  and disp_auth = 'Y' order by view_order asc ";//and disp_auth = 'Y'

	$mdb->query($sql);

	$mstring = "<select name='menu_code' id='menu_code' onchange=\"document.location.href='?menu_div=".$menu_div."&language_type=$language_type&menu_code='+this.value\">";
	$mstring .= "<option value=''>페이지명</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[menu_code] == $selected){
				$mstring .= "<option value='".$mdb->dt[menu_code]."' selected>".$mdb->dt[menu_name]." (".$mdb->dt[menu_link].")</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[menu_code]."'>".$mdb->dt[menu_name]." (".$mdb->dt[menu_link].")</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}

/*

create table admin_dic (
dic_ix int(4) unsigned not null auto_increment  ,
dic_type enum('DIC','DESC') default 'DIC',
menu_div varchar(50) null default null,
menu_code varchar(32) null default null,
language_type enum('english','chinese','indonesia','japan') null default null,
text_div varchar(255) null default null,
text_name varchar(255) null default null,
disp char(1) default '1' ,
regdate datetime not null,
primary key(dic_ix));

*/
?>
