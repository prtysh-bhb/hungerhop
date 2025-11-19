<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - HungerHop</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor_components/bootstrap/dist/css/bootstrap.min.css') }}">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Customer Dashboard</h4>
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
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6>My Orders</h6>
                                        <p class="text-muted">View your order history</p>
                                        <button class="btn btn-primary btn-sm">View Orders</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6>Browse Restaurants</h6>
                                        <p class="text-muted">Find restaurants near you</p>
                                        <button class="btn btn-success btn-sm">Browse</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
