<?
session_start();



?>


var language = "<?=$admininfo[language]?>";

function show_alert(message, event_function){

	if(language == "english"){
		alert('It has been processed successfully.!! ');
	}else if(language == "indonesian"){
		alert('Proses ini telah ditangani dengan baik ');
	}else{
		//alert(message);
		
		//$('#show_alert_box',window.parent.document).html(message);
		//window.parent.document.getElementById('show_alert_box').innerHTML = message;

		window.parent.ViewAlertBox(message, event_function);

	}
	//parent.unblockLoadingBox();
}

function unblockLoadingBox(){
	setTimeout($.unblockUI, 100); 
}

function show_alert2(message){
	if(language == "english"){
		alert('It has been processed successfully.!! ');
	}else if(language == "indonesian"){
		alert('Proses ini telah ditangani dengan baik ');
	}else{
		alert(message);
	}
}
