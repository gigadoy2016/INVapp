<?php
class ProductsController extends AppController{
    var $name='Product';
    var $helpers = array('Html', 'Javascript', 'Ajax');
    var $components = array('body','Promotion','Stocking','Menu');
    var $p_name=null;
    var $category_id=1;
    var $type_id=1;
    var $p_id =null;
	var $defaultLimit=null;
    
    function index(){
        $this->layout='home';
        $this->set('HTML',$this->genCodeHTML());
    }

    function findCategory($id){
        $this->layout='home';
        $this->set('HTML',$this->body->getHtml($this->content($this->Product->findProductByCategory($id))));
    }
    function findType($param){
    	$order = null;
		if(strrpos($param, ":")){
    		list($id,$order)=split(":",$param);
    		list($orderBy,$orderType) = split("=",$order);
    		$order = $orderBy." ".$orderType;
		}else{
			$id=$param;
		}
		$data=$this->Product->findSafyStock($id,$order);
		$this->defaultLimit= $this->Product->limitDefault($id);
        $this->layout='home';
        $this->set('HTML',$this->body->getHtml($this->content($data)));
    }
    
    function view($id){
        $this->layout='home';
		$data = $this->Product->findProductByProduct($id);
		$this->defaultLimit= $this->Product->limitDefault($data[0]['Product']['type_id']);
        $this->set('HTML',$this->body->getHtml($this->DisplayProduct($data)));
    }
    
    function add($id=null,$type=null){
        $this->layout='home';
        $this->body->javaScript=$this->body->getWYSIWYG();
        if(isset($type)){
        	$this->type_id=$type;
        	$data = $this->Product->findTypeByType($type);
        	$this->category_id= $data[0]['Type']['category_id'];
        }
        $this->set('HTML',$this->body->getHtml($this->formAdd($id)));
    }
    
