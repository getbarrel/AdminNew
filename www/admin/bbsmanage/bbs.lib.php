<?

//read an especified file
if(!function_exists('load_template')){
	function load_template($strfile,$ar_files="") {
		global $language_file,$func,$textout;
	
		if($strfile == "" || !file_exists($strfile)) return;
		$thisfile = file($strfile);
	
		while(list($line,$value) = each($thisfile)) {
			$value = ereg_replace("(\r|\n)","",$value);
			$result .= "$value\r\n";
		}
	
	/*
		for($n=0;$n<count($ar_files);$n++) {
			
			$thisfile = $ar_files[$n].".txt";
	
			$lg = file("$language_file/$thisfile");
			while(list($line,$value) = each($lg)) {
				if(strpos(";#",$value[0]) === false && ($pos = strpos($value,"=")) != 0 && trim($value) != "") {
					$varname  = "<!--%".trim(substr($value,0,$pos))."%-->";
					$varvalue = trim(substr($value,$pos+1));
					$result = eregi_replace($varname,$varvalue,$result);
				}
			}
	
		}
		$func($textout);
	*/
		return $result;
	}
}

if(!function_exists('get_tags')){
	function get_tags($begin,$end,$template) {
		$beglen = strlen($begin);
		$endlen = strlen($end);
		$beginpos = strpos($template,$begin);
		$endpos = strpos($template,$end);
		$result["ab-begin"] = $beginpos;
		$result["ab-end"]   = $endpos+$endlen;
		$result["re-begin"] = $beginpos+$beglen;	
		$result["re-end"]   = $endpos;	
		$result["ab-content"] = substr($template,$beginpos,($endpos+$endlen)-$beginpos);
		$result["re-content"] = substr($template,$beginpos+$beglen,$endpos-$beginpos-$beglen);
		unset($beglen,$endlen,$beginpos,$endpos,$begin,$end,$template);
		return $result;
	}
}
function checkOwner($comp,$mem_ix){
	$mdb = new Database();
	
	return true;
	//$sql = "SELECT o.comp,o.mem_ix, o.togather_mem_ix from cardstory_service_order o where o.comp = '$comp' ";
	//echo $sql;
	
	$mdb->query($sql);
	$mdb->fetch();
	
	if($mem_ix != "" && ($mem_ix == $mdb->dt[mem_ix] || $mem_ix == $mdb->dt[togather_mem_ix])){
		return true;
	}else{
		return false;	
	}
}

function CheckNewContents($new,$templet_path="/bbs/bbs_templet/basic/"){
	if($new){
		return "<img src='".$templet_path."icon/icon_new.gif' align='texttop'>";	
	}
}

function bbs_page_bar($total, $page, $max,$bbs_list_url, $templet_path="/bbs/bbs_templet/basic/", $add_query=""){
	$page_string;
	global $cid,$depth,$category_load, $company_id;
	global $nset, $orderby;
	
	
	

	if ($total % $max > 0){
		$total_page = floor($total / $max) + 1;
	}else{
		$total_page = floor($total / $max);
	}

	$next = (($nset)*10+1);
	$prev = (($nset-2)*10+1);
	
	if ($nset == ""){
		$nset = 1;	
	}
	

	if ($total){
		$prev_mark = ($prev > 0) ? "<a href='".$bbs_list_url."?view=innerview&nset=".($nset-1)."&page=".(($nset-2)*10+1)."&cid=$cid&depth=$depth&category_load=$category_load&company_id=$company_id&orderby=$orderby$add_query' target=act><img src='".$templet_path."icon/pre10.gif' border=0 align=absmiddle></a> " : "<img src='".$templet_path."icon/pre10_b.gif' border=0 align=absmiddle> ";
		$next_mark = ($next < $total_page) ? "<a href='".$bbs_list_url."?view=innerview&nset=".($nset+1)."&page=".($nset*10+1)."&cid=$cid&depth=$depth&category_load=$category_load&company_id=$company_id&orderby=$orderby$add_query' target=act><img src='".$templet_path."icon/next10.gif' border=0 align=absmiddle></a>" :  " <img src='".$templet_path."icon/next10_b.gif' border=0 align=absmiddle>";
	}

	$page_string = $prev_mark;

//	for ($i = $page - 10; $i <= $page + 10; $i++)

	for ($i = ($nset-1)*10+1 ; $i <= (($nset-1)*10 + 10); $i++)
	{
		if ($i > 0)
		{
			if ($i <= $total_page)
			{
				if ($i != $page){
					$page_string = $page_string.(" <a href='".$bbs_list_url."?view=innerview&nset=$nset&page=$i&cid=$cid&depth=$depth&category_load=$category_load&company_id=$company_id&orderby=$orderby$add_query' target=act>$i</a> ");
				}else{
					$page_string = $page_string.("<font color=#FF0000>$i</font>");
				}
			}
		}
	}

	$page_string = $page_string.$next_mark;
	
	return $page_string;
}

