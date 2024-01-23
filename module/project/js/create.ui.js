$(function()
{
    setWhite();
});

/**
 * 移除复制项目产生的某个控件的提示信息。
 * Remove the tip of a control generated by copy project.
 *
 * @access public
 * @return void
 */
function removeTips()
{
    const $formGroup = $(this).closest('.form-group');
    $formGroup.removeClass('has-warning');
    $formGroup.find('.has-warning').removeClass('has-warning');
    $formGroup.find('.form-tip').remove();
}

/**
 * 移除复制项目时产生的所有控件的提示信息。
 * Remove all tips generated when copying projects.
 *
 * @access public
 * @return void
 */
function removeAllTips()
{
    $('.has-warning').removeClass('has-warning');
    $('.text-warning').remove();
}

$(document).on('click', '#copyProjects button', function()
{
    const copyProjectID = $(this).hasClass('primary-outline') ? 0 : $(this).data('id');
    setCopyProject(copyProjectID);
    zui.Modal.hide();
});

/**
 * Set copy project.
 *
 * @param  int $copyProjectID
 * @access public
 * @return void
 */
function setCopyProject(copyProjectID)
{
    const programID = $('[name=parent]').val();
    loadPage($.createLink('project', 'create', 'model=' + model + '&programID=' + programID + '&copyProjectID=' + copyProjectID));
}

/**
 * Fuzzy search projects by project name.
 *
 * @access public
 * @return void
 */
$(document).on('keyup', '#projectName', function()
{
    var name = $(this).val();
    name = name.replace(/\s+/g, '');
    $('#copyProjects .project-block').hide();

    if(!name) $('#copyProjects .project-block').show();
    $('#copyProjects .project-block').each(function()
    {
        if($(this).text().includes(name) || $(this).data('pinyin').includes(name)) $(this).show();
    });
});

/**
 * Set acl list when change program.
 *
 * @access public
 * @return void
 */
window.setParentProgram = function()
{
    const programID = $('[name=parent]').val();
    const link      = $.createLink('project', 'create', 'model=' + model + '&program=' + programID);

    if(programID) $('#linkProduct .input-group').addClass('required');
    if(programID == 0) $('#linkProduct .input-group.required').removeClass('required');

    loadPage(link, '#aclList');
    $('select[name^=whitelist]').closest('.form-row').removeClass('hidden')
}

/* Init product. */
window.waitDom('[name^=products]', function()
{
    if($('.newProductBox').length > 0) toggleStageBy($('[name^=products]').eq(0));
});

window.toggleStageBy = function()
{
    let chosenProducts = 0;
    $(".productsBox [name^='products']").each(function()
    {
        if($(this).val() > 0) chosenProducts ++;
    });

    if(chosenProducts > 1)  $('.stageByBox').removeClass('hidden');
    if(chosenProducts <= 1) $('.stageByBox').addClass('hidden');
}
