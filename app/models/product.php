<?php
class Product extends AppModel{
    var $name='Product';
    var $orderBy = null;

    function findProductAll(){
        $sql = "SELECT * FROM `inv_products` AS `Product` LEFT JOIN `inv_categories` AS `Category` ON (`Category`.`category_id` = `Product`.`category_id`) WHERE 1 = 1";
        return $this->query($sql);
    }
    
    function findProductByType($id,$orderBy=null){
        $sql = "SELECT Category.name";
		$sql.=",Product.category_id";
		$sql.=",Product.type_id";
		$sql.=",Product.product_id";
		$sql.=",Product.product";
		$sql.=",Product.code";
		$sql.=",Product.quantity";
		$sql.=",sum(Product.quantity) as q_number";
		$sql.=",MAX(Product.time) as utime ";
		$sql.=" FROM inv_categories as Category INNER JOIN inv_products as Product";
		$sql.=" ON Category.category_id=Product.category_id ";
        $sql.=" WHERE Product.type_id=".$id;
        $sql.=" GROUP BY Product.product_id ";
        if(empty($orderBy)){
        	$sql.=" ORDER BY q_number ASC";
        }else{
        	$sql.=" ORDER BY ".$orderBy;
        }
        
        return $this->query($sql);
    }
    
    function findProductByCategory($id){
        $sql = "SELECT `Product`.`product_id`";
        $sql.=", `Product`.`product`";
        $sql.=", `Product`.`product_eng`";
        $sql.=", `Product`.`type_id`";
        $sql.=", `Product`.`category_id`";
        $sql.=", `Product`.`stock_id`";
        $sql.=", `Product`.`invoice_id`";
        $sql.=", `Product`.`owner_id`";
        $sql.=", `Product`.`quantity`";
        $sql.=", `Product`.`unit`";
        $sql.=", `Product`.`sale_price`";
        $sql.=", `Product`.`margin_price`";
        $sql.=", `Product`.`detail`";
        $sql.=", `Product`.`unit_price`";
        $sql.=", `Category`.`category_id`";
        $sql.=", `Category`.`name`";
        $sql.=",`Category`.`serial_id`";
        $sql.=", `Category`.`detail` ";
        $sql.="FROM `inv_products` AS `Product` LEFT JOIN `inv_categories` AS `Category` ON (`Category`.`category_id` = `Product`.`category_id`) ";
        $sql.="WHERE `Category`.`name`='".$id."';";
        return $this->query($sql);
    }
    
    function findProductByProduct($id){
        //$sql = "SELECT * FROM `inv_products` AS `Product` LEFT JOIN `inv_categories` AS `Category` ON (`Category`.`category_id` = `Product`.`category_id`) WHERE `Product`.`product_id`='".$id."';";
        /*$sql ="SELECT * FROM inv_products AS Product, inv_categories AS Category,inv_stocks AS Stock, inv_types AS Type ";
        $sql.="WHERE (Product.category_id = Category.category_id) ";
        $sql.="&& (Product.stock_id=Stock.stock_id)  ";
        $sql.="&& (Product.type_id=Type.type_id)  ";
        $sql.="&& `Product`.`product_id`='".$id."';";
		*/
		$sql =" SELECT *,";
		$sql.=" (CASE WHEN L.min > Product.quantity THEN 0 WHEN (L.min <= Product.quantity AND Product.quantity <= L.max ) THEN 1 WHEN Product.quantity >= L.max THEN 2 ELSE -1 END)as alert";
		$sql.=" FROM inv_products AS Product";
		$sql.=" INNER JOIN inv_categories AS Category ON Category.category_id=Product.category_id";
		$sql.=" INNER JOIN inv_types AS Type ON Type.type_id=Product.type_id";
		$sql.=" INNER JOIN inv_stocks AS Stock ON Stock.stock_id=Product.stock_id";
		$sql.=" LEFT JOIN inv_limitcheck AS L ON Product.product_id=L.product_id";
		$sql.=" WHERE Product.product_id='".$id."';";
        return $this->query($sql);
    }
    
    function findProductByID($id){
        $sql ="SELECT * FROM inv_products AS Product,inv_types AS Type ";
        $sql.="WHERE (Product.type_id=Type.type_id) && ";
        $sql.="`Product`.`id`='".$id."';";
        return $this->query($sql);    	
    }
    
