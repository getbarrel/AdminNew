<?php
	/**
	 * User: james
	 * Date: 12. 2. 28
	 * Time: 오전 10:54
	 */

    include "LayoutXml.class";
    //로드
    $a = new LayoutXml("./layout.xml");

    //검색
    $results = $a->search("layouts", array("pcode", "templet_name", "mall_ix")
                                   , array("001001000000000" ,"stylestory", "d02b37324dd0b08f6bc0f3847673e7d5"));
    //검색내용 수정
    foreach($results as $result){
      $a->layouts[$result->layout_index]->depth = 100;
    }

    //저장
    $a->SaveXml("./layout.update.result.xml");

    // 검색내용삭제
    $results = $a->search("layouts", array("pcode", "templet_name", "mall_ix")
                                     , array("000000000000000" ,"stylestory", "d02b37324dd0b08f6bc0f3847673e7d5"));
    foreach($results as $result){
      unset($a->layouts[$result->layout_index]);
    }

    $a->SaveXml("./layout.delete.result.xml");