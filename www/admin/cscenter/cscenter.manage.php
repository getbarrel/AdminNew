<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");

$db = new Database;

if($page_type == 'use_after'){
	$page_title = '후기 게시판 설정';
	$div_link = 'cscenter.manage.div.php';
}else{
	$page_title = '상품문의 게시판 설정';
	$div_link = 'cscenter.manage.div2.php';
}

$shmop = new Shared($page_type);
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$datas = $shmop->getObjectForKey($page_type);
$datas = unserialize(urldecode($datas));

$Script = "	<script language='javascript'>

		</script>";


$mstring ="<form name=bbs_manage_frm action='cscenter.manage.act.php' method='post' onsubmit='return CheckFormValue(this)'>
		<input type=hidden name=act value='$act'>
		<input type=hidden name=page_type value='$page_type'>
		<input type=hidden name=mmode value='$mmode'>
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed'>
		<col width='18%'>
		<col width='32%'>
		<col width='18%'>
		<col width='32%'>
		<tr>
			<td align='left' colspan=4 style='padding:2px 0 2px 0;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle><b> 기본 기능 설정</b></div>")."</td>
		</tr>
		</table>
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed' class='input_table_box'>
		<col width='18%'>
		<col width='32%'>
		<col width='18%'>
		<col width='32%'>";

	if($page_type != 'use_after') {
        $mstring .= "
		<tr >
			<td class='input_box_title' > 분류 사용여부 </td>
			<td class='input_box_item'  colspan=3>
			<input type=radio name='use_div' value='Y' id='use_div_y'  " . CompareReturnValue("Y", $datas[use_div], "checked") . "><label for='use_div_y'>사용</label><input type=radio name='use_div' value='N' id='use_div_n' " . CompareReturnValue("N", $datas[use_div], "checked") . "><label for='use_div_n'>사용하지 않음</label>
			<div>
				<input type='button' value='분류 설정' style='margin: 3px;cursor:pointer;' onclick=\"javascript:PoPWindow3('" . $div_link . "',900,800,'manage.div')\">
			</div>
			</td>
		</tr>
		<tr >
			<td class='input_box_title' > 관리자 댓글 사용여부 </td>
			<td class='input_box_item'  colspan=3>
			<input type=radio name='use_comment' value='Y' id='use_comment_y' " . CompareReturnValue("Y", $datas[use_comment], "checked") . "><label for='use_comment_y'>사용</label><input type=radio name='use_comment' value='N' id='use_comment_n' " . CompareReturnValue("N", $datas[use_comment], "checked") . "><label for='use_comment_n'>사용하지 않음</label>
			</td>
		</tr>";
    }
	if($page_type == 'use_after'){
		$mstring .="
        <!--
		<tr >
			<td class='input_box_title' > 베스트 사용 여부 </td>
			<td class='input_box_item'  colspan=3>
			<input type=radio name='use_best' value='Y' id='use_best_y' ".CompareReturnValue("Y",$datas[use_best],"checked")."><label for='use_best_y'>사용</label><input type=radio name='use_best' value='N' id='use_best_n' ".CompareReturnValue("N",$datas[use_best],"checked")."><label for='use_best_n'>사용하지 않음</label>
			</td>
		</tr>
		-->
		<tr >
			<td class='input_box_title' > 상품 평점 사용여부 </td>
			<td class='input_box_item'  colspan=3>
			<input type=radio name='use_valuation_goods' value='Y' id='valuation_goods_y' ".CompareReturnValue("Y",$datas[use_valuation_goods],"checked")."><label for='valuation_goods_y'>사용</label><input type=radio name='use_valuation_goods' value='N' id='valuation_goods_n' ".CompareReturnValue("N",$datas[use_valuation_goods],"checked")."><label for='valuation_goods_n'>사용하지 않음</label>
			</td>
		</tr>
		<tr >
			<td class='input_box_title' > 베송 평점 사용여부 </td>
			<td class='input_box_item'  colspan=3>
			<input type=radio name='use_valuation_delivery' value='Y' id='valuation_delivery_y' ".CompareReturnValue("Y",$datas[use_valuation_delivery],"checked")."><label for='valuation_delivery_y'>사용</label><input type=radio name='use_valuation_delivery' value='N' id='valuation_delivery_n' ".CompareReturnValue("N",$datas[use_valuation_delivery],"checked")."><label for='valuation_delivery_n'>사용하지 않음</label>
			</td>
		</tr>
		";
	}

