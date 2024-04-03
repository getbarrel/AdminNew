<?
	$max = 15; //페이지당 갯수

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}
	//echo substr_count($_SERVER["REQUEST_URI"],"member_black_list");

	if(substr_count($_SERVER["REQUEST_URI"],"member_black_list")){
		$black_list_where = " and black_list='Y' ";
	}
	if ($admininfo[mall_type] == "O"){
		if($db->dbms_type == "oracle"){
			$where = " where cu.mem_type in ('A') $black_list_where ";//,'S' //and cu.code = cmd.code //cu.date_ < '9999/12/31' and 
			$count_where = "where cu.mem_type in ('A') $black_list_where ";//,'S' //cu.date_ < '9999/12/31' and 
		}else{
			$where = " where cu.code != '' and cu.code = cmd.code and cu.mem_type in ('A') $black_list_where ";//,'S'
			$count_where = "where cu.mem_type in ('A') $black_list_where ";//,'S'
		}
	}else{
		if($db->dbms_type == "oracle"){
			$where = " where cu.mem_type in ('A') $black_list_where ";//and cu.code = cmd.code  //cu.date_ < '9999/12/31' and 
			$count_where = "where cu.mem_type in ('A') "; //cu.date_ < '9999/12/31' and 
		}else{
			$where = " where cu.code != '' and cu.code = cmd.code and cu.mem_type in ('A') $black_list_where "; //cu.date < '9999/12/31' and 
			$count_where = "where cu.mem_type in ('A') ";//,'S'
		}
	}

	if($region != ""){

		$where .= " and AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') LIKE  '%$region%' ";
		$count_where .= " and AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') LIKE  '%$region%' ";
		$cmd_where .= " and AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') LIKE  '%$region%' ";
	}

	if($gp_level != ""){
		$where .= " and mg.gp_level = '".$gp_level."' ";
		$count_where .= " and mg.gp_level = '".$gp_level."' ";
		$mg_where .= " and mg.gp_level = '".$gp_level."' ";
	}

	if($gp_ix != ""){
		$where .= " and cmd.gp_ix = '".$gp_ix."' ";
		$count_where .= " and cmd.gp_ix = '".$gp_ix."' ";
		$cmd_where .= " and cmd.gp_ix = '".$gp_ix."' ";
	}

	// forbiz 아이디는 숨김
	$where .=" and cu.id !='forbiz'";
	$count_where .=" and cu.id !='forbiz'";

	if($cid2){

		$sql = "
				select
					cd.company_id,
					cd.com_type
				from
					".TBL_COMMON_COMPANY_RELATION." cr
					inner join ".TBL_COMMON_COMPANY_DETAIL." as cd on (cr.company_id = cd.company_id)
				where
					relation_code ='".$cid2."'
		";
		$db->query($sql);
		$db->fetch();
		$company_id = $db->dt[company_id];
		$where .= " and cu.company_id =  '$company_id' ";
		$count_where .= " and cu.company_id =  '$company_id' ";
		$cmd_where .= " and cu.company_id =  '$company_id' ";
	}

	if($com_group != ""){	//부서그룹검색
		$where .= " and cmd.com_group =  '$com_group' ";
		$count_where .= " and cmd.com_group =  '$com_group' ";
		$cmd_where .= " and cmd.com_group =  '$com_group' ";
	}
	
	if($department != ""){	//부서검색
		$where .= " and cmd.department =  '$department' ";
		$count_where .= " and cmd.department =  '$department' ";
		$cmd_where .= " and cmd.department =  '$department' ";
	}

	if($position != ""){	//직위검색
		$where .= " and cmd.position =  '$position' ";
		$count_where .= " and cmd.position =  '$position' ";
		$cmd_where .= " and cmd.position =  '$position' ";
	}

	if($duty != ""){	//직책검색
		$where .= " and cmd.duty =  '$duty' ";
		$count_where .= " and cmd.duty =  '$duty' ";
		$cmd_where .= " and cmd.duty =  '$duty' ";
	}

	//재직구분
	if(is_array($work_devision)){		//판매상태 (판매중, 일시품절, 본사대기 ... )
		if(count($work_devision)>0){
			$where.=" AND cmd.work_devision IN ('".implode("','",$work_devision)."')";
		}
	}else{
		if($work_devision != ""){
			$where .= " and cmd.work_devision = '".$work_devision."'";
		}else{

			$work_devision='';
		}
	}

	//판매상태 검색관련
	if(is_array($state)){		//판매상태 (판매중, 일시품절, 본사대기 ... )
		if(count($state)>0){
			$where.=" AND p.state IN ('".implode("','",$state)."')";
		}
	}else{
		if($state != ""){
			$where .= " and p.state = '".$state."'";
		}else{
			//$state=array();
			$state='';
		}
	}

	$search_text = trim($search_text);
	if($db->dbms_type == "oracle"){
		if($search_type != "" && $search_text != ""){
			if($search_type == "cmd.name" || $search_type == "cmd.mail" || $search_type == "cmd.pcs" || $search_type == "cmd.tel"  || $search_type == "cu.id" || $search_type == "cmd.addr1"){
				$where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
				$count_where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
			}else{
				$where .= " and $search_type LIKE  '%$search_text%' ";
				$count_where .= " and $search_type LIKE  '%$search_text%' ";
			}
		}
	}else{
		if($search_type != "" && $search_text != ""){
			$search_text = trim($search_text);
			if($search_type == "cmd.name" || $search_type == "cmd.mail" || $search_type == "cmd.addr1" || $search_type == "cmd.com_tel" || $search_type == "cmd.pcs" || $search_type == "cmd.tel"){
				if($search_type == "cmd.pcs" || $search_type == "cmd.tel" || $search_type == "cmd.com_tel"){
					$where .= " and ( AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' OR replace(AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."'),'-','') LIKE  '%$search_text%' ) ";
					$count_where .= " and ( AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' OR replace(AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."'),'-','') LIKE  '%$search_text%') ";
				}else{
					$where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
					$count_where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
				}
			}else if($search_type == "id"){

				if(strpos($search_text,",") !== false){
					$search_array = explode(",",$search_text);
					$where .= "and ( ";
					$count_where .= "and ( ";
					for($i=0;$i<count($search_array);$i++){

						if($i == count($search_array) - 1){
							$where .= $search_type." = '".trim($search_array[$i])."'";
							$count_where .= $search_type." = '".trim($search_array[$i])."'";
						}else{
							$where .= $search_type." = '".trim($search_array[$i])."' or ";
							$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
						}
					}
					$where .= ")";
					$count_where .= ")";
				}else if(strpos($search_text,"\n") !== false){//\n

					$search_array = explode("\n",$search_text);

					$where .= "and ( ";
					$count_where .= "and ( ";
					for($i=0;$i<count($search_array);$i++){

						if($i == count($search_array) - 1){
							$where .= $search_type." = '".trim($search_array[$i])."'";
							$count_where .= $search_type." = '".trim($search_array[$i])."'";
						}else{
							$where .= $search_type." = '".trim($search_array[$i])."' or ";
							$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
						}
					}
					$where .= ")";
					$count_where .= ")";
				}else{
					$where .= "and ".$search_type." like '%".trim($search_text)."%'";
					$count_where .= "and ".$search_type." like '%".trim($search_text)."%'";
				}
			}else{
				$where .= " and $search_type LIKE  '%$search_text%' ";
				$count_where .= " and $search_type LIKE  '%$search_text%' ";
			}
		}
	}

	$startDate = $sdate;
	$endDate = $edate;
	
	if($regdate == '1'){	//가입일자
		if($startDate != "" && $endDate != ""){
			if($db->dbms_type == "oracle"){
				$where .= " and  to_char(cmd.join_date_ , 'YYYY-MM-DD') between '".$startDate."' and '".$endDate."' ";
				$count_where .= " and  to_char(cmd.join_date_ , 'YYYY-MM-DD') between '".$startDate."' and '".$endDate."' ";
			}else{

				$where .= " and date_format(cmd.join_date,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
				$count_where .= " and date_format(cmd.join_date,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
			}
		}
	}else if($regdate == '2'){ //퇴사일자
        if($startDate != "" && $endDate != ""){
            if($db->dbms_type == "oracle"){
                $where .= " and  to_char(cmd.resign_date , 'YYYY-MM-DD') between '".$startDate."' and '".$endDate."' ";
                $count_where .= " and  to_char(cmd.resign_date , 'YYYY-MM-DD') between '".$startDate."' and '".$endDate."' ";
            }else{

                $where .= " and date_format(cmd.resign_date,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
                $count_where .= " and date_format(cmd.resign_date,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
            }
        }
	}

	if($info_type == "member_resign"){
		$where .=" and cmd.work_devision = 'O' ";
		$count_where .=" and cmd.work_devision = 'O' ";
	}

	// 전체 갯수 불러오는 부분
	$sql = "SELECT 
				count(*) as total
			from 
				".TBL_COMMON_USER." cu 
				inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
				inner join ".TBL_COMMON_COMPANY_RELATION." as cr on (cu.company_id = cr.company_id)
				inner join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (cr.company_id = ccd.company_id)
			$where 
				and cu.company_id = ccd.company_id";

	$db->query($sql);
	$db->fetch();
	$total = $db->dt[total];


	if($page_type != "member_report"){//보고서 출력시 리미트 안함 이학봉
		$limit = "LIMIT $start, $max";
	}

	if($db->dbms_type == "oracle"){
		$sql = "select 
					cu.code, cu.id,  cu.company_id,
					AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
					AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail, cu.authorized as authorized, cu.is_id_auth as is_id_auth, cu.mem_type as mem_type,
					cu.visit, date_format(cu.date_,'%Y.%m.%d') as regdate,  cu.last AS last, cmd.gp_ix
				from
					".TBL_COMMON_USER." cu
					inner join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code
				$where
					$limit";
			
	}else{
		$sql = "select 
						cu.code,
						cu.id,
						cu.company_id,
						ccd.com_name,
						cmd.mem_code,
						cmd.join_date,
						cmd.resign_date,
						AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
						AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs,
						AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') as tel,
						AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
						cu.authorized as authorized, 
						cu.is_id_auth as is_id_auth,
						cu.mem_type as mem_type,
						cu.visit,
						date_format(cu.date,'%Y.%m.%d') as regdate, 
						UNIX_TIMESTAMP(cu.last) AS last, 
						cmd.gp_ix,
						cmd.com_group,
						cmd.department,
						cmd.duty,
						cmd.position,
						cr.relation_code,
						igl.fail_step
					from 
						".TBL_COMMON_USER." cu 
						inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
						inner join ".TBL_COMMON_COMPANY_RELATION." as cr on (cu.company_id = cr.company_id)
						inner join ".TBL_COMMON_COMPANY_DETAIL." as ccd on (cr.company_id = ccd.company_id)
						LEFT JOIN ig_admin_loginChk AS igl ON cu.id = igl.id
					$where 
						and cu.company_id = ccd.company_id
						ORDER BY cu.date DESC
						$limit";
	}
	$db->query($sql);

	$goods_infos = $db->fetchall();

	if($_SERVER["QUERY_STRING"] == "nset=$nset&page=$page"){
		$query_string = str_replace("nset=$nset&page=$page","",$_SERVER["QUERY_STRING"]) ;
	}else{
		$query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER["QUERY_STRING"]) ;
	}
	$str_page_bar = page_bar($total, $page, $max, $query_string,"");
	//$str_page_bar = page_bar($total, $page,$max, "&max=$max&update_kind=$update_kind&search_type=$search_type&search_text=$search_text&region=$region&gp_ix=$gp_ix&age=$age&birthday_yn=$birthday_yn&sex=$sex&mailsend_yn=$mailsend_yn&smssend_yn=$smssend_yn&mem_type=$mem_type&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD","view");//gp_level 을 사용하지 않아서 gp_ix 로 바꿈 kbk 13/02/19

