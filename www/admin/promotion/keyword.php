<?
include("../class/layout.class");

$db = new Database;

$db->query("SELECT * FROM shop_search_keyword  where k_ix = '$k_ix' ");
$db->fetch();

if($db->total){
	$act = "update";
}else{
	$act = "insert";
}

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	  <tr >
		<td align='left' > ".GetTitleNavigation("키워드 등록/수정", "프로모션(마케팅) > 키워드 등록/수정 ")."</td>
	  </tr>
	  <tr >
		<td align='left' style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px; vertical-align:top;'><img src='../image/title_head.gif' align=absmiddle> <b>키워드 추가하기</b></div>")."</td>
	  </tr>
	</table>";
$Contents01 .= "
	<table width='100%' cellpadding=3 cellspacing=0 border='0'  class='input_table_box'>
		<col width='25%' />
		<col width='*' />
	  <tr bgcolor=#ffffff align='left'>
	    <td class='input_box_title'> <b>키워드 <img src='".$required3_path."'></b></td>
	    <td class='input_box_item'><input type='text' class='textbox' name='keyword' id='keyword' value='".$db->dt[keyword]."' validation=true title='키워드'placeholder='국문을 입력해 주세요'>
            <input type='text' class='textbox' name='keyword_global' id='keyword_global' value='".$db->dt[keyword_global]."' validation=true title='키워드' placeholder='영문을 입력해 주세요'>
	     <span class=small> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span> </td>
	  </tr>
	  <tr bgcolor=#ffffff align='left'>
	    <td class='input_box_title'> <b>검색횟수 </b><span style='padding-left:2px' class='helpcloud' help_width='340' help_height='30' help_html='관리자가 원하는 키워드에 대하여 보다 상위에 노출 시키기 위하여 초기에 검색횟수를 입력할 수 있습니다.'><img src='/admin/images/icon_q.gif' /></span>&nbsp;<span><img src='".$required3_path."'></span></td>
	    <td class='input_box_item'>
	    		<input type=text class='textbox' name='searchcnt' value='".$db->dt[searchcnt]."' style='width:230px;' validation=true title='검색횟수'>
	    		<span class=small></span>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff align='left'>
	    <td class='input_box_title'> <b>웹 검색횟수 <img src='".$required3_path."'></b></td>
	    <td class='input_box_item'>
	    		<input type=text class='textbox' name='searchcnt_web' value='".$db->dt[searchcnt_web]."' style='width:230px;' validation=true title='웹 검색횟수'>
	    		<span class=small></span>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff align='left'>
	    <td class='input_box_title'> <b>모바일 검색횟수 <img src='".$required3_path."'></b></td>
	    <td class='input_box_item'>
	    		<input type=text class='textbox' name='searchcnt_mobile' value='".$db->dt[searchcnt_mobile]."' style='width:230px;' validation=true title='모바일 검색횟수'>
	    		<span class=small></span>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff align='left'>
	    <td class='input_box_title'> <b>추천검색어 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'>
	    	<input type=radio name='recommend' value='1' id='recommend_1' ".($db->dt[recommend] ? "checked":"")."><label for='recommend_1'>추천검색어</label>
	    	<input type=radio name='recommend' value='0' id='recommend_0' ".(!$db->dt[recommend] ? "checked":"")."><label for='recommend_0'>인기검색어</label>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff align='left'>
	    <td class='input_box_title'> <b>사용유무 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'>
	    	<input type=radio name='disp' value='1' id='disp_1' ".($db->dt[disp] ? "checked":"")."><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' value='0' id='disp_0' ".(!$db->dt[disp] ? "checked":"")."><label for='disp_0'>사용하지않음</label>
	    </td>
	  </tr>
	  </table>";



if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
}


$Contents = "<form name='keyword_form' action='keyword.act.php' method='post' onsubmit='return CheckFormValue(this)' target='act'><input name='act' type='hidden' value='$act'><input name='k_ix' type='hidden' value='$k_ix'>";
$Contents = $Contents."<table width='100%' border=0 >";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";


$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";


$Contents = $Contents."</table >";
$Contents = $Contents."</form>";
/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록하시고자 하는 키워드 정보를 입력하신 후 저장 버튼을 누르시면 검색어가 추가 됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >검색횟수는 임의로 조정 하실 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >추천검색어를 클릭하시면 프론트 추천검색어 영역에 노출 되게 됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >검색어 자동완성이나 인기검색어, 추천검색어 영역에 노출을 원하지 않으시면 사용하지 않음으로 설정하시면 됩니다</td></tr>
</table>
";
*/

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$help_text = HelpBox("키워드 등록/수정", $help_text);
$Contents .= $help_text;

 $Script = "
 <script language='javascript'>

 </script>
 ";


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = promotion_menu();
$P->Navigation = "프로모션(마케팅) > 쇼핑몰 환경설정 > 키워드 등록/수정";
$P->title = "키워드 등록/수정";
$P->strContents = $Contents;
echo $P->PrintLayOut();



/*

create table ".TBL_SHOP_BANKINFO." (
bank_ix int(4) unsigned not null auto_increment  ,
bank_name varchar(20) null default null,
bank_number varchar(20) null default null,
bank_owner varchar(20) null default null,
disp char(1) default '1' ,

regdate datetime not null,
primary key(bank_ix));

alter table shop_search_keyword change key_word keyword varchar(100) ;
alter table shop_search_keyword add searchcnt int(8) default 0 after ref;
alter table shop_search_keyword add disp enum('1','0') default '1' after ref;
alter table shop_search_keyword add recommend enum('1','0') default '0' after searchcnt;
alter table shop_search_keyword change writedate regdate datetime;
alter table shop_search_keyword change uid k_ix int(11) unsigned auto_increment;
*/
?>