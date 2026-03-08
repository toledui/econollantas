const fs = require('fs');
const path = require('path');

function walk(dir) {
    let results = [];
    const list = fs.readdirSync(dir);
    list.forEach(function (file) {
        file = path.join(dir, file);
        const stat = fs.statSync(file);
        if (stat && stat.isDirectory()) {
            results = results.concat(walk(file));
        } else {
            results.push(file);
        }
    });
    return results;
}

const files = walk('c:/Development/Laravel/econollantas/resources/views');
let modifiedCount = 0;

files.forEach(file => {
    if (['.php'].includes(path.extname(file))) {
        let content = fs.readFileSync(file, 'utf8');
        let newContent = content;

        // Fix the spaces introduced inside strings/attributes by my previous script
        // 1. After equal sign `=" ` or `=' `
        newContent = newContent.replace(/=(["']) (\S)/g, '=$1$2');

        // 2. After opening parenthesis `(' ` or `(" `
        newContent = newContent.replace(/\((["']) (\S)/g, '($1$2');

        // 3. After a bracket `[' ` or `[" `
        newContent = newContent.replace(/\[(["']) (\S)/g, '[$1$2');

        // 4. After comma `, ' ` or `, " `  -> `, '`
        // Wait, comma might have its own spaces, like `, ' value `, my script did `, ' value`. We want `, 'value`
        newContent = newContent.replace(/,( *)((["']) )(\S)/g, ',$1$3$4');
        newContent = newContent.replace(/,(.*?)(['"]) ([a-zA-Z0-9])/g, ',$1$2$3');

        // Let's just do a simpler pass. My script blindly did: replace(/(["'])([a-zA-Z@:-]{1})/g, '$1 $2');
        // This means ANY quote followed by a letter got a space between them. 
        // We want to UNDO this specific change ONLY when that quote is the START of a string (following =, (, [, or whitespace+,)
        // Actually, if it's following `="` the quote is the start.
        // If the quote is closing a string, it is followed by another attribute or text.
        // The ones that shouldn't have spaces are inside the string... wait, my script added space AFTER the quote. 
        // So `="` -> `=" `. This is ALWAYS wrong in HTML/blade unless intended, and here we caused it. 
        // `('` -> `(' ` 
        // `['` -> `[' ` 
        // `> '` (text node starting with quote and no space? rare).

        newContent = newContent.replace(/=(['"]) ([a-zA-Z@:-])/g, '=$1$2');
        newContent = newContent.replace(/\(\s*(['"]) ([a-zA-Z@:-])/g, '($1$2');
        newContent = newContent.replace(/\[\s*(['"]) ([a-zA-Z@:-])/g, '[$1$2');
        newContent = newContent.replace(/,\s*(['"]) ([a-zA-Z@:-])/g, ', $1$2');
        newContent = newContent.replace(/{\s*(['"]) ([a-zA-Z@:-])/g, '{$1$2');
        newContent = newContent.replace(/:\s*(['"]) ([a-zA-Z@:-])/g, ':$1$2'); // e.g. default: ' value'

        // Clean up redundant spaces like `class=" mb-4"` -> `class="mb-4"`
        // The above `=(['"]) ([a-zA-Z])` will fix `class=" mb-4"`.

        if (content !== newContent) {
            fs.writeFileSync(file, newContent, 'utf8');
            modifiedCount++;
        }
    }
});

console.log('Archivos arreglados de espacios en strings:', modifiedCount);
