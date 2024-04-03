<?
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");
include_once $_SERVER["DOCUMENT_ROOT"]."/class/Snoopy.class.php";
include_once("buyingService.lib.php");

define_syslog_variables();
openlog("phplog", LOG_PID , LOG_LOCAL1);
/**
 * json_decode로 생성한 객체를 배열로 변환
 *
 * @param obj
 * @return array
 */
if (!function_exists('objectToArray')) {
	function objectToArray($d) {
		if (is_object($d)) {
			// Gets the properties of the given object
			// with get_object_vars function
			$d = get_object_vars($d);
		}

		if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return array_map(__FUNCTION__, $d);
		}
		else {
			// Return array
			return $d;
		}
	}
}

//print_r($_GET);
//exit;
//session_start();
//echo "time : ".time();
if($admininfo[company_id] == ""){
	echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert(language_data['common']['C'][language]);location.href='/admin/admin.php'</script>";
	exit;
}

	if($bs_site == "izabel"){
		$search_list_regxp = "|href=[\",'](.*)[\",']>|U";
		$search_detail_regxp = "|<a.*href=(.*)[>].*>|U";
		$GoodsListPageName = "shopbrand.html";
		$GoodsDetailPageName = "shopdetail.html";
		$Product_ID_Name = "branduid";
		$PageParamName = "page";
		$bs_site_domain = "http://www.izabel.co.kr";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "curl";
		$currency_type = "KRW";
	}else if($bs_site == "saksfifthavenue"){
		$search_list_regxp = "|<li class=\"page-.*\"><a href='(.*)'>|U";
		$search_detail_regxp = "|<a.*href=[\",'](.*)[\",'].*>|U";
        $search_list_max_regxp = "|\"mainBoldBlackText totalNumberOfPages\">(.*)</span>|U";
		$GoodsListPageName = "shop/_/";
		$GoodsDetailPageName = "ProductDetail.jsp";
		$Product_ID_Name = "PRODUCT<>prd_id";
		$PageParamName = "Nao";
		$bs_site_domain = "http://www.saksfifthavenue.com";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "snoopy";
		$currency_type = "USD";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else if($bs_site == "bloomingdales"){
		$search_list_regxp = "|href=[\",'](.*)[\",']>|U";
		$search_detail_regxp = "|<a.*href=[\",'](.*)[\",'].*>|U";
		$GoodsListPageName = "catalog/index.ognc";
		$GoodsDetailPageName = "product/index.ognc";
		$Product_ID_Name = "ID";
		$search_list_max_regxp = "|Page[\s](.*)[\s]of[\s](.*)&nbsp;|U";
		$PageParamName = "CURRENT_PAGE";
		$bs_site_domain = "http://www1.bloomingdales.com";
		$page_size = 1;
		$start_page_num = 0;
		$scrapping_type = "snoopy";
		$currency_type = "USD";
	}else if($bs_site == "macys"){
		$search_list_regxp = "|href=[\",'](.*)[\",']>|U";
		$search_detail_regxp = "|<a.*href=[\",'](.*)[\",'].*>|U";
		$search_list_max_regxp = "|Page[\s](.*)[\s]of[\s](.*)&nbsp;|U";
		$GoodsListPageName = "catalog/index.ognc";
		$GoodsDetailPageName = "product/index.ognc";
		$Product_ID_Name = "ID";
		$PageParamName = "CURRENT_PAGE";
		$bs_site_domain = "http://www1.macys.com";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "snoopy";
		$currency_type = "USD";
	}else if($bs_site == "barneys"){
		$search_list_regxp = "|href=[\",'](.*)[\",']>|U";
		$search_detail_regxp = "|<a.*href=[\",'](.*)[\",'].*>|U";
		$GoodsListPageName = "default,sc.html";
		$GoodsDetailPageName = "default,pd.html";
		$Product_ID_Name = "ID";
		$PageParamName = "start";
		$bs_site_domain = "http://www.barneys.com";
		$page_size = 20;
		$start_page_num = 0;
		$scrapping_type = "curl";
		$currency_type = "USD";
	}else if($bs_site == "nordstrom"){
		$search_list_regxp = "|<li class=\"selected first\">(.*)</li>|U";
		$search_detail_regxp = "|<a href=\"(.*)\" class=\"title\">|U";
        $search_list_max_regxp = "|<a class=\"standard\" href=\".*\">(.*)</a>|U";
		$GoodsListPageName = "/c/";
		$GoodsDetailPageName = "/s/";
		$Product_ID_Name = "ID";
		$PageParamName = "page";
		$bs_site_domain = "http://shop.nordstrom.com";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "snoopy";
		$currency_type = "USD";
	}else if($bs_site == "gymboree"){
		$search_list_regxp = "|href=[\",'](.*)[\",']>|U";
		$search_detail_regxp = "|<a.*href=[\",'](.*)[\",'].*>|U";
		$search_list_max_regxp = "|<span class=\"page_selected\">(.*)</span>|U";
		$GoodsListPageName = "dept_category.jsp";
		$GoodsDetailPageName = "dept_item.jsp";
		$Product_ID_Name = "PRODUCT<>prd_id";
		$PageParamName = "pageClicked";
		$bs_site_domain = "http://www.gymboree.com";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "snoopy";
		$currency_type = "USD";
		$category_navi_regxp = "|<div.*class=\"breadcrumbs\">(.*)</div>|U";

	}else if($bs_site == "ralphlauren"){
		$search_list_regxp = "|href=[\",'](.*)[\",'].*>|U";
		$search_detail_regxp = "|<a.*href=[\",'](.*)[\",'].*>|U";
		$search_list_max_regxp = "|&nbsp;<b>(.*)</b>&nbsp;|U";
		$GoodsListPageName = "family/index.jsp";
		$GoodsDetailPageName = "product/index.jsp";
		$Product_ID_Name = "productId";
		$PageParamName = "pg";
		$bs_site_domain = "http://www.ralphlauren.com";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "curl";
		$currency_type = "USD";
		$category_navi_regxp = "|<title>(.*)</title>|U";

	}else if($bs_site == "onestepahead"){
		$search_list_regxp = "|href=[\",'](.*)[\",'].*>|U";
		$search_detail_regxp = "|<a.*href=[\",'](.*)[\",'].*>|U";
		$search_list_max_regxp = "|<li id=\"numberActive\">(.*)</li>|U";
		$GoodsListPageName = "catalog/thumbnail.jsp";
		$GoodsDetailPageName = "catalog/product.jsp";
		$Product_ID_Name = "productId";
		$PageParamName = "No";
		$bs_site_domain = "http://www.onestepahead.com";
		$page_size = 16;
		$start_page_num = 1;
		$scrapping_type = "snoopy";
		$currency_type = "USD";
	}else if($bs_site == "csnstores"){
		$search_list_regxp = "|href=[\",'](.*)[\",'].*>|U";
		$search_detail_regxp = "|<a.*class=\"prodnamelink_s[\s]secondarytext\".*href=[\",'](.*)[\",'].*>|U";
		//<a id="SKK1287_url" class="prodnamelink_s secondarytext" title="Stokke Sleepi Crib with Mattress" href="http://www.csnstores.com/Stokke-10430X-SKK1287.html">
		$search_list_max_regxp = "|<li id=\"numberActive\">(.*)</li>|U";
		$GoodsListPageName = "asp/superbrowse.asp";
		$GoodsDetailPageName = ".html";
		$Product_ID_Name = "productId";
		$PageParamName = "curpage";
		$bs_site_domain = "http://www.csnstores.com";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "curl";
		$currency_type = "USD";
	}else if($bs_site == "gap" || $bs_site == "oldnavy" || $bs_site == "piperlime" || $bs_site == "bananarepublic.gap.co.jp"){
		$search_list_regxp = "|href=[\",']?(.*)[\",']>|U";
		$search_detail_regxp = "|<a.*href=[\",'](.*)[\",'].*class=\"productItemName\">|U";
		$search_list_max_regxp = "|<span class=\"page_selected\">(.*)</span>|U";
		$GoodsListPageName = "pageID";
		$GoodsDetailPageName = "/browse/product.do";
		$Product_ID_Name = "scid";
		$PageParamName = "pageID";
		if($bs_site == "gap"){
			$bs_site_domain = "http://www.gap.com";
		}elseif($bs_site == "oldnavy"){
			$bs_site_domain = "http://oldnavy.gap.com";
		}elseif($bs_site == "piperlime"){
			$bs_site_domain = "http://piperlime.gap.com";
		}elseif($bs_site == "bananarepublic.gap.co.jp"){
			$bs_site_domain = "http://bananarepublic.gap.co.jp/";
		}

		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "curl";
		$currency_type = "USD";
		//$category_navi_regxp = "|<a.*class=\"subCategory\">(.*)</a>|U";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else if($bs_site == "jcrew"){
		$search_list_regxp = "|<li class=\"pageLabel\">(.*)</li>|U";
		$search_detail_regxp = "|<a.*href=[\",'](.*)[\",'].*border=\"0\">|U";
		$search_list_max_regxp = "|<span class=\"page_selected\">(.*)</span>|U";
		$GoodsListPageName = "PRDOVR~";
		$GoodsDetailPageName = "PRDOVR~";
		$Product_ID_Name = "pid";
		$PageParamName = "iNextCategory";
		$bs_site_domain = "http://www.jcrew.com";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "curl";
		$currency_type = "USD";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else if($bs_site == "abercrombie"){
		$search_list_regxp = "|<a.*href=[\",'](.*)[\",'].*>|U";
		$search_detail_regxp = "|<div class=\"image-wrap\">.*\n.*\n.*<a href=[\",'](.*)[\",']>|U";
		$search_list_max_regxp = "|&nbsp;<b>(.*)</b>&nbsp;|U";
		$GoodsListPageName = "CategoryDisplay";
		$GoodsDetailPageName = "ProductDisplay";
		$Product_ID_Name = "productId";
		$PageParamName = "pg";
		$bs_site_domain = "http://www.abercrombie.com/";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "curl";
	}else if($bs_site == "bodenusa"){
		$search_list_regxp = "|<li class=\"pageLabel\">(.*)</li>|U";
		$search_detail_regxp = "|<a class=\"quickview-link\" title=\"Quick view\" href=\"(.*)\">|U";
		$search_list_max_regxp = "|<span class=\"page_selected\">(.*)</span>|U";
		$GoodsListPageName = ".html";
		$GoodsDetailPageName = ".html";
		$Product_ID_Name = "pid";
		$PageParamName = "pageNo";
		$bs_site_domain = "http://www.bodenusa.com";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "snoopy";
		$currency_type = "USD";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else if($bs_site == "michaelkors"){
		$search_list_regxp = "|<a href=\"(.*)\" class=\"pagelink\">|U";
		$search_detail_regxp = "|<a href=\"(.*)\" class=\"prodImgLink\">|U";
		$search_list_max_regxp = "|<span class=\"page_selected\">(.*)</span>|U";
		$GoodsListPageName = ".jhtml";
		$GoodsDetailPageName = "prod.jhtml";
		$Product_ID_Name = "itemId";
		$PageParamName = "page";
		$bs_site_domain = "http://www.michaelkors.com";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "curl";
		$currency_type = "USD";
		$category_navi_regxp = "|<title>(.*)</title>|U";
		//****************홍진영 start******************
    }else if($bs_site == "janieandjack"){
		$search_list_regxp = "|<a href=\"(.*)\">View All</a>|U";
		$search_detail_regxp = "|<td class=\"bOne\"><a href=\"(.*)\">|U";
		$search_list_max_regxp = "|<td class=\"bOne\" align=\"right\">Page(.*)</td>|U"; //???
		$GoodsListPageName = "dept_category.jsp";
		$GoodsDetailPageName = "dept_item.jsp";
		$Product_ID_Name = "prd_id";
		$PageParamName = "pageClicked";
		$bs_site_domain = "http://www.janieandjack.com";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "snoopy";
		$currency_type = "USD";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else if($bs_site == "hannaandersson"){ // search_list 없음 상품 등록이 안됨
		$search_list_regxp = "|<li class=\"pageLabel\">(.*)</li>|U"; //아무거나 지정
		$search_detail_regxp = "|<div class=\"thumbName\"><a href=\"(.*)&simg.*\".*</div>|U";
		$GoodsListPageName = "category.aspx";
		$GoodsDetailPageName = "style.asp";
		$Product_ID_Name = "product_id";
		$PageParamName = "page_id";
		$bs_site_domain = "http://www.hannaandersson.com";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "snoopy";
		$currency_type = "USD";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else if($bs_site == "ae"){  // search_list 없음 등록 안됨
		$search_list_regxp = "|hr123ef=[\",'](.*)[\",']>|U"; // 아무거나 지정
		$search_detail_regxp = "|<div class=\"sProd\"><a href=\"(.*)\">|U";
		$GoodsListPageName = "category.jsp";
		$GoodsDetailPageName = "product.jsp";
		$Product_ID_Name = "productId";
		$PageParamName = "cad";
		$bs_site_domain = "http://www.ae.com";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "curl";
		$currency_type = "USD";
		$category_navi_regxp = "|<title>(.*)</title>|U";
    }else if($bs_site == "77kids"){
		$search_list_regxp = "|hr123ef=[\",'](.*)[\",']>|U"; // 아무거나 지정
		$search_detail_regxp = "|<a class=\"cat-list-thumb\" href=\"(.*)\"|U";
		$GoodsListPageName = "category";
		$GoodsDetailPageName = "detail";
		$Product_ID_Name = "productId";
		$PageParamName = "cad";
		$bs_site_domain = "http://www.77kids.com";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "curl";
		$currency_type = "USD";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else if($bs_site == "coach"){
		$search_list_regxp = "|hr123ef=[\",'](.*)[\",']>|U"; // 아무거나 지정
		$search_detail_regxp = "|productJSONObjects\[\'(.*)\'\]|U";
		$GoodsListPageName = ".com";
		$GoodsDetailPageName = ".com";
		$Product_ID_Name = "productJSONObjects";
		$PageParamName = "cad";// 아무거나 지정해도 됨 필요 없음
		$bs_site_domain = "http://www.coach.com";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "snoopy";
		$currency_type = "USD";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else if($bs_site == "hollisterco"){
		$search_list_regxp = "|<a.*href=[\",'](.*)[\",'].*>|U";
		$search_detail_regxp = "|<div class=\"image-wrap\">.*\n.*\n.*<a href=[\",'](.*)[\",']>|U";
		$search_list_max_regxp = "|&nbsp;<b>(.*)</b>&nbsp;|U";
		$GoodsListPageName = "CategoryDisplay";
		$GoodsDetailPageName = "ProductDisplay";
		$Product_ID_Name = "productId";
		$PageParamName = "pg";
		$bs_site_domain = "http://www.hollisterco.com/";
		$page_size = 1;
		$start_page_num = 1;
		$currency_type = "USD";
		$scrapping_type = "curl";
	}else if($bs_site == "luxgirl"){ // makeshop
		$search_list_regxp = "|href=\"(.*)\"|U";
		$search_detail_regxp = "|<a href=\"(.*)\"><img class=\"MS_prod_img_s\" src=\".*\">|U";
		$search_list_max_regxp = "|<div class=\"pagerw\">(.*)<!-- pagerw -->|U";
		$GoodsListPageName = "shopbrand.html";
		$GoodsDetailPageName = "shopdetail.html";
		$Product_ID_Name = "branduid";
		$PageParamName = "page";
		$bs_site_domain = "http://www.luxgirl.com/";
		$page_size = 1;
		$start_page_num = 1;
		$currency_type = "KRW";
		$scrapping_type = "curl";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else if($$bs_site == "iwatoo" || $bs_site == "gumzzi" ){
		$search_list_regxp = "|<span id='mk_pager'><a href='(.*)'><font class='brandpage'>|U";
		$search_detail_regxp = "|<td align=center class=\"Brand_prodtHeight\">.*\n.*<a href=\"(.*)\"|U";
		$search_list_max_regxp = "|<font class='brandpage'>\[(.*)\]</font>|U";
		$GoodsListPageName = "shopbrand.html";
		$GoodsDetailPageName = "shopdetail.html";
		$Product_ID_Name = "branduid";
		$PageParamName = "page";
		if($bs_site == "iwatoo"){
			$bs_site_domain = "http://www.iwatoo.com/";
		}elseif($bs_site == "gumzzi"){
			$bs_site_domain = "http://gumzzi.co.kr/";
		}
		$page_size = 1;
		$start_page_num = 1;
		$currency_type = "KRW";
		$scrapping_type = "curl";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else if($bs_site == "aganet"){
		$search_list_regxp = "|javascript:goPage\((.*)\)|U";
		$search_detail_regxp = "|onclick=\"goGoodDetail\((.*)\);\"|U";
		$search_list_max_regxp = "|javascript:goPage\((.*)\).*alt=\"마지막으로\"|U";
		$GoodsListPageName = "goods_list.do";
		$GoodsDetailPageName = "category_detail_view.do";
		$Product_ID_Name = "WG_GB_CODE";
		$PageParamName = "page";
		$bs_site_domain = "http://www.aganet.co.kr/";
		$page_size = 1;
		$start_page_num = 1;
		$currency_type = "KRW";
		$scrapping_type = "snoopy";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else if($bs_site == "lioele"){
		$search_list_regxp = "|javascript:GoPage\((.*)\)|U";
		$search_detail_regxp = "|javascript:view_goodsinfo\((.*)\);|U";
		$search_list_max_regxp = "|<strong>총 (.*) 페이지 </strong>/페이지바로이동|U";
		$GoodsListPageName = "lio_submain";
		$GoodsDetailPageName = "lio_shopview.php";
		$Product_ID_Name = "gnum";
		$PageParamName = "page";
		$bs_site_domain = "http://lioele.com/";
		$page_size = 1;
		$start_page_num = 1;
		$currency_type = "KRW";
		$scrapping_type = "snoopy";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else if($bs_site == "babynsave"){
		$search_list_regxp = "|sort=featured&page=(.*)\"|U";
		$search_detail_regxp = "|<ul class=\"(.*) \">|U";
		$search_list_max_regxp = "|sort=featured&page=(.*)\"|U";
		$GoodsListPageName = "lio_submain";
		$GoodsDetailPageName = "babynsave";
		$Product_ID_Name = "gnum";
		$PageParamName = "page";
		$bs_site_domain = "http://www.babynsave.com/";
		$page_size = 1;
		$start_page_num = 1;
		$currency_type = "USD";
		$scrapping_type = "snoopy";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else if($bs_site == "wgcshop"){
		$search_list_regxp = "|javascript: document.getElementById\('page_no'\).value='(.*)';|U";
		$search_detail_regxp = "|<a href='(.*)'>|U";
		$search_list_max_regxp = "|Items Found in (.*) Pages|U";
		$GoodsListPageName = "content_page";
		$GoodsDetailPageName = "product_detail1";
		$Product_ID_Name = "item";
		$PageParamName = "page_no";
		$bs_site_domain = "http://www.wgcshop.com/";
		$page_size = 1;
		$start_page_num = 1;
		$currency_type = "USD";
		$scrapping_type = "snoopy";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else if($bs_site == "kenko"){
		$search_list_regxp = "|<a href=\"/product/seibun/sei_\d+\_\d+.html\">(\d+)</a>|U";
		$search_detail_regxp = "|<a href=\"(.*)\" itemprop=\"url\">|U";
		$search_list_max_regxp = "|<a href=\"/product/seibun/sei_\d+\_\d+.html\">(\d+)</a>|U";
		$GoodsListPageName = "seibun";
		$GoodsDetailPageName = "item";
		//$Product_ID_Name = "item";
		//$PageParamName = "pageno";
		$bs_site_domain = "http://www.kenko.com/";
		$page_size = 1;
		$start_page_num = 1;
		$currency_type = "JPY";
		$scrapping_type = "snoopy";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else if($bs_site == "akachan"){
		$search_list_regxp = "|<div class=\"page\">(.*)</div>|U";
		//$search_detail_regxp = "|<strong><a href=\"(.*)\" title|U";
		//$search_detail_regxp = "|<h3><a href=\"(.*)\" title=\".*\">.*</a></h3>|U";
		$search_detail_regxp = "|<strong><a href=\"(.*)\">.*</a></strong>|U";
		//$search_list_max_regxp = "|<div class=\"page\">.*>(\d+)<.*</div>|U";
		$GoodsListPageName = "c";
		$GoodsDetailPageName = "g";
		//$Product_ID_Name = "item";
		//$PageParamName = "pageno";
		$bs_site_domain = "http://shop.akachan.jp/";
		$page_size = 1;
		$start_page_num = 1;
		$currency_type = "JPY";
		$scrapping_type = "snoopy";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else if($bs_site == "animate-onlineshop"){
		$search_list_regxp = "|<div id=\"paging\">.*\n.*\n(.*)\n.*</div>|U";
		$search_detail_regxp = "|<div class=\"imgsizefix\">.*\n.*<a href=\"(.*[\/$])\">|U";
		//$search_list_max_regxp = "|<div class=\"page\">.*>(\d+)<.*</div>|U";
		$GoodsListPageName = "index.php";
		$GoodsDetailPageName = "pd";
		//$Product_ID_Name = "item";
		$PageParamName = "pageno";
		$bs_site_domain = "http://www.animate-onlineshop.jp/";
		$page_size = 1;
		$start_page_num = 1;
		$currency_type = "JPY";
		$scrapping_type = "snoopy";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else if($bs_site == "beams"){
		$search_list_regxp = "|<span class=\"current\">.*ページ中&nbsp;(.*)ページ</span>|U";
		$search_detail_regxp = "|<a id=\".*\" class=\"item_list_link big_thumb_link\" href=\"(.*)\">|U";
		$search_list_max_regxp = "|<span class=\"current\">(.*)ページ中&nbsp;.*ページ</span>|U";
		$GoodsListPageName = "search_result.html";
		$GoodsDetailPageName = "goods.html";
		$Product_ID_Name = "gid";
		$PageParamName = "pno";
		$bs_site_domain = "http://shop.beams.co.jp";
		$page_size = 1;
		$start_page_num = 1;
		$currency_type = "JPY";
		$scrapping_type = "snoopy";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else if($bs_site == "lbreath"){
		$search_list_regxp = "|<li ><a href=\"(.*)\"  class=\"act\" >(.*)</a></li>|U";
		$search_detail_regxp = "|<dt class=\"rollover\">.*<a href=\"(.*)\" target|U";
		$search_list_max_regxp = "|<li ><a href=\"(.*)\"  class=\"act\" >(.*)</a></li>|U";
		$GoodsListPageName = "category";
		$GoodsDetailPageName = "item";
		$Product_ID_Name = "cmid";
		$PageParamName = "pageno";
		$bs_site_domain = "http://lbreath.xebio-online.com";
		$page_size = 1;
		$start_page_num = 1;
		$currency_type = "JPY";
		$scrapping_type = "snoopy";
		$category_navi_regxp = "|<title>(.*)</title>|U";
		//****************홍진영 end******************
    /**
     * 배광호 zappos 12.4.13
     */
    }else if($bs_site == "zappos"){
        $search_list_regxp = "|<div class=\"pagination\">(.*)<a href=.*|U"; // search list사용
		$search_detail_regxp = "|<a href=\"(.*)\" class=\"product.*|U";
		$search_list_max_regxp = "|<a href=\".*\" class=\"pager.*\">(.*)</a>|U"; // search list사용
        $search_list_max_regxp2 = "|.*/desc/\">.*/desc/\">(.*)</a>.*<a href=\".*\" class=\"btn secondary arrow pager.*\">|U"; // search list사용
		$GoodsListPageName = "/desc/";
		$GoodsDetailPageName = "/product/";
		$Product_ID_Name = "product";
		$PageParamName = "p"; // search list 사용
		$bs_site_domain = "http://www.zappos.com";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "snoopy";
		$currency_type = "USD";
		$category_navi_regxp = "|<title>(.*)</title>|U";
    /**
     * disney 12.4.13
     */
    }else if($bs_site == "disneystore"){
        $search_list_regxp = "|<div class=\"pagination\">(.*)<a href=.*|U"; // 사용안함
		$search_detail_regxp = "|\"link\": \"(.*)\", \"rating\"|U";
		$search_list_max_regxp = "|<a href=\".*\" class=\"pager.*\">(.*)</a>|U"; // 사용안함
		$GoodsListPageName = "/mn/";
		$GoodsDetailPageName = "/mp/";
		$Product_ID_Name = "";
		$PageParamName = "Nao"; // search list 사용
		$bs_site_domain = "http://www.disneystore.com";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "curl";
		$currency_type = "USD";
		$category_navi_regxp = "|<title>(.*)</title>|U";
    /**
     * toryburch
     *
     * 0부터 페이지 시작인 사이트 때문에
     * 페이지넘버 0에서도 상품등록 되도록 수정하려 하였으나 1부터 시작인 사이트에서 문제발생
     * 페이지를 1부터 쓰도록 수정.
     * toryburch의 경우  +1한뒤 -1값으로 상품검색
     *
     * 12.4.16 배광호
     */
    }else if($bs_site == "toryburch"){
        $search_list_regxp = "|<div class=\"pagination\">(.*)<a href=.*|U"; // 사용안함
		$search_detail_regxp = "|<a href=\"(.*)\" title|U";
		$search_list_max_regxp = "|<a href=\".*\" class=\"pager.*\">(.*)</a>|U"; // 사용안함
		$GoodsListPageName = "sc.html";
		$GoodsDetailPageName = "pd.html";
		$Product_ID_Name = "dwvar";
		$PageParamName = " "; // search list 사용
		$bs_site_domain = "http://www.toryburch.com";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "snoopy";
		$currency_type = "USD";
		$category_navi_regxp = "|<title>(.*)</title>|U";
    /**
     * BeautyQueen
     */
    }else if($bs_site == "bqueen"){
        $search_list_regxp = "|class='text01' align=center><a href=.*><font color=.*>(.*)</font>|U"; //?
		$search_detail_regxp = "|<td bgcolor=\".*\"><a href=\"(.*)\"><img src=|U";
		$search_list_max_regxp = "|class='text01' align=center><a href=.*><font color=.*>(.*)</font></a></td><td style='padding-left:15'>|U"; //?
		$GoodsListPageName = "m_mall_list.php";
		$GoodsDetailPageName = "m_mall_detail.php";
		$Product_ID_Name = "ps_goid";
		$PageParamName = "ps_page";
		$bs_site_domain = "http://www.bqueen.co.kr";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "snoopy";
		$currency_type = "KRW";
		$category_navi_regxp = "|<title>(.*)</title>|U";
    /**
     * Uptol
     */
    }else if($bs_site =="uptol"){
        $search_list_regxp = "|>([1-99])</a>|U";
		$search_detail_regxp = "|<a href=\"(.*)\">|U";
		$search_list_max_regxp = "|>([1-99])</a>] 다음|U";
		$GoodsListPageName = "category.php";
		$GoodsDetailPageName = "product.php";
		$Product_ID_Name = "product_no";
		$PageParamName = "page";
		$bs_site_domain = "http://www.uptol.com";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "snoopy";
		$currency_type = "KRW";
		$category_navi_regxp = "|<title>(.*)</title>|U";
    /**
     * Beautynet 미샤
     */
    }else if($bs_site == "beautynet"){
        $search_list_regxp = "|>([1-99])</a>|U";
		$search_detail_regxp = "|<a href='..(.*)'>|U";
		$search_list_max_regxp = "|>([1-99])</a></div>|U"; // 일단 임시로
		$GoodsListPageName = ".php";
		$GoodsDetailPageName = "detail.php";
		$Product_ID_Name = "id";
		$PageParamName = "pagenum";
		$bs_site_domain = "http://shop.beautynet.co.kr";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "snoopy";
		$currency_type = "KRW";
    /**
     * etude 에뛰드하우스
     */
    }else if($bs_site == "etude"){
        $search_list_regxp = "|>([1-99])</a>|U";
		$search_detail_regxp = "|javascript:productView\('(.*)'\);|U";
		$search_list_max_regxp = "|<a href=\"javascript:searchByTarget\('(.*)', ''\);\"><img src=\"http://image.etude.co.kr/images/common/board/btn_last.gif\"|U"; // 일단 임시로
		$GoodsListPageName = "submain";
		$GoodsDetailPageName = "view";
		$Product_ID_Name = "prdCd";
		$PageParamName = "pageNum";
		$bs_site_domain = "http://www.etude.co.kr";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "snoopy";
		$currency_type = "KRW";
    /**
     *  6pm 12.12.07
     */
    }else if($bs_site == "6pm"){
        $search_list_regxp = "|<div class=\"pagination\">\n(.*)\n<a href=.*|U"; // search list사용
		$search_detail_regxp = "|<a href=\"(.*)\" class=\"product.*|U";
		$search_list_max_regxp = "|<a href=\".*\" class=\"pager.*\">(.*)</a>|U"; // search list사용
        $search_list_max_regxp2 = "|/desc/\">(.*)</a>\n<a href=\".*\" class=\"arrow pager.*\">|U"; // search list사용
		$GoodsListPageName = "/desc/";
		$GoodsDetailPageName = "/product/";
		$Product_ID_Name = "product";
		$PageParamName = "p"; // search list 사용
		$bs_site_domain = "http://www.6pm.com";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "snoopy";
		$currency_type = "USD";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else if($bs_site == "mstyleshop"){
		$search_list_regxp = "|<a href=\".*\">\[(.*)\]</a>|U";		
		$search_detail_regxp = "|<a href=\"(.*)\" class=\"aaa\">.*</a>|U";
		$search_list_max_regxp = "|<a href=\".*\">\[(.*)\]</a>|U"; // 일단 임시로
		$GoodsListPageName = "goodlist.php";
		$GoodsDetailPageName = "goodview.php";
		$Product_ID_Name = "good_code";
		$PageParamName = "page";
		$bs_site_domain = "http://www.mstyleshop.co.kr";
		$page_size = 1;
		$start_page_num = 1;	
		$scrapping_type = "curl";
		$currency_type = "KRW";
	}else if($bs_site == "pinkboll"){
		$search_list_regxp = "|<a href=\"(.*)\" class=\"pageLink\" title=\"page (.*)\">(.*)</a>|U";
		$search_detail_regxp = "|<a href=\"(.*)\"><font style='color:#555555;font-size:12px;font-style:normal;font-weight:;text-decoration:'>|U";
		
		$search_list_max_regxp = "|<font class='brandpage'>\[(.*)\]</font>|U";//사용하지않음 kbk 13/10/15
		$GoodsListPageName = "url=Category";
		$GoodsDetailPageName = "url=Product";
		$Product_ID_Name = "product_no";
		$PageParamName = "page";
		$bs_site_domain = "http://pinkboll.co.kr/";
		$page_size = 1;
		$start_page_num = 1;
		$currency_type = "KRW";
		$scrapping_type = "curl";
		$category_navi_regxp = "|<font face=\"Arial\">(.*) Total Item <b>|U";
	}else if($bs_site == "smartturnout"){
		

		$search_list_regxp = "|<li class=\"pageLabel\">(.*)</li>|U";
		$search_detail_regxp = "|<a href=\"(.*)\" title=\".*\" class=\".*image\">|U";
		$search_list_max_regxp = "|<a href=\"http.*html\?p=(.*)\">|U";
		$GoodsListPageName = ".html";
		$GoodsDetailPageName = ".html";
		$Product_ID_Name = "productID";
		$PageParamName = "p";
		$bs_site_domain = "http://www.smartturnout.com";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "curl";
		$currency_type = "USD";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else if($bs_site == "superinc"){ // makeshop
		$search_list_regxp = "|href=\"(.*)\"|U";
		$search_detail_regxp = "|<a href=\"(.*)\"><img class=\"MS_prod_img_s\" src=\".*\">|U";
		$search_list_max_regxp = "|<div class=\"pagerw\">(.*)<!-- pagerw -->|U";
		$GoodsListPageName = "shopbrand.html";
		$GoodsDetailPageName = "shopdetail.html";
		$Product_ID_Name = "branduid";
		$PageParamName = "page";
		$bs_site_domain = "http://www.superinc.co.kr/";
		$page_size = 1;
		$start_page_num = 1;
		$currency_type = "KRW";
		$scrapping_type = "curl";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	/**
     * secondmove
     */
	}else if($bs_site == "stylekorean"){ // 2017.07.01 
		$search_list_regxp = "|href=\"(.*)\"|U";
		$search_detail_regxp = "|<a href=\"(.*)\" class=\"sct_img\">|U";
		$search_list_max_regxp = '|<a href=".*" class="pg_page">(.*)<span.*>.*</span></a>|U';
		$GoodsListPageName = "/cosmetics/";
		$GoodsDetailPageName = "/shop/";
		$Product_ID_Name = "branduid";
		$PageParamName = "page-1.html";
		$bs_site_domain = "http://www.stylekorean.com/";
		$page_size = 1;
		$start_page_num = 1;
		$currency_type = "USD";
		$scrapping_type = "curl";
		$category_navi_regxp = "|<title>(.*)</title>|U";

    }else if($bs_site =="secondmove"){
        $search_list_regxp = "|>([1-99])</a>|U";
		$search_detail_regxp = "|<a href=\"(.*)\">|U";
		$search_list_max_regxp = "|>([1-99])</a>] 다음|U";
		$GoodsListPageName = "category.php";
		$GoodsDetailPageName = "product.php";
		$Product_ID_Name = "product_no";
		$PageParamName = "page";
		$bs_site_domain = "http://www.secondmove.co.kr";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "snoopy";
		$currency_type = "KRW";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else if($bs_site =="untage" || $bs_site =="meditation" || $bs_site =="ddpopstyle" || $bs_site =="sculptorpage" || $bs_site =="babletwo" || $bs_site =="cluedeclare"  || $bs_site =="diagonal" || $bs_site =="nilbyp" || $bs_site =="augustalive" || $bs_site =="bvmall"  || $bs_site =="lazybee" || $bs_site =="normz"  || $bs_site =="springstrings" || $bs_site =="varisonc" || $bs_site =="wakami" || $bs_site =="boosticsupply" || $bs_site =="millionairehats" || $bs_site =="bonnie-blanche" || $bs_site =="sntles" || $bs_site =="amfeast" || $bs_site =="derrohe" || $bs_site =="knitted" || $bs_site =="lotuff" || $bs_site =="fascy" || $bs_site =="danharoo"){// cafe24 ,  amfeast, sntles, lotuff , fascy
		// lotuff : 신규버젼 
		// knitted : 신규버젼
        $search_list_regxp = "|>([1-99])</a>|U";//
		$search_detail_regxp = "|<a href=\"(.*)\" name=.*>|U";

		$search_list_max_regxp = "|<p class=\"last\"><a href=\"?.*&page=(.*)\">|U";
		$GoodsListPageName = "list.html";
		$GoodsDetailPageName = "detail.html";
		$Product_ID_Name = "product_no";
		$PageParamName = "page";
		if($bs_site =="untage"){
			$bs_site_domain = "http://www.untagestore.com";		
		}else if($bs_site =="meditation"){
			$bs_site_domain = "http://www.meditations.co.kr";
		}else if($bs_site =="ddpopstyle"){
			$bs_site_domain = "http://www.ddpopstyle.co.kr";
		}else if($bs_site =="sculptorpage"){
			$bs_site_domain = "http://www.sculptorpage.com";
		}else if($bs_site =="babletwo"){
			$bs_site_domain = "http://www.babletwo.com";
		}else if($bs_site =="cluedeclare"){
			$bs_site_domain = "http://www.cluedeclarestore.com";
		}else if($bs_site =="diagonal"){
			$bs_site_domain = "http://www.diagonal.co.kr";
		}else if($bs_site =="nilbyp"){
			$bs_site_domain = "http://www.nilbyp.com";
			$search_list_max_regxp = "|<p><a href=\"?.*&page=(.*)\"><img src=\".*/design/skin/fashion001/btn_pageLast.gif\".*>|U";
		}else if($bs_site =="augustalive"){
			$bs_site_domain = "http://www.augustalive.kr";
		}else if($bs_site =="bvmall"){
			$bs_site_domain = "http://www.bvmall.co.kr";
		}else if($bs_site =="lazybee"){
			$bs_site_domain = "http://www.lazybee.co.kr";	
		}else if($bs_site =="normz"){
			$bs_site_domain = "http://www.normz.co.kr";	
		}else if($bs_site =="springstrings"){
			$bs_site_domain = "http://www.springstrings.co.kr";	
		}else if($bs_site =="varisonc"){
			$bs_site_domain = "http://www.varisonc.com";	
		}else if($bs_site =="wakami"){
			$bs_site_domain = "http://www.wakami.co.kr";	
			$search_list_max_regxp = "|<p><a href=\"?.*&page=(.*)\"><img src=\".*/btn_page_last.gif\".*>|U";
		}else if($bs_site =="boosticsupply"){
			$bs_site_domain = "http://www.boosticsupply.co.kr";	
		}else if($bs_site =="millionairehats"){
			$bs_site_domain = "http://www.millionairehats.com";	
			$search_detail_regxp = "|<a href=\"(.*)\">|U";
		}else if($bs_site =="bonnie-blanche"){
			$bs_site_domain = "http://www.bonnie-blanche.com";	
			$GoodsListPageName = "list_sub.html";
			$search_detail_regxp = "|<a href=\"(.*)\">|U";
		}else if($bs_site =="sntles"){
			$bs_site_domain = "http://www.sntles.com";	
		}else if($bs_site =="amfeast"){
			$bs_site_domain = "http://www.amfeast.co.kr";	
			$search_detail_regxp = "|<a href=\"(.*)\">|U";
		}else if($bs_site =="derrohe"){
			$bs_site_domain = "http://www.derrohe.com";	
		}else if($bs_site =="knitted"){
			$bs_site_domain = "http://www.knitted.co.kr";	
		}else if($bs_site =="lotuff"){
			$bs_site_domain = "http://www.lotuff.co.kr";	
			$search_detail_regxp = "|<a href=\"(.*)\" name=\"anchorBoxName_.*\">|U";
			$GoodsDetailPageName = "/product/";
		}else if($bs_site =="fascy"){
			$bs_site_domain = "http://www.fascy.com";	
		}else if($bs_site =="danharoo"){
			$bs_site_domain = "http://danharoo.com";				
		}

		//	http://img.echosting.cafe24.com/design/skin/default/common/btn_page_last.gif
		
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "curl";
		$currency_type = "KRW";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else{
		echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert('필터 정보가 올바르지 않습니다.');location.href='/admin/admin.php'</script>";
		exit;
	}

if($bs_act == "search_list" || $bs_act == "favorite_update"){

	$min_value = 10000000000;
	$max_value = -1;



	//$fcontents = join ('', file ($list_url));
	if($scrapping_type == "curl"){
		//쿠키 생성 파일 입니다.
		//echo $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cookies/";
		if(!file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cookies/")){
			mkdir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cookies/");
			chmod($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cookies/",0777);
		}

		$cookie_nm = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cookies/cookie_".$bs_site."_".session_id().".txt";
		//echo $cookie_nm;
		//실제 로그인이 이루어지는 Curl 입니다.
		//echo $bs_site_domain;

		//print_r($result);

		if($bs_site == "gap" || $bs_site == "oldnavy" || $bs_site == "piperlime" || $bs_site == "bananarepublic.gap.co.jp"){
			$ch = curl_init();
			/*
            curl_setopt ($ch, CURLOPT_URL,$bs_site_domain);                      // 접속할 URL 주소
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);   // SSL 관련 설정 입니다.
			curl_setopt ($ch, CURLOPT_SSLVERSION,1);                  // 이부분 또한 윗 설정과 같이 SSL 관련 부분입니다.
			curl_setopt ($ch, CURLOPT_HEADER, 0);        // 페이지 상단에 헤더값 노출 유뮤 입니다. 0일경우 노출하지 않습니다.
			curl_setopt ($ch, CURLOPT_POST, 1);           // 값 전송을 POST값을 전송 하겠다는 선언 입니다.
			curl_setopt($ch, CURLOPT_POSTFIELDS, $login_data);     // 전송할 POST 값입니다.
			//curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);     // 설정 파일에 쿠키 데이터를 굽습니다.
			//curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);    // 설정 파일의 쿠키 데이터를 페이지에 넣습니다.
			curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			$results = curl_exec ($ch);
			//print_r($_POST);
			//echo $list_url;
			//echo "results : ".$results;


			curl_setopt ($ch, CURLOPT_URL,$list_url);   // 로그인후 이동할 페이지 입니다.
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);

			$category_results = curl_exec ($ch);
			*/

			$aryURL = explode("?",$list_url);
			parse_str($aryURL[1], $url_info);

			//$list_url_tmp = $bs_site_domain."/browse/categoryProductGrid.do?cid=".$url_info[cid]."&actFltr=false&sortBy=0&pageID=-1&globalShippingCountryCode=us";
            $list_url_tmp = $bs_site_domain."/resources/productSearch/v1/search?cid=".$url_info[cid]."&globalShippingCountryCode=us&locale=en_US&urlPageId=0";

           // echo $list_url_tmp."<br>";

			curl_setopt ($ch, CURLOPT_URL,$list_url_tmp);   // 로그인후 이동할 페이지 입니다.
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);

			$results = curl_exec ($ch);

			//echo "category_results : ".$category_results;
			//exit;
			curl_close ($ch);
		}else if($bs_site == "danharoo"){//$bs_site == "michaelkors" || 
		//echo $bs_url;
		$loginUrl = "http://danharoo.com/exec/front/Member/login/"; 
		$login_data = "returnUrl=http://danharoo.com/&forbidIpUrl=index.html&certificationUrl=/intro/adult_certification.html?returnUrl=&sIsSnsCheckid=&sProvider=&member_id=forbizkorea&member_passwd=shin0606";


			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL,$loginUrl);                      // 접속할 URL 주소
			//curl_setopt( $ch, CURLOPT_INTERFACE, "3.23.2.12" );
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);   // SSL 관련 설정 입니다.
			curl_setopt ($ch, CURLOPT_SSLVERSION,1);                  // 이부분 또한 윗 설정과 같이 SSL 관련 부분입니다.
			curl_setopt ($ch, CURLOPT_HEADER, 0);        // 페이지 상단에 헤더값 노출 유뮤 입니다. 0일경우 노출하지 않습니다.
			//curl_setopt( $ch, CURLOPT_HTTPHEADER, array("REMOTE_ADDR: 3.1.2.12", "HTTP_X_FORWARDED_FOR: 3.1.2.12"));
			curl_setopt ($ch, CURLOPT_POST, 1);           // 값 전송을 POST값을 전송 하겠다는 선언 입니다.
			curl_setopt($ch, CURLOPT_POSTFIELDS, $login_data);     // 전송할 POST 값입니다.
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);     // 설정 파일에 쿠키 데이터를 굽습니다.
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);    // 설정 파일의 쿠키 데이터를 페이지에 넣습니다.
			curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_VERBOSE, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$results = curl_exec ($ch);
			//echo $results;



			curl_setopt ($ch, CURLOPT_URL,$list_url);   // 로그인후 이동할 페이지 입니다.
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);
			$results = curl_exec ($ch);
			$category_results =$results;
			curl_close ($ch);
		}else{
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL,$list_url);                      // 접속할 URL 주소
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);   // SSL 관련 설정 입니다.
			curl_setopt ($ch, CURLOPT_SSLVERSION,1);                  // 이부분 또한 윗 설정과 같이 SSL 관련 부분입니다.
			curl_setopt ($ch, CURLOPT_HEADER, 0);        // 페이지 상단에 헤더값 노출 유뮤 입니다. 0일경우 노출하지 않습니다.
			curl_setopt ($ch, CURLOPT_POST, 1);           // 값 전송을 POST값을 전송 하겠다는 선언 입니다.
			curl_setopt($ch, CURLOPT_POSTFIELDS, $login_data);     // 전송할 POST 값입니다.
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);     // 설정 파일에 쿠키 데이터를 굽습니다.
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);    // 설정 파일의 쿠키 데이터를 페이지에 넣습니다.
			curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			$results = curl_exec ($ch);
			$category_results =$results;
			curl_close ($ch);
		}
		//echo "list_url : ".$list_url;
		//echo $results;
		//exit;
		//$datas = split("\n",$results);
		//|href=[\",']?(.*)[\",']>|U
		if($bs_site == "gap" || $bs_site == "oldnavy" || $bs_site == "piperlime" || $bs_site == "bananarepublic.gap.co.jp"){
			preg_match_all("|<div class=\"categoryPaging\">(.*)</div>|U",$results,$links_html, PREG_PATTERN_ORDER);
			//print_r($results);

			if($links_html[1][0] == ""){
					$min_value = 1;
					$max_value = 1;
			}

				//$search_list_regxp = "|href=[\",']?(.*)[\",']>|U";
				//$search_list_max_regxp = "|<span class=\"page_selected\">(.*)</span>|U";

		}
		
		//echo $search_list_regxp;
		//exit;
		if($bs_site == "luxgirl"){
			$results = str_replace("\n","",$results);
			preg_match_all($search_list_max_regxp,$results,$links_tmp, PREG_PATTERN_ORDER);
			$results=$links_tmp[1][0];
		}else{//untage, meditation , ddpopstyle, sculptorpage, babletwo, diagonal, nilbyp, augustalive, bvmall, lazybee, normz, springstrings, varisonc, wakami, boosticsupply, millionairehats, bonnie-blanche, amfeast, sntles, derrohe, knitted, lotuff, fascy, danharoo
			//echo $results;
			
			preg_match_all($search_list_max_regxp,$results,$links_tmp, PREG_PATTERN_ORDER);
			//print_r($links_tmp);
			//$results=$links_tmp[1][0];
			$min_value = 1;
			$max_value = @max($links_tmp[1]);
			if($max_value == 0){
				$max_value = 1;
				//preg_match_all($search_list_regxp,$results,$links, PREG_PATTERN_ORDER);
				//$links[
				//print_r($links);
				//exit;
			}
		}
	//	echo $max_value;
		
		
	//	exit;
		preg_match_all($search_list_regxp,$results,$links, PREG_PATTERN_ORDER);
		//print_r($links);
		if($bs_site == "jcrew"){
			if($links[1][0] == ""){
				$min_value = 1;
				$max_value = 1;
			}else{
				$page_info = split("of",strip_tags($links[1][0]));
				$min_value = trim($page_info[0]);
				$max_value = trim($page_info[1]);
			}
			//print_r($page_info);
			//exit;
			//preg_match_all("|<div class=\"categoryPaging\">(.*)</div>|U",$results,$links_html, PREG_PATTERN_ORDER);
		}else if($bs_site == "mstyleshop"){
			$min_value = 1;
			$max_value = $links[1][0];
		}else if($bs_site == "ralphlauren" || $bs_site == "abercrombie" || $bs_site == "hollisterco" || $bs_site == "michaelkors" || $bs_site == "ae" || $bs_site == "77kids" || $bs_site == "luxgirl" || $bs_site == "iwatoo" || $bs_site == "gumzzi" ){
			$min_value = 1;
			$max_value = 1;
		}else if($bs_site == "pinkboll"){
			$prod_total_regxp="|<font face=\"Arial\">(.*) Total Item <b>(.*)</b>|U";
			$arr_prod_total="";
			preg_match_all($prod_total_regxp,str_replace("\n","",$results),$arr_prod_total, PREG_PATTERN_ORDER);
			$prod_total_cnt=$arr_prod_total[2][0];

			$prod_limit_regxp="|&amp;limit=(.*)&amp;|U";
			$arr_prod_limit="";
			preg_match_all($prod_limit_regxp,str_replace("\n","",$results),$arr_prod_limit, PREG_PATTERN_ORDER);
			if($arr_prod_limit[1][0]!="") {
				$listing_limit=$arr_prod_limit[1][0];
				$max_value=ceil($prod_total_cnt/$listing_limit);
			} else {
				$max_value=1;
			}
			$min_value = 1;
		}
		/*
		if($bs_site == "ralphlauren"){
			$min_value = 1;
			$max_value = 1;
		}

		if($bs_site == "abercrombie"){
			$min_value = 1;
			$max_value = 1;
		}

		if($bs_site == "hollisterco"){
			$min_value = 1;
			$max_value = 1;
		}

		if($bs_site == "michaelkors"){
			$min_value = 1;
			$max_value = 1;
		}

		if($bs_site == "ae"){
			$min_value = 1;
			$max_value = 1;
		}
        if($bs_site == "77kids"){
			$min_value = 1;
			$max_value = 1;
		}

		if($bs_site == "luxgirl" || $bs_site == "iwatoo"){
			$min_value = 1;
			$max_value = 1;
		}
		*/
        if($bs_site == "disneystore"){

            $ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL,$list_url);                      // 접속할 URL 주소
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);   // SSL 관련 설정 입니다.
			curl_setopt ($ch, CURLOPT_SSLVERSION,1);                  // 이부분 또한 윗 설정과 같이 SSL 관련 부분입니다.
			curl_setopt ($ch, CURLOPT_HEADER, 0);        // 페이지 상단에 헤더값 노출 유뮤 입니다. 0일경우 노출하지 않습니다.
			curl_setopt ($ch, CURLOPT_POST, 1);           // 값 전송을 POST값을 전송 하겠다는 선언 입니다.
			curl_setopt($ch, CURLOPT_POSTFIELDS, $login_data);     // 전송할 POST 값입니다.
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);     // 설정 파일에 쿠키 데이터를 굽습니다.
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);    // 설정 파일의 쿠키 데이터를 페이지에 넣습니다.
			curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			$x = curl_exec ($ch);
			curl_close ($ch);
            //print_r($x);
            preg_match_all("|data-initialCallURL=\"(.*)\"|U",$x,$url_tmp,PREG_PATTERN_ORDER);

            $data_url = $bs_site_domain.$url_tmp[1][0];

            $ch = curl_init();
    		curl_setopt ($ch, CURLOPT_URL,$data_url);                      // 접속할 URL 주소
    		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);   // SSL 관련 설정 입니다.
    		curl_setopt ($ch, CURLOPT_SSLVERSION,1);                  // 이부분 또한 윗 설정과 같이 SSL 관련 부분입니다.
    		curl_setopt ($ch, CURLOPT_HEADER, 0);        // 페이지 상단에 헤더값 노출 유뮤 입니다. 0일경우 노출하지 않습니다.
    		curl_setopt ($ch, CURLOPT_POST, 1);           // 값 전송을 POST값을 전송 하겠다는 선언 입니다.
    		curl_setopt($ch, CURLOPT_POSTFIELDS, $login_data);     // 전송할 POST 값입니다.
    		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);     // 설정 파일에 쿠키 데이터를 굽습니다.
    		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);    // 설정 파일의 쿠키 데이터를 페이지에 넣습니다.
    		curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
    		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    		$disney_results = curl_exec ($ch);
    		curl_close ($ch);


            $min_value = 1;
            preg_match_all("|\"totalItems\": \"(.*)\"|U",$disney_results,$max_value_tmp, PREG_PATTERN_ORDER);
            if($max_value_tmp[1][0] > 96){
                $max_value = 1 + floor($max_value_tmp[1][0] / 96);
            }else{
                $max_value = 1;
            }
        }
	}else{

		$snoopy = new Snoopy;
		if($bs_site == "saksfifthavenue"){
			$snoopy->agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";
			$snoopy->referer = "http://www.saksfifthavenue.com/";
			$snoopy->cookies["TLTSID"] = "3BF3A9386ABF106A01518F1811596779";
			$snoopy->cookies["s_cc"] = true;
			$snoopy->cookies["E4X_CURRENCY"] = "USD";
			$snoopy->cookies["JSESSIONID"] = "PH0HSNbpvGFCywGnpXX6HdDBkBT4F8WTwRGQf66NJ1JGZvn4JT1V!-436849081";
			$snoopy->cookies["E4X_COUNTRY"] = "US";
		}
		//$snoopy->agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";
		//$snoopy->referer = "http://www.jcrew.com/";
		$snoopy->fetch($list_url);
		//echo $list_url;
		//exit;
		$results = $snoopy->results;
		$category_results = $results;
		//echo $results;
		//exit;

        if($bs_site == "bqueen" || $bs_site == "uptol"){
            $results = iconv("euc-kr","utf-8",$results);
        }elseif($bs_site == "kenko"||$bs_site == "akachan"||$bs_site == "beams"){
			 $results = iconv("shift_jis","utf-8",$results);
		}else{// || $bs_site == "untage" , ddpopstyle, sculptorpage, babletwo, cluedeclare, diagonal, nilbyp, augustalive, bvmall , lazybee, normz, springstrings, varisonc, wakami, boosticsupply, millionairehats, bonnie-blanche, amfeast, sntles, derrohe, knitted, lotuff, fascy, danharoo
			$results = iconv("euc-kr","utf-8",$results);
		}

		preg_match_all($search_list_regxp,$results,$links, PREG_PATTERN_ORDER);

		if($bs_site == "janieandjack"){
			$links[1][0]=str_replace("pageClicked=0","pageClicked=1",$links[1][0]);

		}

		if($search_list_max_regxp){
			if($bs_site == "lioele"){
				$search_list_max_regxp = iconv("utf-8","euc-kr",$search_list_max_regxp);
			}
			preg_match_all($search_list_max_regxp,$results,$check_max_value, PREG_PATTERN_ORDER);
		}

		if($bs_site == "bodenusa"){
			$min_value = 1;
			$max_value = 1;
		}

		if($bs_site == "gymboree"){
			$min_value = 1;
			$max_value = 1;
		}

		if($bs_site == "hannaandersson"){
			$min_value = 1;
			$max_value = 1;
		}

		if($bs_site == "coach"){
			$min_value = 1;
			$max_value = 1;
		}

		if($bs_site == "janieandjack"){
			preg_match_all($search_list_max_regxp,str_replace("\n","",$results),$check_max, PREG_PATTERN_ORDER);
			$check_max = strip_tags($check_max[1][0]);
			$check_max = str_replace("|next &gt;","",$check_max);
			$check_max = split("\|",$check_max);
			$x=count($check_max)-1;
			$check_max_value[1][0] = 1;
			$check_max_value[2][0] = intval($check_max[$x]);

			if($max_value <= 0){
				$max_value = 1;
			}
		}
        if($bs_site == "zappos"){
			if($links[1][0] == ""){
				$min_value = 1;
				$max_value = 1;
			}else{
				 // 링크페이지가 3페이지 미만일 경우 두번째 정규식 사용.
                if($check_max_value[1][0]==""){
			         preg_match_all($search_list_max_regxp2,$results,$check_max_value, PREG_PATTERN_ORDER);
                }
				$min_value = trim($links[1][0]);
				$max_value = trim($check_max_value[1][0]);
			}
            //echo $min_value."~".$max_value;
		}
        if($bs_site == "6pm"){

			if($links[1][0] == ""){
				$min_value = 1;
				$max_value = 1;
			}else{
				 // 링크페이지가 3페이지 미만일 경우 두번째 정규식 사용.
                if($check_max_value[1][0]==""){
			         preg_match_all($search_list_max_regxp2,$results,$check_max_value, PREG_PATTERN_ORDER);
                }
				$min_value = strip_tags(trim($links[1][0]));
				$max_value = trim($check_max_value[1][0]);
			}
            //echo $min_value."~".$max_value;

		}
        if($bs_site == "toryburch"){
			$min_value = 1;
            preg_match_all("|maxPage=' +(.*)\);|U",$results,$max_value_tmp, PREG_PATTERN_ORDER);
            $max_value = 1 + str_replace("+","",$max_value_tmp[1][0]);

		}
        if($bs_site == "nordstrom"){
            $min_value = 1;
            $count = count($check_max_value[1]);
            $max_value = $check_max_value[1][$count-1];
            if($max_value == ""){
                $max_value = 1;
            }
        }

        if($bs_site == "saksfifthavenue"){
            $min_value = 1;
            preg_match_all("|<span class=\"mainBoldBlackText totalRecords\">(.*)</span>|U",$results,$max_value_tmp, PREG_PATTERN_ORDER);
            $max_value_int = str_replace(",","",$max_value_tmp[1][0]);

            if($max_value_int > 60){
                $max_value = 1 + floor($max_value_int / 60);
            }else{
                $max_value = 1;
            }
        }
        if($bs_site == "bqueen"){
            $min_value = 1;
            $len = strlen($check_max_value[1][0]);
			if(substr_count(substr($check_max_value[1][0],$len-3,3),">")){// $max_value[1]값이 없을때가 있는경우 대비
				$max_value = split(">",substr($check_max_value[1][0],$len-3,3));
				$max_value = $max_value[1];
			}else{
				$max_value = $check_max_value[1][0];
			}
            if($max_value == ""){
                $max_value = 1;
            }
        }
        if($bs_site == "uptol"){
            $min_value = 1;
            //$len = strlen($check_max_value[1][0]);
            //$max_value = split(">",substr($check_max_value[1][0],$len-3,3));
            $max_value = $check_max_value[1][0];
            //print_r($check_max_value);
            if($max_value == ""){
                $max_value = 1;
            }
		}else{// || $bs_site == "untage" , ddpopstyle, sculptorpage, babletwo, cluedeclare, diagonal, nilbyp, augustalive, bvmall, lazybee, normz, springstrings, varisonc, wakami, boosticsupply, millionairehats, bonnie-blanche, amfeast, sntles, derrohe, knitted, lotuff, fascy, danharoo
			 $max_value = $check_max_value[1][0];
            //print_r($check_max_value);
            if($max_value == ""){
                $max_value = 1;
            }
        }
        if($bs_site == "beautynet"){
            $min_value = 1;
            $max_value = $check_max_value[1][0];
            if($max_value == ""){
                $max_value = 1;
            }
        }
        if($bs_site == "etude"){
            $min_value = 1;
            $max_value = $check_max_value[1][0];
            if($max_value == ""){
                $max_value = 1;
            }
        }
		if($bs_site == "aganet"){
            $min_value = 1;
            $max_value = $check_max_value[1][0];
            if($max_value == ""){
                $max_value = 1;
            }
        }
		if($bs_site == "lioele"){
            $min_value = 1;
            $max_value = $check_max_value[1][0];
            if($max_value == ""){
                $max_value = 1;
            }
        }
		if($bs_site == "wgcshop"){
            $min_value = 1;
            $max_value = $check_max_value[1][0];
            if($max_value == ""){
                $max_value = 1;
            }
        }
		if($bs_site == "babynsave"){
            $min_value = 1;

			$check_max_value_ex=array_pop($check_max_value[1]);

			preg_match_all("|ActivePage\">(.*)</li>|U",$results,$check_max_value_, PREG_PATTERN_ORDER);

			if($check_max_value_[1][0] > $check_max_value_ex){
				$max_value = $check_max_value_[1][0];
			}else{
				$max_value = $check_max_value_ex;
			}
            if($max_value == ""){
                $max_value = 1;
            }
        }

		if($bs_site == "kenko"){
			$min_value = 1;
			rsort($check_max_value[1]); //역순 정렬
            $max_value = $check_max_value[1][0];
            if($max_value == ""){
                $max_value = 1;
            }
		}
		
		if($bs_site == "akachan"){

			preg_match_all("|>(\d+)<|U",$links[0][0],$_links, PREG_PATTERN_ORDER);

			$min_value = $_links[1][0];

			rsort($_links[1]); //역순 정렬
            $max_value = $_links[1][0];

			
			if($min_value== ""){
				$min_value= 1;
			}
            if($max_value == ""){
                $max_value = 1;
            }
		}

		if($bs_site == "animate-onlineshop"){

			preg_match_all("|>(\d+)<|U",$links[0][0],$_links, PREG_PATTERN_ORDER);

			$min_value = $_links[1][0];

			rsort($_links[1]); //역순 정렬
            $max_value = $_links[1][0];

			
			if($min_value== ""){
				$min_value= 1;
			}
            if($max_value == ""){
                $max_value = 1;
            }
		}
		
		if($bs_site == "beams"){
			$max_value = $check_max_value[1][0];
		}

		if($bs_site == "lbreath"){
			$min_value= 1;

			//preg_match_all("|<p class=\"left\">検索結果<span>(.*)</span>|U",$results,$max_value_tmp, PREG_PATTERN_ORDER); //변경 되었음. 2013.12.30
			preg_match_all("|<p class=\"target\">対象商品：(.*)件|U",$results,$max_value_tmp, PREG_PATTERN_ORDER);
            $max_value_int = trim(str_replace(",","",$max_value_tmp[1][0]));

			if($max_value_int > 50){
                $max_value = 1 + floor($max_value_int / 50);
            }else{
                $max_value = 1;
            }
		}

	}


	
	//echo "search_list_regxp".$search_list_regxp;
	//echo $results;
	//exit;
	//print_r($links);
	//exit;
    /**
     * saksfifthavenue 0페이지 문제 때문에 패스하도록 수정
     */
    if($bs_site !== "saksfifthavenue" ){
        for($j=0;$j < count($links[1]);$j++){

    		//if(substr($links[1][$j],0,strlen($GoodsListPageName)) == $GoodsListPageName){
    		if(substr_count($links[1][$j],$GoodsListPageName) > 0){
    			//echo $links[1][$j]."\n";
    			$list_url_info = preg_split("/[?]+/",$links[1][$j]);
    			//exit;
    			parse_str(str_replace("&amp;","&",$list_url_info[1]));
    			//print_r($output);
    			//echo $$PageParamName."<br>";
    			if($$PageParamName != "" && $$PageParamName >= 0){
    				if($min_value > $$PageParamName){
    					$min_value = $$PageParamName;
    				}

    				if($max_value < $$PageParamName){
    					$max_value = $$PageParamName;
    				}
    			}//echo "min~max:".$min_value."~".$max_value;

    		}
    	}
    }

	//echo ($max_value+":::"+$max_value);
	//print_r($max_value);
	//exit;

	if(is_array($check_max_value)){
		if($min_value > $check_max_value[1][0]){
			$min_value = $check_max_value[1][0];
		}

		if($max_value < $check_max_value[2][0]){
			$max_value = $check_max_value[2][0];
		}
	}

	if($min_value > $start_page_num){
		$min_value = $start_page_num;
	}

	//echo $category_navi_regxp;
	if($category_navi_regxp){
		//echo $results;
		$category_datas = str_replace(array("\n","\r","\t"),"",$category_results);
		//$category_datas = str_replace("\n","",$category_datas);

		//echo $category_datas;
		//exit;

		preg_match_all($category_navi_regxp,$category_datas,$category_navi, PREG_PATTERN_ORDER);
		$orgin_category_info = strip_tags($category_navi[1][0]);
		//print_r($category_navi);
		//echo $orgin_category_info;
		//echo "<br>";
		//exit;

		if($bs_site == "luxgirl" || $bs_site == "iwatoo" || $bs_site == "gumzzi"){
			$orgin_category_info= iconv("CP949","UTF-8",$orgin_category_info);
		}
	}

	//echo "min~max:".$min_value."~".$max_value;

	if($bs_favorite){
		$db = new Database;
		$sql = "select * from shop_buyingservice_url_info where bs_site = '".$bs_site."' and  bs_list_url_md5='".md5(trim($list_url))."' ";
		//echo $sql;
		$db->query($sql);

		if(!$db->total){
			$orgin_category_info = str_replace("&nbsp;"," ",trim($orgin_category_info));//추가 kbk 13/10/16
			$orgin_category_info = str_replace("\t"," ",trim($orgin_category_info));
			$orgin_category_info = str_replace("'","\'",trim($orgin_category_info));

			$sql = "insert into shop_buyingservice_url_info set
			bsui_ix='',
			cid='".$cid2."',
			depth='".$depth."',
			bs_site = '".$bs_site."',
			bs_list_url = '".$list_url."',
			bs_list_url_md5 = '".md5($list_url)."',
			orgin_category_info='".str_replace("\t"," ",trim($orgin_category_info))."',
			currency_ix = '".$currency_ix."',
			disp='1',
			regdate=NOW()";

			$db->query($sql);
			//echo nl2br($sql);
			//exit;
		}else{
			$db->fetch();
			$buyingservice_url_info =  $db->dt;

			if($buyingservice_url_info[orgin_category_info] == ""){
				$orgin_category_info = str_replace("&nbsp;"," ",trim($orgin_category_info));//추가 kbk 13/10/16
				$orgin_category_info = str_replace("\t"," ",trim($orgin_category_info));
				$orgin_category_info = str_replace("'","\'",trim($orgin_category_info));

				$sql = "update shop_buyingservice_url_info set
							orgin_category_info = '".str_replace("\t"," ",trim($orgin_category_info))."'
							where bs_site = '".$bs_site."' and  bs_list_url_md5='".md5(trim($list_url))."' ";
				//echo $sql;
				$db->query($sql);
			}

		}


	}else{
		/*
		$sql = "update shop_buyingservice_url_info set
					last_working_date = NOW()
					where bsui_ix = '".$bsui_ix."'  ";
				//echo $sql;
				$db->query($sql);
		*/
	}
 
	$mstring = "<script language='javascript' src='../_language/language.php'></script>
	<script>
	parent.unloading();
	parent.document.search_form.start.value = '".($min_value == 10000000000 || $max_value == -1 ? "":$min_value/$page_size)."';
	parent.document.search_form.end.value = '".($min_value == 10000000000 || $max_value == -1 ? "":$max_value/$page_size)."';";

	if($this_page_order == 1){ // 역순으로 등록시 마지막페이지, 마지막 상품부터 등록됩니다.
		$mstring .= "parent.document.search_form.this_pagenum.value = '".($max_value/$page_size)."';";
	}else{
		$mstring .= "parent.document.search_form.this_pagenum.value = '".$start_page_num."';";
	}

	if($min_value == 10000000000 || $max_value == -1){
		$mstring .= "
		setTimeout(\"alert(language_data['product_bsgoods.act.php']['A'][language])\",500);";
		//'해당 URL 에는 상품 리스트 정보가 존재 하지 않습니다. 기본 URL 을 다시 한번 확인해주시기 바랍니다.'
	}else{
		$mstring .= "setTimeout(\"alert('해당 카테고리에 총 페이지 목록수는 ".(($max_value/$page_size))." 입니다. 추출하고자 하는 검색페이지를 지정후 상품가져오기 버튼을 눌러주세요')\",500);";
	}
	$mstring .= "
	</script>";
	if($bs_act == "search_list"){
	echo $mstring;
	}
}
/**
 * 목록+상품 가져오기
 *
 */
if($bs_act == "get_goods" || $bs_act == "new_goods_reg"){
	$db = new Database;
	 

	if(!$list_url){
		$list_url = "http://www1.macys.com/catalog/index.ognc?CategoryID=5449&PageID=23403948886595&kw=Dresses";
	}

	if($this_page_order == 1){ // 역순으로 등록시 마지막페이지, 마지막 상품부터 등록됩니다.
		if($start != ""){
			$start_page_num = $end;
		}
	}else{
		if($start != ""){
			$start_page_num = $start;
		}
	}
	//echo "start_page_num:".$start_page_num;
	//exit;
	
	if(!substr_count($list_url,$bs_site_domain)  && !substr_count($list_url,str_replace(array("http://","www."),"",$bs_site_domain))){
		$list_url = $bs_site_domain.$list_url;
	}

	
//	echo $list_url;
//print_r($_GET);
//print_r($_POST);

	if(!substr_count($list_url,$PageParamName)){
        if($bs_site == "toryburch" || $bs_site =="6pm" || $bs_site =="zappos" || $bs_site =="disneystore" || $bs_site == "toryburch" || $bs_site == "saksfifthavenue" || $bs_site == "bodenusa" || $bs_site == "kenko" || $bs_site == "akachan"){
            //예외
		}else if($bs_site == "stylekorean"){
			$list_url = str_replace("page-1.html","page-".$this_pagenum.".html",$list_url)	;
        }else{
    		$list_url_info = preg_split("/[?]+/",$list_url);
    		//echo count($list_url_info);
    		if(count($list_url_info) <= 1){
    			$list_url = $list_url."?$PageParamName=".$start_page_num;
    		}else{
    			$list_url = $list_url."&$PageParamName=".$start_page_num;
    		}
    		if($bs_site == "barneys"){
    			$list_url = $list_url."&sz=20";
    		}
        }
	}
    /**
     * zappos 페이지네이션 쿼리가 한글자이어서 따로 분리.
     */
     //&& !substr_count($list_url,"p=")
    if($bs_site =="zappos"){
        $list_url_info = preg_split("/[?]+/",$list_url);
		//echo count($list_url_info);
        $_pagenum = $this_pagenum -1;
        if(count($list_url_info) <= 1){
			$list_url = $list_url."?$PageParamName=".$_pagenum."&partial=true&redirect=false";
		}else{
			$list_url = $list_url."&$PageParamName=".$_pagenum."&partial=true&redirect=false";
		}
        //print_r($list_url);
    }
    /**
     * 6pm 페이지네이션 쿼리가 한글자이어서 따로 분리.
     */
     //&& !substr_count($list_url,"p=")
	//echo $list_url;
	//exit;
    if($bs_site =="6pm"){
        
        //scryed.log

        $list_url_info = preg_split("/[?]+/",$list_url);
		//echo count($list_url_info);
        $_pagenum = $this_pagenum -1;

        if(count($list_url_info) <= 1){
			$list_url = $list_url."?$PageParamName=".$_pagenum."&partial=true&redirect=false";
		}else{
			$list_url = $list_url."&$PageParamName=".$_pagenum."&partial=true&redirect=false";
		}

        //print_r($list_url);
    }
    /**
     * disneystore 상품 데이터주소 찾기
     */
    else if($bs_site =="disneystore"){
        $ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL,$list_url);                      // 접속할 URL 주소
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);   // SSL 관련 설정 입니다.
		curl_setopt ($ch, CURLOPT_SSLVERSION,1);                  // 이부분 또한 윗 설정과 같이 SSL 관련 부분입니다.
		curl_setopt ($ch, CURLOPT_HEADER, 0);        // 페이지 상단에 헤더값 노출 유뮤 입니다. 0일경우 노출하지 않습니다.
		curl_setopt ($ch, CURLOPT_POST, 1);           // 값 전송을 POST값을 전송 하겠다는 선언 입니다.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $login_data);     // 전송할 POST 값입니다.
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);     // 설정 파일에 쿠키 데이터를 굽습니다.
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);    // 설정 파일의 쿠키 데이터를 페이지에 넣습니다.
		curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		$disney_results = curl_exec ($ch);
		curl_close ($ch);

        preg_match_all("|data-initialCallURL=\"(.*)\"|U",$disney_results,$url_tmp,PREG_PATTERN_ORDER);
        //print_r($url_tmp);
        if($this_pagenum > 1){
            $num = ($this_pagenum-1)*96;
            $list_url = $bs_site_domain.$url_tmp[1][0]."&Nao=".$num;
        }else
            $list_url = $bs_site_domain.$url_tmp[1][0];

    }else if($bs_site == "toryburch"){
    /**
     * toryburch 페이지네이션
     */

        //토리버치 페이지네이션 ajax방식
        if($this_pagenum > 1){
            $_pagenum = $this_pagenum - 1;
            $start = ($_pagenum)*99;
            $list_url = $list_url."?sz=99&start=".$start."&format=ajax";
        }else{
            $list_url = $list_url."?sz=99&start=0&format=ajax";
        }//http://www.toryburch.com/handbags-view-all/handbags-view-all,default,sc.html?sz=99&start=99&format=ajax
        //print_r($list_url);
    }else if($bs_site == "saksfifthavenue"){
    /**
     * saksfifthavenue 페이지네이션
     */

        $list_url_info = preg_split("/[?]+/",$list_url);
    	parse_str($list_url_info[1]);
    	eval ("\$eval_value = \$".$PageParamName.";");
    	if($this_pagenum == ""){
    	   $this_pagenum=1;
        }
        $_pagenum = $this_pagenum-1;
        if(substr_count($list_url,$PageParamName) > 0){
            $search_str =  "$PageParamName=".$eval_value;
            $replace_str = "$PageParamName=".$_pagenum * 60;
            $list_url = str_replace($search_str,$replace_str,$list_url);
        }else{
            $list_url = $list_url."&".$PageParamName."=".$_pagenum * 60;
        }
	}else if($bs_site == "bodenusa"){
		
    }else if($bs_site == "kenko"){
		if($this_pagenum=="01" || $this_pagenum==1){
			$_list_url = explode("_",$list_url);
			$list_url="";
			if(empty($_list_url[2])){
				$list_url=$_list_url[0]."_".$_list_url[1];
			}else{
				$list_url=$_list_url[0]."_".$_list_url[1]."html";
			}
		}else{
			$_list_url = explode("_",$list_url);
			$list_url="";
			$this_pagenum=str_repeat('0',(2-strlen($this_pagenum))).$this_pagenum;
			if(empty($_list_url[2])){
				$_list_url[1]=str_replace('.html','',$_list_url[1]);
			}
			$list_url=$_list_url[0]."_".$_list_url[1]."_".$this_pagenum.".html";
		}
	 }else if($bs_site == "akachan"){
		$_list_url = explode("_",$list_url);
		$_list_url_bool=true;
		for($t=0;$t<count($_list_url);$t++){
			if(substr($_list_url[$t],0,1)=="p"){
				$_list_url[$t]="p".$this_pagenum;
				$_list_url_bool=false;
			}
		}

		if($_list_url_bool){
			$list_url=$list_url."_p".$this_pagenum;
		}else{
			$list_url=implode("_",$_list_url);
		}
	}else if($bs_site == "stylekorean"){

	} else{
    //페이지넘버 replace를 else 조건으로 수정.

    	$list_url_info = preg_split("/[?]+/",$list_url);
    	parse_str($list_url_info[1]);
    	eval ("\$eval_value = \$".$PageParamName.";");
    	if($this_pagenum == ""){
    	   $this_pagenum=1;
        }
        $search_str =  "$PageParamName=".$eval_value;
        $replace_str = "$PageParamName=".$this_pagenum*$page_size;
        $list_url = str_replace($search_str,$replace_str,$list_url);
	}
	//echo "list_url :".$list_url;
//exit;
	if($scrapping_type == "curl"){
		$cookie_nm = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cookies/cookie_".$bs_site.".txt";
		//echo $cookie_nm;
		//실제 로그인이 이루어지는 Curl 입니다.
		//echo $bs_site_domain;

		//echo $results;

		if($bs_site == "gap" || $bs_site == "oldnavy" || $bs_site == "piperlime" || $bs_site == "bananarepublic.gap.co.jp"){

            $aryURL = explode("?",$list_url);
			parse_str($aryURL[1], $url_info);

			//$list_url_tmp = $bs_site_domain."/browse/categoryProductGrid.do?cid=".trim($url_info[cid])."&actFltr=false&sortBy=0&pageID=-1&globalShippingCountryCode=us";
			$list_url_tmp = $bs_site_domain."/resources/productSearch/v1/search?cid=".$url_info[cid]."&globalShippingCountryCode=us&locale=en_US&urlPageId=0";
            //echo $list_url_tmp."<br>";
            $ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL,$list_url_tmp);   // 로그인후 이동할 페이지 입니다.
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			$results = curl_exec ($ch);
            curl_close($ch);
            //print_r($results);

		}else if($bs_site == "jcrew"){
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL,$bs_site_domain);                      // 접속할 URL 주소
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);   // SSL 관련 설정 입니다.
			curl_setopt ($ch, CURLOPT_SSLVERSION,1);                  // 이부분 또한 윗 설정과 같이 SSL 관련 부분입니다.
			curl_setopt ($ch, CURLOPT_HEADER, 0);        // 페이지 상단에 헤더값 노출 유뮤 입니다. 0일경우 노출하지 않습니다.
			curl_setopt ($ch, CURLOPT_POST, 1);           // 값 전송을 POST값을 전송 하겠다는 선언 입니다.
			curl_setopt($ch, CURLOPT_POSTFIELDS, $login_data);     // 전송할 POST 값입니다.
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);     // 설정 파일에 쿠키 데이터를 굽습니다.
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);    // 설정 파일의 쿠키 데이터를 페이지에 넣습니다.
			curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			$results = curl_exec ($ch);

			curl_setopt ($ch, CURLOPT_URL,$list_url);   // 로그인후 이동할 페이지 입니다.
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);
			$results = curl_exec ($ch);
			$category_results = $results;
			curl_close ($ch);
		}else  if($bs_site == "mstyleshop" || $bs_site == "danharoo"){
			//echo "여기--".time().":::".$list_url;
			if($bs_site == "danharoo"){
				$loginUrl = "http://danharoo.com/exec/front/Member/login/"; 
				$login_data = "returnUrl=http://danharoo.com/&forbidIpUrl=index.html&certificationUrl=/intro/adult_certification.html?returnUrl=&sIsSnsCheckid=&sProvider=&member_id=forbizkorea&member_passwd=shin0606";
			}else{

				$loginUrl = "http://www.mstyleshop.co.kr/login_exec.php";

				//이 부분은 접속 계정 등의 post 값입니다.
				$login_data = '?Surl=&aUrl=%2Flogin.php%3F&q_type=&kind_id=&kind_1=&kind_2=&kind_3=&kind_4=&kind_value=&page=&list_display=&q_goods_code&user_id=test001&user_pwd=test001'; 

				$list_url = $bs_site_domain.str_replace(array("http://","www.","mstyleshop.co.kr"),"",$list_url);

			}
			//쿠키 생성 파일 입니다.
			//$cookie_nm = "./files/cookie_mstyleshop.txt"; 
			$cookie_nm = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cookies/cookie_".$bs_site."_".session_id().".txt"; 
			if(!$ch){
				$ch = curl_init(); 
				curl_setopt ($ch, CURLOPT_URL,$loginUrl);                      // 접속할 URL 주소 
				//curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);   // SSL 관련 설정 입니다.
				//curl_setopt ($ch, CURLOPT_SSLVERSION,1);                  // 이부분 또한 윗 설정과 같이 SSL 관련 부분입니다.
				curl_setopt ($ch, CURLOPT_HEADER, 0);        // 페이지 상단에 헤더값 노출 유뮤 입니다. 0일경우 노출하지 않습니다.
				curl_setopt ($ch, CURLOPT_POST, 1);           // 값 전송을 POST값을 전송 하겠다는 선언 입니다. 
				curl_setopt($ch, CURLOPT_POSTFIELDS, $login_data);     // 전송할 POST 값입니다.
				curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);     // 설정 파일에 쿠키 데이터를 굽습니다.
				curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);    // 설정 파일의 쿠키 데이터를 페이지에 넣습니다.
				curl_setopt ($ch, CURLOPT_TIMEOUT, 30); 
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
				$results = curl_exec ($ch); 
				//echo $results;
			}

			//$ch = curl_init(); 
			
			//echo $list_url;
			curl_setopt ($ch, CURLOPT_URL,$list_url);                      // 접속할 URL 주소 
			curl_setopt ($ch, CURLOPT_POST, 0);
			
			$results = curl_exec ($ch); 

			$results = str_replace("src=\"./","src=\"http://www.mstyleshop.co.kr/",$results);
			$results = str_replace("href=\"./","href=\"http://www.mstyleshop.co.kr/",$results);


			$category_results = $results;
			curl_close ($ch); 
			//echo $results;
        }else{
			$ch = curl_init();
			//echo $list_url;
			curl_setopt ($ch, CURLOPT_URL,$list_url);                      // 접속할 URL 주소
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);   // SSL 관련 설정 입니다.
			curl_setopt ($ch, CURLOPT_SSLVERSION,1);                  // 이부분 또한 윗 설정과 같이 SSL 관련 부분입니다.
			curl_setopt ($ch, CURLOPT_HEADER, 0);        // 페이지 상단에 헤더값 노출 유뮤 입니다. 0일경우 노출하지 않습니다.
			curl_setopt ($ch, CURLOPT_POST, 1);           // 값 전송을 POST값을 전송 하겠다는 선언 입니다.
			curl_setopt($ch, CURLOPT_POSTFIELDS, $login_data);     // 전송할 POST 값입니다.
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);     // 설정 파일에 쿠키 데이터를 굽습니다.
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);    // 설정 파일의 쿠키 데이터를 페이지에 넣습니다.
			curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			$results = curl_exec ($ch);
			$category_results = $results;
			curl_close ($ch);
		}

		//echo $results;
		//exit;
		//print_r($results);
		/*
		$Tag = curl_init();
		curl_setopt( $Tag , CURLOPT_URL , "$list_url" );

		ob_start();
		curl_exec( $Tag );
		curl_close( $Tag );
		$results = ob_get_contents();
		ob_clean();
		*/

		//echo $results;
		//exit;
		//$datas = split("\n",$results);
		//preg_match_all($search_list_regxp,$results,$links, PREG_PATTERN_ORDER);
		//http://sellertool.lazadakorea.com/admin/product/product_bsgoods.act.php?scid2=&bs_act=search_list&cid2=008001006000000&depth=2&bsmode=reg&orderby=&ordertype=&scid0_1=008000000000000&scid1_1=008001000000000&scid2_1=008001006000000&scid3_1=&bs_site=untage&currency_ix=2&list_url=http%3A%2F%2Fwww.untagestore.com%2Fproduct%2Flist.html%3Fcate_no%3D51&start=&end=&this_page_order=1&this_pagenum=-1&this_url=&clearance_type=0&bs_fee_rate=0&bs_air_wt=1&dupe_process=skip&search_status=Y&disp=1&reg_goods_view=Y
