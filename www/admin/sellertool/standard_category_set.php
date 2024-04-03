<?
include("../class/layout.class");
include_once("sellertool.lib.php");
//include_once("../product/goods_input.lib.php");

$db = new Database;

 
$category = "
<script  id='dynamic'></script>
<script>

/*	 Create Tree		*/
	var tree = new Tree();
	tree.color = \"black\";
	tree.bgColor = \"white\";
	tree.borderWidth = 0;


/*	Create Root node	
	setCategory(cname,cid,depth,category_use,category_code)
*/
	var rootnode = new TreeNode(\"표준상품분류\", \"../resources/ServerMag_Etc_Root.gif\",\"../resources/ServerMag_Etc_Root.gif\");
	rootnode.action = \"setCategory('표준상품분류','000000000000000',-1,'','')\";
	rootnode.expanded = true;
";

$tb = $_SESSION['admin_config']["mall_inventory_category_div"]=="P"	?	TBL_SHOP_CATEGORY_INFO:"standard_category_info";

$db->query("SELECT * FROM standard_category_info where depth in(0,1,2,3,4)  order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");

$total = $db->total;
for ($i = 0; $i < $db->total; $i++)
{

	$db->fetch($i);

	if ($db->dt["depth"] == 0){
		$category = $category.PrintNode($db);
	}else if($db->dt["depth"] == 1){
		$category = $category.PrintGroupNode($db);
	}else if($db->dt["depth"] == 2){
		$category = $category.PrintGroupNode($db);
	}else if($db->dt["depth"] == 3){
		$category = $category.PrintGroupNode($db);
	}else if($db->dt["depth"] == 4){
		$category = $category.PrintGroupNode($db);
	}else if($db->dt["depth"] == 5){
		$category = $category.PrintGroupNode($db);
	}
}

$category = $category."
	tree.addNode(rootnode);
	tree.draw();
	tree.nodes[0].select();
	
</script>

";


$Contents = "<script language='JavaScript' src='../include/manager.js'></script>
<script language='JavaScript' src='../include/Tree.js'></script>

		<table cellpadding=0 cellspacing=0 border=0 width='100%'>
		<tr>
		    <td align='left' colspan=3> ".GetTitleNavigation("제휴처별 표준카테고리별 부가정보설정", "제휴사 연동 > 제휴처별 표준카테고리별 부가정보설정")."</td>
		</tr>
		<tr>
			<td valign=top width='100%' align='left' style=''>
				<div class='tab' style='width:100%;height:38px;margin:0px;'>
					<table class='s_org_tab' cellpadding='0' cellspacing='0' border='0'>
					<tr>
						<td class='tab'>
							<!--table id='tab_01' class='on' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('edit_category','tab_01')\" style='padding-left:20px;padding-right:20px;'>
									분류 수정
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('add_subcategory','tab_02')\" style='padding-left:20px;padding-right:20px;'>
									분류 추가
								</td>
								<th class='box_03'></th>
							</tr>
							</table-->
							
							<table id='tab_05'  ".(($list_type == "" || $list_type == "reg") ? "class='on' ":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?list_type=reg'\" >분류별 부가정보 필드 정의</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_04' ".($list_type == "list" ? "class='on' ":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?list_type=list'\" style='padding-left:20px;padding-right:20px;'>
									분류별 부가정보 필드 리스트 
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
						<td class='btn' style='vertical-align:bottom;padding-bottom:3px;'>";
							if($_SESSION['admin_config']["mall_inventory_category_div"]=="P"){
								$Contents .= "<div class='red'>* 표준상품분류를 상품분류와 동일하게 사용하시기 때문에 수정및추가시 상품카테고리와 같이 반영이 됩니다.</div> ";
							}
						$Contents .= "
						</td>
					</tr>
					</table>
				</div>";
if($list_type == "" || $list_type == "reg"){ 
$Contents .= "
			<table cellpadding=0 cellspacing=0 width=100% >
			<tr>
				<td width='15%' align='left'  valign='top'>
					<table cellpadding=0 cellspacing=0 width=100% class='input_table_box'>
					<tr>
						<td valign=top width=236>
							<div id=TREE_BAR >
								<table cellpadding=0 cellspacing=0 border=0 bgcolor=#e2e2e2 width=100% style='border:3px solid #d8d8d8'>
								<tr>
									<td style='padding-left:10px;padding-top:5px;padding-bottom:3px;' valign=middle nowrap>
										<form name='category_order' method='get' action='standard_category.order.php' target='iframe_act'>
										<input type='hidden' name='this_depth' value=''>
										<input type='hidden' name='cid' value=''>
										<input type='hidden' name='mode' value=''>
										<input type='hidden' name='view' value=''><!--innerview-->
										<img src='../image/t.gif' onclick='order_up(document.category_order)' style='cursor:hand' alt='분류 위로 이동' align=absmiddle>
										<img src='../image/b.gif' onclick='order_down(document.category_order)' style='cur sor:hand' alt='분류 아래로 이동' align=absmiddle>
										</td>
										<td width=190 valign=middle>
										<span class=small><!--분류선택후 이동버튼 클릭--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
										</form>
									</td>
								</tr>
								<tr><form>
									<td colspan=2 width=200 height=400 valign=top style='overflow:auto;padding:0 10px 10px 10px;'>
									<div style=\"width:200px;height:418px;padding:5px;overflow:auto;margin:1;background-color:#ffffff\" >
									$category
									</div>
									</td>
								</tr></form>
								</table>
							</div>
						</td >
					</tr>
					</table>
				</td>
				<td style='padding-left:13px;'></td>
				<td width='82%' align='right' valign='top'>";
  


$Contents .= "
				<div class='doong'  id='input_addfield' style='display:block;'>
					<form name='add_field' method=\"post\" enctype='multipart/form-data' action='standard_category.save.php' onsubmit='return SaveAddField(this);' target='calcufrm'>
					<input type='hidden' name='mode' value='starndard_category_add_field'>
					<input type='hidden' name='cid' value='' >
					<input type='hidden' name='sub_mode' value='starndard_category_add_field'>
					<table cellpadding=5 cellspacing=0 border=0 width='97%' class='input_table_box'>
						<col width=15%>
						<col width=75%>
						<tr height=40>
							<td class='input_box_title'  nowrap><b>선택된 분류</b> </td>
							<td class='input_box_item' id='selected_category_1' style='font-weight:bold;' class='small'>
							미선택 --> 왼쪽분류에서 추가하시고자 하는 분류를 선택해주세요
							</td>
						</tr>
						<tr>
        					<td class='input_box_title'>제휴사 선택</td>
        					<td class='input_box_item' >
        						<table border=0 cellpadding=0 cellspacing=0>
        							<tr>
        								<td style='padding-right:5px;'>
                                            ".getSellerToolSiteInfo($site_code , "validation=true title='제휴사' onchange='changeSellerTool()' " ,"selectbox" ,"AND use_mapping_div like '%|I|%'")."
                                        </td>
        							</tr>
        						</table>
        					</td>
        				</tr>
					</table><br><br>
					<table cellpadding=5 cellspacing=0 border=0 width='97%' class='input_table_box' id='sellertool_add_field_table'>
						<col width=100>
						<col width=50>
						<col width=160>
						<col width=160>
						<col width=100>
						<col width=*>
						<tr bgcolor=#efefef style='text-align:center;'>
							<td class='input_box_title' nowrap>구분</td>
							<td class='input_box_title' nowrap>주요<br>정보</td>
							<td class='input_box_title' nowrap>영문명 </td>
							<td class='input_box_title' >필드이름 </td>
							<td class='input_box_title' nowrap>필드타입</td>
							<td class='input_box_title' nowrap>
								필드기본값 예) 1024*768|1280*800|1600*1200
							</td>
						</tr>";
						for($i=0;$i < 10 ; $i++){
$Contents .= "
						<tr class='field_rows'>
							<td class='input_box_title' >기타정보 ".($i+1)."</td>
							<td class='input_box_item' ><input type='checkbox' name='etc".($i+1)."_search' value='1'>  </td>
							<td class='input_box_item' ><input type='text' class='field_ename' name='etc".($i+1)."_ename' id='etc".($i+1)."_ename' value=''>  </td>
							<td class='input_box_item' ><input type='text' class='field_name' name='etc".($i+1)."'  id='etc".($i+1)."' value=''>  </td>
							<td class='input_box_item' nowrap>
								<select name='etc".($i+1)."_type' id='etc".($i+1)."_type'>
									<option value='text'>텍스트박스</option>
									<option value='radio'>라디오버튼</option>
									<option value='checkbox'>체크박스</option>
									<option value='select'>셀렉트박스</option>
								</select>
							</td>
							<td class='input_box_item'  nowrap>
								<input type='text' class='field_value' name='etc".($i+1)."_value' size=50 value=''>
							</td>
						</tr>";
						}

$Contents .= "
						 
					</table>
					<table cellpadding=5 cellspacing=0 border=0 width='97%' >
						<tr>
							<td colspan=2 align=center style='padding:15px;'> <input type=image src='../image/b_save.gif' border=0 align=absmiddle ></td>
						</tr>
					</table>
				</form>
				</div>";


$Contents .= "
			</td>
		</tr>
		</table>";
}else{
$Contents .= "<form name='search_form' method='get' action='' onsubmit='return CheckFormValue(this);' style='display:inline;'>
<input type=hidden name='list_type' value='".$list_type."'>
        <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >        	
                <tr>
					<td>
                    <table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='input_table_box'>
        				<col width='20%' />
                        <col width='30%' />
						<col width='20%' />
                        <col width='30%' />
        				<tr>
        					<td class='input_box_title'>제휴사 선택</td>
        					<td class='input_box_item' colspan=3>
        						<table border=0 cellpadding=0 cellspacing=0>
        							<tr>
        								<td style='padding-right:5px;'>
                                            ".getSellerToolSiteInfo($site_code , "" ,"selectbox" ," ")."
                                        </td>
        							</tr>
        						</table>
        					</td>
        				</tr>
                        <tr>
        					<td class='input_box_title'>  검색어  </td>
        					<td class='input_box_item'  colspan=3>
        						<table cellpadding=2 cellspacing=0 width=100%>
        							<col width='20%'>
									<col width='*'>
        							<tr>
										<td>
										<select name='search_type'>
											<option value=''>통합검색</option>
											<option value='cname' ".($search_type == "cname" ? "selected":"").">카테고리명</option>
											<option value='cid' ".($search_type == "cid" ? "selected":"").">카테고리코드</option> 
											<option value='field_name' ".($search_type == "field_name" ? "selected":"").">필드명</option> 
											<option value='field_ename' ".($search_type == "field_ename" ? "selected":"").">필드 영문명</option> 
											<option value='field_type' ".($search_type == "field_type" ? "selected":"").">필드 타입</option> 
										</select>
										</td>
        								<td >
        								<INPUT id=search_texts  class='textbox' value='' style=' FONT-SIZE: 12px; WIDTH: 90%; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
        								
        								</td>
        								<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'></span></td>
        							</tr>
        						</table>
        					</td>
							 
                        </tr>
                    </table>
					</td>
                </tr>
                <tr>
	               <td colspan=2 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
                </tr>
            
        </table></form>";

$Contents .= "
<table cellpadding=5 cellspacing=0 border=0 width='100%' class='list_table_box' id='sellertool_add_field_table'>
						<col width=20%>
						<col width=10%>
						<col width=10%>
						<col width=10%>
						<col width=10%>
						<col width=*>
						<tr bgcolor=#efefef style='text-align:center;' height=30>
							<td class='s_td' nowrap>선택된 분류</td>
							<td class='m_td' nowrap>제휴사</td> 
							<td class='m_td' nowrap>영문명 </td>
							<td class='m_td' >필드이름 </td>
							<td class='m_td' nowrap>필드타입</td>
							<td class='e_td' nowrap>
								필드 기본값 
							</td>
						</tr>";

$where .= " where 1 "; 
if($search_type && $search_text){
	$where .= " and ".$search_type." LIKE '%".$search_text."%' ";
}

if($site_code){
	$where .= " and site_code = '".$site_code."' " ;
}

		$sql = "select * from sellertool_category_addfield  ".$where;
					
		$db->query($sql); //cid LIKE '".substr($cid,0,($depth)*3)."%
		if($db->total){
						//echo $sql."<br>";
						$add_fields = $db->fetchall();

						for($i=0;$i < count($add_fields);$i++){
$Contents .= "
						<tr class='field_rows' height=30>
							<td >".getStandardCategoryPathByAdmin($add_fields[$i][cid],4)."</td>
							<td align=center>".$add_fields[$i][site_code]."</td>
							<td align=center>".$add_fields[$i][field_ename]." </td>
							<td align=center>".$add_fields[$i][field_name]." </td>
							<td align=center nowrap>
								".$add_fields[$i][field_type]."
							</td>
							<td nowrap>
								".$add_fields[$i][field_value]."
							</td>
						</tr>";
						}
		}else{
$Contents .= "
						<tr class='field_rows' height=80>
							<td colspan=6 align=center>
								등록된 제휴사별 부가정보가 존재하지 않습니다.
							</td>
						</tr>";
		}

$Contents .= "
						 
					</table>";

}
$Contents .= "
	</td>
</tr>
</table>
		";

/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=middle ><img src='/admin/image/icon_list.gif' b></td><td class='small' style='line-height:120%'>분류 디자인을 텍스트 또는 이미지로 하실수 있습니다. 이미지 등록후 예시와 같이 치환코드를 변경해주시면 됩니다 <span class=small>예) text 사용시 : {categorys.cname} , image 사용시 : {categorys.leftcatimg}</span></td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' style='line-height:120%' >상단에 <label for='category_mode_add1' style='font-weight:bold'> 분류 추가</label> 를 선택하신후 좌측 품목 분류에서 추가하기 원하는 분류를  선택하신후  <b>분류 추가</b> 입력란에 분류를 입력하신후 <img src='../image/bt_category_add.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>분류 수정</b> : 상단에 <label for='category_mode_add1' style='font-weight:bold'> 선택된 분류 수정 </label> 를 선택하신후 좌측 품목 분류에서 추가하기 원하는 분류를  선택하신후  <img src='../image/bt_category_del.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>분류 삭제</b> : 상단에 <label for='category_mode_add1' style='font-weight:bold'> 선택된 분류 수정 </label> 를 선택하신후 좌측 품목 분류에서 추가하기 원하는 분류를  선택하신후  <img src='../image/bt_category_del.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' > 하부분류를 포함해서 삭제하고 싶으신경우에는 <input type='checkbox' name='sub_cartegory_delete1' id='sub_cartegory_delete_id1'value='1' > <label for='sub_cartegory_delete_id1' style='font-weight:bold'>하부분류 모두삭제</label> 를 선택한후 <img src='../image/bt_category_del.gif' border=0 align=absmiddle > 버튼을 클릭하시면 됩니다.</td></tr>
</table>
";
*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents .= "
		<table cellpadding=0 cellspacing=0 border=0 width=100%>
		<tr height=10>
			<td colspan=3>
			".HelpBox("분류 관리 ", $help_text)."
			</td>
		</tr>
		</table>
		<iframe name='calcufrm' id='calcufrm' src='' width=0 height=0></iframe>";

