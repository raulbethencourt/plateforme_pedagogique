(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["app"],{

/***/ "./assets/css/app.scss":
/*!*****************************!*\
  !*** ./assets/css/app.scss ***!
  \*****************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "./assets/js/app.js":
/*!**************************!*\
  !*** ./assets/js/app.js ***!
  \**************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _css_app_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../css/app.scss */ "./assets/css/app.scss");
/* harmony import */ var _css_app_scss__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_css_app_scss__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _question_create__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./question_create */ "./assets/js/question_create.js");
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
// any CSS you import will output into a single css file (app.scss in this case)
 // Need jQuery? Install it with "yarn add jquery", then uncomment to import it.



console.log('victoria');

/***/ }),

/***/ "./assets/js/question_create.js":
/*!**************************************!*\
  !*** ./assets/js/question_create.js ***!
  \**************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_array_find__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.array.find */ "./node_modules/core-js/modules/es.array.find.js");
/* harmony import */ var core_js_modules_es_array_find__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_find__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_regexp_exec__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.regexp.exec */ "./node_modules/core-js/modules/es.regexp.exec.js");
/* harmony import */ var core_js_modules_es_regexp_exec__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_regexp_exec__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_string_replace__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.string.replace */ "./node_modules/core-js/modules/es.string.replace.js");
/* harmony import */ var core_js_modules_es_string_replace__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_replace__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_3__);



// call jquery

var $collectionHolder; // setup an "add a proposition" link

var $addPropositionButton = jquery__WEBPACK_IMPORTED_MODULE_3___default()('<button type="button" class="btn btn-primary add_tag_link">Ajouter un proposition</button>');
var $newLinkLi = jquery__WEBPACK_IMPORTED_MODULE_3___default()('<li></li>').append($addPropositionButton);
jquery__WEBPACK_IMPORTED_MODULE_3___default()(document).ready(function () {
  // Get the ul that holds the collection of tags
  $collectionHolder = jquery__WEBPACK_IMPORTED_MODULE_3___default()('ul.propositions'); // add the "add a tag" anchor and li to the tags ul

  $collectionHolder.append($newLinkLi); // count the current form inputs we have (e.g. 2), use that as the new
  // index when inserting a new item (e.g. 2)

  $collectionHolder.data('index', $collectionHolder.find('input').length);
  $addPropositionButton.on('click', function (e) {
    // add a new tag form (see next code block)
    addPropositionForm($collectionHolder, $newLinkLi);
  });
});

function addPropositionForm($collectionHolder, $newLinkLi) {
  // Get the data-prototype explained earlier
  var prototype = $collectionHolder.data('prototype'); // get the new index

  var index = $collectionHolder.data('index');
  var newForm = prototype; // Replace '__name__' in the prototype's HTML to
  // instead be a number based on how many items we have

  newForm = newForm.replace(/__name__/g, index); // increase the index with one for the next item

  $collectionHolder.data('index', index + 1); // Display the form in the page in an li, before the "Add a tag" link li

  var $newFormLi = jquery__WEBPACK_IMPORTED_MODULE_3___default()('<li></li>').append(newForm); // also add a remove button

  $newFormLi.append('<a href="#" class="btn btn-danger remove-tag ">remove</a>');
  $newLinkLi.before($newFormLi); // handle the removal

  jquery__WEBPACK_IMPORTED_MODULE_3___default()('.remove-tag').click(function (e) {
    e.preventDefault();
    jquery__WEBPACK_IMPORTED_MODULE_3___default()(this).parent().remove();
    return false;
  });
}

