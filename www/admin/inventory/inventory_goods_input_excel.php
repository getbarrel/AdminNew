<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include("../class/layout.class");
//include("../product/product_input_excel.lib.php");

if($max == ""){
	$max = 10; //페이지당 갯수
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
				<td align='left' colspan=4 > ".GetTitleNavigation("대량품목등록", "품목관리 > 대량품목등록")."</td>
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
								<td class='box_02' onclick=\"document.location.href='?up_mode=new_upload'\">신규 대량품목등록</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' ".($up_mode=="download" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?up_mode=download'\">샘플다운로드</td>
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
			</tr>";

if($up_mode == "download"){
$Contents .=	"
			<tr>
				<td colspan=3>
					<table width='100%' cellpadding=0 cellspacing=0 border='0'>
						<tr>
							<td  height='25' bgcolor=#ffffff>
								<img src='../images/dot_org.gif' align=absmiddle> <b >샘플다운로드</b> 
								대량등록에 필요한 코드를 다운받아서 사용하시면 됩니다.
							</td>
						</tr>
						<tr>
							<td height='25' style='padding:5px 0px;' bgcolor=#ffffff>
								<img src='../images/dot_org.gif' align=absmiddle> <b >현재 귀사의 코드는 </b> [ ".$admininfo[company_id]." ] 입니다.
							</td>
						</tr>
					</table>
					<table width='100%' border=0 cellpadding=0 cellspacing=1 class='input_table_box'>
						<col width=20%>
						<col width=30%>
						<col width=20%>
						<col width=30%>
						<tr height=30 align=center>
							<td class='input_box_title'><b>품목등록 양식</b></td>
							<td class='input_box_item' id='select_category_path3' align=left style='padding-left:10px;' colspan=3>
								<a href='batch_goods_upload_excel.xls'><img src='../images/".$admininfo["language"]."/btn_sample_excel_save.gif' align=absmiddle></a>
							</td>
						</tr>
						<tr height=30 align=center>
							<td class='input_box_title'  ><b>기타 코드</b></td>
							<td class='input_box_item' id='select_category_path3' align=left style='padding-left:10px;' colspan=3>
								<a href='inventory_goods_input_excel.act.php?act=ect_code_down'><img src='../images/".$admininfo["language"]."/btn_sample_excel_save.gif' align=absmiddle></a>
								<span class='blu'>* 품목분류,주매입처,원산지,제조사, 브랜드 코드를 다운로드 받을수 있습니다.</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>";
}else{

	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || true){

		if($admininfo[mall_type] == "BW"){
			$download_excel_file = "batch_goods_upload_example_wholesale.xls";
		}else{
			$download_excel_file = "batch_goods_upload_example.xls";
		}

$Contents .="
			<tr>
				<td colspan=3>

				<form name='excel_input_form' method='post' action='inventory_goods_input_excel.act.php' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)' target=''><!--iframe_act-->
				<input type='hidden' name='act' value='".($up_mode == "new_upload" ? "new_excel_input":"excel_input")."'>
				<input type='hidden' name='cid' value=''>
				<input type='hidden' name='depth' value=''>
				<input type='hidden' name='page_type' value='input'>
				<table width='100%' border=0 cellpadding=0 cellspacing=1 class='input_table_box'>
				<col width=18%>
				<col width=*>";
if($admininfo[com_type] == 'A'){
	$Contents .="<tr height=30 align=center>
		<td class='input_box_title' ><b>업체 선택</b></td>
		<td class='input_box_item'>".companyAuthList($admininfo[company_id] , "validation=true title='셀러업체' ")."</td>
	</tr>";	
}else{
	$Contents .="	<input type='hidden' name='company_id' id='company_id' validation='true' value='".$admininfo[company_id]."'";
}
$Contents .="
				<tr height=30 align=center>
					<td class='input_box_title' ><b>엑셀파일 입력</b></td>
					<td class='input_box_item'>
						<input type=file class='textbox' name='excel_file' style='height:22px;width:200px;' validation=true title='엑셀파일 입력'>
						* 	batch_goods_upload_example.xls ( 엑셀 저장시 97~03년 양식으로 저장하시고 등록하세요.)
					</td>
				</tr>
				<tr height=30 align=center>
					<td class='input_box_title' ><b>품목 이미지 입력</b></td>
					<td class='input_box_item'>
						<input type=file class='textbox' name='goods_img_file' style='height:22px;width:200px;' validation=false filetype='zip' title='품목이미지 입력'>
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
								엑셀정보에는 <b>' (따옴표)</b>는 사용하실수 없습니다. <b> 엑셀정보내에 품목분류 정보를 등록해 놓으면 해당 품목분류로 품목이 자동등록됩니다.</b><!--".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."-->
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
				</form>
				</td>
			</tr>";
}


if($up_mode == "new_upload"){
$Contents .= "<tr>
				<td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle><b class=blk> 업로드 엑셀정보 </b>&nbsp;</div>")."</td>
			</tr>
			<tr>
				<td colspan=3 align=left style='padding-bottom:10px;'><div style='width:1400px;height:300px;overflow:auto;'>".MakeUploadExcelData()."</div></td>
			</tr>
			<tr>
				<td colspan=3 align=center style='padding-bottom:10px;'><img src='../image/goods_d_btn1.gif' alt='품목등록하기' onclick='UploadExcelGoodsReg();' style='cursor:pointer;'/></div></div></td>
			</tr>";
}

$Contents .= "
			<tr>
				<td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 5px;'><img src='../image/title_head.gif' align=absmiddle><b class=blk> 품목 리스트 :</b>&nbsp;<b id='select_category_path2'>전체(".number_format($total)."개)</b></div>")."</td>
			</tr>
			<tr>
				<td valign=top style='padding-top:33px;'></td>
				<td valign=top style='padding:0px;padding-top:0px;' id=product_list>";

			$innerview = "	
				<form name=listform method=post action='inventory_goods_input_excel.act.php' onsubmit='return CheckDelete(this)' target='iframe_act'>
				<input type='hidden' name='act' value='select_delete'>
				<table cellpadding=2 cellspacing=0 bgcolor=gray width=100%  class='list_table_box'>
				<col width='3%' >
				<col width='7%' >
				<col width='7%'>
				<col width='*'>
				<col width='8%'>
				<col width='8%'>
				<col width='8%'>
				<col width='8%'>
				<col width='7%'>
				<col width='7%'>
				<tr bgcolor='#ffffff' align=center height=30>
					<td class=s_td><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
					<td class=m_td>대표코드</td>
					<td class=m_td>품목코드</td>
					<td class=m_td>이미지/품목명</td>
					<td class=m_td>품목 규격</td>
					<td class=m_td>품목계정</td>
					<td class=m_td>기본단위</td>
					<td class=m_td>등록일</td>
					<td class=m_td>사용여부</td>
					<td class=e_td>관리</td>
				</tr>";

		include("../inventory/inventory_goods_query.php");

		if($mode == "search"){
			$str_page_bar = page_bar_search($total, $page, $max, "&max=$max&company_id=$company_id&cid=$cid&depth=$depth");
		}else{
			$str_page_bar = page_bar($total, $page,$max, "&max=$max&company_id=$company_id&cid=$cid&depth=$depth");
		}

		if($db->total == 0){
			$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=10 align=center> 등록된 제품이 없습니다.</td></tr>";
		}else{
			for ($i = 0; $i < count($goods_infos); $i++){

			//if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$db->dt[id].".gif")){
			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c", $goods_infos[$i])) || $image_hosting_type=='ftp') {
				$img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c", $goods_infos[$i]);
			}else{
				$img_str = "../image/no_img.gif";
			}

$innerview .= "	<tr bgcolor='#ffffff'>
					<td class='list_box_td list_bg_gray'><input type=checkbox class=nonborder id='cpid' name=cpid[] value='".$goods_infos[$i][gid]."'></td>
					<td class='list_box_td list_bg_gray' nowrap>".$goods_infos[$i][gcode]."</td>
					<td class='list_box_td list_bg_gray'>".$goods_infos[$i][gid]."</td>
					<td class='list_box_td point' style='text-align:left;line-height:140%;'>
						<table>
							<tr>
								<td><a href='inventory_goods_input.php?gid=".$goods_infos[$i][gid]."' class='screenshot'  rel='".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], 'b', $goods_infos[$i])."'  ><img src='".$img_str ."' width=50 height=50></a></td>
								<td>
									".getIventoryCategoryPathByAdmin($goods_infos[$i][cid], 4)."<br>
									<a href='inventory_goods_input.php?gid=".$goods_infos[$i][gid]."'><b>".$goods_infos[$i][gname]."</b></a>
								</td>
							</tr>
						</table>
					</td>
					<td class='list_box_td list_bg_gray'>".$goods_infos[$i][standard]."</td>
					<td class='list_box_td list_bg_gray'>".$ITEM_ACCOUNT[$goods_infos[$i][item_account]]."</td>
					<td class='list_box_td list_bg_gray'>".getUnit($goods_infos[$i][basic_unit], "basic_unit","","text")."</td>
					<td class='list_box_td list_bg_gray'  style='padding:0px 5px;' nowrap>".$goods_infos[$i][regdate]."</td>
					<td class='list_box_td list_bg_gray'>".($goods_infos[$i][is_use] == "1" || $goods_infos[$i][is_use] == "Y" ? "사용":"사용안함")."</td>
					<td class='list_box_td ' >";
							if(checkMenuAuth(md5("/admin/inventory/inventory_goods_input_excel.php"),"U")){
								$innerview .= "
								<a href='inventory_goods_input_excel.php?id=".$db->dt[id]."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align=absmiddle ></a>";
							}else{
								$innerview .= "
								<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align=absmiddle ></a>";
							}
							if(checkMenuAuth(md5("/admin/inventory/inventory_goods_input_excel.php"),"D")){
								$innerview .= "
								<img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='cursor:pointer' border=0 onclick=\"deleteProduct('delete_excel','".$db->dt[id]."')\">";
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
						if(checkMenuAuth(md5("/admin/inventory/inventory_goods_input_excel.php"),"D")){
							$innerview .= "<td><input type=image src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0 align=absmiddle ></td>";
						}else{
							//$innerview .= "<td><a href=\"".$auth_delete_msg."\"><img src='../image/bt_all_del.gif' border=0 align=absmiddle ></a></td>";
						}
$innerview .= "
					<td align=right>".$str_page_bar."</td></tr>
				</table>
				</form>";
}// up_mode == upload 일때


$Contents = $Contents.$innerview ."
				<form name=vieworderform method=post action='./order.act.php'>
				<input type='hidden' name='vieworder'>
				<input type='hidden' name='_vieworder'>
				<input type='hidden' name='pid'>
				<input type='hidden' name='cid' value='$cid'>
				<input type='hidden' name='category_load' value='$category_load'>
				<input type='hidden' name='depth' value='$depth'>
				</form>

				</td>
				</tr>
			</table>";

if($up_mode=='upload'||$up_mode==''){
	$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";
}else{
	$category_str ="";
}

$script = "<Script Language='JavaScript'>

var UploadExcelGoodsReg_i = 0;
var p_no = new Array();


function UploadExcelGoodsReg(){
	$('.upload_excel_infos').each(function(i){
		p_no[i] = $(this).val();
	});

	UploadExcelGoodsRegAjax(p_no.length,UploadExcelGoodsReg_i);
}

function UploadExcelGoodsRegAjax(total_no,now_no){

	$.ajax({
		type: 'GET', 
		data: {'act': 'single_goods_reg', 'p_no':p_no[now_no]},
		url: './inventory_goods_input_excel.act.php?page_type=input',
		dataType: 'html',
		async: true,
		page_type :'input',
		beforeSend: function(){
			$('#status_message_'+p_no[now_no]).html('품목등록 진행중...<img src=\'/admin/images/indicator.gif\' border=0 width=20 height=20 align=absmiddle> ')
		},
		success: function(data){
			UploadExcelGoodsReg_i++;
			try{
				if(total_no > now_no){
					$('#status_message_'+p_no[now_no]).html(data);
					UploadExcelGoodsRegAjax(total_no,UploadExcelGoodsReg_i);
				}else{
					if(confirm('등록완료되었습니다. 등록이 실패한 품목정보를 엑셀로 다운받으시겠습니까?')){
						location.href='./inventory_goods_input_excel.act.php?act=bad_goods_info_excel&page_type=input';
					}
				}
			}catch(e){
				alert(e.message);
			}
		},
		error:function(x, o, e){
			alert(x.status + ' : '+ o +' : '+e);
		}
	});

}

</script>";

if($view == "innerview"){

	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>

	<body>$innerview</body></html>";
	echo "
	<Script>
	parent.document.getElementById('product_list').innerHTML = document.body.innerHTML;
	parent.LargeImageView();
	parent.unblockLoadingBox();
	</Script>";

}else{
	$P = new LayOut();
	$P->strLeftMenu = inventory_menu();
	$P->addScript = "<script Language='JavaScript' src='../include/zoom.js'></script>\n".$script;
	$P->Navigation = "재고관리 > 재고품목 대량 등록/수정";
	$P->title = "재고품목 대량 등록/수정";
	$P->strContents = $Contents;
	if ($category_load == "yes"){
		$P->OnLoadFunction = "zoomBox(event,this,200,400,0,90,img3)";
	}
	$P->PrintLayOut();
}


function MakeUploadExcelData(){

	include("../logstory/class/sharedmemory.class");
	//auth(8);
	$shmop = new Shared("upload_goods_excel_data_input_".$_SESSION["admininfo"]["charger_ix"]);
	//	$shmop->clear();
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admininfo"]["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$upload_excel_data = $shmop->getObjectForKey("upload_goods_excel_data_input_".$_SESSION["admininfo"]["charger_ix"]);

	if($upload_excel_data[session_id()]){

		$mstring = "<table cellpadding=3 cellspacing=0 class='list_table_box' >\n";
		$i = 0;
		$z = 0;
		foreach($upload_excel_data[session_id()] as $key => $value){

			$mstring .= "<tr align=center height=25>\n";
			$mstring .= "\t<td ".($i == 0 ? "class=m_td style='padding:0px 50px;'   nowrap":" class='point ' nowrap")."> ".($i == 0 ? "처리현황":"<span id='status_message_".$value["p_no"]."'>".$value["status_message"]."")."</span></td>\n";
			foreach($value as $_key => $_value){
				if($_key != "status_message" && $_key != "p_no"){
					$mstring .= "\t<td ".($i == 0 ? "class=m_td nowrap":" nowrap").">
											".@htmlspecialchars($_value)."";
					if($_key == "gcode" && $i != 0){
						$mstring .= "<input type=hidden class='upload_excel_infos' id='p_no' name='upload_excel_infos[".$value["p_no"]."][p_no]' value='".$value["p_no"]."' >";
						$z++;
					}
					$mstring .= "</td>\n";
				}
			}
			$mstring .= "</tr>\n";

			$i++;
		}
		$mstring .= "</table>\n";
	}

	return $mstring;

}

?>