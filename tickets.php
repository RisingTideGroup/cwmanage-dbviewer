<?php
include 'database.php';

	$sql = "SELECT TOP(1000) TicketNbr, status_description, Summary, Company_Name, Contact_Name, date_entered, date_closed, resource_list 
FROM v_rpt_Service_Summary";

	if (isset($_GET['companyid'])) {
		// Add a WHERE clause to the SQL query
		$sql .= " WHERE Company_RecID = :companyid";
	}

try {
    $stmt = $conn->prepare($sql);
	
    if (isset($_GET['companyid'])) {
        $stmt->bindParam(':companyid', $_GET['companyid']);
    }
	
    $stmt->execute();
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Get Failed: " . $e->getMessage();
}

$title = "List Tickets";


include 'grid_view.php';
?>

<script>
  // Add the onRowClicked event for this specific page
  gridOptions.onRowClicked = function(event) {
    var data = event.data;
    window.location.href = "ticket_details.php?ticket_id=" + data['TicketNbr'];
  };
</script>

