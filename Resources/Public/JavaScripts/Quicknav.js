$(function() {
	if (typeof(dataUrl) !== 'undefined') {
		// load the quick navigation data
		var data;
		$.getJSON(
			dataUrl,
			function(json) {
				data = json;
				prepareQuickNavigation(json)
			}
		);

		$('.quicknav-select').change(function(e) {
			// the ID of the selected category/quickNavigationItem
			var selectedUid = $(this).children(':selected').first().val();
			// the target url of a quickNavigationItem
			var selectedTargetUrl = $(this).children(':selected').first().attr('data-target-url');
			// the navigation level that was selected
			var selectedLevel = $(e.target).attr('data-level');

			if (typeof(selectedUid) !== 'undefined') {
				if (selectedUid.split('-')[0] !== 'item') {
					// the next navigation level from the current
					var nextLevelNumber = parseInt(selectedLevel) + 1;
					var $nextLevel = $('#level' + nextLevelNumber);

					// the next next navigation level from the current
					var nextNextLevelNumber = nextLevelNumber + 1;
					var $nextNextLevel = $('#level' + nextNextLevelNumber);

					// remove all options except for the placeholder
					$nextLevel.find('option').remove();

					nextLevelSelectOptions = findByLevelAndParentUid(nextLevelNumber, selectedUid);
					var firstOptionOfNewSelection = nextLevelSelectOptions[Object.keys(nextLevelSelectOptions)[0]];
					if (typeof(firstOptionOfNewSelection.attr('data-target-url')) !== 'undefined') {
						// the first item of a new selection is a link item --> we need to prepend a placeholder
						$nextLevel.append($('.optionPlaceholder', '#quicknav-placeholders').first().clone());
						$nextNextLevel.attr('disabled', 'disabled');
					} else {
						// the first item of a new selection is a category, so we open the items in the next level
						$nextNextLevel.find('option').not($('.optionPlaceholder')).remove();
						$nextNextLevelSelectOptions = findByLevelAndParentUid(nextNextLevelNumber, firstOptionOfNewSelection.val());
						$nextNextLevel.append($nextNextLevelSelectOptions).removeAttr('disable');
					}
					$nextLevel.append(nextLevelSelectOptions);

				} else {
					// a link
					jumpToTargetUrl(selectedTargetUrl);
				}


			}
		});
	}
});

function buildSelectOption(item) {
    var optionElement = $("<option></option>").attr("data-parent-uid", item.parent).text(item.label);
    if (typeof(item.link) !== 'undefined') {
        optionElement.attr("data-target-url", item.link).attr("value", 'item-' + item.uid);
    } else {
		optionElement.attr("value", item.uid);
	}
    return optionElement;
}

function jumpToTargetUrl(targetUrl) {
    window.location.href = targetUrl;
}

function findByLevelAndParentUid(level, parentUid) {
	var $allLevelItems = $('option', '#allLevel' + level);
	var $requestedItems = [];
	$.each($allLevelItems, function(key, item) {
		var $item = $(item);
		if ($item.attr('data-parent-uid') == parentUid) {
			$requestedItems.push($item.clone());
		}
	});
	return $requestedItems;
}

function prepareQuickNavigation(data) {
	var iterator = 0;
	var level1FirstItemUid;
    $.each(data, function(key, item) {
		var firstLevelOptionElement = buildSelectOption(item);
		// level 1 category or item
		$('#level1').append(firstLevelOptionElement);
		if (iterator === 0) {
			level1FirstItemUid = item.uid;
		}
		if (typeof(item.sub) !== 'undefined') {
			$.each(item.sub, function(key, item) {
				// level 2 category
				$('#allLevel2').append(buildSelectOption(item));
				if (typeof(item.items) !== 'undefined') {
					$.each(item.items, function(key, item) {
						// level 3 item
						$('#allLevel3').append(buildSelectOption(item));
					});
				}
			});
		} else if (typeof(item.items) !== 'undefined') {
			$.each(item.items, function(key, item) {
				// level 2 item
				$('#allLevel2').append(buildSelectOption(item));
			});
		}
		iterator++;
    });

	var level2Items = findByLevelAndParentUid(2, level1FirstItemUid);
	$('#level2').append(level2Items);
	var firstLevel2ItemUid = level2Items[Object.keys(level2Items)[0]].val();
	var level3Items = findByLevelAndParentUid(3, firstLevel2ItemUid);
	$('#level3').append(level3Items);

}