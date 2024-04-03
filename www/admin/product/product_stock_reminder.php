<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2017-06-27
 * Time: 오후 10:57
 */
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");

$db = new Database;
$mdb = new Database;

if($_COOKIE[product_notice_limit]){
    $max = $_COOKIE[product_notice_limit]; //페이지당 갯수
}else{
    $max = 15;
}


if ($page == '')
{
    $start = 0;
    $page  = 1;
}else{
    $start = ($page - 1) * $max;
}

if($page == '')
{
    $start = 0;
    $page  = 1;
}else{
    $start = ($page - 1) * $max;
}

if($filed == ''){
	$filed = "sr.regdate";
}

if($sort == ''){
	$sort = "asc";
}

if($mode == 'search' || $mode == 'excel'){
}else{
	$state		= array("1");
	$reminder_state = array("N");
	$disp = array("1");

	$sql = "select sr_ix from shop_product_stock_reminder sr left join shop_product p on sr.pid = p.id left join shop_product_options_detail d on d.id = sr.op_id where sr.regdate <= '".date("Y-m-d", strtotime("-6 months"))."' and sr.status = 'E' ";

	$db->query($sql);
	$db->fetch();
	$delTotal = $db->total;

	if($delTotal > 0){
		for ($j = 0; $j < $delTotal; $j++)
		{
			$db->fetch($j);

			if($j == 0){
				$sr_ix = $db->dt[sr_ix];
			}else{
				$sr_ix = $sr_ix.', '.$db->dt[sr_ix];
			}
		}
		$sql = "delete from shop_product_stock_reminder where sr_ix in (".$sr_ix.") ";
		$db->query($sql);
	}
}

$Contents = "
<table width='100%' border='0' align='center'>
<tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("문의요청하기", $title)."</td>
</tr>
<tr>
	<td>";

$Contents .= "
	<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<th class='box_01'></th>
		<td class='box_02'></td>
		<th class='box_03'></th>
	</tr>
	<tr>
		<th class='box_04'></th>
		<td class='box_05'  valign=top style='padding:5px 0px 5px 0px'>
 			<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
			<form name=searchmember method='get'>
			<input type='hidden' name='mode' value='search' />
			<input type='hidden' name='sort' id='sort' value='".$sort."' />
			<input type='hidden' name='filed' id='filed' value='".$filed."' />
			<col width='18%'>
			<col width='*'>
			";

$Contents .=	"	<tr>
                <td class='search_box_title' >상품판매상태</td>
                <td class='search_box_item' colspan='3'>
                    <table cellpadding=0 cellspacing=0 border='0'  align='left'>";

$Contents .=	"
                    <col width=80>
                    <col width=80>
                    <col width=80>";

/*$Contents .=	"		<col width=80> 
                    <col width=90>
                    <col width=110>
                    
                    
                ";*/
$Contents .=	"<tr>
                        <td>
                        <input type='checkbox' name='state[]' id='search_state_1' value='1' title='판매중' ".(is_array($state)?in_array('1',$state)?'checked':'':'')."/>
                        <label for='search_state_1'> 판매중 </label> 
                        </td>
                        <td>
                        <input type='checkbox' name='state[]' id='search_state_0' value='0' title='일시품절' ".(is_array($state)?in_array('0',$state)?'checked':'':'')."/>
                        <label for='search_state_0'> 일시품절 </label> 
                        </td>
                        <td>
                        <input type='checkbox' name='state[]' id='search_state_2' value='2' title='판매중지' ".(is_array($state)?in_array('2',$state)?'checked':'':'')."/>
                        <label for='search_state_2'> 판매중지</label>
                        </td>";
