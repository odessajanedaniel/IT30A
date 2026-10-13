<?php
// Database Connection
$host = 'localhost';
$db = 'it30a_lab_db2';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

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

// Determine current section
$section = $_GET['section'] ?? 'students';
$action = $_GET['action'] ?? '';
$id = (int) ($_GET['id'] ?? 0);

// Allow only valid sections
if (!in_array($section, ['students', 'books', 'borrows'], true)) {
    $section = 'students';
}

function redirectTo($url)
{
    header("Location: $url");
    exit;
}

// ==================================================
// STUDENTS
// ==================================================

// Create Student
if ($section === 'students' && $action === 'create'
    && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $course = trim($_POST['course'] ?? '');

    if ($firstName === '' || $lastName === '' || $course === '') {
        $_SESSION['alert'] = 'Please complete all fields.';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO students
                (student_first_name, student_last_name, student_course)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$firstName, $lastName, $course]);

        $_SESSION['alert'] = 'Student saved successfully.';
    }

    redirectTo('index.php?section=students');
}

// Update Student
if ($section === 'students' && $action === 'update') {
    $stmt = $pdo->prepare("
        SELECT * FROM students WHERE student_id = ?
    ");
    $stmt->execute([$id]);
    $student = $stmt->fetch();

    if (!$student) {
        $_SESSION['alert'] = 'Student not found.';
        redirectTo('index.php?section=students');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $course = trim($_POST['course'] ?? '');

        if ($firstName === '' || $lastName === '' || $course === '') {
            $_SESSION['alert'] = 'Please complete all fields.';
        } else {
            $stmt = $pdo->prepare("
                UPDATE students
                SET student_first_name = ?,
                    student_last_name = ?,
                    student_course = ?
                WHERE student_id = ?
            ");
            $stmt->execute([$firstName, $lastName, $course, $id]);

            $_SESSION['alert'] = 'Student updated successfully.';
            redirectTo('index.php?section=students');
        }
    }
}

// Delete Student
if ($section === 'students' && $action === 'delete') {
    $stmt = $pdo->prepare("
        SELECT student_id FROM students WHERE student_id = ?
    ");
    $stmt->execute([$id]);

    if (!$stmt->fetch()) {
        $_SESSION['alert'] = 'Student not found.';
    } else {
        // Use the actual table name: borrow
        $stmt = $pdo->prepare("
            SELECT borrow_id FROM borrow
            WHERE student_id = ? LIMIT 1
        ");
        $stmt->execute([$id]);

        if ($stmt->fetch()) {
            $_SESSION['alert'] =
                'Cannot delete student because borrow records exist.';
        } else {
            $stmt = $pdo->prepare("
                DELETE FROM students WHERE student_id = ?
            ");
            $stmt->execute([$id]);

            $_SESSION['alert'] = 'Student deleted successfully.';
        }
    }

    redirectTo('index.php?section=students');
}

// ==================================================
// BOOKS
// ==================================================

// Create Book
if ($section === 'books' && $action === 'create'
    && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['book_title'] ?? '');
    $author = trim($_POST['book_author'] ?? '');
    $category = trim($_POST['book_category'] ?? '');

    if ($title === '' || $author === '' || $category === '') {
        $_SESSION['alert'] = 'Please complete all fields.';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO books (book_title, book_author, book_category)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$title, $author, $category]);

        $_SESSION['alert'] = 'Book saved successfully.';
    }

    redirectTo('index.php?section=books');
}

// Update Book
if ($section === 'books' && $action === 'update') {
    $stmt = $pdo->prepare("
        SELECT * FROM books WHERE book_id = ?
    ");
    $stmt->execute([$id]);
    $book = $stmt->fetch();

    if (!$book) {
        $_SESSION['alert'] = 'Book not found.';
        redirectTo('index.php?section=books');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['book_title'] ?? '');
        $author = trim($_POST['book_author'] ?? '');
        $category = trim($_POST['book_category'] ?? '');

        if ($title === '' || $author === '' || $category === '') {
            $_SESSION['alert'] = 'Please complete all fields.';
        } else {
            $stmt = $pdo->prepare("
                UPDATE books
                SET book_title = ?,
                    book_author = ?,
                    book_category = ?
                WHERE book_id = ?
            ");
            $stmt->execute([$title, $author, $category, $id]);

            $_SESSION['alert'] = 'Book updated successfully.';
            redirectTo('index.php?section=books');
        }
    }
}

// Delete Book
if ($section === 'books' && $action === 'delete') {
    $stmt = $pdo->prepare("
        SELECT book_id FROM books WHERE book_id = ?
    ");
    $stmt->execute([$id]);

    if (!$stmt->fetch()) {
        $_SESSION['alert'] = 'Book not found.';
    } else {
        $stmt = $pdo->prepare("
            SELECT borrow_id FROM borrow
            WHERE book_id = ? LIMIT 1
        ");
        $stmt->execute([$id]);

        if ($stmt->fetch()) {
            $_SESSION['alert'] =
                'Cannot delete book because borrow records exist.';
        } else {
            $stmt = $pdo->prepare("
                DELETE FROM books WHERE book_id = ?
            ");
            $stmt->execute([$id]);

            $_SESSION['alert'] = 'Book deleted successfully.';
        }
    }

    redirectTo('index.php?section=books');
}

// ==================================================
// BORROWS
// ==================================================

// Borrow a Book
if ($section === 'borrows' && $action === 'create'
    && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $studentId = (int) ($_POST['student_id'] ?? 0);
    $bookId = (int) ($_POST['book_id'] ?? 0);

    if ($studentId <= 0 || $bookId <= 0) {
        $_SESSION['alert'] = 'Please select a student and a book.';
    } else {
        // Check Student
        $stmt = $pdo->prepare("
            SELECT student_id FROM students WHERE student_id = ?
        ");
        $stmt->execute([$studentId]);

        if (!$stmt->fetch()) {
            $_SESSION['alert'] = 'Student not found.';
        } else {
            // Check Book
            $stmt = $pdo->prepare("
                SELECT book_id FROM books WHERE book_id = ?
            ");
            $stmt->execute([$bookId]);

            if (!$stmt->fetch()) {
                $_SESSION['alert'] = 'Book not found.';
            } else {
                // Check if the student already has an unreturned book
                $stmt = $pdo->prepare("
                    SELECT borrow_id FROM borrow
                    WHERE student_id = ?
                      AND borrow_return_date IS NULL
                    LIMIT 1
                ");
                $stmt->execute([$studentId]);

                if ($stmt->fetch()) {
                    $_SESSION['alert'] =
                        'This student already has an unreturned book.';
                } else {
                    // Check if the book is already borrowed
                    $stmt = $pdo->prepare("
                        SELECT borrow_id FROM borrow
                        WHERE book_id = ?
                          AND borrow_return_date IS NULL
                        LIMIT 1
                    ");
                    $stmt->execute([$bookId]);

                    if ($stmt->fetch()) {
                        $_SESSION['alert'] =
                            'This book has not been returned yet.';
                    } else {
                        // Save borrow record
                        $stmt = $pdo->prepare("
                            INSERT INTO borrow
                                (student_id, book_id, borrow_date,
                                 borrow_return_date)
                            VALUES (?, ?, NOW(), NULL)
                        ");
                        $stmt->execute([$studentId, $bookId]);

                        $_SESSION['alert'] =
                            'Book borrowed successfully.';
                    }
                }
            }
        }
    }

    redirectTo('index.php?section=borrows');
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

    redirectTo('index.php?section=borrows');
}

// ==================================================
// FETCH DATA FOR DISPLAY
// ==================================================

$students = [];
$books = [];
$borrows = [];
$student = $student ?? null;
$book = $book ?? null;

if ($section === 'students'
    || ($section === 'borrows' && $action === 'create')) {
    $stmt = $pdo->query("
        SELECT * FROM students ORDER BY student_id DESC
    ");
    $students = $stmt->fetchAll();
}

if ($section === 'books'
    || ($section === 'borrows' && $action === 'create')) {
    $stmt = $pdo->query("
        SELECT * FROM books ORDER BY book_id DESC
    ");
    $books = $stmt->fetchAll();
}

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
        LEFT JOIN students
            ON borrow.student_id = students.student_id
        LEFT JOIN books
            ON borrow.book_id = books.book_id
        ORDER BY borrow.borrow_id DESC
    ");

    $borrows = $stmt->fetchAll();
}

?>

<!DOCTYPE html>
<html lang="en">
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

    <?php elseif ($action === 'update' && $student): ?>

        <h2>Update Student</h2>

        <form method="POST">
            <p>
                <label>First Name:</label><br>
                <input type="text" name="first_name"
                    value="<?= htmlspecialchars($student['student_first_name']) ?>"
                    required>
            </p>

            <p>
                <label>Last Name:</label><br>
                <input type="text" name="last_name"
                    value="<?= htmlspecialchars($student['student_last_name']) ?>"
                    required>
            </p>

            <p>
                <label>Course:</label><br>
                <input type="text" name="course"
                    value="<?= htmlspecialchars($student['student_course']) ?>"
                    required>
            </p>

            <button type="submit">Update</button>
            <a href="index.php?section=students">Cancel</a>
        </form>

    <?php else: ?>

        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Course</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['student_id']) ?></td>
                        <td><?= htmlspecialchars($student['student_first_name']) ?></td>
                        <td><?= htmlspecialchars($student['student_last_name']) ?></td>
                        <td><?= htmlspecialchars($student['student_course']) ?></td>
                        <td><?= htmlspecialchars($student['student_created_at'] ?? '') ?></td>
                        <td>
                            <a href="index.php?section=students&action=update&id=<?= $student['student_id'] ?>">Edit</a>
                            |
                            <a href="index.php?section=students&action=delete&id=<?= $student['student_id'] ?>"
                               onclick="return confirm('Delete this student?');">Delete</a>
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

    <?php elseif ($action === 'update' && $book): ?>

        <h2>Update Book</h2>

        <form method="POST">
            <p>
                <label>Book Title:</label><br>
                <input type="text" name="book_title"
                    value="<?= htmlspecialchars($book['book_title']) ?>"
                    required>
            </p>

            <p>
                <label>Book Author:</label><br>
                <input type="text" name="book_author"
                    value="<?= htmlspecialchars($book['book_author']) ?>"
                    required>
            </p>

            <p>
                <label>Book Category:</label><br>
                <input type="text" name="book_category"
                    value="<?= htmlspecialchars($book['book_category']) ?>"
                    required>
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
                        <td><?= htmlspecialchars($book['book_created_at'] ?? '') ?></td>
                        <td>
                            <a href="index.php?section=books&action=update&id=<?= $book['book_id'] ?>">Edit</a>
                            |
                            <a href="index.php?section=books&action=delete&id=<?= $book['book_id'] ?>"
                               onclick="return confirm('Delete this book?');">Delete</a>
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
                                ($borrow['student_first_name'] ?? '') . ' ' .
                                ($borrow['student_last_name'] ?? '')
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($borrow['book_title'] ?? '') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($borrow['borrow_date'] ?? '') ?>
                        </td>

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
                                >Return</a>
                            <?php else: ?>
                                -
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
</html>