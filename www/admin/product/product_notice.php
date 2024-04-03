<?
include("../class/layout.class");


$db = new Database;
$mdb = new Database;

if($act == 'update' ){
	$sql = "select * from shop_product_notice where pn_ix = '".$pn_ix."'";
	$db->query($sql);
	$db->fetch();
	
	$type = $db->dt[type];
	$title= $db->dt[title];

	$status = $db->dt[status];
	$contents = $db->dt[contents];
	$reply = $db->dt[reply];
	$company_id = $db->dt[company_id];
	$com_name = $db->dt[com_name];
	$id = $db->dt[id];
	$code = $db->dt[code];
	$pid = $db->dt[pid];
	
	$md_cod = $db->dt[md_cod];
	$md_name = $db->dt[md_name];

	$act = "update";
	
	//게시글 확인
	$sql = "update shop_product_notice set check_yn = 'Y' where pn_ix = '$pn_ix' $where";
	$db->query($sql);

}else{
	
	$sql = "select
				p.*,
				pr.cid,
				ccd.company_id,
				ccd.com_name
			from
				shop_product as p 
				left join shop_product_relation as pr on (p.id = pr.pid and pr.basic = '1')
				inner join common_company_detail as ccd on (p.admin = ccd.company_id)
			where
				p.id = '".$pid."'";

	$db->query($sql);
	$db->fetch();
	
	$type = 'P';
	$company_id = $db->dt[company_id];
	$com_name = $db->dt[com_name];
	$pnmae = $db->dt[pnmae];
	$pid = $db->dt[id];
	$pcode = $db->dt[pcode];
	$cid = $db->dt[cid];
	$pname = $db->dt[pname];

	$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $pid, "m", $db->dt);
	
	$contents = "
				<img src='".$img_str."' onerror=\"this.src='../images/noimage_152_148.gif'\" style='border:1px solid silver' width=200 height=200><br>
				상품시스템 코드 : ".$pid."<br>
				상품코드 : ".$pcode."<br>
				상품명 : ".$pname."<br>
				";

	$act = "insert";

	$sql = "select * from shop_category_info where cid = '".$cid."'";
	$db->query($sql);
	$db->fetch();
	$depth = $db->dt[depth];

	for($i=$depth; $i>=0; $i--){

		$cid = substr($cid,0,($i=='0'?'3':($i+1)*3));

		switch(strlen($cid)){
			case '3':
				$ori_cid = $cid."000000000000";
			break;
			case '6':
				$ori_cid = $cid."000000000";
			break;
			case '9':
				$ori_cid = $cid."000000";
			break;
			case '12':
				$ori_cid = $cid."000";
			break;
			case '15':
				$ori_cid = $cid;
			break;
		}

		$sql = "select
					AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
					cmd.code
				from
					shop_category_auth as ca
					inner join common_member_detail as cmd on (ca.access_user = cmd.code)
				where
					ca.category_access = 'MD'
					and auth_use = '1'
					and ca.cid = '".$ori_cid."'";

		$db->query($sql);
		$db->fetch();
		
		if($db->total > 0){		//해당카테고리로 MD정보가 잇을경우 
			$md_name = $db->dt[name];
			$md_code = $db->dt[md_code];
			break;
		}else{	
			$md_name = '';
			$md_code = '';
		}
	}
}


if($type == 'P'){
	$cs_title = "관리자 답변";
}

$addScript = "

<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n
<style text/css>
	.xquared .editor {height: 150px;}
</style>
<SCRIPT LANGUAGE='JavaScript'>
function go_con_del(pn_ix){
	yes = confirm('정말로 삭제하시 겠습니까?');
	if(yes){
		window.frames['act'].location.href = './found_info.act.php?act=detail_delete&pn_ix='+pn_ix;
	}else{
		return;
	}
}
</script>
<script type='text/javascript'>

$(document).ready(function() {	//xq edit IE 11버전에서 문제 잇어서 ck로 교체 2014-08-07 이학봉

	CKEDITOR.replace('contents',{
		startupFocus : false,height:300
	});

	CKEDITOR.replace('reply',{
		startupFocus : false,height:200
	});
});
</script>
";

