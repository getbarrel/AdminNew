<?
include("../class/layout.class");

$db = new Database();



$where="where 1";

if($_GET["search_type"] != "" && $_GET["search_text"] != ""){
	$where .= " and $search_type LIKE  '%$search_text%' ";
}

$cupon_div = $_GET["cupon_div"];
if(is_array($cupon_div)){
	for($i=0;$i < count($cupon_div);$i++){
		if($cupon_div[$i] != ""){
			if($cupon_div_str == ""){
				$cupon_div_str .= "'".$cupon_div[$i]."'";
			}else{
				$cupon_div_str .= ",'".$cupon_div[$i]."' ";
			}
		}
	}

	if($cupon_div_str != ""){
		$where .= " AND c.cupon_div in (".$cupon_div_str.") ";
	}
}else{
	if($cupon_div){
		$where .= " AND c.cupon_div = '".$cupon_div."' ";
	}else{
		$cupon_div = array();
	}
}
/*
if($_GET["cupon_div"] != ""){
	$where .= " and cupon_div =  '".$_GET["cupon_div"]."' ";
}
*/

$cupon_use_div = $_GET["cupon_use_div"];
if(is_array($cupon_use_div)){
	for($i=0;$i < count($cupon_use_div);$i++){
		if($cupon_use_div[$i] != ""){
			if($cupon_use_div_str == ""){
				$cupon_use_div_str .= "'".$cupon_use_div[$i]."'";
			}else{
				$cupon_use_div_str .= ",'".$cupon_use_div[$i]."' ";
			}
		}
	}

	if($cupon_use_div_str != ""){
		$where .= " AND cupon_use_div in (".$cupon_use_div_str.") ";
	}
}else{
	if($cupon_use_div){
		$where .= " AND cupon_use_div = '".$cupon_use_div."' ";
	}else{
		$cupon_use_div = array();
	}
}
/*
if($_GET["cupon_use_div"] != ""){
	$where .= " and cupon_use_div =  '".$_GET["cupon_use_div"]."' ";
}
*/
if($_GET["is_use"] != ""){
	$where .= " and c.is_use =  '".$_GET["is_use"]."' ";
}

if($_GET["disp"] != ""){
	$where .= " and c.disp =  '".$_GET["disp"]."' ";
}

if($_GET["mall_ix"] != ""){
	$where .= " and c.mall_ix =  '".$_GET["mall_ix"]."' ";
}

$sql = "select * from ".TBL_SHOP_CUPON." c $where";
//left join ".TBL_SHOP_CUPON_PUBLISH." cp on c.cupon_ix = cp.cupon_ix
//echo $sql;
$db->query($sql);
$total = $db->total;

$Script ="<script language=javascript>
function DeleteCupon(cupon_ix){
	if(confirm('해당 쿠폰을 삭제 하시겠습니까?')){
		document.location.href='cupon.act.php?act=delete&cupon_ix='+cupon_ix
	}
}
</script>";


