<?
include("../class/layout.class");

$max = 5; //페이지당 갯수

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}


$db = new Database;

if($db->dbms_type == "oracle"){
	$db->query("SELECT cmd.code,AES_DECRYPT(jumin,'".$db->ase_encrypt_key."') as jumin,birthday,birthday_div,AES_DECRYPT(name,'".$db->ase_encrypt_key."') as name,
	AES_DECRYPT(mail,'".$db->ase_encrypt_key."') as mail, AES_DECRYPT(zip,'".$db->ase_encrypt_key."') as zip,AES_DECRYPT(addr1,'".$db->ase_encrypt_key."') as addr1,
	AES_DECRYPT(addr2,'".$db->ase_encrypt_key."') as addr2,AES_DECRYPT(tel,'".$db->ase_encrypt_key."') as tel,tel_div,AES_DECRYPT(pcs,'".$db->ase_encrypt_key."') as pcs,
	black_list,info,sms,nick_name,job,cmd.date_ as regdate2, recent_order_date,recom_id,gp_ix,sex_div,mem_level,branch,team,department,position,add_etc1,add_etc2,add_etc3,add_etc4,add_etc5,add_etc6, cu.*, ccd.* FROM ".TBL_COMMON_USER." cu LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code LEFT JOIN ".TBL_COMMON_COMPANY_DETAIL." ccd ON cu.company_id=ccd.company_id where cu.code = '$code' ORDER BY cu.date_ DESC");
}else{
	$db->query("SELECT cmd.code,birthday,birthday_div,AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,
	AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."') as mail, AES_DECRYPT(UNHEX(zip),'".$db->ase_encrypt_key."') as zip,AES_DECRYPT(UNHEX(addr1),'".$db->ase_encrypt_key."') as addr1,
	AES_DECRYPT(UNHEX(addr2),'".$db->ase_encrypt_key."') as addr2,AES_DECRYPT(UNHEX(tel),'".$db->ase_encrypt_key."') as tel,tel_div,AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as pcs,
	black_list,info,sms,nick_name,job,cmd.date as regdate2, cmd.file,recent_order_date,recom_id,gp_ix,sex_div,mem_level,branch,team,department,position,add_etc1,add_etc2,add_etc3,add_etc4,add_etc5,add_etc6, cu.*, ccd.* FROM ".TBL_COMMON_USER." cu LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code LEFT JOIN ".TBL_COMMON_COMPANY_DETAIL." ccd ON cu.company_id=ccd.company_id where cu.code = '$code' ORDER BY cu.date DESC");
}
$db->fetch();
$com_zip   = explode("-", $db->dt[com_zip]);

$tel   = explode("-", $db->dt[tel]);
$pcs   = explode("-", $db->dt[pcs]);
$zip   = explode("-", $db->dt[zip]);
list($com_phone1, $com_phone2, $com_phone3) = split("-",$db->dt[com_phone]);
list($com_fax1, $com_fax2, $com_fax3) = split("-",$db->dt[com_fax]);
list($com_num1, $com_num2, $com_num3) = split("-",$db->dt[com_number]);


if ($db->dt[info]) $info = "정보수신"; else $info = " 수신하지않음";
if ($db->dt["sms"]) $sms = "정보수신"; else $sms = " 수신하지않음";
if ($db->dt["birthday_div"]) $birthday_div = "양력"; else $birthday_div = " 음력";

$db2 = new Database;
$db2->query("select * from shop_join_info where disp = 'Y' and field like 'add_etc%' order by vieworder ");
$join_info = $db2->fetchall();

if(is_array($join_info))
{
	foreach ($join_info as $key => $sub_array) {
		$select_ = array("field_value_text"=>explode("|",$sub_array[field_value]));
		array_insert($sub_array,14,$select_);
		$join_info[$key] = $sub_array;
	}
}

$Script = "
<script language='JavaScript' >
function act(act,ta_ix, code){
	if (act == 'mem_talk_delete')
	{
		if(confirm('정말로 삭제하시겠습니까?'))
		{
			window.frames['iframe_act'].location.href= 'member.act.php?act='+act+'&ta_ix='+ta_ix+'&code='+code;
		}
	}
}

function showTabContents(vid, tab_id){
	var area = new Array('member_info_view','member_order_list','member_modify_list','member_black_list','member_mbank_list','member_raddr_list','member_couphon_list');
	var tab = new Array('tab_01','tab_02','tab_03','tab_04','tab_05','tab_06','tab_07');

	for(var i=0; i<area.length; ++i){
		if(area[i]==vid){
			document.getElementById(vid).style.display = 'block';
			if(window.addEventListener) document.getElementById(tab_id).setAttribute('class','on');
			else document.getElementById(tab_id).className = 'on';
		}else{
			document.getElementById(area[i]).style.display = 'none';
			if(window.addEventListener) {
				document.getElementById(tab[i]).setAttribute('class','');
			} else {
				document.getElementById(tab[i]).className = '';
			}
		}
	}

}

</Script>";


$Contents = "

<TABLE cellSpacing=0 cellPadding=0 width='1000' align=center border=0>
	<TR>
		<td align=center>
			<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>
				<tr >
					<td align='left' colspan=2> ".GetTitleNavigation("회원정보 보기 및 상담하기", "회원관리 > 회원정보 보기 및 상담하기", false)."</td>
				</tr>
				<tr height=20><td class='p11 ls1' style='padding:1px 0 5px 0px;text-align:left;'  colspan=2><b>".$db->dt[name]."</b> 님의 회원정보 입니다. </td></tr>
				<tr>
					<td align=left style='' width='*' valign='top'>
						<div class='tab' style='width:99%;'>
							<table class='s_org_tab' width=100%>
								<col width='700px'>
								<col width='*'>
								<tr>
									<td class='tab'>
										<table id='tab_01' ".($info_type == "basic_member" || $info_type == "" ? "class='on' ":"").">
										<tr>
											<th class='box_01'></th>
											<td class='box_02' >";

												$Contents .= "<a href='member_view.php?info_type=basic_member&mmode=pop&code=$code'>회원정보</a>";

											$Contents .= "
											</td>
											<th class='box_03'></th>
										</tr>
										</table>
										
										<table id='tab_02' ".($info_type == "member_order" ? "class='on' ":"").">
										<tr>
											<th class='box_01'></th>
											<td class='box_02' >";

												$Contents .= "<a href='member_view.php?info_type=member_order&mmode=pop&code=$code'>회원구매내역</a>";

											$Contents .= "
											</td>
											<th class='box_03'></th>
										</tr>
										</table>

										<table id='tab_03' ".($info_type == "member_info" ? "class='on' ":"").">
										<tr>
											<th class='box_01'></th>
											<td class='box_02' >";

												$Contents .= "<a href='member_view.php?info_type=member_info&mmode=pop&code=$code'>회원정보변경이력</a>";

											$Contents .= "
											</td>
											<th class='box_03'></th>
										</tr>
										</table>

										<table id='tab_04' ".($info_type == "black_list" ? "class='on' ":"").">
										<tr>
											<th class='box_01'></th>
											<td class='box_02' >";

												$Contents .= "<a href='member_view.php?info_type=black_list&mmode=pop&code=$code'>불량회원정보</a>";

											$Contents .= "
											</td>
											<th class='box_03'></th>
										</tr>
										</table>

										<table id='tab_05' ".($info_type == "mbank_list" ? "class='on' ":"").">
										<tr>
											<th class='box_01'></th>
											<td class='box_02' >";

												$Contents .= "<a href='member_view.php?info_type=mbank_list&mmode=pop&code=$code'>M통장관리</a>";

											$Contents .= "
											</td>
											<th class='box_03'></th>
										</tr>
										</table>
										
										<table id='tab_06' ".($info_type == "raddr_list" ? "class='on' ":"").">
										<tr>
											<th class='box_01'></th>
											<td class='box_02' >";

												$Contents .= "<a href='member_view.php?info_type=raddr_list&mmode=pop&code=$code'>배송지관리</a>";

											$Contents .= "
											</td>
											<th class='box_03'></th>
										</tr>
										</table>

										<table id='tab_07' ".($info_type == "cupon_list" ? "class='on' ":"").">
										<tr>
											<th class='box_01'></th>
											<td class='box_02' >";

												$Contents .= "<a href='member_view.php?info_type=cupon_list&mmode=pop&code=$code'>쿠폰함</a>";

											$Contents .= "
											</td>
											<th class='box_03'></th>
										</tr>
										</table>
									</td>
									<td align=left style='vertical-align:bottom;padding-bottom:10px;'><!--a href=\"javascript:PoPWindow('reserve.pop.php?code=".$_GET[code]."',650,700,'reserve_pop')\"><b>적립금 : ".number_format($reserve_sum)." 원</b></a--></td>
								</tr>
							</table>
						</div>
						<div class='mallstory t_no' style='width:97%;'>";
if($info_type == 'basic_member' || $info_type == ""){
$Contents .= "
							<div id='member_info_view' style='width:100%;height:100%'>
								<table border='0' width='100%' cellspacing='0' cellpadding='0' style='border:5px solid #F8F9FA'>
									<tr>
										<td >
											<table border='0' width='100%' cellspacing='0' cellpadding='0' bgcolor='#c0c0c0' class='input_table_box'>

												<tr height=23 bgcolor='#ffffff'>
													<td class='input_box_title' width='20%' > 이름</td>
													<td class='input_box_item' width='30%'>&nbsp;".Black_list_check($db->dt[code],$db->dt[name])."</td>
													<td class='input_box_title'  width='20%'> 주민번호</td>
													<td class='input_box_item' width='30%'>&nbsp;";
														$jm = explode("-",$db->dt[jumin]);
														$Contents .=  $jm[0]." - *******";
$Contents .= "
													</td>
												</tr>
												<tr>
													<td class='input_box_title'>";

													if($db->dt[tel_div] == "C"){
														$Contents .= "회사전화";
													}else{
														$Contents .= "유선전화";
													}

$Contents .= "
													</td>
													<td class='input_box_item' TBL_COMMON_MEMBER_DETAIL>&nbsp;".$db->dt[tel]."</td>
													<td class='input_box_title'> 휴대폰</td>
													<td class='input_box_item'>&nbsp;".$db->dt[pcs]."</td>
												</tr>
												<tr height=23 bgcolor='#ffffff'>
													<td class='input_box_title'  TBL_COMMON_MEMBER_DETAIL> 생년월일</td>
													<td class='input_box_item'>&nbsp;".$db->dt["birthday"]." (".$birthday_div.")</td>
													<td class='input_box_title'> SMS수신</td>
													<td class='input_box_item'>&nbsp;".$sms."</td>
												</tr>
												<tr height=23 bgcolor='#ffffff'>
													<td class='input_box_title'  TBL_COMMON_MEMBER_DETAIL> 이메일</td>
													<td class='input_box_item'>&nbsp;".$db->dt[mail]."</td>
													<td class='input_box_title'> 아이디</td>
													<td class='input_box_item'>&nbsp;<b>".$db->dt[id]."</b></td>
												</tr>
												<tr height=23 bgcolor='#ffffff'>
													<td class='input_box_title'  TBL_COMMON_MEMBER_DETAIL> 우편번호</td>
													<td class='input_box_item'>&nbsp;".$db->dt[zip]."</td>
													<td class='input_box_title'> 정보수신</td>
													<td class='input_box_item'>&nbsp;".$info."</td>
												</tr>
												<tr height=23 bgcolor='#ffffff'>
													<td class='input_box_title'  TBL_COMMON_MEMBER_DETAIL> 주소</td>
													<td class='input_box_item' colspan='3'>&nbsp;".$db->dt[addr1]." ".$db->dt[addr2]."</td>
												</tr>";

								if($db->dt[mem_type] == "C"){
$Contents .= "
												<tr height=23 bgcolor='#ffffff'>
													<td class='input_box_title'  TBL_COMMON_MEMBER_DETAIL> 회사명</td>
													<td class='input_box_item'>&nbsp;".$db->dt[com_name]."</td>
													<td class='input_box_title'> 사업자번호</td>
													<td class='input_box_item'>&nbsp;".$db->dt["com_number"]."</td>
												</tr>
												<tr height=23 bgcolor='#ffffff'>
													<td class='input_box_title'  TBL_COMMON_MEMBER_DETAIL> 대표전화</td>
													<td class='input_box_item'>&nbsp;".$db->dt[com_phone]."</td>
													<td class='input_box_title' > FAX</td>
													<td class='input_box_item'>&nbsp;".$db->dt["com_fax"]."</td>
												</tr>
												<tr height=23 bgcolor='#ffffff'>
													<td class='input_box_title'  TBL_COMMON_MEMBER_DETAIL> 종목</td>
													<td class='input_box_item'>&nbsp;".$db->dt[com_business_status]."</td>
													<td class='input_box_title' > 업태</td>
													<td class='input_box_item'>&nbsp;".$db->dt["com_business_category"]."</td>
												</tr>
												<tr height=23 bgcolor='#ffffff'>
													<td class='input_box_title'  TBL_COMMON_MEMBER_DETAIL> 회사 우편번호</td>
													<td class='input_box_item'>&nbsp;".$db->dt[com_zip]."</td>
													<td class='input_box_title' > 대표자</td>
													<td class='input_box_item'>&nbsp;".$db->dt[com_ceo]."</td>
												</tr>
												<tr height=23 bgcolor='#ffffff'>
													<td class='input_box_title'  TBL_COMMON_MEMBER_DETAIL> 회사 주소</td>
													<td class='input_box_item' colspan='3'>&nbsp;".$db->dt[com_addr1]." ".$db->dt[com_addr2]."</td>
												</tr>
                                                <tr height=23 bgcolor='#ffffff'>
													<td class='input_box_title'  TBL_COMMON_MEMBER_DETAIL> 회사 홈페이지</td>
													<td class='input_box_item' colspan='3'>&nbsp;".$db->dt[com_homepage]."</td>
												</tr>";
								}

$Contents .= "
												<tr height=23 bgcolor='#ffffff'>
													<td class='input_box_title'  TBL_COMMON_MEMBER_DETAIL> 등록일</td>
													<td class='input_box_item'>&nbsp;".$db->dt[regdate2]."</td>
													<td class='input_box_title'> 방문일</td>
													<td class='input_box_item'>&nbsp;".$db->dt[last]."</td>
												</tr>
												<tr height=23 bgcolor='#ffffff'>
													<td class='input_box_title'  TBL_COMMON_MEMBER_DETAIL> 방문주소</td>
													<td class='input_box_item' colspan='3'>&nbsp;".$db->dt[ip]."</td>
													
												</tr>
												<!--tr>
													<td class=leftmenu align='left'  TBL_COMMON_MEMBER_DETAIL> 후불결제 </td>
													<td bgcolor='#ffffff' align='left' style='color:red;font-weight:bold;'>&nbsp;".($db->dt[afterpayment_yn]  == "Y" ? "후불가능":"후불 불가능")."</td>
													<td class=leftmenu align='center'></td>
													<td bgcolor='#ffffff' align='left'></td>
												</tr-->
												<!-- 항목 설정에 따라서 항목을 뿌려줌 시작 kbk -->";

												$cnt_join_info=count($join_info);
												for($i=0;$i<$cnt_join_info;$i++){
												$Contents .= "
												<tr height=23 bgcolor='#ffffff'>
													<td class='input_box_title'  TBL_COMMON_MEMBER_DETAIL> ".$join_info[$i]["field_name"]."</td>
													<td class='input_box_item' colspan=3>&nbsp;".$db->dt[$join_info[$i]["field"]]."</td>
												</tr>";

												}
$Contents .= "
												<!-- 항목 설정에 따라서 항목을 뿌려줌 끝 kbk -->
												<!--tr>
													<td class=leftmenu align='left'  TBL_COMMON_MEMBER_DETAIL> 추가정보1</td>
													<td bgcolor='#ffffff' align='left' colspan=3>&nbsp;".$db->dt[add_etc1]."</td>
												</tr>
												<tr height=23 bgcolor='#ffffff'>
													<td class=leftmenu align='left'  TBL_COMMON_MEMBER_DETAIL> 추가정보2</td>
													<td bgcolor='#ffffff' align='left' colspan=3>&nbsp;".$db->dt[add_etc2]."</td>
												</tr>
												<tr height=23 bgcolor='#ffffff'>
													<td class=leftmenu align='left'  TBL_COMMON_MEMBER_DETAIL> 추가정보3</td>
													<td bgcolor='#ffffff' align='left' colspan=3>&nbsp;".$db->dt[add_etc3]."</td>
												</tr>
												<tr height=23 bgcolor='#ffffff'>
													<td class=leftmenu align='left'  TBL_COMMON_MEMBER_DETAIL> 추가정보4</td>
													<td bgcolor='#ffffff' align='left' colspan=3>&nbsp;".$db->dt[add_etc4]."</td>
												</tr>
												<tr height=23 bgcolor='#ffffff'>
													<td class=leftmenu align='left'  TBL_COMMON_MEMBER_DETAIL> 추가정보5</td>
													<td bgcolor='#ffffff' align='left' colspan=3>&nbsp;".$db->dt[add_etc5]."</td>
												</tr>
												<tr height=23 bgcolor='#ffffff'>
													<td class=leftmenu align='left'  TBL_COMMON_MEMBER_DETAIL> 추가정보6</td>
													<td bgcolor='#ffffff' align='left' colspan=3>&nbsp;".$db->dt[add_etc6]."</td>
												</tr-->
											</table>
										</td>
									</tr>
								</table>
							</div>";
}


if($info_type == 'member_order'){
$Contents .= "
							<div id='member_order_list' style=';width:100%;'>".PrintOrderProduct($db, $code)."</div>";
}

if($info_type == 'member_info'){
$Contents .= "
							<div id='member_modify_list' style='width:100%;'>".PrintModifylist($code)."</div>";
}

if($info_type == 'black_list'){
$Contents .= "
							<div id='member_black_list' style='width:100%;'>".PrintBlacklist($code)."</div>";
}

if($info_type == 'mbank_list'){
$Contents .= "
							<div id='member_mbank_list' style='width:100%;'>".PrintMbanklist($code)."</div>";
}

if($info_type == 'raddr_list'){
$Contents .= "
							<div id='member_raddr_list' style='width:100%;'>".PrintRaddrlist($code)."</div>";
}

if($info_type == 'cupon_list'){
$Contents .= "
							<div id='member_couphon_list' style='width:100%;'>".PrintCouphonlist($code)."</div>";
}

$Contents .= "
						</div>
					</td>";

$Contents1s .= "
					<td width=30% valign=top style='padding:0px 3px'>
						<table width=100% border=0 cellpadding=0 cellspacing=0>
							<tr>
								<td valign=top>
									<form name='mem_talk_info_".$code."' method='post' action='member.act.php' >
									<input type='hidden' name=act value='mem_talk_insert'>
									<input type='hidden' name=code value='".$code."'>
										<table width=350 border=0 cellpadding=0 cellspacing=0>
										<tr height=25 bgcolor='#ffffff'>
											<td style='border-bottom:2px solid #efefef' width='50%' align='left'><img src='../images/dot_org.gif' align=absmiddle> <b>회원상담내역관리</b></td>
											<td style='border-bottom:2px solid #efefef' width='50%' align=right>";
											if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
												$Contents1s .= "
												<input type=image src='../images/".$admininfo["language"]."/btn_counsel_save.gif' style='border:0px'>";
											}else{
												$Contents1s .= "
												<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_counsel_save.gif' style='border:0px'></a>";
											}
											$Contents1s.="
											</td>
										</tr>
										</table>
										<table width=350 border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
										<tr>
											<td align='left' colspan=2 height=150 valign=top style='padding-top:3px;'>
												<table border='0' width='100%' cellspacing='0' cellpadding='0' >
													<tr>
														<td >
															<table border='0' width='100%' cellspacing='1' cellpadding='0' bgcolor='#c0c0c0'>
																<tr height=25 bgcolor='#ffffff'>
																	<td align=right class=leftmenu style='padding-right:5px;' nowrap><b>상담자</b> : <input type=text style='width:100;border:1px solid #efefef' name='ta_counselor' value='".$admininfo[charger]."'></td>
																</tr>
																<tr bgcolor='#ffffff'>
																	<td align='left' valign=middle><textarea  rows=8 name='ta_memo' style='height:100px;width:90%;border:0px;'></textarea></td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									</form>
								</td>
							</tr>
							<tr>
								<td valign=top>
									".PrintMemberCouncelMemo($db, $code)."
								</td>
							</tr>
						</table>
					</td>";
$Contents .= "
				</tr>
			</table>
		</td>
	</tr>
</TABLE>
";


$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "회원관리 > 회원정보 보기 및 상담하기";
$P->NaviTitle = "회원정보 보기 및 상담하기";
$P->strContents = $Contents;
echo $P->PrintLayOut();



function PrintOrderProduct($mdb, $ucode){
global $admin_config,$DOCUMENT_ROOT;
$mstring = "
			<table border='0' width='100%' cellspacing='0' cellpadding='0'>
				<tr>
					<td bgcolor='#ffffff'>
						<table border='0' width='100%' cellpadding='0' cellspacing='0'>
							<tr>
								<td>
									<table width='100%' border='0' cellpadding='0' cellspacing='0'>
										<tr height='25' bgcolor='#efefef' align=center>
											<td width='8%' class='s_td'><b>번호</b></td>
											<!--td width='10%' class='m_td'><b>상품코드</b></td-->
											<td width='*' colspan=2 class='m_td'><b>제품명</b></td>
											<td width='5%' class='m_td'><b>수량</b></td>
											<td width='20%' class='m_td'><b>옵션</b></td>
											<td width='10%' class='m_td'><b>비고</b></td>";
if($admininfo[mall_use_multishop]){
$mstring .=	"								<td width='7%' class='m_td'><b>공급가</b></td>";
$mstring .=	"								<td width='7%' class='m_td'><b>상태</b></td>";
}
$mstring .=	"								<td width='5%'  class='m_td'><b>단가</b></td>
											<!--td width='5%' class='m_td'><b>적립금</b></td-->
											<td width='10%' class='e_td'><b>합계</b></td>

										</tr>";

	$sql = "SELECT od.pid, od.pname, od.reserve, pcnt, psprice, ptprice, od.option_text, po.option_etc1, od.status , od.coprice
		FROM  ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_ORDER." o on o.oid = od.oid left outer join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po on od.option_id = po.id
		WHERE o.user_code = '".$ucode."' and od.status != 'SR' ";
	//echo $sql;

	$mdb->query($sql);

	$num = 1;
	$sum = 0;

	for($j = 0; $j < $mdb->total; $j++)
	{
		$mdb->fetch($j);

		$pname = $mdb->dt[pname];
		$pcode = $mdb->dt[pcode];
		$count = $mdb->dt[pcnt];
		$option_div = $mdb->dt[option_text];
		$option_etc1 = $mdb->dt[option_etc1];
		$price = $mdb->dt[psprice];
		$coprice = $mdb->dt[coprice];
		$sumptprice = $sumptprice + $mdb->dt[ptprice];


		$reserve = $mdb->dt[reserve];
		$ptotal = $price * $count;
		$sum += $ptotal;

		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $mdb->dt[pid], "c"))){
			$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $mdb->dt[pid], "c");
		}else{
			$img_str = "../image/no_img.gif";
		}

