@if(count($accounts) > 0)
  @foreach($accounts as $account)
  	<tr class="product-row">
  		<td class="product-row-data">{{ $account->profile->business_name }}</td>
      @if($account->accountEmail)
        <td class="product-row-data"><span class="label label-success">Complete</span></td>
      @else
        <td class="product-row-data"><span class="label label-danger">Incomplete</span></td>
      @endif

      @if($account->ssn)
        <td class="product-row-data"><span class="label label-success">Complete</span></td>
      @else
        <td class="product-row-data"><span class="label label-danger">Incomplete</span></td>
      @endif

      @if($account->routing)
        <td class="product-row-data"><span class="label label-success">Complete</span></td>
      @else
        <td class="product-row-data"><span class="label label-danger">Incomplete</span></td>
      @endif
      <td class="product-row-data">{{ $account->profile->description }}</td>
  		<td><button v-on:click="selectedAccount = {{ $account }}" class="btn btn-block btn-success btn-sm" data-toggle="modal" data-target="#mccModal">Approve</button></td>
  	</tr>
  @endforeach
@endif