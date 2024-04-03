<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");

// /bbs/bbs.php 에 print_bbs 선언되어 있음


$db=new Database();
/*
$msb = new MsBoard($bbs_table_name);
$msb->bbs_admin_mode = true;
$msb->MsBoardConfigration($P);
$msb->bbs_template_dir = $bbs_template_dir;
$msb->bbs_compile_dir  = $bbs_compile_dir;
$msb->site_template_src  = $site_template_src;
$msb->bbs_data_dir = $bbs_data_dir;
*/
if(!$_GET["board"]){
	$db->query("select bmc.* , bg.div_ix, bg.div_name as group_name from bbs_manage_config bmc , bbs_group bg where bmc.board_group = bg.div_ix and disp = 1 and bmc.board_style = 'bbs' and bmc.recent_list_display = 'Y' and div_ix = 7 limit 1 ");
	$db->fetch();
	$board = $db->dt[board_ename];
	//$bbs_menes = $mdb->fetchall();
}

//$db->debug = true; 
$db->query("SELECT board_name FROM bbs_manage_config where board_ename= '".$board."'");
$db->fetch();
$board_name = $db->dt[board_name];

if($mmode == "personalization"){
	$P = new ManagePopLayOut(); 
	$P->strLeftMenu = cscenter_menu();
	$P->Navigation = "고객센타 > 게시판관리(리스트)";
	$P->title = $board_name;
    $P->NaviTitle = $board_name;
	$P->strContents = print_bbs($board,$_GET["mode"], $_GET["act"],NULL, true);
	$P->layout_display = false;
	$P->view_type = "personalization";
	echo $P->PrintLayOut();
}else{
	if($mmode == "pop"){	//게시판 게시글을 클릭시 팝업으로 창띄우기 2014-10-08 이학봉
		$P = new ManagePopLayOut();
		$P->Navigation = "게시판관리 > 게시판관리(리스트)";
		$P->NaviTitle = $board_name;
		$P->title = $board_name;
		$P->strContents = print_bbs($_GET["board"],$_GET["mode"], $_GET["act"],NULL, true); //$msb->PrintMsBoardList();
		echo $P->PrintLayOut();
	}else{
		$P = new LayOut();
		$P->strLeftMenu = cscenter_menu();
		$P->strContents = print_bbs($board,$_GET["mode"], $_GET["act"],NULL, true); //$msb->PrintMsBoardList();
		$P->Navigation = "고객센타 > 게시판관리(리스트)";
		$P->title = $board_name;
		/*if($mode == "write" || $mode == "modify"){
			$P->prototype_use = false;
		}*///kbk
		echo $P->PrintLayOut();
	}
}
?>
