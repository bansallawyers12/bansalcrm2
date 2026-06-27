const fs = require('fs');
const path = require('path');

const file = path.join(__dirname, '..', 'resources/views/AdminConsole/recent_clients/index.blade.php');
let content = fs.readFileSync(file, 'utf8');

const replacements = [
    [/<i class="fas fa-trash-alt"><\/i>/g, "' + crmIcon('trash-alt') + '"],
    [/<i class="fas fa-external-link-alt"><\/i>/g, "' + crmIcon('external-link-alt') + '"],
    [/<i class="fas fa-cloud-upload-alt"><\/i>/g, "' + crmIcon('cloud-upload-alt') + '"],
    [/\$btn\.prop\('disabled', true\)\.html\('<i class="fas fa-spinner fa-spin"><\/i> Deleting\.\.\.'\)/g, "$btn.prop('disabled', true).html(crmIconSpinner(' Deleting...'))"],
    [/\$btn\.prop\('disabled', true\)\.html\('<i class="fas fa-spinner fa-spin"><\/i> Uploading\.\.\.'\)/g, "$btn.prop('disabled', true).html(crmIconSpinner(' Uploading...'))"],
    [/\$btn\.prop\('disabled', true\)\.html\('<i class="fas fa-spinner fa-spin"><\/i> Checking\.\.\.'\)/g, "$btn.prop('disabled', true).html(crmIconSpinner(' Checking...'))"],
    [/\$btn\.html\('<i class="fas fa-spinner fa-spin"><\/i> Archiving\.\.\.'\)/g, "$btn.html(crmIconSpinner(' Archiving...'))"],
    [/\$btn\.prop\('disabled', true\)\.html\('<i class="fas fa-spinner fa-spin"><\/i>'\)/g, "$btn.prop('disabled', true).html(crmIconSpinner(''))"],
];

for (const [pattern, replacement] of replacements) {
    content = content.replace(pattern, replacement);
}

fs.writeFileSync(file, content);
console.log('Migrated recent_clients inline JS icons');
