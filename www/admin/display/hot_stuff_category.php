<? 
include("../class/layout.class");

$db = new Database;

if($div_ix){
	$db->query("SELECT * FROM shop_recommend_div where div_ix = '$div_ix' ");
	$db->fetch();
	
	$act = "update";
}else{
	$act = "insert";
}


$Contents01 = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6 style='padding-bottom:10px;'> ".GetTitleNavigation("메인추천상품 분류 관리", "마케팅지원 > 메인추천상품 분류 관리 ")."</td>
	  </tr>	  
	  <tr>
	    <td align='left' colspan=4> ".colorCirCleBox("#efefef","100%","<div style='padding:5 5 5 10;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u> 분류 추가</b></div>")."</td>
	  </tr>
	  <!--tr bgcolor=#ffffff >
	    <td><img src='../image/ico_dot2.gif' align=absmiddle>  분류타입 : </td>
	    <td>
	    	<input type=radio name='div_depth' value='1' id='div_depth_1' onclick=\"document.getElementById('parent_div_ix').disabled=true;\" checked><label for='div_depth_1'>1차분류</label>
	    	<input type=radio name='div_depth' value='2' id='div_depth_2' onclick=\"document.getElementById('parent_div_ix').disabled=false;\" ><label for='div_depth_2'>2차분류</label>
	    	".getFirstDIV($bm_ix)." <span class='small'>2차 분류 등록하기 위해서는 반드시 1차분류를 선택하셔야 합니다.</span>
	    </td>
	    <td align=left colspan=2></td>
	  </tr>
	  <tr height=1><td colspan=4 background='../image/dot.gif'></td></tr-->	 
	  <tr bgcolor=#ffffff >
	    <td><img src='../image/ico_dot2.gif' align=absmiddle> 분류명 : </td><td><input type=text class='textbox' name='div_name' value='".$db->dt[div_name]."' style='width:230px;'> <span class=small></span></td>
	    <td align=left colspan=2></td>
	  </tr>
	  <tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>	  	
	  <tr bgcolor=#ffffff >
	    <td><img src='../image/ico_dot2.gif' align=absmiddle> 사용유무 : </td>
	    <td>
	    	<input type=radio name='disp' value='1' ".(($db->dt[disp] == "" || $db->dt[disp] == 1) ? "checked":"" )."><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' value='0' ".(($db->dt[disp] == 0) ? "checked":"" )."><label for='disp_0'>사용하지않음</label>
	    </td>
	    <td align=left colspan=2></td>
	  </tr>
	  <tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>	 
	  </table>";
	  
$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small>
		  <u>$board_name </u> 에 이용할 분류명을  입력해주세요
	</td>
</tr>
</table>
";



$Contents02 = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'>	  
	  <tr>
	    <td align='left' colspan=6> ".colorCirCleBox("#efefef","100%","<div style='padding:5 5 5 10;'><img src='../image/title_head.gif' align=absmiddle> <b><u>$board_name</u>  분류 목록</b></div>")."</td>
	  </tr>
	  <tr height=10><td colspan=6 ></td></tr>		  	  
	  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td style='width:150px;'> 분류명</td>
	    <td style='width:150px;'> 사용유무</td>
	    <td style='width:150px;'> 등록일자</td>
	    <td style='width:150px;'> 관리</td>
	  </tr>";





$sql = 	"SELECT *
		FROM shop_recommend_div 
		";


//echo $sql;
$db->query($sql);


