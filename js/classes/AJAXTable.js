var AJAXTables = {
    idCounter : -1,
    ajaxTableMap : new Map(),
    add : function(ajaxTable){
        if(!ajaxTable.isInstance("AJAXTable")) throw "Object not an AJAXTable.";
        AJAXTables.idCounter++;
        ajaxTable.id = AJAXTables.idCounter;
        AJAXTables.ajaxTableMap.add(ajaxTable.id, ajaxTable);
    },
    get : function(id){
        return AJAXTables.ajaxTableMap.get(id);
    },
    fillDataRecieved : function(jsonData, parameters){
        AJAXTables.get(parameters['id']).fillDataRecieved(jsonData);
    }
};

AJAXTable.constructor = AJAXTable;
function AJAXTable(container, className){
    if(!className) var className = "";
    this.id = -1;
    this.container = container;
    this.tableClassName = className;
    this.rows = new Array();
    this.headerRows = new Array();
    this.cells = new Array(); // Will be 2d
    this.headerCells = new Array(); // Will be 2d
    this.listedByRow = true;
    this.firstColumnLabeled = false;
    this.limit = 25;
//    this.buffer = 25;
    this.offset = 0;
    this.cellContentsFunction = function(val, rowIndex, cellIndex){return val;};
    this.cellContentsFirstColumnFunction = function(val, rowIndex, cellIndex){return val;};
    this.cellContentsHeaderFunction = function(val, rowIndex, cellIndex){return val;};
    this.url = "";
    AJAXTables.add(this);
    this.table = DOMUtils.newElement("table",
    {"class":this.tableClassName, "id": "{table:"+this.id+"}",
        "cellspacing":"0px","cellpadding":"0px"});
    this.tableBody = DOMUtils.newElement("tbody",
        {"id": "{table:"+this.id+",part:body}"});
    this.tableHead = DOMUtils.newElement("thead",
        {"id": "{table:"+this.id+",part:head}"});
    this.table.appendChild(this.tableHead);
    this.table.appendChild(this.tableBody);

    this.linksContainer = DOMUtils.newElement("table",
    {"class":(className!=""?className+"_":"")+"tableLinksContainer",
        "id": "{table:"+this.id+",part:links}"});

}
AJAXTable.prototype.show = function(){
    DOMUtils.removeChildren(this.container);
    this.container.appendChild(this.linksContainer);
    this.container.appendChild(this.table);
};

AJAXTable.prototype.fill = function(url, sortOn, ascending){
    if(!sortOn) var sortOn = null;
    if(!ascending) var ascending = true;
    this.url = url;
    var query = new JSONHttpQuery(this.url, "POST");
    query.post="offset="+this.offset+"&limit="+(this.limit);
    if(this.sortOn!=null){
        query.post += "&sort="+sortOn+"&asc="+(ascending?"true":"false");
    }
    query.parameters = {"id" : this.id};
    query.successFunction = AJAXTables.fillDataRecieved;
    query.run();
};
AJAXTable.prototype.fillDataRecieved = function(jsonData){
    if(typeof(this.cellContentsFunction)!=="function")
        this.cellContentsFunction = function(val, rowIndex, cellIndex){return val;};
    if(typeof(this.cellContentsFirstColumnFunction)!=="function")
        this.cellContentsFirstColumnFunction = function(val, rowIndex, cellIndex){return val;};
    if(typeof(this.cellContentsHeaderFunction)!=="function")
        this.cellContentsHeaderFunction = function(val, rowIndex, cellIndex){return val;};

    var headerRows = jsonData['head'];
    var bodyRows = jsonData['body'];
    var rowClassName;
    var cellClassName;
    var cells;
    var cellInfo;
    var rowIndex;
    var cellIndex;
    var cellContents;
    DOMUtils.removeChildren(this.tableHead);
    for(var i = 0; i<headerRows.length;i++){
        rowClassName = headerRows[i]["class"];
        cells = headerRows[i]["cells"];
        rowIndex = this.addHeaderRow(rowClassName);
        for(var j=0 ; j<cells.length;j++){
            cellInfo = cells[j]["cellInfo"];
            cellClassName = cells[j]["class"];
            cellIndex = this.addHeaderCell(rowIndex, null, cellClassName);
            cellContents = this.cellContentsHeaderFunction(cellInfo, rowIndex, cellIndex);
            this.setHeaderCellContents(rowIndex, cellIndex, cellContents);
        }
    }
    DOMUtils.removeChildren(this.tableBody);
    for(i = 0; i<bodyRows.length;i++){
        rowClassName = bodyRows[i]["class"];
        cells = bodyRows[i]["cells"];
        rowIndex = this.addRow(rowClassName);
        for(j = 0; j<cells.length;j++){
            cellInfo = cells[j]["cellInfo"];
            cellClassName = cells[j]["class"];
            cellIndex = this.addCell(rowIndex, null, cellClassName);
            if(j==0&&this.firstColumnLabeled){
                cellContents = this.cellContentsFirstColumnFunction(cellInfo, rowIndex, cellIndex);
            }else{
                cellContents = this.cellContentsFunction(cellInfo, rowIndex, cellIndex);
            }
            this.setCellContents(rowIndex, cellIndex, cellContents);
        }
    }
};

