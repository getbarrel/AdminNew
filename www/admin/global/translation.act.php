<?
include("../../class/database.class");
include("../class/layout.class");
include("../order/excel_out_columsinfo.php");
include("../include/phpexcel/Classes/PHPExcel.php");

ini_set('memory_limit', '-1');

$db = new MySQL;

if($act == "chinese_trans_text_reg"){
	//echo $trans_text;
	$sql="select text_korea, trans_text, trans_type from global_translation_abcmarket where trans_type != 'korea'  ";//limit 100
	$db->query($sql);
	$trans_datas = $db->fetchall();

	for($i =0 ; $i < count($trans_datas);$i++){
		$text_korea = $trans_datas[$i][text_korea];
		$trans_type = $trans_datas[$i][trans_type];
		$trans_text = $trans_datas[$i][trans_text]; 

		if(trim($trans_text) || true){ 
				$sql="select trans_ix, trans_key from global_translation where trans_type='korean' and text_korea='".str_replace("'","\\'",$text_korea)."' ";
				$db->query($sql);
				if($db->total){
					$db->fetch();
					$trans_ix = $db->dt[trans_ix];
					$trans_key = $db->dt[trans_key];

					$sql="select trans_ix, trans_key from global_translation_detail where trans_ix='".$trans_ix."' and trans_type='".$trans_type."' ";
					$db->query($sql);
					if(!$db->total){
						$sql = " insert into global_translation_detail
							(trans_ix,trans_key, trans_type, trans_text,regdate) 
							values
							('$trans_ix','$trans_key','$trans_type','$trans_text',NOW())";
						//echo nl2br($sql)."<br>";
						$db->query($sql);
					}
				}

				
				$result_massage = $sql;
		}
	}
	echo $result_massage;
	
	exit;
}



if($act == "trans_text_reg"){

	if(trim($trans_text) || true){

			$sql="select trans_ix from global_translation_detail where trans_type='".$trans_type."' and trans_ix='".$trans_ix."' ";
			$db->query($sql);
			if($db->total){
				$db->fetch();
				$trans_ix = $db->dt[trans_ix];
				$sql = " update global_translation_detail set 
							trans_text = '$trans_text'
						where 
							trans_ix='".$trans_ix."' and trans_type = '".$trans_type."' ";
			//	echo "<span class=''>수정완료</span>";

				$db->query($sql);
			}else{
				$sql = " insert into global_translation_detail
					(trans_ix,trans_key, trans_type, trans_text,regdate) 
					values
					('$trans_ix','$trans_key','$trans_type','$trans_text',NOW())";

				$db->query($sql);
				//exit;

				//echo "<span class='blue'>등록완료</span>";
			}
			//echo nl2br($sql);

			//$db->query($sql);
			$result_massage = $sql;
	}
	echo $result_massage;
	
	exit;
}


if ($act == "language_sync"){


	$db->query("SELECT * FROM global_language where disp = 1 and language_code != '".$trans_type."' ");
	$languages = $db->fetchall("object");

	$sql = "select * from global_translation where trans_type = '$trans_type'"; //text_korea = '$text_korea' and 
	$db->query($sql);
	$trans_datas = $db->fetchall("object");

	for($j=0;$j <count($trans_datas);$j++){
		for($i=0;$i < count($languages);$i++){
			$sql = "select * from global_translation where text_korea = '".$trans_datas[$j][text_korea]."' and trans_type = '".$languages[$i][language_code]."'"; 
			$db->query($sql);

			if(!$db->total){
				$sql = "insert into global_translation(trans_ix,trans_key,trans_div,trans_type,file_path, file_name, text_korea,trans_text,disp,regdate) 
					values
					('','$trans_key','$trans_div','".$languages[$i][language_code]."','$file_path','$file_name','$text_korea','$text_korea','$disp',NOW())";
				$db->sequences = "global_translation_SEQ";
				$db->query($sql);
			}
		}
	}

	echo("<script trans='javascript' src='../js/message.js.php'></script><script>show_alert2('번역데이타 동기화가 정상적으로 수정되었습니다.');</script>");
	exit;
}

