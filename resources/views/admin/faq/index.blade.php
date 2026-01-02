@extends('layouts.admin')

@section('title', 'FAQs')

@section('content')
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">FAQs</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    <i class="mdi mdi-home-outline"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item active">FAQs</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div>
                <a href="{{ route('admin.faq.create') }}" class="btn btn-primary">
                    <i class="mdi mdi-plus"></i> Add FAQ
                </a>
            </div>

            <div class="modal fade" id="deleteFaqModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title text-danger">Delete FAQ</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <p class="mb-0">
                                Are you sure you want to delete this FAQ?
                                <br>
                                <small class="text-muted">
                                    This action cannot be undone.
                                </small>
                            </p>
                        </div>

                        <div class="modal-footer d-flex">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Cancel
                            </button>

                            <form method="POST" id="deleteFaqForm">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    Yes, Delete
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-body">

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Question</th>
                                        <th>Category</th>
                                        <th>Target Role</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($faqs as $faq)
                                        <tr>
                                            <td>{{ $faq->id }}</td>

                                            <td>
                                                <strong>{{ $faq->question }}</strong>
                                                <div class="text-muted small">
                                                    {{ \Illuminate\Support\Str::limit(strip_tags($faq->answer), 80) }}
                                                </div>
                                            </td>

                                            <td>
                                                @if ($faq->category)
                                                    <span class="badge bg-info">
                                                        {{ ucfirst($faq->category) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ ucfirst(str_replace('_', ' ', $faq->target_role)) }}
                                                </span>
                                            </td>

                                            <td>
                                                @if ($faq->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>

                                            <td class="text-start">

                                                {{-- View --}}
                                                <button type="button" class="btn btn-sm btn-outline-info"
                                                    data-bs-toggle="modal" data-bs-target="#faqModal{{ $faq->id }}">
                                                    <i class="mdi mdi-eye"></i>
                                                </button>

                                                {{-- Edit --}}
                                                <a href="{{ route('admin.faq.edit', $faq->id) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>

                                                {{-- Delete --}}
                                                <form action="#" method="POST" class="d-inline"
                                                    onclick="event.preventDefault();
                                                    document.getElementById('deleteFaqForm').action = '{{ route('admin.faq.destroy', $faq->id) }}';
                                                    var deleteFaqModal = new bootstrap.Modal(document.getElementById('deleteFaqModal'));
                                                    deleteFaqModal.show();">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </form>

                                                {{-- Modal --}}
                                                <div class="modal fade" id="faqModal{{ $faq->id }}" tabindex="-1">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">FAQ Details</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>

                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="fw-bold">Question</label>
                                                                    <div class="form-control bg-light">
                                                                        {{ $faq->question }}
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="fw-bold">Answer</label>
                                                                    <div class="form-control bg-light">
                                                                        {!! nl2br(e($faq->answer)) !!}
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <label class="fw-bold">Target Role</label>
                                                                        <input class="form-control"
                                                                            value="{{ ucfirst(str_replace('_', ' ', $faq->target_role)) }}"
                                                                            readonly>
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <label class="fw-bold">Category</label>
                                                                        <input class="form-control"
                                                                            value="{{ $faq->category ?? 'General' }}"
                                                                            readonly>
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <label class="fw-bold">Status</label>
                                                                        <input class="form-control"
                                                                            value="{{ $faq->is_active ? 'Active' : 'Inactive' }}"
                                                                            readonly>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                                No FAQs found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
