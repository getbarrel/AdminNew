<?
// 대량 상품수정 실행파일 2014-07-02 이학봉
include("../class/layout.class");
include '../include/phpexcel/Classes/PHPExcel.php';

if($admininfo[company_id] == ""){
	//echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert(language_data['common']['C'][language]);location.href='/admin/admin.php'</script>";
	//'관리자 로그인후 사용하실수 있습니다.'
	//exit;
}

if($search_searialize_value){
	$unserialize_search_value = unserialize(urldecode($search_searialize_value));
	extract($unserialize_search_value);
}

$db = new Database;
$db2 = new Database;
$db3 = new Database;

	ini_set('memory_limit','2048M');
	set_time_limit(9999999);

//search 조건	시작
include("../inventory/inventory_goods_query.php");
// search 조건	끝

/*선택한 회원이나 , 전체 검색한 회원은 같기에 위에서 한번만 선언한다. 2014-04-16 이학봉*/

if($admininfo[admin_level] != '9'){
	$where .= " and p.admin ='".$admininfo[company_id]."' ";
}

$info_type = 'company';

if($update_type == '2'){	//선택

	if(is_array($select_pid)){
	
	$excel_down_where .=" AND g.gid IN ('".implode("','",$select_pid)."')";

	$sql = "select data.* ,
		(select com_name as company_name from common_company_detail ccd where ccd.company_id = data.ci_ix   limit 1) as company_name 
		from (
			select 
				g.*, gu.unit ,gu.gu_ix

			from 
				inventory_goods g 
				left join inventory_goods_unit gu on (g.gid=gu.gid)
			$where
			$stock_where 
			$excel_down_where
			group by g.gid  
			$orderbyString 
		) data";

	$db->query($sql);
	$goods_info = $db->fetchall();

	}else{
		echo "<script language='javascript'>alert('다운받을 품목정보가 없습니다.');</script>";
	}

}else{	//검색한 품목

	$sql = "select
				g.*,
				date_format(g.regdate,'%Y-%m-%d') as g_regdate  , gu.unit ,gu.gu_ix
			from 
				inventory_goods g 
				left join inventory_goods_unit gu on (g.gid=gu.gid)
			$where
				$stock_where 
				group by g.gid  
			$orderbyString";
	$db->query($sql);
	$goods_info = $db->fetchall();

	//검색한 품목은 inventory_goods_query.php 에서 가져옴

}

//if($info_type == 'company'){		//운영자용

	$excel_file_name = "batch_goods_update_excel.xls";

	$objPHPExcel = PHPExcel_IOFactory::load($_SERVER["DOCUMENT_ROOT"]."/admin/inventory/".$excel_file_name);

	$etc_excel = new PHPExcel();

