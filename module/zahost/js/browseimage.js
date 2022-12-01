var interval;

$(function () {
    updateProgressInterval();
});

function updateProgressInterval() {
    updateProgress();
    interval = setInterval(function () 
    {
        updateProgress();
    }, 3000);
}

function updateProgress() {
    $.get(createLink('zahost', 'ajaxImageDownloadProgress', 'hostID=' + hostID)).done(function (response) 
    {
        var result = JSON.parse(response);
        var statusList = result.data;

        var hasInprogress = false;
        for (var imageID in statusList) {
            if (statusList[imageID].statusCode) {
                if (statusList[imageID].statusCode == 'inprogress' || statusList[imageID].statusCode == 'created') 
                {
                    hasInprogress = true;
                    $('.image-download-' + imageID).addClass('disabled');
                    $('.image-cancel-' + imageID).removeClass('disabled');
                }
                 else if (statusList[imageID].statusCode == 'completed') 
                 {
                    $('.image-download-' + imageID).addClass('disabled');
                    $('.image-cancel-' + imageID).addClass('disabled');
                } 
                else 
                {
                    $('.image-download-' + imageID).removeClass('disabled');
                    $('.image-cancel-' + imageID).addClass('disabled');
                }
                if(statusList[imageID].progress != '')
                {
                    $('.image-status-' + imageID).text(statusList[imageID].status);
                    $('.image-progress-' + imageID).text(statusList[imageID].progress);
                }
            }
        }
        if (!hasInprogress) 
        {
            clearInterval(interval)
        }
    });
}