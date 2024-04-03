<?
include("../class/layout.class");

$db = new Database;

$Contents = "
	<form name='search_form' method='get' onsubmit='return CheckFormValue(this);'>
	<input type='hidden' name='mode' value='search'>
	<table cellpadding=0 cellspacing=0 border=0 width=100%>
	<tr>
		<td colspan=2>
			" . GetTitleNavigation("제휴품목 코드구성 목록", "주문관리 > 수동주문 > 제휴품목 코드구성 목록") . "
		</td>
	</tr>
	<tr>
		<td colspan=2>
			<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='input_table_box'>
				<col width='20%' />
				<col width='30%' />
				<col width='20%' />
				<col width='30%' />
                <tr height=30>
                <th class='search_box_title'>판매처</th>
                <td class='search_box_item' nowrap colspan='3'>
                    <table cellpadding=0 cellspacing=0 width='100%' border='0' >
                        <col width='12.5%'>
                        <col width='12.5%'>
                        <col width='12.5%'>
                        <col width='12.5%'>
                        <col width='12.5%'>
                        <col width='12.5%'>
                        <col width='12.5%'>
                        <col width='12.5%'>
                        <tr>";

$slave_db->query("select * from sellertool_site_info where disp='1' ");
$sell_order_from = $slave_db->fetchall('object');
if (count($sell_order_from) > 0) {
    for ($i = 0; $i < count($sell_order_from); $i++) {
        if ($i > 0 && $i % 8 == 0) {
            $Contents .= "              </tr><tr>";
        }
        $Contents .= "
                            <td>
                                <input type='checkbox' name='site_code[]' id='order_from_" . $sell_order_from[$i]['site_code'] . "' value='" . $sell_order_from[$i]['site_code'] . "' " . CompareReturnValue($sell_order_from[$i]['site_code'], $site_code, ' checked') . ">
                                <label for='order_from_" . $sell_order_from[$i]['site_code'] . "'>" . $sell_order_from[$i]['site_name'] . "</label>
                            </td>";
    }
}

$Contents .= "
                        </tr>
                    </table>
                </td>
            </tr>
				<tr>
					<td class='input_box_title'>  검색어  </td>
					<td class='input_box_item' colspan='3'>
						<table cellpadding=0 cellspacing=0 width=30%>
							<col width='80px'>
							<col width='*'>
							<tr>
								<td>
								    <select name='search_type'  style='font-size:12px;height:20px;'>
									    <option value='sg_code' " . CompareReturnValue('sg_code', $search_type, ' selected') . ">제휴 품목코드</option>
									    <option value='gid' " . CompareReturnValue('gid', $search_type, ' selected') . ">품목코드(ERP)</option>
									    <option value='memo' " . CompareReturnValue('memo', $search_type, ' selected') . ">비고</option>
									</select>
								</td>
								<td style='padding-left:5px;'>
								<INPUT id=search_texts  class='textbox' value='" . $search_text . "' style=' FONT-SIZE: 12px; WIDTH: 90%; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
								</td>
							</tr>
						</table>
					</td>
				</tr>
            </table>
		</td>
	</tr>
	<tr>
		<td colspan=2 align=center style='padding:20px 0px;'>
		    <input type=image src='../images/" . $admininfo["language"] . "/bt_search.gif' border=0 align=absmiddle>
		</td>
	</tr>
	</table>
	</form>";

$Contents .= "
		<table cellpadding=5 cellspacing=0 width=100% border=0 align=center style=''>
		 <tr>
			<td>
			<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
				<col style='width:8%;'>
				<col style='width:12%;'>
				<col style='width:15%;'>
				<col style='width:15%;'>
				<col style='width:8%;'>
				<col style='width:*;'>
				<col style='width:10%;'>
			  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
				<td class='s_td'> 번호</td>
				<td class='m_td'> 판매처</td>
				<td class='m_td'> 제휴 품목코드</td>
				<td class='m_td'> 품목코드(ERP)</td>
				<td class='m_td'> 품목수량</td>
				<td class='m_td'> 비고</td>
				<td class='e_td'> 관리</td>
			  </tr>";

$max = 30;

$start = 0;
$page = 1;

if ($page == '') {
    $start = 0;
    $page = 1;
} else {
    $start = ($page - 1) * $max;
}

if ($QUERY_STRING == "nset=$nset&page=$page") {
    $query_string = str_replace("nset=$nset&page=$page", "", $QUERY_STRING);
} else {
    $query_string = str_replace("nset=$nset&page=$page&", "", "&" . $QUERY_STRING);
}

$where = array();
if (is_array($site_code) && !empty($site_code)) {
    $where[] = "site_code in ('" . implode($site_code) . "')";
} else if ($site_code != "") {
    $where[] = "site_code = '" . $site_code . "'";
}

