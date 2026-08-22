C:\dev2\it30a\backup

CREATE DATABASEs <database_name>;
SHOW DATABASES;
CONNECT <database_name>;
CREATE TABLE <table_name_in_plural> ();
INSERT INTO <table_name_in_plural> ();
   (columns)
   VALUES(values);

Utility Commands
\! cls
mysqldump -u root -p --databases library_db >C:\dev2\it30a\backup\08182026_library_db.mysql

mysqldump -u root -p --database library_db > "C:\dev2\it30a\backup\%date:~-4%_%date:~4,2%_%date:~7,2%_%time:~0,2%_%time:~3,2%_%time:~6,2%_%library_db.sql"
ALTER TABLE students ADD COLUMN student_cretaed_at TIMESTAMP NULL DEFAULT NULL;
UPDATE students SET student_cretaed_at = CURRENT_TIMESTAMP WHERE student_created_at IS NULL;
ALTER TABLE students MODIFY COLUMN student_created+at TIMESTAMP NOT  NULL DEFAULT CURRENT_TIMESTAMP;