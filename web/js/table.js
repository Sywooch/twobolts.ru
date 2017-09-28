$(function() {
	
	var russianMessages = {
		serverCommunicationError: 'Ошибка связи с сервером. Попробуйте подключиться позже.',
		loadingMessage: 'Загрузка данных...',
		noDataAvailable: 'Информация не найдена!',
		addNewRecord: 'Добавить',
		editRecord: 'Редактировать',
		areYouSure: 'Вы уверены?',
		deleteConfirmation: 'Вы уверены что вы хотите удалить запись?',
		save: 'Сохранить',
		saving: 'Сохранение',
		cancel: 'Отменить',
		deleteText: 'Удалить',
		deleting: 'Удаление',
		error: 'Ошибка',
		close: 'Закрыть',
		cannotLoadOptionsFor: 'Не могу загрузить поле {0}!',
		pagingInfo: 'Показано {0} - {1}/{2}',
		canNotDeletedRecords: 'Не могу удалить {0} из {1} записей!',
		deleteProggress: 'Удаление {0}/{1} записи, обработка...',
		pageSizeChangeLabel: 'На странице',
		gotoPageLabel: 'Страница'
	};
	
	$('#commentsTable').jtable({
		messages: russianMessages, //Lozalize
		title: '',
		paging: true,
		sorting: true,
		defaultSorting: 'comment_created DESC',
		actions: {
			listAction: baseURL + 'ajax/get_admin_comments'
		},
		recordsLoaded : function(event, data)
		{
			$('.fnDeleteComment').on('click', function() {
				promptDeleteComment($(this))
			});
			
			$('.fnKarmaMinusUser').on('click', function() {
				decreaseKarma($(this));
			});
			
			$('.fnKarmaPlusUser').on('click', function() {
				increaseKarma($(this));
			});
		},
		fields: {
			comment_id: {
				key: true,
				create: false,
				edit: false,
				list: false
			},
			commentCheckbox: {
				title: '<input type="checkbox" id="selectAll" />',
				columnResizable: false,
				sorting: false,
				width: '20px',
				visibility: 'fixed',
				display: function(data) {
					return '<input type="checkbox" class="selectable" data="{\'comment_id\': ' + data.record.comment_id + 
						', \'user_id\': ' + data.record.comment_user_id + '}" />'
				}
			},
			comment_created: {
				title: 'Дата',
				width: '15%'
			},
			username: {
				title: 'Имя пользователя',
				width:'25%'
			},
			comment_text: {
				title: 'Текст комментария',
				width: '60%',
				sorting:false
			},
			comment_comparison_id: {
				title: 'Сравнение',
				width:'20px',
				columnResizable: false,
				visibility: 'fixed',
				listClass: 'jtable-command-column',
				display: function(data) {
					return '<a href="/comparison/view/' + data.record.comment_comparison_id +
						'" target="_blank"><i class="fa fa-share-square-o" title="Посмотреть сравнение"></i></a>';
				}
			},
			karma_plus: {
				title: '&nbsp;',
				columnResizable: false,
				sorting: false,
				width: '20px',
				visibility: 'fixed',
				listClass: 'jtable-command-column',
				display: function(data) {
					return '<span class="fnKarmaPlusUser" title="Увеличить карму" data="{\'comment_id\': ' +
						data.record.comment_id + 
						', \'user_id\': ' + data.record.comment_user_id + '}"><i class="fa fa-thumbs-up"></i></span>';
				}
			},
			karma_minus: {
				title: '&nbsp;',
				columnResizable: false,
				sorting: false,
				width: '20px',
				visibility: 'fixed',
				listClass: 'jtable-command-column',
				display: function(data) {
					return '<span class="fnKarmaMinusUser" title="Уменьшить карму" data="{\'comment_id\': ' +
						data.record.comment_id + 
						', \'user_id\': ' + data.record.comment_user_id + '}"><i class="fa fa-thumbs-down"></i></span>'
				}
			},
			delete: {
				title: '&nbsp;',
				columnResizable: false,
				sorting: false,
				width: '20px',
				visibility: 'fixed',
				listClass: 'jtable-command-column',
				display: function(data) {
					return '<span class="fnDeleteComment" title="Удалить комментарий" data="{\'comment_id\': ' +
						data.record.comment_id + 
						', \'user_id\': ' + data.record.comment_user_id + '}"><i class="fa fa-trash"></i></span>'
				}
			}
		}
	});
	
	$('#commentsTable').jtable('load');
	
	$('#newsTable').jtable({
		messages: russianMessages, //Lozalize
		title: '',
		paging: true,
		sorting: true,
		defaultSorting: 'created DESC',
		actions: {
			listAction: baseURL + 'ajax/get_admin_news'
		},
		recordsLoaded : function(event, data)
		{
			
			$('.fnDeleteNews').on('click', function() {
				promptDeleteNews($(this))
			});
			
		},
		fields: {
			page_id: {
				key: true,
				create: false,
				edit: false,
				list: false
			},
			newsCheckbox: {
				title: '<input type="checkbox" id="selectAll" />',
				columnResizable: false,
				sorting: false,
				width: '20px',
				visibility: 'fixed',
				display: function(data) {
					return '<input type="checkbox" class="selectable" data="{\'news_id\': ' + data.record.page_id + '}" />'
				}
			},
			title: {
				title: 'Заголовок новости',
				width: '60%'
			},
			created: {
				title: 'Дата',
				width: '15%'
			},
			username: {
				title: 'Автор',
				width:'15%'
			},
			num_comments: {
				title: '<i class="fa fa-comments"></i>',
				width:'20px',
				columnResizable: false,
				sorting: false,
				listClass: 'jtable-row-centered',
				visibility: 'fixed'
			},
			num_views: {
				title: '<i class="fa fa-eye"></i>',
				width:'20px',
				columnResizable: false,
				sorting: false,
				listClass: 'jtable-row-centered',
				visibility: 'fixed'
			},
			view_id: {
				title: '&nbsp;',
				width:'20px',
				columnResizable: false,
				sorting: false,
				visibility: 'fixed',
				listClass: 'jtable-command-column',
				display: function(data) {
					return '<a href="/news/view/' + data.record.page_id + 
						'" target="_blank"><i class="fa fa-share-square-o" title="Посмотреть новость"></i></a>';
				}
			},
			edit: {
				title: '&nbsp;',
				columnResizable: false,
				sorting: false,
				width: '20px',
				visibility: 'fixed',
				listClass: 'jtable-command-column',
				display: function(data) {
					return '<a href="/admin/news/edit/' + 
						data.record.page_id + 
						'"><i class="fa fa-pencil" title="Редактировать новость"></i></a>'
				}
			},
			delete: {
				title: '&nbsp;',
				columnResizable: false,
				sorting: false,
				width: '20px',
				visibility: 'fixed',
				listClass: 'jtable-command-column',
				display: function(data) {
					return '<span class="fnDeleteNews" title="Удалить новость" data="{\'news_id\': ' +
						data.record.page_id + '}"><i class="fa fa-trash"></i></span>'
				}
			}
		}
	});
	
	$('#newsTable').jtable('load');
	
	$('#newsCommentsTable').jtable({
		messages: russianMessages, //Lozalize
		title: '',
		paging: true,
		sorting: true,
		defaultSorting: 'comment_created DESC',
		actions: {
			listAction: baseURL + 'ajax/get_admin_news_comments'
		},
		recordsLoaded : function(event, data)
		{
			$('.fnDeleteNewsComment').on('click', function() {
				promptDeleteNewsComment($(this))
			});
		},
		fields: {
			comment_id: {
				key: true,
				create: false,
				edit: false,
				list: false
			},
			commentCheckbox: {
				title: '<input type="checkbox" id="selectAll" />',
				columnResizable: false,
				sorting: false,
				width: '20px',
				visibility: 'fixed',
				display: function(data) {
					return '<input type="checkbox" class="selectable" data="{\'comment_id\': ' + data.record.comment_id + 
						', \'user_id\': ' + data.record.comment_user_id + '}" />'
				}
			},
			comment_created: {
				title: 'Дата',
				width: '15%'
			},
			username: {
				title: 'Имя пользователя',
				width:'20%'
			},
			comment_text: {
				title: 'Текст комментария',
				width: '50%',
				sorting:false
			},
			comment_news_id: {
				title: 'Новость',
				width:'10%',
				columnResizable: false,
				visibility: 'fixed',
				listClass: 'jtable-command-column',
				display: function(data) {
					return '<a href="/news/' + data.record.comment_news_id + 
						'" target="_blank"><i class="fa fa-share-square-o" title="Посмотреть новость"></i></a>';
				}
			},
			delete: {
				title: '&nbsp;',
				columnResizable: false,
				sorting: false,
				width: '20px',
				visibility: 'fixed',
				listClass: 'jtable-command-column',
				display: function(data) {
					return '<span class="fnDeleteNewsComment" title="Удалить комментарий" data="{\'comment_id\': ' +
						data.record.comment_id + 
						', \'user_id\': ' + data.record.comment_user_id + '}"><i class="fa fa-trash"></i></span>'
				}
			}
		}
	});
	
	$('#newsCommentsTable').jtable('load');
	
	$('#comparisonsTable').jtable({
		messages: russianMessages, //Lozalize
		title: '',
		paging: true,
		sorting: true,
		defaultSorting: 'date DESC',
		actions: {
			listAction: baseURL + 'ajax/get_admin_comparisons'
		},
		recordsLoaded : function(event, data)
		{
			$('.fnDeleteComparison').on('click', function() {
				promptDeleteComparison($(this))
			});
			
			$('.fnModerate').on('click', function() {
				display_please_wait();
				$.ajax({
					url: baseURL + 'ajax/moderate_comparison', 
					async: true,
					type: 'POST',
					dataType: 'html',
					data: {
						comparison_id: $(this).metadata().comparison_id,
						active: $(this).metadata().active
					},
					success: function(response)
					{
						$('#comparisonsTable').jtable('reload');
					},
					complete: function() {
						close_please_wait()
					}
				});
			});
			
			$('.fnShowOnHome').on('click', function() {
				display_please_wait();
				$.ajax({
					url: baseURL + 'ajax/show_on_home_comparison', 
					async: true,
					type: 'POST',
					dataType: 'html',
					data: {
						comparison_id: $(this).metadata().comparison_id,
						show_on_home: $(this).metadata().show_on_home
					},
					success: function(response)
					{
						$('#comparisonsTable').jtable('reload');
					},
					complete: function() {
						close_please_wait()
					}
				});
			});
		},
		fields: {
			comparison_id: {
				key: true,
				create: false,
				edit: false,
				list: false
			},
			comparisonCheckbox: {
				title: '<input type="checkbox" id="selectAll" />',
				columnResizable: false,
				sorting: false,
				width: '20px',
				visibility: 'fixed',
				display: function(data) {
					return '<input type="checkbox" class="selectable" data="{\'comparison_id\': ' + data.record.comparison_id + '}" />'
				}
			},
			comparison: {
				title: 'Сравнение',
				sorting: false,
				display: function(data) {
					return data.record.main_manufacturer + ' ' + data.record.main_model + ' ' + data.record.main_engine + 
						' vs ' + 
						data.record.compare_manufacturer + ' ' + data.record.compare_model + ' ' + data.record.compare_engine;
				},
				width: '50%'
			},
			rating: {
				title: 'Рейтинг',
				width: '10%',
				display: function(data) {
					var n = data.record.rating;
					if (n)
					{
						return Number(n).toFixed(2);
					}
					else
					{
						return 0;
					}
				}
			},
			date: {
				title: 'Дата',
				width: '15%'
			},
			user_name: {
				title: 'Автор',
				width:'15%'
			},
			/*num_comments: {
				title: '<span class="entypo">&#59168;</span>',
				width:'20px',
				columnResizable: false,
				sorting: false,
				listClass: 'jtable-row-centered',
				visibility: 'fixed'
			},
			num_views: {
				title: '<span class="entypo">&#59146;</span>',
				width:'20px',
				columnResizable: false,
				sorting: false,
				listClass: 'jtable-row-centered',
				visibility: 'fixed'
			},*/
			active: {
				title: '&nbsp;',
				columnResizable: false,
				width: '20px',
				visibility: 'fixed',
				listClass: 'jtable-command-column',
				display: function(data) {
					var state = 'non-moderated',
						icon = '<i class="fa fa-ban"></i>';
					if (data.record.active == 1)
					{
						state = 'moderated';
						icon = '<i class="fa fa-check"></i>';
					}
					return '<span class="fnModerate ' + state + '" title="Модерация" data="{\'comparison_id\': ' +
						data.record.comparison_id + ', \'active\': ' + data.record.active + '}">' + icon + '</span>'
				}
			},
			show_on_home: {
				title: '&nbsp;',
				columnResizable: false,
				width: '20px',
				visibility: 'fixed',
				listClass: 'jtable-command-column',
				display: function(data) {
					var state = '';
					if (data.record.show_on_home == 1)
					{
						state = 'active'
					}
					return '<span class="fnShowOnHome ' + state + '" title="Показывать на главной" data="{\'comparison_id\': ' +
						data.record.comparison_id + ', \'show_on_home\': ' + data.record.show_on_home + '}"><i class="fa fa-home"></i></span>'
				}
			},
			view_id: {
				title: '&nbsp;',
				width:'20px',
				columnResizable: false,
				sorting: false,
				visibility: 'fixed',
				listClass: 'jtable-command-column',
				display: function(data) {
					return '<a href="/comparison/view/' + data.record.comparison_id +
						'" target="_blank"><i class="fa fa-share-square-o" title="Посмотреть сравнение"></i></a>';
				}
			},
			/*edit: {
				title: '&nbsp;',
				columnResizable: false,
				sorting: false,
				width: '20px',
				visibility: 'fixed',
				listClass: 'jtable-command-column',
				display: function(data) {
					return '<a href="/admin/comparisons/edit/' + 
						data.record.comparison_id + 
						'"><span class="entypo" title="Редактировать сравнение">&#9998;</span></a>'
				}
			},*/
			delete: {
				title: '&nbsp;',
				columnResizable: false,
				sorting: false,
				width: '20px',
				visibility: 'fixed',
				listClass: 'jtable-command-column',
				display: function(data) {
					return '<span class="fnDeleteComparison" title="Удалить сравнение" data="{\'comparison_id\': ' +
						data.record.comparison_id + '}"><i class="fa fa-trash"></i></span>'
				}
			}
		}
	});
	
	$('#comparisonsTable').jtable('load');
	
	$('#usersTable').jtable({
		messages: russianMessages, //Lozalize
		title: '',
		paging: true,
		sorting: true,
		defaultSorting: 'created DESC',
		actions: {
			listAction: baseURL + 'ajax/get_admin_users'
		},
		recordsLoaded : function(event, data)
		{
			
			$('.fnDeleteUser').on('click', function() {
				promptDeleteUser($(this))
			});
			
		},
		fields: {
			user_id: {
				key: true,
				create: false,
				edit: false,
				list: false
			},
			userCheckbox: {
				title: '<input type="checkbox" id="selectAll" />',
				columnResizable: false,
				sorting: false,
				width: '20px',
				visibility: 'fixed',
				display: function(data) {
					return '<input type="checkbox" class="selectable" data="{\'user_id\': ' + data.record.user_id + '}" />'
				}
			},
			username: {
				title: 'Имя пользователя',
				width:'35%'
			},
			email: {
				title: 'E-mail',
				width:'26%'
			},
			created: {
				title: 'Регистрация',
				width: '15%'
			},
			num_comparisons: {
				title: '<i class="fa fa-line-chart" title="Количество сравнений"></i>',
				width:'50px',
				columnResizable: false,
				listClass: 'jtable-row-centered',
				visibility: 'fixed'
			},
			karma: {
				title: '<i class="fa fa-thumbs-up" title="Карма"></i>',
				width:'50px',
				columnResizable: false,
				listClass: 'jtable-row-centered',
				visibility: 'fixed'
			},
			edit: {
				title: '&nbsp;',
				columnResizable: false,
				sorting: false,
				width: '20px',
				visibility: 'fixed',
				listClass: 'jtable-command-column',
				display: function(data) {
					return '<a href="/admin/users/edit/' + 
						data.record.user_id + 
						'"><span title="Редактировать пользователя"><i class="fa fa-pencil"></i></span></a>'
				}
			},
			delete: {
				title: '&nbsp;',
				columnResizable: false,
				sorting: false,
				width: '20px',
				visibility: 'fixed',
				listClass: 'jtable-command-column',
				display: function(data) {
					return '<span class="fnDeleteUser" title="Удалить пользователя" data="{\'user_id\': ' +
						data.record.user_id + '}"><i class="fa fa-trash"></i></span>'
				}
			}
		}
	});
	
	$('#usersTable').jtable('load');
	
	$(".admin-list").on('click', '#selectAll', function() {
		$(".selectable").attr('checked', $(this).prop('checked'));
	});
	
	$('.fnDeleteComment').on('click', function() {
		promptDeleteComment($(this))
	});
	
	$('#btn_delete_selected').on('click', function() {
		var comments_array = []
		$('.selectable:checked').each(function(index, element) {
            comments_array.push($(this).metadata().comment_id);
        });
		
		if (comments_array.length)
		{
			promptDeleteComments(comments_array);
		}
		else
		{
			display_text_message('Не выбраны комментарии для удаления.', 400, 200, 'Ошибка');
		}
	});
	
	$('.fnKarmaMinusUser').on('click', function() {
		decreaseKarma($(this));
	});
	
	$('.fnKarmaPlusUser').on('click', function() {
		increaseKarma($(this));
	});
	
	$('#btn_karma_plus_selected').on('click', function() {
		changeSelectedKarma('increase');
	});
	
	$('#btn_karma_minus_selected').on('click', function() {
		changeSelectedKarma('decrease');
	});
	
	$('.fnDeleteNews').on('click', function() {
		promptDeleteNews($(this))
	});
	
	$('#btn_delete_selected_news').on('click', function() {
		var news_array = []
		$('.selectable:checked').each(function(index, element) {
            news_array.push($(this).metadata().news_id);
        });
		
		if (news_array.length)
		{
			promptDeleteSelectedNews(news_array);
		}
		else
		{
			display_text_message('Не выбраны новости для удаления.', 400, 200, 'Ошибка');
		}
	});
	
	$('.fnDeleteNewsComment').on('click', function() {
		promptDeleteNewsComment($(this))
	});
	
	$('#btn_news_delete_selected').on('click', function() {
		var comments_array = []
		$('.selectable:checked').each(function(index, element) {
            comments_array.push($(this).metadata().comment_id);
        });
		
		if (comments_array.length)
		{
			promptDeleteNewsComments(comments_array);
		}
		else
		{
			display_text_message('Не выбраны комментарии для удаления.', 400, 200, 'Ошибка');
		}
	});
	
	$('.fnDeleteComparison').on('click', function() {
		promptDeleteComparison($(this))
	});
	
	$('#btn_delete_selected_comparisons').on('click', function() {
		var comparisons_array = []
		$('.selectable:checked').each(function(index, element) {
            comparisons_array.push($(this).metadata().comparison_id);
        });
		
		if (comparisons_array.length)
		{
			promptDeleteComparisons(comparisons_array);
		}
		else
		{
			display_text_message('Не выбраны сравнения для удаления.', 400, 200, 'Ошибка');
		}
	});
	
	$('#btn_rate_comparisons').on('click', function() {
		display_please_wait();
		$.ajax({
			url: baseURL + 'ajax/rate_comparisons', 
			async: true,
			type: 'POST',
			dataType: 'html',
			success: function(response)
			{
				window.location = window.location.href;
			},
			complete: function() {
				close_please_wait()
			}
		});
	});
	
	$('.fnDeleteUser').on('click', function() {
		promptDeleteUser($(this))
	});
	
	$('#btn_delete_selected_users').on('click', function() {
		var comparisons_array = []
		$('.selectable:checked').each(function(index, element) {
            comparisons_array.push($(this).metadata().user_id);
        });
		
		if (comparisons_array.length)
		{
			promptDeleteUsers(comparisons_array);
		}
		else
		{
			display_text_message('Не выбраны пользователи для удаления.', 400, 200, 'Ошибка');
		}
	});
	
});

