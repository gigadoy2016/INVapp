<?php

class StepComponent extends Object{
    var $name = 'Step';
    var $controller = true;
	
	private $countNumber =0;
	public $url ='';

	public function setNumber($num){
		$this->countNumber = $num-1;
	}
	
	public function getStep($type,$step){
		$code="<div style='float:right;width:20px;'>";
		if($step !=0)
			$code.="<div><a href='".$this->url."/".$type['type_id']."/".$step."/up'><img src='".HOME."img/ico_up2.gif'></a></div>";
		if($step != $this->countNumber)
			$code.="<div><a href='".$this->url."/".$type['type_id']."/".($step+2)."/down'><img src='".HOME."img/ico_down2.gif'></a></div>";
		$code.="</div>";
		return $code;
	}
}
?>