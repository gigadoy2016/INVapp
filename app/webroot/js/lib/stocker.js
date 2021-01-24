function stocker(){
	this.name = null;
	this.quantity = 0;
	this.pay =0;
	this.balance = 0;
	this.group = new Array();
	
	this.add = function(items){
		for(var i=0 ; i <= items.length; i++){
			if(items[i].inventoryStock>0){
				this.group.push(items[i]);
				alert(items[i].serial);
			}
		}
	}
	this.showCase = function(){	}
}