<?php
/**
 * 카테고리 등록
 * 
 * @date 2013.11.07
 * @author hjy
 */
include ($_SERVER ["DOCUMENT_ROOT"] . "/class/layout.class");
//include($_SERVER ["DOCUMENT_ROOT"] . "/admin/sellertool/sellertool.lib.php");
$site_code = "cjmall";


/////////////////////////////////////////////// 제조국 ///////////////////////////////////////////////

$Infos[] = array("code"=>"000","code_name"=>"없음");
$Infos[] = array("code"=>"075","code_name"=>"타이티");
$Infos[] = array("code"=>"090","code_name"=>"스코틀랜드");
$Infos[] = array("code"=>"098","code_name"=>"버어마");
$Infos[] = array("code"=>"900","code_name"=>"복합원산지");
$Infos[] = array("code"=>"901","code_name"=>"Imported");
$Infos[] = array("code"=>"902","code_name"=>"수입산");
$Infos[] = array("code"=>"903","code_name"=>"대서양");
$Infos[] = array("code"=>"904","code_name"=>"인도양");
$Infos[] = array("code"=>"905","code_name"=>"남태평양");
$Infos[] = array("code"=>"906","code_name"=>"북태평양");
$Infos[] = array("code"=>"907","code_name"=>"북극해");
$Infos[] = array("code"=>"908","code_name"=>"남극해");
$Infos[] = array("code"=>"909","code_name"=>"아시아");
$Infos[] = array("code"=>"910","code_name"=>"남미");
$Infos[] = array("code"=>"911","code_name"=>"북미");
$Infos[] = array("code"=>"913","code_name"=>"아프리카");
$Infos[] = array("code"=>"914","code_name"=>"오세아니아");
$Infos[] = array("code"=>"915","code_name"=>"동남아");
$Infos[] = array("code"=>"916","code_name"=>"연근해");
$Infos[] = array("code"=>"917","code_name"=>"원양산");
$Infos[] = array("code"=>"918","code_name"=>"캄차카해");
$Infos[] = array("code"=>"919","code_name"=>"태평양");
$Infos[] = array("code"=>"923","code_name"=>"세르비아공화국");
$Infos[] = array("code"=>"924","code_name"=>"세르비아공화국OEM");
$Infos[] = array("code"=>"AD","code_name"=>"안도라");
$Infos[] = array("code"=>"AE","code_name"=>"아랍에미리트");
$Infos[] = array("code"=>"AF","code_name"=>"아프가니스탄");
$Infos[] = array("code"=>"AG","code_name"=>"앤티가 바부다");
$Infos[] = array("code"=>"AI","code_name"=>"앵귈라");
$Infos[] = array("code"=>"AL","code_name"=>"알바니아");
$Infos[] = array("code"=>"AM","code_name"=>"아르메니아");
$Infos[] = array("code"=>"AN","code_name"=>"네덜란드령 앤틸리스");
$Infos[] = array("code"=>"AO","code_name"=>"앙골라");
$Infos[] = array("code"=>"AQ","code_name"=>"남극");
$Infos[] = array("code"=>"AR","code_name"=>"아르헨티나");
$Infos[] = array("code"=>"AS","code_name"=>"미국령 사모아");
$Infos[] = array("code"=>"AT","code_name"=>"오스트리아");
$Infos[] = array("code"=>"AU","code_name"=>"오스트레일리아");
$Infos[] = array("code"=>"AW","code_name"=>"아루바");
$Infos[] = array("code"=>"AZ","code_name"=>"아제르바이잔");
$Infos[] = array("code"=>"BA","code_name"=>"보스니아 헤르체고비나");
$Infos[] = array("code"=>"BA1","code_name"=>"보스니아 헤르체고비나OEM");
$Infos[] = array("code"=>"BB","code_name"=>"바베이도스");
$Infos[] = array("code"=>"BD","code_name"=>"방글라데시");
$Infos[] = array("code"=>"BD1","code_name"=>"방글라데시OEM");
$Infos[] = array("code"=>"BE","code_name"=>"벨기에");
$Infos[] = array("code"=>"BE1","code_name"=>"벨기에OEM");
$Infos[] = array("code"=>"BF","code_name"=>"부르키나파소");
$Infos[] = array("code"=>"BG","code_name"=>"불가리아");
$Infos[] = array("code"=>"BH","code_name"=>"바레인");
$Infos[] = array("code"=>"BI","code_name"=>"부룬디");
$Infos[] = array("code"=>"BJ","code_name"=>"베냉");
$Infos[] = array("code"=>"BM","code_name"=>"버뮤다");
$Infos[] = array("code"=>"BN","code_name"=>"브루나이");
$Infos[] = array("code"=>"BO","code_name"=>"볼리비아");
$Infos[] = array("code"=>"BR","code_name"=>"브라질");
$Infos[] = array("code"=>"BR1","code_name"=>"브라질OEM");
$Infos[] = array("code"=>"BS","code_name"=>"바하마");
$Infos[] = array("code"=>"BT","code_name"=>"부탄");
$Infos[] = array("code"=>"BV","code_name"=>"부베이 섬");
$Infos[] = array("code"=>"BW","code_name"=>"보츠와나");
$Infos[] = array("code"=>"BY","code_name"=>"벨로루시");
$Infos[] = array("code"=>"BZ","code_name"=>"벨리즈");
$Infos[] = array("code"=>"CA","code_name"=>"캐나다");
$Infos[] = array("code"=>"CA1","code_name"=>"캐나다OEM");
$Infos[] = array("code"=>"CC","code_name"=>"코코스 제도");
$Infos[] = array("code"=>"CD","code_name"=>"콩고 민주 공화국");
$Infos[] = array("code"=>"CF","code_name"=>"중앙 아프리카 공화국");
$Infos[] = array("code"=>"CG","code_name"=>"콩고");
$Infos[] = array("code"=>"CH","code_name"=>"스위스");
$Infos[] = array("code"=>"CH1","code_name"=>"스위스OEM");
$Infos[] = array("code"=>"CI","code_name"=>"코트디부아르");
$Infos[] = array("code"=>"CK","code_name"=>"쿡 제도");
$Infos[] = array("code"=>"CL","code_name"=>"칠레");
$Infos[] = array("code"=>"CM","code_name"=>"카메룬");
$Infos[] = array("code"=>"CN","code_name"=>"중국");
$Infos[] = array("code"=>"CN1","code_name"=>"중국OEM");
$Infos[] = array("code"=>"CO","code_name"=>"콜롬비아");
$Infos[] = array("code"=>"CR","code_name"=>"코스타리카");
$Infos[] = array("code"=>"CR1","code_name"=>"코스타리카OEM");
$Infos[] = array("code"=>"CU","code_name"=>"쿠바");
$Infos[] = array("code"=>"CV","code_name"=>"카보베르데");
$Infos[] = array("code"=>"CX","code_name"=>"크리스마스 섬");
$Infos[] = array("code"=>"CY","code_name"=>"키프로스");
$Infos[] = array("code"=>"CZ","code_name"=>"체코");
$Infos[] = array("code"=>"CZ1","code_name"=>"체코OEM");
$Infos[] = array("code"=>"DE","code_name"=>"독일");
$Infos[] = array("code"=>"DE1","code_name"=>"독일OEM");
$Infos[] = array("code"=>"DJ","code_name"=>"지부티");
$Infos[] = array("code"=>"DK","code_name"=>"덴마크");
$Infos[] = array("code"=>"DM","code_name"=>"도미니카");
$Infos[] = array("code"=>"DO","code_name"=>"도미니카 공화국");
$Infos[] = array("code"=>"DZ","code_name"=>"알제리");
$Infos[] = array("code"=>"EC","code_name"=>"에콰도르");
$Infos[] = array("code"=>"EE","code_name"=>"에스토니아");
$Infos[] = array("code"=>"EG","code_name"=>"이집트");
$Infos[] = array("code"=>"EH","code_name"=>"서사하라");
$Infos[] = array("code"=>"ER","code_name"=>"에리트레아");
$Infos[] = array("code"=>"ES","code_name"=>"스페인");
$Infos[] = array("code"=>"ES1","code_name"=>"스페인OEM");
$Infos[] = array("code"=>"ET","code_name"=>"에티오피아");
$Infos[] = array("code"=>"EU","code_name"=>"유럽연합");
$Infos[] = array("code"=>"FI","code_name"=>"핀란드");
$Infos[] = array("code"=>"FJ","code_name"=>"피지");
$Infos[] = array("code"=>"FK","code_name"=>"포클랜드 제도");
$Infos[] = array("code"=>"FO","code_name"=>"페로 제도");
$Infos[] = array("code"=>"FR","code_name"=>"프랑스");
$Infos[] = array("code"=>"FR1","code_name"=>"프랑스OEM");
$Infos[] = array("code"=>"FX","code_name"=>"프랑스 메트로폴리탄");
$Infos[] = array("code"=>"GA","code_name"=>"가봉");
$Infos[] = array("code"=>"GB","code_name"=>"영국");
$Infos[] = array("code"=>"GB1","code_name"=>"영국OEM");
$Infos[] = array("code"=>"GD","code_name"=>"그레나다");
$Infos[] = array("code"=>"GE","code_name"=>"그루지야");
$Infos[] = array("code"=>"GF","code_name"=>"프랑스령 기아나");
$Infos[] = array("code"=>"GH","code_name"=>"가나");
$Infos[] = array("code"=>"GI","code_name"=>"지브롤터");
$Infos[] = array("code"=>"GL","code_name"=>"그린란드");
$Infos[] = array("code"=>"GM","code_name"=>"감비아");
$Infos[] = array("code"=>"GN","code_name"=>"기니");
$Infos[] = array("code"=>"GP","code_name"=>"과들루프");
$Infos[] = array("code"=>"GQ","code_name"=>"적도 기니");
$Infos[] = array("code"=>"GR","code_name"=>"그리스");
$Infos[] = array("code"=>"GS","code_name"=>"사우스조지아/사우스샌드위치");
$Infos[] = array("code"=>"GT","code_name"=>"과테말라");
$Infos[] = array("code"=>"GU","code_name"=>"괌");
$Infos[] = array("code"=>"GW","code_name"=>"기니비사우");
$Infos[] = array("code"=>"GY","code_name"=>"가이아나");
$Infos[] = array("code"=>"HK","code_name"=>"홍콩");
$Infos[] = array("code"=>"HK1","code_name"=>"홍콩OEM");
$Infos[] = array("code"=>"HM","code_name"=>"허드 섬 및 맥도널드 제도");
$Infos[] = array("code"=>"HN","code_name"=>"온두라스");
$Infos[] = array("code"=>"HR","code_name"=>"크로아티아/헤르바츠카");
$Infos[] = array("code"=>"HT","code_name"=>"아이티");
$Infos[] = array("code"=>"HU","code_name"=>"헝가리");
$Infos[] = array("code"=>"HU1","code_name"=>"헝가리OEM");
$Infos[] = array("code"=>"ID","code_name"=>"인도네시아");
$Infos[] = array("code"=>"ID1","code_name"=>"인도네시아OEM");
$Infos[] = array("code"=>"IE","code_name"=>"아일랜드");
$Infos[] = array("code"=>"IE1","code_name"=>"아일랜드OEM");
$Infos[] = array("code"=>"IL","code_name"=>"이스라엘");
$Infos[] = array("code"=>"IN","code_name"=>"인도");
$Infos[] = array("code"=>"IN1","code_name"=>"인도OEM");
$Infos[] = array("code"=>"IO","code_name"=>"영국령 인도양 식민지");
$Infos[] = array("code"=>"IQ","code_name"=>"이라크");
$Infos[] = array("code"=>"IR","code_name"=>"이란");
$Infos[] = array("code"=>"IS","code_name"=>"아이슬란드");
$Infos[] = array("code"=>"IT","code_name"=>"이탈리아");
$Infos[] = array("code"=>"IT1","code_name"=>"이탈리아OEM");
$Infos[] = array("code"=>"JM","code_name"=>"자메이카");
$Infos[] = array("code"=>"JO","code_name"=>"요르단");
$Infos[] = array("code"=>"JP","code_name"=>"일본");
$Infos[] = array("code"=>"JP1","code_name"=>"일본OEM");
$Infos[] = array("code"=>"KE","code_name"=>"케냐");
$Infos[] = array("code"=>"KG","code_name"=>"키르기스스탄");
$Infos[] = array("code"=>"KH","code_name"=>"캄보디아");
$Infos[] = array("code"=>"KI","code_name"=>"키리바시");
$Infos[] = array("code"=>"KM","code_name"=>"코모로");
$Infos[] = array("code"=>"KN","code_name"=>"세인트 크리스토퍼 네비스");
$Infos[] = array("code"=>"KP","code_name"=>"북한");
$Infos[] = array("code"=>"KP1","code_name"=>"북한OEM");
$Infos[] = array("code"=>"KR","code_name"=>"한국");
$Infos[] = array("code"=>"KR0","code_name"=>"국산");
$Infos[] = array("code"=>"KR1","code_name"=>"한국OEM");
$Infos[] = array("code"=>"KW","code_name"=>"쿠웨이트");
$Infos[] = array("code"=>"KY","code_name"=>"케이맨 제도");
$Infos[] = array("code"=>"KZ","code_name"=>"카자흐스탄");
$Infos[] = array("code"=>"LA","code_name"=>"라오스");
$Infos[] = array("code"=>"LB","code_name"=>"레바논");
$Infos[] = array("code"=>"LC","code_name"=>"세인트 루시아");
$Infos[] = array("code"=>"LI","code_name"=>"리히텐슈타인");
$Infos[] = array("code"=>"LK","code_name"=>"스리랑카");
$Infos[] = array("code"=>"LK1","code_name"=>"스리랑카OEM");
$Infos[] = array("code"=>"LR","code_name"=>"라이베리아");
$Infos[] = array("code"=>"LS","code_name"=>"레소토");
$Infos[] = array("code"=>"LT","code_name"=>"리투아니아");
$Infos[] = array("code"=>"LU","code_name"=>"룩셈부르크");
$Infos[] = array("code"=>"LV","code_name"=>"라트비아");
$Infos[] = array("code"=>"LY","code_name"=>"리비아");
$Infos[] = array("code"=>"MA","code_name"=>"모로코");
$Infos[] = array("code"=>"MC","code_name"=>"모나코");
$Infos[] = array("code"=>"MD","code_name"=>"몰도바");
$Infos[] = array("code"=>"MD1","code_name"=>"몰도바OEM");
$Infos[] = array("code"=>"MG","code_name"=>"마다가스카르");
$Infos[] = array("code"=>"MH","code_name"=>"마셜 제도");
$Infos[] = array("code"=>"MK","code_name"=>"마케도니아");
$Infos[] = array("code"=>"ML","code_name"=>"말리");
$Infos[] = array("code"=>"MM","code_name"=>"미얀마");
$Infos[] = array("code"=>"MN","code_name"=>"몽골");
$Infos[] = array("code"=>"MO","code_name"=>"마카오");
$Infos[] = array("code"=>"MP","code_name"=>"북마리아나 제도");
$Infos[] = array("code"=>"MQ","code_name"=>"마르티니크");
$Infos[] = array("code"=>"MR","code_name"=>"모리타니");
$Infos[] = array("code"=>"MS","code_name"=>"몬트세라트");
$Infos[] = array("code"=>"MT","code_name"=>"몰타");
$Infos[] = array("code"=>"MU","code_name"=>"모리셔스");
$Infos[] = array("code"=>"MV","code_name"=>"몰디브");
$Infos[] = array("code"=>"MW","code_name"=>"말라위");
$Infos[] = array("code"=>"MX","code_name"=>"멕시코");
$Infos[] = array("code"=>"MX1","code_name"=>"멕시코OEM");
$Infos[] = array("code"=>"MY","code_name"=>"말레이시아");
$Infos[] = array("code"=>"MY1","code_name"=>"말레이시아OEM");
$Infos[] = array("code"=>"MZ","code_name"=>"모잠비크");
$Infos[] = array("code"=>"NA","code_name"=>"나미비아");
$Infos[] = array("code"=>"NC","code_name"=>"뉴칼레도니아");
$Infos[] = array("code"=>"NE","code_name"=>"니제르");
$Infos[] = array("code"=>"NF","code_name"=>"노퍽 섬");
$Infos[] = array("code"=>"NG","code_name"=>"나이지리아");
$Infos[] = array("code"=>"NI","code_name"=>"니카라과");
$Infos[] = array("code"=>"NL","code_name"=>"네덜란드");
$Infos[] = array("code"=>"NO","code_name"=>"노르웨이");
$Infos[] = array("code"=>"NP","code_name"=>"네팔");
$Infos[] = array("code"=>"NR","code_name"=>"나우루");
$Infos[] = array("code"=>"NU","code_name"=>"니우에");
$Infos[] = array("code"=>"NZ","code_name"=>"뉴질랜드");
$Infos[] = array("code"=>"OM","code_name"=>"오만");
$Infos[] = array("code"=>"PA","code_name"=>"파나마");
$Infos[] = array("code"=>"PE","code_name"=>"페루");
$Infos[] = array("code"=>"PF","code_name"=>"프랑스령 폴리네시아");
$Infos[] = array("code"=>"PG","code_name"=>"파푸아뉴기니");
$Infos[] = array("code"=>"PH","code_name"=>"필리핀");
$Infos[] = array("code"=>"PH1","code_name"=>"필리핀OEM");
$Infos[] = array("code"=>"PK","code_name"=>"파키스탄");
$Infos[] = array("code"=>"PL","code_name"=>"폴란드");
$Infos[] = array("code"=>"PL1","code_name"=>"폴란드OEM");
$Infos[] = array("code"=>"PM","code_name"=>"세인트 피에르 미켈론");
$Infos[] = array("code"=>"PN","code_name"=>"핏케언");
$Infos[] = array("code"=>"PR","code_name"=>"푸에르토리코");
$Infos[] = array("code"=>"PT","code_name"=>"포르투갈");
$Infos[] = array("code"=>"PT1","code_name"=>"포르투갈OEM");
$Infos[] = array("code"=>"PW","code_name"=>"팔라우");
$Infos[] = array("code"=>"PY","code_name"=>"파라과이");
$Infos[] = array("code"=>"QA","code_name"=>"카타르");
$Infos[] = array("code"=>"RE","code_name"=>"리유니언");
$Infos[] = array("code"=>"RO","code_name"=>"루마니아");
$Infos[] = array("code"=>"RU","code_name"=>"러시아");
$Infos[] = array("code"=>"RW","code_name"=>"르완다");
$Infos[] = array("code"=>"SA","code_name"=>"사우디아라비아");
$Infos[] = array("code"=>"SB","code_name"=>"솔로몬 제도");
$Infos[] = array("code"=>"SC","code_name"=>"세이셸");
$Infos[] = array("code"=>"SD","code_name"=>"수단");
$Infos[] = array("code"=>"SE","code_name"=>"스웨덴");
$Infos[] = array("code"=>"SG","code_name"=>"싱가포르");
$Infos[] = array("code"=>"SG1","code_name"=>"싱가폴OEM");
$Infos[] = array("code"=>"SH","code_name"=>"세인트 헬레나");
$Infos[] = array("code"=>"SI","code_name"=>"슬로베니아");
$Infos[] = array("code"=>"SJ","code_name"=>"스발바르 및 얀마웬 제도");
$Infos[] = array("code"=>"SK","code_name"=>"슬로바키아");
$Infos[] = array("code"=>"SL","code_name"=>"시에라리온");
$Infos[] = array("code"=>"SM","code_name"=>"산마리노");
$Infos[] = array("code"=>"SN","code_name"=>"세네갈");
$Infos[] = array("code"=>"SO","code_name"=>"소말리아");
$Infos[] = array("code"=>"SR","code_name"=>"수리남");
$Infos[] = array("code"=>"ST","code_name"=>"상투메 프린시페");
$Infos[] = array("code"=>"SV","code_name"=>"엘살바도르");
$Infos[] = array("code"=>"SV1","code_name"=>"엘살바도르OEM");
$Infos[] = array("code"=>"SX","code_name"=>"스칸디나비아");
$Infos[] = array("code"=>"SX1","code_name"=>"스칸디나비아OEM");
$Infos[] = array("code"=>"SY","code_name"=>"시리아");
$Infos[] = array("code"=>"SZ","code_name"=>"스와질란드");
$Infos[] = array("code"=>"TC","code_name"=>"터크스 케이커스 제도");
$Infos[] = array("code"=>"TD","code_name"=>"차드");
$Infos[] = array("code"=>"TF","code_name"=>"프랑스 남쪽 영역");
$Infos[] = array("code"=>"TG","code_name"=>"토고");
$Infos[] = array("code"=>"TH","code_name"=>"태국");
$Infos[] = array("code"=>"TH1","code_name"=>"태국OEM");
$Infos[] = array("code"=>"TJ","code_name"=>"타지키스탄");
$Infos[] = array("code"=>"TK","code_name"=>"토켈라우");
$Infos[] = array("code"=>"TM","code_name"=>"투르크메니스탄");
$Infos[] = array("code"=>"TN","code_name"=>"튀니지");
$Infos[] = array("code"=>"TO","code_name"=>"통가");
$Infos[] = array("code"=>"TP","code_name"=>"동티모르");
$Infos[] = array("code"=>"TR","code_name"=>"터키");
$Infos[] = array("code"=>"TR1","code_name"=>"터키OEM");
$Infos[] = array("code"=>"TT","code_name"=>"트리니다드 토바고");
$Infos[] = array("code"=>"TV","code_name"=>"투발루");
$Infos[] = array("code"=>"TW","code_name"=>"대만");
$Infos[] = array("code"=>"TW1","code_name"=>"대만OEM");
$Infos[] = array("code"=>"TZ","code_name"=>"탄자니아");
$Infos[] = array("code"=>"UA","code_name"=>"우크라이나");
$Infos[] = array("code"=>"UG","code_name"=>"우간다");
$Infos[] = array("code"=>"US","code_name"=>"미국");
$Infos[] = array("code"=>"US1","code_name"=>"미국OEM");
$Infos[] = array("code"=>"US2","code_name"=>"하와이");
$Infos[] = array("code"=>"UY","code_name"=>"우루과이");
$Infos[] = array("code"=>"UZ","code_name"=>"우즈베키스탄");
$Infos[] = array("code"=>"VA","code_name"=>"바티칸");
$Infos[] = array("code"=>"VC","code_name"=>"세인트 빈센트 그레나딘스");
$Infos[] = array("code"=>"VE","code_name"=>"베네수엘라");
$Infos[] = array("code"=>"VG","code_name"=>"영국령 버진 아일랜드");
$Infos[] = array("code"=>"VI","code_name"=>"미국령 버진 아일랜드");
$Infos[] = array("code"=>"VN","code_name"=>"베트남");
$Infos[] = array("code"=>"VN1","code_name"=>"베트남OEM");
$Infos[] = array("code"=>"VU","code_name"=>"바누아투");
$Infos[] = array("code"=>"WF","code_name"=>"월리스 푸투나");
$Infos[] = array("code"=>"WS","code_name"=>"사모아");
$Infos[] = array("code"=>"YE","code_name"=>"예멘");
$Infos[] = array("code"=>"YT","code_name"=>"마요트");
$Infos[] = array("code"=>"YU","code_name"=>"유고슬라비아");
$Infos[] = array("code"=>"ZA","code_name"=>"남아프리카 공화국");
$Infos[] = array("code"=>"ZM","code_name"=>"잠비아");
$Infos[] = array("code"=>"ZW","code_name"=>"짐바브웨");


$etc_div = "N";
$sql = "DELETE FROM sellertool_received_etc WHERE site_code = '".$site_code."' and etc_div='".$etc_div."'";
$db->query($sql);

foreach($Infos as $Info):
	
	$code = trim($Info['code']);
	$code_name = trim(str_replace("'","&#39;",$Info['code_name']));

	$sql = "INSERT INTO 
				sellertool_received_etc
			SET
				etc_div = '".$etc_div."',
				site_code = '".$site_code."',
				code_name = '".$code_name."',
				code = '".$code."',
				insert_date = NOW()
			";
	$db->query($sql);

endforeach;
exit;

