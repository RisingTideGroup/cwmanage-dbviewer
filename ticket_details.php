<?php
session_start();
require 'database.php';

$ticket_id = $_GET['ticket_id'] ?? null;

if (!$ticket_id) {
    die('No ticket ID provided');
}

try {
    // Fetch ticket data
	$sql = "SELECT * FROM v_rpt_Service_Summary WHERE TicketNbr = :id";
    $stmt = $conn->prepare($sql);
	$stmt->bindValue(':id', $ticket_id);
	$stmt->execute();
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch time entries for this ticket
	$sql = "SELECT * FROM Time_Entry WHERE SR_Service_RecID = :id and (TE_Problem_Flag = 0 AND TE_InternalAnalysis_Flag = 0 AND TE_Resolution_Flag = 0) ORDER BY Last_Update DESC";
	$stmt = $conn->prepare($sql);
	$stmt->bindValue(':id', $ticket_id);
	$stmt->execute();
    $time_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	// Fetch notes and email history
	$sql = "SELECT * FROM SR_Detail srd
	WHERE srd.SR_Service_RecID = :ticketId ORDER BY Date_Created_UTC DESC";
	$notesSql = "SELECT sr.SR_Detail_RecID AS RecID,
	   sr.SR_Service_RecID,
	   sr.SR_Detail_Notes_Markdown AS Notes,
	   sr.Problem_Flag AS Detail_Description_Flag,
	   sr.InternalAnalysis_Flag AS Internal_Analysis_Flag,
	   sr.Resolution_Flag,
	   sr.Member_RecID,
	   sr.Contact_RecID,
	   0 AS Time_Flag,
	   sr.Date_Created_UTC,
	   sr.Created_By,
	   sr.Last_Update_UTC,
	   sr.Updated_By
FROM dbo.SR_Detail sr 
where sr.SR_Service_RecID = :ticketId
UNION ALL
SELECT te.Time_RecID AS RecID,
	   te.SR_Service_RecID,
	   te.Notes_Markdown as Notes,
	   te.TE_Problem_Flag AS Detail_Description_Flag,
	   te.TE_InternalAnalysis_Flag AS Internal_Analysis_Flag,
	   te.TE_Resolution_Flag AS Resolution_Flag,
	   te.Member_RecID,
	   te.Contact_RecID,
	   1 AS Time_Flag,
	   te.Date_Entered_UTC AS Date_Created_UTC,
	   te.Entered_By AS Created_By,
	   te.Last_Update_UTC,
	   te.Updated_By
FROM dbo.Time_Entry te
WHERE (te.TE_Problem_Flag = 1 OR te.TE_InternalAnalysis_Flag = 1 OR te.TE_Resolution_Flag = 1)
and te.SR_Service_RecID = :tickId
ORDER BY Date_Created_UTC DESC";
	$stmt = $conn->prepare($notesSql);
	$stmt->bindValue(':ticketId', $ticket_id);
	$stmt->bindValue(':tickId', $ticket_id);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$stmt->execute();
	$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Database error: " . print_r($stmt->errorInfo()));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Details - <?php echo $ticket['TicketNbr'] ?></title>
    <!-- Include Bootstrap CSS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/showdown/2.1.0/showdown.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
	
		html, body {
			min-height: 100%;
			height: 100%;
		}
		
        .mainBody {
            display: flex;
            justify-content: center;
            align-items: center;
			min-height: 100%;
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
		
		.info-container p {
		    line-height: 0.5;  /* Adjust line-height as needed */
		}
		
		.tab-content {
		  display: none;
		}
		.active {
		  display: block;
		}		

    </style>
</head>
<body>
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
<ul class="nav nav-tabs">
	<li class="nav-item"><button class="nav-link active" onclick="openTab(event, 'notesEmails')">Notes/Email History</button></li>
    <li class="nav-item"><button class="nav-link" onclick="openTab(event, 'timeEntries')">Time Entries</button></li>
  </ul>
        <div class="row">
            <div id="timeEntries" class="tab-content overflow-auto">
                <div class="scrollable">
                    <?php foreach ($time_entries as $entry): ?>
                        <div class="card">
                            <div class="card-header">
                                Entered By: <?php echo htmlspecialchars($entry['Entered_By']); ?>, Last Updated: <?php echo htmlspecialchars($entry['Last_Update']); ?>
                            </div>
						<div class="card-body time-<?php echo $entry['Time_RecID']; ?>">
							
							<script>
								var markdown = <?php echo json_encode($entry['Notes_Markdown']); ?>;
								var converter = new showdown.Converter();
								var html = converter.makeHtml(markdown);
								var cardBody = document.querySelector('.time-<?php echo $entry['Time_RecID']; ?>');
								cardBody.innerHTML = html;
							</script>
						</div>							
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
			<div id="notesEmails" class="tab-content overflow-auto active">
				<div class="scrollable">
				<?php foreach($notes as $note): ?>
					<div class="card mb-3">
						<div class="card-header">
							Entered by <?php echo htmlspecialchars($note['Created_By']); ?>, Last Updated <?php echo htmlspecialchars($note['Last_Update_UTC']); ?>
						</div>
						<div class="card-body notes-<?php echo $note['RecID']; ?>">			
							<script>
							document.addEventListener('DOMContentLoaded', function() {
								var markdown = <?php echo json_encode($note['Notes']); ?>;
								var converter = new showdown.Converter();
								var html = converter.makeHtml(markdown);
								var cardBody = document.querySelector('.notes-<?php echo $note['RecID']; ?>');
								cardBody.innerHTML = html;
							});
							</script>
						</div>
					</div>
				<?php endforeach; ?>
				</div>
			</div>
        </div>
    </div>
    <!-- Include Bootstrap JavaScript -->
	</div>
	<script>
	    function openTab(evt, tabName) {
      // Get all tab contents and hide them
      var tabContents = document.getElementsByClassName("tab-content");
      for (var i = 0; i < tabContents.length; i++) {
        tabContents[i].style.display = "none";
      }
      
      // Remove "active" class from all tabs
      var tabs = document.getElementsByClassName("nav-link");
      for (var i = 0; i < tabs.length; i++) {
        tabs[i].classList.remove("active");
      }
      
      // Show the selected tab content
      document.getElementById(tabName).style.display = "block";
      
      // Add "active" class to the clicked tab
      evt.currentTarget.classList.add("active");
    }
	</script>
	<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
	
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>
