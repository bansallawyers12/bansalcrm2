/**
 * Fix corrupted icon migration artifacts.
 */
const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const root = path.resolve(__dirname, '..');

function gitShow(rev, file) {
    return execSync(`git show ${rev}:${file}`, { cwd: root, encoding: 'utf8', maxBuffer: 10 * 1024 * 1024 });
}

function migrateJsTailIcons(text) {
    return text
        .replace(/btn\.innerHTML = '<i class="fas fa-sync-alt"><\/i> Get Emails'/g, "btn.innerHTML = crmIcon('sync-alt') + ' Get Emails'")
        .replace(/btn\.innerHTML = '<i class="fas fa-sync-alt"><\/i> Get Drafts'/g, "btn.innerHTML = crmIcon('sync-alt') + ' Get Drafts'")
        .replace(/newMailBanner\.innerHTML = '<i class="fas fa-envelope"><\/i> '/g, "newMailBanner.innerHTML = crmIcon('envelope') + ' '")
        .replace(/btn\.innerHTML = '<i class="fas fa-spinner fa-spin"><\/i> Loading\.\.\.'/g, "btn.innerHTML = crmIconSpinner(' Loading...')")
        .replace(/saveDraftBtn\.innerHTML\s*=\s*'<i class="fas fa-spinner fa-spin"><\/i> Saving[^']*'/g, "saveDraftBtn.innerHTML = crmIconSpinner(' Saving…')")
        .replace(/saveDraftBtn\.innerHTML = '<i class="fas fa-save"><\/i> Save Draft'/g, "saveDraftBtn.innerHTML = crmIcon('save') + ' Save Draft'")
        .replace(/sendBtn\.innerHTML = '<i class="fas fa-spinner fa-spin"><\/i> Sending[^']*'/g, "sendBtn.innerHTML = crmIconSpinner(' Sending…')")
        .replace(/sendBtn\.innerHTML = '<i class="fas fa-paper-plane"><\/i> Send'/g, "sendBtn.innerHTML = crmIcon('paper-plane') + ' Send'");
}

function fixEliteInbox() {
    const file = path.join(root, 'resources/views/elite/emails-inbox.blade.php');
    const current = fs.readFileSync(file, 'utf8').split(/\r?\n/);
    let original;
    try {
        original = gitShow('5dbe73a9', 'resources/views/elite/emails-inbox.blade.php').split(/\r?\n/);
    } catch (e) {
        console.warn('Could not load elite inbox from git:', e.message);
        return;
    }

    const head = current.slice(0, 1211); // through params.push('sync=1');
    head.push("        if (!silent && btn) { btn.disabled = true; btn.innerHTML = crmIconSpinner(' Syncing...'); }");
    let tail = original.slice(1213, 1990).join('\n'); // if (silent) inboxFetchInFlight ... through }());
    tail = migrateJsTailIcons(tail);
    const restored = [...head, tail, ''].join('\n');
    fs.writeFileSync(file, restored);
    console.log('Fixed elite/emails-inbox.blade.php');
}

