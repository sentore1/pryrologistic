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
					<!-- P-AI Button -->
					<?php if ($userData->userlevel == 9 || $userData->userlevel == 2): ?>
					<li class="nav-item d-flex align-items-center mr-2">
						<button onclick="cdp_openPAI()" id="btn-pryro-ai" style="background:#0d6efd; color:#fff; border:none; font-size:8px; font-weight:700; padding:3px 8px; border-radius:20px; letter-spacing:0.5px; cursor:pointer; transition: all 0.2s ease; white-space:nowrap; overflow:hidden; max-width:24px;"
						onmouseenter="this.style.maxWidth='70px'; this.innerHTML='PRYRO AI';"
						onmouseleave="this.style.maxWidth='24px'; this.innerHTML='AI';">AI</button>
					</li>
					<?php endif; ?>
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

<!-- P-AI Modal -->
<div class="modal fade" id="modal-pai" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="pai-modal-dialog" role="document" style="transition: all 0.2s ease;">
        <div class="modal-content" style="border-radius:0; overflow:hidden; display:flex; flex-direction:column; height:100%;">
            <!-- Header -->
            <div class="modal-header" style="background:#0d6efd; color:#fff; padding:12px 20px; flex-shrink:0;">
                <div class="d-flex align-items-center">
                    <span style="background:rgba(255,255,255,0.2); font-size:10px; font-weight:700; padding:2px 8px; border-radius:3px; margin-right:10px; letter-spacing:1px;">PRYRO AI</span>
                    <h5 class="modal-title mb-0" style="color:#fff; font-size:15px;">Operations Assistant</h5>
                </div>
                <div class="d-flex align-items-center">
                    <!-- Expand/fullscreen toggle -->
                    <button type="button" id="btn-pai-expand" onclick="cdp_togglePAIFullscreen()" style="background:rgba(255,255,255,0.15); border:none; color:#fff; border-radius:4px; padding:3px 8px; margin-right:8px; cursor:pointer;" title="Expand">
                        <i class="ti-fullscreen"></i>
                    </button>
                    <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:1; margin:0;">
                        <span>&times;</span>
                    </button>
                </div>
            </div>

            <!-- Chat messages -->
            <div id="pai-chat-messages" style="flex:1; overflow-y:auto; padding:16px; background:#f8f9fa; min-height:320px; max-height:420px;">
                <div class="text-center text-muted py-4">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2 mb-0" style="font-size:13px;">Pryro AI is analyzing your system...</p>
                </div>
            </div>

            <!-- Input -->
            <div style="padding:12px 16px; border-top:1px solid #e9ecef; background:#fff; flex-shrink:0;">
                <div class="input-group">
                    <input type="text" id="pai-chat-input" class="form-control" placeholder="Ask Pryro AI anything about your operations..." style="border-radius:20px 0 0 20px; font-size:13px;">
                    <div class="input-group-append">
                        <button id="pai-send-btn" onclick="cdp_sendPAIMessage()" class="btn btn-primary" style="border-radius:0 20px 20px 0; padding:6px 18px;">
                            <i class="ti-arrow-right"></i>
                        </button>
                    </div>
                </div>
                <div style="font-size:10px; color:#aaa; margin-top:5px; padding-left:4px;">Press Enter to send</div>
            </div>
        </div>
    </div>
</div>

<script>
var paiHistory   = [];
var paiFullscreen = false;

function cdp_openPAI() {
    paiHistory = [];
    $('#modal-pai').modal('show');
    // Send initial briefing request
    cdp_sendPAIMessage('Give me a full briefing of the current system status. Include stuck shipments with details, driver workload, overdue payments with customer names and amounts, revenue comparison vs last month, top customers, and what happened in the last 24 hours.');
}

