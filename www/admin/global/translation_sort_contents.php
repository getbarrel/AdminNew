<?

ini_set('memory_limit', -1);
set_time_limit(0);

include_once($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");

$db = new MySQL;

$sql = "UPDATE global_translation SET file_path = '', file_short_content = ''";
$db->query($sql);

$sql = "SELECT trans_key FROM global_translation GROUP BY trans_key";
$db->query($sql);
$translation = $db->fetchall();

for($i=0; $i<count($translation); $i++)
{
	$trans_key = $translation[$i]["trans_key"];
	fileShortContent($trans_key, "php");
	fileShortContent($trans_key, "htm", "templet");
	fileShortContent($trans_key, "htm", "mobile_templet");
	fileShortContent($trans_key, "htm", "bbs_templet");
}

function fileShortContent($trans_key, $extension, $templet=NULL)
{
	$db = new MySQL;
	$path = $_SERVER["DOCUMENT_ROOT"];

	if($extension != "php")
	{
		//htm 파일일때는 data 밑의 상세경로까지 이동
		$path .= $_SESSION["admin_config"]["mall_data_root"] . $templet;
	}
	else
	{
		//php 파일일때는 $noPath 를 제외하고 검색
		$noPath = array("admin", "data");
		$noPathRoot = "";
		for($i=0; $i<count($noPath); $i++)
		{
			$noPathRoot .= " \( -name " .$noPath[$i]. " -prune \) -o ";
		}
	}

	$exec = "find " . $path . $noPathRoot . " -name '*." . $extension . "' | xargs grep '" . $trans_key . "'";
	$results = shell_exec($exec);

	if(empty($results))
	{
		$file_short_content = "결과없음";
		$file_path = "결과없음";
	}
	else
	{
		$datas = split("\n",$results);
		$dataArr = "";
		$file_short_content = "";

		//[S] file_path 생성
		$pathData = str_replace($_SERVER["DOCUMENT_ROOT"], "", $datas[0]);
		$pathData = explode($extension, $pathData);
		$pathData = $pathData[0].$extension;
		$basename = basename($pathData);
		$file_path = explode($basename, $pathData);
		$file_path = str_replace("\"", "'", $file_path[0]);
		//[E] file_path 생성

		//[S] file_short_content 생성
		for($i=0; $i<count($datas); $i++)
		{
			$dataArr = explode($extension, $datas[$i]);
			$file_short_content .= $dataArr[1];
		}

		$file_short_content = str_replace(":", "|", $file_short_content);
		$file_short_content = str_replace("\"", "'", $file_short_content);

		//[E] file_short_content 생성
	}

	$sql = "SELECT file_path, file_short_content FROM global_translation WHERE trans_key = '".$trans_key."' LIMIT 1";
	$db->query($sql);
	$db->fetch();

	if($db->dt["file_path"] == "" || $db->dt["file_path"] == "결과없음")
	{
		$file_path_str = " file_path = \"".$file_path."\" , ";
	}
	else
	{
		$file_path_str = "";
	}

	if($db->dt["file_short_content"] != "" && $db->dt["file_short_content"] != "결과없음")
	{
		$file_short_content = $db->dt["file_short_content"] . $file_short_content;
	}

	$sql = "UPDATE global_translation SET
				" . $file_path_str . "
				file_short_content = \"".$file_short_content."\"
			WHERE trans_key = '".$trans_key."'";
	$db->query($sql);
}

echo "
	<script type='text/javascript'>
		alert('데이터 등록이 완료되었습니다.');
		location.href='/admin/global/translation.php';
	</script>
";

?>