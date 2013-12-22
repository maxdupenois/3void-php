/*
        Sets up the framework for the Http Request,
        e.g.


        var query = new HttpQuery("somwhere.html", "POST", "xml");
        var query = new HttpQuery("somwhere.html", "POST", "json");
        var query = new HttpQuery("somwhere.html", "POST", "html");
        var query = new HttpQuery("somwhere.html", "POST", "text");
        var query = new HttpQuery("somwhere.html", "POST", "javascript");

        //Can use this constructor type but it requires
        // ObjectExt.js to get inheritFrom method
        var query = new XMLHttpQuery("somwhere.html", "POST");
        var query = new JSONHttpQuery("somwhere.html", "POST");
        var query = new HTMLHttpQuery("somwhere.html", "POST");
        var query = new TextHttpQuery("somwhere.html", "POST");
        var query = new JavascriptHttpQuery("somwhere.html", "POST");

        // Post data
        query.post = "key=val&other=val2";

        // Maximum time allowed for the query to take
        query.timeout = 4000;

        //Function to call on successful completion of query
        query.successFunction = function(result, parameters){};

        //If supplied fills the second argument of the successFunction
        query.parameters = [Any object type or variable];

        // Function to call on error, first argument is a boolean indicating
        // Whether or not a connection could not be made even to the root of the
        // site
        query.errorFunction = function(connectionLost, message){};

        //Whether or not to retry if it fails
        query.retryOnFail = false;

        // Whether or not to add itself to the queue again after it's completed
        query.repeatOnCompletion = false;

        //Adds the query to the queue and if the queue is stopped, starts it
        query.run();

 */



XMLHttpContainer.prototype.constructor = XMLHttpContainer;
function XMLHttpContainer(){
    this.currCacheID = -1;
    if(window.ActiveXObject){
        //microsoft
        try {
            this.xmlHttpRequest = new ActiveXObject("Msxml2.XMLHttp");
        } catch (e) {
            try {
                this.xmlHttpRequest = new ActiveXObject("Microsoft.XMLHttp");
            } catch (e) {}
        }
    }else if(window.XMLHttpRequest){
        //mozilla opera etc
        this.xmlHttpRequest = new XMLHttpRequest();
    }
    this.setMimeAsXML = function(){
        //needed for some versions of mozilla
        if(window.XMLHttpRequest)
            this.xmlHttpRequest.overrideMimeType("text/xml");
    };
    this.setMimeAsPlain = function(){
        //needed for some versions of mozilla
        if(window.XMLHttpRequest &&
                typeof(this.xmlHttpRequest['overrideMimeType']) != "undefined")
            this.xmlHttpRequest.overrideMimeType("text/plain");
    };
    this.setMimeAsHTML = function(){
        //needed for some versions of mozilla
        if(window.XMLHttpRequest)
            this.xmlHttpRequest.overrideMimeType("text/html");
    };
}

HttpQuery.prototype.constructor=HttpQuery;
function HttpQuery(url, method, type){
    this.type = type.toLowerCase();
    this.url = url;
    this.timeout = 4000;
    this.method = (!method?"GET":method);
    this.post = null;
    this.successFunction = function(){};
    this.parameters = null;
    this.errorFunction = function(){};
    this.retryOnFail = false;
    this.repeatOnCompletion = false;
}

HttpQuery.prototype.run = function(){
    HttpRequestQueue.addQuery(this);
    if(!HttpRequestQueue.running) {
        HttpRequestQueue.start();
    }
};

JSONHttpQuery.prototype.constructor = JSONHttpQuery;
function JSONHttpQuery(url, method){
    //REQUIRES ObjectExt.js
    this.inheritFrom(HttpQuery);
    this.HttpQuery(url, method, "json");
}
XMLHttpQuery.prototype.constructor = XMLHttpQuery;
function XMLHttpQuery(url, method){
    //REQUIRES ObjectExt.js
    this.inheritFrom(HttpQuery);
    this.HttpQuery(url, method, "xml");
}
TextHttpQuery.prototype.constructor = TextHttpQuery;
function TextHttpQuery(url, method){
    //REQUIRES ObjectExt.js
    this.inheritFrom(HttpQuery);
    this.HttpQuery(url, method, "text");
}
JavascriptHttpQuery.prototype.constructor = JavascriptHttpQuery;
function JavascriptHttpQuery(url, method){
    //REQUIRES ObjectExt.js
    this.inheritFrom(HttpQuery);
    this.HttpQuery(url, method, "javascript");
}
HTMLHttpQuery.prototype.constructor = HTMLHttpQuery;
function HTMLHttpQuery(url, method){
    //REQUIRES ObjectExt.js
    this.inheritFrom(HttpQuery);
    this.HttpQuery(url, method, "html");
}


