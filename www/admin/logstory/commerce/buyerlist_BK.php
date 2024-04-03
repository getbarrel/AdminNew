<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../report/etcreferer.chart.php");
include("../include/campaign.lib.php");


if(!empty($_GET['mall_ix'])){
    $where .="and od.mall_ix = '".$_GET['mall_ix']."' ";
}
if(!empty($_GET['seller_type'])){
    $where .="and seller_type = '".$_GET['seller_type']."' ";
}

if($status_disp == "IC"){
    $date_str = "od.ic_date";
}else if($status_disp == "DI"){
    $date_str = "od.di_date";
}else if($status_disp == "OC" || $status_disp == ""){
    $date_str = "od.regdate";
}

//		$date_str = "date_format(od.di_date,'%Y%m%d')";
//		$date_str = "date_format(od.ic_date,'%Y%m%d')";
//		$date_str = "date_format(od.regdate,'%Y%m%d')";
$where = "";
if(!empty($_GET["sdate"]) && !empty($_GET["edate"])){
    $startDate = $_GET["sdate"];
    $endDate = $_GET["edate"];
    $sdate = $_GET["sdate"];
    $edate = $_GET["edate"];

    $where .= "and ".$date_str." between '".substr($startDate, 0, 4)."-".substr($startDate, 4, 2)."-".substr($startDate, 6, 2)." 00:00:00' and '".substr($endDate, 0, 4)."-".substr($endDate, 4, 2)."-".substr($endDate, 6, 2)." 23:59:59' ";
}else{
    $sdate = date("Y-m-d");
    $edate = date("Y-m-d");

    $where .= "and ".$date_str." between '".$sdate." 00:00:00' and '".$edate." 23:59:59' ";
}

if(!empty($cid2)){
    $where .= " and od.cid LIKE '".substr($cid2,0,($depth+1)*3)."%'";
}

if(is_array($age)){
    for($i=0;$i < count($age);$i++){
        if($age[$i] != ""){
            if($age_str == ""){
                if($age[$i] == 10){
                    $age_str .= " o.age between 0 and ".($age[$i]+9)." ";
                }else if($age[$i] == 60){
                    $age_str .= " o.age >= ".$age[$i]."  ";
                }else{
                    $age_str .= " o.age between ".$age[$i]." and ".($age[$i]+9)." ";
                }
            }else{
                if($age[$i] == 10){
                    $age_str .= " or o.age between 0 and ".($age[$i]+9)." ";
                }else if($age[$i] == 60){
                    $age_str .= " or o.age >= ".$age[$i]."  ";
                }else{
                    $age_str .= " or o.age between ".$age[$i]." and ".($age[$i]+10)." ";
                }
            }
        }
    }

    if(empty($age_str)){

        $where .= "and ($age_str) ";
    }
}else{
    if($age){
        $where .= "and o.age between ".$age[$i]." and ".($age[$i]+10)." ";
    }
}

if($_GET['member_div'] =="member" ){
    $where .="and o.user_code != '' ";
}else if($_GET['member_div'] =="nonmember" ){
    $where .="and o.user_code = '' ";
}

if(!empty($_GET['sex'])){
    //$where .="and sex = '".$_GET['sex']."' ";
    //컬럼 추가후
}

//promotion_cupon_code
if(!empty($_GET["brand_code"])){
    $brand_code_array = str_replace(" ","",$_GET["brand_code"]);
    $brand_code_array = explode(",",$brand_code_array);
    if(is_array($brand_code_array)){
        $where .= " AND od.brand_code IN ('".implode("','",$brand_code_array)."')";
    }else{
        $where .= " AND od.brand_code = '".$_GET["brand_code"]."' ";
    }
}

if(!empty($_GET["promotion_cupon_code"])){
    $use_coupon_code_array = explode(",",$_GET["promotion_cupon_code"]);
    if(is_array($use_coupon_code_array)){
        $where .= " AND od.use_coupon_code IN ('".implode("','",$use_coupon_code_array)."')";
    }else{
        $where .= " AND od.use_coupon_code = '".$_GET["promotion_cupon_code"]."' ";
    }
}

if(!empty($_GET["product_code"])){
    $product_code_array = explode(",",$_GET["product_code"]);
    if(is_array($product_code_array)){
        $where .= " AND od.pid IN ('".implode("','",$product_code_array)."')";
    }else{
        $where .= " AND od.pid = '".$_GET["product_code"]."' ";
    }
}