function promptDeleteComments(comments_array)
{
	display_prompt_message(
		'Вы действительно хотите удалить комментарии?', 
		'Удаление комментариев',
		'deleteComments', 
		comments_array
	);
}

function promptDeleteComment(elem)
{
	display_prompt_message(
		'Вы действительно хотите удалить комментарий?', 
		'Удаление комментария',
		'prepareDeleteComment', 
		elem
	);
}

function prepareDeleteComment(elem)
{
	var comment_id = elem.metadata().comment_id;
	deleteComments([comment_id]);
}

function deleteComments(comments_array)
{
	showBusyPanel('Удаление...');
	
	$.ajax({
		url: baseURL + 'ajax/delete_comparisons_comments', 
		async: true,
		type: 'POST',
		dataType: 'html',
		data: {
			comments_array: comments_array
		},
		success: function(response)
		{
			$('#commentsTable').jtable('reload');
		},
		error: function() {
			hideBusyPanel()
		}
	});
}

function showBusyPanel(message)
{
	$('.jtable-busy-message')
		.html(message)
		.show()
		.toggleClass('jtable-busy-message-deleting');
	$('.jtable-busy-panel-background')
		.toggleClass('jtable-busy-panel-background-invisible')
		.show()
		.width($('.jtable-main-container').width())
		.height($('.jtable-main-container').height());
}