if($view == "innerview"){
	$P = new popLayOut;
	$addScript = " 
	<script src='../include/rightmenu.js'></script>\n".$Script;
	$P->addScript = $addScript; /**/
	$P->OnloadFunction = "";
	$P->title = "";//text_button('#', " ♣ 분류 구성");
	$P->strLeftMenu = "";
	$Contents ="
		<div id='category_area'>
			<table cellpadding=0 cellspacing=0 border=0 bgcolor=#e2e2e2 style='border:3px solid #d8d8d8'>
				<tr>
					<td style='padding-left:10px;padding-top:5px;padding-bottom:3px;' valign=middle>
						<form name='category_order' method='get' action='standard_category.order.php' target='iframe_act'>
						<input type='hidden' name='this_depth' value=''>
						<input type='hidden' name='cid' value=''>
						<input type='hidden' name='mode' value=''>
						<input type='hidden' name='view' value='innerview'>
						<img src='../image/t.gif' onclick='order_up(document.category_order)' style='cursor:hand' alt='분류 위로 이동' align=absmiddle>
						<img src='../image/b.gif' onclick='order_down(document.category_order)' style='cursor:hand' alt='분류 아래로 이동' align=absmiddle>
						<span class=small>분류선택후 이동버튼 클릭</span>
						</form>
					</td>
				</tr>
				<tr>
					<td width=200 height=400 valign=top style='overflow:auto;padding:0 10 10 10;'>
					<form>
						<div style=\"width:200px;height:420px;padding:5px;overflow:auto;margin:1;background-color:#ffffff\" >
						$category
						</div>
					</form>
					</td>
				</tr>
				</table>
		</div>

		<script>alert(document.getElementById('category_area').innerHTML);parent.document.getElementById('TREE_BAR').innerHTML = document.getElementById('category_area').innerHTML;</script>";

	$P->strContents = $Contents;
	$P->Navigation = "";
	$P->PrintLayOut();
	exit;
}else{


$addScript = " 
<script src='../include/rightmenu.js'></script>\n
<SCRIPT type='text/javascript'>
<!--
	  
	function SaveAddField(frm){
		
		if(frm.cid.value.length < 1){
			alert('추가항목 설정을 위한 카테고리를 선택해주세요');
			return false;	
		}

		if(frm.site_code.value.length < 1){
			alert('추가항목 설정을 위한 제휴처를 선택해주세요');
			return false;	
		}

		var field_data_bool = false;

		$('table[id=sellertool_add_field_table] tr[class=field_rows]').each(function(){
			//alert($(this).find('[class=field_ename]').val());
			var str = $(this).find('[class=field_ename]').val();
				str += $(this).find('[class=field_name]').val();
				str += $(this).find('[class=field_value]').val();

			if(str.length > 1){
				field_data_bool = true;
			}
		});

		if(!field_data_bool){
			alert('최소한 한개 이상의 항목이 입력해주세요');
			return false;	
		}

		return true;

	}
  
	function changeSellerTool(){
		var site_code = $('#site_code').val();
		var cid = $('form[name=add_field] input[name=cid]').val();

		if(site_code.length < 1){
			alert('추가항목 설정을 위한 제휴처를 선택해주세요');
			return false;	
		}

		if(cid.length < 1){
			alert('추가항목 설정을 위한 카테고리를 선택해주세요');
			return false;	
		}

		if(site_code && cid){
			$.ajax({
			    url : './standard_category.save.php',
			    type : 'POST',
			    data : {cid:cid,site_code:site_code,mode:'get_sellertool_add_fields'},
			    dataType: 'json',
			    error: function(data,error){// 실패시 실행함수
						alert(error);},
			    success: function(datas){
					//alert(datas);
					
					var sellertool_add_field_table = $('table#sellertool_add_field_table');

					sellertool_add_field_table.find('tr').each(function(){
						$(this).find('input[name^=etc]').val('');
					});

					$.each(datas, function(i, data){ 

						sellertool_add_field_table.find('[name=etc'+(i+1)+'_ename]').val(data.field_ename);
						sellertool_add_field_table.find('[name=etc'+(i+1)+']').val(data.field_name);
						sellertool_add_field_table.find('[name=etc'+(i+1)+'_type]').val(data.field_type);
						sellertool_add_field_table.find('[name=etc'+(i+1)+'_value]').val(data.field_value);

						if(data.field_search){
							sellertool_add_field_table.find('[name=etc'+(i+1)+'_search]').attr('checked','checked');
						}
						 
					});  

		        }
		    });
		}
	}

	function setCategory(cname,cid,depth,category_use,category_code)
	{
		
		cname = cname.replace('&quot;','\'');

		$('form[name=add_field]').find('input[name=cid]').val(cid);
		$('form[name=add_field]').find('input[name=sub_depth]').val(eval(depth+1));
		 
		var site_code = $('#site_code').val();
		//alert(site_code);
		document.getElementById('calcufrm').src='../product/standard_calcurate.php?cid='+cid+'&depth='+eval(depth+1); //eval(depth+1) 이값은 depth 가 0 이 없어서 ...
 
		if(site_code && cid){
			$.ajax({
			    url : './standard_category.save.php',
			    type : 'POST',
			    data : {cid:cid,site_code:site_code,mode:'get_sellertool_add_fields'},
			    dataType: 'json',
			    error: function(data,error){// 실패시 실행함수
						alert(error);},
			    success: function(datas){
					//alert(datas);
					
					var sellertool_add_field_table = $('table#sellertool_add_field_table');

					sellertool_add_field_table.find('tr').each(function(){
						$(this).find('input[name^=etc]').val('');
					});

					$.each(datas, function(i, data){ 

						sellertool_add_field_table.find('[name=etc'+(i+1)+'_ename]').val(data.field_ename);
						sellertool_add_field_table.find('[name=etc'+(i+1)+']').val(data.field_name);
						sellertool_add_field_table.find('[name=etc'+(i+1)+'_type]').val(data.field_type);
						sellertool_add_field_table.find('[name=etc'+(i+1)+'_value]').val(data.field_value);

						if(data.field_search){
							sellertool_add_field_table.find('[name=etc'+(i+1)+'_search]').attr('checked','checked');
						}
						 
					});  

		        }
		    });
		}
	}

//-->



</SCRIPT>
".$Script;

	if($mmode == "pop"){
		$P = new ManagePopLayOut();
		$P->addScript = $addScript;
		//$P->OnloadFunction = "initTrees();";
		$P->strLeftMenu = Stat_munu("standard_category.php");
		$P->strContents = $Contents;
		$P->Navigation = "제휴사 연동 > 제휴처별 표준카테고리별 부가정보설정";
		$P->NaviTitle = "제휴처별 표준카테고리별 부가정보설정";
		echo $P->PrintLayOut();
	}else{
		$P = new LayOut();
		
		$P->addScript = $addScript; /**/
		$P->OnloadFunction = ""; //showSubMenuLayer('storeleft'); MenuHidden(false);
		$P->title = "";//text_button('#', " ♣ 분류 구성");
		$P->strLeftMenu = sellertool_menu();


		$P->strContents = $Contents;
		$P->Navigation = "제휴사 연동 > 상품분류관리 > 제휴처별 표준카테고리별 부가정보설정";
		$P->title = "제휴처별 표준카테고리별 부가정보설정";
		$P->PrintLayOut();
	}

}