/*                        <td>
                        <input type='checkbox' name='state[]' id='search_state_8' value='8' title='승인거부' ".(is_array($state)?in_array('8',$state)?'checked':'':'')."/> 
                        <label for='search_state_8'> 승인거부 </label>
                        </td>

if($admininfo[admin_level] == '9'){


$Contents .=	"			<td>
                        <input type='checkbox' name='state[]' id='search_state_6' value='6' title='승인대기' ".(is_array($state)?in_array('6',$state)?'checked':'':'')."/> 
                        <label for='search_state_6'> 승인대기 </label>
                        </td>

                        <td>
                        <input type='checkbox' name='state[]' id='search_state_7' value='7' title='수정대기상품' ".(is_array($state)?in_array('7',$state)?'checked':'':'')."/> 
                        <label for='search_state_7'> 수정대기상품 </label>
                        </td>";


$Contents .=	"	
                        
                        ";

$Contents .=	"	
                        <td>
                        <input type='checkbox' name='state[]' id='search_state_9' value='9' title='판매금지' ".(is_array($state)?in_array('9',$state)?'checked':'':'')."/> 
                        <label for='search_state_9'> 판매금지 </label>
                        </td>
                        ";

$Contents .=	"	
                        <td>
                        <input type='checkbox' name='state[]' id='search_state_4' value='4' title='판매예정' ".(is_array($state)?in_array('4',$state)?'checked':'':'')."/> 
                        <label for='search_state_4'> 판매예정 </label>
                        </td>
                        ";

    $Contents .=	"	
                        <td>
                        <input type='checkbox' name='state[]' id='search_state_5' value='5' title='판매종료' ".(is_array($state)?in_array('5',$state)?'checked':'':'')."/> 
                        <label for='search_state_5'> 판매종료 </label>
                        </td>
                        ";

}*/
$Contents .=	"
                    </tr>
                    </table>
                </td>
            </tr>";

$Contents .=	"	<tr>
                <td class='search_box_title' >재입고상태</td>
                <td class='search_box_item' colspan='3'>
                    <table cellpadding=0 cellspacing=0 border='0'  align='left'>";

$Contents .=	"
                    <col width=80>
                    <col width=80>
                    <col width=80>";
$Contents .=	"<tr>
						<td>
                        <input type='checkbox' name='reminder_state[]' id='search_reminder_state_1' value='N' title='발송요청' ".(is_array($reminder_state)?in_array('N',$reminder_state)?'checked':'':'')."/>
                        <label for='search_reminder_state_1'> 발송요청 </label> 
                        </td>
                        <td>
                        <input type='checkbox' name='reminder_state[]' id='search_reminder_state_3' value='P' title='입고미정' ".(is_array($reminder_state)?in_array('P',$reminder_state)?'checked':'':'')."/>
                        <label for='search_reminder_state_3'> 입고미정 </label> 
                        </td>
                        <td>
                        <input type='checkbox' name='reminder_state[]' id='search_reminder_state_0' value='Y' title='발송완료' ".(is_array($reminder_state)?in_array('Y',$reminder_state)?'checked':'':'')."/>
                        <label for='search_reminder_state_0'> 발송완료 </label> 
                        </td>
                        <td>
                        <input type='checkbox' name='reminder_state[]' id='search_reminder_state_2' value='E' title='판매종료' ".(is_array($reminder_state)?in_array('E',$reminder_state)?'checked':'':'')."/>
                        <label for='search_reminder_state_2'> 판매종료</label>
                        </td>";
$Contents .=	"
                    </tr>
                    </table>
                </td>
            </tr>";
$Contents .=	"	<tr>
                <td class='search_box_title' >상품품절여부</td>
                <td class='search_box_item' colspan='3'>
                    <table cellpadding=0 cellspacing=0 border='0'  align='left'>";

$Contents .=	"
                    <col width=80>
                    <col width=80>";
$Contents .=	"<tr>
						<td>
                        <input type='checkbox' name='sold_out[]' id='search_sold_out_0' value='0' title='미품절' ".(is_array($sold_out)?in_array('0',$sold_out)?'checked':'':'')."/>
                        <label for='search_sold_out_0'> 미품절 </label> 
                        </td>
						<td>
                        <input type='checkbox' name='sold_out[]' id='search_sold_out_1' value='1' title='품절' ".(is_array($sold_out)?in_array('1',$sold_out)?'checked':'':'')."/>
                        <label for='search_sold_out_1'> 품절 </label> 
                        </td>";
$Contents .=	"
                    </tr>
                    </table>
                </td>
            </tr>";
