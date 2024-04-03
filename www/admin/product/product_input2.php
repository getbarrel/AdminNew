<?

include($_SERVER["DOCUMENT_ROOT"]."/shop/common/util.php");
include("../class/layout.class");



//echo exif_imagetype("/home/simpleline/www/data/simpleline/images/product/basic_1705.gif");

$db = new Database;

$admininfo[admin_level] = 9;


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
		
			$Script=$Script. "
			frm = document.product_input;
			document.product_input.pname.value = \"".$db->dt[pname]."\";
			document.product_input.company.value = '".$db->dt[company]." ';
			document.product_input.gift.value = '".$db->dt[gift]."';
		//	try{document.product_input.recomm_saveprice.value = FormatNumber3('".$db->dt[recomm_saveprice]."')}catch(e){};
		//	try{document.product_input.recomm_reserve.value = FormatNumber3('".$db->dt[recomm_reserve]."')}catch(e){};
			document.product_input.sellprice.value = FormatNumber3('".$db->dt[sellprice]."');
			document.product_input.prd_member_price.value = FormatNumber3('".$db->dt[prd_member_price]."');
			document.product_input.prd_dealer_price.value = FormatNumber3('".$db->dt[prd_dealer_price]."');
			document.product_input.prd_agent_price.value = FormatNumber3('".$db->dt[prd_agent_price]."');
			
			
			document.product_input.bsellprice.value = '".$db->dt[sellprice]."';
			document.product_input.coprice.value = FormatNumber3('".$db->dt[coprice]."');
			document.product_input.bcoprice.value = '".$db->dt[coprice]."';
			document.product_input.reserve.value = '".$db->dt[reserve]."';
			document.product_input.breserve.value = '".$db->dt[reserve]."';
			
			for(i=0;i < frm.rate1.length;i++){			
				if(frm.rate1.options[i].value == '".$db->dt[reserve_rate]."'){
					document.product_input.rate1.options[i].selected = true;	
				}			
			}
			
			
			
			
			
			
			";
			
			if($db->dt["new"] == 1){
				$Script=$Script. "document.product_input.onew.checked = 1;";
			}
			if($db->dt["hot"] == 1){
				$Script=$Script. "document.product_input.hot.checked = 1;";
			}
			if($db->dt["event"] == 1){
				$Script=$Script. "document.product_input.event.checked = 1;";
			}
/*			
			if($db->dt["disp"] == 1){
				$Script=$Script. "document.product_input.disp.checked = 1;";
			}
*/		
		}
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
	parent.document.frames['act'].location.href='./relation.category.act.php?mode='+mode+'&pid='+pid+'&rp_pid='+rp_pid; 
}

</Script>";

$Contents = "
	<table cellpadding=0 width=100%>
		<tr>
		    <td align='left' colspan=4 style='padding-bottom:20px;'> ".GetTitleNavigation("product_reg_title.gif", "상품관리 > 상품등록리관리")."</td>
		</tr>
	</table>";	
			
