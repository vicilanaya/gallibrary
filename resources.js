"use strict";
$(document).ready(function () {
  // set spanish to hide by default at page load
  $("[lang=es]").hide();

  // hide spanish, show english when english is clicked
  $("#english").click(function (event) {
    $("[lang=es]").hide();
    $("[lang=en]").show();

    // cancel default link behavior
    event.preventDefault();
  });

  // hide english, show spanish when spanish is clicked
  $("#spanish").click(function (event) {
    $("[lang=en]").hide();
    $("[lang=es]").show();

    // cancel default link behavior
    event.preventDefault();
  });

  // open print dialog box when print is clicked
  $("#print").click(function (event) {
    window.print();

    // cancel default link behavior
    event.preventDefault();
  });

  // keep old links working: section headings used to have their display
  // name as id (e.g. #Education, #Educación, #Social Services). Headings now
  // use slug ids (#education, #education-es) and keep the old name in
  // data-legacy-id, so scroll to it when the hash doesn't match an id.
  function scrollToLegacyAnchor() {
    var hash = window.location.hash.slice(1);
    if (!hash) {
      return;
    }
    try {
      hash = decodeURIComponent(hash);
    } catch (e) {
      return;
    }
    if (document.getElementById(hash)) {
      return; // a current id; the browser already handled it
    }
    $("h2[data-legacy-id]").each(function () {
      if (this.getAttribute("data-legacy-id") === hash) {
        this.scrollIntoView();
        return false; // stop at the first match
      }
    });
  }
  scrollToLegacyAnchor();
  $(window).on("hashchange", scrollToLegacyAnchor);

  // get current year for copyrigt statement in footer
  // function getThisYear() {
  // var date = new Date();
  // var year = date.getFullYear();
  // document.write(year);
  // return year;
  // }
}); // end ready
