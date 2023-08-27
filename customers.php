<?php
include 'database.php';

if (isset($_GET['companyid'])) {
		include 'index.php';
}
else {

	try {
		$sql = "SELECT c.Company_RecID, c.Company_Name as [Name], c.PhoneNbr as [Phone#], c.Last_Update as [Last Updated], cs.Description as [Status], c.Date_Entered as [Date Added]
				FROM Company c
			JOIN Company_Status cs on c.Company_Status_RecID = cs.Company_Status_RecID
				WHERE Delete_Flag = 0";
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	} catch(PDOException $e) {
		echo "Get Failed: " . $e->getMessage();
	}

		if ($title === null) {
			$title = "List Customers";
		}
		
	include 'grid_view.php';
}
?>

<script>
  // Add the onRowClicked event for this specific page
  gridOptions.onRowClicked = function(event) {
    var data = event.data;
		//window.location.href = "tickets.php?companyid=" + data['Company_RecID'];
		window.location.search = "companyid=" + data['Company_RecID'];

  };
</script>