if($mode == "excel"){

	if($info_type == "member_list"){
		$conf_name = "member_excel_".$info_type;
		$conf_name_check = "member_excel_checked_".$info_type;
	}

	include("excel_out_columsinfo.php");
	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='".$conf_name."' ";

	$db->query($sql);
	$db->fetch();
	$stock_report_excel = $db->dt[conf_val];

	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='".$conf_name_check."' ";
	//echo $sql;
	$db->query($sql);
	$db->fetch();
	$stock_report_excel_checked = $db->dt[conf_val];

	$check_colums = unserialize(stripslashes($stock_report_excel_checked));

	$columsinfo = $colums;

	include '../include/phpexcel/Classes/PHPExcel.php';
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

	date_default_timezone_set('Asia/Seoul');

	$inventory_excel = new PHPExcel();

	// 속성 정의
	$inventory_excel->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("accounts plan price List")
								 ->setSubject("accounts plan price List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("accounts plan price List");
	$col = 'A';
	if(is_array($check_colums) && count($check_colums) > 0 ) {
        foreach ($check_colums as $key => $value) {
            $inventory_excel->getActiveSheet(0)->setCellValue($col . "1", $columsinfo[$value][title]);
            $col++;
        }

        $before_pid = "";

        if ($info_type == "warehouse" || $info_type == "category") {
            for ($i = 0; $i < count($goods_infos); $i++) {
                $stock_assets_sum += $goods_infos[$i][stock_assets];
                $stock_sum += $goods_infos[$i][stock];
                $stock_assets_total += $goods_infos[$i][stock_assets];
                $order_cnt_sum += $goods_infos[$i][order_cnt];
            }
        }


        for ($i = 0; $i < count($goods_infos); $i++) {
            $j = "A";
            foreach ($check_colums as $key => $value) {
                if ($key == "com_group") {
                    $value_str = getGroupname('group', $goods_infos[$i][com_group]);
                } else if ($key == "department") {
                    $value_str = getGroupname('department', $goods_infos[$i][department]);
                } else if ($key == "position") {
                    $value_str = getGroupname('position', $goods_infos[$i][position]);
                } else if ($key == "duty") {
                    $value_str = getGroupname('duty', $goods_infos[$i][duty]);
                } else {
                    $value_str = $goods_infos[$i][$value];//$db1->dt[$value];
                }
                $inventory_excel->getActiveSheet()->setCellValue($j . ($z + 2), $value_str);
                $j++;
            }
            $z++;

        }

        // 첫번째 시트 선택
        $inventory_excel->setActiveSheetIndex(0);

        // 너비조정
        $col = 'A';
        foreach ($check_colums as $key => $value) {
            $inventory_excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
    }else{
        $inventory_excel->getActiveSheet(0)->setCellValue($col . "1", "엑셀 다운로드 형식 설정이 안되어 있습니다. 엑셀 설정하기를 클릭하여 저장 후 다시 엑셀 저장 시도해주세요.");
        $inventory_excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
	}
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="stock_report_'.$info_type.'.xls"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'Excel5');
	$objWriter->save('php://output');

	exit;
}


?>
