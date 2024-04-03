<?
include_once("../class/layout.class");
include_once "../class/Snoopy.class.php";
include_once("buyingService.lib.php");

define_syslog_variables();
openlog("phplog", LOG_PID , LOG_LOCAL1);



//print_r($_POST);
//exit;
//session_start();

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
		$GoodsListPageName = "shop/_/";
		$GoodsDetailPageName = "ProductDetail.jsp";
		$Product_ID_Name = "PRODUCT<>prd_id";
		$PageParamName = "Nao";
		$bs_site_domain = "http://www.saksfifthavenue.com";
		$page_size = 60;
		$start_page_num = 0;
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
		$search_list_regxp = "|<a class=\"styleOutfitCollectionImage\" href=[\",'](.*)[\",']>|U";
		$search_detail_regxp = "|<div class=\"styleOutfitCollectionItemDiv1\">\r\n.*href=[\",'](.*)[\",']>|U";
		$GoodsListPageName = "/C/";
		$GoodsDetailPageName = "/S/";
		$Product_ID_Name = "ID";
		$PageParamName = "P";
		$bs_site_domain = "http://shop.nordstrom.com";
		$page_size = 1;
		$start_page_num = 0;
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
	}else if($bs_site == "gap"){
		$search_list_regxp = "|href=[\",']?(.*)[\",']>|U";
		$search_detail_regxp = "|<a.*href=[\",'](.*)[\",'].*class=\"productItemName\">|U";
		$search_list_max_regxp = "|<span class=\"page_selected\">(.*)</span>|U";
		$GoodsListPageName = "pageID";
		$GoodsDetailPageName = "/browse/product.do";
		$Product_ID_Name = "scid";
		$PageParamName = "pageID";
		$bs_site_domain = "http://www.gap.com";
		$page_size = 1;
		$start_page_num = 1;
		$scrapping_type = "curl";
		$currency_type = "USD";
		//$category_navi_regxp = "|<a.*class=\"subCategory\">(.*)</a>|U";
		$category_navi_regxp = "|<title>(.*)</title>|U";
	}else if($bs_site == "oldnavy"){
		$search_list_regxp = "|href=[\",']?(.*)[\",']>|U";
		$search_detail_regxp = "|<a.*href=[\",'](.*)[\",'].*class=\"productItemName\">|U";
		$search_list_max_regxp = "|<span class=\"page_selected\">(.*)</span>|U";
		$GoodsListPageName = "pageID";
		$GoodsDetailPageName = "/browse/product.do";
		$Product_ID_Name = "scid";
		$PageParamName = "pageID";
		$bs_site_domain = "http://oldnavy.gap.com";
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
		$search_detail_regxp = "|<div class=\"image-wrap\">.*\n.*<a href=[\",'](.*)[\",']>|U";
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
		$search_detail_regxp = "|<div class=\"thumbName\"><a href=\"(.*)&simg.*\".*>|U";
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
		$GoodsListPageName = ".jsp";
		$GoodsDetailPageName = "product.jsp";
		$Product_ID_Name = "productId";
		$PageParamName = "cad";// 아무거나 지정해도 됨 필요 없음
		$bs_site_domain = "http://www.ae.com";
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
		$search_detail_regxp = "|<div class=\"image-wrap\">.*\n.*<a href=[\",'](.*)[\",']>|U";
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
	}else if($bs_site == "luxgirl"){
		$search_list_regxp = "|<span id='mk_pager'><a href='(.*)'><font class='brandpage'>|U";
		$search_detail_regxp = "|<td align=center class=\"Brand_prodtHeight\">.*\n.*<a href=\"(.*)\"|U";
		$search_list_max_regxp = "|<font class='brandpage'>\[(.*)\]</font>|U";
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
		//****************홍진영 end******************
    /**
     * 배광호
     *
     * zappos 12.4.13
     *
     * 옵션별 가격 원화로 변경하는 부분 필요.
     *
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
		$start_page_num = 0;
		$scrapping_type = "snoopy";
		$currency_type = "USD";
		$category_navi_regxp = "|<title>(.*)</title>|U";
    /**
     * disney 12.4.13 완료
     *
     * 내부주소 사용
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
		$scrapping_type = "snoopy";
		$currency_type = "USD";
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

		if($bs_site == "gap" || $bs_site == "oldnavy"){
			$ch = curl_init();
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


			$aryURL = explode("?",$list_url);
			parse_str($aryURL[1], $url_info);

			$list_url_tmp = $bs_site_domain."/browse/categoryProductGrid.do?cid=".$url_info[cid]."&actFltr=false&sortBy=0&pageID=-1&globalShippingCountryCode=us";
			//echo $list_url_tmp."<br>";

			curl_setopt ($ch, CURLOPT_URL,$list_url_tmp);   // 로그인후 이동할 페이지 입니다.
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);

			$results = curl_exec ($ch);
			//echo "category_results : ".$category_results;
			//exit;
			curl_close ($ch);
		}else if($bs_site == "michaelkors"){
		//echo $bs_url;
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL,"http://www.michaelkors.com/");                      // 접속할 URL 주소
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
		if($bs_site == "gap" || $bs_site == "oldnavy"){
			preg_match_all("|<div class=\"categoryPaging\">(.*)</div>|U",$results,$links_html, PREG_PATTERN_ORDER);
			//print_r($links_html);
			if($links_html[1][0] == ""){
					$min_value = 1;
					$max_value = 1;
			}
		}

		preg_match_all($search_list_regxp,$results,$links, PREG_PATTERN_ORDER);

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
		}

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

		if($bs_site == "luxgirl"){
			$min_value = 1;
			$max_value = 1;
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
		preg_match_all($search_list_regxp,$results,$links, PREG_PATTERN_ORDER);

		if($bs_site == "janieandjack"){
			$links[1][0]=str_replace("pageClicked=0","pageClicked=1",$links[1][0]);

		}

		if($search_list_max_regxp){
			preg_match_all($search_list_max_regxp,$results,$check_max_value, PREG_PATTERN_ORDER);
			//print_r($check_max_value);
			//exit;
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
				$min_value = trim($links[1][0]);
				$max_value = trim($check_max_value[1][0]) - 1;
			}
            //echo $min_value."~".$max_value;
		}
        if($bs_site == "disneystore"){
            $min_value = 1;
            preg_match_all("|\", 96, \".*\",(.*), \"|U",$results,$max_value_tmp, PREG_PATTERN_ORDER);
            if($max_value_tmp[1][0] > 96){
                $max_value = 1 + floor($max_value_tmp[1][0] / 96);
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
			}

		}
	}


	//echo ($max_value+":::"+$max_value);
	//exit;
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

		if($bs_site == "luxgirl"){
			$orgin_category_info= iconv("CP949","UTF-8",$orgin_category_info);
			//print_r($orgin_category_info);
		}
	}




	if($bs_favorite){
		$db = new Database;
		$sql = "select * from shop_buyingservice_url_info where bs_site = '".$bs_site."' and  bs_list_url_md5='".md5(trim($list_url))."' ";
		//echo $sql;
		$db->query($sql);

		if(!$db->total){
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
				$orgin_category_info = str_replace("\t"," ",trim($orgin_category_info));
				$orgin_category_info = str_replace("'","\'",trim($orgin_category_info));

				$sql = "update shop_buyingservice_url_info set
							orgin_category_info = '".str_replace("\t"," ",trim($orgin_category_info))."'
							where bs_site = '".$bs_site."' and  bs_list_url_md5='".md5(trim($list_url))."' ";
				//echo $sql;
				$db->query($sql);
			}

		}


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
		setTimeout(\"alert(language_data['product_bsgoods2.act.php']['A'][language])\",500);";
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

if($bs_act == "get_goods"){
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
	if(!substr_count($list_url,$bs_site_domain)){
		$list_url = $bs_site_domain.$list_url;
	}
	if(!substr_count($list_url,$PageParamName)){
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
    /**
     * zappos 페이지네이션 쿼리가 한글자이어서 일단 따로 분리.
     */
    if($bs_site =="zappos" && !substr_count($list_url,"p=")){
        $list_url_info = preg_split("/[?]+/",$list_url);
		//echo count($list_url_info);

        if(count($list_url_info) <= 1){
			$list_url = $list_url."?$PageParamName=".$start_page_num."&partial=true&redirect=false";
		}else{
			$list_url = $list_url."&$PageParamName=".$start_page_num."&partial=true&redirect=false";
		}
    }
    /**
     * disneystore 상품 데이터주소 찾기
     */
    if($bs_site =="disneystore"){
        $snoopy = new Snoopy;
        $snoopy->fetch($list_url);
        $disney_results =  $snoopy->results;
        preg_match_all("|\"objItemsListing\", true, 1, \"(.*)\",|U",$disney_results,$url_tmp,PREG_PATTERN_ORDER);
        //print_r($url_tmp);
        if($this_pagenum > 1){
            $num = ($this_pagenum-1)*96;
            $list_url = $bs_site_domain.$url_tmp[1][0]."&Nao=".$num;
        }else
            $list_url = $bs_site_domain.$url_tmp[1][0];

    }
    //페이지넘버 replace를 else 조건으로 수정.
    else{
    	$list_url_info = preg_split("/[?]+/",$list_url);
    	parse_str($list_url_info[1]);
    	eval ("\$eval_value = \$".$PageParamName.";");
    	if($this_pagenum == ""){$this_pagenum=1;}
        $search_str =  "$PageParamName=".$eval_value;
        $replace_str = "$PageParamName=".$this_pagenum*$page_size;
        $list_url = str_replace($search_str,$replace_str,$list_url);
	}
	//echo "list_url :".$list_url;
	if($scrapping_type == "curl"){
		$cookie_nm = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cookies/cookie_".$bs_site.".txt";
		//echo $cookie_nm;
		//실제 로그인이 이루어지는 Curl 입니다.
		//echo $bs_site_domain;

		//echo $results;

		if($bs_site == "gap" || $bs_site == "oldnavy"){
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
			$category_results = $results;
			//print_r($_POST);
			//echo $list_url;
			$aryURL = explode("?",$list_url);
			parse_str($aryURL[1], $url_info);

			$list_url_tmp = $bs_site_domain."/browse/categoryProductGrid.do?cid=".trim($url_info[cid])."&actFltr=false&sortBy=0&pageID=-1&globalShippingCountryCode=us";
			//echo $list_url_tmp."<br>";

			curl_setopt ($ch, CURLOPT_URL,$list_url_tmp);   // 로그인후 이동할 페이지 입니다.
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_nm);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_nm);
			$results = curl_exec ($ch);
			curl_close ($ch);
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
			$category_results = $results;
			curl_close ($ch);
		}
		//echo $results;
		//print_r($result);$bs_site_domain
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
		preg_match_all($search_detail_regxp,$results,$goods_list_a_links, PREG_PATTERN_ORDER);
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
		$snoopy->fetch($list_url);
		$results = $snoopy->results;
		$category_results = $results;
	//	echo $snoopy->results;
	//	exit;
	//href=[\",'](.*)[\",']>|

		preg_match_all($search_detail_regxp,$snoopy->results,$goods_list_a_links, PREG_PATTERN_ORDER);
		//preg_match_all($search_list_regxp,$snoopy->results,$goods_list_a_links, PREG_PATTERN_ORDER);
	}



	//print_r($snoopy->results);
	//exit;

	for($x=0,$j=0;$x < count($goods_list_a_links[1]);$x++){
		if(substr_count($goods_list_a_links[1][$x],$GoodsDetailPageName)){
			if(substr_count($goods_list_a_links[1][$x],"http://")){
				$goods_detail_links[$j] = str_replace("\"","",$goods_list_a_links[1][$x]);
			}else{
				if($bs_site == "abercrombie"||$bs_site == "hollisterco"){
					$goods_detail_links[$j] = $bs_site_domain."/webapp/wcs/stores/servlet/".str_replace("\"","",$goods_list_a_links[1][$x]);
				}else{
					$goods_detail_links[$j] = $bs_site_domain.str_replace("\"","",$goods_list_a_links[1][$x]);
				}

				if($bs_site == "luxgirl"){
					$goods_detail_links[$j] = $bs_site_domain.str_replace("\"","",$goods_list_a_links[1][$x]);
				}
			}
			$j++;
		}
	}



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
	//alert(bs_i);
	var reg_goods_view = '".$reg_goods_view."';
	var this_page_order = '".$this_page_order."';
	var this_pagenum = '".$this_pagenum."';
	if(goods_detail_link.length > 0 && this_pagenum > 0){
		buyingservice_goods_reg();
	}else{
		parent.unloading();
	}

	function buyingservice_goods_reg(){
		//alert(bs_i+'::'+goods_detail_link[bs_i]);
		parent.document.search_form.this_url.value = goods_detail_link[bs_i];//

		$.ajax({
				type: 'POST',
				data:
					{'bs_act': 'bsgoods_one_reg','cid2': '".$cid2."','disp': '".$disp."','depth': '".$depth."','bs_site':'".$bs_site."','clearance_type':'".$clearance_type."','bs_fee_rate':'".$bs_fee_rate."','bs_air_wt':'".$bs_air_wt."','currency_ix':'".$currency_ix."','usable_round':'".$usable_round."','round_precision':'".$round_precision."','round_type':'".$round_type."','goods_detail_link': goods_detail_link[bs_i]},
				url: 'product_bsgoods2.act.php',
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
								parent.document.frames['act'].location.href='product_bsgoods.php?bsmode=reg&view=innerview&cid2=".$cid2."&depth=".$depth."';
							}

							if(this_page_order == 1){

									if(bs_i > 0){
										if(parent.document.search_form.search_status[0].checked){
											bs_i--;
											setTimeout(\"buyingservice_goods_reg()\",900);
										}else{
											if(reg_goods_view == 'N'){
												parent.document.frames['act'].location.href='product_bsgoods.php?bsmode=reg&view=innerview&cid2=".$cid2."&depth=".$depth."';
											}
											parent.unloading();
										}
									}else{
										parent.document.search_form.cid2.value ='$cid2';
										parent.document.search_form.depth.value ='$depth';
										getBuyingServiceInfoNextPage();
									}
							}else{

									if(goods_detail_link.length > bs_i){
										if(parent.document.search_form.search_status[0].checked){

											bs_i++;
											setTimeout(\"buyingservice_goods_reg()\",900);
										}else{
											if(reg_goods_view == 'N'){
												parent.document.frames['act'].location.href='product_bsgoods.php?bsmode=reg&view=innerview&cid2=".$cid2."&depth=".$depth."';
											}
											parent.unloading();
										}
									}else{
										parent.document.search_form.cid2.value ='$cid2';
										parent.document.search_form.depth.value ='$depth';
										getBuyingServiceInfoNextPage();
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
			if(checkd_page){
				if(parent.document.search_form.this_pagenum.value <= 0){
					parent.document.search_form.this_pagenum.value = 0;
				}else{
					//alert(this_page_order);
					if(this_page_order == 1){
							parent.document.search_form.this_pagenum.value = parseInt(parent.document.search_form.this_pagenum.value)-1;
					}else{
							parent.document.search_form.this_pagenum.value = parseInt(parent.document.search_form.this_pagenum.value)+1;
					}

					parent.document.search_form.this_url.value = '".$next_list_url."';
				}
				parent.document.frames['act'].location.href='product_bsgoods.php?bsmode=reg&view=innerview';
				parent.checkSearchFrom(parent.document.search_form,'get_goods');
			}
		}
	}
	</script>";

	if(substr_count($_SERVER["REQUEST_URI"],"product_bsgoods2.act.php") > 0){
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
				$__bs_url = split("[/]",$bs_url);
				$pcode = $__bs_url[4];
			}else if($bs_site == "jcrew"){
				$__bs_url = split("[/]",$bs_url);
				$_pcode = split("~",$__bs_url[6]);
				$pcode = $_pcode[1];
			}else if($bs_site == "bodenusa"){
				$__bs_url = split("[/]",$bs_url);
				$_pcode = split("~",$__bs_url[6]);
				$pcode = $__bs_url[count($__bs_url)-2];
			}else if($bs_site == "gap" || $bs_site == "oldnavy"){
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
				$pcode = $__bs_url[4];
            }else if($bs_site == "zappos"){
                $__bs_url = split("[/]",$bs_url);
				$pcode = $__bs_url[4];
            }else if($bs_site == "janieandjack"){
                preg_match_all("|prd_id=(.*)&|U",$bs_url,$__bs_url, PREG_PATTERN_ORDER);
                $pcode = $__bs_url[1][0];
            }else if($bs_site == "hannaandersson"){
                $__bs_url = split("[?]",urldecode($bs_url));
				$_bs_url = split("[=]",$__bs_url[1]);
                $pcode = str_replace("|","",$_bs_url[1]);
			}else if($bs_site == "coach"){
                $__bs_url = split("[#]",$bs_url);
				$pcode = $__bs_url[1];
            }else{
				$__bs_url = split("[?]",$bs_url);
				parse_str($__bs_url[1], $paraminfos);
				$pcode = $paraminfos[$Product_ID_Name];
			}
			//print_r($_POST);
			//echo "brfore pcode : ".$pcode."<br>";
			if(trim($pcode) == ""){
				$pcode = $_POST["pcode"];
			}else{
				$pcode = $bs_site."_".$pcode;
			}
			//echo "after pcode : ".$pcode."<br>";
			//echo "<br><b style='color:red;'>pid : ".$pid."</b><br>";

			//echo "bs_url:".$bs_url;
			//echo "currency_ix:".$_POST[currency_ix];
			//exit;

			if($pid){
			$sql = "select p.id, p.pcode, pbp.clearance_type, pbp.bs_fee_rate, pbp.air_wt , p.round_type, p.round_precision, p.currency_ix
						from shop_product p, shop_product_buyingservice_priceinfo pbp
						where p.id = pbp.pid and pbp.bs_use_yn = '1' and p.id = '".trim($pid)."'  ";
			}else{
			$sql = "select p.id, p.pcode, pbp.clearance_type, pbp.bs_fee_rate, pbp.air_wt , p.round_type, p.round_precision, p.currency_ix
						from shop_product p, shop_product_buyingservice_priceinfo pbp
						where p.id = pbp.pid and pbp.bs_use_yn = '1' and p.pcode = '".trim($pcode)."'  ";
			}
			//echo $sql;
			//exit;
			//echo $sql;//orgin_price, exchange_rate, air_wt, air_shipping , duty, clearance_fee, bs_fee_rate, bs_fee, clearance_type
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


				if($pcode != $db->dt[pcode]){
					$sql = "update shop_product set pcode = '".$pcode."' where id = '".$pid."' ";
					//echo $sql;
					$db->query ($sql);
				}
			}else{
				if($pid){
					$sql = "update shop_product set pcode = '".$pcode."' where id = '".$pid."' ";
					//echo $sql;
					$db->query ($sql);

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
			//echo $sql;
			$db2->query ($sql);

			if($db2->total){
				$db2->fetch();
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
				$bs_fee_rate = $db2->dt[bs_fee_rate];
			}



			if($db->total == 0 || $dupe_process == "update" || $bs_act == "bsgoods_one_update" || $bs_act == "bsgoods_one_stock_update"){
				// 상세 이미지 복사 이미지 옵션

				//echo "exchange_rate:".$buying_service_currencyinfo[exchange_rate]."<br>";
				include "buyingService.filter.".$bs_site.".php";
				//echo "<br><b style='color:red;'>pcode : ".$pcode."</b><br>";
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
						if($bs_air_wt <= 1){ // 예상무게가 기본 1파운드 미만일경우
							$air_shipping = $buying_service_currencyinfo[bs_basic_air_shipping]; // 기본 1파운드 항공운송료
						}else{// 예상무계가 1파운드를 초과할경우
							$air_shipping = $buying_service_currencyinfo[bs_basic_air_shipping] + ($buying_service_currencyinfo[bs_add_air_shipping] * ($bs_air_wt - 1));
						}

						$price = str_replace(",","",$price);
						$bs_duty_basis = ($price+$air_shipping)*$buying_service_currencyinfo[exchange_rate]; // 관세 대상 기준금액
						$bs_duty = round($bs_duty_basis*$buying_service_currencyinfo[bs_duty_rate]/100,-1); // 관세
						$bs_supertax = round(($bs_duty_basis+$bs_duty)*$buying_service_currencyinfo[bs_supertax_rate]/100,-1); // 부가세


						$buyingservice_coprice = round(($price+$air_shipping)*$buying_service_currencyinfo[exchange_rate]+$bs_duty+$bs_supertax+$buying_service_currencyinfo[clearance_fee],0);
						// 공급원가 = (orgin 원가 + 항공운송료)* 환율 + 관세 + 부가세 + 통관수수료
						$bs_fee = round($buyingservice_coprice*$bs_fee_rate/100,-1);
						// 구매대행 수수료

						//if($cid2 != ""){
							$category[0] = $cid2;
						//}
						$basic = $cid2; // 기본카테고리지정



						$bimg_text = $prod_img_src;
						$img_url_copy = 1;

						$prod_desc_prod = str_replace("'","\'",$prod_desc_prod);

						//$shotinfo = $prod_desc_prod;
						$basicinfo = $prod_desc_prod;

						$orgin_price = $price;
						//$buying_service_currencyinfo[exchange_rate] = $buying_service_currencyinfo[exchange_rate];
						//$buying_service_currencyinfo[bs_basic_air_shipping] = $air_shipping;
						$air_wt = $bs_air_wt;  // 예상무계 어떻게 할껀지 확인필요
						$duty = $bs_duty+$bs_supertax;
						$buying_service_currencyinfo[clearance_fee] = $buying_service_currencyinfo[clearance_fee];


						$coprice = $buyingservice_coprice;
						if($round_type && $round_precision){//$usable_round == "Y"
							//exit;
							if($round_type == "round"){
								$listprice = roundBetterUp($buyingservice_coprice+$bs_fee,-1*$round_precision);
								$sellprice = roundBetterUp($buyingservice_coprice+$bs_fee,-1*$round_precision);
							}else if($round_type == "floor"){
								$listprice = roundBetterDown($buyingservice_coprice+$bs_fee,-1*$round_precision);
								$sellprice = roundBetterDown($buyingservice_coprice+$bs_fee,-1*$round_precision);
							}

						}else{
							$listprice = round($buyingservice_coprice+$bs_fee,-1);
							$sellprice = round($buyingservice_coprice+$bs_fee,-1);
						}

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

									if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/")){
										mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/");
										chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/",0777);
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
											resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif",$image_info2[0][width],$image_info2[0][height],'W');

											if($chk_mimg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif");
												}
												MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif", MIRROR_NONE);
												resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif",$image_info2[1][width],$image_info2[1][height],'W');
											}

											if($chk_msimg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif");
												}
												MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif", MIRROR_NONE);
												resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif",$image_info2[2][width],$image_info2[2][height],'W');
											}

											if($chk_simg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif");
												}
												MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif", MIRROR_NONE);
												resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif",$image_info2[3][width],$image_info2[3][height],'W');
											}

											if($chk_cimg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif");
												}
												MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif", MIRROR_NONE);
												resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif",$image_info2[4][width],$image_info2[4][height],'W');
											}
										}else if($image_type == "png"){

										//if(substr($allimg_name, -3) == "gif"){
											//copy($allimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif");

											if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif")){
												unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif");
											}

											//copy($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif");
											MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif", MIRROR_NONE);
											resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif",$image_info2[0][width],$image_info2[0][height],'W');

											if($chk_mimg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif");
												}
												MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif", MIRROR_NONE);
												resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif",$image_info2[1][width],$image_info2[1][height],'W');
											}

											if($chk_msimg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif");
												}
												MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif", MIRROR_NONE);
												resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif",$image_info2[2][width],$image_info2[2][height],'W');
											}

											if($chk_simg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif");
												}
												MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif", MIRROR_NONE);
												resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif",$image_info2[3][width],$image_info2[3][height],'W');
											}

											if($chk_cimg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif");
												}
												MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif", MIRROR_NONE);
												resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif",$image_info2[4][width],$image_info2[4][height],'W');
											}

										}else{

											if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif")){
												unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif");
											}


											//copy($allimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif");
											Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif", MIRROR_NONE);
											resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif",$image_info2[0][width],$image_info2[0][height],'W');

											if($chk_mimg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif");
												}
												Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif", MIRROR_NONE);
												resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif",$image_info2[1][width],$image_info2[1][height],'W');
											}

											if($chk_msimg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif");
												}
												Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif", MIRROR_NONE);
												resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif",$image_info2[2][width],$image_info2[2][height],'W');
											}

											if($chk_simg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif");
												}
												Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif", MIRROR_NONE);
												resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif",$image_info2[3][width],$image_info2[3][height],'W');
											}

											if($chk_cimg == 1){
												if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif")){
													unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif");
												}
												Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif", MIRROR_NONE);
												resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif",$image_info2[4][width],$image_info2[4][height],'W');
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

									if($goods_desc_copy){
											$data_text_convert = $basicinfo;
											$data_text_convert = str_replace("\\","",$data_text_convert);
											preg_match_all("|<IMG .*src=\"(.*)\".*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);

											$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/".$pid."/";

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




											for($i=0;$i < count($out);$i++){
												for($j=0;$j < count($out[$i]);$j++){

													$img = returnImagePath($out[$i][$j]);
													$img = ClearText($img);


													try{
														if($img){
															if(substr_count($img,$admin_config[mall_data_root]."/images/product_detail/".$pid."/") == 0){// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
																if(substr_count($img,"$HTTP_HOST")>0){
																	$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"],$img);

																	@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/".$pid."/".returnFileName($img));
																	if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
																		unlink($local_img_path);
																	}

																	$basicinfo = str_replace($img,$admin_config[mall_data_root]."/images/product_detail/".$pid."/".returnFileName($img),$basicinfo);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
																}else{
																	if(substr_count($img,$DOCUMENT_ROOT)){
																		//$img = $DOCUMENT_ROOT.$img;
																		if(@copy($DOCUMENT_ROOT.$img,$_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/".$pid."/".returnFileName($DOCUMENT_ROOT.$img))){
																			$basicinfo = str_replace($img,$admin_config[mall_data_root]."/images/product_detail/".$pid."/".returnFileName($img),$basicinfo);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
																		}
																	}else{
																		if(@copy($img,$_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/".$pid."/".returnFileName($img))){
																			$basicinfo = str_replace($img,$admin_config[mall_data_root]."/images/product_detail/".$pid."/".returnFileName($img),$basicinfo);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
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
											}


									}

									if($bimg_text != ""){
										$bimg_str = ", bimg='$bimg_text' ";
									}
							}




							if($currency_ix != "" && $currency_ix != "0"){
								$currency_ix_str = ", currency_ix='$currency_ix' ";
							}
							$sql = "UPDATE ".TBL_SHOP_PRODUCT." SET
											pcode='$pcode', pname='$pname', state = '1',
											buyingservice_coprice='$buyingservice_coprice',listprice='$listprice',sellprice='$sellprice', coprice='$coprice', editdate = NOW() $currency_ix_str $basicinfo_str 	$bimg_str
											Where id = '$pid' "; // basicinfo='$basicinfo', shotinfo='$shotinfo',
							//echo $sql;
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

								$db->query($sql);
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
														option_div='".$options_price_stock[option_div][$j]."',option_price='".$options_price_stock[price][$j]."', option_stock='".$options_price_stock[stock][$j]."', option_safestock='".$options_price_stock[safestock][$j]."' ,
														option_etc1='".$options_price_stock[etc1][$j]."', insert_yn='Y'
														where id ='".$opnd_ix."' and opn_ix = '".$opn_ix."'";
										}else{
											//$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price,option_m_price, option_d_price, option_a_price, option_stock, option_safestock, option_etc1) ";
											$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price,option_stock, option_safestock, option_etc1) ";
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
														option_name='".trim($options[$i]["option_name"])."', option_kind='".$options[$i]["option_kind"]."',
														option_type='".$options[$i]["option_type"]."', option_use='1',insert_yn='Y'
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
															option_div='".$options[$i][details][$j][option_div]."',option_price='".$options[$i][details][$j][price]."', option_stock='0', option_safestock='0' ,
															option_etc1='".$options[$i][details][$j][etc1]."', insert_yn='Y'
															where id ='".$opnd_ix."' and opn_ix = '".$opn_ix."'";
													$db->query($sql);
												}else{
													//$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price,option_m_price, option_d_price, option_a_price, option_stock, option_safestock, option_etc1) ";
													$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price,option_stock, option_safestock, option_etc1) ";
													//$sql = $sql." values('','$pid','".$opn_ix."','".trim($options[$i][details][$j][option_div])."','".$options[$i][details][$j][price]."','".$options[$i][details][$j][price]."','".$options[$i][details][$j][price]."','".$options[$i][details][$j][price]."','0','0','".$options[$i][details][$j][etc1]."') ";
													$sql = $sql." values('','$pid','".$opn_ix."','".trim($options[$i][details][$j][option_div])."','".$options[$i][details][$j][price]."','0','0','".$options[$i][details][$j][etc1]."') ";

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
							$goods_desc_copy = 1;
							include "goods_input.act.php";
							syslog(LOG_INFO,  "<b style='color:red;'>$pname 상품을 등록중입니다.</b> <b>pcode : $pcode pid : $pid </b><br>\r");
							echo " <b style='color:red;'>$pname 상품을 등록중입니다.</b> <b>pcode : $pcode pid : $pid </b><br>";
						}

				}else{
					if(!$stock_bool){
						$sql = "update shop_product set state = '0', editdate = NOW() where id = '".$pid."'  ";
						$db->query ($sql);

						$goods_update_soldout_cnt++;
						syslog(LOG_INFO, "품절/판매불가 :  pname : $pname price : $price  <b>pcode : $pcode </b>  \r");
						syslog(LOG_INFO, "품절/판매불가 스크래핑 URL :  ".$bs_url."  \r");
						//syslog(LOG_INFO, "품절/판매불가 스크래핑 결과 :  ".$results."  \r");
						echo "품절/판매불가 :  pname : $pname price : $price  <b>pcode : $pcode </b>  ";
					}else{
						$goods_update_soldout_cnt++;
						syslog(LOG_INFO, "정보부족 : <b s>pname: $pname</b> <b>price : $price</b>  <b>pcode : $pcode </b>\r");
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
									for($i=0;$i < $db->tota;$i++){
										$db->fetch($i);
										$ad_ix = $db->dt[id];
										//$_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif"

										if($pid && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/deepzoom/$ad_ix")){
											rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/deepzoom/$ad_ix");
										}
									}

									$db->query("DELETE FROM ".TBL_SHOP_ADDIMAGE." WHERE  pid = '$pid'");


									if($pid && is_dir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/$pid")){
										rmdirr($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/$pid");
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
				echo "해당 상품이 이미 등록되어 있습니다. <b>pcode : $pcode </b><br>";
			}
			set_time_limit(30);
			//$snoopy->fetch($goods_detail_links[$i]);



}


closelog();

?>
