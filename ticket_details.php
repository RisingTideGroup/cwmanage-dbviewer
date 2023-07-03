<?php
session_start();
require 'database.php';

$ticket_id = $_GET['ticket_id'] ?? null;

if (!$ticket_id) {
    die('No ticket ID provided');
}

try {
    // Fetch ticket data
    $stmt = $conn->prepare("SELECT * FROM v_rpt_Service_Summary WHERE TicketNbr = :id");
    $stmt->execute(['id' => $ticket_id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch time entries for this ticket
    $stmt = $conn->prepare("SELECT * FROM Time_Entry WHERE SR_Service_RecID = :id ORDER BY Last_Update DESC");
    $stmt->execute(['id' => $ticket_id]);
    $time_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	// Fetch notes and email history
	$sql = "SELECT * FROM SR_Detail WHERE SR_Service_RecID = :ticketId ORDER BY Date_Created_UTC DESC";
	$stmt = $conn->prepare($sql);
	$stmt->execute(['ticketId' => $ticket_id]);
	$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Details - <?php echo $ticket['TicketNbr'] ?></title>
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
        .scrollable {
            height: 400px;
            overflow-y: auto;
        }
		
		.info-container {
			margin-top: 10px; /* Adjust this value to your needs */
		}

    </style>
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
			<h5 class="ml-auto">Ticket# <?php echo $ticket['TicketNbr'] ?>| Summary: "<?php echo htmlspecialchars($ticket['Summary']); ?>" Status: "<?php echo htmlspecialchars($ticket['status_description']); ?>"</h5>
        </div>
	</nav>
    <div class="mainBody">
	<div class="container">
        <div class="info-container row">
            <div class="col-sm-6">
                <p><b>Company: </b><?php echo htmlspecialchars($ticket['Company_Name']); ?></p>
                <p><b>Address: </b><?php echo htmlspecialchars($ticket['Address_Line1']); ?></p>
                <p><b>Contact: </b><?php echo htmlspecialchars($ticket['Contact_Name']); ?></p>
                <p><b>Site: </b><?php echo htmlspecialchars($ticket['Site_Name']); ?></p>
                <p><b>City: </b><?php echo htmlspecialchars($ticket['City']); ?></p>
                <p><b>State: </b><?php echo htmlspecialchars($ticket['State']); ?></p>
                <p><b>Postal Code: </b><?php echo htmlspecialchars($ticket['PostalCode']); ?></p>
                <p><b>Urgency: </b><?php echo htmlspecialchars($ticket['Urgency']); ?></p>
                <p><b>Resource List: </b><?php echo htmlspecialchars($ticket['resource_list']); ?></p>
            </div>
            <div class="col-sm-6">
                <p><b>Board: </b><?php echo htmlspecialchars($ticket['Board_Name']); ?></p>
                <p><b>Service Type: </b><?php echo htmlspecialchars($ticket['ServiceType']); ?></p>
                <p><b>Date Entered: </b><?php echo htmlspecialchars($ticket['date_entered']); ?></p>
                <p><b>Date Closed: </b><?php echo htmlspecialchars($ticket['date_closed']); ?></p>
                <p><b>Closed By: </b><?php echo htmlspecialchars($ticket['closed_by']); ?></p>
                <p><b>Actual Hours: </b><?php echo htmlspecialchars($ticket['Hours_Actual']); ?></p>
                <p><b>Source: </b><?php echo htmlspecialchars($ticket['Source']); ?></p>
                <p><b>Team: </b><?php echo htmlspecialchars($ticket['team_name']); ?></p>
            </div><hr>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <h3>Time Entries</h3>
                <div class="scrollable">
                    <?php foreach ($time_entries as $entry): ?>
                        <div class="card">
                            <div class="card-header">
                                Entered By: <?php echo htmlspecialchars($entry['Entered_By']); ?>, Last Updated: <?php echo htmlspecialchars($entry['Last_Update']); ?>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <?php echo nl2br(htmlspecialchars($entry['Notes_Markdown'])); // Convert markdown line breaks to <br> for display ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
			<div class="col-md-6 overflow-auto">
				<h3 class="mt-3">Notes/Email History</h3>
				<div class="scrollable">
				<?php foreach($notes as $note): ?>
					<div class="card mb-3">
						<div class="card-header">
							Created by <?php echo htmlspecialchars($note['Created_By']); ?> on <?php echo htmlspecialchars($note['Date_Created_UTC']); ?>
						</div>
						<div class="card-body">
							<p><?php echo nl2br(htmlspecialchars($note['SR_Detail_Notes_Markdown'])); ?></p>
						</div>
						<div class="card-footer">
							Updated by <?php echo htmlspecialchars($note['Updated_By']); ?> on <?php echo htmlspecialchars($note['Last_Update_UTC']); ?>
						</div>
					</div>
				<?php endforeach; ?>
				</div>
			</div>
        </div>
    </div>
    <!-- Include Bootstrap JavaScript -->
	</div>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
