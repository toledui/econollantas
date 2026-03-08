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

        // El script anterior ejecutó:
        // newContent = newContent.replace(/ +(?=['"])/g, '');  (borró espacios ANTES de la comilla, ej: class="hola" > se hizo class="hola">)
        // newContent = newContent.replace(/(?<=['"]) +/g, ''); (borró espacios DESPUÉS de la comilla, ej: class="hola" name "texto" se hizo class="hola"name="texto")

        // 1. Añadir espacio DESPUÉS de las comillas, si les sigue una letra, @, o :
        // Ej: "mb-4":status => "mb-4" :status
        // Ej: "login"class  => "login" class
        // Ignoramos si es > (eso no necesita espacio obligatorio aunque podemos ponerlo)
        newContent = newContent.replace(/(["'])([a-zA-Z@:-]{1})/g, '$1 $2');

        // 2. Corregir casos de " /> -> "/> que está bien, pero aseguramos de tener espacio para limpiar
        // newContent = newContent.replace(/(["'])\/>/g, '$1 />');

        // Cuidemos comillas simples dentro de dobles, etc. Este regex simple debería funcionar en html normal.

        if (content !== newContent) {
            fs.writeFileSync(file, newContent, 'utf8');
            modifiedCount++;
        }
    }
});

console.log('Archivos reparados exitosamente:', modifiedCount);
