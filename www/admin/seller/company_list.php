<?
include("../class/layout.class");
include("../econtract/contract.lib.php");

$db = new Database;
$db2 = new Database;


if($_COOKIE[seller_company_list_limit]){
	$max = $_COOKIE[seller_company_list_limit]; //페이지당 갯수
}else{
	$max = 30;
}

if($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

if($mode == 'search'){

	if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
		//다중검색 시작 2014-04-10 이학봉
		/*
		if($search_type == "cmd.name"){
			$search_str .= " and AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
		}
		*/

		if($search_text != ""){
			if(strpos($search_text,",") !== false){
				$search_array = explode(",",$search_text);
				$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
				$search_str .= "and ( ";
				$count_where .= "and ( ";
				for($i=0;$i<count($search_array);$i++){
					$search_array[$i] = trim($search_array[$i]);
					if($search_array[$i]){
						if($i == count($search_array) - 1){
							$search_str .= $search_type." = '".trim($search_array[$i])."'";
							$count_where .= $search_type." = '".trim($search_array[$i])."'";
						}else{
							$search_str .= $search_type." = '".trim($search_array[$i])."' or ";
							$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
						}
					}
				}
				$search_str .= ")";
				$count_where .= ")";
			}else if(strpos($search_text,"\n") !== false){//\n
				$search_array = explode("\n",$search_text);
				$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
				$search_str .= "and ( ";
				$count_where .= "and ( ";

				for($i=0;$i<count($search_array);$i++){
					$search_array[$i] = trim($search_array[$i]);
					if($search_array[$i]){
						if($i == count($search_array) - 1){
							$search_str .= $search_type." = '".trim($search_array[$i])."'";
							$count_where .= $search_type." = '".trim($search_array[$i])."'";
						}else{
							$search_str .= $search_type." = '".trim($search_array[$i])."' or ";
							$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
						}
					}
				}
				$search_str .= ")";
				$count_where .= ")";
			}else{
				$search_str .= " and ".$search_type." = '".trim($search_text)."'";
				$count_where .= " and ".$search_type." = '".trim($search_text)."'";
			}
		}

	}else{	//검색어 단일검색
		if($search_text != ""){
			if(substr_count($search_text,",")){
				$search_str .= " and ".$search_type." in ('".str_replace(",","','",str_replace(" ","",$search_text))."') ";
			}else{
				$search_str .= " and ".$search_type." LIKE '%".trim($search_text)."%' ";
			}
		}
	}

	//승인여부
	if(is_array($seller_auth) && count($seller_auth)>0){		//재고관리 (사용안함,빠른재고,WMS재고 ... )
		$search_str.=" AND ccd.seller_auth IN ('".implode("','",$seller_auth)."')";
	}else{
		if($seller_auth != ""){
			$search_str .= " and ccd.seller_auth = '".$seller_auth."'";
		}else{
			$seller_auth=array();
		}
	}

	//미니샵사용여부
	if(is_array($minishop_use) && count($minishop_use)>0){		//노출여부 
		$search_str.=" AND csd.minishop_use IN ('".implode("','",$minishop_use)."')";
	}else{
		if($disp != ""){
			$search_str .= " and csd.minishop_use = '".$minishop_use."'";
		}else{
			$disp=array();
		}
	}

	if($_REQUEST['regdate'] == '1'){
		if($sdate != "" && $edate != ""){
			if($db->dbms_type == "oracle"){
				$search_str .= " and  to_char(csd.regdate_ , 'YYYY-MM-DD') between  '".$sdate."' and '".$edate."' ";
			}else{
				$search_str .= " and date_format(csd.regdate,'%Y-%m-%d') between  '".$sdate."' and '".$edate."' ";
			}
		}
	}

	if($_REQUEST['is_econtract'] == '1'){
		$search_str .= " and ei.et_ix is not null ";
	}else if($_REQUEST['is_econtract'] == '2'){
		$search_str .= " and ei.et_ix is null ";
	}

}

$sql = "SELECT
			COUNT(*) as total
		FROM 
			common_seller_detail csd ,
			common_seller_delivery csde ,
			common_company_detail ccd
			
		where  
			com_type = 'S'
			and csd.company_id = csde.company_id
			and csd.company_id = ccd.company_id";

$db->query($sql);
$db->fetch();
$vendor_total = $db->dt[total];

if($admininfo[admin_level] == 8){
	$search_str .= " and ccd.company_id = '".$admininfo['company_id']."'";
}

$sql = "SELECT 
			COUNT(distinct ccd.company_id) as total
		FROM 
			common_company_detail as ccd
			inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
			left join common_seller_delivery as csde on (ccd.company_id = csde.company_id)
			left join econtract_info ei on csde.et_ix =ei.et_ix and csde.company_id = ei.contractor_id
		where 
			1
			and ccd.com_type = 'S'
			$search_str ";

