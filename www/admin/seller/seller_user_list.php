<?
include("../class/layout.class");

$db = new Database;
$db2 = new Database;

if($max == ""){
	$max = 15; //페이지당 갯수
}else{
	$max = $max;
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

if($mode == 'search'){

	$search_str = "";

	if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
		//다중검색 시작 2014-04-10 이학봉
		/*
		if($search_type == "cmd.name"){
			$search_str .= " and AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
		}
		
		if($search_text){
			if($search_type == "cmd.name"){
				$search_str .= " and AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
			}else if($search_type == "all"){
				$search_str .= "";
			}else if($search_type == "cmd.pcs"){
				$search_str .= " and AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
			}else if($search_type == "cmd.mail"){
				$search_str .= " and AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
			}else{
				$search_str .= " and $search_type LIKE '%$search_text%' ";
			}
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

	if($charge_code !=""){
		if($charge_code == "Y"){
		$search_str .= " and csd.charge_code = cu.code ";
		}else{
		$search_str .= " and csd.charge_code != cu.code ";
		}
	}

	if($auth !=""){
		if($auth == "Y"){
		$search_str .= " and cu.auth = '4' ";
		}else{
		$search_str .= " and cu.auth != '4' ";
		}
	}

	if($_REQUEST['regdate'] == '1'){
		if($sdate != "" && $edate != ""){
			if($db->dbms_type == "oracle"){
				$search_str .= " and  to_char(".$search_date."_ , 'YYYY-MM-DD') between  '".$sdate."' and '".$edate."' ";
			}else{
				$search_str .= " and date_format(".$search_date.",'%Y-%m-%d') between  '".$sdate."' and '".$edate."' ";
			}
		}
	}

}

if($admininfo["admin_level"] == '8'){
	$search_str .= " and ccd.company_id = '".$admininfo['company_id']."'";
}

$sql = "SELECT 
			COUNT(distinct ccd.company_id) as total
		FROM 
			common_user as cu 
			inner join common_member_detail as cmd on (cu.code = cmd.code)
			inner join common_company_detail as ccd on (cu.company_id = ccd.company_id)
			inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
		where
			1
			and cu.mem_div = 'S'
			and ccd.com_type = 'S'
			$search_str ";
$db->query($sql);
$db->fetch();
$total = $db->dt[total];

if($db->dbms_type == "oracle"){

	$sql = "select
				ccd.*,
				AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
				AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
				AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs,
				cu.id,
				cu.code,
				cu.auth,
				csd.charge_code,
				cu.date
			from
				common_user as cu 
				inner join common_member_detail as cmd on (cu.code = cmd.code)
				inner join common_company_detail as ccd on (cu.company_id = ccd.company_id)
				inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
			where
				1
				and cu.mem_div = 'S'
				and ccd.com_type = 'S'
				$search_str
				order by cmd.date desc
				LIMIT $start,$max
			";

}else{

	$sql = "select
				ccd.*,
				AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
				AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
				AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs,
				cu.id,
				cu.code,
				cu.auth,
				csd.charge_code,
				cu.date
			from
				common_user as cu 
				inner join common_member_detail as cmd on (cu.code = cmd.code)
				inner join common_company_detail as ccd on (cu.company_id = ccd.company_id)
				inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
			where
				1
				and cu.mem_div = 'S'
				and ccd.com_type = 'S' 
				$search_str
				order by csd.regdate desc
				LIMIT $start,$max
			";
}

$db->query($sql);
$goods_infos = $db->fetchall();


if($mode == "excel"){

	$info_type = "seller_user_list";
	include("excel_out_columsinfo.php");
	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='seller_".$info_type."' ";
	$db->query($sql);
	$db->fetch();
	$stock_report_excel = $db->dt[conf_val];

	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='seller_checked_".$info_type."' ";
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
		$j="A";
		foreach($check_colums as $key => $value){
			if($key == "charge_code"){
				if($goods_infos[$i][charge_code] == $goods_infos[$i][code]){
				$value_str = "O";
				}else{
				$value_str = "X";
				}
			}else if($key == "auth"){
				if($goods_infos[$i][auth] == "4"){
				$value_str = "승인";//$db1->dt[$value];
				}else{
				$value_str = "미승인";
				}
			}else if($key == "date"){
				if($goods_infos[$i][auth] == "4"){
					$value_str = $goods_infos[$i][date];
				}else{
					$value_str = "";
				}
			}else{
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
	foreach($check_colums as $key => $value){
		$inventory_excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
		$col++;
	}

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="seller_'.$info_type.'.xls"');
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

$menu_name = "셀러 사용자";
$mstring = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' >
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

if($admininfo[admin_level] == 9){
$mstring .= "
	<tr>
		<td colspan=8>
		<form name='search_seller'>
		<input type='hidden' name='list_type' value='".$list_type."'>
		<input type='hidden' name='mode' value='search'>
		<table border='0' cellpadding='0' cellspacing='0' width='100%'>
		<tr>
			<td style='width:100%;' valign=top colspan=3>
				<table width=100%  cellpadding=0 cellspacing=0 border=0>
				<tr height=22>
					<td ><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>셀러사용자 검색하기 </b></td>
				</tr>
				<tr>
					<td align='left' colspan=2 height=50 width='100%' valign=top style='padding-top:5px;'>
						<table cellpadding=0 cellspacing=0 width='100%' class='input_table_box'>
						<col width='18%'>
						<col width='32%'>
						<col width='18%'>
						<col width='32%'>
						<tr height=30>
							<td class='input_box_title'>
							<select name='search_date' style='width:90px'>
							<option value='cmd.date' ".CompareReturnValue("cmd.date",$search_date,"selected").">신청일</option>
							<option value='csd.seller_date' ".CompareReturnValue("csd.seller_date",$search_date,"selected").">셀러 시작일</option>
							</select>
							<input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.search_seller);' ".CompareReturnValue("1",$regdate,"checked")."></td>
							<td class='input_box_item' colspan='3'>
								".search_date('sdate','edate',$sDate,$eDate)."
							</td>
						</tr>
						<tr height=30>
							<th class='input_box_title'>신청처리상태 </th>
							<td class='input_box_item' >
								<input type=checkbox name='auth[]' value='Y' id='seller_auth_y' ".CompareReturnValue("Y",$auth,"checked")." ><label for='seller_auth_y'> 승인</label>
								<input type=checkbox name='auth[]' value='N' id='seller_auth_x' ".CompareReturnValue("N",$auth,"checked")." ><label for='seller_auth_x'> 미승인</label>
							</td>
							<th class='input_box_title'>협력사 마스터ID </th>
							<td class='input_box_item' >
								<input type=checkbox name='charge_code' value='Y' id='charge_code_y' ".CompareReturnValue("Y",$charge_code,"checked")."><label for='charge_code_y'> 대표</label>
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
											<option value='cmd.name' ".($search_type == 'cmd.name' || $search_type == ''?'selected':'').">셀러명</option>
											<option value='ccd.com_ceo' ".($search_type == 'ccd.com_ceo'?'selected':'').">대표자명</option>
											<option value='ccd.com_name' ".($search_type == 'ccd.com_name'?'selected':'').">업체명</option>
											<option value='cmd.pcs' ".($search_type == 'cmd.pcs'?'selected':'').">연락처</option>
											<option value='cmd.mail' ".($search_type == 'cmd.mail'?'selected':'').">이메일</option>";
											$mstring .= "
											</select>
											</div>
										</td>
										<td style='padding:5px;'>
											<div id='search_text_input_div'>
												<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
											</div>
											<div id='search_text_area_div' style='display:none;'>
												<textarea name='search_text' name='search_text_area' id='search_text_area' class='tline textbox' style='padding:0px;height:90px;width:200px;' >".$search_text."</textarea>
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
}

$mstring .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' >
<col width='16%' />
<col width='*' />
<tr>
	<td >
		<table border='0' cellpadding='0' cellspacing='0' width='100%'>
			<tr>
				<td style=';padding:10px 0px 10px 0px' valign=top >
					<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>셀러 리스트 </b>
				</td>
				<td style=';padding:10px 0px 10px 0px' valign=middle >
				검색 : ".$total." 명
				</td>
			</tr>
		</table>
	</td>
	<td align='right'>";
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
		$mstring .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
		<a href='excel_config.php?".$QUERY_STRING."&info_type=seller_user_list&excel_type=seller_user_excel' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
	}else{
		$mstring .= "
		<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
	}

	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
		$mstring .= " <a href='seller_user_list.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
	}else{
		$mstring .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
	}
	$mstring .= "
	</td>
</tr>
</table>
	<form name='list_frm' action='company_batch.act.php' method='post' onsubmit='return SelectUpdate(this)' enctype='multipart/form-data' target=''><!--iframe_act-->
	<input type='hidden' name='code[]' id='code'>
	<input type='hidden' name='act' value='seller_all_update'>
	<input type='hidden' name='page_name' value='company'>
	<input type='hidden' name='search_searialize_value' value='".($_GET[mode] == "search" ? urlencode(serialize($_GET)):"")."'>
	<input type='hidden' id='pid' value=''>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='list_table_box'>
		<col width=3%>
		<col width=5%>
		<col width='*'>
		<col width=5%>
		<col width='15%'>
		<col width=10%>
		<col width=14%>
		<col width=13%>
		<col width=8%>
		<tr bgcolor=#efefef align=center height=27>
			<td align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
			<td class='m_td'>번호</td>
			<td class='m_td'>상호명 / 대표자명</td>
			<td class='m_td'>대표여부</td>
			<td class='m_td'>셀러ID/명</td>
			<td class='m_td'>연락처</td>
			<td class='m_td'>이메일</td>
			<td class='m_td'>승인여부</td>
			<td class='e_td'>사용관리</td>
		</tr>";

if($db->total){
	for($i=0;$i<count($goods_infos);$i++){
		$no = $total - ($page - 1) * $max - $i;
		$phone = explode("-",$goods_infos[$i][com_phone]);
		$fax = explode("-",$goods_infos[$i][com_fax]);

		if($goods_infos[$i][auth] == "4"){
			$auth_str = "승인 <br> (".$goods_infos[$i][date].")";
		}else{
			$auth_str = "미승인 ";
		}

		if($goods_infos[$i][code] == $goods_infos[$i][charge_code]){
			$charge_str = "O";
		}else{
			$charge_str = "X";
		}

		$mstring .="<tr height=32 align=center>
					<td class='list_box_td'><input type=checkbox name='select_pid[]' id='select_pid' value='".$goods_infos[$i][code]."'></td>
					<td class='list_box_td list_bg_gray'>".$no."</td>
					<td class='list_box_td point'>
					<a href=\"javascript:PopSWindow('./company.add.php?mmode=pop&company_id=".$goods_infos[$i][company_id]."&code=".$goods_infos[$i][code]."',985,600,'member_info')\">
					".$goods_infos[$i][com_name]." / 
					".$goods_infos[$i][com_ceo]."</a></td>
					<td class='list_box_td'>".$charge_str."</td>
					<td class='list_box_td'>
						<table>
						<tr>
							<td width='15'>
								".($goods_infos[$i][auth]=="" || $goods_infos[$i][auth]!="4" ? "<img src='../image/red_point.gif' border='0'> ":"")."
							</td>
							<td>
								<a href=\"javascript:PopSWindow('/admin/member/member_view.php?code=".$goods_infos[$i][code]."',985,600,'member_info')\">
								".$goods_infos[$i][id]." / 
								".$goods_infos[$i][name]."
								</a>
							</td>
						</tr>
						</table>
					</td>
					<td class='list_box_td '> ".$goods_infos[$i][pcs]."</td>
					<td class='list_box_td'>".$goods_infos[$i][mail]."</td>
					<td class='list_box_td'>";

					if($goods_infos[$i][auth]=="4"){
						$mstring .= $auth_str;
					} else {
						$mstring .= $auth_str;
						if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
							$mstring .= "<img src='../images/".$admininfo["language"]."/btn_comform.gif' border=0 align=absmiddle onClick=\"approve_company('".$goods_infos[$i][code]."')\" title='승인'  style='cursor:pointer;'/>";
						} else {
							$mstring .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_comform.gif' border=0 align=absmiddle  style='cursor:pointer;'></a> ";
						}
					}
					$mstring .="
					</td>
					<td class='list_box_td' align=center style='padding:0px 5px' nowrap>";
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
						$mstring .="<img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"PopSWindow('../member/member_info.php?mmode=pop&code=".$goods_infos[$i][code]."&mmode=pop',900,710,'member_view')\" style='cursor:pointer;' alt='회원정보' title='회원정보'/>";
					}else{
						$mstring .="<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align='absmiddle'></a>";
					}
			$mstring .="
					</td>
				</tr>";
	}
}else{
	$mstring .= "<tr height=50><td colspan=11 align=center style='padding-top:10px;'>등록된 셀러 사용자가 없습니다.</td></tr>";
}

$mstring .="</table><br>";
$Contents = $mstring;

$select = "<select name='update_type' onChange='view_member_num(this,\"$total\")'>
				<option value='1'>검색한 회원 전체에게</option>
				<option value='2'>선택한 회원 전체에게</option>
			</select>
			<input type='radio' name='update_kind' id='batch_update_disp' value='use_disp' ".(($update_kind == "pos" || $update_kind == "") ? "checked":"")." onclick=\"ChangeUpdateForm('batch_update_pos');\"><label for='batch_update_disp'>셀러 사용자 승인 일괄변경</label>";

$help_text .= "
			<div style='z-index:-1;position:absolute;' id='select_update_parent_save_loading'>
				<div style='width:700px;height:200px;display:block;position:relative;z-index:10;text-align:center;' id='select_update_save_loading'></div>
			</div>";

$help_text .= "
			<div id='batch_update_pos' ".($update_kind == "pos" || $update_kind == "" ? "style='display:block'":"style='display:none'")." >
			<div style='padding:4px 0px 4px 0px'><img src='../images/dot_org.gif'> <b>셀러 사용자 승인 여부</b> 
				<span class=small style='color:gray'>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'P')."</span></div>
				<table width='100%' border=0 cellpadding=3 cellspacing=0 class='input_table_box'>
					<col width='15%'>
					<col width='*'>
					<tr height=30>
						<td class='input_box_title'><b>승인여부 </b></td>
						<td class='input_box_item'>
							<input type='radio' id='use_disp_1' name='use_disp' value='4' checked><label for='use_disp_1'> 승인</label>
							<input type='radio' id='use_disp_0' name='use_disp' value='0' > <label for='use_disp_0'> 미승인</label>
						</td>
					</tr>
				</table>
				<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
				<tr height=50>
					<td colspan=4 align=center>";
					if( checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$help_text .= "
						<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
					}
				$help_text .= "
					</td> 
				</tr>
				</table>
			</div>";

$select_contents .= "".HelpBox($select, $help_text,750)."</form>";

$Contents .= "<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >";
$Contents .="<tr height=40><td  align=right>".page_bar($total, $page, $max,$query_string,"")."</td></tr>";
$Contents = $Contents."<tr><td>".$select_contents."<br></td></tr>";
$Contents .="</table>
<br>";

$Script .= "
<script language='javascript'>

function clearAll(frm){
    // 20170803 sehyun : select_pid -> 원래 code 였음.
	for(i=0;i < frm.select_pid.length;i++){
			frm.select_pid[i].checked = false;
	}
}

function checkAll(frm){
	for(i=0;i < frm.select_pid.length;i++){
			frm.select_pid[i].checked = true;
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
	//input_check_num();
}

function approve_company(code) {
	yes = confirm('셀러 사용자 승인 하시겠습니까?');
	if(yes){
		alert(code);
		window.frames['iframe_act'].location.href = 'company.act.php?act=seller_update&mode=top&code='+code;
	}else{
		return;
	}
}

function ChangeRegistDate(frm){
	if(frm.regdate.checked){
		frm.sdate.disabled = false;
		frm.edate.disabled = false;
	}else{
		frm.sdate.disabled = true;
		frm.edate.disabled = true;
	}
}

function init(){
//alert(1);
	var frm = document.search_seller;
//	onLoad('$sDate','$eDate');";

if($regdate != "1"){ 
	$Script .= "

	frm.sdate.disabled = true;
	frm.edate.disabled = true;";
}

$Script .= "
}

function SelectUpdate(frm){
	//alert(frm.search_searialize_value.value.length);

	if($('input:radio[name^=update_kind]:checked').val() == 'category'){
		if(!(frm.c_cid.value.length > 0)){
			alert('변경 또는 추가하시고자 하는 카테고리를 선택해주세요');
			return false;
		}
	}else if($('input:radio[name^=update_kind]:checked').val() == 'bs_goods_stock'){

	}
	//	alert($('input:radio[name^=update_kind]:checked').val());
	//return false;
	SelectUpdateLoading();
	
	
	if(frm.update_type.value == 1){
		if(parseInt(frm.search_searialize_value.value.length) <= 58){
			alert(language_data['product_list.js']['K'][language]);	//'검색상품 전체에 대한 적용은 검색후 가능합니다.'
			select_update_unloading();
			return false;
		}
		
		if(confirm(language_data['product_list.js']['G'][language])){//'검색상품 전체에 정보변경을 하시겠습니까?'
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
			alert(language_data['product_list.js']['H'][language]);//'선택된 제품이 없습니다. 변경하시고자 하는 상품을 선택하신 후 저장 버튼을 클릭해주세요'
			select_update_unloading();
			return false;
		}
	}

	//return false;
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

});

";

$Script .= "
function deleteMemberInfo(act, code){
 	if(confirm('해당회원 정보를 정말로 삭제하시겠습니까? \\n 삭제시 관련된 모든 정보가 삭제됩니다.')){
		window.frames['iframe_act'].location.href= 'member.act.php?act='+act+'&code='+code;
 	}
}
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