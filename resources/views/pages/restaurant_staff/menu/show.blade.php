{{-- Extend the main layout --}}
@extends('layouts.admin')

{{-- Define the title for this page --}}
@section('title', 'Menu Item Details')

{{-- Define the main content for this page --}}
@section('content')
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="d-flex align-items-center">
				<div class="me-auto">
					<h4 class="page-title">Menu Item Details</h4>
					<div class="d-inline-block align-items-center">
						<nav>
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
								<li class="breadcrumb-item"><a href="{{ route('restaurant.menu.list') }}">Menu</a></li>
								<li class="breadcrumb-item active" aria-current="page">{{ $menuItem->item_name }}</li>
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
					<div class="d-flex justify-content-between align-items-center mb-3">
						<h5>{{ $menuItem->item_name }}</h5>
						<div>
							<a href="{{ route('restaurant.menu.edit', $menuItem->id) }}" class="btn btn-warning">
								<i class="fa fa-edit"></i> Edit
							</a>
							<a href="{{ route('restaurant.menu.duplicate', $menuItem->id) }}" class="btn btn-info">
								<i class="fa fa-plus-square-o"></i> Duplicate
							</a>
							<a href="{{ route('restaurant.menu.list') }}" class="btn btn-secondary">
								<i class="fa fa-arrow-left"></i> Back to List
							</a>
						</div>
					</div>
				</div>

				<div class="col-lg-8">
					<div class="box">
						<div class="box-header with-border">
							<h4 class="box-title">Item Information</h4>
						</div>
						<div class="box-body">
							<div class="row">
								<div class="col-md-6">
									<table class="table table-borderless">
										<tr>
											<td class="fw-bold">Item Name:</td>
											<td>{{ $menuItem->item_name }}</td>
										</tr>
										<tr>
											<td class="fw-bold">Category:</td>
											<td>{{ $menuItem->category->name ?? 'Uncategorized' }}</td>
										</tr>
										<tr>
											<td class="fw-bold">Base Price:</td>
											<td class="text-success fw-bold">${{ number_format($menuItem->base_price, 2) }}</td>
										</tr>
										<tr>
											<td class="fw-bold">Display Price:</td>
											<td>${{ number_format($menuItem->display_price ?? $menuItem->base_price, 2) }}</td>
										</tr>
										<tr>
											<td class="fw-bold">SKU:</td>
											<td>{{ $menuItem->sku }}</td>
										</tr>
										<tr>
											<td class="fw-bold">Status:</td>
											<td>
												@if($menuItem->is_available)
													<span class="badge badge-success">Available</span>
												@else
													<span class="badge badge-danger">Unavailable</span>
												@endif
											</td>
										</tr>
									</table>
								</div>
								<div class="col-md-6">
									<table class="table table-borderless">
										<tr>
											<td class="fw-bold">Preparation Time:</td>
											<td>{{ $menuItem->preparation_time }} minutes</td>
										</tr>
										<tr>
											<td class="fw-bold">Sort Order:</td>
											<td>{{ $menuItem->sort_order }}</td>
										</tr>
										@if($menuItem->available_from)
										<tr>
											<td class="fw-bold">Available From:</td>
											<td>{{ \Carbon\Carbon::parse($menuItem->available_from)->format('H:i') }}</td>
										</tr>
										@endif
										@if($menuItem->available_until)
										<tr>
											<td class="fw-bold">Available Until:</td>
											<td>{{ \Carbon\Carbon::parse($menuItem->available_until)->format('H:i') }}</td>
										</tr>
										@endif
										<tr>
											<td class="fw-bold">Has Variations:</td>
											<td>{{ $menuItem->has_variations ? 'Yes' : 'No' }}</td>
										</tr>
									</table>
								</div>
							</div>

							@if($menuItem->description)
							<div class="row mt-3">
								<div class="col-12">
									<h5>Description</h5>
									<p>{{ $menuItem->description }}</p>
								</div>
							</div>
							@endif

							<div class="row mt-3">
								<div class="col-md-4">
									<h6>Dietary Information</h6>
									<div>
										@if($menuItem->is_vegetarian)
											<span class="badge badge-info me-1">Vegetarian</span>
										@endif
										@if($menuItem->is_vegan)
											<span class="badge badge-primary me-1">Vegan</span>
										@endif
										@if($menuItem->is_gluten_free)
											<span class="badge badge-warning me-1">Gluten Free</span>
										@endif
										@if($menuItem->is_popular)
											<span class="badge badge-success me-1">Popular</span>
										@endif
									</div>
								</div>
								<div class="col-md-4">
									<h6>Inventory</h6>
									<p>
										<strong>Track Inventory:</strong> {{ $menuItem->track_inventory ? 'Yes' : 'No' }}<br>
										@if($menuItem->track_inventory)
											<strong>Current Stock:</strong> {{ $menuItem->inventory_count ?? 0 }}
										@endif
									</p>
								</div>
								<div class="col-md-4">
									<h6>Performance</h6>
									<p>
										<strong>Total Sales:</strong> {{ $menuItem->total_sales ?? 0 }}<br>
										<strong>Total Reviews:</strong> {{ $menuItem->total_reviews ?? 0 }}<br>
										<strong>Average Rating:</strong> {{ number_format($menuItem->average_rating ?? 0, 1) }}/5
									</p>
								</div>
							</div>

							@if($menuItem->ingredients)
							<div class="row mt-3">
								<div class="col-md-6">
									<h6>Ingredients</h6>
									<p>{{ $menuItem->ingredients }}</p>
								</div>
								@if($menuItem->allergens)
								<div class="col-md-6">
									<h6>Allergens</h6>
									<p class="text-danger">{{ $menuItem->allergens }}</p>
								</div>
								@endif
							</div>
							@endif

							@if($menuItem->meta_title || $menuItem->meta_description)
							<div class="row mt-3">
								<div class="col-12">
									<h6>SEO Information</h6>
									@if($menuItem->meta_title)
										<p><strong>Meta Title:</strong> {{ $menuItem->meta_title }}</p>
									@endif
									@if($menuItem->meta_description)
										<p><strong>Meta Description:</strong> {{ $menuItem->meta_description }}</p>
									@endif
								</div>
							</div>
							@endif
						</div>
					</div>
				</div>

				<div class="col-lg-4">
					<div class="box">
						<div class="box-header with-border">
							<h4 class="box-title">Item Image</h4>
						</div>
						<div class="box-body text-center">
							@if($menuItem->image_url)
								<img src="{{ $menuItem->image_url }}" class="img-fluid" alt="{{ $menuItem->item_name }}" style="max-height: 300px;" />
							@else
								<img src="{{ asset('images/food/dish-1.png') }}" class="img-fluid" alt="No image" style="max-height: 300px;" />
								<p class="text-muted mt-2">No image uploaded</p>
							@endif
						</div>
					</div>

					<div class="box">
						<div class="box-header with-border">
							<h4 class="box-title">Quick Actions</h4>
						</div>
						<div class="box-body">
							<div class="d-grid gap-2">
								<a href="{{ route('restaurant.menu.edit', $menuItem->id) }}" class="btn btn-warning">
									<i class="fa fa-edit"></i> Edit Menu Item
								</a>
								<form action="{{ route('restaurant.menu.toggle', $menuItem->id) }}" method="POST">
									@csrf
									@method('PATCH')
									<button type="submit" class="btn {{ $menuItem->is_available ? 'btn-danger' : 'btn-success' }} w-100">
										<i class="fa fa-{{ $menuItem->is_available ? 'eye-slash' : 'eye' }}"></i> 
										{{ $menuItem->is_available ? 'Mark Unavailable' : 'Mark Available' }}
									</button>
								</form>
								<a href="{{ route('restaurant.menu.duplicate', $menuItem->id) }}" class="btn btn-info">
									<i class="fa fa-plus-square-o"></i> Duplicate Item
								</a>
								<form action="{{ route('restaurant.menu.destroy', $menuItem->id) }}" method="POST" 
									  onsubmit="return confirm('Are you sure you want to delete this menu item?')">
									@csrf
									@method('DELETE')
									<button type="submit" class="btn btn-danger w-100">
										<i class="fa fa-trash"></i> Delete Menu Item
									</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- /.content -->
@endsection
