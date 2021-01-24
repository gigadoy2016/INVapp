<?PHP
class Stock extends AppModel{
    var $name='Stock';
    
    function findStock($id){
        $sql = "SELECT * FROM `inv_stocks` AS `Stock` WHERE `Stock`.`stock_id`='".$id."';";
        return $this->query($sql);
    }
    
    function findProduct($id,$order){
    	$sql = "SELECT * FROM `inv_products` AS `Product`WHERE `Product`.`stock_id`='".$id."'";
    	if(!empty($order)){
    		$sql.=" ".$order;
    	}
        return $this->query($sql);
    }
}
?>