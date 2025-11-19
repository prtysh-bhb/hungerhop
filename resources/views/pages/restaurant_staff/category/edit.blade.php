@extends('layouts.admin')

@section('title', isset($category) ? 'Edit Category' : 'Add Category')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>{{ isset($category) ? 'Edit Category' : 'Add Category' }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ isset($category) ? route('restaurant.categories.update', $category->id) : route('restaurant.categories.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if(isset($category))
                            @method('PUT')
                        @endif
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $category->name ?? '') }}" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $category->description ?? '') }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="menu_template_id" class="form-label">Menu Template</label>
                            <input type="number" class="form-control" id="menu_template_id" name="menu_template_id" value="{{ old('menu_template_id', $category->menu_template_id ?? '') }}">
                            @error('menu_template_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="image" name="image">
                            @if(isset($category) && $category->image_url)
                                <img src="{{ $category->image_url }}" alt="Category Image" class="img-thumbnail mt-2" width="120">
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
                            @error('sort_order')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="is_active" class="form-label">Active</label>
                            <select class="form-control" id="is_active" name="is_active">
                                <option value="1" {{ old('is_active', $category->is_active ?? 1) == 1 ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ old('is_active', $category->is_active ?? 1) == 0 ? 'selected' : '' }}>No</option>
                            </select>
                            @error('is_active')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">{{ isset($category) ? 'Update' : 'Add' }} Category</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
