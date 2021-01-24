/**
 * @author xan
 * version 1.1
 * edit 20/06/2557
 */
function Bill(){
    //property
    this.name = 'Bill';
	this.bill_id = '';
    this.item = new Array(); //real order
    this.itemGroupType = new Array(); //sum order
    this.discount= null;
    
    this.result;
	this.customer=0;
	this.collector=0;
	this.detail=null;
	this.licence='';
	
	this.printOrder ='';
    
    this.ElementId = 'I_order_list_product';
    this.resultForm= 'I_price_summary';
	
	this.status =0;
    
    //method add Item
    this.addItem = function(item) {
    	this.item.push(item); // Add data to Array.
    }
    
    //รวม Item ชนิดเดี่ยวกัน
    this.arrageItem =function(item){
    	var status =0;
    	for(var i=0;i<this.item.length;i++){
    		if(this.item[i].getProductID()==item.getProductID() && 	this.item[i].priceUnit==item.priceUnit){
    			this.item[i].quantity =parseFloat(this.item[i].quantity)+parseFloat(item.quantity);
    			status =1;
    		}
    	}
    	if(status==0){
    		this.item.push(item);// Add data to Array.
    	}
		
    }
    //่รวมItem type เดี่ยวกัน
    this.arrageOrder =function(arr){
		var buffer= new Array();
		
    	for(var j=0;arr.length>j;j++){
			
        	var x = new Item(arr[j].name, arr[j].quantity, arr[j].priceUnit, arr[j].pic, arr[j].serial, arr[j].cost	);	//frist in frist out.
        	x.product_id 		= arr[j].product_id ;
        	x.promotion_id 	= arr[j].promotion_id;
        	x.promotion 		= arr[j].promotion;
			x.priceUnit			= arr[j].priceUnit;
			x.quantity			= arr[j].quantity;
			x.name				= arr[j].name;
			x.pic					= arr[j].pic;
			x.serial				= arr[j].serial;
			x.cost				= arr[j].cost;
			
			//alert("name:"+x.name +" price:"+x.priceUnit+ "promotion :"+x.promotion.length);
        	var length= buffer.length;
        	var status =0;
        	
	        if(buffer.length==0){
    	        buffer.push(x);			//frist item push in stack.
        	}else{
            	for(i=0 ;length >i;i++){
					if( (buffer[i].promotion_id == x.promotion_id)  && (buffer[i].serial == x.serial)	&& (buffer[i].priceUnit == x.priceUnit)	)
					{
						buffer[i].quantity =parseFloat(buffer[i].quantity)+parseFloat(arr[j].quantity);
                		status =0;
                    	break;
                    }
						else
					{
                    	buffer[i].code ='MIX';
                    	buffer[i].product_id =buffer[i].type_id+'MIX';
                    	status =1;
                    }
            	}
           		if(status==1){
                	buffer.push(x);
            	}
        	}

        }
		
        this.itemGroupType = buffer;
        return buffer;
	}
	this.refresh=function(){
		this.arrageOrder(this.item);
		return this.checkPromotion();
	}
	
    this.checkPromotion=function(){
		oList=new Array();
		items=this.itemGroupType;
		for(i=0 ;items.length >i;i++){
			itemA=new Item(items[i].name,items[i].quantity,items[i].priceUnit,null,items[i].serial,items[i].cost);
			itemA.name = items[i].name;
			itemA.quantity = items[i].quantity;
			itemA.priceUnit = items[i].priceUnit;
			itemA.pic = null;
			itemA.serial = items[i].serial;
			itemA.cost = items[i].cost;
			itemA.promotion_id = items[i].promotion_id;

			promotionsItem =itemA.promotion= items[i].promotion;
			//text ="name:"+itemA.name +" price:"+itemA.priceUnit+ "promotion :"+ itemA.promotion_id; 
			//alert(promotionsItem.length);
			
			if(promotionsItem !=null){
				for(j=0 ;promotionsItem.length >j;j++ ){
					if (parseFloat(itemA.quantity) >= parseFloat(promotionsItem[j]['number'])) {
						var cost	= itemA.cost * promotionsItem[j]['number'];
						var doz	= Math.floor(itemA.quantity / promotionsItem[j]['number']);
						itemA.quantity -= doz * promotionsItem[j]['number'];
						name = items[i].name +" ["+ promotionsItem[j]['name']+"]";
						itemB = new Item(name,doz,promotionsItem[j]['price'],null,itemA.serial,cost);
						itemB.name = name;
						itemB.quantity = doz;
						itemB.priceUnit = promotionsItem[j]['price'];
						itemB.serial = itemA.serial;
						itemB.cost = cost;
						itemB.promotion_id = promotionsItem[j]['id'];
						itemB.product_id = items[i].product_id;
						oList.push(itemB);
					}
				}
			}
			if(itemA.quantity >0){
				itemB = new Item(itemA.name,itemA.quantity,itemA.priceUnit,null,items[i].serial,items[i].cost);
				itemB.name = itemA.name;
				itemB.quantity = itemA.quantity;
				itemB.priceUnit = itemA.priceUnit;
				itemB.serial = items[i].serial;
				itemB.cost = items[i].cost;
				
				itemB.product_id = items[i].product_id;
				
				oList.push(itemB);
			}
		}
		return this.discount = oList;
	}
	
    /*
    * Item summary
    */
    this.summary = function(data){
        var sum =0;
        for(var i=0; i < data.length ;i++){
            sum += data[i].summary();
			//alert("data["+i+"].priceUnit =" + data[i].priceUnit);
        }
        return sum.toFixed(2);
    }  
      
    this.orderList = function(data,type){
		this.refresh();
		
    	if(type=='item')
			buttom='buttom_deleteItem_onClick';
		else if(type=='itemGroupType')
			buttom='buttom2_deleteItem_onClick';
		
        var code ='';
        for(var i=0; i<data.length ;i++){
            code +='<div>';
            code+='<div class="I_order_list_product_1" id="IOLP1">'+(i+1)+'</div>';
            code+='<div class="I_order_list_product_1" id="IOLP2">';
			code+='<span style="width:90%">'+data[i].name;
			if(data[i]['code']!='')
				code+='-'+data[i].code;
			if(data[i].unitName!=null)
				code+=' [<span style="color:red">'+data[i].unitName+'</span>]';
			code+='</span>';
			if (type != 'promotion') {
				code += '<span style="width:10%;text-align:right">';
				code += '<a href="#" onclick=\'buttom_deleteItem_onClick(' + i + ')\' title="ลบ"><img src="img/delete.gif"></a></span>';
			}
			code+='</div>';
            code+='<div class="I_order_list_product_1" id="IOLP3">'+data[i].priceUnit+'</div>';
            code+='<div class="I_order_list_product_1" style="width:4px;border-width:0 0 1px 0;">x</div>';
            code+='<div class="I_order_list_product_1" id="IOLP4">';
			code+=data[i].quantity;
			//if(data[i].unitName!=null)
				//code+=' '+data[i].unitName;
			code+='</div>';
            code+='<div class="I_order_list_product_1" id="IOLP5" ';
			if(type=='promotion'){
				code+='onclick="editValue('+i+');"';	
			}
			code+='>'+data[i].summary()+'</div>';
            code+='</div>';
			//alert("t="+type+"จำนวน"+data[i].quantity+"---"+data[i].getCost());
        }

		var result =this.summary(data);
        code+='<div><div class="I_order_list_product_summary">'+result+'</div></div>';
        document.getElementById(this.ElementId).innerHTML = code;
        document.getElementById(this.resultForm).innerHTML = this.result =result;
        
        
    }
	this.orderList2 = function(){
        var code ='';
		var data = this.discount;
        for(var i=0; i<data.length ;i++){
            code +='<div>';
            code+='<div class="I_order_list_product_1" id="IOLP1">'+(i+1)+'</div>';
            code+='<div class="I_order_list_product_1" id="IOLP2">';
			code+='<span style="width:90%">'+data[i].name;
			if(data[i]['code']!='')
				code+='-'+data[i].code;
			if(data[i].unitName!=null)
				code+=' [<span style="color:red">'+data[i].unitName+'</span>]';
			code+='</span>';
			code+='</div>';
            code+='<div class="I_order_list_product_1" id="IOLP3">'+data[i].priceUnit+'</div>';
            code+='<div class="I_order_list_product_1" style="width:4px;border-width:0 0 1px 0;">x</div>';
            code+='<div class="I_order_list_product_1" id="IOLP4">';
			code+=data[i].quantity;
			code+='</div>';
            code+='<div class="I_order_list_product_1" id="IOLP5" ';
			code+='onclick="editValue('+i+');"';	
			code+='>'+data[i].summary()+'</div>';
            code+='</div>';
        }

		var result =this.summary(data);
        code+='<div><div class="I_order_list_product_summary">'+result+'</div></div>';
        document.getElementById(this.ElementId).innerHTML = code;
        document.getElementById(this.resultForm).innerHTML = this.result =result;
        
        
    }	
    this.deleteItem =function(index){
		this.item.splice(index,1);
    }
    this.deleteItemInOrder =function(index){
    	var type_id =this.itemGroupType[index].serial;
    	var promotion_id =this.itemGroupType[index].promotion_id;
    	
    	for(i=0;this.item.length>i;i++){
    		if((type_id==this.item[i].serial) && (promotion_id==this.item[i].promotion_id)){
    			this.deleteItem(i);
				i=-1;
    		}
    	}
		this.itemGroupType.splice(index,1);
		this.checkPromotion();
    }

    this.postData = function(){
    	var param='';
    	for(var i=0;i<this.item.length;i++){
    		param +=i+'[product_id]='+this.item[i].product_id+'&';
    		param +=i+'[type_id]='+this.item[i].serial+'&';
    		param +=i+'[name]='+this.item[i].name+'&';
    		param +=i+'[code]='+this.item[i].code+'&';
    		param +=i+'[quantity]='+this.item[i].quantity+'&';
    		param +=i+'[priceUnit]='+this.item[i].priceUnit+'&';
			param +=i+'[cost]='+this.item[i].getCost()+'&';
    		param +=i+'[sum]='+this.item[i].summary()+'&';
    	}
    	return param;
    }
    this.postDataProduct = function(){
    	var param='';
		this.result =this.summary(this.discount);
    	for(var i=0;i<this.discount.length;i++){
    		param +='order['+i+'][product_id]='+this.discount[i].product_id+'&';
    		param +='order['+i+'][type_id]='+this.discount[i].serial+'&';
    		param +='order['+i+'][name]='+this.discount[i].name+'&';
    		param +='order['+i+'][code]='+this.discount[i].code+'&';
    		param +='order['+i+'][quantity]='+this.discount[i].quantity+'&';
    		param +='order['+i+'][priceUnit]='+this.discount[i].priceUnit+'&';
			param +='order['+i+'][sum]='+this.discount[i].summary()+'&';
			param +='order['+i+'][cost]='+this.discount[i].getCost()+'&';
			param +='order['+i+'][promotion_id]='+this.discount[i].promotion_id+'&';
    	}

    	for(var i=0;i<this.item.length;i++){
    		param +='product['+i+'][product_id]='+this.item[i].product_id+'&';
    		param +='product['+i+'][type_id]='+this.item[i].serial+'&';
    		param +='product['+i+'][name]='+this.item[i].name+'&';
    		param +='product['+i+'][code]='+this.item[i].code+'&';
    		param +='product['+i+'][quantity]='+this.item[i].quantity+'&';
    		param +='product['+i+'][priceUnit]='+this.item[i].priceUnit+'&';
    		param +='product['+i+'][sum]='+this.item[i].summary()+'&';
			param +='order['+i+'][stock]='+this.item[i].inventoryStock+'&';
    	}
		param +='bill[bill_id]='+this.bill_id+'&';
		param +='bill[collector]='+this.collector+'&';
		param +='bill[detail]='+this.detail+'&';
		param +='bill[licence]='+this.licence+'&';    	
		param +='bill[customer]='+this.customer+'&';
    	param +='bill[result]='+this.result+'&';
		param +='bill[status]='+this.status;
    	return param;
    }
	
	this.newList = function(data,type){
		//this.refresh();
    	if(type=='item')
			buttom='buttom_deleteItem_onClick';
		else if(type=='itemGroupType')
			buttom='buttom2_deleteItem_onClick';
		
        var code ='';
        for(var i=0; i<data.length ;i++){
            code +='<div>';
            code+='<div class="I_order_list_product_1 IOLP1">'+(i+1)+'</div>';
			code+='<div class="I_order_list_product_1 IOLP2">';
			code+='<span style="width:90%">'+data[i].name;
			
			if(data[i]['code']!='')
				code+='-'+data[i].code;
			//if(data[i].unitName!=null )
				//code+=' [<span style="color:red">'+data[i].unitName+'</span>]';
			code+=' </span>';
			if (type != 'promotion') {
				code += '<span style="width:10%;text-align:right"> ';
				code += '<a href="#" onclick=\'buttom_deleteItem_onClick(' + i + ')\' title="ลบ"><img src="../img/delete.gif"></a></span>';
			}
			code+='</div>';
			var price = parseFloat(data[i].priceUnit).toFixed(2);
            code+='<div class="I_order_list_product_1 IOLP3" onclick="editValue('+i+')">'+price+'</div>';
            code+='<div class="I_order_list_product_1" style="width:4px;border-width:0 0 1px 0;">x</div>';
            code+='<div class="I_order_list_product_1 IOLP4">';
			code+=data[i].quantity;
			//if(data[i].unitName!=null)
				//code+=' '+data[i].unitName;
			code+='</div>';
            code+='<div class="I_order_list_product_1 IOLP5" ';
			if(type=='promotion'){
				code+='onclick="editValue('+i+');"';	
			}else{
				code+='onclick="editValueItem('+i+');"';
			}
			code+='>'+parseFloat(data[i].summary()).toFixed(2)+'</div>';
            code+='</div>';
			//alert("t="+type+"จำนวน"+data[i].quantity+"---"+data[i].getCost());
		}
		var result =this.summary(data);
		if(data.length > 0){
			code+='<div><div class="I_order_list_product_summary" >'+result+'</div></div>';
		}
		return code;
	}
	
	this.display = function(){
		code	="<div style='border:1px solid gray;width:300px'>"; 
		code +="<H3>item</H3>";
		code	+="<table>";
		code +="<tr><th>no.</th><th>name</th><th>รหัส</th><th>ราคา</th><th>จำนวน</th></tr>";
		for(var i=0; i<this.item.length ;i++){
			code += "<tr><td>"+(i+1)+"</td><td>"+this.item[i].name+"</td><td>"+this.item[i].code+"</td><td>"+this.item[i].priceUnit+"</td><td>"+this.item[i].quantity+"</td></tr>";
		}
		code += "</table>";
		code +="</div>";
		
		return code;
	}
	
	this.cuttingStock = function(){
		var group = new stocker();
		group.add(this.item);
		return group.showCase();
	}
	
	this.print = function(){
		var products = this.discount;
		var d = new Date();
		var year =  parseInt(d.getFullYear()) +543;
		var time = d.getHours() +':' +d.getMinutes() + ':'+ d.getSeconds()+'.';
		
		//print   ='<div class="p_zone">------------------------------------------------------------</div>';
		print ='';
		print +='<div class="p_zone"><h2>ร้านบัวเงิน (02) 916-1133</h2> ตลาดเกรียงไกร เคหะร่มเกล้า ลาดกระบัง</div>';
		print +='<div class="p_zone" >วันที่ '+d.getDate() +"/"+d.getMonth() +"/" + year+' เวลา -'+time+'</div>';
		print +='<div class="p_zone">Bill No. '+this.bill_id+' </div>';
		print +='<div class="p_zone">...............................................................................</div></br></br>';
		print += '<table class="p_zone">';
		
		for(var i=0; i< products.length ;i++){
			print+='<tr>';
			print+='<td style="width:20px"> '+products[i].quantity+'x</td>';
			print+='<td class="p_nameItem"> '+products[i].name+'</td>';
			print+='<td style="text-align:right;padding-right: 50px;">'+parseFloat(products[i].summary()).toFixed(2)+'</td>';
			print+='</tr>';			
		}
		var sum = parseFloat(this.summary(products)).toFixed(2);       //ราคาที่ต้องจ่าย
		var cash = parseFloat($("#payment_text").val()).toFixed(2);     //รับเงินสดมา
		var pay = parseFloat(cash - sum).toFixed(2);    						// เงินทอน
		
		print +='<tr><td colspan="3">-----------------------------------------------------------</td></tr>';
		print += '<tr style="font-size:150%"><td></td><td>รวม</td><td style="text-align:right;padding-right: 50px;">'+sum+' </td></tr>';
		print += '<tr><td></td><td>เงินสด</td><td style="text-align:right;font-size:120%;padding-right: 50px;">'+cash+' </td></tr>';
		print +='<tr><td colspan="3">===================================</td></tr>';
		print += '<tr style="font-size:150%"><td></td><td>เงินทอน</td><td style="text-align:right;padding-right: 50px;">'+pay+' </td></tr>';
		print +='<tr><td colspan="3">===================================</td></tr>';
		print += '</table>';
		print += '<br>';
		print += '<br>';
		return print;
	}
}

function stocker(){
	this.name = null;
	this.quantity = 0;
	this.pay =0;
	this.balance = 0;
	this.group = new Array();
	
	this.add = function(items){
		for(var i=0 ; i < items.length; i++){
			if(items[i].inventoryStock>0){
				this.group.push(items[i]);
			}
		}
	}
	
	this.balance = function(i){
		return parseInt(this.group[i].inventoryStock) - parseInt(this.group[i].quantity);
	}
	this.showCase = function(){
		if (this.group.length <= 0){return '';}
		
		var text = '<table class="payment_stock"><th><td >Stock</td><td >ออก</td><td >เหลือ</td></th>';
		for(var i=0 ;i < this.group.length ; i++){
			text += '<tr><td>'+this.group[i].name+':'+this.group[i].code+'</td><td>'+this.group[i].inventoryStock+'</td><td>'+this.group[i].quantity+'</td><td>'+this.balance(i)+'</td></tr>'
		}
		text +=	'</table>';
		return text;
	}
}