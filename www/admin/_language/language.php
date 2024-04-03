<?
include_once($_SERVER["DOCUMENT_ROOT"]."/class/database.class");


if($admininfo[language]!="") {
?>
var language = "<?=$admininfo[language]?>";
<?
}
?>


var language_data = {};
language_data["common"] = {
	A:{
		korea:"수정 권한이 없습니다.",
		english:"You do not have permission to modify.",
		indonesian: "Anda tidak mempunyai izin memodifikasi.",
		japan: "修正権限がありません.",
		chinese: ""
	},
	B:{
		korea:"삭제 권한이 없습니다.",
		english:"You do not have permission to delete",
		indonesian: "Anda tidak mempunyai izin menghapus",
		japan: "削除権限がありません.",
		chinese: ""
	},
	C:{
		korea:"관리자 로그인후 사용하실 수 있습니다. ",
		english:"You may use after administrator log-in.  ",
		indonesian: "Anda dapat menggunakan setelah administrator log-in.  ",
		japan: "管理者ログイン後、使うことができます.",
		chinese: ""
	},
	D:{
		korea:"정상적으로 추가 되었습니다.  ",
		english:"The ratings has been registered successfully.  ",
		indonesian: "Peringkat berhasil didaftarkan.  ",
		japan: "正常に追加されました.",
		chinese: ""
	},
	E:{
		korea:"정상적으로 수정 되었습니다.  ",
		english:"The ratings has been modified successfully.  ",
		indonesian: "Peringkat berhasil dimodifikasi.  ",
		japan: "正常に修正されました.",
		chinese: ""
	},
	F:{
		korea:"정상적으로 삭제 되었습니다.  ",
		english:"The ratings has been deleted successfully.  ",
		indonesian: "Peringkat berhasil dihapus.  ",
		japan: "正常に削除しました.",
		chinese: ""
	},
	G:{
		korea:"정말로 삭제 하시겠습니까.  ",
		english:"Do you really want to delete?  ",
		indonesian: "Apakah Anda ingin menghapus?",
		japan: "本当に削除しますか.",
		chinese: ""
	},
	H:{
		korea:"정상적으로 등록 되었습니다.  ",
		english:"it has been registered successfully ",
		indonesian: "Berhasil didaftarkan",
		japan: "正常に登録されました.",
		chinese: ""
	}
};


language_data["order_order.js"] = {
	A:{
		korea:"상태 변경하실 주문을 한개 이상 선택하셔야 합니다.",
		english:"Select one or more of the state to change the order is required.",
		indonesian: "Pilih satu atau lebih dari negara untuk mengubah urutan diperlukan.",
		japan: " 状態変更する注文を一つ以上選択しなければなりません.",
		chinese: ""
	}
};


language_data["company_user.php"] = {
	A:{
		korea:"사용자 정보를 정말로 삭제하시겠습니까?",
		english:"Do you really want to delete the user information?",
		indonesian: "Apakah Anda ingin menghapus informasi pengguna?",
		japan: "使用者情報を本当に削除しますか?",
		chinese: ""
	},
	B:{
		korea:"비밀번호가 입력되지 않았습니다.",
		english:"Password not entered",
		indonesian: "Kata sandi belum dimasukkan",
		japan: "パスワードが入力されていません.",
		chinese: ""
	},
	C:{
		korea:"비밀번호가 확인 정보가 입력되지 않았습니다. ",
		english:"Password verification information is not entered",
		indonesian: "Informasi verifikasi kata sandi belum dimasukkan",
		japan: "パスワードと確認情報が入力されていません.",
		chinese: ""
	},
	D:{
		korea:"비밀번호가 정확하지 않습니다 확인 후 다시 입력해주세요 ",
		english:"Password is not correct, please enter again after checking.",
		indonesian: "Kata sandi salah. Silakan masukkan lagi setelah memeriksa. ",
		japan: "パスワードが正確ではありません。確認後再入力してください",
		chinese: ""
	}
};

language_data["company.add.js"] = {
	A:{
		korea:"거래처를 정말로 삭제하시겠습니까?",
		english:"Do you really want to delete the information?",
		indonesian: "Apakah Anda ingin menghapus informasi?",
		japan: "取引先を本当に削除しますか?",
		chinese: ""
	},
	B:{
		korea:"거래처를 정말로 추천하시겠습니까?",
		english:"Do you really want to recommend?",
		indonesian: "Apakah Anda ingin merekomendasikan?",
		japan: "取引先を本当に推薦しますか?",
		chinese: ""
	},
	C:{
		korea:"거래처를 정말로 추천 취소하시겠습니까?",
		english:"Do you really want to cancel the recommendation?",
		indonesian: "Apakah Anda ingin membatalkan rekomendasi?",
		japan: "取引先の推薦を本当に取り消しますか?",
		chinese: ""
	}
};

language_data["company.act.php"] = {
	A:{
		korea:"는 이미 등록된 사용자 입니다.",
		english:"is already registered",
		indonesian: "sudah terdaftar",
		japan: " は既に登録された使用者です.",
		chinese: ""
	}
};


language_data["design.php"] = {
	A:{
		korea:"페이지 수정 내용과 백업 소스를 정말로 삭제하시겠습니까?",
		english:"Do you really want to delete backup source and modified page contents.",
		indonesian: "Apakah Anda ingin menghapus sumber backup dan memodifikasi konten halaman? ",
		japan: "ページ修正内容とバックアップソースを本当に削除しますか?",
		chinese: ""
	},
	B:{
		korea:"해당내용으로 복구하시겠습니까? 1차적으로 화면에만 복구되게 되며 완전한 복구를 원하실때는 화면 복구후 저장버튼을 눌러주시기 바랍니다.",
		english:"Are you sure you want to recover its contents? Repair is done on the screen,if you want a full recovery, pleas click 'Save' button after 'screen recover'",
		indonesian: "Apakah Anda ingin memperbaiki konten? Perbaikan sudah selesai, jika Anda ingin perbaikan menyeluruh, silakan klik tombol 'Simpan' setelah 'Layar telah diperbaiki' ",
		japan: "党内用に復旧しますか? 1次的に画面だけ復旧します。完全に復旧する時は画面復旧後、保存ボタンを押してください.",
		chinese: ""
	},
	C:{
		korea:"선택하신 디자인 백업을 정말로 삭제하시겠습니까? 삭제하신 디자인 백업 복원되지 않습니다",
		english:"Do you really want to delete selected design backup? It will not be restored the deleted design backup permanently.",
		indonesian: "Apakah Anda ingin menghapus desain backup yang terpilih? Back up desain yang sudah dihapus tidak akan disimpan kembali secara permanen.",
		japan: "選択したデザインバックアップを本当に削除しますか? 削除したデザインバックアップ復元されません",
		chinese: ""
	},
	D:{
		korea:"삭제하실 목록을 한개이상 선택하셔야 합니다.",
		english:"You must select a list of more than one to delete.",
		indonesian: "Anda harus memilih satu atau beberapa daftar untuk menghapus.",
		japan: "削除するリストを一つ以上選択しなければいけません",
		chinese: ""
	},
	E:{
		korea:"분류정보는 페이지 구성을 하실수 없으며 하부에 페이지를 생성 하실수 있습니다. 아래 디자인구성을 클릭하셔서 구성정보를 수정하실수 잇습니다.",
		english:"This level can not be configured, so the page of lower level can be configured. Please modify a page after click 'Design configurations' on the bottom left of the page",
		indonesian: "Level ini tidak dapat dikonfigurasi. Halaman level lebih rendah yang dapat dikonfigurasi. Silakan modifikasi halaman setelah mengklik 'Konfigurasi design' di bagian bawah kiri halaman",
		japan: "分類情報はページ構成できません。下部にページを作成することができます. 下のデザイン構成をクリックして構成情報を修正すること引き継ぎます.",
		chinese: ""
	}
};

language_data["design.act.php"] = {
	A:{
		korea:"해당파일에 대한 쓰기권한이 없습니다.",
		english:"You do not have write permission on that file.",
		indonesian: "Anda tidak mempunyai izin menulis dokumen itu. ",
		japan: "該当ファイルに対する書き取り権限がありません.",
		chinese: ""
	},
	B:{
		korea:"정상적으로  화면복구 되었습니다.",
		english:"It has been recovered successfully.",
		indonesian: "Sudah berhasil diperbaiki. ",
		japan: "正常に画面を復旧しました.",
		chinese: ""
	},
	C:{
		korea:"해당코드는 이미 등록된 코드입니다. 페이지코드를 바꿔주시기 바랍니다.",
		english:"The code is already registered. Please change the code.",
		indonesian: "Kode sudah terdaftar. Silakan ubah kode itu.",
		japan: "該当コードは既に登録されたコードです. ページコードを変えてください.",
		chinese: ""
	}
};


language_data["design.common.php"] = {
	A:{
		korea:"선택된 쇼핑몰 스킨이 없습니다. 스킨 선택 후 디자인 수정을 하실수 있습니다.",
		english:"There are no shopping malls skin. Please modify after selecting the skin.",
		indonesian: "Tidak ada shopping malls skin. Silakan modifikasi setelah memilih skin. ",
		japan: "選択されたショッピングモールスキンがありません. スキン選択後デザイン修正することができます",
		chinese: ""
	},
	B:{
		korea:"선택된 스킨이 없습니다. 스킨 선택 후 디자인 수정을 하실수 있습니다.",
		english:"There are no shopping malls skin. Please modify after selecting the skin.",
		indonesian: "Sudah berhasil diperbaiki. ",
		japan: "選択されたスキンがありません. スキン選択後デザイン修正することができます.",
		chinese: ""
	},
	C:{
		korea:"선택된 모바일 스킨이 없습니다. 모바일 스킨 선택 후 디자인 수정을 하실수 있습니다.",
		english:"There are no mobile shopping malls skin. Please modify after selecting the mobile skin.",
		indonesian: "Tidak ada shopping malls skin. Silakan modifikasi setelah memilih skin.  ",
		japan: "選択されたモバイルスキンがありません. モバイルスキン選択後デザイン修正をすることができます.",
		chinese: ""
	},
	D:{
		korea:"선택된 미니샵 스킨이 없습니다. 미니샵 스킨 선택 후 디자인 수정을 하실수 있습니다.",
		english:"There are no minishop  skin. Please modify after selecting the minishop skin.",
		indonesian: "Tidak ada minishop skin. Silakan modifikasi setelah memilih skin.  ",
		japan: "選択されたミニショップスキンがありません. ミニショップスキン選択後デザイン修正をすることができます.",
		chinese: ""
	}
};

language_data["design.mod.php"] = {
	A:{
		korea:"페이지 수정내용과 백업 소스를 정말로 삭제하시겠습니까?",
		english:"Do you really want to delete backup source and modified page contents.",
		indonesian: "Apakah Anda ingin menghapus sumber backup dan memodifikasi konten halaman? ",
		japan: "ページ修正内容とバックアップソースを本当に削除しますか?",
		chinese: ""
	},
	B:{
		korea:"해당내용으로 복구하시겠습니까?\\n1차적으로 화면에만 복구되게 되며 완전한 복구를 원하실때는 화면 복구후 저장버튼을 눌러주시기 바랍니다. ",
		english:"Are you sure you want to recover its contents? Repair is done on the screen,if you want a full recovery, pleas click 'Save' button after 'screen recover' ",
		indonesian: "Apakah Anda ingin memperbaiki konten? Perbaikan sudah selesai, jika Anda ingin perbaikan menyeluruh, silakan klik tombol 'Simpan' setelah 'Layar telah diperbaiki' ",
		japan: "該当内容に復旧しますか?\\n 1次的に画面だけ復旧します。完全に復旧する時は画面復旧後、保存ボタンを押してください.",
		chinese: ""
	},
	C:{
		korea:"분류정보입니다.",
		english:"This is Classified information ",
		indonesian: "Informasi rahasia ",
		japan: "分類情報です.",
		chinese: ""
	}
};

language_data["seller/index.php"] = {
	A:{
		korea:"사용후기를 정말로 삭제하시겠습니까?",
		english:"Do you really want to delete the review?",
		indonesian: "Apakah Anda ingin menghapus review? ",
		japan: "使用レビューを本当に削除しますか?",
		chinese: ""
	},
	B:{
		korea:"해당 프로모션  정말로 삭제하시겠습니까? 삭제하시면 관련된 모든 이미지가 삭제됩니다. ",
		english:"Do you really want to delete this promotion? All the images will be deleted",
		indonesian: "Apakah Anda ingin menghapus promosi ini? Semua gambar akan dihapus ",
		japan: "該当のプロモーションを本当に削除しますか? 削除すると関係あるすべてのイメージが削除されます.",
		chinese: ""
	}
};


language_data["cash.php"] = {
	A:{
		korea:"적립금 정보를 정말로 삭제하시겠습니까?",
		english:"Do you really want to delete Mileage points information?",
		indonesian: "Apakah Anda ingin menghapus informasi kupon poin? ",
		japan: "積立金情報を本当に削除しますか?",
		chinese: ""
	},
	B:{
		korea:"입점업체를 선택해주세요",
		english:"Please select a vendor",
		indonesian: "Silakan pilih vendor ",
		japan: "入店業社を選択してください",
		chinese: ""
	},
	C:{
		korea:"내용을 입력해주세요",
		english:"Please enter a description",
		indonesian: "Silakan masukkan deskripsi",
		japan: "内容を入力してください",
		chinese: ""
	},
	D:{
		korea:"캐쉬를 입력해주세요",
		english:"Please enter a cache",
		indonesian: "Silakan masukkan cache",
		japan: "キャッシュを入力してください",
		chinese: ""
	}
};

language_data["orders.edit.php"] = {
	A:{
		korea:"해당 상담내역을 정말로 삭제 하시겠습니까?",
		english:"Do you really want to delete the history of counseling?",
		indonesian: "Apakah Anda ingin menghapus histori konsultasi? ",
		japan: "該当の相談内訳を本当に削除しますか?",
		chinese: ""
	},
	B:{
		korea:"[처리완료] 기록은 삭제할 수 없습니다.",
		english:"[Process Completed] record can not be deleted",
		indonesian: "(Proses selesai) catatan tidak dapat dihapus ",
		japan: "[処理完了] 記録は削除することができません.",
		chinese: ""
	},
	C:{
		korea:"[카드결제]는 [승인취소]와 [주문취소] 처리를 먼저한 후 삭제해주세요.",
		english:"You should delete [Card Payment] after doing [Approval Cancelation] and [Order Cancelation]",
		indonesian: "Anda harus menghapus (pembayaran menggunakan kartu) setelah melakukan (pembatalan persetujuan) dan (pembatalan pemesanan) ",
		japan: "[カード決済]は [承認取り消し]と [注文取り消し] 先に処理をした後削除してください.",
		chinese: ""
	},
	D:{
		korea:"[처리완료] 기록은 승인취소할 수 없습니다.",
		english:"[Process Completed] record for the 'Approval Canceled' can not be processed.",
		indonesian: "(Proses selesai) catatan 'persetujuan dibatalkan' tidak dapat diproses. ",
		japan: "[処理完了] 記録は承認の取り消しすることができません.",
		chinese: ""
	}
};

language_data["receipt_apply.php"] = {
	A:{
		korea:"원 거래 시각을 정확히 입력해 주시기 바랍니다.",
		english:"Please enter the exact transaction time.",
		indonesian: "Silakan masukkan waktu transaksi yang tepat. ",
		japan: "ウォン 取り引き時間を正確に入力してください.",
		chinese: ""
	},
	B:{
		korea:"발행 사업자번호를 정확히 입력해 주시기 바랍니다.",
		english:"Please enter the exact business registration number.",
		indonesian: "Silakan masukkan nomor pendaftaran bisnis yang tepat.",
		japan: "発行事業者番号を正確に入力してください.",
		chinese: ""
	},
	C:{
		korea:"주민번호 또는 휴대폰번호를 정확히 입력해 주시기 바랍니다.",
		english:"Please enter the correct Mobile phone number or Social security number.",
		indonesian: "Silakan masukkan nomor handphone atau jaminan sosial yang tepat.",
		japan: "住民番号または携帯電話番号を正確に入力してください.",
		chinese: ""
	},
	D:{
		korea:"사업자번호를 정확히 입력해 주시기 바랍니다.",
		english:"Please enter the exact business registration number.",
		indonesian: "Silakan masukkan nomor pendaftaran bisnis yang tepat.",
		japan: "事業者番号を正確に入力してください.",
		chinese: ""
	}
};


language_data["product_return.php"] = {
	A:{
		korea:"반품 요청이 정상적으로 처리 되었습니다.",
		english:"Return request was processed successfully",
		indonesian: "Permintaan pengembalian telah berhasil diproses. ",
		japan: "返品要請を正常に処理しました.",
		chinese: ""
	}
};

language_data["excel_out.act.php"] = {
	A:{
		korea:"[처리완료] 기록은 삭제할 수 없습니다.",
		english:"[Process Completed] record can not be deleted ",
		indonesian: "(Proses selesai) catatan tidak dapat dihapus ",
		japan: "[処理完了] 記録は削除することができません.",
		chinese: ""
	}
};

language_data["accounts.php"] = {
	A:{
		korea:"체크박스를 하나 이상 선택 하셔야 합니다.",
		english:"You must select one or more checkbox. ",
		indonesian: "Anda harus memilih satu atau beberapa checkbox. ",
		japan: "チェックボックスを一つ以上選択しなければいけません.",
		chinese: ""
	},
	B:{
		korea:"해당 정산내용을 정산확인 처리 하시겠습니까?",
		english:"Do you want to confirm that settlement?",
		indonesian: "Apakah Anda ingin menghapus informasi kupon poin?",
		japan: "該当の精算内容を精算確認処理しますか?",
		chinese: ""
	},
	C:{
		korea:"일괄정산 준비중입니다",
		english:"It is being prepared for lump Settlement",
		indonesian: "Pembayaran tagihan akumulasi sedang disiapkan",
		japan: "一括精算準備中です",
		chinese: ""
	}
};

language_data["orders.list.php"] = {	
	A:{
		korea:"[처리완료] 기록은 삭제할 수 없습니다.",
		english:"[Process Completed] record can not be deleted",
		indonesian: "(Proses selesai) catatan tidak dapat dihapus ",
		japan: "[処理完了] 記録は削除することができません.",
		chinese: ""
	}
};

language_data["accounts_plan_price.php"] = {	
	A:{
		korea:"일괄정산 준비중입니다",
		english:"It is being prepared for lump Settlement",
		indonesian: "Pembayaran tagihan akumulasi sedang disiapkan",
		japan: "一括精算準備中です",
		chinese: ""
	},
	B:{
		korea:"해당 정산내용을 정산확인 처리 하시겠습니까?",
		english:"Do you want to confirm that settlement?",
		indonesian: "Apakah Anda ingin mengonfirmasi pembayaran?",
		japan: "該当の精算内容を精算確認処理しますか?",
		chinese: ""
	}
};

language_data["orders_memo.php"] = {	
	A:{
		korea:"해당 상담내역을 정말로 삭제 하시겠습니까?",
		english:"Do you really want to delete the history of counseling?",
		indonesian: "Apakah Anda ingin menghapus histori konseling? ",
		japan: "該当の相談内訳を本当に削除しますか?",
		chinese: ""
	}
};

language_data["taxbill.php"] = {	
	A:{
		korea:"정말로 메일을 발송하시겠습니까? 발송하시게 되면 메일과 함께 세금계산서가 엑셀파일로 고객님께 전송되게 됩니다.",
		english:"Do you really want to send the mail? If so Email and the file with the Tax invoice will be sent to you.",
		indonesian: "Apakah Anda ingin mengirim email? Jika iya, dokumen dengan faktur pajak akan dikirim kepada Anda. ",
		japan: "本当にメールを発送しますか? 発送すればメールと共に税金計算書がエクセルファイルでお客様に送信されます.",
		chinese: ""
	}
};



language_data["accounts_plan.php"] = {	
	A:{
		korea:"권한이 없습니다.",
		english:"You do not have permission",
		indonesian: "Anda tidak mempunyai izin",
		japan: "権限がありません.",
		chinese: ""
	}
};

language_data["accounts_detail.php"] = {	
	A:{
		korea:"[처리완료] 기록은 삭제할 수 없습니다.",
		english:"[Process Completed] record can not be deleted",
		indonesian: "(Proses selesai) catatan tidak dapat dihapus",
		japan: "[処理完了] 記録は削除することができません.",
		chinese: ""
	},
	B:{
		korea:"[카드결제]는 [승인취소]와 [주문취소] 처리를 먼저한 후 삭제해주세요.",
		english:"You should delete [Card Payment] after doing [Approval Cancelation] and [Order Cancelation] ",
		indonesian: " Anda harus menghapus (pembayaran menggunakan kartu) setelah melakukan (pembatalan persetujuan) dan (pembatalan pemesanan) ",
		japan: "[カード決済]は [承認取り消し]と [注文取り消し] 先に処理をした後で削除してください.",
		chinese: ""
	},
	C:{
		korea:"[처리완료] 기록은 승인취소할 수 없습니다.",
		english:"[Process Completed]record for the 'Approval Canceled' can not be processed. ",
		indonesian: " (Proses selesai) catatan 'persetujuan dibatalkan' tidak dapat diproses. ",
		japan: "[処理完了] 記録は承認の取り消しをすることができません.",
		chinese: ""
	}
};

language_data["orders.act.php"] = {	
	A:{
		korea:"카드결제일 경우 [승인취소] 작업도 해주세요.",
		english:"If it is a credit card payment, please do [Approval Cancel]",
		indonesian: "Silakan melakukan (pembatalan persetujuan) jika pembayaran menggunakan kartu kredit",
		japan: "カード決済の場合 [承認取り消し] 作業も行ってください.",
		chinese: ""
	}
};

