var integer_only;
var no_nagative_values;
var positive_integers_only;
var language_type;

if(language == "korea"){
	language_type = 0;
	integer_only = '정수만 입력 가능합니다.';
	no_nagative_values = '음수를 사용 하실 수 없습니다';
	positive_integers_only = '양수만 사용 하실 수 있습니다';
	//agative_values = '';
}else if(language == "english"){
	language_type = 1;
	integer_only = 'Integers only';
	no_nagative_values = 'Negative value is not allowed';
	positive_integers_only = 'Positive integers only';
}else{
	language_type = 3;
	integer_only = 'Integers only';
	no_nagative_values = 'Negative value is not allowed';
	positive_integers_only = 'Positive integers only';
}



/*
var langauge_pack =
{ "order_order_goods_list_js": [
	{ "confirm_incom": [
		{ "korea": "해당 주문을 입금확인 처리 하시겠습니까?", "english":"Do you want to deposit a check, the order processing?", "indonesian": "Apakah anda ingin deposit cek, pemrosesan order?" }
	]},
	{ "confirm_delivery": [
		{ "korea": "해당 주문상품을 배송준비중처리 하시겠습니까?", "english":"Publication processing of the order of items do you want?", "indonesian": "Pengolahan Publikasi urutan item yang Anda inginkan?" }
	]}
]}
*/
//		   alert(langauge_pack.order_order_goods_list_js[0].confirm_incom[0].korea);
/*
var langauge_pack = 
{"order_order_goods_list_js":
	{"confirm_incom":
		{ "aaa": "해당 주문을 입금확인 처리 하시겠습니까?"},
		{ "bbb": "해당 주문을 입금확인 처리 하시겠습니까?"}
	}
}
*/
//alert(langauge_pack.order_order_goods_list_js.confirm_incom.bbb);

var langauge_pack = 
{
"common":[
	{"message":[
		{"text": "카테고리를 선택해주세요"}, 
		{"text":"Please select a category"}, 
		{"text": "Silakan pilih kategori" }
	]},
	{"message":[
		{"text": "정말로 삭제 하시겠습니까?"}, 
		{"text":"Do you really want to delete?"}, 
		{"text": "Apakah Anda yakin?" }
	]}
],
"goods_input_php":[
	{"message":[
		{"text": "카테고리를 선택해주세요"}, 
		{"text":"Please select a category"}, 
		{"text": "Silakan pilih kategori" }
	]},
	{"message":[
		{"text": "-"}, 
		{"text":"Do you want to deposit a check, the order processing?"}, 
		{"text": "Apakah anda ingin deposit cek, pemrosesan order?" }
	]}
],
"product_input_excel_js":[
	{"message":[
		{"text": "삭제하실 제품을 한개이상 선택하셔야 합니다."}, 
		{"text":"Select products you would like to delete one or more is required."}, 
		{"text": "Pilih produk yang Anda ingin menghapus satu atau lebih diperlukan." }
	]},
	{"message":[
		{"text": "-"}, 
		{"text":"Do you really want to delete?"}, 
		{"text": "Apakah Anda yakin?" }
	]}
],
"product_list_js":[
	{"message":[
		{"text": "해당 상품에 대한 정보를 수정하시겠습니까?"}, 
		{"text": "Are you sure you want to edit information about their products?"}, 
		{"text": "Apakah Anda yakin ingin mengedit informasi tentang produk mereka?" }
	]},
	{"message":[
		{"text": "수정하실 제품을 한개이상 선택하셔야 합니다."}, 
		{"text": "Select the product you want to modify one or more is required."}, 
		{"text": "Menyempurnakan produk yang akan perlu memilih satu atau lebih." }
	]},
	{"message":[
		{"text": "삭제하실 제품을 한개이상 선택하셔야 합니다."}, 
		{"text": "Select the product you want to delete one or more must be."}, 
		{"text": "Pilih produk yang anda ingin menghapus satu atau lebih harus" }
	]}
	,
	{"message":[
		{"text": "선택된 제품이 없습니다. 변경하시고자 하는 상품을 선택하신 후 저장 버튼을 클릭해주세요"}, 
		{"text": "There are no products selected. The product you wish to change your choice, please click the Save button"}, 
		{"text": "Tidak ada produk yang dipilih. Produk yang Anda ingin mengubah pilihan Anda, silakan klik tombol Simpan" }
	]}
	,
	{"message":[
		{"text": "검색상품 전체에 정보변경을 하시겠습니까?"}, 
		{"text": "Do you want to change the entire product information retrieved?"}, 
		{"text": "Apakah Anda ingin mengubah informasi seluruh produk diambil?" }
	]}
],
"order_order_js":[
	{"message":[
		{"text": "상태변경하실 주문을 한개이상 선택하셔야 합니다."}, 
		{"text": "Select one or more of the state to change the order is required."}, 
		{"text": "Pilih satu atau lebih dari negara untuk mengubah urutan diperlukan." }
	]}
]
}








/*
var language_data = new Array();
var page = new Array();
var detail = new Array();

detail[language]="거래처를 정말로 삭제하시겠습니까?";

div_code["A"]="11";
language["company.add.js"]= detail;


alert(language["aaaaaa"]["aa"]);
*/

//alert(window.location.pathname);

// langauge_pack.order_order_goods_list_js[9].message[language_type].text
//alert(1);  langauge_pack.common[1].message[language_type].text
 //alert(langauge_pack.goods_input_php[1].message[language_type].text);//.confirm_incom[language_type].text
 //alert(langauge_pack.order_order_goods_list_js[1].message[language_type].text);//.confirm_incom[language_type].text