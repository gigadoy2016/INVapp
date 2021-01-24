<?php
/*
 * Component:Menu
 * Ver :0.1 Alpha
 * Date :20071120
 */
class MenuComponent extends Object{
    var $name = 'Menu';
    var $controller = true;
	
    function getHtml(){
        $menu ='<div id="menu">';
        $menu .='<ol><b>รายการ</b>';
        $menu .='<li><a href="'.HOME.'categories">ประเภทสินค้า</a></li>';
        $menu .='<li><a href="'.HOME.'types">สินค้าทั้งหมด</a></li>';
        $menu .='<li><a href="'.HOME.'stocks">สินค้าในคลังสินค้า</a></li>';
		$menu .='<li><a href="'.HOME.'types/promotion">Promotions.</a></li>';
        $menu .='</ol>';
        
        $menu.='<ol><b>รายการเพิ่ม</b>';
        $menu.='<li><a href="'.HOME.'products/add">เพิ่มรายการสินค้า</a></li>';
        $menu.='<li><a href="'.HOME.'types/add">เพิ่มรายการสินค้าใหม่</a></li>';
        $menu.='<li><a href="'.HOME.'stocks/add">เพิ่มคลังสินค้า</a></li>';
        $menu.='</ol>';
        $menu.='</div>';
        return $menu;
    }
    
    function selectFrom($data){
        $code ="<select name='stock_id'>";
        foreach($data as $datas){
            $code.="<option value ='".$datas['Stock']['stock_id']."'>".$datas['Stock']['stock_name']." - ".$datas['Stock']['detail']."</option>";
        }
        $code.="</select>";
        
        return $code;
    }
    function tagSelect($name_id,$data,$id=null,$selected=null){
    	
    	if(!empty($id)){
    		$code='<select name="'.$name_id.'" id="'.$id.'">';
    	}else{
    		$code='<select name="'.$name_id.'">';
    	}
    	
        foreach($data as $datas){
            $code.='<option value ="'.$datas['Category']['category_id'].'">'.$datas['Category']['name'].' ('.$datas['Category']['serial_id'].') </option>';
        }
    }
    function listOrder($caption,$orderBy,$parameter){
    	$url=HOME.'products/findType/'.$parameter.':'.$orderBy;
    	
    	$code ='<span style="width:70%">'.$caption.'</span>';
    	$code.='<span style="width:30%"><a href="'.$url.'=ASC"><img src="'.HOME.'img/ico_down.gif"/></a> <a href="'.$url.'=DESC"><img src="'.HOME.'img/ico_up.gif"/></a></span>';
    	return $code;
    }
    
    function orderList($caption,$order,$parameter){
    	$parameter.="&order=".$order."&t=";
    	
		$code ='<span style="width:70%">'.$caption.'</span>';
    	$code.='<span style="width:30%">';
    	$code.='<a href="'.$parameter.'ASC"><img src="'.HOME.'img/ico_down.gif"/></a> ';
    	$code.='<a href="'.$parameter.'DESC"><img src="'.HOME.'img/ico_up.gif"/></a>';
    	$code.='</span>';
    	return $code;
    }
    
    function quntityAlert($number){

    	if($number<12){
    		return $code = '<div class="less12">'.$number.'</div>';
    	}else if($number<24){
    		return $code = '<div class="less24">'.$number.'</div>';
    	}else if($number<36){
    		return $code = '<div class="less36">'.$number.'</div>';
    	}else if($number<48){
    		return $code = '<div class="less48">'.$number.'</div>';	
    	}else{
    		return $code = '<div class="more">'.$number.'</div>';
    	}
    }
	function safetyStock($number,$level,$default){
		if($level >=0){
			switch ($level){
				case 0:
					$code = '<div class="less12">'.$number.'</div>';
					break;
				case 1:
					$code = '<div class="less24">'.$number.'</div>';
					break;
				case 2:
					$code = '<div class="less36">'.$number.'</div>';
					break;
			} 
		}else{
			if(count($default)>0){
				if($number <=$default['D']['min'] ){
					$code = '<div class="less12">'.$number.'</div>';
				}else if($number > $default['D']['min'] && $number < $default['D']['max']){
					$code = '<div class="less24">'.$number.'</div>';
				}else{
					$code = '<div class="less36">'.$number.'</div>';
				}
			}else{
				$code = '<div>'.$number.'</div>';
			}
		}
		return $code;
	}
}
?>