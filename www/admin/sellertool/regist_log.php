<?
include("../class/layout.class");
include_once("sellertool.lib.php");

$db = new Database;
$db2 = new Database;

if(empty($max)){
	$max = 20; //페이지당 갯수
}

if(empty($link_type)){
	$link_type = 'regist';
}

if ($startDate == ""){
	$startDate = date("Y-m-d 00:00:00", strtotime('-7 day') );
	$endDate = date("Y-m-d 23:59:59");
}else{
	$startDate = date("Y-m-d H:i:s",mktime($_GET["startDate_h"],$_GET["startDate_i"],$_GET["startDate_s"],substr($_GET["startDate"],5,2),substr($_GET["startDate"],8,2),substr($_GET["startDate"],0,4)));
	$endDate = date("Y-m-d H:i:s",mktime($_GET["endDate_h"],$_GET["endDate_i"],$_GET["endDate_s"],substr($_GET["endDate"],5,2),substr($_GET["endDate"],8,2),substr($_GET["endDate"],0,4)));
}

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <col width='20%'>
	  <col width='*'>
	  <tr >
		<td align='left' colspan=6 > ".GetTitleNavigation("제휴사 연동로그", "제휴사연동 > 기본정보 설정 > 제휴사 연동로그 ")."</td>
	  </tr>";

$Contents01 .= " <tr>
			<td colspan=6 >
				<div class='tab' style='width:100%;height:30px;margin:0px 0 0 0;'>
				<table class='s_org_tab' cellpadding='0' cellspacing='0' border='0'>
					<tr>
						<td class='tab'>
							<table id='tab_01' ".( $link_type == "regist" ? "class='on'" : "" ).">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"location.href='?link_type=regist'\" style='padding-left:20px;padding-right:20px;'>
									상품등록
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' ".( $link_type == "delivery" ? "class='on'" : "" ).">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"location.href='?link_type=delivery'\" style='padding-left:20px;padding-right:20px;'>
									주문/배송관련
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03' ".( $link_type == "bbs" ? "class='on'" : "" ).">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"location.href='?link_type=bbs'\" style='padding-left:20px;padding-right:20px;'>
									Qna/알림
								</td>
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
        <tr>
            <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>제휴사 연동로그 검색</b></div>")."</td>
        </tr>
		
    </table>
        <form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' style='display:inline;'>
			<input type=hidden name='link_type' value='".$link_type."'>
            <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='input_table_box'>
                <col width='15%'>
                <col width='35'>
                <col width='15%'>
                <col width='35%'>
                <tr>
                    <td class='input_box_title'>제휴사 선택</td>
                    <td class='input_box_item' >
                    	<table border=0 cellpadding=0 cellspacing=0>
                    		<tr>
                    			<td style='padding-right:5px;'>
                                    ".getSellerToolSiteInfo($site_code)."
                                </td>
                    		</tr>
                    	</table>
                    </td>
                    <td class='input_box_title'> <b>처리상태 </b></td>
                    <td class='input_box_item'>
						<input type=radio name='result_code' id='result_code' value='' ".($result_code == '' ? "checked" : "")."><label for='result_code'>전체</label>
                    	<input type=radio name='result_code' id='result_code_Y' value='Y' ".($result_code == 'Y' ? "checked" : "")."><label for='result_code_Y'>성공</label>
                    	<input type=radio name='result_code' id='result_code_N' value='N' ".($result_code == 'N' ? "checked" : "")."><label for='result_code_N'>실패</label>
                    </td>
                </tr>
				<tr>
                    <td class='input_box_title'> <b>로그 일자</b></td>
                    <td class='input_box_item' colspan='3'>
						".search_date('startDate','endDate',$startDate,$endDate,'Y')."
                    </td>
                </tr>";

