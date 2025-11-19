@extends('layouts.admin')

@section('title', 'Details')

@section('content')
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="d-flex align-items-center">
				<div class="me-auto">
					<h4 class="page-title">Details</h4>
					<div class="d-inline-block align-items-center">
						<nav>
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
								<li class="breadcrumb-item" aria-current="page">e-Commerce</li>
								<li class="breadcrumb-item active" aria-current="page">Details</li>
							</ol>
						</nav>
					</div>
				</div>
				
			</div>
		</div>

		<!-- Main content -->
		<section class="content">

		  <div class="row">
			<div class="col-lg-12">
				<div class="box">
					<div class="box-body">
						<div class="row">
							<div class="col-md-4 col-sm-6">
								<div class="box box-body b-1 text-center no-shadow">
									<img src="{{ asset('images/product/product-6.png') }}" id="product-image" class="img-fluid" alt="" />
								</div>
								<div class="pro-photos">
									<div class="photos-item item-active">
										<img src="{{ asset('images/product/product-6.png') }}" alt="" >
									</div>
									<div class="photos-item">
										<img src="{{ asset('images/product/product-7.png') }}" alt="" >
									</div>
									<div class="photos-item">
										<img src="{{ asset('images/product/product-8.png') }}" alt="" >
									</div>
									<div class="photos-item">
										<img src="{{ asset('images/product/product-9.png') }}" alt="" >
									</div>
								</div>
								<div class="clear"></div>
							</div>
							<div class="col-md-8 col-sm-6">
								<h2 class="box-title mt-0">Product Title</h2>
								<div class="list-inline">
									<a class="text-warning"><i class="mdi mdi-star"></i></a>
									<a class="text-warning"><i class="mdi mdi-star"></i></a>
									<a class="text-warning"><i class="mdi mdi-star"></i></a>
									<a class="text-warning"><i class="mdi mdi-star"></i></a>
									<a class="text-warning"><i class="mdi mdi-star"></i></a>
								</div>
								<h1 class="pro-price mb-0 mt-20">&#36;270
										<span class="old-price">&#36;540</span>
										<span class="text-danger">50% off</span>
									</h1>
								<hr>
								<p>Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. but the majority have suffered alteration in some form, by injected humour</p>
								<div class="row">
									<div class="col-sm-12">
										<h6>Color</h6>
										<div class="input-group">
											<ul class="icolors">
												<li class="bg-danger rounded-circle"></li>
												<li class="bg-info rounded-circle"></li>
												<li class="bg-primary rounded-circle active"></li>
											</ul>
										</div>
										<h6 class="mt-20">Available Size</h6>
										<p class="mb-0">
											<span class="badge badge-pill badge-lg badge-secondary-light">S</span>
											<span class="badge badge-pill badge-lg badge-secondary-light">M</span>
											<span class="badge badge-pill badge-lg badge-secondary-light">L</span>
										</p>
									</div>
								</div>
								<hr>
								<div class="gap-items">
									<button class="btn btn-success"><i class="mdi mdi-shopping"></i> Buy Now!</button>
									<button class="btn btn-primary"><i class="mdi mdi-cart-plus"></i> Add To Cart</button>
									<button class="btn btn-info"><i class="mdi mdi-compare"></i> Compare</button>
									<button class="btn btn-danger"><i class="mdi mdi-heart"></i> Wishlist</button>
								</div>
								<h4 class="box-title mt-20">Key Highlights</h4>
								<ul class="list-icons list-unstyled">
									<li><i class="fa fa-check text-danger me-3"></i> Party Wear</li>
									<li><i class="fa fa-check text-danger me-3"></i> Nam libero tempore, cum soluta nobis est</li>
									<li><i class="fa fa-check text-danger me-3"></i> Omnis voluptas as placeat facere possimus omnis voluptas.</li>
								</ul>
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12">
								<h4 class="box-title mt-40">General Info</h4>
								<div class="table-responsive">
									<table class="table">
										<tbody>
											<tr>
												<td width="390">Brand</td>
												<td> Brand Name </td>
											</tr>
											<tr>
												<td>Delivery Condition</td>
												<td> Lorem Ipsum </td>
											</tr>
											<tr>
												<td>Type</td>
												<td> Party Wear </td>
											</tr>
											<tr>
												<td>Style</td>
												<td> Modern </td>
											</tr>
											<tr>
												<td>Product Number</td>
												<td> FG1548952 </td>
											</tr>

										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>				
				</div>
			</div>
		</div>

		</section>
		<!-- /.content -->
@endsection

