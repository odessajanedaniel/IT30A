-- Find books that are currently borrowed
SELECT
    br.borrow_id,

    -- Student name
    CONCAT(
        s.student_first_name,
        ' ',
        s.student_last_name
    ) AS student_name,

    -- books
    b.book_title,
    b.book_author,
    b.book_category,

    -- Borrow Date
    br.borrow_date

FROM borrow br

INNER JOIN students s
    ON br.student_id = s.student_id

INNER JOIN books b
    ON br.book_id = b.book_id

WHERE br.borrow_return_date IS NULL

ORDER BY br.borrow_date DESC;

-- RETURN a book
UPDATE borrow
SET borrow_return_date = CURRENT_TIMESTAMP
WHERE borrow_id = 1
     AND borrow_return_date IS NULL



-- View return books
SELECT
    br.borrow_id,

    -- Student Name
    CONCAT(
        s.student_first_name,
        ' ',
        s.student_last_name
    ) AS student_name,

    -- Books
    b.book_title,
    b.book_author,
    b.book_category,

    -- Borrow Date
    br.borrow_date

FROM borrow br

INNER JOIN students s
    ON br.student_id = s.student_id

INNER JOIN books b
    ON br.book_id = b.book_id

WHERE br.borrow_return_date IS NULL

ORDER BY br.borrow_date DESC;