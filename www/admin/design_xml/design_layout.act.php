<?php
	include("../../class/database.class");
	include('./design.common.php');
	
	define_syslog_variables();
	openlog("phplog", LOG_PID , LOG_LOCAL0);
	
	$db = new Database;
	
	
	syslog(LOG_INFO, "design_layout BEGIN");
	
	if ($layout_act == "update"){
		
		if($basic_design){
			
			$db->query("select * from ".TBL_SHOP_DESIGN." where pcode='basic' and mall_ix='$mall_ix' ");
			
			if($db->total){
				$sql = "	update ".TBL_SHOP_DESIGN." set 
						layout='$layout',header1='$header1',header2='$header2',leftmenu='$leftmenu',rightmenu='$rightmenu',footer1='$footer1',footer2='$footer2' 
						where pcode='basic' and mall_ix='$mall_ix'	";
				
				$db->query($sql);
			}else{
				$sql = "insert into shop_design(pcode,mall_ix,page_type,layout,header1,header2,leftmenu,contents,rightmenu,footer1,footer2,regdate) 
				values('basic','$mall_ix','B','$layout','$header1','$header2','$leftmenu','$contents','$rightmenu','$footer1','$footer2',NOW())";
				
				$db->query($sql);
			}
		}
	
		for($i=0;$i<count($pcode);$i++){
			$db->query("select * from ".TBL_SHOP_DESIGN." where pcode='".str_replace("_","",$pcode[$i])."' and mall_ix='$mall_ix' ");
			
			if($db->total){
				$sql = "	update ".TBL_SHOP_DESIGN." set 
						layout='$layout',header1='$header1',header2='$header2',leftmenu='$leftmenu',rightmenu='$rightmenu',footer1='$footer1',footer2='$footer2' 
						where pcode='".str_replace("_","",$pcode[$i])."' and mall_ix='$mall_ix'	";
				//page_link='$page_link',page_title='$page_title',page_path='$page_path',page_help='$page_help',page_addscript='$page_addscript',page_body='$page_body',page_desc='$page_desc',	
				//echo $sql;		
			}else{
				$sql = "insert into ".TBL_SHOP_DESIGN."(pcode,mall_ix,page_type,layout,header1,header2,leftmenu,contents,rightmenu,footer1,footer2,regdate) 
				values('".str_replace("_","",$pcode[$i])."','$mall_ix','B','$layout','$header1','$header2','$leftmenu','$contents','$rightmenu','$footer1','$footer2',NOW())";
			}
			$db->query($sql);
			
		}
		
		updateLayoutXML($mall_ix);
		
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('레이아웃이 정상적으로 수정되었습니다.');</script>");
		echo("<script>parent.document.location.reload();</script>");
	}
	
	syslog(LOG_INFO, "design_layout END");
closelog();
?>
