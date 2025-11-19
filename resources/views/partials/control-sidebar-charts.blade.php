  <aside class="control-sidebar">
	  
	<div class="rpanel-title"><span class="pull-right btn btn-circle btn-danger"><i class="ion ion-close text-white" data-toggle="control-sidebar"></i></span> </div>  <!-- Create the tabs -->
	<ul class="nav nav-tabs control-sidebar-tabs">
	  <li class="nav-item"><a href="#control-sidebar-charts-tab" data-bs-toggle="tab" class="active"><i class="mdi mdi-chart-line"></i></a></li>
	  <li class="nav-item"><a href="#control-sidebar-stats-tab" data-bs-toggle="tab"><i class="mdi mdi-chart-pie"></i></a></li>
	</ul>
	<!-- Tab panes -->
	<div class="tab-content">
	  <!-- Charts tab content -->
	  <div class="tab-pane active" id="control-sidebar-charts-tab">
		  <div class="flexbox">
			<a href="javascript:void(0)" class="text-grey">
				<i class="ti-more"></i>
			</a>	
			<p>Sales Analytics</p>
			<a href="javascript:void(0)" class="text-end text-grey"><i class="ti-plus"></i></a>
		  </div>
		  
		  <!-- Revenue Chart Widget -->
		  <div class="box mt-20">
			<div class="box-header">
				<h4 class="box-title">Revenue</h4>
			</div>
			<div class="box-body">
				<div id="revenue1" style="height: 200px;"></div>
			</div>
		  </div>
		  
		  <!-- Sales Chart Widget -->
		  <div class="box mt-20">
			<div class="box-header">
				<h4 class="box-title">Sales Trend</h4>
			</div>
			<div class="box-body">
				<div id="revenue2" style="height: 200px;"></div>
			</div>
		  </div>
		  
		  <!-- Quick Stats -->
		  <div class="box mt-20">
			<div class="box-body">
				<div class="row">
					<div class="col-12">
						<div class="flexbox mb-10">
							<span>Total Sales</span>
							<span class="text-success fw-600">$24,890</span>
						</div>
						<div class="flexbox mb-10">
							<span>Orders</span>
							<span class="text-info fw-600">1,258</span>
						</div>
						<div class="flexbox mb-10">
							<span>Revenue</span>
							<span class="text-warning fw-600">$18,245</span>
						</div>
						<div class="flexbox">
							<span>Profit</span>
							<span class="text-danger fw-600">$6,645</span>
						</div>
					</div>
				</div>
			</div>
		  </div>

	  </div>
	  <!-- /.tab-pane -->
	  
	  <!-- Stats tab content -->
	  <div class="tab-pane" id="control-sidebar-stats-tab">
		  <div class="flexbox">
			<a href="javascript:void(0)" class="text-grey">
				<i class="ti-more"></i>
			</a>	
			<p>Product Stats</p>
			<a href="javascript:void(0)" class="text-end text-grey"><i class="ti-plus"></i></a>
		  </div>
		  
		  <!-- Product Performance Chart -->
		  <div class="box mt-20">
			<div class="box-header">
				<h4 class="box-title">Performance</h4>
			</div>
			<div class="box-body">
				<div id="revenue3" style="height: 200px;"></div>
			</div>
		  </div>
		  
		  <!-- Top Products List -->
		  <div class="box mt-20">
			<div class="box-header">
				<h4 class="box-title">Top Products</h4>
			</div>
			<div class="box-body">
				<div class="media-list media-list-hover">
					<div class="media py-10 px-0">
						<div class="media-body">
							<p class="fs-16">
								<a class="hover-primary" href="#"><strong>Product A</strong></a>
							</p>
							<p>Sales: $1,245</p>
						</div>
					</div>
					<div class="media py-10 px-0">
						<div class="media-body">
							<p class="fs-16">
								<a class="hover-primary" href="#"><strong>Product B</strong></a>
							</p>
							<p>Sales: $987</p>
						</div>
					</div>
					<div class="media py-10 px-0">
						<div class="media-body">
							<p class="fs-16">
								<a class="hover-primary" href="#"><strong>Product C</strong></a>
							</p>
							<p>Sales: $756</p>
						</div>
					</div>
				</div>
			</div>
		  </div>
		  
		  <!-- Category Distribution -->
		  <div class="box mt-20">
			<div class="box-header">
				<h4 class="box-title">Categories</h4>
			</div>
			<div class="box-body">
				<div id="revenue4" style="height: 150px;"></div>
			</div>
		  </div>
		  
	  </div>
	  <!-- /.tab-pane -->
	</div>
  </aside>
