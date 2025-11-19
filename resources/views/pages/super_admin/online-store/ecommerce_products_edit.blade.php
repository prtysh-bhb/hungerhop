@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
		<!-- Content Header (Page header) -->	  
		<div class="content-header">
			<div class="d-flex align-items-center">
				<div class="me-auto">
					<h4 class="page-title">Edit Product</h4>
					<div class="d-inline-block align-items-center">
						<nav>
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
								<li class="breadcrumb-item" aria-current="page">e-Commerce</li>
								<li class="breadcrumb-item active" aria-current="page">Edit Product</li>
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
					<div class="box-header with-border">
					  <h4 class="box-title">About Product</h4>
					</div>
				  <div class="box-body">
					<form action="#">
						<div class="form-body">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
									  <label class="fw-700 fs-16 form-label">Product Name</label>
									  <input type="text" class="form-control" placeholder="Product Name">
									</div>
								</div>
								<!--/span-->
								<div class="col-md-6">
									<div class="form-group">
									   <label class="fw-700 fs-16 form-label">Sub text</label>
									   <input type="text" class="form-control" placeholder="Lorem Ipsum Text...">
									</div>
								</div>
								<!--/span-->
							</div>
							<!--/row-->
							<!--/row-->
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="fw-700 fs-16 form-label">Category</label>
										<select class="form-select" data-placeholder="Choose a Category" tabindex="1">
											<option value="Category 1">Category 1</option>
											<option value="Category 2">Category 2</option>
											<option value="Category 3">Category 5</option>
											<option value="Category 4">Category 4</option>
										</select>
									</div>
								</div>
								<!--/span-->
								<div class="col-md-6">
									<div class="form-group">
										<label class="fw-700 fs-16 form-label">Status</label>
										<div class="radio-list">
											<label class="radio-inline p-0 me-10">
												<div class="radio radio-info">
													<input type="radio" name="radio" id="radio1" value="option1">
													<label for="radio1">Published</label>
												</div>
											</label>
											<label class="radio-inline">
												<div class="radio radio-info">
													<input type="radio" name="radio" id="radio2" value="option2">
													<label for="radio2">Draft</label>
												</div>
											</label>
										</div>
									</div>
								</div>
								<!--/span-->
							</div>
							<!--/row-->
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="fw-700 fs-16 form-label">Price</label>
										<div class="input-group">
											<div class="input-group-addon"><i class="ti-money"></i></div>
											<input type="text" class="form-control" placeholder="270"> 
										</div>
									</div>
								</div>
								<!--/span-->
								<div class="col-md-6">
									<div class="form-group">
										<label class="fw-700 fs-16 form-label">Discount</label>
										<div class="input-group">
											<div class="input-group-addon"><i class="ti-cut"></i></div>
											<input type="text" class="form-control" placeholder="50%"> 
										</div>
									</div>
								</div>
								<!--/span-->
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="fw-700 fs-16 form-label">Product Description</label>
										<textarea class="form-control p-15" rows="4">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable.</textarea>
									</div>
								</div>
							</div>
							<!--/row-->
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="fw-700 fs-16 form-label">Meta Title</label>
										<input type="text" class="form-control"> </div>
								</div>
								<!--/span-->
								<div class="col-md-6">
									<div class="form-group">
										<label class="fw-700 fs-16 form-label">Meta Keyword</label>
										<input type="text" class="form-control"> </div>
								</div>
								<!--/span-->
								<div class="col-md-4">
									<h4 class="box-title mt-20">Upload Image</h4>
									<div class="product-img text-start">
										<img src="{{ asset('images/product/product-9.png') }}" alt="">
										<div class="input-group my-3">
										  <label class="input-group-text btn-primary" for="inputGroupFile01">Upload Another Image</label>
										  <input type="file" class="form-control" id="inputGroupFile01">
										</div>
										<button class="btn btn-success">Edit</button>
										<button class="btn btn-danger">Delete</button>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<h4 class="box-title mt-40">General Info</h4>
									<div class="table-responsive">
										<table class="table no-border td-padding">
											<tbody>
												<tr>
													<td>
														<input type="text" class="form-control" placeholder="Brand">
													</td>
													<td>
														<input type="text" class="form-control" placeholder="Seller">
													</td>
												</tr>
												<tr>
													<td>
														<input type="text" class="form-control" placeholder="Delivery Condition">
													</td>
													<td>
														<input type="text" class="form-control" placeholder="Knock Down">
													</td>
												</tr>
												<tr>
													<td>
														<input type="text" class="form-control" placeholder="Color">
													</td>
													<td>
														<input type="text" class="form-control" placeholder="Gift Pack">
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
						<div class="form-actions mt-10">
							<button type="submit" class="btn btn-primary"> <i class="fa fa-check"></i> Save</button>
							<button type="button" class="btn btn-danger">Cancel</button>
						</div>
					</form>
				  </div>
				</div>
			  </div>		  
		  </div>

		</section>
		<!-- /.content -->
@endsection

@section('scripts')
<!-- Form validation -->
<script src="{{ asset('assets/vendor_components/form-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/pages/validation.js') }}"></script>
@endsection