if ($id != "" ){
	
$Contents .= "
			
					<!-- tab start -->
					<div class='admin_tab'>
					<table class='admin_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('addimginputarea','tab_01')\">이미지 추가</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('displayinfo_area','tab_02')\">디스플레이옵션설정</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03' class='on' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('OptionArea','tab_03')\">가격별재고별옵션추가</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_04' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('categoryarea','tab_04')\">카테고리등록</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_05' style='display:none;'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('AddOptionArea2','tab_05')\">추가옵션설정</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_06' style='display:none;'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('machumarea','tab_06')\">맞춤쇼핑</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_07' ".(substr_count ($admininfo[permit], "03-02-01") ? "style='display:block;' ":"style='display:none;' ").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('relation_product_area','tab_07')\">관련상품등록</td>
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
								<table cellpadding=2 cellspacing=1 width=99% height=100% border=0>
								<form name='addimageform' target='act' action='img.add.php' method=\"post\" enctype='multipart/form-data'>
								<input type='hidden' name=act value='insert'>
								<input type='hidden' name=pid value='".$id."'>											
									<tr height=1><td colspan=3 background='../image/dot.gif'></td></tr>
									<tr bgcolor='#ffffff' height=25>
										<td width=100 bgcolor=\"#efefef\" align=left nowrap><img src='../image/ico_dot.gif' align=absmiddle> 500×500 * </td> 
										<td width=100> 이미지복사<input type=checkbox name='add_copy_allimg' onclick='copyAddImageCheckAll();' id='add_copy_img' value=1></td>
										<td width=100 bgcolor=\"#ffffff\"><input type='file' name='addbimg' id='addbimg' style='font-size:11px;'> </td>
										<td width=100 align=center rowspan=3>										
										<input type=image src='../images/btn/regist_img.gif'>
										</td>
										<td width=400 height=330 rowspan=8 align=center valign=middle>										
										<div style='overflow:auto;height:335px;width:400px;border:1px solid silver;vertical-align:bottom' id='add_image_view'>&nbsp;</div>										
										</td>
									</tr>
									<tr height=1><td colspan=3 background='../image/dot.gif'></td></tr>
									<tr height=25>
										<td  bgcolor=\"#efefef\" align=left nowrap><img src='../image/ico_dot.gif' align=absmiddle> 300×300 *  </td> <td width=100>이미지복사<input type=checkbox name='add_chk_mimg' id='add_copy_img' value=1></td>
										<td width=100 bgcolor=\"#ffffff\"><input type='file' name='addmimg' id='addsimg' style='font-size:11px;'> </td>
									</tr>
									<tr height=1><td colspan=3 background='../image/dot.gif'></td></tr>
									<tr height=25>
										<td  bgcolor=\"#efefef\" align=left nowrap><img src='../image/ico_dot.gif' align=absmiddle> 50×50 *  </td> <td width=100>이미지복사<input type=checkbox name='add_chk_cimg' id='add_copy_img' value=1></td>
										<td width=100 bgcolor=\"#ffffff\"><input type='file' name='addcimg' id='addsimg' style='font-size:11px;'> </td>
									</tr>
									<tr height=1><td colspan=3 background='../image/dot.gif'></td></tr>
									<tr><td height=230 colspan=4 style='padding-right:20px;' valign=top id='addimgarea'>".PrintAddImage($id)."</td></tr>
									</form>						
								</table>								
							</div>
							
							<div class='doong' id='displayinfo_area'  style='display:none;vertical-align:top;height:360px;' >							
							<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 디스플레이 옵션설정</b></div>")."</td></tr></table>
							<table cellpadding=5 cellspacing=0 bgcolor=#ffffff width='100%' border=0>								
								<tr bgcolor='#ffffff' align=center height=25><form name='dispoptionform' target='act' onsubmit='return dpCheckOptionData(this);' action='display_option.act.php'>
								<input type='hidden' name=act value='insert'>
								<input type='hidden' name=dp_ix value=''>			
								<input type='hidden' name=pid value='".$id."'>	
									<td bgcolor=\"#efefef\" width=15% class=small nowrap>옵션제목</td>
									<td bgcolor=\"#efefef\" class=small  nowrap>옵션내용 *</td>
									<td bgcolor=\"#efefef\" class=small  nowrap>관리</td>
									<td rowspan=4 width=50%>&nbsp;</td>
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
								<tr height=1><td colspan=3 background='../image/dot.gif'></td></tr>
								<tr height=22 bgcolor='#ffffff'>								
									<td bgcolor=\"#efefef\"  nowrap>
									<img src='../image/ico_dot.gif' align=absmiddle>  옵션이름
									</td>
									<td id='addOptionNameArea'>
									".PrintOptionName($id, $select_opn_ix)."
									</td>					
									<td><a href=\"javascript:PoPWindow('./option.pop.php?pid=$id',420,400,'option_name_pop')\"><img  src='../images/btn/option_add.gif'  style='cursor:hand;' aligb=absmiddle border=0></a> </td>				
									<td width='80%' rowspan=16 valign=top>
									<div id='addOptionArea' style='width:100%;height:100%;overflow:auto;padding:0px;'>
									".PrintOption($id)."							
									</div>
									</td>			
								</tr>
								<tr height=1><td colspan=3 background='../image/dot.gif'></td></tr>
								<tr height=22 bgcolor='#ffffff'>								
									<td bgcolor=\"#efefef\" width=13% nowrap>
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
								
								<tr height=1><td colspan=3 background='../image/dot.gif'></td></tr>
								<tr height=22 bgcolor='#ffffff'>								
									<td bgcolor=\"#efefef\"  nowrap><img src='../image/ico_dot.gif' align=absmiddle> 옵션구분 *</td>
									<td  colspan=2><input type=text class='textbox' name=option_div size=28 style='width:100%;vertical-align:middle' value='$option_div'></td>
								</tr>
								<tr height=1><td colspan=3 background='../image/dot.gif'></td></tr>
								<tr id='option_price_line'>
									<td bgcolor=\"#efefef\" id='pricebyoption_title' nowrap><img src='../image/ico_dot.gif' align=absmiddle> 옵션별가격 *</td>
									<td colspan=2>
									<table width=100%>
									<tr><td width=20%>비회원가</td><td width=70%><input type=text class='textbox' name=option_price  style='width:100%' value='$option_price'></td></tr>
									<tr><td>회원가 </td><td><input type=text class='textbox' name=option_m_price  style='width:100%' value='$option_m_price'></td></tr>
									<tr><td>딜러가 </td><td><input type=text class='textbox' name=option_d_price  style='width:100%' value='$option_d_price'></td></tr>
									<tr><td nowrap>대리점가</td><td> <input type=text class='textbox' name=option_a_price  style='width:100%' value='$option_a_price'></td></tr>
									</table>							
									</td>
								</tr>
								<tr  id='option_price_line' height=1><td colspan=3 background='../image/dot.gif'></td></tr>
								<tr id='option_kind_view' >
									<td bgcolor=\"#efefef\" nowrap><img src='../image/ico_dot.gif' align=absmiddle> 옵션별재고 *</td>
									<td colspan=2><input type=text class='textbox' name=option_stock style='width:100%' value='$option_stock'></td>
								</tr>
								<tr id='option_kind_view'  height=1><td colspan=3 background='../image/dot.gif'></td></tr>
								<tr id='option_kind_view'  bgcolor='#ffffff'>
									<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 옵션별 안전재고 </td>
									<td colspan=2><input type=text class='textbox' name='option_safestock' size=28 style='width:100%' value='$option_safestock'></td>
								</tr>
								<tr id='option_kind_view'  height=1><td colspan=3 background='../image/dot.gif'></td></tr>
								<tr id='option_kind_view'  bgcolor='#ffffff'>
									<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 기타 </td>
									<td colspan=2><input type=text class='textbox' name='option_etc1' size=28 style='width:100%' value='$option_etc1'></td>
								</tr>
								<tr id='option_kind_view' height=1><td colspan=3background='../image/dot.gif'></td></tr>
								<tr>
									<td></td>
									<td colspan=2 align=right valign=top>
									<input type=image src='../images/btn/ok.gif' aligb=absmiddle border=0> 
									<a href=\"javascript:document.forms['optionform'].reset();\"><img src='../images/btn/cancel.gif' aligb=absmiddle border=0></a>
									</td>
								</tr></form>								
							</table>
							</div>
							
							
							<div class='doong' id='categoryarea'  style='display:none;vertical-align:top;height:360px;' >
							<table bgcolor=#ffffff border=0 width=100%' >
							<tr height=25>
								<td width='15%' rowspan=2 ><div style='overflow:auto;height:340px;width:200px;border:1px solid silver'>".Category()."</div></td>							
								<td colspan=2 width='100%' valign=top>
								<div id='divarea' style='width:100%;height:340;overflow:auto;padding:1px;padding-left:10px;padding-right:10px;'>				
								".PrintRelation($id)."
								</div>
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
							
							<div class='doong' id='relation_product_area'  style='display:none;vertical-align:top;height:360px;' >
							<table bgcolor=#ffffff border=0 width=100%' >
							<tr height=25>
								<td width='15%' rowspan=2 ><div style='overflow:auto;height:340px;width:200px;border:1px solid silver'><iframe  src='relation.category.php' width=100% height=100% frameborder=0></iframe></div></td>							
								<td colspan=2 width='100%' valign=top>
								<div id='divarea' style='width:100%;height:340;overflow:auto;padding:1px;padding-left:10px;padding-right:10px;'>				
								<table border=0 width=100% height=100%>
									<tr>
										<td width=50%><div ondragstart='return false'  id='reg_product' style='width:100%;height:100%;overflow:auto;padding:1px;padding-left:10px;padding-right:10px;border:1px solid silver;' align=center ><table width=100% height=100%><tr><td align=center>좌측카테고리를 선택해주세요</td></tr></table></div></td>
										<td width=50%>
											<div ondragstart='return false'  id='relation_product' style='width:100%;height:100%;overflow:auto;padding:1px;padding-left:10px;padding-right:10px;border:1px solid silver' ondragover=\"this.style.border='3px solid silver';\" 
ondragout=\"this.style.border='1px solid silver';\" dropzone='true' ondrop=\"onDropAction('insert','".$id."',arguments[0].id);\" >".relationProductList($id)."</div>
										</td>
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

//$Contents = colorCirCleBox("#efefef","100%","<div align=center>".colorCirCleBox("#ffffff","99%",$Contents)."</div>");
}else{	
	$help_text = "<img src='../image/0.gif' height=18 width=0 align=absmiddle><img src='../image/icon_dot3.gif' border=0 align=absmiddle> 아래의 기본적인 상품 등록을 완료하신후 <b style='color:0074ba'>옵션 추가하기</b>, <b style='color:0074ba'>이미지 추가하기</b>, <b style='color:0074ba'>디스플레이 옵션셜정 추가하기</b> 및 <b style='color:0074ba'>카테고리 등록</b> 을 하실수 있습니다.<br>
		<img src='../image/0.gif' height=18 width=0 align=absmiddle><img src='../image/icon_dot3.gif' border=0 align=absmiddle> 한개의 제품을 등록하신후 다중 카테고리 등록이 가능합니다.<br>";
	
	
	$Contents .= HelpBox("상품등록관리", $help_text);
}


				
$Contents = $Contents."
			<!--form name='product_input' action='product_input.act.php' method='post' enctype='multipart/form-data'-->
			<form name=product_input action='product_input.act.php' method='post' enctype='multipart/form-data' onsubmit='return SubmitX(this);'>
			<input type='hidden' name=act value='insert'><input type='hidden' name=id value='".$id."'>
			<table width=100%>
			<tr height=50 align=left>
				<td width=500><a href=\"JavaScript:PoPWindow('/shop/goods_view.php?id=".$id."',980,800,'comparewindow');\">보기</a></td>";
