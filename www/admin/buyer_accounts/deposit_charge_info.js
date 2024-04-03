function deposit_cancel(history_ix){
	if(confirm('예치금 신청 정보를 취소 하시겠습니까?')){
		var formData = new FormData();

		formData.append('act', 'deposit_cancel');
		formData.append('history_ix', history_ix);

		$.ajax({
			url: './deposit_charge.act.php',
			data: formData,
			processData: false,
			contentType: false,
			type: 'POST',
			success: function(result){
				if(result == true){
					alert('예치금 신청 정보가 취소 되었습니다.');
					location.reload();
				}else{
					alert('실패');
				}
			}
		});
	}
}

function deposit_in_complete(history_ix){
	if(confirm('예치금 신청 정보를 입금 완료 하시겠습니까?')){
		var formData = new FormData();

		formData.append('act', 'deposit_in_complete');
		formData.append('history_ix', history_ix);

		$.ajax({
			url: './deposit_charge.act.php',
			data: formData,
			processData: false,
			contentType: false,
			type: 'POST',
			success: function(result){
				if(result == true){
					alert('입금완료 처리 되었습니다.');
					location.reload();
				}else{
					alert('실패');
				}
			}
		});
	}
}
function deposit_w_cancel(history_ix){
	if(confirm('예치금 출금 신청 정보 를 취소 하시겠습니까?')){
		var formData = new FormData();

		formData.append('act', 'deposit_w_cancel');
		formData.append('history_ix', history_ix);

		$.ajax({
			url: './deposit_charge.act.php',
			data: formData,
			processData: false,
			contentType: false,
			type: 'POST',
			success: function(result){
				if(result == true){
					alert('출금 취소 처리 되었습니다.');
					location.reload();
				}else{
					alert('실패');
				}
			}
		});
	}
}
function deposit_w_complete(history_ix){
	if(confirm('예치금 출금 정보를 확정 하시겠습니까?')){
		var formData = new FormData();

		formData.append('act', 'deposit_w_complete');
		formData.append('history_ix', history_ix);

		$.ajax({
			url: './deposit_charge.act.php',
			data: formData,
			processData: false,
			contentType: false,
			type: 'POST',
			success: function(result){
				if(result == true){
					alert('출금 완료 처리 되었습니다.');
					location.reload();
				}else{
					alert('실패');
				}
			}
		});
	}
}