function hideBusyPanel()
{
	$('#selectAll').attr('checked', false);
	
	$('.jtable-busy-message')
		.html('')
		.hide()
		.toggleClass('jtable-busy-message-deleting');
	$('.jtable-busy-panel-background')
		.toggleClass('jtable-busy-panel-background-invisible')
		.hide();
}

function increaseKarma(elem)
{
	var user_id = elem.metadata().user_id;
	
	showBusyPanel('Обработка...');
	
	$.ajax({
		url: baseURL + 'ajax/increase_user_karma', 
		async: true,
		type: 'POST',
		dataType: 'html',
		data: {
			user_id: user_id
		},
		complete: function() {
			hideBusyPanel()
		}
	});
}

function decreaseKarma(elem)
{
	var user_id = elem.metadata().user_id;
	
	showBusyPanel('Обработка...');
	
	$.ajax({
		url: baseURL + 'ajax/decrease_user_karma', 
		async: true,
		type: 'POST',
		dataType: 'html',
		data: {
			user_id: user_id
		},
		complete: function() {
			hideBusyPanel()
		}
	});
}

function changeSelectedKarma(type)
{
	var users_array = []
	$('.selectable:checked').each(function(index, element) {
		users_array.push($(this).metadata().user_id);
	});
	
	if (users_array.length)
	{
		showBusyPanel('Обработка...');
	
		$.ajax({
			url: baseURL + 'ajax/' + type + '_users_karma', 
			async: true,
			type: 'POST',
			dataType: 'html',
			data: {
				users_array: users_array
			},
			complete: function() {
				hideBusyPanel();
				$('.selectable, #selectAll').attr('checked', false);
			}
		});
	}
	else
	{
		display_text_message('Не выбраны записи.', 400, 200, 'Ошибка');
	}
}

