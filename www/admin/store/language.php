<?
include("../class/layout.class");



$db = new Database;

$db->query("SELECT * FROM admin_language al WHERE  al.language_ix = '".$language_ix."'");
$db->fetch();

if($db->total){
	$text_div = $db->dt[text_div];
	$language_type = $db->dt[language_type];
	$act = "update";
}else{
	$act = "insert";
}

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
	<col width='25%' />
	<col width='*' />
	  <tr >
		<td align='left' colspan=2> ".GetTitleNavigation("랭귀지팩관리", "상점관리 > 랭귀지팩관리 ")."</td>
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
	    <td class='input_box_title'> <b>메뉴구분 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'>
		<select name='text_div' style='width:140px;padding:1px;border:1px solid silver' onchange=\"document.location.href='?text_div='+this.value+'&language=".$_GET["language"]."'\">
			<option value=''>선택</option>
			<option value='common' ".($text_div == "common" ? "selected":"").">공통</option>
			<option value='gnb' ".($text_div == "gnb" ? "selected":"").">상단메뉴</option>
			<option value='store_lnb' ".($text_div == "store_lnb" ? "selected":"").">상점관리 좌측메뉴</option>
			<option value='basic_lnb' ".($text_div == "basic_lnb" ? "selected":"").">기초정보관리 좌측메뉴</option>
			<option value='seller_accounts_lnb' ".($text_div == "seller_accounts_lnb" ? "selected":"").">판매자정산 좌측메뉴</option>
			<option value='buyer_accounts_lnb' ".($text_div == "buyer_accounts_lnb" ? "selected":"").">구매자정산 좌측메뉴</option>
			
			<option value='seller_lnb' ".($text_div == "seller_lnb" ? "selected":"").">셀러관리 좌측메뉴</option>
			<option value='design_lnb' ".($text_div == "design_lnb" ? "selected":"").">디지인관리 좌측메뉴</option>
			<option value='product_lnb' ".($text_div == "product_lnb" ? "selected":"").">상품관리 좌측메뉴</option>
			<option value='sns_lnb' ".($text_div == "sns_lnb" ? "selected":"").">소셜커머스 좌측메뉴</option>
			<option value='order_lnb' ".($text_div == "order_lnb" ? "selected":"").">주문관리 좌측메뉴</option>
			<option value='member_lnb' ".($text_div == "member_lnb" ? "selected":"").">회원관리 좌측메뉴</option>
			<option value='mk_lnb' ".($text_div == "mk_lnb" ? "selected":"").">mk_lnb</option>
			<option value='ec_lnb' ".($text_div == "ec_lnb" ? "selected":"").">이커머스분석</option>
			<option value='log_lnb' ".($text_div == "log_lnb" ? "selected":"").">로그분석</option>
			<option value='display_lnb' ".($text_div == "display_lnb" ? "selected":"").">프로모션/전시</option>
			<option value='promotion_lnb' ".($text_div == "promotion_lnb" ? "selected":"").">프로모션(마케팅)</option>
			<option value='mShop_lnb' ".($text_div == "mShop_lnb" ? "selected":"").">모바일샵</option>
			<option value='estimate_lnb' ".($text_div == "estimate_lnb" ? "selected":"").">견적센타</option>
			<option value='offline_order_lnb' ".($text_div == "offline_order_lnb" ? "selected":"").">통합구매</option>
			
			<option value='database_lnb' ".($text_div == "database_lnb" ? "selected":"").">데이타베이스</option>
			<option value='bbsmanage_lnb' ".($text_div == "bbsmanage_lnb" ? "selected":"").">게시판관리</option>
			<option value='cscenter_lnb' ".($text_div == "cscenter_lnb" ? "selected":"").">고객센타</option>
			<option value='cogoods_lnb' ".($text_div == "cogoods_lnb" ? "selected":"").">cogoods_lnb</option>
			<option value='inventory_lnb' ".($text_div == "inventory_lnb" ? "selected":"").">재고관리</option>
			<option value='campaign_lnb' ".($text_div == "campaign_lnb" ? "selected":"").">메일링/SMS</option>
			<option value='work_lnb' ".($text_div == "work_lnb" ? "selected":"").">업무관리</option>
			<option value='tax_lnb' ".($text_div == "tax_lnb" ? "selected":"").">세금계산서</option>
			<option value='econtract_lnb' ".($text_div == "econtract_lnb" ? "selected":"").">전자계약</option>
		</select>
		".getLanguage($language_type,"onchange=\"document.location.href='?text_div=".$_GET["text_div"]."&language_type='+this.value\" ")."
		<!--input type=text class='textbox' name='text_div' value='".$db->dt[text_div]."' style='width:430px;' validation=true title='메뉴구분'> <span class=small></span-->
		</td>
	  </tr>
	  <tr bgcolor=#ffffff height='34'>
	    <td class='input_box_title'> <b>한글번역 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'>
		<textarea type=text class='textbox' name='text_korea' style='padding:4px;width:97%;height:40px;margin:4px 0px' validation=true title='한글번역'>".$db->dt[text_korea]."</textarea>
		<span class=small></span>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff height='34'>
	    <td class='input_box_title'> <b>번역문구 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'>
		<textarea type=text class='textbox' name='text_trans' style='padding:4px;width:97%;height:40px;margin:4px 0px' validation=true title='번역문구'>".$db->dt[text_trans]."</textarea>
		<span class=small></span></td>
	  </tr>
	  <!--tr bgcolor=#ffffff height='34'>
	    <td class='input_box_title'> <b>인도네시아어 번역</b> </td>
	    <td class='input_box_item'>
		<textarea type=text class='textbox' name='text_indomesian' style='padding:4px;width:700px;height:40px;margin:4px 0px' validation=false title='인도네시아어번역'>".$db->dt[text_indomesian]."</textarea>
		<span class=small></span></td>
	  </tr>
	  <tr bgcolor=#ffffff height='34'>
	    <td class='input_box_title'> <b>중국어번역 </b> </td>
	    <td class='input_box_item'>
		<textarea type=text class='textbox' name='text_chinese' style='padding:4px;width:700px;height:40px;margin:4px 0px' validation=false title='중국어번역'>".$db->dt[text_chinese]."</textarea>
		<span class=small></span></td>
	  </tr-->
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
<tr bgcolor=#ffffff ><td colspan=4 align=center style='padding:10px 0px'><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
}
$SearchForm = "<form name='search_frm' method='GET'><input type='hidden' name='text_div' value='$text_div'>
				<table>
					<tr>
						<td><img src='../image/title_head.gif' align=absmiddle> <b>랭귀지 목록</b></td>
						<td><input type='text' class=textbox name='search_text' value='$search_text' ></td>
						<td><input type='image' src='../images/".$admininfo['language']."/btn_search.gif' align=absmiddle></td>
					</tr>
				</table></form>";

