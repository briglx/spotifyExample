
<html>
<head>
    <title></title>
</head>
<body>
    <h1>Spotify Test</h1>

    @if ($error != "")
    <div class="error">{{ $error }}</div>
    @endif

    @if ($username == "")
    <a href="/login">Login</a>
    @else
        <a href="/logout">Log out {{ $username }}</a>
    @endif
    
    <script type="text/javascript" src="js/jquery-2.1.4.min.js"></script>
    <script type="text/javascript" src="js/main.js"></script>

</body>
</html>