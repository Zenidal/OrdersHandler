$(function () {
    $('#repairOrder_company').ready(
        function () {
            $("#repairOrder_company option:first").attr("selected", "selected");
            $.get(
                "/getPlacesByCompanyName",
                {
                    name: $('#repairOrder_company option:selected').text()
                },
                onAjaxSuccess);
        }
    )

    $('#repairOrder_company').change(
        function () {
            $.get(
                "/getPlacesByCompanyName",
                {
                    name: $('#repairOrder_company option:selected').text()
                },
                onAjaxSuccess);
        }
    )

    function onAjaxSuccess(data){
        json = jQuery.parseJSON(data);
        $("#repairOrder_place").empty();
        for(var i = 0; i < json.length; i++){
            $("#repairOrder_place").prepend($('<option value='+ json[i][0] +'>'+ json[i][1] +'</option>'));
        }
    }
});