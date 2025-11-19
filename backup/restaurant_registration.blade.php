<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="HungerHop - Restaurant Registration">
    <meta name="author" content="HungerHop">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <title>HungerHop - Restaurant Registration</title>
  
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor_components/bootstrap/dist/css/bootstrap.min.css') }}">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('assets/icons/font-awesome/css/font-awesome.min.css') }}">
    
    <!-- Themify Icons -->
    <link rel="stylesheet" href="{{ asset('assets/icons/themify-icons/themify-icons.css') }}">
    
    <!-- Ion Icons -->
    <link rel="stylesheet" href="{{ asset('assets/icons/Ionicons/css/ionicons.min.css') }}">
    
    <!-- Perfect Scrollbar -->
    <link rel="stylesheet" href="{{ asset('assets/vendor_components/perfect-scrollbar/css/perfect-scrollbar.css') }}">
    
    <!-- Vendors Style-->
    <link rel="stylesheet" href="{{ asset('css/vendors_css.css') }}">
      
    <!-- Style-->  
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/skin_color.css') }}">

    <style>
        body.restaurant-registration-page {
            background: linear-gradient(135deg, rgba(0,0,0,0.7), rgba(0,0,0,0.5)), 
                        url('{{ asset("images/food/main.jpg") }}'), 
                        url('{{ asset("images/auth-bg/bg-8.jpg") }}'),
                        linear-gradient(45deg, #ff5722, #ff9800);
            background-size: cover, cover, cover, cover;
            background-position: center, center, center, center;
            background-repeat: no-repeat, no-repeat, no-repeat, no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        body.restaurant-registration-page::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, 
                rgba(255,87,34,0.1) 0%, 
                rgba(156,39,176,0.1) 25%, 
                rgba(3,169,244,0.1) 50%, 
                rgba(76,175,80,0.1) 75%, 
                rgba(255,193,7,0.1) 100%);
            z-index: -2;
            pointer-events: none;
        }
        
        body.restaurant-registration-page::after {
            content: '🍕 🍔 🍜 🍰 ☕ 🥗 🍖 🍱 🍝 🥘 🍤 🍳 🥪 🌮 🍕 🍔 🍜 🍰 ☕ 🥗';
            position: fixed;
            top: 0;
            left: -100%;
            width: 200%;
            height: 100%;
            display: flex;
            align-items: center;
            font-size: 2rem;
            opacity: 0.03;
            animation: floatFood 60s linear infinite;
            z-index: -1;
            pointer-events: none;
            white-space: nowrap;
        }
        
        @keyframes floatFood {
            0% { transform: translateX(0); }
            100% { transform: translateX(50%); }
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .step-indicator .step {
            flex: 1;
            text-align: center;
            position: relative;
        }
        .step-indicator .step::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: #e9ecef;
            z-index: 1;
        }
        .step-indicator .step:first-child::before {
            left: 50%;
        }
        .step-indicator .step:last-child::before {
            right: 50%;
        }
        .step-indicator .step.active::before {
            background: #ff5722;
        }
        .step-indicator .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            position: relative;
            z-index: 2;
        }
        .step-indicator .step.active .step-circle {
            background: #ff5722;
            color: white;
        }
        .step-indicator .step.completed .step-circle {
            background: #4caf50;
            color: white;
        }
        .registration-container {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2), 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            margin: 20px 0;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .registration-header {
            background: linear-gradient(135deg, #ff5722 0%, #ff9800 50%, #ffc107 100%);
            color: white;
            padding: 25px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .registration-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('{{ asset("images/food/dish-1.png") }}') no-repeat center;
            background-size: 300px;
            opacity: 0.1;
            transform: rotate(-15deg);
        }
        
        .registration-header h2 {
            position: relative;
            z-index: 2;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .registration-header p {
            position: relative;
            z-index: 2;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .card-header {
            background: linear-gradient(135deg, #2196f3 0%, #21cbf3 100%) !important;
            border: none;
        }
        
        .card-header.bg-success {
            background: linear-gradient(135deg, #4caf50 0%, #8bc34a 100%) !important;
        }
        
        .card-header.bg-warning {
            background: linear-gradient(135deg, #ff9800 0%, #ffc107 100%) !important;
        }
        
        .card-header.bg-info {
            background: linear-gradient(135deg, #00bcd4 0%, #009688 100%) !important;
        }
        
        .card {
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #ff5722;
            box-shadow: 0 0 0 0.2rem rgba(255, 87, 34, 0.25);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #4caf50 0%, #8bc34a 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.3);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .registration-container {
                margin: 10px;
                border-radius: 10px;
            }
            
            .registration-header {
                padding: 20px;
            }
            
            body.restaurant-registration-page {
                background-attachment: scroll;
            }
        }
        
        /* Animation for form elements */
        .card {
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card:nth-child(1) { animation-delay: 0.1s; }
        .card:nth-child(2) { animation-delay: 0.2s; }
        .card:nth-child(3) { animation-delay: 0.3s; }
        .card:nth-child(4) { animation-delay: 0.4s; }
    </style>

</head>
	
<body class="hold-transition theme-primary restaurant-registration-page">
	
	<div class="container-fluid h-p100">
		<div class="row justify-content-center align-items-center h-p100">	
			<div class="col-12 col-lg-10 col-xl-8">
				<div class="registration-container">
					<div class="registration-header">
						<h2 class="mb-0">
							<i class="fa fa-utensils me-2"></i>
							Restaurant Registration
							<i class="fa fa-concierge-bell ms-2"></i>
						</h2>
						<p class="mb-0 mt-2">
							<i class="fa fa-leaf me-1"></i>
							Join HungerHop and start serving your customers online
							<i class="fa fa-heart ms-1 text-danger"></i>
						</p>
						<small class="d-block mt-2 opacity-75">
							<i class="fa fa-star me-1"></i>
							Professional restaurant management made easy
							<i class="fa fa-star ms-1"></i>
						</small>
					</div>
					
					<div class="p-4">
						<!-- Back to Login Link -->
						<div class="mb-3">
							<a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm">
								<i class="fa fa-arrow-left me-1"></i> Back to Login
							</a>
						</div>

						@if($errors->any())
							<div class="alert alert-danger">
								<ul class="mb-0">
									@foreach($errors->all() as $error)
										<li>{{ $error }}</li>
									@endforeach
								</ul>
							</div>
						@endif

						<form action="{{ route('public.restaurant.registration.store') }}" method="POST" enctype="multipart/form-data">
							@csrf
							
							<!-- Basic Information Section -->
							<div class="card mb-4">
								<div class="card-header bg-primary text-white">
									<h5 class="mb-0"><i class="fa fa-info-circle me-2"></i>Basic Information</h5>
								</div>
								<div class="card-body">
									<div class="row">
										<div class="col-md-6">
											<div class="form-group mb-3">
												<label for="restaurant_name">Restaurant Name <span class="text-danger">*</span></label>
												<input type="text" class="form-control" id="restaurant_name" name="restaurant_name" value="{{ old('restaurant_name') }}" required>
											</div>
										</div>
										
										<div class="col-md-6">
											<div class="form-group mb-3">
												<label for="email">Email <span class="text-danger">*</span></label>
												<input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
											</div>
										</div>
										
										<div class="col-md-6">
											<div class="form-group mb-3">
												<label for="phone">Phone <span class="text-danger">*</span></label>
												<input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
											</div>
										</div>
										
										<div class="col-md-6">
											<div class="form-group mb-3">
												<label for="cuisine_type">Cuisine Type</label>
												<input type="text" class="form-control" id="cuisine_type" name="cuisine_type" value="{{ old('cuisine_type') }}" placeholder="e.g., Italian, Chinese, Mexican">
											</div>
										</div>
										
										<div class="col-md-6">
											<div class="form-group mb-3">
												<label for="website_url">Website URL</label>
												<input type="url" class="form-control" id="website_url" name="website_url" value="{{ old('website_url') }}">
											</div>
										</div>
										
										<div class="col-md-12">
											<div class="form-group mb-3">
												<label for="description">Description</label>
												<textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Address Information Section -->
							<div class="card mb-4">
								<div class="card-header bg-success text-white">
									<h5 class="mb-0"><i class="fa fa-map-marker-alt me-2"></i>Address Information</h5>
								</div>
								<div class="card-body">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group mb-3">
												<label for="address">Address <span class="text-danger">*</span></label>
												<textarea class="form-control" id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
											</div>
										</div>
										
										<div class="col-md-4">
											<div class="form-group mb-3">
												<label for="city">City <span class="text-danger">*</span></label>
												<input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}" required>
											</div>
										</div>
										
										<div class="col-md-4">
											<div class="form-group mb-3">
												<label for="state">State <span class="text-danger">*</span></label>
												<input type="text" class="form-control" id="state" name="state" value="{{ old('state') }}" required>
											</div>
										</div>
										
										<div class="col-md-4">
											<div class="form-group mb-3">
												<label for="postal_code">ZIP Code <span class="text-danger">*</span></label>
												<input type="text" class="form-control" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" required>
											</div>
										</div>
										
										<div class="col-md-6">
											<div class="form-group mb-3">
												<label for="latitude">Latitude <span class="text-danger">*</span></label>
												<input type="number" step="any" class="form-control" id="latitude" name="latitude" value="{{ old('latitude') }}" required>
												<small class="text-muted">You can get this from Google Maps</small>
											</div>
										</div>
										
										<div class="col-md-6">
											<div class="form-group mb-3">
												<label for="longitude">Longitude <span class="text-danger">*</span></label>
												<input type="number" step="any" class="form-control" id="longitude" name="longitude" value="{{ old('longitude') }}" required>
												<small class="text-muted">You can get this from Google Maps</small>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Business Configuration Section -->
							<div class="card mb-4">
								<div class="card-header bg-warning text-dark">
									<h5 class="mb-0"><i class="fa fa-cogs me-2"></i>Business Configuration</h5>
								</div>
								<div class="card-body">
									<div class="row">
										<div class="col-md-4">
											<div class="form-group mb-3">
												<label for="delivery_radius_km">Delivery Radius (KM) <span class="text-danger">*</span></label>
												<input type="number" class="form-control" id="delivery_radius_km" name="delivery_radius_km" value="{{ old('delivery_radius_km', 10) }}" min="1" max="50" required>
											</div>
										</div>
										
										<div class="col-md-4">
											<div class="form-group mb-3">
												<label for="minimum_order_amount">Minimum Order Amount <span class="text-danger">*</span></label>
												<input type="number" step="0.01" class="form-control" id="minimum_order_amount" name="minimum_order_amount" value="{{ old('minimum_order_amount', 0) }}" min="0" required>
											</div>
										</div>
										
										<div class="col-md-4">
											<div class="form-group mb-3">
												<label for="base_delivery_fee">Base Delivery Fee <span class="text-danger">*</span>
                                                </label>
												<input type="number" step="0.01" class="form-control" id="base_delivery_fee" name="base_delivery_fee" value="{{ old('base_delivery_fee', 0) }}" min="0" required>
											</div>
										</div>
										
										<div class="col-md-4">
											<div class="form-group mb-3">
												<label for="restaurant_commission_percentage">Commission Percentage <span class="text-danger">*</span></label>
												<input type="number" step="0.01" class="form-control" id="restaurant_commission_percentage" name="restaurant_commission_percentage" value="{{ old('restaurant_commission_percentage', 80) }}" min="0" max="100" required>
												<small class="text-muted">Percentage of order value you receive</small>
											</div>
										</div>
										
										<div class="col-md-4">
											<div class="form-group mb-3">
												<label for="estimated_delivery_time">Estimated Delivery Time (minutes) <span class="text-danger">*</span></label>
												<input type="number" class="form-control" id="estimated_delivery_time" name="estimated_delivery_time" value="{{ old('estimated_delivery_time', 30) }}" min="10" max="120" required>
											</div>
										</div>
										
										<div class="col-md-4">
											<div class="form-group mb-3">
												<label for="tax_percentage">Tax Percentage <span class="text-danger">*</span></label>
												<input type="number" step="0.01" class="form-control" id="tax_percentage" name="tax_percentage" value="{{ old('tax_percentage', 0) }}" min="0" max="50" required>
											</div>
										</div>
										
										<div class="col-md-12">
											<div class="form-group mb-3">
												<label>Business Hours</label>
												<div class="card">
													<div class="card-body">
														<div class="row mb-2 align-items-center">
															<div class="col-md-5">
																<label class="form-label">Open Time</label>
																<input type="time" class="form-control" name="business_hours[open]" value="{{ old("business_hours.open", '09:00') }}">
															</div>
															<div class="col-md-5">
																<label class="form-label">Close Time</label>
																<input type="time" class="form-control" name="business_hours[close]" value="{{ old("business_hours.close", '22:00') }}">
															</div>
														</div>
													</div>
												</div>
												<input type="hidden" name="business_hours_json" id="business_hours_json">
											</div>
										</div>
										
										<div class="col-md-12">
											<div class="form-group mb-3">
												<label for="special_instructions">Special Instructions</label>
												<textarea class="form-control" id="special_instructions" name="special_instructions" rows="3">{{ old('special_instructions') }}</textarea>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Image Upload Section -->
							<div class="card mb-4">
								<div class="card-header bg-info text-white">
									<h5 class="mb-0"><i class="fa fa-image me-2"></i>Restaurant Images</h5>
								</div>
								<div class="card-body">
									<div class="row">
										<div class="col-md-6">
											<div class="form-group mb-3">
												<label for="image">Restaurant Logo/Image</label>
												<input type="file" class="form-control" id="image" name="image" accept="image/*">
												<small class="text-muted">JPG, PNG, GIF (max 2MB)</small>
											</div>
										</div>
										
										<div class="col-md-6">
											<div class="form-group mb-3">
												<label for="cover_image">Cover Image</label>
												<input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*">
												<small class="text-muted">JPG, PNG, GIF (max 2MB)</small>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Hidden fields for optional values -->
							<input type="hidden" name="tenant_id" value="">
							<input type="hidden" name="location_admin_id" value="">

							<!-- Submit Button -->
							<div class="text-center">
								<button type="submit" class="btn btn-success btn-lg px-5">
									<i class="fa fa-save me-2"></i> Register Restaurant
								</button>
								<a href="{{ route('login') }}" class="btn btn-secondary btn-lg px-5 ms-3">
									<i class="fa fa-times me-2"></i> Cancel
								</a>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- jQuery -->
	<script src="{{ asset('assets/vendor_plugins/JqueryPrintArea/demo/jquery-2.1.0.js') }}"></script>
	
	<!-- Bootstrap JS -->
	<script src="{{ asset('assets/vendor_components/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
	
	<!-- Perfect Scrollbar -->
	<script src="{{ asset('assets/vendor_components/perfect-scrollbar/dist/perfect-scrollbar.min.js') }}"></script>
	
	<!-- Feather Icons -->
	<script src="{{ asset('assets/icons/feather-icons/feather.min.js') }}"></script>
	
	<!-- Vendor JS -->
	<script src="{{ asset('js/vendors.min.js') }}"></script>
	
	<!-- Custom JS -->
	<script src="{{ asset('js/template.js') }}"></script>
	
	<script>
		// Form validation
		document.querySelector('form').addEventListener('submit', function(e) {
			const requiredFields = ['restaurant_name', 'email', 'phone', 'address', 'city', 'state', 'postal_code', 'latitude', 'longitude'];
			let hasError = false;
			
			requiredFields.forEach(function(field) {
				const input = document.querySelector('input[name="' + field + '"], textarea[name="' + field + '"]');
				if (!input.value.trim()) {
					hasError = true;
					input.classList.add('is-invalid');
				} else {
					input.classList.remove('is-invalid');
				}
			});
			
			if (hasError) {
				e.preventDefault();
				alert('Please fill in all required fields');
				return;
			}
		});
		
		// Get location from browser (optional helper)
		function getLocation() {
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(function(position) {
					document.getElementById('latitude').value = position.coords.latitude;
					document.getElementById('longitude').value = position.coords.longitude;
				});
			}
		}
		
		// Add a button to get current location
		const latField = document.getElementById('latitude');
		if (latField) {
			const getLocationBtn = document.createElement('button');
			getLocationBtn.type = 'button';
			getLocationBtn.className = 'btn btn-outline-warning btn-sm mt-2 w-100';
			getLocationBtn.innerHTML = '<i class="fa fa-map-marker-alt me-1"></i> Get Current Location <i class="fa fa-location-arrow ms-1"></i>';
			getLocationBtn.onclick = function() {
				getLocationBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Getting Location...';
				getLocationBtn.disabled = true;
				getLocation();
			};
			latField.parentNode.appendChild(getLocationBtn);
		}
		
		// Enhanced getLocation function
		function getLocation() {
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(
					function(position) {
						document.getElementById('latitude').value = position.coords.latitude.toFixed(6);
						document.getElementById('longitude').value = position.coords.longitude.toFixed(6);
						
						// Reset button
						const btn = document.querySelector('.btn-outline-warning');
						if (btn) {
							btn.innerHTML = '<i class="fa fa-check text-success me-1"></i> Location Retrieved!';
							btn.className = 'btn btn-outline-success btn-sm mt-2 w-100';
							setTimeout(() => {
								btn.innerHTML = '<i class="fa fa-map-marker-alt me-1"></i> Get Current Location <i class="fa fa-location-arrow ms-1"></i>';
								btn.className = 'btn btn-outline-warning btn-sm mt-2 w-100';
								btn.disabled = false;
							}, 3000);
						}
					},
					function(error) {
						// Reset button on error
						const btn = document.querySelector('.btn-outline-warning, .btn-outline-success');
						if (btn) {
							btn.innerHTML = '<i class="fa fa-exclamation-triangle text-danger me-1"></i> Location Failed';
							btn.className = 'btn btn-outline-danger btn-sm mt-2 w-100';
							setTimeout(() => {
								btn.innerHTML = '<i class="fa fa-map-marker-alt me-1"></i> Get Current Location <i class="fa fa-location-arrow ms-1"></i>';
								btn.className = 'btn btn-outline-warning btn-sm mt-2 w-100';
								btn.disabled = false;
							}, 3000);
						}
						console.error('Geolocation error:', error);
					}
				);
		}
	}
	
	// Business Hours functionality
	$(document).ready(function() {
		// Prepare business hours JSON before form submission
		$('form').submit(function() {
			const open = $('input[name="business_hours[open]"]').val();
			const close = $('input[name="business_hours[close]"]').val();
			
			const businessHours = {
				open: open,
				close: close
			};
			
			$('#business_hours_json').val(JSON.stringify(businessHours));
		});
	});
</script></body>
</html>
