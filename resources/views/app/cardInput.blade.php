<head>
<title>Card Info</title>
</head>
<body>
  <h1 style="left: 0; line-height: 200px; margin-top: -100px; position: absolute; text-align: center; top: 50%; width: 100%;">Loading...</h1>
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
  <script type="text/javascript" src="https://test-api.splashpayments.com/paymentScript"></script>
  <script>
   PaymentFrame.config.sessionKey = "6c5efd94b04e7ddc049ac0147c0fab01";
  PaymentFrame.config.mode = "token";
  PaymentFrame.config.billingAddress = {
    address: "",
    city: "",
    state: "",
    zip: "",
    email: "test@email.com",
    phone: ""
  };
  
  document.addEventListener("DOMContentLoaded", function(event) {
    PaymentFrame.popup();

    PaymentFrame.config = {
      onSuccess: function (response) {
        console.log(response);
      },
      onFailure: function (response) {
        console.log(response);
      },
      onFinish: function (response) {
        console.log(response);
      }
    };




    
  });
  </script>


</body>
