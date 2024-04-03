<?
include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");
$db = new Database();
$db2 = new Database();
$db3 = new Database();

$sql = "select id from shop_product where mandatory_type LIKE '02%' and id not in (select pid from mandatory_info_tmp) ";

$db->query($sql);
if($db->total > 0){
	for($i=0; $i < $db->total; $i++){
		$db->fetch($i);
		
		$sql = "select pmi_ix,pmi_code,pmi_title, pmi_desc from shop_product_mandatory_info where pid = '".$db->dt[id]."' order by pmi_ix asc";
		$db2->query($sql);

		if($db2->total){
			$no = 0;
			$mandatory_info = $db2->fetchall();
			//print_r($mandatory_info);
			for($z=0; $z < count($mandatory_info); $z++){
				
				
				$pmi_code = explode('|',$mandatory_info[$z][pmi_code]);
				if(count($pmi_code) == 3){
					$mi_ix = $pmi_code[0];
					$detail_code = $pmi_code[2];
				}else if(count($pmi_code) == 2){
					$mi_ix = $pmi_code[0];
					$detail_code = $pmi_code[1];
				}
			//	echo str_pad(substr($cid,0,($j+1)*3),"15","0",STR_PAD_RIGHT);
				$pmi_code = str_pad($mi_ix,"2","0",STR_PAD_LEFT)."|".$detail_code;
				$sql = "select mid_title from shop_mandatory_detail where mi_ix = '".$mi_ix."' and detail_code = '".$detail_code."' ";
				$db3->query($sql);
				$db3->fetch();
				
				$pmi_title = $db3->dt[mid_title];

				$data[$no][pmi_ix] = $mandatory_info[$z][pmi_ix];
				$data[$no][pmi_title] = $pmi_title;
				
				if($no > 1){
					if($no == 2){
						$data[$no][pmi_desc] = "발길이: ".$mandatory_info[$z][pmi_desc];
					}else if($no == 3){
						$data[$no-1][pmi_desc] .= " 굽높이: ".$mandatory_info[$z][pmi_desc];
						
					}else if($no > 3){
						$data[$no-1][pmi_desc] = $mandatory_info[$z][pmi_desc];
						
					}else{
						$data[$no][pmi_desc] = $mandatory_info[$z][pmi_desc];
					}
				}else{
					$data[$no][pmi_desc] = $mandatory_info[$z][pmi_desc];
				}
				$data[$no][pmi_code] = $pmi_code;
				$no++;
			}
			
			for($z=0; $z < count($data); $z++){
				if($data[$z][pmi_ix]){
				$sql = "update shop_product_mandatory_info set pmi_code = '".$data[$z][pmi_code]."', pmi_title = '".$data[$z][pmi_title]."', pmi_desc = '".$data[$z][pmi_desc]."' where pmi_ix = '".$data[$z][pmi_ix]."' ";
				$db3->query($sql);

				}
					
			}
			$sql = "insert into mandatory_info_tmp (pid) values ('".$db->dt[id]."') ";
			$db3->query($sql);
		}
		
		

	}
}
?>