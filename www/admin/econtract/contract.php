<?
include("../class/layout.class");
include("./contract.lib.php");
//include($_SERVER["DOCUMENT_ROOT"]."../include/admin.util.php");
//include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database();
//print_r($_SESSION["admininfo"]);
$db->query("Select * from econtract_tmp where et_ix ='$et_ix'");
$db->fetch();
//$total = $db->total;
if($db->total){
	$mall_ix = $db->dt[mall_ix];
	$et_ix = $db->dt[et_ix];
	
	$contract_type = $db->dt[contract_type];
	$contract_title = $db->dt[contract_title];
	$contract_code = $db->dt[contract_code];
	$contract_group = $db->dt[contract_group];
	$contract_detail = $db->dt[contract_detail];
	$priod_type = $db->dt[priod_type]; 
	$extension_year = $db->dt[extension_year]; 
	$charger_ix = $db->dt[charger_ix];  
	$is_use = $db->dt[is_use]; 
	$use_relation_file = $db->dt[use_relation_file]; 
	
	$priod_type = $db->dt[priod_type]; 
	$company_id = $_SESSION["admininfo"]["company_id"];
	//$charger_ix = $_SESSION["admininfo"]["charger_ix"];

	$disp = $db->dt[disp]; 

	 
	$act = "update";
}else{
	$contract_title = "";
	$contract_code = "";   
	$contract_group = "";
	$is_use = 1; 
	$use_relation_file = 0; 
	$disp = 1; 
	/*
	$contract_title = "테스트 계약서";
	$contract_code = "";
	$contract_group = "1";
	$contract_detail = "계약서 내용";
	*/
	$company_id = $_SESSION["admininfo"]["company_id"];
	$charger_ix = $_SESSION["admininfo"]["charger_ix"];

	$cupon_img = "<img src='../image/0.gif'  style='border:1px solid silver' name='preview_cupon' id='preview_cupon' width='350' height='230' align='absmiddle'>";
	$act = "insert";
}

