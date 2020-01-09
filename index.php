<?php session_start();?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="assets/css/style.css" rel="stylesheet">
    <title>Document</title>
    <?php if (isset($_SESSION['userLocation'])) : ?>
        <script type="text/javascript">
          userLocation = "<?php echo $_SESSION['userLocation']; ?>";
        </script>
    <?php endif ?>
</head>
<body>
    <div class="container">
        <div class="GeoLocation">
            <p class="GeoLocation--Title">Geo Location</p>
            <div id="geoLocationInfo" class="GeoLocation--Info">Are you from: <span>Z-City</span>?</div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
