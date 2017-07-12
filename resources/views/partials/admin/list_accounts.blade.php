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
  		<td><button class="btn btn-block btn-success btn-sm" data-toggle="modal" data-target="#mccModal">Approve</button></td>
  	</tr>
    <div class="modal fade" id="mccModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header-timeline">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="mccModal">Select Business MCC</h4>
        </div>
        <div class="modal-body">
          <div class="modal-body-customer-info">
            <p>{{ $account->id }}</p>
          </div>
        </div>
      </div>
    </div>
  @endforeach
@endif