$Contents = "
<table width='100%' border='0' cellpadding='0' cellspacing='0'>
  <!-- // 계약서 작성 -->
  <tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("계약서 작성", "전자계약 관리 > 계약서 작성 ")."</td>
  </tr>
 
  <tr>
    <td height='10'></td>
  </tr>
  <tr>
	<td align='left' colspan=6 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><b><img src='../image/title_head.gif' align=absmiddle> 계약서 기본설정</b></div>")."</td>
  </tr>
  <tr>
    <td valign='top'>
	<form name='form_cupon' onsubmit='return CheckFormValue(this)' method='post' enctype='multipart/form-data'  action='contract.act.php' target='iframe_act'>
	<input type=hidden name='act' value='".$act."'>
	<input type=hidden name='et_ix' value='".$et_ix."'>
      <table width='100%' border='0' cellpadding='0' cellspacing='0' class='input_table_box'>
		<col width='15%'>
		<col width='35%'>
		<col width='15%'>
		<col width='35%'> 
		<tr >
		  <td class='input_box_title' >  <b>계약서 종류</b></td>
		  <td class='input_box_item'>
				<input type='checkbox' name='contract_type' id='contract_type_1'  align='middle' value='1' ".($contract_type == '1' ? "checked":"")."><label for='contract_type_1' class='green'>셀러 기본계약서(선택시 다른 계약서는 기본계약서가 해지 됩니다.)</label> 
				<!--input type='radio' name='contract_type' id='contract_type_2'  align='middle' value='2' ".($contract_type == '2' ? "checked":"")."><label for='contract_type_2' class='green'>첨부서류</label--> 
		  </td>
		  <td class='input_box_title' >  <b>계약서 분류</b></td>
		  <td class='input_box_item'>
				".getContractGroup($contract_group)."
		  </td> 
		</tr>
        <tr height=30>
          <td class='input_box_title'  > 계약서명</td>
          <td class='input_box_item' style='padding-left:10px;'>
		  <input type='text' validation='true' title='계약서명' id='contract_title' name='contract_title' class='textbox' maxlength='50' style='height: 20px; width: 300px; filter: blendTrans(duration=0.5)' align='absmiddle' value='".$contract_title."'></td>
		  <td class='input_box_title'  > 계약서코드(자동)</td>
          <td class='input_box_item' style='padding-left:10px;'>
		  ".($et_ix ? $et_ix:"계약서 정보 작성시 자동으로 부여됩니다.")."
		  <!--input type='text' validation='true' title='계약서코드' id='contract_title' name='contract_code' class='textbox' maxlength='50' style='height: 20px; width: 300px; filter: blendTrans(duration=0.5)' align='absmiddle' value='".$contract_code."'--></td>
        </tr>
		   
		<tr >
		  <td class='input_box_title' >  <b>사용여부</b></td>
		  <td class='input_box_item'>
				<input type='radio' name='is_use' id='is_use_1'  align='middle' value='1' ".($is_use == '1' || $is_use == '' ? "checked":"")."><label for='is_use_1' class='green'>사용함</label> 
				<input type='radio' name='is_use' id='is_use_0'  align='middle' value='0' ".($is_use == '0' ? "checked":"")."><label for='is_use_0' class='green'>미사용</label> 
		  </td>
			<td class='input_box_title'>담당자</td>
			<td class='input_box_item'>
			".CompayChargerSearch($company_id ,$charger_ix,"","selectbox")."
			</td>
		</tr>
        <tr >
		  <td class='input_box_title' >  <b>계약기간</b></td>
		  <td class='input_box_item' colspan=3>
				<input type='radio' name='priod_type' id='priod_type_1'  align='middle' value='1' ".($priod_type == '1' || $priod_type == '' ? "checked":"")."><label for='priod_type_1' class='green'>1회성</label> 
				<input type='radio' name='priod_type' id='priod_type_2'  align='middle' value='2' ".($priod_type == '2' ? "checked":"")."><label for='priod_type_2' class='green'>계약만료일로부터 
				<select name='extension_year'>
					<option value='1' ".($extension_year == "1" ? "selected":"").">1년</option>
					<option value='2' ".($extension_year == "2" ? "selected":"").">2년</option>
				</select>
				자동연장</label> 
		  </td>
		</tr> 
		<tr >
		  <td class='input_box_title' >  <b>첨부서류</b></td>
		  <td class='input_box_item' colspan=3 id='relation_file_zone' style='padding:10px;'>
				<div style='padding-bottom:5px;'>
				<input type='radio' name='use_relation_file' id='use_relation_file_1'  align='middle' value='1' ".($use_relation_file == '1' || $use_relation_file == '' ? "checked":"")." onclick=\"$('.relation_file_box').show();\"><label for='use_relation_file_1' class='green'>사용함</label> 
				<input type='radio' name='use_relation_file' id='use_relation_file_0'  align='middle' value='0' ".($use_relation_file == '0' ? "checked":"")." onclick=\"$('.relation_file_box').hide();\"><label for='use_relation_file_0' class='green'>미사용</label> 
				</div>
		  ";
$sql = "select * from econtract_file_tmp where et_ix = '".$et_ix."' ";
$db->query($sql);