language_data["orders.read.php"] = {	
	A:{
		korea:"[처리완료] 기록은 삭제할 수 없습니다.",
		english:"[Process Completed] record can not be deleted",
		indonesian: "(Proses selesai) catatan tidak dapat dihapus",
		japan: "[処理完了] 記録は削除することができません.",
		chinese: ""
	},
	B:{
		korea:"[카드결제]는 [승인취소]와 [주문취소] 처리를 먼저한 후 삭제해주세요.",
		english:"You should delete [Card Payment] after doing [Approval Cancelation] and [Order Cancelation] ",
		indonesian: "Anda harus menghapus (pembayaran menggunakan kartu) setelah melakukan (pembatalan persetujuan) dan (pembatalan pemesanan) ",
		japan: "[カード決済]は [承認取り消し]と [注文取り消し] 先に処理した後削除してください.",
		chinese: ""
	}
};

language_data["orders.js"] = {	
	A:{
		korea:"정말로 반품 처리 하시겠습니까?",
		english:"Do you really want to return the procduct?",
		indonesian: "Apakah Anda ingin mengembalikan produk? ",
		japan: "本当に返品処理しますか?",
		chinese: ""
	},
	B:{
		korea:"[카드결제]의 경우는 승인취소후 삭제해주세요. 해당 주문을  정말로 삭제하시겠습니까?",
		english:"Please delete [Credit Card Payment] after 'approval cancelation'. Do you really want to delete that order?",
		indonesian: "Silakan hapus (Pembayaran Kartu Kredit) setelah 'Persetujuan pembatalan'. Apakah Anda ingin menghapus pemesanan itu? ",
		japan: "[カード決済]の場合は承認取り消しの後削除してください. 該当の注文を本当に削除しますか?",
		chinese: ""
	},
	C:{
		korea:"배송정보가 정확하지 않습니다.",
		english:"Delivery information is not accurate",
		indonesian: "Informasi pengiriman tidak akurat ",
		japan: "配送情報が正確ではありません.",
		chinese: ""
	},
	D:{
		korea:"배송중 상태의 경우는 주문정보수정 페이지에서 택배사및 송장번호를 입력하신후 수정하시기 바랍니다.",
		english:"If the status is 'on delivery', please modify it after input the 'invoice number' and the 'delivery company' on the'order information'page ",
		indonesian: "Jika status produk 'sedang dikirim' silakan dimodifikasi setelah memasukkan 'nomor tagihan' dan 'perusahaan pengiriman' di halaman informasi pemesanan ",
		japan: "配送中状態の場合は注文情報修正ページで宅配社及び発送番号を入力した後修正してください.",
		chinese: ""
	},
	E:{
		korea:"입금예정 상태로는 일괄 변경 하실 수 없습니다. \n상태관리가 잘못된 경우는 송장입력 버튼을 클릭하시고 주문상담내역에 정보를 특시사항을 남기신후 상태변경을 하시기 바랍니다.",
		english:"Expected to deposit a batch of state can not be changed. \ n State management of the invoice is incorrect, click the Enter button on your order history information, counseling teuksisahangeul Shin-state changes, please leave. ",
		indonesian: "Diharapkan untuk deposit batch negara tidak dapat diubah. \ N Negara manajemen faktur tidak benar, klik tombol Enter pada informasi sejarah pesanan Anda, konseling teuksisahangeul Shin-negara perubahan, silahkan pergi.",
		japan: "入金予定状態では一括変更することができません. \n状態管理の間違った場合は発送入力ボタンをクリックして注文相談内訳に情報を注意事項を残した後、状態変更をしてください.",
		chinese: ""
	},
	F:{
		korea:"현재상태와 변경을 원하시는 상태가 같은 주문이 한개이상 있으면 상태 변경이 불가능 합니다. \n상태관리가 잘못된 경우는 송장입력 버튼을 클릭하시고 주문상담내역에 정보를 특시사항을 남기신후 상태변경을 하시기 바랍니다.",
		english:"If there are more than one order, 'Current Status'and 'Wish to change the status' whith the same, the status can not be changed.  If you enter an invalid state management, plesae change the status after click 'Enter an invoice' button and enter information on 'Consultation Order History' -'significant to report'",
		indonesian: "Jika terdapat lebih dari satu pemesanan, 'status saat ini' dan 'ingin mengubah status' tidak dapat diubah. Jika Anda memasukkan informasi yang salah, silakan ubah status setelah mengklik tombol 'masukkan tagihan'dan masukkan informasi di kolom 'Histori Konsultasi Pemesanan' - 'signifikan untuk laporan'.",
		japan: "現在状態と変更したい注文が一つ以上あれば状態変更が不可能です. \n状態管理の間違った場合は発送入力ボタンをクリックし、注文相談内訳に注意事項を残した後状態変更をしてください.",
		chinese: ""
	},
	G:{
		korea:"입금예정 상태가 아닌 주문이 한개이상 포함되어 있으면  입금 확인으로 변경이 불가능 합니다. \n상태관리가 잘못된 경우는 송장입력 버튼을 클릭하시고 주문상담내역에 정보를 특시사항을 남기신후 상태변경을 하시기 바랍니다.",
		english:"If there are more than one order, the status is not 'Deposit Expected', can not be changed.  If you enter an invalid state management, plesae change the status after click 'Enter an invoice' button and enter information on 'Consultation Order History' -'significant to report'",
		indonesian: "Jika terdapat lebih dari satu pemesanan, statusnya bukan 'deposit yang diharapkan' dan tidak dapat diubah. Jika Anda memasukkan informasi yang salah, silakan ubah status setelah mengklik tombol 'masukkan tagihan'dan masukkan informasi di kolom 'Histori Konsultasi Pemesanan' - 'signifikan untuk laporan'. ",
		japan: "入金予定状態ではない注文が一つ以上含まれていれば入金確認に変更が不可能です. \n状態管理の間違った場合は発送入力ボタンをクリックし、注文相談内訳に注意事項を残した後状態変更をしてください.",
		chinese: ""
	},
	H:{
		korea:"입금확인 상태가 아닌 주문이 한개이상 포함되어 있으면  배송준비중 으로 변경이 불가능 합니다. \n상태관리가 잘못된 경우는 송장입력 버튼을 클릭하시고 주문상담내역에 정보를 특시사항을 남기신후 상태변경을 하시기 바랍니다.",
		english:"If there are more than one order, the status is not 'Payment Confirmed', can not be changed. If you enter an invalid state management, plesae change the status after click 'Enter an invoice' button and enter information on 'Consultation Order History' -'significant to report' ",
		indonesian: "Jika terdapat lebih dari satu pemesanan, statusnya bukan 'Pembayaran telah dikonfirmasi' dan tidak dapat diubah. Jika Anda memasukkan informasi yang salah, silakan ubah status setelah mengklik tombol 'masukkan tagihan' dan masukkan informasi di kolom 'Histori Konsultasi Pemesanan' - 'signifikan untuk laporan'. ",
		japan: "入金確認状態ではない注文が一つ以上含まれていれば配送準備中に変更が不可能です. \n状態管理の間違った場合は発送入力ボタンをクリックし、注文相談内訳に注意事項を残した後状態変更をしてください.",
		chinese: ""
	},
	I:{
		korea:"주문정보를 삭제 하시겠습니까?",
		english:"Do you want to delete order infomations ?",
		indonesian: "Apakah Anda ingin menghapus informasi memesan? ",
		japan: "注文情報を削除しますか?",
		chinese: ""
	},
	J:{
		korea:"상태변경하실 주문을 한개이상 선택하셔야 합니다.",
		english:"Select one or more of the state to change the order is required.",
		indonesian: "Pilih satu atau lebih dari negara untuk mengubah urutan diperlukan.",
		japan: "状態変更する注文を一つ以上選択しなければいけません.",
		chinese: ""
	},
	K:{
		korea:"배송 업체를 선택해주세요.",
		english:"Please select a delivery company",
		indonesian: "Silakan pilih perusahaan pengiriman",
		japan: "配送業社を選択してください.",
		chinese: ""
	},
	L:{
		korea:"송장번호를 입력해주세요",
		english:"Please enter an invoice number",
		indonesian: "Silakan masukkan nomor tagihan",
		japan: "送狀番号を入力してください",
		chinese: ""
	},
	M:{
		korea:"선택된 주문을 _STATUS_ 상태 변경로 변경 하시겠습니까?",
		english:"Do you want to change the selected orders _STATUS_ status?",
		indonesian: "Apakah Anda ingin mengubah pemesanan yang sudah dipilih _STATUS_  status?",
		japan: "選択された注文を _STATUS_ 状態変更に変更しますか?",
		chinese: ""
	},
	N:{
		korea:"상태정보를 선택해주세요",
		english:"상태정보를 선택해주세요(영문번역필요)",
		indonesian: "상태정보를 선택해주세요(인도네시아 번역필요)",
		japan: "상태정보를 선택해주세요(일어번역필요)",
		chinese: ""
	}
};

language_data["receipt_list.php"] = {	
	A:{
		korea:"현금영수증 신청을 삭제 하시겠습니까?",
		english:"Do you want to delete cash receipts ?",
		indonesian: "Apakah Anda ingin menghapus tanda terima pembayaran tunai? ",
		japan: "現金領収証申し込みを削除しますか?",
		chinese: ""
	},
	B:{
		korea:"아직 주문완료 처리되지 않은 상품이 있습니다. 확인후 주문완료 처리후 현금영수증을 발행하실수 있습니다.",
		english:"There are products incompleted processing. After checking and processing 'order completion', you may be issued cash receipts. ",
		indonesian: "Terdapat produk yang belum selesai diproses. Setelah memeriksa dan memproses 'penyelesaian pemesanan', Anda dapat mengeluarkan tanda terima pembayaran tunai. ",
		japan: "まだ注文完了処理されない商品があります. 確認後、注文完了処理し現金領収証を発行することができます.",
		chinese: ""
	}
};

language_data["account.js"] = {
	A:{
		korea:"배송정보가 정확하지 않습니다.",
		english:"Delivery information is not accurate",
		indonesian: "Informasi pengiriman tidak akurat ",
		japan: "配送情報が正確ではありません.",
		chinese: ""
	},
	B:{
		korea:"정말로 반품 처리 하시겠습니까?",
		english:"Do you really want to return the procduct?",
		indonesian: "Apakah Anda ingin mengembalikan produk? ",
		japan: "本当に返品処理しますか?",
		chinese: ""
	},
	C:{
		korea:"배송중 상태의 경우는 주문정보수정 페이지에서 택배사및 송장번호를 입력하신후 수정하시기 바랍니다",
		english:"If the status is 'on delivery', please modify it after input the 'invoice number' and the 'delivery company' on the'order information'page ",
		indonesian: "Jika status produk 'sedang dikirim' silakan dimodifikasi setelah memasukkan 'nomor tagihan' dan 'perusahaan pengiriman' di halaman informasi pemesanan ",
		japan: "配送中状態の場合は注文情報修正ページで宅配社及び発送番号を入力した後、修正してください",
		chinese: ""
	}
};

language_data["orders.excel2.php"] = {	
	A:{
		korea:"[처리완료] 기록은 삭제할 수 없습니다.",
		english:"[Process Completed] record can not be deleted ",
		indonesian: "(proses selesai) catatan tidak dapat dihapus ",
		japan: "[処理完了] 記録は削除することができません.",
		chinese: ""
	},
	B:{
		korea:"[카드결제]는 [승인취소]와 [주문취소] 처리를 먼저한 후 삭제해주세요.",
		english:"You should delete [Card Payment] after doing [Approval Cancelation] and [Order Cancelation] ",
		indonesian: "Anda harus menghapus (pembayaran menggunakan kartu) setelah melakukan (pembatalan persetujuan) dan (pembatalan pemesanan) ",
		japan: "カード決済]は [承認取り消し]と [注文取り消し] 先に処理をした後削除してください.",
		chinese: ""
	}
};

language_data["orders.goods_list.js"] = {	
	A:{
		korea:"배송정보가 정확하지 않습니다.",
		english:"Delivery information is not accurate.",
		indonesian: "Informasi pengiriman tidak akurat.",
		japan: "配送情報が正確ではありません.",
		chinese: ""
	},
	B:{
		korea:"배송중 상태의 경우는 주문정보수정 페이지에서 택배사및 송장번호를 입력하신후 수정하시기 바랍니다",
		english:"If the status is 'on delivery', please modify it after input the 'invoice number' and the 'delivery company' on the'order information'page.",
		indonesian: "Jika status produk 'sedang dikirim' silakan dimodifikasi setelah memasukkan 'nomor tagihan' dan 'perusahaan pengiriman' di halaman informasi pemesanan",
		japan: "配送中状態の場合は注文情報修正ページで宅配社及び発送番号を入力した後修正してください",
		chinese: ""
	},
	C:{
		korea:"입금예정 상태로는 일괄 변경 하실 수 없습니다. \n상태관리가 잘못된 경우는 송장입력 버튼을 클릭하시고 주문상담내역에 정보를 특시사항을 남기신후 상태변경을 하시기 바랍니다.",
		english:"'Deposit Expected' status can not be a lump change. If you enter an invalid state management, plesae change the status after click 'Enter an invoice' button and enter information on 'Consultation Order History' -'significant to report'.",
		indonesian: "Status 'deposit yang diharapkan' tidak dapat diubah akumulasinya. Jika Anda memasukkan informasi yang salah, silakan ubah status setelah mengklik tombol 'masukkan tagihan' dan masukkan informasi di kolom 'Histori Konsultasi Pemesanan' - 'signifikan untuk laporan'.",
		japan: "入金予定状態では一括変更することができません. \n状態管理の間違った場合は発送入力ボタンをクリックし、注文相談内訳に注意事項を残した後状態変更をしてください.",
		chinese: ""
	},
	D:{
		korea:"현재상태와 변경을 원하시는 상태가 같은 주문이 한개이상 있으면 상태 변경이 불가능 합니다. \n상태관리가 잘못된 경우는 송장입력 버튼을 클릭하시고 주문상담내역에 정보를 특시사항을 남기신후 상태변경을 하시기 바랍니다.",
		english:"If there are more than one order, 'Current Status'and 'Wish to change the status' whith the same, the status can not be changed. If you enter an invalid state management, plesae change the status after click 'Enter an invoice' button and enter information on 'Consultation Order History' -'significant to report'",
		indonesian: "Jika terdapat lebih dari satu pemesanan, 'status saat ini' dan 'ingin mengganti status' tidak dapat diubah. Jika Anda memasukkan informasi yang salah, silakan ubah status setelah mengklik tombol 'masukkan tagihan' dan masukkan informasi di kolom 'Histori Konsultasi Pemesanan' - 'signifikan untuk laporan'.",
		japan: "現在状態と変更したい注文が一つ以上あれば状態変更が不可能です. \n状態管理の間違った場合は発送入力ボタンをクリックし、注文相談内訳に注意事項を残した後状態変更をしてください.",
		chinese: ""
	},
	E:{
		korea:"입금예정 상태가 아닌 주문이 한개이상 포함되어 있으면  입금 확인으로 변경이 불가능 합니다. \n상태관리가 잘못된 경우는 송장입력 버튼을 클릭하시고 주문상담내역에 정보를 특시사항을 남기신후 상태변경을 하시기 바랍니다.",
		english:"If there are more than one order, the status is not 'Deposit Expected', can not be changed.  If you enter an invalid state management, plesae change the status after click 'Enter an invoice' button and enter information on 'Consultation Order History' -'significant to report'",
		indonesian: "Jika terdapat lebih dari satu pemesanan, statusnya bukan 'deposit yang diharapkan' dan tidak dapat diubah. Jika Anda memasukkan informasi yang salah, silakan ubah status setelah mengklik tombol 'masukkan tagihan' dan masukkan informasi di kolom 'Histori Konsultasi Pemesanan' - 'signifikan untuk laporan'. ",
		japan: "入金予定状態ではない注文が一つ以上含まれていれば入金確認に変更が不可能です. \n状態管理の間違った場合は発送入力ボタンをクリックし、注文相談内訳に注意事項を残した後状態変更をしてください",
		chinese: ""
	},
	F:{
		korea:"입금확인 상태가 아닌 주문이 한개이상 포함되어 있으면  배송준비중 으로 변경이 불가능 합니다. \n상태관리가 잘못된 경우는 송장입력 버튼을 클릭하시고 주문상담내역에 정보를 특시사항을 남기신후 상태변경을 하시기 바랍니다.",
		english:"If there are more than one order, the status is not 'Payment Confirmed', can not be changed. If you enter an invalid state management, plesae change the status after click 'Enter an invoice' button and enter information on 'Consultation Order History' -'significant to report' ",
		indonesian: "Jika terdapat lebih dari satu pemesanan, statusnya bukan 'Pembayaran telah dikonfirmasi' dan tidak dapat diubah. Jika Anda memasukkan informasi yang salah, silakan ubah status setelah mengklik tombol 'masukkan tagihan' dan masukkan informasi di kolom 'Histori Konsultasi Pemesanan' - 'signifikan untuk laporan'. ",
		japan: "入金予定状態ではない注文が一つ以上含まれていれば入金確認に変更が不可能です. \n状態管理の間違った場合は発送入力ボタンをクリックし、注文相談内訳に注意事項を残した後状態変更をしてください",
		chinese: ""
	},
	G:{
		korea:"상태변경하실 주문을 한개이상 선택하셔야 합니다. ",
		english:"Select one or more of the state to change the order is required. ",
		indonesian: "Pilih satu atau lebih dari negara untuk mengubah urutan diperlukan. ",
		japan: "状態変更する注文を一つ以上選択しなければなりません.",
		chinese: ""
	},
	H:{
		korea:"[카드결제]의 경우는 승인취소후 삭제해주세요. 해당 주문을  정말로 삭제하시겠습니까? ",
		english:"Please delete [Credit Card Payment] after 'approval cancelation'. Do you really want to delete that order? ",
		indonesian: "Silakan hapus ( Pembayaran Kartu Kredit) setelah 'Persetujuan pembatalan'. Apakah Anda ingin menghapus pemesanan itu?  ",
		japan: "[カード決済]の場合は承認取り消しの後削除してください. 該当の注文を本当に削除しますか?",
		chinese: ""
	},
	I:{
		korea:"해당 주문을 입금확인 처리 하시겠습니까? ",
		english:"Do you want to process that order 'Deposit Confirmed'? ",
		indonesian: "Apakah Anda ingin memproses pemesanan 'Konfirmasi Deposit' ?  ",
		japan: "該当の注文を入金確認処理しますか?",
		chinese: ""
	},
	J:{
		korea:"해당 주문상품을 배송준비중처리 하시겠습니까? ",
		english:"Do you want to process that order 'Ready for Delivery'? ",
		indonesian: "Apakah Anda ingin memproses pemesanan 'Siap dikirim' ?  ",
		japan: "該当の注文商品を配送準備中処理しますか?",
		chinese: ""
	},
	K:{
		korea:"해당 주문상품을 배송완료 처리 하시겠습니까? ",
		english:"Do you want to process that order 'Delivery Completed '?  ",
		indonesian: "Apakah Anda ingin memproses pemesanan  'Pengiriman Selesai'?  ",
		japan: "該当の注文商品を配送完了処理しますか?",
		chinese: ""
	},
	L:{
		korea:"해당 주문상품을 취소승인을 하시겠습니까?  ",
		english:"Do you want to process that order 'Cancle Completed'?  ",
		indonesian: "Apakah Anda ingin memproses pemesanan 'Pembatalan selesai' ?  ",
		japan: "該当の注文商品を取り消し承認をしますか?",
		chinese: ""
	},
	M:{
		korea:"해당 주문상품을 교환승인을 하시겠습니까?  ",
		english:"Do you want to process that order 'Exchange Completed'? ",
		indonesian: "Apakah Anda ingin memproses pemesanan 'Pertukaran selesai'?  ",
		japan: "該当の注文商品を交換承認をしますか?",
		chinese: ""
	},
	N:{
		korea:"해당 주문상품을 회수완료처리 하시겠습니까?  ",
		english:"Do you want to process that order 'Returns Completed'? ",
		indonesian: "Apakah Anda ingin memproses pemesanan 'Pengembalian selesai'?  ",
		japan: "該当の注文商品を回収完了処理しますか?",
		chinese: ""
	},
	O:{
		korea:"해당 주문상품을 반품승인처리 하시겠습니까?  ",
		english:"Do you want to process that order 'Returns Approved'?  ",
		indonesian: "Apakah Anda ingin memproses pemesanan 'Pengembalian Disetujui' ?  ",
		japan: "該当の注文商品を返品承認処理しますか?",
		chinese: ""
	},
	P:{
		korea:"해당 주문상품을 반품회수완료처리 하시겠습니까?  ",
		english:"Do you want to process that order 'Returns Completed'?   ",
		indonesian: "Apakah Anda ingin memproses pemesanan 'Pengembalian selesai'?  ",
		japan: "該当の注文商品を返品回収完了処理しますか?",
		chinese: ""
	},
	Q:{
		korea:"해당 주문상품을 환불신청 처리 하시겠습니까?  ",
		english:"Do you want to process that order 'Refund Requested'?   ",
		indonesian: "Apakah Anda ingin memproses pemesanan 'Permintaan Refund' ?  ",
		japan: "該当の注文商品を払い戻し申し込み処理しますか?",
		chinese: ""
	},
	R:{
		korea:"해당 주문상품을 환불완료 처리 하시겠습니까?  ",
		english:"Do you want to process that order 'Refund Completed'?   ",
		indonesian: "Apakah Anda ingin memproses pemesanan 'Refund Selesai' ?  ",
		japan: "該当の注文商品を払い戻し完了処理しますか?",
		chinese: ""
	},
	S:{
		korea:"해당 주문상품을 반품승인처리 하시겠습니까?  ",
		english:"===> Do you want to process that order 'Returns Approved'?   ",
		indonesian: "Apakah Anda ingin memproses pemesanan 'Pengembalian Disetujui' ?  ",
		japan: "該当の注文商品を返品承認処理なさいますか?",
		chinese: ""
	},
	T:{
		korea:"배송 업체를 선택해주세요.",
		english:"Please select a delivery company",
		indonesian: "Silakan pilih perusahaan pengiriman",
		japan: "配送業社を選択してください.",
		chinese: ""
	},
	U:{
		korea:"송장번호를 입력해주세요",
		english:"Please enter an invoice number",
		indonesian: "Silakan masukkan nomor tagihan",
		japan: "送り状番号を入力してください",
		chinese: ""
	},
	V:{
		korea:"선택된 주문상품을 배송처리 하시겠습니까? ",
		english:"Do you want to process 'On Delivery' for selected orders? ",
		indonesian: "Apakah Anda ingin memproses 'Sedang Dikirim' untuk pemesanan yang telah dipilih?",
		japan: "選択された注文商品を配送処理しますか?",
		chinese: ""
	},
	W:{
		korea:"배송완료 처리할 상품을 선택해주세요 ",
		english:"Please select a product for processing 'Delivery Completed'  ",
		indonesian: "Silakan pilih produk untuk memproses 'pengiriman selesai'. ",
		japan: "配送完了処理する商品を選択してください",
		chinese: ""
	}
};

