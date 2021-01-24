<?php
class CategoriesController extends AppController{
    var $name='Category';
    var $helpers = array('Html', 'Javascript', 'Ajax');
    var $components = array('body');
    
    function index(){
        $this->layout='home';
        $this->set('HTML',$this->genCodeHTML());
    }
    
    function genCodeHTML(){
        return $this->body->getHtml($this->content($this->Category->findAll()));
    }

    function add(){
       $this->set('HTML',$this->body->getHtml($this->formAdd()));
    }
    
    function formAdd(){
        if (!empty($this->params['form'])){
            $param = $this->params['form'];
            if ($this->Category->save($this->params['form'])){
                $this->flash('Your registration information was accepted.','/');
            } else {
                $this->flash('There was a problem with your registration','/');
            }
            print_r($param);
        }
        $code ='';
        $code .='<form method="post" action="'.HOME.'categories/add/" name="form_cat">';
        $code .='<table width="100%" border="1">';
        $code .='<tr><td colspan="2" align="right">เพิ่มข้อมูลประเภทรายการสินค้า</td></tr>';
        $code.='<tr><td width="200px">ชื่อประเภท</td><td><input type="text" name="name" size="30"/></td></tr>';
        $code.='<tr><td width="200px">รหัสสินค้า</td><td><input type="text" name="serial_id" size="2"/>*ไม่เกิน 2ตัวอักษร และห้ามซ้ำ</td></tr>';
        $code.='<tr><td width="200px">รายละเอียด</td><td><textarea name="detail" rows="10" cols="40"></textarea></td></tr>';
        $code.='<tr><td width="400px" align="center" colspan="2"><input type="submit" name="submit" value="เพิ่มข้อมูล"/> <input type="reset" value="clear"></td></tr>';
        $code .='</table>';
        $code .='</form>';
        $code.= $this->content($this->Category->findAll());
        return $code;
    }
    
    function content($DATA){
    $code = "<table border=\"1\" width=\"100%\">";
    $code .="<tr><th colspan='3'>แสดงประเภทรายการสินค้า</th></tr>";
    $code .= "<tr><td colspan=\"3\" align=\"right\"><a href=\"".HOME."categories/add\">เพิ่มข้อมูลประเภทรายการสินค้า</a></td></tr>";
    $code .= "<tr><td width=\"80px\">รหัส</td><td width='200px'>ชื่อประเภท</td><td>รายละเอียด</td></tr>";
        foreach($DATA as $datas){
            $code.= "<tr>";
            $code.= "<td>".$datas['Category']['serial_id']."</td>";
            $code.= "<td><a href='".HOME."types/find/".$datas['Category']['name']."'>".$datas['Category']['name']."</a></td>";
            $code.= "<td>";
				$code.= "<div style='float:right'><img src='".HOME."img/config.png'></div>";
				$code.= "<div>".$datas['Category']['detail']."</div>";
			$code.= "</td>";
            $code.= "</tr>";
        }
        $code.="</table>";
        return $code;
    }
    
}
?>