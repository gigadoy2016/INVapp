<?php
class ProductingComponent extends Object{
    var $name = 'Producting';
    var $controller = true;
    var $components = array('Menu');
    
    function listInStock($DATA){
        $code ="";
        $code .='<table>';
        if(count($DATA)!=0){
            $code .='<tr><th>'.$this->orderList("รายการ","Product.code",$DATA[0]['Product']['stock_id']).'</th>';
            $code .='<th width="100px">'.$this->orderList("จำนวน","Product.quantity",$DATA[0]['Product']['stock_id']).'</th></tr>';
            foreach($DATA as $datas){
                $code .="<tr><td>";
                $code .="<a href='".HOME."products/view/".$datas['Product']['product_id']."'>".$datas['Product']['product']."(".$datas['Product']['code'].")</a>";
                $code .="</td><td>".$this->Menu->quntityAlert($datas['Product']['quantity']);
                $code .="</td></tr>";
            }
        }else{
            $code .="<td>ไม่พบสินค้าในคลังสินค้า  <a href='".HOME."products/add'>เพิ่มสินค้า</a></td>";
        }
        $code .="</table>";
        return $code;
    }
    
    function listInStock2($DATA){
        $code ="";
        $code .='<table>';
        if(count($DATA)!=0){
            $code .='<tr><th>'.$this->orderList("รายการ","Product.product_id",$DATA[0]['Product']['stock_id']).'</th>';
            $code .='<th width="100px">'.$this->orderList("จำนวน","Product.quantity",$DATA[0]['Product']['stock_id']).'</th></tr>';
            foreach($DATA as $datas){
                $code .="<tr><td>";
                $code .="<a href='".HOME."products/view/".$datas['Product']['product_id']."'>".$datas['Product']['product']."(".$datas['Product']['code'].")</a>";
                $code .="</td><td>".$this->Menu->quntityAlert($datas['Product']['quantity']);
                $code .="</td></tr>";
            }
        }else{
            $code .="<td>ไม่พบสินค้าในคลังสินค้า  <a href='".HOME."products/add'>เพิ่มสินค้า</a></td>";
        }
        $code .="</table>";
        return $code;
    }
    
    function orderList($caption,$order,$parameter){
    	$parameter.="&order=".$order."&t=";
    	
		$code ='<span style="width:70%">'.$caption.'</span>';
    	$code.='<span style="width:30%;text-align:right">';
    	$code.='<a href="'.$parameter.'ASC"><img src="'.HOME.'img/ico_down.gif"/></a> ';
    	$code.='<a href="'.$parameter.'DESC"><img src="'.HOME.'img/ico_up.gif"/></a>';
    	$code.='</span>';
    	return $code;
    }
}
?>