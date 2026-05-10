<?php
// *************************************************************************
// *                                                                       *
// * DEPRIXA PRO -  Integrated Web Shipping System                         *
// * Copyright (c) JAOMWEB. All Rights Reserved                            *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * Email: support@jaom.info                                              *
// * Website: http://www.jaom.info                                         *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * This software is furnished under a license and may be used and copied *
// * only  in  accordance  with  the  terms  of such  license and with the *
// * inclusion of the above copyright notice.                              *
// * If you Purchased from Codecanyon, Please read the full License from   *
// * here- http://codecanyon.net/licenses/standard                         *
// *                                                                       *
// *************************************************************************
 

$userData = $user->cdp_getUserData();

$db = new Conexion;

// Obtener el mes y el año actual
$month = date('m');
$year = date('Y');

// Obtener el número del mes actual
$currentMonth = date('n');

// Obtener el nombre del mes actual
$monthName = obtenerNombreMes($currentMonth);

?>
<!DOCTYPE html>
<html dir="<?php echo $direction_layout; ?>" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="Courier DEPRIXA-Integral Web System" />
    <meta name="author" content="Jaomweb">
    <title><?php echo $lang['left-menu-sidebar-2'] ?> | <?php echo $core->site_name ?></title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="assets/<?php echo $core->favicon ?>">

    <?php include 'views/inc/head_scripts.php'; ?>
    <script src="assets/template/assets/extra-libs/chart.js-2.8/Chart.min.js"></script>

</head>