function promptDeleteSelectedNews(news_array)
{
	display_prompt_message(
		'Вы действительно хотите удалить новости?', 
		'Удаление новостей',
		'deleteNews', 
		news_array
	);
}

function promptDeleteNews(elem)
{
	display_prompt_message(
		'Вы действительно хотите удалить новость?', 
		'Удаление новости',
		'prepareDeleteNews', 
		elem
	);
}

function prepareDeleteNews(elem)
{
	var news_id = elem.metadata().news_id;
	deleteNews([news_id]);
}

function deleteNews(news_array)
{
	showBusyPanel('Удаление...');
	
	$.ajax({
		url: baseURL + 'ajax/delete_news', 
		async: true,
		type: 'POST',
		dataType: 'html',
		data: {
			news_array: news_array
		},
		success: function(response)
		{
			$('#newsTable').jtable('reload');
		},
		error: function() {
			hideBusyPanel()
		}
	});
}

function promptDeleteNewsComments(comments_array)
{
	display_prompt_message(
		'Вы действительно хотите удалить комментарии?', 
		'Удаление комментариев',
		'deleteNewsComments', 
		comments_array
	);
}

function promptDeleteNewsComment(elem)
{
	display_prompt_message(
		'Вы действительно хотите удалить комментарий?', 
		'Удаление комментария',
		'prepareDeleteNewsComment', 
		elem
	);
}

