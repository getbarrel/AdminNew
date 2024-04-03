<? 
include($_SERVER["DOCUMENT_ROOT"]."/admin/work/work_project_architecture.lib.php");


if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = work_project_menu();
	$P->Navigation = "프로젝트 관리 > 프로젝트 공정단계 관리";
	$P->NaviTitle = "프로젝트 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();	
}else if($mmode == "inner_list"){
	echo $innerview;
}else{
	$P = new ProjectLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = work_project_menu();
	$P->Navigation = "프로젝트 관리 > 프로젝트 공정단계 관리";
	$P->NaviTitle = "프로젝트 관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();	
}
