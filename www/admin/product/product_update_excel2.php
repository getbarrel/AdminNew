<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include("../class/layout.class");
 
include("../product/product_input_excel.lib.php");

//include("excel_out_columsinfo.php");

/*
//echo memory_get_usage();	//2877696	/12154280
function convert($size){ 
$unit=array('b','kb','mb','gb','tb','pb'); 
return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i]; 
} 
echo convert(memory_get_usage(true)); 
*/
//unset($colums);
//echo "<pre>";
//		print_r($colums);exit;
$page_type = "update_download";
if($_COOKIE["product_update_limit"]){
	$max = $_COOKIE["product_update_limit"]; //페이지당 갯수
}else{
	$max = 10;
}

if ($page == '')
{
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$db = new Database;
$db2 = new Database;

if(!$up_mode){
	$up_mode="new_upload";
}

$Contents =	"<table cellpadding=0 cellspacing=0 width='100%'>
			<tr>
				<td align='left' colspan=4 > ".GetTitleNavigation("대량상품등록", "상품관리 > 대량엑셀수정")."</td>
			</tr>
			<tr>
				<td align='left' colspan=4 style='padding-bottom:15px;'>
					<div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_02' ".($up_mode=="new_upload" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?up_mode=new_upload'\">상품 엑셀 등록 수정</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' ".($up_mode=="download" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?up_mode=download'\">상품 엑셀다운로드 하기</td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
						<td class='btn'>
						</td>
					</tr>
					</table>
					</div>
				</td>
			</tr>
			</table>";

if($up_mode == "download"){

include "../product/product_query.php";
include "../product/product_list_search.php";



if($admininfo[admin_level] == '9'){
	$info_type = 'company';
}else{	
	$info_type = 'seller';
}


$Contents .= "	

<form name='listform' method='post' action='product_update_exceldown2.php' onsubmit='return SelectUpdate(this)' target=''><!--onsubmit='return CheckDelete(this)'iframe_act -->
	<input type='hidden' name='search_searialize_value' value='".($_GET[mode] == "search" || $_GET[mode] == "excel_search" ? urlencode(serialize($_GET)):"")."'>
	<input type='hidden' name='act' value=''>
	<input type='hidden' name='cid2' value='$cid2'>
	<input type='hidden' name='depth' value='$depth'>
	<input type='hidden' name='product_type' value='$product_type'>
	<input type='hidden' name='max' value='$max'>
	<input type='hidden' name='info_type' value='".$info_type."'>

	<table border=0 cellpadding=0 cellspacing=0 width='100%'>
	<tr>
		<td valign=top>
		</td>
		<td valign=top style='padding:0px;padding-top:0px;' id=product_list>
			<table width='100%' cellpadding=0 cellspacing=0 border=0>
			<col width=15%>
			<col width=50%>
			<col width=25%>
			<col width=10%>
			<tr>
				<td>상품수 : ".number_format($total)." 개</td>
				<td align=left height=30>

				</td>
				<td align=right>";
$select = "
				<select name='update_type'>
					<option value='2' selected>선택한 상품 전체에</option>
					<option value='1'>검색한 상품 전체에</option>
				</select>
				";
				
	

$Contents .= "
				</td>
				<td align=right>
				
				</td>
			</tr>
			</table>
		</td>
		</tr>
	</table>


	<table cellpadding=2 cellspacing=0 bgcolor=gray border=0 width=100% class='list_table_box'>
		<col width=3%>
		<col width=11%>
		<col width=13%>
		<col width=*>
		<col width=6%>
		<col width=6%>
		<col width=9%>
		<col width=9%>
		<col width=8%>
		<col width=8%>
	<tr bgcolor='#ffffff' align=center height=35>
		<td class=s_td><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
		<td class=m_td>등록일</td>
		<td class=m_td>셀러명/ID</td>
		<td class=m_td>카테고리/상품정도</td>
		<td class=m_td>판매상태</td>
		<td class=m_td>상세페이지</td>
		<td class=m_td>도매가/할인가</td>
		<td class=m_td>소매가/할인가</td>
		<td class=m_td>재고관리/수량</td>
		<td class=e_td>관리</td>
	</tr>
	";

if(count($goods_datas) == 0){

	$Contents .= "<tr bgcolor=#ffffff height=150><td colspan=10 align=center> 등록된 상품이 없습니다.</td></tr>";

}else{

	for ($i = 0; $i < count($goods_datas); $i++){
		//$db->fetch($i);
		
		/*
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $goods_datas[$i][id], "s", $goods_datas[$i])) || $image_hosting_type=='ftp'){
			$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $goods_datas[$i][id], "s", $goods_datas[$i]);
		}else{
			$img_str = "../image/no_img.gif";
		}*/
		//$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $goods_datas[$i][id], "s", $goods_datas[$i]);
        $img_str = PrintImage($admin_config[mall_data_root]."/images/addimgNew", $goods_datas[$i][id], "slist", $goods_datas[$i]);

		switch($goods_datas[$i][state]){	
			case '1':
				$state = "판매중";
			break;
			case '0':
				$state = "일시품절";
			break;
			case '2':
				$state = "판매중지";
			break;
			case '7':
				$state = "수정대기상품";
			break;
			case '6':
				$state = "승인대기";
			break;
			case '8':
				$state = "승인거부";
			break;
			case '9':
				$state = "판매금지";
			break;
		}

		if($goods_datas[$i][basicinfo] != ""){
			$basicinfo_state = "정상";
		}else{
			$basicinfo_state = "파일없음";
		}
		
		switch($goods_datas[$i][stock_use_yn]){
			case 'Y':
				$stock_use_yn = 'WMS 사용';
			break;
			case 'Q':
				$stock_use_yn = '빠른재고';
			break;
			case 'N':
				$stock_use_yn = '사용안함';
			break;
		}
		
		$sql = "select 
					cu.id as user_id
				from
					common_user as cu 
				where
					cu.company_id = '".$goods_datas[$i][admin]."'
					and cu.code = '".$goods_datas[$i][charge_code ]."'";
		$db2->query($sql);
		$db2->fetch();
		$charge_id = $db2->dt[user_id];

		$Contents .= "
				<tr>
					<td class='list_box_td list_bg_gray' bgcolor='#efefef' align=center>
						<input type=checkbox class=nonborder id='cpid' name=select_pid[] value='".$goods_datas[$i][id]."'>
					</td>
					<td class='list_box_td'>".$goods_datas[$i][regdate]."</td>
					<td class='list_box_td'>".$goods_datas[$i][com_name]." <br> ".$charge_id."</td>
					<td class='list_box_td point' style='padding:10px 10px 10px 10px'>
						<table cellpadding=0 cellspacing=0 width='100%' border=0 align='left'>
						
						<tr>
							<td  colspan='2' style='text-align:left;'>
								<b>".getCategoryPathByAdmin($goods_datas[$i][cid], 4)."</b>
							</td>
						</tr>
						<tr>
							
							<td align='left'>
								<table border=0 cellpadding=0 cellspacing=0 width='100%' align='left'>
								<col width=14%>
								<col width=*>
								<tr height=15>
									<td align='left' rowspan=2 style='padding:5px 5px 0px 0px;'>
										<a href='/shop/goods_view.php?id=".$goods_datas[$i][id]."' target='_blank' class='screenshot'  rel='".PrintImage($admin_config[mall_data_root]."/images/product", $goods_datas[$i][id], $LargeImageSize, $goods_datas[$i])."'><img src='".$img_str."' width=50 height=50></a><br>
									</td>
									<td style='line-height:150%;'>
									".$goods_datas[$i][id]."<br>
									<a href='goods_input.php?id=".$goods_datas[$i][id]."' target='_self'>
										<b> ".$goods_datas[$i][pname]."</b>
									</a>
									</td>
								</tr>
								</table>
							</td>
						</tr>
						</table>
					</td>
					<td class='list_box_td'>".$state."</td>
					<td class='list_box_td'>".$basicinfo_state."</td>
					<td class='list_box_td list_bg_gray'>
						".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_datas[$i][wholesale_price],0)." ".$currency_display[$admin_config["currency_unit"]]["back"]."<br> ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_datas[$i][wholesale_sellprice],0)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td class='list_box_td list_bg_gray'>
						".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_datas[$i][listprice],0)." ".$currency_display[$admin_config["currency_unit"]]["back"]."<br> ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_datas[$i][sellprice],0)." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td class='list_box_td list_bg_gray'>
						".$stock_use_yn."
						".($goods_datas[$i][stock_use_yn] == 'Y'?'<br> '.$goods_datas[$i][stock]:'<br> '.number_format($goods_datas[$i][stock]))."
					</td>
					<td class='list_box_td'>
						<a href=\"/shop/goods_view.php?id=".$id."\" target=_blank><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_preview.gif' border=0 align=absmiddle style='cursor:pointer'></a>";
						if(checkMenuAuth(md5("/admin/product/goods_input.php"),"D")){
							$Contents .= "
							<img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='cursor:pointer' border=0 onclick=\"deleteProduct('delete_excel','".$goods_datas[$i][id]."')\">";
						}else{
							$Contents .= "
							<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle ></a>";
						}
		$Contents .= "
					</td>
				</tr>";

	}

	$Contents .= "
			</table>
			<table cellpadding=0 cellspacing=0 width='100%' border=0>
			<tr>
				<td align=right  style='padding-top:10px;'>".$str_page_bar."</td>
			</tr>
			</table>
			<!--/form-->";

}


}else{	//상품 엑셀 등록 수정 시작

	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || true){

		if($admininfo[mall_type] == "BW"){
			$download_excel_file = "batch_product_upload_example_wholesale.xls";
		}else{
			$download_excel_file = "batch_product_upload_example.xls";
		}

$Contents .="
			<table cellpadding=0 cellspacing=0 width='100%'>
			<tr>
				<td colspan=3>
				<form name='excel_input_form' method='post' action='product_update_excel.act.php' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)' target=''><!--iframe_act-->
				<input type='hidden' name='act' value='".($up_mode == "new_upload" ? "new_excel_input":"excel_input")."'>
				<input type='hidden' name='cid' value=''>
				<input type='hidden' name='depth' value=''>
				<input type='hidden' name='page_type' value='update'>
				<table width='100%' border=0 cellpadding=0 cellspacing=1 class='input_table_box'>
				<col width=18%>
				<col width=*>";

