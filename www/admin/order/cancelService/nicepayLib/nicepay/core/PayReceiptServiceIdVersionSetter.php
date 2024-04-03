<?php

require_once dirname(__FILE__).'/MessageIdVersionSetter.php';

/**
 * 
 * @author kblee
 *
 */
class PayReceiptServiceIdVersionSetter implements MessageIdVersionSetter{
	
	/**
	 * 
	 */
	public function PayReceiptServiceIdVersionSetter(){
		
	}
	
	/**
	 * 
	 * @param  $webMessageDTO
	 */
	public function fillIdAndVersion($webMessageDTO) {
		$webMessageDTO->setParameter(VERSION, "NPG01");
		$webMessageDTO->setParameter(ID, "FCH01");
	}
	
}
?>
