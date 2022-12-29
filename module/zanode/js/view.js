var ifm = document.getElementById("vncIframe");
ifm.height=$('.vnc-detail').css('height')
$('.vnc-mask').click(function(){
    window.open(createLink('zanode', 'getVNC', "id=" + nodeID))
})
$('.vnc-mask').css('width', $('.vnc-detail').css('width'));
$('.vnc-mask').css('height', $('.vnc-detail').css('height'));


$('#checkServiceStatus').click(function(){
    $.get(createLink('zanode', 'ajaxGetServiceStatus', 'nodeID=' + nodeID), function(response)
    {
        var resultData = JSON.parse(response);
        var isSuccess = true

        for (var key in resultData.data)
        {
            if(key == "ZTF")
            {
                if(resultData.data[key] == 'ready')
                {
                    $('.dot-ztf').removeClass("text-danger")
                    $('.dot-ztf').addClass("text-success")
                }
                else{
                    $('.dot-ztf').removeClass("text-success")
                    $('.dot-ztf').addClass("text-danger")
                }

                if(resultData.data[key] == 'ready' || resultData.data[key] == 'not_available')
                {
                    $('.ztf-status').text(zanodeLang.init[resultData.data[key]])
                    $('.ztf-install').text(zanodeLang.reinstall);
                }
                else
                {
                    if(resultData.data[key] == 'unknown')
                    {
                        $('.ztf-status').text(zanodeLang.init.unknown)
                    }
                    else
                    {
                        $('.ztf-status').text(zanodeLang.initializing)
                    }
                    $('.ztf-install').text(zanodeLang.install);
                }
            }
            else
            {
                if(resultData.data[key] == 'ready')
                {
                    $('.dot-zenagent').removeClass("text-danger")
                    $('.dot-zenagent').addClass("text-success")
                }
                else{
                    $('.dot-zenagent').removeClass("text-success")
                    $('.dot-zenagent').addClass("text-danger")
                }
                $('.zenagent-status').text(zanodeLang.init[resultData.data[key]])
                if(resultData.data[key] == 'ready')
                {
                    $('.node-init-install').show();
                }
                else
                {
                    if(resultData.data[key] == 'unknown')
                    {
                        $('.ztf-zenagent').text(zanodeLang.init.unknown)
                    }
                    else
                    {
                        $('.zenagent-status').text(zanodeLang.initializing)
                    }
                }
            }
            if(resultData.data[key] !== 'ready')
            {
                isSuccess = false
            }
        };

        if(!isSuccess)
        {
            // $('.init-fail').show();
            $('.init-success').hide();
        }
        else
        {
            $('.init-success').show();
            // $('.init-fail').hide();
        }
    });
    return
})

$('.node-init-install').on('click', function(){
    $(this).addClass('load-indicator loading');
    var link = $(this).data('href')
    $.get(link, function(response)
    {
        $(this).removeClass('load-indicator');
        $(this).removeClass('loading');
        $('#checkServiceStatus').trigger("click")
    })
})

$('.btn-init-copy').live('click', function()
{
    var copyText = $('#initBash');
    copyText.show();
    copyText .select();
    document.execCommand("Copy");
    copyText.hide();
    $('.btn-init-copy').tooltip({
        trigger: 'click',
        placement: 'bottom',
        title: zanodeLang.copied,
        tipClass: 'tooltip-success'
    });

    $(this).tooltip('show');
    var that = this;
    setTimeout(function()
    {
        $(that).tooltip('hide')
    }, 2000)
})

$(function(){
    $('#checkServiceStatus').trigger("click")
})