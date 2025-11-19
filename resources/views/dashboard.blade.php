<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - HungerHop</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor_components/bootstrap/dist/css/bootstrap.min.css') }}">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Dashboard</h4>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm">Logout</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <h5>Welcome, {{ Auth::user()->first_name ?? Auth::user()->name }}!</h5>
                        <p class="text-muted">Role: {{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</p>
                        <p class="text-muted">Status: {{ ucfirst(Auth::user()->status) }}</p>
                        <p class="text-muted">Email: {{ Auth::user()->email }}</p>
                        @if(Auth::user()->phone)
                            <p class="text-muted">Phone: {{ Auth::user()->phone }}</p>
                        @endif
                        
                        <hr>
                        
                        <div class="alert alert-info">
                            <h6>Authentication System Working! 🎉</h6>
                            <p>You have successfully logged in to HungerHop. This dashboard will be customized based on your role.</p>
                        </div>
                        
                        @if(Auth::user()->role === 'customer')
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Go to Customer Dashboard</a>
                        @elseif(Auth::user()->role === 'restaurant_staff')
                            <a href="{{ route('restaurant.dashboard') }}" class="btn btn-success">Go to Restaurant Dashboard</a>
                        @elseif(Auth::user()->role === 'delivery_partner')
                            <a href="{{ route('delivery.dashboard') }}" class="btn btn-warning">Go to Delivery Dashboard</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
