/**
 * @author xan
 * class: Item
 */
//Class Item

function Item(name,quantity,priceUnit,pic,serial,cost){
    //property
    this.name = name;
    this.product_id=null;
	this.type_id=serial;
    this.serial=serial;
    this.quantity = 0;
    this.priceUnit = 0;
	this.cost = cost;
    this.result =quantity*priceUnit;
    this.code= '';
    this.pic =pic;
    this.promotion =new Array();
    this.promotion_id=null;
    this.stock = new Array();
	this.unitName=null;
	this.inventoryStock =0;
	this.detail=null;
    
    this.XML;
    
    this.getImg = '<img src="'+pic+'"/>';
    //method
    
    this.summary = function(){
		x= this.quantity*parseFloat(this.priceUnit); 
    	return Math.ceil(x);
	}
    
    this.getProductID =function(){
    	return this.serial + this.code;
    }
	this.getQuantity = function(){
		return parseFloat(this.quantity);
	}
	this.getCost = function(){
		if(this.cost==null)
			this.cost =0;
		cost = parseFloat(this.cost) * parseFloat(this.quantity);
		return cost;
	}
    
    this.promotions = function(){
    	var i=0;
    	var sale = 0;
    	var number = this.quantity;
    	while (i<this.promotion.length){
    		if(parseInt(this.promotion[i]['number'])<= parseInt(number)){
                sale 	+=parseFloat(this.promotion[i]['price']);
                number 	-=parseInt(this.promotion[i]['number']);
            }else{
                i++;
            }
    	}
        sale += parseInt(number) * parseFloat(this.priceUnit);
        return sale;
    }
    
    this.ajax = function(URL){
    	var xmlHttp;
        var xml;
        //xmlHttp=GetXmlHttpObject(); //Check Support Brownser 
		xmlHttp = new XMLHttpRequest()
        if (xmlHttp==null){
            alert ("Browser does not support HTTP Request");
            return;
        } 
        xmlHttp.onreadystatechange=function (){
            if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
                xml = xmlHttp.responseXML;
            }else{
            	//document.getElementById("ob1").innerHTML="<div class='loading'> Waitting..</div>" ;
            }
        };
        xmlHttp.open("GET",URL,false);
        xmlHttp.send(null);
        return xml;
    }
    
    this.setXML = function(XML){
    
    	var xmlDoc;
        try {                           //Internet Explorer
            xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
        }catch(e) {
            try {                        //Firefox, Mozilla, Opera, etc.
                xmlDoc=document.implementation.createDocument("​","​",null);
				
            }catch(e) {
                alert(e.message);
                return;
            }
        }
        xmlDoc.async=false;
        xmlDoc.load(XML);
        var x =xmlDoc.getElementsByTagName("promotion");
        this.promotion = new Array();
        if(x.length >0){
        	for(i=0;x.length>i;i++){
            	this.promotion[i] = new Array();
            	this.promotion_id = this.promotion[i]['id'] =x[i].attributes.getNamedItem("id").value;
            	this.promotion[i]['name'] =x[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
            	this.promotion[i]['number'] =parseInt(x[i].getElementsByTagName("number")[0].childNodes[0].nodeValue);
            	this.promotion[i]['price'] =parseFloat(x[i].getElementsByTagName("price")[0].childNodes[0].nodeValue);
        	}
        }else{
        	this.promotion =-1;
        }
        var y = xmlDoc.getElementsByTagName("stock");
        this.stock = new Array();
        if(y.length > 0){
			for (i = 0; y.length > i; i++) {
				this.stock[i] = new Array();
				this.stock[i]['id'] = y[i].getElementsByTagName('id')[0].childNodes[0].nodeValue;
				this.stock[i]['name'] = y[i].getElementsByTagName('name')[0].childNodes[0].nodeValue;
				this.stock[i]['detail'] = y[i].getElementsByTagName('detail')[0].childNodes[0].nodeValue;
				this.stock[i]['quantity'] = y[i].getElementsByTagName('quantity')[0].childNodes[0].nodeValue;
			}
			var p = xmlDoc.getElementsByTagName("product");
			if(p[0].getElementsByTagName('price')[0].childNodes[0].nodeValue>0){
				this.priceUnit = p[0].getElementsByTagName('price')[0].childNodes[0].nodeValue;
			}
			if(p[0].getElementsByTagName('cost')[0].childNodes[0].nodeValue>0){
				this.cost = p[0].getElementsByTagName('cost')[0].childNodes[0].nodeValue;
			}		
        }else{
			alert("ไม่พบสินค้าในระบบ");
		}

		this.priceUnit = parseFloat(this.priceUnit).toFixed(2);
        return this.promotion;
    }

    this.setItem=function(XML){
    	var xmlDoc;

        try {                           //Internet Explorer
            xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
        }catch(e) {
            try {                        //Firefox, Mozilla, Opera, etc.
                xmlDoc=document.implementation.createDocument("​","​",null);
				
            }catch(e) {
                alert(e.message);
                return;
            }
        }
        xmlDoc.async=false;
        xmlDoc.load(XML);
        var x =xmlDoc.getElementsByTagName("type");


        if(x.length >0){
        	for(i=0;x.length>i;i++){
				this.serial 	=x[i].getElementsByTagName("serial")[0].childNodes[0].nodeValue;
            	this.name 		=x[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
            	this.priceUnit 	=parseFloat(x[i].getElementsByTagName("price")[0].childNodes[0].nodeValue);
            	this.cost		=parseFloat(x[i].getElementsByTagName("cost")[0].childNodes[0].nodeValue);
				if (x[i].getElementsByTagName("pic")[0].childNodes.length>0)
					this.pic 	=x[i].getElementsByTagName("pic")[0].childNodes[0].nodeValue;
        	}
        }else{
			alert("ไม่พบสินค้าในระบบ");
			return 0;
		}

		
        var y = xmlDoc.getElementsByTagName("unit");
        this.unitName = new Array();
		
        if(y.length > 0){
			for (i = 0; y.length > i; i++) {
				this.unitName[i] = new Array();
				this.unitName[i]['name'] = y[i].getElementsByTagName('name')[0].childNodes[0].nodeValue;
				this.unitName[i]['ratio'] = y[i].getElementsByTagName('ratio')[0].childNodes[0].nodeValue;
				this.unitName[i]['status'] = y[i].getElementsByTagName('status')[0].childNodes[0].nodeValue;
			}
        }
		
        var z = xmlDoc.getElementsByTagName("promotion");
        this.promotion = new Array();

        if(z.length > 0){
			for (i = 0; z.length > i; i++) {
				this.promotion[i] = new Array();
				this.promotion[i]['name'] = z[i].getElementsByTagName('name')[0].childNodes[0].nodeValue;
				this.promotion[i]['number'] = z[i].getElementsByTagName('number')[0].childNodes[0].nodeValue;
				this.promotion[i]['price'] = z[i].getElementsByTagName('price')[0].childNodes[0].nodeValue;
			}
        }		
	}
    
	this.setItemXML=function(URL){
		
    	var xhttp=new XMLHttpRequest();
		xhttp.open("GET",URL,false);
		xhttp.send();
		xmlDoc = xhttp.responseXML;
		var x =xmlDoc.getElementsByTagName("type");
		
        if(x.length >0){
        	for(i=0;x.length>i;i++){
				this.serial 		=x[i].getElementsByTagName("serial")[0].childNodes[0].nodeValue;
            	this.name 			=x[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
				this.promotion_id	=x[i].getElementsByTagName("promotion_id")[0].childNodes[0].nodeValue;
            	this.priceUnit 		=parseFloat(x[i].getElementsByTagName("price")[0].childNodes[0].nodeValue).toFixed(2);
            	this.cost			=parseFloat(x[i].getElementsByTagName("cost")[0].childNodes[0].nodeValue);
				this.detail			=x[i].getElementsByTagName("detail")[0].childNodes[0].nodeValue;
				if (x[i].getElementsByTagName("pic")[0].childNodes.length>0)
					this.pic 				=x[i].getElementsByTagName("pic")[0].childNodes[0].nodeValue;
				if(this.promotion_id == ' ') this.promotion_id=null;
        	}
        }else{
			alert("ไม่พบสินค้าในระบบ");
			//$("#errmsg").html("ไม่พบสินค้า").show().fadeOut(1600);
			//$("#code").val("");
			return 0;
		}

		
        var y = xmlDoc.getElementsByTagName("unit");
        this.unitName = new Array();
        if(y.length > 0){
			for (i = 0; y.length > i; i++) {
				this.unitName[i] = new Array();
				this.unitName[i]['name'] = y[i].getElementsByTagName('name')[0].childNodes[0].nodeValue;
				this.unitName[i]['ratio'] = y[i].getElementsByTagName('ratio')[0].childNodes[0].nodeValue;
				this.unitName[i]['status'] = y[i].getElementsByTagName('status')[0].childNodes[0].nodeValue;
			}
        }
		
        var z = xmlDoc.getElementsByTagName("promotion");
        this.promotion = new Array();

        if(z.length > 0){
			for (i = 0; z.length > i; i++) {
				this.promotion[i] = new Array();
				this.promotion[i]['name'] = z[i].getElementsByTagName('name')[0].childNodes[0].nodeValue;
				this.promotion[i]['number'] = z[i].getElementsByTagName('number')[0].childNodes[0].nodeValue;
				this.promotion[i]['price'] = z[i].getElementsByTagName('price')[0].childNodes[0].nodeValue;
			}
        }
		//alert("Items promotion :"+this.promotion.length);
	}
	this.getPromotionHTML = function(){
		var text =null;
		if(this.promotion.length > 0){
			text = "<table style=\"width:200px;background-color:#FBF8EF\" >";
			text += "<tr><th>รายการ</th><th>จำนวน</th><th>ราคา</th></tr>";
			for (i = 0; this.promotion.length > i; i++) {
				text += "<tr><td>"+this.promotion[i]['name']+"</td><td>"+this.promotion[i]['number']+"</td><td>"+this.promotion[i]['price']+"</td></tr>";
			}
			text += "</table>";
		}
		return text;
	}
	
	this.getStock= function(URL){
		var xhttp=new XMLHttpRequest();
		xhttp.open("GET",URL,false);
		xhttp.send();
		xmlDoc = xhttp.responseXML;
		
        var x =xmlDoc.getElementsByTagName("promotion");
        this.promotion = new Array();
        if(x.length >0){
        	for(i=0;x.length>i;i++){
            	this.promotion[i] = new Array();
            	this.promotion_id = this.promotion[i]['id'] =x[i].attributes.getNamedItem("id").value;
            	this.promotion[i]['name'] =x[i].getElementsByTagName("name")[0].childNodes[0].nodeValue;
            	this.promotion[i]['number'] =parseInt(x[i].getElementsByTagName("number")[0].childNodes[0].nodeValue);
            	this.promotion[i]['price'] =parseFloat(x[i].getElementsByTagName("price")[0].childNodes[0].nodeValue);
        	}
        }else{
        	this.promotion =-1;
        }
        var y = xmlDoc.getElementsByTagName("stock");
        this.stock = new Array();
		
        if(y.length > 0){
			for (i = 0; y.length > i; i++) {
				this.stock[i] = new Array();
				this.stock[i]['id'] = y[i].getElementsByTagName('id')[0].childNodes[0].nodeValue;
				this.stock[i]['name'] = y[i].getElementsByTagName('name')[0].childNodes[0].nodeValue;
				this.stock[i]['detail'] = y[i].getElementsByTagName('detail')[0].childNodes[0].nodeValue;
				this.stock[i]['quantity'] = y[i].getElementsByTagName('quantity')[0].childNodes[0].nodeValue;
				this.inventoryStock += this.stock[i]['quantity'];
			}
			var p = xmlDoc.getElementsByTagName("product");
			if(p[0].getElementsByTagName('price')[0].childNodes[0].nodeValue>0){
				this.priceUnit = p[0].getElementsByTagName('price')[0].childNodes[0].nodeValue;
			}
		 	if(p[0].getElementsByTagName('cost')[0].childNodes[0].nodeValue>0){
				this.cost = p[0].getElementsByTagName('cost')[0].childNodes[0].nodeValue;
			}		
        }else{
			alert("ไม่พบสินค้าในระบบ");
			//$("#errmsg").html("ไม่พบสินค้า").show().fadeOut(1600);
			//$("#code").val("");
		}

        return this.stock;
	}
	
	this.getStockHTML= function(){
		var text =null;
		//alert(this.stock.length);
		if(this.stock.length>0){
			text ="<table style=\"width:200px\" >";
			for (i = 0; this.stock.length > i; i++) {
				text += "<tr><td width=\"120px\" style=\"font-size:x-small;\">"+this.stock[i]['detail']+"("+this.stock[i]['name']+")</td><td style=\"font-weight:bold;color:green\"><label id='inventoryStock'>"+this.stock[i]['quantity']+"</label> pcs.</td></tr>";
			}
			text += "</table>";
		}
		return text;
	}
}