language_data["product_resize.php"] = {	
	A:{
		korea:"배너를 정말로 삭제하시겠습니까? ",
		english:"Do you really want to delete the banner?  ",
		indonesian: "Apakah Anda ingin menghapus banner? ",
		japan: "バナーを本当に削除しますか?",
		chinese: ""
	},
	B:{
		korea:"정말로 삭제하시겠습니까? ",
		english:"Do you really want to delete? ",
		indonesian: "Apakah Anda ingin menghapus?  ",
		japan: "本当に削除しますか?",
		chinese: ""
	},
	C:{
		korea:"해당 상품에 대한 정보를 수정하시겠습니까? ",
		english:"Do you want to modify the information ? ",
		indonesian: "Apakah Anda ingin memodifikasi informasi?  ",
		japan: "該当の商品に対する情報を修正しますか?",
		chinese: ""
	},
	D:{
		korea:"삭제하실 제품을 한개이상 선택하셔야 합니다. ",
		english:"Select the product you want to delete one or more must be. ",
		indonesian: "Pilih produk yang anda ingin menghapus satu atau lebih harus  ",
		japan: "削除する製品を一つ以上選択しなければなりません.",
		chinese: ""
	},
	E:{
		korea:"선택하신 상품을 정말로 삭제하시겠습니까? 삭제하시면 상품과 관련된 모든 데이타가 삭제되게 됩니다. ",
		english:"Do you really want to delete the selected product? All data relating to products will be deleted. ",
		indonesian: "Apakah Anda ingin menghapus produk terpilih? Semua data mengenai produk itu akan dihapus.  ",
		japan: "選択した商品を本当に削除しますか? 削除すれば商品と係わるすべてのデータが削除されます.",
		chinese: ""
	},
	F:{
		korea:"수정하실 제품을 한개이상 선택하셔야 합니다. ",
		english:"Select the product you want to modify one or more is required ",
		indonesian: "Menyempurnakan produk yang akan perlu memilih satu atau lebih ",
		japan: "修正する製品を一つ以上選択しなければなりません.",
		chinese: ""
	},
	G:{
		korea:"검색상품 전체에 정보변경을 하시겠습니까?  ",
		english:"Do you want to change information to all searched products? ",
		indonesian: "Apakah Anda ingin mengubah informasi semua produk dicari? ",
		japan: "検索商品全体に情報変更をしますか?",
		chinese: ""
	},
	H:{
		korea:"선택된 제품이 없습니다. 변경하시고자 하는 상품을 선택하신 후 저장 버튼을 클릭해주세요  ",
		english:"There are no products selected. The product you wish to change your choice, please click the Save button ",
		indonesian: "Tidak ada produk yang dipilih. Produk yang Anda ingin mengubah pilihan Anda, silakan klik tombol Simpan  ",
		japan: "選択された製品がありません. 変更する商品を選択した後保存ボタンをクリックしてください",
		chinese: ""
	}
};

language_data["region.php"] = {	
	A:{
		korea:"해당지역  정보를 정말로 삭제하시겠습니까?",
		english:"Do you really want to delete the information? ",
		indonesian: "Apakah Anda ingin menghapus informasi? ",
		japan: "該当地域情報を本当に削除しますか?",
		chinese: ""
	}
};

language_data["manufacturer.php"] = {	
	A:{
		korea:"해당 자동차 제조사  정보를 정말로 삭제하시겠습니까? ",
		english:"Do you really want to delete the manufacturer information? ",
		indonesian: "Apakah Anda ingin menghapus informasi mengenai pabrik? ",
		japan: "該当の自動車製造社情報を本当に削除しますか?",
		chinese: ""
	}
};

language_data["product_input_excel.js"] = {	
	A:{
		korea:"정말로 삭제하시겠습니까? ",
		english:"Do you really want to delete? ",
		indonesian: "Apakah Anda ingin menghapus?  ",
		japan: "本当に削除しますか?",
		chinese: ""
	},
	B:{
		korea:"삭제하실 제품을 한개이상 선택하셔야 합니다.  ",
		english:"You must select one or more to delete.  ",
		indonesian: "Anda harus memilih satu atau lebih untuk menghapus.  ",
		japan: "削除する製品を一つ以上選択しなければなりません.",
		chinese: ""
	}
};

language_data["vechile_grade.php"] = {	
	A:{
		korea:"해당자동차 등급  정보를 정말로 삭제하시겠습니까? ",
		english:"Do you really want to delete the information? ",
		indonesian: "Apakah Anda ingin menghapus informasi?  ",
		japan: "該当者動車等級情報を本当に削除しますか?",
		chinese: ""
	}
};

language_data["model.php"] = {	
	A:{
		korea:"해당자동차 모델  정보를 정말로 삭제하시겠습니까? ",
		english:"Do you really want to delete the information? ",
		indonesian: "Apakah Anda ingin menghapus informasi?  ",
		japan: "該当者動車モデル情報を本当に削除しますか?",
		chinese: ""
	}
};

language_data["buyingServiceInfo.php"] = {	
	A:{
		korea:"환율/수수료 정보가 변경되면 구매대행 상품 전체 가격이 재 산정되게됩니다. 환율/수수료 정보를 정말로 변경하시겠습니까?  ",
		english:"If 'Exchange rates / Fees' information changes, the entire price is re-calculated. Do you really want to change 'Exchange rates / Fees' information?  ",
		indonesian: "Jika informasi mengenai 'Exchange Rates / fees' berubah, semua harga terkalkulasi ulang. Apakah Anda ingin mengubah informasi mengenai 'Exchange rates / fees'?  ",
		japan: "為替/手数料情報が変更されれば購買代行商品全体価格が再算定されるようになります. 為替/手数料情報を本当に変更しますか?",
		chinese: ""
	},
	B:{
		korea:"해당 구매대행 환율/수수료 정보를 정말로 삭제하시겠습니까?  ",
		english:"Do you really want to change 'Exchange rates / Fees' information? ",
		indonesian: "Apakah Anda ingin mengubah informasi mengenai 'Exchange rates / Fees'?  ",
		japan: "該当の購買代行為替/手数料情報を本当に削除しますか?",
		chinese: ""
	},
	C:{
		korea:"변경된 환율/수수료 정보가 없습니다. 변경된 정보가 없으면 저장이 되지 않습니다.  ",
		english:"No 'Exchange rates / Fees' information found. If there is no information that has changed is not saved.  ",
		indonesian: "Informasi 'Exchange rates/Fees' tidak dapat ditemukan. Jika tidak ada informasi yang berubah maka tidak akan disimpan.  ",
		japan: "変更された為替/手数料情報がないです. 変更された情報がなければ保存されません.",
		chinese: ""
	}
};

language_data["product_input_excel.act_ing.php"] = {	
	A:{
		korea:"상품등록이 완료되었습니다.  ",
		english:"Product registration is complete ",
		indonesian: "Pendaftaran produk selesai  ",
		japan: "商品登録が完了しました.",
		chinese: ""
	}
};

language_data["product_input.js"] = {	
	A:{
		korea:"현금사용시 적립율이 입력되지 않았습니다.  ",
		english:"Accumulation rate has not been entered, when using cash.  ",
		indonesian: "Tingkat akumulasi belum dimasukkan saat membayar tunai.  ",
		japan: "現金使用の時積立率が入力されていません.",
		chinese: ""
	},
	B:{
		korea:"카드사용시 적립율이 입력되지 않았습니다.  ",
		english:"Accumulation rate has not been entered, when using credit card. ",
		indonesian: "Tingkat akumulasi belum dimasukkan saat menggunakan kartu kredit.  ",
		japan: "カード社用の時積立率が入力されていません.",
		chinese: ""
	},
	C:{
		korea:"판매가격이 입력되지 않았습니다.  ",
		english:"Selling price has not been entered ",
		indonesian: "Harga jual belum dimasukkan.  ",
		japan: "販売価格が入力されていません.",
		chinese: ""
	},
	D:{
		korea:"카드수수료가 입력되지 않았습니다.  ",
		english:"Credit card fee has not been entered ",
		indonesian: "Biaya kartu kredit belum dimasukkan  ",
		japan: "カード手数料が入力されていません.",
		chinese: ""
	},
	E:{
		korea:"제품명이 입력되지 않았습니다.  ",
		english:"Product name has not been entered ",
		indonesian: "Nama produk belum dimasukkan  ",
		japan: "製品名が入力されていません.",
		chinese: ""
	},
	F:{
		korea:"가격에 대한 정보가 변경되었습니다.  ",
		english:"Price information has been changed. ",
		indonesian: "Informasi harga sudah diubah.  ",
		japan: "価格に対する情報が変更されました.",
		chinese: ""
	},
	G:{
		korea:"공급가격이 입력되지 않았습니다.  ",
		english:"Supply price has not been entered ",
		indonesian: "Harga suplier belum dimasukkan  ",
		japan: "供給価格が入力されていません.",
		chinese: ""
	},
	H:{
		korea:"제품소개가 입력되지 않았습니다.  ",
		english:"Product intriduction has not been entered ",
		indonesian: "Perkenalan produk belum dimasukkan  ",
		japan: "製品紹介が入力されていません.",
		chinese: ""
	},
	I:{
		korea:"옵션구분값을 입력해주세요.  ",
		english:"Please enter 'option classification value'. ",
		indonesian: "Silakan masukkan 'Nilai pilihan klasifikasi'  ",
		japan: "オプション仕分け値を入力してください.",
		chinese: ""
	},
	J:{
		korea:"옵션별 비회원가를 입력해주세요.  ",
		english:"Please enter the 'Nonmember price'for each options. ",
		indonesian: "Silakan masukkan 'Harga Bukan Anggota' pada setiap pilihan.  ",
		japan: "オプション別悲懐原価を入力してください.",
		chinese: ""
	},
	K:{
		korea:"옵션이름을 선택해주세요.  ",
		english:"Please select a option name. ",
		indonesian: "Silakan pilih nama pilihan.  ",
		japan: "オプション名前を選択してください.",
		chinese: ""
	},
	L:{
		korea:"옵션별 회원가를 입력해주세요.  ",
		english:"Please enter the 'Member price'for each options. ",
		indonesian: "Silakan masukkan 'Harga Anggota' pada setiap pilihan.  ",
		japan: "オプション別に原価を入力してください.",
		chinese: ""
	},
	M:{
		korea:"옵션별 딜러가를 입력해주세요.  ",
		english:"Please enter the 'Dealer price'for each options. ",
		indonesian: "Silakan masukkan 'Harga agen' pada setiap pilihan  ",
		japan: "オプション別代理価格を入力してください.",
		chinese: ""
	},
	N:{
		korea:"옵션별 대리점가를 입력해주세요.  ",
		english:"Please enter the 'Branch store price'for each options. ",
		indonesian: "Silakan masukkan 'Harga toko cabang' pada setiap pilihan.  ",
		japan: "オプション別代理価格を入力してください.",
		chinese: ""
	},
	O:{
		korea:"문자는 사용할 수 없습니다.  ",
		english:"Letters can not be used. ",
		indonesian: "Huruf tidak dapat digunakan.  ",
		japan: "文字は使うことができません.",
		chinese: ""
	},
	P:{
		korea:"자릿수는 정수로만 구분합니다.  ",
		english:"Digits are separated only by a constant. ",
		indonesian: "Karakter dipisah hanya oleh konstan.  ",
		japan: "桁は定数だけ区分します.",
		chinese: ""
	},
	Q:{
		korea:"숫자만 입력 하세요.  ",
		english:"Please enter numbers only. ",
		indonesian: "Silakan masukkan angka saja.  ",
		japan: "数字だけ入力してください.",
		chinese: ""
	},
	R:{
		korea:"구매단가(공급가)를 입력후 복사 버튼을 클릭해주세요.  ",
		english:"Please click the Copy button after enter the purchased unit price (supply price). ",
		indonesian: "Silakan klik tombol Salin setelah memasukkan harga unit yang dibeli (suplier price).  ",
		japan: "購買単価(供給価格)を入力の後コピーボタンをクリックしてください.",
		chinese: ""
	},
	S:{
		korea:"정가를 입력후 복사 버튼을 클릭해주세요.  ",
		english:"Please click the Copy button after enter the fixed price. ",
		indonesian: "Silakan klik tombol Salin setelah memasukkan harga tetap.  ",
		japan: "定価を入力の後コピーボタンをクリックしてください.",
		chinese: ""
	},
	T:{
		korea:"회원가를 입력후 복사 버튼을 클릭해주세요.  ",
		english:"Please click the Copy button after enter the member price. ",
		indonesian: "Silakan klik tombol Salin setelah memasukkan harga untuk anggota.  ",
		japan: "刺身原価を入力の後コピーボタンをクリックしてください.",
		chinese: ""
	},
	U:{
		korea:"딜러가를 입력후 복사 버튼을 클릭해주세요.  ",
		english:"Please click the Copy button after enter the dealer price. ",
		indonesian: "Silakan klik tombol Salin setelah memasukkan harga agen.  ",
		japan: "代理価格を入力の後コピーボタンをクリックしてください.",
		chinese: ""
	}
};

language_data["category.save.php"] = {	
	A:{
		korea:"카테고리 정보가 정상적으로 수정되었습니다.  ",
		english:"Category information has been modified successfully.  ",
		indonesian: "Informasi kategori berhasil dimodifikasi.  ",
		japan: "カテゴリー情報が正常に修正されました.",
		chinese: ""
	},
	B:{
		korea:"삭제 되었습니다.  ",
		english:"It has been deleted. ",
		indonesian: "Sudah dihapus.  ",
		japan: "削除されました.",
		chinese: ""
	},
	C:{
		korea:"하부 카테고리가 존제 합니다.하부 카테고리를 먼저 삭제하신후 다시 시도해 주세요.  ",
		english:"There is a sub-category. Please try again after deleting sub-category. ",
		indonesian: "Sudah ada sub-kategori. Silakan mencoba lagi setelah menghapus sub-kategori.  ",
		japan: "下部カテゴリーがあります.下部カテゴリーを先に削除後、再度行ってください.",
		chinese: ""
	},
	D:{
		korea:"카테고리 정보가 정확하지 않습니다. 상위 카테고리를 선택해 주세요.  ",
		english:"Category information is not accurate. Please select the parent category. ",
		indonesian: "Kategori informasi tidak akurat. Silakan pilih kategori induk.  ",
		japan: "カテゴリ情報が正確ではありません. 親カテゴリを選択してください. ",
		chinese: ""
	}
};

language_data["pinfo.php"] = {	
	A:{
		korea:" 은 sold out 상품 입니다.  ",
		english:"is sold out product.  ",
		indonesian: "'produk habis terjual'  ",
		japan: "は sold out 商品です.",
		chinese: ""
	},
	B:{
		korea:" 의 구매를 원하시면 마이데조로로 문의해 주시기 바랍니다.  ",
		english:"If you want to purchase, please contact at Mypage. ",
		indonesian: "Jika Anda ingin membeli silakan hubungi di Halaman Saya.  ",
		japan: "の購買をする場合、マイページに問い合わせてください.",
		chinese: ""
	},
	C:{
		korea:" 을 선택하세요.  ",
		english:"Select ",
		indonesian: "Pilih  ",
		japan: "を選択してください.",
		chinese: ""
	}
};

language_data["product_list.js"] = {	
	A:{
		korea:"검색상품 전체에 대한 적용은 검색후 가능합니다.  ",
		english:"Applying for all searched product can be applied after a search.  ",
		indonesian: "Penerapan untuk semua Produk Dicari dapat diterapkan setelah mencari produk.  ",
		japan: "検索商品全体に対する適用は検索後可能です.",
		chinese: ""
	},
	B:{
		korea:"선택된 제품이 없습니다. 변경하시고자 하는 상품을 선택하신 후 저장 버튼을 클릭해주세요.  ",
		english:"There are no products selected. Please click the Save button after selecting items you wish to change. ",
		indonesian: "Tidak ada produk yang dipilih. Silakan klik tombol Simpan setelah memilih barang yang ingin diubah.  ",
		japan: "選択された製品がありません. 変更する商品を選択後、保存ボタンをクリックしてください.",
		chinese: ""
	},
	C:{
		korea:"해당 상품에 대한 정보를 수정하시겠습니까? ",
		english:"Do you want to modify the information ? ",
		indonesian: "Apakah Anda ingin memodifikasi informasi?  ",
		japan: "該当の商品に対する情報を修正しますか?",
		chinese: ""
	},
	D:{
		korea:"삭제하실 제품을 한개이상 선택하셔야 합니다 ",
		english:"ThSelect the product you want to delete one or more must be ",
		indonesian: "Pilih produk yang anda ingin menghapus satu atau lebih harus  ",
		japan: "削除する製品を一つ以上選択しなければならないです",
		chinese: ""
	},
	E:{
		korea:"선택하신 상품을 정말로 삭제하시겠습니까? 삭제하시면 상품과 관련된 모든 데이타가 삭제되게 됩니다. ",
		english:"Do you really want to delete the selected product? All data relating to products will be deleted. ",
		indonesian: "Apakah Anda ingin menghapus produk terpilih? Semua data mengenai produk itu akan dihapus.  ",
		japan: "選択した商品を本当に削除しますか? 削除すれば商品と係わるすべてのデータが削除されます.",
		chinese: ""
	},
	F:{
		korea:"수정하실 제품을 한개이상 선택하셔야 합니다. ",
		english:"Select the product you want to modify one or more is required ",
		indonesian: "Menyempurnakan produk yang akan perlu memilih satu atau lebih ",
		japan: "修正する製品を一つ以上選択しなければなりません.",
		chinese: ""
	},
	G:{
		korea:"검색상품 전체에 정보변경을 하시겠습니까?  ",
		english:"Do you want to change information to all searched products? ",
		indonesian: "Apakah Anda ingin mengubah informasi semua produk dicari? ",
		japan: "検索商品全体に情報変更をなさいますか?",
		chinese: ""
	},
	H:{
		korea:"선택된 제품이 없습니다. 변경하시고자 하는 상품을 선택하신 후 저장 버튼을 클릭해주세요  ",
		english:"There are no products selected. The product you wish to change your choice, please click the Save button ",
		indonesian: "Tidak ada produk yang dipilih. Produk yang Anda ingin mengubah pilihan Anda, silakan klik tombol Simpan  ",
		japan: "選択された製品がありません. 変更する商品を選択後保存ボタンをクリックしてください",
		chinese: ""
	},
	J:{
		korea:"정말로 삭제하시겠습니까? ",
		english:"Do you really want to delete? ",
		indonesian: "Apakah Anda ingin menghapus?  ",
		japan: "本当に削除しますか?",
		chinese: ""
	},
	K:{
		korea:"검색상품 전체에 대한 적용은 검색후 가능합니다. ",
		english:"Applying for all searched product can be applied after a search. ",
		indonesian: "Penerapan untuk semua Produk Dicari dapat diterapkan setelah mencari produk.  ",
		japan: "検索商品全体に対する適用は検索後可能です.",
		chinese: ""
	}
};

language_data["region.php"] = {	
	A:{
		korea:"2차 지역을 등록하기 위해서는 1차지역을 반드시 선택하셔야 합니다.  ",
		english:"To register a second region, you must select a first region.  ",
		indonesian: "Untuk mendaftarkan wilayah kedua, Anda harus memilih wilayah pertama terlebih dahulu.  ",
		japan: "2次地域を登録するためには 1次地域を必ず選択しなければならないです.",
		chinese: ""
	},
	B:{
		korea:"등록하시고자 하는 스페셜카테고리 지역명을 입력해주세요.  ",
		english:"Please enter a special category name of region you wish to register. ",
		indonesian: "Silakan masukkan nama kategori khusus dari wilayah yang ingin Anda daftarkan.  ",
		japan: "登録するスペシャルカテゴリー地域名を入力してください.",
		chinese: ""
	},
	C:{
		korea:"해당지역 정보를 정말로 삭제하시겠습니까?  ",
		english:"Are you sure your local information? ",
		indonesian: "Apakah Anda yakin informasi lokal Anda?  ",
		japan: "該当地域情報を本当に削除しますか?",
		chinese: ""
	}
};

language_data["buyingService_pricehistory.php"] = {	
	A:{
		korea:"변경된 환율/수수료 정보가 없습니다. 변경된 정보가 없으면 저장이 되지 않습니다.  ",
		english:"No 'Exchange rates / Fees' information found. If there is no information that has changed is not saved.  ",
		indonesian: "Informasi 'Exchange rates / Fees' tidak dapat ditemukan. Jika tidak ada informasi yang diubah, maka tidak disimpan.  ",
		japan: "変更された為替/手数料情報がありません. 変更された情報がなければ保存できません.",
		chinese: ""
	},
	B:{
		korea:"환율/수수료 정보가 변경되면 구매대행 상품 전체 가격이 재 산정되게됩니다. 환율/수수료 정보를 정말로 변경하시겠습니까? ",
		english:"If 'Exchange rates / Fees' information changes, the entire price is re-calculated. Do you really want to change 'Exchange rates / Fees' information? ",
		indonesian: "Jika informasi mengenai 'Exchange Rates / fees' berubah, semua harga terkalkulasi ulang. Apakah Anda ingin mengubah informasi mengenai 'Exchange rates / fees'? ",
		japan: "為替/手数料情報が変更されれば購買代行商品全体価格が再算定されるようになります. 為替/手数料情報を本当に変更しますか?",
		chinese: ""
	},
	C:{
		korea:"해당그룹 정보를 정말로 삭제하시겠습니까? ",
		english:"Do you really want to delete the information? ",
		indonesian: "Apakah Anda ingin menghapus informasi?  ",
		japan: "該当グループ情報を本当に削除しますか?",
		chinese: ""
	}
};

