const divisor = document.getElementById("divisor"),
	handle = document.getElementById("handle"),
	slider = document.getElementById("slider");
export function moveDivisor() {
	if (handle)
		handle.style.left = slider.value+"%";
	if (divisor)
		divisor.style.width = slider.value+"%";
}

export const getSiblings = function (elem) {
	const siblings = [];
	let sibling = elem.parentNode.firstChild;
	while (sibling) {
		if (sibling.nodeType === 1 && sibling !== elem) {
			siblings.push(sibling);
		}
		sibling = sibling.nextSibling;
	}
	return siblings;
};

export const slideUp = (target, time) => {
	const duration = time ? time : 500;
	target.style.transitionProperty = "height, margin, padding";
	target.style.transitionDuration = duration + "ms";
	target.style.boxSizing = "border-box";
	target.style.height = target.offsetHeight + "px";
	target.offsetHeight;
	target.style.overflow = "hidden";
	target.style.height = 0;
	window.setTimeout(() => {
		target.style.display = "none";
		target.style.removeProperty("height");
		target.style.removeProperty("overflow");
		target.style.removeProperty("transition-duration");
		target.style.removeProperty("transition-property");
	}, duration);
};

export const slideDown = (target, time) => {
	const duration = time ? time : 500;
	target.style.removeProperty("display");
	let display = window.getComputedStyle(target).display;
	if (display === "none") display = "block";
	target.style.display = display;
	const height = target.offsetHeight;
	target.style.overflow = "hidden";
	target.style.height = 0;
	target.offsetHeight;
	target.style.boxSizing = "border-box";
	target.style.transitionProperty = "height, margin, padding";
	target.style.transitionDuration = duration + "ms";
	target.style.height = height + "px";
	window.setTimeout(() => {
		target.style.removeProperty("height");
		target.style.removeProperty("overflow");
		target.style.removeProperty("transition-duration");
		target.style.removeProperty("transition-property");
	}, duration);
};

export function topOffset(el) {
	let rect = el.getBoundingClientRect(),
		scrollTop = window.pageYOffset || document.documentElement.scrollTop;
	return { top: rect.top + scrollTop };
}

export const tab = function (wrapper) {
	let tabContainer = document.querySelector(wrapper);
	if (tabContainer) {
		tabContainer.addEventListener("click", function (evt) {
			let listItem = evt.target;
			if (listItem.hasAttribute("data-toggle")) {
				let targetId = listItem.dataset.target,
					targetItem = document.querySelector(targetId);
				listItem.parentElement
				.querySelectorAll('[data-toggle="tab"]')
				.forEach(function (list) {
					list.classList.remove("active");
				});
				listItem.classList.add("active");
				targetItem.classList.add("active");
				setTimeout(function () {
					targetItem.classList.add("show");
				}, 150);
				getSiblings(targetItem).forEach(function (pane) {
					pane.classList.remove("show");
					setTimeout(function () {
						pane.classList.remove("active");
					}, 150);
				});
			}
		});
	}
};

export const activeClassAction = function (toggle, target) {
	const to = document.querySelector(toggle),
		ta = document.querySelector(target);
	if (to && ta) {
		to.addEventListener("click", function (e) {
			e.preventDefault();
			let triggerItem = e.target;
			if (triggerItem.classList.contains("active")) {
				triggerItem.classList.remove("active");
				ta.classList.remove("active");
			} else {
				triggerItem.classList.add("active");
				ta.classList.add("active");
			}
		});
		document.addEventListener("click", function (event) {
			if (
				!event.target.closest(toggle) &&
				!event.target.classList.contains(toggle.replace(/\./, ""))
			) {
				if (
					!event.target.closest(target) &&
					!event.target.classList.contains(target.replace(/\./, ""))
				) {
					to.classList.remove("active");
					ta.classList.remove("active");
				}
			}
		});
	}
};

