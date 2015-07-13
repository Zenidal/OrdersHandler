$(function () {
    $('#repairOrder_company').change(
        function () {
            $.get(
                "/getPlacesByCompanyName",
                {
                    name: $('#repairOrder_company option:selected').text()
                },
                onAjaxSuccess);
        }
    );

    function onAjaxSuccess(data){
        json = jQuery.parseJSON(data);
        $("#repairOrder_place").empty();
        for(var i = 0; i < json.length; i++){
            $("#repairOrder_place").prepend($('<option value='+ json[i][0] +'>'+ json[i][1] +'</option>'));
        }
    }
});

function userShow(id){
    window.location.href = '/manager/users/' + id;
}

function repairOrderShow(id){
    window.location.href = '/repair_orders/' +id;
}

function placeShow(id){
    window.location.href = '/manager/places/' +id;
}

function companyShow(id){
    window.location.href = '/manager/companies/' +id;
}

function assignOrder(orderId, userId){
    href = '/repair_orders/' + orderId + '/assign/' + userId;
    $('#assign-order-href').attr('href', href);
    $('#assign-modal').modal('show');
}