var $ = jQuery;

var mm_posts_ajax_data = function( newTerm, newPageVal ) {

	$mmPosts = $( '.mm-posts' );

	$mmPosts.each( function( index ) {
		var postsDataHolder = {};
		var counter = 0;
		counter += 1;
		var $postsScript = $( this ).find( '#mm-posts-script' ).html();
		postsDataHolder["mmPostsData-"+counter] = $postsScript;
		console.log( postsDataHolder["mmPostsData-1"] );
		data = {
			action            : 'mm_posts_ajax_filter',
			currentPage       : newPageVal,
			globalPostId      : postsDataHolder['mmPostsData-'+counter].global_post_id,
			taxonomy          : postsDataHolder['mmPostsData-'+counter].taxonomy,
			queryType         : postsDataHolder['mmPostsData-'+counter].query_type,
			postIds           : postsDataHolder['mmPostsData-'+counter].postIds,
			postType          : postsDataHolder['mmPostsData-'+counter].post_type,
			term              : newTerm,
			headingLevel      : postsDataHolder['mmPostsData-'+counter].heading_level,
			perPage           : postsDataHolder['mmPostsData-'+counter].per_page,
			paged             : postsDataHolder['mmPostsData-'+counter].paged,
			pagination        : postsDataHolder['mmPostsData-'+counter].pagination,
			template          : postsDataHolder['mmPostsData-'+counter].template,
			showFeaturedImage : postsDataHolder['mmPostsData-'+counter].show_featured_image,
			featuredImageSize : postsDataHolder['mmPostsData-'+counter].featured_image_size,
			showPostInfo      : postsDataHolder['mmPostsData-'+counter].show_post_info,
			showPostMeta      : postsDataHolder['mmPostsData-'+counter].show_post_meta,
			usePostContent    : postsDataHolder['mmPostsData-'+counter].use_post_content,
			linkTitle         : postsDataHolder['mmPostsData-'+counter].link_title,
			masonry           : postsDataHolder['mmPostsData-'+counter].masonry,
			totalPosts        : postsDataHolder['mmPostsData-'+counter].total_posts,
			totalPages        : postsDataHolder['mmPostsData-'+counter].total_pages,
			filterStyle       : postsDataHolder['mmPostsData-'+counter].filter_style
		};
	});
}

var mm_posts_ajax_filter = function( e, newPageVal ) {
	var $this = $( this );
	var $mmPosts = $( '.mm-posts' );
	var $mmPostsLoop = $this.find( '.mm-posts-loop' );
	var $filterLinks = $( '.mm-posts-filter a' );
	var $pagination = $( '.pagination' );
	var filterStyle = 'dropdown';
	var totalPages = 5;
	var newTerm;
	var $termText;
	var $responseObj;
	var newTotalPages;

	e.preventDefault();

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

	$this.find( '.mm-loading' ).show();

	$this.parent( 'li' ).addClass( 'active' );

	$( '.no-results' ).remove();

	// Grab the value of the new term-data.
	newTerm = $termText;

	mm_posts_ajax_data( newTerm, newPageVal );

	$mmPostsLoop.empty();

	// Make the AJAX request.
	$.post( ajaxurl, data, function( response ) {

		$responseObj = $( response );

		console.log( data );

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

		if( $this.find( '.mm-posts-loop' ).find( 'article' ).length == 0 ) {
			$this.find( '.mm-posts-loop' ).before( '<span class="no-results">No Results Found.</span>' );
		}

		$this.find( '.mm-loading' ).hide();

		//Remove loading text and total posts markup.
		$this.find( '.ajax-total-pages' ).remove();
		$filterLinks.bind( 'click', mm_posts_ajax_filter );
		$filterLinks.attr( 'href', "#" );

		$( '.pagination li:not(.disabled)' ).on( 'click', mm_posts_ajax_pagination );
	});

}

var mm_posts_ajax_pagination = function( newTerm ) {
	$this = $( this );
	var $mmPosts = $( '.mm-posts' );
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

		$this.find( '.ajax-total-pages' ).remove();

		$this.find( '.mm-loading' ).hide();

		$( '.pagination li:not(.disabled)' ).on( 'click', mm_posts_ajax_pagination );

	});

}

jQuery( document ).ready( function( $ ) {

	var totalPages;
	var $mmPosts = $( '.mm-posts' );
	var $counter = 0;
	var postsDataHolder = {};

	$mmPosts.each( function() {
		$counter += 1;
		var $this = $( this );
		var $postsScript = $this.find( '#mm-posts-script' ).html();
		postsDataHolder["mmPostsData-"+$counter] = $postsScript;

		loading = '<i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw mm-loading"></i>';
		$this.prepend( loading );
		$this.find( '.mm-loading' ).hide();

		$this.prev( '.mm-posts-filter-wrapper' ).find( '.mm-posts-filter .cat-item a').on( 'click', mm_posts_ajax_filter );
		$this.prev( '.mm-posts-filter-wrapper' ).find( '.mm-posts-filter #term_dropdown' ).on( 'change', mm_posts_ajax_filter );
	
		//Only run AJAX pagination if activated.
		if ( $this.hasClass( 'mm-ajax-pagination' ) ) {

			$this.next( '.mm-posts-ajax-pagination-wrapper' ).twbsPagination({
			    totalPages: 5,
			    last : false,
			    first :false
			});

			$this.find( '.pagination li:not(.disabled)' ).on( 'click', mm_posts_ajax_pagination );
		}

	})

});