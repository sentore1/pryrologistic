-- P-AI Configuration columns for cdb_settings
-- Run this once to add AI settings to the existing settings table

ALTER TABLE `cdb_settings`
    ADD COLUMN IF NOT EXISTS `ai_provider`    VARCHAR(20)  DEFAULT 'groq' AFTER `language`,
    ADD COLUMN IF NOT EXISTS `groq_api_key`   VARCHAR(255) DEFAULT ''     AFTER `ai_provider`,
    ADD COLUMN IF NOT EXISTS `openai_api_key` VARCHAR(255) DEFAULT ''     AFTER `groq_api_key`;
