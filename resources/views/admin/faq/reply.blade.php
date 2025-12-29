@extends('layouts.admin')

@section('title', 'Reply to FAQ')

@section('content')
<div class="container-fluid">

    <div class="row mb-3">
        <div class="col-12">
            <h4 class="page-title">Reply to FAQ</h4>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            {{-- Question --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Question</label>
                <div class="form-control bg-light">
                    {{ $faq->question }}
                </div>
            </div>

            {{-- Meta Info --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Target Role</label>
                    <input type="text"
                           class="form-control"
                           value="{{ ucfirst(str_replace('_', ' ', $faq->target_role)) }}"
                           readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Category</label>
                    <input type="text"
                           class="form-control"
                           value="{{ $faq->category ?? 'General' }}"
                           readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Status</label>
                    <input type="text"
                           class="form-control"
                           value="{{ $faq->is_active ? 'Active' : 'Inactive' }}"
                           readonly>
                </div>
            </div>

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Reply Form --}}
            <form action="{{ route('admin.faq.submitReply', $faq->id) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="answer" class="form-label fw-bold">Answer</label>
                    <textarea
                        name="answer"
                        id="answer"
                        class="form-control"
                        rows="6"
                        required
                    >{{ old('answer', $faq->answer) }}</textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.faq.index') }}" class="btn btn-secondary">
                        ← Back
                    </a>

                    <button type="submit" class="btn btn-success">
                        Save Answer
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
