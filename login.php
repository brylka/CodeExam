<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </head>
    <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#"></a>
        <div class="navbar-nav ml-auto">
            <li class="nav-item">
                <select class="custom-select" id="languageSelector">
                    <option value="en">English</option>
                    <option value="pl">Polski</option>
                </select>
            </li>
        </div>
    </nav>
    <div class="container">
        <h1 class="mt-5" id="loginTitle"></h1>
        <form action="authenticate.php" method="post">
            <div class="form-group">
                <label for="username" id="loginUsername"></label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password" id="loginPassword"></label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" id="loginButton"></button>
        </form>
    </div>
    <script src="translations.js"></script>
    </body>
</html>