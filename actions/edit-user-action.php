<?php
    include "../classes/User.php";

    // Instantiate an object
    $user = new User;

    // Call the update method
    # Note: The update method is doing the actual update
    $user->update($_POST, $_FILES);
    # The $_POST --- holds the like the first_name, last_name and username
    # The $_FILES --- holds the file (image file) uploaded from the form
?>