for($i=0;($i < $db->total || $i == 0);$i++){
	$db->fetch($i);
$Contents .= "
				 <div id='relation_file_box' class='relation_file_box' style='padding:3px 0px;".($use_relation_file == 0 ? "display:none;":"")."'>
				 <input type=text class='textbox'  name='relation_files[]' id='relation_file' style='vertical-align:middle;' value='".$db->dt[file_text]."'> 
				 <img src='../images/korea/btn_add.gif' border='0' align=absmiddle onclick=\"AddFiles();\"> 
				 <img src='../images/korea/btc_del.gif' border='0' align=absmiddle id='remove_btn' ".($i == 0 ? "style='display:none;'":"")." onclick=\"$(this).closest('div').remove();\"> 
				 </div>";
}
$Contents .= "
		  </td>
		</tr> 
      </table>
    </td>
  </tr>
  <tr>
	<td align='left' colspan=6 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><b><img src='../image/title_head.gif' align=absmiddle> 계약서 작성</b></div>")."</td>
  </tr>
  <tr>
    <td height='20'><textarea name='contract_detail' >".$contract_detail."</textarea></td>
  </tr>
  <tr>
    <td align='center' style='padding-top:20px;'>";
if($_GET["cupon_ix"] == ""){
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
		$Contents .= "<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0>";
	}else{
		$Contents .= "<a href=\"".$auth_write_msg."\"><img  src='../images/".$admininfo["language"]."/b_save.gif' border=0></a>";
	}
}else{
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
		$Contents .= "<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0>";
	}else{
		$Contents .= "<a href=\"".$auth_update_msg."\"><img  src='../images/".$admininfo["language"]."/b_save.gif' border=0></a>";
	}
}
$Contents .= "
	</td>
  </tr></form>
</table>";

$help_text = "
	<table cellpadding=3 cellspacing=0 class='small' >
		<col width=8>
		<col width=150>
		<col width=150>
		<col width=200>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td colspan=4 ><b>아래 치환코드를 계약서에 삽입하시면 계약 업체에 따라 자동으로 계약서의 정보가 변경되게 됩니다. </b></td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td  ><b>치환코드</b></td></tr>
		<tr><td valign=top> </td><td  >상호명 : </td><td>{상호명}</td></tr>
		<tr><td valign=top> </td><td  >대표자명 : </td><td>{대표자명}</td></tr>
		<tr><td valign=top> </td><td  >회사주소 : </td><td>{회사주소}</td></tr>		
		
		
		<tr><td valign=top> </td><td  >협력사 상호명 : </td><td>{협력사 상호명}</td></tr>
		<tr><td valign=top> </td><td  >협력사 회사주소 : </td><td>{협력사 회사주소}</td></tr>
		<tr><td valign=top> </td><td  >협력사 대표자명 : </td><td>{협력사 대표자명}</td></tr>
		<tr><td valign=top> </td><td  >협력사 승인일자 : </td><td>{협력사 승인일자}</td><td> 예) ".date("Y년 m월 d일")."</td><td></td></tr>
		<tr><td valign=top> </td><td  >보증보험 가입액 : </td><td>{보증보험 가입액}</td></tr>
		<tr><td valign=top> </td><td  >수수료율 : </td><td>{수수료율}</td></tr>
		<tr><td valign=top> </td><td  >대금 지급횟수 : </td><td>{대금 지급횟수}</td></tr>
		<tr><td valign=top> </td><td  >대금 지급일자 : </td><td>{대금 지급일자}</td></tr>
		
	</table>";

$help_text = HelpBox("<table cellpadding='0' cellspacing='0' border='0'><tr><td ><b>계약서 작성</b></td></tr></table>", $help_text,70);
$Contents = $Contents.$help_text;

 $Script = "
<script language='JavaScript' src='/include/ckeditor/ckeditor.js'></script>
 <script language='javascript'>
 $(document).ready(function() {

CKEDITOR.replace('contract_detail',{
		docType : '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">',
		font_defaultLabel : '굴림',
		font_names : '굴림/Gulim;돋움/Dotum;바탕/Batang;궁서/Gungsuh;Arial/Arial;Tahoma/Tahoma;Verdana/Verdana',
		fontSize_defaultLabel : '12px',
		fontSize_sizes : '8/8px;9/9px;10/10px;11/11px;12/12px;14/14px;16/16px;18/18px;20/20px;22/22px;24/24px;26/26px;28/28px;36/36px;48/48px;',
		language :'ko',
		resize_enabled : false,
		enterMode : CKEDITOR.ENTER_BR,
		shiftEnterMode : CKEDITOR.ENTER_P,
		startupFocus : false,
		uiColor : '#EEEEEE',
		toolbarCanCollapse : false,
		menu_subMenuDelay : 0,
		toolbar : [['Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','TextColor','BGColor','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Link','Unlink','-','Find','Replace','SelectAll','RemoveFormat','-','Image','Flash','Table','SpecialChar'],'/',['Source','-','ShowBlocks','-','Font','FontSize','Undo','Redo','-','About'],['Maximize']],
		filebrowserImageUploadUrl : '/include/ckeditor/upload.php',
		height:300});

});

