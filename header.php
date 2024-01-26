<!-- Navbar Start -->
<div class="container-fluid nav-bar bg-transparent">
    <nav class="navbar navbar-expand-lg bg-white navbar-light py-0 px-4">
        <a  class="navbar-brand d-flex align-items-center text-center">
            <div class="icon p-2 me-2">
                <img class="img-fluid" src="img/tv.png" alt="Icon" style="width: 30px; height: 30px;">
            </div>
            <h1 class="m-0 text-primary">ระบบแจ้งซ่อม</h1>
        </a>
        <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto">
                <a href="index.php" class="nav-item nav-link ">หน้าแรก</a>
                <?php if (!isset($_SESSION['p_id']) || $_SESSION['p_id'] == 0) { ?>
                    <a href="login.php" class="nav-item nav-link active">เข้าสู่ระบบ</a>
                <?php } ?>

                <?php if (isset($_SESSION['p_id']) && $_SESSION['p_id'] > 0) { ?>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">บริการ</a>
                        <div class="dropdown-menu rounded-0 m-0">
                            <a href="service.php" class="dropdown-item">แจ้งซ่อม</a>
                            <a href="service1.php" class="dropdown-item">ดูประวัติแจ้งซ่อม</a>
                        </div>
                    </div>
            
                    <a href="logout.php" class="nav-item nav-link active">ออกจากระบบ</a>
                <?php } ?>
                
            </div>
        </div>
    </nav>
</div>
<!-- Navbar End -->
