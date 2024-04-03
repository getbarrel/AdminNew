<?
include("../class/layout.class");

$db = new Database;
$mdb = new Database;

$Script = "
<style>
.list_icon	{margin:15px 0px 0px 15px;}
.main_list	{display:inline-block;*display:inline;*zoom:1;width:74px;height:90px;text-align:center;}
.main_list ul li {padding-top:3px;}
/*전화걸기*/
.call_box {margin:15px auto;background:url(../v3/images/mobile/phone_bt.png) left top no-repeat;width:301px;height:73px;}
.call_text {font-size:20px;color:#000;font-family:arial;font-weight:600;padding-top:36px;padding-left:74px;}

.demo .colors {
        overflow: hidden;
        margin: 20px 0;
        padding-left: 55px;
      }
      
      .demo .colors li {
        display: block;
        width: 96px;
        height: 96px;
        margin: 10px;
        float: left;
        border: 16px solid rgba(0,0,0,0.2);
        -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
        border-radius: 8px;
      }
      
      .demo .warm {
        display: none;
      }

</style>
<script src='http://razorjack.net/quicksand//scripts/assets/jquery-1.4.1-and-plugins.min.js' type='text/javascript'></script>
<script language='javascript'>
function eventDelete(pg_ix){
	if(confirm('해당 프로모션  정말로 삭제하시겠습니까? 삭제하시면 관련된 모든 이미지가 삭제됩니다.'))
	{
		window.frames['act'].location.href= 'promotion_goods.act.php?act=delete&pg_ix='+pg_ix;//kbk
		//document.getElementById('act').src= 'promotion_goods.act.php?act=delete&pg_ix='+pg_ix;
	}


}
/*
$(function() {
  $('.button').click(function(e) {
	 
    $('.all').quicksand( $('.warm li'), {
      duration: 1000,
      attribute: 'id',
      easing: 'easeInOutQuad'
    });
    e.preventDefault();
  });
});
*/

$(function() {
  $('.button').click(function(e) {
	 
    $('#source').quicksand( $('#source'), {
      duration: 1000,
      attribute: 'id',
      easing: 'easeInOutQuad'
    });
    e.preventDefault();
  });
});

</script>";
if(!$guide_type) $guide_type = "main";

$mstring ="

<form name=poll action='board.manage.act.php'><input type=hidden name=act value=insert>
		<table cellpadding=5 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("모바일 메인관리", "전시관리 > 모바일 메인관리 ")."</td>
		</tr>";
if($admininfo[admin_level] == 9 && $admininfo[mall_type] == "O"){
$mstring .="
		<tr>
			<td align='left' colspan=4 style='padding-bottom:15px;'>
			    <div class='tab'>
					<table class='s_org_tab' style='width:100%' border=0>
					<tr>
						<td class='tab'>";

$mstring .= "<table id='tab_01' ".($guide_type == "main" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?guide_type=main'\">사이트 메인</td>
								<th class='box_03'></th>
							</tr>
							</table>";

$mstring .= "<table id='tab_06' ".($guide_type == "cate_main" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?guide_type=cate_main'\">카테고리메인</td>
								<th class='box_03'></th>
							</tr>
							</table>";
$mstring .= "<table id='tab_05' ".($guide_type == "cate_list" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?guide_type=cate_list'\">카테고리리스트</td>
								<th class='box_03'></th>
							</tr>
							</table>";

$mstring .= "<table id='tab_04' ".($guide_type == "car_main" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?guide_type=car_main'\">자동차메인</td>
								<th class='box_03'></th>
							</tr>
							</table>";
$mstring .= "<table id='tab_02' ".($guide_type == "property_main" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?guide_type=property_main'\">부동산메인</td>
								<th class='box_03'></th>
							</tr>
							</table>";
$mstring .= "<table id='tab_03' ".($guide_type == "travel_main" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?guide_type=travel_main'\">여행메인</td>
								<th class='box_03'></th>
							</tr>
							</table>";

$mstring .= "
						</td>
						<td class='btn' align=right>
							<!--a href='promotion_category.php'><img src='../images/".$admininfo["language"]."/btn_promotion_type.gif' align=absmiddle></a-->
						</td>
					</tr>
					</table>
					</div>
			</td>
		</tr>";
}
$mstring .= "
		<tr>
			<td>
<!--div class='demo'>


<ul class='colors all'>
  <li id='c463033' style='background: #463033'></li>
  <li id='c77343d' style='background: #77343d'></li>
  <li id='ce83f2f' style='background: #e83f2f'></li>
  <li id='cffc223' style='background: #ffc223'></li>
  <li id='cffdb59' style='background: #ffdb59'></li>
  <li id='c788b6f' style='background: #788b6f'></li>
  <li id='c486a5e' style='background: #486a5e'></li>
  <li id='c289539' style='background: #289539'></li>
  <li id='c174876' style='background: #174876'></li>
</ul>

<ul class='colors warm'>
  <li id='c62164e' style='background: #62164e'></li>
  <li id='c86286e' style='background: #86286e'></li>
  <li id='cda79c2' style='background: #da79c2'></li>
  <li id='cf39079' style='background: #f39079'></li>
  <li id='ce83f2f' style='background: #e83f2f'></li>
  <li id='cffc223' style='background: #ffc223'></li>
  <li id='cffdb59' style='background: #ffdb59'></li>
  <li id='cf2c478' style='background: #f2c478'></li>
</ul>
</div-->
<p><a class='button' href='#dummy'>More warmth!</a></p>
			<div id='demo' style='width:340px;'>
				<ul class='list_icon' id='source'>
					<li class='main_list' data-id='id-02'>
						<a href='/shop/category.php'>
							<ul>
								<li>
									<img src='../v3/images/mobile/goodsImg01.gif' title='카테고리'>
								</li>
								<li>
									<strong>카테고리</strong>
								</li>
							</ul>
						</a>
					</li>
					<li class='main_list' data-id='id-01'>
						<ul>
							<li>
								<a href='/shop/search.php?move_type=first'><img src='../v3/images/mobile/goodsImg02.gif' title='상품검색'></a>
							</li>
							<li>
								<a href='/shop/search.php?move_type=first'><strong>상품검색</strong></a>
							</li>
						</ul>
					</li>
					<li class='main_list' data-id='id-03'>
						<a href='/shop/cart.php'>
							<ul>
								<li>
									<img src='../v3/images/mobile/goodsImg03.gif' title='장바구니'>
								</li>
								<li>
									<strong>장바구니</strong>
								</li>
							</ul>
						</a>
					</li>
					<li class='main_list' data-id='id-04'>
						<ul>
							<li>
								<a href='/mypage/wishlist.php'><img src='../v3/images/mobile/goodsImg04.gif' title='위시리스트'></a>
							</li>
							<li>
								<a href='/mypage/wishlist.php'><strong>위시리스트</strong></a>
							</li>
						</ul>
					</li>
					<li class='main_list' data-id='id-05'>
						<a href='/shop/goods_list.php?cid=0&depth=-1&list_type=blog&orderby=p.regdate'>
							<ul>
								<li>
									<img src='../v3/images/mobile/goodsImg05.gif' title='신상품'>
								</li>
								<li>
									<strong>신상품</strong>
								</li>
							</ul>
						</a>
					</li>
					<li class='main_list' data-id='id-06'>
						<a href='/shop/goods_list.php?cid=0&depth=-1&list_type=blog&orderby=p.view_cnt'>
							<ul>
								<li>
									<img src='../v3/images/mobile/goodsImg06.gif' title='베스트상품'>
								</li>
								<li>
									<strong>베스트상품</strong>
								</li>
							</ul>
						</a>
					</li>
					<li class='main_list' data-id='id-07'>
						<ul>
							<li>
								<a href='/community/use_after.php'><img src='../v3/images/mobile/goodsImg13.gif' title='상품후기'></a>
							</li>
							<li>
								<a href='/community/use_after.php'><strong>상품후기</strong></a>
							</li>
						</ul>
					</li>
					<li class='main_list' data-id='id-08'>
						<ul>
							<li>
								<a href='/event/event_list.php'><img src='../v3/images/mobile/goodsImg08.gif' title='기획전'></a>
							</li>
							<li>
								<a href='/event/event_list.php'><strong>기획전</strong></a>
							</li>
						</ul>
					</li>
					<li class='main_list' data-id='id-09'>
						<ul>
							<li>
								<a href='/mypage/profile.php'><img src='../v3/images/mobile/goodsImg09.gif' title='마이페이지'></a>
							</li>
							<li>
								<a href='/mypage/profile.php'><strong>마이페이지</strong></a>
							</li>
						</ul>
					</li>
					<li class='main_list' data-id='id-10'>
						<ul>
							<li>
								<a href='/customer/bbs.php?board=notice'><img src='../v3/images/mobile/goodsImg10.gif' title='고객센터'></a>
							</li>
							<li>
								<a href='/customer/bbs.php?board=notice'><strong>고객센터</strong></a>
							</li>
						</ul>
					</li>
					<li class='main_list' data-id='id-11'>
						<ul>
							<li>
								<a href='/mypage/order_history.php'><img src='../v3/images/mobile/goodsImg11.gif' title='주문/배송'></a>
							</li>
							<li>
								<a href='/mypage/order_history.php'><strong>주문/배송</strong></a>
							</li>
						</ul>
					</li>
					<li class='main_list' data-id='id-12'>
						<ul>
							<li>
								<a href='/customer/bbs.php?board=faq'><img src='../v3/images/mobile/goodsImg12.gif' title='FAQ'></a>
							</li>
							<li>
								<a href='/customer/bbs.php?board=faq'><strong>FAQ</strong></a>
							</li>
						</ul>
					</li>
				</ul>


				<ul id='destination' class='list_icon' style='display:none;'>
					<li class='main_list' data-id='id-02'>
						<a href='/shop/category.php'>
							<ul>
								<li>
									<img src='../v3/images/mobile/goodsImg01.gif' title='카테고리'>
								</li>
								<li>
									<strong>카테고리</strong>
								</li>
							</ul>
						</a>
					</li>
					<li class='main_list' data-id='id-01'>
						<ul>
							<li>
								<a href='/shop/search.php?move_type=first'><img src='../v3/images/mobile/goodsImg02.gif' title='상품검색'></a>
							</li>
							<li>
								<a href='/shop/search.php?move_type=first'><strong>상품검색</strong></a>
							</li>
						</ul>
					</li>
					<li class='main_list' data-id='id-03'>
						<a href='/shop/cart.php'>
							<ul>
								<li>
									<img src='../v3/images/mobile/goodsImg03.gif' title='장바구니'>
								</li>
								<li>
									<strong>장바구니</strong>
								</li>
							</ul>
						</a>
					</li>
					<li class='main_list' data-id='id-04'>
						<ul>
							<li>
								<a href='/mypage/wishlist.php'><img src='../v3/images/mobile/goodsImg04.gif' title='위시리스트'></a>
							</li>
							<li>
								<a href='/mypage/wishlist.php'><strong>위시리스트</strong></a>
							</li>
						</ul>
					</li>
					<li class='main_list' data-id='id-05'>
						<a href='/shop/goods_list.php?cid=0&depth=-1&list_type=blog&orderby=p.regdate'>
							<ul>
								<li>
									<img src='../v3/images/mobile/goodsImg05.gif' title='신상품'>
								</li>
								<li>
									<strong>신상품</strong>
								</li>
							</ul>
						</a>
					</li>
					<li class='main_list' data-id='id-06'>
						<a href='/shop/goods_list.php?cid=0&depth=-1&list_type=blog&orderby=p.view_cnt'>
							<ul>
								<li>
									<img src='../v3/images/mobile/goodsImg06.gif' title='베스트상품'>
								</li>
								<li>
									<strong>베스트상품</strong>
								</li>
							</ul>
						</a>
					</li>
					<li class='main_list' data-id='id-07'>
						<ul>
							<li>
								<a href='/community/use_after.php'><img src='../v3/images/mobile/goodsImg13.gif' title='상품후기'></a>
							</li>
							<li>
								<a href='/community/use_after.php'><strong>상품후기</strong></a>
							</li>
						</ul>
					</li>
					<li class='main_list' data-id='id-08'>
						<ul>
							<li>
								<a href='/event/event_list.php'><img src='../v3/images/mobile/goodsImg08.gif' title='기획전'></a>
							</li>
							<li>
								<a href='/event/event_list.php'><strong>기획전</strong></a>
							</li>
						</ul>
					</li>
					<li class='main_list' data-id='id-09'>
						<ul>
							<li>
								<a href='/mypage/profile.php'><img src='../v3/images/mobile/goodsImg09.gif' title='마이페이지'></a>
							</li>
							<li>
								<a href='/mypage/profile.php'><strong>마이페이지</strong></a>
							</li>
						</ul>
					</li>
					<li class='main_list' data-id='id-10'>
						<ul>
							<li>
								<a href='/customer/bbs.php?board=notice'><img src='../v3/images/mobile/goodsImg10.gif' title='고객센터'></a>
							</li>
							<li>
								<a href='/customer/bbs.php?board=notice'><strong>고객센터</strong></a>
							</li>
						</ul>
					</li>
					<li class='main_list' data-id='id-11'>
						<ul>
							<li>
								<a href='/mypage/order_history.php'><img src='../v3/images/mobile/goodsImg11.gif' title='주문/배송'></a>
							</li>
							<li>
								<a href='/mypage/order_history.php'><strong>주문/배송</strong></a>
							</li>
						</ul>
					</li>
					<li class='main_list' data-id='id-12'>
						<ul>
							<li>
								<a href='/customer/bbs.php?board=faq'><img src='../v3/images/mobile/goodsImg12.gif' title='FAQ'></a>
							</li>
							<li>
								<a href='/customer/bbs.php?board=faq'><strong>FAQ</strong></a>
							</li>
						</ul>
					</li>
				</ul>
				<div class='call_box'>
					<a href='tel:02-6270-3082'><div class='call_text'>02-6270-3082</div></a>
				</div>
				<div style='width:85%;margin:15px 5%;font-weight:bold;font-size:13px;'>
					상담시간은 <span style='text-decoration:underline;color:#f31d1d;'>PM08:00~AM:00</span> 입니다.
					토요일 새벽휴무(일요일 am05 오픈) 입니다.
				</div>
			</div>
				
			<!--img src='../images/promotion_guide/{$guide_type}.gif' usemap='#{$guide_type}'-->
			</td>
		</tr>
		</form>";
$mstring .="</table>";

if($guide_type == "main"){
	$mstring .="
		<map id=\"{$guide_type}\" name=\"{$guide_type}\" >
		  <area shape=\"rect\" coords=\"104,2,220,42\" alt='' title='' href='/admin/display/main_flash_write.php?mf_ix=113&act=update' target='_blank'>
		  <area shape=\"rect\" coords=\"106,84,425,223\" alt='' title='' href='/admin/display/main_flash_write.php?mf_ix=98&act=update'>
		  <area shape=\"rect\" coords=\"105,446,426,577\" alt='' title='' href='/admin/display/main_starshop_write.php?mf_ix=1&act=update'>
		  <area shape=\"rect\" coords=\"430,461,544,570\" alt='' title='' href='/admin/display/banner_write.php?banner_ix=102&SubID=SM22464243Sub'>
		  <area shape=\"rect\" coords=\"103,596,259,632\" alt='' title='' href='/admin/display/main_goods.php'>
		</map>
		";
} else if($guide_type == "property_main"){
	$mstring .="
		<map id=\"{$guide_type}\" name=\"{$guide_type}\" >
		  <area shape='rect' coords='58,90,194,242' alt='' title='' href='/admin/display/banner_write.php?banner_ix=109&SubID=SM22464243Sub'>
		  <area shape='rect' coords='197,89,466,242' alt='' title='' href='/admin/display/banner_write.php?banner_ix=110&SubID=SM22464243Sub'>
		  <area shape='rect' coords='468,90,569,439' alt='' title='' href='/admin/display/banner.php?nset=1&page=2&view=innerview&max=20'>
		  <area shape='rect' coords='59,592,572,839' alt='' title='' href='/admin/display/category_main_goods.php?cmg_ix=1'>
		</map>
		";
} else if($guide_type == "travel_main"){
	$mstring .="
		<map id=\"{$guide_type}\" name=\"{$guide_type}\" >
		  <area shape='rect' coords='55,90,175,244' alt='' title='' href='/admin/display/main_flash_write.php?mf_ix=119&act=update'>
		  <area shape='rect' coords='55,267,175,397' alt='' title='' href='/admin/display/banner_write.php?banner_ix=125&SubID=SM22464243Sub'>
		  <area shape='rect' coords='55,402,574,462' alt='' title='' href='/admin/display/banner_write.php?banner_ix=126&SubID=SM22464243Sub'>
		  <area shape='rect' coords='56,480,177,638' alt='' title='' href='/admin/display/banner_write.php?banner_ix=127&SubID=SM22464243Sub'>
		</map>
		";
} else if($guide_type == "car_main"){
	$mstring .="
		<map id=\"{$guide_type}\" name=\"{$guide_type}\" >
		  <area shape='rect' coords='58,93,437,254' alt='' title='' href='/admin/display/main_flash_write.php?mf_ix=118&act=update' />
		  <area shape='rect' coords='438,94,579,257' alt='' title='' href='/admin/display/banner.php' />
		  <area shape='rect' coords='53,476,147,607' alt='' title='' href='/admin/display/banner_write.php?banner_ix=133&SubID=SM22464243Sub' />
		  <area shape='rect' coords='438,478,578,610' alt='' title='' href='/admin/display/main_flash_write.php?mf_ix=115&act=update' />
		  <area shape='rect' coords='56,631,579,831' alt='' title='' href='/admin/display/main_starshop_write.php?mf_ix=4&act=update' />
		  <area shape='rect' coords='56,856,574,1031' alt='' title='' href='/admin/display/promotion_goods.php?pg_ix=29' />
		</map>
		";
} else if($guide_type == "cate_list"){
	$mstring .="
		<map id=\"{$guide_type}\" name=\"{$guide_type}\" >
		  <area shape='rect' coords='47,229,437,355' alt='' title='' href='/admin/display/category_main.list.php'>
          <area shape='rect' coords='439,227,585,357' alt='' title='' href='/admin/display/main_flash_write.php?mf_ix=111&act=update'>
		</map>

		";
} else if($guide_type == "cate_main"){
	$mstring .="
		<map id=\"{$guide_type}\" name=\"{$guide_type}\" >
		  <area shape='rect' coords='34,124,437,254' alt='' title='' href='/admin/display/category_main.list.php'>
		  <area shape='rect' coords='441,124,589,254' alt='' title='' href='/admin/display/main_flash_write.php?mf_ix=111&act=update'>
		</map>
		";
}
$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >노출하고자 하는 모바일 메뉴를 클릭하면 아이콘이 활성화 되며 자동으로 해당메뉴가 모바일 쇼핑몰에 노출되게 됩니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >각 메뉴의 노출순서를 드래그를 통해서 변경 하실 수 있습니다. </td></tr>
	</table>
	";


$help_text = HelpBox("모바일 메인관리", $help_text);

$Contents = $mstring.$help_text;


$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "프로모션/전시 > 모바일 메인관리";
$P->title = "모바일 메인관리";
$P->TitleBool = true;
$P->strLeftMenu = display_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>
