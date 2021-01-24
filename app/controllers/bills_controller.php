<?php
class BillsController extends AppController{
	var $name='Bill';
    var $helpers = array('Html', 'Javascript', 'Ajax');
    var $components = array('body','Casher');
    var $monthM = array(0,"Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
    var $_date=null;
    
    function index(){
    	$this->body->title='POS System';
    	$this->Casher->setCSS(HOME.'css/cash_style.css');
		$javascript = '<script src="'.HOME.'js/lib/bill.js" type="text/javascript"></script>';
		$javascript .='<script src="'.HOME.'js/lib/casher.js" type="text/javascript"></script>';
		$javascript .= '<script src="'.HOME.'js/lib/item.js" type="text/javascript"></script>';
		$javascript .= '<script type="text/javascript">var HOME="'.HOME.'"</script>';
		
		$this->body->setJavascript($javascript);
        $this->set('HTML',$this->body->getBody($this->getForm()));
        $this->layout='home';
    }
	
	function pos(){
		$this->layout='clear';
		$category = $this->Casher->getCategoryPOS($this->Bill->findCategory());
		$this->set('CATEGORY',$category);
		
		$type = $this->Casher->getTypePOS($this->Bill->findType(1));
		$this->set('TYPE',$type);
		//echo "<div style='border:1px solid red;'>test</div>";
	}

	function getForm(){
		$code='';
		$category = $this->Bill->findCategory();
		
		$code=$this->Casher->css;
		
		$code.=$this->formInsert();
		
		$code.='<div id="I_container">';
			//Container price
			$code.='<div class="I_price">';
				$code.='<div id="I_price_header">ราคารวม:</div>';
				$code.='<div id="I_price_summary">0.</div>';
			$code.='</div>';
			//end Container price
			$code.='<div class="I_order">';
				$code.='<div class="I_order_search"><span>สินค้า:</span><span><input type="text"/></span></div>';
		
				$code.='<div class="I_order_list">';
					$code.='<div class="I_order_list_header">';
						$code.='<div class="I_order_list_header_1" style="width:15px">@</div>';
						$code.='<div class="I_order_list_header_1" style="width:200px">สินค้า</div>';
						$code.='<div class="I_order_list_header_1" style="width:40px">ราคา</div>';
						$code.='<div class="I_order_list_header_1" style="width:40px">จำนวน</div>';
						$code.='<div class="I_order_list_header_1" style="width:60px">รวม</div>';
					$code.='</div>';

					$code.='<div id="I_order_list_product">';
					$code.='</div>';
				$code.='</div>';
				$code.='<div><a href="#" onclick="tab_click1()">แยกดูที่ละรายการ</a> <a href="#" onclick="tab_click2()">รายการขายจริง</a></div>';
				$code.='<div style="text-align:right;width:100%;padding-right:20px"><a href="#" onclick=\'submitOrder("'.HOME.'");\'><b>บันทึกข้อมูล</b></a></div>';
			$code.='</div>';
			$code.='<!-- Container category  -->';
			$code.='<div class="I_selection">';
				$code.='<div class="I_selection_category">';
					$code.='<div class="I_selection_category_head">ประเภทสินค้า</div>';
	
					$code.='<div id="I_selection_category_container">';
						$code.='<ol>';
						foreach($category as $data){
							$code.='<li>';
								$code.='<a href="#" onclick=\'getDataByAjax("'.HOME.'bills/getType/'.$data['Category']['category_id'].'","I_selection_type_container")\' style="background-image:url('.HOME.'img/products/'.$data['Category']['pic'].')">';
									$code.=$data['Category']['name'];
								$code.='</a></li>';
						}
						$code.='</ol>';
					$code.='</div>';
				$code.='</div>';
			
				$code.='<div class="I_selection_type">';
					$code.='<div class="I_selection_type_head">รายการสินค้า</div>';
					$code.='<div id="I_selection_type_container">';
					$code.=$this->getType(1);
					$code.='</div>';
				$code.='</div>';
			$code.='</div>';

		$code.='</div>';
		
		return $code;		
	}
	
	function getType($id){
		$this->layout='clear';
		$type= $this->Bill->findType($id);
		$price='';
		 
		$code ='<ol>';
		foreach($type as $data){
			if(empty($data['Type']['sale_price'])){
				$price ='N/A';
			}else{
				$price =$data['Type']['sale_price'];
			}
			
			$code.='<li>';
			$code.='<a href="#" class="typeA" title="ราคา '.$price.'฿" ';
/*
			$code.='onclick=\'';
			$code.='showItem(';							//เปิดหน้าต่างใน่รายละเอียด
			$code.='"'.$data['Type']['name'].'"';		//Product name
			$code.=',1';								//จำนวน
			$code.=','.$data['Type']['sale_price'];		//ราคาขาย
			$code.=',"'.HOME.$data['Type']['pic'].'"';	//รูป
			$code.=',"'.$data['Type']['serial_id'].'"';	//Type_id
			$code.=',"'.$data['Type']['cost'].'"';
			$code.=')\'';
*/
			$code.='onclick="showItemE('.$data['Type']['type_id'].')"';

			$code.=' style=\'background-image:url("'.HOME.$data['Type']['pic'].'")\' ';
			$code.='>';
			$code.=$data['Type']['name'];
			$code.='</a>';
			$code.='</li>';
		}
		$code.='</ol>';
		
		if(count($type)==0){
			$code= "No data";
		}
		
		$this->set('HTML',$code);
		return $code;
	}
	
	function getTypeItem($id = null,$s = null){
		$this->layout='clear';
		if(!empty($s)){
			//$type = $this->Casher->getTypePOS($this->Bill->findProductByID($id));
		}else{
			$type = $this->Casher->getTypePOS($this->Bill->findType($id));
		}
		
		$this->set('HTML',$type);
	}
	
	function formInsert(){
		$code='';
		//----- หน้าจอกรอก code และจำนวน ---------
		$code.='<div id="formAdd1"></div>';
		$code.='<div id="formAdd2">';
			$code.='<div class="closeForm">';
				//$code.='<a href="#" onclick=\'closeElement("formAdd1");closeElement("formAdd2");\'><img src="'.HOME.'img/stop.png"></a>';
				$code.='<a href="#" onclick=\'closeElement("formAdd1");closeElement("formAdd2");\'>';
					$code.='<img src="'.HOME.'img/stop.png">';
				$code.='</a>';
			$code.='</div>';
			$code.='<div id="formAdd2_1">';
			$code.='</div>';
		$code.='</div>';
		//-------------------------------------	
		return $code;
	}

	function checkBill(){
		//$_POST = $_GET;

		//$this->set('HTML',$this->Bill->add($_POST['order']));   // Billing Part
		$this->set('HTML',$this->Bill->update($_POST['order']));   // Billing Part
		$this->clearStock();
		$this->layout='clear';
	}

	function checkStock(){
		$orders = $_POST['product'];
			
		foreach($orders as $order){
			$products = $this->Bill->findProductByID($order['product_id']);
			$order_quantity = $order['quantity'];

			if(count($products)!=0){
				foreach($products as $product){
					
					echo $product['Product']['product_id'].": Product_quantity:".$product['Product']['quantity']."-=".$order_quantity."<br>";
					
					$product['Product']['quantity'] =$product['Product']['quantity']- $order_quantity;
					if($product['Product']['quantity'] <0){
						$order_quantity = abs($product['Product']['quantity']);
						$product['Product']['quantity']=0;
					}else{
						$order_quantity=0;
					}

					if($order_quantity <=0) break;
					
				}
				if($order_quantity >0){
					//Error Over stock
					echo "<br>Error Over Stock. :".$product['Product']['product_id']."<br>";
					echo "<br>ข้อมูลที่กรอกมีมากกว่าที่ในระบบบันทึก และได้ทำการตัดเท่าที่มี<br>";
				}
			}else{
				//บันทึกข้อมูล มี order แต่ไม่มี ข้อมูลที่บันทึก
				echo "<br>Error Over Stock (ไม่มีรหัสนี้อยู่ในระบบ)<br>";
			}
		}
	}
	function clearStock(){
		$orders = $_POST['product'];
		//print_r($orders);
		foreach($orders as $order){
			$products = $this->Bill->findProductByID($order['product_id']);
			$order_quantity = $order['quantity'];

			if(count($products)!=0){
				foreach($products as $product){
					echo $product['Product']['product_id'].": Product_quantity:".$product['Product']['quantity']."-=".$order_quantity."<br>";
					$product['Product']['quantity'] =$product['Product']['quantity']- $order_quantity;
					if($product['Product']['quantity'] <0){
						$order_quantity = abs($product['Product']['quantity']);
						$product['Product']['quantity']=0;
					}else{
						$order_quantity=0;
					}
					//Update Stock
					
					$this->Bill->updateStock($product['Product']['id'],$product['Product']['quantity']);
					//echo "updated<br>";
					if($order_quantity <=0) break;
					
				}
				if($order_quantity >0){
					//Error Over stock
					echo "<br>Error Over Stock. :".$product['Product']['product_id']."<br>";
					echo "<br>ข้อมูลที่กรอกมีมากกว่าที่ในระบบบันทึก และได้ทำการตัดเท่าที่มี<br>";
				}
			}else{
				//บันทึกข้อมูล มี order แต่ไม่มี ข้อมูลที่บันทึก
				echo "<br>Error Over Stock (ไม่มีรหัสนี้อยู่ในระบบ)<br>";
			}
		}
	}

/// Billing section...
	
	public function today(){
		
		$this->layout='home';
		//header("Cache-Control: no-cache, must-revalidate");
		$start = mktime(0,0,0,date('n'),date('d'),date('y'));
		$fin = mktime(23,59,59,date('n'),date('d'),date('y'));
		$datas=$this->Bill->findBillByDate($start,$fin);
		$result=0;
		$sumProfit =0;
		
		$code= '<table border="1" width="800px">';
		$code.='<tr><td width="40px">No.</td><td>Bill No.</td><td>Time</td><td>ยอดขาย</td><td>กำไร</td></tr>';
		for($i=0;count($datas)>$i ;$i++){
			$code.='<tr>';
			$code.='<td>'.($i+1).'</td>';
			$code.='<td>';
			$code.='<a href="'.HOME.'bills/byBill/'.$datas[$i]['Bill']['bill_id'].'">';
			$code.=$datas[$i]['Bill']['bill_id'].'</a>';
			$code.='</td>';
			$code.='<td width="100px">'.date('h:i:s A',$datas[$i]['Bill']['date']).'</td>';
			$code.='<td width="100px" align="right">'.$datas[$i]['Bill']['result'].'</td>';
			$code.='<td width="100px" align="right">'.$datas[$i]['Bill']['profit'].'</td>';
			$code.='</tr>';
			$result+=$datas[$i]['Bill']['result'];
			$sumProfit+=$datas[$i]['Bill']['profit'];
		}
		$code.='<tr>';
			$code.='<td colspan="3">รวมยอดขาย</td>';
			$code.='<td align="right" style="font-size:">'.$result.'</td>';
			$code.='<td align="right">'.$sumProfit.'</td>';
			$code.='</tr>';
		$code.='</table>';
		$this->set('Bill',$code); 
	}
	
	public function byDate($y,$m,$d){
		$this->layout='home';
		//header("Cache-Control: no-cache, must-revalidate");
		/*$y = substr($d,2,2);
		$m = substr($d,4,2);
		$d = substr($d,6,2);*/
		//echo $y." ". $m." ".$d ."<br>";
		//echo date('y')." ".date('n')." ".date('d');
		
		$start = mktime(0,0,0,$m,$d,$y);
		$fin = mktime(23,59,59,$m,$d,$y);
		
		$datas=$this->Bill->findBillByDate($start,$fin);
		$result=0;
		$sumProfit =0;
		
		
		$code= '<table border="1" width="800px">';
		$code.='<tr><td width="40px">No.</td><td>Bill No.</td><td>Time</td><td>ยอดขาย</td><td>กำไร</td></tr>';
		for($i=0;count($datas)>$i ;$i++){
			$code.='<tr>';
			$code.='<td>'.($i+1).'</td>';
			$code.='<td>';
			$code.='<a href="'.HOME.'bills/byBill/'.$datas[$i]['Bill']['bill_id'].'">';
			$code.=$datas[$i]['Bill']['bill_id'].'</a>';
			$code.='</td>';
			$code.='<td width="100px">'.date('h:i:s A',$datas[$i]['Bill']['date']).'</td>';
			$code.='<td width="100px" align="right">'.$datas[$i]['Bill']['result'].'</td>';
			$code.='<td width="100px" align="right">'.$datas[$i]['Bill']['profit'].'</td>';
			$code.='</tr>';
			$result+=$datas[$i]['Bill']['result'];
			$sumProfit+=$datas[$i]['Bill']['profit'];
		}
		$code.='<tr>';
			$code.='<td colspan="3">รวมยอดขาย</td>';
			$code.='<td align="right" style="font-size:">'.$result.'</td>';
			$code.='<td align="right">'.$sumProfit.'</td>';
			$code.='</tr>';
		$code.='</table>';

		$this->set('Bill',$code);
	}
	
	public function byBill($id){
		$this->layout='home';
		$sumCost = 0;
		$sumProfit =0;
		$datas = $this->Bill->findOrderByBill($id);
		//print_r($datas);
		if(count($datas)==0){
			return 0;
		}
		$code='<table border="1" width="700px">';
		$code.='<tr><td colspan="8">Bill No.'.$datas[0]['Bill']['bill_id'].'</td></tr>';
		$code.='<tr><td colspan="8">ลูกค้า :'.$datas[0]['Bill']['customer_id'].'</td></tr>';
		$code.='<tr><td colspan="8">วันที่ '.date("j F Y",$datas[0]['Bill']['date']).'</td></tr>';
		$code.='<tr><td width="50px">No.</td><td width="350px">รายการ</td><td>หน่วยละ</td><td>จำนวน</td><td>จำนวนเงิน</td><td>ทุน</td><td>กำไร</td></tr>';

		
		for($i=0;count($datas)>$i ;$i++){
			
			if($datas[$i]['O']['cost'] == 0){ 
				$profit = $cost ="N/A";
			}else {
				$cost =$datas[$i]['O']['cost'];
				$profit = $datas[$i]['O']['profit'] = $datas[$i]['O']['result']-$datas[$i]['O']['cost'];
				$sumCost += $datas[$i]['O']['cost'];
				$sumProfit += $datas[$i]['O']['profit'];
			}
			
			$code .="<tr>";
			$code .="<td>".($i+1)."</td>";
			$code .="<td>".$datas[$i]['Type']['name']."</td>";
			$code .="<td>".$datas[$i]['O']['unit_price']."</td>";
			$code .="<td>".$datas[$i]['O']['quantity']."</td>";
			$code .="<td>".$datas[$i]['O']['result']."</td>";
			$code .="<td>".$cost."</td>";
			$code .="<td>".$profit."</td>";
			$code .="</tr>";
		}
		
		$code.='<tr style="font-size:30px">';
			$code.='<td colspan="4" align="right">รวมเงิน  </td>';
			$code.='<td>'.$datas[0]['Bill']['result'].'</td>';
			$code.='<td>'.$sumCost.'</td>';
			$code.='<td>'.$sumProfit.'</td>';
		$code.='</tr>';
		$code.='</table>';
		echo $code;
	}
	private function addDate($data,$num){
		$b =array('Profit'=>0,'Result'=>0,'a_Date'=>0);
		$a = array_fill(0, $num,$b);
		//for($i=0;$i<$num;$i++){
			for($j=0 ;$j < count($data);$j++){
				$a[$data[$j][0]['a_Date']-1] =$data[$j][0]; 
			}			
		//}
		return $a;
	}
	private function pieChartSale($data,$type,$number=null){
		if($number==null)$number = 7;
		if(count($data)<$number)$number=count($data);
		//echo count($data);
		
		$aType = array('sale'=>array("persenSale","SumSale","ResultSale"),'profit'=>array("persenMargin","SumProfit","ResultProfit"));
		$chartValue ="chd=t:";//data of chart
		$chartLabel ="chl=";//Label
		$color =array("FF0000","00FF00","0000FF","FFFF00","4B0082","FFA500","FF00FF","5757FF","F0FFF0");
		$chartColor="chco=";
		$labelChart ="<div style='width:300px;padding:3px;float:left;background-color:#D8F0F8'>";
		
		$sumSale 	=0;
		$sumProfit	=0;
		
		for($i=0;$i<$number;$i++){
			$chartValue .=$data[$i][0][$aType[$type][0]];
			$chartLabel .=number_format($data[$i][0][$aType[$type][0]])."%"."-".$data[$i]['Category']['name'];
			$chartColor .=$color[$i];
			
			$labelChart.="<div style='width:280px;margin:3px'>";
				$labelChart.="<div style='float:left;width:15px;border:1px solid gray;background-color:#".$color[$i]."'></div>";
				$labelChart.="<div style='float:leftwidth:275px'>";
					if(empty($this->_date['day']))$labelChart.="<a href='".HOME."bills/reporttype/".$data[$i]['Category']['id']."/".$this->_date['year']."/".$this->_date['month']."'>";
					else $labelChart.="<a href='".HOME."bills/reporttype/".$data[$i]['Category']['id']."/".$this->_date['year']."/".$this->_date['month']."/".$this->_date['day']."'>";
						$labelChart.=$data[$i]['Category']['name']." ".$data[$i][0][$aType[$type][0]]."%";
					$labelChart.="</a>";
					$labelChart.="(".number_format($data[$i][0][$aType[$type][1]]).")";
				$labelChart.="</div>";
			$labelChart.="</div>";
			//if($i < $number-1){
				$chartValue .=",";
				$chartLabel .="|";
				$chartColor .="|";
			//}
			$sumSale 	+=$data[$i][0]['SumSale'];
			$sumProfit  +=$data[$i][0]['SumProfit'];
		}

		$data['Other']['name']="Other";
		$data['Other']['SumSale']=$data['ResultSale']-$sumSale;
		$data['Other']['SumProfit']=$data['ResultProfit']-$sumProfit;
		
		if($data['ResultSale']!=0 )		$data['Other']['persenSale']	=round($data['Other']['SumSale']*100/$data['ResultSale']);
		else 	$data['Other']['persenSale']	=0;
		if($data['ResultProfit']!=0 )	$data['Other']['persenMargin']	=round($data['Other']['SumProfit']*100/$data['ResultProfit']);
		else	$data['Other']['persenMargin']	=0;
		
		$labelChart.="<div style='width:280px;margin:3px'>";
				$labelChart.="<div style='float:left;width:15px;border:1px solid gray;background-color:#".$color[$number]."'></div>";
				$labelChart.="<div style='float:leftwidth:275px'>";
					$labelChart.="<a href=''>".$data['Other']['name']." ".$data['Other'][$aType[$type][0]]."%"."</a> (".number_format($data['Other'][$aType[$type][1]]).")</div>";
		$labelChart.="</div>";
		
		$chartValue .=$data['Other'][$aType[$type][0]];;
		$chartLabel .=$data['Other'][$aType[$type][0]]."%"."-".$data['Other']['name'];
		$chartColor .=$color[$number];
		
		//====================================================================================//
		//								Google Chart
		//====================================================================================//
		$code ='<div>';
			$code.='<div width="400px" style="float:left;">';
				$code.='<img src="http://chart.apis.google.com/chart?cht=p&chs=500x250&'.$chartValue.'&'.$chartLabel.'&'.$chartColor.'">';
			$code.='</div>';
			$code.='<div style="float:left">';
				$code.=$labelChart;
			$code.='</div>';
		$code.='</div>';
		//=====================================================================================

		return $code;
	}
	public function mreport($year,$month,$category=null){
		$this->layout='home';
		header("Cache-Control: no-cache, must-revalidate");
		$valueMax=15000;

		$resultMargin = 0;
		$resultProfit = 0;		
		$this->_date=array('year'=>$year,'month'=>$month);
		
		if(empty($year)&& empty($month)){
			$this->flash("Can't calculate this report <br>Return to Main page",'/');
			return 0;
		}
		
		$numDayOfMonth = cal_days_in_month(CAL_GREGORIAN,$month,$year);
		$dateStart 	= $year."-".$month."-01 00:00:00";
		$dateEnd 	= $year."-".$month."-".$numDayOfMonth." 23:59:59";

		
		if($category ==null){
		//=============================================	
			$data = $this->Bill->reportMonthly($dateStart,$dateEnd);
		//==============================================
			$option = " ORDER BY SumSale DESC ";
			$dataSumSaleCategory = $this->Bill->reportSumCategory($dateStart,$dateEnd,$option);
		//------------------------------------------------------------------
			$option = " ORDER BY SumProfit DESC";
			$dataSumProfitCategory = $this->Bill->reportSumCategory($dateStart,$dateEnd,$option);
		//------------------------------------------------------------------
		}


		$dateLink ='';
		$chartProfit ='';
		$chartMargin ='';
		
		$data = $this->addDate($data,$numDayOfMonth);
		
		for($i=0;$i<count($data);$i++){
			$resultMargin +=$data[$i]['Result'];
			$resultProfit +=$data[$i]['Profit'];
			
			$chartProfit .=($data[$i]['Profit']*(100/$valueMax));
			$chartMargin .=($data[$i]['Result']*(100/$valueMax))-($data[$i]['Profit']*(100/$valueMax));
			$dateLink.='<div class="reportM">';
				$dateLink.='<a href="'.HOME.'bills/dreport/'.$year.'/'.$month.'/'.($i+1).'" title="'.$data[$i]['Profit'].'">';
					if($data[$i]['a_Date']>0){
						$dateLink.=(int)$data[$i]['a_Date'];
					}else{
						$dateLink.=$i+1;
					}
					
				$dateLink.='</a>';
			$dateLink.='</div>';

			if($i+1 < count($data)){
				$chartProfit.=',';
				$chartMargin.=',';
			} 
		}

		
		
		$chart='<div><img src="http://chart.apis.google.com/chart?cht=bvs&chs=750x400&chbh=20,2,0&chco=4d89f9,c6d9fd&chxt=y&chxr=0,0,'.$valueMax.'&chd=t:'.$chartProfit.'|'.$chartMargin.'"><div>';
		
		$code ='<div>';
			$code.='<div id="reportHead">';
				$code.=$this->monthM[(int)$month].' - '.$year;
			$code.='</div>';
			$code.='<div id="report01">';
				$code.=$chart;
				$code.='<div style="margin-left:35px">';
					$code.=$dateLink;
				$code.='</div>';
		$code .='</div>';
		
		$code.='<div>';
			$code.='<table width="400px">';
				$code.='<tr>';
					$code.='<td width="100px" align="right">ยอดสะสม</td><td>'.number_format($resultMargin).'</td>';
				$code.='</tr>';
				$code.='<tr>';
					$code.='<td>ยอดกำไร</td><td>'.number_format($resultProfit).'</td>';
				$code.='</tr>';
			$code.='</table>';
		$code.='</div>';
		
		//##################### Pie Chart #########################

		
		$code.="<div style='margin:5px;float:left'><h1>ยอดขาย</h1>";
			$code.= $this->pieChartSale($dataSumSaleCategory,'sale');
		$code.="</div>";
		
		//------------------------------------------------------------------
		$code.="<div style='margin:5px;float:left'><h1>รายได้</h1>";
			$code.= $this->pieChartSale($dataSumProfitCategory,'profit');
		$code.="</div>";
		//------------------------------------------------------------------
		$this->set('HTML',$this->body->getHtml($code));
	}
	public function dreport($year,$month,$day){
		$this->layout='home';
		if(empty($year)&& empty($month)&& empty($day)){
			$this->flash("Can't calculate this report <br>Return to Main page",'/');
			return 0;
		}
		header("Cache-Control: no-cache, must-revalidate");
		
		$code 			='';
		$resultMargin 	= 0;
		$resultProfit 	= 0;
		$dateStart 		= $year."-".$month."-".$day." 00:00:00";
		$dateEnd	 	= $year."-".$month."-".$day." 23:59:59";
		$this->_date=array('year'=>$year,'month'=>$month,'day'=>$day);
		//// Generrate Chart.
		
		$option = " ORDER BY SumSale DESC ";
		$data 			= $this->Bill->reportSumCategory($dateStart,$dateEnd,$option);
		$code.="<div style='width:100%;text-align:right'>รายงานวันที่: ".$day."/<a href='".HOME."bills/mreport/".$year."/".$month."'>".$month."</a>/".$year."</div>";
		
		$code.="<div style='margin:5px;float:left'><h1>ยอดขาย (".number_format($data['ResultSale']).")</h1>";
			$code.= $this->pieChartSale($data,'sale');
		$code.="</div>";
		//$code.="<hr>";
		
		//----------------------------- Profit----------------------------------
		
		$option = " ORDER BY SumProfit DESC";
		$data 			= $this->Bill->reportSumCategory($dateStart,$dateEnd,$option);
		$code.="<div style='margin:5px;float:left'><h1>กำไร (".number_format($data['ResultProfit']).")</h1>";
			$code.= $this->pieChartSale($data,'profit');
		$code.="</div>";
		$this->set('HTML',$this->body->getHtml($code));
	}
	
	public function reporttype($category,$year,$month,$day=null){
		$this->layout='home';
		if(empty($year)|| empty($month)|| empty($category)){
			$this->flash("Can't calculate this report <br>Return to Main page",'/');
			return 0;
		}
		header("Cache-Control: no-cache, must-revalidate");	
		
		$code 			='';
		$resultMargin 	= 0;
		$resultProfit 	= 0;
		if($day!=null){
			$dateStart 		= $year."-".$month."-".$day." 00:00:00";
			$dateEnd	 	= $year."-".$month."-".$day." 23:59:59";
		}else{
			$numDayOfMonth = cal_days_in_month(CAL_GREGORIAN,$month,$year);
			$dateStart 	= $year."-".$month."-01 00:00:00";
			$dateEnd 	= $year."-".$month."-".$numDayOfMonth." 23:59:59";
		}
		//// Generrate Chart.
		
		$option = " ORDER BY SumSale DESC ";
		$data 			= $this->Bill->reportSumType($dateStart,$dateEnd,$category,$option);
		$code.="<div style='width:100%;text-align:right'>รายงานวันที่: ".$day."/<a href='".HOME."bills/mreport/".$year."/".$month."'>".$month."</a>/".$year."</div>";
		
		$code.="<div style='margin:5px;float:left'><h1>ยอดขาย (".number_format($data['ResultSale']).")</h1>";
			$code.= $this->pieChartSale($data,'sale');
		$code.="</div>";
		//$code.="<hr>";
		
		//----------------------------- Profit----------------------------------
		
		$option = " ORDER BY SumProfit DESC";
		$data 			= $this->Bill->reportSumType($dateStart,$dateEnd,$category,$option);
		$code.="<div style='margin:5px;float:left'><h1>กำไร (".number_format($data['ResultProfit']).")</h1>";
			$code.= $this->pieChartSale($data,'profit');
		$code.="</div>";
		$this->set('HTML',$this->body->getHtml($code));		
	}
	
		/* เปิด PO ใบเสร็จ 
	// status PO 
	//  0 = Not Open
	//  1 = PO Open
	//  2 = PO Hold
	//  3 = PO Closed
	*/	
	public function openBill(){
		$this->layout='clear';
		$bill_id = $this->Bill->openPO();
		print($bill_id);
	}
}
?>