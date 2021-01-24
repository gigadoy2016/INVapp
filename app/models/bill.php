<?php
class Bill extends AppModel{
	var $bill_id='';
	var $name='Bill';
	var $bookNo=1;
	var $customerID=0;
	var $c_licence ='';
	var $collector_id=0;
	var $timeCreate='NULL';
	var $detail=null;
	var $status =0;
	var $summary=0;
	

	function findCategory($id=null){
		$sql = "SELECT * FROM inv_categories AS Category";
		if(!empty($id)){
			$sql .= " WHERE Category.category_id='".$id."'";
		}
		$sql .=" ORDER BY Category.order ASC;";
		return $this->query($sql);
	}
	function findProductByID($id){
		$sql="select * from inv_products AS Product ";
		$sql.="where Product.product_id='".$id."' ";
		$sql.="order by Product.quantity DESC;";
		return $this->query($sql);
	}
	function findType($cat_id=null){
		$sql = "SELECT * FROM inv_types AS Type";
		if(!empty($cat_id)){
			$sql .= " WHERE Type.category_id=".$cat_id;
		}
		$sql .=" ORDER BY Type.order ASC;";
		return $this->query($sql);
	}
	function findProduct($p_id){
		$sql = "SELECT * FROM inv_products AS Product";
		if(!empty($p_id)){
			$sql .= " WHERE Product.type_id=".$p_id.' ORDER BY code asc';
		}
		return $this->query($sql);
	}
	
	public function setBill(){
		
		if(!empty($_POST['bill'])){
			$bill = $_POST['bill'];
			
			$this->summary = $bill['result'];
			if($bill['customer'] != null)
				$this->customerID = $bill['customer'];
			if($bill['licence'] != null)
				$this->c_licence = $bill['licence'];
			if($bill['detail'] != null)
				$this->detail = $bill['detail'];
			if($bill['collector'] != null)
				$this->collector_id = $bill['collector'];
			if($bill['status'] != null)
				$this->status = $bill['status'];
			if($bill['bill_id'] != null)
				$this->bill_id = $bill['bill_id'];
		}
	}
	
	function add($data){
		$comment ='';
		//print_r($_POST['order']);
		$this->timeCreate =$_SERVER['REQUEST_TIME'];
		$this->setBill();
		//เพิ่มข้อมูล Bill
		

		$sql="INSERT INTO inv_bills (bill_id, book_no, customer_id, date, c_licence, result, collector_id, detail) ";
		$sql.=" VALUES (";
		$sql.="NULL";						//Bill id.
		$sql.=", ".$this->bookNo;			//Book No.
		$sql.=", ".$this->customerID;		//Customer Id.
		$sql.=", '".$this->timeCreate."'";	//Time Create
		$sql.=", '".$this->c_licence."'";	//Commence Licence.
		$sql.=", '".$this->summary."'";						//Bill Summary.
		$sql.=", ".$this->collector_id;		//รหัสผู้ขาย
		$sql.=", NULL";						//รายละเอียด
		$sql.=")";
		
		//echo $sql;
		$this->query($sql); //Insert Data;
		
		// Product Order in Bill.
		$order='';
		$bill = $this->findBydate($this->timeCreate);
		$comment ='<div>Bill no.:'.$bill['Bill']['bill_id'].'</div>';
		//print_r($data);
		
		$sumProfit =0;
		foreach($data as $product){
			if($product['cost'] == null || $product['cost']==0){
				$profit = 0;
			}else{
				$profit = $product['sum']- $product['cost'];		
			}

			$sumProfit +=$profit;
			
			$order="INSERT INTO inv_orders ";
			$order.="(order_id, bill_id, type_id, product_id, quantity, unit_price, result,cost,profit) ";
			$order.="VALUES (";
			$order.="NULL";//Order id.
			$order.=", ".$bill['Bill']['bill_id'];//Bill Id.
			$order.=", '".$product['type_id']."'";//Type Id.
			$order.=", '".$product['product_id']."'";//Product Id.
			$order.=", ".$product['quantity'];//Product quantity.
			$order.=", ".$product['priceUnit'];//Price per unit.
			$order.=", ".$product['sum'];//Result of product
			$order.=", ".$product['cost'];//Result of product
			if($product['cost']!=0){
				$order.=", ".($product['sum']-$product['cost']);//Result of product
			}else{
				$order.=", 0";//Result of product
			}
			$order.=");";
			$this->query($order); //Insert Data;
		}
		//echo $order;
		$this->updateProfit($bill['Bill']['bill_id'],$sumProfit);

		$comment = '<div style="width:100%;text-align:center"><input type="submit" onclick="window.location.reload();" value="OK"></div>';
		return $comment;
	}
	
