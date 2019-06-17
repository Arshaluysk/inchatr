<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <title>Espire - Bootstrap 4 Admin Template</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo base_url();?>assets/images/favicon.png"> 
    <!-- plugins css -->
    <link rel="stylesheet" href="<?php echo base_url();?>new_assets/vendors/bootstrap/dist/css/bootstrap.css" />
    <link rel="stylesheet" href="<?php echo base_url();?>new_assets/vendors/PACE/themes/blue/pace-theme-minimal.css" />
    <link rel="stylesheet" href="<?php echo base_url();?>new_assets/vendors/perfect-scrollbar/css/perfect-scrollbar.min.css" />

    <!-- page plugins css -->
    <link rel="stylesheet" href="<?php echo base_url();?>new_assets/vendors/bower-jvectormap/jquery-jvectormap-1.2.2.css" />
    <link rel="stylesheet" href="<?php echo base_url();?>new_assets/vendors/nvd3/build/nv.d3.min.css" />

    <!-- core css -->
    <link href="<?php echo base_url();?>new_assets/css/ei-icon.css" rel="stylesheet">
    <link href="<?php echo base_url();?>new_assets/css/themify-icons.css" rel="stylesheet">
    <link href="<?php echo base_url();?>new_assets/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo base_url();?>new_assets/css/animate.min.css" rel="stylesheet">
    <link href="<?php echo base_url();?>new_assets/css/app.css" rel="stylesheet">
        <?php $this->load->view('include/js_include_back');?>
</head>

<body>
    <div class="app">
        <div class="layout">
            <!-- Side Nav START -->
          <?php 
            $this->load->view('admin/theme/new_sidebar');
          ?>
            <!-- Side Nav END -->

            <!-- Page Container START -->
            <div class="page-container">
                <!-- Header START -->
                <?php 
                $this->load->view('admin/theme/new_header');
                ?>
                <!-- Header END -->

                <!-- Side Panel START -->
                <?php 
                $this->load->view('admin/theme/new_side_panel');
                ?>
                <!-- Side Panel END -->

                <!-- theme configurator START -->
                <?php 
                $this->load->view('admin/theme/new_theme_configurator');
                ?>
                <!-- theme configurator END -->

                <!-- Theme Toggle Button START -->
                <button class="theme-toggle btn btn-rounded btn-icon">
                    <i class="ti-palette"></i>
                </button>
                <!-- Theme Toggle Button END -->

                <!-- Content Wrapper START -->
                <div class="main-content">
