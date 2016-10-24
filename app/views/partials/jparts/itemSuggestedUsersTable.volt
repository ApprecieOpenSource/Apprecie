<script id="itemSuggestedUsersTable" type="text/x-jsrender">
    <table class="table table-striped">
    <thead>
    <tr>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Reference</th>
        <th>Organisation</th>
        <th>Interest Match</th>
        <th>Gender Match</th>
        <th>Age Match</th>
    </tr>
    </thead>
    <tbody>
        <|for items|>
            <tr>
                <td><a href="/people/viewuser/<|:userId|>"><|:firstName|></a></td>
                <td><a href="/people/viewuser/<|:userId|>"><|:lastName|></a></td>
                <td><a href="/people/viewuser/<|:userId|>"><|:reference|></a></td>
                <td><|:organisation|></td>
                <td><|:interestMatch|></td>
                <td><|:genderMatch|></td>
                <td><|:ageMatch|></td>
            </tr>
        <|/for|>
    </tbody>
</table>
</script>