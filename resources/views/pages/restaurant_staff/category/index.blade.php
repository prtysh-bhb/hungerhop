{{-- Extend the main layout --}}
@extends('layouts.admin')

{{-- Define the title for this page --}}
@section('title', 'Menu Categories')

{{-- Define the main content for this page --}}
@section('content')
		<!-- Content Header (Page header) -->	  
		<div class="content-header">
			<div class="d-flex align-items-center">
				<div class="me-auto">
					<h4 class="page-title">Menu Categories</h4>
					<div class="d-inline-block align-items-center">
						<nav>
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
								<li class="breadcrumb-item" aria-current="page">Menu</li>
								<li class="breadcrumb-item active" aria-current="page">Menu Categories</li>
							</ol>
						</nav>
					</div>
				</div>
				
			</div>
		</div>


		<!-- Filter Section (Search & Active/Inactive) -->
		<section class="content mb-4">
			<div class="row">
				<div class="col-md-12">
					<div class="box">
						<div class="box-header with-border">
							<h4 class="box-title">Filter & Search Menu Categories</h4>
							<div class="box-tools">
								<button type="button" class="btn btn-primary btn-sm" id="clearFilters">Clear All Filters</button>
								<a href="{{ route('restaurant.categories.create') }}"><button type="button" class="btn btn-primary btn-sm" id="addCategoryBtn">Add Categories</button></a>
							</div>
						</div>
						<div class="box-body">
							<form id="filterForm">
								<div class="row">
									<!-- Search Input -->
									<div class="col-lg-6 col-md-8 col-12 mb-3">
										<label class="form-label">Search Categories</label>
										<div class="input-group">
											<input type="text" class="form-control" name="search" placeholder="Search categories..." id="searchInput">
											<span class="input-group-text"><i class="fa fa-search"></i></span>
										</div>
									</div>
									<!-- Active/Inactive Filter -->
									<div class="col-lg-3 col-md-4 col-12 mb-3">
										<label class="form-label">Status</label>
										<select class="form-control" name="status" id="statusFilter">
											<option value="">All</option>
											<option value="active">Active</option>
											<option value="inactive">Inactive</option>
										</select>
									</div>
								</div>
							</form>
							<!-- Filter Results Summary -->
							<div class="row mt-3">
								<div class="col-12">
									<div class="alert alert-info" id="filterResults" style="display: none;">
										<i class="fa fa-info-circle"></i> <span id="resultCount">0</span> categories found matching your filters.
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- Category Grid -->
		<section class="content">
			<div class="table-responsive" id="categoryGrid">
				<table class="table border-no" id="example1">
					<thead>
						<tr>
							<th>ID</th>
							<th>Name</th>
							<th>Description</th>
							<th>Image</th>
							<th>Status</th>
							<th>Sort Order</th>
							<th>Menu Template</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
					@foreach($categories as $category)
						<tr class="category-card" data-name="{{ strtolower($category->name) }}" data-status="{{ $category->is_active ? 'active' : 'inactive' }}">
							<td>{{ $category->id }}</td>
							<td>{{ $category->name }}</td>
							<td>{{ $category->description }}</td>
							<td>
								<img src="{{ $category->image_url ?? asset('images/default-category.png') }}" alt="Category Image" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
							</td>
							<td>
								<span class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span>
							</td>
							<td>{{ $category->sort_order }}</td>
							<td>{{ $category->menu_template_id ?? '-' }}</td>
							<td>
								<div class="btn-group" style="gap: 5px;">
									<a href="{{ route('restaurant.categories.edit', $category) }}" class="btn btn-sm btn-warning mr-2" >Edit</a>
								</div>
								<form action="{{ route('restaurant.categories.destroy', $category) }}" method="POST" class="d-inline" id="catDestroy{{ $category->id }}">
									@csrf
									@method('DELETE')
									<button form="catDestroy{{ $category->id }}" type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
								</form>
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</section>

	
		<!-- /.content -->

		<!-- Advanced Filtering JavaScript -->
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				const filterForm = document.getElementById('filterForm');
				const categoryGrid = document.getElementById('categoryGrid');
				const clearFiltersBtn = document.getElementById('clearFilters');
				const filterResults = document.getElementById('filterResults');
				const resultCount = document.getElementById('resultCount');
				
				// Get all filter inputs
				const searchInput = document.getElementById('searchInput');
				const statusFilter = document.getElementById('statusFilter');
				
				// Filter function
				function filterCategories() {
					const searchValue = searchInput.value.toLowerCase().trim();
					const statusValue = statusFilter.value;
					const categoryCards = document.querySelectorAll('.category-card');
					let visibleCount = 0;
					categoryCards.forEach(card => {
						const name = card.dataset.name;
						const status = card.dataset.status;
						// Search filter
						const matchSearch = !searchValue || name.includes(searchValue);
						// Status filter
						const matchStatus = !statusValue || status === statusValue;
						// Show/hide card based on filters
						const shouldShow = matchSearch && matchStatus;
						if (shouldShow) {
							card.style.display = '';
							visibleCount++;
						} else {
							card.style.display = 'none';
						}
					});
					// Update result count
					resultCount.textContent = visibleCount;
					if (hasActiveFilters()) {
						filterResults.style.display = 'block';
					} else {
						filterResults.style.display = 'none';
					}
					// Add smooth animation
					categoryGrid.style.opacity = '0.7';
					setTimeout(() => {
						categoryGrid.style.opacity = '1';
					}, 150);
				}
				
				// Check if any filters are active
				function hasActiveFilters() {
					return searchInput.value.trim() !== '' || statusFilter.value !== '';
				}
				
				// Clear all filters
				function clearAllFilters() {
					searchInput.value = '';
					statusFilter.value = '';
					filterCategories();
				}
				
				// Event listeners
				filterForm.addEventListener('input', filterCategories);
				filterForm.addEventListener('change', filterCategories);
				clearFiltersBtn.addEventListener('click', clearAllFilters);
				
				// Debounce search input for better performance
				let searchTimeout;
				searchInput.addEventListener('input', function() {
					clearTimeout(searchTimeout);
					searchTimeout = setTimeout(filterCategories, 300);
				});
				
				// Mobile optimization - collapse filters on small screens
				function handleMobileFilters() {
					if (window.innerWidth < 768) {
						const filterBox = document.querySelector('.box-body');
						filterBox.classList.add('mobile-filters');
						
						// Add mobile-specific styles
						const style = document.createElement('style');
						style.textContent = `
							.mobile-filters .row {
								margin-bottom: 0;
							}
							.mobile-filters .col-lg-3,
							.mobile-filters .col-md-6 {
								padding: 5px;
							}
							.mobile-filters .form-control {
								font-size: 14px;
								padding: 8px 12px;
							}
							.mobile-filters .form-label {
								font-size: 12px;
								margin-bottom: 4px;
							}
							@media (max-width: 767px) {
								.category-card {
									margin-bottom: 15px;
								}
								.box-header {
									padding: 10px 15px;
								}
								.box-title {
									font-size: 16px;
								}
							}
						`;
						document.head.appendChild(style);
					}
				}
				
				// Initialize mobile optimizations
				handleMobileFilters();
				window.addEventListener('resize', handleMobileFilters);
				
				// Add keyboard shortcuts for power users
				document.addEventListener('keydown', function(e) {
					// Ctrl/Cmd + K to focus search
					if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
						e.preventDefault();
						searchInput.focus();
					}
					
					// Escape to clear search
					if (e.key === 'Escape' && document.activeElement === searchInput) {
						searchInput.value = '';
						filterCategories();
					}
				});
				
				// Initialize tooltips for better UX
				function initializeTooltips() {
					const badges = document.querySelectorAll('[title]');
					badges.forEach(badge => {
						badge.addEventListener('mouseenter', function() {
							const tooltip = document.createElement('div');
							tooltip.className = 'custom-tooltip';
							tooltip.textContent = this.getAttribute('title');
							tooltip.style.cssText = `
								position: absolute;
								background: rgba(0,0,0,0.8);
								color: white;
								padding: 5px 10px;
								border-radius: 4px;
								font-size: 12px;
								z-index: 1000;
								pointer-events: none;
								white-space: nowrap;
							`;
							document.body.appendChild(tooltip);
							
							const rect = this.getBoundingClientRect();
							tooltip.style.left = rect.left + 'px';
							tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
							
							this._tooltip = tooltip;
						});
						
						badge.addEventListener('mouseleave', function() {
							if (this._tooltip) {
								document.body.removeChild(this._tooltip);
								this._tooltip = null;
							}
						});
					});
				}
				
				// Initialize tooltips
				initializeTooltips();
				
				// Performance optimization: Virtual scrolling for large datasets
				function optimizeForLargeDatasets() {
					const cards = document.querySelectorAll('.category-card');
					if (cards.length > 50) {
						// Implement lazy loading or virtual scrolling here
						console.log('Large dataset detected, consider implementing virtual scrolling');
					}
				}
				
				optimizeForLargeDatasets();
			});
		</script>

		<!-- Mobile-responsive CSS enhancements -->
		<style>
			/* Enhanced mobile responsiveness */
			@media (max-width: 768px) {
				.content-header .page-title {
					font-size: 24px;
				}
				
				.breadcrumb {
					font-size: 12px;
				}
				
				.box-header .btn {
					font-size: 12px;
					padding: 5px 10px;
				}
				
				.category-card .box {
					margin-bottom: 15px;
				}
				
				.category-card .info-content h4 {
					font-size: 16px;
				}
				
				.category-card .text-primary {
					font-size: 14px;
				}
				
				.position-absolute .badge {
					font-size: 10px;
					padding: 2px 6px;
				}
			}
			
			@media (max-width: 576px) {
				.col-xxxl-4,
				.col-xl-6,
				.col-lg-6 {
					flex: 0 0 100%;
					max-width: 100%;
				}
				
				.filter-form .row {
					margin: 0 -5px;
				}
				
				.filter-form .col-lg-3,
				.filter-form .col-md-6 {
					padding: 0 5px;
					margin-bottom: 10px;
				}
			}
			
			/* Enhanced visual feedback */
			.category-card {
				transition: all 0.3s ease;
			}
			
			.category-card:hover {
				transform: translateY(-2px);
				box-shadow: 0 4px 15px rgba(0,0,0,0.1);
			}
			
			.form-control:focus {
				border-color: #007bff;
				box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
			}
			
			/* Loading animation */
			.category-grid-loading {
				opacity: 0.7;
				transition: opacity 0.3s ease;
			}
			
			/* Custom scrollbar for filter section */
			.box-body::-webkit-scrollbar {
				width: 6px;
			}
			
			.box-body::-webkit-scrollbar-track {
				background: #f1f1f1;
			}
			
			.box-body::-webkit-scrollbar-thumb {
				background: #888;
				border-radius: 3px;
			}
			
			.box-body::-webkit-scrollbar-thumb:hover {
				background: #555;
			}
		</style>
@endsection