    function update($id=null){
    	$this->body->javaScript=$this->body->getWYSIWYG();
    	$code=null;
    	
        //************ Update ************************

    	if (!empty($this->params['form'])){
        	$param = $this->params['form'];
            $param['time'] = $_SERVER['REQUEST_TIME'];
            //$param['product'] = 
            print_r($param);
			
            if ($this->Product->save($param)){
                $this->flash('ได้ update ข้อมูลลงในบัชชีสินค้าแล้ว.','/products/view/'.$param['product_id']);
            } else {
                $this->flash('พบความผิดพลาดไม่สามารถเพิ่มข้อมูลได้','/');
            }
        }
        //********************************************
    	if(!empty($id)){
    		$this->data=$this->Product->findProductByID($id);
    		$this->data=$this->data[0];
    	}else{
    		$this->layout='home';
			$this->set('HTML',$this->body->getHtml("Back"));
			return 0;
    	}
    	
        $typeData = $this->Product->findType();
        $categoryData = $this->Product->findCategory();
        $stockData =$this->Product->findStockAll();

//******** Java Script *******
$code.='<script type="text/javascript" language="javascript" charset="utf-8">
//<![CDATA[
var types = new Array();var product_name= new Array();';

for($i=0;$i<count($typeData);$i++){
	$code.='types['.$typeData[$i]['Type']['type_id'].']="'.$typeData[$i]['Type']['serial_id'].'";';
	$code.='product_name['.$typeData[$i]['Type']['type_id'].']="'.$typeData[$i]['Type']['name'].'";';
}
$code.='// ]]>';
$code.='</script>';
//*****************************

        //--------------FORM----------------
        $code .='<form method="post" action="'.HOME.'products/update/" name="form_cat">';
        $code .='<table>';
        $code .='<tr><th colspan="2">เพิ่มข้อมูลรายการสินค้าเดิม</th></tr>';
        $code.='<input type="hidden" name="id" value="'.$this->data['Product']['id'].'">';

        //===============Category======================
		$code.='<tr><td width="200px">ประเภทสินค้า</td><td>';
		$code.='<select name="category_id">';	
        foreach($categoryData as $category){
        	$code.='<option value ="'.$category['Category']['category_id'].'" ';
        	if($this->data['Product']['category_id']==$category['Category']['category_id']){
        		$code.='selected="selected" ';}
        	$code.=" >".$category['Category']['name'].' ('.$category['Category']['serial_id'].') </option>';
        }
        $code.='</select></td></tr>';
        //==============End Category======================
        //==============Select Type=======================
		$code.='<tr><td width="200px">ชื่อสินค้า</td><td>';
		$code.='<select type="hidden" name="type_id" id="type_id" onChange="showSerialID()">';
		foreach($typeData as $type){
			$code.='<option value ="'.$type['Type']['type_id'].'" ';
			if($type['Type']['type_id']==$this->data['Product']['type_id']){
				$code.='selected="selected"';
			}
			$code.='>'.$type['Type']['name'].' ('.$type['Type']['serial_id'].') </option>';
		}
		$code.='</select>';
		$code.='<br><br><input name="product" value="'.$this->data['Product']['product'].'" id="product">';
		$code.=' เพิ่มรายการสินค้าใหม่ <a href="'.HOME.'types/add/">ที่นี้</a>';
		$code.='</td></tr>';		
		//==============End Type==========================
		//=============Product Code=======================
        $code.='<tr><td width="200px">รหัสของสินค้า</td><td>';
        $code.='<input type="text" name="code" id="code" size="10" value="'.$this->data['Product']['code'].'" onChange="showSerialID()"/> ->';
        $code.='<input type="text" name="product_id" id="serial_id" value="'.$this->data['Product']['product_id'].'" readonly="readonly">';
		$code.='</td></tr>';
		//=============end Product Code===================

		//================Stock===========================
        $code.='<tr><td width="200px">ระบุคลังสินค้า</td><td>';
		$code.='<select name="stock_id" id="stock_id">';
		foreach($stockData as $stock){
			$code.='<option value ="'.$stock['Stock']['stock_id'].'" ';
			if($stock['Stock']['stock_id']==$this->data['Product']['stock_id']){
				$code.='selected="selected"';
			}
			$code.='>'.$stock['Stock']['stock_name'].' ->'.$stock['Stock']['detail'].' </option>';
		}
		$code.='</select>';
        $code.='</td></tr>';
		//================End Stock=======================
		//================Promotion=======================

		$promotions =$this->Product->findPromotion($this->data['Product']['type_id']);
		$code.='<tr><td>ระบุโปรโมชั่น</td>';
		$code.='<td>';
		/*$code.='<select name="promotion_id" id="promotion_id">';
    	foreach($promotions as $promotion){
			$code.='<option value ="'.$promotion['Promotion']['promotion_id'].'" ';
			if($promotion['Promotion']['promotion_id']==$this->data['Product']['promotion_id']){
				$code.='selected="selected"';
			}
			$code.='>'.$promotion['Promotion']['promotion_id'].' ('.$promotion['Promotion']['price'].') </option>';
		}	
		$code.='</select>';*/
		$code.='</td></tr>';
		
        //====================Other=======================   
		$code.='<script>';
		$code.='function productAdd(){';
		$code.='document.form_cat.quantity.value= parseInt(document.form_cat.quantity.value) + parseInt(document.form_cat.typeAdd.value * document.form_cat.quantityAdd.value)';
		$code.='}';
		$code.='</script>';
        $code.='<tr><td width="200px">จำนวนสินค้าทั้งหมด</td><td><input type="text" name="quantity" size="10" value="'.$this->data['Product']['quantity'].'" onclick="focus();select();"> ชิ้น   ';
		$code.='+ <input type="text" name="quantityAdd" id="quantityAdd" size="3" value="1" OnChange="productAdd();select()" onclick="focus();select();"> ';
		$code.='<select name="typeAdd" id="typeAdd">';
		$code.='<option value="1">ชิ้น.</opion>';
		$code.='<option value="12">โหล.</opion>';
		$code.='</select>';
		$code.='</td></tr>';
        $code.='<tr><td width="200px">ราคาทุน</td><td><input type="text" name="cost" size="10" value="'.$this->data['Product']['cost'].'">บาท</td></tr>';
        $code.='<tr><td width="200px">ราคาขาย</td><td><input type="text" name="sale_price" size="10" value="'.$this->data['Product']['sale_price'].'">บาท</td></tr>';
        $code.='<tr><td width="200px" valign="top">รายละเอียด</td><td><textarea name="detail" rows="10" cols="40" >'.$this->data['Product']['detail'].'</textarea></td></tr>';
        $code.='<tr><td width="400px" align="center" colspan="2"><input type="submit" name="submit" value="แก้ไข"/> <input type="reset" value="clear"></td></tr>';
		//================================================
		
		$code .='</table>';
		$code .='</form>';
		//------------FORM------------------        
    	$this->layout='home';
		$this->set('HTML',$this->body->getHtml($code));
    }

