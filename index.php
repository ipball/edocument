<?php
// Database connection
require_once 'config.php';

// Handle search parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$recordsPerPage = 20;
$offset = ($page - 1) * $recordsPerPage;

// Query to get documents with filtering
$sql = "SELECT d.*, c.category_name, u.fullname 
        FROM documents d
        LEFT JOIN categories c ON d.category_id = c.id
        LEFT JOIN users u ON d.created_by = u.id
        WHERE 1=1";

if (!empty($search)) {
    $sql .= " AND (d.topic LIKE '%$search%' OR d.document_code LIKE '%$search%')";
}

if (!empty($category)) {
    $sql .= " AND d.category_id = $category";
}

if (!empty($status)) {
    $sql .= " AND d.document_status = '$status'";
}

$sql .= " ORDER BY d.created_at DESC LIMIT $offset, $recordsPerPage";

$result = $conn->query($sql);

// Count total records for pagination
$countSql = "SELECT COUNT(*) as total FROM documents d WHERE 1=1";
if (!empty($search)) {
    $countSql .= " AND (d.topic LIKE '%$search%' OR d.document_code LIKE '%$search%')";
}
if (!empty($category)) {
    $countSql .= " AND d.category_id = $category";
}
if (!empty($status)) {
    $countSql .= " AND d.document_status = '$status'";
}

$countResult = $conn->query($countSql);
$totalRecords = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// Query to get categories for dropdown
$categorySql = "SELECT * FROM categories ORDER BY category_name";
$categoryResult = $conn->query($categorySql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Document Management System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .hero-section {
            background-color: #f8f9fa;
            padding: 60px 0;
            margin-bottom: 30px;
        }
        .document-status {
            font-size: 0.8rem;
            padding: 4px 8px;
            border-radius: 12px;
        }
        .status-draft { background-color: #e9ecef; }
        .status-pending { background-color: #fff3cd; }
        .status-approved { background-color: #d1e7dd; }
        .status-active { background-color: #cfe2ff; }
        .status-cancelled { background-color: #f8d7da; }
        .status-expired { background-color: #e2e3e5; }
        .status-archived { background-color: #d3d3d3; }
        .status-exported { background-color: #f5f5f5; }
    </style>
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">E-Document System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>            
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <h1>ระบบจัดการเอกสารที่เรียบง่าย</h1>
                    <p class="lead">จัดเก็บ ติดตาม และจัดการเอกสารสำคัญทั้งหมดของคุณในที่เดียว</p>
                    <a href="<?php echo $admin_url; ?>" class="btn btn-success">เข้าสู่ระบบจัดการเอกสาร</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Search and Filter Options -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="index.php" class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="ค้นหาเอกสาร..." name="search" value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="category" onchange="this.form.submit()">
                            <option value="">หมวดหมู่ทั้งหมด</option>
                            <?php while($cat = $categoryResult->fetch_assoc()): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($category == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['category_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="status" onchange="this.form.submit()">
                            <option value="">สถานะทั้งหมด</option>
                            <option value="draft" <?php echo ($status == 'draft') ? 'selected' : ''; ?>>ร่าง</option>
                            <option value="pending" <?php echo ($status == 'pending') ? 'selected' : ''; ?>>รออนุมัติ</option>
                            <option value="approved" <?php echo ($status == 'approved') ? 'selected' : ''; ?>>อนุมัติแล้ว</option>
                            <option value="active" <?php echo ($status == 'active') ? 'selected' : ''; ?>>ใช้งานอยู่</option>
                            <option value="cancelled" <?php echo ($status == 'cancelled') ? 'selected' : ''; ?>>ยกเลิก</option>
                            <option value="expired" <?php echo ($status == 'expired') ? 'selected' : ''; ?>>หมดอายุ</option>
                            <option value="archived" <?php echo ($status == 'archived') ? 'selected' : ''; ?>>จัดเก็บ</option>
                            <option value="exported" <?php echo ($status == 'exported') ? 'selected' : ''; ?>>ส่งออก</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <a href="index.php" class="btn btn-outline-secondary w-100">รีเซ็ต</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Document List Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">รายการเอกสาร</h5>
            </div>
            <div class="card-body">
                <?php if ($result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>รหัสเอกสาร</th>
                                    <th>ชื่อเอกสาร</th>                                    
                                    <th>สถานะ</th>
                                    <th>วันที่เอกสาร</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['document_code'] ?? 'N/A'); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($row['topic']); ?>
                                            <div>
                                                <small class="text-success"><?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></small>
                                            </div>
                                            <div>
                                                <small class="text-muted">สร้างโดย: <?php echo htmlspecialchars($row['fullname'] ?? 'Unknown'); ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="document-status status-<?php echo $row['document_status']; ?>">
                                                <?php 
                                                    $status_labels = [
                                                        'draft' => 'ร่าง',
                                                        'pending' => 'รออนุมัติ',
                                                        'approved' => 'อนุมัติแล้ว',
                                                        'active' => 'ใช้งานอยู่',
                                                        'cancelled' => 'ยกเลิก',
                                                        'expired' => 'หมดอายุ',
                                                        'archived' => 'จัดเก็บ',
                                                        'exported' => 'ส่งออก'
                                                    ];
                                                    echo $status_labels[$row['document_status']] ?? ucfirst($row['document_status']);
                                                ?>
                                            </span>
                                        </td>
                                        <td><?php echo ($row['register_date']) ? date('d/m/Y', strtotime($row['register_date'])) : 'N/A'; ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="uploads/documents/<?php echo $row['file_name'] ?>" target="_blank" class="btn btn-primary" title="Download">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category; ?>&status=<?php echo $status; ?>">Previous</a>
                                </li>
                                
                                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category; ?>&status=<?php echo $status; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category; ?>&status=<?php echo $status; ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="alert alert-info">
                        No documents found. <?php echo !empty($search) || !empty($category) || !empty($status) ? 'Try changing your search criteria.' : ''; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light text-center text-muted py-4 mt-5">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> E-Document Management System by itoffside.com</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>
