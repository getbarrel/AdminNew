<?
include("../class/layout.class");
include_once("md.lib.php");



$Contents = "<div class='video_area'>
	<h1>
		<img src='../v3/images/".$admininfo["language"]."/video_title.gif' alt='' title='' />
	</h1>
	<div class='video_box'>
		<div class='video'>
			<iframe name='tutorial_movie' id='tutorial_movie' width=\"722\" height=\"362\" src=\"http://www.youtube.com/embed/ZQ5i8DeQvXw?hd=1&autoplay=1\" style='border:1px solid silver' frameborder=\"0\" allowfullscreen></iframe>
			<!--object width=\"722\" height=\"315\" name='tutorial_movie' id='tutorial_movie'>
				<param name=\"movie\" value=\"http://www.youtube.com/v/ZQ5i8DeQvXw?version=3&amp;hl=ko_KR&autoplay=1\"></param>
				<param name=\"allowFullScreen\" value=\"true\"></param>
				<param name=\"allowscriptaccess\" value=\"always\"></param>
				<embed src=\"http://www.youtube.com/v/ZQ5i8DeQvXw?version=3&amp;hl=ko_KR&autoplay=1\" type=\"application/x-shockwave-flash\" width=\"722\" height=\"362\" allowscriptaccess=\"always\" allowfullscreen=\"true\"></embed>
				</object-->
			
			

			<p>
				<a href='#'>주소복사</a> &nbsp; l &nbsp;
				<a href='#'>소스복사</a> &nbsp; l &nbsp;
				<a href='#'>카페담기</a> &nbsp; l &nbsp;
				<a href='#'>블러그담기</a>
			</p>
		</div>
	</div>
	<ul class='video_list'>
		<li>
			<img src='../v3/images/".$admininfo["language"]."/v_step01_on.gif' on_src='../v3/images/".$admininfo["language"]."/v_step01_on.gif' off_src='../v3/images/".$admininfo["language"]."/v_step01_off.gif' play_url='http://www.youtube.com/embed/ZQ5i8DeQvXw?hd=1&autoplay=1' alt='' title='' class='tutorial_step' onclick=\"ViewTutorial($(this));\" style='cursor:pointer;'/>
		</li>
		<li>
			<img src='../v3/images/".$admininfo["language"]."/v_step02_off.gif' on_src='../v3/images/".$admininfo["language"]."/v_step02_on.gif'  off_src='../v3/images/".$admininfo["language"]."/v_step02_off.gif' play_url='http://www.youtube.com/embed/X2PQfJfDa8s?hd=1&autoplay=1'  alt='' title='' class='tutorial_step'  onclick=\"ViewTutorial($(this));\" style='cursor:pointer;' />
		</li>
		<li>
			<img src='../v3/images/".$admininfo["language"]."/v_step03_off.gif' on_src='../v3/images/".$admininfo["language"]."/v_step03_on.gif' off_src='../v3/images/".$admininfo["language"]."/v_step03_off.gif' play_url='http://www.youtube.com/embed/zWNfIcDAtJs?hd=1&autoplay=1' alt='' title='' class='tutorial_step' onclick=\"ViewTutorial($(this));\" style='cursor:pointer;' />
		</li>
		<li>
			<img src='../v3/images/".$admininfo["language"]."/v_step04_off.gif' on_src='../v3/images/".$admininfo["language"]."/v_step04_on.gif' off_src='../v3/images/".$admininfo["language"]."/v_step04_off.gif' 
			play_url='http://www.youtube.com/embed/SQrU6WwyW7o?hd=1&autoplay=1' alt='' title='' class='tutorial_step' onclick=\"ViewTutorial($(this));\" style='cursor:pointer;'/>
		</li>
		<li>
			<img src='../v3/images/".$admininfo["language"]."/v_step05_off.gif' on_src='../v3/images/".$admininfo["language"]."/v_step05_on.gif' off_src='../v3/images/".$admininfo["language"]."/v_step05_off.gif' 
			play_url='http://www.youtube.com/embed/MIXwtD5JkBw?hd=1&autoplay=1' alt='' title='' class='tutorial_step'onclick=\"ViewTutorial($(this));\" style='cursor:pointer;' />
		</li>
		<li>
			<img src='../v3/images/".$admininfo["language"]."/v_step06_off.gif' on_src='../v3/images/".$admininfo["language"]."/v_step06_on.gif' off_src='../v3/images/".$admininfo["language"]."/v_step06_off.gif' 
			play_url='http://www.youtube.com/embed/tOmcG6RoKPA?hd=1&autoplay=1' alt='' title='' class='tutorial_step' onclick=\"ViewTutorial($(this));\" style='cursor:pointer;' />
		</li>
		<li>
			<img src='../v3/images/".$admininfo["language"]."/v_step07_off.gif' on_src='../v3/images/".$admininfo["language"]."/v_step07_on.gif' off_src='../v3/images/".$admininfo["language"]."/v_step07_off.gif' play_url='http://www.youtube.com/embed/v8PjsATCifs?hd=1&autoplay=1'  alt='' title='' class='tutorial_step' onclick=\"ViewTutorial($(this));\" style='cursor:pointer;' />
		</li>
	</ul>
</div>";
 $Script = "
 <style type='text/css'>
*{padding:0;margin:0;}
ul li	{list-style:none;}
.video_area	{width:804px;margin:0 auto;}
.video_area	h1	{text-align:center;margin-bottom:20px;}
.video_list	{width:100%;margin-top:25px;}
.video_list:after	{content:\"\";display:block;clear:both;}
.video_list	li	{float:left;}
.video_box	{background:url(../v3/images/".$admininfo["language"]."/video_bg.gif) no-repeat; width:804px;height:444px;}
.video	{padding-top:30px;width:722px;margin:0 auto;}
.video	p	{font-size:12px;text-align:right;padding:10px 0;}
.video	p	a	{color:#7e7e7e;text-decoration:none;}
</style>
 <script language='javascript'>
 function ViewTutorial(jquery_obj){
	$('.tutorial_step').each(function(){
		$(this).attr('src',$(this).attr('off_src'));
	});
	jquery_obj.attr('src',jquery_obj.attr('on_src'));
	$('#tutorial_movie').find('param[name=movie]').attr('value',jquery_obj.attr('play_url'));
	//alert($('#tutorial_movie').attr('src'));
	$('#tutorial_movie').attr('src',jquery_obj.attr('play_url'));
	//alert($('#tutorial_movie').find('embed').attr('src'));
	//$('#tutorial_movie').find('embed').attr('src',jquery_obj.attr('play_url'));


}
 </script>
 ";


$P = new LayOut();
$P->addScript = $Script;
$P->TitleBool = false;
$P->RightMenuBool = false;
$P->LeftMenuBool = false;

$P->strLeftMenu = store_menu();
$P->Navigation = "상점관리 > MD 관리 > 지사관리";
$P->title = "지사관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();

