/*
 * GAL Resource Library - page behavior (no libraries needed).
 *
 * Language: elements marked lang="en" / lang="es" are shown or hidden by CSS
 * depending on the "show-es" class on <html>. The choice is remembered in
 * localStorage and reflected in the address bar as ?lang=es so the link can
 * be shared. This file is loaded in <head> so the saved language is applied
 * before the page is drawn (no flash of the wrong language).
 */
(function () {
  "use strict";

  var LANGS = ["en", "es"];
  var STORAGE_KEY = "gal-lang";
  var root = document.documentElement;

  function isLang(value) {
    return LANGS.indexOf(value) !== -1;
  }

  function urlLang() {
    var value = new URLSearchParams(window.location.search).get("lang");
    return isLang(value) ? value : null;
  }

  function storedLang() {
    try {
      var value = window.localStorage.getItem(STORAGE_KEY);
      return isLang(value) ? value : null;
    } catch (e) {
      return null; // storage blocked (private mode, settings)
    }
  }

  function currentLang() {
    return root.classList.contains("show-es") ? "es" : "en";
  }

  // Show one language: update <html lang>, the CSS class and the buttons.
  function applyLang(lang) {
    root.lang = lang;
    root.classList.toggle("show-es", lang === "es");
    var buttons = document.querySelectorAll(".lang-switch");
    for (var i = 0; i < buttons.length; i++) {
      buttons[i].setAttribute(
        "aria-pressed",
        buttons[i].getAttribute("data-lang") === lang ? "true" : "false",
      );
    }
  }

  // A visitor's explicit choice: apply, remember, and put it in the address bar.
  function chooseLang(lang) {
    applyLang(lang);
    try {
      window.localStorage.setItem(STORAGE_KEY, lang);
    } catch (e) {
      // not saved; the page still switches
    }
    var url = new URL(window.location.href);
    url.searchParams.set("lang", lang);
    window.history.replaceState(null, "", url.toString());
  }

  // A link in the address bar wins, then the saved choice, then the server's default.
  applyLang(
    urlLang() || storedLang() || (isLang(root.lang) ? root.lang : "en"),
  );

  // Find the element a #hash points to. Section headings used to have their
  // display name as id (#Education, #Educación, #Social Services); they now
  // use slug ids (#education, #education-es) and keep the old name in
  // data-legacy-id, so old links still work.
  function hashTarget() {
    var hash = window.location.hash.slice(1);
    if (!hash) {
      return null;
    }
    try {
      hash = decodeURIComponent(hash);
    } catch (e) {
      return null;
    }
    var el = document.getElementById(hash);
    if (el) {
      return el;
    }
    var headings = document.querySelectorAll("h2[data-legacy-id]");
    for (var i = 0; i < headings.length; i++) {
      if (headings[i].getAttribute("data-legacy-id") === hash) {
        return headings[i];
      }
    }
    return null;
  }

  // If the link points into the other language (e.g. #education-es while
  // English is showing), switch language first, then scroll to it.
  function followHash() {
    var target = hashTarget();
    if (!target) {
      return;
    }
    var owner = target.closest("[lang]");
    if (
      owner &&
      owner !== root &&
      isLang(owner.lang) &&
      owner.lang !== currentLang()
    ) {
      applyLang(owner.lang);
    }
    target.scrollIntoView();
  }

  document.addEventListener("DOMContentLoaded", function () {
    applyLang(currentLang()); // sync the buttons now that they exist

    var buttons = document.querySelectorAll(".lang-switch");
    for (var i = 0; i < buttons.length; i++) {
      buttons[i].addEventListener("click", function () {
        chooseLang(this.getAttribute("data-lang"));
      });
    }

    var print = document.getElementById("print");
    if (print) {
      print.addEventListener("click", function () {
        window.print();
      });
    }

    followHash();
    window.addEventListener("hashchange", followHash);
  });
})();