//echo $bs_site;
        if($bs_site == 'disneystore'){
            $x = json_decode($results);
            for($i=0;$i < count($x->items);$i++){
                $goods_list_a_links[1][$i] = $x->items[$i]->link;
            }
            //print_r($goods_list_a_links);
        }else if($bs_site == "gap" || $bs_site == "oldnavy" || $bs_site == "piperlime" || $bs_site == "bananarepublic.gap.co.jp"){
            $x = objectToArray(json_decode($results));
            //print_r($x);
            $key = 0;
            $item_list = null;
            //$totalItemCount = $x[productCategoryFacetedSearch][productCategory][totalItemCount];
            if(!empty($x[productCategoryFacetedSearch][productCategory][childCategories])){
                $length = count($x[productCategoryFacetedSearch][productCategory][childCategories]);
                for($i = 0;$i < $length;$i++){
                    $cateId = $x[productCategoryFacetedSearch][productCategory][childCategories][$i][businessCatalogItemId];
                    $inner_length = count($x[productCategoryFacetedSearch][productCategory][childCategories][$i][childProducts]);

                    for($j=0;$j<$inner_length;$j++){
                        $itemId = $x[productCategoryFacetedSearch][productCategory][childCategories][$i][childProducts][$j][businessCatalogItemId];

                        $item_list[$key] = $bs_site_domain."/browse/product.do?cid=".$cateId."&vid=1&pid=".$itemId;
                        $key++;
                    }

                }
            }else{

                $cateId = $x[productCategoryFacetedSearch][productCategory][businessCatalogItemId];
                $inner_length = count($x[productCategoryFacetedSearch][productCategory][childProducts]);

                for($j=0;$j<$inner_length;$j++){
                    $itemId = $x[productCategoryFacetedSearch][productCategory][childProducts][$j][businessCatalogItemId];

                    $item_list[$key] = $bs_site_domain."/browse/product.do?cid=".$cateId."&vid=1&pid=".$itemId;
                    $key++;
                }
            }

            $goods_list_a_links[1] = $item_list;

        }else{
		//	echo $results;
			
			
		//	echo $search_detail_regxp;
            preg_match_all($search_detail_regxp,$results,$goods_list_a_links, PREG_PATTERN_ORDER);
		//	print_r($results);
		//	print_r($goods_list_a_links);
			//echo "aaa";
		//exit;
        }
		

	}else{
		$snoopy = new Snoopy;
		if($bs_site == "saksfifthavenue"){
			$snoopy->agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";
			$snoopy->referer = "http://www.saksfifthavenue.com/";
			$snoopy->cookies["TLTSID"] = "3BF3A9386ABF106A01518F1811596779";
			$snoopy->cookies["s_cc"] = true;
			$snoopy->cookies["E4X_CURRENCY"] = "USD";
			$snoopy->cookies["JSESSIONID"] = "PH0HSNbpvGFCywGnpXX6HdDBkBT4F8WTwRGQf66NJ1JGZvn4JT1V!-436849081";
			$snoopy->cookies["E4X_COUNTRY"] = "US";
		}

		if($bs_site == "bodenusa"){

			$snoopy->agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";
			$snoopy->referer = "http://www.bodenusa.com/";

		}
		//echo $list_url;
		$snoopy->fetch($list_url);
		$results = $snoopy->results;
		$category_results = $results;

		//echo $results;

	 
		if($bs_site == "lbreath"){
			$results = str_replace("\n","",$snoopy->results);
		}
		preg_match_all($search_detail_regxp,$results,$goods_list_a_links, PREG_PATTERN_ORDER);
		//preg_match_all($search_list_regxp,$snoopy->results,$goods_list_a_links, PREG_PATTERN_ORDER);

		
		if($bs_site == "babynsave"){
			$datas = split("\n",$results);
			unset($ProductList_line);
			unset($ProductList_end_line);
			for($i=0;$i < count($datas);$i++){
				$data = $datas[$i];
				//상품 상세설명 위치
				if(!$ProductList_line && substr_count($data,"ProductList \"")){
					$ProductList_line = $i;
				}
				if(($ProductList_line && !$ProductList_end_line) && substr_count($data,"</ul>")){
					$ProductList_end_line = $i;
				}
			}
			$ProductList_prod = "";
			for($i=$ProductList_line;$i <= $ProductList_end_line;$i++){
				$ProductList_prod .= $datas[$i];
			}
			//print_r($ProductList_prod);
			preg_match_all("|<strong><a href=\"(.*)\".*>.*</strong>|U",$ProductList_prod,$goods_list_a_links, PREG_PATTERN_ORDER);
		}

		if($bs_site == "aganet"){
			if(!$goods_list_a_links[1]){
				preg_match_all("|<iframe.*src=\"(.*)\".*</iframe>|U",$results,$iframe_link, PREG_PATTERN_ORDER);
				$iframe_link='http://www.aganet.co.kr/plan/'.$iframe_link[1][0];

				$snoopy->fetch($iframe_link);
				$results = $snoopy->results;

				preg_match_all($search_detail_regxp,$results,$goods_list_a_links, PREG_PATTERN_ORDER);
				//print_r($goods_list_a_links);
			}
		}

	}

	// print_r($list_url);
	//print_r($snoopy->results);
	//exit;
   // print_r(count($goods_list_a_links[1]));