export function offCanvasSidebar(openTrigger, closeTrigger, wrapper) {
	let OpenTriggerPrimary__btn = document.querySelectorAll(openTrigger);
	let closeTriggerPrimary__btn = document.querySelector(closeTrigger);
	let WrapperSidebar = document.querySelector(wrapper);
	let wrapperOverlay = wrapper.replace(".", "");

	function handleBodyClass(evt) {
		let eventTarget = evt.target;
		if (!eventTarget.closest(wrapper) && !eventTarget.closest(openTrigger)) {
			WrapperSidebar.classList.remove("active");
			document
			.querySelector("body")
			.classList.remove(`${wrapperOverlay}_active`);
		}
	}
	if (OpenTriggerPrimary__btn && WrapperSidebar) {
		OpenTriggerPrimary__btn.forEach(function (singleItem) {
			singleItem.addEventListener("click", function (e) {
				if (e.target.dataset.offcanvas !== undefined) {
					WrapperSidebar.classList.add("active");
					document
					.querySelector("body")
					.classList.add(`${wrapperOverlay}_active`);
					document.body.addEventListener("click", handleBodyClass.bind(this));
				}
			});
		});
	}

	if (closeTriggerPrimary__btn && WrapperSidebar) {
		closeTriggerPrimary__btn.addEventListener("click", function (e) {
			if (e.target.dataset.offcanvas !== undefined) {
				WrapperSidebar.classList.remove("active");
				document
				.querySelector("body")
				.classList.remove(`${wrapperOverlay}_active`);
				document.body.removeEventListener("click", handleBodyClass.bind(this));
			}
		});
	}
}

export function customAccordion(accordionWrapper, singleItem, accordionBody) {
	let accoridonButtons = document.querySelectorAll(accordionWrapper);
	accoridonButtons.forEach(function (item) {
		item.addEventListener("click", function (evt) {
			let itemTarget = evt.target;
			if (
				itemTarget.classList.contains("accordion__items--button") ||
				itemTarget.classList.contains("widget__categories--menu__label")
			) {
				let singleAccordionWrapper = itemTarget.closest(singleItem),
					singleAccordionBody =
						singleAccordionWrapper.querySelector(accordionBody);
				if (singleAccordionWrapper.classList.contains("active")) {
					singleAccordionWrapper.classList.remove("active");
					slideUp(singleAccordionBody);
				} else {
					singleAccordionWrapper.classList.add("active");
					slideDown(singleAccordionBody);
					getSiblings(singleAccordionWrapper).forEach(function (item) {
						let sibllingSingleAccordionBody = item.querySelector(accordionBody);
						item.classList.remove("active");
						slideUp(sibllingSingleAccordionBody);
					});
				}
			}
		});
	});
}

export const offCanvasHeader = function () {
	const offCanvasOpen = document.querySelector(
			".offcanvas__header--menu__open--btn"
		),
		offCanvasClose = document.querySelector(".offcanvas__close--btn"),
		offCanvasHeader = document.querySelector(".offcanvas__header"),
		offCanvasMenu = document.querySelector(".offcanvas__menu"),
		body = document.querySelector("body");
	/* Offcanvas SubMenu Toggle */
	if (offCanvasMenu) {
		offCanvasMenu
		.querySelectorAll(".offcanvas__sub_menu")
		.forEach(function (ul) {
			const subMenuToggle = document.createElement("button");
			subMenuToggle.classList.add("offcanvas__sub_menu_toggle");
			ul.parentNode.appendChild(subMenuToggle);
		});
	}
	/* Open/Close Menu On Click Toggle Button */
	if (offCanvasOpen) {
		offCanvasOpen.addEventListener("click", function (e) {
			e.preventDefault();
			if (e.target.dataset.offcanvas !== undefined) {
				offCanvasHeader.classList.add("open");
				body.classList.add("mobile_menu_open");
			}
		});
	}
	if (offCanvasClose) {
		offCanvasClose.addEventListener("click", function (e) {
			e.preventDefault();
			if (e.target.dataset.offcanvas !== undefined) {
				offCanvasHeader.classList.remove("open");
				body.classList.remove("mobile_menu_open");
			}
		});
	}

	/* Mobile submenu slideToggle Activation */
	let mobileMenuWrapper = document.querySelector(".offcanvas__menu_ul");
	if (mobileMenuWrapper) {
		mobileMenuWrapper.addEventListener("click", function (e) {
			let targetElement = e.target;
			if (targetElement.classList.contains("offcanvas__sub_menu_toggle")) {
				const parent = targetElement.parentElement;
				if (parent.classList.contains("active")) {
					targetElement.classList.remove("active");
					parent.classList.remove("active");
					parent
					.querySelectorAll(".offcanvas__sub_menu")
					.forEach(function (subMenu) {
						subMenu.parentElement.classList.remove("active");
						subMenu.nextElementSibling.classList.remove("active");
						slideUp(subMenu);
					});
				} else {
					targetElement.classList.add("active");
					parent.classList.add("active");
					slideDown(targetElement.previousElementSibling);
					getSiblings(parent).forEach(function (item) {
						item.classList.remove("active");
						item
						.querySelectorAll(".offcanvas__sub_menu")
						.forEach(function (subMenu) {
							subMenu.parentElement.classList.remove("active");
							subMenu.nextElementSibling.classList.remove("active");
							slideUp(subMenu);
						});
					});
				}
			}
		});
	}

	if (offCanvasHeader) {
		document.addEventListener("click", function (event) {
			if (
				!event.target.closest(".offcanvas__header--menu__open--btn") &&
				!event.target.classList.contains(
					".offcanvas__header--menu__open--btn".replace(/\./, "")
				)
			) {
				if (
					!event.target.closest(".offcanvas__header") &&
					!event.target.classList.contains(
						".offcanvas__header".replace(/\./, "")
					)
				) {
					offCanvasHeader.classList.remove("open");
					body.classList.remove("mobile_menu_open");
				}
			}
		});
	}

	/* Remove Mobile Menu Open Class & Hide Mobile Menu When Window Width in More Than 991 */
	if (offCanvasHeader) {
		window.addEventListener("resize", function () {
			if (window.outerWidth >= 992) {
				offCanvasHeader.classList.remove("open");
				body.classList.remove("mobile_menu_open");
			}
		});
	}
};

