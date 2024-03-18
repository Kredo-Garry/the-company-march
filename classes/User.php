<?php
    # include -> if missing, the program will run but get an error (not so strict)
    # require_once -> if missing, the program will halt, and will
    # an error and will run the program below it. (Strict)
    require_once "Database.php";

    # Note: The logic of our application e.g. (CRUD - CREATE, READ, UPDATE, DELETE)
    # will be in this class file
    class User extends Database{

        public function store($request){
            $first_name = $request['first_name'];
            $last_name = $request['last_name'];
            $username = $request['username'];
            $password = $request['password'];

            # Apply hashing algorithm
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            #SQL Query string
            $sql = "INSERT INTO users(`first_name`, `last_name`, `username`, `password`) VALUES('$first_name', '$last_name', '$username', '$hashed_password')";

            # Execute the query string
            # The $this->conn --- came from the Database class
            if ($this->conn->query($sql)) { 
                header('location: ../views'); //go to index.php or login.php page.. we will create later on
                exit;
            }else {
                die('Error in creating the user: ' . $this->conn->error);
            }
        }

        public function login($request){ //username and password
            $username = $request['username'];
            $password = $request['password'];

            # Query string
            $sql = "SELECT * FROM users WHERE username = '$username'";

            $result = $this->conn->query($sql);

            # Check the username
            if ($result->num_rows == 1) {
                # Check the password
                $user = $result->fetch_assoc();
                # $user = ['id' => 1, 'username' => 'john', 'password' => '$2uy10c9v...']; 

                if (password_verify($password, $user['password'])) { //if the password matched
                    # Create a session variables for future use
                    session_start();
                    $_SESSION['id'] = $user['id']; //1
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['first_name'] . " " . $user['last_name'];

                    header('location: ../views/dashboard.php');
                    exit;
                }else{
                    die("Password is incorrect.");
                }
            }else {
                die("Username not found.");
            }
        }

        function logout(){
            //session_start(); //start this to use session variables
            session_unset(); //execute this to unset the session variables set in the login method
            session_destroy(); // destroy|removed the sesssion variables from the memory

            header("location: ../views"); //redirect the user to the login page
            exit;
        }

        # Retrieved all the users in the users table
        public function getAllUsers(){
            
            $sql = "SELECT id, first_name, last_name, username, photo FROM users";

            if ($result = $this->conn->query($sql)) {
                return $result;
            }else {
                die("Error in retrieving users." . $this->conn->error);
            }

        }

        # Retrieved one user
        # Note: The $id is the id of the user we want to retrieve
        public function getUser($id){
            $sql = "SELECT * FROM users WHERE id = $id";

            if ($result = $this->conn->query($sql)) {
                return $result->fetch_assoc();
            }else {
                die("Error in retrieving one user." . $this->conn->error);
            }
        }
        # $_POST ($request), $_FILES ($files)
        public function update($request, $files)
        {
            session_start();
            $id = $_SESSION['id']; //marywatson --- 1
            $first_name = $request['first_name'];
            $last_name = $request['last_name'];
            $username = $request['username'];

            # Note: The file is handled defferently
            //The 'photo' is the name of the input field from the form
            // The 'name' --the name of the file
            $photo = $files['photo']['name']; //my_avatar.png
            
            //The 'photo' is the name of the input field from the form
            //The 'tmp_name' refers to the temporary storage of our computer where the image is temoparily saved
            $photo_tmp = $files['photo']['tmp_name'];

            //Query string to update the firstname, lastname and username
            $sql = "UPDATE users SET first_name = '$first_name', last_name = '$last_name', username = '$username' WHERE id = $id";

            //Execute the query string above
            if ($this->conn->query($sql)) {
                $_SESSION['username'] = $username;
                $_SESSION['full_name'] = "$first_name $last_name";

                # If there is an uploaded photo, save it to the Db and save the file to the images folder
                if ($photo) { //is it true that the user uploaded a photo?
                    //then execute this
                    $sql = "UPDATE users SET photo = '$photo' WHERE id = '$id'";
                    //file destination folder
                    $destination = "../assets/images/$photo";

                    //Execute the query above to save the image to the Db, and move the uploaded file
                    if ($this->conn->query($sql)) {
                        if (move_uploaded_file($photo_tmp, $destination)) {
                            header('location: ../views/dashboard.php');
                            exit;
                        }else {
                            die("Error in moving the photo.");
                        }
                    }else {
                        die("Error in uploading photo: " . $this->conn->error);
                    }
                }

                header('location: ../views/dashboard.php');
                exit;
            }else {
                die("Error in updating the user. " . $this->conn->error);
            }
        }

        public function delete()
        {
            session_start();
            $id = $_SESSION['id']; //marywatson --- 1

            # Query string
            $sql = "DELETE FROM users WHERE id = $id";

            if ($this->conn->query($sql)) {
                $this->logout(); //call logout, contains header('location: ../views') -- login page
            }else {
                die("Error in deleting your account. " . $this->conn->error);
            }
        }
    }
?>