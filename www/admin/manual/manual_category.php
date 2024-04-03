<?
include("../class/layout.class");
include("../webedit/webedit.lib.php");


$db = new Database;




$Contents = "
		<table cellpadding=0 cellspacing=0 border=0 width='100%'>
		<tr>
		    <td align='left' colspan=4 style='padding-bottom:20px;'> ".GetTitleNavigation("메뉴얼", "메뉴얼관리 > 메뉴얼관리")."</td>
		</tr>		
		<tr>
			<td width=300 valign=top>			
			<div id=TREE_BAR >
				<table cellpadding=0 cellspacing=0 border=0 bgcolor=#e2e2e2 style='border:3px solid #d8d8d8'>
				
				<tr><form>
					<td width=200 height=400 valign=top style='overflow:auto;padding:0 10 10 10;'>
					<div style=\"width:200px;height:420px;padding:5px;overflow:auto;margin:1;background-color:#ffffff\" id='tree'>
					
					</div>
					</td>
				</tr></form>
				</table>
			</div>
			</td >
			<td style='padding:20px;'>
			
			</td>
			<td valign=top width='100%'>
				<div id='edit_category' style='display:block;'>
				<form name='thisCategoryform' method=\"post\" enctype='multipart/form-data' action='manual_category.act.php' >
				<input type='hidden' name='mode' value='insert'>
				<input type='hidden' name='depth' value=''>
				<input type='hidden' name='plevel1' id='plevel1' value=''>
				<input type='hidden' name='plevel2' id='plevel2' value=''>
				<input type='hidden' name='plevel3' id='plevel3' value=''>
				<input type='hidden' name='category_top_view' value=''>
				<table width='100%' border=0 cellpadding=0 cellspacing=0>						
					<tr height=30 bgcolor='#F8F9FA'><td width=100% style='padding-left:5px'><img src='/admin/images/dot_org.gif' align=absmiddle> <b>메뉴얼 추가하기 </b><br></td></tr>
				</table>
				<table cellpadding=0 cellspacing=1  bgcolor=#c0c0c0 width=100%>
				<tr bgcolor=#ffffff>
					<td class='leftmenu' width=170 nowrap oncontextmenu='init2();return false;'> <img src='../image/title_head.gif' >	 선택된 페이지</td>
					<td width=520 style='padding-left:10px;' nowrap>
					<span id='this_category' style='font-weight:bold'></span> 
					</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='leftmenu' width=170 nowrap oncontextmenu='init2();return false;'> <img src='../image/title_head.gif' >	 페이지명</td>
					<td width=520 style='padding-left:10px;' nowrap>
					<input type='text' name='page_name' maxlength=40 id='page_name' validation=true title='페이지명'> 
					</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='leftmenu' width=170 nowrap oncontextmenu='init2();return false;'> <img src='../image/title_head.gif' >	 노출 솔루션타입</td>
					<td width=520 style='padding-left:10px;' nowrap>
					<input type='radio' name='page_auth' maxlength=40 id='page_auth' value='1' checked> 임대형
					<input type='radio' name='page_auth' maxlength=40 id='page_auth' value='2'> 독립형
					<input type='radio' name='page_auth' maxlength=40 id='page_auth' value='3'> 입점형
					</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='leftmenu' width=170 nowrap oncontextmenu='init2();return false;'> <img src='../image/title_head.gif'> 간략설명</td>
					<td width=520 style='padding-left:10px;' nowrap>
					<textarea name='shot_text' style='width:100%;height:50px'></textarea>					
					</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td colspan=2 style='padding-bottom:0px;'>".WebEdit()."</td>
				</tr>
				</table>
				<table cellpadding=5 cellspacing=1 width=100% >	
				<tr bgcolor=#ffffff>
					<td colspan=2 align=right valign=top style='padding:0px;padding-right:20px;'>
					<a href='javascript:doToggleText();' onMouseOver=\"MM_swapImage('editImage15','','../webedit/image/bt_html_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_html.gif' name='editImage15' width='93' height='23' border='0' id='editImage15'></a>
          			      <a href='javascript:doToggleHtml();' onMouseOver=\"MM_swapImage('editImage16','','../webedit/image/bt_source_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_source.gif' name='editImage16' width='93' height='23' border='0' id='editImage16'></a>
					</td>
				</tr>
				
				<tr bgcolor=#ffffff>
					<td colspan=2 align=right> <input type='checkbox' name='sub_cartegory_delete' id='sub_cartegory_delete_id'value='1' > <label for='sub_cartegory_delete_id'>하부카테고리 모두삭제</label> <img src='../image/bt_category_del.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"CategorySave(document.thisCategoryform,'del');\"> <input type=image src='../image/bt_category_modify.gif' border=0 align=absmiddle style='cursor:hand'> </td>
				</tr>
				</table>
				</form>
				<!--div style='display:none' id='category_top_view_area'></div-->
				</div>
				
				
			</td>
			<td style='padding-left:10px;'>
			</td>
		</tr>
		";
		
	
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >카테고리 디자인을 텍스트 또는 이미지로 하실수 있습니다. 이미지 등록후 예시와 같이 치환코드를 변경해주시면 됩니다 <span class=small>예) text 사용시 : {categorys.cname} , image 사용시 : {categorys.leftcatimg}</span></td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >상단에 <label for='category_mode_add1' style='font-weight:bold'> 카테고리 추가</label> 를 선택하신후 좌측 상품 카테고리에서 추가하기 원하는 카테고리를  선택하신후  <b>카테고리 추가</b> 입력란에 카테고리를 입력하신후 <img src='../image/bt_category_add.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>카테고리 수정</b> : 상단에 <label for='category_mode_add1' style='font-weight:bold'> 선택된 카테고리 수정 </label> 를 선택하신후 좌측 상품 카테고리에서 추가하기 원하는 카테고리를  선택하신후  <img src='../image/bt_category_del.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>카테고리 삭제</b> : 상단에 <label for='category_mode_add1' style='font-weight:bold'> 선택된 카테고리 수정 </label> 를 선택하신후 좌측 상품 카테고리에서 추가하기 원하는 카테고리를  선택하신후  <img src='../image/bt_category_del.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' > 하부카테고리를 포함해서 삭제하고 싶으신경우에는 <input type='checkbox' name='sub_cartegory_delete1' id='sub_cartegory_delete_id1'value='1' > <label for='sub_cartegory_delete_id1' style='font-weight:bold'>하부카테고리 모두삭제</label> 를 선택한후 <img src='../image/bt_category_del.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.</td></tr>
</table>
";



