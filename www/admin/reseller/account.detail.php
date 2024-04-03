<?
include("../class/layout.class");

$db = new Database;

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

$where = " r.ac_ix = '".$ac_ix."' AND r.ac_ix IS NOT NULL ";

$sql = "SELECT SUM(r.incentive) as incentive FROM reseller_incentive r WHERE ".$where." LIMIT 1";
$db->query($sql);
$db->fetch();
$total_incentive = $db->dt[incentive];

$sql = "SELECT
			r.regdate,
			r.incentive,
			r.incentive_rate,
			o.bname,
			o.buserid,
			od.pname,
			(SELECT id FROM common_user u WHERE u.code = r.rsl_code) as manager_id
		FROM
			reseller_incentive r
		INNER JOIN reseller_policy p ON r.rsl_code = p.rsl_code
		INNER JOIN shop_order o ON r.oid = o.oid
		LEFT JOIN shop_order_detail od ON r.oid = od.oid
		WHERE
			".$where."
		GROUP BY r.od_ix";

$db->query($sql);
$total = $db->total;

//[S] 페이징
$str_page_bar = page_bar($total, $page,$max, "&search_type=".$search_type."&search_text=".$search_text."&FromYY=".$FromYY."&FromMM=".$FromMM."&FromDD=".$FromDD."&ToYY=".$ToYY."&ToMM=".$ToMM."&ToDD=".$ToDD."&rsl_div=".$rsl_div,"view");
//[E] 페이징

$sql .= " ORDER BY r.regdate DESC LIMIT ".$start." , ".$max;
$db->query($sql);
$db->fetch();

$Contents = "

<table width='100%' border='0' cellpadding='0' cellspacing='0' class='list_table_box'>
	<tbody>
		<tr height='28' bgcolor='#ffffff' align='center'>
			<td width='5%' class='m_td'><font color='#000000'><b>순서</b></font></td>
			<td width='12%' class='m_td'><font color='#000000'><b>주문일</b></font></td>
			<td width='10%' class='m_td'><font color='#000000'><b>구매자 이름</b></font></td>
			<td width='10%' class='m_td'><font color='#000000'><b>구매자 아이디</b></font></td>
			<td width='10%' class='m_td'><font color='#000000'><b>리셀러 아이디</b></font></td>
			<td width='*' class='m_td'><font color='#000000'><b>상품명</b></font></td>
			<td width='8%' class='m_td'><font color='#000000'><b>Reseller</b></font></td>
			<td width='12%' class='m_td'><font color='#000000'><b>정산금액</b></font></td>
		</tr>
";

	for($i=0; $i<$db->total; $i++){

		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;

		$Contents .= "
			<tr height='28'>
				<td class='list_box_td point'>".$no."</td>
				<td class='list_box_td point'>".substr($db->dt[regdate],0,10)."<br/>".substr($db->dt[regdate],10,9)."</td>
				<td class='list_box_td point'>".$db->dt[bname]."</td>
				<td class='list_box_td point'>".$db->dt[buserid]."</td>
				<td class='list_box_td'>".$db->dt[manager_id]."</td>
				<td class='list_box_td' style='padding:5px;'>".$db->dt[pname]."</td>
				<td class='list_box_td'>".number_format($db->dt[incentive_rate])."%</td>
				<td class='list_box_td'>".number_format($db->dt[incentive])."원</td>
			</tr>
		";
	}

$Contents .= "
		<tr height='28'>
			<td class='m_td' colspan='7'><font color='#000000'><b>합계</b></font></td>
			<td class='list_box_td'>".number_format($total_incentive)."원</td>
		</tr>
	</tbody>
</table>
<div style='width:100%;text-align:right;padding:10px 0px;'>".$str_page_bar."</div>

";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "정산 상세내역";
$P->NaviTitle = "정산 상세내역";
$P->title = "정산 상세내역";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>

