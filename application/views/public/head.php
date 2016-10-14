<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
    <title>Tabs - Material</title>

    <!-- css -->
    <link href="../css/base.min.css" rel="stylesheet">
    <link href="../css/project.min.css" rel="stylesheet">

    <!-- favicon -->
    <!-- ... -->
</head>
<body class="page-brand">
<header class="header header-transparent header-waterfall ui-header">
    <ul class="nav nav-list pull-left">
        <li>
            <a data-toggle="menu" href="#ui_menu">
                <span class="icon icon-lg">menu</span>
            </a>
        </li>
    </ul>
    <a class="header-logo header-affix-hide margin-left-no margin-right-no" data-offset-top="213" data-spy="affix" href="index.html">Material</a>
    <span class="header-logo header-affix margin-left-no margin-right-no" data-offset-top="213" data-spy="affix">Tabs</span>
    <ul class="nav nav-list pull-right visible-xx-block">
        <li class="dropdown margin-right">
            <a class="dropdown-toggle padding-left-no padding-right-no" data-toggle="dropdown">
                <span class="access-hide">John Smith</span>
                <span class="avatar avatar-sm"><img alt="alt text for John Smith avatar" src="../images/users/avatar-001.jpg"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a class="padding-right-lg waves-attach" href="javascript:void(0)"><span class="icon icon-lg margin-right">account_box</span>Profile Settings</a>
                </li>
                <li>
                    <a class="padding-right-lg waves-attach" href="javascript:void(0)"><span class="icon icon-lg margin-right">add_to_photos</span>Upload Photo</a>
                </li>
                <li>
                    <a class="padding-right-lg waves-attach" href="page-login.html"><span class="icon icon-lg margin-right">exit_to_app</span>Logout</a>
                </li>
            </ul>
        </li>
    </ul>
    <nav class="tab-nav pull-right hidden-xx">
        <ul class="nav nav-list">
            <li class="active">
                <a class="waves-attach waves-light" data-toggle="tab" href=""><span class="text-white">First Tab</span></a>
            </li>
            <li>
                <a class="waves-attach waves-light" data-toggle="tab" href=""><span class="text-white">Second Tab</span></a>
            </li>
            <li>
                <a class="waves-attach waves-light" data-toggle="tab" href=""><span class="text-white">Third Tab</span></a>
            </li>
        </ul>
    </nav>
</header>
<nav aria-hidden="true" class="menu" id="ui_menu" tabindex="-1">
    <div class="menu-scroll">
        <div class="menu-content">
            <a class="menu-logo" href="index.html">Material</a>
            <ul class="nav">

                <li>
                    <a class="collapsed waves-attach" data-toggle="collapse" href="#ui_menu_extras">Extras</a>
                    <ul class="menu-collapse collapse" id="ui_menu_extras">
                        <li>
                            <a class="waves-attach" href="ui-avatar.html">Avatars</a>
                        </li>
                        <li>
                            <a class="waves-attach" href="ui-icon.html">Icons</a>
                        </li>
                        <li>
                            <a class="waves-attach" href="ui-label.html">Labels</a>
                        </li>
                        <li>
                            <a class="waves-attach" href="ui-nav.html">Navs</a>
                        </li>
                        <li>
                            <a class="waves-attach" href="ui-tile.html">Tiles</a>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</nav>
<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2">
                    <h1 class="content-heading">Tabs</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2">

                <section class="content-inner margin-top-no">
                    <nav class="tab-nav ui-tab">
                        <ul class="nav nav-list">
                            <li class="active">
                                <a class="waves-attach waves-light" data-toggle="tab" href=""><span class="text-white">First Tab</span></a>
                            </li>
                            <li>
                                <a class="waves-attach waves-light" data-toggle="tab" href=""><span class="text-white">Second Tab</span></a>
                            </li>
                            <li>
                                <a class="waves-attach waves-light" data-toggle="tab" href=""><span class="text-white">Third Tab</span></a>
                            </li>
                        </ul>
                    </nav>
                </section>

            </div>
        </div>
    </div>
</main>