	function update($data){
		$this->timeCreate =$_SERVER['REQUEST_TIME'];
		$this->setBill();
		// Update data หลังจาก เปิด PO
			/* เปิด PO ใบเสร็จ 
		// status PO 
		//  0 = Not Open
		//  1 = PO Open
		//  2 = PO Hold
		//  3 = PO Closed
		*/

		$sql ="UPDATE inv_bills ";
		$sql.="SET ";
		$sql.="book_no = '".$this->bookNo."' , ";//book Id.
		$sql.="customer_id = '".$this->customerID."' , ";
		$sql.="date = '".$this->timeCreate."' , ";
		//$sql.="c_licence = '".$this->c_licence."' , ";
		$sql.="result = '".$this->summary."' , ";
		//$sql.="collector_id = '".$this->collector_id."' , ";
		$sql.="status = '".$this->status."'  ";
		$sql.="WHERE bill_id='".$this->bill_id."'; ";
		
		$this->query($sql);  //Update data and change Status =3  (closed PO)
		
		// Product Order in Bill.

		$order='';
		$bill = $this->findBydate($this->timeCreate);
		
		$sumProfit =0;
		foreach($data as $product){
			if($product['cost'] == null || $product['cost']==0){
				$profit = 0;
			}else{
				$profit = $product['sum']- $product['cost'];		
			}

			$sumProfit +=$profit;
			
			$order="INSERT INTO inv_orders ";
			$order.="(order_id, bill_id, type_id, product_id, quantity, unit_price, result,cost,profit) ";
			$order.="VALUES (";
			$order.="NULL";//Order id.
			$order.=", ".$this->bill_id;//Bill Id.
			$order.=", '".$product['type_id']."'";//Type Id.
			$order.=", '".$product['product_id']."'";//Product Id.
			$order.=", ".$product['quantity'];//Product quantity.
			$order.=", ".$product['priceUnit'];//Price per unit.
			$order.=", ".$product['sum'];//Result of product
			$order.=", ".$product['cost'];//Result of product
			if($product['cost']!=0){
				$order.=", ".($product['sum']-$product['cost']);//Result of product
			}else{
				$order.=", 0";//Result of product
			}
			$order.=");";
			$this->query($order); //Insert Data;
		}
		echo $order;
		
		$this->updateProfit($this->bill_id,$sumProfit);

		//$comment = '<div style="width:100%;text-align:center"><input type="submit" onclick="window.location.reload();" value="OK"></div>';
		return 1;	
		
	}
	
	function updateStock($id,$quantity){
		$sql ="UPDATE inv_products ";
		$sql .="SET quantity='".$quantity."',";
		$sql .="time='".$_SERVER['REQUEST_TIME']."' ";
		$sql .="WHERE id='".$id."';";
		$this->query($sql);
	}
	private function updateProfit($id,$cost){
		$sql="UPDATE inv_bills as Bill ";
		$sql.="SET Bill.profit='".$cost."' ";
		$sql.=" WHERE Bill.bill_id='".$id."';";
		echo $sql;
		$this->query($sql);
	}	
	public function findBillByDate($st,$fn){
		$sql="SELECT * FROM inv_bills AS Bill";
		$sql.=" WHERE Bill.date > '".$st."' AND ";
		$sql.="Bill.date < '".$fn."' ";
		$sql.=" ORDER BY Bill.date ASC;";
		return $this->query($sql);
	}
	
