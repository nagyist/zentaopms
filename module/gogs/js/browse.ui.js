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
        var browseProjectLink = $.createLink('gogs', 'view', 'gogsID=' + row.data.id);
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
 * @param  int    gogsID
 * @access public
 * @return void
 */
window.confirmDelete = function(gogsID)
{
    if(window.confirm(confirmDelete))
    {
        $.ajaxSubmit({url: $.createLink('gogs', 'delete', 'gogsID=' + gogsID)});
    }
}