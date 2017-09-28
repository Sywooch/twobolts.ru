var mainCompareUpload;

$(function() {
    $('.catalog-sort a').on('click', function(e) {
        e.preventDefault();

        if (!$(this).hasClass('active')) {
            $('.catalog-sort a').removeClass('active');
            $(this).addClass('active');
            _comparisonListSorting = $(this).attr('data-sorting');

			ajaxSpinner.add(
			    $('.catalog-sort'),
                'medium',
                'append',
                {
                    'position': 'absolute',
                    'top': -6,
                    'margin-left': 5
                }
            );

            _comparisonListPageNum = 1;

			if ($(this).parent().attr('data-get') == 'catalog') {
                $.ajax({
                    url: baseURL + 'catalog/get-manufacturer-cars',
                    async: true,
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        sorting: _comparisonListSorting,
                        manufacturerId: $(this).parent().attr('data-manufacturer')
                    },
                    success: function (response) {
                        var data = $.parseJSON(response);
                        if (data.error)
                            showMessage(data.error, localizationMessages['error']);
                        else {
                            $('.catalog-models-wrapper').html(data.list);
                            showVisible();
                        }
                    },
                    complete: function () {
                        ajaxSpinner.stop();
                    }
                });
			} else {
				loadComparisonItems(false);
			}
        }
    });

    $('#main_manufacturer').on('change', function() {
        manufacturerChange('main');
    });

    $('#main_model').on('change', function() {
        modelChange('main');
    });

    $('#compare_manufacturer').on('change', function() {
        manufacturerChange('compare');
    });

    $('#compare_model').on('change', function() {
        modelChange('compare');
    });

    var comparisonMainImage = $('#main_image');
    if (comparisonMainImage.length) {
        mainCompareUpload = comparisonMainImage.upload({
            name: 'Upload[imageFile]',
            action: '/uploader/upload',
            enctype: 'multipart/form-data',
            params: {
                field: 'Upload[imageFile]'
            },
            autoSubmit: true,
            onSubmit: function() {
                elemUpload = '#main_photo';
                cropFunc = 'cropImage';
                ajaxSpinner.button(
                    comparisonMainImage,
                    'medium-dark'
                );
            },
            onComplete: function(response) { showCropper('#main_image_container', response); },
            onSelect: function() {}
        });
    }

    var comparisonCompareImage = $('#compare_image');
    if (comparisonCompareImage.length) {
        compareCompareUpload = comparisonCompareImage.upload({
            name: 'Upload[imageFile]',
            action: '/uploader/upload',
            enctype: 'multipart/form-data',
            params: {
                field: 'Upload[imageFile]'
            },
            autoSubmit: true,
            onSubmit: function() {
                elemUpload = '#compare_photo';
                cropFunc = 'cropImage';
                ajaxSpinner.button(
                    comparisonCompareImage,
                    'medium-dark'
                );
            },
            onComplete: function(response) { showCropper('#compare_image_container', response); },
            onSelect: function() {}
        });
    }

    $('.no-auto-link').on('click', function(e) {
        e.preventDefault();
        $('.no-auto-catalog > div').slideToggle();
		$(window).trigger('resize');
    });

	$('#start_compare').on('click', function(e) {
		e.preventDefault();

        $('.tnx').slideUp();
		var comparisonValues = $('#comparison_values'),

            mainManufacturerId = $('#main_manufacturer'),
            mainModelId = $('#main_model'),
            mainEngineId = $('#main_engine'),
            mainTime = $('#main_time'),

            compareManufacturerId = $('#compare_manufacturer'),
            compareModelId = $('#compare_model'),
            compareEngineId = $('#compare_engine'),
            compareTime = $('#compare_time');

		if (mainManufacturerId.val() != 0 && mainModelId.val() != 0 && mainEngineId.val() != 0 && mainTime.val() != 0
            && compareManufacturerId.val() != 0 && compareModelId.val() != 0 && compareEngineId.val() != 0 && compareTime.val() != 0) {

			if (mainManufacturerId.val() == compareManufacturerId.val() && mainModelId.val() == compareModelId.val()) {
				showMessage(localizationMessages['Trying to compare the same car'], localizationMessages['error']);
				return;
			}

			ajaxSpinner.button($(this), 'medium-white');
			
			$.ajax({
				url: baseURL + 'comparison/exist-comparison',
				async: true,
				type: 'POST',
				dataType: 'html',
				data: {
					mainManufacturerId: mainManufacturerId.val(),
					mainModelId: mainModelId.val(),
					mainEngineId: mainEngineId.val(),
					compareManufacturerId: compareManufacturerId.val(),
					compareModelId: compareModelId.val(),
					compareEngineId: compareEngineId.val()
				},
				success: function(response) {
					var data = $.parseJSON(response);
					if (data.error) {
                        showMessage(data.error, localizationMessages['error']);
                    } else {
						comparisonValues.slideDown();
                        textareaLineage();
			
						$('.comparison-add-item-main-name').html('<p>' + mainManufacturerId.find(':selected').text() + '</p>' +
							'<p>' + mainModelId.find(':selected').text() + '</p>' +
							'<p>'  + mainEngineId.find(':selected').text() + '</p>');
						
						$('.comparison-add-item-compare-name').html('<p>' + compareManufacturerId.find(':selected').text() + '</p>' +
							'<p>' + compareModelId.find(':selected').text() + '</p>' +
							'<p>' + compareEngineId.find(':selected').text() + '</p>');
									
						var bodyTop = comparisonValues.prev().offset().top + comparisonValues.prev().height();
						$('html, body').animate({scrollTop: bodyTop}, 1000);
					}
				},
				complete: function() {
					ajaxSpinner.stop(true);
				}
			});
		} else {
			showMessage(localizationMessages['Please select car to compare'], localizationMessages['error']);
		}
	});
	
	$('.comparison-add-point-handler').on('click', function() {
		$(this).siblings().removeClass('active');
		$(this).addClass('active');
	});
	
	$('#btn_add_compare').on('click', function(e) {
		e.preventDefault();

		var mainManufacturerID = $('#main_manufacturer').val(),
			mainModelID = $('#main_model').val(),
			mainEngineID = $('#main_engine').val(),
			mainPhoto = $('#main_photo').find('img').attr('src'),
			mainTime = $('#main_time').val(),
			compareManufacturerID = $('#compare_manufacturer').val(),
			compareModelID = $('#compare_model').val(),
			compareEngineID = $('#compare_engine').val(),
            comparePhoto = $('#compare_photo').find('img').attr('src'),
			compareTime = $('#compare_time').val(),
			criteria = [],
			comment = $('#criteria_resume').val();
			
		var counter = 0;
		$('.fn-criteria').each(function(index, element) {
			++counter;
			var criteriaID = $(this).attr('data-id');

			var mainCriteriaElem = $('.fn-point-main-' + criteriaID + ' .active'),
                compareCriteriaElem = $('.fn-point-compare-' + criteriaID + ' .active');

			if (mainCriteriaElem.length && compareCriteriaElem.length) {
				var criteriaMainValue = mainCriteriaElem.text(),
                    criteriaCompareValue = compareCriteriaElem.text(),
                    criteriaComment = $('[name="criteria_comment_' + criteriaID + '"]').val();

				criteria.push({
					criteria_id: criteriaID,
					criteria_main_value: criteriaMainValue,
					criteria_compare_value: criteriaCompareValue,
					criteria_comment: criteriaComment
				});
			}
		});

		if (criteria.length < counter) {
			showMessage(localizationMessages['Enter all points'], localizationMessages['error']);
			return;
		}

        ajaxSpinner.button($(this), 'medium-white');

        var garage = 0,
            before = 0;

        $('[data-value]').each(function() {
            var inter = $(this).bootstrapSwitch('indeterminate');
            var state = $(this).bootstrapSwitch('state');

            if (inter == false) {
                if (state) {
                    garage = $(this).attr('data-value');
                } else {
                    before = $(this).attr('data-value');
                }
            }
        });

		$.ajax({
			url: baseURL + 'comparison/add-model',
			async: true,
			type: 'POST',
			dataType: 'html',
			data: {
				mainManufacturerID: mainManufacturerID,
				mainModelID: mainModelID,
				mainEngineID: mainEngineID,
				mainPhoto: mainPhoto,
				mainTime: mainTime,
				compareManufacturerID: compareManufacturerID,
				compareModelID: compareModelID,
				compareEngineID: compareEngineID,
				comparePhoto: comparePhoto,
				compareTime: compareTime,
				criteria: criteria,
				comment: comment,
                garage: garage,
                before: before
			},
			success: function(response) {
				var data = $.parseJSON(response);
				if (data.error) {
                    showMessage(data.error, localizationMessages['error']);
                } else {
					$('#comparison_values').slideUp();
					$('#criteria_resume').val('');
					$('.small-comment').val('');
					$('.comparison-add-point-handler').removeClass('active');
					$('.comparison-add-item-main-name, .comparison-add-item-compare-name').html('');

					$('#main_time, #compare_time').val(0);
					$('#main_manufacturer, #compare_manufacturer').val(0);

                    manufacturerChange('main');
					manufacturerChange('compare');

                    if (garage || before) {
                        $('.swith-input').bootstrapSwitch('toggleIndeterminate');
                    }

                    showMessage(localizationMessages['Comparison successfully added'], localizationMessages['success']);
				}
			},
			complete: function() {
				ajaxSpinner.stop(true);
			}
		});
	});

    var comparisonTabs = $('#comparison_tabs');
	if (comparisonTabs.length > 0) {
		comparisonTabs.bxSlider({
			pagerCustom: '#pager',
			adaptiveHeight: true,
			controls: false,
			onSliderLoad: function() {
                var bxViewport = $('.bx-viewport');
				bxViewport.css({
					left: $('.bx-wrapper').offset().left - bxViewport.offset().left
				})
			}
		});
	}
	
	var mainPoints = [],
		comparePoints = [],
		pointsCounter = -1;
	
	$('.points_scale.main .mid_mask_points').each(function(index, element) {
		mainPoints.push($(this));
	});
	
	$('.points_scale.compare .mid_mask_points').each(function(index, element) {
		comparePoints.push($(this));
	});

	//animatePoints();
	var animatePoints = function() {
		++pointsCounter;
		if (pointsCounter >= mainPoints.length) {
			return;
		}
		
		var left, 
			right,
			elem = mainPoints[pointsCounter],
			compare = comparePoints[pointsCounter];
		if (elem.hasClass('ps_1')) {
			left = -18;
		} else if (elem.hasClass('ps_2')) {
			left = -54;
		} else if (elem.hasClass('ps_3')) {
			left = -89;
		} else if (elem.hasClass('ps_4')) {
			left = -124;
		} else if (elem.hasClass('ps_5')) {
			left = -159;
		} else if (elem.hasClass('ps_6')) {
			left = -194;
		} else if (elem.hasClass('ps_7')) {
			left = -229;
		} else if (elem.hasClass('ps_8')) {
			left = -264;
		} else if (elem.hasClass('ps_9')) {
			left = -299;
		} else if (elem.hasClass('ps_10')) {
			left = -334;
		}
		
		if (compare.hasClass('ps_1')) {
			right = 18;
		} else if (compare.hasClass('ps_2')) {
			right = 53;
		} else if (compare.hasClass('ps_3')) {
			right = 88;
		} else if (compare.hasClass('ps_4')) {
			right = 123;
		} else if (compare.hasClass('ps_5')) {
			right = 158;
		} else if (compare.hasClass('ps_6')) {
			right = 193;
		} else if (compare.hasClass('ps_7')) {
			right = 228;
		} else if (compare.hasClass('ps_8')) {
			right = 263;
		} else if (compare.hasClass('ps_9')) {
			right = 298;
		} else if (compare.hasClass('ps_10')) {
			right = 333;
		}
		
		elem.animate({
			left: left
		}, 1000);
		
		compare.animate({
			left: right
		}, 1000, function() {
			animatePoints();
		});
	};
	
	$('.fnThanks').on('click', function() {
        if (!$(this).hasClass('disabled')) {
            ajaxSpinner.button($(this));
            $('.fnDislike').addClass('disabled');

            addOpinion(0);
        }
	});
	
	$('.fnDislike').on('click', function() {
	    if (!$(this).hasClass('disabled')) {
            ajaxSpinner.button($(this));
            $('.fnThanks').addClass('disabled');

            addOpinion(1);
        }
	});

    var addOpinion = function (like) {
        var comparisonId = $('#object_id').val();

        $.ajax({
            url: baseURL + 'comparison/add-opinion',
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {
                comparisonId: comparisonId,
                like: like
            },
            success: function(response) {
                if (isJson(response)) {
                    var data = $.parseJSON(response);
                    if (data.error) {
                        showMessage(data.error, 'Ошибка');
                    } else {
                        $('.comparison-thanks-wrapper').remove();
                        $('.comparison-say-thanks').after(data.list).remove();

                        updateTooltip();
                        updateThxLines();
                    }
                }
            },
            complete: function() {
                ajaxSpinner.stop(true);
            }
        });
    };

	var updateThxLines = function () {
		var thxWidth = 0,
			thxParent = $('#thanksList').width(),
			thxLine = 1,
			thxMaxLines = 2;

		$('.comparison-thanks-user-link').each(function(index, element) {
			if (thxLine <= thxMaxLines) {
				thxWidth += $(this).outerWidth(true);
				if (thxWidth > thxParent) {
					++thxLine;
					thxWidth = $(this).outerWidth(true);
					if (thxLine > thxMaxLines) {
						$(this).addClass('hidden-thx hidden');
					}
				}
			} else {
				$(this).addClass('hidden-thx hidden');
			}
		});

        if ($('.comparison-thanks-user-link.hidden-thx.hidden').length == 0) {
            $('.comparison-thanks-wrapper .fa').hide();
        } else {
            $('.comparison-thanks-wrapper .fa').show();
        }

		$('#btn-thanks').on('click', function(e) {
			e.preventDefault();
			$('.comparison-thanks-user-link.hidden-thx').toggleClass('hidden');
			if ($('.comparison-thanks-user-link.hidden-thx.hidden').length) {
				$('.comparison-thanks-wrapper h2 .fa').removeClass('fa-caret-up').addClass('fa-caret-down');
			} else {
				$('.comparison-thanks-wrapper h2 .fa').removeClass('fa-caret-down').addClass('fa-caret-up');
			}
		});
	};

	updateThxLines();

	$('.fnFavorite').on('click', function() {
		var comparisonId = $('#object_id').val();

        ajaxSpinner.button($(this), 'small-dark');

		$.ajax({
			url: baseURL + 'comparison/add-favorite',
			async: true,
			type: 'POST',
			dataType: 'html',
			data: {
                comparisonId: comparisonId
			},
			success: function(response) {
				if (isJson(response)) {
					var data = $.parseJSON(response);
					
					if (data.error) {
						showMessage(data.error, 'Ошибка');
					} else {
						$('.fnFavorite').after('<span class="fnFavorited">' +
						'<i class="fa fa-star"></i> В избранном</span>').remove();
					}
				}
			},
			complete: function() {
				ajaxSpinner.stop(true);
			}
		});
	});

    $('.fnSendCarRequest').on('click', function(e) {
        e.preventDefault();
        var $form = $('#car_request_form'),
            manufacturer = $('#carrequest-manufacturer'),
            model = $('#carrequest-model');

		if (manufacturer.val().length && model.val().length) {
            ajaxSpinner.button($(this));

            $.ajax({
                url: baseURL + 'catalog/car-request',
                async: true,
                type: 'POST',
                dataType: 'html',
                data: {
                    CarRequest: {
                        manufacturer: manufacturer.val(),
                        model: model.val()
                    }
                },
                success: function(response) {
                    var data = $.parseJSON(response);

                    if (data.status == 'ok') {
                        $('.no-auto-catalog > div').slideToggle();
                        showMessage(data.message, localizationMessages['New car']);
                        manufacturer.val('');
                        model.val('');
                    } else {
                        showMessage(data.message, localizationMessages['error']);
                    }
                },
                complete: function() {
                    ajaxSpinner.stop(true);
                }
            });
        }
    });

    $('.switch-input-clear').on('click', function() {
        $(this).parent().find('.switch-input').bootstrapSwitch('toggleIndeterminate');
    });

    var textarea = $('textarea.lineage');

    var textareaLineage = function() {
        $('textarea.lineage').each(function() {
            var el = $(this);
            var h = el.actual('height');
            var parent = el.parent('.lineage-wrap');

            if (parent.length == 0) {
                el.wrap('<div class="lineage-wrap"></div>');
                parent = el.parent('.lineage-wrap');
            }

            h = el.actual('height');
            parent.find('.lineage-line').remove();

            var line = parseInt(el.css('line-height'));
            var i = 0;

            while (i < (h / line)) {
                parent.prepend('<div class="lineage-line" style="top:' + (i + 1) * line + 'px;"/>');
                ++i;
            }
        });
    };

    if (textarea.length) {
        autosize(textarea);
        textarea.on('autosize:resized', function () {
            textareaLineage();
        });
		textareaLineage();
    }
});

var carRequestComplete = function () {
    ajaxSpinner.stop(true);
};