window.createSortLink = function(col)
{
    let sort = col.name + '_asc';
    if(sort == orderBy) sort = col.name + '_desc';
    return sortLink.replace('{orderBy}', sort);
}

/**
 * Compute work days.
 *
 * @access public
 * @return void
 */
function computeWorkDays(currentID)
{
    isBactchEdit = false;
    if(currentID)
    {
        index = currentID.replace(/\w*\[|\]/g, '');
        if(!isNaN(index)) isBactchEdit = true;
    }

    let beginDate, endDate;
    if(isBactchEdit)
    {
        beginDate = $('#begins\\[' + index + '\\]').val();
        endDate   = $('#ends\\[' + index + '\\]').val();
    }
    else
    {
        beginDate = $('#begin').val();
        endDate   = $('#end').val();
    }

    if(beginDate && endDate)
    {
        if(isBactchEdit)  $('#dayses\\[' + index + '\\]').val(computeDaysDelta(beginDate, endDate));
        if(!isBactchEdit) $('#days').val(computeDaysDelta(beginDate, endDate));
    }
    else if($('input[checked="true"]').val())
    {
        computeEndDate();
    }
}


/**
 * Compute the end date for project.
 *
 * @param  int    $delta
 * @access public
 * @return void
 */
function computeEndDate()
{
    let delta     = $('input[name^=delta]:checked').val();
    let beginDate = $('#begin').val();
    if(!beginDate) return;

    delta     = parseInt(delta);
    beginDate = convertStringToDate(beginDate);
    if((delta == 7 || delta == 14) && (beginDate.getDay() == 1))
    {
        delta = (weekend == 2) ? (delta - 2) : (delta - 1);
    }

    endDate = zui.calculateTimestamp((delta - 1) * 24 * 60, 'minute', true, beginDate.getTime());
    endDate = zui.formatDate(endDate, 'yyyy-MM-dd');

    $('#end').val(endDate);
    computeWorkDays();
}

/**
 * Convert a date string like 2011-11-11 to date object in js.
 *
 * @param  string $date
 * @access public
 * @return date
 */
function convertStringToDate(dateString)
{
    dateString = dateString.split('-');
    return new Date(dateString[0], dateString[1] - 1, dateString[2]);
}

/**
 * Compute delta of two days.
 *
 * @param  string $date1
 * @param  string $date2
 * @access public
 * @return int
 */
function computeDaysDelta(date1, date2)
{
    date1 = convertStringToDate(date1);
    date2 = convertStringToDate(date2);
    delta = (date2 - date1) / (1000 * 60 * 60 * 24) + 1;

    let weekEnds = 0;
    for(i = 0; i < delta; i++)
    {
        if((weekend == 2 && date1.getDay() == 6) || date1.getDay() == 0) weekEnds ++;
        date1 = date1.valueOf();
        date1 += 1000 * 60 * 60 * 24;
        date1 = new Date(date1);
    }
    return delta - weekEnds;
}

/**
 * Load branches.
 *
 * @param  event  $e
 * @access public
 * @return void
 */
