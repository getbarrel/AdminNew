<?php
include ("../class/layout.class");
include ("sellertool.lib.php");
include ("../openapi/openapi.lib.php");

if(! empty($_REQUEST['api_key'] )){
	$site_code = getSiteCodeByApiKey( $_REQUEST['api_key'] );
}
if( ! empty($site_code)){
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/admin/sellertool/site_add_info_input_' . $site_code . '.php?add_info_id=' . $_REQUEST['add_info_id']);
}


//제휴사 select page
$Contents = "
<style>
.list li{
	margin-top:10px;
	font-size : 30px;
	font-weight : bold;
	padding : 5px;
	width : 200px;
	height : 100px;
	float : left;
	border-style:outset;
	border-width:4px;
	text-align : center;
}
.site_title{
	padding-top : 30px;
	font-size: 20px;
}
</style>
<div>
	<span>제휴사 선택</span>
	<ul class='list'>
		<li>
			<a href=\"javascript:alert('준비중 입니다.');\">
				<div class='site_title'>
					11st
				</div>
			</a>
		</li>
		<li>
			<a href='/admin/sellertool/site_add_info_input_auction.php'>
				<div class='site_title'>
					Auction
				</div>
			</a>
		</li>
		<li>
			<!--a href='/admin/sellertool/site_add_info_input_shopn.php'-->
			<a href=\"javascript:alert('준비중 입니다.');\">
				<div class='site_title'>
					Shop N
				</div>
			</a>
		</li>
	</ul>
</div>
";

if ($mmode == "pop") {
	$P = new ManagePopLayOut ();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu ();
	$P->Navigation = "셀러툴 > 상품등록 옵션 등록";
	$P->title = "상품등록 옵션 등록";
	$P->strContents = $Contents;
	echo $P->PrintLayOut ();
} else {
	$P = new LayOut ();
	$P->addScript = $Script;
	$P->strLeftMenu = sellertool_menu ();
	$P->Navigation = "셀러툴 > 상품등록 옵션 등록";
	$P->title = "상품등록 옵션 등록";
	$P->strContents = $Contents;
	echo $P->PrintLayOut ();
}