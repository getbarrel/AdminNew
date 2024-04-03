<?
include("../class/layout.class");

$max = 20; //페이지당 갯수

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}

$db = new MySQL;
$mdb = new MySQL;

$Script = "	<script type='text/javascript'>
function ChangeOrderDate(frm){
	if(frm.orderdate.checked){
		$('#startDate').addClass('point_color');
		$('#endDate').addClass('point_color');
	}else{
		$('#startDate').removeClass('point_color');
		$('#endDate').removeClass('point_color');
	}
}
function linecheck(obj){
	if(obj.is(':checked')){
		obj.parent().next().find('input[type=checkbox]').prop('checked',true);
	}else{
		obj.parent().next().find('input[type=checkbox]').prop('checked',false)
	}
}
</script>";

function pi_codeName($pi_code){
		switch ($pi_code) {
			case "use":
						$policy_text = "이용약관";
						break;
			case "person":
						$policy_text = "개인정보 취급방침";
						break;
			case "consign":
						$policy_text = "개인정보 취급위탁";
						break;
			case "third":
						$policy_text = "개인정보 제 3자 제공";
						break;
			case "seller":
						$policy_text = "판매회원 이용약관";
						break;
			case "protect":
						$policy_text = "판매회원 개인정보 보호 준수사항";
						break;
			case "duty":
						$policy_text = "세금 납부 유의사항";
						break;
			case "caution":
						$policy_text = "상품 구매 주의사항";
						break;
			case "alliance":
						$policy_text = "제휴 문의";
						break;
			case "teen":
						$policy_text = "청소년 보호대책";
						break;
			case "email":
						$policy_text = "이메일 무단 수집 거부";
						break;
			case "marketing":
						$policy_text = "마케팅 활용 동의";
						break;
			default:
				;
			break;
		}

		return $policy_text;
	}


function getDIV_Search($policy_type=""){

        $mdb = new Database;
        
       $sql = "SELECT
				*
			FROM
				shop_policy_info
			GROUP BY pi_code
			ORDER BY pi_code DESC
		";
        $mdb->query($sql);
    
        $mstring = "<table cellpadding=0 cellspacing=0 width='100%' border='0' style='padding:0;margin:0' >
    
                        ";
        if($mdb->total){
            for($i=0;$i < $mdb->total;$i++){
                $mdb->fetch($i);
                if($i % 6 == 0){
                    $mstring .="<tr>        
                                ";
                }
                $mstring .= "<td width='400px'><input type='checkbox' class='policy_type' name='policy_type[]' id='policy_type_".$i."' value='".$mdb->dt['pi_code']."' ".($policy_type ? (in_array($mdb->dt['policy_type'], $policy_type) ? "checked" : "") :"")." ><label for='policy_type_".$i."'>".pi_codeName($mdb->dt['pi_code'])."</label></td>";
            }
            $cnt = $mdb->total % 6;
            if($cnt){
                for($i=0; $i < (6-($cnt)); $i++){
                    $mstring .= "<td></td>";
                }
            }
        }                        
        $mstring .="    </tr>
                    </table>";
        return $mstring;
    }

	if($mode!="search"){
		$orderdate=1;
	}

	//검색 1주일단위 디폴트
	if ($startDate == ""){
		$before7day = mktime(0, 0, 0, date("m")  , date("d")-7, date("Y"));

		$startDate = date("Y-m-d", $before7day);
		$endDate = date("Y-m-d");
	}
	

	if($orderdate && $search_date_type){
		$where .= "and date_format(".$search_date_type.",'%Y-%m-%d') between '".$startDate."' and '".$endDate."' ";
	}	


	//상품타입
	if(is_array($policy_type)){
		for($i=0;$i < count($policy_type);$i++){
			if($policy_type[$i] != ""){
				if($policy_type_str == ""){
					$policy_type_str .= "'".$policy_type[$i]."'";
				}else{
					$policy_type_str .= ",'".$policy_type[$i]."' ";
				}
			}
		}

	if($policy_type_str != ""){
			$where .= " AND policy_name in ($policy_type_str) ";
		}
	}else{
		if($policy_type){
			$where .= " AND policy_name = '$policy_type' ";
		}
	}

	if($user_type){
		$where .= " AND user_type = '$user_type'";
	}

	if($search_type && $search_text){
		if($search_type == "idname"){
			$where .= "and (user_name LIKE '%".trim($search_text)."%'  or user_id LIKE '%".trim($search_text)."%') ";
		}else if($search_type == "policy_name"){
			$where .= "and policy_name LIKE '%".trim($search_text)."'";
		}else if($search_type == "user_ip"){
			$where .= "and user_ip = '".trim($search_text)."'";
		}
	}
	
	/*if($search_text != ""){
		if($search_type=="name") $where.=" and AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
		else $where .= " and $search_type LIKE '%$search_text%' ";
	}*/

	$sql = "SELECT 
				*
			FROM 
				shop_agreement_history
			WHERE 1	$where
	"; 

	$db->query($sql);
	$db->fetch();
	$total = $db->dt[total];


	$sql = "SELECT 
				*
			FROM 
				shop_agreement_history
			WHERE 1	$where
			ORDER BY regdate desc limit $start , $max";
	$db->query($sql);