$Contents = "
<table width='100%' border='0' cellpadding='0' cellspacing='0'>
  <tr>
    <td height='2'></td>
  </tr>
  <tr>
	<td align='left' colspan=6 style='padding:0 0 10px 0;'> ".GetTitleNavigation("쿠폰목록", "전시관리 > 쿠폰목록 ")."</td>
  </tr>
  <!--tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	    	<div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='cupon_publish.php'\" >쿠폰발행</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='cupon_modify.php'\">쿠폰생성</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03' class='on'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='cupon_publish_list.php'\">쿠폰목록</td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
						<td style='width:650px;text-align:right;vertical-align:bottom;padding:0 0 10px 0;'>
							총건수 :&nbsp;<b>".$total."</b>
						</td>
					</tr>
					</table>
				</div>
	    </td>
	</tr-->
	<tr>
    <td colspan=7>
        <form name='search_coupon'>
        <table border='0' cellpadding='0' cellspacing='0' width='100%'>
            <tr>
                <td style='width:100%;' valign=top colspan=3>
                    <table width=100%  border=0>
                        <tr height=25>
                            <td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>쿠폰 검색하기</b></td>
                        </tr>
                        <tr>
                            <td align='left' colspan=2 height=50 width='100%' valign=top style='padding-top:5px;'>
                                <table class='box_shadow' style='width:100%;' align=left cellpadding='0' cellspacing='0' border='0'>
                                    <tr>
                                        <th class='box_01'></th>
                                        <td class='box_02'></td>
                                        <th class='box_03'></th>
                                    </tr>
                                    <tr>
                                        <th class='box_04'></th>
                                        <td class='box_05' valign=top>
                                            <TABLE height=20 cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0>
                                                <TR>
                                                    <TD bgColor=#ffffff style='padding:0 0 0 0;height:30px;'>
                                                        <table cellpadding=2 cellspacing=1 width='100%' class='search_table_box'>
															<col width='15%'>
															<col width='35%'>
															<col width='15%'>
															<col width='35%'>";
															if($_SESSION["admin_config"][front_multiview] == "Y"){
															$Contents .= "
															<tr>
																<td class='search_box_title' > 프론트 전시 구분</td>
																<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
															</tr>";
															}
															$Contents .= "
                                                            <tr>
                                                                <th class='search_box_title' >조건검색 : </th>
                                                                <td class='search_box_item' colspan=3>
                                                                    <table cellpadding=0 cellspacing=0>
                                                                        <tr>
                                                                            <td>
                                                                                <select name=search_type>
                                                                                <option value='' >검색조건</option>
																				<option value='cupon_kind' ".CompareReturnValue("cupon_kind",$search_type,"selected").">쿠폰명</option>
																				<option value='cupon_ix' ".CompareReturnValue("cupon_ix",$search_type,"selected").">쿠폰번호</option>																				
                                                                                </select>
                                                                            </td>
                                                                            <td><input type=text class=textbox name='search_text' value='".$search_text."' style='width:200px;' ></td>
                                                                        </tr>

                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <tr height=30>
                                                                <th class='search_box_title' >쿠폰종류 : </th>
                                                                <td class='search_box_item' colspan=3>";

													  foreach($_COUPON_KIND as $key => $value){
														$Contents .= "<input type='checkbox' name='cupon_div[]' id='cupon_div_".$key."' value='".$key."' ".(in_array($key,$cupon_div) ? "checked":"")." validation=true title='쿠폰종류'> <label for='cupon_div_".$key."' >".$value."</label> ";
													  }
													  $Contents .= "
                                                                </td>
                                                            </tr>
															<tr >
															  <td class='input_box_title' >  <b>쿠폰사용 구분</b></td>
															  <td class='input_box_item' colspan=3>";
															  foreach($_COUPON_USE_DIV as $key => $value){
																$Contents .= "<input type='checkbox' name='cupon_use_div[]' id='cupon_use_div_".$key."' value='".$key."' ".(in_array($key,$cupon_use_div) ? "checked":"")."  validation=true title='쿠폰사용'> <label for='cupon_use_div_".$key."' >".$value."</label> ";
															  }
															  $Contents .= "
																<!--input type='radio' name='cupon_use_div' id='cupon_use_div_g' value='G' ".CompareReturnValue("G",$cupon_use_div,"checked")." validation=true title='쿠폰종류'> <label for='cupon_use_div_G' >일반쿠폰</label> 
																 <input type='radio' name='cupon_use_div' id='cupon_use_div_d' value='D' ".CompareReturnValue("D",$cupon_use_div,"checked")." validation=true title='쿠폰종류'><label for='cupon_use_div_d' >중복쿠폰</label>
																 <input type='radio' name='cupon_use_div' id='cupon_use_div_d' value='C' ".CompareReturnValue("R",$cupon_use_div,"checked")." validation=true title='쿠폰종류'><label for='cupon_use_div_r' >C/S쿠폰</label>
																 <input type='radio' name='cupon_use_div' id='cupon_use_div_p' value='M' ".CompareReturnValue("P",$cupon_use_div,"checked")." validation=true title='쿠폰종류'><label for='cupon_use_div_p' >모바일쿠폰</label>
																 <input type='radio' name='cupon_use_div' id='cupon_use_div_p' value='P' ".CompareReturnValue("P",$cupon_use_div,"checked")." validation=true title='쿠폰종류'><label for='cupon_use_div_p' >회원패키지쿠폰</label-->
															  </td>
															  <!--td class='search_box_title' >  <b>C/S 쿠폰여부</b></td>
															  <td class='search_box_item'>
																	<input type='checkbox' name='is_cs' id='is_cs_1'  align='middle' value='1' ".($is_cs == '1' ? "checked":"")."><label for='is_cs_1' class='green'>CS쿠폰</label>
															  </td-->
															</tr>
															<tr >
															  <td class='search_box_title' >  <b>노출여부</b></td>
															  <td class='search_box_item'>
																	<input type='radio' name='disp' id='disp_1'  align='middle' value='1' ".($disp == '1' || $disp == '' ? "checked":"")."><label for='disp_1' class='green'>노출함</label> 
																	<input type='radio' name='disp' id='disp_0'  align='middle' value='0' ".($disp == '0' ? "checked":"")."><label for='disp_0' class='green'>노출안함</label> 
															  </td> 
															  <td class='input_box_title' >  <b>사용여부</b></td>
															  <td class='input_box_item'>
																	<input type='radio' name='is_use' id='is_use_1'  align='middle' value='1' ".($is_use == '1' || $is_use == '' ? "checked":"")."><label for='is_use_1' class='green'>사용함</label> 
																	<input type='radio' name='is_use' id='is_use_0'  align='middle' value='0' ".($is_use == '0' ? "checked":"")."><label for='is_use_0' class='green'>미사용</label> 
															  </td>
															</tr>
                                                        </table>
                                                    </TD>
                                                </TR>

                                            </TABLE>
                                        </td>
                                        <th class='box_06'></th>
                                    </tr>
                                    <tr>
                                        <th class='box_07'></th>
                                        <td class='box_08'></td>
                                        <th class='box_09'></th>
                                    </tr>
                                </table>

                            </td>
                        </tr>
                        <tr >
                            <td colspan=3 align=center  style='padding:10px 0 0 0'>
                                <input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        </form>
    </td>
	</tr>
  <tr>
    <td valign='top'>
      <table width='100%' border='0' cellpadding='0' cellspacing='0'>
        <tr> 
          <td class='blue16'>전체 등록 쿠폰 수 :&nbsp;<b>".$total."</b>&nbsp;개</td>
          <td align=right></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td height='5'></td>
  </tr>";

