<?
include("../class/layout.class");


$Script = "
<style type='text/css'>
 
  div#droppable_demo { width:100%;height:100%;overflow:auto;padding:1px;padding-left:10px;padding-right:10px;border:1px solid silver }
  div#droppable_demo.hover { border:5px dashed #aaa; background:#efefef; }
  
  div#drop_relation_product { width:100%;height:100%;overflow:auto;padding:1px;padding-left:10px;padding-right:10px;border:1px solid silver }
  div#drop_relation_product.hover { border:5px dashed #aaa; background:#efefef; }
  table.tb {width:100%;cursor:hand;}
</style>
<script src='../js/prototype.js' type='text/javascript'></script>
<script src='../js/scriptaculous.js' type='text/javascript'></script>
<script type='text/javascript' src='relationAjax.js'></script>";



$Contents = "
			  	<!-- 카테고리 및 상품 검색 인터페이스 -->
			  	<form name='goods_search'>
					<div class='doong' id='relation_product_area' style='".($use_product_type == 2 ? "display:block;":"display:block;")."vertical-align:top;height:260px;padding-top:10px;'   >
					<table bgcolor=#ffffff border=0 cellpadding=0 cellspacing=0 width=100%' >
					<tr height=25 >
						<td width='15%' style='padding-right:5px;' valign=top>
							<div class='tab' style='margin: 0px 0px ;'>
								<table class='s_org_tab'>
								<tr>
									<td class='tab'>
										<table id='tab_01' class='on' >
										<tr>
											<th class='box_01'></th>
											<td class='box_02 small' onclick=\"showTabContents('category_search','tab_01')\" style='padding-left:5px;padding-right:5px;'>카테고리검색</td>
											<th class='box_03'></th>
										</tr>
										</table>
										<table id='tab_02'>
										<tr>
											<th class='box_01'></th>
											<td class='box_02 small' onclick=\"showTabContents('keyword_search','tab_02')\" style='padding-left:5px;padding-right:5px;'>키워드검색</td>
											<th class='box_03'></th>
										</tr>
										</table>										
									</td>
									<td class='btn'>						
										
									</td>
								</tr>
								</table>	
							</div>
							<div class='t_no' style='margin: 2px 0px ; '>
								<div class='my_box' >
									<div id='category_search' style='overflow:auto;height:370px;width:200px;border:1px solid silver'><iframe  src='relationAjax.category.php' width=100% height=100% frameborder=0 ></iframe></div>
									<div id='keyword_search' style='display:none;height:370px;width:200px;border:1px solid silver;padding-top:10px;'>
										
										<table align=center>
											<tr>
												<td bgcolor='#efefef' align=center>입점업체</td>
												<td>
													".CompanyList($company_id,"","")."
												</td>
											</tr>
											<tr>
												<td>
													<select name='search_type' id='search_type'>
														<option value='p.pname'>상품명</option>
														<option value='brand_name'>브랜드명</option>
													</select>
												</td>
												<td><input type='text' name='search_text' id='search_text' size='15' ></td>
											</tr>
											<tr>												
												<td colspan=2 align=right><img src='../image/search01.gif' onclick=\"SearchProduct(document.goods_search);\"></td>
											</tr>											
											</table>
											
									</div>								
								</div>
							</div>
							</td>
						<td colspan=2 width='100%' valign=top>						
						<table border=0 cellpadding=0 cellspacing=0 width=100% height=100% >
							<tr height=25>
								<td style='padding:1px;padding-left:10px;padding-right:10px;border:1px solid silver;border-bottom:0px;' align=center >
								<table width=100% height=100%><tr><td align=left width='10' ><input type=hidden id='cpid' value=''><input type=checkbox name='all_fix' onclick='fixAll(document.goods_search)' ></td><td id='view_paging' align=center></td></tr></table>
								</td>
								<td style='padding:0 0 0 5' rowspan=3></td>
							</tr>
							<tr height=92%>
								<td width=50%>
								<div id='reg_product' style='width:100%;height:350px;width:100%;height:100%;padding:1px;padding-left:10px;padding-right:10px;border:1px solid silver;' align=center >
								<table width=100% height=100%><tr><td align=center class='small'>좌측카테고리를 선택해주세요</td></tr></table>
								</div><!--ondragstart='return false' onselectstart='return false' -->
								</td>
								<td width=50% style='padding:0 0 0 0'>
									<div id='drop_relation_product'  >
									".relationCouponProductList($publish_ix)."
									</div><!--ondragover=\"this.style.border='3px solid silver';\" ondragout=\"this.style.border='1px solid silver';\" dropzone='true' ondrop=\"onDropAction('insert','".$event_ix."',arguments[0].id);\" ondragstart='return false' onselectstart='return false' -->
								</td>
							</tr>
							<tr height=25>
								<td style='padding:1px;padding-left:0px;padding-right:2px;border:1px solid silver;border-top:0px;' align=center >
								<img src='../image/btn_selected_reg.gif' border='0' align='left' onclick='selectGoodsList(document.goods_search);' style='cursor:hand;'>
								<!--img src='../image/btn_searched_reg.gif' border='0' align='right'-->
								<select name='list_max' id='list_max' align=right onchange='getRelationProduct(_mode,_nset, _page,_cid,_depth);'>
									<option value='3'>3</option>
									<option value='5'>5</option>
									<option value='10'>10</option>
									<option value='15'>15</option>
									<option value='20'>20</option>
									<option value='30'>30</option>
									<option value='40'>40</option>
									<option value='50'>50</option>
									<option value='100'>100</option>
								</select>
								</td>
								
								<td style='padding:1px;padding-left:0px;padding-right:2px;border:1px solid silver;border-top:0px;'><img src='../image/btn_whole_del.gif' border='0' align='left' onclick='deleteWhole();' style='cursor:hand;'></td>
								</tr>
						</table>
						<!--/div-->
						</td>
					</tr>					
					</table>
					</div></form>
					<!-- 카테고리 및 상품 검색 인터페이스 -->
			  	
  ";



