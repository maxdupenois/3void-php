// JavaScript Document
var Galleries = {
	galleryListing : new Array(),
	add : function(gallery){
		if(!gallery.isInstance("Gallery")) throw "Object not an Gallery.";
		if(this.getById(gallery.id)!=null)  throw "Gallery id already exists.";
		this.galleryListing[this.size()] = gallery;
	},
	size : function(){
		return this.galleryListing.length;
	},
	removeByGallery : function(gallery){
		if(!gallery.isInstance("Gallery")) throw "Object not an Gallery.";
		this.galleryListing = this.galleryListing.removeElement(gallery);
	},
	remove : function(id){
		var gallery = this.getById(id);
		if(gallery == null) throw "Gallery '"+id+"' not found.";
		this.removeByGallery(gallery);
	},
	getById : function(id){
		var gallery = null;
		for(var i = 0, max_i=this.size(); i< max_i && gallery==null; i++){
			if(this.galleryListing[i].id == id) 
						gallery = this.galleryListing[i];
		}
		return gallery;
	},
	close : function(evt, properties){
		Galleries.getById(properties['id']).close();
	},
	resize : function(evt, properties){
		Galleries.getById(properties['id']).resize();
	},
	moveIntoView : function(evt, properties){
		Galleries.getById(properties['id']).moveIntoView();
	},
	gotData : function(result, properties){
		Galleries.getById(properties['id']).gotData(result);
	}
};
Gallery.constructor = Gallery;
function Gallery(id, no_img_src, load_img_src, ajax, base_img_path, img_query_string){
	this.id = id;
	this.ajax =ajax;
	this.base_img_path =(!base_img_path?"":base_img_path);
	this.img_query_string =(!img_query_string?"":img_query_string);
	this.background = DOMUtils.newElement("div", {'class':'background'});
	this.gallery = DOMUtils.newElement("div", {'id':'gallery'});
	this.gallery_container = DOMUtils.newElement("div", {'id':'gallery_container'});
	this.previous_button = DOMUtils.newElement("a",
							{'href':'javascript:Galleries.getById(\''+this.id+'\').previous();', 
							'title': "Previous",
							'class': "gallery previous"});
	this.previous_button.appendChild(document.createTextNode("<"));
	this.next_button = DOMUtils.newElement("a",
							{'href':'javascript:Galleries.getById(\''+this.id+'\').next();', 
							'title': "Next",
							'class': "gallery next"});
	this.next_button.appendChild(document.createTextNode(">"));
	this.gallery_image_container = DOMUtils.newElement("div", {'class':'gallery_image_container'});
	this.img  = DOMUtils.newElement("img",
							{'src':'', 
							'alt': "",
							'id': "currentImage",
							'class':"gallery_image"});
	this.clear  = DOMUtils.newElement("div",
							{'style':'clear:both;'});
	this.clear.appendChild(document.createTextNode(""));
	this.gallery_container.appendChild(this.previous_button);
	this.gallery_image_container.appendChild(this.img);
	this.gallery_container.appendChild(this.gallery_image_container);
	this.gallery_container.appendChild(this.next_button);
	this.gallery_container.appendChild(this.clear);
	this.gallery.appendChild(this.gallery_container);
	DOMUtils.setOpacity(this.background, .5);
	
	this.close();
	
	this.no_img = new Image();
	this.no_img.src = no_img_src;
	this.load_img = new Image();
	this.load_img.src = load_img_src;
	this.current_gallery = new Array();
	this.current_image = 0;
	
	Events.add(new Event("gallery_"+id+"_close_background", this.background, "onmousedown", Galleries.close, false, {'id':this.id}));
	Events.add(new Event("gallery_"+id+"_scroll", window, "onscroll", Galleries.moveIntoView, false, {'id':this.id}));
	Events.add(new Event("gallery_"+id+"_resize", window, "onresize", Galleries.resize, false, {'id':this.id}));

	document.body.appendChild(this.background);
	document.body.appendChild(this.gallery);
}
Gallery.prototype.resize = function(){
	if(!this.isOpen) return;
	var winDim = DOMUtils.getWindowDimensions();
	this.background['style']['height'] = winDim['height']+'px';
	//gallery['style']['height'] = (winDim['height']-100)+'px';
	this.gallery['style']['left'] = ((winDim['width']/2)-(400))+'px';
};
Gallery.prototype.close = function(){		
	this.background['style']['height'] = '0px';
	this.background['style']['display'] = 'none';
	this.gallery['style']['height'] = '0px';
	this.gallery['style']['display'] = 'none';
	this.isOpen = false;
};
Gallery.prototype.moveIntoView = function(){
	if(!this.isOpen) return;
	var scrollLoc = DOMUtils.getScrollLocation();
	this.background['style']['top'] = scrollLoc.y+"px";
	this.background['style']['left'] = scrollLoc.x+"px";
	//gallery['style']['top'] = (scrollLoc.y==0?50:scrollLoc.y+10)+"px";
};

Gallery.prototype.open = function(query){
	this.isOpen = true;
	this.resize();
	this.moveIntoView();
	this.background['style']['display'] = 'block';
	this.gallery['style']['height'] = '510px';
	this.img.src = this.load_img.src;
	this.gallery['style']['display'] = 'block';
	this.gallery.scrollIntoView();
	AjaxQueue.addQuery(this.ajax, query, "GET", null, Galleries.gotData, {'id':this.id});
			
};
Gallery.prototype.gotData = function(result){
	var images = result['images'];
	if(images != null){
		var image;
		var imgObj;
		this.current_gallery = new Array();
		for(var i = 0, mx = images.length; i < mx; i++){
			image = images[i];
			imgObj = new Image();
			imgObj.src = this.base_img_path+image['path']+this.img_query_string;
			this.current_gallery[this.current_gallery.length] =   {'src':imgObj.src, 'alt':image['alt']};
		}
		this.setCurrentImage(0);
	}
};

Gallery.prototype.next = function(){
	this.current_image++;
	this.setCurrentImage(this.current_image);
};
Gallery.prototype.previous = function(){
	this.current_image--;
	this.setCurrentImage(this.current_image);
};
Gallery.prototype.setCurrentImage = function(index){
	if(index < 0) index = this.current_gallery.length-1;
	if(index >= this.current_gallery.length) index = 0; 
	if(this.current_gallery.length>0) {
		var i = this.current_gallery[index];
		this.img['src'] = i['src'];
		this.img['alt'] = i['alt'];
		this.current_image = index;
	}else{
		this.img['src'] = this.no_img.src;
		this.img['alt'] = "No Image";
		current_image = 0;
	}
};
