-- WhatsApp multi-provider migration
-- Run this once against your cdb_settings table

ALTER TABLE `cdb_settings`
    ADD COLUMN `whatsapp_provider` VARCHAR(20) NOT NULL DEFAULT 'ultramsg' AFTER `active_whatsapp`,
    ADD COLUMN `twilio_wa_sid`     VARCHAR(100) NOT NULL DEFAULT '' AFTER `whatsapp_provider`,
    ADD COLUMN `twilio_wa_token`   VARCHAR(100) NOT NULL DEFAULT '' AFTER `twilio_wa_sid`,
    ADD COLUMN `twilio_wa_number`  VARCHAR(50)  NOT NULL DEFAULT '' AFTER `twilio_wa_token`,
    ADD COLUMN `meta_wa_token`     VARCHAR(255) NOT NULL DEFAULT '' AFTER `twilio_wa_number`,
    ADD COLUMN `meta_wa_phone_id`  VARCHAR(100) NOT NULL DEFAULT '' AFTER `meta_wa_token`;
