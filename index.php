<<<<<<< HEAD

<?php
// Database Connection
=======
<?php
//Database Connection
>>>>>>> f1c0ce2a69e58925c63bbc422c0eb12febc5b497
$host = 'localhost';
$db = 'it30a_lab_db2';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

<<<<<<< HEAD
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine the current Section
$section = $_GET['section'] ?? 'students';
$action = $_GET['action'] ?? '';
$id = (int) ($_GET['id'] ?? 0);

// ----------------------------------------------
// FETCH STUDENTS
// ----------------------------------------------

if ($section === 'students' ||
    ($section === 'borrows' && $action === 'create')) {

    $stmt = $pdo->query("
        SELECT *
        FROM students
        ORDER BY student_id DESC
    ");

    $students = $stmt->fetchAll();
}

// ----------------------------------------------
// FETCH BOOKS
// ----------------------------------------------

if ($section === 'books' || $section === 'borrows') {

    $stmt = $pdo->query("
        SELECT *
        FROM books
        ORDER BY book_id DESC
    ");

    $books = $stmt->fetchAll();
}

// ----------------------------------------------
// STUDENTS
// ----------------------------------------------

// Create Student
if ($section === 'students' && $action === 'create') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $course = trim($_POST['course'] ?? '');

        if ($first_name !== '' &&
            $last_name !== '' &&
            $course !== '') {

            $stmt = $pdo->prepare("
                INSERT INTO students (
                    student_first_name,
                    student_last_name,
                    student_course
                )
                VALUES (?, ?, ?)
            ");

            $stmt->execute([
                $first_name,
                $last_name,
                $course
            ]);

            $_SESSION['alert'] = 'Student saved successfully.';

            header('Location: index.php?section=students');
            exit;

        } else {
            $_SESSION['alert'] = 'Please complete all fields.';
        }
    }
}

// Update Student
if ($section === 'students' && $action === 'update') {

    $stmt = $pdo->prepare("
        SELECT *
        FROM students
        WHERE student_id = ?
    ");

    $stmt->execute([$id]);
    $student = $stmt->fetch();

    if (!$student) {
        $_SESSION['alert'] = 'Student not found.';
        header('Location: index.php?section=students');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $course = trim($_POST['course'] ?? '');

        if ($first_name !== '' &&
            $last_name !== '' &&
            $course !== '') {

            $stmt = $pdo->prepare("
                UPDATE students
                SET student_first_name = ?,
                    student_last_name = ?,
                    student_course = ?
                WHERE student_id = ?
            ");

            $stmt->execute([
                $first_name,
                $last_name,
                $course,
                $id
            ]);

            $_SESSION['alert'] = 'Student updated successfully.';

            header('Location: index.php?section=students');
            exit;

        } else {
            $_SESSION['alert'] = 'Please complete all fields.';
        }
    }
}

// Delete Student
if ($section === 'students' && $action === 'delete') {

    if ($id <= 0) {

        $_SESSION['alert'] = 'Invalid student ID.';

    } else {

        $stmt = $pdo->prepare("
            SELECT student_id
            FROM students
            WHERE student_id = ?
        ");

        $stmt->execute([$id]);

        if (!$stmt->fetch()) {

            $_SESSION['alert'] = 'Student not found.';

        } else {

            // Check existing borrow records
            $stmt = $pdo->prepare("
                SELECT borrow_id
                FROM borrows
                WHERE student_id = ?
                LIMIT 1
            ");

            $stmt->execute([$id]);

            if ($stmt->fetch()) {

                $_SESSION['alert'] =
                    'Cannot delete student because borrow records exist.';

            } else {

                $stmt = $pdo->prepare("
                    DELETE FROM students
                    WHERE student_id = ?
                ");

                $stmt->execute([$id]);

                $_SESSION['alert'] = 'Student deleted successfully.';
            }
        }
    }

    header('Location: index.php?section=students');
    exit;
}

// ----------------------------------------------
// BOOKS
// ----------------------------------------------

// Create Book
if ($section === 'books' && $action === 'create') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $bookTitle = trim($_POST['book_title'] ?? '');
        $bookAuthor = trim($_POST['book_author'] ?? '');
        $bookCategory = trim($_POST['book_category'] ?? '');

        if ($bookTitle !== '' &&
            $bookAuthor !== '' &&
            $bookCategory !== '') {

            $stmt = $pdo->prepare("
                INSERT INTO books (
                    book_title,
                    book_author,
                    book_category
                )
                VALUES (?, ?, ?)
            ");

            $stmt->execute([
                $bookTitle,
                $bookAuthor,
                $bookCategory
            ]);

            $_SESSION['alert'] = 'Book saved successfully.';

            header('Location: index.php?section=books');
            exit;

        } else {
            $_SESSION['alert'] = 'Please complete all fields.';
        }
    }
}

// Update Book
if ($section === 'books' && $action === 'update') {

    $stmt = $pdo->prepare("
        SELECT *
        FROM books
        WHERE book_id = ?
    ");

    $stmt->execute([$id]);
    $book = $stmt->fetch();

    if (!$book) {
        $_SESSION['alert'] = 'Book not found.';
        header('Location: index.php?section=books');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $bookTitle = trim($_POST['book_title'] ?? '');
        $bookAuthor = trim($_POST['book_author'] ?? '');
        $bookCategory = trim($_POST['book_category'] ?? '');

        if ($bookTitle !== '' &&
            $bookAuthor !== '' &&
            $bookCategory !== '') {

            $stmt = $pdo->prepare("
                UPDATE books
                SET book_title = ?,
                    book_author = ?,
                    book_category = ?
                WHERE book_id = ?
            ");

            $stmt->execute([
                $bookTitle,
                $bookAuthor,
                $bookCategory,
                $id
            ]);

            $_SESSION['alert'] = 'Book updated successfully.';

            header('Location: index.php?section=books');
            exit;

        } else {
            $_SESSION['alert'] = 'Please complete all fields.';
        }
    }
}

// Delete Book
if ($section === 'books' && $action === 'delete') {

    if ($id <= 0) {

        $_SESSION['alert'] = 'Invalid book ID.';

    } else {

        $stmt = $pdo->prepare("
            SELECT book_id
            FROM books
            WHERE book_id = ?
        ");

        $stmt->execute([$id]);

        if (!$stmt->fetch()) {

            $_SESSION['alert'] = 'Book not found.';

        } else {

            // Check existing borrow records
            $stmt = $pdo->prepare("
                SELECT borrow_id
                FROM borrows
                WHERE book_id = ?
                LIMIT 1
            ");

            $stmt->execute([$id]);

            if ($stmt->fetch()) {

                $_SESSION['alert'] =
                    'Cannot delete book because borrow records exist.';

            } else {

                $stmt = $pdo->prepare("
                    DELETE FROM books
                    WHERE book_id = ?
                ");

                $stmt->execute([$id]);

                $_SESSION['alert'] = 'Book deleted successfully.';
            }
        }
    }

    header('Location: index.php?section=books');
    exit;
}

// ----------------------------------------------
// BORROWS
// ----------------------------------------------

// Fetch Borrow Records
if ($section === 'borrows') {

    $stmt = $pdo->query("
        SELECT
            borrow.borrow_id,
            borrow.student_id,
            students.student_first_name,
            students.student_last_name,
            borrow.book_id,
            books.book_title,
            borrow.borrow_date,
            borrow.borrow_return_date
        FROM borrow
        INNER JOIN students
            ON borrow.student_id = students.student_id
        INNER JOIN books
            ON borrow.book_id = books.book_id
        ORDER BY borrow.borrow_id DESC
    ");

    $borrows = $stmt->fetchAll();
}

// Borrow a Book
if ($section === 'borrows' && $action === 'create') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $studentId = (int) ($_POST['student_id'] ?? 0);
        $bookId = (int) ($_POST['book_id'] ?? 0);

        if ($studentId <= 0 || $bookId <= 0) {

            $_SESSION['alert'] =
                'Please select a student and a book.';

        } else {

            // Check Student
            $stmt = $pdo->prepare("
                SELECT student_id
                FROM students
                WHERE student_id = ?
            ");

            $stmt->execute([$studentId]);

            if (!$stmt->fetch()) {

                $_SESSION['alert'] = 'Student not found.';

            } else {

                // Check Book
                $stmt = $pdo->prepare("
                    SELECT book_id
                    FROM books
                    WHERE book_id = ?
                ");

                $stmt->execute([$bookId]);

                if (!$stmt->fetch()) {

                    $_SESSION['alert'] = 'Book not found.';

                } else {

                    // Check if Student has an unreturned book
                    $stmt = $pdo->prepare("
                        SELECT borrow_id
                        FROM borrow
                        WHERE student_id = ?
                          AND borrow_return_date IS NULL
                        LIMIT 1
                    ");

                    $stmt->execute([$studentId]);
                    $studentBorrow = $stmt->fetch();

                    if ($studentBorrow) {

                        $_SESSION['alert'] =
                            'This student already has an unreturned book.';

                    } else {

                        // Check if Book is already borrowed
                        $stmt = $pdo->prepare("
                            SELECT borrow_id
                            FROM borrow
                            WHERE book_id = ?
                              AND borrow_return_date IS NULL
                            LIMIT 1
                        ");

                        $stmt->execute([$bookId]);
                        $bookBorrow = $stmt->fetch();

                        if ($bookBorrow) {

                            $_SESSION['alert'] =
                                'This book has not been returned yet.';

                        } else {

                            // Create Borrow Record
                            $stmt = $pdo->prepare("
                                INSERT INTO borrow (
                                    student_id,
                                    book_id,
                                    borrow_date
                                )
                                VALUES (?, ?, NOW())
                            ");

                            $stmt->execute([
                                $studentId,
                                $bookId
                            ]);

                            $_SESSION['alert'] =
                                'Book borrowed successfully.';
                        }
                    }
                }
            }
        }

        header('Location: index.php?section=borrows');
        exit;
    }
}

// Return a Book
if ($section === 'borrows' && $action === 'update') {

    if ($id <= 0) {

        $_SESSION['alert'] = 'Invalid borrow ID.';

    } else {

        $stmt = $pdo->prepare("
            UPDATE borrow
            SET borrow_return_date = NOW()
            WHERE borrow_id = ?
              AND borrow_return_date IS NULL
        ");

        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['alert'] = 'Book returned successfully.';
        } else {
            $_SESSION['alert'] =
                'Borrow record not found or book already returned.';
        }
    }

    header('Location: index.php?section=borrows');
    exit;
=======
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

//Update Student
if($section==='students'&& $action==='update') {
    $studentId = (int) ($_GET['id'] ?? 0);


    //Retrieve Student Information
    $stmt = $pdo->prepare("
        SELECT * 
        FROM students
        WHERE student_id =?
    ");

    $stmt->execute([$studentId]);

    $student = $stmt->fetch();





    // Update Student Info

    if($section==='students' && $action==='update') {
     if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $firstName =trim($_POST['student_first_name'] ?? '');
        $lastName =trim($_POST['student_last_name'] ?? '');
        $course =trim($_POST['student_course'] ?? '');
        if ($firstName !== '' && $lastName !== '' && $course !== '') {
            $sql=("
              UPDATE students 
              SET

                      student_first_name =?,
                      student_last_name =?,
                      student_course =?
                WHERE student_id=?
            ");

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                $firstName,
                $lastName,
                $course,
                $studentId
            ]);
            
            $_SESSION['alert'] = 'Student Updated Successfully';

            header("Location: index.php?section=students");
            exit;
        }
     }
}  
>>>>>>> f1c0ce2a69e58925c63bbc422c0eb12febc5b497
}
?>

<!DOCTYPE html>
<html lang="en">
<<<<<<< HEAD

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Systems</title>
</head>

<body>

<h1>Simple Library Systems</h1>

<nav>
    <a href="index.php?section=students">Students</a>
    <a href="index.php?section=books">Books</a>
    <a href="index.php?section=borrows">Borrows</a>
</nav>

<hr>

<!-- STUDENTS -->

<?php if ($section === 'students'): ?>

    <h1>Students</h1>

    <p>
        <a href="index.php?section=students&action=create">
            Add New Student
        </a>
    </p>

    <?php if ($action === 'create'): ?>

        <h2>Add New Student</h2>

        <form method="POST">

            <p>
                <label>First Name:</label><br>
                <input type="text" name="first_name" required>
            </p>

            <p>
                <label>Last Name:</label><br>
                <input type="text" name="last_name" required>
            </p>

            <p>
                <label>Course:</label><br>
                <input type="text" name="course" required>
            </p>

            <button type="submit">Save</button>

            <a href="index.php?section=students">Cancel</a>

        </form>

    <?php elseif ($action === 'update'): ?>

        <h2>Update Student</h2>

        <form method="POST">

            <p>
                <label>First Name:</label><br>
                <input
                    type="text"
                    name="first_name"
                    value="<?= htmlspecialchars($student['student_first_name']) ?>"
                    required
                >
            </p>

            <p>
                <label>Last Name:</label><br>
                <input
                    type="text"
                    name="last_name"
                    value="<?= htmlspecialchars($student['student_last_name']) ?>"
                    required
                >
            </p>

            <p>
                <label>Course:</label><br>
                <input
                    type="text"
                    name="course"
                    value="<?= htmlspecialchars($student['student_course']) ?>"
                    required
                >
            </p>

            <button type="submit">Update</button>

            <a href="index.php?section=students">Cancel</a>

        </form>

    <?php else: ?>

        <table border="1">

=======
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

            <h2>Add New Student </h2>

                <form method="POST">
                        <p>
                            <label>First Name</label>
                        <br>
                        <input type="text"
                             name="student_first_name"
            value="<?= htmlspecialchars($student['student_first_name']) ?>"
            required
            />        
    </p>

    <p> 
        <label>Last Name</label>
        <br>
        <input type="text"
            name="student_last_name"
            value="<?= htmlspecialchars($student['student_last_name']) ?>"
            required
            />   
    </p>

    <p> 
        <label>Course</label>
        <br>
        <input type="text"
            name="student_course"
            value="<?= htmlspecialchars($student['student_course']) ?>"
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


<?php elseif($action==='update'): ?>

            <h2>Update Student </h2>

                <form method="POST">
                        <p>
                            <label>First Name</label>
                        <br>
                        <input type="text"
                             name="student_first_name"
            value="<?= htmlspecialchars($student['student_first_name']) ?>"
            required
            />        
    </p>

    <p> 
        <label>Last Name</label>
        <br>
        <input type="text"
            name="student_last_name"
            value="<?= htmlspecialchars($student['student_last_name']) ?>"
            required
            />   
    </p>

    <p> 
        <label>Course</label>
        <br>
        <input type="text"
            name="student_course"
            value="<?= htmlspecialchars($student['student_course']) ?>"
            required
            />   
    </p>

    <button type="submit">
        Update
    </button>

    <a href="index.php?section=students">
        Cancel
    </a>
</form>





                    <?php else: ?>
                    <table border ="1">
>>>>>>> f1c0ce2a69e58925c63bbc422c0eb12febc5b497
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Course</th>
                    <th>Created At</th>
                    <th>Actions</th>
<<<<<<< HEAD
                </tr>
            </thead>

            <tbody>

                <?php foreach ($students as $student): ?>

                    <tr>
                        <td><?= htmlspecialchars($student['student_id']) ?></td>
                        <td><?= htmlspecialchars($student['student_first_name']) ?></td>
                        <td><?= htmlspecialchars($student['student_last_name']) ?></td>
                        <td><?= htmlspecialchars($student['student_course']) ?></td>
                        <td><?= htmlspecialchars($student['student_created_at']) ?></td>

                        <td>
                            <a href="index.php?section=students&action=update&id=<?= $student['student_id'] ?>">
                                Edit
                            </a>
                            |
                            <a
                                href="index.php?section=students&action=delete&id=<?= $student['student_id'] ?>"
                                onclick="return confirm('Are you sure you want to delete this student?');"
                            >
                                Delete
                            </a>
                        </td>
                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>

<?php endif; ?>


<!-- BOOKS -->

<?php if ($section === 'books'): ?>

    <h1>Books</h1>

    <p>
        <a href="index.php?section=books&action=create">
            Add New Book
        </a>
    </p>

    <?php if ($action === 'create'): ?>

        <h2>Add New Book</h2>

        <form method="POST">

            <p>
                <label>Book Title:</label><br>
                <input type="text" name="book_title" required>
            </p>

            <p>
                <label>Book Author:</label><br>
                <input type="text" name="book_author" required>
            </p>

            <p>
                <label>Book Category:</label><br>
                <input type="text" name="book_category" required>
            </p>

            <button type="submit">Save</button>

            <a href="index.php?section=books">Cancel</a>

        </form>

    <?php elseif ($action === 'update'): ?>

        <h2>Update Book</h2>

        <form method="POST">

            <p>
                <label>Book Title:</label><br>
                <input
                    type="text"
                    name="book_title"
                    value="<?= htmlspecialchars($book['book_title']) ?>"
                    required
                >
            </p>

            <p>
                <label>Book Author:</label><br>
                <input
                    type="text"
                    name="book_author"
                    value="<?= htmlspecialchars($book['book_author']) ?>"
                    required
                >
            </p>

            <p>
                <label>Book Category:</label><br>
                <input
                    type="text"
                    name="book_category"
                    value="<?= htmlspecialchars($book['book_category']) ?>"
                    required
                >
            </p>

            <button type="submit">Update</button>

            <a href="index.php?section=books">Cancel</a>

        </form>

    <?php else: ?>

        <table border="1">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Book Title</th>
                    <th>Book Author</th>
                    <th>Book Category</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($books as $book): ?>

                    <tr>
                        <td><?= htmlspecialchars($book['book_id']) ?></td>
                        <td><?= htmlspecialchars($book['book_title']) ?></td>
                        <td><?= htmlspecialchars($book['book_author']) ?></td>
                        <td><?= htmlspecialchars($book['book_category']) ?></td>
                        <td><?= htmlspecialchars($book['book_created_at']) ?></td>

                        <td>
                            <a href="index.php?section=books&action=update&id=<?= $book['book_id'] ?>">
                                Edit
                            </a>
                            |
                            <a
                                href="index.php?section=books&action=delete&id=<?= $book['book_id'] ?>"
                                onclick="return confirm('Are you sure you want to delete this book?');"
                            >
                                Delete
                            </a>
                        </td>
                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>

<?php endif; ?>


<!-- BORROWS -->

<?php if ($section === 'borrows'): ?>

    <h1>Borrows</h1>

    <p>
        <a href="index.php?section=borrows&action=create">
            Borrow A Book
        </a>
    </p>

    <?php if ($action === 'create'): ?>

        <h2>Borrow A Book</h2>

        <form method="POST">

            <p>
                <label>Student:</label><br>

                <select name="student_id" required>

                    <option value="">--Select Student--</option>

                    <?php foreach ($students as $student): ?>

                        <option value="<?= $student['student_id'] ?>">
                            <?= htmlspecialchars(
                                $student['student_first_name'] . ' ' .
                                $student['student_last_name']
                            ) ?>
                        </option>

                    <?php endforeach; ?>

                </select>
            </p>

            <p>
                <label>Book:</label><br>

                <select name="book_id" required>

                    <option value="">--Select Book--</option>

                    <?php foreach ($books as $book): ?>

                        <option value="<?= $book['book_id'] ?>">
                            <?= htmlspecialchars(
                                $book['book_title'] . ' - ' .
                                $book['book_author']
                            ) ?>
                        </option>

                    <?php endforeach; ?>

                </select>
            </p>

            <button type="submit">Borrow</button>

            <a href="index.php?section=borrows">Cancel</a>

        </form>

    <?php else: ?>

        <table border="1">

            <thead>
                <tr>
                    <th>Borrow ID</th>
                    <th>Student Name</th>
                    <th>Book Title</th>
                    <th>Borrow Date</th>
                    <th>Book Return Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($borrows as $borrow): ?>

                    <tr>

                        <td><?= htmlspecialchars($borrow['borrow_id']) ?></td>

                        <td>
                            <?= htmlspecialchars(
                                $borrow['student_first_name'] . ' ' .
                                $borrow['student_last_name']
                            ) ?>
                        </td>

                        <td><?= htmlspecialchars($borrow['book_title']) ?></td>

                        <td><?= htmlspecialchars($borrow['borrow_date']) ?></td>

                        <td>
                            <?php if ($borrow['borrow_return_date'] === null): ?>
                                Not Returned
                            <?php else: ?>
                                <?= htmlspecialchars($borrow['borrow_return_date']) ?>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($borrow['borrow_return_date'] === null): ?>
                                Borrowed
                            <?php else: ?>
                                Returned
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($borrow['borrow_return_date'] === null): ?>

                                <a
                                    href="index.php?section=borrows&action=update&id=<?= $borrow['borrow_id'] ?>"
                                    onclick="return confirm('Confirm that this book has been returned?');"
                                >
                                    Return
                                </a>

                            <?php endif; ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>

<?php endif; ?>


<!-- ALERT MESSAGE -->

<?php if (isset($_SESSION['alert'])): ?>

    <script>
        alert(<?= json_encode($_SESSION['alert']) ?>);
    </script>

    <?php unset($_SESSION['alert']); ?>

<?php endif; ?>

</body>
=======
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
                <a href= "index.php?section=students&action=update&id=<?=$student['student_id'] ?>">Edit</a> 
                |
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

<?php if (isset ($_SESSION['alert'])): ?>

           <script>
               alert(<?=json_encode($_SESSION['alert'])?>);
            </script>

            <?php unset($_SESSION['alert']); ?>

<?php endif; ?>

>>>>>>> f1c0ce2a69e58925c63bbc422c0eb12febc5b497
</html>