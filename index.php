<?php
session_start();

$serverName = $_SESSION['hostname'] ?? null;
$database = $_SESSION['dbname'] ?? null;
$username = $_SESSION['username'] ?? null;
$password = $_SESSION['password'] ?? null;

$isDbConnected = false;
if ($serverName && $database && $username && $password) {
    try {
        $conn = new PDO("sqlsrv:server=$serverName;Database=$database", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $isDbConnected = true;
    } catch(PDOException $e) {
        // Connection failed, show form
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {

            background: #f8f9fa;
        }
		.mainBody {
			display: flex;
			justify-content: center;
			align-items: center;
			height: 100vh;
		}
        .card {
            margin: 20px;
        }
        .title {
            text-align: center;
            margin-bottom: 50px;
        }
    </style>
</head>
<body>
	<?php if ($isDbConnected): ?>
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
	<?php endif; ?>
	<div class="mainBody">
    <div class="container">
        <h1 class="title">ConnectWise Manage Database Archive Viewer</h1>
        <?php if ($isDbConnected): ?>
            <div class="row">
                <div class="col-sm-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Clients</h5>
                            <p class="card-text">View  clients</p>
                            <a href="customers.php" class="btn btn-primary">Go to Clients</a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Contacts</h5>
                            <p class="card-text">View  contacts</p>
                            <a href="contacts.php" class="btn btn-primary">Go to Contacts</a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tickets</h5>
                            <p class="card-text">View  tickets</p>
                            <a href="tickets.php" class="btn btn-primary">Go to Tickets</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <form method="POST" action="connect.php">
                <div class="form-group">
                    <label for="hostname">Hostname</label>
                    <input type="text" class="form-control" id="hostname" name="hostname">
                </div>
                <div class="form-group">
                    <label for="dbname">Database Name</label>
                    <input type="text" class="form-control" id="dbname" name="dbname">
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <button type="submit" class="btn btn-primary">Connect</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
	</div>
</body>
</html>
