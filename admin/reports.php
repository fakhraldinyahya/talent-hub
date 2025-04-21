<?php
require_once '../config/config.php';
require_once '../config/db.php';
require_once '../classes/User.php';
require_once '../classes/Post.php';
require_once '../classes/Admin.php';

// التحقق مما إذا كان المستخدم مسجل الدخول ومسؤول
if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// إنشاء كائنات الفئات
$database = new Database();
$user = new User($database);
$post = new Post($database);
$admin = new Admin($database);

// معالجة الإجراءات
if (isset($_GET['action'])) {
    $action = sanitize($_GET['action']);
    
    // حذف تقرير
    if ($action === 'delete_report' && isset($_GET['id'])) {
        $id = sanitize($_GET['id']);
        
        if ($admin->deleteReport($id)) {
            flash('تم حذف التقرير بنجاح', 'success');
        } else {
            flash('حدث خطأ أثناء حذف التقرير', 'danger');
        }
        
        redirect('admin/reports.php');
    }
    
    // تجاهل تقرير
    if ($action === 'ignore_report' && isset($_GET['id'])) {
        $id = sanitize($_GET['id']);
        
        if ($admin->updateReportStatus($id, 'ignored')) {
            flash('تم تجاهل التقرير بنجاح', 'success');
        } else {
            flash('حدث خطأ أثناء تحديث حالة التقرير', 'danger');
        }
        
        redirect('admin/reports.php');
    }
    
    // معالجة تقرير
    if ($action === 'resolve_report' && isset($_GET['id'])) {
        $id = sanitize($_GET['id']);
        
        if ($admin->updateReportStatus($id, 'resolved')) {
            flash('تم تحديد التقرير كمعالج بنجاح', 'success');
        } else {
            flash('حدث خطأ أثناء تحديث حالة التقرير', 'danger');
        }
        
        redirect('admin/reports.php');
    }
    
    // حذف محتوى مبلغ عنه
    if ($action === 'delete_content' && isset($_GET['report_id']) && isset($_GET['content_type']) && isset($_GET['content_id'])) {
        $report_id = sanitize($_GET['report_id']);
        $content_type = sanitize($_GET['content_type']);
        $content_id = sanitize($_GET['content_id']);
        
        $deleted = false;
        
        if ($content_type === 'post') {
            $deleted = $post->deletePost($content_id, $_SESSION['user_id']);
        } elseif ($content_type === 'comment') {
            $deleted = $comment->deleteComment($content_id, $_SESSION['user_id']);
        }
        
        if ($deleted) {
            // تحديث حالة التقرير
            $admin->updateReportStatus($report_id, 'resolved');
            
            flash('تم حذف المحتوى المبلغ عنه وتحديث حالة التقرير', 'success');
        } else {
            flash('حدث خطأ أثناء حذف المحتوى', 'danger');
        }
        
        redirect('admin/reports.php');
    }
}

// الفلترة حسب حالة التقرير
$status = isset($_GET['status']) ? sanitize($_GET['status']) : 'pending';

// الحصول على التقارير
if ($status === 'all') {
    $reports = $admin->getAllReports();
} else {
    $reports = $admin->getReportsByStatus($status);
}

// تعيين عنوان الصفحة
$page_title = 'إدارة التقارير';

require_once '../includes/header.php';
?>

