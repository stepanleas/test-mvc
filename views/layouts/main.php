<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <!-- CSS Styles -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/5266da74d3.js" crossorigin="anonymous"></script>

    <title>Hello, world!</title>
</head>
<body>

<nav id="main-nav" class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Navbar</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse d-flex justify-content-between" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="/">Tasks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="/contact">Contact</a>
                </li>
            </ul>
            <ul id="authLinks" class="navbar-nav mb-2 mb-lg-0">

            </ul>
        </div>
    </div>
</nav>

{{ content }}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<script>
    // Verify if the user is authorized
    axios.get('/check_auth_user')
    .then(res => {
        const resp = res.data;

        const links = document.getElementById('authLinks');
        // If the user is logged in
        if (resp) {
            links.innerHTML = '<li class="nav-item">\n' +
                '                    <a href="/logout" class="nav-link active" aria-current="page">Logout</a>\n' +
                '                </li>';
        } else {
            links.innerHTML = '<li class="nav-item">\n' +
                '                    <a class="nav-link active" aria-current="page" href="/login">Login</a>\n' +
                '                </li>\n' +
                '                <li class="nav-item">\n' +
                '                    <a class="nav-link active" aria-current="page" href="/register">Register</a>\n' +
                '                </li>';
        }
    });

    // Verify the session message if it exists
    axios.get('/check_session_message')
    .then(res => {
        const resp = res.data;
        console.log(resp)
        // Show an alert to the user if an message is present
        if (resp.type === 'success') {
            let alert = '<div class="container">' +
                '<div class="alert alert-warning alert-dismissible fade show d-flex justify-content-between" role="alert">\n' +
                '  <strong>'+ resp.message +'</strong>\n' +
                '  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">X</button>\n' +
                '</div>' +
                '</div>';

            let nav = document.getElementById('table-upper-header'); // In views/tasks/index.php
            nav.insertAdjacentHTML('afterbegin', alert);
        }
    })
    .catch(e => {
        console.log(e);
    })

</script>

</body>
</html>