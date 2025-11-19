@extends('layouts.admin')

@section('title', 'All Delivery Partners')

@section('content')
<div class="content-header">
    <div class="d-flex align-items-center">
        <div class="me-auto">
            <h4 class="page-title">Delivery Partners</h4>
            <div class="d-inline-block align-items-center">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('restaurant.dashboard') }}"><i class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item active">Delivery Partners</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="box">
               	<div class="box-body">
							<div class="table-responsive rounded card-table">
                            <table class="table border-no" id="example1">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Vehicle Type</th>
                                    <th>Vehicle number</th>
                                    <th>Availability</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($partners as $partner)
                                    <tr>
                                        <td>{{ $partner->id }}</td>
                                        <td>{{ optional($partner->user)->first_name }} {{ optional($partner->user)->last_name }}</td>
                                        <td>{{ optional($partner->user)->phone }}</td>
                                        <td>{{ optional($partner->user)->email }}</td>
                                        <td>{{$partner->vehicle_type}}</td>
                                        <td>{{$partner->vehicle_number}}</td>
                                        <td>{{$partner->is_available ? 'Available' : 'Not Available'}}</td>
                                        <td>{{ optional($partner->user)->created_at ? optional($partner->user)->created_at->format('d-m-Y') : '-' }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="actionMenu{{ $partner->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="actionMenu{{ $partner->id }}">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('partners.show', $partner->id) }}">View</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('partners.edit', $partner->id) }}">Edit</a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('partners.destroy', $partner->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">Delete</button>
                                                        </form>
                                                    </li>
                                                </ul>
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
@endsection
@section('scripts')
<script src="{{ asset('assets/vendor_components/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/pages/order.js') }}"></script>
@endsection