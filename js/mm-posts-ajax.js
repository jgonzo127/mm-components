var $ = jQuery;

var mm_posts_ajax_data = function( newTerm, newPageVal ) {

	$mmPosts = $( '.mm-posts' );
	var counter = 0;

	$mmPosts.each( function( index ) {
		var postsData = {};
		var $this = $( this );
		counter += 1;
		postsDataRaw = $this.find( '#mm-posts-script' ).html();
		postsData = JSON.parse( postsDataRaw );
		postsData["mmPostsData_" + counter] = postData;

		data = {
			action            : 'mm_posts_ajax_filter',
			globalPostId      : postsData['mmPostsData_'+counter].global_post_id,
			taxonomy          : postsData['mmPostsData_'+counter].taxonomy,
			queryType         : postsData['mmPostsData_'+counter].query_type,
			postIds           : postsData['mmPostsData_'+counter].postIds,
			postType          : postsData['mmPostsData_'+counter].post_type,
			headingLevel      : postsData['mmPostsData_'+counter].heading_level,
			perPage           : postsData['mmPostsData_'+counter].per_page,
			paged             : postsData['mmPostsData_'+counter].paged,
			pagination        : postsData['mmPostsData_'+counter].pagination,
			template          : postsData['mmPostsData_'+counter].template,
			showFeaturedImage : postsData['mmPostsData_'+counter].show_featured_image,
			featuredImageSize : postsData['mmPostsData_'+counter].featured_image_size,
			showPostInfo      : postsData['mmPostsData_'+counter].show_post_info,
			showPostMeta      : postsData['mmPostsData_'+counter].show_post_meta,
			usePostContent    : postsData['mmPostsData_'+counter].use_post_content,
			linkTitle         : postsData['mmPostsData_'+counter].link_title,
			masonry           : postsData['mmPostsData_'+counter].masonry,
			totalPosts        : postsData['mmPostsData_'+counter].total_posts,
			totalPages        : postsData['mmPostsData_'+counter].total_pages,
			filterStyle       : postsData['mmPostsData_'+counter].filter_style
		};
	});

	data.term = newTerm;
	data.currentPage = newPageVal;
}

var mm_posts_ajax_filter = function( e, newPageVal, newTerm ) {
	var $this = $( this );
	var counter = 0;
	var $mmPosts = $this.parents( '.mm-posts-filter-wrapper' ).next( '.mm-posts' );
	var $mmPostsLoop = $mmPosts.find( '.mm-posts-loop' );
	var $filterLinks = $( '.mm-posts-filter a' );
	var $pagination = $( '.pagination' );
	var filterStyle;
	var totalPages;
	var newTerm;
	var $termText;
	var $responseObj;
	var newTotalPages;
	var postsData = {};

	e.preventDefault();

	$mmPosts.each( function( index ) {
		counter += 1;
		postsDataRaw = $( this ).find( '#mm-posts-script' ).html();
		postData = JSON.parse( postsDataRaw );
		postsData["mmPostsData_" + counter] = postData;
		filterStyle = postsData['mmPostsData_'+counter].filter_style;
		totalPages = postsData['mmPostsData_'+counter].total_pages;
	});

	$filterLinks.removeAttr('href');

	$filterLinks.unbind( 'click' );

	$( '.mm-posts-filter li.active' ).removeClass( 'active' );

	//Set term-data value to empty when all terms are clicked.
	if( filterStyle == 'links' ) {
		$termText = $this.text();

		if( $this.hasClass( 'mm-posts-filter-all') ) {
			$termText = '';
		} else {
			$termText = $this.text();
		}
	}

	if( filterStyle == 'dropdown' ) {
		$termText = $this.val();

		if( $this.val() == -1 ) {
			$termText = '';
		} else {
			$termText = $this.val();
		}
	}

	$mmPosts.find( '.mm-loading' ).show();

	$mmPosts.parent( 'li' ).addClass( 'active' );

	$( '.no-results' ).remove();

	// Grab the value of the new term-data.
	newTerm = $termText;

	mm_posts_ajax_data( newTerm, newPageVal );

	$mmPostsLoop.empty();

	// Make the AJAX request.
	$.post( ajaxurl, data, function( response ) {

		$responseObj = $( response );

		// Format and update the posts loop.
		$mmPostsLoop.replaceWith( response );

		newTotalPages = $responseObj.filter( '.ajax-total-pages' ).text();

		//Reload pagination links when number of pages changes.
		if ( $this.hasClass( 'mm-ajax-pagination' ) ) {
			if ( newTotalPages > 1 ) {
				$pagination.empty().removeData("twbs-pagination").unbind("page").twbsPagination({
		        	totalPages: newTotalPages,
		        	last : false,
		        	first : false
		    	});
			} else {
				$pagination.empty().removeData("twbs-pagination").unbind("page");
			}
		}

		if( $mmPosts.find( '.mm-posts-loop' ).find( 'article' ).length == 0 ) {
			$mmPosts.find( '.mm-posts-loop' ).before( '<span class="no-results">No Results Found.</span>' );
		}

		$mmPosts.find( '.mm-loading' ).hide();

		//Remove loading text and total posts markup.
		$this.find( '.ajax-total-pages' ).remove();
		$filterLinks.bind( 'click', mm_posts_ajax_filter );
		$filterLinks.attr( 'href', "#" );

		$( '.pagination li:not(.disabled)' ).on( 'click', mm_posts_ajax_pagination );
	});

}

