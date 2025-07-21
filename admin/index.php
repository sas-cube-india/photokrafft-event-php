<?php
session_start();
require_once '../config/database.php';

// Simple authentication
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

if (!isset($_SESSION['admin_logged_in'])) {
    // Login form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login - Photokrafft</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif; }
            body { background:#f4f6f9; color:#333; min-height:100vh; display:flex; align-items:center; justify-content:center; }
            .login-container { background:#fff; padding:2rem; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.05); width:100%; max-width:400px; }
            .login-header h1 { font-size:1.5rem; font-weight:600; margin-bottom:0.5rem; }
            .login-header p { margin-bottom:1.5rem; color:#666; }
            .form-group { margin-bottom:1rem; }
            .form-group label { display:block; margin-bottom:0.5rem; font-weight:500; }
            .form-group input { width:100%; padding:0.75rem; border:1px solid #ddd; border-radius:6px; font-size:1rem; }
            .login-btn { width:100%; padding:0.75rem; background:#5bb5a2; color:#fff; font-weight:600; border:none; border-radius:6px; cursor:pointer; transition: background 0.3s; }
            .login-btn:hover { background:#4da490; }
            .error { color:#e74c3c; text-align:center; margin-bottom:1rem; }
            /* Modal wrapper */
.modal {
  display: none; 
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto; /* Enables scroll if needed */
  background-color: rgba(0,0,0,0.5);
}

/* Modal content box */
.modal-content {
  background-color: #fff;
  margin: 5% auto;
  padding: 20px;
  border-radius: 10px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh; /* Prevents overflow */
  overflow-y: auto; /* Enables vertical scroll inside modal */
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}

/* Modal header */
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* Close button */
.close {
  font-size: 24px;
  cursor: pointer;
  background: none;
  border: none;
}
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="login-header">
                <h1>Admin Login</h1>
                <p>Welcome to Photokrafft Dashboard</p>
            </div>
            <?php if (isset($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button name="login" class="login-btn">Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php exit;
}

// Fetch submissions
$pdo = getDBConnection();
$submissions = $pdo->query("SELECT * FROM form_submissions ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Photokrafft</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif; }
        body { background:#f4f6f9; color:#333; min-height:100vh; }
        .header {
            background:#fff;
            padding:1rem 2rem;
            display:flex; justify-content:space-between; align-items:center;
            box-shadow:0 2px 8px rgba(0,0,0,0.04);
            position:sticky; top:0; z-index:100;
        }
        .header h1 { font-size:1.5rem; font-weight:600; color:#2c3e50; }
        .header-actions .btn {
            background:#f4f4f4; border:1px solid #ccc; color:#333; display:inline-flex; align-items:center; gap:0.5rem; padding:0.5rem 1rem; border-radius:6px; text-decoration:none; transition:background 0.3s;
        }
        .header-actions .btn:hover { background:#e2e2e2; }
        .container { max-width:1400px; margin:2rem auto; padding:0 1rem; }
        .stats-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(250px,1fr)); gap:1rem; margin-bottom:2rem; }
        .stat-card {
            background:#fff; padding:1.5rem; border-radius:10px;
            border:1px solid #eaeaea; box-shadow:0 2px 6px rgba(0,0,0,0.03);
            text-align:center;
        }
        .stat-card h3 { font-size:0.9rem; color:#666; margin-bottom:0.5rem; }
        .stat-card .value { font-size:2rem; font-weight:700; color:#5bb5a2; }
        .search-box {
            width:100%; padding:0.75rem; font-size:1rem; border:1px solid #ccc;
            border-radius:8px; margin-bottom:1.5rem;
        }
        .table-container {
            background:#fff; border-radius:10px; border:1px solid #e0e0e0; overflow-x:auto;
        }
        .table-header {
            padding:1rem 1.5rem; display:flex;
            justify-content:space-between; align-items:center; border-bottom:1px solid #eee;
            background:#fafafa;
        }
        .table-header h2 { font-size:1.2rem; font-weight:600; color:#2c3e50; }
        .table-actions .btn {
            background:#5bb5a2; color:#fff; padding:0.5rem 1rem; border:none; border-radius:6px;
            display:inline-flex; align-items:center; gap:0.5rem; cursor:pointer;
        }
        .table-actions .btn:hover { background:#4da490; }
        .table {
            width:100%; border-collapse:collapse; font-size:0.95rem;
        }
        .table th, .table td {
            padding:1rem; border-bottom:1px solid #f0f0f0; text-align:left; white-space:nowrap;
        }
        .table th { background:#f9f9f9; color:#666; }
        .table tr:hover { background:#f3f3f3; }
        .actions .btn {
            font-size:0.85rem; padding:0.4rem 0.6rem; border:none; border-radius:6px; cursor:pointer;
        }
        .btn-sm { padding:0.3rem 0.5rem; font-size:0.85rem; }
        .btn-primary { background:#5bb5a2; color:#fff; }
        .btn-secondary { background:#ddd; color:#333; }
        .btn-danger { background:#e74c3c; color:#fff; }
        .btn-primary:hover { background:#4da490; }
        .btn-danger:hover { background:#c0392b; }
        .modal {
            display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.4); z-index:1000;
        }
        .modal-content {
            background:#fff; padding:2rem; border-radius:10px;
            max-width:500px; width:90%; margin:5% auto; position:relative;
        }
        .modal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; }
        .close {
            background:none; border:none; font-size:1.5rem; color:#aaa; cursor:pointer;
        }
        .form-group { margin-bottom:1rem; }
        .form-group label { display:block; margin-bottom:0.5rem; font-weight:500; }
        .form-group input, .form-group textarea {
            width:100%; padding:0.75rem; border:1px solid #ccc; border-radius:6px; font-size:1rem;
        }
        .form-group input:focus, .form-group textarea:focus {
            border-color:#5bb5a2; outline:none;
        }
        @media screen and (max-width:768px) {
            .table { font-size:0.85rem; }
            .table th, .table td { padding:0.75rem; }
            .table-header { flex-direction:column; gap:1rem; align-items:flex-start; }
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Photokrafft Admin Dashboard</h1>
    <div class="header-actions">
        <a href="?logout=1" class="btn btn-secondary">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>

<div class="container">

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Registrations</h3>
            <div class="value"><?= count($submissions) ?></div>
        </div>
        <div class="stat-card">
            <h3>Today's Registrations</h3>
            <div class="value">
                <?php
                $today = date('Y-m-d'); $t=0;
                foreach ($submissions as $s) if (date('Y-m-d',strtotime($s['created_at'])) === $today) $t++;
                echo $t;
                ?>
            </div>
        </div>
        <div class="stat-card">
            <h3>Last 7 Days</h3>
            <div class="value">
                <?php
                $week = date('Y-m-d', strtotime('-7 days')); $w=0;
                foreach ($submissions as $s) if (date('Y-m-d',strtotime($s['created_at'])) >= $week) $w++;
                echo $w;
                ?>
            </div>
        </div>
    </div>

    <input type="text" id="searchBox" class="search-box" placeholder="Search registrations...">

    <div class="table-container">
        <div class="table-header">
            <h2>Form Submissions</h2>
            <div class="table-actions">
                <button class="btn btn-primary" onclick="exportToCSV()">
                    <i class="fas fa-download"></i> Export CSV
                </button>
                <button class="btn btn-primary" onclick="exportToExcel()">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
            </div>
        </div>

        <table class="table" id="submissionsTable">
            <thead>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Event</th><th>Workshop</th><th>Investment</th><th>Date</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($submissions as $s): ?>
                <tr data-id="<?= $s['id'] ?>">
                    <td><?= $s['id'] ?></td>
                    <td><?= htmlspecialchars($s['full_name']) ?></td>
                    <td><?= htmlspecialchars($s['email']) ?></td>
                    <td><?= htmlspecialchars($s['phone'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($s['event_name']) ?></td>
                    <td><?= htmlspecialchars($s['workshop_name']) ?></td>
                    <td><?= htmlspecialchars($s['investment']) ?></td>
                    <td><?= date('M d, Y H:i', strtotime($s['created_at'])) ?></td>
                    <td>
                        <div class="actions">
                            <button class="btn btn-secondary btn-sm" onclick="viewSubmission(<?= $s['id'] ?>)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="editSubmission(<?= $s['id'] ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteSubmission(<?= $s['id'] ?>)">
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

// Modle and edit

<!-- View Modal -->
<div id="viewModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>View Submission</h2>
      <button class="close" onclick="closeModal('viewModal')">&times;</button>
    </div>
    <div id="viewModalContent"></div>
  </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Edit Submission</h2>
      <button class="close" onclick="closeModal('editModal')">&times;</button>
    </div>
    <form id="editForm">
      <input type="hidden" id="editId" name="id">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" id="editFullName" name="full_name" required>
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" id="editEmail" name="email" required>
      </div>
      <div class="form-group">
        <label>Event</label>
        <input type="text" id="editEventName" name="event_name">
      </div>
      <div class="form-group">
        <label>Workshop</label>
        <input type="text" id="editWorkshopName" name="workshop_name">
      </div>
      <div class="form-group">
        <label>Investment</label>
        <input type="number" id="editInvestment" name="investment">
      </div>
      <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
  </div>
</div>


<!-- Modals and JS logic remain unchanged -->

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
    			    <label>Phone:</label>
    			    <p>${submission.phone || 'N/A'}</p>
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
            let csv = 'ID,Name,Email,Phone,Event,Workshop,Investment,Date\n';

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const rowData = [];
		for (let i = 0; i < cells.length - 1; i++) {
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
            html += '<tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Event</th><th>Workshop</th><th>Investment</th><th>Date</th></tr>';

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
