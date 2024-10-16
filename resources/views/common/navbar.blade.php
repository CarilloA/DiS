<style>
    /* Sidebar container styling */
    #sidebar {
        width: 250px;
        height: 100vh;
        background-color: #2c3e50; /* Dark background for better readability */
        color: #ecf0f1; /* Light text for contrast */
        position: fixed;
        top: 0;
        right: 0; /* Sidebar on the right */
        transition: all 0.3s;
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        overflow-y: auto;
        font-size: 1rem; /* Base font size */
        z-index: 1000; /* Ensure sidebar is above other elements */
    }

    /* Sidebar header */
    .sidebar-header {
        background-color: #34495e; /* Darker header for distinction */
        padding: 20px;
        text-align: center;
        color: #fff;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-header img {
        width: 80px;
        height: 80px;
        padding: 10px;
    }

    .sidebar-header h6 {
        margin: 0;
        font-weight: 600;
    }

    /* Sidebar navigation */
    #sidebar .components {
        padding: 0;
        margin: 0;
    }

    #sidebar .components li {
        list-style: none;
        padding: 15px 20px;
        margin: 5px 0;
        transition: all 0.3s;
    }

    #sidebar .components li a:active,
    #sidebar .components li a:hover {
        background-color: #1abc9c; /* Soft green on hover/active */
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        margin-bottom: -1em;
    }

    #sidebar .components li a {
        color: #ecf0f1;
        padding: 10px; /* Updated padding for better touch targets */
        display: block;
        transition: color 0.3s;
    }

    #sidebar .components li a i {
        margin-right: 10px;
    }

    /* Responsive behavior */
    @media (max-width: 768px) {
        #sidebar {
            width: 0; /* Initially hide sidebar on mobile */
            overflow: hidden;
        }

        #sidebar.collapsed {
            width: 250px; /* Expand sidebar when toggled */
        }

        .toggle-btn {
            display: block; /* Show toggle button on small screens */
            position: absolute;
            top: 15px;
            left: 15px;
            font-size: 30px;
            color: #ecf0f1; /* Hamburger icon color */
            cursor: pointer;
            z-index: 999; /* Ensure it's above other elements */
        }

        .content {
            margin-right: 0; /* Adjust content on smaller screens */
        }
    }

    /* Small hover effect for links */
    #sidebar .components li a:hover {
        padding-left: 25px;
        transition: padding-left 0.2s ease-in-out;
    }
</style>


@php
    $userImage = auth()->user()->image_url;
    $userRole = auth()->user()->role;
    $userName = auth()->user()->first_name . ' ' . auth()->user()->last_name;
@endphp

<!-- Sidebar Navigation -->
@if(Auth::user()->role == "Administrator")
<nav id="sidebar" class="vh-100 navbar-expand-lg">
    <button class="navbar-toggler toggle-btn" type="button" onclick="toggleSidebar()">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="sidebar-header d-flex flex-row align-items-center py-3">
        @if($userImage)
            <img class="image rounded-circle" src="{{ asset('storage/userImage/' . $userImage) }}">
        @else
            <i class="fa-solid fa-circle-user fa-3x me-3"></i>
        @endif

        <div class="d-flex flex-column">
            <h6>{{ $userRole }}</h6>
            <h6>{{ $userName }}</h6>
        </div>
    </div>
    <ul class="list-unstyled components">
        <li class="active">
            <a href="{{ route('accounts_table') }}"><i class="fa-solid fa-user-shield"></i> ACCOUNT MANAGEMENT</a>
        </li>
        <li>
            <a href="{{ route('accounts_table') }}"><i class="fa-solid fa-file"></i> REPORT</a>
        </li>
        <li>
            <a href="{{ route('accounts_table') }}"><i class="fa-solid fa-truck-ramp-box"></i> SUPPLIER</a>
        </li>
        <li>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-sign-out-alt"></i> LOGOUT
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</nav>
@endif

<!-- Sidebar Navigation -->
@if(Auth::user()->role == "Inventory Manager")
<nav id="sidebar" class="vh-100 navbar-expand-lg">
    <button class="navbar-toggler toggle-btn" type="button" onclick="toggleSidebar()">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="sidebar-header d-flex flex-row align-items-center py-3">
        @if($userImage)
            <img class="image rounded-circle" src="{{ asset('storage/userImage/' . $userImage) }}">
        @else
            <i class="fa-solid fa-circle-user fa-3x me-3"></i>
        @endif

        <div class="d-flex flex-column">
            <h6>{{ $userRole }}</h6>
            <h6>{{ $userName }}</h6>
        </div>
    </div>
    <ul class="list-unstyled components">
        <li>
            <a href="{{ route('show_profile') }}"><i class="fa-solid fa-user-shield"></i> PROFILE</a>
        </li>
        <li>
            <a href="{{ route('products_table') }}"><i class="fa-solid fa-warehouse"></i> INVENTORY</a>
        </li>
        <li>
            <a href="{{ route('accounts_table') }}"><i class="fa-solid fa-file"></i> REPORT</a>
        </li>
        <li>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-sign-out-alt"></i> LOGOUT
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</nav>
@endif

<!-- Sidebar Navigation -->
@if(Auth::user()->role == "Auditor")
<nav id="sidebar" class="vh-100 navbar-expand-lg">
    <button class="navbar-toggler toggle-btn" type="button" onclick="toggleSidebar()">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="sidebar-header d-flex flex-row align-items-center py-3">
        @if($userImage)
            <img class="image rounded-circle" src="{{ asset('storage/userImage/' . $userImage) }}">
        @else
            <i class="fa-solid fa-circle-user fa-3x me-3"></i>
        @endif

        <div class="d-flex flex-column">
            <h6>{{ $userRole }}</h6>
            <h6>{{ $userName }}</h6>
        </div>
    </div>
    <ul class="list-unstyled components">
        <li class="active">
            <a href="{{ route('show_profile') }}"><i class="fa-solid fa-user-shield"></i> PROFILE</a>
        </li>
        <li>
            <a href="{{ route('accounts_table') }}"><i class="fa-solid fa-list-check"></i> AUDIT INVENTORY</a>
        </li>
        <li>
            <a href="{{ route('accounts_table') }}"><i class="fa-solid fa-file"></i> REPORT</a>
        </li>
        <li>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-sign-out-alt"></i> LOGOUT
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</nav>
@endif

<!-- Additional Sidebar for Inventory Manager and Auditor goes here -->

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('collapsed'); // Toggle the collapsed class
    }
</script>



