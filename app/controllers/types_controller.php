<?php
class TypesController extends AppController{
    var $name='Type';
    var $helpers = array('Html', 'Javascript', 'Ajax');
    var $components = array('body','Step');

    function index(){
        $this->layout='home';
        $this->set('HTML',$this->body->getHtml($this->contentAll($this->Type->findTypeAll())));
    }
    function find($id){ 
        $this->layout='home';
        $this->set('HTML',$this->body->getHtml($this->content($this->Type->findTypeByCategory($id))));
    }
	function find1($id){
        $this->layout='home';
        $this->set('HTML',$this->body->getHtml($this->content($this->Type->findTypeByCategoryid($id))));		
	}
    function eng($id){
    	$this->layout='clear';
    	$data=$this->Type->findType($id);
    	$this->set('HTML',$data[0]['Type']['eng_name']."|");
    }
    function add($cat_id=null){
    	$this->layout='home';
    	$this->set('HTML',$this->body->getHtml($this->formAdd($cat_id)));
    }

    function content($DATA){
    	if(count($DATA)==0){
    		return "ไม่พบข้อมูล";
    	}
		$stepUpDown = new $this->Step();
		$stepUpDown->url = HOME."types/step";
		$stepUpDown->setNumber(count($DATA));

		
        $code = "<table>";
        $code .='<tr><th colspan="3">';
        $code .='<div style="width:400px;float:left">รายการสินค้า'.$DATA[0]['Category']['name'].'</div>';
        $code .='</th></tr>';
        $code .="<tr><td>no.</td><td>สินค้า</td><td>ประเภท</td></tr>";
		
		$i=0;
        foreach($DATA as $datas){
            $code.= "<tr>";
            $code.= "<td>".$datas['Type']['serial_id']."</td>";
            $code.= "<td>";
				$code.="<div style='float:left;width:100px;'>";
            	if($datas['Type']['pic']!= null)
					$code.= '<a href="'.HOME.'products/findType/'.$datas['Type']['type_id'].'"><img src="'.HOME.$datas['Type']['pic'].'" height="50px"/></a>';
            	$code.= '</div>';
            	$code.= '<div style="float:left">';
            		$code.= "<a href='".HOME."products/findType/".$datas['Type']['type_id']."'>".$datas['Type']['name']."</a>";
            	$code.= "</div>";
//step			
				$code.= $stepUpDown->getStep($datas['Type'],$i++);
			$code.= "</td>";
            $code.= "<td>";		
				$code.= "<div style='float:left'>";
					$code.= "<a href='".HOME."types/find/".$datas['Category']['name']."'>".$datas['Category']['name']."</a>";
				$code.= "</div>";
//config
				$code.= "<div style='float:right;'>";
					$code.= "<a href='".HOME."types/edit/".$datas['Type']['type_id']."' title='แก้ไข'><img src='".HOME."img/config.png'></a>";
				$code.= "</div>";

			$code.= "</td>";
            $code.= "</tr>";
        }
        $code.="</table>";
        return $code;
    }
    function contentAll($DATA){
        $code = "<table border=\"1\" width=\"100%\">";
        $code .="<tr><th colspan='3'>รายการสินค้าทั้งหมด</th></tr>";
        $code .="<tr><td>no.</td><td>สินค้า</td><td>ประเภท</td></tr>";
        foreach($DATA as $datas){
            $code.= "<tr>";
            $code.= "<td>".$datas['Type']['serial_id']."</td>";
            $code.= "<td><span style='width:100px'>";
            if($datas['Type']['pic']!= null)
				$code.= '<a href="'.HOME.'products/findType/'.$datas['Type']['type_id'].'"><img src="'.HOME.$datas['Type']['pic'].'" height="50px"/></a>';
            $code.= '</span>';
            $code.= '<span>';
            $code.= "<a href='".HOME."products/findType/".$datas['Type']['type_id']."'>".$datas['Type']['name'];            
            $code.= "</span></td>";
            $code.= "<td>";
				$code.= "<div style='float:right'>";
					$code.= "<a href='".HOME."types/edit/".$datas['Type']['type_id']."'><img src='".HOME."img/config.png'></a>";
				$code.= "</div>";				
				$code.= "<div>";
					$code.= "<a href='".HOME."types/find/".$datas['Category']['name']."'>".$datas['Category']['name']."</a>";
				$code.= "</div>";
			$code.= "</td>";
            $code.= "</tr>";
        }
        $code.="</table>";
        return $code;
    }
	function formAdd($cat_id=null){
		if (!empty($this->params['form'])){
            $param = $this->params['form'];
            //print_r($param);
            if ($this->Type->save($param)){
                $this->flash('เพิ่มข้อมูลลงในบัชชีสินค้าแล้ว.','/types/');
            } else {
				print_r($param);
                $this->flash('พบความผิดพลาดไม่สามารถเพิ่มข้อมูลได้','/',20);
            }
        }		
		$code="";
		$code.='<form method="post" action="'.HOME.'types/add/" name="form_type">';
		$code.="<table border=\"1\" width=\"100%\">";
		$code.="<tr><th colspan='2'>เพิ่มสินค้าใหม่</th></tr>";
		$code.="<tr><td width='200px'>ประเภทสินค้า</td><td>";
		$code.='<select name="category_id" onchange=\'location.assign("'.HOME.'types/add/"+this.value)\'>';
        foreach($this->Type->findCategory() as $datas){
            $code.='<option value ="'.$datas['Category']['category_id'].'" ';
			if($cat_id == $datas['Category']['category_id'])
				$code.=' selected="selected" ';
			$code.='>'.$datas['Category']['name'].' ('.$datas['Category']['serial_id'].') </option>';
        }
        $code.='</select></td></tr>';
		$code.="</td></tr>";
		$code.="<tr><td width='200px'>ชื่อสินค้าใหม่</td><td><input type='text' name='name'></td></tr>";
		$code.="<tr><td width='200px'>English name</td><td><input type='text' name='eng_name'></td></tr>";
		$code.="<tr><td width='200px'>ตั้งรหัสสินค้า</td><td><input type='text' name='serial_id' id='serial_id' value=''></td></tr>";
		$code.="<tr><td width='200px'>ตั้งราคาขาย</td><td><input type='text' name='sale_price' id='sale_price' value='0' size='5'></td></tr>";
		$code.="<tr><td width='200px'>รายละเอียด</td><td><textarea name=\"detail\" rows=\"10\" cols=\"40\"></textarea></td></tr>";
		$code.="<tr><td width='200px'>ใส่รูปภาพ (ใส่เป็นชื่อ File)</td><td><input type='text' name='pic' id='pic' value='img/products/'></td></tr>";
		$code.="<tr><td width=\"400px\" align=\"center\" colspan=\"2\"><input type='submit' name='submit' value='submit'> <input type='reset' value='reset'></td></tr>";		
		$code.="<input type='hidden' id='order' name='order' value='99'>";
		$code.="</table></form>";
		$code.="<br/>";
		$code.=$this->contentAll($this->Type->findTypeAll($cat_id));
		return $code;
	}
	
