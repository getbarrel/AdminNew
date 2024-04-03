
$(function(){
	
	// --- Initialize sample trees
	$("#tree").dynatree({
		title: "페이지",
		rootVisible: true,
		fx: { height: "toggle", duration: 200 },

		initAjax: { 
			url: "sample-data1.php" 
		},
		
		onActivate: function(dtnode) {
			
			$("#plevel1").val(dtnode.data.plevel1);
			$("#plevel2").val(dtnode.data.plevel2);
			$("#plevel3").val(dtnode.data.plevel3);
			$("#depth").val(dtnode.data.depth);
			$("#this_category").text(dtnode.data.title);
			
		},

		onLazyRead: function(dtnode){
			// In real life we would call something like this:
//            	dtnode.appendAjax({
//            	    url: "/getChildrenAsJson",
//		            data: {key: dtnode.data.key,
//            		       mode: "funnyMode"
//                         }
//              });
			// .. but here we use a local file instead:
			dtnode.appendAjax({
				url: "sample-data1.php", 
				data: {
					cid: dtnode.data.key,
					depth : dtnode.data.depth
				}
			});
		}
		
	});

});

