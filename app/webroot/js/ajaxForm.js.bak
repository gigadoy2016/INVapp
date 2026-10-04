/*
* Script for From Ajax
*/

function GetXmlHttpObject(){ 
	var xmlHttp=null;
	try{
 // Firefox, Opera 8.0+, Safari
	xmlHttp=new XMLHttpRequest();
	}catch (e) {
 //Internet Explorer
		try{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}catch (e){
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}

function getDataByAjax(URL,ElementId){

	var xmlHttp;
	xmlHttp=GetXmlHttpObject(); //Check Support Brownser

	if (xmlHttp==null){
		alert ("Browser does not support HTTP Request");
		return;
	} 
    xmlHttp.onreadystatechange=function (){
        if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){	
            document.getElementById(ElementId).innerHTML=xmlHttp.responseText ;
        } else{
            document.getElementById(ElementId).innerHTML="<div class='loading'> Loading..</div>" ;
        }
	};
	xmlHttp.open("GET",URL,true);
	xmlHttp.send(null);
}

function sendData(url,data){
	if(data=='' || data ==null){
		alert("ยังไม่มีข้อมูลใน Bill");
		return 0;
	}
var xmlHttp = GetXmlHttpObject(); //Check Support Brownser

showElement("formAdd1");
showElement("formAdd2");

	xmlHttp.onreadystatechange=function (){
    	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){	
            document.getElementById("formAdd2_1").innerHTML=xmlHttp.responseText ;
        } else{
            document.getElementById("formAdd2_1").innerHTML="<div class='loading'> Loading..</div>" ;
        }
	};
	
xmlHttp.open("post",url,false);
xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
xmlHttp.send(data);
}
/*-----------------------------*/

function autoSelectType(objD,URL){
	var xmlHttp;
	xmlHttp=GetXmlHttpObject(); //Check Support Brownser

	if (xmlHttp==null){
		alert ("Browser does not support HTTP Request");
		return;
	} 
    xmlHttp.onreadystatechange=function (){
        if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){	
        	var data = xmlHttp.responseText.split("|")[0];
            objD.value=data;
        }
	};
	xmlHttp.open("GET",URL,true);
	xmlHttp.send(null);
	
}
/*-----------------------------*/


/*
Other Function
*/
function showSerialID(){
	document.getElementById("code").value = document.getElementById("code").value.toUpperCase();
	document.getElementById("serial_id").value = types[document.getElementById("type_id").value]+document.getElementById("code").value;
	document.getElementById("product").value = product_name[document.getElementById("type_id").value]
}

function delConfirm(){
 if(confirm('คุณต้องการลบข้อมูลใช่หรือไม่')){
 	return true;
 }else{
 	return false;
 }
}

//================================================
//Evention
//================================================

function TextBox1_onChnage(obj){		//TextBox(code) in the from Order product.
	obj.value=obj.value.toUpperCase();
	
	var URL ="http://localhost/INVapp/promotions/XML/"+product.serial+obj.value;
	var xml = product.ajax(URL);

	product.setXML(xml);

	if(product.promotion !=-1){
		var pro = '<table>';
		pro +='<tr><td>Promotion</td><td>จำนวน</td><td>ราคา</td></tr>';
		for(var i=0;i<product.promotion.length;i++){
			pro +='<tr><td>'+product.promotion[i]['name']+'</td><td>'+product.promotion[i]['number']+'</td>';
			pro +='<td>'+product.promotion[i]['price']+'</td></tr>';
		}
		pro +='</table>';
		document.getElementById("ob1").innerHTML = pro;
	}else{
		if(pro !=null)
			document.getElementById("ob1").innerHTML = "<div>Promotion Not Found.</div>";
	}
	
	if (product.stock.length > 0) {
		var pro = '<table>';
		for (var i = 0; i < product.stock.length; i++) {
			pro += '<tr><td>' + product.stock[i]['detail'] + '</td><td>' + product.stock[i]['quantity'] + " pz." + '</td></tr>';
		}
		document.getElementById("show_stock").innerHTML = pro;
		document.getElementById("p_price").value = product.priceUnit;
	}
	
}

//================================================
//		Function key number only
//================================================
function blockNaN(){
var isNS4 = (navigator.appName=="Netscape")?1:0;
	regex = /[0-9\.\r]$/; /* ให้กรอกได้เฉพาะเลข 0-9,. */
	if (!isNS4) {
		key = String.fromCharCode(event.keyCode);
		if (!(regex.test(key))) event.returnValue = false;
	}else {
		key = String.fromCharCode(event.which);
		if (!(regex.test(key))) return false;
	}
}