function fixRecentClients() {
    const file = path.join(root, 'resources/views/AdminConsole/recent_clients/index.blade.php');
    const current = fs.readFileSync(file, 'utf8');
    if (!current.includes("html += crmIconSpinner(' }}')")) {
        console.log('recent_clients: corruption marker not found, skip');
        return;
    }

    let original;
    try {
        original = gitShow('b2e4679e', 'resources/views/AdminConsole/recent_clients/index.blade.php');
    } catch (e) {
        console.warn('Could not load recent_clients from git:', e.message);
        return;
    }

    const origLines = original.split(/\r?\n/);
    const restoredBlock = origLines.slice(760, 846).join('\n')
        .replace(
            /html \+= '<i class="far fa-calendar"><\/i> '/g,
            "html += crmIcon('calendar', 'regular') + ' '"
        )
        .replace(
            /html \+= ' \| <i class="far fa-user"><\/i> '/g,
            "html += ' | ' + crmIcon('user', 'regular') + ' '"
        )
        .replace(
            /html \+= '<h6><i class="fas fa-file"><\/i> Documents<\/h6>'/g,
            "html += '<h6>' + crmIcon('file') + ' Documents</h6>'"
        )
        .replace(
            /<i class="fas fa-cloud-upload-alt"><\/i> Upload All These Docs to S3/g,
            "' + crmIcon('cloud-upload-alt') + ' Upload All These Docs to S3"
        )
        .replace(
            /html \+= '<h6><i class="fas fa-archive"><\/i> Actions<\/h6>'/g,
            "html += '<h6>' + crmIcon('archive') + ' Actions</h6>'"
        )
        .replace(
            /html \+= '<i class="fas fa-undo"><\/i> Unarchive Client'/g,
            "html += crmIcon('undo') + ' Unarchive Client'"
        )
        .replace(
            /html \+= '<span class="ml-2 text-muted"><i class="fas fa-info-circle"><\/i> This client is currently archived<\/span>'/g,
            "html += '<span class=\"ml-2 text-muted\">' + crmIcon('info-circle') + ' This client is currently archived</span>'"
        )
        .replace(
            /html \+= '<i class="fas fa-archive"><\/i> Archive Client'/g,
            "html += crmIcon('archive') + ' Archive Client'"
        )
        .replace(
            /html \+= '<span class="ml-2 text-muted"><i class="fas fa-info-circle"><\/i> Archive this client to move it to archived clients<\/span>'/g,
            "html += '<span class=\"ml-2 text-muted\">' + crmIcon('info-circle') + ' Archive this client to move it to archived clients</span>'"
        );

    const marker = "\t\t\t\t\t\thtml += '<div class=\"text-muted small\">';\n\t\t\t\t\t\thtml += crmIconSpinner(' }}'),";
    const replacement = "\t\t\t\t\t\thtml += '<div class=\"text-muted small\">';\n" + restoredBlock.split('\n').slice(1).join('\n');

    if (!current.includes(marker.split('\n')[0])) {
        // try alternate corruption start
        const start = current.indexOf("\t\t\t\t\t\thtml += '<div class=\"text-muted small\">';");
        const end = current.indexOf('\t\t\t\t\t\t$container.html(html);');
        if (start === -1 || end === -1) {
            console.warn('recent_clients: could not locate block boundaries');
            return;
        }
        const fixed = current.slice(0, start) + restoredBlock + current.slice(end);
        fs.writeFileSync(file, fixed);
    } else {
        fs.writeFileSync(file, current.replace(marker, replacement));
    }
    console.log('Fixed AdminConsole/recent_clients/index.blade.php');
}