AJAXTable.prototype.addHeaderRow = function(className){
    if(!className) var className = "";
    var index = this.headerRows.length;
    var tr = DOMUtils.newElement("tr", {"class":className, "id":
            "{table:"+this.id+",part:head,row:"+index+"}"});
    this.headerRows[index] = tr;
    this.tableHead.appendChild(tr);
    return index;
};
AJAXTable.prototype.addHeaderCell = function(row, contents, className){
    if(!className) var className = "";
    if(!contents) var contents = null;
    if(this.headerRows[row]==null) return -1;
    if(this.headerCells[row]==null||!this.headerCells[row].isInstance("Array")){
        this.headerCells[row] = new Array();
    }
    var index = this.headerCells[row].length;
    var td = DOMUtils.newElement("th", {"class":className, "id":
            "{table:"+this.id+",part:head,row:"+row+",cell:"+index+"}"});
    this.headerCells[row][index] = td;
    this.headerRows[row].appendChild(td);
    if(contents!=null){
        this.setHeaderCellContents(row, index, contents);
    }
    return index;
};
AJAXTable.prototype.setHeaderCellContents = function(row, cell, contents){
    if( this.headerCells[row][cell]==null) return;
    DOMUtils.removeChildren(this.headerCells[row][cell]);

    if(contents.isInstance("String")){
        this.headerCells[row][cell].innerHTML = contents;
    }else{
        this.headerCells[row][cell].appendChild(contents);
    }
};
AJAXTable.prototype.addRow = function(className){
    if(!className) var className = "";
    var index = this.rows.length;
    var tr = DOMUtils.newElement("tr", {"class":className, "id":
            "{table:"+this.id+",part:body,row:"+index+"}"});
    this.rows[index] = tr;
    this.tableBody.appendChild(tr);
    return index;
};

AJAXTable.prototype.addCell = function(row, contents, className){
    if(!className) var className = "";
    if(!contents) var contents = null;
    if(this.rows[row]==null) return -1;
    if(this.cells[row]==null||!this.cells[row].isInstance("Array")){
        this.cells[row] = new Array();
    }
    var index = this.cells[row].length;
    var td = DOMUtils.newElement("td", {"class":className, "id":
            "{table:"+this.id+",part:body,row:"+row+",cell:"+index+"}"});
    this.cells[row][index] = td;
    this.rows[row].appendChild(td);
    if(contents!=null){
        this.setCellContents(row, index, contents);
    }
    return index;
};
AJAXTable.prototype.getCell = function(row, cell){
    if( this.cells[row][cell]==null) return null;
    return this.cells[row][cell];
};
AJAXTable.prototype.getRow = function(row){
    if( this.rows[row]==null) return null;
    return this.rows[row];
};
AJAXTable.prototype.setCellContents = function(row, cell, contents){
    if( this.cells[row][cell]==null) return;
    DOMUtils.removeChildren(this.cells[row][cell]);
    
    if(contents.isInstance("String")){
        this.cells[row][cell].innerHTML = contents;
    }else{
        this.cells[row][cell].appendChild(contents);
    }
};
