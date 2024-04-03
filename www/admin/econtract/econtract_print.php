<?php
	include("../class/layout.class");
	include("./contract.lib.php");

	$db = new Database();

	$db->query("SELECT * FROM econtract_info WHERE ei_ix ='$ei_ix'");

//$total = $db->total;
	if( $db->total ) {
		$db->fetch();
		$mall_ix = $db->dt['mall_ix'];
		$ei_ix = $db->dt['ei_ix'];
		$et_ix = $db->dt['et_ix'];
		$contract_group = $db->dt['contract_group'];
		$contract_title = $db->dt['contract_title'];
		$contract_type = $db->dt['contract_type'];
		$contract_detail = $db->dt['contract_detail'];
		$contract_sdate = $db->dt['contract_sdate'];
		$contract_edate = $db->dt['contract_edate'];
		$contract_date = $db->dt['contract_date'];
		$company_id = $db->dt['company_id'];
		$contractor_id = $db->dt['contractor_id'];
		$com_ceo = $db->dt['com_ceo'];
		$com_zip = $db->dt['com_zip'];
		$com_reg_no = $db->dt['com_reg_no'];
		$com_addr1 = $db->dt['com_addr1'];
		$com_addr2 = $db->dt['com_addr2'];
		$contractor_ceo = $db->dt['contractor_ceo'];
		$contractor_zip = $db->dt['contractor_zip'];
		$contractor_reg_no = $db->dt['contractor_reg_no'];
		$contractor_addr1 = $db->dt['contractor_addr1'];
		$contractor_addr2 = $db->dt['contractor_addr2'];
		$is_multiple = 0;//$db->dt[is_multiple];
		$is_use = $db->dt['is_use']; 
		$sign_type = $db->dt['sign_type']; 
		$priod_type = $db->dt['priod_type']; 
		$extension_year = $db->dt['extension_year']; 
		$signature_date = $db->dt['signature_date'];
		$com_signature = $db->dt['com_signature'];
		
		$contractor_signature_date = $db->dt['contractor_signature_date'];
		$contractor_signature = $db->dt['contractor_signature']; 

		$charger_ix = $db->dt['charger_ix'];
		$act = "updateContract";
	}

$Contents = '
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td  align="center">
		<img src="../images/'.$admininfo["language"].'/btn_print.gif" align=absmiddle border=0 onClick=\'printArea("'.$ei_ix.'")\' style="cursor:pointer;" />
	</td>
</tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<!-- // 전자계약서 작성 -->
<tr>
	<td align="left" colspan="6">' . GetTitleNavigation("전자계약서 작성", "전자계약 관리 > 전자계약서 작성 ") . ' </td>
</tr>
<tr>
	<td align="left" colspan="6" style="padding:3px 0px;">' . colorCirCleBox("#efefef","100%","<div style='padding:5px;'><b><img src='../image/title_head.gif' align=absmiddle> 전자계약서 내용</b></div>") . '</td>
</tr>
<tr>
	<td height="20" colspan="6" id="printDiv">
	' . $contract_detail . '
	</td>
</tr>
</table>
<object id="CC_Object_id" classid="CLSID:A099920B-630C-426B-91EC-737685CEEE17" codebase="AxCrossCert.cab#Version=2,6,6,0" width="Document.body.clientWidth" height="Document.body.clientHeight"></object>';

$Script = '
<script src="../search.js"></script>
<script src="/include/ckeditor/ckeditor.js"></script>
<script src="/js/crosscert.js"></script>
<script>

	$(function(){
		$("#com-ceo-sign").click(function(){
			var data = SignData({
				"act": "signInsert",
				"sign_type": "H",
				"orgin_document": $("#contract_detail").html(),
				"ei_ix": "' . $ei_ix . '",
				"reg_no": $("#com-reg-no").val()
			});
		});
		
		$("#contractor-ceo-sign").click(function(){
			var data = SignData({
				"act": "signInsert",
				"sign_type": "C",
				"orgin_document": $("#contract_detail").html(),
				"ei_ix": "' . $ei_ix . '",
				"reg_no": $("#contractor-reg-no").val()
			});
		});
	});

function printArea() {
	document.body.innerHTML = document.getElementById("printDiv").innerHTML;
	window.focus();
	window.print();
	//self.close();
}';

if($mmode == "print"){
	$Script .= "
	$(window).ready(function() {
		printArea();
	});";
}

$Script .= "

</script>";

if($mmode == "print"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = econtract_menu();
	$P->Navigation = "전자계약관리 > 계약서 상세보기";
	$P->NaviTitle = "전자계약서";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();	
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = econtract_menu();
	$P->strContents = $Contents;
	$P->Navigation = "전자계약 > 전자계약 관리 > 계약서 상세보기";
	$P->title = "계약서 상세보기";
	echo $P->PrintLayOut();
}
/*
CREATE TABLE IF NOT EXISTS `econtract_info` (
  `ei_ix` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `company_id` varchar(32) DEFAULT NULL COMMENT '계약자(갑) 회사코드',
  `com_zip` varchar(7) DEFAULT NULL COMMENT '우편번호',
  `com_addr1` varchar(255) DEFAULT NULL COMMENT '주소',
  `com_addr2` varchar(255) DEFAULT NULL COMMENT '나머지주소',
  `com_reg_no` varchar(20) DEFAULT NULL COMMENT '사업자등록번호',
  `use_com_reg_no` int(1) default '1' COMMENT '1:사용, 2:미사용',
  
  `contractor_id` varchar(32) DEFAULT NULL COMMENT '계약자(을) 회사코드',
  `contractor_zip` varchar(7) DEFAULT NULL COMMENT '우편번호',
  `contractor_addr1` varchar(255) DEFAULT NULL COMMENT '주소',
  `contractor_addr2` varchar(255) DEFAULT NULL COMMENT '나머지주소',
  `contractor_reg_no` varchar(20) DEFAULT NULL COMMENT '계약자 사업자등록번호',
  `use_contractor_reg_no` int(1) default '1' COMMENT '1:사용, 2:미사용',

  `et_ix` int(10) unsigned NOT NULL COMMENT '계약서 코드',  
  `contract_type` int(1) default '1' COMMENT '1:일반계약서, 2:첨부서류',
  `contract_title` varchar(255) DEFAULT NULL COMMENT '계약서명',
  `contract_group` int(5) DEFAULT NULL COMMENT '계약서 분류',
  `contract_date` varchar(10) DEFAULT NULL COMMENT '계약서일자',
  `contract_sdate` varchar(10) DEFAULT NULL COMMENT '계약서 시작일자',
  `contract_edate` varchar(10) DEFAULT NULL COMMENT '계약서 종료일자',
  `contract_detail` mediumtext DEFAULT NULL COMMENT '계약서 내용',  
  `priod_type` int(1) DEFAULT NULL COMMENT '계약기간 타입 1:1회성, 자동연장 ',
  `extension_year` int(2) DEFAULT NULL COMMENT '연장기간',
  `sign_type` int(1) DEFAULT NULL COMMENT '전자서명 타입 1: 요청시 자동서명, 2: 클라이언트(협력사/발주처)등 서명후 별도 서명',
  `charger_ix` varchar(32) DEFAULT NULL COMMENT '담당자',
  `is_use` enum(1,0) DEFAULT 1 COMMENT '사용여부',
  `editdate` datetime DEFAULT NULL COMMENT '수정일',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`ei_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='계약서 정보'  AUTO_INCREMENT='100000';

*/

?>