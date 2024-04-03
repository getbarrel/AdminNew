<?
include("../class/layout.class");


/*
if ($FromYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-30, date("Y"));

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;ss
}
*/

$max = 20; //페이지당 갯수

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}

$db = new Database;

//$where = " where pr_ix is not null ";
if($pg_ix){
	$pg_ix_str = " and d.pg_ix = '$pg_ix' ";
}


$sql = "SELECT count(*) as total
		from shop_poll_result d
		where pr_ix is not null $pg_ix_str
		";
$db->query($sql);


$db->fetch();
$total = $db->dt[total];
//echo $total;


$sql = "SELECT a.pg_ix,a.g_title,b.title, b.pt_ix, d.regdate as poll_regdate, c.pf_ix as pf_ix,  c.fielddesc as result,AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,cmd.sex_div, d.poll_type
		from shop_poll_group a ,shop_poll_title b , shop_poll_field c , shop_poll_result d, common_member_detail cmd
		where a.pg_ix = b.pg_ix
		and  b.pt_ix = c.pt_ix
		and c.pt_ix = d.pt_ix
		and c.pf_ix = d.result
		and d.poll_type = '1'
		and d.mem_ix = cmd.code
		$pg_ix_str
		union
		SELECT a.pg_ix,a.g_title,b.title, b.pt_ix, d.regdate as poll_regdate, '' as pf_ix, d.result as result,AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,cmd.sex_div, d.poll_type
		from shop_poll_group a ,shop_poll_title b ,  shop_poll_result d,  common_member_detail cmd
		where a.pg_ix = b.pg_ix
		and b.pt_ix = d.pt_ix
		and d.poll_type = '2'
		and d.mem_ix = cmd.code
		$pg_ix_str
		order by poll_regdate desc
		limit $start , $max";//
//echo nl2br($sql);
//$sql = "SELECT id,g_title,regdate from shop_poll_group order by regdate desc limit $start , $max";

$db->query($sql);

if($mode == "excel"){
	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-Disposition: attachment; filename=poll_result.xls" );
	header( "Content-Description: Generated Data" );


	if($db->total){
		//echo "NO\t업체명\t은행명\t계좌번호\t입금자명\t정산일자\t판매건수\t배송비\t판매총액(할인가기준)\t수수료\t정산금액\t정산상태\n";

			$mstring = "<table border=1 style='font-size:12px;'>";
			$mstring .= "<tr align=center>
									<td >NO</td>
									<td >설문그룹제목</td>
									<td >설문그룹코드</td>
									<td >설문자</td>
									<td >성별</td>
									<td >설문제목</td>
									<td >설문구분</td>
									<td >설문제목코드</td>
									<td >답변</td>
									<td >답변코드</td>
									<td >등록일자</td>
									</tr>";

		for ($i = 0; $i < $db->total; $i++){
			$db->fetch($i);

				$mstring .= "<tr><td>".($i+1)."</td>";
				$mstring .= "<td>".$db->dt[g_title]."</td>";
				$mstring .= "<td>".$db->dt[pg_ix]."</td>";
				$mstring .= "<td>".$db->dt[name]."</td>";
				$mstring .= "<td>".$db->dt[sex_div]."</td>";
				$mstring .= "<td>".$db->dt[title]."</td>";
				$mstring .= "<td>".($db->dt[poll_type] == "1" ? "격관식":"주관식")."(".$db->dt[poll_type].")</td>";
				$mstring .= "<td>".$db->dt[pt_ix]."</td>";
				$mstring .= "<td>".$db->dt[result]."</td>";
				$mstring .= "<td>".$db->dt[pf_ix]."</td>";
				$mstring .= "<td>".$db->dt[poll_regdate]."</td>";
				$mstring .= "</tr>";

		}

	}

	if($acc_view_type == "report"){
		$mstring .= "</table>";
	}

	echo iconv("utf-8","CP949",$mstring);
	exit;
}