$db->query($sql);
$db->fetch();
$total = $db->dt[total];

if($db->dbms_type == "oracle"){
		$sql = "select
				distinct ccd.company_id,
				ccd.*,
				csd.minishop_use,
				csd.seller_cid,
				csd.seller_msg,
				ci.cname,
				csd.authorized_date
				csde.et_ix,
				csde.econtract_commission
			from
				common_company_detail as ccd
				inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
				left join common_seller_delivery as csde on (ccd.company_id = csde.company_id)
				left join shop_category_info as ci on (csd.seller_cid = ci.cid)
			where
				ccd.com_type  = 'S'
				$search_str
				group by ccd.company_id
				order by csd.regdate desc
				LIMIT $start,$max
			";
}else{

	$sql = "select
				distinct ccd.company_id,
				ccd.*,
				csd.minishop_use,
				csd.seller_cid,
				csd.seller_msg,
				ci.cname,
				csd.authorized_date,
				csde.et_ix,
				csde.econtract_commission, 
				ei.et_ix as reg_et_ix, 
				ei.status , ei.com_signature, ei.contractor_signature, ei.contractor_signature_date, ei.contract_title,
				b.brand_name as seller_brand_name				
			from
				common_company_detail as ccd
				inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
				left join common_seller_delivery as csde on (ccd.company_id = csde.company_id)
				left join shop_category_info as ci on (csd.seller_cid = ci.cid)
				left join econtract_info ei on csde.et_ix =ei.et_ix and csde.company_id = ei.contractor_id
				left join shop_brand as b on (csd.seller_brand = b.b_ix)
			where
				ccd.com_type = 'S'
				$search_str
				group by ccd.company_id
				order by csd.regdate desc
				LIMIT $start,$max";
}

$db->query($sql);


if($mode == "excel"){

	$goods_infos = $db->fetchall();
	$info_type = "company_list";
	include("excel_out_columsinfo.php");
	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='seller_list_".$info_type."' ";
	$db->query($sql);
	$db->fetch();
	$stock_report_excel = $db->dt[conf_val];

	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='check_seller_list_".$info_type."' ";

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
	foreach($check_colums as $key => $value){
		$inventory_excel->getActiveSheet(0)->setCellValue($col . "1", $columsinfo[$value][title]);
		$col++;
	}

	$before_pid = "";

	for ($i = 0; $i < count($goods_infos); $i++)
	{
		if($goods_infos[$i][refund_bool]=="Y"){
			$sign = -1;
		}else{
			$style='';
			$sign = 1;
		}

		$j="A";
		foreach($check_colums as $key => $value){
			if($key == "seller_auth"){	//승인여부
				switch($goods_infos[$i][seller_auth]){
					case "N":
						$value_str="승인대기";
						break;
					case "Y":
						$value_str="승인";
						break;
					case "X":
						$value_str="승인거부";
						break;
					default:
						$value_str="-";
						break;
				}
			}else if($key == "goods_total"){		//상품수
	
				//	입점업체별 상품수량 
				$sql = "select sum(case when sp.id is not null then 1 else 0 end) as goods_total from shop_product sp where admin = '".$goods_infos[$i][company_id]."'";
				$db2->query($sql);
				$db2->fetch();
				$goods_total = $db2->dt[goods_total];

				if($goods_total){
					$value_str = $db2->dt[goods_total];
				}else{
					$value_str = "0";
				}

			}else if($key == "contract_title"){	//계약서명
				$sql = "select * from econtract_tmp where et_ix = '".$goods_infos[$i][et_ix]."' ";
				$db2->query($sql);
				$db2->fetch();
				$value_str = $db2->dt[contract_title];

			}else if($key == "minishop_use"){	//미니샵사용여부

				if($goods_infos[$i][minishop_use] == '1'){
					$value_str = '사용';
				}else{
					$value_str = '미사용';
				}

			}else if($key == "seller_cid"){	//주요상품군	
				$value_str = $goods_infos[$i][cname];
			}elseif($key == "com_div"){	//사업자유형
				switch($goods_infos[$i][com_div]){
					case "R":
						$value_str="법인사업자";
						break;
					case "P":
						$value_str="개인사업자";
						break;
					case "S":
						$value_str="간이과세자";
						break;
					case "E":
						$value_str="면세사업자";
						break;
					default:
						$value_str="-";
						break;
				}
			}else{
				$value_str = $goods_infos[$i][$value];//$db1->dt[$value];
			}

			$inventory_excel->getActiveSheet()->setCellValue($j . ($z + 2), $value_str);
			$j++;

			unset($history_text);
		}
		$z++;
	}
	// 첫번째 시트 선택
	$inventory_excel->setActiveSheetIndex(0);

	// 너비조정
	$col = 'A';
	foreach($check_colums as $key => $value){
		$inventory_excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
		$col++;
	}

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="account_list_'.$info_type.'.xls"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'Excel5');
	$objWriter->save('php://output');

	exit;
}