$Contents = "
	<form name='delivery_form' action='product_notice.act.php' method='post' onsubmit='return CheckFormValue(this)' target=''>
	<input type='hidden' name='act' value='".$act."'>
	<input type='hidden' name='pn_ix' value='".$pn_ix."'>
	<input type='hidden' name='pid' value='".$pid."'>
	<input type='hidden' name='type' value='P'>

	<table cellSpacing=0 cellPadding=0 width='99%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("창업 지원 서비스 신청", "고객센타 > 창업 지원 서비스 신청", false)."</td>
			</tr>
			<tr>
				<td align=center style='padding: 10px 0px'>
					<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box'>
						<colgroup>
							<col width='20%' />
							<col width='30%' style='padding:0px 0px 0px 10px'/>
							<col width='20%' />
							<col width='30%' style='padding:0px 0px 0px 10px'/>
						</colgroup>
						<tr height='28' valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>제목</b></td>
							<td class='input_box_item' colspan='3'>
								<input type='text' class='textbox' name='title' id='title' value='".$title."' style='width:200px;' validation=true title='제목'>
							</td>
						</tr>
						<tr height='28' valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>작성자</b></td>
							<td class='input_box_item' colspan='3'>
								<input type='text'  class='textbox' name='name' id='name' value='".($pn_ix != ""?$db->dt[name]:$_SESSION[admininfo][charger])."' style='width:100px;' readonly>
								<input type='hidden' name='code' value='".($pn_ix != ""?$code:$_SESSION[admininfo][charger_ix])."'>
								<input type='hidden' name='id' value='".($pn_ix != ""?$id:$_SESSION[admininfo][charger_id])."'>
							</td>
							
						</tr>

						<tr height='28' valign='middle' bgcolor=#ffffff>

							<td class='input_box_title'> <b>처리상태 </b></td>
							<td class='input_box_item' colspan='3'>";

				if($act == 'update'){
							if(status == 'W'){
								$Contents .= "	문의요청";
							}else if($status == 'I'){
								$Contents .= "	처리중";
							}else if($status == 'C'){
								$Contents .= "	처리완료";
							}else{
								$Contents .= "	문의요청";
							}
				
				}else{
$Contents .= "
								<select id='status' name='status'>
									<option>선택</option>
									<option name='status' value='W' id='status_w' ".(($status == 'W' || $status == '')? "selected":"").">문의요청</option>
									<option name='status' value='I' id='status_i' ".(($status == 'I')? "selected":"").">처리중</option>
									<option name='status' value='C' id='status_c' ".(($status == 'C')? "selected":"").">처리완료</option>
								</select>";
				}
$Contents .= "
							</td>
						</tr>";

$Contents .= "
						<tr height='28' valign='middle' bgcolor=#ffffff>
							<td class='input_box_title'> <b>MD </b></td>
							<td class='input_box_item'>
								<!--input type='hidden' class='textbox' name='md_code' id='md_code' value='".$md_code."'>
								<input type='text' class='textbox point_color' name='md_name' id='md_name' value='".$md_name."' style='width:140px;' readonly=''-->
								".MDSelect($md_code, "md_code", "md_ix")."
								<input type='hidden' name='md_mem_ix' value=''>
							</td>
							<td class='input_box_title'> <b>셀러업체 </b></td>
							<td class='input_box_item'>
								<input type='hidden' class='textbox' name='company_id' id='company_id' value='".$company_id."'>
								<input type='text' class='textbox point_color' name='com_name' id='com_name' value=' ".$com_name."' validation='true' title='셀러업체' style='width:140px;' readonly='readonly'>
							</td>
						</tr>";


$Contents .= "
						<tr valign='middle' bgcolor=#ffffff>
							<td colspan=4 style='padding:5px'>
								<textarea name='contents' id='contents' class='textbox' style='width:95%;height:85px;resize:none;' validation=true title='내용' >"
									.$contents."
								</textarea>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>";


