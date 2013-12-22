/* 
 * REQUIRES: DOMUtils.js, ArrayExt.js, Map.js
 */
function ImageSlideShowManager(){}
ImageSlideShowManager.list = new Map();
ImageSlideShowManager.add = function(name, slideShow){
    var index = ImageSlideShowManager.list.add(name, slideShow);
    if(index < 0) alert("Slide Show with name '"+name+"' already exists.");
}
ImageSlideShowManager.remove = function(name){
    ImageSlideShowManager.list.remove(name);
}
ImageSlideShowManager.get = function(name){
    return ImageSlideShowManager.list.get(name);
}
ImageSlideShowManager.timeout = function(name, forwards){
    var slideshow = ImageSlideShowManager.list.get(name);
    if(forwards){
        slideshow.next();
    }else{
        slideshow.previous();
    }
}
ImageSlideShowManager.fadeTimeout = function(name, fadeIn){
    var slideshow = ImageSlideShowManager.list.get(name);
    slideshow.fade();
}

function ImageSlideShow(name, element, defaultImageSrc, time, width, height){
    if(!width || !height) {
		this.width = -1;
		this.height = -1;
		this.fixedDimensions = false;
	}else{
		this.width = width;
		this.height = height;
		this.fixedDimensions = true;
	}
	
	this.name = name;
    this.imageList = new Array();
    this.random = false;
    this.defaultImage = new Image();
    this.defaultImage.src = defaultImageSrc;
    this.element = element;
	if(this.fixedDimensions){
		this.element.style['width'] = width+"px";
		this.element.style['height'] = height+"px";
	}
	this.overImage = DOMUtils.newElement('img', {"id":"slideshow_"+name+"_dummyImage"});
	this.overImage.style['display'] = "none";
	this.overImage.style['position'] = "absolute";
	this.currentImage = this.defaultImage;
	this.nextImage = this.defaultImage;
	this.element.parentNode.appendChild(this.overImage);
    this.time = time*1000;
    this.running = false;
    this.forwards = true;
    this.currentIndex = 0;
	this.opacity = 1;
	ImageSlideShowManager.add(name, this);
    this.add = function(imageSrc){
        var img = new Image();
        img.src = imageSrc;
        this.imageList.push(img);
    };
    this.goBackward = function(){
        this.forwards = false;
    };
    this.goForward = function(){
        this.forwards = true;
    };
    this.setRandom = function(r){
        this.random = r;
    };
    this.remove = function(imageSrc){
        var index = getIndexBySrc(imageSrc);
        this.imageList = this.imageList.removeByIndex(index);
    };
    this.start = function(){
        if(this.random) this.randomise();
        this.running = true;
        this.run();
    }
    this.stop = function(){
        this.running = false;
    }
    this.run = function(){
        var f = this.forwards;
        var n = this.name;
        if(this.running)setTimeout(function(){ImageSlideShowManager.timeout(n, f);}, this.time);
    }
    this.next = function(){
        if(this.imageList.length < 1) return;
        if(this.currentIndex>(this.imageList.length-1) || this.currentIndex<0){
            this.currentIndex = 0;
			if(this.random)  this.randomise();
        }
        this.imageChange(this.imageList[this.currentIndex]);
        this.currentIndex++;
    };
    this.previous = function(){
        if(this.imageList.length < 1) return;
        if(this.currentIndex>(this.imageList.length-1) || this.currentIndex<0){
            this.currentIndex = this.imageList.length-1;
			if(this.random)  this.randomise();
        }
        this.imageChange(this.imageList[this.currentIndex]);
        this.currentIndex--;
    };
    this.randomise = function(){
        var dummyList = new Array();
        while(this.imageList.length > 0){
            var index = Math.floor(Math.random()*this.imageList.length);
            dummyList.push(this.imageList[index]);
            this.imageList = this.imageList.removeByIndex(index);
        }
        this.imageList = dummyList;
    };
    this.imageChange = function(img){
		this.nextImage = img;
		this.element.src = img.src;
		this.overImage.src = this.currentImage.src;
		
		var pos = DOMUtils.positionOf(this.element);
		
		this.overImage.style['left'] = pos[0]+"px";
		this.overImage.style['top'] = pos[1]+"px";
		if(this.fixedDimensions){
			this.overImage.style['width'] = width+"px";
			this.overImage.style['height'] = height+"px";
		}else{
			this.overImage.style['height'] = this.currentImage.height+"px";
			this.overImage.style['width'] = this.currentImage.width+"px";
		}
		DOMUtils.setOpacity(this.overImage, 1);
		this.overImage.style['display'] = "block";
		this.fade();
    };
	
	this.fade = function(){
		if(this.opacity > 0 ){
			this.opacity -= 0.1;
			if(this.opacity < 0) this.opacity = 0;
			DOMUtils.setOpacity(this.overImage, this.opacity);
		}
		if(this.opacity == 0){
			this.currentImage = this.nextImage;
			this.opacity = 1;
			this.run();
		}else{
			var n = this.name;
			setTimeout(function(){ImageSlideShowManager.fadeTimeout(n);}, 100);
		}
    };
	
    this.getIndex = function(img){
  	var index = -1;
	for(var i = 0, len = this.imageList.length; index < 0 && i < len; i++){
		if(this.imageList[i] == img) index = i;
	}
        return index;
    };
    this.getIndexBySrc = function(src){
  	var index = -1;
	for(var i = 0, len = this.imageList.length; index < 0 && i < len; i++){
		if(this.imageList[i].src == src) index = i;
	}
        return index;
    };

}