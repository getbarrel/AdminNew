<?
include("../class/layout.class");


$Script = "<script language='JavaScript'>

	function acting(act,code){
		if(confirm('수정하시겠습니까?')){
			
			if (act == 'update')
			{
				var form = eval('document.EDIT_'+code);
			
				form.action = 'personal_rule.act.php?act='+act+'&code='+code;
				form.submit();
			}
		}
	}

</Script>";

/*<style>

input {border:1px solid #c6c6c6;padding:3px;}
.member_table td {text-align:left;}
</style>";*/


$db = new Database;



$sql = "SELECT code, AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name, rp.* FROM ".TBL_COMMON_MEMBER_DETAIL." cmd  left join reseller_policy rp on cmd.code = rp.rsl_code
		where cmd.code = '$code' ";
$db->query($sql);
$db->fetch();

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2 valign=top>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("개인별인센티브설정 수정", "리셀러관리 > 개인별인센티브설정 수정", false)."</td>
			</tr>
			<tr height=30><td class='p11 ls1' style='padding:0 0 0 20px;text-align:left;' > <b>".$db->dt[name]."</b> 님의 회원정보 입니다.</td></tr>
			<tr>
				<td align=center style='padding: 0 10px 0 10px;height:369px;vertical-align:top'>

				      <form name='EDIT_".$db->dt[code]."' method='post' onsubmit='return'>
					  <table border='0' cellspacing='1' cellpadding='5' width='100%'>
						<tr>
						  <td bgcolor='#F8F9FA'>
			<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
			<col width='27%' />
			<col width='*' />
				<tr>
					<td align='left' colspan='2''> ".GetTitleNavigation("리셀러인센티브 설정", "리셀러관리 > 개인별인센티브설정 수정")."</td>
				</tr>
				<tr>
					<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>리셀러 인센티브 설정</b></div>")."</td>
				</tr>
			</table>
			<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
				<col width='32%' />
				<col width='*' />
				<tr bgcolor='#ffffff'>
					<td class='input_box_title'> <b>리셀러 승인여부<img src='".$required3_path."'></b></td>
					<td class='input_box_item'>
					<input type='radio' name='rsl_ok' id='rsl_ok_n' value='n' ".($db->dt[rsl_ok] =="n" ? "checked":"").">
					<label for='rsl_ok_n'>사용안함</label>
					<input type='radio' name='rsl_ok' id='rsl_ok_y'  value='y' ".($db->dt[rsl_ok] == "y" ? "checked":"").">
					<label for='rsl_ok_y'>사용</label> 
					</td>
				</tr>
				<!--tr bgcolor='#ffffff'>
					<td class='input_box_title'> <b>추천인 사용여부<img src='".$required3_path."'></b></td>
					<td class='input_box_item'>
					<input type=radio name='rec_incentive_type' id='rec_incentive_1' value='1' ".($db->dt[rec_incentive_type] == "1" ? "checked":"")." onclick=\"document.getElementById('rec_incentive_after').setAttribute('validation','false');document.getElementById('rec_incentive').setAttribute('validation','false');\">
					<label for='rec_incentive_1'>사용안함</label>
					<input type=radio name='rec_incentive_type' id='rec_incentive_2' value='2' ".($db->dt[rec_incentive_type] == "2" ? "checked":"")." onclick=\"document.getElementById('rec_incentive_after').setAttribute('validation','false');document.getElementById('rec_incentive').setAttribute('validation','true');\">
					<label for='rec_incentive_2'>사용함</label>
					<input type=text class='textbox' name='rec_incentive' value='".$db->dt[rec_incentive]."' style='width:60px;' id='rec_incentive' ".($db->dt[rec_incentive_type] == "2" ? "validation='true'":"validation='false'")." title='추천인 사용시 인센티브'> 원
					<input type=radio name='rec_incentive_type' id='rec_incentive_3' value='3' ".($db->dt[rec_incentive_type] == "3" ? "checked":"")." onclick=\"document.getElementById('rec_incentive_after').setAttribute('validation','true');document.getElementById('rec_incentive').setAttribute('validation','false');\">
					<label for='rec_incentive_3'>첫 구매시</label>
					<input type=text class='textbox' name='rec_incentive_after' value='".$db->dt[rec_incentive_after]."' style='width:60px;' id='rec_incentive_after' ".($db->dt[rec_incentive_type] == "3" ? "validation='true'":"validation='false'")." title='추천인첫구매 사용시 인센티브'> 원 
					</td>
				</tr-->
				<tr bgcolor='#ffffff'>
					<td class='input_box_title'> <b>신규회원 유도 인센티브<img src='".$required3_path."'></b></td>
					<td class='input_box_item'>
					<input type=radio name='new_incentive_type' id='new_incentive_1' value='1' ".($db->dt[new_incentive_type] == "1" ? "checked":"")." onclick=\"document.getElementById('new_incentive').setAttribute('validation','false');document.getElementById('new_incentive_after').setAttribute('validation','false');\">
					<label for='new_incentive_1'>사용안함</label>
					<input type=radio name='new_incentive_type' id='new_incentive_2' value='2' ".($db->dt[new_incentive_type] == "2" ? "checked":"")." onclick=\"document.getElementById('new_incentive').setAttribute('validation','true');document.getElementById('new_incentive_after').setAttribute('validation','false');\">
					<label for='new_incentive_2'>사용함</label>
					<input type=text class='textbox' name='new_incentive' value='".$db->dt[new_incentive]."' style='width:60px;' id='new_incentive' ".($db->dt[new_incentive_type] == "2" ? "validation='true'":"validation='false'")." title='신규회원유도시 인센티브'> 원
					<input type=radio name='new_incentive_type' id='new_incentive_3' value='3' ".($db->dt[new_incentive_type] == "3" ? "checked":"")." onclick=\"document.getElementById('new_incentive').setAttribute('validation','false');document.getElementById('new_incentive_after').setAttribute('validation','true');\">
					<label for='new_incentive_3'>첫 구매시</label>
					<input type=text class='textbox' name='new_incentive_after' value='".$db->dt[new_incentive_after]."' style='width:60px;' id='new_incentive_after' ".($db->dt[new_incentive_type] == "3" ? "validation='true'":"validation='false'")." title='신규회원유도첫구매시 인센티브'> 원 
					</td>
				</tr>
				<tr bgcolor='#ffffff'>
					<td class='input_box_title'> <b>매출액 인센티브 설정<img src='".$required3_path."'></b></td>
					<td class='input_box_item'>
					<input type=radio name='incentive_type' id='incentive_1' value='n' ".($db->dt[incentive_type] == "n" ? "checked":"")." onclick=\"document.getElementById('incentive_rate').setAttribute('validation','false');\">
					<label for='incentive_1'>사용안함</label>
					<input type=radio name='incentive_type' id='incentive_2' value='y' ".($db->dt[incentive_type] == "y" ? "checked":"")." onclick=\"document.getElementById('incentive_rate').setAttribute('validation','true');\">
					<label for='incentive_2'>사용함</label>
					<input type=text class='textbox' name='incentive_rate' value='".$db->dt[incentive_rate]."' style='width:60px;' id='incentive_rate' ".($db->dt[incentive_type] == "2" ? "validation='true'":"validation='false'")." title='매출액 인센티브 비율'> %
					</td>
				</tr>
				<tr bgcolor='#ffffff'>
					<td class='input_box_title'> <b>인센티브 지급 방법 설정<img src='".$required3_path."'></b></td>
					<td class='input_box_item'>
					<input type=radio name='incentive_way' id='incentive_way_1' value='1' ".($db->dt[incentive_way] == "1" ? "checked":"").">
					<label for='incentive_way_1'>적립금으로 지급</label>
					<input type=radio name='incentive_way' id='incentive_way_2' value='2' ".($db->dt[incentive_way] == "2" ? "checked":"").">
					<label for='incentive_way_2'>예치금으로 지급</label>
					<input type=radio name='incentive_way' id='incentive_way_3' value='3' ".($db->dt[incentive_way] == "3" ? "checked":"").">
					<label for='incentive_way_3'>현금</label>
					</td>
				</tr>
				<!--tr bgcolor='#ffffff'>
					<td class='input_box_title'> <b>인센티브 지급일 설정 <img src='".$required3_path."'></b></td>
					<td class='input_box_item'>
						매월 <input type=text class='textbox' name='incentive_day' value='".$db->dt[incentive_day]."' style='width:60px;' validation='true' title='인센티브 지급 날짜'> 일<br>
					</td>
				</tr>
				</table>
				<table width='100%' border='0'>
					<tr>
						<td align='left' style='line-height:120%;'>
							※ <span class='small'> 인센티브 설정은 리셀러에게 인센티브를 어떤 정책으로 줄지 설정해주는 곳입니다.</span><br>
							※ <span class='small'> 신규 가입자 인센티브 & 매출액인센티브 사용 안할시 리셀러 화면에도 자동으로 노출하지 안습니다. </span>
						</td>
						
					</tr-->
				</table>
				";

$Contents .= "

				<table width='100%' border='0'>
					<tr>
						<td align='left'>
							※ <span class='small'>  개인별 정책 적용을 선택해야 개별 인센티브 설정이 가능합니다.</span>
						</td>
						<td align='right'>
							<img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 onClick=\"acting('update','".$db->dt[code]."');\" style='cursor:pointer;'>
							<img src='../images/".$admininfo["language"]."/btn_close.gif' border=0 onClick='self.close();' style='cursor:pointer;'>
						</td>
					</tr>
				</table>
				<!-- 수정마침 -->

				                  </td>
				                </tr>
								</tr>
				              </table>
				            </td>
				          </tr>
				        </table>
				        </form>

		</td>
	</tr>

</TABLE>";



$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "리셀러관리 > 개인별인센티브수정";
$P->NaviTitle = "개인별인센티브수정";
$P->title = "개인별인센티브수정";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>
