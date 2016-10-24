<script src="/js/compiled/public/js/raw/library/pagination.min.js"></script>
<script src="/js/compiled/public/js/raw/library/people.min.js"></script>
<script src="/js/compiled/public/js/raw/widgets/userfinder/multiselect.min.js"></script>
<style>
    .clickable{
        cursor: pointer;
    }
    .invite-sent{
        background-color: lightgreen;
    }
</style>
<div class="row">
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Available Users</h5>
            </div>
            <div class="ibox-content no-padding">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                    </tr>
                    </thead>
                    <tbody id="available-tbl">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Selected Users</h5>
            </div>
            <div class="ibox-content no-padding">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                    </tr>
                    </thead>
                    <tbody id="selected-tbl">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>