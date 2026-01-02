<?php
/**
 * Script to update client route references from admin.clients.* and agent.clients.* to clients.*
 * Also updates hardcoded URLs from /admin/clients and /agent/clients to /clients
 * 
 * Run this from command line: php update_client_routes.php
 * 
 * IMPORTANT: Backup your files before running this script!
 */

// Directories to update
$directories = [
    'resources/views/Admin/clients/',
    'resources/views/Agent/clients/',
    'resources/views/Admin/archived/',
    'resources/views/Admin/prospects/',
    'resources/views/Elements/Admin/',
    'resources/views/Elements/Agent/',
    'resources/views/layouts/',
    'public/js/pages/admin/',
    'public/js/pages/agent/',
    'public/js/',
];

// Route name replacements (admin.clients.* -> clients.*)
$routeReplacements = [
    // Main CRUD routes
    "route('admin.clients.index')" => "route('clients.index')",
    "route('admin.clients.create')" => "route('clients.create')",
    "route('admin.clients.store')" => "route('clients.store')",
    "route('admin.clients.edit')" => "route('clients.edit')",
    "route('admin.clients.detail')" => "route('clients.detail')",
    "route('admin.clients.prospects')" => "route('clients.prospects')",
    "route('admin.clients.archived')" => "route('clients.archived')",
    
    // AJAX routes
    "route('admin.clients.getrecipients')" => "route('clients.getrecipients')",
    "route('admin.clients.getonlyclientrecipients')" => "route('clients.getonlyclientrecipients')",
    "route('admin.clients.getallclients')" => "route('clients.getallclients')",
    "route('admin.clients.createnote')" => "route('clients.createnote')",
    "route('admin.clients.getnotedetail')" => "route('clients.getnotedetail')",
    "route('admin.clients.deletenote')" => "route('clients.deletenote')",
    "route('admin.clients.activities')" => "route('clients.activities')",
    "route('admin.clients.getnotes')" => "route('clients.getnotes')",
    "route('admin.clients.uploaddocument')" => "route('clients.uploaddocument')",
    "route('admin.clients.deletedocs')" => "route('clients.deletedocs')",
    "route('admin.clients.renamedoc')" => "route('clients.renamedoc')",
    "route('admin.clients.updateclientstatus')" => "route('clients.updateclientstatus')",
    "route('admin.clients.getapplicationlists')" => "route('clients.getapplicationlists')",
    "route('admin.clients.saveapplication')" => "route('clients.saveapplication')",
    "route('admin.clients.convertapplication')" => "route('clients.convertapplication')",
    "route('admin.clients.deleteservices')" => "route('clients.deleteservices')",
    "route('admin.clients.deleteactivitylog')" => "route('clients.deleteactivitylog')",
    "route('admin.clients.notpickedcall')" => "route('clients.notpickedcall')",
    "route('admin.clients.pinactivitylog')" => "route('clients.pinactivitylog')",
    "route('admin.clients.saveaccountreport')" => "route('clients.saveaccountreport')",
    "route('admin.clients.getTopReceiptValInDB')" => "route('clients.getTopReceiptValInDB')",
    "route('admin.clients.getClientReceiptInfoById')" => "route('clients.getClientReceiptInfoById')",
    "route('admin.clients.clientreceiptlist')" => "route('clients.clientreceiptlist')",
    "route('admin.clients.validate_receipt')" => "route('clients.validate_receipt')",
    "route('admin.clients.printpreview')" => "route('clients.printpreview')",
    "route('admin.clients.addalldocchecklist')" => "route('clients.addalldocchecklist')",
    "route('admin.clients.uploadalldocument')" => "route('clients.uploadalldocument')",
    "route('admin.clients.notuseddoc')" => "route('clients.notuseddoc')",
    "route('admin.clients.renamechecklistdoc')" => "route('clients.renamechecklistdoc')",
    "route('admin.clients.verifydoc')" => "route('clients.verifydoc')",
    "route('admin.clients.deletealldocs')" => "route('clients.deletealldocs')",
    "route('admin.clients.renamealldoc')" => "route('clients.renamealldoc')",
    "route('admin.clients.backtodoc')" => "route('clients.backtodoc')",
    "route('admin.clients.fetchClientContactNo')" => "route('clients.fetchClientContactNo')",
    "route('admin.clients.sendmsg')" => "route('clients.sendmsg')",
    "route('admin.clients.isgreviewmailsent')" => "route('clients.isgreviewmailsent')",
    "route('admin.mail.enhance')" => "route('clients.enhanceMessage')",
    "route('admin.clients.address_auto_populate')" => "route('clients.address_auto_populate')",
    "route('admin.clients.createservicetaken')" => "route('clients.createservicetaken')",
    "route('admin.clients.removeservicetaken')" => "route('clients.removeservicetaken')",
    "route('admin.clients.getservicetaken')" => "route('clients.getservicetaken')",
    "route('admin.clients.gettagdata')" => "route('clients.gettagdata')",
    "route('admin.clients.updatesessioncompleted')" => "route('clients.updatesessioncompleted')",
    "route('client.merge_records')" => "route('clients.merge_records')",
    "route('client.validate_receipt')" => "route('clients.validate_receipt')",
    
    // Agent routes to unified routes
    "route('agent.clients.index')" => "route('clients.index')",
    "route('agent.clients.create')" => "route('clients.create')",
    "route('agent.clients.store')" => "route('clients.store')",
    "route('agent.clients.edit')" => "route('clients.edit')",
    "route('agent.clients.detail')" => "route('clients.detail')",
    "route('agent.clients.getrecipients')" => "route('clients.getrecipients')",
    "route('agent.clients.getallclients')" => "route('clients.getallclients')",
    "route('agent.clients.createnote')" => "route('clients.createnote')",
    "route('agent.clients.getnotedetail')" => "route('clients.getnotedetail')",
    "route('agent.clients.deletenote')" => "route('clients.deletenote')",
    "route('agent.clients.prospects')" => "route('clients.prospects')",
    "route('agent.clients.archived')" => "route('clients.archived')",
    "route('agent.clients.updateclientstatus')" => "route('clients.updateclientstatus')",
    "route('agent.clients.activities')" => "route('clients.activities')",
    "route('agent.clients.getapplicationlists')" => "route('clients.getapplicationlists')",
    "route('agent.clients.saveapplication')" => "route('clients.saveapplication')",
    "route('agent.clients.getnotes')" => "route('clients.getnotes')",
    "route('agent.clients.convertapplication')" => "route('clients.convertapplication')",
    "route('agent.clients.deleteservices')" => "route('clients.deleteservices')",
    "route('agent.clients.uploaddocument')" => "route('clients.uploaddocument')",
    "route('agent.clients.deletedocs')" => "route('clients.deletedocs')",
    "route('agent.clients.renamedoc')" => "route('clients.renamedoc')",
    
    // View detail routes
    "route('clients.viewnotedetail')" => "route('clients.viewnotedetail')",
    "route('clients.viewapplicationnote')" => "route('clients.viewapplicationnote')",
    "route('clients.pinnote')" => "route('clients.pinnote')",
    "route('clients.saveprevvisa')" => "route('clients.saveprevvisa')",
    "route('clients.save_tag')" => "route('clients.save_tag')",
    "route('clients.interested-service')" => "route('clients.interested-service')",
    "route('clients.edit-interested-service')" => "route('clients.edit-interested-service')",
    "route('clients.get-services')" => "route('clients.get-services')",
    "route('clients.uploadmail')" => "route('clients.uploadmail')",
    "route('clients.updatefollowupschedule')" => "route('clients.updatefollowupschedule')",
    "route('clients.getintrestedservice')" => "route('clients.getintrestedservice')",
    "route('clients.saleforcastservice')" => "route('clients.saleforcastservice')",
    "route('clients.getintrestedserviceedit')" => "route('clients.getintrestedserviceedit')",
    "route('clients.savetoapplication')" => "route('clients.savetoapplication')",
    "route('clients.downloadpdf')" => "route('clients.downloadpdf')",
    "route('clients.followup.store')" => "route('clients.followup.store')",
    "route('clients.followup.store_application')" => "route('clients.followup.store_application')",
    "route('clients.followup.retagfollowup')" => "route('clients.followup.retagfollowup')",
    "route('clients.changetype')" => "route('clients.changetype')",
    "route('clients.removetag')" => "route('clients.removetag')",
    "route('clients.change_assignee')" => "route('clients.change_assignee')",
    "route('clients.personalfollowup.store')" => "route('clients.personalfollowup.store')",
    "route('clients.updatefollowup.store')" => "route('clients.updatefollowup.store')",
    "route('clients.reassignfollowup.store')" => "route('clients.reassignfollowup.store')",
];