$mstring .= "
										<tr align='center'>
											<td >".$num."</td>
											<td ><!--".$pcode."--><img src=\"".$img_str."\" style='margin:3px 0px;'></td>
											<td ><div align='left' style='padding:5px 0 5px 0'><a href=\"/shop/goods_view.php?id=".$mdb->dt[pid]."\" target=_blank>".$pname."</a></div></td>
											<td >".$count." 개</td>
											<td align=left style='padding-left:5px;'>".$option_div."</td>
											<td align=center>".$option_etc1."</td>";
if($admininfo[mall_use_multishop]){
$mstring .= "					<td align=center>".number_format($mdb->dt[coprice])."</td>";
$mstring .= "					<td align=center>".getOrderStatus($mdb->dt[status])."</td>";
}
$mstring .= "					<td >".number_format($price)."</td>
											<!--td ><div align='right'>".number_format($coprice)."</div></td-->
											<!--td ><div align='right'>".number_format($reserve)."</div></td-->
											<td >".number_format($ptotal)."</td>
										</tr>
										<tr height=1><td colspan=8 background='../image/dot.gif'></td></tr>";

		$num++;
	}
$mstring = $mstring."
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>";

return $mstring;

}

function PrintMemberCouncelMemo($mdb, $ucode){
	global $max, $page, $start, $admininfo,$auth_delete_msg;


	$mdb->query("SELECT count(*) as total FROM shop_member_talk_history where ucode ='".$ucode."' ");
	$mdb->fetch();
	$total = $mdb->dt[total];

	$mdb->query("SELECT * FROM shop_member_talk_history where ucode ='".$ucode."' ORDER BY regdate DESC LIMIT $start, $max");

	$mstring = "<table width='100%' border=0 cellpadding=0 cellspacing=0>

		<tr height=25 align=center>
			<td class=s_td width=25% nowrap>상담자 </td>
			<td class=m_td width=60% nowrap>상담내용 </td>
			<td class=e_td width=15% nowrap>관리</td>
		</tr>
		";
	if(!$mdb->total){
		$mstring .= "<tr height=40 align=center>
					<td colspan=4 align=center>입력된 상담정보가 없습니다. </td>
				</tr>
				<tr height=1><td colspan=4 background='/img/dot.gif'></td></tr>";
	}else{
		for($i=0;$i < $mdb->total;$i++){
		$mdb->fetch($i);
		$mstring .= "<tr height=27 align=center>
				<td >".$mdb->dt[ta_counselor]."  </td>
				<td bgcolor=#efefef align=left style='padding:8px 0 8px 10px'>".nl2br($mdb->dt[ta_memo])."<br>

				<span class='small'>등록날짜 : ".$mdb->dt[regdate]."</span>
				</td>
				<td bgcolor=#ffffff align=center>";
                if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
                    $mstring.="
                    <a href=\"JavaScript:act('mem_talk_delete', '".$mdb->dt[ta_ix]."', '".$mdb->dt[m_code]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
                }else{
                    $mstring.="
                    <a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
                }    
                $mstring.="    
                </td>
			</tr>
			<tr height=1><td colspan=4 background='/img/dot.gif'></td></tr>";
		}
	}
	$mstring .= "
		<tr height=30><td colspan=4 align=right>".page_bar($total, $page, $max,"&code=$ucode","")."</td></tr>
	</table>";

	return $mstring;
}


