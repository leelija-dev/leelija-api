<?php
require_once("config.php");
class Employee extends DatabaseConnection
{
    //GET EMPLOYEE DETAILS USING EMPLOYEE ID
    public function getEmployeeDetails($id)
    {
        header('Content-Type: application/json');
        $query = "SELECT employees.*, emp_docs.* 
              FROM employees 
              LEFT JOIN emp_docs ON employees.emp_id = emp_docs.emp_id 
              WHERE employees.emp_id = ?";

        $stmt = mysqli_prepare($this->conn, $query);

        if ($stmt) {

            $stmt->bind_param('i', $id);
            $stmt->execute();

            $result = mysqli_stmt_get_result($stmt);
            $employeeDetails = mysqli_fetch_assoc($result);

            if ($employeeDetails) {
                $response = array(
                    'success' => true,
                    'message' => 'Employee and Document Details Fetched successfully',
                    'data' => $employeeDetails
                );
                echo json_encode($response);
                die();
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'Failed to fetch details'
                );
                echo json_encode($response);
                die();
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'There is an error'
            );
            echo json_encode($response);
            die();
        }
    }

    //Get document details of a employee using doc id
    public function getDocDetails($docId)
    {
        $query = "SELECT * FROM emp_docs WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $docId);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $employee = mysqli_fetch_assoc($result);
            return $employee;
        }
    }

    //Get each employee details using emp id
    public function getempDetails($empId)
    {
        $query = "SELECT * FROM employees WHERE emp_id = ?";
        $stmt = mysqli_prepare($this->conn, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $empId);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $employee = mysqli_fetch_assoc($result);
            return $employee;
        }
    }

    //GET ALL EMPLOYEES NAME

    public function getEmployeesName()
    {
        header('Content-Type: application/json');

        $query = "SELECT name FROM employees";
        $stmt = mysqli_prepare($this->conn, $query);

        if ($stmt) {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $employees = mysqli_fetch_all($result, MYSQLI_ASSOC);
            if ($employees) {
                $response = array('success' => true, 'message' => 'Employee Names Fetched successfully', 'data' => $employees);
                echo json_encode($response);
                die();
            } else {
                $response = array('success' => false, 'message' => 'Failed to fetch details');
                echo json_encode($response);
                die();
            }
        }
    }

    //GET ALL EMPLOYEE DETAILS
    public function getEmployeesDetails()
    {
        header('Content-Type: application/json');

        $query = "SELECT * FROM employees";
        $stmt = mysqli_prepare($this->conn, $query);

        if ($stmt) {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $employees = mysqli_fetch_all($result, MYSQLI_ASSOC);
            if ($employees) {
                $response = array('success' => true, 'message' => 'Employees Details Fetched successfully', 'data' => $employees);
                echo json_encode($response);
                die();
            } else {
                $response = array('success' => false, 'message' => 'Failed to fetch details');
                echo json_encode($response);
                die();
            }
        }
    }


    // public function addEmployeeDoc($id, $doc_name, $doc_path, $updated_by)
    // {

    //     $sql = "INSERT INTO emp_docs (emp_id, doc_name, doc_path, updated_by) VALUES (?, ?, ?, ?)";
    //     $stmt = mysqli_prepare($this->conn, $sql);
    //     if ($stmt){
    //         mysqli_stmt_bind_param($stmt, 'isss', $id, $doc_name, $doc_path, $updated_by);

    //         if (mysqli_stmt_execute($stmt)) {

    //             $response = array('success' => true, 'message' => 'Employee Documents Submitted successfully');
    //             echo json_encode($response);
    //             die();
    //         } else {
    //             $response = array('success' => false, 'message' => 'Failed to Submit documents');
    //             echo json_encode($response);
    //             die();
    //         }
    //     }
    // }

    //UPDATE EMPLOYEE DOCUMENTS BY DOC ID

    // public function updateEmployeeDoc($doc_id, $emp_id, $doc_name, $doc_path, $updated_by)
    // {

    //     header('Content-Type: application/json');

    //     $sql = "UPDATE emp_docs SET emp_id = ?, doc_name = ?, doc_path = ?, updated_by = ? WHERE id = ?";
    //     $stmt = mysqli_prepare($this->conn, $sql);

    //     if ($stmt) {
    //         mysqli_stmt_bind_param($stmt, 'isssi', $emp_id, $doc_name, $doc_path, $updated_by, $doc_id);

    //         if (mysqli_stmt_execute($stmt)) {
    //             $response = array('success' => true, 'message' => 'Employee Document updated successfully');
    //             echo json_encode($response);
    //             die();
    //         } else {
    //             $response = array('success' => false, 'message' => 'Failed to update document');
    //             echo json_encode($response);
    //             die();
    //         }
    //     } else {
    //         $response = array('success' => false, 'message' => 'Failed to prepare the query');
    //         echo json_encode($response);
    //         die();
    //     }
    // }



    public function updateEmployeeDoc($doc_id, $emp_id, $doc_name, $doc_path, $updated_by)
    {
        // Set response header to return JSON data
        header('Content-Type: application/json');


        // Sanitize strings to avoid unnecessary characters or malicious input
        $doc_name = htmlspecialchars(trim($doc_name), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        $doc_path = htmlspecialchars(trim($doc_path), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        $updated_by = htmlspecialchars(trim($updated_by), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters

        // Use filter_var for sanitizing inputs (ensures the string is clean from unusual characters)
        $doc_name = filter_var($doc_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $doc_path = filter_var($doc_path, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $updated_by = filter_var($updated_by, FILTER_SANITIZE_FULL_SPECIAL_CHARS);


        // Ensure sanitized strings are not empty
        if (empty($doc_name) || empty($doc_path) || empty($updated_by)) {
            $response = array('success' => false, 'message' => 'Document Name, Path, or Updated By is invalid');
            echo json_encode($response);
            return;
        }

        // Set MySQLi to throw exceptions for errors
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            // SQL query with placeholders
            $sql = "UPDATE emp_docs SET emp_id = ?, doc_name = ?, doc_path = ?, updated_by = ? WHERE id = ?";

            // Prepare the SQL statement
            $stmt = mysqli_prepare($this->conn, $sql);

            // Bind the parameters to the prepared statement
            mysqli_stmt_bind_param($stmt, 'isssi', $emp_id, $doc_name, $doc_path, $updated_by, $doc_id);

            // Execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Success response
                $response = array('success' => true, 'message' => 'Employee Document updated successfully');
                echo json_encode($response);
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        } catch (mysqli_sql_exception $e) {
            // Log error for debugging
            error_log("Database error: " . $e->getMessage());

            // Return failure response to the user
            $response = array('success' => false, 'message' => 'Failed to update document due to a database error');
            echo json_encode($response);
        } catch (Exception $e) {
            // Handle any other general exceptions
            error_log("General error: " . $e->getMessage());

            // Return failure response to the user
            $response = array('success' => false, 'message' => 'An unexpected error occurred');
            echo json_encode($response);
        }
    }

    //UPDATE EMPLOYEE DETAILS USING EMPLOYEE ID
    public function updateEmployeeDetails($emp_id, $data)
    {
        // Set response header to return JSON data
        header('Content-Type: application/json');

        $name = $data['name'];
        $designation = $data['designation'];
        $doj = $data['doj'];
        $gender = $data['gender'];
        $image = $data['image'];
        $phone = $data['phone'];
        $email = $data['email'];
        $password = $data['password'];
        $status = $data['status'];
        $featured = $data['featured'];

        // Sanitize strings to avoid unnecessary characters or malicious input
        $name = htmlspecialchars(trim($name), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        $designation = htmlspecialchars(trim($designation), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        $doj = htmlspecialchars(trim($doj), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        $gender = htmlspecialchars(trim($gender), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        $image = htmlspecialchars(trim($image), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        $phone = htmlspecialchars(trim($phone), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        $email = htmlspecialchars(trim($email), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        $password = htmlspecialchars(trim($password), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        $status = htmlspecialchars(trim($status), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters
        $featured = htmlspecialchars(trim($featured), ENT_QUOTES, 'UTF-8'); // Escape special HTML characters

        // Use filter_var for sanitizing inputs (ensures the string is clean from unusual characters)
        $name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $designation = filter_var($designation, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $doj = filter_var($doj, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $gender = filter_var($gender, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $image = filter_var($image, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $phone = filter_var($phone, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_var($email, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_var($password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $status = filter_var($status, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $featured = filter_var($featured, FILTER_SANITIZE_FULL_SPECIAL_CHARS);


        // Ensure sanitized strings are not empty
        if (
            empty($name) || empty($designation) || empty($doj) || empty($gender) || empty($image) || empty($phone) || empty($email)
            || empty($password) || empty($status) || empty($featured)
        ) {
            $response = array('success' => false, 'message' => 'Document Name, designation, doj, gender, image, phone, email, password, status or featured is invalid');
            echo json_encode($response);
            return;
        }

        // Set MySQLi to throw exceptions for errors
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            // SQL query with placeholders
            $sql = "UPDATE employees SET name = ?, designation = ?, doj = ?, gender = ?, image = ?, phone = ?, email = ?, password = ?, status = ?, featured = ? WHERE emp_id = ?";

            // Prepare the SQL statement
            $stmt = mysqli_prepare($this->conn, $sql);

            // Bind the parameters to the prepared statement
            mysqli_stmt_bind_param($stmt, 'ssssssssiii', $name, $designation, $doj, $gender, $image, $phone, $email, $password, $status, $featured, $emp_id);
            // Execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Success response
                $response = array('success' => true, 'message' => 'Employee updated successfully');
                echo json_encode($response);
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        } catch (mysqli_sql_exception $e) {
            // Log error for debugging
            error_log("Database error: " . $e->getMessage());

            // Return failure response to the user
            $response = array('success' => false, 'message' => 'Failed to update document due to a database error');
            echo json_encode($response);
        } catch (Exception $e) {
            // Handle any other general exceptions
            error_log("General error: " . $e->getMessage());

            // Return failure response to the user
            $response = array('success' => false, 'message' => 'An unexpected error occurred');
            echo json_encode($response);
        }
    }

    // public function updateEmployeeDetails($emp_id, $data)
    // {
    //     $name = $data['name'];
    //     $designation = $data['designation'];
    //     $doj = $data['doj'];
    //     // print_r($doj);   die();
    //     $gender = $data['gender'];
    //     $image = $data['image'];
    //     $phone = $data['phone'];
    //     $email = $data['email'];
    //     $password = $data['password'];
    //     $status = $data['status'];
    //     $featured = $data['featured'];

    //     header('Content-Type: application/json');

    //     $sql = "UPDATE employees SET name = ?, designation = ?, doj = ?, gender = ?, image = ?, phone = ?, email = ?, password = ?, status = ?, featured = ? WHERE emp_id = ?";
    //     $stmt = mysqli_prepare($this->conn, $sql);

    //     if ($stmt) {
    //         mysqli_stmt_bind_param($stmt, 'ssssssssiii', $name, $designation, $doj, $gender, $image, $phone, $email, $password, $status, $featured, $emp_id);

    //         if (mysqli_stmt_execute($stmt)) {
    //             $response = array('success' => true, 'message' => 'Employee updated successfully');
    //             echo json_encode($response);
    //             die();
    //         } else {
    //             $response = array('success' => false, 'message' => 'Failed to update document');
    //             echo json_encode($response);
    //             die();
    //         }
    //     } else {
    //         $response = array('success' => false, 'message' => 'Failed to prepare the query');
    //         echo json_encode($response);
    //         die();
    //     }
    // }

    public function addEmployee($data)
    {

        $name = $data['name'];
        $designation = $data['designation'];
        $doj = $data['doj'];
        $gender = $data['gender'];
        $image = $data['image'];
        $phone = $data['phone'];
        $email = $data['email'];
        $password = $data['password'];
        $status = $data['status'];
        $featured = $data['featured'];
        header('Content-Type: application/json');
        $sql = "INSERT INTO employees (name, designation, doj, gender, image, phone, email, password, status, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssssssssii', $name, $designation, $doj, $gender, $image, $phone, $email, $password, $status, $featured);

            if (mysqli_stmt_execute($stmt)) {

                $response = array('success' => true, 'message' => 'Employee Added successfully');
                echo json_encode($response);
                die();
            } else {
                $response = array('success' => false, 'message' => 'Failed to Submit employee');
                echo json_encode($response);
                die();
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'There is an error'
            );
            echo json_encode($response);
            die();
        }
    }

    // public function addTickets($data)
    // {

    //     $query = $data['query'];
    //     $priority = $data['priority'];
    //     $status = $data['status'];
    //     $generated_by = $data['generated_by'];
    //     header('Content-Type: application/json');
    //     $sql = "INSERT INTO ticket (query, priority, status, generated_by) VALUES (?, ?, ?, ?)";
    //     $stmt = mysqli_prepare($this->conn, $sql);
    //     if ($stmt){
    //         mysqli_stmt_bind_param($stmt, 'ssss', $query, $priority, $status, $generated_by);

    //         if (mysqli_stmt_execute($stmt)) {

    //             $response = array(
    //                 'success' => true, 
    //                 'message' => 'Ticket Added successfully'
    //             );
    //             echo json_encode($response);
    //             die();
    //         } else {
    //             $response = array('success' => false, 'message' => 'Failed to Submit ticket');
    //             echo json_encode($response);
    //             die();
    //         }
    //     } else {
    //         $response = array(
    //             'success' => false,
    //             'message' => 'There is an error'
    //         );
    //         echo json_encode($response);
    //         die();
    //     }


    // }

    public function addTickets($data)
    {
        $query = $data['query'];
        $priority = $data['priority'];
        $status = $data['status'];
        $generated_by = $data['generated_by'];
        $file = $data['file'];

        header('Content-Type: application/json');

        $sql = "INSERT INTO ticket (query, priority, status, generated_by) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssss', $query, $priority, $status, $generated_by);

            if (mysqli_stmt_execute($stmt)) {

                $ticket_id = mysqli_insert_id($this->conn);
                $query = "INSERT INTO ticket_files (ticket_id, file) VALUES (?, ?)";
                $stmt2 = mysqli_prepare($this->conn, $query);

                if ($stmt2) {
                    mysqli_stmt_bind_param($stmt2, 'is', $ticket_id, $file);

                    if (mysqli_stmt_execute($stmt2)) {
                        $response = array(
                            'success' => true,
                            'message' => 'Ticket added successfully'
                        );
                    } else {
                        $response = array(
                            'success' => false,
                            'message' => 'Ticket added but failed to insert the file'
                        );
                    }
                } else {
                    $response = array(
                        'success' => false,
                        'message' => 'Ticket added but there is an error with file'
                    );
                }
            } else {
                $response = array('success' => false, 'message' => 'Failed to submit ticket');
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'There is an error'
            );
        }

        echo json_encode($response);
        die();
    }

    public function addResponse($data)
    {
        $ticket_id = $data['ticket_id'];
        $response = $data['response'];
        $respond_by = $data['respond_by'];
        $file = $data['file'];

        header('Content-Type: application/json');

        $sql = "INSERT INTO ticket_response (ticket_id, response, respond_by) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssss', $ticket_id, $response, $respond_by);

            if (mysqli_stmt_execute($stmt)) {

                $response_id = mysqli_insert_id($this->conn);
                $query = "INSERT INTO ticket_response_files (response_id, file) VALUES (?, ?)";
                $stmt2 = mysqli_prepare($this->conn, $query);

                if ($stmt2) {
                    mysqli_stmt_bind_param($stmt2, 'is', $ticket_id, $file);

                    if (mysqli_stmt_execute($stmt2)) {
                        $response = array(
                            'success' => true,
                            'message' => 'Ticket added successfully'
                        );
                    } else {
                        $response = array(
                            'success' => false,
                            'message' => 'Ticket added but failed to insert the file'
                        );
                    }
                } else {
                    $response = array(
                        'success' => false,
                        'message' => 'Ticket added but there is an error with file'
                    );
                }
            } else {
                $response = array('success' => false, 'message' => 'Failed to submit ticket');
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'There is an error'
            );
        }

        echo json_encode($response);
        die();
    }
}
