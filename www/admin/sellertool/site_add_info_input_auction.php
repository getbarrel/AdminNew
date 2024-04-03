<?php
include ("../class/layout.class");
include ("sellertool.lib.php");
include ("../openapi/openapi.lib.php");

$vdate = date ( "Ymd", time () );
$today = date ( "Y/m/d", time () );
$vyesterday = date ( "Y/m/d", time () + 86400 );
$voneweekafter = date ( "Y/m/d", time () + 86400 * 7 );
$vtwoweekafter = date ( "Y/m/d", time () + 86400 * 14 );
$vfourweekafter = date ( "Y/m/d", time () + 86400 * 28 );
$vyesterday = date ( "Y/m/d", mktime ( 0, 0, 0, substr ( $vdate, 4, 2 ), substr ( $vdate, 6, 2 ), substr ( $vdate, 0, 4 ) ) + 60 * 60 * 24 );
$voneweekafter = date ( "Y/m/d", mktime ( 0, 0, 0, substr ( $vdate, 4, 2 ), substr ( $vdate, 6, 2 ), substr ( $vdate, 0, 4 ) ) + 60 * 60 * 24 * 7 );
$v15after = date ( "Y/m/d", mktime ( 0, 0, 0, substr ( $vdate, 4, 2 ), substr ( $vdate, 6, 2 ), substr ( $vdate, 0, 4 ) ) + 60 * 60 * 24 * 15 );
$vfourweekafter = date ( "Y/m/d", mktime ( 0, 0, 0, substr ( $vdate, 4, 2 ), substr ( $vdate, 6, 2 ), substr ( $vdate, 0, 4 ) ) + 60 * 60 * 24 * 28 );
$vonemonthafter = date ( "Y/m/d", mktime ( 0, 0, 0, substr ( $vdate, 4, 2 ) + 1, substr ( $vdate, 6, 2 ) + 1, substr ( $vdate, 0, 4 ) ) );
$v2monthafter = date ( "Y/m/d", mktime ( 0, 0, 0, substr ( $vdate, 4, 2 ) + 2, substr ( $vdate, 6, 2 ) + 1, substr ( $vdate, 0, 4 ) ) );
$v3monthafter = date ( "Y/m/d", mktime ( 0, 0, 0, substr ( $vdate, 4, 2 ) + 3, substr ( $vdate, 6, 2 ) + 1, substr ( $vdate, 0, 4 ) ) );

$db = new MySQL ();
$add_info_id = $_REQUEST ['add_info_id'];
if ( ! empty ( $add_info_id )) {
	$sql = "SELECT * FROM sellertool_add_info_meta where add_info_id='" . $add_info_id . "' ";
	$db->query ( $sql );
	
	if ($db->total) {
		$result = $db->fetchAll ( 'array', MYSQL_ASSOC );
		foreach ( $result as $key => $val ) :
			$site_addinfo [$val ['meta_key']] = $val ['meta_value'];
		endforeach
		;
	}
	$act = "update";
} else {
	$act = "insert";
}

