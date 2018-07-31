$.pjax.defaults.timeout = 10000;
$.pjax.defaults.maxCacheLength = 0;

pjax_mode == 1 && $(document).pjax('a[target!=_blank]', '.content');

$(document).on('pjax:send', function() {
    // $('.fakeloader').show();
    layer.load(0, {time: 10*1000,shade: false});
});

$(document).on('pjax:complete', function() {
    // $('.fakeloader').hide();
    layer.closeAll();

    //同时绑定多个时间控件
    lay('.date').each(function () {
        laydate.render({
            elem: this,
            type: 'date',
            trigger: 'click',
        })
    })

    //同时绑定多个时间控件
    lay('.datetime').each(function () {
        laydate.render({
            elem: this,
            type: 'datetime',
            trigger: 'click',
        })
    })




    var $checkboxAll = $(".js-checkbox-all"),
        $checkbox = $("tbody").find("[type='checkbox']").not("[disabled]"),
        length = $checkbox.length,
        i=0;

        //启动icheck
        $(".table [type='checkbox'],.search-form [type='checkbox'] ,[type='radio']").iCheck({
          checkboxClass: 'icheckbox_minimal-blue', radioClass: 'iradio_minimal-blue', increaseArea: '20%',
        });
        
        //全选checkbox
        $checkboxAll.on("ifClicked",function(event){
          if(event.target.checked){
            $checkbox.iCheck('uncheck');
            i=0;
          }else{
            $checkbox.iCheck('check');
            i=length;
          }
        });

        $checkbox.on('ifClicked',function(event){
          event.target.checked ? i-- : i++;
          if(i==length){
            $checkboxAll.iCheck('check');
          }else{
            $checkboxAll.iCheck('uncheck');
          }
        });


    /**
     * PJAX模式重写get请求提交处理
     */
    $('.content').off('click','.ajax-get');
    $('.content').on("click",'.ajax-get',(function(){
        var target;

        if ( $(this).hasClass('confirm') ) {

            if(!confirm('确认要执行该操作吗?')){

                return false;
            }
        }
        if ( (target = $(this).attr('href')) || (target = $(this).attr('url')) ) {

            if ($(this).attr('is-jump') == 'true') {
                
                $.pjax({url: target,container: '.content'});
                
            } else {
                $.get(target).success(function(data){
                    
                    obalertp(data);
                });
            }
        }

        return false;
    }))

    /**
     * PJAX模式重写表单POST提交处理
     */
    $(document).off('click','.ajax-post');
    $(document).on('click','.ajax-post',function(e){
        e.preventDefault();
        try {
            if ($(this).attr("data-fn")) {
                var res = eval($(this).attr("data-fn") + "()");
                if (res === false) {
                    return false;
                }
            }
        } catch (e) {

        }

        var target,query,form;

        var target_form = $(this).attr('target-form');

        var that = this;

        var nead_confirm=false;

        if( ($(this).attr('type')=='submit') || (target = $(this).attr('href')) || (target = $(this).attr('url')) ){

            form = $('.'+target_form);

            if ($(this).attr('hide-data') === 'true'){//无数据时也可以使用的功能
                form = $('.hide-data');
                query = form.serialize();
            }else if (form.get(0)==undefined){
                return false;
            }else if ( form.get(0).nodeName=='FORM' ){

                if ( $(this).hasClass('confirm') ) {

                    if(!confirm('确认要执行该操作吗?')){

                        return false;
                    }
                }
                if($(this).attr('url') !== undefined){
                        target = $(this).attr('url');
                }else{
                        target = form.get(0).action;
                }
                query = form.serialize();
            }else if( form.get(0).nodeName=='INPUT' || form.get(0).nodeName=='SELECT' || form.get(0).nodeName=='TEXTAREA') {

                form.each(function(k,v){
                    if(v.type=='checkbox' && v.checked==true){
                        nead_confirm = true;
                    }
                })

                if ( nead_confirm && $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }

                query = form.serialize();
            }else{

                if ( $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                query = form.find('input,select,textarea').serialize();
            }

            var is_ladda_button =  $(that).hasClass('ladda-button');

            is_ladda_button ? button.start('.ladda-button') : $(that).addClass('disabled').attr('autocomplete','off').prop('disabled',true);

            $.post(target,query).success(function(data){
                $('.modal').modal('hide');
                obalertp(data);

                is_ladda_button ? button.stop('.ladda-button') : $(that).removeClass('disabled').prop('disabled',false);
            });
        }

        return false;
    });
    
    //排序
    $(".sort-text").change(function(){
        
        var val = $(this).val();

        if(!((/^(\+|-)?\d+$/.test(val)) && val >= 0)){
            
            toast.warning('请输入正整数');
            return false;
        }

        $.post($(this).attr("href"),{id:$(this).attr('id'), value:val}, function(data){

            obalertp(data);

            },"json");

    });
    
    //全选|全不选
    $(".checkbox-select-all").click(function(){
        
        var select_status = $(this).find("input").is(":checked");

        var table_input = $(".table").find("input");

        if (select_status) {
            
            table_input.prop("checked", true);
            
        } else {
            table_input.prop("checked", false);
        }

    });
    
    //批量处理
    $('.batch_btn').click(function(){
        var that = this;
            var $checked = $('.table input[type="checkbox"]:checked:not(.js-checkbox-all)');
            debugger;
            if($checked.length != 0){

                layer.confirm('您确认批量操作吗？', function (index) {
                    var field = $(that).attr("data-field") || 'status';
                    var params = {ids: $checked.serializeArray()};
                    params[field] = $(that).attr("value");
                    $.extend(params, $(that).data());

                    $.post($(that).attr("href"), params, function (data) {

                        obalertp(data);

                    }, "json");
                    layer.close(index);
                }, function (index) {
                    layer.close(index);
                });

                    // if(confirm('您确认批量操作吗？')){
                    //   var field=$(this).attr("data-field")||'status';
                    //   var params = {ids: $checked.serializeArray()};
                    //   params[field] =  $(this).attr("value");
                    //   $.extend( params, $(this).data());
                    //
                    //     $.post($(this).attr("href"),params, function(data){
                    //
                    //         obalertp(data);
                    //
                    //     },"json");
                    // }
            } else {
                
                toast.warning('请选择批量操作数据');
            }
            return false;
    });
    
    //搜索功能
    $("#search").click(function(){
        
        var url = searchFormUrl(this);

        $.pjax({url: url,container: '.content'});
    });

    //回车搜索
    $(".search-input").keyup(function(e){
        if(e.keyCode === 13){
                $("#search").click();
                return false;
        }
    });

    //导出功能
    $('.export').click(function () {
        if ($(this).attr('disabled')) {
            return false;
        }
        window.location.href = searchFormUrl($('.export'));
    });

    //设置空列表时,td的列数
    var sum=0;
    $("tr.odd").parents("table").eq(0).find("th").each(function(){
        var colspan = $(this).attr('colspan')||1;
        sum+=colspan;
    })
    $("tr.odd td[valign='top']").attr("colspan",sum );
});

/**
 * PJAX模式重写跳转处理
 */
var obalertp = function (data) {

    data.code ? toast.success(data.msg) : toast.error(data.msg);
    data.url && $.pjax({url: data.url,container: '.content'});
    //去除模态框覆盖层影响
    data.url&&$(".modal-backdrop.fade.in").remove();
    // data.url&& $(".modal").modal("hide");
};

/**
 * PJAX模式左侧菜单优化点击显示
 */
$('.sidebar-menu li').click(function () {
    if ($(this).find('ul').length <= 0) {
        $(this).siblings('li').removeClass('active');
        $(this).addClass('active');
    }
});

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
