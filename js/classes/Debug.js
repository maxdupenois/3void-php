/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var Debug = {
    on : false,
    floatingDebugBox : function(title, text){
        if(!Debug.on) return;
        if(typeof(document.body)!="object" || document.body==null)return;
        var stamp = new Date();
        var id = stamp.valueOf();
        var windimensions = MiscUtils.getWindowDimensions();
        var width = 500;
        var height = 300;
        var titlebarheight = 20;
        var left = (windimensions.width/2)-(width/2);
        var top = (windimensions.height/2)-(height/2);
        var boxstyle = "position:absolute;"
                      +"border:1px solid #ccc;"
                      +"height:"+height+"px;"
                      +"width:"+width+"px;"
                      +"top:"+top+"px;"
                      +"left:"+left+"px;"
                      +"z-index:1;"
                  ;
        var box = DOMUtils.newElement("div",{
            "id" : "debugBox_"+id,
            "style" : boxstyle
        });
        var titlebar = DOMUtils.newElement("div",{
            "style" : "background-color:#ccc;height:"+titlebarheight+"px;color:#666;"
        });
        var closelink = DOMUtils.newElement("a",{
            "href" : "javascript:Debug.killFloatingDebugBox('debugBox_"+id+"');",
            "style" : "color:#fff;text-decoration:none;",
            "title" : "Close"
        });
        closelink.appendChild(document.createTextNode("[X] "));
        titlebar.appendChild(closelink);
        titlebar.appendChild(document.createTextNode(title));
        var textbox = DOMUtils.newElement("div",{
            "style" : "background-color:#fff;height:"+(height-titlebarheight)+"px;overflow:auto;"
        });
        textbox.innerHTML = text;
        box.appendChild(titlebar);
        box.appendChild(textbox);
        document.body.appendChild(box);
    },
    killFloatingDebugBox : function(id){
        var box = document.getElementById(id);
        box.parentNode.removeChild(box);
    },
    debug : function (message, colour){
        if(!Debug.on) return;
        if(typeof(document.body)!="object" || document.body==null)return;
        if(!colour) var colour = "#000000";
        var div;
        var a;
        if(!eval(div = document.getElementById("message_div"))){
            div = DOMUtils.newElement("div", {
                'id':"message_div",
                'style' : "width:200px; overflow:auto;padding:5px;text-align:left;position:absolute;top:20px;left:0px;border:1px solid #8a8a8a; background-color:#fed6c6;"
            });
            a = DOMUtils.newElement("a", {
                "href" : "javascript:Debug.debug(null);",
                "title" : "Close"
            });
            a.appendChild(document.createTextNode("Clear"));
            div.appendChild(a);
            document.body.appendChild(div);
        }
        if(message == null){
            while(div.hasChildNodes()){
                div.removeChild(div.firstChild);
            }
            div.parentNode.removeChild(div);
        }else{
            var p = DOMUtils.newElement("p", {
                'style' : "color:"+colour+";"
            });
            //var text = document.createTextNode(message);
            p.innerHTML = message;
            div.appendChild(p);
        }
    },
    showProperties : function(object, like){
	var properties = "";
        var r =  RegExp('.*'+like+'.*', "i");

        for(i in object){
        if(!like || r.test(i)){
            properties += i+": ";
            try{
              properties += object[i]+"<hr/>\n";
            }catch(exception){
              properties += "[CANNOT DISPLAY]<hr/>\n";
            }
        }
        }
         var title;
        if(typeof(object.nodeName)!="undefined"){
            title = object.nodeName+" properties";
        }else{
            title = object+" properties";
        }
        if(like) title += " like '"+like+"'";
        Debug.floatingDebugBox(title, properties);
    }
};