// URL replacements (hardcoded URLs)
$urlReplacements = [
    // Admin URLs - single quotes
    "'/admin/clients'" => "'/clients'",
    "'/admin/clients/" => "'/clients/",
    "'/admin/clients?'" => "'/clients?'",
    
    // Admin URLs - double quotes
    '"/admin/clients"' => '"/clients"',
    '"/admin/clients/' => '"/clients/',
    '"/admin/clients?' => '"/clients?',
    
    // Admin URLs - URL helpers
    "URL::to('/admin/clients" => "URL::to('/clients",
    "url('/admin/clients" => "url('/clients",
    "{{URL::to('/admin/clients" => "{{URL::to('/clients",
    "{{url('/admin/clients" => "{{url('/clients",
    "{{ url('/admin/clients" => "{{ url('/clients",
    
    // Agent URLs - single quotes
    "'/agent/clients'" => "'/clients'",
    "'/agent/clients/" => "'/clients/",
    "'/agent/clients?'" => "'/clients?'",
    
    // Agent URLs - double quotes
    '"/agent/clients"' => '"/clients"',
    '"/agent/clients/' => '"/clients/',
    '"/agent/clients?' => '"/clients?',
    
    // Agent URLs - URL helpers
    "URL::to('/agent/clients" => "URL::to('/clients",
    "url('/agent/clients" => "url('/clients",
    "{{URL::to('/agent/clients" => "{{URL::to('/clients",
    "{{url('/agent/clients" => "{{url('/clients",
    
    // JavaScript URL patterns
    "siteUrl + '/admin/clients" => "siteUrl + '/clients",
    "siteUrl + '/agent/clients" => "siteUrl + '/clients",
    "site_url + '/admin/clients" => "site_url + '/clients",
    "site_url + '/agent/clients" => "site_url + '/clients",
    "baseUrl + '/admin/clients" => "baseUrl + '/clients",
    "baseUrl + '/agent/clients" => "baseUrl + '/clients",
    "base_url + '/admin/clients" => "base_url + '/clients",
    "base_url + '/agent/clients" => "base_url + '/clients",
    
    // Redirect patterns
    "Redirect::to('/admin/clients" => "Redirect::to('/clients",
    "redirect('/admin/clients" => "redirect('/clients",
    "redirect()->to('/admin/clients" => "redirect()->to('/clients",
    "Redirect::to('/agent/clients" => "Redirect::to('/clients",
    "redirect('/agent/clients" => "redirect('/clients",
    "redirect()->to('/agent/clients" => "redirect()->to('/clients",
    
    // Href patterns
    "href='/admin/clients" => "href='/clients",
    'href="/admin/clients' => 'href="/clients',
    "href='/agent/clients" => "href='/clients",
    'href="/agent/clients' => 'href="/clients',
    
    // Action patterns
    "action='/admin/clients" => "action='/clients",
    'action="/admin/clients' => 'action="/clients',
    "action='/agent/clients" => "action='/clients",
    'action="/agent/clients' => 'action="/clients',
];