function PrintRootNode($cname){

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);

	$vPrintRootNode = "var rootnode = new TreeNode('$cname', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');/n
			rootnode.expanded = true;\n\n";

	return $vPrintRootNode;
}

function PrintNode($mdb){
	
	//$cdb = new Database;

	$cname = $mdb->dt[cname];
	$cname_on = $mdb->dt[cname_on];
	$is_adult = $mdb->dt[is_adult];
	$cid = $mdb->dt[cid];
	$depth = $mdb->dt[depth];
	$category_display_type = $mdb->dt[category_display_type];
	
	$category_code = $mdb->dt[category_code];
	$category_use = $mdb->dt[category_use];
	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);
	
	$cname_on = str_replace("\"","&quot;",$cname_on);
	$cname_on = str_replace("'","&#39;",$cname_on);

	
	return "	var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');
	node$cid.expanded = true;
	node$cid.action = \"setCategory('$cname','$cid',$depth, '$category_use','$category_code')\";
	rootnode.addNode(node$cid);\n\n";
}

function PrintGroupNode($mdb)
{

	global $cid;
	
	$cdb = new Database;
	$cname = $mdb->dt[cname];
	$cname_on = $mdb->dt[cname_on];
	$mcid = $mdb->dt[cid];
	$depth = $mdb->dt[depth];
	$category_display_type = $mdb->dt[category_display_type];
	$category_code = $mdb->dt[category_code];
	$is_adult = $mdb->dt[is_adult];
	$category_use = $mdb->dt[category_use];

	$cid1 = substr($mcid,0,3);
	$cid2 = substr($mcid,3,3);
	$cid3 = substr($mcid,6,3);
	$cid4 = substr($mcid,9,3);
	$cid5 = substr($mcid,12,3);

	$Parentdepth = $depth - 1;

	if ($depth+1 == 1){
		$cid1 = "000";
	}else if($depth+1 == 2){
		$cid2 = "000";
	}else if($depth+1 == 3){
		$cid3 = "000";
	}else if($depth+1 == 4){
		$cid4 = "000";
	}else if($depth+1 == 5){
		$cid5 = "000";
	}

	$parent_cid = "$cid1$cid2$cid3$cid4$cid5";

	if ($depth ==1){
		$ParentNodeCode = "node$parent_cid";
	}else if($depth ==2){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==3){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==4){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==5){
		$ParentNodeCode = "groupnode$parent_cid";
	}

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);

	$cname_on = str_replace("\"","&quot;",$cname_on);
	$cname_on = str_replace("'","&#39;",$cname_on);

