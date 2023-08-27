<?php
include 'database.php';

	$sql = "SELECT PM_Project_RecID as [Project#],Project_Name, Project_Manager as [Manager], Project_Type as [Type], Project_Status as [Status], Notes, [Estimated_Start_Date], [Estimated_End_Date], [Percent_Complete], [Hours_Actual], Hours_Budget, [Hours_Billable]
          FROM v_rpt_ProjectHeader";


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
		$title = "List Projects";
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
    var url = "phases.php?project_id=" + data['Project#'];
	$('#ticketDetailsFrame').attr('src', url);
	$('#ticketDetailsModalLabel')[0].innerText = 'Project # ' + data['Project#'] + ' | Summary: "' + data['Project_Name'] + '" | Status: "' + data['Status'] + '"'
	$('#ticketDetailsModal').modal('show');
	
  };

</script>
<?php else:
		$title = 'Please select a customer first';
		include 'customers.php';
	endif; ?>
