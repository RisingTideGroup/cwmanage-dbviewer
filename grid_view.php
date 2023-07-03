<!DOCTYPE html>
<html>
<head>
    <title>CWM - Database Viewer -<?php echo $title;?></title>
    <!-- Include Bootstrap CSS for styling. You can replace this with your own styles if you want. -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- Include the JS for AG Grid -->
    <script src="https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.noStyle.js"></script>
    <!-- Include the core CSS, this is needed by the grid -->
    <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/ag-grid-community/styles/ag-grid.css"/>
    <!-- Include the theme CSS, only need to import the theme you are going to use -->
    <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/ag-grid-community/styles/ag-theme-alpine.css"/>
  </head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="/index.php">Home</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item <?php if (basename($_SERVER['PHP_SELF']) == 'customers.php') echo 'active'; ?>">
                    <a class="nav-link" href="/customers.php">Customers</a>
                </li>
                <li class="nav-item <?php if (basename($_SERVER['PHP_SELF']) == 'tickets.php') echo 'active'; ?>">
                    <a class="nav-link" href="/tickets.php">Tickets</a>
                </li>
				<li class="nav-item ">
                    <a class="btn btn-danger navbar-btn" href="/logout.php">Logout</a>
                </li>
            </ul>
			<form class="form-inline ml-auto my-2 my-lg-0">
				<input class="form-control mr-sm-2" type="search" placeholder="Filter..." aria-label="Filter" oninput="onFilterTextBoxChanged()" id="filter-text-box">
			</form>
        </div>
	</nav>
    <div class="container-fluid my-4">
        <h1 class="mb-4">
            <?php
            echo $title;
			echo $route;
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
	
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
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
        onFirstDataRendered: function (params) {
            params.api.sizeColumnsToFit();
        }
    };

    // setup the grid after the page has finished loading 
    document.addEventListener('DOMContentLoaded', function() {
        var gridDiv = document.querySelector('#myGrid');
        new agGrid.Grid(gridDiv, gridOptions);
    });
	
	function onFilterTextBoxChanged() {
		gridOptions.api.setQuickFilter(document.getElementById('filter-text-box').value);
	}

</script>
</body>
</html>
