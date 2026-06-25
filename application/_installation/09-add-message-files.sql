ALTER TABLE messages
ADD COLUMN file_filename VARCHAR(255) NULL AFTER message_text,
ADD COLUMN file_original_name VARCHAR(255) NULL AFTER file_filename,
ADD COLUMN file_mime_type VARCHAR(100) NULL AFTER file_original_name;