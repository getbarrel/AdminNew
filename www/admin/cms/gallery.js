//document.write("<scr"+"ipt type=\"text/javascript\" src=\"http://www.google.com/jsapi\"><\/scr"+"ipt>");
//document.write("<scr"+"ipt type=\"text/javascript\">google.load('jquery','1.4.2');<\/scr"+"ipt>");
//document.write("<scr"+"ipt type=\"text/javascript\" src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js\"><\/scr"+"ipt>");
(function () {
    //var PATH = "http://220.73.178.124/VE/js/";      // the path to the scripts, relative to HTML page
    var SCRIPTS = [         // the script filenames, in dependency order
            "https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js",
            "https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/jquery-ui.min.js"
        ];
	
    var html = [];
	document.write('<div id="VEScontainer" style="width:100%;height:100%"></div>');
    for (var i = 0; i < SCRIPTS.length; i++) {
        html.push('<script type="text/javascript" src="');
        //html.push(PATH);
        html.push(SCRIPTS[i]);
        html.push('"></script>\n');
    }
	
    document.write(html.join(''));

	//alert($);
})();

function GelleryView(display_type, dgi_ix){
	
	//$.getJSON("http://api.flickr.com/services/feeds/photos_public.gne?jsoncallback=?", 
	$.getJSON("http://dev.forbiz.co.kr/admin/deepzoom/gallery.json.php", 
	{ 
	dgi_ix: dgi_ix,
	format:'json'
	}, 
	function(datas) { 

	$.each(datas, function(i,item){ 
	//alert(item.src);
	  $("<img/>").attr("src", item.src).appendTo("<div/>").css('margin','3px').appendTo("#VEScontainer"); 
	  //if ( i == 3 ) return false; 
	}); 
	})

}



function GelleryView2(display_type, dgi_ix){
	//alert($.getJSON);
	$.getJSON("http://api.flickr.com/services/feeds/photos_public.gne?jsoncallback=?", 
	{ 
	tags: "cat", 
	tagmode: "any", 
	format: "json" 
	}, 
	function(data) { 
	//	alert(data);

	$.each(data.items, function(i,item){ 
	  $("<img/>").attr("src", item.media.m).appendTo("<div/>").css('margin','3px').appendTo("#VEScontainer"); 
	  if ( i == 3 ) return false; 
	}); 
	})

}



