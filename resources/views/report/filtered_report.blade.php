@extends('layouts.app')

<!-- Wrap navbar in a div with a specific class for print -->
<div class="navbar-print-hide">
    @include('common.navbar')
</div>

@section('content')
<style>
    @media print {
        .navbar-print-hide {
            display: none; /* Hide the navbar during printing */
        }
        
        body {
            background-image: none; /* Remove background image in print */
            font-size: 10pt; /* Ensure font size is appropriate for the report */
        }

        .table {
            width: 100%;
            font-size: 8pt;
        }

        .table th, .table td {
            padding: 4px;
            text-align: center;
        }

        .table th {
            background-color: #f8f9fa;
        }

        /* Make sure the table fits within the page */
        .table-bordered {
            border-collapse: collapse;
            page-break-inside: auto;
        }

        .table-bordered td, .table-bordered th {
            border: 1px solid #dee2e6;
        }

        .container-fluid,
        .main-content {
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        #main-content {
            background-color: transparent !important;
            color: black !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        /* Adjust header and footer for print */
        .main-content h1 {
            font-size: 18pt;
            text-align: center;
            margin-bottom: 10px;
        }
        .main-content h5 {
            font-size: 10pt;
            margin-bottom: 10px;
        }

        .printButton {
            display: none;
        }

         /* Signature upload and footer styling */
        .signature-section {
            margin-bottom: 30px;
            text-align: center;
        }

        #signature-preview {
            margin-top: 20px;
            text-align: center;
        }

        .prepared-by-container {
            display: flex;
            flex-direction: column; /* Stack signature and name vertically */
            justify-content: flex-start; /* Align to the left */
            align-items: flex-start; /* Align text and signature to the left */
            margin-top: 40px;
            border-top: none; /* No top border in print view */
            padding-top: 20px;
        }

        #signature-container {
            margin-right: 15px; /* Space between signature and text */
            flex-shrink: 0; /* Prevent the signature from shrinking */
        }

        #signature-preview img {
            max-width: 120px;  /* Adjust image size for printing */
            max-height: 60px;
            visibility: visible !important;  /* Ensure the signature is visible */
        }

        .prepared-by {
            font-size: 12pt;
            font-style: italic;
            color: #333;
            text-align: left;
            line-height: 1.6;
            margin-top: 10px; /* Add spacing between signature and the name */
        }

        .prepared-by h5 {
            font-weight: normal;
            margin-left: 15px; /* Space between signature and name */
        }

        /* Hide signature upload form and button during printing */
        .signature-form {
            display: none;
        }

        /* Ensure the content fits within the bond paper size */
        /* @page {
            size: 8.5in 11in;
            margin: 0.5in;
        } */
    }

    .container-fluid,
    .main-content {
        margin-top: 0;
        padding-top: 0;
    }
    #main-content {
        padding: 20px; /* Add padding for inner spacing */
        margin: 0 20px; /* Add left and right margin */
        color: #fff;
        background-color: #565656; 
        border-radius: 5px; /* Slightly rounded corners */
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5); 
    }

    body {
        background-image: url('/storage/images/bg-photo.jpeg');
        background-size: cover; /* Cover the entire viewport */
        background-position: center; /* Center the background image */
        background-repeat: no-repeat; /* Prevent the image from repeating */
    }
    table th, td {
        text-align: center;
    }

    .printButton {
        margin-bottom: 1em;
    }

    /* Footer */
    /* Signature upload and footer styling */
    .signature-section {
        margin-bottom: 30px;
        text-align: center;
    }

    #signature-preview {
        margin-top: 20px;
        text-align: center;
    }

    .prepared-by-container {
        display: flex;
        justify-content: flex-start; /* Align to the left for a formal report */
        align-items: center;
        margin-top: 40px;
        border-top: 2px solid #000;
        padding-top: 20px;
    }

    #signature-container {
        margin-right: 15px; /* Space between signature and text */
        flex-shrink: 0; /* Prevent the signature from shrinking */
    }

    #signature-preview img {
        max-width: 150px;  /* Adjust image width */
        max-height: 80px;  /* Ensure the signature is not too big */
    }

    .prepared-by {
        font-size: 12pt;
        font-style: italic;
        color: #333;
        text-align: left;
        line-height: 1.6;
        display: flex;
        align-items: flex-start;
    }

    .prepared-by h5 {
        font-weight: normal;
        margin-left: 15px; /* Space between signature and name */
    }

    #signature-upload-title {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 15px;
    }

    .signature-form label {
        display: block;
        margin-bottom: 5px;
        font-size: 14px;
        font-weight: bold;
    }

    .signature-form input[type="file"] {
        margin-bottom: 10px;
    }

    .signature-form button {
        margin-top: 10px;
    }

