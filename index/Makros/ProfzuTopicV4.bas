Attribute VB_Name = "ProfzuTopic"
Sub ProfzuTopic()
Attribute ProfzuTopic.VB_ProcData.VB_Invoke_Func = " \n14"
'
' ProfzuTopic Makro

'Hier werden die Zeilen gelöscht die ein ??? als Prof haben
'
Application.ScreenUpdating = False
Sheets("TopThemen").Select
Rows("1:1").Select
Application.ScreenUpdating = True
    Selection.AutoFilter
Dim fragz As Long
Range("A1:I1").Select
    Columns("D:D").Select
    ActiveWorkbook.Worksheets("TopThemen").Sort.SortFields.Clear
    ActiveWorkbook.Worksheets("TopThemen").Sort.SortFields.Add Key:=Range _
        ("D1"), SortOn:=xlSortOnValues, Order:=xlDescending, DataOption:= _
        xlSortNormal
    With ActiveWorkbook.Worksheets("TopThemen").Sort
        .SetRange Range("A2:I1048576")
        .Header = xlNo
        .MatchCase = False
        .Orientation = xlTopToBottom
        .SortMethod = xlPinYin
        .Apply
    End With



    Range("A1:I1").Select
    'Selection.AutoFilter
    ActiveSheet.Range("$A:$I").AutoFilter Field:=4, Criteria1:="~?~?~?"
    
    anzfragz = Application.WorksheetFunction.CountIf(Range("D:D"), "???")
   Range(Rows(2), Rows(anzfragz + 1)).Delete


'
'
'Prof zu Topic zugeordnet
'
Sheets("TopThemen").Select
Rows("1:1").Select
    Selection.AutoFilter
    
 ThisWorkbook.Worksheets.Add.Name = "ProfzuTopic"
  Sheets("TopThemen").Select
    
    Range("A1").Select
    Selection.AutoFilter
    ActiveWorkbook.Worksheets("TopThemen").AutoFilter.Sort.SortFields.Clear
    ActiveWorkbook.Worksheets("TopThemen").AutoFilter.Sort.SortFields.Add Key:= _
        Range("D2:D1048576"), SortOn:=xlSortOnValues, Order:=xlAscending, _
        DataOption:=xlSortNormal
    ActiveWorkbook.Worksheets("TopThemen").AutoFilter.Sort.SortFields.Add Key:= _
        Range("G2:G1048576"), SortOn:=xlSortOnValues, Order:=xlDescending, _
        DataOption:=xlSortNormal
    With ActiveWorkbook.Worksheets("TopThemen").AutoFilter.Sort
        .Header = xlYes
        .MatchCase = False
        .Orientation = xlTopToBottom
        .SortMethod = xlPinYin
        .Apply
    End With
    
    Range("D2").Select
    
    Do While IsEmpty(Range("D2").Value) = False
    
    'Iteration
    Range("D2:D6").Select
    Selection.Copy
    Sheets("ProfzuTopic").Select
    ActiveWindow.SmallScroll Down:=-12
    Range("A2").Select
    ActiveSheet.Paste
    Sheets("TopThemen").Select
    Range("B2:B6").Select
    Application.CutCopyMode = False
    Selection.Copy
    Sheets("ProfzuTopic").Select
    Range("B2:B6").Select
    ActiveSheet.Paste
    Range("A2").Select
    ActiveSheet.Rows(2).Insert Shift:=xlUp  'eine Zeile oberhalb einfügen
    ActiveSheet.Rows(2).Insert Shift:=xlUp  '2
    ActiveSheet.Rows(2).Insert Shift:=xlUp  '3
    ActiveSheet.Rows(2).Insert Shift:=xlUp  '4
    ActiveSheet.Rows(2).Insert Shift:=xlUp  '5
    
    Application.CutCopyMode = False
   
    Sheets("TopThemen").Select
    'Löschen aktuellen Prof
    Dim var As Integer
    Dim anz As Integer
    Dim anz2 As Long
    var = Range("D2").Value
    anz2 = Application.WorksheetFunction.CountIf(Range("D:D"), var)
   Range(Rows(2), Rows(anz2 + 1)).Delete
    
    
   
   'Do While var = Range("D3").Value
  ' Rows(ActiveCell.Row).Delete
   'Range("D2").Select
    '     var = Application.Match("11", Columns(1), 0)
   ' If Not var = Range("D3").Value Then Exit Do
  ' Loop

    
    'Rows(2).Delete
    
        
    Loop
    Sheets("ProfzuTopic").Select
    Rows(2).Delete
    Rows(2).Delete
    Rows(2).Delete
    Rows(2).Delete
    Rows(2).Delete
    
    '
    Range("B2").Select
    Selection.End(xlDown).Select
    ActiveCell.Offset(0, 1).Select
    ActiveCell.FormulaR1C1 = "=RC[-1]&"",""&RC[-2]"
    Selection.AutoFill Destination:=Range(ActiveCell, ActiveCell.End(xlUp)), Type:=xlFillDefault
    Range("B1").Select
    ActiveCell.FormulaR1C1 = "title"
    Range("A1").Select
    ActiveCell.FormulaR1C1 = "profID"
    Columns("C:C").Select
    Selection.Copy
    Range("D1").Select
    Selection.PasteSpecial Paste:=xlPasteValues, Operation:=xlNone, SkipBlanks _
        :=False, Transpose:=False
    Columns("A:C").Select
    Application.CutCopyMode = False
    Selection.Delete Shift:=xlToLeft
    
    ActiveSheet.Copy
    '
    'Pfad anpassen
    '
    '
ActiveWorkbook.SaveAs Filename:="C:\Nudel\" & topic & ".csv"
    
End Sub