$Contents .="
				<tr height=30 align=center>
					<td class='input_box_title' ><b>엑셀파일 입력</b></td>
					<td class='input_box_item'>
						<input type=file class='textbox' name='excel_file' style='height:22px;width:200px;' validation=true title='엑셀파일 입력'>
						* 	batch_product_upload_example.xls ( 엑셀 저장시 97~03년 양식으로 저장하시고 등록하세요.)
					</td>
				</tr>
				<tr height=30 align=center>
					<td class='input_box_title' ><b>상품 이미지 입력</b></td>
					<td class='input_box_item'>
						<input type=file class='textbox' name='goods_img_file' style='height:22px;width:200px;' validation=false filetype='zip' title='상품이미지 입력'>
						* batch_goods_image.zip ( zip 파일로 압축하여 저장하세요.)
					</td>
				</tr>
				</table>
				<table width='100%' border=0 cellpadding=0 cellspacing=1>
				<tr height=20>
					<td style='padding:6px;line-height:140%;' colspan=2>
						<div>
						<ol>
							<li>
								<img src='../image/emo_3_15.gif' border=0 align=absmiddle>
								엑셀정보에는 <b>' (따옴표)</b>는 사용하실수 없습니다. <b> 엑셀정보내에 카테고리 정보를 등록해 놓으면 해당 카테고리로 상품이 자동등록됩니다.</b><!--".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."-->
							</li>
							<li style='padding-left:20px;'>
								배송정책은 <b>기본 정책</b>으로 자동 설정되며, 개별로 등록시에는  <b>상품일괄수정 > 배송정책 </b>에서 손쉽게 변경할수 있습니다.
							</li>
							<li style='padding-left:20px;'>
								
								카테고리 미등록시  <b>상품등록 > 미분류상품</b>에서 손쉽게 변경할수 있습니다.
							</li>
							<li>
								<img src='../image/emo_3_15.gif' border=0 align=absmiddle>
								<span class='red'>주의사항</span>
							</li>
							<li style='padding-left:20px;'>
								<span class='red'>
								1)	대량엑셀수정은 검색하신 상품을 엑셀로 다운로드 받으시고 수정이 필요한 항목을 수정을 하시고 수정 불필요한 항목은 다운로드 받으신 내용을 수정하지 마시고 다시 엑셀로 업로드 하시면됩니다. 
								<br/>2)	만약 필수 항목에 빈 값으로 엑셀로 다운로드 되어 있을 경우는 필수값을 입력해주셔야 합니다.</span>
							</li>
						</ol>
						</div>

					</td>
				</tr>
				<tr height=30>
					<td colspan=2 style='padding:10px 0px;' align=center>
						<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0>
					</td>
				</tr>
				</table> 
				</td>
			</tr>";
}


