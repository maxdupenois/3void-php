var FolderListings = {
    idCounter : -1,
    folderListingMap : new Map(),
    add : function(folderListing){
        if(!folderListing.isInstance("FolderListing")) throw "Object not an FolderListing.";
        FolderListings.idCounter++;
        folderListing.id = FolderListings.idCounter;
        FolderListings.folderListingMap.add(FolderListings.idCounter, folderListing);
    },
    size : function(){
        return this.folderListingMap.size();
    },
    remove : function(folderListing){
        if(!folderListing.isInstance("FolderListing")) throw "Object not an FolderListing.";
        FolderListings.folderListingMap.remove(folderListing.id);
    },
    removeById : function(id){
        var folderListing = FolderListings.get(id);
        if(folderListing == null) throw "FolderListing '"+id+"' not found.";
        FolderListings.remove(folderListing);
    },
    get : function(id){
        return FolderListings.folderListingMap.get(id);
    },
    gotData : function(result, parameters){
        FolderListings.get(parameters["id"]).gotData(result);
    },
    expandFolder : function(listingId, folderId){
         FolderListings.get(listingId).expandFolder(folderId);
    },
    collapseFolder : function(listingId, folderId){
         FolderListings.get(listingId).collapseFolder(folderId);
    }
};
FolderListing.constructor = FolderListing;
function FolderListing(container, ajaxUrl){
    this.ajaxUrl = ajaxUrl;
    this.container = container;
    this.id = -1;
    this.folders = new Map();
    this.expandedFolders = new Array();
    FolderListings.add(this);
}
FolderListing.prototype.fill = function(){
    var query = new HTMLHttpQuery(this.ajaxUrl, "GET");
    query.successFunction = FolderListings.gotData;
    query.parameters = {
        "id":this.id
    };
    query.run();
};
FolderListing.prototype.gotData = function(data){
    DOMUtils.removeChildren(this.container);
    var foldersArr = DOMUtils.getElementsByClassName(data, 'folder');
    var folder;
    var children;
    var child;
    var expanded;
    this.folders = new Map();
    for(var i= 0; i < foldersArr.length; i++){
        folder = foldersArr[i];
        this.folders.add(folder['id'], folder);
        expanded = this.expandedFolders.contains(folder['id']);
        children = folder.childNodes;
        folder.insertBefore(this.createFolderExpander(folder['id'], expanded), children[0]);
        if(!expanded){
            for(var j=0; j < children.length; j++){
                child = children[j];
                if(child.className == "folder_listing"){
                    child['style']['display'] = "none";
                }
            }
        }
    }
    this.container.appendChild(data);
};
FolderListing.prototype.createFolderExpander = function(folderId, expanded){
    if(expanded){
        return DOMUtils.newElement("a", {
            "class":"folder_expander_expanded",
            "href":"javascript:FolderListings.collapseFolder('"+this.id+"', '"+folderId+"');"
        });
    }else{
        return DOMUtils.newElement("a", {
            "class":"folder_expander_collapsed",
            "href":"javascript:FolderListings.expandFolder('"+this.id+"', '"+folderId+"');"
        });
    }
};
FolderListing.prototype.expandFolder = function(folderId){
    var folder = this.folders.get(folderId);
    this.expandedFolders.push(folderId);
    var children = folder.childNodes;
    for(var j=0; j < children.length; j++){
        child = children[j];
        if(child.className == "folder_listing"){
            child['style']['display'] = "block";
        }
        if(child.className == "folder_expander_collapsed"){
            child["href"] = "javascript:FolderListings.collapseFolder('"+this.id+"', '"+folderId+"');"
            child["className"] = "folder_expander_expanded";
        }
    }
};
FolderListing.prototype.collapseFolder = function(folderId){
    var folder = this.folders.get(folderId);
    this.expandedFolders.removeElement(folderId);
    var children = folder.childNodes;
    for(var j=0; j < children.length; j++){
        child = children[j];
        if(child.className == "folder_listing"){
            child['style']['display'] = "none";
        }
        if(child.className == "folder_expander_expanded"){
            child["href"] = "javascript:FolderListings.expandFolder('"+this.id+"', '"+folderId+"');"
            child["className"] = "folder_expander_collapsed";
        }
    }
};
