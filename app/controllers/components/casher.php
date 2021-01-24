<?php
class CasherComponent extends Object{
	var $name = 'Casher';
	var $controller = true;
	var $css = '';
	
	function setCSS($url){
		$this->css .= '<link rel="stylesheet" type="text/css" href="'.$url.'" />';
	}
	
	function getCategoryPOS($datas){
		
		$code = '';
		$count=0;
		foreach($datas as $CAT){
			if ($CAT['Category']['pic'] != null and $CAT['Category']['pic'] != "") 
				$picture = " style=\"background-image:url('".HOME."img/products/".$CAT['Category']['pic']."') \"";
			else
				$picture ="";
			
			$code .="<li class=\"ui-pos\" value=\"".$CAT['Category']['category_id']."\" ".$picture." >".$CAT['Category']['name']."</li>";
		}
		return $code;
	}
	function getTypePOS($datas){
		//print_r($datas);
		$code = '';
		$count=0;
		
		foreach($datas as $TYPE){
			if ($TYPE['Type']['pic'] != null and $TYPE['Type']['pic'] != "") {
			$picture = " style=\"background-image:url('"."../".$TYPE['Type']['pic']."') \"";
			}else
				$picture ="";
		
		
			$code .="<li class=\"ui-pos\" id=\"ut".$count++."\" onClick=\"selectItem(".$TYPE['Type']['type_id'].");\"  ".$picture." >".$TYPE['Type']['name']."</li>";
		}
		return $code;
	}
	
	function getForm(){
		$code=$this->css;
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
						$code.='<div>';
							$code.='<div class="I_order_list_product_1" id="IOLP1">1</div>';
							$code.='<div class="I_order_list_product_1" id="IOLP2">ด้ายนกยูง</div>';
							$code.='<div class="I_order_list_product_1" id="IOLP3">7.00</div>';
							$code.='<div class="I_order_list_product_1" style="width:4px;border-width:0 0 1px 0;">x</div>';
							$code.='<div class="I_order_list_product_1" id="IOLP4">12</div>';
							$code.='<div class="I_order_list_product_1" id="IOLP5">65.00</div>';
						$code.='</div>';
					$code.='</div>';
				$code.='</div>';
			$code.='</div>';
			$code.='<!-- Container category  -->';
			$code.='<div class="I_selection">';
				$code.='<div class="I_selection_category">';
					$code.='<div class="I_selection_category_head">ประเภทสินค้า</div>';
	
					$code.='<div id="I_selection_category_container">';
						$code.='<ol>';
							$code.='<li><a href="#">test</a></li>';
							$code.='<li><a href="#">test</a></li>';
							$code.='<li><a href="#">test</a></li>';
							$code.='<li><a href="#">test</a></li>';
							$code.='<li><a href="#">test</a></li>';
							$code.='<li><a href="#">test</a></li>';
							$code.='<li><a href="#">test</a></li>';
							$code.='<li><a href="#">test</a></li>';
							$code.='<li><a href="#">test</a></li>';
						$code.='</ol>';
					$code.='</div>';
				$code.='</div>';
			
				$code.='<div class="I_selection_type">';
					$code.='<div class="I_selection_type_head">รายการสินค้า</div>';
					$code.='<div id="I_selection_type_container">';
						$code.='<ol>';
							$code.='<li><a href="#">test</a></li>';
						$code.='</ol>';
					$code.='</div>';
				$code.='</div>';
				/*$code.='<div class="I_selection_item">';
					$code.='<div class="I_selection_item_head">รหัส</div>';
					$code.='<div id="I_selection_item_container"></div>';
				$code.='</div>';*/
			$code.='</div>';

		$code.='</div>';
		
		return $code;		
	}
	
}

?>