export const categoryMobileMenu = function () {
	const CategorySubMenu = document.querySelector(".category__mobile--menu");
	if (CategorySubMenu) {
		CategorySubMenu.querySelectorAll(".category__sub--menu").forEach(function (
			ul
		) {
			let catsubMenuToggle = document.createElement("button");
			catsubMenuToggle.classList.add("category__sub--menu_toggle");
			ul.parentNode.appendChild(catsubMenuToggle);
		});
	}
	let categoryMenuWrapper = document.querySelector(
		".category__mobile--menu_ul"
	);
	if (categoryMenuWrapper) {
		categoryMenuWrapper.addEventListener("click", function (e) {
			let targetElement = e.target;
			if (targetElement.classList.contains("category__sub--menu_toggle")) {
				const parent = targetElement.parentElement;
				if (parent.classList.contains("active")) {
					targetElement.classList.remove("active");
					parent.classList.remove("active");
					parent
					.querySelectorAll(".category__sub--menu")
					.forEach(function (subMenu) {
						subMenu.parentElement.classList.remove("active");
						subMenu.nextElementSibling.classList.remove("active");
						slideUp(subMenu);
					});
				} else {
					targetElement.classList.add("active");
					parent.classList.add("active");
					slideDown(targetElement.previousElementSibling);
					getSiblings(parent).forEach(function (item) {
						item.classList.remove("active");
						item
						.querySelectorAll(".category__sub--menu")
						.forEach(function (subMenu) {
							subMenu.parentElement.classList.remove("active");
							subMenu.nextElementSibling.classList.remove("active");
							slideUp(subMenu);
						});
					});
				}
			}
		});
	}
};

export const newsletterPopup = function () {
	let newsletterWrapper = document.querySelector(".newsletter__popup"),
		newsletterCloseButton = document.querySelector(
			".newsletter__popup--close__btn"
		),
		dontShowPopup = document.querySelector("#newsletter__dont--show"),
		popuDontShowMode = localStorage.getItem("newsletter__show");

	if (newsletterWrapper && popuDontShowMode == null) {
		window.addEventListener("load", (event) => {
			setTimeout(function () {
				document.body.classList.add("overlay__active");
				newsletterWrapper.classList.add("newsletter__show");

				document.addEventListener("click", function (event) {
					if (!event.target.closest(".newsletter__popup--inner")) {
						document.body.classList.remove("overlay__active");
						newsletterWrapper.classList.remove("newsletter__show");
					}
				});

				newsletterCloseButton.addEventListener("click", function () {
					document.body.classList.remove("overlay__active");
					newsletterWrapper.classList.remove("newsletter__show");
				});

				dontShowPopup.addEventListener("click", function () {
					if (dontShowPopup.checked) {
						localStorage.setItem("newsletter__show", true);
					} else {
						localStorage.removeItem("newsletter__show");
					}
				});
			}, 3000);
		});
	}
};