if ($act == "insert")
{

	if($etc == ""){
		$bank_name = $bank_name;
	}else{
		$bank_name = $etc;
	}


	if($db->dbms_type == "oracle"){
		$sql = "select * from global_translation where text_korea like '$text_korea' and trans_type = '$trans_type'"; //trans_div = '$trans_div' and 
	}else{
		$sql = "select * from global_translation where text_korea = '$text_korea' and trans_type = '$trans_type'"; //trans_div = '$trans_div' and 
	}
	//	echo $sql;
	$db->query($sql);

	
	if(!$db->total){
		$trans_key = md5($text_korea);
		$sql = "insert into global_translation(trans_ix,trans_key,trans_div,trans_type,file_path, file_name, text_name, text_korea,trans_text,disp,regdate) 
				values
				('','$trans_key','$trans_div','$trans_type','$file_path','$file_name','$text_korea','$text_korea','$trans_text','$disp',NOW())";
		$db->sequences = "global_translation_SEQ";
		//echo nl2br($sql);
		$db->query($sql);

		$db->query("SELECT trans_ix FROM global_translation WHERE trans_ix=LAST_INSERT_ID()");
		$db->fetch();
		$trans_ix = $db->dt[trans_ix];

		$db->query("SELECT * FROM global_language where disp = 1 ");
		$languages = $db->fetchall("object");
		for($i=0;$i < count($languages);$i++){
			$sql="select trans_ix from global_translation_detail where trans_type='".$languages[$i][language_code]."' and trans_ix='".$trans_ix."' ";
			$db->query($sql);
			if($db->total){
				$db->fetch();
				$trans_ix = $db->dt[trans_ix];
				$sql = " update global_translation_detail set 
								trans_text = '$trans_text'
							where 
								trans_ix='".$trans_ix."' and trans_type = '".$languages[$i][language_code]."' ";
			//	echo "<span class=''>수정완료</span>";
			}else{
				$sql = " insert into global_translation_detail
					(trans_ix,trans_key, trans_type, trans_text,regdate) 
					values
					('".$trans_ix."','$trans_key','".$languages[$i][language_code]."','$trans_text',NOW())";

				//echo "<span class='blue'>등록완료</span>";
			}

			$db->query($sql);
			/*
			$sql = "select * from global_translation where text_korea = '$text_korea' and trans_type = '".$languages[$i][language_code]."'"; 
			$db->query($sql);

			if(!$db->total){
				$sql = "insert into global_translation(trans_ix,trans_key,trans_div,trans_type,file_path, file_name, text_korea,trans_text,disp,regdate) 
					values
					('','$trans_key','$trans_div','".$languages[$i][language_code]."','$file_path','$file_name','$text_korea','$text_korea','$disp',NOW())";
				$db->sequences = "global_translation_SEQ";
				$db->query($sql);
			}
			*/
		}
		echo("<script trans='javascript' src='../js/message.js.php'></script><script>show_alert2('번역항목이 정상적으로 등록되었습니다.');</script>");
		echo("<script>opener.location.href='translation.php?trans_div=$trans_div&trans_type=$trans_type';</script>");
		echo("<script>self.close();</script>");
	
	}else{
		/*
		$db->query("SELECT * FROM global_language where disp = 1 ");
		$languages = $db->fetchall("object");
		for($i=0;$i < count($languages);$i++){
			$sql = "select * from global_translation where text_korea = '$text_korea' and trans_type = '".$languages[$i][language_code]."'"; 
			$db->query($sql);

			if(!$db->total){
				$sql = "insert into global_translation(trans_ix,trans_key,trans_div,trans_type,file_path, file_name, text_name, text_korea,trans_text,disp,regdate) 
					values
					('','$trans_key','$trans_div','".$languages[$i][language_code]."','$file_path','$file_name','$text_korea','$text_korea','$text_korea','$disp',NOW())";
				$db->sequences = "global_translation_SEQ";
				$db->query($sql);
			}
		}
		*/
		echo("<script>alert('이미 등록된 번역항목 목록입니다.');</script>");
		echo("<script>opener.document.location.href='translation.php?trans_div=$trans_div&trans_type=$trans_type';</script>");
		echo("<script>self.close();</script>");

	}
	exit;
}


