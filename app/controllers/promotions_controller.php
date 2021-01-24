<?php
class PromotionsController extends AppController{
	var $name='Promotion';
	
    function XML($id){
    	$this->layout='xml';
		$xmlData ='';
    	//query product & promotion

    	$promotions=$this->Promotion->findProductByPromotionID($id);
    	//print_r($promotions);

    	header("Content-type: text/xml; charset=UTF-8");
    	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    	$xmlData= '<?xml version="1.0" encoding="utf-8"?>';
    	$xmlData.= '<promotions>';
		
    	if(count($promotions)!=0){
    		//display product detail
   			$xmlData.= '<product id="'.$promotions[0]['Product']['product_id'].'">';
				$xmlData.='<name>'.$promotions[0]['Product']['product'].'</name>';
				if($promotions[0]['Product']['sale_price'] > 0){
					$xmlData.='<price>'.$promotions[0]['Product']['sale_price'].'</price>';
					$xmlData.='<cost>'.$promotions[0]['Product']['cost'].'</cost>';
				}else{
					$type_id = $promotions[0]['Product']['type_id'];
					$data = $this->Promotion->findType($type_id);
					
					$xmlData.='<price>'.$data[0]['Type']['sale_price'].'</price>';
					$xmlData.='<cost>'.$data[0]['Type']['cost'].'</cost>';
				}
			$xmlData.='</product>';
			
			$promotion_id=$promotions[0]['Product']['promotion_id'];
    		$datas=$this->Promotion->findPromotion($promotion_id);

	    	// display Promotions
    		foreach($datas as $data){
    			$xmlData.= '<promotion id="'.$data['Promotion']['promotion_id'].'">';
    				$xmlData.= '<name>'.$data['Promotion']['name'].'</name>';
    				$xmlData.= '<number>'.$data['Promotion']['limit'].'</number>';
    				$xmlData.= '<price>'.$data['Promotion']['price'].'</price>';
    			$xmlData.= '</promotion>';
    		}
    	}
    	if(count($promotions)!=0){
    		foreach($promotions as $promotion){
    			$xmlData.= '<stock>';
    			$xmlData.= '<id>'.$promotion['Stock']['stock_id'].'</id>';
    			$xmlData.= '<name>'.$promotion['Stock']['stock_name'].'</name>';
    			$xmlData.= '<detail>'.$promotion['Stock']['detail']." : ".eregi_replace("&nbsp;","",strip_tags($promotion['Product']['detail'])).'</detail>';
				$xmlData.= '<quantity>'.$promotion['Product']['quantity'].'</quantity>';
    			$xmlData.= '</stock>';
			}
    	}
    	$xmlData.= '</promotions>';
		
		echo $xmlData;
    }
    function text($id){
    	$datas=$this->Promotion->findPromotion($id);
    	
    	$this->layout='xml';
    	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    	foreach($datas as $data){
			echo $data['Promotion']['promotion_id'].'|'.$data['Promotion']['name'].'|';
			echo $data['Promotion']['limit'].'|'.$data['Promotion']['price'];
			echo ';';
    	}
    }
	function index(){
		$this->layout='home';
		
		$code ='<div>';
			$code.='<table>';
				$code.='<tr><th>no.</th><th>ประเภทสินค้า</th><th>ชื่อโปรโมชั่น</th></tr>';
			$code.='</table>';
		$code.='</div>';
		
        $this->set('HTML',$this->body->getHtml($code));
	}
    function type($id =null){
    	$this->layout='xml';
		if($id==null){return 0;	}
    	
		$xmlData ='';
    	//query product & promotion

    	$types=$this->Promotion->findType($id);
		
		$unitTypes = $this->Promotion->unitType($id);
		$promotions = $this->Promotion->findPromotionByType($id);

    	header("Content-type: text/xml; charset=UTF-8");
    	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1

    	$xmlData= '<?xml version="1.0" encoding="utf-8"?>';
    	$xmlData.= '<types>';
		$xmlType=$xmlPromotion=$xmlStock='';
		$promotion_id = ' ';
		if(count($types)!=0){
	   		foreach($promotions as $promotion){
				$promotion_id = $promotion['Promotion']['promotion_id'];
    			$xmlPromotion.= '<promotion id="'.$promotion['Promotion']['promotion_id'].'">';
    				$xmlPromotion.= '<name>'.$promotion['Promotion']['name'].'</name>';
    				$xmlPromotion.= '<number>'.$promotion['Promotion']['limit'].'</number>';
    				$xmlPromotion.= '<price>'.$promotion['Promotion']['price'].'</price>';
    			$xmlPromotion.= '</promotion>';
    		}
			$xmlType.= '<type id="'.$types[0]['Type']['type_id'].'">';
				$xmlType.='<serial>'.$types[0]['Type']['serial_id'].'</serial>';
				$xmlType.='<name>'.$types[0]['Type']['name'].'</name>';
				$xmlType.='<pic>'.$types[0]['Type']['pic'].'</pic>';
				$xmlType.='<price>'.$types[0]['Type']['sale_price'].'</price>';
				$xmlType.='<cost>'.$types[0]['Type']['cost'].'</cost>';
				if($types[0]['Type']['detail']!=null)
					$xmlType.='<detail>'.$types[0]['Type']['detail'].'</detail>';
				else
					$xmlType.='<detail> </detail>';
				$xmlType.='<promotion_id>'.$promotion_id.'</promotion_id>';
			$xmlType.='</type>';
		}
    	if(count($unitTypes)!=0){
    		foreach($unitTypes as $unit){
    			$xmlStock.= '<unit id="'.$unit['Unit']['unit_id'].'">';
	    			$xmlStock.= '<name>'.$unit['Unit']['unit_name'].'</name>';
    				$xmlStock.= '<ratio>'.$unit['Unit']['ratio'].'</ratio>';
					$xmlStock.= '<status>'.$unit['Unit']['status'].'</status>';
    			$xmlStock.= '</unit>';
			}
    	}
		$xmlData.=$xmlType.$xmlPromotion.$xmlStock;
		
    	$xmlData.= '</types>';
		
		echo $xmlData;
    }	
}
?>