$mstring = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	  <tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "본사관리 > $menu_name")."</td>
	  </tr>
	</table>
";

$mstring .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	<tr>
		<td>
		<form name='searchmember' metod='post'>
		<input type='hidden' name='mode' value='search' />
		<table border='0' cellpadding='0' cellspacing='0' width='100%'>
			<tr>
			<td style='width:100%;' valign=top colspan=3>
				<table width=100%  border=0 cellpadding='0' cellspacing='0'>
					<tr>
						<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>
							<table class='box_shadow' style='width:100%;' align=left border=0 cellpadding='0' cellspacing='0'>
								<tr>
									<th class='box_01'></th>
									<td class='box_02'></td>
									<th class='box_03'></th>
								</tr>
								<tr>
									<th class='box_04'></th>
									<td class='box_05' valign=top>
										<TABLE cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0>
										<TR>
											<TD bgColor=#ffffff style='padding:0 0 0 0;'>
											<table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
													<col width='20%' />
													<col width='80%' />
												 <tr height='27'>
													<td class='search_box_title' bgcolor='#efefef' width='150' align='center'>
														<select name=search_date_type >
															<option value='regdate' ".CompareReturnValue("regdate",$search_date_type,"selected").">동의일자</option>
															<option value='policy_date' ".CompareReturnValue("policy_date",$search_date_type,"selected").">약관 시작일</option>
															<option value='regdate' ".CompareReturnValue("regdate",$search_date_type,"selected").">고객 동의일</option>
														</select>
														<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.searchmember);' ".CompareReturnValue('1',$orderdate,' checked').">
													</td>
													<td class='search_box_item'>
														".search_date('startDate','endDate',$startDate,$endDate)."
													</td>
												</tr>
												<tr height='27'>
													<td class='search_box_title' bgcolor='#efefef' width='150' align='center'>약관종류<input type='checkbox' onclick=\"linecheck($(this));\" /></td>
													<td class='search_box_item'>
														".getDIV_Search($policy_type)."
													</td>
												</tr>
												<tr height='27'>
													<td class='search_box_title' bgcolor='#efefef' width='150' align='center'>회원구분</td>
													<td class='search_box_item'>
														<select name='user_level' >
															<option value='M' ".CompareReturnValue("M",$user_level,"selected").">일반회원</option>
															<option value='C' ".CompareReturnValue("C",$user_level,"selected").">사업자회원</option>
															<option value='A' ".CompareReturnValue("A",$user_level,"selected").">직원</option>
														</select>
													</td>
												</tr>
												<tr height='27'>
													<td class='search_box_title' bgcolor='#efefef' width='150' align='center'>조건검색</td>
													<td class='search_box_item'>
														<select name='search_type' >
															<option value='idname' ".CompareReturnValue("idname",$search_type,"selected").">회원명+아이디</option>
															<option value='policy_name' ".CompareReturnValue("policy_name",$search_type,"selected").">약관명</option>
															<option value='user_ip' ".CompareReturnValue("user_ip",$search_type,"selected").">접속IP</option>
														</select>
														<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:200px;' >
													</td>
												</tr>
											</table>
											</TD>
										</TR>
										</TABLE>
									</td>
									<th class='box_06'></th>
								</tr>
								<tr>
									<th class='box_07'></th>
									<td class='box_08'></td>
									<th class='box_09'></th>
								</tr>
								</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr >
			<td height='10'>
			</td>
		</tr>
		<tr >
			<td colspan=3 align=center style='padding:10px 0 20px 0'>
				<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
			</td>
		</tr>
		</table>
		</form>
		</td>
	</tr>
