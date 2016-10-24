<style>
    .navbar-default {
        background-color: <?= $this->view->styles->navigationPrimary ?>;
        border-color: <?= $this->view->styles->navigationPrimary ?>;
    }
    .navbar-default .navbar-nav>.open>a, .navbar-default .navbar-nav>.open>a:hover, .navbar-default .navbar-nav>.open>a:focus {
        color: white;
        background-color: <?= $this->view->styles->navigationSecondary ?>;
    }
    .navbar-default .navbar-nav>li>a:hover, .navbar-default .navbar-nav>li>a:focus {
        color: <?= $this->view->styles->navigationSecondaryA ?>;
    }
    .navbar-default .navbar-nav>li>a {
        color: <?= $this->view->styles->navigationPrimaryA ?>;
    }
    .navbar-default .navbar-nav>.active>a, .navbar-default .navbar-nav>.active>a:hover, .navbar-default .navbar-nav>.active>a:focus {
        color: white;
        background-color: <?= $this->view->styles->navigationSecondary ?>;
    }
    a:link {
        color: <?= $this->view->styles->a ?>;
    }
    a:visited {
        color: <?= $this->view->styles->a ?>;
    }
    a:hover {
        color: <?= $this->view->styles->aHover ?>;
    }
    .progress-bar {
        background-color: <?= $this->view->styles->progressBar ?>;
    }
    .btn-primary {
        background-color: <?= $this->view->styles->buttonPrimary ?>;
        border-color: <?= $this->view->styles->buttonPrimaryBorder ?>;
        color: <?= $this->view->styles->buttonPrimaryColor ?> !important;
    }
    .btn-primary:hover{
        background-color: <?= $this->view->styles->buttonPrimaryHover ?>;
        border-color: <?= $this->view->styles->buttonPrimaryHoverBorder ?> !important;
    }

    .btn-primary.disabled, .btn-primary[disabled], fieldset[disabled] .btn-primary, .btn-primary.disabled:hover, .btn-primary[disabled]:hover, fieldset[disabled] .btn-primary:hover, .btn-primary.disabled:focus, .btn-primary[disabled]:focus, fieldset[disabled] .btn-primary:focus, .btn-primary.disabled:active, .btn-primary[disabled]:active, fieldset[disabled] .btn-primary:active, .btn-primary.disabled.active, .btn-primary[disabled].active, fieldset[disabled] .btn-primary.active {
        background-color: <?= $this->view->styles->disabledControl ?>;
        border-color: <?= $this->view->styles->disabledControl ?>;
    }

    body {
        font-family: <?= $this->view->styles->font ?>;
        color: <?= $this->view->styles->fontColor ?>;
    }

    .ajax-loader-container{
        background-color: <?= $this->view->styles->navigationSecondary ?>;
    }

</style>
