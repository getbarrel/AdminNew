<?php
require_once dirname(__FILE__).'/../core/Constants.php';
require_once dirname(__FILE__).'/../../log4php/Logger.php';
/**
 * 
 * @author kblee
 *
 */
class NicePayLogJournal{
	
	/**
	 * 
	 * @var $instance
	 */
	private static $instance;
	
	/** The event log path. */
	private $logPath;
	
	
	/** The event logger. */
	private $eventLogger;

	private $appLogger;
	
	/**
	 * Create a MnBankLogJournal instance.
	 */
	private function NicePayLogJournal(){
		
	}
	
	/**
	 * get a Single MnBankLogJournal instance.
	 */
	public static function getInstance(){
		if(!isset(NicePayLogJournal::$instance)){
			NicePayLogJournal::$instance = new NicePayLogJournal();
		}
		return NicePayLogJournal::$instance;
	}
	
	/**
	 * 
	 * @param  $eventLogPath
	 */
	public function setLogDirectoryPath($logPath){
		$this->logPath = $logPath;
	}
	
	/**
	 * 
	 */
	public function configureNicePayLog4PHP(){
		if(!isset($this->appLogger) || !isset($this->eventLogger)){
			try {
				$currentPath = dirname(__FILE__);
				$currentPathXml = $currentPath.'/nicepay_log4php.xml';
					
				$doc = new DOMDocument();
				
				if($doc->load($currentPathXml)){
					$xpath = new DOMXPath($doc);
					$nodeList = $xpath->query("/log4php:configuration/appender[@name='eventJournal']/param[@name='File']");
					$fileParamNode = $nodeList->item(0);

					$fileParamNode->setAttribute("value",$this->logPath."/event_%s.log");
					
					$appAppendNodeList = $xpath->query("/log4php:configuration/appender[@name='NICEPAY_FILE']/param[@name='File']");
					$appFileParamNode = $appAppendNodeList->item(0);
					$appFileParamNode->setAttribute("value",$this->logPath."/application_%s.log");
					
					
					$doc->save($currentPathXml);


					Logger::configure($currentPathXml);
				
				$this->eventLogger = Logger::getLogger("EventJournal");

				$this->appLogger = Logger::getLogger("AppJournal");
					
				}else{
					echo "Event Logger Configuration Load Fail..";
				}
				
			} catch (Exception $e) {
				echo "Exception  : Event Logger Configuration Loading";
			}
			
		}
		
	}
	
	/**
	 * Write journal.
	 * 
	 * @param dto the dto
	 */
	public function writeEventLog($dto){
		
		$serviceMode  = $dto->getParameter(SERVICE_MODE);
		$logString = "";
		
		//StringBuffer logBuffer = new StringBuffer(); 
		$resultCode = $dto->getParameter("ResultCode");
		$reqDate = date("Ymd");
		$reqTime = date("His");
		
		if(PAY_SERVICE_CODE == $serviceMode){ //��������
			/**
			 *  ����
	         *  P|������������|������û��|������û�ð�|��������|������ǰ��|�����ݾ�|USER_ID|�����ڵ�|����޽���
			 */
			$logString.="P|";
			if( ("3001" == $resultCode) || ("4000" == $resultCode) || ("4001" == $resultCode) || ("A000" == $resultCode)){ //��������
				$logString.="TE|";
			}else{ //��������
				$logString.="TF|";
			}
			$logString.=$reqDate."|";
			$logString.=$reqTime."|";
			$logString.= ($dto->getParameter(PAY_METHOD)==null?"":trim($dto->getParameter(PAY_METHOD)))."|";
			$logString.= ($dto->getParameter(GOODS_NAME)==null?"":trim($dto->getParameter(GOODS_NAME)))."|";
			$logString.= ($dto->getParameter(GOODS_AMT)==null?"0":trim($dto->getParameter(GOODS_AMT)))."|";
			$logString.= ($dto->getParameter(MALL_USER_ID)==null?"":trim($dto->getParameter(MALL_USER_ID)))."|";
			$logString.=trim($resultCode)."|";
			$logString.=($dto->getParameter("ResultMsg")==null?"":trim($dto->getParameter("ResultMsg")));
		}else if(CANCEL_SERVICE_CODE == $serviceMode){                                                          // �������
			/**
			 *  ���
	         *  C|��Ҽ�������|��ҿ�û��|��ҿ�û�ð�|��������|��ұݾ�|USER_ID|�����ڵ�|����޽���
			 */
			$logString.="C|";
			if(("2001" == $resultCode) || ("2005" == $resultCode)){ //��Ҽ���
				$logString.="TE|";
			}else{ //��������
				$logString.="TF|";
			}
			$logString.=$reqDate."|";
			$logString.=$reqTime."|";
			
			$logString.= ($dto->getParameter(PAY_METHOD)==null?"":trim($dto->getParameter(PAY_METHOD)))."|";
			$logString.= ($dto->getParameter(GOODS_NAME)==null?"":trim($dto->getParameter(GOODS_NAME)))."|";
			$logString.= ($dto->getParameter(GOODS_AMT)==null?"0":trim($dto->getParameter(GOODS_AMT)))."|";
			$logString.= ($dto->getParameter(MALL_USER_ID)==null?"":trim($dto->getParameter(MALL_USER_ID)))."|";
			$logString.=trim($resultCode)."|";
			$logString.=($dto->getParameter("ResultMsg")==null?"":trim($dto->getParameter("ResultMsg")));
		}
		
	        if(isset($logString) && strlen($logString) > 0){	
			$this->eventLogger->debug($logString);
		}
	}

	public function writeAppLog($string){
		$this->appLogger->debug($string);
	}

	public function errorAppLog($string){
		$this->appLogger->error($string);
	}

	public function warnAppLog($string){
		$this->appLogger->warn($string);
	}
	
	
}
?>