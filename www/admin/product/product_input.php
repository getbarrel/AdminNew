<?
include("../class/layout.class");



//echo exif_imagetype("/home/simpleline/www/data/simpleline/images/product/basic_1705.gif");

$db = new Database;


function PrintOptionName($pid, $select_opn_ix)
{
	$mdb = new Database;
	$mdb->query("SELECT * FROM ".TBL_SHOP_PRODUCT_OPTIONS." where pid ='$pid'");
	
	$SelectString = "<Select name='opn_ix' onchange=\"ChangeOptionName('$pid', this);\">";
	
	if ($mdb->total){
			$SelectString = $SelectString."<option value=''>옵션이름 선택</option>";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			$SelectString = $SelectString."<option value='".$mdb->dt[opn_ix]."' option_kind='".$mdb->dt[option_kind]."'>".$mdb->dt[option_name]."</option>";
		}
	}else{
	$SelectString = $SelectString."<option value=''> 옵션이 없습니다.</option>";
	}
	
	$SelectString = $SelectString."</Select>";
	
	return $SelectString;
}


$Script = "
<style>
ul {
	LIST-STYLE-IMAGE: none; LIST-STYLE-TYPE: none;padding:0px;
}
li{
	list-style-tyle:none;
	margin:0px;
	padding:0px;
}
  #sortlist { 
      list-style-type:none;
      margin:0;
      padding:0;
   }
   #sortlist li {
     font:13px Verdana;
     margin:0;
     padding:0px;
     cursor:move;
   }
  
</style>
<script  id='dynamic'></script>
<Script Language='JavaScript'>
function ChangeOptionName(pid, obj){
	var option_kind = obj[obj.selectedIndex].option_kind;
	
	
	document.getElementById('option_kind_value').innerHTML = getOptionKind(option_kind);
	if(option_kind == 'b'){
		document.getElementById('pricebyoption_title').innerHTML = '옵션별가격 *';
		var oobj = document.all.option_kind_view;
		for(i=0;i < oobj.length;i++){
			oobj[i].style.display = 'block';
		}
		
		var pobj = document.all.option_price_line;
		for(i=0;i < pobj.length;i++){
			pobj[i].style.display = 'block';
		}
		
		
	}else if(option_kind == 'p'){
		document.getElementById('pricebyoption_title').innerHTML = '옵션별 추가가격 *';
		var oobj = document.all.option_kind_view;
		for(i=0;i < oobj.length;i++){
			oobj[i].style.display = 'none';
		}
		
		var pobj = document.all.option_price_line;
		for(i=0;i < pobj.length;i++){
			pobj[i].style.display = 'block';
		}
		
		
	}else if(option_kind == 's'){
		document.getElementById('pricebyoption_title').innerHTML = '옵션별가격 *';
		var oobj = document.all.option_kind_view;
		for(i=0;i < oobj.length;i++){
			oobj[i].style.display = 'none';
		}
		
		var pobj = document.all.option_price_line;
		for(i=0;i < pobj.length;i++){
			pobj[i].style.display = 'none';
		}
		
	}
	
	document.forms['optionform'].option_kind.value = option_kind;	
	document.frames['act'].location.href='option.act.php?act=view&pid='+pid+'&opn_ix='+obj.value;
}

function getOptionKind(option_kind){

	if(option_kind == 'b'){
		return '가격재고 관리 옵션';
	}else if(option_kind == 'p'){
		return '가격추가옵션';
	}else if(option_kind == 's'){
		return '선택옵션';
	}else{
		return '';
	}

}



function init()
{
";
	if ($id != ""){
		$db->query("SELECT * FROM ".TBL_SHOP_PRODUCT." where id = $id");
	
		if($db->total != 0)
		{
		$db->fetch(0);
		
		$pcode = $db->dt[pcode];
		
		$company = $db->dt[company];
		$state = $db->dt[state];
		$brand = $db->dt[brand];
		$basicinfo = $db->dt[basicinfo];
		$shotinfo = $db->dt[shotinfo];
		$hardware = $db->dt[hardware];
		$software = $db->dt[software];
		$system = $db->dt["system"];
		$etc = $db->dt["etc"];
		$pname = $db->dt[pname];
		$stock = $db->dt[stock];
		$safestock = $db->dt[safestock];
		$search_keyword = $db->dt[search_keyword];
		$disp = $db->dt[disp];
		$gift = $db->dt[gift];
		
		$reserve = $db->dt[reserve];
		
		
		$coprice = $db->dt[coprice];
		$sellprice = $db->dt[sellprice];
		$listprice = $db->dt[listprice];
		
		
		
		
			$Script=$Script. "
			frm = document.product_input;
		//	document.product_input.pname.value = \"".$db->dt[pname]."\";
		//	document.product_input.company.value = '".$db->dt[company]." ';
		//	document.product_input.gift.value = '".$db->dt[gift]."';
		//	try{document.product_input.recomm_saveprice.value = FormatNumber3('".$db->dt[recomm_saveprice]."')}catch(e){};
		//	try{document.product_input.recomm_reserve.value = FormatNumber3('".$db->dt[recomm_reserve]."')}catch(e){};
		//	document.product_input.sellprice.value = FormatNumber3('".$db->dt[sellprice]."');
		//	document.product_input.prd_member_price.value = FormatNumber3('".$db->dt[prd_member_price]."');
		//	document.product_input.prd_dealer_price.value = FormatNumber3('".$db->dt[prd_dealer_price]."');
		//	document.product_input.prd_agent_price.value = FormatNumber3('".$db->dt[prd_agent_price]."');
			
			
	//		document.product_input.bsellprice.value = '".$db->dt[sellprice]."';
	//		document.product_input.coprice.value = FormatNumber3('".$db->dt[coprice]."');
	//		document.product_input.bcoprice.value = '".$db->dt[coprice]."';
		//	document.product_input.reserve.value = '".$db->dt[reserve]."';
		//	document.product_input.breserve.value = '".$db->dt[reserve]."';
			
			for(i=0;i < frm.rate1.length;i++){			
				if(frm.rate1.options[i].value == '".$db->dt[reserve_rate]."'){
					document.product_input.rate1.options[i].selected = true;	
				}			
			}
			
		
			
			
			
			
			
			";
			
			
/*			
			if($db->dt["new"] == 1){
				$Script=$Script. "document.product_input.onew.checked = 1;";
			}
			if($db->dt["hot"] == 1){
				$Script=$Script. "document.product_input.hot.checked = 1;";
			}
			if($db->dt["event"] == 1){
				$Script=$Script. "document.product_input.event.checked = 1;";
			}
			if($db->dt["sale"] == 1){
				$Script=$Script. "document.product_input.sale.checked = 1;";
			}
			if($db->dt["best"] == 1){
				$Script=$Script. "document.product_input.best.checked = 1;";
			}
			
			if($db->dt["disp"] == 1){
				$Script=$Script. "document.product_input.disp.checked = 1;";
			}
*/		
		
		}
	}else{
		$disp = "1";
		$act = "insert";
	}
$Script=$Script. "
	
	Content_Input();
	
	Init(document.product_input);
	
}
function Content_Input(){	
	document.product_input.content.value = document.product_input.basicinfo.value;	
	//alert(document.product_input.content.value);
}

function onDropAction(mode, pid,rp_pid)
{
	//outTip(img3);
	//alert(1);
	parent.document.frames['act'].location.href='./relation.category.act.php?mode='+mode+'&pid='+pid+'&rp_pid='+rp_pid; 
	
}

</Script>";

$Contents = "
	<table cellpadding=0 width=100%>
		<tr>
		    <td align='left' colspan=4 style='padding-bottom:5px;'> ".GetTitleNavigation("상품등록관리", "상품관리 > 상품등록관리")."</td>
		</tr>
	</table>";	

