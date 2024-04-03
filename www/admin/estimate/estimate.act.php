<?
//include("../../class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");
session_start();

$db = new Database;
if ($act == "insert"){

	$estimate_title = trim($estimate_title);		//견적서 타이틀
	$estimate_code = trim($estimate_code);			//견적서 코드
	if($open_date){
		$open_date	= trim($open_date)." ".date("H:i:s");					//견적예정일
	}else{
		$open_date	=  date("Y-m-d H:i:s");					//견적예정일
	}
	$estimate_type = trim($estimate_type);			//견적서타입
	$estimate_div = trim($estimate_div);			//견적서유형
	$ucode = trim($ucode);	//회원코드
	$bname = trim($bname);		//의뢰인명
	$buser_id = trim($buser_id);	//의뢰인 아이디 
	$bmem_group = trim($bmem_group);	//의뢰인 그룹
	$bmail = trim($bmail);			//의뢰인 메일
	$btel = $btel_1."-".$btel_2."-".$btel_3;	//의뢰인 전화번호
	$bmobile = $bmobile_1."-".$bmobile_2."-".$bmobile_3;	//의뢰인 핸드폰번호
	//$bzip = $zipcode1."-".$zipcode2;		//의뢰인 우편번호
	if($zipcode2 != "" || $zipcode2 != NULL){
		$bzip = "$zipcode1-$zipcode2";
	}else{
		$bzip = $zipcode1;
	}

	$baddr1 = trim($addr1);				//의뢰인 주소1
	$baddr2	= trim($addr2);				//의뢰인 주소2
	if($plan_date){
		$plan_date = trim($plan_date)." ".date("H:i:s");			//견적서완료일
	}else{
		$plan_date = date("Y-m-d H:i:s");			//견적서완료일
	}
	$company = trim($company);	//사업장명
	$com_number = $com_number_1."-".$com_number_2."-".$com_number_3;	//사업자 번호
	$com_ceo = trim($com_ceo);	//대표자명
	$com_div = trim($com_div);	//사업자유형
	$com_business_status = trim($com_business_status);	//업태
	$com_business_category = trim($com_business_category);		//업종
	$com_phone = $com_phone_1."-".$com_phone_2."-".$com_phone_3;	//대표자번호
	$com_fax = $com_fax_1."-".$com_fax_2."-".$com_fax_3;	//대표자팩스
	$com_zip = trim($com_zip);		//대표자우편번호
	$com_addr1 = trim($com_addr1);		//사업자주소1
	$com_addr2 = trim($com_addr2);		//사업자주소1
	$etc = trim($etc);			//견적서요청 사항
	$admin_etc = trim($admin_etc);			//관리자상담내용 사항
	

	if($estimate_type == 1){	//가이드 견적서일경우 정보가 부족하여 채워줘야함
		
		$estimate_code = date("Ymd")."-".date("His");
		$status = '4';
	}
	$sql = "insert into shop_estimates set
				estimate_title = '".$estimate_title."',
				estimate_code = '".$estimate_code."',
				estimate_type = '".$estimate_type."',
				estimate_div = '".$estimate_div."',
				ucode = '".$ucode."',
				bname = '".$bname."',
				buser_id = '".$buser_id."',
				bmem_group = '".$bmem_group."',
				btel = '".$btel."',
				bmobile = '".$bmobile."',
				bmail = '".$bmail."',
				bzip = '".$bzip."',
				baddr1 = '".$baddr1."',
				baddr2 = '".$baddr2."',
				bfile = '".$bfile."',
				company = '".$company."',
				com_ceo = '".$com_ceo."',
				com_phone = '".$com_phone."',
				com_fax = '".$com_fax."', 
				com_number = '".$com_number."',
				com_div = '".$com_div."',
				com_business_status = '".$com_business_status."',
				com_business_category = '".$com_business_category."',
				com_zip = '".$com_zip."',
				com_addr1 = '".$com_addr1."',
				com_addr2 = '".$com_addr2."',
				open_date = '".$open_date."',
				plan_date = '".$plan_date."',
				delivery_postion = '".$delivery_postion."',
				receive_method = '".$receive_method."',
				status = '".$status."',
				etc = '".$etc."',
				admin_etc = '".$admin_etc."',
				mall_ix = '".$mall_ix."',
				regdate = NOW()
	";


	$db->query($sql);
	$est_ix = $db->insert_id();
	
	
	if($disp == ""){
		$disp = '1';	//셀프 견적일경우 빈값이 여서 지정해줌 
	}
	if($estimate_type == "1"){
		$sql = "insert into shop_estimates_guide set
					est_ix = '".$est_ix."',
					gu_status = '".$mem_type."',
					md_code = '".$md_code."',
					displan_date = '".$open_date."',
					disp = '".$disp."',
					regdate = NOW()
		";

		$db->query($sql);
		$guide_ix = $db->insert_id();
		
		///////// edit 내용 처리 시작 2013-07-23 이학봉//////////////
		$data_text = $event_text;
		$data_text_convert = $event_text;
		$data_text_convert = str_replace("\\","",$data_text_convert);
		preg_match_all("|<IMG .*src=\"(.*)\".*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);


		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/estimate/";
		if(!is_dir($path)){
			mkdir($path, 0777);
		}

		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/estimate/$guide_ix/";


		// 이미지 저장 시작 
		if ($estimates_file_size > 0){
			//copy($estimates_file,$path."/"."estimates__".$guide_ix.".gif");
			move_uploaded_file($estimates_file, $path."/".$guide_ix."_".$estimates_file_name);
			//$company_stamp_str = $admin_config[mall_data_root]."/images/stamps/stamp_".$company_id.".gif";
		}

		if($estimates_file_name){
			$sql = "update shop_estimates set bfile = '".$guide_ix."_".$estimates_file_name."'";
			
			$db->query($sql);
			echo nl2br($sql);
			exit;
		}
		// 이미지 저장 끝


		if(!is_dir($path)){
			mkdir($path, 0777);
			//chmod($path,0777)
		}

		if(is_dir($path)){
			if($s_img_size > 0){
				move_uploaded_file($s_img, $path."/estimate_guide_".$guide_ix.".gif");
			}

		}
		if(is_dir($path)){
			if($b_img_size > 0){
				move_uploaded_file($b_img, $path."/b_estimate_guide__".$guide_ix.".gif");
			}

		}

		for($i=0;$i < count($out);$i++){
			for($j=0;$j < count($out[$i]);$j++){

				$img = returnImagePath($out[$i][$j]);
				$img = ClearText($img);


				if(substr_count($img,$admin_config[mall_data_root]."/images/estimate/$guide_ix/") == 0){// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
					if(substr_count($img,"$HTTP_HOST")>0){
						$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"]."",$img);

						@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/estimate/$guide_ix/".returnFileName($img));
						if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
							@unlink($local_img_path);
						}

						$data_text = str_replace($img,$admin_config[mall_data_root]."/images/estimate/$guide_ix/".returnFileName($img),$data_text);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
					}else{
						if(copy($img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/estimate/$guide_ix/".returnFileName($img))){
							$data_text = str_replace($img,$admin_config[mall_data_root]."/images/estimate/$guide_ix/".returnFileName($img),$data_text);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
						}
					}
				}
			}
		}

		$data_text = str_replace("http://$HTTP_HOST","",$data_text);
		$sql = "UPDATE shop_estimates_guide SET basicinfo = '$data_text' WHERE guide_ix='$guide_ix'";

		$db->query($sql);

		///////// edit 내용 처리 시작 2013-07-23 이학봉//////////////
	}

	// 이미지 저장 시작 
	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/estimate/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/estimate/$est_ix/";

	if(!is_dir($path)){
		mkdir($path, 0777);
		//exec("mkdir -m 755 ".$path);	//이미지 폴더 생성
		//chmod($path,0777)
	}
	if ($estimates_file_size > 0){
		//copy($estimates_file,$path."/"."estimates__".$est_ix.".gif");
		move_uploaded_file($estimates_file, $path.$est_ix."_".$estimates_file_name);
		//$company_stamp_str = $admin_config[mall_data_root]."/images/stamps/stamp_".$company_id.".gif";
	}

	if($estimates_file_name){
		$sql = "update shop_estimates set bfile = '".$est_ix."_".$estimates_file_name."' where est_ix = '".$est_ix."'";

		$db->query($sql);
	}
	// 이미지 저장 끝 


	if(false){	//견적서 상품은 상품추가시 ajax로 추가되므로 이부분은 필요 없어서 주석처리함 2014-09-01 이학봉
		foreach($goods_infos as $key => $value){

			$pid = $value[id];
			$pname_str .= $value[pname];
			$pcount    = $value[amount];
			$sellprice = $value[sellprice];
			$listprice = $value[listprice];
			$totalprice = $value[total_price];
			$dcprice = $value[dcprice];
			$unit_price = $value[unit_price];
			$discount_rate = $value[discount_rate];
			$opn_ix = $value[opn_ix];

			$sql = "select pname from shop_product where id = '".$key."'";
			$db->query($sql);
			$db->fetch();

			$pname = $db->dt[pname];
			$sql = "insert into ".TBL_SHOP_ESTIMATES_DETAIL." set
						est_ix = '".$est_ix."',
						pid = '".$key."',
						pname = '".$pname."',
						opn_ix = '".$opn_ix."',

						listprice = '".$listprice."',
						sellprice = '".$sellprice."',
						totalprice = '".$totalprice."',
						discountprice = '".$dcprice."',
						pcount = '".$pcount."',
						rate = '".$discount_rate."',

						wholesale_price = '".$wholesale_price."',
						wholesale_sellprice = '".$wholesale_sellprice."',
						wholesale_rate = '".$wholesale_rate."',
						wholesale_pcount = '".$wholesale_pcount."',
						wholesale_totalprice = '".$wholesale_totalprice."',
						wholesale_discountprice = '".$wholesale_discountprice."',
						regdate = NOW()
			";
			$db->query($sql);
		}
	}

	if($estimate_title == ""){ // 견적 제목이 입력되지 않았을 경우 ... 
		$db->query("update  ".TBL_SHOP_ESTIMATES." set estimate_title ='$pname_str' WHERE est_ix=$est_ix");
	}
	//echo $sql;
	session_unregister("ESTIMATE_INTRA");
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 견적서 정보가 입력되었습니다.');</script>");
	if($estimate_type == '1'){
	echo("<script>location.href = 'guide_estimate_list.php';</script>");
	}else{
	echo("<script>location.href = 'estimate_list.php';</script>");
	}
}


if ($act == "update"){

	$estimate_title = trim($estimate_title);		//견적서 타이틀
	$estimate_code = trim($estimate_code);			//견적서 코드
	if($open_date){
		$open_date	= trim($open_date)." ".date("H:i:s");					//견적예정일
	}else{
		$open_date	=  date("Y-m-d H:i:s");					//견적예정일
	}
	$estimate_type = trim($estimate_type);			//견적서타입
	$estimate_div = trim($estimate_div);			//견적서유형
	$ucode = trim($ucode);	//회원코드
	$bname = trim($bname);		//의뢰인명
	$buser_id = trim($buser_id);	//의뢰인 아이디 
	$bmem_group = trim($bmem_group);	//의뢰인 그룹
	$bmail = trim($bmail);			//의뢰인 메일
	$btel = $btel_1."-".$btel_2."-".$btel_3;	//의뢰인 전화번호
	$bmobile = $bmobile_1."-".$bmobile_2."-".$bmobile_3;	//의뢰인 핸드폰번호
	//$bzip = $zipcode1."-".$zipcode2;		//의뢰인 우편번호
	if($zipcode2 != "" || $zipcode2 != NULL){
		$bzip = "$zipcode1-$zipcode2";
	}else{
		$bzip = $zipcode1;
	}
	$baddr1 = trim($addr1);				//의뢰인 주소1
	$baddr2	= trim($addr2);				//의뢰인 주소2
	if($plan_date){
		$plan_date = trim($plan_date)." ".date("H:i:s");			//견적서완료일
	}else{
		$plan_date = date("Y-m-d H:i:s");			//견적서완료일
	}
	$company = trim($company);	//사업장명
	$com_number = $com_number_1."-".$com_number_2."-".$com_number_3;	//사업자 번호
	$com_ceo = trim($com_ceo);	//대표자명
	$com_div = trim($com_div);	//사업자유형
	$com_business_status = trim($com_business_status);	//업태
	$com_business_category = trim($com_business_category);		//업종
	$com_phone = $com_phone_1."-".$com_phone_2."-".$com_phone_3;	//대표자번호
	$com_fax = $com_fax_1."-".$com_fax_2."-".$com_fax_3;	//대표자팩스
	$com_zip = trim($com_zip);		//대표자우편번호
	$com_addr1 = trim($com_addr1);		//사업자주소1
	$com_addr2 = trim($com_addr2);		//사업자주소1
	$etc = trim($etc);			//견적서요청 사항
	$admin_etc = trim($admin_etc);			//견적서요청 사항
		
		if($estimate_type == 1){	//가이드 견적서일경우 정보가 부족하여 채워줘야함
		
		//$estimate_code = date("Ymd")."-".date("His");
		$status = '4';
		}else{
			$set_update = " estimate_code = '".$estimate_code."', ";
		}

		$sql = "update shop_estimates set
				estimate_title = '".$estimate_title."',
				$set_update
				estimate_type = '".$estimate_type."',
				estimate_div = '".$estimate_div."',
				ucode = '".$ucode."',
				bname = '".$bname."',
				buser_id = '".$buser_id."',
				bmem_group = '".$bmem_group."',
				btel = '".$btel."',
				bmobile = '".$bmobile."',
				bmail = '".$bmail."',
				bzip = '".$bzip."',
				baddr1 = '".$baddr1."',
				baddr2 = '".$baddr2."',
				company = '".$company."',
				com_ceo = '".$com_ceo."',
				com_phone = '".$com_phone."',
				com_fax = '".$com_fax."', 
				com_number = '".$com_number."',
				com_div = '".$com_div."',
				com_business_status = '".$com_business_status."',
				com_business_category = '".$com_business_category."',
				com_zip = '".$com_zip."',
				com_addr1 = '".$com_addr1."',
				com_addr2 = '".$com_addr2."',
				open_date = '".$open_date."',
				plan_date = '".$plan_date."',
				delivery_postion = '".$delivery_postion."',
				receive_method = '".$receive_method."',
				status = '".$status."',
				etc = '".$etc."',
				admin_etc = '".$admin_etc."',
				mall_ix = '".$mall_ix."',
				regdate = NOW()
			where
				est_ix = '".$est_ix."'
	";

	$db->query($sql);

	//////////////견적서 수정시 상품은 전체 삭제후 재 입력 2013-07-14 이학봉 시작/////////////////
	//$delete = "delete from ".TBL_SHOP_ESTIMATES_DETAIL."  where est_ix = '".$est_ix."'";
	//$db->query($delete);

	foreach($goods_infos as $key => $value){
		
		$pid = $value[pid];
		$pname_str .= $value[pname];
		$pcount    = $value[amount];
		$sellprice = $value[sellprice];
		$listprice = $value[listprice];
		$totalprice = $value[total_price];
		$dcprice = $value[dcprice];
		$unit_price = $value[unit_price];
		$discount_rate = $value[discount_rate];
		$opn_ix = $value[opn_ix];
		$estd_ix = $value[estd_ix];
	
		$sql = "select pname from shop_product where id = '".$pid."'";
		$db->query($sql);
		$db->fetch();
		$pname = $db->dt[pname];

		$sql = "update ".TBL_SHOP_ESTIMATES_DETAIL." set
					pid = '".$pid."',
					pname = '".$pname."',
					opn_ix = '".$opn_ix."',
					listprice = '".$listprice."',
					sellprice = '".$sellprice."',
					totalprice = '".$totalprice."',
					discountprice = '".$dcprice."',
					pcount = '".$pcount."',
					rate = '".$discount_rate."',

					wholesale_price = '".$wholesale_price."',
					wholesale_sellprice = '".$wholesale_sellprice."',
					wholesale_rate = '".$wholesale_rate."',
					wholesale_pcount = '".$wholesale_pcount."',
					wholesale_totalprice = '".$wholesale_totalprice."',
					wholesale_discountprice = '".$wholesale_discountprice."',
					regdate = NOW()
				where
					estd_ix = '".$estd_ix."'
		";
	
		$db->query($sql);
	}

	//////////////견적서 수정시 상품은 전체 삭제후 재 입력 2013-07-14 이학봉 끝/////////////////

	$sql = "select
				count(*) as total
			from
				shop_estimates_guide
			where
				est_ix = '".$est_ix."'";
	$db->query($sql);
	$db->fetch();
	$guide_cnt = $db->dt{total};

		///////// edit 내용 처리 시작 2013-07-23 이학봉//////////////
		$data_text = $event_text;
		$data_text_convert = $event_text;
		$data_text_convert = str_replace("\\","",$data_text_convert);
		preg_match_all("|<IMG .*src=\"(.*)\".*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);


		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/estimate/";
		if(!is_dir($path)){
			mkdir($path, 0777);
		}

		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/estimate/$est_ix/";

		if(!is_dir($path)){
			mkdir($path, 0777);
			//exec("mkdir -m 755 ".$path);	//이미지 폴더 생성
			//chmod($path,0777)
		}

		if(is_dir($path)){
			if($s_img_size > 0){
				move_uploaded_file($s_img, $path."/estimate_guide_".$est_ix.".gif");
			}

		}
		if(is_dir($path)){
			if($b_img_size > 0){
				move_uploaded_file($b_img, $path."/b_estimate_guide__".$est_ix.".gif");
			}

		}

		// 이미지 저장 시작 
		if ($estimates_file_size > 0){
			//copy($estimates_file,$path."/"."estimates__".$est_ix.".gif");
			move_uploaded_file($estimates_file, $path.$est_ix."_".$estimates_file_name);
			//$company_stamp_str = $admin_config[mall_data_root]."/images/stamps/stamp_".$company_id.".gif";
		}

		if ($estimates_file_size > 0){
			$sql = "update shop_estimates set bfile = '".$est_ix."_".$estimates_file_name."' where est_ix = '".$est_ix."'";
			$db->query($sql);
		}

		// 이미지 저장 끝

		for($i=0;$i < count($out);$i++){
			for($j=0;$j < count($out[$i]);$j++){

				$img = returnImagePath($out[$i][$j]);
				$img = ClearText($img);


				if(substr_count($img,$admin_config[mall_data_root]."/images/estimate/$est_ix/") == 0){// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
					if(substr_count($img,"$HTTP_HOST")>0){
						$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"]."",$img);

						@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/estimate/$est_ix/".returnFileName($img));
						if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
							@unlink($local_img_path);
						}

						$data_text = str_replace($img,$admin_config[mall_data_root]."/images/estimate/$est_ix/".returnFileName($img),$data_text);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
					}else{
						if(copy($img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/estimate/$est_ix/".returnFileName($img))){
							$data_text = str_replace($img,$admin_config[mall_data_root]."/images/estimate/$est_ix/".returnFileName($img),$data_text);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
						}
					}
				}
			}
		}

		$data_text = str_replace("http://$HTTP_HOST","",$data_text);
		///////// edit 내용 처리 시작 2013-07-23 이학봉//////////////


	if($estimate_type == '1'){
		if($guide_cnt < 1){		//가이드 견적서로 변경 경우 새로 입력
		
			$sql = "insert into shop_estimates_guide set
					est_ix = '".$est_ix."',
					gu_status = 'I',
					displan_date = '".$open_date."',
					disp = '1',
					regdate = NOW()";
			$db->query($sql);
		}else{
			$sql = "update shop_estimates_guide set
						md_code = '".$md_code."',
						gu_status = '".$mem_type."',
						displan_date = '".$open_date."',
						basicinfo = '".$data_text."',
						disp = '".$disp."',
						regdate = NOW()
					where
						est_ix = '".$est_ix."'";
			$db->query($sql);
		}
	}else{
		if($guide_cnt > 0){	//가이드 견적에서 해제시 삭제
		
			$delete = "delete from shop_estimates_guide where est_ix = '".$est_ix."'";
			$db->query($delete);
		}
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정 되었습니다.');</script>");

	if($estimate_type == '1'){
		echo("<script>location.href = 'guide_estimate_list.php';</script>");
	}else{
		echo("<script>location.href = 'estimate.php?est_ix=$est_ix';</script>");
	}
}

if ($act == "status_update"){

	$db->query("update  ".TBL_SHOP_ESTIMATES." set status = '$status' WHERE est_ix = '".$est_ix."'");
	echo("<script>top.location.href = './estimate.list.php';</script>");

}

if ($act == "delete"){		//삭제

	$db->query("DELETE FROM ".TBL_SHOP_ESTIMATES." WHERE est_ix = '".$est_ix."'");
	$db->query("DELETE FROM ".TBL_SHOP_ESTIMATES_DETAIL." WHERE est_ix = '".$est_ix."'");
	$db->query("DELETE FROM shop_estimates_guide WHERE est_ix = '".$est_ix."'");

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 삭제 되었습니다.');</script>");
	if($type == "basic"){
	echo("<script>parent.location.href = './estimate.list.php';</script>");
	}else if($type == "guide"){
	echo("<script>parent.location.href = './guide_estimate_list.php';</script>");
	}

}

if($act == "search_option"){
	
	if($pid){
		$sql = "select
					pod.*
				from
					shop_product_options as po 
					inner join shop_product_options_detail as pod on (po.opn_ix = pod.opn_ix)
				where
					po.pid = '".$pid."'
					and po.option_kind = 'b'
					order by po.pid ASC
				";
		$db->query($sql);
		$option_array = $db->fetchall();
		
		if(count($option_array) > 0){
				$html .= "<option value='0'>가격+재고관리 옵션 선택</option>";
			for($i=0;$i<count($option_array);$i++){
				
				$html .= "<option value='".$option_array[$i][id]."'>".$option_array[$i][option_div]."</option>";
			}
		}else{
			$html .= "<option value='0'>가격+재고관리 옵션 없음</option>";
		}

		echo "$html";
	}

}

if($act == "search_productinfo"){
		 
	if($pid){

			$sql = "select
						*
					from
						shop_product_options_detail 
					where
						id = '".$option_id."'
					";

			$db->query($sql);
			$db->fetch();
			

		if($type == "wholesale"){
				$products[coprice] = $db->dt[option_coprice];
				$products[listprice] = $db->dt[option_wholesale_listprice];
				$products[sellprice] = $db->dt[option_wholesale_price];
		}else{	
				$products[coprice] = $db->dt[option_coprice];
				$products[listprice] = $db->dt[option_listprice];
				$products[sellprice] = $db->dt[option_price];
		}

		$datas = json_encode($products);
		echo $datas;
		
	}else{
		echo "N";
	}

}


if ($act == "send_mail"){
	include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");
	//$path = $_SERVER["DOCUMENT_ROOT"]."/mailing/mailing_join.php";
	//$email_card_contents_basic = join ('', file ($path));

//	$sql = "insert into ".TBL_SHOP_ESTIMATES."
//		(est_ix,type,company,charger,email,mobile,plan_date,delivery_postion,receive_method,order_method,pass,etc,regdate)
//		values";

	$db->query("Select * FROM ".TBL_SHOP_ESTIMATES." WHERE est_ix = '".$est_ix."'");
	$db->fetch();

	$mail_info[mem_name] = $db->dt[charger];
	$mail_info[mem_mail] = $db->dt[email];
	$mail_info[mem_id] = $id;
	$email_card_contents_basic = "요청하신 견적서입니다";

	copy("http://".$HTTP_HOST."/admin/estimate/estimate.excel.php?company_id=".$admininfo[company_id]."&est_ix=$est_ix",$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/estimate.xls");

	$subject = " ".$mail_info[mem_name]." 님, 요청하신 견적서 입니다..";
	SendMail($mail_info, $subject,$email_card_contents_basic,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/estimate.xls");


	//echo $mail_info[mem_mail];
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 메일이 발송되었습니다.');</script>");
	echo("<script>self.close();</script>");
}

if($act == 'insert_estimate_detail'){

	$db->query("select state from shop_product where id = '".$pid."'");
	$db->fetch();

	if($db->dt[state] == 0){
		//echo "N";
		//exit;
		//echo "<script>alert('일시품절중인 상품입니다.');history.back();</script>";
		//exit;
	}

	foreach($estimate_detail[options] as $key => $option_infos ){


		//같은상품에 같은옵션일경우 count 만업데이트해야 하므로 serialize로 체크
		$options = explode("-",$option_infos[opnd_ix_array]);

		$select_option_id = $option_infos[opnd_ix_array];
		$opnd_ix = $option_infos[opnd_ix_array];
		$pid = $estimate_detail[pid];
		$pname = $estimate_detail[pname];
		$pcount = $option_infos[pcount];
		$mem_ix = $mem_ix;

		if(is_array($options)){
			if($option_size != ""){
				$option_serial = urlencode(serialize($options)."".$option_size);
			}else{
				$option_serial = urlencode(serialize($options));
			}
		}

		//현재 카트에 동일한 상품아이디와 옵션명을 가진 상품이 있는지 체크
		if($set_group){
			$where .= "and set_group = '".$set_group."' ";
		}

		if($est_ix){
			$where .= " and est_ix = '$est_ix'";
		}else{
			$where .= " and cart_key = '".session_id()."'";
		}

		$sql = "select 
					*
				from 
					shop_estimates_detail 
				where 
					pid = '$pid' 
					
					and options = '$option_serial' 
					$where";

		$db->query($sql);

		if (!$db->total){
			//상품에 관한 쿼리문

			if($db->dbms_type == "oracle"){
				$sql = "SELECT
							brand,brand_name,product_type, pname, $select_price,
							reserve,delivery_company,surtax_yorn, coprice, listprice, 
							sellprice, pcode, barcode,  admin, stock,delivery_type,account_type,
							commission,one_commission,free_delivery_yn,free_delivery_count,
							hotcon_event_id, hotcon_pcode,  co_pid,wholesale_commission,
							(select cid from shop_product_relation where pid = '$pid' and basic = 1) as cid,stock_use_yn,coupon_use_yn
						FROM 
							".TBL_SHOP_PRODUCT." 
						WHERE 
							id='$pid'";
			}else{
				$sql = "SELECT
							brand,brand_name,product_type, p.pname, 
							p.reserve,delivery_company,surtax_yorn, 
							coprice, listprice, sellprice, pcode,
							barcode, admin, stock,delivery_type,account_type,
							commission,one_commission,free_delivery_yn,
							free_delivery_count,hotcon_event_id, 
							hotcon_pcode, co_pid, wholesale_commission,
							stock_use_yn,coupon_use_yn, ci.cid, ci.depth
						FROM
							".TBL_SHOP_PRODUCT." p
							left join shop_product_relation pr on p.id = pr.pid and pr.basic = 1
							left join shop_category_info ci on pr.cid = ci.cid 
						WHERE
							p.id='$pid'";
			}

			$db->query($sql);

			//상품이 있다면
			if ($db->total || true){

				$db->fetch();
				$goods_info = $db->dt;
				
				$pcode = $goods_info[pcode];
				$barcode = $goods_info[barcode];
				$coprice = $goods_info[coprice];
				$listprice = $goods_info[listprice];
				$sellprice = $goods_info[sellprice];
				$dcprice = $goods_info[sellprice];
				$product_type = $goods_info[product_type];
				$pname = str_replace("\"","&quot;",$goods_info[pname]);
				$pname = str_replace("'","&#39;",$pname);
			
				//옵션텍스트 입력구문 //goods_view.php에서 select박스 값 array로 넘어옴.
				if($options){
					$option_price = 0;
					for($i=0;$i<count($options);$i++){
						if($options[$i]){
							$sql = "select 
										o.option_div,ot.option_name, ot.option_kind, 
										option_code, o.option_listprice, o.option_price,
										o.option_coprice, option_barcode 
									from 
										shop_product_options_detail o,
										shop_product_options ot 
									where
										id = '".$options[$i]."'
										and o.opn_ix = ot.opn_ix";
								
							$db->query($sql);
							$db->fetch();
							
							if($db->dt[option_kind] == "x2" || $db->dt[option_kind] == "s2"){
								$pname = $pname." - ".$db->dt[option_name];
								$options_text .= $db->dt[option_div]."";
							}else if($db->dt[option_kind] != "r"){//옵션이 한개만 등록되는 것을 방지 kbk 12/04/12
								if($db->dt[option_price] > 0 && $db->dt[option_kind] != "b"){
									$options_text .= $db->dt[option_name]." : ".$db->dt[option_div]."(".number_format($db->dt[option_price]).")<br>";
								}else{
									 $options_text .= $db->dt[option_name]." : ".$db->dt[option_div]."<!-- <br> -->";
								}
							}
							
							if($db->dt[option_kind] == "b" || $db->dt[option_kind] == "a" || $db->dt[option_kind] == "x" || $db->dt[option_kind] == "c" || $db->dt[option_kind] == "x2" || $db->dt[option_kind] == "s2"){
								//echo $options_text ;
								$sub_pname = $db->dt[option_div];
								if($db->dt[option_kind] == "b"){// || $db->dt[option_kind] == "a"
									$option_kind = "";
								}else{
									$option_kind = $db->dt[option_kind];
								}
								$pcode = $db->dt[option_code];
								$coprice = $db->dt[option_coprice];
								if($db->dt[option_listprice] == 0){
									$listprice = $db->dt[option_price];
								}else{
									$listprice = $db->dt[option_listprice];
								}
								$barcode = $db->dt[option_barcode];
								$sellprice = $db->dt[option_price];
								//$set_group = 1;
							}else if($db->dt[option_kind] == "s" || $db->dt[option_kind] == "p" || $db->dt[option_kind] == "c1" || $db->dt[option_kind] == "c2" || $db->dt[option_kind] == "i1" || $db->dt[option_kind] == "i2"){
								$option_price += $db->dt[option_price];
							}
						}
					}
				}


				$bl->agent_type = "W";
				///$bl->CommerceLogic($user[code],1,$cid, $pid,1,$sellprice);
				//등록할 상품의 업체 수수료 셀렉트
				
				//셀러별 정산수수료 설정 끝 2014-05-23 이학봉
				$sql = "select opn_ix from shop_product_options where pid = '".$pid."' and option_use = '1' and option_kind not in('s2','x2','x','c') ";
				$db->query($sql);
				if($db->total){
					$option_yn = "Y";
				}else{
					$option_yn = "N";
				}

				if($listprice == ""){//추가 kbk 13/06/17
					$listprice = $goods_info[listprice];
				}

				if($sellprice == ""){
					$sellprice = $goods_info[sellprice];
				}

				$brand_name = str_replace("\"","&quot;",$goods_info[brand_name]);
				$brand_name = str_replace("'","&#39;",$brand_name);

				$options_text = str_replace("\"","&quot;",$options_text);
				$options_text = str_replace("'","&#39;",$options_text);

					$sql = "select
								dt.*,
								p.delivery_type
							from 
								shop_product as p 
								inner join shop_product_delivery as pd on (p.id = pd.pid)
								inner join shop_delivery_template as dt on (pd.dt_ix = dt.dt_ix)
							where
								p.id = '".$pid."'
								and dt.product_sell_type = 'R'
								limit 0,1";

					$db->query($sql);
					$db->fetch();
					$dt_ix = $db->dt[dt_ix];

					$delivery_temp_info = delivery_template_info($dt_ix);	//배송비 템플릿 정보 가져오기 2014-05-16 이학봉
	
					if($delivery_method_self == '1'){	//방문수령 체크로 넘오올시에는 무조건 방문수령
						$delivery_method = '4';
					}else{
						if($delivery_temp_info[delivery_div] == '1'){
							$delivery_method = '1';	//택배
						}else if($delivery_temp_info[delivery_div] == '2'){
							$delivery_method = '2';	//화물
						}else if($delivery_temp_info[delivery_div] == '3'){
							$delivery_method = '3';	//직배송
						}
					}
					
					if($delivery_temp_info[delivery_basic_policy] == '5'){	//배송비결제방법선택 (선불/착불선택일 경우는 기본으로 선불로 처리)
						$delivery_pay_method = '1';
					}else{
						$delivery_pay_method = $delivery_temp_info[delivery_basic_policy];
					}

					if($goods_info[delivery_type] == '1'){	//통합배송일 경우 ori_company_id 를 본사 입점업체 키값으로 저장하고 company_id 로 불러와서 통합배송 장바구니에서 처리한다
						$sql = "select company_id from common_company_detail where com_type = 'A' limit 0,1";
						$db->query($sql);
						$db->fetch();
						$ori_company_id = $db->dt[company_id];
					}else{
						$ori_company_id = $goods_info[admin];
					}

					$sql = "select com_name from common_company_detail where company_id = '".$ori_company_id."'";
					$db->query($sql);
					$db->fetch();
					$ori_company_name = $db->dt[com_name];

					$sql = "insert into ".TBL_SHOP_ESTIMATES_DETAIL." set
								est_ix = '".$est_ix."',
								cart_key = '".session_id()."',
								pid = '".$pid."',
								pname = '".$pname."',
								opn_ix = '".$opn_ix."',

								listprice = '".$listprice."',
								sellprice = '".$sellprice."',
								totalprice = '".($sellprice+$option_price)*$pcount."',
								discountprice = '0',
								pcount = '".$pcount."',
								rate = '".$discount_rate."',

								wholesale_price = '".$wholesale_price."',
								wholesale_sellprice = '".$wholesale_sellprice."',
								wholesale_rate = '".$wholesale_rate."',
								wholesale_pcount = '".$wholesale_pcount."',
								wholesale_totalprice = '".$wholesale_totalprice."',
								wholesale_discountprice = '".$wholesale_discountprice."',
								regdate = NOW(),

								cid = '".$goods_info[cid]."',
								product_type = '".$goods_info[product_type]."',
								option_kind = '".$option_kind."',
								set_group = '".$set_group."',

								sub_pname = '".$sub_pname."',
								mem_ix = '".$mem_ix."',
								set_count = '".$set_count."',

								option_price = '".($sellprice+$option_price)*$pcount."',
								options = '".$option_serial."',
								opnd_ix = '".$opnd_ix."',
								select_option_id = '".$select_option_id."',
								option_yn = '".$option_yn."',
								options_text = '".$options_text."',

								company_id = '".$goods_info[admin]."',
								ori_company_id = '".$ori_company_id."',
								company_name = '".$ori_company_name."',
								delivery_package = '".$delivery_temp_info[delivery_package]."',
								delivery_policy = '".$delivery_temp_info[delivery_policy]."',
								delivery_type = '".$goods_info[delivery_type]."',
								dt_ix = '".$dt_ix."',
								delivery_method = '".$delivery_method."',
								delivery_pay_method = '".$delivery_pay_method."',
								delivery_addr_use = '".$delivery_temp_info[delivery_addr_use]."',
								factory_info_addr_ix = '".$delivery_temp_info[factory_info_addr_ix]."'
								";
					
					$db->query($sql);

					$options_text = "";
				//}

			}

		}//같은 상품이 있다면
		else
		{
			//echo "업데이트";
			$db->fetch();
			$cart_goods_info = $db->dt;
			//쇼핑카트에 저장된 상품갯수와 현재 입력할 상품갯수가 틀리면
			if($cart_goods_info[pcount] != $pcount || $cart_goods_info[sellprice] != $sellprice || $cart_goods_info[dt_ix] != $dt_ix || $dt->dt[delivery_method] != $delivery_method || $dt->dt[delivery_pay_method] != $delivery_pay_method ){//판매금액이 다를때도 업데이트 해줘야함 (배송정책 변경시 업데이트함 2014-05-16 이학봉)
				//쇼핑카트에 입력된 상품의 갯수와 금액을 업데이트 

				if($est_ix){	//견적센타 주문시 적립금 0으로 해줌 2013-09-27 이학봉
					$reserve = '0';
				}else{
					$reserve = $reserve;//적립금을 구매 수량만큼 늘려준다 lxf 13/06/27* $pcount
				}
				
				$sql = "select
							dt.*,
							p.delivery_type
						from 
							shop_product as p 
							inner join shop_product_delivery as pd on (p.id = pd.pid)
							inner join shop_delivery_template as dt on (pd.dt_ix = dt.dt_ix)
						where
							p.id = '".$pid."'
							and dt.product_sell_type = 'R'
							limit 0,1";

				$db->query($sql);
				$db->fetch();
				$dt_ix = $db->dt[dt_ix];

				//배송비 관련 추가 값 시작
				$delivery_temp_info = delivery_template_info($dt_ix);	//배송비 템플릿 정보 가져오기 2014-05-16 이학봉
				if($delivery_method_self == '1'){	//방문수령 체크로 넘오올시에는 무조건 방문수령
					$delivery_method = '4';
				}else{
					if($delivery_temp_info[delivery_div] == '1'){
						$delivery_method = '1';	//택배
					}else if($delivery_temp_info[delivery_div] == '2'){
						$delivery_method = '2';	//화물
					}else if($delivery_temp_info[delivery_div] == '3'){
						$delivery_method = '3';	//직배송
					}
				}
				
				$sql = "SELECT
							brand,brand_name,product_type, p.pname, 
							p.reserve,delivery_company,surtax_yorn, 
							coprice, listprice, sellprice, pcode,
							barcode, admin, stock,delivery_type,account_type,
							commission,one_commission,free_delivery_yn,
							free_delivery_count,hotcon_event_id, 
							hotcon_pcode, co_pid, wholesale_commission,
							stock_use_yn,coupon_use_yn, ci.cid, ci.depth
						FROM
							".TBL_SHOP_PRODUCT." p
							left join shop_product_relation pr on p.id = pr.pid and pr.basic = 1
							left join shop_category_info ci on pr.cid = ci.cid 
						WHERE
							p.id='$pid'";
						
				$db->query($sql);
				$db->fetch();
				$goods_info = $db->dt;

				$pcode = $goods_info[pcode];
				$barcode = $goods_info[barcode];
				$coprice = $goods_info[coprice];
				$listprice = $goods_info[listprice];
				$sellprice = $goods_info[sellprice];
				$dcprice = $goods_info[sellprice];
				$product_type = $goods_info[product_type];
				$pname = str_replace("\"","&quot;",$goods_info[pname]);
				$pname = str_replace("'","&#39;",$pname);
				
				//옵션텍스트 입력구문 //goods_view.php에서 select박스 값 array로 넘어옴.
				if($options){
					$option_price = 0;
					for($i=0;$i<count($options);$i++){
						if($options[$i]){
							$sql = "select 
										o.option_div,ot.option_name, ot.option_kind, 
										option_code, o.option_listprice, o.option_price,
										o.option_coprice, option_barcode 
									from 
										shop_product_options_detail o,
										shop_product_options ot 
									where
										id = '".$options[$i]."'
										and o.opn_ix = ot.opn_ix";
							$db->query($sql);
							$db->fetch();
							
							if($db->dt[option_kind] == "x2" || $db->dt[option_kind] == "s2"){
								$pname = $pname." - ".$db->dt[option_name];
								$options_text .= $db->dt[option_div]."";
							}else if($db->dt[option_kind] != "r"){//옵션이 한개만 등록되는 것을 방지 kbk 12/04/12
								if($db->dt[option_price] > 0 && $db->dt[option_kind] != "b"){
									$options_text .= $db->dt[option_name]." : ".$db->dt[option_div]."(".number_format($db->dt[option_price]).")<br>";
								}else{
									 $options_text .= $db->dt[option_name]." : ".$db->dt[option_div]."<!-- <br> -->";
								}
							}
							
							if($db->dt[option_kind] == "b" || $db->dt[option_kind] == "a" || $db->dt[option_kind] == "x" || $db->dt[option_kind] == "c" || $db->dt[option_kind] == "x2" || $db->dt[option_kind] == "s2"){
								//echo $options_text ;
								$sub_pname = $db->dt[option_div];
								if($db->dt[option_kind] == "b"){// || $db->dt[option_kind] == "a"
									$option_kind = "";
								}else{
									$option_kind = $db->dt[option_kind];
								}
								$pcode = $db->dt[option_code];
								$coprice = $db->dt[option_coprice];
								if($db->dt[option_listprice] == 0){
									$listprice = $db->dt[option_price];
								}else{
									$listprice = $db->dt[option_listprice];
								}
								$barcode = $db->dt[option_barcode];
								$sellprice = $db->dt[option_price];
								//$set_group = 1;
							}else if($db->dt[option_kind] == "s" || $db->dt[option_kind] == "p" || $db->dt[option_kind] == "c1" || $db->dt[option_kind] == "c2" || $db->dt[option_kind] == "i1" || $db->dt[option_kind] == "i2"){
								$option_price += $db->dt[option_price];
							}
						}
					}
				}
				

				$bl->agent_type = "W";
				///$bl->CommerceLogic($user[code],1,$cid, $pid,1,$sellprice);
				//등록할 상품의 업체 수수료 셀렉트
				
				//셀러별 정산수수료 설정 끝 2014-05-23 이학봉
				$sql = "select opn_ix from shop_product_options where pid = '".$pid."' and option_use = '1' and option_kind not in('s2','x2','x','c') ";
				$db->query($sql);
				if($db->total){
					$option_yn = "Y";
				}else{
					$option_yn = "N";
				}

				if($listprice == ""){//추가 kbk 13/06/17
					$listprice = $goods_info[listprice];
				}

				if($sellprice == ""){
					$sellprice = $goods_info[sellprice];
				}

				$brand_name = str_replace("\"","&quot;",$goods_info[brand_name]);
				$brand_name = str_replace("'","&#39;",$brand_name);

				$options_text = str_replace("\"","&quot;",$options_text);
				$options_text = str_replace("'","&#39;",$options_text);

					$sql = "select
								dt.*,
								p.delivery_type
							from 
								shop_product as p 
								inner join shop_product_delivery as pd on (p.id = pd.pid)
								inner join shop_delivery_template as dt on (pd.dt_ix = dt.dt_ix)
							where
								p.id = '".$pid."'
								and dt.product_sell_type = 'R'
								limit 0,1";

					$db->query($sql);
					$db->fetch();
					$dt_ix = $db->dt[dt_ix];

					$delivery_temp_info = delivery_template_info($dt_ix);	//배송비 템플릿 정보 가져오기 2014-05-16 이학봉
			
					if($delivery_method_self == '1'){	//방문수령 체크로 넘오올시에는 무조건 방문수령
						$delivery_method = '4';
					}else{
						if($delivery_temp_info[delivery_div] == '1'){
							$delivery_method = '1';	//택배
						}else if($delivery_temp_info[delivery_div] == '2'){
							$delivery_method = '2';	//화물
						}else if($delivery_temp_info[delivery_div] == '3'){
							$delivery_method = '3';	//직배송
						}
					}
					
					if($delivery_temp_info[delivery_basic_policy] == '5'){	//배송비결제방법선택 (선불/착불선택일 경우는 기본으로 선불로 처리)
						$delivery_pay_method = '1';
					}else{
						$delivery_pay_method = $delivery_temp_info[delivery_basic_policy];
					}

					if($goods_info[delivery_type] == '1'){	//통합배송일 경우 ori_company_id 를 본사 입점업체 키값으로 저장하고 company_id 로 불러와서 통합배송 장바구니에서 처리한다
						$sql = "select company_id from common_company_detail where com_type = 'A' limit 0,1";
						$db->query($sql);
						$db->fetch();
						$ori_company_id = $db->dt[company_id];
					}else{
						$ori_company_id = $goods_info[admin];
					}

					$sql = "select com_name from common_company_detail where company_id = '".$ori_company_id."'";
					$db->query($sql);
					$db->fetch();
					$ori_company_name = $db->dt[com_name];

					$sql = "update ".TBL_SHOP_ESTIMATES_DETAIL." set
							
								pid = '".$pid."',
								pname = '".$pname."',
								opn_ix = '".$opn_ix."',

								listprice = '".$listprice."',
								sellprice = '".$sellprice."',
								totalprice = '".($sellprice+$option_price)*$pcount."',
								discountprice = '0',
								pcount = '".$pcount."',
								rate = '".$discount_rate."',

								wholesale_price = '".$wholesale_price."',
								wholesale_sellprice = '".$wholesale_sellprice."',
								wholesale_rate = '".$wholesale_rate."',
								wholesale_pcount = '".$wholesale_pcount."',
								wholesale_totalprice = '".$wholesale_totalprice."',
								wholesale_discountprice = '".$wholesale_discountprice."',
								regdate = NOW(),

								cid = '".$goods_info[cid]."',
								product_type = '".$goods_info[product_type]."',
								option_kind = '".$option_kind."',
								set_group = '".$set_group."',

								sub_pname = '".$sub_pname."',
								mem_ix = '".$mem_ix."',
								set_count = '".$set_count."',

								option_price = '".($sellprice+$option_price)*$pcount."',
								options = '".$option_serial."',
								opnd_ix = '".$opnd_ix."',
								select_option_id = '".$select_option_id."',
								option_yn = '".$option_yn."',
								options_text = '".$options_text."',

								company_id = '".$goods_info[admin]."',
								ori_company_id = '".$ori_company_id."',
								company_name = '".$ori_company_name."',
								delivery_package = '".$delivery_temp_info[delivery_package]."',
								delivery_policy = '".$delivery_temp_info[delivery_policy]."',
								delivery_type = '".$goods_info[delivery_type]."',
								dt_ix = '".$dt_ix."',
								delivery_method = '".$delivery_method."',
								delivery_pay_method = '".$delivery_pay_method."',
								delivery_addr_use = '".$delivery_temp_info[delivery_addr_use]."',
								factory_info_addr_ix = '".$delivery_temp_info[factory_info_addr_ix]."'
							where
								pid = '$pid' 
								and est_ix = '$est_ix'
								and options = '$option_serial' 
								$where";
						
					$db->query($sql);

					$options_text = "";
				//}
			}

		}
	}

	echo "Y";

}

if($act == "get_set_group"){

	$sql = "select IFNULL(max(set_group),0)+1 as set_group from shop_estimates_detail  where pid = '".$pid."' and est_ix = '".$est_ix."'  ";

	$db->query($sql);
	$db->fetch();
	$set_group = $db->dt[set_group];
	

	if($set_group){
		$products[set_group] =  $set_group;
	}else{
		$products[set_group] = '0';
	}

	//$datas = json_encode($products);
	echo $products[set_group];
	exit;
}

if($act == 'delete_estd_ix'){
	$sql = "delete from shop_estimates_detail where estd_ix = '".$estd_ix."'";
	$db->query($sql);
}

if($act == 'delete_detail'){
	
	if(count($pid) > 0){
		for($i=0;$i<count($pid);$i++){
			$sql = "delete from shop_estimates_detail where pid = '".$pid[$i]."' and cart_key = '".session_id()."' and est_ix = '0'";
			$db->query($sql);
		}
	}
}

?>