/**
 * @author xan
 * version 1
 * create 3/7/2014
 */
 
 /*   file used ( pos.thtml )   */
//================================================
//		Function key number only
//================================================
function blockNaN(ele){
	var vchar = String.fromCharCode(event.keyCode);

	if ((vchar<'0' || vchar>'9') && (vchar != '.') && (event.keyCode != 13)) return false;

	ele.onKeyPress=vchar;
}

//  เพิ่มลูกน้ำในหลักพัน ของคัวเลข  
function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

function date_time(id){
        date = new Date;
        year = date.getFullYear();
        month = date.getMonth();
        months = new Array('มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฏาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม');
        d = date.getDate();
        day = date.getDay();
        days = new Array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        h = date.getHours();
        if(h<10)
        {
                h = "0"+h;
        }
        m = date.getMinutes();
        if(m<10)
        {
                m = "0"+m;
        }
        s = date.getSeconds();
        if(s<10)
        {
                s = "0"+s;
        }
        result = ''+days[day]+' '+months[month]+' '+d+' '+year+' '+h+':'+m+':'+s;
        document.getElementById(id).innerHTML = result;
        setTimeout('date_time("'+id+'");','1000');
        return true;
}

function dumpO(arr,level) {
   var dumped_text = "";
   if(!level) level = 0;
   
   //The padding given at the beginning of the line.
   var level_padding = "";
   for(var j=0;j<level+1;j++) level_padding += "    ";
   
   if(typeof(arr) == 'object') { //Array/Hashes/Objects 
      for(var item in arr) {
         var value = arr[item];
         
         if(typeof(value) == 'object') { //If it is an array,
            dumped_text += level_padding + "'" + item + "' ...\n";
            dumped_text += dump(value,level+1);
         } else {
            dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
         }
      }
   } else { //Stings/Chars/Numbers etc.
      dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
   }
   return dumped_text;
}

function dump(arr) {
   var dumped_text = "";
   if(arr == null) return "No data";
   for(var i=0 ; arr.length >i ; i++){
		var promotion = arr[i].promotion;
   
		dumped_text += "item["+i+"] : { \n";
		dumped_text += "\t \t  [name]:"+arr[i].name +", [product_id]:"+arr[i].product_id +", [quantity]:"+arr[i].quantity +" ,[promotion_id]:"+arr[i].promotion_id+", [price]: "+arr[i].priceUnit+"}\n";

		/*for(var j=0 ; promotion.length >j ; j++){
			dumped_text += "\t \t \t \t  [promotion]:"+promotion[j].name +", [จำนวน]:"+promotion[j].number +"[ราคา]:"+promotion[j].price +"}\n";
		}*/
   }
   return dumped_text;
}

function dump2(product){
	var text = "[name]:"+product.name +
					"\n, [product_id]:"+product.product_id +
					"\n, [quantity]:"+product.quantity +
					"\n ,[promotion_id]:"+product.promotion_id+
					"\n, [price]: "+product.priceUnit+
					"\n, [code]: "+product.code;
	return text;
}