function initSectioncontentJsFilter() {
    $('.filterElement').wrapAll('<div class="filterElementWrap" />');
    
    $('.resetJsFilter').on('click', function(e) {
        e.preventDefault();
        
        $(this).addClass('act');
        $(this).siblings('.button').removeClass('act');
        
        $('.filterElementWrap').isotope({ filter: '*' });
    });
    
    $('.jsFilter').show();
    
    $('.jsFilter .button:not(.resetJsFilter)').on('click', function() {
        var thisFilterCat = $(this).attr('data-cat');
        $(this).siblings().removeClass('act');
        $(this).addClass('act');
        
        $('.filterElementWrap').isotope(
            {
                filter: '.filterElement[data-categories*=" ' + thisFilterCat + ' "]'
            }
        );
    });
    
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