function loadBranches(e)
{
    /* When selecting a product, delete a plan that is empty by default. */
    $("#planDefault").remove();

    let $product = $(e.target);
    $(".productsBox select[name^='products']").each(function()
    {
        let productID = $(this).val();
        if($product.val() != 0 && $product.val() == $(this).val() && $product.attr('id') != $(this).attr('id') && !multiBranchProducts[$product.val()])
        {
            zui.Modal.alert(errorSameProducts);
            $product.val(0);
            return false;
        }
    });

    let $formRow  = $product.closest('.form-row');
    let index     = $formRow.find('select').first().attr('name').match(/\d+/)[0];
    let oldBranch = $(e.target).attr('data-branch') !== undefined ? $product.attr('data-branch') : 0;

    if(!multiBranchProducts[$product.val()])
    {
        $formRow.find('.form-group').last().find('select').val('');
        $formRow.find('.form-group').eq(1).addClass('hidden');
    }

    $.get($.createLink('branch', 'ajaxGetBranches', "productID=" + $product.val() + "&oldBranch=" + oldBranch + "&param=active&projectID=" + projectID + "&withMainBranch=true"), function(data)
    {
        if(data)
        {
            $formRow.find("select[name^='branch']").replaceWith(data);
            $formRow.find('.form-group').eq(0).removeClass('w-1/2').addClass('w-1/4');
            $formRow.find('.form-group').eq(1).removeClass('hidden');
            $formRow.find("select[name^='branch']").attr('multiple', '').attr('name', 'branch[' + index + '][]').attr('id', 'branch' + index).attr('onchange', "loadPlans('#products" + index + "', this)");
        }

        if(typeof isStage != 'undefined' && isStage == true)
        {
            $formRow.find("select[name^='branch'] option").attr('selected', 'selected');
            $formRow.find("select[name^='branch']").trigger('chosen:updated');
            $formRow.find("div[id^='branch']").addClass('chosen-disabled');
        }
    });

    let branch = $('#branch' + index);
    loadPlans(e.target, branch);
}

/**
 * Load plans.
 *
 * @param  obj $product
 * @param  obj $branchID
 * @access public
 * @return void
 */
window.loadPlans = function(product, branch)
{
    let productID = $(product).val();
    let branchID  = $(branch).val() == null ? 0 : '0,' + $(branch).val();
    let planID    = $(product).attr('data-plan') !== undefined ? $(product).attr('data-plan') : 0;
    let index     = $(product).attr('name').match(/\d+/)[0];

    $.get($.createLink('product', 'ajaxGetPlans', "productID=" + productID + '&branch=' + branchID + '&planID=' + planID + '&fieldID&needCreate=&expired=unexpired,noclosed&param=skipParent,multiple'), function(data)
    {
        if(data)
        {
            $("div#plan" + index).find("select[name^='plans']").replaceWith(data);
            $("div#plan" + index).find('.chosen-container').remove();
            $("div#plan" + index).find('select').attr('name', 'plans[' + productID + ']' + '[]').attr('id', 'plans' + productID);
        }
    });
}

/**
 * Add new line for link product.
 *
 * @param  obj $obj
 * @access public
 * @return void
 */
function addNewLine(e)
{
    const obj     = e.target;
    const newLine = $(obj).closest('.form-row').clone();
    let index     = 0;
    $(".productsBox select[name^='products']").each(function()
    {
        let id = $(this).attr('name').replace(/[^\d]/g, '');

        id = parseInt(id);
        id ++;

        index = id > index ? id : index;
    })

    newLine.find('.addLine').on('click', addNewLine);
    newLine.find('.removeLine').on('click', removeLine);
    newLine.find("select[name^='products']").on('change', loadBranches);
    newLine.find("select[name^='branch']").on('change', "loadPlan('#products" + index + "', this)");

    newLine.addClass('newLine');
    newLine.find('.linkProduct > .form-label').html('');
    newLine.find('.removeLine').removeClass('hidden');
    newLine.find("select[name^='products']").attr('name', 'products[' + index + ']').attr('id', 'products' + index).val('');
    newLine.find("select[name^='plans']").attr('name', 'plans[' + index + '][' + 0 + '][]');
    newLine.find("select[name^='plans']").empty();
    newLine.find("select[name^='branch']").val('');
    newLine.find('.form-group').eq(0).addClass('w-1/2').removeClass('w-1/4');
    newLine.find('.form-group').eq(1).addClass('hidden');
    newLine.find("div[id^='plan']").attr('id', 'plan' + index);

    $(obj).closest('.form-row').after(newLine);
}

/**
 * Remove line for link product.
 *
 * @param  obj    e
 * @access public
 * @return void
 */
function removeLine(e)
{
    const obj = e.target;
    $(obj).closest('.form-row').remove();
}