	public function findOrderByBill($bill_id){
		$sql="SELECT * FROM inv_orders AS O";
		$sql.=",inv_bills as Bill";
		$sql.=",inv_types as Type";
		//$sql.=",inv_products AS Product";
		$sql.=" WHERE (O.bill_id = Bill.bill_id) ";
		$sql.=" AND (O.type_id = Type.serial_id) ";
		//$sql.=" AND (Product.product_id = O.product_id)";
		$sql.=" AND O.bill_id='".$bill_id."'";
		//echo $sql;
		return $this->query($sql);
	}
	public function reportMonthly($start,$end){
		
		$sql = "SELECT ";
		$sql.="sum(Bill.profit) AS Profit";
		$sql.=",sum(Bill.result) AS Result";
		$sql.=",FROM_UNIXTIME(Bill.date,'%d') AS a_Date "; 
		$sql.="FROM inv_bills as Bill ";
		$sql.="WHERE UNIX_TIMESTAMP('".$start."')<Bill.date ";
		$sql.="AND UNIX_TIMESTAMP('".$end."')>Bill.date ";
		$sql.="GROUP BY a_Date ";
		$sql.="ORDER By a_Date ASC ";
		$data =$this->query($sql);
		
		return $data;
	}
	private function sumData($data){
	
		$sumSale=0;
		$sumCost=0;
		$sumProfit=0;
		
		for($i=0;$i<count($data);$i++){
			$sumSale 	+= $data[$i][0]['SumSale'];
			$sumCost 	+= $data[$i][0]['SumCost']; 
			$sumProfit 	+= $data[$i][0]['SumProfit']; 
		}
		for($i=0;$i<count($data);$i++){
			$data[$i][0]['persenSale']	=round($data[$i][0]['SumSale']*100/$sumSale);
			if($data[$i][0]['SumProfit']!=0 ){
				$data[$i][0]['persenMargin']=round(($data[$i][0]['SumProfit'])*100/($sumProfit));
			}else{
				$data[$i][0]['persenMargin']=0;
			}
		}
		
		
		$data['ResultSale']=$sumSale;
		$data['ResultCost']=$sumCost;
		$data['ResultProfit']=$sumProfit;
		
		return $data;
	}
	public function reportSumCategory($start,$end,$option =null){
		$sql ="SELECT "; 
		$sql.="Category.category_id as id,"; 
		$sql.="Category.name,";
		$sql.="sum(Product.result) as SumSale,";
		$sql.="sum(Product.cost) as SumCost, "; 
		$sql.="sum(Product.profit) as SumProfit "; 
		$sql.="FROM inv_orders AS Product ";
		$sql.="INNER JOIN inv_bills AS Bill ON ( Product.bill_id = Bill.bill_id ) "; 
		$sql.="LEFT JOIN inv_types AS Type ON (Product.type_id = Type.serial_id) "; 
		$sql.="LEFT JOIN inv_categories AS Category ON (Category.category_id = Type.category_id) "; 
		$sql.="WHERE UNIX_TIMESTAMP(  '".$start." 00:00:00' ) < Bill.date ";
		$sql.="AND UNIX_TIMESTAMP(  '".$end." 23:59:59' ) > Bill.date "; 
		$sql.="GROUP BY Category.category_id ";
		
		if($option!=null){
			$sql.=$option;
		}
		
		$data =$this->query($sql);
		$data =$this->sumData($data);
		
		return $data;
	}
	public function reportSumType($start,$end,$category_id,$option =null){
	
		$sql ="SELECT "; 
		$sql.="Category.type_id as id,"; 
		$sql.="Category.name,";
		$sql.="sum(Product.result) as SumSale,";
		$sql.="sum(Product.cost) as SumCost, "; 
		$sql.="sum(Product.profit) as SumProfit "; 
		$sql.="FROM inv_orders AS Product ";
		$sql.="INNER JOIN inv_bills AS Bill ON ( Product.bill_id = Bill.bill_id ) "; 
		$sql.="LEFT JOIN inv_types AS Category ON (Product.type_id = Category.serial_id) "; 
		$sql.="LEFT JOIN inv_categories AS Cate ON (Cate.category_id = Category.category_id) "; 
		$sql.="WHERE UNIX_TIMESTAMP(  '".$start." 00:00:00' ) < Bill.date ";
		$sql.="AND UNIX_TIMESTAMP(  '".$end." 23:59:59' ) > Bill.date "; 
		$sql.="AND Cate.category_id= ".$category_id." ";
		$sql.="GROUP BY Category.type_id ";
		
		if($option!=null){
			$sql.=$option;
		}
		$data =$this->query($sql);
		$data =$this->sumData($data);
		return $data;
	}
	/* เปิด PO ใบเสร็จ 
	// status PO 
	//  0 = Not Open
	//  1 = PO Open
	//  2 = PO Hold
	//  3 = PO Closed
	*/	
	public function openPO(){
	//	ไว้ทำส่วนขยาย เพิ่มการ reused bill ที่เปิดแล้วไม่ได้ปิด 
	// - เช็ค status ที่เท่ากับ 1 และ เวลามากกว่า 1 อาทิตย์
	
		$time = $_SERVER['REQUEST_TIME'];
		$sql ="INSERT INTO  `inv`.`inv_bills` (`bill_id` ,`book_no` ,`status`,`date`)VALUES (NULL ,  '2',  '1','".$time."'); ";	
		$data = $this->query($sql); 			// get last records
		return mysql_insert_id();
	}
}
?>