    function delete($id=null){
   		$this->Product->del($id);
    	$this->flash('The product with id: '.$id.' has been deleted.', '/');
    	
    }
    function genCodeHTML(){
        return $this->body->getHtml($this->content($this->Product->findProductAll()));
    }
    
    /*
     * Display content
	*/
    function content($DATA){
      if(empty($DATA)){
      	return "ไม่มีสินค้า";
      }
	  
      //print_r($DATA);
        $code = "<table width=\"100%\">";
	        $code.="<tr><td colspan='3'>";
    		    $code.="<div id='h_category'><a href='".HOME."types/find/".$DATA[0]['Category']['name']."'>".$DATA[0]['Category']['name']."</a>:</div>";
        		$code.="<div id='h_type'>".$DATA[0]['Product']['product']."</div></td>";
				$code.="<td> <a href='".HOME."products/checkStock/".$DATA[0]['Product']['type_id']."'>ตรวจ stock</a></td>";
        		$code.='<td align="right"><a href="'.HOME.'products/add/'.$DATA[0]['Product']['category_id'].'/'.$DATA[0]['Product']['type_id'].'">เพิ่มสินค้า</a></td></tr>';
	        $code.="<tr><th width='120px'>".$this->menu->listOrder("no.","Product.product_id",$DATA[0]['Product']['type_id'])."</th>";
        $code.="<th>สินค้า</th>";
        $code.="<th width='80px'>".$this->menu->listOrder("รหัส","Product.code",$DATA[0]['Product']['type_id'])."</th>";
        $code.="<th width='80px'>".$this->menu->listOrder("จำนวน","q_number",$DATA[0]['Product']['type_id'])."</th>";
        $code.="<th width='80px'>".$this->menu->listOrder("เวลา","utime",$DATA[0]['Product']['type_id'])."</th>";
        $code.="</tr>";
        
        foreach($DATA as $datas){
        	
            $code.= "<tr>";
            $code.= "<td>".$datas['Product']['product_id']."</td>";
            $code.= "<td><div class='p01'><a href='".HOME."products/view/".$datas['Product']['product_id']."'>".$datas['Product']['product']."</a></div>";
			$code.="</td>";
            $code.= "<td><a href='".HOME."products/view/".$datas['Product']['product_id']."'>".$datas['Product']['code']."</a></td>";
            if(!empty($datas[0]['q_number'])){
            	//$code.= "<td>".$this->menu->quntityAlert($datas[0]['q_number'])."</td>";
				$code.= "<td>".$this->menu->safetyStock($datas[0]['q_number'],$datas['0']['alert'],$this->defaultLimit)."</td>";
            }else{
            	//$code.= "<td>".$this->menu->quntityAlert($datas['Product']['quantity'])."</td>";
				$code.= "<td>".$this->menu->safetyStock($datas['Product']['quantity'],$datas['0']['alert'],$this->defaultLimit)."</td>";
            }
            $code.= "<td><div class='showTime'>".date("d/M/y H:i",$datas[0]['utime'])."</div></td>";
            $code.= "</tr>";
        }
        $code.='<tr><td colspan="5" align="right"><a href="'.HOME.'products/add/'.$DATA[0]['Product']['category_id'].'&type='.$DATA[0]['Product']['type_id'].'">เพิ่มสินค้า</a></td></tr>';
        $code.="</table>";
        return $code;
    }
    