if($search_text != ""){
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&search_type=$search_type&search_text=$search_text&orderby=$orderby&ordertype=$ordertype&list_type=$list_type","view");
}else{
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&orderby=$orderby&ordertype=$ordertype&list_type=$list_type","view");
}

if( $admininfo[mall_use_multishop] && $admininfo[mall_div]){
	if($admininfo[admin_level] == 9){
		$menu_name = "목록관리";
	}else{
		$menu_name = "셀러업체 설정";
	}
}else{
	$menu_name = "거래처관리";
}

$mstring = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width=6%>
	<col width=*>
	<col width=12%>
	<col width=12%>
	<col width=12%>
	<col width=9%>
	<col width=9%>
	<col width='20%'>
	<tr>
	    <td align='left' colspan=8> ".GetTitleNavigation("$menu_name", "셀러업체 관리 > $menu_name")."</td>
	</tr>";

$mstring .=	"
	<tr>
		<td align='left' colspan=8 style='padding-bottom:20px;'>
		<div class='tab'>
			<table class='s_org_tab'>
			<col width='550px'>
			<col width='*'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".($list_type == "" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'><a href='./company_list.php?list_type='>셀러업체 목록</a></td>
						<th class='box_03'></th>
					</tr>
					</table>";

if($admininfo[admin_level] == 9){
$mstring .=	"
					<table id='tab_02' ".($list_type == "user" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'>
							<a href='./seller_user_list.php?list_type=user'>셀러 사용자</a>
						</td>
						<th class='box_03'></th>
					</tr>
					</table>";
}

$mstring .=	"	</td>
				<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'>
				</td>
			</tr>
			</table>
		</div>
		</td>
	</tr>";