<body>
    <?php include 'views/inc/preloader.php'; ?>

    <div id="main-wrapper">
        <!-- ============================================================== -->
        <!-- Preloader - style you can find in spinners.css -->
        <!-- ============================================================== -->


        <!-- ============================================================== -->
        <!-- Preloader - style you can find in spinners.css -->
        <!-- ============================================================== -->

        <?php include 'views/inc/topbar.php'; ?>

        <!-- End Topbar header -->


        <!-- Left Sidebar - style you can find in sidebar.scss  -->

        <?php include 'views/inc/left_sidebar.php'; ?>


        <!-- End Left Sidebar - style you can find in sidebar.scss  -->

        <!-- Page wrapper  -->

        <div class="page-wrapper">
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title"><?php echo $lang['left-menu-sidebar-2'] ?></h4>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">

                <!-- ============================================================== -->
                <!-- Earnings, Sale Locations -->
                <!-- ============================================================== -->

                <div class="row">
                    <!-- View sales -->
                   <div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-end row">
                                  <div class="col-8">
                                    <div class="text-nowrap">
                                      <h5 class="card-title mb-3"><?php echo $lang['messagesform84'] ?></h5>
                                      
                                      <h4 class="text-primary mb-2">
                                        <?php echo $core->currency; ?>
                                        <?php
                                            // Consulta SQL
                                            $sql = "SELECT IFNULL(SUM(total_order), 0) as total 
                                                    FROM cdb_add_order 
                                                    WHERE status_courier != 21 
                                                    AND status_invoice != 0 
                                                    AND order_payment_method > 1 
                                                    AND MONTH(order_date) = :month 
                                                    AND YEAR(order_date) = :year";

                                            // Preparar la consulta
                                            $db->cdp_query($sql);
                                            // Vincular parámetros
                                            $db->bind(':month', $month);
                                            $db->bind(':year', $year);
                                            // Ejecutar la consulta
                                            $db->cdp_execute();
                                            // Obtener el registro
                                            $count = $db->cdp_registro();
                                            // Mostrar el total de ventas
                                            echo cdb_money_format($count->total);
                                        ?>

                                      </h4>
                                      <a href="dashboard_admin_account.php" class="btn btn-primary"><?php echo $lang['messagesform83'] ?></a>
                                    </div>
                                  </div>
                                  <div class="col-4 text-center text-sm-left">
                                    <div class="card-body pb-0 px-0 px-md-4">
                                      <div class="m-r-10"><span class="text-primary display-6"><i class="mdi mdi-chart-line"></i></span></div>
                                    </div>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- View sales -->

                    <!-- Statistics -->
                    <div class="col-12 col-sm-10 col-md-8 col-lg-8 col-xl-8 mb-4">
                        <div class="card">
                            <div class="card-body">
                              <div class="d-flex justify-content-between mb-3">
                                <h4 class="card-title mb-1"><?php echo $lang['messagesform89'] ?></h4>
                                <small class="text-muted"><?php echo $lang['messagesform90'] ?> <?php echo $monthName; ?></small>
                              </div>

                            </div>
                            <div class="card-body">
                              <div class="row gy-3">
                                <div class="col-md-4 col-12">
                                  <div class="d-flex align-items-center">
                                    <div class="badge rounded-pill bg-label-info me-3 p-2" style="width:60px; height:60px; display:flex; align-items:center; justify-content:center;">
                                      <i class="mdi mdi-truck" style="font-size:28px;"></i>
                                    </div>
                                    <div class="card-info">
                                      <h5 class="mb-0">
                                        <?php echo $core->currency; ?>
                                        <?php
                                            $db->cdp_query('SELECT IFNULL(SUM(total_order),0) as total FROM cdb_add_order where status_courier != 21 and order_incomplete != 0 and is_pickup = 1
                                                AND MONTH(order_date) = :month 
                                                AND YEAR(order_date) = :year');
                                            // Vincular parámetros
                                            $db->bind(':month', $month);
                                            $db->bind(':year', $year);
                                            // Ejecutar la consulta
                                            $db->cdp_execute();
                                            // Obtener el registro
                                            $count = $db->cdp_registro();
                                            $sum2 = $count->total;
                                            echo cdb_money_format($sum2);
                                        ?>
                                      </h5>
                                      <small><?php echo $lang['dash-general-11'] ?></small>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-4 col-12">
                                  <div class="d-flex align-items-center">
                                    <div class="badge rounded-pill bg-label-primary me-3 p-2" style="width:60px; height:60px; display:flex; align-items:center; justify-content:center;">
                                      <i class="mdi mdi-package-variant-closed" style="font-size:28px;"></i>
                                    </div>
                                    <div class="card-info">
                                      <h5 class="mb-0">
                                        <?php echo $core->currency; ?>
                                        <?php
                                        $db->cdp_query('SELECT IFNULL(SUM(total_order),0) as total FROM cdb_add_order where status_courier != 21 and is_pickup = 0
                                            AND MONTH(order_date) = :month 
                                            AND YEAR(order_date) = :year');
                                        // Vincular parámetros
                                        $db->bind(':month', $month);
                                        $db->bind(':year', $year);
                                        // Ejecutar la consulta
                                        $db->cdp_execute();
                                        // Obtener el registro
                                        $count = $db->cdp_registro();
                                        $sum1 = $count->total;
                                        echo cdb_money_format($sum1);
                                        ?>
                                      </h5>
                                      <small><?php echo $lang['dash-general-10'] ?></small>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-4 col-12">
                                  <div class="d-flex align-items-center">
                                    <div class="badge rounded-pill bg-label-success me-3 p-2" style="width:60px; height:60px; display:flex; align-items:center; justify-content:center;">
                                      <i class="mdi mdi-shopping" style="font-size:28px;"></i>
                                    </div>
                                    <div class="card-info">
                                      <h5 class="mb-0">
                                        <?php echo $core->currency; ?>

                                        <?php

                                        $db->cdp_query('SELECT IFNULL(SUM(total_order),0) as total FROM cdb_customers_packages where status_courier != 21
                                            AND MONTH(order_date) = :month 
                                            AND YEAR(order_date) = :year');
                                        // Vincular parámetros
                                        $db->bind(':month', $month);
                                        $db->bind(':year', $year);
                                        // Ejecutar la consulta
                                        $db->cdp_execute();
                                        // Obtener el registro
                                        $count1 = $db->cdp_registro();
                                        $sum3 = $count1->total;
                                        echo cdb_money_format($sum3);
                                        ?>
                                                       
                                      </h5>
                                      <small><?php echo $lang['messagesform85'] ?></small>
                                    </div>
                                  </div>
                                </div>
                                
                              </div>
                            </div>
                        </div>
                    </div>
                    <!--/ Statistics -->
                </div> 

                <div class="row">
                    <!-- Earning Reports -->
                    <div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-4 mb-2">
                      <div class="card">
                        <div class="card-body pb-4">
                            <div class="card-header-title d-flex justify-content-between">
                                <div class="card-title mb-0">
                                    <h5 class="m-0 me-2"><?php echo $lang['messagesform95'] ?></h5>
                                    <small class="text-muted"><?php echo $lang['messagesform96'] ?> <?php echo $monthName; ?></small>
                                </div>
                            </div>
                            <div><br></div>
                            <ul class="list-style-none">
                                <li class="mb-0">
                                    <div class="row">
                                        <div class="col-xl-7 col-md-7 mb-2">
                                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                    <h6 class="mb-0"><?php echo $lang['dash-general-11'] ?></h6>
                                                    <small class="text-muted">
                                                        <?php echo $core->currency; ?>
                                                        <?php
                                                        // Ejecutar la consulta SQL para obtener el total de órdenes de compra
                                                        $db->cdp_query('SELECT IFNULL(SUM(total_order),0) as total FROM cdb_add_order where status_courier != 21 and order_incomplete != 0 and is_pickup = 1
                                                            AND MONTH(order_date) = :month 
                                                            AND YEAR(order_date) = :year');
                                                        // Vincular parámetros
                                                        $db->bind(':month', $month);
                                                        $db->bind(':year', $year);
                                                        // Ejecutar la consulta
                                                        $db->cdp_execute();
                                                        // Obtener el registro
                                                        $count = $db->cdp_registro();
                                                        $total_orders = $count->total;
                                                        echo cdb_money_format($total_orders);
                                                        ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-5 col-md-5 mb-0">
                                            <div class="user-progress align-items-center gap-3">
                                                <div class="align-items-center gap-1">
                                                    <div class="progress m-t-10">
                                                        <?php
                                                        // Calcular el progreso actual del mes
                                                        $currentDay = date('j');
                                                        $totalDays = date('t');
                                                        $progressPercentage = ($total_orders / $totalDays) * 100; // Utiliza el total de órdenes en lugar del día actual para calcular el progreso
                                                        ?>
                                                        <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $progressPercentage; ?>%" aria-valuenow="<?php echo $total_orders; ?>" aria-valuemin="0" aria-valuemax="<?php echo $totalDays; ?>"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li class="mb-0">
                                    <div class="row">
                                        <div class="col-xl-7 col-md-7 mb-2">
                                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                    <h6 class="mb-0"><?php echo $lang['dash-general-10'] ?></h6>
                                                    <small class="text-muted">
                                                        <?php echo $core->currency; ?>
                                                        <?php
                                                        // Ejecutar la consulta SQL para obtener el total de órdenes de compra
                                                        $db->cdp_query('SELECT IFNULL(SUM(total_order),0) as total FROM cdb_add_order where status_courier != 21 and is_pickup = 0
                                                            AND MONTH(order_date) = :month 
                                                            AND YEAR(order_date) = :year');
                                                        // Vincular parámetros
                                                        $db->bind(':month', $month);
                                                        $db->bind(':year', $year);
                                                        // Ejecutar la consulta
                                                        $db->cdp_execute();
                                                        // Obtener el registro
                                                        $count = $db->cdp_registro();
                                                        $total_orders2 = $count->total;
                                                        echo cdb_money_format($total_orders2);
                                                        ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-5 col-md-5 mb-0">
                                            <div class="user-progress align-items-center gap-3">
                                                <div class="align-items-center gap-1">
                                                    <div class="progress m-t-10">
                                                       <?php
                                                        // Calcular el progreso actual del mes
                                                        $currentDay = date('j');
                                                        $totalDays = date('t');
                                                        $progressPercentage = ($total_orders2 / $totalDays) * 100; // Utiliza el total de órdenes en lugar del día actual para calcular el progreso
                                                        ?>
                                                        <div class="progress-bar bg-label-blue" role="progressbar" style="width: <?php echo $progressPercentage; ?>%" aria-valuenow="<?php echo $total_orders2; ?>" aria-valuemin="0" aria-valuemax="<?php echo $totalDays; ?>"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li class="mb-0">
                                    <div class="row">
                                        <div class="col-xl-7 col-md-7 mb-2">
                                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                    <h6 class="mb-0"><?php echo $lang['messagesform94'] ?></h6>
                                                    <small class="text-muted">
                                                        <?php echo $core->currency; ?>
                                                        <?php
                                                            // Ejecutar la consulta SQL para obtener el total de órdenes de compra
                                                        $db->cdp_query('SELECT IFNULL(SUM(total_order),0) as total FROM cdb_consolidate where status_courier != 21
                                                            AND MONTH(c_date) = :month 
                                                            AND YEAR(c_date) = :year');
                                                        // Vincular parámetros
                                                        $db->bind(':month', $month);
                                                        $db->bind(':year', $year);
                                                        // Ejecutar la consulta
                                                        $db->cdp_execute();
                                                        // Obtener el registro
                                                        $count = $db->cdp_registro();
                                                        $total_orders3 = $count->total;
                                                        echo cdb_money_format($total_orders3);
                                                        ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-5 col-md-5 mb-6">
                                            <div class="user-progress align-items-center gap-3">
                                                <div class="align-items-center gap-1">
                                                    <div class="progress m-t-10">
                                                        <?php
                                                        // Calcular el progreso actual del mes
                                                        $currentDay = date('j');
                                                        $totalDays = date('t');
                                                        $progressPercentage = ($total_orders3 / $totalDays) * 100; // Utiliza el total de órdenes en lugar del día actual para calcular el progreso
                                                        ?>
                                                        <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $progressPercentage; ?>%" aria-valuenow="<?php echo $total_orders3; ?>" aria-valuemin="0" aria-valuemax="<?php echo $totalDays; ?>"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>


                                <li class="mb-0">
                                    <div class="row">
                                        <div class="col-xl-7 col-md-7 mb-2">
                                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                    <h6 class="mb-0"><?php echo $lang['messagesform93'] ?></h6>
                                                    <small class="text-muted">
                                                        <?php echo $core->currency; ?>
                                                        <?php
                                                        // Ejecutar la consulta SQL para obtener el total de órdenes de compra
                                                        $db->cdp_query('SELECT IFNULL(SUM(total_order),0) as total FROM cdb_consolidate_packages where status_courier != 21
                                                            AND MONTH(c_date) = :month 
                                                            AND YEAR(c_date) = :year');
                                                        // Vincular parámetros
                                                        $db->bind(':month', $month);
                                                        $db->bind(':year', $year);
                                                        // Ejecutar la consulta
                                                        $db->cdp_execute();
                                                        // Obtener el registro
                                                        $count = $db->cdp_registro();
                                                        $total_orders4 = $count->total;
                                                        echo cdb_money_format($total_orders4);
                                                        ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-5 col-md-5 mb-6">
                                            <div class="user-progress align-items-center gap-3">
                                                <div class="align-items-center gap-1">
                                                    <div class="progress m-t-10">
                                                        <?php
                                                        // Calcular el progreso actual del mes
                                                        $currentDay = date('j');
                                                        $totalDays = date('t');
                                                        $progressPercentage = ($total_orders4 / $totalDays) * 100; // Utiliza el total de órdenes en lugar del día actual para calcular el progreso
                                                        ?>
                                                        <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $progressPercentage; ?>%" aria-valuenow="<?php echo $total_orders4; ?>" aria-valuemin="0" aria-valuemax="<?php echo $totalDays; ?>"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                      </div>
                    </div>
                    <!--/ Earning Reports -->

                     <div class="col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 mb-4">

                                        <div class="card-header-title d-flex justify-content-between">
                                            <div class="card-title mb-6">
                                                <h5 class="m-0 me-2"><?php echo $lang['messagesform91'] ?></h5>
                                                <small class="text-muted"><?php echo $lang['messagesform92'] ?></small>
                                            </div>
                                        </div>
                                        <div><br></div>
                                        <div class="pb-0">
                                            <div class="row">
                                                <!-- Primer grupo de 3 elementos -->
                                                <div class="col-sm-6 col-md-6 col-lg-6">
                                                    <!-- Primer elemento contador de envios -->
                                                    <div class="col-lg-12 col-md-12 mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <div class="m-r-10">
                                                                <a href="dashboard_admin_shipments.php">
                                                                    <span class="text-orange display-7">
                                                                        <i class="mdi mdi-package-variant-closed"></i>
                                                                    </span>
                                                                </a>
                                                            </div>

                                                            <div class="card-info-statics">
                                                              <h5 class="mb-0">
                                                                <?php
                                                                    $db->cdp_query('SELECT COUNT(*) as total FROM cdb_add_order WHERE  order_incomplete=1');
                                                                    $db->cdp_execute();
                                                                    $count = $db->cdp_registro();
                                                                    echo $count->total;
                                                                    ?>            
                                                              </h5>
                                                              <small><?php echo $lang['dash-general-1'] ?></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Segundo elemento contador de recogida envio -->
                                                    <div class="col-lg-12 col-md-12 mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <div class="m-r-10"><a href="pickup_list.php"><span class="text-cyan display-7"><i class="mdi mdi-star-circlemdi mdi-clock-fast"></i></span> </a>
                                                            </div>

                                                            <div class="card-info-statics">
                                                              <h5 class="mb-0">
                                                                <?php
                                                                    $db->cdp_query('SELECT COUNT(*) as total FROM cdb_add_order WHERE order_incomplete != 0 and is_pickup=1');
                                                                    $db->cdp_execute();
                                                                    $count = $db->cdp_registro();
                                                                    echo $count->total;
                                                                ?>            
                                                              </h5>
                                                              <small><?php echo $lang['dash-general-2'] ?></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Tercer elemento contador de consolidados de envios-->
                                                    <div class="col-lg-12 col-md-12 mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <div class="m-r-10"><a href="consolidate_list.php"><span class="text-danger display-7"><i class="mdi mdi-gift"></i></span></a>
                                                            </div>

                                                            <div class="card-info-statics">
                                                              <h5 class="mb-0">
                                                                <?php
                                                                    $db->cdp_query('SELECT COUNT(*) as total FROM cdb_consolidate');
                                                                    $db->cdp_execute();
                                                                    $count = $db->cdp_registro();
                                                                    echo $count->total;
                                                                ?>           
                                                              </h5>
                                                              <small><?php echo $lang['dash-general-3'] ?></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                          
                                                <!-- Segundo grupo de 3 elementos -->
                                                <div class="col-sm-6 col-md-6 col-lg-6">
                                                    <!-- Cuarto elemento contador de cuentas por cobrar -->
                                                    <div class="col-lg-12 col-md-12 mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <div class="m-r-10"><a href="accounts_receivable.php"><span class="text-primary display-7"><i class="mdi mdi-package-down"></i></span></a>
                                                            </div>

                                                            <div class="card-info-statics">
                                                              <h5 class="mb-0">
                                                                <?php
                                                                    $db->cdp_query('SELECT COUNT(*) as total FROM cdb_add_order WHERE order_payment_method >1');
                                                                    $db->cdp_execute();
                                                                    $count = $db->cdp_registro();
                                                                    echo $count->total;
                                                                ?>         
                                                              </h5>
                                                              <small><?php echo $lang['dash-general-4'] ?></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Quinto elemento contador de pre alertas -->
                                                    <div class="col-lg-12 col-md-12 mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <div class="m-r-10"><a href="prealert_list.php"><span class="text-warning display-7"><i class="mdi mdi-clock-alert"></i></span></a>
                                                            </div>

                                                            <div class="card-info-statics">
                                                              <h5 class="mb-0">
                                                                 <?php
                                                                    $db->cdp_query('SELECT COUNT(*) as total FROM cdb_pre_alert where is_package=0');
                                                                    $db->cdp_execute();
                                                                    $count = $db->cdp_registro();
                                                                    echo $count->total;
                                                                ?>       
                                                              </h5>
                                                              <small><?php echo $lang['dash-general-5'] ?></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Sexto elemento de contador de paquetes -->
                                                    <div class="col-lg-12 col-md-12 mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <div class="m-r-10"><a href="customer_packages_list.php"><span class="text-success display-7"><i class="fas fa-cube"></i></span></a>
                                                            </div>

                                                            <div class="card-info-statics">
                                                              <h5 class="mb-0">
                                                                 <?php
                                                                    $db->cdp_query('SELECT COUNT(*) as total FROM cdb_customers_packages');
                                                                    $db->cdp_execute();
                                                                    $count = $db->cdp_registro();
                                                                    echo $count->total;
                                                                ?> 
                                                              </h5>
                                                              <small><?php echo $lang['dash-general-661'] ?></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4 mb-4">
                                        <div class="card-header-title d-flex justify-content-between">
                                            <div class="card-title mb-6">
                                                <h5 class="m-0 me-2"><?php echo $lang['messagesform97'] ?></h5>
                                                <small class="text-muted"><?php echo $lang['messagesform98'] ?></small>
                                            </div>
                                        </div>
                                        <div><br></div>
                                        <div class="pb-0">
                                            <ul class="p-0 m-0">
                                                <li class="d-flex mb-2">
                                                        <div class="avatar flex-shrink-0 me-3">
                                                            <span class="avatar-initial rounded bg-label-secondary">     <i class="ti ti-user-star ti-sm"></i>
                                                            </span>
                                                        </div>
                                                        <div class="card-user d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                        <div class="me-2">
                                                            <h6 class="mb-0"><?php echo $lang['dash-general-14'] ?></h6>
                                                        </div>
                                                        <div class="user-progress d-flex align-items-center gap-3">
                                                          
                                                          <div class="d-flex align-items-center gap-1">
                                                            <small class="text-muted">
                                                                <?php
                                                                $db->cdp_query('SELECT COUNT(*) as total FROM cdb_users WHERE userlevel=9');
                                                                $db->cdp_execute();
                                                                $count = $db->cdp_registro();
                                                                echo $count->total;
                                                                ?>  
                                                            </small>
                                                          </div>
                                                        </div>
                                                    </div>
                                                </li>

                                                <li class="d-flex mb-2">
                                                        <div class="avatar flex-shrink-0 me-3">
                                                            <span class="avatar-initial rounded bg-label-secondary">     <i class="ti ti-users-group ti-sm"></i>
                                                            </span>
                                                        </div>
                                                        <div class="card-user d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                        <div class="me-2">
                                                            <h6 class="mb-0"><?php echo $lang['dash-general-15'] ?></h6>
                                                        </div>
                                                        <div class="user-progress d-flex align-items-center gap-3">
                                                          
                                                          <div class="d-flex align-items-center gap-1">
                                                            <small class="text-muted">
                                                                <?php
                                                                $db->cdp_query('SELECT COUNT(*) as total FROM cdb_users WHERE userlevel=2');
                                                                $db->cdp_execute();
                                                                $count = $db->cdp_registro();
                                                                echo $count->total;
                                                                ?>  
                                                            </small>
                                                          </div>
                                                        </div>
                                                    </div>
                                                </li>

                                                <li class="d-flex mb-2">
                                                        <div class="avatar flex-shrink-0 me-3">
                                                            <span class="avatar-initial rounded bg-label-secondary">     <i class="ti ti-user-pin ti-sm"></i>
                                                            </span>
                                                        </div>
                                                        <div class="card-user d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                        <div class="me-2">
                                                            <h6 class="mb-0"><?php echo $lang['dash-general-16'] ?></h6>
                                                        </div>
                                                        <div class="user-progress d-flex align-items-center gap-3">
                                                          
                                                          <div class="d-flex align-items-center gap-1">
                                                            <small class="text-muted">
                                                                <?php
                                                                $db->cdp_query('SELECT COUNT(*) as total FROM cdb_users WHERE userlevel=3');
                                                                $db->cdp_execute();
                                                                $count = $db->cdp_registro();
                                                                echo $count->total;
                                                                ?>  
                                                            </small>
                                                          </div>
                                                        </div>
                                                    </div>
                                                </li>

                                                <li class="d-flex mb-2">
                                                        <div class="avatar flex-shrink-0 me-3">
                                                            <span class="avatar-initial rounded bg-label-secondary">     <i class="ti ti-user-plus ti-sm"></i>
                                                            </span>
                                                        </div>
                                                        <div class="card-user d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                        <div class="me-2">
                                                            <h6 class="mb-0"><?php echo $lang['dash-general-17'] ?></h6>
                                                        </div>
                                                        <div class="user-progress d-flex align-items-center gap-3">
                                                          
                                                          <div class="d-flex align-items-center gap-1">
                                                            <small class="text-muted">
                                                                <?php
                                                                $db->cdp_query('SELECT COUNT(*) as total FROM cdb_users WHERE userlevel=1');
                                                                $db->cdp_execute();
                                                                $count = $db->cdp_registro();
                                                                echo $count->total;
                                                                ?>  
                                                            </small>
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
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- Bar Chart + Image Card Row -->
                <!-- ============================================================== -->
                <div class="row mb-4">
                    <!-- Bar Chart: Last 6 months -->
                    <div class="col-12 col-lg-8 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h5 class="card-title mb-0">Monthly Overview</h5>
                                        <small class="text-muted">Shipments, Pickups &amp; Consolidations — last 6 months</small>
                                    </div>
                                </div>
                                <canvas id="dashboardBarChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Image Card: Top Customers -->
                    <div class="col-12 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <img src="assets/images/alert/truck_dashboard.png" style="height:130px; opacity:0.85;" />
                                    <div class="text-right">
                                        <h5 class="card-title mb-0">Top Customers</h5>
                                        <small class="text-muted">By shipment volume</small>
                                    </div>
                                </div>
                                <?php
                                $db->cdp_query("SELECT CONCAT(u.fname, ' ', u.lname) as customer, COUNT(o.id) as total
                                    FROM cdb_add_order o
                                    LEFT JOIN cdb_users u ON o.id_customer = u.id
                                    WHERE o.order_incomplete = 1
                                    GROUP BY o.id_customer
                                    ORDER BY total DESC LIMIT 5");
                                $db->cdp_execute();
                                $topCustomers = $db->cdp_registros();
                                ?>
                                <ul class="list-unstyled mb-0 flex-grow-1">
                                <?php if ($topCustomers): foreach ($topCustomers as $tc): ?>
                                    <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <span class="avatar-initial rounded-circle bg-label-primary mr-2" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;">
                                                <?php echo strtoupper(substr($tc->customer, 0, 1)); ?>
                                            </span>
                                            <span class="ml-2"><?php echo htmlspecialchars($tc->customer); ?></span>
                                        </div>
                                        <span class="badge badge-pill badge-primary"><?php echo $tc->total; ?></span>
                                    </li>
                                <?php endforeach; else: ?>
                                    <li class="text-center text-muted py-3">No data yet</li>
                                <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- Recent Shipments Table -->
                <!-- ============================================================== -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title mb-0">Recent Shipments</h5>
                                    <a href="courier_list.php" class="btn btn-sm btn-primary">View all</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Tracking #</th>
                                                <th>Customer</th>
                                                <th>Origin</th>
                                                <th>Destination</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $db->cdp_query("SELECT o.order_no, o.order_date, o.order_origin, o.order_destination, s.name_status, s.color_status,
                                            CONCAT(u.fname, ' ', u.lname) as customer
                                            FROM cdb_add_order o
                                            LEFT JOIN cdb_users u ON o.id_customer = u.id
                                            LEFT JOIN cdb_styles s ON o.status_courier = s.id
                                            WHERE o.order_incomplete = 1
                                            ORDER BY o.id DESC LIMIT 5");
                                        $db->cdp_execute();
                                        $recent = $db->cdp_registros();
                                        if ($recent):
                                            foreach ($recent as $r): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($r->order_no); ?></strong></td>
                                                <td><?php echo htmlspecialchars($r->customer); ?></td>
                                                <td><?php echo htmlspecialchars($r->order_origin); ?></td>
                                                <td><?php echo htmlspecialchars($r->order_destination); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($r->order_date)); ?></td>
                                                <td><span class="badge" style="background:<?php echo $r->color_status; ?>; color:#fff;"><?php echo htmlspecialchars($r->name_status); ?></span></td>
                                            </tr>
                                        <?php endforeach; else: ?>
                                            <tr><td colspan="6" class="text-center text-muted">No shipments found</td></tr>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                // Bar chart - last 6 months
                (function() {
                    var labels = <?php
                        $labels = []; $shipData = []; $pickData = []; $conData = [];
                        for ($i = 5; $i >= 0; $i--) {
                            $m = date('m', strtotime("-$i months"));
                            $y = date('Y', strtotime("-$i months"));
                            $labels[] = date('M Y', strtotime("-$i months"));

                            $db->cdp_query('SELECT COUNT(*) as total FROM cdb_add_order WHERE order_incomplete=1 AND is_pickup=0 AND MONTH(order_date)=:m AND YEAR(order_date)=:y');
                            $db->bind(':m',$m); $db->bind(':y',$y); $db->cdp_execute();
                            $shipData[] = (int)$db->cdp_registro()->total;

                            $db->cdp_query('SELECT COUNT(*) as total FROM cdb_add_order WHERE order_incomplete!=0 AND is_pickup=1 AND MONTH(order_date)=:m AND YEAR(order_date)=:y');
                            $db->bind(':m',$m); $db->bind(':y',$y); $db->cdp_execute();
                            $pickData[] = (int)$db->cdp_registro()->total;

                            $db->cdp_query('SELECT COUNT(*) as total FROM cdb_consolidate WHERE MONTH(c_date)=:m AND YEAR(c_date)=:y');
                            $db->bind(':m',$m); $db->bind(':y',$y); $db->cdp_execute();
                            $conData[] = (int)$db->cdp_registro()->total;
                        }
                        echo json_encode($labels);
                    ?>;
                    var shipData = <?php echo json_encode($shipData); ?>;
                    var pickData = <?php echo json_encode($pickData); ?>;
                    var conData  = <?php echo json_encode($conData); ?>;

                    var ctx = document.getElementById('dashboardBarChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                { label: 'Shipments', data: shipData, backgroundColor: 'rgba(70,127,255,0.8)', borderRadius: 4 },
                                { label: 'Pickups',   data: pickData, backgroundColor: 'rgba(0,188,212,0.8)',  borderRadius: 4 },
                                { label: 'Consolidated', data: conData, backgroundColor: 'rgba(76,175,80,0.8)', borderRadius: 4 }
                            ]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { position: 'top' } },
                            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                        }
                    });
                })();
                </script>

                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12">
                        <div class="card">
                            <div class="card-body">

                                <!-- title -->
                                <ul class="nav nav-pills custom-pills m-t-20" id="pills-tab2" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="pills-home-tab2" data-toggle="pill" href="#test11" role="tab" aria-selected="true"><h5 class="card-title mb-0"><?php echo $lang['dash-general-19'] ?></h5></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-profile-tab2" href="pickup_list.php" role="tab" aria-selected="false"><h5 class="card-title mb-0"><?php echo $lang['dash-general-20'] ?></h5></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-profile-tab2" href="consolidate_list.php" role="tab" aria-selected="false"><h5 class="card-title mb-0"><?php echo $lang['dash-general-21'] ?></h5></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-profile-tab" href="prealert_list.php">
                                            <h5 class="card-title mb-0"><?php echo $lang['dash-general-22'] ?></h5>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="pills-profile-tab" href="customer_packages_list.php">
                                            <h5 class="card-title mb-0"><?php echo $lang['dash-general-23'] ?></h5>
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content  m-t-30" id="pills-tabContent">
                                    <div class="tab-pane fade show active" id="pills-shipment" role="tabpanel" aria-labelledby="pills-home-tab">

                                        <div class="col-md-12 mt-12 mb-12">
                                            <div class="input-group">
                                                <input type="text" name="search_shipment" id="search_shipment" class="form-control input-sm float-right" placeholder="<?php echo $lang['left21551'] ?>" onkeyup="cdp_load(1);">
                                                <div class="input-group-append input-sm">
                                                    <button type="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div><br></div>

                                        <div class="results_shipments"></div>
                                    </div>
                                    <div class="tab-pane fade" id="pills-pickup" role="tabpanel" aria-labelledby="pills-profile-tab">

                                        <div class="col-md-4 mt-4 mb-4">
                                            <div class="input-group">
                                                <input type="text" name="search_pickup" id="search_pickup" class="form-control input-sm float-right" placeholder="<?php echo $lang['left21551'] ?>" onkeyup="cdp_load(1);">
                                                <div class="input-group-append input-sm">
                                                    <button type="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="results_pickup"></div>

                                    </div>
                                    <div class="tab-pane fade" id="pills-consolidated" role="tabpanel" aria-labelledby="pills-contact-tab">
                                        <div class="col-md-4 mt-4 mb-4">
                                            <div class="input-group">
                                                <input type="text" name="search_consolidated" id="search_consolidated" class="form-control input-sm float-right" placeholder="<?php echo $lang['left21551'] ?>" onkeyup="cdp_load(1);">
                                                <div class="input-group-append input-sm">
                                                    <button type="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="results_consolidated"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'views/inc/footer.php'; ?>
        </div>
    </div>




    <script src="dataJs/dashboard_index.js"></script>