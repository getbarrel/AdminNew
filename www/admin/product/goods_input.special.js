function loadManufacturer(sel,target) {
	
	var trigger = sel.value;
	var form = sel.form.name;
	//alert(trigger);
	//var depth = sel.getAttribute('depth');
	//document.write('manufacturer.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2');
	window.frames['act'].location.href = 'manufacturer.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';
	
}

function loadModel(sel,target) {
	
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	
	var depth = sel.getAttribute('depth');
	//document.write('model.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2');
	window.frames['act'].location.href = 'model.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';
	
}


function loadGrade(sel,target) {
	
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	
	var depth = sel.getAttribute('depth');
	//document.write('model.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2');
	window.frames['act'].location.href = 'grade.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';
	
}


function loadRegion(sel,target) {
	
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	
	var depth = sel.getAttribute('depth');
	//document.write('region.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2');
	window.frames['act'].location.href = 'region.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';
	

}


function ChangeModel(select_name, vechile_div, selected){
	// alert(obj.value);
	$.ajax({ 
		type: 'GET', 
		data: {'act': 'json','vechile_div': vechile_div, 'rand':Math.random()},  
		url: './model.act.php',  
		dataType: 'json', 
		async: true, 
		cache:false,
		beforeSend: function(){ 
			//$('#loading').show();
			//$.blockUI.defaults.css = {}; 
			//$.blockUI({ message: $('#loading'), css: {backgroundColor:'transparent',  width: '100px' , height: '100px' ,padding:  '10px'} });  
			
		},  
		success: function(datas){ 
			//var md_ix;
			
			//alert(datas);
		
			if(datas != null){
				var select = $('select#md_ix');
				select.empty(); 
				var option = $('<option />');
				option.attr('value', '')
                              .html(select_name)
                              .appendTo(select);
				//alert(selected);
				$.each(datas, function(i, data){
                        var option = $('<option />');
						//alert(data);
						if(data.md_ix == selected){
							 option.attr('value', data.md_ix).attr('selected', 'selected')
                              .html(data.model_name)
                              .appendTo(select);
						}else{
							 option.attr('value', data.md_ix)
                              .html(data.model_name)
                              .appendTo(select);
						}
                       
                });
				//alert(select.html());

			}
			//$.unblockUI(); 
		
		} 
	}); 
 }


 function ChangeManufacturer(select_name, vechile_div, selected){
	// alert(obj.value);
	$.ajax({ 
		type: 'GET', 
		data: {'act': 'json','vechile_div': vechile_div, 'rand':Math.random()},  
		url: './manufacturer.act.php',  
		dataType: 'json', 
		async: true, 
		cache:false,
		beforeSend: function(){ 
			//$('#loading').show();
			//$.blockUI.defaults.css = {}; 
			//$.blockUI({ message: $('#loading'), css: {backgroundColor:'transparent',  width: '100px' , height: '100px' ,padding:  '10px'} });  
			
		},  
		success: function(datas){ 
			//var md_ix;
			
			//alert(datas);
		
			if(datas != null){
				var select = $('select#mf_ix');
				select.empty(); 
				var option = $('<option />');
				option.attr('value', '')
                              .html(select_name)
                              .appendTo(select);
				//alert(selected);
				$.each(datas, function(i, data){
                        var option = $('<option />');
						//alert(data);
						if(data.mf_ix == selected){
							 option.attr('value', data.mf_ix).attr('selected', 'selected')
                              .html(data.manufacturer_name)
                              .appendTo(select);
						}else{
							 option.attr('value', data.mf_ix)
                              .html(data.manufacturer_name)
                              .appendTo(select);
						}
                       
                });
				//alert(select.html());

			}
			//$.unblockUI(); 
		
		} 
	}); 
 }

  function ChangeVechileType(select_name, vechile_div, selected){
	// alert(obj.value);
	$.ajax({ 
		type: 'GET', 
		data: {'act': 'json','vechile_div': vechile_div, 'rand':Math.random()},  
		url: './vechile_type.act.php',  
		dataType: 'json', 
		async: true, 
		cache:false,
		beforeSend: function(){ 
			//$('#loading').show();
			//$.blockUI.defaults.css = {}; 
			//$.blockUI({ message: $('#loading'), css: {backgroundColor:'transparent',  width: '100px' , height: '100px' ,padding:  '10px'} });  
		},  

		success: function(datas){ 
			//var md_ix;
			
			if(datas != null){
				var select = $('select#vt_ix');
				select.empty(); 
				var option = $('<option />');
				option.attr('value', '')
                              .html(select_name)
                              .appendTo(select);
				//alert(selected);
				$.each(datas, function(i, data){
                        var option = $('<option />');
						//alert(data);
						if(data.vt_ix == selected){
							 option.attr('value', data.vt_ix).attr('selected', 'selected')
                              .html(data.vechiletype_name)
                              .appendTo(select);
						}else{
							 option.attr('value', data.vt_ix)
                              .html(data.vechiletype_name)
                              .appendTo(select);
						}
                       
                });
				//alert(select.html());

			}
			//$.unblockUI(); 
		
		} 
	}); 
 }
 