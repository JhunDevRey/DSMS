<?php
session_start();

if ($_SESSION['access_level'] === 'user') {
    header("Location: studentslist.php");
    exit;
}

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$access_level = $_SESSION['access_level'];

include "connection.php";

$query = "SELECT `id`, `username`, `password`, `access_level` FROM users";
$result = mysqli_query($conn, $query);

// Search functionality
$searchTerm = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$query = !empty($searchTerm)
    ? "SELECT id, username, password, access_level FROM users 
       WHERE username LIKE '%$searchTerm%'"
    : "SELECT id, username, password, access_level FROM users";

$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/userslist.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>DSMS - Users List</title>
</head>

<body>
    <!------------------- NAVBAR -------------------->
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
        users list
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
    document.getElementById("main-content").classList.toggle("sidebar-collapsed");
}
</script>
<!------------------------------- USERS LIST TABLE ---------------------------------->
    <div class="content-wrapper p-3" id="main-content">
        <div class="container-fluid">

            <!-- HEADER -->
            <div class="card-header table-header">
                <h5 class="mb-0">Digital User Management System</h5>

                <div class="d-flex gap-2">
                    <form method="GET" action="" class="search-form">
                    <input type="text"
                        class="form-control form-control-sm search"
                        name="search"
                        placeholder="Search"
                        value="<?php echo htmlspecialchars($searchTerm); ?>">
                </form>
                    
                    <button class="btn btn-primary btn-sm" id="addUserBtn">
                        <i class="fas fa-user-plus"></i> Add New
                    </button>
                </div>
            </div>

            <!-- CARD -->
            <div class="card shadow-sm rounded-3">

                <div class="card-body table-responsive p-0">

                    <table class="table table-hover text-nowrap align-middle mb-0 users-table">

                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Access Level</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['username']); ?></td>

                                        <td><?= htmlspecialchars($row['password']); ?></td>

                                        <td>
                                            <span class="badge <?= $row['access_level'] === 'admin' ? 'badge-admin' : 'badge-user'; ?>"> <?= htmlspecialchars($row['access_level']); ?>
                                            </span>
                                        </td>

                                        <td class="text-center">
                                            <!-- EDIT -->
                                            <button class="btn btn-primary btn-sm"
                                                onclick="editUser(
                                                    <?= $row['id']; ?>,
                                                    '<?= htmlspecialchars($row['username'], ENT_QUOTES); ?>',
                                                    '<?= htmlspecialchars($row['password'], ENT_QUOTES); ?>',
                                                    '<?= htmlspecialchars($row['access_level'], ENT_QUOTES); ?>'
                                                )">
                                                <i class="fas fa-pen"></i>
                                            </button>

                                            <!-- DELETE -->
                                            <button class="btn btn-danger btn-sm"
                                                onclick="deleteUser(<?= $row['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>

                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        No users found
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>

                    </table>

                </div>
            </div>

        </div>
    </div>
<!---------------------------------- For Back Home ---------------------------------------->
<script>
document.getElementById('backHome').addEventListener('click', function() {
            window.location.href = 'dashboard.php';
        });
</script>
<!--------------------------------- Add User Modal ------------------------------------------>
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Create User</h2>
            <form id="signupForm">
                <label for="newUsername">Username:</label><br>
                <input type="text" id="newUsername" name="username" class="modal-input" required><br>
                <label for="newPassword">Password:</label><br>
                <input type="text" id="newPassword" name="password" class="modal-input" required><br>
                <label for="newAccessLevel">Access Level:</label><br>
                <input type="text" id="newAccessLevel" name="access_level" class="modal-input" required><br><br>
                <button class="save-btn" type="submit">Save</button>
            </form>
        </div>
    </div>

    <script>
        const addModal = document.getElementById("addModal");
        const addBtn = document.getElementById("addUserBtn");
        const addClose = document.querySelector("#addModal .close");

        // Open
        function openAddModal() {
            addModal.classList.remove("fade-out");
            addModal.classList.add("show");
            addModal.style.display = "flex";
        }

        // Close
        function closeAddModal() {
            addModal.classList.remove("show");
            addModal.classList.add("fade-out");
            setTimeout(() => {
                addModal.style.display = "none";
                addModal.classList.remove("fade-out");
            }, 300);
        }

        // Events
        addBtn.onclick = openAddModal;
        addClose.onclick = closeAddModal;

        // Click outside
        window.addEventListener("click", function(e) {
            if (e.target === addModal) {
                closeAddModal();
            }
        });
        
        document.getElementById('signupForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
            fetch('signup.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'User added successfully!',
                        icon: 'success'
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error!', 'Failed to add user.', 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error!', 'Request failed.', 'error');
            });
        });
    </script>

    <!--------------------------------- Edit User Modal ----------------------------------------->

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-edit">&times;</span>
            <h2>Edit User</h2>
            <form id="editForm">
                <input type="hidden" name="id" id="editUserId">
                <label for="editUsername">Username:</label><br>
                <input type="text" name="username" id="editUsername" required><br>
                <label for="editPassword">Password:</label><br>
                <input type="text" name="password" id="editPassword" required><br>
                <label for="editAccessLevel">Access Level:</label><br>
                <input type="text" name="access_level" id="editAccessLevel" required><br><br>
                <button class="save-btn" type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        const editModal = document.getElementById("editModal");
        const editClose = document.querySelector("#editModal .close-edit");

        function openEditModal() {
            editModal.classList.remove("fade-out");
            editModal.classList.add("show");
            editModal.style.display = "flex";
        }

        function closeEditModal() {
            editModal.classList.remove("show");
            editModal.classList.add("fade-out");
            setTimeout(() => {
                editModal.style.display = "none";
                editModal.classList.remove("fade-out");
            }, 300);
        }

        function editUser(id, username, password, access_level) {
            document.getElementById("editUserId").value = id;
            document.getElementById("editUsername").value = username;
            document.getElementById("editPassword").value = password;
            document.getElementById("editAccessLevel").value = access_level;

            openEditModal();
        }

        // Close
        editClose.onclick = closeEditModal;

        // Click outside
        window.addEventListener("click", function(e) {
            if (e.target === editModal) {
                closeEditModal();
            }
        });

        document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
            fetch('edituser.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Updated!',
                        text: 'User updated successfully!',
                        icon: 'success'
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error!', 'Update failed.', 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error!', 'Request failed.', 'error');
            });
        });
    </script>
    
    <script>
        function deleteUser(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you really want to delete this user?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`deleteuser.php?id=${id}`, {
                            method: 'POST'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'User has been deleted.',
                                    icon: 'success'
                                }).then(function() {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'There was an error deleting the user.',
                                    icon: 'error'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: 'There was an error with your request.',
                                icon: 'error'
                            });
                        });
                }
            });
        }

        // -------------------------SIDE BAR SCRIPT----------------------------
        function openSidebar() {
            document.getElementById("sidebar").style.left = "0";
            document.getElementById("main-content").classList.add("open-sidebar");
        }

        function closeSidebar() {
            document.getElementById("sidebar").style.left = "-260px";
            document.getElementById("main-content").classList.remove("open-sidebar");
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
    </script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
     <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css"> -->
     <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</body>

<?php
mysqli_close($conn);
?>

</html>