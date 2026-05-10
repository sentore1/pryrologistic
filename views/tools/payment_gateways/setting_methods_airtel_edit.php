<?php
if (!$user->cdp_is_Admin())
    cdp_redirect_to("login.php");
$userData = $user->cdp_getUserData();

require_once('helpers/querys.php');

if (isset($_GET['id'])) {
    $data = cdp_getPaymentMethodAPIEdit($_GET['id']);
}

if (!isset($_GET['id']) or $data['rowCount'] != 1) {
    cdp_redirect_to("setting_payment_list.php");
}

$row_off = $data['data'];
?>
<!DOCTYPE html>
<html dir="<?php echo $direction_layout; ?>" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/<?php echo $core->favicon ?>">
    <title><?php echo $lang['tools-config61'] ?> | <?php echo $core->site_name ?></title>
    <?php include 'views/inc/head_scripts.php'; ?>
    <link href="assets/template/assets/libs/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css" rel="stylesheet">
    <link href="assets/template/dist/css/custom_swicth.css" rel="stylesheet">
</head>
<body>
    <?php include 'views/inc/preloader.php'; ?>
    <div id="main-wrapper">
        <?php include 'views/inc/topbar.php'; ?>
        <?php include 'views/inc/left_sidebar.php'; ?>
        <div class="page-wrapper">
            <div class="p-15">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-12">
                                <div id="resultados_ajax"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 col-xl-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-md-flex align-items-center">
                                    <div>
                                        <h3 class="card-title"><span><?php echo $lang['tools-methodpay2'] ?> <i class="icon-double-angle-right"></i> <?php echo $row_off->name_pay; ?></span></h3>
                                    </div>
                                </div>
                                <div><hr><br></div>
                                <div id="msgholder"></div>
                                <form class="form-horizontal form-material" id="update_data_airtel" name="update_data_airtel" method="post">
                                    <section>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><?php echo $lang['tools-config117'] ?></label>
                                                    <input type="text" class="form-control required" id="name_pay" name="name_pay" value="<?php echo $row_off->name_pay; ?>" placeholder="<?php echo $lang['tools-config117'] ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><?php echo $lang['tools-config118'] ?></label>
                                                    <input type="text" class="form-control required" id="detail_pay" name="detail_pay" value="<?php echo $row_off->detail_pay; ?>" placeholder="<?php echo $lang['tools-config118'] ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><?php echo $lang['tools-config120'] ?></label>
                                                    <label class="custom-control custom-checkbox" style="font-size: 18px;">
                                                        <input type="checkbox" class="custom-control-input" name="is_active" id="is_active" value="1"
                                                            <?php if ($row_off->is_active == 1) echo 'checked'; ?>>
                                                        <span class="custom-control-indicator"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                    <br><br>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button class="btn btn-outline-primary btn-confirmation" name="dosubmit" type="submit"><?php echo $lang['tools-methodpay4'] ?> <span><i class="icon-ok"></i></span></button>
                                            <a href="payment_mode_list.php" class="btn btn-outline-secondary btn-confirmation"><span><i class="ti-share-alt"></i></span> <?php echo $lang['tools-methodpay5'] ?></a>
                                        </div>
                                    </div>
                                    <input name="id" type="hidden" value="<?php echo $_GET['id']; ?>" />
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'views/inc/footer.php'; ?>
        </div>
    </div>
    <?php include('helpers/languages/translate_to_js.php'); ?>
    <script src="assets/template/dist/js/app-style-switcher.js"></script>
    <script src="assets/template/assets/libs/bootstrap-switch/dist/js/bootstrap-switch.min.js"></script>
    <script src="dataJs/setting_payment_list.js"></script>
</body>
</html>
