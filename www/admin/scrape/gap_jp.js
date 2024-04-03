
	if(phantom.args.length === 0){
		console.log('Usage: phantom gap_jp.js <url>');
		phantom.exit();
	}
	console.log('Usage: phantom gap_jp.js <url>');
	var page = require('webpage').create(), url;
	var url = phantom.args[0];
//	var url = 'http://www.gap.com/browse/product.do?pid=621326&scid=621326022';

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
	    var result = page.evaluate(function () {
		
		    function FabricContent(){
                this.name = "";
                this.percent = "";
		    }

		    function Images()
		    {
    			this.SI = "";
    			this.VI = "";
    			this.VLI = "";
    			this.Z = "";
		    }

		    function InfoTabInfoBlock()
		    {
                this.displayText = "";
		    }

		    function InfoTabs()
		    {
    			this.infoTabName = "";
    			this.InfoTabInfoBlocks = new Array();
		    }

		    function Option()
		    {
    			this.Name = "";
    			this.StockString = "";
		    }

		    function OptionGroup()
		    {
    			this.Name = "";
    			this.options = new Array();
		    }

		    function Stock()
		    {
    			this.dimension1Name = "";
    			this.dimension2Name = "";
    			this.colorIndex = -1;
    			this.dimension1Index = -1;
    			this.dimension2Index = -1;
    			this.bStock = false;
		    }

		    function VariantStyleColor()
		    {
    			this.colorName = "";
    			this.regularPrice = "";
    			this.salePrice = "";
    			this.promoPrice = "";
    			this.strPartialMupMessage = "";
                this.SI = "";
    			this.VI = "";
    			this.VLI = "";
    			this.Z = "";
		    }
		    function PricePoint()
		    {
                this.text = "";
		    }

		    function Gap()
		    {
    			this.name = "";
                this.pid = "";
    			this.images = new Images();
                this.isInStock = false;
    			this.isImported = false;
    			this.careInstructionText = "";
    			this.activeColor = "";
    			this.activeSizeDimension1 = "";
    			this.activeSizeDimension2 = "";
    			this.sizeDimension1Name = "";
    			this.sizeDimension2Name = "";
    			this.groupNameDefault = "";
    			this.groupNameFit = "";
    			this.groupNameOverview = "";
    			this.groupNameFabric = "";
    
    			this.allSizeDimension1 = new Array();
    			this.allSizeDimension2 = new Array();
    
    			this.stocks = new Array();
    			this.fabricContents = new Array();
    			this.infoTabs = new Array();
    			this.variantStyleColors = new Array();
    			this.pricePoints = new Array();
		    }

		    var gap = new Gap();
		    gap.name = window.productPage.objP.strProductStyleName;
		    gap.pid = window.productPage.strDefaultStyleColorId;
            gap.images.SI   = window.imgs.SI;
		    gap.images.VI  = window.imgs.VI.src;
		    gap.images.VLI  = window.imgs.VLI.src;
            try{
                gap.images.Z    = window.imgs.Z.src;
            }
            catch(err){
            }
            gap.isInStock = window.productPage.objP.isInStock;
            
		    gap.isImported  = Boolean(window.productPage.objP.isImported);
		    gap.careInstructionText = window.productService.objP.strCareInstructionText;
		    gap.activeColor = window.productPage.activeColor;
		    gap.activeSizeDimension1 = window.productPage.activeSizeDimension1;
		    gap.activeSizeDimension2 = window.productPage.activeSizeDimension2;
		    gap.groupNameDefault = window.productPage.product.groupNameDefault;
		    gap.groupNameFabric = window.productPage.product.groupNameFabric;
		    gap.groupNameOverview = window.productPage.product.groupNameOverview;

		    var allSizeDimension1Length = parseInt(window.productPage.arrayAllSizeDimension1.length);
		    for(i = 0; i < allSizeDimension1Length; i++){
                gap.allSizeDimension1.push(window.productPage.arrayAllSizeDimension1[i].strName)
		    }

		    var allSizeDimension2Length = parseInt(window.productPage.arrayAllSizeDimension2.length);
		    for(i = 0; i < allSizeDimension2Length; i++){
                gap.allSizeDimension2.push(window.productPage.arrayAllSizeDimension2[i].strName)
		    }

		    var arrayFabricContentLength = parseInt(window.productPage.objP.arrayFabricContent.length);

		    for (i = 0; i < arrayFabricContentLength; i++)
		    {
    			var fabricContent = new FabricContent();
    			fabricContent.name = window.productPage.objP.arrayFabricContent[i].strName;
    			fabricContent.percent = window.productPage.objP.arrayFabricContent[i].strPercent;
    			gap.fabricContents.push(fabricContent);
		    }

    		try{
    
    		    var arrayTabInfoLength = parseInt(window.productPage.objP.arrayInfoTabs.length);
    
    		    for (i = 0; i < arrayTabInfoLength; i++)
    		    {
        			var infoTabs = new InfoTabs();
        			infoTabs.infoTabName = window.productPage.objP.arrayInfoTabs[i].strInfoTabName;
        			var arrayInfoTabInfoBlocksLength = parseInt(window.productPage.objP.arrayInfoTabs[i].arrayInfoTabInfoBlocks.length);
        			for(j = 0; j < arrayInfoTabInfoBlocksLength; j++){
        			    var infoTabInfoBlock = new InfoTabInfoBlock();
        			    infoTabInfoBlock.displayText = window.productPage.objP.arrayInfoTabs[i].arrayInfoTabInfoBlocks[j]['strDisplayText'];
        			    infoTabs.InfoTabInfoBlocks.push(infoTabInfoBlock);
        			}
                    gap.infoTabs.push(infoTabs);
    		    }
    		}
    		catch(err){
    		}

		    var arrayVariantStyleColorsLength = parseInt(window.productPage.objV.arrayVariantStyleColors.length);

		    for(i = 0; i < arrayVariantStyleColorsLength; i++){
    			var variantStyleColor = new VariantStyleColor();
    			variantStyleColor.colorName = window.productPage.objV.arrayVariantStyleColors[i].strColorName;
    			variantStyleColor.regularPrice = window.productPage.objV.arrayVariantStyleColors[i].strRegularPrice;
    			try{ 
                    variantStyleColor.salePrice = window.productPage.objV.arrayVariantStyleColors[i].strSalePrice;
                }catch (err){
                    
                };
    			try{
                    variantStyleColor.promoPrice = window.productPage.objV.arrayVariantStyleColors[i].strPromoPrice; 
                }catch (err){
                    
                };
        		variantStyleColor.strPartialMupMessage = window.productPage.objV.arrayVariantStyleColors[i].strPartialMupMessage;
                
                variantStyleColor.SI = window.productPage.objV.arrayVariantStyleColors[i].styleColorImagesMap.SI;
    			variantStyleColor.VI = window.productPage.objV.arrayVariantStyleColors[i].styleColorImagesMap.VI.src;
    			variantStyleColor.VLI = window.productPage.objV.arrayVariantStyleColors[i].styleColorImagesMap.VLI.src;
    			variantStyleColor.Z = window.productPage.objV.arrayVariantStyleColors[i].styleColorImagesMap.Z.src;
                
        		gap.variantStyleColors.push(variantStyleColor);
		    }

		    gap.sizeDimension1Name = window.productPage.objV.objStyleSizeInfo.strSizeDimension1Name;
		    gap.sizeDimension2Name = window.productPage.objV.objStyleSizeInfo.strSizeDimension2Name;
		    gap.groupNameDefault = window.productPage.product.groupNameDefault;
		    gap.groupNameFabric = window.productPage.product.groupNameFabric;
		    gap.groupNameFit = window.productPage.product.groupNameFit;
		    gap.groupNameOverview = window.productPage.product.groupNameOverview;
		    gap.groupNameFabric = window.productPage.product.groupNameFabric;
	    
		    for (i = 0; i < arrayVariantStyleColorsLength; i++)
		    {
    		    switch(gap.allSizeDimension2.length)
    		    {
    			case 0:
    			    for(j = 0; j < gap.allSizeDimension1.length; j++){
    			        var stock = new Stock();
    				    stock.dimension1Name = gap.allSizeDimension1[j];
    				    stock.dimension2Name = "";
    			        stock.colorIndex = i;//parseInt(gap.activeColor);
    			        stock.dimension1Index = j;
    			        stock.dimension2Index = -1;
    			        stock.bStock = Boolean(window.productPage.isSkuInStock(i, j, -1, undefined));
    			        gap.stocks.push(stock);
    			    }
    			    break;
    			default:
    			    for(j = 0; j < gap.allSizeDimension1.length; j++){
    			        for(k = 0; k < gap.allSizeDimension2.length; k++){
    			            var stock = new Stock();
    				        stock.dimension1Name = gap.allSizeDimension1[j];
    				        stock.dimension2Name = gap.allSizeDimension2[k];
    			            stock.colorIndex = i;//parseInt(gap.activeColor);
    			            stock.dimension1Index = j;
    			            stock.dimension2Index = k;
    			            stock.bStock = Boolean(window.productPage.isSkuInStock(i, j, k, undefined));
    			            gap.stocks.push(stock);
    			        }
    			    }
    
    			    break;
    		    }
            }
    		var pricePointsLength = parseInt(window.productPage.objV.pricePoints.length);
    
    		for(i = 0; i < pricePointsLength; i++){
    			var pricePoint = new PricePoint();
    			pricePoint.text = window.productPage.objV.pricePoints[i];
    			gap.pricePoints.push(pricePoint);
    		}
    
    		return gap;
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

	function serialize(obj)
	{
	  var returnVal;
	  if(obj != undefined){
	  switch(obj.constructor)
	  {
	   case Array:
	    var vArr="[";
	    for(var i=0;i<obj.length;i++)
	    {
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
	     for(attr in obj)
	     {
	      if(typeof obj[attr] != "function")
	      {
	       vobj.push('"' + attr + '":' + serialize(obj[attr]));
	      }
	     }
	      if(vobj.length >0)
	       return "{" + vobj.join(",") + "}";
	      else
	       return "{}";
	    }  
	    else
	    {
	     return obj.toString();
	    }
	  }
	  }
	  return null;
	}

