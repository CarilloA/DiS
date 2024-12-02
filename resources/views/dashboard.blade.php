@extends('layouts.app')
<!-- Include the vertical navigation bar -->
@include('common.navbar')

<style>
    body {
        background-image: url('/storage/images/bg-photo.jpeg');
        background-size: cover; /* Cover the entire viewport */
        background-position: center; /* Center the background image */
        background-repeat: no-repeat; /* Prevent the image from repeating */
    }

    /* Main content styling */
    .content {
        margin-right: 250px; /* Leave space for the sidebar on larger screens */
        padding: 20px;
        overflow: hidden; /* Prevent content overflow */
        transition: margin-right 0.3s; /* Smooth transition when sidebar toggles */
        position: relative; /* Ensure relative positioning for overlays */
        z-index: 1; /* Ensure content is above background */
    }

    /* Ensure that the card does not overflow horizontally 
    .card {
        max-width: 100%;
        overflow: hidden;
        color: #fff !important;
        background-color: #565656 !important; 
        border-radius: 8px; 
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5); 
    }*/

    .main-content {
        padding: 20px; /* Add padding for inner spacing */
        margin: 0 20px; /* Add left and right margin */
        color: #fff !important;
        background-color: #565656 !important; 
        border-radius: 5px; /* Slightly rounded corners */
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5); 
    }

    @media (max-width: 768px) {
        .content {
            margin-right: 0; /* Remove margin on smaller screens */
        }
    }
</style>