if($link_type == "regist"){
				$Contents01 .= "
				<tr>
					<td class='search_box_title' >판매상태</td>
					<td class='search_box_item'>
						<table cellpadding=0 cellspacing=0 border='0'  align='left'>
						<col width=100>
						<col width=100>
						<col width=100>
						<col width=100> 
						<tr>
							<td>
							<input type='checkbox' name='state[]' id='search_state_1' value='1' title='판매중' ".(is_array($state)?in_array('1',$state)?'checked':'':'')."/>
							<label for='search_state_1'> 판매중 </label> 
							</td>
							<td>
							<input type='checkbox' name='state[]' id='search_state_0' value='0' title='일시품절' ".(is_array($state)?in_array('0',$state)?'checked':'':'')."/>
							<label for='search_state_0'> 일시품절 </label> 
							</td>
							<td>
							<input type='checkbox' name='state[]' id='search_state_2' value='2' title='판매중지' ".(is_array($state)?in_array('2',$state)?'checked':'':'')."/>
							<label for='search_state_2'> 판매중지</label>
							</td>
							<td></td>
						</tr>
						<tr>
							<td>
							<input type='checkbox' name='state[]' id='search_state_8' value='8' title='승인거부' ".(is_array($state)?in_array('8',$state)?'checked':'':'')."/> 
							<label for='search_state_8'> 승인거부 </label>
							</td>
							
							<td>
							<input type='checkbox' name='state[]' id='search_state_6' value='6' title='승인대기' ".(is_array($state)?in_array('6',$state)?'checked':'':'')."/> 
							<label for='search_state_6'> 승인대기 </label>
							</td>

							<td>
							<input type='checkbox' name='state[]' id='search_state_7' value='7' title='수정대기상품' ".(is_array($state)?in_array('7',$state)?'checked':'':'')."/> 
							<label for='search_state_7'> 수정대기상품 </label>
							</td>

							<td>
							<input type='checkbox' name='state[]' id='search_state_9' value='9' title='판매금지' ".(is_array($state)?in_array('9',$state)?'checked':'':'')."/> 
							<label for='search_state_9'> 판매금지 </label>
							</td>
							";


$Contents01 .=	"
						</tr>
						</table>
					</td>
					<td class='search_box_title'>노출여부</td>
					<td class='search_box_item'>
						<table cellpadding=0 cellspacing=0 border='0'  align='left'>
						<col width=60>
						<col width=100>
						<tr>
							<td>
								<input type='checkbox' name='disp[]' id='disp_1' value='1' title='노출' ".(is_array($disp)?in_array('1',$disp)?'checked':'':'')."/><label for='disp_1'> 노출 </label> 
							</td>
							<td>
								<input type='checkbox' name='disp[]' id='disp_0' value='0' title='미노출' ".(is_array($disp)?in_array('0',$disp)?'checked':'':'')."/><label for='disp_0'> 미노출 </label> 
							</td>
							<td></td>
						</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class='search_box_title'>브랜드</td>
					<td class='search_box_item'>
					".BrandListSelect($brand, $cid)."
					</td>
					<td class='search_box_title'>셀러업체</td>
					<td class='search_box_item'>
						".companyAuthList($company_id , "validation=false title='셀러업체' ")."
					</td>
				</tr>
				";

				}
				$Contents01 .= "
                <tr>
                	<td class='input_box_title'>  검색어 <input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> 
								<label for='mult_search_use'>(다중검색 체크)</label> </td>
                	<td class='input_box_item' >
                		<table cellpadding=0 cellspacing=0 width=400>
                			<col width='".($link_type == "delivery" ? "150" : "100")."px'>
							<col width='*'>
							<tr>
                				<td >
                				    <select name='search_type'>";
									if($link_type == "delivery"){
										$Contents01 .= "
										<option value='target.oid' ".($search_type == 'target.oid' ? "selected" : "").">주문번호</option>
										<option value='target.co_oid' ".($search_type == 'target.co_oid' ? "selected" : "").">제휴사주문번호</option>
										<option value='target.co_od_ix' ".($search_type == 'target.co_od_ix' ? "selected" : "").">제휴사주문번호(옥션)</option>";
									}elseif($link_type == "bbs"){
										$Contents01 .= "
										<option value='target.bbs_subject' ".($search_type == 'target.bbs_subject' ? "selected" : "").">제목</option>
										<option value='target.bbs_name' ".($search_type == 'target.bbs_name' ? "selected" : "").">작성자</option>";
									}else{
										$Contents01 .= "
										<option value='target.pname' ".($search_type == 'target.pname' ? "selected" : "").">상품명</option>
										<option value='target.pcode' ".($search_type == 'target.pcode' ? "selected" : "").">상품코드</option>
										<option value='target.id' ".($search_type == 'target.id' ? "selected" : "").">상품코드(키)</option>";
									}
									$Contents01 .= "

										<option value='sl.result_msg' ".($search_type == 'sl.result_msg' ? "selected" : "").">메세지</option>
									</select>
                				</td>
								<td >
                				    <div id='search_text_input_div'>
										<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
									</div>
									<div id='search_text_area_div' style='display:none;'>
										<textarea name='search_text' name='search_text_area' id='search_text_area' class='tline textbox' style='padding:0px;height:90px;width:150px;' >".$search_text."</textarea>
									</div>
                				</td>
                				<td colspan=2 style='padding-left:5px;'>
                                </td>
                			</tr>
                		</table>
                    </td>
					<td class='input_box_title'> 검색수 </td>
                	<td class='input_box_item' >
                		<select name='max' style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle>
							<option value='5' ".CompareReturnValue(5,$max).">5</option>
							<option value='10' ".CompareReturnValue(10,$max).">10</option>
							<option value='20' ".CompareReturnValue(20,$max).">20</option>
							<option value='40' ".CompareReturnValue(40,$max).">40</option>
							<option value='50' ".CompareReturnValue(50,$max).">50</option>
							<option value='100' ".CompareReturnValue(100,$max).">100</option>
						</select>
                    </td>
                </tr>        
            </table>
            <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
                <tr bgcolor=#ffffff ><td colspan=4 align=center style='padding:20px 0px;'><input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
            </table>
        </form>";