$mstring .= "
	<tr>
	<td colspan=8>
		<form name='searchmember'>
		<input type='hidden' name='mode' value='search'>
		<input type='hidden' name='list_type' value='".$list_type."'>
		<table border='0' cellpadding='0' cellspacing='0' width='100%'>
		<tr>
			<td style='width:100%;' valign=top colspan=3>
				<table width=100%  cellpadding=0 cellspacing=0 border=0>
				<tr height=22>
					<td ><img src='../images/dot_org.gif' align=absmiddle> <b>셀러업체 검색하기</b></td>
				</tr>
				<tr>
					<td align='left' colspan=2 height=50 width='100%' valign=top style='padding-top:5px;'>
						<TABLE cellSpacing=0 cellPadding=0 style='width:100%;' align=center border=0>
						<TR>
							<TD bgColor=#ffffff style='padding:0 0 0 0;height:30px;'>
								<table cellpadding=0 cellspacing=0 width='100%' class='input_table_box'>
									<col width='18%'>
									<col width='32%'>
									<col width='18%'>
									<col width='32%'>
									<tr height=30>
										<td class='input_box_title' ><label for='regdate'>셀러 시작일</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.search_seller);' ".CompareReturnValue("1",$regdate,"checked")."></td>
										<td class='input_box_item' colspan='3'>
											".search_date('sdate','edate',$sdate,$edate)."
										</td>
									</tr>
									<tr height=30>
										<th class='input_box_title'>승인여부 </th>
										<td class='input_box_item'>
											<input type=checkbox name='seller_auth[]' value='Y' id='seller_auth_y' ".(is_array($seller_auth)?in_array('Y',$seller_auth)?'checked':'':'')." ><label for='seller_auth_y'> 승인</label>
											<input type=checkbox name='seller_auth[]' value='N' id='seller_auth_n' ".(is_array($seller_auth)?in_array('N',$seller_auth)?'checked':'':'')." ><label for='seller_auth_n'> 승인대기</label>
											<input type=checkbox name='seller_auth[]' value='X' id='seller_auth_x' ".(is_array($seller_auth)?in_array('X',$seller_auth)?'checked':'':'')." ><label for='seller_auth_x'> 승인거부</label>
										</td>
										<th class='input_box_title'>미니샵 사용여부 </th>
										<td class='input_box_item'>

											<input type=checkbox name='minishop_use[]' value='1' id='minishop_use_1'  ".(is_array($minishop_use)?in_array('1',$minishop_use)?'checked':'':'')."><label for='minishop_use_1' > 사용 </label>
											<input type=checkbox name='minishop_use[]' value='0' id='minishop_use_0'  ".(is_array($minishop_use)?in_array('0',$minishop_use)?'checked':'':'')."><label for='minishop_use_0' > 미사용 </label>
										</td>
									</tr>
									<tr>
										<th class='input_box_title'>전자계약 발급여부</th>
										<td class='input_box_item' colspan=3>
											<input type=radio name='is_econtract' value='' id='is_econtract_' ".($is_econtract == ""  ? 'checked':'')." ><label for='is_econtract_'> 전체</label>
											<input type=radio name='is_econtract' value='1' id='is_econtract_1' ".($is_econtract == "1"  ? 'checked':'')." ><label for='is_econtract_1'> 발급완료</label>
											<input type=radio name='is_econtract' value='2' id='is_econtract_2' ".($is_econtract == "2"  ? 'checked':'')."><label for='is_econtract_2'> 미발급</label>
										</td>
									</tr>
									<tr height=30>
										<td class='input_box_title'>조건검색
											<span style='padding-left:2px' class='helpcloud' help_width='220' help_height='30' help_html='검색하시고자 하는 값을 ,(콤마) 구분으로 넣어서 검색 하실 수 있습니다'><img src='/admin/images/icon_q.gif' align=absmiddle/></span>
											<input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'>
											<label for='mult_search_use'>(다중검색 체크)</label>
										</td>
										<td class='input_box_item' colspan='3'>
											<table cellpadding='0' cellspacing='0' border='0' >
												<tr>
													<td valign='top'>
														<div style='padding-top:5px;'>
														<select name='search_type' id='search_type'  style=\"font-size:12px;\">
														<option value='ccd.com_name' ".($search_type == 'ccd.com_name' || $search_type == ''?'selected':'').">업체명</option>
														<option value='ccd.com_ceo' ".($search_type == 'ccd.com_ceo'?'selected':'').">대표자</option>
														<option value='csd.shop_name' ".($search_type == 'csd.shop_name'?'selected':'').">상점이름</option>
														</select>
														</div>
													</td>
													<td style='padding:5px;'>
														<div id='search_text_input_div'>
															<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
														</div>
														<div id='search_text_area_div' style='display:none;'>
															<textarea name='search_text' name='search_text_area' id='search_text_area' class='tline textbox' style='padding:3px;height:90px;width:200px;' >".$search_text."</textarea>
														</div>
													</td>
													<td>
														<div>
															<span class='small blu' > * 다중 검색은 다중 아이디로 검색 지원이 가능합니다. 구분값은 ',' 혹은 'Enter'로 사용 가능합니다. </span>
														</div>
													</td>

												</tr>
											</table>
										</td>
									</tr>
								</table>
							</TD>
						</TR>
						</TABLE>
					</td>
				</tr>
				<tr>
					<td colspan=3 align=center  style='padding:30px 0 10px 0'>
						<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
		</form>
	</td>
</tr>
</table>";


$mstring .= "
	<table border='0' cellpadding='0' cellspacing='0' width='100%'>
	<col width=10%>
	<col width='*'>
	<col width=10%>

	<tr>
		<td >
			<img src='../images/dot_org.gif' align=absmiddle> <b>셀러업체 리스트</b>
		</td>
		<td align=right>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
	$mstring .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
					<a href='excel_config.php?".$QUERY_STRING."&info_type=company_list&excel_type=seller_list' rel='facebox' >
					<img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
}else{
	$mstring .= "<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
}
	$mstring .= "&nbsp;";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
	$mstring .= "<a href='company_list.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
}else{
	$mstring .= "<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
}

