-- ========================================================================
-- PRODUCTION DATABASE FIX SCRIPT
-- Fix sequence issues for 6 tables to prevent duplicate key errors
-- ========================================================================
-- Run this script on your production PostgreSQL database
-- This will sync all sequences with existing data
-- ========================================================================

-- Fix documents table sequence
DO $$
DECLARE
    max_id INTEGER;
BEGIN
    SELECT MAX(id) INTO max_id FROM documents;
    IF max_id IS NOT NULL THEN
        PERFORM setval('documents_id_seq', max_id);
        RAISE NOTICE 'documents: Sequence synced to % (next ID: %)', max_id, max_id + 1;
    END IF;
END $$;

-- Fix account_client_receipts table
DO $$
DECLARE
    max_id INTEGER;
    seq_exists BOOLEAN;
BEGIN
    -- Check if sequence exists
    SELECT EXISTS (
        SELECT 1 FROM pg_class WHERE relname = 'account_client_receipts_id_seq'
    ) INTO seq_exists;
    
    SELECT MAX(id) INTO max_id FROM account_client_receipts;
    
    IF NOT seq_exists THEN
        -- Create sequence if it doesn't exist
        EXECUTE format('CREATE SEQUENCE account_client_receipts_id_seq START WITH %s', COALESCE(max_id + 1, 1));
        ALTER TABLE account_client_receipts ALTER COLUMN id SET DEFAULT nextval('account_client_receipts_id_seq');
        ALTER SEQUENCE account_client_receipts_id_seq OWNED BY account_client_receipts.id;
        RAISE NOTICE 'account_client_receipts: Sequence created and synced to % (next ID: %)', max_id, COALESCE(max_id + 1, 1);
    ELSIF max_id IS NOT NULL THEN
        PERFORM setval('account_client_receipts_id_seq', max_id);
        RAISE NOTICE 'account_client_receipts: Sequence synced to % (next ID: %)', max_id, max_id + 1;
    END IF;
END $$;

-- Fix activities_logs table
DO $$
DECLARE
    max_id INTEGER;
    seq_exists BOOLEAN;
BEGIN
    SELECT EXISTS (
        SELECT 1 FROM pg_class WHERE relname = 'activities_logs_id_seq'
    ) INTO seq_exists;
    
    SELECT MAX(id) INTO max_id FROM activities_logs;
    
    IF NOT seq_exists THEN
        EXECUTE format('CREATE SEQUENCE activities_logs_id_seq START WITH %s', COALESCE(max_id + 1, 1));
        ALTER TABLE activities_logs ALTER COLUMN id SET DEFAULT nextval('activities_logs_id_seq');
        ALTER SEQUENCE activities_logs_id_seq OWNED BY activities_logs.id;
        RAISE NOTICE 'activities_logs: Sequence created and synced to % (next ID: %)', max_id, COALESCE(max_id + 1, 1);
    ELSIF max_id IS NOT NULL THEN
        PERFORM setval('activities_logs_id_seq', max_id);
        RAISE NOTICE 'activities_logs: Sequence synced to % (next ID: %)', max_id, max_id + 1;
    END IF;
END $$;

-- Fix application_activities_logs table
DO $$
DECLARE
    max_id INTEGER;
    seq_exists BOOLEAN;
BEGIN
    SELECT EXISTS (
        SELECT 1 FROM pg_class WHERE relname = 'application_activities_logs_id_seq'
    ) INTO seq_exists;
    
    SELECT MAX(id) INTO max_id FROM application_activities_logs;
    
    IF NOT seq_exists THEN
        EXECUTE format('CREATE SEQUENCE application_activities_logs_id_seq START WITH %s', COALESCE(max_id + 1, 1));
        ALTER TABLE application_activities_logs ALTER COLUMN id SET DEFAULT nextval('application_activities_logs_id_seq');
        ALTER SEQUENCE application_activities_logs_id_seq OWNED BY application_activities_logs.id;
        RAISE NOTICE 'application_activities_logs: Sequence created and synced to % (next ID: %)', max_id, COALESCE(max_id + 1, 1);
    ELSIF max_id IS NOT NULL THEN
        PERFORM setval('application_activities_logs_id_seq', max_id);
        RAISE NOTICE 'application_activities_logs: Sequence synced to % (next ID: %)', max_id, max_id + 1;
    END IF;
END $$;

-- Fix mail_reports table
DO $$
DECLARE
    max_id INTEGER;
    seq_exists BOOLEAN;
BEGIN
    SELECT EXISTS (
        SELECT 1 FROM pg_class WHERE relname = 'mail_reports_id_seq'
    ) INTO seq_exists;
    
    SELECT MAX(id) INTO max_id FROM mail_reports;
    
    IF NOT seq_exists THEN
        EXECUTE format('CREATE SEQUENCE mail_reports_id_seq START WITH %s', COALESCE(max_id + 1, 1));
        ALTER TABLE mail_reports ALTER COLUMN id SET DEFAULT nextval('mail_reports_id_seq');
        ALTER SEQUENCE mail_reports_id_seq OWNED BY mail_reports.id;
        RAISE NOTICE 'mail_reports: Sequence created and synced to % (next ID: %)', max_id, COALESCE(max_id + 1, 1);
    ELSIF max_id IS NOT NULL THEN
        PERFORM setval('mail_reports_id_seq', max_id);
        RAISE NOTICE 'mail_reports: Sequence synced to % (next ID: %)', max_id, max_id + 1;
    END IF;
END $$;

-- Fix notes table
DO $$
DECLARE
    max_id INTEGER;
    seq_exists BOOLEAN;
BEGIN
    SELECT EXISTS (
        SELECT 1 FROM pg_class WHERE relname = 'notes_id_seq'
    ) INTO seq_exists;
    
    SELECT MAX(id) INTO max_id FROM notes;
    
    IF NOT seq_exists THEN
        EXECUTE format('CREATE SEQUENCE notes_id_seq START WITH %s', COALESCE(max_id + 1, 1));
        ALTER TABLE notes ALTER COLUMN id SET DEFAULT nextval('notes_id_seq');
        ALTER SEQUENCE notes_id_seq OWNED BY notes.id;
        RAISE NOTICE 'notes: Sequence created and synced to % (next ID: %)', max_id, COALESCE(max_id + 1, 1);
    ELSIF max_id IS NOT NULL THEN
        PERFORM setval('notes_id_seq', max_id);
        RAISE NOTICE 'notes: Sequence synced to % (next ID: %)', max_id, max_id + 1;
    END IF;
END $$;

-- ========================================================================
-- END OF SCRIPT
-- ========================================================================
-- All sequences are now synced with existing data
-- Bulk uploads should work without duplicate key errors
-- ========================================================================