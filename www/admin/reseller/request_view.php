<?
include("../class/layout.class");


$Script = "<script language='JavaScript'>

	function acting(act,rq_ix){
		if(confirm('수정하시겠습니까?')){
			
			if (act == 'update')
			{
				var form = eval('document.EDIT_'+rq_ix);
			
				form.action = 'request.act.php?act='+act+'&rq_ix='+rq_ix;
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



$sql = "SELECT * FROM reseller_request where rq_ix = '".$rq_ix."' ";
$db->query($sql);
$db->fetch();

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2 valign=top>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("리셀러신청 수정", "리셀러관리 > 리셀러신청 수정", false)."</td>
			</tr>
			<tr>
				<td align=center style='padding: 0 10px 0 10px;height:369px;vertical-align:top'>

				      <form name='EDIT_".$db->dt[rq_ix]."' method='post' onsubmit='return'>
					  <table border='0' cellspacing='1' cellpadding='5' width='100%'>
						<tr>
						  <td bgcolor='#F8F9FA'>
			<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
			<col width='27%' />
			<col width='*' />
				<tr>
					<td align='left' colspan='2''> ".GetTitleNavigation("리셀러신청 수정 ", "리셀러관리 > 리셀러신청 수정")."</td>
				</tr>
				<tr>
					<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>리셀러 신청 수정</b></div>")."</td>
				</tr>
			</table>
			<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
				<col width='32%' />
				<col width='*' />
				<tr bgcolor='#ffffff'>
					<td class='input_box_title'> <b>이름<img src='".$required3_path."'></b></td>
					<td class='input_box_item'>
					".$db->dt[name]."
					</td>
				</tr>
				<tr bgcolor='#ffffff'>
					<td class='input_box_title'> <b>이메일<img src='".$required3_path."'></b></td>
					<td class='input_box_item'>
					".$db->dt[email]."
					</td>
				</tr>
				<tr bgcolor='#ffffff'>
					<td class='input_box_title'> <b>전화번호<img src='".$required3_path."'></b></td>
					<td class='input_box_item'>
					".$db->dt[tel]."
					</td>
				</tr>
				<tr bgcolor='#ffffff'>
					<td class='input_box_title'> <b>자기소개<img src='".$required3_path."'></b></td>
					<td class='input_box_item'>
					<textarea style=\"width:94%;height:200px;padding:10px;margin:10px 0;\" name=\"content\">".$db->dt[content]."</textarea>
				</tr>
				</table>
				";

$Contents .= "

				<table width='100%' border='0'>
					<tr>
						<td align='left'>
							※ <span class='small'>  개인별 정책 적용을 선택해야 개별 인센티브 설정이 가능합니다.</span>
						</td>
						<td align='right'>
							<img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 onClick=\"acting('update','".$db->dt[rq_ix]."');\" style='cursor:pointer;'>
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
$P->Navigation = "리셀러관리 > 리셀러신청수정";
$P->NaviTitle = "리셀러신청수정";
$P->title = "리셀러신청수정";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>