function page_barX($total, $page, $max,$link_URL="plist.php",$add_query="")
{
	if ($listtype == "search"){
		return "";	
	}
	
	if($total == 0){
		$total = 1;	
	}
	
	$page_string;
	global $cid,$depth;

	if ($total % $max > 0)
	{
		$total_page = floor($total / $max) + 1;
	}
	else
	{
		$total_page = floor($total / $max);
	}

	$next = $page + 1;
	$prev = $page - 1;

	$pno10_prev = "<img src='/icon/pno_prev10.gif' onmouseover=\"pno_prev10.src='/icon/pno_prev10_on.gif'\" onmouseout=\"pno_prev10.src='/icon/pno_prev10.gif'\" border='0' align='absmiddle' name='pno_prev10'>";
	$pno10_next = "<img src='/icon/pno_next10.gif' onmouseover=\"pno_next10.src='/icon/pno_next10_on.gif'\" onmouseout=\"pno_next10.src='/icon/pno_next10.gif'\" border='0' align='absmiddle' name='pno_next10'>";

	if ($total)
	{
		$prev_mark = ($prev > 0) ? "$pno10_prev<a href='$link_URL?page=$prev&cid=$cid&depth=$depth&listtype=$listtype".$add_query."'><img src='/icon/pno_prev.gif' onmouseover=\"pno_prev.src='/icon/pno_prev_on.gif'\" onmouseout=\"pno_prev.src='/icon/pno_prev.gif'\"' border='0' align='absmiddle' name='pno_prev'></a>" : "$pno10_prev<img src='/icon/pno_prev.gif' border='0' align='absmiddle' name='pno_prev'>";
		$next_mark = ($next <= $total_page) ? "<a href='$link_URL?page=$next&cid=$cid&depth=$depth&listtype=$listtype".$add_query."'><img src='/icon/pno_next.gif' onmouseover=\"pno_next.src='/icon/pno_next_on.gif'\" onmouseout=\"pno_next.src='/icon/pno_next.gif'\" border='0' align='absmiddle' name='pno_next'></a>$pno10_next" :  "<img src='/icon/pno_next.gif' border='0' align='absmiddle' name='pno_next'>$pno10_next";
	}

	$page_string = $prev_mark;

	for ($i = $page - 5; $i <= $page + 5; $i++)
	{
		if ($i > 0)
		{
			if ($i <= $total_page)
			{
				if ($i != $page)
				{
					$page_string = $page_string.("<a href=$link_URL?page=$i&cid=$cid&depth=$depth&listtype=$listtype".$add_query."><img src='/icon/pno_$i.gif' onmouseover=\"pno_$i.src='/icon/pno_".$i."_on.gif'\" onmouseout=\"pno_$i.src='/icon/pno_$i.gif'\" border='0' align='absmiddle' name='pno_$i'></a>");
				}
				else
				{
					//$page_string = $page_string.("<font color=#FF0000>$i</font> ");
					$page_string = $page_string.("<img src='/icon/pno_".$i."_on.gif' border='0' align='absmiddle' name='pno_$i'>");
					
				}
			}
		}
	}

	$page_string = $page_string.$next_mark;
	
	return $page_string;//." <a href=plist.php?page=$i&cid=$cid&depth=$depth&listtype=all>view all</a>";
}
?>