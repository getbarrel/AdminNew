function loadPlaceSection(sel,target,mode,section_type) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	var depth = sel.getAttribute('depth');
	var h_type = $('#h_type').val(); // 유형

	//document.write('inventory_placesection.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
	if(h_type == "OW"){
		$('#now_ps_ix').attr('disabled',true);
	}else{
		$('#now_ps_ix').attr('disabled',false);
	}

	if(mode=='multiple'){
		window.frames['iframe_act'].location.href = 'inventory_placesection.load.php?mode=multiple&form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target+'&h_div=1&h_type=' + h_type + '&section_type=' + section_type ;
	}else{
		if(sel.selectedIndex!=0) {
			window.frames['iframe_act'].location.href = 'inventory_placesection.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target+'&h_div=1&h_type=' + h_type + '&section_type=' + section_type ;
		}

	}
}

function MoveloadPlaceSection(sel,target) {
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	var depth = sel.getAttribute('depth');
	var h_type = $('#h_type').val(); // 유형

	//document.write('inventory_placesection.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
	if(h_type == "OW"){
		$('#move_ps_ix').attr('disabled',true);
	}else{
		$('#move_ps_ix').attr('disabled',false);
	}
	if(sel.selectedIndex!=0) {
		window.frames['act'].location.href = 'inventory_placesection.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target+'&h_div=2&h_type=' + h_type;
	}
}

function loadPlace(sel,target,mode) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	var depth = sel.getAttribute('depth');

	//document.write('inventory_placesection.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
	
	if(mode=='multiple'){
		window.frames['iframe_act'].location.href = '../inventory/inventory_place.load.php?mode=multiple&form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	}else{
		if(sel.selectedIndex!=0) {
			window.frames['iframe_act'].location.href = '../inventory/inventory_place.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		}
	}
}

function MoveloadPlace(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	var depth = sel.getAttribute('depth');

	//document.write('inventory_placesection.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);

	if(sel.selectedIndex!=0) {
		window.frames['act'].location.href = 'inventory_place.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	}

}


function getPlaceData(obj){
	var place_data="";

	$.ajax({ 
		type: 'POST', 
		data: {'act': 'get_place_data_json', 'pi_ix':obj.val()},
		url: './inventory_place.load.php',  
		dataType: 'json', 
		async: false, 
		beforeSend: function(){ 
				//alert(1);
		},  
		success: function(data){ 
			place_data = data;
		} 
	}); 

	return place_data;
}

function loadPlaceData(sel, shared_pi){
	var pi_ix = $(sel).val();
	$.ajax({
		type: "POST",
		url: "/admin/inventory/inventory_place.load.php",
		data: ({
				act : "getPlaceData",
				pi_ix : pi_ix
		}),
		async: true,
		success:function(result){
			var list = JSON.parse(result);
			var newRows = "<option value=''>창고</option>";
			if (list.datas != null){
				$.each(list.datas, function (index, item) {
					if(shared_pi == item.pi_ix){
						var odCheck = "selected";
					}else{
						var odCheck = "";
					}
					newRows += "<option value=' " + item.pi_ix + " ' " + odCheck + "> " + item.place_name + " </option>";
				});
			}
			$("#regist_pi_ix").html(newRows);
		}
	});
}