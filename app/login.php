<!DOCTYPE html>

<head>
    <title>BIOS Login Page</title>
</head>

<body>
    <h1> Welcome to BIOS of Merlion University!</h1>
    <hr>
    <h2>Please Login with your Credentials</h2>
    <table>
        <form action="process-login.php" method="POST">
            <tr>
                <td>Username:</td>
                <td><input type="text" name="username" id=""></td>
            </tr>
            <tr>
                <td>Password:</td>
                <td><input type="password" name="password" id=""></td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" name="submit" value='Submit'>
                </td>
            </tr>
        </form>
        <tr>
            <td colspan=2>
                <?php
                require_once 'include/common.php';
                printErrors();
                ?>
            </td>
        </tr>
    </table>
</body>

</html>