$Contents .= "<tr>
				<td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle><b class=blk> 업로드 엑셀정보 </b>&nbsp;</div>")."</td>
			</tr>
			<tr>
				<td colspan=3 align=left style='padding-bottom:10px;'><div style='width:1400px;height:300px;overflow:auto;'>".MakeUploadExcelData2('update')."</div></td>
			</tr>
			<tr>
				<td colspan=3 align=center style='padding-bottom:10px;'><img src='../image/goods_d_btn1.gif' alt='상품등록하기' onclick=\"UploadExcelGoodsReg('update');\" style='cursor:pointer;'/></div></div></td>
			</tr>";

/*
include "../product/product_query.php";
//include "../product/product_list_search.php";
		$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $goods_datas[$i][id], "s", $goods_datas[$i]);

$Contents .= "
			<tr>
				<td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle><b class=blk> 상품 리스트 :</b>&nbsp;<b id='select_category_path2'>전체(".number_format($total)."개)</b></div>")."</td>
			</tr>
			<tr>
				<td valign=top style='padding-top:33px;'></td>
				<td valign=top style='padding:0px;padding-top:0px;' id=product_list>";

				$innerview = "
				<table width='100%' cellpadding=0 cellspacing=0>
				<tr height=30>
					<td>
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td>
							<table cellpadding=0 cellspacing=0>
								<tr>
									<td>".CompanyList($company_id,"","max",$max."&up_mode=".$up_mode)."</td>
									<td style='padding-left:5px;'>
										<select style='height:20px;' name=max onchange=\"window.frames['act'].location.href='".$HTTP_URL."?up_mode=".$up_mode."&view=innerview&company_id=".$company_id."&max='+this.value\">
										<option value='10' ".CompareReturnValue(10,$max).">10</option>
										<option value='20' ".CompareReturnValue(20,$max).">20</option>
										<option value='50' ".CompareReturnValue(50,$max).">50</option>
										<option value='100' ".CompareReturnValue(100,$max).">100</option>
										</select> 씩 보기
									</td>
								</tr>
							</table>
							</td>
						</tr>
					</table>
					</td>
					<td align=right></td>
				</tr>
				</table>

				<form name=listform method=post action='product_input_excel.act.php' onsubmit='return CheckDelete(this)' target='iframe_act'>
				<input type='hidden' name='act' value='select_delete'>
				<table cellpadding=2 cellspacing=0 bgcolor=gray width=100%  class='list_table_box'>
				<col width='5%' >
				<col width='10%' >
				<col width='*'>
				<col width='8%'>
				<col width='8%'>
				<col width='8%'>
				<col width='12%'>
				<col width='12%'>
				<col width='7%'>
				<tr bgcolor='#ffffff' align=center height=35>
					<td class=s_td><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
					<td class=m_td>등록일</td>
					<td class=m_td>상품정보</td>
					<td class=m_td>상품상세페이지</td>
					<td class=m_td>판매상태</td>
					<td class=m_td>공급가</td>
					<td class=m_td>도매가/할인가</td>
					<td class=m_td>소매가/할인가</td>
					<td class=e_td>관리</td>
				</tr>";

		if($db->total == 0){
			$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=9 align=center> 등록된 제품이 없습니다.</td></tr>";
		}else{
			for ($i = 0; $i < $db->total; $i++)
			{
				$db->fetch($i);

				//if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$goods_datas[$i][id].".gif")){
				$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $goods_datas[$i][id], "s", $goods_datas[$i]);

				switch($goods_datas[$i][state]){	
					case '1':
						$state = "판매중";
					break;
					case '0':
						$state = "일시품절";
					break;
					case '2':
						$state = "판매중지";
					break;
					case '7':
						$state = "수정대기상품";
					break;
					case '6':
						$state = "승인대기";
					break;
					case '8':
						$state = "승인거부";
					break;
					case '9':
						$state = "판매금지";
					break;
				}

				if($goods_datas[$i][basicinfo] != ""){
					$basicinfo_state = "정상";
				}else{
					$basicinfo_state = "파일없음";
				}

	$innerview .= "	<tr bgcolor='#ffffff'>
						<td class='list_box_td list_bg_gray'><input type=checkbox class=nonborder id='cpid' name=cpid[] value='".$goods_datas[$i][id]."'></td>
						<td class='list_box_td list_bg_gray' nowrap>".$goods_datas[$i][regdate]."<br>".$goods_datas[$i][company_name]."</td>
						<td class='list_box_td point' style='text-align:left;line-height:140%;'>
							<table>
								<tr>
									<td><a href='goods_input.php?id=".$goods_datas[$i][id]."' class='screenshot'  rel='".PrintImage($admin_config[mall_data_root]."/images/product", $goods_datas[$i][id], $LargeImageSize, $goods_datas[$i])."'  ><img src='".$img_str ."' width=50 height=50></a></td>
									<td>
										".getCategoryPathByAdmin($goods_datas[$i][cid], 4)."<br>
										<a href='goods_input.php?id=".$goods_datas[$i][id]."'><b>".$goods_datas[$i][pname]."</b>(".$goods_datas[$i][pcode].")</a>
									</td>
								</tr>
							</table>
						</td>
						<td align=center class='small'>".$basicinfo_state."</td>
						<td align=center class='small'>".$state."</td>
						<td class='list_box_td list_bg_gray'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_datas[$i][coprice],0)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>

						<td class='list_box_td list_bg_gray'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_datas[$i][wholesale_price],0)." ".$currency_display[$admin_config["currency_unit"]]["back"]." / ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_datas[$i][wholesale_sellprice],0)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>

						<td class='list_box_td list_bg_gray'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_datas[$i][listprice],0)." ".$currency_display[$admin_config["currency_unit"]]["back"]." / ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($goods_datas[$i][sellprice],0)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>

						<td class='list_box_td ' >";
							
								if(checkMenuAuth(md5("/admin/product/goods_input.php"),"U")){
									$innerview .= "
									<a href='goods_input.php?id=".$goods_datas[$i][id]."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align=absmiddle ></a>";
								}else{
									$innerview .= "
									<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align=absmiddle ></a>";
								}

								if(checkMenuAuth(md5("/admin/product/goods_input.php"),"D")){
									$innerview .= "
									<img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='cursor:pointer' border=0 onclick=\"deleteProduct('delete_excel','".$goods_datas[$i][id]."')\">";
								}else{
									$innerview .= "
									<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle ></a>";
								}
				$innerview .= "
						
						</td>
					</tr>";
			}
		}

$innerview .= "	</table>
				<table width='100%'>
					<tr height=30>";
						if(checkMenuAuth(md5("/admin/product/goods_input.php"),"D")){
							$innerview .= "<td><input type=image src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0 align=absmiddle ></td>";
						}else{
							//$innerview .= "<td><a href=\"".$auth_delete_msg."\"><img src='../image/bt_all_del.gif' border=0 align=absmiddle ></a></td>";
						}
$innerview .= "
					<td align=right style='padding-top:10px;'>".$str_page_bar."</td>
					</tr>
				</table> 
				";
*/
} // up_mode == upload 일때

