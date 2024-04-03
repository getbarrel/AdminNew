$(document).ready(function() {
	$('#reserve_one_use_type_1').click(function(){
		document.getElementById('max_goods_sum_rate').validation=false;
		document.getElementById('use_reseve_max').validation=true;

		//onclick="document.getElementById('max_goods_sum_rate').validation=true;document.getElementById('use_reseve_max').validation=false"

	});

	$('#reserve_one_use_type_2').click(function(){
		document.getElementById('max_goods_sum_rate').validation=true;
		document.getElementById('use_reseve_max').validation=false;

	});
});
