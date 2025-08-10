$(document).on("submit", "form", function(e) {
    $('.required_field').removeClass('validation-failed');
    $count = 0;
    var obj = $(this).closest('form').find('.required_field');
    var i=1;
    
    obj.each(function() {
        $val = $(this).val();
        if ($val && $val!=='0') { 
            
        } else {
            $count = $count + 1;
            $(this).css("border", "red solid 1px");
            $(this).next('.select2').css("border", "red solid 1px");
            $(this).addClass('validation-failed'+i);
        }
        i++;
    });

    
    if ($count) {
        swal({
            title: "Oops",
            text: "Please fill all Required fields marked as *",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        });
        return false;
    } else {
        return true;
    }
});

$(".sort_function").on('click', function(event) {
    $value = $(this).data("value_search");
    $(this).addClass("important");
    $order_by = $(this).data("order_by");
    let searchParams = new URLSearchParams(window.location.search)
    let param = searchParams.get('orderby')
    let page_param = searchParams.get('page')
    let search_param = searchParams.get('search')
    let filter_param = searchParams.get('filter')
    let tab_param = searchParams.get('tab')
    if (param == "ASC") {
        $order_by = "DESC"
    }
    if (page_param) {
        $sort_string = "?page=" + page_param + "&name=" + $value + "&orderby=" + $order_by;
    } else {
        $sort_string = "?name=" + $value + "&orderby=" + $order_by;
    }
    if (search_param) {
        $sort_string = $sort_string + "&search=" + search_param;
    }
    if (tab_param) {
        $sort_string = $sort_string + "&tab=" + tab_param;
    }
    if (filter_param) {
        $sort_string = $sort_string + "&filter=" + filter_param;
    }
    $current_url = String(window.location.pathname);
    $sort_url = $current_url + $sort_string
    window.location = $sort_url;
});

let searchParams = new URLSearchParams(window.location.search)
let param = searchParams.get('orderby')
if (param == "ASC") {
    $(".sort_function").addClass("asc");
    $(".sort_function").removeClass("desc");
} else if (param == "DESC") {
    $(".sort_function").addClass("desc");
    $(".sort_function").removeClass("asc");
}
