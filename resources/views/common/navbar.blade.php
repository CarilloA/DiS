

    <!-- Sidebar styling improvements -->
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
        box-shadow: -2px 0 5px rgba(0,0,0,0.1); /* Subtle shadow */
        overflow-y: auto;
    }
    
    /* Sidebar header */
    .sidebar-header {
        background-color: #34495e; /* Darker header for distinction */
        padding: 20px;
        text-align: center;
        color: #fff;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .sidebar-header i {
        margin-bottom: 10px;
    }
    
    .sidebar-header h6 {
        margin: 0;
        font-size: 1.1rem;
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
    
    #sidebar .components li.active a,
    #sidebar .components li a:hover {
        background-color: #1abc9c; /* Soft green on hover/active */
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
    }
    
    #sidebar .components li a {
        color: #ecf0f1;
        padding: 0.5em;
        font-size: 1rem;
        font-weight: 500;
        display: block;
        transition: color 0.3s;
    }
    
    #sidebar .components li a i {
        margin-right: 10px;
    }
    
    /* Toggle button for smaller screens */
    .toggle-btn {
        display: none;
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 30px;
        color: #fff;
        cursor: pointer;
    }
    
    /* Responsive behavior */
    @media (max-width: 768px) {
        #sidebar {
            display: none;
        }
    
        .toggle-btn {
            display: block;
        }
    
        .content {
            margin-right: 0;
        }
    }
    
    /* Content margin adjustment */
    .content {
        margin-right: 250px; /* Adjust content to avoid overlap */
    }
    
    /* Small hover effect for links */
    #sidebar .components li a:hover {
        padding-left: 25px;
        transition: padding-left 0.2s ease-in-out;
    }
    </style>


@php
    $userImage = auth()->user()->image_url;
    $userRole = auth()->user()->credential->role;
    $userName = auth()->user()->first_name . ' ' . auth()->user()->last_name;
@endphp

<!-- Administrator Sidebar -->
@if(Auth::user()->credential->role == "Administrator")
<nav id="sidebar" class="vh-100">
    <div class="sidebar-header d-flex flex-row align-items-center py-3">

        @if($userImage)
            <img class="image rounded-circle" src="storage/userImage/{{$userImage}}" style="width: 80px; height: 80px; padding: 10px;">
        @endif
        @if(!$userImage)
            <!-- User icon on the left side -->
            <i class="fa-solid fa-circle-user fa-3x me-3"></i>
        @endif
        
        <!-- Role and Name in a vertical column -->
        <div class="d-flex flex-column">
            <h6>{{ $userRole }}</h6>
            <h6>{{ $userName }}</h6>
        </div>
    </div>
    <ul class="list-unstyled components">
        <li class="active">
            <a href="{{ route('accounts_table') }}" style="margin-bottom: -1.5em;"><i class="fa-solid fa-user-shield"></i> ACCOUNT MANAGEMENT</a>
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

<!-- Inventory Manager Sidebar -->
@if(Auth::user()->credential->role == "Inventory Manager")
<nav id="sidebar" class="vh-100">
    <div class="sidebar-header d-flex flex-row align-items-center py-3">

        @if($userImage)
            <img class="image rounded-circle" src="storage/userImage/{{$userImage}}" style="width: 80px; height: 80px; padding: 10px;">
        @endif
        @if(!$userImage)
            <!-- User icon on the left side -->
            <i class="fa-solid fa-circle-user fa-3x me-3"></i>
        @endif
        
        <!-- Role and Name in a vertical column -->
        <div class="d-flex flex-column">
            <p style="margin-bottom: -0.2em;">{{ $userRole }}</p>
            <h6>{{ $userName }}</h6>
        </div>
    </div>
    <ul class="list-unstyled components">
        <li class="active">
            <a href="{{ route('accounts_table') }}" style="margin-bottom: -1.5em;"><i class="fa-solid fa-user-shield"></i> PROFILE</a>
        </li>
        <li>
            <a href="{{ route('accounts_table') }}" style="margin-bottom: -1.5em;"><i class="fa-solid fa-warehouse"></i> INVENTORY</a>
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

<!-- Inventory Manager Sidebar -->
@if(Auth::user()->credential->role == "Auditor")
<nav id="sidebar" class="vh-100">
    <div class="sidebar-header d-flex flex-row align-items-center py-3">

        @if($userImage)
            <img class="image rounded-circle" src="storage/userImage/{{$userImage}}" style="width: 80px; height: 80px; padding: 10px;">
        @endif
        @if(!$userImage)
            <!-- User icon on the left side -->
            <i class="fa-solid fa-circle-user fa-3x me-3"></i>
        @endif
        
        <!-- Role and Name in a vertical column -->
        <div class="d-flex flex-column">
            <h6>{{ $userRole }}</h6>
            <h6>{{ $userName }}</h6>
        </div>
    </div>
    <ul class="list-unstyled components">
        <li class="active">
            <a href="{{ route('accounts_table') }}" style="margin-bottom: -1.5em;"><i class="fa-solid fa-user-shield"></i> PROFILE</a>
        </li>
        <li>
            <a href="{{ route('accounts_table') }}" style="margin-bottom: -1.5em;"><i class="fa-solid fa-list-check"></i> AUDIT INVENTORY</a>
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

