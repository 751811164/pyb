ids = [];
items = [];
$(function () {

    var $checkboxAll = $(".js-checkbox-all"), $checkbox = $("tbody").find("[type='checkbox']").not("[disabled]"), length = $checkbox.length, i = 0;

    //启动icheck
    $((".table [type='checkbox']")).iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
    });

    //全选checkbox
    $checkboxAll.on("ifClicked", function (event) {
        if (event.target.checked) {
            ids = [];
            items = [];
            $checkbox.iCheck('uncheck');
            i = 0;
        }
        else {
            $checkbox.each(function (i, e) {
                ids.push(this.value);
                var item=$.parseJSON(this.dataset.item);
                item.id=null;
                items.push(item);
            })
            $checkbox.iCheck('check');
            i = length;
        }
    });

    $checkbox.on('ifClicked', function (event) {
        if (event.target.checked) {
            ids.splice(ids.indexOf(event.target.value), 1);
            items.splice(ids.indexOf(event.target.value), 1);
            i--
        }
        else {
            i++;
            ids.push(event.target.value);
            var item=$.parseJSON(event.target.dataset.item);
            item.id=null;
            items.push(item);

        }
        if (i == length) {
            $checkboxAll.iCheck('check');
        }
        else {
            $checkboxAll.iCheck('uncheck');
        }
    });



    //搜索功能
    $("#search").click(function () {
        window.location.href = searchFormUrl(this);
    });

    $("#category a[data-id='"+getQueryString("cid")+"']").addClass("active");

    //设置空列表时,td的列数
    $("tr.odd td[valign='top']").attr("colspan", $("tr.odd").parents("table").eq(0).find("th").length);



})


/**
 * 搜索表单url
 */
var searchFormUrl = function (obj) {
    var url = $(obj).attr('url');
    var query = $('.search-form').find('input').serialize();
    var str = $('.search-form').find('select').serialize();
    query += '&' + str.replace(/=-\d*/g, '=');

    query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g, '');
    query = query.replace(/^&/g, '');
    if (url.indexOf('?') > 0) {
        url += '&' + query;
    }
    else {
        url += '?' + query;
    }

    return url;
};


function getQueryString (name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
}
