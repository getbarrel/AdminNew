fixScale(document);

main();

function main() {
    var val = document.getElementById('rgbValue');
    var hVal = document.getElementById('hslaValue');

    colorjoe.registerExtra('text', function(p, joe, o) {
        e(p, o.text? o.text: 'text');
    });

   

    colorjoe.rgb('extraPicker', '#ffffff', [
                 'currentColor',
                 ['fields', {space: 'RGB', limit: 255, fix: 2}],
                 'hex'
    ]);

	$('#extraPicker').css('display', 'none');
	$('#aClose').css('display', 'none');
}

function colorOpen() {
    $('#extraPicker').css('display', '');
	$('#aOpen').css('display', 'none');
	$('#aClose').css('display', '');
}

function colorClose(){
	$('#c_preface').val($('#colorPickerInput').val());

	$('#extraPicker').css('display', 'none');
	$('#aOpen').css('display', '');
	$('#aClose').css('display', 'none');
}