$mstring .= "
	</td>
	<td align=right>
	목록수 : <select name='max' id='max'>
				<option value='5' ".($_COOKIE[seller_company_list_limit] == '5'?'selected':'').">5</option>
				<option value='10' ".($_COOKIE[seller_company_list_limit] == '10'?'selected':'').">10</option>
				<option value='20' ".($_COOKIE[seller_company_list_limit] == '20'?'selected':'').">20</option>
				<option value='30' ".($_COOKIE[seller_company_list_limit] == '30'?'selected':'').">30</option>
				<option value='50' ".($_COOKIE[seller_company_list_limit] == '50'?'selected':'').">50</option>
				<option value='100' ".($_COOKIE[seller_company_list_limit] == '100'?'selected':'').">100</option>
				<option value='500' ".($_COOKIE[seller_company_list_limit] == '500'?'selected':'').">500</option>
				<option value='1000' ".($_COOKIE[seller_company_list_limit] == '1000'?'selected':'').">1000</option>
				<option value='1500' ".($_COOKIE[seller_company_list_limit] == '1500'?'selected':'').">1500</option>
				<option value='2000' ".($_COOKIE[seller_company_list_limit] == '2000'?'selected':'').">2000</option>
			</select>
	</td>
	</tr>
	</table>
	<form name=listform method=post action='company_batch.act.php' onsubmit='return SelectUpdate(this)'  target='act' ><!--onsubmit='return CheckDelete(this)'iframe_act -->
	<input type='hidden' name='search_searialize_value' value='".($_GET[mode] == "search" ? urlencode(serialize($_GET)):"")."'>
	<input type='hidden' id='pid' value=''>
	<input type='hidden' name='act' value='update'>
	<input type='hidden' name='mode' value = '".$mode."'><!--검색모드 (일반, 엑셀검색)-->
	<input type='hidden' name='search_type' id='listfrom_search_type' value='p.pname'><!--엑설검색 검색타입-->
	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='list_table_box'>
		<col width=3%>
		<col width=5%>
		<col width='*'>
		<col width=6%>
		<col width=8%>
		<col width=10%>
		<col width=8%>
		<col width=8%>
		<col width=6%>
		<col width=5%>
		<col width=5%>
		<col width=4%>
		<col width=4%>
		<col width=6%>
		<tr bgcolor=#efefef align=center height=27>
			<td class=s_td><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
			<td class='m_td'>번호</td>
			<td class='m_td'>업체명</td>
			<td class='m_td'>".($list_type == "user" ? "사용자":"대표자명")."</td>
			<td class='m_td'>대표전화</td>
			<td class='m_td'>주요상품군</td>
			<td class='m_td'>주요브랜드</td>
			<td class='m_td' ".($admininfo[mall_use_multishop] && $admininfo[mall_div] ? "":"style='display:none;'")." style='padding:3px;'>승인여부<br>승인일</td>
			<td class='m_td' style='padding:2px;'>미니샵<br>사용여부</td>
			<td class='m_td' style='padding:2px;'>상품수</td>
			<td class='m_td' style='padding:2px;'>관심상점<br>등록수</td>
			<td class='m_td'>셀러수</td>
			<td class='m_td' style='padding:2px;'>전자계약서<br>수수료</td>
			<td class='e_td'>사용관리</td>
		</tr>";