if ($id == "" ){				
$Contents = $Contents."
				<td align=right><img src='../image/b_save.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'insert');\"></td>";
}else{
$Contents = $Contents."
				<td align=right><img src='../image/b_edit.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'update')\"> <img src='../image/b_del.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'delete')\"></td>";
}				
$Contents = $Contents."
			</tr>
			</table>
			<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 기본정보</b></td><td align=right style='padding-right:20px;'><a href='JavaScript:SampleProductInsert()'>샘플데이타 넣기</a></td></tr></table>")."</td></tr></table>
			<table cellpadding=5 cellspacing=1 bgcolor=#ffffff border=0 width='100%'>				
				<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\" width=15% nowrap><img src='../image/ico_dot.gif' align=absmiddle> 제품명 *</td>
					<td width='35%'><input type=text class='textbox' name=pname size=28 style='width:100%'></td>
					<td width='15%' bgcolor=\"#efefef\" rowspan=7 size=28 nowrap><img src='../image/ico_dot.gif' align=absmiddle> 제품간략소개 *</td>
					<td width='35%' rowspan=7><textarea name=\"shotinfo\" rows=\"9\" cols=\"30\" style='width:100%' class='textbox'>".$shotinfo."</textarea></td>
				</tr>
				<tr height=1><td colspan=2 background='../image/dot.gif'></td></tr>
				<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 제품코드 *</td>
					<td><input type=text class='textbox' name=pcode size=28 style='width:100%' value='$pcode'></td>
				</tr>
				<tr height=1><td colspan=2 background='../image/dot.gif'></td></tr>
				<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 제조사 / 구입업체 *</td>
					<td><input type=text class='textbox' name=company size=28 style='width:100%'></td>
				</tr>
				<tr height=1><td colspan=2 background='../image/dot.gif'></td></tr>
				<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 사은품 </td>
					<td><input type=text class='textbox' name=gift size=28 style='width:100%'></td>
				</tr>
				<tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
				<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 상태체크 </td>
					<td>
					<input type=\"checkbox\" name='onew' class=nonborder id=onew value=1><!--label for=\"onew\">new</label--><img src='/icon/icon_new.gif' align='absmiddle' style='vertical-align:middle'> <input type=checkbox class=nonborder name=hot id=hot value=1><!--label for=\"hot\">hot</label--><img src='/icon/icon_hot.gif' style='vertical-align:middle'> <input type=checkbox class=nonborder name=event id=event value=1><!--label for=\"event\" background='/icon/icon_event.gif' style='width:50px;'></label--> <img src='/icon/icon_event.gif'  style='vertical-align:middle'>
					<!--input type=\"checkbox\" name=disp class=nonborder id=disp value=1><label for=\"disp\">표시</label--> 
					</td>
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
				<tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>				
				<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 진열 </td>
					<td >".displayProduct($disp)."</td>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 판매상태 </td>
					<td >".SellState($state)."</td>
				</tr>
				<tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
			</table><br>
			<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 부가정보</b></div>")."</td></tr></table>
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
					<td colspan=3><input type=text class='textbox' name='search_keyword' size=28 style='width:100%' value='$search_keyword'></td>
				</tr>
				<tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
			</table><br>	
			<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 가격정보</b></div>")."</td></tr></table>
			<table cellpadding=5 cellspacing=1 bgcolor=#ffffff width='100%'>
				<tr height=1><td colspan=7 background='../image/dot.gif'></td></tr>
				<tr bgcolor='#ffffff'>
					<td width=138 bgcolor=\"#efefef\" nowrap><img src='../image/ico_dot.gif' align=absmiddle> 판매가격 *</td>
					<td width=130 valign=botto><input type=hidden name=bsellprice><img src='../image/icon_won.gif' align=absmiddle> <input type=text class='textbox' name=sellprice size=13 onkeypress='num_check()'  maxlength=16 onkeyup='this.value=FormatNumber3(this.value);' style='TEXT-ALIGN:right '></td>
					<td width=80 bgcolor=\"#efefef\" nowrap>".($admininfo[admin_level] == 9 ? "구매단가 *":"공급가격 *")."</td>
					<td width=130><input type=text class='textbox' name=coprice size=13 style='text-align:right' onkeypress='num_check()' onkeyup='this.value=FormatNumber3(this.value);calcurate_maginrate(document.product_input)'><input type=hidden name=bcoprice></td>
					<td width=50 bgcolor=\"#efefef\" nowrap>마진</td>
					<td nowrap> <input size=5 type=text class='textbox' name='basic_margin' style='text-align:right' readonly> %</td>
					<td>".($admininfo[admin_level] == 9 ? "<!--input type=button value='가격변동보기' style='font-size:12px;' onclick=\"PoPWindow('./priceinfo.php?id=$id&pname=$pname',500,600,'priceinfo')\"--><!--onclick=\"modalwin('/admin/priceinfo.php?id=$id',500,600);\"-->" : "" )."</td>
				</tr>
				
			</table>";
