<?
class StocksController extends AppController{
    var $name='Stock';
    var $helpers = array('Html', 'Javascript', 'Ajax');
    var $components = array('body','Stocking','Producting');
    
    function index(){
        $this->layout='home';
        $this->set('HTML',$this->body->getHtml($this->content($this->Stock->findAll("1=1 ORDER BY Stock.stock_name ASC"))));
    }
    function find($id){
        $this->layout='home';
        $this->set('HTML',$this->body->getHtml($this->content($this->Stock->findStock($id))));
    }
    
    function view($id){
        $this->layout='home';   
        $this->set('HTML',$this->body->getHtml($this->displayView($this->Stock->findStock($id))));
    }
    
    function add(){
        $this->layout='home';
        $this->set('HTML',$this->body->getHtml($this->formAdd()));
        
    }
    
    function content($DATA){
        //print_r($DATA[0][0]);
        $code = "<table width=\"100%\">";
        $code.="<tr><td colspan='3'>รายการสินค้าในคลังสินค้า</td></tr>";
        $code.="<tr><th>no.</th><th>ชื่อคลังสินค้า</th><th>รหัสคลังสินค้า</th></tr>";
        foreach($DATA as $datas){
            $code.= "<tr>";
            $code.= "<td>".$datas['Stock']['stock_id']."</td>";
            $code.= "<td><a href='".HOME."stocks/view/".$datas['Stock']['stock_id']."'>".$datas['Stock']['detail']."</a></td>";
            $code.= "<td><a href='".HOME."stocks/view/".$datas['Stock']['stock_id']."'>".$datas['Stock']['stock_name']."</a></td>";
            $code.= "</tr>";
        }
        $code.="</table>";
        return $code;
    }
    
    function displayView($data){
    	if(isset($_GET['order'])&&isset($_GET['t'])){
			$sql="ORDER BY ".$_GET['order']." ".$_GET['t'];
    	}else{
    		$sql ="ORDER BY Product.quantity ASC";
    	}
        $code ="";
        $code .="<table width='100%' border='1'>";
        foreach($data as $datas){
            $code .="<tr><th width='200px'>ชื่อคลังสินค้า</th><td>".$datas['Stock']['stock_name']."</td></tr>";
            $code .="<tr><th>รายละเอียด</th><td>".$datas['Stock']['detail']."</td></tr>";
            $code .="<tr><th>รูป</th><td>".$datas['Stock']['pic']."</td></tr>";
            $code .="<tr><th valign='top'>รายการสินค้าคงคลัง</th>";
            $code .="<td>".$this->Producting->listInStock2($this->Stock->findProduct($datas['Stock']['stock_id'],$sql))."</td>";
            $code .="</tr>";
        }
        $code.="</table>";
        return $code;
    }
    
    function formAdd(){
        if (!empty($this->params['form'])){
            $param = $this->params['form'];
            if ($this->Stock->save($this->params['form'])){
                $this->flash('เพิ่มข้อมูลลงในบัชชีคลังสินค้าแล้ว.','/stocks/');
            } else {
                $this->flash('พบความผิดพลาดไม่สามารถเพิ่มข้อมูลได้','/');
            }
        }
        $code ="<form method='post' action='".HOME."stocks/add/' name='form_stock'>";
        $code .="<table width='600px' border='1'>";
        $code.="<tr><td colspan='2'>เพิ่มข้อมูลคลังสินค้า</td></tr>";
        $code.="<tr><th width='200px'>ชื่อคลังสินค้า</th><td><input type='text' name='stock_name' size='40'/></td></tr>";
        $code.="<tr><th valign='top'>รายละเอียด</th><td><textarea name='detail' rows='5' cols='40'></textarea></td></tr>";
        $code.="<tr><th>รูปภาพ</th><td></td></tr>";
        $code.="<tr><td width='400px' align='center' colspan='2'><input type='submit' name='submit' value='เพิ่มข้อมูล'/> <input type='reset' value='clear'></td></tr>";
        $code.="</table>";
          $code.="</form>";
        return $code;
    }
    
}

?>