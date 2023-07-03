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
		
		$stmt = $conn->query("Select Message FROM System_Table where Description = 'display_version'");
        $dbVersion = $stmt->fetchColumn();
        $isDbConnected = true;
    } catch(PDOException $e) {
        // Connection failed, show form
    }
}

// Load settings from file, if it exists
if (file_exists('settings.php')) {
    $savedSettings = include('settings.php');
} else {
    $savedSettings = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_settings'])) {
        // Get settings from POST request
        $newSettings = [
            'hostname' => $_POST['hostname'],
            'dbname' => $_POST['dbname'],
            'username' => $_POST['username'],
            'password' => $_POST['password']
        ];

        // Add new settings to the saved settings
        $savedSettings[] = $newSettings;

        // Save to file
        $file = fopen('settings.php', 'w');
        fwrite($file, '<?php return ' . var_export($savedSettings, true) . ';');
        fclose($file);
    }

    if (isset($_POST['saved_settings'])) {
        $index = $_POST['saved_settings'];
		echo $index;
		if ($index == 'newServer') {
			$_SESSION['hostname'] = $_POST['hostname'];
			$_SESSION['dbname'] = $_POST['dbname'];
			$_SESSION['username'] = $_POST['username'];
			$_SESSION['password'] = $_POST['password'];
			header("Location: /");
		}
        elseif (isset($savedSettings[$index])) {
            $_SESSION['hostname'] = $savedSettings[$index]['hostname'];
            $_SESSION['dbname'] = $savedSettings[$index]['dbname'];
            $_SESSION['username'] = $savedSettings[$index]['username'];
            $_SESSION['password'] = $savedSettings[$index]['password'];
			header("Location: /");
        }
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
        .mainBody {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f8f9fa;
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
			<h6 class="status alert alert-success" role="alert">
				Connected to SQL Server: <?= $serverName ?>, Database: <?= $database ?>, Version: <?= $dbVersion ?>
			</h6>
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
                                <h5 class="card-title">Tickets</h5>
                                <p class="card-text">View  tickets</p>
                                <a href="tickets.php" class="btn btn-primary">Go to Tickets</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <form method="POST" action="index.php">
                    <div class="form-group">
                        <label for="saved_settings">Server and Database Settings</label>
                        <select class="form-control" name="saved_settings" onchange="handleSelection()">
                            <option value="">Select a server option...</option>
							<option value="newServer">Other Server</option>
                            <?php foreach ($savedSettings as $index => $settings): ?>
                                <option value="<?= $index ?>"><?= $settings['hostname'] ?> - <?= $settings['dbname'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
					<div class="newServerSettings">
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
					
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="save_settings" name="save_settings">
                        <label class="form-check-label" for="save_settings">Save settings for future use</label>
                    </div>
					</div>
                    <button type="submit" class="btn btn-primary">Connect</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<script>
	        $(document).ready(function() {
				$('.newServerSettings').hide();
				$('select[name="saved_settings"]').on('change', function() {
					if ($('select[name="saved_settings"]').val() == 'newServer') {
						$('.newServerSettings').show();
					} else { $('.newServerSettings').hide(); }
				});
			});
	</script>
</body>
</html>
