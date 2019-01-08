Attribute VB_Name = "ProfzuTopic1"
Sub ProfzuTopic()
Attribute ProfzuTopic.VB_ProcData.VB_Invoke_Func = " \n14"
'
' ProfzuTopic Makro

'Hier werden die Zeilen gelöscht die ein ??? als Prof haben
'
'
Application.ScreenUpdating = True ' Zum testen auf true im betrieb auf False

    ChDir "C:\Users\nilsr\Desktop\Final_IT\Volzergebnisse"
    Workbooks.Open Filename:= _
        "C:\dtm.csv"
   
    
     Application.WindowState = xlNormal
   

'Application.ScreenUpdating = False
 
Sheets("dtm").Select
Rows("1:1").Select

    Selection.AutoFilter
Dim fragz As Long
Range("A1:I1").Select
    Columns("D:D").Select
    ActiveWorkbook.Worksheets("dtm").Sort.SortFields.Clear
    ActiveWorkbook.Worksheets("dtm").Sort.SortFields.Add Key:=Range _
        ("D1"), SortOn:=xlSortOnValues, Order:=xlDescending, DataOption:= _
        xlSortNormal
    With ActiveWorkbook.Worksheets("dtm").Sort
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
    
   If anzfragz <> 0 Then
   Range(Rows(2), Rows(anzfragz + 1)).Delete
   Else
   End If


'' löschenwenn Makro
'

' Ab hier werden die Stoppwords gelöscht. Dies benötigt sehr lange daher zum testen ausgeklammert
'Application.ScreenUpdating = False
'
 Dim zähler As Long
 Dim i As Long
 Dim Stopw As String
 
 Range("B3944").Select
 
' Sheets("Stopwords").Select
'For zähler = Cells(Rows.Count, 2).End(xlUp).Row To 1 Step -1
' Sheets("Stopwords").Select
' Stopw = Cells(zähler, 2)
'dieser muss nach der 2 ForSchleife
'  Sheets("dtm - Kopie").Select
'  For i = Cells(Rows.Count, 1).End(xlUp).Row To 1 Step -1
'    If Cells(i, 2) = Stopw Then Rows(i).Delete
'  Next i
'  Next zähler
   









'
'Prof zu Topic zugeordnet
'
Sheets("dtm").Select
Rows("1:1").Select
    Selection.AutoFilter
    
 Workbooks("dtm.csv").Worksheets.Add.Name = "ProfzuTopic"
  Sheets("dtm").Select
    
    Range("A1").Select
    Selection.AutoFilter
    ActiveWorkbook.Worksheets("dtm").AutoFilter.Sort.SortFields.Clear
    ActiveWorkbook.Worksheets("dtm").AutoFilter.Sort.SortFields.Add Key:= _
        Range("D2:D1048576"), SortOn:=xlSortOnValues, Order:=xlAscending, _
        DataOption:=xlSortNormal
    ActiveWorkbook.Worksheets("dtm").AutoFilter.Sort.SortFields.Add Key:= _
        Range("G2:G1048576"), SortOn:=xlSortOnValues, Order:=xlDescending, _
        DataOption:=xlSortNormal
    With ActiveWorkbook.Worksheets("dtm").AutoFilter.Sort
        .Header = xlYes
        .MatchCase = False
        .Orientation = xlTopToBottom
        .SortMethod = xlPinYin
        .Apply
    End With
    
    '
    ' Duplikate entfernen wenn Term und Prof gleich sind
    '
    
    
    Cells.Select
  
    ActiveSheet.Range("$A$1:$I$187538").RemoveDuplicates Columns:=Array(2, 4), _
        Header:=xlYes
    
    
    Range("D2").Select
    Application.ScreenUpdating = False
    Do While IsEmpty(Range("D2").Value) = False
    Application.CutCopyMode = False
    'Iteration
    Range("D2:D6").Select
    Selection.Copy
    Sheets("ProfzuTopic").Select
    ActiveWindow.SmallScroll Down:=-12
    Range("A2").Select
    ActiveSheet.Paste
    Sheets("dtm").Select
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
    
    
   
    Sheets("dtm").Select
    'Löschen aktuellen Prof
    Dim var As Integer
    Dim anz As Integer
    Dim anz3 As Long
    var = Range("D2").Value
    anz3 = Application.WorksheetFunction.CountIf(Range("D:D"), var)
   Range(Rows(2), Rows(anz3 + 1)).Delete
    
    
   
   'Do While var = Range("D3").Value
  ' Rows(ActiveCell.Row).Delete
   'Range("D2").Select
    '     var = Application.Match("11", Columns(1), 0)
   ' If Not var = Range("D3").Value Then Exit Do
  ' Loop

    
    'Rows(2).Delete
    
    
    Loop
    Application.ScreenUpdating = True ' zum testen auf True im betrieb auf false
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
    Application.DisplayAlerts = False
    ActiveWorkbook.SaveAs Filename:="C:\Nudel\topic.csv"
    Application.DisplayAlerts = True