	public function edit($type_id){
		if (!empty($this->params['form'])){
            $param = $this->params['form'];
            if ($this->Type->update($this->params['form'])){
                $this->flash('ได้ทำการเรียบร้อย','/types/edit/'.$type_id);
            }
        }
		$data = $this->Type->findType($type_id);
		$data = $data[0];

		$code="";
		$code.='<form method="post" action="'.HOME.'types/edit/'.$type_id.'" name="form_type">';
		$code.="<table border=\"1\" width=\"100%\">";
		$code.="<tr><th colspan='2'>";
			$code.="<div>แก้ไข Type</div>";
			$code.="<div style='float:right'><a href='".HOME."types/delete/".$type_id."/".$data['Category']['name']."'><img src=".HOME."img/remove.png title='ลบ'></a></div>";
			$code.="<div style='float:right;margin-right:5px'><a href='".HOME."types/addPro/".$type_id."/".$data['Type']['type_id']."'><img src='".HOME."img/icon_promotion_1.gif' height='40px'></a></div>";
			$code.="</th></tr>";
		$code.="<tr><td width='200px'>ประเภทสินค้า</td><td><div>".$data['Category']['name']."</div></td>";
		$code.='</td></tr>';
		$code.="</td></tr>";
		$code.="<tr><td width='200px'>ชื่อ TYPE</td><td><input type='text' name='name' value='".$data['Type']['name']."'><input type='hidden' id='type_id' name='type_id' value='".$type_id."'></td></tr>";
		$code.="<tr><td width='200px'>English name</td><td><input type='text' name='eng_name' value='".$data['Type']['eng_name']."'></td></tr>";
		$code.="<tr><td width='200px'>ตั้งรหัสสินค้า</td><td><input type='text' name='serial_id' id='serial_id' value='".$data['Type']['serial_id']."'></td></tr>";
		$code.="<tr><td width='200px'>ตั้งราคาขาย</td><td><input type='text' name='sale_price' id='sale_price' value='".$data['Type']['sale_price']."' size='5'></td></tr>";
		$code.="<tr><td width='200px'>ตั้งราคาทุน</td><td><input type='text' name='cost' id='cost' value='".$data['Type']['cost']."' size='5'></td></tr>";
		$code.="<tr><td width='200px'>รายละเอียด</td><td><textarea name=\"detail\" rows=\"10\" cols=\"40\">".$data['Type']['detail']."</textarea></td></tr>";
		$code.="<tr>";
			$code.="<td width='200px'>ใส่รูปภาพ (ใส่เป็นชื่อ File)</td>";
			$code.="<td>";
				$code.="<div><img src='".HOME.$data['Type']['pic']."'></div><br>";
				$code.="<div><input type='text' name='pic' id='pic' value='".$data['Type']['pic']."'/></div>";
				
			$code.="</td></tr>";
		$code.="<tr><td width=\"400px\" align=\"center\" colspan=\"2\"><input type='submit' name='submit' value='submit'> <input type='reset' value='reset'></td></tr>";		
		$code.="<input type='hidden' id='order' name='order' value='".$data['Type']['order']."'>";
		$code.="</table></form>";
		$code.="<br/>";
		$code.=$this->contentAll($this->Type->findTypeAll($data['Type']['category_id']));
		
		$this->layout='home';
    	$this->set('HTML',$this->body->getHtml($code));
	}
	
