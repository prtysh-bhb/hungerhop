@extends('layouts.admin')

@section('title', 'Members')

@section('content')
		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-lg-9 col-md-8">
					<div class="box">
						<div class="media-list media-list-divided media-list-hover">
							@forelse($tenants as $tenant)
								<div class="media align-items-center">
									<span class="badge badge-dot badge-success"></span>
									<a class="avatar avatar-lg status-success" href="#">
										<img src="{{ asset('images/avatar/1.jpg') }}" alt="...">
									</a>
									<div class="media-body">
										<p>
											<a href="#"><strong>{{ $tenant->name }}</strong></a>
											<small class="sidetitle">{{ $tenant->email }}</small>
										</p>
										{{-- <p>{{ $user->role }}</p> --}}
									</div>
									<div class="media-right gap-items">
										<a class="btn btn-primary" href="{{ route('admin.show_details', $tenant->id) }}">View Details</a>
									</div>
								</div>
							@empty
								<div class="media align-items-center">
									<div class="media-body">
										<p>No tenant admin users found.</p>
									</div>
								</div>
							@endforelse

							<nav class="nav mt-2">
							  <a class="nav-link" href="#"><i class="fa fa-facebook"></i></a>
							  <a class="nav-link" href="#"><i class="fa fa-twitter"></i></a>
							  <a class="nav-link" href="#"><i class="fa fa-github"></i></a>
							  <a class="nav-link" href="#"><i class="fa fa-linkedin"></i></a>
							</nav>
						  </div>

						  <div class="media-right gap-items">
							<a class="media-action lead" href="#" data-bs-toggle="tooltip" title="Orders"><i class="ti-shopping-cart"></i></a>
							<a class="media-action lead" href="#" data-bs-toggle="tooltip" title="Receipts"><i class="ti-receipt"></i></a>
							<div class="btn-group">
							  <a class="media-action lead" href="#" data-bs-toggle="dropdown"><i class="ion-android-more-vertical"></i></a>
							  <div class="dropdown-menu">
								<a class="dropdown-item" href="#"><i class="fa fa-fw fa-user"></i> Profile</a>
								<a class="dropdown-item" href="#"><i class="fa fa-fw fa-comments"></i> Messages</a>
								<a class="dropdown-item" href="#"><i class="fa fa-fw fa-phone"></i> Call</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="#"><i class="fa fa-fw fa-remove"></i> Remove</a>
							  </div>
							</div>

						  </div>
						</div>

						<div class="media align-items-center">
						  <div class="media-right gap-items">
							<a class="media-action lead" href="#" data-bs-toggle="tooltip" title="Orders"><i class="ti-shopping-cart"></i></a>
							<a class="media-action lead" href="#" data-bs-toggle="tooltip" title="Receipts"><i class="ti-receipt"></i></a>
							<div class="btn-group">
							  <a class="media-action lead" href="#" data-bs-toggle="dropdown"><i class="ion-android-more-vertical"></i></a>
							  <div class="dropdown-menu">
								<a class="dropdown-item" href="#"><i class="fa fa-fw fa-user"></i> Profile</a>
								<a class="dropdown-item" href="#"><i class="fa fa-fw fa-comments"></i> Messages</a>
								<a class="dropdown-item" href="#"><i class="fa fa-fw fa-phone"></i> Call</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="#"><i class="fa fa-fw fa-remove"></i> Remove</a>
							  </div>
							</div>

						  </div>
						</div>
					  </div>
					</div>
				</div>
				<div class="col-lg-3 col-md-4">
					<div class="box no-border">
						<div class="box-header">
							<h4 class="box-title">Member Overview</h4>
						</div>
						<div class="box-body">
						  <a class="btn btn-outline btn-success mb-5 d-flex justify-content-between" href="javascript:void(0)">Online <span class="pull-right">103</span></a>
						  <a class="btn btn-outline btn-danger mb-5 d-flex justify-content-between" href="javascript:void(0)">Offline <span class="pull-right">19</span></a>
						  <a class="btn btn-outline btn-info mb-5 d-flex justify-content-between" href="javascript:void(0)">Available <span class="pull-right">623</span></a>
						  <a class="btn btn-outline btn-primary mb-5 d-flex justify-content-between" href="javascript:void(0)">Private <span class="pull-right">53</span></a>
						  <a class="btn btn-info mt-10 d-flex justify-content-between" href="javascript:void(0)">All Contacts <span class="pull-right">123</span></a>
						  <a href="javascript:void(0)" class="btn btn-success mt-10 d-block text-center">+ Add New Contact</a>
					  </div>
					</div>
				</div>	
				<div class="col-12">
					<h4 class="mt-30">Top Agent</h4>
					<hr>
				</div>
				
				<div class="col-14 d-flex gap-3">
				  <div class="col-12 col-lg-3">
					<div class="box ribbon-box">
					  <div class="ribbon-two ribbon-two-primary"><span>CEO</span></div>
					  <div class="box-header no-border p-0">				
						<a href="#">
						  <img class="img-fluid" src="{{ asset('images/avatar/375x200/1.jpg') }}" alt="">
						</a>
					  </div>
					  <div class="box-body">
							<div class="user-contact list-inline text-center">
								<a href="#" class="btn btn-circle mb-5 btn-facebook"><i class="fa fa-facebook"></i></a>
								<a href="#" class="btn btn-circle mb-5 btn-instagram"><i class="fa fa-instagram"></i></a>
								<a href="#" class="btn btn-circle mb-5 btn-twitter"><i class="fa fa-twitter"></i></a>
								<a href="#" class="btn btn-circle mb-5 btn-warning"><i class="fa fa-envelope"></i></a>				
							</div>
						  <div class="text-center">
							<h3 class="my-10"><a href="#">Tristan</a></h3>
							<h6 class="user-info mt-0 mb-10 text-fade">Designer</h6>
							<p class="text-fade w-p85 mx-auto">125 Ipsum Lorem Ave, Suite 458 New York, USA 154875 </p>
						  </div>
					  </div>
					</div>
				  </div>
				  <div class="col-12 col-lg-3">
					<div class="box ribbon-box">
					  <div class="ribbon-two ribbon-two-danger"><span>MD</span></div>
					  <div class="box-header no-border p-0">				
						<a href="#">
						  <img class="img-fluid" src="{{ asset('images/avatar/375x200/2.jpg') }}" alt="">
						</a>
					  </div>
					  <div class="box-body">
						  <div class="text-center">
							<div class="user-contact list-inline">
								<a href="#" class="btn btn-circle mb-5 btn-facebook"><i class="fa fa-facebook"></i></a>
								<a href="#" class="btn btn-circle mb-5 btn-instagram"><i class="fa fa-instagram"></i></a>
								<a href="#" class="btn btn-circle mb-5 btn-twitter"><i class="fa fa-twitter"></i></a>
								<a href="#" class="btn btn-circle mb-5 btn-warning"><i class="fa fa-envelope"></i></a>				
							</div>
							<h3 class="my-10"><a href="#">Gabriel</a></h3>
							<h6 class="user-info mt-0 mb-10 text-fade">Developer</h6>
							<p class="text-fade w-p85 mx-auto">89 Dolor Lorem Ave, Suite 258 New York, USA 175486 </p>
						  </div>
					  </div>
					</div>
				  </div>
				  <div class="col-12 col-lg-3">
					<div class="box ribbon-box">
					  <div class="ribbon-two ribbon-two-success"><span>HR</span></div>
					  <div class="box-header no-border p-0">				
						<a href="#">
						  <img class="img-fluid" src="{{ asset('images/avatar/375x200/3.jpg') }}" alt="">
						</a>
					  </div>
					  <div class="box-body">
						  <div class="text-center">
							<div class="user-contact list-inline">
								<a href="#" class="btn btn-circle mb-5 btn-facebook"><i class="fa fa-facebook"></i></a>
								<a href="#" class="btn btn-circle mb-5 btn-instagram"><i class="fa fa-instagram"></i></a>
								<a href="#" class="btn btn-circle mb-5 btn-twitter"><i class="fa fa-twitter"></i></a>
								<a href="#" class="btn btn-circle mb-5 btn-warning"><i class="fa fa-envelope"></i></a>				
							</div>
							<h3 class="my-10"><a href="#">Owen</a></h3>
							<h6 class="user-info mt-0 mb-10 text-fade">Manager</h6>
							<p class="text-fade w-p85 mx-auto">895 Lorem Ave, Suite 963 New York, USA 478596 </p>
						  </div>
					  </div>
					</div>
				  </div>
				  <div class="col-12 col-lg-3">
					<div class="box ribbon-box">
					  <div class="ribbon-two ribbon-two-info"><span>SM</span></div>
					  <div class="box-header no-border p-0">				
						<a href="#">
						  <img class="img-fluid" src="{{ asset('images/avatar/375x200/4.jpg') }}" alt="">
						</a>
					  </div>
					  <div class="box-body">
						  <div class="text-center">
							<div class="user-contact list-inline">
								<a href="#" class="btn btn-circle mb-5 btn-facebook"><i class="fa fa-facebook"></i></a>
								<a href="#" class="btn btn-circle mb-5 btn-instagram"><i class="fa fa-instagram"></i></a>
								<a href="#" class="btn btn-circle mb-5 btn-twitter"><i class="fa fa-twitter"></i></a>
								<a href="#" class="btn btn-circle mb-5 btn-warning"><i class="fa fa-envelope"></i></a>				
							</div>
							<h3 class="my-10"><a href="#">Carter</a></h3>
							<h6 class="user-info mt-0 mb-10 text-fade">Sales Manager</h6>
							<p class="text-fade w-p85 mx-auto">45 Sit Lorem Ave, Suite 157 New York, USA 254781 </p>
						  </div>
					  </div>
					</div>
				  </div>
			</div>
		</section>
		<!-- /.content -->
@endsection

@section('scripts')
<script src="{{ asset('assets/vendor_components/apexcharts-bundle/dist/apexcharts.min.js') }}"></script>
@endsection