</table>";




if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
$mstring .= "<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center'>
	<tr>
		<td align='right' style='padding-bottom:5px;'><a href='re_order.excel.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a></td>
	</tr>
</table>";
}
//iframe_act
$mstring .= "

<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
	<col width='100'>
	<col width='80'>
	<col width='140'>
	<col width='80'>
	<col width='200'>
	<col width='80'>
	<col width='80'>
	<col width='100'> 
	<tr bgcolor=#efefef align=center height=30 style='font-weight:600;'>
		<td class='s_td'>동의일자</td>
		<td class='m_td'>회원명</td>
		<td class='m_td'>아이디</td>
		<td class='m_td'>회원유형</td>
		<td class='m_td'>약관명</td>
		<td class='m_td'>약관시작일</td>
		<td class='m_td'>고객동의일</td>
		<td class='m_td'>접속 IP</td>
	</tr>";

if($db->total){
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;
		
		if($db->dt['user_type'] == "M"){
			$db->dt['user_type'] = "일반회원";
		}else if($db->dt['user_type'] == "C"){
			$db->dt['user_type'] = "사업자회원";
		}else if($db->dt['user_type'] == "A"){
			$db->dt['user_type'] = "직원";
		}

		$mstring .="<tr height=30 align=center>
					<td class='list_box_td' bgcolor='#efefef'>".$db->dt['regdate']."</td>
					<td class='list_box_td'>".$db->dt['user_name']."</td>
					<td class='list_box_td' bgcolor='#efefef' >".$db->dt['user_id']."</td>
					<td class='list_box_td'>".$db->dt['user_type']."</td>
					<td class='list_box_td' bgcolor='#efefef' >".pi_codeName($db->dt['policy_name'])."</td>
					<td class='list_box_td' bgcolor='#efefef' >".$db->dt['policy_date']."</td>
					<td class='list_box_td'>".$db->dt['regdate']."</td>
					<td class='list_box_td'>".$db->dt['user_ip']."</td>
				</tr>";
	}
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
	

}else{
	$mstring .= "<tr height=50><td class='list_box_td' colspan=12 align=center style='padding-top:10px;'>데이터가 없습니다.</td></tr>
				";
}
$mstring .="</table>
";


$mstring .= "<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >";
$mstring .="<tr height=40><td  align=right>".page_bar($total, $page, $max,$query_string,"")."</td></tr>";
$mstring = $mstring."<tr><td>".$select_contents."<br></td></tr>";
$mstring .="</table>
<br>";

$Contents = $mstring;

$P = new LayOut;
$P->addScript = $Script;
$P->OnloadFunction = "ChangeOrderDate(document.search_frm);";
$P->strLeftMenu = member_menu();
$P->Navigation = "회원관리 > 약관동의내역";
$P->title = "약관동의내역";
$P->strContents = $Contents;
$P->PrintLayOut();

?>