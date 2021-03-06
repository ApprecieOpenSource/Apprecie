<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= _g('Apprecie'); ?> | <?= _g('Dashboard'); ?></title>

    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/font-awesome/css/font-awesome.css" rel="stylesheet">
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <link href="/css/site.css" rel="stylesheet">
    <link href="/css/bootstrap-clockpicker.min.css" rel="stylesheet">
    <link href="/css/pikaday.css" rel="stylesheet">
    <link href="/css/colpick.css" rel="stylesheet">
    <script src="/js/jquery-1.11.1.min.js"></script>
    <script src="/js/jquery-ui.min.js"></script>

    <script src="/js/bootstrap.min.js"></script>
    <!-- jQuery UI -->
    <script src="/js/moment.js"></script>
    <script src="/js/bootstrap-clockpicker.js"></script>
    <script src="/js/pikaday.js"></script>
    <script src="/js/colpick.js"></script>
    <script src="/js/compiled/public/js/raw/framework/jsrender.min.js"></script>
    <script src="/js/global.js"></script>
    <script src="/js/jquery.tooltipster.min.js"></script>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script>
        $(document).ready(function(){
            $.views.settings.delimiters("<|", "|>");
        })
        var CSRF_SESSION_TOKEN= '<?= (new \Apprecie\Library\Security\CSRFProtection())->getSessionToken();?>';
    </script>
    <?php $this->partial("partials/errors/index"); ?>
    <link href='https://fonts.googleapis.com/css?family=Quicksand:400,700,300' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300' rel='stylesheet' type='text/css'>
</head>
<body>
{{ widget('StyleWidget','index') }}
<div class="container">
    <div class="row">
        <div class="pull-left">
            <img id="main-logo" src="<?= Assets::getOrganisationBrandLogo(Organisation::getActiveUsersOrganisation()->getOrganisationId()); ?>" class="img-responsive brand-img"/>
        </div>
        <div class="pull-right hidden-xs user-type">
                {{ widget('CurrentRoleWidget') }}
        </div>
    </div>
    <div class="row">
        {{ widget('MenuWidget','index') }}
    </div>
</div>
<div class="container" style="background-color: #f3f3f4">
    {{ content() }}
</div>
</div>
<?php $this->partial("partials/footer/index"); ?>
{{ widget('GoogleAnalyticsWidget') }}
</body>
</html>
