<?php 
class BGGHelper {

	public static function bggGetCredData(){
		$getCredData = BGGDb::getTableData('bgg_credentials');
		return $getCredData['0'];
	} 
}
new BGGHelper();