if ($id != "" && $mode != "copy"){

$Contents .= "
			
					<!-- tab start -->
					<div class='tab'><!--admin_tab-->
					<table class='s_org_tab'><!--admin_tab-->
					<tr>
						<td class='tab' style='width:80%'>
							<table id='tab_01'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('addimginputarea','tab_01');relationOnMouseOut();\">이미지 추가</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('displayinfo_area','tab_02');relationOnMouseOut();\">상품추가정보</td>
								<th class='box_03'></th>
							</tr>
							</table>";
$Contents .= "					<table id='tab_03' class='on' ".($admininfo[admin_level] != 9 ? "style='display:none;'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('OptionArea','tab_03');relationOnMouseOut();\" >옵션관리 </td>
								<th class='box_03'></th>
							</tr>
							</table>";
							
$Contents .= "					<table id='tab_04' ".($admininfo[admin_level] == 9 || substr_count ($admininfo[permit], "03-02-02") ? "style='display:block;' ":"style='display:none;' ").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('categoryarea','tab_04');relationOnMouseOut();\" >카테고리등록</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_05' style='display:none;'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('AddOptionArea2','tab_05');relationOnMouseOut();\">추가옵션설정</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_06' style='display:none;'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('machumarea','tab_06');relationOnMouseOut();\"  >맞춤쇼핑</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_07' ".(substr_count ($admininfo[permit], "03-02-01") ? "style='display:block;' ":"style='display:none;' ").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('relation_product_area','tab_07');onLoad();\" >관련상품등록</td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
						<td class='btn'>						
							
						</td>
					</tr>
					</table>
					</div>
					<div class='mallstory t_no'>
						<!-- my_movie start -->
						
						<div class='my_box'>
							<div class='doong' id='addimginputarea' style='display:none;vertical-align:top;height:360px;' >																
							<!--div class='menu'>
							이미지 추가하기 
							</div-->
								<table cellpadding=1 cellspacing=1 width=99% height=100% border=0>
								<form name='addimageform' target='act' action='img.add.php' method=\"post\" enctype='multipart/form-data'>
								<input type='hidden' name=act value='insert'>
								<input type='hidden' name=pid value='".$id."'>								
									<tr height=1><td colspan=3 class='dot-x'></td></tr>
									<tr bgcolor='#ffffff' height=25>
										<td width=90 bgcolor=\"#efefef\" align=left class=small nowrap><img src='../image/ico_dot.gif' align=absmiddle> 500×500 * </td> 
										<td width=85 class=small nowrap><b > 이미지복사</b><input type=checkbox name='add_copy_allimg' onclick='copyAddImageCheckAll();' id='add_copy_img' value=1></td>
										<td width=100 bgcolor=\"#ffffff\"><input type='file' name='addbimg' id='addbimg' size=10 style='font-size:11px;'> </td>
										<td width=100 align=center rowspan=3>										
										<input type=image src='../images/btn/regist_img.gif'>
										</td>
										<td width=400 height=330 rowspan=8 align=center valign=middle>										
										<div style='overflow:auto;height:335px;width:400px;border:1px solid silver;vertical-align:bottom' id='add_image_view'>&nbsp;</div>										
										</td>
									</tr>
									<tr height=1><td colspan=3 class='dot-x'></td></tr>
									<tr height=25>
										<td  bgcolor=\"#efefef\" align=left class=small nowrap><img src='../image/ico_dot.gif' align=absmiddle> 300×300 *  </td> 
										<td class=small>이미지복사<input type=checkbox name='add_chk_mimg' id='add_copy_img' value=1></td>
										<td bgcolor=\"#ffffff\"><input type='file' name='addmimg' id='addsimg' size=10 style='font-size:11px;'> </td>
									</tr>
									<tr height=1><td colspan=3 class='dot-x'></td></tr>
									<tr height=25>
										<td  bgcolor=\"#efefef\" align=left class=small nowrap><img src='../image/ico_dot.gif' align=absmiddle> 50×50 *  </td> 
										<td class=small>이미지복사<input type=checkbox name='add_chk_cimg' id='add_copy_img' value=1></td>
										<td bgcolor=\"#ffffff\"><input type='file' name='addcimg' id='addsimg' size=10 style='font-size:11px;'> </td>
									</tr>
									<tr height=1><td colspan=3 class='dot-x'></td></tr>";


$Contents .= "							<tr>
										<td height=230 colspan=4 style='padding-right:20px;' valign=top >
										<table cellpadding=0 cellspacing=0 height=100% width=100%>
										<tr height=70%>
											<td>
											<div id='addimgarea' style='width:100%;height:100%;overflow:auto-y;padding:0px;'>
											".PrintAddImage($id)."
											</div>		
											</td>
										</tr>
										<tr height=30%>
											<td>";
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >추가하시고자 하는 이미지를 입력후 <b>500×500</b> 이미지 복사를 클릭하시면 나머지 이미지가 자동으로 생성됩니다 </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >이미지 이름을 클릭하면 우측에 미리보기 하실수 있습니다</td></tr>	
</table>
";

//$Contents .= "<div style='position:relative;z-index:100px;'>".HelpBox("이미지추가", $help_text)."</div>";		
$Contents .= "<div style='position:relative;z-index:100px;'>".HelpBox("<div style='position:relative;top:4px;'><table><tr><td valign=bottom><b>이미지추가</b></td><td><a onclick=\"alert(language_data['product_input.php']['A'][language]);\" title='이미지추가 동영 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a></td></tr></table></div>", $help_text, 170)."</div>";
//'동영상 메뉴얼 준비중입니다'
$Contents .= "									</td>
											</tr>
										</table>
										</td>
									</tr>
									</form>						
								</table>								
							</div>
							
							<div class='doong' id='displayinfo_area'  style='display:none;vertical-align:top;height:360px;' >							
							<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 상품추가정보입력</b></div>")."</td></tr></table>
							<table cellpadding=5 cellspacing=0 bgcolor=#ffffff width='100%' border=0>								
								<tr bgcolor='#ffffff' align=center height=25><form name='dispoptionform' target='act' onsubmit='return dpCheckOptionData(this);' action='display_option.act.php'>
								<input type='hidden' name=act value='insert'>
								<input type='hidden' name=dp_ix value=''>			
								<input type='hidden' name=pid value='".$id."'>	
									<td bgcolor=\"#efefef\" width=15% class=small nowrap>추가정보명(예. 사은품)</td>
									<td bgcolor=\"#efefef\" class=small  nowrap>추가정보내용 (예. 이어폰)</td>
									<td bgcolor=\"#efefef\" class=small  nowrap>관리</td>
									<td rowspan=4 width=50% valign=top>";
									$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >제품정보 이외에 고객에게 알려줘야 하는 정보를 설정할수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사은품, 또는 무이자 정보 등 다양한 정보를 상품에 따라 각각 노출할수 있습니다</td></tr>	
</table>
";

//$Contents .= "<div style='position:relative;z-index:100px;'>".HelpBox("이미지추가", $help_text)."</div>";		
$Contents .= "<div style='position:relative;z-index:100px;top:-30px;'>".HelpBox("<div style='position:relative;top:4px;'><table><tr><td valign=bottom><b>상품추가정보</b></td><td><a onclick=\"alert(language_data['product_input.php']['A'][language]);\" title='상품추가정보 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a></td></tr></table></div>", $help_text, 170)."</div>";
//'동영상 메뉴얼 준비중입니다'
$Contents .= "							</td>
								</tr>
								<tr>	
									<td width=20%><input type=text class='textbox' name=dp_title size=28 style='width:100%' value='$dp_title'> </td>
									<td width=20%><input type=text class='textbox' name=dp_desc size=28 style='width:100%' value='$dp_desc'> </td>
									<td width='10%'  align=center valign=top>
									<input type=image src='../image/si_add.gif' border=0>
									</td>
								</tr>
								<tr>	
									<td width='60%' colspan=3 valign=top>
									<div id='addDisplayOptionArea' style='width:100%;height:150;overflow:auto;padding:3px;'>
									".PrintDisplayOption($id)."
									</div>
									</td>
								</tr>								
								</form>
							</table>	
							</div>
							<div class='doong' id='OptionArea'  style='display:block;vertical-align:top;height:360px;' >							
							<table cellpadding=3 cellspacing=1 bgcolor=#ffffff width='100%' height='100%' border=0>								
								<form name='optionform' onsubmit='return CheckOptionData(this);' action='option.act.php' target='act' >
								<input type='hidden' name=act value='insert'>
								<input type='hidden' name=option_kind value=''>
								<input type='hidden' name=option_id value=''>			
								<input type='hidden' name=pid value='".$id."'>	
								<tr height=1><td colspan=3 class='dot-x'></td></tr>
								<tr height=22 bgcolor='#ffffff'>								
									<td bgcolor=\"#efefef\"  class=small nowrap>
									<img src='../image/ico_dot.gif' align=absmiddle>  옵션이름
									</td>
									<td id='addOptionNameArea'>
									".PrintOptionName($id, $select_opn_ix)."
									</td>					
									<td><a href=\"javascript:PoPWindow('./option.pop.php?pid=$id',420,400,'option_name_pop')\"><img  src='../images/btn/option_add.gif'  style='cursor:hand;' aligb=absmiddle border=0></a> </td>				
									<td width='80%' rowspan=16 valign=top>
									<table height=100% width=100%>
										<tr height=70%>
											<td>
											<div id='addOptionArea' style='width:100%;height:100%;overflow:auto;padding:0px;'>
											".PrintOption($id)."
											</div>
											</td>
										</tr>
										<tr height=30%>
											<td>";
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >옵션추가 버튼을 클릭하신다음 추가할 옵션을 입력한다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >옵션이 추가되면 옵션이름에서 선택한후 옵션세부 정보를 추가한후 확인버튼을 클릭한다</td></tr>	
</table>
";

$Contents .= "<div style='position:relative;z-index:100px;'>".HelpBox("<div style='position:relative;top:4px;'><table><tr><td valign=bottom><b>옵션관리</b></td><td><a onclick=\"alert(language_data['product_input.php']['A'][language]);\" title='옵션관리 동영 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a></td></tr></table></div>", $help_text, 180)."</div>";
//'동영상 메뉴얼 준비중입니다'
$Contents .= "									</td>
										</tr>
									</table>
										";

											

					
$Contents .= "					
									</td>			
								</tr>
								<tr height=1><td colspan=3 class='dot-x'></td></tr>
								<tr height=22 bgcolor='#ffffff'>								
									<td bgcolor=\"#efefef\" width=13% class=small nowrap>
									<img src='../image/ico_dot.gif' align=absmiddle> 옵션종류
									</td>
									<td id='option_kind_value' width=20% colspan=2>
									<!--select name=option_kind>
										<option value=b>가격재고 관리 옵션</option>
										<option value=p>가격추가옵션</option>
										<option value=p>선택옵션</option>
									</select-->
									</td>
									
									<td width='2%' rowspan=7 style='padding-top:5px;' valign=top>									
									</td>
											
								</tr>
								
								<tr height=1><td colspan=3 class='dot-x'></td></tr>
								<tr height=22 bgcolor='#ffffff'>								
									<td bgcolor=\"#efefef\" class=small nowrap><img src='../image/ico_dot.gif' align=absmiddle> 옵션구분 *</td>
									<td  colspan=2><input type=text class='textbox' name=option_div size=28 style='width:100%;vertical-align:middle' value='$option_div'></td>
								</tr>
								<tr height=1><td colspan=3 class='dot-x'></td></tr>
								<tr id='option_price_line'>
									<td bgcolor=\"#efefef\" class=small  id='pricebyoption_title' nowrap><img src='../image/ico_dot.gif' align=absmiddle> 옵션별가격 *</td>
									<td colspan=2>
									<table width=100%>
									<tr><td width=20%>비회원가</td><td width=70%><input type=text class='textbox' name=option_price  style='width:100%' value='$option_price'></td></tr>
									<tr><td>회원가 </td><td><input type=text class='textbox' name=option_m_price  style='width:100%' value='$option_m_price'></td></tr>
									<tr><td>딜러가 </td><td><input type=text class='textbox' name=option_d_price  style='width:100%' value='$option_d_price'></td></tr>
									<tr><td nowrap>대리점가</td><td> <input type=text class='textbox' name=option_a_price  style='width:100%' value='$option_a_price'></td></tr>
									</table>							
									</td>
								</tr>
								<tr  id='option_price_line' height=1><td colspan=3 class='dot-x'></td></tr>
								<tr id='option_kind_view' >
									<td bgcolor=\"#efefef\" class=small nowrap><img src='../image/ico_dot.gif' align=absmiddle> 옵션별재고 *</td>
									<td colspan=2><input type=text class='textbox' name=option_stock style='width:100%' value='$option_stock'></td>
								</tr>
								<tr id='option_kind_view'  height=1><td colspan=3 class='dot-x'></td></tr>
								<tr id='option_kind_view'  bgcolor='#ffffff'>
									<td bgcolor=\"#efefef\" class=small><img src='../image/ico_dot.gif' align=absmiddle> 옵션별 안전재고 </td>
									<td colspan=2><input type=text class='textbox' name='option_safestock' size=28 style='width:100%' value='$option_safestock'></td>
								</tr>
								<tr id='option_kind_view'  height=1><td colspan=3 class='dot-x'></td></tr>
								<tr id='option_kind_view'  bgcolor='#ffffff'>
									<td bgcolor=\"#efefef\" class=small><img src='../image/ico_dot.gif' align=absmiddle> 기타 </td>
									<td colspan=2><input type=text class='textbox' name='option_etc1' size=28 style='width:100%' value='$option_etc1'></td>
								</tr>
								<tr id='option_kind_view' height=1><td colspan=3class='dot-x'></td></tr>
								<tr>
									<td></td>
									<td colspan=2 align=right valign=top>
									<input type=image src='../images/btn/ok.gif' aligb=absmiddle border=0> 
									<a href=\"javascript:document.forms['optionform'].reset();\"><img src='../images/btn/cancel.gif' aligb=absmiddle border=0></a>
									</td>
								</tr></form>								
							</table>
							</div>
							
							
							<div class='doong' ondragstart='return false' onselectstart='return false' id='categoryarea'  style='display:none;vertical-align:top;height:360px;' >
							<table bgcolor=#ffffff border=0 width=100%' >
							<tr height=25>
								<td width='15%' rowspan=2 ><div style='overflow:auto;height:340px;width:200px;border:1px solid silver'>".Category()."</div></td>							
								<td colspan=2 width='100%' valign=top>
									<table height=100% width=100%>
										<tr height=70%>
											<td>
											<div id='divarea' style='width:100%;height:100%;overflow:auto;padding:1px;padding-left:10px;padding-right:10px;'>				
											".PrintRelation($id)."
											</div>
											</td>
										</tr>
										<tr height=30%>
											<td>";
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >좌측에 상품카테고리 정보중 원하는 카테고리를 클릭하시면 해당제품이 카테고리에 등록됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >카테고리 <b>[삭제]</b>를 원하시면 삭제 버튼을 클릭하시면 됩니다</td></tr>	
</table>
";

$Contents .= "<div style='position:relative;z-index:100px;'>".HelpBox("<div style='position:relative;top:4px;'><table><tr><td valign=bottom><b>카테고리 등록관리</b></td><td><a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_카테고리상품등록(090108)_config.xml',800,517,'manual_view')\"  title='카테고리 등록관리 동영 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a></td></tr></table></div>", $help_text, 210)."</div>";
$Contents .= "									</td>
										</tr>
									</table>
									
								
								</td>
							</tr>
							</table>
							</div>
							
							<div class='doong'  id='AddOptionArea2' style='display:none;vertical-align:top;height:250px;''>
							<table class='category' border=1 >
							<form name='addoption_input' action='mlog_addoption_regist.act.asp' method='get' onsubmit='return false;'>
							<tr>
								<td rowspan=3>
								
								</td>
								<td rowspan=3></td>
								<td>
								<input type='text' name='abm_title' class='s' >
								<input type='checkbox' name='abm_open' value='1' class='no' onclick='ChangeAlbumName(this.form, this.form.abm_name);' > 표시 
								<input type='text' name='abm_name' class='s' OnKeyUp='AlbumChkByte(this,18);ChangeAlbumName(this.form, this);' value=''>
								<a href='javascript:AddAlbum(document.addoption_input);'><img src='../images/btn/ok.gif' border='0' style='border:0px;'></a><br>								
								
								</td>
							</tr>
							<tr >
								<td  class='select'>
								<SELECT name='album' size=10 onchange='SelectList(this);'>
									<OPTION value='36|앨범|1' selected>앨범</OPTION>
									<OPTION value='73|영상앨범1|0' >영상앨범1</OPTION>
									<OPTION value='237|SEF|0' >SEF</OPTION>
								</Select>
								</td>								
							</tr>
							<tr>
								<td>
									<a href='JavaScript:OnClick_Up(document.addoption_input.album);'><img src='../images/btn/icn_up.gif' class='icn'></a>
									<a href='JavaScript:OnClick_Dn(document.addoption_input.album);'><img src='../images/btn/icn_dn.gif' class='icn'></a>
									<a href='JavaScript:addoption_delete(document.addoption_input)'><img src='../images/btn/s_del.gif' class='more'></a>
								</td>
							</tr></form>
							</table>							
							</div>
							
							<div class='doong'  id=machumarea style='display:none;vertical-align:top;height:250px;''>
								<form name='' target='act' action='search_relation.act.php'><input name=pid type=hidden value='$id'><input type='hidden' name='mode' value='insert'>
								<table cellpadding=0 cellspacing=0>
								<tr><td></td><td valign=top style='padding-left:5px;'><input type='image' src='img/s_edit.gif' border=0 align=absmiddle style='border:0px;cursor:hand'></td>
								</tr></form>
								</table>
							</div>
							
							<div class='doong' id='relation_product_area'  style='display:none;vertical-align:top;height:360px;'  ondragstart='return false' onselectstart='return false' >
							<table bgcolor=#ffffff border=0 width=100%' >
							<tr height=25>
								<td width='15%' rowspan=2 ><div style='overflow:auto;height:340px;width:200px;border:1px solid silver'><iframe  src='relation.category.php' width=100% height=100% frameborder=0 onmouseover=\"onLoad();\" ></iframe></div></td>							
								<td colspan=2 width='100%' valign=top>
								<div id='divarea' style='position:absolute;width:100%;height:340;overflow:auto;padding:1px;padding-left:10px;padding-right:10px;'>				
								<table border=0 cellpadding=2 cellspacing=0 width=100% height=100% >
									<tr height=90%>
										<td width=50%><div ondragstart='return false'  id='reg_product' style='width:100%;height:100%;overflow:auto;padding:1px;padding-left:10px;padding-right:10px;border:1px solid silver;' align=center ><table width=100% height=100%><tr><td align=center class='small'>좌측카테고리를 선택해주세요</td></tr></table></div></td>
										<td width=50% valign=top>										
											<div  ondragstart='return false' onselectstart='return false'   id='relation_product' style='width:100%;height:100%;overflow:auto;padding:1px;padding-left:10px;padding-right:10px;border:1px solid silver;background-color:#ffffff' ondragover=\"this.style.border='3px solid silver';\" 
ondragout=\"this.style.border='1px solid silver';\" dropzone='true' ondrop=\"onDropAction('insert','".$id."',arguments[0].id);\" >".relationProductList($id, $_SESSION["_relation_view_type"])."</div>
										</td>
									</tr>
									<tr height=20>
										<td colspan=4 align=right id='relation_view_button_area'>";
if($_SESSION["_relation_view_type"] == "small"){
	$Contents .= "<a href='relation.category.act.php?mode=view&relation_view_type=large&pid=$id' target=act>큰아이콘보기</a>";	
}else{
	$Contents .= "<a href='relation.category.act.php?mode=view&relation_view_type=small&pid=$id' target=act>작은아이콘 보기</a>";	
}
$Contents .= "
										
										</td>
									</tr>
									<tr height=10%>
										<td colspan=2 >";
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >좌측에 상품카테고리 등록하시면 왼쪽에 선택된 카테고리에 등록된 상품이 나타납니다 </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >관련상품으로 등록을 원하시는 상품을 클릭해서 드래그해서 오른쪽창에 Drop 시키면 관련상품으로 등록됩니다</td></tr>	
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >관련상품으로 등록된 상품의 삭제를 원하시면 삭제 버튼을 클릭하시거나 작은 이미지를 더블클릭 하시면 됩니다</td></tr>
</table>
";

$Contents .= "".HelpBox("<div style='position:relative;top:4px;'><table ><tr><td valign=bottom><b>관련상품 등록관리</b></td><td><a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_관련상품등록(090108)_config.xml',800,517,'manual_view')\" title='관련상품 등록관리 동영 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a></td></tr></table></div>", $help_text, 210)."";

$Contents .= "								</td>
									</tr>
								</table>
								</div>
								</td>
							</tr>
							</table>
							</div>
						</div>
						<!-- my_movie end -->
					</div>
					<!-- tab end -->";


}else{	


	
	
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >아래의 기본적인 상품 등록을 완료하신후 <span style='color:0074ba'>옵션 추가하기</span>, <span style='color:0074ba'>이미지 추가하기</span>, <span style='color:0074ba'>디스플레이 옵션셜정 추가하기</span> , <span style='color:0074ba'>관련상품등록</span>및 <span style='color:0074ba'>카테고리 등록</span> 을 하실수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >한개의 제품을 등록하신후 다중 카테고리 등록이 가능합니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >기본정보가 등록된 상태에서 제품의 부가정보들을 페이지 로딩없이 모두 등록하실수 있습니다</td></tr>";

$help_text .= "	
</table>
";
 
$Contents .= HelpBox("<div style='position:relative;top:7px;'><table><tr><td nowrap>상품등록관리</td><td><a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_상품등록(090108)_config.xml',800,517,'manual_view')\" ><img src='../image/movie_manual.gif' align=absmiddle style='margin:0 0 5 0'></a></td></tr></table></div> ", $help_text);

}

if($mode == "copy"){	
	$Contents .= "<table><tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='str small' style='font-size:15px;' ><b>상품복제 모드 입니다. 아래 정보에서 변경하시고자 하는 정보를 수정하신후 저장버튼을 클릭해주세요</b> </td></tr></table>";
}
				
$Contents = $Contents."
			<!--form name='product_input' action='product_input.act.php' method='post' enctype='multipart/form-data'-->
			<form name=product_input action='product_input.act.php' method='post' enctype='multipart/form-data' onsubmit='return SubmitX(this);'>
			<input type='hidden' name=act value='insert'>
			<input type='hidden' name=id value='".$id."'>
			<input type='hidden' name=bpid value='".$id."'>
								<input type='hidden' name=mode value='".$mode."'>
			<table width=100%>
			<tr height=40 align=left>
				<!--td width=500><a href=\"JavaScript:PoPWindow('/shop/goods_view.php?id=".$id."',980,800,'comparewindow');\">보기</a></td-->";
if ($id == "" || $mode == "copy"){				
$Contents .= "<td align=right>";
	if($mode == "copy"){	
	$Contents .= "<input type=checkbox name='option_copy' id='option_copy' value='1'><label for='option_copy' class='small'>옵션 및 기타 정보 복사하기</label>&nbsp;&nbsp;&nbsp;&nbsp;";
	}
$Contents .= "<img src='../image/b_save.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'insert');\">";
$Contents .= "</td>";
}else{
$Contents .= "<td align=right><img src='../image/b_save.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'update')\"> <img src='../image/b_del.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'delete')\"></td>";
}				
$Contents = $Contents."
			</tr>
			</table>
			<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle><b> 기본정보 : </b><span class=small>굵은 글씨로 되어 있는 항목이 필수 정보입니다.</span></td><td align=right style='padding-right:20px;' class=small><!--a href='JavaScript:SampleProductInsert()'>샘플데이타 넣기</a--></td></tr></table>")."</td></tr></table>
			<table cellpadding=5 cellspacing=1 bgcolor=silver border=0 width='100%'>				
				<col width=10%>
				<col width=40%>
				<col width=10%>
				<col width=40%>
				<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\" width=15% nowrap><img src='../image/ico_dot.gif' align=absmiddle> <b>제품명 *</b> </td>
					<td width='35%' colspan=3><input type=text class='textbox' name=pname size=28 style='width:100%' value='$pname'></td>					
				</tr>
				<!--tr height=1><td colspan=4 class='dot-x'></td></tr-->
				<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 진열 </td>
					<td colspan=3>".displayProduct($disp)."</td>
				</tr>
				<!--tr height=1><td colspan=4 class='dot-x'></td></tr-->
				<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 제품코드 *</td>
					<td><input type=text class='textbox' name=pcode size=28 style='width:100%' value='$pcode'></td>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 상태체크 </td>
					<td>
					<table>
						<tr>
							<td><input type=\"checkbox\" name='onew' class=nonborder id=onew value=1 ".($onew == 1 ? "checked":"")."></td><td><img src='".$admin_config[mall_data_root]."/images/icon/icon_new.gif' align='absmiddle' style='vertical-align:middle'></td>						
							<td><input type=checkbox class=nonborder name=hot id=hot value=1 ".($hot == 1 ? "checked":"")."></td><td><img src='".$admin_config[mall_data_root]."/images/icon/icon_hot.gif' style='vertical-align:middle'></td>
							<td><input type=checkbox class=nonborder name=event id=event value=1 ".($event == 1 ? "checked":"")."></td><td> <img src='".$admin_config[mall_data_root]."/images/icon/icon_event.gif'  style='vertical-align:middle'></td>	
							<td><input type=checkbox class=nonborder name=sale id=sale value=1 ".($sale == 1 ? "checked":"")."></td><td><img src='".$admin_config[mall_data_root]."/images/icon/icon_sale.gif'  style='vertical-align:middle'></td>	
							<td><input type=checkbox class=nonborder name=best id=best value=1 ".($best == 1 ? "checked":"")."></td><td><img src='".$admin_config[mall_data_root]."/images/icon/icon_best.gif'  style='vertical-align:middle'></td>	
					</tr>
					<tr>
						<td colspan=5><a href=\"javascript:PoPWindow('../design/product_icon.php?mmode=pop',960,600,'brand')\"'><img src='../image/btn_pop_manage.gif' align=absmiddle border=0></a></td>
					</tr>
					</table>
					</td>
					
					
				</tr>
				
				<!--tr height=1><td colspan=4 class='dot-x'></td></tr-->
				<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 브랜드 </td>
					<td >
					<table cellpadding=3 cellspacing=0>
						<tr>
							<td><div id='brand_select_area'>".BrandListSelect($brand, $cid)." <input type='text' name=brand_name value='' size=15></div> </td><td><a href=\"javascript:PoPWindow('brand.php?mmode=pop',960,600,'brand')\"'><img src='../image/btn_pop_manage.gif' align=absmiddle border=0></a></td>
						</tr>
					</table>
					</td>
					<!--td width='15%' bgcolor=\"#efefef\" rowspan=9 size=28 nowrap><img src='../image/ico_dot.gif' align=absmiddle> 제품간략소개 *</td>
					<td width='35%' rowspan=9><textarea name=\"shotinfo\" rows=\"10\" cols=\"30\" style='width:100%' class='textbox'>".$shotinfo."</textarea></td-->				
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 제조사</td>
					<td><!--input type=text class='textbox' name=company size=28 style='width:100%'-->
					<table cellpadding=3 cellspacing=0>
						<tr>
							<td><div id='company_select_area'>".MakerList($company,$cid)." <input type='text' name=company_name value='' size=15></div></td><td><a href=\"javascript:PoPWindow('company.php?mmode=pop',960,600,'company')\"'><img src='../image/btn_pop_manage.gif' align=absmiddle border=0></a></td>
						</tr>
					</table>
					</td>
				</tr>
				<!--tr height=1><td colspan=4 class='dot-x'></td></tr-->
				<tr bgcolor='#ffffff'>
					<!--td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 사은품 </td>
					<td><input type=text class='textbox' name=gift size=28 style='width:100%' value='$gift'></td-->							
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 판매상태 </td>
					<td >".SellState($state)."</td>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 재고관리</td>
					<td nowrap>
					<table width='100%' cellpadding=0 cellspacing=0>
					<tr>
						<td align=left>
						
						</td>
						<td align=left>
						재고 <input type=\"text\"  size=5 name=stock value='$stock'> 안전재고 <input type=\"text\"  size=5 name=safestock value='$safestock'>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<!--tr height=1><td colspan=4 class='dot-x'></td></tr-->
				
				<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 검색키워드 </td>
					<td colspan=3 style='line-height:150%'>
					<input type=text class='textbox' name='search_keyword' size=28 style='width:100%' value='$search_keyword'><br>
					※<span class=small > 검색어를 등록하시면 검색이 검색어가 같이 포함되어 노출되게 됩니다</span></td>
				</tr>
				<!--tr height=1><td colspan=4 class='dot-x'></td></tr-->
			</table><br>";
/*
$Contents .= ."		
			<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle><b> 부가정보</b></div>")."</td></tr></table>
			<table cellpadding=5 cellspacing=1 bgcolor=#ffffff width='100%'>								
				<tr bgcolor='#ffffff'>
					<!--td bgcolor=\"#efefef\" width=15% nowrap><img src='../image/ico_dot.gif' align=absmiddle> 규격 *</td>
					<td><input type=text class='textbox' name=standard size=28 style='width:100%' value='$standard'></td-->
					<td bgcolor=\"#efefef\" width=15% nowrap><img src='../image/ico_dot.gif' align=absmiddle> 창고위치 *</td>
					<td width=35%><input type=text class='textbox' name=warehouse  style='width:100%' value='$warehouse'></td>
					<td bgcolor=\"#efefef\" width=15% nowrap><img src='../image/ico_dot.gif' align=absmiddle> 단위 *</td>
					<td width=35%><input type=text class='textbox' name=unit style='width:100%' value='$unit'></td>
				</tr>
				<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 검색키워드 </td>
					<td colspan=3 style='line-height:150%'>
					<input type=text class='textbox' name='search_keyword' size=28 style='width:100%' value='$search_keyword'><br>
					※<span class=small > 검색어를 등록하시면 검색이 검색어가 같이 포함되어 노출되게 됩니다</span></td>
				</tr>
				<tr height=1><td colspan=4 class='dot-x'></td></tr>
			</table><br-->";
*/
$Contents .= "
			<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle><b> 가격정보</b> <a href=\"javascript:mybox.service('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_상품등록(090108)_config.xml','10','665','830', 4, [], Prototype.emptyFunction, [], 'HOME > 메뉴얼 > 상품등록관리');\"><img src='../image/movie_manual.gif' align=absmiddle></a></div>")."</td></tr></table>
			";