if(!empty($_GET["company_v_code"])){
    $company_v_code_array = explode(",",$_GET["company_v_code"]);
    if(is_array($company_v_code_array)){
        $where .= " AND od.company_id IN ('".implode("','",$company_v_code_array)."')";
    }else{
        $where .= " AND od.company_id = '".$_GET["company_v_code"]."' ";
    }
}

if(!empty($_GET["trade_company_code"])){
    $trade_company_code_array = explode(",",$_GET["trade_company_code"]);
    if(is_array($trade_company_code_array)){
        $where .= " AND od.trade_company IN ('".implode("','",$trade_company_code_array)."')";
    }else{
        $where .= " AND od.trade_company = '".$_GET["trade_company_code"]."' ";
    }
}


function ReportTable($vdate,$SelectReport=1){
    global $LargeImageSize;
    global $where;
    global $search_sdate, $search_edate, $report_type, $report_group_type,$sdate,$edate,$member_div;



    $pageview01 = 0;
    $fordb = new forbizDatabase();
    $fordb2 = new forbizDatabase();


    if($SelectReport == ""){
        $SelectReport = 1;
    }


    if($vdate == ""){
        $vdate = date("Ymd", time());
        $selected_date = date("Y-m-d", time());
        $vyesterday = date("Y-m-d", time()-84600);
        $voneweekago = date("Ymd", time()-84600*7);
        $search_sdate = date("Y-m-d", time()-84600*7);
        $search_edate = date("Y-m-d", time());
    }else{
        if($SelectReport ==3){
            $vdate = $vdate."01";
        }
        $selected_date = date("Y-m-d", mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)));
        $vweekenddate = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
        $vyesterday = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);

    }


    $today = date("Ymd", time());
    //$vyesterday = date("Ymd", time()-84600);
    //$voneweekago = date("Ymd", time()-84600*6);
    $vtwoweekago = date("Ymd", time()-84600*14);
    //$vfourweekago = date("Ymd", time()-84600*28);

    $vyesterday = date("Ymd",mktime(0,0,0,substr($today,4,2),substr($today,6,2),substr($today,0,4))-60*60*24);
    //$voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*6);
    $v15ago = date("Ymd",mktime(0,0,0,substr($today,4,2),substr($today,6,2),substr($today,0,4))-60*60*24*15);
    $vfourweekago = date("Ymd",mktime(0,0,0,substr($today,4,2),substr($today,6,2),substr($today,0,4))-60*60*24*28);
    $vonemonthago = date("Ymd",mktime(0,0,0,substr($today,4,2)-1,substr($today,6,2)+1,substr($today,0,4)));
    $v2monthago = date("Ymd",mktime(0,0,0,substr($today,4,2)-2,substr($today,6,2)+1,substr($today,0,4)));
    $v3monthago = date("Ymd",mktime(0,0,0,substr($today,4,2)-3,substr($today,6,2)+1,substr($today,0,4)));



    if(empty($groupbytype)){
        $groupbytype="day";
    }

    if(!is_array($age)){
        $age[] = "";
    }

    // promotion_cupon_code


    if(!empty($_GET["vat_type"])){
        $vat_type = $_GET["vat_type"];
    }else{
        $vat_type = "Y";
    }

    if(!empty($_GET["status_disp"])){
        $status_disp = $_GET["status_disp"];
    }else{
        $status_disp = "OC";
    }


    if(!empty($_GET["groupbytype"])){
        $groupbytype = $_GET["groupbytype"];
    }else{
        $groupbytype = "day";
    }






    if($SelectReport == 1){
        if($fordb->dbms_type == "oracle"){//uid 안됨
            /*
            $sql = "Select cmd.name , cu.id as userid,c.pname, a.*
                from ".TBL_COMMERCE_SALESTACK." a left outer join ".TBL_COMMON_USER." cu on a.ucode = cu.code, ".TBL_SHOP_PRODUCT." c , ".TBL_COMMON_MEMBER_DETAIL." cmd
                where  a.pid = c.id and cu.code = cmd.code and vdate = '$vdate' and step6 = 1 order by a.vdate, vtime";
            */
            //od.regdate like '%Y%m%d' as vdate, od.regdate like '%H' as vtime
            $sql = "Select AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."') as name, od.regdate,  
					cu.id as userid, o.user_code, od.pcnt as vquantity , od.pname, od.pid,  od.psprice as sellprice
					from ".TBL_SHOP_ORDER." o 
					right join ".TBL_SHOP_ORDER_DETAIL." od on o.oid = od.oid
					left join ".TBL_COMMON_USER." cu on o.user_code = cu.code
					left join  ".TBL_COMMON_MEMBER_DETAIL." cmd on  cu.code = cmd.code
					where o.user_code != '' and o.user_code != '6955'
					".$where."
					order by od.regdate ";
            //like '%Y%m%d'
        }else{
            /*
            $sql = "Select AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cu.id as userid,c.pname, a.*
                from ".TBL_COMMERCE_SALESTACK." a left outer join ".TBL_COMMON_USER." cu on a.ucode = cu.code, ".TBL_SHOP_PRODUCT." c , ".TBL_COMMON_MEMBER_DETAIL." cmd
                where  a.pid = c.id and cu.code = cmd.code and vdate = '$vdate' and step6 = 1 order by a.vdate, vtime";
            */
            //od.regdate like '%Y%m%d' as vdate, od.regdate like '%H' as vtime
            $sql = "Select AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."') as name, od.regdate,  
							cu.id as userid, o.user_code, od.pcnt as vquantity , od.pname, od.pid,  od.psprice as sellprice
							from ".TBL_SHOP_ORDER." o 
							right join ".TBL_SHOP_ORDER_DETAIL." od on o.oid = od.oid
							left join ".TBL_COMMON_USER." cu on o.user_code = cu.code
							left join  ".TBL_COMMON_MEMBER_DETAIL." cmd on  cu.code = cmd.code
							where 1
							".$where."
							order by od.regdate ";
            //like '%Y%m%d'

        }

        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2 || $SelectReport == 4){
        if($SelectReport == "4"){
            $vdate = $search_sdate;
            $vweekenddate = date("Y-m-d", mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4)));
        }

        if($fordb->dbms_type == "oracle"){
            /*
            $sql = "Select cmd.name, cu.id as userid,c.pname, a.* from ".TBL_COMMERCE_SALESTACK." a left outer join ".TBL_COMMON_USER." cu on a.ucode = cu.code, ".TBL_SHOP_PRODUCT." c , ".TBL_COMMON_MEMBER_DETAIL." cmd
            where a.pid = c.id and cu.code = cmd.code and vdate between '$vdate' and '$vweekenddate' and step6 = 1
            order by a.vdate, vtime";
            */
            //od.regdate like '%Y%m%d' as vdate, od.regdate like '%H' as vtime
            $sql = "Select AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."') as name, od.regdate,  
				cu.id as userid, o.user_code, od.pcnt as vquantity , od.pname, od.pid,  od.psprice as sellprice
				from ".TBL_SHOP_ORDER." o 
				right join ".TBL_SHOP_ORDER_DETAIL." od on o.oid = od.oid
				left join ".TBL_COMMON_USER." cu on o.user_code = cu.code
				left join  ".TBL_COMMON_MEMBER_DETAIL." cmd on  cu.code = cmd.code
				where 1
				".$where."
				order by od.regdate ";
            //like '%Y%m%d'

        }else{
            /*
            $sql = "Select AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cu.id as userid,c.pname, a.* from ".TBL_COMMERCE_SALESTACK." a left outer join ".TBL_COMMON_USER." cu on a.ucode = cu.code, ".TBL_SHOP_PRODUCT." c , ".TBL_COMMON_MEMBER_DETAIL." cmd
            where a.pid = c.id and cu.code = cmd.code and vdate between '$vdate' and '$vweekenddate' and step6 = 1
            order by a.vdate, vtime";
            */
            //od.regdate like '%Y%m%d' as vdate, od.regdate like '%H' as vtime
            $sql = "Select AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."') as name, od.regdate,  
				cu.id as userid, o.user_code, od.pcnt as vquantity , od.pname, od.pid,  od.psprice as sellprice
				from ".TBL_SHOP_ORDER." o 
				right join ".TBL_SHOP_ORDER_DETAIL." od on o.oid = od.oid
				left join ".TBL_COMMON_USER." cu on o.user_code = cu.code
				left join  ".TBL_COMMON_MEMBER_DETAIL." cmd on  cu.code = cmd.code
				where 1
				".$where."
				order by od.regdate ";
            //like '%Y%m%d'
        }

        $group_title = "날짜";
        if($SelectReport == 2){
            $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
        }else if($SelectReport == 4){
            //echo $search_sdate;
            $s_week_num = date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4)));
            $e_week_num = date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4)));
            $dateString = "기간별 : ".getNameOfWeekday($s_week_num,$search_sdate,"priodname")."~".getNameOfWeekday($e_week_num,$search_edate,"priodname");
        }
    }else if($SelectReport == 3){
        if($fordb->dbms_type == "oracle"){
            /*
            $sql = "Select cmd.name, cu.id as userid,c.pname, a.*
                from ".TBL_COMMERCE_SALESTACK." a left outer join ".TBL_COMMON_USER." cu on a.ucode = cu.code, ".TBL_SHOP_PRODUCT." c , ".TBL_COMMON_MEMBER_DETAIL." cmd
                where a.pid = c.id and cu.code = cmd.code and vdate LIKE '".substr($vdate,0,6)."%' and step6 = 1
                order by a.vdate, vtime";
            */
            //od.regdate like '%Y%m%d' as vdate, od.regdate like '%H' as vtime
            $sql = "Select AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."') as name, od.regdate,  
				cu.id as userid, o.user_code, od.pcnt as vquantity , od.pname, od.pid,  od.psprice as sellprice
				from ".TBL_SHOP_ORDER." o 
				right join ".TBL_SHOP_ORDER_DETAIL." od on o.oid = od.oid
				left join ".TBL_COMMON_USER." cu on o.user_code = cu.code
				left join  ".TBL_COMMON_MEMBER_DETAIL." cmd on  cu.code = cmd.code
				where 1
				".$where."
				order by od.regdate ";
            //order by od.regdate like '%Y%m%d'
        }else{
            //od.regdate like '%Y%m%d' as vdate, od.regdate like '%H' as vtime
            $sql = "Select AES_DECRYPT(UNHEX(cmd.name),'".$fordb->ase_encrypt_key."') as name, od.regdate,  
				cu.id as userid, o.user_code, od.pcnt as vquantity , od.pname, od.pid,  od.psprice as sellprice
				from ".TBL_SHOP_ORDER." o 
				right join ".TBL_SHOP_ORDER_DETAIL." od on o.oid = od.oid
				left join ".TBL_COMMON_USER." cu on o.user_code = cu.code
				left join  ".TBL_COMMON_MEMBER_DETAIL." cmd on  cu.code = cmd.code
				where 1
				".$where."
				order by od.regdate ";
            //order by od.regdate like '%Y%m%d'
        }

        $dateString = getNameOfWeekday(0,$vdate,"monthname");
    }


    //echo nl2br($sql);
    $fordb->query($sql);
    $total = $fordb->total;



    $mstring = $mstring.TitleBar("상품구매고객",$dateString);
