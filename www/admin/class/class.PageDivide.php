<?php
/*****************************************************
 페이지 클래스(Page Class)
 ---------------------------------------------------
 memo 
*****************************************************/


if(defined("__ClassPages__")) return;
define("__ClassPages__", true);


Class PageDivide
{
	var $PAGE   = Array();
	var $CONFIG = Array();

	function PageDivide($argTPage,$argPNum,$argCPage,$argGubunBar="")
	{
		$this->CONFIG["TPage"]		= (!$argTPage)		? 1   : $argTPage;
		$this->CONFIG["PNum"]		= (!$argPNum)		? 10  : $argPNum;
		$this->CONFIG["CPage"]		= (!$argCPage)		? 1   : $argCPage;
		$this->CONFIG["GubunBar"]	= (!$argGubunBar)	? " " : $argGubunBar;
		$this->CONFIG["CPageStyleClass"]	= "";
		$this->CONFIG["LPageStyleClass"]	= "";
		$this->CONFIG["CPageFontColor"]		= "#FF6500";
		$this->CONFIG["LPageFontColor"]		= "#000000";
	}


	function Page_Divide($argDoc="",$argExt="")
	{
		// 정보 셋
		$this->CONFIG["sTotalNum"]  = ceil($this->CONFIG["TPage"]/$this->CONFIG["PNum"]);
		$this->CONFIG["sPageNum"]   = ceil($this->CONFIG["CPage"]/$this->CONFIG["PNum"]);
		$this->CONFIG["sFirstPage"] = (($this->CONFIG["sPageNum"]-1)*$this->CONFIG["PNum"]);
		$this->CONFIG["sLastPage"]  = ($this->CONFIG["sPageNum"]*$this->CONFIG["PNum"]);

		// 마지막 페이지 셋
		if($this->CONFIG["sPageNum"] >= $this->CONFIG["sTotalNum"]) {
			$this->CONFIG["sLastPage"] = $this->CONFIG["TPage"];
		}

		// 이전 페이지
		if($this->CONFIG["sPageNum"] > 1) {
			$sFirstPage = $this->CONFIG["sFirstPage"];
			$this->PAGE["PagePrev"] = "<a href=\"{$argDoc}?CPage={$sFirstPage}{$argExt}\">";
		}

		// 페이지 리스트 출력
		for($i=$this->CONFIG["sFirstPage"]+1;$i<=$this->CONFIG["sLastPage"];$i++) {
			$sCPageFontColor   = "color:{$this->CONFIG[CPageFontColor]}";
			$sLPageFontColor   = "color:{$this->CONFIG[LPageFontColor]}";
			$sCPageStyleClass  = ($this->CONFIG[CPageStyleClass]) ? "class=\"{$this->CONFIG[CPageStyleClass]}\"" : "";
			$sLPageStyleClass  = ($this->CONFIG[LPageStyleClass]) ? "class=\"{$this->CONFIG[LPageStyleClass]}\"" : "";

			$__TmpPageList[$i] = ($this->CONFIG["CPage"] == $i) 
									? "<span style=\"{$sCPageFontColor}\" {$sCPageStyleClass}><b>{$i}</b></span>"
									: "<a href=\"{$argDoc}?CPage={$i}{$argExt}\"><span style=\"{$sLPageFontColor}\" {$sLPageStyleClass}>{$i}</span></a>";
		}
		$this->PAGE["PageList"] = (count($__TmpPageList) > 0) ? implode($this->CONFIG["GubunBar"],$__TmpPageList) : "1";

		// 다음 페이지
		if($this->CONFIG["sPageNum"] < $this->CONFIG["sTotalNum"]) {
			$sLastPage = $this->CONFIG["sLastPage"]+1;
			$this->PAGE["PageNext"] = "<a href=\"{$argDoc}?CPage={$sLastPage}{$argExt}\">";
		}

		// 이전 리스트
		if(($this->CONFIG["CPage"] > 1) && ($this->CONFIG["CPage"] <= $this->CONFIG["sLastPage"])) {
			$this->PAGE["ListPrev"] = "<a href=\"{$argDoc}?CPage=".($this->CONFIG["CPage"]-1)."{$argExt}\">";
		}

		// 다음리스트
		if(($this->CONFIG["CPage"] < $this->CONFIG["TPage"]) && ($this->CONFIG["CPage"] > $this->CONFIG["sFirstPage"])) {
			$this->PAGE["ListNext"] = "<a href=\"{$argDoc}?CPage=".($this->CONFIG["CPage"]+1)."{$argExt}\">";
		}

		return $this->PAGE;
	}
}

?>