var mm_posts_ajax_pagination = function( newTerm ) {
	$this = $( this );
	var $mmPosts = $this.prev( '.mm-posts' );
	var $mmPostsLoop = $mmPosts.find( '.mm-posts-loop' );
	var $paginationWrapper = $this.find( '.mm-posts-ajax-pagination-wrapper' );
	var $paginationLinks = $( '.pagination a' );
	var $page;
	var $responseObj;
	var newPageVal;
	var newTerm;
	var postsPerPage;
	var pageNumber;
	var pageNumberRounded;

	//Set page-data value to the text of current clicked page number.

	newPageVal = $paginationWrapper.find( '.pagination li.active a' ).text();

	newTerm = mmPostsData.term;

	mm_posts_ajax_data( newTerm, newPageVal );

	$mmPostsLoop.css({ "visibility" : "hidden" });

	$( '.mm-loading' ).show();

	// Make the AJAX request.
	$.post( ajaxurl, data, function( response ) {

		$responseObj = $( response );

		// Format and update the posts loop.
		$mmPostsLoop.replaceWith( response );

		newTotalPages = $responseObj.filter( '.ajax-total-pages' ).text();

		$mmPosts.find( '.ajax-total-pages' ).remove();

		$mmPosts.find( '.mm-loading' ).hide();

		$( '.pagination li:not(.disabled)' ).on( 'click', mm_posts_ajax_pagination );

	});

}

jQuery( document ).ready( function( $ ) {

	var totalPages;
	var $mmPosts = $( '.mm-posts' );
	var counter = 0;
	var postsData = {};

	$mmPosts.each( function() {
		counter += 1;
		var $this = $( this );
		postsDataRaw = $this.find( '#mm-posts-script' ).html();
		postData = JSON.parse( postsDataRaw );
		postsData["mmPostsData_" + counter] = postData;

		loading = '<i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw mm-loading"></i>';
		$this.prepend( loading );
		$this.find( '.mm-loading' ).hide();

		$this.prev( '.mm-posts-filter-wrapper' ).find( '.mm-posts-filter .cat-item a').on( 'click', mm_posts_ajax_filter );
		$this.prev( '.mm-posts-filter-wrapper' ).find( '.mm-posts-filter #term_dropdown' ).on( 'change', mm_posts_ajax_filter );

		//Only run AJAX pagination if activated.
		if ( $this.hasClass( 'mm-ajax-pagination' ) ) {

			$this.next( '.mm-posts-ajax-pagination-wrapper' ).twbsPagination({
			    totalPages: postsData['mmPostsData_'+counter].total_pages,
			    last : false,
			    first : false
			});

			$this.find( '.pagination li:not(.disabled)' ).on( 'click', mm_posts_ajax_pagination );
		}

	})

});