if ($admininfo[admin_level] != 9){
	$dispString = "style='display:none;'";
}
$Contents = $Contents."		
			<table cellpadding=5 cellspacing=1 bgcolor=#ffffff width='100%' ".$dispString.">
				<tr height=1><td colspan=7 background='../image/dot.gif'></td></tr>
				<tr bgcolor='#ffffff'>
					<td width=15% bgcolor=\"#efefef\" nowrap><img src='../image/ico_dot.gif' align=absmiddle> 회원할인금액 *</td>
					<td colspan=3 width=85%>
					<table >
					<tr><td>	회원가 : </td><td> <input type=text class='textbox' name=prd_member_price size=13 onkeypress='num_check()'  maxlength=16 onkeyup='this.value=FormatNumber3(this.value);' style='TEXT-ALIGN:right '></td></tr>
					<tr><td>딜러 : </td><td> <input type=text class='textbox' name=prd_dealer_price size=13 onkeypress='num_check()'  maxlength=16 onkeyup='this.value=FormatNumber3(this.value);' style='TEXT-ALIGN:right '></td></tr>
					<tr><td>대리점 : </td><td><input type=text class='textbox' name=prd_agent_price size=13 onkeypress='num_check()'  maxlength=16 onkeyup='this.value=FormatNumber3(this.value);' style='TEXT-ALIGN:right '></td></tr>
					</table>
					</td>					
				</tr>
				<!--tr bgcolor='#ffffff'>
					<td width=80 bgcolor=\"#efefef\" nowrap>추천할인액 *</td>
					<td width=130><input type=text class='textbox' name=recomm_saveprice size=13 onkeypress='num_check()'  maxlength=16 onkeyup='this.value=FormatNumber3(this.value);' style='TEXT-ALIGN:right '></td>
					<td width=90 bgcolor=\"#efefef\" nowrap>추천적립금액 *</td>
					<td width=130><input type=text class='textbox' name=recomm_reserve size=13 style='text-align:right' onkeypress='num_check()' onkeyup='this.value=FormatNumber3(this.value);calcurate_maginrate(document.product_input)'></td>					
				</tr-->
				<tr height=1><td colspan=7 background='../image/dot.gif'></td></tr>
				<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 적립금 *</td>
					<td nowrap>
					(현금사용시 적립금)
					<input type=text class='textbox' name=reserve value='' size=10 style='text-align:right' readonly><input type=hidden name=breserve value='' size=15 style='text-align:right' readonly> &nbsp;&nbsp;
					<select name=rate1 >
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
					
					(카드사용시 적립금)
					<input type=text class='textbox' name=reservebycard value='' size=10 style='text-align:right' readonly><input type=hidden name=breservebycard value='' size=15 style='text-align:right' readonly> &nbsp;&nbsp;
					<select name=rate2 >
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
					&nbsp;&nbsp; <!--button onclick='calcurate(document.product_input);'>적용</button--></td>
				</tr>
				<tr height=1><td colspan=7 background='../image/dot.gif'></td></tr>
				
				<tr height=1><td colspan=7 background='../image/dot.gif'></td></tr>
				<tr bgcolor='#ffffff'>
					<td bgcolor=\"#efefef\"><img src='../image/ico_dot.gif' align=absmiddle> 판매수수료 상세</td>
					<td colspan=3 nowrap> 
					<table cellpadding=0 cellspacing=0 border=0>
					<tr align=center>
						<td><input type=text class='textbox' name=card_price value='' style='text-align:right' size=8 readonly></td><td width=30 align=center> + </td>
						<td><input size=8 type=text class='textbox' name='reserve_price' style='text-align:right' readonly></td><td width=30 align=center> + </td>
						<td><input size=8 type=text class='textbox' name='nointerest_price' style='text-align:right' value='' readonly></td><td width=30 align=center> = </td>						
						<td><input size=8 type=text class='textbox' name='margin' style='text-align:right' value='' readonly></td><td width=30 align=center>  </td>						
						<td> &nbsp;<input type=button onclick='calcurate_margin(document.product_input);' value='계산하기'></td>
					</tr>
					<tr>
						<td nowrap>카드수수료(<input type=text class='textbox' name=card_pay value='4' size=2 readonly style='border:0px;text-align:center'>%)</td><td width=30 align=center> + </td>
						<td nowrap>적립금 사용(현금)</td><td width=30 align=center> + </td>
						<td nowrap>무이자 수수료</td><!--td width=30 align=center> + </td>
						<td nowrap>기본마진</td--><td width=30 align=center> = </td>
						<td nowrap> 토탈 수수료</td><td> &nbsp;</td>
					</tr>
					</table>
					</td>
					
				</tr>
				<tr height=1><td colspan=7 background='../image/dot.gif'></td></tr>
			</table><br>";