if ($admininfo[admin_level] != 9){
	$dispString = "style='display:none;'";
	$readonlyString = " readonly";
	$colorString = ";background-color:#efefef;color:gray";
	$message = "onclick=\"alert(language_data['product_input.php']['C'][language]);\"";
	//'입점업체는 공급가격만 입력하실수 있습니다'
}

$Contents = $Contents."		
			<table cellpadding=0 cellspacing=1 bgcolor=#ffffff width='100%' >
				<tr>
				    <td align='left' colspan=4 style='padding-bottom:0px;'> 
				    <div class='tab'>
							<table class='s_org_tab'>
							<tr>
								<td class='tab'>
									<table id='p_tab_01'  class='on'>
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"showPriceTabContents('price_info','p_tab_01');\">가격정보입력</td>
										<th class='box_03'></th>
									</tr>
									</table>
									<table id='p_tab_02'  ".($admininfo[admin_level] == 9 ? "":"style='display:none;' ").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"showPriceTabContents('detail_price_info','p_tab_02');\">수수료계산기</td>
										<th class='box_03'></th>
									</tr>
									</table>
									
								</td>
								<td class='btn'>						
									
								</td>
							</tr>
							</table>	
					</div>
					<div class='mallstory t_no'>
						<!-- my_movie start -->
						
						<div class='my_box'>
							<div class='doong' id='price_info_box' style='display:block;vertical-align:top;height:250px;' >																
								<table cellpadding=3 cellspacing=0 width=100%>
									<tr>
										<td>
											<table cellpadding=1 cellspacing=1 bgcolor=silver width=100%>
											<col width=14%>
											<col width=14%>
											<col width=14%>
											<col width=14%>
											<col width=14%>
											<col width=14%>
											<col width=14%>
											<tr bgcolor='#efefef' height=35 align=center>
												<td >".($admininfo[admin_level] == 9 ? "구매단가 *":"공급가격 *")." ".($admininfo[admin_level] == 9 ? "<span class=small onclick=\"copyPrice(document.product_input, document.product_input.coprice.value, 1);\" style='cursor:hand;color:red'>복사→</span>":"")." </td>
												<td >정가 ".($admininfo[admin_level] == 9 ? "<span class=small onclick=\"copyPrice(document.product_input, document.product_input.listprice.value, 2);\" style='cursor:hand;color:red'>복사→</span>":"")." </td>
												<td >할인가  </td>
												<!--td >회원가 ".($admininfo[admin_level] == 9 ? "<span class=small onclick=\"copyPrice(document.product_input, document.product_input.prd_member_price.value, 3);\" style='cursor:hand;color:red'>복사→</span>":"")." </td>
												<td >딜러  ".($admininfo[admin_level] == 9 ? "<span class=small onclick=\"copyPrice(document.product_input, document.product_input.prd_dealer_price.value, 4);\" style='cursor:hand;color:red'>복사→</span>":"")."</td>
												<td >대리점  </td-->
												<td >마진(%) </td>
												<td>현금 적립금 </td>
												<td>카드 적립금 </td>
											</tr>
											<tr bgcolor='#fbfbfb' height=35 align=center>
												<td ><input type=text class='textbox' name=coprice size=13 style='text-align:right' value='$coprice' onkeydown='num_check()' style='ime-mode:disabled;TEXT-ALIGN:right' onkeyup='this.value=FormatNumber3(this.value);calcurate_maginrate(document.product_input)'><input type=hidden name=bcoprice value='$coprice'> 원</td>
												<td><input type=hidden name=blistprice value='$listprice'><input type=text class='textbox' name=listprice value='$listprice' size=13 onkeydown='num_check()' style='ime-mode:disabled;TEXT-ALIGN:right".$colorString."'  maxlength=16 onkeyup='this.value=FormatNumber3(this.value);'   $message  $readonlyString> 원</td>
												<td><input type=hidden name=bsellprice value='$sellprice'><input type=text class='textbox' name=sellprice value='$sellprice' size=13 onkeydown='num_check()' style='ime-mode:disabled;TEXT-ALIGN:right".$colorString."'  maxlength=16 onkeyup='this.value=FormatNumber3(this.value);'   $message  $readonlyString> 원</td>
												<!--td> <input type=text class='textbox' name=prd_member_price size=13 value='$prd_member_price' onkeydown='num_check()' maxlength=16 onkeyup='this.value=FormatNumber3(this.value);' style='ime-mode:disabled;TEXT-ALIGN:right;padding-right:3px;".$colorString." ' $message  $readonlyString> 원</td>
												<td> <input type=text class='textbox' name=prd_dealer_price size=13 value='$prd_dealer_price' onkeydown='num_check()' maxlength=16 onkeyup='this.value=FormatNumber3(this.value);' style='Time-mode:disabled;EXT-ALIGN:right".$colorString."' $message  $readonlyString> 원</td>
												<td><input type=text class='textbox' name=prd_agent_price size=13 value='$prd_agent_price' onkeydown='num_check()' maxlength=16 onkeyup='this.value=FormatNumber3(this.value);' style='ime-mode:disabled;TEXT-ALIGN:right".$colorString."' $message  $readonlyString> 원</td-->
												<td > <input  type=text class='textbox' size=13  name='basic_margin' style='text-align:right;background-color:#eff3fc' readonly></td>
												<td style='padding:1 3 1 1' nowrap> 
													<table cellpadding=3 cellspacing=0>
													<tr>
														<td >
														<input type=text class='textbox' name=reserve size=13 style='text-align:right' onkeypress='num_check()'   onkeyup='this.value=FormatNumber3(this.value);' value='$reserve'>
														<input type=hidden name=breserve size=15 style='text-align:right' value='$reserve' readonly>
														
														</td>
														<td align=center>
														
															<select name=rate1 style='font-size:12px;width:50' onchange=\"if(this.form.sellprice.value == ''){alert(language_data['product_input.php']['A'][language]);}else{this.form.reserve.value=Round2(filterNum(this.form.sellprice.value) * this.value/100,1,1);}\">
															<option value=0>0%</option>
															<option value='0.5'>0.5%</option>
															<option value=1>1%</option>
															<option value='1.5'>1.5%</option>
															<option value='2'>2%</option>
															<option value='2.5'>2.5%</option>
															<option value=3>3%</option>
															<option value=5>5%</option>
															<option value=10>10%</option>
														</select>	
														</td>
													</tr>		
													</table>														
												</td>
												<td bgcolor='#ffffff'>
													<table cellpadding=3 cellspacing=0>
													<tr align=center>
														<td >
														<input type=text class='textbox' name=reservebycard size=13 style='text-align:right' onkeypress='num_check()'   onkeyup='this.value=FormatNumber3(this.value);' value='$reservebycard'>
														<input type=hidden name=breservebycard size=15 style='text-align:right' value='$reservebycard'>
														</td>
														<td align=center>
															<select name=rate2 style='font-size:12px;width:50' onchange=\"if(this.form.sellprice.value == ''){alert(language_data['product_input.php']['A'][language]);}else{this.form.reservebycard.value=Round2(filterNum(this.form.sellprice.value) * this.value/100,1,1);}\">
																<option value=0>0%</option>
																<option value='0.5'>0.5%</option>
																<option value=1>1%</option>
																<option value='1.5'>1.5%</option>
																<option value='2'>2%</option>
																<option value='2.5'>2.5%</option>
																<option value=3>3%</option>
																<option value=5>5%</option>
																<option value=10>10%</option>
															</select>	
														</td>
													</tr>		
													</table>
												</td>
											</tr>
											</table>
										</td>										
									</tr>
									<tr>
										<td>";

