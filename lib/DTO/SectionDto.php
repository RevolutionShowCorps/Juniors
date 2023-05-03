<?php

class SectionDto {
	public $ID;
	public $name;
	public $members;

	function __construct($id, $name, $members = array()){
		$this->ID = $id;
		$this->name = $name;
		$this->members = $members;
	}

	static function createFromDataset($row){
		return new SectionDto($row['ID'], $row['Name']);
	}
}