<div class="container-fluid mt-5">
    <div class="row">
        <!-- القائمة الجانبية -->
        <div class="col-lg-2 mb-4">
            <?php require_once 'includes/sidebar.php'; ?>
        </div>
        
        <!-- المحتوى الرئيسي -->
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><?php echo $page_title; ?></h2>
                <div class="btn-group">
                    <a href="<?php echo URL_ROOT; ?>/admin/reports.php?status=pending" class="btn btn-outline-primary <?php echo $status === 'pending' ? 'active' : ''; ?>">قيد الانتظار</a>
                    <a href="<?php echo URL_ROOT; ?>/admin/reports.php?status=resolved" class="btn btn-outline-primary <?php echo $status === 'resolved' ? 'active' : ''; ?>">معالجة</a>
                    <a href="<?php echo URL_ROOT; ?>/admin/reports.php?status=ignored" class="btn btn-outline-primary <?php echo $status === 'ignored' ? 'active' : ''; ?>">متجاهلة</a>
                    <a href="<?php echo URL_ROOT; ?>/admin/reports.php?status=all" class="btn btn-outline-primary <?php echo $status === 'all' ? 'active' : ''; ?>">الكل</a>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <?php if (empty($reports)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-flag fa-3x text-muted mb-3"></i>
                            <h5>لا توجد تقارير <?php echo $status === 'pending' ? 'قيد الانتظار' : ($status === 'resolved' ? 'معالجة' : ($status === 'ignored' ? 'متجاهلة' : '')); ?></h5>
                            <p class="text-muted">ستظهر التقارير المقدمة من المستخدمين هنا.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>المبلغ</th>
                                        <th>نوع المحتوى</th>
                                        <th>سبب البلاغ</th>
                                        <th>الحالة</th>
                                        <th>تاريخ البلاغ</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reports as $index => $report): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $report->reporter_picture; ?>" class="rounded-circle me-2" width="32" height="32" alt="صورة المستخدم">
                                                    <a href="<?php echo URL_ROOT; ?>/profile.php?username=<?php echo $report->reporter_username; ?>" class="text-decoration-none">
                                                        <?php echo $report->reporter_username; ?>
                                                    </a>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($report->content_type === 'post'): ?>
                                                    <span class="badge bg-primary">منشور</span>
                                                <?php elseif ($report->content_type === 'comment'): ?>
                                                    <span class="badge bg-secondary">تعليق</span>
                                                <?php elseif ($report->content_type === 'user'): ?>
                                                    <span class="badge bg-danger">مستخدم</span>
                                                <?php endif; ?>
                                                <a href="<?php echo URL_ROOT; ?>/admin/view_report.php?id=<?php echo $report->id; ?>" class="btn btn-sm btn-link">عرض</a>
                                            </td>
                                            <td><?php echo $report->reason; ?></td>
                                            <td>
                                                <?php if ($report->status === 'pending'): ?>
                                                    <span class="badge bg-warning text-dark">قيد الانتظار</span>
                                                <?php elseif ($report->status === 'resolved'): ?>
                                                    <span class="badge bg-success">معالج</span>
                                                <?php elseif ($report->status === 'ignored'): ?>
                                                    <span class="badge bg-secondary">متجاهل</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($report->created_at)); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?php echo URL_ROOT; ?>/admin/view_report.php?id=<?php echo $report->id; ?>" class="btn btn-outline-primary" title="عرض التفاصيل">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    <?php if ($report->status === 'pending'): ?>
                                                        <a href="<?php echo URL_ROOT; ?>/admin/reports.php?action=resolve_report&id=<?php echo $report->id; ?>" class="btn btn-outline-success" title="تحديد كمعالج">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                        <a href="<?php echo URL_ROOT; ?>/admin/reports.php?action=ignore_report&id=<?php echo $report->id; ?>" class="btn btn-outline-secondary" title="تجاهل">
                                                            <i class="fas fa-ban"></i>
                                                        </a>
                                                        
                                                        <?php if ($report->content_type !== 'user'): ?>
                                                            <a href="<?php echo URL_ROOT; ?>/admin/reports.php?action=delete_content&report_id=<?php echo $report->id; ?>&content_type=<?php echo $report->content_type; ?>&content_id=<?php echo $report->content_id; ?>" class="btn btn-outline-danger" title="حذف المحتوى" onclick="return confirm('هل أنت متأكد من رغبتك في حذف المحتوى المبلغ عنه؟');">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                    
                                                    <a href="<?php echo URL_ROOT; ?>/admin/reports.php?action=delete_report&id=<?php echo $report->id; ?>" class="btn btn-outline-danger" title="حذف التقرير" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذا التقرير؟');">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>