var RequestProcessor ={
    xmlHttpContainer : null,
    isRunningRequest : false,
    requestTimer : null,
    query : null,
    destroy : function(){
        RequestProcessor.xmlHttpContainer = null;
    },
    run : function(query){
        RequestProcessor.query = query;
        RequestProcessor.isRunningRequest = true;
        RequestProcessor.xmlHttpContainer = new XMLHttpContainer();
        
        var queryURL;
        var method;
        var post;
        var timeout;
        var type;

        queryURL = RequestProcessor.query.url;
        method = RequestProcessor.query.method;
        post = RequestProcessor.query.post;
        timeout = RequestProcessor.query.timeout;
        type = RequestProcessor.query.type;

        var stamp = new Date();
        var cacheid = stamp.valueOf(); //Math.round(100*Math.random()); // to prevent caching
        queryURL += (queryURL.search(/\?/g) == -1)? "?cacheid="+cacheid : "&cacheid="+cacheid;
        RequestProcessor.xmlHttpContainer.currCacheID = cacheid;
        RequestProcessor.xmlHttpContainer.xmlHttpRequest.open(method, queryURL, true);

        RequestProcessor.xmlHttpContainer.xmlHttpRequest.onreadystatechange = function(){
            RequestProcessor.stateChangeHandler();
        };
        RequestProcessor.requestTimer = setTimeout(function(){
            RequestProcessor.abort();
        }, timeout);

        if(post != null){
            RequestProcessor.xmlHttpContainer.xmlHttpRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        }
        if(post == null) post = "";

        if(type=="xml"){
            RequestProcessor.xmlHttpContainer.setMimeAsXML();
        }else if(type=="html"){
            RequestProcessor.xmlHttpContainer.setMimeAsHTML();
        }else{
            RequestProcessor.xmlHttpContainer.setMimeAsPlain();
        }
        RequestProcessor.xmlHttpContainer.xmlHttpRequest.send(post);
    },
    abort : function (){
        RequestProcessor.xmlHttpContainer.xmlHttpRequest.abort(); //aborts the request
        var errorFunction = RequestProcessor.query.errorFunction;
        if(typeof(errorFunction)==="function") errorFunction(true, "Request timed out");
        if(RequestProcessor.query.retryOnFail){
            HttpRequestQueue.addQuery(RequestProcessor.query);
        }
        HttpRequestQueue.loop();
    },
    stateChangeHandler : function (){ //handles the result of the query
        if (RequestProcessor.xmlHttpContainer.xmlHttpRequest.readyState == 4) {
//            Debug.floatingDebugBox("Returned", "Ready state 4 Status: "+RequestProcessor.xmlHttpContainer.xmlHttpRequest.status);
            var result = null;
            try{
                var query = RequestProcessor.query;
                if (RequestProcessor.xmlHttpContainer.xmlHttpRequest.status == 200)  {

                    clearTimeout(RequestProcessor.requestTimer); //clears the abort timer
                    if(this.xmlHttpContainer.xmlHttpRequest.responseText != ""){
//        Debug.floatingDebugBox("Returned", this.xmlHttpContainer.xmlHttpRequest.responseText);
                        try{
                            if(query.type == "json"){
                                result = RequestProcessor.json();
                            }else if(query.type == "xml"){
                                result = RequestProcessor.xml();
                            }else if(query.type == "text"){
                                result = RequestProcessor.text();
                            }else if(query.type == "javascript"){
                                result = RequestProcessor.javascript();
                            }else if(query.type == "html"){
                                result = RequestProcessor.html();
                            }
                        }catch(e){
                            var errorFunction = query.errorFunction;
                            if(typeof(errorFunction)==="function") errorFunction(false, "Failed to parse");
                            if(RequestProcessor.query.retryOnFail){
                                HttpRequestQueue.addQuery(RequestProcessor.query);
                            }
                            HttpRequestQueue.loop();
                            return;
                        }

                    }
                    var successFunction = query.successFunction;
                    var param = query.parameters;
                    if(typeof(successFunction) === 'function'){
                        if(param == null){
                            successFunction(result); //if the return function exists pass the result to it
                        }else{
                            successFunction(result, param);
                        }
                    }
                    if(query.repeatOnCompletion){
                        HttpRequestQueue.addQuery(query);
                    }
                    HttpRequestQueue.loop(); //loop queue
                } else {
                    clearTimeout(RequestProcessor.requestTimer); //clears the abort timer
                    var errorFunction = query.errorFunction;
                    if(typeof(errorFunction)==="function"){
                        errorFunction(false,  "Status returned as: "+
                            RequestProcessor.xmlHttpContainer.xmlHttpRequest.status );
                    }
                    if(RequestProcessor.query.retryOnFail){
                        HttpRequestQueue.addQuery(RequestProcessor.query);
                    }
                    HttpRequestQueue.loop(); //loop queue
                }
            }catch(e){
            //debug("Exception Caught: "+e);
            }
        }
    },
    json : function(){
        var json_text = RequestProcessor.xmlHttpContainer.xmlHttpRequest.responseText.replace("�", "\xA3"); //deals with the � sign
        /*if the json parser is available, use that instead of the unsafe eval function*/
        return (typeof(parseJSON) == "function")?json_text.parseJSON() : eval("(" + json_text + ")");
    },
    xml : function(){
        if(window.ActiveXObject){
            //microsoft
            var xmlDocument = new ActiveXObject("Microsoft.XMLDOM");
            xmlDocument.loadXML(RequestProcessor.xmlHttpContainer.xmlHttpRequest.responseText);
            return xmlDocument.documentElement; // deal with microsofts dodgy xml handling
        }else{
            return RequestProcessor.xmlHttpContainer.xmlHttpRequest.responseXML.documentElement;
        }
    },
    text : function(){
        return RequestProcessor.xmlHttpContainer.xmlHttpRequest.responseText;
    },
    javascript : function(){
        return eval(RequestProcessor.xmlHttpContainer.xmlHttpRequest.responseText);
    },
    html : function(){
        var trimmed = RequestProcessor.xmlHttpContainer.xmlHttpRequest.responseText.replace(/^\s*/, "").replace(/\s*$/, "");
//        Debug.floatingDebugBox("Returned", trimmed);
        var dummyLayer = DOMUtils.newElement("div",
                                        {'id': "dummy"});
        dummyLayer.innerHTML = trimmed;
        return dummyLayer.firstChild;
    }
};




var HttpRequestQueue = {
    queue : new Array(),
    runTimeout : null,
    running : false,
    stopping : true,
    /*
     * function to add a query to the queue, 
     */
    addQuery : function(httpQuery){
        this.queue.push( httpQuery);
//        Debug.debug("Query added "+httpQuery.url);
    },
     /*
     * Starts the running method, used so we can restart
     */
    start : function(){
        this.stopping = false;
        this.run();
    },
     /*
     * Loops through the queue running each query it finds
     */
    run : function(){
        this.running = true;
        if(!this.stopping){
            if(this.queue.length>0){
                var query = this.queue[0];
                //clears the running timeout to stop stacking while trying to run a query
                if(this.runTimeout != null) clearTimeout(this.runTimeout);
                this.currQuery = query;
                RequestProcessor.run(query); //runs the query
            }else{
                this.runTimeout = setTimeout(function(){
                    HttpRequestQueue.run();
                }, 50); // loops until it finds a query to run
            }
        }else{
            this.running = false;
        }
    },
     /*
     * Asks the queue to stop
     */
    stop : function(){
        this.stopping = true;
    },
    loop : function(){
        if (this.queue[0] != null) {
            this.queue.shift(); // shifts the array to run the next query
        }
        this.run();
    }
};
