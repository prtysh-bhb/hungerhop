@extends('layouts.admin')

@section('title', 'Product Details')

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
				<div class="text-end">
					<button type="button" class="btn btn-info" data-toggle="control-sidebar"><i class="fa fa-chart-line"></i> Analytics</button>
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
									<div class="list-inline-item"><i class="fa fa-star text-yellow"></i></div>
									<div class="list-inline-item"><i class="fa fa-star text-yellow"></i></div>
									<div class="list-inline-item"><i class="fa fa-star text-yellow"></i></div>
									<div class="list-inline-item"><i class="fa fa-star text-yellow"></i></div>
									<div class="list-inline-item"><i class="fa fa-star-o text-gray"></i></div>
									<div class="list-inline-item"><small class="text-fade">Review (250)</small></div>
								</div>
								<h1 class="pro-price my-20">$89 <small class="text-fade line-through fs-16">$95</small></h1>
								<div class="gap-items-4 mt-10">
									<a class="btn btn-success btn-sm me-5" href="#"><i class="mdi mdi-shopping"></i> Buy Now</a>
									<a class="btn btn-danger btn-sm me-5" href="#"><i class="mdi mdi-heart"></i> Add To Wishlist</a>
									<a class="btn btn-primary btn-sm" href="#"><i class="mdi mdi-compare"></i> Compare</a>
								</div>
								<h4 class="box-title mt-20">Key Highlights</h4>
								<ul class="list-unstyled">
									<li><i class="fa fa-check text-success"></i> Lorem ipsum dolor sit amet.</li>
									<li><i class="fa fa-check text-success"></i> Consectetur adipiscing elit,sed do eiusmod tempor.</li>
									<li><i class="fa fa-check text-success"></i> Incididunt ut labore et dolore magna aliqua.</li>
									<li><i class="fa fa-check text-success"></i> Ut enim ad minim veniam, quis nostrud exercitation.</li>
								</ul>
								<h4 class="box-title mt-20">Size</h4>
								<div class="btn-group" data-bs-toggle="buttons">
									<input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
									<label class="btn btn-outline-primary" for="btnradio1">XS</label>

									<input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
									<label class="btn btn-outline-primary" for="btnradio2">S</label>

									<input type="radio" class="btn-check" name="btnradio" id="btnradio3" autocomplete="off">
									<label class="btn btn-outline-primary" for="btnradio3">M</label>

									<input type="radio" class="btn-check" name="btnradio" id="btnradio4" autocomplete="off">
									<label class="btn btn-outline-primary" for="btnradio4">L</label>

									<input type="radio" class="btn-check" name="btnradio" id="btnradio5" autocomplete="off">
									<label class="btn btn-outline-primary" for="btnradio5">XL</label>
								</div>

								<h4 class="box-title mt-20">Color</h4>
								<ul class="list-inline icolors">
									<li class="list-inline-item">
										<div class="color-box bg-dark"></div>
									</li>
									<li class="list-inline-item">
										<div class="color-box bg-danger"></div>
									</li>
									<li class="list-inline-item">
										<div class="color-box bg-success"></div>
									</li>
									<li class="list-inline-item">
										<div class="color-box bg-primary"></div>
									</li>
									<li class="list-inline-item">
										<div class="color-box bg-warning"></div>
									</li>
								</ul>

								<h4 class="box-title mt-20">Quantity</h4>
								<div class="input-group bootstrap-touchspin bootstrap-touchspin-injected" style="width: 134px;">
									<span class="input-group-btn input-group-prepend">
										<button class="btn btn-white bootstrap-touchspin-down" type="button">-</button>
									</span>
									<input id="demo1" type="text" value="55" name="demo1" class="form-control" style="display: block;">
									<span class="input-group-btn input-group-append">
										<button class="btn btn-white bootstrap-touchspin-up" type="button">+</button>
									</span>
								</div>

								<div class="gap-items-4 mt-20">
									<a class="btn btn-primary" href="#"><i class="mdi mdi-cart-plus"></i> Add To Cart</a>
									<a class="btn btn-danger" href="#"><i class="mdi mdi-heart"></i> Wishlist</a>
									<a class="btn btn-success" href="#"><i class="mdi mdi-compare"></i> Compare</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		  </div>

		  <div class="row">
			<div class="col-lg-12">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
						<li class="nav-item"><a href="#tab_1" class="nav-link active" data-bs-toggle="tab">Description</a></li>
						<li class="nav-item"><a href="#tab_2" class="nav-link" data-bs-toggle="tab">Reviews (2)</a></li>
						<li class="nav-item"><a href="#tab_3" class="nav-link" data-bs-toggle="tab">Tags</a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="tab_1">
							Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.
							<br><br>
							Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.
						</div>
						<!-- /.tab-pane -->
						<div class="tab-pane" id="tab_2">
							Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.
						</div>
						<!-- /.tab-pane -->
						<div class="tab-pane" id="tab_3">
							Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.
						</div>
						<!-- /.tab-pane -->
					</div>
					<!-- /.tab-content -->
				</div>
				<!-- nav-tabs-custom -->
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
<!-- Vendor JS -->
<script src="{{ asset('assets/vendor_components/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<!-- Widget Charts JS -->
<script src="{{ asset('js/pages/widgets.js') }}"></script>
<!-- Ecommerce Details JS -->
<script src="{{ asset('js/pages/ecommerce_details.js') }}"></script>
@endsection
