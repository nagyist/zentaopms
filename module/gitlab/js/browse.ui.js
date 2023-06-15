window.createSortLink = function(col)
{
    var sort = col.name + '_asc';
    if(sort == orderBy) sort = col.name + '_desc';

    return sortLink.replace('{orderBy}', sort);
}

window.renderCell = function(result, {col, row})
{
    if(col.name === 'name')
    {
        var browseProjectLink = $.createLink('gitlab', 'browseProject', 'gitlabID=' + row.data.id);
        if(canBrowseProject) result[0] = {html:'<a href="' + browseProjectLink + '">' + row.data.name + '</a>', style:{flexDirection:"column"}};

        return result;
    }

    if(col.name === 'url')
    {
        result[0] = {html:'<a href="' + row.data.url + '" target="_blank">' + row.data.url + '</a>', style:{flexDirection:"column"}};

        return result;
    }

    return result;
};

/**
 * 提示并删除版本。
 * Delete release with tips.
 *
 * @param  int    gitlabID
 * @access public
 * @return void
 */
window.confirmDelete = function(gitlabID)
{
    zui.Modal.confirm({message: confirmDelete, icon:'icon-info-sign', iconClass: 'warning-pale rounded-full icon-2x'}).then((res) =>
    {
        if(res) $.ajaxSubmit({url: $.createLink('gitlab', 'delete', 'gitlabID=' + gitlabID)});
    });
}
