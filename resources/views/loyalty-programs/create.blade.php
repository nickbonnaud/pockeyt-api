@extends('layoutDashboard')

@section('content')
<div class="content-wrapper-scroll">
	<div class="scroll-main">
		<div class="scroll-main-contents">
			<section class="content-header">
		    <h1>
		      Create your Customer Loyalty Program
		    </h1>
		    <ol class="breadcrumb">
		      <li><a href="{{ route('profiles.show', ['profiles' => $user->profile->id])  }}"><i class="fa fa-dashboard"></i> Home</a></li>
		      <li class="active">Loyalty Program</li>
		    </ol>
		  </section>
		  @include ('errors.form')
			<section class="content">
				<div class="col-md-12">
					<div class="box box-primary">
						<div class="box-header with-border">
							<h3 class="box-title">Program Details</h3>
						</div>
						<div class="box-body">
							<div class="form-group">
								<div class="radio">
									<label>
										<input type="radio" name="optionsRadios" id="increments" value="increments">
										Reward customers once they make a certain number of purchases
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" name="optionsRadios" id="amounts" value="amounts">
										Or reward customers after they have spent a certain amount
									</label>
								</div>
							</div>
							<h4>Number of Purchases required for reward</h4>
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa plus-square"></i>
								</span>
								<input type="number" name="purchases_required" id="purchases_required">
							</div>
							<h4>Total amount customers must spend to receive reward</h4>
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-dollar"></i>
								</span>
								<input type="number" name="amount_required" id="amount_required" pattern="^\\$?(([1-9](\\d*|\\d{0,2}(,\\d{3})*))|0)(\\.\\d{1,2})?$" step="any">
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>


@stop