</style>
<div class="container-fluid">
    <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="main-content" id="main-content">
            <!-- Alert Messages -->
            @include('common.alert')

            <!-- Report Header -->
            <div class="report-header">
                <h1>{{ $reportTitle }}</h1>
                <h5>Report generated on {{ \Carbon\Carbon::now()->format('F j, Y') }}</h5>
            </div>

            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary ms-2 me-2 printButton" onclick="window.print();">
                    <i class="fa-solid fa-print"></i> Print Report
                </button>
            </div>
            <table class="table table-bordered">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                    <h5>Product Details</h5> 
                    {{-- <h5><strong>Report Period:</strong> {{ $startDate }} to {{ $endDate }}</h5> --}}
                </div>
                <thead>
                    <tr>
                        <th>Product No.</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Purchased Price</th>
                        <th>Sale Price</th>
                        <th>UoM</th>
                        <th colspan="3">In Stock</th>
                        <th style="font-size: 0.89em">Reorder Level</th>
                        <th>Date Updated</th>
                        <th>Color</th>
                        <th>Size</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventoryItems as $data)
                        <tr>
                            <td>{{ $data->product_id }}</td>
                            <td>{{ $data->product_name }}</td>
                            <td>{{ $data->category_name }}</td>
                            <td>{{ number_format($data->purchase_price_per_unit, 2) }}</td>
                            <td>{{ number_format($data->sale_price_per_unit, 2) }}</td>
                            <td>{{ $data->unit_of_measure }}</td>
                            <td>{{ $data->in_stock - $data->product_quantity }}</td>
                            <td>{{ $data->product_quantity }}</td>
                            <td>{{ $data->in_stock }}</td>
                            <td>{{ $data->reorder_level }}</td>
                            <td>{{ $data->inventory_updated_at }}</td>
                            <td>{{ $data->descriptionArray['color'] ?? 'N/A' }}</td>
                            <td>{{ $data->descriptionArray['size'] ?? 'N/A' }}</td>
                            <td>{{ $data->descriptionArray['description'] ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="text-center">No data available for the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Stock Tranfer Details Table -->
            <table class="table table-bordered">
                <h5>Stockroom Transfer Details</h5>
                <thead>
                    <tr>
                        <th>Product No.</th>
                        <th>Person In-charge</th>
                        <th>From Stockroom</th>
                        <th>To Stockroom</th>
                        <th>Transfer Quantity</th>
                        <th>Transfer Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockTransferJoined as $data)
                        <tr>
                            <td>{{ $data->product_id }}</td>
                            <td>{{ $data->first_name }} {{ $data->last_name }}</td>
                            <td>{{ $data->from_stockroom_id ? 'Yes' : 'No' }}</td>
                            <td>{{ $data->to_stockroom_id ? 'Yes' : 'No' }}</td>
                            <td>{{ $data->transfer_quantity }}</td>
                            <td>{{ $data->transfer_date }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No data available for the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Stocks and Stockroom Details Table -->
            <table class="table table-bordered">
                <h5>Stock Details</h5>
                <thead>
                    <tr>
                        <th style="border-bottom: 1px solid white"></th>
                        <th colspan="3">In-Stock</th>
                        <th colspan="2">Stockroom</th>
                    </tr>
                    <tr>
                        <th>Product No.</th>
                        <th>In-Store</th>
                        <th>In-Stockroom</th>
                        <th>Qoh</th>
                        <th>Aisle No.</th>
                        <th>Cabinet Level</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventoryItems as $data)
                        <tr>
                            <td>{{ $data->product_id }}</td>
                            <td>{{ $data->in_stock - $data->product_quantity }}</td>
                            <td>{{ $data->product_quantity }}</td>
                            <td>{{ $data->in_stock }}</td>
                            <td>{{ $data->aisle_number }}</td>
                            <td>{{ $data->cabinet_level }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No data available for the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Signature Upload Section -->
            <div id="signature-upload" class="signature-section">
                <form id="signature-form" enctype="multipart/form-data" class="signature-form">
                    <h3 id="signature-upload-title">Upload Signature</h3>
                    <label for="signature">Choose a signature image:</label>
                    <input type="file" name="signature" id="signature" accept="image/*" required>
                    <button type="submit" class="btn btn-primary">Upload Signature</button>
                </form>
            </div>

            <!-- Footer: Prepared By -->
            <div class="prepared-by-container">
                <div id="signature-container">
                    <!-- This will hold the uploaded signature -->
                    <div id="signature-preview">
                        <!-- Signature preview will appear here -->
                    </div>
                </div>

                <div class="prepared-by">
                    <?php 
                        $user = auth()->user();
                    ?>
                    <h5><strong>Prepared By:</strong> {{ $user->first_name }} {{ $user->last_name }}</h5>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    $(document).ready(function() {
        $('#signature-form').on('submit', function(e) {
            e.preventDefault(); // Prevent the form from submitting the traditional way

            // Create a FormData object to hold the file data
            var formData = new FormData(this);

            // Send the form data via AJAX
            $.ajax({
                url: "{{ route('upload.signature') }}",  // Adjust the route as necessary
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status == 'success') {
                        // Display the uploaded signature in a preview
                        $('#signature-preview').html('<img src="' + response.signature_url + '" alt="Signature" style="max-width: 200px;">');
                    } else {
                        alert('Failed to upload signature. Please try again.');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        });
    });
</script>

@endsection
