{{-- Extend the main layout --}}
@extends('layouts.admin')

{{-- Define the title for this page --}}
@section('title', isset($menuItem) ? 'Edit Menu Item' : 'Add New Menu Item')

{{-- Define the main content for this page --}}
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">{{ isset($menuItem) ? 'Edit Menu Item' : 'Add New Menu Item' }}</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('restaurant.menu.list') }}">Menu</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ isset($menuItem) ? 'Edit' : 'Add New' }}</li>
                        </ol>
                    </nav>
                </div>
            </div>

        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form
                            action="{{ isset($menuItem) ? route('restaurant.menu.update', $menuItem) : route('restaurant.menu.store') }}"
                            method="POST" enctype="multipart/form-data" id="menuItemForm" novalidate>
                            @csrf
                            @if (isset($menuItem))
                                @method('PUT')
                            @endif

                            <div class="form-body">
                                <!-- Basic Required Fields -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="fw-700 fs-16 form-label">Menu Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="item_name" id="item_name"
                                                class="form-control @error('item_name') is-invalid @enderror"
                                                placeholder="Enter menu item name"
                                                value="{{ old('item_name', $menuItem->item_name ?? '') }}" required
                                                maxlength="50" pattern="^[a-zA-Z0-9\s&'./\-]+$"
                                                title="Letters, numbers, spaces and special characters (&, ', ., /, -) allowed">
                                            <small class="text-muted">Max 50 characters (letters, numbers, &, ', ., /, -
                                                allowed)</small>
                                            @error('item_name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <div class="invalid-feedback" id="item_name_error"></div>
                                        </div>
                                    </div>
                                    @if ($user->role === 'tenant_admin')
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="fw-700 fs-16 form-label">Restaurant <span
                                                        class="text-danger">*</span></label>
                                                @if ($restaurants->count() > 0)
                                                    <select name="restaurant_id" id="restaurant_id"
                                                        class="form-select @error('restaurant_id') is-invalid @enderror"
                                                        required>
                                                        <option value="">Select Restaurant</option>
                                                        @foreach ($restaurants as $restaurant)
                                                            <option value="{{ $restaurant->id }}"
                                                                {{ old('restaurant_id', $menuItem->restaurant_id ?? '') == $restaurant->id ? 'selected' : '' }}>
                                                                {{ $restaurant->restaurant_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('restaurant_id')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                    <div class="invalid-feedback" id="restaurant_id_error"></div>
                                                @else
                                                    <div class="alert alert-warning mb-0">
                                                        <i class="fa fa-exclamation-triangle me-2"></i>
                                                        No restaurants found. Please create a restaurant first before adding menu items.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="fw-700 fs-16 form-label">Category <span
                                                        class="text-danger">*</span></label>
                                                <select name="menu_category_id" id="menu_category_id"
                                                    class="form-select @error('menu_category_id') is-invalid @enderror"
                                                    required>
                                                    <option value="">Select Category</option>
                                                    @if (isset($categories))
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}"
                                                                {{ old('menu_category_id', $menuItem->menu_category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                                {{ $category->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('menu_category_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <div class="invalid-feedback" id="menu_category_id_error"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if ($user->role !== 'tenant_admin')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="fw-700 fs-16 form-label">Category <span
                                                        class="text-danger">*</span></label>
                                                <select name="menu_category_id" id="menu_category_id"
                                                    class="form-select @error('menu_category_id') is-invalid @enderror"
                                                    required>
                                                    <option value="">Select Category</option>
                                                    @if (isset($categories))
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}"
                                                                {{ old('menu_category_id', $menuItem->menu_category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                                {{ $category->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('menu_category_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <div class="invalid-feedback" id="menu_category_id_error"></div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="fw-700 fs-16 form-label">Category <span
                                                        class="text-danger">*</span></label>
                                                <select name="menu_category_id" id="menu_category_id"
                                                    class="form-select @error('menu_category_id') is-invalid @enderror"
                                                    required>
                                                    <option value="">Select Category</option>
                                                    @if (isset($categories))
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}"
                                                                {{ old('menu_category_id', $menuItem->menu_category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                                {{ $category->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('menu_category_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <div class="invalid-feedback" id="menu_category_id_error"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="fw-700 fs-16 form-label">Base Price <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" name="base_price" id="base_price" step="0.01"
                                                min="1"
                                                class="form-control @error('base_price') is-invalid @enderror"
                                                placeholder="0.00"
                                                value="{{ old('base_price', $menuItem->base_price ?? '') }}" required>
                                            @error('base_price')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <div class="invalid-feedback" id="base_price_error"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="fw-700 fs-16 form-label">Preparation Time (minutes)</label>
                                            <input type="number" name="preparation_time" id="preparation_time"
                                                min="1" max="240"
                                                class="form-control @error('preparation_time') is-invalid @enderror"
                                                placeholder="15"
                                                value="{{ old('preparation_time', $menuItem->preparation_time ?? 15) }}">
                                            @error('preparation_time')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <div class="invalid-feedback" id="preparation_time_error"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="fw-700 fs-16 form-label">Available</label>
                                            <select name="is_available" class="form-control">
                                                <option value="1"
                                                    {{ old('is_available', $menuItem->is_available ?? 1) == 1 ? 'selected' : '' }}>
                                                    Yes</option>
                                                <option value="0"
                                                    {{ old('is_available', $menuItem->is_available ?? 1) == 0 ? 'selected' : '' }}>
                                                    No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="fw-700 fs-16 form-label">Description</label>
                                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                                rows="3" maxlength="255" placeholder="Enter menu item description (max 255 characters)">{{ old('description', $menuItem->description ?? '') }}</textarea>
                                            <small class="text-muted"><span id="description_count">0</span>/255
                                                characters</small>
                                            @error('description')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <div class="invalid-feedback" id="description_error"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="fw-700 fs-16 form-label">Ingredients</label>
                                            <textarea name="ingredients" id="ingredients" class="form-control @error('ingredients') is-invalid @enderror"
                                                rows="3" maxlength="500" placeholder="List main ingredients (comma separated, max 500 characters)">{{ old('ingredients', $menuItem->ingredients ?? '') }}</textarea>
                                            <small class="text-muted"><span id="ingredients_count">0</span>/500
                                                characters</small>
                                            @error('ingredients')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <div class="invalid-feedback" id="ingredients_error"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="fw-700 fs-16 form-label">Allergens</label>
                                            <textarea name="allergens" id="allergens" class="form-control @error('allergens') is-invalid @enderror"
                                                rows="3" maxlength="255" placeholder="List allergens (comma separated, max 255 characters)">{{ old('allergens', $menuItem->allergens ?? '') }}</textarea>
                                            <small class="text-muted"><span id="allergens_count">0</span>/255
                                                characters</small>
                                            @error('allergens')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <div class="invalid-feedback" id="allergens_error"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dietary Options -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 class="mt-20">Dietary Options</h5>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="checkbox checkbox-info">
                                                        <input type="checkbox" name="is_vegetarian" id="is_vegetarian"
                                                            value="1"
                                                            {{ old('is_vegetarian', $menuItem->is_vegetarian ?? false) ? 'checked' : '' }}>
                                                        <label for="is_vegetarian">Vegetarian</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="checkbox checkbox-info">
                                                        <input type="checkbox" name="is_vegan" id="is_vegan"
                                                            value="1"
                                                            {{ old('is_vegan', $menuItem->is_vegan ?? false) ? 'checked' : '' }}>
                                                        <label for="is_vegan">Vegan</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="checkbox checkbox-info">
                                                        <input type="checkbox" name="is_gluten_free" id="is_gluten_free"
                                                            value="1"
                                                            {{ old('is_gluten_free', $menuItem->is_gluten_free ?? false) ? 'checked' : '' }}>
                                                        <label for="is_gluten_free">Gluten Free</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="checkbox checkbox-warning">
                                                        <input type="checkbox" name="is_popular" id="is_popular"
                                                            value="1"
                                                            {{ old('is_popular', $menuItem->is_popular ?? false) ? 'checked' : '' }}>
                                                        <label for="is_popular">Popular Item</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Inventory Management -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 class="mt-20">Inventory Management</h5>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="fw-700 fs-16 form-label">Sort Order</label>
                                            <input type="number" name="sort_order" id="sort_order" min="0"
                                                max="9999"
                                                class="form-control @error('sort_order') is-invalid @enderror"
                                                placeholder="0"
                                                value="{{ old('sort_order', $menuItem->sort_order ?? 0) }}">
                                            <small class="text-muted">Value between 0-9999</small>
                                            @error('sort_order')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <div class="invalid-feedback" id="sort_order_error"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Fields -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 class="mt-20">Additional Information</h5>
                                    </div>
                                </div>

                                <!-- Menu Item Image -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="box-title mt-20">Menu Item Image</h4>
                                        <div class="product-img text-start">
                                            @if (isset($menuItem) && $menuItem->image_url)
                                                <img src="{{ $menuItem->image_url }}" alt="{{ $menuItem->item_name }}"
                                                    class="mb-15" style="max-width: 200px;" id="image_preview">
                                                <p>Current Image</p>
                                            @else
                                                <img src="{{ asset('images/product/product-9.png') }}" alt=""
                                                    class="mb-15" id="image_preview" style="max-width: 200px;">
                                                <p>Upload Menu Item Image</p>
                                            @endif
                                            <div class="btn btn-info mb-20">
                                                <input type="file" name="image" id="image_input"
                                                    class="upload @error('image') is-invalid @enderror"
                                                    accept=".jpeg,.jpg,.png,.gif,.webp" data-max-size="2097152">
                                            </div>
                                            <div class="mt-2">
                                                <small class="text-muted">Accepted formats: JPEG, PNG, JPG, GIF, WebP. Max
                                                    size: 2MB</small>
                                            </div>
                                            <div id="image_error" class="text-danger mt-2" style="display: none;"></div>
                                            @error('image')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions mt-10">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fa fa-check"></i> {{ isset($menuItem) ? 'Update' : 'Save' }} Menu Item
                                </button>
                                <a href="{{ route('restaurant.menu.list') }}" class="btn btn-danger">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Character counters for textareas and inputs
            const charCountFields = [{
                    id: 'description',
                    max: 255
                },
                {
                    id: 'ingredients',
                    max: 500
                },
                {
                    id: 'allergens',
                    max: 255
                }
            ];

            charCountFields.forEach(field => {
                const element = document.getElementById(field.id);
                const counter = document.getElementById(field.id + '_count');
                if (element && counter) {
                    // Set initial count
                    counter.textContent = element.value.length;

                    // Update on input
                    element.addEventListener('input', function() {
                        counter.textContent = this.value.length;
                        if (this.value.length >= field.max) {
                            counter.classList.add('text-danger');
                        } else {
                            counter.classList.remove('text-danger');
                        }
                    });
                }
            });

            // Function to display server-side validation errors
            function displayServerSideErrors() {
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        // This will be handled by the Laravel error display above each field
                        console.log('Server error: {{ $error }}');
                    @endforeach

                    // Highlight fields with errors
                    @foreach ($errors->keys() as $key)
                        const field = document.querySelector('[name="{{ $key }}"]');
                        const errorElement = document.getElementById('{{ $key }}_error');
                        if (field && errorElement) {
                            field.classList.add('is-invalid');
                            // Get the error message for this specific field
                            const errorMessage = '{{ $errors->first($key) }}';
                            errorElement.textContent = errorMessage;
                            errorElement.style.display = 'block';
                        }
                    @endforeach
                @endif
            }

            // Call this function on page load to show server-side errors
            displayServerSideErrors();

            // Menu Name validation - minimum 2 alphabets, allows numbers and special chars
            const itemNameInput = document.getElementById('item_name');
            if (itemNameInput) {
                itemNameInput.addEventListener('input', function(e) {
                    const errorDiv = document.getElementById('item_name_error');
                    const value = this.value.trim();
                    const validPattern = /^[a-zA-Z0-9\s&'./\-]+$/;

                    // Check if empty
                    if (value.length === 0) {
                        this.classList.add('is-invalid');
                        errorDiv.textContent = 'Menu name is required.';
                        errorDiv.style.display = 'block';
                    }
                    // Check for invalid characters
                    else if (!validPattern.test(value)) {
                        this.classList.add('is-invalid');
                        errorDiv.textContent =
                            'Menu name can only contain letters, numbers, spaces and special characters (&, \', ., /, -).';
                        errorDiv.style.display = 'block';
                    }
                    // Check minimum length (at least 2 alphabets)
                    else if (value.replace(/[^a-zA-Z]/g, '').length < 2) {
                        this.classList.add('is-invalid');
                        errorDiv.textContent = 'Menu name must contain at least 2 alphabets.';
                        errorDiv.style.display = 'block';
                    } else {
                        this.classList.remove('is-invalid');
                        errorDiv.style.display = 'none';
                    }
                });

                // Allow only valid characters
                itemNameInput.addEventListener('keypress', function(e) {
                    const char = e.key;
                    const validChars = /^[a-zA-Z0-9\s&'./\-]$/;
                    if (!validChars.test(char) && char !== 'Backspace' && char !== 'Delete') {
                        e.preventDefault();
                    }
                });

                // Validate on blur as well
                itemNameInput.addEventListener('blur', function() {
                    this.dispatchEvent(new Event('input'));
                });
            }

            // Phone Number validation
            const phoneNumberInput = document.getElementById('phone_number');
            if (phoneNumberInput) {
                phoneNumberInput.addEventListener('input', function(e) {
                    const errorDiv = document.getElementById('phone_number_error');
                    const value = this.value.replace(/\D/g, ''); // Remove non-digits

                    // Update the input value with only digits
                    this.value = value;

                    // Validation checks
                    if (value.length === 0) {
                        this.classList.add('is-invalid');
                        errorDiv.textContent = 'Phone number is required.';
                        errorDiv.style.display = 'block';
                    } else if (value.length < 10) {
                        this.classList.add('is-invalid');
                        errorDiv.textContent = 'Phone number must be at least 10 digits.';
                        errorDiv.style.display = 'block';
                    } else if (value.length > 15) {
                        this.classList.add('is-invalid');
                        errorDiv.textContent = 'Phone number cannot exceed 15 digits.';
                        errorDiv.style.display = 'block';
                    } else if (/^0+$/.test(value)) {
                        this.classList.add('is-invalid');
                        errorDiv.textContent = 'Phone number cannot be all zeros.';
                        errorDiv.style.display = 'block';
                    } else {
                        this.classList.remove('is-invalid');
                        errorDiv.style.display = 'none';
                    }
                });

                // Validate on blur as well
                phoneNumberInput.addEventListener('blur', function() {
                    this.dispatchEvent(new Event('input'));
                });

                // Only allow numbers input
                phoneNumberInput.addEventListener('keypress', function(e) {
                    if (!/\d/.test(e.key)) {
                        e.preventDefault();
                    }
                });
            }

            // Restaurant selection validation (for tenant_admin only)
            const restaurantSelect = document.getElementById('restaurant_id');
            if (restaurantSelect) {
                restaurantSelect.addEventListener('change', function() {
                    const errorDiv = document.getElementById('restaurant_id_error');
                    if (!this.value) {
                        this.classList.add('is-invalid');
                        errorDiv.textContent = 'Please select a restaurant.';
                        errorDiv.style.display = 'block';
                    } else {
                        this.classList.remove('is-invalid');
                        errorDiv.style.display = 'none';
                    }
                });
            }

            // Category validation
            const categorySelect = document.getElementById('menu_category_id');
            if (categorySelect) {
                categorySelect.addEventListener('change', function() {
                    const errorDiv = document.getElementById('menu_category_id_error');
                    if (!this.value) {
                        this.classList.add('is-invalid');
                        errorDiv.textContent = 'Please select a category.';
                        errorDiv.style.display = 'block';
                    } else {
                        this.classList.remove('is-invalid');
                        errorDiv.style.display = 'none';
                    }
                });
            }

            // Base Price validation
            const basePriceInput = document.getElementById('base_price');
            if (basePriceInput) {
                basePriceInput.addEventListener('input', function() {
                    const errorDiv = document.getElementById('base_price_error');
                    const price = parseFloat(this.value);

                    if (isNaN(price) || price < 0) {
                        this.classList.add('is-invalid');
                        errorDiv.textContent = 'Please enter a valid base price.';
                        errorDiv.style.display = 'block';
                    } else if (price > 99999.99) {
                        this.classList.add('is-invalid');
                        errorDiv.textContent = 'Base price cannot exceed 99,999.99.';
                        errorDiv.style.display = 'block';
                    } else {
                        this.classList.remove('is-invalid');
                        errorDiv.style.display = 'none';
                    }
                });
            }

            // Image validation
            const imageInput = document.getElementById('image_input');
            const imagePreview = document.getElementById('image_preview');
            const imageError = document.getElementById('image_error');
            const maxFileSize = 2 * 1024 * 1024; // 2MB in bytes
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            const allowedExtensions = ['.jpeg', '.jpg', '.png', '.gif', '.webp'];

            if (imageInput) {
                imageInput.addEventListener('change', function(e) {
                    const file = this.files[0];
                    imageError.style.display = 'none';
                    imageError.textContent = '';

                    if (file) {
                        // Validate file type
                        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
                        if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(
                                fileExtension)) {
                            imageError.textContent =
                                'Invalid file type. Please upload JPEG, PNG, JPG, GIF, or WebP images only.';
                            imageError.style.display = 'block';
                            this.value = '';
                            return;
                        }

                        // Validate file size
                        if (file.size > maxFileSize) {
                            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                            imageError.textContent =
                                `File size (${sizeMB}MB) exceeds the maximum allowed size of 2MB.`;
                            imageError.style.display = 'block';
                            this.value = '';
                            return;
                        }

                        // Preview image
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imagePreview.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Form submission validation
            const form = document.getElementById('menuItemForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    let isValid = true;
                    const fieldErrors = {};

                    // Validate all required fields
                    const requiredFields = [{
                            id: 'item_name',
                            name: 'Menu name'
                        },
                        {
                            id: 'phone_number',
                            name: 'Phone number'
                        },
                        {
                            id: 'menu_category_id',
                            name: 'Category'
                        },
                        {
                            id: 'base_price',
                            name: 'Base price'
                        },
                        {
                            id: 'restaurant_id',
                            name: 'Restaurant',
                            conditional: @json($user->role === 'tenant_admin')
                        }
                    ];

                    requiredFields.forEach(field => {
                        // Skip if this is a conditional field and the condition is false
                        if (field.conditional === false) {
                            return;
                        }

                        const element = document.getElementById(field.id);
                        if (element) {
                            let value = element.value;

                            // For select elements, check if a value is selected
                            if (element.tagName === 'SELECT') {
                                if (!value) {
                                    fieldErrors[field.id] = `${field.name} is required.`;
                                    isValid = false;
                                }
                            }
                            // For input/textarea elements
                            else {
                                value = value.toString().trim();
                                if (!value) {
                                    fieldErrors[field.id] = `${field.name} is required.`;
                                    isValid = false;
                                }
                            }
                        }
                    });

                    // Validate menu name specific rules
                    const itemName = document.getElementById('item_name');
                    if (itemName && itemName.value.trim()) {
                        const value = itemName.value.trim();
                        const validPattern = /^[a-zA-Z0-9\s&'./\-]+$/;
                        if (!validPattern.test(value)) {
                            fieldErrors.item_name =
                                'Menu name can only contain letters, numbers, spaces and special characters (&, \', ., /, -).';
                            isValid = false;
                        } else if (value.replace(/[^a-zA-Z]/g, '').length < 2) {
                            fieldErrors.item_name = 'Menu name must contain at least 2 alphabets.';
                            isValid = false;
                        }
                    }

                    // Validate phone number specific rules
                    const phoneNumber = document.getElementById('phone_number');
                    if (phoneNumber && phoneNumber.value) {
                        const value = phoneNumber.value.replace(/\D/g, '');
                        if (value.length < 10) {
                            fieldErrors.phone_number = 'Phone number must be at least 10 digits.';
                            isValid = false;
                        } else if (value.length > 15) {
                            fieldErrors.phone_number = 'Phone number cannot exceed 15 digits.';
                            isValid = false;
                        } else if (/^0+$/.test(value)) {
                            fieldErrors.phone_number = 'Phone number cannot be all zeros.';
                            isValid = false;
                        }
                    }

                    // Validate base price specific rules
                    const basePrice = document.getElementById('base_price');
                    if (basePrice && basePrice.value) {
                        const price = parseFloat(basePrice.value);
                        if (price > 99999.99) {
                            fieldErrors.base_price = 'Base price cannot exceed 99,999.99.';
                            isValid = false;
                        }
                    }

                    // Validate image if selected
                    const imageFile = document.getElementById('image_input');
                    if (imageFile && imageFile.files[0]) {
                        const file = imageFile.files[0];
                        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();

                        if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(
                                fileExtension)) {
                            fieldErrors.image = 'Invalid image file type.';
                            isValid = false;
                        }
                        if (file.size > maxFileSize) {
                            fieldErrors.image = 'Image file size exceeds 2MB limit.';
                            isValid = false;
                        }
                    }

                    if (!isValid) {
                        e.preventDefault();

                        // Display errors below respective fields
                        Object.keys(fieldErrors).forEach(fieldName => {
                            const errorElement = document.getElementById(fieldName + '_error');
                            const inputElement = document.getElementById(fieldName);

                            if (errorElement && inputElement) {
                                errorElement.textContent = fieldErrors[fieldName];
                                errorElement.style.display = 'block';
                                inputElement.classList.add('is-invalid');
                            }
                        });

                        // Scroll to first error
                        const firstErrorField = document.querySelector('.is-invalid');
                        if (firstErrorField) {
                            firstErrorField.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }
                    }
                });
            }

            // Clear validation errors when user starts typing
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    const errorElement = document.getElementById(this.id + '_error');
                    if (errorElement) {
                        errorElement.style.display = 'none';
                    }
                    this.classList.remove('is-invalid');
                });
            });

            // For select elements, clear error on change
            const selects = form.querySelectorAll('select');
            selects.forEach(select => {
                select.addEventListener('change', function() {
                    const errorElement = document.getElementById(this.id + '_error');
                    if (errorElement) {
                        errorElement.style.display = 'none';
                    }
                    this.classList.remove('is-invalid');
                });
            });
        });
    </script>
@endpush
