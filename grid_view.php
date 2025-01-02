<!DOCTYPE html>
<html>
<head>
    <title>CWM - Database Viewer -<?php echo $title;?></title>
    <!-- Include Bootstrap CSS for styling. You can replace this with your own styles if you want. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <!-- Include the JS for AG Grid -->
    <script src="https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.js"></script>
<style>
    .nav-item.active {
        background-color: #007BFF;  /* Change as per your needs */
    }
    .nav-item.active .nav-link {
        color: white;
    }
</style>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		var links = document.querySelectorAll('.custom-link');

		links.forEach(function(link) {
			link.addEventListener('click', function(event) {
				// Prevent the default link behavior
				event.preventDefault();

				// Get the base URL from the href attribute
				var baseUrl = event.target.getAttribute('href');

				// Get the current query parameters
				var queryParams = new URLSearchParams(window.location.search);

				// Combine the base URL and query parameters
				var newUrl = baseUrl + "?" + queryParams.toString();

				// Navigate to the new URL
				window.location.href = newUrl;
			});
		});
	});

</script>


  </head>
<body>
<?php if ($title != 'List Phases and Tickets'): ?>
<nav class="navbar navbar-expand-lg navbar-light bg-primary">
	<div class="container-fluid">
    <a class="navbar-brand px-3 text-light" href="/index.php">Home</a>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item <?php if (basename($_SERVER['PHP_SELF']) == 'customers.php') echo 'active'; ?>">
                <a class="nav-link text-light" href="/customers.php">Customers</a>
            </li>
	       <li class="nav-item <?php if (basename($_SERVER['PHP_SELF']) == 'users.php') echo 'active'; ?>">
                <a class="custom-link nav-link text-light" href="/users.php">Users</a>
            </li>
            <li class="nav-item <?php if (basename($_SERVER['PHP_SELF']) == 'tickets.php') echo 'active'; ?>">
                <a class="custom-link nav-link text-light" href="/tickets.php">Tickets</a>
            </li>
            <li class="nav-item <?php if (basename($_SERVER['PHP_SELF']) == 'projects.php') echo 'active'; ?>">
                <a class="custom-link nav-link text-light" href="/projects.php">Projects</a>
            </li>
		</ul>
		<ul class="navbar-nav ms-auto d-flex align-items-center">
		        <li class="nav-item me-3">
					<a class="btn btn-warning" href="#" data-bs-toggle="modal" data-bs-target="#ticketModal">Launch Ticket#</a>
				</li>
				<li class="nav-item">
					<form class="d-flex my-2 my-lg-0 me-3">
					<input class="form-control mr-sm-2" type="search" placeholder="Filter..." aria-label="Filter" oninput="onFilterTextBoxChanged()" id="filter-text-box">
					</form>
				</li>
                <li class="nav-item">
                    <a class="btn btn-danger" href="/logout.php">Logout</a>
                </li>
        </ul>
    </div>
	</div>
</nav>
<?php endif; ?>
    <div class="container-fluid my-4">
        <h1 class="mb-4">
            <?php
            echo $title;
            ?>
        </h1>
        <!-- Check if there's any error -->
        <?php if (isset($e)): ?>
            <div class="alert alert-danger" role="alert">
                Error: <?= $e->getMessage() ?>
            </div>
        <!-- If no error, Check if there are any results -->
        <?php elseif (!$result): ?>
            <div class="alert alert-info" role="alert">
                No results found.
            </div>
        <!-- If no error and there are results, display the table -->
        <?php else: ?>
            <!-- Table starts here -->
			<div id="myGrid" style="height: calc(100vh - 200px); width:100%; box-sizing: border-box;" class="ag-theme-alpine"></div>
            <!-- Table ends here -->
        <?php endif; ?>
    </div>
	
	<!-- Modal for entering ticket number -->
	<div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="ticketModalLabel">Launch Ticket by ID</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		  </div>
		  <div class="modal-body">
			  <div class="mb-3">
				<label for="ticketNumber" class="form-label">Ticket Number:</label>
				<input type="text" class="form-control" id="ticketNumber" name="ticket_id" placeholder="Enter ticket number" required>
			  </div>
			  <button type="button" class="close btn btn-primary" data-bs-dismiss="modal" onClick="launchTicketDetails()">Open Ticket</button>
		  </div>
		</div>
	  </div>
	</div>
	
	<style>
 .modal-lg {
  max-width: 80vw !important;
  height: 95vh;
}

 .modal-content {
  height: 100%;
}

#ticketDetailsFrame {
  width: 100%;
  height: 100%;
}
</style>

<div class="modal fade" id="ticketDetailsModal" tabindex="-1" aria-labelledby="ticketDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ticketDetailsModalLabel">Ticket Details</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <iframe id="ticketDetailsFrame" src="" width="100%" height="500px"></iframe>
      </div>
    </div>
  </div>
</div>
		
<script src="https://code.jquery.com/jquery-3.7.0.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script>
    // Specify column definitions for AG Grid
    var columnDefs = [
        <?php 
        foreach ($result[0] as $key => $value) {
            echo "{headerName: '$key', field: '$key', filter: true},";
        }
        ?>
    ];

    // Specify row data for AG Grid
    let rowData = <?php echo json_encode($result, JSON_HEX_TAG); ?>;
    let gridApi;


    // specify the detail cell renderer
    var detailCellRenderer = function (params) {
        // function to take a list of names and return as comma separated string
        function namesFormatter(names) {
            return names.map(function (name) {
                return name.first + ' ' + name.last;
            }).join(', ');
        }

        // function to take a list of addresses and return as comma separated string
        function addressFormatter(addresses) {
            return addresses.map(function (address) {
                return address.street + ', ' + address.city + ', ' + address.state;
            }).join(', ');
        }

        // supplying return to ensure we return something to the cell renderer
        return '';
    };

    // let the grid know which columns and what data to use
    var gridOptions = {
        columnDefs: columnDefs,
        rowData: rowData,
        defaultColDef: {
            flex: 1,
            minWidth: 200,
            resizable: true,
        },
        detailCellRenderer: detailCellRenderer,
		pagination: true,
		rowSelection: { mode: 'multiRow' },
        onFirstDataRendered: function (params) {
            params.api.sizeColumnsToFit();
        }
    };

    // setup the grid after the page has finished loading 
    document.addEventListener('DOMContentLoaded', function() {
        var gridDiv = document.querySelector('#myGrid');
        gridApi = agGrid.createGrid(gridDiv, gridOptions);
    });
	
	function onFilterTextBoxChanged() {
		gridApi.setGridOption('quickFilterText', (document.getElementById('filter-text-box').value));
	}

	function launchTicketDetails() {
		var ticketId = document.getElementById('ticketNumber').value;
		var url = "ticket_details.php?ticket_id=" + ticketId;
		$('#ticketDetailsFrame').attr('src', url);
		//$('#ticketDetailsModalLabel')[0].innerText = 'Ticket # ' + data['TicketNbr'] + ' | Summary: "' + data['Summary'] + '" | Status: "' + data['status_description'] + '"'
		$('#ticketDetailsModal').modal('show');

	};
	
	

</script>
</body>
</html>
