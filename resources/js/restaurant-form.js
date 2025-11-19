document.addEventListener('DOMContentLoaded', function() {
    const restaurantForm = {
        init() {
            this.bindEvents();
            this.initializeMap();
            this.setupImagePreviews();
        },

        bindEvents() {
            // State change handler
            const stateSelect = document.getElementById('state_id');
            if (stateSelect) {
                stateSelect.addEventListener('change', this.loadCities.bind(this));
            }

            // Address geocoding
            const addressInput = document.getElementById('address');
            if (addressInput) {
                addressInput.addEventListener('blur', this.geocodeAddress.bind(this));
            }

            // Form validation
            const form = document.getElementById('restaurant-form');
            if (form) {
                form.addEventListener('submit', this.validateForm.bind(this));
            }
        },

        async loadCities(event) {
            const stateId = event.target.value;
            const citySelect = document.getElementById('city_id');
            
            if (!stateId) {
                citySelect.innerHTML = '<option value="">Select City</option>';
                return;
            }

            try {
                const response = await fetch(`/admin/cities-by-state?state_id=${stateId}`);
                const cities = await response.json();
                
                citySelect.innerHTML = '<option value="">Select City</option>';
                cities.forEach(city => {
                    citySelect.innerHTML += `<option value="${city.id}">${city.name}</option>`;
                });
            } catch (error) {
                console.error('Error loading cities:', error);
                this.showError('Failed to load cities');
            }
        },

        async geocodeAddress(event) {
            const address = event.target.value;
            if (!address) return;

            // You can integrate with Google Maps Geocoding API here
            // For now, we'll just show a placeholder
            console.log('Geocoding address:', address);
        },

        initializeMap() {
            // Initialize map for location selection
            // You can integrate with Google Maps or any other map service
            const mapContainer = document.getElementById('location-map');
            if (mapContainer) {
                // Map initialization code here
                console.log('Map initialized');
            }
        },

        setupImagePreviews() {
            // Image preview functionality
            const imageInputs = ['image_url', 'cover_image_url'];
            
            imageInputs.forEach(inputId => {
                const input = document.getElementById(inputId);
                if (input) {
                    input.addEventListener('change', (e) => this.previewImage(e, inputId));
                }
            });
        },

        previewImage(event, inputId) {
            const file = event.target.files[0];
            const previewId = inputId.replace('_url', '_preview');
            const preview = document.getElementById(previewId);
            
            if (file && preview) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        },

        validateForm(event) {
            let isValid = true;
            const errors = [];

            // Validate required fields
            const requiredFields = [
                'restaurant_name', 'phone', 'email', 'address',
                'minimum_order_amount', 'base_delivery_fee', 'delivery_radius_km'
            ];

            requiredFields.forEach(field => {
                const input = document.getElementById(field);
                if (input && !input.value.trim()) {
                    errors.push(`${field.replace('_', ' ')} is required`);
                    isValid = false;
                }
            });

            // Validate email format
            const emailInput = document.getElementById('email');
            if (emailInput && emailInput.value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailInput.value)) {
                    errors.push('Please enter a valid email address');
                    isValid = false;
                }
            }

            // Validate phone format
            const phoneInput = document.getElementById('phone');
            if (phoneInput && phoneInput.value) {
                const phoneRegex = /^[+]?[0-9\s\-\(\)]+$/;
                if (!phoneRegex.test(phoneInput.value)) {
                    errors.push('Please enter a valid phone number');
                    isValid = false;
                }
            }

            // Validate delivery radius
            const radiusInput = document.getElementById('delivery_radius_km');
            if (radiusInput && radiusInput.value) {
                const radius = parseFloat(radiusInput.value);
                if (radius > 50) {
                    errors.push('Delivery radius cannot exceed 50 km');
                    isValid = false;
                }
            }

            if (!isValid) {
                event.preventDefault();
                this.showErrors(errors);
            }

            return isValid;
        },

        showError(message) {
            // Show error message to user
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger';
            errorDiv.textContent = message;
            
            const container = document.querySelector('.container');
            if (container) {
                container.insertBefore(errorDiv, container.firstChild);
                setTimeout(() => errorDiv.remove(), 5000);
            }
        },

        showErrors(errors) {
            const errorContainer = document.getElementById('error-container') || this.createErrorContainer();
            errorContainer.innerHTML = '';
            
            errors.forEach(error => {
                const errorItem = document.createElement('div');
                errorItem.className = 'alert alert-danger';
                errorItem.textContent = error;
                errorContainer.appendChild(errorItem);
            });
            
            errorContainer.scrollIntoView({ behavior: 'smooth' });
        },

        createErrorContainer() {
            const container = document.createElement('div');
            container.id = 'error-container';
            container.className = 'mb-4';
            
            const form = document.getElementById('restaurant-form');
            if (form) {
                form.insertBefore(container, form.firstChild);
            }
            
            return container;
        }
    };

    // Initialize the restaurant form
    restaurantForm.init();
});