///////////////////
	// 속성 정의
	$etc_excel->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("etc code List")
								 ->setSubject("etc code List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("etc code List");

	$etc_excel->setActiveSheetIndex(0);
	$etc_excel->getActiveSheet()->setTitle('대량품목수정');

	$z = 5;
	for($i=0;$i<count($goods_info);$i++){

		$db->fetch($i);

		$col = 'A';

		$objPHPExcel->getActiveSheet()->setCellValue('A' . $z , "");
		//$objPHPExcel->getActiveSheet()->getStyle('B' . $z)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
		$objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $z , $goods_info[$i][gid], PHPExcel_Cell_DataType::TYPE_STRING);	//품목코드
		
		//$objPHPExcel->getActiveSheet()->getStyle('C' . $z)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );
		$objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $z , $goods_info[$i][cid], PHPExcel_Cell_DataType::TYPE_STRING);			//카테고리
		$objPHPExcel->getActiveSheet()->setCellValue('D' . $z , $goods_info[$i][item_account]);		//품목계정
		$objPHPExcel->getActiveSheet()->setCellValue('E' . $z , $goods_info[$i][is_use]);		//사용여부
		$objPHPExcel->getActiveSheet()->setCellValue('F' . $z , " ".$goods_info[$i][gcode]);		//대표코드
		$objPHPExcel->getActiveSheet()->setCellValue('G' . $z , $goods_info[$i][status]);	//판매상태
		$objPHPExcel->getActiveSheet()->setCellValue('H' . $z , $goods_info[$i][gname]);		//품목명
		$objPHPExcel->getActiveSheet()->setCellValue('I' . $z , $goods_info[$i][model]);		//모델명

		$objPHPExcel->getActiveSheet()->setCellValue('J' . $z , $goods_info[$i][basic_unit]);	//기본단위
		$objPHPExcel->getActiveSheet()->setCellValue('K' . $z , $goods_info[$i][order_basic_unit]);	//매입기본단위
		$objPHPExcel->getActiveSheet()->setCellValue('L' . $z , $goods_info[$i][surtax_div]);				//부가세적용


		$objPHPExcel->getActiveSheet()->setCellValue('M' . $z , $goods_info[$i][admin_type]);			//품목구분

		$objPHPExcel->getActiveSheet()->setCellValue('N' . $z , $goods_info[$i][ci_ix]);		//주매입처

		$objPHPExcel->getActiveSheet()->setCellValue('O' . $z , $goods_info[$i][og_ix]);	//원산지

		$objPHPExcel->getActiveSheet()->setCellValue('P' . $z , $goods_info[$i][c_ix]);		//제조사

		$objPHPExcel->getActiveSheet()->setCellValue('Q' . $z , $goods_info[$i][b_ix]);			//브랜드


		$objPHPExcel->getActiveSheet()->setCellValue('R' . $z , $goods_info[$i][available_priod]);		//유효기간

		$objPHPExcel->getActiveSheet()->setCellValue('S' . $z , $goods_info[$i][material]);			//소재/재질

		$objPHPExcel->getActiveSheet()->setCellValue('T' . $z , $goods_info[$i][glevel]);				//품목등급

		$objPHPExcel->getActiveSheet()->setCellValue('U' . $z , $goods_info[$i][kc_mark]);			//KC인증여부

		$objPHPExcel->getActiveSheet()->setCellValue('V' . $z , $goods_info[$i][hc_code]);				//HC코드

		$objPHPExcel->getActiveSheet()->setCellValue('W' . $z , $goods_info[$i][bs_goods_url]);				//품목구매URL

		$objPHPExcel->getActiveSheet()->setCellValue('X' . $z , $goods_info[$i][search_keyword]);					//검색키워드


		$objPHPExcel->getActiveSheet()->setCellValue('Y' . $z , $goods_info[$i][leadtime]);						//LEAD TIME
		$objPHPExcel->getActiveSheet()->setCellValue('Z' . $z , $goods_info[$i][available_amountperday]);		//일별생산량/구매가능량

		$objPHPExcel->getActiveSheet()->setCellValue('AA' . $z , $goods_info[$i][valuation]);				//재고평가

		$objPHPExcel->getActiveSheet()->setCellValue('AB' . $z , $goods_info[$i][lotno]);			//생산라인번호

		$sql = "select * from inventory_goods_unit where gid = '".$goods_info[$i][gid]."'";
		$db3->query($sql);
		$units = $db3->fetchall();
		if(count($units) > 0)
		for($j=0;$j<count($units);$j++){
			if($j != count($units) -1){
				$units_enter = "|";
				$width_length_enter = "^\n";
			}
			$unit_text .= $units[$j][unit].$units_enter;
			$change_amount .= $units[$j][change_amount].$units_enter;
			$buying_price .= $units[$j][buying_price].$units_enter;
			$wholesale_price .= $units[$j][wholesale_price].$units_enter;
			$sellprice  .= $units[$j][sellprice].$units_enter;
			$weight .= $units[$ij][weight].$units_enter;
			
			$width_length .= $units[$j][width_length]."|".$units[$j][depth_length]."|".$units[$j][height_length].$width_length_enter;

			$width_length_enter = '';
			$units_enter = '';
		}

		$objPHPExcel->getActiveSheet()->setCellValue('AC' . $z , $unit_text);					//단위정보
		$objPHPExcel->getActiveSheet()->setCellValue('AD' . $z , $change_amount);				//단위환산수량
		$objPHPExcel->getActiveSheet()->setCellValue('AE' . $z , $buying_price);				//기본매입가
		$objPHPExcel->getActiveSheet()->setCellValue('AF' . $z , $wholesale_price);				//기본도매가
		$objPHPExcel->getActiveSheet()->setCellValue('AG' . $z , $sellprice);					//기본소매가
		$objPHPExcel->getActiveSheet()->setCellValue('AH' . $z , $weight);						//무게
		$objPHPExcel->getActiveSheet()->setCellValue('AI' . $z , $width_length);				//부피

		$sql = "select g.standard,g.etc,gu.gid, gu.barcode from inventory_goods g , inventory_goods_unit gu where g.gid=gu.gid and g.gcode = '".$goods_info[$i][gcode]."' group by gid";
		$db3->query($sql);
		$display_options = $db3->fetchall();

		for($j=0;$j<count($display_options);$j++){

			if($j != count($display_options) -1){
				$display_enter = "|";
			}

			$options_standard .= $display_options[$j][standard].$display_enter;
			$options_etc .= $display_options[$j][etc].$display_enter;
			$options_gid .= $display_options[$j][gid].$display_enter;

			$sql = "select gu.unit,gu.barcode from inventory_goods_unit gu where gid = '".$display_options[$j][gid]."' order by gu_ix ASC";
			$db3->query($sql);
			$unit_infos = $db3->fetchall();

			for($k=0;$k<count($unit_infos);$k++){
				if($k != count($unit_infos) -1){
					$barcode_enter = "|";
				}

				if($k == count($unit_infos) -1){
					$barcode_enter = "^\n";
				}

				$options_barcode .= $unit_infos[$k][barcode].$barcode_enter;
			}
			
			$display_enter = '';
			$unit_infos = '';
		}

		$objPHPExcel->getActiveSheet()->setCellValue('AJ' . $z , $options_standard);		//규격(옵션)
		$objPHPExcel->getActiveSheet()->setCellValueExplicit('AK' . $z , $options_gid, PHPExcel_Cell_DataType::TYPE_STRING);			//카테고리
		//$objPHPExcel->getActiveSheet()->setCellValue('AK' . $z , $options_gid);				//규격품목코드
		$objPHPExcel->getActiveSheet()->setCellValue('AL' . $z , $options_barcode);			//바코드
		$objPHPExcel->getActiveSheet()->setCellValue('AM' . $z , $options_etc);				//비고
		$objPHPExcel->getActiveSheet()->setCellValue('AN' . $z , $goods_info[$i][admin]);				//재고관리업체
		$objPHPExcel->getActiveSheet()->setCellValue('AO' . $z , $goods_info[$i][allimg]);			//이미지
		$objPHPExcel->getActiveSheet()->setCellValue('AP' . $z , $goods_info[$i][bimg]);				//이미지URL

		$z++;
		
		$unit_text = '';
		$change_amount = '';
		$buying_price = '';
		$wholesale_price = '';
		$sellprice = '';
		$weight = '';
		$width_length = '';

		$options_standard = '';
		$options_gid = '';
		$options_barcode = '';
		$options_etc = '';

	}

/////////////////////

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename=batch_goods_update_excel.xls');
	header('Cache-Control: max-age=0');
	
	//$objPHPExcel = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	//$objPHPExcel = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
	//$objPHPExcel->setUseBOM(true);
	//$objPHPExcel->save('php://output');

	$objPHPExcel = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objPHPExcel->save('php://output');

?>