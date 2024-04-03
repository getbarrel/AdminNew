
function setCategory(cname,cid,depth,pid){
	document.frames['act'].location.href='./product_stock.php?cid='+cid+'&depth='+depth+'&view=innerview';
}

function changeStock(id){
	var frm = document.stockfrm;
	var stock_value = eval('frm.stock'+id+'.value');
	if(stock_value != "")
		stock_value = parseInt(stock_value);	
		
	var bstock_value = eval('frm.bstock'+id+'.value');
	if(bstock_value != "")
		bstock_value = parseInt(bstock_value);	
		
	var incom_value = eval('frm.incom'+id+'.value');
	if(incom_value != "")
		incom_value = parseInt(incom_value);	
		
	var obj_stock = eval('frm.stock'+id);
	obj_stock.value =bstock_value + incom_value;
}


function changeStockByOption(id, option_id){
	var frm = document.stockfrm;
	var option_stock_value = eval('frm.option_stock'+id+'_'+option_id+'.value');
	if(option_stock_value != "")
		option_stock_value = parseInt(option_stock_value);	
		
	var option_bstock_value = eval('frm.option_bstock'+id+'_'+option_id+'.value');	
	if(option_bstock_value != "")
		option_bstock_value = parseInt(option_bstock_value);	
		
	var option_incom_value = eval('frm.option_incom'+id+'_'+option_id+'.value');
	if(option_incom_value != "")
		option_incom_value = parseInt(option_incom_value);	
		
	var option_obj_stock = eval('frm.option_stock'+id+'_'+option_id);
	option_obj_stock.value =option_bstock_value + option_incom_value;
}


function calcurateSafeStockByOption(pid){
	var obj = eval("document.all._option_safestock"+pid);
	var sum_option_safestock = 0;
	
	for(i=0;i < obj.length;i++){
		sum_option_safestock = sum_option_safestock + parseInt(obj[i].value);
	}
	
	var safestock = eval("document.all.safestock"+pid);
	safestock.value = sum_option_safestock;
}


function calcurateStockByOption(pid){	
	var obj = eval("document.all._option_stock"+pid);
	var sum_option_stock = 0;
	
	for(i=0;i < obj.length;i++){
		sum_option_stock = sum_option_stock + parseInt(obj[i].value);
	}
	
	var stock = eval("document.all.stock"+pid);
	stock.value = sum_option_stock;
}