language_data["goods_input.php"] = {	
	A:{
		korea:"카테고리를 선택해주세요.  ",
		english:"No 'Please select a category.  ",
		indonesian: "silakan pilih kategori.  ",
		japan: "カテゴリーを選択してください.",
		chinese: ""
	},
	B:{
		korea:"이미등록된 카테고리 입니다.  ",
		english:"The category is already registered.  ",
		indonesian: "Kategori sudah terdaftar.  ",
		japan: "既に登録されたカテゴリーです.",
		chinese: ""
	},
	C:{
		korea:"가격 정보를 수정하시고자 할대는 MD와 상의해 주세요.  ",
		english:"Price Information / Video manual is preparing /If you want to modify price information please cotact with MD.  ",
		indonesian: "Informasi harga/Manual video sedang disiapkan/ Jika Anda ingin memodifikasi informasi harga silakan menghubungi MD.  ",
		japan: "価格情報を修正する時はMDと相談してください.",
		chinese: ""
	},
	D:{
		korea:"입점업체는 공급가격만 입력하실수 있습니다.  ",
		english:"Vendors may enter only a supply price.  ",
		indonesian: "Vendor hanya dapat memasukkan harga suplier.  ",
		japan: "入店業社は供給価格だけ入力することができます.",
		chinese: ""
	},
	E:{
		korea:"상품등록후 확인하실수 있습니다.  ",
		english:"You can check after registering products. ",
		indonesian: "Anda dapat memeriksa setelah mendaftarkan produk.  ",
		japan: "商品登録の後確認することができます.",
		chinese: ""
	},
	F:{
		korea:"비회원가를 입력후 적립금이 계산됩니다.  ",
		english:"Mileage points is calculated after inputting nonmember price.  ",
		indonesian: "Kupon poin terkalkulasi setelah memasukkan harga bukan anggota.  ",
		japan: "非会員原価を入力の後積立金が計算されます.",
		chinese: ""
	},
	G:{
		korea:"마지막 한개는 삭제 하실 수 없습니다.  ",
		english:"You can not delete the last one / Double-click on that line will be deleted.  ",
		indonesian: "Anda tidak dapat menghapus yang terakhir/ Double-klik pada kalimat itu akan dihapus.  ",
		japan: "最後の一つは削除することができません.",
		chinese: ""
	},
	H:{
		korea:"해당 옵션 구분정보가 삭제 되었습니다.  ",
		english:"You can not delete the last one.  ",
		indonesian: "Anda tidak dapat menghapus yang terakhir.  ",
		japan: "該当のオプション仕分け情報が削除されました.",
		chinese: ""
	},
	I:{
		korea:"동영상 메뉴얼 준비중입니다.  ",
		english:"Video manual is preparing.  ",
		indonesian: "Manual video sedang disiapkan.  ",
		japan: "動画マニュアル準備中です.",
		chinese: ""
	}
};

language_data["product_bsgoods.act.php"] = {	
	A:{
		korea:"해당 URL 에는 상품 리스트 정보가 존재 하지 않습니다. 기본 URL 을 다시 한번 확인해주시기 바랍니다.  ",
		english:"The product list information do not exist in the URL. Please check again at the base URL.  ",
		indonesian: "Informasi daftar produk tidak ada di URL. Silakan periksa kembali di URL dasar.  ",
		japan: "該当の URL には商品リスト情報が存在しません. 基本 URL をもう一度確認してください.",
		chinese: ""
	},
	B:{
		korea:"등록완료  ",
		english:"Registration is done.  ",
		indonesian: "Pendafataran selesai.  ",
		japan: "登録完了",
		chinese: ""
	}
};


language_data["region.act.php"] = {	
	A:{
		korea:"해당 카테고리에 이미 등록되어 있는 상품입니다.  ",
		english:"The product has already been registered in that category.  ",
		indonesian: "Produk sudah terdaftar pada kategori itu.  ",
		japan: "該当のカテゴリーに既に登録されている商品です.",
		chinese: ""
	}
};

language_data["option.pop.php"] = {	
	A:{
		korea:"옵션이름을 입력해주세요 ",
		english:"Enter a name of option.  ",
		indonesian: "Masukkan nama pilihan.  ",
		japan: "オプション名前を入力してください",
		chinese: ""
	},
	B:{
		korea:"상품등록이 완료되었습니다.  ",
		english:"Product has been registered successfully.  ",
		indonesian: "Produk sudah berhasil didaftarkan.  ",
		japan: "商品登録が完了しました.",
		chinese: ""
	}
};

language_data["categoryorder.php"] = {	
	A:{
		korea:"더이상 진행할 방향이 없습니다. ",
		english:"There is no way to proceed  ",
		indonesian: "Tidak dapat diproses  ",
		japan: "これ以上進行する方向がありません.",
		chinese: ""
	},
	B:{
		korea:"상품평 작성이 완료 되었습니다.  ",
		english:"Product review is completed.  ",
		indonesian: "Review produk selesai.  ",
		japan: "商品レビューの作成が完了しました.",
		chinese: ""
	}
};

language_data["buyingService.act.php"] = {	
	A:{
		korea:"이미 상품정보가 입력되었습니다.  ",
		english:"Product information has already been entered.  ",
		indonesian: "Informasi produk sudah dimasukkan.  ",
		japan: "商品情報が入力されました.",
		chinese: ""
	}
};

language_data["product_input.php"] = {	
	A:{
		korea:"동영상 메뉴얼 준비중입니다  ",
		english:"Video menual is preparing.  ",
		indonesian: "Manual video sedang disiapkan  ",
		japan: "動画マニュアル準備中です",
		chinese: ""
	},
	C:{
		korea:"입점업체는 공급가격만 입력하실수 있습니다.  ",
		english:"Vendors may enter only a supply price.  ",
		indonesian: "Vendor hanya dapat memasukkan harga suplier.  ",
		japan: "入店業社は供給価格だけ入力することができます.",
		chinese: ""
	},
	G:{
		korea:"비회원가를 입력후 적립금이 계산됩니다.  ",
		english:"Mileage points is calculated after inputting nonmember price.  ",
		indonesian: "Kupon poin dikalkulasi setelah memasukkan harga Bukan Anggota.  ",
		japan: "非会員原価を入力の後積立金が計算されます.",
		chinese: ""
	}
};

language_data["category_save.php"] = {	
	A:{
		korea:"상품분류를 추가하였습니다. ",
		english:"Product classification has been added.  ",
		indonesian: "Klasifikasi produk sudah ditambah.  ",
		japan: "商品分類を追加しました.",
		chinese: ""
	},
	B:{
		korea:"상품분류 정보를 수정하였습니다.  ",
		english:"Product category has been modified.  ",
		indonesian: "Produk kategori sudah dimodifikasi.  ",
		japan: "商品分類情報を修正しました.",
		chinese: ""
	},
	C:{
		korea:"하위분류가 존재합니다. 삭제하실 수 없습니다.  ",
		english:"There is a sub-category. You can not delete.  ",
		indonesian: "Ada sub-kategori. Anda tidak dapat menghapus.  ",
		japan: "下位分類が存在します. 削除することができません.",
		chinese: ""
	},
	D:{
		korea:"현재분류에 상품이 존재합니다. 삭제하실 수 없습니다.  ",
		english:"There is a product in this category. You can not delete.  ",
		indonesian: "Ada produk di dalam kategori ini. Anda tidak dapat menghapus.  ",
		japan: "現在分類に商品が存在します. 削除することができません.",
		chinese: ""
	},
	E:{
		korea:"선택하신 분류를 삭제하였습니다.  ",
		english:"The selected category has been deleted.  ",
		indonesian: "Kategori yang dipilih sudah dihapus.  ",
		japan: "選択した分類を削除しました.",
		chinese: ""
	}
};

language_data["goods_input.js"] = {	
	A:{
		korea:"판매가격이 입력되지 않았습니다. ",
		english:"Selling price has not been entered.  ",
		indonesian: "Harga jual belum dimasukkan.  ",
		japan: "販売価格が入力されていません.",
		chinese: ""
	},
	B:{
		korea:"현금사용시 적립율이 입력되지 않았습니다.  ",
		english:"Accumulation rate has not been entered, when using cash.  ",
		indonesian: "Tingkat akumulasi belum dimasukkan saat membayar tunai.  ",
		japan: "現金使用時の積立率が入力されていません.",
		chinese: ""
	},
	C:{
		korea:"카드사용시 적립율이 입력되지 않았습니다.  ",
		english:"Accumulation rate has not been entered, when using credit card.  ",
		indonesian: "Tingkat akumulasi belum dimasukkan saat menggunakan kartu kredit.  ",
		japan: "カード使用時の積立率が入力されていません.",
		chinese: ""
	},
	D:{
		korea:"카드수수료가 입력되지 않았습니다.  ",
		english:"Credit card fee has not been entered.  ",
		indonesian: "Biaya kartu kredit belum dimasukkan.  ",
		japan: "カード手数料が入力されていません.",
		chinese: ""
	},
	E:{
		korea:"카테고리를 선택해주세요.  ",
		english:"Select a category.  ",
		indonesian: "Pilih kategori.  ",
		japan: "カテゴリーを選択してください.",
		chinese: ""
	},
	F:{
		korea:"공급가격이 입력되지 않았습니다.  ",
		english:"Supply price has not been entered.  ",
		indonesian: "Harga suplier belum dimasukkan.  ",
		japan: "供給価格が入力されていません.",
		chinese: ""
	},
	G:{
		korea:"제품소개가 입력되지 않았습니다.  ",
		english:"Product intriduction has not been entered.  ",
		indonesian: "Perkenalan produk belum dimasukkan.  ",
		japan: "製品紹介が入力されていません.",
		chinese: ""
	},
	H:{
		korea:"중복된 가격+재고 옵션구분명이 있습니다. 수정후 다시 시도해주세요.  ",
		english:"There is duplicated information. Please try again after modification.  ",
		indonesian: "Informasi terduplikasi. Silakan mencoba lagi setelah dimodifikasi.  ",
		japan: "重複された価格+再考オプション仕分け名があります. 修正後、再試行してください.",
		chinese: ""
	},
	I:{
		korea:"중복된 옵션명이 있습니다. 수정후 다시 시도해주세요.  ",
		english:"There is duplicated option name. Please try again after modification.  ",
		indonesian: "Pilihan nama terduplikasi. Silakan mencoba lagi setelah dimodifikasi.  ",
		japan: "重複されたオプション名がいます. 修正後、再試行してください.",
		chinese: ""
	},
	J:{
		korea:"중복된 디스플레이 옵션명이 있습니다. 수정후 다시 시도해주세요.  ",
		english:"There is duplicated display option. Please try again after modification.  ",
		indonesian: "Pilihan tampilan terduplikasi. Silakan mencoba lagi setelah dimodifikasi.  ",
		japan: "重複されたディスプレーオプション名があります. 修正後、再試行してください.",
		chinese: ""
	},
	K:{
		korea:"옵션구분값을 입력해주세요.  ",
		english:"Please enter the 'option classification value'.  ",
		indonesian: "Silakan masukkan 'Nilai Pilihan Klasifikasi'  ",
		japan: "オプション仕分け値を入力してください.",
		chinese: ""
	},
	L:{
		korea:"옵션별 비회원가를 입력해주세요.  ",
		english:"Please enter the 'Nonmember price'for each options.  ",
		indonesian: "Silakan masukkan 'Harga Bukan Anggota' pada setiap pilihan.  ",
		japan: "オプション別非会員原価を入力してください.",
		chinese: ""
	},
	M:{
		korea:"옵션이름을 선택해주세요.  ",
		english:"Please select a option name.  ",
		indonesian: "Silakan pilih  nama pilihan.  ",
		japan: "オプション名前を選択してください.",
		chinese: ""
	},
	N:{
		korea:"옵션구분값을 입력해주세요.  ",
		english:"Please select a 'option classification value'.  ",
		indonesian: "Silakan masukkan 'Nilai Pilihan Klasifikasi.  ",
		japan: "オプション仕分け値を入力してください.",
		chinese: ""
	},
	O:{
		korea:"옵션별 딜러가를 입력해주세요.  ",
		english:"Please enter the 'Dealer price'for each options.  ",
		indonesian: "Silakan masukkan 'Harga agen' pada setiap pilihan.  ",
		japan: "オプション別代理価格を入力してください.",
		chinese: ""
	},
	P:{
		korea:"옵션별 대리점가를 입력해주세요.  ",
		english:"Please enter the 'Branch store price'for each options.  ",
		indonesian: "Silakan masukkan 'Harga toko cabang' pada setiap pilihan.  ",
		japan: "オプション別代理価格を入力してください.",
		chinese: ""
	},
	Q:{
		korea:"문자는 사용할 수 없습니다.  ",
		english:"Letters can not be used.  ",
		indonesian: "Huruf tidak dapat digunakan.  ",
		japan: "文字は使うことができません.",
		chinese: ""
	},
	R:{
		korea:"자릿수는 정수로만 구분합니다.  ",
		english:"Digits are separated only by a constant.  ",
		indonesian: "Karakter dipisah hanya oleh konstan.  ",
		japan: "桁は定数だけ区分します.",
		chinese: ""
	},
	S:{
		korea:"숫자만 입력 하세요.  ",
		english:"Enter only numbers.  ",
		indonesian: "Masukkan angka saja.  ",
		japan: "数字だけ入力してください.",
		chinese: ""
	},
	T:{
		korea:"구매단가(공급가)를 입력후 복사 버튼을 클릭해주세요.  ",
		english:"After enter supply price, Click the 'Copy button'.  ",
		indonesian: "Setelah memasukkan harga suplier, klik tombol 'Salin'.  ",
		japan: "購買単価(供給価格)を入力の後コピーボタンをクリックしてください.",
		chinese: ""
	},
	U:{
		korea:"정가를 입력후 복사 버튼을 클릭해주세요.  ",
		english:"After enter fixed price, Click the 'Copy button'.  ",
		indonesian: "Setelah memasukkan harga tetap, klik tombol 'Salin'.  ",
		japan: "定価を入力後コピーボタンをクリックしてください.",
		chinese: ""
	},
	V:{
		korea:"판매가를 입력후 복사 버튼을 클릭해주세요.  ",
		english:"After enter selling price, Click the 'Copy button'.  ",
		indonesian: "Setelah memasukkan harga jual, klik tombol 'Salin'.  ",
		japan: "売り値を入力後コピーボタンをクリックしてください.",
		chinese: ""
	}
};

language_data["goods_batch.act.php"] = {	
	A:{
		korea:"선택상품의 적립금 정보변경이 정상적으로 완료되었습니다. ",
		english:"The mileage points information of the selected product has been changed successfylly.  ",
		indonesian: "Informasi kupon poin produk yang dipilih sudah berhasil diubah.  ",
		japan: "選択商品の積立金情報変更が正確に完了しました.",
		chinese: ""
	},
	B:{
		korea:"전체상품의 적립금 정보변경이 정상적으로 완료되었습니다.  ",
		english:"The mileage points information of the all products has been changed successfylly.  ",
		indonesian: "Informasi kupon poin semua produk sudah berhasil diubah.  ",
		japan: "全体商品の積立金情報変更が正確に完了しました.",
		chinese: ""
	},
	C:{
		korea:"선택상품의 판매/진열 상태 정보변경이 정상적으로 완료되었습니다.  ",
		english:"The selling/display status information of the selected product has been changed successfylly.  ",
		indonesian: "Informasi status penjualan/tampilan produk terpilih sudah berhasil diubah.  ",
		japan: "選択商品の販売/陳列状態情報変更が正確に完了しました.",
		chinese: ""
	},
	D:{
		korea:"전체상품의 판매/진열 상태 정보변경이 정상적으로 완료되었습니다.  ",
		english:"The selling/display status information of the all products has been changed successfylly.  ",
		indonesian: "Informasi status penjualan/tampilan semua produk sudah berhasil diubah.  ",
		japan: "全体商品の販売/陳列状態情報変更が正確に完了しました.",
		chinese: ""
	},
	E:{
		korea:"선택상품의 카테고리 정보변경이 정상적으로 완료되었습니다.  ",
		english:"The category information of the selected product has been changed successfylly.  ",
		indonesian: "Informasi kategori produk terpilih sudah berhasil diubah.  ",
		japan: "選択商品のカテゴリー情報変更が正確に完了しました.",
		chinese: ""
	},
	F:{
		korea:"검색상품의 카테고리 정보변경이 정상적으로 완료되었습니다.  ",
		english:"The category information of the searched product has been changed successfylly.  ",
		indonesian: "Informasi kategori produk dicari sudah berhasil diubah.  ",
		japan: "検索商品のカテゴリー情報変更が正確に完了しました.",
		chinese: ""
	},
	G:{
		korea:"등록완료  ",
		english:"Registeraton completed  ",
		indonesian: "Pendaftaran selesai  ",
		japan: "登録完了 ",
		chinese: ""
	},
	H:{
		korea:"재고확인 처리가 완료되었습니다.  ",
		english:"Check stock processing has been completed.  ",
		indonesian: "Proses pemeriksaan stok sudah selesai.  ",
		japan: "再考確認処理が完了しました.",
		chinese: ""
	},
	I:{
		korea:"선택상품의 판매/진열 상태 정보변경이 적상적으로 완료되었습니다.  ",
		english:"The selling/display status information of the selected products has been changed successfylly.  ",
		indonesian: "Informasi kategori produk terpilih sudah berhasil diubah.  ",
		japan: "選択商品の販売/陳列状態情報変更が正確に完了しました.",
		chinese: ""
	},
	J:{
		korea:"정상적으로 입력되었습니다.  ",
		english:"It has been entered successfully.  ",
		indonesian: "Sudah berhasil dimasukkan.  ",
		japan: "正常に入力されました.",
		chinese: ""
	}
};