if($up_mode=="download"){
	
	$page_type = 'update';
	include("goods_mandatory_info.lib2.php");
	if(is_array($goods_basic_sample) && $up_mode=="download"){
	$_help_text .= "<ul id='excel_sortlist'>";

	foreach($goods_basic_sample as $key => $value){
			$_help_text .= "
			<li class='ui-state-default".($value[color] ? " ui-state-disabled":"")."' id='".$key."' style='float:left;height:28px;width:153px;margin:1px; background-color:".$value[color]."' >
							<table width=153 border=0 style='border:0px;'>
							<col width=5>
							<col width=20>
							<col width=*>
							<tr style='background:none;'>
								<td>

								</td>
								<td>
								<input type='checkbox' id='colums' class='colums' style='cursor:pointer;' name='checked_colums[".$value[code]."]' value='".$key."'  validation='false' code_group='".$value[code_group]."' title='".$value[title]."'  ".($value[validation] == "true" ? "checked":"")." onclick=\"single_toggle($(this))\">
								</td>
								<td style='padding-left:0px' ondblclick=\"colum_toggle($(this));\">
									<div style='white-space:nowrap;text-overflow:ellipsis; overflow:hidden; width:126px;'>
										<label for='_colums_".$key."'>".$value[title]."</label>
									</div>
								</td>
							</tr>
							</table>
						</li>";
	}
	$_help_text .= "<ul>";

			if($_help_text != ""){

			    $sql = "select * from shop_product_favorites_excel_info ";
			    $db->query($sql);
			    $favorites_data = $db->fetchall();
                $favorites_options = "";
			    if(is_array($favorites_data)){
			        foreach($favorites_data as $key=>$val){
                        $favorites_options .="<option value='".$val['idx']."'>".$val['title']."</option>";
                    }
                }

			$help_text .=	"<div id='goodsinfo_down_excel'  >
							<div style='padding:10px 0px 10px 0px'>
								<img src='../images/dot_org.gif'> <b>상품엑셀 다운로드</b> <span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택 후 저장 버튼을 클릭해 주세요.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'E')."</span>
							</div>

							<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='search_table_box'>
							<col width=15%>
							<col width=34%>
							<col width=15%>
							<col width=35%>
							<tr height=50>
								<td class='search_box_title'> 자주쓰는 엑셀형식 선택 </td>
								<td class='search_box_item' colspan='3' style='padding:10px;vertical-align:top;'>
									<!--<div style='border:1px solid gray;padding:5px;width:100px;text-align:center;background-color:#efefef;'>신규선택</div>-->
									<div> 
									    <select name='favorites_excel' id='favorites_excel'> 
									        <option value=''>신규등록</option>
									        ".$favorites_options."
									    </select>
									    <input name='favorites_excel_name' class='textbox' value='' id='favorites_excel_name' />
									    <input type='button' value='자주쓰는 엑셀정보저장' id='excelInfoSave' />
									    <input type='button' value='자주쓰는 엑셀정보삭제' id='excelInfoDelete' />
									</div>
								</td>
							</tr>
							<tr height=200>
								<td class='search_box_title'> 엑셀정보 선택 <br> </td>
								<td class='search_box_item' colspan='3' style='padding:10px;height:200px;vertical-align:top;'>
									<div style=''padding:10px 10px 20px 10px;><input type=checkbox name=all_colum id='all_colum' checked><label for='all_colum'>전체 선택/해제</label></div>
									".$_help_text."
								</td>
							</tr>
							</table>
							<div style='text-align:center;padding:10px;;'>";
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
		$help_text .= "<input type='image' src='../images/".$admininfo["language"]."/b_excel_save.gif' style='cursor:pointer;' align=absmiddle>	
		";
	}else{
		$help_text .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/b_excel_save.gif' border=0 align=absmiddle></a>";
	}

	if(false){
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$help_text .= "
			<a href='excel_config.php?page_type=update&".$QUERY_STRING."' rel='facebox' ><span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'><img src='../images/".$admininfo["language"]."/b_excel_config.gif' align=absmiddle></span></a>";
		}else{
			$help_text .= "
			<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/b_excel_config.gif' align=absmiddle></a>";
		}
	}
		$help_text .= "  
							</div>
							</div>";
			}

	$Contents .= "".HelpBox($select, $help_text,200)."";
	}
}