$Contents .="<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 제품상세정보</b></div>")."</td></tr></table>
		<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>
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
$Contents = $Contents."	<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 이미지 추가</b> <img src='../image/icon_q.gif' border=0 align=absmiddle> * 500*500 이미지를 등록하시면 체크된 작은 이미지는 자동으로 등록됩니다.</div>")."</td></tr></table>
			<table cellpadding=5 cellspacing=0 bgcolor=#ffffff width=100%>
				<!--tr height=2 ><td colspan=4><img src='../image/icon_q.gif' border=0 align=absmiddle> * 500*500 이미지를 등록하시면 작은 이미지는 자동으로 등록됩니다.</td></tr-->
				<tr height=1><td colspan=4 background='../image/dot.gif'></td><td rowspan=10 width=100% style='border:1px solid silver' align=center valign=middle id=viewimg><img src='".$admin_config[mall_data_root]."/images/product/m_".$id.".gif' id=chimg></td></tr>
				<tr bgcolor='#ffffff' height=50>				
					<!--td width=100 bgcolor=\"#efefef\"  nowrap></td-->
					<td width=100 bgcolor=\"#efefef\"  class=small nowrap><img src='../image/ico_dot.gif' align=absmiddle > 500×500 *<br>이미지복사<input type=checkbox name='chk_allimg' value=1 id='copy_allimg' onclick='copyImageCheckAll();'></td>
					<td width=300><input type=file name='allimg' size=20 style='font-size:8pt'></td>
					<td width=5% $img_view_style><img src='../image/view_img.gif' onclick=\"ChnageImg('b','".$id."', '".$admin_config[mall_data_root]."/images/product');\" style='cursor:hand'></td>
				</tr>
				<tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
				<tr bgcolor='#ffffff' height=50>				
					<!--td bgcolor=\"#efefef\" ></td-->
					<td bgcolor=\"#efefef\"  class=small nowrap><img src='../image/ico_dot.gif' align=absmiddle > 300×300 *<br>이미지복사<input type=checkbox name='chk_mimg' id='copy_img' value=1></td>
					<td><input type=file name='mimg' size=20 style='font-size:8pt'></td>
					<td $img_view_style><img src='../image/view_img.gif' onclick=\"ChnageImg('m','".$id."', '".$admin_config[mall_data_root]."/images/product');\" style='cursor:hand'></td>
				</tr>
				<tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
				<tr bgcolor='#ffffff' height=50>				
					<!--td bgcolor=\"#efefef\" ></td-->
					<td bgcolor=\"#efefef\"  class=small nowrap><img src='../image/ico_dot.gif' align=absmiddle > 137×137 *<br>이미지복사<input type=checkbox name='chk_msimg' id='copy_img' value=1></td>
					<td><input type=file name='msimg' size=20 style='font-size:8pt'></td>
					<td $img_view_style><img src='../image/view_img.gif' onclick=\"ChnageImg('ms','".$id."', '".$admin_config[mall_data_root]."/images/product');\" style='cursor:hand'></td>
				</tr>
				<tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
				<tr bgcolor='#ffffff' height=50>				
					<!--td bgcolor=\"#efefef\" ></td-->
					<td bgcolor=\"#efefef\" class=small  nowrap><img src='../image/ico_dot.gif' align=absmiddle >  90×90 *<br>이미지복사<input type=checkbox name='chk_simg' id='copy_img' value=1></td>
					<td><input type=file name='simg' size=20 style='font-size:8pt'></td>
					<td $img_view_style><img src='../image/view_img.gif' onclick=\"ChnageImg('s','".$id."', '".$admin_config[mall_data_root]."/images/product');\" style='cursor:hand'></td>
				</tr>
				<tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
				<tr bgcolor='#ffffff' height=50>				
					<!--td bgcolor=\"#efefef\" ></td-->
					<td bgcolor=\"#efefef\"  class=small nowrap><img src='../image/ico_dot.gif' align=absmiddle > 50×50 *<br>이미지복사<input type=checkbox name='chk_cimg' id='copy_img' value=1></td>
					<td><input type=file name='cimg' size=20 style='font-size:8pt'></td>
					<td $img_view_style><img src='../image/view_img.gif' onclick=\"ChnageImg('c','".$id."', '".$admin_config[mall_data_root]."/images/product');\" style='cursor:hand'></td>
				</tr>
				<tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
				<tr height=1></td></tr>
			</table>";
