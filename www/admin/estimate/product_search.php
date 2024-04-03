<?
include("../class/layout.class");

$db2 = new database;

if($max == ""){	//해당페이지 노출개수	
	$max = 10; 
}

$mode="search";

include ("../product/product_query.php");//상품노출 쿼리 실행 페이지


if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

$str_page_bar = page_bar($total, $page, $max,$query_string,"");


$Script = "
<script language='JavaScript' >

	if(window.dialogArguments){
		var opener = window.dialogArguments;
	}else{
		var opener = window.opener;
	}

	$(document).ready(function() {
		$('#search_text').focus();

		$('input[name=mult_search_use]').click(function (){
			var value = $(this).attr('checked');

			if(value == 'checked'){
				$('#search_text_input_div').css('display','none');
				$('#search_text_area_div').css('display','');
				
				$('#search_text_area').attr('disabled',false);
				$('#search_texts').attr('disabled',true);
			}else{
				$('#search_text_input_div').css('display','');
				$('#search_text_area_div').css('display','none');

				$('#search_text_area').attr('disabled',true);
				$('#search_texts').attr('disabled',false);
			}
		});

		var mult_search_use = $('input[name=mult_search_use]:checked').val();
		
		if(mult_search_use == '1'){
			$('#search_text_input_div').css('display','none');
			$('#search_text_area_div').css('display','');

			$('#search_text_area').attr('disabled',false);
			$('#search_texts').attr('disabled',true);
		}else{
			$('#search_text_input_div').css('display','');
			$('#search_text_area_div').css('display','none');

			$('#search_text_area').attr('disabled',true);
			$('#search_texts').attr('disabled',false);
		}

		$('.product_select').click(function(){
			var data = {};
			data['options'] = {};

			var j = 0;
			data['pid'] = $(this).attr('pid');
			data['pcode'] = $(this).attr('pcode');

			data['pname'] = $(this).attr('pname');
			data['com_name'] = $(this).attr('com_name');
			data['delivery_package'] = $(this).attr('delivery_package');
		
			data['options'][j] =  {'pcount':'1'};

			//교환요청에서 타상품선택시!
			//window.returnValue = JSON.stringify(data); //모달팝업은 창닫으면 리턴값을 받을수 있음
			takeOpionData(data);
			//window.close();
		})
	});
	
	
	function takeOpionData(data){

		opener.takeOpionData(data);
		self.close();
	}
