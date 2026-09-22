<?php
//Database Connection
$host = 'localhost';
$db = 'it30a_lab_db2';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO:: ATTR_DEFAULT_FETCH_MODE =>PDO::FETCH_ASSOC,
    PDO:: ATTR_EMULATE_PREPARES =>false,
];

try{
    $pdo = new PDO ($dsn,$user,$pass,$options);
    echo 'Database connection successful';
} catch (PDOException $e) {
    die("Database connection failed" . $e->getMessage());
}

//Session'
session_start();

//Determin current section
$section = $_GET['section'] ?? 'students';

//CRUD Operations
$action = $_GET['action'] ?? '';


//-----------------------------------------------------------
//Students
//-----------------------------------------------------------

//-----------------------------------------------------------
//Fetch Students
if($section === 'students') {
    $stmt = $pdo ->query("
    SELECT *
    FROM students
    ORDER by student_id DESC
    ");
    $students = $stmt->fetchAll();
}

//Create Student
if($section==='students' && $action==='create') {
     if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $firstName =trim($_POST['student_first_name'] ?? '');
        $lastName =trim($_POST['student_last_name'] ?? '');
        $course =trim($_POST['student_course'] ?? '');
        if ($firstName !== '' && $lastName !== '' && $course !== '') {
            $sql=("
              INSERT INTO students (
              student_first_name,
              student_last_name,
              student_course
              )
              VALUES (?,?,?)
            ");

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                $firstName,
                $lastName,
                $course
            ]);
            //$_SESSION['alert'] = 'Student Saved Successfully';

            header("Location: index.php?section=students");
            exit;
        }
     }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library System</title>
</head>
<body>
    <h1> Simple Library System</h1>
    <nav>
        <a href="index.php?section=students">Students</a>
        <a href="index.php?section=books">Books</a>
        <a href="index.php?section=borrow">Borrows
</nav>
        <hr>
        <?php if ($section === 'students') : ?>
            <h1>Students</h1>
            <p>
                <a href="index.php?section=students&action=create">
                    Add Student
        </a>
        </p>

        <?php if($action==='create'): ?>

            <h2>Add Student </h2>

            <form method="POST">
                <p>
                    <label>First Name</label>
                    <br>
                    <input type="text"
                        name="student_first_name"
                        required
                        />        
            </p>
                <p> 
                    <label>Last Name</label>
                    <br>
                    <input type="text"
                        name="student_last_name"
                        required
                        />   
                </p>
                <p> 
                    <label>Course</label>
                    <br>
                    <input type="text"
                        name="student_course"
                        required
                        />   
                </p>
                <button type="submit">
                    Save
        </button>

        <a href="index.php?section=students">
                  Cancel
        </a>
        </form>


                    <?php else: ?>
                    <table border ="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Course</th>
                    <th>Created At</th>
                    <th>Actions</th>
        </tr>
        <tbody> 
            <?php foreach($students as $student): ?> 
                <tr> 
                    <td> 
                        <?= htmlspecialchars($student['student_id']) ?> 
            </td> 
                     <td> 
                        <?= htmlspecialchars($student['student_first_name']) ?> 
            </td> 
                     <td> 
                        <?= htmlspecialchars($student['student_last_name']) ?> 
            </td> 
                     <td> 
                        <?= htmlspecialchars($student['student_course']) ?> 
            </td> 
                     <td> 
                        <?= htmlspecialchars($student['student_created_at']) ?> 
            </td> 
            <td> 
                <a>Edit</a> 
 
                <a>Delete</a> 
            </td> 
            </tr> 
            <?php endforeach; ?>
            </tbody> 
            </table> 
            <?php endif; ?>


<?php endif; ?> 


        <?php if ($section === 'books') : ?>
            <h1>Books</h1>
        <?php endif; ?>

        <?php if ($section === 'borrow') : ?> 
    <h1>Borrows</h1> 
<?php endif; ?>

</body>
</html>