$help_text2 = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><input size=8 type=text class='textbox' style='width:100;background-color:#eff3fc;height:15px;' value='' readonly> 색깔로 된 입력상자는 자동계산되서 입력되어 집니다. <!--<input size=8 type=text class='textbox' style='width:100;height:15px;' value='' readonly> 색깔의 입력상자에 정보를 입력하신후 --></td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >각 회원별 등급에 따라 가격을 차등 적용할 경우에만 회원가, 딜러가, 대리점가를 입력해주세요</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >각 회원등급별 가격정보가 동일할 경우는 <span class=small  style='cursor:hand;color:red'>복사→</span> 클릭해주세요</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >적립금을 백분율로 계산할때는 <b> 비회원가</b>를 기준으로 계산됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>카드사용시 적립금</b>은 카드결제, 가상계좌 및 PG 모듈을 사용하는 결제 방법에 모두 적용됩니다</td></tr>
</table>
";

$help_text = "
<table cellpadding=1 cellspacing=0 bgcolor=#c0c0c0 width=100%>
						<col width=20%>
						<col width=20%>
						<col width=20%>
						<col width=20%>
						<col width=20%>
						<tr bgcolor='#ffffff' height=25 align=center>
							<td align=left>카드수수료(<input type=text class='textbox' name=card_pay value='4' size=2 readonly style='border:1px;text-align:center'>% 기준)</td>
							<td>적립금사용(현금) </td>
							<td >무이자 수수료</td>
							<td ><b>토탈수수료</b></td>
							<td rowspan=2> &nbsp;<input type=button onclick='calcurate_margin(document.product_input);' value='계산하기'></td>
						</tr>
						<tr bgcolor='#ffffff' height=25 align=center>
							
							<td align=left><input type=text class='textbox' name=card_price value='' style='width:80%;text-align:right;background-color:#eff3fc' size=8 readonly></td>
							<td> + <input size=8 type=text class='textbox' name='reserve_price' style='text-align:right;width:80%;background-color:#eff3fc' readonly></td>
							<td> + <input size=8 type=text class='textbox' name='nointerest_price' style='text-align:right;width:80%;background-color:#eff3fc' value='' readonly></td>
							<td> = <input size=8 type=text class='textbox' name='margin' style='text-align:right;width:80%;background-color:#eff3fc' value='' readonly></td>
						</tr>
						<tr bgcolor='#ffffff'>
							<td  colspan=5 style='padding-top:10px;'>
							$help_text2
							</td>
						</tr>
						</table>";


