<?php
    include '../config.php';
    include 'auth.php';

    $page_title = 'Dashboard';
    ob_start();

    // Get total documents this month
    $total_docs_query = "SELECT COUNT(*) as total_docs FROM documents WHERE MONTH(register_date) = MONTH(CURRENT_DATE()) AND YEAR(register_date) = YEAR(CURRENT_DATE())";
    $total_docs_result = mysqli_query($conn, $total_docs_query);
    $total_docs = mysqli_fetch_assoc($total_docs_result)['total_docs'];

    // Get total users
    $total_users_query = "SELECT COUNT(*) as total_users FROM users";
    $total_users_result = mysqli_query($conn, $total_users_query);
    $total_users = mysqli_fetch_assoc($total_users_result)['total_users'];

    // Get total documents
    $total_documents_query = "SELECT COUNT(*) as total_documents FROM documents";
    $total_documents_result = mysqli_query($conn, $total_documents_query);
    $total_documents = mysqli_fetch_assoc($total_documents_result)['total_documents'];

    // Get latest 10 documents
    $latest_docs_query = "SELECT d.document_code, d.topic, d.register_date, d.document_status, u.fullname FROM documents d inner join users u ON d.created_by=u.id ORDER BY d.register_date DESC LIMIT 10";
    $latest_docs_result = mysqli_query($conn, $latest_docs_query);
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="small-box text-bg-primary">
                  <div class="inner">
                    <h3><?php echo $total_docs; ?></h3>
                    <p>เอกสารอัพโหลดเดือนนี้</p>
                  </div>
                  <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625z"></path>
                  </svg>
                  <a href="document-list.php" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    More info <i class="bi bi-link-45deg"></i>
                  </a>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box text-bg-warning">
                <div class="inner">
                    <h3><?php echo $total_users; ?></h3>
                    <p>ผู้ใช้ทั้งหมด</p>
                </div>
                <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M12 2.25a5.25 5.25 0 100 10.5 5.25 5.25 0 000-10.5zM3.75 12a8.25 8.25 0 0116.5 0v.75a.75.75 0 01-.75.75H4.5a.75.75 0 01-.75-.75V12zM12 15a8.25 8.25 0 00-8.25 8.25v.75a.75.75 0 00.75.75h15a.75.75 0 00.75-.75v-.75A8.25 8.25 0 0012 15z"></path>
                </svg>
                <a href="user-list.php" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    More info <i class="bi bi-link-45deg"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box text-bg-danger">
                <div class="inner">
                    <h3><?php echo $total_documents; ?></h3>
                    <p>เอกสารทั้งหมด</p>
                </div>
                <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625z"></path>
                </svg>
                <a href="document-list.php" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    More info <i class="bi bi-link-45deg"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">เอกสารล่าสุด</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                        <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                        <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-lte-toggle="card-remove">
                        <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <div class="table-responsive table-orders">
                        <table class="table m-0">
                            <thead>
                                <tr>
                                <th>รหัสเอกสาร</th>
                                <th>วันที่อัพโหลด</th>
                                <th>สถานะ</th>
                                <th>ชื่อเอกสาร</th>
                                <th>ผู้อัพโหลด</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($doc = mysqli_fetch_assoc($latest_docs_result)): ?>
                                <tr>
                                    <td><?php echo $doc['document_code']; ?></td>
                                    <td><?php echo date('d/m/Y H:i:s', strtotime($doc['register_date'])); ?></td>
                                    <td><?php echo $doc['document_status'] == 1 ? 'เผยแพร่' : ($doc['document_status'] == 2 ? 'ส่วนตัว' : 'ระงับการเผยแพร่'); ?></td>
                                    <td><?php echo $doc['topic']; ?></td>
                                    <td><?php echo $doc['fullname']; ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <!-- /.table-responsive -->
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    <a href="document-list.php" class="btn btn-sm btn-primary float-start">
                        ดูเอกสารทั้งหมด
                    </a>
                </div>
                <!-- /.card-footer -->
            </div>
        </div>
    </div>
</div>

<?php
    $content = ob_get_clean();
    $js_script = '';
    include 'template_master.php';
?>
