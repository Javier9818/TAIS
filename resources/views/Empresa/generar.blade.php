@extends('layouts.my_app')
@section('title')
    Empresa - Panel Administrativo
@endsection

@section('styles')
    <script src="https://unpkg.com/gojs@2.1/release/go.js"></script>
    <script src="https://gojs.net/latest/extensions/DoubleTreeLayout.js"></script>
@endsection

@section('sidenav')
<x-side-bar tab="2" selected='30' empresa="{{$empresa->id}}"/>
@endsection

@section('content')
<div class="container">
    <h5 class="card-title text-left mt-4">Empresa "{{$empresa->nombre}}"</h5>
    <hr>
    <componente-generar-cadena></componente-generar-cadena>
</div>
@endsection

@section('scripts')
<script id="code">
    function init(content_json) {
      if (window.goSamples) goSamples();  // init for these samples -- you don't need to call this
      var F = go.GraphObject.make;

      myDiagram =
        F(go.Diagram, "myDiagramDiv",
          {
            initialContentAlignment: go.Spot.Left,
            initialAutoScale: go.Diagram.UniformToFill,
            layout: F(go.LayeredDigraphLayout,
              { direction: 0 }),
            "undoManager.isEnabled": true
          }
        );

      // when the document is modified, add a "*" to the title and enable the "Save" button
      myDiagram.addDiagramListener("Modified", function(e) {
        var button = document.getElementById("SaveButton");
        if (button) button.disabled = !myDiagram.isModified;
        var idx = document.title.indexOf("*");
        if (myDiagram.isModified) {
          if (idx < 0) document.title += "*";
        } else {
          if (idx >= 0) document.title = document.title.substr(0, idx);
        }
      });

      function makePort(name, leftside) {
        var port = F(go.Shape, "Rectangle",
          {
            fill: "gray", stroke: null,
            desiredSize: new go.Size(8, 8),
            portId: name,  // declare this object to be a "port"
            toMaxLinks: 1,  // don't allow more than one link into a port
            cursor: "pointer"  // show a different cursor to indicate potential link point
          });

        var lab = F(go.TextBlock, name,  // the name of the port
          { font: "7pt sans-serif" });

        var panel = F(go.Panel, "Horizontal",
          { margin: new go.Margin(2, 0) });

        // set up the port/panel based on which side of the node it will be on
        if (leftside) {
          port.toSpot = go.Spot.Left;
          port.toLinkable = true;
          lab.margin = new go.Margin(1, 0, 0, 1);
          panel.alignment = go.Spot.TopLeft;
          panel.add(port);
          panel.add(lab);
        } else {
          port.fromSpot = go.Spot.Right;
          port.fromLinkable = true;
          lab.margin = new go.Margin(1, 1, 0, 0);
          panel.alignment = go.Spot.TopRight;
          panel.add(lab);
          panel.add(port);
        }
        return panel;
      }

      function findHeadShot(picture) {
        if (picture === null) return "/assets/img/empty.jpg"; // There are only 16 images on the server
        return "/storage/images/entidad/" + picture
      }

      function makeTemplate(typename, icon, background, inports, outports) {
        var node = F(go.Node, "Spot",
          F(go.Panel, "Auto",
            { width: 100, height: 100 },
            F(go.Shape, "Rectangle",
              {
                fill: background, stroke: null, strokeWidth: 0,
                spot1: go.Spot.TopLeft, spot2: go.Spot.BottomRight
              }),
            F(go.Panel, "Table",
            //   F(go.TextBlock, typename,
            //     {
            //       row: 0,
            //       margin: 3,
            //       maxSize: new go.Size(80, NaN),
            //       stroke: "white",
            //       font: "bold 11pt sans-serif"
            //     }),
              // F(go.Picture, icon,
              //   { row: 1, width: 55, height: 55 }),
              F(go.TextBlock,
                {
                  row: 2,
                  margin: 3,
                  editable: true,
                  maxSize: new go.Size(80, 40),
                  stroke: "white",
                  font: "bold 9pt sans-serif"
                },
                new go.Binding("text", "name").makeTwoWay()),
                F(go.Picture,
                {
                  name: "Picture",
                  desiredSize: new go.Size(50, 50),
                  margin: new go.Margin(6, 8, 6, 10),
                },
                new go.Binding("source", "picture", findHeadShot)),
            )
          ),
          F(go.Panel, "Vertical",
            {
              alignment: go.Spot.Left,
              alignmentFocus: new go.Spot(0, 0.5, 8, 0)
            },
            inports),
          F(go.Panel, "Vertical",
            {
              alignment: go.Spot.Right,
              alignmentFocus: new go.Spot(1, 0.5, -8, 0)
            },
            outports)
        );
        myDiagram.nodeTemplateMap.set(typename, node);
      }

      makeTemplate("Table", "images/55x55.png", "forestgreen",
        [],
        [makePort("OUT", false)]);

      makeTemplate("Join", "images/55x55.png", "mediumorchid",
        [makePort("L", true), makePort("R", true)],
        [makePort("UL", false), makePort("ML", false), makePort("M", false), makePort("MR", false), makePort("UR", false)]);

      makeTemplate("Project", "images/55x55.png", "darkcyan",
        [makePort("", true)],
        [makePort("OUT", false)]);

      makeTemplate("Filter", "images/55x55.png", "cornflowerblue",
        [makePort("", true)],
        [makePort("OUT", false), makePort("INV", false)]);

      makeTemplate("Group", "images/55x55.png", "mediumpurple",
        [makePort("", true)],
        [makePort("OUT", false)]);

        makeTemplate("Group2", "images/55x55.png", "sienna",
        [makePort("", true)],
        [makePort("OUT", false)]);

      makeTemplate("Sort", "images/55x55.png", "sienna",
        [makePort("", true)],
        [makePort("OUT", false)]);

      makeTemplate("Export", "images/55x55.png", "darkred",
        [makePort("", true)],
        []);

      myDiagram.linkTemplate =
        F(go.Link,
          {
            routing: go.Link.Orthogonal, corner: 5,
            relinkableFrom: true, relinkableTo: true
          },
          F(go.Shape, { stroke: "gray", strokeWidth: 2 }),
          F(go.Shape, { stroke: "gray", fill: "gray", toArrow: "Standard" })
        );

      load(content_json);
    }

    // function init() {
      //   if (window.goSamples) goSamples();  // init for these samples -- you don't need to call this
      //   var F = go.GraphObject.make;  // for conciseness in defining templates in this function

      //   myDiagram =
      //     F(go.Diagram, "myDiagramDiv",
      //       {
      //         layout: F(DoubleTreeLayout,
      //           {
      //             //vertical: true,  // default directions are horizontal
      //             // choose whether this subtree is growing towards the right or towards the left:
      //             directionFunction: function(n) { return n.data && n.data.dir !== "left"; }
      //             // controlling the parameters of each TreeLayout:
      //             //bottomRightOptions: { nodeSpacing: 0, layerSpacing: 20 },
      //             //topLeftOptions: { alignment: go.TreeLayout.AlignmentStart },
      //           })
      //       });

      //   // define all of the gradient brushes
      //   var graygrad = F(go.Brush, "Linear", { 0: "#F5F5F5", 1: "#F1F1F1" });
      //   var bluegrad = F(go.Brush, "Linear", { 0: "#CDDAF0", 1: "#91ADDD" });
      //   var yellowgrad = F(go.Brush, "Linear", { 0: "#FEC901", 1: "#FEA200" });
      //   var lavgrad = F(go.Brush, "Linear", { 0: "#EF9EFA", 1: "#A570AD" });

      //   myDiagram.nodeTemplate =
      //     F(go.Node, "Auto",
      //       { isShadowed: true },
      //       // define the node's outer shape
      //       F(go.Shape, "RoundedRectangle",
      //         { fill: graygrad, stroke: "#D8D8D8" },  // default fill is gray
      //         new go.Binding("fill", "color")),
      //       // define the node's text
      //       F(go.TextBlock,
      //         { margin: 5, font: "bold 11px Helvetica, bold Arial, sans-serif" },
      //         new go.Binding("text", "key"))
      //     );

      //   myDiagram.linkTemplate =
      //     F(go.Link,  // the whole link panel
      //       { selectable: false },
      //       $(go.Shape));  // the link shape

      //   // create the model for the double tree; could be eiher TreeModel or GraphLinksModel
      //   myDiagram.model = new go.TreeModel([
      //     { key: "Root", color: lavgrad },
      //     { key: "Left1", parent: "Root", dir: "left", color: bluegrad },
      //     { key: "leaf1", parent: "Left1" },
      //     { key: "leaf2", parent: "Left1" },
      //     { key: "Left2", parent: "Left1", color: bluegrad },
      //     { key: "leaf3", parent: "Left2" },
      //     { key: "leaf4", parent: "Left2" },
      //     { key: "leaf5", parent: "Left1" },
      //     { key: "Right1", parent: "Root", dir: "right", color: yellowgrad },
      //     { key: "Right2", parent: "Right1", color: yellowgrad },
      //     { key: "leaf11", parent: "Right2" },
      //     { key: "leaf12", parent: "Right2" },
      //     { key: "leaf13", parent: "Right2" },
      //     { key: "leaf14", parent: "Right1" },
      //     { key: "leaf15", parent: "Right1" },
      //     { key: "Right3", parent: "Root", dir: "right", color: yellowgrad },
      //     { key: "leaf16", parent: "Right3" },
      //     { key: "leaf17", parent: "Right3" }
      //   ]);
    // }

    // Show the diagram's model in JSON format that the user may edit
    function save() {
      document.getElementById("mySavedModel").value = myDiagram.model.toJson();
      myDiagram.isModified = false;
    }
    function load(content_json) {
      myDiagram.model = go.Model.fromJson(content_json);
      // myDiagram.model = new go.TreeModel([
      //     { key: "Root" },
      //     { key: "Left1", parent: "Root", dir: "left" },
      //     { key: "leaf1", parent: "Left1" },
      //     { key: "leaf2", parent: "Left1" },
      //     { key: "Left2", parent: "Left1"},
      //     { key: "leaf3", parent: "Left2" },
      //     { key: "leaf4", parent: "Left2" },
      //     { key: "leaf5", parent: "Left1" },
      //     { key: "Right1", parent: "Root", dir: "right"},
      //     { key: "Right2", parent: "Right1" },
      //     { key: "leaf11", parent: "Right2" },
      //     { key: "leaf12", parent: "Right2" },
      //     { key: "leaf13", parent: "Right2" },
      //     { key: "leaf14", parent: "Right1" },
      //     { key: "leaf15", parent: "Right1" },
      //     { key: "Right3", parent: "Root", dir: "right"},
      //     { key: "leaf16", parent: "Right3" },
      //     { key: "leaf17", parent: "Right3" }
      //   ]);
    
    }
</script>

<script>
    let empresa = @json($empresa);
    let unidades_negocio = @json($unidades_negocio);
    // console.log(empresa)
</script>
@endsection
