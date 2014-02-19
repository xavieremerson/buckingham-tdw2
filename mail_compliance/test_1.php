<html>
<head>
<script language='VBScript'>
Dim objXMLHTTP, objXMLDoc
' Define your protocol; http or https
strProtocol = "http"
' Define your server name
strServername = "YOUR_EXCHANGE_SERVER"
' Define your local name for 'Inbox'
strInbox = "Inbox"
Sub getMessages_OnClick()
  strUsername = document.all.mailbox.value
  If strUsername <> "" Then
    strInboxURL = strProtocol & "://" & strServername & "/Exchange/"
    strInboxURL = strInboxURL & strUsername & "/" & strInbox
    Set objXMLHTTP = CreateObject("Microsoft.XMLHTTP")
    objXMLHTTP.Open "SEARCH", strInboxURL, True
    objXMLHTTP.setRequestHeader "Content-type:", "text/xml"
    objXMLHTTP.setRequestHeader "Depth", "1"
    objXMLHTTP.onReadyStateChange = getRef("checkXMLHTTPState")
    strXML = "<?xml version='1.0' ?>" & _
     "<a:searchrequest xmlns:a='DAV:'><a:sql>" & _
       "SELECT" & _
       " ""DAV:href""" & _
       ",""urn:schemas:httpmail:subject""" & _
       " FROM scope('shallow traversal of """ & strInboxURL & """')" & _
      " WHERE ""DAV:ishidden""=False" & _
      " AND ""DAV:isfolder""=False" & _
     "</a:sql></a:searchrequest>"
    objXMLHTTP.SetRequestHeader "Range", "rows=0-9"
    objXMLHTTP.Send(strXML)
   End If
 End Sub

Sub checkXMLHTTPState
  If objXMLHTTP.readyState = 4 Then
    responseStatus.innerHTML = objXMLHTTP.Status & " - " & objXMLHTTP.StatusText
    Set objXMLDoc = objXMLHTTP.ResponseXML
    XSLDiv.innerHTML = objXMLDoc.TransformNode(responseXSL.documentElement)
    Set objXMLHTTP = Nothing
    Set objXMLDoc = Nothing
  End If
End Sub

</script>
<xml id='responseXSL'>
  <xsl:template xmlns:xsl='uri:xsl' xmlns:a='DAV:' xmlns:d='urn:schemas:httpmail:'>
    <table>
      <!-- Add a row for each element in the 207 Multistatus response to the SEARCH request. -->
      <xsl:for-each select='a:multistatus/a:response'>
        <tr>
          <!-- Build a hyperlink using the resource href and subject -->
          <td>
            <a>
              <xsl:attribute name='href'>
                <xsl:value-of select='a:propstat/a:prop/a:href' />/?Cmd=open
              </xsl:attribute>
              <xsl:attribute name='target'>_blank</xsl:attribute>
              <xsl:value-of select='a:propstat/a:prop/d:subject' />
            </a>
          </td>
        </tr>
      </xsl:for-each>
    </table>
  </xsl:template>
</xml>

</head>
<body>
<font face='Verdana' size='2'>
Mailbox name:<br>
<input type='text' name='mailbox'><br>
<input type='button' name='getMessages' value='    GO    '><br>
<div id='responseStatus'></div>
<div id='XSLDiv'></div>
</body>
</html>