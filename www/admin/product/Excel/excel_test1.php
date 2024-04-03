<?
///////////////// CREATE EXCEL FILE METHOD ///////////////// 
function xlsBOF() {
echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);  
return;
}
function xlsEOF() {
echo pack("ss", 0x0A, 0x00);
return;
}
function xlsWriteNumber($Row, $Col, $Value) {
echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
echo pack("d", $Value);
return;
}
function xlsWriteLabel($Row, $Col, $Value, $lang='' ) {
$lang = ($lang)? $lang:'euc-kr';
$Value = mb_convert_encoding($Value,$lang,"utf-8");
$L = strlen($Value);
echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
echo $Value;
return;
}
///////////////////// END //////////////////////////
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

header('Pragma: public');
header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');     // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0');    // HTTP/1.1
header('Content-Type: text/plain; charset=UTF-8');
header('Content-Transfer-Encoding: binary');
header('Content-Type: application/vnd.ms-excel;');                 // This should work for IE & Opera
header("Content-type: application/x-msexcel");                    // This should work for the rest
header('Content-Disposition: attachment; filename="download_filename.xls"');

$db = new Database;
$db->query("select * from shop_product order by regdate desc");
xlsBOF();


xlsWriteLabel(0,0,"이름");
xlsWriteLabel(0,1,"아이디");
xlsWriteLabel(0,2,"우편번호");
xlsWriteLabel(0,3,"title3");
//------ row 1 data ------ 
for($i=0;$i<$db->total;$i++){
	$db->fetch($i);
	xlsWriteLabel(($i+1), 0, $db->dt[pname]);
	xlsWriteLabel(($i+1), 1, $db->dt[id]);
	xlsWriteLabel(($i+1), 2, $db->dt[zip]);
	xlsWriteLabel(($i+1), 3, "TEXT3");
}


xlsEOF();

?>