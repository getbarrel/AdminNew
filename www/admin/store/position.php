<?
include("../class/layout.class");

$use_type = "mallstory";
include("./position.common.php");


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->Navigation = "관리자설정 > 직급관리";
$P->title = "직급관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();



/*

create table shop_company_position (
ps_ix int(4) unsigned not null auto_increment  ,
ps_name varchar(20) null default null,
ps_level int(2)  default '9' ,
sale_rate varchar(20) null default null,

disp char(1) default '1' ,
regdate datetime not null,
primary key(ps_ix));
*/
?>