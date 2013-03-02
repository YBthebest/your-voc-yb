<?php
class ConfigPage{
	private $pageName;
	private $title;
	private $metaContent;

	function pageName(){
		return $this->pageName;
	}
	function setPageName($p_pageName){
		$this->pageName = $p_pageName;
	}

	function title(){
		return $this->title;
	}
	function setTitle($p_title){
		$this->title = $p_title;
	}

	function metaContent(){
		return $this->metaContent;
	}
	function setMetaContent($p_metaContent){
		$this->metaContent = $p_metaContent;
	}
}
?>