$Contents .= "<div id='price_info' style='position:relative;'>".HelpBox("가격정보입력", $help_text2)."</div>";
$Contents .= "<div id='detail_price_info' style='position:relative;display:none'>".HelpBox("판매수수료 상세내역", $help_text, 200)."</div>";


$Contents .= "								</td>
									</tr>
								</table><br>
								
							</div>
						</div>
					</div>
				    </td>
				</tr>";

$Contents .="	</table>";



$Contents .="<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle><b> 제품상세정보</b> <span class=small > <img src='/admin/webedit/image/wtool6_1.gif' align=absmiddle> 이미지를 클릭해서 상품상세 이미지를 등록해주세요/제품간략소개(특이사항)는 상품상세에 노출됩니다.</span></div>")."</td></tr></table>
		<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>
                    <tr bgcolor='#ffffff'>			
			<td width='15%' bgcolor=\"#efefef\"  nowrap><img src='../image/ico_dot.gif' align=absmiddle> 제품간략소개(특이사항) *</td>
			<td width='85%' bgcolor=\"#efefef\" colspan=2 style='padding:4px;'><textarea name=\"shotinfo\" rows=\"3\" cols=\"10\" style='height:40px;width:100%' class='textbox'>".$shotinfo."</textarea></td>
		</tr>
                    <tr>
                      <td height='30' colspan='3'>						      
						      <table id='tblCtrls' width='100%' border='0' cellspacing='1' cellpadding='0' align='center'>
						        <tr> 
						          <td bgcolor='F5F6F5'>
									 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						              <tr>
						                <td width='18%' height='56'>
											 	<table width='100%' height='56' border='0' align='center' cellpadding='0' cellspacing='0'>
						                    <tr align='center' valign='bottom'> 
						                      <td height='26'><a href='javascript:doBold();' onMouseOver=\"MM_swapImage('editImage1','','../webedit/image/wtool1_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool1.gif' name='editImage1' width='19' height='18' border='0' id='editImage1'></a></td>
						                      <td><a href='javascript:doItalic();' onMouseOver=\"MM_swapImage('editImage2','','../webedit/image/wtool2_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool2.gif' name='editImage2' width='19' height='18' border='0' id='editImage2'></a></td>
						                      <td><a href='javascript:doUnderline();' onMouseOver=\"MM_swapImage('editImage3','','../webedit/image/wtool3_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool3.gif' name='editImage3' width='19' height='18' border='0' id='editImage3'></a></td>
						                    </tr>
						                    <tr> 
						                      <td height='3' colspan='3'></td>
						                    </tr>
						                    <tr align='center' valign='top'> 
						                      <td height='27'><a href='javascript:doLeft();' onMouseOver=\"MM_swapImage('editImage8','','../webedit/image/wtool8_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool8.gif' name='editImage8' width='19' height='18' border='0' id='editImage8'></a></td>
						                      <td><a href='javascript:doCenter();' onMouseOver=\"MM_swapImage('editImage9','','../webedit/image/wtool9_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool9.gif' name='editImage9' width='19' height='18' border='0' id='editImage9'></a></td>
						                      <td><a href='javascript:doRight();' onMouseOver=\"MM_swapImage('editImage10','','../webedit/image/wtool10_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool10.gif' name='editImage10' width='19' height='18' border='0' id='editImage10'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='19%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr> 
						                      <td width='100%' height='27' align='center' valign='bottom'><a href='javascript:doFont();' onMouseOver=\"MM_swapImage('editImage4','','../webedit/image/wtool4_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool4.gif' name='editImage4' width='84' height='22' border='0' id='editImage4'></a></td>
						                    </tr>
						                    <tr>
						                      <td height='2'></td>
						                    </tr>
						                    <tr> 
						                      <td height='27' align='center' valign='top'><a href='javascript:doSize();' onMouseOver=\"MM_swapImage('editImage11','','../webedit/image/wtool11_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool11.gif' name='editImage11' width='84' height='22' border='0' id='editImage11'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='20%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr> 
						                      <td height='27' align='center' valign='bottom'><a href='javascript:doForcol();' onMouseOver=\"MM_swapImage('editImage5','','../webedit/image/wtool5_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool5.gif' name='editImage5' width='95' height='22' border='0' id='editImage5'></a></td>
						                    </tr>
						                    <tr> 
						                      <td height='2'></td>
						                    </tr>
						                    <tr> 
						                      <td height='27' align='center' valign='top'><a href='javascript:doBgcol();' onMouseOver=\"MM_swapImage('editImage12','','../webedit/image/wtool12_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool12.gif' name='editImage12' width='95' height='22' border='0' id='editImage12'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='18%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr> 
						                      <td height='27' align='center' valign='bottom'><a href='javascript:doImage();' onMouseOver=\"MM_swapImage('editImage6','','../webedit/image/wtool6_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool6.gif' name='editImage6' width='73' height='22' border='0' id='editImage6'></a></td>
						                    </tr>
						                    <tr> 
						                      <td height='2'></td>
						                    </tr>
						                    <tr> 
						                      <td height='27' align='center' valign='top'><a href='javascript:doTable();' onMouseOver=\"MM_swapImage('editImage13','','../webedit/image/wtool13_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool13.gif' name='editImage13' width='73' height='22' border='0' id='editImage13'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='25%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr> 
						                      <td height='27' align='center' valign='bottom'><a href='javascript:doLink();' onMouseOver=\"MM_swapImage('editImage7','','../webedit/image/wtool7_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool7.gif' name='editImage7' width='74' height='22' border='0' id='editImage7'></a></td>
						                    </tr>
						                    <tr> 
						                      <td height='2'></td>
						                    </tr>
						                    <tr> 
						                      <td height='27' align='center' valign='top'><a href='javascript:doMultilink();' onMouseOver=\"MM_swapImage('editImage14','','../webedit/image/wtool14_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool14.gif' name='editImage14' width='111' height='22' border='0' id='editImage14'></a></td>
						                    </tr>
						                  </table>
											 </td>
						              </tr>
						            </table>
									 </td>
						        </tr>
						      </table>
<textarea name=\"basicinfo\"  style='display:none' >".$basicinfo."</textarea>
						      <input type='hidden' name='content' value=''>
						      <iframe align='right' id='iView' style='width: 100%; height:310;' scrolling='YES' hspace='0' vspace='0'></iframe>
						      <!-- html편집기 메뉴 종료 -->						      
                      </td>
                    </tr>
                    <tr style='display:block;'>
          	          <td width='120' height='25' align='center' bgcolor='#F0F0F0'></td>
          		       <td colspan='2' align='right'>&nbsp; 
						      <a href='javascript:doToggleText(document.product_input);' onMouseOver=\"MM_swapImage('editImage15','','../webedit/image/bt_html_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_html.gif' name='editImage15' width='93' height='23' border='0' id='editImage15'></a>
          			      <a href='javascript:doToggleHtml(document.product_input);' onMouseOver=\"MM_swapImage('editImage16','','../webedit/image/bt_source_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_source.gif' name='editImage16' width='93' height='23' border='0' id='editImage16'></a>
                      </td>
                    </tr>
                    <tr> 
                      <td bgcolor='D0D0D0' height='1' colspan='4'></td>
                    </tr>
                  </table><br>";