$Contents .= "				
		<tr height=10>
			<td colspan=3>
			".HelpBox("카테고리 관리 ", $help_text)."
			</td>
		</tr>
		</table>		
		<iframe name='calcufrm' src='' width=0 height=0></iframe>";


if($view == "innerview"){
	$LO = new popLayOut;
	$addScript = "
	<script language='JavaScript' src='../webedit/webedit.js'></script>
	<script language='JavaScript' src='../include/manager.js'></script>
	<script language='JavaScript' src='../include/cTree.js'></script>
	<script language='JavaScript' src='category.js'></script>
	<script src='../include/rightmenu.js'></script>\n".$Script;
	$LO->addScript = $addScript; /**/
	$LO->OnloadFunction = "";
	$LO->title = "";//text_button('#', " ♣ 카테고리 구성");
	$LO->strLeftMenu = "";
	$Contents ="
		<div id='category_area'>
			<table cellpadding=0 cellspacing=0 border=0 bgcolor=#e2e2e2 style='border:3px solid #d8d8d8'>
				<tr><form name='category_order' method='get' action='categoryorder.php' target='calcufrm'>
					<td style='padding-left:10px;padding-top:5px;padding-bottom:3px;' valign=middle>				
					<input type='hidden' name='this_depth' value=''>
					<input type='hidden' name='cid' value=''>
					<input type='hidden' name='mode' value=''>
					<input type='hidden' name='view' value='innerview'>
					<img src='../image/t.gif' onclick='order_up(document.category_order)' style='cursor:hand' alt='분류 위로 이동' align=absmiddle>
					<img src='../image/b.gif' onclick='order_down(document.category_order)' style='cursor:hand' alt='분류 아래로 이동' align=absmiddle>	
					<span class=small>카테고리선택후 이동버튼 클릭</span>		
					</td>
				</tr></form>
				<tr><form>
					<td width=200 height=400 valign=top style='overflow:auto;padding:0 10 10 10;'>
					<div style=\"width:200px;height:420px;padding:5px;overflow:auto;margin:1;background-color:#ffffff\" >
					$category
					</div>
					</td>
				</tr></form>
				</table>
		</div>
		
		<script>alert(document.getElementById('category_area').innerHTML);parent.document.getElementById('TREE_BAR').innerHTML = document.getElementById('category_area').innerHTML;</script>";
		
	$LO->strContents = $Contents;
	$LO->Navigation = "";
	$LO->PrintLayOut();
	exit;
}else{

$LO = new LayOut;
$addScript = "<script language='JavaScript' src='../webedit/webedit.js'></script><script language='JavaScript' src='../js/jquery-1.4.js'></script>\n
	<script src='ui.core.js' type='text/javascript'></script>\n
    <script src='jquery.cookie.js' type='text/javascript'></script>\n
<script src='jquery.dynatree.js' type='text/javascript'></script>\n
<link href='ui.dynatree.css' rel='stylesheet' type='text/css'>\n
<script language='JavaScript' src='manual_category.js'></script>\n".$Script;
$LO->addScript = $addScript; /**/
$LO->OnloadFunction = "Init(document.thisCategoryform);MM_preloadImages('../webedit/image/wtool1_1.gif','../webedit/image/wtool2_1.gif','../webedit/image/wtool3_1.gif','../webedit/image/wtool4_1.gif','../webedit/image/wtool5_1.gif','../webedit/image/wtool6_1.gif','../webedit/image/wtool7_1.gif','../webedit/image/wtool8_1.gif','../webedit/image/wtool9_1.gif','../webedit/image/wtool11_1.gif','../webedit/image/wtool13_1.gif','../webedit/image/wtool10_1.gif','../webedit/image/wtool12_1.gif','../webedit/image/wtool14_1.gif','../webedit/image/bt_html_1.gif','../webedit/image/bt_source_1.gif')"; //showSubMenuLayer('storeleft');
$LO->title = "";//text_button('#', " ♣ 카테고리 구성");
$LO->strLeftMenu = product_menu();

$LO->prototype_use = false;
$LO->strContents = $Contents;
$LO->Navigation = "HOME > 상품관리 > 상품분류관리";
$LO->PrintLayOut();
}

?>