<?php 

require("browser.php");

css_site();

function css_site() {

//determine font for this platform
if (browser_is_windows() && browser_is_ie()) {

//ie needs smaller fonts than anyone else
$smallfont_size='8pt';
$lgfont_size='9pt';

} else if (browser_is_windows()) {

//netscape or "other" on wintel
$smallfont_size='8pt';
$lgfont_size='9pt';

} else if (browser_is_mac()){

//mac users need bigger fonts
$smallfont_size='10pt';
$lgfont_size='11pt';

} else {

//linux and other users
$smallfont_size='8pt';
$lgfont_size='9pt';

}

?>


	.textbox {
		color : Black;
		background-color : #F5FFFA;
		height : 19px;
		font-size : 12px;
	}
	.formfield {
		border-color : #666699 #666699 #666699 #666699;
		border-width : 1 1 1 1;
		color : Black; background-color : #99CCFF;
		height : 19px; font-size : 12px;
	}
	.button{
		color : Black;
		background-color : #F5FFFA;
		height : 19px;
		font-size : 12px;
	}

<!--
	BODY {font-family : Verdana, Arial, sans-serif; text-decoration : none; color : Black;  font-size : <?=$smallfont_size?>;}
	A { font-family : Verdana, Arial, sans-serif; text-decoration : none; color : Gray; }
	A:HOVER { font-family : Verdana, Arial, sans-serif; color : Blue; }

	.menu { font-family : Verdana, Arial, sans-serif; font-size : <?=$smallfont_size?>; color : Black; }
	.menu:link { font-family : Verdana, Arial, sans-serif; font-size : <?=$smallfont_size?>;color : #004080;  }
	.menu:hover { font-family : Verdana, Arial, sans-serif; font-size : <?=$smallfont_size?>;color : #004080;  }
	.menu:visited { font-family : Verdana, Arial, sans-serif; font-size : <?=$smallfont_size?>;color : #004080;  }
	.navigation { font-family : Verdana, Arial, sans-serif; font-size : <?=$smallfont_size?>;color : #004080; }

	.navigation:link { text-decoration: none; font-family : Verdana, Arial, sans-serif; font-size : <?=$smallfont_size?>;color : #004080;  }
	.navigation:hover { text-decoration: none; font-family : Verdana, Arial, sans-serif; font-size : <?=$smallfont_size?>;color : #004080;  }
	.navigation:visited { text-decoration: none; font-family : Verdana, Arial, sans-serif; font-size : <?=$smallfont_size?>;color : #004080; }

	.autolink:link { text-decoration: underline; font-family : Courier New, Verdana, Arial, sans-serif; font-size : <?=$lgfont_size?>; color : #004080; }
	.autolink:hover { text-decoration: underline; font-family : Courier New, Verdana, Arial, sans-serif; font-size : <?=$lgfont_size?>; color : #004080; }
	.autolink:visited { text-decoration: underline; font-family : Courier New, Verdana, Arial, sans-serif; font-size : <?=$lgfont_size?>; color : #004080; }

	TD.rempli { font-family : Verdana, Arial, sans-serif; background-color : #66CC99;}
	TD.memo { font-family : Verdana, Arial, sans-serif; background-color: #E8E8E8;}
	TD.cent { font-family : Verdana, Arial, sans-serif; background-color: #E8E8E8; text-align: center; font-size : <?=$smallfont_size?>; color: Black;}
	TD.right { font-family : Verdana, Arial, sans-serif; background-color: #E8E8E8; text-align: right; font-size : <?=$smallfont_size?>; color: Black;}
	TD.headers { font-family : Verdana, Arial, sans-serif; background-color : #CFCFCF; text-align : center; font-size : <?=$smallfont_size?>; color: Black;}
	TD.default { font-family : Verdana, Arial, sans-serif; background-color : #E8E8E8; font-size : <?=$smallfont_size?>;color : Black; }

	TD.default2 { font-family : Verdana, Arial, sans-serif; background-color : #ffffff; font-size : <?=$smallfont_size?>;color : Black; }

	TD.default9 { font-family : Verdana, Arial, sans-serif; background-color : #E8E8E8; font-size : <?=$lgfont_size?>;color : Black; }
	TD.title { font-family : Verdana, Arial, sans-serif; color: #191970; background-color : #B0C4DE; text-align : center; font-size : <?=$smallfont_size?>;}
	TD.headerright { font-family : Verdana, Arial, sans-serif; color: #004080; background-color : #CFCFCF; text-align : right;  font-size : <?=$smallfont_size?>; }
	SELECT 	{ font-size : <?=$smallfont_size?>; }
-->



<?php
}

?>

