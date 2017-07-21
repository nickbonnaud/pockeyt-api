<div class="content-wrapper-scroll">
	<div class="scroll-main">
		<div class="scroll-main-contents">
			<section class="content-header">
		    <h1>
		      Refund Center
		    </h1>
		    <ol class="breadcrumb">
		      <li><a href="{{ route('profiles.show', ['profiles' => $user->profile->id])  }}"><i class="fa fa-dashboard"></i> Home</a></li>
		      <li class="active">Refunds</li>
		    </ol>
		  </section>
		  @include ('errors.form')
			<section class="content">
				<div class="scroll-container-analytics">
					<div class="scroll-contents">
						<div class="col-md-6">
							
						</div>

						<div class="col-md-6">
							
						</div>

						<div class="col-md-6">
							
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>


@stop
@section('scripts.footer')
