<?php
if (isset($_FILES['userfile'])) {
    // Example:
    //move_uploaded_file($_FILES['userfile']['tmp_name'], "uploads/" . $_FILES['userfile']['name']);
    echo "ok";	
    exit;
}
?>