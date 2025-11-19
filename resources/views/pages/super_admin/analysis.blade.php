@extends('layouts.admin')

@section('title', 'Analysis')

@section('content')
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="d-flex align-items-center">
				<div class="me-auto">
					<h4 class="page-title">Analysis</h4>
					<div class="d-inline-block align-items-center">
						<nav>
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
								<li class="breadcrumb-item" aria-current="page">Analytics</li>
								<li class="breadcrumb-item active" aria-current="page">Analysis</li>
							</ol>
						</nav>
					</div>
				</div>
				<div class="text-end">
					<button type="button" class="btn btn-info" data-toggle="control-sidebar"><i class="fa fa-chart-pie"></i> More Charts</button>
				</div>
			</div>
		</div>

		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-lg-6 col-12">
					<div class="box">
						<div class="box-header">
							<h4 class="box-title">Company Analytics</h4>
						</div>
						<div class="box-body">
							<div class="d-flex justify-content-between align-items-center">
							  <div class="user-analytics">
								<i class="me-5 mdi mdi-account"></i>
								<span class="text-muted">Users</span>
								<div class="d-flex align-items-center">
								  <h3>61K</h3>
								</div>
							  </div>
							  <div class="sessions-analytics">
								<i class="me-5 mdi mdi-arrow-up"></i>
								<span class="text-muted">Sessions</span>
								<div class="d-flex">
								  <h3>92K</h3>
								</div>
							  </div>
							  <div class="bounce-rate-analytics">
								<i class="me-5 mdi mdi-chart-pie"></i>
								<span class="text-muted">Bounce Rate</span>
								<div class="d-flex">
								  <h3>72.6%</h3>
								</div>
							  </div>
							</div>
							<div id="analytics-bar-chart"></div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-12">					
					<div class="box">
						<div class="box-header">
							<h4 class="box-title">Daily Sales Overview</h4>
						</div>
						<div class="box-body">
							<div id="balancehistory"></div>
						</div>
					</div>
				</div>
				<div class="col-12">
					<div class="box bg-gradient-primary">						
						<div class="box-body">
							<div class="row">
								<div class="col-xl-6 col-12">
									<h4 class="text-white mb-50">Revenue Overview </h4>
									<div class="d-flex justify-content-between align-items-end">
										<div class="d-flex">
											<div class="icon">
												<i class="fa fa-trophy"></i>
											</div>
											<div>
												<h3 class="fw-600 text-white mb-0 mt-0">34040</h3>
												<p class="text-white-50">Revenue</p>
												<h5 class="text-white">+34040 <span class="ms-40"><i class="fa fa-angle-down me-10"></i><span class="text-white-50">0.036%</span></span> </h5>
											</div>
										</div>
										<div>
											<div id="apexChart2" class="mx-50"></div>
										</div>
									</div>
								</div>
								<div class="col-xl-6 col-12">
									<h4 class="text-white mb-50 mt-xl-0 mt-60">Sales Overview </h4>
									<div class="d-flex justify-content-between align-items-end">
										<div class="d-flex">
											<div class="icon">
												<i class="fa fa-trophy"></i>
											</div>
											<div>
												<h3 class="fw-600 text-white mb-0 mt-0">$967k</h3>
												<p class="text-white-50">Sales</p>
												<h5 class="text-white">-6.20967  <span class="ms-40"><i class="fa fa-angle-down me-10"></i><span class="text-white-50">2.036%</span></span> </h5>
											</div>										
										</div>
										<div>
											<div id="apexChart3" class="mx-50"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>					
				</div>
				<div class="col-xxxl-3 col-lg-6 col-12">
					<div class="box">
						<div class="box-body p-20">
							<div class="d-flex justify-content-between align-items-center">
								<div>
									<h2 class="my-0 fw-500">4.56k</h2>
									<p class="text-fade mb-0">Sales Today </p>						
								</div>
								<div class="icon">
									<i class="fa fa-truck bg-success me-0"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xxxl-3 col-lg-6 col-12">
					<div class="box">
						<div class="box-body p-20">
							<div class="d-flex justify-content-between align-items-center">
								<div>
									<h2 class="my-0 fw-500">27.4k</h2>
									<p class="text-fade mb-0">Visitors Today</p>						
								</div>
								<div class="icon">
									<i class="fa fa-bolt bg-warning me-0"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xxxl-3 col-lg-6 col-12">
					<div class="box">
						<div class="box-body p-20">
							<div class="d-flex justify-content-between align-items-center">
								<div>
									<h2 class="my-0 fw-500">$ 29.2k</h2>
									<p class="text-fade mb-0">Total Earnings</p>						
								</div>
								<div class="icon">
									<i class="fa fa-shopping-cart bg-danger me-0"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xxxl-3 col-lg-6 col-12">
					<div class="box">
						<div class="box-body p-20">
							<div class="d-flex justify-content-between align-items-center">
								<div>
									<h2 class="my-0 fw-500">45</h2>
									<p class="text-fade mb-0">Pending Orders</p>						
								</div>
								<div class="icon">
									<i class="fa fa-bullhorn bg-success me-0"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- /.content -->
@endsection

<!-- Override the control sidebar for this page -->
@section('control-sidebar')
@include('partials.control-sidebar-charts')
@endsection

@section('scripts')
<!-- ApexCharts Library -->
<script src="{{ asset('assets/vendor_components/apexcharts-bundle/dist/apexcharts.min.js') }}"></script>
<!-- Analysis Page Scripts -->
<script src="{{ asset('assets/vendor_components/apexcharts-bundle/irregular-data-series.js') }}"></script>
<script src="{{ asset('js/pages/analysis.js') }}"></script>
<!-- Widget Charts JS -->
<script src="{{ asset('js/pages/widgets.js') }}"></script>
@endsection
