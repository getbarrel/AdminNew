<?
ini_set('include_path', ".:/usr/local/lib/php:".$_SERVER["DOCUMENT_ROOT"]."/include/pear");
include("../class/layout.class");
$install_path = "../../include/";
include("SOAP/Client.php");


$soapclient = new SOAP_Client("http://www.mallstory.com/admin/service/api/");
// server.php 의 namespace 와 일치해야함
$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

$service_detail = (array)$soapclient->call("getServiceDetailInfo",$params = array("service_div"=> $_GET["service_div"],"solution_div"=> $_GET["solution_div"],"mall_domain"=> $_SERVER["HTTP_HOST"],"company_id"=> $admininfo[mall_domain_id], "mall_domain_key"=> $mall_domain_key),	$options);

//print_r($service_detail);
$service_info = (array)$service_detail[service_info];
$options_info = (array)$service_detail[option_info];
//print_r($service_info);

$Script = "
<script language='javascript' src='service_apply.js'></script>
$pg_script
<script type='text/javascript'>
StartSmartUpdate();
</script>";

$Script .= "
<style>

input {border:1px solid #c6c6c6;padding:3px;}
.member_table td {text-align:left;}
</style>";

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2 valign=top>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("회원정보 수정", "마이서비스 > 회원정보 수정", false)."</td>
			</tr>
			<tr height=30><td class='p11 ls1' style='padding:0 0 0 15px;text-align:left;' ><img src='../image/title_head.gif' align=absmiddle>  <b class=blk>".$service_info[sv_name]." > ".$service_info[sp_name]."</b> SSL 신청정보 입력 </td></tr>
			<tr>
				<td align=center style='padding: 0 10px 0 10px;vertical-align:top'>

				     <form name='EDIT_".$db->dt[code]."' action='service_apply.act.php' method='post' onsubmit='return CheckFormValue(this)' >
					  <input type='hidden' name='act' value='ssl_apply'>
					  <input type='hidden' name='si_ix' value='".$_GET[si_ix]."'>
					  <input type='hidden' name='oid' value='".$payment_oid."'>
					  <input type='hidden' name='service_div' value='".$_GET[service_div]."'>
					  <input type='hidden' name='solution_div' value='".$_GET[solution_div]."'>
					  <input type='hidden' name='sp_name' value='".$service_info[sp_name]."'>
					  <input type='hidden' name='company_id' value='".$db->dt[company_id]."'>
					  <input type='hidden' name='mall_domain_key' value='".$db->dt[mall_domain_key]."'>

					  
					  <table border='0' cellspacing='1' cellpadding='5' width='100%'>
						<tr>
							<td align='left'>
								<span style='font-size:11px; font-weight:bold; padding-left:5px;'>CSR생성 정보(도메인 소유회사)</span>
							</td>
							<td align='right'>
								<span style='color:red; font-size:11px'>(본항목은 필히 영문으로 작성)</span>
							</td>
						</tr>
						<tr>
							<td bgcolor='#F8F9FA' colspan='2'>

								<table border='0' width='100%' cellspacing='1' cellpadding='0'  class='member_table input_table_box' >
								<col width = '170'>
								<col width = '*'>
									<tr>
										<td class='input_box_title' nowrap> 
											Status (시/도)
										</td>
										
										<td class='input_box_item' style='padding:0px 10px;'>
											<input type='text' name='' value='' title='' style='width:100%;'>
										</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 
											Locality (구/군)
										</td>
										
										<td class='input_box_item' style='padding:0px 10px;'>
											<input type='text' name='' value='' title='' style='width:100%;'>
										</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 
											Organization <br/>(회사명, Full Name으로)
										</td>
										
										<td class='input_box_item' style='padding:0px 10px;'>
											<input type='text' name='' value='' title='' style='width:100%;'>
										</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 
											Organization Unit<br/>(부서명)
										</td>
										
										<td class='input_box_item' style='padding:0px 10px;'>
											<input type='text' name='' value='' title='' style='width:100%;'>
										</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 
											Common Name <br/>(신청 URL)
										</td>
										
										<td class='input_box_item' style='padding:0px 10px;'>
											<input type='text' name='' value='' title='' style='width:100%;'>
										</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 
											인증서종류/년형
										</td>
										
										<td class='input_box_item' style='padding:0px 10px;'>
											<input type='radio' name='kisa' value='' id='kisa_1' title='' style='border:0px;'><label for='kisa_1'>Kisa 1년형</label> 
											<input type='radio' name='kisa' value='' id='kisa_2' title='' style='border:0px;'><label for='kisa_2'>Kisa 2년형</label> 
											<input type='radio' name='kisa' value='' id='kisa_3' title='' style='border:0px;'><label for='kisa_3'>Kisa 3년형</label> 
										</td>
									</tr>
								</table>
							</td>	
						</tr>
						<tr>
							<td>
							 
							</td>
						</tr>
						<tr>
							<td align='left'>
								<span style='font-size:11px; font-weight:bold; padding-left:5px;'>서비스 담당자 정보(도메인)</span>
							</td>
							<td align='right'>
								<span style='color:red; font-size:11px'>(본 항목은 국문으로 작성 가능)</span>
							</td>
						</tr>	
						<tr>
							<td bgcolor='#F8F9FA' colspan='2'>

								<table border='0' width='100%' cellspacing='1' cellpadding='0'  class='member_table input_table_box' >
								<col width = '170'>
								<col width = '*'>
									<tr>
										<td class='input_box_title' nowrap> 
											회사명
										</td>
										
										<td class='input_box_item' style='padding:0px 10px;'>
											<input type='text' name='' value='' title='' style='width:100%;'>
										</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 
											부서명
										</td>
										
										<td class='input_box_item' style='padding:0px 10px;'>
											<input type='text' name='' value='' title='' style='width:100%;'>
										</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 
											담당자명
										</td>
										
										<td class='input_box_item' style='padding:0px 10px;'>
											<input type='text' name='' value='' title='' style='width:100%;'>
										</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 
											직함
										</td>
										
										<td class='input_box_item' style='padding:0px 10px;'>
											<input type='text' name='' value='' title='' style='width:100%;'>
										</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 
											회사주소
										</td>
										
										<td class='input_box_item' style='padding:0px 10px;'>
											<input type='text' name='' value='' title='' style='width:100%;'>
										</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 
											전화번호
										</td>
										
										<td class='input_box_item' style='padding:0px 10px;'>
											<input type='text' name='' value='' title='' style='width:100%;'>
										</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 
											E-mail
										</td>
										
										<td class='input_box_item' style='padding:0px 10px;'>
											<input type='text' name='' value='' title='' style='width:100%;'>
										</td>
									</tr>
								</table>
							</td>	
						</tr>
					 </table>
					 <table width='100%' border='0'>
					<tr>
						<td align='center' style='padding:20px 0px;'>
						<table>
							<tr>
								<td>";
								if(strpos($_SERVER["HTTP_USER_AGENT"],"MSIE")){								
								$Contents .="<input type=image src='../images/".$admininfo["language"]."/bts_ok.gif' border=0 style='cursor:pointer;border:0px;'>";
								}else{
								$Contents .="<img src='../images/".$admininfo["language"]."/bts_ok.gif' onClick=\"alert('죄송합니다.\\n결제는 익스플로어에서만 가능합니다.');\" border=0 style='cursor:pointer;border:0px;'>";
								}
								$Contents .="
								</td>
								<td><img src='../images/".$admininfo["language"]."/btn_close.gif' border=0 onClick='self.close();' style='cursor:pointer;'></td>
							</tr>
						</table>
						</td>
					</tr>
				</table>
					</form>
				</td>		
			</tr>
		</table>
		</td>
	 </tr>
</table>
	

";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "SSL 신청정보 입력";
$P->NaviTitle = "SSL 신청정보 입력";
$P->title = "SSL 신청정보 입력";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>
