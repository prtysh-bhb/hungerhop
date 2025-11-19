  <aside class="main-sidebar">
      <!-- sidebar-->

      <style>
          .sidebar-widgets {
              bottom: 0;
              position: absolute;
              width: 100%;
          }

          .text-white {
              white-space: normal;
          }
      </style>
      <section class="sidebar position-relative">
          <div class="multinav">
              <div class="multinav-scroll" style="height: 100%;">
                  <!-- sidebar menu-->
                  <ul class="sidebar-menu" data-widget="tree">

                      @if (auth()->user() && (auth()->user()->role === 'super_admin' || auth()->user()->role === 'tenant_admin'))
                          <li class="{{ request()->is('admin/dashboard*') ? 'active' : '' }}">
                              <a href="{{ route('admin.dashboard') }}">
                                  <i class="icon-Home"></i>
                                  <span>Dashboard</span>
                              </a>
                          </li>
                      @elseif(auth()->user() && auth()->user()->role === 'location_admin')
                          <li class="{{ request()->is('location-admin/dashboard*') ? 'active' : '' }}">
                              <a href="{{ route('location-admin.dashboard') }}">
                                  <i class="icon-Home"></i>
                                  <span>dashboard</span>
                              </a>
                          </li>
                      @elseif (auth()->user() && auth()->user()->role === 'customer')
                          <li class="{{ request()->is('customer/dashboard*') ? 'active' : '' }}">
                              <a href="{{ route('customer.dashboard') }}">
                                  <i class="icon-Home"></i>
                                  <span>Dashboard</span>
                              </a>
                          </li>
                      @endif
                      {{-- <li class="treeview {{ request()->is('admin/dashboard*') ? 'active menu-open' : '' }}">           
				  <a href="#">
					<i class="icon-Home"></i>
					<span>Dashboard</span>
					<span class="pull-right-container">
					  <i class="fa fa-angle-right pull-right"></i>
					</span>
				  </a>
				  <ul class="treeview-menu">
					<li class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
						<a href="{{ route('admin.dashboard') }}">
							<i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Admin Dashboard
						</a>
					</li>
					@if (auth()->check() && auth()->user()->role !== 'super_admin')
					<li class="{{ request()->is('customer/dashboard') ? 'active' : '' }}">
						<a href="{{ route('customer.dashboard') }}">
							<i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Customer Dashboard
						</a>
					</li>
					@endif
				  </ul> --}}

                      @if (auth()->check() && (auth()->user()->role === 'tenant_admin' || auth()->user()->role === 'location_admin'))
                          <li class="treeview {{ request()->is('restaurant/orders*') ? 'active menu-open' : '' }}">
                              <a href="#">
                                  <i class="icon-Clipboard-check"><span class="path1"></span><span
                                          class="path2"></span><span class="path3"></span></i>
                                  <span>Order</span>
                                  <span class="pull-right-container">
                                      <i class="fa fa-angle-right pull-right"></i>
                                  </span>
                              </a>
                              <ul class="treeview-menu">
                                  <li class="{{ request()->is('restaurant/orders') ? 'active' : '' }}">
                                      <a href="{{ route('restaurant.orders') }}">
                                          <i class="icon-Commit"><span class="path1"></span><span
                                                  class="path2"></span></i>Order List
                                      </a>
                                  </li>
                                  {{-- <li class="{{ request()->is('restaurant/orders/*') ? 'active' : '' }}">
						<a href="{{ route('restaurant.order.details', ['id' => 1]) }}">
							<i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Order Details
						</a>
					</li> --}}
                              </ul>
                          </li>
                      @endif

                      @auth
                          @if (auth()->user() && auth()->user()->role === 'super_admin')
                              <li class="{{ request()->is('delivery/partners') ? 'active' : '' }}">
                                  <a href="{{ route('partners.index') }}">
                                      <i class="icon-Commit"><span class="path1"></span><span
                                              class="path2"></span></i>Delivery Partners
                                  </a>
                              </li>
                          @endif
                      @endauth

                      @if (auth()->check() && (auth()->user()->role === 'tenant_admin' || auth()->user()->role === 'location_admin'))
                          <li class="treeview {{ request()->is('restaurant/menu*') ? 'active menu-open' : '' }}">
                              <a href="#">
                                  <i class="icon-Dinner"><span class="path1"></span><span class="path2"></span><span
                                          class="path3"></span><span class="path4"></span><span
                                          class="path5"></span></i>
                                  <span>Menus</span>
                                  <span class="pull-right-container">
                                      <i class="fa fa-angle-right pull-right"></i>
                                  </span>
                              </a>
                              <ul class="treeview-menu">
                                  <li class="{{ request()->is('restaurant/menu/add') ? 'active' : '' }}">
                                      <a href="{{ route('restaurant.menu.add') }}">
                                          <i class="icon-Commit"><span class="path1"></span><span
                                                  class="path2"></span></i>Add New Menu
                                      </a>
                                  </li>
                                  <li class="{{ request()->is('restaurant/menu/list') ? 'active' : '' }}">
                                      <a href="{{ route('restaurant.menu.list') }}">
                                          <i class="icon-Commit"><span class="path1"></span><span
                                                  class="path2"></span></i>Menu List
                                      </a>
                                  </li>
                                  <li class="{{ request()->is('restaurant/menu/categories') ? 'active' : '' }}">
                                      <a href="{{ route('restaurant.menu.categories') }}">
                                          <i class="icon-Commit"><span class="path1"></span><span
                                                  class="path2"></span></i>Categories
                                      </a>
                                  </li>
                              </ul>
                          </li>
                      @endif

                      <!-- Location Admin Restaurant Management -->
                      @if (auth()->user() && auth()->user()->role === 'location_admin')
                          <li
                              class="treeview {{ request()->is('restaurant-admin/list*') || request()->is('restaurant-admin/show*') || request()->is('restaurant-admin/edit*') ? 'active menu-open' : '' }}">
                              <a href="#">
                                  <i class="icon-Flag"><span class="path1"></span><span class="path2"></span></i>
                                  <span>My Restaurant</span>
                                  <span class="pull-right-container">
                                      <i class="fa fa-angle-right pull-right"></i>
                                  </span>
                              </a>
                              <ul class="treeview-menu">
                                  <li class="{{ request()->is('restaurant-admin/list') ? 'active' : '' }}">
                                      <a href="{{ route('restaurant-admin.list') }}">
                                          <i class="icon-Commit"><span class="path1"></span><span
                                                  class="path2"></span></i>Restaurant Details
                                      </a>
                                  </li>
                              </ul>
                          </li>
                      @endif
                      @if (auth()->check() && auth()->user()->role === 'super_admin')
                          <li
                              class="treeview {{ request()->is('admin/members*', 'admin/customers*') ? 'active menu-open' : '' }}">
                              <a href="#">
                                  <i class="icon-Group"><span class="path1"></span><span class="path2"></span></i>
                                  <span>Customer</span>
                                  <span class="pull-right-container">
                                      <i class="fa fa-angle-right pull-right"></i>
                                  </span>
                              </a>
                              <ul class="treeview-menu">
                                  <li class="{{ request()->is('admin/customers') ? 'active' : '' }}">
                                      <a href="{{ route('admin.customers') }}">
                                          <i class="icon-Commit"><span class="path1"></span><span
                                                  class="path2"></span></i>Customer list
                                      </a>
                                  </li>
                                  <li class="{{ request()->is('admin/members') ? 'active' : '' }}">
                                      <a href="{{ route('admin.members') }}">
                                          <i class="icon-Commit"><span class="path1"></span><span
                                                  class="path2"></span></i>Members
                                      </a>
                                  </li>
                              </ul>
                          </li>
                          <li class="{{ request()->is('admin/analysis') ? 'active' : '' }}">
                              <a href="{{ route('admin.analysis') }}">
                                  <i class="icon-Chart-line"><span class="path1"></span><span
                                          class="path2"></span></i>
                                  <span>Analysis**</span>
                              </a>
                          </li>
                      @endif

                      <!-- Restaurant Admin Section - Only for Super Admin and Tenant Admin -->
                      @can('access-restaurant-admin')
                          @if (auth()->user() && in_array(auth()->user()->role, ['super_admin', 'tenant_admin']))
                              <li class="treeview {{ request()->is('restaurant-admin*') ? 'active menu-open' : '' }}">
                                  <a href="#">
                                      <i class="icon-Flag"><span class="path1"></span><span class="path2"></span></i>
                                      <span>Restaurant Admin</span>
                                      <span class="pull-right-container">
                                          <i class="fa fa-angle-right pull-right"></i>
                                      </span>
                                  </a>
                                  <ul class="treeview-menu">
                                      <li
                                          class="{{ request()->is('restaurant-admin') && !request()->is('restaurant-admin/*') ? 'active' : '' }}">
                                          <a href="{{ route('restaurant-admin.index') }}">
                                              <i class="icon-Commit"><span class="path1"></span><span
                                                      class="path2"></span></i>Dashboard
                                          </a>
                                      </li>
                                      <li
                                          class="treeview {{ request()->is('restaurant-admin/registration*') || request()->is('restaurant-admin/list*') || request()->is('restaurant-admin/show*') || request()->is('restaurant-admin/edit*') ? 'active menu-open' : '' }}">
                                          <a href="#">
                                              <i class="icon-Commit"><span class="path1"></span><span
                                                      class="path2"></span></i>Restaurants
                                              <span class="pull-right-container">
                                                  <i class="fa fa-angle-right pull-right"></i>
                                              </span>
                                          </a>
                                          <ul class="treeview-menu">
                                              <li
                                                  class="{{ request()->is('restaurant-admin/registration/create') ? 'active' : '' }}">
                                                  <a href="{{ route('restaurant-admin.registration.create') }}">
                                                      <i class="icon-Commit"><span class="path1"></span><span
                                                              class="path2"></span></i>Add Restaurant
                                                  </a>
                                              </li>
                                              <li class="{{ request()->is('restaurant-admin/list') ? 'active' : '' }}">
                                                  <a href="{{ route('restaurant-admin.list') }}">
                                                      <i class="icon-Commit"><span class="path1"></span><span
                                                              class="path2"></span></i>Restaurant List
                                                  </a>
                                              </li>
                                              {{-- <li class="{{ request()->is('restaurant-admin/management') && !request()->is('restaurant-admin/management/*') ? 'active' : '' }}">
								<a href="{{ route('restaurant-admin.management.index') }}">
									<i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>All Restaurants
								</a>
							</li> --}}
                                          </ul>
                                      </li>
                          @endif

                          <li
                              class="treeview {{ request()->is('restaurant-admin/documents*') ? 'active menu-open' : '' }}">
                              <a href="#">
                                  <i class="icon-Commit"><span class="path1"></span><span
                                          class="path2"></span></i>Documents
                                  <span class="pull-right-container">
                                      <i class="fa fa-angle-right pull-right"></i>
                                  </span>
                              </a>
                              <ul class="treeview-menu">
                                  <li
                                      class="{{ request()->is('restaurant-admin/documents') && !request()->is('restaurant-admin/documents/*') ? 'active' : '' }}">
                                      <a href="{{ route('restaurant-admin.documents.index') }}">
                                          <i class="icon-Commit"><span class="path1"></span><span
                                                  class="path2"></span></i>All Documents
                                      </a>
                                  </li>
                                  <li class="{{ request()->is('restaurant-admin/documents/create') ? 'active' : '' }}">
                                      <a href="{{ route('restaurant-admin.documents.create') }}">
                                          <i class="icon-Commit"><span class="path1"></span><span
                                                  class="path2"></span></i>Upload Document
                                      </a>
                                  </li>
                              </ul>
                          </li>
                          {{-- <li class="treeview {{ request()->is('restaurant-admin/verification*') ? 'active menu-open' : '' }}">
						<a href="#">
							<i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Verification
							<span class="pull-right-container">
								<i class="fa fa-angle-right pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu">
							<li class="{{ request()->is('restaurant-admin/verification') && !request()->is('restaurant-admin/verification/*') ? 'active' : '' }}">
								<a href="{{ route('restaurant-admin.verification.index') }}">
									<i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pending Verification
								</a>
							</li>
							<li class="{{ request()->is('restaurant-admin/verification') && request()->get('status') === 'approved' ? 'active' : '' }}">
								<a href="{{ route('restaurant-admin.verification.index', ['status' => 'approved']) }}">
									<i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Approved Documents
								</a>
							</li>
							<li class="{{ request()->is('restaurant-admin/verification') && request()->get('status') === 'rejected' ? 'active' : '' }}">
								<a href="{{ route('restaurant-admin.verification.index', ['status' => 'rejected']) }}">
									<i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Rejected Documents
								</a>
							</li>
						</ul>
					</li> --}}
                      </ul>
                      </li>
                  @endcan

                  <!-- Tenant Management Section (Super Admin Only) -->
                  @if (auth()->check() && auth()->user()->role === 'super_admin')
                      @can('access-tenant-management')
                          <li class="treeview {{ request()->is('admin/tenants*') ? 'active menu-open' : '' }}">
                              <a href="#">
                                  <i class="icon-Group"><span class="path1"></span><span class="path2"></span></i>
                                  <span>Tenant Management</span>
                                  <span class="pull-right-container">
                                      <i class="fa fa-angle-right pull-right"></i>
                                  </span>
                              </a>
                              <ul class="treeview-menu">
                                  <li
                                      class="{{ request()->is('admin/tenants') && !request()->is('admin/tenants/*') ? 'active' : '' }}">
                                      <a href="{{ route('admin.tenants.index') }}">
                                          <i class="icon-Commit"><span class="path1"></span><span
                                                  class="path2"></span></i>All Tenants
                                      </a>
                                  </li>
                                  <li class="{{ request()->is('admin/tenants/create') ? 'active' : '' }}">
                                      <a href="{{ route('admin.tenants.create') }}">
                                          <i class="icon-Commit"><span class="path1"></span><span
                                                  class="path2"></span></i>Add New Tenant
                                      </a>
                                  </li>
                                  <li
                                      class="{{ request()->is('admin/tenants') && request()->get('status') === 'pending' ? 'active' : '' }}">
                                      <a href="{{ route('admin.tenants.index', ['status' => 'pending']) }}">
                                          <i class="icon-Commit"><span class="path1"></span><span
                                                  class="path2"></span></i>Pending Approval
                                      </a>
                                  </li>
                                  <li
                                      class="{{ request()->is('admin/tenants') && request()->get('status') === 'approved' ? 'active' : '' }}">
                                      <a href="{{ route('admin.tenants.index', ['status' => 'approved']) }}">
                                          <i class="icon-Commit"><span class="path1"></span><span
                                                  class="path2"></span></i>Active Tenants
                                      </a>
                                  </li>
                              </ul>
                          </li>
                      @endcan
                  @endif

                  @auth
                      @if (auth()->user()->role === 'tenant_admin')
                          <li>
                              <a href="{{ route('admin.tenant.payment.history') }}">
                                  <i class="fa fa-credit-card"><span class="path1"></span><span
                                          class="path2"></span></i>
                                  <span>Payment History</span>
                              </a>
                          </li>
                      @endif
                  @endauth <div class="sidebar-widgets">
                      <div class="mx-25 mb-30 pb-20 side-bx bg-primary bg-food-dark rounded20">
                          <div class="text-center">
                              <img src="{{ asset('assets/admin/images/res-menu.png') }}" class="sideimg"
                                  alt="">
                              <h3 class="title-bx">Add Menu</h3>
                              <a href="#" class="text-white py-10 fs-16 mb-0">
                                  Manage Your food and beverages menu <i class="mdi mdi-arrow-right"></i>
                              </a>
                          </div>
                      </div>
                      <div class="copyright text-start m-25">
                          <p><strong class="d-block">Riday Admin Dashboard</strong> © {{ date('Y') }} All Rights
                              Reserved</p>
                      </div>
                  </div>
              </div>
          </div>
      </section>
  </aside>
