<?php 
include('db_config.php'); 
session_start(); 

$error = "";

// LOGOUT LOGIC
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// REGISTRATION LOGIC
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_btn'])) {
    $name = mysqli_real_escape_string($conn, $_POST['userName']);
    $age = mysqli_real_escape_string($conn, $_POST['userAge']);
    $gender = mysqli_real_escape_string($conn, $_POST['userGender']);
    $pass = password_hash($_POST['userPassword'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (fullname, age, gender, password) VALUES ('$name', '$age', '$gender', '$pass')";
    
    if (mysqli_query($conn, $sql)) {
        $_SESSION['user_name'] = $name;
        $_SESSION['user_age'] = $age;
        $_SESSION['user_gender'] = $gender;
        header("Location: index.php");
        exit();
    } else {
        $error = "Registration failed!";
    }
}

// LOGIN LOGIC
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_btn'])) {
    $name = mysqli_real_escape_string($conn, $_POST['loginName']);
    $pass = $_POST['loginPassword'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE fullname='$name'");
    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($pass, $user['password'])) {
            $_SESSION['user_name'] = $user['fullname'];
            $_SESSION['user_age'] = $user['age'];
            $_SESSION['user_gender'] = $user['gender'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "User not found!";
    }
}

$isLoggedIn = isset($_SESSION['user_name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NariShakti | Secure Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php if (!$isLoggedIn): ?>
    <div id="login-screen">
        <div class="login-card">
            <h2>NariShakti Portal</h2>
            <?php if($error): ?> <p style="color: #e91e63; font-weight: bold;"><?php echo $error; ?></p> <?php endif; ?>
            
            <div style="margin-bottom: 20px; display: flex; gap: 10px;">
                <button onclick="toggleAuth('login')" class="btn-support" style="margin-top:0; flex:1;">Login</button>
                <button onclick="toggleAuth('reg')" class="btn-support" style="margin-top:0; background: #9c27b0; flex:1;">Register</button>
            </div>

            <form id="login-form" method="POST" action="index.php">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="loginName" required placeholder="Enter name">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="loginPassword" required placeholder="Enter password">
                </div>
                <button type="submit" name="login_btn" class="btn-login">Login</button>
            </form>

            <form id="register-form" method="POST" action="index.php" style="display:none;">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="userName" required>
                </div>
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" name="userAge" required>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="userGender">
                        <option value="Female">Female</option>
                        <option value="Male">Male</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Create Password</label>
                    <input type="password" name="userPassword" required>
                </div>
                <button type="submit" name="register_btn" class="btn-login">Register</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($isLoggedIn): ?>
    <div id="dashboard">
        <nav>
            <div class="logo">NariShakti</div>
            <div class="user-info-nav">User: <?php echo $_SESSION['user_name']; ?></div>
            <ul>
                <li><a onclick="showDashboard()">Home</a></li>
                <li><a onclick="showProfile()">Profile</a></li>
                <li><a href="index.php?logout=true">Logout</a></li>
            </ul>
        </nav>

        <div id="dashboard-content">
            <section class="hero">
                <h1>Welcome, <?php echo $_SESSION['user_name']; ?></h1>
                <p>Empowering every voice, one campaign at a time.</p>
            </section>
            <div class="container">
                <h2 class="section-title">Active Campaigns</h2>
                <div class="campaign-grid" id="campaignContainer"></div>
            </div>
        </div>

        <div id="profile-view" class="container" style="display:none;">
            <div class="profile-card">
                <h2>My Profile</h2>
                <p><strong>Name:</strong> <span id="prof-name"><?php echo $_SESSION['user_name']; ?></span></p>
                <p><strong>Age:</strong> <span id="prof-age"><?php echo $_SESSION['user_age']; ?></span></p>
                <p><strong>Gender:</strong> <span id="prof-gender"><?php echo $_SESSION['user_gender']; ?></span></p>
                <hr>
                <h3>Joined Campaigns</h3>
                <div id="enrolled-list">Loading...</div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div id="modal-overlay" onclick="closeModal()">
        <div class="modal" onclick="event.stopPropagation()">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h3 id="modal-title" style="color: var(--secondary-color); margin-bottom: 20px;"></h3>
            <div id="modal-purpose" style="margin-bottom: 20px;"></div> 
            <div id="modal-action-area"></div>
        </div>
    </div>

    <script>
        function toggleAuth(mode) {
            document.getElementById('login-form').style.display = (mode === 'login') ? 'block' : 'none';
            document.getElementById('register-form').style.display = (mode === 'reg') ? 'block' : 'none';
        }
        const loggedInUser = <?php echo json_encode($_SESSION); ?>;
    </script>
    <script src="script.js"></script>
</body>
</html>