	public function delete($type_id,$name){
		$this->Type->del($type_id);
	    $this->flash('The post with id: '.$type_id.' has been deleted.', '/types/find/'.$name);
	}
	public function step($type_id,$step,$level){
		header("Content-type: text/html; charset=UTF-8");
		$data1 = $this->Type->findType($type_id);
		$data1 = $data1[0];
		$category = "/types/find/".$data1['Category']['name'];
		$data1['Type']['order']=$step;
		$sql="SELECT * FROM inv_types AS Type Where Type.category_id=".$data1['Type']['category_id']." AND Type.order=$step";
		$data2 = $this->Type->tQuery($sql);
		$data2 = $data2[0];

		if($level=="up"){
			$data2['Type']['order']++;
		}else{
			$data2['Type']['order']--;
		}
		$sql = "UPDATE inv_types SET inv_types.order='".$data2['Type']['order']."' WHERE type_id=".$data2['Type']['type_id'];
		$this->Type->tQuery($sql);
		$sql = "UPDATE inv_types SET inv_types.order='$step' WHERE type_id=$type_id";
		$this->Type->tQuery($sql);
		
		$this->flash("ลำดับ",$category,0);
		//$this->redirect("/types/find1/".$data1['Type']['category_id']);
	}
//============== Promotion Zone==========================//

