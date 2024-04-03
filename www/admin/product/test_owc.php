<html>
<script language="javascript">
window.onload = function () { 
  var layout_id = "LAYOUT_LAY001";
  var maxrow = 11
  layout_sheet = getBpsExcel( layout_id );
  if (layout_sheet && layout_sheet.ActiveSheet) 

{ // Excel OWC available
    // set the properties of the component
    layout_sheet.style.height = 1000;
    layout_sheet.style.width = 1000;
    layout_sheet.Cells(1,1).value = "Anand Jain";

//get the maximum row for which the data has been entered
for (i=11; i<100; i++) {
if (layout_sheet.Cells(i,1).value !=null)
 { maxrow++;}
else {break;}
if (layout_sheet.Cells(i,1).value !="")
 { maxrow++;}
else {break;}

alert (maxrow); 
alert (layout_sheet.Cells(i,1).value) }
 layout_sheet.Columns.AutoFit();
 layout_sheet.Cells(maxrow,1).value = "This is the last line";}
} 





  function getBpsExcel( layout_id )
  {
      var layout_sheet = document.all( layout_id );
      var classIds = ["CLSID:0002E559-0000-0000-C000-000000000046",  // 2003
                      "CLSID:0002E551-0000-0000-C000-000000000046",  // XP(2002)
                      "CLSID:0002E510-0000-0000-C000-000000000046"]; // 2000

      // if Excel OWC NOT available => try OCX from another known office version
      var defClassId = layout_sheet.classid;
      for (i = 0; !layout_sheet.ActiveSheet && i < classIds.length; i++) {
        if (classIds<i> == defClassId) continue; // skip default

        // remove the irrelevant object
        layout_sheet.removeNode();

        // create new object & set size attributes
        layout_sheet = document.createElement("OBJECT");
        layout_sheet.style.height = "0";
        layout_sheet.style.width = "0";

        // append to dom and activate OCX
        var layout_div = document.all( layout_id + "-div" );
        layout_sheet = layout_div.appendChild(layout_sheet);
        layout_sheet.classid = classIds<i>;
        layout_sheet.id = layout_id;
      } // loop over OCX classes

      if (layout_sheet.ActiveSheet) { // Excel OWC available

        // send the office version to the backend
        document.all(layout_id + "-class").value = layout_sheet.classid;

        layout_sheet.ActiveSheet.Protection.Enabled = false;

        // For input handling of excel we need to submit
        // the decimal separator from regional settings on the client
        // Take sample values from the OWC

        var cellValue;
        var cell = layout_sheet.cells(1,1);
        var oldFormat = cell.NumberFormat;
        cell.NumberFormat  = "#,##0.0";

        bpsClipboardStore();

        cell.ParseText("1.5", "t");
        cellValue = getBpsCell(cell);
        if (cellValue == "") { // clipboard failed, get system setting
          cellValue = new  Number(1.5 ).toLocaleString();
        }
        document.all("bps-float_number").value = cellValue;

        cell.ParseText("1000", "t");
        cellValue = getBpsCell(cell);
        if (cellValue == "") { // clipboard failed, get system setting
          cellValue = new Number(1000).toLocaleString();
        }
        document.all("bps-thousand_number").value = cellValue;

        bpsClipboardRestore();

        cell.NumberFormat = oldFormat;
        cell.clear();
      }

      return layout_sheet;
  }

</script>
<body>
</body>
</html>