/***/ })

},[["./assets/js/app.js","runtime","vendors~app"]]]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvY3NzL2FwcC5zY3NzIiwid2VicGFjazovLy8uL2Fzc2V0cy9qcy9hcHAuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzL3F1ZXN0aW9uX2NyZWF0ZS5qcyJdLCJuYW1lcyI6WyJjb25zb2xlIiwibG9nIiwiJGNvbGxlY3Rpb25Ib2xkZXIiLCIkYWRkUHJvcG9zaXRpb25CdXR0b24iLCIkIiwiJG5ld0xpbmtMaSIsImFwcGVuZCIsImRvY3VtZW50IiwicmVhZHkiLCJkYXRhIiwiZmluZCIsImxlbmd0aCIsIm9uIiwiZSIsImFkZFByb3Bvc2l0aW9uRm9ybSIsInByb3RvdHlwZSIsImluZGV4IiwibmV3Rm9ybSIsInJlcGxhY2UiLCIkbmV3Rm9ybUxpIiwiYmVmb3JlIiwiY2xpY2siLCJwcmV2ZW50RGVmYXVsdCIsInBhcmVudCIsInJlbW92ZSJdLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7O0FBQUEsdUM7Ozs7Ozs7Ozs7OztBQ0FBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBOzs7Ozs7QUFPQTtDQUdBOztBQUNBO0FBRUE7QUFDQUEsT0FBTyxDQUFDQyxHQUFSLENBQVksVUFBWixFOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUNkQTtBQUNBO0FBRUEsSUFBSUMsaUJBQUosQyxDQUVBOztBQUNBLElBQUlDLHFCQUFxQixHQUFHQyw2Q0FBQyxDQUFDLDRGQUFELENBQTdCO0FBQ0EsSUFBSUMsVUFBVSxHQUFHRCw2Q0FBQyxDQUFDLFdBQUQsQ0FBRCxDQUFlRSxNQUFmLENBQXNCSCxxQkFBdEIsQ0FBakI7QUFFQUMsNkNBQUMsQ0FBQ0csUUFBRCxDQUFELENBQVlDLEtBQVosQ0FBa0IsWUFBVztBQUN6QjtBQUNBTixtQkFBaUIsR0FBR0UsNkNBQUMsQ0FBQyxpQkFBRCxDQUFyQixDQUZ5QixDQUl6Qjs7QUFDQUYsbUJBQWlCLENBQUNJLE1BQWxCLENBQXlCRCxVQUF6QixFQUx5QixDQU96QjtBQUNBOztBQUNBSCxtQkFBaUIsQ0FBQ08sSUFBbEIsQ0FBdUIsT0FBdkIsRUFBZ0NQLGlCQUFpQixDQUFDUSxJQUFsQixDQUF1QixPQUF2QixFQUFnQ0MsTUFBaEU7QUFFQVIsdUJBQXFCLENBQUNTLEVBQXRCLENBQXlCLE9BQXpCLEVBQWtDLFVBQVNDLENBQVQsRUFBWTtBQUMxQztBQUNBQyxzQkFBa0IsQ0FBQ1osaUJBQUQsRUFBb0JHLFVBQXBCLENBQWxCO0FBQ0gsR0FIRDtBQUlILENBZkQ7O0FBaUJBLFNBQVNTLGtCQUFULENBQTRCWixpQkFBNUIsRUFBK0NHLFVBQS9DLEVBQTJEO0FBQ3ZEO0FBQ0EsTUFBSVUsU0FBUyxHQUFHYixpQkFBaUIsQ0FBQ08sSUFBbEIsQ0FBdUIsV0FBdkIsQ0FBaEIsQ0FGdUQsQ0FJdkQ7O0FBQ0EsTUFBSU8sS0FBSyxHQUFHZCxpQkFBaUIsQ0FBQ08sSUFBbEIsQ0FBdUIsT0FBdkIsQ0FBWjtBQUVBLE1BQUlRLE9BQU8sR0FBR0YsU0FBZCxDQVB1RCxDQVN2RDtBQUNBOztBQUNBRSxTQUFPLEdBQUdBLE9BQU8sQ0FBQ0MsT0FBUixDQUFnQixXQUFoQixFQUE2QkYsS0FBN0IsQ0FBVixDQVh1RCxDQWF2RDs7QUFDQWQsbUJBQWlCLENBQUNPLElBQWxCLENBQXVCLE9BQXZCLEVBQWdDTyxLQUFLLEdBQUcsQ0FBeEMsRUFkdUQsQ0FnQnZEOztBQUNBLE1BQUlHLFVBQVUsR0FBR2YsNkNBQUMsQ0FBQyxXQUFELENBQUQsQ0FBZUUsTUFBZixDQUFzQlcsT0FBdEIsQ0FBakIsQ0FqQnVELENBbUJ2RDs7QUFDQUUsWUFBVSxDQUFDYixNQUFYLENBQWtCLDJEQUFsQjtBQUVBRCxZQUFVLENBQUNlLE1BQVgsQ0FBa0JELFVBQWxCLEVBdEJ1RCxDQXdCdkQ7O0FBQ0FmLCtDQUFDLENBQUMsYUFBRCxDQUFELENBQWlCaUIsS0FBakIsQ0FBdUIsVUFBU1IsQ0FBVCxFQUFZO0FBQy9CQSxLQUFDLENBQUNTLGNBQUY7QUFFQWxCLGlEQUFDLENBQUMsSUFBRCxDQUFELENBQVFtQixNQUFSLEdBQWlCQyxNQUFqQjtBQUVBLFdBQU8sS0FBUDtBQUNILEdBTkQ7QUFPSCxDIiwiZmlsZSI6ImFwcC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8vIGV4dHJhY3RlZCBieSBtaW5pLWNzcy1leHRyYWN0LXBsdWdpbiIsIi8qXG4gKiBXZWxjb21lIHRvIHlvdXIgYXBwJ3MgbWFpbiBKYXZhU2NyaXB0IGZpbGUhXG4gKlxuICogV2UgcmVjb21tZW5kIGluY2x1ZGluZyB0aGUgYnVpbHQgdmVyc2lvbiBvZiB0aGlzIEphdmFTY3JpcHQgZmlsZVxuICogKGFuZCBpdHMgQ1NTIGZpbGUpIGluIHlvdXIgYmFzZSBsYXlvdXQgKGJhc2UuaHRtbC50d2lnKS5cbiAqL1xuXG4vLyBhbnkgQ1NTIHlvdSBpbXBvcnQgd2lsbCBvdXRwdXQgaW50byBhIHNpbmdsZSBjc3MgZmlsZSAoYXBwLnNjc3MgaW4gdGhpcyBjYXNlKVxuaW1wb3J0ICcuLi9jc3MvYXBwLnNjc3MnO1xuXG4vLyBOZWVkIGpRdWVyeT8gSW5zdGFsbCBpdCB3aXRoIFwieWFybiBhZGQganF1ZXJ5XCIsIHRoZW4gdW5jb21tZW50IHRvIGltcG9ydCBpdC5cbmltcG9ydCAkIGZyb20gXCJqcXVlcnlcIjtcblxuaW1wb3J0ICcuL3F1ZXN0aW9uX2NyZWF0ZSc7XG5jb25zb2xlLmxvZygndmljdG9yaWEnKTsiLCIvLyBjYWxsIGpxdWVyeVxuaW1wb3J0ICQgZnJvbSBcImpxdWVyeVwiO1xuXG52YXIgJGNvbGxlY3Rpb25Ib2xkZXI7XG5cbi8vIHNldHVwIGFuIFwiYWRkIGEgcHJvcG9zaXRpb25cIiBsaW5rXG52YXIgJGFkZFByb3Bvc2l0aW9uQnV0dG9uID0gJCgnPGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJidG4gYnRuLXByaW1hcnkgYWRkX3RhZ19saW5rXCI+QWpvdXRlciB1biBwcm9wb3NpdGlvbjwvYnV0dG9uPicpO1xudmFyICRuZXdMaW5rTGkgPSAkKCc8bGk+PC9saT4nKS5hcHBlbmQoJGFkZFByb3Bvc2l0aW9uQnV0dG9uKTtcblxuJChkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24oKSB7XG4gICAgLy8gR2V0IHRoZSB1bCB0aGF0IGhvbGRzIHRoZSBjb2xsZWN0aW9uIG9mIHRhZ3NcbiAgICAkY29sbGVjdGlvbkhvbGRlciA9ICQoJ3VsLnByb3Bvc2l0aW9ucycpO1xuXG4gICAgLy8gYWRkIHRoZSBcImFkZCBhIHRhZ1wiIGFuY2hvciBhbmQgbGkgdG8gdGhlIHRhZ3MgdWxcbiAgICAkY29sbGVjdGlvbkhvbGRlci5hcHBlbmQoJG5ld0xpbmtMaSk7XG5cbiAgICAvLyBjb3VudCB0aGUgY3VycmVudCBmb3JtIGlucHV0cyB3ZSBoYXZlIChlLmcuIDIpLCB1c2UgdGhhdCBhcyB0aGUgbmV3XG4gICAgLy8gaW5kZXggd2hlbiBpbnNlcnRpbmcgYSBuZXcgaXRlbSAoZS5nLiAyKVxuICAgICRjb2xsZWN0aW9uSG9sZGVyLmRhdGEoJ2luZGV4JywgJGNvbGxlY3Rpb25Ib2xkZXIuZmluZCgnaW5wdXQnKS5sZW5ndGgpO1xuXG4gICAgJGFkZFByb3Bvc2l0aW9uQnV0dG9uLm9uKCdjbGljaycsIGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgLy8gYWRkIGEgbmV3IHRhZyBmb3JtIChzZWUgbmV4dCBjb2RlIGJsb2NrKVxuICAgICAgICBhZGRQcm9wb3NpdGlvbkZvcm0oJGNvbGxlY3Rpb25Ib2xkZXIsICRuZXdMaW5rTGkpO1xuICAgIH0pO1xufSk7XG5cbmZ1bmN0aW9uIGFkZFByb3Bvc2l0aW9uRm9ybSgkY29sbGVjdGlvbkhvbGRlciwgJG5ld0xpbmtMaSkge1xuICAgIC8vIEdldCB0aGUgZGF0YS1wcm90b3R5cGUgZXhwbGFpbmVkIGVhcmxpZXJcbiAgICB2YXIgcHJvdG90eXBlID0gJGNvbGxlY3Rpb25Ib2xkZXIuZGF0YSgncHJvdG90eXBlJyk7XG5cbiAgICAvLyBnZXQgdGhlIG5ldyBpbmRleFxuICAgIHZhciBpbmRleCA9ICRjb2xsZWN0aW9uSG9sZGVyLmRhdGEoJ2luZGV4Jyk7XG5cbiAgICB2YXIgbmV3Rm9ybSA9IHByb3RvdHlwZTtcblxuICAgIC8vIFJlcGxhY2UgJ19fbmFtZV9fJyBpbiB0aGUgcHJvdG90eXBlJ3MgSFRNTCB0b1xuICAgIC8vIGluc3RlYWQgYmUgYSBudW1iZXIgYmFzZWQgb24gaG93IG1hbnkgaXRlbXMgd2UgaGF2ZVxuICAgIG5ld0Zvcm0gPSBuZXdGb3JtLnJlcGxhY2UoL19fbmFtZV9fL2csIGluZGV4KTtcblxuICAgIC8vIGluY3JlYXNlIHRoZSBpbmRleCB3aXRoIG9uZSBmb3IgdGhlIG5leHQgaXRlbVxuICAgICRjb2xsZWN0aW9uSG9sZGVyLmRhdGEoJ2luZGV4JywgaW5kZXggKyAxKTtcblxuICAgIC8vIERpc3BsYXkgdGhlIGZvcm0gaW4gdGhlIHBhZ2UgaW4gYW4gbGksIGJlZm9yZSB0aGUgXCJBZGQgYSB0YWdcIiBsaW5rIGxpXG4gICAgdmFyICRuZXdGb3JtTGkgPSAkKCc8bGk+PC9saT4nKS5hcHBlbmQobmV3Rm9ybSk7XG5cbiAgICAvLyBhbHNvIGFkZCBhIHJlbW92ZSBidXR0b25cbiAgICAkbmV3Rm9ybUxpLmFwcGVuZCgnPGEgaHJlZj1cIiNcIiBjbGFzcz1cImJ0biBidG4tZGFuZ2VyIHJlbW92ZS10YWcgXCI+cmVtb3ZlPC9hPicpO1xuXG4gICAgJG5ld0xpbmtMaS5iZWZvcmUoJG5ld0Zvcm1MaSk7XG5cbiAgICAvLyBoYW5kbGUgdGhlIHJlbW92YWxcbiAgICAkKCcucmVtb3ZlLXRhZycpLmNsaWNrKGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgICQodGhpcykucGFyZW50KCkucmVtb3ZlKCk7XG5cbiAgICAgICAgcmV0dXJuIGZhbHNlO1xuICAgIH0pO1xufVxuIl0sInNvdXJjZVJvb3QiOiIifQ==