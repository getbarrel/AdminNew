<?php
	include_once("../../class/database.class");
	include_once($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");

	if($act == 'tmp_file_upload'){
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."/admin/product/tmp_file/".$_SESSION['admininfo']['charger_ix']."_".$_FILES[up_file][name])){
			echo 1;
			exit;
		}
		$result = copy($_FILES[up_file][tmp_name], $_SERVER["DOCUMENT_ROOT"]."/admin/product/tmp_file/".$_SESSION['admininfo']['charger_ix']."_".$_FILES[up_file][name]."");
		if($result){
			echo 0;
			exit;
		}else{
			echo "fail";
			exit;
		}
		
	}

	if($act == 'tmp_file_delete'){
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."/admin/product/tmp_file/".$_SESSION['admininfo']['charger_ix']."_".$delete_file)){
			unlink($_SERVER["DOCUMENT_ROOT"]."/admin/product/tmp_file/".$_SESSION['admininfo']['charger_ix']."_".$delete_file);
			echo 0;
			exit;
		}else{
			echo 1;
			exit;
		}
	}
	if($act == 'file_delete'){
		
		$cd_ix = str_replace('file_data_','',$cd_data);
		
		$sql = "delete from contents_data where cd_ix = '".$cd_ix."' ";
		$db->query($sql);

		$d_path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/contents/".$ci_ix."/".$delete_file."";

		if(file_exists($d_path)){
			unlink($d_path);
			echo 0;
			exit;
		}else{
			echo 1;
			exit;
		}
	}

	if($act == "tmp_file_delete_all"){
		$dir = $_SERVER["DOCUMENT_ROOT"]."/admin/product/tmp_file/";
	 
		// 핸들 획득
		$handle  = opendir($dir);
		 
		$files = array();
		 
		// 디렉터리에 포함된 파일을 저장한다.
		while (false !== ($filename = readdir($handle))) {
			if($filename == "." || $filename == ".."){
				continue;
			}
		 
			// 파일인 경우만 목록에 추가한다.
			if(is_file($dir . "/" . $filename)){
				$files[] = $filename;
			}
		}
		 
		// 핸들 해제 
		closedir($handle);
		 
		// 정렬, 역순으로 정렬하려면 rsort 사용
		sort($files);
		 
		// 파일명을 출력한다.
		foreach ($files as $f) {
			$admin_key = explode('_',$f);
			if($admin_key[0] == $_SESSION['admininfo']['charger_ix']){
				unlink($_SERVER["DOCUMENT_ROOT"]."/admin/product/tmp_file/".$f);
			}
		} 
	}

	if($act == 'insert'){
		$db = new database();

		$transction  = $db->query("SET AUTOCOMMIT=0");
		$transction  = $db->query("BEGIN");
		$transction_ok = true;

		$title_img = $_FILES[title_img];//등록 메인이미지
		
		if(!empty($title_img[name])){
			$d_path = $_SERVER["DOCUMENT_ROOT"].$_SESSION[admin_config][mall_data_root]."/images/contents/";

			if(!is_dir($d_path)){
				mkdir($d_path, 0777);
				chmod($d_path,0777);
			}
		}

		$displayTopYN = $_POST['displayTopYN'];
		if($displayTopYN == ''){
		   $displayTopYN = 'N';
		}

		$targetURL = $_POST['targetUrl'];
		if($targetURL == ''){
		   $targetURL = '';
		}

		$sql = "insert contents_info set
						c_name = '".$c_name."',
						state= '".$state."',
						c_type = '".$c_type."',
						company_id = '".$company_id."',
						c_file_type = '".serialize($c_file_type)."',
						age = '".$age."',
						end_day = '".$end_day."',
						composer = '".$composer."',
						c_coprice = '".$c_coprice."',
						c_sellprice = '".$c_sellprice."',
						up_file_type = '".$up_file_type."',
						title_img = '".$title_img[name]."',
						displaytopyn = '" . $displayTopYN . "' ,
						targeturl = '" . $targetURL . "' ,
						regdate = NOW()
				";
		$transction = $db->query($sql);
		if(!$transction || mysql_affected_rows() == 0) {
			$transction_ok = false;
		}else{
			$ci_ix = mysql_insert_id();
		}

		if(!empty($title_img[name])){
			$path = $d_path.$ci_ix."/";

			if(!is_dir($path)){
				mkdir($path, 0777);
				chmod($path,0777);
			}

			$newfile = $path.$title_img[name];

			if(!copy($title_img[tmp_name], $newfile)) { 
				$transction_ok = false;
			} 
		}
		
		if(is_array($data_info)){
			for($i=0; $i < count($data_info); $i++){
				$sql = "insert contents_data set
						ci_ix = '".$ci_ix."',
						up_file_type= '".$up_file_type."',
						data_info = '".$data_info[$i]."',
						regdate = NOW()
				";
				$transction = $db->query($sql);
				if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;

				$file_path = $_SERVER["DOCUMENT_ROOT"]."/admin/product/tmp_file/".$_SESSION['admininfo']['charger_ix']."_".$data_info[$i];

				$d_path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/contents/";

				if(!is_dir($d_path)){
					mkdir($d_path, 0777);
					chmod($d_path,0777);
				}

				$path = $d_path.$ci_ix."/";

				if(!is_dir($path)){
					mkdir($path, 0777);
					chmod($path,0777);
				}

				$newfile = $path.$data_info[$i];

				if(file_exists($file_path)) { 
					if(!copy($file_path, $newfile)) { 
						$transction_ok = false;
					} else if(file_exists($newfile)) { 
					// 복사에 성공하면 원본 파일을 삭제합니다. 
						if(!@unlink($file_path)){ 
							if(@unlink($newfile)){ 
								$transction_ok = false;
							} 
						} 
					} 
				} 
			}
		}else{
			$sql = "insert contents_data set
						ci_ix = '".$ci_ix."',
						up_file_type= '".$up_file_type."',
						data_info = '".$data_info."',
						regdate = NOW()
			";
			$transction = $db->query($sql);
			if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;
		}


		if(!$transction_ok){
			$transction = $db->query("ROLLBACK");
			echo("<script>alert('등록실패');parent.document.location.reload();</script>");
			exit;
		}else{
			$transction = $db->query("COMMIT");
			echo("<script>alert('등록 성공.');parent.document.location.reload();</script>");
			exit;
		}
		

		
	}

	if($act == "update"){
		$db = new database();
		
		$sql = "select * from contents_info where ci_ix  = '".$ci_ix."'";
		$db->query($sql);
		$bdata = $db->fetchall();

		$transaction  = $db->query("SET AUTOCOMMIT=0");
		$transaction  = $db->query("BEGIN");
		$transaction_ok = true;
		
		
		$title_img = $_FILES[title_img];//등록 메인이미지
		
		$img_update = "title_img = '".$title_img[name]."', ";

		$displayTopYN = $_POST['displayTopYN'];
		if($displayTopYN == ''){
		   $displayTopYN = 'N';
		}

		$targetURL = $_POST['targetUrl'];
		if($targetURL == ''){
		   $targetURL = '';
		}

		$dataInfo = $_POST['data_info'];
		if($targetURL == ''){
		   $dataInfo = '';
		}

		$sql = "update contents_info set
						c_name = '".$c_name."',
						state= '".$state."',
						c_type = '".$c_type."',
						company_id = '".$company_id."',
						c_file_type = '".serialize($c_file_type)."',
						age = '".$age."',
						end_day = '".$end_day."',
						composer = '".$composer."',
						c_coprice = '".$c_coprice."',
						c_sellprice = '".$c_sellprice."',
						up_file_type = '".$up_file_type."',
						$img_update
						displaytopyn = '" . $displayTopYN . "',
						targeturl = '" . $targetURL . "',
						editdate = NOW()
					where 
						ci_ix = '".$ci_ix."'
				";
		$transaction = $db->query($sql);
		if(!$transaction || mysql_affected_rows() == 0) $transaction_ok = false;

		if(!empty($title_img[name])){
			$y_path = $_SERVER["DOCUMENT_ROOT"].$_SESSION[admin_config][mall_data_root]."/images/contents/";

			$path = $y_path.$ci_ix."/";

			if(!is_dir($path)){
				mkdir($path, 0777);
				chmod($path,0777);
			}

			$newfile = $path.$title_img[name];
			$newfile = str_replace("//", "/", $newfile);
			if(!move_uploaded_file($_FILES["title_img"]["tmp_name"], $newfile)) { 
				$transaction_ok = false;
			} 
		}

		if($b_up_file_type != $up_file_type){
			
			$sql = "select * from contents_data where ci_ix = '".$ci_ix."' and up_file_type = '".$b_up_file_type."'";
			$db->query($sql);
			$file_data = $db->fetchall();
			for($i=0; $i < count($file_data); $i++ ){
				$sql = "delete from contents_data where cd_ix = '".$file_data[$i][cd_ix]."' ";
				$db->query($sql);
				if($b_up_file_type == "F"){
					$d_path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/contents/".$ci_ix."/".$file_data[$i][data_info]."";

					if(file_exists($d_path)){
						unlink($d_path);
					}
				}
			}
			
		}
		if(is_array($data_info)){
			for($i=0; $i < count($data_info); $i++){
				$sql = "insert contents_data set
						ci_ix = '".$ci_ix."',
						up_file_type= '".$up_file_type."',
						data_info = '".$data_info[$i]."',
						regdate = NOW()
				";
				$transaction = $db->query($sql);
				if(!$transaction || mysql_affected_rows() == 0) $transaction_ok = false;

				$file_path = $_SERVER["DOCUMENT_ROOT"]."/admin/product/tmp_file/".$_SESSION['admininfo']['charger_ix']."_".$data_info[$i];

				$d_path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/contents/";

				if(!is_dir($d_path)){
					mkdir($d_path, 0777);
					chmod($d_path,0777);
				}

				$path = $d_path.$ci_ix."/";

				if(!is_dir($path)){
					mkdir($path, 0777);
					chmod($path,0777);
				}

				$newfile = $path.$data_info[$i];

				if(file_exists($file_path)) { 
					if(!copy($file_path, $newfile)) { 
						$transaction_ok = false;
					} else if(file_exists($newfile)) { 
					// 복사에 성공하면 원본 파일을 삭제합니다. 
						if(!@unlink($file_path)){ 
							if(@unlink($newfile)){ 
								$transaction_ok = false;
							} 
						} 
					} 
				} 
			}
		}else{
			if($b_up_file_type != $up_file_type){
				$sql = "insert contents_data 
							set
								ci_ix = '".$ci_ix."',
								up_file_type= '".$up_file_type."',
								data_info = '".$data_info."',
								regdate = NOW()";
				$transaction = $db->query($sql);
				if(!$transaction || mysql_affected_rows() == 0) $transaction_ok = false;

			}else{
				if($up_file_type != 'F'){
					$sql = "update contents_data 
								set
									up_file_type= '".$up_file_type."',
									data_info = '".$data_info."',
									editdate = NOW()
								where cd_ix = '".$cd_ix."'";
					//where cd_ix = '".$cd_ix."' -> where ci_ix = '".$ci_ix."' 확인필요
				}
			}
			$transaction = $db->query($sql);
			if(!$transaction || mysql_affected_rows() == 0) $transaction_ok = false;
		}


		


		if(!$transaction_ok){
			$transaction = $db->query("ROLLBACK");
			echo("<script>alert('수정실패');parent.document.location.reload();</script>");
			exit;
		}else{
			$transaction = $db->query("COMMIT");

			$sql = "select * from contents_info where ci_ix  = '".$ci_ix."'";
			$db->query($sql);
			$adata = $db->fetchall();
			contents_edit_history($bdata,$adata,$ci_ix);

			echo("<script>alert('수정 성공.');parent.document.location.reload();</script>");
			exit;
		}
	}


	if($act == "delete"){
		$db = new database;

		$transction  = $db->query("SET AUTOCOMMIT=0");
		$transction  = $db->query("BEGIN");
		$transction_ok = true;


		$sql = "select * from contents_data where ci_ix = '".$ci_ix."' ";

		$db->query($sql);
		$file_data = $db->fetchall();
		for($i=0; $i < count($file_data); $i++ ){
			$sql = "delete from contents_data where cd_ix = '".$file_data[$i][cd_ix]."' ";
			
			$transction = $db->query($sql);
			if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;

			if($file_data[$i][up_file_type] == "F"){
				$d_path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/contents/".$ci_ix."/".$file_data[$i][data_info]."";

				if(file_exists($d_path)){
					unlink($d_path);
				}
			}
		}

		$sql = "delete from contents_info where ci_ix = '".$ci_ix."'";
		$transction = $db->query($sql);

		if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;

		if(!$transction_ok){
			$transction = $db->query("ROLLBACK");
			echo 'fail';
			exit;
		}else{
			$transction = $db->query("COMMIT");
			echo 0;
			exit;
		}

	}

	function contents_edit_history($bdata,$adata,$ci_ix){

		$compare_value[0] = array("input_name"=>"c_name", "column_name"=>"c_name", "name_text"=>"컨텐츠명");
		$compare_value[1] = array("input_name"=>"state", "column_name"=>"state", "name_text"=>"사용여부");
		$compare_value[2] = array("input_name"=>"c_type", "column_name"=>"c_type", "name_text"=>"컨텐츠타입");
		$compare_value[3] = array("input_name"=>"company_id", "column_name"=>"company_id", "name_text"=>"셀러구분");
		$compare_value[4] = array("input_name"=>"c_file_type", "column_name"=>"c_file_type", "name_text"=>"파일타입");
		$compare_value[5] = array("input_name"=>"age", "column_name"=>"age", "name_text"=>"연령대");
		$compare_value[6] = array("input_name"=>"end_day", "column_name"=>"end_day", "name_text"=>"이용기간");
		$compare_value[7] = array("input_name"=>"c_coprice", "column_name"=>"c_coprice", "name_text"=>"공급가");
		$compare_value[8] = array("input_name"=>"c_sellprice", "column_name"=>"c_sellprice", "name_text"=>"판매가");
		$compare_value[9] = array("input_name"=>"up_file_type", "column_name"=>"up_file_type", "name_text"=>"업로드 타입");

		foreach($compare_value as $val){
			if($bdata[0][$val[column_name]] != $adata[0][$val[column_name]]){
				contents_edit_history_insert($ci_ix,$val[column_name],$val[name_text],$bdata[0][$val[column_name]],$adata[0][$val[input_name]],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger]);
			}
		}
	}

	function contents_edit_history_insert($ci_ix,$column_name,$column_text,$b_data,$after_data,$charger_ix,$charger_name){
		
		if(!$ci_ix){
			return false;
		}

		$db2 = new database;

		$sql = "insert contents_history set
					ci_ix = '".$ci_ix."',
					b_data = '".$b_data."',
					after_data = '".$after_data."',
					column_name = '".$column_name."',
					column_text = '".$column_text."',
					charger_ix = '".$charger_ix."',
					charger_name = '".$charger_name."',
					regdate = NOW()";

		$transction = $db2->query($sql);
		$transction = $db2->query("COMMIT");
	}
