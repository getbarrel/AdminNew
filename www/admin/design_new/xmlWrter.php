<?php

// Simon Willison, 16th April 2003
// Based on Lars Marius Garshol's Python XMLWriter class
// See http://www.xml.com/pub/a/2003/04/09/py-xml.html

class XmlWriter {
    var $xml;
    var $indent;
    var $stack = array();
    function XmlWriter($indent = '  ') {
        $this->indent = $indent;
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
    }
    function _indent() {
        for ($i = 0, $j = count($this->stack); $i < $j; $i++) {
            $this->xml .= $this->indent;
        }
    }
    function push($element, $attributes = array()) {
        $this->_indent();
        $this->xml .= '<'.$element;
        foreach ($attributes as $key => $value) {
            $this->xml .= ' '.$key.'="'.htmlentities($value).'"';
        }
        $this->xml .= ">\n";
        $this->stack[] = $element;
    }
    function element($element, $content, $attributes = array()) {
        $this->_indent();
        $this->xml .= '<'.$element;
        foreach ($attributes as $key => $value) {
            $this->xml .= ' '.$key.'="'.htmlentities($value).'"';
        }
        $this->xml .= '>'.htmlentities($content).'</'.$element.'>'."\n";
    }
    function emptyelement($element, $attributes = array()) {
        $this->_indent();
        $this->xml .= '<'.$element;
        foreach ($attributes as $key => $value) {
            $this->xml .= ' '.$key.'="'.htmlentities($value).'"';
        }
        $this->xml .= " />\n";
    }
    function pop() {
        $element = array_pop($this->stack);
        $this->_indent();
        $this->xml .= "</$element>\n";
    }
    function getXml() {
        return $this->xml;
    }
}

/* Test

$xml = new XmlWriter();
$array = array(
    array('monkey', 'banana', 'Jim'),
    array('hamster', 'apples', 'Kola'),
    array('turtle', 'beans', 'Berty'),
);

$xml->push('zoo');
foreach ($array as $animal) {
    $xml->push('animal', array('species' => $animal[0]));
    $xml->element('name', $animal[2]);
    $xml->element('food', $animal[1]);
    $xml->pop();
}
$xml->pop();

print $xml->getXml();

*/
?>
<?php header('Content-Type: text/xml'); ?>
<?
phpinclude('XmlWriter.php');
$xml = new XmlWriter();
$array = array(    
array('monkey', 'banana', 'Jim'),    
array('hamster', 'apples', 'Kola'),    
array('turtle', 'beans', 'Berty'),
);

$xml->push('zoo');

foreach ($array as $animal) {    
	$xml->push('animal', array('species' => $animal[0]));    
	$xml->element('name', $animal[2]);    
	$xml->element('food', $animal[1]);    
	$xml->pop();
}
	
$xml->pop();
print $xml->getXml();


//XmlDoc의 내용을 가지고 xml파일 생성하기 경로하고 이름 정해서 파일 생성 할수 있다
$dirname = "./etc/data/";
$fileName = "200607.xml"; 
$fp = fopen("$dirname$fileName","w");
fputs($fp, $xmlDoc);
fclose($fp);





?>
<?xml version="1.0" encoding="utf-8"?>
<zoo>
  <animal species="monkey">
    <name>Jim</name>
    <food>banana</food>
  </animal>
  <animal species="hamster">
    <name>Kola</name>
    <food>apples</food>
  </animal>
  <animal species="turtle">
    <name>Berty</name>
    <food>beans</food>
  </animal>
</zoo>
