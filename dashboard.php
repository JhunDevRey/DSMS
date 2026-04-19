<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$access_level = $_SESSION['access_level'];


include "connection.php";

// Safer error handling 
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Search
$searchTerm = $_GET['search'] ?? '';

if (!empty($searchTerm)) {
    $stmt = $conn->prepare("
        SELECT id, firstname, lastname, position, contact, email, address, hired_at 
        FROM employee 
        WHERE firstname LIKE ? OR lastname LIKE ?
    ");
    $search = "%$searchTerm%";
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM employee");
}

// Stats
$totalEmployees = $conn->query("SELECT COUNT(*) as total FROM employee")
    ->fetch_assoc()['total'];

// Handle POST
$insertSuccess = false;
$updateSuccess = false;
$errorMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname'] ?? '');
    $position  = trim($_POST['position'] ?? '');
    $contact   = trim($_POST['contact'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $address   = trim($_POST['address'] ?? '');
    $hired_at  = $_POST['hired_at'] ?? '';

    if ($action === 'insert') {

        $stmt = $conn->prepare("
            INSERT INTO employee 
            (firstname, lastname, position, contact, email, address, hired_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("sssssss", $firstname, $lastname, $position, $contact, $email, $address, $hired_at);

        if ($stmt->execute()) {
            $insertSuccess = true;
        } else {
            $errorMsg = $stmt->error;
        }

        $stmt->close();
    }

    if ($action === 'update') {

        $id = $_POST['id'];

        $stmt = $conn->prepare("
            UPDATE employee 
            SET firstname=?, lastname=?, position=?, contact=?, email=?, address=?, hired_at=? 
            WHERE id=?
        ");

        $stmt->bind_param("sssssssi", $firstname, $lastname, $position, $contact, $email, $address, $hired_at, $id);

        if ($stmt->execute()) {
            $updateSuccess = true;
        } else {
            $errorMsg = $stmt->error;
        }

        $stmt->close();
    }
}

// Chart Data
$chartData = [];

$resultTotal = $conn->query("SELECT COUNT(*) as total FROM employee");
$totalEmployees = $resultTotal ? $resultTotal->fetch_assoc()['total'] : 0;

$query = $conn->query("
    SELECT YEAR(hired_at) as year, MONTH(hired_at) as month, COUNT(*) as total 
    FROM employee 
    GROUP BY YEAR(hired_at), MONTH(hired_at)
");


while ($row = $query->fetch_assoc()) {
    $chartData[$row['year']][$row['month']] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="css/dashboard.css">
<link rel="stylesheet" href="css/sidebar.css">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<title>DSMS - Dashboard</title>
</head>

<body>
    <!------------------- NAVBAR -------------------->
    <nav class="navbar navbar-expand navbar-white navbar-light custom-navbar">

    <!-- LEFT: MENU BUTTON -->
    <!-- <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" href="#" onclick="openSidebar()">
                <i class="fa fa-bars"></i>
            </a>
        </li>
    </ul> -->

    <!-- CENTER: TITLE -->
    <span class="navbar-brand mx-auto fw-bolder text-uppercase" style="color: #343a40; letter-spacing: 2px;">
        dashboard
    </span>

    <!-- RIGHT: USER / LOGOUT -->
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" href="#">
            <i class="fa fa-user"></i>
            Logged in as:
             <?= htmlspecialchars($_SESSION['username']) ?>
        </a>
        <div class="dropdown-menu dropdown-menu-end" id="dropdownMenu">
            <a href="#" class="dropdown-item">Profile</a>
            <!-- <a href="#" class="dropdown-item" id="logoutBtn">Logout</a> -->
        </div>
    </li>

    <script>
        const dropdownToggle = document.getElementById("userDropdown");
        const dropdownMenu = document.getElementById("dropdownMenu");

        // Toggle dropdown
        dropdownToggle.addEventListener("click", function(e) {
            e.preventDefault();
            dropdownMenu.classList.toggle("show");
        });

        // Close when clicking outside
        document.addEventListener("click", function(e) {
            if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove("show");
            }
        });
    </script>
</nav>

    <!------------------- SIDEBAR -------------------->
<div class="sidebar" id="sidebar">

    <a href="#" class="close-btn" onclick="toggleSidebar()">
    <i class="fa fa-bars"></i>
</a>
<br><br><br><br><br>

    <a href="dashboard.php">
        <i class="fa fa-home"></i>
        <span>Dashboard</span>
    </a>

    <a href="studentslist.php" class="active">
        <i class="fa fa-users"></i>
        <span>Students</span>
    </a>

    <a href="userslist.php">
        <i class="fa fa-user"></i>
        <span>Users</span>
    </a><br><br><br><br><br>

    <a id="logoutBtn"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>


</div>

<script>
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("collapsed");
    document.getElementById("main-content2").classList.toggle("sidebar-collapsed");
}
</script>

<!-- MAIN CONTENT -->
 
<div id="main-content2" class="container mt-4">

    <!-- <h2 class="mb-3"><i class="fa fa-chart-line"></i>Dashboard</h2> -->

    <!-- STATS -->
     
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="custom-card1 shadow p-3 text-center">
                <i class="fa fa-users fa-2x mb-2 text-danger"></i>
                <h6>Total Students</h6>
                <h2><?= $totalEmployees ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="custom-card2 shadow p-3 text-center">
                <i class="fa fa-user-plus fa-2x mb-2 text-success"></i>
                <h6>New Students This Year</h6>
                <h2><?= $chartData[date('Y')][date('n')] ?? 0 ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="custom-card3 shadow p-3 text-center">
                <i class="fa fa-calendar-alt fa-2x mb-2 text-info"></i>
                <h6>New Students This Month</h6>
                <h2><?= $chartData[date('Y')][date('n')] ?? 0 ?></h2>
            </div>
        </div>
    </div>

    <!-- CHART -->
    
    <div class="chart-card">
        <div class="custom-card shadow-lg p-4 rounded-4 chart-card">
                <h5><i class="fa fa-chart-line"></i> Student Statistics</h5>
        <canvas id="studentChart"></canvas>
        </div>
    </div>
    
</div>

<script>
    function openSidebar() {
            document.getElementById("sidebar").style.left = "0";
            document.getElementById("main-content2").classList.add("open-sidebar");
        }

        function closeSidebar() {
            document.getElementById("sidebar").style.left = "-260px";
            document.getElementById("main-content2").classList.remove("open-sidebar");
        }
        document.getElementById('logoutBtn').addEventListener('click', function() {
            fetch('logout.php', {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Logged Out!', 'You have been successfully logged out.', 'success').then(() => {
                            window.location.href = 'login.php';
                        });
                    } else {
                        Swal.fire('Error!', 'There was an error logging you out.', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error!', 'There was an error with your request.', 'error');
                });
        });

const rawData = <?php echo json_encode($chartData); ?>;
const months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
const colors = [
    "#4CAF50", "#2196F3", "#FF9800", "#9C27B0", "#E91E63"
];

const datasets = [];
let colorIndex = 0;
Object.keys(rawData).forEach(year => {
    const data = [];
    for (let m = 1; m <= 12; m++) {
        data.push(rawData[year][m] ?? 0);
    }

    datasets.push({
        label: year,
        data: data,
        borderColor: colors[colorIndex % colors.length],
        backgroundColor: colors[colorIndex % colors.length],
        borderWidth: 2,
        tension: 0.4,
        pointRadius: 4,
        fill: false
    });

    colorIndex++;
});

new Chart(document.getElementById("studentChart"), {
    type: "line",
    data: {
        labels: months,
        datasets: datasets
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: "top" }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});
</script>

</body>
</html>