//	$mstring = $mstring."<table cellpadding=0 cellspacing=0 width=745 border=0 >\n";
//	$mstring = $mstring."<tr  align=center><td colspan=3 style='padding-bottom:10px;'>".etcrefererGraph($vdate,$SelectReport)."</td></tr>\n";
//	$mstring = $mstring."<tr  align=center><td colspan=3 ></td></tr>\n";
//	$mstring = $mstring."</table>";
    /*
        $mstring = $mstring."<table cellpadding=3 cellspacing=0 width=745  >\n";
        $mstring = $mstring."<tr height=25 align=center><td width=150 class=s_td width=30>항목</td><td class=m_td width=200>페이지 명</td><td class=e_td width=50>페이지뷰</td></tr>\n";
        $mstring = $mstring."<tr height=25 bgcolor=#ffffff>
            <td bgcolor=#efefef align=center id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">최다 요청</td>
            <td align=right id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">오후 2:00:00 ∼ 오후 3:00:00</td>
            <td bgcolor=#efefef  align=right id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">543</td>
            </tr>\n";
        $mstring = $mstring."<tr height=25 bgcolor=#ffffff>
            <td bgcolor=#efefef align=center id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">최소 요청</td>
            <td align=right id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">오후 2:00:00 ∼ 오후 3:00:00</td>
            <td bgcolor=#efefef  align=right id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">33</td>
            </tr>\n";
        $mstring = $mstring."</table><br>\nword-break:keep-all";
    */


    $mstring .="<table cellpadding=0 cellspacing=0 width=100%>
	<tr height=150>
		<td   >
			 <form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
			<input type='hidden' name='mode' value='search'>
			<input type='hidden' name='cid2' value='$cid2'>
			<input type='hidden' name='depth' value='$depth'>
			<input type='hidden' name='groupbytype' value='$groupbytype'>
			<input type='hidden' name='sprice' value='0' />
			<input type='hidden' name='eprice' value='1000000' />
			<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05 align=center' style='padding:1px'>
						<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='search_table_box'>
							<col width='15%'>
							<col width='35%'>
							<col width='15%'>
							<col width='35%'>";
    if($_SESSION["admin_config"]['front_multiview'] == "Y"){

        $mstring .= "
					<tr>
						<td class='search_box_title' > 쇼핑몰타입</td>
						<td class='search_box_item' colspan=3>".GetDisplayDivision($_GET['mall_ix'], "select")." </td>
					</tr>";
    }


    $mstring .= "
							<tr height=27>
							  <td class='search_box_title'><b>구매일자</b></td>
							  <td class='search_box_item' colspan=3 >
								<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
									<col width=70>
									<col width=20>
									<col width=70>
									<col width=*>
									<tr>
										<TD nowrap>
										<input type='text' name='sdate' class='textbox' value='".$sdate."' style='height:20px;width:70px;text-align:center;' id='start_datepicker'>										
										</TD>
										<TD align=center> ~ </TD>
										<TD nowrap>
										<input type='text' name='edate' class='textbox' value='".$edate."' style='height:20px;width:70px;text-align:center;' id='end_datepicker'>
										<!--SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년
										<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월
										<SELECT name=ToDD></SELECT> 일 -->
										</TD>
										<TD style='padding:0px 10px'>
											<a href=\"javascript:setSelectDate('$today','$today',1);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_today.gif'></a>
											<a href=\"javascript:setSelectDate('$vyesterday','$vyesterday',1);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_yesterday.gif'></a>
											<a href=\"javascript:setSelectDate('$voneweekago','$today',1);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_1week.gif'></a>
											<a href=\"javascript:setSelectDate('$v15ago','$today',1);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_15days.gif'></a>
											<a href=\"javascript:setSelectDate('$vonemonthago','$today',1);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_1month.gif'></a>
											<a href=\"javascript:setSelectDate('$v2monthago','$today',1);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_2months.gif'></a>
											<a href=\"javascript:setSelectDate('$v3monthago','$today',1);\"><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_3months.gif'></a>
										</TD>
									</tr>
								</table>
							  </td>
							</tr>
							<!--
							<tr>
								<td class='search_box_title'><b>전시 카테고리선택</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'>".getCategoryList3("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid2)."</td>
											<td style='padding-right:5px;'>".getCategoryList3("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid2)."</td>
											<td style='padding-right:5px;'>".getCategoryList3("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid2)."</td>
											<td>".getCategoryList3("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $cid2)."</td>
										</tr>
									</table>
								</td>
							</tr>
							-->
							<!--
							<tr  height=27>
							  <td class='search_box_title' width='13%' bgcolor='#efefef' align=center >연령대</td>
							  <td class='search_box_item' width='*' align=left style='padding-left:5px;' colspan=3>
							  
								  <input type='checkbox' name='age[]' id='age_' value='' ".CompareReturnValue("",$age,"checked")."><label for='age_'>전체</label>
								  <input type='checkbox' name='age[]' id='age_10' value='10'  ".CompareReturnValue("10",$age,"checked")."><label for='age_10'  style='width:200px;'>~10대</label>
								  <input type='checkbox' name='age[]' id='age_20' value='20'  ".CompareReturnValue("20",$age,"checked")."><label for='age_20' >20대</label>
								  <input type='checkbox' name='age[]' id='age_30' value='30'  ".CompareReturnValue("30",$age,"checked")."><label for='age_30' >30대</label>
								  <input type='checkbox' name='age[]' id='age_40' value='40'  ".CompareReturnValue("40",$age,"checked")."><label for='age_40' >40대</label>
								  <input type='checkbox' name='age[]' id='age_50' value='50'  ".CompareReturnValue("50",$age,"checked")."><label for='age_50'  >50대</label>
								  <input type='checkbox' name='age[]' id='age_60' value='60'  ".CompareReturnValue("60",$age,"checked")."><label for='age_60' >ETC</label>
								</select>
							  </td>
							 </tr>
							 -->
							<tr  height=27>
								<td class='search_box_title'><b>회원/비회원 여부</b></td>
								<td class='search_box_item' colspan='3'>
									<select name='member_div' style='font-size:12px;'>
										<option value='' ".ReturnStringAfterCompare($member_div, "", " selected").">전체</option>
										<option value='member' ".ReturnStringAfterCompare($member_div, "member", " selected").">회원</option>
										<option value='nonmember' ".ReturnStringAfterCompare($member_div, "nonmember", " selected").">비회원</option>
									</select>
								</td>
								<!--
								<td class='search_box_title' bgcolor='#efefef' align=center>성별검색 </td>
								  <td class='search_box_item' align=left style='padding-left:5px;'>
								  <input type=radio name='sex' value='' id='sex_all'  ".CompareReturnValue("0",$sex,"checked")." checked><label for='sex_all'>모두</label>
								  <input type=radio name='sex' value='M' id='sex_man'  ".CompareReturnValue("M",$sex,"checked")."><label for='sex_man'>남자</label>
								  <input type=radio name='sex' value='W' id='sex_women' ".CompareReturnValue("W",$sex,"checked")."><label for='sex_women'>여자</label>
								</td>
								-->
							</tr>
							";
    if(false){
        $mstring .= "
							<tr  height=27>
							  <td class='search_box_title' width='13%' bgcolor='#efefef' align=center >지역선택</td>
							  <td class='search_box_item' width='*' align=left style='padding-left:5px;'>
							  <select name='region' >
								  <option value=''>-- 선택 --</option>
								  <option value='서울'  ".CompareReturnValue("서울",$region,"selected").">서울</option>
								  <option value='충북'  ".CompareReturnValue("충북",$region,"selected").">충북</option>
								  <option value='충남'  ".CompareReturnValue("충남",$region,"selected").">충남</option>
								  <option value='전북'  ".CompareReturnValue("전북",$region,"selected").">전북</option>
								  <option value='제주'  ".CompareReturnValue("제주",$region,"selected").">제주</option>
								  <option value='전남'  ".CompareReturnValue("전남",$region,"selected").">전남</option>
								  <option value='경북'  ".CompareReturnValue("경북",$region,"selected").">경북</option>
								  <option value='경남'  ".CompareReturnValue("경남",$region,"selected").">경남</option>
								  <option value='경기'  ".CompareReturnValue("경기",$region,"selected").">경기</option>
								  <option value='부산'  ".CompareReturnValue("부산",$region,"selected").">부산</option>
								  <option value='대구'  ".CompareReturnValue("대구",$region,"selected").">대구</option>
								  <option value='인천'  ".CompareReturnValue("인천",$region,"selected").">인천</option>
								  <option value='광주'  ".CompareReturnValue("광주",$region,"selected").">광주</option>
								  <option value='대전'  ".CompareReturnValue("대전",$region,"selected").">대전</option>
								  <option value='울산'  ".CompareReturnValue("울산",$region,"selected").">울산</option>
								  <option value='강원'  ".CompareReturnValue("강원",$region,"selected").">강원</option>
								</select>
							  </td>
							<td class='search_box_title' bgcolor='#efefef' align=center>성별검색 </td>
							  <td class='search_box_item' align=left style='padding-left:5px;'>
							  <input type=radio name='sex' value='' id='sex_all'  ".CompareReturnValue("0",$sex,"checked")." checked><label for='sex_all'>모두</label>
							  <input type=radio name='sex' value='M' id='sex_man'  ".CompareReturnValue("M",$sex,"checked")."><label for='sex_man'>남자</label>
							  <input type=radio name='sex' value='W' id='sex_women' ".CompareReturnValue("W",$sex,"checked")."><label for='sex_women'>여자</label>
							</td>
							</tr>
							";
    }
    if(false){
        $mstring .= "
							<tr>
								<td class='search_box_title'><b>제휴사 카테고리선택</b></td>
								<td class='search_box_item' colspan=3>
									<select name='company_cid1' id='com_cid1' onchange='loadComCategory(this);'><option value=''>대분류</option>".getCompanyCateSelectBoxOption(1,$company_cid1)."</select>
									";
        if(!empty($company_cid)){
            $mstring .="
													<select name='company_cid' id='com_cid2'>".getCompanyCateSelectBoxOption(2,$company_cid)."</select>";
        }else{
            $mstring .="
													<select name='company_cid' id='com_cid2'><option value=''>중분류</option></select>";
        }
        $mstring .=	"
								</td>
							</tr>";
    }

    if(false) {
        $mstring .= "
							<tr>
								<td class='search_box_title'><b>브랜드</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../../images/" . $_SESSION["admininfo"]["language"] . "/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../../code_search.php?search_type=brand',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../../images/" . $_SESSION["admininfo"]["language"] . "/btn_del.gif' border=0 align=absmiddle onClick=\"$('#brand_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='brand_code' id='brand_code' value='" . $brand_code . "' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>상품코드</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../../images/" . $_SESSION["admininfo"]["language"] . "/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../../code_search.php?search_type=product',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../../images/" . $_SESSION["admininfo"]["language"] . "/btn_del.gif' border=0 align=absmiddle onClick=\"$('#product_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='product_code' id='product_code' value='" . $product_code . "' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>";
    }

    if($_SESSION['admininfo']['admin_level'] == 9 && false){
        $mstring .="<tr>
								<td class='search_box_title'><b>셀러업체</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../../code_search.php?search_type=company_v',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"$('#company_v_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='company_v_code' id='company_v_code' value='".$company_v_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>매입업체</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../code_search.php?search_type=trade_company',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"$('#trade_company_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='trade_company_code' id='trade_company_code' value='".$trade_company_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>";
    }
    if(false){
        $mstring .="<tr>
								<td class='search_box_title'><b>프로모션(카드)</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../code_search.php?search_type=promotion_card',600,380,'code_search')\"  style='cursor:pointer;'></td>
											<td><input type=text class='textbox' name='promotion_card_code' id='promotion_card_code' value='".$promotion_card_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>";


    $mstring .="
							<tr>
								<td class='search_box_title'><b>프로모션(쿠폰)</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../code_search.php?search_type=promotion_cupon',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"$('#promotion_cupon_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='promotion_cupon_code' id='promotion_cupon_code' value='".$promotion_cupon_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>";
    }

    if($_SESSION['admininfo']['admin_id'] == "forbiz"){
        if($_SESSION['admininfo']['admin_level'] == 9 && false){
            $mstring .="<tr>
								<td class='search_box_title'><b>제휴사</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'><img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PoPWindow('../code_search.php?search_type=company_j',600,380,'code_search')\"  style='cursor:pointer;'>
											<img src='../../images/".$_SESSION["admininfo"]["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"$('#company_j_code').val('');\" style='cursor:pointer;' alt='삭제' title='삭제'/>
											</td>
											<td><input type=text class='textbox' name='company_j_code' id='company_j_code' value='".$company_j_code."' style='width:750px;' ></td>
										</tr>
									</table>
								</td>
							</tr>";
        }
    }

    $mstring .=	"
							<tr>
							    <!--
								<td class='search_box_title'><b>VAT</b></td>
                                <td class='search_box_item'  >
                                    <input type='radio' name='vat_type'  id='vat_y' value='Y' ".ReturnStringAfterCompare($vat_type, "Y", " checked")."><label for='vat_y'>포함</label>
                                    <input type='radio' name='vat_type' id='vat_n' value='N' ".ReturnStringAfterCompare($vat_type,"N"," checked")."><label for='vat_n'>제외</label>
                                </td>
                                -->
								<td class='search_box_title'><b>집계기준</b></td>
                                <td class='search_box_item' colspan='3' >
                                    <input type='radio' name='status_disp'  id='oc' value='OC' ".ReturnStringAfterCompare($status_disp, "OC", " checked")."><label for='oc'>주문일 기준</label>
									<input type='radio' name='status_disp'  id='ic' value='IC' ".ReturnStringAfterCompare($status_disp, "IC", " checked")."><label for='ic'>결제완료 기준</label>
                                    <input type='radio' name='status_disp' id='di' value='DI' ".ReturnStringAfterCompare($status_disp,"DI"," checked")."><label for='di'>출고완료 기준</label>
                                </td> 
                            </tr>
						</table>
					</td>
					<th class='box_06'></th>
				</tr>
				<tr>
					<th class='box_07'></th>
					<td class='box_08'></td>
					<th class='box_09'></th>
				</tr>
				<tr >
					<td colspan=2 align=center style='padding:10px 0px'><input type=image src='../../images/".$_SESSION["admininfo"]["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	</table>";

    $mstring .= "<form name='list_frm' method='post' action='/admin/member/member_batch.act.php'  target='act' >
					<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
					<table cellpadding=3 cellspacing=0 width='100%' class='list_table_box'  >
								<col width='4%'>
								<col width='9%'>
								<col width='9%' >
								<col width=8% nowrap>
								<col width='*' nowrap>
								<col width=7% nowrap>
								<col width=7% nowrap>
								<col width=7% nowrap>
								<col width=9% nowrap>
									";
    $mstring = $mstring."<tr height=40 align=center style='font-weight:bold'>
							<td class=s_td ><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
							<td class=s_td>날짜/시간</td>
							<td class=m_td >회원명</td>
							<td class=m_td nowrap>회원아이디</td>
							<td class=m_td  nowrap>상품명</td>
							<td class=m_td nowrap>구매수량</td>
							<td class=e_td nowrap>판매가</td>
							<td class=e_td nowrap>매출액</td>
							<td class=e_td nowrap>매출액<br>점유율(%)</td>
							</tr>\n";

    if($fordb->total == 0){
        $mstring = $mstring."<tr height=150 bgcolor=#ffffff align=center><td colspan=9>결과값이 없습니다.</td></tr>\n";
    }else{

        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);
            $sale_sum += $fordb->dt['vquantity']*$fordb->dt['sellprice'];
        }
        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);



            if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($_SESSION["admin_config"]["mall_data_root"]."/images/product", $fordb->dt['pid'], "s", $fordb->dt)) || $image_hosting_type=='ftp'){
                $img_str = PrintImage($_SESSION["admin_config"]["mall_data_root"]."/images/product", $fordb->dt['pid'], "s", $fordb->dt);
            }else{
                $img_str = "../../image/no_img.gif";
            }

            //<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:5px;padding-right:5px' nowrap>".ReturnDateFormat($fordb->dt['vdate'])." : ".$fordb->dt['vtime']."</td>
            $mstring .= "<tr height=40 bgcolor=#ffffff  id='Report$i'>
			<td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$fordb->dt['ucode']."'></td>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:5px;padding-right:5px' nowrap>".substr($fordb->dt['regdate'], 0, 10)." : ".substr($fordb->dt['regdate'], 11, 2)."</td>
			<td class='list_box_td point'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".$fordb->dt['name']."</td>
			<td class='list_box_td list_bg_gray'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt['userid']."</td>
			<td class='list_box_td point' style='text-align:left;padding-left:10px;' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">
			<a href='../../goods_input.php?id=".$fordb->dt['id']."' class='screenshot'  rel='".PrintImage($_SESSION["admin_config"]["mall_data_root"]."/images/product", $fordb->dt['pid'], $LargeImageSize, $fordb->dt)."'  ><img src='".$img_str."' align=absmiddle width=30 height=30 style='float:left;margin-right:10px;border:1px solid silver'></a>
			<div style='padding-top:10px;'>".$fordb->dt['pname']."</div>
			</td>
			<td class='list_box_td list_bg_gray'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt['vquantity']."</td>
			<td class='list_box_td'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['sellprice'],0)."</td>
			<td class='list_box_td list_bg_gray'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['vquantity']*$fordb->dt['sellprice'],0)."</td>
			<td class='list_box_td'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($sale_sum > 0 ? $fordb->dt['vquantity']*$fordb->dt['sellprice']/$sale_sum*100:0),2)."% </td>
			</tr>";

            /*
                    $mstring .= "<tr height=30>
                    <td style='padding-left:20px' class=s_td>결제완료</td><td align=center class=m_td width=125>".$fordb->dt['step6']."</td><td align=center class=m_td width=125>-</td><td align=center class=e_td width=125>-</td>
                    </tr>\n";
            */
            //	$exitcnt = $pageview01 + returnZeroValue($fordb->dt['visit_cnt']);


        }
    }
    $mstring = $mstring."</table>\n";
