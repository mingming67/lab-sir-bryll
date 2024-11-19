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

// Handle form submission for adding or editing a time slot
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['time_slot_id'])) {
        // Update existing time slot based on time_slot_id
        $stmt = $pdo->prepare("UPDATE time_slot SET day=?, start_time=?, end_time=? WHERE time_slot_id=?");
        $stmt->execute([
            $_POST['day'],
            $_POST['start_time'],
            $_POST['end_time'],
            $_POST['time_slot_id']
        ]);
    } else {
        // Add new time slot
        $stmt = $pdo->prepare("INSERT INTO time_slot (day, start_time, end_time) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['day'],
            $_POST['start_time'],
            $_POST['end_time']
        ]);
    }
    // Redirect back to the same page after form submission
    header("Location: timeslot.php");
    exit();
}

// Handle deletion of a time slot based on time_slot_id
if (isset($_GET['delete_time_slot_id'])) {
    $stmt = $pdo->prepare("DELETE FROM time_slot WHERE time_slot_id = ?");
    $stmt->execute([$_GET['delete_time_slot_id']]);
    header("Location: timeslot.php");
    exit();
}

// Fetch all time slots
$stmt = $pdo->query("SELECT * FROM time_slot");
$time_slots = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if a time slot is being edited based on time_slot_id
$timeSlotToEdit = null;
if (isset($_GET['edit_time_slot_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM time_slot WHERE time_slot_id = ?");
    $stmt->execute([$_GET['edit_time_slot_id']]);
    $timeSlotToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Time Slots</title>
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
    <h1>Manage Time Slots</h1>
    <!-- Form for adding or editing time slots -->
    <form action="timeslot.php" method="POST">
        <h2><?php echo $timeSlotToEdit ? 'Edit Time Slot' : 'Add Time Slot'; ?></h2>
        
        <?php if ($timeSlotToEdit): ?>
            <!-- Hidden fields to pass time_slot_id for editing -->
            <input type="hidden" name="time_slot_id" value="<?php echo htmlspecialchars($timeSlotToEdit['time_slot_id']); ?>">
        <?php endif; ?>

        <label for="day">Day:</label>
        <select id="day" name="day" required>
            <option value="">Select Day</option>
            <option value="Monday" <?php echo $timeSlotToEdit && $timeSlotToEdit['day'] === 'Monday' ? 'selected' : ''; ?>>Monday</option>
            <option value="Tuesday" <?php echo $timeSlotToEdit && $timeSlotToEdit['day'] === 'Tuesday' ? 'selected' : ''; ?>>Tuesday</option>
            <option value="Wednesday" <?php echo $timeSlotToEdit && $timeSlotToEdit['day'] === 'Wednesday' ? 'selected' : ''; ?>>Wednesday</option>
            <option value="Thursday" <?php echo $timeSlotToEdit && $timeSlotToEdit['day'] === 'Thursday' ? 'selected' : ''; ?>>Thursday</option>
            <option value="Friday" <?php echo $timeSlotToEdit && $timeSlotToEdit['day'] === 'Friday' ? 'selected' : ''; ?>>Friday</option>
        </select>

        <label for="start_time">Start Time:</label>
        <input type="time" id="start_time" name="start_time" value="<?php echo $timeSlotToEdit ? htmlspecialchars($timeSlotToEdit['start_time']) : ''; ?>" required>

        <label for="end_time">End Time:</label>
        <input type="time" id="end_time" name="end_time" value="<?php echo $timeSlotToEdit ? htmlspecialchars($timeSlotToEdit['end_time']) : ''; ?>" required>

        <button type="submit"><?php echo $timeSlotToEdit ? 'Update Time Slot' : 'Add Time Slot'; ?></button>
    </form>

    <!-- Table to display time slots -->
    <table>
        <thead>
            <tr>
                <th>Day</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($time_slots as $time_slot): ?>
    <tr>
        <td><?php echo htmlspecialchars($time_slot['day']); ?></td>
        <td><?php echo htmlspecialchars($time_slot['start_time']); ?></td>
        <td><?php echo htmlspecialchars($time_slot['end_time']); ?></td>
        <td class="actions">
            <!-- Edit and Delete links based on time_slot_id -->
            <a href="timeslot.php?edit_time_slot_id=<?php echo htmlspecialchars($time_slot['time_slot_id']); ?>">Edit</a>
            <a href="timeslot.php?delete_time_slot_id=<?php echo htmlspecialchars($time_slot['time_slot_id']); ?>" class="delete" onclick="return confirm('Are you sure you want to delete this time slot?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