Columns("A:A").Select
    Selection.TextToColumns Destination:=Range("A1"), DataType:=xlDelimited, _
        TextQualifier:=xlDoubleQuote, ConsecutiveDelimiter:=False, Tab:=False, _
        Semicolon:=False, Comma:=True, Space:=False, Other:=False, FieldInfo _
        :=Array(Array(1, 1), Array(2, 1), Array(3, 1), Array(4, 1), Array(5, 1), Array(6, 1), _
        Array(7, 1), Array(8, 1), Array(9, 1)), TrailingMinusNumbers:=True

Range("A1").Select
    Selection.AutoFilter
    ActiveWorkbook.Worksheets("ProfzuTopic").AutoFilter.Sort.SortFields.Clear
    ActiveWorkbook.Worksheets("ProfzuTopic").AutoFilter.Sort.SortFields.Add Key:=Range( _
        "A1"), SortOn:=xlSortOnValues, Order:=xlAscending, DataOption:= _
        xlSortNormal
    With ActiveWorkbook.Worksheets("ProfzuTopic").AutoFilter.Sort
        .Header = xlYes
        .MatchCase = False
        .Orientation = xlTopToBottom
        .SortMethod = xlPinYin
        .Apply
    End With
    
Columns("B:B").Select
    Selection.Insert Shift:=xlToRight, CopyOrigin:=xlFormatFromLeftOrAbove
    Range("B1").Select
    ActiveCell.FormulaR1C1 = "topicID"
    Range("G10").Select


'
'


Dim title As String
Dim same As Integer
Dim var2 As String
Dim schluss As Integer
Dim AnzahlZeilen As Long
Dim anz2 As Integer
Dim anz1 As Integer
Dim u As Integer


AnzahlZeilen = ActiveSheet.Cells(Rows.Count, 1).End(xlUp).Row

u = 1
anz2 = 1
anz1 = 2
Range("A2").Select
'schluss = Selection.End(xlDown).Select
i = 2

Range("B2").Value = 1

For i = 2 To AnzahlZeilen


title = ActiveSheet.Cells(i, 1).Value

If ActiveSheet.Cells(i - 1, 1) = title Then

ActiveSheet.Cells(i, 2).Value = u

Else

       u = u + 1
  ActiveSheet.Cells(i, 2).Value = u
End If
Next
'
' ab hier wird csv Knoten gebildet
'


Workbooks("topic.csv").Worksheets.Add.Name = "Knoten"
Sheets("ProfzuTopic").Select
Columns("A:B").Select
    Selection.Copy
Sheets("Knoten").Select
Selection.PasteSpecial Paste:=xlPasteValues, Operation:=xlNone, SkipBlanks _
        :=False, Transpose:=False
        
 Columns("B:B").Select
    ActiveSheet.Range("$A$1:$I$187538").RemoveDuplicates Columns:=2, Header:= _
    xlYes
    Range("A2").Select
    Selection.End(xlDown).Select
    ActiveCell.Offset(0, 2).Select
    ActiveCell.FormulaR1C1 = "=RC[-2]&"",""&RC[-1]"
    
    
    Selection.AutoFill Destination:=Range(ActiveCell, ActiveCell.End(xlUp)), Type:=xlFillDefault
    
    Range("C1").Select
    Selection.Copy
    Selection.PasteSpecial Paste:=xlPasteValues, Operation:=xlNone, SkipBlanks _
        :=False, Transpose:=False
    Columns("A:B").Select
    Application.CutCopyMode = False
    Selection.Delete Shift:=xlToLeft
    
    Sheets("Knoten").Select
    Sheets("Knoten").Move
    
    
    'Range("A:B").Delete
        
        
    Application.DisplayAlerts = False
ActiveSheet.SaveAs Filename:="C:\Nudel\knot_topic_topicID.csv", FileFormat:=xlCSV, CreateBackup:=False, Local:=True
Application.DisplayAlerts = True


'ThisWorkbook.Worksheets.Add.Name = "ProfzuTopic"
'
'ab hier wird die Kante gebildet

'
Workbooks("topic.csv").Activate
Range("B2").Select
    Selection.End(xlDown).Select
    ActiveCell.Offset(0, 2).Select
    ActiveCell.FormulaR1C1 = "=RC[-1]&"",""&RC[-2]"
    
    
    Selection.AutoFill Destination:=Range(ActiveCell, ActiveCell.End(xlUp)), Type:=xlFillDefault
    
    
    Workbooks("topic.csv").Worksheets.Add.Name = "Kante"
    Sheets("ProfzuTopic").Select
    Columns("D:D").Select
    Selection.Copy
    Sheets("Kante").Select
    Selection.PasteSpecial Paste:=xlPasteValues, Operation:=xlNone, SkipBlanks _
        :=False, Transpose:=False

    Application.DisplayAlerts = False
    Sheets("Kante").Select
    Sheets("Kante").Move
    
ActiveSheet.SaveAs Filename:="C:\Nudel\edge_prof_topic.csv", FileFormat:=xlCSV, CreateBackup:=False, Local:=True
Application.DisplayAlerts = True
ActiveWorkbook.Close SaveChanges:=True
ActiveWorkbook.Close SaveChanges:=False
ActiveWorkbook.Close SaveChanges:=False
ActiveWorkbook.Close SaveChanges:=False

Kill "C:\Nudel\topic.csv"
  Application.DisplayAlerts = True
  
  
  End Sub
