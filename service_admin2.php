<?php
@session_start();

include 'class/class.scdb.php';

$query = new SCDB();

// Redirect to login page if session variables are not set
if ((!isset($_SESSION['USER_NO'])) || ($_SESSION['USER_NO'] == '')) {
    header("location: login.php");
    exit();
}

// Set the number of records per page
$recordsPerPage = 30;

// Get the current page or set it to 1 if not set
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// Calculate the starting record for the current page
$startFrom = ($page - 1) * $recordsPerPage;



// Modify the SQL query to include the search condition and pagination
$sqlAppointments = "SELECT m.m_id, d.du_name, m.m_date_S, m.m_time, m.m_status
                    FROM tb_du_maint m
                    JOIN tb_durable d ON m.du_id = d.du_id
                    WHERE m.m_status = 1 
                    ORDER BY m.m_id DESC
                    OFFSET $startFrom ROWS
                    FETCH NEXT $recordsPerPage ROWS ONLY";
$stmtAppointmentHistory = $query->prepare($sqlAppointments);

// Bind the search term parameter if it exists
if (!empty($searchTerm)) {
    $stmtAppointmentHistory->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
}

$stmtAppointmentHistory->execute();

// Get the total number of records for pagination
$totalRecordsQuery = "SELECT COUNT(*) as total FROM tb_du_maint WHERE m_status = 1 ";
$totalRecordsStmt = $query->prepare($totalRecordsQuery);

// Bind the search term parameter if it exists
if (!empty($searchTerm)) {
    $totalRecordsStmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
}

$totalRecordsStmt->execute();
$totalRecords = $totalRecordsStmt->fetch(PDO::FETCH_ASSOC)['total'];

// Calculate the total number of pages
$totalPages = ceil($totalRecords / $recordsPerPage);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        body {
            height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .wrapper {
            flex: 1;
        }
    </style>
</head>

<body class="sub_page">
    <div class="wrapper">
        <div class="hero_area">
            <!-- Header Section -->
            <?php include_once('header_admin.php'); ?>
            <!-- End Header Section -->
        </div>

        <!-- About Section -->
        <section class="about_section">
            <div class="container">
                <div class="row">
                    <section class="w3l-contact-info-main col-md-12" id="contact">
                        <div class="contact-sec">
                            <div class="container">
                                <div>
                                    <div class="cont-details">
                                        <div class="table-content table-responsive cart-table-content m-t-30">
                                            <div style="padding-top: 30px;">
                                                <h4 style="padding-bottom: 20px;text-align: center;color: #5c6bc0 ;">รายการแจ้งซ่อมที่เรียบร้อยแล้ว</h4>
                                                <div>


                                                    <div style="padding-top: 30px;">
                                                        <table border="2" class="table">
                                                            <thead class="gray-bg">
                                                                <tr>
                                                                    <th>ลำดับ</th>
                                                                    <th>รหัสการแจ้งซ่อม</th>
                                                                    <th>ชื่อครุภัณฑ์</th>
                                                                    <th>วันที่แจ้งซ่อม</th>
                                                                    <th>เวลาแจ้งซ่อม</th>
                                                                    <th>สถานะการซ่อม</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $cnt = 1;
                                                                while ($rowAppointmentHistory = $stmtAppointmentHistory->fetch(PDO::FETCH_ASSOC)) {
                                                                ?>
                                                                    <tr>
                                                                        <td><?php echo $cnt; ?></td>
                                                                        <td><?php echo $rowAppointmentHistory['m_id']; ?></td>
                                                                        <td><?php echo $rowAppointmentHistory['du_name']; ?></td>
                                                                        <td><p><?php echo $rowAppointmentHistory['m_date_S']; ?></p></td>
                                                                        <td><?php echo $rowAppointmentHistory['m_time']; ?></td>
                                                                        <td><?php
                                                                            $status = $rowAppointmentHistory['m_status'];
                                                                            if ($status == '') {
                                                                                echo "รอยืนยัน";
                                                                            } elseif ($status == '2') {
                                                                                echo "กำลังดำเนินการ";
                                                                            } elseif ($status == '1') {
                                                                                echo "เรียบร้อยแล้ว";
                                                                            }
                                                                            ?></td>
                                                                        <td><a href="admin-detail1.php?m_id=<?php echo $rowAppointmentHistory['m_id']; ?>" class="btn btn-primary">View</a></td>
                                                                    </tr>
                                                                <?php
                                                                    $cnt = $cnt + 1;
                                                                } ?>
                                                                <?php
                                                                if ($cnt === 1) { // No records found
                                                                    echo '<tr><td colspan="8" style="text-align: center;">ไม่พบรายการที่ค้นหา</td></tr>';
                                                                }
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <!-- Display pagination links -->
                <div style="padding-top: 10px;">
    <ul class="pagination">
        <?php
        if ($page > 1) {
            echo '<li class="page-item"><a class="page-link" href="?page=1">First</a></li>';
            $prevPage = $page - 1;
            echo '<li class="page-item"><a class="page-link" href="?page=' . $prevPage . '">Previous</a></li>';
        }

        for ($i = 1; $i <= $totalPages; $i++) {
            echo '<li class="page-item"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
        }

        if ($page < $totalPages) {
            $nextPage = $page + 1;
            echo '<li class="page-item"><a class="page-link" href="?page=' . $nextPage . '">Next</a></li>';
            echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '">Last</a></li>';
        }
        ?>
    </ul>
</div>

            </div>
        </section>
        <!-- End About Section -->

        <!-- JavaScript Dependencies -->
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script>
            function confirmAction(maintenanceId) {
                // Making an AJAX request to update m_status
                $.ajax({
                    type: "POST",
                    url: "update_status.php", // Replace with the actual file that handles the update
                    data: {
                        m_id: maintenanceId,
                        new_status: 2 // Set the new status value
                    },
                    success: function (response) {
                        // Handle the response if needed
                        alert("Status updated successfully!");
                    },
                    error: function (error) {
                        console.error("Error updating status: " + error);
                    }
                });
            }
        </script>
        <script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script type="text/javascript" src="js/custom.js"></script>
    </div>
</body>

<!-- Footer Section -->
<?php include_once('footer.php'); ?>

</html>
