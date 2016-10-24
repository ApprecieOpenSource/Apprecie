<script id="vaultlist" type="text/x-jsrender">
<div class="col-sm-12">
<|for items|>
<div class="panel panel-default">
  <div class="panel-body">
    <div class="row">
        <div class="col-sm-3">
            <|if isByArrangement=="1"|>
                <a style="text-decoration:none" href="/vault/arranged/<|:itemId|>">
            <|else|>
                <a style="text-decoration:none" href="/vault/event/<|:itemId|>">
            <|/if|>
            <img src="<|:image|>" class="img-responsive img-thumbnail" style="width:100%">
            </a>
        </div>
        <div class="col-sm-9">
            <|if isByArrangement=="1"|>
                <a style="text-decoration:none" href="/vault/arranged/<|:itemId|>">
            <|else|>
                <a style="text-decoration:none" href="/vault/event/<|:itemId|>">
            <|/if|>
            <h4 style="font-family: 'Quicksand', sans-serif; font-weight: normal; font-size:20px; margin-right:150px;">
                <|:itemTitle|><br/>
                <span style="font-size:16px;"><|:startDate|></span>
            </h4>
            </a>
            <p>
                <|if distance != null|>
                    <span class="label label-info" style="font-size:11px"><i class="fa fa-map-marker"></i> <|:distance|> Miles</span>
                <|/if|>
                <|if unitPrice=="0"|>
                     <span class="label label-info"  style="font-size:11px"><|:itemType|></span>
                <|/if|>
                <|if suggestions!=0 |>
                    <span class="label label-info"  style="font-size:11px"><i class="fa fa-user"></i> <|:suggestions |> People Matches</span>
                <|/if|>
            </p>
            <p style="margin-top:25px;"><|:shortSummary|></p>
            <div style="position: absolute;top: 0;right: 15;">
                <img src="<|:brandImage|>" style="max-width:150px; max-height:70px;">
            </div>
        </div>
    </div>
    <|if userMatch!=null|>
        <div class="row">
            <div class="col-sm-12">
                <h2><|:relevancy|>% Relevancy</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <h3>Interest Matches <span class="pull-right"><|:userMatch.interestRelevancy|>%</span></h3>
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <td>
                                Total interest matches
                            </td>
                            <td>
                                <|:userMatch.userCategoryMatches|>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                People with interests set
                            </td>
                            <td>
                                <|:userMatch.usersWithInterests|>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                People searched
                            </td>
                            <td>
                                <|:userMatch.totalUsers|>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                People excluded
                            </td>
                            <td>
                                <|:userMatch.interestExcluded|>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-sm-4">
                <h3>Gender Matches <span class="pull-right"><|:userMatch.genderRelevancy|>%</span></h3>
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <td>
                                Total gender matches
                            </td>
                            <td>
                                <|:userMatch.userGenderMatches|>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                People with gender set
                            </td>
                            <td>
                                <|:userMatch.usersWithGender|>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                People searched
                            </td>
                            <td>
                                <|:userMatch.totalUsers|>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                People excluded
                            </td>
                            <td>
                                <|:userMatch.genderExcluded|>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-sm-4">
                <h3>Age Matches <span class="pull-right"><|:userMatch.ageRelevancy|>%</span></h3>
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <td>
                                Total age matches
                            </td>
                            <td>
                                <|:userMatch.userAgeMatches|>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                People with age set
                            </td>
                            <td>
                                <|:userMatch.usersWithAge|>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                People searched
                            </td>
                            <td>
                                <|:userMatch.totalUsers|>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                People excluded
                            </td>
                            <td>
                                <|:userMatch.ageExcluded|>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <|/if|>
  </div>
</div>
<|/for|>
</div>
</script>