$Contents02 = "
	<div style='width:100%;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'>
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
		<tr>
			<td align='left'>
				<img src='../image/title_head.gif' align=absmiddle> <b class=blk>제휴사 연동 결과</b>
			</td>
			<td align='right'>
				<span class='helpcloud' help_height='30' help_html='로그 정보를 엑셀로 다운로드 하실 수 있습니다'>
				<img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align='absmiddle' style='cursor:pointer' onclick=\"location.href='?mmode=excel&".$QUERY_STRING."';\" ></span>
			</td>
		</tr>
	</table>
	</div>")."</div>";

$Contents02 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box' style='margin-top:3px;' >
        <col width=9%>
        <col width=7%>
		<col width=7%>
        <col width=*>
        <col width=15%>
        <col width=8%>
        <col width=25%>
		<col width=13%>
	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>";
	  if($link_type=='delivery'){
	    $Contents02 .= "
	    <td class='m_td'> 주문번호</td>";
	  }elseif($link_type=='bbs'){
		$Contents02 .= "
	    <td class='m_td'> 작성자</td>";
	  }else{
		$Contents02 .= "
		<td class='m_td'> 상품코드</td>";
	  }
		$Contents02 .= "
		<td class='m_td'> 제휴사</td>
		<td class='m_td'> 연동구분</td>";
	  if($link_type=='bbs'){
		$Contents02 .= "
	   <td class='m_td'> 게시글 제목</td>";
	  }else{
		$Contents02 .= "
		<td class='m_td'> 상품명</td>";
	  }
		$Contents02 .= "
		<td class='m_td'> 제휴사 상품코드</td>
		<td class='m_td'> 처리상태</td>
		<td class='m_td'> 메세지</td>
        <td class='m_td'> 등록일자</td> 
	  </tr>";



if($link_type == "delivery"){
	$target_table = "shop_order_detail";
	$target_select = ",target.pname,target.oid";
	$where = "where sl.target_pid = target.od_ix and type='delivery' ";
}elseif($link_type == "bbs"){
	$target_table = TBL_SHOP_PRODUCT_QNA;
	$target_select = ",target.bbs_name,target.bbs_ix,target.bbs_subject";
	$where = "where sl.target_pid = target.bbs_ix and type='bbs' ";
}else{
	$target_table = "shop_product";
	$target_select = ",concat('[',target.brand_name,']',target.pname) as pname ,target.id";
	$where = "where sl.pid = target.id and type='regist' ";
}

$where .= "and sl.regist_date between '".$startDate." 00:00:00' and '".$endDate." 23:59:59' ";

if($site_code != ""){
	$where .= "and site_code = '".trim($site_code)."' ";
}

if($result_code !=""){
	if($result_code == 'Y'){
		$where .= "and result_code = '200' ";
	}else{
		$where .= "and result_code != '200' ";
	}
}

if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
	//다중검색 시작 2014-04-10 이학봉
	if($search_text != ""){
		if(strpos($search_text,",") !== false){
			$search_array = explode(",",$search_text);
			$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
			$where .= "and ( ";
			for($i=0;$i<count($search_array);$i++){
				$search_array[$i] = trim($search_array[$i]);
				if($search_array[$i]){
					if($i == count($search_array) - 1){
						$where .= $search_type." = '".trim($search_array[$i])."'";
					}else{
						$where .= $search_type." = '".trim($search_array[$i])."' or ";
					}
				}
			}
			$where .= ")";
		}else if(strpos($search_text,"\n") !== false){//\n
			$search_array = explode("\n",$search_text);
			$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
			$where .= "and ( ";

			for($i=0;$i<count($search_array);$i++){
				$search_array[$i] = trim($search_array[$i]);
				if($search_array[$i]){
					if($i == count($search_array) - 1){
						$where .= $search_type." = '".trim($search_array[$i])."'";
					}else{
						$where .= $search_type." = '".trim($search_array[$i])."' or ";
					}
				}
			}
			$where .= ")";
		}else{
			$where .= " and ".$search_type." = '".trim($search_text)."'";
		}
	}

}else{	//검색어 단일검색
	if($search_text != ""){
		$where = $where."and ".$search_type." LIKE '%".trim($search_text)."%' ";
	}
}

