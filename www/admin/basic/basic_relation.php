<?
include("../class/layout.class");
include_once("../inventory/inventory.lib.php");

$db = new Database;

$Script = "
<script type='text/javascript'>
	function tree_init_complete(){
		alert(1);
	}
	function initTrees() {
		$('#tree').treeview({
			url: 'inventory_category.act.php?act=get_category_infos',
			addClasses: 'drag',
			collapsed: true,
			animated: 'fast',
			control:'#sidetreecontrol',
			persist: 'cookie',
			cookieId: 'inventory_category',
			toggle: function() {

				$('#tree').find('li').each(function() {
					//alert($(this).html());
					//$(this).addClass('drag');
				});
				//alert(1);
				//window.console && console.log('%o was toggled', this);
			}
		});

		$('#tree').bind('contextmenu', function(event) {
			var jquery_obj = $(event.target).parents('li').filter(':first');
				if(confirm('해당분류를 정말로 삭제하시겠습니까?')){
					if ($(event.target).is('li') || $(event.target).parents('li').length) {
						//alert(jquery_obj.attr('id'));
						$.ajax({
							type: 'GET',
							data: {'act': 'delete', 'cid':jquery_obj.attr('id')},
							url: './inventory_category.act.php',
							dataType: 'json',
							async: false,
							beforeSend: function(){
									//alert(1);
							},
							success: function(data){
								//alert(data.result);
								if(data.result){
									$('#tree').treeview({
										remove: $(event.target).parents('li').filter(':first')
									});
								}else{
									alert(data.result_message);
								}
							}
						});

						return false;
					}
				}

				return false;
			});

			return true;
	}



	function addSubCategory(parent_id){
			var branches = $(\"<li ><span class='folder'>&nbsp;<input type='text' class=textbox style='width:80px;' id='\"+parent_id+\"' name='cname' onfocusout='AddCategory($(this))' onkeydown ='var e = window.event; if (e.keyCode == 13) {AddCategory($(this))}'></span></li>\").appendTo('#'+parent_id);
			$('#'+parent_id).treeview({
				add: branches
			});

	}

	function AddCategory(jquery_obj){
		//alert(jquery_obj.attr('id'));

		if(jquery_obj.val() != ''){
			//alert(jquery_obj.attr('id')+':::'+jquery_obj.val());
			$.ajax({
				type: 'GET',
				data: {'act': 'add', 'parent_cid':jquery_obj.attr('id'),'cname':jquery_obj.val()},
				url: './inventory_category.act.php',
				dataType: 'json',
				async: false,
				beforeSend: function(){
						//alert(1);
				},
				success: function(data){
					//alert(data.cname);
					//alert(jquery_obj.parent().parent().parent().html());
					var html_str = \"<!--li id=\"+data.cid+\"--><span class='folder'>&nbsp;\"+data.cname+\" &nbsp;&nbsp;&nbsp;<a href=\\\"javascript:addSubCategory('\"+data.cid+\"');\\\">+</a></span><!--/li-->\";
					jquery_obj.parent().parent().attr('id',data.cid);
					jquery_obj.parent().parent().html(html_str);
				}
			});
		}

	}

	function EditCategory(jquery_obj){
		var cname = jquery_obj.html();
		//var cid = jquery_obj.html();
		var edit_obj = jquery_obj.parent().parent();
		//alert(edit_obj.html());
		var html_str = \"<input type='text' class=textbox style='width:80px;' id='\"+edit_obj.attr('id')+\"' value='\"+cname+\"' name='cname' onfocusout='UpdateCategory($(this))' onkeydown ='var e = window.event; if (e.keyCode == 13) {UpdateCategory($(this))}'>\";
		jquery_obj.parent().html(html_str);
	}

	function UpdateCategory(jquery_obj){
		//alert(jquery_obj.parent().html());
		//alert(jquery_obj.attr('id'));

		if(jquery_obj.val() != ''){
			$.ajax({
				type: 'GET',
				data: {'act': 'update', 'cid':jquery_obj.attr('id'),'cname':jquery_obj.val()},
				url: './inventory_category.act.php',
				dataType: 'json',
				async: false,
				beforeSend: function(){
						//alert(1);
				},
				success: function(data){
					//alert(data.cname);
					var html_str = \"&nbsp;<a href='#' ondblclick='EditCategory($(this))'>\"+data.cname+\"</a> &nbsp;&nbsp;&nbsp;<a href=\\\"javascript:addSubCategory('\"+data.cid+\"');\\\">+</a>\";
					jquery_obj.parent().html(html_str);
				}
			});
		}

	}

	function check(){

		$('#tree li').each(function() {
			//alert($(this).html());
			//$(this).addClass('drag');
		});
		alert($('#tree').html());
	}
	$(document).ready(function(){
		//var result = initTrees();
		//alert(1);
		//alert($('#tree').html());
		//$('#tree li').addClass('drag');

		$('#tree li').each(function() {
			//alert($(this).html());
			//$(this).addClass('drag');
		});
		counter = 0;
		var tree = $('ul#tree');
		//axis: 'y',
		$('li', tree.get(0)).draggable({
			revert: true,
			helper: 'clone',
			containment: 'frame',

			ghosting : true,
			//When first dragged
			stop: function (ev, ui) {
				var pos = $(ui.helper).offset();
				objName = '#clonediv' + counter
				$(objName).css({
					'left': pos.left,
					'top': pos.top
				});
				$(objName).removeClass('drag');
				//When an existiung object is dragged
				$(objName).draggable({
					containment: 'parent',
					stop: function (ev, ui) {
						var pos = $(ui.helper).offset();
						console.log($(this).attr('id'));
						console.log(pos.left)
						console.log(pos.top)
					}
				});
			}
		});

		$('#tree li').find('span').each(function() {

			$(this).droppable(
				{
					accept			: function(d) {
							return true;
					},
					hoverclass		: 'dropOver',
					activeclass		: 'fakeClass',
					tollerance		: 'pointer',

					onhover			: function(dragged)
					{
						alert(1);
						if (!this.expanded) {
							subbranches = $('ul', this.parentNode);
							if (subbranches.size() > 0) {
								subbranch = subbranches.eq(0);
								this.expanded = true;
								if (subbranch.css('display') == 'none') {
									var targetBranch = subbranch.get(0);
									this.expanderTime = window.setTimeout(
										function()
										{
											$(targetBranch).show();
											$('img.expandImage', targetBranch.parentNode).eq(0).attr('src', 'images/bullet_toggle_minus.png');
											$.recallDroppables();
										},
										500
									);
								}
							}
						}
					},
					onout			: function()
					{
						alert(2);
						if (this.expanderTime){
							window.clearTimeout(this.expanderTime);
							this.expanded = false;
						}
					},
					drop			: function(event, dropped)
					{
						//alert(dropped.html());
						if(this.parentNode == dropped)
							return;
						if (this.expanderTime){
							window.clearTimeout(this.expanderTime);
							this.expanded = false;
						}
						subbranch = $('ul', this.parentNode);
						if (subbranch.size() == 0) {
							$(this).after('<ul></ul>');
							subbranch = $('ul', this.parentNode);
						}
						oldParent = dropped.parentNode;
						subbranch.eq(0).append(dropped);
						oldBranches = $('li', oldParent);
						if (oldBranches.size() == 0) {
							//$('img.expandImage', oldParent.parentNode).src('images/spacer.gif');
							$(oldParent).remove();
						}
						expander = $('img.expandImage', this.parentNode);
						//if (expander.get(0).src.indexOf('spacer') > -1)
						//	expander.get(0).src = 'images/bullet_toggle_minus.png';
					}
				}
			);
		});
		/*
		$('ul#tree li').each(function(){
			//alert($(this).html());
			//$(this).draggable();

		});
		*/
	});
/*
	$(function() {
			$('#flags li').hover(function() {
				$(this).siblings().stop(true).animate({opacity:'0.5'},1000);
			}, function() {
				$(this).siblings().stop(true).animate({opacity:'1'},1000);
			});
		});
*/
	</script>
";

$Contents = "<table cellpadding=0 cellspacing=0 border=0 width='100%'>

		<tr>
		    <td align='left' colspan=3> ".GetTitleNavigation("상품분류설정", "상품관리 > 상품분류설정")."</td>
		</tr>
		<tr>
			<td valign=top width=236>
			<div id=TREE_BAR >
				<table cellpadding=0 cellspacing=0 border=0 bgcolor=#e2e2e2 width=100% style='border:3px solid #d8d8d8'>
				<tr>
					<td style='padding:10px 10px 10px 10px;' valign=middle nowrap>

					<!--img src='../image/t.gif' onclick='order_up(document.category_order)' style='cursor:hand' alt='분류 위로 이동' align=absmiddle>
					<img src='../image/b.gif' onclick='order_down(document.category_order)' style='cursor:hand' alt='분류 아래로 이동' align=absmiddle-->
					재고상품 분류관리 <a href=\"javascript:addSubCategory('tree')\">+</a>
					</td>
					<td width=190 valign=middle>
					<span class=small><!--분류선택후 이동버튼 클릭--> <!--".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."--> </span>
					</td>
				</tr>
				<tr>
					<td colspan=2 width=200 height=400 valign=top style='overflow:auto;padding:0 10px 10px 10px;background-color:#ffffff;'>
					<div style=\"width:200px;height:418px;padding:5px;margin:1px;background-color:#ffffff\" >
					<ul id='tree' class='filetree treeview-famfamfam' onselect=\"return false;\"></ul>
					</div>
					</td>
				</tr>
				<tr>
					<td colspan=2 align=center style='padding:10px 0px;'>
						<div id='sidetreecontrol'> <a href='?#'>모두닫기</a> | <a href='?#'>모두 펼치기</a> | <a href='#'>전환하기</a></div>
					</td>
				</tr>
				</table>
			</div>
			</td >
			<td width='*' style='padding:5px;'>
				<!--a onclick=\"check();\">확인</a-->
				<!--div id='aaa' style='border:1px solid gray;padding:10px;'>asdf</div>
				<ul id='flags'>
					<li id='German'>German</li>
					<li id='Italian'>Italian</li>
					<li id='Spanish'>Spanish</li>
					<li id='French'>French</li>
				</ul-->
			</td>
			<td valign=top width='100%' align='left' style='line-height:150%;'>
				<b>- 사용방법 정리</b><-- 아래 내용을 잘 정리해서 도움말 박스에 넣어서 처리 필요<br>
				1. 플러스 버튼을 누르면 (+) 하부카테고리 추가 할수 있도록 아래 입력창이 노출<br>
				2. 분류 이름 더블 클릭시 input box 로 변경되서 분류이름을 변경할수 도록 변화됨 <br>
				3. 이름 변경후 엔터 또는 입력창에 포커스가 들어갔다가 나오면 자동으로 카테고리 명이 업데이트됨<br>
				4. 삭제하고자 하는 카테고리에서 마우스 오른쪽 버튼을 누르면 카테고리가 삭제됨<br>
				5. 순서 변경은 아직 구현되지 않았음
			</td>
		</tr>
		</table>
<style>

span.dropOver
{
	background-color: #00c;
	color: #fff;
	height: 16px;
	line-height: 16px;
	padding-left: 6px;
}
</style>
";


$Script = "<link rel='stylesheet' href='../js/jquery.treeview/jquery.treeview.css' />
<link rel='stylesheet' href='../js/jquery.treeview/red-treeview.css' />
<link rel='stylesheet' href='../js/jquery.treeview/screen.css' />
<script src='../js/jquery.treeview/jquery.treeview.js' type='text/javascript'></script>
<script src='../js/jquery.treeview/jquery.treeview.edit.js' type='text/javascript'></script>
<script src='../js/jquery.treeview/jquery.treeview.async.js' type='text/javascript'></script>
\n$Script";


if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->OnloadFunction = "initTrees();";
	$P->strLeftMenu = product_menu();
	$P->strContents = $Contents;
	$P->Navigation = "재고관리 > 재고상품 분류관리";
	$P->NaviTitle = "재고상품 분류관리";
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->OnloadFunction = "initTrees();";
	$P->strLeftMenu = inventory_menu();
	$P->Navigation = "재고관리 > 재고상품 분류관리";
	$P->title = "재고상품 분류관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

/*

CREATE TABLE IF NOT EXISTS `inventory_category_info` (
  `cid` varchar(15) NOT NULL COMMENT '분류 코드',
  `depth` smallint(1) unsigned DEFAULT NULL COMMENT '분류 깊이',
  `vlevel1` int(3) DEFAULT NULL COMMENT '깊이0 정렬값',
  `vlevel2` int(3) DEFAULT NULL COMMENT '깊이1 정렬값',
  `vlevel3` int(3) DEFAULT NULL COMMENT '깊이2 정렬값',
  `vlevel4` int(3) DEFAULT NULL COMMENT '깊이3 정렬값',
  `vlevel5` int(3) DEFAULT NULL COMMENT '깊이4 정렬값',
  `cname` varchar(40) DEFAULT NULL COMMENT '분류명',
  `category_use` char(1) DEFAULT '1' COMMENT '분류 사용 여부',
  `regdate` date DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`cid`),
  KEY `IDX_MCI_DEPTH` (`depth`),
  KEY `category_use` (`category_use`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='재고관리 분류정보';



CREATE TABLE IF NOT EXISTS `inventory_goods` (
  `gid` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT '재고상품키값',
  `gname` varchar(255) NOT NULL COMMENT '재고상품명',
  `gcode` varchar(50) DEFAULT NULL COMMENT '재고상품 오프라인코드',
  `cid` varchar(??) DEFAULT NULL COMMENT '카테고리',
  `goods_div` varchar(50) DEFAULT NULL COMMENT '분류',
  `model` varchar(100) DEFAULT NULL COMMENT '모델명',
  `orgin` varchar(100) DEFAULT '' COMMENT '원산지',
  `maker` varchar(20) DEFAULT NULL COMMENT '제조사명',
  `ci_ix` int(6) unsigned DEFAULT NULL COMMENT '입고처',
  `pi_ix` int(6) unsigned DEFAULT NULL COMMENT '입고창고',
  `bs_goods_url` mediumtext COMMENT '상품구매url',
  `search_keyword` varchar(255) DEFAULT NULL COMMENT '검색키워드',
  `etc` varchar(255) DEFAULT NULL COMMENT '기타',
  `bimg` varchar(255) DEFAULT NULL COMMENT '이미지',
  `mimg` varchar(255) DEFAULT NULL COMMENT '이미지',
  `msimg` varchar(255) DEFAULT NULL COMMENT '이미지',
  `simg` varchar(255) DEFAULT NULL COMMENT '이미지',
  `cimg` varchar(255) DEFAULT NULL COMMENT '이미지',
  `editdate` datetime DEFAULT NULL COMMENT '수정일',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`ipid`),
  KEY `IDX_IP_CID` (`cid`),
  KEY `IDX_IP_PCODE` (`pcode`),
  KEY `IDX_IP_CI_IX` (`ci_ix`),
  KEY `IDX_IP_PI_IX` (`pi_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='재고상품정보' AUTO_INCREMENT=1;

//  `admin_memo` varchar(255) DEFAULT NULL COMMENT '관리자메모',

1안!---------------------------------------------------------------------------------------------------
option_detail이 필요??? 옵션 구분값이 필요 없어져서...

CREATE TABLE IF NOT EXISTS `inventory_goods_items` (
  `gi_ix` int(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `gid` int(10) unsigned zerofill NOT NULL COMMENT '재고상품아이디',
  `item_name` varchar(100) NOT NULL COMMENT '단품명',
  `unit` varchar(100) DEFAULT NULL COMMENT '단위',
  `standard` varchar(100) DEFAULT NULL COMMENT '규격',
  `input_price` int(10) unsigned DEFAULT NULL COMMENT '입고단가',
  `output_price` int(10) unsigned DEFAULT NULL COMMENT '출고단가',

  `option_sell_ing_cnt` int(6) DEFAULT NULL COMMENT '옵션 판매진행중 재고',
  `option_stock` int(4) DEFAULT '0' COMMENT '옵션별재고',
  `option_safestock` int(4) DEFAULT '0' COMMENT '안전재고',

  `set_yn` enum('N','Y') DEFAULT 'N' COMMENT '세트상품여부',
  `item_code` varchar(50) DEFAULT '' COMMENT '옵션오프라인관리코드',
  `insert_yn` enum('Y','N') DEFAULT 'Y' COMMENT '수정시구분값',
  `editdate` datetime DEFAULT NULL COMMENT '수정일자',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`gi_ix`),
  KEY `gid` (`gid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='재고상품품목정보' AUTO_INCREMENT=1 ;

2안!------------------------------------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `inventory_product_options` (
  `opn_ix` int(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `pid` int(10) unsigned zerofill NOT NULL COMMENT '재고상품아이디',

  `option_name` varchar(100) NOT NULL COMMENT '단품명',

  `option_use` char(1) NOT NULL DEFAULT '1' COMMENT '옵션사용여부',
  `insert_yn` enum('Y','N') DEFAULT 'Y' COMMENT '수정시구분값',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`opn_ix`),
  KEY `pid` (`pid`,`option_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='재고상품옵션정보' AUTO_INCREMENT=29594 ;

CREATE TABLE IF NOT EXISTS ``inventory_product_options_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `pid` int(10) unsigned zerofill DEFAULT NULL COMMENT '재고상품아이디',
  `opn_ix` int(6) DEFAULT NULL COMMENT '재고옵션인덱스값',
  `option_div` varchar(255) DEFAULT NULL COMMENT '옵션구분',

  `unit` varchar(100) DEFAULT NULL COMMENT '단위',
  `standard` varchar(100) DEFAULT NULL COMMENT '규격',
  `input_price` int(10) unsigned DEFAULT NULL COMMENT '입고단가',
  `output_price` int(10) unsigned DEFAULT NULL COMMENT '출고단가',
  `set_yn` enum('N','Y') DEFAULT 'N' COMMENT '세트상품여부',                                      <-???????????????????????

  `option_code` varchar(50) DEFAULT '' COMMENT '옵션오프라인관리코드',
  `option_sell_ing_cnt` int(6) DEFAULT NULL COMMENT '옵션 판매진행중 재고',
  `option_stock` int(4) DEFAULT '0' COMMENT '옵션별재고',
  `option_safestock` int(4) DEFAULT '0' COMMENT '안전재고',
  `option_etc1` varchar(100) DEFAULT '' COMMENT '옵션상세 기타필드',
  `insert_yn` enum('Y','N') DEFAULT 'Y' COMMENT '수정시구분값',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `pid_2` (`pid`,`option_div`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='상품 옵션상세정보' AUTO_INCREMENT=128528 ;

*/


?>