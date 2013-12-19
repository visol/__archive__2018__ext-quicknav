$(function() {
    // load the quick navigation data
    var dataUrl = '<f:uri.page absolute="TRUE" pageUid="455" pageType="999" />';
    var data;
    $.getJSON(
        dataUrl,
        function(json) {
            data = json;
            initializeQuickNavigation(json)
            console.log(data);
        }
    );

    $('.quicknav-select').change(function(e) {
        // the ID of the selected category/quickNavigationItem
        var selectedUid = $(this).children(':selected').first().val();
        // its parent uid
        var selectedParentUid = $(this).children(':selected').first().attr('data-parent-uid');
        // the target url of a quickNavigationItem
        var selectedTargetUrl = $(this).children(':selected').first().attr('data-target-url');
        // the navigation level that was selected
        var selectedLevel = $(e.target).attr('data-level');
        if (selectedUid > 0) {
            // the next navigation level from the current
            var nextLevelNumber = parseInt(selectedLevel) + 1;
            var $nextLevel = $('#level' + nextLevelNumber);
            // remove all options except for the placeholder
            $nextLevel.find('option').not($('.optionPlaceholder')).remove();
            if (parseInt(selectedParentUid) > 0) {
                // item/category has a category/parent category => second and third level
                if (typeof(selectedTargetUrl) === 'undefined') {
                    // second level
                    if (data[selectedParentUid]['children'][selectedUid].hasOwnProperty('targetUrl')) {
                        // it is a quickNavigationItem, therefore we jump to the URL
                        jumpToTargetUrl(selectedTargetUrl);
                    } else if (data[selectedParentUid]['children'][selectedUid].hasOwnProperty('children')) {
                        // we have children, so we build the next navigation level
                        $.each(data[selectedParentUid]['children'][selectedUid].children, function(key, item){
                            var optionElements = buildSelectOptions(item);
                            $nextLevel.append(optionElements);
                        })
                    }
                } else {
                    // it is a quickNavigationItem, therefore we jump to the URL
                    jumpToTargetUrl(selectedTargetUrl);
                }
            } else if (typeof(selectedTargetUrl) === 'undefined') {
                // item/category has no parent => first level
                if (data[selectedUid].hasOwnProperty('targetUrl')) {
                    // it is a quickNavigationItem, therefore we jump to the URL
                    jumpToTargetUrl(selectedTargetUrl);
                } else if (data[selectedUid].hasOwnProperty('children')) {
                    // we have children, so we build the next navigation level
                    $.each(data[selectedUid].children, function(key, item){
                        var optionElements = buildSelectOptions(item);
                        $nextLevel.append(optionElements);
                    })
                }
            } else {
                // no parent => first level but a target URL => jump to URL
                jumpToTargetUrl(selectedTargetUrl);
            }
        }
    })
});

function buildSelectOptions(item) {
    var optionElement = $("<option></option>").attr("value", item.uid).attr("data-parent-uid", item.parentUid).text(item.name);
    if (item.targetUrl !== typeof undefined) {
        optionElement.attr("data-target-url", item.targetUrl);
    }
    return optionElement;
}

function jumpToTargetUrl(targetUrl) {
    window.location.href = targetUrl;
}

function initializeQuickNavigation(data) {
    var iterator = 0;
    $.each(data, function(key, item) {
        var firstLevelOptionElements = buildSelectOptions(item);
        $('#level1').append(firstLevelOptionElements).children('.optionPlaceholder').remove();
        if (iterator === 0) {
            $.each(item.children, function(key, item) {
                var secondLevelOptionElements = buildSelectOptions(item);
                $('#level2').append(secondLevelOptionElements);
            });
        }
        iterator++;
    });
}