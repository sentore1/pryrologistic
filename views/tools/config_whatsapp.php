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

if (!$user->cdp_is_Admin())
	cdp_redirect_to("login.php");

$userData = $user->cdp_getUserData();
$currentProvider = $core->whatsapp_provider ?: 'ultramsg';
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
	<link href="assets/template/dist/css/custom_swicth.css" rel="stylesheet">
	<style>
		.highlight { border: 1px solid #ff0000; }
		.provider-card {
			border: 2px solid #e9ecef;
			border-radius: 8px;
			padding: 12px 18px;
			cursor: pointer;
			transition: border-color 0.2s, background 0.2s;
			display: flex;
			align-items: center;
			gap: 10px;
		}
		.provider-card:hover { border-color: #336aea; }
		.provider-card.active { border-color: #336aea; background: #f0f4ff; }
		.provider-card input[type="radio"] { accent-color: #336aea; width: 16px; height: 16px; }
	</style>
</head>
<body>
	<?php include 'views/inc/preloader.php'; ?>
	<div id="main-wrapper">
		<?php include 'views/inc/topbar.php'; ?>
		<?php include 'views/inc/left_sidebar.php'; ?>
		<div class="page-wrapper">
			<div class="email-app mt-3">
				<?php include 'views/inc/left_part_menu.php'; ?>
				<div class="right-part mail-list bg-white">
					<div class="bg-light">
						<div class="row justify-content-center">
							<div class="col-md-12">
								<div class="row">
									<div class="col-12">
										<div id="loader" style="display:none"></div>
										<div id="resultados_ajax"></div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row justify-content-center">
						<div class="col-md-12">
							<div class="row">
								<div class="col-12">
									<div class="card-body">

										<div class="d-md-flex align-items-center">
											<div>
												<h3 class="card-title"><span><?php echo $lang['ws-add-text22'] ?></span></h3>
											</div>
										</div>
										<div><hr><br></div>

										<form class="form-horizontal form-material" id="save_data" name="save_data" method="post">

											<!-- Provider selector -->
											<div class="row mb-4">
												<div class="col-md-12">
													<label class="mb-2"><strong>Select Provider</strong></label>
													<div class="d-flex flex-wrap" style="gap:12px;">

														<label class="provider-card <?php echo $currentProvider === 'ultramsg' ? 'active' : ''; ?>" id="card_ultramsg">
															<input type="radio" name="whatsapp_provider" value="ultramsg" <?php echo $currentProvider === 'ultramsg' ? 'checked' : ''; ?>>
															<span>UltraMsg</span>
														</label>

														<label class="provider-card <?php echo $currentProvider === 'twilio' ? 'active' : ''; ?>" id="card_twilio">
															<input type="radio" name="whatsapp_provider" value="twilio" <?php echo $currentProvider === 'twilio' ? 'checked' : ''; ?>>
															<span>Twilio</span>
														</label>

														<label class="provider-card <?php echo $currentProvider === 'meta' ? 'active' : ''; ?>" id="card_meta">
															<input type="radio" name="whatsapp_provider" value="meta" <?php echo $currentProvider === 'meta' ? 'checked' : ''; ?>>
															<span>Meta Cloud API</span>
														</label>

													</div>
												</div>
											</div>

											<!-- UltraMsg fields -->
											<section id="section_ultramsg" style="<?php echo $currentProvider !== 'ultramsg' ? 'display:none;' : ''; ?>">
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<label><?php echo $lang['ws-add-text23'] ?></label>
															<input type="text" class="form-control ultramsg-field" name="api_ws_url" id="api_ws_url"
																placeholder="https://api.ultramsg.com/instance00000/"
																value="<?php echo htmlspecialchars($core->api_ws_url); ?>">
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<label><?php echo $lang['ws-add-text24'] ?></label>
															<input type="text" class="form-control ultramsg-field" name="api_ws_token" id="api_ws_token"
																placeholder="API TOKEN"
																value="<?php echo htmlspecialchars($core->api_ws_token); ?>">
														</div>
													</div>
												</div>
											</section>

											<!-- Twilio WhatsApp fields -->
											<section id="section_twilio" style="<?php echo $currentProvider !== 'twilio' ? 'display:none;' : ''; ?>">
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<label>Account SID</label>
															<input type="text" class="form-control twilio-field" name="twilio_wa_sid" id="twilio_wa_sid"
																placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
																value="<?php echo htmlspecialchars($core->twilio_wa_sid); ?>">
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<label>Auth Token</label>
															<input type="text" class="form-control twilio-field" name="twilio_wa_token" id="twilio_wa_token"
																placeholder="Twilio Auth Token"
																value="<?php echo htmlspecialchars($core->twilio_wa_token); ?>">
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<label>WhatsApp Number <small class="text-muted">(e.g. whatsapp:+14155238886)</small></label>
															<input type="text" class="form-control twilio-field" name="twilio_wa_number" id="twilio_wa_number"
																placeholder="whatsapp:+14155238886"
																value="<?php echo htmlspecialchars($core->twilio_wa_number); ?>">
														</div>
													</div>
												</div>
											</section>

											<!-- Meta Cloud API fields -->
											<section id="section_meta" style="<?php echo $currentProvider !== 'meta' ? 'display:none;' : ''; ?>">
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<label>Access Token</label>
															<input type="text" class="form-control meta-field" name="meta_wa_token" id="meta_wa_token"
																placeholder="Meta Permanent Access Token"
																value="<?php echo htmlspecialchars($core->meta_wa_token); ?>">
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<label>Phone Number ID</label>
															<input type="text" class="form-control meta-field" name="meta_wa_phone_id" id="meta_wa_phone_id"
																placeholder="Meta Phone Number ID"
																value="<?php echo htmlspecialchars($core->meta_wa_phone_id); ?>">
														</div>
													</div>
												</div>
											</section>

											<!-- Enable API toggle (shared) -->
											<div class="row mt-3 mb-3">
												<div class="col-md-12">
													<div class="form-group">
														<label class="custom-control custom-checkbox">
															<?php echo $lang['ws-add-text25'] ?>
															<input type="checkbox" class="custom-control-input" name="active_whatsapp" id="active_whatsapp" value="1"
																<?php if ($core->active_whatsapp == 1) echo 'checked'; ?>>
															<span class="custom-control-indicator"></span>
														</label>
													</div>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12">
													<button class="btn btn-primary btn-confirmation" name="dosubmit" type="submit">
														<?php echo $lang['ws-add-text21'] ?> <span><i class="icon-ok"></i></span>
													</button>
												</div>
											</div>

										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php include 'views/inc/footer.php'; ?>
			</div>
		</div>
	</div>

	<?php include('helpers/languages/translate_to_js.php'); ?>
	<script src="dataJs/whatssap_config.js"></script>
</body>
</html>