$Contents .=	"	<tr>
                <td class='search_box_title' >노출여부</td>
                <td class='search_box_item' colspan='3'>
                    <table cellpadding=0 cellspacing=0 border='0'  align='left'>";

$Contents .=	"
                    <col width=80>
                    <col width=80>";
$Contents .=	"<tr>
						<td>
                        <input type='checkbox' name='disp[]' id='disp_0' value='0' title='미노출' ".(is_array($disp)?in_array('0',$disp)?'checked':'':'')."/>
                        <label for='search_sold_out_0'> 미노출 </label> 
                        </td>
						<td>
                        <input type='checkbox' name='disp[]' id='disp_1' value='1' title='노출' ".(is_array($disp)?in_array('1',$disp)?'checked':'':'')."/>
                        <label for='search_sold_out_1'> 노출 </label> 
                        </td>";
$Contents .=	"
                    </tr>
                    </table>
                </td>
            </tr>";
$Contents .= "
			<tr height=27>
				<td class='search_box_title' >조건검색 </td>
				<td class='search_box_item' colspan='3'>
					<table cellpadding=0 cellspacing=0 width=100%>
						<col width='80'>
						<col width='*'>
						<tr>
							<td>
							<select name=search_type>
									<option value='p.pname' ".CompareReturnValue("p.pname",$search_type,"selected").">상품명</option>
									<option value='p.id' ".CompareReturnValue("p.id",$search_type,"selected").">상품코드(시스템)</option>
							</select>
							</td>
							<td>
								<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:200px;font-size:12px;padding:1px;' >
							</td>
						</tr>
					</table>
				</td>
			</tr>
			</table>
		</td>
		<th class='box_06'></th>
	</tr>
	<tr>
		<th class='box_07'></th>
		<td class='box_08'></td>
		<th class='box_09'></th>
	</tr>
	</table>
	</td>
</tr>
<tr height=50>
	<td style='padding:10px 20px 0 20px' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
</tr>
</table>
<br>
</form>";

//if($mode == 'search'){
    if(!empty($search_text) && !empty($search_type)){
        $where .=" and $search_type LIKE '%".$search_text."%' ";
    }
    //상품판매상태 검색관련
    if(is_array($state)){		//판매상태 (판매중, 일시품절, 본사대기 ... )
        if(count($state)>0){
            $where.=" AND p.state IN ('".implode("','",$state)."')";
        }
    }else{
        if($state != ""){
            $where .= " and p.state = '".$state."'";
        }else{
            //$state=array();
            $state='';
        }
    }

	//재입고상태 검색관련
    if(is_array($reminder_state)){		//판매상태 (판매중, 일시품절, 본사대기 ... )
        if(count($reminder_state)>0){
            $where.=" AND sr.status IN ('".implode("','",$reminder_state)."')";
        }
    }else{
        if($reminder_state != ""){
            $where .= " and sr.status = '".$reminder_state."'";
        }else{
            $reminder_state='';
        }
    }

	if(is_array($sold_out)){
        if(count($sold_out)>0){
            $where .= " and d.option_soldout IN ('".implode("','",$sold_out)."')";
        }
    }else{
        if($sold_out != ""){
            $where .= " and d.option_soldout = '".$sold_out."'";
        }else{
            $sold_out='';
        }
    }

	if(is_array($disp)){
        if(count($disp)>0){
            $where .= " and p.disp IN ('".implode("','",$disp)."')";
        }
    }else{
        if($disp != ""){
            $where .= " and p.disp = '".$disp."'";
        }else{
            $disp='';
        }
    }
/*}else{
	$total = 0;
}*/

