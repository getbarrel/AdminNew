if(phantom.args.length === 0){
	phantom.exit();
}
var page = require('webpage').create(), url;
var url = phantom.args[0];

page.onError = function (msg, trace) {
    //console.log(msg);
    trace.forEach(function(item) {
	//console.log('  ', item.file, ':', item.line);
    })
}
page.onConsoleMessage = function (msg) {
	//console.log(msg);
}
page.includeJs("http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js", function() {
    // jQuery is loaded, now manipulate the DOM
});
page.open(url, function (status) {
    var result = page.evaluate(function () {
	
        function Stock(){
            this.sizeName = "";
            this.availableColorText = "";
            
            this.test1 = "";
            this.test2 = "";
            this.test3 = "";
            this.test4 = "";
        }
        
        function Jcrew(){
            
            this.size = new Array();
        }
            
        var jcrew = new Jcrew();
        //jcrew.name = document.getElementById('productColor0').innerHTML;
        var arraySizeLength = parseInt(window.element.options.length);
        
        
        jcrew.test1 = document.getElementById('productColor0').innerHTML;
        jcrew.test3 = $('#sizeSelect0').val();
        document.getElementById("sizeSelect0")[1].selected = true;
        var obj = document.getElementById('sizeSelect0');
        getProductDetail(obj, 'size');
        jcrew.test2 = document.getElementById('productColor0').innerHTML;
        jcrew.test4 = $('#sizeSelect0').html();
        /*
        for(i = 1; i < arraySizeLength; i++){
			var stock = new Stock();
			stock.sizeName = window.element.options[i].value;
            document.getElementById("sizeSelect0")[i].selected = true;
            var obj = document.getElementById('sizeSelect0');
            //var obj = $('#sizeSelect0');
             stock.test = obj;
             //getProductDetail(obj, 'size');
             /*
            if(getProductDetail(obj, 'size')){
                stock.availableColorText = document.getElementById('productColor0').innerHTML;
            }else{
                stock.test = "false";
            }
        
            try{
                getProductDetail(obj, 'size');
            }catch(e){
                console.log(e);
            };
            
            
            
            
           // jcrew.size.push(stock);

        }
        */
        return jcrew;
    });
	//console.log(serialize(result));
	console.log(JSON.stringify(result));
    phantom.exit();
});


function JSONtoString(object) {
 	var results = [];
 	for (var property in object) {
		var value = object[property];
		if (value)
			results.push(property.toString() + ': ' + value);
	}
	return '{' + results.join(', ') + '}';
}

function serialize(obj){
    var returnVal;
    if(obj != undefined){
        switch(obj.constructor){
            case Array:
                var vArr="[";
                for(var i=0;i<obj.length;i++){
                    if(i>0) vArr += ",";
                    vArr += serialize(obj[i]);
                }
                vArr += "]"
                return vArr;
            case String:
                returnVal = escape("'" + obj + "'");
                return returnVal;
            case Number:
                returnVal = isFinite(obj) ? obj.toString() : null;
                return returnVal;    
            case Date:
                returnVal = "#" + obj + "#";
                return returnVal;  
            default:
                if(typeof obj == "object"){
                    var vobj=[];
                    for(attr in obj){
                        if(typeof obj[attr] != "function"){
                            vobj.push('"' + attr + '":' + serialize(obj[attr]));
                        }
                    }
                    if(vobj.length >0){
                        return "{" + vobj.join(",") + "}";
                    }else{
                        return "{}";
                    }
                }else{
                    return obj.toString();
                }
        }
    }
    return null;
}
