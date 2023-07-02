<!DOCTYPE html>
<html>
<head>
    <title>CWM - Database Viewer -<?php echo $title;?></title>
    <!-- Include Bootstrap CSS for styling. You can replace this with your own styles if you want. -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="/index.php">Home</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item <?php if (basename($_SERVER['PHP_SELF']) == 'customers.php') echo 'active'; ?>">
                    <a class="nav-link" href="/customers.php">Customers</a>
                </li>
                <li class="nav-item <?php if (basename($_SERVER['PHP_SELF']) == 'contacts.php') echo 'active'; ?>">
                    <a class="nav-link" href="/contacts.php">Contacts</a>
                </li>
                <li class="nav-item <?php if (basename($_SERVER['PHP_SELF']) == 'tickets.php') echo 'active'; ?>">
                    <a class="nav-link" href="/tickets.php">Tickets</a>
                </li>
				<li class="nav-item ">
                    <a class="btn btn-danger navbar-btn" href="/logout.php">Logout</a>
                </li>
            </ul>
        </div>
	</nav>
    <div class="container my-4">
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
            <table class="table table-striped">
                <thead>
                    <tr>
                        <?php
                        foreach ($result[0] as $key => $value) {
                            echo "<th>$key</th>";
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($result as $row) {
                        echo "<tr>";
                        foreach ($row as $value) {
                            echo "<td>$value</td>";
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <!-- Table ends here -->
        <?php endif; ?>
    </div>
	
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
