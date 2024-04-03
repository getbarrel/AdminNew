<?
include("../class/layout.class");

$db = new Database;

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

$search_str = "";
if($search_text){
	if($search_type == "cmd.name"){
		$search_str .= " and AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
	}else{
		$search_str .= " and $search_type LIKE '%$search_text%' ";
	}
}
if($seller_auth){
	$search_str .= " and seller_auth = '$seller_auth' ";
}
if($admininfo[mem_type] == "MD"){
	$search_str .= " and ccd.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
}

if($delivery_policy){
	$search_str .= " and csdv.delivery_policy = '".$delivery_policy."' ";
}

if($delivery_basic_policy){
	$search_str .= " and csdv.delivery_basic_policy = '".$delivery_basic_policy."' ";
}



//print_r($admininfo);
if($admininfo[admin_level] == 9){
	if($list_type == "user"){
		$sql = "SELECT COUNT(*) as total
				FROM common_seller_detail csd , common_seller_delivery csdv , common_company_detail ccd  , common_user cu , common_member_detail cmd
				where  com_type = 'CS' and csd.company_id = csdv.company_id
				and csd.company_id = ccd.company_id  and ccd.company_id = cu.company_id
				and cu.code = cmd.code
				$search_str ";
		//echo $sql;
		$db->query($sql);
		$db->fetch();
		$total = $db->dt[total];
		//echo $total;
		$sql = "SELECT ccd.*, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cu.id, cu.code, sum(case when sp.id is not null then 1 else 0 end) as goods_total
				FROM common_seller_detail csd , common_seller_delivery csdv ,
				common_company_detail ccd ,
				common_member_detail cmd ,
				common_user cu
				left join shop_product sp on cu.company_id = sp.admin
				where  com_type = 'CS' and csd.company_id = csdv.company_id
				and csd.company_id = ccd.company_id 
				and ccd.company_id = cu.company_id
				and cu.code = cmd.code
				$search_str
				group by ccd.company_id, cmd.code order by csd.regdate desc  LIMIT $start,$max";
		//echo $sql;
		$db->query($sql);
	}else{
		$sql = "SELECT COUNT(*) as total
				FROM common_seller_detail csd , common_seller_delivery csdv , common_company_detail ccd
				where  com_type = 'CS' and csd.company_id = csdv.company_id
				and csd.company_id = ccd.company_id  $search_str ";
		$db->query($sql);
		$db->fetch();
		$total = $db->dt[total];
		//echo $total;
		$sql = "SELECT ccd.*, sum(case when sp.id is not null then 1 else 0 end) as goods_total
				FROM common_seller_detail csd , common_seller_delivery csdv , common_company_detail ccd left join shop_product sp on ccd.company_id = sp.admin
				where  com_type = 'CS' and csd.company_id = csdv.company_id and csd.company_id = ccd.company_id $search_str group by ccd.company_id order by csd.regdate desc  LIMIT $start,$max";
		//echo $sql;
		$db->query($sql);
	}
}else if($admininfo[admin_level] == 8){
	if($list_type == "user"){
		$sql = "SELECT COUNT(*) as total
				FROM common_seller_detail csd , common_seller_delivery csdv , 
				common_company_detail ccd,
				common_member_detail cmd ,
				common_user cu
				where  com_type = 'CS' and csd.company_id = csdv.company_id 
				and ccd.company_id = '".$admininfo[company_id]."'
				and csd.company_id = ccd.company_id  
				and ccd.company_id = cu.company_id
				and cu.code = cmd.code
				$search_str ";
		$db->query($sql);
		$db->fetch();
		$total = $db->dt[0];
		$sql = "SELECT ccd.*, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cu.id, cu.code FROM common_seller_detail csd , 
				common_seller_delivery csdv , 
				common_company_detail ccd,
				common_member_detail cmd ,
				common_user cu					
				where  com_type = 'CS' 
				and csd.company_id = csdv.company_id 
				and csd.company_id = ccd.company_id 
				and ccd.company_id = '".$admininfo[company_id]."' 
				and ccd.company_id = cu.company_id
				and cu.code = cmd.code
				$search_str
				order by csd.regdate desc  LIMIT $start,$max";
		//echo $sql;
		$db->query($sql);
	}else{
		$sql = "SELECT COUNT(*) as total
				FROM common_seller_detail csd , common_seller_delivery csdv , common_company_detail ccd
				where  com_type = 'CS' and csd.company_id = csdv.company_id and ccd.company_id = '".$admininfo[company_id]."'
				and csd.company_id = ccd.company_id  $search_str ";
		$db->query($sql);
		$db->fetch();
		$total = $db->dt[0];
		$sql = "SELECT * FROM common_seller_detail csd , common_seller_delivery csdv , common_company_detail ccd left join shop_product sp on ccd.company_id = sp.admin
				where  com_type = 'CS' and csd.company_id = csdv.company_id and csd.company_id = ccd.company_id and ccd.company_id = '".$admininfo[company_id]."' $search_str
				order by csd.regdate desc  LIMIT $start,$max";
		//echo $sql;
		$db->query($sql);
	}
}
if($search_text != ""){
	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&search_type=$search_type&search_text=$search_text&orderby=$orderby&ordertype=$ordertype&list_type=$list_type","view");
}else{
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&orderby=$orderby&ordertype=$ordertype&list_type=$list_type","view");
	//echo $total.":::".$page."::::".$max."<br>";
}

