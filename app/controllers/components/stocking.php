<?php
class StockingComponent extends Object{
    var $name = 'Stocking';
    var $controller = true;
    var $components = array('Err');
    
    var $width='100%';
    var $bgColor='#000';
    var $table_name='Stock';
    
    
    function listStock($data){
        $code ="<table>";
        $code .="<tr><td colspan='2'>คลังสินค้า</td></tr>";
        $code .="<tr><th>ตำแหน่ง</th><th>จำนวน</th></tr>";
        foreach($data as $datas){
           $code .="<tr><td>".$datas['Stock']['stock_name']."</td><td></td></tr>";
        }
        $code .="</table>";
        return $code;
    }
    function selectFrom($data = null){
        $code ="<select name='stock_id'>";
        foreach($data as $datas){
            $code.="<option value ='".$datas['Stock']['stock_id']."'>".$datas['Stock']['stock_name']." - ".$datas['Stock']['detail']."</option>";
        }
        $code.="</select>";
        
        return $code;
    }
    
	function iconSelect($data,$id,$caption,$detail){
		if(empty($data)){
			return $this->Err->noData;
		}else{
			//CSS
			$code ='<style>';
			$code.='.iconMain{width:'.$this->width.';height:230px;margin:0;padding:2px;overflow: scroll;}';
			$code.='.iconSub01{width:100px;height:100px;background-color:#FCF8DC;text-align:center;float:left;margin:2px;border:1px dashed gray;}';
			$code.='</style>';

			$code.='<div class="iconMain">';
			foreach($data as $datas){
				$code.='<div class="iconSub01" onclick=\'document.getElementById("radioS'.$datas[$this->table_name][$id].'").checked=true;\'>';
				$code.='<input type="radio" name="'.$id.'" value="'.$datas[$this->table_name][$id].'" id="radioS'.$datas[$this->table_name][$id].'">';
				$code.='<b>'.$datas[$this->table_name][$caption].'</b> '.$datas[$this->table_name][$detail];
				$code.='</div>';
			}
			$code.='</div>';
		}
		return $code;
	}
}
?>