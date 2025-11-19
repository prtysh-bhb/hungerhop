<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="HungerHop - Restaurant Login">
    <meta name="author" content="HungerHop">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <title>HungerHop - Restaurant Login</title>
  
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

</head>
	
<body class="hold-transition theme-primary bg-img" style="background-image: url({{ asset('images/auth-bg/bg-1.jpg') }})">
	
	<div class="container h-p100">
		<div class="row align-items-center justify-content-md-center h-p100">	
			
			<div class="col-12">
				<div class="row justify-content-center g-0">
					<div class="col-lg-5 col-md-5 col-12">
						<div class="bg-white rounded10 shadow-lg">
							<div class="content-top-agile p-20 pb-0">
								<h2 class="text-warning">
									<i class="fa fa-utensils"></i> Restaurant Portal
								</h2>
								<p class="mb-0">Sign in to manage your restaurant.</p>							
							</div>
							<div class="p-40">
								<form action="{{ route('login.submit') }}" method="post">
									@csrf
									<!-- Hidden field to ensure remember is always sent -->
									<input type="hidden" name="remember" value="0">
									<div class="form-group">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text bg-transparent"><i class="ti-user"></i></span>
                                            <input type="text" name="username" class="form-control ps-15 bg-transparent" placeholder="Restaurant Email/Phone" required value="{{ old('username') }}">
                                        </div>
                                        @error('username')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
									</div>
									<div class="form-group">
										<div class="input-group mb-3">
											<span class="input-group-text bg-transparent"><i class="ti-lock"></i></span>
											<input type="password" name="password" class="form-control ps-15 bg-transparent" placeholder="Password" required>
										</div>
										@error('password')
											<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
									  <div class="row">
										<div class="col-6">
										  <div class="checkbox">
											<input type="checkbox" id="restaurant_remember_checkbox" name="remember" value="1">
											<label for="restaurant_remember_checkbox">Remember Me</label>
										  </div>
										</div>
										<!-- /.col -->
										<div class="col-6">
										 <div class="fog-pwd text-end">
											{{-- <a href="{{ route('password.request') }}" class="hover-warning"><i class="ion ion-locked"></i> Forgot pwd?</a><br> --}}
										  </div>
										</div>
										<!-- /.col -->
										<div class="col-12 text-center">
										  <button type="submit" class="btn btn-warning mt-10">
											<i class="fa fa-utensils"></i> RESTAURANT SIGN IN
										  </button>
										</div>
										<!-- /.col -->
										<div class="col-12 text-center mt-15">
										  <a href="{{ route('login') }}" class="btn btn-outline-secondary">
											<i class="fa fa-arrow-left"></i> Back to Customer Login
										  </a>
										</div>
										<!-- /.col -->
									  </div>
								</form>	
								<div class="text-center">
									<p class="mt-15 mb-0">Need to register your restaurant? <a href="{{ route('restaurant.registration') }}" class="text-warning ms-5">Register Here</a></p>
								</div>	
							</div>						
						</div>
						<div class="text-center">
						  <p class="mt-20 text-white">- Restaurant Features -</p>
						  <div class="row text-white">
							<div class="col-4">
								<i class="fa fa-chart-line fa-2x mb-2"></i>
								<p><small>Analytics</small></p>
							</div>
							<div class="col-4">
								<i class="fa fa-utensils fa-2x mb-2"></i>
								<p><small>Menu Management</small></p>
							</div>
							<div class="col-4">
								<i class="fa fa-shopping-bag fa-2x mb-2"></i>
								<p><small>Order Management</small></p>
							</div>
						  </div>	
						</div>
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
	
	<!-- Chat Popup -->
	<script src="{{ asset('js/pages/chat-popup.js') }}"></script>
	
	<!-- ApexCharts -->
	<script src="{{ asset('assets/vendor_components/apexcharts-bundle/dist/apexcharts.min.js') }}"></script>
	
	<!-- Custom JS -->
	<script src="{{ asset('js/template.js') }}"></script>
	
	<script>
		// Form validation
		document.querySelector('form').addEventListener('submit', function(e) {
			const username = document.querySelector('input[name="username"]').value;
			const password = document.querySelector('input[name="password"]').value;
			
			if (!username || !password) {
				e.preventDefault();
				alert('Please fill in all fields');
				return;
			}
		});
	</script>	

</body>
</html>
