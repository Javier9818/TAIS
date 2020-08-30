function init(contentJson) {
    if (window.goSamples) goSamples();  // init for these samples -- you don't need to call this
    var $ = go.GraphObject.make;  // for conciseness in defining templates

    myDiagram =
        $(go.Diagram, "mapaMacroDiv",  // must name or refer to the DIV HTML element
        {
            allowCopy: false,
            allowDelete: false,
            draggingTool: $(CustomDraggingTool),
            layout: $(CustomLayout),
            // enable undo & redo
            "undoManager.isEnabled": true
        });

    // define the Node template
    myDiagram.nodeTemplate =
        $(go.Node, "Auto",
        new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
        // define the node's outer shape, which will surround the TextBlock
        $(go.Shape, "RoundedRectangle",
            {
            fill: "rgb(254, 201, 0)", stroke: "black", parameter1: 20,  // the corner has a large radius
            portId: "", fromSpot: go.Spot.AllSides, toSpot: go.Spot.AllSides
            }),
        $(go.TextBlock,
            new go.Binding("text", "text").makeTwoWay())
        );

    myDiagram.nodeTemplateMap.add("Super",
        $(go.Node, "Auto",
        { locationObjectName: "BODY" },
        $(go.Shape, "RoundedRectangle",
            {
            fill: "rgba(128, 128, 64, 0.5)", strokeWidth: 1.5, parameter1: 20,
            spot1: go.Spot.TopLeft, spot2: go.Spot.BottomRight, minSize: new go.Size(30, 30)
            }),
        $(go.Panel, "Vertical",
            { margin: 10 },
            $(go.TextBlock,
            { font: "bold 10pt sans-serif", margin: new go.Margin(0, 0, 5, 0) },
            new go.Binding("text")),
            $(go.Shape,
            { name: "BODY", opacity: 0 })
        )
        ));

    // replace the default Link template in the linkTemplateMap
    myDiagram.linkTemplate =
        $(go.Link,  // the whole link panel
        { routing: go.Link.Orthogonal, corner: 10 },
        $(go.Shape,  // the link shape
            { strokeWidth: 1.5 }),
        $(go.Shape,  // the arrowhead
            { toArrow: "Standard", stroke: null })
        );

    // read in the JSON-format data from the "mySavedModel" element
    load(contentJson);
}

function CustomLayout() {
  go.Layout.call(this);
}
go.Diagram.inherit(CustomLayout, go.Layout);

CustomLayout.prototype.doLayout = function(coll) {
  coll = this.collectParts(coll);

  var supers = new go.Set(/*go.Node*/);
  coll.each(function(p) {
    if (p instanceof go.Node && p.category === "Super") supers.add(p);
  });

  function membersOf(sup, diag) {
    var set = new go.Set(/*go.Part*/);
    var arr = sup.data._members;
    for (var i = 0; i < arr.length; i++) {
      var d = arr[i];
      set.add(diag.findNodeForData(d));
    }
    return set;
  }

  function isReady(sup, supers, diag) {
    var arr = sup.data._members;
    for (var i = 0; i < arr.length; i++) {
      var d = arr[i];
      if (d.category !== "Super") continue;
      var n = diag.findNodeForData(d);
      if (supers.has(n)) return false;
    }
    return true;
  }

  // implementations of doLayout that do not make use of a LayoutNetwork
  // need to perform their own transactions
  this.diagram.startTransaction("Custom Layout");

  while (supers.count > 0) {
    var ready = null;
    var it = supers.iterator;
    while (it.next()) {
      if (isReady(it.value, supers, this.diagram)) {
        ready = it.value;
        break;
      }
    }
    supers.remove(ready);
    var b = this.diagram.computePartsBounds(membersOf(ready, this.diagram));
    ready.location = b.position;
    var body = ready.findObject("BODY");
    if (body) body.desiredSize = b.size;
  }

  this.diagram.commitTransaction("Custom Layout");
};

function CustomDraggingTool() {
  go.DraggingTool.call(this);
}
go.Diagram.inherit(CustomDraggingTool, go.DraggingTool);

CustomDraggingTool.prototype.moveParts = function(parts, offset, check) {
  go.DraggingTool.prototype.moveParts.call(this, parts, offset, check);
  this.diagram.layoutDiagram(true);
};

CustomDraggingTool.prototype.computeEffectiveCollection = function(parts) {
  var coll = new go.Set(/*go.Part*/).addAll(parts);
  var tool = this;
  parts.each(function(p) {
    tool.walkSubTree(p, coll);
  });
  return go.DraggingTool.prototype.computeEffectiveCollection.call(this, coll);
};

CustomDraggingTool.prototype.walkSubTree = function(sup, coll) {
  if (sup === null) return;
  coll.add(sup);
  if (sup.category !== "Super") return;
  // recurse through this super state's members
  var model = this.diagram.model;
  var mems = sup.data._members;
  if (mems) {
    for (var i = 0; i < mems.length; i++) {
      var mdata = mems[i];
      this.walkSubTree(this.diagram.findNodeForData(mdata), coll);
    }
  }
};

function load(contentJson) {
  myDiagram.model = go.Model.fromJson(contentJson);

  // make sure all data have up-to-date "members" collections
  // this does not prevent any "cycles" of membership, which would result in undefined behavior
  var arr = myDiagram.model.nodeDataArray;
  console.log(myDiagram.model.nodeDataArray.length)
  for (var i = 0; i < arr.length; i++) {
    var data = arr[i];
    var supers = data.supers;
    if (supers) {
      for (var j = 0; j < supers.length; j++) {
        var sdata = myDiagram.model.findNodeDataForKey(supers[j]);
        if (sdata) {
          // update _supers to be an Array of references to node data
          if (!data._supers) {
            data._supers = [sdata];
          } else {
            data._supers.push(sdata);
          }
          // update _members to be an Array of references to node data
          if (!sdata._members) {
            sdata._members = [data];
          } else {
            sdata._members.push(data);
          }
        }
      }
    }
  }
}