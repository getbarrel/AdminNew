<?

function relationGalleryImageList($dgi_ix, $disp_type=""){

	global $start,$page, $orderby, $admin_config, $erpid;
	
	$max = 105;
	
	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}
		
	$db = new Database;
	
	$sql = "SELECT di.*
					FROM deepzoom_image di, deepzoom_gallery_relation dgr
					where di.di_ix = dgr.di_ix and dgi_ix = '$dgi_ix'   ";
	$db->query($sql);
	$total = $db->total;
	
	$sql .= " order by dgr.vieworder asc limit $start,$max";
	$db->query($sql);		
	
	

	if ($db->total == 0){
		if($disp_type == "clipart"){
			
		}else{
			$mString = "<table cellpadding=0 id=tb_relation_product cellspacing=0 width=100% class=tb>";	
		//	$mString .= "<tr bgcolor=#ffffff height=100%><td colspan=5 align=center class='small'>등록된 이벤트/기획전 상품 정보가 없습니다 . <br> 좌측 상품을 이곳으로 드래그하시면 <br>이벤트/기획전 상품으로 등록됩니다. </td></tr>";
			$mString .= "</table>";	
		}
	}else{
//		$mString = "<ul id='sortlist' >";	
			
		$i=0;
		if($disp_type == "clipart"){
			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);			
				
				$mString .= "<div id='_gallery_product_code_1_".$db->dt[di_ix]."' pid='".$db->dt[di_ix]."' _select_gorup_code='1' style='float:left;border:1px solid #efefef;margin:0 3px 3px 3px;padding:2px;width:75px;height:75px;text-align:center;' onclick='spoitDIV(this)' ondblclick='this.removeNode(this)'>\n";
				$mString .= "<table id='seleted_tb_".$db->dt[idi_ix]."' cellpadding=0 cellspacing=0 border=0 style='display:inline;height:75px'>\n";
				$mString .= "<tr>\n";
				$mString .= "<td style='display:none;'></td>\n";
				if(file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/deepzoom/".$db->dt[di_ix]."/s_".$db->dt[deepzoom_file])){
					$mString .= "<td><img src='".$admin_config[mall_data_root]."/deepzoom/".$db->dt[di_ix]."/s_".$db->dt[deepzoom_file]."' title='[".$db->dt[di_ix]."]".$db->dt[gallery_name]."' width=50></td>\n";
				}else{
					$mString .= "<td><img src='/admin/images/noimages_50.gif' title='[".$db->dt[di_ix]."]".$db->dt[gallery_name]."'></td>\n";
				}
				$mString .= "<td style='display:none;'>".$db->dt[gallery_name]."<br></td>\n";
				$mString .= "<td style='display:none;'><input type='hidden' name='rpid[1][]' value='".$db->dt[di_ix]."'></td>\n";
				$mString .= "</tr>\n";
				$mString .= "</table>\n";
				$mString .= "</div>\n";
				
				//$mString .= "<div style='border:1px solid #efefef;margin:0 5 5 0;display:inline;width:50px;height:50px;text-align:center'><img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif' ></div>";
				
			}
		}else{
	  	$mString .= "<!--li id='image_".$db->dt[di_ix]."' -->
							<table width=100% cellpadding=0 cellspacing=0 id=tb_relation_product class=tb border=0 >
							<col width='60'>
							<col width='*'>
							<col width='60'>";
	
			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);			
				//ondblclick=\"$('tb_relation_product').deleteRow(this.rowIndex);$('tb_relation_product').deleteRow(this.rowIndex);	\"
				$mString .= "<tr height=75 bgcolor=#ffffff onclick=\"spoit(this)\" ondblclick=\"document.getElementById('_gallery_product_code_1_".$db->dt[di_ix]."').removeNode(true);this.removeNode(true);\" style='background: url(../images/dot.gif) repeat-x left bottom; '>
							<td class=table_td_white align=center style='padding:5px;'>
								<div style='border:1px solid #efefef;margin:0 5 5 0;display:inline;width:50px;height:50px;text-align:center'>
								<img src='".$admin_config[mall_data_root]."/deepzoom/".$db->dt[di_ix]."/s_".$db->dt[deepzoom_file]."' width=50>
								</div>
							</td>						
							<td class=table_td_white>".cut_str($db->dt[deepzoom_name],30)."</td>
							<td><input type='hidden' name='rpid[]' value='".$db->dt[di_ix]."'></td>
							</tr>						
							";
				//$mString .= "</li>";
			}
			$mString .= "</table>";
		}
	}
	
	//$mString = $mString."</ul>";
	
	return $mString;
	
}
?>