if($db->total){
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);

		$no = $total - ($page - 1) * $max - $i;
		
		$phone = explode("-",$db->dt[com_phone]);
		$fax = explode("-",$db->dt[com_fax]);

		if($db->dt[seller_auth] == "Y"){
			$auth_str = "승인";
		}else if($db->dt[seller_auth] == "N"){
			$auth_str = "승인대기";
		}else if($db->dt[seller_auth] == "X"){
			$auth_str = "승인거부";
		}

        $sql = "select count(*) as total from common_user where company_id = '".$db->dt[company_id]."'";
        $db2->query($sql);
        $db2->fetch();
        $seller_cnt = $db2->dt[total];
		
		//	입점업체별 상품수량 
		$sql = "select sum(case when sp.id is not null then 1 else 0 end) as goods_total from shop_product sp where admin = '".$db->dt[company_id]."'";
		$db2->query($sql);
		$db2->fetch();
		$goods_total = $db2->dt[goods_total];

		if($goods_total){
			$goods_total = $db2->dt[goods_total];
		}else{
			$goods_total = "0";
		}

		$sql = "select * from econtract_tmp where et_ix = '".$db->dt[et_ix]."' ";
		$db2->query($sql);
		$db2->fetch();
		$contract_title = $db2->dt[contract_title];


		if($db->dt[minishop_use] == '1'){
			$minishop_use = '사용';
		}else{
			$minishop_use = '미사용';
		}

		$sql = "select
					count(company_id) as cnt
				from
					shop_minishop_favorite
				where
					company_id = '".$db->dt[company_id]."'";
		$db2->query($sql);
		$db2->fetch();
		$favorite_company_id = $db2->dt[cnt];

		$now_date = date("Ymd", time());
		$now_month = date("Ymd",mktime(0,0,0,substr($now_date,4,2)-1,substr($now_date,6,2)+1,substr($now_date,0,4)));
		$sql = "select
					if(count(o.oid) > 2,1,0) as member_cnt
				from	
					shop_order as o 
					inner join shop_order_detail as od on (o.oid = od.oid)
					inner join common_company_detail as ccd on (od.company_id = ccd.company_id)
				where
					od.company_id = '".$db->dt[company_id]."'
					and od.status = 'DC'
					and o.order_date between '$now_month' and date_format(NOW(),'Ymd')
					group by o.user_code";
	
		$db2->query($sql);
		$member_array = $db2->fetchall();
		
		for($k=0;$k<count($member_cnt);$k++){
			$member_count += $member_array[$i];
		}

		if(!$member_count){
			$member_count = '0';
		}

		$mstring .="<tr height=32 align=center>
					<td class='list_box_td list_bg_gray'><input type=checkbox class=nonborder id='cpid' name='select_pid[]' value='".$db->dt[company_id]."'></td>
					<td class='list_box_td'>".$no."</td>
					<td class='list_box_td point' style='padding-left:10px; text-align:left;'>".$db->dt[com_name]."</td>
					<td class='list_box_td '>".($db->dt[com_ceo])."</td>
					<td class='list_box_td '>".$db->dt[com_phone]."</td>
					<td class='list_box_td '><span style='padding-left:2px;cursor:pointer;' class='helpcloud' help_width='100' help_height='35' help_html='".$db->dt['seller_msg']."'>".$db->dt['cname']."</span></td>
					<td class='list_box_td '>".$db->dt['seller_brand_name']."</td>
					<td class='list_box_td ' ".($admininfo[mall_use_multishop] && $admininfo[mall_div] ? "":"style='display:none;'").">".$auth_str."<br>".str_replace(" ","<br/>",$db->dt[authorized_date])."</td>
					<td class='list_box_td'>".($minishop_use)."</td>
					<td class='list_box_td'>".($goods_total)."</td>
					<td class='list_box_td'>".($favorite_company_id)." 개 </td>
					<td class='list_box_td'>".($seller_cnt)."</td>
					<td class='list_box_td' style='line-height:150%;padding:3px;' nowrap>";
					
					//($db->dt[reg_et_ix] ? "발급완료(".($db->dt[contractor_signature] ? "서명완료":"서명대기").")":"미발행")

					if($db->dt[reg_et_ix]){
						if($db->dt[com_signature] && $db->dt[contractor_signature]){
							$mstring .= "발급완료(서명완료)";
						}else{
							if($db->dt[com_signature]){
								$mstring .= "발급대기(업체서명대기)";
							}else{
								$mstring .= "발급대기(본사서명대기)";
							}
						}
					}else{
						$mstring .= "미발행";
					}

					$mstring .= " / ".$db->dt[econtract_commission]."%
					".($db->dt[contractor_signature] ? "<br>".$db->dt[contractor_signature_date]:"")."
					".($db->dt[contract_title] ? "<br><b>".$db->dt[contract_title]."</b>":"<br>".$contract_title)."
					
					
					</td>
					<td class='list_box_td' align=center style='padding:0px 5px' nowrap>";

		if($db->dt[seller_auth] == "Y" && $_SESSION['admininfo']['admin_level'] == 9){
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
				$mstring .="<a href='company_user.php?company_id=".$db->dt[company_id]."'><img src='../images/".$admininfo["language"]."/btn_add_user.gif' border=0 align='absmiddle'></a>";
			}else{
				$mstring .="<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_add_user.gif' border=0 align='absmiddle'></a>";
			}
		}

		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
			$mstring .="
				<a href='company.add.php?company_id=".$db->dt[company_id]."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align='absmiddle'></a>";
		}else{
			$mstring .="
				<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align='absmiddle'></a>";
		}
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
			if($db->dt[company_id]!=$_SESSION[admininfo][company_id]) {//셀러업체가 자기 자신은 삭제 못하도록 kbk 13/09/13
				//$mstring .="<a href=\"JavaScript:DeleteCompany('".$db->dt[company_id]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle'></a> ";
			}
		}else{
			//$mstring .="<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle'></a> ";
		}

		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
			if($admininfo[mall_type] == "O"){
			// $mstring .=($db->dt[recommend] != "Y" ? "<a href=\"javascript:RecommendCompany('".$db->dt[company_id]."', 'Y')\"><img src='../images/".$admininfo["language"]."/btn_recommend.gif' border=0 align='absmiddle'></a>" : "")."";
			}
		}
		$mstring .="
				</td>
			</tr>";
	}
}else{
	$mstring .= "<tr height=50><td colspan=14 align=center style='padding-top:10px;'>등록된 셀러업체가 없습니다.</td></tr>";
}
$mstring .="</table><br>";

$mstring .="<table width='100%' cellpadding=0 cellspacing=0 border='0' align>
			<tr hegiht=30><td colspan=12 align=right style='padding-top:5px 0px;'>".$str_page_bar."</td></tr>
			</table><br>";
$Contents = $mstring;


$select = "
	<select name='update_type' >
		<option value='2' selected>선택한 상품 전체에</option>c
		<option value='1' >검색한 상품 전체에</option>
	</select>";

