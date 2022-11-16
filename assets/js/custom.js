import NioApp from './vendors/nioapp/nioapp.min';

!(function(NioApp, $) {

	var $win = $(window), $body = $('body'), $doc = $(document),

		//class names
		_body_theme = 'nio-theme',
		_menu = 'nk-menu',
		_mobile_nav = 'mobile-menu',
		_header = 'nk-header',
		_header_menu = 'nk-header-menu',
		_sidebar = 'nk-sidebar',
		_sidebar_mob = 'nk-sidebar-mobile',
		//breakpoints
		_break = NioApp.Break;

	function extend(obj, ext) {
		Object.keys(ext).forEach(function(key) {
			obj[key] = ext[key];
		});
		return obj;
	}

	// ClassInit @v1.0
	NioApp.ClassBody = function() {
		NioApp.AddInBody(_sidebar);
	};

	// ClassInit @v1.0
	NioApp.ClassNavMenu = function() {
		NioApp.BreakClass('.' + _header_menu, _break.lg, {timeOut: 0});
		$win.on('resize', function() {
			NioApp.BreakClass('.' + _header_menu, _break.lg);
		});
	};

	NioApp.TGL = {
		...NioApp.TGL,
		screen: function(elm) {
			if ($(elm).exists()) {
				$(elm).each(function() {
					var ssize = $(this).data('toggle-screen');
					if (ssize) {
						$(this).addClass('toggle-screen-' + ssize);
					}
				});
			}
		},
		content: function(elm, opt) {
			var toggle = (elm) ? elm : '.toggle', $toggle = $(toggle),
				$contentD = $('[data-content]'),
				toggleBreak = true, toggleCurrent = false,
				def = {
					active: 'active',
					content: 'content-active',
					break: toggleBreak,
				},
				attr = (opt) ? extend(def, opt) : def;

			NioApp.TGL.screen($contentD);

			$toggle.on('click', function(e) {
				toggleCurrent = this;
				NioApp.Toggle.trigger($(this).data('target'), attr);
				e.preventDefault();
			});

			$doc.on('mouseup', function(e) {
				if (toggleCurrent) {
					var $toggleCurrent = $(toggleCurrent),
						currentTarget = $(toggleCurrent).data('target'),
						$contentCurrent = $(`[data-content="${currentTarget}"]`),
						$s2c = $('.select2-container'),
						$dpd = $('.datepicker-dropdown'),
						$tpc = $('.ui-timepicker-container');
					if (!$toggleCurrent.is(e.target) &&
						$toggleCurrent.has(e.target).length === 0 &&
						!$contentCurrent.is(e.target) &&
						$contentCurrent.has(e.target).length === 0
						&& !$s2c.is(e.target) && $s2c.has(e.target).length ===
						0 &&
						!$dpd.is(e.target) && $dpd.has(e.target).length === 0
						&& !$tpc.is(e.target) && $tpc.has(e.target).length ===
						0) {
						NioApp.Toggle.removed($toggleCurrent.data('target'),
							attr);
						toggleCurrent = false;
					}
				}
			});

			$win.on('resize', function() {
				$contentD.each(function() {
					var content = $(this).data('content'),
						ssize = $(this).data('toggle-screen'),
						toggleBreak = _break[ssize];
					if (NioApp.Win.width > toggleBreak) {
						NioApp.Toggle.removed(content, attr);
					}
				});
			});
		},
		expand: function(elm, opt) {
			var toggle = (elm) ? elm : '.expand', def = {toggle: true},
				attr = (opt) ? extend(def, opt) : def;

			$(toggle).on('click', function(e) {
				NioApp.Toggle.trigger($(this).data('target'), attr);
				e.preventDefault();
			});
		},
		ddmenu: function(elm, opt) {
			var imenu = (elm) ? elm : '.nk-menu-toggle',
				def = {
					active: 'active',
					self: 'nk-menu-toggle',
					child: 'nk-menu-sub',
				},
				attr = (opt) ? extend(def, opt) : def;

			$(imenu).on('click', function(e) {
				if ((NioApp.Win.width < _break.lg) ||
					($(this).parents().hasClass(_sidebar))) {
					NioApp.Toggle.dropMenu($(this), attr);
				}
				e.preventDefault();
			});
		},
		showmenu: function(elm, opt) {
			var toggle = (elm) ? elm : '.nk-nav-toggle', $toggle = $(toggle),
				$contentD = $('[data-content]'),
				toggleBreak = ($contentD.hasClass(_header_menu))
					? _break.lg
					: _break.xl,
				toggleOlay = _sidebar + '-overlay',
				toggleClose = {profile: true, menu: false},
				def = {
					active: 'toggle-active',
					content: _sidebar + '-active',
					body: 'nav-shown',
					overlay: toggleOlay,
					break: toggleBreak,
					close: toggleClose,
				},
				attr = (opt) ? extend(def, opt) : def;

			$toggle.on('click', function(e) {
				NioApp.Toggle.trigger($(this).data('target'), attr);
				e.preventDefault();
			});

			$doc.on('mouseup', function(e) {
				if (!$toggle.is(e.target) && $toggle.has(e.target).length ===
					0 &&
					!$contentD.is(e.target) &&
					$contentD.has(e.target).length === 0 &&
					NioApp.Win.width < toggleBreak) {
					NioApp.Toggle.removed($toggle.data('target'), attr);
				}
			});

			$win.on('resize', function() {
				if (NioApp.Win.width < _break.xl || NioApp.Win.width <
					toggleBreak) {
					NioApp.Toggle.removed($toggle.data('target'), attr);
				}
			});
		},
	};
	// Compact Sidebar @v1.0
	NioApp.sbCompact = function() {
		var toggle = '.nk-nav-compact', $toggle = $(toggle),
			$content = $('[data-content]');

		$toggle.on('click', function(e) {
			e.preventDefault();
			var $self = $(this), get_target = $self.data('target'),
				$self_content = $('[data-content=' + get_target + ']');

			$self.toggleClass('compact-active');
			$self_content.toggleClass('is-compact');
		});
	};

	// Wizard @v1.0
	NioApp.Wizard = function() {
		var $wizard = $('.nk-wizard');
		if ($wizard.exists()) {
			$wizard.each(function() {
				var $self = $(this), _self_id = $self.attr('id'),
					$self_id = $('#' + _self_id).show();
				$self_id.steps({
					headerTag: '.nk-wizard-head',
					bodyTag: '.nk-wizard-content',
					labels: {
						finish: 'Submit',
						next: 'Next',
						previous: 'Prev',
						loading: 'Loading ...',
					},
					titleTemplate: '<span class="number">0#index#</span> #title#',
					onStepChanging: function(event, currentIndex, newIndex) {
						// Allways allow previous action even if the current form is not valid!
						if (currentIndex > newIndex) {
							return true;
						}
						// Needed in some cases if the user went back (clean up)
						if (currentIndex < newIndex) {
							// To remove error styles
							$self_id.find(
								'.body:eq(' + newIndex + ') label.error').
								remove();
							$self_id.find('.body:eq(' + newIndex + ') .error').
								removeClass('error');
						}
						$self_id.validate().settings.ignore = ':disabled,:hidden';
						return $self_id.valid();
					},
					onFinishing: function(event, currentIndex) {
						$self_id.validate().settings.ignore = ':disabled';
						return $self_id.valid();
					},
					onFinished: function(event, currentIndex) {
						window.location.href = '#';
					},

				}).validate({
					errorElement: 'span',
					errorClass: 'invalid',
					errorPlacement: function(error, element) {
						error.appendTo(element.parent());
					},
				});
			});
		}
	};

	// BootStrap Extended
	NioApp.BS = {
		...NioApp.BS,
		ddfix: function(elm, exc) {
			var dd = (elm) ? elm : '.dropdown-menu',
				ex = (exc)
					? exc
					: 'a:not(.clickable), button:not(.clickable), a:not(.clickable) *, button:not(.clickable) *';

			$(dd).on('click', function(e) {
				if (!$(e.target).is(ex)) {
					e.stopPropagation();
					return;
				}
			});
			if (NioApp.State.isRTL) {
				var $dMenu = $('.dropdown-menu');
				$dMenu.each(function() {
					var $self = $(this);
					if ($self.hasClass('dropdown-menu-end') &&
						!$self.hasClass('dropdown-menu-center')) {
						$self.prev('[data-bs-toggle="dropdown"]').dropdown({
							popperConfig: {
								placement: 'bottom-start',
							},
						});
					} else if (!$self.hasClass('dropdown-menu-end') &&
						!$self.hasClass('dropdown-menu-center')) {
						$self.prev('[data-bs-toggle="dropdown"]').dropdown({
							popperConfig: {
								placement: 'bottom-end',
							},
						});
					}
				});
			}
		},

		tabfix: function(elm) {
			var tab = (elm) ? elm : '[data-toggle="modal"]';
			$(tab).on('click', function() {
				var _this = $(this), target = _this.data('target'),
					target_href = _this.attr('href'),
					tg_tab = _this.data('tab-target');

				var modal = (target) ? $body.find(target) : $body.find(
					target_href);
				if (tg_tab && tg_tab !== '#' && modal) {
					modal.find('[href="' + tg_tab + '"]').tab('show');
				} else if (modal) {
					var tabdef = modal.find('.nk-nav.nav-tabs');
					var link = $(tabdef[0]).find('[data-bs-toggle="tab"]');
					$(link[0]).tab('show');
				}
			});
		}
	}

	// Dark Mode Switch @since v2.0
	NioApp.ModeSwitch = function() {
		var toggle = $('.dark-switch');
		if ($body.hasClass('dark-mode')) {
			toggle.addClass('active');
		} else {
			toggle.removeClass('active');
		}
		toggle.on('click', function(e) {
			e.preventDefault();
			$(this).toggleClass('active');
			$body.toggleClass('dark-mode');
		});
	};

	// Controls @v1.0.0
	NioApp.Control = function(elm) {
		var control = document.querySelectorAll(elm);
		control.forEach(function(item, index, arr) {
			item.checked ? item.parentNode.classList.add('checked') : null;
			item.addEventListener('change', function() {
				if (item.type == 'checkbox') {
					item.checked
						? item.parentNode.classList.add('checked')
						: item.parentNode.classList.remove('checked');
				}
				if (item.type == 'radio') {
					document.querySelectorAll(
						'input[name="' + item.name + '"]').
						forEach(function(item, index, arr) {
							item.parentNode.classList.remove('checked');
						});
					item.checked
						? item.parentNode.classList.add('checked')
						: null;
				}
			});
		});
	};

	// Extra @v1.1
	NioApp.OtherInit = function() {
		NioApp.ClassBody();
		NioApp.LinkOff('.is-disable');
		NioApp.ClassNavMenu();
		NioApp.SetHW('[data-height]', 'height');
		NioApp.SetHW('[data-width]', 'width');
		NioApp.Control('.custom-control-input');
	};

	// BootstrapExtend Init @v1.0
	NioApp.BS.init = function() {
		NioApp.BS.menutip('a.nk-menu-link');
		NioApp.BS.tooltip('.nk-tooltip');
		NioApp.BS.tooltip('.btn-tooltip', {placement: 'top'});
		NioApp.BS.tooltip('[data-toggle="tooltip"]');
		NioApp.BS.tooltip('[data-bs-toggle="tooltip"]');
		NioApp.BS.tooltip('.tipinfo,.nk-menu-tooltip', {placement: 'right'});
		NioApp.BS.popover('[data-toggle="popover"]');
		NioApp.BS.popover('[data-bs-toggle="popover"]');
		NioApp.BS.progress('[data-progress]');
		NioApp.BS.fileinput('.form-file-input');
		NioApp.BS.modalfix();
		NioApp.BS.ddfix();
		NioApp.BS.tabfix();
	};

	// Picker Init @v1.0
	NioApp.Picker = {
		...NioApp.Picker,
		init: function() {
			NioApp.Picker.date('.date-picker');
			NioApp.Picker.dob('.date-picker-alt');
			NioApp.Picker.time('.time-picker');
			NioApp.Picker.date('.date-picker-range', {
				todayHighlight: false,
				autoclose: false,
			});
		}
	}

	// Toggler @v1
	NioApp.TGL = {
		...NioApp.TGL,
		init : function() {
			NioApp.TGL.content('.toggle');
			NioApp.TGL.expand('.toggle-expand');
			NioApp.TGL.expand('.toggle-opt', {toggle: false});
			NioApp.TGL.showmenu('.nk-nav-toggle');
			NioApp.TGL.ddmenu('.' + _menu + '-toggle',
				{self: _menu + '-toggle', child: _menu + '-sub'});
		}
	}

	NioApp.BS.modalOnInit = function() {
		$('.modal').on('shown.bs.modal', function() {
			NioApp.Validate.init();
		});

	};

	// Initial by default
	/////////////////////////////
	NioApp.init = function() {
		NioApp.coms.docReady.push(NioApp.OtherInit);
		NioApp.coms.docReady.push(NioApp.ColorBG);
		NioApp.coms.docReady.push(NioApp.ColorTXT);
		NioApp.coms.docReady.push(NioApp.Ani.init);
		NioApp.coms.docReady.push(NioApp.TGL.init);
		NioApp.coms.docReady.push(NioApp.BS.init);
		NioApp.coms.docReady.push(NioApp.Picker.init);
		NioApp.coms.docReady.push(NioApp.Wizard);
		NioApp.coms.docReady.push(NioApp.sbCompact);
		NioApp.coms.winLoad.push(NioApp.ModeSwitch);
	};

	NioApp.init();

	return NioApp;
})(NioApp, jQuery);
