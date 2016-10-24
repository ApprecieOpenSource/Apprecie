<script id="addGroupUsers" type="text/x-jsrender">
<|for items|>
    <tr id="userlist-<|:userid|>" onclick="toggleRow(<|:userid|>)">
        <td class="hidden-xs">

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