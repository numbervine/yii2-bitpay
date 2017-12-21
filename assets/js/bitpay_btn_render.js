/**
 * @license Copyright 2017 Thomas Varghese, MIT License
 * see https://github.com/numbervine/yii2-bitpay/blob/master/LICENSE
 */
$(function() {

	$('button#bitpay-btn').click(function(e) {

		$('#bitpay-pre-modal').modal('show');

		paymentOption = 'bitpay';
		var post_data = JSON.stringify({
	    	'invoice_id': invoiceId,
	    	'customer_id': clientId,
	    	'amount': invoiceAmount
	    });

    $.post(createInvoiceAction, {'data' : post_data}, function(data) {
			if (data.success) {
    		bitpayInvoiceId = data.payload.bitpay_invoice.id;
    		bitpay.showInvoice(bitpayInvoiceId);
    	} else {
    		swal("Bitpay Error", data.message, "error");
    	}
    });
	});

	$('#bitpay-pre-modal button.close[data-dismiss="modal"]').click(function(e) {
		bitpay.hideFrame();
	});

	bitpay.onModalWillEnter(function() {
		$('#bitpay-pre-modal').modal('hide');
	});

	bitpay.onModalWillLeave(function() {

		$('#bitpay-post-modal').modal('show');

		var post_data = JSON.stringify({
    	'invoice_id': invoiceId,
    	'customer_id': clientId,
    	'amount': invoiceAmount,
    	'bitpay_invoice_id': bitpayInvoiceId
    });

    $.post(queryInvoiceAction, {'data' : post_data}, function(data) {
    	if (data.success) {
    		// if ((data.invoiceStatus=='paid' || data.invoiceStatus=='confirmed' || data.invoiceStatus=='complete') && (data.invoiceExceptionStatus=='false' || data.invoiceExceptionStatus=='paidOver' || data.invoiceExceptionStatus=='paidLate') ) {
				//
    		// }
				// TODO: confirm/cross check with server before next action
    	} else {
    		swal("Bitpay Error", data.message, "error");
    	}
    });


    var checkMsgDivTimer = 0;
    var loops = 0;
    checkMsgDivTimer = window.setInterval(function(){
			loops++;

			if (loops > 300) {
				clearInterval(checkMsgDivTimer);
				$('#bitpay-post-modal').modal('hide');
				// redirect to timed out page
			}

			if ($('div.post-payment-status-update-msg').is(':visible')) {
				clearInterval(checkMsgDivTimer);
				$('#bitpay-post-modal').modal('hide');
			}
		},1000);

	});



//	  window.addEventListener("message", function(event) {
////document.getElementById("s1").innerHTML=event.data.status;
//
//var buff = JSON.stringify(event.data);
//	alert(buff);
//
//if (event.data.status=='new') {
//
//} else if (event.data.status=='paid') {
//
//} else if (event.data.status=='confirmed') {
//
//} else if (event.data.status=='complete') {
//
//} else if (event.data.status=='expired') {
//
//} else if (event.data.status=='invalid') {
//
//}
//
//}, false);

});