//	$mstring = $mstring."<table cellpadding=3 cellspacing=0 width=745  >\n";
//	if ($pageview01 == 0){
//		$mstring = $mstring."<tr height=50 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
//	}
//	$mstring = $mstring."<tr height=2 bgcolor=#ffffff align=center><td colspan=5 width=190></td></tr>\n";
//	$mstring = $mstring."<tr height=25 align=center><td width=50 class=s_td width=30 colspan=2>합계</td><td class=e_td width=190>".$pageview01."</td></tr>\n";
//	$mstring = $mstring."</table>\n";
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                해당기간동안 구매한 회원의 정보입니다 <br>
                상품구매고객을 대상으로 이벤트나 프로모션을 진행하실수 있습니다
                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );
   // $mstring .= SendCampaignBox($total);
    $mstring .= "</form>";

    $mstring .= HelpBox("상품구매고객", $help_text);


    $mstring .= "
<script Language='JavaScript' type='text/javascript'>
$(function() {

	$(\"#start_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
			$('#end_datepicker').val(dateText);
		}else{
			$('#end_datepicker').datepicker('setDate','+0d');
		}
	}

	});

	$(\"#end_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	//$('#end_timepicker').timepicker();
});


function loadCategory(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	//var depth = sel.getAttribute('depth');
	var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
	//alert(depth);
	//dynamic.src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	//alert(1);
	// 크롬과 파폭에서 동작을 한번밖에 안하기에 iframe으로 처리 방식을 바꿈 2011-04-07 kbk
	window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

}

function setSelectDate(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	$(\"#start_datepicker\").val(FromDate);
	$(\"#end_datepicker\").val(ToDate);
}

</script>";

    return $mstring;
}

if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";

    $ca = new Calendar();
    $ca->LinkPage = 'buyerlist.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";


}else{
    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->forbizLeftMenu = commerce_munu('buyerlist.php');
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->Navigation = "이커머스분석 > 고객종합분석(CRM) > 상품구매고객";
    $p->title = "상품구매고객";
    $p->PrintReportPage();
}
?>
