	<header class="topbar">
		<nav class="navbar top-navbar navbar-expand-md navbar-dark">
			<div class="navbar-header">
				<!-- This is for the sidebar toggle which is visible on mobile only -->
				<a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
				<!-- ============================================================== -->
				<!-- Logo -->
				<!-- ============================================================== -->
				<a class="navbar-brand" href="index.php">
					<!-- Logo text -->
					<span class="logo-text">
						<!-- dark Logo text -->
						<?php echo ($core->logo) ? '<img src="assets/' . $core->logo . '" alt="' . $core->site_name . '" width="' . $core->thumb_w . '" height="' . $core->thumb_h . '"/>' : $core->site_name; ?>
					</span>
				</a> 

				<!-- ============================================================== -->
				<!-- End Logo -->
				<!-- ============================================================== -->
				<!-- ============================================================== -->
				<!-- Toggle which is visible on mobile only -->
				<!-- ============================================================== -->
				<a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i class="ti-more"></i></a>
			</div>
			<!-- ============================================================== -->
			<!-- End Logo -->
			<!-- ============================================================== -->
			<div class="navbar-collapse collapse" id="navbarSupportedContent">
				<!-- ============================================================== -->
				<!-- toggle and nav items -->
				<!-- ============================================================== -->
				<ul class="navbar-nav float-left mr-auto">
					<li class="nav-item d-none d-md-block"><a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)" data-sidebartype="mini-sidebar"><i class="mdi mdi-menu font-24"></i></a></li>

				</ul>
				<!-- ============================================================== -->
				<!-- Right side toggle and nav items -->
				<!-- ============================================================== -->
				<ul class="navbar-nav float-right">
					<!-- ============================================================== -->
					<!-- create new -->
					<!-- ============================================================== -->
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown2" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-weight:600; font-size:13px; letter-spacing:0.5px;">
							<i class="mdi mdi-translate mr-1" style="font-size:18px;"></i>
							<?php
							$langLabels = ['en'=>'EN','es'=>'ES','fr'=>'FR','ar'=>'AR','he'=>'HE'];
							echo isset($langLabels[$core->language]) ? $langLabels[$core->language] : strtoupper($core->language);
							?>
						</a>
						<div class="dropdown-menu dropdown-menu-right animated fadeIn" style="min-width:120px;">
							<a class="dropdown-item lang-switch <?php echo $core->language=='en'?'active':''; ?>" href="#" data-lang="en">English</a>
							<a class="dropdown-item lang-switch <?php echo $core->language=='es'?'active':''; ?>" href="#" data-lang="es">Español</a>
							<a class="dropdown-item lang-switch <?php echo $core->language=='fr'?'active':''; ?>" href="#" data-lang="fr">Français</a>
							<a class="dropdown-item lang-switch <?php echo $core->language=='ar'?'active':''; ?>" href="#" data-lang="ar">العربية</a>
							<a class="dropdown-item lang-switch <?php echo $core->language=='he'?'active':''; ?>" href="#" data-lang="he">עברית</a>
						</div>
					</li>
					<!-- ============================================================== -->
					<!-- Comment -->
					<!-- ============================================================== -->

					<li class="nav-item dropdown" id="notif-dropdown">
						<a id="clickme" class="nav-link dropdown-toggle waves-effect waves-dark" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="mdi mdi-bell-outline" style="font-size:22px;"></i>
							<span class="badge badge-notify badge-sm up badge-light pull-top-xs" id="countNotifications">0</span>
						</a>

						<div class="dropdown-menu dropdown-menu-right mailbox">
							<div id="ajax_response"></div>
						</div>

					</li>

					<script>
					document.addEventListener('DOMContentLoaded', function () {
						var notifLi = document.getElementById('notif-dropdown');
						var menu = notifLi.querySelector('.dropdown-menu');
						notifLi.addEventListener('mouseenter', function () {
							menu.classList.add('show');
							notifLi.classList.add('show');
						});
						notifLi.addEventListener('mouseleave', function () {
							menu.classList.remove('show');
							notifLi.classList.remove('show');
						});
					});
					</script>


					<!-- ============================================================== -->
					<!-- User profile and search -->
					<!-- ============================================================== -->
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="assets/<?php echo ($userData->avatar) ? $userData->avatar : "uploads/blank.png"; ?>" class="rounded-circle" width="34" />&nbsp; <i class="fa fa-caret-down"></i></a>
						<div class="dropdown-menu dropdown-menu-right user-dd animated fadeIn">
							<span class="with-arrow"><span class="bg-primary"></span></span>
							<div class="d-flex no-block align-items-center p-15 bg-primary text-white m-b-10">
								<div class="">
									<img src="assets/<?php echo ($userData->avatar) ? $userData->avatar : "uploads/blank.png"; ?>" class="rounded-circle" width="80" />
								</div>
								<div class="m-l-10">
									<h4 class="m-b-0"><?php echo $userData->username; ?></h4>
									<p class=" m-b-0"><?php echo $userData->email; ?></p>
								</div>
							</div>

							<?php
							if ($userData->userlevel == 9 || $userData->userlevel == 2) {
							?>
								<a class="dropdown-item" href="users_edit.php?user=<?php echo $userData->id; ?>">
									<i class="ti-user m-r-5 m-l-5"></i> <?php echo $lang['miprofile'] ?></a>
							<?php
							} else	if ($userData->userlevel == 1) {

							?>
								<a class="dropdown-item" href="customers_profile_edit.php?user=<?php echo $userData->id; ?>">
									<i class="ti-user m-r-5 m-l-5"></i> <?php echo $lang['miprofile'] ?></a>
							<?php

							} else	if ($userData->userlevel == 3) {

							?>
								<a class="dropdown-item" href="drivers_edit.php?user=<?php echo $userData->id; ?>">
									<i class="ti-user m-r-5 m-l-5"></i> <?php echo $lang['miprofile'] ?></a>
							<?php
							}
							?>


							<div class="dropdown-divider"></div>
							<?php
							if ($userData->userlevel == 9) {
							?>
								<a class="dropdown-item" href="users_list.php">
									<i class="ti-settings m-r-5 m-l-5"></i> <?php echo $lang['accountset'] ?></a>
								<div class="dropdown-divider"></div>
							<?php
							}
							?>

							<a class="dropdown-item" href="logout.php"><i class="fa fa-power-off m-r-5 m-l-5"></i>
								<?php echo $lang['logoouts'] ?></a>
						</div>
					</li>
					<!-- ============================================================== -->
					<!-- User profile and search -->
					<!-- ============================================================== -->
				</ul>
			</div>
		</nav>
	</header>

	<audio id="chatAudio">
		<source src="assets/notify.mp3" type="audio/mpeg">
	</audio>

	<script>
	document.addEventListener('DOMContentLoaded', function() {
		document.querySelectorAll('.lang-switch').forEach(function(el) {
			el.addEventListener('click', function(e) {
				e.preventDefault();
				var lang = this.getAttribute('data-lang');
				var xhr = new XMLHttpRequest();
				xhr.open('POST', 'ajax/tools/switch_language_ajax.php', true);
				xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
				xhr.onload = function() {
					try {
						var res = JSON.parse(xhr.responseText);
						if (res.status === 'success') {
							location.reload();
						} else {
							alert('Error: ' + res.message);
						}
					} catch(err) {
						alert('Parse error: ' + xhr.responseText);
					}
				};
				xhr.onerror = function() { alert('Network error'); };
				xhr.send('language=' + encodeURIComponent(lang));
			});
		});
	});
	</script>


	<!-- <script src="dataJs/load_notifications_all.js"> </script> -->