    function findStock($id){
        $sql = "SELECT * FROM inv_stocks as Stock WHERE stock_id='".$id."';";
        return $this->query($sql);
    }
    function findStockAll(){
        $sql = "SELECT * FROM inv_stocks as Stock WHERE Stock.status=0 ORDER BY Stock.stock_name ASC;";
        return $this->query($sql);
    }
    function findType($id=null){
    	$sql = "SELECT * FROM inv_types as Type ORDER BY Type.type_id ASC;";
    	return $this->query($sql);
    }
    function findTypeByType($id){
    	$sql = "SELECT * FROM inv_types as Type WHERE Type.type_id='".$id."' ORDER BY Type.type_id ASC;";
    	return $this->query($sql);
    }
    function findTypeByCategory($id){
    	$sql = "SELECT * FROM inv_types as Type WHERE Type.category_id=".$id." ORDER BY Type.type_id ASC;";
    	return $this->query($sql);
    }
	function findCategory($id=null){
		$sql = "SELECT * FROM inv_categories AS Category";
		if(!empty($id)){
			$sql .= " WHERE Category.category_id=".$id;
		}
		return $this->query($sql);
	}
	function findPromotion($id=null){
		$sql = "SELECT * FROM inv_promotions AS Promotion ";
		if(!empty($id)){
			$sql.="WHERE Promotion.type_id=".$id;
		}
		$sql.=" GROUP BY Promotion.promotion_id ";
		return $this->query($sql);
	}
    function findSafyStock($id,$orderBy=null){
        $sql = "SELECT Category.name";
		$sql.=",Product.category_id";
		$sql.=",Product.type_id";
		$sql.=",Product.product_id";
		$sql.=",Product.product";
		$sql.=",Product.code";
		$sql.=",Product.quantity";		
		$sql.=",sum(Product.quantity) as q_number";
		$sql.=",MAX(Product.time) as utime ";
		$sql.=",(CASE WHEN L.min >= sum(Product.quantity) THEN 0 WHEN (L.min < sum(Product.quantity) AND sum(Product.quantity) < L.max ) THEN 1 WHEN sum(Product.quantity) >= L.max THEN 2 ELSE -1 END)as alert";
		$sql.=" FROM (inv_categories as Category LEFT JOIN inv_products as Product";
		$sql.=" ON Category.category_id=Product.category_id) ";
		$sql.=" LEFT JOIN inv_limitcheck as L ON Product.product_id = L.product_id ";
        $sql.=" WHERE Product.type_id=".$id;
        $sql.=" GROUP BY Product.product_id ";
	
        if(empty($orderBy)){
        	$sql.=" ORDER BY alert,q_number ASC";
			//$sql.=" ORDER BY alert ASC";
        }else{
        	$sql.=" ORDER BY ".$orderBy;
        }
		$datas = $this->query($sql);
		//print_r($datas);
		//echo $sql;
        return $datas;
    }
	