///echo "".substr($mcid,0,($_GET["depth"]+1)*3)."==".substr($_GET["cid"],0,($_GET["depth"]+1)*3)."<br><br>";
	$mstring =  "		
	var groupnode$mcid = new TreeNode('$cname ',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);\n";
	if ($mcid == $cid || (substr($mcid,0,($depth)*3) == substr($_GET["cid"],0,($depth)*3) && $_GET["depth"] > $depth )  || (substr($mcid,0,($depth+1)*3) == substr($_GET["cid"],0,($depth+1)*3)) ){//
		$mstring .=  "	groupnode$mcid.expanded = true;\n";
	//	$mstring .=  "	groupnode$cid.select = true;\n";
	}

	$cdb->query("select * from shop_category_auth where cid = '".$mcid."' and category_access not in ('MD','DE')");
	$cdb->fetch();
	if($cdb->dt[category_access]){
		$category_access = $cdb->dt[category_access];
	}else{
		for($i=$depth;$i>=0;$i--){

			$org_cid = substr($mcid,0,3+(3*$i));
			$org_cid =$org_cid."000000000000";
			$for_cid = substr($org_cid,0,15);

			$sql = "select * from shop_category_auth where cid = '".$for_cid."'  and category_access not in ('MD','DE')";

			$cdb->query($sql);
			$cdb->fetch();
			$category_access = $cdb->dt[category_access];

			if($cdb->dt[category_access]){
				$category_access = $cdb->dt[category_access];
				break;
			}else{
				$category_access = 'D';
			}
		}
	}

	$mstring .=  "	groupnode$mcid.tooltip = '$cname';
		groupnode$mcid.id ='nodeid$mcid';
		groupnode$mcid.action = \"setCategory('$cname','$mcid',$depth, '$category_use','$category_code')\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";

	return $mstring;
}



