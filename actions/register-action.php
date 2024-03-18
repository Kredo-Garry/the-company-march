<?php
    include "../classes/User.php";

    # Create/Instantiate an object
    $user = new User;

    # Call the method
    $user->store($_POST);
    # The $_POST -> will hold the data coming
    # from the registration form
    # $_POST['first_name'] = "Jane";
    # $_POST['last_name'] = "Doe";
    # $_POST['username'] = "jane.doe";
    # $_POST['password'] = "jane1234";
?>