if($_REQUEST["mode"] == "excel"){
	$sql = "select sr.* from shop_product_stock_reminder sr left join shop_product p on sr.pid = p.id left join shop_product_options_detail d on d.id = sr.op_id where 1 $where";

	$db->query($sql);
	$db->fetch();
	$total = $db->total;

	$sql = "select 
				  sr.*
			from 
				shop_product_stock_reminder sr
			left JOIN 
				shop_product p on sr.pid = p.id
			left JOIN 
				shop_product_options_detail d on d.id = sr.op_id
			where 1 
			  $where
			  order by $filed $sort
	";
	$db->query($sql);

	ini_set('memory_limit','2048M');
	set_time_limit(9999999);

	include '../include/phpexcel/Classes/PHPExcel.php';
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

	date_default_timezone_set('Asia/Seoul');

	$discount_excel = new PHPExcel();

	// 속성 정의
	$discount_excel->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("discount product List")
								 ->setSubject("discount product List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("discount product List");
 
	$discount_excel->getActiveSheet(0)->setCellValue('A' . 1, "바코드");//iconv('UTF-8','EUC-KR',"번호")
	$discount_excel->getActiveSheet(0)->setCellValue('B' . 1, "상품명");
	$discount_excel->getActiveSheet(0)->setCellValue('C' . 1, "색상명");
	$discount_excel->getActiveSheet(0)->setCellValue('D' . 1, "옵션명");
	$discount_excel->getActiveSheet(0)->setCellValue('E' . 1, "회원ID");
	$discount_excel->getActiveSheet(0)->setCellValue('F' . 1, "신청날짜");

	for ($i = 0; $i < $total; $i++)
    {
		$db->fetch($i);

		$sql = "select * from shop_product where id = '".$db->dt[pid]."'";
        $mdb->query($sql);
        $mdb->fetch();
        $pname = $mdb->dt[pname];
		$add_info = $mdb->dt['add_info'];

		$sql = "select * from shop_product_options_detail where id = '".$db->dt[op_id]."' ";
        $mdb->query($sql);
        $mdb->fetch();
        $option_div = $mdb->dt[option_div];
		$option_gid = $mdb->dt[option_gid];

		$sql = "select * from common_user where code = '".$db->dt[user_code]."' ";
        $mdb->query($sql);
        $mdb->fetch();
        $id = $mdb->dt[id];

		if($id == ''){
			$sql = "select * from common_user_sleep where code = '".$db->dt[user_code]."' ";
			$mdb->query($sql);
			$mdb->fetch();
			$id = $mdb->dt[id];
			if($id == ''){
				$id = '탈퇴회원';
			}
		}


		$discount_excel->getActiveSheet()->setCellValue('A' . ($i + 2), $option_gid);
		$discount_excel->getActiveSheet()->setCellValue('B' . ($i + 2), $pname);
		$discount_excel->getActiveSheet()->setCellValue('C' . ($i + 2), $add_info);
		$discount_excel->getActiveSheet()->setCellValue('D' . ($i + 2), $option_div);
		$discount_excel->getActiveSheet()->setCellValue('E' . ($i + 2), $id);
		$discount_excel->getActiveSheet()->setCellValue('F' . ($i + 2), $db->dt[regdate]);

		unset($week_str);
	}

	// 첫번째 시트 선택
	$discount_excel->setActiveSheetIndex(0);

	$discount_excel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
	$discount_excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
	$discount_excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
	$discount_excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
	$discount_excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
	$discount_excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);

	header('Content-Type: application/vnd.ms-excel;');
	header('Content-Disposition: attachment;filename="product_stock_reminder_'.date("Ymd").'.xls"');
	header('Cache-Control: max-age=0');
	$objWriter = PHPExcel_IOFactory::createWriter($discount_excel, 'Excel5');

	$objWriter->save('php://output');

	exit;
}else{
	$sql = "select sr.* from shop_product_stock_reminder sr left join shop_product p on sr.pid = p.id left join shop_product_options_detail d on d.id = sr.op_id where 1 $where group by sr.pid, sr.op_id";
	//$sql = "select sr.* from shop_product_stock_reminder sr left join shop_product p on sr.pid = p.id where 1 $where group by sr.pid, sr.op_id";

	$db->query($sql);
	$db->fetch();
	$total = $db->total;

	$sql = "select 
				  sr.*,
				  IFNULL(sum(case when sr.status = 'N' then 1 when sr.status = 'P' then 1 when sr.status = 'E' then 1 else 0 end),0) as ready_cnt,
				  IFNULL(sum(case when sr.status = 'Y' then 1 else '0' end),0) as complate_cnt
			from 
				shop_product_stock_reminder sr
			left JOIN 
				shop_product p on sr.pid = p.id
			left JOIN 
				shop_product_options_detail d on d.id = sr.op_id
			where 1 
			  $where
			  group by sr.pid, sr.op_id
			  order by $filed $sort
	";
	$db->query($sql);
}


