@extends('layouts.admin')

@section('title', 'Restaurant Dashboard')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xxxl-3 col-lg-6 col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="d-flex align-items-start">
                            <div>
                                <img src="{{ asset('images/food/online-order-1.png') }}" class="w-80 me-20" alt="" />
                            </div>
                            <div>
                                <h2 class="my-0 fw-700" id="total-orders">{{ $totalOrders }}</h2>
                                <p class="text-fade mb-0">Total Order</p>
                                <p class="fs-12 mb-0 text-success"><span
                                        class="badge badge-pill badge-success-light me-5"><i
                                            class="fa fa-arrow-up"></i></span>3% (15 Days)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxxl-3 col-lg-6 col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="d-flex align-items-start">
                            <div>
                                <img src="{{ asset('images/food/online-order-2.png') }}" class="w-80 me-20"
                                    alt="" />
                            </div>
                            <div>
                                <h2 class="my-0 fw-700" id="completed-orders">{{ $completedOrders }}</h2>
                                <p class="text-fade mb-0">Total Delivered</p>
                                <p class="fs-12 mb-0 text-success"><span
                                        class="badge badge-pill badge-success-light me-5"><i
                                            class="fa fa-arrow-up"></i></span>8% (15 Days)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxxl-3 col-lg-6 col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="d-flex align-items-start">
                            <div>
                                <img src="{{ asset('images/food/online-order-3.png') }}" class="w-80 me-20"
                                    alt="" />
                            </div>
                            <div>
                                <h2 class="my-0 fw-700" id="canceled-orders">{{ $canceledOrders }}</h2>
                                <p class="text-fade mb-0">Total Canceled</p>
                                <p class="fs-12 mb-0 text-primary"><span
                                        class="badge badge-pill badge-primary-light me-5"><i
                                            class="fa fa-arrow-down"></i></span>2% (15 Days)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxxl-3 col-lg-6 col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="d-flex align-items-start">
                            <div>
                                <img src="{{ asset('images/food/online-order-4.png') }}" class="w-80 me-20"
                                    alt="" />
                            </div>
                            <div>
                                <h2 class="my-0 fw-700" id="total-revenue">{{ $totalRevenue }}</h2>
                                <p class="text-fade mb-0">Total Revenue</p>
                                <p class="fs-12 mb-0 text-primary"><span
                                        class="badge badge-pill badge-primary-light me-5"><i
                                            class="fa fa-arrow-down"></i></span>12% (15 Days)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxxl-7 col-xl-6 col-lg-6 col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="box-title mb-0">Daily Revenue</h4>
                                <p class="mb-0 text-mute">Lorem ipsum dolor</p>
                            </div>
                            <div class="text-end">
                                <h3 class="box-title mb-0 fw-700">₹{{ $totalRevenue }}</h3>
                                <p class="mb-0"><span class="text-success">+ 1.5%</span> than last week</p>
                            </div>
                        </div>
                        <div id="chart" class="mt-20"></div>
                    </div>
                </div>
            </div>
            <div class="col-xxxl-5 col-xl-6 col-lg-6 col-12">
                <div class="box">
                    <div class="box-body">
                        <h4 class="box-title">Customer Flow</h4>
                        <div class="d-md-flex d-block justify-content-between">
                            <div>
                                <h3 class="mb-0 fw-700">$2,780k</h3>
                                <p class="mb-0 text-primary"><small>In Restaurant</small></p>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-700">$1,410k</h3>
                                <p class="mb-0 text-danger"><small>Online Order</small></p>
                            </div>
                        </div>
                        <div id="yearly-comparison"></div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="box bg-transparent no-shadow">
                    <div class="box-header pt-0 px-0">
                        <h4 class="box-title">
                            Customer Review
                        </h4>
                    </div>
                    <div class="box-body px-0">
                        <div class="review-slider owl-carousel">
                            @if ($reviews->count() > 0)
                                @foreach ($reviews as $review)
                                    <div class="box p-0">
                                        <div class="box-body">
                                            <div class="d-flex align-items-center">
                                                <div class="review-tx">
                                                    <div class="d-flex mb-10">
                                                        <img src="{{ asset('images/avatar/1.jpg') }}"
                                                            class="w-40 h-40 me-10 rounded100" alt="" />
                                                        <div>
                                                            <p class="mb-0">
                                                                {{ $review->customer && $review->customer->user ? $review->customer->user->first_name . ' ' . $review->customer->user->last_name : 'Anonymous Customer' }}
                                                            </p>
                                                            <p class="mb-0"><small
                                                                    class="text-mute">{{ $review->created_at ? $review->created_at->diffForHumans() : 'Recently' }}</small>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <p class="mb-10">
                                                        {{ $review->review_text ?? 'No review text available' }}</p>
                                                    <div class="d-flex text-warning align-items-center">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @if ($i <= ($review->rating ?? 0))
                                                                <i class="fa fa-star"></i>
                                                            @else
                                                                <i class="fa fa-star-o"></i>
                                                            @endif
                                                        @endfor
                                                        <span class="text-fade ms-10">{{ $review->rating ?? '0' }}</span>
                                                    </div>
                                                </div>
                                                <img src="{{ asset('images/food/dish-1.png') }}" class="dish-img"
                                                    alt="" />
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="box p-0">
                                    <div class="box-body">
                                        <div class="d-flex align-items-center">
                                            <div class="review-tx">
                                                <div class="d-flex mb-10">
                                                    <img src="{{ asset('images/avatar/1.jpg') }}"
                                                        class="w-40 h-40 me-10 rounded100" alt="" />
                                                    <div>
                                                        <p class="mb-0">No Reviews Yet</p>
                                                        <p class="mb-0"><small class="text-mute">No reviews
                                                                found</small></p>
                                                    </div>
                                                </div>
                                                <p class="mb-10">No customer reviews available yet.</p>
                                                <div class="d-flex text-warning align-items-center">
                                                    <i class="fa fa-star-o"></i>
                                                    <i class="fa fa-star-o"></i>
                                                    <i class="fa fa-star-o"></i>
                                                    <i class="fa fa-star-o"></i>
                                                    <i class="fa fa-star-o"></i>
                                                    <span class="text-fade ms-10">0</span>
                                                </div>
                                            </div>
                                            <img src="{{ asset('images/food/dish-1.png') }}" class="dish-img"
                                                alt="" />
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxxl-5 col-12">
                <div class="box">
                    <div class="box-header no-border">
                        <h4 class="box-title">
                            Trending Keyword
                            <small class="subtitle">Lorem ipsum dolor sit amet, consectetur adipisicing elit</small>
                        </h4>
                    </div>
                    <div class="box-body pt-0">
                        <div>
                            <div class="progress mb-5">
                                <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar"
                                    aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="text-primary">#paneer</p>
                                <p class="text-mute">420 times</p>
                            </div>
                        </div>
                        <div>
                            <div class="progress mb-5">
                                <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar"
                                    aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="text-primary">#breakfast</p>
                                <p class="text-mute">150 times</p>
                            </div>
                        </div>
                        <div>
                            <div class="progress mb-5">
                                <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar"
                                    aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="text-primary">#tea</p>
                                <p class="text-mute">120 times</p>
                            </div>
                        </div>
                    </div>
                    <div class="box-body pt-0">
                        <h4 class="box-title d-block">
                            Others Tag
                        </h4>
                        <div class="d-inline-block">
                            <a href="#"
                                class="waves-effect waves-light btn btn-outline btn-rounded btn-primary mb-5">#panjabifood</a>
                            <a href="#"
                                class="waves-effect waves-light btn btn-outline btn-rounded btn-primary mb-5">#chainissfood</a>
                            <a href="#"
                                class="waves-effect waves-light btn btn-outline btn-rounded btn-primary mb-5">#pizza</a>
                            <a href="#"
                                class="waves-effect waves-light btn btn-outline btn-rounded btn-primary mb-5">#burgar</a>
                            <a href="#"
                                class="waves-effect waves-light btn btn-outline btn-rounded btn-primary mb-5">#coffee</a>
                            <a href="#"
                                class="waves-effect waves-light btn btn-outline btn-rounded btn-primary mb-5">20+</a>
                        </div>
                    </div>
                </div>
                <div class="box">
                    <div class="box-header no-border">
                        <h4 class="box-title">
                            Today's Special
                        </h4>
                    </div>
                    <div class="box-body pt-0">
                        <div class="mb-5">
                            <img class="rounded img-fluid" src="{{ asset('images/food/img1.jpg') }}" alt="">
                        </div>
                        <div class="info-content">
                            <h5 class="my-15"><a href="ecommerce_details.html">Spicy Pizza with Extra Cheese</a></h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="mb-0 text-black">$6.53</h4>
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-heart text-primary"></i>
                                    <h6 class="text-black mb-0">256k</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxxl-7 col-12">
                <div class="box">
                    <div class="box-header no-border">
                        <h4 class="box-title">Weekday Visitor <span class="text-danger fs-18 fw-500">1.40%</span></h4>
                        <div class="box-controls pull-right">
                            <div class="box-header-actions">
                                <div class="lookup lookup-sm lookup-right d-none d-lg-block">
                                    <input type="text" name="s" placeholder="Search" class="w-p100">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-12">
                                <div>
                                    <div class="box p-0">
                                        <div class="box-body p-0">
                                            <div class="media-list media-list-hover">
                                                <div class="media border-primary border-2">
                                                    <span class="badge badge-dot badge-primary"></span>
                                                    <a class="avatar avatar-lg status-success mx-0" href="#">
                                                        <img src="{{ asset('images/avatar/1.jpg') }}"
                                                            class="bg-success-light" alt="...">
                                                    </a>
                                                    <div class="media-body">
                                                        <p class="fs-16">
                                                            <a class="hover-primary"
                                                                href="#"><strong>James</strong></a>
                                                        </p>
                                                        <p class="text-muted">Designer</p>
                                                    </div>
                                                    <div class="media-right">
                                                        <span class="font-size-14">21 Aug</span>
                                                    </div>
                                                </div>

                                                <div class="media border-warning border-2">
                                                    <span class="badge badge-dot badge-warning"></span>
                                                    <a class="avatar avatar-lg status-danger mx-0" href="#">
                                                        <img src="{{ asset('images/avatar/2.jpg') }}"
                                                            class="bg-warning-light" alt="...">
                                                    </a>
                                                    <div class="media-body">
                                                        <p class="fs-16">
                                                            <a class="hover-primary"
                                                                href="#"><strong>Michal</strong></a>
                                                        </p>
                                                        <p class="text-muted">Developer</p>
                                                    </div>
                                                    <div class="media-right">
                                                        <span class="font-size-14">18 Aug</span>
                                                    </div>
                                                </div>

                                                <div class="media border-info border-2">
                                                    <span class="badge badge-dot badge-info"></span>
                                                    <a class="avatar avatar-lg status-warning mx-0" href="#">
                                                        <img src="{{ asset('images/avatar/3.jpg') }}"
                                                            class="bg-info-light" alt="...">
                                                    </a>
                                                    <div class="media-body">
                                                        <p class="fs-16">
                                                            <a class="hover-primary"
                                                                href="#"><strong>Robert</strong></a>
                                                        </p>
                                                        <p class="text-muted">Manager</p>
                                                    </div>
                                                    <div class="media-right">
                                                        <span class="font-size-14">16 Aug</span>
                                                    </div>
                                                </div>

                                                <div class="media border-danger border-2">
                                                    <span class="badge badge-dot badge-danger"></span>
                                                    <a class="avatar avatar-lg status-primary mx-0" href="#">
                                                        <img src="{{ asset('images/avatar/4.jpg') }}"
                                                            class="bg-danger-light" alt="...">
                                                    </a>
                                                    <div class="media-body">
                                                        <p class="fs-16">
                                                            <a class="hover-primary"
                                                                href="#"><strong>Charlie</strong></a>
                                                        </p>
                                                        <p class="text-muted">Sales</p>
                                                    </div>
                                                    <div class="media-right">
                                                        <span class="font-size-14">22 Aug</span>
                                                    </div>
                                                </div>

                                                <div class="media border-success border-2">
                                                    <span class="badge badge-dot badge-success"></span>
                                                    <a class="avatar avatar-lg status-success mx-0" href="#">
                                                        <img src="{{ asset('images/avatar/5.jpg') }}"
                                                            class="bg-success-light" alt="...">
                                                    </a>
                                                    <div class="media-body">
                                                        <p class="fs-16">
                                                            <a class="hover-primary"
                                                                href="#"><strong>Jordan</strong></a>
                                                        </p>
                                                        <p class="text-muted">Designer</p>
                                                    </div>
                                                    <div class="media-right">
                                                        <span class="font-size-14">24 Aug</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Recent Orders Section -->
        </div>
    </section>
    <!-- /.content -->
