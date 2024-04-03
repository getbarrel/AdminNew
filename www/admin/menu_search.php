<?

if($search_type == 'bname' || $search_type == 'rname' || $search_type == 'deliverycode'){
	echo "<script>
		location.href='/admin/order/orders.list.php?search_type=$search_type&search_text=$search_text&date_type=o.date&orderdate=1&startDate=&endDate=&company_id=&com_name=&md_code=&md_name=&x=19&y=17';
	</script>";
}else if($search_type == 'name' || $search_type == 'id'){
	echo "<script>
		location.href='/admin/member/member.php?mc_ix=+&search_type=$search_type&search_text=$search_text&gp_ix=&nationality=&mem_type=&region=&mem_div=&mailsend_yn=A&smssend_yn=A&mileage=&point=&x=-880&y=-498';
	</script>";
}else if($search_type == 'pname' || $search_type == 'pid'){
	if($search_type == 'pid'){
		$search_type = 'id';
	}
	echo "<script>
		location.href='/admin/product/product_list.php?mode=search&cid2=&depth=&sprice=0&eprice=1000000&cid0_1=&cid1_1=&cid2_1=&cid3_1=&company_id=&product_type=&disp=&max=10&search_type=$search_type&search_text=$search_text&state2=&x=-881&y=-443';
	</script>";
}

?>