	public function limitDefault($id){
        $sql = "SELECT * FROM inv_limitcheck as D Where D.product_id='".$id."';";
		$data = $this->query($sql);
		if(count($data)>0){
			return $data[0];
		}
		return null;
	}
	public function updateLimit($id,$data){
		$sql = "UPDATE inv_limitcheck as D SET ";
		$sql.="D.max='".$data['max']."',";
		$sql.="D.min='".$data['min']."' ";
		$sql.=" WHERE D.product_id='".$id."';";
		//echo $sql;
		$this->query($sql);
	}
	public function insertLimit($id,$data){
		$sql = "INSERT INTO inv_limitcheck ";
		$sql.=" VALUES ( NULL,'".$id."',".$data['min'].",".$data['max'].");";
		
		$this->query($sql);
	}
	public function dataAlert($type_id,$option=null){
/*		$sql ="SELECT * FROM ";
        $sql.="(SELECT Type.name";
		$sql.=",Product.product_id";
		$sql.=",Product.code";
		$sql.=",Product.quantity";
		$sql.=",L.max";
		$sql.=",L.min";
		$sql.=",sum(Product.quantity) as q_number";
		$sql.=",(CASE WHEN L.min >= sum(Product.quantity) THEN 0 WHEN (L.min < sum(Product.quantity) AND sum(Product.quantity) < L.max ) THEN 1 WHEN sum(Product.quantity) >= L.max THEN 2 ELSE -1 END)as alert";		
		$sql.=" FROM (inv_types as Type LEFT JOIN inv_products as Product";
		$sql.=" ON Type.type_id=Product.type_id) ";
		$sql.=" LEFT JOIN inv_limitcheck as L ON ";
		$sql.=" Product.type_id = L.product_id ";
        $sql.=" WHERE Product.type_id='".$type_id."'";
        $sql.=" GROUP BY Product.product_id ";
		$sql.=")";
		$sql.=" AS Data";
		$sql.=" ".$option;

		$data1 = $this->query($sql);

		$sql ="SELECT * FROM ";
        $sql.="(SELECT Type.name";
		$sql.=",Product.product_id";
		$sql.=",Product.code";
		$sql.=",Product.quantity";
		$sql.=",L.max";
		$sql.=",L.min";		
		$sql.=",sum(Product.quantity) as q_number";
		$sql.=",(CASE WHEN L.min >= sum(Product.quantity) THEN 0 WHEN (L.min < sum(Product.quantity) AND sum(Product.quantity) < L.max ) THEN 1 WHEN sum(Product.quantity) >= L.max THEN 2 ELSE -1 END)as alert";		
		$sql.=" FROM (inv_types as Type LEFT JOIN inv_products as Product";
		$sql.=" ON Type.type_id=Product.type_id) ";
		$sql.=" LEFT JOIN inv_limitcheck as L ON ";
		$sql.=" Product.product_id = L.product_id ";
        $sql.=" WHERE Product.type_id='".$type_id."' AND L.max > 0";
        $sql.=" GROUP BY Product.product_id ";
		$sql.=")";
		$sql.=" AS Data";
		$sql.=" ".$option;
		$data2 = $this->query($sql);
		
		while(count($data2)>0){
			for($j=0;$j < count($data1);$j++){
				if($data2[0]['Data']['product_id']==$data1[$j]['Data']['product_id']){
					$data1[$j]['Data']['max']=$data2[0]['Data']['max'];
					$data1[$j]['Data']['min']=$data2[0]['Data']['min'];
					$data1[$j]['Data']['alert']=$data2[0]['Data']['alert'];
					array_pop($data2);
					break;
				}
			}
			//array_pop($data2);
		}
		*/

		$sql ="SELECT * FROM ";
        $sql.="((SELECT Type.name";
		$sql.=",Product.product_id";
		$sql.=",Product.code";
		$sql.=",Product.quantity";
		$sql.=",L.max";
		$sql.=",L.min";
		$sql.=",sum(Product.quantity) as q_number";
		$sql.=",(CASE WHEN L.min > sum(Product.quantity) THEN 0 WHEN (L.min <= sum(Product.quantity) AND sum(Product.quantity) <= L.max ) THEN 1 WHEN sum(Product.quantity) >= L.max THEN 2 ELSE -1 END)as alert";		
		$sql.=" FROM (inv_types as Type LEFT JOIN inv_products as Product";
		$sql.=" ON Type.type_id=Product.type_id) ";
		$sql.=" LEFT JOIN inv_limitcheck as L ON ";
		$sql.=" Product.type_id = L.product_id ";
        $sql.=" WHERE Product.type_id='".$type_id."'";
        $sql.=" GROUP BY Product.product_id )";

		$sql.=" UNION ";

        $sql.="(SELECT Type.name";
		$sql.=",Product.product_id";
		$sql.=",Product.code";
		$sql.=",Product.quantity";
		$sql.=",L.max";
		$sql.=",L.min";		
		$sql.=",sum(Product.quantity) as q_number";
		$sql.=",(CASE WHEN L.min >= sum(Product.quantity) THEN 0 WHEN (L.min <= sum(Product.quantity) AND sum(Product.quantity) <= L.max ) THEN 1 WHEN sum(Product.quantity) >= L.max THEN 2 ELSE -1 END)as alert";		
		$sql.=" FROM (inv_types as Type LEFT JOIN inv_products as Product";
		$sql.=" ON Type.type_id=Product.type_id) ";
		$sql.=" LEFT JOIN inv_limitcheck as L ON ";
		$sql.=" Product.product_id = L.product_id ";
        $sql.=" WHERE Product.type_id='".$type_id."' AND L.max > 0";
        $sql.=" GROUP BY Product.product_id ";
		$sql.=")";		

		$sql.=")";
		$sql.=" AS Data";
		
		$sql.=$option;
		$data1 =$this->query($sql);
		return $data1;
	}
	
	public function addQuality($product_id, $value){
			$sql= "UPDATE inv_products AS item SET item.quantity = item.quantity + ".$value." Where item.product_id='".$product_id."';";
			return $sql;
	}
	public function  queryProduct($sql){
		return $this->query($sql);
	}
}
?>