function cdp_togglePAIFullscreen() {
    var $dialog = $('#pai-modal-dialog');
    var $msgs   = $('#pai-chat-messages');
    var $icon   = $('#btn-pai-expand i');
    paiFullscreen = !paiFullscreen;
    if (paiFullscreen) {
        $dialog.css({
            'position':'fixed', 'top':'0', 'left':'0', 'right':'0', 'bottom':'0',
            'max-width':'100vw', 'width':'100vw', 'height':'100vh',
            'margin':'0', 'padding':'0', 'z-index':'9999'
        });
        $('.modal-content', $dialog).css('height','100vh');
        $msgs.css('max-height', 'calc(100vh - 160px)');
        $icon.removeClass('ti-fullscreen').addClass('ti-zoom-out');
    } else {
        $dialog.css({
            'position':'', 'top':'', 'left':'', 'right':'', 'bottom':'',
            'max-width':'800px', 'width':'', 'height':'',
            'margin':'', 'padding':'', 'z-index':''
        });
        $('.modal-content', $dialog).css('height','');
        $msgs.css('max-height', '420px');
        $icon.removeClass('ti-zoom-out').addClass('ti-fullscreen');
    }
}

function cdp_sendPAIMessage(autoMsg) {
    var msg = autoMsg || $('#pai-chat-input').val().trim();
    if (!msg) return;

    var $msgs = $('#pai-chat-messages');
    var $input = $('#pai-chat-input');
    var $btn   = $('#pai-send-btn');

    // Clear input
    if (!autoMsg) $input.val('');

    // Clear initial spinner if first message
    if (paiHistory.length === 0 && autoMsg) {
        $msgs.html('');
    }

    // Show user message (skip for auto briefing)
    if (!autoMsg) {
        $msgs.append(
            '<div style="display:flex; justify-content:flex-end; margin-bottom:10px;">'
            + '<div style="background:#0d6efd; color:#fff; padding:8px 14px; border-radius:16px 16px 4px 16px; max-width:75%; font-size:13px; line-height:1.5;">'
            + $('<div>').text(msg).html()
            + '</div></div>'
        );
    }

    // Show typing indicator
    var typingId = 'typing-' + Date.now();
    $msgs.append(
        '<div id="' + typingId + '" style="display:flex; align-items:flex-start; margin-bottom:10px;">'
        + '<div style="background:#0d6efd; color:#fff; width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:9px; font-weight:700; margin-right:8px; flex-shrink:0;">AI</div>'
        + '<div style="background:#fff; border:1px solid #e9ecef; padding:8px 14px; border-radius:4px 16px 16px 16px; font-size:13px; color:#888;">'
        + '<i class="fa fa-spinner fa-spin"></i> Thinking...</div></div>'
    );
    $msgs.scrollTop($msgs[0].scrollHeight);
    $btn.prop('disabled', true);

    $.ajax({
        url: 'ajax/ai/ai_chat_ajax.php',
        type: 'POST',
        dataType: 'json',
        data: {
            message: msg,
            history: JSON.stringify(paiHistory)
        },
        success: function(data) {
            $('#' + typingId).remove();
            var reply   = data.reply   || 'No response.';
            var actions = data.actions || [];

            // Format reply text
            var html = cdp_formatPAIReply(reply);

            // Build action buttons if any
            var actionsHtml = '';
            if (actions.length > 0) {
                actionsHtml += '<div style="margin-top:10px; padding-top:10px; border-top:1px solid #e9ecef; display:flex; flex-wrap:wrap; gap:6px;">';
                actions.forEach(function(act, idx) {
                    var btnId = 'pai-act-' + Date.now() + '-' + idx;
                    var color = '#0d6efd';
                    if (act.action === 'confirm_payment' || act.action === 'confirm_all_wire_payments') color = '#28a745';
                    if (act.action === 'update_status') color = '#fd7e14';
                    actionsHtml += '<button id="' + btnId + '" '
                        + 'onclick="cdp_executeAction(' + JSON.stringify(act).replace(/"/g, '&quot;') + ', \'' + btnId + '\')" '
                        + 'style="background:' + color + '; color:#fff; border:none; padding:5px 12px; border-radius:4px; font-size:11px; font-weight:600; cursor:pointer;" '
                        + 'title="' + (act.description || '') + '">'
                        + act.label
                        + '</button>';
                });
                actionsHtml += '</div>';
            }

            $msgs.append(
                '<div style="display:flex; align-items:flex-start; margin-bottom:12px;">'
                + '<div style="background:#0d6efd; color:#fff; width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:9px; font-weight:700; margin-right:8px; flex-shrink:0;">AI</div>'
                + '<div style="background:#fff; border:1px solid #e9ecef; padding:10px 14px; border-radius:4px 16px 16px 16px; max-width:85%; font-size:13px; line-height:1.6;">'
                + html + actionsHtml
                + '</div></div>'
            );
            $msgs.scrollTop($msgs[0].scrollHeight);

            // Update history
            paiHistory.push({ role: 'user',      content: msg });
            paiHistory.push({ role: 'assistant', content: reply });
            if (paiHistory.length > 20) paiHistory = paiHistory.slice(-20);
        },
        error: function() {
            $('#' + typingId).remove();
            $msgs.append('<div class="alert alert-warning m-2" style="font-size:12px;">Could not reach AI. Check your API key in <a href="tools.php?list=config_ai">AI Settings</a>.</div>');
        },
        complete: function() {
            $btn.prop('disabled', false);
            $input.focus();
        }
    });
}

// Execute an action button click
function cdp_executeAction(act, btnId) {
    var $btn = $('#' + btnId);
    $btn.prop('disabled', true).text('Processing...');

    $.ajax({
        url: 'ajax/ai/ai_action_ajax.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action:  act.action,
            payload: JSON.stringify(act)
        },
        success: function(res) {
            if (res.success) {
                $btn.css('background', '#28a745').text('✓ Done');
                // Add confirmation message in chat
                var $msgs = $('#pai-chat-messages');
                $msgs.append(
                    '<div style="display:flex; justify-content:center; margin-bottom:8px;">'
                    + '<div style="background:#d4edda; color:#155724; padding:5px 14px; border-radius:20px; font-size:12px;">'
                    + '✓ ' + res.message
                    + '</div></div>'
                );
                $msgs.scrollTop($msgs[0].scrollHeight);
            } else {
                $btn.prop('disabled', false).css('background', '#dc3545').text('✗ Failed');
                setTimeout(function(){ $btn.css('background','').prop('disabled', false).text(act.label); }, 3000);
            }
        },
        error: function() {
            $btn.prop('disabled', false).text(act.label);
            alert('Action failed. Please try again.');
        }
    });
}

