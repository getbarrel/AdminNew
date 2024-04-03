<?php 

require("browser.php");

css_site();

function css_site() {

//determine font for this platform
if (browser_is_windows() && browser_is_ie()) {

//ie needs smaller fonts than anyone else
$smallfont_size='8pt';
$mediumfont_size='9pt';
$largefont_size='10pt';

} else if (browser_is_windows()) {

//netscape or "other" on wintel
$smallfont_size='8pt';
$mediumfont_size='9pt';
$largefont_size='10pt';

} else if (browser_is_mac()){

//mac users need bigger fonts
$smallfont_size='9pt';
$mediumfont_size='10pt';
$largefont_size='11pt';

} else {

//linux and other users
$smallfont_size='8pt';
$mediumfont_size='9pt';
$largefont_size='10pt';

}

$site_fonts='verdana, arial, helvetica, sans-serif';

?>


.plink {  font-family: Verdana, Arial, Helvetica, sans-serif; color: #009966; text-decoration: none}
a:hover.plink {  color: #FF0000; text-decoration: underline}
.ptext {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: <?=$smallfont_size?>;  line-height: 13pt; font-weight: normal; text-decoration: none}
.menutext {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: <?=$smallfont_size?>; color: #999999; line-height: 13pt; font-weight: normal; text-decoration: none}
.title {  font-family: Arial, Helvetica, sans-serif; font-size: <?=$largefont_size?>; line-height: 17pt; font-weight: bold; text-decoration: none}
.ptitle {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: <?=$mediumfont_size?>; color: #666666; line-height: 17pt; font-weight: normal; text-decoration: none}
.table {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: <?=$smallfont_size?>}
.table2 {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: <?=$smallfont_size?>} 


<?php
}

?>
