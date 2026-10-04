<?php
/*
 * Component:body
 * Ver :0.1 Alpha
 * Date :20071120
 */
class BodyComponent extends Object{
    var $name = 'Menu';
    var $controller = true;
    var $components = array('menu');
    var $title ='ร้านบัวเงิน';
    var $charset ='utf-8';
    var $javaScript ='';
    var $CSS='style.css';
    
    function getHtml($content=null){
		$code=$this->getHead();
        $code.='<body background="'.HOME.'img/bg.gif"><table width="100%"><tr><td colspan="2"><div id="header">ร้านบัวเงิน</div></td></tr><tr><td width="200px" valign=\"top\">';
        $code.=$this->menu->getHtml();
        $code.='</td><td valign="top">';
        //
        $code.=$content;
        $code.='</td></tr><tr><td colspan="2">Footer</td></tr></table></body>';
        return $code;
    }
	function getHead(){
	    $code='<head>';
        $code.='<title>'.$this->title.'</title>';
        $code.='<meta http-equiv="content-type" content="text/html; charset='.$this->charset.'" />';
        $code.=$this->getCSS();

        $code.=$this->javaScript;
        //$code.='<script src="'.HOME.'js/lib/jquery.js" type="text/javascript"></script>';
        //$code.='<script src="'.HOME.'js/lib/casher.js" type="text/javascript"></script>';
        $code.='<script src="'.HOME.'js/ajaxScript.js" type="text/javascript"></script>';
        $code.='<script src="'.HOME.'js/ajaxForm.js" type="text/javascript"></script>';
        

        $code.='</head>';	
		return $code;
	}
	function getBody($content){
		$code=$this->getHead();
		$code.=$code.='<body background="'.HOME.'img/bg.gif">';
		$code.=$content;
		$code.='</body>';
		return $code;
	}
	function getCSS($css=null){
		if(!empty($css)){$this->CSS=$css;}
		
		$code='<link rel="stylesheet" type="text/css" href="'.HOME.'css/'.$this->CSS.'" />';
		return $code;
	}
	
	function setJavaScript($java){
		$this->javaScript = $java;
	}
	function getWYSIWYG(){
		$code  ='<script src="'.HOME.'js/lib/tiny_mce/tiny_mce.js" type="text/javascript"></script>';
		$code .= '<script type="text/javascript">
	tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,forecolor,backcolor,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,image,cleanup,help,code,|,insertdate,inserttime,preview",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example word content CSS (should be your site CSS) this one removes paragraph margins
		content_css : "css/word.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});
</script>';
		return $code;
	}
	
	public function showImg($url){
		
	}
}