//판매상태 검색관련
if(is_array($state)){		//판매상태 (판매중, 일시품절, 본사대기 ... )
	if(count($state)>0){
		$where.=" AND target.state IN ('".implode("','",$state)."')";
	}
}else{
	if($state != ""){
		$where .= " and target.state = '".$state."'";
	}else{
		$state='';
	}
}

//노출여부 검색관련
if(is_array($disp)){		//노출여부 
	if(count($disp)>0){
		$where.=" AND target.disp IN ('".implode("','",$disp)."')";
	}
}else{
	if($disp != ""){
		$where .= " and target.disp = '".$disp."'";
	}else{
		//$disp=array();
		$disp='';
	}
}

if($b_ix != ""){	//브랜드검색
	$where .= " and target.brand = '".$b_ix."'";
}

if($company_id != ""){	//셀러별 검색
	$where .= " and target.admin = '".$company_id."'";
}


$sql = "select 
	SQL_CALC_FOUND_ROWS
	sl.type
	,sl.site_code
	,sl.result_pno
	,sl.result_code
	,sl.result_msg
	,sl.regist_date
	".$target_select."
from
	sellertool_log sl , ".$target_table." target
$where 
	order by sl.regist_date desc ";

if($mmode!='excel'){
	$sql .= " limit $start , $max ";
}

$db->query($sql);
$lists = $db->fetchall("object");

$db->query("select FOUND_ROWS() as total ");
$db->fetch();
$total = $db->dt[total];


if($mmode=='excel'){
	
	include("../include/phpexcel/Classes/PHPExcel.php");
	
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

	date_default_timezone_set('Asia/Seoul');

	$Excel = new PHPExcel();
	$Excel->getProperties()->setCreator("포비즈 코리아")
						 ->setLastModifiedBy("Mallstory.com")
						 ->setTitle("sellertool Log")
						 ->setSubject("sellertool Log")
						 ->setDescription("generated by forbiz korea")
						 ->setKeywords("mallstory")
						 ->setCategory("sellertool Log");
	
	if($link_type == "delivery"){
		$excel_file_name = '주문/배송';
		$excel_title = array(
			'주문번호'
			,'제휴사'
			,'연동구분'
			,'상품명'
			,'제휴사 상품코드'
			,'처리상태'
			,'메세지'
			,'등록일자'
		);
	}elseif($link_type == "bbs"){
		$excel_file_name = '작성자';
		$excel_title = array(
			'작성자'
			,'제휴사'
			,'연동구분'
			,'게시글 제목'
			,'제휴사 상품코드'
			,'처리상태'
			,'메세지'
			,'등록일자'
		);
	}else{
		$excel_file_name = '상품등록';
		$excel_title = array(
			'상품코드'
			,'제휴사'
			,'연동구분'
			,'상품명'
			,'제휴사 상품코드'
			,'처리상태'
			,'메세지'
			,'등록일자'
		);
	}

	// 헤더 타이틀
	for ($i=0,$col='A'; $i < count($excel_title); $i++,$col++){
		$Excel->getActiveSheet()->setCellValue($col . "1", $excel_title[$i]);
	}
	
	$Excel->getActiveSheet()->setTitle('sellertool Log');
	$Excel->setActiveSheetIndex(0);


	for($i=0,$col='A';$i < count($lists); $i++,$col='A'){
		$rownum = $i + 2;

		if($link_type == "delivery"){
			$point = $lists[$i][oid];
			$type = '주문';
		}elseif($link_type == "bbs"){
			$point = $lists[$i][bbs_name];
			$type = '게시판';
		}else{
			$point = $lists[$i][id];
			$type = '상품등록';
		}
		
		$Excel->getActiveSheet()->setCellValueExplicit($col . ($rownum), $point, PHPExcel_Cell_DataType::TYPE_STRING);
		//$Excel->getActiveSheet()->setCellValue($col . ($rownum), $point);
		$col++;
		$Excel->getActiveSheet()->setCellValue($col . ($rownum), $lists[$i][site_code]);
		$col++;
		$Excel->getActiveSheet()->setCellValue($col . ($rownum), $type);
		$col++;
		$Excel->getActiveSheet()->setCellValue($col . ($rownum), ($link_type == "bbs" ? $lists[$i][bbs_subject] : $lists[$i][pname] ));
		$col++;
		$Excel->getActiveSheet()->setCellValue($col . ($rownum), $lists[$i][result_pno]);
		$col++;
		$Excel->getActiveSheet()->setCellValue($col . ($rownum), ($lists[$i][result_code] == "200" ? "성공":"에러"));
		$col++;
		$Excel->getActiveSheet()->setCellValue($col . ($rownum), $lists[$i][result_msg]);
		$col++;
		$Excel->getActiveSheet()->setCellValue($col . ($rownum), $lists[$i][regist_date]);
	}

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.iconv("UTF-8","CP949",'제휴연동로그_'.$excel_file_name).'_'.date("Ymd").'.xls"');
	header('Cache-Control: max-age=0');


	$Excel = PHPExcel_IOFactory::createWriter($Excel, 'Excel5');
	//$objWriter = PHPExcel_IOFactory::createWriter($Excel, 'CSV');
	//$objWriter->setUseBOM(true);
	$Excel->save('php://output');
	exit;

}


