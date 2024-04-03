//[S] 수수료 입력

function incentiveTotal(cost, limit)
{
	if(cost > limit){
		alert('0~'+limit+' 사이의 값을 입력해주세요.');
		$('input[name=incentive_rate]').val(limit);
	}
}

function incentiveCreate()
{
	var costTotal = new Number($('input[name=incentive_rate]').val());
	var costMagager = new Number($('input[name=incentive_rate_manager]').val());
	var costReseller = new Number($('input[name=incentive_rate_reseller]').val());

	if(costTotal == ''){
		alert('리셀러 지급 수수료를 입력해주세요.');
		$('input[name=incentive_rate_manager]').val('');
		$('input[name=incentive_rate_reseller]').val('');
		return false;
	}

	if(costTotal < costMagager){
		alert('매니저 수수료는 0~'+costTotal+'% 까지만 입력 가능합니다.');
		$('input[name=incentive_rate_manager]').val(costTotal);
	}

	var cost = costTotal - $('input[name=incentive_rate_manager]').val();

	$('input[name=incentive_rate_reseller]').val(cost);
}

//[E] 수수료 입력


//[S] 리셀러 등록

function reserllerAdd(code, name, btn)
{
	var check = confirm(name+"회원을 매니저로 적용하시겠습니까?");

	if(check == true){

		$.ajax({
			type: "POST",
			url: "reseller.act.php",
			data: ({
					act : "addManager",
					code : code
			}),
			async: true,
			success:function(result){
				if(result == "Y"){
					alert("매니저 등록이 정상적으로 처리되었습니다.");
					btn.remove();
				}else{
					alert("이미 리셀러로 등록되어있는 회원입니다.");
				}
			}
		});

	}else{
		return false;
	}
}

function requetBank(frm){
	var name = frm.find("input[name=name]").val();
	var bank_name = frm.find("select[name=bank_name]").val();
	var bank_number = frm.find("input[name=bank_number]").val();
	var bank_owner = frm.find("input[name=bank_owner]").val();

	if(bank_name == ""){
		alert("은행을 선택해주세요.");
		return false;
	}

	if(bank_owner == ""){
		alert("예금주를 입력해주세요.");
		return false;
	}

	if(bank_number == ""){
		alert("계좌번호를 입력해주세요.");
		return false;
	}

	frm.submit;
}

//[E] 리셀러 등록


//[S] 수수료율 변경

function costChange(frm)
{
	var type = $("select[name=update_type]").val();
	var cost = $("input[name=incentive_rate]").val();
	var chk = false;

	if(cost == ""){
		alert("수수료율을 입력해주세요.");
		$("input[name=incentive_rate]").focus();
		return false;
	}
}

//[E] 수수료율 변경