/**
 * Created by Daniel on 08/12/14.
 */
function getPortalQuota(portalId,organisationId){
    return $.ajax({
        url: "/api/portalquota",
        type: 'post',
        dataType: 'json',
        cache: false,
        data:{"portalId":portalId,"organisationId":organisationId,'CSRF_SESSION_TOKEN': 'CSRF_SESSION_TOKEN'}
    });
}