$mstring .="
		</table>


		";
		if($page_type == 'use_after'){
			$mstring .="
            <!--
            <table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed;margin-top:20px;'
            <tr>
                <td align='left' colspan=4 style='padding:2px 0 2px 0;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle><b> 권한 설정</b></div>")."</td>
            </tr>
            </table>
            <table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed' class='input_table_box'>
            <col width='18%'>
            <col width='32%'>
            <col width='18%'>
            <col width='32%'>
			<tr >
				<td class='input_box_title'> 후기 작성 가능 시점 </td>
				<td class='input_box_item' colspan=3>
					<table cellpadding=0 cellsapcing=0>
						<tr height=40>
							<td><input type=radio name='write_auth' value='1' id='only_member' ".CompareReturnValue("1",$datas[write_auth],"checked")."><label for='only_member'>구매여부와 상관없이 회원만 작성 가능</label></td>
						</tr>
						<tr height=40>
							<td><input type=radio name='write_auth' value='2' id='only_buyer' ".CompareReturnValue("2",$datas[write_auth],"checked")."><label for='only_buyer'>구매내역이 존재하는 경우에만 후기 작성 가능</label> 
							(작성 가능 시점 <select name='write_timing'><option value='BF' ".CompareReturnValue("BF",$datas[write_timing],"checked").">구매확정 이후</option></select>)</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr >
				<td class='input_box_title' > 쓰기권한 (관리자) </td>
				<td class='input_box_item' colspan=3>
				<input type=radio name='write_admin' value='Y' id='write_admin_y' ".CompareReturnValue("Y",$datas[write_admin],"checked")."><label for='write_admin_y'>사용</label><input type=radio name='write_admin' value='N' id='write_admin_n' ".CompareReturnValue("N",$datas[write_admin],"checked")."><label for='write_admin_n'>사용하지 않음</label>
				</td>
			</tr>
			-->
			";
		}else {
            $mstring .= "
            <table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed;margin-top:20px;'
            <tr>
                <td align='left' colspan=4 style='padding:2px 0 2px 0;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle><b> 권한 설정</b></div>")."</td>
            </tr>
            </table>
            <table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed' class='input_table_box'>
            <col width='18%'>
            <col width='32%'>
            <col width='18%'>
            <col width='32%'>
			<tr >
				<td class='input_box_title'> 쓰기 권한 추가 기준 </td>
				<td class='input_box_item' colspan=3>
					<table cellpadding=0 cellsapcing=0>
						<tr height=40>
							<td><input type=radio name='write_auth' value='1' id='only_member' " . CompareReturnValue("1", $datas[write_auth], "checked") . "><label for='only_member'>모든 사용자 작성 가능(비회원 포함) </label></td>
						</tr>
						<tr height=40>
							<td><input type=radio name='write_auth' value='2' id='only_buyer' " . CompareReturnValue("2", $datas[write_auth], "checked") . "><label for='only_buyer'>로그인한 회원만 작성 가능</label></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr >
				<td class='input_box_title'> 비공개 글 작성(사용자) </td>
				<td class='input_box_item' colspan=3>
					<input type=radio name='write_secret' value='Y' id='write_secret_y' " . CompareReturnValue("Y", $datas[write_secret], "checked") . "><label for='write_secret_y'>사용 </label>
					<input type=radio name='write_secret' value='N' id='write_secret_n' " . CompareReturnValue("N", $datas[write_secret], "checked") . "><label for='write_secret_n'>사용 안함 </label>
				</td>
			</tr>
			";


            $mstring .= "
		<tr >
			<td class='input_box_title' > 글 수정 여부(사용자) </td>
			<td class='input_box_item' colspan=3>
				<input type=radio name='modify_posts' value='Y' id='modify_posts_y' " . CompareReturnValue("Y", $datas[modify_posts], "checked") . "><label for='modify_posts_y'>사용</label>
				<input type=radio name='modify_posts' value='N' id='modify_posts_n' " . CompareReturnValue("N", $datas[modify_posts], "checked") . "><label for='modify_posts_n'>사용하지 않음</label>
			</td>
		</tr>
		<tr >
			<td class='input_box_title' > 글 삭제 여부(사용자) </td>
			<td class='input_box_item' colspan=3>
				<input type=radio name='delete_posts' value='Y' id='delete_posts_y' " . CompareReturnValue("Y", $datas[delete_posts], "checked") . "><label for='delete_posts_y'>사용</label>
				<input type=radio name='delete_posts' value='N' id='delete_posts_n' " . CompareReturnValue("N", $datas[delete_posts], "checked") . "><label for='delete_posts_n'>사용하지 않음</label>
			</td>
		</tr>
		</table>";
		}
	if($page_type == 'use_after'){
		$mstring .="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed;margin-top:20px;'
		<tr>
			<td align='left' colspan=4 style='padding:2px 0 2px 0;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle><b> 마일리지 설정</b></div>")."</td>
		</tr>
		</table>
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed' class='input_table_box'>
		<col width='18%'>
		<col width='32%'>
		<col width='18%'>
		<col width='32%'>
		<tr>
			<td class='input_box_title' > 마일리지 적립여부 </td>
			<td class='input_box_item' colspan=3>
				<input type=radio name='use_mileage' value='Y' id='use_mileage_y' ".CompareReturnValue("Y",$datas[use_mileage],"checked").">
				<label for='use_mileage_y'>적립</label>
				<input type=radio name='use_mileage' value='N' id='use_mileage_n' ".CompareReturnValue("N",$datas[use_mileage],"checked").">
				<label for='use_mileage_n'>적립안함</label>
			</td>
		</tr>
		<!--
		<tr>
			<td class='input_box_title' > 마일리지 적립 시점 </td>
			<td class='input_box_item' colspan=3>
				<input type=radio name='mileage_timing' value='D' id='add_direct' ".CompareReturnValue("R",$datas[mileage_timing],"checked")." >
				<label for='add_direct'>즉시</label>
				<input type=radio name='mileage_timing' value='S' id='add_self' ".CompareReturnValue("A",$datas[mileage_timing],"checked")." >
				<label for='add_self'>관리자 확인 후 수동지급</label>
				<input type=radio name='mileage_timing' value='A' id='add_auto' ".CompareReturnValue("A",$datas[mileage_timing],"checked")." >
				<label for='add_auto'>설정 날짜 이후 자동 지급</label> <input type=text class='textbox' style='width: 40px;' name='point_time_date' value='".$datas[point_time_date]."'>일
			</td>
		</tr>
		-->
		<tr>
			<td class='input_box_title' > 마일리지 적립 형태</td>
			<td class='input_box_item' colspan=3>
				<table>
					<tr><td>
						<input type=radio name='add_mileage_type' value='1' id='add_mileage_type1' ".CompareReturnValue("1",$datas[add_mileage_type],"checked")." >
						<label for='add_mileage_type1'>일괄적립</label>
						<input type=text name='mileage_amount' value='".$datas[mileage_amount]."' class='textbox' style='width: 40px;' > 적립
					</td></tr>
					<tr><td>
						<input type=radio name='add_mileage_type' value='2' id='add_mileage_type2' ".CompareReturnValue("2",$datas[add_mileage_type],"checked")." >
						<label for='add_mileage_type2'>분류별 적립</label></br>

						<table cellpadding=0 cellspacing=0 class='input_table_box' style='margin: 5px;'>
							<tr bgcolor='#ffffff'>
								<td class='input_box_title' style='padding-right: 15px;'>일반후기</td>
								<td class='input_box_item'>
									작성시 <input type=text name='mileage_amount_r' value='".$datas[mileage_amount_r]."' class='textbox' style='width: 40px;' > 적립
								</td>
							</tr>
							<tr bgcolor='#ffffff'>
								<td class='input_box_title' style='padding-right: 15px;'>프리미엄후기</td>
								<td class='input_box_item'>
									작성시 <input type=text name='mileage_amount_p' value='".$datas[mileage_amount_p]."' class='textbox' style='width: 40px;' > 적립
								</td>
							</tr>
						</table>
					</td></tr>
					<tr><td>
						<input type=radio name='add_mileage_type' value='3' id='add_mileage_type3' ".CompareReturnValue("3",$datas[add_mileage_type],"checked")." >
						<label for='add_mileage_type3'>회원그룹별 적립</label>

						<table cellpadding=0 cellspacing=0 class='input_table_box' style='margin: 5px;'>
							<tr bgcolor='#ffffff'>
								<td class='input_box_title'>회원그룹</td>
								<td class='input_box_title' style='padding-right: 15px;'>마일리지</td>
							</tr>
							<tr bgcolor='#ffffff'>";

							$sql = "SELECT gi.gp_ix, gi.gp_name 
										FROM shop_groupinfo gi
										where disp='1' and use_coupon_yn='Y'";
							$db->query($sql);
							$basic_groups = $db->fetchall();

							for($j = 0; $j < count($basic_groups); $j++){
								$mstring .="
								<tr>
									<td class='input_box_title' style='padding-right: 15px;'>".$basic_groups[$j][gp_name]."</td>
									<td class='input_box_item'>
										<input type=text name='mileage_amount_group[".$basic_groups[$j][gp_ix]."]' value='".$datas[mileage_amount_group][$basic_groups[$j][gp_ix]]."' class='textbox' style='width: 40px;' >
									</td>
								</tr>
								";
							}

$mstring .="
							</tr>
						</table>
					</td></tr>
				</table>
			</td>
		</tr>		
		</table>";
	}