if (false){
$Contents = $Contents."	<table cellpadding=5 cellspacing=1 border=0 bgcolor=#ffffff width=100%>
				<tr height=2 ><td colspan=6 style='padding-top:30px;padding-bottom:10px;'> <img src='../image/icon_q.gif' border=0 align=absmiddle> 아래 이미지 사이즈를 클릭하시면 해당되는 사이즈의 이미지를 보실수 있습니다. 또한 해당 사이즈의 이미지만을 수정하실수 있습니다.</td></tr>
				<tr height=2 bgcolor=gray><td colspan=6></td></tr>
				<tr bgcolor='#ffffff' height=28>
					<td valign=top width=200>
					<table cellpadding=5 cellspacing=1 border=0 bgcolor=#ffffff width='200' >
						<tr height=2 bgcolor=gray><td ></td></tr>
						<tr bgcolor='#ffffff' height=28>
							<td bgcolor=\"#efefef\" onclick=\"ChnageImg('b','".$id."', '".$admin_config[mall_data_root]."/images/product');\" style='cursor:hand'><img src='../image/ico_dot.gif' align=absmiddle>  이미지 500×500 *</td>							
						</tr>
						<tr height=28>
							<td bgcolor=\"#efefef\" onclick=\"ChnageImg('m','".$id."', '".$admin_config[mall_data_root]."/images/product');\" style='cursor:hand'><img src='../image/ico_dot.gif' align=absmiddle> 이미지 300×300 *</td>
						</tr>
						<tr height=28>
							<td bgcolor=\"#efefef\" onclick=\"ChnageImg('ms','".$id."', '".$admin_config[mall_data_root]."/images/product');\" style='cursor:hand'><img src='../image/ico_dot.gif' align=absmiddle> 이미지 137×137 *</td>
						</tr>
						<tr height=28>
							<td bgcolor=\"#efefef\" onclick=\"ChnageImg('s','".$id."', '".$admin_config[mall_data_root]."/images/product');\" style='cursor:hand'><img src='../image/ico_dot.gif' align=absmiddle> 이미지 90×90 *</td>
						</tr>
						<tr height=28>
							<td bgcolor=\"#efefef\" onclick=\"ChnageImg('c','".$id."', '".$admin_config[mall_data_root]."/images/product');\" style='cursor:hand'><img src='../image/ico_dot.gif' align=absmiddle> 이미지 50×50 *</td>
						</tr>
						</table>
					</td>
					<td rowspan=6 width=90% height=500 bgcolor=\"#efefef\" colspan=5 valign=top><div id=viewimg><img src='".$admin_config[mall_data_root]."/images/product/m_".$id.".gif' id=chimg><br><input type=file name='mimg' size=30 style='font-size:8pt'></div></td>
				</tr>				
			</table>";
}
			
