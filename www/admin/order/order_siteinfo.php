<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");

$db = new Database;
$db->query("SELECT os.* , od.pid FROM shop_order_siteinfo os left join shop_order_detail od on os.od_ix = od.od_ix where os.od_ix = '$od_ix' and os.oid = '$oid' ");
$db->fetch();

//echo "SELECT os.* , od.pid FROM shop_order_siteinfo os left join shop_order_detail od on os.od_ix = od.od_ix where os.od_ix = '$od_ix' and os.oid = '$oid' ";
//print_r($db->dt);

$Script = "";

$Contents = "<form name='order_siteinfo_frm' method='get' action='order_siteinfo.act.php' style='display:inline;' target='iframe_act'>
<input type='hidden' name='act' value='update'>
<input type='hidden' name='pid' value='".$db->dt[pid]."'>
<input type='hidden' name='oid' value='".$oid."'>
<input type='hidden' name='od_ix' value='".$od_ix."'>
		<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>
			<tr>
				<td align=center style='padding: 0 10 0 10'>

						<table border='0' width='100%' cellspacing='1' cellpadding='0' >
							<tr>
								<td >
									<table border='0' width='100%' cellspacing='1' cellpadding='3' class='input_table_box' style='table-layout:fixed;' >
										<col width='150px'>
										<col width='*'>
										<tr>
											<td class='input_box_title' align='left' >  주문번호</td>
											<td class='input_box_item point'><b class=blk>".$oid."</b></td>
										</tr>
										<tr>
											<td class='input_box_title' align='left' >  상품정보</td>
											<td class='input_box_item'>
												<table cellpadding=0 cellspacing=0>
													<tr>
														<td>
														<img src='".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[pid], "c")."'  onerror=\"this.src='".$admin_config[mall_data_root]."/images/noimg_52.gif'\" width=50 height=50 style='margin:5px;border:1px solid gray'>
														</td>
														<td style='line-height:150%;'>".$db->dt[pname]."<br>
											<a href='".$db->dt[orgin_url]."' target=_blank><img src='../images/".$admininfo["language"]."/btn_buy_agency.gif'></a></td>
													</tr>
												</table>
											</td>
										</tr>
										<!--tr>
											<td class='input_box_title' align='left' >  <s>구매사이트 주소</s></td>
											<td class='input_box_item'>&nbsp;".$db->dt[orgin_url]."</td>
										</tr-->
										<!--tr>
											<td class='input_box_title' align='left' >  구매 URL</td>
											<td class='input_box_item' style='padding:7px 5px;line-height:140%;'>&nbsp;
											<a href='".$db->dt[orgin_url]."' target=_blank>".$db->dt[orgin_url]."</a>
											<!--a href='".$db->dt[orgin_url]."' target=_blank><img src='../images/".$admininfo["language"]."/btn_buy_agency.gif'></a-->
											</td>
										</tr-->
										<tr>
											<td class='input_box_title' align='left' >  구매사이트 주문번호</td>
											<td class='input_box_item'><input type='text' name='orgin_oid' class='textbox' style='width:90%' value='".$db->dt[orgin_oid]."'></td>
										</tr>
										<tr style='display:none;'>
											<td class='input_box_title' align='left' ><s> 해외배송업체</s></td>
											<td class='input_box_item'><input type='text' name='orgin_tracking_no' class='textbox' style='width:90%'></td>
										</tr>
										<tr>
											<td class='input_box_title' align='left' >  해외송장번호</td>
											<td class='input_box_item'><input type='text' name='orgin_tracking_no'  class='textbox' style='width:90%' value='".$db->dt[orgin_tracking_no]."'></td>
										</tr>
									</table>
								</td>
							</tr>
						</table><br>
			</td>
  		</tr>
		<tr>
			<td colspan=2 align=center style='padding:0px 0px;'>
			<input type=checkbox name='change_status' id='change_status' value='".ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_ING."'><label for='change_status'>선택시 주문정보 변경(해외창고배송중)</label>
			</td>
		</tr>
		<tr>
			<td colspan=2 align=center style='padding:10px 0px;'>
			<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle><!--btn_inquiry.gif-->
			</td>
		</tr>
  		</table>
</form>";



$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "주문관리 > 주문 사이트 정보 ";
$P->NaviTitle = "주문 사이트 정보";
$P->strContents = $Contents;
echo $P->PrintLayOut();




/*
CREATE TABLE IF NOT EXISTS `shop_order_siteinfo` (
  `osi_ix` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `oid` varchar(17) NOT NULL COMMENT '주문번호',
  `od_ix` int(8) NOT NULL COMMENT '주문상세번호',
  `pname` varchar(200) DEFAULT NULL COMMENT '구매상품명',
  `orgin_url` varchar(255) DEFAULT NULL COMMENT '구매상품 URL',
  `orgin_oid` varchar(50) DEFAULT NULL COMMENT 'Orgin 주문번호',
  `orgin_tracking_no` varchar(50) DEFAULT NULL COMMENT 'Orgin 주문번호',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY (`osi_ix`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='주문 구매 사이트 정보' AUTO_INCREMENT=1 ;


*/