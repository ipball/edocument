<?php
include '../config.php';
include 'auth.php';
$page_title = 'ผู้ใช้';
ob_start();

// Handle search
$search_keyword = isset($_GET['search_keyword']) ? $_GET['search_keyword'] : '';

// Pagination settings
$limit = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Build query
$query = "SELECT * FROM users WHERE 1=1";
if ($search_keyword) {
    $query .= " AND (username LIKE '%$search_keyword%' OR fullname LIKE '%$search_keyword%' OR email LIKE '%$search_keyword%')";
}
$query .= " LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Get total records for pagination
$total_query = "SELECT COUNT(*) as total FROM users WHERE 1=1";
if ($search_keyword) {
    $total_query .= " AND (username LIKE '%$search_keyword%' OR fullname LIKE '%$search_keyword%' OR email LIKE '%$search_keyword%')";
}
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">ผู้ใช้</h3>
        <div class="card-tools">
        <a href="<?php echo $admin_url; ?>/user-add.php" class="btn btn-primary">เพิ่มผู้ใช้</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-8">
                    <input type="text" name="search_keyword" class="form-control" placeholder="ค้นหาผู้ใช้" value="<?php echo $search_keyword; ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">ค้นหา</button>
                </div>
            </div>
        </form>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>รูปภาพ</th>
                    <th>username</th>
                    <th>ชื่อ</th>
                    <th>อีเมล์</th>
                    <th style="width: 200px;">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>
                            <?php if ($row['profile_image']): ?>
                                <img src="<?php echo $base_url . '/uploads/images/' . $row['profile_image']; ?>" alt="User Image" class="img-thumbnail" width="50">
                            <?php else: ?>
                                <img src="<?php echo $admin_url . '/theme/assets/img/default-150x150.png'; ?>" alt="Default Image" class="img-thumbnail" width="50">
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['fullname']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td>
                            <a href="<?php echo $admin_url . '/user-edit.php?id=' . $row['id']; ?>" class='btn btn-warning'>
                            <i class="bi bi-pencil-square me-1"></i>Edit
                            </a>
                            <a href="<?php echo $admin_url . '/user-delete.php?id=' . $row['id']; ?>" class='btn btn-danger' onclick="return confirm('ยืนยันการลบผู้ใช้นี้?');">
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
                    <a class="page-link" href="?page=<?php echo $i; ?>&search_keyword=<?php echo $search_keyword; ?>"><?php echo $i; ?></a>
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