$help_text = "<li> 500×500 이미지를 등록한후 이미지 복사를 클릭하시고 저장하시면 300×300, 137×137, 90×90, 50×50 이미지가 자동으로 생성됩니다. ";
$help_text .= "<li> 별도의 이미지 복사를 원하시면 이미지복사 체크를 푸신 상태에서 원하시는 이미지를 찾아서 등록하시면 됩니다.  ";
$Contents .= "<br>".HelpBox("상품등록관리", $help_text);
			
$Contents = $Contents."	
			<table width='100%'>			
			<tr height=30 align=left><td width=500></td>";


if ($id == "" ){				
$Contents = $Contents."
				<td align=right><img src='../image/b_save.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'insert');\"></td>";
}else{
$Contents = $Contents."
				<td align=right><img src='../image/b_edit.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'update')\"> <img src='../image/b_del.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'delete')\"></td>";
}				
$Contents = $Contents."				
			<!--td><img src='../image/b_save.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'insert');\"></td>
			<td><img src='../image/b_edit.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'update')\"></td>
			<td><img src='../image/b_del.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"ProductInput(document.product_input,'delete')\"-->
			</td></tr>
			</table>

			</form>";
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



$Script = "<script language='JavaScript' src='../webedit/webedit.js'></script>\n<script Language='JavaScript' src='../include/zoom.js'></script>\n<script Language='JavaScript' src='product_input.js'></script><script Language='JavaScript' src='addoption.js'></script>\n<script language='JavaScript' src='../js/dd.js'></script>\n
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
	
	$sql = "select c.cid,c.cname,c.depth, r.rid  from ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_SHOP_CATEGORY_INFO." c where pid = '$pid' and c.cid = r.cid ";
	
	
	$db->query($sql);
	
	$mString = "<table cellpadding=0 cellspacing=0 width=100% bgcolor=silver style='font-size:10px;'>";
	$mString .= "<tr align=center bgcolor=gray height=2><td colspan=5></td></tr>";
	$mString .= "<tr align=center bgcolor=#efefef height=25><td class='table_td small'>번호</td><td class='table_td small'>카테고리 ID</td><td class='table_td small'>카테고리</td><td class='table_td small'>삭제</td></tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=21><td colspan=4 align=center>선택된 카테고리 정보가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$parent_cname = GetParentCategory($db->dt[cid],$db->dt[depth]);
			$mString .= "<tr height=25 bgcolor=#ffffff><td class='table_td_white small' align=center>".($i+1)."</td><td class='table_td_white small'>".$db->dt[cid]."</td><td class='table_td_white small'>".($parent_cname != "" ? $parent_cname.">":"").$db->dt[cname]."</td><td class=table_td_white align=center><a href=\"JavaScript:deleteCategory('delete','".$db->dt[rid]."','$pid')\"><img src='../image/btc_del.gif' border=0></a></td></tr>";
			$mString .= "<tr height=1><td colspan=5 background='../image/dot.gif'></td></tr>";
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
	$mString = $mString."<tr height=1><td colspan=4 bgcolor=silver></td></tr>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td class=small>번호</td><td  class=small colspan=2>클립아트 ID</td><td  class=small>중간이미지</td><td  class=small>큰이미지</td><td  class=small>삭제</td></tr>";
	$mString = $mString."<tr height=1><td colspan=4 bgcolor=silver></td></tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=80><td colspan=6 align=center>입력된 추가이미지가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString = $mString."<tr height=25 bgcolor=#ffffff><td  align=center  class=small>".($i+1)."</td><td  ><img src='".$admin_config[mall_data_root]."/images/addimg/c_".$db->dt[id]."_add.gif' align=absmiddle style='border:1px solid gray'></td><td  class=small><a href=\"javascript:AddImageView('".$admin_config[mall_data_root]."/images/addimg/c_".$db->dt[id]."_add.gif')\">c_".$db->dt[id]."_add.gif</a></td><td  class=small><a href=\"javascript:AddImageView('".$admin_config[mall_data_root]."/images/addimg/m_".$db->dt[id]."_add.gif')\">m_".$db->dt[id]."_add.gif</a></td><td><a href=\"javascript:AddImageView('".$admin_config[mall_data_root]."/images/addimg/b_".$db->dt[id]."_add.gif')\">b_".$db->dt[id]."_add.gif</a></td><td align=center  class=small><a href=\"JavaScript:deleteAddimage('delete','".$db->dt[id]."','$pid')\"><img src='../image/btc_del.gif'></a></td></tr>";
			$mString = $mString."<tr height=1><td colspan=6 background='../image/dot.gif'></td></tr>";
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
	$mString .=  "<tr height=1><td colspan=6 background='../image/dot.gif'></td></tr>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td class=small>비회원가</td><td class=small>회원가</td><td class=small>딜러가</td><td class=small>대리점가</td><td class=small>재고</td><td class=small>안전재고</td></tr>";
	$mString .=  "<tr height=1><td colspan=9 ></td></tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=10 align=center>수정 /  추가 하시고자 하는 옵션이름을 선택해주세요</td></tr>";
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
				<a href=\"JavaScript:deleteOption('delete','".$db->dt[id]."','$pid')\"><img  src='../image/si_remove.gif' border=0></a>
			</td>
			</tr>
			<tr height=1><td colspan=9 background='../image/dot.gif'></td></tr>
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
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td class='s_td small' >번호</td><td class='m_td small' >옵션제목</td><td class='m_td small' >옵션설명</td><td class='e_td small' >관리</td></tr>";	
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=9 align=center class=small >입력된 디스플레이 옵션이 없습니다.</td></tr>";
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
			<tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
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
if ($vstate == 1 || $vstate == ""){	
	$Selectedstr00 = "";
	$Selectedstr01 = " selected";
}else{
	$Selectedstr00 = " selected";
	$Selectedstr01 = "";
}
return "
<Select name=state>
	<option value=0 $Selectedstr00>일시품절</option>
	<option value=1 $Selectedstr01>판매중</option>