function PrintModifylist($ucode){
global $admin_config,$DOCUMENT_ROOT;

$sdb = new Database;
$mdb = new Database;
$db = new Database;

$mstring = "
			<table border='0' width='100%' cellspacing='0' cellpadding='0'>
				<tr>
					<td bgcolor='#ffffff'>
						<table border='0' width='100%' cellpadding='0' cellspacing='0'>
							<tr>
								<td>
									<table width='100%' border='0' cellpadding='0' cellspacing='0'>
										<tr height='25' bgcolor='#efefef' align=center>
											<td width='30%' class='s_td'><b>변경날짜</b></td>
											<td width='*' class='m_td'><b>변경내용</b></td>
											<td width='25%' class='m_td'><b>수정자/ID</b></td>
										</tr>";

	$sql = "
			select
					*
				from
					common_member_edit_history as cmed
				
				where
					1
					and code = '".$ucode."'
					group by regdate
					order by cmed.regdate DESC 
					";

	$sdb->query($sql);

	for($j = 0; $j < $sdb->total; $j++)
	{
		$sdb->fetch($j);
	
		$edit_date = $sdb->dt[edit_date];
		$regdate = $sdb->dt[regdate];

		$sql = "select
				h.column_text,
				(select gp_name from shop_groupinfo where gp_ix = h.gp_ix) as gp_name
				from
					common_member_edit_history h
				where
					h.code  = '".$sdb->dt[code]."'
					and h.edit_date = '".$edit_date."'
					and h.regdate = '".$regdate."'";
	
		$mdb->query($sql);
		$history_text_array = $mdb->fetchall();
		for($i=0;$i<count($history_text_array);$i++){
			if($i == count($history_text_array)-1){
				if($history_text_array[$i][column_text] == "회원그룹"){
					$history_text .= $history_text_array[$i][column_text]."(".$history_text_array[$i][gp_name].")";
				} else {
					$history_text .= $history_text_array[$i][column_text];
				}
			}else{
				if($history_text_array[$i][column_text] == "회원그룹"){
					$history_text .= $history_text_array[$i][column_text]."(".$history_text_array[$i][gp_name].")".", ";
				} else {
					$history_text .= $history_text_array[$i][column_text].", ";
				}
			}
		}

if($sdb->dt[chager_ix]){
	$sql = "select id from common_user where code = '".$sdb->dt[chager_ix]."'";
	$db->query($sql);
	$db->fetch();	
	$chager_id = $db->dt[id];
}
$mstring .= "
				<tr align='center' height='25'>
					<td>".$sdb->dt[regdate]."</td>
					<td align=left style='padding-left:15px;'>".$history_text."</td>
					<td align=left style='padding-left:15px;'>".$sdb->dt[chager_name]."/".$chager_id."</td>
				<tr height=1><td colspan=3 background='../image/dot.gif'></td></tr>";
		unset($history_text);
	}
$mstring = $mstring."
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>";

return $mstring;

}