$mstring .="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='table-layout:fixed' >
		<col width='18%'>
		<col width='32%'>
		<col width='18%'>
		<col width='32%'>
		<tr bgcolor=#ffffff >
            <td colspan=4 align=right style='padding:10px 0px;'>
				<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' >
                <img src='../images/".$admininfo["language"]."/b_cancel.gif' border=0 style='cursor:hand;border:0px;' align=absmiddle onclick='history.back();'>
            </td>
        </tr>
		</form>";
$mstring .="</table>";

//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');
//$help_text = HelpBox("게시판 설정", $help_text);

$Contents = $mstring.$help_text."<br><br><br><br>";

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = cscenter_menu();
$P->Navigation = "게시판관리 > ".$page_title;
$P->title = $page_title;
$P->strContents = $Contents;
echo $P->PrintLayOut();

function makeSelectBox($mdb,$select_name,$gp_level){
	$mdb->query("SELECT * FROM ".TBL_SHOP_GROUPINFO." where disp=1 order by gp_level asc ");

	$mstring = "<select name='$select_name' class=small style='width:100px;'>";
	$mstring .= "<option value='0'>전체보기</option>";
	if($mdb->total){
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$mstring .= "<option value='".$mdb->dt[gp_level]."' ".($mdb->dt[gp_level] == $gp_level ? "selected":"").">".$mdb->dt[gp_name]."  (레벨 : ".$mdb->dt[gp_level].")</option>";
		}
	}else{
		$mstring .= "<option value=''>".$msg."</option>";
	}
	$mstring .= "</select>";

	return $mstring;
}

function board_group()
{
	global $board_group;

	$mdb = new Database;

	$sql = "select div_ix,div_name from bbs_group where disp = '1'";
	$mdb->query($sql);

	if($mdb->total)
	{
		for($i = 0;$i < $mdb->total;$i++)
		{
			$mdb->fetch($i);
		
			$mstring .= "<input type=radio name='board_group' id='group_".$mdb->dt["div_ix"]."' value='".$mdb->dt["div_ix"]."' ".($board_group == $mdb->dt["div_ix"] ? "checked":"")." validation='true' title='게시판 그룹' /><label for='group_".$mdb->dt["div_ix"]."'>".$mdb->dt["div_name"]."</label>";
			
		}
	}

	return $mstring;
}

?>