import {
	activeClassAction,
	categoryMobileMenu,
	customAccordion,
	getSiblings,
	moveDivisor,
	newsletterPopup,
	offCanvasHeader,
	offCanvasSidebar,
	slideDown,
	slideUp,
	tab,
	topOffset,
} from '../functions/base_utils';
import Glightbox from 'glightbox/src/js/glightbox';

let preloaderWrapper = document.getElementById("preloader");
window.onload = function() {
	moveDivisor()
	preloaderWrapper.classList.add("loaded")
}

const headerStickyWrapper = document.querySelector("header");
const headerStickyTarget = document.querySelector(".header__sticky");

if (headerStickyTarget) {
	window.addEventListener("scroll", function () {
		let StickyTargetElement = topOffset(headerStickyWrapper);
		let TargetElementTopOffset = StickyTargetElement.top;

		if (window.scrollY > TargetElementTopOffset) {
			headerStickyTarget.classList.add("sticky");
		} else {
			headerStickyTarget.classList.remove("sticky");
		}
	});
}

const scrollTop = document.getElementById("scroll__top");
if (scrollTop) {
	scrollTop.addEventListener("click", function () {
		window.scroll({ top: 0, left: 0, behavior: "smooth" });
	});
	window.addEventListener("scroll", function () {
		if (window.scrollY > 300) {
			scrollTop.classList.add("active");
		} else {
			scrollTop.classList.remove("active");
		}
	});
}

tab(".product__tab--one")

document.querySelectorAll("[data-countdown]").forEach(function (elem) {
	const countDownItem = function (value, label) {
		return `<div class="countdown__item" ${label}"><span class="countdown__number">${value}</span><p class="countdown__text">${label}</p></div>`;
	};
	const date = new Date(elem.getAttribute("data-countdown")).getTime(),
		second = 1000,
		minute = second * 60,
		hour = minute * 60,
		day = hour * 24;
	const countDownInterval = setInterval(function () {
		let currentTime = new Date().getTime(),
			timeDistance = date - currentTime,
			daysValue = Math.floor(timeDistance / day),
			hoursValue = Math.floor((timeDistance % day) / hour),
			minutesValue = Math.floor((timeDistance % hour) / minute),
			secondsValue = Math.floor((timeDistance % minute) / second);

		elem.innerHTML =
			countDownItem(daysValue, "days") +
			countDownItem(hoursValue, "hrs") +
			countDownItem(minutesValue, "mins") +
			countDownItem(secondsValue, "secs");

		if (timeDistance < 0) clearInterval(countDownInterval);
	}, 1000);
});

activeClassAction(".offcanvas__account--currency__menu", ".offcanvas__account--currency__submenu");
activeClassAction(".currency__link", ".dropdown__currency");
activeClassAction(".language__switcher", ".dropdown__language");
activeClassAction(".offcanvas__language--switcher", ".offcanvas__dropdown--language");

offCanvasSidebar(".minicart__open--btn", ".minicart__close--btn", ".offCanvas__minicart");
offCanvasSidebar(".search__open--btn", ".predictive__search--close__btn", ".predictive__search--box");
offCanvasSidebar(".widget__filter--btn", ".offcanvas__filter--close", ".offcanvas__filter--sidebar");

const quantityWrapper = document.querySelectorAll(".quantity__box");
if (quantityWrapper) {
	quantityWrapper.forEach(function (singleItem) {
		let increaseButton = singleItem.querySelector(".increase");
		let decreaseButton = singleItem.querySelector(".decrease");

		increaseButton.addEventListener("click", function (e) {
			let input = e.target.previousElementSibling.children[0];
			if (input.dataset.counter !== undefined) {
				let value = parseInt(input.value, 10);
				value = isNaN(value) ? 0 : value;
				value++;
				input.value = value;
			}
		});

		decreaseButton.addEventListener("click", function (e) {
			let input = e.target.nextElementSibling.children[0];
			if (input.dataset.counter !== undefined) {
				let value = parseInt(input.value, 10);
				value = isNaN(value) ? 0 : value;
				value < 1 ? (value = 1) : "";
				value--;
				input.value = value;
			}
		});
	});
}

customAccordion(".accordion__container", ".accordion__items", ".accordion__items--body");
customAccordion(".widget__categories--menu", ".widget__categories--menu__list", ".widget__categories--sub__menu");

let accordion = true;
const footerWidgetAccordion = function () {
	accordion = false;
	let footerWidgetContainer = document.querySelector(".main__footer");
	footerWidgetContainer?.addEventListener("click", function (evt) {
		let singleItemTarget = evt.target;
		if (singleItemTarget.classList.contains("footer__widget--button")) {
			const footerWidget = singleItemTarget.closest(".footer__widget"),
				footerWidgetInner = footerWidget.querySelector(
					".footer__widget--inner"
				);
			if (footerWidget.classList.contains("active")) {
				footerWidget.classList.remove("active");
				slideUp(footerWidgetInner);
			} else {
				footerWidget.classList.add("active");
				slideDown(footerWidgetInner);
				getSiblings(footerWidget).forEach(function (item) {
					const footerWidgetInner = item.querySelector(
						".footer__widget--inner"
					);

					item.classList.remove("active");
					slideUp(footerWidgetInner);
				});
			}
		}
	});
};

window.addEventListener("load", function () {
	if (accordion) {
		footerWidgetAccordion();
	}
});

window.addEventListener("resize", function () {
	document.querySelectorAll(".footer__widget").forEach(function (item) {
		if (window.outerWidth >= 768) {
			item.classList.remove("active");
			item.querySelector(".footer__widget--inner").style.display = "";
		}
	});
	if (accordion) {
		footerWidgetAccordion();
	}
});

const customLightboxHTML = `<div id="glightbox-body" class="glightbox-container">
    <div class="gloader visible"></div>
    <div class="goverlay"></div>
    <div class="gcontainer">
    <div id="glightbox-slider" class="gslider"></div>
    <button class="gnext gbtn" tabindex="0" aria-label="Next" data-customattribute="example">{nextSVG}</button>
    <button class="gprev gbtn" tabindex="1" aria-label="Previous">{prevSVG}</button>
    <button class="gclose gbtn" tabindex="2" aria-label="Close">{closeSVG}</button>
    </div>
    </div>`;

const lightbox = Glightbox({
	touchNavigation: true,
	lightboxHTML: customLightboxHTML,
	loop: true,
});

const wrapper = document.getElementById("funfactId");
if (wrapper) {
	const counters = wrapper.querySelectorAll(".js-counter");
	const duration = 1000;

	let isCounted = false;
	document.addEventListener("scroll", function () {
		const wrapperPos = wrapper.offsetTop - window.innerHeight;
		if (!isCounted && window.scrollY > wrapperPos) {
			counters.forEach((counter) => {
				const countTo = counter.dataset.count;

				const countPerMs = countTo / duration;

				let currentCount = 0;
				const countInterval = setInterval(function () {
					if (currentCount >= countTo) {
						clearInterval(countInterval);
					}
					counter.textContent = Math.round(currentCount).toString();
					currentCount = currentCount + countPerMs;
				}, 1);
			});
			isCounted = true;
		}
	});
}

offCanvasHeader();

categoryMobileMenu();

newsletterPopup();
