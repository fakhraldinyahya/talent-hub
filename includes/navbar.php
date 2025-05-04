<nav class="navbar navbar-expand-lg navbar-light  light-background header sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?php echo URL_ROOT; ?>">
            <img src="<?php echo URL_ROOT; ?>/assets/img/logo.png" alt="<?php echo SITE_NAME; ?>" height="70">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>">
                        <i class="fas fa-home me-1"></i>الرئيسية
                    </a>
                </li>
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' && dirname($_SERVER['PHP_SELF']) == '/posts' ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>/posts/index.php">
                            <i class="fas fa-stream me-1"></i>استكشاف المواهب
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' && dirname($_SERVER['PHP_SELF']) == '/chat' ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>/chat/index.php">
                            <i class="fas fa-comments me-1"></i>الدردشة
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' && dirname($_SERVER['PHP_SELF']) == '/groups' ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>/groups/index.php">
                            <i class="fas fa-users me-1"></i>المجموعات
                        </a>
                    </li>
                <?php endif; ?>

                <!-- قائمة الأقسام المنسدلة -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-list-alt me-1"></i>الأقسام
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
                        <li>
                            <a class="dropdown-item" href="<?php echo URL_ROOT; ?>/categories/programming.php">
                                <i class="fas fa-code me-2"></i>قسم البرمجة
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo URL_ROOT; ?>/categories/drawing.php">
                                <i class="fas fa-paint-brush me-2"></i>قسم الرسم
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo URL_ROOT; ?>/categories/photography.php">
                                <i class="fas fa-camera me-2"></i>قسم التصوير الفوتوغرافي
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo URL_ROOT; ?>/categories/reading.php">
                                <i class="fas fa-book-open me-2"></i>قسم القراءة
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo URL_ROOT; ?>/categories/poetry.php">
                                <i class="fas fa-microphone-alt me-2"></i>قسم الإلقاء والشعر
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo URL_ROOT; ?>/categories/fashion.php">
                                <i class="fas fa-tshirt me-2"></i>قسم تصميم الأزياء
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="<?php echo URL_ROOT; ?>/categories/other.php">
                                <i class="fas fa-box me-2"></i> اخرى
                            </a>
                        </li>





                    </ul>
                </li>


                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>/contact.php">
                        <i class="fas fa-envelope me-1"></i> تواصل معنا
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" href="<?php echo URL_ROOT; ?>/about.php">
                        <i class="fas fa-info-circle me-1"></i> من نحن
                    </a>
                </li>




            </ul>

            <!-- <form class="d-flex mx-auto" action="<?php echo URL_ROOT; ?>/search.php" method="GET">
                <div class="input-group">
                    <input class="form-control" type="search" name="q" placeholder="ابحث عن مواهب أو منشورات..." aria-label="Search" required>
                    <button class="btn btn-light" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form> -->





            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link btn btn-success btn-sm text-white me-2" href="<?php echo URL_ROOT; ?>/posts/create.php">
                            <i class="fas fa-plus-circle me-1"></i>منشور جديد
                        </a>
                    </li>
                    <?php if (isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo URL_ROOT; ?>/admin/index.php">
                                <i class="fas fa-cog me-1"></i>لوحة التحكم
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo URL_ROOT; ?>/assets/uploads/profile/<?php echo $_SESSION['profile_picture'] ?? 'default.jpeg'; ?>" class="rounded-circle me-1" width="24" height="24" alt="Profile">
                            <?php echo $_SESSION['username']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="<?php echo URL_ROOT; ?>/profile.php?username=<?php echo $_SESSION['username']; ?>">
                                    <i class="fas fa-user me-1"></i>الملف الشخصي
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo URL_ROOT; ?>/edit_profile.php">
                                    <i class="fas fa-user-edit me-1"></i>تعديل الملف الشخصي
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?php echo URL_ROOT; ?>/logout.php">
                                    <i class="fas fa-sign-out-alt me-1"></i>تسجيل الخروج
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="fas fa-sign-in-alt me-1"></i>تسجيل الدخول
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-light btn-sm" href="register.php">
                            <i class="fas fa-user-plus me-1"></i>إنشاء حساب
                        </a>
                    </li>
                <?php endif; ?>
                <li class="nav-item dropdown">

            </ul>

            </li>
            </ul>
        </div>
    </div>
</nav>