//	print_r($goods_list_a_links[1]);
	for($x=0,$j=0;$x < count($goods_list_a_links[1]);$x++){
		if(substr_count($goods_list_a_links[1][$x],$GoodsDetailPageName)){
			if(substr_count($goods_list_a_links[1][$x],"http://")){
				$goods_detail_links[$j] = str_replace("\"","",$goods_list_a_links[1][$x]);
			}else{
				if($bs_site == "abercrombie"||$bs_site == "hollisterco"){
					$goods_detail_links[$j] = $bs_site_domain."/webapp/wcs/stores/servlet/".str_replace("\"","",$goods_list_a_links[1][$x]);
				}else if($bs_site == "bqueen"){
				    $goods_detail_links[$j] = $bs_site_domain."/mall/".str_replace("\"","",$goods_list_a_links[1][$x]);
				}else if($bs_site == "wgcshop"){
					$goods_detail_links[$j] = $bs_site_domain."wgc2008/main/".str_replace("\"","",$goods_list_a_links[1][$x]);
                }else{
					$goods_detail_links[$j] = $bs_site_domain.str_replace("\"","",$goods_list_a_links[1][$x]);
				}

				if($bs_site == "luxgirl" || $bs_site == "iwatoo" || $bs_site == "gumzzi"){
					$goods_detail_links[$j] = $bs_site_domain.str_replace("\"","",$goods_list_a_links[1][$x]);
				}
			}
			$j++;
		}else if($bs_site == "etude"){
            $goods_detail_links[$j] = $bs_site_domain."/product.do?method=view&prdCd=".$goods_list_a_links[1][$x];
            $j++;
		}else if($bs_site == "aganet"){
			$URLCODE = split(",",$goods_list_a_links[1][$x]);
            $goods_detail_links[$j] = $bs_site_domain."/product/category_detail_view.do?WG_GB_CODE=".$URLCODE[0]."&WG_DC_CODE=".$URLCODE[1];
            $j++;
		}else if($bs_site == "lioele"){
			$URLCODE = split(",",$goods_list_a_links[1][$x]);
            $goods_detail_links[$j] = $bs_site_domain."/shop/lio_shopview.php?mode=subview&gnum=".$URLCODE[1];
            $j++;
		}else if($bs_site == "6pm"){
  		    $goods_detail_links[$j] = $bs_site_domain.$goods_list_a_links[1][$x]."?zfcTest=mat%3A1";
            $j++;
		}else if($bs_site == "pinkboll"){
			$ex_url=explode("?",$goods_list_a_links[1][$x]);
			$_ex_url=explode("&",$ex_url[1]);
			foreach($_ex_url as $k=>$v) {
				$_arr=explode("=",$v);
				${$_arr[0]}=$_arr[1];
			}
  		    $goods_detail_links[$j] = "http://pinkboll.co.kr/Front/Product/?url=Product&product_no=".$product_no."&main_cate_no=".$main_cate_no."&display_group=".$display_group;;
            $j++;
		}
	}
   // print_r($goods_detail_links);


	if($bs_site == "coach"){
		for($x=0;$x < count($goods_list_a_links[1]);$x++){
			$goods_detail_links[$x] =$list_url.'#'.$goods_list_a_links[1][$x];
		}
	}


	//print_r($goods_detail_links);
	//exit;
	if(is_array($goods_detail_links)){
		$goods_detail_unique_links = array_unique($goods_detail_links);

	}
	
	//echo "여기--";
	//exit;
	//print_r($goods_detail_unique_links);
	//exit;

	$mstring = "";
	$mstring .=  "<script language='JavaScript' src='../js/jquery-1.4.js'></Script>\n
	<script language='javascript' src='../_language/language.php'></script>\n
	<script language='javascript'>
	var goods_detail_link = new Array();\n";
	/*
	for($bs_i=0;$bs_i < count($goods_detail_unique_links);$bs_i++){
		echo "goods_detail_link[".$bs_i."] = '".str_replace("&amp;","&",$goods_detail_unique_links[$bs_i])."';\n";
	}
	*/

	if(is_array($goods_detail_unique_links)){
		$bs_i=0;
		foreach($goods_detail_unique_links as $key => $value){
			$mstring .=  "goods_detail_link[".$bs_i."] = '".str_replace("&amp;","&",$value)."';\n";
			$bs_i++;
		}
	}

	//exit;
	if($this_page_order == 1){ // 역순으로 등록시 마지막페이지, 마지막 상품부터 등록됩니다.
		$mstring .= " var bs_i = goods_detail_link.length-1;";
	}else{
		$mstring .= " var bs_i = 0;";
	}


	$mstring .= "
	//alert(goods_detail_link[bs_i]);
	var reg_goods_view = '".$reg_goods_view."';
	var this_page_order = '".$this_page_order."';
	var this_pagenum = '".$this_pagenum."';
	var start = '".$start."';
	var end = '".$end."';
	if(goods_detail_link.length > 0 && parseInt(this_pagenum) > 0){
		if(parseInt(start) <= parseInt(this_pagenum) &&  parseInt(this_pagenum) <= parseInt(end) ){
				buyingservice_goods_reg();
		}else{
			parent.unloading();
		}
	}else{
		parent.unloading();
	}

	function buyingservice_goods_reg(){
		//alert(bs_i+'::'+goods_detail_link[bs_i]);
		parent.document.search_form.this_url.value = goods_detail_link[bs_i];//

		$.ajax({
				type: 'POST',
				data:
					{'bs_act': 'bsgoods_one_reg','company_id': '".$company_id."','cid2': '".$cid2."','disp': '".$disp."','depth': '".$depth."','bs_site':'".$bs_site."','clearance_type':'".$clearance_type."','bs_fee_rate':'".$bs_fee_rate."','bs_air_wt':'".$bs_air_wt."','currency_ix':'".$currency_ix."','usable_round':'".$usable_round."','round_precision':'".$round_precision."','round_type':'".$round_type."','goods_detail_link': goods_detail_link[bs_i]},
				url: 'product_bsgoods.act.php',
				dataType: 'html',
				async: true,
				beforeSend: function(){
					 //   $('#loading-dialog').dialog({minHeight: 100, width: 250}).dialog('open');
					 //   $('#loading-dialog p').text('Loading '+cal+' Calendar Events');
				},
				success: function(data){

						try{
							parent.document.getElementById('loadingbar').innerHTML = \"<table align=center><tr><td><img src='/admin/images/indicator.gif' border=0 width=32 height=32 align=absmiddle> </td><td>[\"+data+\"] <br>\"+goods_detail_link[bs_i]+\"</td></tr></table> \";
							if(reg_goods_view == 'Y'){
								//parent.window.frames['act'].location.href='product_bsgoods.php?bsmode=reg&view=innerview&cid2=".$cid2."&depth=".$depth."';
								parent.window.frames['act'].location.href='product_bsgoods.php?bsmode=reg&view=innerview&cid2=".$cid2."&depth=".$depth."';//표준방식으로 변경 kbk 13/10/18
							}

							if(this_page_order == 1){

									if(bs_i > 0){
										if(parent.document.search_form.search_status[0].checked){
											bs_i--;
											setTimeout(\"buyingservice_goods_reg()\",900);
										}else{
											if(reg_goods_view == 'N'){
												//parent.window.frames['act'].location.href='product_bsgoods.php?bsmode=reg&view=innerview&cid2=".$cid2."&depth=".$depth."';
												parent.window.frames['act'].location.href='product_bsgoods.php?bsmode=reg&view=innerview&cid2=".$cid2."&depth=".$depth."';//표준방식으로 변경 kbk 13/10/18
											}
											parent.unloading();
										}
									}else{
										parent.document.search_form.cid2.value ='$cid2';
										parent.document.search_form.depth.value ='$depth';
										//alert(1);
										getBuyingServiceInfoNextPage();
										//alert(2);
									}
							}else{

									if(goods_detail_link.length > bs_i){
										if(parent.document.search_form.search_status[0].checked){

											bs_i++;
											setTimeout(\"buyingservice_goods_reg()\",900);
										}else{
											if(reg_goods_view == 'N'){
												//parent.window.frames['act'].location.href='product_bsgoods.php?bsmode=reg&view=innerview&cid2=".$cid2."&depth=".$depth."';
												parent.window.frames['act'].location.href='product_bsgoods.php?bsmode=reg&view=innerview&cid2=".$cid2."&depth=".$depth."';//표준방식으로 변경 kbk 13/10/18
											}
											parent.unloading();
										}
									}else{
										parent.document.search_form.cid2.value ='$cid2';
										parent.document.search_form.depth.value ='$depth';
										//alert(3);
										getBuyingServiceInfoNextPage();
										//alert(4);
									}
							}
						}catch(e){
							alert(e.message + '['+goods_detail_link[bs_i]+']<--bs_i : ['+bs_i+']');
							//setTimeout(\"buyingservice_goods_reg()\",900);
						}


				}
			});

	}
	//}
	";


	$search_str =  "$PageParamName=".$eval_value;
	if($this_page_order == 1){
		$replace_str = "$PageParamName=".(($this_pagenum-1)*$page_size);
	}else{
		$replace_str = "$PageParamName=".(($this_pagenum+1)*$page_size);
	}

	$next_list_url = str_replace($search_str,$replace_str,$list_url);

	$mstring .= "
	var this_page_order = '".$this_page_order."';

	function getBuyingServiceInfoNextPage(){
		parent.unloading();
		if(parent.document.search_form.search_status[0].checked){

			if(this_page_order == 1){
				//alert(parseInt(parent.document.search_form.end.value) +'>= '+parseInt(parent.document.search_form.this_pagenum.value));
				var checkd_page = parseInt(parent.document.search_form.end.value) >= parseInt(parent.document.search_form.this_pagenum.value);
			}else{
				//alert(parseInt(parent.document.search_form.end.value) +'<= '+parseInt(parent.document.search_form.this_pagenum.value));
				var checkd_page = parseInt(parent.document.search_form.end.value) <= parseInt(parent.document.search_form.this_pagenum.value);
			}
			//alert(this_page_order);
			if(checkd_page){
				if(parent.document.search_form.this_pagenum.value <= 0){
					//parent.document.search_form.this_pagenum.value = 0;
				}else{
					//alert(this_page_order);
					if(this_page_order == 1){
							parent.document.search_form.this_pagenum.value = parseInt(parent.document.search_form.this_pagenum.value)-1;
					}else{
							parent.document.search_form.this_pagenum.value = parseInt(parent.document.search_form.this_pagenum.value)+1;
					}

					parent.document.search_form.this_url.value = '".$next_list_url."';
				}
				//parent.window.frames['act'].location.href='product_bsgoods.php?bsmode=reg&view=innerview';
				parent.window.frames['act'].location.href='product_bsgoods.php?bsmode=reg&view=innerview';//표준방식으로 변경 kbk 13/10/18
				parent.checkSearchFrom(parent.document.search_form,'get_goods');
			}
		}
	}
	</script>";

	if(substr_count($_SERVER["REQUEST_URI"],"product_bsgoods.act.php") > 0){
	echo $mstring;
	}

}


if($bs_act == "bsgoods_pcode_update" || $bs_act == "bsgoods_one_reg" || $bs_act == "bsgoods_one_update"  || $bs_act == "bsgoods_one_stock_update"){
			$db = new Database;
			$db2 = new Database;


			//$db->debug = true;
			$bs_url = str_replace("&amp;","&",$goods_detail_link);
			$bs_url = str_replace(" ","%20",$bs_url);
			//$bs_url = "http://www.gymboree.com/shop/dept_item.jsp?PRODUCT%3C%3Eprd_id=845524446004872&amp;FOLDER%3C%3Efolder_id=2534374306269293&amp;ASSORTMENT%3C%3East_id=1408474395917465&amp;bmUID=1331651834295&amp;productSizeSelected=0&amp;fit_type=";

			if($bs_site == "barneys"){
				//echo $bs_url;
				$__bs_url = split("[/]",$bs_url);
				//print_r($__bs_url);
				//echo count($__bs_url);
				$_goods_view_url = split(",",$__bs_url[count($__bs_url)-1]);
				//print_r($_goods_view_url);
				$pcode = $_goods_view_url[0];
				//$bs_url = urlencode($bs_url);
				//echo "pcode : ".$pcode ;
				//exit;
			}else if($bs_site == "nordstrom"){
				$__bs_url = split("[/?]",$bs_url);
				$pcode = $__bs_url[5];
			}else if($bs_site == "jcrew"){
				$__bs_url = split("[/]",$bs_url);
				//print_r($__bs_url);
				//exit;
				if(substr_count($__bs_url[6],"RDOVR")){
					$_pcode = split("~",$__bs_url[6]);
				}else if(substr_count($__bs_url[5],"RDOVR")){
					$_pcode = split("~",$__bs_url[5]);
				}else if(substr_count($__bs_url[4],"RDOVR")){
					$_pcode = split("~",$__bs_url[4]);
				}
				$pcode = $_pcode[1];
			}else if($bs_site == "bodenusa"){
				$__bs_url = split("[/]",$bs_url);
				$_pcode = split("~",$__bs_url[6]);
				$pcode = $__bs_url[count($__bs_url)-2];
			}else if($bs_site == "gap" || $bs_site == "oldnavy" || $bs_site == "piperlime" || $bs_site == "bananarepublic.gap.co.jp"){
				//echo $bs_url;
				$__bs_url = split("[?]",$bs_url);
				parse_str($__bs_url[1], $paraminfos);
				//print_r($paraminfos);
				$pcode = $paraminfos[$Product_ID_Name];
				if($pcode == ""){
					$pcode = $paraminfos["pid"];
				}
			}else if($bs_site =="disneystore"){
                $__bs_url = split("[/]",$bs_url);
				$pcode = $__bs_url[5];
                //print_r($__bs_url);
            }else if($bs_site == "zappos"){
                $__bs_url = split("[/]",$bs_url);
				$pcode = $__bs_url[4];
            }else if($bs_site == "6pm"){
                $_snoopy = new Snoopy;
                $_snoopy->fetch($bs_url);
                $_results =  $_snoopy->results;
                $pcode = "";
                preg_match_all("|<input type=\"hidden\" name=\"productId\" value=\"(.*)\" />|U",$_results,$pcode_tmp, PREG_PATTERN_ORDER);
                $pcode = $pcode_tmp[1][0];

                //$__bs_url = split("[/]",$bs_url);
				//$pcode = $__bs_url[4];
                //print_r($bs_url."-----".$pcode);
                
                
            }else if($bs_site == "janieandjack"){
                preg_match_all("|prd_id=(.*)&|U",$bs_url,$__bs_url, PREG_PATTERN_ORDER);
                $pcode = $__bs_url[1][0];
            }else if($bs_site == "hannaandersson"){
                $__bs_url = split("[?]",urldecode($bs_url));
				$_bs_url = split("[=]",$__bs_url[1]);
                $pcode = trim(str_replace("|","",$_bs_url[1]));
			}else if($bs_site == "coach"){
                $__bs_url = split("[#]",$bs_url);
				$pcode = $__bs_url[1];
			}else if($bs_site == "ae"){
				$__bs_url = split("[?]",$bs_url);
				parse_str($__bs_url[1], $paraminfos);
				$pcode = $paraminfos[$Product_ID_Name];
				if($pcode == ""){
					$pcode = $paraminfos["productId"];
				}
				//print_r($pcode);
            }else if($bs_site == "77kids"){
                $__bs_url = split("[/]",$bs_url);
                $_bs_url = split("[?]",$__bs_url[6]);
                $pcode = $_bs_url[0];
            }else if($bs_site == "toryburch"){
                preg_match_all("|dwvar_(.*)_|U",$bs_url,$__bs_url, PREG_PATTERN_ORDER);
                $pcode = $__bs_url[1];
            }else if($bs_site == "bqueen"){
                $__bs_url = split("[&]",$bs_url);
               	$_bs_url = split("[=]",$__bs_url[1]);
				$pcode = $_bs_url[1];
            }else if($bs_site == "uptol"  || $bs_site == "untage"  || $bs_site == "meditation"  || $bs_site == "ddpopstyle" || $bs_site == "sculptorpage" || $bs_site == "babletwo" || $bs_site == "cluedeclare"  || $bs_site == "diagonal" || $bs_site == "nilbyp" || $bs_site == "augustalive" || $bs_site == "bvmall" || $bs_site == "lazybee" || $bs_site =="normz"  || $bs_site =="springstrings" || $bs_site =="varisonc" || $bs_site =="wakami" || $bs_site =="boosticsupply"  || $bs_site =="millionairehats" || $bs_site =="bonnie-blanche" || $bs_site =="sntles" || $bs_site =="amfeast" || $bs_site =="derrohe" || $bs_site =="knitted" || $bs_site =="fascy" || $bs_site =="danharoo"){//  cafe24  amfeast, sntles, derrohe, knitted, lotuff, fascy, danharoo
                $___bs_url = split("[?]",$bs_url);
                $__bs_url = split("[&]",$___bs_url[1]);
               	$_bs_url = split("[=]",$__bs_url[0]);
				$pcode = $_bs_url[1];
			}else if($bs_site == "babynsave"){
				$_bs_url=split("/",$bs_url);
				$pcode = $_bs_url[3];
			}else if($bs_site == "kenko"){
				$_bs_url=explode("_",$bs_url);
				$pcode = str_replace(".html","",$_bs_url[1]);
			}else if($bs_site == "akachan"){
				$_bs_url=explode("/g/g",$bs_url);
				$pcode = $_bs_url[1];
			}else if($bs_site == "animate-onlineshop"){
				$_bs_url=explode("/pd/",$bs_url);
				$pcode = str_replace("/","",$_bs_url[1]);
			}else if($bs_site == "pinkboll"){
				$_bs_url=explode("?",$bs_url);
				$__bs_url=explode("&",$_bs_url[1]);
				$___bs_url=explode("=",$__bs_url[1]);
				$pcode = $___bs_url[1];
			}else if($bs_site == "smartturnout"){
				//echo $bs_url;
				$pcode = str_replace(".html","",$pcode);
				$pcode = md5(str_replace($bs_site_domain,"",$pcode));
			}else if($bs_site == "lotuff"){
				$_bs_url=split("/",$bs_url);
				$pcode = $_bs_url[3];
			}else if($bs_site == "stylekorean"){
				$_bs_url=split("/",$bs_url);
				//echo "<b>bs_url :".$_bs_url."</b><br><br>";
				//print_r($_bs_url);
				$pcode = $_bs_url[5];				
            }else{
				$__bs_url = split("[?]",$bs_url);
				parse_str($__bs_url[1], $paraminfos);
				$pcode = $paraminfos[$Product_ID_Name];
			}
			//print_r($_POST);
			echo "brfore pcode : ".$pcode."<br>";
			if(trim($pcode) == ""){
				$pcode = $_POST["pcode"];
			}else{
				$pcode = substr($bs_site."_".$pcode,0,50); //pcode가 varcar(50) 이기 떄문에
			}
			echo "after pcode : ".$pcode."<br>";
			//echo "<br><b style='color:red;'>pid : ".$pid."</b><br>";

			//echo "bs_url:".$bs_url;
			//echo "currency_ix:".$_POST[currency_ix];
			//exit;
 
			if($pid){
			$sql = "select p.id, p.pname, p.pcode, pbp.clearance_type, pbp.bs_fee_rate, pbp.air_wt , p.round_type, p.round_precision, p.currency_ix
						from shop_product p left join shop_product_buyingservice_priceinfo pbp on p.id = pbp.pid
						where pbp.bs_use_yn = '1' and p.id = '".trim($pid)."'  ";
			}else{
			$sql = "select p.id, p.pname, p.pcode, pbp.clearance_type, pbp.bs_fee_rate, pbp.air_wt , p.round_type, p.round_precision, p.currency_ix
						from shop_product p left join shop_product_buyingservice_priceinfo pbp on p.id = pbp.pid
						where pbp.bs_use_yn = '1' and p.pcode = '".trim($pcode)."'  ";
			}
			if($bs_act == "bsgoods_one_reg"){
			//syslog(LOG_INFO, "sql:".$sql." ".$bs_act."\r");
			}

			$db->query ($sql);

			if(false){
			echo "<br><br>";
			echo nl2br($sql)."<br><br><br><br>";
			//exit;
			//echo $sql;//orgin_price, exchange_rate, air_wt, air_shipping , duty, clearance_fee, bs_fee_rate, bs_fee, clearance_type
			
			echo "<b>pcode:".$pcode."::::pid : ".$pid."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b><br>" ;
			echo "total : ".$db->total."<br><br>";
			echo "bs_act:".$bs_act;
			
			}
			if($db->total){
				echo "<b style='color:red;'>상품정보 있음</b><br>";
				// 상품 정보가 있을경우 기존 상품 정보에서 통관타입
				$db->fetch();
				$pid = $db->dt[id];
				$pname = $db->dt[pname];
				$round_type = $db->dt[round_type];
				$round_precision = $db->dt[round_precision];

				$clearance_type = $db->dt[clearance_type]; // 상품정보가 있을경우 통관타입은 기존 상품 정보에서 가져옴
				if(!$bs_fee_rate){
					$bs_fee_rate = $db->dt[bs_fee_rate];	 // 상품정보가 있을경우 구매대행 수수료율 기존 상품 정보에서 가져옴
				}
				$bs_air_wt = $db->dt[air_wt];	 // 상품정보가 있을경우 구매대행 수수료율 기존 상품 정보에서 가져옴
				if($currency_ix == ""){
					$currency_ix =  $db->dt[currency_ix];
				}
				//echo "환율정보 : ".$currency_ix."<br>";

				if($pcode != $db->dt[pcode]){
					$sql = "update shop_product set pcode = '".$pcode."' where id = '".$pid."' ";
					//echo $sql;
					$db2->query ($sql);
				}
			}else{
				echo nl2br($sql);
				echo "<b style='color:red;'>상품정보 없음(pid : ".$pid.")</b><br>";
				if($pid){
					$sql = "update shop_product set pcode = '".$pcode."' where id = '".$pid."' ";
					//echo $sql;
					$db2->query ($sql);

					$sql = "select p.id, p.pcode, pbp.clearance_type, pbp.bs_fee_rate, pbp.air_wt , p.round_type, p.round_precision
							from shop_product p, shop_product_buyingservice_priceinfo pbp
							where p.id = pbp.pid and pbp.bs_use_yn = '1' and p.pcode = '".trim($pcode)."'  ";

					$db->query ($sql);

					if($db->total){
						// 상품 정보가 있을경우 기존 상품 정보에서 통관타입
						$db->fetch();
						$pid = $db->dt[id];
						$round_type = $db->dt[round_type];
						$round_precision = $db->dt[round_precision];

						$clearance_type = $db->dt[clearance_type]; // 상품정보가 있을경우 통관타입은 기존 상품 정보에서 가져옴
						if(!$bs_fee_rate){
							$bs_fee_rate = $db->dt[bs_fee_rate];	 // 상품정보가 있을경우 구매대행 수수료율 기존 상품 정보에서 가져옴
						}
						$bs_air_wt = $db->dt[air_wt];	 // 상품정보가 있을경우 구매대행 수수료율 기존 상품 정보에서 가져옴
						if($currency_ix == ""){
							$currency_ix =  $db->dt[currency_ix];
						}
					}
				}
			}

			// 상품등록일 경우 기본 구매대행 환율정보 및 수수료 정보를 읽어온다.
			$sql = "select * from shop_buyingservice_info where exchange_type = '".$currency_ix."' order by regdate desc limit 1 ";

			$db2->query ($sql);

			if($db2->total){
				$db2->fetch();
				$usable_round = $db2->dt[usable_round];
				$round_type = $db2->dt[round_type];
				$round_precision = $db2->dt[round_precision];

				if($clearance_type == '9'){// 국내상품일 경우
					$buying_service_currencyinfo[exchange_rate] = $db2->dt[exchange_rate]; // 환율 정보 최근 환율 정보를 가져옴
					$buying_service_currencyinfo[bs_basic_air_shipping] = 0; // 기본 1파운드 항공 운송료  최근 정보를 가져옴
					$buying_service_currencyinfo[bs_add_air_shipping] = 0; // 추가 1파운드 항공 운송료 최근정보를 가져옴
				}else{
					$buying_service_currencyinfo[exchange_rate] = $db2->dt[exchange_rate]; // 환율 정보 최근 환율 정보를 가져옴
					$buying_service_currencyinfo[bs_basic_air_shipping] = $db2->dt[bs_basic_air_shipping]; // 기본 1파운드 항공 운송료  최근 정보를 가져옴
					$buying_service_currencyinfo[bs_add_air_shipping] = $db2->dt[bs_add_air_shipping]; // 추가 1파운드 항공 운송료 최근정보를 가져옴
				}


				if($clearance_type == '1' || $clearance_type == '9'){ // 통관타입이 1 : 목록통관일경우 관세/부가세, 통관수수료 면제
					$buying_service_currencyinfo[bs_duty_rate] = 0; // 관세율 최근정보를 가져옴
					$buying_service_currencyinfo[clearance_fee] = 0; // 통관수수료 최근정보를 가져옴
					$buying_service_currencyinfo[bs_supertax_rate] = 0; // 부가세율 통관수수료 면제
				}else{
					$buying_service_currencyinfo[bs_duty_rate] = $db2->dt[bs_duty];
					$buying_service_currencyinfo[clearance_fee] = $db2->dt[clearance_fee];
					$buying_service_currencyinfo[bs_supertax_rate] = $db2->dt[bs_supertax_rate];
				}
				$buying_service_currencyinfo[bs_fee_rate] = $db2->dt[bs_fee_rate];
				$bs_fee_rate = $db2->dt[bs_fee_rate];
				//echo "bs_fee_rate :".$bs_fee_rate."<br>";
			}



			if($db->total == 0 || $dupe_process == "update" || $bs_act == "bsgoods_one_update" || $bs_act == "bsgoods_one_stock_update"){
				// 상세 이미지 복사 이미지 옵션
 
				//echo "exchange_rate:".$buying_service_currencyinfo[exchange_rate]."<br>";
				//echo  "<b style='color:red;'>buyingService.filter.".$bs_site.".php</b><br>";
				//echo "<br><b style='color:red;'>include before pcode : ".$pcode."</b><br>";
				include "buyingService.filter.".$bs_site.".php";
				//echo "<br><b style='color:red;'>include after pname : ".$pname."</b><br>";
				//echo "<br><b style='color:red;'>include after pcode : ".$pcode."</b><br>";
				//echo "pcode:".$pcode."<br>";
				//echo "exchange_rate:".$buying_service_currencyinfo[exchange_rate]."<br>";
				//echo  "buyingService.filter.".$bs_site.".php";
				//exit;
				//echo "price:".$price."<br><br>";
				//echo "pcode:".$pcode."<br>";
				/*
				if($stock_bool){
					echo "재고 있음";
				}else{
					echo "재고 없음";
				}
				*/

				if($pname && $price && $stock_bool){

						if($dupe_process == "update" || $bs_act == "bsgoods_one_update"){
							$act = "update";
						}else{
							$act = "insert";
						}

						$pname = str_replace("\t"," ",$pname);
						$pname = str_replace("'","\'",$pname);
						$pcode = $pcode;

						$delivery_company = "MI";
						$stock = "999999";
						$safestock = "10";
						$stock_use_yn = "N";
						$surtax_yorn = "N";

						$bs_goods_url = $bs_url;
                        /**
                         * 구매대행 가격 계산 함수 12.4.16 배광호
                         *
                         * BuyingServicePriceCalcurate($price,$buying_service_currencyinfo)
                         *
                         * return array[air_shipping,bs_duty,bs_supertax,buyingservice_coprice,bs_fee]
                         *
                         */
                        $bs_fee_array = BuyingServicePriceCalcurate($price,$buying_service_currencyinfo);

                        /**
                         * 배열값으로 바로 사용할지 옮길지 고민
                         */
                        $air_shipping = $bs_fee_array[air_shipping];
                        $buyingservice_coprice = $bs_fee_array[buyingservice_coprice];
                        $bs_duty = $bs_fee_array[bs_duty];
                        $bs_supertax = $bs_fee_array[bs_supertax];
                        $bs_fee = $bs_fee_array[bs_fee];
						// 구매대행 수수료
						$standard_basic = $cid2;
						$display_standard_category[0] = $cid2;
						//if($cid2 != ""){
							//category->display_category 20130826 hong
							//$category[0] = $cid2;
						//	$display_category[0] = $cid2;
						//}

						$basic = $cid2; // 기본카테고리지정

						if($company_id != ""){
							$admin = $company_id;
						}



						$bimg_text = $prod_img_src;
						$img_url_copy = 1;

						$prod_desc_prod = str_replace("'","\'",$prod_desc_prod);
						//echo $prod_desc_prod;
						//exit;
						//$shotinfo = $prod_desc_prod;
						$basicinfo = $prod_desc_prod;
//echo $basicinfo;

						$orgin_price = $price;
						//$buying_service_currencyinfo[exchange_rate] = $buying_service_currencyinfo[exchange_rate];
						//$buying_service_currencyinfo[bs_basic_air_shipping] = $air_shipping;
						$air_wt = $bs_air_wt;  // 예상무계 어떻게 할껀지 확인필요
						$duty = $bs_duty+$bs_supertax;
						$buying_service_currencyinfo[clearance_fee] = $buying_service_currencyinfo[clearance_fee];


						$coprice = $buyingservice_coprice;

                        /**
                         * 가격반올림/버림 12.4.16 배광호
                         *
                         * PriceRoundUpOrDown($round_type,$round_precision,$buyingservice_coprice,$bs_fee)
                         *
                         * return array[listprice,sellprice]
                         */
						// echo "round_type : ".$round_type."<br>";
						//  echo "round_precision : ".$round_precision."<br>";
						$round_result_array = PriceRoundUpOrDown($round_type,$round_precision,$buyingservice_coprice,$bs_fee);

						$listprice = $round_result_array[listprice];
                        $sellprice = $round_result_array[sellprice];

						$product_type = "1"; // 상품 타입이 구매대행으로 설정
						//print_r($option);
						//print_r($option2);
						//exit;
						//echo "option:";
						//print_r($option);
						if(is_array($option)){
							$options[0][option_type] = "9";
							$options[0][option_name] = $option[0];
							$options[0][option_kind] = "s";
							$options[0][option_use] = "1";

							for($i=1;$i < count($option);$i++){
								$options[0][details][$i-1][option_div] = $option[$i];
								$options[0][details][$i-1][price] = "";
								$options[0][details][$i-1][etc1] = $option[$i];

							}
						}

						if(is_array($option2)){
							$options[1][option_type] = "9";
							$options[1][option_name] = $option2[0];
							$options[1][option_kind] = "s";
							$options[1][option_use] = "1";

							for($i=1;$i < count($option2);$i++){
								$options[1][details][$i-1][option_div] = $option2[$i];
								$options[1][details][$i-1][price] = "";
								$options[1][details][$i-1][etc1] = $option2[$i];

							}
						}
                        /**
                         * 가격재고 옵션일때 환율적용하기
                         * 12.4.16 배광호
                         */
                        if(is_array($options)){
                            if($options[0][option_kind] == "b"){
								//20130906 Hong
								$stock_use_yn = "Q";
                                for($i=0;$i < count($options[0][details]);$i++){
                                    $bs_fee_array_sub = BuyingServicePriceCalcurate($options[0][details][$i][price],$buying_service_currencyinfo);
                                    $round_result_array_sub = PriceRoundUpOrDown($round_type,$round_precision,$bs_fee_array_sub[buyingservice_coprice],$bs_fee_array_sub[bs_fee]);
                                    $options[0][details][$i][origin_price] = $options[0][details][$i][price];
									$options[0][details][$i][soldout] = $options[0][details][$i][soldout];
                                    $options[0][details][$i][price] = $round_result_array_sub[sellprice];
                                }
                            }
                        }

				}else{

				}

				if($pname && $price && $stock_bool){

						//echo "dupe_process:".$dupe_process.":::".$bs_act;
						if($dupe_process == "update" || $bs_act == "bsgoods_one_update" || $bs_act == "bsgoods_one_stock_update"){
							$goods_update_complete_cnt++;
							syslog(LOG_INFO, "<br>".$a." $pname 상품을 업데이트 중입니다. <b>pname : $pname price : $price pcode : $pcode pid : $pid</b><br>\r");
							echo "<br> $pname 상품을 업데이트 중입니다. <br><b>pname : $pname price : $price code : $pcode  pid : $pid</b><br>";

							if($img_update == "Y"){
									include_once("../lib/imageResize.lib.php");
									syslog(LOG_INFO, "<br>".$a." 이미지 정보 업데이트<b>$bimg_text</b><br>\r");
									echo ( "<br>".$a." 이미지 정보 업데이트<b>$bimg_text</b><br>\r");

									if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/")){
										mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/");
										chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/",0777);
									}

									if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/")){
										mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/");
										chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/",0777);
									}

									$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product", $pid, 'Y');
									$adduploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg", $pid, 'Y');
									//$image_info = getimagesize ($allimg);
									//$image_type = substr($image_info['mime'],-3);
									//$image_width = $image_info[0];
									$image_db = new Database;
									$image_db->query("select * from shop_image_resizeinfo order by idx");
									$image_info2 = $image_db->fetchall();

									if($allimg_size != 0 || ($bimg_text && $img_url_copy)){

										if($bimg_text && $img_url_copy){
											//echo str_replace("$","\$",$bimg_text);
											copy(str_replace("$","\$",$bimg_text), $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif");
											$basic_img_src = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif";
											chmod($basic_img_src,0777);
											$image_info = getimagesize ($basic_img_src);
											//print_r($image_info);
											$image_type = substr($image_info['mime'],-3);

											$chk_mimg = 1;
											$chk_msimg = 1;
											$chk_simg = 1;
											$chk_cimg = 1;
										}else{
											$image_info = getimagesize ($allimg);
											$image_type = substr($image_info['mime'],-3);
											$image_width = $image_info[0];

											//var_dump($_FILES);


											//echo($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif");

											copy($allimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif");

											//echo($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif");

											$basic_img_src = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif";
											chmod($basic_img_src,0777);
										}

										if($image_info[0] > $image_info[1]){
											$image_resize_type = "W";
										}else{
											$image_resize_type = "H";
										}
										//echo "image_resize_type:".$image_resize_type."<br>";
										//copy($allimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif");

										//exit;
										//워터마크 적용
										if(false) {
											require_once "../lib/class.upload.php";

											$s_water_handle = new Upload($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif");
											$s_water_result = WaterMarkProcess2($s_water_handle, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/");


											@copy($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/watermark/basic_".$pid.".gif",$_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif");
											@chmod($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", 0777);


											$image_type = "gif"; // 워터마크 처리후 마임타입이 gif로 바뀐다.
										}

										if($image_type == "gif"){

										//if(substr($allimg_name, -3) == "gif"){
											//copy($allimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif");

											if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif")){
												unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif");
											}

											//copy($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif");
											MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif", MIRROR_NONE);
											resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif",$image_info2[0][width],$image_info2[0][height],$image_resize_type);

											if($chk_mimg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif");
												}
												MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif", MIRROR_NONE);
												resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif",$image_info2[1][width],$image_info2[1][height],$image_resize_type);
											}

											if($chk_msimg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif");
												}
												MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif", MIRROR_NONE);
												resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif",$image_info2[2][width],$image_info2[2][height],$image_resize_type);
											}

											if($chk_simg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif");
												}
												MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif", MIRROR_NONE);
												resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif",$image_info2[3][width],$image_info2[3][height],$image_resize_type);
											}

											if($chk_cimg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif");
												}
												MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif", MIRROR_NONE);
												resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif",$image_info2[4][width],$image_info2[4][height],$image_resize_type);
											}
										}else if($image_type == "png"){

										//if(substr($allimg_name, -3) == "gif"){
											//copy($allimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif");

											if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif")){
												unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif");
											}

											//copy($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif");
											MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif", MIRROR_NONE);
											resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif",$image_info2[0][width],$image_info2[0][height],$image_resize_type);

											if($chk_mimg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif");
												}
												MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif", MIRROR_NONE);
												resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif",$image_info2[1][width],$image_info2[1][height],$image_resize_type);
											}

											if($chk_msimg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif");
												}
												MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif", MIRROR_NONE);
												resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif",$image_info2[2][width],$image_info2[2][height],$image_resize_type);
											}

											if($chk_simg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif");
												}
												MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif", MIRROR_NONE);
												resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif",$image_info2[3][width],$image_info2[3][height],$image_resize_type);
											}

											if($chk_cimg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif");
												}
												MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif", MIRROR_NONE);
												resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif",$image_info2[4][width],$image_info2[4][height],$image_resize_type);
											}

										}else{

											if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif")){
												unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif");
											}


											//copy($allimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif");
											Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif", MIRROR_NONE);
											resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif",$image_info2[0][width],$image_info2[0][height],$image_resize_type);

											if($chk_mimg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif");
												}
												Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif", MIRROR_NONE);
												resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif",$image_info2[1][width],$image_info2[1][height],$image_resize_type);
											}

											if($chk_msimg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif");
												}
												Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif", MIRROR_NONE);
												resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif",$image_info2[2][width],$image_info2[2][height],$image_resize_type);
											}

											if($chk_simg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif");
												}
												Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif", MIRROR_NONE);
												resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif",$image_info2[3][width],$image_info2[3][height],$image_resize_type);
											}

											if($chk_cimg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif");
												}
												Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif", MIRROR_NONE);
												resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif",$image_info2[4][width],$image_info2[4][height],$image_resize_type);
											}
										}
									}




									if ($bimg_size > 0){
										copy($bimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif");
									}

									if ($mimg_size > 0)
									{
										copy($mimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif");
									}

									if ($msimg_size > 0)
									{
										copy($msimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif");
									}

									if ($simg_size > 0)
									{
										copy($simg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif");
									}

									if ($cimg_size > 0)
									{
										copy($cimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif");
									}

									if($chk_deepzoom == 1){
										if($pid){
											//echo "test2";
											rmdirr($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/deepzoom/".$pid);
											///exit;
										}
										$client = new SoapClient("http://".$_SERVER["HTTP_HOST"]."/VESAPI/VESAPIWS.asmx?wsdl=0");
										//print_r($client);
										$params = new stdClass();
										$params->inputPhysicalPathString = $basic_img_src;
										$params->outputPhysicalPathString = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/deepzoom/".$pid;



										$response = $client->TilingWithPhysicalPath($params);
									}
									//echo "goods_desc_copy : ".$goods_desc_copy."<br><br>";

									//print_r($addimageurls);
								if(is_array($addimageurls)){
									copyAddImages($pid, $addimageurls);
								}
									
							}

							if($goods_desc_copy){
											$data_text_convert = $basicinfo;
											$data_text_convert = str_replace("\\","",$data_text_convert);
											preg_match_all("|<IMG .*src=\"(.*)\".*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);

											$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/";

											$INSERT_PRODUCT_ID = $pid ;
											//echo $path;

											//if(count($out)>2){
											if(substr_count($data_text_convert,"<IMG") > 0){
												if(!is_dir($path)){

													mkdir($path, 0777);
													chmod($path,0777);
												}else{
													//chmod($path,0777);
												}
											}
											
											if(count($out[1]) < 1){
												preg_match_all("|<img.*src=\"(.*)\".*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);
												if(substr_count($data_text_convert,"<img") > 0){
													if(!is_dir($path)){

														mkdir($path, 0777);
														chmod($path,0777);
													}else{
														//chmod($path,0777);
													}
												}
											}
//echo $data_text_convert;
//print_r($out);

											for($i=0;$i < count($out);$i++){
												for($j=0;$j < count($out[$i]);$j++){

													$img = returnImagePath($out[$i][$j]);
													$img = ClearText($img);


													try{
														if($img){
															if(substr_count($img,$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/") == 0){// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
																if(substr_count($img,"$HTTP_HOST")>0){
																	$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"],$img);

																	@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".returnFileName($img));
																	if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
																		unlink($local_img_path);
																	}

																	$basicinfo = str_replace($img,$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".returnFileName($img),$basicinfo);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
																}else{
																 
																	if(substr_count($img,$_SERVER["DOCUMENT_ROOT"])){
																		//$img = $_SERVER["DOCUMENT_ROOT"].$img;
																		if(@copy($_SERVER["DOCUMENT_ROOT"].$img,$_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".returnFileName($_SERVER["DOCUMENT_ROOT"].$img))){
																			$basicinfo = str_replace($img,$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".returnFileName($img),$basicinfo);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
																		}
																	}else{
																		if(@copy($img,$_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".returnFileName($img))){
																			$basicinfo = str_replace($img,$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".returnFileName($img),$basicinfo);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
																		}
																	}

																}
															}
														}

													}catch(Exception $e){
														// 에러처리 구문
														//exit($e->getMessage());
													}


												}
											}
											$basicinfo = str_replace("http://$HTTP_HOST","",$basicinfo);

											if($basicinfo != ""){
												$basicinfo_str = ", basicinfo='$basicinfo' ";
												//echo "basicinfo : ".$basicinfo."<br><br>";
											}


									}else{
										if($basicinfo != ""){
											$basicinfo_str = ", basicinfo='$basicinfo' ";
											//echo "basicinfo : ".$basicinfo."<br><br>";
										}
									}

									if($bimg_text != ""){
										$bimg_str = ", bimg='$bimg_text' ";
									}


							if($currency_ix != "" && $currency_ix != "0"){
								$currency_ix_str = ", currency_ix='$currency_ix' ";
							}

				
							$brand_name = str_replace("'","&#39;",$brand_name);
							if($brand_name){
								$sql = "select * from shop_brand 
											where (
												brand_name LIKE '%".$brand_name."%' 
												or brand_name LIKE '%".strtolower($brand_name)."%'  
												or brand_name LIKE '%".strtoupper($brand_name)."%'  
												or brand_name LIKE '%".str_replace(" ","",$brand_name)."%'  
												or brand_name LIKE '%".str_replace(" ","",strtolower($brand_name))."%'  
												or brand_name LIKE '%".str_replace(" ","",strtoupper($brand_name))."%' 
											) ";

								$db->query ($sql);
								if($db->total){
									$db->fetch();
									$brand_name = $db->dt[brand_name];
									$b_ix = $db->dt[b_ix];
								}else{
									$sql = "INSERT INTO shop_brand 
												(b_ix, cid, bd_ix,brand_code, brand_name,brand_name_division, global_binfo, disp, search_disp, top_design, company_id,shotinfo,apply_status, brand_html,vieworder, regdate) 
												values
												('', '$cid', '$bd_ix', '$brand_code', '".$brand_name."','$brand_name_division', '$global_binfo', '$disp', '$search_disp','$top_design','".$_SESSION["admininfo"]["company_id"]."','$shotinfo','$apply_status','$brand_html','$vieworder',now()) ";//$bd_ix 추가 kbk 13/07/01
	 
									$db->query($sql);
									$db->query("SELECT b_ix , brand_name FROM shop_brand WHERE b_ix=LAST_INSERT_ID()");
									$db->fetch();
									$b_ix = $db->dt[b_ix];
									$brand_name = $db->dt[brand_name];
								}
							}

							if(count($category_add_infomations) > 0){
								unset($_category_add_infomations);
								foreach($category_add_infomations as $colum => $li){
									foreach($li as $ln => $val){
										if(is_array($val)){
											foreach($val as $key => $value){
												//echo $colum.":::".$ln."<br>";
												//$_value = str_replace("'","&#39;",trim($value));
												$_category_add_infomations[$colum][$ln][] = urlencode($value);
											}
										}else{
											//$_val = str_replace("'","&#39;",trim($val));
											$_category_add_infomations[$colum][$ln] = urlencode($val);
										}
									}
								}
						//
						//exit;
								$category_add_infomations_json = json_encode($_category_add_infomations);
							}
//echo $category_add_infomations_json;
                            // print_r($_category_add_infomations);
							$shotinfo = str_replace("'","&#39;",$shotinfo);
							$sql = "UPDATE ".TBL_SHOP_PRODUCT." SET
											pcode='$pcode', 
											pname='$pname', 
											search_keyword='".$search_keyword."', 
											shotinfo='".$shotinfo."', 
											product_weight='".$product_weight."', 
											product_width='".$product_width."', 
											product_height='".$product_height."', 
											product_depth='".$product_depth."', 
											brand_name='".$brand_name."', 
											category_add_infos='".$category_add_infomations_json."', 											 
											brand='".$b_ix."', 
											state = '1',
											bs_goods_url = '".$bs_goods_url."',
											buyingservice_coprice='$buyingservice_coprice',
											listprice='$listprice',sellprice='$sellprice', 
											coprice='$coprice', 
											editdate = NOW() 
											$currency_ix_str 
											$basicinfo_str 	
											$bimg_str
											Where id = '$pid' "; // basicinfo='$basicinfo', shotinfo='$shotinfo',
							//echo nl2br($sql);
							$db->query ($sql);

							$db->query("select orgin_price, exchange_rate, air_wt, air_shipping , duty, clearance_fee, bs_fee_rate, bs_fee, clearance_type from shop_product_buyingservice_priceinfo where bs_use_yn = '1' and pid ='".$pid."'");
							$db->fetch();
							$b_orgin_price = $db->dt[orgin_price];
							$b_exchange_rate = $db->dt[exchange_rate];
							$b_air_wt = $db->dt[air_wt];
							$b_air_shipping = $db->dt[air_shipping];
							$b_duty = $db->dt[duty];
							$b_clearance_fee = $db->dt[clearance_fee];
							$b_bs_fee_rate = $db->dt[bs_fee_rate];
							$b_bs_fee = $db->dt[bs_fee];
							$clearance_type = $db->dt[clearance_type];


							//if($orgin_price != $b_orgin_price){
							if($orgin_price != $b_orgin_price || $buying_service_currencyinfo[exchange_rate] != $b_exchange_rate || $air_wt != $b_air_wt || $air_shipping != $b_air_shipping || $duty != $b_duty || $buying_service_currencyinfo[clearance_fee] != $b_clearance_fee || $bs_fee_rate != $b_bs_fee_rate || $bs_fee != $b_bs_fee){
									// orgin 원가
								$db->query("update shop_product_buyingservice_priceinfo set bs_use_yn = '0' where pid ='".$pid."'");

								$sql = "insert into shop_product_buyingservice_priceinfo
											(bsp_ix,pid,orgin_price,exchange_rate,air_wt,air_shipping,duty,clearance_fee,clearance_type, bs_fee_rate,bs_fee,bs_use_yn, regdate)
											values('$bsp_ix','$pid','$orgin_price','".$buying_service_currencyinfo[exchange_rate]."','$air_wt','$air_shipping','$duty','".$buying_service_currencyinfo[clearance_fee]."','$clearance_type','$bs_fee_rate','$bs_fee','1',NOW()) ";
								//echo $sql;
								$db->query($sql);
							}



							if(is_array($partner_prd_reg) && count($partner_prd_reg) > 0){
								foreach($partner_prd_reg as $site_code){

									$sql = "select site_code, si.si_ix , site_div, case when ssi.manage_exchange_rate = 1 then si.exchange_rate else ssi.exchange_rate end as exchange_rate 
											from sellertool_site_info si 
											left join sellertool_site_seller_info ssi on si.si_ix = ssi.si_ix 					
											where si.site_code = '".$site_code."'   ";
											//left join sellertool_basicset sb on ssi.sb_ix = sb.sb_ix and sb.company_id = '".$admin."'

									$db->query($sql);
									$db->fetch();
									$site_code = $db->dt[site_code];
									$si_ix = $db->dt[si_ix];
									$exchange_rate = $db->dt[exchange_rate];

									if($db->dt[site_div] == 2){// 해외 제휴처 일때 
										$agent_price = number_format($dollar_price*$exchange_rate,2);
									}else{
										$agent_price = $sellprice;
									}

									$sellertool_pname = $pname;
									/*
									if($db->dt[is_before_pname]){
										$sellertool_pname = $db->dt[before_pname].$sellertool_pname;
									}
									if($db->dt[is_after_pname]){
										$sellertool_pname = $sellertool_pname.$db->dt[after_pname];
									}
									*/

									$sql = "insert into sellertool_market_product_info 
												(agent_id, pid,site_code,si_ix, pname, sellprice, usd_price, agent_price,exchange_rate, state) 
												values 
												('".$_SESSION["admininfo"]["company_id"]."','".$pid."','".$site_code."','".$si_ix."','".$sellertool_pname."','".$sellprice."','".$dollar_price."','".$agent_price."','".$exchange_rate."','1')";
									//echo nl2br($sql);
									$db->query($sql);
								}
							}

							//$pid = $pid;

							if($options_price_stock["option_name"]){
								$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and option_name = '".trim($options_price_stock["option_name"])."' and option_kind = 'b'");

								if($db->total){
									$db->fetch();
									$opn_ix = $db->dt[opn_ix];
									$sql = "update  ".TBL_SHOP_PRODUCT_OPTIONS." set
													option_name='".trim($options_price_stock["option_name"])."', option_kind='".$options_price_stock["option_kind"]."', option_type='".$options_price_stock["option_type"]."',
													option_use='".$options_price_stock["option_use"]."'
													where opn_ix = '".$opn_ix."' ";
									$db->query($sql);


								}else{
									$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS." (opn_ix, pid, option_name, option_kind, option_type, option_use, regdate)
													VALUES
													('','$pid','".$options_price_stock["option_name"]."','".$options_price_stock["option_kind"]."','".$options_price_stock["type"]."','".$options_price_stock["use"]."',NOW())";

									$db->query($sql);
									$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE opn_ix=LAST_INSERT_ID()");
									$db->fetch();
									$opn_ix = $db->dt[0];
								}
								//echo $sql."<br>";
								//exit;

								$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set insert_yn='N' where opn_ix='".$opn_ix."' ";
								//echo $sql."<br><br>";
								$db->query($sql);
								$option_stock_yn = "";
								for($j=0;$j < count($options_price_stock["option_div"]);$j++){
									if($options_price_stock[option_div][$j]){
										$db->query("SELECT id FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE option_div = '".trim($options_price_stock[option_div][$j])."' and opn_ix = '".$opn_ix."' ");

										if($db->total){
											$db->fetch();
											$opnd_ix = $db->dt[id];
											/*
											$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set
														option_div='".$options_price_stock[option_div][$j]."',option_price='".$options_price_stock[price][$j]."',option_m_price='".$options_price_stock[price][$j]."',option_d_price='".$options_price_stock[price][$j]."',
														option_a_price='".$options_price_stock[price][$j]."',option_useprice='".$options_price_stock[price][$j]."', option_stock='".$options_price_stock[stock][$j]."', option_safestock='".$options_price_stock[safestock][$j]."' ,
														option_etc1='".$options_price_stock[etc1][$j]."', insert_yn='Y'
														where id ='".$opnd_ix."' and opn_ix = '".$opn_ix."'";
											*/
											$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set
														option_div='".$options_price_stock[option_div][$j]."',option_price='".$options_price_stock[price][$j]."',option_stock='".$options_price_stock[stock][$j]."', option_safestock='".$options_price_stock[safestock][$j]."' ,
														option_etc1='".$options_price_stock[etc1][$j]."', insert_yn='Y'
														where id ='".$opnd_ix."' and opn_ix = '".$opn_ix."'";
										}else{
											//$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price,option_m_price, option_d_price, option_a_price, option_stock, option_safestock, option_etc1) ";
											$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price, option_stock, option_safestock, option_etc1) ";
											//$sql = $sql." values('','$pid','$opn_ix','".$options_price_stock[option_div][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[stock][$j]."','".$options_price_stock[safestock][$j]."','".$options_price_stock[etc1][$j]."') ";
											$sql = $sql." values('','$pid','$opn_ix','".$options_price_stock[option_div][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[stock][$j]."','".$options_price_stock[safestock][$j]."','".$options_price_stock[etc1][$j]."') ";
										}

										//echo $sql."<br><br>";
										$db->query($sql);

										if($options_price_stock[stock][$j] == 0 && ($option_stock_yn == "" || $option_stock_yn == "R")){
											$option_stock_yn = "N";
										}

										if($options_price_stock[stock][$j] < $options_price_stock[safestock][$j] && $option_stock_yn == ""){
											$option_stock_yn = "R";
										}
									}
								}
								$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$opn_ix."' and insert_yn = 'N' ";
								//echo $sql;
								$db->query($sql);

								if($option_stock_yn){
									$db->query("update ".TBL_SHOP_PRODUCT." set option_stock_yn = '$option_stock_yn' where id ='$pid'");
								}

							}else{

								$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and option_kind = 'b'");

								if($db->total){
									$db->fetch();
									$opn_ix = $db->dt[0];
									$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$opn_ix."'  ";
									$db->query($sql);
								}
								$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and option_kind = 'b' ";
								$db->query($sql);
							}
						//}


							$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS." set insert_yn='N' 	where pid = '$pid' and option_kind in ('s','p','r') ";
							//echo $sql."<br><br>";

							$db->query($sql);
							//$db->debug = true;
							//print_r($options);
							if(is_array($options)){
								for($i=0;$i < count($options);$i++){
									//echo $options[$i][option_name].":::".$options[$i][opn_ix]."<br>";
									//exit;
									if($options[$i]["option_name"]){
										$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and option_name = '".trim($options[$i]["option_name"])."' and option_kind in ('s','p','r') ");
										//$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and opn_ix = '".$options[$i]["opn_ix"]."' and option_kind in ('s','p') ");



										if($db->total){
											$db->fetch();
											$opn_ix = $db->dt[opn_ix];
											$sql = "update  ".TBL_SHOP_PRODUCT_OPTIONS." set
															option_name='".trim($options[$i]["option_name"])."', 
															option_kind='".$options[$i]["option_kind"]."',
															option_type='".$options[$i]["option_type"]."', 
															option_use='1',
															insert_yn='Y'
															where opn_ix = '".$opn_ix."' ";

											$db->query($sql);

										}else{
											$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS." (opn_ix, pid, option_name, option_kind, option_type, option_use, regdate)
															VALUES
															('','$pid','".$options[$i]["option_name"]."','".$options[$i]["option_kind"]."','".$options[$i]["option_type"]."','1',NOW())";
											$db->query($sql);
											$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE opn_ix=LAST_INSERT_ID()");
											$db->fetch();
											$opn_ix = $db->dt[0];
										}


										//echo $sql."<br><br>";
										//


										$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set insert_yn='N'	where opn_ix='".$opn_ix."' ";
										$db->query($sql);
										for($j=0;$j < count($options[$i]["details"]);$j++){
											if($options[$i][details][$j][option_div]){
													
													$db->query("SELECT id FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE option_div = '".trim($options[$i][details][$j][option_div])."' and opn_ix = '".$opn_ix."' ");

													if($db->total){
														$db->fetch();
														$opnd_ix = $db->dt[id];
														/*
														$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set
																option_div='".$options[$i][details][$j][option_div]."',option_price='".$options[$i][details][$j][price]."',option_m_price='".$options[$i][details][$j][price]."',option_d_price='".$options[$i][details][$j][price]."',
																option_a_price='".$options[$i][details][$j][price]."',option_useprice='".$options[$i][details][$j][price]."', option_stock='0', option_safestock='0' ,
																option_etc1='".$options[$i][details][$j][etc1]."', insert_yn='Y'
																where id ='".$opnd_ix."' and opn_ix = '".$opn_ix."'";
														*/
														$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set
																option_div='".$options[$i][details][$j][option_div]."',
																option_price='".$options[$i][details][$j][price]."', 
																option_stock='0', 
																option_safestock='0' ,
																option_soldout='".$options[$i][details][$j][soldout]."', 
																option_coprice='".$options[$i][details][$j][origin_price]."', 
																option_etc1='".$options[$i][details][$j][etc1]."', 
																insert_yn='Y'
																where id ='".$opnd_ix."' and opn_ix = '".$opn_ix."'";
														$db->query($sql);
													}else{
														//$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price,option_m_price, option_d_price, option_a_price, option_stock, option_safestock, option_etc1) ";
														$sql = "INSERT INTO 
																	".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." 
																	(id, pid, opn_ix, option_div,option_price, option_coprice, option_stock, option_safestock, option_soldout, option_etc1) ";
														//$sql = $sql." values('','$pid','".$opn_ix."','".trim($options[$i][details][$j][option_div])."','".$options[$i][details][$j][price]."','".$options[$i][details][$j][price]."','".$options[$i][details][$j][price]."','".$options[$i][details][$j][price]."','0','0','".$options[$i][details][$j][etc1]."') ";
														$sql = $sql." values
																	('','$pid','".$opn_ix."','".trim($options[$i][details][$j][option_div])."','".$options[$i][details][$j][price]."','".$options[$i][details][$j][origin_price]."','0','0','".$options[$i][details][$j][soldout]."','".$options[$i][details][$j][etc1]."') ";

														$db->query($sql);

														$db->query("SELECT id FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE id=LAST_INSERT_ID()");
														$db->fetch();
														$opnd_ix = $db->dt[0];
													}

													//echo $sql."<br><br>";
													if($options[$i]["details"][$j][thumb_images] || $options[$i]["details"][$j][goods_images]){
														$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product", $pid, 'Y');
														if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/options/")){
															mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/options/");
															chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/options/",0777);
														}

														//$options[$option_key][details][$i][thumb_images] = $thumb_images[$i];
														//$options[$option_key][details][$i][goods_images] = $goods_detail_images[$i];
														if($options[$i]["details"][$j][thumb_images]){
															copy($options[$i]["details"][$j][thumb_images], $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/options/options_detail_".$opnd_ix."_s.gif");
														}
														if($options[$i]["details"][$j][goods_images]){
															copy($options[$i]["details"][$j][goods_images], $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/options/options_detail_".$opnd_ix."_b.gif");
														}
													}


											}
										}
										$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$opn_ix."' and insert_yn = 'N' ");
									}
								}
							}
							$sql = "select opn_ix from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and insert_yn = 'N' ";
							//echo $sql."<br><br>";
							$db->query($sql);
							if($db->total){
								$del_options = $db->fetchall();
								//print_r($del_options);
								for($i=0;$i < count($del_options);$i++){
									$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$del_options[$i][opn_ix]."' and pid = '$pid' ");
								}
							}
							$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and insert_yn = 'N' ");

							
							
						}else{
							//echo "여기<----:".$act;

							//echo $basicinfo;
							//exit;
							//print_r($options);
							$goods_reg_complete_cnt++;
							$goods_desc_copy = 1;
							$exchange_rate = $buying_service_currencyinfo[exchange_rate];

							$brand_name = str_replace("'","&#39;",$brand_name);
							if($brand_name){
								
								$sql = "select * from shop_brand 
											where (
												brand_name LIKE '%".$brand_name."%' 
												or brand_name LIKE '%".strtolower($brand_name)."%'  
												or brand_name LIKE '%".strtoupper($brand_name)."%'  
												or brand_name LIKE '%".str_replace(" ","",$brand_name)."%'  
												or brand_name LIKE '%".str_replace(" ","",strtolower($brand_name))."%'  
												or brand_name LIKE '%".str_replace(" ","",strtoupper($brand_name))."%' 
											) ";

								$db->query ($sql);
								if($db->total){
									$db->fetch();
									$brand_name = $db->dt[brand_name];
									$b_ix = $db->dt[b_ix];
								}else{
									$sql = "INSERT INTO shop_brand 
												(b_ix, cid, bd_ix,brand_code, brand_name,brand_name_division, global_binfo, disp, search_disp, top_design, company_id,shotinfo,apply_status, brand_html,vieworder, regdate) 
												values
												('', '$cid', '$bd_ix', '$brand_code', '".$brand_name."','$brand_name_division', '$global_binfo', '$disp', '$search_disp','$top_design','".$_SESSION["admininfo"]["company_id"]."','$shotinfo','$apply_status','$brand_html','$vieworder',now()) ";//$bd_ix 추가 kbk 13/07/01
	 
									$db->query($sql);
									$db->query("SELECT b_ix  FROM shop_brand WHERE b_ix=LAST_INSERT_ID()");
									$db->fetch();
									$b_ix = $db->dt[b_ix];
									$brand_name = $brand_name;
								}
							}

							include "goods_input.act.php";
							syslog(LOG_INFO,  "<b style='color:red;'>$pname 상품을 등록중입니다.</b><---<br> <b>pcode : $pcode pid : $pid </b><br>\r");
							echo " <b style='color:red;'>$pname 상품을 등록중입니다.</b> <b>pcode : $pcode pid : $pid </b><br>";
						}

				}else{ 
					if(!$stock_bool){
					    /**
                         * disp 반영하도록 수정 12.05.24 bgh
                         */
						if(!$stock_bool){
							$sc_disp = 0;
							$sc_state = 0;
						}else{
							$sc_disp = 1;
							$sc_state = 1;
						}
                        if($sc_disp == "" || $sc_disp == NULL){
                            $sc_disp = 0;
                        }
						$sql = "update shop_product set state = '0', disp = '".$sc_disp."', editdate = NOW() where id = '".$pid."'  ";
						//echo $sql;
						$db->query ($sql);

						$goods_update_soldout_cnt++;
						syslog(LOG_INFO, "품절/판매불가 :  pname : $pname price : $price  <b>pcode : $pcode </b>  \r");
						syslog(LOG_INFO, "품절/판매불가 스크래핑 URL :  ".$bs_url."  \r");
						//syslog(LOG_INFO, "품절/판매불가 스크래핑 결과 :  ".$results."  \r");
						echo "품절/판매불가 :  pname : $pname price : $price  <b>pcode : $pcode </b>  ";
					}else{
						$goods_update_soldout_cnt++;
						syslog(LOG_INFO, "정보부족 : <b s>pname: $pname</b> <b>price : $price</b>  <b>pcode : $pcode </b>\r");
						//syslog(LOG_INFO, "품절/판매불가 스크래핑 URL :  ".$bs_url."  \r");
						$sql = "update shop_product set editdate = NOW() where id = '".$pid."'  ";
						$db->query ($sql);

						echo "정보부족 : <b s>pname: $pname</b> <b>price : $price</b>  <b>pcode : $pcode </b>";
						$write = "[정보부족, 품절]".$bs_site." ".$bs_url." 상품코드:".$pcode.", 상품가격:".$price." \n\n";
						$path = $_SERVER["DOCUMENT_ROOT"]."/_logs/";
						if($sc_state == 9){ // 삭제
							if($pid){
									$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product", $pid, 'Y');

									if ($uploaddir && file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/")){
										rmdirr($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/");
									}

									$db->query("DELETE FROM ".TBL_SHOP_PRODUCT." WHERE id='$pid'");
									$db->query("DELETE FROM ".TBL_SHOP_PRICEINFO." WHERE pid='$pid'");
									$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE pid='$pid'");
									$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_RELATION." WHERE pid='$pid'");
									$db->query("DELETE FROM ".TBL_SHOP_RELATION_PRODUCT." WHERE pid = '$pid'");
									$db->query("DELETE FROM ".TBL_SHOP_PRODUCT."_auction WHERE pid = '$pid'");
									$db->query("DELETE FROM ".TBL_SHOP_CART." WHERE id='$pid'");
									//$db->query("DELETE FROM ".TBL_SHOP_ORDER." WHERE pid='$pid'");

									$adduploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg", $pid, 'Y');
									if ($pid && is_dir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/")){
										rmdirr($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/");
									}

									$db->query("SELECT id FROM ".TBL_SHOP_ADDIMAGE." WHERE  pid = '$pid' ");
									for($i=0;$i < $db->total;$i++){
										$db->fetch($i);
										$ad_ix = $db->dt[id];
										//$_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif"

										if($pid && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/deepzoom/$ad_ix")){
											rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/deepzoom/$ad_ix");
										}
									}

									$db->query("DELETE FROM ".TBL_SHOP_ADDIMAGE." WHERE  pid = '$pid'");


									if($pid && is_dir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/")){
										rmdirr($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/");
									}
							}
						}else{// 일시 품절 처리
							if($pname == "" && $price == ""){
								$sql = "update shop_product set state = '0', editdate = NOW() where id = '".$pid."'  ";
							}else{
								$sql = "update shop_product set state = '1', editdate = NOW() where id = '".$pid."'  ";
							}
							$db->query ($sql);
						}
						if(!is_dir($path)){
							mkdir($path, 0777);
							chmod($path,0777);
						}else{
							//chmod($path,0777);
						}


						$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/".$admininfo[mall_data_root]."/_logs/buyingservice_".date("Ymd").".txt","a+");
						fwrite($fp,$write);
						fclose($fp);
					}
				}

				unset($pname);
				unset($pcode);
				unset($prod_img_src);
				unset($price);
				unset($prod_desc_start_line);
				unset($prod_desc_end_line);
				unset($prod_desc_inner_div_cnt);
				unset($option_start_line);
				unset($option_end_line);
				unset($prod_desc_prod);
				//unset($pcode);

			}else{
				echo "해당 상품이 이미 등록되어 있습니다. <-- <b>pcode : $pcode   ::::::   pid : $pid  </b><br>";
				$goods_alreadyreg_cnt++;

				if(true){
					if($bs_url != ""){
						/*
						$sql = "UPDATE ".TBL_SHOP_PRODUCT." SET
									state = '1',disp = '1', bs_goods_url = '".$bs_url."'
									Where id = '".$pid."' ";
						//echo $sql;
						$db->query ($sql);
						*/
						if(!$stock_bool && $soldout_message != ""){
                            $sc_disp = 0;
							$sc_state = 0;
                        }else{
							$sc_disp = 1;
							$sc_state = 1;
						}

						/**
						*    2013.03.07 신훈식 
						*    자동업데이트의 경우 품절처리 되어 있던 상품은 다시 판매중으로 변경되지 않게 처리
						*/
						if($bs_cron_act == "new_goods_reg" || $bs_cron_act == "goods_update"){
							$sql = "UPDATE ".TBL_SHOP_PRODUCT." SET  
										state = '".$sc_state."',disp = '".$sc_disp."', bs_goods_url = '".$bs_url."'										
										Where id = '".$pid."' and state != '0' "; 

						}else{
							$sql = "UPDATE ".TBL_SHOP_PRODUCT." SET  
										state = '".$sc_state."',disp = '".$sc_disp."', bs_goods_url = '".$bs_url."'										
										Where id = '".$pid."' "; 
						}
						//echo $sql;
						$db->query ($sql);
					}

					$cid2 = trim($cid2);
					$sql = "select basic from ".TBL_SHOP_PRODUCT_RELATION." where pid = '".$pid."' and basic='1' ";
					//echo $sql."<br><br>";
					$db->query($sql);
					if($db->total){
						$db->fetch();

						if($db->dt[basic] == "1"){
							$category_basic = "0";
						}else{
							$category_basic = "1";
						}
					}else{
						$category_basic = "1";
					}

					echo $new_goods_reg_list_url."<br><br>";
					$sql = "select rid from ".TBL_SHOP_PRODUCT_RELATION." where pid = '".$pid."' and cid = '".$cid2."' ";
					//echo $sql."<br><br>";
					syslog(LOG_INFO, "카테고리 유무 :  ".$sql."  \r");
					$db->query($sql);

					if(!$db->total){


						$sql = "insert into ".TBL_SHOP_PRODUCT_RELATION."
									(rid, cid, pid, disp, basic,insert_yn, regdate )
									values
									('','".$cid2."','".$pid."','1','".$category_basic."','Y',NOW())";
						//echo nl2br($sql);
						$db->query($sql);
						//syslog(LOG_INFO, "상품리스트 :[new_goods_reg_list_url:".$new_goods_reg_list_url."] \r");
						syslog(LOG_INFO, "카테고리추가매핑 :[bs_act:".$bs_act."] [pname:".$pname."]  [pcode:".$pcode."][pid:".$pid."] ".$sql."  \r\n\n\n\n");
						$sql = "update ".TBL_SHOP_PRODUCT." set reg_category = 'Y' , state = '1', disp = '1', editdate = NOW() where id = '".$pid."' ";
						//echo $sql."<br><br>";
						$db->query($sql);
					}
				}

				//unset($cid2);
				unset($pid);
				unset($pcode);



			}
			set_time_limit(30);
			//$snoopy->fetch($goods_detail_links[$i]);


}


closelog();

?>