if(($admininfo[admin_level] == '9' && $pn_ix && $type != 'C') || ($admininfo[admin_level] == '8' && $type == 'C')){	//관리자 긴급사항시 셀러 답변 가능 기타 미노출

$Contents .= "
		<table width='100%' cellpadding=0 cellspacing=0 border='0'>
			<tr>
				<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
					<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>".$cs_title."</b>
				</td>
			</tr>
		</table>";

$Contents .= "
		<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box'>
			<colgroup>
				<col width='20%' />
				<col width='30%' style='padding:0px 0px 0px 10px'/>
				<col width='20%' />
				<col width='30%' style='padding:0px 0px 0px 10px'/>
			</colgroup>
			<tr>
				<td class='input_box_title'> <b>작성자 </b></td>
				<td class='input_box_item'>
					<input type='text'  class='textbox' name='fs_name' id='fs_name' value='".$_SESSION[admininfo][charger]."' style='width:100px;' readonly>
					<input type='hidden' name='charger_ix' value='".$_SESSION[admininfo][charger_ix]."'>
				</td>
				<td class='input_box_title'> <b>처리상태 </b></td>
				<td class='input_box_item'>
					<select id='status' name='status'>
						<option>선택</option>
						<option name='status' value='W' id='status_w' ".(($status == 'W')? "selected":"").">문의요청</option>
						<option name='status' value='I' id='status_i' ".(($status == 'I')? "selected":"").">처리중</option>
						<option name='status' value='C' id='status_c' ".(($status == 'C')? "selected":"").">처리완료</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class='input_box_item' colspan='4' style='padding:5px;'>
					<textarea name='reply' id='reply' class='textbox' style='width:95%;height:30px;resize:none;'>
						".$reply."
					</textarea>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	</table>";

}


if(($admininfo[admin_level] == '8' && $pn_ix && $type != 'C') || ($admininfo[admin_level] == '9' && $type == 'C')){	//관리자 긴급사항시 관리자 노출 기타 셀러만 노출

$Contents .= "
		<table width='100%' cellpadding=0 cellspacing=0 border='0'>
			<tr>
				<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
					<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>".$cs_title."</b>
				</td>
			</tr>
		</table>";

$Contents .= "
		<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box'>
			<colgroup>
				<col width='20%' />
				<col width='30%' style='padding:0px 0px 0px 10px'/>
				<col width='20%' />
				<col width='30%' style='padding:0px 0px 0px 10px'/>
			</colgroup>
			<tr>
				<td class='input_box_item' colspan='4' style='padding:5px;'>
					<textarea name='reply' id='reply' class='textbox' style='width:95%;height:30px;resize:none;'>
						".$reply."
					</textarea>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	</table>";
}


$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0>
	<tr>
		<td align=center style='padding:10px 0px' colspan=2>";
		
		if($admininfo[admin_level] != '8' || $status == 'W' || $status == ''){

			$Contents .= "<input type='image' src='../images/korea/bts_modify.gif' style='border:0 none;vertical-align:0px;' />";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents .= "
				<a href=\"javascript:go_con_del('".$pn_ix."');\"><img src='../images/".$admininfo[language]."/btn_del.gif' border=0></a>";
			}else{
				$Contents .= "
				<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo[language]."/btn_del.gif' border=0></a>";
			}
		}

$Contents .= "
		</td>
	</tr>
	</TABLE>";


$Contents .= "
</form>
<IFRAME id=act name=act src='' frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>	
";


if($mmode == 'pop'){
	$P = new ManagePopLayOut();
	$P->addScript = $addScript;
	$P->Navigation = "상품관리 > 상품수정알림게시판";
	$P->NaviTitle = "상품수정알림게시판";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else{

	$P = new LayOut();
	$P->strLeftMenu = product_menu();
	$P->addScript = $Script;
	$P->Navigation = "상품관리 > ".$title;
	$P->title = $menu_name;
	$P->strContents = $Contents;
	$P->PrintLayOut();
}
?>





