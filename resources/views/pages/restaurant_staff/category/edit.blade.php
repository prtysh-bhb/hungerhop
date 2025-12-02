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
                        <form id="categoryForm" method="POST"
                            action="{{ isset($category) ? route('restaurant.categories.update', $category->id) : route('restaurant.categories.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            @if (isset($category))
                                @method('PUT')
                            @endif
                            <div class="mb-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', $category->name ?? '') }}" required minlength="2" maxlength="50"
                                    pattern="^[A-Za-z][A-Za-z\s&'-]*$"
                                    title="Must start with a letter and contain only letters, spaces, &, ' and -">
                                <small class="text-muted">2-50 characters, must start with a letter (letters, spaces, &, ',
                                    - allowed)</small>
                                <div class="invalid-feedback" id="name-error">
                                    Please enter a valid category name.
                                </div>
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
                                <input type="number" class="form-control" readonly id="menu_template_id"
                                    name="menu_template_id"
                                    value="{{ old('menu_template_id', isset($category) ? $category->menu_template_id : 1) }}">
                                @error('menu_template_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Image</label>
                                <input type="file" class="form-control" id="image" name="image">
                                @if (isset($category) && $category->image_url)
                                    <img src="{{ $category->image_url }}" alt="Category Image" class="img-thumbnail mt-2"
                                        width="120">
                                @endif
                                @error('image')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control" id="sort_order" name="sort_order"
                                    value="{{ old('sort_order', $category->sort_order ?? 0) }}">
                                <div class="invalid-feedback" id="sort_order-error">
                                    Please enter a valid number.
                                </div>
                                @error('sort_order')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="is_active" class="form-label">Active</label>
                                <select class="form-control" id="is_active" name="is_active">
                                    <option value="1"
                                        {{ old('is_active', $category->is_active ?? 1) == 1 ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="0"
                                        {{ old('is_active', $category->is_active ?? 1) == 0 ? 'selected' : '' }}>No
                                    </option>
                                </select>
                                @error('is_active')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">{{ isset($category) ? 'Update' : 'Add' }}
                                Category</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('categoryForm');
            const nameInput = document.getElementById('name');
            const sortOrderInput = document.getElementById('sort_order');

            // Validation for name field (no numbers allowed)
            nameInput.addEventListener('input', function() {
                validateNameField();
            });

            // Validation for sort_order field (only numbers allowed)
            sortOrderInput.addEventListener('input', function() {
                validateSortOrderField();
            });

            // Form submission validation
            form.addEventListener('submit', function(event) {
                if (!validateForm()) {
                    event.preventDefault();
                }
            });

            function validateNameField() {
                const nameValue = nameInput.value.trim();
                const nameRegex = /^[A-Za-z][A-Za-z\s&'-]*$/; // Must start with letter, only safe chars

                if (nameValue === '') {
                    setFieldError(nameInput, 'Category name is required.');
                    return false;
                } else if (nameValue.length < 2) {
                    setFieldError(nameInput, 'Category name must be at least 2 characters.');
                    return false;
                } else if (nameValue.length > 50) {
                    setFieldError(nameInput, 'Category name cannot exceed 50 characters.');
                    return false;
                } else if (!nameRegex.test(nameValue)) {
                    setFieldError(nameInput,
                        'Category name must start with a letter and can only contain letters, spaces, &, \' and -.'
                        );
                    return false;
                } else {
                    clearFieldError(nameInput);
                    return true;
                }
            }

            function validateSortOrderField() {
                const sortOrderValue = sortOrderInput.value.trim();
                const numberRegex = /^-?\d*$/; // Only numbers (including negative)

                if (sortOrderValue === '') {
                    clearFieldError(sortOrderInput);
                    return true; // Empty is allowed as it's not required
                } else if (!numberRegex.test(sortOrderValue)) {
                    setFieldError(sortOrderInput, 'Sort order must be a number.');
                    return false;
                } else {
                    clearFieldError(sortOrderInput);
                    return true;
                }
            }

            function validateForm() {
                const isNameValid = validateNameField();
                const isSortOrderValid = validateSortOrderField();

                return isNameValid && isSortOrderValid;
            }

            function setFieldError(field, message) {
                field.classList.add('is-invalid');
                const errorElement = document.getElementById(field.id + '-error');
                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.style.display = 'block';
                }
            }

            function clearFieldError(field) {
                field.classList.remove('is-invalid');
                const errorElement = document.getElementById(field.id + '-error');
                if (errorElement) {
                    errorElement.style.display = 'none';
                }
            }
        });
    </script>

    <style>
        .invalid-feedback {
            display: none;
        }

        .is-invalid {
            border-color: #dc3545;
        }
    </style>
@endsection