$select .= "
	<input type='radio' name='update_kind' id='update_kind_minithop' value='minishop_use' ".($update_kind == 'minishop_use' || $update_kind == ''?'checked':'')." onclick=\"ChangeUpdateForm('update_seller_minishop');\"><label for='update_kind_minithop'>미니샵 사용여부 설정</label>
	<input type='radio' name='update_kind' id='update_kind_econtract' value='econtract' ".CompareReturnValue("econtract",$update_kind,"checked")." onclick=\"ChangeUpdateForm('update_seller_econtract');\"><label for='update_kind_econtract'>전자계약서 일괄발행</label>
	";
	
$help_text .= "
<div style='z-index:-1;position:absolute;' id='select_update_parent_save_loading'>
	<div style='width:700px;height:200px;display:block;position:relative;z-index:10;text-align:center;' id='select_update_save_loading'></div>
</div>";

//미니샵 사용여부
$help_text .= "
<div id='update_seller_minishop' ".($update_kind == "minishop_use" || $update_kind == ""? "style='display:block'":"style='display:none'")." >
	<div style='padding:10px 0px 4px 0'>
		<img src='../images/dot_org.gif'> <b>미니샵 처리상태 변경</b> <span class=small style='color:gray'> 변경하고자 하는 셀러를 선택하시고 저장 버튼을 클릭해주세요.</span>
	</div>
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width=160>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> <b>미니샵 승인여부 </b></td>
		<td class='input_box_item'>
			<input type='radio' name='batch_minishop_use_yn' id='batch_minishop_use_yn_1' value='1' checked><label for='batch_minishop_use_yn_1'> 사용 </label>
			<input type='radio' name='batch_minishop_use_yn' id='batch_minishop_use_yn_0' value='0' ><label for='batch_minishop_use_yn_0'> 미사용 </label>
		</td>
	</tr>
	</table>
</div>";

//전자계약서 설정
$help_text .= "
<div id='update_seller_econtract' ".($update_kind == "minishop_use"? "style='display:block'":"style='display:none'")." >
	<div style='padding:10px 0px 4px 0'>
		<img src='../images/dot_org.gif'> <b>전자계약서 설정</b> <span class=small style='color:gray'> 변경하고자 하는 셀러를 선택하시고 저장 버튼을 클릭해주세요.</span>
	</div>
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width=160>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> <b>전자계약서 설정 </b></td>
		<td class='input_box_item' >
		전자계약 선택 
		".getContractGroup($contract_group, "onchange=\"loadContract($(this), 'et_ix')\"")."
		".getContract($contract_group, $et_ix," validation=false title='전자계약서' ")."
		&nbsp;&nbsp;&nbsp;
		계약서내 수수료율 &nbsp;&nbsp;
		<input type='text' class='textbox numeric' name='electron_contract_commission' style='width:40px;' value='".$electron_contract_commission."'> %
		</td>
	</tr>
	</table>
</div>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$help_text .= "
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50><td colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
	</table>";
}else{
	$help_text .= "<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50><td align=center><a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle ></a></td></tr>
	</table>";
}

$Contents .= "".HelpBox($select, $help_text,'500')."</form>";

$Script .= "
<script language='javascript'>