/*
CREATE TABLE IF NOT EXISTS `common_modify_history` (
  `ix` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `code` char(32) NOT NULL COMMENT '제목',
  `msg` mediumtext DEFAULT NULL COMMENT '내용',
  `changer` varchar(255) DEFAULT NULL COMMENT '수정한사람',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='회원정보변경히스토리' AUTO_INCREMENT=1 ;
*/



function PrintBlacklist($ucode){
global $admin_config,$DOCUMENT_ROOT;

$sdb = new Database;

$mstring = "
			<table border='0' width='100%' cellspacing='0' cellpadding='0'>
				<tr>
					<td bgcolor='#ffffff'>
						<table border='0' width='100%' cellpadding='0' cellspacing='0'>
							<tr>
								<td>
									<table width='100%' border='0' cellpadding='0' cellspacing='0'>
										<tr height='25' bgcolor='#efefef' align=center>
											<td width='30%' class='s_td'><b>날짜</b></td>
											<td width='20%' class='s_td'><b>분류</b></td>
											<td width='*' class='m_td'><b>변경내용</b></td>
										</tr>";


	$sql = "SELECT * FROM common_blacklist_history WHERE code = '".$ucode."' order by regdate desc ";
	//echo $sql;

	$sdb->query($sql);

	for($j = 0; $j < $sdb->total; $j++)
	{
		$sdb->fetch($j);

$mstring .= "
										<tr align='center' height='25'>
											<td>".$sdb->dt[regdate]."</td>
											<td>".($sdb->dt[type] =="R" ? '등록' : '해제' )."</td>
											<td align=left style='padding-left:15px;'>".$sdb->dt[msg]."</td>
										<tr height=1><td colspan=3 background='../image/dot.gif'></td></tr>";
	}
$mstring = $mstring."
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>";

return $mstring;

}

