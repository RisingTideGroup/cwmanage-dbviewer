<?php
include 'database.php';

if (isset($_GET['companyid'])) {

	try {
		$sql = "SELECT [Contact_RecID]
      ,[First_Name]
      ,[Last_Name]
      ,[Title]
      ,[Contact_Type_Desc]
      ,[Default_Phone]
      ,[Default_Email]
      ,[Unsubscribe_Flag]
      ,[Inactive_Flag]
      ,[Image_Link]
      ,[Updated_By]
      ,[Last_Update]
      ,[Time_Zone]
      ,[Territory]
      ,[Department]
      ,[Delete_Flag]
		FROM [v_rpt_Contact] where [Company_RecID] = :companyid";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':companyid', $_GET['companyid']);
		$stmt->execute();
		
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	} catch(PDOException $e) {
		echo "Get Failed: " . $e->getMessage();
	}

		if ($title === null) {
			$title = "List Users";
		}
		
	include 'grid_view.php';
}
?>

<?php if (isset($_GET['companyid'])): ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>

<script>
  // Add the onRowClicked event for this specific page
  gridOptions.onRowClicked = function(event) {
	var companyId = <?php echo $_GET['companyid'] ?>;
    var data = event.data;
		window.location.href = "tickets.php?companyid=" + companyId + '&userid=' + data['Contact_RecID'];
		//window.location.search = "companyid=" + data['Company_RecID'];

  };
</script>
<?php else:
		$title = 'Please select a customer first';
		include 'customers.php';
	endif; ?>