$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "HOME > 마케팅지원 > 쿠폰관리";
$P->NaviTitle = "상품검색";
$P->layout_display = false;	
$P->strContents = $Contents;
echo $P->PrintLayOut();


function SelectCuponKind($select_ix){	
	$mdb = new Database;
		
	$mdb->query("SELECT * FROM ".TBL_SHOP_CUPON." order by cupon_ix asc");	
	$mstring =  "<select name='cupon_ix' id='cupon_ix' style=\"font-size:12px;height: 20px; width: 300px;\" align='middle'><!--behavior: url('../js/selectbox.htc'); -->";
			
       $mstring .=  "     <option value=''>ㆍ선택ㆍㆍㆍㆍㆍㆍㆍ</option>";
        	
    for($i=0;$i < $mdb->total;$i++){   
	 	$mdb->fetch($i);	 	
	 	if($select_ix == $mdb->dt[cupon_ix]){
	    	$mstring .= "       <option value='".$mdb->dt[cupon_ix]."' selected>ㆍ".$mdb->dt[cupon_kind]."</option>\n";    
		}else{
			$mstring .= "       <option value='".$mdb->dt[cupon_ix]."'>ㆍ".$mdb->dt[cupon_kind]."</option>\n";    	
		}
	}
    $mstring .= "</select>";
    
    return $mstring;
	
}


function relationCouponProductList($publish_ix){
	global $admin_config;
	$db = new Database;
	
	$sql = "Select crp.*, p.pname from ".TBL_SHOP_PRODUCT." p, shop_cupon_relation_product crp where p.id = crp.pid and publish_ix = '".$publish_ix."' ";	
	$db->query($sql);
	
	
 
	$mString .= "<table id=tb_relation_product class=tb>
								<col width=50>";
								
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);			
		
		$mString .= "
					<tr height=27 bgcolor=#ffffff ondblclick=\"$('tb_relation_product').deleteRow(this.rowIndex);$('tb_relation_product').deleteRow(this.rowIndex);	\">
					<td class=table_td_white align=center style='padding:5px;'>
						<img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[pid].".gif'>
					</td>						
					<td class=table_td_white>".cut_str($db->dt[pname],30)."</td>			
					<td class=table_td_white><input type='hidden' name='rpid[]' value='".$db->dt[pid]."'></td>					
					</tr><tr height=1><td colspan=5 background='../image/dot.gif'></td></tr>";
	}
	
	
	$mString .= "</table>";
	
	return $mString;
	
}

?>