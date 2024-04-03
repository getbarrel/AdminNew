<?
include("../../class/database.class");
include("../logstory/class/sharedmemory.class");

//[S] 리셀러 데이터 저장
include ($_SERVER["DOCUMENT_ROOT"]."/admin/reseller/reseller.lib.php");
$reseller_data = resellerShared("insert", $_POST); // act, 저장 데이터
//[E] 리셀러 데이터 저장

echo "
	<script type='text/javascript'>
		alert('정상적으로 수정되었습니다.');
		history.go(-1);
	</script>
";

exit;

?>