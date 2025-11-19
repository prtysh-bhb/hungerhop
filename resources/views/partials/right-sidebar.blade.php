 <div class="right-bar">
	  <div id="sidebarRight">
		  <div class="right-bar-inner">
			
			  <div class="text-end position-relative">
				  <a href="#" class="d-inline-block d-xl-none btn right-bar-btn waves-effect waves-circle btn btn-circle btn-danger btn-sm">
					<i class="mdi mdi-close"></i>
				  </a>
			  </div>
			  <div class="right-bar-content">
				  <div class="box no-shadow box-bordered border-light">
				  	  <div class="box-body">
					  	  <div class="d-flex justify-content-between align-items-center">
							  <div>
								  <h5>Total Orders assigned for delivery</h5>
								  <h2 class="mb-0">{{ isset($sidebarData['preparing_orders']) ? $sidebarData['preparing_orders'] : 0 }}</h2>
							  </div>
							  <div class="p-10">
							  	<div id="chart-spark1"></div>	
							  </div>
						  </div>
					  </div>
					  <div class="box-footer">
					  	  <div class="d-flex align-items-center justify-content-between">
						  	  <h5 class="my-0">{{ isset($sidebarData['preparing_orders']) ? $sidebarData['preparing_orders'] : 0 }} total orders</h5>
							  <a href="#" class="mb-0">View Report</a>
						  </div>
					  </div>
				  </div>
				  <div class="box no-shadow box-bordered border-light">
				  	  <div class="box-body">
					  	  <div class="d-flex justify-content-between align-items-center">
							  <div>
								  <h5>Total Orders Out for Delivery</h5>
								  <h2 class="mb-0">{{ isset($sidebarData['out_for_delivery_orders']) ? $sidebarData['out_for_delivery_orders'] : 0 }}</h2>
							  </div>
							  <div class="p-10">
							  	<div id="chart-spark2"></div>
							  </div>
						  </div>
					  </div>
					  <div class="box-footer">
					  	  <div class="d-flex align-items-center justify-content-between">						  	  
							  <a href="#" class="btn btn-primary-light btn-sm">Live</a>						  	  
							  <a href="#" class="btn btn-info-light btn-sm">4 Visitors</a>						  	  
							  <a href="#" class="btn btn-success-light btn-sm">See Live View</a>
						  </div>
					  </div>
				  </div>
				  {{-- <div class="box no-shadow box-bordered border-light">
				  	  <div class="box-body">
					  	  <div class="d-flex justify-content-between align-items-center">
							  <div>
								  <h5>Customer rate*</h5>
								  <h2 class="mb-0">5.12%</h2>
							  </div>
							  <div class="p-10">
							  	<div id="chart3"></div>
							  </div>
						  </div>
					  </div>
					  <div class="box-footer">
					  	  <div class="d-flex align-items-center justify-content-between">						  	  
							  <h5 class="my-0"><span class="badge badge-xl badge-dot badge-primary me-10"></span>First Time</h5>								  	  
							  <h5 class="my-0"><span class="badge badge-xl badge-dot badge-danger me-10"></span>Returning</h5>						  	  		
						  </div>
					  </div>
				  </div> --}}
				  <div class="box no-shadow box-bordered border-light">
					  <div class="box-header">
					  	<h4 class="box-title">Recent Activity</h4>
					  </div>
				  	  <div class="box-body p-5">
					  	  <div class="media-list media-list-hover">
							@if(isset($sidebarData['recent_activities']) && $sidebarData['recent_activities']->count() > 0)
								@foreach($sidebarData['recent_activities'] as $activity)
								<a class="media media-single mb-10 p-0 rounded-0" href="#">
								<h3 class="w-50 text-gray fw-500">
									{{ $activity['time']->addHours(5)->addMinutes(30)->format('g:i') }}
								</h3>
								  <div class="media-body ps-15 bs-5 rounded {{ $activity['border_class'] }}">
									<p>{{ $activity['message'] }}</p>
									<span class="text-fade">by {{ $activity['user'] }}</span>
								  </div>
								</a>
								@endforeach
							@else
								<a class="media media-single mb-10 p-0 rounded-0" href="#">
								  <h4 class="w-50 text-gray fw-500">{{ now()->format('g:i A') }}</h4>
								  <div class="media-body ps-15 bs-5 rounded border-primary">
									<p>No recent activities found.</p>
									<span class="text-fade">by System</span>
								  </div>
								</a>
							@endif
						  </div>
					  </div>
					  {{-- <div class="box-footer">
					  	  <div class="text-center">						  	  
							  <a href="#" class="mb-0">Load More</a>					  	  		
						  </div>
					  </div> --}}
				  </div>
			  </div>
		  </div>
	  </div>
  </div>    