language_data["goods_input_quick.js"] = {	
	A:{
		korea:"마지막 한개는 삭제 하실 수 없습니다.  ",
		english:"You can not delete the last one.  ",
		indonesian: "Anda tidak dapat menghapus yang terakhir.  ",
		japan: "最後の一つは削除することができません.",
		chinese: ""
	},
	B:{
		korea:"판매가격이 입력되지 않았습니다.  ",
		english:"Selling price has not been entered.  ",
		indonesian: "Harga jual belum dimasukkan.  ",
		japan: "販売価格が入力されていません.",
		chinese: ""
	},
	C:{
		korea:"현금사용시 적립율이 입력되지 않았습니다.  ",
		english:"Accumulation rate has not been entered, when using cash.  ",
		indonesian: "Tingkat akumulasi belum dimasukkan saat membayar tunai.  ",
		japan: "現金使用の時積立率が入力されていません.",
		chinese: ""
	},
	D:{
		korea:"카드사용시 적립율이 입력되지 않았습니다.  ",
		english:"Accumulation rate has not been entered, when using credit card.  ",
		indonesian: "Tingkat akumulasi belum dimasukkan saat menggunakan kartu kredit.  ",
		japan: "カード社用の時積立率が入力されていません.",
		chinese: ""
	},
	E:{
		korea:"판매가격이 입력되지 않았습니다.  ",
		english:"Selling price has not been entered.  ",
		indonesian: "Harga jual belum dimasukkan.  ",
		japan: "販売価格が入力されていません.",
		chinese: ""
	},
	F:{
		korea:"카드수수료가 입력되지 않았습니다.  ",
		english:"Credit card fee has not been entered.  ",
		indonesian: "Biaya kartu kredit belum dimasukkan.  ",
		japan: "カード手数料が入力されていません.",
		chinese: ""
	},
	G:{
		korea:"카테고리를 선택해주세요.  ",
		english:"Select a category.  ",
		indonesian: "Pilih kategori.  ",
		japan: "カテゴリーを選択してください.",
		chinese: ""
	},
	H:{
		korea:"제품명이 입력되지 않았습니다.  ",
		english:"Product name has not been entered.  ",
		indonesian: "Nama produk belum dimasukkan.  ",
		japan: "製品名が入力されていません.",
		chinese: ""
	},
	I:{
		korea:"면세여부를 선택해주세요.  ",
		english:"Please choose whether tax-free or not.  ",
		indonesian: "Silakan pilih apakah bebas pajak atau tidak.  ",
		japan: "免税可否を選択してください.",
		chinese: ""
	},
	J:{
		korea:"배송방법이 선택되지않았습니다. 배송방법을 선택해주세요.  ",
		english:"Shipping method has not selected. Please select a shipping method.  ",
		indonesian: "Metode pengiriman belum dipilih. Silakan pilih metode pengiriman.  ",
		japan: "配送方法が選択できていません. 配送方法を選択してください.",
		chinese: ""
	},
	K:{
		korea:"포장 방법을 선택해주세요.  ",
		english:"Please choose how to pack.  ",
		indonesian: "Silakan pilih cara pengepakkan.  ",
		japan: "包装方法を選択してください.",
		chinese: ""
	},
	L:{
		korea:"가격에 대한 정보가 변경되었습니다.  ",
		english:"The price information has been changed.  ",
		indonesian: "Informasi harga sudah diubah.  ",
		japan: "価格に対する情報が変更されました.",
		chinese: ""
	},
	M:{
		korea:"공급가격이 입력되지 않았습니다.  ",
		english:"Supply price has not been entered.  ",
		indonesian: "Harga suplier belum dimasukkan.  ",
		japan: "供給価格が入力されていません.",
		chinese: ""
	},
	N:{
		korea:"제품소개가 입력되지 않았습니다.  ",
		english:"Product intriduction has not been entered.  ",
		indonesian: "Perkenalan produk belum dimasukkan.  ",
		japan: "製品紹介が入力されていません.",
		chinese: ""
	},
	O:{
		korea:"중복된 가격+재고 옵션구분명이 있습니다. 수정후 다시 시도해주세요.  ",
		english:"There is duplicated information. Please try again after modification.  ",
		indonesian: "Informasi terduplikasi. Silakan mencoba lagi setelah dimodifikasi.  ",
		japan: "重複された価格+再考オプション仕分け名がありません. 修正後再試行してください.",
		chinese: ""
	},
	P:{
		korea:"중복된 옵션명이 있습니다. 수정후 다시 시도해주세요.  ",
		english:"There is duplicated option name. Please try again after modification.  ",
		indonesian: "Nama pilihan terduplikasi. Silakan mencoba lagi setelah dimodifikasi.  ",
		japan: "重複されたオプション名があります. 修正後再試行してください.",
		chinese: ""
	},
	Q:{
		korea:"중복된 옵션구분명이 있습니다. 수정후 다시 시도해주세요.  ",
		english:"There is duplicated option classification. Please try again after modification.  ",
		indonesian: "Kategori pilihan terduplikasi. Silakan mencoba lagi setelah dimodifikasi.  ",
		japan: "重複されたオプション仕分け名があります. 修正後再試行してください.",
		chinese: ""
	},
	R:{
		korea:"중복된 디스플레이 옵션명이 있습니다. 수정후 다시 시도해주세요.  ",
		english:"There is duplicated display option. Please try again after modification.  ",
		indonesian: "Tampilan pilihan terduplikasi. Silakan mencoba lagi setelah dimodifikasi.  ",
		japan: "重複されたディスプレーオプション名があります. 修正後再試行してください.",
		chinese: ""
	},
	S:{
		korea:"옵션구분값을 입력해주세요.  ",
		english:"Please enter the 'option classification value'.  ",
		indonesian: "Silakan masukkan 'Nilai Pilihan Klasifikasi'.  ",
		japan: "オプション仕分け値を入力してください.",
		chinese: ""
	},
	T:{
		korea:"옵션별 비회원가를 입력해주세요.  ",
		english:"Please enter the 'Nonmember price'for each options.  ",
		indonesian: "Silakan masukkan 'Harga non-member' untuk setiap pilihan.  ",
		japan: "オプション別非会員原価を入力してください.",
		chinese: ""
	},
	U:{
		korea:"옵션이름을 선택해주세요.  ",
		english:"Please select a option name.  ",
		indonesian: "silakan pilih nama pilihan.  ",
		japan: "オプション名前を選択してください.",
		chinese: ""
	},
	V:{
		korea:"옵션구분값을 입력해주세요.  ",
		english:"Please enter the 'option classification value'.  ",
		indonesian: "Silakan masukkan 'Nilai Pilihan Klasifikasi'.  ",
		japan: "オプション仕分け値を入力してください.",
		chinese: ""
	},
	W:{
		korea:"옵션별 회원가를 입력해주세요.  ",
		english:"Please enter the 'Member price'for each options.  ",
		indonesian: "Silakan masukkan 'Harga member' untuk setiap pilihan.  ",
		japan: "オプション別会員原価を入力してください.",
		chinese: ""
	},
	X:{
		korea:"옵션별 딜러가를 입력해주세요.  ",
		english:"Please enter the 'Dealer price'for each options.  ",
		indonesian: "Silakan masukkan 'Harga agen' untuk setiap pilihan.  ",
		japan: "オプション別代理価格を入力してください.",
		chinese: ""
	},
	Y:{
		korea:"옵션별 대리점가를 입력해주세요.  ",
		english:"Please enter the 'Branch store price'for each options.  ",
		indonesian: "Silakan masukkan 'Harga toko cabang' untuk setiap pilihan.  ",
		japan: "オプション別代理価格を入力してください.",
		chinese: ""
	},
	Z:{
		korea:"문자는 사용할 수 없습니다.  ",
		english:"Letters can not be used.  ",
		indonesian: "Huruf tidak dapat digunakan.  ",
		japan: "文字は使うことができません.",
		chinese: ""
	},
	AA:{
		korea:"자릿수는 정수로만 구분합니다.  ",
		english:"Digits are separated only by a constant.  ",
		indonesian: "Karakter dipisah hanya oleh konstan.  ",
		japan: "桁は定数だけ区分します.",
		chinese: ""
	},
	AB:{
		korea:"숫자만 입력 하세요.  ",
		english:"Enter only numbers.  ",
		indonesian: "Masukkan angka saja.  ",
		japan: "数字だけ入力してください.",
		chinese: ""
	},
	AC:{
		korea:"구매단가(공급가)를 입력후 복사 버튼을 클릭해주세요.  ",
		english:"After enter supply price, Click the 'Copy button'.  ",
		indonesian: "Setelah memasukkan harga suplier, klik tombol 'Salin'.  ",
		japan: "購買単価(供給価格)を入力の後コピーボタンをクリックしてください.",
		chinese: ""
	},
	AD:{
		korea:"정가를 입력후 복사 버튼을 클릭해주세요.  ",
		english:"After enter fixed price, Click the 'Copy button'.  ",
		indonesian: "Setelah memasukkan harga tetap, klik tombol 'Salin'.  ",
		japan: "定価を入力後コピーボタンをクリックしてください.",
		chinese: ""
	},
	AE:{
		korea:"판매가를 입력후 복사 버튼을 클릭해주세요.  ",
		english:"After enter selling price, Click the 'Copy button'.  ",
		indonesian: "Setelah memasukkan harga jual, klik tombol 'Salin'.  ",
		japan: "売り値を入力後コピーボタンをクリックしてください.",
		chinese: ""
	}
};

language_data["product_make_order.js"] = {	
	A:{
		korea:"규격을 선택해주세요.  ",
		english:"Please choose Standard.  ",
		indonesian: "Silakan gunakan Standard.  ",
		japan: "規格を選択してください.",
		chinese: ""
	}
};

language_data["product_list.php"] = {	
	A:{
		korea:"비회원가를 입력후 적립금이 계산됩니다.  ",
		english:"Mileage points is calculated after inputting nonmember price.  ",
		indonesian: "Kupon poin terkalkulasi setelah memasukkan harga non-member.  ",
		japan: "非会員原価を入力後積立金が計算されます.",
		chinese: ""
	}
};

language_data["relation.category.act.php"] = {	
	A:{
		korea:"이미 등록된 상품입니다.  ",
		english:"The product has already registered.  ",
		indonesian: "Produk sudah terdaftar.  ",
		japan: "すでに登録された商品です.",
		chinese: ""
	}
};

language_data["img.add.php"] = {	
	A:{
		korea:"정상적으로 입력되었습니다.  ",
		english:"It has been entered successfully.  ",
		indonesian: "Sudah berhasil dimasukkan.  ",
		japan: "正常に入力されました.",
		chinese: ""
	},
	B:{
		korea:"정상적으로 삭제되었습니다.  ",
		english:"It has been deleted successfully.  ",
		indonesian: "Sudah berhasil dimasukkan.  ",
		japan: "正常に削除されました.",
		chinese: ""
	}
};

language_data["goods_input_quick.php"] = {	
	A:{
		korea:"카테고리를 선택해주세요.  ",
		english:"Please select a category.  ",
		indonesian: "Silakan pilih kategori.  ",
		japan: "カテゴリーを選択してください.",
		chinese: ""
	},
	B:{
		korea:"이미등록된 카테고리 입니다.  ",
		english:"The category is already registered.  ",
		indonesian: "Kategori sudah terdaftar.  ",
		japan: "すでに登録されたカテゴリーです.",
		chinese: ""
	},
	C:{
		korea:"카테고리를 선택해주세요.  ",
		english:"Please select a category.  ",
		indonesian: "Silakan pilih kategori.  ",
		japan: "カテゴリーを選択してください.",
		chinese: ""
	},
	D:{
		korea:"동영상 메뉴얼 준비중입니다.  ",
		english:"Video manual is preparing.  ",
		indonesian: "Manual video sedang disiapkan.  ",
		japan: "動画マニュアル準備中です.",
		chinese: ""
	},
	E:{
		korea:"가격 정보를 수정하시고자 할대는 MD와 상의해 주세요.  ",
		english:"If you want to modify price information please cotact with MD.  ",
		indonesian: "Jika Anda ingin memodifikasi informasi harga silakan menghubungi MD.  ",
		japan: "価格情報を修正する場合はMDと相談してください.",
		chinese: ""
	},
	F:{
		korea:"입점업체는 공급가격만 입력하실수 있습니다.  ",
		english:"Vendors may enter only a supply price.  ",
		indonesian: "Vendor hanya bisa memasukkan harga suplier.  ",
		japan: "入店業社は供給価格だけ入力することができます.",
		chinese: ""
	},
	G:{
		korea:"상품등록후 확인하실수 있습니다.  ",
		english:"It can be checked after product registration.  ",
		indonesian: "Hal itu dapat diperiksa setelah pendaftaran produk.  ",
		japan: "商品登録後、確認することができます.",
		chinese: ""
	},
	H:{
		korea:"비회원가를 입력후 적립금이 계산됩니다.  ",
		english:"Mileage points is calculated after inputting nonmember price.  ",
		indonesian: "Kupon poin terkalkulasi setelah memasukkan harga non-member.  ",
		japan: "非会員原価を入力後積立金が計算されます.",
		chinese: ""
	},
	I:{
		korea:"마지막 한개는 삭제 하실 수 없습니다.  ",
		english:"You can not delete the last one.  ",
		indonesian: "Anda tidak dapat menghapus yang terakhir.  ",
		japan: "最後の一つは削除することができません.",
		chinese: ""
	},
	J:{
		korea:"해당 옵션 구분정보가 삭제 되었습니다.  ",
		english:"The option's 'Identification information has been deleted.  ",
		indonesian: "Pilihan Informasi Identifikasi sudah dihapus.  ",
		japan: "該当のオプション仕分け情報が削除されました.",
		chinese: ""
	}
};

language_data["relation.act.php"] = {	
	A:{
		korea:"해당 카테고리에 이미 등록되어 있는 상품입니다.  ",
		english:"The product has already been registered in that category.  ",
		indonesian: "Produk sudah terdaftar pada kategori itu.  ",
		japan: "該当のカテゴリーに既に登録されている商品です.",
		chinese: ""
	}
};

language_data["product_bsgoods.php"] = {	
	A:{
		korea:"구매대행 사이트를 지정해주세요.  ",
		english:"Please specify 'Merchandising trade' site.  ",
		indonesian: "Silakan tentukan situs 'Pertukaran produk.  ",
		japan: "購買代行サイトを指定してください.",
		chinese: ""
	},
	B:{
		korea:"기본 URL 을 입력해주세요 (구매대행 사이트의 카테고리별 상품 리스트페이지 입니다).  ",
		english:"Please enter the base URL (This is a list page of the product on the 'Merchandising trade' site.).  ",
		indonesian: "Silakan masukkan URL dasar( Ini adalah daftar halaman produk di situs 'Pertukaran produk').  ",
		japan: "基本 URL を入力してください (購買代行サイトのカテゴリー別商品リストページです).",
		chinese: ""
	},
	C:{
		korea:"등록카테고리가 선택되지 않았습니다. 등록카테고리 지정후 상품 가져오기를 실행해주세요.  ",
		english:"'Registration category' is not selected. Please run the import products after appointing 'Registration category'.  ",
		indonesian: "'Kategori pendaftaran' belum dipilih. Silakan pilih produk impor setelah menggunakan 'Kategori Pendaftaran'.  ",
		japan: "登録カテゴリーが選択されませんでした. 登録カテゴリー指定後、商品受け取り処理を行ってください.",
		chinese: ""
	},
	D:{
		korea:"기본 URL 이 선택하신 구매대행 사이트와 맞는지 다시 한번 확인해주세요.  ",
		english:"Please check again, 'Base URL' matches the selected 'Merchandising trade' site.  ",
		indonesian: "Silakan diperiksa kembali. 'URL dasar' cocok dengan situs 'Pertukaran Produk' yang dipilih.  ",
		japan: "基本 URL 選択した購買代行サイトに合っているか再度確認してください.",
		chinese: ""
	},
	E:{
		korea:"검색 정지중입니다.  ",
		english:"The search is stopped.  ",
		indonesian: "Pencarian dihentikan.  ",
		japan: "検索停止中です.",
		chinese: ""
	}
};

language_data["category.js"] = {	
	A:{
		korea:"수정/삭제 하시고자 하는 상품카테고리를 선택해 주세요.  ",
		english:"Please select a product category you want to modefy/delete.  ",
		indonesian: "Silakan pilih kategori produk yang ingin dimodifikasi atau dihapus.  ",
		japan: "修正/削除する商品カテゴリーを選択してください.",
		chinese: ""
	},
	B:{
		korea:"추가 하시고자 하는 상품카테고리를 선택해 주세요.  ",
		english:"Please select a product category you want to add.  ",
		indonesian: "Silakan pilih kategori produk yang ingin Anda tambahkan.  ",
		japan: "追加する商品カテゴリーを選択してください.",
		chinese: ""
	},
	C:{
		korea:"카테고리구성은 4단계까지만 가능합니다.  ",
		english:"Category consists is possible only four steps.  ",
		indonesian: "Hanya tersedia empat langkah untuk kategori.  ",
		japan: "カテゴリー構成は4段階まで可能です.",
		chinese: ""
	},
	D:{
		korea:"선택한 주소가 클립보드에 복사되었습니다.  ",
		english:"Selected address is copied to the clipboard.  ",
		indonesian: "Alamat yang dipilih sudah disalin di clipboard.  ",
		japan: "選択した住所がクリップボードにコピーされました.",
		chinese: ""
	},
	E:{
		korea:"상품카테고리를 선택해주세요.  ",
		english:"Please select a product category.  ",
		indonesian: "Silakan pilih kategori produk.  ",
		japan: "商品カテゴリーを選択してください.",
		chinese: ""
	}
};

language_data["buyingService.php"] = {	
	A:{
		korea:"구매대행 사이트를 지정해주세요.  ",
		english:"Please appoint a objet website.  ",
		indonesian: "silahkan gunakan situs obyek.  ",
		japan: "購買代行サイトを指定してください.",
		chinese: ""
	},
	B:{
		korea:"구매대행 상품 URL 을 입력해주세요.  ",
		english:"Please enter a product URL.  ",
		indonesian: "Silakan masukkan URL produk.  ",
		japan: "購買代行商品 URL を入力してください.",
		chinese: ""
	},
	C:{
		korea:"구매대행 상품 URL 선택하신 구매대행 사이트와 맞는지 다시 한번 확인해주세요.  ",
		english:"Please check URL again.  ",
		indonesian: "Silakan periksa URL kembali.  ",
		japan: "購買代行商品 URL 選択した購買代行サイトと合っているか再度確認してください.",
		chinese: ""
	}
};

language_data["addoption.js"] = {	
	A:{
		korea:"앨범이 한개일경우는 지울수 없습니다.  ",
		english:"If the album is only one, you can not delete it.  ",
		indonesian: "Jika hanya ada satu album Anda tidak dapat menghapus.  ",
		japan: "アルバムは１つしかない場合消すことができません.",
		chinese: ""
	},
	B:{
		korea:"앨범이름은 한글 8자 영문 16자 까지 가능합니다.  ",
		english:"The album name can be up to 8 characters in Korean and 16 characters in English.  ",
		indonesian: "Nama album dapat digunakan sampai 8 karakter bahasa Korea dan 16 karakter bahasa Inggris.  ",
		japan: "アルバム名はハングル 8字英文 16字まで可能です.",
		chinese: ""
	},
	C:{
		korea:"앨범이름은 한글 8자 영문 16자 까지 가능합니다. \n초과된 내용은 자동으로 삭제 됩니다.  ",
		english:"The album name can be up to 8 characters in Korean and 16 characters in English. Excess characters are deleted automatically.  ",
		indonesian: "Nama album dapat digunakan sampai 8 karakter bahasa Korea dan 16 karakter bahasa Inggris. Karakter berlebih akan dihapus otomatis.  ",
		japan: "アルバム名はハングル 8字英文 16字まで可能です. \n超過された内容は自動削除されます.",
		chinese: ""
	}
};

language_data["member.js"] = {	
	A:{
		korea:" 값은 알파벳과 숫자만 가능합니다.  ",
		english:"Alphabetic and Numeric characters are available for['+str+'].  ",
		indonesian: "Huruf dan angka tersedia untuk['+str+'].  ",
		japan: "価格はアルファベットと数字だけ可能です.",
		chinese: ""
	},
	B:{
		korea:" 값은 숫자만 가능합니다.  ",
		english:"Numeric characters are available for ['+str+'].  ",
		indonesian: "Angka tersedia untuk['+str+'].  ",
		japan: "価格は数字だけ可能です.",
		chinese: ""
	},
	C:{
		korea:"을 입력해주세요.  ",
		english:"Please enter['+string+'].  ",
		indonesian: "Silakan masukkan['+string+'].  ",
		japan: "を 入力してください.",
		chinese: ""
	},
	D:{
		korea:"[비밀번호]와 [비번확인]이 일치하지 않습니다.  ",
		english:"[Password] and [Password Confirmed] does not match.  ",
		indonesian: "'(Kata sandi) dan (Konfirmasi kata sandi) tidak cocok.  ",
		japan: "[パスワード]と [非番確認]が一致しないです.",
		chinese: ""
	},
	E:{
		korea:"해당회원 정보를 정말로 삭제하시겠습니까? \\n 삭제시 관련된 모든 정보가 삭제됩니다.  ",
		english:"Do you want to delete that member's information? All related information will be deleted.",
		indonesian: "Apakah Anda ingin menghapus informasi anggota? Semua informasi terkait akan dihapus.",
		japan: "該当会員情報を本当に削除しますか? \\n 削除時に係わるすべての情報が削除されます.",
		chinese: ""
	}		
};

language_data["giftcertificate.php"] = {	
	A:{
		korea:"적립내용을 입력해주세요.  ",
		english:"Please enter Accumulation detail.  ",
		indonesian: "Silakan masukkan detail Akumulasi.  ",
		japan: "積立内容を入力してください.",
		chinese: ""
	},
	B:{
		korea:"마일리지를 입력해주세요.  ",
		english:"Please enter Mileage.  ",
		indonesian: "Silakan masukkan kupon poin.  ",
		japan: "マイレージを入力してください.",
		chinese: ""
	},
	C:{
		korea:"삭제하실 목록을 한개이상 선택하셔야 합니다.  ",
		english:"The list you want to delete, should be selected one or more.  ",
		indonesian: "Pilih satu atau lebih dari daftar yang ingin Anda hapus.  ",
		japan: "削除するリストを一つ以上選択しなければなりません.",
		chinese: ""
	},
	D:{
		korea:"상품권 정보를 정말로 삭제하시겠습니까.  ",
		english:"Do you really want to delete the gift voucher's information?",
		indonesian: "Apakah Anda ingin menghapus informasi voucher hadiah?",
		japan: "商品巻情報を本当に削除しますか.",
		chinese: ""
	},
	E:{
		korea:"선택하신 상품권을 정말로 삭제하시겠습니까? 삭제하신 적립금은 복원되지 않습니다.  ",
		english:"Do you really want to delete the selected gift voucher? It will not be restored the deleted mileage points.  ",
		indonesian: "Apakah Anda ingin menghapus voucher hadiah terpilih? Kupon poin yang dihapus tidak akan tersimpan kembali.  ",
		japan: "選択した商品券を本当に削除しますか? 削除した積立金は復元されません.",
		chinese: ""
	}
};

language_data["member_batch.php"] = {	
	A:{
		korea:"적용대상중 [검색회원전체]는  검색후 사용 가능합니다. 확인후 다시 시도해주세요.  ",
		english:"You can use [All Searched Members] after searching. Please try again after checking.  ",
		indonesian: "'Anda dapat menggunakan (Semua Anggota yang dicari) setelah mencari. Silakan coba lagi setelah memeriksa.  ",
		japan: "適用対象中 [検索会員全体]は検索の後使用可能です. 確認後再試行してください.",
		chinese: ""
			},
	B:{
		korea:"적립금 적립내용을 입력해주세요.  ",
		english:"Please enter Accumulation/Deduction.  ",
		indonesian: "'Anda dapat menggunakan (Semua Anggota yang dicari) setelah mencari. Silakan coba lagi setelah memeriksa.  ",
		japan: "積立金積立内容を入力してください.",
		chinese: ""	
	},
	C:{
		korea:"변경하시고자 하는 회원그룹을 선택해주세요.  ",
		english:"Please select the membership group you wish to change.  ",
		indonesian: "Silakan pilih grup keanggotaan yang ingin Anda ubah.  ",
		japan: "変更する会員グループを選択してください.",
		chinese: ""
	},
	D:{
		korea:"SMS 발송 내역을 입력하신후 보내기 버튼을 클릭해주세요.  ",
		english:"Please click the 'Send' button, after entering the SMS message.  ",
		indonesian: "Silakan klik tombol 'Kirim' setelah menulis SMS.  ",
		japan: "SMS 発送内訳を入力した後送信ボタンをクリックしてください.",
		chinese: ""
			},
	E:{
		korea:"지급 하시고자 하는 쿠폰을 선택해주세요.  ",
		english:"Please select the coupons you wish to pay.  ",
		indonesian: "Silakan pilih kupon yang ingin Anda gunakan.  ",
		japan: "支給するクーポンを選択してください.",
		chinese: ""
	},
	F:{
		korea:"이메일 제목을 입력해주세요.  ",
		english:"Please enter the e-mail title.  ",
		indonesian: "Silakan masukkan judul e-mail.  ",
		japan: "電子メール題目を入力してください.",
		chinese: ""
	},
	G:{
		korea:"이메일 내용을 입력하신후 보내기 버튼을 클릭해주세요.  ",
		english:"Please click the 'Send' button, after entering the Email contents.  ",
		indonesian: "Silakan klik tombol 'Kirim' setelah menulis konten e-mail.  ",
		japan: "電子メール内容を入力後、送信ボタンをクリックしてください.",
		chinese: ""
	},
	H:{
		korea:"선택된 회원이 없습니다. 변경/발송 하시고자 하는 수신자를 선택하신 후 저장/보내기 버튼을 클릭해주세요.  ",
		english:"There are no selected members. Click the button 'Save / Send' after selecting the recipients that you wish to 'Change/Send.  ",
		indonesian: "Tidak ada anggota yang dipilih. Klik tombol 'Simpan/Kirim' setelah memilih penerima yang ingin Anda 'Ubah/Kirim.  ",
		japan: "選択された会員がありません. 変更/発送しようとする受信者を選択した後保存/送信ボタンをクリックしてください.",
		chinese: ""
	},
	I:{
		korea:"검색회원 적립금 일괄 지급을 하시겠습니까.  ",
		english:"Do you want to pay mileage points to searched members?.  ",
		indonesian: "Apakah Anda ingin membayar kupon poin anggota yang dicari? ",
		japan: "検索会員積立金一括支給をしますか.",
		chinese: ""
	},
	J:{
		korea:"검색회원 전체의 회원그룹 변경을 하시겠습니까? ",
		english:"Do you want to change the membership group of all selected members? ",
		indonesian: "Apakah Anda ingin mengubah keanggotaan grup semua anggota terpilih? ",
		japan: "検索会員全体の会員グループ変更をしますか?",
		chinese: ""
	},
	K:{
		korea:"검색회원 전체에게 SMS 발송을 하시겠습니까? ",
		english:"Do you want to send SMS to all selected members? ",
		indonesian: "Apakah Anda ingin mengirim SMS ke semua anggota terpilih? ",
		japan: "検索会員全体に SMS 発送をしますか?",
		chinese: ""
	},
	L:{
		korea:"검색회원 전체에게 쿠폰일괄지급을 하시겠습니까? ",
		english:"Do you want to pay the coupon to all selected members? ",
		indonesian: "Apakah Anda ingin membayar kupon semua anggota terpilih? ",
		japan: "検索会員全体にクーポン一括支給をしますか?",
		chinese: ""
	},
	M:{
		korea:"검색회원 전체에게 이메일발송을 하시겠습니까?",
		english:"Do you want to send Email to all selected members?",
		indonesian: "Apakah Anda ingin mengirim email ke semua anggota terpilih? ",
		japan: "検索会員全体に電子メール発送をしますか?",
		chinese: ""
	},
	 N:{
		korea:"적립금 지급액/차감액을 입력해주세요",
		english:"Please enter Accumulation/Deduction",
		indonesian: "",
		japan: "積立金支給額/差引額を入力してください",
		chinese: ""
	}
};