$search_query = "&link_type=$link_type&site_code=$site_code&result_code=$result_code&mult_search_use=$mult_search_use&search_text=$search_text&search_type=$search_type&max=$max&startDate=$startDate&endDate=$endDate";
$str_page_bar = page_bar($total, $page,$max, $search_query ,'');

if($total){
	for($i=0;$i < count($lists); $i++){

	if($lists[$i][type] == "regist"){
		$type_str = "상품등록";
		$point = "<a href=\"../product/goods_input.php?id=".$lists[$i][id]."\" target='_blank'>".$lists[$i][id]."</a>";
	}else if($lists[$i][type] == "delivery"){
		$type_str = "주문";
		$point = "<a href=\"../order/orders.edit.php?oid=".$lists[$i][oid]."\" target='_blank'>".$lists[$i][oid]."</a>";
	}else if($lists[$i][type] == "bbs"){
		$type_str = "작성자";
		$point = $lists[$i][bbs_name];
	}

	$Contents02 .= "
		  <tr bgcolor=#ffffff height=30 align=center>
			<td class='list_box_td point' >
				".$point."
			</td>
			<td class='list_box_td list_bg_gray'>".$lists[$i][site_code]."</td>
			<td class='list_box_td'>".$type_str."</td>
			<td class='list_box_td' style='text-align:left;'>".($lists[$i][type] == "bbs" ? $lists[$i][bbs_subject] : $lists[$i][pname])."</td>
		    <td class='list_box_td list_bg_gray'>".($lists[$i][result_pno])."</td>	
			<td class='list_box_td'>".($lists[$i][result_code] == "200" ? "성공":"에러")."</td>
		    <td class='list_box_td list_bg_gray' style='padding:10px 5px;text-align:left;line-height:130%;'>".($lists[$i][result_msg])."</td>
            <td class='list_box_td' style='padding:5px;'>".$lists[$i][regist_date]."</td> 
		  </tr> ";
	}
}else{
	$Contents02 .= "
		  <tr bgcolor=#ffffff height=50>
		    <td align=center colspan=8> 제휴사연동 로그정보가 없습니다. </td>
		  </tr>";
}



$Contents02 .= "<table width=100%>
						<tr>
						<td style='padding:10px 0px;text-align:right;'>".$str_page_bar."</td>
						</tr>
				</table>";



$Contents .= "<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents .= "<tr><td width='100%'>";
$Contents .= $Contents01."<br>";
$Contents .= "</td></tr>";
$Contents .= "<tr><td>".$ContentsDesc01."</td></tr>";
$Contents .= "<tr height=10><td></td></tr>";
$Contents .= "<tr height=10><td></td></tr>";
$Contents .= "<tr><td>".$Contents02."<br></td></tr>";
$Contents .= "<tr height=30><td></td></tr>";
$Contents .= "</table >";
$Contents .= "</form>";


$help_text = "
	<table cellpadding=1 cellspacing=0  >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >제휴사 연동시 상품, 주문정보 등 연동에 따른 결과를 조회해 보실수 있습니다. </td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ></td></tr>
	</table>
	";
//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'D');

	$help_text = HelpBox("제휴사 연동로그보기", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>
	//다중검색어 시작 2014-04-10 이학봉
	$(document).ready(function() {
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
	})
	//다중검색어 끝 2014-04-10 이학봉
 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu();
	$P->Navigation = "제휴사연동 > 제휴사 연동로그";
	$P->title = "제휴사 연동로그";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu();
	$P->Navigation = "제휴사연동 > 제휴사 연동로그";
	$P->title = "제휴사 연동로그";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

?>