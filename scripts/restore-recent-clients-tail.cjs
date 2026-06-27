const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const root = path.resolve(__dirname, '..');
const file = path.join(root, 'resources/views/AdminConsole/recent_clients/index.blade.php');
const current = fs.readFileSync(file, 'utf8').split(/\r?\n/);
const original = execSync('git show b2e4679e:resources/views/AdminConsole/recent_clients/index.blade.php', {
    cwd: root,
    encoding: 'utf8',
}).split(/\r?\n/);

const head = current.slice(0, 760); // through description block closing
let tail = original.slice(760).join('\n');

tail = tail
    .replace(/html \+= '<i class="far fa-calendar"><\/i> '/g, "html += crmIcon('calendar', 'regular') + ' '")
    .replace(/html \+= ' \| <i class="far fa-user"><\/i> '/g, "html += ' | ' + crmIcon('user', 'regular') + ' '")
    .replace(/html \+= '<h6><i class="fas fa-file"><\/i> Documents<\/h6>'/g, "html += '<h6>' + crmIcon('file') + ' Documents</h6>'")
    .replace(/<i class="fas fa-cloud-upload-alt"><\/i> Upload All These Docs to S3/g, "' + crmIcon('cloud-upload-alt') + ' Upload All These Docs to S3")
    .replace(/html \+= '<h6><i class="fas fa-archive"><\/i> Actions<\/h6>'/g, "html += '<h6>' + crmIcon('archive') + ' Actions</h6>'")
    .replace(/html \+= '<i class="fas fa-undo"><\/i> Unarchive Client'/g, "html += crmIcon('undo') + ' Unarchive Client'")
    .replace(/html \+= '<span class="ml-2 text-muted"><i class="fas fa-info-circle"><\/i> This client is currently archived<\/span>'/g, "html += '<span class=\"ml-2 text-muted\">' + crmIcon('info-circle') + ' This client is currently archived</span>'")
    .replace(/html \+= '<i class="fas fa-archive"><\/i> Archive Client'/g, "html += crmIcon('archive') + ' Archive Client'")
    .replace(/html \+= '<span class="ml-2 text-muted"><i class="fas fa-info-circle"><\/i> Archive this client to move it to archived clients<\/span>'/g, "html += '<span class=\"ml-2 text-muted\">' + crmIcon('info-circle') + ' Archive this client to move it to archived clients</span>'")
    .replace(/\$btn\.html\('<i class="fas fa-spinner fa-spin"><\/i> Processing\.\.\.'\)/g, "$btn.html(crmIconSpinner(' Processing...'))")
    .replace(/html\('<div class="text-center py-4"><i class="fas fa-spinner fa-spin"><\/i> Loading\.\.\.<\/div>'\)/g, "html('<div class=\"text-center py-4\">' + crmIconSpinner(' Loading...') + '</div>')");

fs.writeFileSync(file, [...head, tail].join('\n'));
console.log('Restored recent_clients tail:', original.length - 760, 'lines');
