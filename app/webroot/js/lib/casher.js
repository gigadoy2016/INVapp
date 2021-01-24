// Class product & cahser with javascript;
//Create 2008/05/01
var order = new Bill();
var product;


// ---- Form ใส่รายละเอียดการขาย ---- //

function showItem(name,quantity,priceUnit,pic,serial,cost){
	
	var child_div=document.getElementById("formAdd2_1");
	var code = '<div><table width="100%">';
	code+='<tr>';
		code+='<td width="30%" rowspan="4">';
			code+='<img width="100%" src="'+pic+'"/>';
		code+='</td>';
		code+='<td width="70%">';
			code+='<h2>'+name+'</h2>';
		code+='</td>';
	code+='</tr>';
	code+='<tr><td>รหัสสินค้า :<b>'+serial+'</b><input id="code" type="text" name="code" size="6" onChange="TextBox1_onChnage(this)"/></td></tr>';
	code+='<tr>';
	code+='<td>';
	code+='<span>จำนวน :</span>';
	code+='<span><input id="quantity" type="text" name="quantity" size="3" value="1" onkeypress="blockNaN()"  onclick="focus();select();"/> ชิ้น.</span>';
	code+='<span> <a href="#" onclick="quantity.value++"><img src="img/ico_up2.gif"></a> ';
	code+='<a href="#" onclick="quantity.value--"><img src="img/ico_down2.gif"></a></span>';
	code+='</td></tr>';
	code+='<tr><td><span>ราคาต่อชิ้น :</span>';
	code+='<span><input type="text" name="u_price" id="p_price" value="'+parseFloat(priceUnit).toFixed(2)+'" size="5" onclick="focus();select();"/> บาท.</span>';
	code+='</td></tr>';
	code+='<tr><td colspan="2"><div id="show_stock" style="float:left"></div>';
	code+='<div style="float:right">';
	code+='<input type="submit" name="buttom_1" value="ตกลง" onclick="buttom1_onClick();"><button onclick="closeElement(\'formAdd1\');closeElement(\'formAdd2\')">ยกเลิก</button></div>';
	code+='</td></tr>';
	code+='</table></div><div id="ob1"></div>';
	
	child_div.innerHTML=code;
	
	showElement("formAdd1");
	showElement("formAdd2");
	document.getElementById('code').focus();
	document.getElementById('code').focus();
	product = new Item(name,quantity,priceUnit,pic,serial,cost);
}
//-----------------------------------------------------------------------


function closeElement(ElementID){
	document.getElementById(ElementID).style.display="none";
}
function showElement(ElementID){
	var division= document.getElementById(ElementID);
	division.style.display="block";
}

/* ปุ่มตกลง add item ลงใน order Form submit
*/
function buttom1_onClick(){
	if (document.getElementById("unit") != null) {
		product.quantity = document.getElementById("quantity").value * document.getElementById("unit").value;
	}else{
		product.quantity = document.getElementById("quantity").value;
	}
	product.code = document.getElementById("code").value;
	product.product_id= product.serial+product.code;
	product.priceUnit= document.getElementById("p_price").value;
	
	order.arrageItem(product);
	order.arrageOrder(order.item);
	
	order.orderList(order.item,'item');
	order.orderList(order.discount,'promotion');
	
	closeElement("formAdd1");
	closeElement("formAdd2");
	

}
//ปุ่ม ลบ item
function buttom2_deleteItem_onClick(index){
	order.deleteItemInOrder(index);
	order.orderList(order.itemGroupType,'itemGroupType');
}
function buttom_deleteItem_onClick(index){
	order.deleteItem(index);
	order.orderList(order.item,'item');
}
function tab_click1(){
	order.orderList(order.item,'item');
}
function tab_click2(){
	order.orderList(order.discount,'promotion');
}
function submitOrder(HOME){
	sendData(HOME+"bills/checkBill",order.postDataProduct());
}
function editValue(i){
	value=prompt("แก้ไขจำนวนราคาต่อหน่วย",order.discount[i].priceUnit);
	if (value!=null && value!="")
		order.discount[i].priceUnit=value;
	order.orderList2();
}
function showItemE(id){
	var form = new productForm('formAdd');
	form.showProduct(id);
}

//class input product