function PrintRelation($relation_cid){
	global $db ,$admininfo;

	$sql = "select c.cid,c.cname,c.depth,r.basic, r.rid, r.regdate  from logstory_category_relation r, standard_category_info c where relation_cid = '".$relation_cid."' and c.cid = r.cid ORDER BY r.regdate ASC ";
	

	$db->query($sql);

	$mString = "<table width=100% cellpadding=0 cellspacing=0 id=objCategory>
						";

	if ($db->total == 0){
		//$mString = $mString."<tr bgcolor=#ffffff height=45><td colspan=5 align=center>선택된 카테고리 정보가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$parent_cname = GetParentCategory($db->dt[cid],$db->dt[depth]);
			$mString .= "<tr>
				<td class='table_td_white small ' width='5' height='25'><input type='text' name='display_category[]' id='_category' value='".$db->dt[cid]."' style='display:none'></td>
				<td class='table_td_white small' width='50'><input type='radio' name='basic' id='basic_".$db->dt[cid]."' value='".$db->dt[cid]."' ".($db->dt[basic] == 1 ? "checked":"")."  validation=true title='기본카테고리' ></td>
				<td class='table_td_white small ' width='*'><label for='basic_".$db->dt[cid]."' >".($parent_cname != "" ? $parent_cname." > ":"").$db->dt[cname]."</label></td>
				<td class='table_td_white' width='100'><!--a href=\"JavaScript:void(0)\" onClick='category_del(this.parentNode.parentNode)'--><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0 onClick=\" $(this).closest('tr').remove();\" style='cursor:point;'><!--/a--></td>
				</tr>";//onClick='category_del(this.parentNode.parentNode)' 를 onClick='category_del(true,this.parentNode.parentNode)' 로 변경 kbk 13/06/30<img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle' onClick='category_del(true,this.parentNode.parentNode)' style='cursor:pointer;' />
		}
	}
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";
	$mString = $mString."</table>";

	return $mString;
}

?>