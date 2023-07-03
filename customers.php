<?php
include 'database.php';

try {
    $sql = "SELECT c.Company_RecID, c.Company_Name, c.PhoneNbr, c.Last_Update, cs.Description, c.Date_Entered
            FROM Company c
	    JOIN Company_Status cs on c.Company_Status_RecID = cs.Company_Status_RecID
            WHERE Delete_Flag = 0";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Get Failed: " . $e->getMessage();
}

$title = "List Customers";
//$columns = ['c.Company_RecID', 'c.Company_Name', 'c.PhoneNbr', 'c.Last_Update', 'cs.Description', 'c.Date_Entered'];

include 'grid_view.php';
?>

<script>
  // Add the onRowClicked event for this specific page
  gridOptions.onRowClicked = function(event) {
    var data = event.data;
    window.location.href = "tickets.php?companyid=" + data['Company_RecID'];
  };
</script>
