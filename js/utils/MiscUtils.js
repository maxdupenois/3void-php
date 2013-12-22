// JavaScript Document
var MiscUtils = {
    getScrollLocation : function(){
	//QuirksMode http://www.jr.pl/www.quirksmode.org/viewport/compatibility.html:
	if (self.pageYOffset) // all except Explorer
	{
		x = self.pageXOffset;
		y = self.pageYOffset;
	}
	else if (document.documentElement && document.documentElement.scrollTop)
		// Explorer 6 Strict
	{
		x = document.documentElement.scrollLeft;
		y = document.documentElement.scrollTop;
	}
	else if (document.body) // all other Explorers
	{
		x = document.body.scrollLeft;
		y = document.body.scrollTop;
	}

	return {'y': y, 'x':  x};
    },
    
    getWindowDimensions : function(scrollbarW, scrollbarH){
        if(!scrollbarW) var scrollbarW = true;
        if(!scrollbarH) var scrollbarH = false;


        if(window.innerWidth){
                winW = window.innerWidth;
                winH = window.innerHeight;
                if(scrollbarW) winW-=16;
                if(scrollbarH) winH-=16;
         }
         if(document.body.offsetWidth){
                winW = document.body.offsetWidth;
                winH = document.body.offsetHeight;
                if(scrollbarW) winW-=20;
                if(scrollbarH) winH-=20;
         }
         return {'width':winW, 'height':winH};
    }
    
};


