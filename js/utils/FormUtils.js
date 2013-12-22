/* 
 * Requires ArrayExt.js and DOMUtils.js
 * 
 */
var FormUtils = {
    setSelectedOption : function(selectElement, value){
        if(selectElement == null) return;
        if(selectElement['nodeName'] == null || selectElement['nodeName'].toLowerCase() != "select") return;
        var options = selectElement.options;
        var selected = false;
        var unselected = false;
        var i = 0;
        while(!(selected && unselected) && i < options.length){
            if(options[i].value == value){
                options[i].selected = true;
                selected = true;
            }
            if(options[i].selected &&  options[i].value != value){
                options[i].selected = false;
                unselected = true;
            }
            i++;
        }
    },
    getSelectedOptions : function(selectElement){
        var options = selectElement.options;
        var i = options.length-1;
        var selectedOptions = new Array();
        while(i >= 0){
            if(options[i].selected){
                selectedOptions[selectedOptions.length] = options[i];
            }
            i--;
        }
        return selectedOptions;
    },
    selectMoveOptions : function(selectElement, up){
        var finished = false;
        var i;
        var newOptions;
        var x;
        if(up){
            i = 0;
            while(i< selectElement.options.length && !finished){
                if(i==0 && selectElement.options[i].selected){
                    finished = true;
                }else if(i != 0 && selectElement.options[i].selected){
                    newOptions = selectElement.options.moveElement(selectElement.options[i], i-1);
                    x = 0;
                    DOMUtils.removeChildren(selectElement);
                    while(x < newOptions.length){
                        selectElement.appendChild(newOptions[x]);
                        x++;
                    }
                }
                i++;
            }
        }else{
            i = selectElement.options.length-1 ;
            while(i>=0  && !finished){
                if(i==(selectElement.options.length-1) && selectElement.options[i].selected){
                    finished = true;
                }else if(i != (selectElement.options.length-1) && selectElement.options[i].selected){
                    newOptions = selectElement.options.moveElement(selectElement.options, selectElement.options[i], i+1);
                    x = 0;
                    DOMUtils.removeChildren(selectElement);
                    while(x < newOptions.length){
                        selectElement.appendChild(newOptions[x]);
                        x++;
                    }
                }
                i--;
            }
        }
    },
    
    getFormValuesForPost : function(form){
        var post = "";
        var keyValue = "";
        var elementNodeName;
        var elementName;
        var element;
        for(var i =0;i<form.elements.length;i++){
            keyValue = "";
            element = form.elements[i];
            elementNodeName = element.nodeName;
            elementName = element.name;
            switch(elementNodeName.toUpperCase()){
                case "INPUT":
                    var inputType = element["type"];
                    switch(inputType.toUpperCase()){
                        case "TEXT":
                            keyValue = elementName+"="+encodeURI(element.value);
                        break;
                        case "CHECKBOX":
                            keyValue = elementName+"="+(element.selected?"1":"0");
                        break;
                        case "RADIO":
                            keyValue = elementName+"="+encodeURI(element.value);
                        break;
                        default:
                    }
                break;
                case "SELECT":
                    var selected = FormUtils.getSelectedOptions(element);
                    if(selected.length==0){
                        keyValue = elementName+"=";
                    }else if(selected.length==1){
                        keyValue = elementName+"="+encodeURI(selected[0].value)+"";
                    }else{
                        for(var j=0;j<selected.length;j++) {
                            if(j!=0) keyValue += "&";
                            keyValue += elementName+"[]="+encodeURI(selected[j].value);
                        }
                    }
                break;
                case "TEXTAREA":
                    keyValue = elementName+"="+encodeURI(element.value);
                break;
                default:
            }
            if(post!=""&&keyValue!="") post += "&";
            post += keyValue;
        }
        return post;
    }


};