language_data["mail.send.php"] = {	
	A:{
		korea:"정상적으로 메일이 발송되었습니다.  ",
		english:"Mail has been sent successfully.  ",
		indonesian: "Surat berhasil dikirim.  ",
		japan: "正常にメールが発送されました.",
		chinese: ""
	}
};

language_data["giftcertificate.act.php"] = {	
	A:{
		korea:"상품권 정보가 정상적으로 수정되었습니다.  ",
		english:"Voucher information has been modified successfully.  ",
		indonesian: "Informasi voucher berhasil dimodifikasi.  ",
		japan: "商品巻情報が正常に修正されました.",
		chinese: ""
    },
	B:{
		korea:"상품권 정보가 정상적으로 확인되었습니다. 등록 : $new_cnt , 중복 : $dupe_cnt.  ",
		english:"Voucher information has been verified successfully. Enrollment : $new_cnt , Duplication : $dupe_cnt.  ",
		indonesian: "Informasi voucher berhasil diverifikasi. Pendaftaran : $new_cnt , Duplikasi : $dupe_cnt.  ",
		japan: "商品巻情報が正常に確認されました. 登録 : $new_cnt , 重複 : $dupe_cnt.",
		chinese: ""
    },
	C:{
		korea:"상품권 정보가 정상적으로 등록되었습니다. 등록 : $new_cnt , 중복 : $dupe_cnt .  ",
		english:"Voucher information has been modified successfully.  ",
		indonesian: "Informasi voucher terdaftar dengan benar. Pendaftaran : $new_cnt , Duplikasi : $dupe_cnt.  ",
		japan: "商品巻情報が正常に登録されました. 登録 : $new_cnt , 重複 : $dupe_cnt .",
		chinese: ""
    },
	D:{
		korea:"상품권 정보가 정상적으로 확인되었습니다. 등록 : $new_cnt , 중복 : $dupe_cnt .  ",
		english:"Voucher information has been verified successfully. Enrollment : $new_cnt , Duplication : $dupe_cnt .  ",
		indonesian: "Informasi voucher berhasil diverifikasi. Pendaftaran : $new_cnt , Duplikasi : $dupe_cnt .  ",
		japan: "商品巻情報が正常に確認されました. 登録 : $new_cnt , 重複 : $dupe_cnt .",
		chinese: ""
    },
	E:{
		korea:"상품권 정보가 정상적으로 등록되었습니다. 등록 : $new_cnt , 중복 : $dupe_cnt  .  ",
		english:"Voucher information has been verified successfully. Enrollment : $new_cnt , Duplication : $dupe_cntVoucher information has been properly registered. Enrollment : $new_cnt , Duplicate : $dupe_cnt  .  ",
		indonesian: "Informasi voucher terdaftar dengan benar. Pendaftaran : $new_cnt , Duplikasi : $dupe_cnt.  ",
		japan: "商品巻情報が正常に登録されました. 登録 : $new_cnt , 重複 : $dupe_cnt  .",
		chinese: ""
    }
};	

language_data["reserve.php"] = {	
	A:{
		korea:"적립내용을 입력해주세요.  ",
		english:"Please enter Accumulation detail.  ",
		indonesian: "Silakan masukkan detail Akumulasi.  ",
		japan: "積立内容を入力してください.",
		chinese: ""
    },
	B:{
		korea:"마일리지를 입력해주세요.  ",
		english:"Please enter Mileage.  ",
		indonesian: "Silakan masukkan kupon poin.  ",
		japan: "マイレージを入力してください.",
		chinese: ""
    },
	C:{
		korea:"삭제하실 목록을 한개이상 선택하셔야 합니다.  ",
		english:"The list you want to delete, should be selected one or more.  ",
		indonesian: "Pilih satu atau lebih dari daftar yang ingin Anda hapus.  ",
		japan: "削除するリストを一つ以上選択しなければなりません.",
		chinese: ""
    },
	D:{
		korea:"적립내용을 입력해주세요.  ",
		english:"Please enter Accumulation detail.  ",
		indonesian: "Silakan masukkan detail Akumulasi.  ",
		japan: "積立内容を入力してください.",
		chinese: ""
    },
	E:{
		korea:"마일리지를 입력해주세요.  ",
		english:"Please enter Mileage.  ",
		indonesian: "Silakan masukkan kupon poin.  ",
		japan: "マイレージを入力してください.",
		chinese: ""
    },
	F:{
		korea:"적립금 정보를 정말로 삭제하시겠습니까? ",
		english:"Do you really want to delete the information of mileage points? ",
		indonesian: "Apakah Anda ingin menghapus informasi kupon poin? ",
		japan: "積立金情報を本当に削除しますか?",
		chinese: ""
    },
	G:{
		korea:"선택하신 적립금을 정말로 삭제하시겠습니까? 삭제하신 적립금은 복원되지 않습니다 ",
		english:"Do you really want to delete the selected mileage points? It will not be restored the deleted mileage points",
		indonesian: "Apakah Anda ingin menghapus kupon poin terpilih? Kupon poin yang dihapus tidak akan tersimpan kembali.  ",
		japan: "選択した積立金を本当に削除しますか? 削除した積立金は復元されません",
		chinese: ""
    },
	H:{
		korea:"적립금 정보를 정말로 삭제하시겠습니까?",
		english:"Do you want to delete the mileage points?",
		indonesian: "Apakah Anda ingin menghapus kupon poin?",
		japan: "積立金情報を本当に削除しますか?",
		chinese: ""
    }
};
	
language_data["member_batch.act.php"] = {	
	A:{
		korea:"선택회원 전체에게 적립금 $reserve 이 적립이 완료되었습니다.  ",
		english:"It has been accumulated  $reserve to select the entire members.  ",
		indonesian: "Sudah terakumulasi$. Tersedia untuk memilih semua anggota.  ",
		japan: "選択会員全体に積立金 $reserve この積立が完了しました.",
		chinese: ""
    },
	B:{
		korea:"검색회원 전체에게 적립금 적립이 완료되었습니다.  ",
		english:"Please enter Mileage.  ",
		indonesian: "Silakan masukkan kupon poin.  ",
		japan: "検索会員全体に積立金積立が完了しました.",
		chinese: ""
    },
	C:{
		korea:"선택회원 전체의 그룹변경이 완료되었습니다.  ",
		english:"Changing the group of the entire selected members has been completed.  ",
		indonesian: "Pergantian grup semua anggota terpilih selesai.  ",
		japan: "選択会員全体のグループ変更が完了しました.",
		chinese: ""
    },
	D:{
		korea:"검색회원 전체의 그룹변경이 완료되었습니다.  ",
		english:"Changing the group of the searched members has been completed.  ",
		indonesian: "Pergantian grup anggota terpilih selesai.  ",
		japan: "検索会員全体のグループ変更が完了しました.",
		chinese: ""
    },
	E:{
		korea:"선택회원 전체에게 SMS 가 발송 되었습니다.  ",
		english:"SMS has been sent to all selected members.",
		indonesian: "SMS sudah terkirim ke semua anggota terpilih.",
		japan: "選択会員全体に SMS が発送されました.",
		chinese: ""
    },
	F:{
		korea:"건의 SMS 가 정상적으로 발송되었습니다.  ",
		english:" Of SMS was sent successfully.  ",
		indonesian: " SMS terkirim..  ",
		japan: "件の SMS が正常に発送されました.",
		chinese: ""
    },
	G:{
		korea:"검색회원 전체에게 쿠폰 발급이 완료되었습니다.  ",
		english:"Issuing coupons has been completed to entire searched members.  ",
		indonesian: "Pembagian kupon untuk semua anggota yang dicari selesai.  ",
		japan: "検索会員全体にクーポン発給が完了しました.",
		chinese: ""
    },
	H:{
		korea:"선택회원 전체에게 쿠폰발급이 완료되었습니다.  ",
		english:"Issuing coupons has been completed to entire selected members.  ",
		indonesian: "Pembagian kupon untuk semua anggota terpilih selesai.  ",
		japan: "選択会員全体にクーポン発給が完了しました.",
		chinese: ""
    },
	I:{
		korea:"선택회원에게 E-mail이 정상적으로 발송되었습니다.  ",
		english:"E-mail was sent to a selected member successfully.  ",
		indonesian: "Email berhasil dikirim ke anggota terpilih.  ",
		japan: "選択会員に E-mailが正常に発送されました.",
		chinese: ""
    },
	J:{
		korea:" 건의 이메일이 정상적으로 발송되었습니다.  ",
		english:"Of e-mail was sent successfully.  ",
		indonesian: "E-mail terkirim.  ",
		japan: "件の 電子メールが正常に発送されました.",
		chinese: ""
    },
	K:{
		korea:"발송대상이 존재하지 않습니다. 메일링 수신거부 회원은 메일링 대상이 아닙니다.  ",
		english:"The sending target does not exist. 'Receipt Refusal' members are not eligible for the mailing.  ",
		indonesian: "Target penerima surat tidak ada. Anggota yang mengklik tombol 'Tolak' tidak akan menerima surat kembali.  ",
		japan: "発送対象が存在しません. メーリング受信拒否会員はメーリング対象ではありません.",
		chinese: ""
    }
};
	
language_data["group.act.php"] = {	
	A:{
		korea:"해당 그룹으로 지정된 회원이 있으므로 삭제할 수 없습니다.  ",
		english:"It can not be deleted because there is specified by that group members.  ",
		indonesian: "Tidak dapat dihapus karena ditentukan oleh anggota grup.  ",
		japan: "該当のグループに指定された会員がいるので削除することができません.",
		chinese: ""
    }
};	

language_data["mail.manage.list2.php"] = {	
	A:{
		korea:"해당 메일 목록을 정말로 삭제하시겠습니까? 메일 목록을  삭제 하시면 관련 데이타 모두가 삭제 됩니다.  ",
		english:"Do you really want to delete the Email List? All related data will be deleted.  ",
		indonesian: "Apakah Anda ingin menghapus daftar email? Semua data terkait akan dihapus.  ",
		japan: "該当のメールリストを本当に削除しますか? メールリストを削除すれば関連データすべてが削除になります.",
		chinese: ""
    }
};

language_data["mail.target.js"] = {	
	A:{
		korea:"정말로 반품 처리 하시겠습니까? ",
		english:"Do you really want to return the product? ",
		indonesian: "Apakah Anda ingin mengembalikan produk? ",
		japan: "本当に返品処理しますか?",
		chinese: ""
    },
	B:{
		korea:"해당 게시판을 정말로 삭제하시겠습니까? 게시판을 삭제 하시면 관련 데이타 모두가 삭제 됩니다",
		english:"Do you really want to delete the Board? All related data will be deleted",
		indonesian: "Apakah Anda ingin menghapus board? Semua data terkait akan dihapus.  ",
		japan: "該当の掲示板を本当に削除しますか? 掲示板を削除すれば関連データすべてが削除になります",
		chinese: ""
    }
};

language_data["group.php"] = {	
	A:{
		korea:"해당그룹 정보를 정말로 삭제하시겠습니까? ",
		english:"Do you want to delete the group information?",
		indonesian: "Apakah Anda ingin menghapus informasi grup? ",
		japan: "該当グループ情報を本当に削除しますか?",
		chinese: ""
    },
	B:{
		korea:"해당 게시판을 정말로 삭제하시겠습니까? 게시판을 삭제 하시면 관련 데이타 모두가 삭제 됩니다",
		english:"Do you really want to delete the Board? All related data will be deleted",
		indonesian: "Apakah Anda ingin menghapus board? Semua data terkait akan dihapus.  ",
		japan: "該当の掲示板を本当に削除しますか? 掲示板を削除すれば関連データすべてが削除になります",
		chinese: ""
    }
};

language_data["coupon_list.act.php"] = {	
	A:{
		korea:"일치하는 쿠폰번호가 없습니다.",
		english:"None matched coupon number.",
		indonesian: "Tidak ada nomor kupon yang cocok.",
		japan: "一致するクーポン番号がありません.",
		chinese: ""
    }
};

language_data["sns_product_list.php"] = {	
	A:{
		korea:"비회원가를 입력후 적립금이 계산됩니다.",
		english:"It will be calculated after inputting 'Nonmember price'.",
		indonesian: "Akan dikalkulasi setelah memasukkan 'harga non anggota'.",
		japan: "非会員原価を入力の後積立金が計算されます.",
		chinese: ""
    }
};

language_data["sns_goods_input.js"] = {	
	A:{
		korea:"마지막 한개는 삭제 하실 수 없습니다.",
		english:"You can not delete the last one.",
		indonesian: "Anda tidak dapat menghapus yang terakhir.",
		japan: "最後の一つは削除することができません.",
		chinese: ""
    },
	B:{
		korea:"판매가격이 입력되지 않았습니다.",
		english:"Price has not been entered.",
		indonesian: "Harga belum dimasukkan.",
		japan: "販売価格が入力されていません.",
		chinese: ""
    },
	C:{
		korea:"현금사용시 적립율이 입력되지 않았습니다.",
		english:"Accumulation rate has not been entered on the use of cash.",
		indonesian: "Tingkat akumulasi belum dimasukkan ke dalam penggunaan pembayaran tunai.",
		japan: "現金使用の時積立率が入力されていません.",
		chinese: ""
    },
	D:{
		korea:"카드사용시 적립율이 입력되지 않았습니다.",
		english:"Accumulation rate has not been entered on the use of credit card.",
		indonesian: "Tingkat akumulasi belum dimasukkan ke dalam penggunaan kartu kredit.",
		japan: "カード社用の時積立率が入力されていません.",
		chinese: ""
    },
	E:{
		korea:"판매가격이 입력되지 않았습니다.",
		english:"Selling Price has not been entered.",
		indonesian: "Harga jual belum dimasukkan.",
		japan: "販売価格が入力されていません.",
		chinese: ""
    },
	F:{
		korea:"카드수수료가 입력되지 않았습니다.",
		english:"Credit card fee has not been entered.",
		indonesian: "Biaya kartu kredit belum dimasukkan.",
		japan: "カード手数料が入力されていません.",
		chinese: ""
    },
	G:{
		korea:"카테고리를 선택해주세요.",
		english:"Please select a category.",
		indonesian: "Silakan pilih kategori",
		japan: "カテゴリーを選択してください.",
		chinese: ""
    },
	H:{
		korea:"제품명이 입력되지 않았습니다.",
		english:"Please select a category.",
		indonesian: "Nama produk belum dimasukkan",
		japan: "製品名が入力されていません.",
		chinese: ""
    },
	I:{
		korea:"면세여부를 선택해주세요.",
		english:"Please choose whether tex free or not.",
		indonesian: "Silakan pilih apakah bebas pajak atau tidak",
		japan: "免税可否を選択してください.",
		chinese: ""
    },
	J:{
		korea:"배송방법이 선택되지않았습니다. 배송방법을 선택해주세요.",
		english:"Delivery method has not been selected. Please select delivery method.",
		indonesian: "Metode pengiriman belum dipilih. Silakan pilih metode pengiriman",
		japan: "配送方法が選択できていません. 配送方法を選択してください.",
		chinese: ""
    },
	K:{
		korea:"포장 방법을 선택해주세요.",
		english:"Choose a packing method.",
		indonesian: "Pilih metode pengemasan",
		japan: "包装方法を選択してください.",
		chinese: ""
    },
	L:{
		korea:"가격에 대한 정보가 변경되었습니다.",
		english:"Price information has been changed.",
		indonesian: "Informasi harga sudah diubah",
		japan: "価格に対する情報が変更されました.",
		chinese: ""
    },
	M:{
		korea:"공급가격이 입력되지 않았습니다.",
		english:"Supply price has not been entered.",
		indonesian: "Harga pasokan belum dimasukkan",
		japan: "供給価格が入力されていません.",
		chinese: ""
    },
	N:{
		korea:"제품소개가 입력되지 않았습니다.",
		english:"Product introduction has not been entered.",
		indonesian: "Informasi produk belum dimasukkan",
		japan: "製品紹介が入力されていません.",
		chinese: ""
    },
	O:{
		korea:"중복된 가격+재고 옵션구분명이 있습니다. 수정후 다시 시도해주세요.",
		english:"There is duplicated information. Please try again after modification.",
		indonesian: "Terdapat informasi yang terduplikasi. Silakan coba lagi setelah memodifikasi",
		japan: "重複された価格+再考オプション仕分け名があります. 修正後再試行してください.",
		chinese: ""
    },
	P:{
		korea:"중복된 옵션명이 있습니다. 수정후 다시 시도해주세요.",
		english:"There is duplicated option name. Please try again after modification.",
		indonesian: "Terdapat pilihan klasifikasi yang terduplikasi. Silakan coba lagi setelah memodifikasi",
		japan: "重複されたオプション名があります. 修正後再試行してください.",
		chinese: ""
    },
	Q:{
		korea:"중복된 디스플레이 옵션명이 있습니다. 수정후 다시 시도해주세요.",
		english:"There is duplicated display option. Please try again after modification.",
		indonesian: "Terdapat pilihan tampilan yang terduplikasi. Silakan coba lagi setelah memodifikasi",
		japan: "重複されたディスプレーオプション名がありますう. 修正後再試行してください.",
		chinese: ""
    },
	R:{
		korea:"옵션구분값을 입력해주세요.",
		english:"Please enter the option classification value.",
		indonesian: "Silakan pilih nilai pilihan klasifikasi ",
		japan: "オプション仕分け値を入力してください.",
		chinese: ""
    },
	S:{
		korea:"옵션별 비회원가를 입력해주세요.",
		english:"Please enter the Nonmember price for each options.",
		indonesian: "Silakan masukkan harga untuk non anggota di setiap pilihan",
		japan: "オプション別非会員原価を入力してください.",
		chinese: ""
    },
	T:{
		korea:"옵션이름을 선택해주세요.",
		english:"Please select the name of the option.",
		indonesian: "Silakan pilih pilihan nama",
		japan: "オプション名前を選択してください.",
		chinese: ""
    },
	U:{
		korea:"옵션구분값을 입력해주세요.",
		english:"Please select a option classification value.",
		indonesian: "Silakan pilih nilai pilihan klasifikasi",
		japan: "オプション仕分け値を入力してください.",
		chinese: ""
    },
	V:{
		korea:"옵션별 비회원가를 입력해주세요.",
		english:"Please enter a Nonmember price for each options.",
		indonesian: "Silakan masukkan harga untuk non anggota di setiap pilihan",
		japan: "オプション別非会員原価を入力してください.",
		chinese: ""
    },
	W:{
		korea:"옵션별 회원가를 입력해주세요.",
		english:"Please enter the Member price for each options.",
		indonesian: "Silakan masukkan harga untuk anggota di setiap pilihan",
		japan: "オプション別会員原価を入力してください.",
		chinese: ""
    },
	X:{
		korea:"옵션별 딜러가를 입력해주세요.",
		english:"Please enter the Dealer price for each options.",
		indonesian: "Silakan masukkan harga untuk agen di setiap pilihan",
		japan: "オプション別代理価格を入力してください.",
		chinese: ""
    },
	Y:{
		korea:"옵션별 대리점가를 입력해주세요.",
		english:"Please enter the Branch store price for each options.",
		indonesian: "Silakan masukkan harga untuk toko cabang di setiap pilihan",
		japan: "オプション別代理価格を入力してください.",
		chinese: ""
    },
	Z:{
		korea:"옵션이름을 선택해주세요.",
		english:"Please select the name of the option.",
		indonesian: "Silakan pilih pilihan nama",
		japan: "オプション名を選択してください.",
		chinese: ""
    },
	AA:{
		korea:"옵션구분값을 입력해주세요.",
		english:"Please select a option classification value.",
		indonesian: "Silakan pilih nilai pilihan klasifikasi",
		japan: "オプション仕分け値を入力してください.",
		chinese: ""
    },
	AB:{
		korea:"옵션이름을 선택해주세요.",
		english:"Please select the name of the option.",
		indonesian: "Silakan pilih pilihan nama",
		japan: "オプション名を選択してください.",
		chinese: ""
    },
	AC:{
		korea:"문자는 사용할 수 없습니다.",
		english:"letters can not be used.",
		indonesian: "Huruf tidak dapat digunakan",
		japan: "文字は使うことができません.",
		chinese: ""
    },
	AD:{
		korea:"자릿수는 정수로만 구분합니다.",
		english:"Digits are separated only by a constant.",
		indonesian: "Karakter dipisahkan hanya dengan konstan",
		japan: "桁は定数だけ区分します.",
		chinese: ""
    },
	AE:{
		korea:"숫자만 입력 하세요.",
		english:"Enter numbers only.",
		indonesian: "数字だけ入力してください.",
		japan: "",
		chinese: ""
    },
	AF:{
		korea:"구매단가(공급가)를 입력후 복사 버튼을 클릭해주세요.",
		english:"After enter supply price, Click the Copy button.",
		indonesian: "Setelah memasukkan harga pasokan klik tombol Copy",
		japan: "購買単価(供給価格)を入力後コピーボタンをクリックしてください.",
		chinese: ""
    },
	AG:{
		korea:"정가를 입력후 복사 버튼을 클릭해주세요.",
		english:"After enter fixed price, Click the Copy button.",
		indonesian: "Setelah memasukkan harga tetap klik tombol Copy",
		japan: "定価を入力後コピーボタンをクリックしてください.",
		chinese: ""
    },
	AH:{
		korea:"판매가를 입력후 복사 버튼을 클릭해주세요.",
		english:"After inputting a copy for sale, click the button'.",
		indonesian: "Setelah memasukkan salinan penjualan klik tombol Copy",
		japan: "売り値を入力後コピーボタンをクリックしてください.",
		chinese: ""
    },
	AI:{
		korea:"수정/삭제 하시고자 하는 상품카테고리를 선택해 주세요.",
		english:"Edit / Delete Please select a Product Category that you wish to.",
		indonesian: "Edit/Hapus. Silakan pilih kategori produk yang ingin Anda",
		japan: "修正/削除する商品カテゴリーを選択してください.",
		chinese: ""
    },
	AJ:{
		korea:"추가 하시고자 하는 상품카테고리를 선택해 주세요.",
		english:"Please select the product category that you wish to add.",
		indonesian: "Silakan pilih kategori produk yang ingin Anda tambahkan",
		japan: "追加する商品カテゴリーを選択してください.",
		chinese: ""
	},
	AK:{
		korea:"중복된 옵션구분명이 있습니다. 수정후 다시 시도해주세요.",
		english:"There is duplicated option classification. Please try again after modification.",
		indonesian: "Terdapat pilihan klasifikasi yang terduplikasi. Silakan coba lagi setelah memodifikasi",
		japan: "重複されたオプション仕分け名があります. 修正後再試行してください.",
		chinese: ""
	}		
};

