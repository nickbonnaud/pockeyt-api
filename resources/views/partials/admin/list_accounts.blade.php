@if(count($accounts) > 0)
  @foreach($accounts as $account)
  	<tr class="product-row">
  		<td class="product-row-data">{{ $account->profile->business_name }}</td>
      @if($account->accountEmail)
        <td class="product-row-data">Completed</td>
      @else
        <td class="product-row-data">Incomplete</td>
      @endif

      @if($account->ssn)
        <td class="product-row-data">Completed</td>
      @else
        <td class="product-row-data">Incomplete</td>
      @endif

      @if($account->routing)
        <td class="product-row-data">Completed</td>
      @else
        <td class="product-row-data">Incomplete</td>
      @endif
      <td class="product-row-data">{{ $account->profile->description }}</td>
  		<td>@include('partials.admin.approve_account')</td>
  	</tr>
  @endforeach
@endif