<!--                     <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="card">
                                    <div class="card-block">
                                        <div class="inline-block">
                                            <h1 class="no-mrg-vertical">$168.90</h1>
                                            <p>This Month</p>
                                        </div>
                                        <div class="pdd-top-25 inline-block pull-right">
                                            <span class="label label-success label-lg mrg-left-5">+18%</span>
                                        </div>
                                        <div class="mrg-top-25">
                                            <div id="bar-config"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-block">
                                        <p class="mrg-btm-5">This Quarter</p>
                                        <h1 class="no-mrg-vertical font-size-35">$3,936<b class="font-size-16">.80</b></h1>
                                        <p class="text-semibold">Total Revenue</p>
                                        <div class="mrg-top-10">
                                            <h2 class="no-mrg-btm">88</h2>
                                            <span class="inline-block mrg-btm-10 font-size-13 text-semibold">Online Revenue</span>
                                            <span class="pull-right pdd-right-10 font-size-13">70%</span>
                                            <div class="progress progress-primary">
                                                <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:70%">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mrg-top-10">
                                            <h2 class="no-mrg-btm">69</h2>
                                            <span class="inline-block mrg-btm-10 font-size-13 text-semibold">Offline Revenue</span>
                                            <span class="pull-right pdd-right-10 font-size-13">50%</span>
                                            <div class="progress progress-success">
                                                <div class="progress-bar" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:50%">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <div class="card">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="maps map-500 padding-20">
                                                <div id="monthly-target">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 border left border-hide-sm">
                                            <div class="card-block">
                                                <h2>Allocation </h2>
                                                <div class="mrg-top-40">
                                                    <div>
                                                        <canvas height="230" id="allocation-chart"></canvas>
                                                    </div>
                                                </div>
                                                <div class="widget-legends mrg-top-30">
                                                    <div class="relative mrg-top-15">
                                                        <span class="status info"> </span>
                                                        <span class="pdd-left-20 font-size-16"><b class="text-dark">25%</b> Texas</span>
                                                    </div>
                                                    <div class="relative mrg-top-15">
                                                        <span class="status primary"> </span>
                                                        <span class="pdd-left-20 font-size-16"><b class="text-dark">45%</b> Utah</span>
                                                    </div>
                                                    <div class="relative mrg-top-15">
                                                        <span class="status success"> </span>
                                                        <span class="pdd-left-20 font-size-16"><b class="text-dark">10%</b> Georgia</span>
                                                    </div>
                                                    <div class="relative mrg-top-15">
                                                        <span class="status"> </span>
                                                        <span class="pdd-left-20 font-size-16"><b class="text-dark">15%</b> Nebraska</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-none d-md-inline-block">
                                        <div class="text-center">
                                            <div class="row">
                                                <div class="col-md-10 ml-auto mr-auto">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="pdd-vertical-5">
                                                                <p class="no-mrg-btm"><b class="text-dark font-size-16">968</b> Customers</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="pdd-vertical-5">
                                                                <p class="no-mrg-btm"><b class="text-dark font-size-16">1.8k</b> Orders</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="pdd-vertical-5">
                                                                <p class="no-mrg-btm"><b class="text-dark font-size-16">30k</b> Stock Left</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="pdd-vertical-5">
                                                                <p class="no-mrg-btm"><b class="text-dark font-size-16">1.7k</b> Pending</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-7 col-md-12">
                                <div class="widget card">
                                    <div class="card-block">
                                        <h5 class="card-title">Monthly Overview</h5>
                                        <div class="row mrg-top-30">
                                            <div class="col-md-3 col-sm-6 col-6 border right border-hide-md">
                                                <div class="text-center pdd-vertical-10">
                                                    <h2 class="font-primary no-mrg-top">8%</h2>
                                                    <p class="no-mrg-btm">APPL</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-6 border right border-hide-md">
                                                <div class="text-center pdd-vertical-10">
                                                    <h2 class="font-primary no-mrg-top">$1,730</h2>
                                                    <p class="no-mrg-btm">M.AVG</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-6 border right border-hide-md">
                                                <div class="text-center pdd-vertical-10">
                                                    <h2 class="font-primary no-mrg-top">77%</h2>
                                                    <p class="no-mrg-btm">Increment</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 col-6">
                                                <div class="text-center pdd-vertical-10">
                                                    <h2 class="font-primary no-mrg-top">18%</h2>
                                                    <p class="no-mrg-btm">Profit</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mrg-top-35">
                                            <div class="col-md-12">
                                                <div>
                                                    <canvas id="line-chart" height="220"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5 col-md-12">
                                <div class="card">
                                    <div class="card-heading">
                                        <h4 class="card-title inline-block pdd-top-5">Latest Transaction</h4>
                                        <a href="" class="btn btn-default pull-right no-mrg">All Trasaction</a>
                                    </div>
                                    <div class="pdd-horizon-20 pdd-vertical-5">
                                        <div class="overflow-y-auto relative scrollable" style="max-height: 381px">
                                            <table class="table table-lg table-hover">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <div class="list-info">
                                                                <img class="thumb-img" src="<?php echo base_url();?>new_assets/images/avatars/thumb-1.jpg" alt="">
                                                                <div class="info">
                                                                    <span class="title">Jordan Hurst</span>
                                                                    <span class="sub-title">ID 863</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="mrg-top-10">
                                                                <span>8 May</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="relative mrg-top-10">
                                                                <span class="status online"> </span>
                                                                <span class="pdd-left-20">Confirmed</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="list-info">
                                                                <img class="thumb-img" src="<?php echo base_url();?>new_assets/images/avatars/thumb-4.jpg" alt="">
                                                                <div class="info">
                                                                    <span class="title">Samuel Field</span>
                                                                    <span class="sub-title">ID 868</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="mrg-top-10">
                                                                <span>8 May</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="relative mrg-top-10">
                                                                <span class="status away"> </span>
                                                                <span class="pdd-left-20">Pendding</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="list-info">
                                                                <img class="thumb-img" src="<?php echo base_url();?>new_assets/images/avatars/thumb-5.jpg" alt="">
                                                                <div class="info">
                                                                    <span class="title">Jennifer Watkins</span>
                                                                    <span class="sub-title">ID 860</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="mrg-top-10">
                                                                <span>8 May</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="relative mrg-top-10">
                                                                <span class="status online"> </span>
                                                                <span class="pdd-left-20">Confirmed</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="list-info">
                                                                <img class="thumb-img" src="<?php echo base_url();?>new_assets/images/avatars/thumb-6.jpg" alt="">
                                                                <div class="info">
                                                                    <span class="title">Michael Birch</span>
                                                                    <span class="sub-title">ID 861</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="mrg-top-10">
                                                                <span>8 May</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="relative mrg-top-10">
                                                                <span class="status no-disturb"> </span>
                                                                <span class="pdd-left-20">Rejected</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="list-info">
                                                                <img class="thumb-img" src="<?php echo base_url();?>new_assets/images/avatars/thumb-7.jpg" alt="">
                                                                <div class="info">
                                                                    <span class="title">Jordan Hurst</span>
                                                                    <span class="sub-title">ID 862</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="mrg-top-10">
                                                                <span>8 May</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="relative mrg-top-10">
                                                                <span class="status away"> </span>
                                                                <span class="pdd-left-20">Pendding</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <div class="card widget-weather">
                                    <div class="card-block">
                                        <h5 class="card-title">New York, 22 July</h5>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="inline-block">
                                                    <h1 class="today-cel">
                              <span>28°</span>
                              <i class="ei-partialy-cloudy text-warning"></i> 
                            </h1>
                                                    <p>Partly Sunny</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row border bottom mrg-top-30">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-4 col-sm-4 col-4">
                                                        <h3 class="no-mrg-btm">22°/28°</h3>
                                                        <p class="font-size-13">Temp</p>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-4">
                                                        <h3 class="no-mrg-btm">61%</h3>
                                                        <p class="font-size-13">Humidity</p>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-4">
                                                        <h3 class="no-mrg-btm">18<span class="font-size-13">km/h</span></h3>
                                                        <p class="font-size-13">Wind</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mrg-top-35">
                                            <div class="col-md-2 col-sm-2 col-2">
                                                <div class="next-7day">
                                                    <span class="display-block">WED</span>
                                                    <h2 class="mrg-top-10"><i class="ei-cloud"></i></h2>
                                                    <span>28°</span>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-2 col-2">
                                                <div class="next-7day">
                                                    <span class="display-block">THU</span>
                                                    <h2 class="mrg-top-10"><i class="ei-breeze"></i></h2>
                                                    <span>23°</span>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-2 col-2">
                                                <div class="next-7day">
                                                    <span class="display-block">FRI</span>
                                                    <h2 class="mrg-top-10"><i class="ei-blizzard"></i></h2>
                                                    <span>25°</span>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-2 col-2">
                                                <div class="next-7day">
                                                    <span class="display-block">SAT</span>
                                                    <h2 class="mrg-top-10"><i class="ei-sunny-day"></i></h2>
                                                    <span>27°</span>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-2 col-2">
                                                <div class="next-7day">
                                                    <span class="display-block">SUN</span>
                                                    <h2 class="mrg-top-10"><i class="ei-partialy-cloudy"></i></h2>
                                                    <span>24°</span>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-2 col-2">
                                                <div class="next-7day">
                                                    <span class="display-block">MON</span>
                                                    <h2 class="mrg-top-10"><i class="ei-sunny-day"></i></h2>
                                                    <span>26°</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="card">
                                    <div class="card-heading border bottom">
                                        <h4 class="card-title">Latest Feed</h4>
                                    </div>
                                    <div class="widget-feed">
                                        <ul class="list-info overflow-y-auto relative scrollable" style="max-height: 340px">
                                            <li class="border bottom mrg-btm-10">
                                                <div class="pdd-vertical-10">
                                                    <span class="thumb-img bg-primary">
                              <span class="text-white">JH</span>
                                                    </span>
                                                    <div class="info">
                                                        <a href="" class="text-link"><span class="title"><b class="font-size-15">Jordan Hurst</b></span></a>
                                                        <span class="sub-title">5 mins ago</span>
                                                    </div>
                                                    <div class="mrg-top-10">
                                                        <p class="no-mrg-btm">Remember, a Jedi can feel the Force flowing through him. You mean it controls your actions? Partially.</p>
                                                    </div>
                                                    <ul class="feed-action">
                                                        <li>
                                                            <a href="">
                                                                <i class="ti-heart text-danger pdd-right-5"></i>
                                                                <span>168</span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="">
                                                                <i class="ti-comments text-primary pdd-right-5"></i>
                                                                <span>18</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </li>
                                            <li class="border bottom mrg-btm-10">
                                                <div class="pdd-vertical-10">
                                                    <span class="thumb-img bg-success">
                              <span class="text-white">JW</span>
                                                    </span>
                                                    <div class="info">
                                                        <a href="" class="text-link"><span class="title"><b class="font-size-15">Jennifer Watkins</b></span></a>
                                                        <span class="sub-title">5 mins ago</span>
                                                    </div>
                                                    <div class="mrg-top-15">
                                                        <p>What good's a reward if you ain't around to use it?</p>
                                                    </div>
                                                    <ul class="feed-action">
                                                        <li>
                                                            <a href="">
                                                                <i class="ti-heart text-danger pdd-right-5"></i>
                                                                <span>168</span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="">
                                                                <i class="ti-comments text-primary pdd-right-5"></i>
                                                                <span>18</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </li>
                                            <li class="border bottom">
                                                <div class="pdd-vertical-10">
                                                    <span class="thumb-img bg-warning">
                              <span class="text-white">MB</span>
                                                    </span>
                                                    <div class="info">
                                                        <a href="" class="text-link"><span class="title"><b class="font-size-15">Michael Birch</b></span></a>
                                                        <span class="sub-title">5 mins ago</span>
                                                    </div>
                                                    <div class="mrg-top-15">
                                                        <p>What good's a reward if you ain't around to use it? Besides, attacking that battle station ain't my idea of courage.</p>
                                                    </div>
                                                    <ul class="feed-action">
                                                        <li>
                                                            <a href="">
                                                                <i class="ti-heart text-danger pdd-right-5"></i>
                                                                <span>168</span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="">
                                                                <i class="ti-comments text-primary pdd-right-5"></i>
                                                                <span>18</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="card">
                                    <div class="card-heading border bottom">
                                        <h4 class="card-title">Project</h4>
                                    </div>
                                    <div class="card-block">
                                        <div class="pdd-vertical-10">
                                            <ul class="list-info">
                                                <li>
                                                    <img class="thumb-img img-circle" src="<?php echo base_url();?>new_assets/images/others/thumb-1.jpg" alt="">
                                                    <div class="info">
                                                        <span class="title"><a href="" class="text-link text-dark"><b class="font-size-15">Devolopment - Android App</b></a></span>
                                                        <span class="sub-title">Android App</span>
                                                        <div class="float-object dropdown right">
                                                            <i class="ti-android-o"></i>
                                                            <a href="" class="btn btn-icon btn-flat btn-rounded dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                                <i class="ti-more"></i>
                                                            </a>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a href="">
                                                                        <i class="ti-files pdd-right-10"></i>
                                                                        <span>Duplicate</span>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="">
                                                                        <i class="ti-smallcap pdd-right-10"></i>
                                                                        <span>Edit</span>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="">
                                                                        <i class="ti-image pdd-right-10"></i>
                                                                        <span>Add Images</span>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="mrg-top-20">
                                                        <p>All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary.</p>
                                                    </div>
                                                    <div class="mrg-top-30">
                                                        <b class="pull-left lh-2-5 pdd-right-15">Team: </b>
                                                        <ul class="list-members list-inline">
                                                            <li>
                                                                <a href="">
                                                                    <img src="<?php echo base_url();?>new_assets/images/avatars/thumb-1.jpg" alt="">
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="">
                                                                    <img src="<?php echo base_url();?>new_assets/images/avatars/thumb-2.jpg" alt="">
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="">
                                                                    <img src="<?php echo base_url();?>new_assets/images/avatars/thumb-3.jpg" alt="">
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="">
                                                                    <img src="<?php echo base_url();?>new_assets/images/avatars/thumb-4.jpg" alt="">
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="">
                                                                    <img src="<?php echo base_url();?>new_assets/images/avatars/thumb-5.jpg" alt="">
                                                                </a>
                                                            </li>
                                                            <li class="all-members">
                                                                <a href="">
                                                                    <span>+2</span>
                                                                </a>
                                                            </li>
                                                            <li class="add-member">
                                                                <a href="">
                                                                    <span>+</span>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class=" mrg-top-30">
                                                        <span>Due date: <span class="text-success text-semibold">23/7/2017</span></span>
                                                    </div>
                                                    <div class="mrg-top-30">
                                                        <p class="mrg-btm-5">Task completed: <span class="text-dark text-semibold">7/10</span></p>
                                                        <div class="progress progress-info">
                                                            <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:70%">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->

                  <?php
                  if($this->uri->segment(2)=="login_config" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
                  { ?>  
                    <div class="row">
                       <?php echo "Google auth redirect URL : <span class='blue'>". base_url("home/google_login_back"); ?></span>
                    </div>
                    <div class="row">
                       <h4>Facebook URLs</h4><hr>
                        <?php echo "App Domain : <span class='blue'>".get_domain_only(base_url()); ?></span><br/>
                        <?php echo "Site URL : <span class='blue'>".base_url(); ?> </span><br/>
                        <?php echo "Valid OAuth redirect URI : <span class='blue'>".base_url("home/fb_login_back"); ?></span><br/>
                    </div>
                  <?php } ?>

                  <?php
                  if($this->uri->segment(1)=="facebook_rx_config" && $this->uri->segment(2)=="index" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
                  { 
                    if($this->config->item('developer_access') != '1')
                    {
                  ?>
                   <div class="row">
                       <h4>Facebook URLs</h4><hr>
                        <?php echo "App Domain : <span class='blue'>".get_domain_only(base_url()); ?></span><br/>
                        <?php echo "<br>Site URL : <span class='blue'>".base_url(); ?></span><br/>
                        <?php echo "<br>Privacy Policy URL : <span class='blue'>".base_url('home/privacy_policy');?></span><br>
                        <?php echo "Terms of Service URL : <span class='blue'>".base_url('home/terms_use'); ?></span><br/>
                        <?php echo "<br>Valid OAuth redirect URIs : "; ?> </span><br/>
                        <?php echo "<span class='blue'>".base_url("home/redirect_rx_link"); ?></span><br/>
                        <?php echo "<span class='blue'>".base_url("facebook_rx_account_import/manual_renew_account"); ?></span><br/>
                        <?php echo "<span class='blue'>".base_url("facebook_rx_account_import/redirect_custer_link"); ?></span><br/>
                        <?php echo "<br>Webhooks Setup : "; ?> </span><br/>
                        <?php echo "Callback URL : <span class='blue'> ".base_url("home/central_webhook_callback"); ?></span><br/>
                        <?php 
                          if($this->config->item('central_webhook_verify_token') == '')
                          {
                            $verify_token= substr(uniqid(mt_rand(), true), 0, 10);
                            include('application/config/my_config.php');
                            if(!isset($config['central_webhook_verify_token']))
                            {                  
                              $config['central_webhook_verify_token'] = $verify_token;
                              file_put_contents('application/config/my_config.php', '<?php $config = ' . var_export($config, true) . ';');
                              redirect($this->uri->uri_string());
                            }
                          }
                        ?>
                        <?php echo "Verify Token : <span class='blue'>".$this->config->item('central_webhook_verify_token');?></span><br/>
                    </div>
                  <?php 
                    }
                    if($this->config->item('developer_access') == '1' && $this->session->userdata('user_type') == 'Admin')
                    {         

                      ?>
                      <div class="row">
                        <h4>In order to get Secret Code, Plese follow the steps below</h4><hr>
                        <ol>
                          <li>Please go to <a href="https://ac.getapptoken.com/home/login_page" target="_blank">https://ac.getapptoken.com/</a></li>
                          <li>Sign up there providing your <a href="https://codecanyon.net/item/fb-inboxer-master-facebook-messenger-marketing-software/19578006?ref=xeroneitbd" target="_blank">FBInboxer</a> purchase code. Then you'll receive an email to activate your account.</li>
                          <li>After login to that system, click the login with Facebook button.</li>
                          <li>Then you'll get a secret code and use that secret code here and then click the login button here.</li>
                          <li>You are done!</li>
                        </ol>
                      </div>
                      <?php
                    }
                    
                  }
                  ?>

                  <?php 
                    if($this->session->userdata('secret_code_error') != '')
                    {
                      echo "<div><h4 style='margin:0'><div class='alert alert-danger text-center'><i class='fa fa-remove'></i> ".$this->session->userdata('secret_code_error')."</div></h4></div>";
                      $this->session->unset_userdata('secret_code_error');
                    } 
                  ?>
                   <?php
                   if($this->uri->segment(1)=="comboposter" && $this->uri->segment(2)=="twitter_settings" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
                   { ?>
                    <div class="row">
                        <h4><?php echo $this->lang->line("Twitter Redirect URLs:"); ?>  </h4><hr>
                        
                         <?php echo "<span class='blue'>".base_url("comboposter/twitter_login_callback");?></span><br/>
                       
                     </div>
                   <?php } ?>

                   <?php
                   if($this->uri->segment(1)=="comboposter" && $this->uri->segment(2)=="tumblr_settings" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
                   { ?>
                    <div class="row">
                        <h4>Tumblr Redirect URLs: </h4><hr>
                        
                         <?php echo "<span class='blue'>".base_url("comboposter/tumblr_login_callback"); ?></span><br/>
                       
                     </div>
                   <?php } ?>


                   <?php
                   if($this->uri->segment(1)=="comboposter" && $this->uri->segment(2)=="linkedin_settings" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
                   { ?>
                    <div class="row">
                        <h4>Linkedin Redirect URLs: </h4><hr>
                        
                         <?php echo "<span class='blue'>".base_url("comboposter/linkedin_login_callback"); ?></span><br/>
                     </div>
                   <?php } ?>

                   <?php
                   if($this->uri->segment(1)=="comboposter" && $this->uri->segment(2)=="medium_config" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
                   { ?>
                    <div class="row">
                        <h4><?php echo $this->lang->line('Medium Redirect URLs'); ?>: </h4><hr>
                        
                         <?php echo "<span class='blue'>".base_url("comboposter/medium_login_callback"); ?></span><br/>
                       
                     </div>
                   <?php } ?>

                   <?php
                   if($this->uri->segment(1)=="comboposter" && $this->uri->segment(2)=="add_pinterest_settings" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
                   { ?>
                    <div class="row">
                        <h4>pinterest Redirect URLs: </h4><hr>
                        
                         <?php echo "<span class='blue'>".base_url("comboposter/pinterest_login_callback"); ?></span><br/>
                       
                     </div>
                   <?php } ?>

                   <?php
                   if($this->uri->segment(1)=="comboposter" && $this->uri->segment(2)=="reddit_config" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
                   { ?>
                    <div class="row">
                        <h4><?php echo $this->lang->line("Reddit Redirect URLs"); ?>  : </h4><hr>
                        
                         <?php echo "<span class='blue'>".base_url("comboposter/reddit_callback"); ?></span><br/>
                       
                     </div>
                   <?php } ?>

                   <?php
                   if($this->uri->segment(1)=="comboposter" && $this->uri->segment(2)=="wp_org_config" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
                   { ?>
                    <div class="row">
                        <h4>Wordpress Redirect URLs: </h4><hr>
                        
                         <?php echo "<span class='blue'>".base_url("comboposter/wp_org_login_callback"); ?></span><br/>
                       
                     </div>
                   <?php } ?>

                   <?php
                   if($this->uri->segment(1)=="comboposter" && $this->uri->segment(2)=="youtube_config" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
                   { ?>
                    <div class="row">
                        <h4><?php echo $this->lang->line('Youtube and blogger Redirect URLs'); ?>: </h4><hr>
                        
                          <?php echo "<br>".$this->lang->line('Blogger redirect URL')." : <span class='blue'>".base_url('comboposter/blogger_login_callback');?></span><br>
                           <?php echo "<br>".$this->lang->line('Youtube redirect URL')." : <span class='blue'>".base_url('comboposter/login_redirect'); ?></span>
                         </div>
                   <?php } ?>
                  
                   <?php
                  if($this->uri->segment(1)=="messenger_bot" && $this->uri->segment(2)=="facebook_config" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
                  { ?>
                   <div class="row">
                       <h4>Facebook URLs</h4><hr>
                        <?php echo "App Domain : <span class='blue'>".get_domain_only(base_url()); ?></span><br/>
                        <?php echo "<br>Site URL : <span class='blue'>".base_url(); ?></span><br/>
                        <?php echo "<br>Privacy Policy URL : <span class='blue'>".base_url('home/privacy_policy'); ?></span><br>
                        <?php echo "Terms of Service URL : <span class='blue'>".base_url('home/terms_use'); ?></span><br/>
                        <?php echo "<br>Valid OAuth redirect URIs : "; ?> </span><br/>
                        <?php echo "<span class='blue'>".base_url("messenger_bot/login_callback"); ?></span><br/>
                        <?php echo "<span class='blue'>".base_url("messenger_bot/refresh_login_callback"); ?></span><br/>
                        <?php echo "<span class='blue'>".base_url("messenger_bot/user_login_callback"); ?></span><br/>
                        <?php echo "<br>Webhooks Setup : "; ?> </span><br/>
                        <?php echo "Callback URL : <span class='blue'> ".base_url("home/central_webhook_callback"); ?></span><br/>
                        <?php 
                          if($this->config->item('central_webhook_verify_token') == '')
                          {
                            $verify_token= substr(uniqid(mt_rand(), true), 0, 10);
                            include('application/config/my_config.php');
                            if(!isset($config['central_webhook_verify_token']))
                            {                  
                              $config['central_webhook_verify_token'] = $verify_token;
                              file_put_contents('application/config/my_config.php', '<?php $config = ' . var_export($config, true) . ';');
                              redirect($this->uri->uri_string());
                            }
                          }
                        ?>
                        <?php echo "Verify Token : <span class='blue'>".$this->config->item('central_webhook_verify_token');?></span><br/>
                    </div>
                  <?php } ?> 

                  <?php
                  if($this->uri->segment(1)=="instagram_reply" && $this->uri->segment(2)=="facebook_config" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
                  { ?>
                   <div class="row">
                       <h4>Facebook URLs</h4><hr>
                        <?php echo "App Domain : <span class='blue'>".get_domain_only(base_url()); ?></span><br/>
                        <?php echo "<br>Site URL : <span class='blue'>".base_url(); ?></span><br/>
                        <?php echo "<br>Privacy Policy URL : <span class='blue'>".base_url('home/privacy_policy'); ?></span><br>
                        <?php echo "Terms of Service URL : <span class='blue'>".base_url('home/terms_use'); ?></span><br/>
                        <?php echo "<br>Valid OAuth redirect URIs : "; ?> </span><br/>
                        <?php echo "<span class='blue'>".base_url("instagram_reply/login_callback"); ?></span><br/>
                        <?php echo "<span class='blue'>".base_url("instagram_reply/refresh_login_callback"); ?></span><br/>
                       <!--  <?php echo "<span class='blue'>".base_url("instagram_reply/user_login_callback"); ?></span><br/> -->

                        <?php echo "<br>Webhooks Setup : "; ?> </span><br/>
                        <?php echo "Callback URL :  <span class='blue'> ".base_url("instagram_reply/webhook_callback"); ?></span><br/>
                        <?php echo "Verify Token : <span class='blue'>".$this->config->item('instagram_reply_verify_token');?></span><br/>
                    </div>
                  <?php } ?>

                  
                  <?php
                  if($this->uri->segment(1)=="pageresponse" && $this->uri->segment(2)=="facebook_config" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
                  { ?>
                   <div class="row">
                       <h4>Facebook URLs</h4><hr>
                        <?php echo "App Domain : <span class='blue'>".get_domain_only(base_url()); ?></span><br/>
                        <?php echo "<br>Site URL : <span class='blue'>".base_url(); ?></span><br/>
                        <?php echo "<br>Privacy Policy URL : <span class='blue'>".base_url('home/privacy_policy'); ?></span><br>
                        <?php echo "Terms of Service URL : <span class='blue'>".base_url('home/terms_use'); ?></span><br/>
                        <?php echo "<br>Valid OAuth redirect URIs : "; ?> </span><br/>
                        <?php echo "<span class='blue'>".base_url("pageresponse/login_callback"); ?></span><br/>
                        <?php echo "<span class='blue'>".base_url("pageresponse/refresh_login_callback"); ?></span><br/>
                        <?php echo "<span class='blue'>".base_url("pageresponse/user_login_callback"); ?></span><br/>
                        <?php echo "<br>Webhooks Setup : "; ?> </span><br/>
                        <?php echo "Callback URL : <span class='blue'> ".base_url("home/central_webhook_callback"); ?></span><br/>
                        <?php 
                          if($this->config->item('central_webhook_verify_token') == '')
                          {
                            $verify_token= substr(uniqid(mt_rand(), true), 0, 10);
                            include('application/config/my_config.php');
                            if(!isset($config['central_webhook_verify_token']))
                            {                  
                              $config['central_webhook_verify_token'] = $verify_token;
                              file_put_contents('application/config/my_config.php', '<?php $config = ' . var_export($config, true) . ';');
                              redirect($this->uri->uri_string());
                            }
                          }
                        ?>
                        <?php echo "Verify Token : <span class='blue'>".$this->config->item('central_webhook_verify_token');?></span><br/>
                    </div>
                  <?php } ?>

                  <?php
                  if($this->uri->segment(1)=="vidcasterlive" && $this->uri->segment(2)=="facebook_rx_config" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
                  { ?>
                   <div class="row">
                       <h4>Facebook URLs</h4><hr>
                       <?php echo "App Domain : <span  class='blue'>".get_domain_only(base_url()); ?></span><br/>
                       <?php echo "<br>Site URL : <span  class='blue'>".base_url(); ?></span><br/>
                       <?php echo "<br>Privacy Policy URL : <span  class='blue'>".base_url('home/privacy_policy'); ?></span>
                       <?php echo "<br>Terms of Service URL : <span  class='blue'>".base_url('home/terms_use'); ?></span><br/>
                       <?php echo "<br>Valid OAuth redirect URIs : "; ?> </span><br/>
                       <?php echo "<span  class='blue'>".base_url("vidcasterlive/redirect_rx_link"); ?></span><br/>
                       <?php echo "<span  class='blue'>".base_url("vidcasterlive/manual_renew_account"); ?></span><br/>
                       <?php echo "<span  class='blue'>".base_url("vidcasterlive/redirect_custer_link"); ?></span><br/>
                    </div>
                  <?php } ?>

                  <?php
                   if($this->uri->segment(1)=="vidcasterlive" && $this->uri->segment(2)=="ytube_config" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
                   { ?>
                    <div class="row">
                        <h4><?php echo $this->lang->line('Youtube Redirect URLs'); ?>: </h4><hr>
                           <?php echo $this->lang->line('Youtube redirect URL')." : <span class='blue'>".base_url('vidcasterlive/ytube_login_redirect'); ?></span>
                         </div>
                   <?php } ?>

                  <?php
                  if($this->uri->segment(1)=="email_autoresponder" && $this->uri->segment(2)=="infusionsoft_app_setting" && ($this->uri->segment(3)=="add" || $this->uri->segment(3)=="edit"))
                  { ?>
                   <br>
                   <div class="row">
                        <?php echo "<br>Register Callback URL : "; ?> </span><br/>
                        <?php echo "<span class='blue'>".base_url("email_autoresponder/infusionsoft_login_callback"); ?></span><br/>
                    </div>
                  <?php } ?>

                  <?php 
                    if($crud==1) 
                  $this->load->view('admin/theme/theme_crud',$output); 
                    else 
                  $this->load->view($body);
                  ?>
                </div>
                <!-- Content Wrapper END -->

                <!-- Footer START -->
                <footer class="content-footer">
                    <div class="footer">
                        <div class="copyright">
                            <span>Copyright © 2017 <b class="text-dark">Theme_Nate</b>. All rights reserved.</span>
                            <span class="go-right">
                  <a href="" class="text-gray mrg-right-15">Term &amp; Conditions</a>
                  <a href="" class="text-gray">Privacy &amp; Policy</a>
                </span>
                        </div>
                    </div>
                </footer>
                <!-- Footer END -->

            </div>
            <!-- Page Container END -->

        </div>
    </div>

    <!-- build:js assets/js/vendor.js -->
    <!-- plugins js -->
    <script src="<?php echo base_url();?>new_assets/vendors/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo base_url();?>new_assets/vendors/popper.js/dist/umd/popper.min.js"></script>
    <script src="<?php echo base_url();?>new_assets/vendors/bootstrap/dist/js/bootstrap.js"></script>
    <script src="<?php echo base_url();?>new_assets/vendors/PACE/pace.min.js"></script>
    <script src="<?php echo base_url();?>new_assets/vendors/perfect-scrollbar/js/perfect-scrollbar.jquery.js"></script>
    <!-- endbuild -->

    <!-- page plugins js -->
    <script src="<?php echo base_url();?>new_assets/vendors/bower-jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="<?php echo base_url();?>new_assets/js/maps/jquery-jvectormap-us-aea.js"></script>
    <script src="<?php echo base_url();?>new_assets/vendors/d3/d3.min.js"></script>
    <script src="<?php echo base_url();?>new_assets/vendors/nvd3/build/nv.d3.min.js"></script>
    <script src="<?php echo base_url();?>new_assets/vendors/jquery.sparkline/index.js"></script>
    <script src="<?php echo base_url();?>new_assets/vendors/chart.js/dist/Chart.min.js"></script>

    <!-- build:js <?php echo base_url();?>new_assets/js/app.min.js -->
    <!-- core js -->
    <script src="<?php echo base_url();?>new_assets/js/app.js"></script>
    <!-- endbuild -->

    <!-- page js -->
    <script src="<?php echo base_url();?>new_assets/js/dashboard/dashboard.js"></script>

</body>

</html>
