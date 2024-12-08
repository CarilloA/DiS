@extends('layouts.app')

<!-- Wrap navbar in a div with a specific class for print -->
<div class="navbar-print-hide">
    @include('common.navbar')
</div>

@section('content')
<style>
    /* Styling for printing */
@media print {
    .navbar-print-hide {
        display: none; /* Hide the navbar during printing */
    }

    body {
        background-image: none; /* Remove background image in print */
        font-size: 10pt; /* Ensure font size is appropriate for the report */
        margin: 0;
        padding: 0;
        line-height: 1.6; /* Improve line spacing for readability */
    }

    .table {
        width: 100%;
        font-size: 9pt;
        border-collapse: collapse;
    }

    .table th, .table td {
        padding: 6px;
        text-align: left;
        border: 1px solid #dee2e6;
    }

    .table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    /* Container for the main content */
    .container-fluid {
        max-width: 100%;
        margin: 0;
        padding: 0;
    }

    #main-content {
        background-color: transparent !important;
        color: #000 !important;
        box-shadow: none !important;
        padding: 40px 30px;
        margin: 0;
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
        margin: 1in;
    } */
}

}


    /* Styling for report title and sections in normal view */
    .container-fluid,
    .main-content {
        margin-top: 0;
        padding-top: 0;
    }

    #main-content {
        background-color: #fff; /* White background for normal view */
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .table td,
    .table th {
        text-align: left; /* Align text to the left for better readability */
    }

    .table {
        margin-top: 30px;
        width: 100%;
        border: 1px solid #dee2e6;
        font-size: 10pt;
        border-radius: 5px;
        overflow: hidden;
    }

    .table-bordered td,
    .table-bordered th {
        padding: 8px;
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

            <!-- Print Button -->
            <div class="printButton">
                <button type="button" onclick="window.print();">
                    <i class="fa-solid fa-print"></i> Print Report
                </button>
            </div>

            <!-- Table for audit logs -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Audit No.</th>
                        <th>Auditor</th>
                        <th>Product Name</th>
                        <th>Previous Store Stock</th>
                        <th>Previous Stockroom Stock</th>
                        <th>Previous QoH</th>
                        <th>New Store Stock</th>
                        <th>New Stockroom Stock</th>
                        <th>New QoH</th>
                        <th>Store Stock Discrepancy</th>
                        <th>Stockroom Stock Discrepancy</th>
                        <th>QoH Discrepancy</th>
                        <th>Discrepancy Reason</th>
                        <th>Audit Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($auditLogs as $log)
                        <tr>
                            <td>{{ $log->audit_id }}</td>
                            <td>{{ $log->user->first_name }} {{ $log->user->last_name }}</td>
                            <td>{{ $log->inventory->product->product_name }}</td>
                            <td>{{ $log->previous_store_quantity }}</td>
                            <td>{{ $log->previous_stockroom_quantity }}</td>
                            <td>{{ $log->previous_quantity_on_hand }}</td>
                            <td>{{ $log->new_store_quantity }}</td>
                            <td>{{ $log->new_stockroom_quantity }}</td>
                            <td>{{ $log->new_quantity_on_hand }}</td>
                            <td>{{ $log->store_stock_discrepancy }}</td>
                            <td>{{ $log->stockroom_stock_discrepancy }}</td>
                            <td>{{ $log->in_stock_discrepancy }}</td>
                            <td>{{ $log->discrepancy_reason }}</td>
                            <td>{{ \Carbon\Carbon::parse($log->audit_date)->format('F j, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="text-center">No audit logs found for the selected filters.</td>
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
