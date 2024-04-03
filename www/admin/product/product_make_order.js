function PrintMakeOrder(pid, standard){
	//alert(standard[standard.selectedIndex].text);
	try{
		if(standard.value == 0){
			alert(language_data['product_make_order.js']['A'][language]);
			//'규격을 선택해주세요'
		}else{
			PrintWindow("./product_make_order_print.php?id="+pid+"&standard="+standard[standard.selectedIndex].text,1120,730,'print_stock');
		}
	}catch(E){
		PrintWindow("./product_make_order_print.php?id="+pid,1120,730,'print_stock');	
	}
}

function setCategory(cname,cid,depth,pid){
	document.frames['act'].location.href='./product_make_order.php?cid='+cid+'&depth='+depth+'&view=innerview';
}
