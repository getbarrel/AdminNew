<?

//include("../logstory/include/commerce.lib.php");
include("../logstory/include/util.php");

function claimByDateReportTable($vdate,$groupbytype="day",$SelectReport=1){
	global $startDate, $endDate,$order_select_status_div,$b_status,$f_status;

	$fordb = new Database();
	
	$info_array= $order_select_status_div["A"][$b_status][$f_status];
	//print_r($info_array);

	if($SelectReport == ""){
		$SelectReport = 1;
	}

	if($depth == ""){
		$depth = 0;
	}else{
		$depth = $depth+1;
	}


	if($vdate == ""){
		$vdate = date("Ymd", time());
		$vyesterday = date("Ymd", time()-84600);
		$voneweekago = date("Ymd", time()-84600*7);
	}else{
		if($SelectReport ==3){
			$vdate = $vdate."01";
		}
		$vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
		$vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
		$voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
	}

	$title_div_array = array();
	$title_div_cnt = array();
	$n=0;
	$b=0;
	$s=0;
	$cnt_total=0;
	foreach($info_array as $key => $val){
		
		if($val["type"]=="N"){//아무도 책임 없음
			$title_div_array["N"][$n]["title"]=$val["title"];
			$title_div_array["N"][$n]["code"]=$key;
			$title_div_cnt["N"]["title"]="책임없음";
			$title_div_cnt["N"]["cnt"]++;
			$code_implode["N"] .= ",'".$key."'";
			$n++;
		}elseif($val["type"]=="B"){//구매자 귀책사유
			$title_div_array["B"][$b]["title"]=$val["title"];
			$title_div_array["B"][$b]["code"]=$key;
			$title_div_cnt["B"]["title"]="구매자귀책";
			$title_div_cnt["B"]["cnt"]++;
			$code_implode["B"] .= ",'".$key."'";
			$b++;
		}elseif($val["type"]=="S"){//판매자 귀책사유
			$title_div_array["S"][$s]["title"]=$val["title"];
			$title_div_array["S"][$s]["code"]=$key;
			$title_div_cnt["S"]["title"]="판매자귀책";
			$code_implode["S"] .= ",'".$key."'";
			$title_div_cnt["S"]["cnt"]++;
			$s++;
		}

		$cnt_total++;
	}
	
	$l=0;
	
	$sql="select ";
	$sql.="date_format(os.regdate,'%Y%m%d') as vdate , ";
	$sql.="sum(case when os.reason_code != '' then '1' else '0' end) as total_cnt ,";

		foreach($title_div_cnt as $key => $val){

			$sql.="sum(case when os.reason_code in (".substr($code_implode[$key],1).") then '1' else '0' end) as total_".$key."_cnt, ";

			for($i=0;$i<count($title_div_array[$key]);$i++){
				if($l==($cnt_total-1))		$sql.="sum(case when os.reason_code = '".$title_div_array[$key][$i]["code"]."' then '1' else '0' end) as ".$title_div_array[$key][$i]["code"]."";
				else								$sql.="sum(case when os.reason_code = '".$title_div_array[$key][$i]["code"]."' then '1' else '0' end) as ".$title_div_array[$key][$i]["code"].",";
				$l++;
			}
		}

	$sql.="
			from 
				shop_order_status os 
			where 
					os.status = '".$f_status."' 
				and 
					reason_code !='' ";
			if($groupbytype=="day"){
				$sql.="
				and
					os.regdate between '".substr($vdate,0,4)."-".substr($vdate,4,2)."-01 00:00:00' and '".substr($vdate,0,4)."-".substr($vdate,4,2)."-31 23:59:59'";
			}else{
				$sql.="
				and
					os.regdate between '".substr($startDate,0,4)."-".substr($startDate,4,2)."-".substr($startDate,6,2)." 00:00:00' and '".substr($endDate,0,4)."-".substr($endDate,4,2)."-".substr($endDate,6,2)." 23:59:59' ";
			}
			$sql.="
			group by 
				date_format(os.regdate,'%Y%m%d')
	";
	

	$dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");

	if($sql){
		$fordb->query($sql);
	}

	if($groupbytype=="day"){
		$mstring = "<table width='100%' border=0>
						<tr><td>".TitleBar("사유별 분석 : ".($cid ? getCategoryPath($cid,4):""),$dateString)."</td><td align=right>(단위:원) </td></tr>
					</table>";
	}
	

	$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>
						<col width='2%'>
						<col width='4%'>";

						for($i=0;$i<($cnt_total+count($title_div_cnt));$i++){
							$mstring .= "
							<col width='4%'>";
						}

	$mstring .= "
		<tr height=30>
			<td class=s_td rowspan='2'>일자</td>
			<td class=m_td rowspan='2'>총합계</td>";
	foreach($title_div_cnt as $key => $val){
		$mstring .= "
				<td class=m_td colspan='".($val["cnt"]+1)."'>".$val["title"]."</td>";
	}
	$mstring .= "
		</tr>

		<tr height=30>";
		foreach($title_div_cnt as $key => $val){
			for($i=0;$i<count($title_div_array[$key]);$i++){
				if($i==0){
					$mstring .= "
					<td class=m_td>계/점유율</td>";
				}
				$mstring .= "
				<td class=m_td>".$title_div_array[$key][$i]["title"]."</td>";
			}
		}
		$mstring .= "
		</tr>";


	for($i=0;$i<$fordb->total;$i++){
		$fordb->fetch($i);

		foreach($title_div_cnt as $key => $val){

			$_str="sum_total_".$key."_cnt";
			$$_str += $fordb->dt["total_".$key."_cnt"];

			for($j=0;$j<count($title_div_array[$key]);$j++){

				$_str=$title_div_array[$key][$j]["code"]."_sum";
				$$_str += returnZeroValue($fordb->dt[$title_div_array[$key][$j]["code"]]);

			}
		}

		$total_cnt_sum += returnZeroValue($fordb->dt[total_cnt]);
	}
	

	if ($total_cnt_sum == 0){
		if($groupbytype=="day"){
			$mstring .= "<tr  align=center height=200><td colspan='".($cnt_total+2+count($title_div_cnt))."' class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
		}else{
			$mstring .= "<tr  align=center height=200><td colspan='".($cnt_total+2+count($title_div_cnt))."' class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
		}
	}

	if($total_cnt_sum != 0){
		if($groupbytype=="day"){
			$mstring .= "<tr height=25 align=right>
			<td class=s_td align=center>합계</td>
			<td class='e_td number' style='padding-right:10px;'>".number_format($total_cnt_sum,0)."/100%</td>";
				foreach($title_div_cnt as $key => $val){
					for($j=0;$j<count($title_div_array[$key]);$j++){

						if($j==0){
							$_str="sum_total_".$key."_cnt";
							$mstring .= "<td class='e_td'>".number_format($$_str)."/".number_format($$_str/$total_cnt_sum*100,1)."%</td>";
						}

						$_str=$title_div_array[$key][$j]["code"]."_sum";
						$mstring .= "
						<td class='e_td number' style='padding-right:10px;'>".number_format($$_str,0)."</td>";
					}
				}
			$mstring .= "
			</tr>\n";
		}else{
			$mstring .= "<tr height=25 align=right>
			<td class=s_td align=center>합계</td>
			<td class='e_td point' style='padding-right:10px;'>".number_format($total_cnt_sum,0)."/100%</td>";
				foreach($title_div_cnt as $key => $val){
					for($j=0;$j<count($title_div_array[$key]);$j++){

						if($j==0){
							$_str="sum_total_".$key."_cnt";
							$mstring .= "<td class='e_td'>".number_format($$_str)."/".number_format($$_str/$total_cnt_sum*100,1)."%</td>";
						}

						$_str=$title_div_array[$key][$j]["code"]."_sum";
						$mstring .= "
						<td class='number' style='padding-right:10px;'>".number_format($$_str,0)."</td>";
					}
				}
			$mstring .= "
			</tr>\n";
		}
	}

	for($i=0;$i<$fordb->total;$i++){
		$fordb->fetch($i);

		if($groupbytype=="day"){
			$week_num = date("w",mktime(0,0,0,substr($fordb->dt[vdate],4,2),substr($fordb->dt[vdate],6,2),substr($fordb->dt[vdate],0,4)));

			$mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
			<td class='list_box_td point' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".getNameOfWeekday($week_num, $fordb->dt[vdate],"priodname")." </td>";

			$mstring .= "<td class='list_box_td number blue_point str' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[total_cnt])."/".number_format($fordb->dt[total_cnt]/$total_cnt_sum*100,1)."%</td>";


			foreach($title_div_cnt as $key => $val){
				for($j=0;$j<count($title_div_array[$key]);$j++){
					if($j==0){
						$_str="sum_total_".$key."_cnt";
						if($$_str > 0)
							$mstring .= "<td class='list_box_td point'>".number_format($fordb->dt["total_".$key."_cnt"])."/".number_format($fordb->dt["total_".$key."_cnt"]/$$_str*100,1)."%</td>";
						else
							$mstring .= "<td class='list_box_td point'>0/0.0%</td>";
					}
					$mstring .= "<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[$title_div_array[$key][$j]["code"]],0)."&nbsp;</td>";
				}
			}

			$mstring .= "
			</tr>\n";
		}
	}

	$mstring .= "</table>\n";

	if($groupbytype=="day"){
		$mstring .= "<table width='100%'><tr><td> </td><td align=right style='padding-top:10px;'>VAT 포함</td></tr></table>";

		/*
		$help_text = "
		<table>
			<tr>
				<td style='line-height:150%'>
				- 카테고리별 상품조회 회수를 바탕으로 귀사 사이트의 인기카테고리와 비인기 카테고리를 정확히 파악하여 그에 맞는 운영및 마케팅 정책을 수립 수행할수 있습니다<br>
				- 좌측 카테고리를 클릭하면 하부 카테고리에 대한 상세 정보가 표시 됩니다<br><br>
				</td>
			</tr>
		</table>
		";*/

		$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B' );


		$mstring .= HelpBox("일별매출(종합)", $help_text);
	}
	return $mstring;
}

?>