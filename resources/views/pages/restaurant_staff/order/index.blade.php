<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}">

    <title>Riday - Restaurant Bootstrap Admin Template Webapp</title>
  
	<!-- Vendors Style-->
	<link rel="stylesheet" href="{{ asset('css/vendors_css.css') }}">
	  
	<!-- Style-->  
	<link rel="stylesheet" href="{{ asset('css/style.css') }}">
	<link rel="stylesheet" href="{{ asset('css/skin_color.css') }}">	

</head>
<body class="hold-transition light-skin sidebar-mini theme-primary fixed">
	
<div class="wrapper">
	<div id="loader"></div>

  @include('partials.header')
  
  @include('partials.sidebar')
    
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
	  <div class="container-full">
		<!-- Content Header (Page header) -->	  
		<div class="content-header">
			<div class="d-flex align-items-center">
				<div class="me-auto">
					<h4 class="page-title">Order List</h4>
					<div class="d-inline-block align-items-center">
						<nav>
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
								<li class="breadcrumb-item" aria-current="page">Order</li>
								<li class="breadcrumb-item active" aria-current="page">Order List</li>
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
							<div class="table-responsive rounded card-table">
								<table class="table border-no" id="example1">
									<thead>
										<tr>
											<th>Order ID</th>
											<th>Date</th>
											<th>Customer Name</th>
											<th>Location</th>
											<th>Amount</th>
											<th>Status</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										@foreach($orders as $order)
										<tr class="hover-primary"  data-id="{{ $order->id }}">
											<td>#{{ $order->id }}</td>
											<td>
												{{ $order->created_at->format('d F Y,') }}
												<span class="fs-12"> {{ $order->created_at->format('h:i A') }}</span>
											</td>
											<td>
												@if($order->customer && $order->customer->user)

														{{ $order->customer->user->full_name }}
												@else
													-
												@endif
											</td>
											<td>
												@if($order->deliveryAddress)
													{{ $order->deliveryAddress->address_line1 }},
													{{ $order->deliveryAddress->city }},
													{{ $order->deliveryAddress->state }},
													{{ $order->deliveryAddress->postal_code }}
												@else
													-
												@endif
											</td>
											<td>${{ number_format($order->total_amount, 2) }}</td>
											<td>
												<span class="badge badge-pill 
													@if($order->status == 'Delivered') badge-success-light
													@elseif($order->status == 'Pending') badge-warning-light
													@elseif($order->status == 'Rejected') badge-danger-light
													@elseif($order->status == 'accepted') badge-info-light
													@elseif($order->status == 'cancelled') badge-warning-light
													@else badge-secondary-light
													@endif
												">
													{{ $order->status }}
												</span>
											</td>
											<td>
												<div class="btn-group">
													<a class="hover-primary dropdown-toggle no-caret" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></a>
													<div class="dropdown-menu">
														<a class="dropdown-item" href="{{ route('restaurant.order.details', ['id' => $order->id]) }}">View Details</a>
													</div>
												</div>
											</td>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- /.content -->
  </div>
  </div>
  <!-- /.content-wrapper -->
  
  @include('partials.right-sidebar')
  
  @include('partials.footer')
  
  @include('partials.control-sidebar')
  
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

	<!-- ./side demo panel -->
	<div class="sticky-toolbar">
		<a href="https://themeforest.net/item/riday-restaurant-bootstrap-admin-template-webapp/31603200" data-bs-toggle="tooltip" data-bs-placement="left" title="Buy Now" class="waves-effect waves-light btn btn-success btn-flat mb-5 btn-sm" target="_blank">
			<span class="icon-Money"><span class="path1"></span><span class="path2"></span></span>
		</a>
		<a href="https://themeforest.net/user/multipurposethemes/portfolio" data-bs-toggle="tooltip" data-bs-placement="left" title="Portfolio" class="waves-effect waves-light btn btn-danger btn-flat mb-5 btn-sm" target="_blank">
			<span class="icon-Image"></span>
		</a>
		<a id="chat-popup" href="#" data-bs-toggle="tooltip" data-bs-placement="left" title="Live Chat" class="waves-effect waves-light btn btn-warning btn-flat btn-sm">
			<span class="icon-Group-chat"><span class="path1"></span><span class="path2"></span></span>
		</a>
	</div>
	
	<!-- Sidebar -->
	<div id="chat-box-body">
		<div id="chat-circle" class="waves-effect waves-circle btn btn-circle btn-sm btn-warning l-h-45">
			<div id="chat-overlay"></div>
			<span class="icon-Group-chat fs-18"><span class="path1"></span><span class="path2"></span></span>
		</div>
		
		<div class="chat-box">
			<div class="chat-box-header p-15 d-flex justify-content-between align-items-center">
				<div class="btn-group">
				  <button class="waves-effect waves-circle btn btn-circle btn-primary-light h-40 w-40 rounded-circle l-h-45" type="button" data-bs-toggle="dropdown">
					  <span class="icon-Add-user fs-22"><span class="path1"></span><span class="path2"></span></span>
				  </button>
				  <div class="dropdown-menu min-w-200">
					<a class="dropdown-item fs-16" href="#">
						<span class="icon-Color me-15"></span>
						New Group</a>
					<a class="dropdown-item fs-16" href="#">
						<span class="icon-Clipboard me-15"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></span>
						Contacts</a>
					<a class="dropdown-item fs-16" href="#">
						<span class="icon-Group me-15"><span class="path1"></span><span class="path2"></span></span>
						Groups</a>
					<a class="dropdown-item fs-16" href="#">
						<span class="icon-Active-call me-15"><span class="path1"></span><span class="path2"></span></span>
						Calls</a>
					<a class="dropdown-item fs-16" href="#">
						<span class="icon-Settings1 me-15"><span class="path1"></span><span class="path2"></span></span>
						Settings</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item fs-16" href="#">
						<span class="icon-Question-circle me-15"><span class="path1"></span><span class="path2"></span></span>
						Help</a>
					<a class="dropdown-item fs-16" href="#">
						<span class="icon-Notifications me-15"><span class="path1"></span><span class="path2"></span></span>
						Privacy</a>
				  </div>
				</div>
				<div class="text-center flex-grow-1">
					<div class="text-dark fs-18">Mayra Sibley</div>
					<div>
						<span class="badge badge-sm badge-dot badge-primary"></span>
						<span class="text-muted fs-12">Active</span>
					</div>
				</div>
				<div class="chat-box-toggle">
					<button id="chat-box-toggle" class="waves-effect waves-circle btn btn-circle btn-danger-light h-40 w-40 rounded-circle l-h-45" type="button">
					  <span class="icon-Close fs-22"><span class="path1"></span><span class="path2"></span></span>
					</button>
				</div>
			</div>
			<div class="chat-box-body">
				<div class="chat-box-overlay">
				</div>
				<div class="chat-logs">
					<div class="chat-msg user">
						<div class="d-flex align-items-center">
							<span class="msg-avatar">
								<img src="{{ asset('images/avatar/2.jpg') }}" class="avatar avatar-lg">
							</span>
							<div class="mx-10">
								<a href="#" class="text-dark hover-primary fw-bold">Mayra Sibley</a>
								<p class="text-muted fs-12 mb-0">2 Hours</p>
							</div>
						</div>
						<div class="cm-msg-text">
							Hi there, I'm Jesse and you?
						</div>
					</div>
					<div class="chat-msg self">
						<div class="d-flex align-items-center justify-content-end">
							<div class="mx-10">
								<a href="#" class="text-dark hover-primary fw-bold">You</a>
								<p class="text-muted fs-12 mb-0">3 minutes</p>
							</div>
							<span class="msg-avatar">
								<img src="{{ asset('images/avatar/3.jpg') }}" class="avatar avatar-lg">
							</span>
						</div>
						<div class="cm-msg-text">
						   My name is Anne Clarc.
						</div>
					</div>
					<div class="chat-msg user">
						<div class="d-flex align-items-center">
							<span class="msg-avatar">
								<img src="{{ asset('images/avatar/2.jpg') }}" class="avatar avatar-lg">
							</span>
							<div class="mx-10">
								<a href="#" class="text-dark hover-primary fw-bold">Mayra Sibley</a>
								<p class="text-muted fs-12 mb-0">40 seconds</p>
							</div>
						</div>
						<div class="cm-msg-text">
							Nice to meet you Anne.<br>How can i help you?
						</div>
					</div>
				</div><!--chat-log -->
			</div>
			<div class="chat-input">
				<form>
					<input type="text" id="chat-input" placeholder="Send a message..."/>
					<button type="submit" class="chat-submit" id="chat-submit">
						<span class="icon-Send fs-22"></span>
					</button>
				</form>
			</div>
		</div>
	</div>
	
	<!-- Page Content overlay -->
	
	<!-- Vendor JS -->
	<script src="{{ asset('js/vendors.min.js') }}"></script>
	<script src="{{ asset('js/pages/chat-popup.js') }}"></script>
	<script src="{{ asset('assets/vendor_components/apexcharts-bundle/dist/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/icons/feather-icons/feather.min.js') }}"></script>
	<script src="{{ asset('assets/vendor_components/datatable/datatables.min.js') }}"></script>
	
	<!-- Riday Admin App -->
	<script src="{{ asset('js/template.js') }}"></script>
	<script src="{{ asset('js/pages/order.js') }}"></script>

</body>
</html>
