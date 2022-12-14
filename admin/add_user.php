<!DOCTYPE html>
<html lang="en">
<?php
session_start();
error_reporting(0);
include("../connect/connect.php");

if (isset($_POST['submit'])) {
    if (
        empty($_POST['ssn']) ||
        empty($_POST['firstname']) ||
        empty($_POST['lastname']) ||
        empty($_POST['gender']) ||
        empty($_POST['birthday']) ||
        empty($_POST['country']) ||
        empty($_POST['phone']) ||
        empty($_POST['email']) ||
        empty($_POST['username']) ||
        empty($_POST['password']) ||
        empty($_POST['address']) ||
        empty($_POST['role'])
    ) {

        $error = '<div class="alert alert-danger alert-dismissible fade show">
																<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
																<strong>Bạn phải điền vào tất cả các ô!</strong>
															</div>';
    } else {
        $check_username = mysqli_query($ktx, "SELECT username FROM users where username = '$_POST[username]' ");


        if (mysqli_num_rows($check_username) > 0) {
            $error = '<div class="alert alert-danger alert-dismissible fade show">
																<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
																<strong>Đã tồn tại tài khoản này!</strong>
															</div>';
        } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) // Validate email address
        {
            $error = '<div class="alert alert-danger alert-dismissible fade show">
																<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
																<strong>Email không hợp lệ!</strong>
															</div>';
        } else if (strlen($_POST['ssn']) != 12) {
            $error = '<div class="alert alert-danger alert-dismissible fade show">
																<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
																<strong>SSN có 12 ký tự!</strong>
															</div>';
        } else if (strlen($_POST['password']) < 6) {
            $error = '<div class="alert alert-danger alert-dismissible fade show">
																<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
																<strong>Mật khẩu phải có nhiều hơn 5 ký tự!</strong>
															</div>';
        } else if (strlen($_POST['phone']) != 10) {
            $error = '<div class="alert alert-danger alert-dismissible fade show">
																<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
																<strong>Số điện thoại không hợp lệ!</strong>
															</div>';
        } else if ($_POST['password'] != $_POST['cpassword']) { //matching passwords
            $error = '<div class="alert alert-danger alert-dismissible fade show">
                                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                                <strong>Mật khẩu chưa chính xác! Vui lòng nhập lại</strong>
                                                            </div>';
        } else {


            if ($_POST['role'] == 'student') {
                if (
                    empty($_POST['room']) ||
                    empty($_POST['court']) ||
                    empty($_POST['year']) ||
                    empty($_POST['university']) ||
                    empty($_POST['student_id']) ||
                    empty($_POST['status'])
                ) {

                    $error = '<div class="alert alert-danger alert-dismissible fade show">
                                                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                                            <strong>Bạn phải điền vào tất cả các ô!</strong>
                                                                        </div>';
                } else {
                    $mql = "insert into users
                                    (id,
                                    ssn,
                                    firstname,
                                    lastname,
                                    gender,
                                    birthday,
                                    country,
                                    phone,
                                    email,
                                    username,
                                    password,
                                    address,
                                    role)
                            values (NULL,
                                     '$_POST[ssn]',
                                     '$_POST[firstname]',
                                     '$_POST[lastname]',
                                     '$_POST[gender]',
                                     '$_POST[birthday]',
                                     '$_POST[country]',
                                     '$_POST[phone]',
                                     '$_POST[email]',
                                     '$_POST[username]',
                                     '$_POST[password]',
                                     '$_POST[address]',
                                     '$_POST[role]')";
                    mysqli_query($ktx, $mql);

                    $mql = "select id, username from users where username = '$_POST[username]'";
                    $result = mysqli_query($ktx, $mql);
                    $row = mysqli_fetch_assoc($result);
                    $user_id = $row['id'];

                    $mql = "insert into students
                                    (id,
                                    user_id,
                                    room_id,
                                    year,
                                    university,
                                    student_id,
                                    status,
                                    start_date,
                                    end_date)
                            values (NULL,
                                    '$user_id',
                                    '$_POST[room]',
                                    '$_POST[year]',
                                    '$_POST[university]',
                                    '$_POST[student_id]',
                                    '$_POST[status]',
                                    '$_POST[start_date]',
                                    '$_POST[end_date]')";
                    mysqli_query($ktx, $mql);

                    $mql = "select (slot-count(s.id)) as count
                            from (rooms as r
                            left outer join students as s on s.room_id = r.id)
                            where s.status = 'Gia hạn' and room_id='$_POST[room]' ";
                    $result = mysqli_query($ktx, $mql);
                    $row = mysqli_fetch_assoc($result);
                    $count = $row['count'];

                    $mql = "update rooms set status='Còn $count giường' where id='$_POST[room]' ";
                    mysqli_query($ktx, $mql);

                    $success = '<div class="alert alert-success alert-dismissible fade show">
																<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
																<strong>Tài khoản được tạo thành công</strong></div>';
                }
            } else {
                $mql = "insert into users
                                    (id,
                                    ssn,
                                    firstname,
                                    lastname,
                                    gender,
                                    birthday,
                                    country,
                                    phone,
                                    email,
                                    username,
                                    password,
                                    address,
                                    role)
                            values (NULL,
                                     '$_POST[ssn]',
                                     '$_POST[firstname]',
                                     '$_POST[lastname]',
                                     '$_POST[gender]',
                                     '$_POST[birthday]',
                                     '$_POST[country]',
                                     '$_POST[phone]',
                                     '$_POST[email]',
                                     '$_POST[username]',
                                     '$_POST[password]',
                                     '$_POST[address]',
                                     '$_POST[role]')";
                mysqli_query($ktx, $mql);

                $mql = "select id, username from users where username = '$_POST[username]'";
                $result = mysqli_query($ktx, $mql);
                $row = mysqli_fetch_assoc($result);
                $user_id = $row['id'];

                $mql = "insert into admin
                                    (id,
                                    user_id)
                            values (NULL,
                                    " . $user_id . ")";
                mysqli_query($ktx, $mql);

                $success = '<div class="alert alert-success alert-dismissible fade show">
																<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
																<strong>Tài khoản được tạo thành công</strong></div>';
            }


        }
    }

}
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../themify-icons/themify-icons.css">
    <title>Admin</title>
