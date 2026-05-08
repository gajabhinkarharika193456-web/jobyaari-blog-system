<?php
echo "<h2>Form Test</h2>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<h3>Form was submitted!</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
}
?>

<form method="POST" action="">
    <input type="text" name="test" placeholder="Enter something">
    <button type="submit">Submit</button>
</form>