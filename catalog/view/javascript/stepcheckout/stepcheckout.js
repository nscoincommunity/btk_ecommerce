function loadfirststep(){
	$.ajax({
	url: 'index.php?route=extension/step1',
	dataType: 'html',
	beforeSend: function() {
		//$('#button-register').button('loading');
	},
	success: function(html){
		$('#loadcontent').html(html);
	},
	error: function(xhr, ajaxOptions, thrownError) {
		alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
	}
});
}
function loadsecondstep(){
	$.ajax({
		url: 'index.php?route=extension/step2',
		dataType: 'html',
		beforeSend: function() {
			//$('#button-register').button('loading');
		},
		success: function(html){
			$('#loadcontent').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}
function loadguestsecondstep(){
	$.ajax({
		url: 'index.php?route=extension/gueststep2',
		dataType: 'html',
		beforeSend: function() {
			//$('#button-register').button('loading');
		},
		success: function(html){
			$('#loadcontent').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}
function loadfourthstep(){
	$.ajax({
		url: 'index.php?route=extension/step3',
		dataType: 'html',
		beforeSend: function(){
			//$('#button-register').button('loading');
		},
		success: function(html){
			$('#loadcontent').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError){
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

function autoselectaddress(){
	var shipping_address = $('input[name="address_same"]:checked').attr('value');
	var address_same = '';
	if(shipping_address==1){
		var address_same = true;
		var payment_address_id =  $('input[name="payment_address_id"]').val();
		var shipping_address_id =  $('input[name="payment_address_id"]').val();
	}else{
		var payment_address_id =  $('input[name="payment_address_id"]').val();
		var shipping_address_id =  $('input[name="shipping_address_id"]').val();
	}
	$.ajax({
		url: 'index.php?route=extension/address/autosave&payment_address_id='+payment_address_id+'&shipping_address_id='+shipping_address_id+'&address_same='+address_same,
		dataType: 'json',
		beforeSend: function() {
			//$('#button-register').button('loading');
		},
		success: function(json){
			loadsecondstep();
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

function autoselectguestaddress(){
	var shipping_address = $('input[name="address_same"]:checked').attr('value');
	var address_same = '';
	if(shipping_address==1){
		var address_same = true;
	}
	
	$.ajax({
		url: 'index.php?route=extension/address/guestautosave&address_same='+address_same,
		dataType: 'json',
		beforeSend: function() {
			//$('#button-register').button('loading');
		},
		success: function(json){
			loadguestsecondstep();
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

function loadshippingmethod(){
	$.ajax({
		url: 'index.php?route=extension/shipping_method',
		dataType: 'html',
		beforeSend: function() {
			//$('#button-register').button('loading');
		},
		success: function(html){
			$('#loadshipping').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}
function editorder(code){
	 $.ajax({
        url: 'index.php?route=extension/step3/save&code='+code,
        type: 'post',
        dataType: 'json',
        success: function(json) {
           if (json['redirect']) {
              location = json['redirect'];
           }
		   
		   if(json['step2']){
			  loadsecondstep();
		   }

		   if(json['step3']){
			  loadfourthstep();
		   }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
}

function cartload(){
	$.ajax({
		url: 'index.php?route=extension/cart',
		dataType: 'html',
		beforeSend: function() {
			//$('#button-register').button('loading');
		},
		success: function(html){
			$('#cartload').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}