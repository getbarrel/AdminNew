/**

 4. 12 사용 중지


*/
	if(phantom.args.length === 0){
		console.log('Usage: phantom zappos.js <url>');
	//	phantom.exit();
	}

	var page = require('webpage').create(), url;
	var url = phantom.args[0];
	var url = 'http://www.zappos.com/men-shirts-tops/CKvXARDL1wHAAQLiAgMBGAI.zso?s=goliveRecentSalesStyle/desc/&zfcTest=gs:0#!/men-shirts-tops/CKvXARDL1wHAAQLiAgMBGAI.zso?p=0&s=goliveRecentSalesStyle/desc/';
    //var url = 'http://www.gap.com/browse/product.do?pid=621326&scid=621326022';
    
    
	page.onError = function (msg, trace) {
	    console.log(msg);
	    trace.forEach(function(item) {
		console.log('  ', item.file, ':', item.line);
	    })
	}

	page.onConsoleMessage = function (msg) {
		console.log(msg);
	}

	page.open(url, function (status) {
        //console.log(status);
        if(status !== 'success'){
            console.log('Unable to access network');
        }else{
    	    var result = page.evaluate(function () {
                /**
                function Pagination(){
                    this.endPage = "";
                }
                
                function ProductList(){
                    this.productList = new Array();   
                }
    		    */
                
                function Zappos(){
    				this.productList = new Array();
                    this.endPage = ""; 
    		    }
    
    		    var zappos = new Zappos();
    		    //돔으로 접근이 안되나?
                //zappos.page[0] = document.activeElement.children[3].children[0].children[4].children[0].children[1].children[3].innerHTML;
                //zappos.endPage = document.activeElement.baseURI;
                
                //zappos.page[0] = "ss";
                //안됨
                //zappos.endPage = document.activeElement.children[3].children[0].children[4].baseURI;
                
                //됨
                //zappos.endPage = document.activeElement.children[3].isContentEditable;
                
                /**
                    이게 뭐얔
                */
                //됨 ** 두꺼운글씨
                //zappos.endPage = document.activeElement.children[3].children[0].isContentEditable;
                //안됨
                //zappos.endPage = document.activeElement.children[3].children[0].children[4].isContentEditable;
                //안됨 
                //zappos.endPage = document.activeElement.children[3].children[0].children[4].children[0].children[1].children[3].isContentEditable;
                
                //안됨 **얇은 글씨
                //zappos.endPage = document.activeElement.children[3].children[0].children[4].childElementCount;
                
                //안됨 ** 두꺼운글씨
                //zappos.endPage = document.activeElement.children[3].children[0].children[4].children[0].children[1].accessKeyLabel;
                //zappos.endPage = document.activeElement.children[3].children[0].children[4].children[0].accessKeyLabel;
                //zappos.endPage = document.activeElement.children[3].children[0].children[4].children[1].isContentEditable;
                
                /**
                var productListLength = parseInt(document.activeElement.children[3].children[0].children[4].children[1].children.length);
                for(i=0; i <productListLength; i++){
                    zappos.productList.push(document.activeElement.children[3].children[0].children[4].children[1].children[i].value);
                }
                
    		    //Boolean ex
                //zappos.isImported  = Boolean(window.productPage.objP.isImported);
    		    /**
                var list = document.querySelectorAll('div #searchResults'), product = [], i;
                for (i = 0; i < list.length; i++) {
                product.push(list[i].innerText);
                }
                return product;
                */
    		return zappos;
    	    });
    		console.log(JSON.stringify(result));
        }
	    phantom.exit();
	});
