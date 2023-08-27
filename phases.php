<?php
include 'database.php';

	$sql1 = "SELECT TOP 1 PM_Project_RecID as [Project #],Project_Name, Project_Manager as [Manager], Project_Type as [Type], Project_Status as [Status], Notes, [Estimated_Start_Date], [Estimated_End_Date], [Percent_Complete], [Hours_Actual], Hours_Budget, [Hours_Billable]
          FROM v_rpt_ProjectHeader where [PM_Project_RecID] = :projectno";


	$sql2 = "SELECT [ProjectManager]
				  ,[Phase]
				  ,[TicketNbr]
				  ,[Location]
				  ,[Status_Description]
				  ,[date_closed]
				  ,[closed_by]
				  ,[Type_RecID]
				  ,[ServiceType]
				  ,[Summary]
				  ,[Date_Required]
				  ,[hours_budget]
				  ,[Hours_Scheduled]
				  ,[Hours_Billable]
				  ,[Hours_NonBillable]
				  ,[Hours_Invoiced]
				  ,[Priority]
				  ,[ProjectCloseDate]
				  ,[Resource_List]
				  ,[ServiceSubType]
				  ,[ServiceSubTypeItem]
				  ,[SR_Service_RecID]
			FROM v_rpt_Project
			WHERE [PM_Project_RecID] = :projectid";



try {
	$stmt1 = $conn->prepare($sql1);
    $stmt2 = $conn->prepare($sql2);
	
    if (isset($_GET['project_id'])) {
		$stmt1->bindParam(':projectno', $_GET['project_id']);
        $stmt2->bindParam(':projectid', $_GET['project_id']);
    
	
    $stmt1->execute();
	$stmt2->execute();
    
	$project = $stmt1->fetchAll(PDO::FETCH_ASSOC);
    $result = $stmt2->fetchAll(PDO::FETCH_ASSOC);
	
	if ($title === null) {
		$title = "List Phases and Tickets";
	}


	include 'grid_view.php';
	}
} catch(PDOException $e) {
    echo "Get Failed: " . $e->getMessage();
}

?>

<?php if (isset($_GET['project_id'])): ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>

<script>
  // Add the onRowClicked event for this specific page
  gridOptions.onRowClicked = function(event) {
    var data = event.data;
    var url = "ticket_details.php?ticket_id=" + data['SR_Service_RecID'];
	$('#ticketDetailsFrame').attr('src', url);
	$('#ticketDetailsModalLabel')[0].innerText = 'Task # ' + data['SR_Service_RecID'] + ' | Summary: "' + data['Summary'] + '" | Status: "' + data['Status_Description'] + '"'
	$('#ticketDetailsModal').modal('show');
	
  };
</script>
<?php else:
		$title = 'Please select a project first';
		include 'projects.php';
	endif; ?>
