<?
include("../class/layout.class");

$Script = "<script language='JavaScript'>

	function acting(){
		if(confirm('수정하시겠습니까?')){
			return true;
		}
		else{
			return false;
		}
	}

</Script>";

$db = new Database;



$sql = "SELECT * FROM reseller_accounts where ac_ix = '".$ac_ix."' ";
$db->query($sql);
$db->fetch();

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2 valign=top>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("정산수정하기", "리셀러관리 > 정산수정하기", false)."</td>
			</tr>
			<!--tr height=30><td class='p11 ls1' style='padding:0 0 0 20px;text-align:left;' > <b>".$db->dt[name]."</b> 님의 회원정보 입니다.</td></tr-->
			<tr>
				<td align=center style='padding: 0 10px 0 10px;height:369px;vertical-align:top'>

				      <form name='ac_modify_frm'  action='account.act.php?act=account_modify' method='post' onsubmit='acting()' act='act'>
					   <input type='hidden' name='way' value='".$db->dt[way]."'>
					   <input type='hidden' name='ac_price_befor' value='".$db->dt[ac_price]."'>
					   <input type='hidden' name='status' value='".$db->dt[status]."'>
					   <input type='hidden' name='ac_ix' value='".$db->dt[ac_ix]."'>
					   <input type='hidden' name='rsl_code' value='".$db->dt[rsl_code]."'>
					  <table border='0' cellspacing='1' cellpadding='5' width='100%'>
						<tr>
						  <td bgcolor='#F8F9FA'>
			<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
			<col width='27%' />
			<col width='*' />
				<tr>
					<td align='left' colspan='2''> ".GetTitleNavigation("정산수정하기", "리셀러관리 > 정산수정하기")."</td>
				</tr>
				<tr>
					<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>정산수정하기</b></div>")."</td>
				</tr>
			</table>
			<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
				<col width='25%' />
				<col width='25%' />
				<col width='25%' />
				<col width='25%' />
				<tr bgcolor='#ffffff'>
					<td class='input_box_title'> <b>최종 정산금액".($db->dt[status]=='AR' ? '<img src='.$required3_path.'>':'')."</b></td>
					<td class='input_box_item'>
						<input type=text style='width:50%;text-align:right;' name='last_incentive' value='".$db->dt[last_incentive]."' ".($db->dt[status]=='AC' ? 'readonly':'')."> 원
					</td>
					<td class='input_box_title'> <b>실정산금액".($db->dt[status]=='AC' ? '<img src='.$required3_path.'>':'')."</b></td>
					<td class='input_box_item'>
						<input type=text style='width:50%;text-align:right;' name='ac_price' value='".$db->dt[ac_price]."'  ".($db->dt[status]=='AR' ? 'readonly':'')."> 원
					</td>
				</tr>
				<tr bgcolor='#ffffff'>
					<td class='input_box_title'> <b>메모<img src='".$required3_path."'></b></td>
					<td class='input_box_item' colspan='3' style='padding:10px'>
						<textarea style='width:93%;height:150px;padding:10px' name=note >".$db->dt[note]."</textarea>
					</td>
				</tr>
				</table>
				<table width='100%' border='0'>
					<tr>
						<td align='left' style='line-height:120%;'>
							※ <span class='small'> 정산 대기는 최종 정산금액만 정산완료는 실정산금액만 수정이 가능합니다.</span><br>
							※ <span class='small'> 현금으로 정산완료된 건은 실정산금액을 수정하시면 +- 된 인센티브가 다음달에 자동으로 반영됩니다.</span>
						</td>
					</tr>
				</table>
				";

$Contents .= "

				<table width='100%' border='0'>
					<tr>
						<!--td align='left'>
							※ <span class='small'>  개인별 정책 적용을 선택해야 개별 인센티브 설정이 가능합니다.</span>
						</td-->
						<td align='right' >
							<input type='image' src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 style='vertical-align:top'>
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
$P->Navigation = "리셀러관리 > 정산수정하기";
$P->NaviTitle = "정산수정하기";
$P->title = "정산수정하기";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>
