-----------------------------------------------------------------------------------------
Create a database named dsms(Digital Student Management System) with two tables (employees/students and users)
-----------------------------------------------------------------------------------------
employees/students -- 10 columns including [id] incremented

CREATE TABLE employee/students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  firstname VARCHAR(100),
  lastname VARCHAR(100),
  age INT,
  position VARCHAR(100),
  Year INT,
  contact VARCHAR(50),
  email VARCHAR(100),
  address TEXT,
  hired_at DATETIME
);
-----------------------------------------------------------------------------------------
users -- 4 columns including [id] incremented

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL,
  access_level VARCHAR(50) NOT NULL
);
-----------------------------------------------------------------------------------------

|| ||   //\\   ||==\\  ||==\\  \\  //    //===  //==\\  ||==\\  ||==|| ||\\  ||   //===
||=||  //==\\  ||___|| ||___||  \\//    ||     ||    || ||   ||   ||   || \\ ||  ||   |===|
|| || //    \\ ||      ||        ||      \\===  \\==//  ||==//  ||==|| ||  \\||   \\___|| |

-----------------------------------------------------------------------------------------
