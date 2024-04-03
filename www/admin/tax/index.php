<script>
location.href = "/admin/tax/sales_list.php";
</script>
<?
	die;
	include("../class/layout.class");
 	$db = new Database;

	include_once $DOCUMENT_ROOT."/admin/tax/test_header.php";

	$Contents = "
	<script>
	$(document).ready(function(){
		$('#tax_tab1').click(function(){
			$('#tab1_view').slideDown();
		});
	});
	</script>
	";

	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = tax_menu();
	$P->prototype_use = false;
	$P->jquery_use = true;
	$P->Navigation = "HOME > 세금계산서관리 > 세금계산서 안내";
	$P->strContents = $Contents;

	echo $P->PrintLayOut();
?>