@extends('layouts.admin')

@section('title', 'Checkout')

@section('content')
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="d-flex align-items-center">
				<div class="me-auto">
					<h4 class="page-title">Checkout</h4>
					<div class="d-inline-block align-items-center">
						<nav>
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
								<li class="breadcrumb-item" aria-current="page">e-Commerce</li>
								<li class="breadcrumb-item active" aria-current="page">Checkout</li>
							</ol>
						</nav>
					</div>
				</div>
				
			</div>
		</div>

		<!-- Main content -->
		<section class="content">

		  <div class="row">
			<div class="col-12">
				<div class="box">
				  <div class="box-header">
					<h4 class="box-title">Product Summary</h4>
				  </div>
				  <div class="box-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Photo</th>
									<th>Product</th>
									<th>Quantity</th>
									<th class="w-200">Price</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><img src="{{ asset('images/product/product-1.png') }}" alt="" width="40"></td>
									<td>Product Name Here</td>
									<td>3</td>
									<td>$270</td>
								</tr>
								<tr>
									<td><img src="{{ asset('images/product/product-2.png') }}" alt="" width="40"></td>
									<td>Product Name Here</td>
									<td>3</td>
									<td>$270</td>
								</tr>
								<tr>
									<th colspan="3" class="text-end">Total</th>
									<th>$1620</th>
								</tr>
								<tr>
									<td colspan="3" align="right">Coupan Discount</td>
									<td>$1620</td>
								</tr>
								<tr>
									<td colspan="3"  align="right">Delivery Charges</td>
									<td>$50</td>
								</tr>
								<tr>
									<td colspan="3" align="right">Tax</td>
									<td>$18</td>
								</tr>
								<tr>
									<th colspan="3" class="text-end fs-24 fw-700">Payable Amount</th>
									<th class="fs-24 fw-700">$1678</th>
								</tr>
							</tbody>
						</table>
					</div>

					<hr>
					<h4 class="box-title mb-15">Save Card</h4>
						<div class="row">
							<!-- col -->
							<div class="col-lg-3 col-md-6">
								<div class="box bg-primary box-inverse">
									<div class="box-body">
										<h1 class="mt-0"><i class="fa fa-cc-visa"></i></h1>
										<h3>**** **** **** 7009</h3>
										<span class="pull-right">Exp date: 12/21</span>
										<span class="font-500">Daniel Doe</span>
									</div>
								</div>
							</div>
							<!-- /col -->
							<!-- col -->
							<div class="col-lg-3 col-md-6">
								<div class="box bg-info box-inverse">
									<div class="box-body">
										<h1 class="mt-0"><i class="fa fa-cc-mastercard"></i></h1>
										<h3>**** **** **** 4125</h3>
										<span class="pull-right">Exp date: 12/21</span>
										<span class="font-500">Ethan Doe</span>
									</div>
								</div>
							</div>
							<!-- /col -->
							<!-- col -->
							<div class="col-lg-3 col-md-6">
								<div class="box bg-warning box-inverse">
									<div class="box-body">
										<h1 class="mt-0"><i class="fa fa-cc-discover"></i></h1>
										<h3>**** **** **** 5124</h3>
										<span class="pull-right">Exp date: 12/21</span>
										<span class="font-500">Jayden Doe</span>
									</div>
								</div>
							</div>
							<!-- /col -->
							<!-- col -->
							<div class="col-lg-3 col-md-6">
								<div class="box bg-danger box-inverse">
									<div class="box-body">
										<h1 class="mt-0"><i class="fa fa-cc-amex"></i></h1>
										<h3>**** **** **** 9578</h3>
										<span class="pull-right">Exp date: 12/21</span>
										<span class="font-500">William Doe</span>
									</div>
								</div>
							</div>
							<!-- /col -->
						</div>
					<hr>
					<h4 class="box-title mb-15">Choose payment Option</h4>
					<!-- Nav tabs -->
					<ul class="nav nav-tabs" role="tablist">
						<li class="nav-item"> <a class="nav-link active" data-bs-toggle="tab" href="#debit-card" role="tab"><span class="hidden-sm-up"><i class="fa fa-cc"></i></span> <span class="hidden-xs-down">Debit Card</span></a> </li>
						<li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#paypal" role="tab"><span class="hidden-sm-up"><i class="fa fa-paypal"></i></span> <span class="hidden-xs-down">Paypal</span></a> </li>
					</ul>

					<!-- Tab panes -->
					<div class="tab-content tabcontent-border">
						<div class="tab-pane active" id="debit-card" role="tabpanel">
							<div class="p-30">
								<div class="row">
									<div class="col-lg-7 col-md-6 col-12">
										<form>
											<div class="form-group">
												<label for="exampleInputEmail1" class="form-label">CARD NUMBER</label>
												<div class="input-group">
													<div class="input-group-addon"><i class="fa fa-credit-card"></i></div>
													<input type="text" class="form-control" id="exampleInputuname" placeholder="Card Number"> 
												</div>
											</div>
											<div class="row">
												<div class="col-7">
													<div class="form-group">
														<label class="form-label">EXPIRATION DATE</label>
														<input type="text" class="form-control" name="Expiry" placeholder="MM / YY" required=""> 
													</div>
												</div>
												<div class="col-5 pull-right">
													<div class="form-group">
														<label class="form-label">CV CODE</label>
														<input type="text" class="form-control" name="CVC" placeholder="CVC" required=""> 
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-12">
													<div class="form-group">
														<label class="form-label">NAME OF CARD</label>
														<input type="text" class="form-control" name="nameCard" placeholder="NAME AND SURNAME"> 
													</div>
												</div>
											</div>
											<button class="btn btn-success">Make Payment</button>
										</form>
									</div>
									<div class="col-lg-5 col-md-6 col-12">
										<h3 class="box-title mt-10">General Info</h3>
										<h2><i class="fa fa-cc-visa text-info"></i>
											<i class="fa fa-cc-mastercard text-danger"></i>
											<i class="fa fa-cc-discover text-success"></i>
											<i class="fa fa-cc-amex text-warning"></i>
										</h2>
										<p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock.</p>
										<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.  </p>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="paypal" role="tabpanel">						
							<div class="p-30">
								You can pay your money through paypal, for more info <a href="">click here</a><br><br>
								<button class="btn btn-info btn-outline"><i class="fa fa-cc-paypal"></i> Pay with Paypal</button>
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

@section('scripts')
<!-- Vendor JS -->
<script src="{{ asset('assets/vendor_components/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
@endsection
