<?php
// ini_set('display_errors', 1);
require_once("../../helpers/querys.php");
require_once '../../vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

/**
 * Central dispatch — routes to UltraMsg, Twilio, or Meta based on saved provider.
 * $to      — recipient phone number
 * $message — text body
 * $pdf_base64 — optional base64 PDF (UltraMsg & Twilio support; Meta sends text only)
 * $pdf_filename — filename for the PDF attachment
 */
function dispatchWhatsAppMessage($settings, $to, $message, $pdf_base64 = null, $pdf_filename = 'document.pdf')
{
    $provider = $settings->whatsapp_provider ?? 'ultramsg';

    if ($provider === 'twilio') {
        return dispatchViaTwilio($settings, $to, $message, $pdf_base64, $pdf_filename);
    } elseif ($provider === 'meta') {
        return dispatchViaMeta($settings, $to, $message);
    } else {
        return dispatchViaUltraMsg($settings, $to, $message, $pdf_base64, $pdf_filename);
    }
}

function dispatchViaUltraMsg($settings, $to, $message, $pdf_base64 = null, $pdf_filename = 'document.pdf')
{
    $curl = curl_init();

    if ($pdf_base64 !== null) {
        $params = array(
            'token'    => $settings->api_ws_token,
            'to'       => $to,
            'document' => $pdf_base64,
            'caption'  => $message,
            'filename' => $pdf_filename,
        );
        $endpoint = $settings->api_ws_url . "messages/document";
    } else {
        $params = array(
            'token' => $settings->api_ws_token,
            'to'    => $to,
            'body'  => $message,
        );
        $endpoint = $settings->api_ws_url . "messages/chat";
    }

    curl_setopt_array($curl, array(
        CURLOPT_URL            => $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => "",
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => "POST",
        CURLOPT_POSTFIELDS     => http_build_query($params),
        CURLOPT_HTTPHEADER     => array("content-type: application/x-www-form-urlencoded"),
    ));

    $response = curl_exec($curl);
    $err      = curl_error($curl);
    curl_close($curl);

    if ($err) return ['success' => false, 'message' => "UltraMsg cURL error: " . $err];
    return ['success' => true, 'message' => "Sent via UltraMsg"];
}

function dispatchViaTwilio($settings, $to, $message, $pdf_base64 = null, $pdf_filename = 'document.pdf')
{
    $sid   = $settings->twilio_wa_sid;
    $token = $settings->twilio_wa_token;
    $from  = $settings->twilio_wa_number; // e.g. whatsapp:+14155238886

    // Ensure 'to' is in whatsapp: format
    $toFormatted = (strpos($to, 'whatsapp:') === false) ? 'whatsapp:+' . ltrim($to, '+') : $to;

    $params = array(
        'From' => $from,
        'To'   => $toFormatted,
        'Body' => $message,
    );

    // Twilio can send media via URL — base64 not supported directly,
    // so PDF attachments fall back to text-only for Twilio
    $url  = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_CUSTOMREQUEST  => "POST",
        CURLOPT_POSTFIELDS     => http_build_query($params),
        CURLOPT_USERPWD        => "{$sid}:{$token}",
        CURLOPT_HTTPHEADER     => array("content-type: application/x-www-form-urlencoded"),
    ));

    $response = curl_exec($curl);
    $err      = curl_error($curl);
    curl_close($curl);

    if ($err) return ['success' => false, 'message' => "Twilio cURL error: " . $err];

    $decoded = json_decode($response, true);
    if (isset($decoded['sid'])) {
        return ['success' => true, 'message' => "Sent via Twilio"];
    }
    $errMsg = $decoded['message'] ?? $response;
    return ['success' => false, 'message' => "Twilio error: " . $errMsg];
}

function dispatchViaMeta($settings, $to, $message)
{
    $token    = $settings->meta_wa_token;
    $phone_id = $settings->meta_wa_phone_id;

    // Strip any non-numeric chars except leading +
    $toFormatted = preg_replace('/[^0-9]/', '', $to);

    $payload = json_encode(array(
        'messaging_product' => 'whatsapp',
        'to'                => $toFormatted,
        'type'              => 'text',
        'text'              => array('body' => $message),
    ));

    $url  = "https://graph.facebook.com/v19.0/{$phone_id}/messages";
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_CUSTOMREQUEST  => "POST",
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => array(
            "Authorization: Bearer {$token}",
            "Content-Type: application/json",
        ),
    ));

    $response = curl_exec($curl);
    $err      = curl_error($curl);
    curl_close($curl);

    if ($err) return ['success' => false, 'message' => "Meta cURL error: " . $err];

    $decoded = json_decode($response, true);
    if (isset($decoded['messages'][0]['id'])) {
        return ['success' => true, 'message' => "Sent via Meta Cloud API"];
    }
    $errMsg = $decoded['error']['message'] ?? $response;
    return ['success' => false, 'message' => "Meta error: " . $errMsg];
}


