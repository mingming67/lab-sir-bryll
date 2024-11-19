<?php
// Database connection
$host = 'localhost'; // Database host
$db = 'db_university'; // Database name
$user = 'root'; // Database username
$pass = ''; // Database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submission for adding or editing a department
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle nullable fields by assigning NULL if empty
    $dept_name = $_POST['dept_name'];
    $building = !empty($_POST['building']) ? $_POST['building'] : null;
    $budget = !empty($_POST['budget']) ? $_POST['budget'] : null;

    if (isset($_POST['original_dept_name']) && $_POST['original_dept_name'] != '') {
        // Update existing department
        $stmt = $pdo->prepare("UPDATE department SET dept_name=?, building=?, budget=? WHERE dept_name=?");
        $stmt->execute([$dept_name, $building, $budget, $_POST['original_dept_name']]);
    } else {
        // Add new department
        $stmt = $pdo->prepare("INSERT INTO department (dept_name, building, budget) VALUES (?, ?, ?)");
        $stmt->execute([$dept_name, $building, $budget]);
    }
    // Redirect back to the same page after form submission
    header("Location: department.php");
    exit();
}

// Handle deletion of a department
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM department WHERE dept_name = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: department.php");
    exit();
}

// Fetch all departments
$stmt = $pdo->query("SELECT * FROM department");
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all buildings from the classroom table (ensure your classroom table has a 'building' column)
$stmt = $pdo->query("SELECT DISTINCT building FROM classroom"); // Changed to classroom table
$buildings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if a department is being edited
$departmentToEdit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM department WHERE dept_name = ?");
    $stmt->execute([$_GET['edit']]);
    $departmentToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Department</title>
    <style>
        /* General Body Styles */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f1f1f1; /* Light background to contrast with red/blue */
    margin: 0;
    padding: 0;
    color: #333;
}

/* Container Layout */
.container {
    display: flex;
    height: 100vh;
}

/* Sidebar Styles */
.sidebar {
    width: 250px;
    background-color: #003366; /* Dark blue for the Captain America theme */
    color: #fff;
    padding: 20px 10px;
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
    border-radius: 0 10px 10px 0;
}

.sidebar h2 {
    text-align: center;
    font-size: 1.5rem;
    margin-bottom: 20px;
    letter-spacing: 2px;
    color: #fff;
}

.sidebar ul {
    list-style: none;
    padding: 0;
}

.sidebar ul li {
    margin-bottom: 15px;
}

.sidebar ul li a {
    display: block;
    text-decoration: none;
    color: #fff;
    padding: 12px;
    border-radius: 6px;
    background-color: #0057b8; /* Bright blue for links */
    transition: background-color 0.3s ease-in-out;
}

.sidebar ul li a:hover {
    background-color: #b22222; /* Dark red for hover effect */
}

/* Main Content Styles */
.main-content {
    flex-grow: 1;
    padding: 40px;
    background-color: #fff;
    overflow-y: auto;
    box-shadow: 0px 0px 12px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    margin: 20px;
}

/* Main Title Styles */
h1 {
    text-align: center;
    font-size: 2rem;
    color: #003366; /* Dark blue for heading */
    margin-bottom: 20px;
}

/* Form Styles */
form {
    background-color: #ffffff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

form h2 {
    margin-bottom: 20px;
    font-size: 1.5rem;
    color: #333;
}

/* Label Styles */
label {
    font-weight: 600;
    color: #444;
    margin-bottom: 8px;
}

/* Input, Button, Select Styles */
input, button, select {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border-radius: 8px;
    border: 1px solid #ddd;
    font-size: 1rem;
    color: #333;
    box-sizing: border-box;
}

/* Button Styles */
button {
    background-color: #b22222; /* Dark red for buttons */
    color: white;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #dc143c; /* Crimson red on hover */
}

/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: #fff;
    border-radius: 8px;
    overflow: hidden;
}

table thead {
    background-color: #003366; /* Dark blue for table header */
    color: white;
}