$Contents = "
<form name='add_info_form' action='site_add_info.act.php' onsubmit='return CheckFormValue(this)' method='POST' target='iframe_act'>
<input type='hidden' name='act' value='$act'>
<input type='hidden' id = 'add_info_id' name='add_info_id' value='" . $add_info_id . "'>
<input type='hidden' id = 'prddiv' name='prddiv' value='" . $site_addinfo ['product_div_code'] . "'>

    <table width='100%' cellpadding=0 cellspacing=0>
		<tr height=30>
			<td style='padding-bottom:10px;'>
				" . colorCirCleBox ( "#efefef", "100%", "<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'><b class=blk> 기본정보 : </b><span class=small><!--굵은 글씨로 되어 있는 항목이 필수 정보입니다.--> " . getTransDiscription ( md5 ( $_SERVER ["PHP_SELF"] ), 'F' ) . " </span> </td><td align=right style='padding-right:20px;' class=small><!--a href='JavaScript:SampleProductInsert()'>샘플데이타 넣기</a--></td></tr></table>" ) . "
			</td>
		</tr>
	</table>
	<table cellpadding=0 cellspacing=0 width='100%' class='input_table_box'>
		<col width=15%>
		<col width=35%>
		<col width=15%>
		<col width=35%>
		<tr height=35>
			<td class='input_box_title'>제휴사 선택 <img src='" . $required3_path . "'> </td>
			<td class='input_box_item' >
				<table border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td style='padding-right:5px;'>
							" . getSellerToolSiteInfo ( $site_addinfo ['site_code'], "validation=true title='제휴사' onchange='selectOpenMarket();'" ) . "
						</td>
					</tr>
				</table>
			</td>
			<td class='input_box_title' nowrap> <b>등록옵션명 <img src='" . $required3_path . "'></b> </td>
			<td class='input_box_item'>
                <input type=text class='textbox' name=add_info_name size=40  value='" . $site_addinfo ['add_info_name'] . "' validation=true title='추가정보이름'>
            </td>
		</tr>
        <tr height=35>
			<td class='input_box_title' nowrap> 상품명 앞에 고정적으로 붙일값 </td>
			<td class='input_box_item'>
                <input type=text class='textbox' name='title_prefix'    size=40  value='" . $site_addinfo ['title_prefix'] . "' title='상품명 앞에 붙일값'>
                ex) [신상 기획]
            </td>
			<td class='input_box_title'> 상품상태 </td>
			<td class='input_box_item'>
				<input type=radio name='ItemStatusType' id='prd_stat_cd_01' value='New' " . (($site_addinfo ['ItemStatusType'] == "" || $site_addinfo ['ItemStatusType'] == "New") ? "checked" : "") . "><label for='prd_stat_cd_01'>새상품</label>
				<input type=radio name='ItemStatusType' id='prd_stat_cd_02' value='Used' " . ($site_addinfo ['ItemStatusType'] == "Used" ? "checked" : "") . "><label for='prd_stat_cd_02'>중고상품</label>
				<input type=radio name='ItemStatusType' id='prd_stat_cd_03' value='Cancel' " . ($site_addinfo ['ItemStatusType'] == "Cancel" ? "checked" : "") . "><label for='prd_stat_cd_03'>취소상품</label>
				<input type=radio name='ItemStatusType' id='prd_stat_cd_04' value='CarriedOver' " . ($site_addinfo ['ItemStatusType'] == "CarriedOver" ? "checked" : "") . "><label for='prd_stat_cd_04'>이월상품</label><br>
				<input type=radio name='ItemStatusType' id='prd_stat_cd_05' value='Exhibit' " . ($site_addinfo ['ItemStatusType'] == "Exhibit" ? "checked" : "") . "><label for='prd_stat_cd_05'>전시상품</label>
				<input type=radio name='ItemStatusType' id='prd_stat_cd_06' value='Refurbished' " . ($site_addinfo ['ItemStatusType'] == "Refurbished" ? "checked" : "") . "><label for='prd_stat_cd_06'>리퍼상품</label>
				<input type=radio name='ItemStatusType' id='prd_stat_cd_07' value='Returned' " . ($site_addinfo ['ItemStatusType'] == "Returned" ? "checked" : "") . "><label for='prd_stat_cd_07'>반품상품</label>
				<input type=radio name='ItemStatusType' id='prd_stat_cd_08' value='Stock' " . ($site_addinfo ['ItemStatusType'] == "Stock" ? "checked" : "") . "><label for='prd_stat_cd_08'>재고상품</label>
			</td>
		</tr>
		<tr height=35>
			<td class='input_box_title'> 미성년자 구매가능 </td>
			<td class='input_box_item'>
				<input type=radio name='IsAdult' id='minor_sel_cn_yn_N' value='N' " . ($site_addinfo ['IsAdult'] == "N" ? "checked" : "") . "><label for='minor_sel_cn_yn_N'>구매가능</label>
				<input type=radio name='IsAdult' id='minor_sel_cn_yn_Y' value='Y' " . (($site_addinfo ['IsAdult'] == "" || $site_addinfo ['IsAdult'] == "Y") ? "checked" : "") . "><label for='minor_sel_cn_yn_Y'>구매불가</label>
			</td>
			<td class='input_box_title'> 모바일 구매가능 </td>
			<td class='input_box_item'>
				<input type=radio name='mobile_sell_yn' id='mobile_sell_y' value='Y' " . ( ($site_addinfo ['mobile_sell_yn'] == "" || $site_addinfo ['mobile_sell_yn']  == "Y" ) ? "checked" : "") . "><label for='mobile_sell_Y'>구매가능</label>
				<input type=radio name='mobile_sell_yn' id='mobile_sell_n' value='N' " . ( $site_addinfo ['mobile_sell_yn'] == "N" ? "checked" : "") . "><label for='mobile_sell_n'>구매불가</label>
			</td>
		</tr>
		<!-- ApplyPeriod : 7, 15, 30, 60, 90일 설정 가능-->
		<tr height=35>
			<td class='input_box_title'> 판매기간설정 <img src='" . $required3_path . "'></td>
			<td class='input_box_item'>
				<input type='radio' name='ApplyPeriod' id='period_7'  class='textbox' value='7'  " . (( $site_addinfo ['ApplyPeriod']  == "7" ) ? "checked" : "") . "/><label for='period_7'>7일</label>
				<input type='radio' name='ApplyPeriod' id='period_15' class='textbox' value='15' " . (( $site_addinfo ['ApplyPeriod']  == "15" ) ? "checked" : "") . "/><label for='period_15'>15일</label>
				<input type='radio' name='ApplyPeriod' id='period_30' class='textbox' value='30' " . (( $site_addinfo ['ApplyPeriod']  == "30" ) ? "checked" : "") . "/><label for='period_30'>30일</label>
				<input type='radio' name='ApplyPeriod' id='period_60' class='textbox' value='60' " . (( $site_addinfo ['ApplyPeriod']  == "60" ) ? "checked" : "") . "/><label for='period_60'>60일</label>
				<input type='radio' name='ApplyPeriod' id='period_90' class='textbox' value='90' " . (( $site_addinfo ['ApplyPeriod'] == "" || $site_addinfo ['ApplyPeriod']  == "90" ) ? "checked" : "") . "/><label for='period_90'>90일</label>
			</td>
			<td class='input_box_title' nowrap> <b>배송가능지역<img src='" . $required3_path . "'></b> </td>
			<td class='input_box_item'>
				<select name='selling_area'>
					<option value='Busan' 					" . ($site_addinfo ['selling_area'] == "Busan" 					? "selected" : "") . "	>부산</option>
					<option value='BusanUlsan' 				" . ($site_addinfo ['selling_area'] == "BusanUlsan" 			? "selected" : "") . "	>부산/울산</option>
					<option value='Chungbuk' 				" . ($site_addinfo ['selling_area'] == "Chungbuk" 				? "selected" : "") . "	>충북</option>
					<option value='ChungCheong' 			" . ($site_addinfo ['selling_area'] == "ChungCheong" 			? "selected" : "") . "	>충청</option>
					<option value='Chungnam' 				" . ($site_addinfo ['selling_area'] == "Chungnam" 				? "selected" : "") . "	>충남</option>
					<option value='Daegu' 					" . ($site_addinfo ['selling_area'] == "Daegu" 					? "selected" : "") . "	>대구</option>
					<option value='Daejeon' 				" . ($site_addinfo ['selling_area'] == "Daejeon" 				? "selected" : "") . "	>대전</option>
					<option value='Gangwon' 				" . ($site_addinfo ['selling_area'] == "Gangwon" 				? "selected" : "") . "	>강원</option>
					<option value='Gwangju' 				" . ($site_addinfo ['selling_area'] == "Gwangju" 				? "selected" : "") . "	>광주</option>
					<option value='Gyeongbuk' 				" . ($site_addinfo ['selling_area'] == "Gyeongbuk" 				? "selected" : "") . "	>경북</option>
					<option value='Gyeonggi' 				" . ($site_addinfo ['selling_area'] == "Gyeonggi" 				? "selected" : "") . "	>경기</option>
					<option value='Gyeongnam' 				" . ($site_addinfo ['selling_area'] == "Gyeongnam" 				? "selected" : "") . "	>경남</option>
					<option value='Gyeongsang' 				" . ($site_addinfo ['selling_area'] == "Gyeongsang" 			? "selected" : "") . "	>경상</option>
					<option value='Inchon' 					" . ($site_addinfo ['selling_area'] == "Inchon" 				? "selected" : "") . "	>인천</option>
					<option value='Jeju' 					" . ($site_addinfo ['selling_area'] == "Jeju" 					? "selected" : "") . "	>제주</option>
					<option value='Jeolla' 					" . ($site_addinfo ['selling_area'] == "Jeolla" 				? "selected" : "") . "	>전라</option>
					<option value='Jeonbuk' 				" . ($site_addinfo ['selling_area'] == "Jeonbuk" 				? "selected" : "") . "	>전북</option>
					<option value='Jeonnam' 				" . ($site_addinfo ['selling_area'] == "Jeonnam" 				? "selected" : "") . "	>전남</option>
					<option value='Nationwide' 				" . (( $site_addinfo ['selling_area'] == "Nationwide" || $site_addinfo ['selling_area'] == '' ) ? "selected" : "") . "	>전국</option>
					<option value='NationwidExceptIslands' 	" . ($site_addinfo ['selling_area'] == "NationwidExceptIslands" ? "selected" : "") . "	>전국(제주, 도서지역 제외)</option>
					<option value='Seoul' 					" . ($site_addinfo ['selling_area'] == "Seoul" 					? "selected" : "") . "	>서울</option>
					<option value='SeoulGyeonggi' 			" . ($site_addinfo ['selling_area'] == "SeoulGyeonggi" 			? "selected" : "") . "	>서울/경기</option>
					<option value='SeoulGyeonggiDaejeon' 	" . ($site_addinfo ['selling_area'] == "SeoulGyeonggiDaejeon" 	? "selected" : "") . "	>서울/경기/대전</option>
					<option value='Ulsan' 					" . ($site_addinfo ['selling_area'] == "Ulsan" 					? "selected" : "") . "	>울산</option>
				</select>
			</td>
		</tr>
		<tr height=55>
			<td class='input_box_title' nowrap> <b>출고지 주소 <img src='" . $required3_path . "'></b> </td>
			<td class='input_box_item' id='outaddress_area'>
				<select id='outaddress_select'>
                    <option>제휴사 선택이 필요합니다.</option>
                </select>
			</td>
			<td class='input_box_title' nowrap> <b>반품/교환지 주소 <img src='" . $required3_path . "'></b> </td>
			<td class='input_box_item' id='inaddress_area'>
				<select id='inaddress_select'>
                    <option>제휴사 선택이 필요합니다.</option>
                </select>
			</td>
		</tr>
		<tr height=55>
			<td class='input_box_title' nowrap> <b>A/S 안내 <img src='" . $required3_path . "'></b> </td>
			<td class='input_box_item' colspan=3 style='padding:5px 5px;'>
			<textarea class='textbox' name=as_detail  style='width:96%;height:50px;' validation=true title='A/S 안내'>" . $site_addinfo ['as_detail'] . "</textarea>
			</td>
		</tr>
		<tr height=35>
			<td class='input_box_title'> 반품 교환안내 </td>
			<td class='input_box_item' colspan=3 style='padding:5px 5px;'>
			<textarea class='textbox' name=rtng_exch_detail  style='width:96%;height:50px;' value='' validation=true title='반품 교환안내'>" . $site_addinfo ['rtng_exch_detail'] . "</textarea>
			</td>
		</tr>				
		<tr height=35>
			<td class='input_box_title'> 가격비교사이트 등록여부 </td>
			<td class='input_box_item'>
				<input type=radio name='prc_cmp_exp_yn' id='prc_cmp_exp_yn_Y' value='Y' " . (($site_addinfo ['prc_cmp_exp_yn'] == "" || $site_addinfo ['prc_cmp_exp_yn'] == "Y") ? "checked" : "") . "><label for='prc_cmp_exp_yn_Y'>등록가능</label>
				<input type=radio name='prc_cmp_exp_yn' id='prc_cmp_exp_yn_N' value='N' " . ($site_addinfo ['prc_cmp_exp_yn'] == "N" ? "checked" : "") . "><label for='prc_cmp_exp_yn_N'>등록불가</label>
			</td>
			<td class='input_box_title'> 등록옵션 사용여부</td>
			<td class='input_box_item'>
				<input type=radio name='disp' id='disp_Y' value='Y' " . (($site_addinfo ['disp'] == "" || $site_addinfo ['disp'] == "Y") ? "checked" : "") . "><label for='disp_Y'>사용</label>
				<input type=radio name='disp' id='disp_N' value='N' " . ($site_addinfo ['disp'] == "N" ? "checked" : "") . "><label for='disp_N'>사용하지않음</label>
			</td>
		</tr>
        <tr>
            <td class='input_box_title'> 브랜드 </td>
    		<td class='input_box_item'colspan=3>
    		<table cellpadding=0 cellspacing=0>
    			<tr>
    				<td><div id='brand_select_area'>" . BrandListSelect ( $site_addinfo ['brand_index'], $cid ) . "</div></td>
                    <td><span># 브랜드 지정시 상품에 설정된 브랜드 보다 우선순위로 설정됩니다.(브랜드별로 옵션을 만들어서 사용하세요.)</span></td>
    			</tr>
    		</table>
    		</td>
        </tr>
		</table>
