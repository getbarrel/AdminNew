<?php
include $_SERVER["DOCUMENT_ROOT"] . "/admin/class/layout.class";


if (empty($max)) {
    $max = 50; //페이지당 갯수
}

if ($page == '') {
    $start = 0;
    $page = 1;
} else {
    $start = ($page - 1) * $max;
}

$where = "where gc.gc_ix = '".$gc_ix."' and gcd.status = 'Y' ";

if($gift_change_state != ""){
    $where .= " and gcd.gift_change_state = $gift_change_state ";
}

if($search_text != ""){
    if($search_type != ""){
        if($search_type == "gcd.gift_code"){
            $search_text = str_replace("-","",$search_text);
            $where .= " and $search_type LIKE '%".trim($search_text)."%' ";

        }else if($search_type == "cmd.name"){
            $where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
        }else{
            $where .= " and $search_type LIKE '%".trim($search_text)."%' ";
        }
    }else{
        $where .= " and (gcd.gift_code LIKE '%".str_replace("-","",trim($search_text))."%' or AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') LIKE '%$search_text%' or gcd.member_id LIKE '%$search_text%') ";
    }
}


if($regdate == 1 && ($startDate != "" && $endDate != "")){
    $where .= " and  gcd.use_date between '".$startDate." 00:00:00' and '".$endDate." 23:59:59' ";
}



$sql = "select 
          count(*) cnt 
        from 
          shop_gift_random_certificate gc
        left join 
          shop_gift_random_certificate_detail gcd on gc.gc_ix = gcd.gc_ix
        left JOIN 
          common_member_detail cmd on cmd.code = gcd.user_code
        $where
";
$db->query($sql);
$db->fetch();
$total = $db->dt['cnt'];

if($mode != 'excel'){
    $limit = "LIMIT $start, $max";
}


$sql = "select 
          gc.*,gcd.*, 
          AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') name
        from 
          shop_gift_random_certificate gc 
        left join 
          shop_gift_random_certificate_detail gcd on gc.gc_ix = gcd.gc_ix
        left JOIN 
          common_member_detail cmd on cmd.code = gcd.user_code
        $where
        order by gcd.gcd_ix desc
        $limit
      ";

$db->query($sql);
$gift_details = $db->fetchall();


if($mode == "excel"){	//엑셀다운로드
    include '../include/phpexcel/Classes/PHPExcel.php';
    PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

    date_default_timezone_set('Asia/Seoul');

    $memberXL = new PHPExcel();

    // 속성 정의
    $memberXL->getProperties()->setCreator("포비즈 코리아")
        ->setLastModifiedBy("Mallstory.com")
        ->setTitle("Point List")
        ->setSubject("Point List")
        ->setDescription("generated by forbiz korea")
        ->setKeywords("mallstory")
        ->setCategory("Point List");

    // 데이터 등록
    $memberXL->getActiveSheet(0)->setCellValue('A' . 1, "시리얼 넘버");
    $memberXL->getActiveSheet(0)->setCellValue('B' . 1, "사용가능기간");
    $memberXL->getActiveSheet(0)->setCellValue('C' . 1, "상태");
    $memberXL->getActiveSheet(0)->setCellValue('D' . 1, "이름");
    $memberXL->getActiveSheet(0)->setCellValue('E' . 1, "아이디");
    $memberXL->getActiveSheet(0)->setCellValue('F' . 1, "발급일자");

    for($i=0;$i<$db->total;$i++){
        $db->fetch($i);

        if ($db->dt[gift_change_state] == 0) {
            $gift_change_state_str = "발급대기";
        } else if ($db->dt[gift_change_state] == 1) {
            $gift_change_state_str = "발급완료";
        } else {
            $gift_change_state_str = "";
        }



        $memberXL->getActiveSheet()->setCellValue('A' . ($i + 2), $db->dt[gift_code]);
        $memberXL->getActiveSheet()->setCellValue('B' . ($i + 2), $db->dt[gift_start_date] ." ~ ".$db->dt[gift_end_date]);
        $memberXL->getActiveSheet()->setCellValue('C' . ($i + 2), $gift_change_state_str);
        $memberXL->getActiveSheet()->setCellValue('D' . ($i + 2), $db->dt[name]);
        $memberXL->getActiveSheet()->setCellValue('E' . ($i + 2), $db->dt[member_id]);
        $memberXL->getActiveSheet()->setCellValue('F' . ($i + 2), ($db->dt[member_id] ? $db->dt[use_date] : "-") );
    }

    $memberXL->getActiveSheet()->setTitle('예치금 내역');

    // 첫번째 시트 선택
    $memberXL->setActiveSheetIndex(0);
    $memberXL->getActiveSheet()->getColumnDimension('A')->setWidth(30);
    $memberXL->getActiveSheet()->getColumnDimension('B')->setWidth(30);
    $memberXL->getActiveSheet()->getColumnDimension('C')->setWidth(30);
    $memberXL->getActiveSheet()->getColumnDimension('D')->setWidth(30);
    $memberXL->getActiveSheet()->getColumnDimension('E')->setWidth(30);
    $memberXL->getActiveSheet()->getColumnDimension('F')->setWidth(30);

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="gift_history.xls"');
    header('Cache-Control: max-age=0');


    $objWriter = PHPExcel_IOFactory::createWriter($memberXL, 'Excel5');
    $objWriter->save('php://output');

    exit;
}



