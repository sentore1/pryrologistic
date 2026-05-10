-- Add MOMO and Airtel Money as offline payment gateways
-- Run this SQL against your database

INSERT INTO `cdb_met_payment` (`id`, `name_pay`, `detail_pay`, `is_active`, `public_key`, `secret_key`, `paypal_client_id`)
VALUES
(6, 'MOMO', 'Mobile Money payment. The client sends payment via MOMO and attaches proof of payment for admin verification.', 0, NULL, NULL, NULL),
(7, 'Airtel Money', 'Airtel Money payment. The client sends payment via Airtel Money and attaches proof of payment for admin verification.', 0, NULL, NULL, NULL);
