@extends('layouts.admin')

@section('title', 'Cart')

@section('content')
		<!-- Content Header (Page header) -->	  
		<div class="content-header">
			<div class="d-flex align-items-center">
				<div class="me-auto">
					<h4 class="page-title">Cart</h4>
					<div class="d-inline-block align-items-center">
						<nav>
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
								<li class="breadcrumb-item" aria-current="page">e-Commerce</li>
								<li class="breadcrumb-item active" aria-current="page">Cart</li>
							</ol>
						</nav>
					</div>
				</div>
				
			</div>
		</div>

		<!-- Main content -->
		<section class="content">

		  <div class="row">
			<div class="col-12 col-lg-8">
				<div class="box">
					<div class="box-header bg-primary">
						<h4 class="box-title">YOUR CART (6 ITEMS)</h4>
					</div>

					<div class="box-body">
						<div class="table-responsive">
							<table class="table product-overview">
								<thead>
									<tr>
										<th>Image</th>
										<th>Product info</th>
										<th>Price</th>
										<th>Quantity</th>
										<th style="text-align:center">Total</th>
										<th style="text-align:center">Action</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><img src="{{ asset('images/product/product-1.png') }}" alt="" width="80"></td>
										<td>
											<h5>Product Name Here</h5>
											<p>Lorem Ipsum available the majority have suffered alteration in some.</p>
										</td>
										<td>$270</td>
										<td width="70">
											<input type="number" class="form-control" placeholder="1" min="0" max="5">
										</td>
										<td width="100" align="center" class="fw-900">$270</td>
										<td align="center"><a href="javascript:void(0)" class="btn btn-circle btn-danger btn-xs" title="" data-bs-toggle="tooltip" data-bs-original-title="Delete"><i class="ti-trash"></i></a></td>
									</tr>
									<tr>
										<td><img src="{{ asset('images/product/product-2.png') }}" alt="" width="80"></td>
										<td>
											<h5>Product Name Here</h5>
											<p>Lorem Ipsum available the majority have suffered alteration in some.</p>
										</td>
										<td>$270</td>
										<td>
											<input type="number" class="form-control" placeholder="1" min="0" max="5">
										</td>
										<td align="center" class="fw-900">$270</td>
										<td align="center"><a href="javascript:void(0)" class="btn btn-circle btn-danger btn-xs" title="" data-bs-toggle="tooltip" data-bs-original-title="Delete"><i class="ti-trash"></i></a></td>
									</tr>
									<tr>
										<td><img src="{{ asset('images/product/product-3.png') }}" alt="" width="80"></td>
										<td>
											<h5>Product Name Here</h5>
											<p>Lorem Ipsum available the majority have suffered alteration in some.</p>
										</td>
										<td>$270</td>
										<td>
											<input type="number" class="form-control" placeholder="1" min="0" max="5">
										</td>
										<td align="center" class="fw-900">$270</td>
										<td align="center"><a href="javascript:void(0)" class="btn btn-circle btn-danger btn-xs" title="" data-bs-toggle="tooltip" data-bs-original-title="Delete"><i class="ti-trash"></i></a></td>
									</tr>
									<tr>
										<td><img src="{{ asset('images/product/product-4.png') }}" alt="" width="80"></td>
										<td>
											<h5>Product Name Here</h5>
											<p>Lorem Ipsum available the majority have suffered alteration in some.</p>
										</td>
										<td>$270</td>
										<td>
											<input type="number" class="form-control" placeholder="1" min="0" max="5">
										</td>
										<td align="center" class="fw-900">$270</td>
										<td align="center"><a href="javascript:void(0)" class="btn btn-circle btn-danger btn-xs" title="" data-bs-toggle="tooltip" data-bs-original-title="Delete"><i class="ti-trash"></i></a></td>
									</tr>
									<tr>
										<td><img src="{{ asset('images/product/product-5.png') }}" alt="" width="80"></td>
										<td>
											<h5>Product Name Here</h5>
											<p>Lorem Ipsum available the majority have suffered alteration in some.</p>
										</td>
										<td>$270</td>
										<td>
											<input type="number" class="form-control" placeholder="1" min="0" max="5">
										</td>
										<td align="center" class="fw-900">$270</td>
										<td align="center"><a href="javascript:void(0)" class="btn btn-circle btn-danger btn-xs" title="" data-bs-toggle="tooltip" data-bs-original-title="Delete"><i class="ti-trash"></i></a></td>
									</tr>
									<tr>
										<td><img src="{{ asset('images/product/product-6.png') }}" alt="" width="80"></td>
										<td>
											<h5>Product Name Here</h5>
											<p>Lorem Ipsum available the majority have suffered alteration in some.</p>
										</td>
										<td>$270</td>
										<td>
											<input type="number" class="form-control" placeholder="1" min="0" max="5">
										</td>
										<td align="center" class="fw-900">$270</td>
										<td align="center"><a href="javascript:void(0)" class="btn btn-circle btn-danger btn-xs" title="" data-bs-toggle="tooltip" data-bs-original-title="Delete"><i class="ti-trash"></i></a></td>
									</tr>							
								</tbody>
							</table>
							<button class="btn btn-success pull-right"><i class="fa fa fa-shopping-cart"></i> Checkout</button>
							<button class="btn btn-info"><i class="fa fa-arrow-left"></i> Continue shopping</button>
						</div>

					</div>
				</div>
			</div>

			<div class="col-12 col-lg-4">
				<div class="box">
					<div class="box-header bg-success">
						<h4 class="box-title">Discount</h4>
					</div>
					<div class="box-body">
						<p>If you have any discount vouchers/coupans, apply here. If you don't have any, click <a href="javascript:void(0);" class="text-link">here</a> to get one.</p>
						<form class="form-inline mt-20">
							<div class="input-group w-p100">
								<input type="text" class="form-control">
								<button type="button" class="btn btn-danger">Apply</button>
								<!-- /btn-group -->
							</div>
						</form>

					</div>
				</div>			 

				<div class="box">
					<div class="box-header bg-info">
						<h4 class="box-title">Cart Summary</h4>
					</div>

					<div class="box-body">
						<div class="table-responsive">
							<table class="table simple mb-0">
								<tbody>
									<tr>
										<td>Total</td>
										<td class="text-end fw-700">$3240</td>
									</tr>
									<tr>
										<td>Coupan Discount</td>
										<td class="text-end fw-700"><span class="text-danger me-15">50%</span>-$1620</td>
									</tr>
									<tr>
										<td>Delivery Charges</td>
										<td class="text-end fw-700">$50</td>
									</tr>
									<tr>
										<td>Tax</td>
										<td class="text-end fw-700">$18</td>
									</tr>
									<tr>
										<th class="bt-1">Payable Amount</th>
										<th class="bt-1 text-end fw-900 fs-18">$1688</th>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="box-footer">	
						<button class="btn btn-danger">Cancel Order</button>
						<button class="btn btn-primary pull-right">Place Order</button>
					</div>
				</div> 

				<div class="box">
					<div class="box-header bg-dark">
						<h4 class="box-title">Support</h4>
					</div>

					<div class="box-body">
						<h4 class="fw-500"><i class="ti-mobile"></i> +1800 123 1234 <span class="text-info">(Toll Free)</span></h4>
						<p>Contact us for any queries. We are avalible 24x7x365.</p>
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
@endsection
