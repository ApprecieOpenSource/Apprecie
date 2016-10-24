<script id="cusers" type="text/x-jsrender">
<|for items|>
    <tr id="userlist-<|:userid|>" onclick="toggleRow(<|:userid|>)">
        <td><|:profile.firstname|> <|:profile.lastname|></td>
        <td><|:reference|></td>
        <td>
        <div  data-toggle="tooltip" data-placement="top" title="<|:groups|>">
            <|:groupsCount|>
        </div>
        </td>
        <td><|:organisation|></td>
        <td><|:role|></td>
        <td><|:account|></td>
        <td><|:login|></td>
    </tr>
<|/for|>
</script>