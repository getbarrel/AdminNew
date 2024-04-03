<?php


$Contents.="
<table width='100%'>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation("계좌 거래내역", "")."</td>
	</tr>
	<tr>
	    <td align='left' colspan=4 style='padding-bottom:15px;'>
	    	<div class='tab'>
				<table class='s_org_tab' style='width:100%'>
					<tr>
						<td class='tab'>
							<table id='tab_01' ".(substr_count($_SERVER["REQUEST_URI"],'list.transaction.php') > 0 ? "class='on'" : "" ).">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='list.transaction.php'\">
										거래내역/출력
									</td>
									<th class='box_03'></th>
								</tr>
							</table>";
                            if(substr_count($_SESSION['admininfo']['admin_id'],'forbiz') > 0) {
                                $Contents .= "
							<table id='tab_02' " . (substr_count($_SERVER["REQUEST_URI"], 'list.account.php') > 0 ? "class='on'" : "") . ">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='list.account.php'\">
										계좌정보
									</td>
									<th class='box_03'></th>
								</tr>
							</table>";
                            }
                            $Contents.="
							<!--
							<table id='tab_03' >
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='list.account.php;\">
										서비스 신청
									</td>
									<th class='box_03'></th>
								</tr>
							</table>
							-->
						</td>
					</tr>
				</table>
			</div>
	    </td>
	</tr>
</table>
<div id='div_useradd' style='display:none; position:absolute; top:200px; left:700px; z-index:99; border:3px solid #000000; background:#FFFFFF'>
	<form name='addForm' action='bankda.act.php' method='post' target='act'>
	<input type='hidden' name='accea' value='1'>
	<input type='' name='mall_ix' value='".$admininfo[mall_ix]."'>
	<input type='' name='mall_div' value='".$admininfo[mall_div]."'>
	<input type='' name='mall_domain' value='".$admin_config[mall_domain]."'>
	<input type='hidden' id='act' name='act' value='addUser'>
		<table cellpadding='1' cellspacing='1' border='0' width='500' class='list_table_box'>
		<col width='35%'>
		<col width='65%'>
		<tr height='28'>
			<td class='m_td'>이용자 ID</td>
			<td class='search_box_item'><input type='text' class='textbox' name='user_id' ></td>
		</tr>
		<tr height='28'>
			<td class='m_td'>이용자 비밀번호</td>
			<td class='search_box_item'><input type='password' class='textbox' name='user_pw' ></td>
		</tr>
		<tr height='28'>
			<td class='m_td'>이용자이름(업체명)</td>
			<td class='search_box_item'><input type='text' class='textbox' name='user_name' ></td>
		</tr>
		<tr height='28'>
			<td class='m_td'>전화번호</td>
			<td class='search_box_item'><input type='text' class='textbox' name='user_tel' ></td>
		</tr>
		<tr height='28'>
			<td class='m_td'>이메일주소</td>
			<td class='search_box_item'><input type='text' class='textbox' name='user_email' ></td>
		</tr>
		</table>
		<table width='500' cellpadding=0 cellspacing=0 border='0' align='left' style='float:left;'>
		<tr><td align='center' style='padding:10px 0px;' ><a href='#;' onclick='FnUserAddWrite()'><img src='../../images/".$admininfo["language"]."/btn_reg.gif' align=absmiddle ></a></td></tr>
		</table>
	</form>
</div>

<div id='div_serviceadd' style='display:none; position:absolute; top:200px; left:700px; z-index:99; border:3px solid #000000; background:#FFFFFF'>
	<form name='addForm2' action='bankda.act.php' method='post' target='act'>
	<input type='hidden' id='act' name='act' value='addService'>
		<table cellpadding='1' cellspacing='1' border='0' width='500' class='list_table_box'>
		<col width='35%'>
		<col width='65%'>
		<tr height='28'>
			<td class='m_td'>신청계좌수</td>
			<td class='search_box_item'><input type='text' class='textbox' name='req_accea' value='".$bankda_array[service_unit_value]."' readonly ></td>
		</tr>
		<tr height='28'>
			<td class='m_td'>신청기간(월)</td>
			<td class='search_box_item'><input type='text' class='textbox' name='req_month' value='".$bankda_array[priod]."' readonly ></td>
		</tr>
		</table>
		<table width='500' cellpadding=0 cellspacing=0 border='0' align='left' style='float:left;'>
		<tr><td align='center' style='padding:10px 0px;' ><input type='image' src='../../images/".$admininfo["language"]."/btn_reg.gif' align=absmiddle ></td></tr>
		</table>
	</form>
</div>

<div id='div_servicedrop' style='display:none; position:absolute; top:200px; left:700px; z-index:99; border:3px solid #000000; background:#FFFFFF'>
	<form name='addForm3' action='bankda.act.php' method='post' target='act'>
	<input type='hidden' id='act' name='act' value='dropUser'>
		<table cellpadding='1' cellspacing='1' border='0' width='500' class='list_table_box'>
		<col width='35%'>
		<col width='65%'>
		<tr height='28'>
			<td class='m_td'>서비스계정</td>
			<td class='search_box_item'><input type='text' class='textbox' name='user_id' value='".$UserInfo[bankda_userid]."' readonly ></td>
		</tr>
		<tr height='28'>
			<td class='m_td'>계정비밀번호</td>
			<td class='search_box_item'><input type='text' class='textbox' name='user_pw' value='".$UserInfo[bankda_userpw]."' readonly ></td>
		</tr>
		</table>
		<table width='500' cellpadding=0 cellspacing=0 border='0' align='left' style='float:left;'>
		<tr><td align='center' style='padding:10px 0px;' ><input type='image' src='../../images/".$admininfo["language"]."/btn_reg.gif' align=absmiddle ></td></tr>
		</table>
	</form>
</div>

";
