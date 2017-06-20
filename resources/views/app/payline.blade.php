<head>
<title>Veriforms demo</title>
</head>
<body>
  <button id="show-form">Show form</button>
  <pre id="preview"></pre>

  <script type="text/javascript" src="https://vgs-assets.s3.amazonaws.com/payline-1.latest.js"></script>
  <script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function(event) {
      document.getElementById('show-form').addEventListener('click', function() {
        Payline.openTokenizeCardForm({
          applicationName: 'Pollos Hermanos',
          applicationId: 'AP3UKRi9QBmgAjv9v4iKuH7T',
        }, function (tokenizedResponse) {
          document.getElementById('preview').innerText = JSON.stringify(tokenizedResponse, null, '  ');
        });
      });
    });
  </script>


</body>