$mstring = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<tr>
	    <td align='left'> ".GetTitleNavigation("설문관리", "마케팅지원 > 설문관리")."</td>
	</tr>
	<tr>
	    <td align='left' colspan=5 style='padding-bottom:20px;'>
	        <div class='tab'>
	            <table class='s_org_tab' style='width:100%'>
	                <tr>
	                    <td class='tab'>
	                        <table id='tab_01'  >
	                            <tr>
	                                <th class='box_01'></th>
	                                <td class='box_02' onclick=\"document.location.href='poll_list.php'\">설문 리스트</td>
	                                <th class='box_03'></th>
	                            </tr>
	                        </table>
	                        <table id='tab_02' >
	                            <tr>
	                                <th class='box_01'></th>
	                                <td class='box_02' onclick=\"document.location.href='poll_group.php'\">설문 그룹 만들기</td>
	                                <th class='box_03'></th>
	                            </tr>
	                        </table>
													<table id='tab_03' class=on>
	                            <tr>
	                                <th class='box_01'></th>
	                                <td class='box_02' onclick=\"document.location.href='poll_result.php'\">설문 결과보기</td>
	                                <th class='box_03'></th>
	                            </tr>
	                        </table>
	                    </td>
	                    <td class='btn' align=right>

												<a href='?mode=excel&".$QUERY_STRING."'><img src='../image/btn_excel_save.gif' border=0></a>

	                    </td>
	                </tr>
	            </table>
	        </div>
	    </td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box' style='table-layout:fixed;'>
	<tr bgcolor=#efefef align=center height=27>
		<td class='s_td' width=5%>번호</td>
		<td class='m_td' width=30%>설문그룹제목</td>
		<td class='m_td' width=7%>설문자</td>
		<td class='m_td' width=30%>설문제목</td>
		<td class='m_td' width=20%>답변</td>
		<td class='e_td' width=13%>등록일자</td>
	</tr>";

if($db->total){
	for($i=0;$i< $db->total;$i++){
		$db->fetch($i);

		$pg_ix = $db->dt[pg_ix];

		$result = $db->dt[result];

		if($result == ""){
			$result = 0;
		}

		$regdate = $db->dt[regdate];



		$no = $total - ($page - 1) * $max - $i;

		$mstring .="<tr height=27 align=center>
					<td bgcolor=#efefef>".$no."</td>
					<td style='padding-left:10px;'  align=left>".$db->dt[g_title]."</td>
					<td bgcolor=#efefef align=center>".$db->dt[name]."</td>
					<td bgcolor=#ffffff style='padding-left:10px;' align=left>".$db->dt[title]."</td>
					<td bgcolor=#efefef style='padding-left:10px;' align=left>".$db->dt[result]."</td>
					<td bgcolor=#ffffff class=small>
					".$db->dt[poll_regdate]."
					</td>
				</tr>";
	}
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
	$mstring .="<tr height=40><td colspan=6 align=center>".page_bar($total, $page, $max,$query_string,"")."</td></tr>";

}else{
	$mstring .= "<tr height=50><td colspan=6 align=center style='padding-top:10px;'>작성된 설문이 없습니다.</td></tr>";
}
//	$mstring .= "<tr height='50'><td colspan='6' align='right'><a href='poll.php'><img src='../image/b_poll.gif' border='0'></a></td></tr>";


$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >설문결과에 대한 히스토리 목록입니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >설문 한 문항에 대한 결과치는 설문 수정하기 페이지에서 확인 하실 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >설문결과를 엑셀로 다운 받으셔서 가공하여 사용하시면 됩니다.</td></tr>
</table>
";


//$help_text = HelpBox("게시판 관리", $help_text);
$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<div style='position:relative;top:4px;'><table><tr><td valign=bottom><b>설문 결과보기</b></td><td><a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_메일SMS리스트관리(090322)_config.xml',800,517,'manual_view')\"  title='메일/SMS 관리 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a></td></tr></table></div>", $help_text,200)."</div>";

	$mstring .= "</table><table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'><tr height='50'><td colspan='6' align='right'>$help_text</td></tr>";

$mstring .="</table>";



$Contents = $mstring;


if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->Navigation = "마케팅지원 > 설문결과보기";
	$P->NaviTitle = "설문결과보기";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{

	$P = new LayOut;
	//$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n$Script";
	$P->strLeftMenu = display_menu();
	$P->OnloadFunction = "onLoad('$sDate', '$eDate');";
	$P->Navigation = "마케팅지원 > 설문결과보기";
	$P->title = "설문결과보기";
	$P->strContents = $Contents;
	$P->PrintLayOut();
}




?>