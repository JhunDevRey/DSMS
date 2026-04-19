<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$access_level = $_SESSION['access_level'];

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
include "connection.php";

// Search functionality
$searchTerm = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$query = !empty($searchTerm)
    ? "SELECT id, firstname, lastname, age, position, year, contact, email, address, hired_at 
       FROM employee 
       WHERE firstname LIKE '%$searchTerm%' OR lastname LIKE '%$searchTerm%'"
    : "SELECT id, firstname, lastname, age, position, year, contact, email, address, hired_at FROM employee";

$result = $conn->query($query);

$userQuery = "SELECT username FROM users";
$userResult = $conn->query($userQuery);

// Flags
$insertSuccess = false;
$updateSuccess = false;
$errorMsg = "";

// Handle Insert (Add Student)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'insert') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $age = $_POST['age'];
    $position = $_POST['position'] ?? '';
    $year = $_POST['year'] ?? '';
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $hired_at = $_POST['hired_at'];

    $stmt = $conn->prepare("INSERT INTO `employee`
        (`firstname`, `lastname`, `age`, `position`, `year`, `contact`, `email`, `address`, `hired_at`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt) {
        $stmt->bind_param("sssssssss", $firstname, $lastname, $age, $position, $year, $contact, $email, $address, $hired_at);
        if ($stmt->execute()) {
            $insertSuccess = true;
        } else {
            $errorMsg = "Insert Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errorMsg = "Prepare Error: " . $conn->error;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $age = $_POST['age'];
    $position = $_POST['position'] ?? '';
    $year = $_POST['year'] ?? '';
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $hired_at = $_POST['hired_at'];
    $id = $_POST['id'];

    $stmt = $conn->prepare("
        UPDATE employee 
        SET firstname=?, lastname=?, age=?, position=?, year=?, contact=?, email=?, address=?, hired_at=?
        WHERE id =?
    ");

    if ($stmt) {
        $stmt->bind_param("ssissssssi", $firstname, $lastname, $age, $position, $year, $contact, $email, $address, $hired_at, $id);
        if ($stmt->execute()) {
            $updateSuccess = true;
        } else {
            $errorMsg = "Update Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errorMsg = "Prepare Error: " . $conn->error;
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/studentslist2.css">
     <link rel="stylesheet" href="css/sidebar.css">
    <!-- <link rel="stylesheet" href="css/studentslist_lo-fi.css"> -->
    <!-- <link rel="stylesheet" href="css/sidebar_lo-fi.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>DSMS - Students List</title>
    
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
        students list
    </span>

    <!-- RIGHT: USER / LOGOUT -->
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" href="#">
            <i class="fa fa-user" id="userIcon"></i>
            Logged in as:
             <?= htmlspecialchars($_SESSION['username']) ?>
        </a>
        <div class="dropdown-menu dropdown-menu-end" id="dropdownMenu">
            <a href="#" class="dropdown-item">Profile</a>
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
    document.getElementById("main-content").classList.toggle("sidebar-collapsed");
}
</script>


<!-------------- TABLE ------------>
    <div id="main-content">

    <!-- CARD -->
    <div class="card table-card">

        <!-- HEADER -->
        <div class="card-header table-header">
            <h5 class="mb-0">Digital Student Management System</h5>

            <div class="header-actions">
                <form method="GET" action="" class="search-form">
                    <input type="text"
                        class="form-control form-control-sm search"
                        name="search"
                        placeholder="Search"
                        value="<?php echo htmlspecialchars($searchTerm); ?>">
                </form>

                <button class="btn btn-primary btn-sm add-btn" type="button" id="addNewEmployee">
                    <i class="fa fa-user-plus"></i> Add New
                </button>
            </div>
        </div>

        <!-- TABLE -->
        <div class="card-body p-0">
            <div class="table-responsive">

                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Program</th>
                            <th>Year</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['firstname']); ?></td>
                                    <td><?= htmlspecialchars($row['lastname']); ?></td>
                                    <td><?= htmlspecialchars($row['position']); ?></td>
                                    <td><?= htmlspecialchars($row['year']); ?></td>

                                    <td class="actions">
                                            <button class="btn-sm btn-info fa fa-eye view_data" data-id="<?= $row['id']; ?>" title="View Employee"></button>

                                            <button type="button" class="fa fa-edit editBtn"
                                                data-id="<?= $row['id']; ?>"
                                                data-firstname="<?= htmlspecialchars($row['firstname']); ?>"
                                                data-lastname="<?= htmlspecialchars($row['lastname']); ?>"
                                                data-age="<?= htmlspecialchars($row['age']); ?>"
                                                data-position="<?= htmlspecialchars($row['position']); ?>"
                                                data-year="<?= htmlspecialchars($row['year']); ?>"
                                                data-contact="<?= htmlspecialchars($row['contact']); ?>"
                                                data-email="<?= htmlspecialchars($row['email']); ?>"
                                                data-address="<?= htmlspecialchars($row['address']); ?>"
                                                data-hired_at="<?= htmlspecialchars($row['hired_at']); ?>">
                                            </button>

                                            <button class="fa fa-trash" onclick="deleteEmployee(<?= $row['id']; ?>)"></button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    No students found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </div>

    </div>
</div>
    <!-------------------------------------- Add New Employee Modal --------------------------------------->

    <div id="addEmployeeModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="addEmployeeForm" method="POST" action="">
                <input type="hidden" name="action" value="insert">
                <h2>Add New Student</h2>
                <div class="form-group">
                    <label for="firstname">First Name:</label>
                    <input class="form-control" type="text" name="firstname" id="firstname" required>

                    <label for="lastname">Last Name:</label>
                    <input class="form-control" type="text" name="lastname" id="lastname" required>

                    <label for="age">Age:</label>
                    <input class="form-control" type="text" name="age" id="age" required>

                    <label for="position">Program:</label>
                    <input class="form-control" type="text" name="position" id="position" required>

                    <label for="year">Year:</label>
                    <input class="form-control" type="text" name="year" id="year" required>

                    <label for="contact">Contact:</label>
                    <input class="form-control" type="text" name="contact" id="contact" required>

                    <label for="email">Email:</label>
                    <input class="form-control" type="email" name="email" id="email" required>

                    <label for="address">Address:</label>
                    <input class="form-control" type="text" name="address" id="address" required>

                    <label for="hired_at">Enrolled:</label>
                    <input class="form-control" type="date" name="hired_at" id="hired_at" required>
                </div>
                <button type="submit" class="save-btn">
                    <i class="fa fa-save"></i> Save
                </button>
            </form>
        </div>
    </div>

    <!-- ✅ JavaScript -->
    <script>
        const addModal = document.getElementById("addEmployeeModal");
        const openBtn = document.getElementById("addNewEmployee");
        const closeBtn = document.querySelector("#addEmployeeModal .close");

        // Show modal with animation
        function openAddModal() {
            addModal.classList.remove("fade-out");
            addModal.classList.add("show");
            addModal.style.display = "flex";
        }

        // Hide modal with fade-out animation
        function closeAddModal() {
            addModal.classList.remove("show");
            addModal.classList.add("fade-out");
            setTimeout(() => {
                addModal.style.display = "none";
                addModal.classList.remove("fade-out");
            }, 300); // Match your CSS animation duration
        }

        openBtn.onclick = openAddModal;
        closeBtn.onclick = closeAddModal;

        // Close when clicking outside
        window.onclick = function(e) {
            if (e.target === addModal) {
                closeAddModal();
            }
        };
    </script>

    <!-- ✅ Success Alert -->
    <?php if ($insertSuccess) : ?>
        <script>
            Swal.fire({
                title: 'Success!',
                text: 'Added successfully!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'studentslist.php';
            });
        </script>
    <?php endif; ?>

    <!----------------------------------- END of Add New Employee Modal ------------------------------------>

    <!---------------------------------------- EDIT Employee Modal ------------------------------------------>

    <!-- Edit Student Modal -->
    <div id="editModal" class="modaal">
        <div class="modaal-content">
            <span class="close-edit">&times;</span>
            <form id="editForm" method="POST" action="">
                <input type="hidden" name="action" value="update">
                <h2>Edit Student Details</h2>
                <div class="form-group-edit">
                    
                    <label for="firstname">First Name:</label>
                    <input class="form-control" type="text" name="firstname" id="edit_firstname" required>
                     
                    <label for="lastname">Last Name:</label>
                    <input class="form-control" type="text" name="lastname" id="edit_lastname" required>

                    <label for="age">Age:</label>
                    <input class="form-control" type="text" name="age" id="edit_age" required>

                    <label for="position">Program:</label>
                    <input class="form-control" type="text" name="position" id="edit_position" required>

                    <label for="year">Year:</label>
                    <input class="form-control" type="text" name="year" id="edit_year" required>

                    <label for="contact">Contact:</label>
                    <input class="form-control" type="text" name="contact" id="edit_contact" required>

                    <label for="email">Email:</label>
                    <input class="form-control" type="email" name="email" id="edit_email" required>

                    <label for="address">Address:</label>
                    <input class="form-control" type="text" name="address" id="edit_address" required>

                    <label for="hired_at">Date Enrolled:</label>
                    <input class="form-control" type="date" name="hired_at" id="edit_hired_at" required>

                    <!-- Hidden field for user ID -->
                    <input type="hidden" name="id" id="edit_id">

                    <button type="submit" class="save-btn">
                        <i class="fa fa-save"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const editModal = document.getElementById("editModal");
        const editForm = document.getElementById("editForm");
        const closeEdit = document.querySelector("#editModal .close-edit");

        // Show modal with animation
        function openEditModal() {
            editModal.classList.remove("fade-out");
            editModal.classList.add("show");
            editModal.style.display = "flex";
        }

        // Hide modal with fade-out animation
        function closeEditModal() {
            editModal.classList.remove("show");
            editModal.classList.add("fade-out");
            setTimeout(() => {
                editModal.style.display = "none";
                editModal.classList.remove("fade-out");
            }, 300); // Match your CSS animation duration
        }

        // Attach edit button click events
        document.addEventListener("click", function (e) {
        if (e.target.classList.contains("editBtn")) {

            const btn = e.target;

            document.getElementById("edit_id").value = btn.dataset.id;
            document.getElementById("edit_firstname").value = btn.dataset.firstname;
            document.getElementById("edit_lastname").value = btn.dataset.lastname;
            document.getElementById("edit_age").value = btn.dataset.age;
            document.getElementById("edit_position").value = btn.dataset.position;
            document.getElementById("edit_year").value = btn.dataset.year;
            document.getElementById("edit_contact").value = btn.dataset.contact;
            document.getElementById("edit_email").value = btn.dataset.email;
            document.getElementById("edit_address").value = btn.dataset.address;
            document.getElementById("edit_hired_at").value = btn.dataset.hired_at;

            // document.getElementById("editModal").style.display = "flex";
            openEditModal();
        }
        });

        // Close modal when clicking the "X"
        closeEdit.addEventListener("click", closeEditModal);

        // Close when clicking outside
        window.onclick = function(e) {
            if (e.target === editModal) {
                editModal.classList.remove("open");
            }
        };
    </script>

    <!-- ✅ Success Alert -->
    <?php if ($updateSuccess) : ?>
        <script>
            Swal.fire({
                title: 'Success!',
                text: 'Updated successfully!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'studentslist.php';
            });
        </script>
    <?php endif; ?>

    <!------------------------------------- END of EDIT Employee Modal -------------------------------------->

    <!------------------------------------------ VIEW EMPOLYEE DETAILS---------------------------------->
<div id="viewEmployeeModal" class="yate">
    <div class="yate-inner">

        <span class="close-btn" id="closeView">&times;</span>

        <h2>Student Details</h2>

        <div class="profile-wrapper">

            <!-- LEFT: AVATAR -->
            <div class="profile-avatar">
                <img src="https://ui-avatars.com/api/?name=Employee&background=3c8dbc&color=fff&size=128"
                     id="v_avatar"
                     alt="Avatar">
            </div>

            

        </div>
        <!-- DOWN: INFO -->
            <div class="info-box">
                <div><strong>Name:</strong> <span id="v_name"></span></div>
                <div><strong>Age:</strong> <span id="v_age"></span></div>
                <div><strong>Program:</strong> <span id="v_position"></span></div>
                <div><strong>Contact:</strong> <span id="v_contact"></span></div>
                <div><strong>Email:</strong> <span id="v_email"></span></div>
                <div><strong>Address:</strong> <span id="v_address"></span></div>
                <div><strong>Date Enrolled:</strong> <span id="v_hired_at"></span></div>
            </div>

    </div>
</div>

    <script>
        const viewModal = document.getElementById("viewEmployeeModal");
        const closeView = document.getElementById("closeView");

        // Open modal when clicking eye button
        document.addEventListener("click", function(e) {
            const btn = e.target.closest(".view_data");
            if (btn) {
                const id = btn.getAttribute("data-id");

                fetch("getStudentDetails.php?id=" + id)

                fetch("getStudentDetails.php?id=" + id)
                .then(response => response.json())
                .then(data => {

                    if (data.error) {
                        Swal.fire("Error", data.error, "error");
                        return;
                    }
                    // Populate modal
                    document.getElementById("v_name").innerText =
                        data.firstname + " " + data.lastname;
                    document.getElementById("v_age").innerText = data.age;
                    document.getElementById("v_position").innerText = data.position;
                    document.getElementById("v_contact").innerText = data.contact;
                    document.getElementById("v_email").innerText = data.email;
                    document.getElementById("v_address").innerText = data.address;
                    document.getElementById("v_hired_at").innerText = data.hired_at;
                    
                    // ✅ ADD THIS (AVATAR)
                    document.getElementById("v_avatar").src =
                        "https://ui-avatars.com/api/?name=" +
                        encodeURIComponent(data.firstname + " " + data.lastname) +
                        "&background=3c8dbc&color=fff&size=128";

                    // Show modal
                    viewModal.classList.add("open");
                })
                .catch(() => {
                    Swal.fire("Error", "Failed to fetch employee data", "error");
                });
            }
        });

        // Close modal (X button)
        closeView.onclick = () => viewModal.classList.remove("open");

        // Close when clicking outside
        window.onclick = function(e) {
            if (e.target === viewModal) {
                viewModal.classList.remove("open");
            }
        };
</script>
<!-------------------------------------END OF VIEW EMPOLYEE DETAILS---------------------------------->

<!-- script for sidebar -->
    <script>
        function openSidebar() {
            document.getElementById("sidebar").style.left = "0";
            document.getElementById("main-content").classList.add("open-sidebar");
        }

        function closeSidebar() {
            document.getElementById("sidebar").style.left = "-260px";
            document.getElementById("main-content").classList.remove("open-sidebar");
        }

    //---------------------LOUTOUT SCRIPT----------------------   
//         document.getElementById('logoutBtn').addEventListener('click', function (e) {
//     e.preventDefault();

//     fetch('logout.php', {
//         method: 'POST'
//     })
//     .then(res => res.json())
//     .then(data => {
//         if (data.success) {
//             Swal.fire('Logged Out!', 'You have been successfully logged out.', 'success')
//             .then(() => {
//                 window.location.href = 'login.php';
//             });
//         } else {
//             Swal.fire('Error!', 'There was an error logging you out.', 'error');
//         }
//     })
//     .catch(() => {
//         Swal.fire('Error!', 'There was an error with your request.', 'error');
//     });
// });
document.addEventListener("DOMContentLoaded", function () {
    const logoutBtn = document.getElementById('logoutBtn');

    if (logoutBtn) {
        logoutBtn.addEventListener('click', function (e) {
            e.preventDefault();

            fetch('logout.php', {
                method: 'POST'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Logged Out!', 'You have been successfully logged out.', 'success')
                    .then(() => {
                        window.location.href = 'login.php';
                    });
                } else {
                    Swal.fire('Error!', 'Logout failed.', 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error!', 'There was an error with your request.', 'error');
            });
        });
    }
});

// ---------------- Edit Student script


//----------------- Delete Student script
        const accessLevel = "<?= $_SESSION['access_level']; ?>";
        function deleteEmployee(id) {
            if (accessLevel === 'admin') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you really want to delete this student?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('deletestudent.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    id
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Deleted!', 'Student has been deleted.', 'success').then(() => window.location.reload());
                                } else {
                                    Swal.fire('Error!', data.error || 'There was an error deleting the student.', 'error');
                                }
                            })
                            .catch(() => Swal.fire('Error!', 'There was an error with your request.', 'error'));
                    }
                });
            } else {
                Swal.fire('Access Denied', 'Contact the Administrator!', 'error');
            }
        }
    </script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</body>
<?php $conn->close(); ?>

</html>