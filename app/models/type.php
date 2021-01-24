<?php
class Type extends AppModel{
    var $name='Type';

    function findTypeAll($id=null){
        $sql = "SELECT * FROM inv_types as Type  LEFT JOIN `inv_categories` AS `Category` ON (`Category`.`category_id` = `Type`.`category_id`) ";
		if($id !=null){
			$sql .="WHERE `Type`.`category_id` = '".$id."'";			
		}
		$sql .="ORDER BY Type.serial_id ASC;";
        return $this->query($sql);
    }
    function findTypeByCategory($id){
        $sql = "SELECT * FROM `inv_types` AS `Type` LEFT JOIN `inv_categories` AS `Category` ON (`Category`.`category_id` = `Type`.`category_id`) WHERE `Category`.`name`='".$id."' ORDER BY Type.order,Type.serial_id ASC;";
        return $this->query($sql);
    }
    function findTypeByCategoryid($id){
        $sql = "SELECT * FROM `inv_types` AS `Type` LEFT JOIN `inv_categories` AS `Category` ON (`Category`.`category_id` = `Type`.`category_id`) WHERE `Category`.`category_id`='".$id."' ORDER BY Type.order,Type.serial_id ASC;";
        return $this->query($sql);
    }	
    function findType($id){
    	$sql ="SELECT * FROM `inv_types` AS `Type`,`inv_categories` AS `Category` WHERE (`Category`.`category_id` = `Type`.`category_id`) AND `Type`.`type_id`=".$id;
    	return $this->query($sql);
    }
	function findCategory(){
		$sql="SELECT * FROM `inv_categories` AS `Category`;";
		return $this->query($sql);
	}
	function update($data){
		//print_r($data);
		$sql="UPDATE inv_types ";
		$sql.=" SET ";
		$sql.=" serial_id='".$data['serial_id']."',";
		$sql.=" name='".$data['name']."',";
		$sql.=" detail='".$data['detail']."',";
		$sql.=" eng_name='".$data['eng_name']."',";
		$sql.=" pic='".$data['pic']."',";
		$sql.=" sale_price='".$data['sale_price']."',";
		$sql.=" cost='".$data['cost']."',";
		$sql.=" inv_types.order='".$data['order']."' ";
		$sql.=" WHERE type_id='".$data['type_id']."';";
		echo $sql;
		$this->query($sql);
		return true;
	}
	public function del($id){
		$sql="DELETE FROM inv_types WHERE type_id=".$id;
		$this->query($sql);
	}
	public function tQuery($sql){
		return $this->query($sql);
	}
	public function promotion($id){
		$sql ="SELECT * FROM ";
		$sql.="inv_promotions as Promotion  INNER JOIN inv_types as Type ";
		$sql.="ON Promotion.type_id = Type.type_id ";
		$sql.="Where ";
		$sql.="Promotion.type_id = ".$id;
		$sql.=" ORDER BY Promotion.type_id,Promotion.limit ASC;";
		
		return $this->query($sql);
	}
	public function promotionType(){
		$sql ="SELECT DISTINCT Type.type_id,Type.name,Type.pic   FROM ";
		$sql.="inv_promotions as Promotion  INNER JOIN inv_types as Type ";
		$sql.="ON Promotion.type_id = Type.type_id ";
		$sql.="Where ";
		$sql.="Promotion.type_id > 0 ";
		$sql.="ORDER BY Promotion.type_id,Promotion.limit ASC;";
		
		return $this->query($sql);
	}
	public function pro($id){
		$sql = 'SELECT * FROM inv_promotions AS Promotion ';
		$sql.= 'INNER JOIN inv_types as Type ';
		$sql.= 'ON Promotion.type_id = Type.type_id ';
		$sql.= 'WHERE Promotion.id='.$id;
		return $this->query($sql);
	}
	public function updatePromotion($data){
		$sql ="UPDATE inv_promotions AS Pro SET ";
		$sql.="Pro.promotion_id='".$data['promotion_id']."',";
		$sql.="Pro.name='".$data['name']."',";
		$sql.="Pro.price='".$data['price']."',";
		$sql.="Pro.limit='".$data['limit']."',";
		$sql.="Pro.detail='".$data['detail']."' ";
		$sql.="Where Pro.id='".$data['id']."';";
		return $this->query($sql);
	}
	public function addPromotion($data){
		$sql ="INSERT INTO `inv`.`inv_promotions` ";
		$sql.="(`id`, `promotion_id`, `type_id`, `name`, `class_id`, `price`, `limit`, `detail`) ";
		$sql.="VALUES ";
		$sql.="(NULL, ";
		$sql.="'".$data['promotion_id']."', ";
		$sql.="'".$data['type_id']."', ";
		$sql.="'".$data['name']."', ";
		$sql.="'0', ";
		$sql.="'".$data['price']."', ";
		$sql.="'".$data['limit']."', ";
		$sql.="'".$data['detail']."');";
		return $this->query($sql);
	}
	public function delPromotion($id){
		$sql="DELETE FROM inv_promotions WHERE ";
		$sql.="id = ".$id.";";
		return $this->query($sql);
	}
}
?>