if($db->total){
	for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>	    
		    <td align=left ><span style='width:".(30*$db->dt[div_depth])."px;'></span>".$db->dt[div_name]."</td>
		    <td>".($db->dt[disp] == "1" ?  "사용":"사용하지않음")."</td>
		    <td>".$db->dt[regdate]."</td>
		    <td>
		    	<!--a href=\"javascript:updateBankInfo('".$db->dt[div_ix]."','".$db->dt[div_name]."','".$db->dt[div_bbs_cnt]."','".$db->dt[disp]."')\"><img src='../image/btc_modify.gif' border=0></a-->
		    	<a href=\"?div_ix=".$db->dt[div_ix]."\"><img src='../image/btc_modify.gif' border=0></a>  
		    	
	    		<a href=\"javascript:deleteBankInfo('delete','".$db->dt[div_ix]."')\"><img src='../image/btc_del.gif' border=0></a>
		    </td>
		  </tr>
		  <tr height=1><td colspan=6 background='../image/dot.gif'></td></tr>	  ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=6>등록된 분류가 없습니다. </td>
		  </tr>
		  <tr height=1><td colspan=6 background='../image/dot.gif'></td></tr>	  ";
}
$Contents02 .= "	  
	  <!--tr height=1><td colspan=6 ><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small>사용을 원하시는 PG 모듈을 선택해 주세요</span></td></tr-->	  
	  
	  </table>";

	  
$ContentsDesc02 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td align=left style='padding:10px;'>
		<img src='../image/emo_3_15.gif' align=absmiddle>  거래은행 및 계좌 정보는 결제시 이용됩니다. 정확하게 입력해주세요
	</td>
</tr>
</table>
";	  


$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../image/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
	  
	  
$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='div_form' action='hot_stuff_category.act.php' method='post' onsubmit='return validate(document.edit_form)'><input name='mmode' type='hidden' value='$mmode'>
						<input name='act' type='hidden' value='$act'>						
						<input name='div_ix' type='hidden' value='$div_ix'>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";

$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>		
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>분류명 수정</u>을 원하실 경우는 수정버튼을 클릭하시면 상단에 해당정보가 표시되고 수정하시고자 하는 정보를 정정하신후 저장버튼을 클릭하시면 됩니다</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >분류의 노출을 원하지 않으시면 사용 안함으로 설정 하시면 됩니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사용안함으로 설정한 분류는 프로모션 상품 관리에서 또한 노출 되지 않는다.</td></tr>
	</table>
	";

	
	$help_text = HelpBox("프로모션 분류 관리", $help_text);				
$Contents = $Contents.$help_text;	

 $Script = "
 <script language='javascript'>
 function updateBankInfo(div_ix,div_name,disp){
 	var frm = document.div_form;
 	
 	frm.act.value = 'update';
 	frm.div_ix.value = div_ix;
 	frm.div_name.value = div_name;
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}
 
}
 
 function deleteBankInfo(act, div_ix){
 	if(confirm(language_data['hot_stuff_category.php']['A'][language])){//'해당카테고리  정보를 정말로 삭제하시겠습니까?'
 		var frm = document.div_form; 	
 		frm.act.value = act;
 		frm.div_ix.value = div_ix;
 		frm.submit();
 	}	
}
 </script>
 ";
	
if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->Navigation = "HOME > 마케팅지원 > 프로모션 분류 관리";
	$P->NaviTitle = "게시판관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();	
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->Navigation = "HOME > 마케팅지원 > 프로모션 분류 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

function getFirstDIV($bm_ix, $selected=""){
	$mdb = new Database;
	
	$sql = 	"SELECT *
			FROM shop_promotion_div
			where disp=1 ";
	
	$mdb->query($sql);
	
	$mstring = "<select name='parent_div_ix' id='parent_div_ix' disabled>";
	$mstring .= "<option value=''>1차분류</option>";
	if($mdb->total){
		
		
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[div_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[div_ix]."' selected>".$mdb->dt[div_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[div_ix]."'>".$mdb->dt[div_name]."</option>";
			}
		}
		
	}	
	$mstring .= "</select>";
	
	return $mstring;
}

/*

create table bbs_manage_div (
div_ix int(4) unsigned not null auto_increment  ,
div_name varchar(20) null default null,
disp char(1) default '1' ,
regdate datetime not null, 
primary key(div_ix));
*/
?>