// ---------------------------------------------------------------------------
// Original public functions — now all route through dispatchWhatsAppMessage()
// ---------------------------------------------------------------------------

function sendNotificationWhatsApp($sender, $notification_template, $template_whatsapp_body, $tracking_number = null)
{
    $settings = cdp_getSettingsCourier();
    $result   = ['success' => false, 'message' => ''];

    if (intval($settings->active_whatsapp) != 1) return $result;

    $whatsapp_body = null;

    if ($notification_template !== null) {
        $default = getDefaultTemplateActiveWhatsApp($notification_template);
        if (intval($default->active) == 1 && $default->id_template !== null) {
            $whatsapp_body = getTemplateWhatsApp($default->id_template)->body;
        }
    } elseif ($template_whatsapp_body !== null) {
        $whatsapp_body = $template_whatsapp_body;
    }

    if ($whatsapp_body === null) {
        $result['message'] = "No WhatsApp template found";
        return $result;
    }

    $message = str_replace(
        ['[CUSTOMER_FULLNAME]', '[COMPANY_NAME]', '[COMPANY_SITE_URL]', '[TRACKING_NUMBER]'],
        [$sender->fname . ' ' . $sender->lname, $settings->site_name, $settings->site_url, $tracking_number],
        $whatsapp_body
    );

    return dispatchWhatsAppMessage($settings, $sender->phone, $message);
}


function sendNotificationWhatsAppWithPDF($sender, $package_id, $notification_template)
{
    $db = new Conexion;
    $db->cdp_query("SELECT * FROM cdb_settings");
    $db->cdp_execute();
    $settings_lang = $db->cdp_registro();

    if ($db->cdp_rowCount() > 0) {
        $config_lang = $settings_lang->language;
        include("../../helpers/languages/$config_lang.php");
    }

    $core     = new Core;
    $db       = new Conexion;
    $settings = cdp_getSettingsCourier();
    $result   = ['success' => false, 'message' => ''];

    if (intval($settings->active_whatsapp) != 1) return $result;
    if ($notification_template === null) return $result;

    $default = getDefaultTemplateActiveWhatsApp($notification_template);
    if (intval($default->active) != 1 || $default->id_template === null) return $result;

    $whatsapp_body = getTemplateWhatsApp($default->id_template)->body;
    if ($whatsapp_body === null) return $result;

    $db->cdp_query("SELECT * FROM cdb_add_order WHERE order_id='" . $package_id . "'");
    $row      = $db->cdp_registro();
    $tracking = trim($row->order_prefix . $row->order_no);

    $db->cdp_query("SELECT * FROM cdb_styles where id= '" . $row->status_courier . "'");
    $status_courier = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_users where id= '" . $row->sender_id . "'");
    $sender_data = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_recipients where id= '" . $row->receiver_id . "'");
    $receiver_data = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_courier_com where id= '" . $row->order_courier . "'");
    $courier_com = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_met_payment where id= '" . $row->order_pay_mode . "'");
    $met_payment = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_shipping_mode where id= '" . $row->order_service_options . "'");
    $order_service_options = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_packaging where id= '" . $row->order_package . "'");
    $packaging = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_delivery_time where id= '" . $row->order_deli_time . "'");
    $delivery_time = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_branchoffices where id= '" . $row->agency . "'");
    $branchoffices = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_offices where id= '" . $row->origin_off . "'");
    $offices = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_address_shipments WHERE order_track='" . $row->order_prefix . $row->order_no . "'");
    $address_order = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_add_order_item WHERE order_id='" . $row->order_id . "'");
    $order_items = $db->cdp_registros();
    $fecha    = date("Y-m-d :h:i A", strtotime($row->order_datetime));
    $logo_src = "../../assets/";

    $message = str_replace(
        ['[CUSTOMER_FULLNAME]', '[COMPANY_NAME]', '[COMPANY_SITE_URL]', '[TRACKING_NUMBER]'],
        [$sender->fname . ' ' . $sender->lname, $settings->site_name, $settings->site_url, $tracking],
        $whatsapp_body
    );

    try {
        ob_start();
        include('../../pdf/documentos/html/shipment_print.php');
        $content = ob_get_clean();

        $html2pdf = new Html2Pdf('P', 'A4', 'es', true, 'UTF-8', 0);
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->writeHTML($content);
        $pdf_base64 = base64_encode($html2pdf->Output('', 'S'));

        return dispatchWhatsAppMessage($settings, $sender->phone, $message, $pdf_base64, 'Factura_' . $tracking . '.pdf');
    } catch (Html2PdfException $e) {
        $html2pdf->clean();
        echo (new ExceptionFormatter($e))->getHtmlMessage();
        return $result;
    }
}


