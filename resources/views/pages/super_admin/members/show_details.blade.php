@extends('layouts.admin')

@section('title', 'Member Details')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Restaurant Details</h3>
                    </div>
                    <div class="box-body">
                        @if($restaurants->isEmpty())
                            <p>No restaurant found for this user.</p>
                        @else
                           <table class="table border-no" id="example1">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Tenant</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($restaurants as $restaurant)
                                        <tr>
                                            <td>{{ $restaurant->id }}</td>
                                            <td>{{ $restaurant->restaurant_name ?? $restaurant->name }}</td>
                                            <td>
                                                @php
                                                    $tenant = $tenants->firstWhere('id', $restaurant->tenant_id);
                                                @endphp
                                                {{ $tenant ? $tenant->tenant_name : 'N/A' }}
                                            </td>
                                            <td>{{ $restaurant->address }}</td>
                                            <td>{{ $restaurant->status }}</td>
                                            <td>{{ $restaurant->created_at }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton{{ $restaurant->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        &#8942;
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $restaurant->id }}">
                                                        <a class="dropdown-item" href="{{ route('restaurant-admin.show', $restaurant->id) }}">View</a>
                                                        <a class="dropdown-item" href="{{ route('restaurant-admin.edit', $restaurant->id) }}">Edit</a>
                                                        <form action="{{ route('restaurant-admin.destroy', $restaurant->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to delete this restaurant?')">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <!-- Include Bootstrap 4 CSS and JS with jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
@endpush
