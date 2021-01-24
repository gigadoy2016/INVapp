/*
* Script for Ajax
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

function getData(URL,ElementId){
//alert(URL);
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
/*-----------------------------*/