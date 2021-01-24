<?PHP
class Promotion extends AppModel{
    var $name='Promotion';
    
    function findPromotion($promotionID =null){
    	$sql ="SELECT * FROM inv_promotions AS Promotion ";
    	$sql.="WHERE Promotion.promotion_id='".$promotionID."' ";
    	$sql.="ORDER BY Promotion.limit DESC";

    	//echo $sql;
    	return $this->query($sql);
    }
	public function findPromotionByType($type_id){
    	$sql ="SELECT * FROM inv_promotions AS Promotion ";
    	$sql.="WHERE Promotion.type_id='".$type_id."' ";
    	$sql.="ORDER BY Promotion.limit DESC";
		return $this->query($sql);
	}
    function findProductByPromotionID($id){
    	$sql = "SELECT * FROM inv_products AS Product,inv_stocks AS Stock ";
    	$sql.="WHERE (Product.stock_id=Stock.stock_id) and Product.product_id='".$id."' ";
    	$sql.=";";
    	return $this->query($sql);
    }
    function findType($id){
    	$sql ="SELECT * FROM `inv_types` AS `Type` WHERE `Type`.`type_id`=".$id;
    	return $this->query($sql);
    }
	
	public function unitType($id){
		$sql ="SELECT * FROM inv_runits AS U,inv_units AS Unit ";
		$sql.="Where ";
		$sql.="(U.unit_id = Unit.unit_id) AND ";
		$sql.="U.type_id='".$id."'";
		return $this->query($sql);
	}
}
?>