function AddFiles(){
	
	var newRow = $('#relation_file_box:last').clone(true).appendTo($('#relation_file_zone'));  

	newRow.find('input[id=relation_file]').val('');
	newRow.find('img[id=remove_btn]').show();
}
</script>
 ";


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = econtract_menu();
$P->strContents = $Contents;
$P->Navigation = "전자계약 > 전자계약 관리 > 계약서 작성";
$P->title = "계약서 작성";
echo $P->PrintLayOut();


/*
CREATE TABLE IF NOT EXISTS `econtract_tmp` (
  `et_ix` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `contract_type` int(1) default '1' COMMENT '1:일반계약서, 2:첨부서류',
  `contract_title` varchar(255) DEFAULT NULL COMMENT '계약서명',
  `contract_group` int(5) DEFAULT NULL COMMENT '계약서 분류',
  `contract_detail` mediumtext DEFAULT NULL COMMENT '계약서 내용',  
  `priod_type` int(1) DEFAULT NULL COMMENT '계약기간 타입 1:1회성, 자동연장 ',
  `extension_year` int(2) DEFAULT NULL COMMENT '연장기간',
  `charger_ix` varchar(32) DEFAULT NULL COMMENT '담당자',
  `is_use` enum('1','0') DEFAULT 1 COMMENT '사용여부',
  `editdate` datetime DEFAULT NULL COMMENT '수정일',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`et_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='계약서 정보' ;

CREATE TABLE IF NOT EXISTS `econtract_file_tmp` (
  `et_ix` int(10) unsigned NOT NULL COMMENT '자주쓰는 계약서키',
  `file_text` varchar(100) default NULL COMMENT '파일 이름 ',
  `insert_yn` enum('1','0') DEFAULT '1' COMMENT '입력 flag',
  `regdate` datetime DEFAULT NULL COMMENT '등록일'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='계약서 파일 정보'

CREATE TABLE IF NOT EXISTS `econtract_info_file` (
  `ei_ix` int(10) unsigned NOT NULL COMMENT '자주쓰는 계약서키',
  `file_text` varchar(100) default NULL COMMENT '파일 이름 ',
  `insert_yn` enum('1','0') DEFAULT '1' COMMENT '입력 flag',
  `regdate` datetime DEFAULT NULL COMMENT '등록일'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='계약서 파일 정보'


CREATE TABLE IF NOT EXISTS `econtract_info_status` (
  `eis_ix` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `ei_ix` int(10) unsigned NOT NULL COMMENT '전자계약 키값',
  `status` varchar(3) NOT NULL COMMENT '전자계약상태',
  `status_message` varchar(255) DEFAULT NULL COMMENT '상태 상세',
  `admin_message` varchar(255) DEFAULT NULL COMMENT '관리자 상태 메모',
  `company_id` varchar(32) DEFAULT NULL COMMENT '업체 키값',
  `charger_ix` varchar(32) DEFAULT NULL COMMENT '담당자',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY (`eis_ix`),
  KEY `ei_ix` (`ei_ix`),
  KEY `company_id` (`company_id`),
  KEY `charger_ix` (`charger_ix`),
  KEY `regdate` (`regdate`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='전자계약 상태로그'  ;

*/


?>