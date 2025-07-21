<?php
session_start();
require_once '../config/database.php';

// Simple authentication (you can enhance this)
$admin_username = 'admin';
$admin_password = 'photokrafft2024';

if (isset($_POST['login'])) {
    if ($_POST['username'] === $admin_username && $_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $error = 'Invalid credentials';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    // Show login form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login - Photokrafft</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Inter', sans-serif;
            }
            
            body {
                background: #000;
                color: #fff;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .login-container {
                background: #111;
                padding: 2rem;
                border-radius: 12px;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
                width: 100%;
                max-width: 400px;
            }
            
            .login-header {
                text-align: center;
                margin-bottom: 2rem;
            }
            
            .login-header h1 {
                font-size: 1.5rem;
                font-weight: 600;
                margin-bottom: 0.5rem;
            }
            
            .form-group {
                margin-bottom: 1rem;
            }
            
            .form-group label {
                display: block;
                margin-bottom: 0.5rem;
                font-weight: 500;
            }
            
            .form-group input {
                width: 100%;
                padding: 0.75rem;
                border: 1px solid #333;
                border-radius: 6px;
                background: #222;
                color: #fff;
                font-size: 1rem;
            }
            
            .form-group input:focus {
                outline: none;
                border-color: #5bb5a2;
            }
            
            .login-btn {
                width: 100%;
                padding: 0.75rem;
                background: #5bb5a2;
                color: #000;
                border: none;
                border-radius: 6px;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.3s ease;
            }
            
            .login-btn:hover {
                background: #4a9a8a;
            }
            
            .error {
                color: #ff4444;
                text-align: center;
                margin-bottom: 1rem;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="login-header">
                <h1>Admin Login</h1>
                <p>Photokrafft Dashboard</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" name="login" class="login-btn">Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Get form submissions
$pdo = getDBConnection();
$sql = "SELECT * FROM form_submissions ORDER BY created_at DESC";
$stmt = $pdo->query($sql);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Photokrafft</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: #000;
            color: #fff;
            min-height: 100vh;
        }
        
        .header {
            background: #111;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #333;
        }
        
        .header h1 {
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .header-actions {
            display: flex;
            gap: 1rem;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: #5bb5a2;
            color: #000;
        }
        
        .btn-primary:hover {
            background: #4a9a8a;
        }
        
        .btn-secondary {
            background: #333;
            color: #fff;
        }
        
        .btn-secondary:hover {
            background: #444;
        }
        
        .btn-danger {
            background: #dc2626;
            color: #fff;
        }
        
        .btn-danger:hover {
            background: #b91c1c;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: #111;
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid #333;
        }
        
        .stat-card h3 {
            font-size: 0.875rem;
            color: #888;
            margin-bottom: 0.5rem;
        }
        
        .stat-card .value {
            font-size: 2rem;
            font-weight: 600;
            color: #5bb5a2;
        }
        
        .table-container {
            background: #111;
            border-radius: 8px;
            border: 1px solid #333;
            overflow: hidden;
        }
        
        .table-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .table-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th,
        .table td {
            padding: 1rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid #333;
        }
        
        .table th {
            background: #1a1a1a;
            font-weight: 600;
            color: #888;
        }
        
        .table tr:hover {
            background: #1a1a1a;
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
        }
        
        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #111;
            padding: 2rem;
            border-radius: 8px;
            border: 1px solid #333;
            max-width: 500px;
            width: 90%;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .close {
            background: none;
            border: none;
            color: #888;
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #333;
            border-radius: 6px;
            background: #222;
            color: #fff;
            font-size: 1rem;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #5bb5a2;
        }
        
        .search-box {
            padding: 0.75rem;
            border: 1px solid #333;
            border-radius: 6px;
            background: #222;
            color: #fff;
            font-size: 1rem;
            margin-bottom: 1rem;
        }
        
        .search-box:focus {
            outline: none;
            border-color: #5bb5a2;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .header {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
            }
            
            .table-container {
                overflow-x: auto;
            }
            
            .table {
                min-width: 600px;
            }
            
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Photokrafft Admin Dashboard</h1>
        <div class="header-actions">
            <a href="?logout=1" class="btn btn-secondary">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
    </div>
    
    <div class="container">
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Registrations</h3>
                <div class="value"><?php echo count($submissions); ?></div>
            </div>
            <div class="stat-card">
                <h3>Today's Registrations</h3>
                <div class="value">
                    <?php 
                    $today = date('Y-m-d');
                    $todayCount = 0;
                    foreach ($submissions as $submission) {
                        if (date('Y-m-d', strtotime($submission['created_at'])) === $today) {
                            $todayCount++;
                        }
                    }
                    echo $todayCount;
                    ?>
                </div>
            </div>
            <div class="stat-card">
                <h3>This Week</h3>
                <div class="value">
                    <?php 
                    $weekAgo = date('Y-m-d', strtotime('-7 days'));
                    $weekCount = 0;
                    foreach ($submissions as $submission) {
                        if (date('Y-m-d', strtotime($submission['created_at'])) >= $weekAgo) {
                            $weekCount++;
                        }
                    }
                    echo $weekCount;
                    ?>
                </div>
            </div>
        </div>
        
        <!-- Search -->
        <input type="text" id="searchBox" class="search-box" placeholder="Search registrations...">
        
        <!-- Table -->
        <div class="table-container">
            <div class="table-header">
                <h2>Form Submissions</h2>
                <div class="table-actions">
                    <button class="btn btn-primary" onclick="exportToCSV()">
                        <i class="fas fa-download"></i>
                        Export CSV
                    </button>
                    <button class="btn btn-primary" onclick="exportToExcel()">
                        <i class="fas fa-file-excel"></i>
                        Export Excel
                    </button>
                </div>
            </div>
            
            <table class="table" id="submissionsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Event</th>
                        <th>Workshop</th>
                        <th>Investment</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($submissions as $submission): ?>
                    <tr data-id="<?php echo $submission['id']; ?>">
                        <td><?php echo $submission['id']; ?></td>
                        <td><?php echo htmlspecialchars($submission['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($submission['email']); ?></td>
                        <td><?php echo htmlspecialchars($submission['event_name']); ?></td>
                        <td><?php echo htmlspecialchars($submission['workshop_name']); ?></td>
                        <td><?php echo htmlspecialchars($submission['investment']); ?></td>
                        <td><?php echo date('M d, Y H:i', strtotime($submission['created_at'])); ?></td>
                        <td>
                            <div class="actions">
                                <button class="btn btn-secondary btn-sm" onclick="viewSubmission(<?php echo $submission['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-primary btn-sm" onclick="editSubmission(<?php echo $submission['id']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteSubmission(<?php echo $submission['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- View Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>View Submission</h3>
                <button class="close" onclick="closeModal('viewModal')">&times;</button>
            </div>
            <div id="viewModalContent"></div>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Submission</h3>
                <button class="close" onclick="closeModal('editModal')">&times;</button>
            </div>
            <form id="editForm">
                <input type="hidden" id="editId" name="id">
                <div class="form-group">
                    <label for="editFullName">Full Name</label>
                    <input type="text" id="editFullName" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="editEmail">Email</label>
                    <input type="email" id="editEmail" name="email" required>
                </div>
                <div class="form-group">
                    <label for="editEventName">Event Name</label>
                    <input type="text" id="editEventName" name="event_name">
                </div>
                <div class="form-group">
                    <label for="editWorkshopName">Workshop Name</label>
                    <input type="text" id="editWorkshopName" name="workshop_name">
                </div>
                <div class="form-group">
                    <label for="editInvestment">Investment</label>
                    <input type="text" id="editInvestment" name="investment">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Search functionality
        document.getElementById('searchBox').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#submissionsTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
        
        // View submission
        function viewSubmission(id) {
            // Fetch submission data and populate modal
            fetch(`api.php?action=view&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const submission = data.submission;
                        document.getElementById('viewModalContent').innerHTML = `
                            <div class="form-group">
                                <label>Full Name:</label>
                                <p>${submission.full_name}</p>
                            </div>
                            <div class="form-group">
                                <label>Email:</label>
                                <p>${submission.email}</p>
                            </div>
                            <div class="form-group">
                                <label>Event Name:</label>
                                <p>${submission.event_name || 'N/A'}</p>
                            </div>
                            <div class="form-group">
                                <label>Workshop Name:</label>
                                <p>${submission.workshop_name || 'N/A'}</p>
                            </div>
                            <div class="form-group">
                                <label>Investment:</label>
                                <p>${submission.investment || 'N/A'}</p>
                            </div>
                            <div class="form-group">
                                <label>Created At:</label>
                                <p>${submission.created_at}</p>
                            </div>
                        `;
                        document.getElementById('viewModal').style.display = 'block';
                    }
                });
        }
        
        // Edit submission
        function editSubmission(id) {
            fetch(`api.php?action=view&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const submission = data.submission;
                        document.getElementById('editId').value = submission.id;
                        document.getElementById('editFullName').value = submission.full_name;
                        document.getElementById('editEmail').value = submission.email;
                        document.getElementById('editEventName').value = submission.event_name || '';
                        document.getElementById('editWorkshopName').value = submission.workshop_name || '';
                        document.getElementById('editInvestment').value = submission.investment || '';
                        document.getElementById('editModal').style.display = 'block';
                    }
                });
        }
        
        // Delete submission
        function deleteSubmission(id) {
            if (confirm('Are you sure you want to delete this submission?')) {
                fetch(`api.php?action=delete&id=${id}`, { method: 'POST' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error deleting submission');
                        }
                    });
            }
        }
        
        // Close modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Handle edit form submission
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'update');
            
            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating submission');
                }
            });
        });
        
        // Export to CSV
        function exportToCSV() {
            const table = document.getElementById('submissionsTable');
            const rows = table.querySelectorAll('tbody tr');
            let csv = 'ID,Name,Email,Event,Workshop,Investment,Date\n';
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const rowData = [];
                for (let i = 0; i < cells.length - 1; i++) { // Exclude actions column
                    rowData.push('"' + cells[i].textContent.replace(/"/g, '""') + '"');
                }
                csv += rowData.join(',') + '\n';
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'photokrafft_submissions.csv';
            a.click();
        }
        
        // Export to Excel
        function exportToExcel() {
            const table = document.getElementById('submissionsTable');
            const rows = table.querySelectorAll('tbody tr');
            let html = '<table>';
            
            // Add header
            html += '<tr><th>ID</th><th>Name</th><th>Email</th><th>Event</th><th>Workshop</th><th>Investment</th><th>Date</th></tr>';
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                html += '<tr>';
                for (let i = 0; i < cells.length - 1; i++) { // Exclude actions column
                    html += '<td>' + cells[i].textContent + '</td>';
                }
                html += '</tr>';
            });
            
            html += '</table>';
            
            const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'photokrafft_submissions.xls';
            a.click();
        }
    </script>
</body>
</html>