function productForm(elementID){
	
	this.htmlCode=null;
	this.elementID =elementID;
	this.layerBG = elementID+'1';
	this.layerM = elementID+'2';
	this.item =null;
	this.type_id=null;
	
	this.setType = function(id){
		this.type_id =id;
	}
	
	this.closed = function(){
		document.getElementById(this.layerBG).style.display="none";
		document.getElementById(this.layerM).style.display="none";
	}

	this.show = function(){
		document.getElementById(this.layerBG).style.display="block";
		document.getElementById(this.layerM).style.display="block";
	}
	this.showProduct=function(type_id){
		this.show();
		this.getProductByType(type_id);
		this.printProduct();
	}
    
	this.ajax = function(URL){
    	var xmlHttp;
        var xml;
		var layer = this.layerM+"_1";

        xmlHttp=GetXmlHttpObject(); //Check Support Brownser
        if (xmlHttp==null){
            alert ("Browser does not support HTTP Request");
            return;
        } 
        xmlHttp.onreadystatechange=function (){
            if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
                xml = xmlHttp.responseXML;
            }else{
            	document.getElementById(layer).innerHTML="<div class='loading'> Waitting..</div>" ;
            }
        };
        xmlHttp.open("GET",URL,false);
        xmlHttp.send(null);
        return xml;
    }

	
	this.getProductByType =function(type_id){
		url = "http://localhost/invapp/promotions/type/"+type_id;
		xml = this.ajax(url);
		this.item = new Item();
		this.item.setItem(xml);
		this.genCode(this.item);
	}
	
	this.genCode = function(item){
	
		var child_div=document.getElementById("formAdd2_1");
		var code = '<div><table width="100%">';
			code+='<tr>';
				code+='<td width="30%" rowspan="4">';
					code+='<img width="100%" src="'+item.pic+'"/>';
				code+='</td>';
				code+='<td width="70%">';
					code+='<h2>'+item.name+'</h2>';
				code+='</td>';
			code+='</tr>';
			code+='<tr>';
				code+='<td>รหัสสินค้า :<b>'+item.serial+'</b><input id="code" type="text" name="code" size="6" onChange="TextBox1_onChnage(this)" tabindex="1"/></td></tr>';
			code+='<tr>';
				code+='<td>';
					code+='<span>จำนวน :</span>';
					code+='<span id="display_quantity">';
						code+='<input id="quantity" type="text" name="quantity" size="3" value="1" onkeypress="blockNaN()"  onclick="focus();select();" tabindex="2"/> ';
					if (item.unitName.length > 0) {
						code += '<select id="unit">';
						for (i = 0; item.unitName.length > i; i++) {
							code += '<option value="'+item.unitName[i]['ratio']+'">'+item.unitName[i]['name']+'</option>';
						}
						code += '</select>';
					}else{
						code+=' ชิ้น.';
					}
					code+='</span>';
					code+='<span> <a href="#" onclick="quantity.value++"><img src="img/ico_up2.gif"></a> ';
					code+='<a href="#" onclick="quantity.value--"><img src="img/ico_down2.gif"></a></span>';
				code+='</td>';
			code+='</tr>';
			code+='<tr><td><span>ราคาต่อชิ้น :</span>';
				code+='<span><input type="text" name="u_price" id="p_price" value="'+parseFloat(item.priceUnit).toFixed(2)+'" size="5" onclick="focus();select();" tabindex="3"/> บาท.</span>';
			code+='</td></tr>';
			code+='<tr><td colspan="2"><div id="show_stock" style="float:left"></div>';
			code+='<div style="float:right">';
				code+='<input type="submit" name="buttom_1" value="    ตกลง    " onclick="buttom1_onClick();" tabindex="4"/>';
				code+='<button onclick="closeElement(\'formAdd1\');closeElement(\'formAdd2\')" />ยกเลิก</button>';
			code+='</div>';
			code+='</td></tr>';
			code+='</table>';
			code+='</div>';
			// show promotion
			code+='<div id="ob1">';
			if (item.promotion.length > 0) {
				code += '<table>';
				code += '<tr><td>Promotion</td><td>จำนวน</td><td>ราคา</td></tr>';
				for (var i = 0; i < item.promotion.length; i++) {
					code += '<tr><td>' + item.promotion[i]['name'] + '</td><td>' + item.promotion[i]['number'] + '</td>';
					code += '<td>' + item.promotion[i]['price'] + '</td></tr>';
				}
				code += '</table>';
			}	
			code+='</div>';
	
		child_div.innerHTML=code;
		document.getElementById('code').focus();
		document.getElementById('code').focus();
		product = item;
	}
	this.printProduct = function(){
		//alert(this.product.priceUnit);
	}
}

