<?php 
include('db_config.php'); 
session_start(); 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_btn'])) {
    $name = mysqli_real_escape_string($conn, $_POST['userName']);
    $age = mysqli_real_escape_string($conn, $_POST['userAge']);
    $gender = mysqli_real_escape_string($conn, $_POST['userGender']);

    $sql = "INSERT INTO users (fullname, age, gender) VALUES ('$name', '$age', '$gender')";
    
    if (mysqli_query($conn, $sql)) {
   
        $_SESSION['user_name'] = $name;
        $_SESSION['user_age'] = $age;
        $_SESSION['user_gender'] = $gender;
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$isLoggedIn = isset($_SESSION['user_name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NariShakti | Women Campaign Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php if (!$isLoggedIn): ?>
    <div id="login-screen">
        <div class="login-card">
            <h2>User Registration</h2>
            <form method="POST" action="index.php">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="userName" required placeholder="Enter your name">
                </div>
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" name="userAge" required placeholder="Enter age">
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="userGender">
                        <option value="Female">Female</option>
                        <option value="Male">Male</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <button type="submit" name="login_btn" class="btn-login">Enter Dashboard</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($isLoggedIn): ?>
    <div id="dashboard" style="display: block;">
        <nav>
            <div class="logo">NariShakti Portal</div>
            <div class="user-info-nav" id="displayUserInfo">
                User: <?php echo $_SESSION['user_name']; ?>
            </div>
            <ul>
                <li><a onclick="showDashboard()">Home</a></li>
                <li><a onclick="showProfile()">My Profile</a></li>
                <li><a href="index.php?logout=true">Logout</a></li>
            </ul>
        </nav>

        <div id="dashboard-content">
            <section class="hero">
                <h1>Welcome to the Campaign Hub</h1>
                <p>Click on a campaign to view details and join the movement.</p>
            </section>

            <div class="container">
                <h2 class="section-title">Active Campaigns</h2>
                <div class="campaign-grid" id="campaignContainer"></div>
            </div>
        </div>

        <div id="profile-view" class="container" style="display:none;">
            <h2 class="section-title">My Profile</h2>
            <div class="profile-card">
                <h3>Personal Information</h3>
                <p><strong>Name:</strong> <span><?php echo $_SESSION['user_name']; ?></span></p>
                <p><strong>Age:</strong> <span><?php echo $_SESSION['user_age']; ?></span></p>
                <p><strong>Gender:</strong> <span><?php echo $_SESSION['user_gender']; ?></span></p>
                <hr>
                <h3>Enrolled Campaigns</h3>
                <div id="enrolled-list">
                    <p>You haven't joined any campaigns yet.</p>
                </div>
            </div>
        </div>

        <footer>
            <p>&copy; 2024 NariShakti | College Web Lab Project</p>
        </footer>
    </div>
    <?php endif; ?>

    <div id="modal-overlay" onclick="closeModal()">
        <div class="modal" onclick="event.stopPropagation()">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h3 id="modal-title">Campaign Detail</h3>
            <p id="modal-purpose"></p>
            <div id="modal-action-area"></div>
        </div>
    </div>

    <script>
        const loggedInUser = <?php echo json_encode($_SESSION); ?>;
        console.log("PHP Data sent to JS:", loggedInUser);
    </script>

    <script src="script.js"></script>

    <script>
        if (typeof renderCampaigns === 'function') {
            console.log("Manually triggering render...");
            renderCampaigns();
        } else {
            console.error("renderCampaigns function not found! Is script.js linked correctly?");
        }
    </script>
</body>
</html>