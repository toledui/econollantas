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
        // Match things like dark:bg-black, hover:dark:text-white, dark:ring-zinc-800, sm:dark:focus-visible:ring-[#FF2D20]
        let newContent = content.replace(/(?:\b|(?<=['"\s]))([a-z0-9-]+:)*dark:[^\s"'>]+/g, '');
        // Clean up redundant spaces left by removals
        newContent = newContent.replace(/ +(?=['"])/g, '');
        newContent = newContent.replace(/(?<=['"]) +/g, '');
        newContent = newContent.replace(/  +/g, ' ');

        if (content !== newContent) {
            fs.writeFileSync(file, newContent, 'utf8');
            modifiedCount++;
        }
    }
});

console.log('Modified files:', modifiedCount);
