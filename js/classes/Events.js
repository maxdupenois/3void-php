/*
 * Event manager, use as
 * Event event = Event(id, element, eventtype, func, propogate, properties);
 * Events.add(event);
 */

//var Events_required = new Array();
//Events_required[Events_required.length] = {'check':'ArrayUtils', 'file':'ArrayUtils'};
//Events_required[Events_required.length] = {'check':'Utils', 'file':'Utils'};
//
//if(typeof(JSPath)=="undefined") var JSPath = "";
//if(typeof(script_inc)=="undefined")  var script_inc = "";
//for(var i = 0, max_i=Events_required.length; i< max_i; i++){
//	if(eval("typeof("+Events_required[i]['check']+")") == "undefined"){
//		script_inc = '<script type="text/javascript" language="javascript"';
//		script_inc += ' src="'+JSPath+Events_required[i]['file']+'.js"></script>';
//		document.write(script_inc);
//	}
//	//if(eval("typeof("+Events_required[i]['check']+")")=="undefined")
//	//			throw Events_required[i]['file']+" not found.";
//}

var Events = {
	add : function(evt){
		if(!evt.isInstance("Event")) throw "Object not an Event.";
		if(EventList.getById(evt.id)!=null)  
			throw "Event id '"+evt.id+"' already exists.";
		if(EventList.getByElementAndType(evt.element, evt.eventtype).length==0){
			evt.element[evt.eventtype] = function(e){Events.execute(e)};
		}
		EventList.add(evt);
	},
	remove : function(id){
		if(EventList.getById(id)==null) return;
		EventList.remove(id);
	},
	removeByEvent : function(id){
		if(!evt.isInstance("Event")) throw "Object not an Event.";
		if(EventList.getById(evt.id)==null) return;
		EventList.remove(evt);
	},
	execute : function(e, target_element, original_element){
		if(!e) var e = window.event;
		if(!target_element){
			if(e.currentTarget && e.currentTarget== window) target_element = window;
			else if (e.target) target_element = e.target;
			else if (e.srcElement) target_element = e.srcElement;
			if (target_element.nodeType == 3) // defeat Safari bug
				target_element = target_element.parentNode;
		}
		if(!original_element) var original_element = target_element;


		var type = "on"+e.type;

		var events = EventList.getByElementAndType(target_element, type);

		var propogate = true;
		var evt;
		for(var i = 0, max_i=events.length; i< max_i; i++){
			evt = events[i];
			//If one event cancels then we obey it
			if(!evt.propogate) propogate = false;
			evt.func(e, evt.properties, target_element, original_element);
		}
		//handle propogation ourselves
		e.cancelBubble = true;
		if (e.stopPropagation) e.stopPropagation();
		
		if(propogate && target_element!=window) {
			var parent = target_element.parentNode;
			if(parent != null) Events.execute(e, parent, original_element);
		}

	},
        getMousePositionFromEvent : function(e){
            /*From QuirksBlog*/
            var posx = 0;
            var posy = 0;
            if (e.pageX || e.pageY){
                posx = e.pageX;
                posy = e.pageY;
            }else if (e.clientX || e.clientY){
                posx = e.clientX + document.body.scrollLeft
                + document.documentElement.scrollLeft;
                posy = e.clientY + document.body.scrollTop
                + document.documentElement.scrollTop;
            }
            return {'x':posx, 'y':posy};
        }
};


var EventList = {
	eventlisting : new Array(),
	add : function(evt){
		if(!evt.isInstance("Event")) throw "Object not an Event.";
		this.eventlisting[this.size()] = evt;
	},
	removeByEvent : function(evt){
		if(!evt.isInstance("Event")) throw "Object not an Event.";
		this.eventlisting = this.eventlisting.removeElement(evt);
	},
	remove : function(id){
		var evt = this.getById(id);
		if(evt == null) throw "Event '"+id+"' not found.";
		this.removeByEvent(evt);
	},
	size : function(){
		return this.eventlisting.length;
	},
	getByEventType : function(type){
		var events = new Array();
		for(var i = 0, max_i=this.size(); i< max_i; i++){
			if(this.eventlisting[i].eventtype == type) 
			events[events.length] = this.eventlisting[i];
		}
		return events;
	},
	getById : function(id){
		var evt = null;
		for(var i = 0, max_i=this.size(); i< max_i && evt==null; i++){
			if(this.eventlisting[i].id == id) 
						evt = this.eventlisting[i];
		}
		return evt;
	},
	getByElement : function(element){
		var events = new Array();
		for(var i = 0, max_i=this.size(); i< max_i; i++){
			if(this.eventlisting[i].element == element) 
			events[events.length] = this.eventlisting[i];
		}
		return events;
	},
	getByElementAndType : function(element, type){
		var events = new Array();
		for(var i = 0, max_i=this.size(); i< max_i; i++){
			if(this.eventlisting[i].element == element &&
			this.eventlisting[i].eventtype == type) 
			events[events.length] = this.eventlisting[i];
		}
		return events;
	}
};



Event.constructor = Event;
function Event(id, element, eventtype, func, propogate, properties){
	if(typeof(func) != "function") throw "'"+func+"' is not a function.";
	if(element==null) throw "Element is invalid.";
	if(!properties) var properties = new Object();
	this.id = id;
	this.element = element;
	this.eventtype = eventtype;
	this.properties = properties;
	this.propogate = propogate;
	this.func = func;
}