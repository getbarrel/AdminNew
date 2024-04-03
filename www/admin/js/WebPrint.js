document.write("<OBJECT ");
document.write("	  id=GEPCO_WebPri");
document.write("	  classid='clsid:6CB78280-98C1-48D3-8298-FC3CBA69F8AE'");
document.write("	  codebase='http://www.geps.or.kr/webpri/WebPri_GEPCO.cab#version=1,0,4,0'");
document.write("	  width=0");
document.write("	  height=0");
document.write("	  align=center");
document.write("	  hspace=0");
document.write("	  vspace=0");
document.write(">");
document.write("<param name=ImagePaser value=True>");
document.write("<param name=PaperZoom value=110>");
document.write("</OBJECT>");

function Do_WizViewer() {
    try {
	GEPCO_WebPri.DoClear();
	GEPCO_WebPri.DoAddUrl("http://www.geps.or.kr/webpri/HeaderInfo.js");
	GEPCO_WebPri.DoAddDocument(document);
	GEPCO_WebPri.DoViewer(1);
    } catch (e) {
	alert("WebPri가 정상적으로 설치되지 않았습니다4 44.\n\nWebPri 설치 보안경고시 \"예(Y)\"를 클릭해 주시기 바랍니다.");
    }
}
