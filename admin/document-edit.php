<?php
include '../config.php';
include 'auth.php';
$page_title = 'แก้ไขเอกสาร';
ob_start();

// Get document by ID
$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM documents WHERE id = $id");
$document = mysqli_fetch_assoc($result);
// Convert register_date from database format (Y-m-d) to display format (d/m/Y)
if ($document['register_date']) {
    $date = new DateTime($document['register_date']);
    $document['register_date'] = $date->format('d/m/Y');
}

// Get categories
$categories_query = "SELECT * FROM categories ORDER BY category_name";
$categories_result = mysqli_query($conn, $categories_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date_parts = explode('/', $_POST['register_date']);

    $topic = $_POST['topic'];
    $register_date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
    $document_status = $_POST['document_status'];
    $reference = $_POST['reference'];
    $store_location = $_POST['store_location'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];
    $updated_by = $_SESSION['admin_id'];
    $updated_at = date('Y-m-d H:i:s');

    // Handle file upload
    $file_name = $document['file_name'];
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
            // Remove old file
            if ($file_name && file_exists($target_dir . $file_name)) {
                unlink($target_dir . $file_name);
            }
            $file_name = $unique_file_name;
        }
    }

    $query = "UPDATE documents SET 
              topic = '$topic', 
              register_date = '$register_date', 
              document_status = '$document_status', 
              reference = '$reference', 
              store_location = '$store_location', 
              file_name = '$file_name', 
              description = '$description', 
              category_id = $category_id, 
              updated_by = $updated_by, 
              updated_at = '$updated_at' 
              WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
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
            <h3 class="card-title">แก้ไขเอกสาร: <?php echo $document['topic']; ?></h3>
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
                        <input type="text" name="document_code" id="document_code" class="form-control" value="<?php echo $document['document_code']; ?>" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="register_date">วันที่ลงทะเบียน</label>
                        <input type="text" name="register_date" id="register_date" class="form-control datepicker" value="<?php echo $document['register_date']; ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="topic">หัวข้อเอกสาร</label>
                <input type="text" name="topic" id="topic" class="form-control" value="<?php echo $document['topic']; ?>" required>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category_id">หมวดหมู่</label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            <option value="">-- เลือกหมวดหมู่ --</option>
                            <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($document['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo $cat['category_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="document_status">สถานะเอกสาร</label>
                        <select name="document_status" id="document_status" class="form-control" required>
                            <option value="draft" <?php echo ($document['document_status'] == 'draft') ? 'selected' : ''; ?>>ร่าง (Draft)</option>
                            <option value="pending" <?php echo ($document['document_status'] == 'pending') ? 'selected' : ''; ?>>รออนุมัติ (Pending)</option>
                            <option value="approved" <?php echo ($document['document_status'] == 'approved') ? 'selected' : ''; ?>>อนุมัติแล้ว (Approved)</option>
                            <option value="active" <?php echo ($document['document_status'] == 'active') ? 'selected' : ''; ?>>ใช้งานอยู่ (Active)</option>
                            <option value="cancelled" <?php echo ($document['document_status'] == 'cancelled') ? 'selected' : ''; ?>>ยกเลิก (Cancelled)</option>
                            <option value="expired" <?php echo ($document['document_status'] == 'expired') ? 'selected' : ''; ?>>หมดอายุ (Expired)</option>
                            <option value="archived" <?php echo ($document['document_status'] == 'archived') ? 'selected' : ''; ?>>จัดเก็บ (Archived)</option>
                            <option value="exported" <?php echo ($document['document_status'] == 'exported') ? 'selected' : ''; ?>>ส่งออก (Exported)</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="reference">อ้างอิงเอกสาร</label>
                <input type="text" name="reference" id="reference" class="form-control" value="<?php echo $document['reference']; ?>">
            </div>
            
            <div class="form-group">
                <label for="store_location">สถานที่จัดเก็บเอกสาร</label>
                <input type="text" name="store_location" id="store_location" class="form-control" value="<?php echo $document['store_location']; ?>">
            </div>
            
            <div class="form-group">
                <label for="document_file">ไฟล์เอกสาร</label>
                <input type="file" name="document_file" id="document_file" class="form-control">
                <small class="text-muted">อัพโหลดไฟล์เอกสารใหม่ (PDF, DOC, DOCX, XLS, XLSX)</small>
                <?php if ($document['file_name']): ?>
                    <div class="mt-2">
                        <a href="<?php echo $base_url . '/uploads/documents/' . $document['file_name']; ?>" target="_blank" class="btn btn-info btn-sm">
                            <i class="bi bi-file-earmark-text me-1"></i>ดูไฟล์เอกสารปัจจุบัน
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="description">รายละเอียด</label>
                <textarea name="description" id="description" class="form-control" rows="4"><?php echo $document['description']; ?></textarea>
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