if ($act == "update"){

	$sql = "update global_translation set 
				trans_div='$trans_div',
				trans_type='".$trans_type."',
				file_path='$file_path',
				file_name='$file_name',
				text_name='$text_korea',
				text_korea='$text_korea',
				trans_text='$trans_text',
				disp='$disp' 
				where trans_ix='".$trans_ix."'  ";

	$db->query($sql);

	$sql="select trans_ix, trans_key from global_translation_detail where trans_type='".$trans_type."' and trans_ix='".$trans_ix."' ";

	$sql="select trans_ix from global_translation_detail where trans_type='".$trans_type."' and trans_ix='".$trans_ix."' ";
	$db->query($sql);
	if($db->total){
		$db->fetch();
		$trans_ix = $db->dt[trans_ix];
		$sql = " update global_translation_detail set 
					trans_text = '$trans_text'
				where 
					trans_ix='".$trans_ix."' and trans_type = '".$trans_type."' ";
	//	echo "<span class=''>수정완료</span>";
	}else{
		$trans_key = $db->dt[trans_key];
		$trans_ix = $db->dt[trans_ix];
		$sql = " insert into global_translation_detail
			(trans_ix,trans_key, trans_type, trans_text,regdate) 
			values
			('$trans_ix','$trans_key','$trans_type','$trans_text',NOW())";

		//echo "<span class='blue'>등록완료</span>";
	}

	echo("<script trans='javascript' src='../js/message.js.php'></script><script>show_alert2('번역항목이 정상적으로 수정되었습니다.');</script>");
	echo("<script>opener.location.href = 'translation.php?trans_div=$trans_div&trans_type=$trans_type';</script>");
	echo("<script>self.close();</script>");
	exit;
}

if ($act == "delete"){

	$sql = "delete from global_translation where trans_ix='$trans_ix'";
	$db->query($sql);

	$sql = "delete from global_translation_detail where trans_ix='$trans_ix'";
	$db->query($sql);


	echo("<script trans='javascript' src='../js/message.js.php'></script><script>show_alert2('번역항목이 정상적으로 삭제되었습니다.');</script>");
	echo("<script>document.location.href='translation.php?trans_div=$trans_div&trans_type=$trans_type';</script>");
	exit;
}


