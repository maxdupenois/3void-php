var IFrameUploaderManager ={
    uploaders : new Map(),
    showFrames : false,
    add : function(key, uploader){
        if(this.uploaders.hasKey(key)){
            throw Exception(key+" key already exists");
        }
        this.uploaders.add(key, uploader);
    },
    get : function(key){
        if(this.uploaders.hasKey(key)){
            return this.uploaders.get(key);
        }else{
            return null;
        }
    },
    addToQueue : function(key){
        if(this.uploaders.hasKey(key)){
            this.uploaders.get(key).addToQueue();
        }
    },
    finishedUpload : function(key, id, result, fileLocation, msg){
        if(this.uploaders.hasKey(key)){
            this.uploaders.get(key).finishedUpload(id, result, fileLocation, msg);
        }
    }
};
IFrameUploader.constructor = IFrameUploader;
function IFrameUploader(key, parentElement, action, oneAtATime, styledUpload, afterUploadFunction){
    this.oneAtATime = oneAtATime;
    this.styledUpload = styledUpload;
    this.action = action;
    this.parentElement = parentElement;
    this.key = key;
    this.initialised = false;
    this.afterUploadFunction = afterUploadFunction;
    this.currentlyUploading = false;
    this.otherFormElements = new Array();
    IFrameUploaderManager.add(key, this);
}
IFrameUploader.prototype.addFormElement = function (element){
    this.otherFormElements[this.otherFormElements.length] = element;
}
IFrameUploader.prototype.initialise = function(){
    if(this.initialised) return;
    this.form = DOMUtils.newElement("form", {
       "action": this.action,
       "method": "post",
       "name": "upload_"+this.key,
       "enctype": "multipart/form-data",
       "class": "file_uploader"
    });
    if(this.styledUpload){
        var fakeinputdiv = DOMUtils.newElement("div", {
            "style" : "position: absolute;top: 0px;left: 0px;z-index: 1;"
        });
        this.fakefileinput = DOMUtils.newElement("input", {
            "type" : "text",
            "name" : "fake",
            "class" : "file_input "+this.key
        });
        /*var fakebrowsebutton = DOMUtils.newElement("img", {
            "alt" : "Browse",
            "title" : "Browse",
            "class" : "file_browse",
            "src" : "#"
        });*/
        var fakebrowsebutton = DOMUtils.newElement("span", {
            "title" : "Browse",
            "class" : "file_browse "+this.key
        });
        fakebrowsebutton.appendChild(document.createTextNode("Browse"));
    }
    var realinputdiv = DOMUtils.newElement("div", {
        "class" : "file_input_container "+this.key
    });
    var currentfile = DOMUtils.newElement("input", {
        "type" : "file",
        "name" : "currentfile"
    });

    if(this.styledUpload){
        currentfile["style"]["position"] = "relative";
       /* currentfile["style"]["-moz-opacity"] = "0";
        currentfile["style"]["filter"] = "alpha(opacity: 0)";
        currentfile["style"]["opacity"] = "0";*/
        currentfile["style"]["zIndex"] = "2";

        DOMUtils.setOpacity(currentfile, 0);

    }
    var fileid = DOMUtils.newElement("input", {
        "type" : "hidden",
        "name" : "fileid",
        "value" : "0"
    });
    var uploaderkey = DOMUtils.newElement("input", {
        "type" : "hidden",
        "name" : "uploaderkey",
        "value" : this.key
    });
    this.loadingbar = DOMUtils.newElement("span", {
        "id" : "loading_bar_"+this.key,
        "class" : "loading_bar "+this.key
    });

    this.button = DOMUtils.newElement("a", {
        "href" : "javascript:IFrameUploaderManager.addToQueue('"+this.key+"');",
        "class" : "upload_button "+this.key,
        "title" : "upload"
    });
    this.list = DOMUtils.newElement("ul", {
        "id" : "upload_list_"+this.key,
        "class" : "upload_list "+this.key
    });
    if(IFrameUploaderManager.showFrames){
        this.iframes = DOMUtils.newElement("div", {
            "id" : "iframe_container_"+this.key,
            "style" : "display:block;height:200px;width:600px;overflow:scroll;"
        });
    }else{
        this.iframes = DOMUtils.newElement("div", {
            "id" : "iframe_container_"+this.key,
            "style" : "display:none;height:0px;width:0px;overflow:hidden;"
        });
    }

    if(this.styledUpload){
        //Put together fake file browser:
        fakeinputdiv.appendChild(this.fakefileinput);
        fakeinputdiv.appendChild(fakebrowsebutton);
        currentfile["partnerInput"] = this.fakefileinput;
        currentfile["fakeButton"] = fakebrowsebutton;
        fakebrowsebutton["fileinput"] = currentfile;
        fakebrowsebutton["fakefileinput"] = this.fakefileinput;
        this.fakefileinput["partnerInput"] = currentfile;
        currentfile.onchange = currentfile.onmouseout = function () {
            this["partnerInput"].value = this.value;
        };
        this.fakefileinput["onclick"] = function(){
            this["partnerInput"].click();
        };
        /*this.fakefileinput["onmouseover"] = function(){
            this["fakeButton"]["className"] = "file_browse mouseover";
        };
        this.fakefileinput["onmouseout"] = function(){
            this["fakeButton"]["className"] = "file_browse";
        };*/
        fakebrowsebutton["onmouseover"] = function(){
            this["className"] = "file_browse mouseover";
        };
        fakebrowsebutton["onmouseout"] = function(){
            this["className"] = "file_browse";
        };
        fakebrowsebutton["onclick"] = function(){
            this["fileinput"].click();
        };

        currentfile["onmouseover"] = function(){
            this["fakeButton"]["className"] = "file_browse mouseover";
        };
        currentfile["onmouseout"] = function(){
            this["fakeButton"]["className"] = "file_browse";
        };
    }
    realinputdiv.appendChild(currentfile);
    if(this.styledUpload){
        realinputdiv.appendChild(fakeinputdiv);
    }

    this.button.appendChild(document.createTextNode("Upload"));
    this.form.appendChild(realinputdiv);
    this.form.appendChild(fileid);
    this.form.appendChild(uploaderkey);

    for(var i = 0; i<this.otherFormElements.length; i++){
        this.form.appendChild(this.otherFormElements[i]);
    }

    this.form.appendChild(this.button);
    this.form.appendChild(this.loadingbar);
    this.parentElement.appendChild(this.form);
    this.parentElement.appendChild(this.list);
    document.body.appendChild(this.iframes);

    if(this.styledUpload){
        //Move fake
        var pos = DOMUtils.positionOf(currentfile);
        fakeinputdiv["style"]["left"] = pos["left"]+"px";
        fakeinputdiv["style"]["top"] = pos["right"]+"px";
        //scale real browser
        //alert(fakeinputdiv.offsetWidth);
        currentfile["size"] = fakeinputdiv.offsetWidth/10;
    }
    this.initialised = true;
};


 IFrameUploader.prototype.addToQueue = function(){
    if(!this.initialised) return;
    var currentfile = this.form['currentfile']['value'];
    if(currentfile=="")return;
    if(this.currentlyUploading && this.oneAtATime) return;
    this.currentlyUploading = true;

    this.button["className"] = "upload_button uploading "+this.key;
    if(this.styledUpload){
        this.form["currentfile"]["partnerInput"]["className"] = "file_input uploading "+this.key;
        this.form["currentfile"]["fakeButton"]["className"] = "file_browse uploading "+this.key;
    }
    this.loadingbar["className"] =  "loading_bar uploading "+this.key;

    var id = this.form['fileid']['value'];
    var li = DOMUtils.newElement('li',{'id':"file_"+this.key+"_"+id, 'class':"file_loading "+this.key});
    var span_error = DOMUtils.newElement('span',{'id':"file_error_"+this.key+"_"+id, 'class':"file_upload_error "+this.key});
    li.appendChild(document.createTextNode(currentfile+" "));
    li.appendChild(span_error);
    this.list.appendChild(li);
    var iframeName = "upload_target_"+this.key+"_"+id;

    var iframe = DOMUtils.newElement('iframe',{'id':iframeName, 'name' :iframeName, 'src':'#'});

    if(IFrameUploaderManager.showFrames){
        iframe['style']['width']="600px";
        iframe['style']['height']="200px";
        iframe['style']['border']="1px solid #ccc";
    }else{
        iframe['style']['width']="0px";
        iframe['style']['height']="0px";
        iframe['style']['border']="0px solid #fff";
    }
    this.iframes.appendChild(iframe);
    this.form['target'] = iframeName;
    this.form.submit();
    if(this.oneAtATime){
        this.button["disabled"] = "disabled";
        this.form["currentfile"]["disabled"] = "disabled";
    }
    this.incrementId();
};
IFrameUploader.prototype.incrementId = function(){
    if(!this.initialised) return;
    this.form['fileid']['value'] = parseInt(this.form['fileid']['value'], 10)+1;
};

IFrameUploader.prototype.finishedUpload = function(id, result, fileLocation, msg){
    if(!this.initialised) return;
    this.currentlyUploading = false;
    if(this.oneAtATime){
        this.button["disabled"] = "";
        this.form["currentfile"]["disabled"] = "";
    }
    this.button["className"] = "upload_button "+this.key;
    this.loadingbar["className"] =  "loading_bar "+this.key;

    if(this.styledUpload){
        this.form["currentfile"]["partnerInput"]["className"] = "file_input "+this.key;
        this.form["currentfile"]["fakeButton"]["className"] = "file_browse "+this.key;
    }


    var fileListing = document.getElementById("file_"+this.key+"_"+id);
    fileListing['className'] = (result==1?"file_loading file_succeeded "+this.key:"file_loading file_failed "+this.key);
    var fileError = document.getElementById("file_error_"+this.key+"_"+id);
    fileError.appendChild(document.createTextNode(msg));
    this.iframes.removeChild(document.getElementById("upload_target_"+this.key+"_"+id));
    if(typeof(this.afterUploadFunction)=="function"){
        this.afterUploadFunction(result, fileLocation, msg);
    }
};
