@extends('layouts.admin')

@section('title', 'Edit FAQ')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="page-title">Edit FAQ</h4>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('admin.faq.update', $faq->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="question" class="form-label fw-bold">Question</label>
                    <input type="text" name="question" id="question" class="form-control" value="{{ old('question', $faq->question) }}" required maxlength="500">
                </div>
                <div class="mb-3">
                    <label for="answer" class="form-label fw-bold">Answer</label>
                    <textarea name="answer" id="answer" class="form-control" rows="5">{{ old('answer', $faq->answer) }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="category" class="form-label fw-bold">Category</label>
                    <input type="text" name="category" id="category" class="form-control" value="{{ old('category', $faq->category) }}">
                </div>
                <div class="mb-3">
                    <label for="target_role" class="form-label fw-bold">Target Role</label>
                    <select name="target_role" id="target_role" class="form-control">
                        <option value="all" {{ old('target_role', $faq->target_role) == 'all' ? 'selected' : '' }}>All</option>
                        <option value="customer" {{ old('target_role', $faq->target_role) == 'customer' ? 'selected' : '' }}>Customer</option>
                        <option value="restaurant" {{ old('target_role', $faq->target_role) == 'restaurant' ? 'selected' : '' }}>Restaurant</option>
                        <option value="delivery_partner" {{ old('target_role', $faq->target_role) == 'delivery_partner' ? 'selected' : '' }}>Delivery Partner</option>
                        <option value="admin" {{ old('target_role', $faq->target_role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="order" class="form-label fw-bold">Sort Order</label>
                    <input type="number" name="order" id="order" class="form-control" value="{{ old('order', $faq->order) }}" min="0">
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $faq->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.faq.index') }}" class="btn btn-secondary">← Back</a>
                    <button type="submit" class="btn btn-success">Update FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