$Contents .= "				</form>";

$Contents = $Contents.$innerview ."
			<!--form name=vieworderform method=post action='./order.act.php'>
			<input type='hidden' name='vieworder'>
			<input type='hidden' name='_vieworder'>
			<input type='hidden' name='pid'>
			<input type='hidden' name='cid' value='$cid'>
			<input type='hidden' name='category_load' value='$category_load'>
			<input type='hidden' name='depth' value='$depth'>
			</form-->
			</td>
			</tr>
		</table>";

if($up_mode=='upload'||$up_mode==''){
	$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";
}else{
	$category_str ="";
}



$script = "
<script language='javascript' src='/admin/js/jquery.form.js'></script>
<script language='JavaScript' src='product_list.js'></script>
<Script language='javascript'>

    $(document).ready(function (){
        $('#excel_sortlist').sortable({
            placeholder: 'ui-state-highlight',
            items: 'li:not(.ui-state-disabled)'
        });
        //$('#excel_sortlist').disableSelection();
    
    
        $('#max').change(function(){
            var value= $(this).val();
            $.cookie('product_update_limit', value, {expires:1,domain:document.domain, path:'/', secure:0});
            document.location.reload();
        });
    
        $('#all_colum').click(function(){    
            if($('#all_colum').attr('checked') == 'checked'){
                $('input.colums').attr('checked','checked');
            }else{
                $('input.colums').attr('checked',false);
            }
        });
    
    
        //다중검색어 시작 2014-04-10 이학봉
        
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
        
        //다중검색어 끝 2014-04-10 이학봉
        
        //자주쓰는 엑셀 정보 저장
        $('#excelInfoSave').on('click',function(){
            
            var favorites_excel_key = $('select[name=favorites_excel] :checked').val();
            var favorites_excel_name = $('#favorites_excel_name').val();
            if(favorites_excel_key){
                var act = 'favorites_update';
                var confirm_msg = '자주쓰는 엑셀형식을 수정 하시겠습니까?';
            }else{
                var act = 'favorites_insert';
                var confirm_msg = '자주쓰는 엑셀형식을 등록 하시겠습니까?';
            }
            
            if(favorites_excel_name == ''){
                alert('자주쓰는 엑셀 관리제목을 입력 해 주세요');
                return false;
            }
            
            var excelInfo = [];
            var formData = new FormData();
            $('input:checkbox[name^=checked_colums]').each(function(){
                var colum_name = $(this).attr('name');
                var checkedBool = 0;
                if($(this).is(':checked') == true){                    
                    excelInfo.push($(this).attr('name'));
                    checkedBool = 1;
                }
                formData.append(colum_name, checkedBool);
            });  
            
            if(excelInfo.length > 0){
                if(confirm(confirm_msg)){
                    

                    formData.append('act', act);
                    formData.append('favorites_excel_key', favorites_excel_key);
                    formData.append('favorites_excel_name', favorites_excel_name);
                    
            
                    $.ajax({
                        url: './product_etc.act.php',
                        data: formData,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        success: function(result){
                            if(result == true){
                                alert('등록되었습니다.');location.reload();;
                            }else{
                                alert('실패');
                            }
                        }
                    });
                }
            }else{
                alert('엑셀정보를 하나이상 선택 해 주세요');
            }
            
        });
        
        //자주쓰는 엑셀 제거
        $('#excelInfoDelete').on('click',function(){            
            var favorites_excel_key = $('select[name=favorites_excel] :checked').val();
            if(favorites_excel_key){
                if(confirm('삭제하시겠습니까?')){
                    var formData = new FormData();
                    formData.append('act', 'favorites_delete');
                    formData.append('favorites_excel_key', favorites_excel_key);
                    
            
                    $.ajax({
                        url: './product_etc.act.php',
                        data: formData,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        success: function(result){
                            if(result == true){
                                alert('삭제되었습니다.');location.reload();;
                            }else{
                                alert('실패');
                            }
                        }
                    });
                }
            }else{
                alert('삭제할 엑셀 정보를 선택 해 주세요')
            }
        });
        
        //자주쓰는 엑셀 선택 시 정보 반영
        $('#favorites_excel').on('change',function(){
           var idx =$(this).val();
           var title = $(this).find('option:checked').text();
           var act = 'getFavoritesData';
          
           
           if(idx){
                $('#favorites_excel_name').val(title);
                var formData = new FormData();
                
                formData.append('act', act);
                formData.append('idx', idx);
                
                $.ajax({
                    url: './product_etc.act.php',
                    data: formData,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    dataType:'html',
                    success: function(result){
                        console.log(result)
                        $('#excel_sortlist').html(result);
                    }
                });
            }else{
               $('#favorites_excel_name').val('');
               $('input:checkbox[name^=checked_colums]').prop('checked',true);
            }
           
        });
    });

	function colum_toggle(obj){
		//alert(obj.closest('tr').find('input#colums').attr('checked'));
		if(obj.closest('tr').find('input#colums').attr('checked') == 'checked'){
			obj.closest('tr').find('input#colums').attr('checked',false);
		}else{
			obj.closest('tr').find('input#colums').attr('checked','checked');
		}
	}

	function single_toggle(obj){
		if(obj.attr('code_group') != ''){
			if(obj.attr('checked') == 'checked'){
				obj.closest('ul').find('input[code_group='+obj.attr('code_group')+']').attr('checked','checked');
			}else{
				obj.closest('ul').find('input[code_group='+obj.attr('code_group')+']').attr('checked',false);
			}
			//alert(obj.attr('checked'));
		}
	}

