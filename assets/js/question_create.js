// call jquery
import $ from 'jquery';

var $collectionHolder;

// setup an "add a proposition" link
var $addPropositionButton = $(
	'<button type="button" class="btn btn-outline-dark add_tag_link">Ajouter une réponse</button>'
);
var $newLinkLi = $('<li></li>').append($addPropositionButton);

$(document).ready(function () {
	// Get the ul that holds the collection of tags
	$collectionHolder = $('ul.propositions');

	// add the "add a tag" anchor and li to the tags ul
	$collectionHolder.append($newLinkLi);

	// count the current form inputs we have (e.g. 2), use that as the new
	// index when inserting a new item (e.g. 2)
	$collectionHolder.data('index', $collectionHolder.find('input').length);

	$addPropositionButton.on('click', (e) => {
		// add a new tag form (see next code block)
		addPropositionForm($collectionHolder, $newLinkLi);
	});

	$('.remove-tag').on('click', (e) => {
		e.preventDefault();

		$(this).parent().remove();

		return false;
	});
});
          
function addPropositionForm($collectionHolder, $newLinkLi) {
	// Get the data-prototype explained earlier
	var prototype = $collectionHolder.data('prototype');

	// get the new index
	var index = $collectionHolder.data('index');

	var newForm = prototype;

	// Replace '__name__' in the prototype's HTML to
	// instead be a number based on how many items we have
	newForm = newForm.replace(/__name__/g, index);

	// increase the index with one for the next item
	$collectionHolder.data('index', index + 1);

	// Display the form in the page in an li, before the "Add a tag" link li
	var $newFormLi = $('<li></li>').append(newForm);

	// also add a remove button
	$newFormLi.append(
		'<button type="button" class="close remove-tag" aria-label="Close">' +
			'<span aria-hidden="true" class="text-danger">&times;</span>' +
			'</button>'
	);

	$newLinkLi.before($newFormLi);

	// handle the removal
	$('.remove-tag').on('click', function (e) {
		e.preventDefault();

		$(this).parent().remove();

		return false;
	});
}