if ($id != ""){
	$img_view_style = " style='display:block;'";
}else{
	$img_view_style = " style='display:none;''"	;
}	
$image_db = new Database;
$image_db->query("select * from shop_image_resizeinfo order by idx");
$image_info = $image_db->fetchall();
$Contents = $Contents."	<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle><b> 이미지 추가</b> <span class=small >   ".$image_info[0][width]."*".$image_info[0][height]." 이미지를 등록하시면 체크된 작은 이미지는 자동으로 등록됩니다.</span></div>")."<a href=\"javascript:PoPWindow('product_resize.php?mmode=pop',960,600,'brand')\"'><img src='../image/btn_pop_manage.gif' align=absmiddle border=0></a></td></tr></table>
			<table cellpadding=5 cellspacing=0 bgcolor=#ffffff width=100%>
				<!--tr height=2 ><td colspan=4><img src='../image/icon_q.gif' border=0 align=absmiddle> * ".$image_info[0][width]."*".$image_info[0][height]." 이미지를 등록하시면 작은 이미지는 자동으로 등록됩니다.</td></tr-->
				<tr height=1><td colspan=4 class='dot-x'></td><td rowspan=11 width=100% style='border:1px solid silver' align=center valign=middle id=viewimg><img src='".$admin_config[mall_data_root]."/images/product/m_".$id.".gif' onerror=\"this.src='../images/noimage_152_148.gif'\" style='border:1px solid silver' id=chimg></td></tr>
				<tr bgcolor='#ffffff' height=30>					
					<td width=100 bgcolor=\"#efefef\" rowspan=2 class=small nowrap><img src='../image/ico_dot.gif' align=absmiddle > ".$image_info[0][width]."*".$image_info[0][height]." *<br><b>이미지복사</b><input type=checkbox name='chk_allimg' value=1 id='copy_allimg' onclick='copyImageCheckAll();'></td>
					<td width=300 class='small'>
					<input type=file name='allimg' size=20 style='font-size:8pt'>
					
					</td>
					<td width=5% $img_view_style><img src='../image/view_img.gif' onclick=\"ChnageImg('b','".$id."', '".$admin_config[mall_data_root]."/images/product');\" style='cursor:hand'></td>
				</tr>
				<tr height=10><td colspan=2 class='small'>※ ".$image_info[0][width]."*".$image_info[0][height]." 이미지 복사를 클릭하시면 나머지 이미지가 복사됩니다</td></tr>
				<tr height=1><td colspan=4 class='dot-x'></td></tr>
				<tr bgcolor='#ffffff' height=50>				
					<!--td bgcolor=\"#efefef\" ></td-->
					<td bgcolor=\"#efefef\"  class=small nowrap><img src='../image/ico_dot.gif' align=absmiddle > ".$image_info[1][width]."*".$image_info[1][height]." *<br>이미지복사<input type=checkbox name='chk_mimg' id='copy_img' value=1></td>
					<td><input type=file name='mimg' size=20 style='font-size:8pt'></td>
					<td $img_view_style><img src='../image/view_img.gif' onclick=\"ChnageImg('m','".$id."', '".$admin_config[mall_data_root]."/images/product');\" style='cursor:hand'></td>
				</tr>
				<tr height=1><td colspan=4 class='dot-x'></td></tr>
				<tr bgcolor='#ffffff' height=50>				
					<!--td bgcolor=\"#efefef\" ></td-->
					<td bgcolor=\"#efefef\"  class=small nowrap><img src='../image/ico_dot.gif' align=absmiddle > ".$image_info[2][width]."*".$image_info[2][height]." *<br>이미지복사<input type=checkbox name='chk_msimg' id='copy_img' value=1></td>
					<td><input type=file name='msimg' size=20 style='font-size:8pt'></td>
					<td $img_view_style><img src='../image/view_img.gif' onclick=\"ChnageImg('ms','".$id."', '".$admin_config[mall_data_root]."/images/product');\" style='cursor:hand'></td>
				</tr>
				<tr height=1><td colspan=4 class='dot-x'></td></tr>
				<tr bgcolor='#ffffff' height=50>				
					<!--td bgcolor=\"#efefef\" ></td-->
					<td bgcolor=\"#efefef\" class=small  nowrap><img src='../image/ico_dot.gif' align=absmiddle >  ".$image_info[3][width]."*".$image_info[3][height]." *<br>이미지복사<input type=checkbox name='chk_simg' id='copy_img' value=1></td>
					<td><input type=file name='simg' size=20 style='font-size:8pt'></td>
					<td $img_view_style><img src='../image/view_img.gif' onclick=\"ChnageImg('s','".$id."', '".$admin_config[mall_data_root]."/images/product');\" style='cursor:hand'></td>
				</tr>
				<tr height=1><td colspan=4 class='dot-x'></td></tr>
				<tr bgcolor='#ffffff' height=50>				
					<!--td bgcolor=\"#efefef\" ></td-->
					<td bgcolor=\"#efefef\"  class=small nowrap><img src='../image/ico_dot.gif' align=absmiddle > ".$image_info[4][width]."*".$image_info[4][height]." *<br>이미지복사<input type=checkbox name='chk_cimg' id='copy_img' value=1></td>
					<td><input type=file name='cimg' size=20 style='font-size:8pt'></td>
					<td $img_view_style><img src='../image/view_img.gif' onclick=\"ChnageImg('c','".$id."', '".$admin_config[mall_data_root]."/images/product');\" style='cursor:hand'></td>
				</tr>
				<tr height=1><td colspan=4 class='dot-x'></td></tr>
				<tr height=1></td></tr>
			</table><br>";
if (false){
$Contents = $Contents."	<table cellpadding=5 cellspacing=1 border=0 bgcolor=#ffffff width=100%>
				<tr height=2 ><td colspan=6 style='padding-top:30px;padding-bottom:10px;'> <img src='../image/icon_q.gif' border=0 align=absmiddle> 아래 이미지 사이즈를 클릭하시면 해당되는 사이즈의 이미지를 보실수 있습니다. 또한 해당 사이즈의 이미지만을 수정하실수 있습니다.</td></tr>
				<tr height=2 bgcolor=gray><td colspan=6></td></tr>
				<tr bgcolor='#ffffff' height=28>
					<td valign=top width=200>
					<table cellpadding=5 cellspacing=1 border=0 bgcolor=#ffffff width='200' >
						<tr height=2 bgcolor=gray><td ></td></tr>
						<tr bgcolor='#ffffff' height=28>
							<td bgcolor=\"#efefef\" onclick=\"ChnageImg('b','".$id."', '".$admin_config[mall_data_root]."/images/product');\" style='cursor:hand'><img src='../image/ico_dot.gif' align=absmiddle>  이미지 ".$image_info[0][width]."*".$image_info[0][height]." *</td>							
						</tr>
						<tr height=28>
							<td bgcolor=\"#efefef\" onclick=\"ChnageImg('m','".$id."', '".$admin_config[mall_data_root]."/images/product');\" style='cursor:hand'><img src='../image/ico_dot.gif' align=absmiddle> 이미지 ".$image_info[1][width]."*".$image_info[1][height]." *</td>
						</tr>
						<tr height=28>
							<td bgcolor=\"#efefef\" onclick=\"ChnageImg('ms','".$id."', '".$admin_config[mall_data_root]."/images/product');\" style='cursor:hand'><img src='../image/ico_dot.gif' align=absmiddle> 이미지 ".$image_info[2][width]."*".$image_info[2][height]." *</td>
						</tr>
						<tr height=28>
							<td bgcolor=\"#efefef\" onclick=\"ChnageImg('s','".$id."', '".$admin_config[mall_data_root]."/images/product');\" style='cursor:hand'><img src='../image/ico_dot.gif' align=absmiddle> 이미지 ".$image_info[3][width]."*".$image_info[3][height]." *</td>
						</tr>
						<tr height=28>
							<td bgcolor=\"#efefef\" onclick=\"ChnageImg('c','".$id."', '".$admin_config[mall_data_root]."/images/product');\" style='cursor:hand'><img src='../image/ico_dot.gif' align=absmiddle> 이미지 ".$image_info[4][width]."*".$image_info[4][height]." *</td>
						</tr>
						</table>
					</td>
					<td rowspan=6 width=90% height=500 bgcolor=\"#efefef\" colspan=5 valign=top><div id=viewimg><img src='".$admin_config[mall_data_root]."/images/product/m_".$id.".gif' id=chimg><br><input type=file name='mimg' size=30 style='font-size:8pt'></div></td>
				</tr>				
			</table>";
}
			

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>500×500 이미지</b>를 등록한후 이미지 복사를 클릭하시고 저장하시면 <u>300×300, 137×137, 90×90, 50×50</u> 이미지가 자동으로 생성됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >별도의 이미지 복사를 원하시면 이미지복사 체크를 푸신 상태에서 원하시는 이미지를 찾아서 등록하시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >상품정보의 일괄 관리를 위해서 상품상세 이미지의 경우 외부사이트에서 HTML을 복사해서 넣은경우 복사해온 <u>서버측에서 외부사이트 링크가 허용된 경우</u> <b>이미지가 자동</b>으로 복사되게 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >기본상품정보 등록후 <b>이미지가 추가</b>, <b>상품추가정보</b>,<b>가격별재고옵션추가</b>,<b>카테고리등록</b>,<b>관련상품등록</b> 을 간편하게 등록하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >제품 상세정보페이지에 다른 사이트에 있는 이미지를 붙여 넣고 싶은경우 다른 사이트에 있는 이미지를 드래그해서 넣으면 자동으로 복사 됩니다<br>단, 이미지 원본측에 이미지 복사가 허용된 경우에 한함</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >상태체크 에서 HOT 아이콘을 클릭하면 상품리스트에 상단에 베스트 상품에 노출되게 됩니다</td></tr>
</table>
";

$Contents .= HelpBox("상품등록관리", $help_text);


				
$Contents = $Contents."	
			<table width='100%'>			
			<tr height=30 align=left><td width=500></td>";


if ($id == "" ){				
$Contents = $Contents."
				<td align=right><img src='../image/b_save.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'insert');\"></td>";
}else{
$Contents = $Contents."
				<td align=right><img src='../image/b_save.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'update')\"> <img src='../image/b_del.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'delete')\"></td>";
}				
$Contents .= "
			<!--td><img src='../image/b_save.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'insert');\"></td>
			<td><img src='../image/b_edit.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'update')\"></td>
			<td><img src='../image/b_del.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'delete')\"-->
			</td></tr>
			</table>
			</form>";
if($_relation_view_type =="small"){
	$ajax_add_string = "tag:'img',overlap:'horizontal',constraint:false, ";	
}			

