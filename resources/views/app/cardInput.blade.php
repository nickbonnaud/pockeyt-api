<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0' >
  <title>Card Info</title>
</head>
<body>
  <h1 style="left: 0; line-height: 200px; margin-top: -100px; position: absolute; text-align: center; top: 50%; width: 100%;">Loading...</h1>
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
  <script type="text/javascript" src="https://test-api.splashpayments.com/paymentScript"></script>
  <script>
    PaymentFrame.onSuccess = function (response) {
      var customer = res.response.data[0].customer;
      var payment = res.response.data[0].payment;
      console.log('success');
    };
   
    PaymentFrame.onFailure = function (response) {
      window.location.replace("mobile/close/fail");
    };

    PaymentFrame.config.apiKey = "6c5efd94b04e7ddc049ac0147c0fab01";
    PaymentFrame.config.mode = "token";
    PaymentFrame.config.name = "Pockeyt Card Vault";
    PaymentFrame.config.description = "Address & Phone Optional";
    PaymentFrame.config.billingAddress = { email: "test@email.com" };
    PaymentFrame.config.image = "https://pockeytbiz.com/images/pockeyt-icon-square.png";

    document.addEventListener("DOMContentLoaded", function(event) {
      PaymentFrame.popup();
    });

    sendResults = function(customer, payment) {
      $.ajax({
        method: 'POST',
        url: '/api/vault/card',
        data: {
          'cardType' : payment.method,
          'number' : payment.number,
          'userId' : '{{ $authUser->id }}'
        },
        success: function(data) {
          console.log(data);
          if (data == true) {
            window.location.replace("mobile/close/success");
          } else {
            window.location.replace("mobile/close/fail");
          }
        },
        error: function(data) {
          console.log(data);
        }
      })
    };
  </script>
</body>
</html>
