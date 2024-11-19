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

// Handle form submission for adding or editing a course
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle nullable fields by assigning NULL if empty
    $course_code = $_POST['course_code'];
    $title = $_POST['title'];
    $credits = !empty($_POST['credits']) ? $_POST['credits'] : NULL; // Credits can be NULL if empty
    $dept_name = !empty($_POST['dept_name']) ? $_POST['dept_name'] : NULL; // Department can be NULL if empty

    if (isset($_POST['original_course_code']) && $_POST['original_course_code'] != '') {
        // Update existing course
        $stmt = $pdo->prepare("UPDATE course SET course_code=?, title=?, credits=?, dept_name=? WHERE course_code=?");
        $stmt->execute([$course_code, $title, $credits, $dept_name, $_POST['original_course_code']]);
    } else {
        // Add new course
        $stmt = $pdo->prepare("INSERT INTO course (course_code, title, credits, dept_name) VALUES (?, ?, ?, ?)");
        $stmt->execute([$course_code, $title, $credits, $dept_name]);
    }
    // Redirect back to the same page after form submission
    header("Location: course.php");
    exit();
}

// Handle deletion of a course
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM course WHERE course_code = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: course.php");
    exit();
}

// Fetch all courses
$stmt = $pdo->query("SELECT * FROM course");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all departments for selection in form
$stmt = $pdo->query("SELECT dept_name FROM department");
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if a course is being edited
$courseToEdit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM course WHERE course_code = ?");
    $stmt->execute([$_GET['edit']]);
    $courseToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Course</title>
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
    <h1>Manage Course</h1>
    <form action="course.php" method="POST">
        <h2><?php echo $courseToEdit ? 'Edit Course' : 'Add Course'; ?></h2>
        
        <?php if ($courseToEdit): ?>
            <input type="hidden" name="original_course_code" value="<?php echo htmlspecialchars($courseToEdit['course_code']); ?>">
        <?php endif; ?>

        <label for="course_code">Course Code:</label>
        <input type="text" id="course_code" name="course_code" value="<?php echo $courseToEdit ? htmlspecialchars($courseToEdit['course_code']) : ''; ?>" required>

        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo $courseToEdit ? htmlspecialchars($courseToEdit['title']) : ''; ?>" required>

        <label for="credits">Credits:</label>
        <input type="number" id="credits" name="credits" value="<?php echo $courseToEdit ? htmlspecialchars($courseToEdit['credits']) : ''; ?>" >

        <label for="dept_name">Department:</label>
        <select id="dept_name" name="dept_name" >
            <option value="">-- Select Department --</option> <!-- Option for NULL value -->
            <?php foreach ($departments as $department): ?>
                <option value="<?php echo htmlspecialchars($department['dept_name']); ?>" 
                    <?php echo $courseToEdit && $courseToEdit['dept_name'] == $department['dept_name'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($department['dept_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit"><?php echo $courseToEdit ? 'Update Course' : 'Add Course'; ?></button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Course Code</th>
                <th>Title</th>
                <th>Credits</th>
                <th>Department</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($courses as $course): ?>
            <tr>
                <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                <td><?php echo htmlspecialchars($course['title']); ?></td>
                <td><?php echo htmlspecialchars($course['credits']); ?></td>
                <td><?php echo htmlspecialchars($course['dept_name']); ?></td>
                <td class="actions">
                    <a href="course.php?edit=<?php echo htmlspecialchars($course['course_code']); ?>">Edit</a>
                    <a href="course.php?delete=<?php echo htmlspecialchars($course['course_code']); ?>" 
                       onclick="return confirm('Are you sure you want to delete this course?');" class="delete">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