@endsection

@section('scripts')
    <script src="{{ asset('assets/vendor_components/apexcharts-bundle/dist/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/OwlCarousel2/dist/owl.carousel.js') }}"></script>
    <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/maps.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/geodata/worldLow.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/themes/kelly.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
    <script src="{{ asset('js/pages/dashboard.js') }}"></script>

    <!-- Simple Live Dashboard Updates -->
    <script>
        // Simple dashboard auto-refresh
        function updateDashboardStats() {
            fetch('{{ route('admin.dashboard.stats') }}')
                .then(response => response.json())
                .then(data => {
                    // Update the dashboard numbers with animation
                    updateCounter('#total-orders', data.totalOrders);
                    updateCounter('#completed-orders', data.completedOrders);
                    updateCounter('#canceled-orders', data.canceledOrders);
                    updateCounter('#total-revenue', '$' + data.totalRevenue);

                    console.log('Dashboard updated successfully at:', new Date().toLocaleTimeString());
                })
                .catch(error => {
                    console.log('Dashboard update failed:', error);
                });
        }

        function updateCounter(selector, newValue) {
            const element = document.querySelector(selector);
            if (element) {
                element.textContent = newValue;
            }
        }

        // Start auto-refresh when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Dashboard live updates enabled - will check for new orders every 5 seconds');

            // Update stats every 5 seconds
            setInterval(updateDashboardStats, 10000);

            // Update once immediately after 5 seconds
            setTimeout(updateDashboardStats, 5000);
        });
    </script>
@endsection