</head>

<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <?php include("./header.php"); ?>

        <div class="d-flex">
            <!-- Left Sidebar -->
            <div class="left-sidebar collapse show" id="sidebar">
                <nav class="sidebar-nav">
                    <ul class="sidebar-list">
                        <li class="nav-title">Home</li>
                        <li>
                            <a href="./dashboard.php" class="nav-link">
                                <i class="nav-link-icon ti-dashboard"></i>
                                <span>
                                    Dashboard
                                </span>
                                <i class="ti-angle-right collapse-icon"></i>
                            </a>
                        </li>
                        <li class="nav-title">Log</li>
                        <li class=" tag--active">
                            <a href="./users.php" class="nav-link">
                                <i class="nav-link-icon ti-user"></i>
                                <span>
                                    User
                                </span>
                                <i class="ti-angle-right collapse-icon"></i>
                            </a>
                        </li>
                        <li>
                            <a href="./students.php" class="nav-link">
                                <i class="nav-link-icon ti-face-smile"></i>
                                <span>
                                    Students
                                </span>
                                <i class="ti-angle-right collapse-icon"></i>
                            </a>
                        </li>
                        <li>
                            <a href="./notifications.php" class="nav-link">
                                <i class="nav-link-icon ti-bell"></i>
                                <span>
                                    Notification
                                </span>
                                <i class="ti-angle-right collapse-icon"></i>
                            </a>
                        </li>
                        <li>
                            <a href="./facilities.php" class="nav-link">
                                <i class="nav-link-icon ti-home"></i>
                                <span>
                                    Facilities
                                </span>
                                <i class="ti-angle-right collapse-icon"></i>
                            </a>
                        </li>
                        <li>
                            <a href="./bills.php" class="nav-link">
                                <i class="nav-link-icon ti-notepad"></i>
                                <span>
                                    Bills
                                </span>
                                <i class="ti-angle-right collapse-icon"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <!-- End Left Sidebar -->

            <!-- Page Wrapper -->
            <div class="page-wrapper">
                <!-- User -->
                <div class="user">
                    <div class="row page-title">
                        <h2 class="user-title text-primary col-12">Add User</h2>
                    </div>

                    <?php
                    echo $error;
                    echo $success;
                    ?>

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-body">
                                    <h2 class="card-title">
                                        User Data
                                    </h2>

                                    <div class="card">

                                        <div class="card-body">
                                            <form action='' method='post'>
                                                <div class="form-body">
                                                    <hr>
                                                    <div class="row p-t-20">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Username</label>
                                                                <input type="text" name="username" class="form-control"
                                                                    value="">
                                                            </div>
                                                        </div>
                                                        <!--/span-->
                                                        <div class="col-md-6">
                                                            <div class="form-group has-danger">
                                                                <label class="control-label">SSN</label>
                                                                <input type="text" name="ssn"
                                                                    class="form-control form-control-danger" value=""
                                                                    placeholder="123456789012">
                                                            </div>
                                                        </div>
                                                        <!--/span-->

                                                    </div>
                                                    <!--/row-->
                                                    <div class="row p-t-20">
                                                        <div class="col-md-6">
                                                            <div class="form-group has-danger">
                                                                <label class="control-label">Last Name</label>
                                                                <input type="text" name="lastname"
                                                                    class="form-control form-control-danger" value=""
                                                                    placeholder="jon">
                                                            </div>
                                                        </div>
                                                        <!--/span-->
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">First Name</label>
                                                                <input type="text" name="firstname" class="form-control"
                                                                    placeholder="doe" value="">
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <!--/row-->

                                                    <div class="row p-t-20">
                                                        <div class="col-md-6">
                                                            <div class="form-group has-danger">
                                                                <label class="control-label">Gender</label><br>
                                                                <select
                                                                    style="font-size:medium; padding: 8px; border:1px solid rgb(232,232,232); color:rgb(80,80,80)"
                                                                    name="gender" aria-label="select example">
                                                                    <option>Nam</option>
                                                                    <option>Nữ</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <!--/span-->
                                                        <div class="col-md-6">
                                                            <div class="form-group has-danger">
                                                                <label class="control-label">Birthday</label>
                                                                <input type="date" name="birthday"
                                                                    class="form-control form-control-danger" value=""
                                                                    placeholder="01/01/2002">
                                                            </div>
                                                        </div>
                                                        <!--/span-->

                                                    </div>
                                                    <!--/row-->
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Address</label>
                                                                <input type="text" name="address"
                                                                    class="form-control form-control-danger" value=""
                                                                    placeholder="Address">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Phone</label>
                                                                <input type="text" name="phone"
                                                                    class="form-control form-control-danger" value=""
                                                                    placeholder="Phone">

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Country</label>
                                                                <input type="text" name="country"
                                                                    class="form-control form-control-danger" value=""
                                                                    placeholder="Country">

                                                            </div>
                                                        </div>

                                                        <!--/span-->
                                                        <div class="col-md-6">
                                                            <div class="form-group has-danger">
                                                                <label class="control-label">Email</label>
                                                                <input type="text" name="email"
                                                                    class="form-control form-control-danger" value=""
                                                                    placeholder="example@gmail.com">
                                                            </div>
                                                        </div>
                                                        <!--/span-->
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Role</label><br>
                                                                <select
                                                                    style="font-size:medium; padding: 8px; border:1px solid rgb(232,232,232); color:rgb(80,80,80)"
                                                                    name="role" aria-label="select example">
                                                                    <option selected>admin</option>
                                                                    <option>student</option>
                                                                </select>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="student_infomation">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label">Court</label><br>
                                                                    <select
                                                                        style="font-size:medium; padding: 8px; border:1px solid rgb(232,232,232); color:rgb(80,80,80)"
                                                                        name="court" aria-label="select example">
                                                                        <option value="0" type="0">Chọn Tòa</option>

                                                                        <?php
                                                                        $query = "select * from courts";
                                                                        $result = mysqli_query($ktx, $query);
                                                                        while ($court = mysqli_fetch_array($result)) {
                                                                            echo "<option value='" . $court['id'] . "' type='" . $court['type'] . "'>" . $court['name'] . "</option>";
                                                                        }
                                                                        ?>
                                                                    </select>

                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label">Room</label><br>
                                                                    <select
                                                                        style="font-size:medium; padding: 8px; border:1px solid rgb(232,232,232); color:rgb(80,80,80)"
                                                                        name="room" aria-label="select example">
                                                                        <option value="0" court="0">Chọn Phòng</option>

                                                                        <?php
                                                                        $query = "select *, count(s_id) as slot_count
                                                                                from (rooms
                                                                                left outer join (select id as s_id, room_id from students) as s on rooms.id = s.room_id)
                                                                                GROUP by id;";
                                                                        $result = mysqli_query($ktx, $query);
                                                                        while ($room = mysqli_fetch_array($result)) {

                                                                            if ($room['slot_count'] != $room['slot'])
                                                                                echo "<option value='" . $room['id'] . "' court='" . $room['court_id'] . "'>" . $room['room_number'] . "</option>";
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label">Year</label>
                                                                    <input type="text" name="year"
                                                                        class="form-control form-control-danger"
                                                                        value="" placeholder="Year">

                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label">University</label>
                                                                    <input type="text" name="university"
                                                                        class="form-control form-control-danger"
                                                                        value="" placeholder="University">

                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label">Student ID</label>
                                                                    <input type="text" name="student_id"
                                                                        class="form-control form-control-danger"
                                                                        value="" placeholder="Student ID">

                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="control-label">Status</label>
                                                                    <input type="text" name="status"
                                                                        class="form-control form-control-danger"
                                                                        value="" placeholder="Status">

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row p-t-20">
                                                            <div class="col-md-6">
                                                                <div class="form-group has-danger">
                                                                    <label class="control-label">Start Date</label>
                                                                    <input type="date" name="start_date"
                                                                        class="form-control form-control-danger"
                                                                        value="" placeholder="01/01/2002">
                                                                </div>
                                                            </div>
                                                            <!--/span-->
                                                            <div class="col-md-6">
                                                                <div class="form-group has-danger">
                                                                    <label class="control-label">End Date</label>
                                                                    <input type="date" name="end_date"
                                                                        class="form-control form-control-danger"
                                                                        value="" placeholder="01/01/2002">
                                                                </div>
                                                            </div>
                                                            <!--/span-->

                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Password</label>
                                                                <input type="password" name="password"
                                                                    class="form-control form-control-danger" value=""
                                                                    placeholder="Nhập Mật khẩu">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="control-label">Confirm Password</label>
                                                                <input type="password" name="cpassword"
                                                                    class="form-control form-control-danger" value=""
                                                                    placeholder="Confirm Password">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-actions mt-16">
                                                    <input type="submit" name="submit" class="btn btn-success"
                                                        value="Add">
                                                    <a href="users.php" class="btn btn-inverse">Cancel</a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- End User -->

                <!-- Footer -->
                <div class="footer">
                    <h3 class="copyright">© 2022 All right reserved</h3>
                </div>
                <!-- End Footer -->
            </div>
            <!-- End Wrapper -->
        </div>
    </div>
    <!-- End Wrapper -->







    <script src="../js/jquery-3.6.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>
    <script src="../js/users.js"></script>
    <script>
    changeRole();

    $("select[name='gender']")
        .change(changeGender)

    $("select[name='role']")
        .change(changeRole)

    $("select[name='court']")
        .change(changeCourt)

    function changeGender() {
        $(`select[name='court'] option`).hide();
        $(`select[name='court'] option:selected`).removeAttr("selected");

        const gender = $("select[name='gender'] option:selected").text();
        const courts = $(`select[name='court'] option[type='${gender}']`);

        courts.show();
        courts.first().attr("selected", "selected");
        changeCourt()
    }

    function changeRole() {
        const role = $("select[name='role'] option:selected");

        if (role.val() == 'student') {
            $(".student_infomation").show();
            changeGender();
        } else {
            $(".student_infomation").hide();
        }

    }

    function changeCourt() {
        $(`select[name='room'] option`).hide();
        $(`select[name='room'] option:selected`).removeAttr("selected");

        const court = $("select[name='court'] option:selected").val();
        const rooms = $(`select[name='room'] option[court='${court}']`);

        rooms.show();
        rooms.first().attr("selected", "selected");
    }
    </script>
</body>

</html>