if ($_SERVER["QUERY_STRING"] == "nset=$nset&page=$page") {
    $query_string = str_replace("nset=$nset&page=$page", "", $_SERVER["QUERY_STRING"]);
} else {
    $query_string = str_replace("nset=$nset&page=$page&", "", "&" . $_SERVER["QUERY_STRING"]);
}
$str_page_bar = page_bar($total, $page, $max, $query_string, "");


$Contents01 = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
    <tr >
    <td align='left' colspan=6 > " . GetTitleNavigation("랜덤 상품권 발행 내역", "마케팅관리 > 랜덤 상품권 발행 내역") . "</td>
    </tr>
    <tr>
        <td>
            <form name='searchmember' style='display:inline;'>
            <input type='hidden' name='gc_ix' value='" . $gc_ix . "' />
            <input type='hidden' name='max' value='" . $max . "' />
                <table width=100%  border=0 cellpadding=0 cellspacing=0>
                    <tr>
                        <td align='left' colspan=2 width='100%' valign=top >
                            <table cellpadding=0 cellspacing=1 width='100%' class='search_table_box'>
                                <tr>
                                    <th class='search_box_title' width='150'>조건검색 : </th>
                                    <td class='search_box_item' colspan=3>
                                        <select name=search_type>
                                            <option value=''>통합검색</option>
                                            <option value='cmd.name' " . CompareReturnValue("cmd.name", $search_type, "selected") . ">이름</option>
                                            <option value='gcd.member_id' " . CompareReturnValue("gcd.member_id", $search_type, "selected") . ">아이디</option>
                                            <option value='gcd.gift_code' " . CompareReturnValue("gcd.gift_code", $search_type, "selected") . ">시리얼넘버</option>
                                        </select>
                                        <input type=text name='search_text' class='textbox' value='" . $search_text . "' style='width:50%' >
                                    </td>
                                </tr>
                                <tr>
                                    <th class='search_box_title' width='150'>상태 : </th>
                                    <td class='search_box_item' colspan=3>
                                        <input type='radio' name='gift_change_state' id='gift_change_state_' class='gift_change_state' value='' " . ($gift_change_state == "" ? "checked" : "") . "> <label for='gift_change_state_' >전체</label>
                                        <input type='radio' name='gift_change_state' id='gift_change_state_0' class='gift_change_state' value='0' " . ($gift_change_state == "0" ? "checked" : "") . "> <label for='gift_change_state_0' >발급대기</label>
                                        <input type='radio' name='gift_change_state' id='gift_change_state_1' class='gift_change_state' value='1' " . ($gift_change_state == "1" ? "checked" : "") . "> <label for='gift_change_state_1' >발급완료</label>
                                    </td>
                                </tr>
                                <tr height=27>
                                    <td class='search_box_title' align=center>
                                        <label for='regdate'><b>발급일자</b></label><input type='checkbox' name='regdate' id='regdate' value='1' " . CompareReturnValue("1", $regdate, "checked") . ">
                                    </td>
                                    <td class='search_box_item' colspan=3 style='padding-left:3px;'>
                                        ".search_date('startDate','endDate',$startDate,$endDate,'N','A',' readonly')."
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr >
                        <td colspan=3 align=center style='padding:10px 0px 0px 0px;'>
                            <input type='image' src='../image/bt_search.gif' border=0>
                        </td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
</table>
";


$gift_items = "";

if(is_array($gift_details) && count($gift_details) > 0){
    foreach($gift_details as $key=>$val){

        if ($val[gift_change_state] == 0) {
            $gift_change_state_str = "발급대기";
        } else if ($val[gift_change_state] == 1) {
            $gift_change_state_str = "발급완료";
        } else {
            $gift_change_state_str = "";
        }

        $gift_items .= "<tr height=28 align=center>
				<td bgcolor='#ffffff'><input type=checkbox class=nonborder id='giftcertificate_id' name=ix[] value='" . $val[gcd_ix] . "'></td>
				<td bgcolor='#efefef'>" . $val[gift_code] . "</td>
				<td bgcolor='#ffffff'>" . $val[gift_start_date] . " ~ " . $val[gift_end_date] . "</td>
				<td bgcolor='#efefef'>" . $gift_change_state_str . "</td>
				<td bgcolor='#efefef'>" . $val[name] . "</td>
				<td bgcolor='#ffffff'>" . $val[member_id] . "</td>
				<td bgcolor='#efefef'>" . ($val[member_id] ? $val[use_date] : "-") . "</td>";
        if (checkMenuAuth(md5($_SERVER["PHP_SELF"]), "D")) {
            $gift_items .= "
				<td bgcolor='#ffffff'><a href=\"javascript:DeleteGiftCertificateDetail('" . $val[gcd_ix] . "')\"><img src='../image/btc_del.gif' border=0></a></td>
                ";
        } else {
            $gift_items .= "
				<td bgcolor='#ffffff'><a href=\"" . $auth_delete_msg . "\"><img src='../image/btc_del.gif' border=0></a></td>
                ";
        }
        $gift_items .= "
			</tr>";
    }
}else{
    $gift_items .= "<tr height=60><td colspan=9 align=center>상품권 내용이 없습니다.</td></tr>";
}