@section('content')
    <div class="content"> <!-- Add the content class to prevent overlap -->
        {{-- @if(Auth::user()->role == "Administrator")
            <div class="container">
                <!-- Alert Messages -->
                @include('common.alert')
                <div class="row justify-content-center">
                    <div class="col">
                        <div class="main-content">
                            <!--<div class="text card-header text-center text-light fw-bold" style="background-color: #3a8f66">
                                {{ __("DASHBOARD: EMPLOYEE ACCOUNT LIST") }}
                            </div>-->
                            <h1 class="text-center mt-4 mb-4">Admin Dashboard</h1>
                        </div>
                    </div>
                </div>
            </div>
        @endif --}}

        @if(Auth::user()->role == "Inventory Manager")
            <div class="container">
                <!-- Alert Messages -->
                @include('common.alert')
                <div class="row justify-content-center">
                    <div class="col">
                        <div class="main-content">
                            <!--<div class="text card-header text-center text-light fw-bold" style="background-color: #3a8f66">
                                {{ __("DASHBOARD: EMPLOYEE ACCOUNT LIST") }}
                            </div>-->
                            <!-- Check and display low stock messages -->
                            {{-- @if(!empty($lowStockMessages))
                                <div class="alert alert-warning">
                                    <strong>Low Stock Alerts:</strong>
                                    <ul>
                                        @foreach($lowStockMessages as $message)
                                            <li>{{ $message }}
                                                <button type="button" class="btn btn-warning" onclick="window.location.href='{{ route('purchase_table') }}'">click here</button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif --}}
                            @if(!empty($lowStoreStockMessages))
                                <div class="alert alert-warning" style="height: 6em">
                                    <strong>Low Store Product Stock Alerts:</strong>
                                    <ul>
                                        @foreach($lowStoreStockMessages as $message)
                                            <li>{{ $message }}
                                                <button type="button" class="btn btn-warning" onclick="window.location.href='{{ route('purchase_table') }}'">click here</button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if(!empty($lowStockroomStockMessages))
                                <div class="alert alert-warning" style="height: 6em">
                                    <strong>Low Stockroom Product Stock Alerts:</strong>
                                    <ul>
                                        @foreach($lowStockroomStockMessages as $message)
                                            <li>{{ $message }}
                                                <button type="button" class="btn btn-warning" onclick="window.location.href='{{ route('purchase_table') }}'">click here</button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- start graphs --}}
                            <div class="container">
                                <h1 class="text-center">Inventory Dashboard</h1>
                                <div class="row">
                                    <div class="col">
                                        <h3>Stocks in Stockroom vs Store</h3>
                                        <canvas id="inventoryOverview"></canvas>
                                    </div>
                                    <div class="col">
                                        <h3>Low Stocks</h3>
                                        <canvas id="inventoryRestock" width="400" height="200"></canvas>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h3>Stock Transfer Tracking</h3>
                                        <canvas id="stockTransferChart" width="400" height="200"></canvas>
                                        {{-- <canvas id="stockTransferTracking"></canvas> --}}
                                    </div>
                                    <div class="col-md-6">
                                        <h3>Total Stock vs Total Sales Quantity</h3>
                                        <canvas id="stockSalesChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            
                            <script>
                                document.addEventListener('DOMContentLoaded', () => {
                                    // Stocks in Stockroom vs Store
                                    const inventoryOverviewCtx = document.getElementById('inventoryOverview')?.getContext('2d');
                                    if (inventoryOverviewCtx) {
                                        const totalStockroom = {{ $totalStockroom }};
                                        const totalStoreStock = {{ $totalStoreStock }};

                                        new Chart(inventoryOverviewCtx, {
                                            type: 'doughnut',
                                            data: {
                                                labels: [
                                                    `Stockroom: ${totalStockroom}`, // Display label and value
                                                    `Store: ${totalStoreStock}` // Display label and value
                                                ],
                                                datasets: [{
                                                    data: [totalStockroom, totalStoreStock], // Data points
                                                    backgroundColor: ['#3a8f66', '#64edbd'] // Colors
                                                }]
                                            },
                                            options: {
                                                responsive: true,
                                                cutout: '75%', // Set this to control the size of the hole in the center
                                                aspectRatio: 2.2,
                                                plugins: { 
                                                    legend: { 
                                                        position: 'top',
                                                        labels: { color: 'white' } // Legend label color
                                                    },
                                                    tooltip: {
                                                        callbacks: {
                                                            // Format tooltip to show the value in addition to the label
                                                            label: function(tooltipItem) {
                                                                const value = tooltipItem.raw;
                                                                return `${tooltipItem.label}: ${value}`; // Show the label with value
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    }

                                    const stockSalesCtx = document.getElementById('stockSalesChart')?.getContext('2d');
                                    if (stockSalesCtx) {
                                        const totalStock = {{ $totalStock }};
                                        const totalSalesQuantity = {{ $totalSalesQuantity }};  // Total sales quantity

                                        new Chart(stockSalesCtx, {
                                            type: 'doughnut',
                                            data: {
                                                labels: [
                                                    `Quantity on Hand: ${totalStock}`, // Display label and value
                                                    `Sales Quantity: ${totalSalesQuantity}` // Display label and value
                                                ],
                                                datasets: [{
                                                    data: [totalStock, totalSalesQuantity], // Data points
                                                    backgroundColor: ['#3a8f66', '#64edbd'] // Colors
                                                }]
                                            },
                                            options: {
                                                responsive: true,
                                                cutout: '75%', // Set this to control the size of the hole in the center
                                                aspectRatio: 2.2,
                                                plugins: { 
                                                    legend: { 
                                                        position: 'top',
                                                        labels: { color: 'white' } // Legend label color
                                                    },
                                                    tooltip: {
                                                        callbacks: {
                                                            // Format tooltip to show the value in addition to the label
                                                            label: function(tooltipItem) {
                                                                const value = tooltipItem.raw;
                                                                return `${tooltipItem.label}: ${value}`; // Show the label with value
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    }
                            
                                    // Stock Transfer Tracking
                                    const ctx = document.getElementById('stockTransferChart').getContext('2d');
                                    const transferData = @json($transferData); // Pass the data from the controller to JS
                                    const labels = transferData.map(item => item.date); // Extract dates for x-axis
                                    const quantities = transferData.map(item => item.quantity); // Extract transfer quantities

                                    const stockTransferChart = new Chart(ctx, {
                                        type: 'line', // You can change this to 'bar' or 'pie' based on your preference
                                        data: {
                                            labels: labels,
                                            datasets: [{
                                                label: 'Stock Transfer Quantity',
                                                data: quantities,
                                                borderColor: 'rgba(75, 192, 192, 1)',
                                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                                borderWidth: 1
                                            }]
                                        },
                                        options: {
                                            scales: {
                                                x: {
                                                    ticks: {
                                                        color: 'white' // Set x-axis ticks color to white
                                                    }
                                                },
                                                y: {
                                                    beginAtZero: true,
                                                    ticks: {
                                                        color: 'white' // Set y-axis ticks color to white
                                                    }
                                                }
                                            },
                                            responsive: true,
                                            plugins: {
                                                legend: {
                                                        position: 'top',
                                                        labels: { color: 'white' } // Change label color for the legend
                                                    },
                                            }
                                        }
                                    });

                                    const inventoryRestockCtx = document.getElementById('inventoryRestock')?.getContext('2d');
                                    if (inventoryRestockCtx) {
                                        const productNames = {!! json_encode($productNames) !!};
                                        const storeStock = {!! json_encode($storeStock) !!};
                                        const stockroomStock = {!! json_encode($stockroomStock) !!};

                                        new Chart(inventoryRestockCtx, {
                                            type: 'bar',
                                            data: {
                                                labels: productNames, // Array of product names
                                                datasets: [{
                                                    label: 'Store Stock',
                                                    data: storeStock, // Store stock values
                                                    backgroundColor: '#3a8f66', // Green color for store stock
                                                }, {
                                                    label: 'Stockroom Stock',
                                                    data: stockroomStock, // Stockroom stock values
                                                    backgroundColor: '#64edbd', // blue green color for stockroom stock
                                                }]
                                            },
                                            options: {
                                                responsive: true,
                                                plugins: { 
                                                    legend: {
                                                        position: 'top',
                                                        labels: { color: 'white' } // Change label color for the legend
                                                    },
                                                    tooltip: {
                                                        callbacks: {
                                                            label: function(tooltipItem) {
                                                                const value = tooltipItem.raw;
                                                                return `${tooltipItem.label}: ${value}`; // Display product and its value
                                                            }
                                                        }
                                                    }
                                                },
                                                scales: {
                                                    x: { 
                                                        title: { 
                                                            display: true, 
                                                            text: 'Products', 
                                                            color: 'white' 
                                                        }
                                                    },
                                                    y: {
                                                        title: { 
                                                            display: true, 
                                                            text: 'Stock Level', 
                                                            color: 'white' 
                                                        },
                                                        ticks: {
                                                            beginAtZero: true,
                                                            color: 'white' // Color the y-axis ticks white
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    }
                                });
                            </script>
                            
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(Auth::user()->role == "Auditor")
            <div class="container">
                <!-- Alert Messages -->
                @include('common.alert')
                <div class="row justify-content-center">
                    <div class="col">
                        <div class="main-content">
                            <!--<div class="text card-header text-center text-light fw-bold" style="background-color: #3a8f66">
                                {{ __("DASHBOARD: EMPLOYEE ACCOUNT LIST") }}
                            </div>-->
                            {{-- start graphs --}}
                            <div class="container">
                                <h1 class="text-center">Audit Dashboard</h1>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3>Discrepancy Summary</h3>
                                        <canvas id="discrepancySummaryChart"></canvas>
                                    </div>
                                    <div class="col-md-6">
                                        <h3>Quantity on Hand Discrepancy</h3>
                                        <canvas id="quantityComparisonChart"></canvas>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h3>Discrepancy Trends Over Time</h3>
                                        <canvas id="discrepancyTrendsChart"></canvas>
                                    </div>
                                    <div class="col-md-6">
                                        <h3>Previous vs New Stock Discrepancy</h3>
                                        <canvas id="newVsOldChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            <script>
                                // Discrepancy Summary Bar Chart
                                const discrepancySummaryCtx = document.getElementById('discrepancySummaryChart').getContext('2d');
                                new Chart(discrepancySummaryCtx, {
                                    type: 'bar',
                                    data: {
                                        labels: ['Stockroom Discrepancy', 'Store Discrepancy'],
                                        datasets: [{
                                            label: 'Discrepancy Amount',
                                            data: [{!! json_encode($discrepancies['stockroom']) !!}, {!! json_encode($discrepancies['store']) !!}],
                                            backgroundColor: ['#3a8f66', '#64edbd'],
                                            borderColor: '#ffffff',
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        plugins: {
                                            legend: { display: false },
                                        },
                                        scales: {
                                            x: { 
                                                ticks: { color: 'white' },
                                            },
                                            y: {
                                                ticks: { color: 'white' },
                                            }
                                        }
                                    }
                                });
                            
                                // Quantity on Hand vs Store Quantity Line Chart
                                const quantityComparisonCtx = document.getElementById('quantityComparisonChart').getContext('2d');
                                new Chart(quantityComparisonCtx, {
                                    type: 'line',
                                    data: {
                                        labels: {!! json_encode($auditDates) !!},
                                        datasets: [
                                            {
                                                label: 'Previous Quantity on Hand',
                                                data: {!! json_encode($quantityOnHand) !!},
                                                borderColor: '#3a8f66',
                                                backgroundColor: 'transparent',
                                                fill: false,
                                            },
                                            {
                                                label: 'New Quantity on Hand',
                                                data: {!! json_encode($newQuantityOnHand) !!},
                                                borderColor: '#64edbd',
                                                backgroundColor: 'transparent',
                                                fill: false,
                                            },
                                        ]
                                    },
                                    options: {
                                        responsive: true,
                                        plugins: {
                                            legend: { display: true, 
                                                labels: { color: 'white' }
                                            },
                                        },
                                        scales: {
                                            x: {
                                                ticks: { color: 'white' },
                                            },
                                            y: {
                                                ticks: { color: 'white' },
                                            }
                                        }
                                    }
                                });
                            
                                // new Vs Old Chart
                                const newVsOldCtx = document.getElementById('newVsOldChart').getContext('2d');
                                new Chart(newVsOldCtx, {
                                    type: 'line',
                                    data: {
                                        labels: {!! json_encode($auditDates) !!},
                                        datasets: [
                                            {
                                                label: 'Previous Store Quantity',
                                                data: {!! json_encode($storeQuantities) !!},
                                                borderColor: '#3a8f66',
                                                backgroundColor: 'transparent',
                                                fill: false,
                                            },
                                            {
                                                label: 'Previous Stockroom Quantity',
                                                data: {!! json_encode($stockroomQuantities) !!},
                                                borderColor: '#71f5c2',
                                                backgroundColor: 'transparent',
                                                fill: false,
                                            },
                                            {
                                                label: 'New Store Quantity',
                                                data: {!! json_encode($newStoreQuantities) !!},
                                                borderColor: '#967403',
                                                backgroundColor: 'transparent',
                                                fill: false,
                                            },
                                            {
                                                label: 'New Stockroom Quantity',
                                                data: {!! json_encode($newStockroomQuantities) !!},
                                                borderColor: '#edcb5a',
                                                backgroundColor: 'transparent',
                                                fill: false,
                                            }
                                        ]
                                    },
                                    options: {
                                        responsive: true,
                                        plugins: {
                                            legend: { display: true, 
                                                labels: { color: 'white' }
                                            },
                                        },
                                        scales: {
                                            x: {
                                                ticks: { color: 'white' },
                                            },
                                            y: {
                                                ticks: { color: 'white' },
                                            }
                                        }
                                    }
                                });
                            
                                // Discrepancy Trends Over Time Line Chart
                                const discrepancyTrendsCtx = document.getElementById('discrepancyTrendsChart').getContext('2d');
                                new Chart(discrepancyTrendsCtx, {
                                    type: 'line',
                                    data: {
                                        labels: {!! json_encode($auditDates) !!},
                                        datasets: [{
                                            label: 'Total Discrepancy',
                                            data: {!! json_encode($discrepancyData) !!},
                                            borderColor: '#64edbd',
                                            backgroundColor: 'transparent',
                                            fill: false,
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        plugins: {
                                            legend: { display: false },
                                        },
                                        scales: {
                                            x: {
                                                ticks: { color: 'white' },
                                            },
                                            y: {
                                                ticks: { color: 'white' },
                                            }
                                        }
                                    }
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        @endif 
    </div>
@endsection