language_data["sns_categoryorder.php"] = {	
	A:{
		korea:"더이상 진행할 방향이 없습니다.",
		english:"Delete option ",
		indonesian: "Tidak dapat diproses.",
		japan: "これ以上進行する方向がありません.",
		chinese: ""
    }
};

language_data["sns_category.js"] = {	
	A:{
		korea:"추가 하시고자 하는 상품카테고리를 입력해 주세요.",
		english:"Please enter the product category that you wish to add.",
		indonesian: "Silakan masukkan kategori produk yang ingin Anda tambahkan",
		japan: "追加する商品カテゴリーを入力してください",
		chinese: ""
    },
	B:{
		korea:"카테고리구성은 4단계까지만 가능합니다.",
		english:"Category consists is possible only four steps.",
		indonesian: "",
		japan: "追加する商品カテゴリーを入力してください.",
		chinese: ""
    },
	C:{
		korea:"선택한 주소가 클립보드에 복사되었습니다.",
		english:"Selected address is copied to the clipboard.",
		indonesian: "Alamat terpilih tersalin ke clipboard.",
		japan: "選択した住所がクリップボードにコピーされました.",
		chinese: ""
    },
	D:{
		korea:"상품카테고리를 선택해주세요.",
		english:"Please select a product category.",
		indonesian: ".",
		japan: "商品カテゴリーを選択してください.",
		chinese: ""
    },
	E:{
		korea:"수정/삭제 하시고자 하는 상품카테고리를 선택해 주세요.",
		english:"Edit / Delete Please select a Product Category that you wish to.",
		indonesian: "Edit/Hapus. Silakan pilih kategori produk yang ingin Anda",
		japan: "修正/削除する商品カテゴリーを選択してください.",
		chinese: ""
    }
};

language_data["sns_goods_input.php"] = {	
	A:{
		korea:"이미등록된 카테고리 입니다.",
		english:"Category is already registered.",
		indonesian: "Kategori sudah terdaftar",
		japan: "既に登録されたカテゴリーです.",
		chinese: ""
    },
	B:{
		korea:"동영상 메뉴얼 준비중입니다.",
		english:"Video manual is preparing.",
		indonesian: "Manual video sedang disiapkan",
		japan: "動画マニュアル準備中です.",
		chinese: ""
    },
	C:{
		korea:"가격 정보를 수정하시고자 할대는 MD와 상의해 주세요.",
		english:"If you want to modify price information please cotact with MD.",
		indonesian: "Jika Anda ingin memodifikasi informasi harga silakan hubungi MD.",
		japan: "価格情報を修正する場合はMDと相談してください.",
		chinese: ""
    },
	D:{
		korea:"상품등록후 확인하실수 있습니다.",
		english:"It can be checked after product registration.",
		indonesian: "Dapat diperiksa setelah pendaftaran produk",
		japan: "商品登録の後確認することができます.",
		chinese: ""
    },
	E:{
		korea:"비회원가를 입력후 적립금이 계산됩니다.",
		english:"Mileage points is calculated after inputting nonmember price.",
		indonesian: "Kupon poin dikalkulasi setelah memasukkan harga non anggota",
		japan: "非会員原価を入力後、積立金が計算されます.",
		chinese: ""
    },
	F:{
		korea:"마지막 한개는 삭제 하실 수 없습니다.",
		english:"You can not delete the last one.",
		indonesian: "Anda tidak dapat menghapus yang terakhir",
		japan: "最後の一つは削除することができません.",
		chinese: ""
    },
	G:{
		korea:"더블클릭시 해당 라인이 삭제 됩니다.",
		english:"When you double-click, the table will be deleted.",
		indonesian: "Tabel akan terhapus saat Anda mengklik dua kali",
		japan: "ダブルクリック時に該当のラインが削除になります.",
		chinese: ""
    },
	H:{
		korea:"카테고리를 선택해주세요.",
		english:"Please select a category.",
		indonesian: "Silakan pilih kategori",
		japan: "カテゴリーを選択してください.",
		chinese: ""
    }
};

language_data["sns_coupon_list.js"] = {	
	A:{
		korea:"쿠폰번호를 넣으세요.",
		english:"Enter the coupon number.",
		indonesian: "Kategori sudah Masukkan nomor kupon",
		japan: "クーポン番号を入れてください.",
		chinese: ""
    },
	B:{
		korea:"error",
		english:"error.",
		indonesian: "error",
		japan: "error",
		chinese: ""
   }
};

language_data["sns_coupon_pop.php"] = {	
	A:{
		korea:"쿠폰번호 뒷자리를 입력해주세요.",
		english:"Please enter the last digits of coupon number.",
		indonesian: "Silakan masukkan digit terakhir nomor kupon",
		japan: "クーポン番号の後半を入力してください.",
		chinese: ""
   }
};

language_data["sns_coupon_orders_list.act.php"] = {	
	A:{
		korea:"건의 SMS 가 정상적으로 발송되었습니다",
		english:"of SMS was sent successfully.",
		indonesian: "SMS terkirim",
		japan: "件のSMS が正常に発送されました",
		chinese: ""
   }
};

language_data["sns_category.save.php"] = {	
	A:{
		korea:"하부 카테고리가 존제 합니다.하부 카테고리를 먼저 삭제하신후 다시 시도해 주세요",
		english:"There is a sub-category. Please try again after deleting sub-category.",
		indonesian: "Terdapat sub kategori. Silakan coba lagi setelah menghapus sub kategori",
		japan: "下部カテゴリーが存在します.下部カテゴリーを先に削除後、再試行してください",
		chinese: ""
   }
};
language_data["sns_goods_batch.php"] = {	
	A:{
		korea:"검색 정지중입니다.",
		english:"Search is stopped.",
		indonesian: "Pencarian dihentikan",
		japan: "検索停止中です.",
		chinese: ""
   }
};

language_data["sns_coupon_orders_list.php"] = {	
	A:{
		korea:"적용대상중 [검색회원전체]는  검색후 사용 가능합니다. 확인후 다시 시도해주세요.",
		english:"You may use [All searched members] after a search. Please try again after checking.",
		indonesian: "Anda dapat menggunakan (Semua Anggota yang dicari) setelah mencari. Silakan coba lagi setelah memeriksa",
		japan: "適用対象中 [検索会員全体]は検索後、使用可能です. 確認後、再試行してください.",
		chinese: ""
   },
	B:{
		korea:"선택된 수신자가 없습니다. 변경/발송 하시고자 하는 수신자를 선택하신 후 저장/보내기 버튼을 클릭해주세요",
		english:"There are no recipients selected. Please click the Save/Send button after selecting Change / Send you wish to change.",
		indonesian: "Anda belum memilih penerima. Silakan klik tombol Simpan/Kirim setelah memilih Ubah/Kirim yang Anda ingin ubah",
		japan: "選択された受信者がいません. 変更/発送する受信者を選択した後、保存/送信ボタンをクリックしてください",
		chinese: ""
   },
	C:{
		korea:"SMS 발송 내역을 입력하신후 보내기 버튼을 클릭해주세요",
		english:"Please click the 'Send' button, after entering the SMS message.",
		indonesian: "Silakan klik tombol 'Kirim' setelah menulis SMS",
		japan: "SMS 発送内訳を入力後、送信ボタンをクリックしてください",
		chinese: ""
   },
	D:{
		korea:"검색회원 전체에게 SMS 발송을 하시겠습니까?",
		english:"Do you want to send a SMS to all searched memebers?",
		indonesian: "Apakah Anda ingin mengirim SMS ke semua anggota yang dicari?",
		japan: "検索会員全体に SMS 発送しますか?",
		chinese: ""
   },
	E:{
		korea:"선택한 회원에게 SMS 발송을 하시겠습니까?",
		english:"Do you want to send a SMS to all selected memebers?",
		indonesian: "Apakah Anda ingin mengirim SMS ke semua anggota terpilih?",
		japan: "選択した会員に SMS 発送をしますか?",
		chinese: ""
   }
};

language_data["sns_goods_batch.act.php"] = {	
	A:{
		korea:"재고확인 처리가 완료되었습니다",
		english:"The stock checking has been completed.",
		indonesian: "Pemeriksaan persediaan sudah selesai",
		japan: "再考確認処理が完了しました",
		chinese: ""
   }
};

language_data["sns_product_list.js"] = {	
	A:{
		korea:"삭제하실 제품을 한개이상 선택하셔야 합니다.",
		english:"You must select a list of more than one to delete.",
		indonesian: "Anda harus memilih beberapa daftar untuk menghapus",
		japan: "削除する製品を一つ以上選択しなければなりません.",
		chinese: ""
   },
	B:{
		korea:"수정하실 제품을 한개이상 선택하셔야 합니다",
		english:"You must select a list of more than one to modify.",
		indonesian: "Anda harus memilih beberapa daftar untuk memodifikasi",
		japan: "修正する製品を一つ以上選択しなければなりません。",
		chinese: ""
   },
	C:{
		korea:"검색상품 전체에 대한 적용은 검색후 가능합니다",
		english:"Applying for all searched product can be applied after a search.",
		indonesian: "Terapkan untuk semua produk yang dicari dapat diterapkan setelah melakukan pencarian",
		japan: "検索商品全体に対する適用は検索後、可能です",
		chinese: ""
   },
	D:{
		korea:"선택된 제품이 없습니다. 변경하시고자 하는 상품을 선택하신 후 저장 버튼을 클릭해주세요",
		english:"There are no products selected. Please click the Save button after selecting items you wish to change.",
		indonesian: "Tidak ada produk yang dipilih. Silakan klik tombol Simpan setelah memilih produk yang ingin Anda ubah",
		japan: "選択された製品がありません. 変更する商品を選択後、保存ボタンをクリックしてください",
		chinese: ""
   },
	E:{
		korea:"해당 상품에 대한 정보를 수정하시겠습니까?",
		english:"Do you want to edit information of the products?",
		indonesian: "Apakah Anda ingin mengedit informasi produk?",
		japan: "該当の商品に対する情報を修正しますか?",
		chinese: ""
   },
	F:{
		korea:"삭제하시겠습니까?",
		english:"Do you really want to delete?",
		indonesian: "Apakah Anda ingin menghapus?",
		japan: "削除しますか?",
		chinese: ""
   },
	G:{
		korea:"검색상품 전체에 정보변경을 하시겠습니까?",
		english:"Do you want to change information to all searched products?",
		indonesian: "Apakah Anda ingin mengubah informasi semua produk yang dicari?",
		japan: "検索商品全体に情報変更をしますか?",
		chinese: ""
   }
};

language_data["sns_product_input.js"] = {	
	A:{
		korea:"판매가격이 입력되지 않았습니다.",
		english:"The selling price has not been entered.",
		indonesian: "Harga jual belum dimasukkan",
		japan: "販売価格が入力されていません.",
		chinese: ""
   },
	B:{
		korea:"현금사용시 적립율이 입력되지 않았습니다",
		english:"The accumulation rate has not been entered on use of cash.",
		indonesian: "Tingkat akumulasi belum dimasukkan ke dalam penggunaan pembayaran tunai",
		japan: "現金使用時の積立率が入力されていません",
		chinese: ""
   },
	C:{
		korea:"카드사용시 적립율이 입력되지 않았습니다",
		english:"The accumulation rate has not been entered on use of credit cards.",
		indonesian: "Tingkat akumulasi belum dimasukkan ke dalam penggunaan kartu kredit",
		japan: "カード使用時の積立率が入力されていません",
		chinese: ""
   },
	D:{
		korea:"판매가격이 입력되지 않았습니다",
		english:"The selling price has not been entered.",
		indonesian: "Harga jual belum dimasukkan",
		japan: "販売価格が入力されていません",
		chinese: ""
   },
	E:{
		korea:"카드수수료가 입력되지 않았습니다",
		english:"The credit card fee has not been entered.",
		indonesian: "Biaya kartu kredit belum dimasukkan",
		japan: "カード手数料が入力されていません",
		chinese: ""
   },
	F:{
		korea:"제품명이 입력되지 않았습니다",
		english:"The product name has not been entered.",
		indonesian: "Nama produk belum dimasukkan",
		japan: "製品名が入力されていません",
		chinese: ""
   },
	G:{
		korea:"가격에 대한 정보가 변경되었습니다",
		english:"The price information has been changed.",
		indonesian: "Informasi harga sudah diubah",
		japan: "価格に対する情報が変更されました",
		chinese: ""
   },
	H:{
		korea:"공급가격이 입력되지 않았습니다",
		english:"The supply price has not been entered.",
		indonesian: "Harga pasokan belum dimasukkan",
		japan: "供給価格が入力されていません",
		chinese: ""
   },
	I:{
		korea:"제품소개가 입력되지 않았습니다",
		english:"The product introduction has not been entered.",
		indonesian: "Informasi produk belum dimasukkan",
		japan: "製品紹介が入力されていません",
		chinese: ""
   },
	J:{
		korea:"옵션구분값을 입력해주세요",
		english:"Please select a option classification value.",
		indonesian: "Silakan masukkan nilai pilihan klasifikasi",
		japan: "オプション仕分け値を入力してください",
		chinese: ""
   },
	K:{
		korea:"옵션별 비회원가를 입력해주세요",
		english:"Please enter the nonmember price.",
		indonesian: "Silakan masukkan harga untuk non anggota di setiap pilihan",
		japan: "オプション別非会員原価を入力してください",
		chinese: ""
   },
	L:{
		korea:"옵션이름을 선택해주세요",
		english:"Please select the name of the option.",
		indonesian: "Silakan pilih pilihan nama",
		japan: "オプション名を選択してください",
		chinese: ""
   },
	M:{
		korea:"옵션별 회원가를 입력해주세요",
		english:"Please enter the membership price.",
		indonesian: "Silakan masukkan harga untuk anggota di setiap pilihan",
		japan: "オプション別会員原価を入力してください",
		chinese: ""
   },
	N:{
		korea:"옵션별 딜러가를 입력해주세요",
		english:"Please enter the dealer price for each options.",
		indonesian: "Silakan masukkan harga untuk agen di setiap pilihan",
		japan: "オプション別代理価格を入力してください",
		chinese: ""
   },
	O:{
		korea:"옵션별 대리점가를 입력해주세요",
		english:"Please enter the branch store price for each options.",
		indonesian: "Silakan masukkan harga untuk toko cabang di setiap pilihan",
		japan: "オプション別代理価格を入力してください",
		chinese: ""
   },
	P:{
		korea:"문자는 사용할 수 없습니다",
		english:"Characters are not allowed.",
		indonesian: "Karakter tidak boleh digunakan",
		japan: "文字は使うことができません",
		chinese: ""
   },
	Q:{
		korea:"자릿수는 정수로만 구분합니다",
		english:"Digits are separated only by a constant.",
		indonesian: "Karakter dipisahkan hanya dengan konstan",
		japan: "桁は定数だけ区分します",
		chinese: ""
   },
	R:{
		korea:"구매단가(공급가)를 입력후 복사 버튼을 클릭해주세요",
		english:"After enter supply price, click the copy button.",
		indonesian: "Setelah memasukkan harga pasokan klik tombol Copy",
		japan: "購買単価(供給価格)を入力後、コピーボタンをクリックしてください",
		chinese: ""
   },
	S:{
		korea:"정가를 입력후 복사 버튼을 클릭해주세요",
		english:"After enter fixed price, Click the 'Copy button.",
		indonesian: "Setelah memasukkan harga tetap klik tombol Copy",
		japan: "定価入力後、コピーボタンをクリックしてください",
		chinese: ""
   },
	T:{
		korea:"회원가를 입력후 복사 버튼을 클릭해주세요",
		english:"Please click the Copy button after enter the member price.",
		indonesian: "Silakan klik tombol Copy setelah memasukkan harga anggota",
		japan: "会員原価を入力後コピーボタンをクリックしてください",
		chinese: ""
   },
	U:{
		korea:"딜러가를 입력후 복사 버튼을 클릭해주세요",
		english:"Please click the Copy button after enter the dealer price.",
		indonesian: "Silakan klik tombol Copy setelah memasukkan harga agen",
		japan: "代理価格を入力の後コピーボタンをクリックしてください",
		chinese: ""
   },
	V:{
		korea:"검색 정지중입니다",
		english:"Search is stopped.",
		indonesian: "Pencarian dihentikan",
		japan: "検索停止中です",
		chinese: ""
   }
};

language_data["sns_sp_coupon.list.php"] = {	
	A:{
		korea:"해당 쿠폰을 정말로 삭제하시겠습니까?",
		english:"Do you really want to delete the coupon?",
		indonesian: "Apakah Anda ingin menghapus kupon?",
		japan: "該当のクーポンを本当に削除しますか?",
		chinese: ""
   },
	B:{
		korea:"무료쿠폰이 정상적으로 수정되었습니다",
		english:"Free coupons have been modified successfully.",
		indonesian: "Kupon gratis berhasil didaftarkan",
		japan: "無料クーポンが正常に修正されました",
		chinese: ""
   },
	C:{
		korea:"무료쿠폰이 정상적으로 삭제되었습니다",
		english:"Free coupons have been deleted successfully.",
		indonesian: "Kupon gratis berhasil didaftarkan",
		japan: "無料クーポンが正常に削除されました",
		chinese: ""
   },
	D:{
		korea:"무료쿠폰이 정상적으로 수정되었습니다",
		english:"Free coupons have been modified successfully.",
		indonesian: "Kupon gratis berhasil didaftarkan",
		japan: "無料クーポンが正常に修正されました",
		chinese: ""
   }
};

language_data["sns_product_resize.php"] = {	
	A:{
		korea:"배너를 정말로 삭제하시겠습니까?",
		english:"Do you really want to delete the banners?",
		indonesian: "Apakah Anda ingin menghapus banner?",
		japan: "バナーを本当に削除しますか?",
		chinese: ""
   }
};

language_data["sns_free_goods_category.php"] = {	
	A:{
		korea:"해당카테고리  정보를 정말로 삭제하시겠습니까?",
		english:"Do you really want to delete the information of the catoegory?",
		indonesian: "Apakah Anda ingin menghapus informasi kategori?",
		japan: "該当カテゴリー情報を本当に削除しますか?",
		chinese: ""
   }
};

language_data["download.php"] = {	
	A:{
		korea:"다운로드 권한이 없습니다.",
		english:"You do not have permission to download.",
		indonesian: "Anda tidak mempunyai izin untuk mengunduh",
		japan: "ダウンロード権限がありません.",
		chinese: ""
   },
	B:{
		korea:"회원이시라면 로그인 후 이용해 보십시오",
		english:"Are you a member? Please log-in.",
		indonesian: "Apakah Anda anggota? Silakan log-in",
		japan: "会員はログイン後利用してください",
		chinese: ""
   },
	C:{
		korea:"해당 파일이나 경로가 존재하지 않습니다",
		english:"The file or path does not exist.",
		indonesian: "Dokumen atau analisa tidak ada",
		japan: "該当ファイルと経路が存在しません",
		chinese: ""
   },
	D:{
		korea:"파일을 찾을 수 없습니다",
		english:"No file found.",
		indonesian: "Dokumen tidak ditemukan. ",
		japan: "ファイルを捜すことができません",
		chinese: ""
   },
	E:{
		korea:"해당 파일이나 경로가 존재하지 않습니다",
		english:"The file or path does not exist.",
		indonesian: "Dokumen atau analisa tidak ada. ",
		japan: "該当のファイルや経路が存在しません",
		chinese: ""
   }
};