function PrintMbanklist($code){

	global $admin_config,$DOCUMENT_ROOT;
	$db = new Database;
	


	$sql = "select
				sum(if(state = '1',reserve,'0')) as total_mileage,
				sum(if(state = '1',reserve,'0')) as member_mileage,
				sum(if(state = '2',reserve,'0')) as use_mileage
			from
				shop_reserve
			where
				uid = '".$code."'";
				
	$db->query($sql);
	$db->fetch();
	
	$total_mileage = $db->dt[total_mileage] ? $total_mileage = $db->dt[total_mileage]:"0";
	$member_mileage = $total_mileage - $db->dt[use_mileage];
	$use_mileage = $db->dt[use_mileage] ? $use_mileage = $db->dt[use_mileage]:"0";

	$sql = "select
				sum(if(state = '1',reserve,'0')) as total_point,
				sum(if(state = '1',reserve,'0')) as member_point,
				sum(if(state = '2',reserve,'0')) as use_point
			from
				shop_point
			where
				uid = '".$code."'";
				
	$db->query($sql);
	$db->fetch();
	
	$total_point = $db->dt[total_point] ? $total_point = $db->dt[total_point]:"0";
	$member_point = $total_point - $db->dt[use_point];
	$use_point = $db->dt[use_point] ? $use_point = $db->dt[use_point]:"0";

	$mstring = "
			<table border='0' width='100%' cellspacing='0' cellpadding='0'>
			<tr>
				<td width='50%'>
				<br>
				<table border='0' width='100%' cellspacing='0' cellpadding='0'>
					<tr>
						<td style='border-bottom:2px solid #ffffff' width='50%' align='left'><img src='../images/dot_org.gif' align=absmiddle> <b>마일리지 정보</b></td>
					</tr>
				</table><br>
				<table border='0' width='80%' cellspacing='0' cellpadding='0' style='margin-left:10px;'>
					<tr heidt='30'>
						<td width='40'>
						- 누적 마일리지 : 
						</td>
						<td width='30'>
							".number_format($total_mileage)." 점
						</td>
					</tr>
					<tr heidt='30'>
						<td width='40'>
						- 보유 마일리지 : 
						</td>
						<td width='30'>
							".number_format($member_mileage)." 점
						</td>
					</tr>
					<tr heidt='30'>
						<td width='40'>
						- 사용 마일리지 : 
						</td>
						<td width='30'>
							".number_format($use_mileage)." 점
						</td>
					</tr>
				</table><br>
				</td>

				<td width='50%'>
				<table border='0' width='100%' cellspacing='0' cellpadding='0'>
					<tr>
						<td style='border-bottom:2px solid #ffffff' width='50%' align='left'><img src='../images/dot_org.gif' align=absmiddle> <b>포인트 정보</b></td>
					</tr>
				</table><br>
				<table border='0' width='80%' cellspacing='0' cellpadding='0' style='margin-left:10px;'>
					<tr heidt='30'>
						<td width='30'>
						- 누적 포인트 : 
						</td>
						<td width='30'>
							".number_format($total_point)." 점
						</td>
					</tr>
					<tr heidt='30'>
						<td width='30'>
						- 보유 포인트 : 
						</td>
						<td width='30'>
							".number_format($member_point)." 점
						</td>
					</tr>
					<tr heidt='30'>
						<td width='30'>
						- 사용 포인트 : 
						</td>
						<td width='30'>
							".number_format($use_point)." 점
						</td>
					</tr>
				</table><br>
				</td>
			</tr>
		</table>
				";

	$max = 10; //페이지당 갯수

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}

	if($db->dbms_type == "oracle"){
		$sql="SELECT COUNT(bank_ix) AS cnt FROM shop_user_bankinfo WHERE ucode='".$code."' ";
	}else{
		$sql="SELECT COUNT(bank_ix) AS cnt FROM shop_user_bankinfo WHERE ucode='".$code."' ";
	}

	$db->query($sql);
	$db->fetch();

	$total = $db->dt["cnt"];

	$no = $total - ($page - 1) * $max;

	if($db->dbms_type == "oracle"){
		$sql="SELECT bank_ix, ucode, bank_code, use_yn, is_basic, regdate, editdate,
					AES_DECRYPT(UNHEX(bank_name),'".$db->ase_encrypt_key."') as bank_name,
					AES_DECRYPT(UNHEX(bank_number),'".$db->ase_encrypt_key."') as bank_number,
					AES_DECRYPT(UNHEX(bank_owner),'".$db->ase_encrypt_key."') as bank_owner,
					$no AS no 
				FROM shop_user_bankinfo WHERE ucode='".$code."' ORDER BY regdate DESC LIMIT $start, $max ";
	}else{
		$sql="SELECT bank_ix, ucode, bank_code, use_yn, is_basic, regdate, editdate,
					AES_DECRYPT(UNHEX(bank_name),'".$db->ase_encrypt_key."') as bank_name,
					AES_DECRYPT(UNHEX(bank_number),'".$db->ase_encrypt_key."') as bank_number,
					AES_DECRYPT(UNHEX(bank_owner),'".$db->ase_encrypt_key."') as bank_owner,
					$no AS no 
				FROM shop_user_bankinfo WHERE ucode='".$code."' ORDER BY regdate DESC LIMIT $start, $max ";
	}

	$db->query($sql);

	$mstring .= "
			<table border='0' width='100%' cellspacing='0' cellpadding='0'>
				<tr>
					<td bgcolor='#ffffff'>
						<table border='0' width='100%' cellpadding='0' cellspacing='0'>
							<tr>
								<td>
									<table width='100%' border='0' cellpadding='0' cellspacing='0'>
										<tr height='25' bgcolor='#efefef' align=center>
											<td width='20' class='s_td'><b>No.</b></td>
											<td width='20%' class='s_td'><b>은행명</b></td>
											<td width='*' class='m_td'><b>예금주</b></td>
											<td width='20%' class='s_td'><b>계좌번호</b></td>
											<td width='20%' class='s_td'><b>사용여부</b></td>
										</tr>";
			if($db->total) {
			for($i=0;$i  < $db->total; $i++){
				$db->fetch($i);
				$mstring .= "<tr height=28 align=center>
							<td bgcolor='#fbfbfb'>".($db->dt[no]-$i)."</td>
							<td bgcolor='#fbfbfb'>".$db->dt[bank_name]."</td>
							<td bgcolor='#fbfbfb'>".$db->dt[bank_owner]."</td>
							<td bgcolor='#fbfbfb'>".$db->dt[bank_number]."</td>
							<td bgcolor='#fbfbfb'>".($db->dt[use_yn]=="Y"?"사용":"미사용")."</td>
							</tr>";
			}
					$mstring .= "<tr height=40><td colspan=6 align=center>".page_bar($total, $page, $max,"&info_type=$info_type&mmode=$mmode&code=$code","")."</td></tr>";
			} else {
					$mstring .= "<tr height=60><td colspan=6 align=center>등록된 정보가 없습니다.</td></tr>";
			}
	$mstring = $mstring."
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>";