$Contents .= "
<form name='list_frm' method='POST' onsubmit='return false' action=''  target='act'>
<input type='hidden' name='code[]' id='code'>
<input type='hidden' name='act' value='info_delete'>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='left' >
<col width='80'>

<col width='*'>
<col width='100'>
<tr height=30 >
	<td>	
		전체 : ".$total." 개
	</td>
	
	<td align=right>
	";
$Contents .= "<a href='?".$_SERVER["QUERY_STRING"]."&mode=excel'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
$Contents .= "
	</td>
	
</tr>
</table>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
	<col width='2%'>
	<col width='10%'>
	<col width='11%'>
	<col width='20%'> 
	<col width='10%'> 
	<col width='8%'> 
	<col width='5%'>
	<col width='8%'>
	<col width='8%'>
	<col width='10%'>
	<col width='10%'>
	<col width='*'>

	<tr height='40' bgcolor='#ffffff'>
		<td align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
		<td align='center' class='m_td'><font color='#000000'><b><a href='javascript:sort(0);'>최초신청날짜</a>".(($filed == 'sr.regdate')?($sort == 'asc')?'▲':'▼':'')."</b></font></td>
		<td align='center' class='m_td'><font color='#000000'><b>상품이미지</b></font></td>
		<td align='center' class='m_td'><font color='#000000'><b><a href='javascript:sort(1);'>상품명</a>".(($filed == 'p.pname')?($sort == 'asc')?'▲':'▼':'')."<br>(색상)</b></font></td>
		<td align='center' class='m_td'><font color='#000000'><b>옵션명</b></font></td>
		<td align='center' class='m_td'><font color='#000000'><b><a href='javascript:sort(2);'>상품판매상태</a>".(($filed == 'p.state')?($sort == 'asc')?'▲':'▼':'')."<br>(<a href='javascript:sort(4);'>상품품절상태</a>".(($filed == 'd.option_soldout')?($sort == 'asc')?'▲':'▼':'').")</b></font></td>
		<td align='center' class='m_td'><font color='#000000'><b><a href='javascript:sort(5);'>노출상태</a>".(($filed == 'p.disp')?($sort == 'asc')?'▲':'▼':'')."</b></font></td>
		<td align='center' class='m_td'><font color='#000000'><b>재입고상태</b></font></td>
		<td align='center' class='m_td'><font color='#000000'><b>재고수량</b></font></td>
		<td align='center' class=m_td><font color='#000000'><b><a href='javascript:sort(3);'>알림요청회원수</a>".(($filed == 'ready_cnt')?($sort == 'asc')?'▲':'▼':'')."</b></font></td>
		<td align='center' class=m_td><font color='#000000'><b>알림완료회원수</b></font></td>
		<td align='center' class=e_td><font color='#000000'><b>관리</b></font></td>
	</tr>";

