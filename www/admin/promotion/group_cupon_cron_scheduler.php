<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");

$db = new Database;

$shmop = new Shared("cron_group_cupon");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$cron_group_cupon = $shmop->getObjectForKey("cron_group_cupon");
$cron_group_cupon = unserialize(urldecode($cron_group_cupon));

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
			<td align='left' colspan=4> ".GetTitleNavigation("쿠폰 발행일 자동 설정", "프로모션(마케팅) > 쿠폰 발행일 자동 설정")."</td>
	  </tr>
	 </table>

	<form name='search_form' method='get' action='group_cupon_cron_scheduler.act.php' onsubmit='return CheckFormValue(this);' style='display:inline;' target='iframe_act'>
	<input type='hidden' name='act' value='update'>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	<tr>
		<td colspan=2>
			
			<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='input_table_box'>
				<col width='20%' />
				<col width='30%' />
				<col width='20%' />
				<col width='30%' />
				<tr>
					<td class='input_box_title'>  자동발급 사용 유무  </td>
					<td class='input_box_item' colspan=3 style='padding:5px;' >
					<table cellpadding=3>
						<tr>
							<td>
								<input type='radio' name='disp' id='disp_0' value='0' ".CompareReturnValue("0",$cron_group_cupon[disp],"checked")." ><label for='disp_0'>사용안함</label>
							</td>
							<td>
								<input type='radio' name='disp' id='disp_1' value='1' ".CompareReturnValue("1",$cron_group_cupon[disp],"checked")." ><label for='disp_1'>사용</label>
							</td> 
							<td>
							</td>
							<td>
								매월 1일에 자동발행이 됩니다.
							<!--주기 설정 매월 
								<select name='day'  id='day'  style=\"font-size:12px;width:90px;\" >
								<option value=''>--</option>";
								for($i=1; $i < 31;$i++){
								$Contents01 .= "<option value='".$i."' ".( $cron_group_cupon[day] == $i ? 'selected' : '' ).">".$i."</option>";
								}
								$Contents01 .= "
							</select>
								일 기준으로 발급-->
							</td>
						</tr>
					</table>
					</td>
				</tr>
				</table>
		</td>
	</tr>
	<tr>
		<td colspan=2 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
	</tr>
	  </table>
	  </form>";


$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";
/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >상품 업데이트를 원하시는 시간대를 설정 하신후 저장 버튼을 클릭하시면 설정이 완료되게 됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록된 설정정보를 바탕으로 상품정보를 주기적으로 연동하게 됩니다</td></tr>
</table>
";
//$help_text =  getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');

$Contents .= HelpBox("상품자동연동 스케줄링", $help_text)."<br>";
*/
 $Script = "
 <script language='javascript'>

	</script>
 ";
/*
if($mmode == "pop"){

	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = goodss_menu();
	$P->NaviTitle = "상품자동연동 스케줄링";
	$P->Navigation = "도매아이템 > 기본정보설정 > 상품자동연동 스케줄링";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else{
}

*/
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = promotion_menu();
$P->title = "쿠폰 발행일 자동 설정";
$P->Navigation = "프로모션(마케팅) > 회원그룹 쿠폰 > 쿠폰 발행일 자동 설정";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>