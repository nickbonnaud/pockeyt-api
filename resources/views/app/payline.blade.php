@extends('layout')
@section('content')


@stop
<script type="text/javascript" src="https://vgs-assets.s3.amazonaws.com/payline-1.latest.js"></script>

<script type="text/javascript">
  document.addEventListener("DOMContentLoaded", function(event) {
    Payline.openTokenizeCardForm({
      applicationName: 'Test',
      applicationId: 'AP3UKRi9QBmgAjv9v4iKuH7T',
    }, function (tokenizedResponse) {
      document.getElementById('preview').innerText = JSON.stringify(tokenizedResponse, null, '  ');
    });
  });
</script>
