<?php

require_once("SectionDto.php");

class AssetDto{
	public $ID;
	public $name;
	public $description;
	public $section;
	public $value;
	public $hireCost;
	public $currentHire = null;

	function __construct($id, $name, $description, $section, $value, $cost){
		$this->ID = $id;
		$this->name = $name;
		$this->description = $description;
		$this->section = $section;
		$this->value = $value;
		$this->hireCost = $cost;
	}

	static function createFromDataset($row){
		$section = new SectionDto($row['SectionID'], $row['SectionName'], $row['SectionColour']);
		return new AssetDto($row['ID'], $row['Name'], $row['Description'], $section, $row['EstimatedValue'], $row['HireCost']);
	}
}