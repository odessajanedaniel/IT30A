-- Table #1 students table
CREATE TABLE IF NOT EXISTS students (

    -- Primary key for the students table
    student_id INT AUTO_INCREMENT PRIMARY KEY,

    -- Student name
    student_first_name VARCHAR(50) NOT NULL,
    student_last_name VARCHAR(50) NOT NULL,

    -- student course
    student_course VARCHAR(50) NOT NULL,

    -- student created at timestamp
    student_created_at TIMESTAMP NOT NULL
        DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;


-- Tables #2 books
CREATE TABLE IF NOT EXISTS books (

    -- Primary key for books table
    book_id INT AUTO_INCREMENT PRIMARY KEY,

    -- Book information
    book_title VARCHAR(100) NOT NULL,
    book_author VARCHAR(100) NOT NULL,
    book_category VARCHAR(100) NOT NULL,

    -- Book created at timestamp
    book_created_at TIMESTAMP NOT NULL
        DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;


-- Tables #3 borrow
CREATE TABLE IF NOT EXISTS borrow (

    -- Primary key for borrow table
    borrow_id INT AUTO_INCREMENT PRIMARY KEY,

    -- Foreign key references
    student_id INT NOT NULL,
    book_id INT NOT NULL,

    -- Borrow timestamp not null by default
    borrow_date TIMESTAMP NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    -- Borrow return date, null by default
    borrow_return_date TIMESTAMP NULL
        DEFAULT NULL,

    -- Borrow table constraints and foreign keys
    CONSTRAINT fk_borrow_student
        FOREIGN KEY (student_id)
        REFERENCES students (student_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_borrow_book
        FOREIGN KEY (book_id)
        REFERENCES books (book_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;


-- insert statement #1: insert students

INSERT INTO students (
    student_first_name,
    student_last_name,
    student_course
)

VALUES
("ODESSA", "DANIEL", "BSIT"),
("HONEY", "DANIEL", "BSIT");


-- insert statement #2: insert books

INSERT INTO books (
    book_title,
    book_author,
    book_category
)

VALUES
("DESTINY", "ARLYN", "FICTION"),
("INFINITY", "CHAN", "MYSTERY");


-- insert statement #3: insert borrow

INSERT INTO borrow (
    student_id,
    book_id
)

VALUES
(1, 2),
(2, 1);