</Script>";

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("상품정보 검색", "상품정보 검색", false)."</td>
			</tr>
			<tr height=30><td class='p11 ls1' style='padding:0 0 0 5px;'  align='left' > -  등록하고자 하는 상품을 검색해주세요</td></tr>
			<tr>
				<td align=center style='padding: 0 5px 0 5px'>
					<form name='z' method='get' onSubmit='return CheckSearch(this)' >
					<input type='hidden' name='company_id' value='".$company_id."'>
					<input type='hidden' name='surtax_yorn' value='".$surtax_yorn."'>
					<table class='box_shadow' style='width:100%;' cellpadding=0 cellspacing=0>
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05' align=right style='padding: 0 20 0 20'>
								<table border='0' width='100%' cellspacing='0' cellpadding='0' class='input_table_box'>
									<col width='220'>
									<col width='*'>
									<tr>
										<td class='search_box_title' > 선택된 카테고리  </td>
										<td class='search_box_item' >  <b id='select_category_path1'>".($search_text == "" ? getCategoryPathByAdmin($cid2, $depth)."(".$total."개)":"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."</b></div>  </td>
									</tr>
									<tr>
										<td class='input_box_title'>
											<div style='float:left;padding-top:5px;'><b>카테고리선택</b></div>
											<div style='float:left;padding-left:20px;'>
												<img src='../images/icon/search_icon.gif' value='검색' onclick=\"ShowModalWindow('../product/search_category.php?group_code=',600,600,'add_brand_category')\" style='cursor:pointer;'>
											</div>
										</td>
										<td class='input_box_item' >
											<div id='selected_category_6' style='padding:10px;overflow-y:scroll;max-height:100px;'>
											<table width='98%' cellpadding='0' cellspacing='0' id='objMd'>
											<colgroup>
												<col width='*'>
												<col width='50'>
											</colgroup>
											<tbody>";
												if(count($cid) > 0){
										
													for($k=0;$k<count($cid);$k++){

														$re_cid = $cid[$k];
														$sql = "select * from shop_category_info where cid = '".$re_cid."'";
														$db2->query($sql);
														$db2->fetch();
														$depth = $db2->dt[depth];
													
														for($i=0;$i<=$depth;$i++){
															$this_cid = substr(substr($re_cid, 0,($i*3+3)).'000000000000',0,15);
															$sql = "select * from shop_category_info where cid = '".$this_cid."'";
															$db2->query($sql);
															$db2->fetch();
															$cname = $db2->dt[cname];
															$relation_cname[$k] .= $cname." > ";
														}
									
										$Contents .= "<tr style='height:26px;' id='row_".$re_cid."'>
															<td>
															<input type='hidden' name='cid[]' id='cid_".$re_cid."' value='".$re_cid."'>".$relation_cname[$k]."</td><td><a href='javascript:void(0)' onclick=\"cid_del('".$re_cid."')\"><img src='./images/".$admininfo["language"]."/btc_del.gif' border='0'></a>
															</td>
														</tr>";
													}
												}
									$Contents .= "
												</tbody>
												</table>
											</div>
										</td>
									</tr>
									<tr>
										<td class='search_box_title'>  검색어
										<span style='padding-left:2px' class='helpcloud' help_width='220' help_height='30' help_html='검색하시고자 하는 값을 ,(콤마) 구분으로 넣어서 검색 하실 수 있습니다'><img src='/admin/images/icon_q.gif' align=absmiddle/></span>
										<input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> <label for='mult_search_use'>(다중검색 체크)</label>
										</td>
										<td class='search_box_item'>
											<table cellpadding=0 cellspacing=0 border='0'>
											<col width='120'>
											<col width='*'>
											<tr>
												<td valign='top'>
													<div style='padding-top:5px;'>
													<select name='search_type' id='search_type'  style=\"font-size:12px;\">
													<option value='p.pname' ".CompareReturnValue("p.pname",$search_type).">상품명</option>
													<option value='p.pcode' ".CompareReturnValue("p.pcode",$search_type).">상품코드</option>
													<option value='p.id' ".CompareReturnValue("p.id",$search_type).">상품시스템코드</option>
													<option value='pod.option_code' ".CompareReturnValue("pod.option_code",$search_type).">옵션코드</option>
													<option value='ccd.com_name' ".CompareReturnValue("ccd.com_name",$search_type).">셀러명</option>
													<option value='b.brand_name' ".CompareReturnValue("brand_name",$search_type).">브랜드명</option>
													</select>
													</div>
												</td>
												<td style='padding:5px;'>
													<div id='search_text_input_div'>
														<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
													</div>
													<div id='search_text_area_div' style='display:none;'>
														<textarea name='search_text' name='search_text_area' id='search_text_area' class='tline textbox' style='padding:0px;height:90px;width:150px;' >".$search_text."</textarea>
													</div>
												</td>
											</tr>
											<tr>
												<td colspan='2'>
													<div>
														<span class='small blu' > * 다중 검색은 다중 아이디로 검색 지원이 가능합니다. 구분값은 ',' 혹은 'Enter'로 사용 가능합니다. </span>
													</div>
												</td>
											</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
							<th class='box_06'></th>
						</tr>
						<tr>
							<th class='box_07'></th>
							<td class='box_08'></td>
							<th class='box_09'></th>
						</tr>
						<tr >
							<td colspan=3 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
						</tr>
					</table>
					</form>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr height=30>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> ".($search_text == "" ? "":"'".$search_text ."' 로 검색된 결과 입니다.")."</td>
		<td class='p11 ls1' style='padding:0 0 0 5px;text-align:right;' nowrap>
		</td>
	</tr>
	<tr>
		<td colspan=2 style='padding: 0 5px 0 5px' width=100% valign=top>
		<table width=100% class='list_table_box'>
		<col width='10%'>
		<col width='*'>
		<col width='10%'>
		<col width='10%'>
		<col width='10%'>
		<col width='7%'>
		<tr height='28' bgcolor='#ffffff'>
			<td align='center' class=m_td ><font color='#000000'><b>상품구분</b></font></td>
			<td align='center' class='m_td' ><font color='#000000'><b>상품명</b></font></td>
			<td align='center' class=m_td ><font color='#000000'><b>상품상태</b></font></td>
			<td align='center' class=m_td ><font color='#000000'><b>상품할인가</b></font></td>
			<td align='center' class=m_td ><font color='#000000'><b>상품가격</b></font></td>
			<td align='center' class=m_td ><font color='#000000'><b>옵션선택</b></font></td>
		</tr>";

 
