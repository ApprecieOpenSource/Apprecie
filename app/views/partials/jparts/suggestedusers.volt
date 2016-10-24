<script id="suggestedusers" type="text/x-jsrender">
<|for items|>
    <tr id="userlist-<|:userid|>" onclick="toggleRow(<|:userid|>)">
        <td class="hidden-xs">
        <|if suggested==1|>
        <span class="label label-info">Category Match</span>
        <|/if|>
        </td>
        <td><|:profile.firstname|> <|:profile.lastname|></td>
        <td><|:reference|></td>
        <td><|:groups|></td>
        <td><|:organisation|></td>
        <td><|:profile.email|></td>
        <td><|:role|></td>
        <td><|:account|></td>
        <td><|:login|></td>
    </tr>
<|/for|>
</script>