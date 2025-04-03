<?php
include '../config.php';
include 'auth.php';
$page_title = 'เอกสาร';
ob_start();

// Handle search
$search_keyword = isset($_GET['search_keyword']) ? $_GET['search_keyword'] : '';
$category_filter = isset($_GET['category_filter']) ? $_GET['category_filter'] : '';

// Pagination settings
$limit = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Build query
$query = "SELECT d.*, c.category_name FROM documents d 
          LEFT JOIN categories c ON d.category_id = c.id 
          WHERE 1=1";
if ($search_keyword) {
    $query .= " AND (d.document_code LIKE '%$search_keyword%' OR d.topic LIKE '%$search_keyword%')";
}
if ($category_filter) {
    $query .= " AND d.category_id = $category_filter";
}
$query .= " ORDER BY d.id DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Get total records for pagination
$total_query = "SELECT COUNT(*) as total FROM documents d WHERE 1=1";
if ($search_keyword) {
    $total_query .= " AND (d.document_code LIKE '%$search_keyword%' OR d.topic LIKE '%$search_keyword%')";
}
if ($category_filter) {
    $total_query .= " AND d.category_id = $category_filter";
}
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// Get categories for filter
$categories_query = "SELECT * FROM categories ORDER BY category_name";
$categories_result = mysqli_query($conn, $categories_query);
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">เอกสาร</h3>
        <div class="card-tools">
            <a href="<?php echo $admin_url; ?>/document-add.php" class="btn btn-primary">เพิ่มเอกสาร</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-5">
                    <input type="text" name="search_keyword" class="form-control" placeholder="ค้นหาเอกสาร" value="<?php echo $search_keyword; ?>">
                </div>
                <div class="col-md-3">
                    <select name="category_filter" class="form-control">
                        <option value="">-- ทุกหมวดหมู่ --</option>
                        <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($category_filter == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo $cat['category_name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">ค้นหา</button>
                </div>
            </div>
        </form>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>รหัสเอกสาร</th>
                    <th>หัวข้อ</th>
                    <th>หมวดหมู่</th>
                    <th>วันที่ลงทะเบียน</th>
                    <th>สถานะ</th>
                    <th>เอกสาร</th>
                    <th style="width: 200px;">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['document_code']; ?></td>
                        <td><?php echo $row['topic']; ?></td>
                        <td><?php echo $row['category_name']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['register_date'])); ?></td>
                        <td>
                            <?php
                            $status_class = '';
                            switch($row['document_status']) {
                                case 'draft': $status_class = 'badge bg-secondary'; break;
                                case 'pending': $status_class = 'badge bg-warning'; break;
                                case 'approved': $status_class = 'badge bg-success'; break;
                                case 'active': $status_class = 'badge bg-primary'; break;
                                case 'cancelled': $status_class = 'badge bg-danger'; break;
                                case 'expired': $status_class = 'badge bg-danger'; break;
                                case 'archived': $status_class = 'badge bg-info'; break;
                                case 'exported': $status_class = 'badge bg-dark'; break;
                            }
                            ?>
                            <span class="<?php echo $status_class; ?>"><?php echo $row['document_status']; ?></span>
                        </td>
                        <td>
                            <?php if ($row['file_name']): ?>
                                <a href="<?php echo $base_url . '/uploads/documents/' . $row['file_name']; ?>" target="_blank" class="btn btn-sm btn-info">
                                    <i class="bi bi-file-earmark-text"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo $admin_url . '/document-edit.php?id=' . $row['id']; ?>" class='btn btn-warning'>
                                <i class="bi bi-pencil-square me-1"></i>Edit
                            </a>
                            <a href="<?php echo $admin_url . '/document-delete.php?id=' . $row['id']; ?>" class='btn btn-danger' onclick="return confirm('ยืนยันการลบเอกสารนี้?');">
                                <i class="bi bi-trash me-1"></i>Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        <ul class="pagination pagination-sm m-0 float-end">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&search_keyword=<?php echo $search_keyword; ?>&category_filter=<?php echo $category_filter; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </div>
</div>
<?php
    $content = ob_get_clean();
    $js_script = '';
    include 'template_master.php';
?>
