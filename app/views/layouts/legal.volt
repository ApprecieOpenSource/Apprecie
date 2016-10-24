<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apprecie | Dashboard</title>
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/font-awesome/css/font-awesome.css" rel="stylesheet">
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <link href="/css/site.css" rel="stylesheet">
    <link href="/css/bootstrap-clockpicker.min.css" rel="stylesheet">
    <link href="/css/pikaday.css" rel="stylesheet">
    <link href="/css/colpick.css" rel="stylesheet">
    <script src="/js/jquery-1.11.1.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <!-- jQuery UI -->
    <script src="/js/jquery-ui.min.js"></script>
    <script src="/js/moment.js"></script>
    <script src="/js/bootstrap-clockpicker.min.js"></script>
    <script src="/js/pikaday.js"></script>
    <script src="/js/colpick.js"></script>
    <script src="/js/global.js"></script>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        body {
            /* Location of the image */
            background-image: url('<?= Assets::getOrganisationBackground(Organisation::getActiveUsersOrganisation()->getOrganisationId());?>');

            /* Background image is centered vertically and horizontally at all times */
            background-position: center center;

            /* Background image doesn't tile */
            background-repeat: no-repeat;

            /* Background image is fixed in the viewport so that it doesn't move when
               the content's height is greater than the image's height */
            background-attachment: fixed;

            /* This is what makes the background image rescale based
               on the container's size */
            background-size: cover;

            /* Set a background color that will be displayed
               while the background image is loading */
            background-color: #e4e4e4;
        }
    </style>
</head>
<body>
{{ widget('StyleWidget','index') }}
<style>
    @media (min-width: 768px){
        .container {
            width: 400px;
        }
    }

    @media (min-width: 992px){
        .container {
            width: 800px;
        }
    }

    @media (min-width: 1200px){
        .container {
            width: 800px;
        }
    }
</style>
<div class="container" style="background-color: white; margin-top: 75px; border-radius: 5px;">
    {{ content() }}
</div>
</body>
</html>
