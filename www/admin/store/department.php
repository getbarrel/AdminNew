<?
include("../class/layout.class");

$use_type = "mallstory";
include("./department.common.php");

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->Navigation = "상점관리 > 관리자설정 > 부서관리";
$P->title = "부서관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();



/*

create table shop_company_department (
dp_ix int(4) unsigned not null auto_increment  ,
dp_name varchar(20) null default null,
dp_level int(2)  default '9' ,
sale_rate varchar(20) null default null,

disp char(1) default '1' ,
regdate datetime not null,
primary key(dp_ix));
*/
?>