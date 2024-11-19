<?php
// Database connection
$host = 'localhost';
$db = 'db_university';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch all buildings from the department table
$stmt = $pdo->query("SELECT building FROM department");
$buildings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission for adding or editing a classroom
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['building']) && isset($_POST['room_number']) && isset($_POST['capacity'])) {
        if ($_POST['building'] && $_POST['room_number'] && $_POST['capacity']) {
            try {
                // Check if we are updating or inserting
                if (isset($_POST['room_number_old'])) {
                    // Update existing classroom
                    $stmt = $pdo->prepare("UPDATE classroom SET capacity=? WHERE building=? AND room_number=?");
                    $stmt->execute([$_POST['capacity'], $_POST['building'], $_POST['room_number_old']]);
                } else {
                    // Add new classroom
                    $stmt = $pdo->prepare("INSERT INTO classroom (building, room_number, capacity) VALUES (?, ?, ?)");
                    $stmt->execute([$_POST['building'], $_POST['room_number'], $_POST['capacity']]);
                }
                header("Location: classroom.php"); // Redirect to refresh the page and show changes
                exit();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage(); // Display error if query fails
            }
        } else {
            echo "Please fill in all fields.";
        }
    }
}

// Handle deletion of a classroom
if (isset($_GET['delete_building']) && isset($_GET['delete_room_number'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM classroom WHERE building = ? AND room_number = ?");
        $stmt->execute([$_GET['delete_building'], $_GET['delete_room_number']]);
        header("Location: classroom.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fetch all classrooms
$stmt = $pdo->query("SELECT * FROM classroom");
$classrooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if a classroom is being edited
$classroomToEdit = null;
if (isset($_GET['edit_building']) && isset($_GET['edit_room_number'])) {
    $stmt = $pdo->prepare("SELECT * FROM classroom WHERE building = ? AND room_number = ?");
    $stmt->execute([$_GET['edit_building'], $_GET['edit_room_number']]);
    $classroomToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classrooms</title>
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

<<div class="container">
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
    <h1>Manage Classrooms</h1>

    <!-- Form for adding or editing classrooms -->
    <form action="classroom.php" method="POST">
        <h2><?php echo $classroomToEdit ? 'Edit Classroom' : 'Add Classroom'; ?></h2>
        
        <?php if ($classroomToEdit): ?>
            <!-- Hidden fields to pass building and room number for editing -->
            <input type="hidden" name="building" value="<?php echo htmlspecialchars($classroomToEdit['building']); ?>">
            <input type="hidden" name="room_number_old" value="<?php echo htmlspecialchars($classroomToEdit['room_number']); ?>">
        <?php endif; ?>

        <label for="building">Building:</label>
        <input type="text" id="building" name="building" value="<?php echo $classroomToEdit ? htmlspecialchars($classroomToEdit['building']) : ''; ?>" required>

        <label for="room_number">Room Number:</label>
        <input type="text" id="room_number" name="room_number" value="<?php echo $classroomToEdit ? htmlspecialchars($classroomToEdit['room_number']) : ''; ?>" required>

        <label for="capacity">Capacity:</label>
        <input type="number" id="capacity" name="capacity" value="<?php echo $classroomToEdit ? htmlspecialchars($classroomToEdit['capacity']) : ''; ?>" required>

        <button type="submit"><?php echo $classroomToEdit ? 'Update Classroom' : 'Add Classroom'; ?></button>
    </form>

    <!-- Table to display classrooms -->
    <table>
        <thead>
            <tr>
                <th>Building</th>
                <th>Room Number</th>
                <th>Capacity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($classrooms as $classroom): ?>
        <tr>
            <td><?php echo htmlspecialchars($classroom['building']); ?></td>
            <td><?php echo htmlspecialchars($classroom['room_number']); ?></td>
            <td><?php echo htmlspecialchars($classroom['capacity']); ?></td>
            <td class="actions">
                <!-- Edit and Delete links -->
                <a href="classroom.php?edit_building=<?php echo htmlspecialchars($classroom['building']); ?>&edit_room_number=<?php echo htmlspecialchars($classroom['room_number']); ?>">Edit</a>
                <a href="classroom.php?delete_building=<?php echo htmlspecialchars($classroom['building']); ?>&delete_room_number=<?php echo htmlspecialchars($classroom['room_number']); ?>" class="delete" onclick="return confirm('Are you sure you want to delete this classroom?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>