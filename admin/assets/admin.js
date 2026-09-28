/* global jQuery, scraperblockAdmin */
(function ($) {
	'use strict';

	var cfg = window.scraperblockAdmin || {};
	var i18n = cfg.i18n || {};
	var $tabs = $('.gg-tab-btn');
	var $breadcrumbCurrent = $('#gg-breadcrumb-current');
	var $footerBar = $('#gg-footer-bar');
	var $form = $('#gg-settings-form');
	var $saveBtn = $('#gg-save-btn');
	var $resetBtn = $('#gg-reset-btn');
	var $toast = $('#gg-toast');
	var toastTimer = null;

	function updateTabUi(target, label) {
		if ($breadcrumbCurrent.length) {
			$breadcrumbCurrent.text(label || '');
		}
		if ($footerBar.length) {
			var visible = ($footerBar.data('tab-visible') || '').toString().split(',');
			$footerBar.toggle(visible.indexOf(target) !== -1);
		}
	}

	function setBusy(isBusy) {
		$saveBtn.prop('disabled', isBusy);
		$resetBtn.prop('disabled', isBusy);
	}

	function showToast(message, type) {
		if (!$toast.length) {
			return;
		}
		clearTimeout(toastTimer);
		var cls = 'gg-toast gg-toast-show ';
		if (type === 'error') {
			cls += 'gg-toast-error';
		} else if (type === 'reset') {
			cls += 'gg-toast-reset';
		} else {
			cls += 'gg-toast-success';
		}
		$toast.attr('class', cls).text(message || '');
		toastTimer = setTimeout(function () {
			$toast.removeClass('gg-toast-show');
		}, 2200);
	}

	function applySettingsToForm(settings) {
		if (!settings || typeof settings !== 'object') {
			return;
		}
		Object.keys(settings).forEach(function (key) {
			var selector = '[name="scraperblock_settings[' + key + ']"]';
			var $field = $(selector);
			if (!$field.length) {
				return;
			}
			if ($field.is(':checkbox')) {
				$field.prop('checked', String(settings[key]) === 'yes');
			} else {
				$field.val(settings[key]);
			}
			$field.trigger('change');
		});
	}

	function initBotListShowcase() {
		var $searchInputs = $('.gg-botlist-search');
		if ($searchInputs.length) {
			$searchInputs.on('input', function () {
				var query = ($(this).val() || '').toString().trim().toLowerCase();
				var $chips = $(this).closest('.gg-botlist-card').find('.gg-bot-chip');
				$chips.each(function () {
					var haystack = ($(this).data('bot-signature') || '').toString();
					$(this).toggle(query === '' || haystack.indexOf(query) !== -1);
				});
			});
		}
	}

	if ($tabs.length) {
		$tabs.on('click', function () {
			var $btn = $(this);
			var target = ($btn.data('tab') || '').toString();
			var label = ($btn.data('breadcrumb') || $btn.text() || '').toString().trim();

			$tabs.removeClass('gg-tab-active').attr('aria-selected', 'false');
			$btn.addClass('gg-tab-active').attr('aria-selected', 'true');
			$('.gg-tab-panel').removeClass('gg-tab-panel-active');
			$('[data-panel="' + target + '"]').addClass('gg-tab-panel-active');
			updateTabUi(target, label);
		});

		var $active = $('.gg-tab-btn.gg-tab-active').first();
		if ($active.length) {
			updateTabUi(($active.data('tab') || '').toString(), ($active.data('breadcrumb') || $active.text() || '').toString().trim());
		}
	}

	initBotListShowcase();

	if (!$form.length || !cfg.ajaxUrl || !cfg.saveAction || !cfg.saveNonce) {
		return;
	}

	$saveBtn.on('click', function () {
		$form.trigger('submit');
	});

	$form.on('submit', function (event) {
		event.preventDefault();
		setBusy(true);

		var formData = $form.serializeArray();
		formData.push({ name: 'action', value: cfg.saveAction });
		formData.push({ name: 'nonce', value: cfg.saveNonce });

		$.post(cfg.ajaxUrl, $.param(formData))
			.done(function (response) {
				if (response && response.success) {
					showToast((response.data && response.data.message) || i18n.saved || 'Settings saved!', 'success');
				} else {
					showToast((response && response.data && response.data.message) || i18n.save_error || 'Unable to save settings. Please try again.', 'error');
				}
			})
			.fail(function () {
				showToast(i18n.save_error || 'Unable to save settings. Please try again.', 'error');
			})
			.always(function () {
				setBusy(false);
			});
	});

	$resetBtn.on('click', function () {
		if (!cfg.resetAction || !cfg.resetNonce) {
			return;
		}
		if (!window.confirm(i18n.reset_confirm || 'Reset all settings to default values?')) {
			return;
		}
		setBusy(true);
		$.post(cfg.ajaxUrl, {
			action: cfg.resetAction,
			nonce: cfg.resetNonce
		}).done(function (response) {
			if (response && response.success) {
				applySettingsToForm(response.data && response.data.settings ? response.data.settings : {});
				showToast((response.data && response.data.message) || i18n.reset_done || 'Settings reset to defaults.', 'reset');
				return;
			}
			showToast((response && response.data && response.data.message) || i18n.reset_error || 'Unable to reset settings. Please try again.', 'error');
		}).fail(function () {
			showToast(i18n.reset_error || 'Unable to reset settings. Please try again.', 'error');
		}).always(function () {
			setBusy(false);
		});
	});

	// ── Review notice dismiss ─────────────────────────────────────────────
	if ( typeof scraperblock_notices !== 'undefined' && scraperblock_notices.nonce ) {
		$( document ).on( 'click', '[data-gg-review-action]', function () {
			var action  = $( this ).data( 'gg-review-action' );
			var $notice = $( '#gg-review-notice' );
			$.post( scraperblock_notices.ajaxUrl, {
				action:         'scraperblock_dismiss_review',
				nonce:          scraperblock_notices.nonce,
				dismiss_action: action,
			} ).always( function () {
				$notice.slideUp( 200 );
			} );
		} );

		// WP's native X button — treat as "Maybe Later" (14-day hide).
		$( '#gg-review-notice' ).on( 'click', '.notice-dismiss', function () {
			$.post( scraperblock_notices.ajaxUrl, {
				action:         'scraperblock_dismiss_review',
				nonce:          scraperblock_notices.nonce,
				dismiss_action: 'later',
			} );
		} );
	}

	// ── Help dropdown ─────────────────────────────────────────────────────────
	var $helpBtn      = $( '#gg-help-btn' );
	var $helpDropdown = $( '#gg-help-dropdown' );

	$helpBtn.on( 'click', function ( e ) {
		e.stopPropagation();
		var opening = $helpDropdown.attr( 'hidden' ) !== undefined;
		if ( opening ) {
			$helpDropdown.removeAttr( 'hidden' );
			$helpBtn.addClass( 'is-open' ).attr( 'aria-expanded', 'true' );
		} else {
			$helpDropdown.attr( 'hidden', '' );
			$helpBtn.removeClass( 'is-open' ).attr( 'aria-expanded', 'false' );
		}
	} );

	$( document ).on( 'click', function () {
		$helpDropdown.attr( 'hidden', '' );
		$helpBtn.removeClass( 'is-open' ).attr( 'aria-expanded', 'false' );
	} );

	$helpDropdown.on( 'click', function ( e ) {
		e.stopPropagation();
	} );

	// Escape closes the menu and hands focus back to the Help button.
	$( document ).on( 'keydown', function ( e ) {
		if ( 'Escape' === e.key && $helpDropdown.attr( 'hidden' ) === undefined ) {
			$helpDropdown.attr( 'hidden', '' );
			$helpBtn.removeClass( 'is-open' ).attr( 'aria-expanded', 'false' ).trigger( 'focus' );
		}
	} );
})(jQuery);