return $mstring;
}


function PrintRaddrlist($code){

	global $admin_config,$DOCUMENT_ROOT,$_REQUEST;
	$db = new Database;

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$sql = "SELECT count(ix) as cnt FROM shop_shipping_address WHERE mem_ix = '".$code."'";

	$db->query($sql);
	$db->fetch();

	$total = $db->dt["cnt"];
	$no = $total - ($page - 1) * $max;

	$sql = "SELECT *,$no AS no FROM shop_shipping_address WHERE mem_ix = '".$code."' LIMIT $start, $max ";
	$db->query($sql);
	$db->fetch();

	$mstring .= "
			<table border='0' width='100%' cellspacing='0' cellpadding='0'>
				<tr>
					<td bgcolor='#ffffff'>
						<table border='0' width='100%' cellpadding='0' cellspacing='0'>
							<tr>
								<td>
									<table width='100%' border='0' cellpadding='0' cellspacing='0'>
										<tr height='25' bgcolor='#efefef' align=center>
											<td width='10' class='s_td'><b>번호</b></td>
											<td width='15%' class='m_td'><b>배송지명</b></td>
											<td width='12%' class='m_td'><b>받는사람</b></td>
											<td width='18%' class='m_td'><b>핸드폰번호</b></td>
											<td width='35%' class='m_td'><b>배송지주소</b></td>
											<td width='15%' class='e_td'><b>상태</b></td>
										</tr>";
					if($db->total) {
						for($i=0;$i  < $db->total; $i++){
							$db->fetch($i);
							$mstring .= "<tr height=28 align=center>
										<td bgcolor='#fbfbfb'>".($db->dt[no]-$i)."</td>
										<td bgcolor='#fbfbfb'>".$db->dt[shipping_name]."</td>
										<td bgcolor='#fbfbfb'>".$db->dt[recipient]."</td>
										<td bgcolor='#fbfbfb'>".$db->dt[mobile]."</td>
										<td bgcolor='#fbfbfb'>".$db->dt[address1]."<br>".$db->dt[address2]."</td>
										<td bgcolor='#fbfbfb'>".($db->dt[default_yn]=="Y"?"기본배송지":"-")."</td>
										</tr>";
						}
							$mstring .= "<tr height=40><td colspan=6 align=center>".page_bar($total, $page, $max,"&info_type=$info_type&mmode=$mmode&code=$code","")."</td></tr>";
					} else {
							$mstring .= "<tr height=60><td colspan=6 align=center>등록된 정보가 없습니다.</td></tr>";
					}
	$mstring = $mstring."
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>";

return $mstring;
}


