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

    // Fetch data for appointment history
    $u_id = $_SESSION['p_id'];
    $sqlAppointmentHistory = "SELECT m.m_id, m.m_date_S, m.m_time, m.m_status, d.du_name FROM tb_du_maint m
        JOIN tb_durable d ON m.du_id = d.du_id
        WHERE m.p_id = :p_id";
    $stmtAppointmentHistory = $query->prepare($sqlAppointmentHistory);
    $stmtAppointmentHistory->bindParam(':p_id', $u_id, PDO::PARAM_INT);
    $stmtAppointmentHistory->execute();
} catch (Exception $e) {
    // Log or handle the exception appropriately
    die("An error occurred: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Head content remains unchanged -->

    <!-- Custom CSS for Form Styling -->
    <style>
        .form-label {
            font-weight: bold;
        }

        .form-select,
        .form-control {
            border-radius: 20px;
            margin-bottom: 15px;
        }

        .blue-small-label {
            font-size: 12px;
            color: #1a237e;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .float-end {
            float: right;
        }

        /* Table Styles */
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            background-color: #fff;
            border-collapse: collapse;
            border: 1px solid rgba(0, 0, 0, 0.125);
        }

        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        }

        .table th {
            background-color: #d0d9ff;
        }

        .gray-bg {
            background-color: #f8f9fa;
        }

        /* Button Style */
        .btn-primary {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 20px;
            padding: 8px 16px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

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
                                    <div class="mb-5" style="margin-top: 50px;">
                                        <h4 style="padding-bottom: 20px;text-align: center;color: #5c6bc0 ;">รายการแจ้งซ่อม</h4>
                                        </div>
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
                                                        <td>
                                                            <?php
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

    <!-- Footer Section -->
    <?php include_once('footer.php'); ?>

    <!-- JavaScript Dependencies -->
    <script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/custom.js"></script>

</body>

</html>