//print_r($admininfo);
if( $admininfo[mall_use_multishop] && $admininfo[mall_div]){
	if($admininfo[admin_level] == 9){
		$menu_name = "가맹점 전체목록관리";
	}else{
		$menu_name = "가맹점 전체목록관리 설정";
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
	<col width='22%'>
	<tr>
	    <td align='left' colspan=8> ".GetTitleNavigation("$menu_name", "가맹점 전체목록관리 관리 > $menu_name")."</td>
	</tr>";
if($admininfo[admin_level] == 9){
$mstring .=	"
			<tr height=22>
				<td colspan='8' style='padding:5px 0px;'><img src='../images/dot_org.gif' align=absmiddle> <b>가맹점 전체목록관리 일괄등록</b></td>
			</tr>
			 <tr>
			 	<td colspan=8>
			 	<form name='excel_seller_form' method='post' action='company_exel.act.php' enctype='multipart/form-data' target='iframe_act' onsubmit='return CheckFormValue(this)' >
			 	<input type='hidden' name='act' value='excel_input'>
				<table width='100%' border=0 cellpadding=0 cellspacing=1 class='input_table_box'>
					<col width='30%'>
					<col width='*'>
					<tr height=30 align=center>
						<td class='input_box_title'><b>엑셀파일 입력</b> <a href='seller_list.xls'><img src='../images/".$admininfo["language"]."/btn_sample_excel_save.gif' align='absmiddle' ></a>  </td>
						<td class='input_box_item'><span><input type=file class='textbox' name='excel_file' style='width:60%' validation=true title='엑셀파일 입력'></span></td>
					</tr>
				</table>
				<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
				<tr bgcolor=#ffffff ><td style='padding:10px 0px;' align=center><input type='image' src='../images/".$admininfo['language']."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
				</table>
				</form>
			 	</td>
			 </tr>";
}
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
						<td class='box_02'  ><a href='?list_type='>가맹점 전체목록관리 목록</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($list_type == "user" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >
							<a href='?list_type=user'>가맹점 전체목록관리별 사용자 목록</a>

						</td>
						<th class='box_03'></th>
					</tr>
					</table>

				</td>
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
        <form name='searchmember'>
		<input type='hidden' name='list_type' value='".$list_type."'>
        <table border='0' cellpadding='0' cellspacing='0' width='100%'>
            <tr>
                <td style='width:100%;' valign=top colspan=3>
                    <table width=100%  cellpadding=0 cellspacing=0 border=0>
                        <tr height=22>
                            <td ><img src='../images/dot_org.gif' align=absmiddle> <b class=blk>가맹점 전체목록관리 검색하기</b></td>
                        </tr>
                        <tr>
                            <td align='left' colspan=2 height=50 width='100%' valign=top style='padding-top:5px;'>
                                <table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
                                    <tr>
                                        <th class='box_01'></th>
                                        <td class='box_02'></td>
                                        <th class='box_03'></th>
                                    </tr>
                                    <tr>
                                        <th class='box_04'></th>
                                        <td class='box_05' valign=top>
                                            <TABLE cellSpacing=0 cellPadding=0 style='width:100%;' align=center border=0>
                                                <TR>
                                                    <TD bgColor=#ffffff style='padding:0 0 0 0;height:30px;'>
                                                        <table cellpadding=2 cellspacing=1 width='100%' class='input_table_box'>
															<col width='17%'>
															<col width='33%'>
															<col width='17%'>
															<col width='33%'>
                                                            <tr height=30>
                                                                <th class='input_box_title'>조건검색 </th>
                                                                <td class='input_box_item'>
                                                                    <table cellpadding='0' cellspacing='0' border='0' >

																		<tr>
                                                                            <td >
                                                                                <select name=search_type>
                                                                                <option value='com_name' ".CompareReturnValue("com_name",$search_type,"selected").">업체명</option>
                                                                                <option value='com_ceo' ".CompareReturnValue("com_ceo",$search_type,"selected").">대표자</option>
                                                                                <option value='com_number' ".CompareReturnValue("com_number",$search_type,"selected").">사업자번호</option>
                                                                                <option value='shop_name' ".CompareReturnValue("shop_name",$search_type,"selected").">상점이름</option>";
																				if($list_type == "user"){
																				$mstring .= "
                                                                                <option value='cmd.name' ".CompareReturnValue("cmd.name",$search_type,"selected").">담당자명</option>
																				<option value='cu.id' ".CompareReturnValue("cu.id",$search_type,"selected").">사용자 ID</option>";
																				}
																				$mstring .= "
                                                                                <!--option value='com_phone' ".CompareReturnValue("phone",$search_type,"selected").">대표전화</option-->
                                                                                </select>
                                                                            </td>
                                                                            <td ><input type=text class=textbox name='search_text' value='".$search_text."' style='width:95%;' ></td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                                <th class='input_box_title'>승인여부 </th>
                                                                <td class='input_box_item'>
                                                                    <input type=radio name='seller_auth' value='' id='seller_auth_' ".CompareReturnValue("",$seller_auth,"checked")."><label for='seller_auth_'>전체</label>
																	<input type=radio name='seller_auth' value='Y' id='seller_auth_y' ".CompareReturnValue("Y",$seller_auth,"checked")." ><label for='seller_auth_y'>승인</label>
																	<input type=radio name='seller_auth' value='N' id='seller_auth_n' ".CompareReturnValue("N",$seller_auth,"checked")." ><label for='seller_auth_n'>승인대기</label>
																	<input type=radio name='seller_auth' value='X' id='seller_auth_x' ".CompareReturnValue("X",$seller_auth,"checked")." ><label for='seller_auth_x'>승인거부</label>
                                                                </td>
                                                            </tr>
															<tr height=30>
                                                                <th class='input_box_title'>본사정책 적용유무 </th>
                                                                <td class='input_box_item' colspan=3>
																	<input type=radio name='delivery_policy' value='' id='delivery_policy_'  ".($delivery_policy == "" ? "checked":"")."><label for='delivery_policy_' >전체</label>
																	<input type=radio name='delivery_policy' value='1' id='delivery_policy_1'  ".($delivery_policy == "1" ? "checked":"")."><label for='delivery_policy_1' >본사 기본배송 정책 사용</label>
																	<input type=radio name='delivery_policy' value='2' id='delivery_policy_2' ".($delivery_policy == "2" ? "checked":"")."><label for='delivery_policy_2'>가맹점 전체목록관리 배송정책 설정</label>
                                                                </td>
                                                            </tr>
															<tr height=30>
                                                                <th class='input_box_title'>가맹점 전체목록관리 배송 정책</th>
                                                                <td class='input_box_item' colspan=3>
																	<input type=radio name='delivery_basic_policy' value='' id='delivery_basic_policy_' ".(($company_id == "" || $delivery_basic_policy == "" || $delivery_basic_policy == "") ? "checked":"")."><label for='delivery_basic_policy_'>전체</label>
																	<input type=radio name='delivery_basic_policy' value='1' id='delivery_basic_policy_1' ".($delivery_basic_policy == "1" ? "checked":"")."><label for='delivery_basic_policy_1'>선불</label>
																	<input type=radio name='delivery_basic_policy' value='2' id='delivery_basic_policy_2' ".($delivery_basic_policy == "2" ? "checked":"")."><label for='delivery_basic_policy_2'>착불</label>
																	<input type=radio name='delivery_basic_policy' value='3' id='delivery_basic_policy_3' ".($delivery_basic_policy == "3" ? "checked":"")."><label for='delivery_basic_policy_3'>무료</label>
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
                        <tr >
                            <td colspan=3 align=center  style='padding:10px 0 10px 0'>
                                <input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        </form>
    </td>
</tr>";
}

$mstring .= "</table>";

$mstring .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<tr>
		<td colspan=8>
			<table border='0' cellpadding='0' cellspacing='0' width='100%'>
				<tr>
					<td style='width:100%;padding:10px 0px 10px 0px' valign=top colspan=3>
						<img src='../images/dot_org.gif' align=absmiddle> <b class=blk>가맹점 리스트</b>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='list_table_box'>
		<col width=6%>
		<col width=*>
		<col width=12%>
		<col width=12%>
		<col width=12%>
		<col width=9%>
		<col width='22%'>
	<tr bgcolor=#efefef align=center height=27>
		<td class='s_td'>번호</td>
		<td class='m_td'>업체명</td>
		<td class='m_td'>".($list_type == "user" ? "사용자":"대표자명")."</td>
		<td class='m_td'>대표전화</td>
		<td class='m_td'>팩스</td>
		<td class='m_td' ".($admininfo[mall_use_multishop] && $admininfo[mall_div] ? "":"style='display:none;'").">승인여부</td>
		<td class='e_td'>사용관리</td>
		</tr>";

$vendor_total = $db-total;
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


		$mstring .="<tr height=32 align=center>
					<td class='list_box_td list_bg_gray'>".$no."</td>
					<td class='list_box_td point'><a href='chainstore.add.php?company_id=".$db->dt[company_id]."'>".$db->dt[com_name]."</a></td>
					<td class='list_box_td list_bg_gray'>".($list_type == "user" ? "<a href='chainstore_user.php?company_id=".$db->dt[company_id]."&code=".$db->dt[code]."'>".$db->dt[name]."(".$db->dt[id].")":$db->dt[com_ceo])."</td>
					<td class='list_box_td'>".$db->dt[com_phone]."</td>
					<td class='list_box_td list_bg_gray'>".$db->dt[com_fax]."</td>
					<td class='list_box_td list_bg_gray' ".($admininfo[mall_use_multishop] && $admininfo[mall_div] ? "":"style='display:none;'").">".$auth_str."</td>
					<td class='list_box_td' align=center style='padding:0px 5px' nowrap>";
			if($db->dt[seller_auth] == "Y"){
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
					$mstring .="<a href='chainstore_user.php?company_id=".$db->dt[company_id]."'><img src='../images/".$admininfo["language"]."/btn_add_user.gif' border=0 align='absmiddle'></a>";
				}else{
					$mstring .="<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_add_user.gif' border=0 align='absmiddle'></a>";
				}
			}
			$mstring .="
					<a href='chainstore.add.php?company_id=".$db->dt[company_id]."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align='absmiddle'></a>
					<a href=\"JavaScript:DeleteCompany('".$db->dt[company_id]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle'></a> ";
			if($admininfo[mall_type] == "O"){
			$mstring .=($db->dt[recommend] != "Y" ? "<a href=\"javascript:RecommendCompany('".$db->dt[company_id]."', 'Y')\"><img src='../images/".$admininfo["language"]."/btn_recommend.gif' border=0 align='absmiddle'></a>" : "")."";
			}
			$mstring .="
					</td>
				</tr>";
	}
}else{
	$mstring .= "<tr height=50><td colspan=8 align=center style='padding-top:10px;'>등록된 가맹점 전체목록관리가 없습니다.</td></tr>";
}
$mstring .="</table><br>";
$mstring .="<table width='100%' cellpadding=0 cellspacing=0 border='0' >";
	if( $admininfo[mall_use_multishop] && $admininfo[admin_level] == 9){
		

		$mstring .= "<tr hegiht=30><td colspan=8 align=center style='padding:10px 0px;'>".$str_page_bar."</td></tr>";
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
			if(checkMyService("ADD","VENDOR")){
				$mstring .= "<tr hegiht=30><td colspan=8 align=right style='padding-top:10px;'><a href='chainstore.add.php'><img src='../images/".$admininfo["language"]."/btn_company_add.gif' border=0></a></td></tr>";
			}else{
				$mstring .= "<tr hegiht=30><td colspan=8 align=right style='padding-top:10px;'><a href=\"javascript:alert('가맹점 전체목록관리 허용 갯수를 초과했습니다. 마이서비스에서 신청 하시기바랍니다.');\"><img src='../images/".$admininfo["language"]."/btn_company_add.gif' border=0></a></td></tr>";
			}
		}
	}else{
	//	$mstring .= "<tr hegiht=40><td colspan=8 align=right style='padding-top:10px;'><a href='chainstore.add.php'><img src='../image/b_companyadd.gif' border=0></a></td></tr>";
		$mstring .= "<tr hegiht=30><td colspan=8 align=right style='padding-top:10px 0px;'>".$str_page_bar."</td></tr>";
	}
$mstring .="</table><br>";

$Contents = $mstring;

$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' >귀사에서 관리하는 가맹점을 등록 관리하실 수 있습니다</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' >가맹점 정보를 수정하시고자 할때는 수정 버튼 또는 업체명을 클릭하시면 수정하실수 있습니다</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td class='small' >가맹점 전체목록관리 <b>승인후</b> 가맹점 전체목록관리의 사용자를 추가 하실 수 있습니다.</td></tr>

</table>
";

//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A',$menu_name,"menu_name");

$help_text = HelpBox("$menu_name", $help_text);
$Contents .= $help_text;

$Script = "<script language='javascript' src='company.add.js'></script>";
/*
$P = new AdminPage();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->strContents = $Contents;
echo $P->AdminFrame();
*/
$P = new LayOut;
$P->addScript = "$Script";
$P->strLeftMenu = chainstore_menu();
$P->Navigation = "가맹점관리 > 가맹점 전체목록관리 관리 > $menu_name";
$P->title = "$menu_name";
$P->strContents = $Contents;
$P->PrintLayOut();


?>