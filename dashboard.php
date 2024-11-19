<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Admin Dashboard</title>
    <style>
   /* General Body Styles */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f7f7f7; /* Light gray for a neutral background */
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
    background-color: #003366; /* Dark Blue (Captain America's primary color) */
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
    background-color: #0057b8; /* Bright Blue (links color) */
    transition: background-color 0.3s ease-in-out;
}

.sidebar ul li a:hover {
    background-color: #b22222; /* Dark Red (hover effect) */
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
    color: #003366; /* Dark Blue (for the title) */
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

/* Input, Select, Button Styles */
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
    background-color: #b22222; /* Dark Red (for buttons) */
    color: white;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #dc143c; /* Crimson (hover effect) */
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
    background-color: #003366; /* Dark Blue (for table header) */
    color: white;
}

table th, table td {
    padding: 14px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

table tbody tr:hover {
    background-color: #f2f2f2; /* Light gray on hover */
}

/* Action Buttons Styles */
.actions a {
    display: inline-block;
    padding: 8px 15px;
    color: white;
    background-color: #ffd700; /* Gold (for action buttons) */
    border-radius: 5px;
    margin-right: 10px;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.actions a.delete {
    background-color: #dc143c; /* Crimson (for delete buttons) */
}

.actions a:hover {
    opacity: 0.8;
}

/* Focus States for Accessibility */
input:focus, select:focus, button:focus {
    border-color: #b22222; /* Dark Red (focus state) */
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
    <div class="sidebar">
        <h2> Dashboard</h2>
        <ul>
            <li><a href="tables/instructor.php">Manage Instructors</a></li>
            <li><a href="tables/department.php">Manage Departments</a></li>
            <li><a href="tables/course.php">Manage Courses</a></li>
            <li><a href="tables/classroom.php">Manage Classrooms</a></li>
            <li><a href="tables/section.php">Manage Sections</a></li>
            <li><a href="tables/timeslot.php">Manage Time Slots</a></li>
            <li><a href="tables/student.php">Manage Students</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Welcome to the University  Dashboard</h1>
        <p>Select an option from the sidebar to manage university data.</p>
    </div>
</div>

</body>
</html>