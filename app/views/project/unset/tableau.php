<script type="text/javascript" src="https://online.tableau.com/javascripts/api/tableau-2.0.0.min.js"></script>


<div id="tableauViz"></div>

<script>
var placeholderDiv = document.getElementById("tableauViz");
var url = "http://localhost:8000/#/views/Regional/College?:iid=3";
var options = {
   hideTabs: true,
   width: "800px",
   height: "700px",
   onFirstInteractive: function() {
     // The viz is now ready and can be safely used.
   }
};
var viz = new tableau.Viz(placeholderDiv, url, options);
</script>

<form action="http://localhost:8000/api/2.0/auth/signin" method="POST">
	<textarea>
    <?='<?xml version="1.0" encoding="UTF-8" ?>'?>
    <tsRequest>
    <credentials name="tim72117" password="superd@150201">
    <site contentUrl="" />
    </credentials>
    </tsRequest>
  </textarea>
	<input type="submit" />
</form>