@section('control-sidebar')
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
								<h5>Total Sale</h5>
								<h2 class="mb-0">$254.90</h2>
							</div>
							<div class="p-10">
								<div id="chart-spark1"></div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<div class="d-flex align-items-center justify-content-between">
							<h5 class="my-0">6 total orders</h5>
							<a href="#" class="mb-0">View Report</a>
						</div>
					</div>
				</div>
				<div class="box no-shadow box-bordered border-light">
					<div class="box-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<h5>Total Sessions</h5>
								<h2 class="mb-0">845</h2>
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
				<div class="box no-shadow box-bordered border-light">
					<div class="box-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<h5>Customer rate</h5>
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
				</div>
				<div class="box no-shadow box-bordered border-light">
					<div class="box-header">
						<h4 class="box-title">Resent Activity</h4>
					</div>
					<div class="box-body p-5">
						<div class="media-list media-list-hover">
						  <a class="media media-single mb-10 p-0 rounded-0" href="#">
							<h4 class="w-50 text-gray fw-500">10:10</h4>
							<div class="media-body ps-15 bs-5 rounded border-primary">
							  <p>Morbi quis ex eu arcu auctor sagittis.</p>
							  <span class="text-fade">by Johne</span>
							</div>
						  </a>

						  <a class="media media-single mb-10 p-0 rounded-0" href="#">
							<h4 class="w-50 text-gray fw-500">08:40</h4>
							<div class="media-body ps-15 bs-5 rounded border-success">
							  <p>Proin iaculis eros non odio ornare efficitur.</p>
							  <span class="text-fade">by Amla</span>
							</div>
						  </a>

						  <a class="media media-single mb-10 p-0 rounded-0" href="#">
							<h4 class="w-50 text-gray fw-500">07:10</h4>
							<div class="media-body ps-15 bs-5 rounded border-info">
							  <p>In mattis mi ut posuere consectetur.</p>
							  <span class="text-fade">by Josef</span>
							</div>
						  </a>

						  <a class="media media-single mb-10 p-0 rounded-0" href="#">
							<h4 class="w-50 text-gray fw-500">01:15</h4>
							<div class="media-body ps-15 bs-5 rounded border-danger">
							  <p>Morbi quis ex eu arcu auctor sagittis.</p>
							  <span class="text-fade">by Rima</span>
							</div>
						  </a>

						  <a class="media media-single mb-10 p-0 rounded-0" href="#">
							<h4 class="w-50 text-gray fw-500">23:12</h4>
							<div class="media-body ps-15 bs-5 rounded border-warning">
							  <p>Morbi quis ex eu arcu auctor sagittis.</p>
							  <span class="text-fade">by Alaxa</span>
							</div>
						  </a>

						</div>
					</div>
					<div class="box-footer">
						<div class="text-center">						  	  
							<a href="#" class="mb-0">Load More</a>					  	  		
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<!-- Vendor JS -->
<script src="{{ asset('js/vendors.min.js') }}"></script>
<script src="{{ asset('js/pages/chat-popup.js') }}"></script>
<script src="{{ asset('assets/vendor_components/apexcharts-bundle/dist/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/icons/feather-icons/feather.min.js') }}"></script>	

<!-- Riday Admin App -->
<script src="{{ asset('js/template.js') }}"></script>

<script src="{{ asset('js/pages/ecommerce_details.js') }}"></script>

<!-- Control Sidebar Charts -->
<script>
	$(document).ready(function(){
		// Only initialize charts if the elements exist
		if (document.getElementById('chart-spark1')) {
			var options1 = {
				series: [{
					data: [25, 66, 41, 89, 63, 25, 44, 12, 36, 9, 54]
				}],
				chart: {
					type: 'line',
					width: 100,
					height: 35,
					sparkline: {
						enabled: true
					}
				},
				stroke: {
					curve: 'straight'
				},
				colors: ['#00D0FF'],
				tooltip: {
					fixed: {
						enabled: false
					},
					x: {
						show: false
					},
					y: {
						title: {
							formatter: function (seriesName) {
								return ''
							}
						}
					},
					marker: {
						show: false
					}
				}
			};
			var chart1 = new ApexCharts(document.querySelector("#chart-spark1"), options1);
			chart1.render();
		}

		if (document.getElementById('chart-spark2')) {
			var options2 = {
				series: [{
					data: [12, 14, 2, 47, 42, 15, 47, 75, 65, 19, 14]
				}],
				chart: {
					type: 'line',
					width: 100,
					height: 35,
					sparkline: {
						enabled: true
					}
				},
				stroke: {
					curve: 'straight'
				},
				colors: ['#1B88F4'],
				tooltip: {
					fixed: {
						enabled: false
					},
					x: {
						show: false
					},
					y: {
						title: {
							formatter: function (seriesName) {
								return ''
							}
						}
					},
					marker: {
						show: false
					}
				}
			};
			var chart2 = new ApexCharts(document.querySelector("#chart-spark2"), options2);
			chart2.render();
		}

		if (document.getElementById('chart3')) {
			var options3 = {
				series: [44, 55],
				chart: {
					width: 100,
					type: 'donut',
				},
				colors: ['#1B88F4', '#FF4236'],
				legend: {
					show: false
				},
				dataLabels: {
					enabled: false,
				},
				responsive: [{
					breakpoint: 480,
					options: {
						chart: {
							width: 200
						},
						legend: {
							position: 'bottom'
						}
					}
				}]
			};
			var chart3 = new ApexCharts(document.querySelector("#chart3"), options3);
			chart3.render();
		}
	});
</script>
@endsection
