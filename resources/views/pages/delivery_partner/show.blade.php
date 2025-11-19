@extends('layouts.admin')

@section('title', 'Delivery Partner Details')

@section('content')
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Delivery Partner Details</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('restaurant.dashboard') }}"><i
                                        class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('partners.index') }}">Delivery Partners</a></li>
                            <li class="breadcrumb-item active">Details</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        @php
                            $profileImageUrl = $partner->profile_image_url
                                ? asset('storage/' . $partner->profile_image_url)
                                : asset('images/default-profile.png');
                        @endphp
                        <img src="{{ $profileImageUrl }}" class="rounded-circle mb-3" width="120" height="120"
                            alt="Profile Image" style="object-fit: cover;"
                            onerror="this.src='{{ asset('images/default-profile.png') }}'">
                        <!-- Debug: Image URL - {{ $profileImageUrl }} -->
                        <h4>{{ optional($partner->user)->first_name }} {{ optional($partner->user)->last_name }}</h4>
                        <p class="text-muted">{{ optional($partner->user)->email }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h6>Total Deliveries</h6>
                                <p class="display-6">
                                    {{ $partner->total_deliveries ?? ($partner->assignments ? $partner->assignments->count() : 0) }}
                                </p>
                            </div>
                            <div class="col-md-4">
                                <h6>Average Rating</h6>
                                <p class="display-6">
                                    {{ number_format($partner->average_rating ?? ($partner->reviews ? $partner->reviews->avg('rating') : 0), 2) }}
                                </p>
                            </div>
                            <div class="col-md-4">
                                <h6>Total Reviews</h6>
                                <p class="display-6">
                                    {{ $partner->total_reviews ?? ($partner->reviews ? $partner->reviews->count() : 0) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5>Uploaded Documents</h5>
                    </div>
                    <div class="card-body">
                        @if ($partner->documents && $partner->documents->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Document Type</th>
                                            <th>File Name</th>
                                            <th>Status</th>
                                            <th>Uploaded Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($partner->documents as $document)
                                            <tr>
                                                <td>{{ ucfirst(str_replace('_', ' ', $document->document_type)) }}</td>
                                                <td>{{ $document->document_name ?? 'N/A' }}</td>
                                                <td>
                                                    @if ($document->status == 'approved')
                                                        <span class="badge bg-success">Approved</span>
                                                    @elseif($document->status == 'pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                    @elseif($document->status == 'rejected')
                                                        <span class="badge bg-danger">Rejected</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $document->status }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $document->uploaded_at ? $document->uploaded_at->format('d M Y') : ($document->created_at ? $document->created_at->format('d M Y') : 'N/A') }}
                                                </td>
                                                <td>
                                                    @if ($document->document_path)
                                                        <a href="{{ asset('storage/' . $document->document_path) }}"
                                                            target="_blank" class="btn btn-sm btn-primary">
                                                            <i class="fa fa-eye"></i> View
                                                        </a>
                                                        <a href="{{ asset('storage/' . $document->document_path) }}"
                                                            download class="btn btn-sm btn-success">
                                                            <i class="fa fa-download"></i> Download
                                                        </a>
                                                    @else
                                                        <span class="text-muted">No file</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> No documents uploaded yet.
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5>Profile Details</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">Name</dt>
                            <dd class="col-sm-8">{{ optional($partner->user)->first_name }}
                                {{ optional($partner->user)->last_name }}</dd>
                            <dt class="col-sm-4">Email</dt>
                            <dd class="col-sm-8">{{ optional($partner->user)->email }}</dd>
                            <dt class="col-sm-4">Phone</dt>
                            <dd class="col-sm-8">{{ optional($partner->user)->phone }}</dd>
                            <dt class="col-sm-4">Vehicle Type</dt>
                            <dd class="col-sm-8">{{ $partner->vehicle_type ?? '-' }}</dd>
                            <dt class="col-sm-4">Vehicle Number</dt>
                            <dd class="col-sm-8">{{ $partner->vehicle_number ?? '-' }}</dd>
                            <dt class="col-sm-4">Status</dt>
                            <dd class="col-sm-8">{{ $partner->is_available ? 'Available' : 'Unavailable' }}</dd>
                            <dt class="col-sm-4">Joined</dt>
                            <dd class="col-sm-8">
                                {{ optional($partner->user)->created_at ? optional($partner->user)->created_at->format('Y-m-d') : '-' }}
                            </dd>
                            <dt class="col-sm-4">Current Location</dt>
                            <dd class="col-sm-8">
                                @if ($partner->current_latitude && $partner->current_longitude)
                                    <a href="https://maps.google.com/?q={{ $partner->current_latitude }},{{ $partner->current_longitude }}"
                                        target="_blank">
                                        {{ $partner->current_latitude }}, {{ $partner->current_longitude }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </dd>
                        </dl>
                        <a href="{{ route('partners.edit', $partner->id) }}" class="btn btn-primary">Edit</a>
                        <a href="{{ route('partners.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