<table width='100%' cellpadding=0 cellspacing=0>
	<tr>";
if (checkMenuAuth ( md5 ( $_SERVER ['PHP_SELF'] ), "C" )) {
	$Contents .= "
		<td colspan=2 align=center style='padding:10px 0px;'><input type=image src='../images/" . $admininfo ['language'] . "/b_save.gif' border=0 align=absmiddle></td>";
} else {
	$Contents .= "
		<td colspan=2 align=center style='padding:10px 0px;'><a href=\"" . $auth_write_msg . "\"><img src='../images/" . $admininfo ['language'] . "/b_save.gif' border=0 align=absmiddle></a></td>";
}
$Contents .= "
	</tr>
</table>
    </form>
    ";

$Script = "<link type='text/css' href='../js/themes/base/ui.all.css' rel='stylesheet' />
<script type='text/javascript' src='../js/ui/ui.core.js'></script>
<script type='text/javascript' src='../js/ui/ui.datepicker.js'></script>
<script>
$(function() {
        var add_info_id;
        add_info_id = $('#add_info_id').val();
        
        if(add_info_id != ''){
            selectOpenMarket();
        }
        
		$(\"#start_datepicker\").datepicker({
		monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		showMonthAfterYear:true,
		dateFormat: 'yy/mm/dd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){
			if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
				$('#end_datepicker').val(dateText);
			}else{
				$('#end_datepicker').datepicker('setDate','+0d');
			}
		}

		});

		$(\"#end_datepicker\").datepicker({
		monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		showMonthAfterYear:true,
		dateFormat: 'yy/mm/dd',
		buttonImageOnly: true,
		buttonText: '달력'

		});

		
	});



	function select_date(FromDate,ToDate,dType) {
		var frm = document.searchmember;

		$(\"#start_datepicker\").val(FromDate);
		$(\"#end_datepicker\").val(ToDate);
	}
    
    function selectOpenMarket(){
        $(function(){
            var site_code = $('#site_code :selected').val();
            var prddiv = $('#prddiv').val();
            var addr_seq = $('#addr_seq_out').val();
            if(site_code != ''){
                $.ajax({
                    type: 'POST',
                    url: '/admin/sellertool/ajax_controller.php',
                    data: {'act':'getInAddress','site_code': site_code},
                    success : function(response) {
                        $('#inaddress_select').remove();
                        $('#inaddress_area').html(response);
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: '/admin/sellertool/ajax_controller.php',
                    data: {'act':'getOutAddress','site_code': site_code, 'selected':addr_seq},
                    success : function(response) {
                        $('#outaddress_select').remove();
                        $('#outaddress_area').html(response);
                    }
                });
            }
        });
    }
    function select_out_addr(){
    	var site_code = $('#site_code :selected').val();
    	var addr_seq = $('#addr_seq_out').val();
    	$.ajax({
            type: 'POST',
            url: '/admin/sellertool/ajax_controller.php',
            data: {'act':'getOutAddress','site_code': site_code, 'selected':addr_seq},
            success : function(response) {
                $('#outaddress_select').remove();
                $('#outaddress_area').html(response);
            }
        });
    }
</script>";

if ($mmode == "pop") {
	$P = new ManagePopLayOut ();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu ();
	$P->Navigation = "셀러툴 > 상품등록 옵션 등록";
	$P->title = "상품등록 옵션 등록";
	$P->strContents = $Contents;
	echo $P->PrintLayOut ();
} else {
	$P = new LayOut ();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu ();
	$P->Navigation = "셀러툴 > 상품등록 옵션 등록";
	$P->title = "상품등록 옵션 등록";
	$P->strContents = $Contents;
	echo $P->PrintLayOut ();
}