<script type="text/javascript">
	//	wel_우클릭 드레그 방지
	$(document).ready(function(){
		$(document).bind("contextmenu", function(e) {
			return false;
		});
	});

	$(document).bind('selectstart',function() {return false;}); 
	$(document).bind('dragstart',function(){return false;});




	var ctrlDown = false;
	var ctrlKey = 17, vKey = 86, cKey = 67;
	$(document).keydown(function(e)
	{
		if (e.keyCode == ctrlKey) ctrlDown = true;
	}).keyup(function(e)
	{
		if (e.keyCode == ctrlKey) ctrlDown = false;
	});
</script>