if ($act == "excel"){

	$sql = "SELECT 
					t.trans_ix, t.trans_key, trans_div, td.trans_type, file_path, file_name, text_name, 
					text_korea, is_check, disp, td.regdate, td.trans_text, td_j.trans_text as japanese, td_c.trans_text as chinese, td_e.trans_text as english
				FROM 
					global_translation t left join global_translation_detail td on t.trans_ix = td.trans_ix
								left join global_translation_detail td_j on t.trans_ix = td_j.trans_ix and td_j.trans_type='Japanese'
								left join global_translation_detail td_c on t.trans_ix = td_c.trans_ix and td_c.trans_type='chinese'
								left join global_translation_detail td_e on t.trans_ix = td_e.trans_ix and td_e.trans_type='english'
				where 
					t.trans_ix is not null and td.trans_type in ('chinese','english','Japanese','korean')
				group by t.trans_ix
				order by regdate desc";
	//echo nl2br($sql);
	$db->query($sql);

	$trans_datas = $db->fetchall();

	//print_r($trans_datas);
	//exit;


	$ordersXL = new PHPExcel();

	// 속성 정의

	$ordersXL->getProperties()->setCreator("포비즈 코리아")
							 ->setLastModifiedBy("Mallstory.com")
							 ->setTitle("trans datas List")
							 ->setSubject("trans datas")
							 ->setDescription("generated by forbiz korea")
							 ->setKeywords("mallstory")
							 ->setCategory("trans datas");


	if($trans_datas){

		$j=0;

		$check_colums='';
		$columsinfo='';


		$check_colums[file_path] = 'file_path';
		$columsinfo[file_path] = array(value=>'file_path', title=>'경로', checked=>'checked');
		$check_colums[trans_ix] = 'trans_ix';
		$columsinfo[trans_ix] = array(value=>'trans_ix', title=>'trans_ix', checked=>'checked');
		$check_colums[trans_key] = 'trans_key';
		$columsinfo[trans_key] = array(value=>'trans_key', title=>'trans_key', checked=>'checked');
		$check_colums[text_name] = 'text_name';
		$columsinfo[text_name] = array(value=>'text_name', title=>'한글 문구', checked=>'checked');
		$check_colums[Japanese] = 'Japanese';
		$columsinfo[Japanese] = array(value=>'Japanese', title=>'일본어', checked=>'checked');
		$check_colums[chinese] = 'chinese';
		$columsinfo[chinese] = array(value=>'chinese', title=>'중국어', checked=>'checked');
		$check_colums[english] = 'english';
		$columsinfo[english] = array(value=>'english', title=>'영어', checked=>'checked');
		$check_colums[text_korea] = 'text_korea';
		$columsinfo[text_korea] = array(value=>'text_korea', title=>'한국어', checked=>'checked');


		// 헤더찍기
		$col = 'A';

		$ordersXL->getActiveSheet(0)->mergeCells('E1:H1')->setCellValue('E1', "번역 문구");

		foreach($check_colums as $key => $value){

			if($col == 'A' || $col == 'B' || $col == 'C' || $col == 'D'){
				$ordersXL->getActiveSheet(0)->mergeCells($col . "1:" . $col . "2")->setCellValue($col . "1", $columsinfo[$value][title]);
			}else{
				$ordersXL->getActiveSheet(0)->setCellValue($col . "2", $columsinfo[$value][title]);
			}
			$col++;

		}

		for ($i=0,$z=0; $i < count($trans_datas); $i++)
		{

			$j="A";

			foreach($check_colums as $key => $value){
				//echo $value;
				if($value == "file_path"){
					$value_str = $trans_datas[$i][trans_div].'-'.$trans_datas[$i][$value].'-'.$trans_datas[$i][file_name];
				}else{
					$value_str = $trans_datas[$i][$value];
				}

				if(empty($value_str)){
					$value_str=' ';
				}

				$ordersXL->getActiveSheet(0)->setCellValue($j . ($z + 3), $value_str);
				$j++;
			}

			$z++;

		}
	}
//exit;

	$ordersXL->getActiveSheet(0)->getColumnDimension('A')->setWidth('40');
	$ordersXL->getActiveSheet(0)->getColumnDimension('B')->setWidth('15');
	$ordersXL->getActiveSheet(0)->getColumnDimension('C')->setAutoSize(true);
	$ordersXL->getActiveSheet(0)->getColumnDimension('D')->setWidth('30');
	$ordersXL->getActiveSheet(0)->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$ordersXL->getActiveSheet(0)->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$ordersXL->getActiveSheet(0)->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$ordersXL->getActiveSheet(0)->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$ordersXL->getActiveSheet(0)->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$ordersXL->getActiveSheet(0)->setTitle('trans_datas');
	$ordersXL->setActiveSheetIndex(0);
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.iconv("UTF-8","CP949","번역문구리스트").'_'.date("Ymd").'.xls"');
	header('Cache-Control: max-age=0');
	$objWriter = PHPExcel_IOFactory::createWriter($ordersXL, 'Excel5');
	$objWriter->save('php://output');


}


?>