function sendNotificationWhatsAppWithPDFPackages($sender, $package_id, $notification_template)
{
    $db = new Conexion;
    $db->cdp_query("SELECT * FROM cdb_settings");
    $db->cdp_execute();
    $settings_lang = $db->cdp_registro();

    if ($db->cdp_rowCount() > 0) {
        $config_lang = $settings_lang->language;
        include("../../helpers/languages/$config_lang.php");
    }

    $core     = new Core;
    $db       = new Conexion;
    $settings = cdp_getSettingsCourier();
    $result   = ['success' => false, 'message' => ''];

    if (intval($settings->active_whatsapp) != 1) return $result;
    if ($notification_template === null) return $result;

    $default = getDefaultTemplateActiveWhatsApp($notification_template);
    if (intval($default->active) != 1 || $default->id_template === null) return $result;

    $whatsapp_body = getTemplateWhatsApp($default->id_template)->body;
    if ($whatsapp_body === null) return $result;

    $db->cdp_query("SELECT * FROM cdb_customers_packages WHERE order_id='" . $package_id . "'");
    $row = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_users WHERE id= '" . $row->sender_id . "'");
    $sender_data = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_courier_com WHERE id= '" . $row->order_courier . "'");
    $courier_com = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_category where id= '" . $row->order_item_category . "'");
    $category = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_met_payment WHERE id= '" . $row->order_pay_mode . "'");
    $met_payment = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_address_shipments WHERE order_track='" . $row->order_prefix . $row->order_no . "'");
    $address_order = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_customers_packages_detail WHERE order_id='" . $row->order_id . "'");
    $order_items = $db->cdp_registros();

    $dias_  = array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
    $meses_ = array('01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');
    $fecha    = date("Y-m-d :h:i A", strtotime($row->order_datetime));
    $tracking = $row->order_prefix . $row->order_no;
    $logo_src = "../../assets/";

    $message = str_replace(
        ['[CUSTOMER_FULLNAME]', '[COMPANY_NAME]', '[COMPANY_SITE_URL]', '[TRACKING_NUMBER]'],
        [$sender->fname . ' ' . $sender->lname, $settings->site_name, $settings->site_url, $tracking],
        $whatsapp_body
    );

    try {
        ob_start();
        include('../../pdf/documentos/html/package_print.php');
        $content = ob_get_clean();

        $html2pdf = new Html2Pdf('P', 'A4', 'es', true, 'UTF-8', 0);
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->writeHTML($content);
        $pdf_base64 = base64_encode($html2pdf->Output('', 'S'));

        return dispatchWhatsAppMessage($settings, $sender->phone, $message, $pdf_base64, 'Factura_' . $tracking . '.pdf');
    } catch (Html2PdfException $e) {
        $html2pdf->clean();
        echo (new ExceptionFormatter($e))->getHtmlMessage();
        return $result;
    }
}