$Contents02 = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >

    <tr height=10>
        <td colspan=2>검색 결과 : " . $total . " 건 </td>
        <td colspan=10 align=right style='padding-bottom:5px;'>";
if (checkMenuAuth(md5($_SERVER["PHP_SELF"]), "E")) {
    $Contents02 .= "    
            <a href='?mode=excel&" . $QUERY_STRING . "'><img src='../image/btn_excel_save.gif' border=0></a>";
} else {
    $Contents02 .= "    
            <a href=\"" . $auth_excel_msg . "\"><img src='../image/btn_excel_save.gif' border=0></a>";
}
$Contents02 .= "   
        </td>
    </tr>
</table>
<form id='list_form' name='list_frm' method='POST' action='random_gift.act.php' enctype='multipart/form-data'>
<input type='hidden' name='search_searialize_value' value='" . urlencode(serialize($_GET)) . "'>
<input type='hidden' name='act' value='delete_detail_selected'>
<input type='hidden' name='update_type' value=''>
<input type='hidden' name='gc_ix' value='" . $gc_ix . "'>
    <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
        <tr bgcolor=#efefef align=center height=28>
            <td class='s_td' width=3%><input type=checkbox class=nonborder name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
            <td class='m_td' width=18%>시리얼 넘버 </td>
            <td class='m_td' width=15%>사용가능기간</td>
            <td class='m_td' width=8% >상태</td>
            <td class='m_td' width=10% >이름</td>
            <td class='m_td' width=10% >아이디</td>
            <td class='m_td' width=13% >발급일자</td>
            <td class='e_td' width=7% >관리 </td>
        </tr>
        ".$gift_items."
    </table>
</form>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >	
    <tr height=40>
        <td colspan=8 align=left>
            " . $str_page_bar . "
        </td>
    </tr>
</table>";


$Contents = "<table width='100%' border=0>";
$Contents = $Contents . "<tr><td>$Contents01<br></td></tr>";

$Contents = $Contents . "<tr><td>" . $Contents02 . "<br></td></tr>";
$Contents = $Contents . "<tr height=30><td></td></tr>";

$Contents = $Contents . "</table >";

$help_text = "
	<div id='batch_update_coupon'>
				<table cellpadding=3 cellspacing=0 width=100% style='border:0px; margin-top: 10px;'>
					<tr height=30>
						<td class='input_box_item'>
							<input type='radio' name='detail' value='delete' id='delete' checked><label for='delete'>삭제하기</label>
						</td></tr>
				</table>
			<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
				<tr height=50>
					<td colspan=4 align=center>
						<input type=image src='../images/" . $admininfo["language"] . "/b_save.gif' border=0 style='cursor:pointer;border:0px;' onclick='submitToChange();'>
					</td>
				</tr>
			</table>
	</div>
	";

$select = "
<nobr>
<select id='update_type'>
	<option value='1'>검색한 쿠폰 전체에게</option>
	<option value='2' selected>선택한 쿠폰 전체에게</option>
</select>";

$Contents .= HelpBox($select, $help_text, 700);

$Script = "
<script> 
    function DeleteGiftCertificateDetail(gcd_ix){
        if(confirm('상품권 정보를 정말로 삭제하시겠습니까?')){
            window.frames['iframe_act'].location.href='random_gift.act.php?act=detail_delete&gcd_ix='+gcd_ix;
        }
    }
    
    
    function submitToChange(){
        var update_type = $('#update_type').val();

        if(confirm('상품권을 삭제하시겠습니까?\\n(삭제시 주의사항을 다시 한 번 확인해주세요)')){
            $('input[name=update_type]').val(update_type);
            $('form[name=list_frm]').submit();
        }
    }
    
    
    function clearAll(frm){
        for(i=0;i < frm.giftcertificate_id.length;i++){
            frm.giftcertificate_id[i].checked = false;
        }
    }
    function checkAll(frm){
        for(i=0;i < frm.giftcertificate_id.length;i++){
            frm.giftcertificate_id[i].checked = true;
        }
    }
    function fixAll(frm){
        if (!frm.all_fix.checked){
            clearAll(frm);
            frm.all_fix.checked = false;
    
        }else{
            checkAll(frm);
            frm.all_fix.checked = true;
        }
    }

</script>
";

$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n" . $Script;
$P->OnloadFunction = "";
$P->strLeftMenu = promotion_menu();
$P->Navigation = "프로모션(마케팅)전시 > 랜덤 상품권 발행 내역";
$P->title = "랜덤 상품권 발행 내역";
$P->strContents = $Contents;
echo $P->PrintLayOut();