// Statistics
$stats = [
    'files_processed' => 0,
    'files_updated' => 0,
    'replacements_made' => 0,
    'errors' => [],
];

echo "=== Client Routes Update Script ===\n\n";
echo "This script will update route references and URLs in views and JavaScript files.\n";
echo "IMPORTANT: Make sure you have a backup before running this!\n\n";
echo "Starting update...\n\n";

// Helper function to recursively get files
function getFilesRecursive($dir, $extensions = ['php', 'js', 'blade.php']) {
    $files = [];
    if (!is_dir($dir)) {
        return $files;
    }
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $ext = $file->getExtension();
            if (in_array($ext, $extensions)) {
                $files[] = $file->getPathname();
            }
        }
    }
    
    return $files;
}

// Process each directory
foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        echo "⚠ Directory not found: $dir (skipping)\n";
        continue;
    }
    
    $files = getFilesRecursive($dir);
    
    foreach ($files as $filePath) {
        try {
            $stats['files_processed']++;
            $content = file_get_contents($filePath);
            $originalContent = $content;
            $fileReplacements = 0;
            
            // Apply route replacements
            foreach ($routeReplacements as $old => $new) {
                $count = 0;
                $content = str_replace($old, $new, $content, $count);
                $fileReplacements += $count;
            }
            
            // Apply URL replacements
            foreach ($urlReplacements as $old => $new) {
                $count = 0;
                $content = str_replace($old, $new, $content, $count);
                $fileReplacements += $count;
            }
            
            // If content changed, write it back
            if ($content !== $originalContent) {
                if (file_put_contents($filePath, $content) !== false) {
                    $stats['files_updated']++;
                    $stats['replacements_made'] += $fileReplacements;
                    $relativePath = str_replace(getcwd() . DIRECTORY_SEPARATOR, '', $filePath);
                    echo "✓ Updated: $relativePath ($fileReplacements replacements)\n";
                } else {
                    $stats['errors'][] = "Failed to write: $filePath";
                    echo "✗ Failed to write: $filePath\n";
                }
            }
        } catch (Exception $e) {
            $stats['errors'][] = "Error processing $filePath: " . $e->getMessage();
            echo "✗ Error: $filePath - " . $e->getMessage() . "\n";
        }
    }
}

// Print summary
echo "\n=== Summary ===\n";
echo "Files processed: {$stats['files_processed']}\n";
echo "Files updated: {$stats['files_updated']}\n";
echo "Total replacements: {$stats['replacements_made']}\n";

if (!empty($stats['errors'])) {
    echo "\n⚠ Errors encountered: " . count($stats['errors']) . "\n";
    foreach ($stats['errors'] as $error) {
        echo "  - $error\n";
    }
}

echo "\n=== Done! ===\n";
echo "\nNext steps:\n";
echo "1. Review the changes in a few key files\n";
echo "2. Clear Laravel caches: php artisan route:clear && php artisan config:clear\n";
echo "3. Test the application\n";
echo "4. Check browser console for any JavaScript errors\n";

