function payWithPaystack(paystackkey, email) {
    var handler = PaystackPop.setup({ 
        key: paystackkey, //put your public key here
        email: email, //put your customer's email here
        amount: 100, //amount the customer is supposed to pay
        metadata: {},
        callback: function (response) {
            //using transaction reference as post data
            verifyTransactionOnBackend(response.reference);    
        },
        onClose: function () {
            //when the user close the payment modal
            //alert('Transaction cancelled');
        }
    });
    handler.openIframe(); //open the paystack's payment modal
}
