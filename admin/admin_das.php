<?php
@include 'admin.php';
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Waste Management System</title>
    <link rel="stylesheet" href="../assets/style/dashStyle.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <ul>
                <li><a href="#users">Users</a></li>
                <li><a href="#schedules">Schedules</a></li>
                <li><a href="#categories">Waste Categories</a></li>
                <li><a href="#reports">Reports</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="users">
            <h2>User Management</h2>
            <form action="" method="POST">
                <input type="hidden" name="action" value="add_user">
                <input type="text" name="name" placeholder="Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <select name="role" required>
                    <option value="citizen">Citizen</option>
                    <option value="operator">Operator</option>
                </select>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Add User</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- User rows will be dynamically added here -->
                </tbody>
            </table>
        </section>

        <section id="schedules">
            <h2>Schedule Management</h2>
            <form action="" method="POST">
                <input type="text" name="route" placeholder="Route" required>
                <select name="day" required>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                </select>
                <input type="time" name="time" required>
                <button type="submit">Add Schedule</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Route</th>
                        <th>Day</th>
                        <th>Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Schedule rows will be dynamically added here -->
                </tbody>
            </table>
        </section>

        <section id="categories">
            <h2>Waste Category Management</h2>
            <form action="" method="POST">
                <input type="text" name="name" placeholder="Category Name" required>
                <textarea name="description" placeholder="Description" required></textarea>
                <button type="submit">Add Category</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Category rows will be dynamically added here -->
                </tbody>
            </table>
        </section>

        <section id="reports">
            <h2>Reports</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Report rows will be dynamically added here -->
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2023 Waste Management System. All rights reserved.</p>
    </footer>

    <script src="assets/js/dashScript.js"></script>
</body>
</html>