</Select>";
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
<input type='radio' name='disp'  id='disp_0' value='0' ".ReturnStringAfterCompare($disp, "0", " checked")."><label for='disp_0'>진열안함</label>
<input type='radio' name='disp'  id='disp_1' value='1' ".ReturnStringAfterCompare($disp, "1", " checked")."><label for='disp_1'>일반몰</label>
<input type='radio' name='disp'  id='disp_0' value='2' ".ReturnStringAfterCompare($disp, "2", " checked")."><label for='disp_2'>포인트몰</label>
<input type='radio' name='disp'  id='disp_0' value='3' ".ReturnStringAfterCompare($disp, "3", " checked")."><label for='disp_3'>현금,포인트몰</label>
<input type='radio' name='disp'  id='disp_0' value='9' ".ReturnStringAfterCompare($disp, "9", " checked")."><label for='disp_9'>공동구매</label>

<!--Select name=state>
	<option value=0 $Selectedstr00>일시품절</option>
	<option value=1 $Selectedstr01>판매중</option>
</Select-->";
}



function relationProductList($pid){
	global $start,$page, $orderby, $admin_config;
	
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
	
	
	$db->query("SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,p.prd_member_price, p.prd_dealer_price, p.prd_agent_price,  p.reserve, rp_ix  FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_RELATION_PRODUCT." rp where p.id = rp.rp_pid and rp.pid = '$pid'  and p.disp = 1 order by vieworder limit $start,$max");		
	
	
	

	if ($db->total == 0){
		$mString = "<table cellpadding=0 cellspacing=0 width=100% height=100%  bgcolor=silver style='font-size:10px;'>";	
		$mString = $mString."<tr bgcolor=#ffffff height=100%><td colspan=5 align=center>등록된 관련 상품 정보가 없습니다 . <br> 좌측 상품을 이곳으로 드래그하시면 관련상품이 등록됩니다. </td></tr>";
	}else{
		$mString = "<table cellpadding=0 cellspacing=0 width=100% bgcolor=silver style='font-size:10px;'>";	
			
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);			
			$mString .= "<tr height=27 bgcolor=#ffffff >
						<td class=table_td_white align=center style='padding:5px;'>
							<img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif'>
						</td>						
						<td class=table_td_white>".cut_str($db->dt[pname],30)."</td>
						<td><a href='relation.category.act.php?mode=delete&pid=".$pid."&rp_ix=".$db->dt[rp_ix]."'  target=act><img src='../image/btc_del.gif'></a></td>
						</tr>";
			$mString .= "<tr height=1><td colspan=5 background='../image/dot.gif'></td></tr>";
		}
	}
	//$str_page_bar = product_page_bar($total, $page,$max, "&view=innerview&max=$max&cid=$cid");
	
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";
	$mString .= "<tr align=right bgcolor=#ffffff height=30><td colspan=5 align=left >$str_page_bar</td></tr>";
	$mString = $mString."</table>
	
	";
	
	return $mString;
	
}
?>