<?php
@session_start();

include 'class/class.scdb.php';

$query = new SCDB();

// Redirect to login page if session variables are not set
if ((!isset($_SESSION['USER_NO'])) || ($_SESSION['USER_NO'] == '')) {
    header("location: login.php");
    exit();
}

try {
    if (!$query->connect()) {
        throw new Exception("Database connection error: " . $query->getError());
    }

    // Fetch all maintenance requests where m_status is 2
    $sqlAppointments = "SELECT m.m_id, d.du_name, m.m_date_S, m.m_time, m.m_status 
                        FROM tb_du_maint m
                        JOIN tb_durable d ON m.du_id = d.du_id
                        WHERE m.m_status = 2";

    $stmtAppointmentHistory = $query->prepare($sqlAppointments);
    $stmtAppointmentHistory->execute();

} catch (Exception $e) {
    // Log or handle the exception appropriately
    die("An error occurred: " . $e->getMessage());
}
?>



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


<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<title>ระบบแจ้งซ่อมครุภัณฑ์ออนไลน์</title>
    <!-- Head content remains unchanged -->

    <!-- Custom CSS for Form Styling -->
    <style>
        /* Your existing styles remain unchanged */
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
                                        <h4 style="padding-bottom: 20px;text-align: center;color: #5c6bc0 ;">รายการแจ้งซ่อมกำลังดำเนินการ</h4>
                                        <div>
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
                                                       <td><a href="admin-detail.php?m_id=<?php echo $rowAppointmentHistory['m_id']; ?>" class="btn btn-primary">View</a>
                                                    </td>

                                                </tr>
                                                <?php
                                                    $cnt = $cnt + 1;
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
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