function prepareDeleteNewsComment(elem)
{
	var comment_id = elem.metadata().comment_id;
	deleteNewsComments([comment_id]);
}

function deleteNewsComments(comments_array)
{
	showBusyPanel('Удаление...');
	
	$.ajax({
		url: baseURL + 'ajax/delete_news_comments', 
		async: true,
		type: 'POST',
		dataType: 'html',
		data: {
			comments_array: comments_array
		},
		success: function(response)
		{
			$('#newsCommentsTable').jtable('reload');
		},
		error: function() {
			hideBusyPanel()
		}
	});
}

function promptDeleteComparison(elem)
{
	display_prompt_message(
		'Вы действительно хотите удалить сравнение?', 
		'Удаление сравнения',
		'prepareDeleteComparison', 
		elem
	);
}

function prepareDeleteComparison(elem)
{
	var comparison_id = elem.metadata().comparison_id;
	deleteComparisons([comparison_id]);
}

function deleteComparisons(comparisons_array)
{
	showBusyPanel('Удаление...');
	
	$.ajax({
		url: baseURL + 'ajax/delete_comparisons', 
		async: true,
		type: 'POST',
		dataType: 'html',
		data: {
			comparisons_array: comparisons_array
		},
		success: function(response)
		{
			$('#comparisonsTable').jtable('reload');
		},
		error: function() {
			hideBusyPanel()
		}
	});
}

function promptDeleteComparisons(comparisons_array)
{
	display_prompt_message(
		'Вы действительно хотите удалить сравнения?', 
		'Удаление сравнений',
		'deleteComparisons', 
		comparisons_array
	);
}

function promptDeleteUser(elem)
{
	display_prompt_message(
		'Вы действительно хотите удалить пользователя?', 
		'Удаление пользователя',
		'prepareDeleteUser', 
		elem
	);
}

function prepareDeleteUser(elem)
{
	var user_id = elem.metadata().user_id;
	deleteUsers([user_id]);
}

function deleteUsers(users_array)
{
	showBusyPanel('Удаление...');
	
	$.ajax({
		url: baseURL + 'ajax/delete_users', 
		async: true,
		type: 'POST',
		dataType: 'html',
		data: {
			users_array: users_array
		},
		success: function(response)
		{
			$('#usersTable').jtable('reload');
		},
		error: function() {
			hideBusyPanel()
		}
	});
}

function promptDeleteUsers(users_array)
{
	display_prompt_message(
		'Вы действительно хотите удалить пользователей?', 
		'Удаление пользователей',
		'deleteUsers', 
		users_array
	);
}