function sendNotificationWhatsAppWithPDFPackagess($sender, $package_id, $notification_template)
{
    $db = new Conexion;
    $db->cdp_query("SELECT * FROM cdb_settings");
    $db->cdp_execute();
    $settings_lang = $db->cdp_registro();

    if ($db->cdp_rowCount() > 0) {
        $config_lang = $settings_lang->language;
        include("../../helpers/languages/$config_lang.php");
    }

    $core     = new Core;
    $db       = new Conexion;
    $settings = cdp_getSettingsCourier();
    $result   = ['success' => false, 'message' => ''];

    if (intval($settings->active_whatsapp) != 1) return $result;
    if ($notification_template === null) return $result;

    $default = getDefaultTemplateActiveWhatsApp($notification_template);
    if (intval($default->active) != 1 || $default->id_template === null) return $result;

    $whatsapp_body = getTemplateWhatsApp($default->id_template)->body;
    if ($whatsapp_body === null) return $result;

    $db->cdp_query("SELECT * FROM cdb_customers_packages WHERE order_id='" . $package_id . "'");
    $package = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_styles where id= '" . $package->status_courier . "'");
    $status_courier = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_users where id= '" . $package->sender_id . "'");
    $sender_data = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_courier_com where id= '" . $package->order_courier . "'");
    $courier_com = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_met_payment where id= '" . $package->order_pay_mode . "'");
    $met_payment = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_shipping_mode where id= '" . $package->order_service_options . "'");
    $order_service_options = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_packaging where id= '" . $package->order_package . "'");
    $packaging = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_delivery_time where id= '" . $package->order_deli_time . "'");
    $delivery_time = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_branchoffices where id= '" . $package->agency . "'");
    $branchoffices = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_offices where id= '" . $package->origin_off . "'");
    $offices = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_address_shipments where order_track='" . $package->order_prefix . $package->order_no . "'");
    $address_order = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_customers_packages_detail WHERE order_id='" . $package->order_id . "'");
    $order_items = $db->cdp_registros();

    $dias_  = array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
    $meses_ = array('01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');
    $fecha    = date("Y-m-d :h:i A", strtotime($package->order_datetime));
    $tracking = $package->order_prefix . $package->order_no;
    $logo_src = "../../assets/";

    $message = str_replace(
        ['[CUSTOMER_FULLNAME]', '[COMPANY_NAME]', '[COMPANY_SITE_URL]', '[TRACKING_NUMBER]'],
        [$sender->fname . ' ' . $sender->lname, $settings->site_name, $settings->site_url, $tracking],
        $whatsapp_body
    );

    try {
        ob_start();
        include('../../pdf/documentos/html/shipment_print.php');
        $content = ob_get_clean();

        $html2pdf = new Html2Pdf('P', 'A4', 'es', true, 'UTF-8', 0);
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->writeHTML($content);
        $pdf_base64 = base64_encode($html2pdf->Output('', 'S'));

        return dispatchWhatsAppMessage($settings, $sender->phone, $message, $pdf_base64, 'Factura_' . $tracking . '.pdf');
    } catch (Html2PdfException $e) {
        $html2pdf->clean();
        echo (new ExceptionFormatter($e))->getHtmlMessage();
        return $result;
    }
}


function sendNotificationWhatsAppWithPDFConsolidate($sender, $consolidate_id, $notification_template)
{
    $core     = new Core;
    $db       = new Conexion;
    $settings = cdp_getSettingsCourier();
    $result   = ['success' => false, 'message' => ''];

    if (intval($settings->active_whatsapp) != 1) return $result;
    if ($notification_template === null) return $result;

    $default = getDefaultTemplateActiveWhatsApp($notification_template);
    if (intval($default->active) != 1 || $default->id_template === null) return $result;

    $whatsapp_body = getTemplateWhatsApp($default->id_template)->body;
    if ($whatsapp_body === null) return $result;

    $db->cdp_query("SELECT * FROM cdb_consolidate WHERE consolidate_id='" . $consolidate_id . "'");
    $package  = $db->cdp_registro();
    $tracking = $package->order_prefix . $package->order_no;

    $db->cdp_query("SELECT * FROM cdb_consolidate_detail WHERE consolidate_id='" . $package->consolidate_id . "'");
    $paquetes_detalles = $db->cdp_registros();
    $db->cdp_query("SELECT * FROM cdb_met_payment where id= '" . $package->order_pay_mode . "'");
    $met_payment = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_courier_com where id= '" . $package->order_courier . "'");
    $courier_com = $db->cdp_registro();
    $fecha = date("Y-m-d :h:i A", strtotime($package->order_datetime));
    $db->cdp_query("SELECT * FROM cdb_users where id= '" . $package->sender_id . "'");
    $sender_data = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_users where id= '" . $package->recipient_id . "'");
    $recipients_data = $db->cdp_registro();
    $db->cdp_query("SELECT * FROM cdb_address_shipments where order_track='" . $tracking . "'");
    $address_order = $db->cdp_registro();
    $logo_src = "../../assets/";

    $message = str_replace(
        ['[CUSTOMER_FULLNAME]', '[COMPANY_NAME]', '[COMPANY_SITE_URL]', '[TRACKING_NUMBER]'],
        [$sender->fname . ' ' . $sender->lname, $settings->site_name, $settings->site_url, $tracking],
        $whatsapp_body
    );

    try {
        ob_start();
        include('../../pdf/documentos/html/shipment_export_pdf.php');
        $content = ob_get_clean();

        $html2pdf = new Html2Pdf('P', 'A4', 'es', true, 'UTF-8', 0);
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->writeHTML($content);
        $pdf_base64 = base64_encode($html2pdf->Output('', 'S'));

        return dispatchWhatsAppMessage($settings, $sender->phone, $message, $pdf_base64, 'Factura_' . $tracking . '.pdf');
    } catch (Html2PdfException $e) {
        $html2pdf->clean();
        echo (new ExceptionFormatter($e))->getHtmlMessage();
        return $result;
    }
}
