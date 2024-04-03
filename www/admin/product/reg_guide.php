<?
include("../class/layout.class");
//include($_SERVER["DOCUMENT_ROOT"]."/class/bbs.lib.php");
//include_once($_SERVER["DOCUMENT_ROOT"]."/include/sns.config.php");

$db = new Database;


$sql = "select * from shop_mandatory_info order by mi_ix ASC";
$db->query($sql);
$data_array = $db->fetchall();

for($j=0;$j<count($data_array);$j++){

$mstring .="
	<table width='100%' cellpadding=0 cellspacing=0 border='0'>
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b >".$data_array[$j][mandatory_name]."</b> 코드 : ".$data_array[$j][mi_ix]."
			</td>
		</tr>
	</table>

	<table width='100%' cellpadding=0 cellspacing=0 bgcolor=silver border='0' class='list_table_box' id='mandatory_detail'  >
	<col width='15%'/>
	<col width='8%'/>
	<col width='8%'/>
	<col width='25%'/>
	<col width='*'/>
	<tr height=25 bgcolor='#ffffff' align=center>
		<td bgcolor=\"#efefef\" class=small> 상품고시명</td>
		<td bgcolor=\"#efefef\" class=small> 코드</td>
		<td bgcolor=\"#efefef\" class=small> 상세코드</td>
		<td bgcolor=\"#efefef\" class=small> 상품정보 고시명 </td>
		<td bgcolor=\"#efefef\" class=small> 설명</td>
	</tr>";

$sql = "select * from shop_mandatory_detail where mi_ix = '".$data_array[$j][mi_ix]."' order by seq asc ";
$db->query($sql);
$mandatory_detail_array = $db->fetchall();

for($i =0;$i<count($mandatory_detail_array);$i++){

	$no = $i + 1;
	$mstring .="
	<tr height=30  bgcolor='#ffffff' align=center depth=1 id='mandatory_detail_tr'>";
	if($i == 0){
	$mstring .="<td class='list_box_td' rowspan='".count($mandatory_detail_array)."'>".$data_array[$j][mandatory_name]."</td>
				<td class='list_box_td' rowspan='".count($mandatory_detail_array)."'>".$data_array[$j][mi_ix]."</td>";
	}
	$mstring .="
		<td class='list_box_td'>".$mandatory_detail_array[$i][detail_code]."</td>
		<td class='list_box_td'>".$mandatory_detail_array[$i][mid_title]."</td>
		<td class='list_box_td'>".$mandatory_detail_array[$i][mid_comment]."</td>

	</tr>";
}
$mstring .="
	</table>";

}

$Script = "
<SCRIPT type='text/javascript'>
<!--

//-->
</SCRIPT>
";

$P = new ManagePopLayOut();
$P->strLeftMenu = inventory_menu();
$P->addScript = $Script;
$P->Navigation = "상품관리 > 대량상품등록 > 상품정보고시 가이드 ";
$P->NaviTitle = "상품정보고시 가이드 ";
$P->strContents = $mstring;
$P->jquery_use = false;

$P->PrintLayOut();


?>