function PrintCouphonlist($code){

	global $admin_config,$DOCUMENT_ROOT,$_REQUEST,$admininfo,$page;
	$db = new Database;

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	if($status=='o' || $status==""){
		//$status_str = "and (use_yn = '0' and (use_yn = '0' && (cr.use_date_limit >= ".date("Ymd")." ))) ";
		$status_str = "and ((cp.use_date_type!='9') OR cp.use_date_type=9)";//kbk 12/01/19
	}else if($status=='x'){
		//$status_str = "and (use_yn = '1' or (use_yn = '0' && cr.use_date_limit < ".date("Ymd").")) ";
		$status_str = "and (use_yn = '1' or ( cp.use_date_type!='9')) ";
	}

	if($_REQUEST[cupon_div]){
		$status_str .= " and c.cupon_div = '".$_REQUEST[cupon_div]."' ";
	}

	if($_REQUEST[use_yn]){
		$status_str .= " and cr.use_yn = '".$_REQUEST[use_yn]."' ";
	}

	$sql = 	"SELECT
				count(*) as total
			FROM 
				".TBL_SHOP_CUPON_PUBLISH." cp , 
				".TBL_SHOP_CUPON_REGIST." cr ,
				".TBL_COMMON_USER." cu , 
				".TBL_COMMON_MEMBER_DETAIL." cmd ,
				".TBL_SHOP_CUPON." c
			where 
				cr.publish_ix = cp.publish_ix 
				and cu.code = cr.mem_ix 
				and cu.code = cmd.code 
				and c.cupon_ix = cp.cupon_ix
				and cr.mem_ix = '".$code."' 
				$status_str $where";
	$db->query($sql);
	$db->fetch();

	$total = $db->dt[total];
	$no = $total - ($page - 1) * $max;

	$sql = 	"SELECT 
				$no as no, cp.cupon_no, cp.cupon_ix, cp.use_date_type,
				cr.regdate, cr.regist_ix, c.cupon_kind, cmd.name as mem_name,
				cu.id as mem_id, cr.use_sdate,cp.use_edate,
				cr.use_date_limit as use_date_limit, cr.use_yn , 
				case when use_yn = 1 then '사용완료' else '사용가능' end as use_yn_text ,
				cp.publish_condition_price,cp.publish_limit_price,
				cp.use_product_type,c.cupon_div,c.cupon_sale_value,
				c.cupon_sale_type,cp.publish_name
			FROM 
				".TBL_SHOP_CUPON_PUBLISH." cp ,
				".TBL_SHOP_CUPON_REGIST." cr , 
				".TBL_COMMON_USER." cu , 
				".TBL_COMMON_MEMBER_DETAIL." cmd , 
				".TBL_SHOP_CUPON." c
			where 
				cr.publish_ix = cp.publish_ix
				and cu.code = cr.mem_ix 
				and cu.code = cmd.code 
				and c.cupon_ix = cp.cupon_ix
				and cr.mem_ix = '".$code."'
				$status_str $where
				order by cr.regdate desc LIMIT $start,$max ";

	$db->query($sql);
	
	$mstring .= "
			
			<table border='0' width='100%' cellspacing='0' cellpadding='0'>
				<tr>
					<td style='border-bottom:2px solid #ffffff' width='50%' align='left'><img src='../images/dot_org.gif' align=absmiddle> <b>쿠폰 정보</b></td>
				</tr>
			</table><br>
			<form name=searchmember method='get'>
			<input type='hidden' name='code' value='".$code."'>
			<input type='hidden' name='mmode' value='pop'>
			<input type='hidden' name='info_type' value='cupon_list'>
			<table border='0' width='100%' cellspacing='0' cellpadding='0' class='search_table_box'>
				<colgroup>
					<col width='20%'>
					<col width='*'>
				</colgroup>
				<tr>
					<td class='search_box_title'>
						사용범위
					</td>
					<td class='search_box_item'>
						<input type='radio' value='' name='cupon_div' id='use_product_type_a' ".CompareReturnValue("",$_REQUEST[cupon_div],"checked")." > <label for='use_product_type_a'>전체</lable> 
						<input type='radio' value='P' name='cupon_div' id='use_product_type_P' ".CompareReturnValue("P",$_REQUEST[cupon_div],"checked")."> <label for='use_product_type_P'> 상품구매할인 </lable> 
						<input type='radio' value='D' name='cupon_div' id='use_product_type_D' ".CompareReturnValue("D",$_REQUEST[cupon_div],"checked")."> <label for='use_product_type_D'> 배송비할인 </lable> 
						<input type='radio' value='R' name='cupon_div' id='use_product_type_R' ".CompareReturnValue("R",$_REQUEST[cupon_div],"checked")."> <label for='use_product_type_R'> 적립금쿠폰 </lable> 
					</td>
				</tr>
				<tr>
					<td class='search_box_title'>
						사용유무
					</td>
					<td class='search_box_item'>
						<input type='radio' value='' name='use_yn' id='use_yn_a' ".CompareReturnValue("",$_REQUEST[use_yn],"checked")."> <label for='use_yn_a'>전체</lable> 
						<input type='radio' value='0' name='use_yn' id='use_yn_0' ".CompareReturnValue("0",$_REQUEST[use_yn],"checked")."> <label for='use_yn_0'> 사용가능 </lable> 
						<input type='radio' value='1' name='use_yn' id='use_yn_1' ".CompareReturnValue("1",$_REQUEST[use_yn],"checked")."> <label for='use_yn_1'> 기간만료 </lable> 
					</td>
				</tr>
			</table><br>
			<table border='0' width='100%' cellspacing='0' cellpadding='0'>
				<tr height=40>
					<td style='padding:10px 20px 0 20px' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
				</tr>
			</table><br></form><br>";
	$mstring .= "
			<table border='0' width='100%' cellspacing='0' cellpadding='0' >
				<tr>
					<td bgcolor='#ffffff'>
						<table border='0' width='100%' cellpadding='0' cellspacing='0'>
							<tr>
								<td>
									<table width='100%' border='0' cellpadding='0' cellspacing='0' class='list_table_box'>
										<tr height='25' bgcolor='#efefef' align=center>
											<td width='20' class='s_td'><b>번호</b></td>
											<td width='19%' class='m_td'><b>쿠폰명</b></td>
											<td width='17%' class='m_td'><b>제한조건</b></td>
											<td width='14%' class='m_td'><b>사용범위</b></td>
											<td width='12%' class='m_td'><b>할인율(액)</b></td>
											<td width='10%' class='m_td'><b>유효기간</b></td>
											<td width='10%' class='m_td'><b>사용유무</b></td>
											<td width='10%' class='e_td'><b>적용대상</b></td>
										</tr>";
					if($db->total) {
						for($i=0;$i  < $db->total; $i++){
							$db->fetch($i);

							if($db->dt[use_date_type] != '9'){
								
								if($db->dt[use_sdate] <= date("Ymd") && date("Ymd") <= $db->dt[use_date_limit]){
									$disp_use = $db->dt[use_yn_text];
								}else if($db->dt[use_sdate] >  date("Ymd")){
									$disp_use ="사용대기";
								}else{
									$disp_use ="기간만료";
								}
							}

							$mstring .= "<tr height=28 align=center>
										<td bgcolor='#fbfbfb' class='list_box_td'>".($db->dt[no]-$i)."</td>
										<td bgcolor='#fbfbfb' class='list_box_td'>".$db->dt[publish_name]."</td>

										<td bgcolor='#fbfbfb' class='list_box_td'>".($db->dt[publish_condition_price] > 0 ? number_format($db->dt[publish_condition_price]).' 이상 구매시':' 제한조건 없음')."<br>".($db->dt[publish_limit_price] > 0 ? '최대'.number_format($db->dt[publish_limit_price]).'원 할인':'')."</td>

										<td bgcolor='#fbfbfb' class='list_box_td'>".($db->dt[cupon_div]=='P'?"상품구매할인":"배송비할인")."</td>
										<td bgcolor='#fbfbfb' class='list_box_td'>".number_format($db->dt[cupon_sale_value]).($db->dt[cupon_sale_type]=='1'?'%':'원')."</td>
										<td bgcolor='#fbfbfb' class='list_box_td'>".($db->dt[use_date_type] !='9'?ChangeDate($db->dt[use_sdate],"Y/m/d").' ~ '.ChangeDate($db->dt[use_date_limit],"Y/m/d"):'기간제한없음')."</td>";
		
							$mstring .= "
										<td bgcolor='#fbfbfb' class='list_box_td'>".($db->dt[use_date_type] != '9'?$disp_use:$db->dt[use_yn_text])."</td>";

						if($db->dt[use_product_type] == '1'){
							$mstring .= "
										<td bgcolor='#fbfbfb' class='list_box_td'>전체상품</td>";
						}else if($db->dt[use_product_type] == '4'){
							$mstring .= "
										<td bgcolor='#fbfbfb' class='list_box_td'> 특정브랜드 <a href='javascript:void(0);' onclick=\"window.open('/admin/member/couphon_apply_pop.php?cupon_no=".$db->dt[cupon_no]."', 'couphon_apply_pop', 'width=520,height=620,scrollbars=yes,status=no');\">
											<img src='/data/arounz_data/templet/stylestory/images/btns/apply_btn.gif' alt='적용범위확인' title='적용범위확인' />
										</a></td>";
						}else if($db->dt[use_product_type] == '2'){
							$mstring .= "
										<td bgcolor='#fbfbfb' class='list_box_td'> 카테고리 
										<a href='javascript:void(0);' onclick=\"window.open('/admin/member/couphon_apply_pop.php?cupon_no=".$db->dt[cupon_no]."', 'couphon_apply_pop', 'width=520,height=620,scrollbars=yes,status=no');\">
											<img src='/data/arounz_data/templet/stylestory/images/btns/apply_btn.gif' alt='적용범위확인' title='적용범위확인' />
										</a></td>";
						}else if($db->dt[use_product_type] == '3'){
							$mstring .= "
										<td bgcolor='#fbfbfb' class='list_box_td'> 특정상품 
										<a href='javascript:void(0);' onclick=\"window.open('/admin/member/couphon_apply_pop.php?cupon_no=".$db->dt[cupon_no]."', 'couphon_apply_pop', 'width=520,height=620,scrollbars=yes,status=no');\">
											<img src='/data/arounz_data/templet/stylestory/images/btns/apply_btn.gif' alt='적용범위확인' title='적용범위확인' />
										</a></td>";
						}
							$mstring .= "
										</tr>";
						}
							$mstring .= "<tr height=40><td colspan=8 align=center>".page_bar($total, $page, $max,"&info_type=cupon_list&mmode=pop&code=$code&cupon_div=$cupon_div&use_yn=$use_yn","")."</td></tr>";
					} else {
							$mstring .= "<tr height=60><td colspan=8 align=center>등록된 정보가 없습니다.</td></tr>";
					}
	$mstring = $mstring."
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>";

return $mstring;

}

/*
CREATE TABLE IF NOT EXISTS `common_blacklist_history` (
  `ix` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `type` char(1) NOT NULL COMMENT 'R:등록C:해제',
  `code` char(32) NOT NULL COMMENT '제목',
  `msg` mediumtext DEFAULT NULL COMMENT '내용',
  `changer` varchar(255) DEFAULT NULL COMMENT '수정한사람',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='블랙리스트히스토리' AUTO_INCREMENT=1 ;
*/


?>