if ($id && $mode != "copy"){
$Contents .= "			
<script type='text/javascript'>
Sortable.create('sortlist',
{	
	$ajax_add_string
	onUpdate: function()
	{
		//alert(Sortable.serialize('sortlist'));
		new Ajax.Request('/admin/product/product_input.act.php',
		{
			method: 'POST',
			parameters: Sortable.serialize('sortlist')+'&act=vieworder_update&pid=$id',
			onComplete: function(transport){
			//alert(transport.responseText);
			}
		});
	}
});
</script>";
}

/*			
			<div class=box id=img3  style='Z-INDEX: 110; FILTER: revealTrans(duration=1); LEFT: 10px; VISIBILITY: hidden; WIDTH: 200px; POSITION: absolute; TOP: 10px'>
				<table cellpadding=0 cellspacing=0 bgcolor=#ffffff style='border:1px solid #000000'>
				<tr height=30 align=center bgcolor='#efefef'><td onclick='outTip(img3)'>상품카테고리정보</td></tr>
				<tr><td>".Category()."</td></tr>
				</table>			
				</div>
			</div>
			<br>";
*/			



$Script = "<script language='JavaScript' src='../js/scriptaculous.js'></script>\n<script language='JavaScript' src='../webedit/webedit.js'></script>\n<script Language='JavaScript' src='../include/zoom.js'></script>\n<script Language='JavaScript' src='product_input.js'></script><script Language='JavaScript' src='addoption.js'></script>\n<script language='JavaScript' src='../js/dd.js'></script>\n
<!--script language='JavaScript' src='../js/mozInnerHTML.js'></script-->\n$Script";
$P = new LayOut();
$P->addScript = $Script;
if ($id != ""){
	if ($admininfo[admin_level] == 9){
		$P->OnloadFunction = "init();calcurate_maginrate(document.product_input);calcurate_margin(document.product_input);";
	}else{
		$P->OnloadFunction = "init();calcurate_maginrate(document.product_input);calcurate_margin(document.product_input);";
	}
}else{
	$P->OnloadFunction = "init();";
}

$P->strLeftMenu = product_menu();
$P->Navigation = "HOME > 상품관리 > 상품등록관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();


function search_select($pid){
$listdb = new Database;

$listdb->query("SELECT m.id,m.myshopping_desc,s.disp  FROM myshopping m left outer join search_relation s on m.id = s.search_id and s.pid = '$pid'");



	$mstr = "	<select name=search_id[] style='height:130px;width:630px;'multiple>";
				
	
	for($i=0;$i < $listdb->total;$i++){
	$listdb->fetch($i);
		if($listdb->dt[disp] == 1){	
			$mstr = $mstr."		<option value='".$listdb->dt[id]."' selected>".($i+1).". ".$listdb->dt[myshopping_desc]." ".$listdb->dt[disp]."</option>";
		}else{
			$mstr = $mstr."		<option value='".$listdb->dt[id]."'>".($i+1).". ".$listdb->dt[myshopping_desc]." ".$listdb->dt[disp]."</option>";
		}
	}		
	
	
	$mstr = $mstr."  </select>";
	
	return $mstr;
	
}


function PrintRootNode($cname){

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);
	
	$vPrintRootNode = "var rootnode = new TreeNode('$cname', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');/n
			rootnode.expanded = true;\n\n";
	
	return $vPrintRootNode;
}

function PrintNode($cname,$cid,$depth)
{
	global $id;
	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);
	
	return "	var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');
	node$cid.expanded = true;
	node$cid.action = \"setCategory('insert','$cname','$cid',$depth,'$id')\";
	rootnode.addNode(node$cid);\n\n";
}

function PrintGroupNode($cname,$cid,$depth)
{
	global $id;
	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);

	$Parentdepth = $depth - 1;
	
	if ($depth+1 == 1){
		$cid1 = "000";
	}else if($depth+1 == 2){
		$cid2 = "000";
	}else if($depth+1 == 3){
		$cid3 = "000";
	}else if($depth+1 == 4){
		$cid4 = "000";
	}else if($depth+1 == 3){
		$cid5 = "000";
	}
	
	$parent_cid = "$cid1$cid2$cid3$cid4$cid5";
	
	if ($depth ==1){
		$ParentNodeCode = "node$parent_cid";
	}else if($depth ==2){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==3){
		$ParentNodeCode = "groupnode$parent_cid";
	}

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);

	return "		var groupnode$cid = new TreeNode('$cname',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);
		groupnode$cid.tooltip = '$cname';
		groupnode$cid.id ='nodeid$cid';
		groupnode$cid.action = \"setCategory('insert','$cname','$cid',$depth,'$id')\";
		$ParentNodeCode.addNode(groupnode$cid);\n\n";
}

function Category()
{
	global $id;
	global $db;
	
$cate = "
<script language=\"JavaScript\" src=\"../include/manager.js\"></script>
<script language=\"JavaScript\" src=\"../include/Tree.js\"></script>
<script>

/*	 Create Tree		*/
	var tree = new Tree();
	tree.color = \"black\";
	tree.bgColor = \"white\";
	tree.borderWidth = 0;


/*	Create Root node	*/
	var rootnode = new TreeNode(\"상품카테고리\", \"../resources/ServerMag_Etc_Root.gif\",\"../resources/ServerMag_Etc_Root.gif\");
	rootnode.action = \"setCategory('상품카테고리','000000000000000',-1,0,'".$id."')\";
	rootnode.expanded = true;";
	


$db->query("SELECT * FROM ".TBL_SHOP_CATEGORY_INFO." order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");

$total = $db->total;
for ($i = 0; $i < $db->total; $i++)
{

	$db->fetch($i);
	
	if ($db->dt["depth"] == 0){
		$cate = $cate. PrintNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}else if($db->dt["depth"] == 1){
		$cate = $cate. PrintGroupNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}else if($db->dt["depth"] == 2){
		$cate = $cate. PrintGroupNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}else if($db->dt["depth"] == 3){
		$cate = $cate. PrintGroupNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}else if($db->dt["depth"] == 4){
		$cate = $cate. PrintGroupNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}
}

$cate = $cate."	tree.addNode(rootnode);";
$cate = $cate."	
</script>
<form>
<div id=TREE_BAR style=\"margin:5;\">
<script>		
tree.draw();
tree.nodes[0].select();
</script>
</div>
</form>";

return $cate;
}

function PrintRelation($pid){
	global $db;
	
	$sql = "select c.cid,c.cname,c.depth, r.rid, r.regdate  from ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_SHOP_CATEGORY_INFO." c where pid = '$pid' and c.cid = r.cid ";
	
	
	$db->query($sql);
	
	$mString = "<table cellpadding=0 cellspacing=0 width=100% bgcolor=silver style='font-size:10px;'>";	
	$mString .= "<tr align=center bgcolor=#efefef height=25>
				<td class='s_td small'>번호</td>
				<td class='m_td small'>카테고리 ID</td>
				<td class='m_td small'>카테고리</td>
				<td class='m_td small'>등록날짜</td>
				<td class='e_td small'>삭제</td>
			</tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=45><td colspan=5 align=center>선택된 카테고리 정보가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$parent_cname = GetParentCategory($db->dt[cid],$db->dt[depth]);
			$mString .= "<tr height=30 bgcolor=#ffffff align=center>
				<td class='table_td_white small' align=center>".($i+1)."</td>
				<td class='table_td_white small'>".$db->dt[cid]."</td>
				<td class='table_td_white small'>".($parent_cname != "" ? $parent_cname.">":"").$db->dt[cname]."</td>
				<td class='table_td_white small'>".$db->dt[regdate]."</td>
				<td class=table_td_white align=center><a href=\"JavaScript:deleteCategory('delete','".$db->dt[rid]."','$pid')\"><img src='../image/btc_del.gif' border=0></a></td>
				</tr>";
			$mString .= "<tr height=1><td colspan=5 class='dot-x'></td></tr>";
		}
	}
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";
	$mString = $mString."</table>";
	
	return $mString;
}


function PrintAddImage($pid){
	global $db, $admin_config;
	
	$sql = "select id from ".TBL_SHOP_ADDIMAGE." a where pid = '$pid' ";
	$db->query($sql);
	
	$mString = "<table cellpadding=5 cellspacing=0 width='100%' bgcolor=silver>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25>
						<td class='s_td'>번호</td>
						<td  class='m_td' colspan=2>클립아트 ID</td>
						<td  class='m_td'>중간이미지</td>
						<td  class='m_td'>큰이미지</td>
						<td  class='e_td'>삭제</td>
					</tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=80><td colspan=6 align=center>입력된 추가이미지가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString = $mString."<tr height=25 bgcolor=#ffffff><td  align=center  class=small>".($i+1)."</td><td  ><img src='".$admin_config[mall_data_root]."/images/addimg/c_".$db->dt[id]."_add.gif' align=absmiddle style='border:1px solid gray'></td><td  class=small><a href=\"javascript:AddImageView('".$admin_config[mall_data_root]."/images/addimg/c_".$db->dt[id]."_add.gif')\">c_".$db->dt[id]."_add.gif</a></td><td  class=small><a href=\"javascript:AddImageView('".$admin_config[mall_data_root]."/images/addimg/m_".$db->dt[id]."_add.gif')\">m_".$db->dt[id]."_add.gif</a></td><td><a href=\"javascript:AddImageView('".$admin_config[mall_data_root]."/images/addimg/b_".$db->dt[id]."_add.gif')\">b_".$db->dt[id]."_add.gif</a></td><td align=center  class=small><a href=\"JavaScript:deleteAddimage('delete','".$db->dt[id]."','$pid')\"><img src='../image/btc_del.gif'></a></td></tr>";
			$mString = $mString."<tr height=1><td colspan=6 class='dot-x'></td></tr>";
		}
	}
	$mString = $mString."</table>";
	
	return $mString;
}


function PrintOption($pid, $opn_ix =''){
	global $db;
	
	$sql = "select id, option_div,option_price, option_m_price, option_d_price, option_a_price, option_useprice, option_stock, option_safestock,option_etc1 from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a where pid = '$pid' and opn_ix = '$opn_ix' order by id asc";
	$db->query($sql);
	
	$mString = "<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver>";
	$mString .=  "<tr height=1><td colspan=9 ></td></tr>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td rowspan=3 class=small>번호</td><td rowspan=3 class=small>옵션구분</td><td colspan=4 class=small>옵션가격</td><td colspan=2 class=small>옵션재고</td><td rowspan=3 class=small>기타(색상)</td><td rowspan=3 class=small>관리</td></tr>";
	$mString .=  "<tr height=1><td colspan=6 class='dot-x'></td></tr>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td class=small>비회원가</td><td class=small>회원가</td><td class=small>딜러가</td><td class=small>대리점가</td><td class=small>재고</td><td class=small>안전재고</td></tr>";
	$mString .=  "<tr height=1><td colspan=9 ></td></tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=120><td colspan=10 align=center class=small>수정 /  추가 하시고자 하는 옵션이름을 선택해주세요</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString = $mString."<tr height=25 bgcolor=#ffffff>
			<td  align=center>".($i+1)."</td>
			<td><a href=\"JavaScript:UpdateOption('".$db->dt[id]."','".str_replace("\"","&quot;",$db->dt[option_div])."','".$db->dt[option_price]."','".$db->dt[option_m_price]."','".$db->dt[option_d_price]."','".$db->dt[option_a_price]."','".$db->dt[option_stock]."','".$db->dt[option_safestock]."','".$db->dt[option_etc1]."')\" ><u>".$db->dt[option_div]."</u></a></td>
			<td>".$db->dt[option_price]."</td>
			<td>".$db->dt[option_m_price]."</td>
			<td>".$db->dt[option_d_price]."</td>
			<td>".$db->dt[option_a_price]."</td>
			<td>".$db->dt[option_stock]."</td>
			<td>".$db->dt[option_safestock]."</td>
			<td>".$db->dt[option_etc1]."</td>
			<td align=center>				
				<a href=\"JavaScript:deleteOption('delete','".$db->dt[id]."','$pid')\"><img  src='../image/btc_del.gif' border=0></a>
			</td>
			</tr>
			<tr height=1><td colspan=9 class='dot-x'></td></tr>
			";
		}
	}
	$mString = $mString."</table>";
	
	return $mString;
}