table th, table td {
    padding: 14px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

table tbody tr:hover {
    background-color: #f2f2f2;
}

/* Action Buttons Styles */
.actions a {
    display: inline-block;
    padding: 8px 15px;
    color: white;
    background-color: #0057b8; /* Bright blue for action buttons */
    border-radius: 5px;
    margin-right: 10px;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.actions a.delete {
    background-color: #b22222; /* Dark red for delete actions */
}

.actions a:hover {
    opacity: 0.8;
}

/* Focus States for Accessibility */
input:focus, select:focus, button:focus {
    border-color: #b22222; /* Dark red border when focused */
    outline: none;
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .container {
        flex-direction: column;
        height: auto;
    }

    .sidebar {
        width: 100%;
        height: auto;
        box-shadow: none;
        border-radius: 0;
        margin-bottom: 20px;
    }

    .main-content {
        margin: 20px 0;
    }

    .sidebar h2 {
        font-size: 1.3rem;
    }

    h1 {
        font-size: 1.6rem;
    }

    form h2 {
        font-size: 1.3rem;
    }

    table th, table td {
        padding: 12px;
    }
}

    </style>
</head>
<body>
<div class="container">
            <!-- Sidebar -->
            <div class="sidebar">
        <h2>Dashboard</h2>
        <ul>
            <li><a href="/fundamentals_act/dashboard.php">Dashboard Home</a></li>
            <li><a href="/fundamentals_act/tables/instructor.php">Manage Instructors</a></li>
            <li><a href="/fundamentals_act/tables/department.php">Manage Departments</a></li>
            <li><a href="/fundamentals_act/tables/course.php">Manage Courses</a></li>
            <li><a href="/fundamentals_act/tables/classroom.php">Manage Classrooms</a></li>
            <li><a href="/fundamentals_act/tables/timeslot.php">Manage Time Slots</a></li>
            <li><a href="/fundamentals_act/tables/student.php">Manage Students</a></li>
        </ul>
    </div>

    <div class="main-content">
    <h1>Manage Department</h1>
    <form action="department.php" method="POST">
        <h2><?php echo $departmentToEdit ? 'Edit Department' : 'Add Department'; ?></h2>
        
        <?php if ($departmentToEdit): ?>
            <input type="hidden" name="original_dept_name" value="<?php echo htmlspecialchars($departmentToEdit['dept_name']); ?>">
        <?php endif; ?>

        <!-- Dropdown for building -->
        <label for="building">Building:</label>
        <select id="building" name="building" required>
            <option value="">Select Building</option>
            <?php foreach ($buildings as $buildingOption): ?>
                <option value="<?php echo htmlspecialchars($buildingOption['building']); ?>" 
                        <?php echo $departmentToEdit && $departmentToEdit['building'] == $buildingOption['building'] ? 'selected' : ''; ?> >
                    <?php echo htmlspecialchars($buildingOption['building']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="dept_name">Department Name:</label>
        <input type="text" id="dept_name" name="dept_name" value="<?php echo $departmentToEdit ? htmlspecialchars($departmentToEdit['dept_name']) : ''; ?>" required>

        <label for="budget">Budget:</label>
        <input type="number" id="budget" name="budget" value="<?php echo $departmentToEdit ? htmlspecialchars($departmentToEdit['budget']) : ''; ?>">

        <button type="submit"><?php echo $departmentToEdit ? 'Update Department' : 'Add Department'; ?></button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Department Name</th>
                <th>Building</th>
                <th>Budget</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($departments as $department): ?>
            <tr>
                <td><?php echo htmlspecialchars($department['dept_name']); ?></td>
                <td><?php echo htmlspecialchars($department['building']); ?></td>
                <td><?php echo htmlspecialchars($department['budget']); ?></td>
                <td class="actions">
                    <a href="department.php?edit=<?php echo htmlspecialchars($department['dept_name']); ?>">Edit</a>
                    <a href="department.php?delete=<?php echo htmlspecialchars($department['dept_name']); ?>" 
                       onclick="return confirm('Are you sure you want to delete this department?');" class="delete">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
