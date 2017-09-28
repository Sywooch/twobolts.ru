var commentEditor,
	commentObject;

$(function() {
	$('#btn_comments').on('click', function(e) {
		e.preventDefault();
	
		$('.comments-list').slideToggle(function() {
			if ($('.comments-list').css('display') == 'none') {
				$('.comments-wrap h2 .fa').removeClass('fa-caret-up').addClass('fa-caret-down');
			} else {
				$('.comments-wrap h2 .fa').removeClass('fa-caret-down').addClass('fa-caret-up');
			}
		});
	});

    var commentText = $('#comment_text');
	if (commentText.length) {
		commentEditor = CKEDITOR.replace(
			'comment_text',
			{
				allowedContent: 'p br b i s blockquote; b(!fnReplyAuthor);'
			}
		);
		
		if (commentEditor) {
			commentEditor.on('instanceReady', function() {
				if (!isLoggedIn) commentEditor.setReadOnly();
			});
		} else {
			if (!isLoggedIn) commentText.addClass('ui-state-disabled');
		}
	}
	
	$('.ui-state-disabled').each(function(index, element) {
        $(this).attr('disabled', true);
    });
	
	$('#btn_add_comment').on('click', function(e) {
		e.preventDefault();
		var objectId = $('#object_id').val(),
            commentText = '';

		if (commentEditor) {
            commentText = commentEditor.getData();
		} else {
            commentText = $('#comment_text').val();
		}

		if (commentText.length <= 0) {
			showMessage(localizationMessages['Enter comment text'], localizationMessages['error']);
			return;
		}

		ajaxSpinner.button($(this), 'medium-white');

		$.ajax({
			url: baseURL + 'comment/add',
			async: true,
			type: 'POST',
			dataType: 'html',
			data: {
                objectId: objectId,
                objectClass: commentObject,
                commentText: commentText
			},
			success: function(response) {
			    if (isJson(response)) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                        showMessage(data.error, 400, 200, localizationMessages['error']);
                    } else {
                        commentEditor.setData('');
                        $('.user-comment-wrapper.hidden').removeClass('hidden');
                        $('.comments-list h4').remove();
                        $('.comments-btn-wrap').before(data.comment);

                        $('#btn_more_comment').fadeOut();

                        refreshCommentsHandlers();

                        $('html, body').animate({
                            scrollTop: $('.comment-form').offset().top
                        }, 1000);

                        var totalWrap = $('.total-comments'),
                            total = parseInt(totalWrap.text());
                        totalWrap.text(++total);
                    }
                }
			},
			complete: function() {
				ajaxSpinner.stop(true);
			}
		});
	});

    var setCommentReplyTo = function(elem) {
        var parent = elem.parent(),
            commentText = commentEditor.getData(),
            editorData;

        $('#comment_reply_id').val(parent.attr('data-id'));

        editorData = $('<div></div>').html(commentText);
        if (editorData.html().length > 0) {
            editorData.children('p:first-child').prepend('<b class="fnReplyAuthor">' + parent.attr('data-username') + ',</b>&nbsp;');
            commentText = editorData.html();
        } else {
            commentText = '<b class="fnReplyAuthor">' + parent.attr('data-username') + ',</b>&nbsp;';
        }

        commentEditor.setData(commentText);

        $('html, body').animate({
            scrollTop: $('.comment-form').offset().top
        }, 1000);
    };

	$('.fnCommentReply').on('click', function(e) {
        e.preventDefault();
		setCommentReplyTo($(this))
	});

    var setCommentQuote = function(elem) {
        var parent = elem.parent(),
            commentText = commentEditor.getData(),
            quoteText = $('#comment_text_' + parent.attr('data-id')).clone(),
            editorData;

        quoteText.children('p').children('b.fnReplyAuthor').remove();
        if (commentText.length > 0) {
            editorData = $('<div></div>').html(commentText);
            editorData.prepend('<blockquote><b>' + parent.attr('data-username') + ' написал:</b> '+ quoteText.html() + '</blockquote>');
            commentText = editorData.html();
        } else {
            commentText = '<blockquote><b>' + parent.attr('data-username') + ' написал:</b> '+ quoteText.html() + '</blockquote><p></p>';
        }
        commentEditor.setData(commentText);

        $('html, body').animate({
            scrollTop: $('.comment-form').offset().top
        }, 1000);
    };

	$('.fnCommentQuote').on('click', function() {
		setCommentQuote($(this))
	});

	$('#btn_clear_comment').on('click', function() {
		if (commentEditor) {
            commentEditor.setData('');
		} else {
			$('#comment_text').val('');
		}
	});
	
	$('#btn_more_comment').on('click', function() {
		loadMoreComments()
	});

    var manageCommentKarma = function (elem, type) {
        var commentId = elem.parent().attr('data-id'),
            objectId = $('#object_id').val();

        $.ajax({
            url: baseURL + 'comment/manage-karma',
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {
                commentId: commentId,
                objectId: objectId,
                objectClass: commentObject,
                type: type
            },
            success: function(response) {
                var data = $.parseJSON(response);

                if (data.error) {
                    showMessage(data.error, 'Ошибка');
                } else {
                    updateUserKarma(elem, type);
                    elem.siblings('.fnManageUserKarma').remove();
                    elem.remove();
                }
            }
        });
    };

    var updateUserKarma = function(elem, type) {
        var karma = parseInt(elem.siblings('.karma-total').text());
        if (type == 'increase') {
            ++karma;
        } else {
            --karma;
        }

        var karmaSign;
        var karmaClass;
        if (karma > 0) {
            karmaSign = '+';
            karmaClass = 'increased';
        } else {
            if (karma < 0) {
                karmaClass = 'decreased';
            } else {
                karmaClass = '';
            }
            karmaSign = '';
        }

        elem.siblings('.karma-total').removeClass('decreased').removeClass('increased');
        elem.siblings('.karma-total').addClass(karmaClass).text(karmaSign + karma);
    };

	$('.fnIncreaseUserKarma').on('click', function() {
		manageCommentKarma($(this), 'increase');
	});

	$('.fnDecreaseUserKarma').on('click', function() {
		manageCommentKarma($(this), 'decrease');
	});

    var refreshCommentsHandlers = function() {
        $('.fnCommentReply').on('click', function() {
            setCommentReplyTo($(this))
        });

        $('.fnCommentQuote').on('click', function() {
            setCommentQuote($(this))
        });

        $('.fnIncreaseUserKarma').on('click', function() {
            manageCommentKarma($(this), 'increase');
        });

        $('.fnDecreaseUserKarma').on('click', function() {
            manageCommentKarma($(this), 'decrease');
        });

        $('.user-comment-wrapper').on('mouseenter', function() {
            $(this).removeClass('new-comment');
        });
    };

    var loadMoreComments = function() {
        var loadMore = $('#btn_more_comment'),
            hiddenComments = $('.user-comment-wrapper.hidden'),
            hiddenRemain = 0;

        ajaxSpinner.button(loadMore);

        hiddenComments.each(function (index, element) {
            if (index < commentsPerPage) {
                $(this).removeClass('hidden');
            }
        });

        hiddenRemain = hiddenComments.length - commentsPerPage;

        ajaxSpinner.stop(true);

        if (hiddenRemain > 0) {
            loadMore.fadeIn();
            loadMore.find('.badge').text(hiddenRemain);
        } else {
            loadMore.fadeOut();
        }

        refreshCommentsHandlers();
    };
});