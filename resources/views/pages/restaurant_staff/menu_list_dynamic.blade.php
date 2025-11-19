{{-- Extend the main layout --}}
@extends('layouts.admin')

{{-- Define the title for this page --}}
@section('title', 'Menu List')

{{-- Define the main content for this page --}}
@section('content')
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="d-flex align-items-center">
				<div class="me-auto">
					<h4 class="page-title">Menu List</h4>
					<div class="d-inline-block align-items-center">
						<nav>
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
								<li class="breadcrumb-item" aria-current="page">Menu</li>
								<li class="breadcrumb-item active" aria-current="page">Menu List</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
		</div>

		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-12 mb-3">
					<div class="d-flex justify-content-between align-items-center">
						<h5>Total Menu Items: {{ $menuItems->count() }}</h5>
						<a href="{{ route('restaurant.menu.add') }}" class="btn btn-primary">
							<i class="fa fa-plus"></i> Add New Menu Item
						</a>
					</div>
				</div>

				@if(session('success'))
					<div class="col-12">
						<div class="alert alert-success alert-dismissible">
							<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
							{{ session('success') }}
						</div>
					</div>
				@endif

				@if(session('error'))
					<div class="col-12">
						<div class="alert alert-danger alert-dismissible">
							<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
							{{ session('error') }}
						</div>
					</div>
				@endif

				@if($menuItems->count() > 0)
					@foreach($menuItems as $menuItem)
					<div class="col-xxxl-3 col-xl-4 col-lg-6 col-12">
						<div class="box food-box">
							<div class="box-body text-center">
								<div class="menu-item">
									@if($menuItem->image_url)
										<img src="{{ $menuItem->image_url }}" class="img-fluid w-p75" alt="{{ $menuItem->item_name }}" />
									@else
										<img src="{{ asset('images/food/dish-1.png') }}" class="img-fluid w-p75" alt="{{ $menuItem->item_name }}" />
									@endif
								</div>
								<div class="menu-details text-center">
									<h4 class="mt-20 mb-10">{{ $menuItem->item_name }}</h4>
									<p>{{ $menuItem->category->name ?? 'Uncategorized' }}</p>
									<p class="text-success fw-bold">${{ number_format($menuItem->base_price, 2) }}</p>
									<p class="small">
										@if($menuItem->is_available)
											<span class="badge badge-success">Available</span>
										@else
											<span class="badge badge-danger">Unavailable</span>
										@endif
										@if($menuItem->is_vegetarian)
											<span class="badge badge-info">Vegetarian</span>
										@endif
										@if($menuItem->is_vegan)
											<span class="badge badge-primary">Vegan</span>
										@endif
										@if($menuItem->is_gluten_free)
											<span class="badge badge-warning">Gluten Free</span>
										@endif
									</p>
									@if($menuItem->description)
										<p class="text-muted small">{{ Str::limit($menuItem->description, 60) }}</p>
									@endif
								</div>
								<div class="act-btn d-flex justify-content-between">
									<div class="text-center mx-5">
										<a href="{{ route('restaurant.menu.show', $menuItem->id) }}" class="waves-effect waves-circle btn btn-circle btn-success-light btn-xs mb-5">
											<i class="fa fa-eye"></i>
										</a>
										<small class="d-block">View</small>
									</div>
									<div class="text-center mx-5">
										<a href="{{ route('restaurant.menu.edit', $menuItem->id) }}" class="waves-effect waves-circle btn btn-circle btn-danger-light btn-xs mb-5">
											<i class="fa fa-edit"></i>
										</a>
										<small class="d-block">Edit</small>
									</div>
									<div class="text-center mx-5">
										<form action="{{ route('restaurant.menu.destroy', $menuItem->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this menu item?')" style="display: inline;">
											@csrf
											@method('DELETE')
											<button type="submit" class="waves-effect waves-circle btn btn-circle btn-primary-light btn-xs mb-5" style="border: none; background: none;">
												<i class="fa fa-trash"></i>
											</button>
										</form>
										<small class="d-block">Delete</small>
									</div>
									<div class="text-center mx-5">
										<a href="{{ route('restaurant.menu.duplicate', $menuItem->id) }}" class="waves-effect waves-circle btn btn-circle btn-info-light btn-xs mb-5">
											<i class="fa fa-plus-square-o"></i>
										</a>
										<small class="d-block">Duplicate</small>
									</div>
								</div>
							</div>
						</div>
					</div>
					@endforeach
				@else
					<div class="col-12">
						<div class="box">
							<div class="box-body text-center">
								<h4>No Menu Items Found</h4>
								<p>Start by adding your first menu item.</p>
								<a href="{{ route('restaurant.menu.add') }}" class="btn btn-primary">
									<i class="fa fa-plus"></i> Add First Menu Item
								</a>
							</div>
						</div>
					</div>
				@endif
		    </div>
		</section>
		<!-- /.content -->
@endsection
