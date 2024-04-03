function changeImageInputMode(pid, mode){	
	if(mode == 1){
		document.getElementById('img_all_table_'+pid).style.display = "block";	
		document.getElementById('img_one_table_'+pid).style.display = "none";			
	}else{
		document.getElementById('img_all_table_'+pid).style.display = "none";	
		document.getElementById('img_one_table_'+pid).style.display = "block";	
	}
}