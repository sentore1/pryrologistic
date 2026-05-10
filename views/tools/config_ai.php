<div class="right-part mail-list bg-white">
    <div class="p-15 b-b">
        <div class="d-flex align-items-center">
            <span style="background:#7460ee; color:#fff; font-size:10px; font-weight:700; padding:2px 7px; border-radius:3px; margin-right:8px; letter-spacing:0.5px;">P-AI</span>
            <span>AI Assistant Configuration</span>
        </div>
    </div>

<?php
// Load current AI config from database
$ai_db = new Conexion;
$ai_db->cdp_query("SELECT ai_provider, groq_api_key, openai_api_key FROM cdb_settings LIMIT 1");
$ai_db->cdp_execute();
$ai_row = $ai_db->cdp_registro();

$current_groq     = ($ai_row && !empty($ai_row->groq_api_key))   ? $ai_row->groq_api_key   : '';
$current_openai   = ($ai_row && !empty($ai_row->openai_api_key)) ? $ai_row->openai_api_key : '';
$current_provider = ($ai_row && !empty($ai_row->ai_provider))    ? $ai_row->ai_provider    : 'groq';
$is_active        = !empty($current_groq) || !empty($current_openai);
?>

    <div class="bg-light p-15">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div id="resultados_ajax"></div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card-body">
                <form class="form-horizontal form-material" id="save_ai_config" name="save_ai_config" method="post">

                    <h4 class="card-title"><b>AI Provider Settings</b></h4>
                    <p class="text-muted" style="font-size:13px;">Connect an AI model to power the P-AI Daily Briefing on your dashboard. Get a free Groq key at <a href="https://console.groq.com" target="_blank">console.groq.com</a> or an OpenAI key at <a href="https://platform.openai.com/api-keys" target="_blank">platform.openai.com</a>.</p>
                    <hr />

                    <section>
                        <!-- Provider selection -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><i class="mdi mdi-robot"></i> AI Provider</label>
                                    <select class="form-control" name="ai_provider" id="ai_provider">
                                        <option value="groq"  <?php echo ($current_provider == 'groq')  ? 'selected' : ''; ?>>Groq (Free &amp; Fast — Recommended)</option>
                                        <option value="openai" <?php echo ($current_provider == 'openai') ? 'selected' : ''; ?>>OpenAI (GPT-4o)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Groq -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label><i class="mdi mdi-key"></i> Groq API Key</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="groq_api_key" id="groq_api_key"
                                            placeholder="gsk_xxxxxxxxxxxxxxxxxxxxxxxx"
                                            value="<?php echo htmlspecialchars($current_groq); ?>">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary toggle-eye" data-target="groq_api_key">
                                                <i class="mdi mdi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted">Get your free key at <a href="https://console.groq.com" target="_blank">console.groq.com</a></small>
                                </div>
                            </div>
                        </div>

                        <!-- OpenAI -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label><i class="mdi mdi-key-variant"></i> OpenAI API Key <small class="text-muted">(optional)</small></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="openai_api_key" id="openai_api_key"
                                            placeholder="sk-xxxxxxxxxxxxxxxxxxxxxxxx"
                                            value="<?php echo htmlspecialchars($current_openai); ?>">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary toggle-eye" data-target="openai_api_key">
                                                <i class="mdi mdi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted">Get your key at <a href="https://platform.openai.com/api-keys" target="_blank">platform.openai.com</a></small>
                                </div>
                            </div>
                        </div>

                        <hr />

                        <!-- Status indicator -->
                        <div class="row">
                            <div class="col-md-12">
                                <?php if ($is_active): ?>
                                    <div class="alert alert-success" style="font-size:13px;">
                                        <i class="mdi mdi-check-circle"></i> P-AI is <strong>active</strong>. The Daily Briefing is running on your dashboard.
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning" style="font-size:13px;">
                                        <i class="mdi mdi-alert"></i> No API key configured. Add a Groq or OpenAI key above to activate P-AI.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>

                    <div class="form-group">
                        <div class="col-sm-12">
                            <button type="button" class="btn btn-primary" id="btn_save_ai">
                                Save AI Settings <i class="icon-ok"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Eye toggle for API key fields
document.querySelectorAll('.toggle-eye').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var input = document.getElementById(this.getAttribute('data-target'));
        var icon  = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('mdi-eye', 'mdi-eye-off');
        } else {
            input.type = 'password';
            icon.classList.replace('mdi-eye-off', 'mdi-eye');
        }
    });
});

document.getElementById('btn_save_ai').addEventListener('click', function () {
    var btn = this;
    btn.disabled = true;
    btn.innerText = 'Saving...';

    var formData = new FormData();
    formData.append('groq_api_key',   document.getElementById('groq_api_key').value);
    formData.append('openai_api_key', document.getElementById('openai_api_key').value);
    formData.append('ai_provider',    document.getElementById('ai_provider').value);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/ai/save_ai_config_ajax.php', true);
    xhr.onload = function () {
        var box = document.getElementById('resultados_ajax');
        try {
            var res = JSON.parse(xhr.responseText);
            if (res.success) {
                box.innerHTML = '<div class="alert alert-success"><i class="mdi mdi-check-circle"></i> ' + res.message + '</div>';
            } else {
                box.innerHTML = '<div class="alert alert-danger"><i class="mdi mdi-alert"></i> ' + res.message + '</div>';
            }
        } catch (e) {
            box.innerHTML = '<div class="alert alert-danger">Unexpected response: ' + xhr.responseText + '</div>';
        }
        box.scrollIntoView({ behavior: 'smooth' });
        btn.disabled = false;
        btn.innerHTML = 'Save AI Settings <i class="icon-ok"></i>';
    };
    xhr.onerror = function () {
        document.getElementById('resultados_ajax').innerHTML = '<div class="alert alert-danger">Request failed. Check your server.</div>';
        btn.disabled = false;
        btn.innerHTML = 'Save AI Settings <i class="icon-ok"></i>';
    };
    xhr.send(formData);
});
</script>