function cid_del(code){
	$('#row_'+code).remove();
}


function SelectUpdate(frm){
	//alert(frm.search_searialize_value.value.length);

	if($('input:radio[name^=update_kind]:checked').val() == 'category'){
		if(!(frm.c_cid.value.length > 0)){
			alert('변경 또는 추가하시고자 하는 카테고리를 선택해주세요');
			return false;
		}
	}else if($('input:radio[name^=update_kind]:checked').val() == 'bs_goods_stock'){

	}
	//	alert($('input:radio[name^=update_kind]:checked').val());
	//return false;
	//SelectUpdateLoading();
	
	if(frm.update_type.value == 1){
		if(parseInt(frm.search_searialize_value.value.length) <= 58){
			alert('검색상품 전체에 대한 적용은 검색후 가능합니다.');	//'검색상품 전체에 대한 적용은 검색후 가능합니다.'
			select_update_unloading();
			return false;
		}
		
		if(confirm('검색상품 전체에 정보를 다운받으시겠습니까?')){//'검색상품 전체에 정보변경을 하시겠습니까?'
			return true;
		}else{
			select_update_unloading();
			return false;
		}
	}else if(frm.update_type.value == 2){
		var pid_checked_bool = false;
		var pid_obj=document.getElementsByName('select_pid[]');//kbk
		//for(i=0;i < frm.cpid.length;i++){//kbk
		for(i=0;i < pid_obj.length;i++){
			//if(frm.cpid[i].checked){//kbk
			if(pid_obj[i].checked){
				pid_checked_bool = true;
			}
			//	frm.cpid[i].checked = false;
		}
		if(!pid_checked_bool){
			alert('선택된 제품이 없습니다. 다운받을 상품을 선택하신 후 엑셀저장 버튼을 클릭해주세요');//'선택된 제품이 없습니다. 다운받을 상품을 선택하신 후 엑셀저장 버튼을 클릭해주세요'
			select_update_unloading();
			return false;
		}
	}

	//return false;
	frm.act.value = 'update';
	return true;
	//frm.submit();
	
}