$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
		<col style='width:80px;'>
		<col style='width:70px;'>
		<col style='width:150px;'>
		<col style='width:*;'>
		<col style='width:80px;'>
		<col style='width:120px;'>
		<col style='width:110px;'>
	  <tr>
	    <td align='left' colspan=7 style='padding:4px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:0px 3px 0px 13px;'> $SearchForm </div>")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=7 style='padding-bottom:20px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".($text_div == "" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='?text_div='>전체</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($text_div == "common" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='?text_div=common'>common</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_03' ".($text_div == "gnb" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='?text_div=gnb'>gnb</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_04' ".($text_div == "store_lnb" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='?text_div=store_lnb'>상점관리</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_05' ".($text_div == "seller_lnb" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='?text_div=seller_lnb'>셀러관리</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_06' ".($text_div == "design_lnb" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='?text_div=design_lnb'>디자인관리</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_07' ".($text_div == "product_lnb" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='?text_div=product_lnb'>상품관리</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<!--table id='tab_08' ".($text_div == "sns_lnb" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='?text_div=sns_lnb'>sns_lnb</a></td>
						<th class='box_03'></th>
					</tr>
					</table-->
					<table id='tab_09' ".($text_div == "order_lnb" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='?text_div=order_lnb'>주문관리</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<!--table id='tab_10' ".($text_div == "member_lnb" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='?text_div=member_lnb'>회원관리</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_11' ".($text_div == "mk_lnb" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='?text_div=mk_lnb'>mk_lnb</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_12' ".($text_div == "ec_lnb" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='?text_div=ec_lnb'>ec_lnb</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_13' ".($text_div == "log_lnb" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='?text_div=log_lnb'>log_lnb</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_14' ".($text_div == "display_lnb" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='?text_div=display_lnb'>display_lnb</a></td>
						<th class='box_03'></th>
					</tr>
					</table-->

				</td>
				<td style='width:145px;text-align:right;vertical-align:bottom;padding:0px 0px 10px 0'>
					<select name='text_div' style='width:140px;padding:1px;border:1px solid silver' onchange=\"document.location.href='?text_div='+this.value\">
						<option value=''>선택</option>
						<option value='member_lnb' ".($text_div == "member_lnb" ? "selected":"").">회원관리</option>
						<option value='mk_lnb' ".($text_div == "mk_lnb" ? "selected":"").">마케팅</option>
						<option value='ec_lnb' ".($text_div == "ec_lnb" ? "selected":"").">이커머스분석</option>
						<option value='log_lnb' ".($text_div == "log_lnb" ? "selected":"").">로그분석</option>
						<option value='display_lnb' ".($text_div == "display_lnb" ? "selected":"").">프로모션/전시</option>
						<option value='promotion_lnb' ".($text_div == "promotion_lnb" ? "selected":"").">프로모션(마케팅)</option>
						<option value='mShop_lnb' ".($text_div == "mShop_lnb" ? "selected":"").">모바일샵</option>
						<option value='estimate_lnb' ".($text_div == "estimate_lnb" ? "selected":"").">견적센타</option>
						<option value='database_lnb' ".($text_div == "database_lnb" ? "selected":"").">데이타베이스</option>
						<option value='bbsmanage_lnb' ".($text_div == "bbsmanage_lnb" ? "selected":"").">게시판관리</option>
						<option value='cscenter_lnb' ".($text_div == "cscenter_lnb" ? "selected":"").">고객센타</option>
						<option value='cogoods_lnb' ".($text_div == "cogoods_lnb" ? "selected":"").">cogoods_lnb</option>
						<option value='inventory_lnb' ".($text_div == "inventory_lnb" ? "selected":"").">WMS/구매</option>
						<option value='campaign_lnb' ".($text_div == "campaign_lnb" ? "selected":"").">메일링/SMS</option>
						<option value='work_lnb' ".($text_div == "work_lnb" ? "selected":"").">업무관리</option>
					</select>
				</td>
			</tr>
			</table>
		</div>
	    </td>
	</tr>
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
		<col style='width:100px;'>
		<col style='width:100px;'>
		<col style='width:200px;'>
		<col style='width:*;'>
		<col style='width:100px;'>
		<col style='width:140px;'>
		<col style='width:140px;'>
	  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td class='s_td'> 구분</td>
		<td class='m_td'> 언어구분</td>
	    <td class='m_td'> 한글번역</td>
		<td class='m_td' style='width:*;'> 번역문구</td>
	    <td class='m_td'> 사용유무</td>
	    <td class='m_td'> 등록일자</td>
	    <td class='e_td'> 관리</td>
	  </tr>";
$db = new Database;

$where = "where language_ix is not null " ;
if($text_div){
	$where .= " and text_div = '$text_div' ";
}

if($language_type){
	$where .= " and language_type = '$language_type' ";
}

if($search_text != ""){
	$where .= " and (text_div LIKE '%".$search_text."%' or text_korea LIKE '%".$search_text."%' or text_trans LIKE '%".$search_text."%' )";
}

$db->query("SELECT * FROM admin_language $where order by regdate desc limit 0, 200");


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
		    <td class='list_box_td list_bg_gray'>".$db->dt[text_div]."</td>
			<td class='list_box_td '>".$db->dt[language_type]."</td>
		    <td class='list_box_td point'>".$db->dt[text_korea]."</td>
			<td class='list_box_td' style='padding-left:10px; text-align:left;'>".$db->dt[text_trans]."</td>
		    <td class='list_box_td list_bg_gray'>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td class='list_box_td '>".$db->dt[regdate]."</td>
		    <td class='list_box_td list_bg_gray'>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents02 .= "<a href=\"?text_div=".$_GET["text_div"]."&language_type=".$_GET["language_type"]."&search_text=".$_GET["search_text"]."&language_ix=".$db->dt[language_ix]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
				//$Contents02 .= "<a href=\"javascript:updateLanguageInfo('".$db->dt[language_ix]."','".$db->dt[language_type]."','".$db->dt[text_div]."','".$db->dt[text_korea]."','".$db->dt[text_english]."','".$db->dt[disp]."')\"><img src='../image/btc_modify.gif' border=0></a>";
			}else{
				$Contents02 .= "<a href=\"javascript:alert('수정권한이 없습니다.');\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents02 .= "<a href=\"javascript:deleteLangaugeInfo('delete','".$db->dt[language_ix]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}else{
				$Contents02 .= "<a href=\"javascript:alert('삭제권한이 없습니다.')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}
			$Contents02 .= "
		    </td>
		  </tr> ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=7>등록된 랭귀지 목록이 없습니다. </td>
		  </tr> ";
}
$Contents02 .= "</table>";


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
$Contents = $Contents."<form name='bank_form' action='language.act.php' method='post' onsubmit='return CheckFormValue(this)' style='display:inline;'><input name='act' type='hidden' value='".$act."'><input name='language_ix' type='hidden' value='$language_ix'>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";

 $Script = "
 <script language='javascript'>
 function updateLanguageInfo(language_ix,language_type,text_div,text_korea, text_english ,disp){
 	var frm = document.bank_form;

 	frm.act.value = 'update';
 	frm.language_ix.value = language_ix;
 	frm.language_type.value = language_type;
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

 function deleteLangaugeInfo(act, language_ix){
 	if(confirm('해당랭귀지 목록을 정말로 삭제하시겠습니까?')){
 		var frm = document.bank_form;
 		frm.act.value = act;
 		frm.language_ix.value = language_ix;
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
$P->Navigation = "상점관리 > 다국어지원 > 랭귀지팩관리";
$P->title = "랭귀지팩관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();



/*

create table admin_language (
language_ix int(4) unsigned not null auto_increment  ,
text_div varchar(255) null default null,
text_name varchar(255) null default null,
text_korea varchar(255) null default null,
text_english varchar(255) null default null,
disp char(1) default '1' ,
regdate datetime not null,
primary key(language_ix));

*/
?>