function clearAll(frm){
	for(i=0;i < frm.cpid.length;i++){
			frm.cpid[i].checked = false;
	}
}
function checkAll(frm){
	for(i=0;i < frm.cpid.length;i++){
		frm.cpid[i].checked = true;
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

function ChangeRegistDate(frm){
	if($('input[name=regdate]').attr('checked') == 'checked'){
		$('input[name=sdate]').attr('disabled',false);
		$('input[name=edate]').attr('disabled',false);
	}else{
		$('input[name=sdate]').attr('disabled',true);
		$('input[name=edate]').attr('disabled',true);
	}
}

function init(){

	var frm = document.search_seller;
//	onLoad('$sDate','$eDate');";

if($regdate != "1"){ 
	$Script .= "
	frm.sdate.disabled = true;
	frm.edate.disabled = true;";
}

$Script .= "
}

function ChangeUpdateForm(selected_id){
	var area = new Array('update_seller_minishop','update_seller_econtract'); 

	for(var i=0; i<area.length; ++i){
		if(area[i]==selected_id){
				//alert('2___'+selected_id);
			//alert(selected_id);
			document.getElementById(selected_id).style.display = 'block';
			$('#'+selected_id).find('select').attr('validation','true');
			//$.cookie('goodsinfo_update_kind', selected_id.replace('batch_update_',''), {expires:1,domain:document.domain, path:'/', secure:0});
		}else{
			//alert('1___'+selected_id);
			$('#'+area[i]).css('display','none');
			$('#'+selected_id).find('select').attr('validation','false');
			//document.getElementById(area[i]).style.display = 'none';
		}
	}
}

function loadContract(obj,target) {
	
	var contract_group = obj.find('option:selected').val();
	var form = obj.closest('form').attr('name'); 

	$.ajax({ 
		type: 'GET', 
		data: {'act':'getContractList','return_type': 'json',  'contract_group':contract_group},
		url: '../econtract/contract.act.php',  
		dataType: 'json', 
		async: true, 
		beforeSend: function(){  
		},  
		error: function(request,status,error){ 
			alert('code:'+request.status+':: message:'+request.responseText+':: error:'+error);
		},  
		success: function(datas){
			$('select#'+target).find('option').not(':first').remove();
			if(datas != null){
				$.each(datas, function(i, data){ 
						$('select[name='+target+']').append(\"<option value='\"+data.et_ix+\"'>\"+data.contract_title+\"</option>\");
				});  
			}
		} 
	});  
}


function SelectUpdate(frm){
	//alert(frm.search_searialize_value.value.length);

	SelectUpdateLoading();
	
	if(frm.update_type.value == 1){
		if(parseInt(frm.search_searialize_value.value.length) <= 58){
			alert(language_data['company_list.js']['K'][language]);	//'검색상품 전체에 대한 적용은 검색후 가능합니다.'
			select_update_unloading();
			return false;
		}
		
		if(confirm(language_data['company_list.js']['G'][language])){//'검색상품 전체에 정보변경을 하시겠습니까?'
			return true;
		}else{
			select_update_unloading();
			return false;
		}
	}else if(frm.update_type.value == 2){
		var pid_checked_bool = false;
		var pid_obj=document.getElementsByName('select_pid[]');//kbk
		//for(i=0;i < frm.cpid.length;i++){//kbk
		for(i=0;i < pid_obj.length;i++){
			//if(frm.cpid[i].checked){//kbk
			if(pid_obj[i].checked){
				pid_checked_bool = true;
			}
			//	frm.cpid[i].checked = false;
		}
		if(!pid_checked_bool){
			alert(language_data['company_list.js']['H'][language]);//'선택된 제품이 없습니다. 변경하시고자 하는 상품을 선택하신 후 저장 버튼을 클릭해주세요'
			select_update_unloading();
			return false;
		}
	}

	frm.act.value = 'update';
	return true;
	//frm.submit();
	
}

function SelectUpdateLoading(){

	document.getElementById('select_update_parent_save_loading').style.zIndex = '1';
	with (document.getElementById('select_update_save_loading').style){

		width = '100%';
		height = '179px';
		backgroundColor = '#ffffff';
		filter = 'Alpha(Opacity=70)';
		//border = '1px solid red';
		opacity = '0.8';
		//left = '-20px';
		//top = '-14px';
	}

	var obj = document.createElement('div');
	with (obj.style){
		position = 'relative';
		zIndex = 100;
	}
	obj.id = 'select_update_loadingbar';

	obj.innerHTML = \"<table width=100% height=100%><tr><td valign=middle align=center><img src='/admin/images/indicator.gif' border=0 width=32 height=32 align=absmiddle> 상품정보를 변경중입니다. 잠시만 기다려주세요.</td></tr></table>\";

	document.getElementById('select_update_save_loading').appendChild(obj);

	document.getElementById('select_update_save_loading').style.display = 'block';
}

function select_update_unloading(){

	parent.document.getElementById('select_update_parent_save_loading').style.zIndex = '-1';
	parent.document.getElementById('select_update_loadingbar').innerHTML ='';
	parent.document.getElementById('select_update_save_loading').innerHTML ='';
	parent.document.getElementById('select_update_save_loading').style.display = 'none';
}";

$Script .= "
function deleteMemberInfo(act, code){
	if(confirm('해당회원 정보를 정말로 삭제하시겠습니까? \\n 삭제시 관련된 모든 정보가 삭제됩니다.')){
		window.frames['iframe_act'].location.href= 'member.act.php?act='+act+'&code='+code;
	}
}

$(document).ready(function (){

//다중검색어 시작 2014-04-10 이학봉

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

//다중검색어 끝 2014-04-10 이학봉
	
	$('#max').change(function(){
		var value= $(this).val();
		$.cookie('seller_company_list_limit', value, {expires:1,domain:document.domain, path:'/', secure:0});
		document.location.reload();
	});
});

</script>";
$Script .= "<script language='javascript' src='company.add.js'></script>";

$P = new LayOut;
$P->addScript = "$Script";
$P->strLeftMenu = seller_menu();
$P->Navigation = "셀러관리 > 셀러업체 관리 > $menu_name";
$P->title = "$menu_name";
$P->strContents = $Contents;
$P->PrintLayOut();


?>