    function DisplayProduct($data){

    $code= "<br><div style='float:left'>รายการสินค้า</div></br>";
	$code.="<div style='float:right;margin:0 15px 1px 0'>";
		$code.="<div style='float:right;margin-right:5px'> <a href='".HOME."products/limitEdit/".$data[0]['Product']['product_id']."'>Limit</a></div>";
		$code.="<div style='float:right;margin-right:5px'>Report.</div>";
	$code.="</div>";
	$code.="<div style='float:left;width:100%'>";
    foreach($data as $datas){
        $code .="<table width='100%' border='1'>";
        $code .="<tr><td colspan='2'><div class='pd01'><a href='".HOME."products/findType/".$datas['Product']['type_id']."'>".$datas['Product']['product']."</a></div>";
        $code .="<div class='pd02'>";
        $code .="<a href='".HOME."products/add/".$datas['Product']['category_id']."&type=".$datas['Product']['type_id']."'>เพิ่มสินค้า</a> ";
        $code .="<a href='".HOME."products/update/".$datas['Product']['id']."'>แก้ไข</a> ";
        $code .="<a href='".HOME."products/delete/".$datas['Product']['id']."' onclick='return delConfirm();'>ลบ</a> ";
        $code .="</div>";
        $code .="</td></tr>";
        $code .="<tr><th width='200px'>รหัสสินค้า</th><td>".$datas['Product']['product_id']."</td></tr>";
        $code .="<tr><th width='200px'>รหัส</th><td>".$datas['Product']['code']."</td></tr>";
        $code .="<tr><th width='200px'>English Name</th><td>".$datas['Type']['eng_name']."</td></tr>";
        $code .="<tr><th width='200px'>ประเภทสินค้า</th><td><a href='".HOME."types/find/".$datas['Category']['name']."'>".$datas['Category']['name']."</a></td></tr>";
        $code .="<tr><th width='200px' valign='top'>ราคาขายปลีก</th>";
        $code .="<td>";
        $code .="<div style='float:left;'>จำนวน <input type='text' value='1' size='2' onclick='select();' onChange='document.getElementById(\"r_prize\").innerHTML=(this.value*".$datas['Product']['sale_price'].")'> * ".$datas['Product']['sale_price']." บาท = </div>";
        $code .="<div id='r_prize' style='float:left;'>".$datas['Product']['sale_price']."</div>";
        $code .="<div style='clear:left'></div>";
        $code .="</td></tr>";
        $code .="<tr><th width='200px' valign='top'>คลังสินค้า</th>";
        $code .="<td><a href='".HOME."stocks/view/".$datas['Stock']['stock_id']."'>".$datas['Stock']['stock_name']." -- ".$datas['Stock']['detail']."</a></td></tr>";
        $code .="<tr><th width='200px'>จำนวนสินค้า</th><td>".$this->menu->safetyStock($datas['Product']['quantity'],$datas[0]['alert'],$this->defaultLimit)." ".$datas['Product']['unit']."</td></tr>";
        $code .="<tr><th width='200px' valign='top'>รายละเอียด</th><td><span>".$datas['Type']['detail']." : </span><span>".$datas['Product']['detail']."</span></td></tr>";
        $code .="<tr><th width='200px'>update</th><td>".date("H:i d/M/y",$datas['Product']['time'])."</td></tr>";
        $code .="</table><br/>";
    }
    	$code.="</div>";
    return $code;
}
    
