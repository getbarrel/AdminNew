<?php
/**
 * @date 2017.01.24
*/

if($_SERVER['HTTP_HOST'] == 'isnt-she.com' || $_SERVER['HTTP_HOST'] == 'www.isnt-she.com'){
	define('DEMANDSHIP_URL',"https://demandship.com");

	define('CLIENT_ID', "4");
	define('CLIENT_SECRET', "MVKVPyfovziWxb7xkm5DkUjAN2LS4hS5PuUQeKwM");
	define('CALLBACK_URL', "http:/isnt-she.com/admin/openapi/demandship/demandship_callback.php");
}else{
    define('DEMANDSHIP_URL',"http://52.52.147.111"); // TEST

    define('CLIENT_ID', "5");
    define('CLIENT_SECRET', "LfJjnfpQ76fcKNNIvDsF25xxdWxBk2m4TUwnMJiP");
    define('CALLBACK_URL', "http://charmee.forbiz.co.kr/admin/openapi/demandship/demandship_callback.php");
}


$store = array(
		"11st" => "11st Korea",
		"11st_my" => "11st Malaysia",
		"storefarm" => "Naver Storefarm",
		"gmarket" => "Gmarket",
		"auction" => "Auction"
	);

define('POST', 1);
define('GET', 0);

//define('PATHNER_ACCESS_TOKEN',"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjUzM2E3NWUyMTY1ZTNjYjk2NWZkNmQ3NTc5OWQ2NDRlM2M2ZjQxYTU2MTViYTM5MWU1MDg3YWViODlmOWMxNmY3NDVkMGM2MGUyZjZjODI0In0.eyJhdWQiOiIyIiwianRpIjoiNTMzYTc1ZTIxNjVlM2NiOTY1ZmQ2ZDc1Nzk5ZDY0NGUzYzZmNDFhNTYxNWJhMzkxZTUwODdhZWI4OWY5YzE2Zjc0NWQwYzYwZTJmNmM4MjQiLCJpYXQiOjE0ODQ2MTMwNDAsIm5iZiI6MTQ4NDYxMzA0MCwiZXhwIjoxODAwMTQ1ODQwLCJzdWIiOiIyNCIsInNjb3BlcyI6W119.jDxEPsL4j4pAerWv2-PG6b-ev991lVHu2dT3SuQG1rbSr1QwuqK67tr2vDXt42g-ODnSFU_uyklZQPfmnyvAaPzkxrObtaivwXCy5a_ZAbMwLyemYboqxsF-n1kA-1AEsAy0eq1ED4Zh2kf3L4V34vgRBYwbsLq7nL30GSrTMxwWTtCeDtJl4nOqZQkrNqVTfeN2N6GVmQOxPeoVvTFuLGG9Bs9Aa8KFzazgzdnuVh8vQvh172v0cn6bqakzxJTSmxjAop0Xu85uRA5HIFcRsJdeNon4XXOhGbMgvHmEDkiMb7wIRddU77mQ9WeTNEm1mslGrxwB6eaJvhcbY8DyKOWixQ1jd1bWbBWhs4mzvF_HZGSrpHIBEXsOKjCG6m8iaGZOG760Juw7AMjKiWBbvflutEvWoaWZi9SBwXB_rXr4T1CAputyvafFub3oxgj2w6di2FGDHElHbCWWxzY3v4ZKJtUudB7Grotd1fJT5hiNocUACnOCs3ExkHPVFbiotiFzoPiwcNUiUoNckKggZRi9X2hqGYs1uzVa7X2gcQ435FbgIyPdOpgk-P4yF7aPF6uW9tf-P8PkwPhIlgzVreyWQRK7QeKCsklrzFe2nIOHXiESoRayMPlpoCC_sUEQUqacLKHBmddEsUMaqD6j8ZQ7h_AOS4DtG5hv24GN_6M"); // TEST Token

//define('PATHNER_ACCESS_TOKEN',"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImRkMjA1ZGI4YWMzZTQwYjc0ODk0ODQ5N2MwZjg3Y2U1NWNmN2IyMTM5YWMxYTU0MDM0OGQ4MzRlMjFiMjgzYTBkZjgwODdhZDkzZWNkNzMzIn0.eyJhdWQiOiI0IiwianRpIjoiZGQyMDVkYjhhYzNlNDBiNzQ4OTQ4NDk3YzBmODdjZTU1Y2Y3YjIxMzlhYzFhNTQwMzQ4ZDgzNGUyMWIyODNhMGRmODA4N2FkOTNlY2Q3MzMiLCJpYXQiOjE0ODU4NDM3NjcsIm5iZiI6MTQ4NTg0Mzc2NywiZXhwIjoyMTE2OTk1NzY3LCJzdWIiOiIyOSIsInNjb3BlcyI6W119.SH9oTe-WdiLwYMvQ-abCn6JCMAEAlXWlJtMrY2KkwEEG0N2AD9Su_-RF6Rj43-kEciMlGcA1gm3bD7jz3GQOWdIHGw8xNF0QS3EhArVhWkEBOCq_yXvd9sKglUyjigmYoBBWJn99KHHtyZcVzeWRAl37Lt0RRtGvuvt54W5ikhkv6KVYG0x6P35ZjsjMTq2nlTacGb8BcJ--j7NfB3iVgBV1lTePXQCUsdfK9equ399PY2j_VZBERg4Q1-mgHlhhLYyEJGdTZz_mBxAaROveAdJYXffgo6eAvtfK9JUcyR_rV6CtPLz2JfepvXI7w_Ep6v4y1nvKA46fQjreBPGalswew5nX48Nky4N--IUKnomQlZZtGXtbGUyx2Zh_PWEpA5UPCvQ3uzLAEf1szFFS5gq0BvyhjPgNXZCU7GSl7hRhPZAQxTFNZgSA2B0RPK8YQwhAD2g2zjXXAC9nnBv-0qWNrYyQIawawcaq1YeRHBggXwEKgq6HsBk6RWUjF7X7q0nOcSsQOKL1SHAg-jP-k2OyoXqbxk1H0SPUWXo6jnCmNoU1BS-rZsRXz590zFDKaW84aC3n-R9d3kQscH8KuzXP89CThYrkPaBhtVoTkibbi3ANRS3w0Mt1XBwPs3K2aLk_AvoXL5caF1AxGDZKQM1m38OorwvIcU5Q7M_h118");