function cdp_formatPAIReply(text) {
    text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    text = text.replace(/\*(.+?)\*/g, '<em>$1</em>');
    var lines = text.split('\n');
    var html  = '';
    lines.forEach(function(line) {
        line = line.trim();
        if (!line) { html += '<div style="height:6px;"></div>'; return; }
        if (line.match(/^[-•*]\s+/)) {
            line = line.replace(/^[-•*]\s+/, '');
            html += '<div style="padding-left:12px; margin-bottom:4px; display:flex; gap:6px;"><span style="color:#0d6efd; flex-shrink:0;">•</span><span>' + line + '</span></div>';
        } else {
            html += '<div style="margin-bottom:4px;">' + line + '</div>';
        }
    });
    return html;
}

// Enter key to send
$(document).on('keypress', '#pai-chat-input', function(e) {
    if (e.which === 13) cdp_sendPAIMessage();
});

// Reset on modal close
$('#modal-pai').on('hidden.bs.modal', function() {
    paiHistory = [];
    paiFullscreen = false;
    $('#pai-modal-dialog').css({ 'position':'', 'top':'', 'left':'', 'right':'', 'bottom':'', 'max-width':'', 'width':'', 'height':'', 'margin':'', 'padding':'', 'z-index':'' });
    $('.modal-content', '#pai-modal-dialog').css('height','');
    $('#pai-chat-messages').css('max-height', '420px').html(
        '<div class="text-center text-muted py-4"><i class="fa fa-spinner fa-spin fa-2x"></i><p class="mt-2 mb-0" style="font-size:13px;">Pryro AI is analyzing your system...</p></div>'
    );
    $('#btn-pai-expand i').removeClass('ti-zoom-out').addClass('ti-fullscreen');
});
</script>