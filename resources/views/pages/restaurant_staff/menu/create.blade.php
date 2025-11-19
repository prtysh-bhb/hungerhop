{{-- Extend the main layout --}}
@extends('layouts.admin')

{{-- Define the title for this page --}}
@section('title', isset($menuItem) ? 'Edit Menu Item' : 'Add New Menu Item')

{{-- Define the main content for this page --}}
@section('content')
		<!-- Content Header (Page header) -->	  
		<div class="content-header">
			<div class="d-flex align-items-center">
				<div class="me-auto">
					<h4 class="page-title">{{ isset($menuItem) ? 'Edit Menu Item' : 'Add New Menu Item' }}</h4>
					<div class="d-inline-block align-items-center">
						<nav>
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
								<li class="breadcrumb-item" aria-current="page">Menu</li>
								<li class="breadcrumb-item active" aria-current="page">{{ isset($menuItem) ? 'Edit' : 'Add New' }}</li>
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
				  <div class="box-body">
					@if($errors->any())
						<div class="alert alert-danger">
							<ul class="mb-0">
								@foreach($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif

					@if(session('success'))
						<div class="alert alert-success">
							{{ session('success') }}
						</div>
					@endif

					@if(session('error'))
						<div class="alert alert-danger">
							{{ session('error') }}
						</div>
					@endif

					<form action="{{ isset($menuItem) ? route('restaurant.menu.update', $menuItem) : route('restaurant.menu.store') }}" 
						  method="POST" 
						  enctype="multipart/form-data">
						@csrf
						@if(isset($menuItem))
							@method('PUT')
						@endif

						<div class="form-body">
							<!-- Basic Required Fields -->
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
									  <label class="fw-700 fs-16 form-label">Menu Name <span class="text-danger">*</span></label>
									  <input type="text" 
											 name="item_name" 
											 class="form-control @error('item_name') is-invalid @enderror" 
											 placeholder="Enter menu item name"
											 value="{{ old('item_name', $menuItem->item_name ?? '') }}"
											 required>
									  @error('item_name')
										<div class="invalid-feedback">{{ $message }}</div>
									  @enderror
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="fw-700 fs-16 form-label">Category <span class="text-danger">*</span></label>
										<select name="menu_category_id" 
												class="form-select @error('menu_category_id') is-invalid @enderror" 
												required>
											<option value="">Select Category</option>
											@if(isset($categories))
												@foreach($categories as $category)
													<option value="{{ $category->id }}" 
															{{ old('menu_category_id', $menuItem->menu_category_id ?? '') == $category->id ? 'selected' : '' }}>
														{{ $category->name }}
													</option>
												@endforeach
											@endif
										</select>
										@error('menu_category_id')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label class="fw-700 fs-16 form-label">Base Price <span class="text-danger">*</span></label>
										<input type="number" 
											   name="base_price" 
											   step="0.01" 
											   min="0"
											   class="form-control @error('base_price') is-invalid @enderror" 
											   placeholder="0.00"
											   value="{{ old('base_price', $menuItem->base_price ?? '') }}"
											   required>
										@error('base_price')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label class="fw-700 fs-16 form-label">Preparation Time (minutes)</label>
										<input type="number" 
											   name="preparation_time" 
											   min="1" 
											   max="240"
											   class="form-control @error('preparation_time') is-invalid @enderror" 
											   placeholder="15"
											   value="{{ old('preparation_time', $menuItem->preparation_time ?? 15) }}">
										@error('preparation_time')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<label class="fw-700 fs-16 form-label">Available</label>
										<select name="is_available" class="form-control">
											<option value="1" {{ old('is_available', $menuItem->is_available ?? 1) == 1 ? 'selected' : '' }}>Yes</option>
											<option value="0" {{ old('is_available', $menuItem->is_available ?? 1) == 0 ? 'selected' : '' }}>No</option>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label class="fw-700 fs-16 form-label">Status</label>
										<select name="status" class="form-control @error('status') is-invalid @enderror">
											<option value="active" {{ old('status', $menuItem->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
											<option value="inactive" {{ old('status', $menuItem->status ?? 'active') == 'inactive' ? 'selected' : '' }}>Inactive</option>
											<option value="draft" {{ old('status', $menuItem->status ?? 'active') == 'draft' ? 'selected' : '' }}>Draft</option>
										</select>
										@error('status')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="fw-700 fs-16 form-label">Description</label>
										<textarea name="description" 
												  class="form-control @error('description') is-invalid @enderror" 
												  rows="3" 
												  placeholder="Enter menu item description">{{ old('description', $menuItem->description ?? '') }}</textarea>
										@error('description')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="fw-700 fs-16 form-label">Ingredients</label>
										<textarea name="ingredients" 
												  class="form-control @error('ingredients') is-invalid @enderror" 
												  rows="3" 
												  placeholder="List main ingredients (comma separated)">{{ old('ingredients', $menuItem->ingredients ?? '') }}</textarea>
										@error('ingredients')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="fw-700 fs-16 form-label">Allergens</label>
										<textarea name="allergens" 
												  class="form-control @error('allergens') is-invalid @enderror" 
												  rows="3" 
												  placeholder="List allergens (comma separated)">{{ old('allergens', $menuItem->allergens ?? '') }}</textarea>
										@error('allergens')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>
							</div>

							<!-- Dietary Options -->
							<div class="row">
								<div class="col-md-12">
									<h5 class="mt-20">Dietary Options</h5>
									<div class="form-group">
										<div class="row">
											<div class="col-md-3">
												<div class="checkbox checkbox-info">
													<input type="checkbox" 
														   name="is_vegetarian" 
														   id="is_vegetarian" 
														   value="1"
														   {{ old('is_vegetarian', $menuItem->is_vegetarian ?? false) ? 'checked' : '' }}>
													<label for="is_vegetarian">Vegetarian</label>
												</div>
											</div>
											<div class="col-md-3">
												<div class="checkbox checkbox-info">
													<input type="checkbox" 
														   name="is_vegan" 
														   id="is_vegan" 
														   value="1"
														   {{ old('is_vegan', $menuItem->is_vegan ?? false) ? 'checked' : '' }}>
													<label for="is_vegan">Vegan</label>
												</div>
											</div>
											<div class="col-md-3">
												<div class="checkbox checkbox-info">
													<input type="checkbox" 
														   name="is_gluten_free" 
														   id="is_gluten_free" 
														   value="1"
														   {{ old('is_gluten_free', $menuItem->is_gluten_free ?? false) ? 'checked' : '' }}>
													<label for="is_gluten_free">Gluten Free</label>
												</div>
											</div>
											<div class="col-md-3">
												<div class="checkbox checkbox-warning">
													<input type="checkbox" 
														   name="is_popular" 
														   id="is_popular" 
														   value="1"
														   {{ old('is_popular', $menuItem->is_popular ?? false) ? 'checked' : '' }}>
													<label for="is_popular">Popular Item</label>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Inventory Management -->
							<div class="row">
								<div class="col-md-12">
									<h5 class="mt-20">Inventory Management</h5>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label class="fw-700 fs-16 form-label">Sort Order</label>
										<input type="number" 
											   name="sort_order" 
											   min="0"
											   class="form-control @error('sort_order') is-invalid @enderror" 
											   placeholder="0"
											   value="{{ old('sort_order', $menuItem->sort_order ?? 0) }}">
										@error('sort_order')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>
							</div>

							<!-- Availability Schedule -->
							<div class="row">
								<div class="col-md-12">
									<h5 class="mt-20">Availability Schedule</h5>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="fw-700 fs-16 form-label">Available From</label>
										<input type="time" 
											   name="available_from" 
											   class="form-control @error('available_from') is-invalid @enderror"
											   value="{{ old('available_from', $menuItem->available_from ?? '') }}">
										@error('available_from')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="fw-700 fs-16 form-label">Available Until</label>
										<input type="time" 
											   name="available_until" 
											   class="form-control @error('available_until') is-invalid @enderror"
											   value="{{ old('available_until', $menuItem->available_until ?? '') }}">
										@error('available_until')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>
							</div>

							<!-- SEO Fields -->
							<div class="row">
								<div class="col-md-12">
									<h5 class="mt-20">SEO Settings</h5>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="fw-700 fs-16 form-label">Meta Title</label>
										<input type="text" 
											   name="meta_title" 
											   class="form-control @error('meta_title') is-invalid @enderror"
											   placeholder="SEO title for this menu item"
											   value="{{ old('meta_title', $menuItem->meta_title ?? '') }}">
										@error('meta_title')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="fw-700 fs-16 form-label">Meta Keywords</label>
										<input type="text" 
											   name="meta_keywords" 
											   class="form-control @error('meta_keywords') is-invalid @enderror"
											   placeholder="Keywords separated by commas"
											   value="{{ old('meta_keywords', $menuItem->meta_keywords ?? '') }}">
										@error('meta_keywords')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group">
										<label class="fw-700 fs-16 form-label">Meta Description</label>
										<textarea name="meta_description" 
												  class="form-control @error('meta_description') is-invalid @enderror" 
												  rows="2" 
												  placeholder="SEO description for this menu item">{{ old('meta_description', $menuItem->meta_description ?? '') }}</textarea>
										@error('meta_description')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>
							</div>

							<!-- Additional Fields -->
							<div class="row">
								<div class="col-md-12">
									<h5 class="mt-20">Additional Information</h5>
								</div>
							</div>

							<!-- Menu Item Image -->
							<div class="row">
								<div class="col-md-12">
									<h4 class="box-title mt-20">Menu Item Image</h4>
									<div class="product-img text-start">
										@if(isset($menuItem) && $menuItem->image_url)
											<img src="{{ $menuItem->image_url }}" alt="{{ $menuItem->item_name }}" class="mb-15" style="max-width: 200px;">
											<p>Current Image</p>
										@else
											<img src="{{ asset('images/product/product-9.png') }}" alt="" class="mb-15">
											<p>Upload Menu Item Image</p>
										@endif
										<div class="btn btn-info mb-20">
                                            <input type="file" 
												   name="image" 
												   class="upload @error('image') is-invalid @enderror"
												   accept="image/*"> 
										</div>
										@error('image')
											<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
								</div>
							</div>
						</div>
						
						<div class="form-actions mt-10">
							<button type="submit" class="btn btn-primary"> 
								<i class="fa fa-check"></i> {{ isset($menuItem) ? 'Update' : 'Save' }} Menu Item
							</button>
							<a href="{{ route('restaurant.menu.list') }}" class="btn btn-danger">Cancel</a>
						</div>
					</form>
				  </div>
				</div>
			  </div>		  
		  </div>
		</section>
		<!-- /.content -->
@endsection
