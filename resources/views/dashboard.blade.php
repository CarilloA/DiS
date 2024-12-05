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

    /* Styling for the notification bell and restock buttons */
    .notification-bell {
        display: flex;
        justify-content: flex-end;
        gap: 20px; /* Space between the buttons */
    }

    /* Styling for each restock button */
    .restock-button {
        position: relative; /* This allows absolute positioning for the notification circle */
        background-color: #3a8f66; /* Button background color */
        color: white;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
    }

    .restock-button:hover {
        background-color: #64edbd; /* Darker shade on hover */
        color: #000;
    }

    /* Styling for the notification circle */
    .notification-circle {
        position: absolute;
        top: -5px;
        right: -10px;
        background-color: #64edbd; /* Red background for the notification */
        color: #000;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        text-align: center;
        font-size: 12px;
        line-height: 20px; /* Center the number inside the circle */
    }

</style>

@section('content')
    <div class="content"> <!-- Add the content class to prevent overlap -->

        @if(Auth::user()->role == "Inventory Manager")
            <div class="container">
                <!-- Alert Messages -->
                @include('common.alert')
                <div class="row justify-content-center">
                    <div class="col">
                        <div class="main-content">

                            <!-- Notification Bell with Restock Buttons -->
                            <div class="notification-bell">
                                <!-- Restock Store Button with Notification -->
                                <button class="restock-button" onclick="window.location.href='{{ route('filter_store_restock') }}'">
                                    <i class="fas fa-bell"></i> Restock Store
                                    @if($lowStoreStockCount > 0)
                                        <span class="notification-circle">
                                            {{ $lowStoreStockCount }}
                                        </span>
                                    @endif
                                </button>

                                <!-- Restock Stockroom Button with Notification -->
                                <button class="restock-button" onclick="window.location.href='{{ route('filter_stockroom_restock') }}'">
                                    <i class="fas fa-bell"></i> Restock Stockroom
                                    @if($lowStockroomStockCount > 0)
                                        <span class="notification-circle">
                                            {{ $lowStockroomStockCount }}
                                        </span>
                                    @endif
                                </button>
                            </div>



                            {{-- start graphs --}}
                            <div class="container">
                                <h1 class="text-center">Inventory Dashboard</h1>
                                <div class="row">
                                    <div class="col">
                                        <h3>Stocks in Stockroom vs Store</h3>
                                        <canvas id="inventoryOverview"></canvas>
                                    </div>
                                    <div class="col-md-6">
                                        <h3>Stock Transfer Tracking</h3>
                                        <canvas id="stockTransferChart" width="400" height="200"></canvas>
                                        {{-- <canvas id="stockTransferTracking"></canvas> --}}
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
                                                    },
                                                    title: {
                                                        display: true,  // Enable the title to be displayed on the y-axis
                                                        text: 'Date Transferred',  // Set the y-axis label
                                                        color: 'white',  // Color of the y-axis title
                                                        font: {
                                                            size: 14,  // Font size for the label
                                                        }
                                                    }
                                                },
                                                y: {
                                                    beginAtZero: true,
                                                    ticks: {
                                                        color: 'white' // Set y-axis ticks color to white
                                                    },
                                                    title: {
                                                        display: true,  // Enable the title to be displayed on the y-axis
                                                        text: 'Product Quantity',  // Set the y-axis label
                                                        color: 'white',  // Color of the y-axis title
                                                        font: {
                                                            size: 14,  // Font size for the label
                                                        }
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