if($total == 0){
    $Contents .="
	<tr height=50>
		<td colspan='11' align='center'>등록된 데이타가 없습니다.</td>
	</tr>";

}else{
    for ($i = 0; $i < $total; $i++)
    {
        $db->fetch($i);

        $img_str = PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[pid], "s", '');

        $sql = "select * from shop_product where id = '".$db->dt[pid]."'";
        $mdb->query($sql);
        $mdb->fetch();
        $pname = $mdb->dt[pname];
        $state = $mdb->dt['state'];
		$add_info = $mdb->dt['add_info'];

		if($mdb->dt[disp] == 1){
			$disp = "노출";
		} else {
			$disp = "미노출";
		}

        $sql = "select * from shop_product_options_detail where id = '".$db->dt[op_id]."' ";
        $mdb->query($sql);
        $mdb->fetch();
        $option_div = $mdb->dt[option_div];
		$option_stock = $mdb->dt[option_stock];
		if($mdb->dt[option_soldout] == 1){
			$soldout = "<p>(품절)</p>";
		} else {
			$soldout = "";
		}
        //$option_gid = $mdb->dt['option_gid'];

        /*$sql = "select total_stock from inventory_goods_unit where gid = '".$option_gid."' ";
        $mdb->query($sql);
        $mdb->fetch();
        $total_stock = $mdb->dt[total_stock];*/

		if($db->dt[status] == 'N'){
			$reminderStatus = '발송요청';
		} else if($db->dt[status] == 'Y'){
			$reminderStatus = '발송완료';
		} else if($db->dt[status] == 'X'){
			$reminderStatus = '발송취소';
		} else if($db->dt[status] == 'P'){
			$reminderStatus = '입고미정';
		} else if($db->dt[status] == 'E'){
			$reminderStatus = '판매종료';
		}

		if($db->dt[ready_cnt] == 0) {
			$regdate = '';
		} else {
			$regdate = $db->dt[regdate];
		}
			//onClick='input_check_num()'
        $Contents .="
		<tr height='45' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
			<td class='list_box_td'><input type=checkbox name=sr_ix[] id='code' class='sr_ix' pid='".$db->dt[pid]."' option_id='".$db->dt['op_id']."' value='".$db->dt[sr_ix]."'></td>
			<td class='list_box_td' >".$regdate."</td>
			<td class='list_box_td' height='80px'><a href='/shop/goods_view.php?id=".$db->dt[pid]."' target='_blank' class='screenshot'  rel='".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[pid], $LargeImageSize, '')."'><img src='".$img_str."' width=50 height=50></a></td>
			<td class='list_box_td' >".$pname."<p>(".$add_info.")</p></td>
			<td class='list_box_td' >".$option_div."</td>
			<td class='list_box_td' >".$_SELL_STATUS[$state].$soldout."</td>
			<td class='list_box_td' >".$disp."</td>
			<td class='list_box_td' >".$reminderStatus."</td>
			<td class='list_box_td' >".$option_stock." 개</td>
			<!-- td class='list_box_td' >".$total_stock." 개</td -->
			<td class='list_box_td point' >".$db->dt[ready_cnt]."</td>
			<td class='list_box_td' >".$db->dt[complate_cnt]."</td>
			
			<td class='list_box_td ctr' style='padding:5px;'>
				<table border='0' cellpadding='0' cellspacing='0' align='center'>
				<tr>
				";
				$Contents.="<td>";
		        if($db->dt[status] == 'N'){
					$Contents.="
						<input type='button' value='알림발송' onclick=\"push_mail('".$db->dt['pid']."','".$db->dt['op_id']."')\" />";
		        }else{
					$Contents.="
						<input type='button' value='알림발송불가' onclick=\"push_no_mail('')\" />
					";
				}
				$Contents.="</td>";
				//<input type='button' value='알림발송' onclick=\"push_mail('".$db->dt['pid']."','".$db->dt['op_id']."')\" />
				//</td>
				//";
        $Contents.="<td>";
//        if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
//            $Contents.="
//					<a href=JavaScript:ProductQnaDelete('".$db->dt[pn_ix]."')><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
//        }else{
//            $Contents.="
//					<a href=\"".$auth_delete_msg."\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
//        }
        $Contents.="&nbsp;</td>";

        $Contents.="
				</tr>
				</table>";


        $Contents .= "
			</td>
		</tr>";
    }
}

$Contents .= "
</table>
<table> 
    <tr>
        <td>
            <input type='button' value='선택상품 일괄알림발송' onclick=\"push_mail_all()\"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<select name=reminder_status id=reminder_status>
					<option value='N' >발송요청</option>
					<option value='Y' >발송완료</option>
					<option value='X' >발송취소</option>
					<option value='P' >입고미정</option>
					<option value='E' >판매종료</option>
			</select>
            <input type='button' value='선택상품 재입고상태변경' onclick=\"reminder_status_all()\"/>
        </td>
    </tr>
</table>
";