function PrintDisplayOption($pid){
	global $db;
	
	$sql = "select * from ".TBL_SHOP_PRODUCT_DISPLAYINFO." a where pid = '$pid' ";
	$db->query($sql);
	
	$mString = "<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver>";	
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td class='s_td small' >번호</td><td class='m_td small' >추가정보명</td><td class='m_td small' >추가정보내용</td><td class='e_td small' >관리</td></tr>";	
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=9 align=center class=small >입력된 상품추가정보 항목이  없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString = $mString."<tr height=25 bgcolor=#ffffff align=center>
			<td  class=small >".($i+1)."</td>
			<td class=small ><a href=\"JavaScript:UpdateDisplayOption('".$db->dt[dp_ix]."','".$db->dt[dp_title]."','".$db->dt[dp_desc]."')\" ><u>".$db->dt[dp_title]."</u></a></td>
			<td class=small >".$db->dt[dp_desc]."</td>
			<td align=center>				
				<a href=\"JavaScript:deleteDisplayOption('delete','".$db->dt[dp_ix]."','$pid')\"><img  src='../image/si_remove.gif' border=0></a>
			</td>
			</tr>
			<tr height=1><td colspan=4 class='dot-x'></td></tr>
			";
		}
	}
	$mString = $mString."</table>";
	
	return $mString;
}

function _PrintOption($pid){
	global $db;
	
	$sql = "select id, option_div,option_price, option_useprice, option_stock from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." a where pid = '$pid' ";
	$db->query($sql);
	
	$mString = "<table cellpadding=2 cellspacing=1 width=100% bgcolor=silver>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td>번호</td><td>옵션이름</td><td>옵션구분</td><td>옵션가격</td><td>옵션재고</td><td>옵션표시</td><td>관리</td></tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=40><td colspan=7 align=center>입력된 옵션이 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString = $mString."<tr height=25 bgcolor=#ffffff>
			<td  align=center>".($i+1)."</td><td>".$db->dt[option_name]."</td><td>".$db->dt[option_div]."</td><td>".$db->dt[option_price]."</td><td>".$db->dt[option_stock]."</td>
			<td>".PrintSelect($db->dt[option_name],$db->dt[option_div],$db->dt[option_price],$db->dt[option_useprice])."</td>
			<td align=center>
				<a href=\"JavaScript:UpdateOption('".$db->dt[id]."','".$db->dt[option_name]."','".$db->dt[option_div]."','".$db->dt[option_price]."','".$db->dt[option_stock]."')\">○</a>
				<a href=\"JavaScript:deleteOption('delete','".$db->dt[id]."','$pid')\">×</a>
			</td>
			</tr>";
		}
	}
	$mString = $mString."</table>";
	
	return $mString;
}

function PrintSelect($op_name,$op_div,$op_price,$op_useprice)
{
	$aryOp_div = explode("|",$op_div);
	$aryOp_price = explode("|",$op_price);
	$size = count($aryOp_div);
	
	$SelectString = "<Select>";
	
	if ($size == 0){
		$SelectString = $SelectString."<option>옵션이 없습니다.</option>";
	}else{
		if($op_useprice ==1){
			for($i=0; $i < $size; $i++){
				$SelectString = $SelectString."<option value='".$aryOp_div[$i]."'>".$aryOp_div[$i]."</option>";
			}
		}else{
			for($i=0; $i < $size; $i++){
				$SelectString = $SelectString."<option value='".$aryOp_price[$i]."'>".$aryOp_div[$i]."</option>";
			}
		}
	}
	
	$SelectString = $SelectString."</Select>";
	
	return $SelectString;
}


function SellState($vstate){
	global $admininfo;
	
	if($admininfo[admin_level] == 9){
		$mstring = "
		<Select name=state class=small>
			<option value=0 ".($vstate == 0 ? "selected":"").">일시품절</option>
			<option value=1 ".(($vstate == 1 || $vstate == "") ? "selected":"").">판매중</option>";
		if($admininfo[mall_use_multishop]){
		$mstring .= "<option value=6 ".($vstate == 6 ? "selected":"").">입점업체 등록신청</option>";
		}
		$mstring .= "</Select>";
	}else if($admininfo[admin_level] == 8){
		$mstring = "
		<Select name=state>
			<option value=0 ".($vstate == 0 ? "selected":"").">일시품절</option>";
		if ($vstate == 1 ){	
		$mstring .= "<option value=1 ".($vstate == 1 ? "selected":"").">판매중</option>";
		}
		if($admininfo[mall_use_multishop]){
		$mstring .= "<option value=6 ".(($vstate == 6 || $vstate == "") ? "selected":"").">입점업체 등록신청</option>";
		}
		$mstring .= "</Select>";
	}
	return $mstring;
}


function displayProduct($disp){
if ($disp == 1 || $disp == ""){	
	$Selectedstr00 = "";
	$Selectedstr01 = " selected";
}else{
	$Selectedstr00 = " selected";
	$Selectedstr01 = "";
}
return "
<input type='radio' name='disp'  id='disp_0' value='0' ".ReturnStringAfterCompare($disp, "0", " checked")."><label for='disp_0'>노출안함</label>
<input type='radio' name='disp'  id='disp_1' value='1' ".ReturnStringAfterCompare($disp, "1", " checked")."><label for='disp_1'>노출함</label>
<!--input type='radio' name='disp'  id='disp_0' value='2' ".ReturnStringAfterCompare($disp, "2", " checked")."><label for='disp_2'>포인트몰</label>
<input type='radio' name='disp'  id='disp_0' value='3' ".ReturnStringAfterCompare($disp, "3", " checked")."><label for='disp_3'>현금,포인트몰</label>
<input type='radio' name='disp'  id='disp_0' value='9' ".ReturnStringAfterCompare($disp, "9", " checked")."><label for='disp_9'>공동구매</label-->

<!--Select name=state>
	<option value=0 $Selectedstr00>일시품절</option>
	<option value=1 $Selectedstr01>판매중</option>
</Select-->";
}




function relationProductList($pid, $relation_view_type=""){

	global $start,$page, $orderby, $admin_config ;
	
	$max = 105;
	
	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}
		
	$db = new Database;
	
	$db->query("SELECT distinct p.id,p.pname, p.sellprice,p.prd_member_price, p.prd_dealer_price, p.prd_agent_price,  p.reserve, rp_ix FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_RELATION_PRODUCT." rp where p.id = rp.rp_pid and rp.pid = '$pid' and p.disp = 1   ");
	$total = $db->total;
	
	
	$db->query("SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,p.prd_member_price, p.prd_dealer_price, p.prd_agent_price,  p.reserve, rp_ix  FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_RELATION_PRODUCT." rp where p.id = rp.rp_pid and rp.pid = '$pid'  and p.disp = 1 order by rp.vieworder limit $start,$max");		
	
	
	

	if ($db->total == 0){
		$mString = "<table cellpadding=0 cellspacing=0 width=100% height=100%  bgcolor=silver style='font-size:10px;'>";	
		$mString .= "<tr bgcolor=#ffffff height=100%><td colspan=5 align=center class='small'>등록된 관련 상품 정보가 없습니다 . <br> 좌측 상품을 이곳으로 드래그하시면 <br>관련상품이 등록됩니다. </td></tr>";
		$mString .= "</table>";	
	}else{
		
		if($relation_view_type == "small"){		
			$mString = "<div id='sortlist'>";				
			$i=0;
			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);			
				$mString .= "<img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif' id='image_".$db->dt[id]."' title='".cut_str($db->dt[pname],30)."' ondblclick=\"document.frames['act'].location.href='relation.category.act.php?mode=delete&pid=".$pid."&rp_ix=".$db->dt[rp_ix]."'\"width=50 height=50 style='border:1px solid silver' vpace=2 hspace=2>";
			}
			$mString = $mString."</div>";
		}else{
			$mString = "<ul id='sortlist'>";				
			$i=0;
			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);			
				$mString .= "<li id='image_".$db->dt[id]."' >
							<table width=96% border=0 >
							<col width='60'>
							<col width='*'>
							<col width='60'>
							<tr height=27 bgcolor=#ffffff >
							<td class=table_td_white align=center style='padding:5px;'>
								<img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif'>
							</td>						
							<td class=table_td_white>".cut_str($db->dt[pname],30)."</td>
							<td><a href='relation.category.act.php?mode=delete&pid=".$pid."&rp_ix=".$db->dt[rp_ix]."'  target=act><img src='../image/btc_del.gif'></a></td>
							</tr>
							<tr height=1><td colspan=5 class='dot-x'></td></tr>
						</table></li>";
			}
			$mString = $mString."</ul>";
		}
	}
	
	
	
	return $mString;
	
}





function MakerList($company, $cid, $return_type ="")
{
//global $db;

	$mdb = new Database;

	if($cid){
		$mdb->query("SELECT * FROM ".TBL_SHOP_COMPANY." where disp=1 and cid = '$cid'");
	}else{
		$mdb->query("SELECT * FROM ".TBL_SHOP_COMPANY." where disp=1");
	}
	
	$bl = "<Select name='company' class=small>";	
	if ($mdb->total == 0)	{
		$bl = $bl."<Option>등록된 제조사가 없습니다.</Option>";
	}else{
		if($return_type == ""){
			$bl = $bl."<Option value=''>제조사 선택</Option>";
			for($i=0 ; $i <$mdb->total ; $i++)
			{
				$mdb->fetch($i);
				if ($company == $mdb->dt[company_name]){
					$strSelected = "Selected";
				}else{
					$strSelected = "";
				}
				
				$bl = $bl."<Option value='".$mdb->dt[company_name]."' $strSelected>".$mdb->dt[company_name]."</Option>";
				
			}
		}else{
			for($i=0 ; $i <$mdb->total ; $i++)
			{
				$mdb->fetch($i);
				if ($brand == $mdb->dt[c_ix]){
					return $mdb->dt[comapny_name];
				}
			}
		}
	}
	
	$bl = $bl."</Select>";
		
	return $bl;
}
?>