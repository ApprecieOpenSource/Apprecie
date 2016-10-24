<script>
    function getError(id){
        var errorString=null;
        switch(id){
            case 1:
                errorString=("<?= _g('Sorry, there were no results'); ?>");
                break;
        }
        return errorString;
    }
</script>