$Script = "
<script>
    function push_mail(pid,op_id){
          window.frames['act'].location.href='./product_stock_reminder.act.php?act=push&pid='+pid+'&op_id='+op_id;
//          location.href='./product_stock_reminder.act.php?act=push&pid='+pid+'&op_id='+op_id;
    }

	function push_no_mail(){
		alert('재입고 알림발송이 불가상태 입니다.');
        return false;
	}

    function push_mail_all(){
        if(confirm('선택상품을 입고알림 처리 하시겠습니까?')){
            var srIx = [];
            var checkBool = false;
            $('.sr_ix').each(function(){            
               if($(this).is(':checked')){
                   var sr_ix = $(this).val();         
                   srIx.push(sr_ix);
                   checkBool = true;
                   //console.log('/product_stock_reminder.act.php?act=pushAll&pid='+pid+'&op_id='+op_id);
                   
               } 
            });
            if(checkBool == false){
                alert('선택된 상품이 없습니다. 재입고알림을 발송하고자 하는 상품을 선택해주세요.');
                return false;
            }
            if(srIx.length > 0){
                window.frames['act'].location.href='./product_stock_reminder.act.php?act=pushAll&srIx='+srIx;
            }      
            alert('재입고알림이 발송되었습니다.');
            location.reload();
        }
    }

	function reminder_status_all(){
        if(confirm('선택상품의 재입고상태변경을 처리 하시겠습니까?')){
            var srIx = [];
            var checkBool = false;
            $('.sr_ix').each(function(){            
               if($(this).is(':checked')){
                   var sr_ix = $(this).val();
                   srIx.push(sr_ix);
                   checkBool = true;
               } 
            });
            if(checkBool == false){
                alert('선택된 상품이 없습니다. 재입고상태를 변경하고자 하는 상품을 선택해주세요.');
                return false;
            }

            if(srIx.length > 0){

				var allData = { 'act': 'statusAll', 'srIx': srIx,'status': $('#reminder_status option:selected').val() };

				$.ajax({
					url:'./product_stock_reminder.act.php',
					type:'POST',
					data: allData,
					success:function(data){
						alert('재입고상태가 변경되었습니다.');
						location.reload();
					},error:function(jqXHR, textStatus, errorThrown){
						alert('에러 발생~~' + textStatus + ' : ' + errorThrown);
					}
				});

                //window.frames['act'].location.href='./product_stock_reminder.act.php?act=statusAll&srIx='+srIx+'&status='+$('#reminder_status option:selected').val();
				//document.statusFrm.submit();
            }      
            //alert('재입고상태가 변경되었습니다.');
            //location.reload();
        }
    }
    
    
    function clearAll(frm){
        for(i=0;i < frm.code.length;i++){
                frm.code[i].checked = false;
        }
    }
    
    function checkAll(frm){
        for(i=0;i < frm.code.length;i++){
                frm.code[i].checked = true;
        }
    }
    
    function fixAll(frm){
        if (!frm.all_fix.checked){
            clearAll(frm);
            frm.all_fix.checked = false;
                
        }else{
            checkAll(frm);
            frm.all_fix.checked = true;
        }
        //input_check_num();
    }

	function sort(val){
		if($('#sort').val() == '' || $('#sort').val() == 'desc'){
			$('#sort').val('asc');
		}else{
			$('#sort').val('desc');
		}
		if(val == 0){
			$('#filed').val('sr.regdate');
		} else if(val == 1) {
			$('#filed').val('p.pname');
		} else if(val == 2) {
			$('#filed').val('p.state');
		} else if(val == 3) {
			$('#filed').val('ready_cnt');
		} else if(val == 4) {
			$('#filed').val('d.option_soldout');
		} else if(val == 5) {
			$('#filed').val('p.disp');
		}
		document.searchmember.submit();
	}

</script>
";
    
$P = new LayOut;
$P->addScript = "$Script";
$P->strLeftMenu = product_menu();
$P->OnloadFunction = "";
$P->Navigation = "상품관리 > 재입고알림";
$P->title = "재입고알림";
$P->strContents = $Contents;
$P->PrintLayOut();
