<?php
include '../config.php';
include 'auth.php';
$page_title = 'เพิ่มเอกสาร';
ob_start();

// Get categories
$categories_query = "SELECT * FROM categories ORDER BY category_name";
$categories_result = mysqli_query($conn, $categories_query);

// Generate document code
$document_code = '';
$query = "SELECT current_number FROM running_numbers WHERE prefix = 'DOC'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $next_number = $row['current_number'] + 1;
    $document_code = 'DOC-' . date('Y') . '-' . str_pad($next_number, 5, '0', STR_PAD_LEFT);
} else {
    $next_number = 1;
    $document_code = 'DOC-' . date('Y') . '-' . str_pad($next_number, 5, '0', STR_PAD_LEFT);
    mysqli_query($conn, "INSERT INTO running_numbers (prefix, current_number) VALUES ('DOC', 0)");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date_parts = explode('/', $_POST['register_date']);

    $document_code = $_POST['document_code'];
    $topic = $_POST['topic'];
    $register_date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
    $document_status = $_POST['document_status'];
    $reference = $_POST['reference'];
    $store_location = $_POST['store_location'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];
    $created_by = $_SESSION['admin_id'];
    $created_at = date('Y-m-d H:i:s');

    // Handle file upload
    $file_name = '';
    if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] == 0) {
        $target_dir = "../uploads/documents/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES["document_file"]["name"], PATHINFO_EXTENSION);
        $unique_file_name = 'doc_' . date('YmdHis') . rand(1000, 9999) . '.' . $file_extension;
        $target_file = $target_dir . $unique_file_name;
        
        if (move_uploaded_file($_FILES["document_file"]["tmp_name"], $target_file)) {
            $file_name = $unique_file_name;
        }
    }

    $query = "INSERT INTO documents (document_code, topic, register_date, document_status, reference, 
              store_location, file_name, description, category_id, created_by, created_at) 
              VALUES ('$document_code', '$topic', '$register_date', '$document_status', '$reference', 
              '$store_location', '$file_name', '$description', $category_id, $created_by, '$created_at')";
    
    if (mysqli_query($conn, $query)) {
        // Update running number
        mysqli_query($conn, "UPDATE running_numbers SET current_number = $next_number WHERE prefix = 'DOC'");
        header('Location: ' . $admin_url . '/document-list.php');
        exit;
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}
?>
<form method="POST" enctype="multipart/form-data">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">เพิ่มเอกสาร</h3>
        </div>
        <div class="card-body">
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="document_code">รหัสเอกสาร</label>
                        <div class="input-group">
                            <input type="text" name="document_code" id="document_code" class="form-control" value="<?php echo $document_code; ?>" readonly>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <input type="checkbox" id="enable_manual_code" class="me-1">
                                    <label for="enable_manual_code" class="mb-0">กำหนดเอง</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="register_date">วันที่ลงทะเบียน</label>
                        <input type="text" name="register_date" id="register_date" class="form-control datepicker" value="" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="topic">หัวข้อเอกสาร</label>
                <input type="text" name="topic" id="topic" class="form-control" required>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category_id">หมวดหมู่</label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            <option value="">-- เลือกหมวดหมู่ --</option>
                            <?php 
                            mysqli_data_seek($categories_result, 0);
                            while ($cat = mysqli_fetch_assoc($categories_result)): 
                            ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo $cat['category_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="document_status">สถานะเอกสาร</label>
                        <select name="document_status" id="document_status" class="form-control" required>
                            <option value="draft">ร่าง (Draft)</option>
                            <option value="pending">รออนุมัติ (Pending)</option>
                            <option value="approved">อนุมัติแล้ว (Approved)</option>
                            <option value="active">ใช้งานอยู่ (Active)</option>
                            <option value="cancelled">ยกเลิก (Cancelled)</option>
                            <option value="expired">หมดอายุ (Expired)</option>
                            <option value="archived">จัดเก็บ (Archived)</option>
                            <option value="exported">ส่งออก (Exported)</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="reference">อ้างอิงเอกสาร</label>
                <input type="text" name="reference" id="reference" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="store_location">สถานที่จัดเก็บเอกสาร</label>
                <input type="text" name="store_location" id="store_location" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="document_file">ไฟล์เอกสาร</label>
                <input type="file" name="document_file" id="document_file" class="form-control" required>
                <small class="text-muted">อัพโหลดไฟล์เอกสาร (PDF, DOC, DOCX, XLS, XLSX)</small>
            </div>
            
            <div class="form-group">
                <label for="description">รายละเอียด</label>
                <textarea name="description" id="description" class="form-control" rows="4"></textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="bi bi-floppy me-1"></i>บันทึกข้อมูล</button>
            <a href="<?php echo $admin_url . '/document-list.php'; ?>" class='btn btn-secondary'>ย้อนกลับ</a>
        </div>
    </div>
</form>
<?php
$content = ob_get_clean();
$js_script = '<script src="assets/js/document.js"></script>';
include 'template_master.php';
?>
