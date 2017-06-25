<head>
<title>Card Info</title>
</head>
<body>
  <h1 class="loading-payline">Loading...</h1>
  <script type="text/javascript" src="https://vgs-assets.s3.amazonaws.com/payline-1.latest.js"></script>
  <script src="{{ asset('/vendor/jquery/jquery-1.12.0.min.js') }}"></script>
  <script type="text/javascript">
  document.addEventListener("DOMContentLoaded", function(event) {
    Payline.openTokenizeCardForm({
      applicationName: 'Secure Card Vault',
      applicationId: 'AP3UKRi9QBmgAjv9v4iKuH7T',
    }, function (tokenizedResponse) {
      $.ajax({
        method: 'POST',
        url: '/api/payline',
        data: {
          'tokenId' : tokenizedResponse.id,
          // 'userId' : '{{ $authUser->id }}'
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
    });
  });
  </script>


</body>
