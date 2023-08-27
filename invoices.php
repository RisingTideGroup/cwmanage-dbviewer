<?php
include 'database.php';

	$sql = "SELECT TicketNbr as [Ticket#], status_description as [Status], Summary as [Title], Company_Name as [Company], Contact_Name as [User], Board_Name as [Board], agreement_name as [Agreement], date_entered as [Date Created], date_closed as [Date Closed]
FROM v_rpt_Service_Summary";

	if (isset($_GET['companyid'])) {
		// Add a WHERE clause to the SQL query
		$sql .= " WHERE Company_RecID = :companyid";
	}

try {
    $stmt = $conn->prepare($sql);
	
    if (isset($_GET['companyid'])) {
        $stmt->bindParam(':companyid', $_GET['companyid']);
    
	
    $stmt->execute();
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	if ($title === null) {
		$title = "List Tickets";
	}


	include 'grid_view.php';
	}
} catch(PDOException $e) {
    echo "Get Failed: " . $e->getMessage();
}

?>

<?php if (isset($_GET['companyid'])): ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>

<script>
  // Add the onRowClicked event for this specific page
  gridOptions.onRowClicked = function(event) {
    var data = event.data;
    var url = "ticket_details.php?ticket_id=" + data['Ticket#'];
	$('#ticketDetailsFrame').attr('src', url);
	$('#ticketDetailsModalLabel')[0].innerText = 'Ticket # ' + data['Ticket#'] + ' | Summary: "' + data['Title'] + '" | Status: "' + data['Status'] + '"'
	$('#ticketDetailsModal').modal('show');
	
  };

</script>
<?php else:
		$title = 'Please select a customer first';
		include 'customers.php';
	endif; ?>
