<?php

class SectionDto {
	public $ID;
	public $name;
	public $colour;
	public $members;

	function __construct($id, $name, $colour, $members = array()){
		$this->ID = $id;
		$this->name = $name;
		$this->colour = $colour;
		$this->members = $members;
	}

	static function createFromDataset($row){
		return new SectionDto($row['ID'], $row['Name'], $row['Colour']);
	}
}