/** In backtick template literals, ' + crmIcon(...) + ' is literal text — use ${crmIcon(...)} */
function fixTemplateLiteralCrmIcons(content) {
    return content.replace(
        /(`(?:\\`|[^`])*)' \+ crmIcon\(([^)]+(?:\([^)]*\)[^)]*)*)\) \+ '((?:\\`|[^`])*)`/g,
        (match, before, args, after) => `${before}\${crmIcon(${args})}${after}\``
    );
}

function fixJsTemplateLiterals() {
    const files = [
        'public/js/pages/admin/client-detail/document-categories.js',
        'public/js/pages/admin/client-detail/document-signature.js',
        'public/js/pages/admin/client-detail/document-actions.js',
        'public/js/pages/admin/client-detail/blade-inline.js',
        'public/js/pages/admin/partner-detail/invoice-handlers.js',
        'public/js/common/document-handlers.js',
        'public/js/emails_v2.js',
    ];

    for (const rel of files) {
        const file = path.join(root, rel);
        if (!fs.existsSync(file)) continue;
        const original = fs.readFileSync(file, 'utf8');
        let content = fixTemplateLiteralCrmIcons(original);

        if (rel.endsWith('document-actions.js')) {
            content = content.replace(
                /trRow \+= "<tr class='drow' id='id_"+subArray\.id+"'><td>"\+subArray\.checklist+"<\/td><td>"\+ res\.Added_By \+ "<br>" \+ res\.Added_date+"<\/td><td><a target='_blank' class='dropdown-item' href='"+subArray\.myfile+"'>' \+ crmIcon\('file-image'\) \+ ' <span>"\+subArray\.file_name\+'\.'\+subArray\.filetype+"<\/span><\/a><\/div><\/td><td>"\+res\.Verified_By\+ "<br>" \+res\.Verified_At+"<\/td><\/tr>";/,
                'trRow += "<tr class=\'drow\' id=\'id_"+subArray.id+"\'><td>"+subArray.checklist+"</td><td>"+ res.Added_By + "<br>" + res.Added_date+"</td><td><a target=\'_blank\' class=\'dropdown-item\' href=\'"+subArray.myfile+"\'>"+crmIcon(\'file-image\')+" <span>"+subArray.file_name+\'.\'+subArray.filetype+"</span></a></td><td>"+res.Verified_By+ "<br>" +res.Verified_At+"</td></tr>";'
            );
            content = content.replace(
                /trRow \+= "<tr class='drow' id='id_"+subArray\.id+"'><td>"\+subArray\.checklist+"<\/td><td>"\+ res\.Added_By \+ "<br>" \+ res\.Added_date+"<\/td><td>' \+ crmIcon\('file-image'\) \+ ' <span>"\+subArray\.file_name\+'\.'\+subArray\.filetype+"<\/span><\/div><\/td><td>"\+res\.Verified_By\+ "<br>" \+res\.Verified_At+"<\/td><\/tr>";/,
                'trRow += "<tr class=\'drow\' id=\'id_"+subArray.id+"\'><td>"+subArray.checklist+"</td><td>"+ res.Added_By + "<br>" + res.Added_date+"</td><td>"+crmIcon(\'file-image\')+" <span>"+subArray.file_name+\'.\'+subArray.filetype+"</span></td><td>"+res.Verified_By+ "<br>" +res.Verified_At+"</td></tr>";'
            );
        }

        if (content !== original) {
            fs.writeFileSync(file, content);
            console.log('Fixed template literals:', rel);
        }
    }
}

function fixSignaturesShow() {
    const file = path.join(root, 'resources/views/crm/signatures/show.blade.php');
    let content = fs.readFileSync(file, 'utf8');
    content = content.replace(
        /@icon\('\{\{', 'solid', \['class' => '\$signer->status === 'pending' \? 'clock' : \(\$signer->status === 'signed' \? 'check' : 'times'\) }}'\]\)/,
        "@icon($signer->status === 'pending' ? 'clock' : ($signer->status === 'signed' ? 'check' : 'times'))"
    );
    content = content.replace(
        /@icon\('\{\{', 'solid', \['class' => '\$icon }}'\]\)/,
        '@icon($icon)'
    );
    fs.writeFileSync(file, content);
    console.log('Fixed crm/signatures/show.blade.php');
}

function fixExplanationCircle() {
    const files = [
        'resources/views/Admin/clients/addclientmodal.blade.php',
        'resources/views/Admin/partners/addpartnermodal.blade.php',
        'resources/views/Admin/products/addproductmodal.blade.php',
    ];
    for (const rel of files) {
        const file = path.join(root, rel);
        if (!fs.existsSync(file)) continue;
        const content = fs.readFileSync(file, 'utf8').replace(/@icon\('explanation-circle'\)/g, "@icon('info-circle')");
        fs.writeFileSync(file, content);
        console.log('Fixed explanation-circle in', rel);
    }
}

function fixMinimalLayoutScripts() {
    const file = path.join(root, 'resources/js/minimal-layout-scripts.js');
    const content = `/**
 * Minimal layout scripts (login, outlook) — Vite entry (Phase 2f).
 */
'use strict';

import '@legacy/common/crm-icon.js';
import '@legacy/scripts.js';
import '@legacy/custom.js';
`;
    fs.writeFileSync(file, content);
    console.log('Added crm-icon.js to minimal-layout-scripts.js');
}

fixEliteInbox();
fixRecentClients();
fixSignaturesShow();
fixExplanationCircle();
fixMinimalLayoutScripts();
fixJsTemplateLiterals();