	public function promotion(){
		$this->layout='home';
		$datas = $this->Type->promotionType();
		
		$code  ='<div style="margin:15px">โปรโมชั่นของสินค้าแต่ละประเภท</div>';
		$code .='<div style="margin:15px">';
		foreach($datas as $data){
			$code.='<div class="promotion_style">';
				$code.='<span class="promotion_style_1"><a href="'.HOME.'types/proType/'.$data['Type']['type_id'].'"><img src="'.HOME.$data['Type']['pic'].'" width="60px" height="40px"></a></span>';
				$code.='<span class="promotion_style_2"><a href="'.HOME.'types/proType/'.$data['Type']['type_id'].'">'.$data['Type']['name'].'</a></span>';
				$code.='</a>';
			$code.='</div>';
		}
		$code.='</div>';
		$code.='<div style="float:right;">';
		$code.='<a href="'.HOME.'types/selectCategory/">เพิ่มโปรโมชั่น <img src="'.HOME.'img/write-review.gif"/></a>';
		$code.='</div>';
		$this->set('HTML',$this->body->getHtml($code));
	}
	public function proType($id){
		$this->layout='home';
		$type=null;
		$datas = $this->Type->promotion($id);
		
		if(count($datas)>0)
			$type = $datas[0]['Type'];
		$code ='';
		
		$code.='<div style="margin:15px">';
		$code.='<span style="font-size:20px;">โปรโมชั่นของ '.$type['name'].'</span>';
		$code.='<span> <img src="'.HOME.$type['pic'].'" width="30px" height="30px"/></span>';
		$code.='<span> <a href="'.HOME.'types/promotion">กลับ</a></span>';
		$code.='</div>';
		
		$code .='<div style="margin:15px">';
		$code .='<table id="promotion_list_1">';
		$code .='<tr align="right"><th width="30px">No.</th><th width="220px"> Name</th><th width="80px">จำนวนชิ้น</th><th width="80px">ราคา</th></tr>';
		for($i=0;$i<count($datas);$i++){
			$code.='<tr>';
				$code.='<td>'.($i+1).'</td>';
				$code.='<td>'.$datas[$i]['Promotion']['name'].'</td>';
				$code.='<td>'.$datas[$i]['Promotion']['limit'].'</td>';
				$code.='<td>'.$datas[$i]['Promotion']['price'].'</td>';
				$code.='<td><a href="'.HOME.'types/editPro/'.$datas[$i]['Promotion']['id'].'"><img src="'.HOME.'img/config.png" height="16px"/></a>';
				$code.=' | <a href="'.HOME.'types/deletePro/'.$type['type_id'].'/'.$datas[$i]['Promotion']['id'].'" onclick="return delConfirm();">';
				$code.='<img src="'.HOME.'img/stop.png"/></a></td>';
			$code.='</tr>';
		}
		$code .='</table>';
		$code.='</div>';
		$code.='<div><a href="'.HOME.'types/addPro/'.$type['type_id'].'">เพิ่มโปรโมชั่น <img src="'.HOME.'img/write-review.gif"/></a></div>';
		$this->set('HTML',$this->body->getHtml($code));
	}
	public function editPro($id){
		if (!empty($this->params['form'])){
			$this->Type->updatePromotion($this->params['form']);
        }
		$this->layout='home';
		$code='';
		$datas = $this->Type->pro($id);
		$data = $datas[0];

		$code.='<div style="margin:15px;background-color:#FEF5CA;padding:5px">';
		$code.='<span style="font-size:20px;">แก้ไขโปรโมชั่นของ '.$data['Type']['name'].'</span>';
		$code.='<span> <img src="'.HOME.$data['Type']['pic'].'" width="30px" height="30px"/></span>';
		$code.='<span> <a href="'.HOME.'types/proType/'.$data['Type']['type_id'].'">กลับ</a></span>';
		$code.='</div>';
		
		$code.='<div style="margin:15px;background-color:#F6F9ED;padding:5px">';
		$code.='<form method="post" action="" name="form_type">';
			$code.='<div class="p1"> <b>id.</b> : '.$data['Promotion']['id'].'<input type="hidden" value="'.$data['Promotion']['id'].'" name="id"/></div>';
			$code.='<div class="p1"> <b>Type.</b> : '.$data['Type']['name'].' change</div>';
			$code.='<div class="p1"> <b>Serial.</b> :<input style="text-align:left" type="text" value="'.$data['Promotion']['promotion_id'].'" class="promotion" name="promotion_id" size="6"/></div>';			
			$code.='<div class="p1"> <b>Name.</b> :<input style="text-align:left" type="text" value="'.$data['Promotion']['name'].'" class="promotion" name="name" size="12"/></div>';
			$code.='<div class="p1"> <b>Price.</b> :<input type="text" value="'.$data['Promotion']['price'].'" class="promotion" name="price" size="6"/> บาท.</div>';
			$code.='<div class="p1"> <b>Limit.</b> :<input type="text" value="'.$data['Promotion']['limit'].'" class="promotion" name="limit" size="6"/> ชิ้น.</div>';
			$code.='<div class="p1"> <b>Detail</b> :<input style="text-align:left" type="text" value="'.$data['Promotion']['detail'].'" class="promotion" name="detail" size="40"/></div>';
			$code.='<div class="p1"><br><input type="submit" name="submit" value="submit"> </div>';
		$code.='</form>';
		$code.='</div>';
		
		$this->set('HTML',$this->body->getHtml($code));
	}
	public function addPro($id){
		$code='';
		if (!empty($this->params['form'])){
			if($this->params['form']['name'] !=null && $this->params['form']['price']){
				$this->Type->addPromotion($this->params['form']);
				$this->flash('save',"/types/proType/".$id);
				exit();
			}else{
				$code.='<div style="margin:15px;background-color:red;padding:5px;width:200px">';
				$code.='กรุณาลงข้อมูลให้ครบถ้วน';
				$code.='</div>';
			}

        }		
		$this->layout='home';
		$datas = $this->Type->findType($id);
		$data = $datas[0];
		
		$code .='<div style="margin:15px;background-color:#FEF5CA;padding:5px">';
		$code.='<span style="font-size:20px;">เพิ่มโปรโมชั่นของ '.$data['Type']['name'].'</span>';
		$code.='<span> <img src="'.HOME.$data['Type']['pic'].'" width="30px" height="30px"/></span>';
		$code.='<span> <a href="'.HOME.'types/proType/'.$data['Type']['type_id'].'">กลับ</a></span>';
		$code.='</div>';
		
		$code.='<div style="margin:15px;background-color:#F6F9ED;padding:5px">';
		$code.='<form method="post" action="" name="form_type">';
			$code.='<div class="p1"> <b>Type.</b> : '.$data['Type']['name'].' change <input type="hidden" value="'.$data['Type']['type_id'].'" name="type_id"/></div>';
			$code.='<div class="p1"> <b>Serial.</b> :<input style="text-align:left" type="text" value="'.$data['Type']['serial_id'].'" class="promotion" name="promotion_id" size="6"/></div>';			
			$code.='<div class="p1"> <b>Name</b> :<input style="text-align:left" type="text" value="" class="promotion" name="name" size="12"/></div>';
			$code.='<div class="p1"> <b>Price</b> :<input type="text" value="" class="promotion" name="price" size="6"/> บาท.</div>';
			$code.='<div class="p1"> <b>Limit</b> :<input type="text" value="" class="promotion" name="limit" size="6"/> ชิ้น.</div>';
			$code.='<div class="p1"> <b>Detail</b> :<input style="text-align:left" type="text" value="" class="promotion" name="detail" size="40"/></div>';
			$code.='<div class="p1"><br><input type="submit" name="submit" value="submit"> </div>';
		$code.='</form>';
		$code.='</div>';
		
		$this->set('HTML',$this->body->getHtml($code));		
	}	
	public function deletePro($type_id,$id){
		$this->Type->delPromotion($id);
		$this->redirect(HOME."types/proType/".$type_id);
	}
	public function selectCategory(){
		$this->layout='home';
		$datas=$this->Type->findCategory();
		
		$code ='<div id="p_main_1">';
		$code.='<div style="margin-bottom:20px"><b>Select Category.</b></div>';
		foreach($datas as $data){
			if(($data['Category']['pic']==null) ||($data['Category']['pic']=='') )$img = 'img/noimg.gif';
			else $img = 'img/products/'.$data['Category']['pic'];

			$code .='<div class="promotion_style">';
				$code.='<div><img src="'.HOME.$img.'" width="45px" height="50px"/> ';
				$code.='<a href="'.HOME.'types/find/'.$data['Category']['name'].'">'.$data['Category']['name'];
				$code.='</div>';
			$code .='</div>';
		}
		$code.='</div>';
		$this->set('HTML',$this->body->getHtml($code));	
	}
	public function createPro(){
		$code='';
		if (!empty($this->params['form'])){
			if($this->params['form']['name'] !=null && $this->params['form']['price']){
				$this->Type->addPromotion($this->params['form']);
				$this->redirect(HOME."types/proType/".$id);
			}else{
				$code.='<div style="margin:15px;background-color:red;padding:5px;width:200px">';
				$code.='กรุณาลงข้อมูลให้ครบถ้วน';
				$code.='</div>';
			}

        }		
		$this->layout='home';
		$datas = $this->Type->findType($id);
		$data = $datas[0];
		
		$code .='<div style="margin:15px;background-color:#FEF5CA;padding:5px">';
		$code.='<span style="font-size:20px;">เพิ่มโปรโมชั่นของ '.$data['Type']['name'].'</span>';
		$code.='<span> <img src="'.HOME.$data['Type']['pic'].'" width="30px" height="30px"/></span>';
		$code.='<span> <a href="'.HOME.'types/proType/'.$data['Type']['type_id'].'">กลับ</a></span>';
		$code.='</div>';
		
		$code.='<div style="margin:15px;background-color:#F6F9ED;padding:5px">';
		$code.='<form method="post" action="" name="form_type">';
			$code.='<div class="p1"> <b>Type.</b> : '.$data['Type']['name'].' change <input type="hidden" value="'.$data['Type']['type_id'].'" name="type_id"/></div>';
			$code.='<div class="p1"> <b>Serial.</b> :<input style="text-align:left" type="text" value="'.$data['Type']['serial_id'].'" class="promotion" name="promotion_id" size="6"/></div>';			
			$code.='<div class="p1"> <b>Name</b> :<input style="text-align:left" type="text" value="" class="promotion" name="name" size="12"/></div>';
			$code.='<div class="p1"> <b>Price</b> :<input type="text" value="" class="promotion" name="price" size="6"/> บาท.</div>';
			$code.='<div class="p1"> <b>Limit</b> :<input type="text" value="" class="promotion" name="limit" size="6"/> ชิ้น.</div>';
			$code.='<div class="p1"> <b>Detail</b> :<input style="text-align:left" type="text" value="" class="promotion" name="detail" size="40"/></div>';
			$code.='<div class="p1"><br><input type="submit" name="submit" value="submit"> </div>';
		$code.='</form>';
		$code.='</div>';
		
		$this->set('HTML',$this->body->getHtml($code));		

	}
}
?>