function select_update_unloading(){
	//parent.document.getElementById('select_update_parent_save_loading').style.zIndex = '-1';
	//parent.document.getElementById('select_update_loadingbar').innerHTML ='';
	//parent.document.getElementById('select_update_save_loading').innerHTML ='';
	//parent.document.getElementById('select_update_save_loading').style.display = 'none';
}


</Script>
<style>
  #excel_sortlist {
      list-style-type:none;
      margin:0;
      padding:0;

   }
   #excel_sortlist li {
     margin:1
     padding:0 0 0 10;
     cursor:move;
     width:153px;
     display:inline;
     border:1px solid #c0c0c0;
   }

   #excel_sortlist2 div {
     margin:1
     padding:0 0 0 10;
     cursor:move;
     width:445px;
     display:inline;
     border:1px solid #c0c0c0;
   }
  .ui-state-highlight {float:left;height:25px;width:153px;padding:4px 0px 0px 3px;margin:1px;}
  .ui-state-disabled {opacity:0.8 !important;
filter: Alpha(Opacity=80) !important;}
</style>
";

if($view == "innerview"){
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><script src='../js/jquery-1.8.3.js'></script><body>$innerview</body></html>";
	//<script src='../js/jquery-1.8.3.js'></script> combobox() 사용때문에 추가 kbk 13/04/16

	$inner_category_path = getCategoryPathByAdmin($cid, $depth);
	echo "<Script language='javascript'>
		parent.document.getElementById('product_list').innerHTML = document.body.innerHTML;
		parent.document.getElementById('select_category_path2').innerHTML='".$inner_category_path."(".$total."개) ';
		parent.document.forms['excel_input_form'].cid.value = '".$cid."';
		parent.document.forms['excel_input_form'].depth.value = '".$depth."';
		parent.LargeImageView();
		parent.unblockLoadingBox();
		</Script>";
}else{
	$P = new LayOut();
	$P->strLeftMenu = product_menu("/admin",$category_str);
	$P->addScript = "<script Language='JavaScript' src='product_input_excel2.js'></script><script Language='JavaScript' src='../product/goods_mandatory_info.js'></script>".$script;
	$P->Navigation = "상품관리 > 상품등록 > 대량엑셀수정";
	$P->title = "대량엑셀수정";
	$P->strContents = $Contents;
	if ($category_load == "yes"){
		$P->OnLoadFunction = "zoomBox(event,this,200,400,0,90,img3)";
	}
	$P->PrintLayOut();
}