    function formAdd($id=null,$type_id=null){
		if(!empty($id))$this->category_id = $id;
    	$code=null;
        if (!empty($this->params['form'])){
            $param = $this->params['form'];
            $param['time'] = $_SERVER['REQUEST_TIME'];
            //$param['product'] = 
            //print_r($param);
			
            if ($this->Product->save($param)){
                $this->flash('เพิ่มข้อมูลลงในบัชชีสินค้าแล้ว.','/products/findType/'.$param['type_id']);
            } else {
                $this->flash('พบความผิดพลาดไม่สามารถเพิ่มข้อมูลได้','/');
            }
        }
        $typeData = $this->Product->findTypeByCategory($this->category_id);
        $categoryData = $this->Product->findCategory();

//******** Java Script *******
$code.='<script type="text/javascript" language="javascript" charset="utf-8">
//<![CDATA[
var types = new Array();var product_name= new Array();';

for($i=0;$i<count($typeData);$i++){
	$code.='types['.$typeData[$i]['Type']['type_id'].']="'.$typeData[$i]['Type']['serial_id'].'";';
	$code.='product_name['.$typeData[$i]['Type']['type_id'].']="'.$typeData[$i]['Type']['name'].'";';
}
$code.='// ]]>';
$code.='</script>';
//*****************************
		
        //------------Form--------------
        $code .='<form method="post" action="'.HOME.'products/add/" name="form_cat">';
        $code .='<table>';
        $code .='<tr><th colspan="2">เพิ่มข้อมูลรายการสินค้าเดิม</th></tr>';
        
        //===============Category======================
		$code.='<tr><td width="200px">ประเภทสินค้า</td><td>';
		$code.='<select name="category_id" onchange="window.location.replace(\''.HOME.'products/add/\'+this.value);">';	
        if(!empty($id)){
        	//select Category
        	foreach($categoryData as $category){
        		$code.='<option value ="'.$category['Category']['category_id'].'" ';
        		if($id==$category['Category']['category_id']){
        			$code.='selected="selected" ';}
        		$code.=" >".$category['Category']['name'].' ('.$category['Category']['serial_id'].') </option>';
        	}
        	$code.='</select></td></tr>';
        }else{
        	//select Category
        	foreach($categoryData as $category){
            	$code.='<option value ="'.$category['Category']['category_id'].'">'.$category['Category']['name'].' ('.$category['Category']['serial_id'].') </option>';
        	}
        	$code.='</select></td></tr>';
        }
        //==============End Category======================

        //==============Select Type=======================
		$code.='<tr><td width="200px">ชื่อสินค้า</td><td>';
		$code.='<select name="type_id" id="type_id" onChange="showSerialID()">';
		foreach($typeData as $type){
			$code.='<option value ="'.$type['Type']['type_id'].'" ';
			if($type['Type']['type_id']==$this->type_id){
				$code.='selected="selected"';
			}
			$code.='>'.$type['Type']['name'].' ('.$type['Type']['serial_id'].') </option>';
		}
		$code.='</select>';
		$code.='<input type="hidden" name="product" value="" id="product">';
		$code.=' เพิ่มรายการสินค้าใหม่ <a href="'.HOME.'types/add/">ที่นี้</a>';
		$code.='</td></tr>';		
		//==============End Type==========================
 
		//=============Product Code=======================
        $code.='<tr><td width="200px">รหัสของสินค้า</td><td>';
        $code.='<input type="text" name="code" id="code" size="10" onChange="showSerialID()"/> ->';
        $code.='<input type="text" name="product_id" id="serial_id" value="" readonly="readonly">';
		$code.='</td></tr>';
		//=============end Product Code===================
		//================Promotion=======================

		$promotions =$this->Product->findPromotion($this->data['Product']['type_id']);
		$code.='<tr><td>ระบุโปรโมชั่น</td>';
		$code.='<td>';
		/*$code.='<select name="promotion_id" id="promotion_id">';
    	foreach($promotions as $promotion){
			$code.='<option value ="'.$promotion['Promotion']['promotion_id'].'" ';
			if($promotion['Promotion']['promotion_id']==$this->data['Product']['promotion_id']){
				$code.='selected="selected"';
			}
			$code.='>'.$promotion['Promotion']['promotion_id'].' ('.$promotion['Promotion']['price'].') </option>';
		}	
		$code.='</select>';*/
		$code.='</td></tr>';
		
		//====================Other=======================
        //$code.='<tr><td width="200px">ระบุคลังสินค้า</td>';
        //$code.='<td>'.$this->Stocking->selectFrom($this->Product->findStockAll()).'</td></tr>';
		$code.='<tr><td width="200px">ระบุคลังสินค้า</td><td>'.$this->Stocking->iconSelect($this->Product->findStockAll(),'stock_id','stock_name','detail').'</td></tr>';
        $code.='<tr><td width="200px">จำนวนสินค้าทั้งหมด</td><td><input type="text" name="quantity" size="10"> ชิ้น</td></tr>';
        $code.='<tr><td width="200px">ราคาทุน</td><td><input type="text" name="cost" size="10">บาท</td></tr>';
        $code.='<tr><td width="200px">ราคาขาย</td><td><input type="text" name="sale_price" size="10">บาท</td></tr>';
        $code.='<tr><td width="200px" valign="top">รายละเอียด</td><td><textarea name="detail" rows="10" cols="40"></textarea></td></tr>';
        $code.='<tr><td width="400px" align="center" colspan="2"><input type="submit" name="submit" value="เพิ่มข้อมูล"/> <input type="reset" value="clear"></td></tr>';
		//================================================
        $code .='</table>';
        $code.='</form>';
        //-----------------End Form------------------
        
        return $code;
    }
	public function limitEdit($id){
		$this->layout='home';

		if(!empty($_POST)){
			$data = $this->Product->limitDefault($id);
			if(count($data)<=0){
				//ทำการ Insert ข้อมูล
				$this->Product->insertLimit($id,$_POST);
				$this->flash('เพิ่มเรียบร้อยแล้ว','/products/limitEdit/'.$id);
				exit;
			}else{
				//ทำการUpdate ข้อมูล
				$this->Product->updateLimit($id,$_POST);
				$this->flash('แก้ไขเรียบร้อยแล้ว','/products/limitEdit/'.$id);
				exit;
			}
		}else{
			$data = $this->Product->limitDefault($id);
		}
		
		if(count($data)<=0){
			$types = $this->Product->findProductByProduct($id);
			$data = $this->Product->limitDefault($types[0]['Type']['type_id']);
		}
		
		$code ='<form method="post" action="" name="limit">';
		$code.="<div style='margin:12px 0 2px 12px'><b>Safey Stock.</b>(<a href='".HOME."products/view/".$id."'>back</a>)</div>";

		if(empty($data)){
			$code.="<div style='margin:50px 0 0 50px'>ยังไม่ได้กำหนด (<a href='".HOME."products/addLimit/".$types[0]['Type']['type_id']."'>กำหนดใหม่</a>)</div>";
		}else{
			$code.="<div style='float:left;width:602px;border:1px solid #7F7F7F;margin:30px 0 0 30px;height:50px'>";
				$code.="<div style='float:left;width:200px;border-right:1px solid #7F7F7F;text-align:center;height:50px;background-color:red;'><< <input type='text' value='".$data['D']['min']."' size='3' name='min'></div>";
				$code.="<div style='float:left;width:200px;text-align:center;height:50px;background-color:yellow;'>".$data['D']['min']." <-> ".$data['D']['max']." </div>";
				$code.="<div style='float:left;width:200px;border-left:1px solid #7F7F7F;text-align:center;height:50px;background-color:green;'><input type='text' value='".$data['D']['max']."' size='3' name='max'> >> </div>";
			$code.="</div>";
		}
		$code.="<div style='float:left;width:602px;margin:10px 0 0 30px;text-align:right'>";
		$code.="<input type='submit' name='submit' value='submit'>";
		$code.="</div>";
		$code.="</form>";
		$this->set('HTML',$this->body->getHtml($code));
	}
	public function checkStock($type_id){
		$this->layout='home';
		$codition =' WHERE Data.alert <= 1 ORDER by Data.alert,Data.code ASC';
		
		$datas = $this->Product->dataAlert($type_id,$codition);
		//Part แรก
		$code ='<div>';
			$code.='<table>';
				$code.='<tr>';
					$code.='<td><input type="submit" value="" style="background-color: red;width:100%"></td>';
					$code.='<td><input type="submit" value="" style="background-color: yellow;width:100%"></td>';
					$code.='<td><input type="submit" value="" style="background-color: green;width:100%"></td>';
				$code.='</tr>';
			$code.='</table>';
		$code.='</div>';
		$code.='<div style="width:400px">';
			$code.='<table>';
				$code.='<tr>';
					$code.='<tr><th colspan="4">'.$datas[0]['Data']['name'].'</th></tr>';
					$code.='<th>no.</th><th>Code</th><th>จำนวน</th><th>จำนวนขาด</th>';
				$code.='</tr>';
			$i=0;
			foreach($datas as $data){
				$save = $data['Data']['max']-$data['Data']['quantity'];
				$code.='<tr>';
					$code.='<td>'.++$i.'</td>';
					$code.='<td><a href="'.HOME.'products/view/'.$data['Data']['product_id'].'">'.$data['Data']['code'].'</a></td>';
					$code.='<td>'.$data['Data']['quantity'].'</td>';
					$code.='<td>'.$this->menu->safetyStock($save,$data['Data']['alert'],$this->defaultLimit).'</td>';
				$code.='</tr>';
			}
			$code.='</table>';
		$code.='</div>';
		
		$this->set('HTML',$this->body->getHtml($code));
	}
	public function changed($type_id){
		$this->layout='home';
		$this->set('stocks',"");
		$dataIn = null;
		$dataOut =null;
		
		if(empty($_POST)){
			return 0;
		}
		
		$stockIn 	= $_POST['in'];
		$stockOut	= $_POST['out'];
		$n			= $_POST['n'];
		$m			= $_POST['m'];
		
		
		$inStock = $outStock = '';
		
		for($i=0 ;$i < count($stockIn) ; $i++){
			if($stockIn[$i] != ''){
				$product_id 	= $type_id.$stockIn[$i];
				$sqlIN 			=$this->Product->addQuality( $product_id , $n[$i] );
				$this->Product->queryProduct($sqlIN);
				if($inStock !=''){ 
					$inStock .=" OR ";
				}
				$inStock 		.=" product_id = '".$product_id."' ";
			}
		}

		for($i=0 ;$i < count($stockOut) ; $i++){
			if($stockOut[$i] != ''){
				$product_id 	= $type_id.$stockOut[$i];
				$sqlOUT 		=$this->Product->addQuality($product_id, "(-".$m[$i].")" );
				$this->Product->queryProduct($sqlOUT);
				if($outStock !=''){ 
					$outStock .=" OR ";
				}
				$outStock 		.=" product_id = '".$product_id."' ";
			}
		}
		
		if($inStock != '' ){
			$inStock 	= "SELECT * FROM `inv_products`  WHERE ".$inStock; 
			$dataIn 	= $this->Product->queryProduct($inStock);
		}
		if($outStock !='' ){
			$outStock	="SELECT * FROM `inv_products`  WHERE ".$outStock;
			$dataOut 	= $this->Product->queryProduct($outStock);
		}
		$stockIn	=$this->showStock_changed($dataIn,"นำเข้าคลังสินค้า");
		$stockOut	=$this->showStock_changed($dataOut,"นำออกจากคลังสินค้า");
		$this->set('stocks',$stockIn."<br>".$stockOut);
	}
	
	function showStock_changed($data,$label){
		//print_r($data);
		if(empty($data)){
			return "";
		}
		$text 	="<table class='stock'>";
		$text		.="<tr><th width='250px' align='left'>".$label."</th></tr>";
		foreach($data as $items){
			$text	.="<tr><td >".$items['inv_products']['product']."[ ".$items['inv_products']['code']." ] </td><td width='35px'>..... x </td><td align='right' width='100px'>".$items['inv_products']['quantity']." ชิ้น</td></tr>";
		}
		$text		.="</table>";
		
		return $text;
	}
}
?>