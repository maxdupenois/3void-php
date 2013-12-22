/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var DOMUtils = {
    setOpacity : function(element, value){
        if (value == 1){
            DOMUtils.setStyle(element, {
                opacity:
                (/Gecko/.test(navigator.userAgent) && !/Konqueror|Safari|KHTML/.test(navigator.userAgent)) ?
                0.999999 : null
            });
            if(/MSIE/.test(navigator.userAgent))
                DOMUtils.setStyle(element, {
                    filter: DOMUtils.getStyle(element,'filter').replace(/alpha\([^\)]*\)/gi,'')
                    });
        } else {
            if(value < 0.00001) value = 0;
            DOMUtils.setStyle(element, {
                opacity: value
            });
            if(/MSIE/.test(navigator.userAgent))
                DOMUtils.setStyle(element,
                {
                    filter: DOMUtils.getStyle(element,'filter').replace(/alpha\([^\)]*\)/gi,'') +
                    'alpha(opacity='+value*100+')'
                });
        }
    },
    setStyle : function (element, style) {
        for(k in style){
            element.style[k] = style[k];
        }
    },
    getStyle : function getStyle(element, name){
        return element.style[name];
    },
    newElement : function (element, attributes){
        var el;
        try{
            var att_string = "";
            if(attributes !=null){
                for(a in attributes){
                    if(typeof(attributes[a]) != "function"){
                        att_string += a+"=\""+attributes[a]+"\" ";
                    }
                }
            }
            el = document.createElement("<"+element+" "+att_string+">");
        }catch(e){
            el = document.createElement(element);
            if(attributes !=null){
                for(a in attributes){
                    if(typeof(attributes[a]) != "function"){
                        el.setAttribute(a, attributes[a]);
                    }
                }
            }
        }
        return el;
    },
    positionOf : function (element) {
        var curleft, curtop = 0;

        if (element.offsetParent) {
            curleft = element.offsetLeft
            curtop = element.offsetTop

            while (element = element.offsetParent) {
                curleft += element.offsetLeft
                curtop += element.offsetTop
            }

        }
        return {"left":curleft,"right":curtop};
    },
    removeChildren : function(element){
        while(element.hasChildNodes()){
            element.removeChild(element.firstChild);
        }
    },
    getElementsByClassName : function(baseElement, className, elementList){
        if(!elementList) var elementList = new Array();
        var children = baseElement.childNodes;
        for(var i=0; i < children.length; i++){
            var child = children[i];
            if(!child) continue;
            if(child.tagName == "#text") continue;
            if(child.hasChildNodes()){
                elementList = DOMUtils.getElementsByClassName(child, className, elementList);
            }
            if(child.className != null && child.className == className){
                elementList.push(child);
            }
        }
        return elementList;
    },
    isChildOf : function (parent, child){
        var isChild = false;

        while((child)&&(child.nodeName != "BODY")){
            if(child == parent){
                isChild = true;
                break;
            }
            child = child.parentNode;
        }

        return isChild;
    },
    setText : function(element, text){
        var textElementFound = false;
        var i =0;
        while(!textElementFound && i<element.childNodes.length){
            if(element.childNodes[i].nodeName.toLowerCase() == "#text"){
                element.childNodes[i].nodeValue = text;
                textElementFound = true;
            }
            i++;
        }
        if(!textElementFound) element.appendChild(document.createTextNode(text));
    },
    getText : function(element){
        var i =element.childNodes.length-1;
        var textString = null;
        while(textString == null && i>=0){
            if(element.childNodes[i].nodeName.toLowerCase() == "#text"){
                textString = element.childNodes[i].nodeValue;
            }
            i--;
        }
        return textString;
    }
};