function sectioncontentHideNonPossibleCategories() {
    // GET ALL CATEGORIES WHICH ARE POSSIBLE
    $('.filterElement').each(function() {
        
        var possibleCategories = $(this).attr('data-categories').split(' ');
        $.each(possibleCategories, function(key,value) {
            if(value != '') {
                $('.jsFilter select option[value="' + value + '"]').show();
            }
        });
    });
}


function initSectioncontentJsFilter() {
    $('.filterElement').wrapAll('<div class="filterElementWrap" />');
    
    $('.resetJsFilter').on('click', function(e) {
        e.preventDefault();
        
        $('.jsFilter select').each(function() {
            $(this).val('');
            $(this).trigger('change');
        });
    });


    $('.jsFilter').show();
    
    $('.jsFilter select option').hide();
    $('.jsFilter select option[value=""]').show();
    
    sectioncontentHideNonPossibleCategories();
    
    
    /**
     * 
     * IF YOU SELECT A CATEGORY
     * 
     **/
    $('.jsFilter select').on('change',function(e) {
    
        var curSelect = $(this);
        
        if(goJsFilter.options.resetOtherFilters || curSelect.find('option[value="' + curSelect.val() + '"]').hasClass('notPossible')) {
            $('.jsFilter select').each(function() {
                if (curSelect.attr('name') != $(this).attr('name')) {
                    $(this).val('');
                }
            });
        }
        
        $('.jsFilter select option').addClass('notPossible');
        $('.jsFilter select option[value=""]').removeClass('notPossible');
        
        
        var filterCats = [];
        $('.jsFilter select').each(function() {
            if($(this).val() != '') {
                filterCats.push($(this).val());
            }
        });
        
        
        var filterSelector = '';
        
        if (filterCats.length > 0) {
            filterSelector = '.filterElement[data-categories*=" ' + filterCats.join(' "][data-categories*=" ') + ' "]';
        } else {
            filterSelector = '.filterElement';
        }
        
        
        
        // GET ALL CATEGORIES WHICH ARE POSSIBLE
        $(filterSelector).each(function() {
            
            var possibleCategories = $(this).attr('data-categories').split(' ');
            $.each(possibleCategories, function(key,value) {
                if(value != '') {
                    $('.jsFilter select option[value="' + value + '"]').removeClass('notPossible');
                }
            });
        });
        
        // ALL OPTION IN CURRENT SELECT ARE NOT POSSIBLE
        curSelect.find('option').addClass('notPossible');
        curSelect.find('option[value=""]').removeClass('notPossible');
        curSelect.find('option[value="' + curSelect.val() + '"]').removeClass('notPossible');
        
        // filter .metal items
        $('.filterElementWrap').isotope(
            {
                filter: filterSelector
            }
        );
    });
    
    log(goJsFilter.options.layoutMode);
    
	$('.filterElementWrap').imagesLoaded(function() {
		$('.filterElementWrap').isotope({
			// options
			itemSelector: '.filterElement',
			layoutMode: goJsFilter.options.layoutMode
		});
	});
}

if(typeof($('body').isotope) == 'undefined') {
	$.when(
		$.getScript("/typo3conf/ext/sectioncontent/Resources/Public/js/isotope.min.js"),
		$.getScript("/typo3conf/ext/sectioncontent/Resources/Public/js/imagesloaded.pkgd.min.js"),
		$.Deferred(function(deferred) {
			$(deferred.resolve);
		})
	).done(function() {
		initSectioncontentJsFilter();
	});
} else {
	initSectioncontentJsFilter();
}
 