function Category(){

	$mdb = new Database;

	global $id;

	$m_string = "
	<script language='JavaScript' src='../include/manager.js'></script>
	<script language='JavaScript' src='../include/Tree.js'></script>
	<script>

	/*	 Create Tree		*/
		var tree = new Tree();
		tree.color = 'black';
		tree.bgColor = 'white';
		tree.borderWidth = 0;


	/*	Create Root node	*/
		var rootnode = new TreeNode('상품카테고리', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');
		rootnode.action = \"setCategory('product category','000000000000000',-1,'".$id."')\";
		rootnode.expanded = true;";

	$mdb->query("SELECT * FROM ".TBL_SHOP_CATEGORY_INFO." order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");

	$total = $mdb->total;
	for ($i = 0; $i < $mdb->total; $i++)
	{

		$mdb->fetch($i);

		if ($mdb->dt["depth"] == 0){
			$m_string = $m_string.PrintNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"]);
		}else if($mdb->dt["depth"] == 1){
			$m_string = $m_string.PrintGroupNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"]);
		}else if($mdb->dt["depth"] == 2){
			$m_string = $m_string.PrintGroupNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"]);
		}else if($mdb->dt["depth"] == 3){
			$m_string = $m_string.PrintGroupNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"]);
		}else if($mdb->dt["depth"] == 4){
			$m_string = $m_string.PrintGroupNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"]);
		}
	}

		$m_string = $m_string."tree.addNode(rootnode);";

	$m_string = $m_string."
	</script>
	<form>
	<div id=TREE_BAR style='margin:5px;'>
	<script>
	tree.draw();
	tree.nodes[0].select();
	</script>
	</div>
	</form>";

	return $m_string;
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
	node$cid.action = \"setCategory('$cname','$cid',$depth,'$id')\";
	rootnode.addNode(node$cid);\n\n";
}

function PrintGroupNode($cname,$mcid,$depth)
{
	global $id,$cid;
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

	if ($cid == $mcid){
		$expandstring = "true";
	}else{
		$expandstring = "false";
	}

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","&#39;",$cname);

	return "		var groupnode$mcid = new TreeNode('$cname',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);
		groupnode$mcid.tooltip = '$cname';
		groupnode$mcid.id ='nodeid$mcid';
		groupnode$mcid.expanded = $expandstring;
		groupnode$mcid.action = \"setCategory('$cname','$mcid',$depth,'$id')\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";
}


/*


CREATE TABLE IF NOT EXISTS `shop_excel_template` (
  `excel_tmp_ix` int(4) NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `excel_type` char(1) NOT NULL DEFAULT 'A' COMMENT '타입(A:전체,I:입력,O:출력)',
  `excel_name` varchar(150) NOT NULL DEFAULT '' COMMENT '엑셀템플릿명',
  `excel_templete` mediumtext COMMENT '엑셀설정값(json)',
  `company_id` varchar(32) NOT NULL DEFAULT '' COMMENT '업체코드',
  `charger_ix` varchar(32) NOT NULL DEFAULT '' COMMENT '회원코드',
  PRIMARY KEY (`excel_tmp_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='엑셀탬플릿' ;

*/
?>