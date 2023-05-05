<?php

require_once('BaseAccessor.php');
require_once("DTO/AssetDto.php");

class Asset extends BaseAccessor {
	static function getAll($con = null){
		$openedConnection = self::ensureConnected($con);

		$assets = array();
		$data = DB::executeQuery("SELECT a.*, s.Name AS SectionName, s.Colour AS SectionColour FROM Assets a LEFT OUTER JOIN Sections s ON s.ID = a.SectionID", $con);

		foreach($data as $row){
			$asset = AssetDto::createFromDataset($row);
			$assets[] = $asset;
		}

		if($openedConnection){
			DB::close($con);
		}

		return $assets;
	}
}