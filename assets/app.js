/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

const $ = require('jquery');
require('bootstrap');

function addFormToCollection($collectionHolderClass) {
    var $collectionHolder = $('.' + $collectionHolderClass);
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');

    var newForm = prototype;
    newForm = newForm.replace(/__prot__/g, index);

    $collectionHolder.data('index', index + 1);
    $collectionHolder.append(newForm)
}

$(document).ready(function() {
    // Make elements with data-href clickable (except buttons)
    $('*[data-href]').on('click', function(event) {
        if(!$(event.target).is(":button")) {
             window.location = $(this).data('href');
        }
    });

    var $collectionHolder = $('.collection-holder');
    $collectionHolder.data('index', $collectionHolder.find('.collection-row').length);

    $('body').on('click', '.add-collection-row', function(e) {
        var $collectionHolderClass = $(e.currentTarget).data('collectionHolderClass');
        addFormToCollection($collectionHolderClass);
    })

    $('.collection-holder').on('click', '.remove-collection-row', function(e) {
        e.preventDefault();
        $(this).closest('.collection-row').remove();

        return false;
    });
});
