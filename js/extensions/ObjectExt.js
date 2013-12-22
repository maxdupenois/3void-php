/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


Object.prototype.isInstance = function(classType){
    var constructorString = this.constructor.toString();
    constructorString = constructorString.replace(/^function\s/, "");
    constructorString = constructorString.replace(/\([^)]*\)\s?\{(.|\n)*$/gm, "");
    constructorString = constructorString.replace(/\s/g, "");
    return (constructorString==classType);
};
/*Modified from code written by: Troels Knak-Nielsen, got from
 * http://blogs.sitepoint.com/2006/01/17/javascript-inheritance
 */
Object.prototype.inheritFrom = function (parent){
    var constructorString = parent.toString();
    constructorString = constructorString.replace(/^function\s/, "");
    constructorString = constructorString.replace(/\([^)]*\)\s?\{(.|\n)*$/gm, "");
    constructorString = constructorString.replace(/\s/g, "");

    if (constructorString!=""&& constructorString != null ) { this[constructorString] = parent; }
    for (var m in parent.prototype) {
        if(typeof(this[m])=="undefined"){
            this[m] = parent.prototype[m];
        }
    }
};