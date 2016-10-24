<script id="alertmessage" type="text/x-jsrender">
<|for items|>
<tr <|if bold==true|>style="font-weight:bold;"<|/if|>>
    <td>
        <|:senderName|>
    </td>
    <td>
        <|:recipientName|>
    </td>
    <td>
        <a href="/alertcentre/view/<|:threadId|>"><|:firstMessage.title|></a>
    </td>
    <td>
        <|:firstMessage.sent|>
    </td>
    <td>
    <|if firstMessage.referenceItem!=null|>
        <|:item.title|>
    <|/if|>
    </td>
    </tr>
<|/for|>
</script>