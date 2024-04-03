<?
include("../class/layout.class");

$Script = "
<script language='JavaScript' >

	function manual_login(frm){
		
		if($('#nonmember:checked').val()!='Y'){
				if(!$('#charger_ix').val()){
				alert('회원를 선택해주세요');
				return false;
			}
		}
		
		if(!CheckFormValue(frm)){
			return false;
		}

		return true;
	}
	
	function approve_manual_order(url){

		//manual_order_win = window.open(url,'manual_order','');

		ShowModalWindow(url,1400,800,'manual_order',true);
		window.frames['act'].location.href='./manual_order.new.act.php?act=manual_logout';

		/*
		$('#manual_order_table').hide();

		$('#manual_order_frame').attr('src',url);
		$('#manual_order_frame').show();
		$('#manual_order_frame').css({'height':'1000'});
		WideView();
		*/
	}

</Script>
";

$Contents ="
<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' valign=top>
<tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("수동주문", "수동주문")."</td>
</tr>
<tr>
	<td colspan=2 width='100%' valign=top style='padding-top:3px;'>

	<!--iframe name='manual_order_frame' id='manual_order_frame' width='100%' frameborder='0' style='display:none;'></iframe-->

	<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center height='120px' id='manual_order_table'>
		<tr>
			<td style='padding:0px 22%'>
				<form  name='manual_login_frm' method='post' onsubmit=\"return manual_login(this)\" action='./manual_order.new.act.php' target='act' >
				<input type=hidden name=act value='manual_login'>
					<table cellpadding=0 cellspacing=0 border=0 width=100% align=center class='input_table_box'>
						<col width='30%'>
						<col width='*'>
						<col width='20%'>
						<tr height='30'>
							<td class='input_box_title' >
								<b>회원 선택</b> <img src='".$required3_path."'>
							</td>
							<td class='input_box_item'>
								<table cellpadding=0 cellspacing=0 border=0 width=100%>
									<col width='60%'>
									<col width='*'>
									<tr>
										<td>
											".SearchMember("")."
										</td>
										<td>
											<input type='checkbox' name='nonmember' id='nonmember' value='Y'/><label for='nonmember'>비회원 요청</label>
										</td>
									</tr>
								</table>
							</td>
							<td class='input_box_item' rowspan='2' style='padding:0px;text-align:center;'>
								<input type=image src='/admin/v3/images/btns/login_btn01.gif' border=0 align=absmiddle style='cursor:pointer;'>
							</td>
						</tr>
						<tr height='30'>
							<td class='input_box_title' ><b>관리자 비밀번호</b> <img src='".$required3_path."'></td>
							<td class='input_box_item'>
								<input type='password' name='admin_pw'  id='admin_pw' value='' size=50 class='textbox' validation='true' title='관리자 비밀번호' style='width:130px'>
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

$help_text = "
<table cellpadding=2 cellspacing=0 class='small' style='line-height:120%' >
	<col width=8>
	<col width=*>
	<!--tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td >수동수주서 작성은 이동창고가 다를 경우 별도로 작성을 하셔야 합니다..</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td >이동하고자 하는 정보와 품목의 수량정보를 입력하신후 저장버튼을 눌러 요청대장의 작성을 완료 하실 수 있습니다. </td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td ><u>내부창고</u> 이동의 경우 자동으로 이동출고 와 이동입고에 대한 기록이 남으며 재고정보도 즉시 이동되게 됩니다.</td></tr>
	<tr><td valign=middle><img src='/admin/image/icon_list.gif' ></td><td ><u>외부창고</u> 이동의 경우 보관장소는 자동으로 현재창고의 경우 출고 보관장소 가 이동창고의 경우는 입고 보관장소가 자동 선택되게 됩니다. </td></tr-->
</table>
";



$Contents .= HelpBox("수동주문", $help_text,"100");



if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->strLeftMenu = order_menu();
	$P->addScript = $Script;
	$P->Navigation = "주문관리 > 수동주문";
	$P->NaviTitle = "수동주문 ";
	$P->strContents = $Contents;
	$P->PrintLayOut();
}else{
	$P = new LayOut;
	$P->addScript = $Script;
	$P->OnloadFunction = "";
	$P->strLeftMenu = order_menu();
	$P->strContents = $Contents;
	$P->Navigation = "주문관리 > 수동주문";
	$P->title = "수동주문";
	$P->PrintLayOut();
}

?>