if (!empty($search_type) && !empty($search_text)) {
    $where[] = "$search_type like '%" . $search_text . "%'";
}

$sql = "select count(*) as total from dewytree_product_linked " . (!empty($where) ? 'WHERE ' . implode(' and ', $where) : '') . "";
$db->query($sql);
$db->fetch();
$total = $db->dt['total'];

if ($total == 0) {
    $Contents .= "<tr bgcolor=#ffffff><td height=35 colspan=7 align=center>내역이 존재 하지 않습니다.</td></tr>";
} else {

    $sql = "SELECT pl.*, si.site_name FROM (
            SELECT * FROM dewytree_product_linked pl " . (!empty($where) ? 'WHERE ' . implode(' and ', $where) : '') . " ORDER BY pl.regdate DESC limit $start,$max
           ) pl LEFT JOIN sellertool_site_info si ON (si.site_code=pl.site_code)";
    $db->query($sql);

    for ($i = 0; $i < $db->total; $i++) {
        $db->fetch($i);

        $no = $total - ($page - 1) * $max - $i;

        $Contents .= "
						  <tr height=27 align=center pl_id='" . $db->dt['pl_id'] . "' update_bool='0'>
							<td class='list_box_td list_bg_gray'>" . $no . "</td>
							<td class='list_box_td'>" . $db->dt['site_name'] . "</td>
							<td class='input_box_item1 list_bg_gray'>" . $db->dt['sg_code'] . "</td>
							<td class='input_box_item1'>
							    <span class='info'>" . $db->dt['gid'] . "</span>
							    <span class='input' style='display:none;'><input type='text' class='textbox input_gid' value='" . $db->dt['gid'] . "' style='width:90%;'/></span>
                        </td>
							<td class='list_box_td list_bg_gray'>
							    <span class='info'>" . $db->dt['qty'] . "</span>
							    <span class='input' style='display:none;'><input type='text' class='textbox input_qty' value='" . $db->dt['qty'] . "' style='width:90%;'/></span>
                        </td>
							<td class='input_box_item1'>
							    <span class='info'>" . $db->dt['memo'] . "</span>
							    <span class='input' style='display:none;'><input type='text' class='textbox input_memo' value='" . $db->dt['memo'] . "' style='width:90%;'/></span>
                        </td>
							<td class='list_box_td list_bg_gray'>
							    <a href=\"JavaScript:productLinkedUpdate('" . $db->dt['pl_id'] . "')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
							    <a href=\"JavaScript:productLinkedDelete('" . $db->dt['pl_id'] . "')\"><img  src='../images/" . $admininfo["language"] . "/btc_del.gif' border=0></a>
							</td>
						  </tr>";
    }
}
$Contents .= "</table>";
$Contents .= "<table cellpadding=0 cellspacing=0 border=0 width=100% >
                <tr height=50 bgcolor=#ffffff>
                    <td align=left>" . page_bar($total, $page, $max, $query_string, "") . "</td>
                    <td align='right'><a href='/admin/order/product_linked.write.php'><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0 ></a></td>
                </tr>
            </table>
			</td>
		</tr>
	</table>";

$Script = "
<script type='text/javascript'>
    function productLinkedDelete (pl_id){
        if(confirm('해당 정보를 정말로 삭제 하시겠습니까 ?')){
            $.ajax({ 
                type: 'POST', 
                data: {'act': 'delete', 'pl_id':pl_id},
                url: './product_linked.act.php',  
                dataType: 'text',
                success: function(result){ 
                    alert('성공적으로 삭제되었습니다.');
                    top.location.reload();     
                }
            });
        }
    }
    function productLinkedUpdate (pl_id){
        var trObj = $('tr[pl_id=' + pl_id + ']');
        if(trObj.attr('update_bool') == '1'){
            $.ajax({ 
                type: 'POST', 
                data: {'act': 'update', 'pl_id':pl_id, 'gid':trObj.find('.input_gid').val()
                , 'qty':trObj.find('.input_qty').val(), 'memo':trObj.find('.input_memo').val()},
                url: './product_linked.act.php',  
                dataType: 'text',
                success: function(result){ 
                    alert('성공적으로 수정 되었습니다.');
                    top.location.reload();     
                }
            });
        } else {
            trObj.find('.info').hide();
            trObj.find('.input').show();
            trObj.attr('update_bool','1');
        }
    }
</script>
";

$P = new LayOut();
$P->addScript = $Script;
$P->OnloadFunction = "";
$P->Navigation = "주문관리 > 수동주문 > 제휴품목 코드구성 목록";
$P->title = "제휴품목 코드구성 목록";
$P->strLeftMenu = order_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();