if($db->total < 1){
	$Contents .= "<tr height=150><td colspan=20 style='border:1px solid silver;' align=center> 등록된 쿠폰 정보가 없습니다. </td></tr>";
}else{
$max = 10;

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

	$sql = "Select c.*, sum(CASE WHEN cp.publish_ix IS NOT NULL THEN 1 ELSE 0 END) as publish_cnt
				from ".TBL_SHOP_CUPON." c
				left join ".TBL_SHOP_CUPON_PUBLISH." cp on c.cupon_ix = cp.cupon_ix
				$where 
				group by cupon_ix
				order by regdate desc 
				LIMIT $start, $max ";

	$db->query($sql);

	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);

		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/cupon/cupon_".$db->dt[cupon_ix].".gif")){
			$cupon_img = "<img src='".$admin_config[mall_data_root]."/images/cupon/cupon_".$db->dt[cupon_ix].".gif' border='0' width='140' name='previews'>";
		}else{
			$cupon_img = "";
		}
		
		/*
		if($db->dt[cupon_div]=="G"){
			$cupon_div="상품";
		}elseif($db->dt[cupon_div]=="D"){
			$cupon_div="배송비";
		}elseif($db->dt[cupon_div]=="R"){
			$cupon_div="적립금";
		}elseif($db->dt[cupon_div]=="P"){
			$cupon_div="포인트";
		}else{
			$cupon_div="-";
		}
		*/

		if($db->dt[cupon_use_div]=="G"){
			$cupon_use_div="일반";
		}elseif($db->dt[cupon_use_div]=="D"){
			$cupon_use_div="중보쿠폰";
		}elseif($db->dt[cupon_use_div]=="C"){
			$cupon_use_div="C/S쿠폰";
		}elseif($db->dt[cupon_use_div]=="M"){
			$cupon_use_div="모바일쿠폰";
		}elseif($db->dt[cupon_use_div]=="P"){
			$cupon_use_div="회원패키지쿠폰";
		}else{
			$cupon_use_div="-";
		}

		$Contents .= "
		  <!--- // 목록 반복 시작 ---------->
		  <tr>
		    <td height='10'></td>
		  </tr>
		  <tr>
		    <td>
		      <table width='100%' border='0' cellpadding='15' cellspacing='0' style='border:1px solid #E9E9E9;' bgcolor=#FAFAFA>
		        <tr >
		          <td class='con_l'>
		            <table width='100%' border='0' cellpadding='20' cellspacing='1' bgcolor='#E9E9E9'>
		              <tr bgcolor='white'>
		                <td class='con_l' rowspan=2 width='30%'>".$cupon_img."</td>
		                <td class='con_l' width='30%'> 
							<b style='line-height:160%;font-size:13px;'>
							<img src='../image/ico_dot.gif' border=0> (쿠폰종류) 쿠폰명 : (".$_COUPON_KIND[$db->dt[cupon_div]].")".$db->dt[cupon_kind]." <!--".$db->dt[publish_cnt]." : ".$db->dt[cupon_ix]."--><br>
							<img src='../image/ico_dot.gif' border=0> 쿠폰사용구분 : ".$_COUPON_KIND[$db->dt[cupon_div]]."<br>
							<img src='../image/ico_dot.gif' border=0> (할인적용가) 할인적용 : ".number_format($db->dt[cupon_sale_value])." ".($db->dt[cupon_sale_type] == "1" ? "%":"원")." - 본사부담율 ".number_format($db->dt[haddoffice_rate])." ".($db->dt[cupon_sale_type] == "1" ? "%":"원")." , 셀러담율 ".number_format($db->dt[seller_rate])." ".($db->dt[cupon_sale_type] == "1" ? "%":"원")."<br>
							<img src='../image/ico_dot.gif' border=0> 사용여부 : ".($db->dt[is_use] == "1" ? "사용함":"미사용")." , 노출여부 : ".($db->dt[disp] == "1" ? "노출함":"노출안함")." <br>
							</b> 
		                </td>
		              </tr>
		              <tr bgcolor='white'>
		              	<td align=right>";

				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$Contents .= "<a href='cupon_modify.php?cupon_ix=".$db->dt[cupon_ix]."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border='0' ></a> ";
				}else{
					$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border='0' ></a> ";
				}
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
					if($db->dt[publish_cnt] > 0){
						$Contents .= "<a href=\"javascript:alert('해당쿠폰은 이미 사용중인 쿠폰입니다. 발행내역 및 발급 내역이 없을 경우 삭제 할 수 있습니다.');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border='0' ></a> ";
					}else{
						$Contents .= "<a href=\"javascript:DeleteCupon('".$db->dt[cupon_ix]."');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border='0' ></a> ";
					}
				}else{
					$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border='0' ></a> ";
				}
				$Contents .= "
		              	</td>
		              </tr>
		            </table>
		          </td>
		        </tr>
		      </table>
		    </td>
		  </tr>
		  <tr>
		    <td height='10'></td>
		  </tr>";
	}//for

$Contents .= "
	<tr>
    <td height='10'>
    <table width='100%' border='0' cellpadding='0' cellspacing='0'>
        <tr>
          <td align='center'>".page_bar($total, $page, $max,$HTTP_URL,'')."</td>
        </tr>
      </table>
    </td>
  </tr>";

}

$Contents .= "
</table>";



$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = promotion_menu();
$P->Navigation = "프로모션(마케팅)(마케팅) > 쿠폰관리 > 쿠폰목록";
$P->title = "쿠폰목록";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>