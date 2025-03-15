<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link" href="index.php">
                <i class="bi bi-house-door"></i> <span>Home</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-toggle="collapse" href="#analyze-nav" role="button">
               <i class="bi bi-person-lines-fill"></i> <span>Lecture</span> <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="analyze-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="pages-add-lecture.php">
                        <i class="bi bi-circle"></i> <span>Add Lecture</span>
                    </a>
                </li>
                <!--li>
                    <a href="../super-admin/pages-release-summery-ward-wise.php">
                        <i class="bi bi-circle"></i> <span>Manage Lectures</span>
                    </a>
                </li-->
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-toggle="collapse" href="#wards-nav" role="button">
               <i class="bi bi-people"></i> <span>Active Students</span> <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="wards-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="pages-add-new-student.php">
                        <i class="bi bi-circle"></i> <span>Add Student</span>
                    </a>
                </li>
                <li>
                    <a href="manage-students.php">
                        <i class="bi bi-circle"></i> <span>Manage Students</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-toggle="collapse" href="#users-nav" role="button">
                <i class="bi bi-people"></i> <span>Former Students</span> <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="users-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="add-former-student.php">
                        <i class="bi bi-circle"></i> <span>Add Former Student</span>
                    </a>
                </li>
                <li>
                    <a href="manage-former-students-edu.php">
                        <i class="bi bi-circle"></i> <span>Manage Fulltime Edu</span>
                    </a>
                </li>
                <li>
                    <a href="manage-former-students-work.php">
                        <i class="bi bi-circle"></i> <span>Manage Fulltime Work</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-toggle="collapse" href="#admins-nav" role="button">
                <i class="bi bi-person-gear"></i> <span>test</span> <i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="admins-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="add-admin.php">
                        <i class="bi bi-circle"></i> <span>Add Admin</span>
                    </a>
                </li>
                <li>
                    <a href="manage-super-admins.php">
                        <i class="bi bi-circle"></i> <span>Manage Admin</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="user-profile.php">
                <i class="bi bi-person-circle"></i> <span>Profile</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="logout.php">
                <i class="bi bi-box-arrow-right"></i> <span>Log Out</span>
            </a>
        </li>

    </ul>
</aside><!-- End Sidebar -->
