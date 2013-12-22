Map.constructor = Map;

function Map(){
    this.mainMap = new Array();
    this.referenceMap = new Array();

}

Map.prototype.hasKey = function(key){
  return (this.getKeyIndex(key) > -1);
};

Map.prototype.getKeyIndex = function(keyObject){
  	var index = -1;
	for(var i = 0, len = this.referenceMap.length; index < 0 && i < len; i++){
		if(this.referenceMap[i] == keyObject) index = i;
	}
    return index;
};

Map.prototype.add= function(keyObject, object){
    var existingIndex = this.getKeyIndex(keyObject);
    if(existingIndex >= 0) return -1;
    this.referenceMap.push(keyObject);
    this.mainMap.push(object);
    return this.getKeyIndex(keyObject);
};
Map.prototype.get= function(keyObject){;
    var index = this.getKeyIndex(keyObject);
	if(index < 0) return null;
    return this.mainMap[index];
};
Map.prototype.remove = function(keyObject){
	var index = this.getKeyIndex(keyObject);
	if(index < 0) return;
	this.referenceMap = this.referenceMap.removeByIndex(index);
	this.mainMap = this.mainMap.removeByIndex(index);
};
Map.prototype.getKeys = function(){
	var array = new Array();
	for(var i = 0, len = this.referenceMap.length; i < len; i++) array.push(this.referenceMap[i]);
	return array;
};
Map.prototype.getValues = function(){
	var array = new Array();
	for(var i = 0, len = this.mainMap.length; i < len; i++) array.push(this.mainMap[i]);
	return array;
};
Map.prototype.clear= function(){
   this.mainMap = new Array();
   this.referenceMap = new Array();
};

Map.prototype.size= function(){
    return this.refCounter;
};
Map.prototype.getByIndex= function(i){
    return this.mainMap[i];
};

Map.prototype.debug= function(){
    if(typeof(Debug)!="undefined"){
        var text = "";
        for(var i = 0, len = this.referenceMap.length; i < len; i++){
            text += ("["+i+"] "+this.referenceMap[i]+" : "+this.mainMap[i]+" <hr/>\n");
        }
        Debug.floatingDebugBox("Map Contents", text);
    }

};