if($total > 0){

	for ($i = 0; $i < count($goods_datas); $i++){



		switch($goods_datas[$i][state]){
			case '1':
				$state_text = "판매중";
			break;
			case '0':
				$state_text = "임시품절";
			break;
			case '2':
				$state_text = "판매중지";
			break;
			case '6':
				$state_text = "승인대기";
			break;
			case '8':
				$state_text = "승인거부";
			break;
			case '9':
				$state_text = "판매거부";
			break;
			case '7':
				$state_text = "본사대기상품";
			break;

		}
		

		$sql = "select dt.delivery_package from shop_product_delivery pd left join shop_delivery_template dt on (pd.dt_ix=dt.dt_ix) where pd.pid = '".$goods_datas[$i][id]."' and pd.is_wholesale = 'R' order by pd.delivery_div limit 0,1";
		$db2->query($sql);
		$db2->fetch();
		$delivery_package = $db2->dt[delivery_package];

		if($delivery_package == 'N'){
			$use_bundle_text = '묶음배송';
		}else{
			$use_bundle_text = '개별배송';
		}
		
		$sql = "select * from shop_product_options where pid = '".$goods_datas[$i][id]."' and option_use='1' ";
		$db2->query($sql);
		if($db2->total){
			$option_select = "<input type='button' value='옵션선택' onclick=\"ShowModalWindow('/admin/estimate/goods_option_select.php?pid=".$goods_datas[$i][id]."&delivery_package=".$delivery_package."',450,700,'goods_option_select',true);\" />";
		}else{
			$option_select = "<input type='button' value='상품선택' class='product_select' pid='".$goods_datas[$i][id]."' pcode='".$goods_datas[$i][pcode]."' pname='".$goods_datas[$i][pname]."' com_name='".$goods_datas[$i][com_name]."' delivery_package='".$delivery_package."' />";
		}

		$Contents .= "
		<tr height=45 >
			<td class='list_box_td list_bg_gray'>".getProductType($goods_datas[$i][product_type])."</td>
			<td class='list_box_td' style='text-align:left;padding:0px 5px;line-height:150%;'><b>".$goods_datas[$i][com_name]."</b> <b class='red'>배송정책 : ".$use_bundle_text."</b> <br/>".$goods_datas[$i][pname]."</td>
			<td class='list_box_td '>".$state_text."</td>
			<td class='list_box_td list_bg_gray'>".number_format($goods_datas[$i][sellprice])."원</td>
			<td class='list_box_td point'>".number_format($goods_datas[$i][sellprice])."원</td>
			<td class='list_box_td'>".$option_select."</td>
		</tr>";

	}

}else{
	$Contents .= "<tr align=center height=30>
				<td colspan=7 align=center> 상품정보가 존재 하지 않습니다.</td>	
			</tr>";
}



$Contents .= "
		</table>
		</td>
	</tr>
	<tr>
		<td align=center style='padding:0 10px 0 0px' colspan=2>
		</td>
	</tr>

	<tr>
		<td align=left style='padding:10px 10px 0 5px' >
			<!--img src='../images/".$admininfo["language"]."/btn_goods_intoon.gif' border='0' align='absmiddle' onclick='GoodsSelectAll()' style='cursor:pointer;'-->
		</td> 
		<td align=right style='padding:10px 0 0 0' >
			".$str_page_bar."
		</td>
	</tr>
	<tr>
		<td align=left style='padding:10px 10px' colspan=2>";

$help_text = "
<table cellpadding=2 cellspacing=0 class='small' style='line-height:120%' >
	<col width=8>
	<col width=*>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td>원하는 삼품을 검색후 선택하실수 있습니다.</td></tr>
</table>
";



$Contents .= HelpBox("상품선택", $help_text,"50");
$Contents .= "
		</td>
	</tr>
</TABLE>
";




$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "상품선택";
$P->NaviTitle = "상품선택";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>w