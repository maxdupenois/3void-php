// JavaScript Document

Array.prototype.contains = function(element){
    return (this.indexOf(element)>-1);
};
Array.prototype.indexOf = function(element){
    var i = this.length-1;
    var index = -1;
    while(index == -1 && i >= 0){
        if(this[i] == element){
            index = i;
        }
        i--;
    }
    return index;
};

Array.prototype.moveElement = function(element, pos){
    var i = this.length-1;
    var origIndex = null;
    while(origIndex == null && i >= 0){
        if(this[i] == element){
            origIndex = i;
        }
        i--;
    }
    var	newArray = this;
    if(origIndex != null && pos != origIndex){
        newArray = newArray.removeElement(element);
        newArray = newArray.insertElement(element, pos);
    }
    return newArray;
};


Array.prototype.insertElement = function(element, pos){
    var arr = new Array(this.length+1);
    var i = 0;
    //alert("Array: "+array+"\nElement: "+element+"\npos:"+pos);
    if(pos >= this.length){
        while(i <= this.length-1){
            arr[i] = this[i];
            i++;
        }
        arr[this.length] = element;
    }else{
        while(i <= this.length-1){
            
            if(i<pos){
                arr[i] = this[i];
            }else if(i == pos){
                arr[i] = element;
                arr[i+1] = this[i];
            }else if(i > pos){
                arr[i+1] = this[i];
            }
            //alert("Inserting: "+arr);
            i++;
        }
    }
    return arr;
};
Array.prototype.removeElement = function(element){
    var index = this.indexOf(element);
    if(index<0) return this;
    return this.removeByIndex(index);
};

Array.prototype.removeByIndex = function(index){
	i = 0;
	var arr = new Array(this.length-1);
    while(i <= this.length-1){
        if(i<index){
            arr[i] = this[i];
        }else if(i > index){
            arr[i-1] = this[i];
        }
        i++;
    }
    return arr;
};