language_data["product_qna.php"] = {	
	A:{
		korea:"제품문의를  정말로 삭제하시겠습니까?",
		english:"Do you really want to delete the inquiry?",
		indonesian: "Apakah Anda ingin menghapus pertanyaan?",
		japan: "製品お問い合わせを本当に削除しますか?",
		chinese: ""
   }
};

language_data["useafter.list.php"] = {	
	A:{
		korea:"사용후기를 정말로 삭제하시겠습니까?",
		english:"Do you really want to delete the reviews?",
		indonesian: "Apakah Anda ingin menghapus review?",
		japan: "使用レビューを本当に削除しますか?",
		chinese: ""
   }
};

language_data["ReportReferTree.php"] = {	
	A:{
		korea:"아래 카테고리를 선택하세요",
		english:"Select a category below",
		indonesian: "Pilih kategori di bawah ini",
		japan: "下のカテゴリーを選択してください",
		chinese: ""
   }
};

language_data["refererorder.php"] = {	
	A:{
		korea:"더이상 진행할 방향이 없습니다",
		english:"There is no way to proceed",
		indonesian: "Tidak dapat diproses",
		japan: "これ以上進行する方向がありません",
		chinese: ""
   }
};

language_data["referer.js"] = {	
	A:{
		korea:"수정/삭제 하시고자 하는 상품카테고리를 선택해 주세요",
		english:"Please select a product category you want to modefy/delete",
		indonesian: "Silakan pilih kategori produk yang ingin Anda modifikasi/hapus",
		japan: "修正/削除する商品カテゴリーを選択してください",
		chinese: ""
   },
	B:{
		korea:"추가 하시고자 하는 상품카테고리를 선택해 주세요",
		english:"Please select a product category you want to add.",
		indonesian: "Silakan pilih kategori produk yang ingin Anda tambahkan",
		japan: "追加する商品カテゴリーを選択してください",
		chinese: ""
   },
	C:{
		korea:"해당 파일이나 경로가 존재하지 않습니다",
		english:"The file or path does not exist.",
		indonesian: "Dokumen atau analisa tidak ada",
		japan: "該当のファイルや経路が存在しません",
		chinese: ""
   },
	D:{
		korea:"추가 하시고자 하는 상품카테고리를 입력해 주세요",
		english:"Please select a product category you want to add.",
		indonesian: "Silakan pilih kategori produk yang ingin Anda tambahkan. ",
		japan: "追加する商品カテゴリーを入力してください",
		chinese: ""
   },
	E:{
		korea:"카테고리구성은 4단계까지만 가능합니다",
		english:"Category consists is possible only four steps.",
		indonesian: "Kategori terdiri dari empat langkah. ",
		japan: "カテゴリー構成は4段階まで可能です",
		chinese: ""
   },
	F:{
		korea:"상품카테고리를 선택해주세요",
		english:"Please select a product category.",
		indonesian: "Silakan pilih kategori produk. ",
		japan: "商品カテゴリーを選択してください",
		chinese: ""
   }
};

language_data["categoryorder.php"] = {	
	A:{
		korea:"더이상 진행할 방향이 없습니다",
		english:"There is no way to proceed",
		indonesian: "Tidak dapat diproses",
		japan: "これ以上進行する方向がありません",
		chinese: ""
   }
};

language_data["page.php"] = {	
	A:{
		korea:"통계 관리자 모드에서는 달력을 사용 하실 수 없습니다",
		english:"You can not use the calendar on the mode of the statistics manager",
		indonesian: "Anda tidak dapat menggunakan kalender dalam mode manager statistik",
		japan: "統計管理者モードではカレンダーを使うことができません",
		chinese: ""
   }
};

language_data["referer.save.php"] = {	
	A:{
		korea:"하부 카테고리가 존제 합니다.하부 카테고리를 먼저 삭제하신후 다시 시도해 주세요",
		english:"There is a sub-category. Please try again after deleting sub-category.",
		indonesian: "Terdapat sub kategori. Silakan coba lagi setelah menghapus sub kategori.",
		japan: "下部カテゴリーがあります.下部カテゴリーを先に削除した後、再試行してください",
		chinese: ""
   },
	B:{
		korea:"삭제되었습니다",
		english:"It has been deleted.",
		indonesian: "Sudah dihapus",
		japan: "削除されました",
		chinese: ""
	}	 
};

language_data["referer.php"] = {	
	A:{
		korea:"통계 관리자 모드에서는 달력을 사용 하실 수 없습니다",
		english:"You can not use the calendar on the mode of the statistics manager",
		indonesian: "Anda tidak dapat menggunakan kalender dalam mode manager statistik",
		japan: "統計管理者モードではカレンダーを使うことができません",
		chinese: ""
   }
};

language_data["reportpage.class"] = {	
	A:{
		korea:"관리자 로그인후 사용해주세요",
		english:"Please use after the administrator log-in",
		indonesian: "Silakan gunakan setelah administrator log-in",
		japan: "管理者ログイン後、使用してください",
		chinese: ""
   }
};

language_data["FusionCharts.js"] = {	
	A:{
		korea:"You need Adobe Flash Player 6 (or above) to view the charts. It is a free and lightweight installation from Adobe.com. Please click on Ok to install the same",
		english:"You need Adobe Flash Player 6 (or above) to view the charts. It is a free and lightweight installation from Adobe.com. Please click on Ok to install the same",
		indonesian: "Anda membutuhkan Adobe Flash Player 6 (atau aplikasi terbaru) untuk melihat grafik. Ini adalah instalasi gratis dan ringan dari Adobe.com. Silakan klik OK untuk menginstal yang sama",
		japan: "You need Adobe Flash Player 6 (or above) to view the charts. It is a free and lightweight installation from Adobe.com. Please click on Ok to install the same",
		chinese: ""
   }
};

language_data["cupon_publish.php"] = {	
	A:{
		korea:"쿠폰종류가 선택되지 않았습니다. 쿠폰종류를 선택해주세요.",
		english:"Coupon type has not been selected. Please select the type of coupon.",
		indonesian: "Tipe kupon belum dipilih. Silakan pilih tipe kupon",
		japan: "クーポン種類が選択されていません. クーポン種類を選択してください.",
		chinese: ""
   },
	B:{
		korea:"발행일로부터의 사용기간을 입력해주세요",
		english:"Please enter the expiration date form issued date.",
		indonesian: "Silakan masukkan tanggal terakhir berlaku sejak tanggal dikeluarkan",
		japan: "発行日からの使用期間を入力してください",
		chinese: ""
   },
	C:{
		korea:"등록일로부터의 사용기간을 입력해주세요",
		english:"Please enter the expiration date form registered date.",
		indonesian: "Silakan masukkan tanggal terakhir berlaku sejak tanggal didaftarkan",
		japan: "登録日からの使用期間を入力してください",
		chinese: ""
   },
	D:{
		korea:"결제가격 조건을 입력해 주세요",
		english:"Please enter the conditions of payment price.",
		indonesian: "Silakan masukkan ketentuan harga pembayaran. ",
		japan: "決済価格条件を入力してください",
		chinese: ""
   },
	E:{
		korea:"지정발행의 경우 사용자를 선택하셔야 합니다",
		english:"In case of designated issuing should be choose a user.",
		indonesian: "Harus memilih pengguna jika ingin mengeluarkan. ",
		japan: "指定発行の場合、使用者を選択しなければいけません",
		chinese: ""
   },
	F:{
		korea:"카테고리를 선택해주세요",
		english:"Please select a category.",
		indonesian: "Silakan pilih kategori. ",
		japan: "カテゴリーを選択してください",
		chinese: ""
   },
	G:{
		korea:"이미등록된 카테고리 입니다",
		english:"The category is already registered.",
		indonesian: "Kategori sudah terdaftar. ",
		japan: "すでに登録されたカテゴリーです",
		chinese: ""
   }
};

language_data["hot.write.js"] = {	
	A:{
		korea:"상품그룹은 20개까지만 가능합니다",
		english:"Product groups should be no more than 20",
		indonesian: "Grup produk tidak boleh lebih dari 20",
		japan: "商品グループは 20個まで可能です",
		chinese: ""
   },
	B:{
		korea:"상품 그룹을 삭제하실려면 마지막 상품그룹 부터 삭제하셔야 합니다",
		english:"If you want to delete product group. You should be deleted from the final product group.",
		indonesian: "Jika Anda ingin menghapus grup produk, Anda harus menghapus grup produk terakhir",
		japan: "商品グループを削除するなら、最後の商品グループから削除しなければいけません",
		chinese: ""
   }
};

language_data["relationAjax.js"] = {	
	A:{
		korea:"XML 객체 생성 실패",
		english:"Creating XML has been Failed",
		indonesian: "Pembuatan XML gagal",
		japan: "XML 作成失敗",
		chinese: ""
   },
	B:{
		korea:"이미등록된 상품입니다",
		english:"The product is already registered.",
		indonesian: "Produk sudah terdaftar",
		japan: "既に登録された商品です",
		chinese: ""
   }
};

language_data["promotion_goods.php"] = {	
	A:{
		korea:"프로모션 상품분류를 선택하셔야 합니다",
		english:"You must select a product classification of promotion",
		indonesian: "Anda harus memilih promosi klasifikasi produk",
		japan: "プロモーション商品分類を選択しなければなりません",
		chinese: ""
   },
	B:{
		korea:"프로모션 카테고리를 선택하셔야 합니다",
		english:"You must select a category of promotion.",
		indonesian: "Anda harus memilih kategori promosi",
		japan: "プロモーションカテゴリーを選択しなければなりません",
		chinese: ""
   },
	C:{
		korea:"동영상 메뉴얼 준비중입니다",
		english:"Video menual are preparing.",
		indonesian: "Video manual sedang disiapkan",
		japan: "動画マニュアル準備中です",
		chinese: ""
   }
};

language_data["promotion.write.js"] = {	
	A:{
		korea:"상품 그룹을 삭제하실려면 마지막 상품그룹 부터 삭제하셔야 합니다",
		english:"'If you want to delete product group. You should be deleted from the final product group",
		indonesian: "Jika Anda ingin menghapus grup produk, Anda harus menghapus grup produk terakhir",
		japan: "商品グループを削除する場合、最後の商品グループから削除しなければなりません",
		chinese: ""
   }
};

language_data["relationAjaxForHot.js"] = {	
	A:{
		korea:"이미등록된 상품입니다",
		english:"The product is already registered",
		indonesian: "Produk sudah terdaftar",
		japan: "既に登録された商品です",
		chinese: ""
   }
};

language_data["event.write.js"] = {	
	A:{
		korea:"상품그룹은 20개까지만 가능합니다",
		english:"Product groups should be no more than 20",
		indonesian: "Grup produk tidak boleh lebih dari 20",
		japan: "商品グループは 20個まで可能です",
		chinese: ""
   },
	B:{
		korea:"상품 그룹을 삭제하실려면 마지막 상품그룹 부터 삭제하셔야 합니다",
		english:"If you want to delete product group. You should be deleted from the final product group.",
		indonesian: "Jika Anda ingin menghapus grup produk, Anda harus menghapus grup produk terakhir",
		japan: "商品グループを削除する場合、最後の商品グループから削除しなければなりません",
		chinese: ""
   },
	C:{
		korea:"분류명을 입력해주세요",
		english:"Please enter the name of the classification name.",
		indonesian: "Silakan masukkan nama klasifikasi",
		japan: "分類名を入力してください",
		chinese: ""
   }
};

language_data["category_select.php"] = {	
	A:{
		korea:"카테고리를 선택해주세요",
		english:"Please select a category",
		indonesian: "Silakan pilih kategori",
		japan: "カテゴリーを選択してください",
		chinese: ""
   },
	B:{
		korea:"이미등록된 카테고리 입니다",
		english:"The ategory is already registered.",
		indonesian: "Kategori sudah terdaftar",
		japan: "すでに登録されたカテゴリーです",
		chinese: ""
   }
};

language_data["relationAjaxForEvent.js"] = {	
	A:{
		korea:"XML 문서 해석 실패",
		english:"XML Document parsing failed",
		indonesian: "Pengurangan dokumen XML gagal",
		japan: "XML 文書解析失敗",
		chinese: ""
   }
};

language_data["poll.act.php"] = {	
	A:{
		korea:"이미 설문에 참여 했습니다",
		english:"You have already participated in the survey.",
		indonesian: "Anda sudah berpartisipasi dalam survey",
		japan: "既にアンケートに参加しました",
		chinese: ""
   }
};

language_data["hot.write.php"] = {	
	A:{
		korea:"동영상 메뉴얼 준비중입니다",
		english:"Classification of category main has been registered successfully",
		indonesian: "Klasifikasi kategori utama berhasil didaftarkan",
		japan: "動画マニュアル準備中です",
		chinese: ""
   }
};

language_data["relation.category.act.php"] = {	
	A:{
		korea:"이미 등록된 상품입니다",
		english:"The product is already registered",
		indonesian: "Produk sudah terdaftar",
		japan: "すでに登録された商品です",
		chinese: ""
   }
};

language_data["product_order3.js"] = {	
	A:{
		korea:"이동할 상품을 선택해주세요",
		english:"Please select a product to move",
		indonesian: "Silakan pilih produk untuk dipindahkan",
		japan: "移動する商品を選択してください",
		chinese: ""
   }
};

language_data["cupon.act.php"] = {	
	A:{
		korea:"삭제할수 없습니다",
		english:"You can not delete",
		indonesian: "Anda tidak dapat menghapus",
		japan: "削除することができません",
		chinese: ""
   },
	B:{
		korea:"사용되지 않은 쿠폰이 존재합니다. 쿠폰등록을 취소한후 삭제하시기 바랍니다",
		english:"There is an unused coupons. Please delete after cancel the registration of coupon.",
		indonesian: "",
		japan: "使えないクーポンが存在します. クーポン登録を取消し後削除してください",
		chinese: ""
   }
};

language_data["cupon.js"] = {	
	A:{
		korea:"이미지 파일만 업로드 하실수 있습니다",
		english:"You can upload image files only",
		indonesian: "",
		japan: "イメージファイルだけアップロードすることができます",
		chinese: ""
   },
	B:{
		korea:"BMP 파일은 웹상에서 사용하기엔 적절한 이미지 포맷이 아닙니다.\n그래도 계속 하시겠습니까?",
		english:"BMP file format is not an appropriate image on the web.\nDo you want to continue anyway?",
		indonesian: "Format dokumen BMP tidak sesuai dengan gambar di situs. Apakah Anda ingin melanjutkan ?",
		japan: "BMP ファイルが、ウェブ上で使える適切なイメージフォーマットではありません.\nそれでも続けますか?",
		chinese: ""
   }
};

language_data["promotion_goods.js"] = {	
	A:{
		korea:"상품그룹은 20개까지만 가능합니다",
		english:"Product groups should be no more than 20",
		indonesian: "Grup produk tidak boleh lebih dari 20",
		japan: "商品グループは 20個まで可能です",
		chinese: ""
   },
	B:{
		korea:"상품 그룹을 삭제하실려면 마지막 상품그룹 부터 삭제하셔야 합니다",
		english:"If you want to delete product group. You should be deleted from the final product group.",
		indonesian: "Jika Anda ingin menghapus grup produk, Anda harus menghapus grup produk terakhir",
		japan: "商品グループを削除する場合、最後の商品グループから削除しなければなりません",
		chinese: ""
   }
};

language_data["promotion_guide.php"] = {	
	A:{
		korea:"해당 프로모션  정말로 삭제하시겠습니까? 삭제하시면 관련된 모든 이미지가 삭제됩니다",
		english:"Do you really want to delete this promotion? All the images will be deleted",
		indonesian: "Apakah Anda ingin menghapus promosi ini? Semua gambar akan dihapus",
		japan: "該当のプロモーションを本当に削除しますか? 削除すると関係するすべてのイメージが削除されます",
		chinese: ""
   }
};

language_data["poll.php"] = {	
	A:{
		korea:"해당 설문을 삭제하시겠습니까?",
		english:"Do you want to delete that poll",
		indonesian: "Apakah Anda ingin menghapus polling?",
		japan: "該当のアンケートを削除しますか?",
		chinese: ""
   }
};

language_data["banner_category.php"] = {	
	A:{
		korea:"해당 카테고리 정보를 정말로 삭제하시겠습니까?",
		english:"Do you really want to delete the category information?",
		indonesian: "Apakah Anda ingin menghapus informasi kategori?",
		japan: "該当カテゴリー情報を本当に削除しますか",
		chinese: ""
   }
};

language_data["hot_stuff.php"] = {	
	A:{
		korea:"해당 메인 추천상품 관리상품을 정말로 삭제하시겠습니까? 삭제하시면 관련된 모든 이미지가 삭제됩니다",
		english:"Do you really want to delete the product of main recommendations? All the images will be deleted",
		indonesian: "Apakah Anda ingin menghapus produk rekomendasi utama? Semua gambar akan dihapus",
		japan: "該当するメイン推薦商品、管理商品を本当に削除しますか? 削除すると関係するすべてのイメージが削除されます",
		chinese: ""
   }
};

language_data["popup.list.php"] = {	
	A:{
		korea:"해당 팝업를 정말로 삭제하시겠습니까? 삭제하시면 관련된 모든 이미지가 삭제됩니다",
		english:"Do you really want to delete those pop-ups? All the images will be deleted",
		indonesian: "Apakah Anda ingin menghapus pop-up? Semua gambar akan dihapus",
		japan: "該当のポップアップを本当に削除しますか? 削除すれると関係するすべてのイメージが削除されます",
		chinese: ""
   }
};

language_data["category_main.list.php"] = {	
	A:{
		korea:"해당 프로모션  정말로 삭제하시겠습니까? 삭제하시면 관련된 모든 이미지가 삭제됩니다",
		english:"Do you really want to delete this promotion? All the images will be deleted",
		indonesian: "Apakah Anda ingin menghapus promosi ini? Semua gambar akan dihapus",
		japan: "該当のプロモーションを本当に削除しますか? 削除すると関係するすべてのイメージが削除されます",
		chinese: ""
   }
};

language_data["order_gift.list.php"] = {	
	A:{
		korea:"해당 이벤트를 정말로 삭제하시겠습니까? 삭제하시면 관련된 모든 이미지가 삭제됩니다",
		english:"Do you want to delete that event, really? All the images will be deleted",
		indonesian: "Apakah Anda ingin menghapus event itu? Semua gambar akan dihapus",
		japan: "該当のイベントを本当に削除しますか? 削除すると関係するすべてのイメージが削除されます",
		chinese: ""
   }
};

language_data["banner.php"] = {	
	A:{
		korea:"배너를 정말로 삭제하시겠습니까?",
		english:"Do you really want to delete the banners?",
		indonesian: "Apakah Anda ingin menghapus banner?",
		japan: "バナーを本当に削除しますか?",
		chinese: ""
   }
};

language_data["hot_stuff_category.php"] = {	
	A:{
		korea:"해당카테고리  정보를 정말로 삭제하시겠습니까?",
		english:"Do you really want to delete the category information?",
		indonesian: "Apakah Anda ingin menghapus informasi kategori?",
		japan: "該当カテゴリー情報を本当に削除しますか?",
		chinese: ""
   }
};

language_data["promotion_goods.list.php"] = {	
	A:{
		korea:"해당 프로모션  정말로 삭제하시겠습니까? 삭제하시면 관련된 모든 이미지가 삭제됩니다",
		english:"Do you really want to delete this promotion? All the images will be deleted",
		indonesian: "",
		japan: "該当のプロモーション本当に削除しますか? 削除すると関係するすべてのイメージが削除されます",
		chinese: ""
   }
};

language_data["main_starshop.php"] = {	
	A:{
		korea:"해당이미지정보를 정말로 삭제하시겠습니까?",
		english:"Do you really want to delete the image information?",
		indonesian: "Apakah Anda ingin menghapus informasi gambar?",
		japan: "該当イメージ情報を本当に削除しますか?",
		chinese: ""
   }
};

language_data["cupon_publish_list.php"] = {	
	A:{
		korea:"정말 쿠폰발행을 삭제 하시겠습니까?",
		english:"Do you want to delete the coupon?",
		indonesian: "Apakah Anda ingin menghapus kupon?",
		japan: "本当にクーポン発行を削除しますか?",
		chinese: ""
   }
};

language_data["promotion_category.php"] = {	
	A:{
		korea:"해당카테고리  정보를 정말로 삭제하시겠습니까?",
		english:"Do you really want to delete the category information?",
		indonesian: "Apakah Anda ingin menghapus informasi kategori?",
		japan: "該当カテゴリー情報を本当に削除しますか?",
		chinese: ""
   }
};

language_data["category_main_div.php"] = {	
	A:{
		korea:"해당카테고리  정보를 정말로 삭제하시겠습니까?",
		english:"Do you really want to delete the category information?",
		indonesian: "Apakah Anda ingin menghapus informasi kategori?",
		japan: "該当カテゴリー情報を本当に削除しますか?",
		chinese: ""
   }
};

language_data["event.list.php"] = {	
	A:{
		korea:"해당 이벤트를 정말로 삭제하시겠습니까? 삭제하시면 관련된 모든 이미지가 삭제됩니다",
		english:"Do you really want to delete that event? All the images will be deleted",
		indonesian: "Apakah Anda ingin menghapus event itu? Semua gambar akan dihapus",
		japan: "該当のイベントを本当に削除しますか? 削除すると関係するすべてのイメージが削除されます",
		chinese: ""
   }
};

// 
//alert(   language_data['buyingServiceInfo.php']['B'][language]   );
//alert(   language_data['region.php']['A'][language]   );
//alert(   language_data['common']['A'][language]   );
// language_data['product_input_excel.js']['A'][language]
//<script language='javascript' src='../_language/language.php'></script>
