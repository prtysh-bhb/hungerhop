<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}">
    
        <!-- Icons CSS -->
    <link href="{{ asset('assets/admin/icons/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/icons/Ionicons/css/ionicons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/icons/themify-icons/themify-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/icons/material-design-iconic-font/css/materialdesignicons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/icons/icomoon/style.css') }}" rel="stylesheet">

    <!-- Vendor Components -->
    <link href="{{ asset('assets/vendor_components/animate/animate.css') }}" rel="stylesheet">

    <!-- Main Theme CSS (compiled by Vite) -->
    
    <title>Riday - Restaurant Bootstrap Admin Template Webapp</title>
    
    <!-- Vendors Style-->
    <link rel="stylesheet" href="{{ asset('css/vendors_css.css') }}" rel="stylesheet">

    
      
    <!-- Style-->  
    <link rel="stylesheet" href="{{ asset('css/style.css') }}"  rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style_rtl.css') }}"  rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/skin_color.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/color_theme.css') }}">


    <!-- Vite SCSS -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    @stack('styles')
     
  </head>
<body class="hold-transition light-skin sidebar-mini theme-primary fixed">
	
    <div class="wrapper">
        <div id="loader"></div>

        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Header -->
        @include('partials.header')
        
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <div class="container-full">
                @yield('content')
            </div>
        </div>
        
        <!-- Footer -->
        @include('partials.footer')
        
        <!-- Right Sidebar -->
        @include('partials.right-sidebar')
        
        <!-- Control Sidebar -->
        @hasSection('control-sidebar')
            @yield('control-sidebar')
        @else
            @include('partials.control-sidebar')
        @endif
        
        <!-- Add the sidebar's background -->
        <div class="control-sidebar-bg"></div>
    </div>
    
    <script src="{{ asset('assets/admin/js/vendors.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/pages/chat-popup.js') }}"></script>
    <script src="{{ asset('assets/icons/feather-icons/feather.min.js') }}"></script>
        
    

    <script src="{{ asset('assets/vendor_components/OwlCarousel2/dist/owl.carousel.js') }}"></script>


    <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/maps.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/geodata/worldLow.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/themes/kelly.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
    
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>
    
    <!-- Riday Admin App -->
    <script src="{{ asset('js/template.js') }}"></script>

    <!-- Page-specific scripts -->
    @yield('scripts')
    @stack('scripts')

</body>
</html>
